<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_late.class.php,v 1.3.2.2 2019-11-28 15:04:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/mail/reader/loans/mail_reader_loans.class.php");

class mail_reader_loans_late extends mail_reader_loans {
	
	protected static $niveau_relance;
	
	protected static function get_parameter_prefix() {
		return "mailretard";
	}
	
	protected function get_parameter_value($name) {
		if(isset(static::$niveau_relance)) {
			$parameter_name = static::get_parameter_prefix().'_'.static::$niveau_relance.$name;
			$parameter_value = $this->get_evaluated_parameter($parameter_name);
			if(!empty($parameter_value)) {
				return $parameter_value;
			}
		}
		$parameter_name = static::get_parameter_prefix().'_1'.$name;
		$parameter_value = $this->get_evaluated_parameter($parameter_name);
		if($parameter_value) {
		    return $parameter_value;
		} else {
		    return parent::get_parameter_value($name);
		}
	}
	
	protected function _init_default_parameters() {
	    parent::_init_default_parameters();
	    $this->_init_parameter_value('list_order', 'pret_date');
	}
	
	protected function get_mail_object() {
		return $this->get_parameter_value('objet');
	}
	
	protected function get_query_list_order() {
	    if (!empty($this->get_parameter_value('list_order'))) {
    	    return "order by ".$this->get_parameter_value('list_order');
	    }
	    return '';
	}
	
	protected function get_query_list($id_empr) {
	    return $this->get_query_list_base()." where pret_idempr='".$id_empr."' and pret_retour < curdate() ".$this->get_query_list_order();
	}
	
	protected function get_mail_content($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
		
		$mail_content = '';
		if($this->get_parameter_value('madame_monsieur')) {
			$mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
		}
		if($this->get_parameter_value('before_list')) {
			$mail_content .= $this->get_parameter_value('before_list')."\r\n\r\n";
		}
		
		//Récupération des exemplaires
		$query = $this->get_query_list($id_empr);
		$result = pmb_mysql_query($query);
		
		while ($data = pmb_mysql_fetch_array($result)) {
			$mail_content .= $this->get_mail_expl_content($data['expl_cb']);
		}
		$mail_content .= "\r\n";
		if($this->get_parameter_value('after_list')) {
			$mail_content .= $this->get_parameter_value('after_list')."\r\n\r\n";
		}
		if($this->get_parameter_value('fdp')) {
			$mail_content .= $this->get_parameter_value('fdp')."\r\n\r\n";
		}
		$mail_content .= $this->get_mail_bloc_adresse() ;
		return $mail_content;
	}
	
	protected function get_resp_coords($id_empr) {
		//Si mail de rappel affecté au responsable du groupe
		$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=$id_empr and resp_groupe and mail_rappel limit 1";
		$res=pmb_mysql_query($requete);
		/* Récupération du nom, prénom et mail du lecteur destinataire */
		if(pmb_mysql_num_rows($res) > 0) {
			$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
			$result=pmb_mysql_query($requete);
			$coords_dest=pmb_mysql_fetch_object($result);
		} else {
			$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr=$id_empr";
			$result=pmb_mysql_query($requete);
			$coords_dest=pmb_mysql_fetch_object($result);
		}
		return $coords_dest;
	}
	
	public function send_mail($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
		global $biblio_name, $biblio_email, $PMBuseremailbcc;
		
		$coords_dest = $this->get_resp_coords($id_empr);
		$coords = $this->get_empr_coords($id_empr, $id_groupe);
		$headers = "Content-type: text/plain; charset=".$charset."\n";
		$mail_content = $this->get_mail_content($id_empr, $id_groupe);
		
		//remplacement nom et prenom
		$mail_content=str_replace("!!empr_name!!", $coords->empr_nom,$mail_content);
		$mail_content=str_replace("!!empr_first_name!!", $coords->empr_prenom,$mail_content);
		
		if($coords_dest->empr_mail) {
    		$res_envoi=mailpmb($coords_dest->empr_prenom." ".$coords_dest->empr_nom, $coords_dest->empr_mail, $this->get_mail_object()." : ".$coords->empr_prenom." ".mb_strtoupper($coords->empr_nom,$charset)." (".$coords->empr_cb.")",$mail_content, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);	
    		if ($res_envoi) echo "<h3>".sprintf($msg["mail_retard_succeed"],$coords_dest->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($mail_content);
    		else echo "<h3>".sprintf($msg["mail_retard_failed"],$coords_dest->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
		} else {
		    echo "<h3>".sprintf($msg["mail_retard_unknown_mail"],$coords_dest->empr_prenom." ".$coords_dest->empr_nom)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
		}
	}
	
	public static function set_niveau_relance($niveau_relance) {
		static::$niveau_relance = $niveau_relance;
	}
}