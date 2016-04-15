<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records.class.php,v 1.5 2015-04-03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/searcher/searcher_generic.class.php");

class searcher_records extends searcher_generic {
	public $typdocs;			// tableau des typdoc
	public $nb_explnum=0;		// nombre de documents numériques associés à la recherche...
	public $explnums=array();	// tableau contenant les documents numériques associés à la recherche
	
	
	public function __construct($user_query){
		parent::__construct($user_query);
		$this->object_table = "notices";
		$this->object_key = "notice_id";
		$this->object_index_key= "id_notice";
		$this->object_words_table = "notices_mots_global_index";
		$this->object_fields_table = "notices_fields_global_index";
	}

	protected function _get_search_type(){
		return "records";
	}

	protected function get_full_results_query(){
		return "select notice_id as id_notice from notices";
	}
	
	protected static function _get_typdoc_filter($on_notice=false){
		global $typdoc_query;
		global $statut_query;
		$return ="";
		if($on_notice){
			if($typdoc_query && $statut_query) {
				$return = " where typdoc = '".$typdoc_query."' and statut = '".$statut_query."'";
			}else if ($typdoc_query){
				$return = " join notices on id_notice = notice_id and typdoc = '".$typdoc_query."'";
			}else if ($statut_query){
				$return = " join notices on id_notice = notice_id and statut = '".$statut_query."'";
			}else{
				return "";
			}
		}else{
			if($typdoc_query && $statut_query) {
				$return = " join notices on id_notice = notice_id and typdoc = '".$typdoc_query."' and statut = '".$statut_query."'";
			}else if ($typdoc_query){
				$return = " join notices on id_notice = notice_id and typdoc = '".$typdoc_query."'";
			}else if ($statut_query){
				$return = " join notices on id_notice = notice_id and statut = '".$statut_query."'";
			}else{
				return "";
			}
		}
		return $return;
	}
	
	protected function _get_search_query(){
		$query = parent::_get_search_query();
		if($this->user_query == "*"){
			$query.= self::_get_typdoc_filter(true);
		}else {
			$query.= self::_get_typdoc_filter();
		}
		return $query;
	}	

	protected function _get_pert($with_explnum=false, $query=false){
		if($query){
			return $this->aq->get_pert($this->objects_ids,$this->field_restrict,false,$with_explnum,$query);
		}else{
			$this->table_tempo = $this->aq->get_pert($this->objects_ids,$this->field_restrict,false,$with_explnum,$query);
		}
	}
	
	protected function _get_sign($sorted=false){
		global $typdoc;
		$sign = parent::_get_sign($sorted);
		$sign.= md5("&typdoc=".$typdoc);
		return $sign;
	}
	
	public function get_typdocs(){
		if(!$this->typdocs){
			if(!$this->objects_ids){
				$this->get_result();
			}
			$this->typdocs = array();
			if($this->objects_ids != ""){
				$query = "select distinct typdoc from notices where ".$this->object_key." in (".$this->objects_ids.")";
				$res = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($res)){
					while ($row = pmb_mysql_fetch_object($res)){
						$this->typdocs[] = $row->typdoc;
					}
				}
			}
		}
		return $this->typdocs;
	}
	
	protected function _filter_results(){
		$this->_get_objects_ids();
	
		if($this->objects_ids!='') {
			$fr = new filter_results($this->objects_ids);
			$this->objects_ids = $fr->get_results();
		}
	}
	
	public function get_nb_explnums(){
		if(!$this->objects_ids){
			$this->get_result();
		}
		$this->nb_explnum = 0;
		if($this->objects_ids != ""){
			$query_noti = "select explnum_id from explnum where explnum_notice in (".$this->objects_ids.")";
			$query_issue = "select explnum_id from explnum join bulletins on explnum_bulletin!= 0 and explnum_bulletin = bulletin_id join notices on notice_id = num_notice and num_notice!=0 where notice_id in (".$this->objects_ids.")";
			$query = "select explnum_id from(".$query_noti ." union ".$query_issue.") as uni";
			$res = pmb_mysql_query($query);
			$this->nb_explnum =pmb_mysql_num_rows($res);
		}
		return $this->nb_explnum;
	}
	
	public function get_explnums($tri){
		$this->explnums = array();
		$this->get_result();
		//$table = $this->_get_pert();
		$this->_get_pert();
		//liste complete des résultats..;
		if($this->objects_ids != ""){
			$sort = new sort("notices","session");
			$query = $sort->appliquer_tri_from_tmp_table($tri,$this->table_tempo,"notice_id",0,0);
	
			$explnum_noti = "select explnum_id,".$sort->table_tri_tempo.".* from explnum join ".$sort->table_tri_tempo." on explnum_notice!=0 and explnum_notice = ".$sort->table_tri_tempo.".notice_id ";
			$rqt = "create temporary table explnum_list $explnum_noti";
			pmb_mysql_query($rqt);
			$explnum_issue = "select explnum_id,".$sort->table_tri_tempo.".* from explnum join bulletins on explnum_bulletin!=0 and bulletin_id = explnum_bulletin join ".$sort->table_tri_tempo." on num_notice != 0 and num_notice = ".$sort->table_tri_tempo.".notice_id ";
			$rqt = "insert ignore into explnum_list $explnum_issue";
			pmb_mysql_query($rqt);
			pmb_mysql_query("alter table explnum_list order by ".$sort->get_order_by($tri));
			$rqt = "select explnum_id from explnum_list order by ".$sort->get_order_by($tri);
			$res = pmb_mysql_query($rqt);
			//si get_order_by renvoit une valeur nulle, on ne s'occupe pas du tri.
			if (!$res) {
				$rqt = "select explnum_id from explnum_list";
				$res = pmb_mysql_query($rqt);
			}
			if ($res) {
				if(pmb_mysql_num_rows($res)){
					while($row = pmb_mysql_fetch_object($res)){
						$this->explnums[] = $row->explnum_id;
					}
				}
			}
		}
		return $this->explnums;
	}
	
	protected function _sort($start,$number){
		if($this->table_tempo != ""){
			$sort = new sort("notices","session");
			$query = $sort->appliquer_tri_from_tmp_table($this->tri,$this->table_tempo,$this->object_key,$start,$number);
			$res = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->$this->{object_key};
				}
			}
		}
	}

	public function get_full_query(){
		if($this->get_nb_results()){
			$query  = $this->_get_pert(false,true);
		}else{
			$query = "select ".$this->object_key." from notices where ".$this->object_key." = 0"; 
		}
		return $query;
	}
	
	
}