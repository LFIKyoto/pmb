<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddie_ui.class.php,v 1.7.6.5 2019-11-22 14:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/caddie/list_caddie_root_ui.class.php");

class list_caddie_ui extends list_caddie_root_ui {
		
	protected $instance_notice_tpl_gen;
	
	protected $flag_notice_id;
	
	protected function _get_query_caddie_content() {
		$query = "SELECT caddie_content.object_id FROM caddie_content";
		switch (static::$object_type) {
			case 'NOTI' :
				$query .= " left join notices on object_id=notice_id " ;
				break;
			case 'EXPL' :
				$query .= " left join exemplaires on object_id=expl_id " ;
				break;
			case 'BULL' :
				$query .= " left join bulletins on object_id=bulletin_id " ;
				break;
			default:
			    break;
		}
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'NOTI':
				$query = "SELECT n1.notice_id as id, n1.*, series.*, p1.*, p2.*, collections.*, sub_collections.*, indexint.*
					FROM notices n1 
					left join series on serie_id=n1.tparent_id
					left join publishers p1 on p1.ed_id=n1.ed1_id
					left join publishers p2 on p2.ed_id=n1.ed2_id
					left join collections on n1.coll_id=collection_id
					left join sub_collections on n1.subcoll_id=sub_coll_id
					left join indexint on n1.indexint=indexint_id 
					WHERE n1.notice_id IN (".$this->_get_query_caddie_content().")";
				break;
			case 'EXPL':
				$query = "SELECT e.expl_id as id, e.*, t.*, s.*, st.*, l.*, stat.*, n.*, series.*, p1.*, collections.*, sub_collections.*, p2.*, indexint.*, b.*
					FROM exemplaires e
					, docs_type t
					, docs_section s
					, docs_statut st
					, docs_location l
					, docs_codestat stat
					, notices n left join series on serie_id=n.tparent_id
					left join publishers p1 on p1.ed_id=n.ed1_id
					left join publishers p2 on p2.ed_id=n.ed2_id
					left join collections on n.coll_id=collection_id
					left join sub_collections on n.subcoll_id=sub_coll_id
					left join indexint on n.indexint=indexint_id
					left join bulletins as b on b.bulletin_notice=.n.notice_id 
					WHERE e.expl_id IN (".$this->_get_query_caddie_content().")
					AND e.expl_typdoc=t.idtyp_doc
					AND e.expl_section=s.idsection
					AND e.expl_statut=st.idstatut
					AND e.expl_location=l.idlocation
					AND e.expl_codestat=stat.idcode
					AND ((e.expl_notice=n.notice_id AND e.expl_notice <> 0) )";
				// OR (e.expl_bulletin=b.bulletin_id AND e.expl_bulletin <> 0)
				break;
			case 'BULL':
				$query = "select bulletins.bulletin_id as id, bulletins.* from bulletins where bulletin_id IN (".$this->_get_query_caddie_content().") ";
				break;
			default:
			    break;
		}
		return $query;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	
		$notice_tpl = $this->objects_type.'_notice_tpl';
		global ${$notice_tpl};
		if(isset(${$notice_tpl})) {
			$this->filters['notice_tpl'] = ${$notice_tpl};
		}
		parent::set_filters_from_form();
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
	
