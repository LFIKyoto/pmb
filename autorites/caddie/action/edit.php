<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.2 2018-08-06 10:46:34 dgoron Exp $

// d�finition du minimum n�cessaire 
$base_path="../../..";                            
$base_auth = "AUTORITES_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");  
require_once ($class_path."/caddie/authorities_caddie_controller.class.php");

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

// cr�ation de la page
if(empty($mode)) $mode = 'simple';
switch($dest) {
	case "TABLEAU":
		authorities_caddie_controller::proceed_edition_tableau($idcaddie, $mode);
		break;
	case "TABLEAUHTML":
		authorities_caddie_controller::proceed_edition_tableauhtml($idcaddie, $mode);
		break;
	default:
		authorities_caddie_controller::proceed_edition_html($idcaddie, $mode);
		break;
}
	
pmb_mysql_close($dbh);
