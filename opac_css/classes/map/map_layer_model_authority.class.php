<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_layer_model_authority.class.php,v 1.7 2016-11-05 14:49:08 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_layer_model.class.php");


/**
 * class map_layer_model_authority
 * Classe représentant le modèle de données d'une autorité
 */
class map_layer_model_authority extends map_layer_model {

  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/

  /**
   * Type d'autorité
   * @access protected
   */
  protected $type;


  /**
   * Contructeur
   *
   * @param Array() ids Tableau des identifiants des objets

   * @param string type Type d'autorité

   * @return void
   * @access public
   */
  public function __construct($type, $ids ) {
  	$this->ids=$ids;
  	$this->type=$type;
  	$this->fetch_datas();
  } // end of member function __construct

  /**
   * 
   *
   * @param Array() ids Tableau des identifiants des objets

   * @return void
   * @access public
   */
  public function set_ids( $ids) {
  	$this->ids=$ids;
  } // end of member function set_ids

  /**
   * 
   *
   * @param string type Type d'autorité

   * @return void
   * @access public
   */
  public function set_type( $type) {
  	$this->type=$type;
  } // end of member function set_type

  /**
   * Cherche et instancie les emprises pour autorités correspondantes
   *
   * @return void
   * @access public
   */
  public function fetch_datas() {
  	global $dbh;  
  	global $opac_map_holds_authority_color;
  	
  	$this->holds=array();	
  	
  	$emprises = array();
  	$coordonnees =array();
  	$infos =array();
  	
  	$req="select map_emprises.map_emprise_id, map_emprises.map_emprise_obj_num, AsText(map_emprises.map_emprise_data) as map, map_hold_areas.bbox_area as bbox_area, map_hold_areas.center as center from map_emprises join map_hold_areas on map_emprises.map_emprise_id = map_hold_areas.id_obj where map_emprises.map_emprise_type='".$this->type."' and map_emprises.map_emprise_obj_num in (".implode(",", $this->ids).")";
  	$res=pmb_mysql_query($req, $dbh);
  	if (pmb_mysql_num_rows($res)) {
  	  	while($r=pmb_mysql_fetch_object($res)){
  		  	$geometric = strtolower(substr($r->map,0,strpos($r->map,"(")));
  		   	$hold_class = "map_hold_".$geometric;
  		  	if(class_exists($hold_class)){
  			  	$emprise =  new $hold_class("authority",$r->map_emprise_obj_num,$r->map);
  			  	$emprise->set_normalized_bbox_area($r->bbox_area);
  			  	$emprise->set_center($r->center);
  			  	$this->holds[$r->map_emprise_id] = $emprise;
  			}
  	  	}
  	}  
  	$this->color = $opac_map_holds_authority_color;	
  	  	
  	
  } // end of member function fetch_datas



 	protected function get_layer_model_type(){
  		return "authority";
  	}
  
 	protected function get_layer_model_name() {
  		return "authority";
  	}

} // end of map_layer_model_authority