<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: noeuds.class.php,v 1.42 2015-06-10 07:14:04 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/thesaurus.class.php");
require_once($class_path."/category.class.php");
require_once($include_path."/templates/category.tpl.php");
require_once("$include_path/user_error.inc.php");
require_once("$include_path/misc.inc.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/synchro_rdf.class.php");

class noeuds{
	
	
	var $id_noeud = 0;				//Identifiant du noeud 
	var $autorite = '';
	var $num_parent = 0;
	var $num_renvoi_voir = 0;
	var $visible = '1';
	var	$num_thesaurus = 0;			//Identifiant du thesaurus de rattachement
	var	$authority_import_denied = 0;			//Interdit l'import de l'autorité
	var $not_use_in_indexation = 0; // Interdir l'utilisation de la catégorie en indexation de notice
	 
	//Constructeur.	 
	function noeuds($id=0) {
		
		global $dbh;
	
		if ($id) {
			$this->id_noeud = $id;
			$this->load();	
		}	
	}
	
		
	// charge le noeud à partir de la base.
	function load(){
	
		global $dbh;
		$q = "select * from noeuds where id_noeud = '".$this->id_noeud."' ";
		$r = pmb_mysql_query($q, $dbh) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->id_noeud = $obj->id_noeud;
		$this->autorite = $obj->autorite;
		$this->num_parent = $obj->num_parent;
		$this->num_renvoi_voir = $obj->num_renvoi_voir;
		$this->visible = $obj->visible;
		$this->num_thesaurus = $obj->num_thesaurus;
		$this->path = $obj->path;
		$this->not_use_in_indexation = $obj->not_use_in_indexation;
		$this->authority_import_denied = $obj->authority_import_denied;
	}

	
	// enregistre le noeud en base.
	function save(){
		
		global $dbh;
		
		if (!$this->num_thesaurus) die ('Erreur de création noeud');
		
		if ($this->id_noeud) {	//Mise à jour noeud
			
			$q = 'update noeuds set autorite =\''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\', ';
			$q.= 'authority_import_denied = \''.$this->authority_import_denied.'\', not_use_in_indexation = \''.$this->not_use_in_indexation.'\' ';
			$q.= 'where id_noeud = \''.$this->id_noeud.'\' ';
			pmb_mysql_query($q, $dbh);
			audit::insert_modif (AUDIT_CATEG, $this->id_noeud) ;

		} else {
			
			$q = 'insert into noeuds set autorite = \''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\', ';
			$q.= 'authority_import_denied = \''.$this->authority_import_denied.'\', not_use_in_indexation = \''.$this->not_use_in_indexation.'\' ';
			pmb_mysql_query($q, $dbh);
			$this->id_noeud = pmb_mysql_insert_id($dbh);
			audit::insert_creation (AUDIT_CATEG, $this->id_noeud) ;
		}
		
		// Mis à jour du path de lui-meme, et de tous les fils
		$thes = thesaurus::getByEltId($this->id_noeud);

		$id_top = $thes->num_noeud_racine;
		$path='';		
		$id_tmp=$this->id_noeud;
		while (true) {
			$q = "select num_parent from noeuds where id_noeud = '".$id_tmp."' limit 1";
			$r = pmb_mysql_query($q, $dbh);
			$id_tmp= $id_cur = pmb_mysql_result($r, 0, 0);
			if (!$id_cur || $id_cur == $id_top) break;
			if($path) $path='/'.$path;
			$path=$id_tmp.$path;			
		}
		noeuds::process_categ_path($this->id_noeud,$path);
	}
	
	static function process_categ_path($id_noeud=0, $path='') {
		global $dbh;

		if(!$id_noeud) return; 	
		
		if($path) $path.='/';
		$path.=$id_noeud;
		
		$res = noeuds::listChilds($id_noeud, 0);
		while (($row = pmb_mysql_fetch_object($res))) {
			// la categorie a des filles qu'on va traiter
			noeuds::process_categ_path ($row->id_noeud,$path);
		}		
		$req="update noeuds set path='$path' where id_noeud=$id_noeud";
		pmb_mysql_query($req,$dbh);		
	}

