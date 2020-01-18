<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: modelling.php,v 1.5.2.1 2019-11-14 10:45:31 jlaurent Exp $


// définition du minimum nécessaire 
$base_path=".";                            
$base_auth = "MODELLING_AUTH";  
$base_title = "\$msg[param_modelling]";  
                            
$base_use_dojo=1; 

require_once ($base_path."/includes/init.inc.php");  

print " <script type='text/javascript' src='javascript/ajax.js'></script>";
print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;

if($use_shortcuts) {
	include($include_path."/shortcuts/circ.sht");
}
echo window_title($database_window_title.$msg['modelling'].$msg[1003].$msg[1001]);

if($pmb_javascript_office_editor){
    print $pmb_javascript_office_editor;
    print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
}
require_once($class_path."/modules/module_modelling.class.php");

$module_modelling = new module_modelling();
if (!isset($id)) {
	$id = 0;
}
$id = intval($id);
$module_modelling->set_object_id($id);
$module_modelling->set_url_base($base_path.'/modelling.php?categ='.$categ);
$module_modelling->proceed();

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);