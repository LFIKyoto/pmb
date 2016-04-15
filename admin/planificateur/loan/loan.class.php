<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loan.class.php,v 1.3 2015-04-03 11:16:28 jpermanne Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");
//require_once($class_path."/docs_location.class.php");
require_once($class_path."/filter_list.class.php");
require_once($include_path."/relance_func.inc.php");
//require_once($class_path."/amende.class.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class loan extends tache {
	
	function loan($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $msg, $pmb_lecteurs_localises, $empr_sort_rows, $empr_show_rows, $empr_filter_rows, $deflt2docs_location;
			
//		//paramètres pré-enregistré
//		$lst_opt = array();
//		if ($param['chk_loan']) {
//			foreach ($param['chk_loan'] as $elem) {
//				$lst_opt[$elem] = $elem;
//			}
//		}
//		$loc_selected = ($param["empr_location_id"] ? $param["empr_location_id"] : "");
		
		//Automatisation sur les prêts
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_loan_generate"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='radio' name='chk_loan' value='".LOAN_ALL_ACTIONS."' ".(($param["chk_loan"] == LOAN_ALL_ACTIONS) ? "checked" : "")."/>".$this->msg["loan_all_actions"]."
				<br /><input type='radio' name='chk_loan' value='".LOAN_PRINT_MAIL."' ".(($param["chk_loan"] == LOAN_PRINT_MAIL) || (!$param["chk_loan"])  ? "checked" : "")."/>".$this->msg["loan_print_mail"]."
				<br /><input type='radio' name='chk_loan' value='".LOAN_CSV_MAIL."' ".(($param["chk_loan"] == LOAN_CSV_MAIL)  ? "checked" : "")."/>".$this->msg["loan_csv_mail"]."
			</div>
		</div>
		<div class='row'>&nbsp;</div>";	
	
		if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
			if ($pmb_lecteurs_localises) $localisation=",l";
			$filter=new filter_list("empr","empr_list","b,n,c,g","b,n,c,g".$localisation.",2,3,cs","n,g");
			if ($pmb_lecteurs_localises) {
				$lo="f".$filter->fixedfields["l"]["ID"];
				global $$lo;
				if (!$$lo) {
					$tableau=array();
					$tableau[0]=$deflt2docs_location;
					$$lo=$tableau;
				}
			}
			$filter->fixedcolumns="b,n,c";
			$filter->original_query=$requete;
			$filter->multiple=1;

			$filter->select_original="table_filter_tempo.empr_nb,empr_mail";
			$filter->original_query="select id_empr,count(pret_idexpl) as empr_nb from empr,pret where pret_retour<now() and pret_idempr=id_empr group by empr.id_empr";
			$filter->from_original="";
			$filter->activate_filters();
			if (!$filter->error) {
				$t_filters = explode(",",$filter->filtercolumns);
				foreach ($t_filters as $i=>$f) {
					if ((substr($s[$i],0,1)=="#")&&($filter->params["REFERENCE"][0]["DYNAMICFIELDS"]=="yes")) {
						//Faut-il adapter les champs perso ??
						
					} elseif (array_key_exists($t_filters[$i],$filter->fixedfields)) {
						$filters_selectors="f".$filter->fixedfields[$f]["ID"];
					} else {
						$filters_selectors="f".$filter->specialfields[$f]["ID"];
					}
					
					global $$filters_selectors;
					if ($param[$filters_selectors]) {
						$tableau=array();
						foreach ($param[$filters_selectors] as $categ) {
							$tableau[$categ] = $categ;
						}
						$$filters_selectors = $tableau;
					}
				}
			
				$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_loan_filters"]."</label>
				</div>
				<div class='colonne_suite'>
					".$filter->display_filters()."
					</div>
				</div>
				<div class='row'>&nbsp;</div>";
				
				$t_sort = explode(",",$filter->sortablecolumns);
				//parcours des selecteurs de tris 
	    		for ($j=0;$j<=count($t_sort)-1;$j++) {
	    			$sort_selector="sort_list_".$j;
	    			global $$sort_selector;
					if ($param[$sort_selector]) {
						$$sort_selector = $param[$sort_selector];
					}
	    		}
				$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_loan_tris"]."</label>
				</div>
				<div class='colonne_suite'>
					".$filter->display_sort()."
					</div>
				</div>
				<div class='row'>&nbsp;</div>";
			} else {
				$form_task .= $filter->error_message;
			}
		}
		return $form_task;
	}
		
	function task_execution() {
		global $dbh,$msg,$PMBusername,$pmb_lecteurs_localises,$empr_filter_rows,$empr_sort_rows,$empr_show_rows;
				
		//& (RESTRICTCIRC_AUTH)
		if (SESSrights & CIRCULATION_AUTH) {
			//requete pour la construction du pdf
			$rqt = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
				left join taches t on t.num_planificateur = p.id_planificateur
				left join tache_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
				where t.id_tache=".$id_tache;
			$res_query = pmb_mysql_query($rqt, $dbh);
		
			$parameters = $this->unserialize_task_params();

			if ($parameters["chk_loan"]) {
				$option = $parameters["chk_loan"];
//				$count = count($parameters["chk_loan"]);
//				$percent = 0;
//				$p_value = (int) 100/$count;
//				foreach ($parameters["chk_loan"] as $elem) {
//					$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
//					if($this->statut == WAITING) {
//						$this->send_command(RUNNING);
//					}
				if ($this->statut == RUNNING) {
					$this->report[] = "<tr><th>".$this->msg["loan_relance"]."</th></tr>";
					$results=array();
					if (method_exists($this->proxy, "pmbesLoans_filterLoansReaders")) {
						$results[] = $this->proxy->pmbesLoans_filterLoansReaders("empr","empr_list","b,n,c,g","b,n,c,g,2,3,cs","n,g",$parameters);
						$t_empr = array();
						if ($results) {
							foreach ($results as $result) {
								$t_empr[] = $result["id_empr"];
							}
							//Au minimum 1 emprunteur dans le tableau pour poursuivre..
							if (count($t_empr) > 0) {
								//traitement des options choisies
								switch ($option) {
									case LOAN_ALL_ACTIONS :
										//Comment connaître le niveau à valider ??
										$this->report[] = "<tr><th>".$this->msg["loan_all_actions"]."</th></tr>";
										foreach ($results as $result) {
											if ($result["id_empr"] != "") {
//												$this->proxy->fonction_pour_valider_action
											}
										}
										
										break;
									case LOAN_PRINT_MAIL :
										$this->report[] = "<tr><th>".$this->msg["loan_print_mail"]."</th></tr>";
										if(method_exists($this->proxy, "pmbesLoans_relanceLoansReaders")) {
											if(method_exists($this->proxy, "pmbesLoans_buildPdfLoansDelayReaders")) {
												if ($this->isUploadValide()) {
													$not_all_mail = $this->proxy->pmbesLoans_relanceLoansReaders($t_empr);
													if ($not_all_mail) {
														$object_fpdf = $this->proxy->pmbesLoans_buildPdfLoansDelayReaders($t_empr, "", "");
														if ($object_fpdf) {
															$this->generate_docnum($object_fpdf,"application/pdf","pdf");
														}
													} else {
														$this->report[] = "<tr><td>".$this->msg["loan_no_letter"]."</td></tr>";
													}
												} else {
													$this->report[] = "<tr><td>Le chemin du répertoire d'upload est invalide ou protégé en écriture</td></tr>";
												}
											} else {
												$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"buildPdfLoansDelayReaders","pmbesLoans",$PMBusername)."</td></tr>";
											}
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"relanceLoansReaders","pmbesLoans",$PMBusername)."</td></tr>";
										}
										
										break;
									case LOAN_CSV_MAIL :
										$this->report[] = "<tr><th>".$this->msg["loan_csv_mail"]."</th></tr>";
										if (method_exists($this->proxy, "pmbesLoans_exportCSV")) {
											if ($this->isUploadValide()) {
												$content_csv = $this->proxy->pmbesLoans_exportCSV($t_empr);
												$this->generate_docnum($content_csv,"application/ms-excel","xls");
											} else {
												$this->report[] = "<tr><td>Le chemin du répertoire d'upload est invalide ou protégé en écriture</td></tr>";
											}
										} else {
											$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"exportCSV","pmbesLoans",$PMBusername)."</td></tr>";
										}
										break;
								}
							} else {
								$this->report[] = "<tr><td>".$this->msg["loan_no_empr"]."</td></tr>";
							}
						}
					} else {
						$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"filterLoansReaders","pmbesLoans")."</td></tr>";
					}
				}			
