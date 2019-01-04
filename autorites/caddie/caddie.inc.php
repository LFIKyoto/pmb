<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.inc.php,v 1.1 2017-05-06 12:03:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idcaddie)) $idcaddie = 0;
if(!isset($quoi)) $quoi = '';
if(!isset($moyen)) $moyen = '';
if(!isset($quelle)) $quelle = '';

require_once($class_path."/caddie/authorities_caddie_controller.class.php") ;

$idcaddie = authorities_caddie::check_rights($idcaddie) ;

if(!isset($sub) || !$sub) $sub = 'gestion';
echo window_title($database_window_title.$msg['caddie_menu']." : ".$msg["caddie_menu_".$sub]);
switch($sub) {
	case "pointage" :
		$autorites_layout = str_replace('<!--!!menu_contextuel!! -->', $autorites_menu_panier_pointage, $autorites_layout);
		authorities_caddie_controller::proceed_module_pointage($moyen, $idcaddie);
		break;
	case "action" :
		$autorites_layout = str_replace('<!--!!menu_contextuel!! -->', $autorites_menu_panier_action, $autorites_layout);
		authorities_caddie_controller::proceed_module_action($quelle, $idcaddie);
		break;
	case "collecte" :
		$autorites_layout = str_replace('<!--!!menu_contextuel!! -->', $autorites_menu_panier_collecte, $autorites_layout);
		authorities_caddie_controller::proceed_module_collecte($moyen, $idcaddie);
		break;
	case "gestion" :
	default:
		$autorites_layout = str_replace('<!--!!menu_contextuel!! -->', $autorites_menu_panier_gestion, $autorites_layout);
		authorities_caddie_controller::proceed_module_gestion($quoi, $idcaddie);
		break;
}

