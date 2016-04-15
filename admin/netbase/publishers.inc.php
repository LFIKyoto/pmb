<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers.inc.php,v 1.16 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/editor.class.php");

// la taille d'un paquet de notices
$lot = PUBLISHER_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_suppr_editeurs"], ENT_QUOTES, $charset)."</h2>";

$requete="SELECT DISTINCT ed_id FROM publishers LEFT JOIN notices n1 ON n1.ed1_id=ed_id LEFT JOIN notices n2 ON n2.ed2_id=ed_id LEFT JOIN collections ON ed_id=collection_parent WHERE n1.notice_id IS NULL AND  n2.notice_id IS NULL AND collection_id IS NULL";
$res=pmb_mysql_query($requete);
$affected=0;
if($affected = pmb_mysql_num_rows($res)){
	while ($ligne = pmb_mysql_fetch_object($res)) {
		$editeur = new editeur($ligne->ed_id);
		$editeur->delete();
	}
}


$spec = $spec - CLEAN_PUBLISHERS;
$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_suppr_editeurs"], ENT_QUOTES, $charset)." : ";
$v_state .= $affected." ".htmlentities($msg["nettoyage_res_suppr_editeurs"], ENT_QUOTES, $charset);
$opt = pmb_mysql_query('OPTIMIZE TABLE publishers');
// mise à jour de l'affichage de la jauge
print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
  			<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
 			<div align='center'>100%</div>";
print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
	</form>
	<script type=\"text/javascript\"><!--
		document.forms['process_state'].submit();
		-->
		</script>";
