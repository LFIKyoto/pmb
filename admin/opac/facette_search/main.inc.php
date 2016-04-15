<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_compare_notice_active;

switch($section){
	case "facette":
		// affichage de la liste des recherches en opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_opac_facette"], $admin_layout);
		print $admin_layout;
		if($opac_compare_notice_active*1=='1'){
			print $admin_menu_facettes;
		}
		include("./admin/opac/facette_search/facette.inc.php");
	break;
	case "comparateur":
		// affichage de la liste des recherches en opac
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_opac_facette"], $admin_layout);
		print $admin_layout;
		if($opac_compare_notice_active*1=='1'){
			print $admin_menu_facettes;
		}
		include("./admin/opac/facette_search/comparateur.inc.php");
		break;
}