<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_ui.class.php,v 1.17 2015-06-12 09:36:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/skos/onto_skos_concept_ui.tpl.php');

class onto_skos_concept_ui extends onto_common_ui{
	
	/**
	 * Retourne la liste hierarchisée en provenance du controler instancié
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return array $elements
	 */
	
// 	public static function get_list_from_controler($controler,$params){
// 		if($params->action== 'list_selector' && $params->parent_id != 0){
// 			//dans le cas des concepts, on veut le parent
// 			$elements = $controler->get_hierarchized_list($controler->get_class_uri($params->element),$params);
// 		}else{
// 			switch($params->action){
// 				case "search" :
// 					$elements = $controler->get_searched_list($params);
// 					break;
// 				case "list" :	
// 					$elements = $controler->get_hierarchized_list($controler->get_class_uri($params->sub),$params);
// 					break;
// 				default :
// 					$elements = parent::get_list_from_controler($controler,$params);
// 					break;
// 			}
// 		}
// 		//@todo : ajouter une factory pour déterminer quelle liste remonter?
		
// 		if($this->)
		
		
// 		return $elements;
// 	}
	
	/**
	 * Construit et retourne le selecteur de schema
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * @param bool $empty
	 * @param string $onchange
	 * @param string $name
	 * @param string $id
	 * 
	 * @return string $selector
	 */
	public static function get_scheme_list_selector($controler,$params,$empty=false,$onchange='',$name='',$id=''){
		global $msg,$charset,$lang,$base_path,$ontology_tpl;
		$selector=$ontology_tpl['scheme_list_selector'];
		
		$selector = str_replace("!!scheme_list_selector_onchange!!",$onchange , $selector);
		$selector = str_replace("!!scheme_list_selector_name!!",$name , $selector);
		$selector = str_replace("!!scheme_list_selector_id!!",$id , $selector);
	
		$list=$controler->get_scheme_list();
		
		$selector_options='';
		$option='';
		
		// Ajout de l'option "Tous les concepts"
		$option=$ontology_tpl['scheme_list_selector_option'];
		$option= str_replace("!!scheme_list_selector_options_value!!",'-1' , $option);
		$option= str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_all_concepts"], $option);
		$selected='';
		if($params->concept_scheme == -1){
			$selected='selected="selected"';
		}
		$option= str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
		$selector_options.=$option;
		
		// Ajout de l'option "Sans schéma"
		$option=$ontology_tpl['scheme_list_selector_option'];
		$option= str_replace("!!scheme_list_selector_options_value!!",'0' , $option);
		$option= str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_no_scheme"], $option);
		$selected='';
		if($params->concept_scheme == 0){
			$selected='selected="selected"';
		}
		$option= str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
		$selector_options.=$option;
		
		foreach($list['elements'] as $uri=>$scheme){
			
			$option=$ontology_tpl['scheme_list_selector_option'];
			$scheme_id=onto_common_uri::get_id($uri);
			
			$selected='';
			if($scheme_id==$params->concept_scheme){
				$selected='selected="selected"';
			}
			
			if($scheme[$lang]!=''){
				$display_label=$scheme[$lang];
			}else{
				$display_label=$scheme['default'];
			}
			
			$option= str_replace("!!scheme_list_selector_options_selected!!",$selected , $option);
			$option= str_replace("!!scheme_list_selector_options_value!!",$scheme_id , $option);
			$option= str_replace("!!scheme_list_selector_options_label!!",htmlentities($display_label,ENT_QUOTES,$charset) , $option);
			$selector_options.=$option;
		}		
		$selector = str_replace("!!scheme_list_selector_options!!",$selector_options , $selector);

		return $selector;
	}
	
	
	/**
	 * Renvoie la construction du fil de navigation
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * 
	 * @return string return 
	 */
	public static function get_breadcrumb($controler,$params){
		global $base_path;
		
		$breadcrumb=$controler->handle_breadcrumb();
		$return='';
		if(sizeof($breadcrumb)) {
			
			foreach($breadcrumb as $key=>$parent_id){
				if($return){
					$return.=' > ';
				}
				
				$return.="<a href='$base_path/autorites.php?categ=".$params->categ."&sub=".$params->sub."&action=list&concept_scheme=".$params->concept_scheme."&parent_id=".$parent_id."'>".$controler->get_data_label(onto_common_uri::get_uri($parent_id)).'</a>';
			}
		}
		return $return;
	}
	
