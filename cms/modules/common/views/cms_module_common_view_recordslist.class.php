<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_recordslist.class.php,v 1.10 2014-11-21 11:16:50 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_recordslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		
		$this->default_template = "<h3>{{title}}</h3>
{% for record in records %}
<p>{{record.header}}</p>
<blockquote>{{record.content}}</blockquote>
{% endfor %}";
	}
	
	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_recordslist_view_link'>".$this->format_text($this->msg['cms_module_recordslist_view_link'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
				</div>
			</div>";
		$form.= parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_recordslist_used_template'>".$this->format_text($this->msg['cms_module_common_view_recordslist_used_template'])."</label>
				</div>
				<div class='colonne-suite'>";
		
		$form.= notice_tpl::gen_tpl_select("cms_module_common_view_recordslist_used_template",$this->parameters['used_template']);
		$form.="				
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_recordslist_used_template;
		
		$this->save_constructor_link_form("notice");
		$this->parameters['used_template'] = $cms_module_common_view_recordslist_used_template;
		return parent::save_form();
	}
	
	public function render($datas){
		global $opac_notice_affichage_class;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg;
		if(!$opac_notice_affichage_class){
			$opac_notice_affichage_class ="notice_affichage";
		}

		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $datas["title"];
		$render_datas['records'] = array();
		if(is_array($datas["records"])){
			foreach($datas["records"] as $notice){
				//on calcule les templates pour chaque notices...
				$notice_class = new $opac_notice_affichage_class($notice);
				$notice_class->do_header();
				if($notice_class->notice->niveau_biblio != "b"){
					$notice_id = $notice_class->notice_id;
					$is_bulletin = false;
				}else {
					$notice_id = $notice_class->bulletin_id;
					$is_bulletin = true;
				}
				if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $notice_class->notice->thumbnail_url)) {
					$code_chiffre = pmb_preg_replace('/-|\.| /', '', $notice_class->notice->code);
					$url_image = $opac_book_pics_url ;
					$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($notice_class->notice->thumbnail_url) ;
					if ($notice_class->notice->thumbnail_url){
						$url_vign=$notice_class->notice->thumbnail_url;
					}else if($code_chiffre){
						$url_vign = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					}else {
						$url_vign = $opac_url_base."images/vide.png";
					}
				}
				$infos = array(
					'id' => $notice_id,
					'title' => $notice_class->notice->tit1,
					'vign' => $url_vign,
					'header' => $notice_class->notice_header,
					'link' => $this->get_constructed_link("notice",$notice_id,$is_bulletin),
				);
				if($this->parameters['used_template']){
					$tpl = new notice_tpl_gen($this->parameters['used_template']);
					$infos['content'] = $tpl->build_notice($notice);
				}else{
					$notice_class->do_isbd();
					$infos['content'] = $notice_class->notice_isbd;
				}
				$render_datas['records'][]=$infos;
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
		$format[] =	array(
			'var' => "records",
			'desc' => $this->msg['cms_module_commom_view_records_desc'],
			'children' => array(
				array(
					'var' => "records[i].id",
					'desc'=> $this->msg['cms_module_common_view_record_id_desc']
				),
				array(
					'var' => "records[i].title",
					'desc'=> $this->msg['cms_module_common_view_record_title_desc']
				),
				array(
					'var' => "records[i].vign",
					'desc'=> $this->msg['cms_module_common_view_record_vign_desc']
				),
				array(
					'var' => "records[i].header",
					'desc'=> $this->msg['cms_module_common_view_record_header_desc']
				),	
				array(
					'var' => "records[i].content",
					'desc'=> $this->msg['cms_module_common_view_record_content_desc']
				),	
				array(
					'var' => "records[i].link",
					'desc'=> $this->msg['cms_module_common_view_record_link_desc']
				)
			)
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}