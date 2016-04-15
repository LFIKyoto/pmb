<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_search_controler.class.php,v 1.6 2015-04-03 11:16:28 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_model.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($include_path."/rec_history.inc.php");

/**
 * class map_search_controler
 * Controlleur de notre super dev
 */
class map_search_controler {

	/** Aggregations: */
	
	/** Compositions: */
	
	/*** Attributes: ***/
	
	/**
	 *
	 * @access protected
	 */
	protected $model;
	
	/**
	 *
	 * @access protected
	 */
	protected $mode;


	/**
	 * Constructeur.
	 *
	 * Il joue à  aller chercher les infos utiles pour le modèle (listes d'ids des
	 * objets liés,...)
	 *
	 * @param map_hold map_hold Emprise courante de la carte
	
	 * @param int mode Mode de récupération des éléments
	
	 * @return void
	 * @access public
	 */
	public function __construct($map_hold, $mode, $max_hold, $force_ajax=false, $cluster="true") {
		$this->editable = false;
		$this->ajax = $force_ajax;
  		$this->set_mode($mode);

  		$this->objects = array();
  		
  		$this->objects = $this->get_objects();
  		if(count($this->objects)){
  			$this->model = new map_model($map_hold, $this->objects,$max_hold,$cluster);
  			$this->model->set_mode("search");
  		}else{
  			//la recherche n'est pas encore enregistré...
  			$this->ajax = true;
  		}
  		
  	} // end of member function __construct

  	/**
  	 * Modifie le mode
  	 *
  	 * @return void
  	 * @access public
  	 */
  	public function set_mode($mode) {
  		 
  		$this->mode = $mode;
  		
  	} // end of member function get_mode
  	
  	/**
  	 * Retourne le mode
  	 *
  	 * @return string
  	 * @access public
  	 */
  	public function get_mode() {
  	
  		return $this->mode;
  		
  	} // end of member function get_mode
  	
