<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette.inc.php,v 1.6.4.1 2015-10-26 15:56:14 jpermanne Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/facette_search.class.php');
require_once($class_path.'/facette_search_compare.class.php');

switch($sub){
	case 'call_facettes':
		session_write_close();
		
		if($opac_facettes_ajax){
			$tab_result=$_SESSION['tab_result'];
			$str .= facettes::make_ajax_facette($tab_result);
			ajax_http_send_response($str);
		}
		
		break;
	case 'see_more':		
		$facette = new facettes();
		if($charset != "utf-8") $sended_datas=utf8_encode($sended_datas);
		$sended_datas=pmb_utf8_array_decode(json_decode(stripslashes($sended_datas),true));
		ajax_http_send_response($facette->see_more($sended_datas['json_facette_plus']));
	
		break;
	case 'compare_see_more':
		//les parametres nécéssaires
		global $pmb_compare_notice_template;
		global $pmb_compare_notice_nb;
		
		if($charset != "utf-8") $sended_datas=utf8_encode($sended_datas);
		$sended_datas=pmb_utf8_array_decode(json_decode(stripslashes($sended_datas),true));
		$sended_datas['json_notices_ids']=implode(',',$sended_datas['json_notices_ids']);
		
		$tab_return=array();
		if($charset != "utf-8") {
			$tab_return['notices']=utf8_encode(facette_search_compare::call_notice_display($sended_datas['json_notices_ids'], $pmb_compare_notice_nb, $pmb_compare_notice_template));
		} else {
			$tab_return['notices']=facette_search_compare::call_notice_display($sended_datas['json_notices_ids'], $pmb_compare_notice_nb, $pmb_compare_notice_template);
		}
		
		if($sended_datas['json_notices_ids']){
			if($charset != "utf-8") {
				$tab_return['see_more']=utf8_encode(facette_search_compare::get_compare_see_more($sended_datas['json_notices_ids']));
			} else {
				$tab_return['see_more']=facette_search_compare::get_compare_see_more($sended_datas['json_notices_ids']);
			}
		}
		
		ajax_http_send_response(json_encode($tab_return));
	
		break;	
}
