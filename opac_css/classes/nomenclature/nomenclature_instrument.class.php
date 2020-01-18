<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
//

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class nomenclature_instrument{
    
    /** Aggregations: */
    
    /** Compositions: */
    
    /*** Attributes: ***/
    
    /**
     * Nom de l'instrument
     * @access protected
     */
    protected $name;
    
    /**
     * Abréviation de l'instrument
     * @access protected
     */
    protected $code;
    
    /**
     * Effectif de l'instrument
     * @access protected
     */
    protected $effective = 1;
    
    /**
     * Booléen pour savoir si l'instrument est standard
     * @access protected
     */
    protected $standard = true;
    
    /**
     * Tableau des instruments annexes à l'instrument.
     * ex : Un flutiste qui joue aussi du piccolo et du basson 1/Pic/Bn
     * @access protected
     */
    protected $others_instruments = array();
    
    /**
     * Ordre de l'instrument sur le pupitre
     * @access protected
     */
    protected $order = 1;
    
    /**
     * Pupitre auquel est rattaché l'instrument
     * @access protected
     */
    protected $musicstand;
    
    /**
     * Booléen qui indique si l'instrument est valide
     * @access protected
     */
    protected $valid = false;
    
    /**
     * Numéro de partie
     * @access protected
     */
    protected $part;
    
    /**
     * Nomenclature de l'instrument abrégée
     * @access protected
     */
    protected $abbreviation;
    /**
     * Id de l'instrument
     * @access protected
     */
    protected $id=0;
    /**
     * Constructeur
     *
     * @param int id Id de l'instrument
     
     * @param string code Abréviation de l'instrument
     
     * @param string name Nom de l'instrument
     
     * @return void
     * @access public
     */
    public function __construct($id, $code = "", $name = "") {
        if ($id) {
            $this->id = (int) $id;
            $this->fetch_datas();
        } else {
            $this->set_code($code);
            $this->set_name($name);
        }
    } // end of member function __construct
    
    public static function get_instrument_name_from_id($id) {
        $instrument_name = '';
        $id = intval($id);
        $query = "select instrument_name from nomenclature_instruments where id_instrument=".$id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $instrument_name = $row->instrument_name;
        }
        return $instrument_name;
    }
}