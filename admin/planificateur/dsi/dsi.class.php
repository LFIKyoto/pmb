<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi.class.php,v 1.6 2015-04-03 11:16:29 jpermanne Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");
require_once($class_path."/bannette.class.php");

class dsi extends tache {
	var $liste_bannette;				//liste des bannettes sélectionnées
	var $indice_tableau;				//indice tableau bannette avant traitement 
	
	function dsi($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $dbh;
		
		//paramètres pré-enregistré
		$liste_bannettes = array();
		if ($param['list_bann']) {
			foreach ($param['list_bann'] as $id_bann) {
				$liste_bannettes[$id_bann] = $id_bann;
			}
		}
		if ($param['action']) {
			foreach ($param['action'] as $action) {
				$liste_actions[$action] = $action;
			}
		}
		
		$requete = "select id_bannette, nom_bannette from bannettes where bannette_auto=1 order by nom_bannette";
		$result = pmb_mysql_query($requete,$dbh);
		//size select
		$nb_rows = pmb_mysql_num_rows($result);
		if (($nb_rows > 0) && ($nb_rows < 10)) {
			$size_select = $nb_rows;	
		} elseif ($nb_rows == 0) {
			$size_select = 1;
		} else {
			$size_select = 10;
		}
		
		//Choix de la bannette à diffuser
		$form_task .= "
		<script type='text/javascript'>
		function changeActions(operator) {
			if (operator == 'full') {
				if (document.getElementById('full').checked == true) {
					document.getElementById('flush').checked = false;
					document.getElementById('fill').checked = false;
					document.getElementById('diffuse').checked = false;
				} else {
					if ((document.getElementById('flush').checked == false)
						&& (document.getElementById('fill').checked == false)
						&& (document.getElementById('diffuse').checked == false)
						&& (document.getElementById('export').checked == false)){
							document.getElementById('full').checked = true;
					}
				}
			} else {
				if ((document.getElementById('flush').checked == true)
					|| (document.getElementById('fill').checked == true)
					|| (document.getElementById('diffuse').checked == true)){
						document.getElementById('full').checked = false;
				} else if ((document.getElementById('full').checked == false)
					&& (document.getElementById('flush').checked == false)
					&& (document.getElementById('fill').checked == false)
					&& (document.getElementById('diffuse').checked == false)
					&& (document.getElementById('export').checked == false)){
						document.getElementById(operator).checked = true;
				}
			}
		}
		</script>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_dsi_bannette"]."</label>
			</div>
			<div class='colonne_suite' >
				<input type='radio' name='radio_bannette' value='1' ".((($param['radio_bannette'] == "1") || (!$param['radio_bannette']))  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_all"]."
				<br />
				<input type='radio' name='radio_bannette' value='2' ".(($param['radio_bannette'] == "2")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_public"]."
				<br />
				<input type='radio' name='radio_bannette' value='3' ".(($param['radio_bannette'] == "3")  ? "checked" : "")."/>".$this->msg["planificateur_dsi_bannette_private"]."
				<br />
				<input type='radio' name='radio_bannette' value='4' ".($param['radio_bannette'] == "4" ? "checked" : "")."/>
				<select id='list_bann' style='vertical-align:middle' class='saisie-30em' name='list_bann[]' size='".$size_select."' multiple>";
					while ($row = pmb_mysql_fetch_object($result)) {
							$form_task .= "<option  value='".$row->id_bannette."' ".($liste_bannettes[$row->id_bannette] == $row->id_bannette ? 'selected=\'selected\'' : '' ).">".$row->nom_bannette."</option>";
					}		
		$form_task .="</select>
			</div>
		</div>
		<div class='row' >&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette_options'>".$this->msg["planificateur_dsi_action"]."</label>
			</div>
			<div class='colonne_suite'>
				<input id='full' type='checkbox' name='action[]' value='full' ".($liste_actions['full'] == "full"  ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_full"]."
				<br />
				<input id='flush' type='checkbox' name='action[]' value='flush' ".($liste_actions['flush'] == "flush" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_flush"]."
				<br />
				<input id='fill' type='checkbox' name='action[]' value='fill' ".($liste_actions['fill'] == "fill" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_fill"]."
				<br />
				<input id='diffuse' type='checkbox' name='action[]' value='diffuse' ".($liste_actions['diffuse'] == "diffuse" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_diffuse"]."";
