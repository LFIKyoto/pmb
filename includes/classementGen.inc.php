<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.inc.php,v 1.1 2015-03-30 07:14:52 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$classementGen = new classementGen($categ,0);

switch($action){
	case "edit" :
		print $classementGen->show_form_edit_classement($classement,$baseLink);
		break;
	case "update" :
		$classementGen->update_classement($oldClassement,$newClassement,$PMBuserid);
		print $classementGen->show_list_classements($PMBuserid,$baseLink);
		break;
	case "delete" :
		$classementGen->delete_classement($oldClassement,$PMBuserid);
		print $classementGen->show_list_classements($PMBuserid,$baseLink);
		break;
	default :
		print $classementGen->show_list_classements($PMBuserid,$baseLink);
		break;
}