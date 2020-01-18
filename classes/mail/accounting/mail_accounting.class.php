<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_accounting.class.php,v 1.1.4.1 2019-11-28 15:04:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/mail/mail_root.class.php");
require_once ($include_path."/mail.inc.php") ;

class mail_accounting extends mail_root {
	
    protected $id_bibli;
    protected $id_acte;
    protected $acte;
    protected $bib;
    protected $coord_liv;
    protected $coord_fac;
    protected $coord_fou;
    
    protected $dest_name;
    protected $dest_mail;
    
	protected function get_mail_object() {
	    $mail_object = $this->get_parameter_value('obj_mail');
	    return static::render($mail_object, $this->get_formatted_data());
	}
	
	protected function get_mail_content() {
	    $mail_content = $this->get_parameter_value('text_mail');
	    return static::render($mail_content, $this->get_formatted_data());
	}
	
	protected function get_attachments($id_bibli,$id_acte) {
	    return array();
	}
	
	public function send_mail($id_bibli, $id_acte) {
		global $charset;
		global $PMBuseremailbcc;
	    
		$this->id_bibli = $id_bibli;
		$this->id_acte = $id_acte;
		$bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($this->id_bibli,1));
		$acte = new actes($this->id_acte);
	    
	    $id_fou = $acte->num_fournisseur;
	    $fou = new entites($id_fou);
	    $fou_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_fou,1));
	    $this->dest_name='';
	    if($fou_coord->libelle) {
	        $this->dest_name = $fou_coord->libelle;
	    } else {
	        $this->dest_name = $fou->raison_sociale;
	    }
	    if($fou_coord->contact) $this->dest_name.=" ".$fou_coord->contact;
	    $this->dest_mail=$fou_coord->email;
	    $bib_name = $bib_coord->libelle;
	    $bib_mail = $bib_coord->email;
		$headers = "Content-Type: text/plain; charset=".$charset."\n";
		$mail_content = $this->get_mail_content();
		$attachments = $this->get_attachments($this->id_bibli, $this->id_acte);
		if($this->dest_mail) {
		    $res_envoi=mailpmb($this->dest_name, $this->dest_mail, $this->get_mail_object(), $mail_content ,$bib_name, $bib_mail, $headers, '', $PMBuseremailbcc, 1, $attachments);
		} else {
		    $res_envoi=false;
		}
		return $res_envoi;
	}
	
	public function get_formatted_data(){
	    if(empty($this->formatted_data)){
	        $this->formatted_data = array();
	        $this->formatted_data = array(
	            'obj_mail' => $this->get_parameter_value('obj_mail'),
	            'text_before' => $this->get_parameter_value('text_before'),
	            'text_after' => $this->get_parameter_value('text_after'),
	            'text_sign' => $this->get_parameter_value('text_sign'),
	            'acte' => $this->get_acte(),
	            'bib' => $this->get_bib(),
	            'fou' => $this->get_fou(),
	            'coord_liv' => $this->get_coord_liv(),
	            'coord_fac' => $this->get_coord_fac(),
	            'coord_fou' => $this->get_coord_fou()
	        );
	    }
	    return $this->formatted_data;
	}
	
	public function get_acte() {
	    if(!isset($this->acte)) {
	        $this->acte = new actes($this->id_acte);
	    }
	    return $this->acte;
	}
	
	public function get_bib() {
	    if(!isset($this->bib)) {
	        $this->bib = new entites($this->get_acte()->num_entite);
	    }
	    return $this->bib;
	}
	
	public function get_coord_liv() {
	    if(!isset($this->coord_liv)) {
	        $this->coord_liv = new coordonnees($this->get_acte()->num_contact_livr);
	    }
	    return $this->coord_liv;
	}
	
	public function get_coord_fac() {
	    if(!isset($this->coord_fac)) {
	        $this->coord_fac = new coordonnees($this->get_acte()->num_contact_fact);
	    }
	    return $this->coord_fac;
	}
	
	public function get_fou() {
	    if(!isset($this->fou)) {
	        $this->fou = new entites($this->get_acte()->num_fournisseur);
	    }
	    return $this->fou;
	}
	
	public function get_coord_fou() {
	    if(!isset($this->coord_fou)) {
	        $this->coord_fou = entites::get_coordonnees($this->get_acte()->num_fournisseur, '1');
	        $this->coord_fou = pmb_mysql_fetch_object($this->coord_fou);
	    }
	    return $this->coord_fou;
	}
	
	public function get_dest_name() {
	    return $this->dest_name;
	}
	
	public function get_dest_mail() {
	    return $this->dest_mail;
	}
}