//				<br />
//				<input id='export' type='checkbox' name='action[]' value='export' ".($liste_actions['export'] == "export" ? "checked" : "")." onchange='changeActions(this.value);'/>".$this->msg["task_dsi_export"]."
			$form_task .= "</div>
		</div>";	
			
		return $form_task;
	}
	
	function task_execution() {
		global $dbh,$msg, $PMBusername;
		
		if (SESSrights & DSI_AUTH) {
			$parameters = $this->unserialize_task_params();
			
			if ($parameters["radio_bannette"] == "2") {
				$restrict_sql = " and proprio_bannette = 0";
			} else if ($parameters["radio_bannette"] == "3") {
				$restrict_sql = " and proprio_bannette <> 0";
			} else {
				$restrict_sql = "";
			}
			// requete 
			$requete = "SELECT id_bannette, nom_bannette, proprio_bannette FROM bannettes ";
			$requete .= "WHERE bannette_auto=1 " ;
			$requete .= $restrict_sql;
			$res = pmb_mysql_query($requete, $dbh);
			
			//lister les bannettes sélectionnées en vérifiant qu'elles soient toujours en automatique
			if ($parameters["radio_bannette"] == "4") {
				if ($parameters["list_bann"]) {
					while(($bann=pmb_mysql_fetch_object($res))) {
						foreach ($parameters["list_bann"] as $id_bann) {
							//récupération des bannettes sélectionnées
							if ($bann->id_bannette == $id_bann) {
								$t=array();
								$t["id_bann"] = $id_bann;
								$t["nom_bann"] = $bann->nom_bannette;
								$this->liste_bannette[] = $t;
							}
						}
					}
				}
			} else {
				while(($bann=pmb_mysql_fetch_object($res))) {
					$t=array();
					$t["id_bann"] = $bann->id_bannette;
					$t["nom_bann"] = $bann->nom_bannette;
					$this->liste_bannette[] = $t;
				}
			}
			pmb_mysql_free_result($res);
	
			$this->report[] = "<tr><th>".$this->msg["dsi_report_header"]."</th></tr>";
			if ($this->liste_bannette) {
				//liste des actions à réaliser
				if ($parameters["action"]) {
					$lst_actions=array();
					foreach ($parameters["action"] as $act) {
						$lst_actions[$act] = $act;	
					}
	
					$percent = 0;
					//progression en fn de : nbre bannettes & nbre actions
					$p_value = (int) 100/(count($this->liste_bannette)*count($lst_actions));
	
					$this->indice_tableau = 0; 
					foreach($this->liste_bannette as $bann) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
						
						if($this->statut == WAITING) {
							$this->send_command(RUNNING);
						}
						if($this->statut == RUNNING) {
							$this->report[] = "<tr><th>".$this->msg["dsi_report_action"]." : ".$bann["nom_bann"]."</th></tr>";
							foreach ($lst_actions as $action) {
								$this->report[] = "<tr><td>";
								switch ($action) {
									case 'full' :
										if (method_exists($this->proxy, 'pmbesDSI_diffuseBannetteFullAuto')) {
 											// On diffuse en fonction de la périodicité
 											$requete = "SELECT periodicite FROM bannettes WHERE id_bannette=".$bann["id_bann"];
 											$res = pmb_mysql_query($requete, $dbh);
 											$periodicite = 0;
 											if ($res) $periodicite = pmb_mysql_result($res, 0,"periodicite");
//  										if (!$periodicite) $periodicite = 1; //Limiter à 1 fois par jour minimum
 											$requete = "SELECT count(*) as diffuse FROM bannettes WHERE id_bannette=".$bann["id_bann"]." AND (DATE_ADD(date_last_envoi, INTERVAL ".$periodicite." DAY) <= sysdate())";
 											$res = pmb_mysql_query($requete, $dbh);
 											if ($res) {
 												if (pmb_mysql_result($res, 0,"diffuse")) {
													$this->report[] = $this->proxy->pmbesDSI_diffuseBannetteFullAuto($bann["id_bann"]);
 												} else {
 													$this->report[] = "<tr><td>".sprintf($this->msg["dsi_no_diffusable"],$periodicite)."</td></tr>";
 												}
 											}
											$percent += $p_value;
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"diffuseBannetteFullAuto","pmbesDSI",$PMBusername)."</td></tr>";
										}
										break;
									case 'flush' :
										if (method_exists($this->proxy, 'pmbesDSI_flushBannette')) {
											$this->report[] = $this->proxy->pmbesDSI_flushBannette($bann["id_bann"]);									
											$percent += $p_value;
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"flushBannette","pmbesDSI",$PMBusername)."</td></tr>";
										}
										break;
									case 'fill' :
										if (method_exists($this->proxy, 'pmbesDSI_fillBannette')) {
											$this->report[] = $this->proxy->pmbesDSI_fillBannette($bann["id_bann"]);
											$percent += $p_value;
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"fillBannette","pmbesDSI",$PMBusername)."</td></tr>";
										}
										break;
									case 'diffuse' :
										if (method_exists($this->proxy, 'pmbesDSI_diffuseBannette')) {
											// On diffuse en fonction de la périodicité
											$requete = "SELECT periodicite FROM bannettes WHERE id_bannette=".$bann["id_bann"];
											$res = pmb_mysql_query($requete, $dbh);
											$periodicite = 0;
											if ($res) $periodicite = pmb_mysql_result($res, 0,"periodicite");
// 											if (!$periodicite) $periodicite = 1; //Limiter à 1 fois par jour minimum
											$requete = "SELECT count(*) as diffuse FROM bannettes WHERE id_bannette=".$bann["id_bann"]." AND (DATE_ADD(date_last_envoi, INTERVAL ".$periodicite." DAY) <= sysdate())";
											$res = pmb_mysql_query($requete, $dbh);
											if ($res) {
												if (pmb_mysql_result($res, 0,"diffuse")) {
													$this->report[] = $this->proxy->pmbesDSI_diffuseBannette($bann["id_bann"]);
												} else {
													$this->report[] = "<tr><td>".sprintf($this->msg["dsi_no_diffusable"],$periodicite)."</td></tr>";
												}
											}
											$percent += $p_value;
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"diffuseBannette","pmbesDSI", $PMBusername)."</td></tr>";
										}
										break;
//									case 'export' :
//										$this->report[] = "<strong>".$this->msg['dsi_diff_export'].": ".$bann["id_bann"]."</strong><br />" ;
//										$object_fpdf = $this->proxy->pmbesDSI_exportBannette($id_bann);
//										//génération d'un pdf
//										$create_success = $this->generate_docnum($object_fpdf);
//										if (!$create_success) {
//											$this->statut = FAILED;
//										}
//										break;
								}
								$this->report[] = "</td></tr>";
								$this->update_progression($percent);
								$this->indice_tableau++;
							}
						}
					}
				} else {
					$this->report[] = "<tr><td>".$this->msg["dsi_action_unknown"]."</td></tr>";
				}
			} else {
				$this->report[] = "<tr><td>".$this->msg["dsi_bannette_unknown"]."</td></tr>";
			}
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
	}
	
	function traite_commande($cmd,$message) {		
		switch ($cmd) {
			case RESUME:
				$this->send_command(WAITING);
				break;
			case SUSPEND:
				$this->suspend_dsi();
				break;
			case STOP:
				$this->stop_dsi();
				$this->finalize();
				die();
				break;
			case FAIL:
				$this->stop_dsi();
				$this->finalize();
				die();
				break;
		}
	}
    
	function show_report($task_rapport) {
		global $charset;
		
		if ($task_rapport != "") {
			$report_execution = "<table>";
			foreach ($task_rapport as $ligne) {
				$report_execution .= html_entity_decode($ligne, ENT_QUOTES, $charset);	
			}
			$report_execution .= "</table>";
		}

		return $report_execution;
	}
	function make_serialized_task_params() {
    	global $list_bann, $radio_bannette, $action;
		$t = parent::make_serialized_task_params();
		
		if ($radio_bannette) {
			$t["radio_bannette"]=$radio_bannette;
			//liste de bannettes sélectionnées dans le cas où on choisi..
			if ($radio_bannette == "4") {
				if ($list_bann) {
					foreach ($list_bann as $id_bann) {
						$t["list_bann"][$id_bann]=stripslashes($id_bann);			
					}
				}
			}
		}
		if ($action) {
			foreach ($action as $act) {
				$t["action"][$act]=$act;			
			}
		}

    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }

	function suspend_dsi() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
	
	/*Récupère les bannettes non traitées*/
	function stop_dsi() {
		$this->report[] = "<tr><th>".$this->msg["dsi_stopped"]."</th></tr>";
		$chaine = "<tr><td>".$this->msg["dsi_no_proceed"]." : <br />";
		for($i=$this->indice_tableau; $i <= count($this->liste_bannette); $i++) {
			$chaine .= $this->liste_bannette[$i]["nom_bann"]."<br />";
		}
		$chaine .= "</td></tr>";
		$this->report[] = $chaine;
	}
}