<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept.inc.php,v 1.3 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/searcher/opac_searcher_autorities_skos_concepts.class.php");
require_once($class_path."/skos/skos_concept.class.php");
// second niveau de recherche OPAC sur concept

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['concept'] = $count;
}

if($opac_allow_affiliate_search){
	print $search_result_affiliate_lvl2_head;
}else {
	print "	<div id=\"resultatrech\"><h3>$msg[resultat_recherche]</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	// requête de recherche sur les concepts
	print pmb_bidi("<h3><span>".$count." ".$msg['concepts_found']." <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'");
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
		print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
	}
	print "</b>";
	print activation_surlignage();
	print "</h3></span>\n";
	
		if(!$opac_allow_affiliate_search) print "
				</div>";
		print "
				<div id=\"resultatrech_liste\">
				<ul>";
	
		
	$searcher = new opac_searcher_autorities_skos_concepts($user_query);
	$concepts = $searcher->get_sorted_result();
	
	foreach($concepts as $concept){
		$concept = new skos_concept($concept);
		print pmb_bidi("<li class='categ_colonne'><font class='notice_fort'><a href='".str_replace("!!id!!", $concept->get_id(), $liens_opac['lien_rech_concept'])."&from=search'>".$concept->get_display_label()."</a></font></li>\n");
	}
	print "</ul>";
	print "
	</div></div>";
	if($opac_allow_affiliate_search) print $catal_navbar;
	else print "</div>";
}else{
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$as=new affiliate_search_concept($user_query,"authorities");
		print $as->getResults();
	}
	print "
	</div>
	<div class='row'>&nbsp;</div>";
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['concept_affiliate'] = $as->getTotalNbResults();
	}
}
