<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_modif.inc.php,v 1.68 2016-12-21 10:08:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/user.class.php');

$requete = "SELECT username, nom, prenom, rights, userid, user_lang, ";
$requete .="nb_per_page_search, nb_per_page_select, nb_per_page_gestion, ";
$requete .="param_popup_ticket, param_sounds, ";
$requete .="user_email, user_alert_resamail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num FROM users WHERE userid='$id' LIMIT 1 ";
$res = pmb_mysql_query($requete, $dbh);
$nbr = pmb_mysql_num_rows($res);
if ($nbr) {
	$usr=pmb_mysql_fetch_object($res);
} else die ('Unknown user');

$param_default = user::get_form($id, 'userform');

echo window_title($msg[1003].$msg[18].$msg[1003].$msg[86].$msg[1003].$usr->username.$msg[1001]);
user_form(	$usr->username,
			$usr->nom,
			$usr->prenom,
			$usr->rights,
			$usr->userid,
			$usr->user_lang,
			$usr->nb_per_page_search,
			$usr->nb_per_page_select,
			$usr->nb_per_page_gestion,
			$param_default,
			$usr->user_email,
			$usr->user_alert_resamail,
			$usr->user_alert_demandesmail,
			$usr->user_alert_subscribemail,
			$usr->user_alert_suggmail,
			$usr->user_alert_serialcircmail,
			$usr->grp_num
			);
echo form_focus('userform', 'form_nom');
