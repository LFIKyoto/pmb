<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-facture.inc.php,v 1.17 2018-08-07 15:13:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour facture
// re�oit : id_cde

if (!$id_fac) {print "<script> self.close(); </script>" ; die;}

//Footer personalis�
class PDF extends FPDF
{
	function Footer() {
		
		global $msg;
		global $y_footer, $fs_footer;
		
	    $this->SetY(-$y_footer);
	    //Num�ro de page centr�
	    $this->Cell(0,$fs_footer,$msg['acquisition_act_page'].$this->PageNo().' / '.$this->AliasNbPages,0,0,'C');
	}
}

if (strpos($acquisition_pdffac_print, '.php')) {
	require_once($acquisition_pdffac_print);
} else {
	require_once($class_path."/pdf/accounting/lettre_invoice_PDF.class.php");
	$lettre_invoice_PDF = lettre_invoice_factory::make();
	$lettre_invoice_PDF->doLettre(0, $id_fac);
	$lettre_invoice_PDF->getLettre();
}
?>