<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_commande.class.php,v 1.6 2015-07-16 12:16:51 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf_factory.class.php");
require_once("$class_path/entites.class.php");
require_once("$class_path/coordonnees.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/types_produits.class.php");
require_once("$class_path/paiements.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");

class lettreCommande_PDF {
	
	var $PDF;
	var $orient_page = 'P';			//Orientation page (P=portrait, L=paysage)
	var $largeur_page = 210;		//Largeur de page
	var $hauteur_page = 297;		//Hauteur de page
	var $unit = 'mm';				//Unite 
	var $marge_haut = 10;			//Marge haut
	var $marge_bas = 20;			//Marge bas
	var $marge_droite = 10;			//Marge droite
	var $marge_gauche = 10;			//Marge gauche
	var $w = 190;					//Largeur utile page
	var $font = 'Helvetica';		//Police
	var $fs = 10;					//Taille police 
	var $x_logo = 10;				//Distance du logo / bord gauche de page
	var $y_logo = 10;				//Distance du logo / bord haut de page
	var $l_logo = 20;				//Largeur logo
	var $h_logo = 20;				//Hauteur logo
	var $x_raison = 35;				//Distance raison sociale / bord gauche de page
	var $y_raison = 10;				//Distance raison sociale / bord haut de page
	var $l_raison = 100;			//Largeur raison sociale
	var $h_raison = 10;				//Hauteur raison sociale
	var $fs_raison = 16;			//Taille police raison sociale
	var $x_date = 150;				//Distance date / bord gauche de page
	var $y_date = 10;				//Distance date / bord haut de page
	var $l_date = 0;				//Largeur date
	var $h_date = 6;				//Hauteur date
	var $fs_date = 8;				//Taille police date
	var $sep_ville_date = '';		//Séparateur entre ville et date
	var $x_adr_fac = 10;			//Distance adr facture / bord gauche de page
	var $y_adr_fac = 35;			//Distance adr facture / bord haut de page
	var $l_adr_fac = 60;			//Largeur adr facture
	var $h_adr_fac = 5;				//Hauteur adr facture
	var $fs_adr_fac = 10;			//Taille police adr facture
	var $text_adr_fac = '';
	var $text_adr_fac_tel = '';
	var $text_adr_fac_fax = '';
	var $text_adr_fac_email = '';
	var $x_adr_liv = 10;			//Distance adr livraison / bord gauche de page
	var $y_adr_liv = 75;			//Distance adr livraison / bord haut de page
	var $l_adr_liv = 60;			//Largeur adr livraison
	var $h_adr_liv = 5;				//Hauteur adr livraison
	var $fs_adr_liv = 10;			//Taille police adr livraison
	var $text_adr_liv = '';
	var $text_adr_liv_tel = '';
	var $x_adr_fou = 100;			//Distance adr fournisseur / bord gauche de page
	var $y_adr_fou = 55;			//Distance adr fournisseur / bord haut de page
	var $l_adr_fou = 100;			//Largeur adr fournisseur
	var $h_adr_fou = 6;				//Hauteur adr fournisseur
	var $fs_adr_fou = 14;			//Police adr fournisseur
	var $x_titre = 10;				//Distance titre / bord gauche de page
	var $y_titre = 90;				//Distance titre / bord haut de page
	var $l_titre = 100;				//Largeur titre
	var $h_titre = 10;				//Hauteur titre
	var $fs_titre = 16;				//Police titre
	var $text_titre = '';
	var $x_num = 10;				//Distance num commande / bord gauche de page
	var $y_num = 110;				//Distance num commande / bord haut de page
	var $l_num = 0;					//Largeur num commande
	var $h_num = 10;				//Hauteur num commande
	var $fs_num = 16;				//Taille police num commande
	var $text_num = '';				//Texte commande
	var $text_num_cli = '';
	var $text_ref = ''; 
	var $text_before = '';			//texte avant table commande
	var $text_after = '';			//texte après table commande
	var $h_tab = 5;					//Hauteur de ligne table commande
	var $fs_tab = 10;				//Taille police table commande
	var $x_tab = 10;				//position table commande / bord gauche page 
	var $y_tab = 10;				//position table commande / haut page sur pages 2 et + 
	var $x_tot = 10;				//position total / bord gauche de page
	var $l_tot = 40;				//largeur total
	var $h_tot = 5;					//hauteur total
	var $fs_tot = 10;				//Taille police total
	var $x_sign = 10;				//Distance signature / bord gauche de page
	var $l_sign = 60;				//Largeur cellule signature
	var $h_sign = 5;				//Hauteur signature
	var $fs_sign = 10;				//Taille police signature
	var $text_sign = '';			//Texte signature
	var $y_footer = 15;				//Distance footer / bas de page
	var $fs_footer = 8;				//Taille police footer
	var $x_col1 = '';
	var $w_col1 = '';
	var $txt_header_col1 = '';
	var $x_col2 = '';
	var $w_col2 = '';
	var $txt_header_col2 = '';
	var $x_col3 = '';
	var $w_col3 = '';
	var $txt_header_col3 = '';
	var $x_col4 = '';
	var $w_col4 = '';
	var $txt_header_col4 = '';
	var $x_col5 = '';
	var $w_col5 = '';
	var $txt_header_col5 = '';
	var $y = 0;
	var $h = 0;
	var $s = 0;
	var $h_header = 0;
	var $p_header = false;
	var $filename='commande.pdf';
	
