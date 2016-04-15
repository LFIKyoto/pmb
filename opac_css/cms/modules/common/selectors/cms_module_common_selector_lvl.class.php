<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_lvl.class.php,v 1.7 2014-11-19 15:53:45 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_lvl extends cms_module_common_selector{
	protected $lvl=array(); 
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->lvl = array(
			"author_see",
			"categ_see",
			"indexint_see",
			"coll_see",
			"more_results",
			"notice_display",
			"bulletin_display",
			"publisher_see",
			"titre_uniforme_see",
			"serie_see",
			"search_result",
			"subcoll_see",
			"search_history",
			"etagere_see",
			"etageres_see",
			"show_cart",
			"show_list",
			"section_see",
			"rss_see",
			//"doc_command",
			"sort",
			"lastrecords",
			"infopages",
			"extend",
			"external_authorities",
			"perio_a2z_see",
			"cmspage",
			"index",
			//search_type_asked	
			"simple_search",
			"extended_search",
			"term_search",
			"tags_search",
			"search_perso",
			"external_search",
			"perio_a2z",
			"bannette_see",
			"faq",
			"empr",
			"askmdp",
			"subscribe"
		);
	}
	
	public function get_form(){
		//si on est sur une page de type Page en création de cadre, on propose la condition pré-remplie...
		switch($this->cms_build_env['input']){
			case "empr.php" :
				if(!$this->id){
					$this->parameters[] = "empr";
				}
				break;
			case "askmdp.php" :
				if(!$this->id){
					$this->parameters[] = "askmdp";
				}
				break;
			case "subscribe.php" :
				if(!$this->id){
					$this->parameters[] = "subscribe";
				}
				break;
			default : 
				if ($this->cms_build_env['search_type_asked']){
					if(!$this->id){
						$this->parameters[] = $this->cms_build_env['search_type_asked'];
					}
				}else if($this->cms_build_env['lvl']){
					if(!$this->id){
						$this->parameters[] = $this->cms_build_env['lvl'];
					}
				}
				break;
		}
		if (!$this->parameters) $this->parameters=array();
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_lvl'>".$this->format_text($this->msg['cms_module_common_selector_lvl'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='".$this->get_form_value_name("lvl")."[]' multiple='yes'>";
		foreach($this->lvl as $lvl){
			$form.="
						<option value='".$lvl."' ".(in_array($lvl,$this->parameters) ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_selector_lvl_'.$lvl])."</option>";
		}
		$form.="				
					</select>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("lvl");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}