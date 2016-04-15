<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_objects_controler.class.php,v 1.4 2015-03-20 16:13:57 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_model.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/analyse_query.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_objects_controler {

	/** Aggregations: */
	
	/** Compositions: */
	
	/*** Attributes: ***/
	
	/**
	 *
	 * @access protected
	 */
	protected $model;
	
	/**
	 *
	 * @access protected
	 */
	protected $mode;


	/**
	 * Constructeur.
	 *
	 * Il joue à  aller chercher les infos utiles pour le modèle (listes d'ids des
	 * objets liés,...)
	 *
	 * @param map_hold map_hold Emprise courante de la carte
	
	 * @param int mode Mode de récupération des éléments
	
	 * @return void
	 * @access public
	 */
	public function __construct($type,$ids) {
		global $opac_map_max_holds;
		$this->editable = false;
		$this->ajax = false;
		
		$this->ids=$ids;
		$this->type=$type;
  		$this->objects = array();
	
  		switch($this->type){
  			case TYPE_RECORD :
  				$items = array(
	  				'layer' => "record",
	  				'ids' =>  $this->ids
  				);
  				break;  	
  			case AUT_TABLE_AUTHORS :
  				$items = array(
	  				'layer' => "authority",
	  				'ids' => $this->ids
  				);  				
  				break;  			
  		}
	   	$this->objects[] = $items;
	   	$this->fetch_datas();
	   	$this->model = new map_model(null, $this->objects,$pmb_map_max_holds);
	   	$this->model->set_mode("visualisation");
  	} // end of member function __construct

  		
  	public function get_data() {
  		return $this->map;
  	}
  	public function fetch_datas() {
  		global $dbh,$msg;
  	
  		switch($this->type){
  			case TYPE_RECORD :
  				break;
  			case AUT_TABLE_AUTHORS :  	
  				break;
  		}  		
  	}
  	
  	public function get_json_informations(){
  		global $opac_url_base;
  		global $dbh;
  	
  		$map_hold = $this->get_bounding_box();
  		if($map_hold){
	  		$coords = $map_hold->get_coords();
	  		if(!count($coords))return "";
	  		$lats = $longs = array();
	  		for($i=0 ; $i<count($coords) ; $i++){
	  			$lats[] = $coords[$i]->get_decimal_lat();
	  			$longs[] = $coords[$i]->get_decimal_long();
	  		}
	  		$lats = array_unique($lats);
	  		$longs = array_unique($longs);
	  		sort($lats);
	  		sort($longs);
	  		return "mode:\"visualization\", initialFit: [ ".$longs[0]." , ".$lats[0]." , ".$longs[1]." , ".$lats[1]."], layers : ".json_encode($this->model->get_json_informations(false, $opac_url_base,$this->editable));
  		}else{
  			return "";
  		}
  	}
  	
  	public function get_bounding_box(){
  		return $this->model->get_bounding_box();
  	}
  	
  	
  	public function get_map() {
  		global $opac_map_base_layer_type;
  		global $opac_map_base_layer_params;
  		global $opac_map_size_notice_view;
	  	
  		$json_informations = $this->get_json_informations();
  		$map = "";
  		if($json_informations){
  			$id=$this->ids[0];
  			$map_hold = null;
	  		$layer_params = json_decode($opac_map_base_layer_params,true);
	  		$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
	  		if(count($layer_params)){
	   			if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
	  			if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
	  			if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
	  		}
	  		
	  		$size=explode("*",$opac_map_size_notice_view);
	  		if(count($size)!=2)$map_size="width:800px; height:480px;";
	  		$map_size= "width:".$size[0]."px; height:".$size[1]."px;";

	  		$map = "
	  		<div id='map_objet_".$this->type."_".$id."' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='mode:\"visualization\",".$baselayer.", ".$this->get_json_informations()."'></div>";
  		}
  		return $map;
  	}
  	

} // end of map_objects_controler