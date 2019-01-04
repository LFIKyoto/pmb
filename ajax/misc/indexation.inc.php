<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation.inc.php,v 1.1 2017-11-13 11:17:56 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/indexation_stack.class.php");
require_once($class_path."/encoding_normalize.class.php");

switch($sub){
	case 'get_infos':
		print encoding_normalize::json_encode(indexation_stack::get_indexation_state());		
		break;
}
