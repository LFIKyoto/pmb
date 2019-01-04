<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.inc.php,v 1.1 2016-04-28 09:08:01 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$temp_aff = demande_abo();

if ($temp_aff) $aff_alerte .= "<ul>".$msg["menu_alert_demande_abo"].$temp_aff."</ul>";
  
function demande_abo () {
	global $dbh,$msg ;
	
	$sql="SELECT * FROM serialcirc_ask WHERE serialcirc_ask_statut=0";
	$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
	$nb = pmb_mysql_num_rows($req) ;

	if (!$nb) return "" ;
	else return "<li><a href='./catalog.php?categ=serials&sub=circ_ask' target='_parent'>".$msg["alert_demande_abo"]."</a></li>" ;
}

