<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categories.inc.php,v 1.8 2014-03-12 15:42:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/categ_browser.class.php");

// gestion des catégories
print "<h1>".$msg[140]."&nbsp;: ". $msg[134]."</h1>";

switch($sub) {
	case 'categ_form':
		include('./autorites/subjects/categ_form.inc.php');
		break;
	case 'delete':
		include('./autorites/subjects/categ_delete.inc.php');
		break;
	case 'update':
		include('./autorites/subjects/categ_update.inc.php');
		break;
	case 'search':
		include('./autorites/subjects/search.inc.php');
		break;
	case 'thes' :
		include('./autorites/subjects/thesaurus.inc.php');
		break; 
	case 'thes_form' :
		include('./autorites/subjects/thes_form.inc.php');
		break; 	
	case 'thes_update' :
		include('./autorites/subjects/thes_update.inc.php');
		break; 
	case 'thes_delete' :
		include('./autorites/subjects/thes_delete.inc.php');
		break;
	case 'categ_replace' :
		include('./autorites/subjects/categ_replace.inc.php');
		break;
	case 'categorie_last':
		$last_param=1;
		$tri_param = "order by id_noeud desc ";
		$limit_param = "limit 0, $pmb_nb_lastautorities ";
		$clef = "";
		$nbr_lignes = 0 ;
		include('./autorites/subjects/default.inc.php');
		break;
	default:
		include('./autorites/subjects/default.inc.php');
		break;
}
