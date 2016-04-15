<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_pro.inc.php,v 1.38.2.1 2015-08-11 10:33:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function get_bannette_pro($title_form, $message, $form_action, $form_cb="") {
	global $dsi_search_bannette_tmpl;
	global $id_classement;
	$dsi_search_tmpl = $dsi_search_bannette_tmpl;
	$dsi_search_tmpl = str_replace("!!titre_formulaire!!", $title_form, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!form_action!!", $form_action, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!message!!", $message, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!cb_initial!!", $form_cb, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!classement!!", gen_liste_classement("BAN", $id_classement, "this.form.submit();")  , $dsi_search_tmpl);
	return $dsi_search_tmpl;
	}

function dsi_list_bannettes($form_cb="", $id_bannette=0, $id_classement="") {

global $dbh, $msg, $charset;
global $page, $nbr_lignes;
global $dsi_list_tmpl;


// nombre de références par pages
$nb_per_page = 10;

if ($form_cb) {
	$form_cb = str_replace("*", "%", $form_cb) ;
	$clause = "WHERE nom_bannette like '$form_cb%' and proprio_bannette=0" ;
	} else $clause = "WHERE proprio_bannette=0" ;
if ($id_classement===0) $clause.= " and num_classement=0 "; 
	elseif ($id_classement>0) $clause.= " and num_classement='$id_classement' " ;

if(!$nbr_lignes) {
	$requete = "SELECT COUNT(1) FROM bannettes $clause ";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	}

if (!$page) $page=1;
$debut = ($page-1)*$nb_per_page;

if($nbr_lignes) {

		// on lance la vraie requête
		$requete = "SELECT id_bannette, nom_bannette, comment_gestion FROM bannettes $clause ORDER BY nom_bannette, id_bannette LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete, $dbh);

		$parity = 0;
		$ban_trouvees =  pmb_mysql_num_rows($res) ;
		while(($bann=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even";
				else $pair_impair = "odd";
			$td_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=$bann->id_bannette&suite=acces&id_classement=$id_classement';\" ";
			$bann_list .= "<tr class='$pair_impair' $td_javascript style='cursor: pointer' >";
			$bann_list .= "
				<td width='70%'>
					<strong>".htmlentities($bann->nom_bannette,ENT_QUOTES, $charset)."</strong>
					<br />(".htmlentities($bann->comment_gestion,ENT_QUOTES, $charset).")
					</td>";
			$bann_list .= "
				<td width='30%'>
					<a href='./dsi.php?categ=bannettes&sub=pro&suite=affect_equation&id_bannette=$bann->id_bannette'>$msg[dsi_ban_affect_equation]</a>
					<br />
					<a href='./dsi.php?categ=bannettes&sub=pro&suite=affect_lecteurs&id_bannette=$bann->id_bannette'>$msg[dsi_ban_affect_lecteurs]</a>
					</td>";
			$bann_list .= "</tr>";
			$parity += 1;
			}
		pmb_mysql_free_result($res);

		// affichage de la barre de navig
		$url_base = "$PHP_SELF?categ=bannettes&sub=pro&form_cb=".rawurlencode($form_cb)."&id_classement=$id_classement" ;
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		$dsi_list_tmpl = str_replace("!!cle!!", $form_cb, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!list!!", $bann_list, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!message_trouve!!", $msg['dsi_ban_trouvees'], $dsi_list_tmpl);
		
		return $dsi_list_tmpl;
		} else return $msg['dsi_no_ban_found'] ;
}

function bannette_equation ($nom="", $id_bannette=0) {
	global $dsi_bannette_equation_assoce, $msg, $dbh, $id_classement ;
	global $charset ;
	global $faire;
	global $page, $nbr_lignes, $nb_per_page;
	
	if (!$id_classement) $id_classement=0;
	$link_pagination = "";
	if($page > 1) {
		$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
	}
	$url_base = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=affect_equation"; 
	$url_modif = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=acces"; 
	// $detail_bannette = "<h3>$nom &nbsp;<input type='button' class='bouton' value=\"$msg[dsi_bt_modifier_ban]\" onclick=\"document.location='$url_modif';\" /></h3>";
	if ($id_classement>0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 and num_classement='$id_classement' order by nom_equation " ;
		elseif ($id_classement==0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 order by nom_equation " ;
			elseif ($id_classement==-1) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations, bannette_equation where num_bannette=$id_bannette and num_equation=id_equation and proprio_equation=0 order by nom_equation " ;
	$res = pmb_mysql_query($requete, $dbh) or die ($requete) ;
	$parity = 0;
	$equ_trouvees =  pmb_mysql_num_rows($res) ;
	while ($equa=pmb_mysql_fetch_object($res)) {
		$equations .= "<input type='checkbox' name='bannette_equation[]' value='$equa->id_equation' ";
		$requete_affect = "SELECT 1 FROM bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id_bannette' ";
		$res_affect = pmb_mysql_query($requete_affect, $dbh);
		if (pmb_mysql_num_rows($res_affect)) $equations .= "checked" ;
		$equations .= " /> $equa->nom_equation<br />";
	}
	$dsi_bannette_equation_assoce = str_replace("!!form_action!!", $url_base."&faire=enregistrer".$link_pagination, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!nom_bannette!!", $nom, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!equations!!", $equations, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!id_classement_anc!!", $id_classement, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!id_bannette!!", $id_bannette, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!classement!!", 
		gen_liste ("select id_classement, nom_classement from classements where id_classement=1 union select 0 as id_classement, '".$msg['dsi_all_classements']."' as nom_classement UNION select id_classement, nom_classement from classements where type_classement='EQU' order by nom_classement", "id_classement", "nom_classement", "id_classement", "this.form.faire.value=''; this.form.submit();", $id_classement, "", "",-1,$msg['dsi_ban_equation_affectees'],0)
		, $dsi_bannette_equation_assoce);
	if($faire == "enregistrer") {
		$dsi_bannette_equation_assoce = str_replace("!!bannette_equations_saved!!", "<div class='erreur'>".$msg["dsi_bannette_equations_update"]."</div><br />", $dsi_bannette_equation_assoce);
	} else {
		$dsi_bannette_equation_assoce = str_replace("!!bannette_equations_saved!!", "", $dsi_bannette_equation_assoce);
	}
	// afin de revenir où on était : $form_cb, le critère de recherche
	global $form_cb ;
	$dsi_bannette_equation_assoce = str_replace('!!form_cb!!', urlencode($form_cb),  $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace('!!form_cb_hidden!!', htmlentities($form_cb,ENT_QUOTES, $charset),  $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace('!!link_pagination!!', $link_pagination,  $dsi_bannette_equation_assoce);
	
	$detail_bannette .= $dsi_bannette_equation_assoce ;
	return $detail_bannette ;
}

function bannette_lecteur ($nom="", $id_bannette=0) {
	global $dsi_bannette_lecteurs_assoce, $msg, $dbh, $quoi, $id_categorie, $id_groupe, $mail_abon ;
	global $lect_restrict, $empr_location_id, $deflt2docs_location, $pmb_lecteurs_localises ;
	global $charset ;
	global $faire;
	global $page, $nbr_lignes, $nb_per_page;
	
	$nb_limit = 20 ;
	if ($lect_restrict) {
		$lect_query = str_replace("*","%",$lect_restrict."*") ;
		$limit_nb = "" ;
	} else {
		$lect_query = "%" ;
		$limit_nb = " limit $nb_limit " ;
	}
	
	if ($pmb_lecteurs_localises && (string)$empr_location_id!="0") {
		if ((string)$empr_location_id=="") $empr_location_id=$deflt2docs_location;
		$restrict_loc = " and empr_location=$empr_location_id ";
	} else $restrict_loc = "";

	if ($mail_abon) {
		$restrict_mail = " and empr_mail <>'' ";
		$mail_abon_checked = "checked=checked";	
	} else {
		$restrict_mail = "";
		$mail_abon_checked = "";
	}
	
	$link_pagination = "";
	if($page > 1) {
		$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
	}
	
	$url_base = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=affect_lecteurs"; 
	$url_modif = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=acces"; 

	if ($quoi == 'groups') {
		if (!$id_groupe) $id_groupe=0;
		$id_categorie=0;
		if ($id_groupe>0) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr,empr_groupe where id_empr=empr_id and groupe_id='$id_groupe' and empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb" ;
			elseif ($id_groupe==0) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr where empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb " ;
				elseif ($id_groupe==-1) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr, bannette_abon where num_bannette='$id_bannette' and id_empr=num_empr and empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb" ;
		$res = pmb_mysql_query($requete, $dbh) or die ($requete) ;
	} else {
		if (!$id_categorie) $id_categorie = 0;
		$id_groupe=0;
		if ($id_categorie>0) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr where empr_categ='$id_categorie' and empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb" ;
			elseif ($id_categorie==0) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr where empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb " ;
				elseif ($id_categorie==-1) $requete = "select id_empr, empr_cb, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_mail from empr, bannette_abon where num_bannette='$id_bannette' and id_empr=num_empr and empr_nom like '$lect_query' $restrict_loc $restrict_mail order by nom_prenom, empr_cb $limit_nb" ;
		$res = pmb_mysql_query($requete, $dbh) or die ($requete) ;
	}

	$parity = 0;
	$lec_trouvees =  pmb_mysql_num_rows($res) ;
	$lecteurs="";
	if($lec_trouvees){
		$lecteurs="<table>";
		while ($lec=pmb_mysql_fetch_object($res)) {
			$requete_affect = "SELECT * FROM bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id_bannette' ";
			$res_affect = pmb_mysql_query($requete_affect, $dbh);
			$checked= "";
			if (pmb_mysql_num_rows($res_affect)){
				$abon=pmb_mysql_fetch_object($res_affect);
				$checked= "checked";
			}
			$sel_mail="";
			if ($lec->empr_mail){
				$destinataires = explode(";",$lec->empr_mail) ;
				if(count($destinataires)>1){
					$sel_mail="<select name='sel_mail_".$lec->id_empr."' >";
					if(!$abon->bannette_mail) $selected=" selected='selected' ";
					$sel_mail.="<option value ='' $selected>".$msg["dsi_ban_form_mail_all"]."</option>";
					foreach($destinataires as $mail){
						$selected="";
						if($mail == $abon->bannette_mail)  $selected=" selected='selected' ";
						$sel_mail.="<option value ='$mail' $selected >$mail</option>";
					}
					$sel_mail.="</select>";
				}
			}			
			$lecteurs.="
			<tr>
				<td><input type='checkbox' name='bannette_abon[]' value='$lec->id_empr' $checked /></td>
				<td>".htmlentities($lec->nom_prenom,ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($lec->empr_mail,ENT_QUOTES, $charset)."</td> 
				<td>".$sel_mail."</td>
			</tr>";
		}
		$lecteurs.="</table>";
	}	
	$dsi_bannette_lecteurs_assoce = str_replace("!!form_action!!", $url_base."&faire=enregistrer".$link_pagination, $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace("!!nom_bannette!!", $nom, $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace("!!selected!!", $quoi, $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace("!!lecteurs!!", $lecteurs, $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace("!!id_bannette!!", $id_bannette, $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace("!!lect_restrict!!", $lect_restrict, $dsi_bannette_lecteurs_assoce);
	if ($limit_nb) $dsi_bannette_lecteurs_assoce = str_replace("!!limitation!!", str_replace('!!nbres!!',$nb_limit,$msg[dsi_ban_abo_limit_abon]), $dsi_bannette_lecteurs_assoce);
		$dsi_bannette_lecteurs_assoce = str_replace("!!limitation!!", $msg[dsi_ban_abo_nolimit_abon], $dsi_bannette_lecteurs_assoce);
	
	$liste_localisations=gen_liste ("select 0 as idlocation, '".$msg['all_location']."' as libelle UNION select idlocation, location_libelle as libelle from docs_location order by libelle", "idlocation", "libelle", "empr_location_id", "this.form.faire.value=''; this.form.submit();", $empr_location_id, "", "","","",0);
	//if ($pmb_lecteurs_localises) $dsi_bannette_lecteurs_assoce = str_replace("!!restrict_location!!", docs_location::gen_combo_box_empr($empr_location_id), $dsi_bannette_lecteurs_assoce);
	if ($pmb_lecteurs_localises) $dsi_bannette_lecteurs_assoce = str_replace("!!restrict_location!!", $liste_localisations, $dsi_bannette_lecteurs_assoce);
		else $dsi_bannette_lecteurs_assoce = str_replace("!!restrict_location!!", "", $dsi_bannette_lecteurs_assoce);
	
	$dsi_bannette_lecteurs_assoce = str_replace("!!classement!!",  
		gen_liste ("select 0 as id_categorie, '".addslashes($msg['dsi_all_categories'])."' as libelle UNION select id_categ_empr as id_categorie, libelle from empr_categ order by libelle", "id_categorie", "libelle", "id_categorie", "this.form.faire.value=''; this.form.quoi.value='categories'; this.form.submit();", $id_categorie, "", "",-1,$msg['dsi_ban_lecteurs_affectes'],0)
		, $dsi_bannette_lecteurs_assoce);

	$dsi_bannette_lecteurs_assoce = str_replace("!!groupe!!",  
		gen_liste ("select 0 as id_groupe, '".addslashes($msg['dsi_all_groups'])."' as libelle_groupe UNION select groupe_id as id_groupe, libelle_groupe from empr_groupe, groupe where groupe_id=id_groupe order by libelle_groupe", "id_groupe", "libelle_groupe", "id_groupe", "this.form.faire.value=''; this.form.quoi.value='groups'; this.form.submit();", $id_groupe, "", "",-1,$msg['dsi_ban_lecteurs_affectes'],0)
		, $dsi_bannette_lecteurs_assoce);
		
	$dsi_bannette_lecteurs_assoce = str_replace("!!mail_abon_checked!!", $mail_abon_checked, $dsi_bannette_lecteurs_assoce);
	
	if($faire == "enregistrer") {
		$dsi_bannette_lecteurs_assoce = str_replace("!!bannette_lecteurs_saved!!", "<div class='erreur'>".$msg["dsi_bannette_lecteurs_update"]."</div><br />", $dsi_bannette_lecteurs_assoce);
	} else {
		$dsi_bannette_lecteurs_assoce = str_replace("!!bannette_lecteurs_saved!!", "", $dsi_bannette_lecteurs_assoce);
	}
	
	// afin de revenir où on était : $form_cb, le critère de recherche
	global $form_cb ;
	$dsi_bannette_lecteurs_assoce = str_replace('!!form_cb!!', urlencode($form_cb),  $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace('!!form_cb_hidden!!', htmlentities($form_cb,ENT_QUOTES, $charset),  $dsi_bannette_lecteurs_assoce);
	$dsi_bannette_lecteurs_assoce = str_replace('!!link_pagination!!', $link_pagination,  $dsi_bannette_lecteurs_assoce);
	
	$detail_lecteurs .= $dsi_bannette_lecteurs_assoce ;
	return $detail_lecteurs ;
}
	
function dsi_list_bannettes_info($form_cb="", $id_bannette=0, $id_classement="") {

global $dbh, $msg, $charset;
global $page, $nbr_lignes;
global $dsi_list_tmpl;


// nombre de références par pages
$nb_per_page = 10;
 
if ($form_cb) {
	$form_cb_save = $form_cb ;
	$form_cb = str_replace("*", "%", $form_cb) ;
	$clause = "WHERE nom_bannette like '$form_cb%' and proprio_bannette=0 " ;
	} else {
		$form_cb_save = "*" ;
		$clause = "WHERE proprio_bannette=0 " ;
		}
if ($id_classement===0) $clause.= " and num_classement=0 "; 
	elseif ($id_classement>0) $clause.= " and num_classement='$id_classement' " ;

if(!$nbr_lignes) {
	$requete = "SELECT COUNT(1) FROM bannettes $clause ";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	}

if (!$page) $page=1;
$debut = ($page-1)*$nb_per_page;

if($page > 1) {
	$link_pagination ="&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
} else $link_pagination = "";

if($nbr_lignes) {

		// on lance la vraie requête
		$requete = "SELECT id_bannette FROM bannettes $clause ORDER BY nom_bannette, id_bannette LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete, $dbh);

		$bann_list .= "<tr >";
		$bann_list .= "
				<th>
					<strong>".htmlentities($msg[dsi_ban_form_nom],ENT_QUOTES, $charset)."</strong>
					(".htmlentities($msg[dsi_classement],ENT_QUOTES, $charset).")<ul>
					".htmlentities($msg[dsi_ban_form_com_gestion],ENT_QUOTES, $charset)."
					</ul></th>";
		$bann_list .= "
				<th>
					<strong>".htmlentities($msg[dsi_ban_list_equ],ENT_QUOTES, $charset)."</strong>
					</th>";
		$bann_list .= "
				<th>
					<strong>".htmlentities($msg[dsi_ban_nb_notices],ENT_QUOTES, $charset)."</strong>
					</th>";
		$bann_list .= "
				<th>
					<strong>".htmlentities($msg[dsi_ban_nb_abonnes],ENT_QUOTES, $charset)."</strong>
					</th>";
		$bann_list .= "
				<th>
					<strong>".htmlentities($msg[dsi_ban_date_last_envoi],ENT_QUOTES, $charset)."</strong>
					<br />(".htmlentities($msg[dsi_ban_date_last_remp],ENT_QUOTES, $charset).")
					</th>";
		$bann_list .= "</tr>";
		
		$parity = 0;
		$ban_trouvees =  pmb_mysql_num_rows($res) ;
		while(($ban=pmb_mysql_fetch_object($res))) {
			$bann = new bannette($ban->id_bannette) ;
			if ($parity % 2) $pair_impair = "even";
				else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$bann_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer' >";
			$td_javascript=" onmousedown=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=$bann->id_bannette";
			$td_javascript.="&suite=acces&id_classement=$id_classement";
			$td_javascript.="&form_cb=".urlencode($form_cb_save);
			$td_javascript.=$link_pagination;
			$td_javascript.="';\" ";
			$bann_list .= "
				<td valign='top' $td_javascript>
					<strong>".htmlentities($bann->nom_bannette,ENT_QUOTES, $charset)."</strong>
					<strong>(".htmlentities($bann->nom_classement,ENT_QUOTES, $charset).")</strong><ul>
					<em>".htmlentities($bann->comment_gestion,ENT_QUOTES, $charset)."</em>";
			$bann_list .= "</ul></td>";
			$requete = "select id_equation, num_classement, nom_equation, comment_equation, proprio_equation, num_bannette from equations, bannette_equation where num_equation=id_equation and proprio_equation=0 and num_bannette='$bann->id_bannette' order by nom_equation " ;
			$resequ = pmb_mysql_query($requete, $dbh) or die ($requete) ;
			$equ_trouvees =  pmb_mysql_num_rows($resequ) ;
			$equations = "" ;
			while ($equa=pmb_mysql_fetch_object($resequ)) {
				$equations .= "<li>$equa->nom_equation</li>";
			}
			$td_javascript=" onmousedown=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=$bann->id_bannette&suite=affect_equation&id_classement=$id_classement&form_cb=".urlencode($form_cb_save);
			$td_javascript.=$link_pagination;
			$td_javascript.="';\" ";
			if($equ_trouvees == 0) $bann_list .= "<td valign='top' $td_javascript><ul>".$msg[dsi_ban_no_equ]."</ul></td>";
				else $bann_list .= "<td valign='top' $td_javascript><ul>$equations</ul></td>";
			$bann_list .= "<td valign='top'>$bann->nb_notices</td>";
			
			$td_javascript=" onmousedown=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=$bann->id_bannette&suite=affect_lecteurs&id_classement=$id_classement&form_cb=".urlencode($form_cb_save);
			$td_javascript.=$link_pagination;
			$td_javascript.="';\" ";
			if ($bann->num_panier) $aff_bann_fills_basket = "&nbsp;&nbsp;<img src='./images/basket_small_20x20.gif' border='0' align='center' />";
				else $aff_bann_fills_basket = "";
			$bann_list .= "<td valign='top' $td_javascript>".$bann->nb_abonnes.$aff_bann_fills_basket."</td>";
			
			$td_javascript=" onmousedown=\"document.location='./dsi.php?categ=diffuser&sub=auto&id_bannette=$bann->id_bannette&id_classement=$id_classement&form_cb=".urlencode($form_cb_save);
			$td_javascript.=$link_pagination;
			$td_javascript.="';\" ";
			$bann_list .= "<td valign='top' sorttable_customkey='".$bann->date_last_envoi."' $td_javascript>
					<strong>".htmlentities($bann->aff_date_last_envoi,ENT_QUOTES, $charset)."</strong>";
			if ($bann->alert_diff) $bann_list .= "<br /><font color=red>(".htmlentities($bann->aff_date_last_remplissage,ENT_QUOTES, $charset).")</font>";
				else $bann_list .= "<br />(".htmlentities($bann->aff_date_last_remplissage,ENT_QUOTES, $charset).")" ;
			$bann_list .= "</td>";
			$bann_list .= "</tr>";
			$parity += 1;
		}
		pmb_mysql_free_result($res);


		// affichage de la barre de navig
		$url_base = "$PHP_SELF?categ=bannettes&sub=pro&suite=search&form_cb=".rawurlencode($form_cb)."&id_classement=$id_classement" ;
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		if ($nbr_lignes>0) $dsi_list_tmpl = str_replace("<!--!!nb_total!!-->", "(".$nbr_lignes.")", $dsi_list_tmpl);
		
		$dsi_list_tmpl = str_replace("!!cle!!", $form_cb, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!list!!", $bann_list, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!message_trouve!!", $msg['dsi_ban_trouvees'], $dsi_list_tmpl);
		
		return $dsi_list_tmpl;
	} else return $msg['dsi_no_ban_found'] ;
}
	