	static function process_categ($id_noeud) {
		global $dbh;
		
		global $deleted;
		global $lot;
		
		$res = noeuds::listChilds($id_noeud, 0);
		$total = pmb_mysql_num_rows($res);
		if ($total) {
			while ($row = pmb_mysql_fetch_object($res)) {
				// la categorie a des filles qu'on va traiter
				noeuds::process_categ ($row->id_noeud);
			}
			
			// après ménage de ses filles, reste-t-il des filles ?
			$total_filles = noeuds::hasChild($id_noeud);
			
			// categ utilisée en renvoi voir ?
			$total_see = noeuds::isTarget($id_noeud);
			
			// est-elle utilisée ?
			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
			
			if(!$iuse && !$total_filles && !$total_see) {
				$deleted++ ;
				noeuds::delete($id_noeud);
			}
			
		} else { // la catégorie n'a pas de fille on va la supprimer si possible
			// regarder si categ utilisée
			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
			if(!$iuse) {
				$deleted++ ;
				noeuds::delete($id_noeud);
			}
		}
				
	}

	//fonctions !!!

	//supprime un noeud et toutes ses références
	function delete($id_noeud=0) {
		
		global $dbh;

		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	

		// Supprime les categories.
		$q = "delete from categories where num_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);
		
		//Import d'autorité
		noeuds::delete_autority_sources($id_noeud);
		
		// Supprime les renvois voir_aussi vers ce noeud. 
		$q= "delete from voir_aussi where num_noeud_dest = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);
		
		// Supprime les renvois voir_aussi depuis ce noeud. 
		$q= "delete from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);
		
		// Supprime les associations avec des notices. 
		$q= "delete from notices_categories where num_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);

		//Supprime les emprises du noeud
		$req = "select map_emprise_id from map_emprises where map_emprise_type=2 and map_emprise_obj_num=".$id_noeud;
		$result = pmb_mysql_query($req, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$q= "delete from map_emprises where map_emprise_obj_num ='".$id_noeud."' and map_emprise_type = 2";
			pmb_mysql_query($q, $dbh);
			$req_areas="delete from map_hold_areas where type_obj=2 and id_obj=".$row->map_emprise_id;
			pmb_mysql_query($req_areas,$dbh);
		}
		
		//suppression des renvois voir restants
		$q = "update noeuds set num_renvoi_voir = '0' where num_renvoi_voir = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);
		
		// Supprime le noeud.
		$q = "delete from noeuds where id_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q, $dbh);
		
		audit::delete_audit(AUDIT_CATEG,$id_noeud);
		
		// liens entre autorités 
		$aut_link= new aut_link(AUT_TABLE_CATEG,$id_noeud);
		$aut_link->delete();
		
		$aut_pperso= new aut_pperso("categ",$id_noeud);
		$aut_pperso->delete();
				
	}

	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	static function delete_autority_sources($idnoeud=0){
		$tabl_id=array();
		if(!$idnoeud){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN noeuds ON num_authority=id_noeud  WHERE authority_type = 'category' AND id_noeud IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idnoeud;
		}
		foreach ( $tabl_id as $value ) {
	       //suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'category'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'category'";
			pmb_mysql_query($query);
		}
	}

	// recherche si une autorite existe deja dans un thesaurus, 
	// et retourne le noeud associe
	function searchAutorite($num_thesaurus, $autorite) {
		
		global $dbh;
		
		$q = "select id_noeud from noeuds where num_thesaurus = '".$num_thesaurus."' ";
		$q.= "and autorite = '".addslashes($autorite)."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r) == 0) return FALSE;
		$noeud = new noeuds(pmb_mysql_result($r, 0, 0));
		return $noeud;
	}
	
	
	//recherche si un noeud a des fils
	static function hasChild($id_noeud=0) {
	
		global $dbh;

		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	
		if($id_noeud){
			$q = "select count(1) from noeuds where num_parent = '".$id_noeud."' ";
			$r = pmb_mysql_query($q, $dbh);
			return pmb_mysql_result($r, 0, 0);
		}
		return 0;
	}	

		
	//recherche si un noeud est le renvoi voir d'un autre noeud.
	static function isTarget($id_noeud=0) {
		
		global $dbh;
		
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 
		if($id_noeud){
			$q = "select count(1) from noeuds where num_renvoi_voir = '".$id_noeud."' ";
			$r = pmb_mysql_query($q, $dbh);
			return pmb_mysql_result($r, 0, 0);
		}
		return 0;
	}		


