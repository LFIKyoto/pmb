<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emploi.class.php,v 1.6.2.2 2015-09-15 14:32:56 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");
//
// Scandir for PHP4
//
if(!function_exists('scandir'))
{
    function scandir($dir, $sortorder = 0)
    {
        if(is_dir($dir))
        {
            $dirlist = opendir($dir);
           
            while( ($file = readdir($dirlist)) !== false)
            {
                if(!is_dir($file))
                {
                    $files[] = $file;
                }
            }
           
            ($sortorder == 0) ? asort($files) : arsort($files);
           
            return $files;
        }
        else
        {
        return FALSE;
        break;
        }
    }
}

class emploi extends connector {
	//Variables internes pour la progression de la récupération des notices
	var $callback_progress;		//Nom de la fonction de callback progression passée par l'appellant
	var $current_set;			//Set en cours de synchronisation
	var $total_sets;			//Nombre total de sets sélectionnés
	var $metadata_prefix;		//Préfixe du format de données courant
	var $source_id;				//Numéro de la source en cours de synchro
	var $n_recu;				//Nombre de notices reçues
	var $xslt_transform;		//Feuille xslt transmise
	var $sets_names;			//Nom des sets pour faire plus joli !!
	var $del_old;				//Supression ou non des notices déjà existantes
	var $schema_config;
	
	//Résultat de la synchro
	var $error;					//Y-a-t-il eu une erreur	
	var $error_message;			//Si oui, message correspondant
	
    function emploi($connector_path="") {
    	parent::connector($connector_path);
    }
    
