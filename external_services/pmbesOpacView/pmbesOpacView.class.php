<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOpacView.class.php,v 1.1 2011-05-12 13:12:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/opac_view.class.php");

class pmbesOpacView extends external_services_api_class {
	var $error=false;		//Y-a-t-il eu une erreur
	var $error_message="";	//Message correspondant à l'erreur
	var $es;				//Classe mère qui implémente celle-ci !
	var $msg;
	
	function gen_search() {
		global $dbh;
		$views=new opac_view();
		$views->gen();
	
	}
	
}




?>