<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2017-05-06 12:03:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// functions particulières à ce module
require_once("$include_path/templates/etagere.tpl.php");
require_once("$include_path/etagere.inc.php");
require_once("$include_path/cart.inc.php");
require_once("$class_path/etagere.class.php");
require_once("$class_path/classementGen.class.php");

switch($sub) {
	case "constitution" :
		echo window_title($database_window_title.$msg["etagere_menu"]." : ".$msg["etagere_menu_constitution"]);
		print "<h1>".$msg["etagere_menu"]." > ".$msg["etagere_menu_constitution"]."</h1>" ;
		include('./catalog/etagere/constitution.inc.php');
		break;
	case "classementGen" :
		echo window_title($database_window_title.$msg["etagere_menu"]." : ".$msg["etagere_menu_classement"]);
		print "<h1>".$msg["etagere_menu"]." > ".$msg["etagere_menu_classement"]."</h1>" ;
		$baseLink="./catalog.php?categ=etagere&sub=classementGen";
		$classementGen = new classementGen($categ,0);
		$classementGen->proceed($action);
		break;
	case "gestion" :
	default:
		echo window_title($database_window_title.$msg["etagere_menu"]." : ".$msg["etagere_menu_gestion"]);
		print "<h1>".$msg["etagere_menu"]." > ".$msg["etagere_menu_gestion"]."</h1>" ;
		include('./catalog/etagere/etagere.inc.php');
		break;
	}

