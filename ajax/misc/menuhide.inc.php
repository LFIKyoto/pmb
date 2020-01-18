<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: menuhide.inc.php,v 1.5 2019-08-01 13:16:36 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $fname;

/***********************************************
 *	Procedure ajax menuhide.inc.php
 *	 
 * Input:
 *	- $p1, date envoyée par POST or GET metod
 * Output:
 *  - retourne 0 si gestion de la preference 
 *  - retrourne 1 si le script a rencontre une erreur
 *
 */	
function menuvchgpref() {
	global $page, $values;
	global $PMBuserid;
	$page = rawurldecode($page);
	$values = rawurldecode($values);
	$_SESSION["AutoHide"][$page] = array();
	$values = explode(",", $values);
	$i = 1;
	foreach ($values as $char) {
		if ($char == "t") {
		    $_SESSION["AutoHide"][$page][$i] = "True";
		} elseif ($char == "f") {
		    $_SESSION["AutoHide"][$page][$i] = "False";
		}
		$i++;
	}
	$sauvemenu = serialize($_SESSION["AutoHide"]);
	$sql = "update users set environnement='".addslashes($sauvemenu)."' where userid=$PMBuserid";
	@pmb_mysql_query($sql);
	ajax_http_send_response("0", "text/text");
	return;
}

function menuvgetpref() {
	global $page;
	$page = rawurldecode($page);
	if (empty($_SESSION["AutoHide"][$page])) {
		$trueids = "0";
	} else {
		$trueids = "";
		foreach ($_SESSION["AutoHide"][$page] as $boolh3) {
			if ($boolh3 == "True") {
			    $trueids .= "t,";
			} elseif ($boolh3 == "False") {
			    $trueids .= "f,";
			}
		}
	}
	ajax_http_send_response(trim($trueids), "text/text");
	return;
}

switch ($fname) {
	case "setpref":
		menuvchgpref();
		break;
	case "getpref":
		menuvgetpref();
		break;
	default:
		ajax_http_send_error("404 Not Found","Invalid command : $fname");
		break;
}
#maintenant on retourne toutes les nh3 true (liste) de la page considérée, de sorte à
#que notre javascript appelant appelle un autre js (lequel sera lancé au chargement de la page aussi) qui
#rétracte tous les menus sauf les menus dans la liste des numéros spécifiés.
#commenter puis suggerer a flo
?>