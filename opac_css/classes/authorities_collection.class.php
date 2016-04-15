<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_collection.class.php,v 1.1.2.2 2015-09-28 15:23:44 apetithomme Exp $

/**
 * Classe de collection d'autorités pour éviter d'instancier plusieurs fois les mêmes autorités dans une même page
 * @author apetithomme
 *
 */
class authorities_collection {
	static private $authorities = array();
	
	static public function get_authority($authority_type, $authority_id) {
		if (isset(self::$authorities[$authority_type][$authority_id])) {
			return self::$authorities[$authority_type][$authority_id];
		}
		
		if (!isset(self::$authorities[$authority_type])) {
			self::$authorities[$authority_type] = array();
		}
		
		switch($authority_type){
			case "author" :
				self::load_class("author");
				self::$authorities[$authority_type][$authority_id] = new auteur($authority_id);
				break;
			case "publisher" :
				self::load_class("publisher");
				self::$authorities[$authority_type][$authority_id] = new publisher($authority_id);
				break;
			case "collection" :
				self::load_class("collection");
				self::$authorities[$authority_type][$authority_id] = new collection($authority_id);
				break;
			case "subcollection" :
				self::load_class("subcollection");
				self::$authorities[$authority_type][$authority_id] = new subcollection($authority_id);
				break;
			case "serie" :
				self::load_class("serie");
				self::$authorities[$authority_type][$authority_id] = new serie($authority_id);
				break;
			case "indexint" :
				self::load_class("indexint");
				self::$authorities[$authority_type][$authority_id] = new indexint($authority_id);
				break;
			case "titre_uniforme" :
				self::load_class("titre_uniforme");
				self::$authorities[$authority_type][$authority_id] = new titre_uniforme($authority_id);
				break;
			case "category" :
				global $lang;
				self::load_class("categorie");
				self::$authorities[$authority_type][$authority_id] = new categorie($authority_id,$lang);
				break;
			case "authperso" :
				self::load_class("authperso_authority");
				self::$authorities[$authority_type][$authority_id] = new authperso_authority($authority_id);
				break;
			default :
				return null;
		}
		return self::$authorities[$authority_type][$authority_id];
	}
	
	static private function load_class($classname) {
		global $base_path,$include_path,$class_path,$javascript_path,$style_path;
		require_once($class_path."/".$classname.".class.php");
	} 
}