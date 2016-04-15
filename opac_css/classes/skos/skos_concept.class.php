<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_concept.class.php,v 1.14 2015-06-18 14:23:54 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/skos/skos_datastore.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/category.class.php");
require_once($class_path."/publisher.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/explnum.class.php");
require_once($class_path."/authperso_authority.class.php");

if(!defined(TYPE_NOTICE)){
	define(TYPE_NOTICE,1);
}
if(!defined(TYPE_AUTHOR)){
	define(TYPE_AUTHOR,2);
}
if(!defined(TYPE_CATEGORY)){
	define(TYPE_CATEGORY,3);
}
if(!defined(TYPE_PUBLISHER)){
	define(TYPE_PUBLISHER,4);
}
if(!defined(TYPE_COLLECTION)){
	define(TYPE_COLLECTION,5);
}
if(!defined(TYPE_SUBCOLLECTION)){
	define(TYPE_SUBCOLLECTION,6);
}
if(!defined(TYPE_SERIE)){
	define(TYPE_SERIE,7);
}
if(!defined(TYPE_TITRE_UNIFORME)){
	define(TYPE_TITRE_UNIFORME,8);
}
if(!defined(TYPE_INDEXINT)){
	define(TYPE_INDEXINT,9);
}
if(!defined(TYPE_EXPL)){
	define(TYPE_EXPL,10);
}
if(!defined(TYPE_EXPLNUM)){
	define(TYPE_EXPLNUM,11);
}
if(!defined(TYPE_AUTHPERSO)){
	define(TYPE_AUTHPERSO,12);
}

/**
 * class skos_concept
 * Le modèle d'un concept
*/
class skos_concept {
	
	/**
	 * Identifiant du concept
	 * @var int
	 */
	private $id;
	
	/**
	 * URI du concept
	 * @var string
	 */
	private $uri;
	
	/**
	 * Label du concept
	 * @var string
	 */
	private $display_label;
	
	/**
	 * Tableau des schemas du concept
	 * @var string
	 */
	private $schemes;
	
	/**
	 * Vedette composée associée si concept composé
	 * @var vedette_composee
	 */
	private $vedette = null;
	
	/**
	 * Enfants du concept
	 * @var skos_concepts_list
	 */
	private $narrowers;
	
	/**
	 * Parents du concept
	 * @var skos_concepts_list
	 */
	private $broaders;
	
	/**
	 * Concepts composés qui utilisent ce concept
	 * @var skos_concepts_list
	 */
	private $composed_concepts;
	
	/**
	 * Tableau des identifiants de notices indexées par le concept
	 * @var array
	 */
	private $indexed_notices;
	
