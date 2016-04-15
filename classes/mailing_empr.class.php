<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_empr.class.php,v 1.9 2015-06-11 14:17:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/mailing.inc.php");
require_once($include_path."/mail.inc.php");

class mailing_empr {
	var $id_caddie_empr;
	var $total = 0;
	var $total_envoyes = 0;
	var $envoi_KO = 0;
	
	function mailing_empr($id_caddie_empr=0) {
		$this->id_caddie_empr = $id_caddie_empr;
	}
	
	function send($objet_mail, $message, $paquet_envoi=0,$pieces_jointes=array()) {
		global $charset, $msg;
		global $pmb_mail_delay, $pmb_mail_html_format, $pmb_img_url, $pmb_img_folder;
		global $PMBuserprenom, $PMBusernom, $PMBuseremail, $PMBuseremailbcc;
		global $opac_connexion_phrase;

		if ($this->id_caddie_empr) {
			// ajouter les tags <html> si besoin :
			if (strpos("<html>",substr($message,0,20))===false) $message="<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\"></head><body>$message</body></html>";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1";

			if (!$this->total) {
				$sql = "select 1 from empr_caddie_content where (flag='' or flag is null or flag=2) and empr_caddie_id=".$this->id_caddie_empr;
				$sql_result = pmb_mysql_query($sql) or die ("Couldn't select count(*) mailing table $sql");
				$this->total=pmb_mysql_num_rows($sql_result);
			}
			$sql = "select *, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration from empr, empr_caddie_content where (flag='' or flag is null) and empr_caddie_id=".$this->id_caddie_empr." and object_id=id_empr ";
			if ($paquet_envoi) $sql .= " limit 0,$paquet_envoi ";
			$sql_result = pmb_mysql_query($sql) or die ("Couldn't select empr table !");
			$n_envoi=pmb_mysql_num_rows($sql_result);
			$ienvoi=0;
			$this->envoi_KO=0;
			
			//On n'envoie en BCC que pour le premier email
			$envoiBcc=false;
			$resBcc=pmb_mysql_query("SELECT * FROM empr_caddie_content WHERE flag='1' AND empr_caddie_id=".$this->id_caddie_empr);
			if($resBcc && pmb_mysql_num_rows($resBcc)){
				//On a déjà fait une passe précédemment, on ne renvoie pas en BCC
				$envoiBcc=true;
			}
			
			while ($ienvoi<$n_envoi) {
				$destinataire=pmb_mysql_fetch_object($sql_result);
				$iddest=$destinataire->id_empr;
				$emaildest=$destinataire->empr_mail;
				$nomdest=$destinataire->empr_nom;
				if ($destinataire->empr_prenom) $nomdest=$destinataire->empr_prenom." ".$destinataire->empr_nom; 
				$message_to_send = $message;
				$message_to_send=str_replace("!!empr_name!!", $destinataire->empr_nom,$message_to_send); 
				$message_to_send=str_replace("!!empr_first_name!!", $destinataire->empr_prenom,$message_to_send);
				switch ($destinataire->empr_sexe) {
					case "2":
						$empr_civilite = $msg["civilite_madame"];
						break;
					case "1":
						$empr_civilite = $msg["civilite_monsieur"];
						break;
					default:
						$empr_civilite = $msg["civilite_unknown"];
						break;
				}
				$message_to_send=str_replace('!!empr_sexe!!',$empr_civilite,$message_to_send);
				$message_to_send=str_replace("!!empr_cb!!", $destinataire->empr_cb,$message_to_send);
				$message_to_send=str_replace("!!empr_login!!", $destinataire->empr_login,$message_to_send); 
				$message_to_send=str_replace("!!empr_mail!!", $destinataire->empr_mail,$message_to_send);
				if (strpos($message_to_send,"!!empr_loans!!")) $message_to_send=str_replace("!!empr_loans!!", m_liste_prets($destinataire),$message_to_send);
				if (strpos($message_to_send,"!!empr_resas!!")) $message_to_send=str_replace("!!empr_resas!!", m_liste_resas($destinataire),$message_to_send);
				if (strpos($message_to_send,"!!empr_name_and_adress!!")) $message_to_send=str_replace("!!empr_name_and_adress!!", nl2br(m_lecteur_adresse($destinataire)),$message_to_send);
				if (strpos($message_to_send,"!!empr_all_information!!")) $message_to_send=str_replace("!!empr_all_information!!", nl2br(m_lecteur_info($destinataire)),$message_to_send);
				$dates = time();
				$login = $destinataire->empr_login;
				$code=md5($opac_connexion_phrase.$login.$dates);
				if (strpos($message_to_send,"!!code!!")) $message_to_send=str_replace("!!code!!", $code,$message_to_send);
				if (strpos($message_to_send,"!!login!!")) $message_to_send=str_replace("!!login!!", $login,$message_to_send);
				if (strpos($message_to_send,"!!date_conex!!")) $message_to_send=str_replace("!!date_conex!!", $dates,$message_to_send);
				//générer le corps du message
				if ($pmb_mail_html_format==2){
					// transformation des url des images pmb en chemin absolu ( a cause de tinyMCE ) 
					preg_match_all("/(src|background)=\"(.*)\"/Ui", $message_to_send, $images);
				    if(isset($images[2])) {
				      	foreach($images[2] as $i => $url) {
				        	$filename  = basename($url);
				        	$directory = dirname($url);
				        	if(urldecode($directory."/")==$pmb_img_url){
					        	$newlink=$pmb_img_folder .$filename;
					        	$message_to_send = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$newlink."\"", $message_to_send);
				        	}
				      	}
				    }
				}
				if(!$envoiBcc){
					$bcc=$PMBuseremailbcc;
				}else{
					$bcc="";
				}
				$envoi_OK = mailpmb($nomdest, $emaildest, $objet_mail, $message_to_send, $PMBuserprenom." ".$PMBusernom, $PMBuseremail, $headers, "", $bcc, 0, $pieces_jointes) ;
				if ($pmb_mail_delay*1) sleep((int)$pmb_mail_delay*1/1000);
				if ($envoi_OK) {
					$envoiBcc=true;
					pmb_mysql_query("update empr_caddie_content set flag='1' where object_id='".$iddest."' and empr_caddie_id=".$this->id_caddie_empr) or die ("Couldn't update empr_caddie_content !");
				} else {
					pmb_mysql_query("update empr_caddie_content set flag='2' where object_id='".$iddest."' and empr_caddie_id=".$this->id_caddie_empr) or die ("Couldn't update empr_caddie_content !");
					$this->envoi_KO++;
				}
				$ienvoi++;
			}
			$this->total_envoyes=(($this->total_envoyes+$ienvoi)*1)-$this->envoi_KO;
		}
	}	
} //mailing_empr class end

	
