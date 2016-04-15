<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_serialize.class.php,v 1.1 2011-08-02 12:35:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/openurl.class.php");

class openurl_serialize extends openurl_root{

    function openurl_serialize() {
    	$this->uri = parent::$uri."/fmt";
    }
    
    function serialize(){}
}