<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing.class.php,v 1.2 2015-04-03 11:16:29 jpermanne Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");

class mailing extends tache {
	
	function mailing($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $dbh, $PMBuserid;
		
		//paramètres pré-enregistré
		if ($param['mailtpl_id']) {
			$id_sel = $param['mailtpl_id'];
		} else {
			$id_sel=0;
		}
		if ($param['empr_caddie']) {
			$idemprcaddie_sel = $param['empr_caddie'];
		} else {
			$idemprcaddie_sel = 0;
		}
		
		$mailtpl = new mailtpls();

		//Choix du template de mail
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='mailing_template'>".$this->msg["planificateur_mailing_template"]."</label>
			</div>
			<div class='colonne_suite' >
				".$mailtpl->get_sel('mailtpl_id',$id_sel)."
			</div>
		</div>
		<div class='row' >&nbsp;</div>";
		
		$liste = empr_caddie::get_cart_list();
		$gen_select_empr_caddie = "<select name='empr_caddie' id='empr_caddie'>";
		if (sizeof($liste)) {
			while (list($cle, $valeur) = each($liste)) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
					if($valeur['idemprcaddie']==$idemprcaddie_sel){
						$gen_select_empr_caddie .= "<option value='".$valeur['idemprcaddie']."' selected='selected'>".$valeur['name']."</option>";
					} else {
						$gen_select_empr_caddie .= "<option value='".$valeur['idemprcaddie']."'>".$valeur['name']."</option>";
					}		
					
				}
			}	
		}
		$gen_select_empr_caddie .= "</select>";

		//Choix du panier d'emprunteurs
		$form_task .= "<div class='row'>
			<div class='colonne3'>
				<label for='mailing_caddie'>".$this->msg["planificateur_mailing_caddie_empr"]."</label>
			</div>
			<div class='colonne_suite'>
				".$gen_select_empr_caddie."
			</div>
		</div>";	
			
		return $form_task;
	}
	
	function task_execution() {
		global $dbh,$msg, $PMBusername;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$parameters = $this->unserialize_task_params();	
			if ($parameters['empr_caddie'] && $parameters['mailtpl_id']) {	
				$percent = 0;
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if($this->statut == RUNNING) {
					if (method_exists($this->proxy, 'pmbesMailing_sendMailingCaddie')) {
						$result = $this->proxy->pmbesMailing_sendMailingCaddie($parameters['empr_caddie'], $parameters['mailtpl_id']);
						if ($result) {
							$this->report[] = "<tr><td>
								<h1>$msg[empr_mailing_titre_resultat]</h1>
								<strong>$msg[admin_mailtpl_sel]</strong> 
								".htmlentities($result["name"],ENT_QUOTES,$charset)."<br />
								<strong>$msg[empr_mailing_form_obj_mail]</strong> 
								".htmlentities($result["object_mail"],ENT_QUOTES,$charset)."
								</td></tr>";
							
							$tpl_report = "<tr><td>
								<strong>$msg[empr_mailing_resultat_envoi]</strong>";
							$msg[empr_mailing_recap_comptes] = str_replace("!!total_envoyes!!", $result["nb_mail_sended"], $msg[empr_mailing_recap_comptes]) ;
							$msg[empr_mailing_recap_comptes] = str_replace("!!total!!", $result["nb_mail"], $msg[empr_mailing_recap_comptes]) ;
							$tpl_report .= $msg[empr_mailing_recap_comptes] ;
							
							$sql = "select id_empr, empr_mail, empr_nom, empr_prenom from empr, empr_caddie_content where flag='2' and empr_caddie_id=".$parameters['empr_caddie']." and object_id=id_empr ";
							$sql_result = pmb_mysql_query($sql) ;
							if (pmb_mysql_num_rows($sql_result)) {
								$tpl_report .= "<hr /><div class='row'>
									<strong>$msg[empr_mailing_liste_erreurs]</strong>  
									</div>";
								while ($obj_erreur=pmb_mysql_fetch_object($sql_result)) {
									$tpl_report .= "<div class='row'>
										".$obj_erreur->empr_nom." ".$obj_erreur->empr_prenom." (".$obj_erreur->empr_mail.") 
										</div>
										";
								}
							}
							$tpl_report .= "</td></tr>";

							$this->report[] = $tpl_report;
							$this->update_progression(100);
						}	
					} else {
						$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],"sendMailingCaddie","pmbesMailing",$PMBusername)."</td></tr>";
					}
				}
			} else {
				$this->report[] = "<tr><td>".$this->msg["mailing_unknown"]."</td></tr>";
			}
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
	}

	function make_serialized_task_params() {
    	global $empr_caddie, $mailtpl_id;
		$t = parent::make_serialized_task_params();
		
		$t["empr_caddie"] = $empr_caddie;
		$t["mailtpl_id"] = $mailtpl_id;

    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }
}