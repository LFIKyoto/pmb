<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_sections_categories.class.php,v 1.4 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_sections_categories extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_env_var",
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"publication_date",
			"id_section",
			"section_title",
			"section_order"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector) {
			$query = "select distinct id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date from cms_sections join cms_sections_descriptors on id_section=num_section join notices_categories on cms_sections_descriptors.num_noeud=notices_categories.num_noeud and notcateg_notice = ".$selector->get_value();
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
			}
			$result = pmb_mysql_query($query);
			$return = array();
			if(pmb_mysql_num_rows($result) > 0){
				while($row = pmb_mysql_fetch_object($result)){
					$return[] = $row->id_section;
				}
			}
			$return = $this->filter_datas("sections",$return);
			if ($this->parameters["nb_max_elements"] > 0) $return = array_slice($return, 0, $this->parameters["nb_max_elements"]);
			return $return;
		}
		return false;
	}
}