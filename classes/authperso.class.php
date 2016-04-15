<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.class.php,v 1.12 2015-06-23 14:35:34 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $javascript_path; // pas compris pourquoi, sinon fait planter connector_out
require_once($javascript_path."/misc.inc.php");
require_once($include_path."/templates/authperso.tpl.php");
require_once($include_path."/templates/parametres_perso.tpl.php");
require_once($class_path."/custom_parametres_perso.class.php");
require_once("$class_path/aut_link.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/audit.class.php");
@ini_set('zend.ze1_compatibility_mode',0);
require_once($include_path."/h2o/h2o.php");


class authperso {
	var $id=0;
	var $info=array();
	var $elt_id=0;
	
	function authperso($id=0,$id_auth=0) {
		global $dbh;
		if(!$id && $id_auth){			
			$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id_auth;
			$res = pmb_mysql_query($req,$dbh);
			if(($r=pmb_mysql_fetch_object($res))) {
				$id=$r->authperso_authority_authperso_num;
			}
		}
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	function fetch_data() {		
		$this->info=array();
		$this->info['fields']=array();
		if(!$this->id) return;
		
		$req="select * from authperso where id_authperso=". $this->id;		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_authperso;	
			$this->info['name']= $r->authperso_name;
			$this->info['onglet_num']= $r->authperso_notice_onglet_num;			
			$this->info['isbd_script']= $r->authperso_isbd_script;			
			$this->info['opac_search']= $r->authperso_opac_search;			
			$this->info['opac_multi_search']= $r->authperso_opac_multi_search;				
			$this->info['gestion_search']= $r->authperso_gestion_search;			
			$this->info['gestion_multi_search']= $r->authperso_gestion_multi_search;		
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
	
	function get_info_fields($id=0){
		global $dbh;
		$info= array();
		if($id){
			$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
			$res = pmb_mysql_query($req,$dbh);
			if(($r=pmb_mysql_fetch_object($res))) {
				$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id,$option_navigation,$option_visibilite);
				$fields=$p_perso->get_out_values($id);
				$authperso_fields=$p_perso->values;				
			}
		}
		foreach($this->info['fields'] as $field){
			$info[$field['id']]['id']= $field['id'];
			$info[$field['id']]['name']= $field['name'];
			$info[$field['id']]['label']= $field['label'];
			$info[$field['id']]['type']= $field['type'];
			$info[$field['id']]['ordre']= $field['ordre'];
			$info[$field['id']]['search']=$field['search'];
			$info[$field['id']]['pond']=$field['pond'];
			$info[$field['id']]['obligatoire']=$field['obligatoire'];
			$info[$field['id']]['export']=$field['export'];
			$info[$field['id']]['multiple']=$field['multiple'];
			$info[$field['id']]['opac_sort']=$field['opac_sort'];
			$info[$field['id']]['code_champ']=$this->id;
			$info[$field['id']]['code_ss_champ']=$field['id'];
			$info[$field['id']]['values']= $authperso_fields[$field['name']]['values'];		
			$info[$field['id']]['all_format_values']= $authperso_fields[$field['name']]['all_format_values'];				
		}
		return $info;
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
	
	function get_search_list($tpl_auth,$restriction){	
		global $msg,$charset,$dbh;
		
		$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
		$req .= " order by id_authperso_authority DESC $restriction";
		$res = pmb_mysql_query($req, $dbh);
		while(($r=pmb_mysql_fetch_object($res))) {
			$id=$r->id_authperso_authority;
			$isbd=$this->get_isbd($id);
			
			$tpl=$tpl_auth;
			$tpl = str_replace ('!!isbd_addslashes!!', htmlentities(addslashes($isbd),ENT_QUOTES, $charset), $tpl);
			$tpl = str_replace ('!!isbd!!', htmlentities($isbd), $tpl);
			$tpl = str_replace ('!!auth_id!!', $id, $tpl);			
			$auth_lines.=$tpl;
		}
		return $auth_lines;
	}
	
	function get_list() {		
		global $msg,$charset,$dbh;
		global $user_query, $user_input,$page,$nbr_lignes,$last_param;	
		global $url_base;
		
		$nb_per_page = 10;		
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
		
		$search_word = str_replace('*','%',$user_input);
		if(!($nb_per_page*1)){
			$nb_per_page=$nb_per_page_search;
		}
		if(!$page) $page=1;
		if(!$last_param){
			$debut =($page-1)*$nb_per_page;
			$requete = "SELECT count(1) FROM authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and authperso_authority_authperso_num= ".$this->id;
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			
			$req = "select * from authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by authperso_index_infos_global LIMIT ".$debut.",".$nb_per_page." ";	
		}else{ // les derniers créés
			$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by id_authperso_authority DESC LIMIT $nb_per_page";
		}
		
		$res = pmb_mysql_query($req,$dbh);
		$parity=1;
		while(($r=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even"; else $pair_impair = "odd";				
			$parity += 1;
			$id=$r->id_authperso_authority;
			$this->fetch_data_auth($r->id_authperso_authority);
			$auth_line="";
			//$this->info['fields'][$i]['data'][$id][$field['name']]
			foreach($this->info['fields'] as $field){
				$data_label=$field['data'][$id]['values'][0]['format_value'];
				$auth_line.="
				<td onmousedown=\"document.location='./autorites.php?categ=authperso&sub=authperso_form&id_authperso=".$this->id."&amp;id=$id&amp;user_input=!!user_input_url!!&amp;nbr_lignes=$nbr_lignes&amp;page=$page';\" title='' valign='top'>".
					$data_label
				."</td>";
				
				$auth_line = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)),$auth_line);
			}
			// usage
			$auth_line.="
				<td onmousedown=\"document.location='./catalog.php?categ=search&mode=".($this->id +1000)."&etat=aut_search&aut_type=authperso&authperso_id=".$this->id."&aut_id=$id';\" title='' valign='top'>".
					$this->get_count_notice($id)
				."</td>";
				
			$auth_lines.="
			<tr class='$parity' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\" style=\"cursor: pointer\">
				$auth_line
			</tr>
			";
		}		
		$user_query = str_replace ('!!user_query_title!!', $msg["authperso_search_title"], $user_query);
		$user_query = str_replace ('!!action!!', "./autorites.php?categ=authperso&sub=reach&id_authperso=".$this->id."&id=", $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg["authperso_search_add"] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', './autorites.php?categ=authperso&sub=authperso_form&id_authperso='.$this->id, $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='./autorites.php?categ=authperso&sub=authperso_last&last_param=authperso_last&id_authperso=".$this->id."'>".$msg["authperso_search_last"]."</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		$user_query = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)),$user_query);
	
		if($error){
			$user_query.error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			exit;
		}
		$authperso_list_tpl= "
		$user_query
		<br />
		<br />
		<div class='row'>
			<h3><! --!!nb_autorite_found!!-- >".$msg["authperso_search_found"]." !!cle!! </h3>
			</div>
			<script type='text/javascript' src='./javascript/sorttable.js'></script>
			<table class='sortable'>
				<tr>
				!!th_fields!!
				</tr>
				!!list!!
			</table>
		<div class='row'>
			!!nav_bar!!
		</div>
		";
		
		foreach($this->info['fields'] as $field){
			$th_fields.="<th>".htmlentities($field['label'],ENT_QUOTES,$charset)."</th>";
		}	
		$th_fields.="<th>".htmlentities($msg['authperso_usage'],ENT_QUOTES,$charset)."</th>";
		
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		$authperso_list_tpl=str_replace( "<! --!!nb_autorite_found!!-- >",$nbr_lignes.' ',$authperso_list_tpl);		
		$authperso_list_tpl = str_replace("!!th_fields!!", $th_fields, $authperso_list_tpl);
		$authperso_list_tpl = str_replace("!!cle!!", $user_input, $authperso_list_tpl);
		$authperso_list_tpl = str_replace("!!list!!", $auth_lines, $authperso_list_tpl);
		$authperso_list_tpl = str_replace("!!nav_bar!!", $nav_bar, $authperso_list_tpl);
		
		return $authperso_list_tpl;
	}
	
	function get_list_selector() {
		global $msg,$charset,$dbh;
		global $user_query, $user_input,$page,$nbr_lignes,$last_param;		
		global $callback;
		global $caller;		
		global $base_url;
		
		$nb_per_page = 10;
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
	
		$search_word = str_replace('*','%',$user_input);
		if(!($nb_per_page*1)){
			$nb_per_page=$nb_per_page_search;
		}
		if(!$page) $page=1;
		if(!$last_param){
			$debut =($page-1)*$nb_per_page;
			$requete = "SELECT count(1) FROM authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and authperso_authority_authperso_num= ".$this->id;
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
				
			$req = "select * from authperso_authorities where ( authperso_infos_global like '%".$search_word."%' or authperso_index_infos_global like '%".$user_input."%' ) and  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by authperso_index_infos_global LIMIT ".$debut.",".$nb_per_page." ";
		}else{ // les derniers créés
			$req = "select * from authperso_authorities where  authperso_authority_authperso_num= ".$this->id;
			$req .= " order by id_authperso_authority DESC LIMIT $nb_per_page";
		}
	
		$res = pmb_mysql_query($req,$dbh);
		while(($r=pmb_mysql_fetch_object($res))) {
			$id=$r->id_authperso_authority;
			$isbd=$this->get_isbd($id);
			$auth_lines.="<a href='#' onclick=\"set_parent('$caller', '$id', '".htmlentities(addslashes($isbd),ENT_QUOTES, $charset)."','$callback')\">".
					htmlentities($isbd,ENT_QUOTES, $charset)."</a><br />";			
		}
		
		//$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($base_url, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		$authperso_list_tpl= "		
			<br />
				$auth_lines
			<div class='row'>&nbsp;<hr /></div><div align='center'>			
				$nav_bar
			</div>
		";
	
		return $authperso_list_tpl;
	}	
	
	function get_isbd($id){
		global $dbh;

		$req="select * from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id;
		$res = pmb_mysql_query($req,$dbh);
		if(($r=pmb_mysql_fetch_object($res))) {			
			$p_perso=new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num);
			$fields=$p_perso->get_out_values($id);			
			$authperso_fields=$p_perso->values;			
			if($r->authperso_isbd_script){
				$index_concept = new index_concept($id, TYPE_AUTHPERSO);	
				$authperso_fields['index_concepts']=$index_concept->get_data();
				$isbd=H2o::parseString($r->authperso_isbd_script)->render($authperso_fields);
			}else{
				foreach ($authperso_fields as $field){					
					$isbd.=$field[values][0][format_value].".  ";
				}
			}	
		}
		return $isbd;
	}
	
