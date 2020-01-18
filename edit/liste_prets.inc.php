<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_prets.inc.php,v 1.11.2.1 2019-11-21 12:51:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $coch_groupe, $selected_objects, $id_groupe;

require_once($class_path."/pdf/reader/loans/lettre_reader_loans_group_PDF.class.php");

// popup d'impression PDF pour lettres de retard par groupe
// reçoit : liste des groupes cochés $coch_groupe
//Via la nouvelle mécanique de listes
if(empty($coch_groupe) && !empty($selected_objects)) {
    $coch_groupe = explode(',', $selected_objects);
}

header("Content-Type: application/pdf");
$lettre_reader_loans_group_PDF = lettre_reader_loans_group_PDF::get_instance('reader/loans');
$lettre_reader_loans_group_PDF->doLettre($id_groupe);
$ourPDF = $lettre_reader_loans_group_PDF->PDF;
$ourPDF->OutPut();

?>