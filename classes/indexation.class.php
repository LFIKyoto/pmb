<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation.class.php,v 1.4 2015-05-07 14:21:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");


//classe générique de calcul d'indexation...
class indexation {
	public $xml_indexation =array();
	public $table_prefix ="";
	public $temp_not=array();
	public $temp_ext=array();
	public $temp_marc=array();
	public $champ_trouve=false;
	public $tab_code_champ = array();
	public $tab_languages=array();
	public $tab_keep_empty = array();
	public $tab_pp=array();
	public $isbd_ask_list=array();	
	protected $initialized = false;
	protected $queries = array();
	protected $queries_lang= array();
	protected $datatypes = array();
	protected $reference_key = "";
	protected $reference_table = "";
	
	
	
	public function __construct($xml_filepath, $table_prefix){
		$this->table_prefix = $table_prefix;
		
		//recuperation du fichier xml de configuration
		if(!count($this->xml_indexation)) {
			if(!file_exists($xml_filepath)) return false;
			
			$subst_file = str_replace(".xml","_subst.xml",$xml_filepath);
			if(file_exists($subst_file)){
				$file = $subst_file;
			}else $file = $xml_filepath ;
		
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			$champ_base=_parser_text_no_function_($xml,"INDEXATION");
		}
		$this->xml_indexation=$champ_base;
	}
	
