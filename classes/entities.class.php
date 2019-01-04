<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities.class.php,v 1.5 2018-11-27 16:26:47 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class entities{
    public static $entities;
    
    public static function get_entities() {
    	return array(
    			TYPE_NOTICE,
    			TYPE_AUTHOR,
    			TYPE_CATEGORY,
    			TYPE_PUBLISHER,
    			TYPE_COLLECTION,
    			TYPE_SUBCOLLECTION,
    			TYPE_SERIE,
    			TYPE_TITRE_UNIFORME,
    			TYPE_INDEXINT,
    			TYPE_EXPL,
    			TYPE_EXPLNUM,
    			TYPE_AUTHPERSO,
    			TYPE_CMS_SECTION,
    			TYPE_CMS_ARTICLE,
    			TYPE_CONCEPT
    	);
    }
    
	public static function get_entities_labels() {
	    global $msg;
	    
	    $entities = array(    
	    		TYPE_NOTICE => $msg['288'],
	    		TYPE_AUTHOR => $msg['isbd_author'],
	    		TYPE_CATEGORY => $msg['isbd_categories'],
	    		TYPE_PUBLISHER => $msg['isbd_editeur'],
	    		TYPE_COLLECTION => $msg['isbd_collection'],
	    		TYPE_SUBCOLLECTION => $msg['isbd_subcollection'],
	    		TYPE_SERIE => $msg['isbd_serie'],
	    		TYPE_TITRE_UNIFORME => $msg['isbd_titre_uniforme'],
	    		TYPE_INDEXINT => $msg['isbd_indexint'],
	    		TYPE_EXPL => $msg['376'],
	    		TYPE_EXPLNUM => $msg['search_explnum'],
	    		TYPE_AUTHPERSO => $msg['search_by_authperso_title'],
	    		TYPE_CMS_SECTION => $msg['cms_menu_editorial_section'],
	    		TYPE_CMS_ARTICLE => $msg['cms_menu_editorial_article'],
	    		TYPE_CONCEPT => $msg['search_concept_title']
	    );
	    return $entities;
	}
	
	public static function get_entities_options($selected) {
	    global $charset;
	    $entities = static::get_entities_list();
	    $html = '';
	    foreach ($entities as $value => $label) {
	        $html .= '<option value="'.$value.'" '.($value == $selected ? 'selected="selected"' : '').'>'.htmlentities($label, ENT_QUOTES, $charset).'</option>';
	    }
	    return $html;
	}
	
	public static function get_string_from_const_type($type){
	    if(!is_numeric($type)){
	        return $type;
	    }
		switch ($type) {
			case TYPE_NOTICE :
				return 'notices';
			case TYPE_AUTHOR :
				return 'authors';
			case TYPE_CATEGORY :
				return 'categories';
			case TYPE_PUBLISHER :
				return 'publishers';
			case TYPE_COLLECTION :
				return 'collections';
			case TYPE_SUBCOLLECTION :
				return 'subcollections';
			case TYPE_SERIE :
				return 'series';
			case TYPE_TITRE_UNIFORME :
				return 'titres_uniformes';
			case TYPE_INDEXINT :
				return 'indexint';
			case TYPE_CONCEPT_PREFLABEL:
			case TYPE_CONCEPT:
				return 'concepts';
			case TYPE_AUTHPERSO :
				return 'authperso';
		}
	}
}