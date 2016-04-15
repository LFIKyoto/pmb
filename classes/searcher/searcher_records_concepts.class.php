<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_concepts.class.php,v 1.1 2014-07-30 15:05:37 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_concepts extends searcher_records {
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => array(36),
				'op' => "and",
				'not' => false
		); ;
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_concept";
	}
}