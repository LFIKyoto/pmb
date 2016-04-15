<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/facette_search_opac.class.php");

$test = new facette_search();

switch($action) {	
	case "save":
		print $test->save_form_facette();
	break;
	case "delete":
		print $test->delete_facette();
	break;
	case "new":
		print $test->form_facette();
	break;
	case "edit":
		print $test->edit_facette();
	break;
	case "up":
		$test->facette_up($idF);
		print $test->view_list_facette();
	break;
	case "down":
		$test->facette_down($idF);
		print $test->view_list_facette();
	break;
	case "order":
		$test->facette_order_by_name($idF);
		print $test->view_list_facette();
	break;
	default:
		print $test->view_list_facette();
	break;
}

