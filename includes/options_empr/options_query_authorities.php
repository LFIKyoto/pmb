<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_query_authorities.php,v 1.2.10.1 2015-09-10 07:41:58 jpermanne Exp $

//Gestion des options de type text
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once("$include_path/fields_empr.inc.php");

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param["FOR"] = "query_auth";
	$param["METHOD"][0][value] = stripslashes($METHOD);
	$param["DATA_TYPE"][0][value] = $DATA_TYPE;
	
	$param["ID_THES"][0][value] = $ID_THES;
	$param["CATEG_SHOW"][0][value] = $CATEG_SHOW;
	if ($MULTIPLE=="yes")
		$param[MULTIPLE][0][value]="yes";
	else
		$param[MULTIPLE][0][value]="no";
	

	$options = array_to_xml($param, "OPTIONS");
	
	print"
	<script>
	opener.document.formulaire.".$name."_options.value='".str_replace("\n", "\\n", addslashes($options)) ."';
	opener.document.formulaire.".$name."_for.value='query_auth';
	self.close();
	</script>
	";
	
} else {
// Création formulaire
	if($options){
		$param=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options,"OPTIONS");
	}
	if ($param["FOR"] != "query_auth") {
		$param = array();
		$param["FOR"] = "query_auth";
	}
	
	$MULTIPLE=$param[MULTIPLE][0][value];
	
	if($param["METHOD"]["0"]["value"])$method_checked[$param["METHOD"]["0"]["value"]]="checked";
	else $method_checked[1]="checked";
	$data_type_selected[$param["DATA_TYPE"]["0"]["value"]]="selected"; 
	if($param["CATEG_SHOW"]["0"]["value"])$categ_show_checked[$param["CATEG_SHOW"]["0"]["value"]]="checked";
	else $categ_show_checked[0]="checked";
	$multiple_checked="";
	if ($MULTIPLE=="yes") $multiple_checked= "checked";
	
	$sel_thesaurus = '';
	$opt_thesaurus = '';
	if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus		
		$liste_thesaurus = thesaurus::getThesaurusList();
		$sel_thesaurus = "<select class='saisie-20em' id='id_thes' name='ID_THES' >";
	
		//si on vient du form de categories, le choix du thesaurus n'est pas possible
		foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
			$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
			if ($id_thesaurus == $param["ID_THES"][0][value]) $sel_thesaurus.= " selected";
			$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
		}
		$sel_thesaurus.= "<option value=0 ";
		if ($param["ID_THES"][0][value] == 0) $sel_thesaurus.= "selected ";
		$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
		$sel_thesaurus.= "</select>&nbsp;";
	}
	$opt_thesaurus = "<input type='radio' name='CATEG_SHOW' value='0' ".$categ_show_checked[0].">".$msg["cp_auth_show_all"];
	$opt_thesaurus .= "<br><input type='radio' name='CATEG_SHOW' value='1' ".$categ_show_checked[1].">".$msg["cp_auth_show_last"];
	
	//Formulaire	
	$form="
	<h3>".$msg[procs_options_param].$name."</h3><hr />
	<form class='form-$current_module' name='formulaire' action='options_query_authorities.php' method='post'>
	<h3>".$type_list[$type]."</h3>
	<div class='form-contenu'>
	<input type='hidden' name='first' value='1'>
	<input type='hidden' name='name' value='".htmlentities(	$name,ENT_QUOTES,$charset)."'>
	<table class='table-no-border' width=100%>	
		<tr><td>".$msg['parperso_include_option_methode']."</td><td>
		<table width=100% valign='center'>
			<tr><td><center>".$msg['parperso_include_option_selectors_id']."
			<br />
			<input type='radio' name='METHOD' value='1' ".$method_checked[1].">
			</center></td>
			<td><center>".$msg['parperso_include_option_selectors_label']."
			<br />
			<input type='radio' name='METHOD' value='2' ".$method_checked[2].">
			</center></td></tr>
		</table></td></tr>
	
		<tr><td>".$msg['include_option_type_donnees']."
		</td>
		<td>
		<select name='DATA_TYPE'>
			<option value='1' ".$data_type_selected[1]." >".$msg['133']."</option>
			<option value='2' ".$data_type_selected[2]." >".$msg['134']."</option>
			<option value='3' ".$data_type_selected[3]." >".$msg['135']."</option>
			<option value='4' ".$data_type_selected[4]." >".$msg['136']."</option>
			<option value='5' ".$data_type_selected[5]." >".$msg['137']."</option>
			<option value='6' ".$data_type_selected[6]." >".$msg['333']."</option>
			<option value='7' ".$data_type_selected[7]." >".$msg['indexint_menu']."</option>
			<option value='8' ".$data_type_selected[8]." >".$msg['titre_uniforme_search']."</option>
		</select>
		$sel_thesaurus
		<br>$opt_thesaurus
		</td>
		
				
		
		</tr>
		<tr>
			<td>".$msg[procs_options_liste_multi]."</td>
			<td><input type='checkbox' value='yes' name='MULTIPLE' $multiple_checked></td>
		</tr>
	</table>
	
	</div>
	<input class='bouton' type='submit' value='".$msg[77]."'>
	</form>
	</body>
	</html>
	";
	print $form;
}

