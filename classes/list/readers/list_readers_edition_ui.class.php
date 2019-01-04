<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_edition_ui.class.php,v 1.3 2018-12-28 16:30:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/readers/list_readers_ui.class.php");
require_once($include_path."/templates/list/readers/list_readers_edition_ui.tpl.php");

class list_readers_edition_ui extends list_readers_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_form_title() {
		return '';
	}
	
	protected function init_default_columns() {
		global $sub;
		
		if(count($this->get_selection_actions())) {
			$this->add_column_selection();
		}
		$this->add_column('cb');
		$this->add_column('empr_name');
		$this->add_column('adr1');
		$this->add_column('ville');
		$this->add_column('birth');
		$this->add_column('aff_date_expiration');
		$this->add_column('empr_statut_libelle');
		switch ($sub) {
			case "encours" :
				break;
			case "categ_change" :
				$this->add_column('categ_libelle');
				$this->add_column('categ_change');
				break;
			default :
				$this->add_column('relance', '');
				break;
		}
	}
		
	protected function get_display_spreadsheet_title() {
		global $titre_page;
		$this->spreadsheet->write_string(0,0,$titre_page);
	}
	
	protected function get_html_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_selection_actions() {
		global $msg;
		global $sub;
		global $current_module;
		global $empr_show_caddie;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			switch ($sub) {
				case 'categ_change':
					$link = array(
						'href' => static::get_controller_url_base()."&statut_action=modify",
						'confirm' => $msg["empr_categ_confirm_change"]
					);
					$this->selection_actions[] = $this->get_selection_action('change_categ', $msg["save_change_categ"], 'group_by_grey.png', $link);
					break;
				case 'limite':
				case 'depasse':
					$link = array(
						'href' => static::get_controller_url_base()."&action=print_all"
					);
					$this->selection_actions[] = $this->get_selection_action('print_all_relances', $msg["print_all_relances"], 'doc.gif', $link);
					break;
			}
			if ($empr_show_caddie) {
				$link = array();
				$link['openPopUp'] = "./cart.php?object_type=EMPR&action=add_empr_".$sub;
				$link['openPopUpTitle'] = 'cart';
				$this->selection_actions[] = $this->get_selection_action('add_empr_cart', $msg['add_empr_cart'], 'basket_20x20.gif', $link);
			}
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return "button";
	}
		
	protected function get_display_others_actions() {
		global $msg, $charset;
		
		return "
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
		<span class='right list_ui_other_action_empr_change_status ".$this->objects_type."_other_action_empr_change_status'>
			".$msg["empr_chang_statut"]."&nbsp;
			".gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle",$this->objects_type."_selection_action_empr_change_status","","",0,$msg['none'],0,$msg['none'])."
			&nbsp;<input type='button' id='".$this->objects_type."_other_action_empr_change_status_link' class='bouton_small' value='".$msg['empr_chang_statut_button']."' />
		</span>
		<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){
			on(dom.byId('".$this->objects_type."_other_action_empr_change_status_link'), 'click', function() {		
				var statut_action = domConstruct.create('input', {
					type : 'hidden',
					id : 'statut_action',
					name : 'statut_action',
					value : 'modify'
				});
				domConstruct.place(statut_action, dom.byId('".$this->objects_type."_search_form'));
						
				var change_status_hidden = domConstruct.create('input', {
					type : 'hidden',
					id : '".$this->objects_type."_empr_change_status',
					name : '".$this->objects_type."_empr_change_status',
					value : dom.byId('".$this->objects_type."_selection_action_empr_change_status').value
				});
				domConstruct.place(change_status_hidden, dom.byId('".$this->objects_type."_search_form'));
						
				dom.byId('".$this->objects_type."_search_form').submit();
			});
		});
		</script>";
	}
	
	public function run_action_add_caddie() {
		global $caddie;
		
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach($caddie as $id_caddie => $coche) {
				if($coche){
					$myCart = new empr_caddie($id_caddie);
					foreach ($selected_objects as $id) {
						$myCart->add_item($id);
					}
				}
			}
		}
	}
	
	public function run_change_status() {
		$change_status = $this->objects_type."_empr_change_status";
		global ${$change_status};
		if(!empty(${$change_status})) {
			foreach ($this->objects as $object) {
				$query = "UPDATE empr set empr_statut='".$$change_status."' where id_empr = ".$object->id;
				pmb_mysql_query($query);
				$object->set_empr_statut($$change_status);
			}
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $sub;
	
		return $base_path.'/edit.php?categ=empr&sub='.$sub;
	}
}