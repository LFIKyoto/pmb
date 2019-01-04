<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visits_statistics.inc.php,v 1.2 2017-12-28 10:11:08 apetithomme Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$empr_visits_statistics_active) die();

require_once($class_path.'/visits_statistics.class.php');

$visits_statistics = new visits_statistics();

switch ($sub) {
	case 'add_visit':
		$visits_statistics->add_visit($counter_type);
		break;
	case 'remove_visit':
		$visits_statistics->remove_visit($counter_type);
		break;
	case 'update_visits':
		$visits_statistics->update_visits($counter_type, $value);
		break;
	case 'get_data':
		print $visits_statistics->get_json_statistics();
		break;
}