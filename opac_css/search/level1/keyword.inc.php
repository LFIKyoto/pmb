<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: keyword.inc.php,v 1.43 2013-10-30 15:00:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur mot-clé
require_once($class_path."/searcher.class.php");

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

$search_keywords = new searcher_keywords(stripslashes($user_query));
$notices = $search_keywords->get_result();
$nb_result_keywords = $search_keywords->get_nb_results();
$l_typdoc= implode(",",$search_keywords->get_typdocs());

$search_terms = $aq->get_positive_terms($aq->tree);
//On enlève le dernier terme car il s'agit de la recherche booléenne complète
unset($search_terms[count($search_terms)-1]);


$form = "<form name=\"search_keywords\" action=\"./index.php?lvl=more_results\" method=\"post\">";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"keyword\">
	<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
  	<input type=\"hidden\" name=\"count\" value=\"".$nb_result_keywords."\">
  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
  </form>";

if($opac_allow_affiliate_search){
	if ($auto_submit) {
		if($nb_result_keywords){
			print "<div style='search_result'>".$form."</div><script type=\"text/javascript\" >
				document.search_keywords.action = './index.php?lvl=more_results&tab=catalog';
				document.search_keywords.submit();
			</script>";
		}
	} else {
		$search_result_affiliate_all =  str_replace("!!mode!!","keywords",$search_result_affiliate_lvl1);
		$search_result_affiliate_all =  str_replace("!!search_type!!","notices",$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!label!!",$msg['keywords'],$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_keywords,$search_result_affiliate_all);
		if($nb_result_keywords){
			$link = "<a href='#' onclick=\"document.search_keywords.action = './index.php?lvl=more_results&tab=catalog'; document.search_keywords.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
		}else $link = "";
		$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!form_name!!","search_keywords",$search_result_affiliate_all);
		$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
		print $search_result_affiliate_all;
	}
}else{
	if ($nb_result_keywords) {
		if ($auto_submit) {
			print $form."<script type=\"text/javascript\" >document.forms['search_keywords'].submit();</script>";
		} else {
			$mots_cle_chaine = '';
			print "<div style=search_result id=\"titre\" name=\"titre\">";
			print "<strong>";
			if($opac_allow_tags_search)
				print $msg['tag'];
			else
				print $msg['keywords'];
			print "</strong> ".$nb_result_keywords."&nbsp;".$msg['results']."&nbsp;";
			print "<a href=\"javascript:document.forms['search_keywords'].submit()\">$msg[suite]&nbsp;<img src=./images/search.gif border=0 align=absmiddle></a>";
			print $form;
			print "</div>";
		}
	}
}

if ($nb_result_keywords) {
	$_SESSION["level1"]["keywords"]["form"]=$form;
	$_SESSION["level1"]["keywords"]["count"]=$nb_result_keywords;	
}