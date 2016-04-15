<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_faq.inc.php,v 1.3 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/indexation.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
$jauge_size .= "px";

// initialisation de la borne de départ
if (!isset($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE faq_questions_words_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE faq_questions_words_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE faq_questions_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

if (!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM faq_questions", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
}
	
print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_faq"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

$query = pmb_mysql_query("select id_faq_question from faq_questions order by id_faq_question LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
		
	// définition de l'état de la jauge
	$state = floor($start / ($count / $jauge_size));
	$state .= "px";
	// mise à jour de l'affichage de la jauge
	print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge' width='100%'>";
	print "<img src='../../images/jauge.png' width='$state' height='16px'></td></tr></table>";
		
	// calcul pourcentage avancement
	$percent = floor(($start/$count)*100);
	
	// affichage du % d'avancement et de l'état
	print "<div align='center'>$percent%</div>";
	$indexation = new indexation($include_path."/indexation/faq/question.xml", "faq_questions");
	while($row = pmb_mysql_fetch_assoc($query)) {		
		// permet de charger la bonne langue, mot vide...
		$info=$indexation->maj($row['id_faq_question']);
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
	<script type=\"text/javascript\"><!-- 
	setTimeout(\"document.forms['current_state'].submit()\",1000); 
	-->
	</script>";
} else {
	$spec = $spec - INDEX_FAQ;
	$not = pmb_mysql_query("SELECT count(distinct id_faq_question) FROM faq_questions_words_global_index", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_faq"], ENT_QUOTES, $charset)." :";
	$v_state .= $compte." ".htmlentities($msg["nettoyage_res_reindex_faq"], ENT_QUOTES, $charset);
	print "
		<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		</form>
		<script type=\"text/javascript\"><!--
			document.forms['process_state'].submit();
			-->
		</script>";
	pmb_mysql_query("ALTER TABLE faq_questions_words_global_index ENABLE KEYS",$dbh);
	pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index ENABLE KEYS",$dbh);
}