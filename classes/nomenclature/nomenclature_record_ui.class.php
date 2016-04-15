<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_ui.class.php,v 1.7 2015-04-10 09:26:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class nomenclature_record_ui
 * Représente la nomenclature d'une notice
 */

require_once($class_path."/nomenclature/nomenclature_record_formations_ui.class.php");
require_once($class_path."/nomenclature/nomenclature_record_child_ui.class.php");

require_once($class_path."/nomenclature/nomenclature_datastore.class.php");

class nomenclature_record_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom du type
	 * @access protected
	 */

	public $nomenclature_record;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		global $dbh;
		global $pmb_nomenclature_record_children_link;
		
		$this->id=$id*1;	
		$this->id_parent=0;
		
		$query = "select * from notices_relations where relation_type='".$pmb_nomenclature_record_children_link."' and num_notice = ".$this->id;
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			if($row = pmb_mysql_fetch_object($result)){
				$this->id_parent=$row->linked_notice;
				$this->nomenclature_record = new nomenclature_record_child_ui($this->id);
			}
		}				
		if(!$this->id_parent){
			$this->nomenclature_record = new nomenclature_record_formations_ui($this->id);
		}
	} // end of member function __construct
	
	public function create_record_child($id_parent){
		$this->nomenclature_record = new nomenclature_record_child_ui();
		return $this->nomenclature_record->create_record_child($id_parent);
	}
		
	public function get_form(){

		$args = "num_record:".$this->id."";
		if($this->id_parent){
			$args.=",child_detail :\"".addslashes(encoding_normalize::json_encode($this->nomenclature_record->record_child->get_data()))."\"";
		}else{
			$args.=",record_formations :\"".addslashes(encoding_normalize::json_encode($this->nomenclature_record->record_formations->get_data()))."\"";
		}
		$div = nomenclature_datastore::get_form();
		return $div."<div data-dojo-type='apps/nomenclature/nomenclature_record_ui' data-dojo-props='".$args."'></div>";
	} 
	
	public function save_form(){	
		
		if(!$this->id)return; // pas id de notice	
		$this->nomenclature_record->save_form();				
	}
	
	public function delete(){		
		if(!$this->id)return; // pas id de notice	
		$this->nomenclature_record->delete();

	}
	
	public function get_isbd(){
		return $this->nomenclature_record->get_isbd();
	}
	
	public static function get_index($id) {
		global $dbh;
		global $pmb_nomenclature_record_children_link;
		
		$query = "select linked_notice from notices_relations where relation_type='".$pmb_nomenclature_record_children_link."' and num_notice = ".$id;
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			return nomenclature_record_child_ui::get_index($id);
		}else{
			return nomenclature_record_formations_ui::get_index($id);
		}	
	}
} // end of nomenclature_record_formations

