<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret_parametres_perso.class.php,v 1.2 2015-06-30 13:27:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/pret_parametres_perso.tpl.php");

class pret_parametres_perso extends parametres_perso {
	
	//Créateur : passer dans $prefix le type de champs persos et dans $base_url l'url a appeller pour les formulaires de gestion	
	function __construct($prefix,$base_url="",$option_visibilite=array()) {
		global $_custom_prefixe_;
		
		$this->option_visibilite=$option_visibilite;
		
		$this->prefix=$prefix;
		$this->base_url=$base_url;
		$_custom_prefixe_=$prefix;
		
		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		$requete="select idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, filters, exclusion_obligatoire, pond, opac_sort from ".$this->prefix."_custom order by ordre";

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
				$this->t_fields[$r->idchamp]["FILTERS"]=$r->filters;
				$this->t_fields[$r->idchamp]["POND"]=$r->pond;
			}
		}
	}
	
	//Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
	function show_field_list() {
		global $type_list_empr;
		global $datatype_list;
		global $form_list;
		global $msg;
	
		$res="";		
		$requete="select idchamp, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export, filters, exclusion_obligatoire, opac_sort from ".$this->prefix."_custom order by ordre";
		$resultat=pmb_mysql_query($requete);
		/*if(!$resultat)
		{
			echo "ya pas de res : ".pmb_mysql_num_rows($resultat)."<br />";
		}
		echo "nombre : ".pmb_mysql_num_rows($resultat)."<br />";*/
		if (pmb_mysql_num_rows($resultat)==0) {
			$res="<br /><br />".$msg["parperso_no_field"]."<br />";
			$form_list=str_replace("!!liste_champs_perso!!",$res,$form_list);
			$form_list=str_replace("!!base_url!!",$this->base_url,$form_list);
			return $form_list;
		} else {
			$res="<table width=100%>\n";
			$res.="<tr><th></th><th>".$msg["parperso_field_name"]."</th><th>".$msg["parperso_field_title"]."</th><th>".$msg["parperso_input_type"]."</th><th>".$msg["parperso_data_type"]."</th>";
			if($this->option_visibilite["obligatoire"] == "block") $res.= "<th>".$msg["parperso_mandatory"]."</th>" ;
			if($this->option_visibilite["filters"] == "block") $res.= "<th>".$msg["parperso_filters"]."</th>" ;
			else $res .= "</tr>\n";
			$parity=1;
			$n=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity+=1;
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
				$action_td=" onmousedown=\"document.location='".$this->base_url."&action=edit&id=$r->idchamp';\" ";
				$res.="<tr class='$pair_impair' style='cursor: pointer' $tr_javascript>";
				$res.="<td>";
				$res.="<input type='button' class='bouton_small' value='-' onClick='document.location=\"".$this->base_url."&action=up&id=".$r->idchamp."\"'/></a><input type='button' class='bouton_small' value='+' onClick='document.location=\"".$this->base_url."&action=down&id=".$r->idchamp."\"'/>";
				$res.="</td>";
				$res.="<td $action_td><b>".$r->name."</b></td><td $action_td>".$r->titre."</td><td $action_td>".$type_list_empr[$r->type]."</td><td $action_td>".$datatype_list[$r->datatype]."</td>";
				if($this->option_visibilite["obligatoire"] == "block") { 
					$res.="<td $action_td>";
					if ($r->obligatoire==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["filters"] == "block") {
					$res.="<td $action_td>";
					if ($r->filters==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				$res.="</tr>\n";
			}
			$res.="</table>";
			$form_list=str_replace("!!liste_champs_perso!!",$res,$form_list);
			$form_list=str_replace("!!base_url!!",$this->base_url,$form_list);
			return $form_list;
		}
	}
	
	//Affichage du formulaire d'édition d'un champ perso
	function show_edit_form($idchamp=0) {
		global $charset;
		global $type_list_empr;
		global $datatype_list;
		global $form_loan_edit;
		global $include_path;
		global $msg;
				
		if ($idchamp!=0 and $idchamp!="") {
			$requete="select idchamp, name, titre, type, datatype, options, multiple, obligatoire, ordre, search, export, filters, exclusion_obligatoire, pond, opac_sort from ".$this->prefix."_custom where idchamp=$idchamp";
			$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
			$r=pmb_mysql_fetch_object($resultat);
			
			$name=$r->name;
			$titre=htmlentities($r->titre,ENT_QUOTES,$charset);
			$type=$r->type;
			$datatype=$r->datatype;
			$options=htmlentities($r->options,ENT_QUOTES,$charset);
			$obligatoire=$r->obligatoire;
			$ordre=$r->ordre;
			$filters=$r->filters;
			$pond=$r->pond;
			$opac_sort=$r->opac_sort;
			$form_loan_edit=str_replace("!!form_titre!!",sprintf($msg["parperso_field_edition"],$name),$form_loan_edit);
			$form_loan_edit=str_replace("!!action!!","update",$form_loan_edit);
			
			if ($r->options!="") {
				$param=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
				$form_loan_edit=str_replace("!!for!!",$param["FOR"],$form_loan_edit);
			} else {
				$form_loan_edit=str_replace("!!for!!","",$form_loan_edit);
			}
			$form_loan_edit=str_replace("!!supprimer!!","&nbsp;<input type='button' class='bouton' value='".$msg["63"]."' onClick=\"if (confirm('".$msg["parperso_delete_field"]."')) { this.form.action.value='delete'; this.form.submit();} else return false;\">",$form_loan_edit);
		} else {
			$form_loan_edit=str_replace("!!form_titre!!",$msg["parperso_create_new_field"],$form_loan_edit);
			$form_loan_edit=str_replace("!!action!!","create",$form_loan_edit);
			$form_loan_edit=str_replace("!!for!!","",$form_loan_edit);
			$form_loan_edit=str_replace("!!supprimer!!","",$form_loan_edit);
		}
		
		$onclick="openPopUp('".$include_path."/options_empr/options.php?name=&type='+this.form.type.options[this.form.type.selectedIndex].value+'&_custom_prefixe_=".$this->prefix."','options',550,600,-2,-2,'menubars=no,resizable=yes,scrollbars=yes');";
		$form_loan_edit=str_replace("!!onclick!!",$onclick,$form_loan_edit);
		
		$form_loan_edit=str_replace("!!idchamp!!",$idchamp,$form_loan_edit);
		$form_loan_edit=str_replace("!!name!!",$name,$form_loan_edit);
		$form_loan_edit=str_replace("!!titre!!",$titre,$form_loan_edit);
		$form_loan_edit=str_replace("!!pond!!",$pond,$form_loan_edit);	
		
		//Liste des types
		$t_list="<select name='type'>\n";
		reset($type_list_empr);
		while (list($key,$val)=each($type_list_empr)) {
			$t_list.="<option value='".$key."'";
			if ($type==$key) $t_list.=" selected";
			$t_list.=">".htmlentities($val,ENT_QUOTES, $charset)."</option>\n";
		}
		$t_list.="</select>\n";
		$form_loan_edit=str_replace("!!type_list!!",$t_list,$form_loan_edit);
		
		//Liste des types de données
		$t_list="<select name='datatype'>\n";
		reset($datatype_list);
		while (list($key,$val)=each($datatype_list)) {
			$t_list.="<option value='".$key."'";
			if ($datatype==$key) $t_list.=" selected";
			$t_list.=">".htmlentities($val,ENT_QUOTES, $charset)."</option>\n";
		}
		$t_list.="</select>\n";
		$form_loan_edit=str_replace("!!datatype_list!!",$t_list,$form_loan_edit);
		
		$form_loan_edit=str_replace("!!options!!",$options,$form_loan_edit);
		
		if ($obligatoire==1) $f_obligatoire="checked"; else $f_obligatoire="";
		$form_loan_edit=str_replace("!!obligatoire_checked!!",$f_obligatoire,$form_loan_edit);
		
		if ($filters==1) $f_filters="checked"; else $f_filters="";
		$form_loan_edit=str_replace("!!filters_checked!!",$f_filters,$form_loan_edit);
		
		foreach ( $this->option_visibilite as $key => $value ) {
       		$form_loan_edit=str_replace("!!".$key."_visible!!",$value,$form_loan_edit);
		}
		
		$form_loan_edit=str_replace("!!ordre!!",$ordre,$form_loan_edit);
		$form_loan_edit=str_replace("!!base_url!!",$this->base_url,$form_loan_edit);
		
		echo $form_loan_edit;
	}

	//Validation du formulaire de création
	function check_form() {
		global $action,$idchamp;
		global $name,$titre,$type,$_for,$multiple,$obligatoire,$exclusion,$msg,$search,$export,$filters,$pond,$opac_sort;
		//Vérification conformité du champ name
		if (!preg_match("/^[A-Za-z][A-Za-z0-9_]*$/",$name)) $this->make_error(sprintf($msg["parperso_check_field_name"],$name));
		//On vérifie que le champ name ne soit pas déjà existant
		if ($action == "update") $requete="select idchamp from ".$this->prefix."_custom where name='$name' and idchamp<>$idchamp";
		else $requete="select idchamp from ".$this->prefix."_custom where name='$name'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat) > 0) $this->make_error(sprintf($msg["parperso_check_field_name_already_used"],$name));
		if ($titre=="") $titre=$name;
		if ($_for!=$type) $this->make_error($msg["parperso_check_type"]);
		if ($multiple=="") $multiple=0;
		if ($obligatoire=="") $obligatoire=0;
		if($search=="") $search=0;
		if($export=="") $export=0;
		if($filters=="") $filters=0;
		if($exclusion=="") $exclusion=0;
		if($pond=="") $pond=1;
		if($opac_sort=="") $opac_sort=0;
	}
	
	//Validation des valeurs des champs soumis lors de la saisie d'une fichie emprunteur ou autre...
	function check_submited_fields() {
		global $chk_list_empr,$charset;
		
		$nberrors=0;
		$this->error_message="";
		
		if (!$this->no_special_fields) {
			reset($this->t_fields);
			while (list($key,$val)=each($this->t_fields)) {
				$check_message="";
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$key]["OPTIONS"], "OPTIONS");
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["PREFIX"]=$this->prefix;
				$field["FILTERS"]=$this->t_fields[$key]["FILTERS"];
				eval("\$field[VALUES]=\$".$val["NAME"].";");
				eval($chk_list_empr[$this->t_fields[$key]["TYPE"]]."(\$field,\$check_message);");
				if ($check_message!="") {
					$nberrors++;
					$this->error_message.="<p>".$check_message."</p>";
				}
			}
		}
		return $nberrors;
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
				$field["FILTERS"]=$this->t_fields[$key]["FILTERS"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$key]["OPTIONS"], "OPTIONS");
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				eval("\$aff=".$aff_list_empr[$this->t_fields[$key][TYPE]]."(\$field,\$check_scripts);");
				$t["AFF"]=$aff;
				$t["NAME"]=$field["NAME"];
				$t["MANDATORY"]=$field["MANDATORY"];
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
				$t["TITRE"]="<b>".htmlentities($val["TITRE"],ENT_QUOTES,$charset)." : </b>";
				$t["OPAC_SHOW"]=$val["OPAC_SHOW"];
				if(!isset(static::$fields[$this->prefix][$key])){
					static::$fields[$this->prefix][$key]=array();
					static::$fields[$this->prefix][$key]["ID"]=$key;
					static::$fields[$this->prefix][$key]["NAME"]=$this->t_fields[$key]["NAME"];
					static::$fields[$this->prefix][$key]["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
					static::$fields[$this->prefix][$key]["FILTERS"]=$this->t_fields[$key]["FILTERS"];
					static::$fields[$this->prefix][$key]["ALIAS"]=$this->t_fields[$key]["TITRE"];
					static::$fields[$this->prefix][$key]["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
					static::$fields[$this->prefix][$key]["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$key]["OPTIONS"], "OPTIONS");
					static::$fields[$this->prefix][$key]["VALUES"]=$values;
					static::$fields[$this->prefix][$key]["PREFIX"]=$this->prefix;
				}
				$aff=$val_list_empr[$this->t_fields[$key]["TYPE"]](static::$fields[$this->prefix][$key],$this->values[$key]);
				
				if (is_array($aff) && $aff[ishtml] == true)$t["AFF"] = $aff["value"];
				else $t["AFF"]=htmlentities($aff,ENT_QUOTES,$charset);
				$t["NAME"]=$field["NAME"];
				$t["ID"]=$field["ID"];
				$perso["FIELDS"][]=$t;
			}
		}
		return $perso;
	}
	
	function get_formatted_output($values,$field_id) {
		global $val_list_empr,$charset;
		
		$field=array();
		$field["ID"]=$field_id;
		$field["NAME"]=$this->t_fields[$field_id]["NAME"];
		$field["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
		$field["FILTERS"]=$this->t_fields[$field_id]["FILTERS"];
		$field["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
		$field["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
		$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$field_id]["OPTIONS"], "OPTIONS");
		$field["VALUES"]=$values;
		$field["PREFIX"]=$this->prefix;
		$aff=$val_list_empr[$this->t_fields[$field_id]["TYPE"]]($field,$values);
		if(is_array($aff)) return $aff['withoutHTML']; 
		else return $aff;
	}

	//Gestion des actions en administration
	function proceed() {
		global $action;
		global $name,$titre,$type,$datatype,$_options,$multiple,$obligatoire,$search,$export,$filters,$exclusion,$ordre,$idchamp,$id,$pond,$opac_sort;
		
		switch ($action) {
			case "nouv":
				$this->show_edit_form();
				break;
			case "edit":
				$this->show_edit_form($id);
				break;
			case "create":
				$this->check_form();
				$requete="select max(ordre) from ".$this->prefix."_custom";
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)!=0)
					$ordre=pmb_mysql_result($resultat,0,0)+1;
				else
					$ordre=1;
	
				$requete="insert into ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=$ordre, search=$search, export=$export, filters=$filters, exclusion_obligatoire=$exclusion, opac_sort=$opac_sort ";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "update":
				$this->check_form();
				$requete="update ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=$ordre, search=$search, export=$export, filters=$filters, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort where idchamp=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "up":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(ordre) as ordre from ".$this->prefix."_custom where ordre<$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				if ($ordre_max) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_max limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_max=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_max."' where idchamp=$id";
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_max;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "down":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select min(ordre) as ordre from ".$this->prefix."_custom where ordre>$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_min=@pmb_mysql_result($resultat,0,0);
				if ($ordre_min) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_min limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_min=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_min."' where idchamp=$id";
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_min;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "delete":
				$requete="delete from ".$this->prefix."_custom where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			default:
				echo $this->show_field_list();
		}
	}
}