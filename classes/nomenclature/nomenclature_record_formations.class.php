<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations.class.php,v 1.16 2015-04-10 09:26:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/nomenclature/nomenclature_record_formation.class.php");

/**
 * class nomenclature_record_formations
 * Représente les formations de la nomenclature d'une notice
 */
class nomenclature_record_formations{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom du type
	 * @access protected
	 */

	public $record_formations;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = $id*1;
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		global $dbh;
		$this->record_formations = array();
		if($this->id){
			$query = "select id_notice_nomenclature from nomenclature_notices_nomenclatures where notice_nomenclature_num_notice = ".$this->id." order by notice_nomenclature_order, notice_nomenclature_label";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->add_record_formation( new nomenclature_record_formation($row->id_notice_nomenclature));	
				}
			}
		}
	}
	
	public function add_record_formation($record_formation ) {		
		$this->record_formations[] = $record_formation;	
	}
	
	public function get_data() {	
		$data=array();
		foreach($this->record_formations  as $formation){
			$data[]=$formation->get_data();
		}
		return($data);
	}
	
	public function save_form($formations_list) {		
		$this->delete();
		
		if(!is_array($formations_list)) return;
		foreach($formations_list as $record_formation){			
			$formation=new nomenclature_record_formation($record_formation['num_formation']);
			$formation->save_form($record_formation);
		}
	}

	public function delete(){
		// supression des formations de la notice
		foreach($this->record_formations  as $formation){
			$formation->delete();
		}
		$this->record_formations = array();
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public static function get_index($id) {
		global $dbh;
		
		$mots="";
		$req="
		select formation_name, notice_nomenclature_label, notice_nomenclature_notes,type_name 
			from nomenclature_notices_nomenclatures, nomenclature_formations, nomenclature_types
			where id_formation=notice_nomenclature_num_formation and id_type=notice_nomenclature_num_type and notice_nomenclature_num_notice='".$id."'
		union
		select formation_name, notice_nomenclature_label, notice_nomenclature_notes, '' as type_name
			from nomenclature_notices_nomenclatures, nomenclature_formations
			where id_formation=notice_nomenclature_num_formation and notice_nomenclature_num_type=0 and notice_nomenclature_num_notice='".$id."'
		";
		
		$result = pmb_mysql_query($req, $dbh);
		if($result){
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$mots.=$row->formation_name." ".$row->notice_nomenclature_label." ".$row->notice_nomenclature_notes." ".$row->type_name." ";
				}
			}	
		}
		return $mots;
	}	
} // end of nomenclature_record_formations
