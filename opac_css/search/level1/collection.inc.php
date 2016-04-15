<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collection.inc.php,v 1.30 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur collections
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

// on regarde comment la saisie utilisateur se présente
$clause = '';
$add_notice = '';

$aq=new analyse_query(stripslashes($user_query));
$members=$aq->get_query_members("collections","collection_name","index_coll","collection_id");
$clause.= 'where '.$members["where"];

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice) $clause = ',notices '.$clause.' and coll_id=collection_id ';

if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$tri = 'order by pert desc, index_coll';
$pert=$members["select"]." as pert";

$collections = pmb_mysql_query("SELECT COUNT(distinct collection_id) FROM collections $clause", $dbh);
$nb_result_collections = pmb_mysql_result($collections, 0 , 0);

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['collections'] = $nb_result_collections;
}

//définition du formulaire...
$form = "<form name=\"search_collection\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"collection\">";
$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_collections."\">\n";
$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
$form .= "</form>";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","collection",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['collections'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_collections,$search_result_affiliate_all);
	if($nb_result_collections){
		$link = "<a href='#' onclick=\"document.search_collection.action = './index.php?lvl=more_results&tab=catalog'; document.search_collection.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_collection",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if($nb_result_collections) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		$requete = "select collection_id,collection_name from collections $clause $tri LIMIT $opac_search_results_first_level";
		// ??? ER : $found = pmb_mysql_query($requete, $dbh);
		print "<div style=search_result id=\"collection\" name=\"collection\">";
		print "<strong>$msg[collections]</strong> ".$nb_result_collections." $msg[results] ";
		print "<a href=\"#\" onclick=\"document.forms['search_collection'].submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a><br />";
		print $form;
		print "</div>";
	}
}

if ($nb_result_collections) {
	$_SESSION["level1"]["collection"]["form"]=$form;
	$_SESSION["level1"]["collection"]["count"]=$nb_result_collections;	
}