<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_groupe.inc.php,v 1.49.6.1 2019-11-21 13:48:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $dest;

/*********************************************************************************/
/**********************DG / REFONTE DU 21/11/2019*********************************/
/****************REVENIR SUR LA VERSION 1.49 SI BLOQUANT**************************/
/*********************************************************************************/

require_once ($class_path."/list/loans/list_loans_groups_edition_ui.class.php");

switch($sub) {
    case 'ppargroupe' :
        $list_loans_groups_edition_ui = new list_loans_groups_edition_ui(array('associated_group' => '1', 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'groups'));
        break;
    case 'rpargroupe' :
        $list_loans_groups_edition_ui = new list_loans_groups_edition_ui(array('associated_group' => '1', 'pret_retour_end' => date('Y-m-d'), 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'groups'));
        break;
}
switch($dest) {
    case "TABLEAU":
        $list_loans_groups_edition_ui->get_display_spreadsheet_list();
        break;
    case "TABLEAUHTML":
        print $list_loans_groups_edition_ui->get_display_html_list();
        break;
    default:
        print $list_loans_groups_edition_ui->get_display_list();
        //impression/emails (on est dans le cas retards/retards par date)
        //         if ($action == "print") {
        //             $list_loans_groups_edition_ui->print_relances();
        //         }
            break;
}
?>