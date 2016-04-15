<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abstract.inc.php,v 1.41 2013-10-30 15:00:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur résumé/notes
require_once($class_path."/searcher.class.php");

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

$searcher_abstract = new searcher_abstract(stripslashes($user_query));
$notices = $searcher_abstract->get_result();
$nb_result_abstract = $searcher_abstract->get_nb_results();
$l_typdoc= implode(",",$searcher_abstract->get_typdocs());

//définition du formulaire
$form = "<form name=\"search_abstract\" action=\"./index.php?lvl=more_results\" method=\"post\">";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
	<input type=\"hidden\" name=\"mode\" value=\"abstract\"> 
	<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
	<input type=\"hidden\" name=\"count\" value=\"".$nb_result_abstract."\">
  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
  	</form>";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","abstract",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","notices",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['abstract'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_abstract,$search_result_affiliate_all);
	if($nb_result_abstract){
		$link = "<a href='#' onclick=\"document.search_abstract.action = './index.php?lvl=more_results&tab=catalog'; document.search_abstract.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_abstract",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if($nb_result_abstract) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		print "<div style=search_result id=\"titre\" name=\"titre\">";
		print "<strong>$msg[abstract]</strong> ".$nb_result_abstract." $msg[results] ";
		print "<a href=\"#\" onclick=\"document.forms['search_abstract'].submit(); return false;\">$msg[suite]&nbsp;<img src=./images/search.gif border=0 align=absmiddle></a>";
		print $form;
		print "</div>";
	}
}

if ($nb_result_abstract) {
	$_SESSION["level1"]["abstract"]["form"]=$form;
	$_SESSION["level1"]["abstract"]["count"]=$nb_result_abstract;	
}