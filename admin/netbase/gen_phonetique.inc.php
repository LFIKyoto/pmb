<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_phonetique.inc.php,v 1.2 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE*10; // defini dans ./params.inc.php
// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
// initialisation de la borne de départ
if(!isset($start)) $start=0;
$v_state=urldecode($v_state);

if(!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM words", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
}

print "<br /><br /><h2 align='center'>".htmlentities($msg["gen_phonetique_en_cours"], ENT_QUOTES, $charset)."</h2>";

$query = pmb_mysql_query("select id_word,word from words LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {

    // définition de l'état de la jauge
    $state = floor($start / ($count / $jauge_size));

    // mise à jour de l'affichage de la jauge
    print "<table border='0' align='center' width='$jauge_size' cellpadding='0' border='0'><tr><td class='jauge'>";
    print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";

    // calcul pourcentage avancement
    $percent = floor(($start/$count)*100);

    // affichage du % d'avancement et de l'état
    print "<div align='center'>$percent%</div>";
   	while($row = pmb_mysql_fetch_object($query)){
		$dmeta = new DoubleMetaPhone($row->word);
		$stemming = new stemming($row->word);
		$element_to_update = "";
		if($dmeta->primary || $dmeta->secondary){
			$element_to_update.="
			double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
		}
		if($element_to_update) $element_to_update.=",";
		$element_to_update.="stem = '".$stemming->stem."'";
		
		if ($element_to_update){
			pmb_mysql_query("update words set ".$element_to_update." where id_word = '".$row->id_word."'");
		}
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
	$spec = $spec - GEN_PHONETIQUE;
	$v_state .= "<br /><img src=../../images/d.gif hspace=3>";
	$v_state .= $count." ".htmlentities($msg["gen_phonetique_end"], ENT_QUOTES, $charset);
	$opt = pmb_mysql_query('OPTIMIZE TABLE words');
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