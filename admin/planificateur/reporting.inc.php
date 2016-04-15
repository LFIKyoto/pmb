<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reporting.inc.php,v 1.1 2011-07-29 12:32:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/tache.class.php");

define ('RESUME','1');
define ('SUSPEND','2');
define ('STOP','3');
define ('RETRY','4');
define ('ABORT','5');

define('WAITING','1');
define('RUNNING','2');
define('ENDED','3');
define('SUSPENDED','4');
define('STOPPED','5');
define('FAILED','6');

function reporting_list () {
	global $base_path,$msg, $charset, $act;
	
	$tasks = new taches();

	print "<script>
			function show_docsnum(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
					
				} else {
					document.getElementById(id).style.display='none';
				}
			} 
		</script>
		<script type=\"text/javascript\" src='".$base_path."/javascript/select.js'></script>
		<script>
			var ajax_get_report=new http_request();
			
			function get_report_content(task_id,type_task_id) {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=get_report&task_id='+task_id+'&type_task_id='+type_task_id;
				  ajax_get_report.request(url,0,'',1,show_report_content,0,0); 
			}
			
			function show_report_content(response) {
				document.getElementById('frame_notice_preview').innerHTML=ajax_get_report.get_text();
			}
			
			function refresh() {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=reporting';
				ajax_get_report.request(url,0,'',1,refresh_div,0,0); 
				
			}
			function refresh_div() {
				document.getElementById('table_reporting', true).innerHTML=ajax_get_report.get_text();
				var timer=setTimeout('refresh()',20000);
			}
			
			var ajax_command=new http_request();
			var tache_id='';
			function commande(id_tache, cmd) {
				tache_id=id_tache;
				var url_cmd = './ajax.php?module=ajax&categ=planificateur&sub=command&task_id='+tache_id+'&cmd='+cmd;
				ajax_command.request(url_cmd,0,'',1,commande_td,0,0); 
			}
			function commande_td() {
				document.getElementById('commande_tache_'+tache_id, true).innerHTML=ajax_command.get_text();
			}
		</script>
		<script type='text/javascript'>var timer=setTimeout('refresh()',20000);</script>";
	
	$tasks->get_tasks_plan();
}


switch ($act)  {
	case 'report_table':
		reporting_list();
		break;
	default:
		reporting_list();
		break;
}




