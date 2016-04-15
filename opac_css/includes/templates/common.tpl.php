<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: common.tpl.php,v 1.198.2.6 2015-12-03 16:18:52 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($class_path."/sort.class.php");

// template for PMB OPAC

// éléments standards pour les pages :
// $short_header
// $std_header
//
//$footer qui contient
//	$liens_bas : barre de liens bibli, google, pmb
//	contenu du div bandeau (bandeau de gauche) soit
//		$home
//		$loginform
//		$meteo
//		$adresse
//
//Classes et IDs utilisés dans l'OPAC
//
//Tout est contenu dans #container
//
//Partie gauche (menu)
//	#bandeau
//		#accueil
//		#connexion
//		#meteo
//		#addresse
//		
//Partie droite (principale)
//	#intro (tout le bloc incluant pmb, nom de la bibli, message d'accueil)
//		#intro_pmb : pmb
//		#intro_message : message d'information s'il existe
//		#intro_bibli
//			h3 : nom de la bibli
//			p .intro_bibli_presentation_1 : texte de présentation de la bibli
//	
//	#main : contient les différents blocs d'affichage et de recherches (browsers)
//		div
//			h3 : nom du bloc
//			contenu du bloc
					
//	récupère les feuilles de styles du répertoire /styles/$css/
function link_styles($style) {
	// où $rep = répertoire de stockage des feuilles
	// retourne un tableau indexé avec les noms des CSS disponibles
	
	global $charset;
	
	$rep = './styles/';
	
	if(!preg_match('/\/$/', $rep)) $rep .= '/';
	
	$feuilles_style="";
	$handle = @opendir($rep."common");	
	$allfiles = array();
	if($handle) {
		while($css = readdir($handle)) {
			$allfiles[] = $css;			
		}
		closedir($handle);
		
		sort($allfiles);
		foreach($allfiles as $css) {
			if(is_file($rep."common/".$css) && preg_match('/css$/', $css)) {
				$result[] = $css;
				$vide_cache=@filemtime($rep."common/".$css);
				$feuilles_style.="\n\t<link rel='stylesheet' type='text/css' href='".$rep."common/".$css."?".$vide_cache."' />";
			}
		}
	}
			
	$handle = @opendir($rep.$style);	
	if(!$handle) {
		$result = array();
		return $result;
	}
	$allfiles = array();
	while($css = readdir($handle)) {
		$allfiles[] = $css;				
	}
	sort($allfiles);
	foreach($allfiles as $css) {
		if(is_file($rep.$style."/".$css) && preg_match('/css$/', $css)) {
			$result[] = $css;
			$vide_cache=@filemtime($rep.$style."/".$css);
			$feuilles_style.="\n\t<link rel='stylesheet' type='text/css' href='".$rep.$style."/".$css."?".$vide_cache."' />";
	    }
	}
	closedir($handle);
	return $feuilles_style;
}


//Récupération du login
if (!$_SESSION["user_code"]) {
	//Si pas de session
	$cb_=$msg['common_tpl_cardnumber_default'];
} else {
	//Récupération des infos de connection
	$cb_=$_SESSION["user_code"];
}
$stylescsscodehtml=link_styles($css);
//HEADER : short_header = pour les popups
//         std_header = pour les pages standards

// pb de resize de page avec IE6 et 7 : on force le rechargement de la page (position absolue qui reste absolue !)
if ($opac_ie_reload_on_resize) $iecssresizepb="onresize=\"history.go(0);\"";

if ($opac_default_style_addon) $css_addon = "
	<style type='text/css'>
	".$opac_default_style_addon."
		</style>";
else $css_addon="";


$std_header.="
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"
    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" charset='".$charset."'>
<head>
	<meta http-equiv=\"content-type\" content=\"text/html; charset=$charset\" />
	<meta name=\"author\" content=\"".($opac_meta_author?htmlentities($opac_meta_author,ENT_QUOTES,$charset):"PMB Group")."\" />

	<meta name=\"keywords\" content=\"".($opac_meta_keywords?htmlentities($opac_meta_keywords,ENT_QUOTES,$charset):$msg['opac_keywords'])."\" />
	<meta name=\"description\" content=\"".($opac_meta_description?htmlentities($opac_meta_description,ENT_QUOTES,$charset):$msg['opac_title']." $opac_biblio_name.")."\" />

	<meta name=\"robots\" content=\"all\" />
	<!--IE et son enfer de compatibilité-->
	<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
			
	<title>".$msg['opac_title']." $opac_biblio_name</title>
	!!liens_rss!!
	".$stylescsscodehtml.$css_addon."
	<!-- css_authentication -->";
