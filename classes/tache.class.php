<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tache.class.php,v 1.9.4.2 2015-09-24 09:12:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/parser.inc.php");
require_once($include_path."/templates/taches.tpl.php");
require_once($include_path."/connecteurs_out_common.inc.php");
require_once($class_path."/tache_calendar.class.php");
require_once($class_path."/tache_docnum.class.php");
require_once("$class_path/progress_bar_tache.class.php");
require_once($class_path."/upload_folder.class.php");
require_once($class_path."/xml_dom.class.php");

//commands
define('RESUME','1');
define('SUSPEND','2');
define('STOP','3');
define('RETRY','4');
define('ABORT','5');
define('FAIL','6');

//status
define('WAITING','1');
define('RUNNING','2');
define('ENDED','3');
define('SUSPENDED','4');
define('STOPPED','5');
define('FAILED','6');
define('ABORTED','7');
		
class tache {
	var $id_type=0;						// identifiant du type de tâche
	var $name='';						// nom du type de tâche
	var $comment;						// commentaire sur le type de tâche
	var $states;						// listing des états 
	var $commands;						// listing des commandes
	var $dir_upload_boolean;			// La tâche a-t-elle besoin d'un répertoire d'upload?
	var $msg;							// Messages propres au type de tâche
	var $parameters="";					// paramètres 
	var $timeout;						// Temps limite d'exécution
	var $histo_day;						// Historique de conservation en jour 
	var $histo_number;					// Historique de conservation en nombre
	var $restart_on_failure=0;			// Replanifier la tâche automatiquement en cas d'échec
	var $alert_mail_on_failure=0;		// Alerter par mail en cas d'échec ?
	var $mail_on_failure='';			// Adresses mails destinataires
	var $proxy;							// classe contenant les méthodes de l'API
	var $id_tache=0;					//identifiant de la tâche
	var $report=array();				// rapport de la tâche
	var $statut;
	
	function tache($atache) {
		global $base_path, $msg, $sub;
		
		$this->id_type = $atache["ID"];
		$this->name = $atache["NAME"];
		if(strstr($atache["COMMENT"],"msg:"))
			$this->comment = $msg[str_replace("msg:", "", $atache["COMMENT"])];
		else 
			$this->comment = $atache["COMMENT"];
		if (!$this->id_type || !$this->name)
			return false;
			
		$tache_path = $base_path."/admin/planificateur/".$this->name;
		$this->get_messages($tache_path);
		
		//fichier de commandes
		$xml_commands=file_get_contents($base_path."/admin/planificateur/workflow.xml");
		$xml_dom_commands = new xml_dom($xml_commands);
		
		$filename = $tache_path."/manifest.xml";
		//fichier manifest spécifique
		$xml_manifest=file_get_contents($filename);
		$xml_dom_manifest = new xml_dom($xml_manifest);
			
		$this->states = $this->parse_states($xml_dom_commands, $xml_dom_manifest);
		$this->commands = $this->parse_commands($xml_dom_commands, $xml_dom_manifest);
		$this->dir_upload_boolean = $this->parse_dir_upload($xml_dom_manifest);
	}
	
	function get_id_type() {
		return $this->id_type;
	}
	
	//messages 
	function get_messages($tache_path) {
		global $lang;
		
		if (file_exists($tache_path."/messages/".$lang.".xml")) {
			$file_name=$tache_path."/messages/".$lang.".xml";
		} else if (file_exists($tache_path."/messages/fr_FR.xml")) {
			$file_name=$tache_path."/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$this->msg=$xmllist->table;
		}
	}
	
	// listing des états
	function parse_states($xml_dom_commands, $xml_dom_manifest) {
		global $base_path;

		$nodes_nostates_manifest = $xml_dom_manifest->get_nodes("manifest/capacities/nostates/state");
		$nodes_states_manifest = $xml_dom_manifest->get_nodes("manifest/capacities/states/state");
		
		$nodes_states = $xml_dom_commands->get_nodes("workflow/states/state");		
		foreach ($nodes_states as $id=>$node_state) {
			$t=array();
			$state_impossible = false;
			if ($nodes_nostates_manifest) {
				foreach ($nodes_nostates_manifest as $node_nostate_manifest) {
					if (($xml_dom_manifest->get_attribute($node_nostate_manifest, "name")) == ($xml_dom_commands->get_attribute($node_state,"name"))){
						$state_impossible = true;
					}
				}
			}
			//etat possible
			if (!$state_impossible) {
				$t["id"] = $xml_dom_commands->get_attribute($node_state,"id");
				$t["name"] = $xml_dom_commands->get_attribute($node_state,"name");
				$nodes_next_states = $xml_dom_commands->get_nodes("workflow/states/state[$id]/nextState");
				$t2 = array();
				if ($nodes_next_states) {
					foreach ($nodes_next_states as $index=>$node_next_state) {
						$command_impossible = false;
						if ($nodes_states_manifest) {
							foreach ($nodes_states_manifest as $k=>$node_state_manifest) {
								if (($xml_dom_manifest->get_attribute($node_state_manifest, "name")) == ($xml_dom_commands->get_attribute($node_state,"name"))){
									$nodes_nocommands_manifest = $xml_dom_manifest->get_nodes("manifest/capacities/states/state[$k]/nocommand");
									if ($nodes_nocommands_manifest) {
										foreach ($nodes_nocommands_manifest as $node_nocommand_manifest) {
											if (($xml_dom_manifest->get_attribute($node_nocommand_manifest, "commands")) == ($xml_dom_commands->get_attribute($node_next_state,"commands"))){
												$command_impossible = true;
											}
										}
									}
								}
							}
						}
						if (!$command_impossible) {
							$t2[$index]["command"] = $xml_dom_commands->get_attribute($node_next_state,"commands");
							$t2[$index]["dontsend"] = $xml_dom_commands->get_attribute($node_next_state,"dontsend");
							$t2[$index]["value"] = $xml_dom_commands->get_value("workflow/states/state[$id]/nextState[$index]");
							$value = $index;
						}
					}
				}
				if ($nodes_states_manifest) {
					foreach ($nodes_states_manifest as $k=>$node_state_manifest) {
						if (($xml_dom_manifest->get_attribute($node_state_manifest, "name")) == ($xml_dom_commands->get_attribute($node_state,"name"))){
							$nodes_add_commands_manifest = $xml_dom_manifest->get_nodes("manifest/capacities/states/state[$k]/nextState");
							if ($nodes_add_commands_manifest) {
								foreach ($nodes_add_commands_manifest as $node_add_command_manifest) {
									//ajout des nouvelles commandes
									$value++;
									$t2[$value]["command"] = $xml_dom_manifest->get_attribute($node_add_command_manifest, "commands");
									$t2[$value]["dontsend"] = $xml_dom_manifest->get_attribute($node_add_command_manifest,"dontsend");
									$t2[$value]["value"] = $xml_dom_manifest->get_value("manifest/capacities/states/state[$k]/nextState");
								}
							}
						}
					}
				}
				$t["nextState"] = $t2;
				$tab_states[$t["name"]]=$t;
			}
		}
		return $tab_states;
	}
	
	// listing des commandes
	function parse_commands($xml_dom_commands, $xml_dom_manifest) {
		global $base_path, $msg, $lang;
		
		$nodes_commands = $xml_dom_commands->get_nodes("workflow/commands/command");
		if ($nodes_commands) {
			foreach ($nodes_commands as $id=>$node_command) {
				$t=array();
				$t["id"] = $xml_dom_commands->get_attribute($node_command,"id");
				$t["name"] = $xml_dom_commands->get_attribute($node_command,"name");
				$t["label"] = $msg[str_replace("msg:", "", $xml_dom_commands->get_attribute($node_command,"label"))];
				
				$tab_commands[$t["name"]]=$t;
			}
		}
		
		$nodes_commands_manifest = $xml_dom_manifest->get_nodes("manifest/capacities/commands/command");
		if ($nodes_commands_manifest) {
			foreach ($nodes_commands_manifest as $id=>$node_command_manifest) {
				$t=array();
				$t["id"] = $xml_dom_manifest->get_attribute($node_command_manifest,"id");
				$t["name"] = $xml_dom_manifest->get_attribute($node_command_manifest,"name");
				$t["label"] = $this->msg[str_replace("msg:", "", $xml_dom_manifest->get_attribute($node_command_manifest,"label"))];
				
				$tab_commands[$t["name"]]=$t;
			}
		}
		return $tab_commands;
	}
	
