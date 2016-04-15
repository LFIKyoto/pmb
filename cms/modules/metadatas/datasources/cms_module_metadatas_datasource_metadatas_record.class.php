<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_datasource_metadatas_record.class.php,v 1.1 2014-12-18 10:20:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_datasource_metadatas_record extends cms_module_metadatas_datasource_metadatas_generic{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_common_selector_record",
				"cms_module_common_selector_env_var",
				"cms_module_common_selector_type_article",
				"cms_module_common_selector_type_section",
				"cms_module_common_selector_type_article_generic",
				"cms_module_common_selector_type_section_generic"
		);
	}
	
	/*
	 * Récupération des données de la source...
	*/
	public function get_datas(){
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_url_base;
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		if($this->parameters['selector'] != ""){
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			
			$notice=$selector->get_value();
			if(is_array($notice)){
				$notice = $notice[0];
			}

			if($notice){
				$group_metadatas = parent::get_group_metadatas();
				
				$datas = array();
				$notice_class = new notice($notice);
				if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $notice_class->thumbnail_url)) {
					$code_chiffre = pmb_preg_replace('/-|\.| /', '', $notice_class->code);
					$url_image = $opac_book_pics_url ;
					$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($notice_class->thumbnail_url) ;
					if ($notice_class->thumbnail_url){
						$url_vign=$notice_class->thumbnail_url;
					}else if($code_chiffre){
						$url_vign = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					}else {
						$url_vign = $opac_url_base."images/vide.png";
					}
				}
				$datas = array(
						'id' => $notice_class->id,
						'title' => $notice_class->tit1,
						'link' => $this->get_constructed_link("notice",$notice_class->id),
						'logo_url' => $url_vign,
						'header' => $notice_class->notice_header,
						'resume' => $notice_class->n_resume,
						'content' => $content,
						'type' => 'notice'
				);
				$datas["details"] = $datas;
				$datas = array_merge($datas,parent::get_datas());
				$datas['link'] = $this->get_constructed_link("notice",$notice_class->id);
				foreach ($group_metadatas as $i=>$metadatas) {
					if (is_array($metadatas["metadatas"])) {
						foreach ($metadatas["metadatas"] as $key=>$value) {
							try {
								$group_metadatas[$i]["metadatas"][$key] = H2o::parseString($value)->render($datas);
							}catch(Exception $e){
							}
						}
					}
				}
				return $group_metadatas;
			}
		}
		return false;
	}

	public function get_format_data_structure(){
		$datas = array(
			array(
				'var' => $this->msg['cms_module_metadatas_datasource_main_fields'],
				"children" => array(
								array(
										'var' => "id",
										'desc'=> $this->msg['cms_module_metadatas_datasource_record_id_desc']
								),
								array(
										'var' => "title",
										'desc' => $this->msg['cms_module_metadatas_datasource_record_title_desc']
								),
								array(
										'var' => "resume",
										'desc' => $this->msg['cms_module_metadatas_datasource_record_resume_desc']
								),
								array(
										'var' => "logo_url",
										'desc'=> $this->msg['cms_module_metadatas_datasource_record_vign_desc']
								),
								array(
										'var' => "header",
										'desc'=> $this->msg['cms_module_metadatas_datasource_record_header_desc']
								),
								array(
										'var' => "content",
										'desc'=> $this->msg['cms_module_metadatas_datasource_record_content_desc']
								),
								array(
										'var' => "link",
										'desc'=> $this->msg['cms_module_metadatas_datasource_record_link_desc']
								)
						)
				),
			);
		
		$format_datas = array(
				array(
						'var' => "details",
						'desc' => $this->msg['cms_module_metadatas_datasource_metadatas_record_record_desc'],
						'children' => $this->prefix_var_tree($datas,"details")
				)
		);
		$format_datas = array_merge(parent::get_format_data_structure(),$format_datas);
		return $format_datas;
	}
}