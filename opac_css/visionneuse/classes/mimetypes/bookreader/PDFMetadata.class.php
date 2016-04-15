<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PDFMetadata.class.php,v 1.3 2013-04-23 13:06:18 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class PDFMetadata {
	var $pdf;					//Document PDF
	var $metadatas = array();	//Tableau des metadatas
	var $title;					//Titre
	var $author;				//Author
	var $creator;				
	var $producer;
	var $creation_date;			//Date de création
	var $tagged;
	var $nb_pages;				//Nombre de pages
	var $encrypted;
	var $page_size;				//Taille des pages l x h pts
	var $file_size;				//Taille du fichier
	var $optimized;
	var $version;				//Numéro de version PDF
	var $bookmarks = array();	//Tableau des bookmarks
	var $pagesSizes = array();
	
	function PDFMetadata($pdf){
		$this->pdf = $pdf;
		$this->setMetadatas();
		$this->title = $this->metadatas["Title"];
		$this->author = $this->metadatas["Author"];
		$this->creator = $this->metadatas["Creator"];
		$this->producer = $this->metadatas["Producer"];
		$this->creation_date = $this->metadatas["CreationDate"];
		$this->tagged = $this->metadatas["Tagged"];
		$this->nb_pages = $this->metadatas["Pages"];
		$this->encrypted = $this->metadatas["Encrypted"];
		$this->page_size = $this->metadatas["Page size"];
		$this->file_size = $this->metadatas["File size"];
		$this->optimized = $this->metadatas["Optimized"];
		$this->version = $this->metadatas["PDF version"];
		$this->getPagesSizes();
	}
	
	function getPagesSizes(){
		if(!count($this->pagesSizes)){
			exec("pdfinfo -f 1 -l ".$this->nb_pages." ".$this->pdf , $output);
			for ($i = 0; $i < count($output); $i++) {
				if (substr($output[$i],0,5) == "Page "){
					if(preg_match('/^Page\D+(\d+)\D+(\d+[.]?\d+?)\D+(\d+[.]?\d+)/',$output[$i],$matches)){
						$this->pagesSizes[$matches[1]] = array(
							'width' => $matches[2],
							'height' => $matches[3]
						);
					}
				}
			}
		}
	}
	
	function setMetadatas(){
		$output = array();
		exec("pdfinfo ".$this->pdf , $output);
		for ($i = 0; $i < count($output); $i++) {
			$meta = explode(":", $output[$i]);
			$this->metadatas[trim($meta[0])] = trim($meta[1]);
		}
	}
	
	function getBookmarks(){
		$output = array();
		exec("pdftk ".$this->pdf." dump_data" , $output);
		for ($i = 0; $i < count($output); $i++) {
			//On verifie si l'info correspond au titre d'un bookmark
			$info_title = explode(":", $output[$i]);
			if (trim($info_title[0]) == "BookmarkTitle") {
				$title = trim($info_title[1]);
				$i++;
				//On récupère la profondeur du bookmark
				$info_deep = explode(":", $output[$i]);
				$deep = trim($info_deep[1]);
				$i++;
				//On récupère la page du bookmark
				$info_page_number = explode(":", $output[$i]);
				$page = trim($info_page_number[1]);
				$this->bookmarks[] = array(
						"label" => $title,
						"deep" => $deep,
						"page" => $page*1,
						"analysis_page" => $page*1,
						);
			}
		}
		return $this->bookmarks;
	}
}