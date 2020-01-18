<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_concepts.class.php,v 1.3.6.1 2019-09-26 13:47:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_concepts extends frbr_entity_common_datasource_concept {
	
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		if(!empty($this->get_parameters()->sub_datasource_choice)) {
		    $class_name = $this->get_parameters()->sub_datasource_choice;
		    $sub_datasource = new $class_name();
		    $sub_datasource->set_parameters($this->parameters);
		    if(isset($this->external_filter) && $this->external_filter) {
		        $sub_datasource->set_filter($this->external_filter);
		    }
		    if(isset($this->external_sort) && $this->external_sort) {
		        $sub_datasource->set_sort($this->external_sort);
		    }
		    return $sub_datasource->get_datas($datas);
		} 
		$query = "select distinct index_concept.num_concept as id, index_concept.num_object as parent FROM index_concept
			WHERE index_concept.type_object = 12 AND index_concept.num_object IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function get_sub_datasources() {
	    return array(
	        "frbr_entity_authperso_datasource_concepts_indexing",
	        "frbr_entity_authperso_datasource_concepts_used_in_custom_fields",
	    );
	}
}