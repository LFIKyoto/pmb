<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes.inc.php,v 1.1.6.1 2019-09-20 09:42:04 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/search_universes/search_universes_controller.class.php');
require_once($class_path."/autoloader.class.php");

if(!is_object($autoloader)){
    $autoloader = new autoloader();
}
$autoloader->add_register("onto_class",true);

$search_universes_controller = new search_universes_controller($id);
$search_universes_controller->proceed_ajax();