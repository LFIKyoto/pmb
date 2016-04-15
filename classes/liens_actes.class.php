<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liens_actes.class.php,v 1.11 2015-04-03 11:16:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


if(!defined('TYP_ACT_ALL')) define('TYP_ACT_ALL', -1);	//				-1 = Tous types
if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande
if(!defined('TYP_ACT_DEV')) define('TYP_ACT_DEV', 1);	//				1 = Demande de devis
if(!defined('TYP_ACT_LIV')) define('TYP_ACT_LIV', 2);	//				2 = Bon de Livraison
if(!defined('TYP_ACT_FAC')) define('TYP_ACT_FAC', 3);	//				3 = Facture

class liens_actes{
	
	
	var $num_acte = 0;				//Numéro d'acte
	var $num_acte_lie = 0;			//Numéro d'acte lié

	 
	//Constructeur.	 
	function liens_actes($num_acte= 0, $num_acte_lie= 0 ) {
		
		global $dbh;
	
		$this->num_acte = $num_acte;
		$this->num_acte_lie = $num_acte_lie;
		if (!$num_acte || !$num_acte_lie) die ("Erreur de création liens_actes");

		$q = "select count(1) from liens_actes where num_acte = '".$num_acte."' and num_acte_lie = '".$num_acte_lie."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_result($r, 0, 0) == 0) {
			$q = "insert into liens_actes set num_acte = '".$num_acte."', num_acte_lie = '".$num_acte_lie."' ";
			$r = pmb_mysql_query($q, $dbh);
		}

	}	


	//supprime un lien entre actes de la base
	function delete($num_acte) {
		
		global $dbh;

		$q = "delete from liens_actes where num_acte = '".$num_acte."' or num_acte_lie = '".$num_acte."' ";
		$r = pmb_mysql_query($q, $dbh);
				
	}


	//recherche l'acte pere de l'acte passé en paramètre
	static function getParent($num_acte_lie) {
		
		global $dbh;

		$q = "select num_acte from liens_actes where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}


	
	//recherche du devis origine de l'acte passe en parametre
	static function getDevis($num_acte_lie) {
		
		global $dbh;
		$q = "select num_acte from liens_actes join actes on num_acte=id_acte and type_acte = '".TYP_ACT_DEV."' where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}
	
	
	//recherche la commande du bl/facture passe  en parametre
	static function getOrder($num_acte_lie) {
		
		global $dbh;
		$q = "select num_acte from liens_actes join actes on num_acte=id_acte and type_acte = '".TYP_ACT_CDE."' where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}
	
	
	//recherche les enfants de l'acte passé en paramètre
	static function getChilds($num_acte, $type_acte=TYP_ACT_ALL) {
		
		global $dbh;

		$q = "select num_acte_lie, numero, type_acte, statut from liens_actes, actes where num_acte = '".$num_acte."' and id_acte = num_acte_lie ";
		if ($type_acte != TYP_ACT_ALL) $q.= "and type_acte = '".$type_acte."' ";
		$q.= "order by type_acte, numero";
		$r = pmb_mysql_query($q, $dbh);
		return $r;
	}	

	
	//recherche les livraisons pour une commande
	//retourne un tableau d'ids
	static function getDeliveries ($num_cde, $with_date=false) {

		global $dbh;
		$t=array();
		
		$q = "select num_acte_lie from liens_actes join actes on num_acte_lie = id_acte and type_acte = '".TYP_ACT_LIV."' ";
		$q.= "where num_acte = '".$num_cde."' ";
		if($with_date) {
			"and date_acte = '".$with_date."' ";
		}
		$q.= "order by numero desc";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r)) {
			while($row=pmb_mysql_fetch_object($r)) {
				$t[]=$row->num_acte_lie;
			}
		}
		return $t;
	}
	
	//optimization de la table liens_actes
	function optimize() {
		
		global $dbh;
		
		$opt = pmb_mysql_query('OPTIMIZE TABLE liens_actes', $dbh);
		return $opt;
				
	}
	
				
}
?>