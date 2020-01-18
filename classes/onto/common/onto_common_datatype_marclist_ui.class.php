<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_marclist_ui.class.php,v 1.7 2019-08-14 08:02:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once($class_path.'/marc_table.class.php');


/**
 * 
 * Add use march & generate the selector
 */

/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_common_datatype_marclist_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param Array() class_uris URI des classes de l'ontologie list�es dans le s�lecteur

	 * @return void
	 * @access public
	 */
	public function __construct( $class_uris ) {
	} // end of member function __construct

	/**
	 * 
	 *
	 * @param string class_uri URI de la classe d'instances � lister

	 * @param integer page Num�ro de page � afficher

	 * @return Array()
	 * @access public
	 */
	public function get_list( $class_uri,  $page ) {
	} // end of member function get_list

	/**
	 * Recherche
	 *
	 * @param string user_query Chaine de recherche dans les labels

	 * @param string class_uri Rechercher iniquement les instances de la classe

	 * @param integer page Page du r�sultat de recherche � afficher

	 * @return Array()
	 * @access public
	 */
	public function search( $user_query,  $class_uri,  $page ) {
	} // end of member function search


	/**
	 * 
	 *
	 * @param onto_common_property $property la propri�t� concern�e
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
		
		$marc_list = new marc_list($property->pmb_marclist_type);
		$content='';
		$list_values_to_display = static::get_list_values_to_display($property);
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
				$inside_row=$ontology_tpl['form_row_content_marclist'];
				$options = '';
				foreach($marc_list->table as $value => $label){
					$display_none = '';
					if (count($list_values_to_display) && !in_array($value, $list_values_to_display)) {
						$display_none = 'style="display:none;"';
					}
					$options.= '<option value="'.$value.'" '.($data->get_formated_value() == $value ? 'selected=selected' : '').' '.$display_none.'>'.htmlentities($label,ENT_QUOTES,$charset).'</option>';
				}
				/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
				$inside_row=str_replace('!!onto_row_content_marclist_options!!', $options, $inside_row);
				$inside_row=str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
		
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
		
				$input='';
				if($first){
					if($restrictions->get_max()<$i || $restrictions->get_max()===-1){
						$input=$ontology_tpl['form_row_content_input_add'];
					}
				}else{
					$input=$ontology_tpl['form_row_content_input_del'];
				}
		
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
		
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
				
			$row=$ontology_tpl['form_row_content'];
				
			$inside_row=$ontology_tpl['form_row_content_marclist'];

			$options = '';
			foreach($marc_list->table as $value => $label){
				$display_none = '';
				if (count($list_values_to_display) && !in_array($value, $list_values_to_display)) {
					$display_none = 'style="display:none;"';
				}
				$options.= '<option value="'.$value.'" '.$display_none.' >'.$label.'</option>';
			}
			
			$inside_row=str_replace("!!onto_row_content_marclist_options!!", $options, $inside_row);
			$inside_row=str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$input='';
			if($restrictions->get_max()!=1){
				$input=$ontology_tpl['form_row_content_input_add'];
			}
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;		
		
	} // end of member function get_form
	
	/**
	 
	 * @param onto_common_datatype datas Tableau des valeurs � afficher associ�es � la propri�t�

	 * @param property property la propri�t� � utiliser

	 * @param string instance_name nom de l'instance

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
		
	}
	
	/**
	 * A d�river pour filtrer la liste des valeurs � afficher dans le s�lecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return array();
	}

} // end of onto_common_datatype_resource_selector_ui
