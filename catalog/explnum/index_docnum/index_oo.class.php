<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_oo.class.php,v 1.6 2019-08-01 13:16:35 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/zip.class.php";

/**
 * Classe qui permet la gestion de l'indexation des fichiers OpenOffice
 */
class index_oo {
	public $fichier = '';
	
	public function __construct($filename, $mimetype = '', $extension = '') {
		$this->fichier = $filename;
	}
	
	/**
	 * M�thode qui retourne le texte � indexer des docs OpenOffice
	 */
	public function get_text($filename) {
		global $charset;
		$zip = new zip($filename);
		$texte = $zip->getFileContent("content.xml");			
		//On enl�ve toute les balises offices
		preg_match_all("(<([^<>]*)>)", $texte, $result);
		
		$nb_results = count($result[0]);
		for ($i = 0; $i < $nb_results; $i++) {
			$texte = str_replace($result[0][$i], " ", $texte);
		}
		$texte = str_replace("&apos;", "'", $texte);
		$texte = str_replace("&nbsp;", " ", $texte);
		if ($charset != "utf-8") {
			$texte = utf8_decode($texte);		
		}
		$texte = html_entity_decode($texte, ENT_QUOTES, $charset);
		return $texte;
	}
}
?>