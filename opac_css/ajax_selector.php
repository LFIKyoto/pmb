<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_selector.php,v 1.42.2.1 2015-10-26 15:21:44 jpermanne Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
	
// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");
require_once($base_path.'/includes/start.inc.php');

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

require_once($base_path."/includes/marc_tables/".$pmb_indexation_lang."/empty_words");
require_once($base_path."/includes/misc.inc.php");

require_once("$class_path/marc_table.class.php");
require_once("$class_path/analyse_query.class.php");
require_once("$class_path/perio_a2z.class.php");

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");
	if($_SESSION["opac_view"]){
		$opac_view_class= new $pmb_opac_view_class($_SESSION["opac_view"],$_SESSION["id_empr_session"]);
	 	if($opac_view_class->id){
	 		$opac_view_class->set_parameters();
	 		$opac_view_filter_class=$opac_view_class->opac_filters;
	 		$_SESSION["opac_view"]=$opac_view_class->id;
	 		if(!$opac_view_class->opac_view_wo_query) {
	 			$_SESSION['opac_view_query']=1;
	 		}
	 	} else {
	 		$_SESSION["opac_view"]=0;
	 	}
		$css=$_SESSION["css"]=$opac_default_style;
	}
}

header("Content-Type: text/html; charset=$charset");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

$start=stripslashes($datas);
$start = str_replace("*","%",$start);

$autexclude_tab=explode(",",$autexclude);
foreach($autexclude_tab as $id_autexclude){
	$autexclude_tab_clean[]=$id_autexclude+0;
}
$autexclude=implode(",",$autexclude_tab_clean);

$fontsize = "10px";

