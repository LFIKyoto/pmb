<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_form.inc.php,v 1.39 2015-06-08 14:25:09 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// inclusions diverses
include_once("$include_path/templates/category.tpl.php");
require_once("$class_path/category.class.php");
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/noeuds.class.php");
require_once("$class_path/categories.class.php");
require_once("$class_path/XMLlist.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map/map_edition_controler.class.php");

if (noeuds::isRacine($id)) {
	error_form_message($msg['categ_forb']);
	exit();		
}

//recuperation du thesaurus session 
if(!$id_thes) {
	$id_thes = thesaurus::getSessionThesaurusId();
}
if($id_thes == '-1') $id_thes = $thesaurus_defaut;
thesaurus::setSessionThesaurusId($id_thes);

$thes = new thesaurus($id_thes);

//Récuperation de la liste des langues définies pour l'interface
$langages = new XMLlist("$include_path/messages/languages.xml", 1);
$langages->analyser();
$lg = $langages->table;

//Récuperation de la liste des langues définies pour les thésaurus
//autre que la langue par defaut du thesaurus
$thes_liste_trad = thesaurus::getTranslationsList();
$lg1 = array();
foreach($thes_liste_trad as $dummykey=>$item) {
	if ( ($item != $thes->langue_defaut) && ($lg[$item]!= '') )
		 $lg1[$item] = $lg[$item];
}


// dessin du form

$see_also=array();

if($id) {
	$title = $msg[318];
	$action = "./autorites.php?categ=categories&sub=update&id=$id&parent=$parent";
	$delete_button = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";
			
	$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' ";
	$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$id\"'>";	

	// on récupère les données de la catégorie
	$cat = new category($id);
	$c_form = '<p>'.$cat->catalog_form.'</p>';
	$p_value = $cat->parent_id;
	$p_libelle = $cat->parent_libelle;
	$v_value = $cat->voir_id;
	if($v_value) {
		$voir = new category($v_value);
		$v_libelle = $voir->catalog_form;
	}
	
	//renvois voir aussi
	$see_also=$cat->associated_terms;
	
	//Non utilisisable en indexation
	$not_use_in_indexation=$cat->not_use_in_indexation;
	
	//numero autorite
	$n=new noeuds($id);
	$num_aut=$n->autorite;
	
	//import bloqué
	$import_denied = $n->authority_import_denied;
	
	if (noeuds::isProtected($id)) {
		$aff_node_info=false;
	} else {
		$aff_node_info=true;
	}
	
} else {
	
	$action = "./autorites.php?categ=categories&sub=update&id=$id&parent=$parent";
	$delete_button = '';
	$title = $msg[319];
	$libelle = '';
	$c_form = '';
	if($parent) {
		$pr = new category($parent);
		$p_value = $pr->id;
		$p_libelle = $pr->catalog_form;
	} else {
		$p_value = 0;
		$p_libelle = '';
	}
	$v_value = 0;
	$v_libelle = '';
	$not_use_in_indexation = 0;
	$aff_node_info=true;
	
	$import_denied = 0;
}

if ($thesaurus_mode_pmb != 0) $title.= ' ('.htmlentities($thes->libelle_thesaurus, ENT_QUOTES, $charset).')';


//Traductions
$tab_traductions = array();

//Affichage des boutons de traduction
$bt_lib_trad = '';
$bt_cm_na_trad = '';
if ( count($lg1) > 0 ) {
	$bt_lib_trad = 	"<input type='button' class='bouton_small' value='".htmlentities(addslashes($msg[thes_traductions]), ENT_QUOTES, $charset)."' onclick=\"bascule_trad('lib_trad')\" />";
	$bt_cm_na_trad =  	"<input type='button' class='bouton_small' value='".htmlentities(addslashes($msg[thes_traductions]), ENT_QUOTES, $charset)."' onclick=\"bascule_trad('cm_na_trad')\" />";
}
$category_form = str_replace('<!-- bt_lib_trad -->', $bt_lib_trad, $category_form);
$category_form = str_replace('<!-- bt_cm_na_trad -->', $bt_cm_na_trad, $category_form);


