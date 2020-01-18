<?php
// +-------------------------------------------------+
// � 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frais.class.php,v 1.15 2019-08-09 13:06:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frais{
	
    public $id_frais = 0;					//Identifiant du frais annexe
	public $libelle = '';
	public $condition_frais = '';
	public $montant = '000000.00';
	public $num_cp_compta = 0;
	public $num_tva_achat = 0;
	public $add_to_new_order = 0;
	
	//Constructeur.	 
	public function __construct($id_frais= 0) {
		$this->id_frais = $id_frais+0;
		if ($this->id_frais) {
			$this->load();	
		}
	}
	
		
	// charge un frais annexe � partir de la base.
	public function load(){
		$q = "select * from frais where id_frais = '".$this->id_frais."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->condition_frais = $obj->condition_frais;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
		$this->add_to_new_order = $obj->add_to_new_order;
	}
	
	// enregistre le frais annexe en base.
	public function save(){
		if($this->libelle == '') die("Erreur de cr�ation frais"); 
		if($this->id_frais) {		
		    
			$q = "update frais set 
                    libelle ='{$this->libelle}', 
                    condition_frais = '{$this->condition_frais}', 
                    montant = '{$this->montant}', 
                    num_cp_compta = '{$this->num_cp_compta}', 
                    num_tva_achat = '{$this->num_tva_achat}', 
                    index_libelle = ' ".strip_empty_words($this->libelle)." ',
                    add_to_new_order = {$this->add_to_new_order}
                where id_frais = {$this->id_frais} ";
			pmb_mysql_query($q);
	
		} else {
		
			$q = "insert into frais set 
                    libelle = '{$this->libelle}', 
                    condition_frais =  '{$this->condition_frais}', 
                    montant = '{$this->montant}',  
                    num_cp_compta = '{$this->num_cp_compta}',  
                    num_tva_achat = '{$this->num_tva_achat}', 
                    index_libelle = ' ".strip_empty_words($this->libelle)." ',
                    add_to_new_order = {$this->add_to_new_order} ";
			pmb_mysql_query($q);
			$this->id_frais = pmb_mysql_insert_id();
		}
	
	}

	//supprime un frais annexe de la base
	public static function delete($id_frais=0) {
		$id_frais += 0;
		if(!$id_frais) return; 	
		$q = "delete from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q);
	}

	//Retourne un Resultset contenant la liste des frais
	public static function listFrais() {
		$q = "select * from frais order by libelle ";
		$r = pmb_mysql_query($q);
		return $r;
	}
	
	//V�rifie si un frais existe			
	public static function exists($id_frais){
		$id_frais += 0;
		$q = "select count(1) from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//V�rifie si le libell� d'un frais annexe existe d�j�			
	public static function existsLibelle($libelle, $id_frais=0){
		$id_frais += 0;
		$q = "select count(1) from frais where libelle = '".$libelle."' ";
		if ($id_frais) $q.= "and id_frais != '".$id_frais."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}

	//V�rifie si le frais annexe est utilis� dans les fournisseurs	
	public static function hasFournisseurs($id_frais){
		$id_frais += 0;
		if (!$id_frais) return 0;
		$q = "select count(1) from entites where num_frais = '".$id_frais."' and type_entite = '0'";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table frais
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE frais');
		return $opt;
	}
	
	public static function getFraisForNewOrder() {
	    
	    $ret = [];
	    $q = "select 
                frais.id_frais, frais.libelle as libelle_frais, frais.condition_frais, frais.montant as montant_frais, frais.num_cp_compta as num_cp_compta_frais, 
                tva_achats.id_tva, tva_achats.libelle as libelle_tva, tva_achats.taux_tva 
            from frais left join tva_achats on frais.num_tva_achat = tva_achats.id_tva 
            where frais.add_to_new_order=1 order by frais.libelle";
	    $r = pmb_mysql_query($q);
	    if(!pmb_mysql_num_rows($r)) {
	        return [];
	    }
	    while($row=pmb_mysql_fetch_assoc($r)) {
	        $ret[] = $row;
	    }
	    return $ret;
	}
}

