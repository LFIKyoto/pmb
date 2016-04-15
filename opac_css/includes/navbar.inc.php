<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: navbar.inc.php,v 1.14 2014-02-17 14:23:51 abacarisse Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/includes/javascript/form.inc.php');

function printnavbar($page, $nbrpages, $url,$action='') {
	global $script_test_form;
	global $msg;

	$precedente = $page-1;
	$suivante = $page+1;

	// crée les tests de formulaire
	$script = $script_test_form;
	$script = str_replace("!!tests!!",
	                      test_field_value_comp('form', 'page', GREATER, $nbrpages, $msg["page_too_high"]) ."\n".
	                      test_field_value_comp('form', 'page', LESSER, 1, $msg["page_too_low"]),
	                      $script);
	$print = $script;
	// affichage de la barre de navigation
	$print .= "<div class=\"navbar\">\n";

	$printurl = $url;
	$printurl = str_replace("&page=!!page!!", "", $printurl);
	$printurl = str_replace("page=!!page!!&", "", $printurl);
	$printurl = str_replace("page=!!page!!", "", $printurl);
	if($action) $printurl=$action;
	
	$print .= "<form name='form' action='$printurl' method='post' onsubmit='return test_form(form)'>\n";
	// first
	if ($page != 1)	{
		$printurl = str_replace("!!page!!", "1", $url);
		$print .= "<a href='$printurl'><img src='./images/first.gif' alt='first' border='0' title='".$msg["first_page"]."'></a>\n";
	}else {
		$print .= "<img src='./images/first-grey.gif' alt='first'>\n";
	}
	// prev
	if ($precedente >= 1) {
		$printurl = str_replace("!!page!!", "$precedente", $url);
		$print .= "<a href='$printurl'><img src='./images/prev.gif' alt='previous' border='0' title='".$msg["prec_page"]."'></a>\n";
	}else {
		$print .= "<img src='./images/prev-grey.gif' alt='previous'>\n";
	}
	// page courante
	if ($nbrpages > 1) {
		$printurl = $url;
		$printurl = str_replace("&page=!!page!!", "", $printurl);
		$printurl = str_replace("page=!!page!!&", "", $printurl);
		$printurl = str_replace("page=!!page!!", "", $printurl);
		$print .= "page <input type='text' class='numero_page' name='page' value='$page' size='".strlen("$nbrpages")."'>/$nbrpages\n";
	}else {
		$print .= "page $page/$nbrpages\n";
	}
	// next
	if ($suivante <= $nbrpages) {
		$printurl = str_replace("!!page!!", "$suivante", $url);
		$print .= "<a href='$printurl'><img src='./images/next.gif' alt='next' border='0' title='".$msg["next_page"]."'></a>\n";
	} else {
		$print .= "<img src='./images/next-grey.gif' alt='next'>\n";
	}
	// last
	if ($page != $nbrpages)	{
		$printurl = str_replace("!!page!!", "$nbrpages", $url);
		$print .= "<a href='$printurl'><img src='./images/last.gif' alt='last' border='0' title='".$msg["last_page"]."'></a>\n";
	}else {
		$print .= "<img src='./images/last-grey.gif' alt='last'>\n";
	}
	$print .= "</form>\n";
	$print .= "</div>\n";
	return $print;
}


function printnavbar_onclick($page, $nbrpages, $url,$action='') {
	global $script_test_form;
	global $msg;

	$precedente = $page-1;
	$suivante = $page+1;


	// affichage de la barre de navigation
	$print = "<div class=\"navbar\">\n";
	
	$print .= "<form name='form' onsubmit=\"document.getElementById('page').value=document.getElementById('navbar_input_page').value;$action\">\n";
	// first
	if ($page != 1)	{
		$print .= "<img src='./images/first.gif' alt='first' border='0' title='".$msg["first_page"]."' onclick=\"document.getElementById('page').value=1;$action\">\n";
	}else {
		$print .= "<img src='./images/first-grey.gif' alt='first'>\n";
	}
	// prev
	if ($precedente >= 1) {
		$printurl = str_replace("!!page!!", "$precedente", $url);
		$print .= "<img src='./images/prev.gif' alt='previous' border='0' title='".$msg["prec_page"]."'  onclick=\"document.getElementById('page').value=$precedente;$action\">\n";
	}else {
		$print .= "<img src='./images/prev-grey.gif' alt='previous'>\n";
	}
	// page courante
	if ($nbrpages > 1) {
		$print .= "page <input type='text' class='numero_page' name='page' id='navbar_input_page' value='$page' size='".strlen("$nbrpages")."'>/$nbrpages\n";
	}else {
		$print .= "page $page/$nbrpages\n";
	}
	// next
	if ($suivante <= $nbrpages) {
	
		$print .= "<img src='./images/next.gif' alt='next' border='0' title='".$msg["next_page"]."' onclick=\"document.getElementById('page').value=$suivante;$action\">\n";
	} else {
		$print .= "<img src='./images/next-grey.gif' alt='next'>\n";
	}
	// last
	if ($page != $nbrpages)	{
		$print .= "<img src='./images/last.gif' alt='last' border='0' title='".$msg["last_page"]."' onclick=\"document.getElementById('page').value=$nbrpages;$action\">\n";
	}else {
		$print .= "<img src='./images/last-grey.gif' alt='last'>\n";
	}
	$print .= "</form>\n";
	$print .= "</div>\n";
	return $print;
}
