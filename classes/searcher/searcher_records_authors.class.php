<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_authors.class.php,v 1.1 2014-08-12 13:18:36 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_authors extends searcher_records {
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(27,28,29),
				'op' => "and",
				'not' => false
		); ;
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_authors";
	}
}