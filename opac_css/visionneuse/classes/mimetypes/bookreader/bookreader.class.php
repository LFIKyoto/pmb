<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreader.class.php,v 1.21 2014-10-13 10:23:36 arenou Exp $

require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");
//require_once($visionneuse_path."/classes/mimetypes/converter_factory.class.php");
// require_once($visionneuse_path."/../classes/docbnf.class.php");
// require_once($visionneuse_path."/../classes/docbnf_zip.class.php");
// ini_set("display_errors",1);
// error_reporting(E_ALL & ~E_NOTICE);

require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderPDF.class.php");
require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderBNF.class.php");
require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderEPUB.class.php");

// ini_set("display_errors", 1);
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

class bookreader extends affichage{
	var $doc;					//le document numérique à afficher
	var $driver;				//class driver de la visionneuse
	var $params;				//paramètres éventuels
	var $toDisplay= array();	//tableau des infos à afficher	
	var $tabParam = array();	//tableau décrivant les paramètres de la classe
	var $parameters = array();	//tableau des paramètres de la classe
	var $mimeTypeClass;			//instance selon le mimetype
 
    function bookreader($doc=0) {
    	if($doc){
    		$this->doc = $doc; 
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    		$this->getTabParam();
    		$this->allowedFunction = array(
    			"getPage",
    			"getWidth",
    			"getHeight",
    			"search",
    			"getBookmarks",
    			"getPDF",
    			"getCSS",
    			"getPageCount"
    		);
    		$this->driver->cleanCache();
    		if (!$this->driver->isInCache($this->doc->id)) {
    			$this->driver->setInCache($this->doc->id,$this->driver->openCurrentDoc());
    		}
    	}
    	
    	switch($this->doc->mimetype){
    		case "application/pdf" : 
    		case "application/x-pdf" : 
    			$this->mimeTypeClass = new bookreaderPDF($this->doc, $this->parameters);
    			break;
    		case "application/bnf" :
    		case "application/bnf+zip" :
    			$this->mimeTypeClass = new bookreaderBNF($this->doc);
    			break;
    		case "application/epub+zip" :
    			$this->mimeTypeClass = new bookreaderEPUB($this->doc, $this->parameters);
    			break;
    	}
    }
    
    function fetchDisplay(){
    	global $visionneuse_path,$base_path;
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//la visionneuse pdf
    	
    	if($this->parameters['pdf_allowed'] && ($this->doc->mimetype != "application/pdf")){
    		$this->toDisplay["doc"].="
    		<div><a href='".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getPDF")."' target='_blank'>G&eacute;n&eacute;rer un PDF</a></div>";
    	}
    	$this->toDisplay["doc"].="
    	<script type='text/javascript'>
    		window.onload = function(){
				checkSize();
				document.getElementById('bookreader_frame').src='".$this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id."&myPage=".$this->driver->params['page']."&user_query=".$this->driver->params['user_query'])."';
    		}
			function checkSize(){
				var iframe= document.getElementById('bookreader_frame');
				if (isNaN(iframe.width) || iframe.width/getFrameWidth() <= 0.9 || iframe.width/getFrameWidth() >= 1){
					iframe.width = '95%';
					iframe.height = ((getFrameHeight()-40-80)*0.95)+'px';
				}				
			}
		</script>
		<iframe id='bookreader_frame'></iframe> 
		";
    	//if ($this->parameters['autoresize'] == 1)
		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;  	
    }
    
