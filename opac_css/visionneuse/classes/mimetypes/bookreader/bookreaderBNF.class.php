<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreaderBNF.class.php,v 1.4 2013-07-24 13:09:22 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class bookreaderBNF {
	var $doc;		//le document BNF à traiter
	var $bnfClass;
	
	function bookreaderBNF($doc){
		$this->doc = $doc;
		$this->getBnfClass();
	}
	
	function getBnfClass(){
		global $visionneuse_path;
		$class_name = $this->doc->driver->getBnfClass($this->doc->mimetype);
		$this->bnfClass = new $class_name($this->doc->driver->get_cached_filename($this->doc->id));
	}
	
	function getPage($page){
		if (!$this->doc->driver->isInCache($this->doc->id."_".$page)) {
			$this->doc->driver->setInCache($this->doc->id."_".$page,$this->bnfClass->get_page_content($page));
		}
		print $this->doc->driver->readInCache($this->doc->id."_".$page);
	}
	
	function getWidth($page){
		print $this->bnfClass->getWidth($page);
	}
	
	function getHeight($page){
		print $this->bnfClass->getHeight($page);
	}
	
	function search($user_query){
		return $this->bnfClass->search($user_query);
	}
	
	function getBookmarks(){
		return $this->bnfClass->getBookmarks();
	}
	
	function getPDF($pdfParams){
		$this->bnfClass->generatePDF($pdfParams);
	}
	
	function getPageCount(){
		return $this->bnfClass->getNbPages();
	}
	
	function getPagesSizes(){
 		$this->pagesSizes= $this->bnfClass->pagesSizes;
	}
}
?>