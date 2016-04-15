<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_pret.inc.php,v 1.32 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$base_path/circ/pret_func.inc.php");
// liste des prêts et réservations
// prise en compte du param d'envoi de ticket de prêt électronique si l'utilisateur le veut !
if ($empr_electronic_loan_ticket && $param_popup_ticket) {
	electronic_ticket($id_empr) ;
}

// popup d'impression PDF pour fiche lecteur
// reçoit : id_empr
// Démarrage et configuration du pdf
$ourPDF = new $fpdf('P', 'mm', 'A4');
$ourPDF->Open();

//requete par rapport à un emprunteur
$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;	
$req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
$count = pmb_mysql_num_rows($req);

$ourPDF->addPage();
//$ourPDF->SetMargins(10,10,10);
$ourPDF->SetLeftMargin(10);
$ourPDF->SetTopMargin(10);
// paramétrage spécifique à ce document :
$offsety = 0;

if(!$pmb_hide_biblioinfo_letter) biblio_info( 10, 10, 1) ;
$offsety=(ceil($ourPDF->GetStringWidth($biblio_name)/90)-1)*10; //90=largeur de la cell, 10=hauteur d'une ligne
lecteur_info($id_empr, 90, 10+$offsety, $dbh, 1,1);
date_edition(10,15+$offsety);

$ourPDF->SetXY (10,22+$offsety);
$ourPDF->setFont($pmb_pdf_font, 'BI', 14);
$ourPDF->multiCell(190, 20, $msg["prets_en_cours"]." (".($count).")", 0, 'L', 0);
$indice_page = 0 ;
$nb_page=0;
$nb_par_page = 21;
$nb_1ere_page = 19;
$taille_bloc = 12 ;
$debut_expl_1er_page=35+$offsety;
$debut_expl_page=10;
$limite_after_list = 260;
while ($data = pmb_mysql_fetch_array($req)) {
	if ($nb_page==0 && $indice_page==$nb_1ere_page) {
		$ourPDF->addPage();
		$nb_page++;
		$indice_page = 0 ;
	} elseif ((($nb_page>=1) && (($indice_page % $nb_par_page)==0)) || ($ourPDF->GetY()>$limite_after_list)) { 
		$ourPDF->addPage();
		$nb_page++;
		$indice_page = 0 ;
	}
	if ($nb_page==0) $pos_page = $debut_expl_1er_page+$taille_bloc*$indice_page;
		else $pos_page = $debut_expl_page+$taille_bloc*$indice_page;
	expl_info ($data['expl_cb'],10,$pos_page,$dbh, 1, 80);
	$indice_page++;
}

pmb_mysql_free_result($req);

header("Content-Type: application/pdf");
$ourPDF->OutPut();