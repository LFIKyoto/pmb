<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: manager.inc.php,v 1.4.6.1 2015-09-01 10:40:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/tache.class.php");
require_once($class_path."/tache_calendar.class.php");

	function task_list () {
		global $base_path, $msg, $charset, $type_task_id;
		$tasks = new taches();
		
		print "
		<script type='text/javascript'>
			function show_taches(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
				} else {
					document.getElementById(id).style.display='none';
				}
			}
			function expand_taches_all() {";
				foreach ($tasks->types_taches as $type_tache) {
					print "if (document.getElementById('".$type_tache->name."').style.display=='none') {
						document.getElementById('".$type_tache->name."').style.display='';
					}";
				}	
			print "}
			function collapse_taches_all() {";
				foreach ($tasks->types_taches as $type_tache) {
					print "if (document.getElementById('".$type_tache->name."').style.display=='') {
						document.getElementById('".$type_tache->name."').style.display='none';
					} ";
				}	
			print "}
		</script>
		<script type='text/javascript' src='".$base_path."/javascript/tablist.js'></script>
		<a href='javascript:expand_taches_all()'><img border='0' id='expandall' src='./images/expand_all.gif'></a>		
		<a href='javascript:collapse_taches_all()'><img border='0' id='collapseall' src='".$base_path."/images/collapse_all.gif'></a>
		<table>
			<tr>
				<th>&nbsp;</th>
				<th>".$msg["planificateur_type_task"]."</th>
				<th>".$msg["planificateur_task"]."</th>
				<th>&nbsp;</th>
			</tr>";
		
		$pair_impair=0;
		$parity=0;
		
		//on affiche chaque type de tache
		foreach($tasks->types_taches as $type_tache) {
			$pair_impair = $parity++ % 2 ? "even" : "odd";

			//recherche du nombre de tâches planifiées
			$n_taches = $tasks->get_nb_tasks($type_tache->id_type);

		    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"if (event) e=event; else e=window.event; if (e.srcElement) target=e.srcElement; else target=e.target; if ((target.nodeName!='IMG')&&(target.nodeName!='INPUT')) document.location='./admin.php?categ=planificateur&sub=manager&act=modif&type_task_id=".$type_tache->id_type."';\" ";
		    print "<tr class='$pair_impair' $tr_javascript style='cursor: pointer' title='".htmlentities($type_tache->comment,ENT_QUOTES,$charset)."' alter='".htmlentities($type_tache->comment,ENT_QUOTES,$charset)."' id='tr".$type_tache->id_type."'><td>".($n_taches?"<img src='images/plus.gif' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_taches(\"".addslashes($type_tache->name)."\"); '/>":"&nbsp;")."</td><td>".htmlentities($type_tache->comment,ENT_QUOTES,$charset)."</td>
			<td>".$n_taches." ".$msg["planificateur_count_tasks"]."</td><td style='text-align:right'><input type='button' value='".$msg["planificateur_task_add"]."' class='bouton_small' onClick='document.location=\"admin.php?categ=planificateur&sub=manager&act=task&type_task_id=".$type_tache->id_type."\"'/></td></tr>\n";
		    
		    print "<tr class='$pair_impair' style='display:none' id='".$type_tache->name."'><td>&nbsp;</td><td colspan='3'><table style='border:1px solid'>";
		    $tasks->get_tasks($type_tache->id_type);
		    print "</table></td></tr>";
		}
		print "</table>";

	}

switch ($act)  {
	case "modif":
		if ($type_task_id) {
			$tasks=new taches();
			print $tasks->show_type_task_form($type_task_id);
		}
		break;
	case "update":
		$tasks=new taches();
		foreach ($tasks->types_taches as $type_tache) {
			if ($type_tache->id_type == $type_task_id) {
				require_once($base_path."/admin/planificateur/".$type_tache->name."/".$type_tache->name.".class.php");
				eval("\$plan=new ".$type_tache->name."();");
				if ($plan) {
					$plan->id_type=$type_task_id;
					$plan->timeout=$timeout;
					$plan->histo_day=$histo_day;
					$plan->histo_number=$histo_number;
					$plan->restart_on_failure=($restart_on_failure ? "1" : "0");
					$plan->alert_mail_on_failure=($alert_mail_on_failure ? "1" : "0").($mail_on_failure ? ",".$mail_on_failure : "");
					$plan->save_global_properties();
				}
			}
		}
		task_list();
		break;
	case "task":
		$tasks=new taches();
		switch ($subaction) {
			case "change":
				print $tasks->show_form_task($planificateur_id);
				break;
			case "save":
				foreach ($tasks->types_taches as $type_tache) {
					if ($type_tache->id_type == $type_task_id) {
						if (is_file($base_path."/admin/planificateur/".$type_tache->name."/".$type_tache->name.".class.php")) {
							require_once($base_path."/admin/planificateur/".$type_tache->name."/".$type_tache->name.".class.php");
							eval("\$plan=new ".$type_tache->name."();");
							$plan->save_property_form($planificateur_id);
						}
					}
				}		
				task_list();
				break;
			default :
				print $tasks->show_form_task($planificateur_id);
		}
		break;
	case "task_del":
		$tasks=new taches();
		print $tasks->del_task($planificateur_id);
		break;
	case "task_duplicate":
		$tasks=new taches();
		print $tasks->show_form_task($planificateur_id);
		break;
	default:
		task_list();
		break;
}