<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl.class.php,v 1.1 2011-08-02 12:35:58 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class openurl_root {
	public static $uri ="info:ofi";
	public static $serialize ="";

    function openurl_root() {
    	    	
    }
}