// FAVICON
if ($opac_faviconurl) $std_header.="	<link rel='SHORTCUT ICON' href='".$opac_faviconurl."'>";
else $std_header.="	<link rel='SHORTCUT ICON' href='images/site/favicon.ico'>";
$std_header.="
	<script type=\"text/javascript\" src=\"includes/javascript/drag_n_drop.js\"></script>
	<script type=\"text/javascript\" src=\"includes/javascript/handle_drop.js\"></script>
	<script type=\"text/javascript\" src=\"includes/javascript/popup.js\"></script>
	<script type='text/javascript'>
	  	if (!document.getElementsByClassName){ // pour ie
			document.getElementsByClassName = 
			function(nom_class){
				var items=new Array();
				var count=0;
				for (var i=0; i<document.getElementsByTagName('*').length; i++) {  
					if (document.getElementsByTagName('*').item(i).className == nom_class) {
						items[count++] = document.getElementsByTagName('*').item(i); 
				    }
				 }
				return items;
			 }
		}
		// Fonction a utilisier pour l'encodage des URLs en javascript
		function encode_URL(data){
			var docCharSet = document.characterSet ? document.characterSet : document.charset;
			if(docCharSet == \"UTF-8\"){
				return encodeURIComponent(data);
			}else{
				return escape(data);
			}
		}
	</script>
";

if($opac_map_activate){
	switch($opac_map_base_layer_type){
		case "GOOGLE" :
			$std_header.="<script src='http://maps.google.com/maps/api/js?v=3&amp;sensor=false'></script>";
			break;
	}
	$std_header.="<link rel='stylesheet' type='text/css' href='".$javascript_path."/openlayers/theme/default/style.css'/>";
	$std_header.="<script type='text/javascript' src='".$javascript_path."/openlayers/lib/OpenLayers.js'></script>";
}
$std_header.="
<link rel='stylesheet' type='text/css' href='".$javascript_path."/dojo/dijit/themes/tundra/tundra.css' />
<script type='text/javascript'>
	var dojoConfig = {
		parseOnLoad: true,
		locale: '".str_replace("_","-",strtolower($lang))."',
		isDebug: false,
		usePlainJson: true,
		packages: [{
			name: 'pmbBase',
			location:'../../..'
		}],
		deps: ['apps/pmb/MessagesStore'],
		callback:function(MessagesStore){
			window.pmbDojo = {};
			pmbDojo.messages = new MessagesStore({url:'./ajax.php?module=ajax&categ=messages', directInit:false});
		
		},
	};
</script>
<script type='text/javascript' src='".$javascript_path."/dojo/dojo/dojo.js'>
</script><script type='text/javascript' src='".$javascript_path."/dojo/dojo/pmbdojo.js'></script>
";

if (!$_COOKIE['PhpMyBibli-COOKIECONSENT']) {
	if ($opac_cookies_consent && ($opac_script_analytics || $opac_show_social_network || $pmb_logs_activate)) {
		$std_header.="
		<script type='text/javascript'>
			var msg_script_analytics_content = '".addslashes($msg["script_analytics_content"])."';
			var msg_script_analytics_inform_title = '".addslashes($msg["script_analytics_inform_title"])."';
			var msg_script_analytics_inform_content = '".addslashes($msg["script_analytics_inform_content"])."';
			var msg_script_analytics_inform_ask_opposite = '".addslashes($msg["script_analytics_inform_ask_opposite"])."';
			var msg_script_analytics_inform_ask_accept = '".addslashes($msg["script_analytics_inform_ask_accept"])."';
			var msg_script_analytics_content_opposed = '".addslashes($msg["script_analytics_content_opposed"])."';
		";
		if ($opac_url_more_about_cookies) {
			$std_header.="var script_analytics_content_link_more = '<a href=\'".$opac_url_more_about_cookies."\' target=\'_blank\'>".addslashes($msg["script_analytics_content_link_more"])."</a>';";
		} else {
			$std_header.="var script_analytics_content_link_more = '';";
		}
		$std_header.="
		</script>
		<script type='text/javascript' src='".$include_path."/javascript/script_analytics.js'></script>
		<script type='text/javascript'>
			scriptAnalytics.CookieConsent.start();
		</script>
		";
	}
}
if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] != "false") {
	if ($opac_script_analytics) {
		eval("\$opac_script_analytics=\"".str_replace("\"","\\\"",$opac_script_analytics)."\";");
		$std_header.= $opac_script_analytics;
	}
}
//Opposition à l'utilisation des cookies, désactivation des partages sur les réseaux sociaux
if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false") {
	$opac_show_social_network = 0;
}

