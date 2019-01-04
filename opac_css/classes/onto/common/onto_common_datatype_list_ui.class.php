<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_list_ui.class.php,v 1.12 2018-12-21 13:12:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_list_ui
 * 
 */
class onto_common_datatype_list_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl;
		
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->label, 'utf-8') ,ENT_QUOTES,$charset), $form);	
		
		$options_values = array();
		if (isset($property->pmb_list_item)) {
			foreach ($property->pmb_list_item as $list_item) {
				$options_values[$list_item['id']] = $list_item['value'];
			}
		}
		if (isset($property->pmb_list_query)) {
			$result = pmb_mysql_query($property->pmb_list_query);
			while ($row = pmb_mysql_fetch_array($result)) {				
				$options_values[$row[0]] = $row[1];
			}
		}
		
		$content='';
		
		$list_values_to_display = static::get_list_values_to_display($property);
		
		if(sizeof($datas)){			
			$form=str_replace("!!onto_new_order!!",0 , $form);
		
			$row=$ontology_tpl['form_row_content'];
			$inside_row = ($restrictions->get_max() != 1 ? $ontology_tpl['form_row_content_list_multi'] : $ontology_tpl['form_row_content_list']);
			$inside_row.= $ontology_tpl['form_row_content_type'];
							
			$options = '';
			$values = array();
			foreach($datas as $key=>$data){
				$formated_value = $data->get_formated_value();
				if (is_array($formated_value)) {
					$values = array_merge($values, $formated_value);
				} else {
					$values[] = $formated_value;
				}
			}
			if ($restrictions->get_max() != 1) {
				$inside_row = str_replace("!!onto_value!!", '['.implode(',', $values).']', $inside_row);
			}
			foreach($options_values as $id => $value){
				if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
					continue;
				}
				$options.= '<option value="'.htmlentities($id, ENT_QUOTES, $charset).'" '.(in_array($id, $values) ? 'selected="selected"' : '').'>'.htmlentities($value, ENT_QUOTES, $charset).'</option>';
			}
			/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
			$inside_row=str_replace("!!onto_row_content_list_options!!", $options, $inside_row);
			$inside_row=str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
	
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
	
			$input='';
	
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
			$row=str_replace("!!onto_row_order!!",0 , $row);
	
			$content.=$row;
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
				
			$row=$ontology_tpl['form_row_content'];
				
			$inside_row = ($restrictions->get_max() != 1 ? $ontology_tpl['form_row_content_list_multi'] : $ontology_tpl['form_row_content_list']);
			$inside_row.= $ontology_tpl['form_row_content_type'];
			
			$options = '';
			foreach($options_values as $id => $value){
				if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
					continue;
				}
				$options.= '<option value="'.htmlentities($id, ENT_QUOTES, $charset).'">'.htmlentities($value, ENT_QUOTES, $charset).'</option>';
			}			
			
			$inside_row=str_replace("!!onto_row_content_list_options!!", $options, $inside_row);
			$inside_row=str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$input='';
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
				
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form =  self::get_form_with_special_properties($property, $datas, $instance_name, $form);		
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
// 		printr($form);
		return $form;
		
	} // end of member function get_form

	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
	 * @param property property la propriété à utiliser
	 * @param string instance_name nom de l'instance
	 * 
	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
		
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->label.' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
		
	} // end of member function get_display
	
	public static function get_hidden_fields($property,$datas, $instance_name, $flag = false) {
		global $msg,$charset,$ontology_tpl;
		
		$form=$ontology_tpl['form_row_hidden'];
		
		$content='';
		
		if(sizeof($datas)){
			$form=str_replace("!!onto_new_order!!", '0', $form);
						
			$formated_values = array();
			foreach($datas as $key=>$data){
				$formated_value = $data->get_formated_value();
				if (is_array($formated_value)) {
					$formated_values = array_merge($formated_values, $formated_value);
				} else {
					$formated_values[] = $formated_value;
				}
			}
			$row = $ontology_tpl['form_row_content_list_hidden'];
			
			$form_row_content_list_item_hidden = '';
			if (is_array($formated_values)) {
				$row = str_replace("!!form_row_content_list_hidden_values!!", implode(',', $formated_values), $row);
				foreach ($formated_values as $formated_value) {
					$form_row_content_list_item_hidden.= str_replace("!!onto_row_content_hidden_value!!",htmlentities($formated_value, ENT_QUOTES, $charset), $ontology_tpl['form_row_content_list_item_hidden']);
				}
			} else {
				$row = str_replace("!!form_row_content_list_hidden_values!!", $formated_values, $row);
				$form_row_content_list_item_hidden = str_replace("!!onto_row_content_hidden_value!!",htmlentities($formated_values, ENT_QUOTES, $charset) ,$ontology_tpl['form_row_content_list_item_hidden']);
			}
			$row = str_replace("!!form_row_content_list_item_hidden!!", $form_row_content_list_item_hidden, $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row = str_replace("!!onto_row_order!!", '0', $row);
	
			$content.= $row;
		} else {	
				
			$form=str_replace("!!onto_new_order!!","0" , $form);
					
			$row = $ontology_tpl['form_row_content_hidden'];
			$row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		if ($flag) {
			$form=$content;
		} else {
			$form=str_replace("!!onto_rows!!",$content ,$form);
		}
				
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	}

	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return array();
	}

} // end of onto_common_datatype_ui