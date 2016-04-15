<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pperso.class.php,v 1.5 2013-06-11 13:02:55 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion champs perso des autorités

require_once($class_path."/parametres_perso.class.php");

class aut_pperso {
	var $aut=""; // prefixe de l'autorité	
	var $id=0; // id de l'autorité
	var $error_message="";
	
	function aut_pperso($aut,$id=0) {
		$this->aut = $aut;
		$this->id = $id;
		$this->p_perso=new parametres_perso($this->aut);
		$this->getdata();
	}	

	function getdata() {
		global $charset,$dbh,$msg;
		$this->error_message="";
	}

	function get_form() {
		global $charset;
		$perso_=$this->p_perso->show_editable_fields($this->id);
		if (count($perso_["FIELDS"])) $perso = "<div class='row'></div>" ;
		else $perso="";
		$class="colonne2";
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			$p=$perso_["FIELDS"][$i];
			
			$perso.="<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]."</label></div>\n";
			$perso.="<div class='row'>";
			$perso.=$p["AFF"]."</div>";
			if ($class=="colonne2") $class="colonne_suite"; else $class="colonne2";
		}
		if ($class=="colonne_suite") $perso.="<div class='$class'>&nbsp;</div>";
		$perso.=$perso_["CHECK_SCRIPTS"];
		return $perso;
	}
	
	function save_form() {
		global $dbh;
		
		$nberrors=$this->p_perso->check_submited_fields();
		$this->error_message=$this->p_perso->error_message;
		if(!$nberrors){
			$this->p_perso->rec_fields_perso($this->id);
			return 0;
		}
		return 	$nberrors;
			
	}
	
	function delete() {
		$this->p_perso->delete_values($this->id);
	}
	
	function get_base_values($name,$id){
		return $this->p_perso->read_base_fields_perso_values($name,$id);
	}
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité
	function get_fields_recherche($id){
		return $this->p_perso->get_fields_recherche($id);
	}
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	function get_fields_recherche_mot($id){
		return $this->p_perso->get_fields_recherche_mot($id);
	}		
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	function get_fields_recherche_mot_array($id){
		return $this->p_perso->get_fields_recherche_mot_array($id);
	}
// fin class
}