//On lit d'abord dans la langue par défaut du thesaurus
if (categories::exists($id, $thes->langue_defaut)) {
	$c = new categories($id, $thes->langue_defaut);
	$libelle_categorie = $c->libelle_categorie;
	$note_application = $c->note_application;			
	$commentaire = $c->comment_public;
} else {
	$libelle_categorie = '';
	$note_application = '';				
	$comment_public = '';
}
$tab_traductions [$thes->langue_defaut][0] = $lg[$thes->langue_defaut];
$tab_traductions [$thes->langue_defaut][1] = $libelle_categorie;
$tab_traductions [$thes->langue_defaut][2] = $note_application;  
$tab_traductions [$thes->langue_defaut][3] = $commentaire;  

//Ensuite, on regarde si les categories existent pour les langues de traduction	des thesaurus
foreach($lg1 as $key=>$value){
	if (categories::exists($id, $key)) {
		$c = new categories($id, $key);
		$libelle_categorie = $c->libelle_categorie;
		$note_application = $c->note_application;			
		$commentaire = $c->comment_public;
	} else {
		$libelle_categorie = '';
		$note_application = '';				
		$commentaire = '';
	}
	$tab_traductions[$key][0] = $value;
	$tab_traductions[$key][1] = $libelle_categorie;
	$tab_traductions[$key][2] = $note_application;  
	$tab_traductions[$key][3] = $commentaire;
}

	
//categories langue par defaut thesaurus
$category_form = str_replace('!!lang_def_cle!!', htmlentities('['.$thes->langue_defaut.']', ENT_QUOTES, $charset), $category_form);
$category_form = str_replace('!!lang_def!!', htmlentities(' ('.$tab_traductions[$thes->langue_defaut][0].') ', ENT_QUOTES, $charset), $category_form);
$category_form = str_replace('!!lang_def_js!!', ' ('.$tab_traductions[$thes->langue_defaut][0].') ', $category_form);
$category_form = str_replace('!!lang_def_libelle!!', htmlentities($tab_traductions[$thes->langue_defaut][1], ENT_QUOTES, $charset), $category_form);

$label1 = "\t<div class='row'><label class='etiquette'>(";
$label2 = ") </label></div>\n";
$input1 = "\t<div class='row'><input type='text' class='saisie-80em' name='category_libelle[";
$input2 = "]' value=\"";
$input3 = "\" /></div>\n";


//categories langue interface (si dans la liste des langues pour les thesaurus)
if ( ($lang != $thes->langue_defaut) && ($lg1[$lang] != '') ) {
	$c_libelle_trad = $label1.htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset).$label2;
	$c_libelle_trad.= $input1.$lang.$input2.htmlentities($tab_traductions[$lang][1], ENT_QUOTES, $charset).$input3;
}

//categories autres langues 
foreach($tab_traductions as $key=>$value) {
	if ($key != $thes->langue_defaut && $key != $lang) {
		$c_libelle_trad.= $label1.htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset).$label2;
		$c_libelle_trad.= $input1.$key.$input2.htmlentities($tab_traductions[$key][1], ENT_QUOTES, $charset).$input3;
	}
}
$category_form = str_replace('!!c_libelle_trad!!', $c_libelle_trad, $category_form);

//Non utilisisable en indexation
if($not_use_in_indexation == 1){
	$not_use_checked = "checked='checked'";
}else{
	$not_use_checked = "";
}
$category_form = str_replace('!!not_use_in_indexation!!',$not_use_checked,$category_form);

	
//note d'application langue par defaut thesaurus
$category_form = str_replace('!!lang_def_na!!', htmlentities($tab_traductions[$thes->langue_defaut][2], ENT_QUOTES, $charset), $category_form);

//commentaire langue par defaut thesaurus
$category_form = str_replace('!!lang_def_cm!!', htmlentities($tab_traductions[$thes->langue_defaut][3], ENT_QUOTES, $charset), $category_form);

$label1 = "\t<div class='colonne2'><div class='row'><label class='etiquette'>(";
$label2 = ") </label></div>\n";
$input1 = "\t<div class='row'><textarea class='saisie-50em' id='category_na' name='category_na[";
$input2 = "]'  cols='40' rows='2' wrap='virtual'>";
$input3 = "</textarea></div></div>\n";

