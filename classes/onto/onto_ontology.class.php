<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontology.class.php,v 1.25 2014-09-17 10:41:18 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/onto/onto_store.class.php");
require_once($class_path."/onto/common/onto_common_class.class.php");
require_once($class_path."/onto/common/onto_common_property.class.php");
require_once($class_path."/onto/onto_class.class.php");
require_once($class_path."/onto/onto_property.class.php");


/**
 * class onto_ontology
 * 
 */
class onto_ontology {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Tableau des URI & infos annexes des classes
	 * @access protected
	 */
	protected $classes_uris;

	/**
	 * Tableau des instances des classes déjà  lues
	 * @access private
	 */
	private $classes;

	/**
	 * Tableau des URI & infos annexes (domaine, range) des propriétés de l'ontologie
	 * @access private
	 */
	private $properties_uri;
	
	/**
	 * Tableau des instances des propriétés déjà  lues
	 * @access private
	 */
	private $properties;

	/**
	 * nom de l'ontologie (pmb:name)
	 * @access public
	 */
	public $name = "";

	/**
	 * Store de l'ontologie
	 * @var onto_store
	 * @access private
	 */
	private $store;
	
	/**
	 * Tableau des URI des propriétés inverse
	 * @access private
	 */
	private $inverse_of;
	
	/**
	 * 
	 *
	 * @param onto_store store 

	 * @return void
	 * @access public
	 */
	public function __construct( $store ) {
		$this->store = $store;
		$this->get_name();
		$this->get_classes();
		$this->get_properties();
		$this->get_inverse_of_properties();
	} // end of member function __construct

