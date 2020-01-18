<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_authorities.class.php,v 1.9 2019-09-04 13:20:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_authorities extends frbr_entity_common_datasource_aut_link {
	
	protected $link_type;
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_sub_datasources() {
	    return [];
	}
	
	protected function get_query($datas = array()) {
		$sub_query = "SELECT aut_link_from_num as id, aut_link_to_num as parent 
		              FROM aut_link WHERE aut_link_to = ".$this->authority_type;
		if (!empty($this->link_type) && $this->link_type[0] != "0") {
		    // Si le link_type = 0, alors il ne faut spécifier aucun link_type
		    if (!is_array($this->link_type)) {
		        $this->link_type = array($this->link_type);
		    }
		    foreach ($this->link_type as $key => $link_type) {
		        if (empty($key)) {
		            $sub_query .= " AND (aut_link_type = '$link_type'";
		        } else {
		            $sub_query .= " OR aut_link_type = '$link_type'";
		        }
		    }
		    $sub_query .= ")";
		}
		if($this->get_parent_type() !== 'authperso'){
            $aut_link_from = authority::get_const_type_object($this->get_parent_type());
		}else{
		    $query = "select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority = ".$datas[0];
		    $result = pmb_mysql_query($query);
		    $aut_link_from = pmb_mysql_result($result,0,0);
		    $aut_link_from = 1000 + intval($aut_link_from);
		}
		$sub_query .= " AND aut_link_from = ".$aut_link_from;
		$sub_query .= " AND aut_link_from_num IN (".implode(',', $datas).")";
		return $sub_query;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas = array()) {
		$query = $this->get_query($datas);
		$result = pmb_mysql_query($query);
		$datas = array();
		while ($row = pmb_mysql_fetch_object($result)) {
			$datas[$row->parent][] = $row->id;
			$datas[0][] = $row->id;
		}
		if (isset($datas[0])) {
		    $datas[0] = parent::get_datas($datas[0]);
		}
		if (!empty($this->used_external_filter)) {
			foreach($datas as $parent => $data) {
				if (!empty($parent)) {
					$datas[$parent] = $this->external_filter->filter_datas($data);
				}
			}
		}
		return $datas;
	}
	
	public function get_link_type() {
		return $this->link_type;
	}
	
	public function set_link_type($link_type) {
		$this->link_type = $link_type;
		return $this;
	}
	
	public function get_form() {
	    $form = parent::get_form();
	    $form .= "<div class='row'>
					<div class='colonne3'>
						<label for='aut_link_type_parameter'>".$this->format_text($this->msg['frbr_entity_common_datasource_aut_link_type'])."</label>
					</div>
					<div class='colonne-suite'>
						".$this->get_link_selector()."
					</div>
				</div>";
	    return $form;
	}
	
	private function get_link_selector() {
	    global $charset;
	    
	    if (!isset($this->parameters->link_type)) $this->parameters->link_type = array();
	    $display = "<select name='aut_link_type_parameter[]' id='aut_link_type_parameter' multiple>";
	    $display .= "<option value='0'".(empty($this->parameters->link_type) || $this->parameters->link_type[0] == "0" ? 'selected' : '').">".$this->msg['frbr_entity_common_datasource_aut_link_all_links']."</option>";
	    $source = marc_list_collection::get_instance('aut_link');
	    $aut_link = $source->table;
	    foreach ($aut_link as $value => $libelle) {
	        if (is_array($libelle)) {
	            $display .= "<optgroup label='".htmlentities($value, ENT_QUOTES, $charset)."'>";
	            foreach ($libelle as $key => $val) {
	                $selected = "";
	                if (is_string($this->parameters->link_type)) {
	                    // Il peut rester des link_type sous forme de chaine en base tant que l'utilisateur n'a pas ré-enregistré sa page FRBR
	                    $this->parameters->link_type = array($this->parameters->link_type);
	                }
	                foreach ($this->parameters->link_type as $link) {
	                    if ($key == $link) {
	                        $selected = "selected='selected'";
	                        break;
	                    }
	                }
	                $display .= "<option value='$key' $selected>".htmlentities($val, ENT_QUOTES, $charset)."</option>";
	            }
	            $display .= "</optgroup>";
	        } else {
	            $selected = "";
	            foreach ($this->parameters->link_type as $link) {
	                if ($key == $link) {
	                    $selected = "selected='selected'";
	                    break;
	                }
	            }
	            $display .= "<option value='$key' $selected>".htmlentities($val, ENT_QUOTES, $charset)."</option>";
	        }
	    }
	    $display .= "</select>";
	    return $display;
	}
}