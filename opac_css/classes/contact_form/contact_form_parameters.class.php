<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_parameters.class.php,v 1.2.6.1 2019-11-07 16:10:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class contact_form_parameters {
	
	/**
	 * Liste des paramètres
	 */
	protected $parameters;
	
	public function __construct() {
		$this->_init_parameters();
		$this->fetch_data();
	}
	
	protected function _get_field($type='text', $display=0, $mandatory=0, $readonly=0) {
		return array(
			'type' => $type,
			'display' => $display,
			'mandatory' => $mandatory,
			'readonly' => $readonly
		);
	}
	
	protected function _init_parameters() {
		$this->parameters = array(
				'fields' => array(
					'name' => $this->_get_field('text', 1, 1),
					'firstname' => $this->_get_field('text', 1, 1),
					'group' => $this->_get_field(),
					'email' => $this->_get_field('email', 1, 1, 1),
					'tel' => $this->_get_field(),
				    'attachments' => $this->_get_field('file')
				),
				'recipients_mode' => 'by_persons',
			    'email_object_free_entry' => 0,
		    	'email_content' => '',
				'confirm_email' => 1
		);
	}
	
	protected function fetch_data() {
		
		$query = 'select valeur_param from parametres where type_param="pmb" and sstype_param="contact_form_parameters"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->valeur_param) {
				$this->parameters = unserialize($row->valeur_param);
			}
		}
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
}