<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reader.class.php,v 1.3 2015-04-03 11:16:28 jpermanne Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");
require_once($class_path."/docs_location.class.php");

class reader extends tache {
	
	function reader($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
				
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $msg, $pmb_lecteurs_localises;		
		//paramètres pré-enregistré
		$lst_opt = array();
		if ($param['chk_reader']) {
			foreach ($param['chk_reader'] as $elem) {
				$lst_opt[$elem] = $elem;
			}
		}
		$loc_selected = ($param["empr_location_id"] ? $param["empr_location_id"] : "");
		$statut_selected = ($param["empr_statut_edit"] ? $param["empr_statut_edit"] : "");
		
		//Choix de l'action à réaliser
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_reader_abon"]."</label>
			</div>
			<div class='colonne_suite'>
			<input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche' ".(($lst_opt["reader_abon_fin_proche"] == "reader_abon_fin_proche")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche"]."
				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse' ".(($lst_opt["reader_abon_depasse"] == "reader_abon_depasse")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse"]."";
				
//				<input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche_mail' ".(($lst_opt["reader_abon_fin_proche_mail"] == "reader_abon_fin_proche_mail")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche_mail"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_fin_proche_pdf' ".(($lst_opt["reader_abon_fin_proche_pdf"] == "reader_abon_fin_proche_pdf")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_fin_proche_pdf"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse_mail' ".(($lst_opt["reader_abon_depasse_mail"] == "reader_abon_depasse_mail")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse_mail"]."
//				<br /><input type='checkbox' name='chk_reader[]' value='reader_abon_depasse_pdf' ".(($lst_opt["reader_abon_depasse_pdf"] == "reader_abon_depasse_pdf")  ? "checked" : "")."/>".$this->msg["planificateur_reader_abon_depasse_pdf"]."
			$form_task .= "</div>
		</div>
		<div class='row'>&nbsp;</div>";	
		
		//Choix de la localisation
		if ($pmb_lecteurs_localises) {
			$form_task .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_reader_loc"]."</label>
				</div>
				<div class='colonne_suite'>".
				docs_location::gen_combo_box_empr($loc_selected)."
				</div>
			</div>
			<div class='row'>&nbsp;</div>";
		}
		
		//Choix du statut
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_reader_statut"]."</label>
			</div>
			<div class='colonne_suite'>".
			gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$statut_selected,"","",0,$msg["all_statuts_empr"])."
			</div>
		</div>";
		
		return $form_task;
	}
		
	function task_execution() {
		global $dbh,$msg, $PMBusername;
		global $empr_relance_adhesion;
		
		//requete
		$rqt = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
			left join taches t on t.num_planificateur = p.id_planificateur
			left join tache_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
			where t.id_tache=".$id_tache;
		$res_query = pmb_mysql_query($rqt, $dbh);
		
		$parameters = $this->unserialize_task_params();
		
		if ($parameters["chk_reader"]) {
			$empr_location_id = ($parameters["empr_location_id"] ? $parameters["empr_location_id"] : "0");
			if ($empr_location_id != "0") {
				$query = "select name from docs_location where idlocation=".$empr_location_id;
				$res = pmb_mysql_query($query, $dbh);
				if ($res) {
					$location_name = pmb_mysql_result($res,0,"name");
				}
			}
			$empr_statut_edit = ($parameters["empr_statut_edit"] ? $parameters["empr_statut_edit"] : "0");
			if ($empr_statut_edit != "0") {
				$query = "select statut_libelle from empr_statut where idstatut=".$empr_statut_edit;
				$res = pmb_mysql_query($query, $dbh);
				if ($res) {
					$statut_name = pmb_mysql_result($res,0,"statut_libelle");
				}
			}
			$count = count($parameters["chk_reader"]);
			$percent = 0;
			$p_value = (int) 100/$count;
			$this->report[] = "<tr><th>".$this->msg["reader_relance"]."</th></tr>";
			foreach ($parameters["chk_reader"] as $elem) {
				//traitement des options choisies
				switch ($elem) {
					case "reader_abon_fin_proche" :
						//Lecteurs en fin d'abonnement (proche)
						$this->report[] = "<tr><th>".$this->msg["reader_relance_abon_fin_proche"]." ".($location_name ? "(".$location_name.")" : "")." ".($statut_name ? " ".$msg[297]." : ".$statut_name : "")."</th></tr>";
						if (method_exists($this->proxy, "pmbesReaders_listReadersSubscription")) {
							$results = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);
							if ($results) {
								if ($empr_relance_adhesion == "0") {
									if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
										$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($results,$empr_location_id);
									} else {
										$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"relanceReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
									}
								} else if ($empr_relance_adhesion == "1") {
									//envoi de mail, à défaut lettre
									$tab_no_mail=array();
									foreach ($results as $aresult) {
										if ($aresult["empr_mail"] != '') {
											$text = $this->proxy->pmbesReaders_generateMailReadersSubscription($aresult["id_empr"],$empr_location_id);
//											generateMailReadersEndSubscription($ligne["id_empr"],$empr_location_id);	
										} else {
											$tab_no_mail[] = $aresult;
										}
									}
									if ($tab_no_mail) {
										if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
											$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($tab_no_mail,$empr_location_id);
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"relanceReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
										}
									}
								}
								if ($object_fpdf) {
									//génération du pdf
									$this->generate_docnum($object_fpdf);
								}
							} else {
								$this->report[] = "<tr><td>".$this->msg["reader_no_result"]."</td></tr>";
							}
						} else {
							$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"listReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
						}
						break;
					case "reader_abon_depasse" :
						//Lecteurs dont l'abonnement est dépassé
						$this->report[] = "<tr><th>".$this->msg["reader_relance_abon_depassee"]." ".($location_name ? "(".$location_name.")" : "")." ".($statut_name ? " ".$msg[297]." : ".$statut_name : "")."</th></tr>";
						if (method_exists($this->proxy, "pmbesReaders_listReadersSubscription")) {
							$results = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
							if ($results) {
								if ($empr_relance_adhesion == "0") {
									if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
										$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($results,$empr_location_id);
									} else {
										$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"relanceReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
									}
								} else if ($empr_relance_adhesion == "1") {
									//envoi de mail, à défaut lettre
									$tab_no_mail=array();
									foreach ($results as $aresult) {
										if ($aresult["empr_mail"] != '') {
											if (method_exists($this->proxy, "pmbesReaders_generateMailReadersSubscription")) {
												$text = $this->proxy->pmbesReaders_generateMailReadersSubscription($aresult["id_empr"],$empr_location_id);
//												generateMailReadersExceedSubscription($ligne["id_empr"],$empr_location_id);	
											} else {
												$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"generateMailReadersExceedSubscription","pmbesReaders",$PMBusername)."</td></tr>";
											}
										} else {
											$tab_no_mail[] = $aresult;
										}
									}
									if ($tab_no_mail) {
										if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
											$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($tab_no_mail,$empr_location_id);
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"relanceReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
										}
									}
								}
								if ($object_fpdf) {
									//génération du pdf
									$this->generate_docnum($object_fpdf);
								}
							} else {
								$this->report[] = "<tr><td>".$this->msg["reader_no_result"]."</td></tr>";
							}
						} else {
							$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"listReadersSubscription","pmbesReaders",$PMBusername)."</td></tr>";
						}
						break;
					
					
