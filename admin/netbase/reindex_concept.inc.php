<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_concept.inc.php,v 1.2 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/autoloader.class.php');
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
$jauge_size .= "px";

// initialisation de la borne de départ
if (!isset($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE skos_words_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE skos_words_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE skos_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE skos_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

$onto_store_config = array(
		/* db */
		'db_name' => DATA_BASE,
		'db_user' => USER_NAME,
		'db_pwd' => USER_PASS,
		'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'ontology',
		/* stop after 100 errors */
		'max_errors' => 100,
		'store_strip_mb_comp_str' => 0
);
$data_store_config = array(
		/* db */
		'db_name' => DATA_BASE,
		'db_user' => USER_NAME,
		'db_pwd' => USER_PASS,
		'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'rdfstore',
		/* stop after 100 errors */
		'max_errors' => 100,
		'store_strip_mb_comp_str' => 0
);

$tab_namespaces=array(
		"skos"	=> "http://www.w3.org/2004/02/skos/core#",
		"dc"	=> "http://purl.org/dc/elements/1.1",
		"dct"	=> "http://purl.org/dc/terms/",
		"owl"	=> "http://www.w3.org/2002/07/owl#",
		"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
		"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
		"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
		"pmb"	=> "http://www.pmbservices.fr/ontology#"
);

$onto_index = new onto_index();
$onto_index->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
$onto_index->init();

$elem_query = "";
//la requete de base...
$query = "select * where {
		?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
		?item rdf:type ?type .
		filter(";
$i=0;
foreach($onto_index->infos as $uri => $infos){
	if($i) $query.=" || ";
	$query.= "?type=<".$uri.">";
	$i++;
}
$query.=")
	}";
if (!$count) {
	$onto_index->handler->data_query($query);
	$count = $onto_index->handler->data_num_rows();
}
	
print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

// $query = pmb_mysql_query("select id_faq_question from faq_questions order by id_faq_question LIMIT $start, $lot");
$query.= " order by asc(?label) limit ".$lot." offset ".$start;
$onto_index->handler->data_query($query);	
if($onto_index->handler->data_num_rows()) {
		
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
	$results = $onto_index->handler->data_result();
	foreach($results as $row){	
		$info=$onto_index->maj(0,$row->item);
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
	$spec = $spec - INDEX_CONCEPT;
	$not = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)." :";
	$v_state .= $compte." ".htmlentities($msg["nettoyage_res_reindex_concept"], ENT_QUOTES, $charset);
	print "
		<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		</form>
		<script type=\"text/javascript\"><!--
			document.forms['process_state'].submit();
			-->
		</script>";
	pmb_mysql_query("ALTER TABLE skos_words_global_index ENABLE KEYS",$dbh);
	pmb_mysql_query("ALTER TABLE skos_fields_global_index ENABLE KEYS",$dbh);
}