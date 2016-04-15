<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: doc_num.php,v 1.46.2.2 2015-08-14 08:04:07 jpermanne Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once('./includes/opac_config.inc.php');

// récupération paramètres MySQL et connection á la base
require_once('./includes/opac_db_param.inc.php');
require_once('./includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premer include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

if ($css=="") $css=1;
	
require_once('./includes/start.inc.php');

require_once("./includes/check_session_time.inc.php");

// récupération localisation
require_once('./includes/localisation.inc.php');

// version actuelle de l'opac
require_once('./includes/opac_version.inc.php');

require_once ("./includes/explnum.inc.php");  

require_once ($class_path."/upload_folder.class.php"); 

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	$current_opac_view=$_SESSION["opac_view"];
	if($opac_view==-1){
		$_SESSION["opac_view"]="default_opac";
	}else if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view*1;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");

	$opac_view_class= new $pmb_opac_view_class($_SESSION["opac_view"],$_SESSION["id_empr_session"]);
	if($opac_view_class->id){
		$opac_view_class->set_parameters();
		$opac_view_filter_class=$opac_view_class->opac_filters;
		$_SESSION["opac_view"]=$opac_view_class->id;
		if(!$opac_view_class->opac_view_wo_query) {
			$_SESSION['opac_view_query']=1;
		}
	} else {
		$_SESSION["opac_view"]=0;
	}
	$css=$_SESSION["css"]=$opac_default_style;
	if ($opac_view) {
		if ($current_opac_view!=$opac_view*1) {
			//on change de vue donc :
			//on stocke le tri en cours pour la vue en cours
			$_SESSION["last_sortnotices_view_".$current_opac_view]=$_SESSION["last_sortnotices"];
			if (isset($_SESSION["last_sortnotices_view_".($opac_view*1)])) {
				//on a déjà un tri pour la nouvelle vue, on l'applique
				$_SESSION["last_sortnotices"] = $_SESSION["last_sortnotices_view_".($opac_view*1)];
			} else {
				unset($_SESSION["last_sortnotices"]);
			}
			//comparateur de facettes : on ré-initialise
			require_once($base_path.'/classes/facette_search_compare.class.php');
			facette_search_compare::session_facette_compare(null,true);
		}
	}
}

//gestion des droits
require_once($class_path."/acces.class.php");

//si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// Si l'on vient d'une DSI avec une connexion auto
if($code) {
	// pour fonction de vérification de connexion
	require_once($base_path.'/includes/empr_func.inc.php');
	$log_ok=connexion_empr();
	if($log_ok) $_SESSION["connexion_empr_auto"]=1;
}

/**
 * Récupère les infos du document numérique
 */
function recup_explnum_infos($id_explnum){
	
	global $dbh, $infos_explnum;
	
	$rqt_explnum = "SELECT explnum_id, explnum_notice, explnum_bulletin, IF(location_libelle IS null, '', location_libelle) AS location_libelle, explnum_nom, explnum_mimetype, explnum_url, explnum_extfichier, IF(explnum_nomfichier IS null, '', explnum_nomfichier) AS nomfichier, explnum_path, IF(rep.repertoire_nom IS null, '', rep.repertoire_nom) AS nomrepertoire
		from explnum ex_n
		LEFT JOIN explnum_location ex_l ON ex_n.explnum_id= ex_l.num_explnum
		LEFT JOIN docs_location dl ON ex_l.num_location= dl.idlocation
		LEFT JOIN upload_repertoire rep ON ex_n.explnum_repertoire= rep.repertoire_id
		where explnum_id='".$id_explnum."'";
	$res_explnum=pmb_mysql_query($rqt_explnum,$dbh);
	while(($explnum = pmb_mysql_fetch_array($res_explnum,MYSQL_ASSOC))){
		$infos_explnum[]=$explnum;
	}
}

$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_url, 
			explnum_data, explnum_extfichier, explnum_path, concat(repertoire_path,explnum_path,explnum_nomfichier) as path, repertoire_id
			FROM explnum left join upload_repertoire on repertoire_id=explnum_repertoire WHERE explnum_id = '$explnum_id' ";
$resultat = pmb_mysql_query($requete,$dbh);
$nb_res = pmb_mysql_num_rows($resultat) ;


if (!$nb_res) {
	header("Location: images/mimetype/unknown.gif");
	exit ;
} 
	
$ligne = pmb_mysql_fetch_object($resultat);

$id_for_rigths = $ligne->explnum_notice;
if($ligne->explnum_bulletin != 0){
	//si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
	$res = pmb_mysql_query($req,$dbh);
	if(pmb_mysql_num_rows($res)){
		$row = pmb_mysql_fetch_object($res);
		$id_for_rigths = $row->num_notice;
		if(!$id_for_rigths){
			$id_for_rigths = $row->bulletin_notice;
		}
	}$type = "" ;
}


//droits d'acces emprunteur/notice
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	$ac= new acces();
	$dom_2= $ac->setDomain(2);
	$rights= $dom_2->getRights($_SESSION['id_empr_session'],$id_for_rigths);
}

//Accessibilité des documents numériques aux abonnés en opac
$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='".$id_for_rigths."' AND statut=id_notice_statut ";

$result=pmb_mysql_query($req_restriction_abo,$dbh);
$expl_num=pmb_mysql_fetch_array($result,MYSQL_ASSOC);

//droits d'acces emprunteur/document numérique
if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	$ac= new acces();
	$dom_3= $ac->setDomain(3);
	$docnum_rights= $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id);
}

