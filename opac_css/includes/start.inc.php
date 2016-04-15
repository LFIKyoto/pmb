<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start.inc.php,v 1.32.4.1 2015-09-28 15:23:35 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//header charset
if ($charset) header("Content-Type: text/html; charset=$charset");

// paramêtres par défaut de l'applic :
// ce système crée des variables de nom type_param_sstype_param et de contenu valeur_param à partir de la table parametres

/* param par défaut */	
$requete_param = "SELECT type_param, sstype_param, valeur_param FROM parametres  ";
// where type_param='opac'
$res_param = pmb_mysql_query($requete_param, $dbh);
while ($field_values = pmb_mysql_fetch_row( $res_param )) {
	$field = $field_values[0]."_".$field_values[1] ;
	global $$field;
	$$field = $field_values[2];
	}

//On a une petite histoire avec les URLs dans le portail !
if($cms_active && $_SESSION['cms_build_activate']){
	$opac_url_base = $cms_url_base_cms_build;
}

//if there isn't a custom class stored in the notice_affichage_class parameter 
//it's selected the default
if (!$opac_notice_affichage_class) $opac_notice_affichage_class="notice_affichage" ;

// afin que le séparateur de catégories soit correct partout mais visible à l'oeil nu en paramétrage :
$opac_categories_categ_path_sep	= ' '.htmlentities($opac_categories_categ_path_sep,ENT_QUOTES, $charset).' ';

// chargement de la feuille de style
if ($opac_css) { 
	$_SESSION["css"]=$opac_css; 
	$css=$opac_css;
} else if ($_SESSION["css"]!="") 
	$css=$_SESSION["css"];
else $css=$opac_default_style;

// vérification que le style demandé (éventuellement par l'url) est bien autorisé:
$tab_opac_authorized_styles = explode(',',$opac_authorized_styles);
$style_is_authorized = array_search ($css, $tab_opac_authorized_styles) ;
if (!($style_is_authorized!== FALSE && $style_is_authorized!== NULL)) $css=$opac_default_style;

// si aucune feuille de style n'est précisée, 
// chargement de la feuille 1/1.css
if (!$css) $css="1";

$_SESSION["css"]=$css;//Je mets en session le bon style Opac pour le cas ou le style demandé ne soit pas autorisé

// a language was selected so refresh cookie and set lang
if($lang_sel) {
	$rqtveriflang="select 1 from parametres where type_param='opac' and sstype_param='show_languages' and valeur_param like '%".$lang_sel."%'" ;
	$reqveriflang = pmb_mysql_query($rqtveriflang,$dbh);
	if (!pmb_mysql_num_rows($reqveriflang)) $lang_sel = $opac_default_lang;
	$expiration = time() + 30000000; /* 1 year */
	setcookie ('PhpMyBibli-LANG', $lang_sel, $expiration);
	$lang=$lang_sel;
	// if there is a user session we also change the language in PMB database for this user
	if ($_SESSION["user_code"]) {
		$query = "UPDATE empr SET empr_lang='$lang' WHERE empr_login='".$_SESSION['user_code']."' limit 1";
		$req = pmb_mysql_query($query,$dbh);
		$_SESSION["lang"] = $lang ;
	}
	
} else {
	// there is a user session so we use his params
	if (isset($_SESSION["lang"])) $lang=$_SESSION["lang"];
	else {
		// no changement,no session, we use the cookie to set the lang
		// cookies must be enabled to remember the lang...this must be changed ?
		if ($_COOKIE['PhpMyBibli-LANG']) {
			$rqtveriflang="select 1 from parametres where type_param='opac' and sstype_param='show_languages' and valeur_param like '%".pmb_mysql_real_escape_string(stripslashes($_COOKIE['PhpMyBibli-LANG']))."%'" ;
			$reqveriflang = pmb_mysql_query($rqtveriflang,$dbh);
			if (!pmb_mysql_num_rows($reqveriflang)) $lang = $opac_default_lang;
			else $lang=$_COOKIE['PhpMyBibli-LANG'];
		}
		if (!$lang) {
			if ($opac_default_lang) $lang = $opac_default_lang;
			else $lang = "fr_FR";
		}
	}
}

if (!$pmb_indexation_lang) $pmb_indexation_lang = $lang; 

if ($opac_search_results_per_page > $opac_max_results_on_a_page) $opac_search_results_per_page = $opac_max_results_on_a_page;

require_once($base_path."/includes/logs.inc.php");

