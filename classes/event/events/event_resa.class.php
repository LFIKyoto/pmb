<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_resa.class.php,v 1.1 2016-11-25 15:18:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_resa extends event {
	protected $resa;

	public function get_resa() {
		return $this->resa;
	}
	
	public function set_resa($resa){
		$this->resa = $resa;
	}
	
	public function get_result() {
		return $this->result;
	}
	
	public function set_result($result){
		$this->result = $result;
	}
}
