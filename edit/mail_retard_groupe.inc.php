<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_retard_groupe.inc.php,v 1.14.6.1 2019-11-28 15:27:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $relance, $id_groupe, $selected_objects;

require_once($class_path."/mail/reader/loans/mail_reader_loans_late_group.class.php");

if (empty($relance)) $relance = 1;
if ($id_groupe) {
    $id_groupe = intval($id_groupe);
	$req = "select libelle_groupe from groupe where id_groupe='".$id_groupe."'";
	$res = pmb_mysql_query($req);
	if ($res && pmb_mysql_num_rows($res)) {
		$row = pmb_mysql_fetch_object($res);
		$group_name = $row->libelle_groupe;
	}
	mail_reader_loans_late_group::set_niveau_relance($relance);
	$mail_reader_loans_late_group = new mail_reader_loans_late_group();
	$mail_reader_loans_late_group->send_mail(0, $id_groupe);
} elseif(!empty($selected_objects)) {
    mail_reader_loans_late_group::set_niveau_relance($relance);
    $mail_reader_loans_late_group = new mail_reader_loans_late_group();
    $groups = explode(',', $selected_objects);
    foreach ($groups as $id_groupe) {
        $mail_reader_loans_late_group->send_mail(0, $id_groupe);
    }
}