<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_root.class.php,v 1.3.2.2 2019-12-02 14:33:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/h2o/pmb_h2o.inc.php");

class mail_root {
	
    protected $formatted_data;
    
    public function __construct() {
        $this->_substitution_parameters();
        $this->_init();
    }
    
    protected function _substitution_parameters() {
        global $include_path;
        global $deflt2docs_location;
        
        //Globalisons tout d'abord les paramètres communs à toutes les localisations
        if (file_exists($include_path."/parameters_subst/mail_per_localisations_subst.xml")){
            $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations_subst.xml", 0);
        } else {
            $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations.xml", 0);
        }
        $parameter_subst->extract();
        
        if(isset($deflt2docs_location)) {
            if (file_exists($include_path."/parameters_subst/mail_per_localisations_subst.xml")){
                $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations_subst.xml", $deflt2docs_location);
            } else {
                $parameter_subst = new parameters_subst($include_path."/parameters_subst/mail_per_localisations.xml", $deflt2docs_location);
            }
            $parameter_subst->extract();
        }
    }
    
    protected function _init_default_parameters() {
        
    }
    
    protected function _init() {
        $this->_init_default_parameters();
    }
    
    protected static function get_parameter_prefix() {
		return '';
	}
	
	protected function get_evaluated_parameter($parameter_name) {
	    global $biblio_name, $biblio_email, $biblio_phone, $biblio_commentaire, ${$parameter_name};
		eval ("\$evaluated=\"".${$parameter_name}."\";");
		return $evaluated;
	}
	
	protected function get_parameter_value($name) {
		$parameter_name = static::get_parameter_prefix().'_'.$name;
		return $this->get_evaluated_parameter($parameter_name);
	}
	
	protected function _init_parameter_value($name, $value) {
		$parameter_name = static::get_parameter_prefix().'_'.$name;
		global ${$parameter_name};
		if(empty(${$parameter_name})) {
			${$parameter_name} = $value;
		}
	}
	
	public static function render($tpl, $data) {
	    global $charset;
        $data=encoding_normalize::utf8_normalize($data);
        $tpl=encoding_normalize::utf8_normalize($tpl);
        $data_to_return = H2o::parseString($tpl)->render($data);
        if ($charset !="utf-8") {
            $data_to_return = utf8_decode($data_to_return);
        }
        return $data_to_return;
	}
	
	public static function get_instance($group='') {
	    global $msg, $charset;
	    global $base_path, $class_path, $include_path;
	    
	    $className = static::class;
	    if($group) {
	        $prefix = static::get_parameter_prefix();
	        $print_parameter = $prefix."_print";
	        global ${$print_parameter};
	        if(!empty(${$print_parameter}) && file_exists($class_path."/mail/".$group."/".${$print_parameter}.".class.php")) {
	            require_once($class_path."/mail/".$group."/".${$print_parameter}.".class.php");
	            $className = ${$print_parameter};
	        } else {
	            require_once($class_path."/mail/".$group."/".$className.".class.php");
	        }
	    } else {
	        if(!empty(${$print_parameter}) && file_exists($class_path."/mail/".${$print_parameter}.".class.php")) {
	            require_once($class_path."/mail/".${$print_parameter}.".class.php");
	            $className = ${$print_parameter};
	        } else {
	            require_once($class_path."/mail/".$className.".class.php");
	        }
	    }
	    return new $className();
	}
}