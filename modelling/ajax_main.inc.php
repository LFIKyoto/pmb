<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.3 2018-10-03 12:11:51 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants
require_once($class_path.'/modules/module_modelling.class.php');
switch($categ):
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("modelling",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'contribution_area':
		if ($pmb_contribution_area_activate) {
			$module_modelling = new module_modelling();
			if(!isset($id)) $id = 0; else $id += 0;
			$module_modelling->set_object_id($id);
			$module_modelling->proceed_ajax_contribution_area();
		}
		break;
	case 'computed_fields':
		$module_modelling = new module_modelling();
		$module_modelling->proceed_ajax_computed_fields();
		break;
	default:
		break;		
endswitch;
