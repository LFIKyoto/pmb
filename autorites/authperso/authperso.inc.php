<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.3 2014-10-09 07:18:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once('./autorites/auth_common.inc.php');
require_once("$class_path/authperso.class.php");

// gestion des authperso
$authperso = new authperso($id_authperso,$id);
print "<h1>".$msg[140]."&nbsp;: ".$authperso->info['name']."</h1>";

$url_base = "./autorites.php?categ=authperso&sub=&id_authperso=$id_authperso&user_input=".rawurlencode(stripslashes($user_input))."&exact=$exact" ;

switch($sub) {
	case 'reach':
			print $authperso->get_list();
		break;
	case 'delete':
		$sup_result = $authperso->delete($id);
		if(!$sup_result)
			print $authperso->get_list();
		else {
			error_message($msg[132], $sup_result, 1, "./autorites.php?categ=authperso&sub=authperso_form&id_authperso=$id_authperso&id=$id");
		}
		break;
	case 'replace':
		if(!$by) {
			print $authperso->replace_form($id);
		} else {
			// routine de remplacement
			$rep_result = $authperso->replace($id,$by,$aut_link_save);
			if(!$rep_result)
				print $authperso->get_list();
			else {
				error_message($msg[132], $rep_result, 1, "./autorites.php?categ=authperso&sub=authperso_form&id_authperso=$id_authperso&id=$id");
			}
		}
		break;
	case 'update':				
		$authperso->update_from_form($id);
		print $authperso->get_list();
		break;
	case 'authperso_form':
		print $authperso->get_form($id);
		break;
	case 'authperso_last':			
		print $authperso->get_list();
		break;
	case 'duplicate':

		break;
	default:
		// affichage du début de la liste
		print $authperso->get_list();
		break;
}
