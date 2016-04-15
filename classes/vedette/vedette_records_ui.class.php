<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records_ui.class.php,v 1.2 2014-10-07 10:34:18 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_records.tpl.php");

class vedette_records_ui extends vedette_element_ui{
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array()){
		global $vedette_records_tpl;
		return $vedette_records_tpl["vedette_records_selector"];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array()){
		global $vedette_records_tpl;
		return $vedette_records_tpl["vedette_records_script"];
	}
	
	/**
	 * Renvoie les données (id objet, type)
	 *
	 * @return void
	 * @access public
	 */
	public static function get_from_form($params = array()){
	
	}
}
