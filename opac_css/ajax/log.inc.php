<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: log.inc.php,v 1.1.2.1 2015-08-13 07:30:45 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $log, $infos_notice, $infos_expl;

$rqt= " select empr_prof,empr_cp, empr_ville as ville, empr_year, empr_sexe, empr_login,  empr_date_adhesion, empr_date_expiration, count(pret_idexpl) as nbprets, count(resa.id_resa) as nbresa, code.libelle as codestat, es.statut_libelle as statut, categ.libelle as categ, gr.libelle_groupe as groupe,dl.location_libelle as location
			from empr e
			left join empr_codestat code on code.idcode=e.empr_codestat
			left join empr_statut es on e.empr_statut=es.idstatut
			left join empr_categ categ on categ.id_categ_empr=e.empr_categ
			left join empr_groupe eg on eg.empr_id=e.id_empr
			left join groupe gr on eg.groupe_id=gr.id_groupe
			left join docs_location dl on e.empr_location=dl.idlocation
			left join resa on e.id_empr=resa_idempr
			left join pret on e.id_empr=pret_idempr
			where e.empr_login='".addslashes($login)."'
			group by resa_idempr, pret_idempr";
$res=pmb_mysql_query($rqt);
if($res){
	$empr_carac = pmb_mysql_fetch_array($res);
	$log->add_log('empr',$empr_carac);
}
$log->add_log('num_session',session_id());

$log->save();