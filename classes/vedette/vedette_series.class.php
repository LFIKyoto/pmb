<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_series.class.php,v 1.1 2014-08-08 13:29:23 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/serie.class.php");

class vedette_series extends vedette_element{
	
	public function set_vedette_element_from_database(){
		$serie = new serie($this->id);
		$this->isbd = $serie->name;
	}
}
