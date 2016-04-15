<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs.class.php,v 1.36.4.1 2015-09-15 14:32:56 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/parser.inc.php");
require_once($include_path."/templates/connecteurs.tpl.php");
require_once($class_path."/upload_folder.class.php");

class connector {
	var $repository;				//Est-ce un entrepot ?
	var $timeout;					//Time-out
	var $retry;						//Nombre de réessais
	var $ttl;						//Time to live
	var $parameters;				//Paramètres propres au connecteur
	var $sources;					//Sources disponibles
	var $msg;						//Messages propres au connecteur
	var $connector_path;
	
	function connector($connector_path="") {
		$this->fetch_global_properties();
		$this->get_messages($connector_path);
		$this->connector_path=$connector_path;
	}
	
	//Signature de la classe
	function get_id() {
		return "";
	}
	
	//Est-ce un entrepot ?
	function is_repository() {
		return 0;
	}
	
	function get_messages($connector_path) {
		global $lang;
		
		if (file_exists($connector_path."/messages/".$lang.".xml")) {
			$file_name=$connector_path."/messages/".$lang.".xml";
		} else if (file_exists($connector_path."/messages/fr_FR.xml")) {
			$file_name=$connector_path."/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$this->msg=$xmllist->table;
		}
	}
	
	//Récupération de la liste des sources
	function get_sources() {
		$sources=array();
		$requete="SELECT connectors_sources.*, source_sync.cancel, source_sync.percent, source_sync.date_sync FROM connectors_sources LEFT JOIN source_sync ON ( connectors_sources.source_id = source_sync.source_id ) where id_connector='".addslashes($this->get_id())."' order by connectors_sources.name";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$s["SOURCE_ID"]=$r->source_id;
				$s["PARAMETERS"]=$r->parameters;
				$s["NAME"]=$r->name;
				$s["COMMENT"]=$r->comment;
				$s["RETRY"]=$r->retry;
				$s["REPOSITORY"]=$r->repository;
				$s["TTL"]=$r->ttl;
				$s["TIMEOUT"]=$r->timeout;
				$s["OPAC_ALLOWED"]=$r->opac_allowed;
				$s["UPLOAD_DOC_NUM"]=$r->upload_doc_num;
				$s["REP_UPLOAD"] = $r->rep_upload;
				$s["ENRICHMENT"] = $r->enrichment;
				$s["OPAC_AFFILIATE_SEARCH"] = $r->opac_affiliate_search;
				$s["OPAC_SELECTED"] = $r->opac_selected;
				$s["TYPE_ENRICHEMENT_ALLOWED"]=unserialize($r->type_enrichment_allowed);
				$s["CANCELLED"]=$r->cancel;
				$s["PERCENT"]=$r->percent;
				$s["DATESYNC"]=$r->date_sync;
				$s["LASTSYNCDATE"]=$r->last_sync_date;
				$s["ICO_NOITICE"]=$r->ico_notice;
				$sources[$r->source_id]=$s;
			}
		}
		$this->sources=$sources;
		return $sources;
	}
	
	//Récupération des paramètres d'une source
	function get_source_params($source_id) {
		if ($source_id) {
			$requete="select * from connectors_sources where id_connector='".addslashes($this->get_id())."' and source_id=".$source_id."";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$s["SOURCE_ID"]=$r->source_id;
				$s["PARAMETERS"]=$r->parameters;
				$s["NAME"]=$r->name;
				$s["COMMENT"]=$r->comment;
				$s["RETRY"]=$r->retry;
				$s["REPOSITORY"]=$r->repository;
				$s["TTL"]=$r->ttl;
				$s["TIMEOUT"]=$r->timeout;
				$s["OPAC_ALLOWED"]=$r->opac_allowed;
				$s["UPLOAD_DOC_NUM"]=$r->upload_doc_num;
				$s["REP_UPLOAD"] = $r->rep_upload;
				$s["ENRICHMENT"] = $r->enrichment;
				$s["OPAC_AFFILIATE_SEARCH"] = $r->opac_affiliate_search;
				$s["OPAC_SELECTED"]=$r->opac_selected;
				if($r->type_enrichment_allowed == ""){
					$s["TYPE_ENRICHMENT_ALLOWED"] = array();
				}else{
					$s["TYPE_ENRICHMENT_ALLOWED"]=unserialize($r->type_enrichment_allowed);
				}
				$s["ICO_NOITICE"]=$r->ico_notice;
			} 
		} else {
			$s["SOURCE_ID"]="";
			$s["PARAMETERS"]="";
			$s["NAME"]="Nouvelle source";
			$s["COMMENT"]="";
			$s["RETRY"]=$this->retry;
			$s["REPOSITORY"]=$this->repository;
			$s["TTL"]=$this->ttl;
			$s["TIMEOUT"]=$this->timeout;
			$s["OPAC_ALLOWED"]=0;
			$s["UPLOAD_DOC_NUM"]=1;
			$s["REP_UPLOAD"] = 0;
			$s["ENRICHMENT"] = 0;
			$s["OPAC_AFFILIATE_SEARCH"] = 0;
			$s["OPAC_SELECTED"]=0;
			$s["ICO_NOITICE"]="";
			$s["TYPE_ENRICHMENT_ALLOWED"]=array();
		}
		//Gestion du timeout au niveau de mysql pour ne pas perdre la connection
		if($s["TIMEOUT"]){
			$res=pmb_mysql_query("SHOW SESSION VARIABLES like 'wait_timeout'");
			$timeout_default=0;
			if($res && pmb_mysql_num_rows($res)){
				$timeout_default=pmb_mysql_result($res,0,1);
			}
			pmb_mysql_query("SET SESSION wait_timeout=".($timeout_default+(($s["TIMEOUT"])*1)));
		}
		return $s;
	}
	
	//Formulaire des propriétés d'une source
	function source_get_property_form($source_id) {
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]	
		} else {
			//Affichage du formulaire vide
		}
	}
	
	function make_serialized_source_properties($source_id) {
		$this->sources[$source_id]["PARAMETERS"]="";
	}
	
	//Formulaire de sauvegarde des propriétés d'une source
	function source_save_property_form($source_id) {
		global $source_categories;
		$this->make_serialized_source_properties($source_id);
		$this->sources[$source_id]["OPAC_ALLOWED"] = $this->sources[$source_id]["OPAC_ALLOWED"] ? 1 : 0;
		$this->sources[$source_id]["UPLOAD_DOC_NUM"] = $this->sources[$source_id]["UPLOAD_DOC_NUM"] ? 1 : 0;
		$this->sources[$source_id]["ENRICHMENT"] = $this->sources[$source_id]["ENRICHMENT"] ? 1 : 0;
		$this->sources[$source_id]["OPAC_AFFILIATE_SEARCH"] = $this->sources[$source_id]["OPAC_AFFILIATE_SEARCH"] ? 1 : 0;
		$this->sources[$source_id]["OPAC_SELECTED"] = $this->sources[$source_id]["OPAC_SELECTED"] ? 1 : 0;
		if(!is_array($this->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"])){
			$this->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"]=array();
		}
		$this->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"] = serialize($this->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"]);
		if(is_array($this->sources[$source_id]["PARAMETERS"])){
			$this->sources[$source_id]["PARAMETERS"]=serialize($this->sources[$source_id]["PARAMETERS"]);
		}
		$requete="replace into connectors_sources (source_id,id_connector,parameters,comment,name,repository,retry,ttl,timeout,opac_allowed,upload_doc_num,rep_upload,enrichment,opac_affiliate_search,opac_selected,type_enrichment_allowed,ico_notice) 
			values('".$source_id."','".addslashes($this->get_id())."','".addslashes($this->sources[$source_id]["PARAMETERS"])."','".addslashes($this->sources[$source_id]["COMMENT"])."','".addslashes($this->sources[$source_id]["NAME"])."','".addslashes($this->sources[$source_id]["REPOSITORY"])."','".addslashes($this->sources[$source_id]["RETRY"])."','".addslashes($this->sources[$source_id]["TTL"])."','".addslashes($this->sources[$source_id]["TIMEOUT"])."','".addslashes($this->sources[$source_id]["OPAC_ALLOWED"])."','".addslashes($this->sources[$source_id]["UPLOAD_DOC_NUM"])."','".addslashes($this->sources[$source_id]["REP_UPLOAD"])."','".addslashes($this->sources[$source_id]["ENRICHMENT"])."','".addslashes($this->sources[$source_id]["OPAC_AFFILIATE_SEARCH"])."','".addslashes($this->sources[$source_id]["OPAC_SELECTED"])."','".addslashes($this->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"])."','".addslashes($this->sources[$source_id]["ICO_NOTICE"])."')";
		$result = pmb_mysql_query($requete);
		if (!$source_id) $source_id = pmb_mysql_insert_id(); 

		$table_entrepot_sql = "CREATE TABLE IF NOT EXISTS `entrepot_source_".$source_id."` (
							  `connector_id` varchar(20) NOT NULL default '',
							  `source_id` int(11) unsigned NOT NULL default '0',
							  `ref` varchar(220) NOT NULL default '',
							  `date_import` datetime NOT NULL default '0000-00-00 00:00:00',
							  `ufield` char(3) NOT NULL default '',
							  `field_ind` char(2) NOT NULL default '  ',
							  `usubfield` char(1) NOT NULL default '',
							  `field_order` int(10) unsigned NOT NULL default '0',
							  `subfield_order` int(10) unsigned NOT NULL default '0',
							  `value` text NOT NULL,
							  `i_value` text NOT NULL,
							  `recid` bigint(20) unsigned NOT NULL default '0',
							  `search_id` varchar(32) NOT NULL default '',
							  PRIMARY KEY  (`connector_id`,`source_id`,`ref`,`ufield`,`usubfield`,`field_order`,`subfield_order`,`search_id`),
							  KEY `usubfield` (`usubfield`),
							  KEY `ufield_2` (`ufield`,`usubfield`),
							  KEY `recid_2` (`recid`,`ufield`,`usubfield`),
							  KEY `source_id` (`source_id`),
							  KEY `i_recid_source_id` (`recid`,`source_id`),
							  KEY `i_ref` (`ref`)
							)";
		pmb_mysql_query($table_entrepot_sql);
		
		//Mise à jour des catégories
		$sql = "DELETE FROM connectors_categ_sources WHERE num_source = ".$source_id;
		pmb_mysql_query($sql);
		if ($source_categories) {
			$values = array();
			foreach($source_categories as $acateg_id) {
				if (!$acateg_id) 
					continue;
				$values[] = "(".addslashes($acateg_id).", ".addslashes($source_id).")";
			}
			$values = implode(",", $values);
			if ($values) {
				$sql = "INSERT INTO `connectors_categ_sources` (`num_categ`, `num_source`) VALUES ".$values;
				pmb_mysql_query($sql) or die (pmb_mysql_error());					
			}
		}		
		return $result;
	}
	
	//Suppression d'une source
	function del_source($source_id) {
		//suppression des documents numériques intégrés en tant que fichiers
		$this->del_explnums($source_id);
		$table_entrepot_sql = "DROP TABLE `entrepot_source_$source_id`;";
		pmb_mysql_query($table_entrepot_sql);
		
		$requete="delete from connectors_sources where source_id=$source_id and id_connector='".addslashes($this->get_id())."'";
		return pmb_mysql_query($requete);
	}
	
	//Récupération  des propriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	function fetch_default_global_values() {
		$this->timeout=5;
		$this->repository=2;
		$this->retry=3;
		$this->ttl=1800;
		$this->parameters="";
	}
	
	//Récupération  des propriétés globales du connecteur (timeout, retry, repository, parameters)
	function fetch_global_properties() {
		$requete="select * from connectors where connector_id='".addslashes($this->get_id())."'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$this->repository=$r->repository;
			$this->timeout=$r->timeout;
			$this->retry=$r->retry;
			$this->ttl=$r->ttl;
			$this->parameters=$r->parameters;
		} else {
			$this->fetch_default_global_values();
		}
	}
	
	//Formulaire des propriétés générales
	function get_property_form() {
		$this->fetch_global_properties();
		//Affichage du formulaire en fonction de $this->parameters
		if ($this->parameters) {
		} else {
			//Affichage du formulaire vide
		}	
	}
	
	function make_serialized_properties() {
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
	}
	
	//Sauvegarde des propriétés générales
	function save_property_form() {
		$this->make_serialized_properties();
		$requete="replace into connectors (connector_id,parameters, retry, timeout, ttl, repository) values('".addslashes($this->get_id())."',
		'".addslashes($this->parameters)."','".$this->retry."','".$this->timeout."','".$this->ttl."','".$this->repository."')";
		return pmb_mysql_query($requete);
	}
	
	//Supression des notices dans l'entrepot !
	function del_notices($source_id) {
		$requete="select * from source_sync where source_id=".$source_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			if (!$r->cancel) return false;
		}
		//suppression des documents numériques intégrés en tant que fichiers
		$this->del_explnums($source_id);
		
		pmb_mysql_query("TRUNCATE TABLE entrepot_source_".$source_id);
		pmb_mysql_query("delete from source_sync where source_id=".$source_id);
		return true;
	}
	
	//Suppression des documents numériques intégrés en tant que fichiers
	function del_explnums($source_id) {
		global $dbh;
		$q = "select value as file_name from entrepot_source_$source_id where ufield='897' and usubfield='a' and value like '/%' ";
		$r = pmb_mysql_query($q,$dbh);
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				@unlink($row->file_name);
			}
		}
	}
	
	//Annulation de la mise a jour (faux = synchro conservee dans la table, vrai = synchro supprimee dans la table)
	function cancel_maj($source_id) {
		return false;
	}
	
	//Annulation de la mise a jour (faux = synchro conservee dans la table, vrai = synchro supprimee dans la table)
	function break_maj($source_id) {
		return false;
	}
	
	//Formulaire complementaire facultatif pour la synchronisation
	function form_pour_maj_entrepot($source_id) {
		return false;
	}

	//Nécessaire pour passer les valeurs obtenues dans form_pour_maj_entrepot au javascript asynchrone
	function get_maj_environnement($source_id) {
		return false;
	}
	
	//M.A.J. Entrepot lie a une source
	function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
	}
	
	//Export d'une notice en UNIMARC
	function to_unimarc($notice) {
	}
	
	//Export d'une notice en Dublin Core (c'est le minimum)
	function to_dublin_core($notice) {
	}
	
	//Fonction de recherche
	function search($source_id,$query,$search_id) {
	}
	
	//Recherche d'une page de resultat
	function get_page_result($search_id,$page, $n_per_page) {
	}
	
	//Nombre de résultats d'une recherche
	function get_n_results($search_id) {
	}
	
	//Recuperation de la valeur d'une autorite
	function get_values_from_id($id,$ufield) {
		$r="";
		switch ($ufield) {
			//Categorie
			case "60X":
				$requete="select libelle_categorie from categories where num_noeud=".$id;
				$r_cat=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_cat)) {
					$r=pmb_mysql_result($r_cat,0,0);
				}
				break;
			//Dewey
			case "676\$a686\$a":
				$requete="select indexint_name from indexint where indexint_id=".$id;
				$r_indexint=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_indexint)) {
					$r=pmb_mysql_result($r_indexint,0,0);
				}
				break;
			//Editeur
			case "210\$c":
				$requete="select ed_name from publishers where ed_id=".$id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$r=pmb_mysql_result($r_pub,0,0);
				}
				break;
			//Collection
			case "225\$a410\$t":
				$requete="select collection_name from collections where collection_id=".$id;
				$r_coll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_coll)) {
					$r=pmb_mysql_result($r_coll,0,0);
				}
				break;
			//Sous collection
			case "225\$i411\$t":
				$requete="select sub_coll_name from sub_collections where sub_coll_id=".$id;
				$r_subcoll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_subcoll)) {
					$r=pmb_mysql_result($r_subcoll,0,0);
				}
				break;
			//Auteur
			case "7XX":
				$requete="select concat(author_name,', ',author_rejete) from authors where author_id=".$id;
				$r_author=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_author)) {
					$r=pmb_mysql_result($r_author,0,0);
				}
				break;
		}
		return $r;
	}
	
	function get_unimarc_search_fields() {
    	$fields=array();
    	//Calcul de la liste des champs disponibles
		$sc=new search(false,"search_fields_unimarc");
		$lf=$sc->get_unimarc_fields();
		$sc=new search(false,"search_simple_fields_unimarc");
		$lfs=$sc->get_unimarc_fields();
		//On fusionne les deux listes
		foreach($lf as $ufield=>$values) {
			if (substr($ufield,0,3)=="id:") {
				$ufield=substr($ufield,3);
			}
			$fields[$ufield]["TITLE"]=$values["TITLE"];
			foreach($values["OPERATORS"] as $op=>$top) {
				$fields[$ufield]["OPERATORS"][$op]=$top;
			}
		}
		foreach($lfs as $ufield=>$values) {
			if (substr($ufield,0,3)=="id:") {
				$ufield=substr($ufield,3);
			}
			if (!$fields[$ufield]["TITLE"])
				$fields[$ufield]["TITLE"]=$values["TITLE"];
			else {
				foreach($values["TITLE"] as $key=>$title) {
					if (array_search($title,$fields[$ufield]["TITLE"])===false) {
						$fields[$ufield]["TITLE"][]=$title;
					}
				}
			}
			foreach($values["OPERATORS"] as $op=>$top) {
				$fields[$ufield]["OPERATORS"][$op]=$top;
			}
		}
		return $fields;
    }
    
    function enrichment_is_allow(){
		return false;
	}
	
	function rec_records_from_xml_array($records=array(),$source_id=0) {

		global $dbh;
		
		if (is_array($records) && count($records) && $source_id*1) {
			
			$this->source_id=$source_id;
			
			foreach($records as $k=>$rec) {
				
				//Initialisation
				$ref='';
				$ufield='';
				$usubfield='';
				$field_order=0;
				$subfield_order=0;
				$value='';
				$date_import=today();
				
				$ref=$rec['f'][0]['value'];		
				$ref.=(($ref)?'-':'').md5(microtime());
				$n_header['rs']=$rec['rs']['value'];
				$n_header['ru']=$rec['ru']['value'];
				$n_header['el']=$rec['el']['value'];
				$n_header['bl']=$rec['bl']['value'];
				$n_header['hl']=$rec['hl']['value'];
				$n_header['dt']=$rec['dt']['value'];
				
				//suppression des anciennes notices
				$q="delete from external_count where recid='".addslashes($this->get_id().' '.$this->source_id.' '.$ref)."' and source_id='".$this->source_id."' ";
				pmb_mysql_query($q,$dbh);
				$q="delete from entrepot_source_".$this->source_id." where ref='".addslashes($ref)."'";
				pmb_mysql_query($q,$dbh);
				
				//Récupération d'un ID
				$q="insert into external_count (recid, source_id) values('".addslashes($this->get_id()." ".$this->source_id." ".$ref)."', ".$this->source_id.")";
				$rid=pmb_mysql_query($q,$dbh);
				if ($rid) $recid=pmb_mysql_insert_id();
				
				
				foreach($n_header as $hc=>$code) {
					$q="insert into entrepot_source_".$this->source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid) values(
					'".addslashes($this->get_id())."',".$this->source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
					'".$hc."','',-1,0,'".addslashes($code)."','',$recid)";
					pmb_mysql_query($q,$dbh);
				}
			
				for ($i=0; $i<count($rec['f']); $i++) {
					$ufield=$rec['f'][$i]['c'];
					$field_order=$i;
					$field_ind=$rec['f'][$i]['ind'];
					$ss=$rec['f'][$i]['s'];
					if (is_array($ss)) {
						for ($j=0; $j<count($ss); $j++) {
							$usubfield=$ss[$j]['c'];
							$value=$ss[$j]['value'];
							$subfield_order=$j;
							$q="insert into entrepot_source_".$this->source_id." (connector_id,source_id,ref,date_import,ufield,field_ind,usubfield,field_order,subfield_order,value,i_value,recid) values(
							'".addslashes($this->get_id())."',".$this->source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
							'".addslashes($ufield)."','".addslashes($field_ind)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
							' ".addslashes(strip_empty_words($value))." ',$recid)";
							pmb_mysql_query($q,$dbh);
						}
					}
				}
			}
		}
	}
	
	protected function delete_from_external_count($source_id, $ref) {
		global $dbh;
		
		$requete="delete from external_count where recid='".addslashes($this->get_id()." ".$source_id." ".$ref)."' and source_id = ".$source_id;
		pmb_mysql_query($requete, $dbh);
	}
} 

