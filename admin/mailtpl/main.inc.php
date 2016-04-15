<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2012-07-05 14:33:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'build':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_mailtpl_title"], $admin_layout);
		print $admin_layout;
		include("./admin/mailtpl/build.inc.php");		
	break;
	case 'img':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_mailtpl_img_title"], $admin_layout);
		print $admin_layout;
		include("./admin/mailtpl/img.inc.php");		
	break;
	
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[131].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_mailtpl.txt");
	break;
}
?>