$std_header.="<script type='text/javascript'>var opac_show_social_network =$opac_show_social_network;</script>";
if($opac_show_social_network){
	
	if($opac_param_social_network){
		$addThisParams=json_decode($opac_param_social_network);
	}
	//ra-4d9b1e202c30dea1
	if(sizeof($addThisParams->addthis_share)){
		$std_header.="<script type='text/javascript'>var addthis_share = ".json_encode($addThisParams->addthis_share).";</script>";
	}
	$std_header.="<script type='text/javascript'>var addthis_config = ".json_encode($addThisParams->addthis_config).";</script>
	<script type='text/javascript' src='https://s7.addthis.com/js/".$addThisParams->version."/addthis_widget.js#pubid=".$addThisParams->token."'></script>";
}
if($opac_allow_affiliate_search){
	$std_header.="
	<script type='text/javascript' src='includes/javascript/affiliate_search.js'></script>";
}
if($opac_allow_simili_search){
	$std_header.="
	<script type='text/javascript' src='includes/javascript/simili_search.js'></script>";
}
if($opac_visionneuse_allow) {
	$std_header.="
	<script type='text/javascript' src='".$opac_url_base."/visionneuse/javascript/visionneuse.js'></script>";
}

$std_header.="
	<script type='text/javascript' src='$include_path/javascript/http_request.js'></script>";

if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] != "false" && $pmb_logs_activate) {
	$std_header.="
	<script type='text/javascript' src='$include_path/javascript/track_clicks.js'></script>";
}

$std_header.="
	!!enrichment_headers!!
</head>

<body onload=\"window.defaultStatus='".$msg["page_status"]."';\" $iecssresizepb id=\"pmbopac\">";
if($opac_notice_enrichment == 0){
	$std_header.="
<script type='text/javascript'>
	function findNoticeElement(id){
		var ul=null;
		//cas des notices classiques
		var domNotice = document.getElementById('el'+id+'Child');
		//notice_display
		if(!domNotice) domNotice = document.getElementById('notice');
		if(domNotice){
			var uls = domNotice.getElementsByTagName('ul');
			for (var i=0 ; i<uls.length ; i++){
				if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
					var ul = uls[i];
					break;
				}
			}
		} else{
			var li = document.getElementById('onglet_isbd'+id);
			if(!li) var li = document.getElementById('onglet_public'+id);
			if(li) var ul = li.parentNode;
		}
		return ul;
	}
	function show_what(quoi, id) {
		switch(quoi){
			case 'EXPL_LOC' :
				document.getElementById('div_expl_loc' + id).style.display = 'block';
				document.getElementById('div_expl' + id).style.display = 'none';		
				document.getElementById('onglet_expl' + id).className = 'isbd_public_inactive';		
				document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_active';
				break;
			case 'EXPL' :
				document.getElementById('div_expl_loc' + id).style.display = 'none';
				document.getElementById('div_expl' + id).style.display = 'block';
				document.getElementById('onglet_expl' + id).className = 'isbd_public_active';
				document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_inactive';
				break;
			default :
				quoi= quoi.toLowerCase();
				var ul = findNoticeElement(id);
				if (ul) {
					var items  = ul.getElementsByTagName('li');
					for (var i=0 ; i<items.length ; i++){
						if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
							items[i].className = 'isbd_public_active';
							document.getElementById('div_'+quoi+id).style.display = 'block';
						}else{
							if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket'){
								items[i].className = 'isbd_public_inactive';	
								document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
							}
						}
					}			
				}
				break;
		}
	}
</script>";
}
if($opac_recherche_ajax_mode){
	$std_header.="
	<script type='text/javascript' src='./includes/javascript/tablist_ajax.js'></script>";
}
$std_header.="
<script type='text/javascript' src='./includes/javascript/tablist.js'></script>
<script type='text/javascript' src='./includes/javascript/misc.js'></script>
	<div id='att' style='z-Index:1000'></div>
	<div id=\"container\"><div id=\"main\"><div id='main_header'>!!main_header!!</div><div id=\"main_hors_footer\">!!home_on_top!!
						\n";
$std_header.="<script type='text/javascript' src='".$include_path."/javascript/auth_popup.js'></script>	\n";

$inclus_header = "
!!liens_rss!!
!!enrichment_headers!!
".$stylescsscodehtml.$css_addon."	
<script type='text/javascript'>
	var opac_show_social_network =$opac_show_social_network;";
