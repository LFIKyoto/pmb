<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_update.inc.php,v 1.28 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// si tout est OK, on a les variables suivantes à exploiter :
// $id			id de la catégorie (0 si nouvelle)
// $category_libelle	libellé de la catégorie
// $category_comment	commentaire de la catégorie
// $category_parent_id	id de la catégorie parent
// $category_parent	libellé de la catégorie parent
// 		note : peut être vide si l'utilisateur a vidé le champ -> suppression du parent dans ce cas
// $category_voir_id	id de la forme retenue
// $category_voir	libelle de la forme retenue
// 		même remarque que pour $category_parent

require_once("$class_path/category.class.php");
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/categories.class.php");
require_once("$class_path/XMLlist.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/map/map_edition_controler.class.php");

if (noeuds::isRacine($id)) {
	error_form_message($msg['categ_forb']);
	exit();		
}

if(!strlen($category_parent)) $category_parent_id = 0;
if(!strlen($category_voir)) $category_voir_id = 0;

if ($id && ($category_parent_id==$id || $category_voir_id==$id)) {
	error_form_message($msg["categ_update_error_parent_see"]);
	exit ;
}

//recuperation de la table des langues
$langages = new XMLlist("$include_path/messages/languages.xml", 1);
$langages->analyser();
$lg = $langages->table;

//recuperation du thesaurus session 
$id_thes = thesaurus::getSessionThesaurusId();
$thes = new thesaurus($id_thes);	

// libelle langue defaut thesaurus non renseigne
if ( (trim($category_libelle[$thes->langue_defaut])) == '' ) {
	error_form_message($msg["thes_libelle_categ_ref_manquant"].'\n('.$lg[$thes->langue_defaut].')');
	exit ;	
}

//Vérification de l'unicité du numéro d'autorité
$num_aut=trim(stripslashes($num_aut));

if ($num_aut && !noeuds::isUnique($id_thes, $num_aut,$id) ) {
	error_form_message($msg['categ_num_aut_not_unique']);
	exit;
}

//Si pas de parent, le parent est le noeud racine du thesaurus
if (!$category_parent_id) $category_parent_id = $thes->num_noeud_racine;

//synchro_rdf : on empile les noeuds impactés pour les traiter plus loin
if($pmb_synchro_rdf){
	$arrayIdImpactes=array();
	if($id){
		$noeud=new noeuds($id);
		//on est en mise à jour
		$arrayIdImpactes[]=$id;
		//parent
		if($noeud->num_parent!=$thes->num_noeud_racine){
			$arrayIdImpactes[]=$noeud->num_parent;
		}
		//enfants
		$res=noeuds::listChilds($id,1);
		if(pmb_mysql_num_rows($res)){
			while($row=pmb_mysql_fetch_array($res)){
				$arrayIdImpactes[]=$row[0];
			}
		}
		//renvoi_voir
		if($noeud->num_renvoi_voir){
			$arrayIdImpactes[]=$noeud->num_renvoi_voir;
		}
	}else{
		//on est en création : rien à supprimer
	}
}
//traitement noeud
if($id) {
	//noeud existant
	$noeud = new noeuds($id);
	if (!noeuds::isProtected($id)) {
		$noeud->num_parent = $category_parent_id;
		$noeud->num_renvoi_voir = $category_voir_id;
		$noeud->authority_import_denied = $authority_import_denied;
		$noeud->not_use_in_indexation = $not_use_in_indexation;
		$noeud->autorite=$num_aut;
		$noeud->save();
	}
} else {
	//noeud a creer
	$noeud = new noeuds();
	$noeud->num_parent = $category_parent_id;
	$noeud->num_renvoi_voir = $category_voir_id;
	$noeud->autorite=$num_aut;
	$noeud->num_thesaurus = $thes->id_thesaurus;
	$noeud->authority_import_denied = $authority_import_denied;
	$noeud->not_use_in_indexation = $not_use_in_indexation;
	$noeud->save();
	$id = $noeud->id_noeud;
}
// Indexation concepts
if($thesaurus_concepts_active == 1 ){
	$index_concept = new index_concept($id, TYPE_CATEGORY);
	$index_concept->save();
}
// liens entre autorités 
require_once("$class_path/aut_link.class.php");
$aut_link= new aut_link(AUT_TABLE_CATEG,$id);
$aut_link->save_form();

