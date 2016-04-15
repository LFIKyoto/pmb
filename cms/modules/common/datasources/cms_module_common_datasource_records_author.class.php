<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_author.class.php,v 1.5 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_author extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_principal_author"
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
			if($value['author'] != 0){
				$query = "select distinct responsability_notice from responsability where responsability_author = ".$value['author'].' and responsability_notice != '.$value['record'];
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result) > 0){
					$return["title"] = "Du même auteur";
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->responsability_notice;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
				$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
			}
			return $return;
		}
		return false;
	}
}