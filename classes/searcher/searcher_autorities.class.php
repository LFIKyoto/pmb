<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_autorities.class.php,v 1.2 2014-10-08 12:38:28 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//un jour ca sera utile
class searcher_autorities extends searcher_generic {
	
	public function _get_search_type(){
		return "authorites";
	}
}