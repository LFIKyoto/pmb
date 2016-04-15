<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_info.php,v 1.64.2.6 2015-11-03 12:36:27 jpermanne Exp $

//Actions et affichage du résultat pour un panier de l'opac
$base_path=".";
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
	
// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

require_once($base_path."/classes/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/filter_results.class.php");

// si paramétrage authentification particulière et pour le re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	$current_opac_view=$_SESSION["opac_view"];
	if($opac_view==-1){
		$_SESSION["opac_view"]="default_opac";
	}else if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view*1;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");

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

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

?>
<html>
<body class="cart_info_body">
<span id='cart_info_iframe_content'>
<?php

function add_query($requete) {
	global $cart_;
	global $opac_max_cart_items;
	global $msg;
	global $charset;
	
	$resultat=pmb_mysql_query($requete);
	$nbtotal=@pmb_mysql_num_rows($resultat);
	$n=0; $na=0;
	while ($r=pmb_mysql_fetch_object($resultat)) {
		if (count($cart_)<$opac_max_cart_items) {
			$as=array_search($r->notice_id,$cart_);
			if (($as===null)||($as===false)) {
				$cart_[]=$r->notice_id;
				$n++;	
			} else $na++;
		}
	}
	$message=sprintf($msg["cart_add_notices"],$n,$nbtotal);
	if ($na) $message.=", ".sprintf($msg["cart_already_in"],$na);
	if (count($cart_)==$opac_max_cart_items) $message.=", ".$msg["cart_full"];
	return $message;
}

function change_basket_image($id_notice){
	print "<script>changeBasketImage(".$id_notice.")</script>";
}

function add_notices_to_cart($notices){
	global $cart_;
	global $opac_max_cart_items;
	global $msg;

	$n=0; $na=0;
	$tab_notices = explode(",",$notices);
	$nbtotal=count($tab_notices);
	for($i=0 ; $i<count($tab_notices) ; $i++){
		if (count($cart_)<$opac_max_cart_items) {
			$as=array_search($tab_notices[$i],$cart_);
			if (($as===null)||($as===false)) {
				$cart_[]=$tab_notices[$i];
				change_basket_image($tab_notices[$i]);
				$n++;	
			} else $na++;
		}	
	}
	$message=sprintf($msg["cart_add_notices"],$n,$nbtotal);
	if ($na) $message.=", ".sprintf($msg["cart_already_in"],$na);
	if (count($cart_)==$opac_max_cart_items) $message.=", ".$msg["cart_full"];
	return $message;	
}

print "<script type='text/javascript'>
			var msg_notice_title_basket_exist = '".addslashes($msg["notice_title_basket_exist"])."';
		</script>";
print "<script type='text/javascript' src='".$include_path."/javascript/cart.js'></script>";
$vide_cache=filemtime("./styles/".$css."/".$css.".css");
print "<link rel=\"stylesheet\" href=\"./styles/".$css."/".$css.".css?".$vide_cache."\" />
<span class='img_basket'><img src='".get_url_icon("basket_small_20x20.png")."' border='0' valign='center'/></span>&nbsp;";
$cart_=$_SESSION["cart"];
if (!count($cart_)) $cart_=array();

$id=stripslashes($id);// attention id peut etre du type es123 (recherche externe)
$location+=0;

