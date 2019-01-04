<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_request.class.php,v 1.5 2018-08-30 14:09:07 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rent/rent_account.class.php");
require_once($class_path.'/html2pdf/html2pdf.class.php');

class rent_request extends rent_account {
	
	public function __construct($id) {
		parent::__construct($id);
		$this->object_type = 'request';
	}
	
	/**
	 * Retourne la fonction JS d'initialisation du formulaire (display)
	 */
	protected function get_function_form_hide_fields() {
		return 'request_form_hide_fields();';
	}
		
	public function gen_command() {
		global $msg, $include_path, $charset;
	
		$tpl = $include_path.'/templates/rent/rent_account_command.tpl.html';
		if (file_exists($include_path.'/templates/rent/rent_account_command_subst.tpl.html')) {
			$tpl = $include_path.'/templates/rent/rent_account_command_subst.tpl.html';
		}
		$h2o = H2o_collection::get_instance($tpl);
		$command_tpl = $h2o->render(array('account' => $this));
		if($charset != "utf-8"){
			$command_tpl=utf8_encode($command_tpl);
		}
		$html2pdf = new HTML2PDF('L','A4','fr');
		$html2pdf->WriteHTML($command_tpl);
		$html2pdf->Output(sprintf($msg['acquisition_request_pdf_filename'], $this->get_supplier()->raison_sociale, $this->get_id()).'.pdf','D');
	}
}