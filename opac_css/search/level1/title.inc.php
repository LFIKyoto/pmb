<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: title.inc.php,v 1.42 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur titre

// inclusion classe pour affichage notices (level 1)
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/classes/notice.class.php');
require_once($class_path."/searcher.class.php");

$search_title = new searcher_title(stripslashes($user_query));
$notices = $search_title->get_result();
$nb_result_titres = $search_title->get_nb_results();
$l_typdoc= implode(",",$search_title->get_typdocs());

//définition du formulaire
$form = "<form name=\"search_objects\" action=\"./index.php?lvl=more_results\" method=\"post\">";
if (function_exists("search_other_function_post_values")){
		$form .=search_other_function_post_values(); 
	}
$form .= "
  	<input type=\"hidden\" name=\"mode\" value=\"title\">
  	<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
  	<input type=\"hidden\" name=\"count\" value=\"".$nb_result_titres."\">
  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
  	</form>";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","title",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","notices",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['titles'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_titres,$search_result_affiliate_all);
	if($nb_result_titres){
		$link = "<a href='#' onclick=\"document.search_objects.action = './index.php?lvl=more_results&tab=catalog'; document.search_objects.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_objects",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if ($nb_result_titres) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		// (affichage sur une ligne cliquable, maybe...
		print '<strong>'.$msg['titles'].'</strong> '.$nb_result_titres.' '.$msg['results'].' ';
		// $found = pmb_mysql_query("select * from notices $clause $tri LIMIT $opac_search_results_first_level", $dbh);
		// si il y a d'autres résultats, je met le lien 'plus de résultats'
		// Le lien validant le formulaire est inséré avant le formulaire, cela évite les blancs à l'écran
		print "<a href=\"javascript:document.forms['search_objects'].submit()\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	  	print "<div style=search_result>$form</div>";			
	}
}

if ($nb_result_titres) {
	$_SESSION["level1"]["title"]["form"]=$form;
	$_SESSION["level1"]["title"]["count"]=$nb_result_titres;	
}