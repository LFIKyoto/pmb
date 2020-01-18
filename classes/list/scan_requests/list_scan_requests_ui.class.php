<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scan_requests_ui.class.php,v 1.2.2.1 2019-11-22 14:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($class_path.'/scan_request/scan_requests.class.php');
require_once($class_path.'/scan_request/scan_request.class.php');
require_once($class_path.'/templates.class.php');
require_once($include_path.'/templates/list/scan_requests/list_scan_requests_ui.tpl.php');

class list_scan_requests_ui extends list_ui {
	
    protected $scan_requests; // Utilisé pour le Django
	
	protected function _get_query_base() {
		$query = 'select id_scan_request from scan_requests';
		return $query;
	}
	
	protected function add_object($row) {
	    $this->objects[] = new scan_request($row->id_scan_request);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    global $pmb_scan_request_location_activate;
	    
		$this->available_filters =
		array('main_fields' =>
				array(
						'status' => 'scan_request_form_status',
						'priority' => 'scan_request_form_priority',
						'user_only' => 'scan_request_user_only',
						'user_input' => 'global_search',
						'date' => 'scan_request_form_date',
			            'wish_date' => 'scan_request_form_wish_date',
				        'deadline_date' => 'scan_request_form_deadline_date'
				)
		);
		if($pmb_scan_request_location_activate) {
		    $this->available_filters['main_fields']['location'] = 'scan_request_location_search';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	    global $pmb_scan_request_location_activate;
	    
		$this->filters = array(
				'status' => '',
				'priority' => '',
                'user_only' => 0,
                'user_input' => '', 
				'date_start' => '',
				'date_end' => '',
                'wish_date_start' => '',
                'wish_date_end' => '',
                'deadline_date_start' => '',
                'deadline_date_end' => '',
		);
		if($pmb_scan_request_location_activate) {
		    $this->filters['location'] = '';
		}
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
	    global $pmb_scan_request_location_activate;
	    
	    $this->add_selected_filter('status');
		$this->add_selected_filter('priority');
		$this->add_selected_filter('user_only');
		$this->add_selected_filter('user_input');
		$this->add_empty_selected_filter();
		$this->add_empty_selected_filter();
		$this->add_selected_filter('date');
		$this->add_selected_filter('wish_date');
		$this->add_selected_filter('deadline_date');
		if($pmb_scan_request_location_activate) {
		    $this->add_selected_filter('location');
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'title' => 'scan_request_title',
					'creator_name' => 'scan_request_creator_name',
					'empr' => 'empr_nom_prenom',
					'date' => 'scan_request_date',
					'wish_date' => 'scan_request_wish_date',
                    'deadline_date' => 'scan_request_deadline_date',
                    'priority' => 'scan_request_priority',
                    'status' => 'scan_request_status'
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date', 'desc');
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
					$order .= 'id_scan_request';
					break;
				case 'title' :
				case 'desc' :
				case 'date':
				case 'wish_date':
				case 'deadline_date':
					$order .= 'scan_request_'.$sort_by;
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
	
	protected function get_form_title() {
	    global $msg, $charset;
	    return htmlentities($msg['scan_request_list_search'], ENT_QUOTES, $charset);
	}
	
	protected function get_button_add() {
	    global $msg, $base_path;
	    
	    return "<input class='bouton' type='button' value='".$msg["scan_request_add"]."' onClick=\"document.location='".$base_path."/circ.php?categ=scan_request&sub=request&action=edit'\" />";
	}
	
	public function get_export_icons() {
	   return '';    
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
        global $list_scan_requests_ui_search_form_tpl;
        global $scan_request_order_by;
        global $scan_request_order_by_sens;
        
        if(!$scan_request_order_by_sens)	$scan_request_order_by_sens='asc';
        
        $search_form = parent::get_search_form();
        $search_form .= $list_scan_requests_ui_search_form_tpl;
        $search_form .= "
        <input type='hidden' name='scan_request_order_by' id='scan_request_order_by' value='".$scan_request_order_by."'/>
        <input type='hidden' name='scan_request_order_by_sens' id='scan_request_order_by_sens' value='".$scan_request_order_by_sens."'/>";
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		$search_form = str_replace('!!objects_type!!', $this->objects_type, $search_form);
		return $search_form;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

	    $status = $this->objects_type.'_status';
	    global ${$status};
	    if(isset(${$status})) {
	        $this->filters['status'] = ${$status};
	    }
	    $priority = $this->objects_type.'_priority';
	    global ${$priority};
	    if(isset(${$priority})) {
	        $this->filters['priority'] = ${$priority};
	    }
	    $user_only = $this->objects_type.'_user_only';
	    global ${$user_only};
	    $this->filters['user_only'] = 0;
	    if(isset(${$user_only})) {
	        $this->filters['user_only'] = ${$user_only};
	    }
	    $user_input = $this->objects_type.'_user_input';
	    global ${$user_input};
	    if(isset(${$user_input})) {
	        $this->filters['user_input'] = ${$user_input};
	    }
	    
		$date_start = $this->objects_type.'_date_start';
		global ${$date_start};
		if(isset(${$date_start})) {
			$this->filters['date_start'] = ${$date_start};
		}
		$date_end = $this->objects_type.'_date_end';
		global ${$date_end};
		if(isset(${$date_end})) {
			$this->filters['date_end'] = ${$date_end};
		}
		$wish_date_start = $this->objects_type.'_wish_date_start';
		global ${$wish_date_start};
		if(isset(${$wish_date_start})) {
		    $this->filters['wish_date_start'] = ${$wish_date_start};
		}
		$wish_date_end = $this->objects_type.'_wish_date_end';
		global ${$wish_date_end};
		if(isset(${$wish_date_end})) {
		    $this->filters['wish_date_end'] = ${$wish_date_end};
		}
		$deadline_date_start = $this->objects_type.'_deadline_date_start';
		global ${$deadline_date_start};
		if(isset(${$deadline_date_start})) {
		    $this->filters['deadline_date_start'] = ${$deadline_date_start};
		}
		$deadline_date_end = $this->objects_type.'_deadline_date_end';
		global ${$deadline_date_end};
		if(isset(${$deadline_date_end})) {
		    $this->filters['deadline_date_end'] = ${$deadline_date_end};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_selection_actions() {
		global $msg;
		
		if(!isset($this->selection_actions)) {
		    $this->selection_actions = array();
		}
		return $this->selection_actions;
	}
	
	protected function add_column_expand() {
	    $this->columns[] = array(
	        'property' => '',
	        'label' => "<div class='center'>
							<i class='fa fa-plus-square' onclick='".$this->objects_type."_expand_all(document.".$this->get_form_name().");' style='cursor:pointer;'></i>
							&nbsp;
							<i class='fa fa-minus-square' onclick='".$this->objects_type."_collapse_all(document.".$this->get_form_name().");' style='cursor:pointer;'></i>
						</div>",
	        'html' => "<div class='center'><img style='border:0px; margin:3px 3px' onclick='expand_scan_request(!!id!!); return false;' id='scan_request_!!id!!_img' name='imEx' class='img_plus' src='./images/plus.gif'></div>"
	    );
	}
	
	protected function add_column_edit() {
	    $this->columns[] = array(
	        'property' => 'edit',
	        'label' => '',
	        'html' => "<img onclick=\"document.location='!!edit_link!!'\" class='icon' width='16' height='16' src='".get_url_icon('b_edit.png')."'>"
	    );
	}
	
	protected function init_default_columns() {
	
		$this->add_column_expand();
		$this->add_column('title');
		$this->add_column('creator_name');
		$this->add_column('empr');
		$this->add_column('date');
		$this->add_column('wish_date');
		$this->add_column('deadline_date');
		$this->add_column('priority');
		$this->add_column('status');
		$this->add_column_edit();
	}
	
	protected function get_search_filter_status() {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_status'>";
		$selector .= "<option value='-1' ".(($this->filters['status'] == -1) ? 'selected="selected"' : '').">".htmlentities($msg['scan_request_list_statuses_selector_open'], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='0' ".((!$this->filters['status']) ? 'selected="selected"' : '').">".htmlentities($msg['scan_request_list_statuses_selector_all'], ENT_QUOTES, $charset)."</option>";
        $selector .= scan_request_statuses::get_options($this->filters['status']);
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_filter_priority() {
	    global $msg, $charset;
	    
	    $selector = "<select name='".$this->objects_type."_priority'>";
	    $selector .= "<option value='0'>".htmlentities($msg['scan_request_list_priorities_selector_all'], ENT_QUOTES, $charset)."</option>";
        $selector .= scan_request_priorities::get_options($this->filters['priority']);
	    $selector .= "</select>";
	    return $selector;
	}
	
	protected function get_search_filter_user_only() {
	    $input = "<input type='checkbox' name='".$this->objects_type."_user_only' value='1' ".($this->filters['user_only'] ? "checked='checked'" : "")."/>";
	    return $input;
	}
	
	protected function get_search_filter_user_input() {
	    $input = "<input type='text' class='saisie-50em' name='".$this->objects_type."_user_input' value='".$this->filters['user_input']."'/>";
	    return $input;
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function get_search_filter_wish_date() {
	    return $this->get_search_filter_interval_date('wish_date');
	}
	
	protected function get_search_filter_deadline_date() {
	    return $this->get_search_filter_interval_date('deadline_date');
	}
	
	protected function get_search_filter_location() {
	    global $msg;
	    return gen_liste("select idlocation, location_libelle from docs_location order by location_libelle ", "idlocation", "location_libelle", $this->objects_type.'_location', "", $this->filters['location'], "", "", "0", $msg['all_location'],0);
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
	    global $PMBuserid;
	    
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		
		if($this->filters['status']) {
		    $filters [] = 'scan_request_num_status = "'.$this->filters['status'].'"';
		}
		if($this->filters['priority']) {
		    $filters [] = 'scan_request_num_priority = "'.$this->filters['priority'].'"';
		}
		if($this->filters['user_only']) {
		    $filters [] = 'scan_request_num_creator = "'.$PMBuserid.'" and scan_request_type_creator=1';
		}
		if($this->filters['user_input']) {
		    $filters [] = 'scan_request_title like "%'.$this->filters['user_input'].'%"';
		}
		if($this->filters['date_start']) {
			$filters [] = 'scan_request_date >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'scan_request_date <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		if($this->filters['wish_date_start']) {
		    $filters [] = 'scan_request_wish_date >= "'.$this->filters['wish_date_start'].'"';
		}
		if($this->filters['wish_date_end']) {
		    $filters [] = 'scan_request_wish_date <= "'.$this->filters['wish_date_end'].' 23:59:59"';
		}
		if($this->filters['deadline_date_start']) {
		    $filters [] = 'scan_request_deadline_date >= "'.$this->filters['deadline_date_start'].'"';
		}
		if($this->filters['deadline_date_end']) {
		    $filters [] = 'scan_request_deadline_date <= "'.$this->filters['deadline_date_end'].' 23:59:59"';
		}
		if($this->filters['location']) {
		    $filters [] = 'scan_request_num_location = "'.$this->filters['location'].'"';
		}
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
		$display = str_replace('!!categ!!', 'scan_request', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
		    case 'creator_name':
		        if($object->get_location_name()) {
		            $content .= $object->get_location_name()." / ";
		        }
		        $content .= $object->get_creator_name();
		        break;
		    case 'empr':

		        break;
			case 'date':
				$content .= $object->get_formatted_date();
				break;
			case 'wish_date':
			    $content .= $object->get_formatted_wish_date();
			    break;
			case 'deadline_date':
			    $content .= $object->get_formatted_deadline_date();
			    break;
			case 'priority':
			    $content .= $object->get_priority()->get_label();
			    break;
			case 'status':
			    $content .= "<span><img id='scan_request_img_statut_part_".$object->get_id()."' class='".$object->get_status()->get_class_html()."' style='width:7px; height:7px; vertical-align:middle; margin-left:-3px;' src='./images/spacer.gif'></span>";
			    $content .= $object->get_status()->get_label();
			    break;
			case 'edit':
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
	    $display = "<td class='center' onclick='scan_request_show_form(".$object->get_id().")' style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
	    return $display;
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
	    global $include_path;
	    global $scan_request_order_by, $scan_request_order_by_sens;
	    
	    $scan_request_order_by = $this->applied_sort[0]['by'];
	    $scan_request_order_by_sens = ($this->applied_sort[0]['asc_desc'] ? $this->applied_sort[0]['asc_desc'] : 'asc');
	    $display = '';
	    $tpl = $include_path.'/templates/scan_request/scan_requests_header_list.tpl.html';
	    if (file_exists($include_path.'/templates/scan_request/scan_requests_header_list_subst.tpl.html')) {
	        $tpl = $include_path.'/templates/scan_request/scan_requests_header_list_subst.tpl.html';
	    }
	    if(file_exists($tpl)) {
	        $h2o = H2o_collection::get_instance($tpl);
	        $this->scan_requests = $this->objects;
	        $display .= $h2o->render(array('scan_requests' => $this));
	    } else {
	        $display .= '<tr>';
	        foreach ($this->columns as $column) {
	            $display .= $this->_get_cell_header($column['property'], $column['label']);
	        }
	        $display .= '</tr>';
	    }
	    return $display;
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
	    global $include_path;
	    
	    $display = '';
	    $tpl = $include_path.'/templates/scan_request/scan_request_in_list.tpl.html';
	    if (file_exists($include_path.'/templates/scan_request/scan_request_in_list_subst.tpl.html')) {
	        $tpl = $include_path.'/templates/scan_request/scan_request_in_list_subst.tpl.html';
	    }
	    if(file_exists($tpl)) {
	       $h2o = H2o_collection::get_instance($tpl);
	       $empr = '';
	       if ($object->get_num_dest_empr()) {
	           $query = 'select empr_nom, empr_prenom from empr where id_empr = '.$object->get_num_dest_empr();
	           $result = pmb_mysql_query($query);
	           if (pmb_mysql_num_rows($result)) {
	               $row = pmb_mysql_fetch_object($result);
	               $empr = $row->empr_nom;
	               if($row->empr_prenom) $empr .= ', '.$row->empr_prenom;
	           }
	       }
	       $display .= $h2o->render(array('scan_request' => $object, 'empr' => $empr));
	    } else {
            $display .= "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\">";
    	    foreach ($this->columns as $column) {
    	        if($column['html']) {
    	            $display .= $this->get_display_cell_html_value($object, $column['html']);
    	        } else {
    	            $display .= $this->get_display_cell($object, $column['property']);
    	        }
    	    }
    	    $display .= "</tr>";
	    }
	    return $display;
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			case 'date':
				$grouped_label = substr($object->get_formatted_date(),0,10);
				break;
			case 'wish_date':
			    $grouped_label = substr($object->get_formatted_wish_date(),0,10);
			    break;
			case 'deadline_date':
			    $grouped_label = substr($object->get_formatted_deadline_date(),0,10);
			    break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	public static function delete() {
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach ($selected_objects as $id) {
			    $scan_request = new scan_request($id);
			    $scan_request->delete();
			}
		}
	}
	
	public function get_scan_requests() {
	    return $this->scan_requests;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
        return $base_path.'/circ.php?categ=scan_request&sub=list';
	}
}