<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_liste.inc.php,v 1.10 2019-08-01 13:16:35 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $iddemande, $act, $class_path, $chk, $state;

if (!isset($iddemande)) $iddemande = 0;
if (!isset($act)) $act = '';

require_once "$class_path/demandes.class.php";

$demande = new demandes($iddemande);

switch ($act) {
	
	case 'new':
		$demande->show_modif_form();
		break;
	case 'save':
		demandes::get_values_from_form($demande);
		demandes::save($demande);
		$demande->show_list_form();
		break;
	case 'search':
		$demande->show_list_form();
		break;
	case 'suppr':
		if (!empty($iddemande)) {
			demandes::delete($demande);
		} elseif(!empty($chk)) {
			$chk = explode(",", $chk);
			$nb_chk = count($chk);
			for ($i = 0; $i < $nb_chk; $i++) {
				$dmde = new demandes($chk[$i]);
				demandes::delete($dmde);
			}
		}		
		$demande->show_list_form();
		break;
	case 'suppr_noti':
		$requete = "SELECT num_notice FROM demandes WHERE id_demande IN (".implode(",", $chk).") AND num_notice!=0";
		$result = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result) > 0) {
			$demande->suppr_notice_form();
		} else {
			if (!empty($iddemande)) {
				demandes::delete($demande);
			} elseif(!empty($chk)) {
				if (!is_array($chk)) {
					$chk = explode(",", $chk);
				}
				$nb_chk = count($chk);
				for ($i = 0; $i < $nb_chk; $i++) {
					$dmde = new demandes($chk[$i]);
					demandes::delete($dmde);
				}
			}
			$demande->show_list_form();
		}
		break;
	case 'change_state':
		if (!empty($chk)) {
		    $nb_chk = count($chk);
		    for ($i = 0; $i < $nb_chk; $i++) {
				$dde = new demandes($chk[$i]);
				demandes::change_state($state, $dde);
			}
		} else {
			demandes::change_state($state, $demande);
			$demande->fetch_data($iddemande);
		}		
		$demande->show_list_form();
		break;
	case 'affecter':
		$demande->attribuer();
		$demande->show_list_form();
		break;
	default:
		$demande->show_list_form();
		break;
}
?>