switch($completion):
	case 'categories':
		$array_selector=array();
		$array_prefix=array();
		require_once("$class_path/thesaurus.class.php");
		require_once("$class_path/categories.class.php");
		if ($opac_thesaurus==1){
			$id_thes=-1;	
		}else{
			$id_thes=$opac_thesaurus_defaut;
		}

		$thesaurus_requette='';
		$thes_unique=0;
		if($opac_thesaurus==0){
			$thesaurus_requette= " id_thesaurus='$opac_thesaurus_defaut' and ";
			$thes_unique=$opac_thesaurus_defaut;
			$thes = new thesaurus($thes_unique);
		}elseif($linkfield){
			if(!preg_match("#,#i",$linkfield)){
				$thesaurus_requette= " id_thesaurus='$linkfield' and ";
				$thes_unique=$linkfield;
				$thes = new thesaurus($thes_unique);
			}else{
				$thesaurus_requette= " id_thesaurus in ($linkfield) and ";
			}
		}
		
		$aq=new analyse_query($start);
		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		
		$requete="SELECT noeuds.id_noeud AS categ_id, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, noeuds.not_use_in_indexation";
			if($thes_unique && (($lang==$thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false))){
				$requete.=", catdef.langue as langue, catdef.libelle_categorie as categ_libelle,catdef.index_categorie as index_categorie, (".$members_catdef["select"].") as pert ";
				$requete.=" FROM noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND  catdef.langue = '".$thes->langue_defaut."'";
				$requete.=" WHERE noeuds.num_thesaurus='".$thes_unique."' and catdef.libelle_categorie like '".addslashes($start)."%'";
			}else{
				$requete.=", if (catlg.num_noeud is null, catdef.langue , catlg.langue) as langue, if (catlg.num_noeud is null, catdef.libelle_categorie , catlg.libelle_categorie ) as categ_libelle,if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as index_categorie, if(catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
				$requete.=" FROM thesaurus JOIN noeuds ON thesaurus.id_thesaurus = noeuds.num_thesaurus JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue=thesaurus.langue_defaut JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
				$requete.=" WHERE $thesaurus_requette if(catlg.num_noeud is null, catdef.libelle_categorie like '".addslashes($start)."%', catlg.libelle_categorie like '".addslashes($start)."%')";
			}
			$requete.=" order by pert desc,num_thesaurus, categ_libelle";		
		$aq=new analyse_query(stripslashes($datas."*"));
		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		if (!$aq->error) {
			$requete1="SELECT noeuds.id_noeud AS categ_id, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, noeuds.not_use_in_indexation";
			if($thes_unique && (($lang==$thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false))){
				$requete1.=", catdef.langue as langue, catdef.libelle_categorie as categ_libelle,catdef.index_categorie as index_categorie, (".$members_catdef["select"].") as pert ";
				$requete1.=" FROM noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND  catdef.langue = '".$thes->langue_defaut."'";
				$requete1.=" WHERE noeuds.num_thesaurus='".$thes_unique."' and catdef.libelle_categorie not like '~%' and ".$members_catdef["where"];
			}else{
				$requete1.=", if (catlg.num_noeud is null, catdef.langue , catlg.langue) as langue, if (catlg.num_noeud is null, catdef.libelle_categorie , catlg.libelle_categorie ) as categ_libelle,if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as index_categorie, if(catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
				$requete1.=" FROM thesaurus JOIN noeuds ON thesaurus.id_thesaurus = noeuds.num_thesaurus JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue=thesaurus.langue_defaut JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
				$requete1.=" WHERE $thesaurus_requette if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].")";
			}
			$requete1.=" order by pert desc,num_thesaurus, categ_libelle";
		} else $requete1="";
		$res = @pmb_mysql_query($requete, $dbh);
		while(($categ=pmb_mysql_fetch_object($res)) && (count($array_selector) < 20)) {
			$display_temp = "" ;
			$display_temp_prefix = "" ;
			$lib_simple="";
			$tab_lib_categ="";
			$temp = new categories($categ->categ_id, $categ->langue);
			if (($id_thes == -1) && (!$thes_unique)) {
				$thes = new thesaurus($categ->num_thesaurus);
				$display_temp_prefix = htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset);
			}
			$id_categ_retenue = $categ->categ_id ;
			$not_use_in_indexation=$categ->not_use_in_indexation;
			if($categ->categ_see) {
				$id_categ_retenue = $categ->categ_see ;
				//Catégorie à ne pas utiliser en indexation
				$category=new category($id_categ_retenue);
				$not_use_in_indexation=$category->not_use_in_indexation;
				
				$temp = new categories($categ->categ_see, $categ->langue);
				$display_temp.= $categ->categ_libelle." -> ";
				$lib_simple = $temp->libelle_categorie;
				$chemin=categories::listAncestorNames($categ->categ_see, $categ->langue);
				if ($opac_categories_show_only_last){
					$display_temp.= $temp->libelle_categorie;	
				}else{
					$display_temp.= $chemin;
				}			
				$display_temp.= "@";
			} else {
				$lib_simple = $categ->categ_libelle;
				$chemin=categories::listAncestorNames($categ->categ_id, $categ->langue);
				if ($opac_categories_show_only_last){
					$display_temp.= $categ->categ_libelle;
				}else{
					$display_temp.= $chemin;
				} 			
			}	
			
			if(!$not_use_in_indexation && !preg_match("#:~|^~#i",$chemin)){
				$tab_lib_categ[$display_temp] = $lib_simple;		
				$array_selector["*".$id_categ_retenue] = $tab_lib_categ ;
				if ($display_temp_prefix) {
					$array_prefix["*".$id_categ_retenue]=array(
						'id' => $categ->num_thesaurus,
						'libelle' => $display_temp_prefix
					);	
				}
			}
			
		} // fin while
		if ($requete1  && (count($array_selector) < 20)) {
			$res1 = @pmb_mysql_query($requete1, $dbh);
			while(($categ=pmb_mysql_fetch_object($res1)) && (count($array_selector) <= 20)) {
				$display_temp = "" ;
				$display_temp_prefix="";
				$lib_simple="";
				$tab_lib_categ="";
				$temp = new categories($categ->categ_id, $categ->langue);
				if (($id_thes == -1) && (!$thes_unique)) {
					$thes = new thesaurus($categ->num_thesaurus);
					$display_temp_prefix = htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset);
				}
				$id_categ_retenue = $categ->categ_id ;
				$not_use_in_indexation=$categ->not_use_in_indexation;
				if($categ->categ_see) {
					$id_categ_retenue = $categ->categ_see ;
					//Catégorie à ne pas utiliser en indexation
					$category=new category($id_categ_retenue);
					$not_use_in_indexation=$category->not_use_in_indexation;
					
					$temp = new categories($categ->categ_see, $categ->langue);
					$display_temp.= $categ->categ_libelle." -> ";
					$lib_simple = $temp->libelle_categorie;
					$chemin=categories::listAncestorNames($categ->categ_see, $categ->langue);
					if ($opac_categories_show_only_last){
						$display_temp.= $temp->libelle_categorie;	
					}else{
						$display_temp.= $chemin;
					}			
					$display_temp.= "@";
				} else {
					$lib_simple = $categ->categ_libelle;
					$chemin=categories::listAncestorNames($categ->categ_id, $categ->langue);
					if ($opac_categories_show_only_last){
						$display_temp.= $categ->categ_libelle;
					}else{
						$display_temp.= $chemin;
					}			
				}		
				if (!$array_selector[$id_categ_retenue] && !$not_use_in_indexation && !preg_match("#:~|^~#i",$chemin)) {			
					$tab_lib_categ[$display_temp] = $lib_simple;		
					$array_selector["*".$id_categ_retenue] = $tab_lib_categ ;
					if ($display_temp_prefix) {
						$array_prefix["*".$id_categ_retenue]=array(
							'id' => $categ->num_thesaurus,
							'libelle' => $display_temp_prefix
						);	
					}
				}
			} // fin while		
		}
		$origine = "ARRAY" ;
		break;
	case 'categories_mul':
		/* Pas utilisé en Opac Matthieu 02/08/2012 */
		$array_selector=array();
		require_once("$class_path/thesaurus.class.php");
		require_once("$class_path/categories.class.php");
		
		if ($opac_thesaurus==1) $id_thes=-1;
			else $id_thes=$opac_thesaurus_defaut;

		$aq=new analyse_query($start);

		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");

		$thesaurus_requette='';
		
		if($thesaurus_mode_pmb==0) $thesaurus_requette= " id_thesaurus='$thesaurus_defaut' and ";
		elseif($linkfield) $thesaurus_requette= " id_thesaurus in ($linkfield) and ";
		
		$requete_langue="select catlg.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catlg.langue as langue, 
		catlg.libelle_categorie as categ_libelle,catlg.index_categorie as index_categorie, catlg.note_application as categ_comment, 
		(".$members_catlg["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catlg on noeuds.id_noeud = catlg.num_noeud 
		and catlg.langue = '".$lang."' where $thesaurus_requette catlg.libelle_categorie like '".addslashes($start)."%' and catlg.libelle_categorie not like '~%'";
		
		$requete_defaut="select catdef.num_noeud as categ_id, noeuds.num_parent as categ_parent, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, catdef.langue as langue, 
		catdef.libelle_categorie as categ_libelle,catdef.index_categorie as index_categorie, catdef.note_application as categ_comment, 
		(".$members_catdef["select"].") as pert from thesaurus left join noeuds on  thesaurus.id_thesaurus = noeuds.num_thesaurus left join categories as catdef on noeuds.id_noeud = catdef.num_noeud 
		and catdef.langue = thesaurus.langue_defaut where $thesaurus_requette catdef.libelle_categorie like '".addslashes($start)."%' and catdef.libelle_categorie not like '~%'";
		
		$requete="select * from (".$requete_langue." union ".$requete_defaut.") as sub1 group by categ_id order by pert desc,num_thesaurus, index_categorie limit 20";

		$res = @pmb_mysql_query($requete, $dbh);
		while(($categ=pmb_mysql_fetch_object($res))) {
			$display_temp = "" ;
			$lib_simple="";
			$tab_lib_categ="";
			$temp = new categories($categ->categ_id, $categ->langue);
			if ($id_thes == -1) {
				$thes = new thesaurus($categ->num_thesaurus);
				$display_temp = htmlentities('['.$thes->libelle_thesaurus.'] ',ENT_QUOTES, $charset);
			}
			$id_categ_retenue = $categ->categ_id ;	
			if($categ->categ_see) {
				$id_categ_retenue = $categ->categ_see ;
				$temp = new categories($categ->categ_see, $categ->langue);
				$display_temp.= $categ->categ_libelle." -> ";
				$lib_simple = $temp->libelle_categorie;
				if ($thesaurus_categories_show_only_last) $display_temp.= $temp->libelle_categorie;
					else $display_temp.= categories::listAncestorNames($categ->categ_see, $categ->langue);				
				$display_temp.= "@";
			} else {
				$lib_simple = $categ->categ_libelle;
				if ($thesaurus_categories_show_only_last) $display_temp.= $categ->categ_libelle;
				else $display_temp.= categories::listAncestorNames($categ->categ_id, $categ->langue); 			
			}		
			
			$tab_lib_categ[$display_temp] = $lib_simple; 
			$array_selector[$id_categ_retenue] = $tab_lib_categ;
			$array_prefix[$id_categ_retenue]['id'] = $categ->num_thesaurus;
		} // fin while		
		$origine = "ARRAY" ;
		break;
	case 'authors':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		$requete="select if(author_date!='',concat(if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name),' (',author_date,')'),if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as author,author_id from authors where if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'authors_person':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		$requete="select if(author_date!='',concat(if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name),' (',author_date,')'),if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as author,author_id from authors where author_type='70' and if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'congres_name':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		$requete="select distinct author_name from authors where  author_type='72' and author_name like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;	
	case 'collectivite_name':
		if ($autexclude) $restrict = " AND author_id not in ($autexclude) ";
		$requete="select distinct author_name from authors where  author_type='71' and author_name like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;	
	case 'publishers':
		if ($autexclude) $restrict = " AND ed_id not in ($autexclude) ";
		$requete="select concat(
					ed_name,
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''), 
					if(ed_ville is not null and ed_ville!='',ed_ville,''),
					if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''), 
					if(ed_pays is not null and ed_pays!='',ed_pays,''), 
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
					) as ed,ed_id from publishers where concat(
					ed_name,
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''), 
					if(ed_ville is not null and ed_ville!='',ed_ville,''),
					if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''), 
					if(ed_pays is not null and ed_pays!='',ed_pays,''), 
					if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
					) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'titres_uniformes':
		if ($autexclude) $restrict = " AND tu_id not in ($autexclude) ";
		$requete="select if(tu_comment is not null and tu_comment!='',concat(tu_name,' : ',tu_comment),tu_name) as titre_uniforme,tu_id from titres_uniformes where if(tu_comment is not null and tu_comment!='',concat(tu_name,' - ',tu_comment),tu_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;		
	case 'collections':
		if ($autexclude) $restrict = " AND collection_id not in ($autexclude) ";
		if ($linkfield) $restrict .= " AND collection_parent ='$linkfield' ";
		$requete="select if(collection_issn is not null and collection_issn!='',concat(collection_name,', ',collection_issn),collection_name) as coll,collection_id from collections where if(collection_issn is not null and collection_issn!='',concat(collection_name,', ',collection_issn),collection_name) like '".addslashes($start)."%' $restrict order by index_coll limit 20";
		$origine = "SQL" ;
		break;
	case 'subcollections':
		if ($autexclude) $restrict = " AND sub_coll_id not in ($autexclude) ";
		if ($linkfield) $restrict .= " AND sub_coll_parent ='$linkfield' ";
		$requete="select if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) as subcoll,sub_coll_id from sub_collections where if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'indexint':
		if ($autexclude) $restrict = " AND indexint_id not in ($autexclude) ";
		$requete="select if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' : ',indexint_comment),indexint_name) as indexint,indexint_id, concat( indexint_name,' ',indexint_comment) as indexsimple from indexint where if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' $restrict order by 1 limit 20";	
		$origine = "SQL" ;
		break;
	case 'notice':
		require_once('./includes/isbn.inc.php');
		if ($autexclude) $restrict = " AND notice_id not in ($autexclude) ";
		$requete = "select if(serie_name is not null,if(tnvol is not null,concat(serie_name,', ',tnvol,'. ',tit1),concat(serie_name,'. ',tit1)),tit1), notice_id from notices left join series on serie_id=tparent_id where (index_sew like ' ".addslashes(strip_empty_words($start))."%' or TRIM(index_wew) like '".addslashes($start)."%' or tit1 like '".addslashes($start)."%' or code like '".traite_code_isbn(addslashes($start))."') $restrict order by index_serie, tnvol, index_sew , code limit 20 ";
		$origine = "SQL" ;
		break;
	case 'serie':
		if ($autexclude) $restrict = " AND serie_id not in ($autexclude) ";
		$requete="select serie_name,serie_id from series where serie_name like '".addslashes($start)."%' $restrict order by 1 limit 20";
		$origine = "SQL" ;
		break;
	case 'fonction':
		// récupération des codes de fonction
		if (!count($s_func )) {
			$s_func = new marc_list('function');
		}
		$origine = "TABLEAU" ;
		break;
	case 'langue':
		// récupération des codes de langue
		if (!count($s_func )) {
			$s_func = new marc_list('lang');
		}
		$origine = "TABLEAU" ;
		break;
	case 'bull_num':	
		$id_notice = substr($id,13);
		$requete = "select bulletin_numero, date_date from bulletins where bulletin_notice='$id_notice' and bulletin_numero like '%".addslashes($start)."%' order by 1 limit 20";
		$origine = "SQL"; 
		break;
	case 'perio_a2z';
		$array_selector=array();
		$abt_actif=$autexclude;
		$perio_a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
		//$titles=$perio_a2z->filter($start);
		$titles=$perio_a2z->filterSearch($start,$datas);
		foreach($titles as $title) {
			$array_selector[$title["onglet"].".".$title["id"]]=array($title["title"]=>$title["title"]);
		} 
		$origine="ARRAY";
		break;
	case 'perso_notices':
		require_once($class_path.'/parametres_perso.class.php');
		$p_perso = new parametres_perso('notices');
		$array_selector=$p_perso->get_ajax_list($persofield,$start);
		$origine='ARRAY';
		break;
	case 'perso_expl':
		require_once($class_path.'/parametres_perso.class.php');
		$p_perso = new parametres_perso('notices');
		$array_selector=$p_perso->get_ajax_list($persofield,$start);
		$origine='ARRAY';
		break;
	case 'perso_empr':
		require_once($class_path.'/parametres_perso.class.php');
		$p_perso = new parametres_perso('notices');
		$array_selector=$p_perso->get_ajax_list($persofield,$start);
		$origine='ARRAY';
		break;
	case 'perso_collstate':
		require_once($class_path.'/parametres_perso.class.php');
		$p_perso = new parametres_perso('notices');
		$array_selector=$p_perso->get_ajax_list($persofield,$start);
		$origine='ARRAY';
		break;
	case "suggestions" :
		$fontsize = "12px";
		require_once($class_path.'/suggest.class.php');
		$suggestion = new suggest($start);
		$array_selector=array();
		if(count($suggestion->arrayResults)){
			foreach($suggestion->arrayResults as $v){
				if(str_word_count($v["field_content"])>10){
					//$array_selector[]=array($v["field_content"]." <small>dans <i>".$v["field_name"]."</i></small>"=>implode(" ",$v["field_content_search"]));
					$array_selector[]=array($v["field_content"]." <small></small>"=>implode(" ",$v["field_content_search"]));
				}else{
					//$array_selector[]=array($v["field_content"]." <small>dans <i>".$v["field_name"]."</i></small>"=>$v["field_clean_content"]);
					$array_selector[]=array($v["field_content"]." <small></small>"=>$v["field_clean_content"]);
				}
			}
		}
		$origine='ARRAY';
		break;
	default: 
		break;
