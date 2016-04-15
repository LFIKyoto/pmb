<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere_see.inc.php,v 1.43.2.2 2015-12-11 11:14:34 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du contenu d'une étagère

print "<div id='aut_details'>\n";

if ($id) {
	//enregistrement de l'endroit actuel dans la session
	rec_last_authorities();
	//Récupération des infos de l'étagère
	$id+=0;
	$requete="select idetagere,name,comment,id_tri from etagere where idetagere=$id";
	$resultat=pmb_mysql_query($requete);
	$r=pmb_mysql_fetch_object($resultat);
	
	print pmb_bidi("<h3><span>".$r->name."</span></h3>\n");
	print "<div id='aut_details_container'>\n";
	if ($r->comment){
			print "<div id='aut_see'>\n";
			print pmb_bidi("<strong>".$r->comment."</strong><br /><br />");
			print "	</div><!-- fermeture #aut_see -->\n";			
		}

	print "<div id='aut_details_liste'>\n";

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
	//$requete = "select count(distinct object_id) from caddie_content, etagere_caddie, notices, notice_statut where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ";
	//$requete.= " and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	$requete = "select count(distinct object_id) from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$resultat=pmb_mysql_query($requete);
	$nbr_lignes=pmb_mysql_result($resultat,0,0);
	
	//Recherche des types doc
	//$requete="select distinct notices.typdoc FROM caddie_content, etagere_caddie, notices, notice_statut where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ";
	//$requete .= " and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	$requete = "select distinct typdoc FROM caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$res = pmb_mysql_query($requete, $dbh);

	
	$t_typdoc=array();
	if ($res) {
		while ($tpd=pmb_mysql_fetch_object($res)) {
			$t_typdoc[]=$tpd->typdoc;
		}
	}
	$l_typdoc=implode(",",$t_typdoc);

	// pour la DSI
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ($_SESSION['abon_cree_bannette_priv']==1 || $opac_allow_bannette_priv==2)) {
		print "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}

	// Ouverture du div resultatrech_liste
	print "<div id='resultatrech_liste'>";
	
	if(!$page) $page=1;
	$debut =($page-1)*$opac_nb_aut_rec_per_page;
		
	if($nbr_lignes) {
		if ($opac_notices_depliable) print $begin_result_liste;
				
		//gestion du tri
		if(($r->id_tri) && !(isset($_GET["sort"]))) {
			//Le tri est défini en gestion, on l'ajoute aux tris dispos en OPAC si nécessaire
			$res_tri = pmb_mysql_query("SELECT * FROM tris WHERE id_tri=".$r->id_tri);
			if (pmb_mysql_num_rows($res_tri)) {
				$last = "";
				$row_tri = pmb_mysql_fetch_object($res_tri);
				if ($_SESSION["nb_sortnotices"]<=0) {
					$_SESSION["sortnotices".$_SESSION["nb_sortnotices"]]=$row_tri->tri_par;
					if ($row_tri->nom_tri) {
						$_SESSION["sortnamenotices".$_SESSION["nb_sortnotices"]]=$row_tri->nom_tri;
					}
					$last = 0;
					$_SESSION["nb_sortnotices"]++;					
				} else {
					$bool=false;
					for ($i=0;$i<$_SESSION["nb_sortnotices"];$i++) {
						if ($_SESSION["sortnotices".$i] == $row_tri->tri_par) {
							$bool=true;
							$last = $i;
						}
					}
					if (!$bool) {
						$_SESSION["sortnotices".$_SESSION["nb_sortnotices"]] = $row_tri->tri_par;
						if ($row_tri->nom_tri) {
							$_SESSION["sortnamenotices".$_SESSION["nb_sortnotices"]] = $row_tri->nom_tri;
						}
						$last = $_SESSION["nb_sortnotices"];
						$_SESSION["nb_sortnotices"]++;						
					}
				}
				$_SESSION["last_sortnotices"]="$last";
			}
			
			/* Ancien fonctionnement
			$_SESSION["last_sortnotices"]=$r->id_tri;
			$sort_class = new sort('notices','base');*/
		}else{
			if (isset($_GET["sort"])) {	
				$_SESSION["last_sortnotices"]=$_GET["sort"];
			}
		}
		if ($nbr_lignes>$opac_nb_max_tri) {
			$_SESSION["last_sortnotices"]="";
			print "<span class=\"espaceResultSearch\">&nbsp;</span>";
		} else {
			$pos=strpos($_SERVER['REQUEST_URI'],"?");
			$pos1=strpos($_SERVER['REQUEST_URI'],"get");
			if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
			else $pos1=$pos1-3;
			$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
			$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
			$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
			$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
			print $affich_tris_result_liste;
			if ($_SESSION["last_sortnotices"]!="") {
				if(!$sort_class || !is_object($sort_class)) $sort_class = new sort('notices','session');
				print " ".$msg['tri_par']." ".$sort_class->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span>"; 
			} 
		}
		//fin gestion du tri
		
		print $add_cart_link;
		
		//affinage
		//enregistrement de l'endroit actuel dans la session
		$_SESSION["last_module_search"]["search_mod"]="etagere_see";
		$_SESSION["last_module_search"]["search_id"]=$id;
		$_SESSION["last_module_search"]["search_page"]=$page;
		
		// Gestion des alertes à partir de la recherche simple
 		include_once($include_path."/alert_see.inc.php");
 		print $alert_see_mc_values;
			
		//affichage
		print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";	
		//fin affinage
		
		print "<blockquote>\n";
		print aff_notice(-1);
		// on lance la vraie requête
		//$requete = "select distinct notice_id from caddie_content, etagere_caddie, notices, notice_statut where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id";
		//$requete .= " and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		$requete = "select distinct notice_id from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
		$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
		
		// ER: supprimé du tri aléatoire parce que affichage d'UNE seule étagère en détail et paginé, donc aléatoire à faire sauter.
		// if ($opac_etagere_notices_order) $requete.=" order by ".$opac_etagere_notices_order;
		//gestion du tri
		if ($_SESSION["last_sortnotices"]!="") {
			$requete = $sort_class->appliquer_tri($_SESSION["last_sortnotices"], $requete, "notice_id", $debut, $opac_nb_aut_rec_per_page);		
		} else {
			$requete .= "order by ".$opac_etagere_notices_order." LIMIT $debut,$opac_nb_aut_rec_per_page ";	
		}
		//fin gestion du tri
		
		$res = pmb_mysql_query($requete, $dbh);
		$nb=0;
		$recherche_ajax_mode=0;
		while(($obj=pmb_mysql_fetch_object($res))) {
			if($nb>4)$recherche_ajax_mode=1;
			$nb++;
			print pmb_bidi(aff_notice($obj->notice_id, 0, 1, 0, "", "", 0, 1, $recherche_ajax_mode));
		}
		print aff_notice(-2);
		pmb_mysql_free_result($res);
		// constitution des liens pur affichage de la barre de navigation
		$nbepages = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
		print "	</blockquote>\n";
		print "</div>"; // Fermeture du div resultatrech_liste
		print "</div><!-- fermeture #aut_details_liste -->\n";
		print "<div id='navbar'><hr /><center>".printnavbar($page, $nbepages, "./index.php?lvl=etagere_see&id=$id&page=!!page!!&nbr_lignes=$nbr_lignes")."</center></div>\n";
	} else {
			print $msg[no_document_found];
			print "</div><!-- fermeture #aut_details_liste -->\n";
	}
	print "</div><!-- fermeture #aut_details_container -->\n";
}

print "</div><!-- fermeture #aut_see -->\n";	

//FACETTES
$records = "";
if($nbr_lignes){
	require_once($base_path.'/classes/facette_search.class.php');
	$requete = "select distinct notice_id from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
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
?>