	function __construct() {
		
		global $msg, $charset, $pmb_pdf_font;
		global $acquisition_pdfcde_orient_page, $acquisition_pdfcde_text_size, $acquisition_pdfcde_format_page, $acquisition_pdfcde_marges_page;
		global $acquisition_pdfcde_pos_logo, $acquisition_pdfcde_pos_raison, $acquisition_pdfcde_pos_date, $acquisition_pdfcde_pos_adr_fac;
		global $acquisition_pdfcde_pos_adr_liv, $acquisition_pdfcde_pos_adr_fou, $acquisition_pdfcde_pos_num, $acquisition_pdfcde_text_before;
		global $acquisition_pdfcde_text_after, $acquisition_pdfcde_tab_cde, $acquisition_pdfcde_pos_tot, $acquisition_pdfcde_pos_sign, $acquisition_pdfcde_text_sign;
		global $acquisition_pdfcde_pos_footer, $acquisition_pdfcde_pos_titre, $acquisition_pdfcde_pos_num_cli ;
		global $acquisition_gestion_tva;
			
		if($acquisition_pdfcde_orient_page) $this->orient_page = $acquisition_pdfcde_orient_page;
		
		$format_page = explode('x',$acquisition_pdfcde_format_page);
		if($format_page[0]) $this->largeur_page = $format_page[0];
		if($format_page[1]) $this->hauteur_page = $format_page[1];

		$this->PDF = pdf_factory::make($this->orient_page, $this->unit, array($this->largeur_page, $this->hauteur_page));
		
		$marges_page = explode(',', $acquisition_pdfcde_marges_page);
		if ($marges_page[0]) $this->marge_haut = $marges_page[0];
		if ($marges_page[1]) $this->marge_bas = $marges_page[1];
		if ($marges_page[2]) $this->marge_droite = $marges_page[2];
		if ($marges_page[3]) $this->marge_gauche = $marges_page[3];
				
		$this->w = $this->largeur_page-$this->marge_gauche-$this->marge_droite;
		
		$this->font = $pmb_pdf_font;
		if($acquisition_pdfcde_text_size) $this->fs = $acquisition_pdfcde_text_size;

		$pos_logo = explode(',', $acquisition_pdfcde_pos_logo);
		if ($pos_logo[0]) $this->x_logo = $pos_logo[0];
		if ($pos_logo[1]) $this->y_logo = $pos_logo[1];
		if ($pos_logo[2]) $this->l_logo = $pos_logo[2];
		if ($pos_logo[3]) $this->h_logo = $pos_logo[3];

		$pos_raison = explode(',', $acquisition_pdfcde_pos_raison);
		if ($pos_raison[0]) $this->x_raison = $pos_raison[0];
		if ($pos_raison[1]) $this->y_raison = $pos_raison[1];
		if ($pos_raison[2]) $this->l_raison = $pos_raison[2];
		if ($pos_raison[3]) $this->h_raison = $pos_raison[3];
		if ($pos_raison[4]) $this->fs_raison = $pos_raison[4];
		
		$pos_date = explode(',', $acquisition_pdfcde_pos_date);
		if ($pos_date[0]) $this->x_date = $pos_date[0];
		if ($pos_date[1]) $this->y_date = $pos_date[1];
		if ($pos_date[2]) $this->l_date = $pos_date[2];
		if ($pos_date[3]) $this->h_date = $pos_date[3];
		if ($pos_date[4]) $this->fs_date = $pos_date[4];
		$this->sep_ville_date = $msg['acquisition_act_sep_ville_date'];
		
		$pos_adr_fac = explode(',', $acquisition_pdfcde_pos_adr_fac);
		if ($pos_adr_fac[0]) $this->x_adr_fac = $pos_adr_fac[0];
		if ($pos_adr_fac[1]) $this->y_adr_fac = $pos_adr_fac[1];
		if ($pos_adr_fac[2]) $this->l_adr_fac = $pos_adr_fac[2];
		if ($pos_adr_fac[3]) $this->h_adr_fac = $pos_adr_fac[3];
		if ($pos_adr_fac[4]) $this->fs_adr_fac = $pos_adr_fac[4];
		$this->text_adr_fac = $msg['acquisition_adr_fac']." :";
		$this->text_adr_fac_tel = $msg['acquisition_tel'].".";
		$this->text_adr_fac_tel2 = $msg['acquisition_tel2'].".";
		$this->text_adr_fac_fax = $msg['acquisition_fax'].".";
		$this->text_adr_fac_email = $msg['acquisition_mail']." :";
		
		$pos_adr_liv = explode(',', $acquisition_pdfcde_pos_adr_liv);
		if ($pos_adr_liv[0]) $this->x_adr_liv = $pos_adr_liv[0];
		if ($pos_adr_liv[1]) $this->y_adr_liv = $pos_adr_liv[1];
		if ($pos_adr_liv[2]) $this->l_adr_liv = $pos_adr_liv[2];
		if ($pos_adr_liv[3]) $this->h_adr_liv = $pos_adr_liv[3];
		if ($pos_adr_liv[4]) $this->fs_adr_liv = $pos_adr_liv[4];
		$this->text_adr_liv = $msg['acquisition_adr_liv']." :";
		$this->text_adr_liv_tel = $msg['acquisition_tel'].".";
		$this->text_adr_liv_tel2 = $msg['acquisition_tel2'].".";
		$this->text_adr_liv_email = $msg['acquisition_mail']." :";
		
		$pos_adr_fou = explode(',', $acquisition_pdfcde_pos_adr_fou);
		if ($pos_adr_fou[0]) $this->x_adr_fou = $pos_adr_fou[0];
		if ($pos_adr_fou[1]) $this->y_adr_fou = $pos_adr_fou[1];
		if ($pos_adr_fou[2]) $this->l_adr_fou = $pos_adr_fou[2];
		if ($pos_adr_fou[3]) $this->h_adr_fou = $pos_adr_fou[3];
		if ($pos_adr_fou[4]) $this->fs_adr_fou = $pos_adr_fou[4];
		
		$pos_titre = explode(',', $acquisition_pdfcde_pos_titre);
		if ($pos_titre[0]) $this->x_titre = $pos_titre[0];
		if ($pos_titre[1]) $this->y_titre = $pos_titre[1];
		if ($pos_titre[2]) $this->l_titre = $pos_titre[2];
		if ($pos_titre[3]) $this->h_titre = $pos_titre[3];
		if ($pos_titre[4]) $this->fs_titre = $pos_titre[4];
		$this->text_titre = $msg['acquisition_recept_lettre_titre'];
		
		$pos_num = explode(',', $acquisition_pdfcde_pos_num);
		if ($pos_num[0]) $this->x_num = $pos_num[0];
		if ($pos_num[1]) $this->y_num = $pos_num[1];
		if ($pos_num[2]) $this->l_num = $pos_num[2];
		if ($pos_num[3]) $this->h_num = $pos_num[3];
		if ($pos_num[4]) $this->fs_num = $pos_num[4];
		$this->text_num = $msg['acquisition_act_num_cde'];
		$this->text_num_cli = $msg['acquisition_num_cp_client'];

		$this->text_ref = $msg['acquisition_cde_ref_dev'];
		$this->text_before = $acquisition_pdfcde_text_before;
		$this->text_after = $acquisition_pdfcde_text_after;
		
		$pos_tab = explode(',', $acquisition_pdfcde_tab_cde);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
		$this->x_tab = $this->marge_gauche;
		$this->y_tab = $this->marge_haut; 

		$pos_tot = explode(',', $acquisition_pdfcde_pos_tot);
		if ($pos_tot[0]) $this->x_tot = $pos_tot[0];
		if ($pos_tot[1]) $this->l_tot = $pos_tot[1];
		if ($pos_tot[2]) $this->h_tot = $pos_tot[2];
		if ($pos_tot[3]) $this->fs_tot = $pos_tot[3];
		
		$pos_sign = explode(',', $acquisition_pdfcde_pos_sign);
		if ($pos_sign[0]) $this->x_sign = $pos_sign[0];
		if ($pos_sign[1]) $this->l_sign = $pos_sign[1];
		if ($pos_sign[2]) $this->h_sign = $pos_sign[2];
		if ($pos_sign[3]) $this->fs_sign = $pos_sign[3];
			
		if ($acquisition_pdfcde_text_sign) $this->text_sign = $acquisition_pdfcde_text_sign;
			else $this->text_sign = $msg['acquisition_act_sign'];
		
		$pos_footer = explode(',', $acquisition_pdfcde_pos_footer);
		if ($pos_footer[0]) $this->PDF->y_footer = $pos_footer[0];
			else $this->PDF->y_footer=$this->y_footer;
		if ($pos_footer[1]) $this->PDF->fs_footer = $pos_footer[1];
			else $this->PDF->fs_footer=$this->fs_footer;
		
		$this->x_col1 =  $this->x_tab;
		$this->w_col1 = round($this->w*20/100);
		$this->txt_header_col1 = $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'];
		
		$this->x_col2 = $this->x_col1 + $this->w_col1;
		$this->w_col2 = round($this->w*40/100);
		$this->txt_header_col2 = $msg['acquisition_act_tab_lib'];
		
		$this->x_col3 = $this->x_col2 + $this->w_col2;
		$this->w_col3 = round($this->w*10/100); 
		$this->txt_header_col3 = $msg['acquisition_act_tab_qte'];
		
		$this->x_col4 = $this->x_col3 + $this->w_col3;
		$this->w_col4 = round($this->w*10/100); 
		switch($acquisition_gestion_tva) {
			case '1' :
				$this->txt_header_col4 = $msg['acquisition_act_tab_priht']."\n".$msg['acquisition_tva']."\n".$msg['acquisition_rem']; 
				break;
			case '2' :
				$this->txt_header_col4 = $msg['acquisition_act_tab_prittc']."\n".$msg['acquisition_tva']."\n".$msg['acquisition_rem'];
				break;
			default :
				$this->txt_header_col4 = " ".$msg['acquisition_act_tab_prittc']."\n".$msg['acquisition_rem'];
				break;
		}	
		
		$this->x_col5 = $this->x_col4 + $this->w_col4;
		$this->w_col5 = round($this->w*20/100); 
		$this->txt_header_col5 = $msg['acquisition_act_tab_dateliv'];
		
		$this->PDF->Open();
		$this->PDF->SetMargins($this->marge_gauche, $this->marge_haut, $this->marge_droite);
		$this->PDF->setFont($this->font);

		$this->h_header = $this->h_tab * max( 	$this->PDF->NbLines($this->w_col1, $this->txt_header_col1 ),
		$this->PDF->NbLines($this->w_col2,$this->txt_header_col2),
		$this->PDF->NbLines($this->w_col3, $this->txt_header_col3),
		$this->PDF->NbLines($this->w_col4, $this->txt_header_col4),
		$this->PDF->NbLines($this->w_col5, $this->txt_header_col5) );
		$this->p_header = false;
		
		$this->PDF->footer_type=1;
		$this->PDF->msg_footer = $msg['acquisition_act_page'];
	}
	
	
	function doLettre($id_bibli, $id_cde) {
		
		global $msg, $acquisition_gestion_tva;
		
		//On récupère les infos de la commande
	
		$cde = new actes($id_cde);
		$lignes = actes::getLignes($id_cde);
		$bib = new entites ($cde->num_entite);
		$coord_liv = new coordonnees($cde->num_contact_livr);
		$coord_fac = new coordonnees($cde->num_contact_fact);
		
		$fou = new entites($cde->num_fournisseur);
		$coord_fou = entites::get_coordonnees($cde->num_fournisseur, '1');
		$coord_fou = pmb_mysql_fetch_object($coord_fou);
		
		$this->PDF->AddPage();
		$this->PDF->npage = 1;
		
		//Affichage logo
		if($bib->logo != '') {
			$this->PDF->Image($bib->logo, $this->x_logo, $this->y_logo, $this->l_logo, $this->h_logo);
		}
		
		//Affichage raison sociale
		$raison =  $bib->raison_sociale;
		$this->PDF->setFontSize($this->fs_raison);
		$this->PDF->SetXY($this->x_raison, $this->y_raison);
		$this->PDF->MultiCell($this->l_raison, $this->h_raison, $raison, 0, 'L', 0);
		
		//Affichage date $ville
		$ville_end=stripos($coord_fac->ville,"cedex");	
		if($ville_end!==false) $ville=trim(substr($coord_fac->ville,0,$ville_end));
		else $ville=$coord_fac->ville;
		if ($cde->date_valid != '0000-00-00') {
			$date = $ville.$this->sep_ville_date.format_date($cde->date_valid);
		} else {
			$date = $ville.$this->sep_ville_date.format_date($cde->date_acte);
		}
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, $date, 0, 0, 'L', 0);
		
