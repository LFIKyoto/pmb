<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie.class.php,v 1.10.2.2 2015-10-16 14:47:09 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des paniers

define( 'CADDIE_ITEM_NULL', 0 );
define( 'CADDIE_ITEM_OK', 1 );
define( 'CADDIE_ITEM_DEJA', 1 ); // identique car on peut ajouter des liés avec l'item et non pas l'item saisi lui-même ...
define( 'CADDIE_ITEM_IMPOSSIBLE_BULLETIN', 2 );
define( 'CADDIE_ITEM_EXPL_PRET' , 3 );
define( 'CADDIE_ITEM_BULL_USED', 4) ;
define( 'CADDIE_ITEM_NOTI_USED', 5) ;
define( 'CADDIE_ITEM_SUPPR_BASE_OK', 6) ;
define( 'CADDIE_ITEM_INEXISTANT', 7 );
define( 'CADDIE_ITEM_RESA', 8 );

class empr_caddie {
// propriétés
var $idemprcaddie ;
var $name = ''			;	// nom de référence
var $comment = ""		;	// description du contenu du panier
var $nb_item = 0		;	// nombre d'enregistrements dans le panier
var $nb_item_pointe = 0		;	// nombre d'enregistrements pointés dans le panier
var $autorisations = ""		;	// autorisations accordées sur ce panier
var $classementGen = ""		;	// classement
var $liaisons = array("mailing" => array()); // Liaisons associées à un panier

// ---------------------------------------------------------------
//		empr_caddie($id) : constructeur
// ---------------------------------------------------------------
function empr_caddie($empr_caddie_id=0) {
	if($empr_caddie_id) {
		$this->idemprcaddie = $empr_caddie_id;
		$this->getData();
		} else {
			$this->idemprcaddie = 0;
			$this->getData();
			}
	}

// ---------------------------------------------------------------
//		getData() : récupération infos caddie
// ---------------------------------------------------------------
function getData() {
	global $dbh;
	if(!$this->idemprcaddie) {
		// pas d'identifiant.
		$this->name	= '';
		$this->comment	= '';
		$this->nb_item	= 0;
		$this->autorisations	= "";
		$this->classementGen	= "";
	} else {
		$requete = "SELECT * FROM empr_caddie WHERE idemprcaddie='$this->idemprcaddie' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			pmb_mysql_free_result($result);
			$this->idemprcaddie = $temp->idemprcaddie;
			$this->name = $temp->name;
			$this->comment = $temp->comment;
			$this->autorisations = $temp->autorisations;
			$this->classementGen = $temp->empr_caddie_classement;
			
			//liaisons
			$req="SELECT id_planificateur, num_type_tache, libelle_tache FROM planificateur WHERE num_type_tache=8 AND param REGEXP 's:11:\"empr_caddie\";s:[0-9]+:\"".$this->idemprcaddie."\";'";
			$res=pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while ($ligne=pmb_mysql_fetch_object($res)){
					$this->liaisons["mailing"][]=array("id"=>$ligne->id_planificateur,"id_bis"=>$ligne->num_type_tache,"lib"=>$ligne->libelle_tache);
				}
			}
		} else {
			// pas de caddie avec cet id
			$this->idemprcaddie = 0;
			$this->name = '';
			$this->comment = '';
			$this->autorisations = "";
			$this->classementGen = "";
		}
		$this->compte_items();
	}
}

// liste des paniers disponibles
static function get_cart_list() {
	global $dbh, $PMBuserid;
	$cart_list=array();
	if ($PMBuserid!=1) $where=" where (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
	$requete = "SELECT * FROM empr_caddie $where order by name ";
	$result = @pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($result)) {
		while ($temp = pmb_mysql_fetch_object($result)) {
			$nb_item = 0 ;
			$nb_item_pointe = 0 ;
			$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' ";
			$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
			$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and (flag is not null and flag!='') ";
			$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);

			$cart_list[] = array( 
				'idemprcaddie' => $temp->idemprcaddie,
				'name' => $temp->name,
				'comment' => $temp->comment,
				'autorisations' => $temp->autorisations,
				'empr_caddie_classement' => $temp->empr_caddie_classement,
				'nb_item' => $nb_item,
				'nb_item_pointe' => $nb_item_pointe
				);
		}
	} 
	return $cart_list;
}

// création d'un panier vide
function create_cart() {
	global $dbh;
	$requete = "insert into empr_caddie set name='".$this->name."', comment='".$this->comment."', autorisations='".$this->autorisations."', empr_caddie_classement='".$this->classementGen."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->idemprcaddie = pmb_mysql_insert_id($dbh);
	$this->compte_items();
	}


// ajout d'un item
function add_item($item=0) {
	global $dbh;
	
	if (!$item) return CADDIE_ITEM_NULL ;
	
	$requete = "replace into empr_caddie_content set empr_caddie_id='".$this->idemprcaddie."', object_id='".$item."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	return CADDIE_ITEM_OK ;
	}