$label11 = "\t<div class='colonne_suite'><div class='row'><label class='etiquette'>(";
$label12 = ") </label></div>\n";
$input11 = "\t<div class='row'><textarea class='saisie-50em' id='category_cm' name='category_cm[";
$input12 = "]'  cols='40' rows='2' wrap='virtual'>";
$input13 = "</textarea></div></div>\n";


//note d'application et commentaire en langue de l'interface
if ($lang != $thes->langue_defaut) {
	$cm_na_trad = $label1.htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset).$label2;
	$cm_na_trad.= $input1.$lang.$input2.htmlentities($tab_traductions[$lang][2], ENT_QUOTES, $charset).$input3;
	$cm_na_trad.= $label11.htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset).$label12;
	$cm_na_trad.= $input11.$lang.$input12.htmlentities($tab_traductions[$lang][3], ENT_QUOTES, $charset).$input13;
}

//note d'application et commentaire autres langues 
foreach($tab_traductions as $key=>$value) {
	if ($key != $thes->langue_defaut && $key != $lang) {
		$cm_na_trad.= $label1.htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset).$label2;
		$cm_na_trad.= $input1.$key.$input2.htmlentities($tab_traductions[$key][2], ENT_QUOTES, $charset).$input3;
		$cm_na_trad.= $label11.htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset).$label12;
		$cm_na_trad.= $input11.$key.$input12.htmlentities($tab_traductions[$key][3], ENT_QUOTES, $charset).$input13;
	}
}
$category_form = str_replace('!!cm_na_trad!!', $cm_na_trad, $category_form);


$category_form = str_replace('!!id!!', $id, $category_form);
$category_form = str_replace('!!parent!!', $parent, $category_form);
$category_form = str_replace('!!action!!', $action, $category_form);
$category_form = str_replace('!!id_parent!!', $parent, $category_form);
$category_form = str_replace('!!form_title!!', $title, $category_form);
$category_form = str_replace('!!category_comment!!', htmlentities($commentaire,ENT_QUOTES, $charset), $category_form);


