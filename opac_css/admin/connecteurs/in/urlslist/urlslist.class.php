<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: urlslist.class.php,v 1.4 2015-04-03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");


class urlslist extends connector {
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
	
    function urlslist($connector_path="") {
    	parent::connector($connector_path);
    }
    
    function get_id() {
    	return "urlslist";
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
    	global $charset,$dbh;
  		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
    	$form ="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='libelle'>".$this->msg["urlslist_libelle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='libelle' value='".htmlentities($libelle,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='infobulle'>".$this->msg["urlslist_infobulle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='infobulle' value='".htmlentities($infobulle,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["urlslist_source_name"]."</label></div>
				<div class='colonne-suite'><input type='text' name='source_name' value='".htmlentities($source_name,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='width'>".$this->msg["urlslist_width"]."</label></div>
				<div class='colonne-suite'><input type='text' name='width' value='".htmlentities($width,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='height'>".$this->msg["urlslist_height"]."</label></div>
				<div class='colonne-suite'><input type='text' name='height' value='".htmlentities($height,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["urlslist_source_field"]."</label></div>
				<div class='colonne-suite'>
					<select name='cp_field'>";
    	$query = "select idchamp, titre from notices_custom where type='url'";
    	$result = pmb_mysql_query($query,$dbh);
    	if(pmb_mysql_num_rows($result)){
    		while($row = pmb_mysql_fetch_object($result)){
    			$form.="
    					<option value='".$row->idchamp."' ".($row->idchamp == $cp_field ? "selected='selected'" : "").">".htmlentities($row->titre,ENT_QUOTES,$charset)."</option>";
    		}
    	}else{
    		$form.="
    					<option value='0'>".$this->msg["urlslist_no_field"]."</option>";
    	}
    	$form.="
    				</select>
				</div>
			</div>
			<div class='row'>&nbsp;</div>";
    	
		return $form;
    }
    
    function make_serialized_source_properties($source_id) {
    	global $libelle,$infobulle,$source_name;
    	global $cp_field;
    	global $width,$height;
    	$t=array();
    	$t['libelle'] = $libelle;
    	$t['infobulle'] = $infobulle;
    	$t['source_name'] = $source_name;
    	$t['cp_field'] = $cp_field;
    	$t['width'] = $width;
    	$t['height'] = $height;
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
		return "";
	}
    
    function make_serialized_properties() {
		$this->parameters = serialize(array());
	}

	function enrichment_is_allow(){
		return true;
	}
	
	function getEnrichmentHeader($source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		$header= array();
		$header[] ="
		<script type='text/javascript'>
					function load_urlslist(url,parent){
						var frame = document.getElementById(parent+'_frame');
						if(!frame){
							var div = document.createElement('div');
							div.setAttribute('id',parent+'_frame');
							var iframe = document.createElement('iframe');
							iframe.setAttribute('src',url);
							iframe.setAttribute('style','margin:5px;width:"."$width"."px;height:".$height."px;');
							div.appendChild(iframe);
							document.getElementById(parent).appendChild(div);
						}else{
							document.getElementById(parent).removeChild(frame);
						}
					}
				</script>
		";
		return $header;
	}
	
	function getTypeOfEnrichment($notice_id,$source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		$query = "select 1 from notices_custom_values where notices_custom_champ = ".$cp_field." and notices_custom_origine = ".$notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$type['type'] = array(
				array(
					'code' => str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle))),
					'label' => $libelle,
					'infobulle' => $infobulle
				) 
			);
			$type['source_id'] = $source_id;
		}
		return $type;
	}
	
	function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
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
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle))) :
			default :
				$enrichment[str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle)))]['content'] = $this->urlsInfos($notice_id,$source_id);
				break;
		}		
		$enrichment['source_label']= sprintf($this->msg['urlslist_enrichment_source'],$source_name);
		return $enrichment;
	}
	
	
	
	function urlsInfos($notice_id,$source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global $$key;
				$$key=$val;
			}	
		}
		if($cp_field){
			$query = "select datatype from notices_custom where idchamp = ".$cp_field;
			$datatype = pmb_mysql_result(pmb_mysql_query($query),0,0);
			$query = "select notices_custom_".$datatype." from notices_custom_values where notices_custom_champ = ".$cp_field." and notices_custom_origine= ".$notice_id;
			//return $query;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$html_to_return = "
				<table id='enrichment_urlslist_".$source_id."' class='enrichment_urlslist'>";
				$i=0;
				while($row = pmb_mysql_fetch_row($result)){
					$tab = explode("|",$row[0]);
						$html_to_return.= "
					<tr style='margin:2px;'>
						<td id='urlslist_".$source_id."_".$i."'>
							<a href='#' onclick='load_urlslist(\"".$tab[0]."\",\"urlslist_".$source_id."_".$i."\");return false' >".$tab[1]."</a>
						</td>
					</tr>";
						$i++;
				}
				$html_to_return.= "
				</table>
				";
			}else{
				$html_to_return.= $this->msg['urlslist_no_informations'];
			}
		}else{
			$html_to_return = $this->msg['urlslist_no_informations'];
		}
		return $html_to_return;
	}
}
?>