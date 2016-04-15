<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.inc.php,v 1.46 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$url_base = "./autorites.php?categ=categories&sub=&id=0&parent=";

// inclusions diverses
include("$include_path/templates/category.tpl.php");
require_once("$class_path/category.class.php");
require_once("$class_path/analyse_query.class.php");
require_once("$class_path/thesaurus.class.php");

// search.inc : recherche des catégories en gestion d'autorités

//Récuperation de la liste des langues définies pour l'interface
$langages = new XMLlist("$include_path/messages/languages.xml", 1);
$langages->analyser();
$lg = $langages->table;


//affichage du selectionneur de thesaurus et du lien vers les thésaurus
$liste_thesaurus = thesaurus::getThesaurusList();
$sel_thesaurus = '';
$lien_thesaurus = '';

if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	$sel_thesaurus = "<select class='saisie-30em' id='id_thes' name='id_thes' ";
	$sel_thesaurus.= "onchange = \"document.location = '".$url_base."&id_thes='+document.getElementById('id_thes').value; \">" ;
	foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
		$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
		if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
		$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES, $charset)."</option>";
	}
	$sel_thesaurus.= "<option value=-1 ";
	if ($id_thes == -1) $sel_thesaurus.= "selected ";
	$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
	$sel_thesaurus.= "</select>&nbsp;";

	$lien_thesaurus = "<a href='./autorites.php?categ=categories&sub=thes'>".$msg[thes_lien]."</a>";

}	
$user_query=str_replace("<!-- sel_thesaurus -->",$sel_thesaurus,$user_query);
$user_query=str_replace("<!-- lien_thesaurus -->",$lien_thesaurus,$user_query);


//affichage du choix de langue pour la recherche
$sel_langue = '';
$sel_langue = "<div class='row'>";
$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' ";
if($lg_search == 1){
	$sel_langue .= " checked='checked' ";
}
$sel_langue.= "/>&nbsp;".htmlentities($msg['thes_sel_langue'],ENT_QUOTES, $charset);
$sel_langue.= "</div><br />";
$user_query=str_replace("<!-- sel_langue -->",$sel_langue,$user_query);
$user_query=str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);

//recuperation du thesaurus session 
if(!$id_thes) {
	$id_thes = thesaurus::getSessionThesaurusId();
} else {
	thesaurus::setSessionThesaurusId($id_thes);
}

if ($id_thes != -1) {
	$thes = new thesaurus($id_thes);
}


// nombre de références par pages
if ($nb_per_page_author != "") 
	$nb_per_page = $nb_per_page_author ;
	else $nb_per_page = 10;

// traitement de la saisie utilisateur
include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");


// $authors_list_tmpl : template pour la liste auteurs
$categ_list_tmpl = "
<br />
<br />
<div class='row'>
	<h3><! --!!nb_autorite_found!!-- >$msg[1320] !!cle!! </h3>
	</div>
	<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<table class='sortable'>
		!!list!!
	</table>
<div class='row'>
	!!nav_bar!!
	</div>
";


function list_categ($cle, $categ_list, $nav_bar) {
	global $categ_list_tmpl;
	$categ_list_tmpl = str_replace("!!cle!!", $cle, $categ_list_tmpl);
	$categ_list_tmpl = str_replace("!!list!!", $categ_list, $categ_list_tmpl);
	$categ_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $categ_list_tmpl);
	categ_browser::search_form();
	print pmb_bidi($categ_list_tmpl);
}

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page;

if(!$nbr_lignes){
	$requete = "SELECT SQL_CALC_FOUND_ROWS noeuds.id_noeud AS categ_id, ";
}else{
	$requete = "SELECT noeuds.id_noeud AS categ_id, ";
}
$requete.= "noeuds.num_renvoi_voir AS categ_see, ";
$requete.= "noeuds.num_thesaurus, ";

if($user_input){
	$aq=new analyse_query(stripslashes($user_input));
	if ($aq->error) {
		categ_browser::search_form($parent);
		error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
		exit;
	}
}


