<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_password.inc.php,v 1.18.6.2 2019-12-04 10:27:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $allow_pwd, $action, $msg, $id_empr, $old_password, $new_password, $confirm_new_password, $empr_login, $opac_websubscribe_password_regexp;

if (!$allow_pwd) die();

switch ($action) {
    case "save":
        $emprunteur = new emprunteur($id_empr);
        if ($emprunteur->pwd == emprunteur::get_hashed_password($empr_login, $old_password)) {
            if ($new_password == $confirm_new_password) {
                if (pmb_preg_match("/$opac_websubscribe_password_regexp/", $new_password)) {
                    emprunteur::hash_password($empr_login, $new_password);
                    $status = 'empr_password_changed';
                } else {
                    $status = 'empr_password_bad_security';
                }
            } else {
                $status = 'empr_password_does_not_match';
            }
        } else {
            $status = 'empr_old_password_wrong';
        }
        print "<div id='change-password'>
                   <div id='change-password-container'>
                       $msg[$status]
                       <br />
                       <br />
                   </div>
               </div>";
        break;
    case "get_form":
    default:
        print emprunteur_display::get_display_change_password($id_empr);
        break;
}