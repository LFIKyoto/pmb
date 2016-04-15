<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formation.class.php,v 1.20 2015-04-03 11:16:23 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/nomenclature/nomenclature_workshop.class.php");
require_once($class_path."/nomenclature/nomenclature_formation.class.php");

/**
 * class nomenclature_record_formation
 * Représente une formation de la nomenclature d'une notice
 */
class nomenclature_record_formation{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	public $num_record=0;
	public $num_formation=0;
	public $num_type=0;
	public $label="";
	public $abbreviation="";
	public $notes="";
	public $order=0;
	public $nature=0;
	public $workshops=array();
	public $instruments =array();
	public $instruments_other =array();
	public $instruments_data =array();
	
	/**
	 * Constructeur
	 *
	 * @param int id de nomenclature_notices_nomenclatures: id_notice_nomenclature
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {

		$this->id = $id*1;			

		$this->fetch_datas();
	} // end of member function __construct

	public function fetch_datas(){
		global $dbh;
		
		$this->num_record=0;		
		$this->num_formation=0;		
		$this->num_type=0;	
		$this->label="";
		$this->abbreviation="";
		$this->notes="";				
		$this->order=0;										
		$this->workshops=array();			
		$this->instruments =array();// non standard
		$this->instruments_other =array();// non standard
		$this->instruments_data=array();
		$this->nature=0;
		if($this->id){
			$query = "select * from nomenclature_notices_nomenclatures where id_notice_nomenclature = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->num_record=$row->notice_nomenclature_num_notice;		
					$this->num_formation=$row->notice_nomenclature_num_formation;		
					$this->num_type=$row->notice_nomenclature_num_type;	
					$this->label=$row->notice_nomenclature_label;
					$this->abbreviation=$row->notice_nomenclature_abbreviation;
					$this->notes=$row->notice_nomenclature_notes;				
					$this->order=$row->notice_nomenclature_order;	
					
					$formation=new nomenclature_formation($row->notice_nomenclature_num_formation);		
					$this->nature=$formation->get_nature();
					if(!$this->nature){
						// formation instruments
						// Ateliers de la nomenclature de la notice
						$query = "select id_workshop from nomenclature_workshops where workshop_num_nomenclature = ".$this->id." order by workshop_order, workshop_label";
						$result = pmb_mysql_query($query,$dbh);
						if($result){
							if(pmb_mysql_num_rows($result)){
								while($row = pmb_mysql_fetch_object($result)){	
									$this->add_workshop( new nomenclature_workshop($row->id_workshop));				
								}	
							}
						}
						// Instruments non standard de la nomenclature de la notice
						$query = "select * from nomenclature_exotic_instruments where exotic_instrument_num_nomenclature = ".$this->id." order by exotic_instrument_order";
						$result = pmb_mysql_query($query,$dbh);
						if($result){
							if(pmb_mysql_num_rows($result)){
								while($row = pmb_mysql_fetch_object($result)){	
									$id_exotic_instrument=$row->id_exotic_instrument;	
									$this->add_instrument($id_exotic_instrument,new nomenclature_instrument($row->exotic_instrument_num_instrument));
									$this->instruments_data[$id_exotic_instrument]['effective']=$row->exotic_instrument_number;
									$this->instruments_data[$id_exotic_instrument]['order']=$row->exotic_instrument_order;	
									$this->instruments_data[$id_exotic_instrument]['id']=$row->exotic_instrument_num_instrument;
									$this->instruments_data[$id_exotic_instrument]['id_exotic_instrument']=$id_exotic_instrument;
									
									$this->instruments_data[$id_exotic_instrument]['other']=array();
									$query = "select * from nomenclature_exotic_other_instruments where exotic_other_instrument_num_exotic_instrument = ".$id_exotic_instrument." order by exotic_other_instrument_order";
									$result_other = pmb_mysql_query($query,$dbh);
									if($result_other){
										if(pmb_mysql_num_rows($result_other)){
											$count_other=0;
											while($row = pmb_mysql_fetch_object($result_other)){
												$id_exotic_other_instrument=$row->id_exotic_other_instrument;
												$this->add_other_instrument($id_exotic_instrument,new nomenclature_instrument($row->exotic_other_instrument_num_instrument));
												
												$this->instruments_data[$id_exotic_instrument]['other'][$count_other]['id']=$row->exotic_other_instrument_num_instrument;
												$this->instruments_data[$id_exotic_instrument]['other'][$count_other]['order']=$row->exotic_other_instrument_order;
												$count_other++;
											}
											
										}
									}
								}		
							}
						}
					// fin formation instrument
					}else{
						// formation voix
						
					}// fin formation voix
					
				}		
			}
		}
	}
	
	public function get_formation_nature($formation) {
		return($formation->nature);
	}	
	
	public function add_workshop( $workshop ) {
		$this->workshops[] = $workshop;
	}
	
	public function add_instrument($id_exotic_instrument, $instrument) {
		$this->instruments[$id_exotic_instrument]= $instrument;
	}
	
	public function add_other_instrument($id_exotic_instrument, $instrument) {
		$this->instruments_other[$id_exotic_instrument][] = $instrument;
	}
	
	public function get_data(){
		
		// Ateliers de la nomenclature de la notice
		$data_workshop=array();
		foreach($this->workshops as $workshop){
			$data_workshop[]=$workshop->get_data();
		}
		// Instruments non standards de la nomenclature de la notice
		$data_intruments=array();
		foreach ($this->instruments as $key => $instrument)	{
			$data=$instrument->get_data();
			$data['effective']=$this->instruments_data[$key]['effective'];
			$data['order']=$this->instruments_data[$key]['order'];
			if($this->instruments_other[$key]){
				foreach ($this->instruments_other[$key] as $key_other => $instrument_other)	{
					$data_other=$instrument_other->get_data();
					$data_other['order']=$this->instruments_data[$key]['other'][$key_other]['order'];
					$data['other'][]=$data_other;
				}
			}			
			$data_intruments[]=$data;
		}
		
		// data de la nomenclature de la notice
		return (
			array(
				"id" => $this->id,
				"num_record" => $this->num_record,
				"num_formation" => $this->num_formation,
				"num_type" => $this->num_type,
				"nature" => $this->nature,
				"label" => $this->label,
				"abbreviation" => $this->abbreviation,
				"notes" => $this->notes,
				"workshops" => $data_workshop,
				"instruments" => $data_intruments,
				"order" => $this->order
			)
		);
	}
	
	public function save_form($data) {	
		$this->num_record=$data["num_record"]*1;		
		$this->num_formation=$data["num_formation"]*1;		
		$this->num_type=$data["num_type"]*1;	
		$this->label=stripslashes($data["label"]);
		$this->abbreviation=stripslashes($data["abbr"]);
		$this->notes=stripslashes($data["notes"]);				
		$this->order=$data["order"]*1;	
		
		$this->workshops=array();			
		$this->instruments =array();// non standard
		$this->instruments_data=array();
		
		// instruments non standarts de la nomenclature de la notice
		if(is_array($data["instruments"]))
		foreach($data["instruments"] as $formation_instrument){
			$id_instrument=$formation_instrument["id"]*1;
			$this->instruments_data[$id_instrument]["id"]=$id_instrument;
			$this->instruments_data[$id_instrument]["effective"]=$formation_instrument["effective"]*1;
			$this->instruments_data[$id_instrument]["order"]=$formation_instrument["order"]*1;
		
			$this->instruments_data[$id_instrument]["other"]=array();
			$other_order=1;
			if(is_array($formation_instrument["other"]))
			foreach($formation_instrument["other"] as $instrument_other){
				$id_instrument_other=$instrument_other["id"]*1;
				$this->instruments_data[$id_instrument]["other"][$id_instrument_other]["id"]=$id_instrument_other;
				$this->instruments_data[$id_instrument]["other"][$id_instrument_other]["order"]=$instrument_other["order"]*1;	
			}
			
		}
		
		$this->save();	
		
		// Ateliers de la nomenclature de la notice
		if(is_array($data["workshops"]))
		foreach($data["workshops"] as $formation_workshop){
			$workshop = new nomenclature_workshop($formation_workshop["id"]);
			$formation_workshop["num_nomenclature"]=$this->id;
			$workshop->save_form($formation_workshop);
		}		
	}
	
	public function save(){
		global $dbh;
				
		$fields="
			notice_nomenclature_num_notice='".$this->num_record."',
			notice_nomenclature_num_formation='".$this->num_formation."',
			notice_nomenclature_num_type='".$this->num_type."',
			notice_nomenclature_label='". addslashes($this->label) ."',
			notice_nomenclature_abbreviation='". addslashes($this->abbreviation) ."',
			notice_nomenclature_notes='". addslashes($this->notes) ."',
			notice_nomenclature_order='".$this->order."'
		";		

		$req="INSERT INTO nomenclature_notices_nomenclatures SET $fields ";
		
		pmb_mysql_query($req, $dbh);
		$this->id=pmb_mysql_insert_id();
		
		foreach($this->instruments_data as $formation_instrument){			
			$req="INSERT INTO nomenclature_exotic_instruments SET 
			exotic_instrument_num_instrument=".$formation_instrument["id"].",
			exotic_instrument_number=".$formation_instrument["effective"].",
			exotic_instrument_order=".$formation_instrument["order"].",			
			exotic_instrument_num_nomenclature=".$this->id;
			pmb_mysql_query($req, $dbh);
			$id_exotic_instrument=pmb_mysql_insert_id();		
			
			if(is_array($formation_instrument["other"]) && $id_exotic_instrument)
			foreach($formation_instrument["other"] as $instrument_other){
				$req="INSERT INTO nomenclature_exotic_other_instruments SET 
				exotic_other_instrument_num_instrument=".$instrument_other["id"].",
				exotic_other_instrument_order=".$instrument_other["order"].",			
				exotic_other_instrument_num_exotic_instrument=".$id_exotic_instrument;
				pmb_mysql_query($req, $dbh);	
			}
		}
		
		$this->fetch_datas();
	}
	
	public function delete(){
		global $dbh;
		
		foreach($this->workshops as $workshop){
			$workshop->delete();
		}
		
		foreach($this->instruments_data as $id_exotic_instrument => $formation_instrument){
			foreach($formation_instrument["other"]  as $instrument_other){
				$req = "DELETE FROM nomenclature_exotic_other_instruments WHERE exotic_other_instrument_num_exotic_instrument=".$id_exotic_instrument;
			}
			pmb_mysql_query($req, $dbh);
		}
		
		$req = "DELETE FROM nomenclature_exotic_instruments WHERE exotic_instrument_num_nomenclature=".$this->id;
		pmb_mysql_query($req, $dbh);
		
		$req="DELETE from nomenclature_notices_nomenclatures WHERE id_notice_nomenclature = ".$this->id;
		pmb_mysql_query($req, $dbh);
		
		$this->id=0;
		$this->fetch_datas();
	}
	
	public function get_id(){
		return $this->id;
	}

} // end of nomenclature_record_formation
