<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docNum.class.php,v 1.7 2013-02-22 16:06:33 apetithomme Exp $

require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");
require_once($visionneuse_path."/classes/defaultConf.class.php");
require_once($visionneuse_path."/classes/mimetypeClass.class.php");

class docNum {
	var $infos;
	var $driver;				//classe driver dela visionneuse
	var $displayClass = false;
	var $defaultClass;		
	var $params=array();

	function docNum($infos,$driver,$params=array()) {
		$this->titre = $infos["titre"];
		$this->path = $infos["path"];
		$this->desc = $infos["desc"];
		$this->mimetype = $infos["mimetype"];
		$this->extension = $infos["extension"];
		$this->id = $infos["id"];
		$this->driver = $driver;
		$this->params = $params;
		$this->mimetypeClass = $this->driver->getMimetypeConf();
		if($infos["searchterms"]) $this->search = "#search=\"".trim(stripslashes($infos["searchterms"]))."\"";
		else $this->search = "";
    }

    function fetchDisplay(){
    	global $visionneuse_path;
    	if($this->driver->is_allowed($this->id)){
			$this->selectDisplayClass();
		
			return $this->displayClass->fetchDisplay();
    	}else{
    		//le titre
    		$this->toDisplay["titre"] = $this->titre;
	    	$this->toDisplay["doc"] = false;
			//la description
			$this->toDisplay["desc"] = $this->desc;
			return $this->toDisplay;
    	}
    }
    
    function render(){
    	$this->selectDisplayClass();
    	$this->displayClass->render();
    }
    
    function exec($method){
    	$this->selectDisplayClass();
    	if(method_exists($this->displayClass, "exec") && $method){
    		$this->displayClass->exec($method);
    	}
    	return false;
    }
     
    
    function selectDisplayClass(){
    	global $visionneuse_path;

    	if (sizeof($this->mimetypeClass)>0){
    	//si une configuration existe 
    		if($this->mimetypeClass[$this->mimetype]){
    		//et le mimetype courant est défini
    			//on récupère la bonne classe
	 			require_once($visionneuse_path."/classes/mimetypes/".$this->mimetypeClass[$this->mimetype]."/".$this->mimetypeClass[$this->mimetype].".class.php");
				$this->displayClass = new $this->mimetypeClass[$this->mimetype]($this); 
    		}else $this->displayClass = false;
    	}
    	
    	//sinon celle attribué par défaut...
    	if ($this->displayClass === false){
    		//on instancie les choix par défaut
	    	$this->defaultClass= new defaultConf();
	    	//si le mimetype est défini
			if($this->defaultClass->defaultMimetype[$this->mimetype]){
				//on récupère la bonne classe
				require_once($visionneuse_path."/classes/mimetypes/".$this->defaultClass->defaultMimetype[$this->mimetype]."/".$this->defaultClass->defaultMimetype[$this->mimetype].".class.php");
				$this->displayClass = new $this->defaultClass->defaultMimetype[$this->mimetype]($this);
			//sinon
			}else{
				//on prend la classe principale...
				$this->displayClass = new affichage($this);
			}		
    	}
    }
}
?>