if ($aff_node_info) {
	
	$form_categ_parent = str_replace('!!parent_value!!', $p_value, $form_categ_parent);
	$form_categ_parent = str_replace('!!parent_libelle!!', htmlentities($p_libelle,ENT_QUOTES, $charset), $form_categ_parent);
	$category_form = str_replace('<!--categ_parent -->', $form_categ_parent, $category_form);

	$form_renvoivoir = str_replace('!!voir_value!!', $v_value, $form_renvoivoir);
	$form_renvoivoir = str_replace('!!voir_libelle!!', htmlentities($v_libelle,ENT_QUOTES, $charset), $form_renvoivoir);
	$category_form = str_replace('<!-- renvoivoir -->', $form_renvoivoir , $category_form);


	if (count($see_also)==0) {
		$max_categ=1;
		$categ0_id=0;
		$categ0_lib="";
		$categ0_rec="unchecked='unchecked'";  
	} else { 
		$max_categ=count($see_also);
		$csa=new category($see_also[0]['id']);
		$categ0_id=$see_also[0]['id'];
		$categ0_lib=$csa->catalog_form;
		if ( $see_also[0]['rec'] )$categ0_rec="checked='checked'"; else $categ0_rec="unchecked='unchecked'"; 
	}


	$see_also_form=$add_see_also;
	$see_also_form.="<input type='hidden' name='max_categ' value='$max_categ'/>\n";
	$categ0=str_replace("!!categ_libelle!!",$categ0_lib,$categ0);
	$categ0=str_replace("!!categ_id!!",$categ0_id,$categ0);
	$categ0=str_replace("!!icateg!!","0",$categ0);
	$categ0=str_replace("!!parent!!", $parent, $categ0);
	$categ0=str_replace("!!chk!!", $categ0_rec, $categ0);
	
	$see_also_form.=$categ0."\n";
	$see_also_form.="<div id='addcateg'>\n";
	for ($i=1; $i<count($see_also); $i++) {
		$csa=new category($see_also[$i]['id']);
		$categ_=$categ1;
		$categ_=str_replace("!!categ_libelle!!",$csa->catalog_form,$categ_);
		$categ_=str_replace("!!categ_id!!",$see_also[$i]['id'],$categ_);
		$categ_=str_replace("!!icateg!!",$i,$categ_);
		if ( $see_also[$i]['rec'] )$categ_rec="checked='checked'"; else $categ_rec="unchecked='unchecked'";
		$categ_=str_replace("!!chk!!", $categ_rec, $categ_);
		$see_also_form.=$categ_."\n";
	}
	$see_also_form.="</div>";

	$form_renvoivoiraussi=str_replace("!!renvoi_voir_aussi!!",$see_also_form,$form_renvoivoiraussi);
	$category_form=str_replace("<!-- renvoivoiraussi -->",$form_renvoivoiraussi,$category_form);

	//liaisons
	$has_link = false;
	$categ_child_content="";
	if (noeuds::hasChild($id)) {
		$has_link = true;
		$odd_even=1;
		if ($res = noeuds::listChilds($id, 0)) {
			$categ_child_content .= "
				<div class='row'>
	        		<label for='' class='etiquette'>$msg[categ_childs]</label>
	        	</div>
	        	<div class='row'>
	        		<table>";
			while ($row = pmb_mysql_fetch_object($res)) {
				$tcateg =  new category($row->id_noeud);
				if ($odd_even==0) {
					$categ_child_content .= "	<tr class='odd'>";
					$odd_even=1;
				} else if ($odd_even==1) {
					$categ_child_content .= "	<tr class='even'>";
					$odd_even=0;
				}
				$notice_count = $tcateg->notice_count(false);
			
				$categ_child_content .= "<td class='colonne80'>";
				if($tcateg->has_child) {
					$categ_child_content .= "<a href='./autorites.php?categ=categories&sub=&id=0&parent=".$tcateg->id."'>";
					$categ_child_content .= "<img src='./images/folderclosed.gif' hspace='3' border='0'></a>";
				} else {
					$categ_child_content .= "<img src='./images/doc.gif' hspace='3' border='0'>";
				}
				if ($tcateg->commentaire) {
					$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment.= htmlentities($tcateg->commentaire,ENT_QUOTES, $charset);
					$zoom_comment.="</div>";
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
				} else {
					$zoom_comment = "" ;
					$java_comment = "" ;
				}
				$categ_child_content .= "<a href='./autorites.php?categ=categories&sub=categ_form&parent=".$parent."&id=".$tcateg->id."' $java_comment >";
				$categ_child_content .= $tcateg->libelle;
				$categ_child_content .= '</a>';
				$categ_child_content .= $zoom_comment.'</td>';
				if($notice_count && $notice_count!=0)
					$categ_child_content .= "<td style='cursor: pointer; width:20%; text-align:center;' onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$tcateg->id'\">".$notice_count."</td>";
				else $categ_child_content .= "<td>&nbsp;</td>";
				$categ_child_content .='</tr>';
			}
			$categ_child_content .= "</table>
				</div>";
		}
	}
	$categ_renvoivoir_content="";
	if (noeuds::isTarget($id)){
		$has_link = true;
		$odd_even=1;
		if ($res = noeuds::listTargets($id)) {
			$categ_renvoivoir_content .= "
				<div class='row'>
	        		<label for='' class='etiquette'>$msg[categ_renvoivoir]</label>
	        	</div>
	        	<div class='row'>
	        		<table>";
			while ($row = pmb_mysql_fetch_object($res)) {
				$tcateg =  new category($row->id_noeud);
				$categ_renvoivoir_content .= "	<tr class='even'>";
				if ($odd_even==0) {
					$categ_renvoivoir_content .= "	<tr class='odd'>";
					$odd_even=1;
				} else if ($odd_even==1) {
					$categ_renvoivoir_content .= "	<tr class='even'>";
					$odd_even=0;
				}
				$notice_count = $tcateg->notice_count(false);
			
				$categ_renvoivoir_content .= "<td class='colonne80'>";
				if($tcateg->has_child) {
					$categ_renvoivoir_content .= "<a href='./autorites.php?categ=categories&sub=&id=0&parent=".$tcateg->id."'>";
					$categ_renvoivoir_content .= "<img src='./images/folderclosed.gif' hspace='3' border='0'></a>";
				} else {
					$categ_renvoivoir_content .= "<img src='./images/doc.gif' hspace='3' border='0'>";
				}
				if ($tcateg->commentaire) {
					$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment.= htmlentities($tcateg->commentaire,ENT_QUOTES, $charset);
					$zoom_comment.="</div>";
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
				} else {
					$zoom_comment = "" ;
					$java_comment = "" ;
				}
				$categ_renvoivoir_content .= "<a href='./autorites.php?categ=categories&sub=categ_form&parent=".$parent."&id=".$tcateg->id."' $java_comment >";
				$categ_renvoivoir_content .= $tcateg->libelle;
				$categ_renvoivoir_content .= '</a>';
				$categ_renvoivoir_content .= $zoom_comment.'</td>';
				if($notice_count && $notice_count!=0)
					$categ_renvoivoir_content .= "<td style='cursor: pointer; width:20%; text-align:center;' onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$tcateg->id'\">".$notice_count."</td>";
				else $categ_renvoivoir_content .= "<td>&nbsp;</td>";
				$categ_renvoivoir_content .='</tr>';
			}
			$categ_renvoivoir_content .= "</table>
				</div>";
		}
	}
	$categ_renvoivoiraussi_content="";
	//Voir aussi
	$requete="SELECT distinct num_noeud_orig AS id_noeud FROM voir_aussi WHERE num_noeud_dest='".$id."'";
	$res=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($res)) {
		$has_link = true;
		$odd_even=1;
		$categ_renvoivoiraussi_content .= "
			<div class='row'>
        		<label for='' class='etiquette'>$msg[categ_renvoivoiraussi]</label>
        	</div>
        	<div class='row'>
        		<table>";
		while ($row = pmb_mysql_fetch_object($res)) {
			$tcateg =  new category($row->id_noeud);
			$categ_renvoivoiraussi_content .= "	<tr class='even'>";
			if ($odd_even==0) {
				$categ_renvoivoiraussi_content .= "	<tr class='odd'>";
				$odd_even=1;
			} else if ($odd_even==1) {
				$categ_renvoivoiraussi_content .= "	<tr class='even'>";
				$odd_even=0;
			}
			$notice_count = $tcateg->notice_count(false);
		
			$categ_renvoivoiraussi_content .= "<td class='colonne80'>";
			if($tcateg->has_child) {
				$categ_renvoivoiraussi_content .= "<a href='./autorites.php?categ=categories&sub=&id=0&parent=".$tcateg->id."'>";
				$categ_renvoivoiraussi_content .= "<img src='./images/folderclosed.gif' hspace='3' border='0'></a>";
			} else {
				$categ_renvoivoiraussi_content .= "<img src='./images/doc.gif' hspace='3' border='0'>";
			}
			if ($tcateg->commentaire) {
				$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
				$zoom_comment.= htmlentities($tcateg->commentaire,ENT_QUOTES, $charset);
				$zoom_comment.="</div>";
				$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
			} else {
				$zoom_comment = "" ;
				$java_comment = "" ;
			}
			$categ_renvoivoiraussi_content .= "<a href='./autorites.php?categ=categories&sub=categ_form&parent=".$parent."&id=".$tcateg->id."' $java_comment >";
			$categ_renvoivoiraussi_content .= $tcateg->libelle;
			$categ_renvoivoiraussi_content .= '</a>';
			$categ_renvoivoiraussi_content .= $zoom_comment.'</td>';
			if($notice_count && $notice_count!=0)
				$categ_renvoivoiraussi_content .= "<td style='cursor: pointer; width:20%; text-align:center;' onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$tcateg->id'\">".$notice_count."</td>";
			else $categ_renvoivoiraussi_content .= "<td>&nbsp;</td>";
			$categ_renvoivoiraussi_content .='</tr>';
		}
		$categ_renvoivoiraussi_content .= "</table>
			</div>";
	}
	
	if ($has_link) {
	    $liaison_tpl=str_replace("<!-- categ_child -->",$categ_child_content,$liaison_tpl);
	    $liaison_tpl=str_replace("<!-- categ_renvoivoir -->",$categ_renvoivoir_content,$liaison_tpl);
	    $liaison_tpl=str_replace("<!-- categ_renvoivoiraussi -->",$categ_renvoivoiraussi_content,$liaison_tpl);
		$category_form=str_replace("<!-- liaison -->",$liaison_tpl,$category_form);
    } else {
    	$category_form=str_replace("<!-- liaison -->","",$category_form);
    }
    
	//Numéro d'autorité
	$form_num_aut=str_replace("!!num_aut!!",$num_aut,$form_num_aut);
	$category_form=str_replace("<!-- numero_autorite -->",$form_num_aut,$category_form);
	
	// Indexation concepts
	if ($id) $id_categ = $id;
	else $id_categ = 0;
	
	if($thesaurus_concepts_active == 1 ){
		$index_concept = new index_concept($id, TYPE_CATEGORY);
		$category_form = str_replace('!!concept_form!!', $index_concept->get_form('categ_form'), $category_form);
	}else{
		$category_form = str_replace('!!concept_form!!', "", $category_form);
	}
	if ($id) {
		// Impression de la branche du thésaurus
		$lien_impression_thesaurus="<a href='#' onClick=\"openPopUp('./print_thesaurus.php?current_print=2&action=print_prepare&aff_num_thesaurus=".$id_thes."&id_noeud_origine=$id','print', 500, 600, -2, -2, 'scrollbars=yes,menubar=0,resizable=yes'); return false;\">".$msg[print_branche]."</a>";
		$category_form=str_replace("<!-- imprimer_thesaurus -->",$lien_impression_thesaurus,$category_form);
	}
	
	//Remplacement
	$button_remplace = "<input type='button' class='bouton' value='$msg[158]' ";
	$button_remplace .= "onclick='unload_off();document.location=\"./autorites.php?categ=categories&sub=categ_replace&id=$id&parent=$parent\"'/>";
	$category_form = str_replace("<!-- remplace_categ -->", $button_remplace, $category_form);
	
	//Suppression
	$category_form = str_replace('<!-- delete_button -->', $delete_button, $category_form);
	
} else {
	$category_form=str_replace("<!-- numero_autorite -->",$num_aut,$category_form);
	$category_form = str_replace('!!concept_form!!', "", $category_form);
}
if($import_denied == 1){
	$import_denied_checked = "checked='checked'";
}else{
	$import_denied_checked = "";
}
$category_form = str_replace('!!authority_import_denied!!',$import_denied_checked,$category_form);

