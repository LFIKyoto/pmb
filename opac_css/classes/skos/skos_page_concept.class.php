<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_page_concept.class.php,v 1.6 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
/**
 * class skos_page_concept
 * Controler d'une Page OPAC représentant un concept de SKOS
 */
class skos_page_concept {
	
	/**
	 * Instance du concept
	 * @var skos_concept
	 */
	private $concept;
	
	/**
	 * Constructeur d'une page concept
	 * @param int $concept_id Identifiant du concept à représenter
	 * @return void
	 */
	public function __construct($concept_id) {
		$concept_id+=0;
		$this->concept = new skos_concept($concept_id);
	}
	
	/**
	 * Affiche les données renvoyées par les vues
	 */
	public function proceed(){
		$authority = new authority("concept", $this->concept->get_id());
		
 		$context['authority']=array(
			//affichage des termes génériques...
			'broaders' => skos_view_concepts::get_broaders_list($this->concept->get_broaders()),
			//affichage de l'intitulé du concept
			'title' => skos_view_concept::get_concept($this->concept),
			//affichage des termes spécifiques...
			'narrowers' => skos_view_concepts::get_narrowers_list($this->concept->get_narrowers()),
			//toutes les informations du concept
			'details' => skos_view_concept::get_detail_concept($this->concept),
			//affichage des concepts composé utilisant le concept
			'composed_concepts' => skos_view_concepts::get_composed_concepts_list($this->concept->get_composed_concepts()),
			//notices indexées
			'recordslist' => skos_view_concept::get_notices_indexed_with_concept($this->concept),
			//autorités indexées
			'authoritieslist' => skos_view_concept::get_authorities_indexed_with_concept($this->concept)
 		);
		print $authority->render($context);
	}
	
	
	public function get_indexed_notices(){
		return $this->concept->get_indexed_notices();
	}
}