    function render(){
    	global $visionneuse_path;
    	
    	$subst_style="";
    	if(file_exists($visionneuse_path."/classes/mimetypes/bookreader/BookReader/BookReader_subst.css")){
    		$subst_style="<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader_subst.css'/>";
    	}
    	//$doc = new docbnf_zip($visionneuse_path."/temp/".$this->doc->id);
    	print "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html>
    <head>
    	<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader.css'/>
    	<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReaderPerso.css'/>
    	<link rel='stylesheet' type='text/css' href='".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getCSS'/>")."
	    $subst_style
	    <script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/excanvas.compiled.js'></script>
    	<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery-1.4.2.min.js'></script>
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery-ui-1.8.5.custom.min.js?v=3.0.9'></script>
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/dragscrollable.js'></script>
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.colorbox-min.js'></script>
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.ui.ipad.js'></script>
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.bt.min.js'></script>	
		<script type='text/javascript' src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader.js?v=3.0.9'></script>	
    	<script type='text/javascript'>
    		$(document).ready(function() {
	    		br = new BookReader();
	    		
				//Ici on génère le bloc d'informations...
				".$this->genereInfos()."
				
				//mode par défaut
				br.mode = br.".$this->parameters['mode_affichage'].";
				
	    		br.pagesSizes= ".$this->getJSPagesSizes().";
				
				br.getPageWidth = function(index) {
					if(this.pagesSizes[this.getPageNum(index)]){
				   		return this.pagesSizes[this.getPageNum(index)].width;
				   	}else return 480;
				}
	
				br.getPageHeight = function(index) {
					if(this.pagesSizes[this.getPageNum(index)]){
				    	return this.pagesSizes[this.getPageNum(index)].height;
				   	}else return 640;
				}
				
				br.getPageURI = function(index, reduce, rotate) {
				    // reduce and rotate are ignored in this simple implementation, but we
				    // could e.g. look at reduce and load images from a different directory
				    // or pass the information to an image server
				    var url = '".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getPage")."&page='+(index+1);
				    return url;
				}
				
				// Return which side, left or right, that a given page should be displayed on
				br.getPageSide = function(index) {
					if (0 == (index & 0x1)) {
						return 'R';
					} else {
						return 'L';
					}
				}
				
				br.getSpreadIndices = function(pindex) {   
					var spreadIndices = [null, null]; 
					if ('rl' == this.pageProgression) {
						// Right to Left
						if (this.getPageSide(pindex) == 'R') {
							spreadIndices[1] = pindex;
							spreadIndices[0] = pindex + 1;
						} else {
							// Given index was LHS
							spreadIndices[0] = pindex;
							spreadIndices[1] = pindex - 1;
						}
					} else {
						// Left to right
						if (this.getPageSide(pindex) == 'L') {
							spreadIndices[0] = pindex;
							spreadIndices[1] = pindex + 1;
						} else {
							// Given index was RHS
							spreadIndices[1] = pindex;
							spreadIndices[0] = pindex - 1;
						}
					}
					return spreadIndices;
				}			
							
				br.getPageNum = function(index) {
				    return index+1;
				}
				
				br.leafNumToIndex = function(leaf) {
				    return leaf-1;
				}
	
				br.numLeafs = ".$this->getPageCount()." ;
				
				// Book title and the URL used for the book title link
				br.bookTitle= '".addslashes($this->doc->titre)."';
				br.bookUrl  = '".addslashes($this->getBookURL())."';
				br.logoURL = '".addslashes($this->driver->getUrlBase())."';
				
				// Override the path used to find UI images
				br.imagesBaseURL = '".$visionneuse_path."/classes/mimetypes/bookreader/BookReader/images/';
				
				br.getEmbedCode = function(frameWidth, frameHeight, viewParams) {
				    return \"\";
				}
				
				br.search = function(term){
					$('#textSrch').blur();
					var url = '".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=search")."&user_query='+".pmb_escape()."(term);
					term = term.replace(/\//g, ' '); // strip slashes, since this goes in the url
					this.searchTerm = term;
					this.showProgressPopup('<img id=\"searchmarker\" src=\"'+this.imagesBaseURL + 'marker_srch-on.png'+'\"> Recherche en cours');
					$.ajax({url:url, dataType:'json',success : br.BRSearchCallback}); 
				}
				
				br.getBookmarksCallback = function(result){
					if(result){
						for(var i=0 ; i<result.length ; i++){
							if(result[i].deep>0)
							br.addChapter(result[i].label, result[i].analysis_page*1, result[i].page*1-1);
						}
					}
				}
				
				// Let's go!
				br.init();
				
				$('#BRreturn a').attr('target', '_blank');
				$('#BRtoolbar').find('.read').hide();
				$('#BRtoolbar .play').show();
				$('#BRtoolbar .share').hide();
	
				//affichage des Bookmarks !
				$.ajax({url:'".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getBookmarks")."', dataType:'json',success : br.getBookmarksCallback});";
    	
    if($this->driver->params['page']){
    	print "
    			br.jumpToIndex(".($this->driver->params['page']-1).");";
    }

	if($this->parameters['allow_search']){
		print "			
				//Recherche auto à l'ouverture
				var user_query = '".$this->driver->params['user_query']."';
				if ((user_query) && (user_query != '*')) {
					br.search(user_query);
				}";
	}
	print "
	    	});
    	</script>
    </head>
    <body>
    	<div id='BookReader'></div>
    </body>
</html>";
    	
    }
    
    function getBookURL(){
    	return $this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id);
    }
    
