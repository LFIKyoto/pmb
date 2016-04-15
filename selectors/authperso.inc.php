<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.3 2014-10-31 16:15:57 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=authperso&authperso_id=$authperso_id&caller=$caller&p1=$p1&p2=$p2&p3=$p3&p4=$p4&p5=$p5&p6=$p6&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&callback=$callback&infield=$infield"
		."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field;

require_once("$class_path/authperso.class.php");
require_once('./selectors/templates/sel_authperso.tpl.php');

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;	
}


if($bt_ajouter == "no"){
	$bouton_ajouter="";
} else {
	$bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value='".$msg["authperso_sel_add"]."'>";
}

// affichage des membres de la page
switch($action){
	case 'add':
		$authperso = new authperso($authperso_id);
		print $authperso->get_form_select(0,$base_url);
	//	$authperso_form = str_replace("!!deb_saisie!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $authperso_form);
	//	print $authperso_form;
		break;
	case 'update':
		print $sel_header;
		$authperso = new authperso($authperso_id);
		$authperso->update_from_form();
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $user_input, $nbr_lignes, $page);
		break;
	default:
		print $sel_header;
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $user_input, $nbr_lignes, $page);
		break;
}

	
function show_results($dbh, $user_input, $nbr_lignes=0, $page=0, $id = 0) {
	
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $callback;
	global $msg;
 	global $charset;
 	global $no_display ;
 	global $authperso_id;
 	
 	$authperso=new authperso($authperso_id);
 	print $authperso->get_list_selector();
 
}

print $sel_footer;