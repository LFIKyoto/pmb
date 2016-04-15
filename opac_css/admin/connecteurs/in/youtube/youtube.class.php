<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: youtube.class.php,v 1.5 2015-04-03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

require_once("youtube_api.class.php");

class youtube extends connector {
	
    function youtube($connector_path="") {
    	parent::connector($connector_path);
    }
    
    function get_id() {
    	return "youtube";
    }
    
    //Est-ce un entrepot ?
	function is_repository() {
		return 2;
	}
    
    function unserialize_source_params($source_id) {
    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			$vars=unserialize($params["PARAMETERS"]);
			$params["PARAMETERS"]=$vars;
		}
		return $params;
    }
    
    function get_libelle($message) {
    	if (substr($message,0,4)=="msg:") return $this->msg[substr($message,4)]; else return $message;
    }
    
    function source_get_property_form($source_id) {
		return "";
    }
    
    function make_serialized_source_properties($source_id) {
    	$this->sources[$source_id]["PARAMETERS"]=serialize(array());
	}
	
	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	function fetch_default_global_values() {
		$this->timeout=5;
		$this->repository=2;
		$this->retry=3;
		$this->ttl=1800;
		$this->parameters="";
	}
	
	 //Formulaire des propriétés générales
	function get_property_form() {
		return "";
	}
    
    function make_serialized_properties() {
    	global $accesskey, $secretkey;
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$keys = array();
		$this->parameters = serialize($keys);
	}

	function enrichment_is_allow(){
		return true;
	}
	
	function getEnrichmentHeader($source_id){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour Youtube-->";
		return $header;
	}
	
	function getTypeOfEnrichment($notice_id,$source_id){
		$type['type'] = array(
			array(
				'code' => "youtube",
				'label' => $this->msg['youtube']
			)
			
		);		
		$type['source_id'] = $source_id;
		return $type;
	}
	
	function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
		global $lang;
		
		$this->noticeToEnrich= $notice_id;
		
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		$enrichment= array();
		
		$infos = $this->get_notice_infos();
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case "youtube" :
				$api = new youtube_api();
				$vars = array(
					"q" => utf8_encode($infos['title']." ".$infos['author'])
				);
				$result = $api->search_videos($vars);
				
				if($result->feed->{'openSearch$totalResults'}->{'$t'} >= $result->feed->{'openSearch$itemsPerPage'}->{'$t'}){
					$aff_result = sprintf($this->msg['youtube_partial_results'],$result->feed->{'openSearch$itemsPerPage'}->{'$t'},$result->feed->{'openSearch$totalResults'}->{'$t'});
					$aff_result.= "<br/>
					<a target='_blank' href='http://www.youtube.com/results?search_query=".$vars['q']."'>".$this->msg['youtube_go_to_result_page']."</a>";
				}else{
					$aff_result = sprintf($this->msg['youtube_all_results'],$result->feed->{'openSearch$totalResults'}->{'$t'});
				}
				$enrichment['youtube']['content']= "<p style='padding:10px;'>".$aff_result."</p>";
				foreach($result->feed->entry as $elem){
					$enrichment['youtube']['content'].= "
					<span style='margin-right : 4px;'>";
					if(!$elem->{'yt$noembed'}){
						$enrichment['youtube']['content'].= "
						<iframe style='width:480px;height:360px;' src='".$elem->{'media$group'}->{'media$content'}[0]->url."' frameborder='0' allowfullscreen></iframe>";
					}else{
						$enrichment['youtube']['content'].= " 
						<a target='_blank' href='".$elem->{'media$group'}->{'media$player'}[0]->url."'><img alt='".$elem->content->{'$t'}."' title='".$elem->title->{'$t'}."' src='".$elem->{'media$group'}->{'media$thumbnail'}[0]->url."'/></a>";
					}
					$enrichment['youtube']['content'].= "
					</span>";
				}
				break;
		}		
		$enrichment['source_label']=$this->msg['youtube_enrichment_source'];
		return $enrichment;
	}

	function get_notice_infos(){
		$infos = array();
		//on va chercher le titre de la notice...
		$query = "select tit1 from notices where notice_id = ".$this->noticeToEnrich;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos['title'] = pmb_mysql_result($result,0,0);
		}
		//on va chercher l'auteur principal...
		$query = "select responsability_author from responsability where responsability_notice =".$this->noticeToEnrich." and responsability_type=0";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$author_id = pmb_mysql_result($result,0,0);
			$author = new auteur($author_id);
			//$infos['author'] = $author->display;
			$infos['author'] = ($author->rejete!= ""? $author->rejete." ":"").$author->name;
		}
		return $infos; 		
	}
}

?>