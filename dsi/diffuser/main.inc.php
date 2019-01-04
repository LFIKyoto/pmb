<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2017-11-13 10:24:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';
if(!isset($liste_bannette)) $liste_bannette = array();
if(!isset($id_bannette)) $id_bannette = 0;

require_once($class_path."/dsi/bannettes_controller.class.php") ;

echo window_title($database_window_title.$msg['dsi_menu_title']);

// en visualisation, possibilit� de supprimer des notices � la demande
if ($suite=="suppr_notice") {
	$bannette = new bannette($id_bannette) ;
	$bannette->suppr_notice($num_notice);
	// on r�affiche la bannette de laquelle on a supprim� une notice
	$liste_bannette[] = $id_bannette ;
	$suite = "visualiser";
}

switch($sub) {
	case 'lancer':
		print "<h1>".$msg['dsi_dif_auto_titre']."</h1>" ;
		break;
	case 'auto':
		print "<h1>".$msg['dsi_dif_auto']."</h1>" ;
		break;
	case 'manu':
		print "<h1>".$msg['dsi_dif_manu']."</h1>" ;
		break;
	default:
		break;
}

bannettes_controller::proceed_module_diffusion($suite);
