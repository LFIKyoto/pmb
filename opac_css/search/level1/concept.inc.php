<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept.inc.php,v 1.3 2015-03-27 10:30:21 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/searcher/opac_searcher_autorities_skos_concepts.class.php");

// premier niveau de recherche OPAC sur concept

// on regarde comment la saisie utilisateur se présente

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);
$searcher = new opac_searcher_autorities_skos_concepts($user_query);
$nb_result_concepts = $searcher->get_nb_results();

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['concepts'] = $nb_result_concepts;
}

//définition du formulaire
$form = "<div style=search_result><form name=\"search_concepts\" action=\"./index.php?lvl=more_results\" method=\"post\">";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"concept\">\n";
$form .= "<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">";
$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_concepts ."\">\n";
$form .= "</div>";	

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","concept",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['skos_view_concepts_concepts'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_concepts,$search_result_affiliate_all);
	if($nb_result_concepts){
		$link = "<a href='#' onclick=\"document.search_concepts.action = './index.php?lvl=more_results&tab=catalog'; document.search_concepts.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_concepts",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if ($nb_result_concepts ) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		print "<div style=search_result id=\"concept\" name=\"concept\">";
		print "<strong>".$msg['skos_view_concepts_concepts']."</strong> ".$nb_result_concepts." ".$msg['results']." ";
		print "<a href=\"#\" onclick=\"document.forms['search_concepts'].submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";

		print $form;
		print "</div>";
	}
}

if ($nb_result_concepts) {
	$_SESSION["level1"]["concept"]["form"]=$form;
	$_SESSION["level1"]["concept"]["count"]=$nb_result_concepts;	
}