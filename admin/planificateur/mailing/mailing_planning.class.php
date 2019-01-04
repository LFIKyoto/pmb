<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_planning.class.php,v 1.2 2017-08-18 15:29:02 jpermanne Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");

class mailing_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $dbh, $PMBuserid;
		
		//paramètres pré-enregistré
		if (isset($param['mailtpl_id'])) {
			$id_sel = $param['mailtpl_id']+0;
		} else {
			$id_sel=0;
		}
		if (isset($param['empr_caddie'])) {
			$idemprcaddie_sel = $param['empr_caddie']+0;
		} else {
			$idemprcaddie_sel = 0;
		}
		if (isset($param['email_cc'])) {
			$email_cc = trim($param['email_cc']);
		} else {
			$email_cc = "";
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

		//Destinataire supplémentaire
		$form_task .= "<div class='row'>
			<div class='colonne3'>
				<label for='mailing_caddie'>".$this->msg["planificateur_mailing_email_cc"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-30em' name='email_cc' id='email_cc' value='".$email_cc."'>
			</div>
		</div>";
			
		return $form_task;
	}

	public function make_serialized_task_params() {
    	global $empr_caddie, $mailtpl_id, $email_cc;
		$t = parent::make_serialized_task_params();
		
		$t["empr_caddie"] = $empr_caddie;
		$t["mailtpl_id"] = $mailtpl_id;
		$t["email_cc"] = $email_cc;

    	return serialize($t);
	}
}