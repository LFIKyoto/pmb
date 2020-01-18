<?php
// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_layer_model.class.php,v 1.5.10.1 2019-09-20 14:03:45 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_hold_polygon.class.php");
require_once($class_path."/map/map_hold_point.class.php");
require_once($class_path."/map/map_hold_multipolygon.class.php");
require_once($class_path."/map/map_hold_multilinestring.class.php");
require_once($class_path."/map/map_hold_linestring.class.php");
require_once($class_path."/map/map_coord.class.php");

class map_layer_model {

    /**
     * Tableau des emprises, l'identifiant de l'objet est la cl�
     * 
     * @var array
     * @access protected
     */
    protected $holds;

    /**
     * Tableau des identifiants des objets contenant des emprises
     * 
     * @var array
     * @access protected
     */
    protected $ids;

    /**
     * Instance de polygone repr�sentant la bounding box
     * 
     * @var map_hold_polygon
     * @access protected
     */
    protected $bounding_box;
  
    /**
     * Couleur du layer
     * 
     * @var string
     * @access protected
     */
    protected $color="";


    /**
     * Constructeur
     *
     * @param array $ids Tableau des identifiants des objets contenant des emprises
     * @return void
     * @access public
     */
    public function __construct($ids) {
        $this->ids = $ids;
        $this->fetch_datas();
    }

    /**
     * Retourne l'emprise normalis� minimal pour afficher toutes les emprises
     *
     * @return map_hold_polygon
     * @access public
     */
    public function get_bounding_box() {
        if(!$this->bounding_box){
          	//on teste la mani�re forte !
          	$collection = $global_collection = "";
          	$i=0;
          	foreach ($this->holds as $hold){ 
          		if($collection) $collection.=",";
          		$collection.=$hold->get_wkt();
          		$i++;
          		if($i == 500){
          			$query = "select astext(envelope(geomfromtext('geometrycollection(".$collection.")'))) as bounding_box";
          			$result = pmb_mysql_query($query) or die(pmb_mysql_error());
          			if(pmb_mysql_num_rows($result)){
          				if($global_collection) $global_collection.=",";
          				$global_collection.= pmb_mysql_result($result,0,0);
          			}
          			$i=0;
          			$collection ="";
          		}
          	}
          	
          	if($collection){
          		$query = "select astext(envelope(geomfromtext('geometrycollection(".$collection.")'))) as bounding_box";
          		$result = pmb_mysql_query($query) or die(pmb_mysql_error());
          		if(pmb_mysql_num_rows($result)){
          			if($global_collection) $global_collection.=",";
          			$global_collection.= pmb_mysql_result($result,0,0);
          		}
          	}
          	
          	if($global_collection){
        	  	$query = "select astext(envelope(geomfromtext('geometrycollection(".$global_collection.")'))) as bounding_box";
        	  	$result = pmb_mysql_query($query) or die(pmb_mysql_error());
        	  	if(pmb_mysql_num_rows($result)){
        	  		$this->bounding_box = new map_hold_polygon("bounding", 0,pmb_mysql_result($result,0,0));
        	  	}
          	}
          	
          	if(!$this->bounding_box){
          		return false;
          	}
        }
        return $this->bounding_box;
    }


    /**
     * Appelle toutes les emprises normalis�e des emprises courantes pour calculer
     * l'emprise minimum n�cessaire pour afficher toutes les emprises associ�es aux
     * objets courants
     *
     * @return void
     * @access protected
     */
    protected function calc_bounding_box() {
    }

	public function get_holds() {
  		return $this->holds;
  	}

	public function get_informations() {
 		return array(
			'type' => $this->get_layer_model_type(),
 			'name' => $this->get_layer_model_name(),
 			'color' => $this->color,
 			'field_id'=> $this->get_layer_model_type()."_hidden_field"
 		);
	}
	
	public function have_results() {
		if (!empty($this->holds)) {
			return true;
		}
		return false;
	}
}