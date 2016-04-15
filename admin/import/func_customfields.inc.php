<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_customfields.inc.php,v 1.3.4.2 2015-09-25 15:20:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

include_once $base_path.'/admin/import/lib_func_customfields.inc.php';

function recup_noticeunimarc_suite($notice) {
	func_customfields_recup_noticeunimarc_suite($notice);
} // fin recup_noticeunimarc_suite 
	
function import_new_notice_suite() {
	func_customfields_import_new_notice_suite();
} 