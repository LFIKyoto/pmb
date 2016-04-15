<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.php,v 1.57.2.2 2015-12-10 15:20:28 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "PREF_AUTH|ADMINISTRATION_AUTH";  
$base_title = "\$msg[933]";    
require_once ("$base_path/includes/init.inc.php");  

// modules propres à account.php ou à ses sous-modules
include("$include_path/account.inc.php");
include("$include_path/templates/account.tpl.php");
require_once("$include_path/user_error.inc.php");
require_once("$class_path/thesaurus.class.php");
require_once("$base_path/admin/users/users_func.inc.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/suggestions_map.class.php");
require_once("$class_path/lignes_actes_statuts.class.php");
require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");

require_once("$class_path/onto/common/onto_common_uri.class.php");
require_once("$class_path/onto/onto_store_arc2.class.php");
require_once("$class_path/onto/onto_handler.class.php");
require_once("$class_path/onto/onto_root_ui.class.php");
require_once("$class_path/onto/common/onto_common_ui.class.php");
require_once("$class_path/onto/common/onto_common_controler.class.php");
require_once("$class_path/onto/skos/onto_skos_concept_ui.class.php");
require_once("$class_path/onto/skos/onto_skos_controler.class.php");
require_once("$class_path/onto/onto_param.class.php");

print $menu_bar;
print $extra2;
print $account_layout;

if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

