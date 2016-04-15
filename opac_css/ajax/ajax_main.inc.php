<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.24.2.2 2015-10-14 08:55:14 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'misc':
		include('./ajax/misc/misc.inc.php');
	break;
	case 'liste_lecture' :
		include('./ajax/ajax_liste_lecture.inc.php');
	break;
	case 'demandes' :
		include('./ajax/ajax_demandes.inc.php');
	break;
	case "enrichment" :
		include("./ajax/ajax_enrichment.inc.php"); 
		break;
	case "search" :
		include("./ajax/ajax_search.inc.php"); 
		break;
	case "perio_a2z" :
		include("./ajax/perio_a2z.inc.php"); 
		break;
	case "avis" :
		include("./ajax/avis.inc.php"); 
		break;
	case "simili" :
		include("./ajax/simili_search.inc.php"); 
		break;
	case "level1" :
		include("./ajax/ajax_level1.inc.php"); 
		break;
	case "expl_voisin" :
		include("./ajax/expl_voisin.inc.php"); 
		break;
	case 'facette':
		include('./ajax/facette.inc.php');
	break;
	case 'print_docnum':
		include('./ajax/print_docnum.inc.php');
	break;
	case 'explnum_associate':
		include('./ajax/explnum_associate.inc.php');
		break;
	case "auth":
		require_once($class_path."/auth_popup.class.php");
		//print $popup_header;
		$auth_popup = new auth_popup();
		$auth_popup->process();
		break;
	//abacarisse
	case 'param_social_network' :
		include('./ajax/ajax_param_social_network.inc.php');
		break;
	//abacarisse
	case "extend" :
		if(file_exists("./ajax/misc/extend.inc.php")) include("./ajax/misc/extend.inc.php");
		break;
	case 'sort' :
		include('./ajax/sort.inc.php');
		break;
	case 'map' :
		include('./ajax/misc/map.inc.php');
		break;
	case 'notice' :
		include('./ajax/misc/notice.inc.php');
		break;
	case 'messages':
		include('./ajax/misc/messages.inc.php');
		break;
	case 'storage' :
		include('./ajax/storage.inc.php');
		break;
	case 'download_docnum':
		include('./ajax/download_docnum.inc.php');
		break;
	case 'log':
		include('./ajax/log.inc.php');
		break;
	default:
	break;		
endswitch;	