if($opac_notice_enrichment == 0){
	$inclus_header.= "
	function findNoticeElement(id){
		var ul=null;
		//cas des notices classiques
		var domNotice = document.getElementById('el'+id+'Child');
		//notice_display
		if(!domNotice) domNotice = document.getElementById('notice');
		if(domNotice){
			var uls = domNotice.getElementsByTagName('ul');
			for (var i=0 ; i<uls.length ; i++){
				if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
					var ul = uls[i];
					break;
				}
			}
		} else{
			var li = document.getElementById('onglet_isbd'+id);
			if(!li) var li = document.getElementById('onglet_public'+id);
			if(li) var ul = li.parentNode;
		}
		return ul;
	}
	function show_what(quoi, id) {
		quoi= quoi.toLowerCase();
		var ul = findNoticeElement(id);
		if (ul) {
			var items  = ul.getElementsByTagName('li');
			for (var i=0 ; i<items.length ; i++){
				if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
					items[i].className = 'isbd_public_active';
					document.getElementById('div_'+quoi+id).style.display = 'block';
				}else{
					if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket'){
						items[i].className = 'isbd_public_inactive';	
						document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
					}
				}
			}
		}
	}";
} 	
$inclus_header.= "
</script>
<script type='text/javascript' src='includes/javascript/http_request.js'></script>
<script type='text/javascript' src='includes/javascript/tablist_ajax.js'></script>
<script type='text/javascript' src='includes/javascript/tablist.js'></script>
<script type='text/javascript' src='includes/javascript/drag_n_drop.js'></script>
<script type='text/javascript' src='includes/javascript/handle_drop.js'></script>
<script type='text/javascript' src='includes/javascript/popup.js'></script>
<script type='text/javascript'>
  	if (!document.getElementsByClassName){ // pour ie
		document.getElementsByClassName = 
		function(nom_class){
			var items=new Array();
			var count=0;
			for (var i=0; i<document.getElementsByTagName('*').length; i++) {  
				if (document.getElementsByTagName('*').item(i).className == nom_class) {
					items[count++] = document.getElementsByTagName('*').item(i); 
			    }
			 }
			return items;
		 }
	}
	// Fonction a utilisier pour l'encodage des URLs en javascript
	function encode_URL(data){
		var docCharSet = document.characterSet ? document.characterSet : document.charset;
		if(docCharSet == \"UTF-8\"){
			return encodeURIComponent(data);
		}else{
			return escape(data);
		}
	}
</script>";
$inclus_header.="
<link rel='stylesheet' type='text/css' href='".$javascript_path."/dojo/dijit/themes/tundra/tundra.css' />
<script type='text/javascript'>
	var dojoConfig = {
		parseOnLoad: true,
		locale: '".str_replace("_","-",strtolower($lang))."',
		isDebug: false,
		usePlainJson: true,
		packages: [{
			name: 'pmbBase',
			location:'../../..'
		}],
		deps: ['apps/pmb/MessagesStore'],
		callback:function(MessagesStore){
			window.pmbDojo = {};
			pmbDojo.messages = new MessagesStore({url:'./ajax.php?module=ajax&categ=messages', directInit:false});

		},
	};
