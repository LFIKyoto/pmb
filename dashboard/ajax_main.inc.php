<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4 2019-08-12 10:31:10 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants
require_once($class_path."/autoloader.class.php");
$autoload = new autoloader();


if($categ === 'plugin') {
	$plugins = plugins::get_instance();
	$file = $plugins->proceed_ajax("dashboard",$plugin,$sub);
	if($file){
		include $file;
	}
}else{
	switch($sub):
		case "save_quick_params":
			if(count($_POST)){
				$class_name="dashboard_module_".$module;
				$save = new $class_name();
				$result = $save->save_quick_params();
				ajax_http_send_response($result);
			}else{
				ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
			}
		break;
		case "save_notification_readed" :
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				$notifications[$module] = 0;			
				$query = "update sessions set notifications = '".addslashes(serialize($notifications))."' where  SESSID = ".SESSid;
				$result = pmb_mysql_query($query);
				if($result){
					ajax_http_send_response(1);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;	
		case "save_new_notification" :
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				$notifications[$module] = 1;
				$query = "update sessions set notifications = '".addslashes(serialize($notifications))."' where  SESSID = ".SESSid;
				$result = pmb_mysql_query($query);
				if($result){
					ajax_http_send_response(1);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;
		case "get_notifications_state" :
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				if(isset($notifications[$module])){
					ajax_http_send_response($notifications[$module]);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;		
		default:
			ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
			break;		
	endswitch;	

}