if(!$modified) {
	$user_params = get_account_info(SESSlogin);
	// constitution des paramètres utilisateurs
	$requete_param = "SELECT * FROM users WHERE userid='$PMBuserid' LIMIT 1 ";
	$res_param = pmb_mysql_query($requete_param, $dbh);
	$field_values = pmb_mysql_fetch_row( $res_param );
	
	$param_user="<div class='row'><b>".$msg["1500"]."</b></div>\n";
	$deflt_user="<div class='row'><b>".$msg["1501"]."</b></div>\n";

	$i = 0;
	while ($i < pmb_mysql_num_fields($res_param)) {
		$field = pmb_mysql_field_name($res_param, $i) ;
		$field_deb = substr($field,0,6);
		switch ($field_deb) {

			case "deflt_" :
				if ($field=="deflt_styles") {
					$deflt_user_style="
						<div class='colonne60'><div class='row'>".
						$msg[$field]."&nbsp;:&nbsp;</div></div>\n
						<div class='colonne_suite'><div_class='row'>"			
						.make_user_style_combo($field_values[$i]).
						"</div></div>\n";
				} elseif ($field=="deflt_docs_location") {
					//visibilité des exemplaires
					if ($pmb_droits_explr_localises && $explr_visible_mod) $where_clause_explr = "idlocation in (".$explr_visible_mod.") and";
					else $where_clause_explr = "";
					$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "account_calcule_section(this);", $field_values[$i], "", "","","",0);
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} elseif ($field=="deflt_collstate_location") {
					$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} elseif ($field=="deflt_resas_location") {
					$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} elseif ($field=="deflt_docs_section") {
					// calcul des sections
					$selector="";
					if ($pmb_droits_explr_localises && $explr_visible_mod) $where_clause_explr = "where idlocation in (".$explr_visible_mod.")";
					else $where_clause_explr = "";
					$rqtloc = "SELECT idlocation FROM docs_location $where_clause_explr order by location_libelle";
					$resloc = pmb_mysql_query($rqtloc, $dbh);
					while ($loc=pmb_mysql_fetch_object($resloc)) {
						$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
						$result = pmb_mysql_query($requete, $dbh);
						$nbr_lignes = pmb_mysql_num_rows($result);
						if ($nbr_lignes) {			
							if ($loc->idlocation==$deflt_docs_location ) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
							else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
							$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>\r\n";
							while($line = pmb_mysql_fetch_row($result)) {
								$selector .= "<option value='$line[0]'";
								$line[0] == $deflt_docs_section ? $selector .= ' SELECTED>' : $selector .= '>';
					 			$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>\r\n';
								}                                         
							$selector .= '</select></div>';
							}                 
						}
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} elseif ($field=="deflt_upload_repertoire") { 
					$selector = "";
					$req = "select repertoire_id, repertoire_nom from upload_repertoire";
					$res = pmb_mysql_query($req, $dbh);
					$selector .=  "<div id='upload_section'>";
					$selector .= "<select name='form_deflt_upload_repertoire'>";
					$selector .= "<option value='0'>".$msg[upload_repertoire_sql]."</option>";
					while(($rep = pmb_mysql_fetch_object($res))){
						$selector .= "<option value='".$rep->repertoire_id."'";
						$selector .= (($deflt_upload_repertoire == $rep->repertoire_id) ? 'SELECTED' : '') . ">";
						$selector .= htmlentities($rep->repertoire_nom,ENT_QUOTES,$charset)."</option>";
					}
					$selector .=  "</select></div>";
					$deflt_user.="
						<div class='row'>
							<div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;
							</div>
							<div class='colonne_suite'>".$selector."
							</div>
						</div>";		
			 	} elseif($field=="deflt_import_thesaurus"){
					$requete="select * from thesaurus order by 2";
					$resultat_liste=pmb_mysql_query($requete,$dbh);
					$nb_liste=pmb_mysql_num_rows($resultat_liste);
					if ($nb_liste==0) {
						$deflt_user.="" ;
					} else {
						$deflt_user.="
							<div class='row'>
								<div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;
								</div>\n
								<div class='colonne_suite'>
									<select class='saisie-30em' name=\"form_".$field."\">";
						$j=0;
						while ($j<$nb_liste) {
							$liste_values = pmb_mysql_fetch_row( $resultat_liste );
							$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
							if ($field_values[$i]==$liste_values[0]) {
								$deflt_user.="selected" ;
							}
							$deflt_user.=">".$liste_values[1]."</option>\n" ;
							$j++;
						}
						$deflt_user.="</select>
								</div>
							</div>\n" ;
					} 
			 	} elseif ($field=="deflt_short_loan_activate") {
					$deflt_user.="<div class='row'><div class='colonne60'>".$msg[$field]."</div>\n
						<div class='colonne_suite'>
						<input type='checkbox' class='checkbox'";
					if ($field_values[$i]==1) $deflt_user.=" checked"; 
					$deflt_user.=" value='1' name='form_$field'></div></div>\n" ;
				} elseif ($field=="deflt_cashdesk"){
					$requete="select * from cashdesk order by cashdesk_name";
					$resultat_liste=pmb_mysql_query($requete,$dbh);
					$nb_liste=pmb_mysql_num_rows($resultat_liste);
					if ($nb_liste==0) {
						$deflt_user.="" ;
					} else {
						$deflt_user.="
							<div class='row'>
								<div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;
								</div>\n
								<div class='colonne_suite'>
									<select class='saisie-30em' name=\"form_".$field."\">";
						$j=0;
						while ($j<$nb_liste) {
							$liste_values = pmb_mysql_fetch_object( $resultat_liste );
							$deflt_user.="<option value=\"".$liste_values->cashdesk_id."\" " ;
							if ($field_values[$i]==$liste_values->cashdesk_id) {
								$deflt_user.="selected" ;
							}
							$deflt_user.=">".htmlentities($liste_values->cashdesk_name,ENT_QUOTES,$charset)."</option>\n" ;
							$j++;
						}
						$deflt_user.="</select>
								</div>
							</div>\n" ;
					}
				}elseif(($field=="deflt_concept_scheme")){
					$deflt_user.="<div class='row'><div class='colonne60'>".$msg[$field]."</div>\n";
					$deflt_user.="<div class='colonne_suite'>";
					
					
					$onto_store_config = array(
							/* db */
							'db_name' => DATA_BASE,
							'db_user' => USER_NAME,
							'db_pwd' => USER_PASS,
							'db_host' => SQL_SERVER,
							/* store */
							'store_name' => 'ontology',
							/* stop after 100 errors */
							'max_errors' => 100,
							'store_strip_mb_comp_str' => 0
					);
					$data_store_config = array(
							/* db */
							'db_name' => DATA_BASE,
							'db_user' => USER_NAME,
							'db_pwd' => USER_PASS,
							'db_host' => SQL_SERVER,
							/* store */
							'store_name' => 'rdfstore',
							/* stop after 100 errors */
							'max_errors' => 100,
							'store_strip_mb_comp_str' => 0
					);
					
					$tab_namespaces=array(
							"skos"	=> "http://www.w3.org/2004/02/skos/core#",
							"dc"	=> "http://purl.org/dc/elements/1.1",
							"dct"	=> "http://purl.org/dc/terms/",
							"owl"	=> "http://www.w3.org/2002/07/owl#",
							"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
							"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
							"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
							"pmb"	=> "http://www.pmbservices.fr/ontology#"
					);
					
					
					$onto_handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel','http://www.w3.org/2004/02/skos/core#ConceptScheme');
					
					$params=new onto_param();
					$params->concept_scheme=$deflt_concept_scheme;
					$onto_controler=new onto_skos_controler($onto_handler, $params);
					
					$deflt_user.=onto_skos_concept_ui::get_scheme_list_selector($onto_controler, $params,true,'','form_deflt_concept_scheme');
					$deflt_user.="</div></div>\n" ;
					
				} elseif ($field=="deflt_notice_replace_keep_categories") {
					$deflt_user.="<div class='row'><div class='colonne60'>".$msg[$field]."</div>\n
						<div class='colonne_suite'>
							".$msg[39]." <input type='radio' name='form_$field' value='0' ".(!$field_values[$i] ? "checked='checked'" : "")." />
							".$msg[40]." <input type='radio' name='form_$field' value='1' ".($field_values[$i] ? "checked='checked'" : "")." />
						</div></div>\n" ;
				} elseif ($field=="deflt_notice_is_new") {
					$deflt_user.="<div class='row'><div class='colonne60'>".$msg[$field]."</div>\n
						<div class='colonne_suite'>
							".$msg[39]." <input type='radio' name='form_$field' value='0' ".(!$field_values[$i] ? "checked='checked'" : "")." />
							".$msg[40]." <input type='radio' name='form_$field' value='1' ".($field_values[$i] ? "checked='checked'" : "")." />
						</div></div>\n" ;
				} elseif ($field=="deflt_bulletinage_location") {
					$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} elseif ($field=="deflt_agnostic_warehouse") {
					$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
					$conn->get_sources();
					$selector = "<select name=\"form_".$field."\">
						<option value='0' ".(!$field_values[$i] ? "selected='selected'" : "").">".$msg['caddie_save_to_warehouse_none']."</option>";
					if(is_array($conn->sources)) {
						foreach($conn->sources as $key_source=>$source) {
							$selector .= "<option value='".$key_source."' ".($field_values[$i] == $key_source ? "selected='selected'" : "").">".htmlentities($source['NAME'],ENT_QUOTES,$charset)."</option>";
						}
					}
					$selector .= "</select>";
					$deflt_user.="
						<div class='row'><div class='colonne60'>".
						$msg[$field]."&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>"			
						.$selector.
						"</div></div>\n";
				} else {
					$deflt_table = substr($field,6);
					if($deflt_table == "integration_notice_statut") $deflt_table= "notice_statut";
					$requete="select * from ".$deflt_table." order by 2";
					$resultat_liste=pmb_mysql_query($requete,$dbh);
					$nb_liste=pmb_mysql_num_rows($resultat_liste);
					if ($nb_liste==0) {
						$deflt_user.="" ;
					} else {
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>						
							<select class='saisie-30em' name=\"form_".$field."\">";
									
						$j=0;
						while ($j<$nb_liste) {
							$liste_values = pmb_mysql_fetch_row( $resultat_liste );
							$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
							if ($field_values[$i]==$liste_values[0]) {
								$deflt_user.="selected" ;
							}
							$deflt_user.=">".$liste_values[1]."</option>\n" ;
							$j++;
						}
						$deflt_user.="</select></div></div><br />\n" ;
					}
				} // fin else
				break;
			case "param_" :
				if ($field=="param_allloc") {
					$param_user_allloc="<div class='row'><div class='colonne60'>".$msg[$field]."</div>\n
						<div class='colonne_suite'>
						<input type='checkbox' class='checkbox'";
					if ($field_values[$i]==1) $param_user_allloc.=" checked"; 
					$param_user_allloc.=" value='1' name='form_$field'></div></div>\n" ;
				} else {
					$param_user.="<div class='row'><input type='checkbox' class='checkbox'";
					if ($field_values[$i]==1) $param_user.=" checked"; 
					$param_user.=" value='1' name='form_$field'>" ;
					$param_user.="&nbsp; $msg[$field]</div>" ;
				}
				break ;
			case "value_" :
				switch ($field) {
					case "value_deflt_fonction" :
						$flist=new marc_list('function');
						$f=$flist->table[$field_values[$i]];
						$value_user.="<div class='row'><div class='colonne60'>					
						$msg[$field]&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>
						<input type='text' class='saisie-30emr' id='form_value_deflt_fonction_libelle' name='form_value_deflt_fonction_libelle' value='".htmlentities($f,ENT_QUOTES, $charset)."' />
						<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=account_form&p1=form_value_deflt_fonction&p2=form_value_deflt_fonction_libelle', 'select_func0', 400, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" />
						<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_fonction'].value='';this.form.elements['form_value_deflt_fonction_libelle'].value='';return false;\" />
						<input type='hidden' name='form_value_deflt_fonction' id='form_value_deflt_fonction' value=\"$field_values[$i]\" />
						</div></div><br />";
						break;
					case "value_deflt_lang" :
						$llist=new marc_list('lang');
						$l=$llist->table[$field_values[$i]];
						$value_user.="<div class='row'><div class='colonne60'>					
						$msg[$field]&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>
						<input type='text' class='saisie-30emr' id='form_value_deflt_lang_libelle' name='form_value_deflt_lang_libelle' value='".htmlentities($l,ENT_QUOTES, $charset)."' />
						<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=account_form&p1=form_value_deflt_lang&p2=form_value_deflt_lang_libelle', 'select_lang', 400, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" />
						<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_lang'].value='';this.form.elements['form_value_deflt_lang_libelle'].value='';return false;\" />
						<input type='hidden' name='form_value_deflt_lang' id='form_form_value_deflt_lang' value=\"$field_values[$i]\" />
						</div></div><br />";
						break;
					case "value_deflt_relation" :
					case "value_deflt_relation_serial" :
					case "value_deflt_relation_bulletin" :
					case "value_deflt_relation_analysis" :
						$value_user.="<div class='row'><div class='colonne60'>					
						$msg[$field]&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>";
						
						$liste_type_relation_down=new marc_list("relationtypedown");
						$liste_type_relation_up=new marc_list("relationtypeup");
						$liste_type_relation_both=array();
						
						foreach($liste_type_relation_up->table as $key_up=>$val_up){
							foreach($liste_type_relation_down->table as $key_down=>$val_down){
								if($val_up==$val_down){
									$liste_type_relation_both[$key_down]=$val_down;
									unset($liste_type_relation_down->table[$key_down]);
									unset($liste_type_relation_up->table[$key_up]);
								}
							}
						}
					
						$value_user.="<select onchange='' name='form_".$field."' size='1'>
						<optgroup class='erreur' label='$msg[notice_lien_montant]'>";
						
						foreach($liste_type_relation_up->table as $key=>$val){
							if($key.'-up'==$field_values[$i]){
								$value_user.='<option  style="color:#000000" value="'.$key.'-up" selected="selected">'.$val.'</option>';
							}else{
								$value_user.='<option  style="color:#000000" value="'.$key.'-up">'.$val.'</option>';
							}
						}
						$value_user.="</optgroup>
						<optgroup class='erreur' label='$msg[notice_lien_descendant]'>";
						
						foreach($liste_type_relation_down->table as $key=>$val){
							if($key.'-down'==$field_values[$i]){
								$value_user.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
							}else{
								$value_user.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
							}
						}
						$value_user.="</optgroup>
						<optgroup class='erreur' label='$msg[notice_lien_symetrique]'>";
						
						foreach($liste_type_relation_both as $key=>$val){
							if($key.'-down'==$field_values[$i]){
								$value_user.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
							}else{
								$value_user.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
							}
						}
						$value_user.="</optgroup>
						</select>";
						$value_user.="</div></div><br />";
						break;
					default :
						$value_user.="<div class='row'><div class='colonne60'>					
						$msg[$field]&nbsp;:&nbsp;</div>\n
						<div class='colonne_suite'>
						<input type='text' class='saisie-20em' name='form_$field' value='".htmlentities($field_values[$i],ENT_QUOTES, $charset)."' />
						</div></div><br />";
						break;
				}
				break ;
			case "deflt2" :
				if ($field=="deflt2docs_location") {
					// localisation des lecteurs
					$deflt_table = substr($field,6);
					$requete="select * from ".$deflt_table." order by 2";
					$resultat_liste=pmb_mysql_query($requete,$dbh);
					$nb_liste=pmb_mysql_num_rows($resultat_liste);
					if ($nb_liste==0) {
						$deflt_user.="" ;
					} else {
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>						
							<select class='saisie-30em' name=\"form_".$field."\">";
									
						$j=0;
						while ($j<$nb_liste) {
							$liste_values = pmb_mysql_fetch_row( $resultat_liste );
							$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
							if ($field_values[$i]==$liste_values[0]) {
								$deflt_user.="selected" ;
							}
							$deflt_user.=">".$liste_values[1]."</option>\n" ;
							$j++;
						}
						$deflt_user.="</select></div></div>!!param_allloc!!<br />\n" ;
					}
				} else {
					$deflt_table = substr($field,6);
					$requete="select * from ".$deflt_table."  order by 2 ";
					$resultat_liste=pmb_mysql_query($requete,$dbh);
					$nb_liste=pmb_mysql_num_rows($resultat_liste);
					if ($nb_liste==0) {
						$deflt_user.="" ;
					} else {
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>						
							<select class='saisie-30em' name=\"form_".$field."\">";
									
						$j=0;
						while ($j<$nb_liste) {
							$liste_values = pmb_mysql_fetch_row( $resultat_liste );
							$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
							if ($field_values[$i]==$liste_values[0]) {
								$deflt_user.="selected" ;
							}
							$deflt_user.=">".$liste_values[1]."</option>\n" ;
							$j++;
						}
						$deflt_user.="</select></div></div>\n" ;
					}
				}
				break;
			case "xmlta_" :
				switch($field) {
					case "xmlta_indexation_lang" :
						$langues = new XMLlist("$include_path/messages/languages.xml");
						$langues->analyser();
						$clang = $langues->table;
						
						$combo = "<select name='form_".$field."' id='form_".$field."' class='saisie-20em' >";
						if(!$field_values[$i]) $combo .= "<option value='' selected>--</option>";
						else $combo .= "<option value='' >--</option>";
						while(list($cle, $value) = each($clang)) {
							// arabe seulement si on est en utf-8
							if (($charset != 'utf-8' and $user_lang != 'ar') or ($charset == 'utf-8')) {
								if(strcmp($cle, $field_values[$i]) != 0) $combo .= "<option value='$cle'>$value ($cle)</option>";
								else $combo .= "<option value='$cle' selected>$value ($cle)</option>";
							}
						}
						$combo .= "</select>";
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>$combo</div></div>\n" ;
						break;
					case "xmlta_doctype_serial" :
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>";
						$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
						$deflt_user.= $select_doc->display ;
						$deflt_user.="</div></div>\n" ;
						break;
					case "xmlta_doctype_bulletin" :
					case "xmlta_doctype_analysis" :
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>";
						$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "","0",$msg[$field."_parent"]);
						$deflt_user.= $select_doc->display ;
						$deflt_user.="</div></div>\n" ;
						break;
					default :
						$deflt_table = substr($field,6);
						$deflt_user.="
							<div class='row'><div class='colonne60'>".
							$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt_user.= "
							<div class='colonne_suite'>";
						$select_doc = new marc_select("$deflt_table", "form_".$field, $field_values[$i], "");
						$deflt_user.= $select_doc->display ;
						$deflt_user.="</div></div>\n" ;
						break;
				}
			case "deflt3" :
				$q='';
				$t=array();
				switch($field) {
					case "deflt3bibli":
						$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
						$q.="select id_entite, raison_sociale from entites where type_entite='1' order by 2 ";
						break;
					case "deflt3exercice":
						$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
						$q.="select id_exercice, libelle from exercices order by 2 ";
						break;
					case "deflt3rubrique":
						$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
						$q.="select id_rubrique, concat(budgets.libelle,':',rubriques.libelle) from rubriques join budgets on num_budget=id_budget order by 2 ";
						break;
					case "deflt3dev_statut":
						$t=actes::getStatelist(TYP_ACT_DEV);
						break;
					case "deflt3cde_statut":
						$t=actes::getStatelist(TYP_ACT_CDE);
						break;
					case "deflt3liv_statut":
						$t=actes::getStatelist(TYP_ACT_LIV);
						break;
					case "deflt3fac_statut":
						$t=actes::getStatelist(TYP_ACT_FAC);
						break;
					case "deflt3sug_statut":
						$m=new suggestions_map();
						$t=$m->getStateList();
						break;
					case 'deflt3lgstatcde':
					case 'deflt3lgstatdev':	
						$q=lgstat::getList('QUERY');
						break;
					case 'deflt3receptsugstat':
						$m=new suggestions_map();
						$t=$m->getStateList('ORDERED',TRUE);
						break;
				}
				if($q) {
					$r=pmb_mysql_query($q, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$q."<br />");
					$nb=pmb_mysql_num_rows($r);
					while($row=pmb_mysql_fetch_row($r)) {
						$t[$row[0]]=$row[1];
					}
				}
				if (count($t)) {
					$deflt3user.="<div class='row'><div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;</div>\n";
					$deflt3user.= "<div class='colonne_suite'><select class='saisie-30em' name=\"form_".$field."\">";
					foreach($t as $k=>$v) {
						$deflt3user.="<option value=\"".$k."\" " ;
						if ($field_values[$i]==$k) {
							$deflt3user.="selected" ;
						}
						$deflt3user.=">".htmlentities($v, ENT_QUOTES, $charset)."</option>\n" ;
					}
					$deflt3user.="</select></div></div><br />\n";
				}
				break;
			case "speci_" :
				$speci_func = substr($field, 6);
				eval('$speci_user.= get_'.$speci_func.'($PMBuserid, $field_values, $i, \'account_form\');'); 
				break;
			default :
				break ;
		}
			
		$i++;
	}

	$param_default="
		<div class='row'><hr /></div>
			$param_user
		<div class='row'><hr /></div>
			".str_replace("!!param_allloc!!",$param_user_allloc,$deflt_user)."
		<br />
		<div class='row'><hr /></div>
			$value_user";
	if ($speci_user || $deflt3user) {
		$param_default.= "<div class='row'><hr /></div>";
		$param_default.=$deflt3user;
		$param_default.=$speci_user;
		$param_default.= "<div class='row'></div>";	
	}
	$account_form = str_replace('!!all_user_param!!', $param_default, $account_form);
	// fin gestion des paramètres personalisés du user
	
	$account_form = str_replace('!!combo_user_style!!', make_user_style_combo($stylesheet), $account_form);
	$account_form = str_replace('!!combo_user_lang!!', make_user_lang_combo($user_params->user_lang), $account_form);
	$account_form = str_replace('!!nb_per_page_search!!', $user_params->nb_per_page_search, $account_form);
	$account_form = str_replace('!!nb_per_page_select!!', $user_params->nb_per_page_select, $account_form);
	$account_form = str_replace('!!nb_per_page_gestion!!', $user_params->nb_per_page_gestion, $account_form);
	print $account_form;

} else {
		
	// code de mise à jour
	// constitution des variables MySQL
	// mise à jour de la date d'update 
	
	$names[] = 'last_updated_dt';
	$values[] = "'".today()."'";
	
	$names[] = 'user_lang';
	$values[] = "'$user_lang'";
	
	if ($form_pwd) {
		$names[] = 'pwd';
		$values[] = "password('$form_pwd')";
		$names[] = 'user_digest';
		$values[]= "'".md5(SESSlogin.":".md5($pmb_url_base).":".$form_pwd)."'";
	}
	
	if($form_nb_per_page_search >= 1) {
		$names[] = 'nb_per_page_search';
		$values[] = "'$form_nb_per_page_search'";
	}
	
	if($form_nb_per_page_select >= 1) {
		$names[] = 'nb_per_page_select';
		$values[] = "'$form_nb_per_page_select'";
	}
	
	if($form_nb_per_page_gestion >= 1) {
		$names[] = 'nb_per_page_gestion';
		$values[] = "'$form_nb_per_page_gestion'";
	}
	
	if(strcmp($form_style, $stylesheet)) {
		$names[] .= 'deflt_styles';
		$values[] .= "'$form_style'";
	}
	
			
	/* insérer ici la maj des param et deflt */
	
	//maj thesaurus par defaut en session
	if ($form_deflt_thesaurus) thesaurus::setSessionThesaurusId($form_deflt_thesaurus);
			
	$requete_param = "SELECT * FROM users WHERE userid='$PMBuserid' LIMIT 1 ";
	$res_param = pmb_mysql_query($requete_param, $dbh);
	$field_values = pmb_mysql_fetch_row( $res_param );
	$i = 0;
	while ($i < pmb_mysql_num_fields($res_param)) {
		$field = pmb_mysql_field_name($res_param, $i) ;
		$field_deb = substr($field,0,6);
		switch ($field_deb) {
			case "deflt_" :
				if ($field == "deflt_styles") {
					$dummy[$i+8]=$field."='".$form_style."'";
				} elseif ($field == "deflt_docs_section") {
					$formlocid="f_ex_section".$form_deflt_docs_location ;
					$dummy[$i+8]=$field."='".$$formlocid."'";
				} else {
					$var_form = "form_".$field;
					$dummy[$i+8]=$field."='".$$var_form."'";
				}
				break;
			case "deflt2" :
				$var_form = "form_".$field;
				$dummy[$i+8]=$field."='".$$var_form."'";
				break ;
			case "param_" :
				$var_form = "form_".$field;
				$dummy[$i+8]=$field."='".$$var_form."'";
				break ;
			case "value_" :
				$var_form = "form_".$field;
				$dummy[$i+8]=$field."='".$$var_form."'";
				break ;
			case "xmlta_" :
				$var_form = "form_".$field;
				$dummy[$i+8]=$field."='".$$var_form."'";
				break ;
			case "deflt3" :
				$var_form = "form_".$field;
				$dummy[$i+8]=$field."='".$$var_form."'";
				break ;
			case "speci_" :
				$speci_func = substr($field, 6);
				eval('$dummy[$i+8].= set_'.$speci_func.'();');
				break;
			default :
				break ;
		}
		$i++;
	}

	if(!empty($dummy)) {
		$set = join($dummy, ", ");
		$set = " , ".$set ;
	} else $set = "" ;
		
	if(sizeof($names) == sizeof($values)) {
		while(list($cle, $valeur) = each($names)) {
			$n_values ? $n_values .= ", $valeur=${values[$cle]}" : $n_values = "$valeur=${values[$cle]}";
	    }
		$requete = "UPDATE users SET $n_values $set , last_updated_dt=curdate() WHERE username='".SESSlogin."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if($result) {
			$loc = "index.php" ;
			if (SESSrights & ADMINISTRATION_AUTH) 
				$loc="admin.php";
			if (SESSrights & EDIT_AUTH) 
				$loc="edit.php";
			if (SESSrights & AUTORITES_AUTH) 
				$loc="autorites.php";
			if (SESSrights & CATALOGAGE_AUTH) 
				$loc="catalog.php";
			if (SESSrights & CIRCULATION_AUTH) 
				$loc="circ.php";
			print $msg["937"]." <!-- back to main page --> <script type=\"text/javascript\"> document.location=\"./".$loc."\"; </script>";
		} else {
			// c'est parti en vrac : erreur MySQL
			warning($msg["281"], $msg["936"]);
		}
	}
}
	
print "</div></div>";
print $account_layout_end;
print $extra;
print $extra_info;
print $footer;

pmb_mysql_close($dbh);
