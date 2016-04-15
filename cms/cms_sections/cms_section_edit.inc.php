<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section_edit.inc.php,v 1.2 2013-09-06 08:00:05 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_section.class.php");

if($id != "new"){
	$section = new cms_section($id);
}else if ($num_parent){
	$section = new cms_section(0,$num_parent);
}else{
	$section = new cms_section();
}

print $section->get_form("cms_section_edit","cms_section_edit");