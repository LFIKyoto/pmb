<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_sectionslist.class.php,v 1.6.2.1 2015-10-28 16:25:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_sectionslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for section in sections %}
<h3>{{section.title}}</h3>
<img src='{{section.logo.large}}'/>
<blockquote>{{section.resume}}</blockquote>
<blockquote>{{section.content}}</blockquote>
{% endfor %}
</div>";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_sectionslist_link_section'>".$this->format_text($this->msg['cms_module_common_view_sectionslist_build_section_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("section");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_sectionslist_link_article'>".$this->format_text($this->msg['cms_module_common_view_sectionslist_build_article_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("article");
		$form.="	
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("section");
		$this->save_constructor_link_form('article');
		return parent::save_form();
	}
	
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = "Liste de rubriques";
		$render_datas['sections'] = array();
		if(is_array($datas) && count($datas)){
			foreach($datas as $section){
				$cms_section = cms_provider::get_instance("section",$section);
				$infos= $cms_section->format_datas(true, true, true, true);
				$infos['link'] = $this->get_constructed_link("section",$section);
				foreach ($infos['articles'] as $i=>$article) {
					$infos['articles'][$i]['link'] = $this->get_constructed_link("article",$article["id"]);
				}
				$render_datas['sections'][]=$infos;
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['cms_module_common_view_title']
		);
		$sections = array(
			'var' => "sections",
			'desc' => $this->msg['cms_module_common_view_section_desc'],
			'children' => $this->prefix_var_tree(cms_section::get_format_data_structure(true, true, true, true),"sections[i]")
		);
		$sections['children'][] = array(
			'var' => "sections[i].link",
			'desc'=> $this->msg['cms_module_common_view_section_link_desc']
		);
		$format[] = $sections;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}