<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl.inc.php,v 1.1 2014-10-14 09:44:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc_tpl.class.php");

$serialcirc_tpl=new serialcirc_tpl($id);

switch ($action) {
	case "edit": 
		print $serialcirc_tpl->show_form();
	break;
	case "update": 
		$serialcirc_tpl->update_from_form();	
		print $serialcirc_tpl->show_list();	
	break;	
	case "delete": 
		$serialcirc_tpl->delete();
		print $serialcirc_tpl->show_list();
	break;
	case 'duplicate':
		$serialcirc_tpl->id = 0;
		$serialcirc_tpl->duplicate_from_id = $id;
		print $serialcirc_tpl->show_form();
		break;
	case 'add_field':
		print $serialcirc_tpl->show_form("./edit.php",$action);
		break;
	case 'del_field':
		print $serialcirc_tpl->show_form("./edit.php",$action);
		break;
	default:
		print $serialcirc_tpl->show_list();
	break;	
}

?>
