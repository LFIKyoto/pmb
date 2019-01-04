<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_resource.inc.php,v 1.1 2018-12-28 16:19:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	die();
}

require_once($class_path.'/notice_affichage.class.php');
require_once($class_path.'/authority.class.php');

$template = "";
if (!empty($type) && !empty($id)) {
    switch ($type) {
        case 'categories':
            $authority = new authority(0, $id, AUT_TABLE_CATEG);
            $template = $authority->get_isbd();
            break;
        case 'authors':
            $authority = new authority(0, $id, AUT_TABLE_AUTHORS);
            $template = $authority->get_isbd();
            break;
        case 'publishers':
            $authority = new authority(0, $id, AUT_TABLE_PUBLISHERS);
            $template = $authority->get_isbd();
            break;
        case 'titres_uniformes':
            $authority = new authority(0, $id, AUT_TABLE_TITRES_UNIFORMES);
            $template = $authority->get_isbd();
            break;
        case 'collections':
            $authority = new authority(0, $id, AUT_TABLE_COLLECTIONS);
            $template = $authority->get_isbd();
            break;
        case 'subcollections':
            $authority = new authority(0, $id, AUT_TABLE_SUB_COLLECTIONS);
            $template = $authority->get_isbd();
            break;
        case 'indexint':
            $authority = new authority(0, $id, AUT_TABLE_INDEXINT);
            $template = $authority->get_isbd();
            break;
        case 'serie':
            $authority = new authority(0, $id, AUT_TABLE_SERIES);
            $template = $authority->get_isbd();
            break;
        case 'notice':
            if (!empty($id)) {
                $notice = new notice_affichage($id);
                $notice->do_header();
                $template = $notice->notice_header;
            }
    		break;
    }
}
print $template;