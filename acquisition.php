<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.php,v 1.11 2019-08-20 09:18:41 btafforeau Exp $

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $menu_bar, $extra, $extra2, $extra_info, $use_shortcuts;
global $acquisition_layout, $acquisition_layout_end, $footer;

// définition du minimum nécéssaire 
$base_path = ".";                            
$base_auth = "ACQUISITION_AUTH";  
$base_title = "\$msg[acquisition_menu_title]";
$base_use_dojo = 1;

if (isset($_POST['dest']) && ($_POST['dest'] == "TABLEAU" || $_POST['dest'] == "TABLEAUHTML")) {
    $base_noheader = 1;
}

require_once ("$base_path/includes/init.inc.php");  

// modules propres à acquisition.php ou à ses sous-modules
require_once("$include_path/templates/acquisition.tpl.php");

if(!isset($dest)) $dest = '';
switch($dest) {
    case "TABLEAU":
        break;
    case "TABLEAUHTML":
        header("Content-Type: application/download\n");
        header("Content-Disposition: atttachement; filename=\"tableau.html\"");
        print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head>
				<body>";
        break;
    default:
        print "<div id='att' style='z-Index:1000'></div>";
        print $menu_bar;
        print $extra;
        print $extra2;
        print $extra_info;
        if ($use_shortcuts) {
            require_once("$include_path/shortcuts/circ.sht");
        }
        print $acquisition_layout;
        break;
}

require_once("./acquisition/acquisition.inc.php");

switch($dest) {
    case "TABLEAU":
        break;
    case "TABLEAUHTML":
        print $footer;
        break;
    default:
        print $acquisition_layout_end;
        // pied de page
        print $footer;
        break;
}
// deconnection MYSql
pmb_mysql_close();
?>