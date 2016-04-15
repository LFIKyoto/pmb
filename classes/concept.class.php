<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept.class.php,v 1.8 2015-06-12 09:36:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/onto/onto_index.class.php");


/**
 * class concept
 * Un concept
 */
class concept {
	
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
	 * URI de la classe de l'ontologie
	 * @var string
	 */
	private $type;
	
	/**
	 * Label du concept
	 * @var string
	 */
	private $display_label;
	
	/**
	 * Store de donnée
	 * @var onto_store_arc2
	 */
	private $data_store;
	
	/**
	 * Nom du schema du concept
	 * @var string
	 */
	private $scheme;
	
	/**
	 * Tableau des identifiants de notices indexées par le concept
	 * @var array
	 */
	private $indexed_notices;
	
	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 * @param string $type [optional] URI de la classe de l'ontologie
	 * @param string $display_label [optional] Label du concept
	 */
	public function __construct($id, $uri = "", $type = "", $display_label = "") {
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_type();
			$this->get_display_label();
		} else {
			$this->uri = $uri;
			$this->get_id();
			$this->type = $type;
			$this->display_label = $display_label;
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
	 * Retourne le type du concept
	 * @return string
	 */
	public function get_type() {
		if (!$this->type) {
			$this->type = "http://www.w3.org/2004/02/skos/core#Concept";
		}
		return $this->type;
	}
	
	public function get_display_label() {
		if (!$this->display_label) {
			global $lang;
			
			$this->get_data_store();
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
			}";
			$this->data_store->query($query);
			if($this->data_store->num_rows()){
				$results = $this->data_store->get_result();
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
	
	private function get_data_store() {
		if (!$this->data_store) {
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
			
			$this->data_store = new onto_store_arc2($data_store_config);
		}
	}
	
	public function update_display_label($label) {
		global $base_path;
		
		$this->get_data_store();

		// On commence par supprimer le label existant
		$query = "delete {
				<".$this->get_uri()."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?obj
				}";
		$this->data_store->query($query);
		
		// On insert le nouveau label
		$query = "insert into <pmb> {
				<".$this->get_uri()."> <http://www.w3.org/2004/02/skos/core#prefLabel> '".$label."'
				}";
		
		$this->data_store->query($query);
		
		// On réindexe
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
		$onto_index->maj($this->get_id());
	}
	
	public function get_scheme() {
		global $dbh, $lang;
		
		if (!$this->scheme) {
			$query = "select value, lang from skos_fields_global_index where id_item = ".$this->id." and code_champ = 4 and code_ss_champ = 1";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->lang == substr($lang,0,2)) {
						$this->scheme = $row->value;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->scheme) {
					$this->scheme = $row->value;
				}
			}
		}
		return $this->scheme;
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
} // end of concept