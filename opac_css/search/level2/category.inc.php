<?php
// +----------------------------------------------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +----------------------------------------------------------------------------------------+
// $Id: category.inc.php,v 1.39 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur catégorie

require_once($class_path."/thesaurus.class.php");
//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['categories'] = $count;
}

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
	<h3><span><b>$count</b> $msg[categs_found] <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'";
	
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
		print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
	}
	
	print "</b></font>";
	print activation_surlignage();
	print "</h3></span>";
	
		if(!$opac_allow_affiliate_search) print "
				</div>";
		print "
				<div id=\"resultatrech_liste\">
				<ul>";
	
	$first_clause.= "catdef.libelle_categorie not like '~%' ";
	
	$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
	$members_catdef = $aq->get_query_members('catdef','catdef.libelle_categorie','catdef.index_categorie','catdef.num_noeud');
	$members_catlg = $aq->get_query_members('catlg','catlg.libelle_categorie','catlg.index_categorie','catlg.num_noeud');
	
	$list_thes = array();
	if ($id_thes == -1) { 
	//recherche dans tous les thesaurus
		$list_thes = thesaurus::getThesaurusList();
	} else {
	//recherche dans le thesaurus transmis
		$thes = new thesaurus($id_thes);
		$list_thes[$id_thes]=$thes->libelle_thesaurus;
	}
	
	$q = "drop table if exists catjoin ";
	$r = pmb_mysql_query($q, $dbh);
	
	$q = "create temporary table catjoin ENGINE=MyISAM as select ";
	foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
		$thes = new thesaurus($id_thesaurus);
		if(($lang==$thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false)){
			$q.= "noeuds.num_thesaurus, ";
			$q.= "noeuds.id_noeud as num_noeud, ";
			$q.= "catdef.note_application as note_application, ";
			$q.= "catdef.comment_public as comment_public, ";
			$q.= "noeuds.num_renvoi_voir, ";
			$q.= "catdef.libelle_categorie as libelle_categorie, ";
			$q.= "catdef.index_categorie as index_categorie, ";
			$q.= " ".$members_catdef['select']." as pert ";
			$q.= "from noeuds "; 
			$q.= "join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
			$q.= "where 1 ";
			$q.= "and noeuds.num_thesaurus = '".$thes->id_thesaurus."' ";
			$q.= "and ".$first_clause." ";
			$q.= "and ".$members_catdef['where']." ";
		}else{
			$q.= "noeuds.num_thesaurus, ";
			$q.= "noeuds.id_noeud as num_noeud, ";
			$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as note_application, ";
			$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public) as comment_public, ";
			$q.= "noeuds.num_renvoi_voir, ";
			$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as libelle_categorie, ";
			$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie) as index_categorie, ";
			$q.= "if (catlg.num_noeud is null, ".$members_catdef['select'].", ".$members_catlg['select'].") as pert ";
			$q.= "from noeuds "; 
			$q.= "join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
			$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$q.= "where 1 ";
			$q.= "and noeuds.num_thesaurus = '".$thes->id_thesaurus."' ";
			$q.= "and ".$first_clause." ";
			$q.= "and ( if (catlg.num_noeud is null, ".$members_catdef['where'].", ".$members_catlg['where'].") ) ";
		}
		$r = pmb_mysql_query($q, $dbh);
		$q = "INSERT INTO catjoin SELECT ";
	}
	
	$q = 'select distinct catjoin.num_noeud, catjoin.* from catjoin '.$clause.' ORDER BY pert desc, catjoin.index_categorie '.$limiter;
	$found = pmb_mysql_query($q, $dbh);
	while($mesCategories_trouvees = pmb_mysql_fetch_object($found)) {
		print "<li class='categ_colonne'>";
		if ($mesCategories_trouvees->num_renvoi_voir) {// Affichage des renvois_voir
			
			if (categories::exists($mesCategories_trouvees->num_renvoi_voir, $lang)) $lg=$lang;
			else {
				$thes = thesaurus::getByEltId($mesCategories_trouvees->num_noeud);
				$lg = $thes->langue_defaut;
			}
			$q =  "select * from noeuds, categories where num_noeud='".$mesCategories_trouvees->num_renvoi_voir."' and langue = '".$lg."' and noeuds.id_noeud = categories.num_noeud limit 1";
			$found_see = pmb_mysql_query($q, $dbh);
			
			$mesCategories = @pmb_mysql_fetch_object($found_see) ;
			print pmb_bidi("<b>".$mesCategories_trouvees->libelle_categorie."</b> ".$msg['term_show_see']." ") ;
		} else $mesCategories = $mesCategories_trouvees ;
		
			
		// Affichage de l'arborescence des renvois voir
		if ($mesCategories->num_parent) {
			$bar = categories::listAncestors($mesCategories->num_noeud, $lang);
			$bar = array_reverse($bar);
			if ($bar[3]) print pmb_bidi("<a href=./index.php?lvl=categ_see&id=".$bar[3]['num_noeud']."><img src='./images/folder.gif' border='0' align='middle'>...</a> > ");
			if ($bar[2]) print pmb_bidi("<a href=./index.php?lvl=categ_see&id=".$bar[2]['num_noeud']."><img src='./images/folder.gif' border='0' align='middle'>".$bar[2]['libelle_categorie'].'</a> > ');
			if ($bar[1]) print pmb_bidi("<a href=./index.php?lvl=categ_see&id=".$bar[1]['num_noeud']."><img src='./images/folder.gif' border='0' align='middle'>".$bar[1]['libelle_categorie'].'</a> > ');
		}
		print "<a href=./index.php?lvl=categ_see&id=".$mesCategories->num_noeud.">";
		
		
		// Si il y a présence d'un commentaire affichage du layer
		$result_com = categorie::zoom_categ($mesCategories_trouvees->num_noeud, $mesCategories_trouvees->note_application);
		
		if(category::has_notices($mesCategories_trouvees->num_noeud))
			print " <img src='$base_path/images/folder_search.gif' border=0 align='middle' />";
		else		
			print "<img src='./images/folder.gif' border='0' align='middle'>";
	
		print pmb_bidi("</a><a href=./index.php?lvl=categ_see&id=".$mesCategories->num_noeud.$result_com['java_com']. "&from=search>".$mesCategories->libelle_categorie.'</a>'.$result_com['zoom']);
	
			print "</li>";
		}
	
	print "</ul>";
	print "
	</div></div>";
	if($opac_allow_affiliate_search) print $catal_navbar;
	else print "</div>";
}else{
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$as=new affiliate_search_category($user_query,"authorities");
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
		$nb_results_tab['category_affiliate'] = $as->getTotalNbResults();
	}	
}