		//Affichage coordonnees fournisseur
		//si pas de raison sociale définie, on reprend le libellé
		//si il y a une raison sociale, pas besoin 
		if($fou->raison_sociale != '') {
			$adr_fou = $fou->raison_sociale."\n";
		} else { 
			$adr_fou = $coord_fou->libelle."\n";
		}
		if($coord_fou->adr1 != '') $adr_fou.= $coord_fou->adr1."\n";
		if($coord_fou->adr2 != '') $adr_fou.= $coord_fou->adr2."\n";
		if($coord_fou->cp != '') $adr_fou.= $coord_fou->cp." ";
		if($coord_fou->ville != '') $adr_fou.= $coord_fou->ville."\n\n";
		if ($coord_fou->contact != '') $adr_fou.= $coord_fou->contact;
		$this->PDF->setFontSize($this->fs_adr_fou);
		$this->PDF->SetXY($this->x_adr_fou, $this->y_adr_fou);
		$this->PDF->MultiCell($this->l_adr_fou, $this->h_adr_fou, $adr_fou, 0, 'L', 0);
		
	
		//Affichage adresse facturation
		$adr_fac=$this->text_adr_fac."\n"; 
		if($coord_fac->libelle != '') $adr_fac.= $coord_fac->libelle."\n"; 
		if($coord_fac->adr1 != '') $adr_fac.= $coord_fac->adr1."\n";
		if($coord_fac->adr2 != '') $adr_fac.= $coord_fac->adr2."\n";
		if($coord_fac->cp != '') $adr_fac.= $coord_fac->cp." ";
		if($coord_fac->ville != '') $adr_fac.= $coord_fac->ville."\n";
		if($coord_fac->tel1 != '') $adr_fac.= $this->text_adr_fac_tel." ".$coord_fac->tel1."\n";
		if($coord_fac->tel2 != '') $adr_fac.= $this->text_adr_fac_tel2." ".$coord_fac->tel2."\n";
		if($coord_fac->fax != '') $adr_fac.= $this->text_adr_fac_fax." ".$coord_fac->fax."\n";
		if($coord_fac->email != '') $adr_fac.= $this->text_adr_fac_email." ".$coord_fac->email."\n";
		if($bib->tva)$adr_fac.=$msg["acquisition_tva"].": ".$bib->tva."\n";
		$this->PDF->setFontSize($this->fs_adr_fac);
		$this->PDF->SetXY($this->x_adr_fac, $this->y_adr_fac);
		$this->PDF->MultiCell($this->l_adr_fac, $this->h_adr_fac, $adr_fac, 1, 'L', 0);
		
