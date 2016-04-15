<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records.class.php,v 1.2 2014-10-07 15:29:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/mono_display.class.php");

class vedette_records extends vedette_element{

	public function set_vedette_element_from_database(){
		$record = new mono_display($this->id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
 		$this->isbd = strip_tags($record->result);
	}
}
