<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_ui.class.php,v 1.1.6.2 2019-11-22 14:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");

class list_configuration_ui extends list_ui {
	
	protected static $module;
	
	protected static $categ;
	
	protected static $sub;
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 100;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_sur_location_activate;
		global $pmb_collstate_advanced;
	
		$this->available_columns =
		array('main_fields' =>
				$this->get_main_fields_from_sub()
		);
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		return $this->get_search_hidden_form();;
	}
	
	public function get_export_icons() {
		return "";
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', static::$categ, $display);
		$display = str_replace('!!sub!!', static::$sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_visible_flag($object, $property) {
		if ($object->{$property}) {
			return "X";
		} else {
			return "&nbsp;";
		}
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" onmousedown=\"document.location='".$this->get_edition_link($object)."';\" style='cursor: pointer'>";
		foreach ($this->columns as $column) {
			if($column['html']) {
				$display .= $this->get_display_cell_html_value($object, $column['html']);
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		return $display;
	}
	
	protected function get_button_add() {
		global $charset;
	
		return "<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=add';\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_add();
		return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub='.static::$sub;
	}
}