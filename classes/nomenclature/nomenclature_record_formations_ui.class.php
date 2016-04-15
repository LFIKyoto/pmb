<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations_ui.class.php,v 1.21 2015-04-10 09:26:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class nomenclature_record_formations
 * Représente les formations de la nomenclature d'une notice
 */

require_once($class_path."/nomenclature/nomenclature_record_formations.class.php");
require_once($class_path."/nomenclature/nomenclature_formations.class.php");
require_once($class_path."/nomenclature/nomenclature_datastore.class.php");

class nomenclature_record_formations_ui {

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
		$this->id=$id*1;
		$this->record_formations = new nomenclature_record_formations($id);
	} // end of member function __construct
	
	public function get_form(){
		
		$data=  encoding_normalize::json_encode($this->record_formations->get_data());				
		$div .= "
  		<script type='text/javascript' src='./javascript/instru_drag_n_drop.js'></script>
  		<div id='nomenclature_record_formations_".$this->record_formations->get_id()."' data-dojo-type='apps/nomenclature/nomenclature_record_formations_ui' data-dojo-props='num_record:".$this->record_formations->id.",record_formations:\"".addslashes($data)."\"'></div>";
  		return $div;
	} 
	
	public function save_form(){		
		global $record_formations;
		
		if(!$this->id)return; // pas id de notice
		$formations_list=array();
		if (is_array($record_formations)) {
			foreach($record_formations as $name){
				global $$name;
				$record_formation=$$name;
				$record_formation["num_record"]=$this->id;
				$formations_list[]=$record_formation;		
			}
		}
		$this->record_formations->save_form($formations_list);
	}
	
	public function delete(){		
		if(!$this->id)return; // pas id de notice
		// supression de la nomenclature de la notice 		
		$this->record_formations->delete();

	}
	
	public function get_isbd(){
		global $dbh,$msg;	
		
		$all_formations= new nomenclature_formations();
		$isbd="";
		// pour toutes les formations de la monenclature de la notice
		foreach($this->record_formations->get_data()  as $record_formation){	
			$titre="";
			$label="";
			$contenu="";
			$workshops_tpl="";
			foreach ($all_formations->get_data() as $formation){				
				if($formation['id']==$record_formation['num_formation']){
					foreach ($formation["types"] as $type){
						if($type['id']==$record_formation['num_type']){
							$label= " / ".$type['name'];
							break;
						}						
					}
					/*	
					// décompose par atelier => non voulu				
					foreach ($record_formation["workshops"] as $workshop){
						$workshop_tpl=$workshop["label"];
						$instruments_tpl="";
						foreach ($workshop["instruments"] as $instrument){
							if($instruments_tpl)$instruments_tpl.=" / ";
							else $instruments_tpl.=" : ";
							$instruments_tpl.= " ".$instrument["number"]." ".$instrument["code"];
							if($instrument["name"])$instruments_tpl.=" ( ". $instrument["name"]." ) ";
						}
						$workshop_tpl.= $instruments_tpl;
						if($workshop_tpl)$workshops_tpl.="<br />".$workshop_tpl;
					}*/
					
					// Ateliers: on liste tous les instruments sur une ligne
					foreach ($record_formation["workshops"] as $workshop){
						$instruments_tpl="";
						foreach ($workshop["instruments"] as $instrument){
							if($instruments_tpl)$instruments_tpl.=" / ";
							$instruments_tpl.= " ".$instrument["effective"]." ".$instrument["code"];
							if($instrument["name"])$instruments_tpl.=" ( ". $instrument["name"]." ) ";
						}
						$workshops_tpl.= $instruments_tpl;
					}
					if($workshops_tpl) $workshops_tpl="<br />".$msg["nomenclature_formation_isbd_workshops"].$workshops_tpl;
					
					$instruments_no_standard_tpl="";
					foreach ($record_formation["instruments"] as $instrument){
						if($instruments_no_standard_tpl)$instruments_no_standard_tpl.=" / ";
						$instruments_no_standard_tpl.= " ".$instrument["effective"]." ".$instrument["code"];
						if($instrument["name"])$instruments_no_standard_tpl.=" ( ". $instrument["name"]." ) ";
						if(is_array($instrument["other"]))	
						foreach ($instrument["other"] as $instrument_other){	
							if($instruments_no_standard_tpl)$instruments_no_standard_tpl.=" / ";
							$instruments_no_standard_tpl.= " ".$instrument_other["effective"]." ".$instrument_other["code"];
							if($instrument_other["name"])$instruments_no_standard_tpl.=" ( ". $instrument_other["name"]." ) ";
							
						}				
					}
					if($instruments_no_standard_tpl)$instruments_no_standard_tpl="<br />".$msg["nomenclature_formation_isbd_instruments_non_standards"].$instruments_no_standard_tpl;
					break;
				}
			}	
			$titre.="<b>".$msg["nomenclature_formation_isbd_formation"].$formation['name']."$label</b>";
			if($record_formation['label'])	$titre.= " / ".$record_formation['label'];	
			if($record_formation['abbreviation']){
				if(!$formation['nature'])
					$contenu.= $msg["nomenclature_formation_isbd_abbreviation"]. $record_formation['abbreviation'];
				else
					$contenu.= $msg["nomenclature_formation_isbd_abbreviation_voix"]. $record_formation['abbreviation'];
			}
			$contenu.=$workshops_tpl.$instruments_no_standard_tpl;
			
			$isbd.=gen_plus($record_formation['id'],$titre,$contenu);			
		}
  		return $isbd;
	}
	
	public static function get_index($id) {
		return nomenclature_record_formations::get_index($id);
	}
} // end of nomenclature_record_formations

