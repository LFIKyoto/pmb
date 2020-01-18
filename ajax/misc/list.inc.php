<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list.inc.php,v 1.2 2019-08-20 09:08:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $objects_type, $class_path, $id;

$type = $objects_type;
$directory = '';

if (!isset($id)) $id = 0;

if (strpos($objects_type, 'accounting') !== false) $directory = 'accounting';
if (strpos($objects_type, 'bannettes') !== false) $directory = 'bannettes';
if (strpos($objects_type, 'configuration') !== false) $directory = 'configuration';
if (strpos($objects_type, 'loans') !== false) $directory = 'loans';
if (strpos($objects_type, 'readers') !== false) $directory = 'readers';
if (strpos($objects_type, 'records') !== false) $directory = 'records';
if (strpos($objects_type, 'reservations') !== false) $directory = 'reservations';
if (strpos($objects_type, 'scan_requests') !== false) $directory = 'scan_requests';
if (strpos($objects_type, 'suggestions') !== false) $directory = 'suggestions';
if (strpos($objects_type, 'transferts') !== false) $directory = 'transferts';
if (strpos($objects_type, 'users') !== false) $directory = 'users';
if (strpos($objects_type, 'custom_fields') !== false) $directory = 'custom_fields';

if (strpos($objects_type, 'caddie_ui') !== false) $type = 'caddie_ui';

switch ($type) {
    case 'caddie_ui':
        require_once "$class_path/caddie/caddie_root_lists_controller.class.php";
        caddie_root_lists_controller::proceed_manage_ajax($id, $objects_type);
        break;
    default:
        require_once "$class_path/list/lists_controller.class.php";
        lists_controller::proceed_manage_ajax($id, $objects_type, $directory);
        break;
}