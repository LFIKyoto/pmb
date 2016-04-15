<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z.inc.php,v 1.7.6.1 2015-11-24 14:27:07 jpermanne Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
                    
require_once($base_path."/classes/perio_a2z.class.php");

switch($sub){
	case 'get_onglet':
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
		ajax_http_send_response( $a2z->get_onglet($onglet_sel) );
	break;
	case 'get_perio':	
		$a2z=new perio_a2z($id,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);	
		if($pmb_logs_activate){
			//Enregistrement du log
			global $log;
				
			$rqt= " select empr_prof,empr_cp, empr_ville as ville, empr_year, empr_sexe, empr_login, empr_date_adhesion, empr_date_expiration, count(pret_idexpl) as nbprets, count(resa.id_resa) as nbresa, code.libelle as codestat, es.statut_libelle as statut, categ.libelle as categ, gr.libelle_groupe,dl.location_libelle as location
			from empr e
			left join empr_codestat code on code.idcode=e.empr_codestat
			left join empr_statut es on e.empr_statut=es.idstatut
			left join empr_categ categ on categ.id_categ_empr=e.empr_categ
			left join empr_groupe eg on eg.empr_id=e.id_empr
			left join groupe gr on eg.groupe_id=gr.id_groupe
			left join docs_location dl on e.empr_location=dl.idlocation
			left join resa on e.id_empr=resa_idempr
			left join pret on e.id_empr=pret_idempr
			where e.empr_login='".addslashes($_SESSION['user_code'])."'
			group by resa_idempr, pret_idempr";
			$res=pmb_mysql_query($rqt);
			if($res){
				$empr_carac = pmb_mysql_fetch_array($res);
				$log->add_log('empr',$empr_carac);
			}
		
			$log->add_log('num_session',session_id());
			
			$rqt="select notice_id, typdoc, niveau_biblio, index_l, libelle_categorie, name_pclass, indexint_name
				from notices n
				left join notices_categories nc on nc.notcateg_notice=n.notice_id
				left join categories c on nc.num_noeud=c.num_noeud
				left join indexint i on n.indexint=i.indexint_id
				left join pclassement pc on i.num_pclass=pc.id_pclass
				where notice_id='".$id."'";
			$res_noti = pmb_mysql_query($rqt);
			while(($noti=pmb_mysql_fetch_array($res_noti))){
				$infos_notice=$noti;
			}
			$log->add_log('docs',$infos_notice);
		
			//Enregistrement vue
			if($opac_opac_view_activate){
				$log->add_log('opac_view', $_SESSION["opac_view"]);
			}
		
			$log->save();
		}
		ajax_http_send_response($a2z->get_perio($id) );
	break;
	case 'reload':	
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);	
		ajax_http_send_response( $a2z->get_form(0,0,1) );
	break;
}

?>