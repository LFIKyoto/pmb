<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/facette_search_opac.class.php");

switch($section){
	case "lst_facette":
		$facette = new facette_search();
		print $facette->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
		
	break;
}
	