if(($lg_search == 1) || (($id_thes != -1) && ($thes->langue_defaut == $lang))){
	//On recherche dans toutes les langues ou dans celle par défaut du thésaurus
	
	// $user_input -> Permet de savoir si l'on a préciser une recheche
	//$id_thes -> Si l'on est ici c'est que l'on a qu'un thésaurus et la langue de l'interface est celle du thésaurus
	$requete.= "categories.langue AS langue, ";
	$requete.= "categories.libelle_categorie AS categ_libelle, ";
	$requete.= "categories.note_application AS categ_comment, ";
	$requete.= "categories.index_categorie AS index_categorie ";
	if($user_input){
		$members = $aq->get_query_members("categories", "libelle_categorie", "index_categorie", "num_noeud");
		
		$requete.= ", ".$members["select"]." AS pert "; 
	}
	
	$requete.= "FROM noeuds JOIN categories ON noeuds.id_noeud = categories.num_noeud ";
	if($lg_search != 1){
		$requete.=" AND categories.langue='".addslashes($lang)."' ";
	}
	$requete.= "WHERE 1 ";
	if($user_input){
		$requete.= "AND (".$members["where"].") ";
	}
	if($id_thes != -1) $requete.= "AND noeuds.num_thesaurus = '".$id_thes."' ";

}else{
	//J'ai qu'un thésaurus mais la langue du thésaurus est différente de l'interface
	$requete.= "IF (catlg.num_noeud IS NULL, catdef.langue, catlg.langue) as langue, ";
	$requete.= "IF (catlg.num_noeud IS NULL, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
	$requete.= "IF (catlg.num_noeud IS NULL, catdef.note_application, catlg.note_application) as categ_comment, ";
	$requete.= "IF (catlg.num_noeud IS NULL, catdef.index_categorie, catlg.index_categorie) as index_categorie ";
	
	if($user_input){
		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		
		$requete.= ", IF (catlg.num_noeud IS NULL, (".$members_catdef["select"]."), (".$members_catlg["select"].") ) AS pert ";
	}
	
	if(($id_thes != -1)){//Je n'ai qu'un thésaurus
		$requete.= "FROM noeuds JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = '".$thes->langue_defaut."' ";
		$requete.= "LEFT JOIN categories AS catlg ON catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
		$requete.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
	}else{
		//Plusieurs thésaurus
		$requete.= "FROM noeuds JOIN thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus ";
		$requete.= "JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = thesaurus.langue_defaut ";
		$requete.= "LEFT JOIN categories AS catlg on catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
		$requete.= "WHERE 1 "; 	
	}
	
	if($user_input){
		$requete.= "AND ( IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catdef["where"].") ) ";	
	}
	
}
$requete.= "ORDER BY ";
if($user_input){
	$requete.= "pert DESC,";
}
$requete.= " num_thesaurus, index_categorie ";
$requete.= "LIMIT ".$debut.",".$nb_per_page." ";

$res = pmb_mysql_query($requete, $dbh);
if(!$nbr_lignes){
	$qry = "SELECT FOUND_ROWS() AS NbRows";
	if($resnum = pmb_mysql_query($qry)){
		$nbr_lignes=pmb_mysql_result($resnum,0,0);
	}
}

