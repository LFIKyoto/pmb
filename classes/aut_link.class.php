<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_link.class.php,v 1.37 2018-07-09 15:23:46 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion des liens entre autorités

require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/titre_uniforme.class.php");
require_once("$class_path/authperso.class.php");
require_once("$class_path/indexation_authority.class.php");
require_once("$class_path/indexation_authperso.class.php");
require_once("$class_path/onto/onto_index.class.php");
require_once("$class_path/authorities_collection.class.php");
require_once($include_path."/templates/aut_link.tpl.php");
require_once($class_path."/cache_factory.class.php");

define('AUT_TABLE_AUTHORS',1);
define('AUT_TABLE_CATEG',2);
define('AUT_TABLE_PUBLISHERS',3);
define('AUT_TABLE_COLLECTIONS',4);
define('AUT_TABLE_SUB_COLLECTIONS',5);
define('AUT_TABLE_SERIES',6);
define('AUT_TABLE_TITRES_UNIFORMES',7);
define('AUT_TABLE_INDEXINT',8);
define('AUT_TABLE_AUTHPERSO',9);
define('AUT_TABLE_CONCEPT',10);
define('AUT_TABLE_INDEX_CONCEPT',11);
// Pour la classe authorities_collection
define('AUT_TABLE_CATEGORIES',12);
define('AUT_TABLE_AUTHORITY',13);
// authperso >1000

$aut_table_name_list=array(
	AUT_TABLE_AUTHORS => 'authors',
	AUT_TABLE_CATEG => 'categ',
	AUT_TABLE_PUBLISHERS=> 'publishers',
	AUT_TABLE_COLLECTIONS => 'collection',
	AUT_TABLE_SUB_COLLECTIONS => 'sub_collections',
	AUT_TABLE_SERIES => 'series',
	AUT_TABLE_TITRES_UNIFORMES => 'titres_uniformes',
	AUT_TABLE_INDEXINT => 'indexint',
	AUT_TABLE_CONCEPT => 'concept',
	AUT_TABLE_INDEX_CONCEPT => 'concept',
	AUT_TABLE_AUTHPERSO => 'authperso'
); 

// définition de la classe de gestion des liens entre autorités
class aut_link {

	protected $aut_link_xml;
	public $aut_table;
	public $id;
	protected $js_aut_link_table_list = ''; // nécesaire pour les aut perso..
	private static $onto_index;
	
	public function __construct($aut_table,$id) {
		$this->aut_table = $aut_table;
		$this->id = $id;
		$this->getdata();
	}	

