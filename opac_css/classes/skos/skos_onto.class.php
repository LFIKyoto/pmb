<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_onto.class.php,v 1.3 2015-03-12 14:15:54 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_store_arc2.class.php");

/**
 * class skos_onto
 * Classe g�rant un acc�s au store de l'ontologie SKOS
*/
class skos_onto {
	/**
	 * Tableau des labels des propri�t�s des classes de l'ontologie SKOS
	 * @var array
	 * @access private
	 */
	private static $labels = array();
	
	/**
	 * Instance de la classe d'interrogation ARC2
	 * @var onto_store_arc2
	 * @access private
	 */
	private static $store = array();
	

	/**
	 * Inialisation de l'instance d'onto_store_arc2 dans self::$store
	 * @return void
	 * @access private
	 */
	private static function init(){
		if(!is_object(self::$store)){
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
			self::$store = new onto_store_arc2($onto_store_config);
			self::$store->set_namespaces(array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
			));
				
		}
	}
	
	
	/**
	 * Ex�cute une nouvelle requ�te SPARQL sur le store de l'ontologie SKOS
	 * @param query string  <p>Requ�te SPARQL a lancer sur le store ARC2</p>
	 * @return bool
	 * @access public
	 */
	public static function query($query){
		self::init();
		return self::$store->query($query);
	}
	
	/**
	 * Retourne le nombre de lignes de la derni�re requete SPARQL sur le store de l'ontologie SKOS
	 * @return <p>Nombre de lignes pour la derni�re requete<br>FALSE si le store n'est pas initialis�</p>
	 * @access public
	 */
	public static function num_rows(){
		if(is_object(self::$store)){
			return self::$store->num_rows();
		}
		return false;
	}
	
	/**
	 * Retourne le r�sulat de la derni�re requete SPARQL sur le store de l'ontologie SKOS
	 * @return <p>Tableau du r�sultat pour la derni�re requete<br>FALSE si le store n'est pas initialis�</p>
	 * @access public
	 */
	public static function get_result(){
		if(is_object(self::$store)){
			return self::$store->get_result();
		}
		return false;
	}
	
	/**
	 * Retoune le label PMB d'une propri�t� d'une classe de l'ontologie SKOS. Interroge le store si n�cessaire
	 * @param class_uri string  <p>URI de la classe associ�e</p>
	 * @param property_uri string  <p>URI de la propri�te dont on veut le label PMB</p>
	 * @return <p>Retourne le label associ�</p>
	 * @access public
	 */
	public static function get_property_label($class_uri,$property_uri){
		if(!isset(self::$labels[$class_uri])){
			self::get_properties_labels($class_uri);
		}
		if(isset(self::$labels[$class_uri][$property_uri])){
			return self::$labels[$class_uri][$property_uri]['label'];
		}else{
			return $property_uri;
		}
	}
		
	/**
	 * Retoune les labels PMB des propri�t�s d'une classe de l'ontologie SKOS. Interroge le store si n�cessaire
	 * @param class_uri string  <p>URI de la classe associ�e</p>
	 * @return <p>Retourne le tableau de labels associ�s</p>
	 * @access public
	 */
	public static function get_properties_labels($class_uri){
		// on trouve les libell�s?
		if(!isset(self::$labels[$class_uri])){
			//on recherche toutes les propri�t�s associ�s
			$query  = "select * where {
				?property rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .
				?property rdfs:label ?label .
				?property pmb:name ?name . 
				optional {
					?property rdfs:domain ?domain
				}				
			}";
			self::query($query);
			if(self::$store->num_rows()){
				$result = self::$store->get_result();
				//init de la static pour la classe concern�e
				self::$labels[$class_uri] = array();
				foreach ($result as $property){
					if(!isset($property->domain) || $property->domain == $class_uri){
						self::$labels[$class_uri][$property->property] = array(
							'pmb_name' => $property->name
						);
						self::$labels[$class_uri][$property->property]['label'] = self::calc_label($class_uri, $property->property,$property->label);
					}
				}
			}
		}
		return self::$labels[$class_uri];
	}

	/**
	 * R�cup�re le libell� appropri� d'une propri�t� d'une classe d'ontologie dans les messages PMB.
	 * @param class_uri string  <p>URI de la classe associ�e</p>
	 * @param property_uri string  <p>URI de la propri�te dont on veut le label PMB</p>
	 * @param default_label string  <p>Libell� r�cup�r� dans le store de l'ontologie</p>
	 * @return <p>Retourne le libell� le plus appropri� pour la propri�t� d'une classe de l'ontologie</p>
	 * @access private
	 */
	private static function calc_label($class_uri, $property_uri,$default_label = ""){
		global $msg;
		if(isset($msg['onto_skos_'.self::$labels[$class_uri][$property_uri]['pmb_name']])){
			//le message PMB sp�cifique pour l'ontologie courante
			$label = $msg['onto_skos_'.self::$labels[$class_uri][$property_uri]['pmb_name']];
		}else if (isset($msg['onto_common_'.self::$labels[$class_uri][$property_uri]['pmb_name']])){
			//le message PMB g�n�rique
			$label = $msg['onto_common_'.self::$labels[$class_uri][$property_uri]['pmb_name']];
		}else {
			$label = $default_label;
		}
		return $label;
	}
	
}