require_once("$class_path/aut_link.class.php");
$aut_link= new aut_link(AUT_TABLE_CATEG,$id);
$category_form = str_replace('<!-- aut_link -->', $aut_link->get_form('categ_form') , $category_form);

global $pmb_map_activate;
if($pmb_map_activate){
	$map_edition=new map_edition_controler(AUT_TABLE_CATEG,$id);
	$map_form=$map_edition->get_form();
	$category_form = str_replace('<!-- map -->', $map_form , $category_form);
}

$aut_pperso= new aut_pperso("categ",$id);
$category_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $category_form);

$category_form = str_replace('!!voir_notices!!', $button_voir, $category_form);

if($pmb_type_audit && $id)
	$bouton_audit= "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=".AUDIT_CATEG."&object_id=".$id."', 'audit_popup', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />&nbsp;";	
$category_form = str_replace('!!audit_bt!!', $bouton_audit, $category_form);

$category_form = str_replace('!!user_input!!', htmlentities($user_input,ENT_QUOTES, $charset), $category_form);
$category_form = str_replace('!!nbr_lignes!!', $nbr_lignes, $category_form);
$category_form = str_replace('!!page!!', $page, $category_form);
$category_form = str_replace('!!nb_per_page!!', $nb_per_page, $category_form);

print $category_form;
