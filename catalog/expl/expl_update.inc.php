<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_update.inc.php,v 1.28 2015-04-16 11:39:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/notice.class.php");
// Pour l'indexation des concepts
require_once($class_path."/index_concept.class.php");

$expl_id="";

//Vérification des champs personalisés
$p_perso=new parametres_perso("expl");
$nberrors=$p_perso->check_submited_fields();
if ($nberrors) {
	error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
	exit();
}

switch($sub) {
	case 'create':
		$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$f_ex_cb' ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		$nbr_lignes ? $valid_requete = FALSE : $valid_requete = TRUE;
		$requete = "INSERT INTO exemplaires SET create_date=sysdate(), ";
		$limiter = "";
		$libelle = $msg[4007];
		break;
	case 'update':
		// ceci teste si l'exemplaire cible existe bien
		$requete = "SELECT expl_id FROM exemplaires WHERE expl_cb='$org_cb' ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_num_rows($res);
		$nbr_lignes ? $valid_requete = TRUE : $valid_requete = FALSE;
		if ($nbr_lignes) $expl_id = pmb_mysql_result($res,0,0);
		 
		// remplacement code-barre : test sur le nouveau numéro
		if($org_cb != $f_ex_cb) {
			$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$f_ex_cb' ";
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			$nbr_lignes ? $valid_requete = FALSE : $valid_requete = TRUE;
			}
		$requete = "UPDATE exemplaires SET ";
		$limiter = " WHERE expl_cb='${org_cb}' ";
		$libelle = $msg[4007];
		break;
}

print pmb_bidi("<div class=\"row\"><h1>$libelle</h1></div>");

if(!is_numeric($f_ex_nbparts) || !$f_ex_nbparts) $f_ex_nbparts=1;

$formlocid="f_ex_section".$f_ex_location ;
$f_ex_section=$$formlocid;
$transfert_origine="";
if($expl_id){
	$rqt = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert and etat_transfert=0 AND num_expl='".$expl_id."' " ;
	$res = pmb_mysql_query( $rqt );
	if (!pmb_mysql_num_rows($res)){
		// pas de transfert en cours, on met à jour transfert_location_origine
		$transfert_origine= ", transfert_location_origine='$f_ex_location', transfert_statut_origine='$f_ex_statut', transfert_section_origine='$f_ex_section' ";
	}
}else{
	// en création
	$transfert_origine= ", transfert_location_origine='$f_ex_location', transfert_statut_origine='$f_ex_statut', transfert_section_origine='$f_ex_section' ";
}

if($expl_id){
	$audit=new audit();
	$audit->get_old_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='$f_ex_cb' ");
}

if($valid_requete) {
	$requete .= "expl_cb='${f_ex_cb}'";
	$requete .= ", expl_notice=${id}";
	$requete .= ", expl_typdoc=${f_ex_typdoc}";
	$requete .= ", expl_cote=trim('${f_ex_cote}')";
	$requete .= ", expl_section='".$f_ex_section."'";
	$requete .= ", expl_statut='${f_ex_statut}'";
	$requete .= ", expl_location='$f_ex_location' $transfert_origine ";
	$requete .= ", expl_codestat='${f_ex_cstat}'";
	$requete .= ", expl_note='".${f_ex_note}."'";
	$requete .= ", expl_comment='".${f_ex_comment}."'";
	$requete .= ", expl_prix='${f_ex_prix}'";
	$requete .= ", expl_owner='${f_ex_owner}'";
	$requete .= ", type_antivol='${type_antivol}'";
	$requete .= ", expl_nbparts='${f_ex_nbparts}'";
	$requete .= $limiter;
	$result = pmb_mysql_query($requete, $dbh);
	if (!$expl_id) {
		$expl_id=pmb_mysql_insert_id();
		audit::insert_creation (AUDIT_EXPL, $expl_id) ;
	} else{
		$audit->get_new_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='$f_ex_cb' ");
		$audit->save_info_modif(AUDIT_EXPL, $expl_id,"expl_update.inc.php");
	}
	
	// traitement des concepts
	if($thesaurus_concepts_active == 1){
		$index_concept = new index_concept($expl_id, TYPE_EXPL);
		$index_concept->save();
	}
	
	//Insertion des champs personalisés
	$p_perso->rec_fields_perso($expl_id);
	
	// Mise a jour de la table notices_mots_global_index
	notice::majNoticesMotsGlobalIndex($id,'expl');	
	
	// tout va bene, on réaffiche l'ISBD
	print "<div class='row'><div class='msg-perio'>".$msg[maj_encours]."</div></div>";
	$id_form = md5(microtime());
	$retour = "./catalog.php?categ=isbd&id=$id";
	print "
		<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
		</form>
		<script type=\"text/javascript\">document.dummy.submit();</script>
		";
} else {
	error_message($msg[301], $msg[303], 1, "./catalog.php?categ=isbd&id=$id");
}
?>