		$search_filters_form = parent::get_search_filters_form();
		if(!isset($this->filters['notice_tpl'])) $this->filters['notice_tpl'] = 0;
		$sel_notice_tpl=notice_tpl_gen::gen_tpl_select($this->objects_type."_notice_tpl",$this->filters['notice_tpl'],'',1,1);
		$suppl = "";
		if($sel_notice_tpl) {
			$sel_notice_tpl= "
				<div class='row'>
					<div class='colonne3'>
						<div class='row'>
							<label>".$msg['caddie_select_notice_tpl']."</label>
						</div>
						<div class='row'>
							".$sel_notice_tpl."
						</div>
					</div>
				</div>";
		}
		$search_filters_form .= $sel_notice_tpl;
		return $search_filters_form;
	}
	
	/**
	 * Objet de la liste du document bibliographique
	 */
	protected function get_display_export_noti_content_object_list($object, $line) {
		$display = "";
		$myCart = caddie_root::get_instance_from_object_type(static::$object_type, static::$id_caddie);
		if ($myCart->type=="EXPL"){
			$rqt_test = "select expl_notice as id from exemplaires where expl_id='".$object->id."' ";
			$res_notice = pmb_mysql_query($rqt_test);
			$obj_notice = pmb_mysql_fetch_object($res_notice) ;
			if (!$obj_notice->id) {
				$rqt_test = "select num_notice as id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_id='".$object->id."' ";
				$res_notice = pmb_mysql_query($rqt_test);
				$obj_notice = pmb_mysql_fetch_object($res_notice) ;
			}
			if((!isset($this->flag_notice_id[$obj_notice->id]) || !$this->flag_notice_id[$obj_notice->id]) && $obj_notice->id){
				$this->flag_notice_id[$obj_notice->id]=1;
				$display .= $this->instance_notice_tpl_gen->build_notice($obj_notice->id);
			}
		} elseif ($myCart->type=="NOTI") $display .= $this->instance_notice_tpl_gen->build_notice($object->id);
		if ($myCart->type=="BULL"){
			$rqt_test = $rqt_tout = "select num_notice as id from bulletins where bulletin_id = '".$object->id."' ";
			$res_notice = pmb_mysql_query($rqt_test);
			$obj_notice = pmb_mysql_fetch_object($res_notice);
			if((!isset($this->flag_notice_id[$obj_notice->id]) || !$this->flag_notice_id[$obj_notice->id]) && $obj_notice->id){
				$this->flag_notice_id[$obj_notice->id]=1;
				$display .= $this->instance_notice_tpl_gen->build_notice($obj_notice->id);
			}
		}
		return $display;
	}
	
	/**
	 * Liste des objets du document bibliographique
	 */
	public function get_display_export_noti_content_list() {
		$display = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				$display .= "
					<div class='list_ui_content_list_group ".$this->objects_type."_content_list_group' colspan='".count($this->columns)."'>
						".$group_label."
					</div>";
				foreach ($objects as $i=>$object) {
					$display .= $this->get_display_export_noti_content_object_list($object, $i);
				}
			}
		} else {
			foreach ($this->objects as $i=>$object) {
					$display .= $this->get_display_export_noti_content_object_list($object, $i);
			}
		}
		return $display;
	}
	
	public function get_display_export_noti_list() {
		global $charset;
		
		$display = "";
		
		$notice_tpl = $this->objects_type."_notice_tpl";
		global ${$notice_tpl};
		$this->instance_notice_tpl_gen=new notice_tpl_gen(${$notice_tpl});
		if(count($this->objects)) {
			$display .= $this->get_display_export_noti_content_list();
		}
		return "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>".$display."</body></html>";
	}
	
	protected function get_exclude_fields() {
		switch (static::$object_type) {
			case 'NOTI':
				return array(
						'tparent_id',
						'ed1_id',
						'ed2_id',
						'coll_id',
						'subcoll_id',
						'indexint',
						'statut',
						'signature',
						'opac_visible_bulletinage',
						'map_echelle_num',
						'map_projection_num',
						'map_ref_num',
						'map_equinoxe'
				);
				break;
			case 'EXPL':
				return array(
						'expl_notice',
						'expl_bulletin',
						'expl_typdoc',
						'expl_section',
						'expl_statut',
						'expl_location',
						'expl_codestat',
						'expl_owner',
						'transfert_location_origine',
						'transfert_statut_origine',
						'transfert_section_origine',
						'idtyp_doc',
						'tdoc_owner'
				);
				break;
			case 'BULL':
				return array(
						'index_titre',
						'num_notice'
				);
				break;
			default:
			    return array();
			    break;
		}
	}
	
	protected function get_main_fields() {
		switch (static::$object_type) {
			case 'NOTI':
				return array_merge(
						$this->get_describe_fields('notices', 'notices', 'notices'),
						array('serie_name' => $this->get_describe_field('titrserie', 'notices', 'notices')),
						array('collection_name' => $this->get_describe_field('coll', 'notices', 'notices')),
						array('sub_coll_name' => $this->get_describe_field('subcoll', 'notices', 'notices')),
						array('publisher_name' => $this->get_describe_field('editeur', 'notices', 'notices')),
						array('indexint_name' => $this->get_describe_field('indexint', 'notices', 'notices')),
						array('statut_name' => $this->get_describe_field('statut', 'notices', 'notices'))
				);
				break;
			case 'EXPL':
				return array_merge(
						$this->get_describe_fields('exemplaires', 'items', 'exemplaires'),
						$this->get_describe_fields('notices', 'notices', 'notices')
				);
				break;
			case 'BULL':
				return array_merge(
						array('bulletin_numero' => 'bulletin_numero', 'mention_date' => 'mention_date', 'date_date' => 'date_date', 'bulletin_titre' => 'bulletin_titre', 'bulletin_cb' => 'bulletin_cb')
				);
				break;
			default:
			    break;
		}
		
	}
	
	protected function add_authors_available_columns() {
		return array(
				'author_main' => '244',
// 				'authors_others' => '246',
				'authors_secondary' => '247'
		);
	}
	
	protected function add_categories_available_columns() {
		return array(
				'categories' => '134'
		);
	}
	
	protected function add_languages_available_columns() {
		return array(
				'langues' => '710',
				'languesorg' => '711'
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		switch (static::$object_type) {
			case 'NOTI':
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authors_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_categories_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_languages_available_columns());
				$this->add_custom_fields_available_columns('notices', 'notice_id');
				break;
			case 'EXPL':
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authors_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_categories_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_languages_available_columns());
				$this->add_custom_fields_available_columns('expl', 'expl_id');
				break;
			default:
			    break;
		}
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	    if ($this->applied_sort[0]['by']) {
	        $sort_by = $this->applied_sort[0]['by'];
	        switch($sort_by) {
	            case 'author_main':
	            case 'authors_others':
	            case 'authors_secondary':
	            case 'categories':
	            case 'langues':
	            case 'languesorg':
	            case 'typdoc':
	            case 'statut_name':
	            case 'publisher_name':
	                $this->applied_sort_type = 'OBJECTS';
	                return '';
	            default :
	                return parent::_get_query_order();
	        }
	    }
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 */
	protected function _compare_objects($a, $b) {
	    if($this->applied_sort[0]['by']) {
	        $sort_by = $this->applied_sort[0]['by'];
	        switch($sort_by) {
	            case 'author_main':
	                $record_datas_a = record_display::get_record_datas($a->id);
	                $record_datas_b = record_display::get_record_datas($b->id);
	                return strcmp($record_datas_a->get_auteurs_principaux(), $record_datas_b->get_auteurs_principaux());
	                break;
	            case 'authors_others':
	                //TODO
	                break;
	            case 'authors_secondary':
	                $record_datas_a = record_display::get_record_datas($a->id);
	                $record_datas_b = record_display::get_record_datas($b->id);
	                return strcmp($record_datas_a->get_auteurs_secondaires(), $record_datas_b->get_auteurs_secondaires());
	                break;
	            case 'categories':
	                $categories_a = strip_tags($this->get_cell_categories_content($a));
	                $categories_b = strip_tags($this->get_cell_categories_content($b));
	                return strcmp($categories_a, $categories_b);
	                break;
	            case 'langues':
	            case 'languesorg':
	                $record_datas_a = record_display::get_record_datas($a->id);
	                $langues_a = $record_datas_a->get_langues();
	                $record_datas_b = record_display::get_record_datas($b->id);
	                $langues_b = $record_datas_b->get_langues();
	                return strcmp(record_display::get_lang_list($langues_a[$sort_by]), record_display::get_lang_list($langues_b[$sort_by]));
	                break;
	            case 'typdoc':
	                $marc_list_instance = marc_list_collection::get_instance('doctype');
	                return strcmp($marc_list_instance->table[$a->{$sort_by}], $marc_list_instance->table[$b->{$sort_by}]);
	                break;
	            case 'statut_name':
	                $record_datas_a = record_display::get_record_datas($a->id);
	                $record_datas_b = record_display::get_record_datas($b->id);
	                return strcmp($record_datas_a->get_statut_notice(), $record_datas_b->get_statut_notice());
	                break;
	            case 'publisher_name':
	                //@TODO
	                break;
	            default :
	                return parent::_compare_objects($a, $b);
	                break;
	        }
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('tit1');
	}
	
	protected function get_cell_categories_content($object) {
		global $opac_thesaurus;
		global $opac_categories_categ_in_line;
		global $pmb_keyword_sep;
		
		$content = '';
		$record_datas = record_display::get_record_datas($object->id);
		$categories = $record_datas->get_categories();
		foreach($categories as $id_thes => $thesaurus) {
			if($opac_thesaurus) {
				foreach ($thesaurus as $i=>$categorie) {
					if($opac_categories_categ_in_line) {
						if(!$i) {
							$content .= "<p><strong>".$categorie['object']->thes->libelle_thesaurus."</strong></p>";
						} else {
							$content .= $pmb_keyword_sep;
						}
						$content .= "<span>".$categorie['format_label']."</span>";
					} else {
						$content .= "<p>[".$categorie['object']->thes->libelle_thesaurus."] ".$categorie['object']->libelle."</p>";
					}
				}
			} else {
				foreach ($thesaurus as $i=>$categorie) {
					if($opac_categories_categ_in_line) {
						if($i) {
							$content .= $pmb_keyword_sep;
						}
						$content .= "<span>".$categorie['object']->libelle."</span>";
					} else {
						$content .= "<p>".$categorie['object']->libelle."</p>";
					}
				}
			}
		}
		return $content;
	}
	
	protected function get_cell_group_label($group_label, $indice=0) {
		$content = '';
		switch($this->applied_group[$indice]) {
			case 'typdoc':
				$marc_list_instance = marc_list_collection::get_instance('doctype');
				$content .= $marc_list_instance->table[$group_label];
				break;
			default :
				$content .= parent::get_cell_group_label($group_label, $indice);
				break;
		}
		return $content;
	}
	
	protected function get_grouped_label($object, $property) {
	    $grouped_label = '';
	    switch($property) {
	        case 'author_main':
	            $record_datas = record_display::get_record_datas($object->id);
	            $grouped_label = $record_datas->get_auteurs_principaux();
	            break;
	        case 'authors_others':
	            //TODO
	            break;
	        case 'authors_secondary':
	            $record_datas = record_display::get_record_datas($object->id);
	            $grouped_label = $record_datas->get_auteurs_secondaires();
	            break;
	        case 'categories':
	            $grouped_label = strip_tags($this->get_cell_categories_content($object));
	            break;
	        case 'langues':
	        case 'languesorg':
	            $record_datas = record_display::get_record_datas($object->id);
	            $langues = $record_datas->get_langues();
	            return record_display::get_lang_list($langues[$property]);
	            break;
	        case 'statut_name':
	            $record_datas = record_display::get_record_datas($object->id);
	            $grouped_label = $record_datas->get_statut_notice();
	            break;
	        case 'publisher_name':
	            //@TODO
	            break;
	        default:
	            $grouped_label = parent::get_grouped_label($object, $property);
	            break;
	    }
	    return $grouped_label;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		switch($property) {
			case 'author_main':
				$record_datas = record_display::get_record_datas($object->id);
				$content = $record_datas->get_auteurs_principaux();
				break;
			case 'authors_others':
				//TODO
				break;
			case 'authors_secondary':
				$record_datas = record_display::get_record_datas($object->id);
				$content = $record_datas->get_auteurs_secondaires();
				break;
			case 'categories':
				$content = $this->get_cell_categories_content($object);
				break;
			case 'langues':
			case 'languesorg':
				$record_datas = record_display::get_record_datas($object->id);
				$langues = $record_datas->get_langues();
				$content = record_display::get_lang_list($langues[$property]); 
				break;
			case 'typdoc':
				$marc_list_instance = marc_list_collection::get_instance('doctype');
				$content = $marc_list_instance->table[$object->{$property}];
				break;
			case 'statut_name':
			    $record_datas = record_display::get_record_datas($object->id);
			    $content = $record_datas->get_statut_notice();
			    break;
			case 'publisher_name' :
			    $publishers_name = array();
			    $record_datas = record_display::get_record_datas($object->id);
			    $publishers = $record_datas->get_publishers();
			    if(count($publishers)) {
			        foreach ($publishers as $publisher) {
			            $publishers_name[] = $publisher->get_isbd();
			        }
			    }
			    $content = implode(' / ',$publishers_name);
			    break;
			default :
				$content = parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_export_icons() {
		global $msg;
		
		$export_icons = "<img  src='".get_url_icon('texte_ico.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('EXPORT_NOTI');\" alt='".$msg['etatperso_export_notice']."' title='".$msg['etatperso_export_notice']."'/>&nbsp;&nbsp;";
		$export_icons .= parent::get_export_icons();
		return $export_icons;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type='.static::$object_type.'&idcaddie='.static::$id_caddie.'&item=0';
	}
}