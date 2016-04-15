<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//les parametres nécéssaires
global $pmb_compare_notice_template;
global $pmb_compare_notice_nb;

require_once("$class_path/facette_search_compare.class.php");
$facette_compare = new facette_search_compare($pmb_compare_notice_template,$pmb_compare_notice_nb);

switch($action) {	
	case "save":
		$facette_compare->save_form_compare();
		
		$pmb_compare_notice_template=$facette_compare->notice_tpl;
		$pmb_compare_notice_nb=$facette_compare->notice_nb;
		
		print $facette_compare->display_compare();
	break;
	case "modify":
		print $facette_compare->form_compare();
		break;
	case "display":
	default:
		print $facette_compare->display_compare();
	break;
}

