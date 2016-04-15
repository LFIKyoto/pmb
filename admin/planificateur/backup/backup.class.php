<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: backup.class.php,v 1.4 2015-04-03 11:16:27 jpermanne Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");

class backup extends tache {
	var $liste_sauvegarde=array();		//liste des jeux de sauvegarde sélectionnées
	var $indice_tableau;				//indice tableau jeu de sauvegarde avant traitement
	var $log_ids=array();				//les jeux de sauvegarde réalisés en cas d'annulation.. 
	
	function backup($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
		
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		
		//paramètres pré-enregistré
		$value_param = array();
		if ($param['form_jeu_sauv']) {
			foreach ($param['form_jeu_sauv'] as $jeu_sauvegarde) {
				$value_param[$jeu_sauvegarde] = $jeu_sauvegarde;
			}
		}
		
		$requete = "select sauv_sauvegarde_id, sauv_sauvegarde_nom from sauv_sauvegardes";
		$result = pmb_mysql_query($requete);
		$nb_rows = pmb_mysql_num_rows($result);
		//taille du selecteur
		if ($nb_rows < 3) $nb=3;
		else if ($nb_rows > 10) $nb=10;
		else $nb = $nb_rows;
			
		//Choix du ou des jeux de sauvegardes
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='jeu_sauv'>".$this->msg["planificateur_backup_choice"]."</label>
			</div>
			<div class='colonne_suite'>
				<select id='form_jeu_sauv' class='saisie-50em' name='form_jeu_sauv[]' size='".$size_select."' multiple>";
					while ($row = pmb_mysql_fetch_object($result)) {
							$form_task .= "<option  value='".$row->sauv_sauvegarde_id."' ".($value_param[$row->sauv_sauvegarde_id] == $row->sauv_sauvegarde_id ? 'selected=\'selected\'' : '' ).">".$row->sauv_sauvegarde_nom."</option>";
					}
		$form_task .="</select>";
		$form_task .= "</div></div>";		
			
		return $form_task;
	}
	
	function task_execution() {
		global $dbh, $msg, $PMBusername;
		
		if (SESSrights & SAUV_AUTH) {
			$parameters = $this->unserialize_task_params();

			// récupérer les jeux de sauvegarde
			$this->report[] = "<tr><th>".$this->msg["sauv_sets"]."</th></tr>";
			if (method_exists($this->proxy, 'pmbesBackup_listSetBackup')) {
				$result = $this->proxy->pmbesBackup_listSetBackup();
				//lister les sauvegardes sélectionnées en vérifiant qu'elles soient toujours présentes dans PMB
				if ($result) {
					foreach ($result as $aresult) {
						foreach ($parameters["form_jeu_sauv"] as $id_lst) {
							//récupération des sauvegardes sélectionnées
							if ($aresult["sauv_sauvegarde_id"] == $id_lst) {
								$t=array();
								$t["id_sauv"] = $id_lst;
								$t["nom_sauv"] = $aresult["sauv_sauvegarde_nom"];
								$this->liste_sauvegarde[] = $t;
							}
						}
					}
				}
				if ($this->liste_sauvegarde) {
					$percent = 0;
					$p_value = (int) 100/count($this->liste_sauvegarde);
					$this->indice_tableau = 0;
					foreach($this->liste_sauvegarde as $sauvegarde) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
						
						if($this->statut == WAITING) {
							$this->send_command(RUNNING);
						}
						if($this->statut == RUNNING) {
							//lancement de la sauvegarde
							$this->report[] = "<tr><th>".$this->msg["sauv_launch"]." : ".$sauvegarde["nom_sauv"]."</th></tr>";
							if (method_exists($this->proxy, 'pmbesBackup_launchBackup')) {
								$result_save = $this->proxy->pmbesBackup_launchBackup($sauvegarde["id_sauv"]);
								$this->report[] = $result_save["report"];
								$this->log_ids[] = $result_save["logid"];
								//mise à jour de la progression
								$percent += $p_value;
								$this->update_progression($percent);
								$this->indice_tableau++;
							} else {
								$this->report[] = sprintf($msg["planificateur_function_rights"],"launchBackup","pmbesBackup",$PMBusername);
							}
						}
					}
				} else {
					$this->report[] = "<tr><td>".$this->msg["sauv_unknown_sets"]."</td></tr>";
				}
			} else {
				$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"listSetBackup","pmbesBackup",$PMBusername)."</td></tr>";
			}
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
	}
	
	function traite_commande($cmd,$message) {
		
		switch ($cmd) {
			case RESUME :
				$this->send_command(WAITING);
				break;
			case SUSPEND :
				$this->suspend_backup();
				break;
			case STOP :
				$this->stop_backup();
				$this->finalize();
				die();
				break;
			case ABORT :
				$this->abort_backup();
				$this->finalize();
				die();
				break;
			case FAIL :
				$this->stop_backup();
				$this->finalize();
				die();
				break;
		}
	}
	
	function make_serialized_task_params() {
    	global $form_jeu_sauv;
		$t = parent::make_serialized_task_params();
		if ($form_jeu_sauv) {
			foreach ($form_jeu_sauv as $jeu_sauvegarde) {
				$t["form_jeu_sauv"][$jeu_sauvegarde]=stripslashes($jeu_sauvegarde);			
			}
		}

    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }
    
	function suspend_backup() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
	
	/*Récupère les jeux de sauvegarde non traitées*/
	function stop_backup() {
		$this->report[] = "<tr><th>".$this->msg["backup_stopped"]."</th></tr>";
		$chaine = "<tr><td>".$this->msg["backup_no_proceed"]." : <br />";
		for($i=$this->indice_tableau; $i <= count($this->liste_sauvegarde); $i++) {
			$chaine .= $this->liste_sauvegarde[$i]["nom_sauv"]."<br />";
		}
		$chaine .= "</td></tr>";
		$this->report[] = $chaine;
	}
	
	/*Récupère les jeux de sauvegarde traitées*/
	function abort_backup() {
		global $msg;

		$this->report[] = "<tr><th>".$this->msg["backup_abort"]."</th></tr>";
		if(method_exists($this->proxy, "pmbesBackup_deleteSauvPerformed")) {
			$chaine .= "<tr><td>";
			for($i=0; $i < $this->indice_tableau; $i++) {
				if ($this->log_ids[$i] != "") {
					$succeed = $this->proxy->pmbesBackup_deleteSauvPerformed($this->log_ids[$i]);
					if ($succeed) {
						$chaine .= $this->msg["backup_delete"]." : ".$this->liste_sauvegarde[$i]["nom_sauv"]."<br />";
					} else {
						$chaine .= $this->msg["backup_delete_error"]." : ".$this->liste_sauvegarde[$i]["nom_sauv"]."<br />";;
					}
				}
			}
			$chaine .= "</td></tr>";
			$this->report[] = $chaine;
		} else {
			$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"deleteSauvPerformed","pmbesBackup")."</td></tr>";
		}
	}
}