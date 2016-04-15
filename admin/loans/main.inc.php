<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2015-06-26 13:15:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	default:
	case 'perso':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_loans_perso"], $admin_layout);
		print $admin_layout;
		include("./admin/loans/perso.inc.php");
		break;
}
