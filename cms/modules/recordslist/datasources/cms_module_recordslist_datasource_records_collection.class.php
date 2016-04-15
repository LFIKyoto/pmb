<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_collection.class.php,v 1.1 2015-04-10 10:29:04 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_collection extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_collection"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			if($value['collection'] != 0){
				$query = "select notice_id from notices where coll_id = ".$value['collection'].' and notice_id != '.$value['record'];
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result) > 0){
					$return["title"] = "Dans la même collection";
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
				if($this->parameters['nb_max_elements'] > 0){
					$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
				}
			}
			return $return;
		}
		return false;
	}
}