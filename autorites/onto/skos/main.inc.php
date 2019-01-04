<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.18 2018-11-22 13:33:52 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($authority_statut))  $authority_statut = 0;

$params = new onto_param(array(
	'categ'=>'concepts',
	'sub'=> 'concept',
	'action'=>'list',
	'page'=>'1',
	'nb_per_page'=> $nb_per_page_gestion,
	'id'=>'',
	'parent_id'=>'',
	'user_input'=>'',
	'concept_scheme'=>((isset($_SESSION['onto_skos_concept_last_concept_scheme']) && ($_SESSION['onto_skos_concept_last_concept_scheme'] !== "")) ? $_SESSION['onto_skos_concept_last_concept_scheme'] : $deflt_concept_scheme),
	'item_uri' => "",
	'only_top_concepts' => ((empty($skos_concept_search_form_submitted) && isset($_SESSION['onto_skos_concept_only_top_concepts'])) ? $_SESSION['onto_skos_concept_only_top_concepts'] : 0),
	'base_resource'=> "autorites.php",
	/* Pour le replace */
	'by' => '',
	'aut_link_save' => '',
	'authority_statut' => $authority_statut,
	'thesaurus_concepts_autopostage' => (!empty($thesaurus_concepts_autopostage) ? $thesaurus_concepts_autopostage : 0)
));

$onto_ui = new onto_ui("", skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel', $params);
$onto_ui->proceed();