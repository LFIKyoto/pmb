<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: paiements.class.php,v 1.11 2017-04-26 15:25:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class paiements{
	
	public $id_paiement = 0;					//Identifiant du paiement 
	public $libelle = '';
	public $commentaire = '';
	 
	//Constructeur.	 
	public function __construct($id_paiement= 0) {
		$this->id_paiement = $id_paiement+0;
		if ($this->id_paiement) {
			$this->load();	
		}
	}	

	// charge le paiement à partir de la base.
	public function load(){
		$q = "select * from paiements where id_paiement = '".$this->id_paiement."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->commentaire = $obj->commentaire;
	}
	
	// enregistre le paiement en base.
	public function save(){
		if($this->libelle =='') Die("Erreur de création paiement");
		if($this->id_paiement) {
			$q = "update paiements set libelle ='".$this->libelle."', commentaire = '".$this->commentaire."' ";
			$q.= "where id_paiement = '".$this->id_paiement."' ";
			$r = pmb_mysql_query($q);
		} else {
			$q = "insert into paiements set libelle = '".$this->libelle."', commentaire = '".$this->commentaire."' ";
			pmb_mysql_query($q);
			$this->id_paiement = pmb_mysql_insert_id();
		}
	}

	//supprime un paiement de la base
	public static function delete($id_paiement= 0) {
		$id_paiement += 0;
		if(!$id_paiement) return; 	
		$q = "delete from paiements where id_paiement = '".$id_paiement."' ";
		pmb_mysql_query($q);
	}
	
	//Retourne un Resultset contenant la liste des modes de paiement
	public static function listPaiements() {
		$q = "select * from paiements order by libelle ";
		$r = pmb_mysql_query($q);
		return $r;
	}
	
	//Vérifie si un mode de paiement existe			
	public static function exists($id_paiement){
		$id_paiement += 0;
		$q = "select count(1) from paiements where id_paiement = '".$id_paiement."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libellé d'un mode de paiement existe déjà			
	public static function existsLibelle($libelle, $id_paiement=0){
		$id_paiement += 0;
		$q = "select count(1) from paiements where libelle = '".$libelle."' ";
		if ($id_paiement) $q.= "and id_paiement != '".$id_paiement."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le mode de paiement est utilisé dans les fournisseurs	
	public static function hasFournisseurs($id_paiement){
		$id_paiement += 0;
		if (!$id_paiement) return 0;
		$q = "select count(1) from entites where num_paiement = '".$id_paiement."' and type_entite = '0'";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//optimization de la table paiements
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE paiements');
		return $opt;
	}
}
?>