//					case "reader_abon_fin_proche_mail":
//						//Lecteurs en fin d'abonnement (proche) => envoi de mail
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);			
//						
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
//									$this->report[] = "<tr><td>".$msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]."</td></tr>";
//									$text = $this->proxy->pmbesReaders_generateMailReadersEndSubscription($ligne["id_empr"],$empr_location_id);	
//								}
//							}
//						} else {
//							$this->report[] = "<tr><td>".$msg["planificateur_result_not_found"]."</td></tr>";
//						}
//						break;
//					case "reader_abon_fin_proche_pdf":
//						//Lecteurs en fin d'abonnement (proche) => generation de pdf
////						if (method_exists($this->proxy, 'pmbesReaders_listReadersSubscription')) {
//							$result = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);		
////						}
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
//									$this->report[] = "<tr><td>".$msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]."</td></tr>";
//									$object_fpdf = $this->proxy->pmbesReaders_generatePdfReadersSubscription($ligne["id_empr"],$empr_location_id);	
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->report[] = "<tr><td>".$msg["planificateur_result_not_found"]."</td></tr>";
//						}
//						break;
//					case "reader_abon_depasse_mail":
//						//Avertissement des abonnements expirés par mail
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
//									$this->report[] = "<tr><td>".$msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]."</td></tr>";
//	//								get_texts(1);
//									$text = $this->proxy->pmbesReaders_generateMailReadersExceedSubscription($ligne["id_empr"],$empr_location_id);	
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->report[] = "<tr><td>".$msg["planificateur_result_not_found"]."</td></tr>";
//						}
//						break;
//					case "reader_abon_depasse_pdf":
//						//Génération pdf des abonnements expirés
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
//									$this->report[] = "<tr><td>".$msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]."</td></tr>";
//	//								get_texts(1);
//									$object_fpdf = $this->proxy->pmbesReaders_generatePdfReadersSubscription($ligne["id_empr"],$empr_location_id);		
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->report[] = "<tr><td>".$msg["planificateur_result_not_found"]."</td></tr>";
//						}
//						break;
				}
				$percent = $percent + $p_value;
				$this->update_progression($percent);	
			}
		} else {
			$this->report[] = "<tr><td>".$this->msg["reader_no_option"]."</td></tr>";
		}	
	}
	
	function traite_commande($cmd,$message) {		
		switch ($cmd) {
			case RESUME:
				$state = $this->send_command(WAITING);
				break;
			case SUSPEND:
				break;
			case STOP:
				$this->finalize();
				break;
			case ABORT:
				break;				
		}
	}
    
	function make_serialized_task_params() {
    	global $chk_reader,$empr_location_id,$empr_statut_edit;
		
    	$t = parent::make_serialized_task_params();
		
		if (!empty($chk_reader)) {
			for ($i=0; $i<count($chk_reader); $i++) {
				$t["chk_reader"]=$chk_reader;				
			}
		}
		$t["empr_location_id"] = $empr_location_id;
		$t["empr_statut_edit"] = $empr_statut_edit;

    	return serialize($t);
	}
	

	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }
		
}