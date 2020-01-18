<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_handler.class.php,v 1.21.4.1 2019-11-25 12:52:58 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_ontology.class.php");
require_once($class_path."/onto/common/onto_common_item.class.php");
require_once($class_path."/onto/onto_store.class.php");


/**
 * class onto_handler
 * 
 */
class onto_handler {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access protected
	 */
	protected $ontology;

	/**
	 * Store pour l'ontologie
	 * 
	 * @var onto_store
	 * 
	 * @access protected
	 */
	protected $onto_store;

	/**
	 * Store pour les donnn�es
	 * 
	 * @var onto_store
	 * 
	 * @access private
	 */
	protected $data_store;
	
	protected $default_display_label;
	
	private $nb_elements =array();

	/**
	 * 
	 *
	 * @param string ontology_filepath 
	 * @param string onto_store_type nom de la classe store � utiliser pour l'ontologie
	 * @param array() onto_store_config Configuration du store pour l'ontologie
	 * @param string data_store_type Nom de la classe � utiliser pour le store data
	 * @param Array() data_store_config Configuration du store data
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function __construct( $ontology_filepath,  $onto_store,  $onto_store_config,  $data_store_type,  $data_store_config,$tab_namespaces ,$default_display_label) {
			
		if (is_object($onto_store)) {
			$this->onto_store = $onto_store;
		} else {
			//on r�cup�re les stores...
			$onto_store_class = "onto_store_".$onto_store;
			$this->onto_store = new $onto_store_class($onto_store_config);
			$this->onto_store->set_namespaces($tab_namespaces);
			//chargement de l'ontologie dans son store
			if($ontology_filepath){
				$this->onto_store->load($ontology_filepath);
			}
		}
		$data_store_class = "onto_store_".$data_store_type;
		$this->data_store = new $data_store_class($data_store_config);
		$this->data_store->set_namespaces($tab_namespaces);
		
		$this->default_display_label=$default_display_label;
	} // end of member function __construct

	/**
	 * PARTIE DATASTORE
	 */
	
