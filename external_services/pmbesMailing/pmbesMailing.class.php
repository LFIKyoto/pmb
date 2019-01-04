<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesMailing.class.php,v 1.3 2017-08-18 15:29:02 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesMailing extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	public function sendMailingCaddie($id_caddie_empr, $id_tpl, $email_cc = '') {

		$id_caddie_empr += 0;
		if (!$id_caddie_empr)
			throw new Exception("Missing parameter: id_caddie_empr");
		$id_tpl +=0;
		if (!$id_tpl)
			throw new Exception("Missing parameter: id_tpl");
	
		$result = array();
		if (SESSrights & CIRCULATION_AUTH) {
			if ($id_caddie_empr && $id_tpl) {
				$mailtpl = new mailtpl($id_tpl);
				$objet_mail = $mailtpl->info['objet'];
				$message = $mailtpl->info['tpl'];

				$mailing = new mailing_empr($id_caddie_empr, $email_cc);
				$mailing->send($objet_mail, $message);
				
				$result["name"] = $mailtpl->info['name'];
				$result["object_mail"] = $objet_mail;
				$result["nb_mail"] = $mailing->total;
				$result["nb_mail_sended"] = $mailing->total_envoyes;
				$result["nb_mail_failed"] = $mailing->envoi_KO;
			}
		}
		return $result;
	}
}	




?>