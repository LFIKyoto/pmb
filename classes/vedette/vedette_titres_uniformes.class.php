<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_titres_uniformes.class.php,v 1.1 2014-08-08 13:29:23 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/titre_uniforme.class.php");

class vedette_titres_uniformes extends vedette_element{
	
	public function set_vedette_element_from_database(){
		$titre_uniforme = new titre_uniforme($this->id);
		$this->isbd = $titre_uniforme->name;
	}
}
