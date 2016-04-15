<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.php,v 1.17.6.2 2015-12-09 14:09:30 jpermanne Exp $
//
// Affichage de la zone de recherche et choix du mode de navigation dans les catégories

$base_path="..";                            
$base_auth = "";  
$base_title = "Selection";

require_once ("$base_path/includes/init.inc.php");  
require_once("$class_path/marc_table.class.php");
require_once("$class_path/thesaurus.class.php");

// modules propres à select.php ou à ses sous-modules
include_once ("$javascript_path/misc.inc.php");
print reverse_html_entities();

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "category.php?caller=$caller&p1=$p1&p2=$p2&perso_id=$perso_id&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&keep_tilde=$keep_tilde&parent=&callback=".$callback."&infield=".$infield
			."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field."&id_thes_unique=$id_thes_unique&autoindex_class=autoindex_record";
require_once("$base_path/selectors/templates/category.tpl.php");
 

require_once ("$base_path/selectors/category_autoindex.inc.php");

print $sel_header;

if($id_thes_unique>0)$id_thes=$id_thes_unique;
else{
	//recuperation du thesaurus session en fonction du caller 
	switch ($caller) {
		case 'notice' :
			if (!$id_thes) $id_thes = thesaurus::getNoticeSessionThesaurusId();
			if (!$perso_id) thesaurus::setNoticeSessionThesaurusId($id_thes);
			break;
		case 'categ_form' :
			if (!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			if( $dyn!=2) thesaurus::setSessionThesaurusId($id_thes);
			break;
		default :
			if (!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			thesaurus::setSessionThesaurusId($id_thes);
			break;
	}
}
$thes = new thesaurus($id_thes);


//affichage du selectionneur de thesaurus
$liste_thesaurus = thesaurus::getThesaurusList();

$sel_thesaurus = '';
if ($thesaurus_mode_pmb != 0 && !$id_thes_unique) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	$sel_thesaurus = "<select class='saisie-20em' id='id_thes' name='id_thes' ";

	//si on vient du form de categories, le choix du thesaurus n'est pas possible
	if($caller == 'categ_form' && $dyn!=2) $sel_thesaurus.= "disabled "; 
	if($search_type!='autoindex') {
		$sel_thesaurus.= "onchange = \"this.form.submit()\">" ;
	} else {
		$sel_thesaurus.= '>' ;
	}
	foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
		$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
		if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
		$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
	}
	$sel_thesaurus.= "<option value=-1 ";
	if ($id_thes == -1) $sel_thesaurus.= "selected ";
	$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
	$sel_thesaurus.= "</select>&nbsp;";
}	
$sel_search_form=str_replace("!!sel_thesaurus!!",$sel_thesaurus,$sel_search_form);


// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;

if(!$f_user_input && !$user_input) {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if(!$user_input && $f_user_input) $user_input = $f_user_input;
}
// indexation auto
$sel_search_form=str_replace("!!sel_index_auto!!",get_autoindex_form(),$sel_search_form);

switch ($search_type) {
	case "term":
		$sel_search_form=str_replace("!!t_checked!!","checked",$sel_search_form);
		$sel_search_form=str_replace("!!h_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!autoindex_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!display_search_part!!","block",$sel_search_form);
		$src='term_browse.php';
		break;	
	case "autoindex":
		$sel_search_form=str_replace("!!t_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!h_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!autoindex_checked!!","checked",$sel_search_form);
		$sel_search_form=str_replace("!!display_search_part!!","none",$sel_search_form);
		break;	
	default:
		$sel_search_form=str_replace("!!h_checked!!","checked",$sel_search_form);
		$sel_search_form=str_replace("!!t_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!autoindex_checked!!","",$sel_search_form);
		$sel_search_form=str_replace("!!display_search_part!!","block",$sel_search_form);
		$src='category_browse.php';
		break;
}

$sel_search_form=str_replace("!!f_user_input_value!!",htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset),$sel_search_form);
print $sel_search_form;

if($search_type == "autoindex"){	
	echo $jscript_term;
	print display_autoindex_list();
	print $sel_footer;
	exit;
}	


if(!$parent) $parent=0;
print "
<script type='text/javascript' >
	parent.document.getElementsByTagName( 'frameset' )[ 0 ].rows = '135,*' ;
	parent.category_browse.location='$src?caller=$caller&p1=$p1&p2=$p2&perso_id=$perso_id&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&keep_tilde=$keep_tilde&parent=$parent&id2=$id2&id_thes=$id_thes&user_input=".rawurlencode(stripslashes($user_input))."&f_user_input=".rawurlencode(stripslashes($f_user_input))."&callback=".$callback."&infield=".$infield
		."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field."&id_thes_unique=".$id_thes_unique."&autoindex_class=autoindex_record';
</script>\n";
print $sel_footer;
