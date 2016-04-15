<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audio.class.php,v 1.3 2013-07-24 13:09:22 arenou Exp $

require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");

class audio extends affichage{
	var $doc;					//le document numérique à afficher
	var $driver;				//class driver de la visionneuse
	var $params;				//paramètres éventuels
	var $toDisplay= array();	//tableau des infos à afficher	
	var $tabParam = array();	//tableau décrivant les paramètres de la classe
	var $parameters = array();	//tableau des paramètres de la classe
 
    function audio($doc=0) {
    	if($doc){
    		$this->doc = $doc; 
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    	}
    }
    
    function fetchDisplay(){
    	global $base_path;
    	global $visionneuse_path;
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	// lecture audio
    	$this->toDisplay["doc"]="
    	<object type='application/x-shockwave-flash' data='$visionneuse_path/classes/mimetypes/audio/player/player_mp3.swf' width='".$this->parameters["size_x"]."' height='".$this->parameters["size_y"]."'>
			<param name='wmode' value='transparent' />
			<param name='movie' value='$visionneuse_path/classes/mimetypes/audio/player/player_mp3.swf' />
			<param name='FlashVars' value='mp3=".rawurlencode($this->driver->getDocumentUrl($this->doc->id))."&amp;showstop=".$this->parameters["showstop"]."&amp;showinfo=".$this->parameters["showinfo"]."&amp;showvolume=".$this->parameters["showvolume"]."' />
			<embed pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'>
		</object>";
		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;  	
    }
    
    function render(){
    }
    
    function getTabParam(){

    	$this->tabParam = array(
			"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur du lecteur"),
			"size_y"=>array("type"=>"text","name"=>"size_y","value"=>$this->parameters['size_y'],"desc"=>"Hauteur du lecteur"),
    		"showstop"=>array("type"=>"checkbox","name"=>"showstop","value"=>1,"desc"=>"Bouton stop"),
    		"showinfo"=>array("type"=>"checkbox","name"=>"showinfo","value"=>1,"desc"=>"Informations sur le document audio"),
    		"showvolume"=>array("type"=>"checkbox","name"=>"showvolume","value"=>1,"desc"=>"R&eacute;glage du volume")
		);
       	return $this->tabParam;
    }
    
	function getParamsPerso(){
		$params = $this->driver->getClassParam('audio');
		$this->unserializeParams($params);
		
		if($this->parameters['size_x'] == 0) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if($this->parameters['size_y'] == 0) $this->parameters['size_y'] = $this->driver->getParam("maxY");
	}
	
	function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!$this->parameters['showstop']) $this->parameters['showstop'] = 0;
		if(!$this->parameters['showinfo']) $this->parameters['showinfo'] = 0;
		if(!$this->parameters['showvolume']) $this->parameters['showvolume'] = 0;
		return $this->parameters;
	}
	
	function serializeParams($paramsToSerialized){
		if(!$paramsToSerialized['showstop']) $paramsToSerialized['showstop'] = 0;
		if(!$paramsToSerialized['showinfo']) $paramsToSerialized['showinfo'] = 0;
		if(!$paramsToSerialized['showvolume']) $paramsToSerialized['showvolume'] = 0;
		$this->parameters =$paramsToSerialized;
		return serialize($paramsToSerialized);
	}
}
?>
