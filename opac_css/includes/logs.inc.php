<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: logs.inc.php,v 1.5 2015-04-10 14:36:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($base_path."/classes/record_log.class.php");

global $pmb_logs_activate;
if($pmb_logs_activate){
	$tab_logs_exclude_robots = array();
	$tab_logs_exclude_robots = explode(",", $pmb_logs_exclude_robots);
	if ($tab_logs_exclude_robots[0]) {
		$robots = array('BOT','SPIDER','CRAWL');
		foreach ($robots as $robot) {
			if (preg_match('/'.$robot.'/i',$_SERVER[HTTP_USER_AGENT])){
				$pmb_logs_activate = 0;
			}
		}
	}
	if (count($tab_logs_exclude_robots) > 1) {
		$ip_adress = array();
		for($i=1;$i<count($tab_logs_exclude_robots);$i++) {
			$ip_adress[] = $tab_logs_exclude_robots[$i];
		}
		if (in_array($_SERVER['REMOTE_ADDR'], $ip_adress)) {
			$pmb_logs_activate = 0;
		}
	}
	//Opposition à l'utilisation des cookies, aucun enregistrement de logs
	if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false") {
		$pmb_logs_activate = 0;
	}
	global $log;
	$log = new record_log();	
}
?>