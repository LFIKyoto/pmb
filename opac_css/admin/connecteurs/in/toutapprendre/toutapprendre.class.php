<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: toutapprendre.class.php,v 1.2.4.1 2015-09-11 08:53:14 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");
require_once($base_path."/cms/modules/common/includes/pmb_h2o.inc.php");


class toutapprendre extends connector {
	//Variables internes pour la progression de la rï¿½cupï¿½ration des notices
	var $callback_progress;		//Nom de la fonction de callback progression passï¿½e par l'appellant
	var $current_set;			//Set en cours de synchronisation
	var $total_sets;			//Nombre total de sets sï¿½lectionnï¿½s
	var $metadata_prefix;		//Prï¿½fixe du format de donnï¿½es courant
	var $source_id;				//Numï¿½ro de la source en cours de synchro
	var $n_recu;				//Nombre de notices reï¿½ues
	var $xslt_transform;		//Feuille xslt transmise
	var $sets_names;			//Nom des sets pour faire plus joli !!
	var $del_old;				//Supression ou non des notices dejï¿½ existantes
	var $schema_config;
	
	//Résultat de la synchro
	var $error;					//Y-a-t-il eu une erreur	
	var $error_message;			//Si oui, message correspondant
	
    function toutapprendre($connector_path="") {
    	parent::connector($connector_path);
    }
    
    function get_id() {
    	return "toutapprendre";
    }
    
    //Est-ce un entrepot ?
	function is_repository() {
		return 1;
	}
    