	//Indique si un noeud est protégé (TOP, ORPHELINS et NONCLASSES).
	static function isProtected($id_noeud=0) {
		
		global $dbh;
		
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 
		$q = "select autorite from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q, $dbh);
		$a = pmb_mysql_result($r, 0, 0);
		if( $a=='TOP' || $a=='ORPHELINS' || $a=='NONCLASSES') return TRUE;
			else return FALSE;
	}		


	//Indique si un noeud est racine (non modifiable).
	static function isRacine($id_noeud=0) {
		
		global $dbh;
		
		if (!$id_noeud) return FALSE;
		$q = "select * from thesaurus where num_noeud_racine = '".$id_noeud."' limit 1 ";
		$r = pmb_mysql_query($q, $dbh);
		if( pmb_mysql_num_rows($r)) return TRUE;
			else return FALSE;
	}		


	//Liste les ancetres d'un noeud et les retourne sous forme d'un tableau 
	static function listAncestors($id_noeud=0) {
		
		global $dbh;
		if(!$id_noeud && (is_object($this))) {
			$id_noeud = $this->id_noeud;
			$path= $this->path;
		} else {
			$q = "select path from noeuds where id_noeud = '".$id_noeud."' ";
			$r = pmb_mysql_query($q, $dbh);
			if($r && pmb_mysql_num_rows($r)){
				$path=pmb_mysql_result($r, 0, 0);
			}
		}
		if ($path){ 
			$id_list=explode('/',$path);
			krsort($id_list);
			return $id_list;		
		}
		$thes = thesaurus::getByEltId($id_noeud);

		$id_top = $thes->num_noeud_racine;
		$i = 0;		
		$id_list[$i] = $id_noeud;
		while (true) {
			$q = "select num_parent from noeuds where id_noeud = '".$id_list[$i]."' limit 1";
			$r = pmb_mysql_query($q, $dbh);
			$id_cur = pmb_mysql_result($r, 0, 0);
			if (!$id_cur || $id_cur == $id_top) break;
			$i++;
			$id_list[$i] = pmb_mysql_result($r, 0, 0);
		}
		return $id_list;		
	}
	
	
	//Liste les enfants d'un noeud sous forme de resultset (si $renvoi=0, ne retourne pas les noeuds renvoyés)
	static function listChilds($id_noeud=0, $renvoi=0) {
	
		global $dbh;

		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	
		$q = "select id_noeud from noeuds where num_parent = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		if (!$renvoi) $q.= "and num_renvoi_voir = '0' ";
		$r = pmb_mysql_query($q, $dbh);
		return $r;
	}

