<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheets.inc.php,v 1.1 2016-07-26 13:38:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/sticks_sheet/sticks_sheets.class.php");

switch($action){
	case 'get_data':
		$sticks_sheets = new sticks_sheets();
		print $sticks_sheets->get_json_data();
		break;
}