if($nbr_lignes) {
	$categ_list_tmpl=str_replace( "<! --!!nb_autorite_found!!-- >",$nbr_lignes.' ',$categ_list_tmpl);	

	$parity=1;
	
	$categ_list .= "<tr>
		<th>".$msg[103]."</th>
		<th>".$msg["categ_commentaire"]."</th>
		<!--!!col_num_autorite!!-->
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$num_auth_present=false;
	$req="SELECT id_authority_source FROM authorities_sources WHERE authority_type='category' AND TRIM(authority_number) !='' LIMIT 1";
	$res_aut=pmb_mysql_query($req,$dbh);
	if($res_aut && pmb_mysql_num_rows($res_aut)){
		$categ_list=str_replace("<!--!!col_num_autorite!!-->","<th>".$msg["authorities_number"]."</th>",$categ_list);
		$num_auth_present=true;
	}
	
	while(($categ=pmb_mysql_fetch_object($res))) {
		
		$temp = new categories($categ->categ_id, $categ->langue);
		if ($id_thes == -1) {
			$thes = new thesaurus($categ->num_thesaurus);
			$display = '['.htmlentities($thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
		} else {
			$display = '';
		}
		if ($lg_search) $display.= '['.$lg[$categ->langue].'] '; else $display.= '';				
		if($categ->categ_see) {
			$temp = new categories($categ->categ_see, $categ->langue);
			$display.= $categ->categ_libelle." -&gt; <i>";
			if ($thesaurus_categories_show_only_last) {
				$display.= $temp->libelle_categorie;
			} else {
				$display.= categories::listAncestorNames($categ->categ_see, $categ->langue);
			} 
			$display.= "@</i>";
		} else {
			if ($thesaurus_categories_show_only_last) {
				$display.= $categ->categ_libelle;
			} else {
				$display.= categories::listAncestorNames($categ->categ_id, $categ->langue);
			} 			
		}	

		$acateg = new category($categ->categ_id);
		$notice_count = $acateg->notice_count(false);
		
		$categ_entry = $display ;
		$categ_comment = $categ->categ_comment;
		$link_categ = "./autorites.php?categ=categories&sub=categ_form&parent=0&id=".$categ->categ_id."&id_thes=".$categ->num_thesaurus."&user_input=".rawurlencode(stripslashes($user_input))."&nbr_lignes=".$nbr_lignes."&page=".$page."&nb_per_page=".$nb_per_page;
		
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		
		$parity += 1;
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
        $categ_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
        				<td valign='top' onmousedown=\"document.location='$link_categ';\">
						$categ_entry
						</td>
						<td valign='top' onmousedown=\"document.location='$link_categ';\">
						$categ_comment
						</td>";
		
		//Numéros d'autorite
		if($num_auth_present){
			$requete="SELECT authority_number,origin_authorities_name, origin_authorities_country FROM authorities_sources JOIN origin_authorities ON num_origin_authority=id_origin_authorities WHERE authority_type='category' AND num_authority='".$categ->categ_id."' AND TRIM(authority_number) !='' GROUP BY authority_number,origin_authorities_name,origin_authorities_country ORDER BY authority_favorite DESC, origin_authorities_name";
			$res_aut=pmb_mysql_query($requete,$dbh);
			if($res_aut && pmb_mysql_num_rows($res_aut)){
				$categ_list .= "<td>";
				$first=true;
				while ($aut = pmb_mysql_fetch_object($res_aut)) {
					if(!$first)$categ_list .=", ";
					$categ_list .=htmlentities($aut->authority_number,ENT_QUOTES,$charset);
					if($tmp=trim($aut->origin_authorities_name)){
						$categ_list .=htmlentities(" (".$aut->origin_authorities_name.")",ENT_QUOTES,$charset);
					}
					$first=false;
				}
				$categ_list .= "</td>";
			}else{
				$categ_list .= "<td>&nbsp;</td>";
			}
		}
		
		if($notice_count && $notice_count!=0)	
			$categ_list .= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$categ->categ_id'\">".$notice_count."</a></td>";
		else $categ_list .= "<td>&nbsp;</td>";
		$categ_list .= "</tr>";
			
	} // fin while
	

	pmb_mysql_free_result($res);


	//Création barre de navigation
	$url_base='./autorites.php?categ=categories&sub=search&id_thes='.$id_thes.'&user_input='.rawurlencode(stripslashes($user_input)).'&lg_search='.$lg_search;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
        else $nav_bar = "";

	
	// affichage du résultat
	list_categ(stripslashes($user_input), $categ_list, $nav_bar);

} else {
	// la requête n'a produit aucun résultat
	categ_browser::search_form($parent);
	error_message($msg[211], str_replace('!!categ_cle!!', stripslashes($user_input), $msg["categ_no_categ_found_with"]), 0, './autorites.php?categ=categories&sub=search');
}

