<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: wikipedia.class.php,v 1.6 2015-04-03 11:16:28 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/curl.class.php");
require_once($class_path."/nusoap/nusoap.php");

class wikipedia extends connector {
	//Variables internes pour la progression de la récupération des notices
	var $del_old;				//Supression ou non des notices dejà existantes
	
	var $profile;				//Profil wikipedia
	var $match;					//Tableau des critères wikipedia
	var $current_site;			//Site courant du profile (n°)
	var $searchindexes;			//Liste des indexes de recherche possibles pour le site
	var $current_searchindex;	//Numéro de l'index de recherche de la classe
	var $match_index;			//Type de recherche (power ou simple)
	var $types;					//Types de documents pour la conversino des notices
	
	//Résultat de la synchro
	var $error;					//Y-a-t-il eu une erreur	
	var $error_message;			//Si oui, message correspondant
	
    function wikipedia($connector_path="") {
    	parent::connector($connector_path);
    }
    
    function get_id() {
    	return "wikipedia";
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
    	
    	$keys['accesskey']=$accesskey;
		$keys['secretkey']=$secretkey;
		$this->parameters = serialize($keys);
	}

	function enrichment_is_allow(){
		return true;
	}
	
	function getEnrichmentHeader(){
		global $lang;
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour wikipedia-->";
		$header[]= "<script type='text/javascript'>
			function load_wiki_bio(notice_id,type,label){
				var	wiki= new http_request();
				var content = document.getElementById('div_'+type+notice_id);
				content.innerHTML = '';
				var patience= document.createElement('img');
				patience.setAttribute('src','images/patience.gif');
				patience.setAttribute('align','middle');
				patience.setAttribute('id','patience'+notice_id);
				content.appendChild(patience);
				wiki.request('./ajax.php?module=ajax&categ=enrichment&action=enrichment&type='+type+'&id='+notice_id,true,'&enrich_params[label]='+label,true,gotEnrichment);
			}
		</script>";
		return $header;
	}
	
	function getTypeOfEnrichment($source_id){
		$type['type'] = array(
			array( 
				'code' => "wiki",
				'label' => $this->msg["wikipedia_label"]
			),
			"bio"
		);		
		$type['source_id'] = $source_id;
		return $type;
	}
	
