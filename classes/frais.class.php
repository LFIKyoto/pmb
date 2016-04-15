<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frais.class.php,v 1.12 2015-04-03 11:16:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frais{
	
	
	var $id_frais = 0;					//Identifiant du frais 
	var $libelle = '';
	var $condition_frais = '';
	var $montant = '000000.00';
	var $num_cp_compta = 0;
	var $num_tva_achat = 0;
	

	 
	//Constructeur.	 
	function frais($id_frais= 0) {
		
		if ($id_frais) {
			$this->id_frais = $id_frais;
			$this->load();	
		}
	}
	
		
	// charge le frais à partir de la base.
	function load(){
	
		global $dbh;
		
		$q = "select * from frais where id_frais = '".$this->id_frais."' ";
		$r = pmb_mysql_query($q, $dbh) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->condition_frais = $obj->condition_frais;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;

	}

	
	// enregistre le frais de tva en base.
	function save(){
		
		global $dbh;
		
		if($this->libelle == '') die("Erreur de création frais"); 
		
		if($this->id_frais) {
			
			$q = "update frais set libelle ='".$this->libelle."', condition_frais = '".$this->condition_frais."', ";
			$q.= "montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', ";
			$q.= "num_tva_achat = '".$this->num_tva_achat."', index_libelle = ' ".strip_empty_words($this->libelle)." ' ";
			$q.= "where id_frais = '".$this->id_frais."' ";
			$r = pmb_mysql_query($q, $dbh);
	
		} else {
		
			$q = "insert into frais set libelle = '".$this->libelle."', condition_frais =  '".$this->condition_frais."', ";
			$q.= "montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', num_tva_achat = '".$this->num_tva_achat."', index_libelle = ' ".strip_empty_words($this->libelle)." ' ";
			$r = pmb_mysql_query($q, $dbh);
			$this->id_frais = pmb_mysql_insert_id($dbh);
		
		}
	
	}


	//supprime un taux de tva de la base
	static function delete($id_frais=0) {
		
		global $dbh;

		if(!$id_frais) return; 	

		$q = "delete from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q, $dbh);
				
	}


	//Retourne un Resultset contenant la liste des frais
	static function listFrais() {
		
		global $dbh;

		$q = "select * from frais order by libelle ";
		$r = pmb_mysql_query($q, $dbh);
		return $r;
				
	}
	
	
	//Vérifie si un frais existe			
	static function exists($id_frais){
		
		global $dbh;
		$q = "select count(1) from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
		
	}
	
		
	//Vérifie si le libellé d'un frais annexe existe déjà			
	static function existsLibelle($libelle, $id_frais=0){
		
		global $dbh;
		$q = "select count(1) from frais where libelle = '".$libelle."' ";
		if ($id_frais) $q.= "and id_frais != '".$id_frais."' ";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
		
	}

		
	//Vérifie si le frais est utilisé dans les fournisseurs	
	static function hasFournisseurs($id_frais){
		
		global $dbh;
		if (!$id_frais) return 0;
		$q = "select count(1) from entites where num_frais = '".$id_frais."' and type_entite = '0'";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
		
	}


	//optimization de la table taux de tva
	function optimize() {
		
		global $dbh;
		
		$opt = pmb_mysql_query('OPTIMIZE TABLE frais', $dbh);
		return $opt;
				
	}
	
				
}
?>