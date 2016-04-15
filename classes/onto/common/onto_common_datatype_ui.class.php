<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.class.php,v 1.17 2014-08-08 10:11:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/onto_root_ui.class.php');
require_once($class_path.'/onto/common/onto_common_datatype.class.php');
require_once($include_path.'/templates/onto/common/onto_common_datatype_ui.tpl.php');


/**
 * class onto_common_datatype_ui
 * 
 */
abstract class onto_common_datatype_ui extends onto_root_ui{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_uri URI de l'instance
	 * @param string flag Flag
	 * 
	 * @return string
	 * @static
	 * @access public
	 */
	abstract public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag);

	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
	 * @param property property la propriété à utiliser
	 * @param string instance_uri URI de l'instance (item)
	 * 
	 * @return string
	 * @access public
	 */
	abstract public function get_display($datas, $property, $instance_name);
	
	
	/**
	 * Retourne un object JSON avec 2 méthodes check et get_error_message
	 *
	 * @param property property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_uri URI de l'instance
	 * @param string flag Flag
	 *
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_validation_js($item_uri,$property, $restrictions,$datas, $instance_name,$flag){
		return '{
			"check": function(){
				return true;
			},
			"get_error_message": function(){
				return "";	
			} 	
		}';	
	}
	
	public static function get_combobox_lang($name,$id,$current_lang='',$size=1,$onchange='', $tab_lang = array()) {
		global $charset, $msg;
		
		if (!count($tab_lang)) {
			$tab_lang=array(0=>$msg["onto_common_datatype_ui_no_lang"],'fr'=>$msg["onto_common_datatype_ui_fr"],'en'=>$msg["onto_common_datatype_ui_en"]);
		}
	
		$combobox='';
		$combobox.='<select onchange="'.$onchange.'" name="'.$name.'" id="'.$id.'" size="'.$size.'">';
		foreach($tab_lang as $key=>$lang){
			if($key==$current_lang){
				$combobox.='<option selected value="'.$key.'">'.htmlentities($lang, ENT_QUOTES, $charset).'</option>';
			}else{
				$combobox.='<option value="'.$key.'">'.htmlentities($lang,ENT_QUOTES,$charset).'</option>';
			}
		}
	
		$combobox.='</select>';
	
		return $combobox;
	}
	
} // end of onto_common_datatype_ui