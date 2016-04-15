<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_section.class.php,v 1.9.2.1 2015-10-28 16:25:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_section extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_section",
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_global_var",
			"cms_module_common_selector_generic_parent_section",
		);
	}
	
	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;
		
		$this->parameters= array();
		$this->parameters['selector'] = $selector_choice;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$section_id = $selector->get_value();
			$section_ids = $this->filter_datas("sections",array($section_id));
			if($section_ids[0]){
				$section = cms_provider::get_instance("section",$section_ids[0]);
				$return = $section->format_datas(true, true, true, true);
				return $return;
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return cms_section::get_format_data_structure(true, true, true, true);
	}
}