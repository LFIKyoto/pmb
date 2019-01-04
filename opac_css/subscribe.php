<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subscribe.php,v 1.47 2018-10-24 12:34:04 ngantier Exp $

$base_path=".";
$is_opac_included = false;

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if (!$opac_websubscribe_show) die("");
if(!isset($subsact)) $subsact = '';
if ($subsact=="validation" && (!$login || !$cle_validation)) die("");

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path."/includes/rec_history.inc.php");

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/common.tpl.php");
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");

require_once($base_path."/includes/notice_affichage.inc.php");

require_once($base_path."/classes/analyse_query.class.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");

//pour la localisation du lecteur
require_once($base_path."/classes/docs_location.class.php");

require_once($base_path.'/classes/quick_access.class.php');

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

if ($time_expired ==1) {
	echo "<script type='text/javascript' >alert(reverse_html_entities(\"".sprintf($msg["session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
} elseif ($time_expired==2) {
	echo "<script>alert(reverse_html_entities(\"".sprintf($msg["anonymous_session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
}

if (((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"])))&&($lvl!="show_cart"))
	print "<div id='resume_panier'><iframe recept='yes' recepttype='cart' frameborder='0' id='iframe_resume_panier' name='cart_info' allowtransparency='true' src='cart_info.php' scrolling='no' scrollbar='0'></iframe></div>";
else
	print "<div id='resume_panier' class='empty'></div>";

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
	if(isset($pageid) && $pageid) {
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
	$cms_build_info=rawurlencode(serialize(pmb_base64_encode($cms_build_info)));
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
		$loginform=str_replace('<!-- common_tpl_login_invite -->','<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>',$loginform);
		$loginform__ = genere_form_connexion_empr();
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','',$loginform);
		$loginform__.="<b class='logged_user_name'>".$empr_prenom." ".$empr_nom."</b><br />\n";
		if($opac_quick_access) {
			$loginform__.= quick_access::get_selector();
			$loginform__.="<br />";
		} else {
			$loginform__.="<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
		}
		if(!$opac_quick_access_logout || !$opac_quick_access){
			$loginform__.="<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
		}
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