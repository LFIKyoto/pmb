<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: help.php,v 1.12.2.3 2015-11-05 16:56:24 mbertin Exp $

$base_path="./";
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once("./includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
	
// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");
require_once($base_path."includes/start.inc.php");
require_once("./includes/misc.inc.php");
require_once($base_path.'/includes/templates/common.tpl.php');
// récupération localisation
require_once('./includes/localisation.inc.php');

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

print "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"
    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" >
<head>
	<meta http-equiv=\"content-type\" content=\"text/html; charset=".$charset."\" />
	<meta name=\"author\" content=\"PMB Group\" />";
if ($charset=='utf-8') {
	print utf8_encode("	<meta name=\"keywords\" content=\"OPAC, web, libray, opensource, catalog, catalogue, bibliothèque, médiathèque, pmb, phpmybibli\" />");
} else {
	print "	<meta name=\"keywords\" content=\"OPAC, web, libray, opensource, catalog, catalogue, bibliothèque, médiathèque, pmb, phpmybibli\" />";
}
print "	<meta name=\"description\" content=\"Recherches simples dans l'OPAC de PMB\" />
	<meta name=\"robots\" content=\"all\" />
	<title>pmb : opac</title>
	<script>
	function div_show(name) {
		var z=document.getElementById(name);
		if (z.style.display==\"none\") {
			z.style.display=\"block\"; }
		else { z.style.display=\"none\"; }
		}
	</script>
	".link_styles($css)."
</head>

<body onload=\"window.defaultStatus='pmb : opac';\" id=\"help_popup\" class='popup'>
<div id='help-container'>
<p align=right style=\"margin-top:4px;\"><a name='top' ></a><a href='#' onclick=\"self.close();return false\" title=\"".$msg[search_close]."\" alt=\"".$msg[search_close]."\"><img src=\"".get_url_icon('close.gif')."\" align=\"absmiddle\" border=\"0\"></a></p>

";

if (file_exists("includes/messages/".$lang."/doc_".$whatis."_subst.txt")) {
	$aide = file_get_contents("includes/messages/".$lang."/doc_".$whatis."_subst.txt");
} elseif (file_exists("includes/messages/".$lang."/doc_".$whatis.".txt")) {
	$aide = file_get_contents("includes/messages/".$lang."/doc_".$whatis.".txt");
}
if ($charset=='utf-8') {
	print utf8_encode($aide);
} else {
	print $aide;
}

print "
<p align=\"right\"><a href='#top' title=\"".$msg[search_up]."\" alt=\"".$msg[search_up]."\"><img src=\"images/up.gif\" align=\"absmiddle\" border=\"0\"></a></p>
</div>
<script>self.focus();</script>";
print "</body></html>"
?>