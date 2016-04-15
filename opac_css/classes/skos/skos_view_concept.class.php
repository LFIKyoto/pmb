<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_view_concept.class.php,v 1.14 2015-07-09 10:21:31 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_composee.class.php");
require_once($base_path."/cms/modules/common/includes/pmb_h2o.inc.php");
include_once($include_path."/templates/skos/skos_view_concept.tpl.php");
require_once($class_path."/skos/skos_view_concepts.class.php");

/**
 * class skos_view_concept
 * La vue d'un concept
*/
class skos_view_concept {
	
	/**
	 * Retourne l'affichage d'un concept
	 * @param array $datas Données
	 * @param string $template Nom du template à utiliser
	 * @return string
	 */
	static protected function render($datas, $template) {
		global $$template;
		
		return H2o::parseString($$template)->render(array("concept"=>$datas));
	}
	
	/**
	 * Retourne la génération d'un concept avec un lien vers chaque élément de sa composition s'il s'agit d'un concept composé
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept_in_list_with_all_links($concept) {
		if ($vedette = $concept->get_vedette()) {
			$vedette_elements = $vedette->get_elements();
			$datas['separator'] = $vedette->get_separator();
			$datas['elements'] = array();
			foreach ($vedette_elements as $elements) {
				foreach ($elements as $element) {
					$datas['elements'][] = array(
							'label' => $element->get_isbd(),
							'link' => str_replace("!!id!!", $element->get_db_id(), $element->get_lien_opac())
					);
				}
			}
			return self::render($datas, "skos_view_concept_concept_in_list_with_all_links");
		} else {
			// Sinon c'est un concept classique
			return self::get_concept_in_list($concept);
		}
	}
	
	/**
	 * Retourne la génération d'un concept classique
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept_in_list($concept) {
		global $liens_opac;
		
		$datas = array(
				'label' => $concept->get_display_label(),
				'link' => str_replace("!!id!!", $concept->get_id(), $liens_opac['lien_rech_concept'])
		);
		return self::render($datas, "skos_view_concept_concept_in_list");
	}

	/**
	 * Met en forme le libellé d'un concept
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept($concept) {
		$datas = array(
			'label' => $concept->get_display_label()
		);
		return self::render($datas, "skos_view_concept_concept");
	}
	
	/**
	 * Gère l'affichage de la grammaire si concept composé
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_detail_concept($concept) {
		$display_datas = array();
		
		$datas = $concept->get_details();
		$formatted_datas = array();
		foreach ($datas as $property => $values){
			$formatted_datas[$property]['values'] = $values; 
			$formatted_datas[$property]['label'] = skos_onto::get_property_label("http://www.w3.org/2004/02/skos/core#Concept", $property);
		}
		$display_datas['properties'] = $formatted_datas;
		
		if ($vedette = $concept->get_vedette()) {
			$vedette_elements = $concept->get_vedette()->get_elements();
			$datas['composed_concept_separator'] = $vedette->get_separator();
			$display_datas['composed_concept_elements'] = array();
			foreach ($vedette_elements as $subdivision => $elements) {
				foreach ($elements as $element) {
					$display_datas['composed_concept_elements'][$vedette->get_subdivision_name_by_code($subdivision)][] = array(
							'label' => $element->get_isbd(),
							'link' => str_replace("!!id!!", $element->get_db_id(), $element->get_lien_opac())
					);
				}
			}
		}
		return self::render($display_datas, "skos_view_concept_detail_concept");
	}
	
	/**
	 * Retourne l'affichage de la liste des notices indexées avec le concept
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_notices_indexed_with_concept($concept) {
		global $page;
		global $opac_nb_aut_rec_per_page, $opac_notices_depliable, $opac_allow_bannette_priv, $opac_nb_max_tri;
		global $allow_dsi_priv;
		global $begin_result_liste, $affich_tris_result_liste, $add_cart_link;
		global $include_path, $class_path, $base_path, $msg;
		global $opac_visionneuse_allow, $nbexplnum_to_photo, $link_to_visionneuse;
		global $opac_show_suggest, $opac_resa_popup;
		global $opac_allow_external_search;
		global $from;
		
		$indexed_notices = $concept->get_indexed_notices();
		
		if (!$page) $page=1;
		$debut =($page-1)*$opac_nb_aut_rec_per_page;
		if ($nbr_lignes = count($indexed_notices)) {
			// pour la DSI
			if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ($_SESSION['abon_cree_bannette_priv']==1 || $opac_allow_bannette_priv==2)) {
				print "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
			}
			
			if ($opac_notices_depliable) $content .= $begin_result_liste;
					
			//gestion du tri
			if (isset($_GET["sort"])) {	
				$_SESSION["last_sortnotices"]=$_GET["sort"];
			}
			if ($nbr_lignes>$opac_nb_max_tri) {
				$_SESSION["last_sortnotices"]="";
				$content .= "<span class=\"espaceResultSearch\">&nbsp;</span>";
			} else {
				$pos=strpos($_SERVER['REQUEST_URI'],"?");
				$pos1=strpos($_SERVER['REQUEST_URI'],"get");
				if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
				else $pos1=$pos1-3;
				$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
				$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
				$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
				$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
				$content .= $affich_tris_result_liste;
				if ($_SESSION["last_sortnotices"]!="") {
					require_once($class_path."/sort.class.php");
					$sort = new sort('notices','session');
					$content .= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>"; 
				}
			} 
			//fin gestion du tri
			
			$content .= $add_cart_link;
			
			if($opac_visionneuse_allow && $nbexplnum_to_photo){
				$content .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
				$sendToVisionneuseByGet = str_replace("!!mode!!","concept_see",$sendToVisionneuseByGet);
				$sendToVisionneuseByGet = str_replace("!!idautorite!!",$concept->get_id(),$sendToVisionneuseByGet);			
				$content .= $sendToVisionneuseByGet;
			}
			
			if ($opac_show_suggest) {
				$bt_sugg = "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";		
				if ($opac_resa_popup) $bt_sugg .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
				else $bt_sugg .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";			
				$bt_sugg.= " title='".$msg["empr_bt_make_sugg"]."' >".$msg['empr_bt_make_sugg']."</a></span>";
				$content .= $bt_sugg;
			}		
			
			rec_last_authorities();
			//affinage
			if ($main) {
				// Gestion des alertes à partir de la recherche simple
		 		include_once($include_path."/alert_see.inc.php");
		 		$content .= $alert_see_mc_values;
				
				//affichage
				$content .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='".$base_path."/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";	
				//Etendre
				if ($opac_allow_external_search) $content .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='".$base_path."/index.php?search_type_asked=external_search&mode_aff=aff_module&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
				//fin etendre			
			} else {
				// Gestion des alertes à partir de la recherche simple
		 		include_once($include_path."/alert_see.inc.php");
		 		$content .= $alert_see_mc_values;
	
				//affichage
				$content .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_".($from=="search" ? "simple_search" : "module")."' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
				//Etendre
				if ($opac_allow_external_search) $content .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
				
				//fin etendre
			}
			//fin affinage
		
			foreach ($indexed_notices as $notice_id) {
				$content .= aff_notice($notice_id, 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode);
			}
		}
		return $content;
	}
	
	/**
	 * Retourne l'affichage de la liste des autorités indexées avec le concept
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_authorities_indexed_with_concept($concept) {
		global $msg, $liens_opac, $charset;
		
		$indexed_authorities = $concept->get_indexed_authorities();
		foreach ($indexed_authorities as $type => $authorities) {
			foreach ($authorities as $authority) {
				switch ($type) {
					case 'author' :
						if (!isset($datas['authorities']['author'])) {
							$datas['authorities']['author'] = array('type_name' => $msg['isbd_author'], 'elements' => array());
						}
						$datas['authorities']['author']['elements'][] = array(
								'label' => $authority->isbd_entry,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_auteur'])
						);
						break;
					case 'category':
						if (!isset($datas['authorities']['category'])) {
							$datas['authorities']['category'] = array('type_name' => $msg['isbd_categories'], 'elements' => array());
						}
						$datas['authorities']['category']['elements'][] = array(
								'label' => $authority->libelle,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_categ'])
						);
						break;
					case 'publisher' :
						if (!isset($datas['authorities']['publisher'])) {
							$datas['authorities']['publisher'] = array('type_name' => $msg['isbd_editeur'], 'elements' => array());
						}
						$datas['authorities']['publisher']['elements'][] = array(
								'label' => $authority->display,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_editeur'])
						);
						break;
					case 'collection' :
						if (!isset($datas['authorities']['collection'])) {
							$datas['authorities']['collection'] = array('type_name' => $msg['isbd_collection'], 'elements' => array());
						}
						$datas['authorities']['collection']['elements'][] = array(
								'label' => $authority->isbd_entry,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_collection'])
						);
						break;
					case 'subcollection' :
						if (!isset($datas['authorities']['subcollection'])) {
							$datas['authorities']['subcollection'] = array('type_name' => $msg['isbd_subcollection'], 'elements' => array());
						}
						$datas['authorities']['subcollection']['elements'][] = array(
								'label' => $authority->name,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_subcollection'])
						);
						break;
					case 'serie' :
						if (!isset($datas['authorities']['serie'])) {
							$datas['authorities']['serie'] = array('type_name' => $msg['isbd_serie'], 'elements' => array());
						}
						$datas['authorities']['serie']['elements'][] = array(
								'label' => $authority->name,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_serie'])
						);
						break;
					case 'titre_uniforme' :
						if (!isset($datas['authorities']['titre_uniforme'])) {
							$datas['authorities']['titre_uniforme'] = array('type_name' => $msg['isbd_titre_uniforme'], 'elements' => array());
						}
						$datas['authorities']['titre_uniforme']['elements'][] = array(
								'label' => $authority->name,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_titre_uniforme'])
						);
						break;
					case 'indexint' :
						if (!isset($datas['authorities']['indexint'])) {
							$datas['authorities']['indexint'] = array('type_name' => $msg['isbd_indexint'], 'elements' => array());
						}
						$label = "";
						if ($authority->name_pclass) {
							$label .= "[".$authority->name_pclass."] ";
						}
						
						$label .= $authority->name;
						
						if ($authority->comment) {
							$label .= " - ".$authority->comment;
						}
						$datas['authorities']['indexint']['elements'][] = array(
								'label' => $label,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_indexint'])
						);
						break;
					case 'expl' :
						break;
					case 'explnum' :
						break;
					case 'authperso' :
						$authority_name = ($charset != 'utf-8' ? utf8_decode($authority->info['authperso']['name']) : $authority->info['authperso']['name']);
						if (!isset($datas['authorities'][$authority_name])) {
							$datas['authorities'][$authority_name] = array('type_name' => $authority_name, 'elements' => array());
						}
						$datas['authorities'][$authority_name]['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_authperso'])
						);
						break;
				}
			}
		}
		return self::render($datas, "skos_view_concept_authorities_indexed_with_concept");
	}
}