// suppression d'un item
function del_item($item=0) {
	global $dbh;
	$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->compte_items();
}

function del_item_base($item=0) {
	global $dbh;
	
	if (!$item) return CADDIE_ITEM_NULL ;
	
	$verif_empr_item = $this->verif_empr_item($item); 
	if (!$verif_empr_item) {
		emprunteur::del_empr($item);
		return CADDIE_ITEM_SUPPR_BASE_OK ;
	} elseif ($verif_empr_item == 1) {
		return CADDIE_ITEM_EXPL_PRET ;
	} else {
		return CADDIE_ITEM_RESA ;
	}
				
}

// suppression d'un item de tous les caddies du même type le contenant
function del_item_all_caddies($item) {
	global $dbh;
	$requete = "select idemprcaddie FROM empr_caddie ";
	$result = pmb_mysql_query($requete, $dbh);
	for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
		$temp=pmb_mysql_fetch_object($result);
		$requete_suppr = "delete from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and object_id='".$item."' ";
		$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
	}
}

function del_item_flag() {
	global $dbh;
	$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->compte_items();
}

function del_item_no_flag() {
	global $dbh;
	$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->compte_items();
}

// Dépointage de tous les items
function depointe_items() {
	global $dbh;
	$requete = "update empr_caddie_content set flag=null where empr_caddie_id='".$this->idemprcaddie."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->compte_items();
}	

function pointe_item($item=0) {
	global $dbh;
	$requete = "update empr_caddie_content set flag='1' where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->compte_items();
	return CADDIE_ITEM_OK ;
}

function depointe_item($item=0) {
	global $dbh;

	if ($item) {
		$requete = "update empr_caddie_content set flag=null where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if ($result) {
			$this->compte_items();
			return 1;
		} else {
			return 0;
		}
	}
}

// suppression d'un panier
function delete() {
	global $dbh;
	$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$requete = "delete FROM empr_caddie where idemprcaddie='".$this->idemprcaddie."' ";
	$result = @pmb_mysql_query($requete, $dbh);
}

// sauvegarde du panier
function save_cart() {
	global $dbh;
	$requete = "update empr_caddie set name='".$this->name."', comment='".$this->comment."', autorisations='".$this->autorisations."', empr_caddie_classement='".$this->classementGen."' where idemprcaddie='".$this->idemprcaddie."'";
	$result = @pmb_mysql_query($requete, $dbh);
}


// get_cart() : ouvre un panier et récupère le contenu
function get_cart($flag="") {
	global $dbh;
	$cart_list=array();
	switch ($flag) {
		case "FLAG" :
			$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
			break ;
		case "NOFLAG" :
			$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
			break ;
		case "ALL" :
		default :
			$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
			break ;
		}
	$result = @pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($result)) {
		while ($temp = pmb_mysql_fetch_object($result)) {
			$cart_list[] = $temp->object_id;
		}
	} 
	return $cart_list;
}

// compte_items 
function compte_items() {
	global $dbh;
	$this->nb_item = 0 ;
	$this->nb_item_pointe = 0 ;
	$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
	$this->nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
	$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
	$this->nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);
}

function verif_empr_item($id) {
	global $dbh;
	
	if ($id) {
		//Prêts en cours
		$query = "select count(1) from pret where pret_idempr=".$id." limit 1 ";
		$result = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_result($result, 0, 0)){
			return 1 ;
		} else {
			//Réservations validées
			$query = "select count(1) from resa where resa_idempr=".$id." and resa_confirmee=1 limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 2 ;
			} else {
				return 0 ;
			}
		}		
	} else return 0 ;
}

static function show_actions($id_caddie = 0) {
	global $msg,$empr_cart_action_selector,$empr_cart_action_selector_line;

	//Le tableau des actions possibles
	$array_actions = array();
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_panier"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprpanier&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_transfert"], 'location' => './circ.php?categ=caddie&sub=action&quelle=transfert&action=transfert&idemprcaddie='.$id_caddie.'&item=');
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edition"], 'location' => './circ.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_mailing"], 'location' => './circ.php?categ=caddie&sub=action&quelle=mailing&action=envoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_selection"], 'location' => './circ.php?categ=caddie&sub=action&quelle=selection&action=&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
	$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_base"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprbase&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
	
	//On crée les lignes du menu
	$lines = '';
	foreach($array_actions as $item_action){
		$tmp_line = str_replace('!!cart_action_selector_line_location!!',$item_action['location'],$empr_cart_action_selector_line);
		$tmp_line = str_replace('!!cart_action_selector_line_msg!!',$item_action['msg'],$tmp_line);
		$lines.= $tmp_line;
	}
	
	//On récupère le template
	$to_show = str_replace('!!cart_action_selector_lines!!',$lines,$empr_cart_action_selector);

	return $to_show;
}
	
} // fin de déclaration de la classe
  
