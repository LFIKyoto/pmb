<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.inc.php,v 1.3 2014-11-17 13:39:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$include_path/cms/cms.inc.php");
require_once ("$include_path/templates/cms/cms_build.tpl.php");  
require_once("$class_path/cms/cms_build.class.php");

$cms_build=new cms_build($opac_id);


switch($sub) {			
	case 'block':
		$cms_layout = str_replace('!!menu_sous_rub!!', $msg["cms_menu_build_page_layout"], $cms_layout);
		print $cms_layout;
		print $cms_build->get_form_block();
	break;
	default:
		$cms_layout = str_replace('!!menu_sous_rub!!', $msg["cms_menu_build_page_layout"], $cms_layout);
		print $cms_layout;
		print $cms_build->get_form_block();
	break;
}		
