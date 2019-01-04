<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_datasource_works.class.php,v 1.2 2017-10-02 14:52:22 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authors_datasource_works extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
	}
		
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT DISTINCT responsability_tu_num AS id, responsability_tu_author_num AS parent FROM responsability_tu 
				WHERE responsability_tu_author_num IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}