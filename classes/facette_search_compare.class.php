<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_search_compare.class.php,v 1.2 2015-04-03 11:16:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des facettes pour la recherche OPAC

// inclusions principales
require_once("$include_path/templates/facette_search_compare_tpl.php");
require_once("$class_path/notice_tpl_gen.class.php");

class facette_search_compare {
	public $notice_tpl;
	public $notice_nb;
	
	function __construct($compare_notice_template,$compare_notice_nb){
		$this->notice_tpl=$compare_notice_template;
		$this->notice_nb=$compare_notice_nb;
	}
	
	function display_compare(){
		global $tpl_display_compare,$msg,$charset;
		
		$tpl=new notice_tpl_gen($this->notice_tpl);
		
		$tpl_display_compare = str_replace('!!notice_nb_libelle!!', htmlentities($this->notice_nb,ENT_QUOTES,$charset), $tpl_display_compare);
		$tpl_display_compare = str_replace('!!notice_tpl_libelle!!',htmlentities($tpl->name,ENT_QUOTES,$charset) , $tpl_display_compare);
		
		return $tpl_display_compare;
	}
	
	function form_compare(){
		global $tpl_form_compare, $msg,$charset;
		
		$sel_notice_tpl=notice_tpl_gen::gen_tpl_select("notice_tpl",$this->notice_tpl,'');
	
		$tpl_form_compare = str_replace('!!notice_nb!!', $this->notice_nb, $tpl_form_compare);
		
		$tpl_form_compare = str_replace('!!sel_notice_tpl!!', $sel_notice_tpl, $tpl_form_compare);
	
		return $tpl_form_compare;
	}
	
	function save_form_compare(){
		global $dbh;
		global $notice_tpl;
		global $notice_nb;

		
		$notice_tpl=$notice_tpl*1;
		$notice_nb=$notice_nb*1;
		
		$query="UPDATE parametres SET valeur_param=$notice_tpl WHERE type_param='pmb' AND sstype_param='compare_notice_template'";
		pmb_mysql_query($query,$dbh);
		
		$query="UPDATE parametres SET valeur_param=$notice_nb WHERE type_param='pmb' AND sstype_param='compare_notice_nb'";
		pmb_mysql_query($query,$dbh);
		
		$this->notice_tpl=$notice_tpl;
		$this->notice_nb=$notice_nb;
		return true;
	}
}
