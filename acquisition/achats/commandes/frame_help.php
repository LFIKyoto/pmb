<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frame_help.php,v 1.2 2015-04-03 11:16:26 jpermanne Exp $

$base_path="./../../..";
$base_auth = "ACQUISITION_AUTH";
$base_title = "\$msg[acquisition_menu]";
//permet d'appliquer le style de l'onglet ou apparait la frame
$current_alert = "acquisition";

require_once ("$base_path/includes/init.inc.php");

print "
<div class='row'>
	<div class='right'><a href='#' onClick='parent.kill_frame_help();return false;'><img src='".$base_path."/images/close.gif' border='0' align='right'></a></div>";

switch ($whatis) {
	case 'cde_saisie':
		require_once("$include_path/messages/help/$helpdir/acquisition_commande_saisie.txt");
		break;
}
	
print "</div>";

print "</body></html>";

pmb_mysql_close($dbh);

?>