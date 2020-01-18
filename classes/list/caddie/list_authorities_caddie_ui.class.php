<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_caddie_ui.class.php,v 1.4.6.4 2019-12-03 10:32:33 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/list/caddie/list_caddie_root_ui.class.php");

class list_authorities_caddie_ui extends list_caddie_root_ui {
	
	protected function _get_query_caddie_content() {
		$query = "SELECT authorities_caddie_content.object_id FROM authorities_caddie_content";
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function fetch_data() {
	    if (static::$object_type == 'CONCEPTS') {
    	    $this->objects = array();
	        $this->_get_query_order();
	        $query = $this->_get_query_caddie_content();
    	    $result = pmb_mysql_query($query);
    	    if (pmb_mysql_num_rows($result)) {
    	        while($row = pmb_mysql_fetch_object($result)) {
	                $aut = new authority($row->object_id);
    	            $this->add_object($aut->get_object_instance());
    	        }
    	        if($this->applied_sort_type != "SQL"){
    	            $this->pager['nb_results'] = pmb_mysql_num_rows($result);
    	        }
    	    }
    	    $this->messages = "";
	    } else {
	        parent::fetch_data();
	    }
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'AUTHORS':
				$query = "SELECT authorities.id_authority AS id, authors.* 
				          FROM authors 
                          JOIN authorities ON authorities.num_object = authors.author_id 
                          AND authorities.type_object = " . AUT_TABLE_AUTHORS;
				break;
			case 'CATEGORIES':
				$query = "SELECT authorities.id_authority AS id, categories.* 
                          FROM categories 
                          JOIN authorities ON authorities.num_object = categories.num_noeud 
                          AND authorities.type_object = " . AUT_TABLE_CATEG;
				break;
			case 'PUBLISHERS':
				$query = "SELECT authorities.id_authority AS id, publishers.* 
                          FROM publishers 
                          JOIN authorities ON authorities.num_object = publishers.ed_id 
                          AND authorities.type_object = " . AUT_TABLE_PUBLISHERS;
				break;
			case 'COLLECTIONS':
				$query = "SELECT authorities.id_authority AS id, collections.* 
                          FROM collections 
                          JOIN authorities ON authorities.num_object = collections.collection_id 
                          AND authorities.type_object = " . AUT_TABLE_COLLECTIONS;
				break;
			case 'SUBCOLLECTIONS':
				$query = "SELECT authorities.id_authority AS id, sub_collections.* 
                          FROM sub_collections 
                          JOIN authorities ON authorities.num_object = sub_collections.sub_coll_id 
                          AND authorities.type_object = " . AUT_TABLE_SUB_COLLECTIONS;
				break;
			case 'SERIES':
				$query = "SELECT authorities.id_authority AS id, series.* 
                          FROM series 
                          JOIN authorities ON authorities.num_object = series.serie_id 
                          AND authorities.type_object = " . AUT_TABLE_SERIES;
				break;
			case 'TITRES_UNIFORMES':
				$query = "SELECT authorities.id_authority AS id, titres_uniformes.* 
				          FROM titres_uniformes 
			              JOIN authorities ON authorities.num_object = titres_uniformes.tu_id 
				          AND authorities.type_object = " . AUT_TABLE_TITRES_UNIFORMES;
				break;
			case 'AUTHPERSO':
			    $query = "SELECT authorities.id_authority AS id, authperso.authperso_name, authperso_authorities.* 
			              FROM authperso_authorities 
		                  JOIN authperso ON authperso.id_authperso = authperso_authorities.authperso_authority_authperso_num 
			              JOIN authorities ON authorities.num_object = authperso_authorities.id_authperso_authority 
			              AND authorities.type_object = " . AUT_TABLE_AUTHPERSO;
			    break;
			case 'MIXED':
			    $query = "SELECT authorities.id_authority AS id, authorities_statuts.authorities_statut_label, authorities.* 
			              FROM authorities
			              JOIN authorities_statuts ON authorities_statuts.id_authorities_statut = authorities.num_statut";
			    break;
			default:
			    $query = "";
			    break;
		}
		if ($query) {
			$query .= " where authorities.id_authority IN (".$this->_get_query_caddie_content().")";
		}
		return $query;
	}
	