</script>
<script type='text/javascript' src='".$javascript_path."/dojo/dojo/dojo.js'>
</script><script type='text/javascript' src='".$javascript_path."/dojo/dojo/pmbdojo.js'></script>
";
if (!$_COOKIE['PhpMyBibli-COOKIECONSENT']) {
	if ($opac_script_analytics || $opac_show_social_network || $pmb_logs_activate) {
		$inclus_header.="
		<script type='text/javascript'>
			var msg_script_analytics_content = '".addslashes($msg["script_analytics_content"])."';
			var msg_script_analytics_inform_title = '".addslashes($msg["script_analytics_inform_title"])."';
			var msg_script_analytics_inform_content = '".addslashes($msg["script_analytics_inform_content"])."';
			var msg_script_analytics_inform_ask_opposite = '".addslashes($msg["script_analytics_inform_ask_opposite"])."';
			var msg_script_analytics_inform_ask_accept = '".addslashes($msg["script_analytics_inform_ask_accept"])."';
			var msg_script_analytics_content_opposed = '".addslashes($msg["script_analytics_content_opposed"])."';
		";
		if ($opac_url_more_about_cookies) {
			$inclus_header.="var script_analytics_content_link_more = '<a href=\'".$opac_url_more_about_cookies."\' target=\'_blank\'>".addslashes($msg["script_analytics_content_link_more"])."</a>';";
		} else {
			$inclus_header.="var script_analytics_content_link_more = '';";
		}
		$inclus_header.="
		</script>
		<script type='text/javascript' src='".$include_path."/javascript/script_analytics.js'></script>
		<script type='text/javascript'>
			scriptAnalytics.CookieConsent.start();
		</script>
		";
	}
}
if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] != "false") {
	if ($opac_script_analytics) {
		eval("\$opac_script_analytics=\"".str_replace("\"","\\\"",$opac_script_analytics)."\";");
		$inclus_header.= $opac_script_analytics;
	}
}
//Opposition à l'utilisation des cookies, désactivation des partages sur les réseaux sociaux
if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false") {
	$opac_show_social_network = 0;
}
if($opac_show_social_network){
	
	if($opac_param_social_network){
		$addThisParams=json_decode($opac_param_social_network);
	}
	//ra-4d9b1e202c30dea1
	if(sizeof($addThisParams->addthis_share)){
		$inclus_header.="<script type='text/javascript'>var addthis_share = ".json_encode($addThisParams->addthis_share).";</script>";
	}
	$inclus_header.="<script type='text/javascript'>var addthis_config = ".json_encode($addThisParams->addthis_config).";</script>
	<script type='text/javascript' src='https://s7.addthis.com/js/".$addThisParams->version."/addthis_widget.js#pubid=".$addThisParams->token."'></script>";
}
if($opac_allow_affiliate_search){
	$inclus_header.="
	<script type='text/javascript' src='includes/javascript/affiliate_search.js'></script>";
}
if($opac_allow_simili_search){
	$inclus_header.="
	<script type='text/javascript' src='includes/javascript/simili_search.js'></script>";
}
if($opac_visionneuse_allow) {
	$inclus_header.="
	<script type='text/javascript' src='".$opac_url_base."/visionneuse/javascript/visionneuse.js'></script>";
}
if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] != "false" && $pmb_logs_activate) {
	$inclus_header.="
	<script type='text/javascript' src='$include_path/javascript/track_clicks.js'></script>";
}

$inclus_header.="

$inclure_recherche
		
<div id='att' style='z-Index:1000'></div>
	<div id=\"container\"><div id=\"main\"><div id='main_header'>!!main_header!!</div><div id=\"main_hors_footer\">!!home_on_top!!
						\n";
$short_header="
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"
    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" charset='".$charset."'>
<head>
<meta http-equiv='content-type' content='text/html; charset=".$charset."'>
<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
<script type='text/javascript' src='includes/javascript/http_request.js'></script>
<script type='text/javascript'>
var opac_show_social_network = ".$opac_show_social_network.";

function findNoticeElement(id){
	var ul=null;
	//cas des notices classiques
	var domNotice = document.getElementById('el'+id+'Child');
	//notice_display
	if(!domNotice) domNotice = document.getElementById('notice');
	if(domNotice){
		var uls = domNotice.getElementsByTagName('ul');
		for (var i=0 ; i<uls.length ; i++){
			if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
				var ul = uls[i];
				break;
			}
		}
	} else{
		var li = document.getElementById('onglet_isbd'+id);
		if(!li) var li = document.getElementById('onglet_public'+id);
		if(li) var ul = li.parentNode;
	}
	return ul;
}
function show_what(quoi, id) {
	switch(quoi){
		case 'EXPL_LOC' :
			document.getElementById('div_expl_loc' + id).style.display = 'block';
			document.getElementById('div_expl' + id).style.display = 'none';		
			document.getElementById('onglet_expl' + id).className = 'isbd_public_inactive';		
			document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_active';
			break;
		case 'EXPL' :
			document.getElementById('div_expl_loc' + id).style.display = 'none';
			document.getElementById('div_expl' + id).style.display = 'block';
			document.getElementById('onglet_expl' + id).className = 'isbd_public_active';
			document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_inactive';
			break;
		default :
			quoi= quoi.toLowerCase();
			var ul = findNoticeElement(id);
			if (ul) {
				var items  = ul.getElementsByTagName('li');
				for (var i=0 ; i<items.length ; i++){
					if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
						items[i].className = 'isbd_public_active';
						document.getElementById('div_'+quoi+id).style.display = 'block';
					}else{
						if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket'){
							items[i].className = 'isbd_public_inactive';	
							document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
						}
					}
				}			
			}
			break;
	}
}
</script>
!!liens_rss!!
	".$stylescsscodehtml.$css_addon."
</head>
<body>";



$short_footer="</body></html>";

$popup_header="
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"
    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" charset='".$charset."'>
<head>
	<meta http-equiv=\"content-type\" content=\"text/html; charset=$charset\" />
	".$stylescsscodehtml.$css_addon."
	<title>".$msg['opac_title']." $opac_biblio_name.</title>
