<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.30 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur indexations decimales

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

// contrôle du nombre de résultats à afficher en premier niveau (6 par défaut)
if(!$opac_search_results_first_level) $opac_search_results_first_level=6;

// on regarde comment la saisie utilisateur se présente
$clause = '';
$add_notice = '';

$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
$members=$aq->get_query_members("indexint","concat(indexint_name,' ',indexint_comment)","index_indexint","indexint_id");
$clause.= "where ".$members["where"];

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice) $clause = ', notices '.$clause.' and indexint=indexint_id ';

if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$tri = 'order by pert desc, index_indexint';
$pert=$members["select"]." as pert";

$indexint = pmb_mysql_query("SELECT COUNT(distinct indexint_id) FROM indexint $clause", $dbh);
$nb_result_indexint = pmb_mysql_result($indexint, 0 , 0);

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['indexint'] = $nb_result_indexint;
}

//définition du formulaire
$form = "<form name=\"search_indexint\" action=\"./index.php?lvl=more_results\" method=\"post\">";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"indexint\">\n";
$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_indexint."\">\n";
$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","indexint",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['indexint'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_indexint,$search_result_affiliate_all);
	if($nb_result_indexint){
		$link = "<a href='#' onclick=\"document.search_indexint.action = './index.php?lvl=more_results&tab=catalog'; document.search_indexint.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_indexint",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if($nb_result_indexint) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		print "<div style=search_result id=\"collection\" name=\"collection\">";
		print "<strong>$msg[indexint]</strong> ".$nb_result_indexint." $msg[results] ";
		print "<a href=\"javascript:document.forms['search_indexint'].submit()\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
		print $form;
		print "</div>";
	}
}

if ($nb_result_indexint) {
	$_SESSION["level1"]["indexint"]["form"]=$form;
	$_SESSION["level1"]["indexint"]["count"]=$nb_result_indexint;	
}