	// Est-ce une tâche qui demande un répertoire d'upload pour des fichiers générés??
	function parse_dir_upload($xml_dom_manifest) {
		$node_directory = $xml_dom_manifest->get_node("manifest/directory_upload");
		if ($node_directory) {
			return $xml_dom_manifest->get_value("manifest/directory_upload");
		} else {
			return "0";
		}
	}
	
	function setEsProxy($proxy) {
		$this->proxy = $proxy;
	}
	
	function listen_commande($methode_callback) {
		global $dbh;
		
		$query_commande = "select status, commande, next_state from taches where id_tache=".$this->id_tache;
		$result = pmb_mysql_query($query_commande, $dbh);
		
		if (pmb_mysql_result($result,0,"commande") != '0') {
			$cmd = pmb_mysql_result($result,0,"commande");			
			$requete = "update taches set status=".pmb_mysql_result($result,0, "next_state").", commande=0, next_state=0 where id_tache=".$this->id_tache."";
			$res = pmb_mysql_query($requete, $dbh);
			if ($res) {
				$this->statut = pmb_mysql_result($result,0, "next_state");
				call_user_func($methode_callback,$cmd);	
			}
		}
	}
	
	// Envoi d'une commande par la tache, changement du statut de la tâche...
	function send_command($state=''){
		global $dbh;
		
		if ($state != '') {
			$this->statut = $state;
			pmb_mysql_query("update taches set status=".$this->statut." where id_tache='".$this->id_tache."'", $dbh);
		}
//		return $cmd;
	}
	
	function make_serialized_task_params() {
		global $timeout, $histo_day, $histo_number, $restart_on_failure, $alert_mail_on_failure, $mail_on_failure;

		$t["timeout"] = ($timeout != "0" ? $timeout : "");
		$t["histo_day"] = ($histo_day != "0" ? $histo_day : "");
		$t["histo_number"] = ($histo_number != "0" ? $histo_number : "");
	 	$t["restart_on_failure"] = ($restart_on_failure ? "1" : "0");
	 	$t["alert_mail_on_failure"] = $alert_mail_on_failure.($mail_on_failure ? ",".$mail_on_failure : "");

		return $t;
	}
    
	//Sauvegarde des propriétés générales
	function save_global_properties() {
		global $dbh;

		$requete="replace into taches_type (id_type_tache,parameters, timeout, histo_day, histo_number, restart_on_failure, alert_mail_on_failure) values('".$this->get_id_type()."',
		'".serialize($this->parameters)."','".$this->timeout."','".$this->histo_day."','".$this->histo_number."','".$this->restart_on_failure."','".$this->alert_mail_on_failure."')";

