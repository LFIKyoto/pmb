<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.1 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$nb_result=0;
$id_authperso = $row->id_authperso;
$members = array();
$clause = "";

$members=$aq->get_query_members("authperso_authorities","authperso_infos_global","authperso_index_infos_global","id_authperso_authority");
$clause.= " where ".$members["where"] ." and authperso_authority_authperso_num=".$id_authperso;
		
if ($opac_search_other_function) $add_notice=search_other_function_clause();
if ($typdoc || $add_notice) $clause = ', notices, notices_authperso '.$clause;
if ($typdoc) $clause.=" and notice_authperso_notice_num=notice_id and typdoc='".$typdoc."' ";
if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')';
		
$tri = 'order by pert desc, authperso_index_infos_global';
$pert=$members["select"]." as pert";
		
$auth_res = pmb_mysql_query("SELECT COUNT(distinct id_authperso_authority) FROM authperso_authorities $clause", $dbh);
$nb_result = pmb_mysql_result($auth_res, 0 , 0);
if ($nb_result) {
	$authority_name = authpersos::get_name($id_authperso);
	
	//définition du formulaire
	$form = "<div style=search_result><form name=\"search_authperso_".$id_authperso."\" action=\"./index.php?lvl=more_results\" method=\"post\">";
	$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
	if (function_exists("search_other_function_post_values")){
		$form .=search_other_function_post_values();
	}
	$form .= "<input type=\"hidden\" name=\"mode\" value=\"authperso_".$id_authperso."\">\n";
	$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
	$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result ."\">\n";
	$form .= "<input type=\"hidden\" name=\"name\" value=\"".htmlentities($authority_name,ENT_QUOTES,$charset)."\">\n";
	$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
	$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
	$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
	$form .= "</div>";
		
	$_SESSION["level1"]["authperso_".$id_authperso]["form"]=$form;
	$_SESSION["level1"]["authperso_".$id_authperso]["count"]=$nb_result;
	$_SESSION["level1"]["authperso_".$id_authperso]["name"]=$authority_name;
	
	
	print "<div style=search_result id=\"authperso_".$id_authperso."\" name=\"authperso\">";
	print "<strong>".htmlentities($authority_name,ENT_QUOTES,$charset)."</strong> ".$nb_result." $msg[results] ";
	print "<a href=\"#\" onclick=\"document.forms['search_authperso_".$id_authperso."'].submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";	
	print $form;
	print "</div>";
	
	$_SESSION["level1"]["authperso_".$id_authperso]["form"]=$form;
	$_SESSION["level1"]["authperso_".$id_authperso]["count"]=$nb_result;
	
}