	function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		$enrichment= array();
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case "bio" :
				$enrichment['bio']['content'] = $this->get_author_page($notice_id,$enrich_params);	
				break;
			case "wiki" :
			default :
				$enrichment['wiki']['content'] = $this->noticeInfos($notice_id);
				break;
		}		
		$enrichment['source_label']=$this->msg['wikipedia_enrichment_source'];
		return $enrichment;
	}
	
	function get_author_page($notice_id,$enrich_params){
		global $lang;
		global $charset;
		if($enrich_params['label']!=""){
			$author = $enrich_params['label'];
		}else{
			//on va chercher l'auteur principal...
			$query = "select responsability_author from responsability where responsability_notice =".$notice_id." and responsability_type=0";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$author_id = pmb_mysql_result($result,0,0);
				$author_class = new auteur($author_id);
				$author =  $author_class->isbd_entry;
			}
		}
		$curl = new Curl();
		//on fait un premier appel pour regarder si on a quelque chose chez Wikipédia...
		$url = "http://".substr($lang,0,2).".wikipedia.org/w/api.php?format=json&action=opensearch&search=".rawurlencode($author)."&limit=20";
		$json = $curl->get($url);
		$search = json_decode($json);
		if(count($search[1])==1 || $enrich_params['label']!=""){
			$url = "http://".substr($lang,0,2).".wikipedia.org/w/api.php?format=json&action=query&titles=".rawurlencode($search[1][0])."&prop=revisions&rvprop=content&rvparse=1";
			$json = $curl->get($url);
			$response = json_decode($json);
			$html_to_return="";
			foreach($response->query->pages as $page){
				foreach($page->revisions[0] as $rev){
					$html_to_return .= utf8_decode($rev);
				}
			}
			$html_to_return = str_replace("href=\"/","target='_blank' href=\"http://".substr($lang,0,2).".wikipedia.org/",$html_to_return);		
			@ini_set("zend.ze1_compatibility_mode", "0");
			$dom = new domDocument();
			$dom->loadHTML($html_to_return);
			$spans = $dom->getElementsByTagName("span");
			for($i=0; $i<$spans->length ; $i++){
				for($j=0 ; $j<$spans->item($i)->attributes->length ; $j++){
					if($spans->item($i)->attributes->item($j)->name == "class" && $spans->item($i)->attributes->item($j)->nodeValue == "editsection"){
						$spans->item($i)->parentNode->removeChild($spans->item($i));
					}
				}
			}
			$html_to_return = $dom->saveHTML();
			@ini_set("zend.ze1_compatibility_mode", "1");		
		}else if(count($search[1])>1){
			//si plus d'un résultat on propose le choix...
			$html_to_return = "
			<div id='wiki_bio_".$notice_id."'>
				<table>";
			for($i=0 ; $i<count($search[1]) ; $i++){
				if($i%4 == 0){
					$html_to_return.= "
					<tr>";
				}
				$html_to_return.="
						<td>
							<a href='#' onclick='load_wiki_bio(\"".$notice_id."\",\"bio\",\"".htmlentities(utf8_decode($search[1][$i]),ENT_QUOTES,$charset)."\");return false;' >".utf8_decode($search[1][$i])."</a>
						</td>";
				if($i%4 == 3){
					$html_to_return.= "
					</tr>";
				}
			}
			$html_to_return.= "
				</table>
			</div>";
			
			
		}else{
			$html_to_return = $this->msg['wikipedia_no_informations'];
		}
//		print $html_to_return;
		return $html_to_return; 
	}
	
	function noticeInfos($notice_id){
		global $lang;
		
		$rqt = "select tit1 from notices where notice_id='$notice_id'";
		$res =pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$titre = pmb_mysql_result($res,0,0);
			$curl = new Curl();
			//on fait un premier appel pour regarder si on a quelque chose chez Wikipédia...
			$url = "http://".substr($lang,0,2).".wikipedia.org/w/api.php?format=json&action=opensearch&search=".rawurlencode($titre);
			$json = $curl->get($url);
			$search = json_decode($json);
			if(count($search[1])>0){
				$url = "http://".substr($lang,0,2).".wikipedia.org/w/api.php?format=json&action=query&titles=".rawurlencode($search[1][0])."&prop=revisions&rvprop=content&rvparse=1";
				$json = $curl->get($url);
				$response = json_decode($json);
				$html_to_return="";
				foreach($response->query->pages as $page){
					foreach($page->revisions[0] as $rev){
						$html_to_return .= utf8_decode($rev);
					}
				}
				$html_to_return = str_replace("href=\"/","target='_blank' href=\"http://".substr($lang,0,2).".wikipedia.org/",$html_to_return);		
				@ini_set("zend.ze1_compatibility_mode", "0");
				$dom = new domDocument();
				$dom->loadHTML($html_to_return);
				$spans = $dom->getElementsByTagName("span");
				for($i=0; $i<$spans->length ; $i++){
					for($j=0 ; $j<$spans->item($i)->attributes->length ; $j++){
						if($spans->item($i)->attributes->item($j)->name == "class" && $spans->item($i)->attributes->item($j)->nodeValue == "editsection"){
							$spans->item($i)->parentNode->removeChild($spans->item($i));
						}
					}
				}
				$html_to_return = $dom->saveHTML();
				@ini_set("zend.ze1_compatibility_mode", "1");
			}else{
				$html_to_return = $this->msg['wikipedia_no_informations'];
			}
		}
		
		return $html_to_return; 
	}
}
?>