</head>
<body id='pmbopac' class='popup'>
<script type='text/javascript'>
var opac_show_social_network =$opac_show_social_network;
function findNoticeElement(id){
	var ul=null;
	//cas des notices classiques
	var domNotice = document.getElementById('el'+id+'Child');
	//notice_display
	if(!domNotice) domNotice = document.getElementById('notice');
	if(domNotice){
		var uls = domNotice.getElementsByTagName('ul');
		for (var i=0 ; i<uls.length ; i++){
			if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
				var ul = uls[i];
				break;
			}
		}
	} else{
		var li = document.getElementById('onglet_isbd'+id);
		if(!li) var li = document.getElementById('onglet_public'+id);
		if(li) var ul = li.parentNode;
	}
	return ul;
}
function show_what(quoi, id) {
	quoi= quoi.toLowerCase();
	var ul = findNoticeElement(id);
	if (ul) {
		var items  = ul.getElementsByTagName('li');
		for (var i=0 ; i<items.length ; i++){
			if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
				items[i].className = 'isbd_public_active';
				document.getElementById('div_'+quoi+id).style.display = 'block';
			}else{
				if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket'){
					items[i].className = 'isbd_public_inactive';	
					document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
				}
			}
		}
	}
}
</script>
<script type='text/javascript' src='includes/javascript/http_request.js'></script>
<script type='text/javascript' src='./includes/javascript/tablist.js'></script>
<script type='text/javascript' src='./includes/javascript/misc.js'></script>
";

$popup_footer="</body></html>";



// liens du bas de la page
$liens_bas = "</div><!-- fin DIV main_hors_footer --><div id=\"footer\">

<span id=\"footer_rss\">
	<!-- rss -->
</span>
<span id=\"footer_link_sup\">
		$opac_lien_bas_supplementaire &nbsp;
</span>
";	
	
if ($opac_biblio_website)	$liens_bas .= "
<span id=\"footer_link_website\">
	<a class=\"footer_biblio_name\" href=\"$opac_biblio_website\" title=\"$opac_biblio_name\">$opac_biblio_name</a> &nbsp;
</span>	
";
$liens_bas .= "
<span id=\"footer_link_pmb\">
$opac_lien_moteur_recherche &nbsp;
		<a class=\"lien_pmb_footer\" href=\"http://www.sigb.net\" title=\"".$msg['common_tpl_motto']."\" target='_blank'>".$msg['common_tpl_motto_pmb']."</a> 	
</span>		
		
</div>" ;

// ACCESSIBILITE
if ($opac_accessibility) {
	$accessibility="<div id='accessibility'>\n
		<ul class='accessibility_font_size'>
			<li class='accessibility_font_size_small'><a href='javascript:set_font_size(-1);' title='".$msg["accessibility_font_size_small"]."'>A-</a></li>
			<li class='accessibility_font_size_normal'><a href='javascript:set_font_size(0);' title='".$msg["accessibility_font_size_normal"]."'>A</a></li>
			<li class='accessibility_font_size_big'><a href='javascript:set_font_size(1);' title='".$msg["accessibility_font_size_big"]."'>A+</a></li>
		</ul>
		</div>\n";
	if($_SESSION["pmbopac_fontSize"]) {
		$accessibility.="<script type='text/javascript'>set_value_style('pmbopac', 'fontSize', '".$_SESSION["pmbopac_fontSize"]."');</script>";
	}
}

// HOME
$home_on_left = "<div id=\"accueil\">\n
<h3><span onclick='document.location=\"./index.php?\"' style='cursor: pointer;'>!!welcome_page!!</span></h3>\n";

if ($opac_logosmall<>"") $home_on_left .= "<p class=\"centered\"><a href='./index.php?'><img src='".$opac_logosmall."'  border='0' align='center'/></a></p>\n";
else $home_on_left .= "<p class=\"centered\"><a href='./index.php?'><img src='./images/home.jpg' border='0' align='center'/></a></p>\n";
	
// affichage du choix de langue  
$common_tpl_lang_select="<div id='lang_select'><h3 ><span>!!msg_lang_select!!</span></h3><span>!!lang_select!!</span></div>\n";

$home_on_left.="!!common_tpl_lang_select!!
					</div><!-- fermeture #accueil -->\n" ;

// HOME lorsque le bandeau gauche n'est pas affiché
$home_on_top ="<div id='home_on_top'>
	<span onclick='document.location=\"./index.php?\"' style='cursor: pointer;'><img src='./images/home.gif' align='absmiddle' /> ".$msg["welcome_page"]."</span>
	</div>
	";

