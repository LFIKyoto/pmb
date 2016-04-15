<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_context_object.class.php,v 1.1 2011-08-02 12:36:00 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/openurl.class.php");
require_once($class_path."/openurl/entities/openurl_entities.class.php");
//require_once($class_path.'/search.class.php');

class openurl_context_object extends openurl_root {
	var $infos= array();//	tableau regroupant des infos générales..;
	var $entitites;		//	tableau des entités

    function openurl_context_object() {
    	$this->uri = parent::$uri."/fmt";
    }
    
    function serialize(){}
}