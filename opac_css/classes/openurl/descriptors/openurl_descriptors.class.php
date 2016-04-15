<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_descriptors.class.php,v 1.1 2011-08-02 12:36:00 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/openurl.class.php");

class openurl_descriptor extends openurl_root {
	var $notice=array();		//	infos sur la notice
	var $infos = array();		//	descripteur
	var $entityType = "";		//	type d'entité associée
	var $search_infos =array();	//	Infos pour la recherche dans le catalogue
	//on définit une bonne fois pour toute ce tableau!
	var $crit_id = array(
		'publisher' => 1,
		'collection' => 2,
		'title' => 3,
		'first_author' => 4,
		'author' => 4,
		'author_corp' => 4,
		'typdoc' => 5,
		'keywords' => 6,
		'abstract' => 7,
		'mention_edition' => 8,
		'collection_issn' => 9,
		'pub_place' => 10,
		'year_edition' => 11,
		'book_title' => 14,
		'parent_issn' => 13,
		'book_isbn' => 13,
		'isbn' => 12,
		'issn' => 12,
		'date_parution' => 11,
		'num_bull' => 15,
		'pages' => 16,
		'tpages' => 16,
		'external_id' => 17,
		'serial_title' => 18
	);

    function openurl_descriptor($notice) {
     	 $this->notice = $notice; 
    }
    
    function setEntityType($type){
    	switch ($type){
    		case "referent" :
    			$this->entityType = "rft";
    			break;
    		case "referring_entity" :
    			$this->entityType = "rfe";
    			break;
     		case "requester" :
    			$this->entityType = "req";
    			break;
     		case "service_type" :
    			$this->entityType = "svc";
    			break;
     		case "resolver" :
    			$this->entityType = "res";
    			break;
    		case "referrer" :
    			$this->entityType = "rfr";
    			break;  
    		default :
    			$this->entityType = $type;  	
    			break;		   			   			   			
    	}
    }
    
	function serialize(){}
}

/*
 * Description via identifiant
 */
class openurl_descriptor_identifier extends openurl_descriptor{

	function openurl_descriptor_identifier($notice=array()) {
		parent::openurl_descriptor($notice);
		$this->uri = parent::$uri."/nam";
	}
}

/*
 * Description via valeurs
 */
class openurl_descriptor_byval extends openurl_descriptor{

	function openurl_descriptor_byval($notice=array()) {
		parent::openurl_descriptor($notice);
		$this->uri = parent::$uri."/fmt";
	}
}

/*
 * Description via une référence à un jeu de valeur
 */
class openurl_descriptor_byref extends openurl_descriptor{

	function openurl_descriptor_byref($notice=array()) {
		parent::openurl_descriptor($notice);
		$this->uri = parent::$uri."/fmt";
	}
}

/*
 * Description via données privées
 */
class openurl_descriptor_private extends openurl_descriptor{

	function openurl_descriptor_private($notice=array()) {
		parent::openurl_descriptor($notice);
	}
}
