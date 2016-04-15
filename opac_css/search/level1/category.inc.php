<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.inc.php,v 1.51 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur catégorie

// on regarde comment la saisie utilisateur se présente

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);
require_once($class_path."/thesaurus.class.php");
$first_clause.= "categories.libelle_categorie not like '~%' ";

$q = 'drop table if exists catjoin ';
$r = pmb_mysql_query($q, $dbh);
$q = 'create  temporary table catjoin ( ';
$q.= "num_thesaurus int(3) unsigned not null default '0', ";
$q.= "num_noeud int(9) unsigned not null default '0', ";
$q.= 'key (num_noeud,num_thesaurus) ';
$q.= ") ENGINE=MyISAM ";
$r = pmb_mysql_query($q, $dbh);



$list_thes = array();
if ($opac_thesaurus) { 
//mode multithesaurus
	$list_thes = thesaurus::getThesaurusList();
	$id_thes_for_link=-1;
} else {
//mode monothesaurus
	$thes = new thesaurus($opac_thesaurus_defaut);
	$list_thes[$opac_thesaurus_defaut]=$thes->libelle_thesaurus;
	$id_thes_for_link=$opac_thesaurus_defaut;
}

$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
$members_catdef = $aq->get_query_members('catdef','catdef.libelle_categorie','catdef.index_categorie','catdef.num_noeud');
$members_catlg = $aq->get_query_members('catlg','catlg.libelle_categorie','catlg.index_categorie','catlg.num_noeud');

foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
	$thes = new thesaurus($id_thesaurus);
	$q="INSERT INTO catjoin SELECT noeuds.num_thesaurus, noeuds.id_noeud FROM ";
	if(($lang==$thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false)){
		$q.="noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND  catdef.langue = '".$thes->langue_defaut."'";
		//$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND not_use_in_indexation='0' AND catdef.libelle_categorie not like '~%' and ".$members_catdef["where"];
		$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND catdef.libelle_categorie not like '~%' and ".$members_catdef["where"];
	}else{
		$q.="noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue='".$thes->langue_defaut."' LEFT JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."'";
		//$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND not_use_in_indexation='0' AND if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") AND if(catlg.num_noeud is null,catdef.libelle_categorie not like '~%',catlg.libelle_categorie not like '~%')";
		$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") AND if(catlg.num_noeud is null,catdef.libelle_categorie not like '~%',catlg.libelle_categorie not like '~%')";
	}
	$r = pmb_mysql_query($q, $dbh);
}

$clause = '';
$add_notice = '';

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice){
	$clause.= ' JOIN notices_categories ON notices_categories.num_noeud=catjoin.num_noeud JOIN notices ON notices_categories.notcateg_notice=notices.notice_id WHERE 1 ';
}else{
	$clause.= ' WHERE 1 ';
}

if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$q = 'select count(distinct catjoin.num_noeud) from catjoin '.$clause;

$r=pmb_mysql_query($q);
$nb_result_categories = pmb_mysql_result($r, 0 , 0);

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['categories'] = $nb_result_categories;
}

$form = "<form name=\"search_categorie\" action=\"./index.php?lvl=more_results\" method=\"post\">";
$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
$form .= "<input type=\"hidden\" name=\"mode\" value=\"categorie\">\n";
$form .= "<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".$nb_result_categories."\">\n";
$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
$form .= "<input type=\"hidden\" id=\"id_thes2\" name=\"id_thes\" value=\"".$id_thes_for_link."\">\n";
$form .= "</form>\n";

$form .= "
			<script type='text/javascript' >\n

				function submitSearch_CategorieForm(id, nb)
				{
					document.getElementById('id_thes2').value = id;
					document.getElementById('count').value = nb;
					document.forms['search_categorie'].submit();  
				}
			</script>\n";

