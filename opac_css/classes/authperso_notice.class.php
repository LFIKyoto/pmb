<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_notice.class.php,v 1.7 2015-06-30 09:59:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/authperso.class.php");

class authperso_notice {
	public $id=0; // id de la notice
	public $auth_info=array();	
	public $onglets_auth_list = array();
	
	function authperso_notice($id=0) {
		$this->id=$id+0; // id de la notice
		$this->fetch_data();
	}
	
	function fetch_data() {		
		global $dbh;
		
		$this->auth_info=array();		
		// pour chaque autorités existantes récupérér les autorités affectés à la notice
		$req="select * from authperso, notices_authperso,authperso_authorities where id_authperso=authperso_authority_authperso_num and notice_authperso_authority_num=id_authperso_authority and notice_authperso_notice_num=".$this->id."
		order by notice_authperso_order";
		$res = pmb_mysql_query($req,$dbh);
		while(($r=pmb_mysql_fetch_object($res))) {
			$this->auth_info[$r->notice_authperso_authority_num]['onglet_num']=$r->authperso_notice_onglet_num;
			$this->auth_info[$r->notice_authperso_authority_num]['authperso_name']=$r->authperso_name;
			$authperso = new authperso($r->id_authperso);
			$isbd=$authperso->get_isbd($r->notice_authperso_authority_num);
			$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['id']=$r->notice_authperso_authority_num;
			$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['isbd']=$isbd;
			$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['authperso_name']=$r->authperso_name;		
			$view=$authperso->get_view($r->notice_authperso_authority_num);
			$this->auth_info[$r->notice_authperso_authority_num]['isbd']=$isbd;
			$this->auth_info[$r->notice_authperso_authority_num]['view']=$view;
			$this->auth_info[$r->notice_authperso_authority_num]['auth_see']="<a href='./index.php?lvl=authperso_see&id=".$r->notice_authperso_authority_num."'>$isbd</a>";
		}
	}
	
	function get_info(){		
		return $this->auth_info;
	}	
	
	function get_notice_display(){
		$aff="";
		foreach($this->onglets_auth_list as $onglet_num => $onglet){
			$authperso_name="";
			foreach($onglet as $authperso_num => $auth_perso){
				foreach($auth_perso as $auth_num => $auth){
					if($authperso_name!=$auth['authperso_name']){
						$authperso_name=$auth['authperso_name'];
						$aff.="<br><b>".$authperso_name."</b>&nbsp;: ";
						$new=1;
					}
					if(!$new)	$aff.=", ";
					$aff.=$auth['isbd'];
					$new=0;
				}
			}
		}
		return $aff;
	}
	
	function get_notice_display_list(){
		$aff_list=array();
		foreach($this->onglets_auth_list as $onglet_num => $onglet){
			$authperso_name="";
			foreach($onglet as $authperso_num => $auth_perso){
				$aff_list[$authperso_num]['isbd']="";
				$aff_list[$authperso_num]['name']="";
				foreach($auth_perso as $auth_num => $auth){
					$aff_list[$authperso_num]['name']=$auth['authperso_name'];
					if($aff_list[$authperso_num]['isbd'])$aff_list[$authperso_num]['isbd'].=", ";
					$aff_list[$authperso_num]['isbd'].=$auth['isbd'];
				}
			}
		}
		return $aff_list;
	}
	
} // authperso_notice class end
	
