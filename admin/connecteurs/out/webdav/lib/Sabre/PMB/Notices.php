<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Notices.php,v 1.5 2013-04-25 16:02:17 mbertin Exp $
namespace Sabre\PMB;

class Notices extends Collection {
	private $notices;
	public $config;

	function __construct($notices,$config) {
		
		$this->notices = $notices;
		$this->config = $config;
		$this->type = "notices";
	}
	
	function getChildren() {
		$children = array();
		for($i=0 ; $i<count($this->notices) ; $i++){
			$children[] = $this->getChild("(N".$this->notices[$i].")");
		}
		return $children;
	}

	function getName() {
		global $charset;
		if($charset != "utf-8"){
			return utf8_encode("[Notices]");
		}else{
			return "[Notices]";
		}
	}
}