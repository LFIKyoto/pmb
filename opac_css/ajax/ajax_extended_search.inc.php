<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_extended_search.inc.php,v 1.4 2018-03-02 15:37:18 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

$sc = new search($search_xml_file);

switch ($sub) {
	case 'get_already_selected_fields' :
		if ($add_field && $delete_field==="") {
			$search[] = $add_field;
		}
		print $sc->get_already_selected_fields();
		print '<script type="text/javascript">';
		print $sc->get_script_window_onload();
		print '</script>';
		break;
	default :
		break;
}