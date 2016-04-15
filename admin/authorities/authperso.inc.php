<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.1 2014-08-27 09:02:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/authperso_admin.class.php");

switch($auth_action) {
	case 'form':
		$authperso=new authperso_admin($id_authperso);
		print $authperso->get_form();
		break;
	case 'save':
		$authperso=new authperso_admin($id_authperso);
		print $authperso->save();
		$authpersos=new authperso_admins();
		print $authpersos->get_list();
		break;	
	case 'delete':
		$authperso=new authperso_admin($id_authperso);
		print $authperso->delete();
		$authpersos=new authperso_admins();
		print $authpersos->get_list();
		break;			
	case 'edition': // gestion des champs persos (liste ,cration, edition, suppression...
		$authperso=new authperso_admin($id_authperso);		
		$authperso->fields_edition();		
		break;	
	case 'save_edition':
		$authperso=new authperso_admin($id_authperso);
		print $authperso->field_save();
		$authpersos=new authperso_admins();
		print $authpersos->get_list();
		break;		
	default:
		$authpersos=new authperso_admins();
		print $authpersos->get_list();
		break;
}
