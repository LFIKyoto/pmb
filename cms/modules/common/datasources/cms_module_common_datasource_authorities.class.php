<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_authorities.class.php,v 1.1 2016-04-20 13:54:55 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/authority.class.php');

class cms_module_common_datasource_authorities extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	 */
	public function get_available_selectors(){
		return array(
				"cms_module_common_selector_generic_authorities"
		);
	}
	
	/*
	 * On d�fini les crit�res de tri utilisable pour cette source de donn�e
	 */
	protected function get_sort_criterias() {
		return array (
			"id_authority"
		);
	}
	
	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas(){
		//on commence par r�cup�rer l'identifiant retourn� par le s�lecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$authorities_ids = array();
			if (count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$authorities_ids[] = $value;
				}
			}
			$authorities_ids = $this->filter_datas("authorities", $authorities_ids);
			$authorities = array();
			foreach ($authorities_ids as $authority_id) {
				$authorities[] = new authority($authority_id);
			}
			return $authorities;
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return cms_authority::get_format_data_structure();
	}
}