	function get_count_notice($id){
		global $dbh;
			
		$req="select count(1) from notices_authperso where notice_authperso_authority_num=". $id;
		return pmb_mysql_result(pmb_mysql_query($req, $dbh), 0, 0);					
	}
		
	function get_notices($id){
		global $dbh;
		
		$list=array();	
		$req="select notice_authperso_notice_num from notices_authperso where notice_authperso_authority_num=". $id;
		$res = pmb_mysql_query($req,$dbh);
		if(($r=pmb_mysql_fetch_object($res))) {	
			$list[]=$r>notice_authperso_notice_num;
		}	
		return $list;		
	}
		
	function get_form($id) {
		global $msg,$charset,$authperso_form;	
		global $user_query, $user_input,$page,$nbr_lignes;	
		global $pmb_type_audit;
		global $thesaurus_concepts_active;
		
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id,$option_navigation,$option_visibilite);
		$authperso_fields=$p_perso->show_editable_fields($id);
		
		$authperso_field_tpl="	
		<div class='row'>
			<label class='etiquette'>!!titre!!</label>
		</div>
		<div class='row'>
			!!aff!!
		</div>";
		foreach($authperso_fields['FIELDS'] as $field){
			//printr($field);
			$field_tpl=$authperso_field_tpl;			
			$field_tpl = str_replace("!!titre!!", $field['TITRE'], $field_tpl);
			$field_tpl = str_replace("!!aff!!", $field['AFF'], $field_tpl);
			$tpl.=$field_tpl;
		}	
		$button_remplace = "<input type='button' class='bouton' value='$msg[158]' onclick='unload_off();document.location=\"./autorites.php?categ=authperso&sub=replace&id_authperso=".$this->id."&id=$id\"'>";			
		$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=".($this->id + 1000)."&etat=aut_search&aut_type=authperso&aut_id=$id\"'>";
		
