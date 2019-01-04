<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet_stick.class.php,v 1.2 2016-09-14 13:04:15 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class sticks_sheet_stick {
		
	/**
	 * Marge int�rieure haute de l'�tiquette
	 * @var float
	 */
	protected $top_margin;
	
	/**
	 * Marge int�rieure basse de l'�tiquette
	 * @var float
	 */
	protected $bottom_margin;
	
	/**
	 * Marge int�rieure gauche de l'�tiquette
	 * @var float
	 */
	protected $left_margin;
	
	/**
	 * Marge int�rieure droite de l'�tiquette
	 * @var float
	 */
	protected $right_margin;
	
	/**
	 * Largeur de l'�tiquette
	 * @var float
	 */
	protected $width;
	
	/**
	 * Hauteur de l'�tiquette
	 * @var float
	 */
	protected $height;
	
	public function set_top_margin($top_margin) {
		$this->top_margin = $top_margin;
	}
	
	public function set_bottom_margin($bottom_margin) {
		$this->bottom_margin = $bottom_margin;
	}
	
	public function set_left_margin($left_margin) {
		$this->left_margin = $left_margin;
	}
	
	public function set_right_margin($right_margin) {
		$this->right_margin = $right_margin;
	}
	
	public function set_width($width) {
		$this->width = $width;
	}
	
	public function set_height($height) {
		$this->height = $height;
	}
	
	public function zoom_out(&$pdf, $string, $width = 0) {
		if (!$width){
			$width = $this->width;
		}
		while($pdf->GetStringWidth($string) > $width) {
			$pdf->SetFontSize($pdf->FontSizePt-0.5);
		}
	}
	
}