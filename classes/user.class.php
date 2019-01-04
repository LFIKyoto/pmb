<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user.class.php,v 1.14 2018-08-27 14:34:57 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/marc_table.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/actes.class.php");
require_once($class_path."/suggestions_map.class.php");
require_once($class_path."/lignes_actes_statuts.class.php");
require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/onto/onto_handler.class.php");
require_once($class_path."/onto/onto_root_ui.class.php");
require_once($class_path."/onto/common/onto_common_ui.class.php");
require_once($class_path."/onto/common/onto_common_controler.class.php");
require_once($class_path."/onto/skos/onto_skos_concept_ui.class.php");
require_once($class_path."/onto/skos/onto_skos_controler.class.php");
require_once($class_path."/onto/onto_param.class.php");
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');
require_once($class_path.'/notice_relations_collection.class.php');
require_once($class_path.'/printer/raspberry.class.php');

class user {
	
	protected $userid;
	
	public function __construct($userid=0) {
		$this->userid = $userid+0;
		$this->fetch_data();
	}
	
	public static function get_field_selector($field, $selector) {
		global $msg;
		//TODO : Tester les deux points finaux du $msg
		return 
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;</div>
			<div class='colonne_suite'>".$selector."</div>
		</div>\n";
	}
	
	public static function get_field_radio($field, $selected) {
		global $msg;
		return
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."</div>\n
			<div class='colonne_suite'>
				".$msg[39]." <input type='radio' name='form_$field' value='0' ".(!$selected ? "checked='checked'" : "")." />
				".$msg[40]." <input type='radio' name='form_$field' value='1' ".($selected ? "checked='checked'" : "")." />
			</div>
		</div>\n";
	}
	
	public static function get_field_checkbox($field, $checked) {
		global $msg;
		return 
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."</div>\n
			<div class='colonne_suite'>
				<input type='checkbox' class='checkbox' ".($checked==1 ? "checked='checked'" : "")." value='1' name='form_$field' />
			</div>
		</div>\n";
	}
	
