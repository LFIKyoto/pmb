<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author.inc.php,v 1.29 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur auteur

if($opac_allow_affiliate_search){
	print $search_result_affiliate_lvl2_head;
}else {
	print "	<div id=\"resultatrech\"><h3>$msg[resultat_recherche]</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

switch($author_type) {
	case '70':
		$titre=$msg["authors_found"];
		//Enregistrement des stats
		if($pmb_logs_activate){
			global $nb_results_tab;
			$nb_results_tab['physiques'] = $count;
		}
	break;
	case '71':
		$titre=$msg["collectivites_found"];
		//Enregistrement des stats
		if($pmb_logs_activate){
			global $nb_results_tab;
			$nb_results_tab['collectivites'] = $count;
		}
	break;
	case '72':
		$titre=$msg["congres_found"];
		//Enregistrement des stats
		if($pmb_logs_activate){
			global $nb_results_tab;
			$nb_results_tab['congres'] = $count;
		}
	break;	
	default:
		$titre=$msg["authors_found"];
		//Enregistrement des stats
		if($pmb_logs_activate){
			global $nb_results_tab;
			$nb_results_tab['physiques'] = $count;
		}
	break;				
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	print pmb_bidi("	<h3><span><b>$count</b> $titre <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'");
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
		print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
	}
	print "</b></span>";
	print activation_surlignage()."</h3>";
		if(!$opac_allow_affiliate_search) print "
			</div>";
	print "
			<div id=\"resultatrech_liste\">
			<ul>";
	if($type)	$restrict_type=" and author_type='$type' ";
	
	$found = pmb_mysql_query("select author_id, ".$pert.",author_type, author_name, author_rejete,author_see from authors $clause $restrict_type group by author_id $tri $limiter", $dbh);
	while(($mesAuteurs = pmb_mysql_fetch_object($found))) {
		$psNom="";
		if ($mesAuteurs->author_see){
			$pseud = pmb_mysql_query("select author_name, author_rejete from authors where author_id='".$mesAuteurs->author_see."'", $dbh);
			$psAut = pmb_mysql_fetch_object($pseud);
			$psNom = "(".$msg['see'].": ".$psAut->author_name.",".$psAut->author_rejete.")";
		}
		if($mesAuteurs->author_type == 71 || $mesAuteurs->author_type == 72) {
			//C'est un congres
			$congres=new auteur($mesAuteurs->author_id);
			$auteur_isbd=$congres->isbd_entry;	
			$aff_type="";
			if($mesAuteurs->author_type == 72) $aff_type=" / ".$msg["congres_libelle"];
			print pmb_bidi("<li class='categ_colonne'><font class='notice_fort'><a href='index.php?lvl=author_see&id=".$mesAuteurs->author_id."&from=search' title='".$congres->info_bulle."'>".$auteur_isbd." ".$psNom.$aff_type."</a></font></li>\n");
		} else {
			print pmb_bidi("<li class='categ_colonne'><font class='notice_fort'><a href='index.php?lvl=author_see&id=".$mesAuteurs->author_id."&from=search' >".$mesAuteurs->author_name." ".$mesAuteurs->author_rejete." ".$psNom."</a></font></li>\n");
		}
	}	
	print "</ul>";
	print "
	</div></div>";
	if($opac_allow_affiliate_search) print $catal_navbar;
	else print "</div>";
}else{
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$as=new affiliate_search_author($user_query,"authorities");
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
		foreach($as->getNbResults() as $type => $nb){
			switch($type){
				case "authors":
					$nb_results_tab['physiques'] = $nb;
					break;
				case "coll":
					$nb_results_tab['collectivites'] = $nb;
					break;
				case "congres":
					$nb_results_tab['congres'] = $nb;
					break;
			}
		}
	}
}
