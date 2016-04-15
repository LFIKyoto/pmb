<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origins.class.php,v 1.1 2011-12-20 13:12:44 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/origin.class.php");
require_once($include_path."/templates/origin.tpl.php");


class origins {
	var $type;
	
	public function __construct(){
		//pas grand chose à faire
	}
	
	//aiguilleur
	public function proceed(){
		global $sub;
		global $action;
		
		switch ($action){
			case "add" :
				$origin = new origin();
				print $origin->show_form();
				break;
			case "modif" :
				$origin = new origin($id);
				print $origin->show_form();
				break;
			case "delete" :
				$origin = new origin($id);
				$origin->delete();
				print $this->get_tab();
				break;
			case "update" :
				$origin = new origin($id);
				$origin->name = $origin_name;
				$origin->country = $origin_country;
				$origin->diffusible = ($origin_diffusible ? true : false);
				$origin->save();
				//pas de break, à la sauvegarde on réaffiche le tableau...
			default :
				print $this->get_tab();
				break;
		}
	}
	
	public function get_tab(){
		global $origin_tab_display;
		$list_origins = origin::get_list($this->type);
		$origin_tab_display = str_replace("!!type!!",$this->type,$origin_tab_display);
		$rows ="";
		if(count($list_origins)>0){
			for($i=0 ; $i<count($list_origins) ; $i++){
				$origin = new origin($list_origins[$i]);
				$rows.=$origin->show_tab_row();
			}
		}
		return str_replace("!!rows!!",$rows,$origin_tab_display);
	}
}

class origins_authorities extends origins{
	
	public function __construct(){
		$this->type = "authorities";
	}	
}