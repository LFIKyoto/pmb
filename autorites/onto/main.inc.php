<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2014-08-12 09:30:30 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/autoloader.class.php");


$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

switch($categ){
	case "concepts" :
		if($thesaurus_concepts_active  == 1){
			include($base_path."/autorites/onto/skos/main.inc.php");
		}
		break;
}