if($opac_allow_affiliate_search){
	$search_result_affiliate_all =  str_replace("!!mode!!","category",$search_result_affiliate_lvl1);
	$search_result_affiliate_all =  str_replace("!!search_type!!","authorities",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!label!!",$msg['categories'],$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!nb_result!!",$nb_result_categories,$search_result_affiliate_all);
	if($nb_result_categories){
		$link =  "<a href=\"#\" onclick=\"document.search_categorie.action = './index.php?lvl=more_results&tab=catalog';document.forms.search_categorie.id_thes.value='".$id_thes_for_link."';document.forms.search_categorie.count.value='".$nb_result_categories."';document.forms.search_categorie.clause.value='".htmlentities(addslashes($clause),ENT_QUOTES,$charset)."';document.search_categorie.submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
		if ($opac_thesaurus) {	//mode multithesaurus dans l'opac
			$nb_thes=0;
			foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
				$q = 'select count(distinct catjoin.num_noeud) from catjoin '.$clause;
				$q.= " and catjoin.num_thesaurus = '".$id_thesaurus."' ";
				$clause_link=$clause." and catjoin.num_thesaurus = '".$id_thesaurus."' ";
				$r = pmb_mysql_query($q, $dbh);
				$nb = pmb_mysql_result($r, 0, 0);
				if ($nb) {
					$nb_thes++;
					if($nb_thes==1) $link .= '<blockquote>';
					$link .= htmlentities($msg['thes_libelle'],ENT_QUOTES, $charset).' '.htmlentities($libelle_thesaurus,ENT_QUOTES, $charset).'&nbsp;'.$nb.' '.htmlentities($msg[results],ENT_QUOTES, $charset);
					$link .= "<a href=\"#\" onclick=\"document.search_categorie.action = './index.php?lvl=more_results&tab=catalog';document.forms.search_categorie.id_thes.value='".$id_thesaurus."';document.forms.search_categorie.count.value='".$nb."';document.forms.search_categorie.clause.value='".htmlentities(addslashes($clause_link),ENT_QUOTES,$charset)."';document.search_categorie.submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
					$link .= '<br />';
				}
			}
			if($nb_thes)$link .=  ' </blockquote>';	
		}
	}else $link = "";
	$search_result_affiliate_all =  str_replace("!!link!!",$link,$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query)))),$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form_name!!","search_categorie",$search_result_affiliate_all);
	$search_result_affiliate_all =  str_replace("!!form!!",$form,$search_result_affiliate_all);
	print $search_result_affiliate_all;
}else{
	if($nb_result_categories) {
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		print "<div style=search_result id=\"categorie\" name=\"categorie\">";
		print "<strong>$msg[categories]</strong> ".$nb_result_categories." $msg[results] ";
		print "<a href=\"#\" onclick=\"document.forms.search_categorie.count.value='".$nb_result_categories."';document.forms.search_categorie.clause.value='".htmlentities(addslashes($clause),ENT_QUOTES,$charset)."';submitSearch_CategorieForm('".$id_thes_for_link."','".$nb_result_categories."'); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
	
		if ($opac_thesaurus) {	//mode multithesaurus dans l'opac
			$nb_thes=0;
			foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
				$q = 'select count(distinct catjoin.num_noeud) from catjoin '.$clause;
				$q.= " and catjoin.num_thesaurus = '".$id_thesaurus."' ";
				$clause_link=$clause." and catjoin.num_thesaurus = '".$id_thesaurus."' ";
				$r = pmb_mysql_query($q, $dbh);
				$nb = pmb_mysql_result($r, 0, 0);
				if ($nb) {
					$nb_thes++;
					if($nb_thes==1)print '<blockquote>';
					print htmlentities($msg['thes_libelle'],ENT_QUOTES, $charset).' '.htmlentities($libelle_thesaurus,ENT_QUOTES, $charset).'&nbsp;'.$nb.' '.htmlentities($msg[results],ENT_QUOTES, $charset);
					print "<a href=\"#\" onclick=\"document.forms.search_categorie.count.value='".$nb."';document.forms.search_categorie.clause.value='".htmlentities(addslashes($clause_link),ENT_QUOTES,$charset)."';submitSearch_CategorieForm('".$id_thesaurus."','".$nb."'); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
					print '<br />';
				}
			}
			if($nb_thes)print ' </blockquote>';	
		}
		// Le lien validant le formulaire est inséré dans le code avant le formulaire, cela évite les blancs à l'écran
		$form = "<div style='search_result'>".$form."</div>\n";
		print $form;
		print "</div>";
	}
}

if ($nb_result_categories) {
	$_SESSION["level1"]["category"]["form"]=$form;
	$_SESSION["level1"]["category"]["count"]=$nb_result_categories;	
}