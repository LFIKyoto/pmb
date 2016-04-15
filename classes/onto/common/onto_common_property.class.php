<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_property.class.php,v 1.9 2014-08-07 14:31:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_root.class.php';
require_once $class_path.'/onto/common/onto_common_class.class.php';


/**
 * class onto_common_property
 * 
 */
class onto_common_property extends onto_common_root {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access public
	 */
	public $domain;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_name;
	
	/**
	 * 
	 * @access public
	 */
	public $range;

	/**
	 *
	 * @access public
	 */
	public $pmb_datatype;
	
	/**
	 *
	 * @access public
	 */
	public $default_value;
	/**
	 * 
	 *
	 * @return void
	 * @access public
	 */

	/**
	 * Tableau des URI des propriétés inverses à la propriété représentée
	 * @access public
	 */
	public $inverse_of;
	
	public function __construct($uri,$ontology) {
		parent::__construct($uri,$ontology);
		$this->fetch_pmb_datatype();
		$this->fetch_default_value();
	} // end of member function __construct

	protected function fetch_label(){
		$this->label = $this->ontology->get_property_label($this->uri);
	}
	
	protected function fetch_pmb_datatype(){
		$this->pmb_datatype = $this->ontology->get_property_pmb_datatype($this->uri);
	}

	protected function fetch_default_value(){
		$this->default_value = $this->ontology->get_property_default_value($this->uri);
	}

	protected function fetch_flags(){
		$this->flags = $this->ontology->get_flags("",$this->uri);
	}
	
	public function set_domain($domain){
		$this->domain = $domain;	
	}
	
	public function set_range($range){
		$this->range = $range;
	}
	
	public function set_pmb_name($pmb_name){
		$this->pmb_name = $pmb_name;
	}
	
	public function set_inverse_of($inverse_of){
		$this->inverse_of = $inverse_of;
	}
} // end of onto_common_property
