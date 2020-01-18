<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_relances_ui.class.php,v 1.1.2.3 2019-11-27 15:17:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/readers/list_readers_ui.class.php");
require_once($class_path."/emprunteur.class.php");

class list_readers_relances_ui extends list_readers_ui {
	
    protected $amendes;
    protected $levels;
    
    protected $list_dates_sort;
    protected $list_dates_relance;
    
	protected function _get_query_base() {
	    $query = 'SELECT id_empr, expl_id FROM empr
				JOIN pret ON pret_idempr=id_empr AND pret_retour<CURDATE()
                JOIN exemplaires ON pret_idexpl=expl_id
				JOIN empr_categ ON empr.empr_categ=empr_categ.id_categ_empr';
	    return $query;
	}
	
	protected function add_object($row) {
	    global $all_level;
	    
	    if ($all_level[$row->id_empr]) {
	        $this->levels[$row->id_empr] = $all_level[$row->id_empr];
	    } else {
	        $amende=$this->get_amende($row->id_empr);
	        $this->levels[$row->id_empr] = $amende->get_max_level();
	    }
	    if (($this->levels[$row->id_empr]["level_normal"])||($this->levels[$row->id_empr]["level_min"])) {
	        $emprunteur = new emprunteur($row->id_empr);
	        $emprunteur->set_pret_idexpl($row->expl_id);
	        $this->objects[] = $emprunteur;
	    }
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    parent::init_available_filters();
	    $this->add_custom_fields_available_filters('pret', 'pret_idexpl');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    parent::init_available_columns();
	    $this->add_custom_fields_available_columns('pret', 'pret_idexpl');
	    //Ajout de colonnes HORS XML
	    $this->available_columns['main_fields']['number_late'] = 'relance_nb_retard';
	    $this->available_columns['main_fields']['last_level'] = 'relance_dernier_niveau';
	    $this->available_columns['main_fields']['last_date'] = 'relance_date_derniere';
	    $this->available_columns['main_fields']['printed'] = 'relance_imprime';
	    $this->available_columns['main_fields']['supposed_level'] = 'relance_niveau_suppose';
	}
	
	protected function add_column_next_levels() {
		$this->columns[] = array(
				'property' => 'next_levels',
				'label' => "relance_action_prochaine",
				'html' => ""
		);
	}
	
