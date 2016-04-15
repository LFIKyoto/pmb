<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_authority.class.php,v 1.2 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/authperso.class.php");

class authperso_authority {
	var $id=0; // id de l'autorité 
	var $info=array();
	var $elt_id=0;
	
	function authperso_authority($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	function fetch_data() {		
		global $dbh;
		
		$this->info=array();
		if(!$this->id) return;
		
		$req="select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority=". $this->id;
		$res = pmb_mysql_query($req,$dbh);
		if(($r=pmb_mysql_fetch_object($res))) {			
			$authperso=new authperso($r->authperso_authority_authperso_num);
			$this->info['isbd']=$authperso->get_isbd($this->id);
			$this->info['view']=$authperso->get_view($this->id);
			$this->info['authperso']=$authperso->get_data();
			$this->info['data']=$authperso->fetch_data_auth($this->id);		
			$this->info['authperso_num']=$r->authperso_authority_authperso_num;			
		}
	}
	
	function get_data() {
		return $this->info;
	}
	
	function get_isbd() {
		return $this->info['isbd'];
	}
	
	function get_authperso_num() {
		return $this->info['authperso_num'];
	}
	
	function print_resume() {
		if(!$this->info['view'])return($this->info['authperso']['name'] ." : ".$this->info['isbd']);	
		else return $this->info['view'];
	}

} //authperso class end

