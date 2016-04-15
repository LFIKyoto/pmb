<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tous.inc.php,v 1.55 2015-04-01 07:23:04 arenou Exp $
// premier niveau de recherche OPAC sur tous

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// inclusion classe pour affichage tous (level 1)
require_once($base_path.'/includes/templates/tous.tpl.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/includes/notice_affichage.inc.php');
require_once($class_path."/searcher.class.php");

$search_all_fields = new searcher_all_fields(stripslashes($user_query),$map_emprises_query);
//$notices = $search_all_fields->get_result();
$nb_result = $search_all_fields->get_nb_results();
$l_typdoc= implode(",",$search_all_fields->get_typdocs());

//définition du formulaire
$form = "
	<form name=\"search_tous\" action=\"./index.php?lvl=more_results\" method=\"post\">";
	if (function_exists("search_other_function_post_values")){
		$form .=search_other_function_post_values(); 
	}
	if($map_emprises_query) {
		foreach($map_emprises_query as $map_emprise_query){
			$form .= "
		<input type=\"hidden\" name=\"map_emprises_query[]\" value=\"$map_emprise_query\">";
		}
	}
  	$form .= "
  		<input type=\"hidden\" name=\"mode\" value=\"tous\">
  		<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
  		<input type=\"hidden\" name=\"count\" value=\"".$nb_result."\">
  		<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
  		<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">";
  	if($opac_indexation_docnum_allfields) 
  		$form .= "<input type=\"hidden\" name=\"join\" value=\"".htmlentities($join,ENT_QUOTES,$charset)."\">";
  	$form .= "
	</form>";

if($opac_allow_affiliate_search){
	$libelle=($opac_indexation_docnum_allfields ? " [".$msg['docnum_search_with']."] " : '');
	$search_result_affiliate_all =  str_replace("!!mode!!","all",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","notices",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['tous'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result,$search_result_affiliate_all);
	if($nb_result){
		$link = "<a href='#' onclick=\"document.search_tous.action = './index.php?lvl=more_results&tab=catalog'; document.search_tous.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_tous",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
	if($opac_show_results_first_page && $nb_result>0) {
		print "<div id='res_first_page'>\n";
		if ($opac_notices_depliable) print $begin_result_liste;
		$nb=0;
		$recherche_ajax_mode=0;
		$notices = array();
		$notices = $search_all_fields->get_sorted_result("default",0,$opac_nb_results_first_page);
		
		for ($i =0 ; $i<$opac_nb_results_first_page;$i++){
			if($i>4)$recherche_ajax_mode=1;
			if($i==count($notices))break;
			print pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
		}
		print '</div>';	
	}
}else{
	if($nb_result){
		$libelle=($opac_indexation_docnum_allfields ? " [".$msg['docnum_search_with']."] " : '');
		if($opac_show_results_first_page && $nb_result > $opac_nb_results_first_page) {
			print "<strong>".$msg['tous'].$libelle."</strong> ".$opac_nb_results_first_page." ".$msg['notice_premiere']." ".$nb_result." ".$msg['results']." ";
			print "<a href=\"javascript:document.forms['search_tous'].submit()\">".$msg['notice_toute']."&nbsp;<img src=./images/search.gif border='0' align='absmiddle'/></a><br />";
		} else {
			print "<strong>".$msg['tous'].$libelle."</strong> ".$nb_result." ".$msg['results']." ";	
			print "<a href=\"javascript:document.forms['search_tous'].submit()\"> ".$msg['suite']."&nbsp;<img src=./images/search.gif border='0' align='absmiddle'/></a><br />";
		}  	
		if($opac_show_results_first_page) {
			print "<div id='res_first_page'>\n";
			if ($opac_notices_depliable) print $begin_result_liste;
			$nb=0;
			$recherche_ajax_mode=0;
			$notices = array();
			$notices = $search_all_fields->get_sorted_result("default",0,$opac_nb_results_first_page);
			for ($i =0 ; $i<$opac_nb_results_first_page;$i++){
				if($i>4)$recherche_ajax_mode=1;
				if($i==count($notices))break;
				print pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
			}
			print '</div>';	
		}
		$form = "<div style=search_result>$form</div>";
		print $form;
	}
}

if ($nb_result) {
	$_SESSION["level1"]["tous"]["form"]=$form;
	$_SESSION["level1"]["tous"]["count"]=$nb_result;	
}