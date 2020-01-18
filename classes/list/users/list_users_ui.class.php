<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_users_ui.class.php,v 1.1.2.1 2019-11-27 15:39:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($class_path.'/user.class.php');

class list_users_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT * FROM users';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
	    $this->selected_filters = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('username');
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 0;
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
	    if($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'id':
					$order .= 'userid';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort[0]['asc_desc']; 
			} else {
				return "";
			}
		}	
	}
	
	public function get_export_icons() {
		return "
		";
	}
	
	protected function get_button_add() {
		global $msg;
		
		return "<input type='button' class='bouton' value='".$msg['85']."' onClick=\"document.location='".static::get_controller_url_base().'&action=add'."';\" />";
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if(!count($humans)) {
			$humans[] = "<b>".htmlentities($msg['list_ui_no_filter'], ENT_QUOTES, $charset)."</b>";
		}
		return $this->get_display_query_human($humans);;
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'users', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $opac_url_base;
		
		$content = '';
		switch($property) {
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		switch($property) {
			default:
				$display = "<td class='center' onclick=\"window.location='".static::get_controller_url_base()."&action=view&suite=acces&id_rss_flux=".$object->id_rss_flux."'\" style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
				break;
		}
		return $display;
	}
	
	protected function get_display_permission_access($permission_access=0) {
	    if($permission_access) {
	        return '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>';
	    } else {
	        return '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>';
	    }
	}
	
	protected function get_display_ask_alert_mail($name, $alert_mail=0) {
	    global $msg;
	    global $admin_user_alert_row;
	    
	    if($alert_mail) {
	        return str_replace("!!user_alert!!", $msg[$name].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
	    } else {
	        return '';
	    }
	}
	
	protected function get_display_content_object_list($object, $indice) {
	    global $msg;
	    global $admin_user_list;
	    global $admin_user_link1;
	    
	    // réinitialisation des chaînes
	    $dummy = $admin_user_list;
	    $dummy1 = $admin_user_link1;
	    
	    $flag = "<img src='./images/flags/".$object->user_lang.".gif' width='24' height='16' vspace='3'>";
	    
	    $dummy =str_replace('!!user_link!!', $dummy1, $dummy);
	    $dummy =str_replace('!!user_name!!', "$object->prenom $object->nom", $dummy);
	    $dummy =str_replace('!!user_login!!', $object->username, $dummy);
	    
	    $dummy =str_replace('!!nuseradmin!!', $this->get_display_permission_access($object->rights & ADMINISTRATION_AUTH), $dummy);
	    $dummy =str_replace('!!nusercatal!!', $this->get_display_permission_access($object->rights & CATALOGAGE_AUTH), $dummy);
	    $dummy =str_replace('!!nusercirc!!', $this->get_display_permission_access($object->rights & CIRCULATION_AUTH), $dummy);
	    $dummy =str_replace('!!nuserpref!!', $this->get_display_permission_access($object->rights & PREF_AUTH), $dummy);
	    $dummy =str_replace('!!nuseracquisition_account_invoice!!', $this->get_display_permission_access($object->rights & ACQUISITION_ACCOUNT_INVOICE_AUTH), $dummy);
	    $dummy =str_replace('!!nuserauth!!', $this->get_display_permission_access($object->rights & AUTORITES_AUTH), $dummy);
	    $dummy =str_replace('!!nuseredit!!', $this->get_display_permission_access($object->rights & EDIT_AUTH), $dummy);
	    $dummy =str_replace('!!nusereditforcing!!', $this->get_display_permission_access($object->rights & EDIT_FORCING_AUTH), $dummy);
	    $dummy =str_replace('!!nusersauv!!', $this->get_display_permission_access($object->rights & SAUV_AUTH), $dummy);
	    $dummy =str_replace('!!nuserdsi!!', $this->get_display_permission_access($object->rights & DSI_AUTH), $dummy);
	    $dummy =str_replace('!!nuseracquisition!!', $this->get_display_permission_access($object->rights & ACQUISITION_AUTH), $dummy);
	    $dummy =str_replace('!!nuserrestrictcirc!!', $this->get_display_permission_access($object->rights & RESTRICTCIRC_AUTH), $dummy);
	    $dummy =str_replace('!!nuserthesaurus!!', $this->get_display_permission_access($object->rights & THESAURUS_AUTH), $dummy);
	    $dummy =str_replace('!!nusertransferts!!', $this->get_display_permission_access($object->rights & TRANSFERTS_AUTH), $dummy);
	    $dummy =str_replace('!!nuserextensions!!', $this->get_display_permission_access($object->rights & EXTENSIONS_AUTH), $dummy);
	    $dummy =str_replace('!!nuserdemandes!!', $this->get_display_permission_access($object->rights & DEMANDES_AUTH), $dummy);
	    $dummy =str_replace('!!nusercms!!', $this->get_display_permission_access($object->rights & CMS_AUTH), $dummy);
	    $dummy =str_replace('!!nusercms_build!!', $this->get_display_permission_access($object->rights & CMS_BUILD_AUTH), $dummy);
	    $dummy =str_replace('!!nuserfiches!!', $this->get_display_permission_access($object->rights & FICHES_AUTH), $dummy);
	    $dummy =str_replace('!!nusermodifcbexpl!!', $this->get_display_permission_access($object->rights & CATAL_MODIF_CB_EXPL_AUTH), $dummy);
	    $dummy =str_replace('!!nusersemantic!!', $this->get_display_permission_access($object->rights & SEMANTIC_AUTH), $dummy);
	    $dummy =str_replace('!!nuserconcepts!!', $this->get_display_permission_access($object->rights & CONCEPTS_AUTH), $dummy);
	    $dummy =str_replace('!!nusermodelling!!', $this->get_display_permission_access($object->rights & MODELLING_AUTH), $dummy);
	    
	    
        $dummy = str_replace('!!lang_flag!!', $flag, $dummy);
        $dummy = str_replace('!!nuserlogin!!', $object->username, $dummy);
        $dummy = str_replace('!!nuserid!!', $object->userid, $dummy);
              
        $dummy =str_replace('!!user_alert_resamail!!', $this->get_display_ask_alert_mail('alert_resa_user_mail', $object->user_alert_resamail), $dummy);
        $dummy =str_replace('!!user_alert_demandesmail!!', $this->get_display_ask_alert_mail('alert_demandes_user_mail', $object->user_alert_demandesmail), $dummy);
        $dummy =str_replace('!!user_alert_subscribemail!!', $this->get_display_ask_alert_mail('alert_subscribe_user_mail', $object->user_alert_subscribemail), $dummy);
        $dummy =str_replace('!!user_alert_suggmail!!', $this->get_display_ask_alert_mail('alert_sugg_user_mail', $object->user_alert_suggmail), $dummy);
        $dummy =str_replace('!!user_alert_serialcircmail!!', $this->get_display_ask_alert_mail('alert_subscribe_serialcirc_mail', $object->user_alert_serialcircmail), $dummy);
                                                
        $dummy = str_replace('!!user_created_date!!', $msg['user_created_date'].format_date($object->create_dt), $dummy);
                    
	    return $dummy;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
	    $display = '';
	    //Affichage de la liste des objets
	    if(count($this->objects)) {
	        $display .= $this->get_display_content_list();
	    }
	    if(count($this->get_selection_actions())) {
	        $display .= $this->get_display_selection_actions();
	    }
	    $display .= $this->get_display_others_actions();
	    $display .= $this->pager();
	    $display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
			</div>
			<div class='right'>
			</div>
		</div>";
	    return $display;
	}
	
	protected function get_link_action($action, $act) {
	    global $msg;
	    
	    return array(
	        'href' => static::get_controller_url_base()."&action=".$action,
	        'confirm' => ''
	    );
	}
	
	protected function get_selection_actions() {
	    global $msg;
	    
	    if(!isset($this->selection_actions)) {
	        $this->selection_actions = array();
	        //Bouton modifier
	        $link = array();
//	        $this->selection_actions[] = $this->get_selection_action('edit', $msg['62'], 'b_edit.png', $link);
	        
	        //Bouton supprimer
//	        $this->selection_actions[] = $this->get_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'delete'));
	    }
	    return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
	    return 'icon-dialog';
	}
	
	protected function get_display_selection_actions() {
	    $display = parent::get_display_selection_actions();
	    $display .= "<script type='text/javascript'>
            require(['dojo/ready', 'apps/pmb/Users'], function(ready, Users){
                ready(function(){
                    new Users();
                });
            });
       </script>";
	    return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/admin.php?categ=users&sub=users';
	}
}