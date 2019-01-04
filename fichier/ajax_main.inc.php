<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.3 2016-03-29 15:31:33 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$prefix = "gestfic0";
switch($categ){
	
	case 'fiche':
		include('./fichier/ajax/fiche_ajax.inc.php');
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("fichier",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	
}