<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync.class.php,v 1.4.4.1 2015-09-15 16:23:37 apetithomme Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");
require_once($class_path."/connecteurs.class.php");

class sync extends tache {
	var $id_connector;
	var $id_source;
	
	function sync($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
	}

	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $msg;
		global $base_path, $type_task_id, $planificateur_id;
		global $subaction;

		if ($subaction == 'change') {
			global $source_entrepot, $connecteurId;
		} else {
			if (is_array($param)) {
				foreach ($param as $aparam=>$aparamv) {
					if (is_array($aparamv)) {
						foreach ($aparamv as $sparam=>$sparamv) {
							global $$sparam;
							$$sparam = $sparamv;		
						}
					} else {
						global $$aparam;
						$$aparam = $aparamv;	
					}		
				}				
			}
		}

		$f_select .= "
		<script>
			function reload(obj) {
				var index=document.forms[0].source_entrepot.options.selectedIndex;
				
					document.getElementById('connecteurId').value=obj.form.source_entrepot.options[obj.form.source_entrepot.options.selectedIndex].label;
					document.getElementById('subaction').value='change';
					obj.form.submit();
			}
		</script>";
		$f_select .= "<select id='source_entrepot' class='saisie-50em' name='source_entrepot' onchange='reload(this);'>";
		$f_select .="<option id='' label='' value='' >".$this->msg["planificateur_sync_choice"]."</option>";
		$contrs=new connecteurs();
		foreach ($contrs->catalog as $id=>$prop) {
			//Recherche du nombre de sources
			$n_sources=0;
			if (is_file($base_path."/admin/connecteurs/in/".$prop["PATH"]."/".$prop["NAME"].".class.php")) {
				require_once($base_path."/admin/connecteurs/in/".$prop["PATH"]."/".$prop["NAME"].".class.php");
				eval("\$conn=new ".$prop["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$prop["PATH"]."\");");
				$conn->get_sources();
				$n_sources=count($conn->sources);
			}
			if ($n_sources) {
				foreach($conn->sources as $id_source=>$s) {
					//entrepot synchronisable
					if ($s["REPOSITORY"]==1) {
						$f_select .="<option id='".$id_source."' label='".$id."' value='".$id_source."' ".($source_entrepot == $id_source ? "selected" : "").">".htmlentities($s["NAME"],ENT_QUOTES,$charset)."</option>";
					}
				}
			}
		}
		$f_select .= "</select>";
		$f_select .= "<input type='hidden' id='connecteurId' name='connecteurId' value='".$connecteurId."' />";
		//liste des entrepots synchronisable
		$form_task .= "
		<div class='row'> 
			<div class='colonne3'>
				<label for='entrepot'>".$this->msg["planificateur_sync_liste"]."</label>
			</div>
			<div class='colonne_suite'>".
				$f_select
			."</div>
		</div>";

		$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='source'>&nbsp;</label>
				</div>
				<div class='colonne_suite' id='synchro_source' >";
		if ($source_entrepot) {		
			if ($connecteurId) {
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."/".$contrs->catalog[$connecteurId]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connecteurId]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."\");");

				//Si on doit afficher un formulaire de synchronisation
				$syncr_form = $conn->form_pour_maj_entrepot($source_entrepot,"planificateur_form");			
				if ($syncr_form) {
					$form_task .= $syncr_form;
				}
			}
		}
		$form_task .= "</div>
			</div>
		<div class='row'>&nbsp;</div>	
		<div class='row'>
			<div class='colonne3'>
				<label for='auto_import'>".$this->msg["planificateur_sync_import"]."</label>
			</div>
			<div class='colonne_suite'>
				".$msg['40']."&nbsp;<input type='radio' name='auto_import' value='1' ".($auto_import ? "checked='checked'" : "")."/>&nbsp;".$msg['39']."&nbsp;<input type='radio' name='auto_import' value='0' ".($auto_import ? "" : "checked='checked'")."/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='auto_delete'>".$this->msg["planificateur_sync_delete"]."</label>
			</div>
			<div class='colonne_suite'>
				".$msg['40']."&nbsp;<input type='radio' name='auto_delete' value='1' ".($auto_delete ? "checked='checked'" : "")."/>&nbsp;".$msg['39']."&nbsp;<input type='radio' name='auto_delete' value='0' ".($auto_delete ? "" : "checked='checked'")."/>
			</div>
		</div>
		<div class='row'>&nbsp;</div>";
			
		return $form_task;
	}
	
	function task_execution() {
		global $base_path, $dbh, $msg, $PMBusername;				
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			if (file_exists($base_path."/admin/connecteurs/in/catalog_subst.xml")) 
				$catalog=$base_path."/admin/connecteurs/in/catalog_subst.xml";
			else
				$catalog=$base_path."/admin/connecteurs/in/catalog.xml";
				
			$xml=file_get_contents($catalog);
			$param=_parser_text_no_function_($xml,"CATALOG");
			
			$tparameters = $this->unserialize_task_params();
		
			if (isset($tparameters)) {
				if (is_array($tparameters)) {
					foreach ($tparameters as $aparameters=>$aparametersv) {
						if (is_array($aparametersv)) {
							foreach ($aparametersv as $sparameters=>$sparametersv) {
								global $$sparameters;
								$$sparameters = $sparametersv;
							}
						} else {
							global $$aparameters;
							$$aparameters = $aparametersv;						
						}
					}
				}
			}

			$this->id_source = $source_entrepot;
			if ($this->id_source) {
				$rqt = "select id_connector, name from connectors_sources where source_id=".$this->id_source;
				$res = pmb_mysql_query($rqt);
				$path = pmb_mysql_result($res,0,"id_connector");
				$name = pmb_mysql_result($res,0,"name");
				for ($i=0; $i<count($param["ITEM"]); $i++) {
					$item=$param["ITEM"][$i];
					if ($item["PATH"] == $path) {
						if ($item["ID"]) {
							$this->id_connector = $item["ID"];
							$result = array();
							$this->report[] = "<tr ><th>".$this->msg["report_sync"]." : ".$name."</th></tr>";
							if (method_exists($this->proxy, "pmbesSync_doSync")) {
								$result[] = $this->proxy->pmbesSync_doSync($this->id_connector, $this->id_source, $auto_import, $this->id_tache, array(&$this, "listen_commande"), array(&$this, "traite_commande"), $auto_delete);
								if ($result) {
									foreach ($result as $lignes) {
										foreach ($lignes as $ligne) {
											if ($ligne != '') {
												$htmlOutput = "<tr class='odd'>";
												$htmlOutput .= "<td >".$ligne."</td>";
												$htmlOutput .= "</tr>";
												$this->report[] = htmlentities($htmlOutput, ENT_QUOTES, $charset);
											}
										}
									}
								}
							} else {
								$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"]."</td></tr>","doSync","pmbesSync",$PMBusername);
							}	
						}
					}
				}
			} else {
				$this->report[] = "<tr><th>".$this->msg["report_sync"]." : ".$this->msg["report_sync_false"]."</th></tr>";
				$this->report[] = "<tr class='odd'><td>".$this->msg["error_parameters"]."</td></tr>";
			}
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
	}
		
	function traite_commande($cmd,$message) {
		global $msg;

		switch ($cmd) {
			case RESUME:
				$this->send_command(RUNNING);
				break;
			case SUSPEND:
				$this->suspend_sync();
				break;
			case STOP:
				$this->report[] = "<tr class='odd'><td>".$this->msg["planificateur_stop_sync"]."</td></tr>";				
				$this->finalize($this->id_tache);
				die();
				break;
			case ABORT:
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->report[] = "<tr class='odd'><td>".$this->msg["planificateur_abort_sync"]."</td></tr>";				
				$this->finalize($this->id_tache);
				die();
				break;
			case FAIL :
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->report[] = "<tr class='odd'><td>".$msg["planificateur_timeout_overtake"]."</td></tr>";				
				$this->finalize($this->id_tache);
				die();
				break;
		}	
	}
	
	function make_serialized_task_params() {
    	global $base_path, $source_entrepot, $connecteurId;
    	global $auto_import, $auto_delete;

    	$t = parent::make_serialized_task_params();

		if ($source_entrepot) {
			$t["source_entrepot"]=$source_entrepot;
			$t["connecteurId"]=$connecteurId;
			if ($connecteurId) {
				$contrs=new connecteurs();
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."/".$contrs->catalog[$connecteurId]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connecteurId]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."\");");

				//Propre au connecteur
				$t["envt"]=$conn->get_maj_environnement($source_entrepot);
			}
		}
		if($auto_import){
			$t['auto_import'] = ($auto_import ? true : false);
		}
		if($auto_delete){
			$t['auto_delete'] = ($auto_delete ? true : false);
		}

    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		return $params;
    }
    
	function suspend_sync() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->statut = $this->listen_commande(array(&$this,"traite_commande"));
		}
	}
	
    function flush_sync() {
//    	if ($id) {
//			$contrs=new connecteurs();
//			require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."/".$contrs->catalog[$id]["NAME"].".class.php");
//			eval("\$conn=new ".$contrs->catalog[$id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."\");");
//			if (($source_id)&&($conn)) { 
//				$conn->del_notices($source_id);
//			}
//			$sql = "UPDATE connectors_sources SET last_sync_date = '0000-00-00 00:00:00' WHERE source_id = $source_id ";
//			pmb_mysql_query($sql); 
//		}
    }
}