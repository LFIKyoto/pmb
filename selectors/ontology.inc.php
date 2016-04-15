<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontology.inc.php,v 1.14 2014-09-11 09:02:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/* $caller = Nom du formulaire appelant
 * $objs = type d'objet demandé
 * $element = id de l'element à modifier
 * $order = numéro du champ à modifier
 * $range = id du range à afficher
 * $deb_rech = texte à rechercher 
 */

if (!isset($range)) $range = 0;
if (!isset($page)) $page = 1;
if($parent_id){
	$deb_rech= "";
}
$base_url = "./select.php?what=ontology&caller=".rawurlencode($caller)."&objs=$objs&element=$element&order=$order&infield=$infield&callback=$callback&dyn=$dyn&deb_rech=$deb_rech&param1=$param1&param2=$param2";

// contenu popup selection
require('./selectors/templates/sel_ontology.tpl.php');

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

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

$params=new onto_param(
		array(
				'categ'=>'',
				'sub'=>'',
				'objs'=>$objs,
				'action'=>'list_selector',
				'page'=>'1',
				'nb_per_page'=>'20',
				'caller'=>$caller,
				'element'=>$element,
				'order'=>$order,
				'callback'=>$callback,
				'base_url'=>$base_url,
				'deb_rech'=>$deb_rech,
				'range'=>$range,
				'parent_id'=>'',
				'param1' => $param1,
				'param2' => $param2,
				'item_uri' => $item_uri,
				
				'concept_scheme'=>$deflt_concept_scheme,
				'only_top_concepts' => '0'
));

$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel',$params);
$onto_ui->proceed();

// ?>