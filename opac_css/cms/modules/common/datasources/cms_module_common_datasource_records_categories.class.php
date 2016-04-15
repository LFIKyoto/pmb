<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_categories.class.php,v 1.9 2015-04-09 16:19:51 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_categories extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_article",
			"cms_module_common_selector_env_var"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$selector = $this->get_selected_selector();
		if ($selector) {
			
			$query = "select distinct notice_id 
				from notices join notices_categories on notice_id=notcateg_notice 
				join cms_articles_descriptors on cms_articles_descriptors.num_noeud=notices_categories.num_noeud 
				and num_article=".$selector->get_value();
			
			$result = pmb_mysql_query($query,$dbh);
			$return = array();
			if($result && (pmb_mysql_num_rows($result) > 0)){
				$return["title"] = "Liste de notices";
				while($row = pmb_mysql_fetch_object($result)){
					$return["records"][] = $row->notice_id;
				}
			}
			$return['records'] = $this->filter_datas("notices",$return['records']);
			if($this->parameters['nb_max_elements'] > 0){
				$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
			}
			if(!count($return['records'])) return false;
			// on tri par pertinence
			$query = "SELECT notice_id
			FROM notices
			JOIN notices_categories ON notice_id = notcateg_notice
			JOIN cms_articles_descriptors ON cms_articles_descriptors.num_noeud = notices_categories.num_noeud
			AND num_article =".$selector->get_value()." where notice_id in(".implode(',', $return['records']).")group by notice_id order by count(*) desc, create_date desc";
		
			$result = pmb_mysql_query($query,$dbh);
			$return = array();
			if($result && (pmb_mysql_num_rows($result) > 0)){
				$return["title"] = "Liste de notices";
				while($row = pmb_mysql_fetch_object($result)){
					$return["records"][] = $row->notice_id;
				}
			}
			return $return;
		}
		return false;
	}
}