endswitch;

switch ($origine):
	case 'SQL':
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat) > 0) {
			$i=1;
			while($r=@pmb_mysql_fetch_array($resultat)) {
				if($r[2])
					echo "<div id='c".$id."_".$i."' style='display:none' autid='".$r[1]."'>".$r[2]."</div>";
				echo "<div id='l".$id."_".$i."'";
				if ($autfield) echo " autid='".$r[1]."'";
				echo " style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";width:100%'";
				echo " onMouseOver='document.getElementById(\"l".$id."_".$i."\").style.background=\"#000088\";document.getElementById(\"l".$id."_".$i."\").style.color=\"#FFFFFF\";'";
				echo " onMouseOut='document.getElementById(\"l".$id."_".$i."\").style.background=\"#FFFFFF\";document.getElementById(\"l".$id."_".$i."\").style.color=\"\";'";
				echo " onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\");'>".$r[0]."</div>";
				$i++;
			}
		} else {
			echo "<div id='l".$id."_0' style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";width:100%'>".$msg["no_result"]."</div>";
		}
		break;
	case 'TABLEAU':
		if (count($s_func->table) > 0) {
			$i=1;
			while(list($index, $value) = each($s_func->table)) {
				if (strtolower(substr($value,0,strlen($start)))==strtolower($start)) {
					echo "<div id='l".$id."_".$i."'";
					if ($autfield) echo " autid='".$index."'";
					echo " style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";width:100%'";
					echo " onMouseOver='document.getElementById(\"l".$id."_".$i."\").style.background=\"#000088\";document.getElementById(\"l".$id."_".$i."\").style.color=\"#FFFFFF\";'";
					echo " onMouseOut='document.getElementById(\"l".$id."_".$i."\").style.background=\"#FFFFFF\";document.getElementById(\"l".$id."_".$i."\").style.color=\"\";'";
					echo " onClick='ajax_set_datas(\"l".$id."_".$i."\",\"$id\")'>".$value."</div>";
					$i++;
				}
			}
		} else {
			echo "<div id='l".$id."_0' style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";width:100%'>".$msg["no_result"]."</div>";
		}
		break;
	case 'ARRAY':
		if (is_array($array_selector) && count($array_selector)) {
			$i=1;
			while(list($index, $value) = each($array_selector)) {
				$grey=false;
				$prefix=$array_prefix[$index]['libelle'];
				$thesid = $array_prefix[$index]['id'];
				if ($index[0]=="*") { $index=substr($index,1); $grey=true; }
				if ($prefix) {
					echo "<div id='p".$id.$i."' style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";".($prefix?"width:100%":"").($grey?";color:#888":"").";'>";
					echo $prefix." ";
				}
				$lib_liste="";
				if(is_array($value)){
					foreach($value as $k=>$v){
						$lib_liste = $k;
						echo "<div id='c".$id."_".$i."' style='display:none' thesid='".$thesid."' autid='".$index."'>$v</div>";
					}
				} else $lib_liste=$value;
				echo " <".($prefix?"span":"div")." id='l".$id."_".$i."'";
				if ($autfield) echo " autid='".$index."'";
				if ($thesid) echo " thesid='".$thesid."'";
				echo " style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";".(!$prefix?"width:100%":"").($grey?";color:#888":"").";'";
				echo " onMouseOver='document.getElementById(\"l".$id."_".$i."\").style.background=\"#000088\";document.getElementById(\"l".$id."_".$i."\").style.color=\"#FFFFFF\";'";
				echo " onMouseOut='document.getElementById(\"l".$id."_".$i."\").style.background=\"#FFFFFF\";document.getElementById(\"l".$id."_".$i."\").style.color=\"".($grey?"#888":"")."\";'";
				echo " onClick='if(document.getElementById(\"c".$id."_".$i."\")) ajax_set_datas(\"c".$id."_".$i."\",\"$id\"); else ajax_set_datas(\"l".$id."_".$i."\",\"$id\");'>".$lib_liste."</".($prefix?"span":"div").">";
				if ($prefix) echo "</div>";
				$i++;	
			}
		} else {
			echo "<div id='l".$id."_0' style='cursor:default;font-family:arial,helvetica;font-size:".$fontsize.";width:100%;color:red;'>".$msg["no_result"]."</div>";
		}
		break;
	default: 
		break;
endswitch;