	/**
	 * Renvoie le formulaire de recherche dans les concepts
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * 
	 * @return string $form
	 */
	public static function get_search_form($controler,$params){
		global $msg,$charset,$base_path,$ontology_tpl;
		
		$classes = $controler->get_classes();
		foreach($classes as $class){
			if($class->pmb_name == $params->sub){
				$title.= $controler->get_label($class->pmb_name);
			}
		}
		
		$onchange_scheme_list_selector='onchange="document.location =\''.$base_path.'/autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&id=&action=search&concept_scheme=\'+document.getElementById(\'id_concept_scheme\').value+\'&user_input=\'+document.getElementById(\'id_user_input\').value+\'&only_top_concepts='.$params->only_top_concepts.'\';"';
		$name_scheme_list_selector='concept_scheme';
		$id_scheme_list_selector='id_concept_scheme';
		
		$form=$ontology_tpl['skos_concept_search_form'];
		$form=str_replace('!!skos_concept_search_form_action!!', $base_path.'/autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&id=&action=search', $form);
		$form=str_replace('!!skos_concept_search_form_title!!', $title, $form);
		$form=str_replace('!!skos_concept_search_form_selector!!', self::get_scheme_list_selector($controler, $params,false,$onchange_scheme_list_selector,$name_scheme_list_selector,$id_scheme_list_selector), $form);
		
		$onchange_only_top_concepts = 'onchange="document.location =\''.$base_path.'/autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&id=&action=search&concept_scheme=\'+document.getElementById(\'id_concept_scheme\').value+\'&user_input=\'+document.getElementById(\'id_user_input\').value+\'&only_top_concepts=\'+this.checked*1;"';
		$checked_only_top_concepts = '';
		if ($params->only_top_concepts) $checked_only_top_concepts = 'checked="checked"';

		$form=str_replace('!!only_top_concepts_onchange!!', $onchange_only_top_concepts, $form);
		$form=str_replace('!!only_top_concepts_checked!!', $checked_only_top_concepts, $form);
		
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub];
		}else{
			$add_msg = sprintf($msg['onto_common_add'],$controler->get_label($params->sub));
		}
		$form=str_replace('!!skos_concept_search_form_user_input!!',stripslashes(htmlentities($params->user_input,ENT_QUOTES,$charset)),$form);
		$form=str_replace('!!skos_concept_search_form_concept_onclick!!','document.location=\'./autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit&concept_scheme='.$params->concept_scheme.'&parent_id='.$params->parent_id.'\'',$form);
		$form=str_replace('!!skos_concept_search_form_concept_value!!',htmlentities($add_msg,ENT_QUOTES,$charset),$form);
		
		$form=str_replace('!!skos_concept_search_form_composed_onclick!!','document.location=\'./autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit&composed=composed&concept_scheme='.$params->concept_scheme.'&parent_id='.$params->parent_id.'\'',$form);
		
		$form=str_replace('!!skos_concept_search_form_href!!',$base_path.'/autorites.php?categ='.$params->categ.'&sub='.$params->sub.'&action='.$params->action.'&concept_scheme='.$params->concept_scheme,$form);
		
		$form=str_replace('!!skos_concept_search_form_breadcrumb!!',self::get_breadcrumb($controler,$params) ,$form);
		
		return $form;
		
	}
	
	/**
	 * Renvoie l'affichage html de la liste hierarchisée
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_list($controler,$params){
		global $msg,$charset,$base_path,$ontology_tpl,$lang;
		
		$elements = $controler->get_list_elements($params);
		$list="<h3>".$elements['nb_total_elements']." ".$msg['onto_skos_concept_nb_results']."</h3>".$ontology_tpl['skos_concept_list'];
 		$list=str_replace("!!list_header!!", htmlentities($msg['103'],ENT_QUOTES,$charset), $list);
 		$list=str_replace("!!list_header_utilisation!!", htmlentities($msg['voir_notices_assoc'],ENT_QUOTES,$charset), $list);
		
		$list_content='';
		foreach($elements['elements'] as $uri => $item){
			$id = onto_common_uri::get_id($uri);
			if($controler->has_narrower($uri,$params)){	
				$line=$ontology_tpl['skos_concept_list_line_folder'];
				$line=str_replace("!!list_line_folder_href!!",$base_path."/autorites.php?categ=".$params->categ."&sub=".$params->sub."&action=list&parent_id=".$id."&concept_scheme=".$params->concept_scheme , $line);
			}else{
				$line=$ontology_tpl['skos_concept_list_line_doc'];
			}	
			$line=str_replace("!!list_line_href!!",$base_path."/autorites.php?categ=".$params->categ."&sub=".$params->sub."&action=edit&id=".$id."&parent_id=".$params->parent_id."&concept_scheme=".$params->concept_scheme , $line);
			$line=str_replace("!!list_line_libelle!!",htmlentities((isset($item[substr($lang,0,2)]) ? $item[substr($lang,0,2)] : $item['default']),ENT_QUOTES,$charset) , $line);
			
			$concept = new concept($id);
			$line=str_replace("!!list_line_nb_utilisations!!", count($concept->get_indexed_notices()), $line);
			$line=str_replace("!!list_line_nb_utilisations_href!!", $base_path."/catalog.php?categ=search&mode=0&etat=aut_search&aut_type=concept&aut_id=".$id, $line);
			
			$list_content.=$line;
		}
		
		$list=str_replace("!!list_content!!",$list_content , $list);
		$list=str_replace("!!list_pagination!!",aff_pagination("./autorites.php?categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&concept_scheme=".$params->concept_scheme."&parent_id=".$params->parent_id."&user_input=".$params->user_input,$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ) , $list);
		
		return $list;
	}
	
	/**
	 * Renvoie l'affichage html de la liste pour le selecteur d'autorité
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return string $form
	 */
	public static function get_list_selector($controler,$params){
		global $msg,$charset,$element_form,$ontology_tpl,$list_form,$lang;
	
		if($params->objs){
			$property=$controler->get_onto_property_from_pmb_name($params->objs);
			$element = $property->range[$params->range];
		}else {
			$element = $controler->get_class_uri($params->element);
		}
		$elements = $controler->get_list_elements($params);
		$elements_form = "";
		$list = $list_form;
		if($elements["nb_total_elements"]){
			foreach($elements['elements'] as $uri => $item){
				switch($element){
					case "http://www.w3.org/2004/02/skos/core#Concept" :
						if($controler->has_narrower($uri,$params)){
							$current_element_form = $ontology_tpl['skos_concept_list_selector_line_folder'];
							$current_element_form = str_replace("!!folder_href!!",  $params->base_url."&parent_id=".onto_common_uri::get_id($uri), $current_element_form);
						}else{
							$current_element_form = $ontology_tpl['skos_concept_list_selector_line_doc'];
						}
						break;
					default :
						$current_element_form = $element_form;
						break;
				}
				
				$current_element_form = str_replace("!!caller!!", $params->caller, $current_element_form);
				$current_element_form = str_replace("!!element!!", $params->element, $current_element_form);
				$current_element_form = str_replace("!!order!!", $params->order, $current_element_form);
				$current_element_form = str_replace("!!uri!!", $uri, $current_element_form);
				$current_element_form = str_replace("!!item!!", addslashes((isset($item[substr($lang,0,2)]) ? $item[substr($lang,0,2)] : $item['default'])), $current_element_form);
				$current_element_form = str_replace("!!range!!", rawurlencode($element ? $element : $controler->get_class_uri($params->sub)), $current_element_form);
				$current_element_form = str_replace("!!callback!!", $params->callback, $current_element_form);
				$current_element_form = str_replace("!!item_libelle!!", htmlentities((isset($item[substr($lang,0,2)]) ? $item[substr($lang,0,2)] : $item['default']),ENT_QUOTES,$charset), $current_element_form);
				$infobulle_libelle = "";
				if ($controler->has_broader($uri,$params)) {
					$parents = $controler->get_broaders($uri,$params);
					if ($parents[0]["id"]){
						$infobulle_libelle .= $msg["onto_skos_concept_broader"]." ".$parents[0]["label"].". ";
					}
				} 				
				$infos = $controler->get_informations_concept($uri);
				if ($infos[0]["scopeNote"]) $infobulle_libelle .= $msg["onto_skos_concept_scopenote"]." ".$infos[0]["scopeNote"];
				$current_element_form = str_replace("!!infobulle_libelle!!", htmlentities($infobulle_libelle, ENT_QUOTES, $charset), $current_element_form);
				$elements_form.= $current_element_form;
			}
			$list = str_replace("!!elements_form!!", $elements_form, $list);
			$list = str_replace("!!aff_pagination!!", aff_pagination($params->base_url."&concept_scheme=".$params->concept_scheme."&parent_id=".$params->parent_id,$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ), $list);
		}else{
			$list = $msg["1915"];
		}
		return $list;
	}
	
	public static function get_search_form_selector($controler,$params){
		global $ontology_tpl,$jscript,$range_link_form;
		global $list_range_links_form;
		global $sel_no_available_search_form;
		global $msg;
	
		if($params->objs){
			$property=$controler->get_onto_property_from_pmb_name($params->objs);
			$element = $property->range[$params->range];
		}else {
			$element = $controler->get_class_uri($params->element);
		}
		$form = "";
		if($controler->get_searcher_class_name($element)){
			$search = $ontology_tpl['skos_concept_selector_search_form'];
			$search = str_replace("!!base_url!!", $params->base_url, $search);
			$search = str_replace("!!deb_rech!!", stripslashes(htmlentities($params->deb_rech,ENT_QUOTES,$charset)), $search);
			$form.= $search;
		}else{
			$form = $sel_no_available_search_form;
		}
		if (is_object($property) && count($property->range) > 1) {
			$range_links_form = "";
			foreach ($property->range as $i => $uri_class) {
				$current_range_link_form = $range_link_form;
				$current_range_link_form = str_replace("!!class!!", (($params->range == $i) ? "class='selected'" : ""), $current_range_link_form);
				$current_range_link_form = str_replace("!!href!!", $params->base_url."&range=".$i, $current_range_link_form);
				$current_range_link_form = str_replace("!!libelle!!", $controler->get_class_label($uri_class), $current_range_link_form);
	
				$range_links_form.= $current_range_link_form;
			}
			$range_links = str_replace("!!range_links_form!!", $range_links_form, $list_range_links_form);
			$form.= $range_links;
		}
		
		$onchange_scheme_list_selector='onchange="document.location =\''.$params->base_url.'&concept_scheme=\'+document.getElementById(\'id_concept_scheme\').value+\'&deb_rech=\'+document.getElementById(\'id_deb_rech\').value+\'&only_top_concepts='.$params->only_top_concepts.'\';"';
		$name_scheme_list_selector='concept_scheme';
		$id_scheme_list_selector='id_concept_scheme';
		$form=str_replace('!!skos_concept_search_form_selector!!', self::get_scheme_list_selector($controler, $params,false,$onchange_scheme_list_selector,$name_scheme_list_selector,$id_scheme_list_selector), $form);

		$onchange_only_top_concepts = 'onchange="document.location =\''.$params->base_url.'&concept_scheme=\'+document.getElementById(\'id_concept_scheme\').value+\'&deb_rech=\'+document.getElementById(\'id_deb_rech\').value+\'&only_top_concepts=\'+this.checked*1;"';
		$checked_only_top_concepts = '';
		if ($params->only_top_concepts) $checked_only_top_concepts = 'checked="checked"';
		
		$form=str_replace('!!only_top_concepts_onchange!!', $onchange_only_top_concepts, $form);
		$form=str_replace('!!only_top_concepts_checked!!', $checked_only_top_concepts, $form);
		
		//fil d'arianne
		$form=str_replace('!!skos_concept_search_form_href!!',$params->base_url ,$form);
		$form=str_replace('!!skos_concept_selector_breadcrumb!!',self::get_selector_breadcrumb($controler,$params) ,$form);
		$form.= $jscript;
		
		//ajout d'un nouveau concept
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_concept'])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_concept'];
		}else{
			$add_msg = sprintf($msg['onto_common_add'],$controler->get_label("concept"));
		}
		$form = str_replace("!!add_button_label!!", $add_msg, $form);
		$onclick = "document.location=\"./".$params->base_url."&concept_scheme=".$params->concept_scheme."&parent_id=".$params->parent_id."&action=selector_add\"";
		$form = str_replace("!!add_button_onclick!!", $onclick, $form);
		
		return $form;
	}
	
	public static function get_selector_breadcrumb($controler,$params){
		$breadcrumb=$controler->handle_breadcrumb();
		$return='';
		if(sizeof($breadcrumb)) {
			foreach($breadcrumb as $key=>$parent_id){
				if($return){
					$return.=' > ';
				}
				$return.="<a href='".$params->base_url."&parent_id=".$parent_id."'>".$controler->get_data_label(onto_common_uri::get_uri($parent_id)).'</a>';
			}
		}
		return $return;
	}
	
}