// LOGIN FORM=0
// Si le login est autorisé, alors afficher le formulaire de saisie utilisateur/mot de passe ou le code de l'utilisateur connecté
$loginform='';
if ($opac_show_loginform) {
	$loginform ="<div id=\"connexion\">\n
			<h3><!-- common_tpl_login_invite --></h3><span id='login_form'>!!login_form!!</span>\n
			</div><!-- fermeture #connexion -->\n";
} else {
	if (!file_exists($include_path.'/ext_auth.inc.php')) {
		$_SESSION["user_code"]="";
	}
}

// METEO
if ($opac_show_meteo && $opac_show_meteo_url) {
	$meteo = "<div id=\"meteo\">\n
		<h3>".$msg['common_tpl_meteo_invite']."</h3>\n
		<p class=\"centered\">$opac_show_meteo_url</p>\n
		<small>".$msg['common_tpl_meteo']." $opac_biblio_town</small>\n
		</div><!-- fermeture # meteo -->\n";
}

// ADRESSE
$adresse = "<div id=\"adresse\">\n
		<h3>!!common_tpl_address!!</h3>\n
		<span>
			$opac_biblio_name<br />
			$opac_biblio_adr1<br />
			$opac_biblio_cp $opac_biblio_town<br />
			$opac_biblio_country&nbsp;<br />
			$opac_biblio_phone<br />";
			if ($opac_biblio_email) $adresse.="<span id='opac_biblio_email'>
			<a href=\"mailto:$opac_biblio_email\" alt=\"$opac_biblio_email\">!!common_tpl_contact!!</a></span>";
$adresse.="</span>" ;
$adresse.="
	    </div><!-- fermeture #adresse -->" ;

// bloc post adresse
if ($opac_biblio_post_adress){
	$adresse .= "<div id=\"post_adress\">\n
		<span>".$opac_biblio_post_adress."
		</span>	
	    </div><!-- fermeture #post_adress -->" ;
}

if($lvl=="more_results" || strpos($lvl,"_see") !== false){
$facette ="
			<div id='facette'>
				!!lst_facette!!
			</div>";
$lvl1 ="
			<div id='lvl1'>
				!!lst_lvl1!!
			</div>";
}

// le footer clos le <div id=\"supportingText\"><span>, reste ouvert le <div id=\"container\">
$footer = "	
		!!div_liens_bas!! \n
		</div><!-- /div id=main -->\n
		<div id=\"intro\">\n";

$inclus_footer = "	
		</span>
		!!div_liens_bas!! \n
		</div><!-- /div id=main -->\n
		<div id=\"intro\">\n";
		
// Si $opac_biblio_important_p1 est renseigné, alors intro_message est affiché
// Ceci permet plus de liberté avec la CSS
$std_header_suite="<div id=\"intro_message\">";
if ($opac_biblio_important_p1) 	
		 $std_header_suite.="<div class=\"p1\">$opac_biblio_important_p1</div>";
// si $opac_biblio_important_p2 est renseigné alors suite d'intro_message
if ($opac_biblio_important_p2 && !$std_header_suite)
	   $std_header_suite.="<div class=\"p2\">$opac_biblio_important_p2</div>";
else $std_header_suite.="<div class=\"p2\">$opac_biblio_important_p2</div>";
// fin intro_message
$std_header_suite.="</div>";
	
$std_header.=$std_header_suite ;
$inclus_header.=$std_header_suite;

$footer.= $footer_suite ;
$inclus_footer.= $footer_suite ;
eval("\$opac_biblio_preamble_p1=\"".str_replace("\"","\\\"",$opac_biblio_preamble_p1)."\";");
eval("\$opac_biblio_preamble_p2=\"".str_replace("\"","\\\"",$opac_biblio_preamble_p2)."\";");
$footer_suite ="<div id=\"intro_bibli\">
			<h3>$opac_biblio_name</h3>
			<div class=\"p1\">$opac_biblio_preamble_p1</div>
			<div class=\"p2\">$opac_biblio_preamble_p2</div>
			</div>
		</div><!-- /div id=intro -->";

$footer.= $footer_suite ;
$inclus_footer.= $footer_suite ;
		
$footer .="		
		!!contenu_bandeau!!";

$footer .="</div><!-- /div id=container -->
		!!cms_build_info!!
		<script type='text/javascript'>init_drag();	//rechercher!!</script> 
		</body>
		</html>
		"; //".($surligne?"rechercher(1);":"")."

$inclus_footer .="
		!!contenu_bandeau!!
		</div><!-- /div id=container -->
		!!cms_build_info!!
		<script type='text/javascript'>init_drag(); //rechercher!!</script>
				";