		if ($pmb_type_audit && $id)
			$bouton_audit= "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=".($this->id + 1000)."&object_id=".$id."', 'audit_popup', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />&nbsp;";

		$aut_link= new aut_link($this->id+1000,$id);
		$authperso_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_authperso') , $authperso_form);

		// Indexation concept
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$authperso_form = str_replace('<!-- index_concept_form -->', $index_concept->get_form('saisie_authperso'), $authperso_form);
		}
		$authperso_form = str_replace("!!list_field!!", $tpl, $authperso_form);
		if(!$id){
			$authperso_form = str_replace("!!libelle!!", $msg['authperso_form_titre_new'], $authperso_form);
			$authperso_form = str_replace("!!delete!!", "", $authperso_form);
			$authperso_form = str_replace("!!remplace!!", "", $authperso_form);
			$authperso_form = str_replace("!!voir_notices!!", "", $authperso_form);
			$authperso_form = str_replace("!!audit_bt!!", "", $authperso_form);
		}else{
			$authperso_form = str_replace("!!libelle!!", $msg['authperso_form_titre_edit'], $authperso_form);
			$authperso_form = str_replace("!!delete!!", "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">", $authperso_form);
			$authperso_form = str_replace("!!remplace!!", $button_remplace, $authperso_form);
			$authperso_form = str_replace("!!voir_notices!!", $button_voir, $authperso_form);
			$authperso_form = str_replace("!!audit_bt!!", $bouton_audit, $authperso_form);
		}
		$authperso_form = str_replace("!!action!!", "./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id."&id=$id", $authperso_form);
		$authperso_form = str_replace("!!id_authperso!!", $this->id, $authperso_form);
		$authperso_form = str_replace("!!id!!", $id, $authperso_form);
		$authperso_form = str_replace("!!page!!", $page, $authperso_form);
		$authperso_form = str_replace("!!nbr_lignes!!", $nbr_lignes, $authperso_form);
		$authperso_form = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$authperso_form);
		$authperso_form = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)),$authperso_form);
		return $authperso_form;
	}
	
	function get_form_select($id,$base_url) {
		global $msg,$charset,$authperso_form_select;	
		global $user_query, $user_input,$page,$nbr_lignes;	
		global $pmb_type_audit;
		global $thesaurus_concepts_active;
		
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id,"./autorites.php?categ=authperso&sub=update&id_authperso=".$this->id,$option_navigation,$option_visibilite);
		$authperso_fields=$p_perso->show_editable_fields($id);
		
		$authperso_field_tpl="	
		<div class='row'>
			<label class='etiquette'>!!titre!!</label>
		</div>
		<div class='row'>
			!!aff!!
		</div>";
		foreach($authperso_fields['FIELDS'] as $field){
			//printr($field);
			$field_tpl=$authperso_field_tpl;			
			$field_tpl = str_replace("!!titre!!", $field['TITRE'], $field_tpl);
			$field_tpl = str_replace("!!aff!!", $field['AFF'], $field_tpl);
			$tpl.=$field_tpl;
		}	
		$button_remplace = "<input type='button' class='bouton' value='$msg[158]' onclick='unload_off();document.location=\"./autorites.php?categ=authperso&sub=replace&id_authperso=".$this->id."&id=$id\"'>";			
		$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=".($this->id + 1000)."&etat=aut_search&aut_type=authperso&aut_id=$id\"'>";
		
		if ($pmb_type_audit && $id)
			$bouton_audit= "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=".($this->id + 1000)."&object_id=".$id."', 'audit_popup', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />&nbsp;";
		
		$aut_link= new aut_link($this->id+1000,$id);
		$authperso_form_select = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_authperso') , $authperso_form_select);

		// Indexation concept
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$authperso_form_select = str_replace('<!-- index_concept_form -->', $index_concept->get_form('saisie_authperso'), $authperso_form_select);
		}
		
		$authperso_form_select = str_replace("!!libelle!!", $msg['authperso_form_titre_new'], $authperso_form_select);
		$authperso_form_select = str_replace("!!list_field!!", $tpl, $authperso_form_select);
		
		$authperso_form_select = str_replace("!!retour!!", "$base_url&action=", $authperso_form_select);
		$authperso_form_select = str_replace("!!action!!", "$base_url&action=update", $authperso_form_select);
		$authperso_form_select = str_replace("!!id_authperso!!", $this->id, $authperso_form_select);
		$authperso_form_select = str_replace("!!id!!", $id, $authperso_form_select);
		return $authperso_form_select;
	}
		
	function update_from_form($id=0) {
		global $dbh;
		global $thesaurus_concepts_active;
		
		$id+=0;
		if(!$id){
			$requete="insert into authperso_authorities set authperso_authority_authperso_num=".$this->id;
			pmb_mysql_query($requete);			
			$id = pmb_mysql_insert_id($dbh);			
			audit::insert_creation ($this->id+1000,$id);			
		}else{			
			audit::insert_modif ($this->id+1000,$id);				
		}
		if(!$id) return;
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$p_perso->rec_fields_perso($id);
		
		$aut_link= new aut_link($this->id+1000,$id);
		$aut_link->save_form();
		
		// Indexation concepts
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($id, TYPE_AUTHPERSO);
			$index_concept->save();
		}
		
		$this->update_global_index($id);
			
	}
	
	function update_global_index($id){
		global $dbh;		
		
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
		$mots_perso=$p_perso->get_fields_recherche($id);
		if($mots_perso) {
			$infos_global.= $mots_perso.' ';
			$infos_global_index.= strip_empty_words($mots_perso).' ';
		}
		$req = "update authperso_authorities set authperso_infos_global='".addslashes($infos_global)."', authperso_index_infos_global='".addslashes(' '.$infos_global_index)."' where id_authperso_authority=$id";
		pmb_mysql_query($req,$dbh);
	}
	
	function reindex_all(){
		global $dbh;	
		
		$req = "select id_authperso_authority from authperso_authorities";
		$res = pmb_mysql_query($req,$dbh);
		while($fiche = pmb_mysql_fetch_object($res)){
			$this->update_global_index($fiche->id_authperso_authority);
		}
	}
	
	function delete($id) {
		global $dbh;	
		
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);	
		$p_perso->delete_values($id);
		// nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_AUTHPERSO);
		$index_concept->delete();
		$req="DELETE FROM authperso_authorities where id_authperso_authority=". $id;		
		$resultat=pmb_mysql_query($req);	
		
		audit::delete_audit($this->id+1000,$id);
	}	
	
	function replace_form($id) {
		global $authperso_replace;
		global $msg;
		global $include_path;
		
		if(!$id ) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, './autorites.php?categ=authperso&sub=&id=');
			return;
		}	
		$authperso_replace=str_replace('!!old_authperso_libelle!!', $this->get_isbd($id), $authperso_replace);
		$authperso_replace=str_replace('!!id!!', $id, $authperso_replace);
		$authperso_replace=str_replace('!!id_authperso!!', $this->id, $authperso_replace);
		
		return $authperso_replace;
	}
	
	function replace($id,$by,$link_save=0) {	
		global $msg;
		global $dbh;
		global $pmb_synchro_rdf;
		
		if (($id == $by) || (!$id) || (!$by))  return $msg[223];
		$aut_link= new aut_link($this->id+1000,$id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to($this->id +1000,$by);
		}
		$aut_link->delete();
		
		// remplacement dans les notices
		$requete = "UPDATE notices_authperso SET notice_authperso_authority_num='$by' WHERE notice_authperso_authority_num='$id' ";
		@pmb_mysql_query($requete, $dbh);
		
		// effacement de 
		$this->delete($id);		
	}	
	function import($data) {
		global $dbh;
		// to do
		
	}
	
	
	function get_ajax_list($user_input){
		global $dbh;
				
		$values=array();
		$search_word = str_replace('*','%',$user_input);
		$req = "select * from authperso_authorities where ( authperso_infos_global like ' ".addslashes($search_word)."%' or authperso_index_infos_global like ' ".addslashes($user_input)."%' ) and  authperso_authority_authperso_num= ".$this->id;
		$req .= " order by authperso_index_infos_global limit 20";
		$res = pmb_mysql_query($req,$dbh);
		while(($r=pmb_mysql_fetch_object($res))) {
			$values[$r->id_authperso_authority]=$this->get_isbd($r->id_authperso_authority);
		}
		return($values);
	}
	
} //authperso class end