if (($id)&&(!$lvl)) {
	if (count($cart_)<$opac_max_cart_items) {
		$as=array_search($id,$cart_);
		$notice_header=htmlentities(substr(strip_tags(stripslashes(html_entity_decode($header,ENT_QUOTES))),0,45),ENT_QUOTES,$charset);
		if ($notice_header!=$header) $notice_header.="...";
		if (($as!==null)&&($as!==false)) {
			$message=sprintf($msg["cart_notice_exists"],$notice_header);
		} else {
			$cart_[]=$id;
			$message=sprintf($msg["cart_notice_add"],$notice_header);
			change_basket_image($id);
		}
	} else {
		$message=$msg["cart_full"];
	}
} else if ($lvl) {
	switch ($lvl) {
		case "more_results":
			//changement de plan !
			switch ($mode) {
				case "tous" :
					$searcher = new searcher_all_fields(stripslashes($user_query));
					if(!empty($_SESSION["last_sortnotices"])){
						$cart_sort=$_SESSION["last_sortnotices"];
					}else{
						$cart_sort="default";
					}
					$notices = $searcher->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
					if(count($notices)){
						$notices = implode(",",$notices);
					}
					add_notices_to_cart($notices);
					break;
				case "title":	
				case "titre":
					$searcher = new searcher_title(stripslashes($user_query));
					if(!empty($_SESSION["last_sortnotices"])){
						$cart_sort=$_SESSION["last_sortnotices"];
					}else{
						$cart_sort="default";
					}
					$notices = $searcher->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
					if(count($notices)){
						$notices = implode(",",$notices);
					}
					add_notices_to_cart($notices);
					break;
				case "keyword":
					$searcher = new searcher_keywords(stripslashes($user_query));
					if(!empty($_SESSION["last_sortnotices"])){
						$cart_sort=$_SESSION["last_sortnotices"];
					}else{
						$cart_sort="default";
					}
					$notices = $searcher->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
					if(count($notices)){
						$notices = implode(",",$notices);
					}
					add_notices_to_cart($notices);
					break;
				case "abstract":
					$searcher = new searcher_abstract(stripslashes($user_query));
					if(!empty($_SESSION["last_sortnotices"])){
						$cart_sort=$_SESSION["last_sortnotices"];
					}else{
						$cart_sort="default";
					}
					$notices = $searcher->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
					if(count($notices)){
						$notices = implode(",",$notices);
					}
					add_notices_to_cart($notices);
					break;
				case "extended":
					$searcher = new searcher_extended(stripslashes($user_query));
					if(!empty($_SESSION["last_sortnotices"])){
						$cart_sort=$_SESSION["last_sortnotices"];
					}else{
						$cart_sort="default";
					}
					$notices = $searcher->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
					if(count($notices)){
						$notices = implode(",",$notices);
					}
					add_notices_to_cart($notices);
					break;
				case "external":
					if ($_SESSION["external_type"]=="multi") $es=new search("search_fields_unimarc"); else $es=new search("search_simple_fields_unimarc");
					$table=$es->make_search();
					$requete="select concat('es', notice_id) as notice_id from $table where 1;";
					$message=add_query($requete);
					break;
				case 'docnum' :
					$notices = '';
					//droits d'acces emprunteur/notice
					$acces_j='';
					if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
						require_once("$class_path/acces.class.php");
						$ac= new acces();
						$dom_2= $ac->setDomain(2);
						$acces_j= $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
					} 				
					if ($acces_j) {
						$statut_j='';
					} else {
						$statut_j=',notice_statut';
					}
					$q_noti = "select notice_id from explnum, notices $statut_j $acces_j ".stripslashes($clause).' '; 
					$q_bull  = "select notice_id from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($clause_bull).' '; 
					$q_bull_num_notice  = "select notice_id from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($clause_bull_num_notice).' '; 
					$q = "select uni.notice_id from ($q_noti UNION $q_bull UNION $q_bull_num_notice) as uni"; 					
					$res = pmb_mysql_query($q,$dbh);	
					if(pmb_mysql_num_rows($res)){
						while ($row = pmb_mysql_fetch_object($res)){						
							if ($notices != "") $notices.= ",";
							$notices.= $row->notice_id;
						}						
					}
					add_notices_to_cart($notices);
					break;
			}
			break;
		case "author_see":
			$notices = '';
			$rqt_auteurs = "select author_id as aut from authors where author_see='$id' and author_id!=0 union select author_see as aut from authors where author_id='$id' and author_see!=0 " ;
			$res_auteurs = pmb_mysql_query($rqt_auteurs, $dbh);
			$clause_auteurs = "responsability_author in ('$id'";
			while($id_aut=pmb_mysql_fetch_object($res_auteurs)) {
				$clause_auteurs .= ",'".$id_aut->aut."'"; 
			}
			$clause_auteurs .= ")" ;
			$q = "select distinct responsability_notice as notice_id from responsability where $clause_auteurs ";
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)) {
				$tab_notices=array();
				while($row=pmb_mysql_fetch_object($res)) {
					$tab_notices[] = $row->notice_id;
				}
				$notices = implode(',',$tab_notices);
				$fr = new filter_results($notices);
				$notices = $fr->get_results();
			}
			add_notices_to_cart($notices);
			break;
		case "categ_see":
			$notices = '';
			$q = "select notcateg_notice from notices_categories where num_noeud='$id' ";
			$res = pmb_mysql_query($q,$dbh);	
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notcateg_notice;
				}		
				$fr = new filter_results($notices);
				$notices = $fr->get_results();				
			}
			add_notices_to_cart($notices);
			break;
		case "indexint_see":
			$notices = '';
			$q = "select notice_id from notices where indexint='$id' " ;			
			$res = pmb_mysql_query($q,$dbh);	
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notice_id;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "coll_see":
			$notices = '';
			$q = "select notice_id from notices where coll_id='$id' " ;
			$res = pmb_mysql_query($q,$dbh);	
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notice_id;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "publisher_see":
			$notices = '';
			$q = "select distinct notice_id from notices where (ed1_id='$id' or ed2_id='$id')" ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)) {
				$tab_notices=array();
				while ($row= pmb_mysql_fetch_object($res)) {
					$tab_notices[]=$row->notice_id;
				}
				$notices = implode(',',$tab_notices);
				$fr = new filter_results($notices);
				$notices = $fr->get_results();
			}
			add_notices_to_cart($notices);
			break;
		case "serie_see":
			$notices = '';
			$q = "select distinct notice_id from notices where tparent_id='$id' " ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notice_id;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "subcoll_see":
			$notices = '';
			$q = "select distinct notice_id from notices where subcoll_id='$id' " ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notice_id;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "etagere_see":
			$notices = '';
			$q = "select distinct object_id from caddie_content join etagere_caddie on caddie_content.caddie_id=etagere_caddie.caddie_id where etagere_id='$id'";
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->object_id;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "dsi":
			$notices = '';
			$q = "select distinct num_notice from bannette_contenu where num_bannette='$id' " ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->num_notice;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;
		case "analysis":
			$notices='';
			$q = "select distinct analysis_notice from analysis where analysis_bulletin='$id' " ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->analysis_notice;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			break;	
		case "listlecture":
			$notices='';
			$q = "select notices_associees from opac_liste_lecture where id_liste=$id" ;
			$res = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){						
					if ($notices != "") $notices.= ",";
					$notices.= $row->notices_associees;
				}				
				$fr = new filter_results($notices);
				$notices = $fr->get_results();		
			}
			add_notices_to_cart($notices);
			
			if($sub == "consult")
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=consultation&id_liste=$id\";top.document.liste_lecture.target=\"\"</script>";
			else
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=view&id_liste=$id\";top.document.liste_lecture.target=\"\"</script>";
			break;		
		case "section_see":
			//On regarde dans quelle type de navigation on se trouve
			$requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$location."' AND num_section='".$id."' ";
			$res=pmb_mysql_query($requete);
			$type_aff_navigopac=0;
			if(pmb_mysql_num_rows($res)){
				$type_aff_navigopac=pmb_mysql_result($res,0,0);
			}

			if($type_aff_navigopac == 0 or ($type_aff_navigopac == -1 && !$plettreaut)or ($type_aff_navigopac != -1 && $type_aff_navigopac != 0 && !isset($dcote) && !isset($nc))){
				//Pas de navigation ou navigation par les auteurs mais sans choix effectué
				$requete="create temporary table temp_n_id ENGINE=MyISAM ( select distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
				$requete="INSERT INTO temp_n_id (select distinct bulletin_notice as notice_id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
				$requete = "SELECT notice_id FROM temp_n_id ";				
				
			}elseif($type_aff_navigopac == -1 ){
				
				$requete="create temporary table temp_n_id ENGINE=MyISAM ( SELECT distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
				$requete="INSERT INTO temp_n_id (select distinct bulletin_notice as notice_id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				
				if($plettreaut == "num"){
					$requete = "SELECT temp_n_id.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[0-9]' GROUP BY temp_n_id.notice_id";
				}elseif($plettreaut == "vide"){
					$requete = "SELECT temp_n_id.notice_id FROM temp_n_id LEFT JOIN responsability ON responsability_notice=temp_n_id.notice_id WHERE responsability_author IS NULL GROUP BY temp_n_id.notice_id";
				}else{
					$requete = "SELECT temp_n_id.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[".$plettreaut."]' GROUP BY temp_n_id.notice_id";
				}
				
			}else{
				
				//Navigation par plan de classement
				
				//Table temporaire de tous les id
				if ($ssub) {
					$t_dcote=explode(",",$dcote);
					$t_expl_cote_cond=array();
					for ($i=0; $i<count($t_dcote); $i++) {
						$t_expl_cote_cond[]="expl_cote regexp '(^".$t_dcote[$i]." )|(^".$t_dcote[$i]."[0-9])|(^".$t_dcote[$i]."$)|(^".$t_dcote[$i].".)'";
					}
					$expl_cote_cond="(".implode(" or ",$t_expl_cote_cond).")";
				}else{
					$expl_cote_cond= " expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
				}	
				$requete="create temporary table temp_n_id ENGINE=MyISAM select distinct expl_notice as notice_id from exemplaires where expl_location=$location and expl_section='$id' " ;
				if (strlen($dcote)) {
					$requete.= " and $expl_cote_cond ";
					$level_ref=strlen($dcote)+1;
				}
				@pmb_mysql_query($requete);

				$requete2 = "insert into temp_n_id (SELECT distinct bulletin_notice as notice_id FROM bulletins join exemplaires on expl_bulletin=bulletin_id where expl_location=$location and expl_section=$id ";
				if (strlen($dcote)) {
					$requete2.= " and $expl_cote_cond ";
				}			
				$requete2.= ") ";
				@pmb_mysql_query($requete2);
				@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
				
				//Calcul du classement
				$rq1_index="create temporary table union1 ENGINE=MyISAM (select distinct expl_cote from exemplaires, temp_n_id where expl_location=$location and expl_section=$id and expl_notice=temp_n_id.notice_id) ";
				$res1_index=pmb_mysql_query($rq1_index);
				$rq2_index="create temporary table union2 ENGINE=MyISAM (select distinct expl_cote from exemplaires, temp_n_id, bulletins where expl_location=$location and expl_section=$id and bulletin_notice=temp_n_id.notice_id and expl_bulletin=bulletin_id) ";
				$res2_index=pmb_mysql_query($rq2_index);			
				$req_index="select distinct expl_cote from union1 union select distinct expl_cote from union2";
				$res_index=pmb_mysql_query($req_index);
		
				if ($level_ref==0) $level_ref=1;
				
				while (($ct=pmb_mysql_fetch_object($res_index)) && $nc) {
					if (preg_match("/[0-9][0-9][0-9]/",$ct->expl_cote,$c)) {
						$found=false;
						$lcote=(strlen($c[0])>=3) ? 3 : strlen($c[0]);
						$level=$level_ref;
						while ((!$found)&&($level<=$lcote)) {
							$cote=substr($c[0],0,$level);
							$compl=str_repeat("0",$lcote-$level);
							$rq_index="select indexint_name,indexint_comment from indexint where indexint_name='".$cote.$compl."' and length(indexint_name)>=$lcote and num_pclass='".$type_aff_navigopac."' order by indexint_name limit 1";
							$res_index_1=pmb_mysql_query($rq_index);
							if (pmb_mysql_num_rows($res_index_1)) {
								$rq_del="select distinct notice_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
								$rq_del.=" union select distinct notice_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
								$res_del=pmb_mysql_query($rq_del) ;
								while (list($n_id)=pmb_mysql_fetch_row($res_del)) {
									pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id);
								}
								$found=true;
							} else $level++;
						}
					}
				}
				$requete = "SELECT notice_id FROM temp_n_id " ;	
			}
			
			$notices='';
			$r =pmb_mysql_query($requete,$dbh);
			if (pmb_mysql_num_rows($r)) {
				$tab_notices=array();
				while($row=pmb_mysql_fetch_object($r)) {
					$tab_notices[]=$row->notice_id;
				}
				$notices=implode(',',$tab_notices);
				$fr = new filter_results($notices);
				$notices = $fr->get_results();
			}
			add_notices_to_cart($notices);
			break;
		case "concept_see":
			require_once($class_path."/skos/skos_concept.class.php");
			$notices = '';
			
			$concept = new skos_concept($id);
			$notices = implode(",", $concept->get_indexed_notices());
			$fr = new filter_results($notices);
			$notices = $fr->get_results();		
			add_notices_to_cart($notices);
			break;
	}
} else $message="";
if(!count($cart_)) echo $msg["cart_empty"]; else echo $message." <a href='#' onClick=\"parent.document.location='index.php?lvl=show_cart'; return false;\">".sprintf($msg["cart_contents"],count($cart_))."</a>";
$_SESSION["cart"]=$cart_;
?>
</span>
</body>
</html>
