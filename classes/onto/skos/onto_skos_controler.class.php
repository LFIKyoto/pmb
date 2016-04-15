<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_controler.class.php,v 1.27 2015-06-18 12:36:22 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_composee.class.php");

class onto_skos_controler extends onto_common_controler {
	
	/** L'uri des schema **/
	protected static $concept_scheme_uri='http://www.w3.org/2004/02/skos/core#ConceptScheme';
	protected static $concept_uri='http://www.w3.org/2004/02/skos/core#Concept';
	

	/**
	 * Gère la variable session breadcrumb qui garde les id présents dans la navigation
	 * permet la construction du fil de navigation dans le thésaurus
	 * renvoie un tableau des id de parents parcouru
	 *
	 * @param onto_handler $handler
	 * @param onto_param $params
	 * @param bool $reset
	 *
	 * @return array breadcrumb
	 */
	public function handle_breadcrumb($reset=false){
		if(!$_SESSION['breadcrumb'] || $reset){
			$_SESSION['breadcrumb']='';
		}
	
		//on enregistre le fil d'ariane
		if($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-/',$_SESSION['breadcrumb'])){
			$_SESSION['breadcrumb'].='-'.$this->params->parent_id.'-';
		}elseif($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-$/',$_SESSION['breadcrumb'])){
			$_SESSION['breadcrumb']=substr($_SESSION['breadcrumb'],0, strpos($_SESSION['breadcrumb'], '-'.$this->params->parent_id.'-')+strlen('-'.$this->params->parent_id.'-'));
		}elseif(!$this->params->parent_id){
			$_SESSION['breadcrumb']='';
		}
	
		$breadcrumb=explode('--',$_SESSION['breadcrumb']);
		foreach($breadcrumb as $key=>$parent_id){
			$breadcrumb[$key]=str_replace('-', '', $parent_id);
		}
	
		return $breadcrumb;
	}
	
	/**
	 * renvoie la liste des schema
	 * 
	 * @return array
	 */
	public function get_scheme_list(){
		$params=new onto_param();
		$params->page = 1;
		$params->nb_per_page = 0;
		$params->action = "list";
		return $this->get_list(self::$concept_scheme_uri,$params);	
	}
	
	public function get_list($class_uri,$params){
		global $lang;
		switch($class_uri){
			case self::$concept_uri :
				return $this->get_hierarchized_list($class_uri,$params);
				break;
			default :
				return parent::get_list($class_uri,$params);
				break;
		}
	}
	
