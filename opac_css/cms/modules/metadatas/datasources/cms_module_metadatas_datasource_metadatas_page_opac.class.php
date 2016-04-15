<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_datasource_metadatas_page_opac.class.php,v 1.6 2015-04-03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_datasource_metadatas_page_opac extends cms_module_metadatas_datasource_metadatas_generic{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_query(){
		global $id;
		
		$post = $_POST;
		$get = $_GET;
		
		if($post['lvl']){
			$niveau = $post['lvl'];
		} elseif ($get['lvl']){
			$niveau = $get['lvl'];
		} else $niveau='';
		
		$query = "";
		if ($id) {
			$id+=0;
			switch($niveau){
				case 'notice_display' :
					$query = "select notice_id as id, tit1 as title, n_resume as resume, code, thumbnail_url as logo_url, 'notice' as type from notices where notice_id=".$id;
					break;
				case 'bulletin_display' :
					$query = "select bulletin_id as id, bulletin_titre as title, n_resume as resume, code, thumbnail_url as logo_url, 'bulletin' as type from bulletins left join notices on notice_id=num_notice where bulletin_id=".$id;
					break;
				case 'author_see':
					$query = "select author_id as id, concat(author_name,' ',author_rejete) as title, author_comment as resume, 'authority' as type from authors where author_id=".$id;
					break;
				case 'titre_uniforme_see':
					$query = "select tu_id as id, tu_name as title, tu_comment as resume, 'authority' as type from titres_uniformes where tu_id=".$id;
					break;
				case 'serie_see':
					$query = "select serie_id as id, serie_name as title, 'authority' as type from series where serie_id=".$id;
					break;
				case 'categ_see':
					$query = "select num_noeud as id, libelle_categorie as title, comment_public as resume, 'authority' as type from categories where num_noeud=".$id;
					break;
				case 'indexint_see':
					$query = "select indexint_id as id, indexint_name as title, indexint_comment as resume, 'authority' as type from indexint where indexint_id=".$id;
					break;
				case 'publisher_see':
					$query = "select ed_id as id, ed_name as title, ed_comment as resume, 'authority' as type from publishers where ed_id=".$id;
					break;
				case 'coll_see':
					$query = "select collection_id as id, collection_name as title, collection_comment as resume, 'authority' as type from collections where collection_id=".$id;
					break;
				case 'subcoll_see':
					$query = "select sub_coll_id as id, sub_coll_name as title, subcollection_comment as resume, 'authority' as type from sub_collections where sub_coll_id=".$id;
					break;
				case 'etagere_see':
					$query = "select idetagere as id, name as title, comment as resume, 'etagere' as type from etagere where idetagere=".$id;
					break;
				case 'bannette_see':
					$query = "select id_bannette as id, nom_bannette as title, comment_public as resume, 'bannette' as type from bannettes where id_bannette=".$id;
					break;
				case 'rss_see':
					$query = "select id_rss_flux as id, nom_rss_flux as title, descr_rss_flux as resume, 'rss' as type from rss_flux where id_rss_flux=".$id;
					break;
				case 'concept_see' :
					$query ="select id_item as id, value as title from skos_fields_global_index where code_champ =1 and code_ss_champ =1 and id_item = $id";
					break;
				case "authperso_see":
					global $dbh;
					$query = "select num_type from authperso_custom_values join authperso_custom on authperso_custom_champ = idchamp where authperso_custom_origine = ".$id;
					$result = pmb_mysql_query($query,$dbh);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$authperso = new authperso($row->num_type);
						$query = "select '".$id."' as id ,'".addslashes($authperso->get_isbd($id))."' as title";
					}
				default :
					break;
			}
		}
		return $query;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $opac_url_base;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $dbh;
		//on commence par récupérer le type et le sous-type de page...
		$type_page_opac = cms_module_common_datasource_typepage_opac::get_type_page();
		$subtype_page_opac = cms_module_common_datasource_typepage_opac::get_subtype_page();
		
		if($type_page_opac && $subtype_page_opac){
				$group_metadatas = parent::get_group_metadatas();
				
				$datas = array();
				$query = $this->get_query();
				if ($query) {
					$post = $_POST;
					$get = $_GET;
					
					if($post['lvl']){
						$niveau = $post['lvl'];
					} elseif ($get['lvl']){
						$niveau = $get['lvl'];
					} else $niveau='';
					
					$result = pmb_mysql_query($query, $dbh);
					while ($row = pmb_mysql_fetch_object($result)) {
						$datas["id"] = $row->id;
						$datas["title"] = $row->title;
						$datas["resume"] = $row->resume;
						if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $row->logo_url)) {
							$code_chiffre = pmb_preg_replace('/-|\.| /', '', $row->code);
							$url_image = $opac_book_pics_url ;
							$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($row->logo_url) ;
							if ($row->logo_url){
								$url_vign=$row->logo_url;
							}else if($code_chiffre){
								$url_vign = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
							}else {
								$url_vign = $opac_url_base."images/vide.png";
							}
						}
						$datas["logo_url"] = $url_vign;
						$datas["link"] = $opac_url_base."index.php?lvl=".$niveau."&id=".$row->id;
						$datas["type"] = $row->type;
					}
				}
				$datas["details"] = array(
					'type_page' => cms_module_common_datasource_typepage_opac::get_label($type_page_opac),
					'subtype_page' => cms_module_common_datasource_typepage_opac::get_label($subtype_page_opac)
				);
				$datas = array_merge($datas,parent::get_datas());
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
		return false;
	}
	
	public function get_format_data_structure(){
		$main_fields = array();
		$main_fields[] = array(
				'var' => "type_page",
				'desc'=> $this->msg['cms_module_metadatas_datasource_metadatas_page_opac_typepage_desc']
		);
		$main_fields[] = array(
				'var' => "subtype_page",
				'desc'=> $this->msg['cms_module_metadatas_datasource_metadatas_page_opac_subtypepage_desc']
		);
	
		$datas = array(
				array(
						'var' => $this->msg['cms_module_metadatas_datasource_metadatas_page_opac_main_fields'],
						"children" => $main_fields
				)
		);

		$format_datas = array(
				array(
						'var' => "details",
						'desc' => $this->msg['cms_module_metadatas_datasource_metadatas_page_opac_desc'],
						'children' => $this->prefix_var_tree($datas,"details")
				)
		);
		$format_datas = array_merge(parent::get_format_data_structure(),$format_datas);
		return $format_datas;
	}
}