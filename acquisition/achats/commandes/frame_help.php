<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frame_help.php,v 1.5 2017-11-21 12:01:00 dgoron Exp $

$base_path="./../../..";
$base_auth = "ACQUISITION_AUTH";
$base_title = "\$msg[acquisition_menu]";
//permet d'appliquer le style de l'onglet ou apparait la frame
$current_alert = "acquisition";

require_once ("$base_path/includes/init.inc.php");

print "
<div class='row'>
	<div class='right'><a href='#' onClick='parent.kill_frame_help();return false;'><img src='".get_url_icon('close.gif')."' style='border:0px' class='align_right'></a></div>";

switch ($whatis) {
	case 'cde_saisie':
		require_once("$include_path/messages/help/$helpdir/acquisition_commande_saisie.txt");
		break;
}
	
print "</div>";

print "</body></html>";

pmb_mysql_close($dbh);

?>