	/**
	 * renvoie le nombre d'enfants d'un noeud.
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * 
	 * @return int
	 */
	public function has_narrower($class_uri,$params){
		$in_scheme = "";
		
		if (($params->concept_scheme != -1) && ($params->concept_scheme != 0)) {
			// On est dans un schéma en particulier
			$in_scheme = " .
			?child <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">";
		}
		$query .= "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?child".$in_scheme."
		}";
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}
	
	/**
	 * renvoie le nombre de parents d'un noeud. 
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 * 
	 * * @return int
	 */
	public function has_broader($class_uri,$params){
		$query .= "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">
		}";
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}
	
	/**
	 * renvoie les parents d'un noeud
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_broaders($class_uri,$params){
		$query .= "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">
		}";
		$this->handler->data_query($query);
		$results=$this->handler->data_result();
		
		if(sizeof($results)){
			$return=array();
			foreach ($results as $key=>$result){
				$return[$key]["id"]=onto_common_uri::get_id($result->parent);
				$return[$key]["label"] = $this->get_data_label($result->parent);
			}
			return $return;
		}
		return array();
 	}
	
	/**
	 * Retourne une liste hierarchisée
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_hierarchized_list($class_uri,$params){
		global $lang;
	
		$page=$params->page-1;
		$displayLabel=$this->handler->get_display_label(self::$concept_uri);
		
		$query = $filter = "";
		$query .= "select ?elem ?label where {
			?elem rdf:type <".self::$concept_uri."> .";
		$counted = false;
		if(!$params->parent_id){
			//retourne les top concepts
			if($params->only_top_concepts){
				$more = "
					?elem <".$displayLabel."> ?label";
				if($params->concept_scheme == 0) {
					$more.= " . ?elem pmb:showInTop owl:Nothing";
					$count_query = "select count(?elem) as ?nb where{ ?elem pmb:showInTop owl:Nothing }";
					$this->handler->data_query($count_query);
					if($this->handler->data_num_rows()){
						$counted = true;
						$result = $this->handler->data_result();
						$nb_elements = $result[0]->nb;
					}
				}else if ($params->concept_scheme != -1) {
					$more.= " .	?elem skos:topConceptOf <".onto_common_uri::get_uri($params->concept_scheme).">";
					$count_query = "select count(?elem) as ?nb where{ ?elem skos:topConceptOf <".onto_common_uri::get_uri($params->concept_scheme)."> }";
					$this->handler->data_query($count_query);
					if($this->handler->data_num_rows()){
						$counted = true;
						$result = $this->handler->data_result();
						$nb_elements = $result[0]->nb;
					}
				}else {
					$more.= " .	?elem skos:topConceptOf ?top";
				}
			}else{
				$more = "
					?elem <".$displayLabel."> ?label";
				
				if ($params->concept_scheme == 0) {
					/*
					 * 
					 * TODO : HACK à reprendre un jour
					 * 
					 */
					$more.= " . ?elem pmb:showInTop owl:Nothing";
				} else {
					// Sinon on affiche les top concepts de tous les schémas y compris sans schema
					/*
					 * 
					 * TODO : HACK à reprendre un jour
					 * 
					 */
					$more.= " . optional { ?elem pmb:showInTop ?scheme";
					if ($params->concept_scheme != -1) {
						// On n'affiche qu'un schéma
						$filter .= " (?scheme = <".onto_common_uri::get_uri($params->concept_scheme).">)";
					}
					$more.= " }";
					if($filter){
						$more.= " filter (".$filter.")
						";
					}
				}
			}
			$query.=$more;
			$nb_elements=$this->handler->get_nb_elements(self::$concept_uri,$more);
		}else{
				//retourne les enfants du parent
				$more = "
					?elem <".$displayLabel."> ?label .
					?elem <http://www.w3.org/2004/02/skos/core#broader> <".onto_common_uri::get_uri($params->parent_id).">";
	
				if ($params->concept_scheme == 0) {
					// On affiche les concepts qui n'ont pas de schéma
					$more.= " .
						optional {
							?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
						}
						filter (!bound(?scheme))
						";
				} else if ($params->concept_scheme != -1) {
					// On n'affiche qu'un schéma
					$more.= " .
						?elem <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">  
						";
				
				}
				$query.=$more;
				$nb_elements=$this->handler->get_nb_elements(self::$concept_uri,$more);
		}
		
		$query.= " } group by ?elem order by ?label limit ".$params->nb_per_page;
		$query.= " offset ".($page*$params->nb_per_page);
		$this->handler->data_query($query);
		$results=$this->handler->data_result();
		$list = array(
				'nb_total_elements' => 	$nb_elements,
				'nb_onto_element_per_page' => $params->nb_per_page,
				'page' => $page
		);
		$list['elements']=array();
		if($this->handler->data_num_rows()){
			foreach($results as $result){
				if(!$list['elements'][$result->elem]['default']){
					$list['elements'][$result->elem]['default'] = $result->label;
				}
				if(substr($lang,0,2) == $result->label_lang){
					$list['elements'][$result->elem][$result->label_lang] = $result->label;
				}
			}
		}
		return $list;
	}
	
	/**
	 * Dérivation de l'aiguilleur principal pour les ajouts d'éléments dans les sélecteurs
	 */
	public function proceed(){
		
		$this->init_item();
		switch($this->params->action){
			case "selector_add" :
				$this->proceed_selector_add();
				break;
			case "selector_save" :
				$this->proceed_selector_save();
				break;
			case "search" :
				// On met à jour le dernier schéma sélectionné
				if (isset($this->params->concept_scheme) && ($this->params->concept_scheme !== "")) {
					$_SESSION['onto_skos_concept_last_concept_scheme'] = $this->params->concept_scheme;
				}
			default : 
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				return parent::proceed();
				break;
		}
	}
	
	protected function init_item(){
		if($this->params->action == "selector_add"){
			//dans le sélecteur, c'est forcément un nouveau...
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params),"");
		}else if($this->params->action == "selector_save"){
			//lors d'une sauvegarde d'un item, on a posté l'uri
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params), $this->params->item_uri);
		}else{
			//on réinvente pas la roue
			parent::init_item();
		}
	}

	protected function proceed_edit(){
		print $this->item->get_form("./autorites.php?categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&parent_id=".$this->params->parent_id."&concept_scheme=".$this->params->concept_scheme);
	}
	
	protected function proceed_selector_save(){
		$this->item->get_values_from_form();
		$saved = $this->handler->save($this->item);
		$query = "select ?scheme ?broader ?broaderScheme where{
			<".$this->item->get_uri()."> rdf:type skos:Concept .
			<".$this->item->get_uri()."> skos:inScheme ?scheme .
			optional {
				<".$this->item->get_uri()."> skos:broader ?broader .
				?broader skos:inScheme ?broaderScheme
			}
		} order by ?scheme ?broader";
		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			$results = $this->handler->data_result();
			$lastScheme=$results[0]->scheme;
			$flag = true;
			foreach($results as $result){
				if($result->scheme == $result->broaderScheme){
					$flag = false;
				}
				if($lastScheme != $result->scheme){
					if($flag){
						$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
						$this->handler->data_query($query);
					}
					$flag = true;
					$lastScheme = $result->scheme;
				}
			}
			if($flag){
				$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
				$this->handler->data_query($query);
			}
		}else{
			$query = "select * where{
			<".$this->item->get_uri()."> rdf:type skos:Concept .
			optional{
			 <".$this->item->get_uri()."> skos:inScheme ?scheme .
			} . filter(!bound(?scheme)) .
			 optional {
				<".$this->item->get_uri()."> skos:broader ?broader .
				?broader skos:inScheme ?broaderScheme
			} filter (!bound(?broaderScheme))
		} ";
			$this->handler->data_query($query);
			if(!$this->handler->data_num_rows()){
				$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
				$this->handler->data_query($query);
			}
		}
		
		$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		//ils sont nouveaux dont pas encore utilisé...pas besoin du hook pour les notices...
		if($saved !== true){
			$ui_class_name::display_errors($this,$saved);
		}else{
// 			$this->proceed_list();
			$this->params->action = "list_selector";
			$this->params->deb_rech = "\"".$this->item->get_label("http://www.w3.org/2004/02/skos/core#prefLabel")."\"";
//  		$this->params->parent_id = $_SESSION['onto_skos_concept_selector_last_parent_id'];
			return parent::proceed();
		}
	}
	
	protected function proceed_selector_add(){
		//on en aura besoin à la sauvegarde...
		$_SESSION['onto_skos_concept_selector_last_parent_id'] = $this->params->parent_id;
		//réglons rapidement ce problème... cf. dette technique
 		print "<div id='att'></div>";
 		$type = $this->get_item_type_to_list($this->params,true);
		print $this->item->get_form($this->params->base_url."&range=".$this->params->range, $type."_selector_form", "selector_save");
	}
	
	/*
	 * On hook la sauvegarde pour déclencher la réindexation des éléments impactés
	 */
	protected function proceed_save($list=true){
		global $dbh;
		$this->item->get_values_from_form();
		$result = $this->handler->save($this->item);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else{
			//TODO: reprendre ce hack un peu crade
			//pour faciliter les requetes SPARQL en gestion, on ajoute une propriété qui sort de nulle part... pmb:showInTop si pas de parent dans le schéma
			
			$query = "select ?scheme ?broader ?broaderScheme where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .	
				<".$this->item->get_uri()."> skos:inScheme ?scheme .
				optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				}		
			} order by ?scheme ?broader";
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$lastScheme=$results[0]->scheme;
				$flag = true;
				foreach($results as $result){
					if($result->scheme == $result->broaderScheme){
						$flag = false;
					}
					if($lastScheme != $result->scheme){
						if($flag){
							$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
							 $this->handler->data_query($query);
						}
						$flag = true;
						$lastScheme = $result->scheme;
					}
				}
				if($flag){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
					$this->handler->data_query($query);
				}
			}else{
				$query = "select * where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .	
				optional{		
				 <".$this->item->get_uri()."> skos:inScheme ?scheme .
				} . filter(!bound(?scheme)) .
				 optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				} filter (!bound(?broaderScheme))
			} ";
				$this->handler->data_query($query);
				if(!$this->handler->data_num_rows()){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
					$this->handler->data_query($query);
				}
			}
	
			//sauvegarde des autorités liées pour les concepts...
			if( get_class($this->item) == "onto_skos_concept_item"){
				$aut_link= new aut_link(AUT_TABLE_CONCEPT,onto_common_uri::get_id($this->item->get_uri()));
				$aut_link->save_form();
			}
					
			// Mise à jour des vedettes composées contenant cette autorité
			vedette_composee::update_vedettes_built_with_element(onto_common_uri::get_id($this->item->get_uri()), "concept");
		
			//réindexation des notices indexés avec le concepts
			$query = "select num_object from index_concept where type_object =1 and num_concept = ".onto_common_uri::get_id($this->item->get_uri());
			$result = pmb_mysql_query($query,$dbh);
			if($result && pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					notice::majNoticesMotsGlobalIndex($row->num_object,"concept");
				}
			}
		}
		if($list){
			$this->proceed_list();
		}
	}
	
	/*
	 * On hook la suppression pour vérifier l'utilisation au préalable
	 */
	protected function proceed_delete($force_delete = false){
		global $dbh,$msg;
		
		// On déclare un flag pour savoir si on peut continuer la suppression
		$deletion_allowed = true;

		$message  = $this->item->get_label($this->handler->get_display_label($this->handler->get_class_uri($this->params->categ)));
		
		// on  déjà vérifier l'utilisation dans les notices d'un concept
		$query = "select num_object from index_concept where type_object =1 and num_concept = ".onto_common_uri::get_id($this->item->get_uri());
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$deletion_allowed = false;
			$message.= "<br/>".$msg['concept_use_in_notices_cant_delete'];
		}
		
		// On regarde si l'autorité est utilisée dans des vedettes composées
		$attached_vedettes = vedette_composee::get_vedettes_built_with_element(onto_common_uri::get_id($this->item->get_uri()), "concept");
		if (count($attached_vedettes)) {
			// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
			$deletion_allowed = false;
			$message.= "<br/>".$msg['vedette_dont_del_autority'];
		}
		
		if ($deletion_allowed) {
			// On peut continuer la suppression
			$id_vedette = vedette_link::get_vedette_id_from_object(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT_PREFLABEL);
			$vedette = new vedette_composee($id_vedette);
			$vedette->delete();
			
			//suppression des autorités liées...
			// liens entre autorités
			if( get_class($this->item) == "onto_skos_concept_item"){
				$aut_link= new aut_link(AUT_TABLE_CONCEPT,onto_common_uri::get_id($this->item->get_uri()));
				$aut_link->delete();
			}
			parent::proceed_delete($force_delete);
		} else {
			error_message($msg[132], $message, 1, "./autorites.php?categ=concepts&sub=concept&action=edit&id=".onto_common_uri::get_id($this->item->get_uri()));
		}
	}
	
	/**
	 * Place un concept en tête de hiérarchie si il est dans un schéma et qu'il n'a pas de broader
	 */
	protected function define_top_concept_of() {
		$query = "select ?scheme where {
				<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> ?topscheme .
					filter (?topscheme = ?scheme)
				}
				filter (!bound(?topscheme))
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
					?broader <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
				}
				filter (!bound(?broader))
			}";
		// Détails : on va chercher les schémas de l'item; pour chaque schema, on regarde si il est topconcept ou si il a un parent
		
		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			// Le concept est dans des schémas dans lesquels il n'est pas topconcept et il n'a pas de parent
			// On le définit donc top concept de ces schémas 
			$query = "insert into <pmb> {";
			
			$results = $this->handler->data_result();
			foreach($results as $result){
				$query .= "
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> <".$result->scheme."> .
					<".$result->scheme."> <http://www.w3.org/2004/02/skos/core#hasTopConcept> <".$this->item->get_uri()."> .";
			}
			$query .= "}";
		}
		$this->handler->data_query($query);
	}
	
	/**
	 * renvoie les informations d'un noeud
	 *
	 * @param string $uri
	 * @return array
	 */
	public function get_informations_concept($uri){
		$query = "select ?scopeNote where {
					<".$uri."> rdf:type <".self::$concept_uri."> .
					optional {
						<".$uri."> skos:scopeNote ?scopeNote
					}
				}";
	
		$this->handler->data_query($query);
		$results=$this->handler->data_result();
		if(is_array($results) && sizeof($results)){
			$return=array();
			foreach ($results as $key=>$result){
				$return[$key]["scopeNote"]=$result->scopeNote;
			}
			return $return;
		}
		return array();
	}
}