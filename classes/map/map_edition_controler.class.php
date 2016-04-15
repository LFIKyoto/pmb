<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_edition_controler.class.php,v 1.23 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_model.class.php");
require_once($class_path."/map/map_objects_controler.class.php");

/**
 * class map_edition_controler
 * 
 */
class map_edition_controler {

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
   *
   * @param string object_type Type d'objet lié à  l'emprise

   * @param int object_id Identifiant de l'objet lié à  l'emprise

   * @return void
   * @access public
   */
  public function __construct( $object_type, $object_id) {
		$this->editable=true;
  	
  		$this->type=$object_type;
  		$this->id=$object_id;
  		$objects = array();
  		switch($this->type){
  			case TYPE_RECORD :
  				$objects[]= array(
  					'layer' => "record",
  					'ids' => array($this->id)
  				);
  				break;
  			case AUT_TABLE_CATEG :
  				$objects[]= array(
  					'type' => $this->type,
  					'layer' => "authority",
  					'ids' => array($this->id)
  				);
  				break;
  		}
  		$this->model = new map_model($map_hold, $objects,0);
  		$this->model->set_mode("edition");
  	
  } // end of member function __construct
  
	
	public function get_json_informations(){
		global $pmb_url_base;
		global $dbh;
	
		$map_hold = $this->get_bounding_box();
		if(!$map_hold)return "";
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
		return "initialFit: [ ".$longs[0]." , ".$lats[0]." , ".$longs[1]." , ".$lats[1]."], layers : ".json_encode($this->model->get_json_informations(false, $pmb_url_base,$this->editable));
	}
	
	public function get_bounding_box(){
		return $this->model->get_bounding_box(1);
	}
	
	
	public function get_map() {
		global $dbh;
		global $pmb_map_base_layer_type;
		global $pmb_map_base_layer_params;
		global $pmb_map_size_notice_edition;
		
		$layer_params = json_decode($pmb_map_base_layer_params,true);
		$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$pmb_map_base_layer_type;
		if(count($layer_params)){
			if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
			if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
			if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
		}		
				
		$ids[]=$this->id;
		switch($this->type){
			case TYPE_RECORD :
				$objects[]= array(
					'layer' => "record",
					'ids' => $ids
				);
				break;	
  			case AUT_TABLE_CATEG :
				$objects[]= array(
					'type' => $this->type,
					'layer' => "authority",
					'ids' => $ids
				);
				break;	
		}
		$map_hold = null;
		
		$size=explode("*",$pmb_map_size_notice_edition); 
		if(count($size)!=2)$map_size="width:800px; height:480px;";
		$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
		
		$map = "
		<div class='row'>
			<div class='colonne60'>
				<div id='map_objet_".$this->type."_".$this->id."' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='mode:\"edition\", hiddenField:\"map_wkt\", ".$baselayer.",".$this->get_json_informations()."'></div>
			</div>
			<div class='colonne40'>
				<div id='map_manual_edition'>
				</div>		
			</div>	
			<div class='row'></div>					
		</div>";		
		return $map;
	}
	
	public function get_form() {
		global $dbh,$msg;
		$form_map="";
		
		switch($this->type){
			case TYPE_RECORD : 
				$form_map="
					<div class='row'>
						".$this->get_map()."
					</div>
				";				
				break;	
  			case AUT_TABLE_CATEG :
  				$form_map="
	  				<div class='row'>
	  					<label class='etiquette'>".$msg["categ_map_title"]."</label>
	  				</div>
	  				<div class='row'>
	  					".$this->get_map()."
	  				</div>
	  			";			
  				break;  			
		}		
		return $form_map;
	}
	
	public function save_form() {
		global $dbh;
		global $map_wkt;
	
		$this->delete();
  		// save des emprises:
  		if(count($map_wkt)){
  			for($i=0 ; $i<count($map_wkt) ; $i++){
  				$query = "insert into map_emprises set
				map_emprise_data= GeomFromText('".$map_wkt[$i]."'),
				map_emprise_type=".$this->type.",
				map_emprise_obj_num=".$this->id.",
				map_emprise_order = ".$i;
  				pmb_mysql_query($query,$dbh);
  				$id_emprise = pmb_mysql_insert_id($dbh);
  				$query_area = "insert into map_hold_areas set
  				id_obj=".$id_emprise.",
  				type_obj=".$this->type.",
  				area=Area(GeomFromText('".$map_wkt[$i]."')),
  				bbox_area=Area(envelope(GeomFromText('".$map_wkt[$i]."'))),
  				center=AsText(Centroid(envelope(GeomFromText('".$map_wkt[$i]."'))))";
  				pmb_mysql_query($query_area,$dbh);
  			}
  		}
	}
	
	public function delete() {
		global $dbh;
		$req = "select map_emprise_id from map_emprises where map_emprise_type=".$this->type." and map_emprise_obj_num=".$this->id;
		$result = pmb_mysql_query($req, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$req="DELETE FROM map_emprises where map_emprise_type=".$this->type." and map_emprise_obj_num=".$this->id;
			pmb_mysql_query($req,$dbh);
			//Partie map_hold_areas
			$req_areas="DELETE FROM map_hold_areas where type_obj=".$this->type." and id_obj=".$row->map_emprise_id;
			pmb_mysql_query($req_areas,$dbh);
		}

	}


} // end of map_edition_controler