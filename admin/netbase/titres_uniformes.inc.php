<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes.inc.php,v 1.3 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/titre_uniforme.class.php");

// la taille d'un paquet de notices
$lot = SERIE_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_suppr_titres_uniformes"], ENT_QUOTES, $charset)."</h2>";

$query = pmb_mysql_query("SELECT tu_id from titres_uniformes left join notices_titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu is null");
$affected=0;
if($affected = pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$tu = new titre_uniforme($ligne->tu_id);
		$tu->delete();
	}
}

//Nettoyage des informations d'autorités pour les sous collections
titre_uniforme::delete_autority_sources();

$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join titres_uniformes on ntu_num_tu=tu_id where tu_id is null");

$spec = $spec - CLEAN_TITRES_UNIFORMES;
$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_suppr_titres_uniformes"], ENT_QUOTES, $charset)." : ";
$v_state .= $affected." ".htmlentities($msg["nettoyage_res_suppr_titres_uniformes"], ENT_QUOTES, $charset);
$opt = pmb_mysql_query('OPTIMIZE TABLE titres_uniformes');
// mise à jour de l'affichage de la jauge
print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
  			<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
 			<div align='center'>100%</div>";
print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"$v_state\">
		<input type='hidden' name='spec' value=\"$spec\">
	</form>
	<script type=\"text/javascript\"><!--
		document.forms['process_state'].submit();
		-->
	</script>";
		
