<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subscribe.php,v 1.27.2.3 2015-12-10 11:20:51 dbellamy Exp $

$base_path=".";
$is_opac_included = false;

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');

if (!$opac_websubscribe_show) die("");
if ($subsact=="validation" && (!$login || !$cle_validation)) die("");

require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

// fonctions de gestion de formulaire
require_once($base_path.'/includes/javascript/form.inc.php');

require_once($base_path.'/includes/divers.inc.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path."/includes/marc_tables/".$pmb_indexation_lang."/empty_words");
require_once($base_path."/includes/misc.inc.php");

require_once($base_path."/includes/rec_history.inc.php");

//si les vues sont activées (à laisser après le calcul des mots vides)
// Il n'est pas possible de changer de vue à ce niveau
if($opac_opac_view_activate){
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
}

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/common.tpl.php");
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/notice_authors.inc.php");
require_once($base_path."/includes/notice_categories.inc.php");

require_once($base_path."/includes/notice_affichage.inc.php");

require_once($base_path."/classes/analyse_query.class.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");

//pour la localisation du lecteur
require_once($base_path."/classes/docs_location.class.php");

// si paramétrage authentification particulière
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// pour les étagères et les nouveaux affichages
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/classes/notice_affichage.class.php");
require_once($base_path."/includes/etagere_func.inc.php");

require_once($base_path."/includes/websubscribe.inc.php");
require_once($base_path."/includes/mail.inc.php");

// RSS
require_once($base_path."/includes/includes_rss.inc.php");

if ($is_opac_included) {
	$std_header = $inclus_header ;
	$footer = $inclus_footer ;
}

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque
if ($opac_show_homeontop==1) $std_header= str_replace("!!home_on_top!!",$home_on_top,$std_header);
else $std_header= str_replace("!!home_on_top!!","",$std_header);

// mise à jour du contenu opac_biblio_main_header
$std_header= str_replace("!!main_header!!",$opac_biblio_main_header,$std_header);

// RSS
$std_header= str_replace("!!liens_rss!!",genere_link_rss(),$std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace("<!-- rss -->",$logo_rss_si_rss,$liens_bas);

$std_header = str_replace("!!enrichment_headers!!","",$std_header);
if($opac_parse_html || $cms_active){
	ob_start();
}


print $std_header;

if ($time_expired) echo "<script type='text/javascript' >alert(\"".sprintf($msg["session_expired"],round($opac_duration_session_auth/60))."\");</script>";

echo "<div id='websubscribe'>";

switch($subsact) {
	case 'validation':
		$verif=verif_validation_compte();
		echo $verif[1];
		break;
	case 'inscrire':
		if ($f_verifcode) {
			if (md5($f_verifcode) == $_SESSION['image_random_value']) {
				// set the session
				$_SESSION['image_is_logged_in'] = true;
				// remove the random value from session
				$_SESSION['image_random_value'] = '';
				$verif=verif_validite_compte();
				echo $verif[1];
			} else {
				// set the session
				$_SESSION['image_is_logged_in'] = false;
				// remove the random value from session
				$_SESSION['image_random_value'] = '';
				// Raté on repart...
				echo $msg['subs_pb_wrongcode'] ;
				echo generate_form_inscription() ;
			}
		} else {
			// vide
			echo $msg['subs_pb_wrongcode'] ;
			echo generate_form_inscription() ;
		}
		break;
	case '':
	default:
		$subsact='';
		echo $msg['subs_intro_services'];
		echo str_replace("!!nb_h_valid!!",$opac_websubscribe_valid_limit,$msg['subs_intro_explication']);
		echo generate_form_inscription() ;
		break;
	}

echo "</div>";

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) $footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
	else $footer = str_replace("!!div_liens_bas!!","",$footer);

$cms_build_info="";
if($cms_build_activate == -1){
	unset($_SESSION["cms_build_activate"]);
}else if($cms_build_activate || $_SESSION["cms_build_activate"]){ // issu de la gestion
	if($pageid){
		require_once($base_path."/classes/cms/cms_pages.class.php");
		$cms_page= new cms_page($pageid);
		$cms_build_info['page']=$cms_page->get_env();
	}
	global $log, $infos_notice, $infos_expl, $nb_results_tab;
	$cms_build_info['input']="subscribe.php";
	$cms_build_info['session']=$_SESSION;
	$cms_build_info['post']=$_POST;
	$cms_build_info['get']=$_GET;
	$cms_build_info['lvl']=$lvl;
	$cms_build_info['tab']=$tab;
	$cms_build_info['log']=$log;
	$cms_build_info['infos_notice']=$infos_notice;
	$cms_build_info['infos_expl']=$infos_expl;
	$cms_build_info['nb_results_tab']=$nb_results_tab;
	$cms_build_info['search_type_asked']=$search_type_asked;
	$cms_build_info=rawurlencode(serialize($cms_build_info));
	$cms_build_info= "<input type='hidden' id='cms_build_info' name='cms_build_info' value='".$cms_build_info."' />";
	$cms_build_info.="
	<script type='text/javascript'>
		if(window.top.window.cms_opac_loaded){
			window.onload = function() {
				window.top.window.cms_opac_loaded('".$_SERVER['REQUEST_URI']."');
			}
		}
	</script>
	";
	$_SESSION["cms_build_activate"]="1";
}
$footer=str_replace("!!cms_build_info!!",$cms_build_info,$footer);

//affichage du bandeau_2 si $opac_show_bandeau_2 = 1
if ($opac_show_bandeau_2==0) {
	$bandeau_2_contains= "";
} else {
	$bandeau_2_contains= "<div id=\"bandeau_2\">!!contenu_bandeau_2!!</div>";
}
//si ce n'est pas un popup qui est affiché, alors on affiche $footer
if ($opac_show_bandeaugauche==0) {
	$footer= str_replace("!!contenu_bandeau!!",$bandeau_2_contains,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
} else {
	$footer = str_replace("!!contenu_bandeau!!","<div id=\"bandeau\">!!contenu_bandeau!!</div>.$bandeau_2_contains",$footer);
	$home_on_left=str_replace("!!welcome_page!!",$msg["welcome_page"],$home_on_left);
	$adresse=str_replace("!!common_tpl_address!!",$msg["common_tpl_address"],$adresse);
	$adresse=str_replace("!!common_tpl_contact!!",$msg["common_tpl_contact"],$adresse);

	// loading the languages available in OPAC - martizva >> Eric
	require_once($base_path.'/includes/languages.inc.php');
	$home_on_left = str_replace("!!common_tpl_lang_select!!", show_select_languages("index.php"), $home_on_left);


	if (!$_SESSION["user_code"]) {
		$loginform=str_replace('<!-- common_tpl_login_invite -->',$msg["common_tpl_login_invite"],$loginform);
		$loginform__ = genere_form_connexion_empr();
	} else {
		$loginform__.="<b>".$empr_prenom." ".$empr_nom."</b><br />\n";
		$loginform__.="<select name='empr_quick_access' onchange='if (this.value) window.location.href=this.value'>
				<option value=''>".$msg["empr_quick_access"]."</option>
				<option value='empr.php'>".$msg["empr_my_account"]."</option>";
		if ($allow_loan || $allow_loan_hist) {
			$loginform__.="<option value='empr.php?tab=loan_reza&lvl=all#empr-loan'>".$msg["empr_my_loans"]."</option>";
		}
		if ($allow_book && $opac_resa) {
			$loginform__.="<option value='empr.php?tab=loan_reza&lvl=all#empr-resa'>".$msg["empr_my_resas"]."</option>";
		}
		if ($opac_demandes_active && $allow_dema) {
			$loginform__.="<option value='empr.php?tab=request&lvl=list_dmde'>".$msg["empr_my_dmde"]."</option>";
		}
		$loginform__.="</select><br />";
		$loginform__.="<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
	}
	$loginform = str_replace("!!login_form!!",$loginform__,$loginform);
	$footer= str_replace("!!contenu_bandeau!!",($opac_accessibility ? $accessibility : "").$home_on_left.$loginform.$meteo.$adresse,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
}

print $footer;

if($opac_parse_html || $cms_active){
	if($opac_parse_html){
		$htmltoparse= parseHTML(ob_get_contents());
	}else{
		$htmltoparse= ob_get_contents();
	}

	ob_end_clean();
	if ($cms_active) {
		require_once($base_path."/classes/cms/cms_build.class.php");
		$cms=new cms_build();
		$htmltoparse = $cms->transform_html($htmltoparse);
	}
	//Compression CSS
	if($opac_compress_css == 1 && !$cms_active){
		$compressed_file_exist = file_exists("./temp/full.css");
		require_once($class_path."/curl.class.php");
		$dom = new DOMDocument();
		$dom->encoding = $charset;
		$dom->loadHTML($htmltoparse);
		$css_buffer = "";
		$links = $dom->getElementsByTagName("link");
		$dom_css = array();
		for($i=0 ; $i<$links->length ; $i++){
			$dom_css[] = $links->item($i);
			if(!$compressed_file_exist && $links->item($i)->hasAttribute("type") && $links->item($i)->getAttribute("type") == "text/css"){
				$css_buffer.= loadandcompresscss(html_entity_decode($links->item($i)->getAttribute("href")));
			}
		}
		$styles = $dom->getElementsByTagName("style");
		for($i=0 ; $i<$styles->length ; $i++){
			$dom_css[] = $styles->item($i);
			if(!$compressed_file_exist){
				$css_buffer.= compresscss($styles->item($i)->nodeValue,"");
			}
		}
		foreach($dom_css as $link){
			$link->parentNode->removeChild($link);
		}
		if(!$compressed_file_exist){
			file_put_contents("./temp/full.css",$css_buffer);
		}
		$link = $dom->createElement("link");
		$link->setAttribute("href", "./temp/full.css");
		$link->setAttribute("rel", "stylesheet");
		$link->setAttribute("type", "text/css");
		$dom->getElementsByTagName("head")->item(0)->appendChild($link);
		$htmltoparse = $dom->saveHTML();
	}else if (file_exists("./temp/full.css") && !$cms_active){
		unlink("./temp/full.css");
	}
	print $htmltoparse;
}
pmb_mysql_close($dbh);