    function unserialize_source_params($source_id) {
    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			$vars=unserialize($params["PARAMETERS"]);
			$params["PARAMETERS"]=$vars;
		}
		return $params;
    }
    
   function source_get_property_form($source_id) {
    	global $charset;
    	
    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		//URL
		if (!isset($url))
			$url = "http://biblio.toutapprendre.com/cours/catalogue.asp?id=353&complet=yes";
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["toutapprendre_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='url' id='url' class='saisie-60em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
			</div>
		</div>
		<div class='row'></div>";
   	
		return $form;
    }
    
    function make_serialized_source_properties($source_id) {
    	global $url;
    	$t = array();
    	$t["url"]=stripslashes($url);
		$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}
			
	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	function fetch_default_global_values() {
		$this->timeout=40;
		$this->repository=1;
		$this->retry=3;
		$this->ttl=1800;
		$this->parameters="";
	}
	
	//Formulaire des propriétés générales
	function get_property_form() {
		$this->fetch_global_properties();
		return "";
	}
	
	function make_serialized_properties() {
		$this->parameters="";
	}
		
	function progress($query,$token) {
		$callback_progress=$this->callback_progress;
		if ($token["completeListSize"]) {
			$percent=($this->current_set/$this->total_sets)+(($token["cursor"]/$token["completeListSize"])/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
			//$nlu=$token["cursor"];
			//$ntotal=$token["completeListSize"];
		} else {
			$percent=($this->current_set/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
		}
		call_user_func($callback_progress,$percent,$nlu,$ntotal);
	}
		
	function cancel_maj($source_id) {
		return true;
	}
	
	function break_maj($source_id) {
		return true;
	}
	
	function form_pour_maj_entrepot($source_id) {
		return false;
	}
	
	//Nécessaire pour passer les valeurs obtenues dans form_pour_maj_entrepot au javascript asynchrone
	function get_maj_environnement($source_id) {
		return array();
	}
	
	function sync_custom_page($source_id) {
		return '';
	}
	
	function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
		global $base_path,$charset;
		
		$this->n_recu=0;	
		$this->callback_progress=$callback_progress;	
		$params=$this->get_source_params($source_id);
		$this->fetch_global_properties();
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		if (!isset($url)) {
			$this->error_message = $this->msg["toutapprendre_unconfigured"];
			$this->error = 1;
			return;
		}
		//Recherche de la dernière date...
		$requete="select unix_timestamp(max(date_import)) from entrepot_source_".$source_id." where 1;";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$last_date=pmb_mysql_result($resultat,0,0);
			if ($last_date) {				
				$last_date+=3600*24;
			}	
		}
	
		$ch = curl_init();

		$addr=$url;
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $addr);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
		configurer_proxy_curl($ch,$addr);	
	 	$xml=curl_exec($ch);	
	 	
	 	if($charset=='utf-8') $xml = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
	 			'|[\x00-\x7F][\x80-\xBF]+'.
	 			'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
	 			'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
	 			'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
	 			'?', $xml );	 	 
	 	
	 	@ini_set("zend.ze1_compatibility_mode", "0");
	 	$this->dom = new DomDocument();
	 	$this->dom->encoding = $charset;
	 	
	 	if(!@$this->dom->loadXML($xml)) return 0;
	 	 
 		 $cours = $this->dom->getElementsByTagName('cours');
 		 foreach($cours as $cour){
 		 	$data_notice=array(); 		 	
 		 	if($cour->childNodes->length) {
 		 		foreach($cour->childNodes as $i) {
 		 			if($charset!='utf-8')$val= utf8_decode($i->nodeValue);
 		 			else $val=$i->nodeValue;
 		 			$data_notice[$i->nodeName] =$val;
		 		 		
 		 		}
 		 	} 		 	
 			$this->rec_record($this->notice_2_uni($data_notice),$source_id);
 				
 		}	 			
 		curl_close($ch);	
		return $this->n_recu;
	}
	
	function search($source_id,$query,$search_id) {
	}	
	
	function notice_2_uni($nt) {

		$unimarc=array();
		$unimarc["001"][0]=$nt["pk"];

		$unimarc["200"][0]["a"][0]=$nt["titre"];
		
		//Editeur
		if ($nt["editeur"]) $unimarc["210"][0]["c"][0]=$nt["editeur"];
		
		//Résumé
		if ($nt["description"])  $unimarc["330"][0]["a"][0]=$nt["description"];
					
		// Link demo		
		if(count($nt["demo"])) $unimarc["856"][0]["u"][0]=$nt["demo"];
		
		// Keywords
		if ($nt["categorie"])  $unimarc["610"][0]["a"][0]=$nt["categorie"];		
			
		// vignette
		if ($nt["thumbnail"])	$unimarc["896"][0]["a"][0]=$nt["thumbnail"];
		
		// collation		
		if ($nt["dureeCours"])  $unimarc["215"][0]["a"][0]=$nt["dureeCours"];
		
		return $unimarc;
	}	
	
	function rec_record($record,$source_id) {
		global $charset,$base_path,$url,$search_index;
		
		$date_import=date("Y-m-d H:i:s",time());
		
		//Recherche du 001
		$ref=$record["001"][0];
		//Mise à jour 
		if ($ref) {
			//Si conservation des anciennes notices, on regarde si elle existe
			if (!$this->del_old) {
				$requete="select count(*) from entrepot_source_".$source_id." where ref='".addslashes($ref)."' ";
				$rref=pmb_mysql_query($requete);
				if ($rref) {
					$ref_exists=pmb_mysql_result($rref,0,0);
					if($ref_exists) return 1;
				}
			}
			//Si pas de conservation des anciennes notices, on supprime
			if ($this->del_old) {
				$requete="delete from entrepot_source_".$source_id." where ref='".addslashes($ref)."' ";
				pmb_mysql_query($requete);
			}
			//Si pas de conservation ou reférence inexistante
			if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
				//Insertion de l'entête
				$n_header["rs"]="*";
				$n_header["ru"]="*";
				$n_header["el"]="1";
				$n_header["bl"]="m";
				$n_header["hl"]="0";
				$n_header["dt"]=$this->types[$search_index[$url][0]];
				if (!$n_header["dt"]) $n_header["dt"]="a";
				
				$n_header["001"]=$record["001"][0];
				//Récupération d'un ID
				$requete="insert into external_count (recid, source_id) values('".addslashes($this->get_id()." ".$source_id." ".$ref)."', ".$source_id.")";
				$rid=pmb_mysql_query($requete);
				if ($rid) $recid=pmb_mysql_insert_id();
				
				foreach($n_header as $hc=>$code) {
					$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid) values(
					'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".$date_import."',
					'".$hc."','',-1,0,'".addslashes($code)."','',$recid,'".$recid."')";
					pmb_mysql_query($requete);
				}
				
				$field_order=0;
				foreach ($record as $field=>$val) {
					for ($i=0; $i<count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ($val[$i] as $sfield=>$vals) {
								for ($j=0; $j<count($vals); $j++) {
									//if ($charset!="utf-8")  $vals[$j]=utf8_decode($vals[$j]);
									$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid) values(
									'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".$date_import."',
									'".addslashes($field)."','".addslashes($sfield)."',".$field_order.",".$j.",'".addslashes($vals[$j])."',
									' ".addslashes(strip_empty_words($vals[$j]))." ',$recid)";
									pmb_mysql_query($requete);
								}
							}
						} else {
							//if ($charset!="utf-8")  $vals[$i]=utf8_decode($vals[$i]);
							$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid) values(
							'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".$date_import."',
							'".addslashes($field)."','',".$field_order.",0,'".addslashes($val[$i])."',
							' ".addslashes(strip_empty_words($val[$i]))." ',$recid)";
							pmb_mysql_query($requete);
						}
						$field_order++;
					}
				}
				$this->n_recu++;
			}
		}
	}
	
	function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		global $dbh;
		
		$enrichment= array();
		
		$params=$this->get_source_params($source_id);
		if ($params['PARAMETERS']) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
		}
		
		$fields = array();
		
		$query = "select recid from notices_externes where num_notice = ".$notice_id;
		$result = pmb_mysql_query($query, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				$external_infos = explode(" ", $row->recid);
				
				$query = "select ufield, usubfield, field_order, value from entrepot_source_".$external_infos[1]." where connector_id = '".$external_infos[0]."' and source_id = '".$external_infos[1]."' and ref = '".$external_infos[2]."'";
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						// Si c'est une leçon, on a affaire à un champ répétable
						if ($row->ufield == 917) {
							$fields[$row->ufield][$row->field_order][$row->usubfield] = $row->value;
						} else {
							$fields[$row->ufield][$row->usubfield] = $row->value;
						}
					}
				}
			}
		}
		$lessons = array();
		
		// Titre
		$lessons['title'] = $fields[200]['a'];
		
		// Editeur
		$lessons['publisher'] = $fields[210]['c'];
		
		// Durée
		$lessons['duration'] = $fields[215]['a'];
		
		// Catégorie
		$lessons['category'] = $fields[610]['a'];
		
		// Démo
		$lessons['demo'] = $fields[856]['u'];
		
		// Vignette
		$lessons['thumbnail'] = $fields[896]['a'];
		
		// leçons
		$lessons['lessons'] = array();
		
		foreach ($fields[917] as $lesson) {
			$lessons['lessons'][] = array(
					'id' => $lesson['a'],
					'title' => $lesson['b']
			);
		}
		$infos = unserialize($this->parameters);
		
		// url de base
		if($_SESSION['user_code'] && isset($infos['privatekey'])) {
			global $empr_cb, $empr_nom, $empr_prenom;
			
			$date = date("YmdHi");
			$hash = md5($empr_cb.$date.$infos['privatekey']);

			$lessons['base_url'] = "http://biblio.toutapprendre.com/ws/wsUrl.aspx?iduser=".$empr_cb."&firstname=".$empr_prenom."&lastname=".$empr_nom."&etablissement=".$infos['establishmentid']."&d=".$date."&hash=".$hash."&pkl=";
		}
		
		$enrichment[$type]['content'] = H2o::parseString(stripslashes($vars['enrichment_template']))->render(array("lessons"=>$lessons));
		$enrichment['source_label'] = $this->msg['toutapprendre_enrichment_source'];
		
		return $enrichment;
	}

	function enrichment_is_allow(){
		return true;
	}
	
	function getTypeOfEnrichment($notice_id, $source_id){
		global $dbh;
	
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
		}
	
		$type = array();
	
		// On n'affiche l'onglet que si le champ perso est renseigné
		$query = "select 1 from notices_custom_values where notices_custom_champ = ".$vars['cp_field']." and notices_custom_origine= ".$notice_id;
		$result = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_num_rows($result)){
			$type['type'] = array(
					array(
							"code" => "toutapprendre",
							"label" => $this->msg['toutapprendre_toutapprendre']
					)
			);
			$type['source_id'] = $source_id;
		}
		return $type;
	}
	
	function getEnrichmentHeader($source_id){
		$header= array();
		return $header;
	}
}// class end


