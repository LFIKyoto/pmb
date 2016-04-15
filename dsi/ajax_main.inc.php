<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4 2015-02-16 15:59:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ){	
	case 'bannettes':
		include('./dsi/bannettes/main.inc.php');
		break;		
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'docwatch' :
		include("./dsi/docwatch/ajax_main.inc.php");
		break;
	default:
	//tbd
	break;		
}	
