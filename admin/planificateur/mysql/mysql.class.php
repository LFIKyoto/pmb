<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql.class.php,v 1.2 2012-07-31 10:12:16 dgoron Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");

class mysql extends tache {
	
	function mysql($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
				
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {		
		//paramètres pré-enregistré
		$tab_maintenance = array();
		if ($param['mySQL']) {
			foreach ($param['mySQL'] as $elem) {
				$tab_maintenance[$elem] = $elem;
			}
		}

		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_mysql_maintenance"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' id='check' name='mySQL[]' value='CHECK' ".($tab_maintenance['CHECK'] == 'CHECK' ? 'checked' : '')."/><label for='check'>".$this->msg["planificateur_mysql_checkTable"]."</label>
				<br />
				<input type='checkbox' id='analyze' name='mySQL[]' value='ANALYZE' ".($tab_maintenance['ANALYZE'] == 'ANALYZE' ? 'checked' : '')."/><label for='analyze'>".$this->msg["planificateur_mysql_analyzeTable"]."</label>
				<br />
				<input type='checkbox' id='repair' name='mySQL[]' value='REPAIR' ".($tab_maintenance['REPAIR'] == 'REPAIR' ? 'checked' : '')."/><label for='repair'>".$this->msg["planificateur_mysql_repairTable"]."</label>
				<br />
				<input type='checkbox' id='optimize' name='mySQL[]' value='OPTIMIZE' ".($tab_maintenance['OPTIMIZE'] == 'OPTIMIZE' ? 'checked' : '')."/><label for='optimize'>".$this->msg["planificateur_mysql_optimizeTable"]."</label>		
			</div>
		</div>";
					
		return $form_task;
	}
	
	function task_execution() {
		global $dbh, $charset, $msg, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
		
			$this->report[] = "<tr><th colspan=4>".$this->msg["mysql_operation"]."</th></tr>";
			if (method_exists($this->proxy, "pmbesMySQL_mysqlTable")) {
				if ($parameters["mySQL"]) {
					$percent = 0;
					$p_value = (int) 100/count($parameters["mySQL"]);
					foreach($parameters["mySQL"] as $action) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
			
						if($this->statut == WAITING) {
							$this->send_command(RUNNING);
						}

						if($this->statut == RUNNING) {
							$this->report[] = "<tr ><th colspan=4>".$action."</th></tr>";
							$result = $this->proxy->pmbesMySQL_mysqlTable($action);
							$maintenance_mysql = array();
							foreach ($result as $i=>$table) {
								switch ($table[2]) {	//Msg_Type : status error info warning
									case "status" :
										$maintenance_mysql["status"][$table[3]][] = $table[0];  
										break;
									case "error" :
										$maintenance_mysql["error"][$table[3]][] = $table[0];
										break;
									case "info" :
										$maintenance_mysql["info"][$table[3]][] = $table[0];
										break;
									case "warning" :
										$maintenance_mysql["warning"][$table[3]][] = $table[0];
										break;
								}
							}
							$txt_msg_type = "";
							$txt_msg_text = "";
							$htmlOutput = "";
							foreach ($maintenance_mysql as $msg_type=>$values) {
								if ($msg_type != $txt_msg_type) {
									$txt_msg_type = $msg_type;
									$htmlOutput .= "<tr class='odd'><td><b>Op : </b>".$msg_type."</td></tr>";
								}
								foreach ($values as $msg_text=>$tables) {
									if ($msg_text != $txt_msg_text) {
										$txt_msg_text = $msg_text;
										$htmlOutput .= "<tr class='odd'><td><b>Msg_text : </b>".$msg_text."</td></tr>";
										$htmlOutput .= "<tr class='odd'><td><b>Tables : </b>".implode(" - ", $tables)."</td></tr>";
									}
								}
							}
							$this->report[] = htmlentities($htmlOutput, ENT_QUOTES, $charset);
							$percent += $p_value;
							$this->update_progression($percent);	
						}
					}
				} else {
					$this->report[] = "<tr><td>".$this->msg["mysql_action_unknown"]."</td></tr>";
				}
			} else {
				$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"mysqlTable","pmbesMySQL",$PMBusername)."</td></tr>";
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
				$this->suspend_mysql();
				break;
			case STOP:
				$this->finalize();
				die();
				break;
			case FAIL:
				$this->finalize();
				die();
				break;
		}
	}
    
	function make_serialized_task_params() {
    	global $mySQL;

		$t = parent::make_serialized_task_params();
		
		if ($mySQL) {
			foreach ($mySQL as $elem) {
				$t["mySQL"][$elem]=stripslashes($elem);			
			}
		}

    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }

	function suspend_mysql() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
}