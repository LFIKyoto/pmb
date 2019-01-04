<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.1 2017-07-21 08:34:40 vtouchard Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path.'/explnum_licence/explnum_licence.class.php');
session_write_close();

switch ($sub) {
	case 'get_licence_tooltip':
		$id+=0;
		print explnum_licence::get_explnum_licence_tooltip($id);
		break;
	case 'get_licence_as_pdf':
		$id+=0;
		print explnum_licence::get_explnum_licence_as_pdf($id);
		break;
}

?>