class authpersos {	
	var $info=array();
	
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
				// $this->info[$i]= new authperso($r->id_authperso);	
				$authperso= new authperso($r->id_authperso);
				$this->info[$r->id_authperso]=$authperso->get_data();				
				$i++;
			}
		}
	}
	
	function get_data(){
		return($this->info);
	}
		
	function get_all_index_fields(){
		$index_fields=array();
		$req="select id_authperso from authperso ";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$index_fields[]=$r->id_authperso;
			}		
		}	
		return $index_fields;
	}
	
	function get_onglet_list() {
		$onglets=array();
		foreach($this->info as $elt){
			if($elt['onglet_num'])
				$onglets[$elt['onglet_num']][]=$elt;			
		}
		return $onglets;
	}
	
	function get_menu() {
		global $authperso_list_tpl,$authperso_list_line_tpl,$msg;
		
		$line_tpl="<li><a href='./autorites.php?categ=authperso&sub=&id_authperso=!!id_authperso!!&id='>!!name!!</a></li>";
		
		foreach($this->info as $elt){
			$tpl_elt=$line_tpl;
			$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
			$tpl_elt=str_replace('!!id_authperso!!',$elt['id'], $tpl_elt);
			$tpl_list.=$tpl_elt;
		}
		return $tpl_list;
	}	
    	
} // authpersos class end
	