  	/**
  	 *
  	 *
  	 * @return void
  	 * @access public
  	 */
  	public function get_objects() {
  		global $dbh;
  		global $search;
  		global $opac_stemming_active;  	
  		global $user_query; 	
  		
  		$objects = array();
  		
  		$current_search = $this->get_mode();
  		
  		//	print $_SESSION["tab_result"];
  		$notices_ids=explode(",",$_SESSION["tab_result"]);
  		if(!count($notices_ids))return $objects;
  		$objects[] = array(
  			'layer' => "record",
  			'ids' => $notices_ids
  		);
  		
  		$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  		$result = pmb_mysql_query($requete,$dbh);
  		if(pmb_mysql_num_rows($result)){
  			$categ_ids = array();
  			while ($row = pmb_mysql_fetch_object($result)) {
  				$categ_ids[] = $row->map_emprise_obj_num;
  			}
  			$objects[] = array(
  				'layer' => "authority",
  				'type' => 2,
  				'ids' => $categ_ids
  			);
  		
  		}
  		return $objects;
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		print $current_search." $user_query";
  		//printr($_SESSION);
  		//print "xxxxxxxxxxxx $current_search";
  		get_history($current_search);
  		//$_SESSION["new_last_query"]=$current_search;
  		
  		switch ($search_type) {
  			case "simple_search":
  				
  				$aq=new analyse_query(stripslashes($user_query),0,0,1,1,$opac_stemming_active);
  				if ($aq->error) {  					
  					break;
  				}
  				
  				if ($opac_modules_search_title && $look_TITLE) {
  					require_once($base_path.'/search/level1/title.inc.php');
  					$total_results += $nb_result_titres;
  				}
  				
  				
  				
  				
			break;
			case "extended_search":
				$es=new search();
				$table=$es->make_search();
				$requete="select ".$table.".* from $table";
				break;
			case "term_search":
				break;
  		
  		}	
			
			
			
  		print $requete;
  		$resultat=@pmb_mysql_query($requete);
  		while (($r=pmb_mysql_fetch_object($resultat))) {
  			$notices_ids[]=$r->notice_id;
  		}
  		$objects[] = array(
  				'layer' => "record",
  				'ids' => $notices_ids
  		);
  		if(count($notices_ids)){
  			$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  			$result = pmb_mysql_query($requete,$dbh);
  			if(pmb_mysql_num_rows($result)){
  				$categ_ids = array();
  				while ($row = pmb_mysql_fetch_object($result)) {
  					$categ_ids[] = $row->map_emprise_obj_num;
  				}
  				$objects[] = array(
  						'layer' => "authority",
  						'type' => 2,
  						'ids' => $categ_ids
  				);
  			}
  		}
  		
  		printr($objects);
  		return $objects;
  
  		
  		
  		
  		
  		
  		
  		if ($_SESSION["session_history"][$current_search]["NOTI"]["GET"]["mode"] != "") {
  			$mode_search = $_SESSION["session_history"][$current_search]["NOTI"]["GET"]["mode"];
  			switch($mode_search) {
  				case 1 :
  				case 2 :
  				case 9 :
  					$requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
  					$result = pmb_mysql_query($requete,$dbh);
  					$notices_ids = array();
  					while ($row = pmb_mysql_fetch_object($result)) {
  						$notices_ids[] = $row->notice_id;
  					}
  					$objects[] = array(
  							'layer' => "record",
  							'ids' => $notices_ids
  					);
  					if(count($notices_ids)){
  						$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  						$result = pmb_mysql_query($requete,$dbh);
  						if(pmb_mysql_num_rows($result)){
  							$categ_ids = array();
  							while ($row = pmb_mysql_fetch_object($result)) {
  								$categ_ids[] = $row->map_emprise_obj_num;
  							}
  							$objects[] = array(
  									'layer' => "authority",
  									'type' => 2,
  									'ids' => $categ_ids
  							);
  						}
  					}
  					break;
  				case 0 :
    			case 11 :
  					if ($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"]) {
  						$requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
  						$result = pmb_mysql_query($requete,$dbh);
  						$notices_ids = array();
  						while ($row = pmb_mysql_fetch_object($result)) {
  							$notices_ids[] = $row->notice_id;
  						}
  						$objects[] = array(
  								'layer' => "record",
  								'ids' => $notices_ids
  						);
						if(count($notices_ids)){
	  						$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";  						
	  						$result = pmb_mysql_query($requete,$dbh);
	  						if(pmb_mysql_num_rows($result)){
		  						$categ_ids = array();
		  						while ($row = pmb_mysql_fetch_object($result)) {
		  							$categ_ids[] = $row->map_emprise_obj_num;
		  						}
		  						$objects[] = array(
		  								'layer' => "authority",
		  								'type' => 2,
		  								'ids' => $categ_ids
		  						);
	  						}
						}
  					}
  					break;
  				case 3 :
  					$requete = "SELECT object_id FROM caddie_content where caddie_id='".$_SESSION["session_history"][$current_search]["NOTI"]["GET"]["idcaddie"]."' ";
  					$res = pmb_mysql_query($requete, $dbh);
  					$result = pmb_mysql_query($requete,$dbh);
  					$notices_ids = array();
  					while ($row = pmb_mysql_fetch_object($result)) {
  						$notices_ids[] = $row->object_id;
  					}
  					$objects[] = array(
  							'layer' => "record",
  							'ids' => $notices_ids
  					);
  					if(count($notices_ids)){
  						$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  						$result = pmb_mysql_query($requete,$dbh);
  						if(pmb_mysql_num_rows($result)){
  							$categ_ids = array();
  							while ($row = pmb_mysql_fetch_object($result)) {
  								$categ_ids[] = $row->map_emprise_obj_num;
  							}
  							$objects[] = array(
  									'layer' => "authority",
  									'type' => 2,
  									'ids' => $categ_ids
  							);
  						}
  					}
  					break;
  				case 6 :
  					//Récupération et mise en variables globales des valeurs de l'historique
  					if ($_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"][0]) {
  						$search=$_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"];
  						//Pour chaque champ
  						for ($i=0; $i<count($search); $i++) {
  							 
  							//Récupération de l'opérateur
  							$op="op_".$i."_".$search[$i];
  							global $$op;
  							$$op=$_SESSION["session_history"][$current_search]["QUERY"]["POST"][$op];
  					
  							//Récupération du contenu de la recherche
  							$field_="field_".$i."_".$search[$i];
  							global $$field_;
  							$$field_=$_SESSION["session_history"][$current_search]["QUERY"]["POST"][$field_];
  							$field=$$field_;
  					
  							//Récupération de l'opérateur inter-champ
  							$inter="inter_".$i."_".$search[$i];
  							global $$inter;
  							$$inter=$_SESSION["session_history"][$current_search]["QUERY"]["POST"][$inter];
  								
  							//Récupération des variables auxiliaires
  							$fieldvar_="fieldvar_".$i."_".$search[$i];
  							global $$fieldvar_;
  							$$fieldvar_=$_SESSION["session_history"][$current_search]["QUERY"]["POST"][$fieldvar_];
  							$fieldvar=$$fieldvar_;
  						}
  					} 

   					//on instancie la classe search avec le nom de la nouvelle table temporaire
  					if ($_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"][0]) {
  						$sc=new search(false);
  					} else {
  						$sc=new search(false,"search_simple_fields");
  					}
  					
  					$table_tempo=$sc->make_search("tempo_".$current_search);
  					$requete = "select * from ".$table_tempo;
  					$result = pmb_mysql_query($requete,$dbh);
  					$notices_ids = array();
  					while ($row = pmb_mysql_fetch_object($result)) {
  						$notices_ids[] = $row->notice_id;
  					}
  					$objects[] = array(
  							'layer' => "record",
  							'ids' => $notices_ids
  					);
  					if(count($notices_ids)){
  						$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  						$result = pmb_mysql_query($requete,$dbh);
  						if(pmb_mysql_num_rows($result)){
  							$categ_ids = array();
  							while ($row = pmb_mysql_fetch_object($result)) {
  								$categ_ids[] = $row->map_emprise_obj_num;
  							}
  							$objects[] = array(
  									'layer' => "authority",
  									'type' => 2,
  									'ids' => $categ_ids
  							);
  						}
  					}
  					break;
  					default:
  						// authpersos
  						if($mode_search>1000){
  							if ($_SESSION["session_history"][$current_search]["NOTI"]["POST"]) {
  								$requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
  								$result = pmb_mysql_query($requete,$dbh);
  								$notices_ids = array();
  								while ($row = pmb_mysql_fetch_object($result)) {
  									$notices_ids[] = $row->notice_id;
  								}
  								$objects[] = array(
  										'layer' => "record",
  										'ids' => $notices_ids
  								);
  								if(count($notices_ids)){
  									$requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (".implode(",",$notices_ids).")";
  									$result = pmb_mysql_query($requete,$dbh);
  									if(pmb_mysql_num_rows($result)){
  										$categ_ids = array();
  										while ($row = pmb_mysql_fetch_object($result)) {
  											$categ_ids[] = $row->map_emprise_obj_num;
  										}
  										$objects[] = array(
  												'layer' => "authority",
  												'type' => 2,
  												'ids' => $categ_ids
  										);
  									}
  								}
  							}  							
  						}
  					break;
  						
  			}
  		} elseif ($_SESSION["session_history"][$current_search]["AUT"]["GET"]["mode"] != "") {
  			$mode_search = $_SESSION["session_history"][$current_search]["AUT"]["GET"]["mode"];
  			switch($mode_search) {
  				case 0 :
  					if ($_SESSION["session_history"][$current_search]["AUT"]["POST"]) {
  						foreach ($_SESSION["session_history"][$current_search]["AUT"]["POST"] as $key=>$valeur) {
  							global $$key;
  							$$key=$valeur;
  						}
  						// Recherche sur l'auteur uniquement :
  						$aq=new analyse_query(stripslashes($author_query),0,0,1,1);
  						$restrict='';
  						if ($typdoc_query) $restrict = "and typdoc='".$typdoc_query."' ";
  						if ($statut_query) $restrict.= "and statut='".$statut_query."' ";
  						if ($typdoc_query || $statut_query || $acces_j) {
  					
  							$restrict ="and responsability_author=author_id and responsability_notice=notice_id ".$restrict." ";
  							$members=$aq->get_query_members("authors","concat(author_name,', ',author_rejete)","index_author","author_id");
  								
  							$requete_count = "select count(distinct author_id) from authors, responsability, notices ";
  							$requete_count.= $acces_j;
  							$requete_count.= "where ".$members["where"]." ";
  							$requete_count.= $restrict;
  								
  							$requete = "select author_id,".$members["select"]." as pert from authors, responsability, notices ";
  							$requete.= $acces_j;
  							$requete.= "where ".$members["where"]." ";
  							$requete.= $restrict." group by author_id order by pert desc,author_name, author_rejete,author_numero , author_subdivision ";
  								
  						} else {
  							$requete_count=$aq->get_query_count("authors","concat(author_name,', ',author_rejete)","index_author","author_id");
  							$t_query=$aq->get_query_members("authors","concat(author_name,', ',author_rejete)","index_author","author_id");
  							$requete="select author_id,".$t_query["select"]." as pert from authors where ".$t_query["where"]." group by author_id order by pert desc,author_name, author_rejete, author_numero , author_subdivision ";
  						}
  							
  						$t_query=@pmb_mysql_query($requete,$dbh);
  						while ($row = pmb_mysql_fetch_object($t_query)) {
  							$aut_ids[] = $row->author_id;
  						}
  						$objects[] = array(
  								'layer' => "authority",
  								'ids' => $aut_ids
  						);
  					}
  					break;
  			}
  		}
  		return $objects;
  		 
  	} // end of member function get_objects