    function get_id() {
    	return "emploi";
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
			$url = "http://travail-emploi.gouv.fr/etudes-recherche-statistiques-de,76/etudes-et-recherche,77/publications-dares,98/dares-analyses-dares-indicateurs,102";
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["emploi_url"]."</label>
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
		$this->ttl=60000;
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
			$this->error_message = $this->msg["emploi_unconfigured"];
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

		//$addr="http://travail-emploi.gouv.fr/etudes-recherche-statistiques-de,76/etudes-et-recherche,77/publications-dares,98/dares-analyses-dares-indicateurs,102/";
		
		$addr=$url;
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $addr);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
		configurer_proxy_curl($ch,$addr);	
	 	$html=curl_exec($ch);	 	
 		if (!$html) {
 			$sortir=1; 			
 		} else {
 			if ($charset=="utf-8")  $html=utf8_encode($html);
 		//	if (strtoupper($charset)!="UTF-8") $html=utf8_decode($html);	 	 		 		
	 		
	 		$notice_list=$this->get_field_betwen_2sep($html,"<h2 class=\"smaller\">","</ul>");
	 		
	 		$notice_list=$this->get_field_betwen_2sep($notice_list,"<ul>","<ul>");
	 		
	 		$sep_notices="</li>\n";
	 		$notices_html=explode($sep_notices,$notice_list);
	 		$nb=0;
	 		//print printr($notices_html);
	 		foreach($notices_html as $notice_html){
	 			$data_notice=array();
		 				 			
		 		$link=$this->get_field_betwen_2sep($notice_html,"<a href=\"","\">");			 		
		 		$link=	"http://travail-emploi.gouv.fr/".$link;

		 		curl_setopt($ch, CURLOPT_URL, $link);	
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
				configurer_proxy_curl($ch,$link);	
 				$html_notice_complete=curl_exec($ch);	 	
		 		
	//	 		http://travail-emploi.gouv.fr/IMG/pdf/2012-035.pdf
		 		if (!$html_notice_complete) {
		 			continue;
		 		}
		 		if ($charset=="utf-8")  $html_notice_complete=utf8_encode($html_notice_complete);
		 		$data_notice["Url_notice"]=$link;
		 		
		 		$html_notice_complete=$this->get_field_from_sep($html_notice_complete,"<div class=\"gris clearfix\">");
		 		 
			 	$data_notice["Publication_Date"]=$this->get_field_betwen_2sep($html_notice_complete,"<span class=\"date\">","</span>");
			 	$data_notice["Title"]=$this->get_field_betwen_2sep($html_notice_complete,"<h1>","</h1>");
			 	
		 		$data_notice["Abstract"]=$this->get_field_betwen_2sep($html_notice_complete,"<div class=\"texteencadre-spip spip\">","<div class=\"listdoc\">",1,1);	
			 	$data_notice["Abstract"]=str_replace("&#8217;","'",$data_notice["Abstract"]);
				$zone_docnum=$this->get_field_from_sep($html_notice_complete," spip_documents spip_lien_ok\">");
		 		$link_docnum=$this->get_field_betwen_2sep($zone_docnum,"<a href=\"","\" ");
		 		if($link_docnum){
		 			$data_notice["doc_links"][0]["link"]="http://travail-emploi.gouv.fr/".$link_docnum;		
			 		$data_notice["id"]=$this->get_field_betwen_2sep($link_docnum,"IMG/pdf/",".pdf");
		 		}		 		
	 		//	printr ($data_notice);
	 			if($this->rec_record($this->notice_2_uni($data_notice),$source_id)){
	 				// notice déjà en entrepot, on ne va pas chercher les suivantes
	 				break;
	 			}
	 			
 			}	
 		}	 			
 		curl_close($ch);	
		return $this->n_recu;
	}
	
    function parse_xml($ch,$data) {
		$notices=explode("6",$data);
		print $notices[1];  
    	return strlen($data);
	}
	
	function search($source_id,$query,$search_id) {
	}	
	
	function notice_2_uni($nt) {

		$unimarc=array();
		$unimarc["001"][0]=$nt["id"];

		$unimarc["200"][0]["a"][0]=$nt["Title"];
		
		//Editeurs
		if ($nt["Publication"]) $unimarc["210"][0]["c"][0]=$nt["Publication"];
		if ($nt["Publication_Date"]) $unimarc["210"][0]["d"][]=$nt["Publication_Date"];
		
		// DOI
		if ($nt["DOI"]){
			$unimarc["014"][0]["a"][0]=$nt["DOI"];
			$unimarc["014"][0]["b"][0]="DOI";
		} 
		
		if ($nt["Affiliation"])  $unimarc["300"][0]["a"][0]=$nt["Affiliation"];
		
		//Résumé
		if ($nt["Abstract"])  $unimarc["330"][0]["a"][0]=$nt["Abstract"];
				
		if (count($nt["numberofpages"])) $unimarc["215"][0]["a"][]=$nt["numberofpages"];
		
		//Auteurs
		$aut=array();
		if (count($nt["Authors"])) {			
			if (count($nt["Authors"])>1) $autf="701"; else $autf="700";
			for ($i=0; $i<count($nt["Authors"]); $i++) {
					
				$name_surname=explode(",",$nt["Authors"][$i]);	
				if(count($name_surname)==2) {
					$aut[$i]["a"][0]=$name_surname[0];
					$aut[$i]["b"][0]=$name_surname[1];
				} else{					
					$aut[$i]["a"][0]=$nt["Authors"][$i];
				} 
			//	$aut[$i]["4"][0]="070";
			}
			$unimarc[$autf]=$aut;			
		}
		if ($nt["Url_notice"]){
			$unimarc["856"][0]["u"][0]=$nt["Url_notice"];
		} 
		
		// Link doc num
		if(count($nt["doc_links"])) {
			$i=0;
			foreach($nt["doc_links"] as $key =>$val){
				$unimarc["897"][$i]["a"][0]=$val["link"];
				if($val["label"])
					$unimarc["897"][$i]["b"][0]=$val["label"];
				else
					$unimarc["897"][$i]["b"][0]=$key;
				$i++;
			}
		}
		// Keywords
		if ($nt["Keywords"])  $unimarc["610"][0]["a"][0]=$nt["Keywords"];			
		// Origin
		if ($nt["Origin"])  $unimarc["801"][0]["b"][0]=$nt["Origin"];
				
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
				$this->delete_from_external_count($source_id, $ref);
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
	

function get_field_from_sep($chaine, $deb,$html_decode=0,$keep_tags=""){
	global $charset;
	$i_deb=strpos($chaine,$deb);
	if ($i_deb === false) return "";
	$i_deb+=strlen($deb);
	if($html_decode){
		//return html_entity_decode(substr($chaine,$i_deb),ENT_QUOTES,$charset);	
		return html_entity_decode(strip_tags(substr($chaine,$i_deb),$keep_tags),ENT_QUOTES,$charset); 
	}else
		return substr($chaine,$i_deb);	
	
}
function get_field_betwen_2sep($chaine, $deb,$end,$html_decode=0,$keep_tags=""){
	global $charset;
	$i_deb=strpos($chaine,$deb);
	if ($i_deb === false) return "";
	$i_deb+=strlen($deb);
	$chaine_deb=substr($chaine,$i_deb);
	$i_end=strpos($chaine_deb,$end);
	if ($i_end === false) return "";
	if($html_decode){
		// return html_entity_decode(substr($chaine_deb,0,$i_end),ENT_QUOTES,$charset);
		return html_entity_decode(strip_tags(substr($chaine_deb,0,$i_end),$keep_tags),ENT_QUOTES,$charset); 
	}else
		return substr($chaine_deb,0,$i_end);	
}
}// class end


