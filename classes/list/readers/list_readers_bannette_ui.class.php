<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_bannette_ui.class.php,v 1.2 2018-12-28 16:30:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/readers/list_readers_ui.class.php");
require_once($include_path."/templates/list/readers/list_readers_bannette_ui.tpl.php");
require_once($class_path."/bannette.class.php");

class list_readers_bannette_ui extends list_readers_ui {
	
	protected $id_bannette;
	
	protected $bannette;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['dsi_ban_lec_assoce'], ENT_QUOTES, $charset)." : ".$this->get_bannette()->nom_bannette;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
		global $pmb_lecteurs_localises;
		global $list_readers_bannette_ui_search_filters_form_tpl;
		global $faire;
		
		$search_filters_form = $list_readers_bannette_ui_search_filters_form_tpl;
		if($faire == "enregistrer") {
			$search_filters_form = str_replace("!!bannette_lecteurs_saved!!", "<div class='erreur'>".$msg["dsi_bannette_lecteurs_update"]."</div><br />", $search_filters_form);
		} else {
			$search_filters_form = str_replace("!!bannette_lecteurs_saved!!", "", $search_filters_form);
		}
		$search_filters_form = str_replace('!!categories!!', $this->get_multiple_selector($this->get_selector_query('categories'), 'categories', $msg['dsi_all_categories']), $search_filters_form);
		$search_filters_form = str_replace('!!groups!!', $this->get_multiple_selector($this->get_selector_query('groups'), 'groups', $msg['dsi_all_groups']), $search_filters_form);
		if ($pmb_lecteurs_localises) {
			$search_filters_form = str_replace('!!locations!!', $this->get_multiple_selector($this->get_selector_query('locations'), 'locations', $msg['all_location']), $search_filters_form);
		}
		$search_filters_form = str_replace('!!name!!', $this->filters['name'], $search_filters_form);
		$search_filters_form = str_replace('!!has_mail_unchecked!!', (!$this->filters['has_mail'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!has_mail_checked!!', ($this->filters['has_mail'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!has_affected_unchecked!!', (!$this->filters['has_affected'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!has_affected_checked!!', ($this->filters['has_affected'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!mail!!', $this->filters['mail'], $search_filters_form);
		$search_filters_form = str_replace('!!objects_type!!', $this->objects_type, $search_filters_form);
		return $search_filters_form;
	}
	
	/**
	 * Jointure externes SQL pour les lecteurs affectés
	 */
	protected function _get_query_join_filter_affected() {
		
		return " JOIN bannette_abon ON bannette_abon.num_empr = empr.id_empr";
	}
	
	/**
	 * Filtre SQL pour les lecteurs affectés
	 */
	protected function _get_query_filter_affected() {
		global $id_bannette;
		
		$filter_query_affected = '';
		if($this->id_bannette || $id_bannette) {
			$filter_query_affected = 'bannette_abon.num_bannette = "'.($this->id_bannette ? $this->id_bannette : $id_bannette).'"';
		}
		return $filter_query_affected;
	}
	
	protected function get_search_buttons_extension() {
		global $base_path;
		global $msg;
		global $form_cb;
		
		return "
			<input type='button' class='bouton' value=\"".$msg['bt_retour']."\" onClick=\"document.location='".$base_path."/dsi.php?categ=bannettes&sub=pro&id_bannette=&suite=search&form_cb=".$form_cb."';\" />
			<input type='button' class='bouton' value=\"".$msg['dsi_ban_affect_equation']."\" onclick=\"document.location='".$base_path."/dsi.php?categ=bannettes&sub=pro&suite=affect_equation&id_bannette=".$this->id_bannette."&form_cb=".$form_cb."'\"/>
			";
	}
	
	protected function add_column_mails_selection() {
		$this->columns[] = array(
				'property' => 'mails_selection',
				'label' => "",
				'html' => ""
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'mails_selection':
				$mails_selection = '';
				if ($object->mail){
					$requete_affect = "SELECT * FROM bannette_abon where num_empr='".$object->id."' and num_bannette='".$this->id_bannette."' ";
					$res_affect = pmb_mysql_query($requete_affect);
					if (pmb_mysql_num_rows($res_affect)){
						$abon=pmb_mysql_fetch_object($res_affect);
					}
					$destinataires = explode(";",$object->mail) ;
					if(count($destinataires)>1){
						$mails_selection="<select name='".$this->objects_type."_mails_selection[".$object->id."]' data-empr-id='".$object->id."' class='".$this->objects_type."_mails_selection'>";
						if(!isset($abon->bannette_mail) || !$abon->bannette_mail) $selected=" selected='selected' ";
						$mails_selection.="<option value ='' $selected>".$msg["dsi_ban_form_mail_all"]."</option>";
						foreach($destinataires as $mail){
							$selected="";
							if(isset($abon) && $mail == $abon->bannette_mail)  $selected=" selected='selected' ";
							$mails_selection.="<option value ='$mail' $selected >$mail</option>";
						}
						$mails_selection.="</select>";
					}
				}
				$content .= $mails_selection;
				break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function add_column_selection() {
		global $msg, $charset;
	
		// Bloc HTML toggle
// 		<input type='checkbox' id='".$this->objects_type."_selection_!!id!!' name='".$this->objects_type."_selection[!!id!!]' class='".$this->objects_type."_selection switch' value='!!id!!' !!subscribed!!>
// 		<label for='".$this->objects_type."_selection_!!id!!'>".htmlentities($msg['dsi_ban_affect_lecteur'], ENT_QUOTES, $charset)."</label>
		$this->columns[] = array(
				'property' => '',
				'label' => "<div class='center'>
							<i class='fa fa-plus-square' onclick='".$this->objects_type."_selection_all(document.".$this->get_form_name().");' style='cursor:pointer;' title='".htmlentities($msg['tout_cocher_checkbox'], ENT_QUOTES, $charset)."'></i>
							&nbsp;
							<i class='fa fa-minus-square' onclick='".$this->objects_type."_unselection_all(document.".$this->get_form_name().");' style='cursor:pointer;' title='".htmlentities($msg['tout_decocher_checkbox'], ENT_QUOTES, $charset)."'></i>
						</div>",
				'html' => "<div class='center'>
							<input type='checkbox' id='".$this->objects_type."_selection_!!id!!' name='".$this->objects_type."_selection[!!id!!]' class='".$this->objects_type."_selection' value='!!id!!' !!subscribed!!>
						</div>"
		
		);
	}
	
	protected function get_display_cell_html_value($object, $value) {
		if(method_exists($object, 'get_id')) {
			$value = str_replace('!!subscribed!!', ($this->get_bannette()->is_subscribed($object->get_id()) ? "checked='checked'" : ""), $value);
		} else {
			$value = str_replace('!!subscribed!!', ($this->get_bannette()->is_subscribed($object->id) ? "checked='checked'" : ""), $value);
		}
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function init_default_columns() {
	
		$this->add_column_selection();
		$this->add_column('empr_name');
		$this->add_column('mail');
		$this->add_column_mails_selection();
	}
	
	protected function init_columns($columns=array()) {
		parent::init_columns($columns);
		if(count($this->selected_columns)) {
			$this->add_column_mails_selection();
		}
	}
	
	protected function get_selection_actions() {
		global $msg;
		global $base_path;
		global $id_bannette;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			if($this->id_bannette || $id_bannette) {
				$link = array();
				$link['href'] = $base_path."/dsi.php?categ=bannettes&sub=pro&id_bannette=".($this->id_bannette ? $this->id_bannette : $id_bannette)."&suite=affect_lecteurs&faire=enregistrer";
					
				$this->selection_actions[] = $this->get_selection_action('save', $msg['77'], 'sauv.gif', $link);
			}
		}
		return $this->selection_actions;
	}

	protected function add_events_on_selection_actions() {
		$display = "<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){";
		foreach($this->get_selection_actions() as $action) {
			$display .= "
				on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
					var selection = new Array();
					query('.".$this->objects_type."_selection:checked').forEach(function(node) {
						selection.push(node.value);
					});
					var mails_selection = new Array();
					query('.".$this->objects_type."_mails_selection').forEach(function(node) {
						mails_selection.push(node);
					});
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
								name : '".$this->objects_type."_selected_objects_form',
								id : '".$this->objects_type."_selected_objects_form',
								method : 'POST'
							});
							selection.forEach(function(selected_option) {
								var selected_objects_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->objects_type."_selected_objects[]',
									value : selected_option
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);
							});
							mails_selection.forEach(function(selector_node) {
								var empr_id = selector_node.getAttribute('data-empr-id');
								var selected_mails_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->objects_type."_mails_selection['+empr_id+']',
									value : selector_node.value
								});
								domConstruct.place(selected_mails_hidden, selected_objects_form);
							});
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
									: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				});";
		}
		$display .= "});
		</script>";
		return $display;
	}
	
	public function get_export_icons() {
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub, $id_bannette, $suite;
	
		return $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub.'&id_bannette='.$id_bannette.'&suite='.$suite;
	}
	
	public function run_action_affect_lecteurs() {
		$selected_objects = static::get_selected_objects();
		if(count($selected_objects)) {
			$name = $this->objects_type."_mails_selection";
			global ${$name};
			$sel_mail = ${$name};
			foreach ($this->objects as $object) {
				pmb_mysql_query("delete from bannette_abon where num_empr='".$object->id."' and num_bannette='".$this->id_bannette."'");
				if(in_array($object->id, $selected_objects)) {
					pmb_mysql_query("insert into bannette_abon set num_empr='".$object->id."', num_bannette='".$this->id_bannette."', bannette_mail='".$sel_mail[$object->id]."'");
				}
			}
		}
	}
	
	public function set_id_bannette($id_bannette) {
		$this->id_bannette = $id_bannette+0;
	}
	
	public function get_bannette() {
		if(!isset($this->bannette)) {
			$this->bannette = new bannette($this->id_bannette);
		}
		return $this->bannette;
	}
}