	/**
	 * revoie les assertion � inserer pour un item
	 *
	 * @param string $uri
	 *
	 * @return array
	 */
	public function get_assertions($uri){
		$assertions = array();
		$query = "select * where {
			<".$uri."> ?predicate ?object .
			optional {
				?object rdf:type ?type
			}.
			optional {
				?object pmb:has_assertions ?has_assertions.
			}
		}";
		$this->data_store->query($query);
		$results = $this->data_store->get_result();
		foreach($results as $assertion){
			$object_properties = array();
			foreach($assertion as $key=>$value){
				if(substr($key,0,strlen("object_")) == "object_"){
					$object_properties[substr($key,strlen("object_"))] = $value;
				}
			}
			if($object_properties['type'] == "literal"){
				$type = "http://www.w3.org/2000/01/rdf-schema#Literal";
			}else{
			    $type = (isset($assertion->type) ? $assertion->type : "");
				if(!$type){
					$type = $assertion->predicate;
				}else{
					$displayLabel=$this->get_display_label($assertion->type);
					$query="select ?display_label where {
						<".$assertion->object."> <".$displayLabel."> ?display_label
					}";
					$this->data_store->query($query);
					if($this->data_store->num_rows()){
						$result = $this->data_store->get_result();
						$object_properties['display_label'] = $result[0]->display_label;
					} else {//cas particulier pour les assertions qui auraient directement un displayLabel
						$query="select ?display_label where {
									<".$assertion->object."> pmb:displayLabel ?display_label
								}";
						$this->data_store->query($query);
						if($this->data_store->num_rows()){
							$result = $this->data_store->get_result();
							$object_properties['display_label'] = $result[0]->display_label;
						}
					}
				}
				if (!empty($assertion->has_assertions)) {
				    $object_properties["assertions"] = $this->get_assertions($assertion->object);
				}				
			}
			$assertions[] = new onto_assertion($uri, $assertion->predicate, $assertion->object, $type,$object_properties);
		}
		return $assertions;
	}
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans le data store
	 *
	 * @param string $query
	 *
	 */
	public function data_query($query){
		$this->data_store->query($query);
		if($this->data_store->num_rows()){
			return true;
		}elseif ($errs = $this->data_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans le data store
	 * renvoi le r�sultat
	 *
	 * @return array result
	 */
	public function data_result(){
		if($this->data_store->num_rows()){
			return $this->data_store->get_result();
		}elseif ($errs = $this->data_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans le data store
	 * renvoi le nombre de r�sultat
	 *
	 * @return integer num rows
	 */
	public function data_num_rows(){
		if($this->data_store->num_rows()){
			return $this->data_store->num_rows();
		}elseif ($errs = $this->data_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	public function get_data_label($uri){
		global $lang;
	
		$displayLabel=$this->get_display_label($uri);
	
		$query = "select * where {
			<".$uri.">  <".$displayLabel."> ?label
		}";
		$this->data_store->query($query);
	
		if($this->data_store->num_rows()){
			$results = $this->data_store->get_result();
			foreach($results as $key=>$result){
				if(isset($result->label_lang) && $result->label_lang==substr($lang,0,2)){
					return $result->label;
				}
			}
			//pas de langue de l'interface trouv�e
			foreach($results as $key=>$result){
				return $result->label;
			}
		}
	
	}

	
	public function get_nb_elements($class_uri,$more=""){
		
		if(!$this->nb_elements[$class_uri.$more]){
			$query="";
			$query.="select count(?elem) as ?nb_elem where {
				?elem rdf:type <".$class_uri.">";
			if($more){
				if(substr(trim($more),0,1) == '.'){
					$query.=$more;
				}else{
					$query.=' . '.$more;
				}
				
			}
			$query.="}";
			$this->data_store->query($query);
			$results = $this->data_store->get_result();
			$this->nb_elements[$class_uri.$more] = $results[0]->nb_elem;
		}
		return $this->nb_elements[$class_uri.$more];
	}
	

	/**
	 * Supprime et recr�e les d�clarations de l'instance pass�e en param�tre
	 *
	 * @param onto_common_item $item Instance � sauvegarder
	 * 
	 * @return bool
	 * 
	 * @access public
	 */
	public function save( $item ) {
		global $area_id;		
		
		if ($item->check_values()) {	
			if(onto_common_uri::is_temp_uri($item->get_uri())){
				$item->replace_temp_uri();
			}
			$assertions = $item->get_assertions();
			
			// On peut y aller
			$query = "insert into <pmb> {";
			$query .= $this->build_triples($assertions, $item->get_uri());
			$query .= ".\n <".addslashes($assertions[0]->get_subject())."> pmb:area ".$area_id;
			
			//on ne rentre qu'une seule, afin de ne pas �craser le display label
			if($assertions[0]->get_object_properties()['type'] == "uri") {					
			    $display_label = $item->get_label($this->get_display_label($assertions[0]->get_object()));
				
				//si pas de display label, on va chercher celui du parent
				if (!$display_label) {
				    $sub_class_of = $this->ontology->get_sub_class_of($assertions[0]->get_object());
					foreach ($sub_class_of as $parent_uri) {							
						$display_label = $item->get_label($this->get_display_label($parent_uri));
						if ($display_label) {
							break;
						}
					}
				}					
				$query .= " .\n <".addslashes($assertions[0]->get_subject())."> pmb:displayLabel '".addslashes($display_label)."'";
			}
			
			$query.="}";
			
			$this->data_store->query($query);
			if ($errs = $this->data_store->get_errors()) {
				print "<br/>Erreurs: <br/>";
				print "<pre>";print_r($errs);print "</pre><br/>";
			}else{
			    //TODO: a reprendre plus tard si besoin (indexation des contribution par exemple...)
			    if ($this->get_onto_name() == "skos") {
    				$index = new onto_index();
    				$index->set_handler($this);
    				$index->maj(0,$item->get_uri());
			    }
			}
		} else {
			return $item->get_checking_errors();
		}
		return true;
	} // end of member function save

	
	
	/**
	 * D�truit une instance (l'ensemble de ses d�clarations)
	 *
	 * @param onto_common_item $item Instance � supprimer (l'ensemble de ses d�clarations)
	 * @param bool $force_delete Si false, renvoie un tableau des assertions o� l'item est objet. Si true, supprime toutes les occurences de l'item
	 * 
	 * @return bool
	 * @access public
	 */
	public function delete($item, $force_delete = false) {
		global $dbh;
		
		// On stockera dans un tableau tous les triplets desquels l'item est l'objet
		$is_object_of = array();
		
		$query = "select * where {
			?subject ?predicate <".$item->get_uri().">
		}";
		$this->data_store->query($query);
		$result = $this->data_store->get_result();
		
		foreach ($result as $assertion) {
			$is_object_of[] = new onto_assertion($assertion->subject, $assertion->predicate, $item->get_uri());
		}
		
		if ($force_delete || !count($is_object_of)) {
			$query = "delete {
				<".$item->get_uri()."> ?prop ?obj
			}";
			$this->data_store->query($query);
			
			if ($errs = $this->data_store->get_errors()) {
				print "<br>Erreurs: <br>";
				print "<pre>";print_r($errs);print "</pre><br>";
			} else {
				$query = "delete {
					?subject ?predicate <".$item->get_uri().">
				}";
				$result = $this->data_store->query($query);
				
				if ($errs = $this->data_store->get_errors()) {
					print "<br>Erreurs: <br>";
					print "<pre>";print_r($errs);print "</pre><br>";
				}else{
					// On met � jour l'index
					$index = new onto_index();
					$index->set_handler($this);
					$index->maj(0,$item->get_uri());
					
					if (count($is_object_of)) {
						foreach ($is_object_of as $object) {
						    $index->maj(0,$object->subject);
						}
					}
					
					//on a tout vir� on supprime aussi l'URI dans la table
					$query = "delete from onto_uri where uri = '".$item->get_uri()."'";
					pmb_mysql_query($query, $dbh);
				}
			}
		}
		return $is_object_of;
	} // end of member function delete
	
	/**
	 * PARTIE DATASTORE
	 */
	
	
	/**
	 * Retourne l'item le plus appropri� pour d�finir l'URI pass�e en param�tre
	 *
	 * @param string class_uri URI de la classe de l'ontologie � instancier
	 * @param string uri URI de l'instance � cr�er
	 *
	 * @return onto_common_item $item
	 *
	 * @access public
	 */
	public function get_item($class_uri,$uri) {
		$item_class = "onto_".$this->ontology->name."_".$this->get_class_pmb_name($class_uri)."_item";
		if(!class_exists($item_class)){
			$item_class = "onto_".$this->ontology->name."_item";
		}
		if(!class_exists($item_class)){
			$item_class = "onto_common_item";
		}
		$item = new $item_class($this->ontology->get_class($class_uri),$uri);
		$item->set_assertions($this->get_assertions($uri));
		if(!$uri){
			//pas d'uri, on instancie les assertions par d�faut...
			$assertions = array();
			foreach($this->ontology->get_class_properties($class_uri) as $uri_property){
				$property=$this->ontology->get_property($class_uri,$uri_property);
				if(count($property->default_value)){
					global ${$property->default_value['value']};
					if(isset(${$property->default_value['value']})){
						$assertions[] = new onto_assertion($item->get_uri(),$uri_property,onto_common_uri::get_uri(${$property->default_value['value']}),$property->range[0], array('type' => "uri",'display_label' => $this->get_data_label(onto_common_uri::get_uri(${$property->default_value['value']}))));
					}
				}
			}
			if(count($assertions)){
				$item->set_assertions($assertions);
			}
		}
		return $item;
	} // end of member function get_item
	
	
	/**
	 * PARTIE ONTOLOGIE
	 */
	
	
	/**
	 * retourne les uri des classes de l'ontologie
	 * 
	 * @return array
	 */
	public function get_classes(){
		return $this->ontology->get_classes_uri();
	}
	
	
	/**
	 * Retourne le nom de la classe ontologie en fonction de son uri
	 *
	 * @param string $uri_class
	 */
	public function get_class_label($uri_class){
		return $this->ontology->get_class_label($uri_class);
	}
	
	/**
	 * Renvoie le premier nom de classe de l'ontologie (choisi par d�faut)
	 * 
	 * @return string
	 */
	public function get_first_ontology_class_name(){
		$classes = $this->get_classes();
		reset($classes);
		return current($classes)->pmb_name;
	}
	
	/**
	 * Renvoie l'uri d'une classe en fonction de son nom pmb
	 * 
	 * @param string $class_name
	 */
	public function get_class_uri($class_name){
		$classes = $this->get_classes();
		$class_uri = "";
		foreach($classes as $class){
			if($class->pmb_name == $class_name){
				$class_uri = $class->uri;
				break;
			}
		}
		return $class_uri;
	}
	
	/**
	 * Renvoie le nom PMB d'une classe en fonction de son uri
	 * 
	 * @param string $class_uri
	 */
	public function get_class_pmb_name($class_uri){
		$classes = $this->get_classes();
		$class_pmb_name = "";
		foreach($classes as $class){
			if($class->uri == $class_uri){
				$class_pmb_name = $class->pmb_name;
				break;
			}
		}
		return $class_pmb_name;
	}
	
	/**
	 * Renvoi le titre de l'ontologie
	 * 
	 * @return string
	 */
	public function get_title(){
		return $this->ontology->title;
	}
	
	/**
	 * renvoie le nom de l'ontologie
	 * 
	 * @return string
	 */
	public function get_onto_name(){
		return $this->ontology->name;
	}
	
	/**
	 * Instancie et renvoie la valeur labels
	 * Contient les libell�s des mots pr�sents dans le data_store
	 * 
	 * @return array
	 */
	public function get_labels(){
		if(!isset($this->labels) || !$this->labels){		
			$this->labels = array();
			$query="select * where {
				?uri pmb:name ?name .
				?uri rdfs:label ?label .
				optional {
					?uri pmb:displayLabel ?displayLabel .
					?uri pmb:searchLabel ?searchLabel
				}
			}";
			
			$this->onto_store->query($query);
			$results = $this->onto_store->get_result();
			foreach($results as $result){
				$this->labels[$result->name]['uri'] = $result->uri;
				
				$this->labels[$result->name]['name'] = $result->name;
				
				if(isset($result->displayLabel) && $result->displayLabel){
					$this->labels[$result->name]['displayLabel'] = $result->displayLabel;
				}
				
				if(isset($result->searchLabel) && $result->searchLabel){
					$this->labels[$result->name]['searchLabel'] = $result->searchLabel;
				}
				
				if(!isset($labels[$result->name]['label']['default'])){
					$this->labels[$result->name]['label']['default'] = $result->label;
				}
				if (!empty($result->label_lang)) {
				    $this->labels[$result->name]['label'][$result->label_lang] = $result->label;
				}
			}
		}
		return $this->labels;
	}

	
	public function get_display_label($class_uri){
		$query = "select ?displayLabel where {
			<".$class_uri."> pmb:displayLabel ?displayLabel
		}";
		$this->onto_store->query($query);
		$displayLabel = $this->default_display_label;
		if($this->onto_store->num_rows()){
			$result = $this->onto_store->get_result();
			$displayLabel = $result[0]->displayLabel;
		}
		return $displayLabel;
	}
	
	/**
	 * Renvoie un libell� en fonction du nom ou de l'uri
	 * 
	 * @param string $name
	 */
	public function get_label($name){
		global $msg,$lang;
		$label= "";
		
		//@todo recherche SPARQL sur un libelle?
		if(!isset($this->labels) || !$this->labels){
			$this->get_labels();
		}
		
		foreach($this->labels as $key => $infos){
			if($name == $key || $name == $infos['uri']){
				if(isset($msg['onto_'.$this->get_onto_name().'_'.$infos['name']])){
					//le message PMB sp�cifique pour l'ontologie courante
					$label = $msg['onto_'.$this->get_onto_name().'_'.$infos['name']];
				}else if (isset($msg['onto_common_'.$infos['name']])){
					//le message PMB g�n�rique
					$label = $msg['onto_common_'.$infos['name']];
				}else if (isset($infos['label'][substr($lang,0,2)])){
					//le label de l'ontologie dans la langue de l'interface
					$label = $infos['label'][substr($lang,0,2)];
				}else{
					//le label g�n�rique de l'ontologie
					$label = $infos['label']['default'];
				}
				break;
			}
		}
	
		return $label;
	}
	
	/**
	 * Renvoie les propri�t�s en fonction d'un nom de classe pmb
	 * 
	 * @param string $pmb_name
	 * 
	 * @return array
	 */
	public function get_onto_property_from_pmb_name($pmb_name) {
		$properties_uri = $this->ontology->get_properties();
		foreach ($properties_uri as $uri => $info) {
			if ($info->pmb_name == $pmb_name) {
				return $this->ontology->get_property("", $uri);
			}
		}
	}
	
	
	/**
	 * Retourne une instance de l'ontologie charg�e � partir de onto_store
	 *
	 * @return onto_ontology
	 *
	 * @access public
	 */
	public function get_ontology() {
		if(!isset($this->ontology )){
			$this->ontology = new onto_ontology($this->onto_store);
		}
		$this->ontology->set_data_store($this->data_store);
		return $this->ontology;
	} // end of member function get_ontology
	
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans l'onto store
	 *
	 * @param string $query
	 *
	 */
	public function onto_query($query){
		$this->onto_store->query($query);
		if($this->onto_store->num_rows()){
			return true;
		}elseif ($errs = $this->onto_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans l'onto store
	 * renvoi le r�sultat
	 *
	 * @return array result
	 */
	public function onto_result(){
		if($this->onto_store->num_rows()){
			return $this->onto_store->get_result();
		}elseif ($errs = $this->onto_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	/**
	 * Fonction d'acc�s aux requetes sparql dans l'onto store
	 * renvoi le nombre de r�sultat
	 *
	 * @return integer num rows
	 */
	public function onto_num_rows(){
		if($this->onto_store->num_rows()){
			return $this->onto_store->num_rows();
		}elseif ($errs = $this->onto_store->get_errors()) {
			print "<br>Erreurs: <br>";
			print "<pre>";print_r($errs);print "</pre><br>";
		}
		return false;
	}
	
	public function get_data_store() {
		return $this->data_store;
	}
	
	/**
	 * recupere les uri des proprietes qui composent le display_label
	 * @param uri $class_uri
	 * @return NULL[]
	 */
	public function get_display_labels($class_uri){
	    $query = "select ?displayLabel where {
			<".$class_uri."> pmb:displayLabel ?displayLabel
		}";
	    $this->onto_store->query($query);
	    $displayLabels = [$this->default_display_label];
	    if($this->onto_store->num_rows()){
	        $displayLabels = [];
	        $results = $this->onto_store->get_result();
	        foreach ($results as $result) {
	            $displayLabels[] = $result->displayLabel;
	        }
	    }
	    return $displayLabels;
	}
	/**
	 * PARTIE ONTOLOGIE
	 */
	
	private function build_triples($assertions, $main_uri) {
	    global $opac_url_base;
	    
	    $nb_assertions = count($assertions);
	    $i = 0;
	    
	    $subjects_deleted = array();
	    
	    // On peut y aller
	    $query = "";
	    foreach ($assertions as $assertion) {
	        if (!in_array($assertion->get_subject(), $subjects_deleted)) {
	            $pmb_id = 0;
	            
	            //on stocke l'id de l'entit� en base SQL s'il existe
	            $query_pmb_id = '	select ?pmb_id where {
						<'.$assertion->get_subject().'> pmb:identifier ?pmb_id
					}';
	            $this->data_store->query($query_pmb_id);
	            if ($this->data_store->num_rows()) {
	                $pmb_id = $this->data_store->get_result()[0]->pmb_id;
	            }
	            
	            // On supprime tous les triplets correspondant � cette uri pour les mettre � jour par la suite
	            if ($assertion->get_subject() == $main_uri) {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> ?prop ?obj
    						}";
	                $this->data_store->query($query_delete);
	                
	                $subjects_deleted[] = $assertion->get_subject();
	            } else {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> <".$assertion->get_predicate()."> <".$assertion->get_object().">
    						}";
	                $this->data_store->query($query_delete);
	            }
	            
	            //puis on commence par r�-ins�rer l'id de l'entit� en base SQL dans le store
	            if ($pmb_id) {
	                if (!$this->data_store->num_rows()) {
	                    $query_insert = 'insert into <pmb> {
									<'.$assertion->get_subject().'> pmb:identifier "'.$pmb_id.'" .
								}';
	                    $this->data_store->query($query_insert);
	                }
	            }
	        }
	        
	        if ($assertion->offset_get_object_property("type") == "literal"){
	            $object = "'".addslashes($assertion->get_object())."'";
	            $object_properties = $assertion->get_object_properties();
	            if (!empty($object_properties['lang'])) {
	                $object.="@".$object_properties['lang'];
	            }
	        }else{
	            
	            $object = "<".addslashes($assertion->get_object()).">";
	            
	            if ($assertion->offset_get_object_property("type") == "uri"){
	                
	                if ($assertion->get_object_type()) {
	                    if (is_numeric($assertion->get_object())) {
	                        $query_bis = "	select ?uri where {
													?uri pmb:identifier '".addslashes($assertion->get_object())."' .
													?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($assertion->get_object_type()).">
												}";
	                        $this->data_store->query($query_bis);
	                        if (!$this->data_store->num_rows()) {
	                            
	                            $uri = "<".addslashes(onto_common_uri::get_new_uri($this->get_class_pmb_name($assertion->get_object_type()),$opac_url_base.$this->get_class_pmb_name($assertion->get_object_type())."#")).">";
	                            $object = $uri;
	                            
	                            $object .= " .\n";
	                            //sujet
	                            $object .= $uri;
	                            //pr�dicat
	                            $object .= ' pmb:identifier ';
	                            //objet
	                            $object .= '"'.addslashes($assertion->get_object()).'"';
	                            
	                            $object .= " .\n";
	                            //sujet
	                            $object .= $uri;
	                            //pr�dicat
	                            $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
	                            //objet
	                            $object .= '<'.addslashes($assertion->get_object_type()).'>';
	                            
	                            if ($assertion->offset_get_object_property('display_label')) {
	                                $object .= " .\n";
	                                //sujet
	                                $object .= $uri;
	                                //pr�dicat
	                                $object .= ' pmb:displayLabel ';
	                                //objet
	                                $object .= '"'.addslashes($assertion->offset_get_object_property('display_label')).'"';
	                            }
	                        } else {
	                            $object = "<".$this->data_store->get_result()[0]->uri.">";
	                        }
	                    }
	                    if ($assertion->offset_get_object_property('object_assertions')) {
	                        
	                        $object .= " .\n";
	                        //sujet
	                        $object .= '<'.addslashes($assertion->get_object()).'>';
	                        //pr�dicat
	                        $object .= ' pmb:has_assertions ';
	                        //objet
	                        $object .= '"1"';
	                        
	                        $object .= " .\n";
	                        //sujet
	                        $object .= '<'.addslashes($assertion->get_object()).'>';
	                        //pr�dicat
	                        $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
	                        //objet
	                        $object .= "<".addslashes($assertion->get_object_type())."> .\n";
	                                                
	                        $object .= $this->build_triples($assertion->offset_get_object_property('object_assertions'),$assertion->get_object());
	                    }
	                }
	            }
	        }
	        $query.= " <".addslashes($assertion->get_subject())."> <".addslashes($assertion->get_predicate())."> ".$object;
	            	        
	        $i++;
	        if ($i < $nb_assertions) {
	            $query.=" .\n";
	        }
	        
	        //$query.=" \n";
	    }
	    return $query;
	}
	
} // end of onto_handler