	public function getdata() {
		global $dbh,$msg;
		global $aut_table_name_list;
		global $pmb_opac_url;
		$this->parse_file();
		if($this->aut_table > 1000) {
			$this->aut_table_name = $aut_table_name_list[AUT_TABLE_AUTHPERSO];
		} else {
			$this->aut_table_name = $aut_table_name_list[$this->aut_table];
		}
		$this->aut_list=array();		
			
		$rqt="select * from aut_link where (aut_link_from='".$this->aut_table."'	and aut_link_from_num='".$this->id."' )
		or ( aut_link_to='".$this->aut_table."' and aut_link_to_num='".$this->id."' and aut_link_reciproc=1 )
		order by aut_link_type ";
		$aut_res=pmb_mysql_query($rqt, $dbh);
		$i=0;
		while($row = pmb_mysql_fetch_object($aut_res)){
			$i++;
			$this->aut_list[$i]['to']=$row->aut_link_to;
			$this->aut_list[$i]['to_num']=$row->aut_link_to_num;
			$this->aut_list[$i]['type']=$row->aut_link_type;
			$this->aut_list[$i]['reciproc']=$row->aut_link_reciproc;
			$this->aut_list[$i]['comment']=$row->aut_link_comment;
						
			if(($this->aut_table==$row->aut_link_to ) and ($this->id == $row->aut_link_to_num)) {
				$this->aut_list[$i]['flag_reciproc']=1;
				$this->aut_list[$i]['to']=$row->aut_link_from;
				$this->aut_list[$i]['to_num']=$row->aut_link_from_num;
			} else {
				$this->aut_list[$i]['flag_reciproc']=0;
			}
			
			switch($this->aut_list[$i]['to']){
				case AUT_TABLE_AUTHORS :
					$auteur = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $auteur->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[133].'] '.$auteur->get_isbd();
				break;
				case AUT_TABLE_CATEG :
					$categ = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $categ->libelle;
					$this->aut_list[$i]['libelle'] = '['.$msg[134].'] '.$categ->libelle;
				break;
				case AUT_TABLE_PUBLISHERS :
					$ed = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $ed->get_isbd();	
					$this->aut_list[$i]['libelle'] = '['.$msg[135].'] '.$ed->get_isbd();
				break;
				case AUT_TABLE_COLLECTIONS :
					$collection = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $collection->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[136].'] '.$collection->get_isbd();
				break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$subcollection = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $subcollection->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[137].'] '.$subcollection->get_isbd();
				break;
				case AUT_TABLE_SERIES :
					$serie = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $serie->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[333].'] '.$serie->get_isbd();
				break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$tu = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$tu->get_isbd();	
					$this->aut_list[$i]['libelle']='['.$msg['aut_menu_titre_uniforme'].'] '.$tu->get_isbd();
				break;
				case AUT_TABLE_INDEXINT :
					$indexint = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$indexint->get_isbd();
					$this->aut_list[$i]['libelle']='['.$msg['indexint_menu'].'] '.$indexint->get_isbd();
				break;
				case AUT_TABLE_CONCEPT :
					$concept= authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$concept->get_display_label();
					$this->aut_list[$i]['libelle']='['.$msg['concept_menu'].'] '.$concept->get_display_label();	
				break;
				default:
					if($this->aut_list[$i]['to']>1000){
						// authperso
						$authperso = new authperso($this->aut_list[$i]['to']-1000);
						$isbd = authperso::get_isbd($this->aut_list[$i]['to_num']);
						$this->aut_list[$i]['isbd_entry']=$isbd;
						$this->aut_list[$i]['libelle']='['.$authperso->info['name'].'] '.$isbd;
						$this->aut_list[$i]['url_to_gestion']='./autorites.php?categ=authperso&sub=authperso_form&id_authperso=&id='.$this->aut_list[$i]['to_num'];
						$this->aut_list[$i]['url_to_opac']=$pmb_opac_url.'index.php?lvl=authperso_see&id='.$this->aut_list[$i]['to_num'];
					}
				
				break;
			}
			if($this->aut_list[$i]["flag_reciproc"]){
				$type_relation=new marc_select("relationtype_autup","f_aut_link_type$i", $this->aut_list[$i]['type']);
			}else {
				$type_relation=new marc_select("relationtype_aut","f_aut_link_type$i", $this->aut_list[$i]['type']);
			}
			$this->aut_list[$i]['relation_libelle']=$type_relation->libelle;
		}
	}

