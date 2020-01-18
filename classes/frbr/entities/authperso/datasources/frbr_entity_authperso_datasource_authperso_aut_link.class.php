<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_authperso_aut_link.class.php,v 1.1.2.2 2019-09-18 09:36:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_authperso_aut_link extends frbr_entity_common_datasource_aut_link_authpersos {
    
    public function __construct($id=0) {
        $this->entity_type = "authperso";
        $this->parent_type = "authperso";
        parent::__construct($id);
    }
	
	public function set_authperso_id($id) {
	    $this->parameters->authperso_id = intval($id);
	    $this->authority_type = 1000 + intval($id);
	}
}