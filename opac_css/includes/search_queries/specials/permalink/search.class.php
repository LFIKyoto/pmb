<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.4 2018-10-23 13:59:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/rec_history.inc.php");

//Classe de gestion de la recherche sp�cial "combine"

class permalink_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
	public function __construct($id,$n_ligne,$params,&$search) {
		$this->id=$id;
		$this->n_ligne=$n_ligne;
		$this->params=$params;
		$this->search=&$search;
	}

	/**
	 * Fonction de r�cup�ration des op�rateurs disponibles pour ce champ sp�cial (renvoie un tableau d'op�rateurs)
	 * @return array Op�rateurs disponibles
	 */
	public function get_op() {
		$operators = array();
		if ($_SESSION["nb_queries"]!=0) {
			$operators["EQ"]="=";
		}
		return $operators;
	}

	/**
	 * Fonction de r�cup�ration de l'affichage de la saisie du crit�re
	 * @return string Chaine html
	 */
	public function get_input_box() {
		global $charset;
		
		$form = '';
		$field_name="field_".$this->n_ligne."_s_".$this->id;
    	global ${$field_name};
    	$valeur = ${$field_name};
    	if(is_array($valeur) && $valeur[0]) {
    		$permalink_data = unserialize($valeur[0]);
    		$form .= $permalink_data['human_query'];
    		$form .= "<input type='hidden' name='".$field_name."[]' value=\"".htmlentities($valeur[0],ENT_QUOTES,$charset)."\"/>";
    	}
    	return $form;
	}

	/**
	 * Fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
	 */
	public function transform_input() {
	}

	/**
	 * Fonction de cr�ation de la requ�te (retourne une table temporaire)
	 * @return string Nom de la table temporaire
	 */
	public function make_search() {
		$valeur_="field_".$this->n_ligne."_s_".$this->id;
		global ${$valeur_};
		$valeur=${$valeur_};
		$this->search->push();
		$context = unserialize($valeur[0]);
		$search=new search($context["search_type"]);
		$search->unserialize_search(serialize($context["serialized_search"]));
		$table = $search->make_search();
		$this->search->pull();
		return $table;
	}
	
	/**
	 * Fonction de cr�ation de la recherche s�rialis�e (retourne un tableau s�rialis�)
	 * @return string Nom du tableau s�rialis�
	 */
	public function serialize_search() {
			
		//R�cup�ration de la valeur de saisie
		$valeur_="field_".$this->n_ligne."_s_".$this->id;
		global ${$valeur_};
		$valeur=${$valeur_};
			
		if (!$this->is_empty($valeur)) {
			//enregistrement de l'environnement courant
			$this->search->push();
				
			$mc = self::simple2mc($valeur[0]);
				
			$es = $mc['search_instance'];
				
			$retour=$es->serialize_search();
				
			//restauration de l'environnement courant
			$this->search->pull();
		}
		return $retour;
	}

	/**
	 * Fonction de traduction litt�rale de la requ�te effectu�e (renvoie un tableau des termes saisis)
	 * @return array
	 */
	public function make_human_query() {
		global $msg,$charset;
		global $include_path;

		//R�cup�ration de la valeur de saisie
		$valeur_="field_".$this->n_ligne."_s_".$this->id;
		global ${$valeur_};
		$valeur=${$valeur_};
		
		$context = unserialize($valeur[0]);
		$human = $context['human_query'];
 		if(in_array('s_3',$context['serialized_search']['SEARCH'])){
 			for( $i=0 ; $i<count($context['serialized_search']['SEARCH']) ; $i++) {
 				if($context['serialized_search']['SEARCH'][$i] == 's_3'){
 					switch ($context['serialized_search'][$i]['INTER']) {
						case "and":
							$human.= ' '.$msg["search_and"];
							break;
						case "or":
							$human.= ' '.$msg["search_or"];
							break;
						default:
							
							$human.= ' '.$msg["search_or"];
							break;
					}
					$human.= ' '.$msg['search_facette'].' ';
					
    				$valeur = $context['serialized_search'][$i]['FIELD'];
			    	$item_literal_words = array();
			    	foreach ($valeur as $v) {
				    	$filter_value = $v[1];
				    	$filter_name = $v[0];
				    	
				    	$libValue = "";
				    	foreach ($filter_value as $value) {
				    		//if ($libValue!= '') $libValue .= ' '.$msg["search_or"].' ';
				    		$libValue .= (substr($value, 0, 4) == "msg:" ? $msg[substr($value, 4)] : $value);
				    	}
						$item_literal_words[] = stripslashes($filter_name)." : '".stripslashes($libValue)."'";
			    	}
			    	
			    	$human.= implode(' ',$item_literal_words);
 				}
 			}
 		}
		return array($human);
	}

	/**
	 * Fonction de d�coupage d'une chaine trop longue
	 * @param string $valeur Chaine � d�couper
	 * @return string Chaine d�coup�e
	 */
	public function cutlongwords($valeur) {
		if (strlen($valeur)>=50) {
			$pos=strrpos(substr($valeur,0,50)," ");
			if ($pos) {
				$valeur=substr($valeur,0,$pos+1)."...";
			}
		}
		return $valeur;
	}

	/**
	 * Fonction de v�rification du champ saisi ou s�lectionn�
	 * @param array $valeur Champ saisi ou s�lectionn�
	 * @return boolean true si vide
	 */
	public function is_empty($valeur) {
		return false;

	}
}