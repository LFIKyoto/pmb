<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: planificateur.inc.php,v 1.2 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/tache.class.php");
require_once($class_path."/connecteurs.class.php");
require_once($base_path."/admin/planificateur/templates/tache_rapport.tpl.php");

function show_rapport() { 
	global $msg, $dbh, $base_path, $report_task, $report_error, $task_id, $type_task_id;
	
	$query_chk = "select id_tache from taches where id_tache=".$task_id;
	$res_chk = pmb_mysql_query($query_chk, $dbh);
	
	if (pmb_mysql_num_rows($res_chk) == '1') {
		//date de génération du rapport
		$rs = pmb_mysql_query("select curdate()");
		$date_MySQL = pmb_mysql_result($rs, $row);
				
		$tasks = new taches();
		foreach ($tasks->types_taches as $type_tache) {
			if ($type_tache->id_type == $type_task_id) {
				require_once($base_path."/admin/planificateur/".$type_tache->name."/".$type_tache->name.".class.php");
				eval("\$conn=new ".$type_tache->name."(\"".$base_path."/admin/planificateur/".$type_tache->name."\");");
				$task_datas = $conn->get_report_datas($task_id);
				
				//affiche le rapport avec passage du template
				$report_task = str_replace("!!print_report!!", "<a onclick=\"openPopUp('./pdf.php?pdfdoc=rapport_tache&type_task_id=$type_task_id&task_id=".$task_id."', 'Fiche', 500, 400, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" href=\"#\"><img src='".$base_path."/images/print.gif' alt='Imprimer...' /></a>", $report_task);
				$report_task = str_replace("!!type_tache_name!!", $type_tache->comment, $report_task);
				$report_task = str_replace("!!planificateur_task_name!!", $msg["planificateur_task_name"], $report_task);
				$report_task=str_replace("!!date_mysql!!",formatdate($date_MySQL),$report_task);
				$report_task=str_replace("!!libelle_date_generation!!",$msg["tache_date_generation"],$report_task);
				$report_task=str_replace("!!libelle_date_derniere_exec!!",$msg["tache_date_dern_exec"],$report_task);
				$report_task=str_replace("!!libelle_heure_derniere_exec!!",$msg["tache_heure_dern_exec"],$report_task);
				$report_task=str_replace("!!libelle_date_fin_exec!!",$msg["tache_date_fin_exec"],$report_task);
				$report_task=str_replace("!!libelle_heure_fin_exec!!",$msg["tache_heure_fin_exec"],$report_task);
				$report_task=str_replace("!!libelle_statut_exec!!",$msg["tache_statut"],$report_task);
				$report_task=str_replace("!!report_execution!!",$msg["tache_report_execution"],$report_task);
				
				$report_task=str_replace("!!id!!",$task_datas["id_tache"],$report_task);
				$report_task=str_replace("!!libelle_task!!",stripslashes($task_datas["libelle_tache"]),$report_task);
				$report_task=str_replace("!!date_dern_exec!!",formatdate($task_datas['start_at'][0]),$report_task);
				$report_task=str_replace("!!heure_dern_exec!!",$task_datas['start_at'][1],$report_task);
				$report_task=str_replace("!!date_fin_exec!!",formatdate($task_datas['end_at'][0]),$report_task);
				$report_task=str_replace("!!heure_fin_exec!!",$task_datas['end_at'][1],$report_task);
				$report_task=str_replace("!!status!!",$msg["planificateur_state_".$task_datas["status"]],$report_task);
				$report_task=str_replace("!!percent!!",$task_datas["indicat_progress"],$report_task);
				
				$report_execution = $conn->show_report($task_datas["rapport"]);
				$report_task=str_replace("!!rapport!!",$report_execution,$report_task);
				
				ajax_http_send_response($report_task);
				return;
			}
		}
	} else {
		// contenu non disponible
		$report_task = "Contenu non disponible";
		ajax_http_send_response($report_error);
//		ajax_http_send_error('400',$msg['error_message_invalid_date']);
		return;
	}
}

switch($sub) {
	case 'get_report' :
		print show_rapport();
		break;
	case 'reporting':
		$tasks = new taches();
		$tasks->get_tasks_plan();
		break;
	case 'command':
		$tasks = new taches();
		print $tasks->command_waiting($task_id,$cmd);
		break;
//	case 'source_synchro':
//		if ($id) {
//			if ($planificateur_id) {
//				$sql = "select param from planificateur where id_planificateur=".$planificateur_id;
//				$res = pmb_mysql_query($sql);
//				
//				$params = pmb_mysql_result($res,0,"param");
//			} else {
//				$params ="";
//			}
//			$contrs=new connecteurs();
//			require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."/".$contrs->catalog[$id]["NAME"].".class.php");
//			eval("\$conn=new ".$contrs->catalog[$id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."\");");
//			$conn->unserialized_environnement($source_id,$params);
//
//			//Si on doit afficher un formulaire de synchronisation
//			$syncr_form = $conn->form_pour_maj_entrepot($source_id,"planificateur_form");			
//			if ($syncr_form) {
//				print utf8_normalize($syncr_form);
//			}
//		}
//		break;		
}
?>