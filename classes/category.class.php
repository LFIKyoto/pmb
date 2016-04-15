<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.class.php,v 1.38 2015-06-05 13:04:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'
if ( ! defined( 'CATEGORY_CLASS' ) ) {
  define( 'CATEGORY_CLASS', 1 );
require_once("$class_path/thesaurus.class.php");
require_once("$base_path/javascript/misc.inc.php");

require_once("$class_path/categories.class.php");
require_once("$class_path/noeuds.class.php");

//Renvoi récursivement la liste des notices référançant un noeuds et ses enfants
function get_category_notice_count($node_id, &$listcontent) {
	//On ajoute les notices du noeuds
	$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		$listcontent[] = $arow[0];
	}

	//Et on recurse		
	$asql = "SELECT id_noeud FROM noeuds WHERE num_parent = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		get_category_notice_count($arow[0], $listcontent);
	}
}

class category {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	var $id=0;
	var $libelle='';
	var $commentaire='';
	var $catalog_form=''; // forme pour affichage complet
	var $isbd_entry_lien_gestion=''; // pour affichage avec lien vers la gestion
	var $parent_id=0;
	var $parent_libelle = '';
	var $voir_id=0;
	var $has_child=FALSE;
	var $has_parent=FALSE;
	var $path_table;	// tableau contenant le path éclaté (ids et libellés)
	var $associated_terms; // tableau des termes associés
	var $is_under_tilde=0; // Savoir si c'est sous une catégorie qui commence par un ~
	var $thes;		//le thesaurus d'appartenance
	var $import_denied = 0;
	var $not_use_in_indexation=0; //Savoir si l'on peut utiliser le terme en indexation

	// ---------------------------------------------------------------
	//		category($id) : constructeur
	// ---------------------------------------------------------------
	function category($id=0) {
		if($id) {
			// on cherche à atteindre une notice existante
			$this->id = $id;
			$this->is_under_tilde=0;
			$this->thes = thesaurus::getByEltId($id);
			$this->getData();
		} else {
			// la notice n'existe pas
			$this->id = 0;
			$this->is_under_tilde=0;
			$this->getData();
		}
	}

	// ---------------------------------------------------------------
	//		getData() : récupération des propriétés
	// ---------------------------------------------------------------
	function getData() {
		global $dbh;
		global $lang;
		global $opac_url_base, $use_opac_url_base;
		global $thesaurus_categories_show_only_last ; // le paramètre pour afficher le chemin complet ou pas
		$anti_recurse=array();
		
		if(!$this->id) return;
	
		$requete = "SELECT noeuds.id_noeud as categ_id, ";
		$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
		$requete.= "noeuds.num_parent as categ_parent, ";
		$requete.= "noeuds.num_renvoi_voir as categ_see, ";
		$requete.= "noeuds.not_use_in_indexation as not_use_in_indexation, ";
		$requete.= "noeuds.authority_import_denied as authority_import_denied, ";	
		$requete.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as categ_comment ";
		$requete.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
		$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$requete.= "where noeuds.id_noeud = '".$this->id."' limit 1 ";
	
		$result = pmb_mysql_query($requete, $dbh);	
		if(!pmb_mysql_num_rows($result)) return;
		
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->categ_id;
		$id_top = $this->thes->num_noeud_racine;
		$this->libelle = $data->categ_libelle;
		if(preg_match("#^~#",$this->libelle)){
			$this->is_under_tilde=1;
		}
		$this->commentaire = $data->categ_comment;
		$this->parent_id = $data->categ_parent;
		$this->voir_id = $data->categ_see;
		$this->import_denied = $data->authority_import_denied;
		$this->not_use_in_indexation = $data->not_use_in_indexation;
		//$anti_recurse[$this->voir_id]=1;
		if($this->parent_id != $id_top) $this->has_parent = TRUE;
	
		$requete = "SELECT 1 FROM noeuds WHERE num_parent='".$this->id."' limit 1";
		$result = @pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($result)) $this->has_child = TRUE;
	
