<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_vign.php,v 1.9.2.1 2015-11-30 08:57:33 mbertin Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once("./includes/apache_functions.inc.php");

//Renvoi du statut 304 si possible
$mode = (string) $_GET['mode'];
$type = (string) $_GET['type'];
$id = (int) $_GET['id'];

$headers = getallheaders();
//une journée
$offset = 60 * 60 * 24 ;
if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) <= time())) {
	header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
	return;
}else{
	header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
}

require_once ("$base_path/includes/init.inc.php"); 
require_once ("$base_path/includes/error_report.inc.php");
require_once ("$base_path/includes/global_vars.inc.php");

// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();
require_once($base_path."/includes/session.inc.php");
session_write_close();

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("cms_modules",true);

//on ne charge que le minima, donc il faut aller chercher soit même le param qui nous interesse
$query = "select valeur_param from parametres where type_param= 'cms' and sstype_param='active_image_cache'";
$result = pmb_mysql_query($query,$dbh);
if(pmb_mysql_num_rows($result)){
	global $cms_active_image_cache;
	$cms_active_image_cache = pmb_mysql_result($result,0,0);
}

$logo = new cms_logo($id,$type);
$logo->show_picture($mode);