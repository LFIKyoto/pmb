<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2011-06-06 08:04:26 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($sub){
	case 'recept':
		include("./acquisition/achats/receptions/ajax/ajax_receptions.inc.php");
		break;
	default:
		break;		
}	
