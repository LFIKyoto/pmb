<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: artevod.class.php,v 1.2 2015-05-25 09:35:26 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once("$class_path/curl.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/parser.inc.php");
require_once($base_path."/cms/modules/common/includes/pmb_h2o.inc.php");
if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	if (substr(phpversion(), 0, 1) == "5") @ini_set("zend.ze1_compatibility_mode", "0");
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

class artevod extends connector {
	//Variables internes pour la progression de la récupération des notices
	var $callback_progress;		//Nom de la fonction de callback progression passée par l'appellant
	var $source_id;				//Numéro de la source en cours de synchro
	var $n_recu;				//Nombre de notices reçues
	var $xslt_transform;		//Feuille xslt transmise
	var $del_old;				//Supression ou non des notices dejà existantes
	
	//Résultat de la synchro
	var $error;					//Y-a-t-il eu une erreur
	var $error_message;			//Si oui, message correspondant
	
	function artevod($connector_path="") {
		parent::connector($connector_path);
		$xml=file_get_contents($connector_path."/profil.xml");
		$this->profile=_parser_text_no_function_($xml,"ARTEVODCONFIG");
	}
    
    function get_id() {
    	return "artevod";
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

    function get_libelle($message) {
    	if (substr($message,0,4)=="msg:") return $this->msg[substr($message,4)]; else return $message;
    }
    
   	function source_get_property_form($source_id) {
    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		$searchindexes=$this->profile["SEARCHINDEXES"][0]["SEARCHINDEX"];
		if (!$url) $url=$searchindexes[0]["URL"];
		$form = "";
		if (count($searchindexes) > 1) {
			$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='search_indexes'>".$this->msg["artevod_search_in"]."</label>
				</div>
				<div class='colonne_suite'>
					<select name='url' id='url' >";
				for ($i=0; $i<count($searchindexes); $i++) {
					$form.="<option value='".$searchindexes[$i]["URL"]."' ".($url==$searchindexes[$i]["URL"]?"selected":"").">".$this->get_libelle($searchindexes[$i]["COMMENT"])."</option>\n";
				}
				$form.="
				</select>
				</div>
			</div>";
		} else {
			$form .= "
			<input type='hidden' id='url' name='url' value='".$searchindexes[0]["URL"]."' />
			";
		}
		$form .= "<div class='row'>
			<div class='colonne3'>
				<label for='xslt_file'>".$this->msg["artevod_xslt_file"]."</label>
			</div>
			<div class='colonne_suite'>
				<input name='xslt_file' type='file'/>";
		if ($xsl_transform) $form.="<br /><i>".sprintf($this->msg["artevod_xslt_file_linked"],$xsl_transform["name"])."</i> : ".$this->msg["artevod_del_xslt_file"]." <input type='checkbox' name='del_xsl_transform' value='1'/>";
		$form.="</div>
		</div>";
		$form .= "<div class='row'></div>";
		return $form;
    }
    
    function make_serialized_source_properties($source_id) {
    	global $url;
    	global $del_xsl_transform;
    	 
    	$t["url"]=$url;
    	 
    	//Vérification du fichier
    	if (($_FILES["xslt_file"])&&(!$_FILES["xslt_file"]["error"])) {
    		$xslt_file_content=array();
    		$xslt_file_content["name"]=$_FILES["xslt_file"]["name"];
    		$xslt_file_content["code"]=file_get_contents($_FILES["xslt_file"]["tmp_name"]);
    		$t["xsl_transform"]=$xslt_file_content;
    	} else if ($del_xsl_transform) {
    		$t["xsl_transform"]="";
    	} else {
    		$oldparams=$this->get_source_params($source_id);
    		if ($oldparams["PARAMETERS"]) {
    			//Anciens paramètres
    			$oldvars=unserialize($oldparams["PARAMETERS"]);
    		}
    		$t["xsl_transform"] = $oldvars["xsl_transform"];
    	}
    	
    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
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
    	global $charset;
    	$this->fetch_global_properties();
    	//Affichage du formulaire en fonction de $this->parameters
    	if ($this->parameters) {
    		$keys = unserialize($this->parameters);
    		$accesskey= $keys['accesskey'];
    		$secretkey=$keys['secretkey'];
    		$privatekey=$keys['privatekey'];
    	} else {
    		$accesskey="";
    		$secretkey="";
    		$privatekey="";
    	}
    	$r="<div class='row'>
				<div class='colonne3'><label for='accesskey'>".$this->msg["artevod_key"]."</label></div>
				<div class='colonne-suite'><input type='text' id='accesskey' name='accesskey' value='".htmlentities($accesskey,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='secretkey'>".$this->msg["artevod_secret_key"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='secretkey' name='secretkey' value='".htmlentities($secretkey,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='privatekey'>".$this->msg["artevod_private_key"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='privatekey' name='secretkey' value='".htmlentities($privatekey,ENT_QUOTES,$charset)."'/></div>
			</div>";
    	return $r;
    }
    
    function make_serialized_properties() {
    	global $accesskey, $secretkey, $privatekey;
    	//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
    	$keys = array();
    	 
    	$keys['accesskey']=$accesskey;
    	$keys['secretkey']=$secretkey;
    	$keys['privatekey']=$privatekey;
    	$this->parameters = serialize($keys);
    }
    
    function apply_xsl_to_xml($xml, $xsl) {
    	global $charset;
    	
    	$xh = xslt_create();
    	xslt_set_encoding($xh, $charset);
    	$arguments = array(
    			'/_xml' => $xml,
    			'/_xsl' => $xsl
    	);
    	$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
    	xslt_free($xh);
    	return $result;
    }
    
    function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
    	global $charset,$base_path;
    	
    	$this->fetch_global_properties();
    	$keys = unserialize($this->parameters);

		$this->callback_progress=$callback_progress;
		$params=$this->unserialize_source_params($source_id);
		$p=$params["PARAMETERS"];
		$this->source_id=$source_id;
		$this->n_recu=0;
		
		//Récupération du fichier XML distant en cURL
		$xml="";
		if(strpos($p["url"],"?")) {
			$url = substr($p["url"],0,strpos($p["url"],"?"));
		} else {
			$url = $p["url"];
		}
			
		$aCurl = new Curl();
		$aCurl->timeout=60;
		@mysql_set_wait_timeout();
		
		//Authentification Basic
		if (substr($url,0,7) == "http://") {
			$auth_basic = "http://".$keys["accesskey"].":".$keys["secretkey"]."@".substr($url,7);
		} elseif (substr($url,0,8) == "https://") {
			$auth_basic = "https://".$keys["accesskey"].":".$keys["secretkey"]."@".substr($url,8);
		} else {
			$auth_basic = $keys["accesskey"].":".$keys["secretkey"]."@".$url;
		}
			
		//On fait un premier appel pour récupérer le nombre total de documents
 		$url_temp_auth_basic = $auth_basic."?partial=0&page_size=0";
		$content = $aCurl->get($url_temp_auth_basic);
 		$xml_content=$content->body;
 			
 		if($xml_content && $content->headers['Status-Code'] == 200){
 			$xsl_transform=$p["xsl_transform"]["code"];
 			if($xsl_transform){
 				if($xsl_transform['code'])
 					$xsl_transform_content = $xsl_transform['code'];
 				else $xsl_transform_content = "";
 			}
 			if($xsl_transform_content == "") {
 				$xsl_transform_content = file_get_contents($base_path."/admin/connecteurs/in/artevod/xslt/artevod_to_pmbxmlunimarc.xsl");
 			}
 			$params = _parser_text_no_function_($xml_content,"WSOBJECTLISTQUERY");
 			if($params["TOTAL_COUNT"]) {
 				$this->n_total = $params["TOTAL_COUNT"]; 
 				$nb = 0;
 				$nb_per_pass = 50;
 				$page_nb = 1;
 				while ($nb <= $params["TOTAL_COUNT"]) {
 				 	$url_temp_auth_basic = $auth_basic."?partial=0&page_size=".$nb_per_pass."&page_nb=".$page_nb;
 				 	$content = $aCurl->get($url_temp_auth_basic);
 				 	$xml_content=$content->body;
 				 	if($xml_content && $content->headers['Status-Code'] == 200){
 				 		$pmbxmlunimarc = $this->apply_xsl_to_xml($xml_content, $xsl_transform_content);
 				 		$this->rec_records($pmbxmlunimarc, $this->source_id,'');
 				 	}
 				 	$page_nb++;
 				 	$nb = $nb + $nb_per_pass;
 				}
 			}
 		} else {
 			$this->error=true;
 			$this->error_message=$this->msg["artevod_error_auth"];
 		}
		
		return $this->n_recu;
    }
    
    function progress() {
    	$callback_progress=$this->callback_progress;
		if ($this->n_total) {
			$percent =($this->n_recu / $this->n_total);
			$nlu = $this->n_recu;
			$ntotal = $this->n_total;
		} else {
			$percent=0;
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		}
		call_user_func($callback_progress,$percent,$nlu,$ntotal);
    }
    
    function rec_records($noticesxml, $source_id, $search_id) {
    	global $charset,$base_path;
    	if (!trim($noticesxml))
    		return;
    
    	$rec_uni_dom=new xml_dom($noticesxml,$charset);
    	$notices=$rec_uni_dom->get_nodes("unimarc/notice");
    	foreach ($notices as $anotice) {
    		$this->rec_record($rec_uni_dom, $anotice, $source_id, $search_id);
    	}
    }
    
    function rec_record($rec_uni_dom, $noticenode, $source_id, $search_id) {
    	global $charset,$base_path,$dbh;
    	
    	if (!$rec_uni_dom->error) {
    		//Initialisation
    		$ref="";
    		$ufield="";
    		$usubfield="";
    		$field_order=0;
    		$subfield_order=0;
    		$value="";
    		$date_import=date("Y-m-d H:i:s",time());
    			
    		$fs=$rec_uni_dom->get_nodes("f", $noticenode);
    
    		$fs[] = array("NAME" => "f", "ATTRIBS" => array("c" => "1000"), 'TYPE' => 1, "CHILDS" => array(array("DATA" => $search_term, "TYPE" => 2)));
    		
    		//Pas de 001
    		$ref = md5(serialize($noticenode));
    		//Mise à jour
    		if ($ref) {
    			//Si conservation des anciennes notices, on regarde si elle existe
    			if (!$this->del_old) {
    				$requete="select count(*) from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
    				$rref=pmb_mysql_query($requete,$dbh);
    				if ($rref) $ref_exists=pmb_mysql_result($rref,0,0);
    			}
    			//Si pas de conservation des anciennes notices, on supprime
    			if ($this->del_old) {
    				$requete="delete from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
    				pmb_mysql_query($requete,$dbh);
    			}
    			$ref_exists = false;
    			//Si pas de conservation ou reférence inexistante
    			if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
    				//Insertion de l'entête
    				$n_header["rs"]=$rec_uni_dom->get_value("unimarc/notice/rs");
    				$n_header["ru"]=$rec_uni_dom->get_value("unimarc/notice/ru");
    				$n_header["el"]=$rec_uni_dom->get_value("unimarc/notice/el");
    				$n_header["bl"]=$rec_uni_dom->get_value("unimarc/notice/bl");
    				$n_header["hl"]=$rec_uni_dom->get_value("unimarc/notice/hl");
    				$n_header["dt"]=$rec_uni_dom->get_value("unimarc/notice/dt");
    					
    				//Récupération d'un ID
    				$requete="insert into external_count (recid, source_id) values('".addslashes($this->get_id()." ".$source_id." ".$ref)."', ".$source_id.")";
    				$rid=pmb_mysql_query($requete,$dbh);
    				if ($rid) $recid=pmb_mysql_insert_id();
    					
    				foreach($n_header as $hc=>$code) {
    					$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
						'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
						'".$hc."','',-1,0,'".addslashes($code)."','',$recid, '$search_id')";
    					pmb_mysql_query($requete,$dbh);
    				}
    				if ($fs)
    				for ($i=0; $i<count($fs); $i++) {
    					$ufield=$fs[$i]["ATTRIBS"]["c"];
    					$field_order=$i;
    					$ss=$rec_uni_dom->get_nodes("s",$fs[$i]);
    					if (is_array($ss)) {
    						for ($j=0; $j<count($ss); $j++) {
    							$usubfield=$ss[$j]["ATTRIBS"]["c"];
    							$value=$rec_uni_dom->get_datas($ss[$j]);
    							$subfield_order=$j;
    							$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
								'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
								'".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
								' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
    							pmb_mysql_query($requete,$dbh);
    						}
    					} else {
    						$value=$rec_uni_dom->get_datas($fs[$i]);
    						$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
							'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
							'".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
							' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
    						pmb_mysql_query($requete,$dbh);
    					}
    				}
    			}
    			$this->n_recu++;
    			$this->progress();
    		}
    	}
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
					"code" => "artevod",
					"label" => $this->msg['artevod_vod']
				)
			);		
			$type['source_id'] = $source_id;
		}
		return $type;
	}
	
	function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		global $charset;
		
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
		}
		$enrichment= array();
		switch ($type){
			case "artevod" :
			default :
				$perso_params = new parametres_perso("notices");
				$perso_params->get_values($notice_id);
				$values = $perso_params->values;
				
				$link = "http://www.mediatheque-numerique.com/ws/films/".$values[$vars['cp_field']][0];
				
				$infos = unserialize($this->parameters);
				
				$curl = new Curl();
				if (isset($infos['accesskey']) && $infos['accesskey']) $curl->set_option("CURLOPT_USERPWD", $infos['accesskey'].":".$infos['secretkey']);
				
				$result = $curl->get($link);
				
				$result = _parser_text_no_function_($result->body, "WSOBJECTQUERY");
				
				$content = "";
				$film = array();
				
				// Titre
				$film['title'] = $result['FILM'][0]['EDITORIAL'][0]['TITLE'][0]['value'];
				$film['original_title'] = $result['FILM'][0]['EDITORIAL'][0]['ORIGINAL_TITLE'][0]['value'];
				
				// Genres
				$film['genres'] = array();
				foreach ($result['FILM'][0]['EDITORIAL'][0]['GENRE'] as $genre) {
					foreach ($genre['LABEL'] as $label) {
						if ($label['LANG'] == 'fr') {
							$film['genres'][] = $label['value'];
						}
					}
				}
				
				// Sous-genres
				$film['subgenres'] = array();
				foreach ($result['FILM'][0]['EDITORIAL'][0]['SUB_GENRE'] as $genre) {
					foreach ($genre['LABEL'] as $label) {
						if ($label['LANG'] == 'fr') {
							$film['subgenres'][] = $label['value'];
						}
					}
				}
				
				// Auteurs
				$film['authors'] = array();
				foreach ($result['FILM'][0]['STAFF'][0]['AUTHORS'][0]['PERSON'] as $author) {
					if ($author['FULL_NAME'][0]['value']) {
						$film['authors'][] = $author['FULL_NAME'][0]['value'];
					} else {
						$film['authors'][] = $author['FIRST_NAME'][0]['value']." ".$author['LAST_NAME'][0]['value'];
					}
				}
				
				// Acteurs
				$film['actors'] = array();
				foreach ($result['FILM'][0]['STAFF'][0]['ACTORS'][0]['PERSON'] as $actor) {
					if ($actor['FULL_NAME'][0]['value']) {
						$film['actors'][] = $actor['FULL_NAME'][0]['value'];
					} else {
						$film['actors'][] = $actor['FIRST_NAME'][0]['value']." ".$actor['LAST_NAME'][0]['value'];
					}
				}
				
				// Couverture
				$film['poster'] = $result['FILM'][0]['MEDIA'][0]['POSTERS'][0]['MEDIA'][0]['SRC'];
				
				// Durée
				$film['duration'] = array(
						'raw_value' => $result['FILM'][0]['TECHNICAL'][0]['DURATION'][0]['value'],
						'format_value' => floor($result['FILM'][0]['TECHNICAL'][0]['DURATION'][0]['value']/60).":".str_pad($result['FILM'][0]['TECHNICAL'][0]['DURATION'][0]['value']%60, 2, '0', STR_PAD_LEFT)
				);
				
				// Description
				$film['description'] = $result['FILM'][0]['EDITORIAL'][0]['DESCRIPTION'][0]['value'];
				
				// Résumé
				$film['body'] = $result['FILM'][0]['EDITORIAL'][0]['BODY'][0]['value'];
				
				// Extraits
				$film['trailers'] = array();
				foreach ($result['FILM'][0]['MEDIA'][0]['TRAILERS'][0]['MEDIA'] as $trailer) {
					$film['trailers'][] = $trailer['SRC'];
				}
				
				// Photos
				$film['photos'] = array();
				foreach ($result['FILM'][0]['MEDIA'][0]['PHOTOS'][0]['MEDIA'] as $photo) {
					$film['photos'][] = $photo['SRC'];
				}
				
				// Public
				$film['target_audience'] = $result['FILM'][0]['TECHNICAL'][0]['TARGET_AUDIENCE'][0]['LABEL'][0]['value'];
				
				// Année de production
				$film['production_year'] = $result['FILM'][0]['TECHNICAL'][0]['PRODUCTION_YEAR'][0]['value'];
				
				// Pays de production
				$film['production_countries'] = array();
				foreach ($result['FILM'][0]['TECHNICAL'][0]['PRODUCTION_COUNTRIES'][0]['COUNTRY'] as $country) {
					foreach ($country['LABEL'] as $label) {
						if ($label['LANG'] == 'fr') {
							$film['production_countries'] = $label['value'];
						}
					}
				}
				
				// Langues
				$film['languages'] = array();
				foreach ($result['FILM'][0]['TECHNICAL'][0]['LANGUAGES'][0]['LANGUAGE'] as $language) {
					foreach ($language['LABEL'] as $label) {
						if ($label['LANG'] == 'fr') {
							$film['languages'] = $label['value'];
						}
					}
				}
				
				// Lien externe
				if ($result['FILM'][0]['EXTERNALURI'][0]['value']) {
					$film['externaluri'] = $result['FILM'][0]['EXTERNALURI'][0]['value'];
					if($_SESSION['user_code'] && isset($infos['privatekey'])) {
						global $empr_cb, $empr_nom, $empr_prenom, $empr_mail, $empr_year;
						
						$id_encrypted = hash('sha256', $empr_cb.$infos['privatekey']);
						
						$film['externaluri'] .= "?sso_id=mednum&id=".$empr_cb."&email=".$empr_mail."&nom=".strtolower($empr_nom)."&prenom=".strtolower($empr_prenom)."&dnaiss=".$empr_year."&id_encrypted=".$id_encrypted;
					}
				}
				$enrichment[$type]['content'] = H2o::parseString(stripslashes($vars['enrichment_template']))->render(array("film"=>$film));
				break;
		}		
		$enrichment['source_label'] = $this->msg['artevod_enrichment_source'];
		return $enrichment;
	}
	
	function getEnrichmentHeader($source_id){
		$header= array();
		return $header;
	}
}// class end


