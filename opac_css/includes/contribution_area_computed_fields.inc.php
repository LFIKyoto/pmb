<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_computed_fields.inc.php,v 1.1 2018-12-18 08:01:47 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	die();
}

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/contribution_area/computed_fields/computed_field.class.php');

$return = array();

switch ($what) {
	default:
		$computed_fields = computed_field::get_area_computed_fields($area_id);
		foreach ($computed_fields as $computed_field) {
			$return[] = $computed_field->get_data();
		}
		break;
}

print encoding_normalize::json_encode($return);