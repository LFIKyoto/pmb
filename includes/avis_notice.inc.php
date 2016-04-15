<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_notice.inc.php,v 1.13.2.1 2015-10-31 13:02:26 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($include_path."/interpreter/bbcode.inc.php");
require_once("$class_path/acces.class.php");
require_once($include_path.'/templates/avis.tpl.php');

function avis_notice($id,$avis_quoifaire,$valid_id_avis){
	global $dbh,$msg,$charset, $gestion_acces_active,$gestion_acces_user_notice;
	global $PMBuserid;
	global $avis_tpl_form1;
	global $opac_avis_allow;
	global $base_path;
	global $pmb_javascript_office_editor,$pmb_avis_note_display_mode;

	if(!$opac_avis_allow) return;
	if($avis_quoifaire){
		$acces_jm='';
		$acces_jl='';
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {

			$ac= new acces();
			$dom_1= $ac->setDomain(1);
			$acces_jm = $dom_1->getJoin($PMBuserid,8,'num_notice');	//modification
			$acces_jl = $dom_1->getJoin($PMBuserid,4,'num_notice');	//lecture
		}
		switch ($avis_quoifaire) {
			case 'valider':
				for ($i=0 ; $i < sizeof($valid_id_avis) ; $i++) {
					$acces_m=1;
					if ($acces_jm) {
						$q = "select count(1) from avis $acces_jm where id_avis=".$valid_id_avis[$i];
						$r = pmb_mysql_query($q, $dbh);
						if(pmb_mysql_result($r,0,0)==0) {
							$acces_m=0;
						}
					}
					if ($acces_m!=0) {
						$rqt = "update avis set valide=1 where id_avis='".$valid_id_avis[$i]."' ";
						pmb_mysql_query($rqt, $dbh) ;
					}
				}
			break;
			case 'invalider':
				for ($i=0 ; $i < sizeof($valid_id_avis) ; $i++) {
					$acces_m=1;
					if ($acces_jm) {
						$q = "select count(1) from avis $acces_jm where id_avis=".$valid_id_avis[$i];
						$r = pmb_mysql_query($q, $dbh);
						if(pmb_mysql_result($r,0,0)==0) {
							$acces_m=0;
						}
					}
					if ($acces_m!=0) {
						$rqt = "update avis set valide=0 where id_avis='".$valid_id_avis[$i]."' ";
						pmb_mysql_query($rqt, $dbh) ;
					}
				}
			break;
			case 'supprimer' :
				for ($i=0 ; $i < sizeof($valid_id_avis) ; $i++) {
					$acces_m=1;
					if ($acces_jm) {
						$q = "select count(1) from avis $acces_jm where id_avis=".$valid_id_avis[$i];
						$r = pmb_mysql_query($q, $dbh);
						if(pmb_mysql_result($r,0,0)==0) {
							$acces_m=0;
						}
					}
					if ($acces_m!=0) {
						$rqt = "delete from avis where id_avis='".$valid_id_avis[$i]."' ";
						pmb_mysql_query($rqt, $dbh) ;
					}
				}
			break;
			case 'ajouter' :
				global $avis_note,$avis_sujet, $avis_commentaire;
				if (!$avis_note) $avis_note="NULL";
				if($charset != "utf-8") $avis_commentaire=cp1252Toiso88591($avis_commentaire);
				$sql="insert into avis (num_empr,num_notice,note,sujet,commentaire) values ('0','$id','$avis_note','$avis_sujet','".$avis_commentaire."')";
				pmb_mysql_query($sql, $dbh);
			break;
			default:
			break;
		}
	}
	$aff="";
	$req_avis="select id_avis,note,sujet,commentaire,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate,empr_login,empr_nom, empr_prenom, valide
		from avis left join empr on id_empr=num_empr where num_notice='".$id."' order by avis_rank, dateajout desc";

	$r = pmb_mysql_query($req_avis, $dbh);
	$nb_avis=0;
	$nb_avis=pmb_mysql_num_rows($r);
		$aff= "
			<script type='text/javascript' src='javascript/tablist.js'></script>
			<script type=\"text/javascript\" src='./javascript/dyn_form.js'></script>
			<script type=\"text/javascript\" src='./javascript/http_request.js'></script>
			<script type='text/javascript' src='./javascript/bbcode.js'></script>
			<script type='text/javascript' src='./javascript/avis_drop.js'></script>

			<script type='text/javascript'>
				function setCheckboxes(the_form, the_objet, do_check) {
					var elts = document.forms[the_form].elements[the_objet+'[]'] ;
					var elts_cnt  = (typeof(elts.length) != 'undefined')
			                  ? elts.length
			                  : 0;
					if (elts_cnt) {
						for (var i = 0; i < elts_cnt; i++) {
							elts[i].checked = do_check;
							} // end for
						} else {
							elts.checked = do_check;
							} // end if... else
					return true;
				}

			</script>

			<form class='form-catalog' method='post' id='validation_avis_$id' name='validation_avis_$id' >
		";
		$i=0;
		while ($loc = pmb_mysql_fetch_object($r)) {
			if($pmb_javascript_office_editor)	{
				$office_editor_cmd=" if (typeof(tinyMCE) != 'undefined') tinyMCE.execCommand('mceAddControl', true, 'avis_desc_".$loc->id_avis."');	 ";
			}
			$avis_notice= "
				<div id='avis_$loc->id_avis' onclick=\" make_form('".$loc->id_avis."');$office_editor_cmd\">
					<div class='left'>
						<input type='checkbox' name='valid_id_avis[]' id='valid_id_avis[]' value='$loc->id_avis' onClick=\"stop_evenement(event);\" />" ;

			if($pmb_avis_note_display_mode){
				if($pmb_avis_note_display_mode!=1){
					$categ_avis=$msg['avis_detail_note_'.$loc->note];
				}
				if($pmb_avis_note_display_mode!=2){
					$etoiles="";$cpt_star = 4;
					for ($i = 1; $i <= $loc->note; $i++) {
						$etoiles.="<img border=0 src='images/star.png' align='absmiddle' />";
					}
					for ( $j = round($loc->note);$j <= $cpt_star ; $j++) {
						$etoiles .= "<img border=0 src='images/star_unlight.png' align='absmiddle' />";
					}
				}
				if($pmb_avis_note_display_mode==3 || $pmb_avis_note_display_mode==5)$note=$etoiles."<br />".$categ_avis;
				else if($pmb_avis_note_display_mode==4)$note=$etoiles;
				else $note=$etoiles.$categ_avis;
			} else $note="";

			if (!$loc->valide)
				$avis_notice.=  "<font color='#CC0000'><span >$note<b>".htmlentities($loc->sujet,ENT_QUOTES,$charset)."</b></span></font>";
			else
				$avis_notice.=  "<font color='#00BB00'><span >$note<b>".htmlentities($loc->sujet,ENT_QUOTES,$charset)."</b></span></font>";
			if($charset != "utf-8") $loc->commentaire=cp1252Toiso88591($loc->commentaire);
			$avis_notice.=  ", ".$loc->ladate."  $loc->empr_prenom $loc->empr_nom
					</div>
					<div class='row'>".do_bbcode($loc->commentaire)."	</div>
				</div>
				<div id='update_$loc->id_avis'></div>
				<br />
			";

			//Drag pour tri
			$id_elt =  $loc->id_avis;
			$drag_avis= "<div id=\"drag_".$id_elt."\" handler=\"handle_".$id_elt."\" dragtype='avisdrop' draggable='yes' recepttype='avisdrop' id_avis='$id_elt'
				recept='yes' dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext='".htmlentities($loc->sujet,ENT_QUOTES,$charset)."' downlight=\"avis_downlight\" highlight=\"avis_highlight\"
				order='$i' style='' >

				<span id=\"handle_".$id_elt."\" style=\"float:left; padding-right : 7px\"><img src=\"".$base_path."/images/sort.png\" style='width:12px; vertical-align:middle' /></span>";

			$aff.= $drag_avis.$avis_notice."</div>";
			$i++;

		}
		$avis_tpl_form=$avis_tpl_form1;
		$avis_tpl_form=str_replace("!!notice_id!!",$id,$avis_tpl_form);
		$add_avis_onclick="show_add_avis(".$id.");";
		$aff.="	$avis_tpl_form
				<div class='row'>
					<div class='left'>
						<input type='hidden' name='avis_quoifaire' value='' />
						<input type='button' class='bouton' name='selectionner' value='".$msg[avis_bt_selectionner]."' onClick=\"setCheckboxes('validation_avis_$id', 'valid_id_avis', true); return false;\" />&nbsp;
						<input type='button' class='bouton' name='valider' value='".$msg[avis_bt_valider]."' onclick='this.form.avis_quoifaire.value=\"valider\"; this.form.submit()' />&nbsp;
						<input type='button' class='bouton' name='invalider' value='".$msg[avis_bt_invalider]."' onclick='this.form.avis_quoifaire.value=\"invalider\"; this.form.submit()' />&nbsp;
						<input type='button' class='bouton' name='ajouter' value='".$msg[avis_bt_ajouter]."' onclick='$add_avis_onclick' />&nbsp;
					</div>
					<div class='right'>
						<input type='button' class='bouton' name='supprimer' value='".$msg[avis_bt_supprimer]."' onclick='this.form.avis_quoifaire.value=\"supprimer\"; this.form.submit()' />&nbsp;
					</div>
				</div>
				<div class='row'></div>
			</form>

				";
		if($avis_quoifaire) $deplier=1;

		$aff=gen_plus("plus_avis_notice_".$id,$msg["avis_notice_titre"]." ($nb_avis)",$aff,$deplier,'',"recalc_recept();");

	return $aff;
}