		return pmb_mysql_query($requete, $dbh);
	}
	
	//sauvegarde des données du formulaire, 
	function save_property_form($planificateur_id) {
		global $dbh, $type_task_id, $charset;
		global $task_name, $task_desc,$form_users, $task_active;
		global $id_rep, $path;
		global $task_perio_heure, $task_perio_min, $chkbx_task_quotidien, $chkbx_task_hebdo, $chkbx_task_mensuel;
		
		$params = $this->make_serialized_task_params();

		$task_perio_heure = ($task_perio_heure == '') ? '*' : $task_perio_heure;
		$task_perio_minute = ($task_perio_min == '') ? '*' : $task_perio_min;
		
		//concaténation de la periodicité des jours du mois
		$task_perio_quotidien = "";
		if ($chkbx_task_quotidien[0] == '*') {
			$task_perio_quotidien.= $chkbx_task_quotidien[0].",";
		} else {
			for ($i=0; $i<sizeof($chkbx_task_quotidien); $i++) {
				$task_perio_quotidien.= $chkbx_task_quotidien[$i].",";
			}
		}
		$task_perio_quotidien = ($task_perio_quotidien != '' ? substr($task_perio_quotidien,0,strlen($task_perio_quotidien)-1) : '*');
		
		//concaténation de la periodicité des jours de la semaine
		$task_perio_hebdo = "";
		if ($chkbx_task_hebdo[0] == '*') {
			$task_perio_hebdo.= $chkbx_task_hebdo[0].",";
		} else {	
			for ($i=0; $i<sizeof($chkbx_task_hebdo); $i++) {
				$task_perio_hebdo.= $chkbx_task_hebdo[$i].",";
			}	
		}
		$task_perio_hebdo = ($task_perio_hebdo != '' ? substr($task_perio_hebdo,0,strlen($task_perio_hebdo)-1) : '*');
		
		//concaténation de la periodicité des mois
		$task_perio_mensuel = "";
		if ($chkbx_task_mensuel[0] == '*') {
			$task_perio_mensuel.= $chkbx_task_mensuel[0].",";
		} else {
			for ($i=0; $i<sizeof($chkbx_task_mensuel); $i++) {
				$task_perio_mensuel.= $chkbx_task_mensuel[$i].",";
			}
		}
		$task_perio_mensuel = ($task_perio_mensuel != '' ? substr($task_perio_mensuel,0,strlen($task_perio_mensuel)-1) : '*');
		
		if ($id_rep && $path) {
			$up = new upload_folder($id_rep);
			$path = stripslashes($path);
			$path_name = $up->formate_path_to_save($up->formate_path_to_nom($path));	
		} else {
			$id_rep="";
			$path_name = "";	
		}
		
		// est-ce une nouvelle tâche ??
		if ($planificateur_id == '') {
			//Nouvelle planification
			$requete="insert into planificateur (num_type_tache, libelle_tache, desc_tache, num_user, param, statut, rep_upload, path_upload, perio_heure, 
				perio_minute, perio_jour_mois, perio_jour, perio_mois) 
				values(".$type_task_id.",'".addslashes($task_name)."','".addslashes($task_desc)."',
				'".$form_users."','".addslashes($params)."','".$task_active."','".$id_rep."','".$path_name."','".$task_perio_heure."','".$task_perio_minute."',
				'".htmlentities($task_perio_quotidien, ENT_QUOTES,$charset)."','".htmlentities($task_perio_hebdo, ENT_QUOTES,$charset)."','".htmlentities($task_perio_mensuel, ENT_QUOTES,$charset)."')";
			pmb_mysql_query($requete, $dbh);
			$planificateur_id = pmb_mysql_insert_id();
		} else {
			//Mise à jour des informations
			$requete="update planificateur
				set num_type_tache = '".$type_task_id."',
				libelle_tache = '".addslashes($task_name)."',
				desc_tache = '".addslashes($task_desc)."',
				num_user = '".$form_users."',
				param = '".addslashes($params)."',
				statut = '".$task_active."',
				rep_upload = '".$id_rep."',
				path_upload = '".$path_name."',
				perio_heure = '".$task_perio_heure."', 
				perio_minute = '".$task_perio_minute."',
				perio_jour_mois = '".htmlentities($task_perio_quotidien, ENT_QUOTES,$charset)."',
				perio_jour = '".htmlentities($task_perio_hebdo, ENT_QUOTES,$charset)."',
				perio_mois = '".htmlentities($task_perio_mensuel, ENT_QUOTES,$charset)."'
				where id_planificateur='".$planificateur_id."'";
			pmb_mysql_query($requete, $dbh);
		}
		
		//calcul de la prochaine exécution
		$this->calcul_execution($planificateur_id);
		//Vérification des paramètres enregistrés
		$this->checkParams($planificateur_id);
		// insertion d'une nouvelle tâche si aucune n'est planifiée
		$this->insertOfTask($planificateur_id, $task_active);
	}
	
	/* Calcul prochaine execution */
	function calcul_execution($id_planificateur) {
		global $dbh;
		
		if ($id_planificateur) {
			$call_calendar = new tache_calendar($id_planificateur);
			$jour = $call_calendar->new_date["JOUR"];
			$mois = $call_calendar->new_date["MOIS"];
			$annee = $call_calendar->new_date["ANNEE"];
			$heure = $call_calendar->new_date["HEURE"];
			$minute = $call_calendar->new_date["MINUTE"];
			if ($jour != "00") {
				$date_exec = $annee."-".$mois."-".$jour;
				$heure_exec = $heure.":".$minute;
			} else {
				$date_exec = "0000-00-00";
				$heure_exec = "00:00";
			}
		} else {
			$date_exec = "0000-00-00";
			$heure_exec = "00:00";
		}
		//mise à jour de la prochaine planification
		$requete = "update planificateur set calc_next_heure_deb='".$heure_exec."', calc_next_date_deb='".$date_exec."' 
		where id_planificateur=".$id_planificateur;
		pmb_mysql_query($requete, $dbh);
	}
	
	function insertOfTask($num_planificateur, $active ='') {
		global $dbh;

		if ($active == '') {
			//statut de la tâche
			$query_state = "select statut from planificateur where id_planificateur=".$num_planificateur;
			$result_query_state = pmb_mysql_query($query_state, $dbh);
			if (pmb_mysql_num_rows($result_query_state) > 0) {
				$active = pmb_mysql_result($result_query_state,0, "statut");				
			}
		}
		// on recherche si cette planification possède une tâche en attente ou en cours d'exécution...
		$query = "select t.id_tache, t.num_planificateur, p.statut 
			from taches t, planificateur p  
			where t.num_planificateur=p.id_planificateur 
			and t.end_at='0000-00-00 00:00:00' and t.num_planificateur=".$num_planificateur;
		$result_query = pmb_mysql_query($query, $dbh);

		// nouvelle planification && planification activée
		if ((pmb_mysql_num_rows($result_query) == 0) && ($active == '1')) {
			$insertok=false;
			$cmpt=0;//Pour éviter une boucle infini... au cas où
			while ((!$insertok) && ($cmpt < 10000)){//MB: Comme des tâches peuvent être executées en parallèle et que id_tache est unique on peut tenter d'insérer avec le même id
				$cmpt++;
				//valeur maximale d'identifiant de tâche
				$reqMaxId = pmb_mysql_query("select max(id_tache) as maxId from taches",$dbh);
				$rowMaxId = pmb_mysql_fetch_row($reqMaxId);
				$id_tache = $rowMaxId[0] + 1;
				
				//insertion de la tâche planifiée
				$requete="insert into taches (id_tache, num_planificateur, status, commande, indicat_progress,id_process)
					values(".$id_tache.",'".$num_planificateur."',1,0,0,0)";
				$insertok = pmb_mysql_query($requete, $dbh);
			}
		// modification planification && planification désactivée
		} else if ((pmb_mysql_num_rows($result_query) == 1) && ($active == '0')) {
			//il faut vérifier que la tâche ne soit pas déjà planifiée, si oui on la supprime	
			if (pmb_mysql_num_rows($result_query) >= 1) {
				$requete="delete from taches where start_at='0000-00-00 00:00:00' and num_planificateur='".$num_planificateur."'";
				pmb_mysql_query($requete, $dbh);
			}
		}
	}
	
	function get_report_datas($id_tache) {
		global $dbh;
		
		$sql = "SELECT t.id_tache, p.num_type_tache, p.libelle_tache, t.start_at, t.end_at, t.status, t.indicat_progress, t.rapport FROM taches t,planificateur p 
				Where t.num_planificateur = p.id_planificateur
				And t.id_tache=".$id_tache."
				order by p.calc_next_date_deb DESC
				";
		$res=pmb_mysql_query($sql, $dbh);

		if (pmb_mysql_num_rows($res)) {
				$r = pmb_mysql_fetch_object($res);
				$task["id_tache"]=$r->id_tache;
				$task["num_planificateur"]=$r->num_planificateur;
				$task["libelle_tache"]=$r->libelle_tache;
				$task["start_at"]= explode (" ",$r->start_at);
				$task["end_at"]= explode (" ",$r->end_at);
				$task["status"] = $r->status;
				$task["indicat_progress"] = $r->indicat_progress;
				$task["rapport"] = unserialize(htmlspecialchars_decode($r->rapport, ENT_QUOTES));
		} else {
			$task["id_tache"]="";
			$task["num_planificateur"]="";
			$task["libelle_tache"]="";
			$task["start_at"]="";
			$task["end_at"]="";
			$task["status"] = "";
			$task["indicat_progress"] = "";
			$task["rapport"] = "";
		}
		return $task;
	}
	
	function fetch_default_global_values() {
		$this->parameters="";
		$this->timeout=5;
		$this->histo_day=7;
		$this->histo_number=3;
		$this->restart_on_failure=0;
		$this->alert_mail_on_failure=0;
	}
	
	//Propriétes globales d'un type de tache du planificateur (timeout, histo_day, ...)
	function fetch_global_properties() {
		global $dbh;
		global $type_task_id;

		$requete="select parameters, timeout, histo_day, histo_number, restart_on_failure, alert_mail_on_failure from taches_type where id_type_tache='".$type_task_id."'";
		$resultat=pmb_mysql_query($requete, $dbh);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$this->parameters=unserialize($r->parameters);
			$this->timeout=$r->timeout;
			$this->histo_day=$r->histo_day;
			$this->histo_number=$r->histo_number;
			$this->restart_on_failure=$r->restart_on_failure;
			$this->alert_mail_on_failure=$r->alert_mail_on_failure;
		} else {
			$this->fetch_default_global_values();
		}
	}
	
	/*
	 * Exécution de la tâche - Méthode appelée par la classe spécifique
	 * Modification des données de la base
	 */
	function execute() {
		global $dbh,$charset;
			 
		//initialisation de la tâche planifiée sur la base
		$this->initialize();
		//appel de la méthode spécifique
		$this->task_execution();
		//finalisation de la tâche planifiée sur la base
		$this->finalize();

		$result_success = pmb_mysql_query("select num_planificateur from taches where id_tache=".$this->id_tache);
		//mise à jour de la prochaine exec
		if (pmb_mysql_num_rows($result_success) == 1) {
			//planification d'une nouvelle tâche
			$this->calcul_execution(pmb_mysql_result($result_success,0,"num_planificateur"));
			$this->insertOfTask(pmb_mysql_result($result_success,0,"num_planificateur"));
		}
	}
	
	//appelée si show_report non existant classe spécifique fille
	function show_report($task_rapport) {
		global $charset;
		
		if ($task_rapport != "") {
			$report_execution = "<table>";
			foreach ($task_rapport as $ligne) {
				if (is_array($ligne)) {
					foreach ($ligne as $une_ligne) {
						$report_execution .= html_entity_decode($une_ligne, ENT_QUOTES, $charset)."<br />";
					}
				} else {
					$report_execution .= html_entity_decode($ligne, ENT_QUOTES, $charset);	
				}
			}
			$report_execution .= "</table>";
		}

		return $report_execution;
	}
	
	//vérification de deux paramètres génériques (historique, nb exécution conservées)
	function checkParams($id_planificateur) {
		global $dbh;
		
		$requete = "select param from planificateur where id_planificateur=".$id_planificateur;

		$resultat=pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($resultat) > 0) {
			$r=pmb_mysql_fetch_object($resultat);
			$params=unserialize($r->param);
			if ($params) {
				foreach ($params as $index=>$param) {
					if (($index == "histo_day") && ($param != "") && ($param !="0")) {
						$requete_suppr = "delete from taches where num_planificateur ='".$id_planificateur."'
							and end_at < DATE_SUB(curdate(), INTERVAL ".$param." DAY)
							and end_at != '0000-00-00 00:00:00'";
	
						pmb_mysql_query($requete_suppr, $dbh);
					}
					if (($index == "histo_number") && ($param != "") && ($param !="0")) {
						//check nbre exécution
						$requete_select = "select count(*) as nbre from taches where num_planificateur =".$id_planificateur."
								and end_at != '0000-00-00 00:00:00'";
						$result = pmb_mysql_query($requete_select, $dbh);
						$nb = pmb_mysql_result($result, 0,"nbre");
	
						if ($nb > $param) {
							$nb_r = $nb - $param;
							$query = "delete from taches 
								where num_planificateur=".$id_planificateur."
								and end_at != '0000-00-00 00:00:00'
								order by end_at ASC 
								limit ".$nb_r;
							pmb_mysql_query($query, $dbh);
							
							// il faut aussi effacer les documents numériques...
							//en base...
							$query_del_docnum = "delete from taches_docnum where num_tache not in (select id_tache from taches)";
							pmb_mysql_query($query_del_docnum);
						}							
					}
				}
			}
		}
	}
	
	//recherche les informations de la tâche planifiée si elles est existante, dans le cas d'une modif...
	function get_property_task_bdd($planificateur_id) {
		global $dbh;

		if (!$planificateur_id) {
			$planificateur_id = 0;
		}
		$requete="SELECT id_planificateur, num_type_tache, libelle_tache, desc_tache, num_user, param, statut, rep_upload, path_upload, perio_heure, perio_minute,
			perio_jour_mois, perio_jour, perio_mois, calc_next_heure_deb, calc_next_date_deb,repertoire_nom, repertoire_path
			 FROM planificateur left join upload_repertoire on rep_upload=repertoire_id
			 where id_planificateur=".$planificateur_id;
		$res=pmb_mysql_query($requete,$dbh);

		if (pmb_mysql_num_rows($res)) {
			$r = pmb_mysql_fetch_object($res);
			$t["planificateur_id"]=$r->id_planificateur;
			$t["num_type_tache"]=$r->num_type_tache;
			$t["libelle_tache"]=htmlspecialchars_decode(stripslashes($r->libelle_tache),ENT_QUOTES);
			$t["desc_tache"]=htmlspecialchars_decode(stripslashes($r->desc_tache), ENT_QUOTES);
			$t["num_user"]=$r->num_user;
			$t["param"] = unserialize($r->param);
			$t["statut"] = $r->statut;
			$t["rep_upload"] = $r->rep_upload;
			$t["path_upload"] = $r->path_upload;
			$t["perio_heure"] = $r->perio_heure;
			$t["perio_minute"] = $r->perio_minute;
			$t["perio_jour_mois"] = explode(",",$r->perio_jour_mois);
			$t["perio_jour"] = explode(",",$r->perio_jour);
			$t["perio_mois"] = explode(",",$r->perio_mois);
			$t["calc_next_heure_deb"] = $r->calc_next_heure_deb;
			$t["calc_next_date_deb"] = $r->calc_next_date_deb;
			$t["repertoire_nom"] = $r->repertoire_nom;
			$t["repertoire_path"] = $r->repertoire_path;
		} else {
			$t["planificateur_id"]="";
			$t["num_type_tache"]="";
			$t["libelle_tache"]="";
			$t["desc_tache"]="";
			$t["num_user"]="";
			$t["param"]["timeout"] = $this->timeout;
			$t["param"]["histo_day"] = $this->histo_day;
			$t["param"]["histo_number"] = $this->histo_number;
			$t["param"]["restart_on_failure"] = $this->restart_on_failure;
			$t["param"]["alert_mail_on_failure"] = $this->alert_mail_on_failure;
			$t["statut"] = "1";
			$t["rep_upload"] = "0";
			$t["path_upload"] = "";
			$t["perio_heure"] = "*";
			$t["perio_minute"] = "01";
			$t["perio_jour_mois"] = "";
			$t["perio_jour"] = "";
			$t["perio_mois"] = "";
			$t["calc_next_heure_deb"] = "";  
			$t["calc_next_date_deb"] = "";  
			$t["repertoire_nom"] = "";
			$t["repertoire_path"] = "";  
		}
		return $t;
	}
	
	function get_task_params() {
		$params = "";
		if ($this->id_tache) {
			$result = pmb_mysql_query("select param from planificateur, taches where id_planificateur=num_planificateur and id_tache=".$this->id_tache);
			if ($result) $params = unserialize(pmb_mysql_result($result, 0,"param"));
		}
		return $params; 
	} 
	
	function initialize() {
		global $dbh;
		
		$this->statut = RUNNING;

		$requete = "update taches set start_at = CURRENT_TIMESTAMP, status = ".$this->statut."
			where id_tache='".$this->id_tache."'";
		
		pmb_mysql_query($requete,$dbh);
	}

	function finalize() {
		global $dbh,$base_path,$charset;
							
		$res = pmb_mysql_query("select indicat_progress from taches where id_tache=".$this->id_tache);
		$progress = pmb_mysql_result($res,0, "indicat_progress");
		
		if ($progress == 100) $this->statut=ENDED;
		else $this->statut = FAILED;
		
		//fin de l'exécution, mise à jour sur la base
		$req = "update taches set end_at = CURRENT_TIMESTAMP, status = ".$this->statut.", commande=0, rapport = '".htmlspecialchars(serialize($this->report), ENT_QUOTES,$charset)."',id_process=0
			where id_tache='".$this->id_tache."'";
		pmb_mysql_query($req,$dbh);
	}
	
	function update_progression($percent) {
		global $dbh;
		
		if ($this->id_tache) {
			$requete = "update taches set indicat_progress ='".$percent."' where id_tache=".$this->id_tache;
			pmb_mysql_query($requete,$dbh);
		}
	}
	
	function isUploadValide($id_tache) {
		global $dbh;
		
		$query_sel = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
			left join taches t on t.num_planificateur = p.id_planificateur
			left join taches_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
			where t.id_tache=".$id_tache;
		$res_query = pmb_mysql_query($query_sel, $dbh);
		if ($res_query) {
			$row = pmb_mysql_fetch_object($res_query);
			
			$up = new upload_folder($row->rep_upload);
			$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$row->path_upload);
			if ((is_dir($nom_chemin)) && (is_writable($nom_chemin)))
				return true;
		}
		return false;
	}
	
	// que passer à cette fonction datas ou object ?? (objet pdf , contenu xls)
	function generate_docnum($id_tache, $content, $mimetype="application/pdf", $ext_fichier="pdf") {
		global $dbh,$msg, $base_path;
		
		$tdn = new tache_docnum();
		
		$tdn->num_tache = $id_tache;
		
		$query_sel = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
			left join taches t on t.num_planificateur = p.id_planificateur
			left join taches_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
			where t.id_tache=".$tdn->num_tache;
		$res_query = pmb_mysql_query($query_sel, $dbh);
		if ($res_query) {
			$row = pmb_mysql_fetch_object($res_query);
			
			$up = new upload_folder($row->rep_upload);
			$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$row->path_upload);
//			if ((!is_dir($nom_chemin)) || (!is_writable($nom_chemin))) {
//				$nom_chemin = $base_path."/temp/";
//			}
			//appel de fonction pour le calcul de nom de fichier
			$date_now = date('Ymd');
//			$tdn->tache_docnum_nomfichier = str_replace(" ", "_", $row->libelle_tache)."_".$date_now;
			$tdn->tache_docnum_nomfichier = clean_string_to_base($row->libelle_tache)."_".$date_now;
			$tdn->tache_docnum_contenu = $content;
			$tdn->tache_docnum_extfichier= $ext_fichier;
			$tdn->tache_docnum_file = "";
			$tdn->tache_docnum_mimetype = $mimetype;
			$tdn->tache_docnum_repertoire = $row->rep_upload;
			$tdn->tache_docnum_path = $row->path_upload;
			$path_absolu = $nom_chemin.$tdn->tache_docnum_nomfichier.".".$tdn->tache_docnum_extfichier;
			if (file_exists($path_absolu)) {
				$i=2;
				while (file_exists($nom_chemin.$tdn->tache_docnum_nomfichier."_".$i.".".$tdn->tache_docnum_extfichier)) {
					$i++;
				}
				$path_absolu = $nom_chemin.$tdn->tache_docnum_nomfichier."_".$i.".".$tdn->tache_docnum_extfichier;
				$tdn->tache_docnum_nomfichier = $tdn->tache_docnum_nomfichier."_".$i;
			}
			$path_absolu = $up->encoder_chaine($path_absolu);
						
			//verifier permissions d'ecriture...
			if (is_writable($nom_chemin)) {
				switch ($mimetype) {
					case "application/pdf" :
						$content->Output($path_absolu,"F");
						break;
					case "application/ms-excel" :
						file_put_contents($path_absolu, $content);
						break;
				}
//				if ($mimetype == "application/pdf") {
//					$content->Output($path_absolu,"F");	
//				} else if ($mimetype == "application/ms-excel") {
//					file_put_contents($path_absolu, $content);
//				}
				
				$tdn->save();
				$this->report[] = "<tr><td>".$msg["planificateur_write_success"]." : <a target='_blank' href='./tache_docnum.php?tache_docnum_id=".$tdn->id_tache_docnum."'>".$tdn->tache_docnum_nomfichier.".".$tdn->tache_docnum_extfichier."</a></td></tr>";
				return true;
			} else {
				$this->report[] = "<tr><td>".sprintf($msg["planificateur_write_error"],$path_absolu)."</td></tr>";
				return false;
			}		
		}
	}
		
	function show_form() {
		// à surcharger
	}
}