	public static function get_form($id=0, $caller='') {
		global $msg, $charset;
		global $base_path, $class_path, $include_path;
		global $deflt_concept_scheme;
		global $pmb_droits_explr_localises;
		global $deflt_docs_location;
		global $cms_active;
		global $pmb_scan_request_activate;
		global $pmb_printer_name;
		
		//A verifier : si ce sont bien des globales
		global $explr_invisible;
		global $explr_visible_unmod;
		global $explr_visible_mod;
		global $user_lang;
		
		$requete = "SELECT username, nom, prenom, rights, userid, user_lang, ";
		$requete .="nb_per_page_search, nb_per_page_select, nb_per_page_gestion, ";
		$requete .="param_popup_ticket, param_sounds, ";
		$requete .="user_email, user_alert_resamail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num FROM users WHERE userid='$id' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$nbr = pmb_mysql_num_rows($res);
		if ($nbr) {
			$usr=pmb_mysql_fetch_object($res);
		} else die ('Unknown user');
		
		$requete_param = "SELECT * FROM users WHERE userid='$id' LIMIT 1 ";
		$res_param = pmb_mysql_query($requete_param);
		$field_values = pmb_mysql_fetch_row( $res_param );
		
		$param_user="<div class='row'><b>".$msg["1500"]."</b></div>\n";
		$deflt_user="<div class='row'><b>".$msg["1501"]."</b></div>\n";
		$speci_user="";
		$deflt3user="";
		$value_user="";
		
		$i = 0;
		while ($i < pmb_mysql_num_fields($res_param)) {
			$field = pmb_mysql_field_name($res_param, $i) ;
			$field_deb = substr($field,0,6);
			switch ($field_deb) {
				case "deflt_" :
					if ($field=="deflt_styles") {
						$deflt_user_style=static::get_field_selector($field, make_user_style_combo($field_values[$i]));
					} elseif ($field=="deflt_docs_location") {
						//visibilité des exemplaires
						if ($pmb_droits_explr_localises && $usr->explr_visible_mod) $where_clause_explr = "idlocation in (".$usr->explr_visible_mod.") and";
						else $where_clause_explr = "";
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "account_calcule_section(this);", $field_values[$i], "", "","","",0);
						$deflt_user .= static::get_field_selector($field, $selector);
						//localisation de l'utilisateur pour le calcul de la section
						$location_user_section = $field_values[$i];
					} elseif ($field=="deflt_collstate_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_resas_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_docs_section") {
						// calcul des sections
						$selector="";
						if (!$location_user_section) $location_user_section = $deflt_docs_location;
						if ($pmb_droits_explr_localises && $usr->explr_visible_mod) $where_clause_explr = "where idlocation in (".$usr->explr_visible_mod.")";
						else $where_clause_explr = "";
						$rqtloc = "SELECT idlocation FROM docs_location $where_clause_explr order by location_libelle";
						$resloc = pmb_mysql_query($rqtloc);
						while ($loc=pmb_mysql_fetch_object($resloc)) {
							$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
							$result = pmb_mysql_query($requete);
							$nbr_lignes = pmb_mysql_num_rows($result);
							if ($nbr_lignes) {
								if ($loc->idlocation==$location_user_section ) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
								else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
								$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
								while($line = pmb_mysql_fetch_row($result)) {
									$selector .= "<option value='$line[0]' ";
									$selector .= (($line[0] == $field_values[$i]) ? "selected='selected' >" : '>');
									$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
								}
								$selector .= '</select></div>';
							}
						}
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_upload_repertoire") {
						$selector = "";
						$requpload = "select repertoire_id, repertoire_nom from upload_repertoire";
						$resupload = pmb_mysql_query($requpload);
						$selector .=  "<div id='upload_section'>";
						$selector .= "<select name='form_deflt_upload_repertoire'>";
						$selector .= "<option value='0'>".$msg['upload_repertoire_sql']."</option>";
						while(($repupload = pmb_mysql_fetch_object($resupload))){
							$selector .= "<option value='".$repupload->repertoire_id."' ";
							if ($field_values[$i] == $repupload->repertoire_id ) {
								$selector .= "selected='selected' ";
							}
							$selector .= ">";
							$selector .= htmlentities($repupload->repertoire_nom,ENT_QUOTES,$charset)."</option>";
						}
						$selector .=  "</select></div>";
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif($field=="deflt_import_thesaurus"){
						$requete="select * from thesaurus order by 2";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if($nb_liste) {
							$selector = "
								<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$selector.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$selector.="selected='selected' " ;
								}
								$selector.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$selector.="</select>" ;
							$deflt_user .= static::get_field_selector($field, $selector);
						}
					} elseif ($field=="deflt_short_loan_activate") {
						$deflt_user.=static::get_field_checkbox($field, $field_values[$i]);
					} elseif ($field=="deflt_camera_empr") {
						$deflt_user.=static::get_field_checkbox($field, $field_values[$i]);
					} elseif ($field=="deflt_cashdesk"){
						$requete="select * from cashdesk order by cashdesk_name";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste) {
							$selector= "<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_object( $resultat_liste );
								$selector.= "<option value=\"".$liste_values->cashdesk_id."\" " ;
								if ($field_values[$i]==$liste_values->cashdesk_id) {
									$selector.="selected" ;
								}
								$selector.=">".htmlentities($liste_values->cashdesk_name,ENT_QUOTES,$charset)."</option>\n" ;
								$j++;
							}
							$selector.="</select>";
							$deflt_user .= static::get_field_selector($field, $selector);
						}
					} elseif(($field=="deflt_concept_scheme")){
						$deflt_user.="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."</div>\n";
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
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_notice_is_new") {
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_bulletinage_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user.=static::get_field_selector($field, $selector);
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
						$deflt_user.=static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_cms_article_statut") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$publications_states = new cms_editorial_publications_states();
							$selector = "
							<select name=\"form_".$field."\">
								".$publications_states->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}  elseif ($field=="deflt_cms_article_type") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$types = new cms_editorial_types('article');
							$types->get_types();
							$selector = "
							<select name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}  elseif ($field=="deflt_cms_section_type") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$types = new cms_editorial_types('section');
							$types->get_types();
							$selector = "
							<select name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}elseif ($field=="deflt_scan_request_status") {
						if($pmb_scan_request_activate){
							$request_status_instance = new scan_request_admin_status();
							$selector = "
							<select name=\"form_".$field."\">
								".$request_status_instance->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					} elseif ($field=="deflt_catalog_expanded_caddies") {
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_notice_replace_links") {
						$selector = "<input type='radio' name='form_".$field."' value='0' ".($field_values[$i]==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
							<br /><input type='radio' name='form_".$field."' value='1' ".($field_values[$i]==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
							<br /><input type='radio' name='form_".$field."' value='2' ".($field_values[$i]==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced'];
						$deflt_user.=static::get_field_selector($field, $selector);
					}elseif ($field=="deflt_printer") {
						if (substr($pmb_printer_name,0,9) == 'raspberry') {
							$selector = "
								<select name=\"form_".$field."\">
									".raspberry::get_selector_options($field_values[$i])."
								</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					} else {
						$deflt_table = substr($field,6);
						switch ($deflt_table) {
							case 'integration_notice_statut' :
								$deflt_table = 'notice_statut';
								break;
							case 'serials_docs_type' :
								$deflt_table = 'docs_type';
								break;
						}
						switch($field) {
							case "deflt_entites":
								$requete="select id_entite, raison_sociale from ".$deflt_table." where type_entite='1' order by 2 ";
								break;
							case "deflt_exercices":
								$requete="select id_exercice, libelle from ".$deflt_table." order by 2 ";
								break;
							case "deflt_rubriques":
								$requete="select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from ".$deflt_table." join budgets on num_budget=id_budget order by 2 ";
								break;
							case "deflt_notice_statut_analysis":
								$requete="(select 0,'".addslashes($msg[$field."_parent"])."') union ";
								$requete.="(select id_notice_statut, gestion_libelle from notice_statut order by 2)";
								break;
							default :
								$requete="select * from ".$deflt_table." order by 2";
								break;
						}
		
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste) {
							$selector="
							<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$selector.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$selector.="selected='selected' " ;
								}
								$selector.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$selector.="</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}
					break;
		
				case "param_" :
					if ($field=="param_allloc") {
						$param_user_allloc="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."</div>\n
					<div class='colonne_suite'>
					<input type='checkbox' class='checkbox'";
						if ($field_values[$i]==1) $param_user_allloc.=" checked";
						$param_user_allloc.=" value='1' name='form_$field'></div></div>\n" ;
					} else {
						$param_user.="<div class='row'>";
						//if (strpos($msg[$field],'<br />')) $param_user .= "<br />";
						$param_user.="<input type='checkbox' class='checkbox'";
						if ($field_values[$i]==1) $param_user.=" checked";
						$param_user.=" value='1' name='form_$field'>\n
						$msg[$field]
						</div>\n";
					}
					break ;
		
				case "value_" :
					switch ($field) {
						case "value_deflt_fonction" :
							$flist=new marc_list('function');
							$f=$flist->table[$field_values[$i]];
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
							$msg[$field]&nbsp;:&nbsp;</div>\n
							<div class='colonne_suite'>
							<input type='text' class='saisie-30emr' id='form_value_deflt_fonction_libelle' name='form_value_deflt_fonction_libelle' value='".htmlentities($f,ENT_QUOTES, $charset)."' />
					<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=".$caller."&p1=form_value_deflt_fonction&p2=form_value_deflt_fonction_libelle', 'selector')\" />
							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_fonction'].value='';this.form.elements['form_value_deflt_fonction_libelle'].value='';return false;\" />
							<input type='hidden' name='form_value_deflt_fonction' id='form_value_deflt_fonction' value=\"$field_values[$i]\" />
							</div></div><br />";
							break;
						case "value_deflt_lang" :
							$llist=new marc_list('lang');
							$l=$llist->table[$field_values[$i]];
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
							$msg[$field]&nbsp;:&nbsp;</div>\n
							<div class='colonne_suite'>
							<input type='text' class='saisie-30emr' id='form_value_deflt_lang_libelle' name='form_value_deflt_lang_libelle' value='".htmlentities($l,ENT_QUOTES, $charset)."' />
					<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=".$caller."&p1=form_value_deflt_lang&p2=form_value_deflt_lang_libelle', 'selector')\" />
							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_lang'].value='';this.form.elements['form_value_deflt_lang_libelle'].value='';return false;\" />
							<input type='hidden' name='form_value_deflt_lang' id='form_value_deflt_lang' value=\"$field_values[$i]\" />
							</div></div><br />";
							break;
						case "value_deflt_relation" :
						case "value_deflt_relation_serial" :
						case "value_deflt_relation_bulletin" :
						case "value_deflt_relation_analysis" :
							$selector = notice_relations::get_selector('form_'.$field, $field_values[$i]);
							$value_user.=static::get_field_selector($field, $selector);
							break;
						case "value_deflt_module" :
							$arrayModules = array(
							'dashboard'=>$msg['dashboard'],
							'circu'=>$msg['5'],
							'catal'=>$msg['93'],
							'autor'=>$msg['132'],
							'edit'=>$msg['1100'],
							'dsi'=>$msg['dsi_droit'],
							'acquis'=>$msg['acquisition_droit'],
							'admin'=>$msg['7'],
							'cms'=>$msg['cms_onglet_title'],
							'account'=>$msg['933'],
							'fiches'=>$msg['onglet_fichier']
							);
							$selector = "<select name='form_".$field."'>";
							foreach ($arrayModules as $k=>$v) {
								$selector.="<option value='".$k."'";
								if($k == $field_values[$i]){
									$selector.=" selected";
								}
								$selector.=">".$v."</option>";
							}
							$selector.="</select>";
							$value_user.=static::get_field_selector($field, $selector);
							break;
						default :
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
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
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste==0) {
							$deflt_user.="" ;
						} else {
							$deflt_user.="
						<div class='row userParam-row'><div class='colonne60'>".
								$msg[$field]."&nbsp;:&nbsp;</div>\n";
							$deflt_user.= "
						<div class='colonne_suite'>
						<select class='saisie-30em' name=\"form_".$field."\">";
		
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$deflt_user.="selected='selected' " ;
								}
								$deflt_user.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$deflt_user.="</select></div></div>!!param_allloc!!<br />\n" ;
						}
					} else {
						$deflt_table = substr($field,6);
						$requete="select * from ".$deflt_table." order by 2 ";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste==0) {
							$deflt_user.="" ;
						} else {
							$deflt_user.="
							<div class='row userParam-row'><div class='colonne60'>".
									$msg[$field]."&nbsp;:&nbsp;</div>\n";
							$deflt_user.= "
							<div class='colonne_suite'>
								<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$deflt_user.="selected='selected' " ;
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
							$deflt_user.=static::get_field_selector($field, $combo);
							break;
						case "xmlta_doctype_serial" :
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
								break;
						case "xmlta_doctype_bulletin" :
						case "xmlta_doctype_analysis" :
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "","0",$msg[$field."_parent"]);
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
								break;
						case "xmlta_doctype_scan_request_folder_record" :
							if($pmb_scan_request_activate){
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
							}
							break;
						default :
							$deflt_table = substr($field,6);
							$select_doc = new marc_select("$deflt_table", "form_".$field, $field_values[$i], "");
							$deflt_user.=static::get_field_selector($field, $select_doc->display);
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
							$q.="select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from rubriques join budgets on num_budget=id_budget order by 2 ";
							break;
						case "deflt3type_produit":
							$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
							$q.="select id_produit, libelle from types_produits order by 2 ";
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
						$r=pmb_mysql_query($q);
						$nb=pmb_mysql_num_rows($r);
						while($row=pmb_mysql_fetch_row($r)) {
							$t[$row[0]]=$row[1];
						}
					}
					if (count($t)) {
						$deflt3user.="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt3user.= "<div class='colonne_suite'><select class='saisie-30em' name=\"form_".$field."\">";
						foreach($t as $k=>$v) {
							$deflt3user.="<option value=\"".$k."\" " ;
							if ($field_values[$i]==$k) {
								$deflt3user.="selected='selected' " ;
							}
							$deflt3user.=">".htmlentities($v, ENT_QUOTES, $charset)."</option>\n" ;
						}
						$deflt3user.="</select></div></div><br />\n";
					}
					break;
		
				case "speci_" :
					$speci_func = substr($field, 6);
					eval('$speci_user.= get_'.$speci_func.'($id, $field_values, $i, \''.$caller.'\');');
					break;
		
				case "explr_" :
					${$field}=$field_values[$i];
					break;
				default :
					break ;
			}
			$i++;
		}
		if($caller == 'userform') {
			//visibilité des exemplaires
			if ($pmb_droits_explr_localises) {
				$explr_tab_invis=explode(",",$explr_invisible);
				$explr_tab_unmod=explode(",",$explr_visible_unmod);
				$explr_tab_modif=explode(",",$explr_visible_mod);
			
				$visibilite_expl_user="
				<div class='row'><hr /></div>
				<div class='row'>
					<div class='colonne3'>".$msg["expl_visibilite"]."&nbsp;:&nbsp;</div>
					<div class='colonne_suite'>&nbsp;</div>
				</div>\n";
				$requete_droits_expl="select idlocation, location_libelle from docs_location order by location_libelle";
				$resultat_droits_expl=pmb_mysql_query($requete_droits_expl);
				$temp="";
				while ($j=pmb_mysql_fetch_array($resultat_droits_expl)) {
					$temp.=$j["idlocation"].",";
					$visibilite_expl_user.= "
					<div class='row'>
						<div class='colonne3 align_right'>".$j["location_libelle"]." : </div>
						<div class='colonne_suite'>&nbsp;<select name=\"form_expl_visibilite_".$j["idlocation"]."\">
					";
					$as_invis = array_search($j["idlocation"],$explr_tab_invis);
					$as_unmod = array_search($j["idlocation"],$explr_tab_unmod);
					$as_mod = array_search($j["idlocation"],$explr_tab_modif);
					$visibilite_expl_user .="\n<option value='explr_invisible' ".($as_invis!== FALSE && $as_invis!== NULL?"selected='selected' ":"").">".$msg["explr_invisible"]."</option>";
					if (($as_mod!== FALSE && $as_mod !== NULL)||($as_unmod!== FALSE && $as_unmod !== NULL)||($as_invis!== FALSE && $as_invis !== NULL)) {
						$visibilite_expl_user .="\n<option value='explr_visible_unmod' ".($as_unmod!== FALSE && $as_unmod!== NULL?"selected='selected' ":"").">".$msg["explr_visible_unmod"]."</option>";
					} else {
						$visibilite_expl_user .="\n<option value='explr_visible_unmod' selected='selected' >".$msg["explr_visible_unmod"]."</option>";
					}
					$visibilite_expl_user .="\n<option value='explr_visible_mod' ".($as_mod!== FALSE && $as_mod!== NULL?"selected='selected' ":"").">".$msg["explr_visible_mod"]."</option>";
					$visibilite_expl_user.="</select></div></div>\n" ;
				}
				pmb_mysql_free_result($resultat_droits_expl);
			
				if ((!$explr_invisible)&&(!$explr_visible_unmod)&&(!$explr_visible_mod)) {
					$rqt="UPDATE users SET explr_invisible=0,explr_visible_mod=0,explr_visible_unmod='".substr($temp,0,strlen($temp)-1)."' WHERE userid=$id";
					@pmb_mysql_query($rqt);
				}
			
				$deflt_user .=$visibilite_expl_user;
			} //fin visibilité des exemplaires
		}
		
		$param_default="
		<div class='row'><hr /></div>
		".$param_user."
		<div class='row'><hr /></div>
		".str_replace("!!param_allloc!!",$param_user_allloc,$deflt_user)."
		<br />
		<div class='row'><hr /></div>
		".$value_user.
		($caller == 'userform' ? "<div class='row'><hr /></div>
		$deflt_user_style
		<br />" : "");
		if ($speci_user || $deflt3user) {
			$param_default.= "<div class='row'><hr /></div>";
			$param_default.=$deflt3user;
			$param_default.=$speci_user;
			$param_default.= "<div class='row'></div>";
		}
		return $param_default;
	}
	
	public static function get_param($id, $field) {
		$id += 0;
		$param = '';
		if($id) {
			$query = "SELECT ".$field." FROM users WHERE userid='".$id."' ";
			$result = pmb_mysql_query($query);
			$param = pmb_mysql_result($result, 0, 0);
		}
		return $param;
	}
	
} // fin de déclaration de la classe user