<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.12 2015-05-26 08:21:38 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/rec_history.inc.php");

//Classe de gestion de la recherche spécial "facette"

class facette_search {
	var $id;
	var $n_ligne;
	var $params;
	var $search;
	var $champ_base;
	
	//Constructeur
    function facette_search($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    	
    	//les facettes sont désormais un tableau de tableaux
    	//il faut parfois les desérialiser quand on est passé par un formulaire
    	$field_name="field_".$this->n_ligne."_s_".$this->id;
    	global $$field_name,$launch_search;
    	$valeur = $$field_name;
    	if (!is_array($valeur[0])) {
    		$tmpValeur = unserialize(stripslashes($valeur[0]));
    		while (($tmpValeur !== false) && (!is_array($tmpValeur[0]))) {
    			$tmpValeur=unserialize(stripslashes($tmpValeur[0]));
    		}
    		if ($tmpValeur !== false) {
    			$valeur = $tmpValeur;
    			$$field_name = $tmpValeur;
    		}
    	}
    }
    
	function get_op() {
    	$operators = array();
    	if ($_SESSION["nb_queries"]!=0) {
    		$operators["EQ"]="=";
    	}
    	return $operators;
    }
    
    function make_search(){
		global $dbh;
		
    	$valeur = "field_".$this->n_ligne."_s_".$this->id;
    	global $$valeur;
    	
    	$filter_array = $$valeur; 	
    	if (!is_array($filter_array[0])) {
	   		$tmpValeur = unserialize(stripslashes($filter_array[0]));
	  		
	    	if ($tmpValeur !== false) {
	    		$$valeur = $tmpValeur;
	    	}
    	}
    	$filter_array = $$valeur;

    	$table_name = "table_facette_temp".$this->n_ligne;
  		$req_table_tempo = "CREATE TEMPORARY TABLE ".$table_name." (notice_id int, index i_notice_id(notice_id))";
  		$req = pmb_mysql_query($req_table_tempo,$dbh) or die ();
  		
   		foreach ($filter_array as $k=>$v) {
  			$filter_value = $v[1];
    		$filter_field = $v[2];
    		$filter_subfield = $v[3];

    		if(!$k){
	  			$req_table_tempo = "INSERT INTO ".$table_name." SELECT DISTINCT id_notice FROM notices_fields_global_index WHERE code_champ = ".($filter_field+0)." AND code_ss_champ = ".($filter_subfield+0)." AND (";
		  		foreach ($filter_value as $k2=>$v2) {
		  			if ($k2) {
		  				$req_table_tempo .= " OR ";
		  			}
		  			$req_table_tempo .= "value ='".addslashes($v2)."'";
		  		}
	  			$req_table_tempo .= ")";
    		}else{
    			$req_table_tempo = "DELETE FROM ".$table_name." WHERE notice_id NOT IN (SELECT DISTINCT id_notice FROM notices_fields_global_index WHERE code_champ = ".($filter_field+0)." AND code_ss_champ = ".($filter_subfield+0)." AND (";
		  		foreach ($filter_value as $k2=>$v2) {
		  			if ($k2) {
		  				$req_table_tempo .= " OR ";
		  			}
		  			$req_table_tempo .= "value ='".addslashes($v2)."'";
		  		}
	  			$req_table_tempo .= "))";
    		}
  			$req = pmb_mysql_query($req_table_tempo,$dbh) or die ();
  		}
  		 	
    	return $table_name;
    	
    }
    
    function make_human_query(){
		global $dbh, $champ_base, $msg;
		$literral_words = array();
    	
    	$valeur="field_".$this->n_ligne."_s_".$this->id;
    	global $$valeur;
    	$valeur = $$valeur;
    	$item_literal_words = array();
    	foreach ($valeur as $k=>$v) {
	    	$filter_value = $v[1];
	    	$filter_name = $v[0];
	    	
	    	$libValue = "";
	    	foreach ($filter_value as $value) {
	    		if ($libValue) $libValue .= ' '.$msg["search_or"].' ';
	    		$libValue .= (substr($value, 0, 4) == "msg:" ? $msg[substr($value, 4)] : $value);
	    	}
			$item_literal_words[] = stripslashes($filter_name)." : '".stripslashes($libValue)."'";
    	}
    	
    	$literral_words[] = implode(' '.$msg["search_and"].' ',$item_literal_words);
    	
    	return $literral_words;
    }
    
    function get_input_box() {
    	global $charset, $dbh, $msg;
    	
    	$field_name="field_".$this->n_ligne."_s_".$this->id;
    	global $$field_name,$launch_search;
    	$valeur = $$field_name;

    	$item_literal_words = array();
    	
    	foreach ($valeur as $k=>$v) {
	    	$filter_value = $v[1];
	    	$filter_name = $v[0];

	    	if (count($filter_value)==1) {
	    		$libValue = $filter_value[0];
	    	} else {
	    		$libValue = implode(' '.$msg["search_or"].' ',$filter_value);
	    	}
			$item_literal_words[] = stripslashes($filter_name)." : '".stripslashes($libValue)."'";
    	}
    	
    	$literral_words = implode(' '.$msg["search_and"].' ',$item_literal_words);
    	
    	$form=$literral_words;
    	$form.="<input type='hidden' name='".$field_name."[]' value=\"".htmlentities(serialize($valeur),ENT_QUOTES,$charset)."\"/>";
		
    	return $form;
    }
    
}
?>