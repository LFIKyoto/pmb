<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parametres_perso.class.php,v 1.25.4.1 2015-09-11 12:50:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Gestion des champs personalisés simplifiée pour l'OPAC

require_once($include_path."/parser.inc.php");
require_once($include_path."/fields_empr.inc.php");
require_once($include_path."/datatype.inc.php");


class parametres_perso {
	
	var $prefix;
	var $no_special_fields;
	var $values;
	
	//Créateur : passer dans $prefix le type de champs persos et dans $base_url l'url a appeller pour les formulaires de gestion	
	function parametres_perso($prefix) {
		global $_custom_prefixe_;

		$this->prefix=$prefix;
		$this->base_url=$base_url;
		$_custom_prefixe_=$prefix;
		
		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		$requete="select idchamp, name, titre, type, datatype, obligatoire, options, multiple, export, search, opac_sort from ".$this->prefix."_custom order by ordre";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)==0)
			$this->no_special_fields=1;
		else {
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$this->t_fields[$r->idchamp]["DATATYPE"]=$r->datatype;
				$this->t_fields[$r->idchamp]["NAME"]=$r->name;
				$this->t_fields[$r->idchamp]["TITRE"]=$r->titre;
				$this->t_fields[$r->idchamp]["TYPE"]=$r->type;
				$this->t_fields[$r->idchamp]["OPTIONS"]=$r->options;
				$this->t_fields[$r->idchamp]["MANDATORY"]=$r->obligatoire;
				$this->t_fields[$r->idchamp]["OPAC_SHOW"]=$r->multiple;
				$this->t_fields[$r->idchamp]["EXPORT"]=$r->export;
				$this->t_fields[$r->idchamp]["SEARCH"]=$r->search;
				$this->t_fields[$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
			}
		}
	}
	
	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	function get_values($id) {
		//Récupération des valeurs stockées 
		if ((!$this->no_special_fields)&&($id)) {
			$this->values = array() ;
			$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float from ".$this->prefix."_custom_values where ".$this->prefix."_custom_origine=".$id;
			$resultat=pmb_mysql_query($requete);
			while ($r=pmb_mysql_fetch_array($resultat)) {
				$this->values[$r[$this->prefix."_custom_champ"]][]=$r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]];
			}
		} else $this->values=array();
	}

	//Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
	function show_editable_fields($id,$from_z3950=false) {
		global $aff_list_empr,$charset;
		$perso=array();
		
		if (!$this->no_special_fields) {
			if(!$from_z3950){
				$this->get_values($id);
			}
			$check_scripts="";
			reset($this->t_fields);
			while (list($key,$val)=each($this->t_fields)) {
				$t=array();
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
			
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];				
				$field["SEARCH"]=$this->t_fields[$key]["SEARCH"];
				$field["EXPORT"]=$this->t_fields[$key]["EXPORT"];	
				$field["EXCLUSION"]=$this->t_fields[$key]["EXCLUSION"];	
				$field["OPAC_SORT"]=$this->t_fields[$key]["OPAC_SORT"];	
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$key]["OPTIONS"], "OPTIONS");
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				eval("\$aff=".$aff_list_empr[$this->t_fields[$key][TYPE]]."(\$field,\$check_scripts);");
				$t["AFF"]=$aff;
				$t["NAME"]=$field["NAME"];
				$perso["FIELDS"][]=$t;
			}
			//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
			$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
			$perso["CHECK_SCRIPTS"]=$check_scripts;
		} else 
			$perso["CHECK_SCRIPTS"]="<script>function check_form() { return true; }</script>";
		return $perso;
	}
	
	//Affichage des champs en lecture seule pour visualisation d'un fiche emprunteur ou autre...
	//Retourne le tableau $perso de valeur des champs :
	//$perso["FIELDS"]=tableau des champs
	//Pour le Xième champ : 
	//	$perso["FIELDS"][X]["TITRE"] : libellé du champ
	//	$perso["FIELDS"][X]["AFF"] : contenu du champ
	//	$perso["FIELDS"][X]["OPAC_SHOW"] : affichable ou pas dans l'opac (1=affichable, 0=non affichable)
	
	function show_fields($id) {
		global $val_list_empr;
		global $charset;
		
		$perso=array();
		//Récupération des valeurs stockées pour l'emprunteur
		$this->get_values($id);
		if (!$this->no_special_fields) {
			//Affichage champs persos
			$c=0;
			reset($this->t_fields);
			while (list($key,$val)=each($this->t_fields)) {
				$t=array();
				$t["TITRE"]="<b>".htmlentities($val["TITRE"],ENT_QUOTES,$charset)."&nbsp;: </b>";
				$t["TITRE_CLEAN"]=htmlentities($val["TITRE"],ENT_QUOTES,$charset);
				$t["OPAC_SHOW"]=$val["OPAC_SHOW"];
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
				$field["OPAC_SORT"]=$this->t_fields[$key]["OPAC_SORT"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$key]["OPTIONS"], "OPTIONS");
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				$aff=$val_list_empr[$this->t_fields[$key]["TYPE"]]($field,$this->values[$key]);
				if (is_array($aff) && $aff[ishtml] == true)$t["AFF"] = $aff["value"];
				else $t["AFF"]=htmlentities($aff,ENT_QUOTES,$charset);
				$t["ID"]=$field["ID"];
				$t["NAME"]=$field["NAME"];
				$perso["FIELDS"][]=$t;
			}
		}
		return $perso;
	}
	
	function get_formatted_output($values,$field_id) {
		global $val_list_empr, $charset;
		
		$field=array();
		$field["ID"]=$field_id;
		$field["NAME"]=$this->t_fields[$field_id]["NAME"];
		$field["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
		$field["OPAC_SORT"]=$this->t_fields[$field_id]["OPAC_SORT"];
		$field["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
		$field["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
		$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$field_id]["OPTIONS"], "OPTIONS");
		$field["VALUES"]=$values;
		$field["PREFIX"]=$this->prefix;
		$aff=$val_list_empr[$this->t_fields[$field_id]["TYPE"]]($field,$values);
		if(is_array($aff)) return $aff['withoutHTML']; 
		else return $aff;
	}

	function get_fields_recherche($id) {
		$return_val='';
		
		$this->get_values($id);
		if (!$this->no_special_fields) {
			reset($this->t_fields);		
			while (list($key,$val)=each($this->t_fields)) {
				if($this->t_fields[$key]["SEARCH"] ) {
					for ($i=0; $i<count($this->values[$key]); $i++) {
						$return_val.=$this->values[$key][$i].' ';
					}
				}	
			}
		}		
		return stripslashes($return_val);
	}	

	function get_ajax_list($name, $start) {
		global $charset,$dbh;

		$values=array();
		reset($this->t_fields);
		while (list($key,$val)=each($this->t_fields)) {
			if($val['NAME'] == $name) {
				switch ($val['TYPE']) {
					case 'list' :
						$q="select ".$this->prefix."_custom_list_value, ".$this->prefix."_custom_list_lib from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=".$key." order by ordre";
						$r=pmb_mysql_query($q,$dbh);	
						if(pmb_mysql_num_rows($r)) {
							while ($row=pmb_mysql_fetch_row($r)) {
								$values[$row[0]]=$row[1];
							}
						}
						break;
					case 'query_list' :
						$field['OPTIONS'][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$val['OPTIONS'], 'OPTIONS');
						$q=$field['OPTIONS'][0]['QUERY'][0]['value'];
						$r = pmb_mysql_query($q,$dbh);
						if(pmb_mysql_num_rows($r)) {
							while ($row=pmb_mysql_fetch_row($r)) {
								$values[$row[0]]=$row[1];
							}
						}
						break;
				}
				break;
			}	
		}
		if (count($values) && $start && $start!='%') {
			$filtered_values=array();
			foreach($values as $k=>$v) {
				if (strtolower(substr($v,0,strlen($start)))==strtolower($start)) {
					$filtered_values[$k]=$v;
				}
			}
			return $filtered_values;
		}
		return $values;
	}	
	
	function get_val_field($id_elt,$name) {
		global $val_list_empr;
		global $charset;		
		if (!$this->no_special_fields) {	
			$this->get_values($id_elt);
			foreach($this->t_fields as $key=>$val){			
				if($val["NAME"] == $name){
					//$this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])
					return $this->get_formatted_output($this->values[$key],$key);					
				}	
			}			
		}		
		return "";
	}
	
	function get_field_form($id,$field_name,$values){
		global $aff_list_empr_search,$charset;
		$field=array();
		$field[ID]=$id;
		$field[NAME]=$this->t_fields[$id][NAME];
		$field[MANDATORY]=$this->t_fields[$id][MANDATORY];
		$field[ALIAS]=$this->t_fields[$id][TITRE];
		$field[DATATYPE]=$this->t_fields[$id][DATATYPE];
		$field[OPTIONS][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$id][OPTIONS], "OPTIONS");
		$field[VALUES]=$values;
		$field[PREFIX]=$this->prefix;
		eval("\$r=".$aff_list_empr_search[$this->t_fields[$id][TYPE]]."(\$field,\$check_scripts,\$field_name);");
		return $r;
	}
	
	//Lecture des champs de recherche
	function read_search_fields_from_form() {
	
		$perso=array();
		reset($this->t_fields);
		while (list($key,$val)=each($this->t_fields)) {
			if($this->t_fields[$key]["SEARCH"]) {
				$t=array();
				$t["DATATYPE"]=$val["DATATYPE"];
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
				$name = $this->t_fields[$key]["NAME"];
				$values = array();
				if($val["NAME"] == $name) {
					global $$name;
					$value=$$name;
					for ($i=0; $i<count($value); $i++) {
						if($value[$i]) {
							$values[] = $value[$i];
						}
					}
				}
				$t["VALUE"]=$values;
				$perso["FIELDS"][]=$t;
			}
		}
		return $perso;
	}
}

?>