		//Affichage adresse livraison
		$adr_liv = '';
		if($coord_liv->libelle != '') $adr_liv.= $coord_liv->libelle."\n"; 
		if($coord_liv->adr1 != '') $adr_liv.= $coord_liv->adr1."\n";
		if($coord_liv->adr2 != '') $adr_liv.= $coord_liv->adr2."\n";
		if($coord_liv->cp != '') $adr_liv.= $coord_liv->cp." ";
		if($coord_liv->ville != '') $adr_liv.= $coord_liv->ville."\n";
		if($coord_liv->tel1 != '') $adr_liv.= $this->text_adr_liv_tel." ".$coord_liv->tel1."\n";
		if($coord_liv->tel2 != '') $adr_liv.= $this->text_adr_liv_tel2." ".$coord_liv->tel2."\n";
		if($coord_liv->email != '') $adr_liv.= $this->text_adr_liv_email." ".$coord_liv->email."\n";
		
		if($adr_liv != '') {
			$adr_liv = $this->text_adr_liv."\n".$adr_liv; 
			$this->PDF->setFontSize($this->fs_adr_liv);
			$this->PDF->SetXY($this->x_adr_liv, $this->y_adr_liv);
			$this->PDF->MultiCell($this->l_adr_liv, $this->h_adr_liv, $adr_liv, 1, 'L', 0);
		}
		
