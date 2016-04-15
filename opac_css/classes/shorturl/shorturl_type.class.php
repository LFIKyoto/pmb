<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type.class.php,v 1.2 2015-04-18 13:01:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/shorturl/shorturl_type_search.class.php");

class shorturl_type {
	protected $id;	
	protected $hash;	
	protected $lastaccess;	
	protected $context;
	protected $todo;	
	
	public function __construct($hash="") {
		$this->hash=$hash;
		$this->fetch_datas();
	} 	
	
	private function fetch_datas(){
		global $dbh;
		
		$query = "select * from shorturls where shorturl_hash = '".$this->hash."'";
		$result=pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->id=$row->id_shorturl;
			$this->lastaccess=$row->shorturl_lastaccess;
			$this->context=$row->shorturl_context;
			$class_name=$this->get_type_class_name($row->shorturl_type);
			if($class_name) $this->type= new $class_name($row->shorturl_type);
			$this->todo=$this->get_todo_function_name($row->shorturl_action);
		}	
	}
	
	protected function get_type_class_name($type=""){
		if($type){
			if(class_exists("shorturl_type_".$type)){
				return "shorturl_type_".$type;
			}
		}	
		return false;
	}
	
	protected function get_todo_function_name($action=""){
		if($action){
			$function="generate_".$action;
			return $function;
			if(function_exists($this->type->$function)){
				return $function;
			}
		}
		return false;
	}	
	
	public function proceed(){		
		global $dbh;
		
		if(!$this->id) return "";
		$query = "update shorturls set shorturl_last_access=now() where shorturl_hash = '".$this->hash."'";
		pmb_mysql_query($query, $dbh);
		
		return $this->type->generate_rss($this->context,$this->hash);
		//$func=$this->type->$this->todo;
		//return $func($this->get_context());
	}
	
	public function generate_obj($type, $action, $context){	
		global $dbh;
		$hash=self::generate_hash($type, $action, $context);
		$query = "select shorturl_hash from shorturls where shorturl_hash='$hash' ";
		$result=pmb_mysql_query($query, $dbh);
		if (!pmb_mysql_num_rows($result)) {
			$query = "insert into shorturls set shorturl_hash='$hash', shorturl_type='$type', shorturl_action='$action', shorturl_context = '$context'";
			pmb_mysql_query($query, $dbh);
		}
		return $hash;
		
	}
	
	public function generate_hash($type, $action, $context){
		return md5($type."_".$action."_".$context);
	}
			
	public function get_id(){
		return $this->id;
	}
	
	public function get_hash(){
		return $this->hash;
	}
	
	public function get_lastaccess(){
		return $this->lastaccess;
	}
	
	public function get_context(){
		return $this->context;
	}
	
	public function get_action(){
		return $this->todo;
	}	
	
	public function set_hash($hash){
		$this->hash=$hash;
	}
	
	public function set_lastaccess($lastaccess){
		$this->lastaccess=$lastaccess;
	}
	
	public function set_context($context){
		$this->context=$context;
	}
	
	public function set_action($todo){
		$this->todo=$todo;
	}
} // end of class

