<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_PDF.class.php,v 1.1 2018-08-07 12:42:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/pdf_factory.class.php");

class lettre_PDF {
	
	public $PDF;
	public $orient_page = 'P';			//Orientation page (P=portrait, L=paysage)
	public $largeur_page = 210;			//Largeur de page
	public $hauteur_page = 297;			//Hauteur de page
	public $unit = 'mm';				//Unite 
	public $marge_haut = 10;			//Marge haut
	public $marge_bas = 20;				//Marge bas
	public $marge_droite = 10;			//Marge droite
	public $marge_gauche = 10;			//Marge gauche
	public $w = 190;					//Largeur utile page
	public $font = 'Helvetica';			//Police
	public $fs = 10;					//Taille police
	
	public function __construct() {
		$this->_init();
		$this->_open();
	}
	
	protected function _init() {
		global $msg, $charset, $pmb_pdf_font;
			
		if($this->get_parameter_value('orient_page')) {
			$this->orient_page = $this->get_parameter_value('orient_page');
		}
		
		$format_page = explode('x',$this->get_parameter_value('format_page'));
		if($format_page[0]) $this->largeur_page = $format_page[0];
		if($format_page[1]) $this->hauteur_page = $format_page[1];
		
		$this->PDF = pdf_factory::make($this->orient_page, $this->unit, array($this->largeur_page, $this->hauteur_page));
		
		$this->_init_marges();
		
		$this->w = $this->largeur_page-$this->marge_gauche-$this->marge_droite;
		
		$this->font = $pmb_pdf_font;
		if($this->get_parameter_value('text_size')) {
			$this->fs = $this->get_parameter_value('text_size');
		}
	}
	
	protected function _open() {
		$this->PDF->Open();
		$this->PDF->SetMargins($this->marge_gauche, $this->marge_haut, $this->marge_droite);
		$this->PDF->setFont($this->font);
		
		$this->PDF->footer_type=1;
	}
	
	protected function get_parameter_value($name) {
		//A surcharger
	}
	
	protected function _init_marges() {
		$marges_page = explode(',', $this->get_parameter_value('marges_page'));
		if ($marges_page[0]) $this->marge_haut = $marges_page[0];
		if ($marges_page[1]) $this->marge_bas = $marges_page[1];
		if ($marges_page[2]) $this->marge_droite = $marges_page[2];
		if ($marges_page[3]) $this->marge_gauche = $marges_page[3];
	}
	
	protected function _init_position($name, $position=array()) {
		if (isset($position[0]) && $position[0]) $this->x_{$name} = $position[0];
		if (isset($position[1]) && $position[1]) $this->y_{$name} = $position[1];
		if (isset($position[2]) && $position[2]) $this->l_{$name} = $position[2];
		if (isset($position[3]) && $position[3]) $this->h_{$name} = $position[3];
		if (isset($position[4]) && $position[4]) $this->fs_{$name} = $position[4];
	}
	
	public function getLettre($format=0,$name='lettre.pdf') {
		if (!$format) {
			return $this->PDF->OutPut();
		} else {
			return $this->PDF->OutPut($name,'S');
		}
	}
	
	public function getFileName() {
		return $this->filename;
	}
}

// class lettrePDF_factory {

// 	public static function make() {

// 		global $acquisition_pdfdev_print, $base_path;
// 		$className = 'lettreDevis_PDF';
// 		if (file_exists("$base_path/acquisition/achats/devis/$acquisition_pdfdev_print.class.php")) {
// 			require_once("$base_path/acquisition/achats/devis/$acquisition_pdfdev_print.class.php");
// 			$className = $acquisition_pdfdev_print;
// 		}
// 		return new $className();
// 	}
// }