	protected function add_column_list_actions() {
	    $this->columns[] = array(
	        'property' => 'list_actions',
	        'label' => "",
	        'html' => ""
	    );
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $mailretard_priorite_email;
		global $mailretard_priorite_email_2;
		global $mailretard_priorite_email_3;
		
		$content = '';
		switch($property) {
		    case 'empr_name':
		        $content .= "<a href='./circ.php?categ=pret&id_empr=".$object->id."'>".$object->nom." ".$object->prenom."</a>";
		        break;
		    case 'number_late':
		        $query = "select count(pret_idexpl) as empr_nb from empr,pret where id_empr = ".$object->id." and pret_retour<CURDATE() and pret_idempr=id_empr group by empr.id_empr";
		        $result = pmb_mysql_query($query);
		        $content .= pmb_mysql_result($result, 0, 0);
				break;
		    case 'last_level':
		        $content .= $this->levels[$object->id]["level_min"];
		        break;
		    case 'last_date':
		        $date_relance=$this->levels[$object->id]["level_min_date_relance"];
		        $list_dates = array();
		        if(!isset($this->list_dates_sort)) $this->list_dates_sort = array();
		        if(!isset($this->list_dates_relance)) $this->list_dates_relance = array();
		        $list_dates[$date_relance]=format_date($date_relance);
		        if ($this->levels[$object->id]["printed"]) {
		            $this->list_dates_relance[$date_relance]=$list_dates[$date_relance];
		            $dr=explode("-",$date_relance);
		            $this->list_dates_sort[$date_relance]=mktime(0,0,0,$dr[1],$dr[2],$dr[0]);
		        }
		        //Tri des dates
		        if (count($this->list_dates_sort)) {
		            arsort($this->list_dates_sort);
		        }
		        $content .= $list_dates[$date_relance];
		        break;
		    case 'printed':
		        $content .= ($this->levels[$object->id]["printed"] ? "X" : "");
		        break;
		    case 'supposed_level':
		        $content .= $this->levels[$object->id]["level_normal"];
		        break;
		    case 'next_levels':
		        $niveau_min = $this->levels[$object->id]["level_min"];
		        $niveau_normal = $this->levels[$object->id]["level_normal"];
		        $content .= relance::get_action($object->id,$niveau_min,$niveau_normal);
		        break;
		    case 'list_actions':
// 		        $script="envoi();";
// 		        $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$object->id."'; this.form.act.value='solo'; this.form.relance_solo.value='".$object->id."'; $script\"/>&nbsp;";
		        $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo&relance_solo=".$object->id."&action_".$object->id."='+document.getElementsByName('action_".$object->id."')[0].value+'#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>&nbsp;";
		        
		        //Si mail de rappel affecté au responsable du groupe
		        $requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$object->id." and resp_groupe and mail_rappel limit 1";
		        $res=pmb_mysql_query($requete);
		        if(pmb_mysql_num_rows($res) > 0) {
		            $requete="select id_empr, empr_mail from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
		            $result=pmb_mysql_query($requete);
		            $has_mail = (pmb_mysql_result($result, 0,1) ? 1 : 0);
		        } else {
		            $has_mail = ($object->mail ? 1 : 0);
		        }
		        $niveau_min = $this->levels[$object->id]["level_min"];
		        if ($niveau_min) {
// 		            $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$object->id."&niveau=".$niveau_min."','lettre'); this.form.act.value='solo_print'; this.form.relance_solo.value='".$object->id."'; $script\"/>";
		            $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$object->id."&niveau=".$niveau_min."','lettre'); document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo_print&relance_solo=".$object->id."#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>";
		            $flag_mail=false;
		            if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($has_mail))&&(($niveau_min<3)||($mailretard_priorite_email_3==1 && $niveau_min>=3))) {
		                $flag_mail=true;
		                if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
		                    //On force en lettre
		                    $flag_mail=false;
		                }
		            }
		            if ($flag_mail) {
// 		                $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"this.form.action = this.form.action + 'act=solo_mail&relance_solo=".$object->id."#relance_empr_".$object->id."'; this.form.act.value='solo_mail'; this.form.relance_solo.value='".$object->id."'; $script\"/>";
		                $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo_mail&relance_solo=".$object->id."#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>";
		            }
		        }
		      break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
	    switch ($property) {
	        case 'empr_name':
	        case 'categ_libelle':
	        case 'codestat_libelle':
	        case 'groups':
	        case 'list_actions':
	            $display = "<td>".$this->get_cell_content($object, $property)."</td>";
	            break;
	        default:
	            $display = parent::get_display_cell($object, $property);
	            break;
	    }
	    return $display;
	}
	
	protected function init_default_columns() {
	
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
	        $this->add_column('categ_libelle');
	        $this->add_column('codestat_libelle');
	        $this->add_column('groups');
	    }
		//Afficher les CP de prêt
		foreach ($this->custom_fields_available_columns as $name=>$column) {
		    if($column['type'] == 'pret') {
		        $parametres_perso = $this->get_custom_parameters_instance('pret');
		        $custom_id = $parametres_perso->get_field_id_from_name($name);
		        $this->add_column($name, $parametres_perso->t_fields[$custom_id]['TITRE']);
		    }
		}
		
		$this->add_column('number_late');
		$this->add_column('last_level');
		$this->add_column('last_date');
		$this->add_column('printed');
		$this->add_column('supposed_level');
		
		$this->add_column_next_levels();
		$this->add_column_list_actions();
	}
	
	protected function _get_query_order() {
	    $this->applied_sort_type = 'SQL';
	    return " group by id_empr order by empr_nom, empr_prenom";
	}
	
	public function get_export_icons() {
		return '';
	}
	
	protected function get_selection_mode() {
	    return 'button';
	}
	
	protected function get_link_action($action, $msg_confirm='') {
	    global $msg;
	    
	    return array(
	        'href' => static::get_controller_url_base()."&act=".$action,
	        'confirm' => $msg_confirm
	    );
	}
	
	protected function get_selection_actions() {
	    global $msg;
	    
	    if(!isset($this->selection_actions)) {
	        $this->selection_actions = array();
            $this->selection_actions[] = $this->get_selection_action('valid_all', $msg['relance_valid_all'], 'tick.gif', $this->get_link_action('valid'));
            $this->selection_actions[] = $this->get_selection_action('print_nonprinted', $msg['relance_print_nonprinted'], 'print.gif', $this->get_link_action('print'));
            $this->selection_actions[] = $this->get_selection_action('export', $msg['relance_export'], 'tableur.gif', $this->get_link_action('export'));
	    }
	    return $this->selection_actions;
	}
	
	protected function get_name_selected_objects() {
	    return "empr";
	}
	
	protected function add_event_on_selection_action($action=array()) {
	    global $msg;
	    
	    $display = "
			on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					selection.push(node.value);
				});
				if(selection.length) {
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
									name : '".$this->get_name_selected_objects()."[]',
									value : selected_option
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);

                                var next_actions_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : 'action_'+selected_option,
									value : selected_option
								});
								domConstruct.place(next_actions_hidden, selected_objects_form);
							});
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
						    : "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				} else {
					alert('".addslashes($msg['list_ui_no_selected'])."');
				}
			});
		";
	    return $display;
	}
	
	protected function get_display_others_actions() {
	    global $msg;
	    
	    $display = '';
	    if (count($this->list_dates_relance)) {
	        $display .= "
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
		<span class='right list_ui_other_action_relance_clear ".$this->objects_type."_other_action_relance_clear'>
			<input type='button' id='".$this->objects_type."_other_action_relance_clear_link' value='".addslashes($msg["print_relance_clear"])."' class='bouton'/>&nbsp;
            <select id='clear_date' name='clear_date'>
                <option value=''>".$msg["print_relance_clear_all"]."</option>";
	        foreach ($this->list_dates_sort as $val=>$stamp) {
	            $lib=$this->list_dates_relance[$val];
	            $display .= "<option value='$val'>".$lib."</option>\n";
	        }
	        $display .= "
            </select>
        </span>
		<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){
			on(dom.byId('".$this->objects_type."_other_action_relance_clear_link'), 'click', function() {
				var selected_clear_date = dom.byId('clear_date').options[dom.byId('clear_date').selectedIndex].text;
                if (confirm('".sprintf(addslashes($msg["confirm_print_relance_clear"]),"'+selected_clear_date+' ?'").")) {
                    var input_act = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'act',
    					name : 'act',
    					value : 'raz_printed'
    				});
                    domConstruct.place(input_act, dom.byId('".$this->objects_type."_search_form'));
                    var input_printed_cd = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'printed_cd',
    					name : 'printed_cd',
    					value : selected_clear_date
    				});
    				domConstruct.place(input_printed_cd, dom.byId('".$this->objects_type."_search_form'));
                    var input_relance_solo = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'relance_solo',
    					name : 'relance_solo',
    					value : ''
    				});
    				domConstruct.place(input_relance_solo, dom.byId('".$this->objects_type."_search_form'));
                }
				dom.byId('".$this->objects_type."_search_form').submit();
			});
		});
		</script>";
	    }
	    return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub;
	
		return $base_path.'/circ.php?categ='.$categ.'&sub='.$sub;
	}
		
	protected function init_correspondence_filters_fields() {
	    parent::init_correspondence_filters_fields();
	    $this->correspondence_filters_fields['main_fields']['2'] = 'last_level_validated';
	    $this->correspondence_filters_fields['main_fields']['3'] = 'supposed_level';
	}
	
	protected function init_correspondence_columns_fields() {
	    parent::init_correspondence_columns_fields();
	    $this->correspondence_columns_fields['main_fields']['2'] = 'last_level_validated';
	    $this->correspondence_columns_fields['main_fields']['3'] = 'supposed_level';
	}
	
	public function get_amende($id_empr) {
	    if(!isset($this->amendes[$id_empr])) {
	        $this->amendes[$id_empr] = new amende($id_empr);
	    }
	    return $this->amendes[$id_empr];
	}
}