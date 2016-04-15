<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.3 2014-01-07 10:16:14 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'commande':
		
	break;
	case 'type_empty_word':
		include('./autorites/semantique/ajax/type_empty_word.inc.php');
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;	
	default:
	//tbd
	break;		
endswitch;	
