<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailtpl.inc.php,v 1.1 2012-07-05 14:33:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/mailtpl.class.php");

$mailtpl = new mailtpl($id_mailtpl);
switch($quoifaire){	
	case 'get_mailtpl' :
		ajax_http_send_response($mailtpl->get_mailtpl());
	break;	
}
