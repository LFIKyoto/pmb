<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: hash_empr_password.inc.php,v 1.1.2.2 2015-09-25 14:32:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/emprunteur.class.php");

// la taille d'un paquet de lecteurs
$lot = EMPR_PAQUET_SIZE*10; // defini dans ./params.inc.php
// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$empr = pmb_mysql_query("SELECT count(1) FROM empr where empr_password_is_encrypted=0", $dbh);
	$count = pmb_mysql_result($empr, 0, 0);
}

print "<br /><br /><h2 align='center'>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</h2>";

$query = pmb_mysql_query("SELECT id_empr, empr_password, empr_login FROM empr where empr_password_is_encrypted=0 LIMIT $lot");
// start <= count : test supplémentaire pour s'assurer de ne pas boucler à l'infini
// problème rencontré : login vide et 2 login identiques (en théorie impossible)
if(pmb_mysql_num_rows($query) && ($start <= $count)) {

	if (!$start) {
		$requete = "CREATE TABLE if not exists empr_passwords (
			id_empr INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			empr_password VARCHAR( 255 ) NOT NULL default '')";
		pmb_mysql_query($requete, $dbh);
		$requete = "INSERT IGNORE INTO empr_passwords SELECT id_empr, empr_password FROM empr where empr_password_is_encrypted=0";
		pmb_mysql_query($requete, $dbh);
	}
	
    // définition de l'état de la jauge
    $state = floor($start / ($count / $jauge_size));

    // mise à jour de l'affichage de la jauge
    print "<table border='0' align='center' width='$jauge_size' cellpadding='0' border='0'><tr><td class='jauge'>";
    print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";

    // calcul pourcentage avancement
    $percent = floor(($start/$count)*100);

    // affichage du % d'avancement et de l'état
    print "<div align='center'>$percent%</div>";
   	while ($row = pmb_mysql_fetch_object($query) )  {
   		emprunteur::update_digest($row->empr_login,$row->empr_password);
   		emprunteur::hash_password($row->empr_login,$row->empr_password);
   	}
   	pmb_mysql_free_result($query);
	$next = $start + $lot;
 	print "
	<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		<input type='hidden' name='start' value=\"$next\">
		<input type='hidden' name='count' value=\"$count\">
	</form>
	<script type=\"text/javascript\">
	<!--
		document.forms['current_state'].submit();
	-->
	</script>";
} else {
	$spec = $spec - HASH_EMPR_PASSWORD;
	$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["hash_empr_password_status"], ENT_QUOTES, $charset);
	$v_state .= $count." ".htmlentities($msg["hash_empr_password_status_end"], ENT_QUOTES, $charset);
	$opt = pmb_mysql_query('OPTIMIZE TABLE empr');
	// mise à jour de l'affichage de la jauge
	print "
	<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
	<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
	<div align='center'>100%</div>";

	print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
	</form>
	<script type=\"text/javascript\">
	<!--
		document.forms['process_state'].submit();
	-->
	</script>";	
}	