    function getCSS(){
    	$width = 0;
    	if($this->parameters['logo_url']){
    		$img = imagecreatefromstring(file_get_contents($this->parameters['logo_url']));
			if($img){
    			$width = imagesx($img);
			}
    	}
    	print "#BRtoolbar a.logo {
	display: block;
	float: left;
	width: ".$width."px;
	height: 40px;
	margin: 0 5px;
	background: transparent url(".$this->parameters['logo_url'].") no-repeat 0 0;
}";
    }
    
    function getTabParam(){

    	$this->tabParam = array(
    		"pdf_allowed"=>array("type"=>"checkbox","name"=>"pdf_allowed","value"=>1,"desc"=>"Autoriser l'export au format PDF"),
    		"pdf_creator"=>array("type"=>"text","name"=>"pdf_creator","value"=>$this->parameters['pdf_creator'],"desc"=>"Auteur du PDF"),
     		"pdf_footer_name"=>array("type"=>"text","name"=>"pdf_footer_name","value"=>$this->parameters['pdf_footer_name'],"desc"=>"Libell&eacute; du pied de page"),
     		"pdf_footer_link"=>array("type"=>"text","name"=>"pdf_footer_link","value"=>$this->parameters['pdf_footer_link'],"desc"=>"Lien du pied de page"),
     		"logo_url"=>array("type"=>"text","name"=>"logo_url","value"=>$this->parameters['logo_url'],"desc"=>"Lien vers l'image du logo (en haut &agrave; gauche) hauteur maximum : 40px"),
    		"resolution_image"=>array("type"=>"text","name"=>"resolution_image","value"=>$this->parameters['resolution_image'],"desc"=>"R&eacute;solution des images g&eacute;n&eacute;r&eacute;es par pdftoppm"),
    		"format_image"=>array("type"=>"radio","name"=>"format_image","value"=>array("jpeg" => "jpeg", "png" => "png", "imagick" => "imagick"),"desc"=>"Format des images g&eacute;n&eacute;r&eacute;es par pdftoppm. Choisir imagick si les attributs -png et -jpeg ne sont pas support&eacute;s par pdftoppm."),
    		"mode_affichage"=>array("type"=>"radio","name"=>"mode_affichage","value"=>array("constMode1up" => "Mode 1 page", "constMode2up" => "Mode 2 pages", "constModeThumb" => "Mode vignettes"),"desc"=>"Mode d'affichage par d&eacute;faut"),
    		"allow_search"=>array("type"=>"radio","name"=>"allow_search","value"=>array("0" => "Non", "1" => "Oui"),"desc"=>"Lancer automatiquement une recherche à l'ouverture"),
    	);
       	return $this->tabParam;
    }
    
	function getParamsPerso(){
		$params = $this->driver->getClassParam('bookreader');
		$this->unserializeParams($params);
		if($this->parameters['size_x'] == 0) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if($this->parameters['size_y'] == 0) $this->parameters['size_y'] = $this->driver->getParam("maxY");
		if($this->parameters['resolution_image'] == 0) $this->parameters['resolution_image'] = 100;
		if($this->parameters['format_image'] == "") $this->parameters['format_image'] = "imagick";
		if(!$this->parameters['mode_affichage']) $this->parameters['mode_affichage'] = "constMode1up";
	}
	
	function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!$this->parameters['print_allowed']) $this->parameters['print_allowed'] = 0;
		return $this->parameters;
	}
	
	function serializeParams($paramsToSerialized){
		if(!$paramsToSerialized['print_allowed']) $paramsToSerialized['print_allowed'] = 0;
		$this->parameters =$paramsToSerialized;
		return serialize($paramsToSerialized);
	}
	
	function getPage(){
		global $visionneuse_path;
		session_write_close();
		$page = 1;
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		$this->mimeTypeClass->getPage($page);
	}

	function getWidth(){
		global $visionneuse_path;
		$page = 1;
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		$this->mimeTypeClass->getWidth($page);
	}	
	
	function getHeight(){
		global $visionneuse_path;
		$page = 1;
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		$this->mimeTypeClass->getHeight($page);
	}
	
	function search(){
		global $visionneuse_path;
		$user_query = 1;
		if(isset($_GET['user_query'])){
			$user_query = $_GET['user_query'];
		}
		$result = $this->mimeTypeClass->search($user_query);

		print json_encode($this->utf8_normalize($result));
	}
	
	function getBookmarks(){
		print json_encode($this->utf8_normalize($this->mimeTypeClass->getBookmarks()));
	}
	
	function getPDF(){
		global $visionneuse_path;
		if($this->parameters['pdf_allowed']){
			$pdfParams = array();
			
			//Auteur du PDF
			if(!$this->parameters['pdf_creator']){
				$this->parameters['pdf_creator'] = "PMB";
			}
			$pdfParams['creator'] = $this->utf8_normalize($this->parameters['pdf_creator']);
	
			//Définition du footer
			if($this->parameters['pdf_footer_name'] || $this->parameters['pdf_footer_link']){
				$pdfParams['footers']=array(
					'all' => array(
						'name' => $this->utf8_normalize($this->parameters['pdf_footer_name']),
						'link' => $this->utf8_normalize($this->parameters['pdf_footer_link'])
					)		
				);
			}
			
			//Nommage du fichier de sortie
			$pdfParams['outname'] = $this->utf8_normalize($this->doc->titre.".pdf");

			$this->mimeTypeClass->getPDF($pdfParams);
		}
	}
	
	function getJSPagesSizes(){
		$this->mimeTypeClass->getPagesSizes();
		$js = json_encode($this->mimeTypeClass->pagesSizes);
		return $js;
	}
	
	function utf8_normalize($value){
		global $charset;
		if($charset != "utf-8"){
			if(is_string($value)){
				$value = utf8_encode($value);
			}else{
				foreach($value as $key => $val){
					$value[$key] = $this->utf8_normalize($val);
				}
			}
		}
		return $value;
	}
	
	function getPageCount(){
		global $visionneuse_path;
		
		return $this->mimeTypeClass->getPageCount();
	}
	
	function genereInfos(){
		$infos = $this->doc->driver->getCurrentBiblioInfos();
		$bloc_infos = "br.buildInfoDiv = function(jInfoDiv) {
			//Le titre du document
			jInfoDiv.find('.BRfloatTitle a').attr({
				'href': this.bookUrl, 'alt': this.bookTitle}).text(this.bookTitle);
				//La première page en couverture...
				jQuery('<img>', {
					src : br.getPageURI(0),
					height : '200px',
				}).appendTo(jInfoDiv.find('.BRfloatCover'));
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));
 				jQuery('<p>', {
 					text : 'Titre : ".addslashes($infos['title']['value'])."',
 				}).appendTo(jInfoDiv.find('.BRfloatTitle'));";
		if($infos['author']['value']){
			$bloc_infos.= "
				jQuery('<p>".addslashes(($infos['author']['label']))." : ".addslashes(($infos['author']['value']))."</p>').appendTo(jInfoDiv.find('.BRfloatTitle'));";
		}		
		if($infos['date']['value']){
			$bloc_infos.= "
 				jQuery('<p>', {
 					text : '".addslashes(($infos['date']['label']))." : ".addslashes($infos['date']['value'])."',
 				}).appendTo(jInfoDiv.find('.BRfloatTitle'));";
		}	
 		$bloc_infos.= "
 				jQuery(\"<p><a href='".$infos['permalink']['value']."' target='_blank'>".addslashes(($infos['permalink']['label']))."</a></p>\").appendTo(jInfoDiv.find('.BRfloatTitle'));";	
	
//  		if($this->parameters['pdf_allowed']){
//  			$bloc_infos.= "
//  				jQuery(\"<p><a href='./visionneuse.php?lvl=ajax&explnum_id=".$this->doc->id."&method=getPDF' target='_blank'>Télécharger le PDF</a></p>\").appendTo(jInfoDiv.find('.BRfloatFoot'));";
//  		}				
 		$bloc_infos.= "		
			}";
		return $bloc_infos;
	}
}
?>
