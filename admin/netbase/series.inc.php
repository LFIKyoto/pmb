<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.inc.php,v 1.14 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serie.class.php");

// la taille d'un paquet de notices
$lot = SERIE_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_suppr_series"], ENT_QUOTES, $charset)."</h2>";

$query = pmb_mysql_query("SELECT serie_id from series left join notices on tparent_id=serie_id where tparent_id is null");
$affected=0;
if($affected = pmb_mysql_num_rows($query)){
	while ($ligne = pmb_mysql_fetch_object($query)) {
		$serie = new serie($ligne->serie_id);
		$serie->delete();
	}
}

$query = pmb_mysql_query("update notices left join series on tparent_id=serie_id set tparent_id=0 where serie_id is null");

$spec = $spec - CLEAN_SERIES;
$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_suppr_series"], ENT_QUOTES, $charset)." : ";
$v_state .= $affected." ".htmlentities($msg["nettoyage_res_suppr_series"], ENT_QUOTES, $charset);
$opt = pmb_mysql_query('OPTIMIZE TABLE series');
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
		
