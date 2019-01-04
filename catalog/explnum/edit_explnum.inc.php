<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit_explnum.inc.php,v 1.18 2017-08-10 09:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_records_explnum_controller.class.php");

// gestion des doc numeriques

$entities_records_explnum_controller = new entities_records_explnum_controller($explnum_id);
$entities_records_explnum_controller->set_record_id($id);
$entities_records_explnum_controller->set_bulletin_id($bul_id);
$entities_records_explnum_controller->set_action('explnum_form');
$entities_records_explnum_controller->proceed();

?>
	