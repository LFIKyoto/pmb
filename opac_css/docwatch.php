<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch.php,v 1.1 2015-03-02 17:31:34 arenou Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;

//Il me faut le charset pour la suite
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path.'/includes/opac_config.inc.php');
// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");

require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path."/includes/rec_history.inc.php");
	
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');


// à inclure?
require_once($base_path."/includes/check_session_time.inc.php");

//le kit nécessaire aux veilles...
require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("docwatch",true);
//ca suffit...

if($id){
	$watch = new docwatch_watch($id);
	$watch->fetch_items(true);
	print $watch->get_xmlrss();
}
?>