	/**
	 * Récupère le nom informatique de l'ontologie
	 *
	 * @return void
	 * @access private
	 */	
	private function get_name(){
		$query = "select ?name ?title where {
			?onto rdf:type <http://www.w3.org/2002/07/owl#Ontology> .
			?onto pmb:name ?name . 
			optional {		
				?onto dct:title ?title
			}
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$result = $this->store->get_result();
				$this->name = $result[0]->name;
				$this->title = $result[0]->title;
			}
		}else{
			highlight_string(print_r($this->store->get_errors(),true));
		}
	}
	
	/**
	 * Récupère la liste des URI des classes de l'ontologie (propriété classes_uri)
	 *
	 * @return void
	 * @access public
	 */
	public function get_classes( ) {
		$query  = "select * where { 
			?class rdf:type <http://www.w3.org/2002/07/owl#Class> .
			?class rdfs:label ?label .
			?class pmb:name ?name
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$result = $this->store->get_result();
				foreach ($result as $elem){
					$class = new onto_class();
					$class->uri = $elem->class;
					$class->name = $elem->label;
					$class->pmb_name = $elem->name;
					$this->classes_uris[$elem->class] = $class;					
				}
			}
		}else{
			highlight_string(print_r($this->store->get_errors(),true));
		}
		return $this->classes;
		
	} // end of member function get_classes

	/**
	 * Retourne une instance de la classe correspondante à l'URI
	 *
	 * @param string uri_class 

	 * @return onto_common_class
	 * @access public
	 */
	public function get_class( $uri_class ) {
		if(!$this->classes[$uri_class]){
			$elements = array(
				'class_uri' => $uri_class
			);
			$class_name = $this->get_class_name("class", $elements);
			$this->classes[$uri_class] = new $class_name($uri_class,$this);
			$this->classes[$uri_class]->set_pmb_name($this->classes_uris[$uri_class]->pmb_name);
			$this->classes[$uri_class]->set_onto_name($this->name);
		}
		return $this->classes[$uri_class];		
	} // end of member function get_class

	/**
	 * Récupère les cardinalités entre une classe et une propriété
	 *
	 * @param string uri_class 

	 * @param string uri_property 

	 * @return onto_restriction
	 * @access public
	 */
	public function get_restriction($uri_class,$uri_property) {
		$restriction = new onto_restriction();
		//recherche des exlusions !
		$query = "select ?distinct where {
			<".$uri_property."> pmb:distinctWith ?distinct
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$results = $this->store->get_result();
				foreach ($results as $result){
					$restriction->set_new_distinct($this->get_property($uri_class, $result->distinct));
				}
			}
		}else{
			var_dump($this->store->get_errors());
		}
		$query = "select ?max ?min where {
			<".$uri_class."> rdf:type <http://www.w3.org/2002/07/owl#Class> .
			<".$uri_class."> rdfs:subClassOf ?restrict .	
			?restrict rdf:type <http://www.w3.org/2002/07/owl#Restriction> .
			?restrict owl:onProperty <".$uri_property."> .		
			optional {
				?restrict owl:maxCardinality ?max
			} .
			optional {
				?restrict owl:minCardinality ?min
			}
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$results = $this->store->get_result();
				foreach ($results as $result){
					if($result->min){
						$restriction->set_min($result->min);
					}
					if($result->max){
						$restriction->set_max($result->max);
					}
				}
			}
		}else{
			var_dump($this->store->get_errors());
		}
		return $restriction;
	} // end of member function get_card

	/**
	 * Retourne la liste des URI des propriétés avec les domain et range (propriété
	 * properties_uri)
	 *
	 * @return void
	 * @access public
	 */
	public function get_properties( ) {
		if (!$this->properties_uri) {
			$query  = "select * where {
				?property rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .
				?property rdfs:label ?label .
				?property pmb:name ?name . 
				optional {
					?property rdfs:range ?range
				} .
				optional {
					?property rdfs:domain ?domain
				}
				optional {
					?property pmb:datatype ?datatype
				} .
				optional {
					?property pmb:defaultValueType ?default_value .
					?property pmb:defaultValue ?default_value_name
				} . 
				optional {
					?property pmb:flag ?flag				
				}
			}";
			if($this->store->query($query)){
				if($this->store->num_rows()){
					$result = $this->store->get_result();
					foreach ($result as $elem){
						if(!isset($this->properties_uri[$elem->property])){
							$this->properties_uri[$elem->property] = new onto_property();
							$this->properties_uri[$elem->property]->uri = $elem->property;
							$this->properties_uri[$elem->property]->name = $elem->label;
							$this->properties_uri[$elem->property]->pmb_name = $elem->name;
							$this->properties_uri[$elem->property]->pmb_datatype = $elem->datatype;
						}
						if($elem->domain){
							$this->properties_uri[$elem->property]->domain[] = $elem->domain;
						}
						if($elem->range){
							if(!$this->properties_uri[$elem->property]->range) {
								$this->properties_uri[$elem->property]->range = array();
							}
							//il faut gérer le cas du noeud blanc
							if($elem->range_type == "bnode"){
								$this->properties_uri[$elem->property]->range = array_merge($this->properties_uri[$elem->property]->range,$this->get_recursive_blank_range($elem->range));
							}else{
								if(!in_array($elem->range,$this->properties_uri[$elem->property]->range)){
									$this->properties_uri[$elem->property]->range[] = $elem->range;
								}
							}
						}
						if($elem->default_value){
							$this->properties_uri[$elem->property]->default_value = array(
								'value' => $elem->default_value_name,
								'type' => $elem->default_value
							);
						}
						if($elem->flag){
							if(!$this->properties_uri[$elem->property]->flags) {
								$this->properties_uri[$elem->property]->flags = array();
							}
							$this->properties_uri[$elem->property]->flags[] = $elem->flag;
						}
					}
				}
				//on vérifie, si aucun domaine précisé, on peut mettre la propriété partout
				foreach($this->properties_uri as $property_uri => $property){
					if(!count($property->domain)){
						foreach($this->classes_uris as $class_uri => $class){
							$this->properties_uri[$property_uri]->domain[] = $class_uri;
						}
					}
				}
			}else{
				highlight_string(print_r($this->store->get_errors(),true));
			}
		}
		return $this->properties_uri;
	} // end of member function get_properties

	
	protected function get_recursive_blank_range($brange){
		$range = array();
		$query = "select * where {
			<".$brange."> ?prop ?value .
		}";
		$this->store->query($query);
		$range_results = $this->store->get_result();
		foreach($range_results as $range_result){
			switch($range_result->prop){
				case "http://www.w3.org/2002/07/owl#unionOf" :
					$range = array_merge($range,$this->get_recursive_blank_range($range_result->value));
					break;
				case "http://www.w3.org/1999/02/22-rdf-syntax-ns#first":
					$range[] = $range_result->value;
					break;
				case "http://www.w3.org/1999/02/22-rdf-syntax-ns#rest" :
					if($range_result->value_type == "bnode"){
						$range = array_merge($range,$this->get_recursive_blank_range($range_result->value));
					}else if ($range_result->value != "http://www.w3.org/1999/02/22-rdf-syntax-ns#nil"){
						$range[] = $range_result->value;  
					}
					break;
			}
		}
		return $range;
	}
	
	public function get_class_properties($class_uri){
		$properties = array();
		foreach($this->properties_uri as $property_uri => $property){
			if(in_array($class_uri,$property->domain)){
				$properties[] = $property_uri;
			}
		}
		return $properties;
	}
	/**
	 * 
	 *
	 * @param string uri_property 

	 * @return onto_common_property
	 * @access public
	 */
	public function get_property($uri_class, $uri_property ) {
		if(!$this->properties[$uri_property][$uri_class]){
			$elements = array(
					'class_uri' => $uri_class,
					'property_uri' => $uri_property
			);
			$class_name = $this->get_class_name("property", $elements);
			$this->properties[$uri_property][$uri_class] = new $class_name($uri_property,$this);
			$this->properties[$uri_property][$uri_class]->set_domain($this->properties_uri[$uri_property]->domain);
			$this->properties[$uri_property][$uri_class]->set_range($this->properties_uri[$uri_property]->range);
			$this->properties[$uri_property][$uri_class]->set_pmb_name($this->properties_uri[$uri_property]->pmb_name);
			if($this->inverse_of[$uri_property]){
				$this->properties[$uri_property][$uri_class]->set_inverse_of($this->get_property($uri_class,$this->inverse_of[$uri_property]));
			}
			$this->properties[$uri_property][$uri_class]->set_onto_name($this->name);
		}
		return $this->properties[$uri_property][$uri_class];
		
	} // end of member function get_property

	/**
	 * 
	 *
	 * @param string class_prefix 

	 * @param Array() elements Tableau des noms pour déterminer le nom de la classe. Associatif ?

	 * @return string
	 * @access public
	 */
	public function get_class_name( $class_prefix,  $elements ) {
		return $this->search_class_name($class_prefix, $this->name, $elements);
	} // end of member function get_class_name

	protected function search_class_name($class_prefix,$name,$elements){
		switch ($class_prefix){
			case "class" :
				/*
				 * $elements['class_uri'] => URI de la classe
				 */
				//ex : onto_skos_class_concept
				$class_name = "onto_".$name."_".$class_prefix."_".$this->classes_uris[$elements['class_uri']]->pmb_name;
				if(!class_exists($class_name)){
					//ex : onto_skos_class
					$class_name = "onto_".$name."_".$class_prefix;
					if(!class_exists($class_name)){
						$class_name = $this->search_class_name($class_prefix,"common",$elements);
					}
				}
				break;
			case "property" :
				/*
				 * $elements['class_uri'] => URI de la classe
				 * $elements['property_uri'] => URI de la classe
				 */
				//ex : onto_skos_class_concept_property_inscheme
				$class_name = "onto_".$name."_class_".$this->classes_uris[$elements['class_uri']]->pmb_name."_".$class_prefix."_".$this->properties_uri[$elements['property_uri']]->pmb_name;
				if(!class_exists($class_name)){
					//ex : onto_skos_property_inscheme
					$class_name = "onto_".$name."_".$class_prefix."_".$this->properties_uri[$elements['property_uri']]->pmb_name;
					if(!class_exists($class_name)){
						//ex : onto_skos_property
						$class_name = "onto_".$name."_".$class_prefix;
						if(!class_exists($class_name)){
							$class_name = $this->search_class_name($class_prefix, "common", $elements);
						}
					}
				}
				break;
		}
		return $class_name;
	}
	
	public function get_label($object){
		global $msg,$lang;
		if(!$this->name){
			$this->get_name();
		}

		if(isset($msg['onto_'.$this->name.'_'.$object->pmb_name])){
			//le message PMB spécifique pour l'ontologie courante
			$label = $msg['onto_'.$this->name.'_'.$object->pmb_name];
		}else if (isset($msg['onto_common_'.$object->pmb_name])){
			//le message PMB générique
			$label = $msg['onto_common_'.$object->pmb_name];
		}else {
			$label = $object->name;
		}
		return $label;
	}

	public function get_class_label($uri_class){
		return $this->get_label($this->classes_uris[$uri_class]);
	}
	
	public function get_property_label($uri_property){
		return $this->get_label($this->properties_uri[$uri_property]);
	}
	
	public function get_property_pmb_datatype($uri_property){
		return $this->properties_uri[$uri_property]->pmb_datatype;
	}
	
	public function get_property_default_value($uri_property){
		return $this->properties_uri[$uri_property]->default_value;
	}
	
	public function get_classes_uri(){
		return $this->classes_uris;
	}
	
	public function get_inverse_of_properties(){
		if(!count($this->inverse_of)){
			$query = "select * {
				?property owl:inverseOf ?inverse
			}";
			$this->store->query($query);
			$inverse_results = $this->store->get_result();
			foreach($inverse_results as $inverse_of){
				$this->inverse_of[$inverse_of->property] = $inverse_of->inverse;
			}
		}
		return $this->inverse_of;
	}
	
	public function get_flags($uri_class="",$uri_property=""){
		$flags = array();
		if($class_uri && isset($this->classes_uri[$uri_class]->flags)){
			$flags = $this->classes_uri[$uri_class]->flags;
		}else if (isset($this->properties_uri[$uri_property]->flags)){
			$flags = $this->properties_uri[$uri_property]->flags;
		}
		return $flags;
	}
} // end of onto_ontology