<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.3 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur editeur

require_once("$class_path/authperso_authority.class.php");


print "	<div id=\"resultatrech\"><h3>$msg[resultat_recherche]</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";



// requête de recherche sur les titres uniformes
print pmb_bidi("<h3><span>$count ".$name." <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'");
if ($opac_search_other_function) {
	require_once($include_path."/".$opac_search_other_function);
	print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
}
print "</b>";
print activation_surlignage();
print "</h3></span>\n";

print "
		<div id=\"resultatrech_liste\">
		<ul>";

$found = pmb_mysql_query("select id_authperso_authority, ".$pert." from authperso_authorities $clause group by id_authperso_authority $tri $limiter", $dbh);

while(($res = pmb_mysql_fetch_object($found))) {
	$authority= new  authperso_authority($res->id_authperso_authority);
	print pmb_bidi("<li class='categ_colonne'><font class='notice_fort'><a href='index.php?lvl=authperso_see&id=".$res->id_authperso_authority."&from=search'>".$authority->get_isbd()."</a></font></li>\n");
}
print "</ul>";
print "
</div></div>";
print $catal_navbar;
print "</div>";

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab[$mode] = $count;
}