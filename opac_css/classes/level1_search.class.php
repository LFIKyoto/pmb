<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_search.class.php,v 1.10 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/searcher.class.php");
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);
require_once($class_path."/thesaurus.class.php");

/* Classe qui permet de faire la recherche de premier niveau */

class level1_search {
	var $user_query;
	
    function level1_search() {
    }
    
    function search_title() {
    	global $typdoc;
    	global $charset;
		$search_title = new searcher_title(stripslashes($this->user_query));
		$notices = $search_title->get_result();
		$nb_result_titres = $search_title->get_nb_results();
		$l_typdoc= implode(",",$search_title->get_typdocs());
		$mode="title";
		
		//définition du formulaire
		$form = "<form name=\"search_objects\" action=\"./index.php?lvl=more_results\" method=\"post\">";
		if (function_exists("search_other_function_post_values")){
				$form .=search_other_function_post_values(); 
			}
		$form .= "
		  	<input type=\"hidden\" name=\"mode\" value=\"title\">
		  	<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">
		  	<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
		  	<input type=\"hidden\" name=\"count\" value=\"".$nb_result_titres."\">
		  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">
		  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
		  	</form>";
		if ($nb_result_titres) {
			$_SESSION["level1"]["title"]["form"]=$form;
			$_SESSION["level1"]["title"]["count"]=$nb_result_titres;	
		}
		return $nb_result_titres;
    }
    
    function search_authors() {
    	global $opac_search_other_function,$typdoc,$msg,$charset,$dbh;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,1);
		$members=$aq->get_query_members("authors","concat(author_name,', ',author_rejete)","index_author","author_id");
		$clause =' where '.$members["where"];
		
		if ($opac_search_other_function) $add_notice=search_other_function_clause();
		if ($typdoc || $add_notice) $clause = ',notices, responsability '.$clause.' and responsability_author=author_id and notice_id=responsability_notice';
		if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";		
		if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 
		
		$tri = 'order by pert desc, index_author';
		$pert=$members["select"]." as pert";
		$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='70' ", $dbh);
		$nb_result_auteurs_physiques = pmb_mysql_result($auteurs, 0 , 0);
		$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='71' ", $dbh);
		$nb_result_auteurs_collectivites = pmb_mysql_result($auteurs, 0 , 0);
		$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='72' ", $dbh);
		$nb_result_auteurs_congres = pmb_mysql_result($auteurs, 0 , 0);
		$nb_result_auteurs=$nb_result_auteurs_physiques+$nb_result_auteurs_collectivites+$nb_result_auteurs_congres;

		if($nb_result_auteurs_physiques == $nb_result_auteurs) {
			// Il n'y a que des auteurs physiques, affichage type: Auteurs xx résultat(s) afficher
			$titre_resume[0]=$msg["authors"];
			$nb_result_resume[0]=$nb_result_auteurs;
			$link_type_resume[0]="70";
		} else if($nb_result_auteurs_collectivites == $nb_result_auteurs) {
			// Il n'y a que des collectivites, affichage type: Collectivités xx résultat(s) afficher
			$titre_resume[0]=$msg["collectivites_search"];
			$nb_result_resume[0]=$nb_result_auteurs;
			$link_type_resume[0]="71";
		} else if($nb_result_auteurs_congres == $nb_result_auteurs) {
			// Il n'y a que des congres, affichage type: Collectivités xx résultat(s) afficher
			$titre_resume[0]=$msg["congres_search"];
			$nb_result_resume[0]=$nb_result_auteurs;
			$link_type_resume[0]="72";
		} else {
			// il y a un peu de tout, affichage en titre type: Auteurs xx résultat(s) afficher
			$titre_resume[0]=$msg["authors"];
			$nb_result_resume[0]=$nb_result_auteurs;
			$link_type_resume[0]="";
		
			if($nb_result_auteurs_physiques) {
			// Il n'y a des auteurs physiques, affichage en sous-titre titre: Auteurs physiques xx résultat(s) afficher
				$titre_resume[]=$msg["personnes_physiques_search"];
				$nb_result_resume[]=$nb_result_auteurs_physiques;
				$link_type_resume[]="70";
			}
			if($nb_result_auteurs_collectivites) {
				// Il n'y a des collectivites, affichage en sous-titre titre: Collectivités xx résultat(s) afficher
				$titre_resume[]=$msg["collectivites_search"];
				$nb_result_resume[]=$nb_result_auteurs_collectivites;
				$link_type_resume[]="71";
			}
			if($nb_result_auteurs_congres) {
				// Il n'y a des congres, affichage en sous-titre titre: Congrès xx résultat(s) afficher
				$titre_resume[]=$msg["congres_search"];
				$nb_result_resume[]=$nb_result_auteurs_congres;
				$link_type_resume[]="72";
			}
		}
		
