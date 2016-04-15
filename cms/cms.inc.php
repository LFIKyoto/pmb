<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.inc.php,v 1.7 2015-01-09 10:20:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (substr(phpversion(), 0, 1) == "5") @ini_set("zend.ze1_compatibility_mode", "0");

require_once ("$include_path/cms/cms.inc.php");
require_once($class_path."/cms/cms_editorial.class.php");

if (!$pmb_editorial_dojo_editor && $pmb_javascript_office_editor){
	print $pmb_javascript_office_editor;
}

switch($categ) {			
	case 'build':
		require_once("./cms/cms_build/cms_build.inc.php");		
	break;
	case 'pages':
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		require_once("./cms/cms_pages/cms_pages.inc.php");		
	break;
	case 'section':
		require_once("./cms/cms_sections/cms_section.inc.php");		
	break;
	case 'editorial':
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		require_once("./cms/cms_editorial/cms_editorial.inc.php");		
	break;
	case 'article':
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		require_once("./cms/cms_articles/cms_articles.inc.php");		
	break;
	case "collection" :
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		require_once("./cms/cms_collection/cms_collection.inc.php");
		break;
	case 'manage':
		require_once("./cms/cms_manage_module.inc.php");		
		break;
	break;
	default:
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		print $cms_layout;
	break;
}		

print $cms_layout_end;