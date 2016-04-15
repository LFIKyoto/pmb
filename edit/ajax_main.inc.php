<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2014-01-07 10:16:16 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($categ){
	case "editions_state" :
		include("./edit/editions_state/ajax_main.inc.php");
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
}