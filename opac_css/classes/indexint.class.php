<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.class.php,v 1.22 2015-04-03 11:16:18 jpermanne Exp $

// définition de la classe de gestion des 'indexations internes'
if ( ! defined( 'INDEXINT_CLASS' ) ) {
  define( 'INDEXINT_CLASS', 1 );

class indexint {

// ---------------------------------------------------------------
//		propriétés de la classe
// ---------------------------------------------------------------
	var 	$indexint_id=0;			// MySQL indexint_id in table 'indexint'
	var	$name='';			// nom de l'indexation
	var	$comment='';			// commentaire
	var	$pclass='';			// id plan de classement
	var	$childs = array();
	var	$has_child = 0 ;


// ---------------------------------------------------------------
//		indexint($id) : constructeur
// ---------------------------------------------------------------
function indexint($id=0, $rech_cote="") {
	if($id) {
		$this->indexint_id = $id;
	} else {
		if ($rech_cote) $this->name=$rech_cote;
		$this->indexint_id = 0;
	}
	$this->getData();
}

// ---------------------------------------------------------------
//		getData() : récupération infos 
// ---------------------------------------------------------------
function getData() {
	global $dbh;
	
	if(!$this->indexint_id) {
		if ($this->name) { // rech par cote et non par $id
			$requete = "SELECT indexint_id,indexint_name,indexint_comment,num_pclass FROM indexint WHERE indexint_name='".$this->name."' " ;
			$result = pmb_mysql_query($requete, $dbh) or die ($requete."<br />".pmb_mysql_error());
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->indexint_id	= $temp->indexint_id;
				$this->name		= $temp->indexint_name;
				$this->comment		= $temp->indexint_comment;
				$this->pclass		= $temp->num_pclass;
				if ($this->comment) $this->display = $this->name." ($this->comment)" ;
				else $this->display = $this->name ;
			} else {
				// pas de titre avec cette clé
				$this->indexint_id		=	0;
				$this->name			=	'';
				$this->comment			=	'';
				$this->pclass		= 0;
				$this->display="";
			}
		} else {
			// pas d'identifiant. on retourne un tableau vide
			$this->indexint_id	=0;
			$this->name		='';
			$this->comment		='';
			$this->pclass		= 0;
			$this->display="";		
		}
	} else {
		$requete = "SELECT indexint_id,indexint_name,indexint_comment,num_pclass FROM indexint WHERE indexint_id='".$this->indexint_id."' " ;
		$result = pmb_mysql_query($requete, $dbh) or die ($requete."<br />".pmb_mysql_error());
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$this->indexint_id	= $temp->indexint_id;
			$this->name		= $temp->indexint_name;
			$this->comment		= $temp->indexint_comment;
			$this->pclass		= $temp->num_pclass;
			if ($this->comment) $this->display = $this->name." ($this->comment)" ;
			else $this->display = $this->name ;
		} else {
			// pas de titre avec cette clé
			$this->indexint_id		=	0;
			$this->name			=	'';
			$this->comment			=	'';
			$this->pclass		= 0;
			$this->display="";
		}			
	}
	$this->cherche_child();
}

function has_notices() {
	global $dbh;
	$query = "select count(1) from notices where indexint=".$this->indexint_id;
	$result = pmb_mysql_query($query, $dbh);
	return (@pmb_mysql_result($result, 0, 0));
	}

function cherche_direct_child() {
// fonction réduite à un seul niveau de récursivité par rapport à cherche_child. gm
	global $dbh;
	global $pmb_indexint_decimal ;
	
	$this->childs = array();
	
	if (!$pmb_indexint_decimal) {
		$this->has_child = 0 ;
		return ;
		}
	
	/* calcul de l'arbo :
	si 3ème carac != 0
		niveau 3
		sinon si 2eme carac != 0
			niveau 2
			sinon prendre le premier carac
	rechercher quand même avec les trois carac entiers
	*/

	if (pmb_strlen($this->name)>3)
	{
	$clause = " indexint_name regexp '^".$this->name.".$'";
	}
	else
	{
	$carac1 = substr($this->name, 0 , 1);
	$carac2 = substr($this->name, 1 , 1);
	$carac3 = substr($this->name, 2 , 1);
	$entier = substr($this->name, 0 , 3);
		if ($carac3 != "0") {
			$clause = " indexint_name regexp '^".$entier."..$' " ;
			} elseif ($carac2 != "0") {
				$clause = " indexint_name regexp '^".$carac1.$carac2.".$' " ;
				} else
					{ 
					if ($carac1 != "1") { $clause = " indexint_name regexp '^".$carac1.".$' " ; } 
					else $clause = " indexint_name regexp '^.00$' "; }  
	}
	
	$query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." order by indexint_name ";
	$res = pmb_mysql_query($query, $dbh);
	$this->has_child=pmb_mysql_num_rows($res) ;
	if ($this->has_child) {
		while ($obj=pmb_mysql_fetch_object($res)) {
			$this->childs[]=array(
					'idchild' => $obj->indexint_id,
					'namechild' => $obj->indexint_name,
					'commentchild' => $obj->indexint_comment) ;
			}
		} 
	return ;
	}


function cherche_child() {
	global $dbh;
	global $pmb_indexint_decimal ;
	
	$this->childs = array();
	
	if (!$pmb_indexint_decimal) {
		$this->has_child = 0 ;
		return ;
		}
	
	/* calcul de l'arbo :
	si 3ème carac != 0
		niveau 3
		sinon si 2eme carac != 0
			niveau 2
			sinon prendre le premier carac
	rechercher quand même avec les trois carac entiers
	*/
	$entier = substr($this->name, 0 , 3);
	if (pmb_strlen($this->name)>3){
		$clause = " indexint_name like '".$entier."%'";
	}else {
		$carac1 = substr($this->name, 0 , 1);
		$carac2 = substr($this->name, 1 , 1);
		$carac3 = substr($this->name, 2 , 1);
		if ($carac3 != "0"){
			$clause = " indexint_name like '".$entier."%' " ;
		}elseif ($carac2 != "0"){
			$clause = " indexint_name like '".$carac1.$carac2."%' " ;
		}else{
			$clause = " indexint_name like '".$carac1."%' " ;
		}
	}
	if($this->pclass){
		$clause.= " AND num_pclass='".$this->pclass."' " ;
	}
	// avec affichage de l'indexation parente
	// $query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." order by indexint_name ";
	// sans affichage de l'indexation parente
	$query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." and indexint_name <> '".addslashes($this->name)."' order by indexint_name ";
	$res = pmb_mysql_query($query, $dbh);
	$this->has_child=pmb_mysql_num_rows($res) ;
	if ($this->has_child) 
		while ($obj=pmb_mysql_fetch_object($res)) {
			$this->childs[]=array(
					'idchild' => $obj->indexint_id,
					'namechild' => $obj->indexint_name,
					'commentchild' => $obj->indexint_comment) ;
			}
	return ;
	}

function child_list($image='./images/folder.gif',$css, $dest=0) {

	global $css;
	global $dbh;
	global $nb_col_scat;
	global $main;

	while(list($cle, $valeur) = each($this->childs)) {
		$libelle = $valeur['namechild']." ".$valeur['commentchild'];
		$id = $valeur['idchild'];
		$l .=  "<a href=./index.php?lvl=indexint_see&id=$id&main=$main ><img src='./images/folder.gif' border='0'> ".$libelle."</a>";
		$l .= "<br />";
		}
	$l = "<br /><div style='margin-left:48px'>$l</div>";
	return $l; 
	}
	


} # fin de définition de la classe indexint

} # fin de délaration