//Accessibilité (Consultation/Téléchargement) sur le document numérique aux abonnés en opac 
$req_restriction_docnum_abo = "SELECT explnum_download_opac, explnum_download_opac_abon FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";

$result_docnum=pmb_mysql_query($req_restriction_docnum_abo,$dbh);
$docnum_expl_num=pmb_mysql_fetch_array($result_docnum,MYSQL_ASSOC);

		
if( ($rights & 16 || (is_null($dom_2) && $expl_num["explnum_visible_opac"] && (!$expl_num["explnum_visible_opac_abon"] || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"]))))
	&& ($docnum_rights & 8 || (is_null($dom_3) && $docnum_expl_num["explnum_download_opac"] && (!$docnum_expl_num["explnum_download_opac_abon"] || ($docnum_expl_num["explnum_download_opac_abon"] && $_SESSION["user_code"]))))){
	if (($ligne->explnum_data)||($ligne->explnum_path)) {
		if($pmb_logs_activate){
			//Récupération des informations du document numérique
			recup_explnum_infos($explnum_id);
			//Enregistrement du log
			global $log, $infos_explnum;
				
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
			where e.empr_login='".addslashes($_SESSION['user_code'])."'
			group by resa_idempr, pret_idempr";
			$res=pmb_mysql_query($rqt,$dbh);
			if($res){
				$empr_carac = pmb_mysql_fetch_array($res);
				$log->add_log('empr',$empr_carac);
			}
		
			$log->add_log('num_session',session_id());
			$log->add_log('explnum',$infos_explnum);
			$infos_restriction_abo = array();
			foreach ($expl_num as $key=>$value) {
				$infos_restriction_abo[$key] = $value;
			}
			$log->add_log('restriction_abo',$infos_restriction_abo);
			$infos_restriction_docnum_abo = array();
			foreach ($docnum_expl_num as $key=>$value) {
				$infos_restriction_docnum_abo[$key] = $value;
			}
			$log->add_log('restriction_docnum_abo',$infos_restriction_docnum_abo);
		
			$log->save();
		}
		
		if ($ligne->explnum_path) {
			$up = new upload_folder($ligne->repertoire_id);
			$path = str_replace("//","/",$ligne->path);
			$path=$up->encoder_chaine($path);
			if(file_exists($path) && filesize($path)){
				$fo = fopen($path,'rb');
			}else{
				$fo=false;
				header("Location: images/mimetype/unknown.gif");
				exit ;
			}
			if ($fo && (substr($ligne->explnum_mimetype,0,5)=="image")&&($opac_photo_watermark)) {
				$ligne->explnum_data=fread($fo,filesize($path));
				fclose($fo);
			} else $ligne->explnum_data="";
		}
		
		create_tableau_mimetype() ;
		$name=$_mimetypes_bymimetype_[$ligne->explnum_mimetype]["plugin"] ;
		if ($name) {
			$type = "" ;
			// width='700' height='525' 
			$name = " name='$name' ";
		}
		$type="type='$ligne->explnum_mimetype'" ;
		
		if ($_mimetypes_bymimetype_[$ligne->explnum_mimetype]["embeded"]=="yes") {
			print "<html><body><EMBED src=\"./doc_num_data.php?explnum_id=$explnum_id\" $type $name controls='console' ></EMBED></body></html>" ;
			if ($fo) fclose($fo);
			exit ;
		}
		
		$nomfichier="";
		if ($ligne->explnum_nomfichier) {
			$nomfichier=$ligne->explnum_nomfichier;
		} elseif ($ligne->explnum_extfichier)
			$nomfichier="pmb".$ligne->explnum_id.".".$ligne->explnum_extfichier;
		if ($nomfichier) header("Content-Disposition: inline; filename=".$nomfichier);
		
		if ((substr($ligne->explnum_mimetype,0,5)=="image")&&($opac_photo_watermark)) {
			$content_image=reduire_image_middle($ligne->explnum_data);
			if ($content_image) {
				print header("Content-Type: image/png");
				print $content_image;
			} else {
				header("Content-Type: ".$ligne->explnum_mimetype);
				print $ligne->explnum_data;
			}
		} else {
			header("Content-Type: ".$ligne->explnum_mimetype);
			if (($fo)&&($ligne->explnum_path)&&(!$ligne->explnum_data)) {
				while(!feof($fo)){
					print fread($fo,4096);
				}
				//fpassthru($fo);
				fclose($fo);
			} else print $ligne->explnum_data;
		}
		exit ;
	}
	
	if ($ligne->explnum_mimetype=="URL") {
		if ($ligne->explnum_url){
			if($pmb_logs_activate){
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
				$log->get_log["called_url"] = $ligne->explnum_url;
				$log->get_log["type_url"] = "external_url_docnum";
				$log->save();
			}
			header("Location: $ligne->explnum_url");
		}
		exit ;
	}
}else{
	if(!$_SESSION['id_empr_session']){
		require_once($base_path."/includes/templates/common.tpl.php");
		$short_header = str_replace("!!liens_rss!!","",$short_header);
		print $short_header;
		require_once($class_path."/auth_popup.class.php");
		print "<div id='att'></div>
				<script type='text/javascript' src='".$include_path."/javascript/auth_popup.js'></script>";
		print "<script type='text/javascript'>
				auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=docnum_refresh');
				function docnum_refresh(){
					window.location.reload();
				}
			</script>";
	}else{
		print $msg['forbidden_docnum'];
	}
}