class connecteurs {
	
	var $catalog=array();			//Liste des connecteurs declares
	
	//Constructeur
	function connecteurs() {
		global $base_path;
		if (file_exists($base_path."/admin/connecteurs/in/catalog_subst.xml")) 
			$catalog=$base_path."/admin/connecteurs/in/catalog_subst.xml";
		else
			$catalog=$base_path."/admin/connecteurs/in/catalog.xml";
		$this->parse_catalog($catalog);
	}
	
	static function get_class_name($source_id) {
		$connector_id="";
		$requete="select id_connector from connectors_sources where source_id=".$source_id;
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			$connector_id=pmb_mysql_result($resultat,0,0);
		}
		return $connector_id;
	}
	
	function parse_catalog($catalog) {
		global $base_path,$lang;
		//Construction du tableau des connecteurs disponbibles
		$xml=file_get_contents($catalog);
		$param=_parser_text_no_function_($xml,"CATALOG");
		for ($i=0; $i<count($param["ITEM"]); $i++) {
			$item=$param["ITEM"][$i];
			$t=array();
			$t["PATH"]=$item["PATH"];
			//Parse du manifest du connecteur!
			$xml_manifest=file_get_contents($base_path."/admin/connecteurs/in/".$item["PATH"]."/manifest.xml");
			$manifest=_parser_text_no_function_($xml_manifest,"MANIFEST");
			$t["NAME"]=$manifest["NAME"][0]["value"];
			$t["AUTHOR"]=$manifest["AUTHOR"][0]["value"];
			$t["ORG"]=$manifest["ORG"][0]["value"];
			$t["DATE"]=$manifest["DATE"][0]["value"];
			$t["STATUS"]=$manifest["STATUS"][0]["value"];
			$t["URL"]=$manifest["URL"][0]["value"];
			$t["REPOSITORY"]=$manifest["REPOSITORY"][0]["value"];
			$t["ENRICHMENT"]=$manifest["ENRICHMENT"][0]["value"];
			//Commentaires
			$comment=array();
			for ($j=0; $j<count($manifest["COMMENT"]); $j++) {
				if ($manifest["COMMENT"][$j]["lang"]==$lang) { 
					$comment=$manifest["COMMENT"][$j]["value"];
					break;
				} else if (!$manifest["COMMENT"][$j]["lang"]) {
					$c_default=$manifest["COMMENT"][$j]["value"];	
				}
			}
			if ($j==count($manifest["COMMENT"])) $comment=$c_default;
			$t["COMMENT"]=$comment;
			
			//enrichissement
			if($manifest["ENRICHMENTS"]){
				$t["ENRICHMENTS"]=array();
				foreach($manifest["ENRICHMENTS"][0]["TYPE"] as $type){
					$t["ENRICHMENTS"][$type["NAME"]] = $type["value"];
				}
			}
			
			$this->catalog[$item["ID"]]=$t;
		}
	}	
	
	function show_connector_form($id) {
		global $base_path,$charset,$admin_connecteur_global_params,$lang,$msg;
		//Inclusion de la classe
		require_once($base_path."/admin/connecteurs/in/".$this->catalog[$id]["PATH"]."/".$this->catalog[$id]["NAME"].".class.php");
		eval("\$conn=new ".$this->catalog[$id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$this->catalog[$id]["PATH"]."\");");
		$connector_form=$conn->get_property_form();
		$connector_form=str_replace("!!special_form!!",$connector_form,$admin_connecteur_global_params);
		//Remplacement des valeurs par défaut
		$connector_form=str_replace("!!id!!",$id,$connector_form);
		$connector_form=str_replace("!!connecteur!!",htmlentities($this->catalog[$id]["COMMENT"],ENT_QUOTES,$charset),$connector_form);
		switch ($conn->is_repository()) {
			//Oui
			case 1:
				$connector_form=str_replace("!!repository!!","<input type='hidden' value='1' name='repository' id='repository'/>".$msg["connecteurs_yes"],$connector_form);
				break;
			//Non
			case 2:
				$connector_form=str_replace("!!repository!!","<input type='hidden' value='2' name='repository' id='repository'/>".$msg["connecteurs_no"],$connector_form);
				break;
			//Possible
			case 3:
				$connector_form=str_replace("!!repository!!","<select name='repository' id='repositiory'><option value='1' ".($conn->repository==1?"selected":"").">".$msg["connecteurs_yes"]."</option><option value='2' ".($conn->repository==2?"selected":"").">".$msg["connecteurs_no"]."</option></select>",$connector_form);
				break;
		}
		$connector_form=str_replace("!!timeout!!",$conn->timeout,$connector_form);
		$connector_form=str_replace("!!ttl!!",$conn->ttl,$connector_form);
		$connector_form=str_replace("!!retry!!",$conn->retry,$connector_form);
		return $connector_form;
	}
	
	function show_source_form($id,$source_id="") {
		global $base_path,$charset,$admin_connecteur_source_global_params,$lang,$msg, $dbh;
		
		//Inclusion de la classe
		require_once($base_path."/admin/connecteurs/in/".$this->catalog[$id]["PATH"]."/".$this->catalog[$id]["NAME"].".class.php");
		eval("\$conn=new ".$this->catalog[$id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$this->catalog[$id]["PATH"]."\");");
		$connector_form=$conn->source_get_property_form($source_id);
		$s=$conn->get_source_params($source_id);

		$connector_form=str_replace("!!special_form!!",$connector_form,$admin_connecteur_source_global_params);
		//Remplacement des valeurs par defaut
		$connector_form=str_replace("!!id!!",$id,$connector_form);
		$connector_form=str_replace("!!source_id!!",$source_id,$connector_form);
		$connector_form=str_replace("!!connecteur!!",htmlentities($this->catalog[$id]["COMMENT"],ENT_QUOTES,$charset),$connector_form);
		$connector_form=str_replace("!!source!!",htmlentities($s["NAME"],ENT_QUOTES,$charset),$connector_form);
		$connector_form=str_replace("!!name!!",htmlentities($s["NAME"],ENT_QUOTES,$charset),$connector_form);
		$connector_form=str_replace("!!comment!!",htmlentities($s["COMMENT"],ENT_QUOTES,$charset),$connector_form);
		$connector_form=str_replace("!!ico_notice!!",htmlentities($s["ICO_NOITICE"],ENT_QUOTES,$charset),$connector_form);
		
		$xsl_exemplaire_input .= '&nbsp;<input onchange="document.source_form.action_xsl_expl.selectedIndex=1" type="file" name="xsl_exemplaire">';
				
		$categories_select = '<select MULTIPLE name="source_categories[]">';
		$categories_select .= '<option value="">'.$msg["source_no_category"].'</option>';
		$categories_sql = "SELECT connectors_categ.*, connectors_categ_sources.num_categ FROM connectors_categ LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_categ = connectors_categ.connectors_categ_id AND connectors_categ_sources.num_source = ".(isset($source_id) ? $source_id : '-1').")";

		$res = pmb_mysql_query($categories_sql, $dbh);
		while($row=pmb_mysql_fetch_object($res)) {
			$categories_select .= '<option value="'.$row->connectors_categ_id.'" '.(isset($row->num_categ) ? "SELECTED" : "").'>'.htmlentities($row->connectors_categ_name , ENT_QUOTES,$charset).'</option>';			
		}		
		$categories_select .= '</select>';
		$connector_form=str_replace("!!categories!!", $categories_select, $connector_form);
				
		if ($s["OPAC_ALLOWED"]) $connector_form=str_replace("!!opac_allowed_checked!!","checked",$connector_form);
		else $connector_form=str_replace("!!opac_allowed_checked!!","",$connector_form);
		
		if ($s["OPAC_SELECTED"]) $connector_form=str_replace("!!opac_selected_checked!!","checked",$connector_form);
		else $connector_form=str_replace("!!opac_selected_checked!!","",$connector_form);
		
		if ($s["OPAC_AFFILIATE_SEARCH"]) $connector_form=str_replace("!!opac_affiliate_search!!","checked",$connector_form);
		else $connector_form=str_replace("!!opac_affiliate_search!!","",$connector_form);

		if ($s["UPLOAD_DOC_NUM"]) $connector_form=str_replace("!!upload_doc_num!!","checked",$connector_form);
		else $connector_form=str_replace("!!upload_doc_num!!","",$connector_form);
		
		switch ($conn->is_repository()) {
			//Oui
			case 1:
				$connector_form=str_replace("!!repository!!","<input type='hidden' value='1' name='repository' id='repository'/>".$msg["connecteurs_yes"],$connector_form);
				break;
			//Non
			case 2:
				$connector_form=str_replace("!!repository!!","<input type='hidden' value='2' name='repository' id='repository'/>".$msg["connecteurs_no"],$connector_form);
				break;
			//Possible
			case 3:
				$connector_form=str_replace("!!repository!!","<select name='repository' id='repositiory'><option value='1' ".($s["REPOSITORY"]==1?"selected":"").">".$msg["connecteurs_yes"]."</option><option value='2' ".($s["REPOSITORY"]==2?"selected":"").">".$msg["connecteurs_no"]."</option></select>",$connector_form);
				break;
		}
		
		if($conn->enrichment_is_allow()){
			//si l'enrichissement est possible, le propose
			$enrichment = "
		<div class='row'>	
			<div class='colonne3'>
				<label for='search_index'>".$msg['connecteurs_source_enrichment']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='enrichment' value='1' ".($s["ENRICHMENT"] ? "checked":"")." />
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='type_enrichement_allowed'>".$msg['connecteurs_source_type_enrichment_allowed']."</label>
			</div>
			<div class='colonne_suite'>
				!!types!!
			</div>	
		</div>
		<div class='row'>&nbsp;</div>";

			$type_enrichment_form = "
				<table class='quadrille'>
					<tr>
						<th>".$msg['enrichment_type']."</th>
						<th>".$msg['enrichment_type_allow']."</th>
					</tr>";
			//on récupère les libellés par défaut des onglets d'enrichissement
			global $include_path,$lang;
			$file = $include_path."/enrichment/categories.xml";
			$xml = file_get_contents($file);
			$elems= _parser_text_no_function_($xml,"XMLLIST");
			$type_labels=array();
			foreach($elems['ENTRY'] as $elem){
				$type_labels[$elem['CODE']] = $elem['value'];
			}
			
			
			$enrichment_types = $conn->getTypeOfEnrichment($source_id);
			foreach($enrichment_types['type'] as $elem){
				$type=array();
				$type_enrichment_form .= "
					<tr>
						<td>";
				if(!is_array($elem)) {
					$type = array(
						'code' => $elem, 
						'label' => $msg[substr($type_labels[$elem],4)]
					);
				}else{
					$type = $elem;
					if(!$type['label']){
						$type['label'] = $msg[substr($type_labels[$type['code']],4)];
					}
				}		
				$type_enrichment_form .= "
							".$type['label']."
						</td>
						<td>
							<input type='checkbox' name='type_enrichment_allowed[]' value='".$type['code']."' ".(in_array($type['code'],$s['TYPE_ENRICHMENT_ALLOWED']) ? "checked='checked'" : "" )." />
						</td>
					</tr>";
			}
			$type_enrichment_form .= "			
				</table>";
			
			$enrichment=str_replace("!!types!!",$type_enrichment_form,$enrichment);
			$connector_form=str_replace("!!enrichment!!",$enrichment,$connector_form);
		}else{
			$connector_form=str_replace("!!enrichment!!","",$connector_form);
		}
		$connector_form=str_replace("!!timeout!!",$s["TIMEOUT"],$connector_form);
		$connector_form=str_replace("!!ttl!!",$s["TTL"],$connector_form);
		$connector_form=str_replace("!!retry!!",$s["RETRY"],$connector_form);
		
		//rep upload    
		$rep_upload_form="
				<select name='rep_upload'>
					<option value=''>".$msg["connecteurs_no_upload_rep"]."</option>";
		//on récup la liste des répertoires d'upload...
		$upload_folders = array();
		$res = pmb_mysql_query("select repertoire_id from upload_repertoire");
		if(pmb_mysql_num_rows($res)){
			while ($r = pmb_mysql_fetch_object($res)){
				$rep = new upload_folder($r->repertoire_id);
				$rep_upload_form.="
					<option value='".$rep->repertoire_id."' ".($s['REP_UPLOAD']==$rep->repertoire_id ? "selected" : "").">".$rep->repertoire_nom."</option>";
			}
		}			
		$rep_upload_form.="
				</select>";	
		$connector_form=str_replace("!!rep_upload!!",$rep_upload_form,$connector_form);
		
		
		if (!$source_id) {
			$bt_suppr="";
		} else {
			$bt_suppr="<input type='button' class='bouton' value='".$msg["63"]."' onClick='this.form.act.value=\"delete_source\"; this.form.submit();'/>";
		}
		$connector_form=str_replace("!!bt_supprimer!!",$bt_suppr,$connector_form);
		return $connector_form;
	}

}
?>