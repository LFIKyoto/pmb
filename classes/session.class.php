<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.class.php,v 1.2 2019-07-05 13:25:14 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class session {
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct() {

	}
	
	public static function get_last_used($type) {
		return $_SESSION["last_".$type."_used"];
	}
	
	public static function set_last_used($type, $value) {
		$_SESSION["last_".$type."_used"] = $value;
	}
	
// 	static function get_value($name) {
// 		return $_SESSION[$name];
// 	}
	
// 	static function set_value($name, $value) {
// 		$_SESSION[$name] = $value;
// 	}
	
} // class session