	public function get_form($caller="categ_form") {
		global $msg,$add_aut_link,$aut_link0,$aut_link1,$form_aut_link;
		global $thesaurus_concepts_active,$charset;
		
		$form = $add_aut_link;
		$this->js_aut_link_table_list="
		var aut_link_table_select=Array();
		aut_link_table_select[".AUT_TABLE_AUTHORS."]='./select.php?what=auteur&caller=$caller&dyn=2&param1=';		
		aut_link_table_select[".AUT_TABLE_CATEG."]='./select.php?what=categorie&caller=$caller&dyn=2&parent=1&p1=';
		aut_link_table_select[".AUT_TABLE_PUBLISHERS."]='./select.php?what=editeur&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_COLLECTIONS."]='./select.php?what=collection&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_SUB_COLLECTIONS."]='./select.php?what=subcollection&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_SERIES."]='./select.php?what=serie&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_TITRES_UNIFORMES."]='./select.php?what=titre_uniforme&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_INDEXINT."]='./select.php?what=indexint&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_CONCEPT."]='./select.php?what=ontology&caller=$caller&element=concept&dyn=2&param1=';
		";
		
		$aut_table_list = $this->generate_aut_type_selector($caller);
		
		$i=0;
		if(!count($this->aut_list)){		
			// pas d'enregistrement	
			$form.=$aut_link0;
			
			$liste_type_relation=new marc_select("relationtype_aut","f_aut_link_type$i", "","","","",array(array('name'=>'data-form-name','value'=>'f_aut_link_type')));	
			$form=str_replace("!!aut_link_type!!",$liste_type_relation->display,$form);				
			$form=str_replace("!!aut_link_reciproc!!","unchecked='unchecked'",$form);	
			$form=str_replace("!!aut_link!!",$i,$form);	
			$form=str_replace("!!aut_link_libelle!!","",$form);
			$form=str_replace("!!aut_link_table!!","",$form);
			$form=str_replace("!!aut_link_id!!","",$form);	
			$form=str_replace("!!aut_link_comment!!","",$form);
			$i++;
		} else{			
			foreach ($this->aut_list as $aut) {	
				// Construction de chaque ligne du formulaire	
				if($i) $form_suivant=$aut_link1; else $form_suivant=$aut_link0;		
				if($aut["flag_reciproc"]){
					$liste_type_relation=new marc_select("relationtype_autup","f_aut_link_type$i", $aut["type"],"","","",array(array('name'=>'data-form-name','value'=>'f_aut_link_type')));
				}else {
					$liste_type_relation=new marc_select("relationtype_aut","f_aut_link_type$i", $aut["type"],"","","",array(array('name'=>'data-form-name','value'=>'f_aut_link_type')));
				}
				$form_suivant=str_replace("!!aut_link_type!!",$liste_type_relation->display,$form_suivant);
				if($aut["reciproc"]) $check="checked='checked'"; else $check="";
				$form_suivant=str_replace("!!aut_link_reciproc!!",$check,$form_suivant);	
				$form_suivant=str_replace("!!aut_link!!",$i,$form_suivant);
				$form_suivant=str_replace("!!aut_link_libelle!!",htmlentities($aut["libelle"],ENT_QUOTES, $charset,false),$form_suivant);
				$form_suivant=str_replace("!!aut_link_table!!",$aut["to"],$form_suivant);
				$form_suivant=str_replace("!!aut_link_id!!",$aut["to_num"],$form_suivant);
				$form_suivant=str_replace("!!aut_link_comment!!",$aut["comment"],$form_suivant);
				$form.=$form_suivant;		
				$i++;		
			}				
		}
		$form=str_replace("!!max_aut_link!!",$i,$form);
		$form=str_replace("!!js_aut_link_table_list!!",$this->js_aut_link_table_list,$form);
		$form=str_replace("!!aut_table_list!!",$aut_table_list,$form);
		if(!$aut_table_list && !count($this->aut_list)){
			return str_replace("!!aut_link_contens!!", $msg['no_aut_link'], $form_aut_link);
		}
		return str_replace("!!aut_link_contens!!", $form , $form_aut_link);
		
	}
	
	public function save_form() {
		global $dbh;
		//max_aut_link
		//f_aut_link_typexxx
		//f_aut_link_tablexxx
		//f_aut_link_idxxx
		global $max_aut_link;
		if(!$this->aut_table && !$this->id) return;
		$this->delete_link();
		for($i=0;$i<$max_aut_link;$i++){
			eval("global \$f_aut_link_table".$i.";\$f_aut_link_table= \$f_aut_link_table$i;"); 
			eval("global \$f_aut_link_id".$i.";\$f_aut_link_id= \$f_aut_link_id$i;"); 
			eval("global \$f_aut_link_type".$i.";\$f_aut_link_type= \$f_aut_link_type$i;"); 
			eval("global \$f_aut_link_reciproc".$i.";\$f_aut_link_reciproc= \$f_aut_link_reciproc$i;"); 
			eval("global \$f_aut_link_comment".$i.";\$f_aut_link_comment= \$f_aut_link_comment$i;");
			
			// Les selecteurs de concept retourne l'uri et non id 
			if($f_aut_link_table==AUT_TABLE_CONCEPT && !is_numeric($f_aut_link_id)){ 
				$f_aut_link_id=onto_common_uri::get_id($f_aut_link_id);				
			}
			if($f_aut_link_reciproc)$f_aut_link_reciproc=1;
			if($f_aut_link_id && $f_aut_link_table && $f_aut_link_type && !(($this->aut_table == $f_aut_link_table) && ($this->id == $f_aut_link_id))) {
	 			$requete="INSERT INTO aut_link (aut_link_from, aut_link_from_num, aut_link_to,aut_link_to_num , aut_link_type, aut_link_reciproc, aut_link_comment) 
	 			VALUES ('".$this->aut_table."', '".$this->id."','".$f_aut_link_table."', '".$f_aut_link_id."', '".$f_aut_link_type."', '".$f_aut_link_reciproc."','".$f_aut_link_comment."')";
				pmb_mysql_query($requete);
			}
			if($f_aut_link_reciproc){
				$this->maj_index($f_aut_link_id, $f_aut_link_table > 1000 ? 9 : $f_aut_link_table);
			}
		}
	}
			
	// delete tous les liens (from vers to) de cette autorité 
	public function delete_link() {
		if(!$this->aut_table && !$this->id) return;
		$query = "SELECT aut_link_to_num, aut_link_to FROM aut_link WHERE aut_link_from_num='".$this->id."' and aut_link_from='".$this->aut_table."'";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."' ");
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->maj_index($row->aut_link_to_num, $row->aut_link_to > 1000 ? 9 : $row->aut_link_to);
			}
		}
		$query = "SELECT aut_link_from_num, aut_link_from FROM aut_link WHERE aut_link_to_num='".$this->id."' and aut_link_to='".$this->aut_table."' and aut_link_reciproc=1";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_to='".$this->aut_table."' and aut_link_to_num='".$this->id."' and aut_link_reciproc=1 ");
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->maj_index($row->aut_link_from_num, $row->aut_link_from > 1000 ? 9 : $row->aut_link_from);
			}
		}
	}		
	
	// delete tous les liens (from et to) de cette autorité 
	public function delete() {
		global $dbh;
		if(!$this->aut_table && !$this->id) return;
		$query = "SELECT aut_link_to_num, aut_link_to FROM aut_link WHERE aut_link_from_num='".$this->id."' and aut_link_from='".$this->aut_table."'";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."' ");
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->maj_index($row->aut_link_to_num, $row->aut_link_to > 1000 ? 9 : $row->aut_link_to);
			}
		}
		$query = "SELECT aut_link_from_num, aut_link_from FROM aut_link WHERE aut_link_to_num='".$this->id."' and aut_link_to='".$this->aut_table."'";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_to='".$this->aut_table."' and aut_link_to_num='".$this->id."' ");
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->maj_index($row->aut_link_from_num, $row->aut_link_from > 1000 ? 9 : $row->aut_link_from);
			}
		}
	}	
	
	// copie les liens from et to par une autre autorité
	public function add_link_to($copy_table,$copy_num) {
		global $dbh;
		if(!$this->aut_table && !$this->id && !$copy_link_to && !$copy_link_to_num) return;
		
		foreach ($this->aut_list as $aut) {		
			if($aut["flag_reciproc"]){
		 		$requete="INSERT INTO aut_link (aut_link_from, aut_link_from_num, aut_link_to,aut_link_to_num , aut_link_type, aut_link_reciproc, aut_link_comment) 
		 		VALUES ('".$aut["to"]."', '".$aut["to_num"]."','".$copy_table."', '".$copy_num."', '".$aut["type"]."', '".$aut["reciproc"]."','".$aut["comment"]."')";					
			}else {
		 		$requete="INSERT INTO aut_link (aut_link_from, aut_link_from_num, aut_link_to,aut_link_to_num , aut_link_type, aut_link_reciproc, aut_link_comment) 
		 		VALUES ('".$copy_table."', '".$copy_num."','".$aut["to"]."', '".$aut["to_num"]."', '".$aut["type"]."', '".$aut["reciproc"]."','".$aut["comment"]."')";							
			}
			@pmb_mysql_query($requete);
		}		
	}
	
	public function get_display($caller="categ_form") {
		global $msg;
		if(!count($this->aut_list)) return"";
	
		$aut_see_link = "./autorites.php?categ=see&sub=!!type!!&id=!!to_num!!";		

		$marc_table=marc_list_collection::get_instance("relationtype_aut");
		$liste_type_relation = $marc_table->table;
		$marc_tableup=marc_list_collection::get_instance("relationtype_autup");
		$liste_type_relationup = $marc_tableup->table;
	
		$aff="<ul>";
		foreach ($this->aut_list as $aut) {
			$type = $this->get_type_from_const(($aut['to']>1000?9:$aut['to']));
			$aff.="<li>";
			if($aut['flag_reciproc']) {
				$aff.=$liste_type_relationup[$aut['type']]." : ";
			} else	{
				$aff.=$liste_type_relation[$aut['type']]." : ";
			}
			$link =str_replace("!!to_num!!",$aut['to_num'],$aut_see_link);
			$link = str_replace("!!type!!",$type,$link);
			$aff.=" <a href='".$link."'>".$aut['libelle']."</a>";
			if($aut['comment']) {
				$aff.=" (".$aut['comment'].")";
			}
			$aff.="</li>";
		}
		$aff.="</ul>";
		return $aff;
	}
	
	/**
	 * Parse le fichier xml
	 */
	private function parse_file() {
		global $base_path, $include_path, $charset;
		global $msg, $KEY_CACHE_FILE_XML;
		
		$filepath = $include_path."/authorities/aut_links_subst.xml";
		if (!file_exists($filepath)) {
			$filepath = $include_path."/authorities/aut_links.xml";
		}

		$fileInfo = pathinfo($filepath);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
		$tempFile = $base_path."/temp/XML".$fileName.".tmp";
		$dejaParse = false;
		
		$cache_php=cache_factory::getCache();
		$key_file="";
		if ($cache_php) {
			$key_file=getcwd().$fileName.filemtime($filepath);
			$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
			if($tmp_key = $cache_php->getFromCache($key_file)){
				if($cache = $cache_php->getFromCache($tmp_key)){
					if(count($cache) == 1){
						$this->aut_link_xml = $cache[0];
						$dejaParse = true;
					}
				}
			}
				
		}else{
		if (file_exists($tempFile) ) {
			//Le fichier XML original a-t-il été modifié ultérieurement ?
			if (filemtime($filepath) > filemtime($tempFile)) {
				//on va re-générer le pseudo-cache
				if($tempFile && file_exists($tempFile)){
					unlink($tempFile);
				}
			} else {
				$dejaParse = true;
			}
		}
		if ($dejaParse) {
			$tmp = fopen($tempFile, "r");
			$cache = unserialize(fread($tmp,filesize($tempFile)));
			fclose($tmp);
			if(count($cache) == 1){
				$this->aut_link_xml = $cache[0];
			}else{
				//SOUCIS de cache...
				if($tempFile && file_exists($tempFile)){
					unlink($tempFile);
				}
					$dejaParse = false;
				}
			}
			}
		
		if(!$dejaParse){
			$fp=fopen($filepath,"r") or die("Can't find XML file");
			$size=filesize($filepath);
	
			$xml=fread($fp,$size);
			fclose($fp);
			$aut_links = _parser_text_no_function_($xml, "AUT_LINKS");
			
			$this->aut_link_xml = array();
			$aut_definition = array();
			foreach($aut_links['DEFINITION'][0]['ENTRY'] as $xml_aut_definition){
				$aut_def[$xml_aut_definition['CODE']] = $xml_aut_definition['value'];
			}
			
			/**
			 * Le résultat du parse du fichier xml est stocké en temps que tableau sérialisé dans le fichier tempo  
			 */
			//Lecture des liens
			foreach ($aut_links['LINKS'][0]['AUTHORITY'] as $main_authority) {
				$aut_allowed = array();
				if($main_authority['AUTHORITY_ALLOWED']){
					foreach($main_authority['AUTHORITY_ALLOWED'] as $sub_aut_allowed){
						if(isset($aut_def[$sub_aut_allowed['value']])){
							$aut_allowed[] = $aut_def[$sub_aut_allowed['value']];
						}
						
					}	
				}
				if(isset($aut_def[$main_authority['CODE']])){
					$this->aut_link_xml[$aut_def[$main_authority['CODE']]]['aut_to_display'] = $aut_allowed;
				}
			}
			
			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->aut_link_xml)));
				$cache_php->setInCache($key_file_content, array($this->aut_link_xml));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
			$tmp = fopen($tempFile, "wb");
			fwrite($tmp,serialize(array($this->aut_link_xml)));
			fclose($tmp);
		}
	}
	}
	
	public function get_type_from_const($const){
		switch($const){
			case "1" :
				return "author";
			case "2" :
				return "category";
			case "3" :
				return "publisher";
			case "4" :
				return "collection";
			case "5" :
				return "subcollection";
			case "6" :
				return "serie";
			case "7" :
				return "titre_uniforme";
			case "8" :
				return "indexint";
			case "9" :
				return "authperso";
			case "10" :
				return "concept";
		}	
	}
	
	protected function generate_aut_type_selector($caller="categ_form"){
		global $msg;
		global $thesaurus_concepts_active;
		global $charset;
		global $form_aut_link_buttons;
	
		$aut_table_list="<select id='f_aut_link_table_list' name='f_aut_link_table_list'>";
		
		$options = '';
		//Cas à gérer pour les autorités persos
		$auth_type = ($this->aut_table <= 1000 ? $this->aut_table : 9);
		
		foreach($this->aut_link_xml[$auth_type]['aut_to_display'] as $aut_to_display){
			switch($aut_to_display){
				case '1':
					$options.= '<option value="'.AUT_TABLE_AUTHORS.'" selected="selected">'.$msg["133"].'</option>';
					break;
				case '2':
					$options.= '<option value="'.AUT_TABLE_CATEG.'">'.$msg['134'].'</option>';
					break;
				case '3':
					$options.= '<option value="'.AUT_TABLE_PUBLISHERS.'">'.$msg['135'].'</option>';
					break;
				case '4':
					$options.= '<option value="'.AUT_TABLE_COLLECTIONS.'">'.$msg['136'].'</option>';
					break;
				case '5':
					$options.= '<option value="'.AUT_TABLE_SUB_COLLECTIONS.'">'.$msg['137'].'</option>';
					break;
				case '6':
					$options.= '<option value="'.AUT_TABLE_SERIES.'">'.$msg['333'].'</option>';
					break;
				case '7':
					$options.= '<option value="'.AUT_TABLE_TITRES_UNIFORMES.'">'.$msg['aut_menu_titre_uniforme'].'</option>';
					break;
				case '8':
					$options.= '<option value="'.AUT_TABLE_INDEXINT.'">'.$msg['indexint_menu'].'</option>';
					break;
				case '9':
					$authpersos = authpersos::get_instance();
					$info=$authpersos->get_data();
					foreach($info as $elt){
						$tpl_elt="<option value='!!id_authperso!!'>!!name!!</option>";
						$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
						$tpl_elt=str_replace('!!id_authperso!!',$elt['id'] + 1000, $tpl_elt);
						$this->js_aut_link_table_list.="aut_link_table_select[".($elt['id'] + 1000)."]='./select.php?what=authperso&authperso_id=".$elt['id']."&caller=$caller&dyn=2&param1=';";
						$options.= $tpl_elt;
					}
					break;
				case '10':
					if($thesaurus_concepts_active){
						$options.= '<option value="'.AUT_TABLE_CONCEPT.'">'.$msg['ontology_skos_menu'].'</option>';
					}
					break;
			}
		}
		if($options){
			return $aut_table_list.$options.'</select>'.$form_aut_link_buttons;
		}
		return '';
	}
	
	protected function maj_index($object_id, $object_type) {
		global $include_path, $class_path;
		
		$directory = authority::get_indexation_directory($object_type);
		if($directory == 'concepts') {
			if(!isset(static::$onto_index)) {
				$onto_store_config = array(
						/* db */
						'db_name' => DATA_BASE,
						'db_user' => USER_NAME,
						'db_pwd' => USER_PASS,
						'db_host' => SQL_SERVER,
						/* store */
						'store_name' => 'ontology',
						/* stop after 100 errors */
						'max_errors' => 100,
						'store_strip_mb_comp_str' => 0
				);
				$data_store_config = array(
						/* db */
						'db_name' => DATA_BASE,
						'db_user' => USER_NAME,
						'db_pwd' => USER_PASS,
						'db_host' => SQL_SERVER,
						/* store */
						'store_name' => 'rdfstore',
						/* stop after 100 errors */
						'max_errors' => 100,
						'store_strip_mb_comp_str' => 0
				);
					
				$tab_namespaces=array(
						"skos"	=> "http://www.w3.org/2004/02/skos/core#",
						"dc"	=> "http://purl.org/dc/elements/1.1",
						"dct"	=> "http://purl.org/dc/terms/",
						"owl"	=> "http://www.w3.org/2002/07/owl#",
						"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
						"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
						"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
						"pmb"	=> "http://www.pmbservices.fr/ontology#"
				);
					
				static::$onto_index = onto_index::get_instance();
				static::$onto_index->load_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
			}
			static::$onto_index->maj($object_id);
		} elseif($directory == 'authperso') {
			$indexation_authperso = new indexation_authperso($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+$object_id), $object_id);
// 			if($this->aut_table > 1000) {
// 				$indexation_authperso->maj($object_id,'authperso');
// 			} else {
				$indexation_authperso->maj($object_id,'authperso');
// 			}
		} else {
			if(file_exists($include_path."/indexation/authorities/".$directory."/champs_base.xml")) {
				$indexation_authority = new indexation_authority($include_path."/indexation/authorities/".$directory."/champs_base.xml", "authorities", $object_type);
				if($this->aut_table > 1000) {
					$indexation_authority->maj($object_id,'authperso_link');
				} else {
					$indexation_authority->maj($object_id,'aut_link');
				}
			}
		}
	}
	
// fin class
}