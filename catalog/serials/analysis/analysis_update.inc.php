<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_update.inc.php,v 1.58 2015-04-18 13:01:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/notice_doublon.class.php");

if ($forcage == 1) {
	$tab= unserialize( urldecode($ret_url) );
	foreach($tab->GET as $key => $val){
		$GLOBALS[$key] = $val;	    
	}	
	foreach($tab->POST as $key => $val){
		$GLOBALS[$key] = $val;
	}
} elseif ($pmb_notice_controle_doublons != 0 && !$analysis_id) {	
	//Si control de dédoublonnage activé	
	$sign = new notice_doublon();
	$signature = $sign->gen_signature();
	
	$requete="select signature, niveau_biblio ,notice_id from notices where signature='$signature'";
	if($serial_id)	$requete.= " and notice_id != '$analysis_id' ";
	//$requete.= " limit 1 ";
		
	$result=pmb_mysql_query($requete, $dbh);	
	if ($dbls=pmb_mysql_num_rows($result)) {
		//affichage de l'erreur, en passant tous les param postés (serialise) pour l'éventuel forcage 
		$tab = new stdClass();
		$tab->POST = addslashes_array($_POST);
		$tab->GET = addslashes_array($_GET);
		$ret_url= urlencode(serialize($tab));
		require_once("$class_path/mono_display.class.php");
		require_once("$class_path/serial_display.class.php");
	   
		print "
			<br /><div class='erreur'>$msg[540]</div>
			<script type='text/javascript' src='./javascript/tablist.js'></script>
			<div class='row'>
				<div class='colonne10'>
					<img src='./images/error.gif' align='left'>
				</div>
				<div class='colonne80'>
					<strong>".$msg["gen_signature_erreur_similaire"]."</strong>
				</div>
			</div>
			<div class='row'>
				<form class='form-$current_module' name='dummy'  method='post' action='./catalog.php?categ=serials&sub=analysis&action=update&bul_id=$bul_id&analysis_id=$analysis_id'>
					<input type='hidden' name='forcage' value='1'>
					<input type='hidden' name='signature' value='$signature'>
					<input type='hidden' name='ret_url' value='$ret_url'>
					<input type='button' name='ok' class='bouton' value=' $msg[76] ' onClick='history.go(-1);'>
					<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." '>
				</form>
				
			</div>
			";
		if($dbls<$nb_per_page_search){
			$maxAffiche=$dbls;
			echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
		}else{
			$maxAffiche=$nb_per_page_search;
			echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
		}
		$enCours=1;
		while($enCours<=$maxAffiche){
			$r=pmb_mysql_fetch_object($result);
			if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
				// notice de monographie
				$nt = new mono_display($r->notice_id,1,'catalog.php?categ=isbd&id='.$r->notice_id);
			} elseif($r->niveau_biblio == 's'){
				// on a affaire à un périodique
				$nt = new serial_display($r->notice_id,1,'catalog.php?categ=serials&sub=view&serial_id='.$r->notice_id);
			}else{
				// on a affaire à un article
				$bulletin_id = analysis::getBulletinIdFromAnalysisId($r->notice_id);
				$nt = new serial_display($r->notice_id,1,'','catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='.$bulletin_id);
			}
			echo "
				<div class='row'>
				$nt->result
		 	    </div>
				<script>document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');</script>
				<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";	
			$enCours++;
		}
		exit();
	}
}

	
//verification des droits de modification notice
//droits d'acces
if ($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
}

