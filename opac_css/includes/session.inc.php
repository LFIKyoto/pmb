<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.inc.php,v 1.32.2.1 2015-10-07 12:46:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/sessions.inc.php");

if(basename($_SERVER['SCRIPT_FILENAME']) !== "cms_vign.php"){
	header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: post-check=0, pre-check=0",false);
}
session_set_cookie_params("");
session_cache_limiter('must-revalidate');

session_start();

$check_empr = checkEmpr("PmbOpac");
if (!$check_empr) {
	unset($_SESSION["user_code"]);
}

$logout = $_GET["logout"];

if (!isset($logout)) $logout=0;

//Sauvegarde de l'environnement
if ($_SESSION["user_code"]) {
	$requete="replace into opac_sessions (empr_id,session) values(".$_SESSION["id_empr_session"].",'".addslashes(serialize($_SESSION))."')";
	pmb_mysql_query($requete);
}

//Si logout = 1, destruction de la session
if ($logout) { 
	if($_SESSION["cms_build_activate"])$cms_build_activate=1;	
	if($_SESSION["build_id_version"])$build_id_version=$_SESSION["build_id_version"];
	$_SESSION=array();
	if(!$cms_build_activate){		
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		sessionDelete("PmbOpac");
	}
	$_SESSION["cms_build_activate"]=$cms_build_activate;
	$_SESSION["build_id_version"]=$build_id_version;
	
}

//Si session en cours, récupération des préférences utilisateur
if ($_SESSION["user_code"]) {
	
	if($_SESSION["user_expired"] ){
		$req_param = "select valeur_param from parametres where sstype_param='adhesion_expired_status' and type_param='opac'";
		$res_param = pmb_mysql_query($req_param,$dbh);
		if($res_param && pmb_mysql_result($res_param,0,0)){
			$req = "select * from empr_statut where idstatut='".pmb_mysql_result($res_param,0,0)."'";
			$res = pmb_mysql_query($req,$dbh);
			$data_expired = pmb_mysql_fetch_array($res);
			$droit_loan= $data_expired['allow_loan'];
			$droit_loan_hist= $data_expired['allow_loan_hist'];
			$droit_book= $data_expired['allow_book'];
			$droit_opac= $data_expired['allow_opac'];
			$droit_dsi= $data_expired['allow_dsi'];
			$droit_dsi_priv= $data_expired['allow_dsi_priv'];
			$droit_sugg= $data_expired['allow_sugg'];
			$droit_dema= $data_expired['allow_dema'];
			$droit_prol= $data_expired['allow_prol'];
			$droit_avis= $data_expired['allow_avis'];
			$droit_tag= $data_expired['allow_tag'];
			$droit_pwd= $data_expired['allow_pwd'];
			$droit_liste_lecture = $data_expired['allow_liste_lecture'];
		}	else {
			$droit_loan= 1;
			$droit_loan_hist=1;
			$droit_book= 1;
			$droit_opac= 1;
			$droit_dsi= 1;
			$droit_dsi_priv=1;
			$droit_sugg= 1;
			$droit_dema= 1;
			$droit_prol= 1;
			$droit_avis=1 ;
			$droit_tag= 1;
			$droit_pwd= 1;
			$droit_liste_lecture = 1;
		}		
	} else {
		$droit_loan= 1;
		$droit_loan_hist=1;
		$droit_book= 1;
		$droit_opac= 1;
		$droit_dsi= 1;
		$droit_dsi_priv=1;
		$droit_sugg= 1;
		$droit_dema= 1;
		$droit_prol= 1;
		$droit_avis=1 ;
		$droit_tag= 1;
		$droit_pwd= 1;
		$droit_liste_lecture = 1;
	}
	//Préférences utilisateur
	$query0 = "select * from empr, empr_statut where empr_login='".$_SESSION['user_code']."' and idstatut=empr_statut limit 1";
	$req0 = pmb_mysql_query($query0,$dbh);
	$data = pmb_mysql_fetch_array($req0);
	$id_empr = $data['id_empr'];
	$empr_cb = $data['empr_cb'];
	$empr_nom = $data['empr_nom'];
	$empr_prenom= $data['empr_prenom'];
	$empr_adr1= $data['empr_adr1'];
	$empr_adr2= $data['empr_adr2'];
	$empr_cp= $data['empr_cp'];
	$empr_ville= $data['empr_ville'];
	$empr_mail= $data['empr_mail'];
	$empr_tel1= $data['empr_tel1'];
	$empr_tel2= $data['empr_tel2'];
	$empr_prof= $data['empr_prof'];
	$empr_year= $data['empr_year'];
	$empr_categ= $data['empr_categ'];
	$empr_codestat= $data['empr_codestat'];
	$empr_sexe= $data['empr_sexe'];
	$empr_login= $data['empr_login'];
	$empr_password= $data['empr_password'];
	$empr_location= $data['empr_location'];
	
	// droits de l'utilisateur
	$allow_loan= $data['allow_loan'] & $droit_loan;
	$allow_loan_hist= $data['allow_loan_hist'] & $droit_loan_hist;
	$allow_book= $data['allow_book'] & $droit_book;
	$allow_opac= $data['allow_opac'] & $droit_opac;
	$allow_dsi= $data['allow_dsi'] & $droit_dsi;
	$allow_dsi_priv= $data['allow_dsi_priv'] & $droit_dsi_priv;
	$allow_sugg= $data['allow_sugg'] & $droit_sugg;
	$allow_dema= $data['allow_dema'] & $droit_dema;
	$allow_prol= $data['allow_prol'] & $droit_prol;
	$allow_avis= $data['allow_avis'] & $droit_avis;
	$allow_tag= $data['allow_tag'] & $droit_tag;
	$allow_pwd= $data['allow_pwd'] & $droit_pwd;
	$allow_liste_lecture = $data['allow_liste_lecture'] & $droit_liste_lecture;
}else{
	//pas de session authentifiée... AR veut une trace quand même
	check_anonymous_session('PmbOpac');
}

// message de debug messages ?
if ($check_messages==-1) $_SESSION["CHECK-MESSAGES"] = 0;
if ($check_messages==1) $_SESSION["CHECK-MESSAGES"] = 1;
	