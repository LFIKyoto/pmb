<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7 2012-08-08 14:42:08 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice

// inclusions principales

switch($sub) {
	case "opac_view": 
		// affichage de la liste des vues Opac
		include("./admin/opac/opac_view/main.inc.php");
	break;	
	case "search_persopac":
		// affichage de la liste des recherches en opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_search_persopac"], $admin_layout);
		include("./admin/opac/search_persopac/main.inc.php");
	break;	
	case "stat":
		//affichage des statistiques pour l'opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["stat_opac_menu"], $admin_layout);	
		include("./admin/opac/stat/main.inc.php");
		break;
	case 'navigopac':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["exemplaire_admin_navigopac"], $admin_layout);
		print $admin_layout;
		include("./admin/opac/navigation_opac.inc.php");
		break;
	case "facette_search_opac":
		// affichage de la liste des facette en opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_opac_facette"], $admin_layout);
		include("./admin/opac/facette_search/main.inc.php");
	break;
	default :
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
        print $admin_layout;
        include("$include_path/messages/help/$lang/admin_opac.txt");
	break;
}
?>