global $pmb_map_activate;
if($pmb_map_activate){
	$map = new map_edition_controler(AUT_TABLE_CATEG, $id);
	$map->save_form();
}

$aut_pperso= new aut_pperso("categ",$id);
$aut_pperso->save_form();

//traitement categories 
foreach($lg as $key=>$value) {

	if ( ($category_libelle[$key]) !== NULL ) {
		
		if ( ($category_libelle[$key] !== '')  || 
		 ( ($category_libelle[$key] === '') && (categories::exists($id, $key)) ) ){

			$cat = new categories($id, $key);
			$cat->libelle_categorie = stripslashes($category_libelle[$key]);	
			$cat->note_application = stripslashes($category_na[$key]);
			$cat->comment_public = stripslashes($category_cm[$key]);			
			$cat->index_categorie = strip_empty_words($category_libelle[$key]);
			$cat->save();
		 }
	}
}


if (!noeuds::isProtected($id)) {

	//Ajout des renvois "voir aussi"
	$requete="DELETE FROM voir_aussi WHERE num_noeud_orig=".$id;
	pmb_mysql_query($requete);
	for ($i=0; $i<$max_categ; $i++) {
		$categ_id="f_categ_id".$i;
		$categ_rec = "f_categ_rec".$i;
		if ($$categ_id && $$categ_id!=$id) {
			$requete="INSERT INTO voir_aussi (num_noeud_orig, num_noeud_dest, langue) VALUES ($id,".$$categ_id.",'".$thes->langue_defaut."' )";
			@pmb_mysql_query($requete);
			if ($$categ_rec) {
				$requete="INSERT INTO voir_aussi (num_noeud_orig, num_noeud_dest, langue) VALUES (".$$categ_id.",".$id.",'".$thes->langue_defaut."' )";				
			} else {
				$requete="DELETE from voir_aussi where num_noeud_dest = '".$id."' and num_noeud_orig = '".$$categ_id."'	";
			}
			@pmb_mysql_query($requete);
	
		}
	}
}
//synchro_rdf : le noeud a été créé/modifié
if($pmb_synchro_rdf){
	//De nouveaux noeuds impactés ?
	if((!count($arrayIdImpactes))||(!in_array($id,$arrayIdImpactes))){
		$arrayIdImpactes[]=$id;
	}
	if($noeud->num_parent!=$thes->num_noeud_racine){
		if((!count($arrayIdImpactes))||(!in_array($noeud->num_parent,$arrayIdImpactes))){
			$arrayIdImpactes[]=$noeud->num_parent;
		}
	}
	//enfants
	$res=noeuds::listChilds($id,1);
	if(pmb_mysql_num_rows($res)){
		while($row=pmb_mysql_fetch_array($res)){
			if((!count($arrayIdImpactes))||(!in_array($row[0],$arrayIdImpactes))){
				$arrayIdImpactes[]=$row[0];
			}
		}
	}
	//renvoi_voir
	if($noeud->num_renvoi_voir){
		if((!count($arrayIdImpactes))||(!in_array($noeud->num_renvoi_voir,$arrayIdImpactes))){
			$arrayIdImpactes[]=$noeud->num_renvoi_voir;
		}
	}
	//on met le tout à jour
	$synchro_rdf=new synchro_rdf();
	if(count($arrayIdImpactes)){
		foreach($arrayIdImpactes as $idNoeud){
			$synchro_rdf->delConcept($idNoeud);
			$synchro_rdf->storeConcept($idNoeud);
		}
	}
	//On met à jour le thésaurus pour les topConcepts
	$synchro_rdf->updateAuthority($id_thes,'thesaurus');
}

if ($user_input != "") {
	include('./autorites/subjects/search.inc.php');
} else {
	include('./autorites/subjects/default.inc.php');
}
