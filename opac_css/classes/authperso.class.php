<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.class.php,v 1.5 2015-04-16 16:09:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/custom_parametres_perso.class.php");
require_once($class_path."/authperso_authority.class.php");
@ini_set('zend.ze1_compatibility_mode',0);
require_once($include_path."/h2o/h2o.php");
require_once("$class_path/aut_link.class.php");


class authperso {
	var $id=0; // id de authperso
	var $info=array();
	var $elt_id=0;
	
	function authperso($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	function fetch_data() {		
		$this->info=array();
		$this->info['fields']=array();
		if(!$this->id) return;
		
		$req="select * from authperso where id_authperso=". $this->id." order by authperso_name";		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_authperso;	
			$this->info['name']= $r->authperso_name;
			$this->info['onglet_num']= $r->authperso_notice_onglet_num;			
			$this->info['isbd_script']= $r->authperso_isbd_script;			
			$this->info['opac_search']= $r->authperso_opac_search;			
			$this->info['opac_multi_search']= $r->authperso_opac_multi_search;			
			$this->info['comment']= $r->authperso_comment;
			$this->info['onglet_name']="";
			$req="SELECT * FROM notice_onglet where id_onglet=".$r->authperso_notice_onglet_num;
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$r_onglet=pmb_mysql_fetch_object($resultat);	
				$this->info['onglet_name']= $r_onglet->onglet_name;						
			}	
		}		
		$req="select * from authperso_custom where num_type=". $this->id." order by ordre";		
		$resultat=pmb_mysql_query($req);	
		$i=0;
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info['fields'][$i]['id']= $r->idchamp;	
				$this->info['fields'][$i]['name']= $r->name;	
				$this->info['fields'][$i]['label']= $r->titre;	
				$this->info['fields'][$i]['type']= $r->type ;	
				$this->info['fields'][$i]['ordre']= $r->ordre ;				
				$this->info['fields'][$i]['search']=$r->search;				
				$this->info['fields'][$i]['pond']=$r->pond;
				$this->info['fields'][$i]['obligatoire']=$r->obligatoire;
				$this->info['fields'][$i]['export']=$r->export;
				$this->info['fields'][$i]['multiple']=$r->multiple;
				$this->info['fields'][$i]['opac_sort']=$r->opac_sort;
				$this->info['fields'][$i]['code_champ']=$this->id;
				$this->info['fields'][$i]['code_ss_champ']=$r->idchamp;
				$this->info['fields'][$i]['data']= array();		
							
				$i++;
			}
		}
	}
	
	function get_data(){
		return $this->info;
	}
	
	function fetch_data_auth($id) {
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$authperso_fields=$p_perso->get_out_values($id);
		
		$this->info['data_auth'][$id]=$p_perso->values;
		//pour ne pas louper les champs vides...
		foreach($this->info['fields'] as $i =>$field){
			$this->info['fields'][$i]['data'][$id]=$this->info['data_auth'][$id][$field['name']];
		}
		return $p_perso->values;
	}
	
	// Génération de l'isbd de l'autorité
	function get_isbd($id){
		global $dbh;
		
		$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req,$dbh);
		if(($r=pmb_mysql_fetch_object($res))) {			
			$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id,$option_navigation,$option_visibilite);
			$fields=$p_perso->get_out_values($id);			
			$authperso_fields=$p_perso->values;			
			if($r->authperso_isbd_script){
				$isbd=H2o::parseString($r->authperso_isbd_script)->render($authperso_fields);
			}else{
				foreach ($authperso_fields as $field){					
					$isbd.=$field[values][0][format_value].".  ";
				}
			}		
		}
		return $isbd;
	}
	
	// Génération de la notice d'autorité
	function get_view($id){
		global $dbh;
	
		$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req,$dbh);
		if(($r=pmb_mysql_fetch_object($res))) {
			$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id,$option_navigation,$option_visibilite);
			$fields=$p_perso->get_out_values($id);
			$authperso_fields=$p_perso->values;
			$aut_link= new aut_link($r->authperso_authority_authperso_num + 1000,$id);		
			$authperso_fields['authorities_link']=$aut_link->get_data();
			//printr($authperso_fields);
			if($r->authperso_view_script){
				$view=H2o::parseString($r->authperso_view_script)->render($authperso_fields);
			}else{
				foreach ($authperso_fields as $field){					
					$view.=$field[values][0][format_value].".  ";
				}
			}
		}
		return $view;
	}
	
} //authperso class end


