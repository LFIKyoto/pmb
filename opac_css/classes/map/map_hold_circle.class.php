<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_circle.class.php,v 1.1 2014-12-12 13:08:02 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_hold_polygon.class.php");
require_once($class_path."/map/map_coord.class.php");


/**
 * class map_hold_circle
 * 
 */
class map_hold_circle extends map_hold_polygon {

  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/

  /**
   * Centre du cercle
   * @access protected
   */
  protected $center;

  /**
   * Rayon du cercle
   * @access protected
   */
  protected $radius;

  /**
   * Nombre de points pour tracer le polygone approchant
   * @access protected
   */
  protected $nb_points;


  /**
   * 
   *
   * @param map_coord coord Coordonnées du centre

   * @return void
   * @access public
   */
  public function set_center( $coord) {
  } // end of member function set_center

  /**
   * 
   *
   * @param int nb_points Nombre de points pour le calcul du polygone approchant

   * @return void
   * @access public
   */
  public function set_nb_points( $nb_points) {
  } // end of member function set_nb_points

  /**
   * Retourne de nombre de points utilisés pour le polygone approchant
   *
   * @return int
   * @access public
   */
  public function get_nb_points() {
  } // end of member function get_nb_points

  /**
   * Retourne la classe représentant les coordonnées du centre du cercle
   *
   * @return map_coord
   * @access public
   */
  public function get_center() {
  } // end of member function get_center

  /**
   * 
   *
   * @param float radius Rayon du cercle

   * @return void
   * @access public
   */
  public function set_radius( $radius) {
  } // end of member function set_radius

  /**
   * Retourne le rayon du cercle
   *
   * @return float
   * @access public
   */
  public function get_radius() {
  } // end of member function get_radius

  /**
   * Constructeur
   *
   * @param map_coord center Centre du cercle

   * @param float radius Rayon du cercle

   * @param int nb_points Nombre de points pour générer le polygone approchant

   * @return void
   * @access public
   */
  public function __construct( $center,  $radius,  $nb_points) {
  } // end of member function __construct

  /**
   * 
   *
   * @return string
   * @access public
   */
  public function get_hold_type() {
  } // end of member function get_hold_type


  /**
   * Méthode qui calcule les points du polygone approchant
   *
   * @return void
   * @access protected
   */
  protected function fill_coords() {
  } // end of member function fill_coords




} // end of map_hold_circle