<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author_see.inc.php,v 1.76.2.2 2015-10-16 10:55:52 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/authority.class.php");
require_once($base_path.'/includes/templates/author.tpl.php');
require_once($base_path."/includes/explnum.inc.php");

if($id){
	$id+=0;
	
	$rqt_auteurs = "select author_id as aut from authors where author_see='$id' and author_id!=0 ";
	$rqt_auteurs .= "union select author_see as aut from authors where author_id='$id' and author_see!=0 " ;
	$res_auteurs = pmb_mysql_query($rqt_auteurs, $dbh);
	$clause_auteurs = " in ('$id' ";
	while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
		$clause_auteurs .= ", '".$id_aut->aut."' ";
		$rqt_auteursuite = "select author_id as aut from authors where author_see='$id_aut->aut' and author_id!=0 ";
		$res_auteursuite = pmb_mysql_query($rqt_auteursuite, $dbh);
		while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' "; 
	} 
	$clause_auteurs .= " ) " ;

	$renvoi = "";
	// récupération des formes rejetées pour affichage
	$requete = "select distinct author_id as aut from authors where author_id $clause_auteurs and author_id!=$id " ;
	$res = pmb_mysql_query($requete, $dbh);
	while (($obj=pmb_mysql_fetch_object($res))) {
		$objRenvoi = new auteur($obj->aut);
		pmb_strlen($renvoi) ? $renvoi .= ', ('.$objRenvoi->isbd_entry.")" : $renvoi = $objRenvoi->isbd_entry;
	}
	
	//récupération de la liste...
	// affichage des notices associées
	
	$records_list = "<h3><span class=\"aut_details_liste_titre\">";
	if($ourAuteur->type == 72) {
		//Congrès
		$records_list.= pmb_bidi($msg[documents_disponibles_meme_congres]);
	} else {
		$records_list.= pmb_bidi($msg[documents_disponibles_meme_auteur]);
	}
	$records_list.= "</span></h3>\n";
	
	//droits d'acces emprunteur/notice
	$acces_j='';
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
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
	// comptage des notices associées
	if(!$nbr_lignes) {
	
		$requete = "SELECT count(distinct notices.notice_id) FROM notices $acces_j, responsability $statut_j ";
		$requete.= "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r ";
		$res = pmb_mysql_query($requete,$dbh);
		if ($res) $nbr_lignes = pmb_mysql_result($res,0,0); else $nbr_lignes=0;
	
		//Recherche des types doc
		$requete = "select distinct notices.typdoc FROM notices $acces_j, responsability $statut_j  ";
		$requete.= "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r group by notices.typdoc";
		if ($opac_visionneuse_allow){
			$requete_noti = "select distinct notices.typdoc, count(explnum_id) as nbexplnum FROM notices left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j, responsability $statut_j  ";
			$requete_bull = "select distinct notices.typdoc, count(explnum_id) as nbexplnum FROM notices left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j, responsability $statut_j  ";
			$where = "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r group by notices.typdoc";
			$requete = "select distinct uni.typdoc, sum(nbexplnum) as nbexplnum from ($requete_noti $where union $requete_bull $where) as uni group by typdoc";
		}
	
		$res = pmb_mysql_query($requete, $dbh);
		$t_typdoc=array();
		$nbexplnum_to_photo=0;
		if ($res) {
			while ($tpd=pmb_mysql_fetch_object($res)) {
				$t_typdoc[]=$tpd->typdoc;
				if ($opac_visionneuse_allow)
					$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
		$l_typdoc=implode(",",$t_typdoc);
	}else if($opac_visionneuse_allow){
		$requete_noti = "select distinct notices.typdoc, count(explnum_id) as nbexplnum FROM notices left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id $acces_j, responsability $statut_j  ";
		$requete_bull = "select distinct notices.typdoc, count(explnum_id) as nbexplnum FROM notices left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j, responsability $statut_j  ";
		$where = "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r group by notices.typdoc";
		$requete = "select distinct uni.typdoc, sum(nbexplnum) as nbexplnum from ($requete_noti $where union $requete_bull $where) as uni group by typdoc";
		$res = pmb_mysql_query($requete, $dbh);
		$nbexplnum_to_photo=0;
		if ($res) {
			while ($tpd=pmb_mysql_fetch_object($res)) {
				$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
	}
	
	// pour la DSI
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ($_SESSION['abon_cree_bannette_priv']==1 || $opac_allow_bannette_priv==2)) {
		$records_list.= "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
	// Ouverture du div resultatrech_liste
	$records_list.= "<div id='resultatrech_liste'>";
	
	if(!$page) $page=1;
	$debut =($page-1)*$opac_nb_aut_rec_per_page;
	
	if($nbr_lignes) {
		if ($opac_notices_depliable) $records_list.= $begin_result_liste;
	
		//gestion du tri
	
		//si on dépasse le nombre de lignes maxi à trier
		if ($nbr_lignes>$opac_nb_max_tri) {
			//on annule tri selectionner
			$_SESSION["last_sortnotices"]="";
			$records_list.= "<span class=\"espaceResultSearch\">&nbsp;</span>";
		} else {
			$pos=strpos($_SERVER['REQUEST_URI'],"?");
			$pos1=strpos($_SERVER['REQUEST_URI'],"get");
			if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
			else $pos1=$pos1-3;
			$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
			$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
			$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
			$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
			$records_list.= $affich_tris_result_liste;
	
			//on recupere le tri à appliquer
			if (isset($_GET["sort"])) {
				$_SESSION["last_sortnotices"]=$_GET["sort"];
			}
	
			if ($_SESSION["last_sortnotices"]!="") {
				$sort = new sort('notices','session');
				$records_list.= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			}
		}
		//fin gestion du tri
	
		$records_list.= $add_cart_link;
		if($opac_visionneuse_allow && $nbexplnum_to_photo){
			$records_list.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
			$sendToVisionneuseByGet = str_replace("!!mode!!","author_see",$sendToVisionneuseByGet);
			$sendToVisionneuseByGet = str_replace("!!idautorite!!",$id,$sendToVisionneuseByGet);
			$records_list.= $sendToVisionneuseByGet;
		}
	
		if ($opac_show_suggest) {
			$bt_sugg = "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";
			if ($opac_resa_popup) $bt_sugg .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
			else $bt_sugg .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";
			$bt_sugg.= " title='".$msg["empr_bt_make_sugg"]."' >".$msg[empr_bt_make_sugg]."</a></span>";
			$records_list.= $bt_sugg;
	
		}
	
		//affinage
		//enregistrement de l'endroit actuel dans la session
		rec_last_authorities();
	
		// Gestion des alertes à partir de la recherche simple
		include_once($include_path."/alert_see.inc.php");
		$records_list.= $alert_see_mc_values;
	
			//affichage
		$records_list.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_".($from == "search" ? "simple_search" : "module")."' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
		//fin affinage
		//Etendre
		if ($opac_allow_external_search) $records_list.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
		//fin etendre
	
		$records_list.= "<blockquote>\n";
		$records_list.= aff_notice(-1);
	
			// on lance la vraie requête
		$requete = "SELECT distinct notices.notice_id FROM notices $acces_j, responsability $statut_j ";
		$requete.= "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r ";
	
		//gestion du tri
		if ($_SESSION["last_sortnotices"]!="") {
			$requete = $sort->appliquer_tri($_SESSION["last_sortnotices"], $requete, "notice_id", $debut, $opac_nb_aut_rec_per_page);
		} else {
		$requete.= " ORDER BY index_serie,tnvol,index_sew";
			$requete .= " LIMIT $debut,$opac_nb_aut_rec_per_page ";
		}
		//fin gestion du tri
		$nb=0;
		$recherche_ajax_mode=0;
		$res = pmb_mysql_query($requete, $dbh);
		while(($obj=pmb_mysql_fetch_object($res))) {
		global $infos_notice;
			if($nb++>4) $recherche_ajax_mode=1;
			$records_list.= pmb_bidi(aff_notice($obj->notice_id, 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
			$infos_notice['nb_pages'] = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
		}
		pmb_mysql_free_result($res);
	
			// constitution des liens pur affichage de la barre de navigation
			$nbepages = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
			$records_list.= aff_notice(-2);
			$records_list.= "</blockquote>\n";
// 			$records_list.= "</div><!-- fermeture du div aut_details_liste -->\n";
		$records_list.= "<div id='navbar'><hr /><center>".printnavbar($page, $nbepages, "./index.php?lvl=author_see&id=$id&page=!!page!!&nbr_lignes=$nbr_lignes&l_typdoc=".rawurlencode($l_typdoc))."</center></div>\n";
	
	} else {
		$records_list.= $msg[no_document_found];
// 		$records_list.= "</div><!-- fermeture du div aut_details_liste -->\n";
	}
	$records_list.= "</div>"; // Fermeture du div resultatrech_liste
	
	
	

	// Documents numériques auxquels l'auteur est associé
	$query = "select distinct explnum_speaker_explnum_num from explnum_speakers where explnum_speaker_author ".$clause_auteurs;
	$result = pmb_mysql_query($query, $dbh);
	$docnum_associate = "";
	if ($result && pmb_mysql_num_rows($result)) {
		$docnum_associate = pmb_bidi("<h3>".$msg['author_see_explnum_associate']."</h3>\n");
		while ($explnum = pmb_mysql_fetch_object($result)) {
			$docnum_associate.= "<div>".show_explnum_per_id($explnum->explnum_speaker_explnum_num)."</div>";
		}
	}	
	
	//composition du contexte, puis envoi des données au template Django
	$context = array();
	$context['authority']["author_see"] = $renvoi;
	$context['authority']["recordslist"] = $records_list;
	$context['authority']["docnum_associate"] = $docnum_associate;
	
	$authority = new authority("author", $id);
	$authority->render($context);
	
	//FACETTES
	$records = "";
	if($nbr_lignes){
		require_once($base_path.'/classes/facette_search.class.php');
		$requete = "SELECT distinct notices.notice_id FROM notices $acces_j, responsability $statut_j ";
		$requete.= "where responsability_author $clause_auteurs and notice_id=responsability_notice $statut_r ";
		$facettes_result = pmb_mysql_query($requete,$dbh);
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
}