	protected function init(){
		$this->temp_not=array();
		$this->temp_ext=array();
		$this->temp_marc=array();
		$this->champ_trouve=false;
		$this->tab_code_champ = array();
		$this->tab_languages=array();
		$this->tab_keep_empty = array();
		$this->tab_pp=array();
		$this->isbd_ask_list=array();	
		$this->reference_key = $this->xml_indexation['REFERENCEKEY'][0]['value'];
		$this->reference_table = $this->xml_indexation['REFERENCE'][0]['value'];
		
		for ($i=0;$i<count($this->xml_indexation['FIELD']);$i++) { //pour chacun des champs decrits
			$datatype = $this->xml_indexation['FIELD'][$i]['DATATYPE'];
			if(!$this->xml_indexation['FIELD'][$i]['DATATYPE']){
				$datatype = "undefined"; 
			}
			$this->datatypes[$datatype][] = $this->xml_indexation['FIELD'][$i]['ID'];
			
			//recuperation de la liste des informations a mettre a jour
			//conservation des mots vides
			if($this->xml_indexation['FIELD'][$i]['KEEPEMPTYWORD'] == "yes"){
				$this->tab_keep_empty[]=$this->xml_indexation['FIELD'][$i]['ID'];
			}
			//champ perso
			if($this->xml_indexation['FIELD'][$i]['DATATYPE'] == "custom_field"){
				$this->tab_pp[$this->xml_indexation['FIELD'][$i]['ID']]=$this->xml_indexation['FIELD'][$i]['TABLE'][0]['value'];
			}else if ($this->xml_indexation['FIELD'][$i]['EXTERNAL']=="yes") {
				//champ externe à la table
				//Stockage de la structure pour un accès plus facile
				$this->temp_ext[$this->xml_indexation['FIELD'][$i]['ID']]=$this->xml_indexation['FIELD'][$i];
			} else {
				//champ de la table
				$this->temp_not['f'][0][$this->xml_indexation['FIELD'][$i]['ID']]= $this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
				$this->tab_code_champ[0][$this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
						'champ' => $this->xml_indexation['FIELD'][$i]['ID'],
						'ss_champ' => 0,
						'pond' => $this->xml_indexation['FIELD'][$i]['POND'],
						'no_words' => ($this->xml_indexation['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false),
						'internal' => 1
				);
				if($this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE']){
					$this->tab_code_champ[0][$this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype']=$this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
					$this->temp_not['f'][0][$this->xml_indexation['FIELD'][$i]['ID']."_marc"]=$this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']." as "."subst_for_marc_".$this->xml_indexation['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
				}
			}
			if($this->xml_indexation['FIELD'][$i]['ISBD']){ // isbd autorités
				$this->isbd_ask_list[$this->xml_indexation['FIELD'][$i]['ID']]= array(
						'champ' => $this->xml_indexation['FIELD'][$i]['ID'],
						'ss_champ' => $this->xml_indexation['FIELD'][$i]['ISBD'][0]['ID'],
						'pond' => $this->xml_indexation['FIELD'][$i]['ISBD'][0]['POND'],
						'class_name' => $this->xml_indexation['FIELD'][$i]['ISBD'][0]['CLASS_NAME']
				);
			}
			$this->champ_trouve=true;
		}
		
		foreach($this->temp_ext as $k=>$v) {
			$isbd_tab_req=array();
			$no_word_field=false;
			//Construction de la requete
			//Champs pour le select
			$select=array();
			//on harmonise les fichiers XML décrivant des requetes...
			for ($i = 0; $i<count($v["TABLE"]); $i++) {
				$table = $v['TABLE'][$i];
				$select=array();
				if(count($table['TABLEFIELD'])){
					$use_word=true;
				}else{
					$use_word=false;
				}
				if($table['IDKEY'][0]){
					$select[]=$table['NAME'].".".$table['IDKEY'][0]['value']." as subst_for_autorite_".$table['IDKEY'][0]['value'];
				}
				for ($j=0;$j<count($table['TABLEFIELD']);$j++) {
					$select[]=($table['ALIAS'] ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["value"];
					if($table['LANGUAGE']){
						$select[]=$table['LANGUAGE'][0]['value'];
						$this->tab_languages[$k]=$table['LANGUAGE'][0]['value'];
					}
					$field_name = $table['TABLEFIELD'][$j]["value"];
					if(strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")!== false){//Pour le cas où l'on a besoin de nommer un champ et d'utiliser un alias
						$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")+4);
					}elseif(strpos($table['TABLEFIELD'][$j]["value"],".")!== false){
						$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos($table['TABLEFIELD'][$j]["value"],".")+1);
					}
					$field_name=trim($field_name);
					$this->tab_code_champ[$v['ID']][$field_name] = array(
							'champ' => $v['ID'],
							'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
							'pond' => $table['TABLEFIELD'][$j]['POND'],
							'no_words' => ($v['DATATYPE'] == "marclist" ? true : false),
							'autorite' =>  $table['IDKEY'][0]['value'],
					);
					if($table['TABLEFIELD'][$j]['MARCTYPE']){
						$this->tab_code_champ[$v['ID']][$table['TABLEFIELD'][$j]["value"]]['marctype']=$table['TABLEFIELD'][$j]['MARCTYPE'];
						$select[]=$table['NAME'].".".$table['TABLEFIELD'][$j]["value"]." as subst_for_marc_".$table['TABLEFIELD'][$j]['MARCTYPE'];
					}
				}
				$query="select ".implode(",",$select)." from ".$this->reference_table;
				$jointure="";
				for( $j=0 ; $j<count($table['LINK']) ; $j++){
						
					$link = $table['LINK'][$j];
		
					if($link["TABLE"][0]['ALIAS']){
						$alias = $link["TABLE"][0]['ALIAS'];
					}else{
						$alias = $link["TABLE"][0]['value'];
					}
					switch ($link["TYPE"]) {
						case "n0" :
							if ($link["TABLEKEY"][0]['value']) {
								$jointure .= " LEFT JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
								if($link["EXTERNALTABLE"][0]['value']){
									$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
								}else{
									$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
								}
								$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							} else {
								$jointure .= " LEFT JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
								$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
								$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							}
							break;
						case "n1" :
							if ($link["TABLEKEY"][0]['value']) {
								$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
								if($link["EXTERNALTABLE"][0]['value']){
									$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
								}else{
									$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
								}
								$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							} else {
								$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
								$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
								$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							}
							break;
						case "1n" :
							$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
							$jointure .= " ON (" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
							$jointure .= "=" . $this->reference_table . "." . $link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
								
								
							break;
						case "nn" :
							$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
							$jointure .= " ON (" . $this->reference_table . "." .  $this->reference_key;
							$jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
							if ($link["TABLEKEY"][0]['value']) {
								$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
								$jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
								$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] ." ".$link["LINKRESTRICT"][0]['value']. ") ";
							} else {
								$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
								$jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
								$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value'].") ";
							}
							break;
					}
				}
				$where=" where ".$this->reference_table.".".$this->reference_key."=!!object_id!!";
				if($table['FILTER']){
					foreach ( $table['FILTER'] as $filter ) {
						if($tmp=trim($filter["value"])){
							$where.=" AND (".$tmp.")";
						}
					}
				}
				if($table['LANGUAGE']){
					$this->queries_lang[$k]= "select ".$table['LANGUAGE'][0]['value']." from ";
				}
				$query.=$jointure.$where;
				if($table['LANGUAGE']){
					$this->queries_lang[$k].=$jointure.$where;
				}
				if($use_word){
					$this->queries[$k]["new_rqt"]['rqt'][]=$query;
				}
				if($this->isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités
					$id_aut=$table['NAME'].".".$table["TABLEKEY"][0]['value'];
					$req="select $id_aut as id_aut_for_isbd from ".$this->reference_table.$jointure.$where;
					$isbd_tab_req[]=$req;
				}
		
			}
			if($use_word){
				$this->queries[$k]["rqt"] = implode(" union ",$this->queries[$k]["new_rqt"]['rqt']);
			}
			if($this->isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités
				$req=implode(" union ",$isbd_tab_req);
				$this->isbd_ask_list[$k]['req']=  $req;
			}
		}
	}
		
	public function maj($object_id,$datatype='all'){	
		global $dbh, $lang;
// 		global $indexation_lang; spécificité des notices (langue d'indexation)
		
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		//on réinitialise les tableaux d'injection
		$tab_insert=array();
		$tab_field_insert=array();
		
		
		//on a des éléments à indexer...
		if ($this->champ_trouve) {
			//Recherche des champs directs
			if($datatype=='all') {
				$this->queries[0]["rqt"]= "select ".implode(',',$this->temp_not['f'][0])." from ".$this->reference_table;
				$this->queries[0]["rqt"].=" where ".$this->reference_key."='".$object_id."'";
				$this->queries[0]["table"]=$this->reference_table;
			}
			//qu'est-ce qu'on efface?
			if($datatype=='all') {
				$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' ";
				pmb_mysql_query($req_del,$dbh);
				//la table pour les recherche exacte
				$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' ";
				pmb_mysql_query($req_del,$dbh);
				//Les champs perso
				if(count($this->tab_pp)){
					foreach ( $this->tab_pp as $id ) {
						$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ=100 and code_ss_champ='".$id."' ";
						pmb_mysql_query($req_del,$dbh);
						//la table pour les recherche exacte
						$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ=100 and code_ss_champ='".$id."' ";
						pmb_mysql_query($req_del,$dbh);
					}
				}
			}else{
				foreach($this->datatypes as $xml_datatype=> $codes){
					if($xml_datatype == $datatype){
						foreach($codes as $code_champ){
							foreach($subfields as $subfield){
								$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
								pmb_mysql_query($req_del,$dbh);
								//la table pour les recherche exacte
								$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
								pmb_mysql_query($req_del,$dbh);
								break;
							}
						}
					}
				}
			}
			//qu'est-ce qu'on met a jour ?
			$tab_insert=array();
			$tab_field_insert=array();
			
			
			foreach($this->datatypes as $xml_datatype => $code_champ){
				if($datatype == "all" || $xml_datatype == $datatype){
					
				}
			}
			foreach($this->queries as $k=>$v) {
				if($datatype == 'all' || in_array($k,$this->datatypes[$datatype])){
				
					$v['rqt'] = str_replace("!!object_id!!",$object_id,$v['rqt']);
					$r=pmb_mysql_query($v["rqt"],$dbh) or die("Requete echouee.");
			
					$tab_mots=array();
					$tab_fields=array();
					if (pmb_mysql_num_rows($r)) {
						while(($tab_row=pmb_mysql_fetch_array($r,MYSQL_ASSOC))) {
							$langage="";
							if(isset($tab_row[$this->tab_languages[$k]])){
								$langage = $tab_row[$this->tab_languages[$k]];
								unset($tab_row[$this->tab_languages[$k]]);
							}
							foreach($tab_row as $nom_champ => $liste_mots) {
								if(substr($nom_champ,0,10)=='subst_for_'){
									continue;
								}
								if($this->tab_code_champ[$k][$nom_champ]['internal']){
									$langage=$indexation_lang;
								}
								if($this->tab_code_champ[$k][$nom_champ]['marctype']){
									//on veut toutes les langues, pas seulement celle de l'interface...
									$saved_lang = $lang;
									$code = $liste_mots;
									$dir = opendir($include_path."/marc_tables");
									while($dir_lang = readdir($dir)){
										if($dir_lang!= "." && $dir_lang!=".." && $dir_lang!="CVS" && $dir_lang!=".svn" && is_dir($include_path."/marc_tables/".$dir_lang)){
											$lang = $dir_lang;
											$marclist = new marc_list($this->tab_code_champ[$k][$nom_champ]['marctype']);
											$liste_mots = $marclist->table[$code];
											$tab_fields[$nom_champ][] = array(
													'value' => trim($liste_mots),
													'lang' => $lang,
													'autorite' => $tab_row["subst_for_marc_".$this->tab_code_champ[$k][$nom_champ]['marctype']]
											);
										}
									}
									$lang = $saved_lang;
									$liste_mots = "";
								}
								if($liste_mots!='') {
									$liste_mots = strip_tags($liste_mots);
									$tab_tmp=array();
									if(!in_array($k,$this->tab_keep_empty)){
										$tab_tmp=explode(' ',strip_empty_words($liste_mots));
									}else{
										$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
									}
									//	if($lang!="") $tab_tmp[]=$lang;
									//la table pour les recherche exacte
									if(!$tab_fields[$nom_champ]) $tab_fields[$nom_champ]=array();
									$tab_fields[$nom_champ][] = array(
											'value' =>trim($liste_mots),
											'lang' => $langage,
											'autorite' => $tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']]
									);
									if(!$this->tab_code_champ[$k][$nom_champ]['no_words']){
										foreach($tab_tmp as $mot) {
											if(trim($mot)){
												$tab_mots[$nom_champ][$mot]=$langage;
											}
										}
									}
								}
							}
						}
					}
			
					foreach ($tab_mots as $nom_champ=>$tab) {
						$memo_ss_champ="";
						$order_fields=1;
						$pos=1;
						foreach ( $tab as $mot => $langage ) {
							$num_word = indexation::add_word($mot, $langage);
							if($num_word != 0){
								$tab_insert[]="(".$object_id.",".$this->tab_code_champ[$k][$nom_champ]['champ'].",".$this->tab_code_champ[$k][$nom_champ]['ss_champ'].",".$num_word.",".$this->tab_code_champ[$k][$nom_champ]['pond'].",$order_fields,$pos)";
								$pos++;
								if($this->tab_code_champ[$k][$nom_champ]['ss_champ']!= $memo_ss_champ) $order_fields++;
								$memo_ss_champ=$this->tab_code_champ[$k][$nom_champ]['ss_champ'];
							}
						}
			
					}
					//la table pour les recherche exacte
					foreach ($tab_fields as $nom_champ=>$tab) {
						foreach($tab as $order => $values){
							$tab_field_insert[]="(".$object_id.",".$this->tab_code_champ[$k][$nom_champ]['champ'].",".$this->tab_code_champ[$k][$nom_champ]['ss_champ'].",".($order+1).",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$this->tab_code_champ[$k][$nom_champ]['pond'].",'".addslashes($values['autorite'])."')";
						}
					}
					
				}
// 			//Les champs perso
// 			if(count($this->tab_pp)){
// 				foreach ( $this->tab_pp as $code_champ => $table ) {
// 					$p_perso=new parametres_perso($table);
// 					//on doit retrouver l'id des eléments...
// 					switch($table){
// 						case "expl" :
// 							$rqt = "select expl_id from notices join exemplaires on expl_notice = notice_id and expl_notice!=0 where notice_id = $notice union select expl_id from notices join bulletins on num_notice = notice_id join exemplaires on expl_bulletin = bulletin_id and expl_bulletin != 0 where notice_id = $notice";
// 							$res = pmb_mysql_query($rqt);
// 							if(pmb_mysql_num_rows($res)) {
// 								$ids = array();
// 								while($row= pmb_mysql_fetch_object($res)){
// 									$ids[] =$row->expl_id;
// 								}
// 							}
// 							break;
// 						case "collstate" :
// 							break;
// 						default :
// 							$ids = array($notice);
// 					}
// 					if(count($ids)){
// 						for($i=0 ; $i<count($ids) ; $i++) {
// 							$data=$p_perso->get_fields_recherche_mot_array($ids[$i]);
// 							$j=0;
// 							$order_fields=1;
// 							foreach ( $data as $code_ss_champ => $value ) {
// 								$tab_mots=array();
// 								foreach($value as $val) {
// 									$tab_tmp=explode(' ',strip_empty_words($val));
// 									//la table pour les recherche exacte
// 									$tab_field_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$j.",'".addslashes(trim($val))."','',".$p_perso->get_pond($code_ss_champ).",0)";
// 									$j++;
// 									foreach($tab_tmp as $mot) {
// 										if(trim($mot)){
// 											$tab_mots[$mot]= "";
// 										}
// 									}
// 								}
// 								$pos=1;
// 								foreach ( $tab_mots as $mot => $langage ) {
// 									//on cherche le mot dans la table de mot...
// 									$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
// 									$result = pmb_mysql_query($query);
// 									if(pmb_mysql_num_rows($result)){
// 										$num_word = pmb_mysql_result($result,0,0);
// 									}else{
// 										$dmeta = new DoubleMetaPhone($mot);
// 										$stemming = new stemming($mot);
// 										$element_to_update = "";
// 										if($dmeta->primary || $dmeta->secondary){
// 											$element_to_update.="
// 												double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
// 										}
// 										if($element_to_update) $element_to_update.=",";
// 										$element_to_update.="stem = '".$stemming->stem."'";
											
// 										$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
// 										pmb_mysql_query($query);
// 										$num_word = pmb_mysql_insert_id();
// 									}
// 									$tab_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$num_word.",".$p_perso->get_pond($code_ss_champ).",$order_fields,$pos)";
// 									$pos++;
// 								}
// 								$order_fields++;
// 							}
// 						}
// 					}
// 				}
// 			}
			}
		
			if(count($this->isbd_ask_list)){
				// Les isbd d'autorités
				foreach($this->isbd_ask_list as $k=>$infos){
					$isbd_s=array(); // cumul des isbd
					if($datatype == "all" || in_array($k,$this->datatypes[$datatype])){
						$query = str_replace("!!object_id!!",$object_id,$infos["req"]);
						$res = pmb_mysql_query($query) or die($query);
						if(pmb_mysql_num_rows($res)) {
				
							switch ($infos["class_name"]){
								case 'author':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new auteur($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
									break;
								case 'editeur':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new editeur($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
									break;
								case 'indexint':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new indexint($row->id_aut_for_isbd);
										$isbd_s[]=$aut->display;
									}
									break;
								case 'collection':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new collection($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
									break;
								case 'subcollection':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new subcollection($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
									break;
								case 'serie':
									while($row= pmb_mysql_fetch_object($res)){
									$aut=new serie($row->id_aut_for_isbd);
									$isbd_s[]=$aut->name;
									}
									break;
								case 'categories':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new categories($row->id_aut_for_isbd,$lang);
										$isbd_s[]=$aut->libelle_categorie;
									}
									break;
								case 'titre_uniforme':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new titre_uniforme($row->id_aut_for_isbd);
										$isbd_s[]=$aut->libelle;
									}
									break;
							}
						}
					}
					$order_fields=1;
					for($i=0 ; $i<count($isbd_s) ; $i++) {
						$tab_mots=array();
						$tab_field_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$order_fields.",'".addslashes(trim($isbd_s[$i]))."','',".$infos["pond"].",0)";
					
						$tab_tmp=explode(' ',strip_empty_words($isbd_s[$i]));
						foreach($tab_tmp as $mot) {
							if(trim($mot)){
								$tab_mots[$mot]= "";
							}
						}
						$pos=1;
						foreach ( $tab_mots as $mot => $langage ) {
							$num_word = indexation::add_word($mot, $langage);
							$tab_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$order_fields,$pos)";
							$pos++;
						}
						$order_fields++;
					}
				}
			}
			$this->save_elements($tab_insert, $tab_field_insert);
		}
	}
	
	
	// compile les tableaux et lance les requetes
	protected function save_elements($tab_insert, $tab_field_insert){
		global $dbh;
		$req_insert="insert into ".$this->table_prefix."_words_global_index(".$this->reference_key.",code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert);
		pmb_mysql_query($req_insert,$dbh);
		//la table pour les recherche exacte
		$req_insert="insert into ".$this->table_prefix."_fields_global_index(".$this->reference_key.",code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert);
		pmb_mysql_query($req_insert,$dbh);		
	}
	
	//vérifie l'utilisation d'un mot dans les tables d'index.
	public static function check_word_use($id_word){
		//TODO
		return true;
	}
	
	public static function calc_stem($word,$lang){
		$stemming = new stemming($word);
		return $stemming->stem;
	}
	
	public static function calc_double_metephone($word,$lang){
		$dmeta = new DoubleMetaPhone($word);
		if($dmeta->primary || $dmeta->secondary){
			return $dmeta->primary." ".$dmeta->secondary;
		}else{
			return "";
		} 
	}
	
	public static function add_word($word,$lang){
		global $dbh;
		
		$query = "select id_word from words where word = '".$word."' and lang = '".$lang."'";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$num_word = pmb_mysql_result($result,0,0);
		}else{
			$double_metaphone = indexation::calc_double_metephone($word, $lang);
			$stem = indexation::calc_stem($word, $lang);
			$element_to_update = "";
			if($double_metaphone){
				$element_to_update.="double_metaphone = '".$double_metaphone."'";
			}
			if($element_to_update) $element_to_update.=",";
			$element_to_update.="stem = '".$stem."'";
				
			$query = "insert into words set word = '".$word."', lang = '".$lang."'".($element_to_update ? ", ".$element_to_update : "");
			pmb_mysql_query($query,$dbh);
			$num_word = pmb_mysql_insert_id($dbh);
		}
		return $num_word;
	}

	protected function delete_index($object_id,$datatype="all"){
		global $dbh;
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
			//la table pour les recherche exacte
			$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
			//Les champs perso
			if(count($this->tab_pp)){
				foreach ( $this->tab_pp as $id ) {
					$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ=100 and code_ss_champ='".$id."' ";
					pmb_mysql_query($req_del,$dbh);
					//la table pour les recherche exacte
					$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ=100 and code_ss_champ='".$id."' ";
					pmb_mysql_query($req_del,$dbh);
				}
			}
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					foreach($codes as $code_champ){
						foreach($subfields as $subfield){
							$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
							pmb_mysql_query($req_del,$dbh);
							//la table pour les recherche exacte
							$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
							pmb_mysql_query($req_del,$dbh);
							break;
						}
					}
				}
			}
		}
	}
}