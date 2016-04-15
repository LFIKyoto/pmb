<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_transport_http.class.php,v 1.1 2011-08-02 12:35:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/openurl/transport/openurl_transport.class.php");

class openurl_transport_byref_http extends openurl_transport_byref{
	
    function openurl_transport_byref_http($url,$notice_id,$source_id,$byref_url) {
    	parent::openurl_transport_byref($url,$notice_id,$source_id,$byref_url);
    	$this->uri = $this->uri.":http:openurl-by-ref";
    }
}

class openurl_transport_byval_http extends openurl_transport_byval{

    function openurl_transport_byval_http($url) {
    	parent::openurl_transport_byval($url);
    	$this->uri = $this->uri.":http:openurl-by-val";
    }
}

class openurl_transport_inline_http extends openurl_transport_inline{

    function openurl_transport_inline_http($url) {
     	parent::openurl_transport_inline($url); 
     	$this->uri = $this->uri.":http:openurl-inline";
    }
    
    function send(){
    	return  openurl_transport_http::get($this->generateURL());
    }
}