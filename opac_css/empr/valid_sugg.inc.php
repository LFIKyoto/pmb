<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: valid_sugg.inc.php,v 1.22.4.1 2019-11-14 10:11:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classes de gestion des suggestions
require_once($base_path.'/classes/suggestions.class.php');
require_once($base_path.'/classes/suggestions_origine.class.php');
require_once($base_path.'/classes/suggestions_map.class.php');
require_once($base_path.'/classes/suggestions_categ.class.php');
require_once($include_path.'/explnum.inc.php');
require_once($base_path.'/classes/explnum_doc.class.php');
require_once($base_path.'/classes/suggestion_source.class.php');

$sug_map = new suggestions_map();

$sug_form = "<h3>".htmlentities($msg["empr_make_sugg"], ENT_QUOTES, $charset)."</h3>\n";

// Contrôle des données saisies 
if (($tit != "") && ($aut != "" || $edi != "" || $code != "" || $_FILES['piece_jointe_sug']['name'] != "") ) {		//Les données minimun ont été saisies	

	$userid = $_SESSION["id_empr_session"];
	if (!$userid) {
		$type = '2';	//Visiteur non authentifié
		$userid= $mail;	
	} else {
		$type = '1';	//Abonné
	}

	//On évite de saisir 2 fois la même suggestion
	if ($id_sug || !suggestions::exists($userid, $tit, $aut, $edi, $code)) {
		$su = new suggestions($id_sug);
		$su->titre = stripslashes($tit);
		$su->editeur = stripslashes($edi);
		$su->auteur = stripslashes($aut);
		$su->code = stripslashes($code);
		$prix = str_replace(',','.',$prix);
		if (is_numeric($prix)) $su->prix = $prix;
		$su->nb = ((int)$nb?(int)$nb:"1");
		$su->statut = $sug_map->getFirstStateId();
		$su->url_suggestion = stripslashes($url_sug);
		$su->commentaires = stripslashes($comment);
		$su->date_creation = today();
		$su->date_publi = stripslashes($date_publi);
		$su->sugg_src = $sug_src; 

		// chargement de la PJ
		if($_FILES['piece_jointe_sug']['name']){			
			$explnum_doc = new explnum_doc();
			$explnum_doc->load_file($_FILES['piece_jointe_sug']);
			$explnum_doc->analyse_file();
		} 
		
		if ($opac_sugg_categ == '1' ) {
			
			if (!suggestions_categ::exists($num_categ) ){
				$num_categ = $opac_sugg_categ_default;
			}
			 if (!suggestions_categ::exists($num_categ) ) {
				$num_categ = '1';
			}
			$su->num_categ = $num_categ;	
		}
		$su->sugg_location=$sugg_location_id;
		$su->save($explnum_doc);
		
		$orig = new suggestions_origine($userid, $su->id_suggestion);
		$orig->type_origine = $type;
		$orig->save();
		
		//Ré-affichage de la suggestion
		$sug_form.= $su->get_table();
		$sug_form.= "<br />";
		$sug_form.= "<b>".htmlentities($msg["empr_sugg_ok"], ENT_QUOTES, $charset)."</b><br /><br />";
		
		//Envoi mail
		suggestions::alert_mail_sugg_users_pmb($type, $userid, $su->get_table(), $sugg_location_id) ;
		
	} else {
		//Mise en forme des données pour ré-affichage
		$tit = stripslashes($tit);
		$edi = stripslashes($edi);
		$aut = stripslashes($aut);
		$code = stripslashes($code);
		//Ré-affichage de la suggestion
		$sug_form.= "
		<table style='width:60%' cellpadding='5'>
			<tr>
				<td >".htmlentities($msg["empr_sugg_tit"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($tit, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_aut"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($aut, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_edi"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($edi, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_code"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($code, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_qte"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($nb, ENT_QUOTES, $charset)."</td>
			</tr>";
		$sug_form.= "</table><br />";
		$sug_form.= "<b>".htmlentities($msg["empr_sugg_already_exist"], ENT_QUOTES, $charset)."</b><br /><br />";
	}
} else {	// Les données minimun n'ont pas été saisies
	$sug_form.= str_replace('\n','<br />',$msg["empr_sugg_ko"])."<br /><br />";
	$sug_form.= "<input type='button' class='bouton' name='ok' value='&nbsp;".addslashes($msg['acquisition_sugg_retour'])."&nbsp;' onClick='history.go(-1)'/>";
}

print $sug_form;
 
?>