class authpersos {	
	var $info=array();
	
	
	public static function get_name($id_authperso){
		global $dbh;
		
		$id_authperso+=0;
		$query = "select authperso_name from authperso where id_authperso = ".$id_authperso;
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			return pmb_mysql_result($result, 0);
		}
	}

	function authpersos() {
		$this->fetch_data();
	}
	
	function fetch_data() {
		global $PMBuserid;
		$this->info=array();
		$i=0;
		$req="select * from authperso ";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$authperso= new authperso($r->id_authperso);
				//$this->info[$i]=$authperso->get_data();
				$this->info[$r->id_authperso]=$authperso->get_data();
				$i++;
			}
		}
	}
	
	function get_data(){
		return($this->info);
	}
	
	function get_simple_seach_list_tpl() {
		global $look_FIRSTACCESS ; // si 0 alors premier Acces : la rech par defaut est cochee
		global $get_query;
		
		$ou_chercher_tab=array();
		foreach($this->info as $authperso){			
			
			if (!$authperso['opac_search']) continue;
			
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$look=$$look_name;
			
			if (!$look_FIRSTACCESS && !$get_query ) {
				if ($authperso['opac_search']==2) $look = 1 ;
			}			
			if($look){
				$checked_AUTHPERSO= " checked='' " ; 
				$this->simple_seach_list_checked=1;
			}else $checked_AUTHPERSO="";
			
			$ou_chercher_tab[]= "\n<span style='width: 30%; float: left;'><input type='checkbox' name='$look_name' id='$look_name' value='1' $checked_AUTHPERSO/><label for='$look_name'> ".$authperso['name']." </label></span>";
			
		}
		return $ou_chercher_tab;
	}
	
	function get_simple_seach_list_tpl_hiden() {
		$tpl="";
		foreach($this->info as $authperso){				
			if (!$authperso['opac_search']) continue;
							
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$look=$$look_name;
			if($look)$tpl.="<input type='hidden' name='$look_name' id='$look_name' value='1' />";				
		}
		return $tpl;
	}

	function make_search_test() {
		$tpl="";
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
				
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$look=$$look_name;
			if($look)$tpl.="<input type='hidden' name='$look_name' id='$look_name' value='1' />";
		}
		return $tpl;
	}
	
	function get_field_text($id) {
				
		$auth=new authperso_authority($id);		
		return  array('valeur_champ'=>get_isbd(),"look_AUTHPERSO_".'typ_search'=>get_authperso_num());
		
	}	
	
	function search_authperso($user_query) {
    	global $opac_search_other_function,$typdoc,$charset,$dbh;
    	global $opac_stemming_active;
    	$total_results=0;
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
				
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$look=$$look_name;
			if(!$look) continue;
			
			$clause = '';
			$add_notice = '';
			
			$aq=new analyse_query(stripslashes($user_query),0,0,1,1,$opac_stemming_active);
			$members=$aq->get_query_members("authperso_authorities","authperso_infos_global","authperso_index_infos_global","id_authperso_authority");
			$clause.= "where ".$members["where"] ." and authperso_authority_authperso_num=".$authperso['id'];
			
			if ($opac_search_other_function) $add_notice=search_other_function_clause();
			if ($typdoc || $add_notice) $clause = ', notices, notices_authperso '.$clause;
			if ($typdoc) $clause.=" and notice_authperso_notice_num=notice_id and typdoc='".$typdoc."' ";
			if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')';
					
			$tri = 'order by pert desc, authperso_index_infos_global';
			$pert=$members["select"]." as pert";
			
			$auth_res = pmb_mysql_query("SELECT COUNT(distinct id_authperso_authority) FROM authperso_authorities $clause", $dbh);
			$nb_result = pmb_mysql_result($auth_res, 0 , 0);
			if ($nb_result) {
				$total_results+=$nb_result;
				//définition du formulaire
				$form = "<div style=search_result><form name=\"search_authperso_".$authperso['id']."\" action=\"./index.php?lvl=more_results\" method=\"post\">";
				$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
				if (function_exists("search_other_function_post_values")){
					$form .=search_other_function_post_values();
				}
				$form .= "<input type=\"hidden\" name=\"mode\" value=\"authperso_".$authperso['id']."\">\n";
				$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
				$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result ."\">\n";
				$form .= "<input type=\"hidden\" name=\"name\" value=\"".$authperso["name"] ."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">";
				$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form>\n";
				$form .= "</div>";
				 
				$_SESSION["level1"]["authperso_".$authperso['id']]["form"]=$form;
				$_SESSION["level1"]["authperso_".$authperso['id']]["count"]=$nb_result;
				$_SESSION["level1"]["authperso_".$authperso['id']]["name"]=$authperso["name"];
			}
		}		
	    	
    	return $total_results;
	}	
	
	function rec_history($n) {
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$look=$$look_name;
			if($look)$_SESSION[$look_name.$n]=$look;
		}
	}
	
	function get_history($n) {
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			$$look_name=$_SESSION[$look_name.$n];
		}
	}
	function get_human_query($n) {
		foreach($this->info as $authperso){
			if (!$authperso['opac_search']) continue;
	
			$look_name="look_AUTHPERSO_".$authperso['id']."#";
			global $$look_name;
			if ($_SESSION["$look_name".$n]) $r1.=$authperso['name']." ";
		}
		return $r1;
	}
	
} // authpersos class end
	
