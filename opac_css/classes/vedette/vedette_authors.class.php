<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authors.class.php,v 1.1 2015-03-10 08:46:23 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/author.class.php");

class vedette_authors extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_auteur";
	
	public function set_vedette_element_from_database(){
		$author = new auteur($this->id);
		$this->isbd = $author->isbd_entry;
	}
}
