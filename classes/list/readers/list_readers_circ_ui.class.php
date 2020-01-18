<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_circ_ui.class.php,v 1.1.2.4 2019-11-22 14:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/readers/list_readers_ui.class.php");
require_once($class_path."/emprunteur.class.php");

class list_readers_circ_ui extends list_readers_ui {
	
	protected function get_display_cell($object, $property) {
	    global $id_notice, $id_bulletin, $type_resa, $groupID;
	    // si on est en résa on a un id de notice ou de bulletin
	    if ($id_notice || $id_bulletin) {
	        //type_resa : on est en prévision
	        if ($type_resa) {
	            $onmousedown = "document.location=\"./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=".$object->id."&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
	        } else {
	            $onmousedown = "document.location=\"./circ.php?categ=resa&id_empr=".$object->id."&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
	        }
	    } else {
	        $onmousedown = "if(event.ctrlKey || event.metaKey) { window.open(\"./circ.php?categ=pret&form_cb=".$object->cb."\",\"_blank\"); } else { document.location=\"./circ.php?categ=pret&form_cb=".$object->cb."\"; }";
	    }
	    $display = "<td onmousedown='".$onmousedown."' style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
	    return $display;
	}
	
	protected function init_default_columns() {
	    global $empr_show_caddie;
	    
	    $this->add_column_selection();
	    if(!empty(static::$used_filter_list_mode)) {
	        $displaycolumns=explode(",",static::$filter_list->displaycolumns);
	        //parcours des champs
	        foreach ($displaycolumns as $displaycolumn) {
	            if(substr($displaycolumn,0,2) == "#e") {
	                $parametres_perso = $this->get_custom_parameters_instance('empr');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($displaycolumn,2));
	                $label = $this->get_label_available_column($custom_name, 'custom_fields');
	                $this->add_column($custom_name, $label);
	            } else {
	                $this->add_column($this->correspondence_columns_fields['main_fields'][$displaycolumn]);
	            }
	        }
	    } else {
	        $this->add_column('cb');
	        $this->add_column('empr_name');
	        $this->add_column('groups');
	        $this->add_column('adr1');
	        $this->add_column('ville');
	        $this->add_column('birth');
	        $this->add_column('nb_loans');
	        if($empr_show_caddie) {
	            $this->add_column('add_empr_cart');
	        }
	    }
	}
	
	protected function _get_query_order() {
	    $this->applied_sort_type = 'SQL';
	    return " group by id_empr order by empr_nom, empr_prenom";
	}
	
	public function get_export_icons() {
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ;
	
		return $base_path.'/circ.php?categ='.$categ;
	}
}