<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2015-03-04 14:26:24 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

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

$tab_namespaces = array(
	"skos"	=> "http://www.w3.org/2004/02/skos/core#",
	"dc"	=> "http://purl.org/dc/elements/1.1",
	"dct"	=> "http://purl.org/dc/terms/",
	"owl"	=> "http://www.w3.org/2002/07/owl#",
	"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
	"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
	"pmb"	=> "http://www.pmbservices.fr/ontology#"
);

$params = new onto_param(array(
	'categ'=>'concepts',
	'sub'=> 'concept',
	'action'=>'list',
	'page'=>'1',
	'nb_per_page'=>'20',
	'id'=>'',
	'parent_id'=>'',
	'user_input'=>'',
	'concept_scheme'=>((isset($_SESSION['onto_skos_concept_last_concept_scheme']) && ($_SESSION['onto_skos_concept_last_concept_scheme'] !== "")) ? $_SESSION['onto_skos_concept_last_concept_scheme'] : $deflt_concept_scheme),
	'item_uri' => "",
	'only_top_concepts' => '0',
));

$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel',$params);
$onto_ui->proceed();