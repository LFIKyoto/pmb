<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_search_view_search.class.php,v 1.23 2015-06-08 09:09:12 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_search_view_search extends cms_module_common_view{
	protected $cadre_parent;
	
	public function __construct($id=0){
		parent::__construct($id+0);
	}
	
	public function get_form(){
		$form ="
		<div class='row'>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_help'>".$this->format_text($this->msg['cms_module_search_view_help'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_help' value='1' ".($this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_help_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_help' value='0' ".(!$this->parameters['help'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_help_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_title'>".$this->format_text($this->msg['cms_module_search_view_title'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_search_view_title' value='".($this->parameters['title'] ? htmlentities($this->parameters['title'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_link_search_advanced'>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_search_view_link_search_advanced' value='1' ".($this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_yes'])."
					&nbsp;<input type='radio' name='cms_module_search_view_link_search_advanced' value='0' ".(!$this->parameters['link_search_advanced'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_search_view_input_placeholder'>".$this->format_text($this->msg['cms_module_search_view_input_placeholder'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_search_view_input_placeholder' value='".($this->parameters['input_placeholder'] ? htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset) : "")."'/>
				</div>
			</div>
		</div>
		".parent::get_form();
		return $form;
	}
	
	public function save_form(){
		global $cms_module_search_view_help;
		global $cms_module_search_view_title;
		global $cms_module_search_view_link_search_advanced;
		global $cms_module_search_view_input_placeholder;
		
		$this->parameters['help'] = $cms_module_search_view_help+0;
		$this->parameters['title'] = stripslashes($cms_module_search_view_title);
		$this->parameters['link_search_advanced'] = $cms_module_search_view_link_search_advanced+0;
		$this->parameters['input_placeholder'] = stripslashes($cms_module_search_view_input_placeholder);

		return parent::save_form();
	}
	
	public function render($datas){
		global $base_path,$include_path,$opac_autolevel2;
		global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher,$opac_modules_search_titre_uniforme;
		global $opac_modules_search_collection,$opac_modules_search_subcollection,$opac_modules_search_category,$opac_modules_search_indexint;
		global $opac_modules_search_keywords,$opac_modules_search_abstract,$opac_modules_search_docnum,$opac_simple_search_suggestions;
		global $dest,$user_query,$charset;
		//juste une searchbox...
		if(count($datas) == 1){
			if($datas[0]['page']>0){
				$action = $base_path."/index.php?lvl=cmspage&pageid=".$datas[0]['page'];
			}else{
				if ($opac_autolevel2==2) {
					$action = $base_path."/index.php?lvl=more_results&autolevel1=1";
				} else {
					$action = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
				}
			}
			$onsubmit = "if (".$this->get_module_dom_id()."_searchbox.user_query.value.length == 0) { ".$this->get_module_dom_id()."_searchbox.user_query.value='*'; return true; }";
		}else{
			if ($opac_autolevel2==2) {
				$action = $base_path."/index.php?lvl=more_results&autolevel1=1";
			} else {
				$action = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
			}
			$onsubmit = "if (".$this->get_module_dom_id()."_searchbox.user_query.value.length == 0) { ".$this->get_module_dom_id()."_searchbox.user_query.value='*';}".$this->get_module_dom_id()."_change_dest();";
		}
		if ($opac_modules_search_title==2) $look["look_TITLE"]=1;
		if ($opac_modules_search_author==2) $look["look_AUTHOR"]=1 ;
		if ($opac_modules_search_publisher==2) $look["look_PUBLISHER"] = 1 ; 
		if ($opac_modules_search_titre_uniforme==2) $look["look_TITRE_UNIFORME"] = 1 ; 
		if ($opac_modules_search_collection==2) $look["look_COLLECTION"] = 1 ;	
		if ($opac_modules_search_subcollection==2) $look["look_SUBCOLLECTION"] = 1 ;
		if ($opac_modules_search_category==2) $look["look_CATEGORY"] = 1 ;
		if ($opac_modules_search_indexint==2) $look["look_INDEXINT"] = 1 ;
		if ($opac_modules_search_keywords==2) $look["look_KEYWORDS"] = 1 ;
		if ($opac_modules_search_abstract==2) $look["look_ABSTRACT"] = 1 ;
		$look["look_ALL"] = 1 ;
		if ($opac_modules_search_docnum==2) $look["look_DOCNUM"] = 1;
		$html = "
			<form method='post' class='searchbox' action='".$action."' name='".$this->get_module_dom_id()."_searchbox' ".($onsubmit!= "" ? "onsubmit=\"".$onsubmit."\"" : "").">
				";
		foreach($look as $looktype=>$lookflag) { $html.="
				<input type='hidden' value='1' name='$looktype'>"; 
		}
		
		if ($this->parameters['title']) {
			$html.="
			<h4 class='searchbox_title'>".htmlentities($this->parameters['title'],ENT_QUOTES,$charset)."</h4>";
		}
		
		if($opac_simple_search_suggestions){
			$html.= "
				<script type='text/javascript' src='$include_path/javascript/ajax.js'></script>
				<input type='text' name='user_query' id='user_query_lib_2' value='".stripslashes(htmlentities($user_query,ENT_QUOTES,$charset))."' expand_mode='1' completion='suggestions' disableCompletion='false' word_only='no' placeholder='".stripslashes(htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset))."'/>
				<script type='text/javascript'>
					function toggleCompletion(destValue){
						if(destValue!='0'){
							document.getElementById('user_query_lib_2').setAttribute('disableCompletion','true');
						}else{
							document.getElementById('user_query_lib_2').setAttribute('disableCompletion','false');
						}
					}
					ajax_parse_dom();
				</script>";
		}else{
			$html.="
				<input type='text' name='user_query' value='".stripslashes(htmlentities($user_query,ENT_QUOTES,$charset))."' placeholder='".stripslashes(htmlentities($this->parameters['input_placeholder'],ENT_QUOTES,$charset))."'/>";
		}
		
		$html.="
				<input class='bouton' type='submit' value='".$this->format_text($this->msg['cms_module_search_button_label'])."' />";
		if ($this->parameters['help']) {
			$html.="
				<input class='bouton' type='button' onclick='window.open(\"./help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' value='".$this->format_text($this->msg['cms_module_search_help'])."'>";
		}
		if(count($datas) >1){
			$html.= "<br/>";
			for($i=0 ; $i<count($datas) ; $i++){
				$checked ="";
				if($dest){
					if($datas[$i]['page'] == $dest){
						$checked= " checked='checked'";
					}
				}else if($i == 0){
					$checked= " checked='checked'";
				}
				if($opac_simple_search_suggestions){
					$html.="
						<span class='search_radio_button' id='search_radio_button_".$i."'><input type='radio' name='dest' value='".$datas[$i]['page']."'".$checked." onClick='toggleCompletion(this.value);' />&nbsp;".$this->format_text($datas[$i]['name'])."</span>";
				}else{
					$html.="
						<span class='search_radio_button' id='search_radio_button_".$i."'><input type='radio' name='dest' value='".$datas[$i]['page']."'".$checked."/>&nbsp;".$this->format_text($datas[$i]['name'])."</span>";
				}
			}
		}
		if ($this->parameters['link_search_advanced']) {
			$html.="
				<p class='search_advanced_link'><a href='./index.php?search_type_asked=simple_search'>".$this->format_text($this->msg['cms_module_search_view_link_search_advanced_display'])."</a></p>";
		}
		$html.= "		
			</form>";
		return $html;
	}
	
	public function get_headers($datas=array()){
		global $base_path;
		$headers = array();
		
		$headers[] = "
		<script type='text/javascript'>
			function ".$this->get_module_dom_id()."_change_dest(){
				var page = 0;
				var dests = document.forms['".$this->get_module_dom_id()."_searchbox'].dest;
				for(var i = 0; i < dests.length; i++){
					if(dests[i].checked){
						page = dests[i].value;
						break;
					}
				}
				
				if(page>0){
					document.forms['".$this->get_module_dom_id()."_searchbox'].action = '".$base_path."/index.php?lvl=cmspage&pageid='+page;
				}
				return true;
			}
		</script>";
		return $headers;	
	}
}