class taches {
	var $types_taches=array();								// liste des types de tâches
	
	function taches() {
		global $base_path;
		if (file_exists($base_path."/admin/planificateur/catalog_subst.xml")) {
			$filename = $base_path."/admin/planificateur/catalog_subst.xml";
		} else {
			$filename = $base_path."/admin/planificateur/catalog.xml";
		}
		$this->parse_catalog($filename);
	}
	
	function parse_catalog($filename) {
		global $base_path,$type_task_id;
		
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"CATALOG");
		
		foreach ($param["ACTION"] as $anitem) {
			$this->types_taches[] = new tache($anitem);				
		}
	}
	
	//retourne le nombre de tâches associé à un type de tâche
	function get_nb_tasks($type_task_id) {
		global $dbh;

		$res = pmb_mysql_query("select * from planificateur where num_type_tache=".$type_task_id,$dbh);
		$nb = pmb_mysql_num_rows($res);

		return $nb;
	}
	
	//retourne le nombre de tâches associé à un type de tâche
	function get_nb_docnum($id_tache) {
		global $dbh;

		$res = pmb_mysql_query("select * from taches t, taches_docnum tdn where t.id_tache=tdn.num_tache and id_tache=".$id_tache,$dbh);
		$nb = pmb_mysql_num_rows($res);

		return $nb;
	}
	
	//affiche la planification de tâches par type
	function get_tasks($num_type_tache) {
		global $dbh;

		$sql = "SELECT id_planificateur, libelle_tache, desc_tache FROM planificateur WHERE num_type_tache = '".$num_type_tache."'";
		$res = pmb_mysql_query($sql, $dbh);
		$parity_source= $num_type_tache % 2;
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
			    $pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
				$tr_javascript_source=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; if (e.srcElement) target=e.srcElement; else target=e.target; if (target.nodeName!='INPUT') document.location='./admin.php?categ=planificateur&sub=manager&act=task&type_task_id=".$num_type_tache."&planificateur_id=".$row->id_planificateur."';\" ";
					print "<tr style='cursor: pointer' class='$pair_impair_source' $tr_javascript_source>
						<td>".htmlspecialchars_decode(stripslashes($row->libelle_tache),ENT_QUOTES)."</td>
						<td>".htmlspecialchars_decode(stripslashes($row->desc_tache),ENT_QUOTES)."</td>
						<td></td><td></td></tr>";
			}
		}
	}
	
	//documents numériques par tâches en cours ou exécutées
	function get_docsnum($task_id) {
		global $dbh;

		$sql = "SELECT id_tache_docnum, tache_docnum_nomfichier, tache_docnum_mimetype,tache_docnum_extfichier, tache_docnum_repertoire FROM taches_docnum WHERE num_tache = '".$task_id."'";
		$res = pmb_mysql_query($sql, $dbh);
		$tab_docnum = array();
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
				$t=array();
				$t["id_tache_docnum"] = $row->id_tache_docnum;
				$t["tache_docnum_nomfichier"] = $row->tache_docnum_nomfichier;
				$t["tache_docnum_mimetype"] = $row->tache_docnum_mimetype;
				$t["tache_docnum_extfichier"] = $row->tache_docnum_extfichier;
				$t["tache_docnum_repertoire"] = $row->tache_docnum_repertoire;
				
				$tab_docnum[] = $t;
			}
		}
		$tdn = new tache_docnum();

		print "<tr style='cursor: pointer' >";
		print $tdn->show_docnum_table($tab_docnum, "");
		print "</tr>";		
	}
	
	// affichage partie reporting 
	function get_tasks_plan() {
		global $dbh, $msg,$charset;
			
		$sql = "SELECT t.id_tache, p.num_type_tache, p.libelle_tache, t.start_at, t.end_at, t.status, t.msg_statut, p.calc_next_date_deb, p.calc_next_heure_deb, t.commande, t.indicat_progress 
				FROM taches t,planificateur p 
				Where t.num_planificateur = p.id_planificateur
				";
//		order by start_at DESC
//		if(start_at='0000-00-00 00:00:00','status',''),
		$sql_first .= $sql." and start_at = '0000-00-00 00:00:00'";
		$sql_second .= $sql." and start_at <> '0000-00-00 00:00:00' order by t.start_at DESC";
		
		$res = pmb_mysql_query($sql_first, $dbh);
		$res2 = pmb_mysql_query($sql_second, $dbh);

		$pair_impair=0;
		$parity=0;
		
		print "<table id='table_reporting' >
				<tr>
					<th>&nbsp;</th>
					<th width='20%'>".htmlentities($msg["planificateur_task"], ENT_QUOTES, $charset)."</th>
					<th width='15%'>".htmlentities($msg["planificateur_start_exec"], ENT_QUOTES, $charset)."</th>
					<th width='15%'>".htmlentities($msg["planificateur_end_exec"], ENT_QUOTES, $charset)."</th>
					<th width='18%'>".htmlentities($msg["planificateur_next_exec"],ENT_QUOTES,$charset)."</th>
					<th width='12%'>".htmlentities($msg["planificateur_progress_task"], ENT_QUOTES, $charset)."</th>
					<th width='10%'>".htmlentities($msg["planificateur_etat_exec"], ENT_QUOTES, $charset)."</th>
					<th width='10%'>".htmlentities($msg["planificateur_commande_exec"], ENT_QUOTES, $charset)."</th>
				</tr>";
		
		//taches en attente...
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
				global $pair_impair_source;
				$pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
				$this->row_planned($row);
			}
		}
		//taches en cours et terminé
		if ($res2) {
			while ($row2=pmb_mysql_fetch_object($res2)) {
				$pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
				global $pair_impair_source;
				$this->row_planned($row2);
			}
		}