if ($acces_m==0) {

	if (!$analysis_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_depo_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {

	
	// script d'update d'un dépouillement de périodique 
	
	// mise à jour des champs avec autorité
	// si l'utilisateur vide les champs, l'id est mise à zéro
	if(!$f_indexint) $f_indexint_id = 0;
	
	// nettoyage des valeurs du form
	// les valeurs passées sont mises en tableau pour être passées
	// à la méthode d'update
	$table = array();
	$table['doc_type']      =  $typdoc;
	$table['typdoc']        =  $typdoc;
	$table['statut']		=  $form_notice_statut;
	$table['b_level']       =  $b_level;
	$table['h_level']       =  $h_level;
	$table['f_tit1']        =  clean_string($f_tit1);
	$table['f_tit2']        =  clean_string($f_tit2);
	$table['f_tit3']        =  clean_string($f_tit3);
	$table['f_tit4']        =  clean_string($f_tit4);
	$table['f_commentaire_gestion'] =  $f_commentaire_gestion ;
	$table['f_thumbnail_url'] =  $f_thumbnail_url ;
	$table['indexation_lang'] = $indexation_lang;
	
	// auteur principal
	$f_aut[] = array (
			'id' => $f_aut0_id,
			'fonction' => $f_f0_code,
			'type' => '0',
			'ordre' => 0 );
	// autres auteurs
	for ($i=0; $i<$max_aut1; $i++) {
		$var_autid = "f_aut1_id$i" ;
		$var_autfonc = "f_f1_code$i" ;
		$f_aut[] = array (
				'id' => $$var_autid,
				'fonction' => $$var_autfonc,
				'type' => '1',
				'ordre' => $i );
		}
	// auteurs secondaires
	for ($i=0; $i<$max_aut2 ; $i++) {
	
		$var_autid = "f_aut2_id$i" ;
		$var_autfonc = "f_f2_code$i" ;
		$f_aut[] = array (
				'id' => $$var_autid,
				'fonction' => $$var_autfonc,
				'type' => '2',
				'ordre' => $i );
		}
	
	$table['pages']                   =  clean_string($pages);
	$table['f_n_contenu']             =  $f_n_contenu;
	$table['f_n_gen']                 =  $f_n_gen;
	$table['f_n_resume']              =  $f_n_resume;
	
// catégories		
	if($tab_categ_order){
		$categ_order=explode(",",$tab_categ_order);
		$order=0;
		foreach($categ_order as $old_order){
			$var_categid = "f_categ_id$old_order" ;
			if($var_categid){
				$f_categ[] = array (
						'id' => $$var_categid,
						'ordre' => $order );
				$order++;
			}	
		}
	}else{
		for ($i=0; $i< $max_categ ; $i++) {
			$var_categid = "f_categ_id$i" ;
			$f_categ[] = array (
					'id' => $$var_categid,
					'ordre' => $i );
		}
	}
	
	$table['f_indexint_id']     =  $f_indexint_id;
	$table['f_indexation']      =  clean_string($f_indexation);
	$table['f_lien']            =  clean_string($f_lien);
	$table['f_eformat']         =  clean_string($f_eformat);
	$table['signature']			= $signature;
	
	// mise à jour de l'entête de page
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4023], $serial_header);
	
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	
	//Pour la synchro rdf
	if($pmb_synchro_rdf){
		require_once($class_path."/synchro_rdf.class.php");
		$synchro_rdf=new synchro_rdf();
		if($analysis_id){
			$synchro_rdf->delRdf($analysis_id,0);
		}
	}
	
	//Traitement des périos et bulletins
	global $perio_type, $bull_type;
	global  $f_perio_new, $f_perio_new_issn;
	global  $f_bull_new_num, $f_bull_new_date, $f_bull_new_mention, $f_bull_new_titre;
	//Perios
	if($perio_type == 'insert_new' && !$serial_id){
		$new_serial = new serial();
		$values = array();
		$values['tit1'] = $f_perio_new;
		$values['code'] = $f_perio_new_issn;
		$values['niveau_biblio'] = "s";
		$values['niveau_hierar'] = "1";
		$serial_id =  $new_serial->update($values);
		if($pmb_synchro_rdf){
			$synchro_rdf->addRdf($serial_id,0);
		}
	} 	
	//Bulletin
	if($bull_type == 'insert_new' && !$bul_id) {
		$req = "insert into bulletins set bulletin_numero='".$f_bull_new_num."',
			  mention_date='".$f_bull_new_mention."',
			  date_date='".$f_bull_new_date."',
			  bulletin_titre='".$f_bull_new_titre."',
			  bulletin_notice='".$serial_id."'";
		pmb_mysql_query($req,$dbh);
		$bul_id = pmb_mysql_insert_id();
		if($pmb_synchro_rdf){
			$synchro_rdf->addRdf(0,$bul_id);
		}
	}
	$table['serial_id']     =  $serial_id;
	$table['bul_id']        =  $bul_id;
	
	if($analysis_id) {
		$req_new="select notice_is_new, notice_date_is_new from notices where notice_id=$analysis_id ";
		$res_new=pmb_mysql_query($req_new, $dbh);
		if (pmb_mysql_num_rows($res_new)) {
			if($r=pmb_mysql_fetch_object($res_new)){
				if($r->notice_is_new==$f_notice_is_new){ // pas de changement du flag
					$req_notice_date_is_new= "";
				}elseif($f_notice_is_new){ // Changement du flag et affecté comme new
					$req_notice_date_is_new= ", notice_date_is_new =now() ";
				}else{// raz date
					$req_notice_date_is_new= ", notice_date_is_new ='' ";
				}
			}
		}
	}else{
		if($f_notice_is_new){ // flag affecté comme new en création
			$req_notice_date_is_new= ", notice_date_is_new =now() ";
		}
	}
	$table['notice_is_new'] = $f_notice_is_new+0;
	
	if (!$nberrors) {
		$myAnalysis = new analysis($analysis_id, $bul_id);
		$result = $myAnalysis->analysis_update($table, $req_notice_date_is_new);
	} else {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}
	
	if($id_sug && $result){
		$req_sug = "update suggestions set num_notice='".$result."' where id_suggestion='".$id_sug."'";
		pmb_mysql_query($req_sug,$dbh); 
	}
	
	if ($result) {
		//Traitement des liens
		$requete="DELETE FROM notices_relations WHERE num_notice='$result' OR linked_notice='$result'";
		pmb_mysql_query($requete);
		for ($i=0; $i<$max_rel; $i++) {
			$f_rel="f_rel_type_".$i;
			$f_rel=explode('-', ${$f_rel});
			$f_rel_id="f_rel_id_".$i;
			$f_rel_rank="f_rel_rank_".$i;
			$f_rel_type=$f_rel[0];
			$f_rel_direction=$f_rel[1];
			
			$f_rel_rank=${$f_rel_rank};
			$f_rel_id=${$f_rel_id};
			
			if ($f_rel_id) {
				if($f_rel_direction=='up'){
					$requete="INSERT INTO notices_relations VALUES('$result','$f_rel_id','$f_rel_type','$f_rel_rank')";
					@pmb_mysql_query($requete);
				}elseif($f_rel_direction=='down'){
					$requete="INSERT INTO notices_relations VALUES('$f_rel_id','$result','$f_rel_type','$f_rel_rank')";
					@pmb_mysql_query($requete);
				}
			}
		}
		
		// traitement des auteurs
		$rqt_del = "DELETE FROM responsability WHERE responsability_notice='$result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre) VALUES ";
		$i=0;
		while ($i<=count ($f_aut)-1) {
			$id_aut=$f_aut[$i]['id'];
			if ($id_aut) {
				$fonc_aut=$f_aut[$i]['fonction'];
				$type_aut=$f_aut[$i]['type'];
				$ordre_aut = $f_aut[$i]['ordre'];
				$rqt = $rqt_ins . " ('$id_aut','$result','$fonc_aut','$type_aut', $ordre_aut) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
			$i++;
		}
		
		// traitement des categories
		$rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='$result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
		while (list ($key, $val) = each ($f_categ)) {
			$id_categ=$val['id'];
			if ($id_categ) {
				$ordre_categ = $val['ordre'];
				$rqt = $rqt_ins . " ('$result','$id_categ', $ordre_categ ) " ; 
				$res_ins = pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// Indexation concepts
		global $thesaurus_concepts_active;
		require_once($class_path."/index_concept.class.php");
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($result, TYPE_NOTICE);
			$index_concept->save();
		}
	
		// traitement des langues
		// langues
		$f_lang_form = array();
		$f_langorg_form = array() ;
		for ($i=0; $i< $max_lang ; $i++) {
			$var_langcode = "f_lang_code$i" ;
			if ($$var_langcode) $f_lang_form[] =  array ('code' => $$var_langcode,'ordre' => $i);
		}
	
		// langues originales
		for ($i=0; $i< $max_langorg ; $i++) {
			$var_langorgcode = "f_langorg_code$i" ;
			if ($$var_langorgcode) $f_langorg_form[] =  array ('code' => $$var_langorgcode,'ordre' => $i);
		}
	
		$rqt_del = "delete from notices_langues where num_notice='$result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		while (list ($key, $val) = each ($f_lang_form)) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$result',0, '$tmpcode_langue', $tmpordre_langue) " ; 
				$res_ins = pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// traitement des langues originales
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		while (list ($key, $val) = each ($f_langorg_form)) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$result',1, '$tmpcode_langue', $tmpordre_langue) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
		}
		//Traitement des champs persos
		$p_perso->rec_fields_perso($result);
		
		// Mise à jour de la table notices_global_index
		notice::majNoticesGlobalIndex($result);
		// Mise à jour de la table notices_mots_global_index
		notice::majNoticesMotsGlobalIndex($result);
		
		if ($gestion_acces_active==1) {

			//mise a jour des droits d'acces user_notice (idem notice mere perio)
			if ($gestion_acces_user_notice==1) {
				$q = "replace into acces_res_1 select $result, res_prf_num, usr_prf_num, res_rights, res_mask from acces_res_1 where res_num=".$myAnalysis->bulletin_notice;
				pmb_mysql_query($q, $dbh);
			} 
	
			//mise a jour des droits d'acces empr_notice 
			if ($gestion_acces_empr_notice==1) {
				$dom_2 = $ac->setDomain(2);
				if ($analysis_id) {	
					$dom_2->storeUserRights(1, $result, $res_prf, $chk_rights, $prf_rad, $r_rad);
				} else {
					$dom_2->storeUserRights(0, $result, $res_prf, $chk_rights, $prf_rad, $r_rad);
				}
			} 
			
		}
	
	}
	
	if($pmb_synchro_rdf){
		$synchro_rdf->addRdf($myAnalysis->analysis_id,0);
	}
	
	if($result) {
		print "<div class='row'><div class='msg-perio'>".$msg[maj_encours]."</div></div>";
		$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=".$myAnalysis->bulletin_id;
		print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
	} else {
	   	error_message(	$msg[4023] ,$msg['catalog_serie_modif_depouill_imp'] ,1,"./catalog.php?categ=serials&sub=bulletinage&action=view&serial_id=$serial_id&bul_id=$bul_id");
	}

}
?>

