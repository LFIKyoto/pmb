<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations8.inc.php,v 1.1.2.2 2015-09-30 08:40:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la taille d'un paquet de notices
$lot = SERIE_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);
print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_clean_blob"], ENT_QUOTES, $charset)."</h2>";

$affected = 0;

for($i=1;$i<=2;$i++){
	if($i==1){
		$table='logopac';
	}else{
		$table='statopac';
	}
	$query = "SELECT column_type FROM information_schema.columns WHERE table_schema = '".DATA_BASE."' AND table_name = '".$table."' AND column_name = 'empr_expl'";
	$res = pmb_mysql_query($query);
	$row = pmb_mysql_fetch_object($res);
	
	if ($row->column_type == 'blob') {
		$query = pmb_mysql_query("ALTER TABLE ".$table." CHANGE empr_expl empr_expl MEDIUMBLOB NOT NULL");
		$affected += pmb_mysql_affected_rows();
	}
}

$spec = $spec - CLEAN_RELATIONS;
$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_suppr_relations"], ENT_QUOTES, $charset)." : ";
$v_state .= $affected." ".htmlentities($msg["nettoyage_res_suppr_blob"], ENT_QUOTES, $charset);

// mise à jour de l'affichage de la jauge
print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
  			<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
 			<div align='center'>100%</div>";
print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		<input type='hidden' name='pass2' value=\"9\">	
	</form>
	<script type=\"text/javascript\"><!--
		document.forms['process_state'].submit();
		-->
	</script>";
