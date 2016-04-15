<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_expl_update.inc.php,v 1.33 2015-04-16 11:39:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc_diff.class.php");
require_once($class_path."/serialcirc.class.php");

// mise a jour de l'entete de page
if(!$expl_id) {
	// pas d'id, c'est une creation
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4007], $serial_header);
} else {
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4008], $serial_header);
}


//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
	$q = "select count(1) from bulletins $acces_j where bulletin_id=".$expl_bulletin;
	$r = pmb_mysql_query($q, $dbh);
	if(pmb_mysql_result($r,0,0)==0) {
		$acces_m=0;
	}
}

if ($acces_m==0) {

	if (!$expl_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_expl_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {
		
	
	// le form d'exemplaire renvoit :
	// Je nettoie ce qui me parait devoir etre nettoye
	
	// $bul_id
	// $id_form
	// $org_cb
	// $expl_id
	// $expl_bulletin
	// $expl_typdoc
	$expl_cote = clean_string($expl_cote);
	// $expl_section
	// $expl_statut
	// $expl_location
	// $expl_codestat
	$expl_note = clean_string($expl_note);
	$expl_comment = clean_string($f_ex_comment);
	$expl_prix = clean_string($expl_prix);
	// $expl_owner
	
	//Verification des champs personalises
	$p_perso=new parametres_perso("expl");
	$nberrors=$p_perso->check_submited_fields();
	if ($nberrors) {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}
	// controle sur le nouveau code barre si applicable :
	if($org_cb != $f_ex_cb) {
		// si le nouveau code-barre est deja utilise, on reste sur l'ancien
		$requete = "SELECT expl_id FROM exemplaires WHERE expl_cb='$f_ex_cb'";
		
		$myQuery = pmb_mysql_query($requete, $dbh);
		if(!($result=pmb_mysql_result($myQuery, 0, 0))) {
			$expl_cb = $f_ex_cb;
		} else {
			// Verif si expl_id est celui poste
			if($expl_id == $result[0]) {
				$expl_cb = $org_cb;
			} else {
				//Erreur: code barre deja existant
				error_message_history($msg[301],$msg[303],1);
				exit();
			}
		}	
	} else {
		$expl_cb = $f_ex_cb;
	}
	
	// on prepare la date de creation ou modification
	$expl_date = today();
	
	// on recupere les valeurs 
	$formlocid="f_ex_section".$expl_location ;
	$expl_section=$$formlocid ;
	
	if(!is_numeric($f_ex_nbparts) || !$f_ex_nbparts) $f_ex_nbparts=1;
	
	$transfert_origine="";
	if($expl_id){
		$rqt = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert and etat_transfert=0 AND num_expl='".$expl_id."' " ;
		$res = pmb_mysql_query( $rqt );
		if (!pmb_mysql_num_rows($res)){
			// pas de transfert en cours, on met à jour transfert_location_origine
			$transfert_origine= ", transfert_location_origine='$expl_location', transfert_statut_origine='$expl_statut', transfert_section_origine='$expl_section' ";
		}
	}else{
		// en création
		$transfert_origine= ", transfert_location_origine='$expl_location', transfert_statut_origine='$expl_statut', transfert_section_origine='$expl_section' ";
	}
	
	// preparation de la requete
	if($expl_id) {

		$audit=new audit();
		$audit->get_old_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='$expl_cb' ");

		// update de l'exemplaire
		// on prepare la requete
		$values = "expl_cb='$expl_cb'";
		$values .= ", expl_typdoc='$expl_typdoc'";
		$values .= ", expl_cote='$expl_cote'";
		$values .= ", expl_section='$expl_section'";
		$values .= ", expl_statut='$expl_statut'";
		$values .= ", expl_location='$expl_location' $transfert_origine ";
		$values .= ", expl_codestat='$expl_codestat'";
		$values .= ", expl_note='$expl_note'";
		$values .= ", expl_comment='$expl_comment'";
		$values .= ", expl_prix='$expl_prix'";
		$values .= ", expl_owner='$expl_owner'";
		$values .= ", type_antivol='$type_antivol'";
		$values .= ", expl_nbparts='$f_ex_nbparts'";
		$requete = "UPDATE exemplaires SET $values WHERE expl_id=$expl_id AND expl_notice=0 LIMIT 1";
		$myQuery = pmb_mysql_query($requete, $dbh);		
		$audit->get_new_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='$expl_cb' ");
		$audit->save_info_modif(AUDIT_EXPL, $expl_id,"bul_expl_update.inc.php");
		
	} else {
		// insertion d'un nouvel exemplaire
		$values = "expl_cb='$expl_cb'";
		$values .= ", expl_notice='0'";
		$values .= ", expl_bulletin='$expl_bulletin'";
		$values .= ", expl_typdoc='$expl_typdoc'";
		$values .= ", expl_cote='$expl_cote'";
		$values .= ", expl_section='$expl_section'";
		$values .= ", expl_statut='$expl_statut'";
		$values .= ", expl_location='$expl_location' $transfert_origine ";
		$values .= ", expl_codestat='$expl_codestat'";
		$values .= ", expl_note='$expl_note'";
		$values .= ", expl_comment='$expl_comment'";
		$values .= ", expl_prix='$expl_prix'";
		$values .= ", expl_owner='$expl_owner'";
		$values .= ", type_antivol='$type_antivol'";
		$values .= ", expl_nbparts='$f_ex_nbparts'";
		$requete = "INSERT INTO exemplaires set $values , create_date=sysdate() ";
		$myQuery = pmb_mysql_query($requete, $dbh);
		$expl_id=pmb_mysql_insert_id();
		audit::insert_creation(AUDIT_EXPL, $expl_id) ;
	}
	
	if($abt_id && $serial_circ_add)		
	$serialcirc_diff=new serialcirc_diff(0,$abt_id);
		// Si c'est à faire circuler
	if($serialcirc_diff->id){ 
		$serialcirc_diff->add_circ_expl($expl_id);
	}
	
	// traitement des concepts
	if($thesaurus_concepts_active == 1){
		$index_concept = new index_concept($expl_id, TYPE_EXPL);
		$index_concept->save();
	}
	
	//Insertion des champs personalises
	$p_perso->rec_fields_perso($expl_id);
	
	// Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
	$req_maj="SELECT bulletin_notice,num_notice, analysis_notice FROM bulletins LEFT JOIN analysis ON analysis_bulletin=bulletin_id WHERE bulletin_id='".$expl_bulletin."'";
	$res_maj=pmb_mysql_query($req_maj);
	if($res_maj && pmb_mysql_num_rows($res_maj)){
		$first=true;//Pour la premiere ligne de résultat on doit indexer aussi la notice de périodique et de bulletin au besoin
		while ( $ligne=pmb_mysql_fetch_object($res_maj) ) {
			if($first){
				if($ligne->bulletin_notice){
					notice::majNoticesMotsGlobalIndex($ligne->bulletin_notice,'expl');
				}
				if($ligne->num_notice){
					notice::majNoticesMotsGlobalIndex($ligne->num_notice,'expl');
				}
			}
			if($ligne->analysis_notice){
				notice::majNoticesMotsGlobalIndex($ligne->analysis_notice,'expl');
			}
			$first=false;
		}
	}
	
	$id_form = md5(microtime());
	print "<div class='row'><div class='msg-perio'>".$msg[maj_encours]."</div></div>";
	$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=$expl_bulletin";
	
	if ($pointage) {
		$templates="<script type='text/javascript'>
	
			function Fermer(obj,type_doc) {		
				var obj_1=obj+\"_1\";	
				var obj_2=obj+\"_2\";	
				var obj_3=obj+\"_3\";		
				parent.document.getElementById(obj_1).disabled = true;
				parent.document.getElementById(obj_2).disabled = true;
				parent.document.getElementById(obj_3).disabled = true;								
			 	parent.kill_frame_periodique();
			}	
	
		</script>
		<script type='text/javascript'>Fermer('$id_bull','$type_doc');</script>
		";
	} else {
		print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
		<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
		</form>
		<script type=\"text/javascript\">document.dummy.submit();</script>";
	}
}
?>