$liens_opac['lien_rech_notice'] 		= "./index.php?lvl=notice_display&id=!!id!!";
$liens_opac['lien_rech_auteur'] 		= "./index.php?lvl=author_see&id=!!id!!";
$liens_opac['lien_rech_editeur'] 		= "./index.php?lvl=publisher_see&id=!!id!!";
$liens_opac['lien_rech_titre_uniforme']	= "./index.php?lvl=titre_uniforme_see&id=!!id!!";
$liens_opac['lien_rech_serie'] 			= "./index.php?lvl=serie_see&id=!!id!!";
$liens_opac['lien_rech_collection'] 	= "./index.php?lvl=coll_see&id=!!id!!";
$liens_opac['lien_rech_subcollection'] 	= "./index.php?lvl=subcoll_see&id=!!id!!";
$liens_opac['lien_rech_indexint'] 		= "./index.php?lvl=indexint_see&id=!!id!!";
//$liens_opac['lien_rech_motcle'] 		= "./index.php?lvl=search_result&mode=keyword&auto_submit=1&user_query=!!mot!!";
$liens_opac['lien_rech_motcle'] 		= "./index.php?lvl=more_results&mode=keyword&user_query=!!mot!!&tags=ok";
$liens_opac['lien_rech_categ'] 			= "./index.php?lvl=categ_see&id=!!id!!";
$liens_opac['lien_rech_perio'] 			= "./index.php?lvl=notice_display&id=!!id!!";
$liens_opac['lien_rech_bulletin'] 		= "./index.php?lvl=bulletin_display&id=!!id!!";
$liens_opac['lien_rech_concept'] 		= "./index.php?lvl=concept_see&id=!!id!!";
$liens_opac['lien_rech_authperso'] 		= "./index.php?lvl=authperso_see&id=!!id!!";


switch($opac_allow_simili_search){
	case "1" :
		$simili_search_call = "show_simili_search_all();show_expl_voisin_search_all();";
		break;
	case "2" :
		$simili_search_call = "show_expl_voisin_search_all();";
		break;
	case "3" :
		$simili_search_call = "show_simili_search_all()";
		break;
}

if($opac_recherche_ajax_mode){
	$begin_result_liste = "<span class=\"expandAll\"><a href='javascript:expandAll_ajax(".$opac_recherche_ajax_mode.");$simili_search_call'><img class='img_plusplus' src='./images/expand_all.gif' border='0' id='expandall'></a></span><span class=\"espaceResultSearch\">&nbsp;</span><span class=\"collapseAll\"><a href='javascript:collapseAll()'><img class='img_moinsmoins' src='./images/collapse_all.gif' border='0' id='collapseall'></a></span>" ;
}else{
	$begin_result_liste = "<span class=\"expandAll\"><a href='javascript:expandAll()'><img class='img_plusplus' src='./images/expand_all.gif' border='0' id='expandall'></a></span><span class=\"espaceResultSearch\">&nbsp;</span><span class=\"collapseAll\"><a href='javascript:collapseAll()'><img class='img_moinsmoins' src='./images/collapse_all.gif' border='0' id='collapseall'></a></span>" ;
}
$affich_tris_result_liste .= sort::show_tris_selector();

define( 'AFF_ETA_NOTICES_NON', 0 );
define( 'AFF_ETA_NOTICES_ISBD', 1 );
define( 'AFF_ETA_NOTICES_PUBLIC', 2 );
define( 'AFF_ETA_NOTICES_BOTH', 4 );
define( 'AFF_ETA_NOTICES_BOTH_ISBD_FIRST', 5 );
define( 'AFF_ETA_NOTICES_REDUIT', 8 );
define( 'AFF_ETA_NOTICES_DEPLIABLES_NON', 0 );
define( 'AFF_ETA_NOTICES_DEPLIABLES_OUI', 1 );
define( 'AFF_ETA_NOTICES_TEMPLATE_DJANGO', 9 );

define( 'AFF_BAN_NOTICES_NON', 0 );
define( 'AFF_BAN_NOTICES_ISBD', 1 );
define( 'AFF_BAN_NOTICES_PUBLIC', 2 );
define( 'AFF_BAN_NOTICES_BOTH', 4 );
define( 'AFF_BAN_NOTICES_BOTH_ISBD_FIRST', 5 );
define( 'AFF_BAN_NOTICES_REDUIT', 8 );
define( 'AFF_BAN_NOTICES_DEPLIABLES_NON', 0 );
define( 'AFF_BAN_NOTICES_DEPLIABLES_OUI', 1 );
