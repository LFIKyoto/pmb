<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturls.class.php,v 1.1 2015-04-16 12:23:40 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once("$class_path/shorturl/shorturl_type.class.php");

class shorturls {
	
	static function get_obj($hash){
		 $st=new shorturl_type($hash); 
		 return $st->proceed();
	}
	
	static function generate_obj($type, $action, $context){		
		return shorturl_type::generate_obj($type, $action, $context); 		 
	}	
	
	static function purge(){
	
	}
} // end of class

