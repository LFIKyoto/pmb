<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_see.inc.php,v 1.100.2.1 2015-10-16 12:25:16 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


require_once ($class_path . "/authority.class.php");
require_once ($class_path . "/categories.class.php");

if ($id) {
	$context = array ();
	$id+= 0;
	
	//recuperation du thesaurus session
	if (!$id_thes) {
		$id_thes = thesaurus::getSessionThesaurusId();
	} else {
		thesaurus::setSessionThesaurusId($id_thes);
	}
	$thes = new thesaurus($id_thes);
	$id_top = $thes->num_noeud_racine;
	
	//FIL D'ARIANNE DANS LE THESAURUS
	$context['authority']['breadcrumb'] = "";
	$ourCateg = new categorie($id);
	// affichage du path de la catégorie
	if ($opac_thesaurus) $thes_lib_to_print = "<a href=\"./index.php?lvl=categ_see&id=".$ourCateg->thes->num_noeud_racine."\">".$ourCateg->thes->libelle_thesaurus."</a>";
	else $thes_lib_to_print = "<a href=\"./index.php?lvl=categ_see&id=".$ourCateg->thes->num_noeud_racine."\"><img src='./images/home.gif' border='0'></a>";
	$context['authority']['breadcrumb'] = $thes_lib_to_print;
	
	$context['authority']['breadcrumb'].= pmb_bidi($ourCateg->categ_path($opac_categories_categ_path_sep,$css));
	
	//SYNONYMES
	$context['authority']['synonyms'] = array();
	$synonymes=categories::listSynonymes($id, $lang);
	while($row = pmb_mysql_fetch_object($synonymes)){
		$context['authority']['synonyms'][] =$row->libelle_categorie;
	}
	
	//VOIR
	if($ourCateg->voir){
		$context['authority']['voir'] = new categories($ourCateg->voir, $lang);
	}
	
	//VOIR AUSSI
	$context['authority']['see_also'] = array();
	$q = "select ";
	$q.= "distinct catdef.num_noeud,catdef.note_application, catdef.comment_public,";
	$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as libelle_categorie ";
	$q.= "from voir_aussi left join noeuds on noeuds.id_noeud=voir_aussi.num_noeud_dest ";
	$q.= "left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
	$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
	$q.= "where ";
	$q.= "voir_aussi.num_noeud_orig = '".$id."' ";
	$q.= "order by libelle_categorie limit ".$opac_categories_max_display;
	
	$found_see_too = pmb_mysql_query($q, $dbh);
	$see_also="";
	if (pmb_mysql_num_rows($found_see_too)) {
		$deb = 0 ;
		while (($mesCategories_see_too = pmb_mysql_fetch_object($found_see_too))) {
			$mesCategories_see_too->zoom  = categorie::zoom_categ($mesCategories_see_too->num_noeud, $mesCategories_see_too->comment_public);
			$mesCategories_see_too->has_notice = category::has_notices($mesCategories_see_too->num_noeud);
			$context['authority']['see_also'][] = $mesCategories_see_too;
		}
	}
	
	//LISTE DES NOTICES ASSOCIEES
	//Lire le champ path du noeud pour étendre la recherche éventuellement au fils et aux père de la catégorie
	// lien Etendre auto_postage
	if (!isset($nb_level_enfants)) {
		// non defini, prise des valeurs par défaut
		if (isset($_SESSION["nb_level_enfants"]) && $opac_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
		else $nb_level_descendant=$opac_auto_postage_nb_descendant;
	} else {
		$nb_level_descendant=$nb_level_enfants;
	}
	
	// lien Etendre auto_postage
	if(!isset($nb_level_parents)) {
		// non defini, prise des valeurs par défaut
		if(isset($_SESSION["nb_level_parents"]) && $opac_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
		else $nb_level_montant=$opac_auto_postage_nb_montant;
	} else {
		$nb_level_montant=$nb_level_parents;
	}
	
	$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
	$_SESSION["nb_level_parents"]=	$nb_level_montant;
	
	$q = "select path from noeuds where id_noeud = '".$id."' ";
	$r = pmb_mysql_query($q, $dbh);
	if($r && pmb_mysql_num_rows($r)){
		$path=pmb_mysql_result($r, 0, 0);
		$nb_pere=substr_count($path,'/');
	}else{
		$path="";
		$nb_pere=0;
	}
	
	$acces_j='';
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notcateg_notice');
	}
	
	if($acces_j) {
		$statut_j='';
		$statut_r='';
	} else {
		$statut_j=',notice_statut';
		$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	}
	
	if($_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
		$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
		$statut_r.=" and ".$opac_view_restrict;
	}
	
	// Si un path est renseigné et le paramètrage activé
	if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
	
		//Recherche des fils
		if(($opac_auto_postage_descendant || $opac_auto_postage_etendre_recherche)&& $nb_level_descendant) {
			if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
				$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
			else
				//$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
				$liste_fils=" path like '$path/%' or  path = '$path' ";
		} else {
			$liste_fils=" id_noeud='".$id."' ";
		}
	
		// recherche des pères
		if(($opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && $nb_level_montant ) {
				
			$id_list_pere=explode('/',$path);
			$stop_pere=0;
			if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere=$nb_pere-$nb_level_montant;
			if($stop_pere<0) $stop_pere=0;
			for($i=$nb_pere;$i>=$stop_pere; $i--) {
				$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
			}
		}
		$suite_req = " FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id $acces_j $statut_j ";
		$suite_req.= "WHERE ($liste_fils $liste_pere) $statut_r ";
	
	} else {
		// cas normal d'avant
		$suite_req = " FROM notices_categories join notices on notcateg_notice=notice_id $acces_j $statut_j ";
		$suite_req.= "WHERE num_noeud='".$id."' $statut_r ";
	}
	if ($path) {
		if ($opac_auto_postage_etendre_recherche == 1 || ($opac_auto_postage_etendre_recherche == 2 && !$nb_pere)) {	
			$input_txt="<input name='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_enfants='+this.value\">";
			$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_enfants"]);
		} elseif ($opac_auto_postage_etendre_recherche == 2 && $nb_pere) {
			$input_txt="<input name='nb_level_enfants' id='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_enfants='+this.value+'&nb_level_parents='+document.getElementById('nb_level_parents').value;\">";
			$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_parents_enfants"]);
	
			$input_txt="<input name='nb_level_parents' id='nb_level_parents' type='text' size='2' value='$nb_level_montant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_parents='+this.value+'&nb_level_enfants='+document.getElementById('nb_level_enfants').value;\">";
			$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$auto_postage_form);
	
		} elseif ($opac_auto_postage_etendre_recherche == 3 ) {
			if($nb_pere) {
				$input_txt="<input name='nb_level_parents' type='text' size='2' value='$nb_level_montant'
				onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_parents='+this.value\">";
				$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$msg["categories_autopostage_parents"]);
			}
		}
	}
	
	// comptage des notices associées
	if (!$nbr_lignes) {
		$requete = "SELECT count(distinct notice_id) ".$suite_req;
		$res = pmb_mysql_query($requete, $dbh);
	
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
	
		//Recherche des types doc
		$requete="select distinct typdoc ";
		if($opac_visionneuse_allow){
			$requete.= ",count(explnum_id) as nbexplnum ";
			if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
				$suite_req_type_doc_noti = "FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j $statut_j ";
				$suite_req_type_doc_bull = "FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j $statut_j ";
				$suite_req_type_doc= "WHERE ($liste_fils $liste_pere) $statut_r group by typdoc";
			}else {
				$suite_req_type_doc_noti = "FROM notices_categories join notices on notcateg_notice=notice_id left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j $statut_j ";
				$suite_req_type_doc_bull = "FROM notices_categories join notices on notcateg_notice=notice_id left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j $statut_j ";
				$suite_req_type_doc= "WHERE num_noeud='".$id."' $statut_r  group by typdoc";
			}
						
			$requete_noti = $requete.$suite_req_type_doc_noti.$suite_req_type_doc;
			$requete_bull = $requete.$suite_req_type_doc_bull.$suite_req_type_doc;
			$requete = "select distinct uni.typdoc, sum(nbexplnum) as nbexplnum from (($requete_noti) union ($requete_bull)) as uni group by typdoc";
		}else{
			$requete .= $suite_req;
		}
		$res = pmb_mysql_query($requete, $dbh);
		$t_typdoc=array();
		$nbexplnum_to_photo=0;
		if ($res) {
			while (($tpd=pmb_mysql_fetch_object($res))) {
				$t_typdoc[]=$tpd->typdoc;
				if($opac_visionneuse_allow)
					$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
		$l_typdoc=implode(",",$t_typdoc);
	}else if($opac_visionneuse_allow){
		$requete="select count(explnum_id) as nbexplnum ";
		if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
			$suite_req_type_doc_noti = "FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j $statut_j ";
			$suite_req_type_doc_bull = "FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j $statut_j ";
			$suite_req_type_doc= "WHERE ($liste_fils $liste_pere) $statut_r";
		}else {
			$suite_req_type_doc_noti = "FROM notices_categories join notices on notcateg_notice=notice_id left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j $statut_j ";
			$suite_req_type_doc_bull = "FROM notices_categories join notices on notcateg_notice=notice_id left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j $statut_j ";
			$suite_req_type_doc= "WHERE num_noeud='".$id."' $statut_r";
		}
							
		$requete_noti = $requete.$suite_req_type_doc_noti.$suite_req_type_doc;
		$requete_bull = $requete.$suite_req_type_doc_bull.$suite_req_type_doc;
		$requete = "select sum(nbexplnum) as nbexplnum from (($requete_noti) union ($requete_bull)) as uni";

		$res = pmb_mysql_query($requete, $dbh);
		$nbexplnum_to_photo=0;
		if ($res) {
			while (($tpd=pmb_mysql_fetch_object($res))) {
				$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
	}

	// pour la DSI
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ($_SESSION['abon_cree_bannette_priv']==1 || $opac_allow_bannette_priv==2)) {
		$recordslist= "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
						// Ouverture du div resultatrech_liste
						$recordslist.= "<div id='resultatrech_liste'>";
	
						if (!$page) $page=1;
						$debut =($page-1)*$opac_nb_aut_rec_per_page;
						$recordslist.= pmb_bidi(str_replace("!!categ_libelle!!",$ourCateg->libelle,$categ_notices));
						if ($nbr_lignes) {
							if ($opac_notices_depliable) $recordslist.= $begin_result_liste;
	
							//gestion du tri
							if (isset($_GET["sort"])) {
								$_SESSION["last_sortnotices"]=$_GET["sort"];
							}
							if ($nbr_lignes>$opac_nb_max_tri) {
								$_SESSION["last_sortnotices"]="";
								$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;</span>";
							} else {
								$pos=strpos($_SERVER['REQUEST_URI'],"?");
								$pos1=strpos($_SERVER['REQUEST_URI'],"get");
								if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
								else $pos1=$pos1-3;
								$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
								$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
								$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
								$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
								$recordslist.= $affich_tris_result_liste;
								if ($_SESSION["last_sortnotices"]!="") {
									$sort = new sort('notices','session');
									$recordslist.= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
								}
							}
							//fin gestion du tri
	
							$recordslist.= $add_cart_link;
	
							if($opac_visionneuse_allow && $nbexplnum_to_photo){
								$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
								$sendToVisionneuseByGet = str_replace("!!mode!!","categ_see",$sendToVisionneuseByGet);
								$sendToVisionneuseByGet = str_replace("!!idautorite!!",$id,$sendToVisionneuseByGet);
								$recordslist.= $sendToVisionneuseByGet;
							}
	
							if ($opac_show_suggest) {
								$bt_sugg = "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";
								if ($opac_resa_popup) $bt_sugg .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
								else $bt_sugg .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";
								$bt_sugg.= " title='".$msg["empr_bt_make_sugg"]."' >".$msg[empr_bt_make_sugg]."</a></span>";
								$recordslist.= $bt_sugg;
							}
	
							rec_last_authorities();
							//affinage
							if ($main) {
								// Gestion des alertes à partir de la recherche simple
								include_once($include_path."/alert_see.inc.php");
								$recordslist.= $alert_see_mc_values;
									
								//affichage
								$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
								//Etendre
								if ($opac_allow_external_search) $recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_module&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
								//fin etendre
							} else {
								// Gestion des alertes à partir de la recherche simple
								include_once($include_path."/alert_see.inc.php");
								$recordslist.= $alert_see_mc_values;
	
								//affichage
								$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_".($from=="search" ? "simple_search" : "module")."' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
								//Etendre
								if ($opac_allow_external_search) $recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
									
								//fin etendre
							}
							//fin affinage
							if ($auto_postage_form) $recordslist.= "<div id='autopostageform'>".$auto_postage_form."</div>";
							$recordslist.= "<blockquote>\n";
							// on lance la vraie requête
							$requete = "SELECT distinct notices.notice_id $suite_req";
	
							//gestion du tri
							if ($_SESSION["last_sortnotices"]!="") {
								$requete = $sort->appliquer_tri($_SESSION["last_sortnotices"], $requete, "notice_id", $debut, $opac_nb_aut_rec_per_page);
							} else {
								$sort=new sort('notices','session');
								$requete = $sort->appliquer_tri("default", $requete, "notice_id", $debut, $opac_nb_aut_rec_per_page);
							}
							//fin gestion du tri
							$res = @pmb_mysql_query($requete, $dbh);
							$recordslist.= aff_notice(-1);
							$nb=0;
							$recherche_ajax_mode=0;
							while (($obj=pmb_mysql_fetch_object($res))) {
								global $infos_notice;
								if($nb++>4) $recherche_ajax_mode=1;
								$recordslist.= pmb_bidi(aff_notice($obj->notice_id, 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
								$infos_notice['nb_pages'] = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
							}
							$recordslist.= aff_notice(-2);
							pmb_mysql_free_result($res);
	
							// constitution des liens
							$nbepages = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
							$recordslist.= "</blockquote>\n";							
// 							$recordslist.= "</div><!-- fermeture aut_details_liste -->\n";
							$recordslist.= "<div id='navbar'><hr />\n<center>".printnavbar($page, $nbepages, "./index.php?lvl=categ_see&id=$id&page=!!page!!&nbr_lignes=$nbr_lignes&main=$main&l_typdoc=".rawurlencode($l_typdoc))."</center></div>";
	
						} else {
							$recordslist.= $msg['categ_empty'];
							if($auto_postage_form) $recordslist.= "<br />".$auto_postage_form;
						}
						$recordslist.= "</div>"; // Fermeture du div resultatrech_liste
 	$context ['authority'] ['recordslist'] = $recordslist;
	
	$authority = new authority ( "category", $id );
	$authority->render ( $context );
	
	//FACETTES
	//gestion des facette si active
	require_once($base_path.'/classes/facette_search.class.php');
	$records = "";
	if($nbr_lignes){
		$facettes_result = pmb_mysql_query("SELECT distinct notices.notice_id $suite_req",$dbh);
		while($row = pmb_mysql_fetch_object($facettes_result)){
			if($records){
				$records.=",";
			}
			$records.= $row->notice_id;
		}
		
		if(!$opac_facettes_ajax){
			$str .= facettes::make_facette($records);
		}else{
			$_SESSION['tab_result']=$records;
			$str .=facettes::get_facette_wrapper();
			$str .="<div id='facette_wrapper'><img src='./images/patience.gif'/></div>";
		
			$str .="
			<script type='text/javascript'>
				var req = new http_request();
				req.request(\"./ajax.php?module=ajax&categ=facette&sub=call_facettes\",false,null,true,function(data){
					document.getElementById('facette_wrapper').innerHTML=data;
				});
			</script>";
		}
		//Formulaire "FACTICE" pour l'application du comparateur et du filetre multiple...
		$str.= '
<form name="form_values" style="display:none;" method="post" action="?lvl=more_results&mode=extended">
	<input type="hidden" name="from_see" value="1" />
	'.facette_search_compare::form_write_facette_compare().'
</form>';
	}
	
}else {
	$ourCateg = new categorie(0);
	print pmb_bidi($ourCateg->child_list('./images/folder.gif',$css));
}