<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: s.php,v 1.2 2015-04-17 14:12:03 ngantier Exp $
$base_path=".";

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

require_once("$class_path/shorturl/shorturls.class.php");

require_once($base_path."/includes/search_queries/specials/combine/search.class.php");

require_once($include_path."/rec_history.inc.php");

if($action=="gen"){	
	$mc=combine_search::simple2mc($_SESSION['last_query']);
	$memo["serialized_search"]=unserialize($mc['serialized_search']);
	$memo["search_type"]=$mc['search_type'];
	$memo["human_query"]=get_human_query_level_two($_SESSION['last_query']);
	
	$h=shorturls::generate_obj("search", "rss", addslashes(serialize($memo)));
	print "
	<script>
 		document.location.href='$base_path/s.php?h=$h' 
	</script>";
	exit;
}	
shorturls::get_obj($h);

