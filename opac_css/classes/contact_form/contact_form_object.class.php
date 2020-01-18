<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_object.class.php,v 1.1.10.1 2019-10-09 08:18:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/translation.class.php");

class contact_form_object {
	
	/**
	 * identifiant de l'objet
	 */
	protected $id;
	
	/**
	 * Libell� de l'objet
	 * @var string
	 */
	protected $label;
	
	/**
	 * Votre message
	 * @var string
	 */
	protected $message;
	
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		
		if($this->id) {
			$query = 'select object_label, object_message from contact_form_objects where id_object ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->object_label;
			$this->message = $row->object_message;
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_translated_label() {
	    return translation::get_text($this->id, 'contact_form_objects', 'object_label', $this->label);
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function get_message() {
	    return $this->message;
	}
	
	public function get_translated_message() {
	    return translation::get_text($this->id, 'contact_form_objects', 'object_message', $this->message);
	}
	
	public function set_message($message) {
	    $this->message = $message;
	}
}