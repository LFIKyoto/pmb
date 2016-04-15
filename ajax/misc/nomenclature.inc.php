<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature.inc.php,v 1.4 2015-02-10 17:45:39 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


switch($sub){
	case 'record_child':		
		require_once($class_path."/nomenclature/nomenclature_record_child_ui.class.php");
		$record_child = new nomenclature_record_child_ui($id);
		switch($action){
			case "create" :
				print encoding_normalize::json_encode($record_child->create_record_child($id_parent));
				break;
			case "get_child" :
				print $record_child->get_child($id_parent);
				break;
			case "get_possible_values" :
				print $record_child->get_possible_values($id_parent);
				break;
		}
	break;
}