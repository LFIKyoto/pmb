<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_item.class.php,v 1.1 2014-10-08 14:13:19 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/skos/onto_skos_concept_item.tpl.php');

class onto_skos_concept_item extends onto_common_item {

	public function get_form($prefix_url="",$flag="",$action="save") {
		$form = parent::get_form($prefix_url,$flag,$action);
		if($flag != "concept_selector_form"){
			$aut_link= new aut_link(AUT_TABLE_CONCEPT,onto_common_uri::get_id($this->get_uri()));
			$form = str_replace('<!-- aut_link -->', $aut_link->get_form(onto_common_uri::get_name_from_uri($this->get_uri(), $this->onto_class->pmb_name)) , $form);
		}else {
			$form = str_replace('<!-- aut_link -->', "" , $form);
		}
		return $form;
	}
}