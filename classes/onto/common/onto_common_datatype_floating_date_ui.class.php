<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_floating_date_ui.class.php,v 1.1 2019-08-20 15:11:35 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_floating_date_ui
 * 
 */
class onto_common_datatype_floating_date_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propri�t� concern�e
	 * @param restriction $restrictions le tableau des restrictions associ�es � la propri�t� 
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
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		
		$content=$ontology_tpl['form_row_content_floating_date_script'];
		if (!empty($datas)) {
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$inside_row=$ontology_tpl['form_row_content_floating_date'];
				$formated_value = $data->get_formated_value();
				$inside_row=str_replace("!!floating_date_value!!", $formated_value[0], $inside_row);
				$inside_row=str_replace("!!floating_date_begin!!", '', $inside_row);
				$inside_row=str_replace("!!floating_date_end!!", '', $inside_row);
				$inside_row=str_replace("!!floating_date_comment!!", '', $inside_row);
// 				$inside_row=str_replace("!!onto_row_content_floating_date_value!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$inside_row);
// 				$inside_row=str_replace("!!onto_row_combobox_lang!!",self::get_combobox_lang($instance_name.'_'.$property->pmb_name.'['.$order.'][lang]',$instance_name.'_'.$property->pmb_name.'_'.$order.'_lang',$data->get_lang()) ,$inside_row);
// 				$inside_row=str_replace("!!onto_row_content_floating_date_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$row=str_replace("!!onto_row_inputs!!",'' , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
			
			$row=$ontology_tpl['form_row_content'];
			
			$inside_row=$ontology_tpl['form_row_content_floating_date'];
			
			$inside_row=str_replace("!!floating_date_value!!", '', $inside_row);
			$inside_row=str_replace("!!floating_date_begin!!", '', $inside_row);
			$inside_row=str_replace("!!floating_date_end!!", '', $inside_row);
			$inside_row=str_replace("!!floating_date_comment!!", '', $inside_row);
			
// 			$inside_row=str_replace("!!onto_row_content_floating_date_value!!","" , $inside_row);
// 			$inside_row=str_replace("!!onto_row_combobox_lang!!",self::get_combobox_lang($instance_name.'_'.$property->pmb_name.'[0][lang]',$instance_name.'_'.$property->pmb_name.'_0_lang') , $inside_row);
// 			$inside_row=str_replace("!!onto_row_content_floating_date_range!!",$property->range[0] , $inside_row);
			
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
		
			$row=str_replace("!!onto_row_inputs!!",'' , $row);
			
			$row=str_replace("!!onto_row_order!!","0" , $row);
			
			$content.=$row;
		}
		
// 		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	} // end of member function get_form

	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs � afficher associ�es � la propri�t�
	 * @param property property la propri�t� � utiliser
	 * @param string instance_name nom de l'instance
	 * 
	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->get_label().' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
		
	} // end of member function get_display

} // end of onto_common_datatype_floating_date_ui