<?php
// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_multipolygon.class.php,v 1.2 2016-11-05 14:49:08 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_hold.class.php");


/**
 * class map_hold_multipolygon
 * 
 */
class map_hold_multipolygon extends map_hold {

	/** Aggregations: */
	
	/** Compositions: */
	
	/*** Attributes: ***/
	
	
	/**
	 *
	 *
	 * @return string
	 * @access public
	 */
	public function get_hold_type() {
		 
		return "MULTIPOLYGON";
		 
	} // end of member function get_hold_type
	
	protected function build_coords(){
  		$coords_string = substr($this->wkt,strpos($this->wkt,"(")+2,-2);
		$coords_multiple_string = substr($coords_string,strpos($coords_string,"(")+1,-1);
  		$polygons = explode("),(",$coords_multiple_string);
  		foreach ($polygons as $polygon) {
  			$coords = explode(",",$polygon);
  			$coords_polygon =array();
  			for($i=0 ; $i< count($coords) ; $i++){
  				$infos =array();
  				$coord = $coords[$i];
  				$infos = explode(" ",$coord);
  				//on ne met pas la derni�re coordonn�e
  				if($i<(count($coords)-1)){
  					$coords_polygon[] = new map_coord($infos[0],$infos[1]);
  				}
  			}
  			$this->coords[] = $coords_polygon;
  		}
  		$this->coords_uptodate = true;
  	}
  	
  	protected function build_wkt(){
  		$this->wkt = $this->get_hold_type()."((";
  		$tmp_wkt = "";
		foreach ($this->coords as $polygon) {
			if ($tmp_wkt == "") $tmp_wkt = "(";
			else $tmp_wkt .= ",("; 
			foreach($polygon as $coord){
				$tmp_wkt.= $coord->get_decimal_lat()." ".$coord->get_decimal_long().",";
			}
			$tmp_wkt.= $polygon[0]->get_decimal_lat()." ".$polygon[0]->get_decimal_long();
			$tmp_wkt .= ")";
		}
		$this->wkt .= $tmp_wkt;
		$this->wkt .= "))";
		$this->wkt_uptodate = true;
  	}

} // end of map_hold_polygon