	protected function get_main_fields() {
		switch (static::$object_type) {
			case 'AUTHPERSO':
			    $type_object = 'authperso_authorities';
			    break;
			case 'MIXED':
			    return array();
			case 'CONCEPTS':
			    $props = skos_concept::get_properties();
			    return $props;		
			default:
			    $type_object = static::$object_type;
				break;
		}
		return array_merge(
		    $this->get_describe_fields(strtolower($type_object), strtolower($type_object), strtolower($type_object))
	    );
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		switch (static::$object_type) {
			case 'AUTHORS':
				$this->add_custom_fields_available_columns('author', 'author_id');
				break;
			case 'CATEGORIES':
				$this->add_custom_fields_available_columns('categ', 'num_noeud');
				break;
			case 'PUBLISHERS':
			    $this->add_custom_fields_available_columns('publisher', 'ed_id');
			    break;
			case 'COLLECTIONS':
			    $this->add_custom_fields_available_columns('collection', 'collection_id');
			    break;
			case 'SUBCOLLECTIONS':
			    $this->add_custom_fields_available_columns('subcollection', 'sub_coll_id');
			    break;
			case 'SERIES':
			    $this->add_custom_fields_available_columns('serie', 'serie_id');
			    break;
			case 'TITRES_UNIFORMES':
			    $this->add_custom_fields_available_columns('tu', 'tu_id');
			    break;
			case 'AUTHPERSO':
			    $this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authperso_available_columns());
			    $this->add_custom_fields_available_columns('authperso', 'id_authperso_authority');
			    break;
			case 'MIXED':
			    $this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_mixed_available_columns());
			    break;
			case 'CONCEPTS':
			    $this->add_custom_fields_available_columns('skos', 'id');
			    break;
			default:
			    break;
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		switch (static::$object_type) {
			case 'AUTHORS':
				$sort_by = 'author_name';
				break;
			case 'CATEGORIES':
				$sort_by = 'libelle_categorie';
				break;
			case 'PUBLISHERS':
				$sort_by = 'ed_name';
				break;
			case 'COLLECTIONS':
				$sort_by = 'collection_name';
				break;
			case 'SUBCOLLECTIONS':
				$sort_by = 'sub_coll_name';
				break;
			case 'SERIES':
				$sort_by = 'serie_name';
				break;
			case 'TITRES_UNIFORMES':
				$sort_by = 'tu_name';
				break;
			case 'AUTHPERSO':
			    $sort_by = 'id_authperso_authority';
			    break;
			case 'MIXED':
			    $sort_by = 'id_authority';
			    break;
			case 'CONCEPTS':
			    $sort_by='id';
			    break;
			default:
			    $sort_by = '';
			    break;
		}
		$this->add_applied_sort($sort_by);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/autorites.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type='.static::$object_type.'&idcaddie='.static::$id_caddie.'&item=0';
	}
	
	protected function _get_query_order() {
	    if (static::$object_type == 'CONCEPTS'){
	        $this->applied_sort_type = 'OBJECTS';
	        return '';
	    }
	    if ($this->applied_sort[0]['by']) {
	        $sort_by = $this->applied_sort[0]['by'];
	        switch($sort_by) {
	            case 'isbd_authority':
	                $this->applied_sort_type = 'OBJECTS';
	                return '';
	            default :
	                return parent::_get_query_order();
	        }
	    }
	}
	
	protected function add_authperso_available_columns() {
	    return array(
	        'authperso_name' => 'search_by_authperso_title'
	    );
	}
	
	protected function add_mixed_available_columns() {
	    return array(
	        'id_authority' => 'cms_authority_format_data_id',
	        'num_object' => 'cms_authority_format_data_db_id',
	        'type_object' => 'include_option_type_donnees',
	        'isbd_authority' => 'cms_authority_format_data_isbd',
	        'authorities_statut_label' => 'search_extended_common_statut',
	        'thumbnail_url' => 'explnum_vignette',
	    );
	}
	
	protected function get_cell_content($object, $property) {
	    if (static::$object_type == 'CONCEPTS') {
	        $content = $object->{$property};
	        if($content !== null){
	            if(is_array($content)){
	                $content = implode("<br>",$content);
	            }
	            return $content;
	        }
	    }
	    switch($property) {
	        case 'type_object':
	            return authority::get_type_label_from_type_id($object->{$property});
	        case 'isbd_authority':
	            $authority = new authority($object->id_authority);
	            return $authority->get_isbd();
	        default:
	            return parent::get_cell_content($object, $property);
	    }
	}
}