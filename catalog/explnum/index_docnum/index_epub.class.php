<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_epub.class.php,v 1.3 2017-06-30 14:08:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/epubData.class.php");

/**
 * Classe qui permet la gestion de l'indexation des fichiers epub
 */
class index_epub{
	
	public $fichier='';
	
	public function __construct($filename, $mimetype='', $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * M�thode qui retourne le texte � indexer des epub
	 */
	public function get_text($filename){
		global $charset;
		
		$epub=new epub_Data($this->fichier);
		return $epub->getFullTextContent($charset);
	}
}
?>