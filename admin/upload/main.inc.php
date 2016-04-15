<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2014-09-16 13:51:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'rep':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["upload_repertoire"], $admin_layout);
		print $admin_layout;
		include("./admin/upload/folders.inc.php");		
		break;
	case "storages" :
		require_once($class_path."/storages/storages.class.php");
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["storage_menu"], $admin_layout);
		print $admin_layout;		
		$storages = new storages();
		$storages->process($action,$id);
		break;
	case "statut" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_docnum_statut"], $admin_layout);
		print $admin_layout;
		include("./admin/upload/statut.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg[131].$msg[1003].$msg[1001]);
		break;
}
?>