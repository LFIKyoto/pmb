<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.inc.php,v 1.17 2017-08-04 07:23:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_collections_controller.class.php");

// gestion des collections
print '<h1>'.$msg[140].'&nbsp;: '. $msg[136].'</h1>';
$tri_param = ' order by index_coll ';

$entities_collections_controller = new entities_collections_controller($id);
$entities_collections_controller->set_url_base('autorites.php?categ=collections');
$entities_collections_controller->proceed();