	//Liste les noeuds qui ont un renvoi voir d'un autre noeud sous forme de resultset
	static function listTargets($id_noeud=0) {
	
		global $dbh;
		
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	
		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q, $dbh);
		return $r;
	}
	
	//Liste les noeuds termes orphelins qui ont un renvoi voir d'un autre noeud sous forme de tableau
	static function listTargetsOrphansOnly($id_noeud=0) {
		
		global $dbh;
		
		$id_list = array();
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud;
		
		$thes = thesaurus::getByEltId($id_noeud);
		
		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$id_list_ancestors = noeuds::listAncestors($row->id_noeud);
				if (count($id_list_ancestors)) {
					if (in_array($thes->num_noeud_orphelins,$id_list_ancestors)) {
						$id_list[] = $row->id_noeud;
					}
				}
			}
		}
		
		return $id_list;
	}
	
	//Liste les noeuds sauf termes orphelins qui ont un renvoi voir d'un autre noeud sous forme de tableau
	static function listTargetsExceptOrphans($id_noeud=0) {
	
		global $dbh;
	
		$id_list = array();
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud;
	
		$thes = thesaurus::getByEltId($id_noeud);
	
		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$id_list_ancestors = noeuds::listAncestors($row->id_noeud);
				if (count($id_list_ancestors)) {
					if (!in_array($thes->num_noeud_orphelins,$id_list_ancestors)) {
						$id_list[] = $row->id_noeud;
					}
				} else {
					$id_list[] = $row->id_noeud;
				}
			}
		}
	
		return $id_list;
	}
	
	//recherche si un noeud est utilisé dans une notice.
	static function isUsedInNotices($id_noeud=0) {
		
		global $dbh;
		
		if(!$id_noeud) return 0; 
		$q = "select count(1) from notices_categories where num_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q, $dbh);
		return pmb_mysql_result($r, 0, 0);
	}		


	//recherche si un noeud est utilisé dans la table voir_aussi.
	static function isUsedInSeeAlso($id_noeud=0) {
		
		global $dbh;
		
		if(!$id_noeud) return 0; 
		$q = "select count(1) from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		$q.= "or num_noeud_dest = '".$id_noeud."' ";
		$r = pmb_mysql_query($q, $dbh);
		return pmb_mysql_result($r, 0, 0);
	}		

	//Liste les noeuds de la table voir_aussi sous forme de resultset
	function listUsedInSeeAlso($id_noeud=0) {
	
		global $dbh;
		
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	
		$q = "select distinct if(num_noeud_orig!= ".$id_noeud.",num_noeud_orig,num_noeud_dest)as id_noeud from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		$q.= "or num_noeud_dest = '".$id_noeud."' ";
		$r = pmb_mysql_query($q, $dbh);
		return $r;
	}
	
	//optimization de la table noeuds
	static function optimize() {
		
		global $dbh;
		
		$opt = pmb_mysql_query('OPTIMIZE TABLE noeuds', $dbh);
		return $opt;
				
	}
	
	//vérification de l'unicité du numéro d'autorité dans le thésaurus
	static function isUnique($num_thesaurus, $num_aut='', $id_noeud=0) {
		
		global $dbh;
		if ($num_aut=='') return true;
		$q = 'select count(1) from noeuds where num_thesaurus=\''.$num_thesaurus.'\' ';
		$q.= 'and autorite=\''.addslashes($num_aut).'\' ';
		if ($id_noeud) $q.= 'and id_noeud != \''.$id_noeud.'\' ';
		$r = pmb_mysql_query($q, $dbh);
		if(pmb_mysql_result($r, 0, 0)==0) return true;
			else return false;
	}
	
	// ---------------------------------------------------------------
	//		replace_categ_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	function replace_categ_form($parent=0) {
		global $form_categ_replace;
		global $thesaurus_mode_pmb;
		global $msg;
		
		if(!$this->id_noeud) {
			error_message($msg[161], $msg[162], 1, "./autorites.php?categ=categories&sub=&parent=".$parent."&id=0");//Voir éventuelement pour mettre un message valable quand le cas se présentera
			return false;
		}
		
		$categ = new category($this->id_noeud);
		if ($thesaurus_mode_pmb) $nom_thesaurus='['.$categ->thes->getLibelle().'] ' ;
		else $nom_thesaurus='' ;
		$form_categ_replace=str_replace('!!old_categ_libelle!!',$nom_thesaurus.$categ->catalog_form, $form_categ_replace);
		$form_categ_replace=str_replace('!!id!!',$this->id_noeud, $form_categ_replace);
		$form_categ_replace=str_replace('!!parent!!',$this->num_parent, $form_categ_replace);
		print pmb_bidi($form_categ_replace);
		return true;
	}		
	
	// ---------------------------------------------------------------
	//		replace : Remplacement d'un noeud du thésaurus par un autre
	// ---------------------------------------------------------------
	function replace($by=0,$link_save=0) {
		global $msg,$dbh;
		global $pmb_synchro_rdf;
		
		if (($this->id_noeud == $by) || (!$this->id_noeud) || (!$by))  {
			return $msg["categ_imposible_remplace_elle_meme"];
		}
		
		$aut_link= new aut_link(AUT_TABLE_CATEG,$this->id_noeud);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_CATEG,$by);		
		}
		$aut_link->delete();
		
		//synchro_rdf : on empile les noeuds impactés pour les traiter plus loin
		if($pmb_synchro_rdf){
			$arrayIdImpactes=array();
			$arrayThesImpactes=array();
			$thes = thesaurus::getByEltId($this->id_noeud);
			$arrayThesImpactes[]=$thes->id_thesaurus;
			//parent
			if($this->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$this->num_parent;
			}
			//enfants
			$res=noeuds::listChilds($this->id_noeud,1);
			if(pmb_mysql_num_rows($res)){
				while($row=pmb_mysql_fetch_array($res)){
					$arrayIdImpactes[]=$row[0];
				}
			}
			//renvoi_voir
			if($this->num_renvoi_voir){
				$arrayIdImpactes[]=$this->num_renvoi_voir;
			}
		}
		
		$noeuds_a_garder = new noeuds($by);
		
		//Si les noeuds sont du même thésaurus
		if($noeuds_a_garder->num_thesaurus == $this->num_thesaurus){
			//On déplace les catégories qui renvoi vers l'ancien noeuds pour qu'elle renvoie vers le nouveau
			if(noeuds::isTarget($this->id_noeud)){
				$requete="UPDATE noeuds SET num_renvoi_voir='".$by."' WHERE num_renvoi_voir='".$this->id_noeud."' and id_noeud!='".$by."' ";
				@pmb_mysql_query($requete, $dbh);
			}
			//On garde les liens voir_aussi
			$requete="UPDATE ignore voir_aussi SET num_noeud_orig='".$by."' WHERE num_noeud_orig='".$this->id_noeud."' and num_noeud_dest!='".$by."' ";
			@pmb_mysql_query($requete, $dbh);
			$requete="UPDATE ignore voir_aussi SET num_noeud_dest='".$by."' WHERE num_noeud_dest='".$this->id_noeud."' and num_noeud_orig!='".$by."'";
			@pmb_mysql_query($requete, $dbh);
		}
		
		if(noeuds::isTarget($this->id_noeud)){//Si le noeuds à supprimé est utilisé pour des renvois et qu'il reste des liens on les supprime
			//On supprime les renvoies
			$requete="UPDATE noeuds SET num_renvoi_voir='0' WHERE num_renvoi_voir='".$this->id_noeud."'";
			@pmb_mysql_query($requete, $dbh);
		}
		
		//On déplace les notices liées
		$requete= "UPDATE ignore notices_categories SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		@pmb_mysql_query($requete, $dbh);

		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id_noeud." and authority_type = 'category'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->authority_favorite == 1){
					//on suprime les références si l'autorité a été importée...
					$query = "delete from notices_authorities_sources where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_result($query);
					$query = "delete from authorities_sources where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_result($query);
				}else{
					//on fait suivre le reste
					$query = "update authorities_sources set num_authority = ".$by." where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}
			}
		}
		//On supprime le noeuds
		$this->delete();
		
		//synchro_rdf
		if($pmb_synchro_rdf){
			//on ajoute les noeuds impactés par le $by
			$thesBy = thesaurus::getByEltId($by);
			if(!in_array($thesBy->id_thesaurus,$arrayThesImpactes)){
				$arrayThesImpactes[]=$thesBy->id_thesaurus;
			}
			$arrayIdImpactes[]=$by;
			//parent
			if($noeuds_a_garder->num_parent!=$thesBy->num_noeud_racine){
				$arrayIdImpactes[]=$noeuds_a_garder->num_parent;
			}
			//enfants
			$res=noeuds::listChilds($noeuds_a_garder->id_noeud,1);
			if(pmb_mysql_num_rows($res)){
				while($row=pmb_mysql_fetch_array($res)){
					$arrayIdImpactes[]=$row[0];
				}
			}
			//renvoi_voir
			if($noeuds_a_garder->num_renvoi_voir){
				$arrayIdImpactes[]=$noeuds_a_garder->num_renvoi_voir;
			}
			//On met le tout à jour
			$synchro_rdf=new synchro_rdf();
			$synchro_rdf->delConcept($this->id_noeud);
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			if(count($arrayThesImpactes)){
				foreach($arrayThesImpactes as $idThes){
					$synchro_rdf->updateAuthority($idThes, 'thesaurus');
				}
			}
		}
		
		return "";
	}
}
?>