//				$percent = $percent + $p_value;
				$percent = 100;
				$this->update_progression($percent);	
			} else {
				$this->report[] = "Aucune option choisie !";
			}
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
	}
	
	//inutilisé..
	function traite_commande($cmd,$message) {		
		switch ($cmd) {
			case RESUME:
				$this->send_command(WAITING);
				break;
			case SUSPEND:
				$this->suspend_loan();
				break;
			case STOP:
				$this->finalize();
				break;
			case RETRY:
				break;				
		}
	}
    
	function make_serialized_task_params() {
    	global $chk_loan,$empr_location_id;
    	global $f6, $f8, $f5, $f11, $f2, $f3;
    	global $sort_list_0, $sort_list_1;
    	
		$t = parent::make_serialized_task_params();
		
		if ($chk_loan) {
			$t["chk_loan"]=$chk_loan;				
		}
		if (!empty($f6)) {
			for ($i=0; $i<count($f6); $i++) {
				$t["f6"]=$f6;				
			}
		}
		if (!empty($f8)) {
			for ($i=0; $i<count($f8); $i++) {
				$t["f8"]=$f8;				
			}
		}
		if (!empty($f11)) {
			for ($i=0; $i<count($f11); $i++) {
				$t["f11"]=$f11;				
			}
		}
		if (!empty($f5)) {
			for ($i=0; $i<count($f5); $i++) {
				$t["f5"]=$f5;				
			}
		}
		if (!empty($f2)) {
			for ($i=0; $i<count($f2); $i++) {
				$t["f2"]=$f2;				
			}
		}
		if (!empty($f3)) {
			for ($i=0; $i<count($f3); $i++) {
				$t["f3"]=$f3;				
			}
		}
		$t["sort_list_0"] = $sort_list_0;
		$t["sort_list_1"] = $sort_list_1;
		$t["empr_location_id"] = $empr_location_id;

    	return serialize($t);
	}
		
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }
    
	function suspend_loan() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
		
}