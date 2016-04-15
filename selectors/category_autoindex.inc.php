<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category_autoindex.inc.php,v 1.2 2013-10-31 15:22:21 ngantier Exp $
//
// Navigation simple dans l'arbre des catégories

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if($autoindex_class)
require_once("$class_path/autoindex/".$autoindex_class.".class.php");

function get_autoindex_form(){
	global $msg,$autoindex_class;
	if(!$autoindex_class) return;
	$autoindex=new $autoindex_class();	
	return $autoindex->get_form();
}


function display_autoindex_list(){
	global $autoindex_class;
	
	if(!$autoindex_class) return;	
	$autoindex=new $autoindex_class();
	return $autoindex->index_list();
}
