<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_authperso.class.php,v 1.1.2.2 2019-09-19 08:39:10 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_authperso extends frbr_entity_common_datasource_authperso {
    
    public function get_sub_datasources() {
        return array(
            "frbr_entity_concepts_datasource_authperso_indexed",
            "frbr_entity_concepts_datasource_authperso_custom_fields",
            "frbr_entity_concepts_datasource_authperso_used_in_custom_fields",
            "frbr_entity_concepts_datasource_authperso_aut_link",
        );
    }
}