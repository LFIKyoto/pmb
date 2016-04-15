<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_agroparistech.inc.php,v 1.2.10.1 2015-09-02 09:32:05 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//EQUIVALENT DE FUNC_CATEGORY_AUTO + inversion dans les monos du 200$i et du 200$a
require_once("func_category_auto.inc.php");

function z_recup_noticeunimarc_suite($notice) {
	global $base_path;
	require_once($base_path."/admin/import/func_agroparistech.inc.php");
	recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	global $base_path;
	require_once($base_path."/admin/import/func_agroparistech.inc.php");
	import_new_notice_suite();
}

function traite_info_subst(&$obj){

	//pour les monographies, le 200$a et 200$i s'inverse...
	if($obj->bibliographic_level=="m"){
		if($obj->serie_200[0]['i'] != ''){
			$tmp_buffer = $obj->serie_200[0]['i'];
			$obj->serie = $obj->titles[0];
			$obj->titles[0] = $tmp_buffer;
		}
	}
}