		// constitution du chemin
		$anti_recurse[$this->id]=1;
		$this->path_table=array();
		if ($this->has_parent) {
			$id_parent=$this->parent_id;
			do {
				$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$id_parent."' 
				AND noeuds.id_noeud = categories.num_noeud 
				order by p desc limit 1";
				$result=@pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$parent = pmb_mysql_fetch_object($result);
					if(preg_match("#^~#",$parent->categ_libelle)){
						$this->is_under_tilde=1;
					}
					$anti_recurse[$parent->categ_id]=1;
					$this->path_table[] = array(
								'id' => $parent->categ_id,
								'libelle' => $parent->categ_libelle,
								'commentaire' => $parent->categ_comment);
					$id_parent=$parent->categ_parent;
				} else {
					break;
				}
			} while (($parent->categ_parent != $id_top) &&(!$anti_recurse[$parent->categ_parent]));
		}
		
		// ceci remet le tableau dans l'ordre général->particulier
		$this->path_table = array_reverse($this->path_table);
	
		if ($thesaurus_categories_show_only_last) {
			$this->catalog_form = $this->libelle;
			
			// si notre catégorie a un parent, on initie la boucle en le récupérant
			/*
			$requete_temp = "SELECT noeuds.id_noeud as categ_id, ";
			$requete_temp.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle ";
			$requete_temp.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$requete_temp.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$requete_temp.= "where noeuds.id_noeud = '".$this->parent_id."' limit 1 ";
	
			ER 12/08/2008 NOUVELLE VERSION OPTIMISEE DESSOUS : */
			$requete_temp = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$this->parent_id."' 
				AND noeuds.id_noeud = categories.num_noeud 
				order by p desc limit 1";
			
			$result_temp=@pmb_mysql_query($requete_temp);
			if (pmb_mysql_num_rows($result_temp)) {
				$parent = pmb_mysql_fetch_object($result_temp);
				$this->parent_libelle = $parent->categ_libelle ;
			} else $this->parent_libelle ; 
	
		} elseif(sizeof($this->path_table)) {
			while(list($i, $l) = each($this->path_table)) {
				$temp_table[] = $l['libelle'];
			}
			$this->parent_libelle = join(':', $temp_table);
			$this->catalog_form = $this->parent_libelle.':'.$this->libelle;
		} else {
			$this->catalog_form = $this->libelle;
		}
	
		// Ajoute un lien sur la fiche catégorie si l'utilisateur à accès aux autorités, ou bien en envoi en OPAC.
		if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
		else $url_base_lien_aut="./autorites.php?categ=categories&sub=categ_form&id=";
		if (SESSrights & AUTORITES_AUTH || $use_opac_url_base) $this->isbd_entry_lien_gestion = "<a href='".$url_base_lien_aut.$this->id."' class='lien_gestion'>".$this->catalog_form."</a>";
		else $this->isbd_entry_lien_gestion = $this->catalog_form;
		
		//Recherche des termes associés
		$requete = "select count(1) from categories where num_noeud = '".$this->id."' and langue = '".$lang."' ";
		$result = pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_result($result, 0,0) == 0) $lg = $this->thes->langue_defaut ; 
		else $lg = $lang;  
	
		$requete = "SELECT distinct voir_aussi.num_noeud_dest as categ_assoc_categassoc, ";
		$requete.= "categories.libelle_categorie as categ_libelle, categories.note_application as categ_comment ";
		$requete.= "FROM voir_aussi, categories ";
		$requete.= "WHERE voir_aussi.num_noeud_orig='".$this->id."' ";
		$requete.= "AND categories.num_noeud=voir_aussi.num_noeud_dest "; 
		$requete.= "AND categories.langue = '".$lg."' ";
	
		$result=@pmb_mysql_query($requete,$dbh);
		while ($ta=pmb_mysql_fetch_object($result)) {
	
			//Recherche des renvois réciproques
			$requete1 = "select count(1) from voir_aussi where num_noeud_orig = '".$ta->categ_assoc_categassoc."' and num_noeud_dest = '".$this->id."' ";
			if (pmb_mysql_result(pmb_mysql_query($requete1, $dbh), 0, 0)) $rec=1;
			else $rec=0;
			
			$this->associated_terms[] = array(
				'id' => $ta->categ_assoc_categassoc,
				'libelle' => $ta->categ_libelle,
				'commentaire' => $ta->categ_comment,
				'rec' => $rec);
		}	 
	}

	function has_notices() {
		global $dbh;
		global $thesaurus_auto_postage_montant,$thesaurus_auto_postage_descendant,$thesaurus_auto_postage_nb_montant,$thesaurus_auto_postage_nb_descendant;
		global $thesaurus_auto_postage_etendre_recherche,$nb_level_enfants,$nb_level_parents;
		$thesaurus_auto_postage_descendant = $thesaurus_auto_postage_montant=0;
		// Autopostage actif
		if ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant ) {
			if(!isset($nb_level_enfants)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_enfants"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
				else $nb_level_descendant=$thesaurus_auto_postage_nb_descendant;
			} else {
				$nb_level_descendant=$nb_level_enfants;
			}				
			
			// lien Etendre auto_postage
			if(!isset($nb_level_parents)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_parents"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
				else $nb_level_montant=$thesaurus_auto_postage_nb_montant;
			} else {
				$nb_level_montant=$nb_level_parents;
			}	
			$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
			$_SESSION["nb_level_parents"]=	$nb_level_montant;
			
			$q = "select path from noeuds where id_noeud = '".$this->id."' ";
			$r = pmb_mysql_query($q);
			$path=pmb_mysql_result($r, 0, 0);
			$nb_pere=substr_count($path,'/');
			// Si un path est renseigné et le paramètrage activé			
			if ($path && ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
				
				//Recherche des fils 
				if(($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_etendre_recherche)&& $nb_level_descendant) {
					if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
						$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
					else 
						$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
				} else {
					$liste_fils=" id_noeud='".$this->id."' ";
				}
						
				// recherche des pères
				if(($thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && $nb_level_montant) {
					
					$id_list_pere=explode('/',$path);			
					$stop_pere=0;
					if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere=$nb_pere-$nb_level_montant;
					for($i=$nb_pere;$i>=$stop_pere; $i--) {
						$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
					}
				}			
				// requete permettant de remonter les notices associées à la liste des catégories trouvées;
				$suite_req = " FROM noeuds inner join notices_categories on id_noeud=num_noeud inner join notices on notcateg_notice=notice_id 
					WHERE ($liste_fils $liste_pere) and notices_categories.notcateg_notice = notices.notice_id ";					
			} else {	
				// cas normal d'avant		
				$suite_req=" FROM notices_categories, notices WHERE notices_categories.num_noeud = '".$this->id."' and notices_categories.notcateg_notice = notices.notice_id ";
			}	
		
			$query ="SELECT COUNT(1) ".$suite_req;
		} else {
			// Autopostage désactivé	
			$query ="SELECT COUNT(1) FROM notices_categories WHERE notices_categories.num_noeud='".$this->id."' ";
			
		}	 
		$result = pmb_mysql_query($query, $dbh);
		return (pmb_mysql_result($result, 0, 0));
	}

	function notice_count($include_subcategories=true) {
		/*
		 * $include_subcategories : Inclue également les notices dans les catégories filles
		 */
		if (!$include_subcategories) {
			$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$this->id;
			$ares = pmb_mysql_query($asql);
			while ($arow=pmb_mysql_fetch_row($ares)) {
				$listcontent[] = $arow[0];
			}
			$notice_count = count($listcontent);
			return $notice_count;
		}
		else {
			$listcontent = array();
			get_category_notice_count($this->id, $listcontent);
			$listcontent = array_unique($listcontent); //S'agirait pas d'avoir deux fois la même notice comptée.
			$notice_count = count($listcontent);
			return $notice_count;
		}
	}
	
	static function get_informations_from_unimarc($fields,$link = false,$code_field="250"){
		$data = array();
		if(!$link){
			$data['label'] = $fields[$code_field][0]['a'][0];
			if($fields[$code_field][0]['j']){
				for($i=0 ; $i<count($fields[$code_field][0]['j']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['j'][$i];
				}
			}
			if($fields[$code_field][0]['x']){
				for($i=0 ; $i<count($fields[$code_field][0]['x']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['x'][$i];
				}
			}
			if($fields[$code_field][0]['y']){
				for($i=0 ; $i<count($fields[$code_field][0]['y']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['y'][$i];
				}
			}
			if($fields[$code_field][0]['z']){
				for($i=0 ; $i<count($fields[$code_field][0]['z']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['z'][$i];
				}
			}		
			
			for ($i=0 ; $i<count($fields['300']) ; $i++){
				for($j=0 ; $j<count($fields['300'][$i]['a']) ; $j++){
					if($data['comment'] != "") $data['comment'].="\n";
					$data['comment'] .= $fields['300'][$i]['a'][$j];
				}
			}
			for ($i=0 ; $i<count($fields['330']) ; $i++){
				for($j=0 ; $j<count($fields['330'][$i]['a']) ; $j++){
					if($data['note'] != "") $data['note'].="\n";
					$data['note'] .= $fields['330'][$i]['a'][$j];
				}
			}
		}else{
			$data['label'] = $fields['a'][0];
			if($fields['j']){
				for($i=0 ; $i<count($fields['j']) ; $i++){
					$data['label'] .=  " -- ".$fields['j'][$i];
				}
			}
			if($fields['x']){
				for($i=0 ; $i<count($fields['x']) ; $i++){
					$data['label'] .=  " -- ".$fields['x'][$i];
				}
			}
			if($fields['y']){
				for($i=0 ; $i<count($fields['y']) ; $i++){
					$data['label'] .=  " -- ".$fields['y'][$i];
				}
			}
			if($fields['z']){
				for($i=0 ; $i<count($fields['z']) ; $i++){
					$data['label'] .=  " -- ".$fields['z'][$i];
				}
			}		
			$data['authority_number'] = $fields['3'][0];
		}
		$data['type_authority'] = "category";
		return $data; 
	}
	
	function import($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}
		
		$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
			if(!$id){
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie=$data['label'];
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}else{
				$c=new categories($id, $lang);
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}
		}else{
			//pas de thésausus, on peut rien faire...
			return 0;
		}
		return $id;
	}
	
	function check_if_exists($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		
		$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
		$result = pmb_mysql_query($query);
		$id=0;
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
		}
		return $id;
	}
	
	/*
	 * Pour import autorité
	 */
	function update($data,$id_thesaurus,$num_parent,$lang){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}
		if($this->id == 0){
			$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie= $data['label'];
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
				$this->id = $c->num_noeud;
				return 1;
			}
		}else{
			$c=new categories($this->id, $lang);
			$c->libelle_categorie= $data['label'];
			$c->note_application = $data['note'];
			$c->comment_public = $data['comment'];
			$c->save();
			return 1;
		}
	}
	
	
} # fin de définition de la classe category

} # fin de déclaration
