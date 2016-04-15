<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_rdfstore.inc.php,v 1.5 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
$jauge_size .= "px";

// initialisation de la borne de départ
if (!isset($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE rdfstore_index",$dbh);
}

$v_state=urldecode($v_state);

if (!$count) {
	$q_count = "select count(1) from rdfstore_triple t, rdfstore_id2val l where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3";
	$r_count = pmb_mysql_query($q_count, $dbh);
	$count = pmb_mysql_result($r_count, 0, 0);
}

print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_rdfstore_reindexation"], ENT_QUOTES, $charset)."</h2>";

$q_sel = "
select t.t as num_triple, s.val as subject_uri, p.val as predicat_uri, o.id as num_object, o.val as object_val, l.val as object_lang 
from rdfstore_triple t, rdfstore_s2val s, rdfstore_id2val p, rdfstore_o2val o, rdfstore_id2val l  
where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3 and t.s=s.id and t.p=p.id and t.o=o.id 
order by t.t LIMIT $start, $lot";
$r_sel = pmb_mysql_query($q_sel,$dbh);

if(pmb_mysql_num_rows($r_sel)) {
	
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

	require_once("$class_path/rdf/ontology.class.php");
	$op = new ontology_parser("$class_path/rdf/skos_pmb.rdf");
	$sh = new skos_handler($op);

	while(($triple = pmb_mysql_fetch_object($r_sel))){
		$type=$sh->op->from_ns($sh->get_object_type($triple->subject_uri));
		$q_ins = "insert ignore into rdfstore_index ";
		$q_ins.= "set num_triple='".$triple->num_triple."', ";
		$q_ins.= "subject_uri='".addslashes($triple->subject_uri)."', ";
		$q_ins.= "subject_type='".addslashes($type)."', ";
		$q_ins.= "predicat_uri='".addslashes($triple->predicat_uri)."', ";
		$q_ins.= "num_object='".$triple->num_object."', "; 
		$q_ins.= "object_val ='".addslashes($triple->object_val)."', ";
		$q_ins.= "object_index=' ".strip_empty_chars($triple->object_val)." ', ";
		$q_ins.= "object_lang ='".addslashes($triple->object_lang)."' ";

		$r_ins = pmb_mysql_query($q_ins,$dbh);
	}
	
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
	$spec = $spec - INDEX_RDFSTORE;
	$not = pmb_mysql_query("select count(1) from rdfstore_triple where o_type=2", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg['nettoyage_rdfstore_reindexation'], ENT_QUOTES, $charset)." : ";
	$v_state .= $compte." ".htmlentities($msg['nettoyage_rdfstore_reindex_elt'], ENT_QUOTES, $charset);
	print "
		<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		</form>
		<script type=\"text/javascript\"><!--
			document.forms['process_state'].submit();
			-->
		</script>";
}

?>