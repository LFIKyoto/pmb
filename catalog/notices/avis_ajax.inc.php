<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_ajax.inc.php,v 1.11 2015-06-23 09:48:21 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($include_path."/interpreter/bbcode.inc.php");

switch($quoifaire){

	case 'show_form':
		show_form($id);
	break;
	case 'update_avis':
		update_avis($id);
	break;

}

function show_form($id){
	global $dbh, $msg, $charset,$pmb_javascript_office_editor;

	$req = "select sujet, commentaire from avis where id_avis='".$id."'";
	$res = pmb_mysql_query($req,$dbh);
	while(($avis = pmb_mysql_fetch_object($res))){
		$sujet = $avis->sujet;
		$desc = $avis->commentaire;
		if($charset != "utf-8") $desc=cp1252Toiso88591($desc);
	}
	if ($pmb_javascript_office_editor) {
		$office_editor_cmd_quit="if (typeof(tinyMCE) != 'undefined') tinyMCE.execCommand('mceRemoveControl', true, 'avis_desc_".$id."');";
		$display .= "
		<div class='row'>
			<label class='etiquette'>$msg[avis_sujet]</label> <br />
			<input type='texte' size='50' name='field_sujet_$id' id='field_sujet_$id' value='".htmlentities($sujet,ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label class='etiquette' >$msg[avis_comm]</label><br />

			<textarea id='avis_desc_$id' name='avis_commentaire' cols='120' rows='20'>".htmlentities($desc,ENT_QUOTES,$charset)."</textarea>
		</div>
		<input type='button' class='bouton_small' name='save_avis_$id' id='save_avis_$id' value='$msg[avis_save]' />
		<input type='button' class='bouton_small' name='mceToggleEditor' onclick=\"if (typeof(tinyMCE) != 'undefined') tinyMCE.execCommand('mceToggleEditor',false,'avis_desc_".$id."'); return false;\"  value='Edition'>
		<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='$msg[avis_exit]' onclick=\"$office_editor_cmd_quit avis_exit('$id')\" />
		";

	} else{
		$display .= "
		<div class='row'>
			<label class='etiquette'>$msg[avis_sujet]</label>
			<input type='texte' class='saisie-20em' name='field_sujet_$id' id='field_sujet_$id' value='".htmlentities($sujet,ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label class='etiquette' >$msg[avis_comm]</label>
			<div style='padding-top: 4px;'>
				<input value=' B ' name='B' onclick=\"insert_text('avis_desc_$id','[b]','[/b]')\" type='button' class='bouton_small'>
				<input value=' I ' name='I' onclick=\"insert_text('avis_desc_$id','[i]','[/i]')\" type='button' class='bouton_small'>
				<input value=' U ' name='U' onclick=\"insert_text('avis_desc_$id','[u]','[/u]')\" type='button' class='bouton_small'>
				<input value='http://' name='Url' onclick=\"insert_text('avis_desc_$id','[url]','[/url]')\" type='button' class='bouton_small'>
				<input value='Img' name='Img' onclick=\"insert_text('avis_desc_$id','[img]','[/img]')\" type='button' class='bouton_small'>
				<input value='Code' name='Code' onclick=\"insert_text('avis_desc_$id','[code]','[/code]')\" type='button' class='bouton_small'>
				<input value='Quote' name='Quote' onclick=\"insert_text('avis_desc_$id','[quote]','[/quote]')\" type='button' class='bouton_small'>
			</div>
			<textarea style='vertical-align:top' id='avis_desc_$id' name='avis_desc_$id' cols='60' rows='8'>".htmlentities($desc,ENT_QUOTES,$charset)."</textarea>
		</div>
		<input type='button' class='bouton_small' name='save_avis_$id' id='save_avis_$id' value='$msg[avis_save]' />
		<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='$msg[avis_exit]' onclick=\"avis_exit('$id')\" />
		";
	}
	print $display;
}

function update_avis($id){
	global $dbh,$desc, $sujet, $msg, $charset;
	
	header('Content-Type: text/html;charset='.$charset);

	$req = "update avis set sujet='".$sujet."', commentaire='".$desc."' where id_avis='".$id."'";
	pmb_mysql_query($req,$dbh);

	$requete = "select avis.note, avis.sujet, avis.commentaire, avis.id_avis, DATE_FORMAT(avis.dateAjout,'".$msg[format_date]."') as ladate, ";
	$requete.= "empr_login, empr_nom, empr_prenom, ";
	$requete.= "niveau_biblio, niveau_biblio, valide, notice_id ";
	$requete.= "from avis ";
	$requete.= "left join empr on empr.id_empr=avis.num_empr ";
	$requete.= "left join notices on notices.notice_id=avis.num_notice ";
	$requete.= "where id_avis='".$id."'";
	$requete.= "order by index_serie, tnvol, index_sew ,dateAjout desc ";
	$res = pmb_mysql_query($requete,$dbh);
	while(($loc = pmb_mysql_fetch_object($res))){
		$display = "
			<div class='left'>
				<input type='checkbox' name='valid_id_avis[]' id='valid_id_avis[]' value='$loc->id_avis' onClick=\"stop_evenement(event);\" />" ;
		if (!$loc->valide) $display .=
				"<font color='#CC0000'>".$msg[gestion_avis_note]." <span >".htmlentities($loc->note,ENT_QUOTES,$charset)." <b>".htmlentities($loc->sujet,ENT_QUOTES, $charset)."</b></span></font>";
		else $display .=
				"<font color='#00BB00'>".$msg[gestion_avis_note]." <span >".htmlentities($loc->note,ENT_QUOTES,$charset)." <b>".htmlentities($loc->sujet,ENT_QUOTES,$charset)."</b></span></font>";
		if($charset != "utf-8") $loc->commentaire=cp1252Toiso88591($loc->commentaire);
		$display .=  ", ".htmlentities($loc->ladate,ENT_QUOTES,$charset)." ".htmlentities($loc->empr_prenom." ".$loc->empr_nom,ENT_QUOTES,$charset)."
			</div>
			<div class='row'>".do_bbcode($loc->commentaire)."	</div>
		";
	}
	print $display;
}
?>