<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: check_session_time.inc.php,v 1.10 2012-11-21 10:07:28 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$time_expired=0;
if ($_SESSION["user_code"] && !$_SESSION["cms_build_activate"]) {
	if (!$opac_duration_session_auth) $opac_duration_session_auth=600;
	if (((time()-$_SESSION["connect_time"])>$opac_duration_session_auth)&&($opac_duration_session_auth!=-1)) {
		unset($_SESSION["user_code"]);		
				
		session_destroy();	
				
		$time_expired=1;
	} else {
		$_SESSION["connect_time"]=time();
	}
}
