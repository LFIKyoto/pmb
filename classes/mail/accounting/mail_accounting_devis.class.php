<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_accounting_devis.class.php,v 1.2 2019-08-02 10:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/mail/accounting/mail_accounting.class.php");

class mail_accounting_devis extends mail_accounting {
	
    protected static function get_parameter_prefix() {
		return "acquisition_pdfdev";
	}
	
	protected function get_attachments($id_bibli,$id_dev) {
	    $lettre = lettreDevis_factory::make();
	    $lettre->doLettre($id_bibli,$id_dev);
	    $piece_jointe=array();
	    $piece_jointe[0]['contenu']=$lettre->getLettre('S');
	    $piece_jointe[0]['nomfichier']=$lettre->getFileName();
	    return $piece_jointe;
	}
}