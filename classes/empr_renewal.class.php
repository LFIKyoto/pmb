<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_renewal.class.php,v 1.4.2.1 2019-11-08 10:55:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class empr_renewal extends empr_form {
    
	protected function get_template() {
	    global $empr_renewal_form;
	    return $empr_renewal_form;
        }
        
    public function save() {
        global $empr_renewal_activate;
        
        if (empty($this->empr_fields)) {
            return false;
        }
        pmb_mysql_query("TRUNCATE TABLE empr_renewal_form_fields");
        
        $values = array();
        foreach ($this->empr_fields as $empr_field_code => $options) {
            $values[] = "('".addslashes($empr_field_code)."', ".$options['display'].", ".$options['mandatory'].", ".$options['alterable'].", '".addslashes($options['explanation'])."')";
        }
        $query = "INSERT INTO empr_renewal_form_fields (empr_renewal_form_field_code, empr_renewal_form_field_display, empr_renewal_form_field_mandatory, empr_renewal_form_field_alterable, empr_renewal_form_field_explanation)
			VALUES ".implode(',', $values);
        pmb_mysql_query($query);
        
        $query = "UPDATE parametres SET valeur_param=" . $this->active . " WHERE type_param='empr' and sstype_param='renewal_activate' ";
        pmb_mysql_query($query);
        $empr_renewal_activate = $this->active;
        return true;
    }
    
	protected function get_empr_fields_query() {
        $query = "SELECT empr_renewal_form_field_code, empr_renewal_form_field_display, empr_renewal_form_field_mandatory, empr_renewal_form_field_alterable, empr_renewal_form_field_explanation
				FROM empr_renewal_form_fields";
	    return $query;
            }
        
	public function get_form() {
	    global $empr_renewal_activate;
	    
	    $html = parent::get_form();
    
	    $checked = '';
	    $this->active = $empr_renewal_activate;
	    if ($this->active) {
	        $checked = 'checked="checked"';
            }
	    $html = str_replace('!!renewal_activate_checked!!', $checked, $html);
	    return $html;
    }
}