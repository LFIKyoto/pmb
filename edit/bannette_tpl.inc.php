<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.inc.php,v 1.1 2014-10-20 13:38:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/bannette_tpl.class.php");

$bannette_tpl=new bannette_tpl($id);

switch ($action) {
	case "edit": 
		print $bannette_tpl->show_form();
	break;
	case "update": 
		$bannette_tpl->update_from_form();	
		print $bannette_tpl->show_list();	
	break;	
	case "delete": 
		$bannette_tpl->delete();
		print $bannette_tpl->show_list();
	break;
	case 'duplicate':
		$bannette_tpl->id = 0;
		$bannette_tpl->duplicate_from_id = $id;
		print $bannette_tpl->show_form();
		break;
	default:
		print $bannette_tpl->show_list();
	break;	
}

?>
