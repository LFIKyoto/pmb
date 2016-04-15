<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collection.inc.php,v 1.24 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur collections

if($opac_allow_affiliate_search){
	print $search_result_affiliate_lvl2_head;
}else {
	print "	<div id=\"resultatrech\"><h3>$msg[resultat_recherche]</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	print "
			<h3><span><b>$count</b> $msg[collections_found] <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'";
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
		print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
	}
	print "</b>
		   ";
	print activation_surlignage();
	print "</h3></span>\n";
	
	$found = pmb_mysql_query("select collection_id, ".$pert.",collection_name from collections $clause group by collection_id $tri $limiter", $dbh);
	
	if(!$opac_allow_affiliate_search) print "
			</div>";
	print "
			<div id=\"resultatrech_liste\">
			<ul>";
	while($mesCollections = pmb_mysql_fetch_object($found)) {
		print pmb_bidi("<li class='categ_colonne'><font class='notice_fort'><a href='index.php?lvl=coll_see&id=".$mesCollections->collection_id."&from=search'>".$mesCollections->collection_name."</a></font></li>\n");
		}
	print "</ul>";
	print "
	</div></div>";
	if($opac_allow_affiliate_search) print $catal_navbar;
	else print "</div>";
}else{
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$as=new affiliate_search_collection($user_query,"authorities");
		//un peu crade, mais dans l'immédiat ca fait ce qu'on lui demande...
		$as->filter = $author_type;
		print $as->getResults();
	}
	print "
	</div>
	<div class='row'>&nbsp;</div>";
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['collection_affiliate'] = $as->getTotalNbResults();
	}
}
//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['collections'] = $count;
}