<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.inc.php,v 1.18 2019-08-02 08:42:43 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $acquisition_no_html, $include_path, $msg, $charset, $action, $id_bibli, $id_bud;

// gestion des budgets
require_once("$class_path/entites.class.php");
require_once("$class_path/budgets.class.php");

if (!$acquisition_no_html) {
	require_once("$include_path/templates/budgets.tpl.php");
	echo "<h1>".htmlentities($msg['acquisition_ach_ges'],ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg['acquisition_menu_ref_budget'],ENT_QUOTES, $charset)."</h1>";
}

switch($action) {
	case 'list':
		entites::setSessionBibliId($id_bibli);
		echo budgets::show_list_bud($id_bibli);
		break;
	case 'show':
	    echo budgets::show_bud($id_bibli, $id_bud);
		break;
	case 'print_budget':
	    budgets::print_bud($id_bibli, $id_bud);
		break;
	default:
		echo entites::show_list_biblio('show_list_bud', 'budgets');	
		break;
}
