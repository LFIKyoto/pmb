<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2012-08-23 08:48:11 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

echo window_title($database_window_title.$msg['editorial_content'].$msg[1003].$msg[1001]);
switch($sub) {
	case "type" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['editorial_content_type'], $admin_layout);
		print $admin_layout;
		include("./admin/cms/editorial/types.inc.php");
		break;
	case 'publication_state':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['editorial_content_publication_state'], $admin_layout);
		print $admin_layout;
		include("./admin/cms/editorial/publication_states.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		include("$include_path/messages/help/$lang/admin_cms_editorial.txt");
		break;
}