		//Affichage tiret pliage 
		$this->PDF->Line(0,105, 3, 105);
		$this->y=$this->PDF->GetY();
		$this->PDF->Ln();
		$this->PDF->Ln();

		//Affichage numero client
		$numero_cli = $this->text_num_cli." ".$fou->num_cp_client;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->SetXY($this->x_num, $this->y_num);
		$this->PDF->Cell($this->l_num_cli, $this->h_num, $numero_cli, 0, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage numero commande
		$numero =  $this->text_num.$cde->numero;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->Cell($this->l_num, $this->h_num, $numero, 0, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage reference
		if ($cde->reference != '') {
			$ref = $this->text_ref.$cde->reference;
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->Cell($this->w, $this->h_tab, $ref, 0, 0, 'L', 0);
			$this->PDF->Ln();
			$this->PDF->Ln();
		}
		
		//Affichage texte before + commentaires
		if ($cde->commentaires_i != '') {
			if ($this->text_before != '') $this->text_before.= "\n\n";
			$this->text_before.= $cde->commentaires_i;
		}
		if ($this->text_before != '') {
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_before, 0, 'J', 0);
		}
		
		//Affichage lignes commandes
		$this->PDF->SetAutoPageBreak(false);
		$this->PDF->AliasNbPages();
	
		$this->PDF->SetFontSize($this->fs_tab);
		$this->PDF->SetFillColor(230);
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_tab,$this->y);
		
		
		$tab_mnt=array();
		$i=0;
		while (($row = pmb_mysql_fetch_object($lignes))) {
			
			$typ = new types_produits($row->num_type);
			$col1 = $typ->libelle;
			if($row->code) $col1.= "\n".$row->code;
			$col2 = $row->libelle;
			$col3 = $row->nb;
			$col4 = number_format(round($row->prix, 2),2,'.','' )." ".$cde->devise;
			if ($acquisition_gestion_tva){
				$col4.= "\n".number_format(round($row->tva,2),2,'.','' )." %";
			}
			$col4.= "\n".number_format(round($row->remise,2),2,'.','' )." %";
			$col5='';
		 	if ($row->date_ech != '0000-00-00') {
		 		$col5 = formatdate($row->date_ech);
		 	}
		 	if($row->num_rubrique) {
				$rub = new rubriques($row->num_rubrique);
				if($rub->num_cp_compta) $col5.= "\n\n".$rub->num_cp_compta;
			}
		
			//Est ce qu'on dépasse ?		
			$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_col1, $col1),
						$this->PDF->NbLines($this->w_col2, $col2),
						$this->PDF->NbLines($this->w_col3, $col3),
						$this->PDF->NbLines($this->w_col4, $col4),
						$this->PDF->NbLines($this->w_col5, $col5) );
			$this->s = $this->y+$this->h;
			if(!$this->p_header) $this->s=$this->s + $this->h_header;		
		
			//Si oui, chgt page
			if ($this->s > ($this->hauteur_page-$this->marge_bas-$this->fs_footer)){
				$this->PDF->AddPage();
				$this->y = $this->y_tab;
				$this->p_header = false;
			}
			if (!$this->p_header) {
				$this->doEntete();		
				$this->y+=$this->h_header;		
			}
			$this->p_header = true; 
		
			$this->PDF->SetXY($this->x_col1, $this->y);
			$this->PDF->Rect($this->x_col1, $this->y, $this->w_col1, $this->h);
			$this->PDF->MultiCell($this->w_col1, $this->h_tab, $col1, 0, 'L');
			$this->PDF->SetXY($this->x_col2, $this->y);
			$this->PDF->Rect($this->x_col2, $this->y, $this->w_col2, $this->h);
			$this->PDF->MultiCell($this->w_col2, $this->h_tab, $col2, 0, 'L');
			$this->PDF->SetXY($this->x_col3, $this->y);
			$this->PDF->Rect($this->x_col3, $this->y, $this->w_col3, $this->h);
			$this->PDF->MultiCell($this->w_col3, $this->h_tab, $col3, 0, 'R');
			$this->PDF->SetXY($this->x_col4, $this->y);
			$this->PDF->Rect($this->x_col4, $this->y, $this->w_col4, $this->h);
			$this->PDF->MultiCell($this->w_col4, $this->h_tab, $col4, 0, 'R');
			$this->PDF->SetXY($this->x_col5, $this->y);
			$this->PDF->Rect($this->x_col5, $this->y, $this->w_col5, $this->h);
			$this->PDF->MultiCell($this->w_col5, $this->h_tab, $col5, 0, 'R');
			$this->y+= $this->h;

			$tab_mnt[$i]['q']=$row->nb;
			$tab_mnt[$i]['p']=$row->prix;
			$tab_mnt[$i]['r']=$row->remise;
			$tab_mnt[$i]['t']=$row->tva;
			$i++;	
				
	}
		
	$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
	$this->PDF->SetX($this->marge_gauche);
	$this->PDF->SetY($this->y);
	$this->PDF->SetFontSize($this->fs);
	$this->PDF->Ln();
	
	//affichage des montants ht, ttc, tva	
	$tab_tot = calc($tab_mnt,2);
	$this->y = $this->PDF->GetY();
	if ($acquisition_gestion_tva) $this->h = $this->h_tot * 3;
		else $this->h = $this->h_tot;
	$this->s = $this->y + $this->h;
	
	if ($this->s > ($this->hauteur_page-$this->marge_bas)){
	
		$this->PDF->AddPage();
		$this->PDF->SetXY($this->x_tot, $this->marge_haut);
		$this->y = $this->PDF->GetY(); 
	}
	
	if ($acquisition_gestion_tva) {
		$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ht'], 1, 0, 'L',0);
		$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['ht'],2,'.','' )." ".$cde->devise, 1, 1, 'R',0);
		$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_tva'], 1, 0, 'L',0);
		$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['tva'],2,'.','' )." ".$cde->devise, 1, 1,'R',0);	 		 	
	}
	$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ttc'], 1, 0, 'L',0);
	$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['ttc'],2,'.','' )." ".$cde->devise, 1, 1, 'R',0);	 	
	$this->PDF->Ln();
	
	//Affichage conditions de paiement
	$text_paiement = $msg['acquisition_mode_pai'];
	if ($fou->num_paiement) {
		$pai = new paiements($fou->num_paiement); 
		$text_paiement.= "$pai->libelle";
		$this->PDF->MultiCell($this->w, $this->h_tab, $text_paiement, 0, 'L', 0);
		$this->PDF->Ln();
	}
	
	//Affichage texte after
	if ($this->text_after != '') {
		$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_after, 0, 'J', 0);
		$this->PDF->Ln();		
	}
	
	//Affichage signature
	$this->PDF->Ln();		
	$this->PDF->SetFontSize($this->fs_sign);
	$this->PDF->SetX($this->x_sign);
	$this->PDF->MultiCell($this->l_sign, $this->h_sign, $this->text_sign, 0, 'L', 0);
				

}
	
	function getLettre($format=0,$name='commande.pdf') {
		if (!$format) {
			return $this->PDF->OutPut();
		} else {
			return $this->PDF->OutPut($name,'S');
		}
	}
	
	function getFileName() {
		return $this->filename;
	}
	
	//Entete de tableau
	function doEntete() {
		$this->PDF->SetXY($this->x_num,$this->y);
		$this->PDF->MultiCell($this->w_num, $this->h_num, $this->text_num_ech, 0, 'L');
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_col1, $this->y);
		$this->PDF->Rect($this->x_col1, $this->y, $this->w_col1, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col1, $this->h_tab, $this->txt_header_col1, 0, 'L');
		$this->PDF->SetXY($this->x_col2, $this->y);
		$this->PDF->Rect($this->x_col2, $this->y, $this->w_col2, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col2, $this->h_tab, $this->txt_header_col2, 0, 'L');
		$this->PDF->SetXY($this->x_col3, $this->y);
		$this->PDF->Rect($this->x_col3, $this->y, $this->w_col3, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col3, $this->h_tab, $this->txt_header_col3, 0, 'L');
		$this->PDF->SetXY($this->x_col4, $this->y);
		$this->PDF->Rect($this->x_col4, $this->y, $this->w_col4, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col4, $this->h_tab, $this->txt_header_col4, 0, 'L');
		$this->PDF->SetXY($this->x_col5, $this->y);
		$this->PDF->Rect($this->x_col5, $this->y, $this->w_col5, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col5, $this->h_tab, $this->txt_header_col5, 0, 'L');
	}

}


class lettreCommande_factory {
	
	public static function make() {
		
		global $acquisition_pdfcde_print, $base_path;
		$className = 'lettreCommande_PDF';
		if (file_exists("$base_path/acquisition/achats/commandes/$acquisition_pdfcde_print.class.php")) {
			require_once("$base_path/acquisition/achats/commandes/$acquisition_pdfcde_print.class.php");
			$className = $acquisition_pdfcde_print;	
		}
		return new $className();
	}
}