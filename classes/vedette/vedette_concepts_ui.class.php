<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_concepts_ui.class.php,v 1.3 2014-10-07 10:34:18 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/vedette/vedette_concepts.tpl.php");

class vedette_concepts_ui extends vedette_element_ui{
	
	/**
	 * Boite de sélection de l'élément
	 *
	 * @return string
	 * @access public
	 */
	public static function get_form($params = array()){
		global $vedette_concepts_tpl;
		return $vedette_concepts_tpl["vedette_concepts_selector"];
	}
	
	
	/**
	 * Renvoie le code javascript pour la création du sélécteur
	 *
	 * @return string
	 */
	public static function get_create_box_js($params = array()){
		global $vedette_concepts_tpl;
		return $vedette_concepts_tpl["vedette_concepts_script"];
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