  	public function have_results(){
  		if(!$this->model){
  			return false;
  		}else{
  			return $this->model->have_results();
  		}
  	}
  	
  	public function get_holds_json_informations($indice){
  		global $pmb_url_base;
  		global $dbh;
		$json = array(); 	
  		if($this->model){
  			$json = $this->model->get_holds_informations($this->objects[$indice]['layer']);
  			return json_encode($json);
  		}
  	}
  	
  	public function get_json_informations(){
  		global $opac_url_base;
  		global $opac_map_base_layer_type;
  		global $opac_map_base_layer_params;
  		global $dbh;
  		
  		$layer_params = json_decode($opac_map_base_layer_params,true);
  		$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
  		if(count($layer_params)){
  			if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
  			if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
  			if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
  		}
  				
  		if ($this->ajax){
  			return "mode:\"search_result\", searchId: ".$this->mode.",".$baselayer.",layers_url: \"".$opac_url_base."ajax.php?module=ajax&categ=map&sub=search&action=get_layers\"";
  		}else if($this->model){
  			$json = array();
	  		$map_hold = $this->get_bounding_box();
	  		if($map_hold){
		  		$coords = $map_hold->get_coords();
		  		$lats = $longs = array();
		  		for($i=0 ; $i<count($coords) ; $i++){
					$lats[] = $coords[$i]->get_decimal_lat();
					$longs[] = $coords[$i]->get_decimal_long();
		  		}
		  		$lats = array_unique($lats);
		  		$longs = array_unique($longs);
		  		sort($lats);
		  		sort($longs);
		  		$json = array(
			  		'initialFit' => array($longs[0],$lats[0],$longs[1],$lats[1]),
			  		'layers' => $this->model->get_json_informations(true, $opac_url_base,false)
			  	);
	  		}else{
	  			$json = array(
	  				'initialFit' => array(0,0,0,0),
	  				'layers' => array(
	  					array(
	  						'type' => "record",
	  						'name' => "record",
	  						'holds' => array(),
	  						'ajax' => false
	  					)
	  				)	
	  			);
	  		}
	  		return json_encode($json);
  		}
  	}
  	
  	public function get_bounding_box(){
  		return $this->model->get_bounding_box();
  	}

} // end of map_search_controler