	/**
	 * Tableau associatif de tableaux d'autorités indexées par le concept
	 * @var array
	 */
	private $indexed_authorities;
	
	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 */
	public function __construct($id = 0, $uri = "") {
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_display_label();
		} else {
			$this->uri = $uri;
			$this->get_id();
			$this->get_display_label();
		}
	}
	
	/**
	 * Retourne l'URI du concept
	 */
	public function get_uri() {
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_uri($this->id);
		}
		return $this->uri;
	}
	
	/**
	 * Retourne l'identifiant du concept
	 * @return int
	 */
	public function get_id() {
		if (!$this->id) {
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->id;
	}
	
	/**
	 * Retourne le libellé à afficher
	 * @return string
	 */
	public function get_display_label() {
		if (!$this->display_label) {
			global $lang;
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
			}";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $key=>$result){
					if($result->label_lang==substr($lang,0,2)){
						$this->display_label = $result->label;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->display_label){
					$this->display_label = $result->label;
				}
			}
		}
		return $this->display_label;
	}
	
	/**
	 * Retourne les schémas du concept
	 * @return string
	 */
	public function get_schemes() {
		global $dbh, $lang;
		
		if (!$this->schemes) {
			$query = "select value, lang from skos_fields_global_index where id_item = ".$this->id." and code_champ = 4 and code_ss_champ = 1";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->lang == substr($lang,0,2)) {
						$this->schemes = $row->value;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->schemes) {
					$this->schemes = $row->value;
				}
			}
		}
		return $this->schemes;
	}
	
	/**
	 * Retourne la vedette composée associée au concept
	 * @return vedette_composee
	 */
	public function get_vedette() {
		if (!$this->vedette) {
			if ($vedette_id = vedette_link::get_vedette_id_from_object($this->id, TYPE_CONCEPT_PREFLABEL)) {
				$this->vedette = new vedette_composee($vedette_id);
			}
		}
		return $this->vedette;
	}
	
	/**
	 * Retourne les enfants du concept
	 * @return skos_concepts_list Liste des enfants du concept
	 */
	public function get_narrowers() {
		if (!$this->narrowers) {
			$this->narrowers = new skos_concepts_list();
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower
			}";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->narrowers->add_concept(new skos_concept(0, $result->narrower));
				}
			}
		}
		return $this->narrowers;
	}
	
	/**
	 * Retourne les parents du concept
	 * @return skos_concepts_list Liste des parents du concept
	 */
	public function get_broaders() {
		if (!$this->broaders) {
			$this->broaders = new skos_concepts_list();
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader
			}";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->broaders->add_concept(new skos_concept(0, $result->broader));
				}
			}
		}
		return $this->broaders;
	}
	
	/**
	 * Retourne les identifiants des notices indexées par le concept
	 * @return array Tableau des notices indexées par le concept
	 */
	public function get_indexed_notices() {
		global $dbh;
		
		if (!$this->indexed_notices) {
			$this->indexed_notices = array();
			
			$query = "select num_object from index_concept where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->indexed_notices[] = $row->num_object;
				}
			}
		}
		return $this->indexed_notices;
	}
	
	/**
	 * Retourne les autorités indexées par le concept
	 * @return array Tableau associatif de tableaux d'autorités indexées par le concept
	 */
	public function get_indexed_authorities() {
		global $dbh;
		
		if (!$this->indexed_authorities) {
			$this->indexed_authorities = array();
			
			$query = "select num_object, type_object from index_concept where num_concept = ".$this->id." and type_object != ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					switch ($row->type_object) {
						case TYPE_AUTHOR :
							$this->indexed_authorities['author'][] = new auteur($row->num_object);
							break;
						case TYPE_CATEGORY :
							$this->indexed_authorities['category'][] = new category($row->num_object);
							break;
						case TYPE_PUBLISHER :
							$this->indexed_authorities['publisher'][] = new publisher($row->num_object);
							break;
						case TYPE_COLLECTION :
							$this->indexed_authorities['collection'][] = new collection($row->num_object);
							break;
						case TYPE_SUBCOLLECTION :
							$this->indexed_authorities['subcollection'][] = new subcollection($row->num_object);
							break;
						case TYPE_SERIE :
							$this->indexed_authorities['serie'][] = new serie($row->num_object);
							break;
						case TYPE_TITRE_UNIFORME :
							$this->indexed_authorities['titre_uniforme'][] = new titre_uniforme($row->num_object);
							break;
						case TYPE_INDEXINT :
							$this->indexed_authorities['indexint'][] = new indexint($row->num_object);
							break;
						case TYPE_EXPL :
							//TODO Quelle classe utiliser ?
// 							$this->indexed_authorities['expl'][] = new auteur($row->num_object);
							break;
						case TYPE_EXPLNUM :
							$this->indexed_authorities['explnum'][] = new explnum($row->num_object);
							break;
						case TYPE_AUTHPERSO :
							$this->indexed_authorities['authperso'][] = new authperso_authority($row->num_object);
							break;
						default:
							break;
					}
				}
			}
		}
		return $this->indexed_authorities;
	}
	
	/**
	 * Retourne les concepts composés qui utilisent le concept
	 * @return skos_concepts_list Liste des concepts composés qui utilisent le concept
	 */
	public function get_composed_concepts() {
		if (!$this->composed_concepts) {
			$this->composed_concepts = new skos_concepts_list();
			
			$this->composed_concepts->set_composed_concepts_built_with_element($this->id, "concept");
		}
		return $this->composed_concepts;
	}

	/**
	 * Retourne le détail d'un concept
	 * @return array Tableau des différentes propriétés du concept
	 */
	public function get_details() {
		global $lang;
		$details = array();
		$query = "select * where {
				<".$this->uri."> rdf:type skos:Concept .
				<".$this->uri."> skos:prefLabel ?label .		
				optional {
					<".$this->uri."> skos:altLabel ?altlabel
				} . 
				optional {
					<".$this->uri."> skos:note ?note
				} .
				optional {
					<".$this->uri."> <http://www.w3.org/2004/02/skos/core#Note> ?notebnf
				} .			
				optional {
					<".$this->uri."> skos:related ?related .
					optional {		
						?related skos:prefLabel ?relatedlabel	
					}
				} .
				optional {
					<".$this->uri."> skos:related ?related .
					optional {		
						?related skos:prefLabel ?relatedlabel	
					}
				} .
				optional {
					<".$this->uri."> owl:sameAs ?sameas .
					optional {		
						?sameas skos:prefLabel ?sameaslabel	
					}
				} .
				optional {
					<".$this->uri."> rdfs:seeAlso ?seealso .
					optional {		
						?seealso skos:prefLabel ?seealsolabel	
					}
				} .
				optional {
					<".$this->uri."> skos:exactMatch ?exactmatch .
					optional {		
						?exactmatch skos:prefLabel ?exactmatchlabel	
					}
				} .
				optional {
					<".$this->uri."> skos:closeMatch ?closematch .
					optional {		
						?closematch skos:prefLabel ?closematchlabel	
					}
				}
			}";
			
		skos_datastore::query($query);
		if(skos_datastore::num_rows()){
			$results = skos_datastore::get_result();
			foreach($results as $result){
				foreach($result as $property => $value){
					switch($property){
						//cas des literaux
						case "altlabel" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#altLabel'])){
								$details['http://www.w3.org/2004/02/skos/core#altLabel'] = array();
							}
							if(isset($result->{$propery."_lang"}) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;
						case "hiddenlabel" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])){
								$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'] = array();
							}
							if(isset($result->hiddenlabel_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;							
						case "related" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#related'])){
								$details['http://www.w3.org/2004/02/skos/core#related'] = array();
							}
							if($result->related_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->relatedlabel)){
									$detail['label'] = $result->relatedlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#related'])){
									$details['http://www.w3.org/2004/02/skos/core#related'][] = $detail;
								}
							}
							break;
						case "sameas" :
							if(!isset($details['http://www.w3.org/2002/07/owl#sameAs'])){
								$details['http://www.w3.org/2002/07/owl#sameAs'] = array();
							}
							if($result->sameas_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->sameaslabel)){
									$detail['label'] = $result->sameaslabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2002/07/owl#sameAs'])){
									$details['http://www.w3.org/2002/07/owl#sameAs'][] = $detail;
								}
							}
							break;
						case "note" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#note'])){
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if(isset($result->note_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "notebnf" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#note'])){
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if(isset($result->notebnf_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "seealso" :
							if(!isset($details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])){
								$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'] = array();
							}
							if($result->seealso_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->seealsolabel)){
									$detail['label'] = $result->seealsolabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])){
									$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'][] = $detail;
								}
							}
							break;
						case "exactmatch" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#exactMatch'])){
								$details['http://www.w3.org/2004/02/skos/core#exactMatch'] = array();
							}
							if($result->exactmatch_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->exactmatchlabel)){
									$detail['label'] = $result->exactmatchlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#exactMatch'])){
									$details['http://www.w3.org/2004/02/skos/core#exactMatch'][] = $detail;
								}
							}
							break;
						case "closematch" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#closeMatch'])){
								$details['http://www.w3.org/2004/02/skos/core#closeMatch'] = array();
							}
							if($result->closematch_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->closematchlabel)){
									$detail['label'] = $result->closematchlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#closeMatch'])){
									$details['http://www.w3.org/2004/02/skos/core#closeMatch'][] = $detail;
								}
							}
							break;
					}
				}			
			}
		}
		return $details;
	}
}