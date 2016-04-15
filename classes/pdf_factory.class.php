<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pdf_factory.class.php,v 1.4 2013-01-17 15:49:08 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/fpdf.class.php");
require_once("$class_path/ufpdf.class.php");

class pmb2FPDF extends FPDF {

	var $footer_type=0;
	var $y_footer;
	var $fs_footer;
	var $msg_footer = '';
	var $npage = 1;
	
	function Footer() {
		
		global $msg;

		switch ($this->footer_type) {
			
			case '1' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->fs_footer,$this->msg_footer.$this->PageNo().' / '.$this->AliasNbPages,0,0,'C');
	    		$this->npage++;
				break;
			case '2' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->fs_footer,$this->msg_footer.$this->npage,0,0,'C');
	    		$this->npage++;
				break;
			case '3' :
	    		$this->SetY(-$this->y_footer);
	    		$this->MultiCell(0,$this->fs_footer,$this->msg_footer.$this->PageNo().' / '.$this->AliasNbPages,0,'C');
	    		$this->npage++;
				break;
			default :
			case '0';
				break;
		}
	}
}

class pmb2UFPDF extends UFPDF {
	
	var $footer_type=0;
	var $y_footer;
	var $fs_footer;
	var $msg_footer = '';
	var $npage = 1;
	
	function Footer() {
		
		global $msg;

		switch ($this->footer_type) {
			
			case '1' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->fs_footer,$this->msg_footer.$this->npage,0,0,'C');
	    		$this->npage++;
				break;
			case '2' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->fs_footer,$this->msg_footer.$this->npage,0,0,'C');
	    		$this->npage++;
				break;
			case '3' :
	    		$this->SetY(-$this->y_footer);
	    		$this->MultiCell(0,$this->fs_footer,$this->msg_footer.$this->npage,0,'C');
	    		$this->npage++;
				break;
			default :
			case '0';
				break;
		}
	}
}


class pdf_factory {
	
	public static function make($orientation='P', $unit='mm', $format='A4') {
		
		global $charset;
		
		$className = 'pmb2FPDF';
		if($charset=='utf-8') {
			$className = 'pmb2UFPDF';
		}		
		return new $className($orientation, $unit, $format);
	}
}

