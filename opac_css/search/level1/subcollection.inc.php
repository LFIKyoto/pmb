<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollection.inc.php,v 1.28 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

// on regarde comment la saisie utilisateur se présente
$clause = '';
$add_notice = '';

$aq=new analyse_query(stripslashes($user_query));
$members=$aq->get_query_members("sub_collections","sub_coll_name","index_sub_coll","sub_coll_id");
$clause.= "where ".$members["where"];

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice) $clause = ', notices '.$clause.' and subcoll_id=sub_coll_id ';

if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$tri = 'order by pert desc, index_sub_coll';
$pert=$members["select"]." as pert";

$subcollections = pmb_mysql_query("SELECT COUNT(sub_coll_id) FROM sub_collections $clause", $dbh);
$nb_result_subcollections = pmb_mysql_result($subcollections, 0 , 0); 

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['subcollections'] = $nb_result_subcollections;
}

//définition du formulaire
$form = "<div style=search_result><form name=\"search_sub_collection\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"souscollection\">\n";
$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_subcollections."\">\n";
$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","subcollection",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['subcollections'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_subcollections,$search_result_affiliate_all);
	if($nb_result_subcollections){
		$link = "<a href='#' onclick=\"document.search_sub_collection.action = './index.php?lvl=more_results&tab=catalog'; document.search_sub_collection.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_sub_collection",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
if ($nb_result_subcollections) {
	// tout bon, y'a du résultat, on lance le pataquès d'affichage
	$requete = "select sub_coll_id,sub_coll_name from sub_collections $clause $tri LIMIT $opac_search_results_first_level";
	// ??? ER : $found = pmb_mysql_query($requete, $dbh);
	print "<div style=search_result id=\"subcollection\" name=\"subcollection\">";
	print "<strong>$msg[subcollections]</strong> ".$nb_result_subcollections." $msg[results] ";
	print "<a href=\"javascript:document.forms['search_sub_collection'].submit()\">$msg[suite] <img src='./images/search.gif' border='0' align='absmiddle'/></a>\n";
	print $form;
	print "</div>";
}
}

if ($nb_result_subcollections) {
	$_SESSION["level1"]["subcollection"]["form"]=$form;
	$_SESSION["level1"]["subcollection"]["count"]=$nb_result_subcollections;	
}