//			//recherche du nombre de documents numériques par tâche
//			$n_docsnum = $this->get_nb_docnum($row->id_tache);
//			
//			//comment task
//			$comment = "";
//			foreach ($this->types_taches as $atache) {
//				if ($atache->id_type == $row->num_type_tache) {
//					$comment = $atache->comment;
//					//présence de commandes .. selecteurs ??
//					$show_commands = "";
//					foreach ($atache->states as $aelement) {
//						if ($row->status == $aelement["id"]) {
//							foreach ($aelement["nextState"] as $state) {
//								if ($state["command"] != "") {
//									//récupère le label de la commande
//									foreach($atache->commands as $command) {
//										if (($state["command"] == $command["name"]) && ($state["dontsend"] != "yes")) {
//											$show_commands .= "<option id='".$row->id_tache."' value='".$command["id"]."'>".utf8_normalize($command["label"])."</option>";
//										}
//									}
//								}
//							}
//						}
//					}					
//				}
//			}
//		
//			//lien du rapport
//			if ($row->end_at == '0000-00-00 00:00:00') {
//				$line=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; \" ";
//			} else {
//				$line=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; \" onClick='show_layer(); get_report_content(".$row->id_tache.",".$row->num_type_tache.");' style='cursor: pointer'";
//			}
//			
//			print "	<tr class='$pair_impair_source' $line title='".htmlentities($comment,ENT_QUOTES,$charset)." : ".htmlentities(stripslashes($row->libelle_tache),ENT_QUOTES,$charset)."'>
//					<td>".($n_docsnum?"<img src='images/plus.gif' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_docsnum(\"tache_".$row->id_tache."\"); '/>":"&nbsp;")."</td>
//					<td>".htmlentities(substr(stripslashes($row->libelle_tache),0,25),ENT_QUOTES,$charset).(strlen($row->libelle_tache) > 25 ? "...":"")."</td>
//					<td>".($row->start_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->start_at,ENT_QUOTES,$charset)))."</td>
//					<td>".($row->end_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->end_at,ENT_QUOTES,$charset)))."</td>
//					".$this->command_waiting($row->id_tache)."
//					<td >";
////					$progress_bar=new progress_bar($row->indicat_progress."%",$row->indicat_progress,0);
//					print "
//				        <div class='row' id='progress_bar_".$row->id_tache."' style='text-align:center; width:80%; border: 1px solid #000000; padding: 3px; z-index:1;'>
//				            <div style='text-align:left; width:100%; height:20px;'>
//				                <img id='progress' src='images/jauge.png' style='width:".$row->indicat_progress."%; height:20px'/>
//				            
//					            <div style='text-align:center; position:relative; top: -25px; z-index:1'>
//					                <span id='progress_text'></span>".$row->indicat_progress." %
//					                <span id='progress_percent'></span>
//					            </div>
//					    	</div>
//				        </div>";
//					print "</td>
//					<td >".htmlentities($msg['planificateur_state_'.$row->status.''],ENT_QUOTES,$charset)."</td>
//					<td>";
//					if ($show_commands != "") {
//						print "<select id='form_commandes' name='form_commandes' class='saisie-15em' onchange='commande(this.options[this.selectedIndex].id, this.options[this.selectedIndex].value)' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation();'>
//						<option value='0' selected>".$msg['planificateur_commande_default']."</option>";
//						print $show_commands;
//						print"</select>";
//					}
//					print "</td></tr>";
//					print "<tr class='$pair_impair_source' style='display:none' id='tache_".$row->id_tache."'><td>&nbsp;</td>
//					<td colspan='8'><table style='border:1px solid; background: #ffffff' class='docnum'>";
//					$this->get_docsnum($row->id_tache);
//				    print "</table></td></tr>";
//		}
		print "</table>";
	}

	function row_planned($row) {
		global $msg, $charset, $pair_impair_source;
		
		//recherche du nombre de documents numériques par tâche
		$n_docsnum = $this->get_nb_docnum($row->id_tache);
			
		//comment task
		$comment = "";
		foreach ($this->types_taches as $atache) {
			if ($atache->id_type == $row->num_type_tache) {
				$comment = $atache->comment;
				//présence de commandes .. selecteurs ??
				$show_commands = "";
				foreach ($atache->states as $aelement) {
					if ($row->status == $aelement["id"]) {
						foreach ($aelement["nextState"] as $state) {
							if ($state["command"] != "") {
								//récupère le label de la commande
								foreach($atache->commands as $command) {
									if (($state["command"] == $command["name"]) && ($state["dontsend"] != "yes")) {
										$show_commands .= "<option id='".$row->id_tache."' value='".$command["id"]."'>".htmlentities($command["label"], ENT_QUOTES, $charset)."</option>";
									}
								}
							}
						}
					}
				}					
			}
		}
		
		//lien du rapport
		if ($row->end_at == '0000-00-00 00:00:00') {
			$line=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; \" ";
		} else {
			$line=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; \" onClick='show_layer(); get_report_content(".$row->id_tache.",".$row->num_type_tache.");' style='cursor: pointer'";
		}
			
		print "	<tr class='$pair_impair_source' $line title='".htmlentities($comment,ENT_QUOTES,$charset)." : ".htmlentities(stripslashes($row->libelle_tache),ENT_QUOTES,$charset)."'>
				<td>".($n_docsnum?"<img src='images/plus.gif' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_docsnum(\"tache_".$row->id_tache."\"); '/>":"&nbsp;")."</td>
				<td>".htmlentities(stripslashes($row->libelle_tache),ENT_QUOTES,$charset)."</td>
				<td>".($row->start_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->start_at,ENT_QUOTES,$charset)))."</td>
				<td>".($row->end_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->end_at,ENT_QUOTES,$charset)))."</td>
				".$this->command_waiting($row->id_tache)."
				<td >";
				$progress_bar=new progress_bar_tache($row->indicat_progress);
				print "</td>
				<td >".htmlentities($msg['planificateur_state_'.$row->status.''],ENT_QUOTES,$charset)."</td>
				<td>";
				if ($show_commands != "") {
					print "<select id='form_commandes' name='form_commandes' class='saisie-15em' onchange='commande(this.options[this.selectedIndex].id, this.options[this.selectedIndex].value)' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation();'>
					<option value='0' selected>".$msg['planificateur_commande_default']."</option>";
					print $show_commands;
					print"</select>";
				}
				print "</td></tr>";
				print "<tr class='$pair_impair_source' style='display:none' id='tache_".$row->id_tache."'><td>&nbsp;</td>
				<td colspan='8'><table style='border:1px solid; background: #ffffff' class='docnum'>";
				$this->get_docsnum($row->id_tache);
			    print "</table>
			</td>
		</tr>";
	}
	
	// Envoi d'une commande pour l'interprétation...
	function command_waiting($id_tache,$cmd=''){
		global $dbh,$msg;

		$requete_sql = "select status, commande from taches where id_tache='".$id_tache."' and end_at='0000-00-00 00:00:00'";
		$result = pmb_mysql_query($requete_sql);
		if(pmb_mysql_num_rows($result) == "1") {
			$status = pmb_mysql_result($result, 0,"status");
			$commande = pmb_mysql_result($result, 0,"commande");
		} else {
			$status = '';
			$commande = 0;
		}
		
		// une commande a déjà été envoyée auparavant...
		if ($commande != '0') {
			$cmd = $commande;
		} 

		if ($cmd != '') {
			//check command - la commande envoyée est vérifié par rapport au status
			foreach($this->types_taches as $type_tache) {
				foreach ($type_tache->states as $state) {
					if ($state["id"] == $status) {
						foreach($state["nextState"] as $nextState) {
							foreach($type_tache->commands as $command) {
								if ($nextState["command"] == $command["name"]) {
									if ($command["id"] == $cmd)
										pmb_mysql_query("update taches set commande=".$cmd.", next_state='".constant($nextState["value"])."' where id_tache=".$id_tache, $dbh);
								}
							}
						}
					}
				}
			}
		}
		
		$rs = pmb_mysql_query("select t.start_at, t.commande, p.calc_next_date_deb, p.calc_next_heure_deb 
			from taches t , planificateur p 
			where t.num_planificateur = p.id_planificateur 
			and id_tache=".$id_tache);
		$tpl = "<td id='commande_tache_".$id_tache."'>";
		if ($rs) {
			$row = pmb_mysql_fetch_object($rs);
			if($row->start_at == '0000-00-00 00:00:00') {
				$tpl .= htmlentities(formatdate($row->calc_next_date_deb),ENT_QUOTES,$charset)." ".htmlentities($row->calc_next_heure_deb,ENT_QUOTES,$charset);	
			} else if (($row->start_at != '0000-00-00 00:00:00') && ($row->commande != NULL)) {
				$tpl .= utf8_normalize($msg["planificateur_command_$row->commande"]);
			} 
		} 
		$tpl .= "</td>";
		
		return $tpl;
	}
	
	//affichage du formulaire global au type de tâche
	function show_type_task_form($num_type_task) {
		global $base_path,$charset,$msg, $admin_planificateur_global_params;
		
		foreach ($this->types_taches as $atache) {
			if ($atache->id_type == $num_type_task) {
				$name = $atache->name;
				$comment = $atache->comment;
//				$dir_upload_boolean = $atache->dir_upload_boolean;
			}
		}
		//Inclusion de la classe spécifique
		if (file_exists($base_path."/admin/planificateur/".$name."/".$name.".class.php")) {
			require_once($base_path."/admin/planificateur/".$name."/".$name.".class.php");
			eval("\$type_plan=new ".$name."();");
			$type_plan->fetch_global_properties();
//			$admin_planificateur_global_params=str_replace("!!script_js!!","<script type='text/javascript' src='$base_path/javascript/upload.js'></script>",$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!script_js!!","",$admin_planificateur_global_params);						
			$admin_planificateur_global_params=str_replace("!!special_form!!","",$admin_planificateur_global_params);
			//Remplacement des valeurs par défaut
			$admin_planificateur_global_params=str_replace("!!id!!",$num_type_task,$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!comment!!",htmlentities($comment,ENT_QUOTES,$charset),$admin_planificateur_global_params);
			
			//ce type de tâche nécessite-t-il d'un répertoire d'upload pour les documents numériques?
			$admin_planificateur_global_params=str_replace("!!div_upload!!","",$admin_planificateur_global_params);
//			if ($dir_upload_boolean != "0") {
//				$up = new upload_folder($rep_upload);
//				$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$path_upload);
//	
//				$admin_planificateur_global_params=str_replace("!!div_upload!!","<div class='row'>
//					<div class='colonne3'><label for='timeout'/>".$msg["print_numeric_ex_title"]."</label></div>
//							<div class='colonne_suite'>
//								".$msg["planificateur_upload"]." : 
//								<input type='text' name='path' id='path' value='!!path!!' class='saisie-50emr' READONLY />
//								<input type='button' id='upload_path' class='bouton' onclick='upload_openFrame(event)' value='...' name='upload_path' />
//								<input id='id_rep' type='hidden' value='!!id_rep!!' name='id_rep' /> 
//							</div>
//					</div>",$admin_planificateur_global_params);
//			} else {
//				$admin_planificateur_global_params=str_replace("!!div_upload!!","",$admin_planificateur_global_params);
//			}
		
			$admin_planificateur_global_params=str_replace("!!timeout!!",$type_plan->timeout,$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!histo_day!!",$type_plan->histo_day,$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!histo_number!!",$type_plan->histo_number,$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!restart_on_failure_checked!!",($type_plan->restart_on_failure ? "checked=checked" : ""),$admin_planificateur_global_params);
			$params_alert_mail = explode(",",$type_plan->alert_mail_on_failure);
			$admin_planificateur_global_params=str_replace("!!alert_mail_on_failure_checked!!",($params_alert_mail[0] ? " checked=checked " : ""),$admin_planificateur_global_params);
			$admin_planificateur_global_params=str_replace("!!mail_on_failure!!",$params_alert_mail[1],$admin_planificateur_global_params);
		}
		
		return $admin_planificateur_global_params;
	}
	
	// affichage du formulaire de la tâche
	function show_form_task ($planificateur_id="") {
    	global $charset, $base_path, $msg;
    	global $planificateur_form, $act, $type_task_id, $subaction;
		
    	foreach($this->types_taches as $type) {
			if ($type->id_type == $type_task_id) {
				$comment = $type->comment;
				$name = $type->name;
				$dir_upload_boolean = $type->dir_upload_boolean;	
			}
		}
		
		// Inclusion de la classe spécifique
		if (is_file($base_path.'/admin/planificateur/'.$name.'/'.$name.'.class.php')) {
			require_once ($base_path.'/admin/planificateur/'.$name.'/'.$name.'.class.php');
			eval("\$a_task=new ".$name."();");
			
			//Récupération des données du formulaire
			if ($subaction == "change") {
				global $task_name,$task_desc,$form_users,$task_active, $id_rep,$path;
				global $task_perio_heure, $task_perio_min, $chkbx_task_quotidien, $chkbx_task_hebdo, $chkbx_task_mensuel;
				global $timeout,$radio_histo_day_number,$histo_day, $histo_number,$restart_on_failure,$alert_mail_on_failure,$mail_on_failure;
				$libelle_tache = stripslashes($task_name);
				$desc_tache = stripslashes($task_desc);
				$perio_heure = $task_perio_heure;
				$statut = $task_active;
				$rep_upload = ($id_rep ? $id_rep : "");
				$chemin_upload = ($path ? $path : "");
				$perio_heure = $task_perio_heure;
				$perio_minute = $task_perio_min;
				$perio_jour_mois = $chkbx_task_quotidien;
				$perio_jour = $chkbx_task_hebdo;
				$perio_mois = $chkbx_task_mensuel;
				$param["timeout"] = $timeout;
				$param["histo_day"] = $histo_day;
				$param["histo_number"] = $histo_number;
				$param["restart_on_failure"] = $restart_on_failure;
				$param["alert_mail_on_failure"] = $alert_mail_on_failure.($mail_on_failure ? ",".$mail_on_failure : "");
			} else {
				//Récupération des données de la base
				$a_task->fetch_global_properties();
				$tab_properties = $a_task->get_property_task_bdd($planificateur_id);
				if (isset($tab_properties)) {
					if (is_array($tab_properties)) {
						foreach($tab_properties as $atab_properties=>$atab_propertiesv) {
//							global $$atab_properties;
							$$atab_properties = $atab_propertiesv;
						}					
					}
				}
			}

			//form spécifique
			$form_specific_task = $a_task->show_form($param);
		} else {
			$form_specific_task = "";
		}
		
		$planificateur_form=str_replace("!!script_js!!","
			<script type='text/javascript' src='./javascript/select.js'></script>
			<script type='text/javascript' src='./javascript/upload.js'></script>",$planificateur_form);
		$planificateur_form=str_replace("!!submit_action!!","return checkForm();",$planificateur_form);
		$planificateur_form=str_replace("!!libelle_type_task!!",$comment,$planificateur_form);
    	$planificateur_form=str_replace("!!task_name!!",htmlentities($libelle_tache,ENT_QUOTES,$charset),$planificateur_form);
		$planificateur_form=str_replace("!!task_desc!!",htmlentities($desc_tache,ENT_QUOTES,$charset),$planificateur_form);
		
		$rqt_user = pmb_mysql_query("select esuser_id, esuser_username from es_esusers");
		$form_users = "<select name='form_users'>";
		while ($row = pmb_mysql_fetch_object($rqt_user)) {
			if ($row->esuser_id == $num_user) {
				$form_users .="<option value='".$row->esuser_id."' selected>".$row->esuser_username."</option>";
			} else {
				$form_users .="<option value='".$row->esuser_id."'>".$row->esuser_username."</option>";				
			}
		}
		$form_users .= "</select>";
		if (pmb_mysql_num_rows($rqt_user) == 0) {
			$form_users .= "* ".$msg["planificateur_task_users_unknown"];
		}
		$planificateur_form=str_replace("!!task_users!!",$form_users,$planificateur_form);
		
		$planificateur_form=str_replace("!!task_statut!!","<input type='checkbox' name='task_active' id='task_active' value='".$statut."' ".($statut != "0" ? " checked " : "''" )." onchange='changeStatut();'/>",$planificateur_form);
		
		//ce type de tâche nécessite-t-il d'un répertoire d'upload pour les documents numériques?
		if ($dir_upload_boolean != "0") {
			$up = new upload_folder($rep_upload);
			if ($subaction == 'change') {
				$nom_chemin = $up->formate_path_to_nom($chemin_upload);
			} else {
				$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$path_upload);
			}
			$planificateur_form=str_replace("!!div_upload!!","<div class='row'>
				<div class='colonne3'><label for='timeout'/>".$msg["print_numeric_ex_title"]."</label></div>
						<div class='colonne_suite'>
							".$msg["planificateur_upload"]." : 
							<input type='text' name='path' id='path' value='!!path!!' class='saisie-50emr' READONLY />
							<input type='button' id='upload_path' class='bouton' onclick='upload_openFrame(event)' value='...' name='upload_path' />
							<input id='id_rep' type='hidden' value='!!id_rep!!' name='id_rep' /> 
						</div>
				</div>",$planificateur_form);
		} else {
			$planificateur_form=str_replace("!!div_upload!!","",$planificateur_form);
		}
		$planificateur_form = str_replace('!!path!!', htmlentities($nom_chemin ,ENT_QUOTES, $charset), $planificateur_form);
		$planificateur_form = str_replace('!!id_rep!!', htmlentities($rep_upload ,ENT_QUOTES, $charset), $planificateur_form);
		
		$planificateur_form=str_replace("!!task_perio_heure!!","<input type='text' id='task_perio_heure' name='task_perio_heure' value='".$perio_heure."' class='saisie-5em'/>",$planificateur_form);
		$planificateur_form=str_replace("!!task_perio_min!!","<input type='text' id='task_perio_min' name='task_perio_min' value='".$perio_minute."' class='saisie-5em'/>",$planificateur_form);
		
		$planificateur_form=str_replace("!!help!!",
			"<a onclick='openPopUp(\"./admin/planificateur/help.php?action_help=configure_time\",\"help\",500,600,-2,-2,\"scrollbars=yes,menubar=0\"); w.focus(); return false;' href='#'>
			<img border='0' align='center' title='Aide...' alt='Aide...' src='".$base_path."/images/aide.gif' /></a>",$planificateur_form);
		
		$perio_quotidien .= "<input type='checkbox' id='chkbx_task_quotidien_0' name='chkbx_task_quotidien[]'  value='*' ".($perio_jour_mois[0] == '*' ? "checked" : "''" )." onchange='changePerio(\"*\",\"chkbx_task_quotidien\",31);'> ".$msg["planificateur_task_all_days_of_month"]."</input>";
		for ($i=1; $i<=31; $i++) {
			$cochee = false;
			for ($j=0; $j<sizeof($perio_jour_mois); $j++) {
				if ($perio_jour_mois[$j] == $i) {
					$cochee = true;
				}
			}
			$perio_quotidien .= "<input type='checkbox' id='chkbx_task_quotidien_".$i."' name='chkbx_task_quotidien[]'  value='".$i."' ".($cochee == true ? "checked" : "''" )." onchange='changePerio($i,\"chkbx_task_quotidien\",31);'/> ".$i." ";
			if ($i == 15) {
				$perio_quotidien .= "<br />";
			}
		}
		$planificateur_form=str_replace("!!task_perio_quotidien!!",$perio_quotidien,$planificateur_form);
		
		$perio_hebdo .= "<input type='checkbox' id='chkbx_task_hebdo_0' name='chkbx_task_hebdo[]'  value='*' ".($perio_jour[0] == '*' ? "checked" : "''" )." onchange='changePerio(\"*\",\"chkbx_task_hebdo\",7);'> ".$msg["planificateur_task_all_days"]."</input>";	
		for ($i=1; $i<=7; $i++) {
			$cochee = false;
			for ($j=0; $j<sizeof($perio_jour); $j++) {
				if ($perio_jour[$j] == $i) {
					$cochee = true;
				} 
			}
			$perio_hebdo .= "<input type='checkbox' id='chkbx_task_hebdo_".$i."' name='chkbx_task_hebdo[]'  value='".$i."' ".($cochee == true ? "checked" : "''" )." onchange='changePerio($i,\"chkbx_task_hebdo\",7);'/> ".$msg["week_days_$i"];						
			if ($i == 3) {
				$perio_hebdo .= "<br />";
			}
		}
		$planificateur_form=str_replace("!!task_perio_hebdo!!",$perio_hebdo,$planificateur_form);
		
		$perio_mensuel .= "<input type='checkbox' id='chkbx_task_mensuel_0' name='chkbx_task_mensuel[]'  value='*' ".($perio_mois[0] == '*' ? " checked " : "''" )." onchange='changePerio(\"*\",\"chkbx_task_mensuel\",12);'> ".$msg["planificateur_task_all_months"]."</input>";
		for ($i=1; $i<=12; $i++) {
			$cochee = false;
			for ($j=0; $j<sizeof($perio_mois); $j++) {
				if ($perio_mois[$j] == $i) {
					$cochee = true;
				} 
			}
			$perio_mensuel .= "<input type='checkbox' id='chkbx_task_mensuel_".$i."' name='chkbx_task_mensuel[]'  value='".$i."' ".($cochee == true ? "checked" : "''" )." onchange='changePerio($i,\"chkbx_task_mensuel\",12);'> ".ucfirst($msg[$i+1005])."</input>";
			if ($i == 6) {
				$perio_mensuel .= "<br />";
			}
		}		
		$planificateur_form=str_replace("!!task_perio_mensuel!!",$perio_mensuel,$planificateur_form);

		$planificateur_form=str_replace("!!timeout!!",$param["timeout"],$planificateur_form);
		$planificateur_form=str_replace("!!histo_day_checked!!",($param["histo_day"] != "" ? " checked " : ""),$planificateur_form);
		$planificateur_form=str_replace("!!histo_number_checked!!",($param["histo_number"] != "" ? " checked " : ""),$planificateur_form);
		$planificateur_form=str_replace("!!histo_day!!",$param["histo_day"],$planificateur_form);
		$planificateur_form=str_replace("!!histo_day_visible!!",($param["histo_day"] == "" ? "disabled" : ""),$planificateur_form);
		$planificateur_form=str_replace("!!histo_number!!",$param["histo_number"],$planificateur_form);
		$planificateur_form=str_replace("!!histo_number_visible!!",($param["histo_number"] == "" ? "disabled" : ""),$planificateur_form);
		$planificateur_form=str_replace("!!restart_on_failure_checked!!",($param["restart_on_failure"] ? " checked " : ""),$planificateur_form);
		$params_alert_mail = explode(",",$param["alert_mail_on_failure"]);
		$planificateur_form=str_replace("!!alert_mail_on_failure_checked!!",($params_alert_mail[0] ? " checked " : ""),$planificateur_form);
		$planificateur_form=str_replace("!!mail_on_failure!!",$params_alert_mail[1],$planificateur_form);
		
		//Inclusion du formulaire spécifique au type de tâche
		$planificateur_form=str_replace("!!specific_form!!",$form_specific_task,$planificateur_form);
		if ($act == "task_duplicate") $planificateur_id = 0;
		if (!$planificateur_id) {
			$bt_save=$base_path."/admin.php?categ=planificateur&sub=manager&act=task&type_task_id=".$type_task_id;
			$bt_duplicate="";
			$bt_suppr="";
		} else {
			$bt_save=$base_path."/admin.php?categ=planificateur&sub=manager&act=task&type_task_id=".$type_task_id."&planificateur_id=".$planificateur_id;
			$bt_duplicate="<input type='button' class='bouton' value='".$msg["tache_duplicate_bouton"]."' onclick='document.location=\"./admin.php?categ=planificateur&sub=manager&act=task_duplicate&type_task_id=".$type_task_id."&planificateur_id=".$planificateur_id."\"' />";
			$bt_suppr="<input type='button' class='bouton' value='".$msg["63"]."' onClick='location.href=\"$base_path/admin.php?categ=planificateur&sub=manager&act=task_del&type_task_id=$type_task_id&planificateur_id=".$planificateur_id."\"'/>";
		}
		$planificateur_form=str_replace("!!bt_save!!",$bt_save,$planificateur_form);
		$planificateur_form=str_replace("!!bt_duplicate!!",$bt_duplicate,$planificateur_form);
		$planificateur_form=str_replace("!!bt_supprimer!!",$bt_suppr,$planificateur_form);
		
		return $planificateur_form;
    }
	
	//Suppression d'une planification de tâche associée à un type de tâche
	function del_task($planificateur_id) {
		global $dbh, $msg, $base_path;
		global $template_result, $type_task_id, $confirm, $disabled;
		
		$libelle_tache = "";		
    	foreach($this->types_taches as $type) {
			if ($type->id_type == $type_task_id) {
				$libelle_tache = $type->comment;
			}
		}	
		//disabled == 1 then statut = 0
		if ($disabled == "1") {
			if ($planificateur_id != "") {
				$query = "update planificateur set statut=0 where id_planificateur=".$planificateur_id;
				pmb_mysql_query($query, $dbh);
			}
		}
		
    	$template_result=str_replace("!!libelle_type_task!!",$libelle_tache,$template_result);
    	
		//on vérifie tout d'abord que la tâche soit désactivée
		$query_active = "select statut from planificateur where id_planificateur=".$planificateur_id;
		$result = pmb_mysql_query($query_active, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$value_statut = pmb_mysql_result($result, 0, "statut");
		} else {
			$value_statut = "";
		}
				
		if ($value_statut == "0") {
			$body = "<div align=center>".$msg["planificateur_confirm_phrase"]."<br />
				<a href='$base_path/admin.php?categ=planificateur&sub=manager&act=task_del&type_task_id=$type_task_id&planificateur_id=$planificateur_id&confirm=1'>
				".$msg["40"]."
				</a> - <a href='$base_path/admin.php?categ=planificateur&sub=manager&type_task_id=$type_task_id&planificateur_id=$planificateur_id&confirm=0'>
				".$msg["39"]."
				</a>	
				</div>
			";
		} else {
			$body = "<div align=center>".$msg["planificateur_error_active"]."<br />
				<a href='$base_path/admin.php?categ=planificateur&sub=manager&act=task_del&type_task_id=$type_task_id&planificateur_id=$planificateur_id&disabled=1'>
				".$msg["40"]."
				</a> - <a href='$base_path/admin.php?categ=planificateur&sub=manager&type_task_id=$type_task_id&planificateur_id=$planificateur_id&disabled=0'>
				".$msg["39"]."
				</a>	
				</div>
			";
		}
		
		$template_result=str_replace("!!BODY!!",$body,$template_result);
		
		//Confirmation de suppression
		if ($confirm == "1") {
			//Vérifie si une tâche est en cours sur cette planification
			$query_check = "select id_tache from taches where num_planificateur=".$planificateur_id." and status <> 3";
			$result = pmb_mysql_query($query_check);		
			if (pmb_mysql_num_rows($result) == '1') {
				// ne pas la supprimer !
				$ident_tache = pmb_mysql_result($result, 0,"id_tache");
			}
			//suppression des tâches à l'exclusion de celle en cours
			$requete="delete from taches where num_planificateur=".$planificateur_id." 
				and id_tache <> ".$ident_tache;
			pmb_mysql_query($requete);
			$requete="delete from planificateur where id_planificateur=".$planificateur_id."";
			pmb_mysql_query($requete);
			
			//et les documents numériques qu'en fait-on???
			
			print "<script>document.location.href='$base_path/admin.php?categ=planificateur&sub=manager';</script>";
		}
		return $template_result;
	}
}