		if ($nb_result_auteurs) {
			// tout bon, y'a du résultat, on lance le pataquès d'affichage
			$form = "<div style=search_result><form name=\"search_authors\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"auteur\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"author_type\" value=\"\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_auteurs."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form></div>\n";
			if ($nb_result_auteurs) {
				$_SESSION["level1"]["author"]["form"]=$form;
				$_SESSION["level1"]["author"]["count"]=$nb_result_auteurs;	
			}
		}
		return $nb_result_auteurs;
    }
    
    function search_publishers() {
    	global $opac_search_other_function,$typdoc,$charset,$dbh;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,1);
		$members=$aq->get_query_members("publishers","ed_name","index_publisher","ed_id");
		$clause.= "where ".$members["where"];
		
		if ($opac_search_other_function) $add_notice=search_other_function_clause();
		if ($typdoc || $add_notice) $clause = ', notices '.$clause.' and (ed1_id=ed_id or ed2_id=ed_id) ';
		if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";
		if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 
		
		$tri = 'order by pert desc, index_publisher';
		$pert=$members["select"]." as pert";
		
		$editeurs = pmb_mysql_query("SELECT COUNT(distinct ed_id) FROM publishers $clause", $dbh);
		$nb_result_editeurs = pmb_mysql_result($editeurs, 0 , 0); 
		
		if ($nb_result_editeurs) {
			//définition du formulaire
			$form = "<div style=search_result><form name=\"search_publishers\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"editeur\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_editeurs ."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";	
			$form .= "</div>";	
			
			$_SESSION["level1"]["publisher"]["form"]=$form;
			$_SESSION["level1"]["publisher"]["count"]=$nb_result_editeurs;	
		}
		return $nb_result_editeurs;
    }
    
    function search_titres_uniformes() {
    	global $opac_search_other_function,$typdoc,$charset,$dbh;
    	global $opac_stemming_active;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,1,$opac_stemming_active);
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
	
		if ($nb_result_titres_uniformes) {
			//définition du formulaire...
			$form = "<form name=\"search_titres_uniformes\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"titre_uniforme\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_titres_uniformes ."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";	
		
			$_SESSION["level1"]["titre_uniforme"]["form"]=$form;
			$_SESSION["level1"]["titre_uniforme"]["count"]=$nb_result_titres_uniformes;	
		}	
		return $nb_result_titres_uniformes;
    }
    
    function search_collections() {
    	global $opac_search_other_function,$typdoc,$dbh,$charset;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query));
		$members=$aq->get_query_members("collections","collection_name","index_coll","collection_id");
		$clause.= 'where '.$members["where"];
		
		if ($opac_search_other_function) $add_notice=search_other_function_clause();
		if ($typdoc || $add_notice) $clause = ',notices '.$clause.' and coll_id=collection_id ';
		if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";
		if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 
		
		$tri = 'order by pert desc, index_coll';
		$pert=$members["select"]." as pert";
		
		$collections = pmb_mysql_query("SELECT COUNT(distinct collection_id) FROM collections $clause", $dbh);
		$nb_result_collections = pmb_mysql_result($collections, 0 , 0);
		
		if ($nb_result_collections) {
			//définition du formulaire...
			$form = "<form name=\"search_collection\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"collection\">";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_collections."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "</form>";
			$_SESSION["level1"]["collection"]["form"]=$form;
			$_SESSION["level1"]["collection"]["count"]=$nb_result_collections;	
		}
		return $nb_result_collections;
    }
    
    function search_subcollections() {
    	global $opac_search_other_function,$typdoc,$dbh,$charset;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query));
		$members=$aq->get_query_members("sub_collections","sub_coll_name","index_sub_coll","sub_coll_id");
		$clause.= "where ".$members["where"];
		
		if ($opac_search_other_function) $add_notice=search_other_function_clause();
		if ($typdoc || $add_notice) $clause = ', notices '.$clause.' and subcoll_id=sub_coll_id ';
		if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";
		if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 
		$tri = 'order by pert desc, index_sub_coll';
		$pert=$members["select"]." as pert";
		
		$subcollections = pmb_mysql_query("SELECT COUNT(sub_coll_id) FROM sub_collections $clause", $dbh);
		$nb_result_subcollections = pmb_mysql_result($subcollections, 0 , 0); 
		
		if ($nb_result_subcollections) {
			//définition du formulaire
			$form = "<div style=search_result><form name=\"search_sub_collection\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"souscollection\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_subcollections."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
			$_SESSION["level1"]["subcollection"]["form"]=$form;
			$_SESSION["level1"]["subcollection"]["count"]=$nb_result_subcollections;	
		}
		return $nb_result_subcollections;
    }
    
    function search_categories() {
    	global $opac_search_other_function,$typdoc,$dbh,$charset,$opac_thesaurus_defaut,$lang,$opac_thesaurus;
    	global $opac_stemming_active;
    	
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
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,0,$opac_stemming_active);
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
				$q.="noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue='".$thes->langue_defaut."' JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."'";
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
		
		if ($nb_result_categories) {
			$form = "<form name=\"search_categorie\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"categorie\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".$nb_result_categories."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" id=\"id_thes\" name=\"id_thes\" value=\"".$id_thes_for_link."\"></form>\n";
			$_SESSION["level1"]["category"]["form"]=$form;
			$_SESSION["level1"]["category"]["count"]=$nb_result_categories;	
		}	
		return $nb_result_categories;
    }
    
    function search_indexints() {
    	global $opac_search_other_function,$typdoc,$dbh,$charset;
    	global $opac_stemming_active;
    	// on regarde comment la saisie utilisateur se présente
		$clause = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,0,$opac_stemming_active);
		$members=$aq->get_query_members("indexint","concat(indexint_name,' ',indexint_comment)","index_indexint","indexint_id");
		$clause.= "where ".$members["where"];
		
		if ($opac_search_other_function) $add_notice=search_other_function_clause();
		
		if ($typdoc || $add_notice) $clause = ', notices '.$clause.' and indexint=indexint_id ';
		
		if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";
		
		if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 
		
		$tri = 'order by pert desc, index_indexint';
		$pert=$members["select"]." as pert";
		
		$indexint = pmb_mysql_query("SELECT COUNT(distinct indexint_id) FROM indexint $clause", $dbh);
		$nb_result_indexint = pmb_mysql_result($indexint, 0 , 0);

		if ($nb_result_indexint) {
			//définition du formulaire
			$form = "<form name=\"search_indexint\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"indexint\">\n";
			$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
			$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_indexint."\">\n";
			$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
			$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
			$_SESSION["level1"]["indexint"]["form"]=$form;
			$_SESSION["level1"]["indexint"]["count"]=$nb_result_indexint;	
		}
		return $nb_result_indexint;
    }
    
    function search_keywords() {
    	global $typdoc,$dbh,$charset;
    	$search_keywords = new searcher_keywords(stripslashes($this->user_query));
		$notices = $search_keywords->get_result();
		$nb_result_keywords = $search_keywords->get_nb_results();
		$l_typdoc= implode(",",$search_keywords->get_typdocs());
		
		/*$search_terms = $aq->get_positive_terms($aq->tree);
		//On enlève le dernier terme car il s'agit de la recherche booléenne complète
		unset($search_terms[count($search_terms)-1]);*/
		
		if ($nb_result_keywords) {
			$form = "<form name=\"search_keywords\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"mode\" value=\"keyword\">
				<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">
				<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
			  	<input type=\"hidden\" name=\"count\" value=\"".$nb_result_keywords."\">
			  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">
			  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
			  </form>";
		  
			$_SESSION["level1"]["keywords"]["form"]=$form;
			$_SESSION["level1"]["keywords"]["count"]=$nb_result_keywords;	
		}
		return $nb_result_keywords;
    }
    
    function search_abstracts() {
    	global $typdoc,$dbh,$charset;
    	$searcher_abstract = new searcher_abstract(stripslashes($this->user_query));
		$notices = $searcher_abstract->get_result();
		$nb_result_abstract = $searcher_abstract->get_nb_results();
		$l_typdoc= implode(",",$searcher_abstract->get_typdocs());
		
		if ($nb_result_abstract) {
			//définition du formulaire
			$form = "<form name=\"search_abstract\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
			$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">
				<input type=\"hidden\" name=\"mode\" value=\"abstract\">
				<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">
				<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
				<input type=\"hidden\" name=\"count\" value=\"".$nb_result_abstract."\">
			  	<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">
			  	<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">
			  	</form>";
			$_SESSION["level1"]["abstract"]["form"]=$form;
			$_SESSION["level1"]["abstract"]["count"]=$nb_result_abstract;	
		}
		return $nb_result_abstract;
    }
    
    function search_docnums() {
    	global $typdoc,$dbh,$charset,$gestion_acces_active,$gestion_acces_empr_notice,$opac_search_other_function,$class_path;
    	global $opac_stemming_active;
    	if($_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
			$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
		}
		if ($typdoc) $restrict="typdoc='".$typdoc."'"; else $restrict="";
		
		//droits d'acces emprunteur/notice
		$acces_j='';
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			require_once("$class_path/acces.class.php");
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],16,'notice_id');
		} 
		
		// on regarde comment la saisie utilisateur se presente
		$clause = '';
		$clause_bull = '';
		$clause_bull_num_notice = '';
		$add_notice = '';
		
		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,0,$opac_stemming_active);
		
		if ($acces_j) {
			$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice"," explnum_notice=notice_id and explnum_bulletin=0",0,0,true);
			$clause="where ".$members["where"]." and (".$members["restrict"].")";
			
			$members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and explnum_notice=0 and num_notice=0 and bulletin_notice=notice_id",0,0,true);
			$clause_bull="where ".$members_bull["where"]." and (".$members_bull["restrict"].")";
			
			$members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and num_notice=notice_id",0,0,true);
			$clause_bull_num_notice="where ".$members_bull_num_notice["where"]." and (".$members_bull_num_notice["restrict"].")";
			$statut_j='';
		
		} else {
			$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice" ," explnum_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause="where ".$members["where"]." and (".$members["restrict"].")";
			
			$members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and bulletin_notice=notice_id and num_notice=0 and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause_bull="where ".$members_bull["where"]." and (".$members_bull["restrict"].")";
			
			$members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and num_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause_bull_num_notice="where ".$members_bull_num_notice["where"]." and (".$members_bull_num_notice["restrict"].")";
			
			$statut_j=',notice_statut';
		}
		
		if ($opac_search_other_function) {
			$add_notice = search_other_function_clause();
			if ($add_notice) {
				$clause.= ' and notice_id in ('.$add_notice.')';
				$clause_bull.= ' and notice_id in ('.$add_notice.')';  
				$clause_bull_num_notice.= ' and notice_id in ('.$add_notice.')';  
			}
		}
		
		$search_terms = $aq->get_positive_terms($aq->tree);
		//On enlève le dernier terme car il s'agit de la recherche booléenne complète
		unset($search_terms[count($search_terms)-1]);
		
		$pert=$members["select"]." as pert";
		$tri="order by pert desc, index_serie, tnvol, index_sew";
		
		if ($restrict) {
			$clause.=" and ".$restrict;
			$clause_bull.=" and ".$restrict;
			$clause_bull_num_notice.=" and ".$restrict;
		}
		
		if($opac_view_restrict)  $clause.=" and ".$opac_view_restrict;
		
		if($clause) {
			// instanciation de la nouvelle requête 
			$q_docnum_noti = "select explnum_id from explnum, notices $statut_j $acces_j $clause"; 
			$q_docnum_bull = "select explnum_id from bulletins, explnum, notices $statut_j $acces_j $clause_bull";
			$q_docnum_bull_notice = "select explnum_id from bulletins, explnum, notices $statut_j $acces_j $clause_bull_num_notice";
			
			$q_docnum = "select count(explnum_id) from ( $q_docnum_noti UNION $q_docnum_bull UNION $q_docnum_bull_notice) as uni	";
			$docnum = pmb_mysql_query($q_docnum, $dbh);
			$nb_result_docnum=0;
			if($docnum && pmb_mysql_num_rows($docnum)){
				$nb_result_docnum = pmb_mysql_result($docnum, 0, 0);
			}
			
			$req_typdoc_noti="select distinct typdoc from explnum,notices $statut_j $acces_j $clause group by typdoc"; 
			$req_typdoc_bull = "select distinct typdoc from bulletins, explnum,notices $statut_j $acces_j $clause_bull group by typdoc";  
			$req_typdoc_bull_num_notice = "select distinct typdoc from bulletins, explnum,notices $statut_j $acces_j $clause_bull_num_notice group by typdoc";  
			$req_typdoc = "($req_typdoc_noti) UNION ($req_typdoc_bull) UNION ($req_typdoc_bull_num_notice)";
			$res_typdoc = pmb_mysql_query($req_typdoc, $dbh);	
			$t_typdoc=array();
			if($res_typdoc && pmb_mysql_num_rows($res_typdoc)){
				while (($tpd=pmb_mysql_fetch_object($res_typdoc))) {
					$t_typdoc[]=$tpd->typdoc;
				}
			}
			$l_typdoc=implode(",",$t_typdoc);	
			if ($nb_result_docnum) {
				$form = "<form name=\"search_docnum\" action=\"./index.php?lvl=more_results\" method=\"post\">";
				$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
				if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
				$form .= "<input type=\"hidden\" name=\"mode\" value=\"docnum\">\n";
				$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
				$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_docnum."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause_bull\" value=\"".htmlentities($clause_bull,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause_bull_num_notice\" value=\"".htmlentities($clause_bull_num_notice,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"search_terms\" value=\"".htmlentities(serialize($search_terms),ENT_QUOTES,$charset)."\"></form>\n";
				$_SESSION["level1"]["docnum"]["form"]=$form;
				$_SESSION["level1"]["docnum"]["count"]=$nb_result_docnum;	
			}
		}
    }

    function search_concepts() {
    	global $opac_search_other_function,$typdoc,$charset,$dbh;
    	global $opac_stemming_active;
    	// on regarde comment la saisie utilisateur se présente
    	$clause = '';
    	$add_notice = '';
    
    	$aq=new analyse_query(stripslashes($this->user_query),0,0,1,0,$opac_stemming_active);
    	$clause.= "where (value like '%".stripslashes($user_query)."%') and code_champ = 1";
    
    	if ($opac_search_other_function) $add_notice=search_other_function_clause();
    	if ($typdoc || $add_notice) $clause = ',notices, index_concept '.$clause.' and num_concept=id_item and notice_id=num_object and type_object=1';
    	if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";
    	if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')';

		$tri = 'order by pert desc, value';
		$pert= "(value like '%".stripslashes($user_query)."%') as pert";

		$nb_result_concepts=pmb_mysql_result(pmb_mysql_query("SELECT COUNT(distinct id_item) FROM skos_fields_global_index ".$clause, $dbh),0,0);
    
    	if ($nb_result_concepts) {
    		//définition du formulaire
    		$form = "<div style=search_result><form name=\"search_publishers\" action=\"./index.php?lvl=more_results\" method=\"post\">";
    		$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
    		if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
    		$form .= "<input type=\"hidden\" name=\"mode\" value=\"concept\">\n";
    		$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
    		$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_concepts ."\">\n";
    		$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
    		$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
    		$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
    		$form .= "</div>";
    			
    		$_SESSION["level1"]["concept"]["form"]=$form;
    		$_SESSION["level1"]["concept"]["count"]=$nb_result_concepts;
    	}
    	return $nb_result_concepts;
    }
    
    function make_search() {
    	global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher;
    	global $opac_modules_search_titre_uniforme,$opac_modules_search_collection,$opac_modules_search_subcollection;
    	global $opac_modules_search_category,$opac_modules_search_indexint,$opac_modules_search_keywords;
    	global $opac_modules_search_abstract,$opac_modules_search_docnum,$opac_modules_search_all, $opac_modules_search_concept;
    	global $look_TITLE,$look_AUTHOR,$look_PUBLISHER,$look_TITRE_UNIFORME,$look_COLLECTION,$look_SUBCOLLECTION;
    	global $look_CATEGORY,$look_INDEXINT,$look_KEYWORDS,$look_ABSTRACT,$look_DOCNUM,$look_ALL, $look_CONCEPT;
    	global $user_query;

    	$this->user_query=$user_query;
    	    	
    	if ($opac_modules_search_title && $look_TITLE) {
			$total_results += $this->search_title();	
		}

		if ($opac_modules_search_author && $look_AUTHOR) {
			$total_results += $this->search_authors();
		}

		if ($opac_modules_search_publisher && $look_PUBLISHER) {
			$total_results += $this->search_publishers();
		}
		if ($opac_modules_search_titre_uniforme && $look_TITRE_UNIFORME) {
			$total_results += $this->search_titres_uniformes();
		}
		if ($opac_modules_search_collection && $look_COLLECTION) {
			$total_results += $this->search_collections();
		}

		if ($opac_modules_search_subcollection && $look_SUBCOLLECTION) {
			$total_results += $this->search_subcollections();
		}

		if ($opac_modules_search_category && $look_CATEGORY) {
			$total_results += $this->search_categories();
		}
		if ($opac_modules_search_indexint && $look_INDEXINT) {
			$total_results += $this->search_indexints();
		}

		if ($opac_modules_search_keywords && $look_KEYWORDS) {	
			$total_results += $this->search_keywords();
		}

		if ($opac_modules_search_abstract && $look_ABSTRACT) {
			$total_results += $this->search_abstracts();
		}
		
		if ($opac_modules_search_docnum && $look_DOCNUM) {
			$total_results += $this->search_docnums();
		}
		if ($opac_modules_search_concept && $look_CONCEPT) {
			$total_results += $this->search_concepts();
		}
		
		$authpersos=new authpersos();
		$total_results +=$authpersos->search_authperso($this->user_query);
		
		/*if ($opac_modules_search_all && $look_ALL) {
			require_once($base_path.'/search/level1/tous.inc.php');	
			$total_results += $nb_result;
			$nb_all_results=$nb_result;
		}*/
		return $total_results;
    }
}
?>