<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollection.class.php,v 1.50 2015-06-05 13:04:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'sous-collections'

if ( ! defined( 'SUB_COLLECTION_CLASS' ) ) {
  define( 'SUB_COLLECTION_CLASS', 1 );

require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once($class_path."/subcollection.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");

class subcollection {

// ---------------------------------------------------------------
//		propriétés de la classe
// ---------------------------------------------------------------
	var $id;				// MySQL id in table 'collections'
	var $name;				// collection name
	var $parent;			// MySQL id of parent collection
	var $parent_libelle;	// name of parent collection
	var $editeur;			// MySQL id of publisher
	var $editeur_libelle;	// name of parent publisher
	var $editor_isbd;		// isbd form of publisher
	var $display;			// usable form for displaying	( _collection_. _name_ (_editeur_) )
	var $isbd_entry;		// ISBD form ( _collection_. _name_ )
	var $issn;				// ISSN of sub collection
	var $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	var $subcollection_web;			// web de sous-collection
	var $subcollection_web_link;	// lien web de sous-collection

	// ---------------------------------------------------------------
	//		subcollection($id) : constructeur
	// ---------------------------------------------------------------
	function subcollection($id=0) {
		if($id) {
			// on cherche à atteindre une notice existante
			$this->id = $id;
			$this->getData();
		} else {
			// la notice n'existe pas
			$this->id = 0;
			$this->getData();
		}
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos sous collection
	// ---------------------------------------------------------------
	function getData() {
		global $dbh;
		if(!$this->id) {
			// pas d'identifiant. on retourne un tableau vide
			$this->id = 0;
			$this->name				=	'';
			$this->parent			=	0;
			$this->parent_libelle	=	'';
			$this->editeur			=	0;
			$this->editeur_libelle	=	'';
			$this->display			=	'';
			$this->isbd_entry		=	'';
			$this->issn				=	'';
			$this->subcollection_web	= '';
			$this->subcollection_web_link = "" ;
			$this->comment = "" ;
		} else {
			$requete = "SELECT * FROM sub_collections WHERE sub_coll_id=$this->id LIMIT 1 ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->id= $temp->sub_coll_id;
				$this->name= $temp->sub_coll_name;
				$this->parent= $temp->sub_coll_parent;
				$this->issn= $temp->sub_coll_issn;
				$this->subcollection_web	= $temp->subcollection_web;
				$this->comment	= $temp->subcollection_comment;
				if($temp->subcollection_web) $this->subcollection_web_link = " <a href='$temp->subcollection_web' target=_blank><img src='./images/globe.gif' border=0 /></a>";
				else $this->subcollection_web_link = "" ;
				$parent = new collection($temp->sub_coll_parent);
				$this->parent_libelle = $parent->name;
				$parent_libelle_lien_gestion = $parent->isbd_entry_lien_gestion ;
				$this->editeur = $parent->parent;
				$editeur = new editeur($parent->parent);
				$this->editeur_libelle = $editeur->name;
				$this->editor_isbd = $editeur->isbd_entry;
				$this->issn ? $this->isbd_entry = $this->parent_libelle.'.&nbsp;'.$this->name.', ISSN '.$this->issn : $this->isbd_entry = $this->parent_libelle.'.&nbsp;'.$this->name ;
				$this->display = $this->parent_libelle.'. '.$this->name.' ('.$this->editeur_libelle.')';
				// Ajoute un lien sur la fiche sous-collection si l'utilisateur à accès aux autorités
				if (SESSrights & AUTORITES_AUTH) {
					if ($this->issn) 
						$lien_lib = $this->name.', ISSN '.$this->issn ;
					else 
						$lien_lib = $this->name ;
					$this->isbd_entry_lien_gestion = $parent_libelle_lien_gestion.".&nbsp;<a href='./autorites.php?categ=souscollections&sub=collection_form&id=".$this->id."' class='lien_gestion'>".$lien_lib."</a>";
				} else 
					$this->isbd_entry_lien_gestion = $this->isbd_entry;
					
			} else {
				// pas de sous-collection avec cette clé
				$this->id = 0;
				$this->name				=	'';
				$this->parent			=	0;
				$this->parent_libelle	=	'';
				$this->editeur			=	0;
				$this->editeur_libelle	=	'';
				$this->display			=	'';
				$this->isbd_entry		=	'';
				$this->issn				=	'';
				$this->subcollection_web = '';
				$this->comment = '';
				$this->subcollection_web_link = "" ;
			}
		}
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression de la sous collection
	// ---------------------------------------------------------------
	function delete() {
		global $dbh;
		global $msg;
		
		if(!$this->id)
			// impossible d'accéder à cette notice de sous-collection
			return $msg[406];
	
		// récupération du nombre de notices affectées
		$requete = "SELECT COUNT(1) FROM notices WHERE ";
		$requete .= "subcoll_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		if(!$nbr_lignes) {

			// On regarde si l'autorité est utilisée dans des vedettes composées
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, "subcollection");
			if (count($attached_vedettes)) {
				// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
				return '<strong>'.$this->display."</strong><br />".$msg["vedette_dont_del_autority"];
			}
			
			// sous collection non-utilisée dans des notices : Suppression OK
			// effacement dans la table des collections
			$requete = "DELETE FROM sub_collections WHERE sub_coll_id=".$this->id;
			$result = pmb_mysql_query($requete, $dbh);
			//suppression dans la table de stockage des numéros d'autorités...
			//Import d'autorité
			subcollection::delete_autority_sources($this->id);
			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
			$aut_link->delete();
			$aut_pperso= new aut_pperso("subcollection",$this->id);
			$aut_pperso->delete();
			
			// nettoyage indexation concepts
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$index_concept->delete();
			
			audit::delete_audit(AUDIT_SUB_COLLECTION,$this->id);
			return false;
		} else {
			// Cette collection est utilisé dans des notices, impossible de la supprimer
			return '<strong>'.$this->display."</strong><br />${msg[407]}";
		}
	}
	
	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	static function delete_autority_sources($idsubcol=0){
		$tabl_id=array();
		if(!$idsubcol){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN sub_collections ON num_authority=sub_coll_id  WHERE authority_type = 'subcollection' AND sub_coll_id IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idsubcol;
		}
		foreach ( $tabl_id as $value ) {
	       //suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'subcollection'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'subcollection'";
			pmb_mysql_query($query);
		}
	}
	
	// ---------------------------------------------------------------
	//		replace($by) : remplacement de la collection
	// ---------------------------------------------------------------
	function replace($by,$link_save=0) {
	
		global $msg;
		global $dbh;
	
		if(!$by) {
			// pas de valeur de remplacement !!!
			return "serious error occured, please contact admin...";
		}
	
		if (($this->id == $by) || (!$this->id))  {
			// impossible de remplacer une collection par elle-même
			return $msg[226];
		}
		// a) remplacement dans les notices
		// on obtient les infos de la nouvelle collection
	
		$n_collection = new subcollection($by);
		if(!$n_collection->parent) {
			// la nouvelle collection est foireuse
			return $msg[406];
		}
		
		$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_SUB_COLLECTIONS,$by);		
		}
		$aut_link->delete();
		
		$requete = "UPDATE notices SET ed1_id=".$n_collection->editeur;
		$requete .= ", coll_id=".$n_collection->parent;
		$requete .= ", subcoll_id=$by WHERE subcoll_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
	
		// b) suppression de la collection
		$requete = "DELETE FROM sub_collections WHERE sub_coll_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		
		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id." and authority_type = 'subcollection'";
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
		audit::delete_audit (AUDIT_COLLECTION, $this->id);
		
		subcollection::update_index($by);
	
		return FALSE;
	}
	
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	function show_form() {
	
		global $msg;
		global $sub_collection_form;
		global $charset;
		global $pmb_type_audit;
		global $thesaurus_concepts_active;
	
		if($this->id) {
			$action = "./autorites.php?categ=souscollections&sub=update&id=$this->id";
			$libelle = $msg[178];
			$button_replace = "<input type='button' class='bouton' value='$msg[158]' ";
			$button_replace .= "onClick=\"unload_off();document.location='./autorites.php?categ=souscollections&sub=replace&id=$this->id';\">";
	
			$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' ";
			$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=2&etat=aut_search&aut_type=subcoll&aut_id=$this->id\"'>";
	
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
			$button_delete .= "onClick=\"confirm_delete();\">";
		} else {
			$action = './autorites.php?categ=souscollections&sub=update&id=';
			$libelle = $msg[177];
			$button_replace = '';
			$button_delete ='';
		}
		$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
		$sub_collection_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_sub_collection') , $sub_collection_form);
		
		$aut_pperso= new aut_pperso("subcollection",$this->id);
		$sub_collection_form = str_replace('!!aut_pperso!!',	$aut_pperso->get_form(), $sub_collection_form);
		
		$sub_collection_form = str_replace('!!id!!', $this->id, $sub_collection_form);
		$sub_collection_form = str_replace('!!libelle!!', $libelle, $sub_collection_form);
		$sub_collection_form = str_replace('!!action!!', $action, $sub_collection_form);
		$sub_collection_form = str_replace('!!collection_nom!!', htmlentities($this->name,ENT_QUOTES, $charset), $sub_collection_form);
		$sub_collection_form = str_replace('!!coll_id!!', $this->parent, $sub_collection_form);
		$sub_collection_form = str_replace('!!coll_libelle!!', htmlentities($this->parent_libelle,ENT_QUOTES, $charset), $sub_collection_form);
		$sub_collection_form = str_replace('!!ed_libelle!!', htmlentities($this->editeur_libelle,ENT_QUOTES, $charset), $sub_collection_form);
		$sub_collection_form = str_replace('!!ed_id!!', $this->editeur, $sub_collection_form);
		$sub_collection_form = str_replace('!!issn!!', $this->issn, $sub_collection_form);
		$sub_collection_form = str_replace('!!delete!!', $button_delete, $sub_collection_form);
		$sub_collection_form = str_replace('!!remplace!!', $button_replace, $sub_collection_form);
		$sub_collection_form = str_replace('!!voir_notices!!', $button_voir, $sub_collection_form);
		$sub_collection_form = str_replace('!!subcollection_web!!',		htmlentities($this->subcollection_web,ENT_QUOTES, $charset),	$sub_collection_form);
		$sub_collection_form = str_replace('!!comment!!',		htmlentities($this->comment,ENT_QUOTES, $charset),	$sub_collection_form);
		// pour retour à la bonne page en gestion d'autorités
		// &user_input=".rawurlencode(stripslashes($user_input))."&nbr_lignes=$nbr_lignes&page=$page
		global $user_input, $nbr_lignes, $page ;
		$sub_collection_form = str_replace('!!user_input_url!!',		rawurlencode(stripslashes($user_input)),			$sub_collection_form);
		$sub_collection_form = str_replace('!!user_input!!',			htmlentities($user_input,ENT_QUOTES, $charset),		$sub_collection_form);
		$sub_collection_form = str_replace('!!nbr_lignes!!',			$nbr_lignes,										$sub_collection_form);
		$sub_collection_form = str_replace('!!page!!',					$page,												$sub_collection_form);
		if( $thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$sub_collection_form = str_replace('!!concept_form!!',			$index_concept->get_form('saisie_sub_collection'),	$sub_collection_form);
		}else{
			$sub_collection_form = str_replace('!!concept_form!!',			"",	$sub_collection_form);
		}
		if ($pmb_type_audit && $this->id)
			$bouton_audit= "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=".AUDIT_SUB_COLLECTION."&object_id=".$this->id."', 'audit_popup', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />&nbsp;";
		
		$sub_collection_form = str_replace('!!audit_bt!!',				$bouton_audit,												$sub_collection_form);
		
		print $sub_collection_form;
	}
	
	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	function replace_form() {
		global $sub_coll_rep_form;
		global $msg;
		global $include_path;
		
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, './autorites.php?categ=collections&sub=&id=');
			return false;
		}
	
		$sub_coll_rep_form=str_replace('!!id!!', $this->id, $sub_coll_rep_form);
		$sub_coll_rep_form=str_replace('!!subcoll_name!!', $this->display, $sub_coll_rep_form);
		print $sub_coll_rep_form;
	}
	
	
	// ---------------------------------------------------------------
	//		?? update($value) : mise à jour de la collection
	// ---------------------------------------------------------------
	function update($value,$force_creation = false) {
	
		global $dbh;
		global $msg,$charset;
		global $include_path;
		global $thesaurus_concepts_active;
		
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$value['parent']){
			if($value['collection']){
				//on les a, on crée l'éditeur
				$value['collection']=stripslashes_array($value['collection']);//La fonction d'import fait les addslashes contrairement à l'update
				$value['parent'] = collection::import($value['collection']);
			}
		}
		
		if(!$value['name'] || !$value['parent'])
			return false;
	
		// nettoyage des valeurs en entrée
		$value['name'] = clean_string($value['name']);
	
		// construction de la requête
		$requete = "SET sub_coll_name='$value[name]', ";
		$requete .= "sub_coll_parent='$value[parent]', ";
		$requete .= "sub_coll_issn='$value[issn]', ";
		$requete .= "subcollection_web='$value[subcollection_web]', ";
		$requete .= "subcollection_comment='$value[comment]', ";
		$requete .= "index_sub_coll=' ".strip_empty_words($value[name])." ".strip_empty_words($value["issn"])." '";
	
		if($this->id) {
			// update
			$requete = 'UPDATE sub_collections '.$requete;
			$requete .= ' WHERE sub_coll_id='.$this->id.' ';
			if(pmb_mysql_query($requete, $dbh)) {
				$requete = "select collection_parent from collections WHERE collection_id='".$value[parent]."' ";
				$res = pmb_mysql_query($requete, $dbh) ;
				$ed_parent = pmb_mysql_result($res, 0, 0);
				$requete = "update notices set ed1_id='$ed_parent', coll_id='".$value[parent]."' WHERE subcoll_id='".$this->id."' ";
				$res = pmb_mysql_query($requete, $dbh) ;
				$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("subcollection",$this->id);
				$aut_pperso->save_form();
				audit::insert_modif (AUDIT_SUB_COLLECTION, $this->id) ;
				subcollection::update_index($this->id);
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[178],htmlentities($msg[182]." -> ".$this->display,ENT_QUOTES, $charset));
				return FALSE;
			}
		} else {
			if(!$force_creation){
				// création : s'assurer que la sous-collection n'existe pas déjà
				if ($id_subcollection_exists = subcollection::check_if_exists($value)) {
					$subcollection_exists = new subcollection($id_subcollection_exists);
					require_once("$include_path/user_error.inc.php");
					warning($msg[177],htmlentities($msg[219]." -> ".$subcollection_exists->display,ENT_QUOTES, $charset));
					return FALSE;
				}
			}
			$requete = 'INSERT INTO sub_collections '.$requete.';';
			if(pmb_mysql_query($requete, $dbh)) {
				$this->id=pmb_mysql_insert_id();
				$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
				$aut_link->save_form();			
				$aut_pperso= new aut_pperso("subcollection",$this->id);
				$aut_pperso->save_form();
				audit::insert_creation (AUDIT_SUB_COLLECTION, $this->id) ;
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[177],htmlentities($msg[182]." -> ".$requete,ENT_QUOTES, $charset));
				return FALSE;
			}
		}
		// Indexation concepts
		if( $thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->id, "subcollection");
		
		return TRUE;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'une sous-collection
	// ---------------------------------------------------------------
	// fonction d'import de sous-collection (membre de la classe 'subcollection');
	function import($data) {
	
		// cette méthode prend en entrée un tableau constitué des informations éditeurs suivantes :
		//	$data['name'] 	Nom de la collection
		//	$data['coll_parent']	id de l'éditeur parent de la collection
		//	$data['issn']	numéro ISSN de la collection
	
		global $dbh;
	
		// check sur le type de  la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
	
		// check sur les éléments du tableau (data['name'] est requis).
		
		$long_maxi_name = pmb_mysql_field_len(pmb_mysql_query("SELECT sub_coll_name FROM sub_collections limit 1"),0);
		$data['name'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))),0,$long_maxi_name));
	
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$data['coll_parent']){
			if($data['collection']){
				//on les a, on crée l'éditeur
				$data['coll_parent'] = collection::import($data['collection']);
			}
		}	
		
		if($data['name']=="" || $data['coll_parent']==0) /* il nous faut impérativement une collection parente */
			return 0;
	
		// préparation de la requête
		$key0 = addslashes($data['name']);
		$key1 = $data['coll_parent'];
		$key2 = addslashes($data['issn']);
		
		/* vérification que la collection existe bien ! */
		$query = "SELECT collection_id FROM collections WHERE collection_id='${key1}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT colections ".$query);
		if (pmb_mysql_num_rows($result)==0) 
			return 0;
	
		/* vérification que la sous-collection existe */
		$query = "SELECT sub_coll_id FROM sub_collections WHERE sub_coll_name='${key0}' AND sub_coll_parent='${key1}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT sub_collections ".$query);
		$subcollection  = pmb_mysql_fetch_object($result);
	
		/* la sous-collection existe, on retourne l'ID */
		if($subcollection->sub_coll_id)
			return $subcollection->sub_coll_id;
	
		// id non-récupérée, il faut créer la forme.
		$query = "INSERT INTO sub_collections SET sub_coll_name='$key0', ";
		$query .= "sub_coll_parent='$key1', ";
		$query .= "sub_coll_issn='$key2', ";
		$query .= "subcollection_web='".addslashes($data["subcollection_web"])."', ";
		$query .= "subcollection_comment='".addslashes($data["comment"])."', ";
		$query .= "index_sub_coll=' ".strip_empty_words($key0)." ".strip_empty_words($key2)." ' ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into sub_collections".$query);
		$id=pmb_mysql_insert_id($dbh);
		audit::insert_creation (AUDIT_COLLECTION, $id) ;
		return $id;
	}
		
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	static function search_form() {
		global $user_query, $user_input;
		global $msg, $charset;
	
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[137] , $user_query);
		$user_query = str_replace ('!!action!!', './autorites.php?categ=souscollections&sub=reach&id=', $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[176] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', './autorites.php?categ=souscollections&sub=collection_form', $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='./autorites.php?categ=souscollections&sub=collection_last'>$msg[1313]</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		print pmb_bidi($user_query) ;
	}
	
	//---------------------------------------------------------------
	// update_index($id) : maj des n-uplets la table notice_global_index en rapport avec cet sous collection	
	//---------------------------------------------------------------
	static function update_index($id) {
		global $dbh;
		// On cherche tous les n-uplet de la table notice correspondant à cet auteur.
		$found = pmb_mysql_query("select distinct notice_id from notices where subcoll_id='".$id."'",$dbh);
		// Pour chaque n-uplet trouvés on met a jour la table notice_global_index avec l'auteur modifié :
		while(($mesNotices = pmb_mysql_fetch_object($found))) {
			$notice_id = $mesNotices->notice_id;
			notice::majNoticesGlobalIndex($notice_id);
			notice::majNoticesMotsGlobalIndex($notice_id,'subcollection');
		}
	}
	
	static function get_informations_from_unimarc($fields,$from_collection=false){
		$data = array();
		
		if($from_collection){
			for($i=0 ; $i<count($fields['411']) ; $i++){
				$sub = array();
				$sub['authority_number'] = $fields['411'][$i]['0'][0];
				$sub['issn'] = $fields['411'][$i]['x'][0];
				$sub['name'] = $fields['411'][$i]['t'][0];	
				$data[] = $sub;
			}
		}else{
			$data['name'] = $fields['200'][0]['a'][0];
			if(count($fields['200'][0]['i'])){
				foreach ( $fields['200'][0]['i'] as $value ) {
	       			$data['name'].= ". ".$value;
				}
			}
			if(count($fields['200'][0]['e'])){
				foreach ( $fields['200'][0]['e'] as $value ) {
	       			$data['name'].= " : ".$value;
				}
			} 
			$data['issn'] = $fields['011'][0]['a'][0];	
			$data['collection'] = collection::get_informations_from_unimarc($fields,true);
			
		}
		return $data;
	}
	
	static function check_if_exists($data){
		global $dbh;
	
		if (!$data['coll_parent'] && $data['parent']) $data['coll_parent'] = $data['parent'];
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$data['coll_parent']){
			if($data['collection']){
				//on les a, on crée l'éditeur
				$data['coll_parent'] = collection::check_if_exists($data['collection']);
			}
		}	
	
		// préparation de la requête
		$key0 = addslashes($data['name']);
		$key1 = $data['coll_parent'];
		$key2 = addslashes($data['issn']);
		
		/* vérification que la sous-collection existe */
		$query = "SELECT sub_coll_id FROM sub_collections WHERE sub_coll_name='${key0}' AND sub_coll_parent='${key1}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT sub_collections ".$query);
		$subcollection  = pmb_mysql_fetch_object($result);
	
		/* la sous-collection existe, on retourne l'ID */
		if($subcollection->sub_coll_id)
			return $subcollection->sub_coll_id;
		return 0;
	}
} # fin de définition de la classe collection

} # fin de délaration
