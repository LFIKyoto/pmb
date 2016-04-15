<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme.inc.php,v 1.12 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

// on regarde comment la saisie utilisateur se présente
$clause = '';
$add_notice = '';

$aq=new analyse_query(stripslashes($user_query),0,0,1,1,$opac_stemming_active);
$members=$aq->get_query_members("titres_uniformes","tu_name","index_tu","tu_id");
$clause.= "where ".$members["where"];

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice) $clause.=',notices, notices_titres_uniformes '.$clause;

if ($typdoc) $clause.= " and ntu_num_notice=notice_id and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$tri = "order by pert desc, index_tu";
$pert=$members["select"]." as pert";

$tu = pmb_mysql_query("SELECT COUNT(distinct tu_id) FROM titres_uniformes $clause", $dbh);
$nb_result_titres_uniformes = pmb_mysql_result($tu, 0 , 0); 

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['titres_uniformes'] = $nb_result_titres_uniformes;
}


//définition du formulaire...
$form = "<form name=\"search_titres_uniformes\" action=\"./index.php?lvl=more_results\" method=\"post\">";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"titre_uniforme\">\n";
$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_titres_uniformes ."\">\n";
$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","titre_uniforme",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['titres_uniformes'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_titres_uniformes,$search_result_affiliate_all);
	if($nb_result_titres_uniformes){
		$link = "<a href='#' onclick=\"document.search_titres_uniformes.action = './index.php?lvl=more_results&tab=catalog'; document.search_titres_uniformes.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_titres_uniformes",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if ($nb_result_titres_uniformes ) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		$requete = "select tu_id,tu_name from titres_uniformes $clause $tri LIMIT $opac_search_results_first_level";
		// ??? ER : $found = pmb_mysql_query($requete, $dbh);
		print "<div style=search_result id=\"titre_uniforme\" name=\"titre_uniforme\">";
		print "<strong>".$msg["titres_uniformes"]."</strong> ".$nb_result_titres_uniformes." ".$msg["results"]." ";
		/* while($mesEditeurs = pmb_mysql_fetch_object($found)) {
			print "<li><a href='./index.php?lvl=publisher_see&id=".$mesEditeurs->ed_id."'>".$mesEditeurs->ed_name."</a></li>\n";
			} */
		
		// si il y a d'autres résultats, je met le lien 'plus de résultats'
		// Le lien validant le formulaire est inséré dans le code avant le formulaire, cela évite les blancs à l'écran
		print "<a href=\"javascript:document.forms['search_titres_uniformes'].submit()\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
		print $form;
		print "</div>";
	}
}

if ($nb_result_titres_uniformes) {
	$_SESSION["level1"]["titre_uniforme"]["form"]=$form;
	$_SESSION["level1"]["titre_uniforme"]["count"]=$nb_result_titres_uniformes;	
}
