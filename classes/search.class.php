<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.192.2.3 2015-12-01 09:39:14 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//Classe de gestion des recherches avancees

if(!is_object($autoloader)){
	require_once($class_path."/autoloader.class.php");
	$autoloader = new autoloader();
	
}

require_once($include_path."/parser.inc.php");
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/search.tpl.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/sort.class.php");
require_once("$class_path/acces.class.php");
require_once($include_path."/isbn.inc.php");
require_once("$class_path/fiche.class.php");

//pour les autorités
require_once("$class_path/author.class.php");
require_once("$class_path/categories.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/titre_uniforme.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/authperso.class.php");
require_once($class_path."/map/map_search_controler.class.php");

if($pmb_map_activate){
	require_once($class_path."/map/map_search_controler.class.php");
}

if ($pmb_allow_external_search && (SESSrights & ADMINISTRATION_AUTH) && $id_connector_set)
	require_once($class_path."/connecteurs_out_sets.class.php");			

class mterm {
	var $sc_type;
	var	$uid;			//Identifiant du champ
	var	$ufield;		//Nom du champ UNIMARC
	var $op;			//Operateur
	var $values;		//Liste des valeurs (tableau)
	var $vars;			//Valeurs annexes
	var $sub;			//sous liste de termes (tableau)
	var $inter;			//operateur entre ce terme et le precedent
	
	function mterm($ufield,$op,$values,$vars,$inter,$uid="") {
		$this->uid = $uid;
		$this->ufield=$ufield;
		$this->op=$op;
		$this->values=$values;
		$this->vars=$vars;
		$this->inter=$inter;
	}
	
	function set_sub($sub) {
		$this->sub=$sub;
	}
}

class search {

	var $operators;
	var $op_empty;
	var $fixedfields;
	var $dynamicfields;
	var $specialfields;
	var $pp;
	var $error_message;
	var $link;
	var	$link_expl;
	var	$link_expl_bull; 
	var	$link_explnum;
	var $link_serial;
	var $link_analysis;
	var	$link_bulletin;
	var	$link_explnum_serial;
	var	$link_explnum_analysis;
	var	$link_explnum_bulletin;
	var $rec_history;
	var $tableau_speciaux;
	var $operator_multi_value;
	var $full_path='';
	var $fichier_xml;
	
	var $dynamics_not_visible;
	var $specials_not_visible;
	
	var $isfichier = false;
	var $memory_engine_allowed = false;
	var $current_engine = 'MyISAM';
	var $authpersos = array();
	
	
	
    function search($rec_history=false,$fichier_xml="",$full_path='') {
    	global $launch_search;
    	
    	$this->parse_search_file($fichier_xml,$full_path);
    	$this->strip_slashes();
    	foreach ( $this->dynamicfields as $key => $value ) {
       		$this->pp[$key]=new parametres_perso($value["TYPE"]);
		}
		$authpersos=new authpersos();
		$this->authpersos=$authpersos->get_data();
		
		$this->rec_history=$rec_history;
		$this->full_path = $full_path;		
		$this->fichier_xml=$fichier_xml;
    }
    
    function strip_slashes() {
    	global $search, $explicit_search;
    	for ($i=0; $i<count($search); $i++) {
    		$s=explode("_",$search[$i]);
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		for ($j=0; $j<count($field); $j++) {
    			$field[$j]=stripslashes($field[$j]);
    		}
    		if ($explicit_search) {
    			if ($s[0]=="f") {
    				$ff=$this->fixedfields[$s[1]];
    				switch ($ff["INPUT_TYPE"]) {
    					case "date":
    						if(!preg_match("/^\d{4}-\d{2}-\d{2}$/",$field[0])) {
    							$field_temp=extraitdate($field[0]);
    							$field[0]=$field_temp;
     						}
    						break;
    					default:
    					//Rien a faire
    						break;
    				}
    			}
    		}
    		$$field_=$field;
    	}
    }
    
    function get_id_from_datatype($datatype, $fieldType = "d") {
    	reset($this->dynamicfields[$fieldType]["FIELD"]);
    	while (list($key,$val)=each($this->dynamicfields[$fieldType]["FIELD"])) {
    		if ($val["DATATYPE"]==$datatype) return $key;
    	}
    	return "";
    }
    
    function get_field($i,$n,$search,$pp) {
    	global $charset;
    	global $aff_list_empr_search;
    	global $msg;
    	global $include_path;
    	global $thesaurus_classement_mode_pmb;
    	global $pmb_map_size_search_edition; 
    	
    	$r="";
    	$s=explode("_",$search);
    	
    	//Champ
    	$val="field_".$i."_".$search;
    	global $$val;
    	$v=$$val;
    	if ($v=="") $v=array();
    	
    	//Variables
    	$fieldvar_="fieldvar_".$i."_".$search;
		global $$fieldvar_;
		$fieldvar=$$fieldvar_;
    	
     	if ($s[0]=="f") {
    		//Champs fixes
    		$ff=$this->fixedfields[$s[1]];
    		
    	   	//Variables globales et input
    		for ($j=0; $j<count($ff["VAR"]); $j++) {
    			switch ($ff["VAR"][$j]["TYPE"]) {
    				case "input":
 			  		  	$valvar="fieldvar_".$i."_".$search."[\"".$ff["VAR"][$j]["NAME"]."\"]";
   					 	global $$valvar;
   				 		$vvar[$ff["VAR"][$j]["NAME"]]=$$valvar;
   				 		if ($vvar[$ff["VAR"][$j]["NAME"]]=="") $vvar[$ff["VAR"][$j]["NAME"]]=array();
   				 		$var_table[$ff["VAR"][$j]["NAME"]]=$vvar[$ff["VAR"][$j]["NAME"]];
   				 		break;
   				 	case "global":
   				 		$global_name=$ff["VAR"][$j]["NAME"];
   				 		global $$global_name;
   				 		$var_table[$ff["VAR"][$j]["NAME"]]=$$global_name;
   				 		break;
    			}
    		}
    		
    		switch ($ff["INPUT_TYPE"]) {
    			case "authoritie_external":
    				$r="";
    				$op = "op_".$i."_".$search;
    				global $$op;
					global $lang;
					$libelle = "";
    				if ($$op == "AUTHORITY"){
						if($v[0]!= 0){
							switch ($ff['INPUT_OPTIONS']['SELECTOR']){
								case "auteur":
									$aut=new auteur($v[0]);
									if($aut->rejete) $libelle = $aut->name.', '.$aut->rejete;
									else $libelle = $aut->name;
									if($aut->date) $libelle .= " ($aut->date)";
									break;
								case "categorie":
									$libelle = categories::getlibelle($v[0],$lang);
									break;
								case "editeur":
									$ed = new editeur($v[0]);
									$libelle=$ed->name;
									if ($ed->ville) 
										if ($ed->pays) $libelle.=" ($ed->ville - $ed->pays)";
										else $libelle.=" ($ed->ville)";
									break;
								case "collection" :
									$coll = new collection($v[0]);
									$libelle = $coll->name;
									break;
								case "subcollection" :
									$coll = new subcollection($v[0]);
									$libelle = $coll->name;
									break;
								case "serie" :
									$serie = new serie($v[0]);
									$libelle = $serie->name;
									break;
								case "indexint" :
									$indexint = new indexint($v[0]);
									if ($indexint->comment) $libelle = $indexint->name." - ".$indexint->comment;
									else $libelle = $indexint->name ;
									if ($thesaurus_classement_mode_pmb != 0) {
										$libelle="[".$indexint->name_pclass."] ".$libelle;
									}
									break;
								case "titre_uniforme" :
									$tu = new titre_uniforme($v[0]);
									$libelle = $tu->name;
									break;
								case "notice" :
										$requete = "select if(serie_name is not null,if(tnvol is not null,concat(serie_name,', ',tnvol,'. ',tit1),concat(serie_name,'. ',tit1)),tit1) AS tit from notices left join series on serie_id=tparent_id where notice_id='".$v[0]."' ";
										$res=pmb_mysql_query($requete);
										if($res && pmb_mysql_num_rows($res)){
											$libelle = pmb_mysql_result($res,0,0);
										}else{
											$libelle = $v[0];
										}
										break;
								default :
									$libelle = $v[0];
									break;
							}
						}
						$$op == "BOOLEAN";
    					$r="<script>document.forms['search_form'].".$op.".options[0].selected=true;</script>";
    				}
    				
    				if($libelle){
    					$r.="<input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($libelle,ENT_QUOTES,$charset)."'/>";
    				}else{
    					$r.="<input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($v[0],ENT_QUOTES,$charset)."'/>";
    				}
    				break;
     			case "authoritie":
						$fnamesans="field_".$n."_".$search;
						$fname="field_".$n."_".$search."[]";
						$fname_id="field_".$n."_".$search."_id";
						$fnamesanslib="field_".$n."_".$search."_lib";
						$fnamelib="field_".$n."_".$search."_lib[]";
						$fname_name_aut_id="fieldvar_".$n."_".$search."[authority_id][]";
						$fname_aut_id="fieldvar_".$n."_".$search."_authority_id";

						$ajax=$ff["INPUT_OPTIONS"]["AJAX"];
						$selector=$ff["INPUT_OPTIONS"]["SELECTOR"];
						$p1=$ff["INPUT_OPTIONS"]["P1"];
						$p2=$ff["INPUT_OPTIONS"]["P2"];
						global $thesaurus_mode_pmb;
						if($ajax == "categories_mul" and $thesaurus_mode_pmb == 1){
							$fnamevar_id = "linkfield=\"fieldvar_".$n."_".$search."[id_thesaurus][]\"";
							$fnamevar_id_js = "fieldvar_".$n."_".$search."[id_thesaurus][]";
						}else{
							$fnamevar_id = "";
							$fnamevar_id_js = "";
						}
						$op = "op_".$i."_".$search;
						global $$op;
						global $lang;
						
						$nb_values=count($v);
						if(!$nb_values){
							//Création de la ligne
							$nb_values=1;
						}
						$nb_max_aut=$nb_values-1;
						
						$r= "<input type='hidden' id='$fnamesans"."_max_aut' value='".$nb_max_aut."'>";
						$r.= "<input class='bouton' value='...' id='$fnamesans"."_authority_selector' onclick=\"openPopUp('./select.php?what=$selector&caller=search_form&mode=un&p1=".$fname_id."_0&p2=".$fnamesanslib."_0&deb_rech='+".pmb_escape()."(document.getElementById('".$fnamesanslib."_0').value)+'&callback=authoritySelected&infield=".$fnamesans."_0', 'select_author0', 400, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" type=\"button\">";
						$r.= "<input class='bouton' type='button' value='+' onclick='add_line(\"$fnamesans\")'>";

						$r.= "<div id='el$fnamesans'>";
						
						for($inc=0;$inc<$nb_values;$inc++){
							$r.="<input id='".$fnamesans."_".$inc."' name='$fname' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='hidden' />";
							
							if ($$op == "AUTHORITY"){
								if($v[$inc]!= 0){
									switch ($ff['INPUT_OPTIONS']['SELECTOR']){
										case "auteur":
											$aut=new auteur($v[$inc]);
											if($aut->rejete) $libelle = $aut->name.', '.$aut->rejete;
											else $libelle = $aut->name;
											if($aut->date) $libelle .= " ($aut->date)";
											break;
										case "categorie":
											$libelle = categories::getlibelle($v[$inc],$lang);
											break;
										case "editeur":
											$ed = new editeur($v[$inc]);
											$libelle=$ed->name;
											if ($ed->ville) 
												if ($ed->pays) $libelle.=" ($ed->ville - $ed->pays)";
												else $libelle.=" ($ed->ville)";
											break;
										case "collection" :
											$coll = new collection($v[$inc]);
											$libelle = $coll->name;
											break;
										case "subcollection" :
											$coll = new subcollection($v[$inc]);
											$libelle = $coll->name;
											break;
										case "serie" :
											$serie = new serie($v[$inc]);
											$libelle = $serie->name;
											break;
										case "indexint" :
											$indexint = new indexint($v[$inc]);
											if ($indexint->comment) $libelle = $indexint->name." - ".$indexint->comment;
											else $libelle = $indexint->name ;
											if ($thesaurus_classement_mode_pmb != 0) {
												$libelle="[".$indexint->name_pclass."] ".$libelle;
											}
											break;
										case "titre_uniforme" :
											$tu = new titre_uniforme($v[$inc]);
											$libelle = $tu->name;
											break;	
										default :
											$libelle = $v[$inc];
											break;
									}
								}else{
									$libelle = "";
								}
								$r.="<input autfield='".$fname_id."_".$inc."' onkeyup='fieldChanged(\"".$fnamesans."\",".$inc.",this.value,event)' callback='authoritySelected' completion='$ajax' $fnamevar_id id='".$fnamesanslib."_".$inc."' name='$fnamelib' value='".htmlentities($libelle,ENT_QUOTES,$charset)."' type='text' class='saisie-20emr'/>
								";						
							}else{
								$r.="<input autfield='".$fname_id."_".$inc."' onkeyup='fieldChanged(\"".$fnamesans."\",".$inc.",this.value,event)' callback='authoritySelected' completion='$ajax' $fnamevar_id id='".$fnamesanslib."_".$inc."' name='$fnamelib' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='text' />
								";
							}
							$r.= "<input class='bouton' type='button' onclick='this.form.".$fnamesanslib."_".$inc.".value=\"\";this.form.".$fnamesans."_".$inc.".value=\"0\";' value='X'>";
							$r.= "<input type='hidden' value='".($fieldvar['authority_id'][$inc] ?$fieldvar['authority_id'][$inc] : "")."' id='".$fname_aut_id."_".$inc."' name='$fname_name_aut_id' />";
							$r.= "<input name='$fname_id' id='".$fname_id."_".$inc."' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='hidden'><br>";
						}
						$r.= "</div>";
						if($nb_values>1){
							$r.="<script>
									document.getElementById('op_".$i."_".$search."').disabled=true;
									operators_to_enable.push('op_".$i."_".$search."');
								</script>";
						}
    				break;
    			case "text":
    				if (substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
    					$input_placeholder = $msg[substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($ff['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
    				} else {
    					$input_placeholder = $ff['INPUT_OPTIONS']["PLACEHOLDER"];
    				}
    				$r="<input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($v[0],ENT_QUOTES,$charset)."' ".($input_placeholder?"placeholder='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."'":"")."/>";
    				break;
    			case "query_list":
    				$requete=$ff["INPUT_OPTIONS"]["QUERY"][0]["value"];
    				if ($ff["INPUT_OPTIONS"]["FILTERING"] == "yes") {
    					$requete = str_replace("!!acces_j!!", "", $requete);
    					$requete = str_replace("!!statut_j!!", "", $requete);
    					$requete = str_replace("!!statut_r!!", "", $requete);
    				}
    				if ($ff["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
    					$use_global = explode(",", $ff["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
    					for($j=0; $j<count($use_global); $j++) {
    						$var_global = $use_global[$j];
    						global $$var_global;
    						$requete = str_replace("!!".$var_global."!!", $$var_global, $requete);
    					}
    				}
    				$resultat=pmb_mysql_query($requete);
    				$r="<select name='field_".$n."_".$search."[]' multiple size='5' style='width:40em;'>";
    				while ($opt=pmb_mysql_fetch_row($resultat)) {
    					$r.="<option value='".htmlentities($opt[0],ENT_QUOTES,$charset)."' ";
    					$as=array_search($opt[0],$v);
    					if (($as!==null)&&($as!==false)) $r.=" selected";
    					$r.=">".htmlentities($opt[1],ENT_QUOTES,$charset)."</option>";
    				}
    				$r.="</select>";
    				break;
    			case "list":
    				$options=$ff["INPUT_OPTIONS"]["OPTIONS"][0];
    				$r="<select name='field_".$n."_".$search."[]' multiple size='5' style='width:40em;'>";
    				sort($options["OPTION"]);
    				for ($i=0; $i<count($options["OPTION"]); $i++) {
    					$r.="<option value='".htmlentities($options["OPTION"][$i]["VALUE"],ENT_QUOTES,$charset)."' ";
    					$as=array_search($options["OPTION"][$i]["VALUE"],$v);
    					if (($as!==null)&&($as!==false)) $r.=" selected";
    					if (substr($options["OPTION"][$i]["value"],0,4)=="msg:") {
    						$r.=">".htmlentities($msg[substr($options["OPTION"][$i]["value"],4,strlen($options["OPTION"][$i]["value"])-4)],ENT_QUOTES,$charset)."</option>";
    					} else {
    						$r.=">".htmlentities($options["OPTION"][$i]["value"],ENT_QUOTES,$charset)."</option>";
    					}
    				}
    				$r.="</select>";
    				break;
    			case "marc_list":
    				$options=new marc_list($ff["INPUT_OPTIONS"]["NAME"][0]["value"]);
    				$tmp=array();
    				$tmp = $options->table;
					$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
					$tmp=array_map("strtoupper",$tmp);//On met en majuscule
					asort($tmp);//Tri sur les valeurs en majuscule sans accent
					foreach ( $tmp as $key => $value ) {
		       			$tmp[$key]=$options->table[$key];//On reprend les bons couples clé / libellé
					}
					$options->table=$tmp;
    				reset($options->table);

  		  			// gestion restriction par code utilise.
  		  			if ($ff["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]) {
  		  				$restrictquery=pmb_mysql_query($ff["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]);
				  		if ($restrictqueryrow=@pmb_mysql_fetch_row($restrictquery)) {
				  			if ($restrictqueryrow[0]) {
				  				$restrictqueryarray=explode(",",$restrictqueryrow[0]);
				  				$existrestrict=true;
				  			} else $existrestrict=false;
				  		} else $existrestrict=false;
  		  			} else $existrestrict=false;

    				$r="<select name='field_".$n."_".$search."[]' multiple size='5' class=\"ext_search_txt\">";
    				while (list($key,$val)=each($options->table)) {
    					if ($existrestrict && array_search($key,$restrictqueryarray)!==false) {
    						$r.="<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
	    					$as=array_search($key,$v);
    						if (($as!==null)&&($as!==false)) $r.=" selected";
    						$r.=">".htmlentities($val,ENT_QUOTES,$charset)."</option>";
    					} elseif (!$existrestrict) {
    						$r.="<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
	    					$as=array_search($key,$v);
    						if (($as!==null)&&($as!==false)) $r.=" selected";
    						$r.=">".htmlentities($val,ENT_QUOTES,$charset)."</option>";
    					}    						
    				}
    				$r.="</select>";
    				break;
    			case "date":
     				$date_formatee = format_date_input($v[0]);
     				$date_clic = "onClick=\"openPopUp('./select.php?what=calendrier&caller=search_form&date_caller=".str_replace('-', '', $v[0])."&param1=field_".$n."_".$search."_date&param2=field_".$n."_".$search."[]&auto_submit=NO&date_anterieure=YES&format_return=IN', 'field_".$n."_".$search."_date', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"  ";
    				if (substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
    					$input_placeholder = $msg[substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($ff['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
    				} else {
    					$input_placeholder = $ff['INPUT_OPTIONS']["PLACEHOLDER"];
    				}
    				
    				$r="<input type='hidden' name='field_".$n."_".$search."_date' value='".str_replace('-','', $v[0])."' />
    					<input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($date_formatee,ENT_QUOTES,$charset)."' ".($input_placeholder?"placeholder='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."'":"")."/>
    					<input class='bouton_small' type='button' name='field_".$n."_".$search."_date_lib_bouton' value='".$msg["bouton_calendrier"]."' ".$date_clic." />";
    				break;
    			case "map" :
    				global $pmb_map_base_layer_type;
    				global $pmb_map_base_layer_params;
    				global $dbh;
    				
    				$layer_params = json_decode($pmb_map_base_layer_params,true);
    				$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$pmb_map_base_layer_type;
    				if(count($layer_params)){
    					if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
    					if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
    					if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
    				}
    				
    				$size=explode("*",$pmb_map_size_search_edition);
    				if(count($size)!=2)$map_size="width:800px; height:480px;";
    				$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
    				$map_holds=array();
    				foreach($v as $map_hold){
    					$map_holds[] = array(
    						"wkt" => $map_hold,
    						"type"=> "search",
    						"color"=> null,
    						"objects"=> array()
    					);
    				}
    				$r="<div id='map_search_".$n."_".$search."' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"field_".$n."_".$search."\",searchHolds:".json_encode($map_holds,true)."'></div>";
    				
    				break;
    		}
    		//Traitement des variables d'entree
    		//Variables
	    	for ($j=0; $j<count($ff["VAR"]); $j++) {
   		 		if ($ff["VAR"][$j]["TYPE"]=="input") {
   		 			$varname=$ff["VAR"][$j]["NAME"];
   		 			$visibility=1;
   		 			$vis=$ff["VAR"][$j]["OPTIONS"]["VAR"][0];
   		 			if ($vis["NAME"]) {
   		 				$vis_name=$vis["NAME"];
   		 				global $$vis_name;
   		 				if ($vis["VISIBILITY"]=="no") $visibility=0;
   		 				for ($k=0; $k<count($vis["VALUE"]); $k++) {
   		 					if ($vis["VALUE"][$k]["value"]==$$vis_name) {
   		 						if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
   		 						if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
   		 						break;
   		 					}
   		 				}
   		 			}
   		 			
   		 			//Recherche de la valeur par defaut
   		 			$vdefault=$ff["VAR"][$j]["OPTIONS"]["DEFAULT"][0];
   		 			if ($vdefault) {
   			 			switch ($vdefault["TYPE"]) {
   			 				case "var":
   			 						$default=$var_table[$vdefault["value"]];
   			 					break;
   			 				case "value":
   			 				default:
   			 						$default=$vdefault["value"];
   			 			}
   		 			} else $vdefault="";
   		 				
   		 			if ($visibility) {
	   		 			$r.="&nbsp;".$ff["VAR"][$j]["COMMENT"];
			  		  	$input=$ff["VAR"][$j]["OPTIONS"]["INPUT"][0];
			  		  	switch ($input["TYPE"]) {
			  		  		case "query_list":
			  		  			if ((!$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
			  		  			$r.="&nbsp;<select id=\"fieldvar_".$n."_".$search."[".$varname."][]\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\">\n";
			  		  			$query_list_result=@pmb_mysql_query($input["QUERY"][0]["value"]);
			  		  			$var_tmp=$concat="";
			  		  			while ($line=pmb_mysql_fetch_array($query_list_result)) {
			  		  				if($concat)$concat.=",";
			  		  				$concat.=$line[0];
			  		  				$var_tmp.="<option value=\"".htmlentities($line[0],ENT_QUOTES,$charset)."\"";
			  		  				$as=@array_search($line[0],$fieldvar[$varname]);
			  		  				if (($as!==false)&&($as!==NULL)) $var_tmp.=" selected";
			  		  				$var_tmp.=">".htmlentities($line[1],ENT_QUOTES,$charset)."</option>\n";
			  		  			}
			  		  			if($input["QUERY"][0]["ALLCHOICE"] == "yes"){
			  		  				$r.="<option value=\"".htmlentities($concat,ENT_QUOTES,$charset)."\"";
			  		  				$as=@array_search($concat,$fieldvar[$varname]);
			  		  				if (($as!==false)&&($as!==NULL)) $r.=" selected";
			  		  				$r.=">".htmlentities($msg[substr($input["QUERY"][0]["TITLEALLCHOICE"],4,strlen($input["QUERY"][0]["TITLEALLCHOICE"])-4)],ENT_QUOTES,$charset)."</option>\n";
			  		  			}
			  		  			$r.=$var_tmp;
			  		  			$r.="</select>";
			  		  			break;
			  		  		case "checkbox" :
			  		  			if(!$input["DEFAULT_ON"]){
			  				  		if ((!$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
			  		  			} elseif(!$fieldvar[$input["DEFAULT_ON"]][0]) $fieldvar[$varname][0] =$default;
			  		  			$r.="&nbsp;<input type=\"checkbox\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".$input["VALUE"][0]["value"]."\" ";
			  		  			if($input["VALUE"][0]["value"] == $fieldvar[$varname][0]) $r.="checked";			  		  			
			  		  			$r.="/>\n";
			  		  			break;
		  		  			case "radio" :
	  		  					if ((!$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
	  		  					foreach($input["OPTIONS"][0]["LABEL"] as $radio_value){
			  		  				$r.="&nbsp;<input type=\"radio\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".$radio_value["VALUE"]."\" ";
			  		  				if($radio_value["VALUE"] == $fieldvar[$varname][0]) $r.="checked";
			  		  				$r.="/>".htmlentities($msg[substr($radio_value["value"],4,strlen($radio_value["value"])-4)],ENT_QUOTES,$charset);
	  		  					}
	  		  					$r.="\n";
		  		  				break;
			  		  		case "hidden":
			  		  			if ((!$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
			  		  			if(is_array($input["VALUE"][0])) $hidden_value=$input["VALUE"][0]["value"]; 
			  		  			else $hidden_value=$fieldvar[$varname][0];
			  		  			$r.="<input type='hidden' name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".htmlentities($hidden_value,ENT_QUOTES,$charset)."\"/>";
			  		  			break;
			  		  	}
   		 			} else {
   		 				if($vis["HIDDEN"] != "no")
   		 					$r.="<input type='hidden' name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".htmlentities($default,ENT_QUOTES,$charset)."\"/>";
   		 			}
   		 		}
   	 		}
   	 	} elseif (array_key_exists($s[0],$this->pp)){
   	 		//Recuperation du champ
    		$field=array();
			$field[ID]=$s[1];
			$field[NAME]=$this->pp[$s[0]]->t_fields[$s[1]][NAME]."_".$n;
			$field[MANDATORY]=$this->pp[$s[0]]->t_fields[$s[1]][MANDATORY];
			$field[ALIAS]=$this->pp[$s[0]]->t_fields[$s[1]][TITRE];
			$field[DATATYPE]=$this->pp[$s[0]]->t_fields[$s[1]][DATATYPE];
			$field[OPTIONS][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->pp[$s[0]]->t_fields[$s[1]][OPTIONS], "OPTIONS");
			$field[VALUES]=$v;
			$field[PREFIX]=$this->pp[$s[0]]->prefix;
			eval("\$r=".$aff_list_empr_search[$this->pp[$s[0]]->t_fields[$s[1]][TYPE]]."(\$field,\$check_scripts,\"field_".$n."_".$search."\");");
   	 	} elseif ($s[0]=="authperso") {
 			$r="<span class='search_value'><input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($v[0],ENT_QUOTES,$charset)."' class='ext_search_txt'/></span>";
    				
    	}elseif ($s[0]=="s") {
    		//appel de la fonction get_input_box de la classe du champ special
    		$type=$this->specialfields[$s[1]]["TYPE"];
    		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
				if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
					$sf=$this->specialfields[$s[1]];
					if ($this->full_path && file_exists($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php"))
						require_once($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					else
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$n,$sf,$this);
					$r=$specialclass->get_input_box();	
					break;
				}
    		}
     	}
    	return $r;
    }
    
    function make_search($prefixe="") {
    	global $search;
    	global $dbh;
    	global $msg;
    	global $include_path;
    	global $pmb_multi_search_operator;
    	global $pmb_search_stemming_active;
    	 	
    	$this->error_message="";
	   	$last_table="";
	   	$field_keyName=$this->keyName;
	   	//Pour chaque champ

    	for ($i=0; $i<count($search); $i++) {
    		//construction de la requete
    		$s=explode("_",$search[$i]);
    		
    		//Recuperation de l'operateur
    		$op="op_".$i."_".$search[$i];
    		
    		//Recuperation du contenu de la recherche
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		
    		//Recuperation de l'operateur inter-champ
    		$inter="inter_".$i."_".$search[$i];
    		global $$inter;
    		global $$op;
    		
    		//Recuperation des variables auxiliaires
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		global $$fieldvar_;
    		$fieldvar=$$fieldvar_;
    		
			//Si c'est un champ fixe
    		if ($s[0]=="f") {
   	 			$ff=$this->fixedfields[$s[1]];

   	 			//Choix du moteur
				if ($this->memory_engine_allowed && !$ff['MEMORYENGINEFORBIDDEN'] ) {
					$this->current_engine = 'MEMORY';
				} else {
					$this->current_engine = 'MyISAM';
				}
   	 			
   	 			//Calcul des variables
   	 			$var_table=array();
   	 			for ($j=0; $j<count($ff["VAR"]); $j++) {
		    		switch ($ff["VAR"][$j]["TYPE"]) {
						case "input":
					 		$var_table[$ff["VAR"][$j]["NAME"]]=@implode(",",$fieldvar[$ff["VAR"][$j]["NAME"]]);
		 					break;
					 	case "global":
					 		$global_name=$ff["VAR"][$j]["NAME"];
		 					global $$global_name;
		 			 		$var_table[$ff["VAR"][$j]["NAME"]]=$$global_name;
				 			break;
				 		case "calculated":
				 			$calc=$ff["VAR"][$j]["OPTIONS"]["CALC"][0];
				 			switch ($calc["TYPE"]) {
				 				case "value_from_query":
				 					$query_calc=$calc["QUERY"][0]["value"];
				 					@reset($var_table);
				 					while (list($var_name,$var_value)=@each($var_table)) {
				 						$query_calc=str_replace("!!".$var_name."!!",$var_value,$query_calc);
				 					}
				 					$r_calc=pmb_mysql_query($query_calc);
				 					$var_table[$ff["VAR"][$j]["NAME"]]=@pmb_mysql_result($r_calc,0,0);
				 					break;
				 			}
				 			break;
			 		}
			  	}
	 			$q_index=$ff["QUERIES_INDEX"];
	 			//Recuperation de la requete associee au champ et a l'operateur
	 			$q=$ff["QUERIES"][$q_index[$$op]];
	 			
	 			//Si c'est une requete conditionnelle, on sélectionne la bonne requete et on supprime les autres
	 			if($q[0]["CONDITIONAL"]){	 				
	 				$k_default=0;
	 				$q_temp = array();
	 				$q_temp["OPERATOR"]=$q["OPERATOR"];
	 				for($k=0; $k<count($q)-1;$k++){
	 					if($var_table[$q[$k]["CONDITIONAL"]["name"]]== $q[$k]["CONDITIONAL"]["value"]) break;
	 					if ($q[$k]["CONDITIONAL"]["value"] == "default") $k_default=$k;	 						
	 				} 
	 				if($k == count($q)-1) $k=$k_default;
	 				$q_temp[0] = $q[$k];
	 				$q= $q_temp;
	  			}
	  		
				//Remplacement par les variables eventuelles pour chaque requete
				for ($k=0; $k<count($q)-1; $k++) {
	 				reset($var_table);
					while (list($var_name,$var_value)=each($var_table)) {
						$q[$k]["MAIN"]=str_replace("!!".$var_name."!!",$var_value,$q[$k]["MAIN"]);
						$q[$k]["MULTIPLE_TERM"]=str_replace("!!".$var_name."!!",$var_value,$q[$k]["MULTIPLE_TERM"]);
					}
				}	
							
				$last_main_table="";
				
				// pour les listes, si un opérateur permet une valeur vide, il en faut une...
				if($this->op_empty[$$op] && !is_array($field) ){
					$field = array();
					$field[0] = "";
				}
				// si sélection d'autorité et champ vide : on ne doit pas le prendre en compte
				if($$op=='AUTHORITY'){
					$suppr=false;
					foreach($field as $k=>$v){
						if($v==0){
							unset($field[$k]);
							$suppr=true;
						}
					}
					if($suppr){
						$field = array_values($field);
					}
				}
				//Pour chaque valeur du champ
				for ($j=0; $j<count($field); $j++) {
					//Pour chaque requete
					$field_origine=$field[$j];
					for ($z=0; $z<count($q)-1; $z++) {
						//Pour chaque valeur du cha
	   					//Si le nettoyage de la saisie est demande
	   					if($q[$z]["KEEP_EMPTYWORD"])	$field[$j]=strip_empty_chars($field_origine);
						elseif ($q[$z]["REGDIACRIT"]) $field[$j]=strip_empty_words($field_origine);
						elseif ($q[$z]["DETECTDATE"])  {
							$field[$j]=detectFormatDate($field_origine,$q[$z]["DETECTDATE"]);
						}
						$main=$q[$z]["MAIN"];
						//Si il y a plusieurs termes possibles on construit la requete avec le terme !!multiple_term!!
						if ($q[$z]["MULTIPLE_WORDS"]) {
							$terms=explode(" ",$field[$j]);
							//Pour chaque terme,
							$multiple_terms=array();
							for ($k=0; $k<count($terms); $k++) {
								$multiple_terms[]=str_replace("!!p!!",$terms[$k],$q[$z]["MULTIPLE_TERM"]);
							}
							$final_term=implode(" ".$q[$z]["MULTIPLE_OPERATOR"]." ",$multiple_terms);
							$main=str_replace("!!multiple_term!!",$final_term,$main);
						//Si la saisie est un ISBN
						} else if ($q[$z]["ISBN"]) {
							//Code brut
							$terms[0]=$field[$j];
							//EAN ?
							if (isEAN($field[$j])) {
								//C'est un isbn ?
								if (isISBN($field[$j])) {
									$rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
									//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
									$terms[1]=formatISBN($rawisbn,10);
									$terms[2]=formatISBN($rawisbn,13);
									$terms[3]=preg_replace('/-|\.| /', '', $terms[1]);
									$terms[4]=preg_replace('/-|\.| /', '', $terms[2]);
								}
							}
							else if (isISBN($field[$j])) {
								$rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
								//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
								$terms[1]=formatISBN($rawisbn,10);
								$terms[2]=formatISBN($rawisbn,13);
								$terms[3]=preg_replace('/-|\.| /', '', $terms[1]);
								$terms[4]=preg_replace('/-|\.| /', '', $terms[2]);
							}
							//Pour chaque terme,
							$multiple_terms=array();
							for ($k=0; $k<count($terms); $k++) {
								$multiple_terms[]=str_replace("!!p!!",$terms[$k],$q[$z]["MULTIPLE_TERM"]);
							}
							$final_term=implode(" ".$q[$z]["MULTIPLE_OPERATOR"]." ",$multiple_terms);
							$main=str_replace("!!multiple_term!!",$final_term,$main);
						} else if ($q[$z]["BOOLEAN"]) {
							if($q[$z]['STEMMING']){
								$stemming = $pmb_search_stemming_active;
							}else{
								$stemming = 0;
							}
    						$aq=new analyse_query($field[$j],0,0,1,0,$stemming);
							$aq1=new analyse_query($field[$j],0,0,1,1,$stemming);
							if ($q[$z]["KEEP_EMPTY_WORDS_FOR_CHECK"]) $err=$aq1->error; else $err=$aq->error;
							if (!$err) {
								if (is_array($q[$z]["TABLE"])) {
									for ($z1=0; $z1<count($q[$z]["TABLE"]); $z1++) {
										$is_fulltext=false;
										if ($q[$z]["FULLTEXT"][$z1]) $is_fulltext=true;
										if (!$q[$z]["KEEP_EMPTY_WORDS"][$z1]) 
											$members=$aq->get_query_members($q[$z]["TABLE"][$z1],$q[$z]["INDEX_L"][$z1],$q[$z]["INDEX_I"][$z1],$q[$z]["ID_FIELD"][$z1],$q[$z]["RESTRICT"][$z1],0,0,$is_fulltext);
										else $members=$aq1->get_query_members($q[$z]["TABLE"][$z1],$q[$z]["INDEX_L"][$z1],$q[$z]["INDEX_I"][$z1],$q[$z]["ID_FIELD"][$z1],$q[$z]["RESTRICT"][$z1],0,0,$is_fulltext);
										$main=str_replace("!!pert_term_".($z1+1)."!!",$members["select"],$main);
										$main=str_replace("!!where_term_".($z1+1)."!!",$members["where"],$main);
									}
								} else {
									$is_fulltext=false;
									if ($q[$z]["FULLTEXT"]) $is_fulltext=true;
									if ($q[$z]["KEEP_EMPTY_WORDS"])
										$members=$aq1->get_query_members($q[$z]["TABLE"],$q[$z]["INDEX_L"],$q[$z]["INDEX_I"],$q[$z]["ID_FIELD"],$q[$z]["RESTRICT"],0,0,$is_fulltext);
									else $members=$aq->get_query_members($q[$z]["TABLE"],$q[$z]["INDEX_L"],$q[$z]["INDEX_I"],$q[$z]["ID_FIELD"],$q[$z]["RESTRICT"],0,0,$is_fulltext);
									$main=str_replace("!!pert_term!!",$members["select"],$main);
									$main=str_replace("!!where_term!!",$members["where"],$main);
								}
							} else {
								$main="select notice_id from notices where notice_id=0";
								$this->error_message=sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message);
							}
    					}else if ($q[$z]["WORD"]){
    						if(($q[$z]['CLASS'] == "searcher_all_fields")){//Pour savoir si la recherche tous champs inclut les docnum ou pas
    							global $mutli_crit_indexation_docnum_allfields;
    							if($var_table["is_num"]){
    								$mutli_crit_indexation_docnum_allfields=1;
    							}else{
    								$mutli_crit_indexation_docnum_allfields=-1;
    							}
    						}
    						//recherche par terme...
    						if($q[$z]["FIELDS"]){
    							$searcher = new $q[$z]['CLASS']($field[$j],$q[$z]["FIELDS"]);
    						}else{
    							$searcher = new $q[$z]['CLASS']($field[$j]);
    						}
    						$main = $searcher->get_full_query();
    						
//   							print "<br><br>".$main;
						} else $main=str_replace("!!p!!",addslashes($field[$j]),$main);
						//Y-a-t-il une close repeat ?
						if ($q[$z]["REPEAT"]) {
							//Si oui, on repete !!
							$onvals=$q[$z]["REPEAT"]["ON"];
							global $$onvals;
							$onvalst=explode($q[$z]["REPEAT"]["SEPARATOR"],$$onvals);
							$mains=array();
							for ($ir=0; $ir<count($onvalst); $ir++) {
								$mains[]=str_replace("!!".$q[$z]["REPEAT"]["NAME"]."!!",$onvalst[$ir],$main);
							}
							$main=implode(" ".$q[$z]["REPEAT"]["OPERATOR"]." ",$mains);
							$main="select * from (".$main.") as sbquery".($q[$z]["REPEAT"]["ORDERTERM"]?" order by ".$q[$z]["REPEAT"]["ORDERTERM"]:"");
						}
						if ($z<(count($q)-2)) pmb_mysql_query($main);
					}		

					if($fieldvar["operator_between_multiple_authorities"]){
						$operator=$fieldvar["operator_between_multiple_authorities"][0];
					} elseif($q["DEFAULT_OPERATOR"]){
						$operator=$q["DEFAULT_OPERATOR"];
					} else {
						$operator = ($pmb_multi_search_operator?$pmb_multi_search_operator:"or");
					}
					
					if (count($field)>1) {
						if($operator == "or"){
							//Ou logique si plusieurs valeurs
							if ($prefixe) {
								$requete="create temporary table ".$prefixe."mf_".$j." ENGINE=".$this->current_engine." ".$main;	
								@pmb_mysql_query($requete,$dbh);
								$requete="alter table ".$prefixe."mf_".$j." add idiot int(1)";
								@pmb_mysql_query($requete);
								$requete="alter table ".$prefixe."mf_".$j." add unique($field_keyName)";
								@pmb_mysql_query($requete);
							} else {
								$requete="create temporary table mf_".$j." ENGINE=".$this->current_engine." ".$main;
								@pmb_mysql_query($requete,$dbh);
								$requete="alter table mf_".$j." add idiot int(1)";
								@pmb_mysql_query($requete);
								$requete="alter table mf_".$j." add unique($field_keyName)";
								@pmb_mysql_query($requete);
							}
		
							if ($last_main_table) {
								if ($prefixe) {
									$requete="insert ignore into ".$prefixe."mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
								} else {
									$requete="insert ignore into mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
								}
	 							pmb_mysql_query($requete,$dbh);
								//pmb_mysql_query("drop table mf_".$j,$dbh);
								pmb_mysql_query("drop table ".$last_main_table,$dbh);
							} //else pmb_mysql_query("drop table mf_".$j,$dbh);
							if ($prefixe) {
								$last_main_table=$prefixe."mf_".$j;
							} else {
								$last_main_table="mf_".$j;
							}
						} elseif($operator == "and"){
							//ET logique si plusieurs valeurs
							if ($prefixe) {
								$requete="create temporary table ".$prefixe."mf_".$j." ENGINE=".$this->current_engine." ".$main;	
								@pmb_mysql_query($requete,$dbh);
								$requete="alter table ".$prefixe."mf_".$j." add idiot int(1)";
								@pmb_mysql_query($requete);
								$requete="alter table ".$prefixe."mf_".$j." add unique($field_keyName)";
								@pmb_mysql_query($requete);
							} else {
								$requete="create temporary table mf_".$j." ENGINE=".$this->current_engine." ".$main;
								@pmb_mysql_query($requete,$dbh);
								$requete="alter table mf_".$j." add idiot int(1)";
								@pmb_mysql_query($requete);
								$requete="alter table mf_".$j." add unique($field_keyName)";
								@pmb_mysql_query($requete);
							}
							
							if ($last_main_table) {
								if ($prefixe) {
									$requete="create temporary table ".$prefixe."and_result_".$j." ENGINE=".$this->current_engine." select ".$last_tables.".* from ".$last_tables." where exists ( select ".$prefixe."mf_".$j.".* from ".$prefixe."mf_".$j." where ".$last_tables.".notice_id=".$prefixe."mf_".$j.".notice_id)";
								} else {
									$requete="create temporary table and_result_".$j." ENGINE=".$this->current_engine." select ".$last_tables.".* from ".$last_tables." where exists ( select mf_".$j.".* from mf_".$j." where ".$last_tables.".notice_id=mf_".$j.".notice_id)";
								}
	 							pmb_mysql_query($requete,$dbh);
								pmb_mysql_query("drop table ".$last_tables,$dbh);
								
							} 
							if ($prefixe) {
								$last_tables=$prefixe."mf_".$j;
							} else {
								$last_tables="mf_".$j;
							}
							if ($prefixe) {
								$last_main_table = $prefixe."and_result_".$j;
							} else {
								$last_main_table = "and_result_".$j;
							}
						}
					} //else print $main;
				}
				if ($last_main_table){
					$main="select * from ".$last_main_table;
				}
			} elseif (array_key_exists($s[0],$this->pp)) {
				$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
				$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
				$q_index=$df["QUERIES_INDEX"];
	 			$q=$df["QUERIES"][$q_index[$$op]];
	 			
	 			//Choix du moteur
	 			if ($this->memory_engine_allowed && !$df['MEMORYENGINEFORBIDDEN'] ) {
	 				$this->current_engine = 'MEMORY';
	 			} else {
	 				$this->current_engine = 'MyISAM';
	 			}
	 			
 				//Pour chaque valeur du champ
				$last_main_table="";
				if (count($field)==0) $field[0]="";
				for ($j=0; $j<count($field); $j++) {
					if($q["KEEP_EMPTYWORD"])	$field[$j]=strip_empty_chars($field[$j]);
					elseif ($q["REGDIACRIT"]) $field[$j]=strip_empty_words($field[$j]);
					$main=$q["MAIN"];
					//Si il y a plusieurs termes possibles
					if ($q["MULTIPLE_WORDS"]) {
						$terms=explode(" ",$field[$j]);
						//Pour chaque terme
						$multiple_terms=array();
						for ($k=0; $k<count($terms); $k++) {
							$mt=str_replace("!!p!!",addslashes($terms[$k]),$q["MULTIPLE_TERM"]);
							$mt=str_replace("!!field!!",$s[1],$mt);	
							$multiple_terms[]=$mt;
						}
						$final_term=implode(" ".$q["MULTIPLE_OPERATOR"]." ",$multiple_terms);
						$main=str_replace("!!multiple_term!!",$final_term,$main);
					} else {
						$main=str_replace("!!p!!",addslashes($field[$j]),$main);
					}
					$main=str_replace("!!field!!",$s[1],$main);
					
					if ($q["WORD"]){
						//recherche par terme...
						$searcher = new $q['CLASS']($field[$j],$s[1]);
						$main = $searcher->get_full_query();
					}
					
					//Choix de l'operateur dans la liste
					if($q["DEFAULT_OPERATOR"]){
						$operator =$q["DEFAULT_OPERATOR"];
					} else {
						$operator = ($pmb_multi_search_operator?$pmb_multi_search_operator:"or");
					}
					if (count($field)>1) {
						if($operator == "or"){
								//Ou logique si plusieurs valeurs
								if ($prefixe) {
									$requete="create temporary table ".$prefixe."mf_".$j." ENGINE=".$this->current_engine." ".$main;	
									@pmb_mysql_query($requete,$dbh);
									$requete="alter table ".$prefixe."mf_".$j." add idiot int(1)";
									@pmb_mysql_query($requete);
									$requete="alter table ".$prefixe."mf_".$j." add unique($field_keyName)";
									@pmb_mysql_query($requete);
								} else {
									$requete="create temporary table mf_".$j." ENGINE=".$this->current_engine." ".$main;
									@pmb_mysql_query($requete,$dbh);
									$requete="alter table mf_".$j." add idiot int(1)";
									@pmb_mysql_query($requete);
									$requete="alter table mf_".$j." add unique($field_keyName)";
									@pmb_mysql_query($requete);
								}
			
								if ($last_main_table) {
									if ($prefixe) {
										$requete="insert ignore into ".$prefixe."mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
									} else {
										$requete="insert ignore into mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
									}
		 							pmb_mysql_query($requete,$dbh);
									//pmb_mysql_query("drop table mf_".$j,$dbh);
									pmb_mysql_query("drop table ".$last_main_table,$dbh);
								} //else pmb_mysql_query("drop table mf_".$j,$dbh);
								if ($prefixe) {
									$last_main_table=$prefixe."mf_".$j;
								} else {
									$last_main_table="mf_".$j;
								}
							} elseif($operator == "and"){
								
								//ET logique si plusieurs valeurs
								if ($prefixe) {
									$requete="create temporary table ".$prefixe."mf_".$j." ENGINE=".$this->current_engine." ".$main;	
									@pmb_mysql_query($requete,$dbh);
									$requete="alter table ".$prefixe."mf_".$j." add idiot int(1)";
									@pmb_mysql_query($requete);
									$requete="alter table ".$prefixe."mf_".$j." add unique($field_keyName)";
									@pmb_mysql_query($requete);
								} else {
									$requete="create temporary table mf_".$j." ENGINE=".$this->current_engine." ".$main;
									@pmb_mysql_query($requete,$dbh);
									$requete="alter table mf_".$j." add idiot int(1)";
									@pmb_mysql_query($requete);
									$requete="alter table mf_".$j." add unique($field_keyName)";
									@pmb_mysql_query($requete);
								}
								
								if ($last_main_table) {
									if ($prefixe) {
										$requete="create temporary table ".$prefixe."and_result_".$j." ENGINE=".$this->current_engine." select ".$last_tables.".* from ".$last_tables." where exists ( select ".$prefixe."mf_".$j.".* from ".$prefixe."mf_".$j." where ".$last_tables.".notice_id=".$prefixe."mf_".$j.".notice_id)";
									} else {
										$requete="create temporary table and_result_".$j." ENGINE=".$this->current_engine." select ".$last_tables.".* from ".$last_tables." where exists ( select mf_".$j.".* from mf_".$j." where ".$last_tables.".notice_id=mf_".$j.".notice_id)";
									}
		 							pmb_mysql_query($requete,$dbh);
									pmb_mysql_query("drop table ".$last_tables,$dbh);
									
								} 
								if ($prefixe) {
									$last_tables=$prefixe."mf_".$j;
								} else {
									$last_tables="mf_".$j;
								}
								if ($prefixe) {
									$last_main_table = $prefixe."and_result_".$j;
								} else {
									$last_main_table = "and_result_".$j;
								}
							}
						} //else print $main;
					}		
				
				if ($last_main_table)
					$main="select * from ".$last_main_table;	
			} elseif ($s[0]=="s") {
				//instancier la classe de traitement du champ special
    			$type=$this->specialfields[$s[1]]["TYPE"];
  		  		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$last_main_table=$specialclass->make_search();
						break;
					}
    			}
    			if ($last_main_table)
    				$main="select * from ".$last_main_table;
    		}elseif ($s[0]=="authperso") {
				$aq=new analyse_query($field[0],0,0,1,1,$opac_stemming_active);
				$members=$aq->get_query_members("authperso_authorities","authperso_infos_global","authperso_index_infos_global","id_authperso_authority");
				$clause= "where ".$members["where"] ." and notice_id=notice_authperso_notice_num and notice_authperso_authority_num=id_authperso_authority and authperso_authority_authperso_num=".$s[1];
				$main="select distinct notice_id FROM notices,notices_authperso,authperso_authorities $clause ";
    			if ($last_main_table)
    				$main="select * from ".$last_main_table;
	   		}
    		if ($prefixe) {
    			$table=$prefixe."t_".$i."_".$search[$i];
    			$requete="create temporary table ".$prefixe."t_".$i."_".$search[$i]." ENGINE=".$this->current_engine." ".$main;
    			pmb_mysql_query($requete,$dbh);
				$requete="alter table ".$prefixe."t_".$i."_".$search[$i]." add idiot int(1)";
				@pmb_mysql_query($requete);
				$requete="alter table ".$prefixe."t_".$i."_".$search[$i]." add unique($field_keyName)";
				pmb_mysql_query($requete);
    		} else {
    			$table="t_".$i."_".$search[$i];
 				$requete="create temporary table t_".$i."_".$search[$i]." ENGINE=".$this->current_engine." ".$main;
    			pmb_mysql_query($requete,$dbh);
				$requete="alter table t_".$i."_".$search[$i]." add idiot int(1)";
				@pmb_mysql_query($requete);
				$requete="alter table t_".$i."_".$search[$i]." add unique($field_keyName)";
				pmb_mysql_query($requete);
    		}
			if ($last_main_table) { 
				$requete="drop table ".$last_main_table;
				pmb_mysql_query($requete);
			}
			if ($prefixe) {
				$requete="create temporary table ".$prefixe."t".$i." ENGINE=".$this->current_engine." ";
			} else {
				$requete="create temporary table t".$i." ENGINE=".$this->current_engine." ";
			}
			$isfirst_criteria=false;
			switch ($$inter) {
				case "and":
					$requete.="select ".$table.".* from $last_table,$table where ".$table.".$field_keyName=".$last_table.".$field_keyName and $table.idiot is null and $last_table.idiot is null";
					@pmb_mysql_query($requete,$dbh);
					break;
				case "or":
					//Si la table précédente est vide, c'est comme au premier jour !
					$requete_c="select count(*) from ".$last_table;
					if (!@pmb_mysql_result(pmb_mysql_query($requete_c),0,0)) {
						$isfirst_criteria=true;
					} else {
						$requete.="select * from ".$table;
						@pmb_mysql_query($requete,$dbh);
						if ($prefixe) {
							$requete="alter table ".$prefixe."t".$i." add idiot int(1)";
							@pmb_mysql_query($requete);
							$requete="alter table ".$prefixe."t".$i." add unique($field_keyName)";
							@pmb_mysql_query($requete);
						} else {
							$requete="alter table t".$i." add idiot int(1)";
							@pmb_mysql_query($requete);
							$requete="alter table t".$i." add unique($field_keyName)";
							@pmb_mysql_query($requete);
						}
						if ($prefixe) {
							$requete="insert into ".$prefixe."t".$i." ($field_keyName,idiot) select distinct ".$last_table.".".$field_keyName.",".$last_table.".idiot from ".$last_table." left join ".$table." on ".$last_table.".$field_keyName=".$table.".$field_keyName where ".$table.".$field_keyName is null";
						} else {
							$requete="insert into t".$i." ($field_keyName,idiot) select distinct ".$last_table.".".$field_keyName.",".$last_table.".idiot from ".$last_table." left join ".$table." on ".$last_table.".$field_keyName=".$table.".$field_keyName where ".$table.".$field_keyName is null";
							//print $requete;
						}
						@pmb_mysql_query($requete,$dbh);
					}
					break;
				case "ex":
					//$requete_not="create temporary table ".$table."_b select notices.notice_id from notices left join ".$table." on notices.notice_id=".$table.".notice_id where ".$table.".notice_id is null";
					//@pmb_mysql_query($requete_not);
					//$requete_not="alter table ".$table."_b add idiot int(1), add unique(notice_id)";
					//@pmb_mysql_query($requete_not);
					$requete.="select ".$last_table.".* from $last_table left join ".$table." on ".$table.".$field_keyName=".$last_table.".$field_keyName where ".$table.".$field_keyName is null";
					@pmb_mysql_query($requete);
					//$requete="drop table ".$table."_b";
					//@pmb_mysql_query($requete);
					if ($prefixe) {
						$requete="alter table ".$prefixe."t".$i." add idiot int(1)";
						@pmb_mysql_query($requete);
						$requete="alter table ".$prefixe."t".$i." add unique($field_keyName)";
						@pmb_mysql_query($requete);
					} else {
						$requete="alter table t".$i." add idiot int(1)";
						@pmb_mysql_query($requete);
						$requete="alter table t".$i." add unique($field_keyName)";
						@pmb_mysql_query($requete);
					}
					break;
				default:
					$isfirst_criteria=true;
					@pmb_mysql_query($requete,$dbh);
					$requete="alter table $table add idiot int(1)";
					@pmb_mysql_query($requete);
					$requete="alter table $table add unique($field_keyName)";
					@pmb_mysql_query($requete);
					break;
			}
			if (!$isfirst_criteria) {
				if($last_table){
					pmb_mysql_query("drop table if exists ".$last_table,$dbh);
				}
				if($table){
					pmb_mysql_query("drop table if exists ".$table,$dbh);
				}
				if ($prefixe) {
					$last_table=$prefixe."t".$i;
				} else {
					$last_table="t".$i;	
				}
			} else {
				if($last_table){
					pmb_mysql_query("drop table if exists ".$last_table,$dbh);
				}
				
				$last_table=$table;
			}
    	}
    	return $last_table;
    }
    
    function make_hidden_search_form($url,$form_name="search_form",$target="",$close_form=true) {
    	global $search;
    	global $charset;
    	global $page;
    	
    	$r="<form name='$form_name' action='$url' style='display:none' method='post'";
    	if ($target) $r.=" target='$target'";
    	$r.=">\n";
    	
    	for ($i=0; $i<count($search); $i++) {
    		$inter="inter_".$i."_".$search[$i];
    		global $$inter;
    		$op="op_".$i."_".$search[$i];
    		global $$op;
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		//Recuperation des variables auxiliaires
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		global $$fieldvar_;
    		$fieldvar=$$fieldvar_;
    		
    		if (!is_array($fieldvar)) $fieldvar=array();
    		
    		// si sélection d'autorité et champ vide : on ne doit pas le prendre en compte
    		if($$op=='AUTHORITY'){
    			$suppr=false;
    			foreach($field as $k=>$v){
    				if($v==0){
    					unset($field[$k]);
    					unset($fieldvar[$k]);
    					$suppr=true;
    				}
    			}
    			if($suppr){
    				$field = array_values($field);
    				$fieldvar = array_values($field);
    			}
    		}
    		
    		$r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
    		$r.="<input type='hidden' name='".$inter."' value='".htmlentities($$inter,ENT_QUOTES,$charset)."'/>";
    		$r.="<input type='hidden' name='".$op."' value='".htmlentities($$op,ENT_QUOTES,$charset)."'/>";
    		for ($j=0; $j<count($field); $j++) {
    			$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
    		}
    		reset($fieldvar);
    		while (list($var_name,$var_value)=each($fieldvar)) {
    			for ($j=0; $j<count($var_value); $j++) {
    				$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
    			}
    		}
    	}
    	$r.="<input type='hidden' name='page' value='$page'/>";
    	global $dsi_active;
    	if ($dsi_active) {
    		global $id_equation;
    		$r.="<input type='hidden' name='id_equation' value='$id_equation'/>";
    	}
    		
    	global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		global $opac_view_id;
    		$r.="<input type='hidden' name='opac_view_id' value='$opac_view_id'/>";
    	}
    	
    	if ($close_form) $r.="</form>";
    	return $r;
    }
    
    function make_human_query() {
    	global $search;
    	global $msg;
    	global $charset;
    	global $include_path;
		global $pmb_multi_search_operator;
		global $lang;
		global $thesaurus_classement_mode_pmb;
		
		$r="";
    	for ($i=0; $i<count($search); $i++) {
    		$s=explode("_",$search[$i]);
    		if ($s[0]=="f") {
    			$title=$this->fixedfields[$s[1]]["TITLE"]; 
    		} elseif(array_key_exists($s[0],$this->pp)){
    			$title=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    		} elseif ($s[0]=="s") {
    			$title=$this->specialfields[$s[1]]["TITLE"];
    		}elseif ($s[0]=="authperso") {
    			$title=$this->authpersos[$s[1]]['name'];
    		}
    		$op="op_".$i."_".$search[$i];
    		global $$op;
    		//faire un test de classe et getop()
    		$operator=$this->operators[$$op];
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		
    		//Recuperation des variables auxiliaires
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		global $$fieldvar_;
    		$fieldvar=$$fieldvar_;
    		if (!is_array($fieldvar)) $fieldvar=array(); 
    		
    		$field_aff=array();
    		$fieldvar_aff=array();
    		$operator_multi = ($pmb_multi_search_operator?$pmb_multi_search_operator:"or");
    		if (array_key_exists($s[0],$this->pp)) {
    			$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
    			$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
				$q_index=$df["QUERIES_INDEX"];
	 			$q=$df["QUERIES"][$q_index[$$op]];
    			if ($q["DEFAULT_OPERATOR"])
    				$operator_multi=$q["DEFAULT_OPERATOR"];
    			for ($j=0; $j<count($field); $j++) {
	    			$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
    			}
    		} elseif ($s[0]=="f") {
    			$ff=$this->fixedfields[$s[1]];
	 			$q_index=$ff["QUERIES_INDEX"];
	 			$q=$ff["QUERIES"][$q_index[$$op]];
	 			if($fieldvar["operator_between_multiple_authorities"]){
	 				$operator_multi=$fieldvar["operator_between_multiple_authorities"][0];
	 			} else {
		 			if ($q["DEFAULT_OPERATOR"])
		    			$operator_multi=$q["DEFAULT_OPERATOR"];
	 			}
    			switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
    				case "list":
    					$options=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["OPTIONS"][0];
    					$opt=array();
    					for ($j=0; $j<count($options["OPTION"]); $j++) {
    						if (substr($options["OPTION"][$j]["value"],0,4)=="msg:") {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$msg[substr($options["OPTION"][$j]["value"],4,strlen($options["OPTION"][$j]["value"])-4)];
    						} else {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$options["OPTION"][$j]["value"];
    						}
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "query_list":
    					$requete=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["FILTERING"] == "yes") {
    						$requete = str_replace("!!acces_j!!", "", $requete);
    						$requete = str_replace("!!statut_j!!", "", $requete);
    						$requete = str_replace("!!statut_r!!", "", $requete);
    					}
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
    						$use_global = explode(",", $this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
    						for($j=0; $j<count($use_global); $j++) {
    							$var_global = $use_global[$j];
    							global $$var_global;
    							$requete = str_replace("!!".$var_global."!!", $$var_global, $requete);
    						}
    					}
    					$resultat=pmb_mysql_query($requete);
    					$opt=array();
    					while ($r_=@pmb_mysql_fetch_row($resultat)) {
    						$opt[$r_[0]]=$r_[1];
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "marc_list":
    					$opt=new marc_list($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["NAME"][0]["value"]);
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt->table[$field[$j]];
    					}
    					break;
    				case "date":
    					$field_aff[0]=format_date($field[0]);
    					break;
    				case "authoritie":
    					for($j=0 ; $j<sizeof($field) ; $j++){
    						if(is_numeric($field[$j]) && ($$op == "AUTHORITY")){
								switch ($ff['INPUT_OPTIONS']['SELECTOR']){
									case "categorie":
										$thes = thesaurus::getByEltId($field[$j]);
										$field[$j] = categories::getlibelle($field[$j],$lang)." [".$thes->libelle_thesaurus."]";
										if(isset($fieldvar["id_thesaurus"])){
											unset($fieldvar["id_thesaurus"]);
										}
										break;
									case "auteur":
										$aut=new auteur($field[$j]);
										if($aut->rejete) $field[$j] = $aut->name.', '.$aut->rejete;
										else $field[$j] = $aut->name;
										if($aut->date) $field[$j] .= " ($aut->date)";
										break;
									case "editeur":
										$ed = new editeur($field[$j]);
										$field[$j]=$ed->name;
										if ($ed->ville) 
											if ($ed->pays) $field[$j].=" ($ed->ville - $ed->pays)";
											else $field[$j].=" ($ed->ville)";
										break;	
									case "collection" :
										$coll = new collection($field[$j]);
										$field[$j] = $coll->name;
										break;
									case "subcollection" :
										$coll = new subcollection($field[$j]);
										$field[$j] = $coll->name;
										break;
									case "serie" :
										$serie = new serie($field[$j]);
										$field[$j] = $serie->name;
										break;
									case "indexint" :
										$indexint = new indexint($field[$j]);
										if ($indexint->comment) $field[$j] = $indexint->name." - ".$indexint->comment;
										else $field[$j] = $indexint->name ;
										if ($thesaurus_classement_mode_pmb != 0) {
											$field[$j]="[".$indexint->name_pclass."] ".$field[$j];
										}
										break;
									case "titre_uniforme" :
										$tu = new titre_uniforme($field[$j]);
										$field[$j] = $tu->name;
										break;
									case "notice" :
										$requete = "select if(serie_name is not null,if(tnvol is not null,concat(serie_name,', ',tnvol,'. ',tit1),concat(serie_name,'. ',tit1)),tit1) AS tit from notices left join series on serie_id=tparent_id where notice_id='".$field[$j]."' ";
										$res=pmb_mysql_query($requete);
										if($res && pmb_mysql_num_rows($res)){
											$field[$j] = pmb_mysql_result($res,0,0);
										}
										break;	
								}
    						}
    					}
    					$field_aff= $field;
    					break;
    				default:
    					$field_aff=$field;
    					break;		
    			}
    			
    			//Ajout des variables si necessaire
    			reset($fieldvar);
    			$fieldvar_aff=array();
    			while (list($var_name,$var_value)=each($fieldvar)) {
    				//Recherche de la variable par son nom
    				$vvar=$this->fixedfields[$s[1]]["VAR"];
    				for ($j=0; $j<count($vvar); $j++) {
    					if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {
    						
    						//Calcul de la visibilite
    						$varname=$vvar[$j]["NAME"];
   		 					$visibility=1;
   		 					$vis=$vvar[$j]["OPTIONS"]["VAR"][0];
   		 					if ($vis["NAME"]) {
   		 						$vis_name=$vis["NAME"];
   		 						global $$vis_name;
   		 						if ($vis["VISIBILITY"]=="no") $visibility=0;
   		 						for ($k=0; $k<count($vis["VALUE"]); $k++) {
   		 							if ($vis["VALUE"][$k]["value"]==$$vis_name) {
   		 								if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
   		 								if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
   		 								break;
   		 							}
   		 						}
   		 					}
    						
    						$var_list_aff=array();
    						$flag_aff = false;
    						
    						if ($visibility) {		
    							switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
    								case "query_list":
    									$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
       									$r_list=pmb_mysql_query($query_list);
    									while ($line=pmb_mysql_fetch_array($r_list)) {
    										$as=array_search($line[0],$var_value);
    										if (($as!==false)&&($as!==NULL)) {
    											$var_list_aff[]=$line[1];
    										}
    									}
    									if($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["ALLCHOICE"] == "yes" && count($var_list_aff) == 0){
    										$var_list_aff[]=$msg[substr($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"],4,strlen($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"])-4)];
    									}
    									$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff); 
    									$flag_aff=true;
    									break;
    								case "checkbox":
    									$value = $var_value[0];
    									$label_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["COMMENTS"][0]["LABEL"];
    									for($indice=0;$indice<count($label_list);$indice++){
    										if($value == $label_list[$indice]["VALUE"]){
    											$libelle = $label_list[$indice]["value"];
    											break; 
    										}
    									}
    									    									
    									$fieldvar_aff[]=$libelle;
    									$flag_aff=true;
    									break;
    							}
    							if($flag_aff) $fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
    						}
    					}
    				}
    			}
    		} elseif ($s[0]=="s") {
    			//appel de la fonction make_human_query de la classe du champ special
    			//Recherche du type
    			$type=$this->specialfields[$s[1]]["TYPE"];
    			for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$field_aff=$specialclass->make_human_query();
						$field_aff[0]=html_entity_decode(strip_tags($field_aff[0]),ENT_QUOTES,$charset);
						break;
					}
    			}
    		}elseif ($s[0]=="authperso") {    			
	 			$field_aff[0]=$field[0];
	    	}
    		
	   		switch ($operator_multi) {
    			case "and":
    				$op_list=$msg["search_and"];
    				break;
    			case "or":
    				$op_list=$msg["search_or"];
    				break;
    			default:
    				$op_list=$msg["search_or"];
    				break;
    		}
    		if(is_array($field_aff)){
    			$texte=implode(" ".$op_list." ",$field_aff);
    		}
    		if (count($fieldvar_aff)) $texte.=" [".implode(" ; ",$fieldvar_aff)."]";
    		
    		$inter="inter_".$i."_".$search[$i];
    		global $$inter;
    		switch ($$inter) {
    			case "and":
    				$inter_op=$msg["search_and"];
    				break;
    			case "or":
    				$inter_op=$msg["search_or"];
    				break;
    			case "ex":
    				$inter_op=$msg["search_exept"];
    				break;
    			default:
    				$inter_op="";
    				break;
    		}
    		
    		if ($inter_op) $inter_op="<strong>".htmlentities($inter_op,ENT_QUOTES,$charset)."</strong>";
    		$r.=$inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".htmlentities($texte,ENT_QUOTES,$charset).")</i> ";
    	}
    	return $r;
    }
    
    function make_unimarc_query() {
    	global $search;
    	global $msg;
    	global $charset;
    	global $include_path;

		$mt=array();
		
		//Récupération du type de recherche
		$sc_type = $this->fichier_xml;
		$sc_type = substr($sc_type,0,strlen($sc_type)-8);

		for ($i=0; $i<count($search); $i++) {
    		$sub="";
    		$s=explode("_",$search[$i]);

    		if ($s[0]=="f") {
    			$id=$search[$i];
    			$title=$this->fixedfields[$s[1]]["UNIMARCFIELD"]; 
    		} elseif (array_key_exists($s[0],$this->pp)){
    			$id=$search[$i];
    			$title=$this->pp[$s[0]]->t_fields[$s[1]]["UNIMARCFIELD"];
    		} elseif ($s[0]=="s") {
    			$id=$search[$i];
    			$title=$this->specialfields[$s[1]]["UNIMARCFIELD"];
    		}
    		$op="op_".$i."_".$search[$i];
    		global $$op;
    		//faire un test de classe et getop()
    		//$operator=$this->operators[$$op];
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		
    		//Recuperation des variables auxiliaires
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		global $$fieldvar_;
    		$fieldvar=$$fieldvar_;
    		if (!is_array($fieldvar)) $fieldvar=array(); 
    		
    		$field_aff=array();
    		
    		if(array_key_exists($s[0],$this->pp)){
    			for ($j=0; $j<count($field); $j++) {
    				$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
    			}
    		} elseif ($s[0]=="f") {
    			switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
    				case "list":
    					$options=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["OPTIONS"][0];
    					$opt=array();
    					for ($j=0; $j<count($options["OPTION"]); $j++) {
    						if (substr($options["OPTION"][$j]["value"],0,4)=="msg:") {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$msg[substr($options["OPTION"][$j]["value"],4,strlen($options["OPTION"][$j]["value"])-4)];
    						} else {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$options["OPTION"][$j]["value"];
    						}
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "query_list":
    					$requete=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["FILTERING"] == "yes") {
    						$requete = str_replace("!!acces_j!!", "", $requete);
    						$requete = str_replace("!!statut_j!!", "", $requete);
    						$requete = str_replace("!!statut_r!!", "", $requete);
    					}
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
    						$use_global = explode(",", $this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
    						for($j=0; $j<count($use_global); $j++) {
    							$var_global = $use_global[$j];
    							global $$var_global;
    							$requete = str_replace("!!".$var_global."!!", $$var_global, $requete);
    						}
    					}
    					$resultat=pmb_mysql_query($requete);
    					$opt=array();
    					while ($r_=@pmb_mysql_fetch_row($resultat)) {
    						$opt[$r_[0]]=$r_[1];
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "marc_list":
    					$opt=new marc_list($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["NAME"][0]["value"]);
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt->table[$field[$j]];
    					}
    					break;
    				case "date":
    					$field_aff[0]=format_date($field[0]);
    					break;
    				default:
    					$field_aff=$field;
    					break;		
    			}
    			
    			//Ajout des variables si necessaire
    			reset($fieldvar);
    			$fieldvar_aff=array();
    			while (list($var_name,$var_value)=each($fieldvar)) {
    				//Recherche de la variable par son nom
    				$vvar=$this->fixedfields[$s[1]]["VAR"];
    				for ($j=0; $j<count($vvar); $j++) {
    					if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {
    						
    						//Calcul de la visibilite
    						$varname=$vvar[$j]["NAME"];
   		 					$visibility=1;
   		 					$vis=$vvar[$j]["OPTIONS"]["VAR"][0];
   		 					if ($vis["NAME"]) {
   		 						$vis_name=$vis["NAME"];
   		 						global $$vis_name;
   		 						if ($vis["VISIBILITY"]=="no") $visibility=0;
   		 						for ($k=0; $k<count($vis["VALUE"]); $k++) {
   		 							if ($vis["VALUE"][$k]["value"]==$$vis_name) {
   		 								if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
   		 								if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
   		 								break;
   		 							}
   		 						}
   		 					}
    						
    						$var_list_aff=array();
    						
    						if ($visibility) {		
    							switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
    								case "query_list":
    									$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
       									$r_list=pmb_mysql_query($query_list);
    									while ($line=pmb_mysql_fetch_array($r_list)) {
    										$as=array_search($line[0],$var_value);
    										if (($as!==false)&&($as!==NULL)) {
    											$var_list_aff[]=$line[1];
    										}
    									}
    									$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff);
    									break;
    							}
    							$fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
    						}
    					}
    				}
    			}
    		} elseif ($s[0]=="s") {
    			//appel de la fonction make_unimarc_query de la classe du champ special
    			//Recherche du type
    			$type=$this->specialfields[$s[1]]["TYPE"];
    			for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$sub=$specialclass->make_unimarc_query();
						break;
					}
    			}
    		}
    		
    		$inter="inter_".$i."_".$search[$i];
    		global $$inter;
			
    		$mterm=new mterm($title,$$op,$field_aff,$fieldvar_aff,$$inter,$id);
    		if ($i==1) $mterm->sc_type=$sc_type;
    		if ((is_array($sub))&&(count($sub))) $mterm->set_sub($sub); else if (is_array($sub)) unset($mterm);
    		if ($mterm) $mt[]=$mterm;
    	}
    	return $mt;
    }
    
    function get_results($url,$url_to_search_form,$hidden_form=true,$search_target="") {
    	global $dbh;
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $affich_tris_result_liste;
    	global $pmb_allow_external_search;
 
    	$start_page=$nb_per_page_search*$page;
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		array_pop($_SESSION["session_history"]);
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}
    	
    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global $$op;
     		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$sf,$i,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[$$op])) {
    			array_pop($_SESSION["session_history"]);
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}
    	
    	$table=$this->make_search();
    	return $table;
 
    }
    
    function get_current_search_map(){
    	global $pmb_map_activate;
    	$map = "";
    	if($pmb_map_activate){
			$map = "<div id='map_container'><div id='map_search' ></div></div>";
    	}
    	return $map;
    }
    
    function check_emprises(){
    	global $pmb_map_activate;
    	global $pmb_map_max_holds;
    	global $pmb_map_size_search_result;
    	$current_search = $_SESSION['CURRENT'];
    	$map = "";
    	$size=explode("*",$pmb_map_size_search_result);
    	if(count($size)!=2)$map_size="width:800px; height:480px;";
    	$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
    	
    	$map_search_controler = new map_search_controler(null, $current_search, $pmb_map_max_holds,false);
    	$json = $map_search_controler->get_json_informations();
    	//Obligatoire pour supprimer les {}
    	$json = substr($json, 1, strlen($json)-2);
    	if($map_search_controler->have_results()){
    		$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-attr', 'dojo/parser', 'dojo/dom'], function(ready, domAttr, parser, dom){
							ready(function(){
								domAttr.set('map_search', 'data-dojo-type', 'apps/map/map_controler');
								domAttr.set('map_search', 'data-dojo-props','searchId: ".$current_search.", mode:\"search_result\", ".$json."');
    									domAttr.set('map_search', 'style', '$map_size');
    									parser.parse('map_container');
    	});
    	});
    	</script>";
    	}else{
    		$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
							ready(function(){
								domConstruct.destroy('map_container');
							});
						});
			</script>";
    	}
    	print $map;
    }
         
    function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
    	global $dbh;
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $affich_tris_result_liste;
    	global $pmb_allow_external_search;
    	global $debug;
		global $gestion_acces_active, $gestion_acces_user_notice,$PMBuserid, $pmb_allow_external_search;
		global $link_bulletin;
		global $opac_view_id; 
 				
		$start_page=$nb_per_page_search*$page;
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		array_pop($_SESSION["session_history"]);
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}
    	$recherche_externe=true;//Savoir si l'on peut faire une recherche externe à partir des critères choisis
    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global $$op;
     		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$recherche_externe=false;
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$recherche_externe=false;
    			$champ=$this->specialfields[$s[1]]["TITLE"];
		 		$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$sf,$i,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[$$op])) {
    			array_pop($_SESSION["session_history"]);
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}
    	
    	$table=$this->make_search();

		if ($acces==true && $gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	    	$this->filter_searchtable_from_accessrights($table, $PMBuserid);
		}
    	
		$requete="select count(1) from $table";
		if($res=pmb_mysql_query($requete)){
			$nb_results=pmb_mysql_result($res,0,0); 
		}else{
			array_pop($_SESSION["session_history"]);
    		error_message_history("",$msg["search_impossible"], 1);
    		exit();
		}
    	
    	
    	//gestion du tri
    	$has_sort = false;
 		if ($nb_results <= $pmb_nb_max_tri) {
			if ($_SESSION["tri"]) {
				$sort = new sort('notices','base');
				$requete = $sort->appliquer_tri($_SESSION["tri"],"SELECT * FROM " . $table, "notice_id", $start_page, $nb_per_page_search);
				$table = $sort->table_tri_tempo;	
				$has_sort = true; 
			}
		}
		// fin gestion tri
    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		array_pop($_SESSION["session_history"]);
    		error_message_history("", $this->error_message, 1);
    		exit();
    	}
    	
    	if ($hidden_form)
    		print $this->make_hidden_search_form($url);
    	
    	$requete="select $table.*,notices.niveau_biblio from ".$table.",notices where notices.notice_id=$table.notice_id"; 
    	if(count($search) > 1 && !$has_sort) 
    		$requete .= " order by index_serie, tnvol, index_sew";
    	$requete .= " limit ".$start_page.",".$nb_per_page_search;
    	
    	$resultat=pmb_mysql_query($requete,$dbh);
    	
    	$human_requete = $this->make_human_query();
    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
		if ($debug) print "<br />".$this->serialize_search();
		if ($nb_results) {
			print " => ".$nb_results." ".$msg["1916"]."<br />\n";
			print $begin_result_liste;
			if ($this->rec_history) {
				//Affichage des liens paniers et impression
				$current=$_SESSION["CURRENT"];
				if ($current!==false) {
					$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
					print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare$tri_id_info','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\"><img src='./images/basket_small_20x20.gif' border='0' align='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare$tri_id_info','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); w.focus(); return false;\"><img src='./images/print.gif' border='0' align='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
					print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download',500,600,-2,-2,'scrollbars=yes,menubar=0'); return false;\"><img src='./images/upload.gif' border='0' align='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
					if ($pmb_allow_external_search){
						if($recherche_externe){
							$tag_a="href='catalog.php?categ=search&mode=7&from_mode=6&external_type=multi'";
						}else{
							$tag_a="onClick=\"alert('".$msg["search_interdite_externe"]."')\"";
						}
						print "&nbsp;<a ".$tag_a."  title='".$msg["connecteurs_external_search_sources"]."'><img src='./images/external_search.png' border='0' align='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
					}
					if ($nb_results<=$pmb_nb_max_tri) {
						print $affich_tris_result_liste;
					}
				}
			}
		} else print "<br />".$msg["1915"]." ";
		print "<input type='button' class='bouton' onClick=\"document.search_form.action='$url_to_search_form'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
		global $dsi_active;
		if ($dsi_active && !$opac_view_id) {
			global $id_equation, $priv_pro, $id_empr;
			if ($id_equation) $mess_bouton = $msg['dsi_sauvegarder_equation'] ;
				else $mess_bouton = $msg["dsi_transformer_equation"] ;
			print "&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_dsi'].submit(); \" value=\"".$mess_bouton."\"/>
						<form name='transform_dsi' style='display:none;' method='post' action='./dsi.php'>";
			if ($priv_pro=="PRI") print "
						<input type=hidden name='categ' value='bannettes' />
						<input type=hidden name='sub' value='abo' />
						<input type=hidden name='suite' value='transform_equ' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='id_empr' value='$id_empr' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
				else print "
						<input type=hidden name='categ' value='equations' />
						<input type=hidden name='sub' value='gestion' />
						<input type=hidden name='suite' value='transform' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
			}
		global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		if($opac_view_id){
    			$mess_bouton = $msg['opac_view_sauvegarder_equation'] ;			
				print "
					&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_opac_view'].submit(); \" value=\"".$mess_bouton."\"/>
					<form name='transform_opac_view' style='display:none;' method='post' action='./admin.php?categ=opac&sub=opac_view&section=list&action=form&opac_view_id=$opac_view_id'>						
							<input type=hidden name='suite' value='transform_equ' />
							<input type=hidden name='opac_view_id' value='$opac_view_id' />
							<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
					</form>"; 
    		}   		
    	}	
		// transformation de la recherche en multicriteres: on reposte tout avec mode=8
    	if(!$opac_view_id){
    		print "&nbsp;<input  type='button' class='bouton' onClick='document.search_transform.submit(); return false;' value=\"".$msg["search_notice_to_expl_transformation"]."\"/>";
			print "<form name='search_transform' action='./catalog.php?categ=search&mode=8&sub=launch' style=\"display:none\" method='post'>";	
			$memo_search="";
			foreach($_POST as $key =>$val) {
				if($val) {
					if(is_array($val)) {
						foreach($val as $cle=>$val_array) {
							if(is_array($val_array)){
								foreach($val_array as $valeur){
									$memo_search.= "<input type='hidden' name=\"".$key."[".$cle."][]\" value='".htmlentities($valeur,ENT_QUOTES,$charset)."'/>";
								}
							} else $memo_search.= "<input type='hidden' name='".$key."[]' value='".htmlentities($val_array,ENT_QUOTES,$charset)."'/>";
						}
					}
					else $memo_search.="<input type='hidden' name='$key' value='$val'/>";
				}		
			}	
			print "$memo_search</form>";
    	}
		//transformation en set pour connecteur externe
		global $id_connector_set;
		$id_connector_set+=0;
		//Il faut que l'on soit passé par le formulaire d'édition de set pour avoir $id_connector_set pour ne pas avoir le bouton tout le temps vu qu'il sert rarement
		if ($pmb_allow_external_search && (SESSrights & ADMINISTRATION_AUTH) && $id_connector_set) {
			//Il faut qu'il y ait des sets multi critères si on veut pouvoir associer la recherche à quelque chose
			if (connector_out_sets::get_typed_set_count(2)) {
				print '<form name="export_to_outset" style="display:none;" method="post" action="./admin.php?categ=connecteurs&sub=out_sets&action=import_notice_search_into_set&candidate_id='.$id_connector_set.'"><input type="hidden" name="toset_search" value="'.htmlentities($this->serialize_search(),ENT_QUOTES,$charset).'" /></form>';
				print '&nbsp;<input type="button" onClick="document.forms[\'export_to_outset\'].submit(); " class="bouton" value="'.htmlentities($msg["search_notice_to_connector_out_set"] ,ENT_QUOTES, $charset).'">';
			}
		}
		
		print $this->get_current_search_map();
		
    	while ($r=pmb_mysql_fetch_object($resultat)) {
    		if($nb++>5)	$recherche_ajax_mode=1;
    		switch($r->niveau_biblio) {
				case 'm' :
					// notice de monographie
					$nt = new mono_display($r->notice_id, 6, $this->link, 1, $this->link_expl, '', $this->link_explnum,1, 0, 1, 1, "", 1, false,true,$recherche_ajax_mode,1);
					break ;
				case 's' :
					// on a affaire a un periodique
					// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
					$nt = new serial_display($r->notice_id, 6, $this->link_serial, $this->link_analysis, $this->link_bulletin, "", $this->link_explnum_serial, 0, 0, 1, 1, true, 1  ,$recherche_ajax_mode);
					break;
				case 'a' :
					// on a affaire a un article
					// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
					$nt = new serial_display($r->notice_id, 6, $this->link_serial, $this->link_analysis, $this->link_bulletin, "", $this->link_explnum_analysis, 0, 0, 1, 1, true, 1  ,$recherche_ajax_mode);
					break;
				case 'b' :
					// on a affaire a un bulletin
					$rqt_bull_info = "SELECT s.notice_id as id_notice_mere, bulletin_id as id_du_bulletin, b.notice_id as id_notice_bulletin FROM notices as s, notices as b, bulletins WHERE b.notice_id=$r->notice_id and s.notice_id=bulletin_notice and num_notice=b.notice_id";
					$bull_ids=@pmb_mysql_fetch_object(pmb_mysql_query($rqt_bull_info));
					if(!$link_bulletin){
						$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='.$bull_ids->id_du_bulletin;
					} else {
						$link_bulletin = str_replace("!!id!!",$bull_ids->id_du_bulletin,$link_bulletin);
					}
					if ($this->link_explnum_bulletin) {
						$link_explnum_bulletin = str_replace("!!bul_id!!",$bull_ids->id_du_bulletin,$this->link_explnum_bulletin);
					} else {
						$link_explnum_bulletin = "";
					}
					$nt = new mono_display($r->notice_id, 6, $link_bulletin, 1, $this->link_expl, '', $link_explnum_bulletin,1, 0, 1, 1, "", 1  , false,true,$recherche_ajax_mode);
					$link_bulletin ='';
					break;
			}
    		echo "<div class='row'>".$nt->result."</div>";
    	}

    	//Gestion de la pagination
    	if ($nb_results) {
	  	  	$n_max_page=ceil($nb_results/$nb_per_page_search);
	  	  	$etendue=10;
	   	 	
	   	 	if (!$page) $page_en_cours=0 ;
				else $page_en_cours=$page ;
				
	    	//Première
			if(($page_en_cours+1)-$etendue > 1) {
				$nav_bar .= "<a href='#' onClick=\"document.search_form.page.value=0;";
				if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
				$nav_bar .= "document.search_form.submit(); return false;\"><img src='./images/first.gif' border='0' alt='".$msg['first_page']."' hspace='6' align='middle' title='".$msg['first_page']."' /></a>";
			}
		
	   	 	// affichage du lien precedent si necessaire
   		 	if ($page>0) {
   		 		$nav_bar .= "<a href='#' onClick='document.search_form.page.value-=1; ";
   		 		if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
   		 		$nav_bar .= "document.search_form.submit(); return false;'>";
   	 			$nav_bar .= "<img src='./images/left.gif' border='0'  title='".$msg[48]."' alt='[".$msg[48]."]' hspace='3' align='middle'/>";
    			$nav_bar .= "</a>";
    		}
        	
			$deb = $page_en_cours - 10 ;
			if ($deb<0) $deb=0;
			for($i = $deb; ($i < $n_max_page) && ($i<$page_en_cours+10); $i++) {
				if($i==$page_en_cours) $nav_bar .= "<strong>".($i+1)."</strong>";
					else {
						$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=".($i)."; ";
    					if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
		    			$nav_bar .= "document.search_form.submit(); return false;\">";
    					$nav_bar .= ($i+1);
    					$nav_bar .= "</a>";
						}
				if($i<$n_max_page) $nav_bar .= " "; 
				}
        	
			if(($page+1)<$n_max_page) {
    			$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=parseInt(document.search_form.page.value)+parseInt(1); ";
    			if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
    			$nav_bar .= "document.search_form.submit(); return false;\">";
    			$nav_bar .= "<img src='./images/right.gif' border='0' title='".$msg[49]."' alt='[".$msg[49]."]' hspace='3' align='middle'>";
    			$nav_bar .= "</a>";
        		} else 	$nav_bar .= "";
        	
        	//Dernière
        	if((($page_en_cours+1)+$etendue)<$n_max_page){
        		$nav_bar .= "<a href='#' onClick=\"document.search_form.page.value=".($n_max_page-1).";";
        		if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
        		$nav_bar .= "document.search_form.submit(); return false;\"><img src='./images/last.gif' border='0' alt='".$msg['last_page']."' hspace='6' align='middle' title='".$msg['last_page']."' /></a>";
        	}
        	
			$nav_bar = "<div align='center'>$nav_bar</div>";
	   	 	echo $nav_bar ;
	   	 	
    	}  	
    }
    
    function show_results_unimarc($url,$url_to_search_form,$hidden_form=true,$search_target="") {
    	global $dbh;
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $affich_tris_result_liste;
    	global $pmb_allow_external_search;
    	global $opac_view_id; 
    	$start_page=$nb_per_page_search*$page;
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}
    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global $$op;
     		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[$$op])) {
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}
    	global $inter_1_f_1;
    	$table=$this->make_search();
    	$requete="select count(1) from $table";
    	if($res=pmb_mysql_query($requete)){
			$nb_results=pmb_mysql_result($res,0,0); 
		}else{
    		error_message_history("",$msg["search_impossible"], 1);
    		exit();
		}
    	
    	/*
    	//gestion du tri
    	if ($nb_results<=$pmb_nb_max_tri) {
			if ($_SESSION["tri"]) {
				$sort=new sort('notices','base');
				$sort->table_tri_tempo=$table;
				$sort->table_primary_tri_tempo="notice_id";
				$sort->limit="limit ".$start_page.",".$nb_per_page_search;
				$requete=$sort->appliquer_tri();
				if (substr($requete,0,1)=="(") $creer_table_tempo="CREATE TEMPORARY TABLE tri_tempo ENGINE=MyISAM ".$requete."";
					else $creer_table_tempo="CREATE TEMPORARY TABLE tri_tempo ENGINE=MyISAM (".$requete.")";
				@pmb_mysql_query($creer_table_tempo);
				$modif_primaire="ALTER TABLE tri_tempo PRIMARY KEY notice_id";
				@pmb_mysql_query($modif_primaire);
				$table="tri_tempo";
			}
    	} 
		// fin gestion tri
		*/
		
    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		error_message_history("", $this->error_message, 1);
    		exit();
    	}
    	
    	if ($hidden_form)
    		print $this->make_hidden_search_form($url);
    	
    	//$requete="select $table.* from $table left join entrepots on recid=notice_id and (ufield='200' and usubfield='a') or (recid is null) order by i_value"; 
    	//$requete .= " limit ".$start_page.",".$nb_per_page_search;
    	//$resultat=pmb_mysql_query($requete,$dbh);
		
		$requete = "select * from $table";
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		
		$resultat=pmb_mysql_query($requete,$dbh);
		
    	$human_requete = $this->make_human_query();
    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
		
		if ($nb_results) {
			print " => ".$nb_results." ".$msg["1916"]."<br />\n";
			print $begin_result_liste;
			if ($this->rec_history) {
				//Affichage des liens paniers et impression
				$current=$_SESSION["CURRENT"];
				if ($current!==false) {
					$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
					print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare$tri_id_info','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\"><img src='./images/basket_small_20x20.gif' border='0' align='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare$tri_id_info','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); return false;\"><img src='./images/print.gif' border='0' align='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				}
			}
		} else print "<br />".$msg["1915"]." ";
		print "<input type='button' class='bouton' onClick=\"document.search_form.action='$url_to_search_form'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
		
		print "<input type='button' class='bouton' onClick=\"integer_notices_submit();\" value=\"".$msg["external_search_integer_results"]."\"/>";
		
		global $dsi_active;
		if (($dsi_active)&&false) {
			global $id_equation, $priv_pro, $id_empr;
			if ($id_equation) $mess_bouton = $msg['dsi_sauvegarder_equation'] ;
				else $mess_bouton = $msg["dsi_transformer_equation"] ;
			print "&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_dsi'].submit(); \" value=\"".$mess_bouton."\"/>
						<form name='transform_dsi' style='display:none;' method='post' action='./dsi.php'>";
			if ($priv_pro=="PRI") print "
						<input type=hidden name='categ' value='bannettes' />
						<input type=hidden name='sub' value='abo' />
						<input type=hidden name='suite' value='transform_equ' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='id_empr' value='$id_empr' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
				else print "
						<input type=hidden name='categ' value='equations' />
						<input type=hidden name='sub' value='gestion' />
						<input type=hidden name='suite' value='transform' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
			}
   		global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		if($opac_view_id){
    			$mess_bouton = $msg['opac_view_sauvegarder_equation'];			
				print "
					&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_opac_view'].submit(); \" value=\"".$mess_bouton."\"/>
					<form name='transform_opac_view' style='display:none;' method='post' action='./dsi.php'>
							<input type=hidden name='categ' value='opac' />
							<input type=hidden name='sub' value='opac_view' />
							<input type=hidden name='section' value='list' />
							<input type=hidden name='action' value='form' />
							<input type=hidden name='suite' value='transform_equ' />
							<input type=hidden name='opac_view_id' value='$opac_view_id' />
							<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
					</form>";    		
    		}
    	}				
			
		flush();
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}
		
		print "
		<div class='row'>
		<input type='button' id='select_all' class='bouton' onClick='external_notices_select_all()' value='".$msg['tout_cocher_checkbox']."'/>
		<script type='text/javascript'>
			function external_notices_select_all(){
				switch(document.getElementById('select_all').todo){
		    		case 'unselect':
		    			if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
			    			for (var i=0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
								if(!document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
			    					document.forms.integer_notices['external_notice_to_integer[]'][i].checked=false;
			    				}
			    			}
		    			} else {
							if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
		    					document.forms.integer_notices['external_notice_to_integer[]'].checked=false;
		    				}		    			
    					}
		    			document.getElementById('select_all').value='".$msg['tout_cocher_checkbox']."';
		    			document.getElementById('select_all').todo = 'select';
		    			break;				
		    		case 'select' :
		    		default :
		    			if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
			    			for (var i=0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
								if(!document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
			    					document.forms.integer_notices['external_notice_to_integer[]'][i].checked=true;
			    				}
			    			}
		    			} else {
							if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
		    					document.forms.integer_notices['external_notice_to_integer[]'].checked=true;
		    				}		    			
    					}
		    			document.getElementById('select_all').value='".$msg['tout_decocher_checkbox']."';
		    			document.getElementById('select_all').todo = 'unselect';
		    			break;

		    	}
		    					
    		}
    		function integer_notices_submit(){
				var ok = false;
				if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
	    			for (var i = 0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
						if(document.forms.integer_notices['external_notice_to_integer[]'][i].checked && !document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
							ok = true;
							break;
	    				}
	    			}
	   			} else {
	   				if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
	    				document.forms.integer_notices['external_notice_to_integer[]'].checked=true;
						ok = true;
		    		}
	   			}
    			if(!ok){
    				alert('".$msg['integer_notices_error']."');
    			}else{
    				document.forms.integer_notices.submit();
    			}
    		}
		</script>
		</div>
		<form name='integer_notices' action='./catalog.php?categ=search&mode=7&sub=integre_notices' method='post'>
			<input type=hidden name='serialized_search' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
			<input type='hidden' name='page' value='".htmlentities($page,ENT_QUOTES,$charset)."'/>	";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
    		/*if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
				// notice de monographie
				$nt = new mono_display($r->notice_id, 6, $this->link, 1, $this->link_expl, '', $this->link_explnum,1, 0, 1, 1, "", 1);
			} else {
				// on a affaire a un periodique
				$nt = new serial_display($r->notice_id, 6, $this->link_serial, $this->link_analysis, $this->link_bulletin, "", $this->link_explnum_serial, 0, 0, 1, 1 );
			}*/
			$nt = new mono_display_unimarc($r->notice_id,6, 1, 0, 1, false, $entrepots_localisations);
    		echo "<div class='row'>".$nt->result."</div>";
    	}
    	print "</form>";
    	//Gestion de la pagination
    	if ($nb_results) {
	  	  	$n_max_page=ceil($nb_results/$nb_per_page_search);
	   	 	
	   	 	if (!$page) $page_en_cours=0 ;
				else $page_en_cours=$page ;
		
	   	 	// affichage du lien precedent si necessaire
   		 	if ($page>0) {
   		 		$nav_bar .= "<a href='#' onClick='document.search_form.page.value-=1; ";
   		 		if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
   		 		$nav_bar .= "document.search_form.submit(); return false;'>";
   	 			$nav_bar .= "<img src='./images/left.gif' border='0'  title='".$msg[48]."' alt='[".$msg[48]."]' hspace='3' align='middle'/>";
    			$nav_bar .= "</a>";
    		}
        	
			$deb = $page_en_cours - 10 ;
			if ($deb<0) $deb=0;
			for($i = $deb; ($i < $n_max_page) && ($i<$page_en_cours+10); $i++) {
				if($i==$page_en_cours) $nav_bar .= "<strong>".($i+1)."</strong>";
					else {
						$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=".($i)."; ";
    					if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
		    			$nav_bar .= "document.search_form.submit(); return false;\">";
    					$nav_bar .= ($i+1);
    					$nav_bar .= "</a>";
						}
				if($i<$n_max_page) $nav_bar .= " "; 
				}
        	
			if(($page+1)<$n_max_page) {
    			$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=parseInt(document.search_form.page.value)+parseInt(1); ";
    			if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
    			$nav_bar .= "document.search_form.submit(); return false;\">";
    			$nav_bar .= "<img src='./images/right.gif' border='0' title='".$msg[49]."' alt='[".$msg[49]."]' hspace='3' align='middle'>";
    			$nav_bar .= "</a>";
        		} else 	$nav_bar .= "";
			$nav_bar = "<div align='center'>$nav_bar</div>";
	   	 	echo $nav_bar ;
	   	 	
    	}  	
    }
    
    function filter_searchtable_from_accessrights($table, $PMBUserId) {
    	global $dbh;
    	global $gestion_acces_active,$gestion_acces_user_notice;
    	 
    	if($gestion_acces_active && $gestion_acces_user_notice){
	    	//droits d'acces lecture notice
			$ac= new acces();
			$dom_1= $ac->setDomain(1);
			$usr_prf = $dom_1->getUserProfile($PMBUserId);
			
			$requete = "delete from $table using $table, acces_res_1 ";
			$requete.= "where ";
			$requete.= "$table.notice_id = res_num and usr_prf_num=".$usr_prf." ";
			$requete.= "and (((res_rights ^ res_mask) & 4)=0) ";
			pmb_mysql_query($requete, $dbh);
    	}
    }
    
 // fonction de calcul de la visibilite d'un champ de recherche
    function visibility($ff) {
    	
    	if (!count($ff["VARVIS"])) return $ff["VISIBILITY"];
     	 
    	for ($i=0; $i<count($ff["VARVIS"]); $i++) {
    		$name=$ff["VARVIS"][$i]["NAME"] ;
    		global $$name;
    		$visibilite=$ff["VARVIS"][$i]["VISIBILITY"] ;
    		if (isset($ff["VARVIS"][$i]["VALUE"][$$name])) {
    			if ($visibilite) 
    				$test = $ff["VARVIS"][$i]["VALUE"][$$name] ;
    			else  
    				$test = $visibilite || $ff["VARVIS"][$i]["VALUE"][$$name] ;
    			return $test ;
    		}
    	} // fin for
    	// aucune condition verifiee : on retourne la valeur par defaut
    	return $ff["VISIBILITY"] ;
    }
    
    //Templates des listes d'operateurs
    function show_form($url,$result_url,$result_target='',$memo_url='') {
    	global $charset;
    	global $search;
    	global $add_field;
    	global $delete_field;
    	global $launch_search;
    	global $page;
    	global $search_form;
    	global $msg;
    	global $include_path;   	
    	global $option_show_expl,$option_show_notice_fille;
    	global $pmb_extended_search_auto;
		
    	if($option_show_expl)$option_show_expl_check="checked='checked'";
    	if($option_show_notice_fille)$option_show_notice_fille_check="checked='checked'";
    	$option="
    		<div class='row'>
    			<h3>".$msg['search_option_show_title']."</h3>
    			<input $option_show_expl_check value='1' name='option_show_expl' id='option_show_expl'  type='checkbox'>".$msg["search_option_show_expl"]."
    			<input $option_show_notice_fille_check value='1' name='option_show_notice_fille' id='option_show_notice_fille'  type='checkbox'>".$msg["search_option_show_notice_fille"]."
    		</div><div class='row'>&nbsp;</div>";    	
    	$search_form=str_replace("<!--!!limitation_affichage!!-->",$option,$search_form);
    	
    	if (($add_field)&&(($delete_field==="")&&(!$launch_search)))
    		$search[]=$add_field;
    	
    	$search_form=str_replace("!!url!!",$url,$search_form);
    	if(!$memo_url) $memo_url="catalog.php?categ=search_perso&sub=form";
    	$search_form=str_replace("!!memo_url!!",$memo_url,$search_form);
    	
    	//Génération de la liste des champs possibles
    	if($this->limited_search){ 
    		$search_form = str_replace("!!limit_search!!","<input type='hidden' id='limited_search' name='limited_search' />",$search_form);  		
	    	$limit_search = " this.form.limited_search.value='1'; ";
    	} else {
    		$search_form = str_replace("!!limit_search!!","",$search_form);
    		$limit_search = "";
    	}
    	if ($pmb_extended_search_auto) $r="<select name='add_field' id='add_field' onChange=\"if (this.form.add_field.value!='') { enable_operators();this.form.action='$url'; this.form.target=''; $limit_search this.form.submit();} else { alert('".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."'); }\" >\n";
    	else $r="<select name='add_field' id='add_field'>\n";
    	$r.="<option value='' style='color:#000000'>".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."</font></option>\n";

    	//Champs fixes
    	if($this->fixedfields){
	    	reset($this->fixedfields);
	    	$open_optgroup=0;
			$open_optgroup_deja_affiche=0;
			$open_optgroup_en_attente_affiche=0;
	    	while (list($id,$ff)=each($this->fixedfields)) {
	    		if ($ff["SEPARATOR"]) {
	    			if ($open_optgroup) $r.="</optgroup>\n";
	    			// $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
	    			$r_opt_groupe="<optgroup label='".htmlentities($ff["SEPARATOR"],ENT_QUOTES,$charset)."' class='erreur'>\n";
	    			$open_optgroup=0;
	    			$open_optgroup_deja_affiche=0;
	    			$open_optgroup_en_attente_affiche=1;
	    		}
	    		//if ($ff["VISIBLE"]) {
	    		if ($this->visibility($ff)) {
	    			if ($open_optgroup_en_attente_affiche && !$open_optgroup_deja_affiche) {
	    				$r.=$r_opt_groupe ;
	    				$open_optgroup_deja_affiche = 1 ;
	    				$open_optgroup_en_attente_affiche = 0 ;
	    				$open_optgroup = 1 ; 
	    			}
	    			$r.="<option value='f_".$id."' style='color:#000000'>".htmlentities($ff["TITLE"],ENT_QUOTES,$charset)."</font></option>\n";
	    		}
	    	}
    	}

    	//Champs fixes
    	/*reset($this->fixedfields);
    	$open_optgroup=0;
    	while (list($id,$ff)=each($this->fixedfields)) {
    		if ($ff["SEPARATOR"]) {
    			if ($open_optgroup) $r.="</optgroup>\n";
    			// $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
    			$r.="<optgroup label='".htmlentities($ff["SEPARATOR"],ENT_QUOTES,$charset)."' class='erreur'>\n";
    			$open_optgroup=1;
    		}
    		$r.="<option value='f_".$id."' style='color:#000000'>".htmlentities($ff["TITLE"],ENT_QUOTES,$charset)."</font></option>\n";
    	}*/
    	
    	//Champs dynamiques
    	if ($open_optgroup) $r.="</optgroup>\n";
    	// $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
    	if(!$this->dynamics_not_visible){
    		foreach ( $this->dynamicfields as $key => $value ) {
    			if(!$this->pp[key]->no_special_fields){
       				$r.="<optgroup label='".$msg["search_custom_".$value["TYPE"]]."' class='erreur'>\n";
	   		 		reset($this->pp[$key]->t_fields);
	   		 		while (list($id,$df)=each($this->pp[$key]->t_fields)) {
	    				$r.="<option value='".$key."_".$id."' style='color:#000000'>".htmlentities($df["TITRE"],ENT_QUOTES,$charset)."</option>\n";
	    			}
	    			$r.="</optgroup>\n";
	    		}
			}
    	}    	
  
    	//Champs autorités perso
    	if ($open_optgroup) $r.="</optgroup>\n";
    	$r_authperso="";
    	foreach($this->authpersos as $authperso){
    		if(!$authperso['gestion_multi_search'])continue;
    		$r_authperso.="<optgroup label='".$msg["authperso_multi_search_by_field_title"]." : ".$authperso['name']."' class='erreur'>\n";
    		$r_authperso.="<option value='authperso_".$authperso['id']."' style='color:#000000'>".$msg["authperso_multi_search_tous_champs_title"]."</option>\n";
    		foreach($authperso['fields'] as $field){
    			$r_authperso.="<option value='a_".$field['id']."' style='color:#000000'>".htmlentities($field['label'],ENT_QUOTES,$charset)."</option>\n";
    		}
    		$r_authperso.="</optgroup>\n";
    	}    	
    	$r.=$r_authperso;
    	
    	//Champs speciaux
    	if (!$this->specials_not_visible && $this->specialfields) {
   		 	while (list($id,$sf)=each($this->specialfields)) {
   		 		if($sf['VISIBLE']){
   		 			if ($sf["SEPARATOR"]) {
	   		 			if ($open_optgroup) $r.="</optgroup>\n";
	  		  			// $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
	    				$r.="<optgroup label='".htmlentities($sf["SEPARATOR"],ENT_QUOTES,$charset)."' class='erreur'>\n";
	    				$open_optgroup=1;
	    			}
	    			$r.="<option value='s_".$id."' style='color:#000000'>".htmlentities($sf["TITLE"],ENT_QUOTES,$charset)."</font></option>\n";
    			}
   		 	}
    	}
    	$r.="</select>";
    	   	
    	$search_form=str_replace("!!field_list!!",$r,$search_form);
    	
    	//Affichage des champs deja saisis
    	$r="";
    	$n=0;
    	$r.="<table class='table-no-border'>\n";
    	for ($i=0; $i<count($search); $i++) {
    		if ((string)$i!=$delete_field) {
    			$f=explode("_",$search[$i]);
    			//On regarde si l'on doit masquer des colonnes
    			$notdisplaycol=array();
    			if ($f[0]=="f") {
    				if($this->fixedfields[$f[1]]["NOTDISPLAYCOL"]){
    					$notdisplaycol=explode(",",$this->fixedfields[$f[1]]["NOTDISPLAYCOL"]);
    				}
    			} elseif ($f[0]=="s") {
    				if($this->specialfields[$f[1]]["NOTDISPLAYCOL"]){
    					$notdisplaycol=explode(",",$this->specialfields[$f[1]]["NOTDISPLAYCOL"]);
    				}
    			} elseif (array_key_exists($f[0],$this->pp)) {
    				if($this->pp[$f[0]]->t_fields[$f[1]]["NOTDISPLAYCOL"]){
    					$notdisplaycol=explode(",",$this->pp[$f[0]]->t_fields[$f[1]]["NOTDISPLAYCOL"]);
    				}
    			}
    			
    			$r.="<tr>";
    			$r.="<td".(in_array("1",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 1
    			$r.="<input type='hidden' name='search[]' value='".$search[$i]."'>";
    			$r.="</td>";
    			$r.="<td class='search_first_column' ".(in_array("2",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 2
    			if ($n>0) {
    				$inter="inter_".$i."_".$search[$i];
    				global $$inter;
    				$r.="<select name='inter_".$n."_".$search[$i]."'>";
    				$r.="<option value='and' ";
    				if ($$inter=="and")
    					$r.=" selected";
    				$r.=">".$msg["search_and"]."</option>";
    				$r.="<option value='or' ";
    				if ($$inter=="or")
    					$r.=" selected";
    				$r.=">".$msg["search_or"]."</option>";
    				$r.="<option value='ex' ";
    				if ($$inter=="ex")
    					$r.=" selected";
    				$r.=">".$msg["search_exept"]."</option>";
    				$r.="</select>";
    			} else $r.="&nbsp;";
    			$r.="</td>";
    			
    			$r.="<td ".(in_array("3",$notdisplaycol)?"style='display:none;'":"")."><span class='search_critere'>";//Colonne 3
    			if ($f[0]=="f") {
    				$r.=htmlentities($this->fixedfields[$f[1]]["TITLE"],ENT_QUOTES,$charset);
    			} elseif ($f[0]=="s") {
    				$r.=htmlentities($this->specialfields[$f[1]]["TITLE"],ENT_QUOTES,$charset);
    			} elseif (array_key_exists($f[0],$this->pp)) {
    				$r.=htmlentities($this->pp[$f[0]]->t_fields[$f[1]]["TITRE"],ENT_QUOTES,$charset);
    			}elseif ($f[0]=="authperso") {
    				$r.=htmlentities($this->authpersos[$f[1]]['name'],ENT_QUOTES,$charset);					
    			}
    			$r.="</td>";
    			//Recherche des operateurs possibles
    			$r.="<td ".(in_array("4",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 4
    			$op="op_".$i."_".$search[$i];
    			global $$op;
    			if ($f[0]=="f") {	
     				$r.="<select name='op_".$n."_".$search[$i]."' id='op_".$n."_".$search[$i]."'";
					//gestion des autorités
					$onchange ="";

					if (isset($this->fixedfields[$f[1]]["QUERIES_INDEX"]["AUTHORITY"])){
						$selector=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["SELECTOR"];
						$p1=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P1"];
						$p2=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P2"];
						$onchange ="onchange='operatorChanged(\"".$n."_".$search[$i]."\",this.value);'";
					}
     				$r.="$onchange>\n";
    				for ($j=0; $j<count($this->fixedfields[$f[1]]["QUERIES"]); $j++) {
    					$q=$this->fixedfields[$f[1]]["QUERIES"][$j];
    					$r.="<option value='".$q["OPERATOR"]."' ";
    					if ($$op==$q["OPERATOR"]) $r.=" selected";
    					$r.=">".htmlentities($this->operators[$q["OPERATOR"]],ENT_QUOTES,$charset)."</option>\n";
    				}
    				$r.="</select>";
    			} elseif (array_key_exists($f[0],$this->pp)) {
    				$datatype=$this->pp[$f[0]]->t_fields[$f[1]]["DATATYPE"];
    				$type=$this->pp[$f[0]]->t_fields[$f[1]]["TYPE"];
    				$df=$this->get_id_from_datatype($datatype, $f[0]);
    				$r.="<select name='op_".$n."_".$search[$i]."'>\n";
    				for ($j=0; $j<count($this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"]); $j++) {
    					$q=$this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"][$j];
    					$as=array_search($type,$q["NOT_ALLOWED_FOR"]);
    					if (!(($as!==null)&&($as!==false))) {
    						$r.="<option value='".$q["OPERATOR"]."' ";
    						if ($$op==$q["OPERATOR"]) $r.="selected";
    						$r.=">".htmlentities($this->operators[$q["OPERATOR"]],ENT_QUOTES,$charset)."</option>\n";
    					}
    				}
    				$r.="</select>";
    				$r.="&nbsp;";
    			} elseif ($f[0]=="s") {
					//appel de la fonction get_input_box de la classe du champ special
					$type=$this->specialfields[$f[1]]["TYPE"];
   			 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
						if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
							$sf=$this->specialfields[$f[1]];
							require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
							$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($f[1],$sf,$n,$this);
							$q=$specialclass->get_op();
							if (count($q)) {
								$r.="<span class='search_sous_critere'><select name='op_".$n."_".$search[$i]."'>\n";
								foreach ($q as $key => $value) {
									$r.="<option value='".$key."' ";
	    							if ($$op==$key) $r.="selected";
	    							$r.=">".htmlentities($value,ENT_QUOTES,$charset)."</option>\n";
								}
								$r .= "</select></span>";
							} else print "&nbsp;";
							break;
						}
    				}
    			}elseif ($f[0]=="authperso") {
					$r.="<span class='search_sous_critere'>
					<select name='op_".$n."_".$search[$i]."' >
						<option  value='BOOLEAN' selected>".htmlentities($this->operators["BOOLEAN"],ENT_QUOTES,$charset)."</option>\n					
					</span>";
    			}
    			$r.="</td>";
    			
    			//Affichage du champ de saisie
    			$r.="<td ".(count($notdisplaycol)?"colspan='".(count($notdisplaycol)+1)."'":"")." ".(in_array("5",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 5
    			$r.=$this->get_field($i,$n,$search[$i],$this->pp);
    			$r.="</td>";
    			$delnotallowed=false;
    			if ($f[0]=="f") {
    				$delnotallowed=$this->fixedfields[$f[1]]["DELNOTALLOWED"];
       			} elseif ($f[0]=="s") {
    				$delnotallowed=$this->specialfields[$f[1]]["DELNOTALLOWED"];
    			}
    			if($this->limited_search) 
    				$script_limit = " this.form.limited_search.value='0'; ";
    			else $script_limit = "";
    			$r.="<td ".(in_array("6",$notdisplaycol)?"style='display:none;'":"")."><span class='search_cancel'>".(!$delnotallowed?"<input type='button' class='bouton' value='".$msg["raz"]."' onClick=\"enable_operators(); this.form.delete_field.value='".$n."'; this.form.action='$url'; this.form.target=''; $script_limit this.form.submit();\">":"&nbsp;")."</td>";//Colonne 6
    			$r.="</tr>\n";
    			$n++;
    		}
    	}
    	$r.="</table>\n";
    	
    	//Recherche explicite
    	$r.="<input type='hidden' name='explicit_search' value='1'/>\n";
    	
    	$search_form=str_replace("!!already_selected_fields!!",$r,$search_form);
    	$search_form=str_replace("!!page!!",$page,$search_form);
    	$search_form=str_replace("!!result_url!!",$result_url,$search_form);

    	global $dsi_active;
    	if ($dsi_active) {
    		global $id_equation;
    		$search_form=str_replace("!!id_equation!!",$id_equation,$search_form);
    	} else {
    		$search_form=str_replace("!!id_equation!!","",$search_form);
    	}

    	global $id_search_persopac;
    	if ($id_search_persopac) {
    		$search_form=str_replace("!!id_search_persopac!!",$id_search_persopac,$search_form);
    	} else {
    		$search_form=str_replace("!!id_search_persopac!!","",$search_form);
    	}

   		global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		global $opac_view_id; 
    		$search_form=str_replace("!!opac_view_id!!",$opac_view_id,$search_form);				
    	}else {
    		$search_form=str_replace("!!opac_view_id!!","",$search_form);
    	}	
    	
    	global $id_connector_set;
    	if (isset($id_connector_set))
    		$search_form=str_replace("!!id_connector_set!!",$id_connector_set,$search_form);
    	else 
    		$search_form=str_replace("!!id_connector_set!!","",$search_form);
    			
    	if ($result_target) $r="document.search_form.target='$result_target';"; else $r="";
    	$search_form=str_replace("!!target_js!!",$r,$search_form);

		$search_form .= "\n\n<script type=\"text/javascript\" >
			function change_source_checkbox(changing_control, source_id) {
				var i=0; var count=0;
				onoff = changing_control.checked;
				for(i=0; i<document.search_form.elements.length; i++)
				{
					if(document.search_form.elements[i].name == 'source[]')	{
						if (document.search_form.elements[i].value == source_id)
							document.search_form.elements[i].checked = onoff;
					}
				}	
			
			}

			//callback du selecteur d'opérateur
			function operatorChanged(field,operator){
				for(i=0;i<=document.getElementById('field_'+field+'_max_aut').value;i++){
					var selector = document.getElementById('field_'+field+'_authority_selector')
					var f_lib = document.getElementById('field_'+field+'_lib_'+i);
					var f_id = document.getElementById('field_'+field+'_id_'+i);
					var f = document.getElementById('field_'+field+'_'+i);
					var authority_id = document.getElementById('fieldvar_'+field+'_authority_id');
					if(operator == 'AUTHORITY'){
		//				f_lib.setAttribute('class','saisie-20emr');
		//				if(authority_id.value != 0) f.value = authority_id.value;
					}else{
						f_lib.removeAttribute('class');
						f.value = f_lib.value; 
					}
				}
			}

			//callback du selecteur AJAX pour les autorités
			function authoritySelected(infield){
				//on enlève le dernier _X
				var tmp_infield = infield.split('_');
				var tmp_infield_length = tmp_infield.length;
				//var inc = tmp_infield[tmp_infield_length-1];
				tmp_infield.pop();
				infield = tmp_infield.join('_');
				//pour assurer la compatibilité avec le selecteur AJAX
				infield=infield.replace('_lib','');
				infield=infield.replace('_authority_label','');
				
				var op_name =infield.replace('field','op');
				var op_selector = document.forms['search_form'][op_name];
				//on passe le champ en selecteur d'autorité !
				for (var i=0 ; i<op_selector.options.length ; i++){
					if(op_selector.options[i].value == 'AUTHORITY')
						op_selector.options[i].selected = true;
				}
				for(i=0;i<=document.getElementById(infield+'_max_aut').value;i++){
					var searchField = document.getElementById(infield+'_'+i);
					var f_lib = document.getElementById(infield+'_lib'+'_'+i);
					var f_id = document.getElementById(infield+'_id'+'_'+i);
					var authority_id = document.getElementById(infield.replace('field','fieldvar')+'_authority_id'+'_'+i);
					
					f_lib.setAttribute('class','saisie-20emr');
					if(f_id.value==''){
						f_id.value=0;
					}
					searchField.value=f_id.value;
					authority_id.value= f_id.value;
				}			
			}
	
			//callback sur la saisie libre 
			function fieldChanged(id,inc,value,e){
				var ma_touche;
				if(window.event){
					ma_touche=window.event.keyCode;
				}else{
					ma_touche=e.keyCode;
				}
				
				var f_lib = document.getElementById(id+'_lib_'+inc);
				var f_id = document.getElementById(id+'_id_'+inc);
				var f = document.getElementById(id+'_'+inc);		
				var authority_id = document.getElementById(id.replace('field','fieldvar')+'_authority_id'+'_'+inc);

				var selector = document.forms['search_form'][id.replace('field','op')];		
				if (selector.options[selector.selectedIndex].value != 'AUTHORITY')
					f.value = value;
				else if(ma_touche != 13){
					var max_aut=document.getElementById(id+'_max_aut').value;
					if(max_aut>0){
						//Plus d'un champ : on bloque
						return;
					}
					f_lib.setAttribute('class','ext_search_txt');
					for (var i=0 ; i<selector.options.length ; i++){
						if (selector.options[i].value == 'BOOLEAN')
							selector.options[i].selected = true;
					}
					f.value = f_lib.value;
					authority_id.value = '';
				}
			}		
				
			function add_line(fnamesans){

				fname=fnamesans+'[]';
				fname_id=fnamesans+'_id';
				fnamesanslib=fnamesans+'_lib';
				fnamelib=fnamesans+'_lib[]';
				fname_name_aut_id=fnamesans+'[authority_id][]';
				fname_name_aut_id=fname_name_aut_id.replace('field','fieldvar');
				fname_aut_id=fnamesans+'_authority_id';
				fname_aut_id=fname_aut_id.replace('field','fieldvar');
				op=fnamesans.replace('field','op');
				
				template = document.getElementById('el'+fnamesans);
				inc=document.getElementById(fnamesans+'_max_aut').value;
				inc++;
		        line=document.createElement('div');
		  
				f_id = document.createElement('input');
				f_id.setAttribute('id',fnamesans+'_'+inc);
				f_id.setAttribute('name',fname);
				f_id.setAttribute('value','');
				f_id.setAttribute('type','hidden');
						
				f_lib = document.createElement('input');
				f_lib.setAttribute('autfield',fname_id+'_'+inc);
				f_lib.setAttribute('onkeyup','fieldChanged(\''+fnamesans+'\',\''+inc+'\',this.value,event)');
				f_lib.setAttribute('callback','authoritySelected');
				if(document.getElementById(fnamesanslib+'_0').getAttribute('completion')){
					f_lib.setAttribute('completion',document.getElementById(fnamesanslib+'_0').getAttribute('completion'));
				}
				f_lib.setAttribute('id',fnamesanslib+'_'+inc);
				f_lib.setAttribute('name',fnamelib);
				f_lib.setAttribute('value','');
				f_lib.setAttribute('type','text');
				if(document.getElementById(fnamesanslib+'_0').getAttribute('linkfield')){
					f_lib.setAttribute('linkfield',document.getElementById(fnamesanslib+'_0').getAttribute('linkfield'));
				}
				if (document.getElementById(op).options[document.getElementById(op).selectedIndex].value == 'AUTHORITY'){
					f_lib.setAttribute('class','saisie-20emr');
				}
						
				f_del = document.createElement('input');
				f_del.setAttribute('class','bouton');
				f_del.setAttribute('type','button');
				f_del.setAttribute('onclick','document.getElementById(\''+fnamesanslib+'_'+inc+'\').value=\'\';document.getElementById(\''+fnamesans+'_'+inc+'\').value=\'0\';');
				f_del.setAttribute('value','X');
				
				f_aut = document.createElement('input');
				f_aut.setAttribute('type','hidden');
				f_aut.setAttribute('value','');
				f_aut.setAttribute('id',fname_aut_id+'_'+inc);
				f_aut.setAttribute('name',fname_name_aut_id);
				
				f_id2 = document.createElement('input');
				f_id2.setAttribute('type','hidden');
				f_id2.setAttribute('value','');
				f_id2.setAttribute('id',fname_id+'_'+inc);
				f_id2.setAttribute('name',fname_id);
		        	        
		        line.appendChild(f_id);
		        line.appendChild(f_lib);
		        line.appendChild(f_del);
		        line.appendChild(f_aut);
		        line.appendChild(f_id2);
		        
		        template.appendChild(line);
						
				ajax_pack_element(f_lib);
		
		        document.getElementById(fnamesans+'_max_aut').value=inc;
				
				//Plus d'un champ : on bloque
				var selector = document.getElementById(op);
				selector.disabled=true;
				operators_to_enable.push(op);
			}
				
			function enable_operators(){
				if(operators_to_enable.length>0){
					for	(index = 0; index < operators_to_enable.length; index++) {
					    document.getElementById(operators_to_enable[index]).disabled=false;
					}
				}
			}
			
		</script>";

    	return $search_form;
    }
    
    //Parse du fichier de configuration
    function parse_search_file($fichier_xml,$full_path='') {
    	global $include_path;
    	global $msg;
    	
    	if(!$full_path){
	    	if ($fichier_xml!="") {
	    		if (file_exists($include_path."/search_queries/".$fichier_xml."_subst.xml")) {
	    			$fp=fopen($include_path."/search_queries/".$fichier_xml."_subst.xml","r") or die("Can't find XML file");
	    			$size=filesize($include_path."/search_queries/".$fichier_xml."_subst.xml");
	    		} else {
	    			$fp=fopen($include_path."/search_queries/".$fichier_xml.".xml","r") or die("Can't find XML file");
	    			$size=filesize($include_path."/search_queries/".$fichier_xml.".xml");
	    		}
	    	} else {
	    		if (file_exists($include_path."/search_queries/search_fields_subst.xml")) {
	    			$fp=fopen($include_path."/search_queries/search_fields_subst.xml","r") or die("Can't find XML file");
	    			$size=filesize($include_path."/search_queries/search_fields_subst.xml");
	    		} else {
	    			$fp=fopen($include_path."/search_queries/search_fields.xml","r") or die("Can't find XML file");
	    			$size=filesize($include_path."/search_queries/search_fields.xml");
	    		}
	    	}
    	} else{
    		if (file_exists($full_path.$fichier_xml."_subst.xml")) {
    			$fp=fopen($full_path.$fichier_xml."_subst.xml","r") or die("Can't find XML file");
    			$size=filesize($full_path.$fichier_xml."_subst.xml");
    		} else {
    			$fp=fopen($full_path.$fichier_xml.".xml","r") or die("Can't find XML file");
    			$size=filesize($full_path.$fichier_xml.".xml");
    		} 
    	}		
    	    	
		$xml=fread($fp,$size);
		fclose($fp);
		$param=_parser_text_no_function_($xml, "PMBFIELDS");

		//Lecture parametre memory_engine_allowed
		if($param['MEMORYENGINEALLOWED'][0]['value'] && $param['MEMORYENGINEALLOWED'][0]['value']=='yes') {
			$this->memory_engine_allowed = true;
		}
		
		//Lecture des operateurs
		for ($i=0; $i<count($param["OPERATORS"][0]["OPERATOR"]); $i++) {
			$operator_=$param["OPERATORS"][0]["OPERATOR"][$i];
			if (substr($operator_["value"],0,4)=="msg:") {
				$this->operators[$operator_["NAME"]]=$msg[substr($operator_["value"],4,strlen($operator_["value"])-4)];
			} else {
				$this->operators[$operator_["NAME"]]=$operator_["value"];	
			}
			if ($operator_["EMPTYALLOWED"]=="yes") $this->op_empty[$operator_["NAME"]]=true; else $this->op_empty[$operator_["NAME"]]=false;
		}
		
		//Lecture des champs fixes
		for ($i=0; $i<count($param["FIXEDFIELDS"][0]["FIELD"]); $i++) {
			$t=array();
			$ff=$param["FIXEDFIELDS"][0]["FIELD"][$i];
			if (substr($ff["TITLE"],0,4)=="msg:") {
				$t["TITLE"]=$msg[substr($ff["TITLE"],4,strlen($ff["TITLE"])-4)];	
			} else {
				$t["TITLE"]=$ff["TITLE"];	
			}
			$t["ID"]=$ff["ID"];
			$t["NOTDISPLAYCOL"]=$ff["NOTDISPLAYCOL"];
			$t["UNIMARCFIELD"]=$ff["UNIMARCFIELD"];
			$t["INPUT_TYPE"]=$ff["INPUT"][0]["TYPE"];
			$t["INPUT_OPTIONS"]=$ff["INPUT"][0];
			if (substr($ff["SEPARATOR"],0,4)=="msg:") {
				$t["SEPARATOR"]=$msg[substr($ff["SEPARATOR"],4,strlen($ff["SEPARATOR"])-4)];
			} else {
				$t["SEPARATOR"]=$ff["SEPARATOR"];	
			}
			//Visibilite
			$t["VISIBLE"]=($ff["VISIBLE"]=="no"?false:true);
			//Moteur memory
			$t['MEMORYENGINEFORBIDDEN']=($ff['MEMORYENGINEFORBIDDEN']=='yes'?true:false);
			
			//Variables
			for ($j=0; $j<count($ff["VARIABLE"]); $j++) {
				$v=array();
				$vv=$ff["VARIABLE"][$j];
				$v["NAME"]=$vv["NAME"];
				$v["TYPE"]=$vv["TYPE"];
				if (substr($vv["COMMENT"],0,4)=="msg:") {
					$v["COMMENT"]=$msg[substr($vv["COMMENT"],4,strlen($vv["COMMENT"])-4)];
				} else {
					$v["COMMENT"]=$vv["COMMENT"];	
				}
				//Recherche des options
				reset($vv);
				while (list($key,$val)=each($vv)) {
					if (is_array($val)) {
						$v["OPTIONS"][$key]=$val;
					}
				}
				$t["VAR"][]=$v;
			}

			if (!isset($ff["VISIBILITY"]))
				$t["VISIBILITY"]=true;
			else 
				if ($ff["VISIBILITY"]=="yes") $t["VISIBILITY"]=true; else $t["VISIBILITY"]=false;
			
			for ($j=0; $j<count($ff["QUERY"]); $j++) {
				$q=array();
				$q["OPERATOR"]=$ff["QUERY"][$j]["FOR"];
				if (($ff["QUERY"][$j]["MULTIPLE"]=="yes")||($ff["QUERY"][$j]["CONDITIONAL"]=="yes")) {
					if($ff["QUERY"][$j]["MULTIPLE"]=="yes") $element = "PART";
					else $element = "VAR";
					
					for ($k=0; $k<count($ff["QUERY"][$j][$element]); $k++) {
						$pquery=$ff["QUERY"][$j][$element][$k];						
						if($element == "VAR"){
							$q[$k]["CONDITIONAL"]["name"] = $pquery["NAME"];
							$q[$k]["CONDITIONAL"]["value"] = $pquery["VALUE"][0]["value"];
						}
						if ($pquery["MULTIPLEWORDS"]=="yes")
							$q[$k]["MULTIPLE_WORDS"]=true;
						else
							$q[$k]["MULTIPLE_WORDS"]=false;
						if ($pquery["REGDIACRIT"]=="yes")
							$q[$k]["REGDIACRIT"]=true;
						else
							$q[$k]["REGDIACRIT"]=false;
						if ($pquery["KEEP_EMPTYWORD"]=="yes")
							$q[$k]["KEEP_EMPTYWORD"]=true;
						else
							$q[$k]["KEEP_EMPTYWORD"]=false;					
						if ($pquery["REPEAT"]) {
							$q[$k]["REPEAT"]["NAME"]=$pquery["REPEAT"][0]["NAME"];
							$q[$k]["REPEAT"]["ON"]=$pquery["REPEAT"][0]["ON"];
							$q[$k]["REPEAT"]["SEPARATOR"]=$pquery["REPEAT"][0]["SEPARATOR"];
							$q[$k]["REPEAT"]["OPERATOR"]=$pquery["REPEAT"][0]["OPERATOR"];
							$q[$k]["REPEAT"]["ORDERTERM"]=$pquery["REPEAT"][0]["ORDERTERM"];
						}
						if ($pquery["BOOLEANSEARCH"]=="yes") {
							$q[$k]["BOOLEAN"]=true;
							if ($pquery["BOOLEAN"]) {
								for ($z=0; $z<count($pquery["BOOLEAN"]); $z++) {
									$q[$k]["TABLE"][$z]=$pquery["BOOLEAN"][$z]["TABLE"][0]["value"];
									$q[$k]["INDEX_L"][$z]=$pquery["BOOLEAN"][$z]["INDEX_L"][0]["value"];
									$q[$k]["INDEX_I"][$z]=$pquery["BOOLEAN"][$z]["INDEX_I"][0]["value"];
									$q[$k]["ID_FIELD"][$z]=$pquery["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
									if ($pquery["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
										$q[$k]["KEEP_EMPTY_WORDS"][$z]=1;
										$q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
									}
									if ($pquery["BOOLEAN"][$z]["FULLTEXT"][0]["value"]=="yes") {
										$q[$k]["FULLTEXT"][$z]=1;
									}
								}
							} else {
								$q[$k]["TABLE"]=$pquery["TABLE"][0]["value"];
								$q[$k]["INDEX_L"]=$pquery["INDEX_L"][0]["value"];
								$q[$k]["INDEX_I"]=$pquery["INDEX_I"][0]["value"];
								$q[$k]["ID_FIELD"]=$pquery["ID_FIELD"][0]["value"];
								if ($pquery["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
									$q[$k]["KEEP_EMPTY_WORDS"]=1;
									$q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
								}
								if ($pquery["FULLTEXT"][0]["value"]=="yes") {
									$q[$k]["FULLTEXT"]=1;
								}
							}
						} else $q[$k]["BOOLEAN"]=false;
						if ($pquery["ISBNSEARCH"]=="yes") {
							$q[$k]["ISBN"]=true;
						} else $q[$k]["ISBN"]=false;
						if ($pquery["DETECTDATE"]) {
							$q[$k]["DETECTDATE"]=$pquery["DETECTDATE"];
						} else $q[$k]["DETECTDATE"]=false;
						$q[$k]["MAIN"]=$pquery["MAIN"][0]["value"];
						$q[$k]["MULTIPLE_TERM"]=$pquery["MULTIPLETERM"][0]["value"];
						$q[$k]["MULTIPLE_OPERATOR"]=$pquery["MULTIPLEOPERATOR"][0]["value"];
					}
					$t["QUERIES"][]=$q;
					$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
				} else {
					if ($ff["QUERY"][$j]["MULTIPLEWORDS"]=="yes")
						$q[0]["MULTIPLE_WORDS"]=true;
					else
						$q[0]["MULTIPLE_WORDS"]=false;
					if ($ff["QUERY"][$j]["REGDIACRIT"]=="yes")
						$q[0]["REGDIACRIT"]=true;
					else
						$q[0]["REGDIACRIT"]=false;					
					if ($ff["QUERY"][$j]["KEEP_EMPTYWORD"]=="yes")
						$q[0]["KEEP_EMPTYWORD"]=true;
					else
						$q[0]["KEEP_EMPTYWORD"]=false;						
					if ($ff["QUERY"][$j]["REPEAT"]) {
						$q[0]["REPEAT"]["NAME"]=$ff["QUERY"][$j]["REPEAT"][0]["NAME"];
						$q[0]["REPEAT"]["ON"]=$ff["QUERY"][$j]["REPEAT"][0]["ON"];
						$q[0]["REPEAT"]["SEPARATOR"]=$ff["QUERY"][$j]["REPEAT"][0]["SEPARATOR"];
						$q[0]["REPEAT"]["OPERATOR"]=$ff["QUERY"][$j]["REPEAT"][0]["OPERATOR"];
						$q[0]["REPEAT"]["ORDERTERM"]=$ff["QUERY"][$j]["REPEAT"][0]["ORDERTERM"];
					}
					if ($ff["QUERY"][$j]["BOOLEANSEARCH"]=="yes") {
						$q[0]["BOOLEAN"]=true;
						if ($ff["QUERY"][$j]["BOOLEAN"]) {
							for ($z=0; $z<count($ff["QUERY"][$j]["BOOLEAN"]); $z++) {
								$q[0]["TABLE"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["TABLE"][0]["value"];
								$q[0]["INDEX_L"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_L"][0]["value"];
								$q[0]["INDEX_I"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_I"][0]["value"];
								$q[0]["ID_FIELD"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
								if ($ff["QUERY"][$j]["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
									$q[0]["KEEP_EMPTY_WORDS"][$z]=1;
									$q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
								}
							}
						} else {
							$q[0]["TABLE"]=$ff["QUERY"][$j]["TABLE"][0]["value"];
							$q[0]["INDEX_L"]=$ff["QUERY"][$j]["INDEX_L"][0]["value"];
							$q[0]["INDEX_I"]=$ff["QUERY"][$j]["INDEX_I"][0]["value"];
							$q[0]["ID_FIELD"]=$ff["QUERY"][$j]["ID_FIELD"][0]["value"];
							if ($ff["QUERY"][$j]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
								$q[0]["KEEP_EMPTY_WORDS"]=1;
								$q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
							}
						}
					} else $q[0]["BOOLEAN"]=false;
					//prise en compte ou non du paramétrage du stemming
					if($ff["QUERY"][$j]['STEMMING']=="no"){
						$q[0]["STEMMING"]= false;
					}else{
						$q[0]["STEMMING"]= true;
					}
					//modif arnaud pour notices_mots_global_index..
					if ($ff["QUERY"][$j]['WORDSEARCH']=="yes"){
						$q[0]["WORD"]=true;
						$q[0]['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
						$q[0]['FOLDER'] = $ff["QUERY"][$j]['CLASS'][0]['FOLDER'];
						$q[0]['FIELDS'] = $ff["QUERY"][$j]['CLASS'][0]['FIELDS'];
					}else $q[0]["WORD"]=false;
					//fin modif arnaud
					if ($ff["QUERY"][$j]["ISBNSEARCH"]=="yes") {
						$q[0]["ISBN"]=true;
					} else $q[0]["ISBN"]=false;
					if ($ff["QUERY"][$j]["DETECTDATE"]) {
						$q[0]["DETECTDATE"]=$ff["QUERY"][$j]["DETECTDATE"];
					} else $q[0]["DETECTDATE"]=false;
					$q[0]["MAIN"]=$ff["QUERY"][$j]["MAIN"][0]["value"];
					$q[0]["MULTIPLE_TERM"]=$ff["QUERY"][$j]["MULTIPLETERM"][0]["value"];
					$q[0]["MULTIPLE_OPERATOR"]=$ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"];
					$t["QUERIES"][]=$q;
					$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
				}
			}
			
			// recuperation des visibilites parametrees
			for ($j=0; $j<count($ff["VAR"]); $j++) {
				$q=array();
				$q["NAME"]=$ff["VAR"][$j]["NAME"];
				if ($ff["VAR"][$j]["VISIBILITY"]=="yes") 
					$q["VISIBILITY"]=true;
				else 
					$q["VISIBILITY"]=false;
				for ($k=0; $k<count($ff["VAR"][$j]["VALUE"]); $k++) {
					$v=array();
					if ($ff["VAR"][$j]["VALUE"][$k]["VISIBILITY"]=="yes")
						$v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = true ;
					else 
						$v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = false ;
				} // fin for <value ...
				$q["VALUE"] = $v ;
				$t["VARVIS"][] = $q ;
			} // fin for
			
			$this->fixedfields[$ff["ID"]]=$t;
		}
		
		//Lecture des champs dynamiques
		if ($param["DYNAMICFIELDS"][0]["VISIBLE"]=="no") $this->dynamics_not_visible=true;
		if(!$param["DYNAMICFIELDS"][0]["FIELDTYPE"]){//Pour le cas de fichiers subst basés sur l'ancienne version 
			$tmp=$param["DYNAMICFIELDS"][0]["FIELD"];
			unset($param["DYNAMICFIELDS"]);
			$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["PREFIX"]="d";
			$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["TYPE"]="notices";
			$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["FIELD"]=$tmp;
			unset($tmp);
		}
		for ($h=0; $h <count($param["DYNAMICFIELDS"][0]["FIELDTYPE"]); $h++){
			$champType=array();
			$ft=$param["DYNAMICFIELDS"][0]["FIELDTYPE"][$h];
			$champType["TYPE"]=$ft["TYPE"];
			for ($i=0; $i<count($ft["FIELD"]); $i++) {
				$t=array();
				$ff=$ft["FIELD"][$i];
				$t["DATATYPE"]=$ff["DATATYPE"];
				$t["NOTDISPLAYCOL"]=$ff["NOTDISPLAYCOL"];
				//Moteur memory
				$t['MEMORYENGINEFORBIDDEN']=($ff['MEMORYENGINEFORBIDDEN']=='yes'?true:false);
				$q=array();
				for ($j=0; $j<count($ff["QUERY"]); $j++) {
					$q["OPERATOR"]=$ff["QUERY"][$j]["FOR"];
					if ($ff["QUERY"][$j]["MULTIPLEWORDS"]=="yes")
						$q["MULTIPLE_WORDS"]=true;
					else
						$q["MULTIPLE_WORDS"]=false;
					if ($ff["QUERY"][$j]["REGDIACRIT"]=="yes")
						$q["REGDIACRIT"]=true;
					else
						$q["REGDIACRIT"]=false;				
					if ($ff["QUERY"][$j]["KEEP_EMPTYWORD"]=="yes")
						$q["KEEP_EMPTYWORD"]=true;
					else
						$q["KEEP_EMPTYWORD"]=false;
					if ($ff["QUERY"][$j]["DEFAULT_OPERATOR"])
						$q["DEFAULT_OPERATOR"] = $ff["QUERY"][$j]["DEFAULT_OPERATOR"]; 					
					$q["NOT_ALLOWED_FOR"]=array();
					$naf=$ff["QUERY"][$j]["NOTALLOWEDFOR"];
					if ($naf) {
						$naf_=explode(",",$naf);
						$q["NOT_ALLOWED_FOR"]=$naf_;
					}
					//modif arnaud pour notices_mots_global_index..
					if($ff["QUERY"][$j]['WORDSEARCH']=="yes"){
						$q["WORD"]=true;
						$q['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
					}else $q["WORD"]=false;
					if ($ff["QUERY"][$j]['SEARCHABLEONLY']=="yes"){
						$q["SEARCHABLEONLY"]=true;
					}else $q["SEARCHABLEONLY"]=false;
					//fin modif arnaud
					
					$q["MAIN"]=$ff["QUERY"][$j]["MAIN"][0]["value"];
					$q["MULTIPLE_TERM"]=$ff["QUERY"][$j]["MULTIPLETERM"][0]["value"];
					$q["MULTIPLE_OPERATOR"]=$ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"];
					$t["QUERIES"][]=$q;
					$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
				}
				$champType["FIELD"][$ff["ID"]]=$t;
			}
			$this->dynamicfields[$ft["PREFIX"]]=$champType;
		}
		//Lecture des champs speciaux
		if ($param["SPECIALFIELDS"][0]["VISIBLE"]=="no") $this->specials_not_visible=true;
		for ($i=0; $i<count($param["SPECIALFIELDS"][0]["FIELD"]); $i++) {
			$t=array();
			$sf=$param["SPECIALFIELDS"][0]["FIELD"][$i];
			if (substr($sf["TITLE"],0,4)=="msg:") {
				$t["TITLE"]=$msg[substr($sf["TITLE"],4,strlen($sf["TITLE"])-4)];	
			} else {
				$t["TITLE"]=$sf["TITLE"];	
			}
			$t["NOTDISPLAYCOL"]=$sf["NOTDISPLAYCOL"];
			$t["UNIMARCFIELD"]=$sf["UNIMARCFIELD"];
			if (substr($sf["SEPARATOR"],0,4)=="msg:") {
				$t["SEPARATOR"]=$msg[substr($sf["SEPARATOR"],4,strlen($sf["SEPARATOR"])-4)];
			} else {
				$t["SEPARATOR"]=$sf["SEPARATOR"];	
			}
			$t["TYPE"]=$sf["TYPE"];
			if($sf["VISIBLE"] != "no"){
				$t['VISIBLE'] = true;
			}else $t['VISIBLE'] = false;
			$t["DELNOTALLOWED"]=($sf["DELNOTALLOWED"]=="yes"?true:false);
			$this->specialfields[$sf["ID"]]=$t;
		}
		if (count($this->specialfields)!=0) {
			if (file_exists($include_path."/search_queries/specials/catalog_subst.xml")) {
				$nom_fichier=$include_path."/search_queries/specials/catalog_subst.xml";
			} else {
				$nom_fichier=$include_path."/search_queries/specials/catalog.xml";	
			}
			$parametres=file_get_contents($nom_fichier);
			$this->tableau_speciaux=_parser_text_no_function_($parametres, "SPECIALFIELDS");
		}
		$this->keyName = $param["KEYNAME"][0]["value"];
		if(!$this->keyName) $this->keyName="notice_id";
    }
    
    function serialize_search() {
    	global $search;
    	
    	$to_serialize=array();
    	$to_serialize["SEARCH"]=$search;
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		$to_serialize[$i]["SEARCH"]=$search[$i];
    		$to_serialize[$i]["OP"]=$$op;
    		$to_serialize[$i]["FIELD"]=$$field_;
    		$to_serialize[$i]["INTER"]=$$inter;
    		$to_serialize[$i]["FIELDVAR"]=$$fieldvar;
    	}
    	return serialize($to_serialize);
    }
    
    function unserialize_search($serialized) {
    	global $search;
    	$to_unserialize=unserialize($serialized);
    	$search=$to_unserialize["SEARCH"];
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		$$op=$to_unserialize[$i]["OP"];
    		$$field_=$to_unserialize[$i]["FIELD"];
    		$$inter=$to_unserialize[$i]["INTER"];
    		$$fieldvar=$to_unserialize[$i]["FIELDVAR"];
    	}
    }
    
    function make_serialized_human_query($serialized) {
    	//global $search;
    	global $msg;
    	global $charset;
    	global $include_path;
    	
    	$to_unserialize=unserialize($serialized);
    	$search=$to_unserialize["SEARCH"];
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		if(!isset($GLOBALS[$$op])){
    			global $$op;
    		}
    		if(!isset($GLOBALS[$$field_])){
    			global $$field_;
    		}
    		if(!isset($GLOBALS[$$inter])){
    			global $$inter;
    		}
    		if(!isset($GLOBALS[$$fieldvar])){
    			global $$fieldvar;
    		}
    		$$op=$to_unserialize[$i]["OP"];
    		$$field_=$to_unserialize[$i]["FIELD"];
     		$$inter=$to_unserialize[$i]["INTER"];
     		$$fieldvar=$to_unserialize[$i]["FIELDVAR"];
    	}
    	
    	$r="";
    	for ($i=0; $i<count($search); $i++) {
    		$s=explode("_",$search[$i]);
    		if ($s[0]=="f") {
    			$title=$this->fixedfields[$s[1]]["TITLE"]; 
    		} elseif (array_key_exists($s[0],$this->pp)) {
    			$title=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    		} elseif ($s[0]=="s") {
    			$title=$this->specialfields[$s[1]]["TITLE"];
    		}
    		$op="op_".$i."_".$search[$i];
     		$operator=$this->operators[$$op];
    		$field_="field_".$i."_".$search[$i];
    		$field=$$field_;
    		//Recuperation des variables auxiliaires
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		$fieldvar=$$fieldvar_;
    		if (!is_array($fieldvar)) $fieldvar=array();
    		
    		$field_aff=array();
    		if (array_key_exists($s[0],$this->pp)) {
    			$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
				$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
				$q_index=$df["QUERIES_INDEX"];
	 			$q=$df["QUERIES"][$q_index[$$op]];
    			if ($q["DEFAULT_OPERATOR"])
    				$operator_multi=$q["DEFAULT_OPERATOR"];
    			for ($j=0; $j<count($field); $j++) {
    				$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
    			}
    		} elseif($s[0]=="f") {
    			$ff=$this->fixedfields[$s[1]];
	 			$q_index=$ff["QUERIES_INDEX"];
	 			$q=$ff["QUERIES"][$q_index[$$op]];
	 			if($fieldvar["operator_between_multiple_authorities"]){
	 				$operator_multi=$fieldvar["operator_between_multiple_authorities"][0];
	 			} else {
	 				if ($q["DEFAULT_OPERATOR"])
	 					$operator_multi=$q["DEFAULT_OPERATOR"];
	 			}
    			switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
    				case "list":
    					$options=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["OPTIONS"][0];
    					$opt=array();
    					for ($j=0; $j<count($options["OPTION"]); $j++) {
    						if (substr($options["OPTION"][$j]["value"],0,4)=="msg:") {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$msg[substr($options["OPTION"][$j]["value"],4,strlen($options["OPTION"][$j]["value"])-4)];
    						} else {
    							$opt[$options["OPTION"][$j]["VALUE"]]=$options["OPTION"][$j]["value"];	
    						}
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "query_list":
    					$requete=$this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["FILTERING"] == "yes") {
    						$requete = str_replace("!!acces_j!!", "", $requete);
    						$requete = str_replace("!!statut_j!!", "", $requete);
    						$requete = str_replace("!!statut_r!!", "", $requete);
    					}
    					if ($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
    						$use_global = explode(",", $this->fixedfields[$s[1]]["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
    						for($j=0; $j<count($use_global); $j++) {
    							$var_global = $use_global[$j];
    							global $$var_global;
    							$requete = str_replace("!!".$var_global."!!", $$var_global, $requete);
    						}
    					}
    					$resultat=pmb_mysql_query($requete);
    					$opt=array();
    					while ($r_=@pmb_mysql_fetch_row($resultat)) {
    						$opt[$r_[0]]=$r_[1];
    					}
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt[$field[$j]];
    					}
    					break;
    				case "marc_list":
    					$opt=new marc_list($this->fixedfields[$s[1]]["INPUT_OPTIONS"]["NAME"][0]["value"]);
    					for ($j=0; $j<count($field); $j++) {
    						$field_aff[$j]=$opt->table[$field[$j]];
    					}
    					break;
    				case "date":
    					$field_aff[0]=format_date($field[0]);
    					break;
    				case "authoritie":
    					for($j=0 ; $j<sizeof($field) ; $j++){
    						if(is_numeric($field[$j]) && ($$op == "AUTHORITY")){
    							switch ($ff['INPUT_OPTIONS']['SELECTOR']){
    								case "categorie":
    									$field[$j] = categories::getlibelle($field[$j],$lang);
    									break;
    								case "auteur":
    									$aut=new auteur($field[$j]);
    									if($aut->rejete) $field[$j] = $aut->name.', '.$aut->rejete;
    									else $field[$j] = $aut->name;
    									if($aut->date) $field[$j] .= " ($aut->date)";
    									break;
    								case "editeur":
    									$ed = new editeur($field[$j]);
    									$field[$j]=$ed->name;
    									if ($ed->ville)
    									if ($ed->pays) $field[$j].=" ($ed->ville - $ed->pays)";
    									else $field[$j].=" ($ed->ville)";
    									break;
    								case "collection" :
    									$coll = new collection($field[$j]);
    									$field[$j] = $coll->name;
    									break;
    								case "subcollection" :
    									$coll = new subcollection($field[$j]);
    									$field[$j] = $coll->name;
    									break;
    								case "serie" :
    									$serie = new serie($field[$j]);
    									$field[$j] = $serie->name;
    									break;
    								case "indexint" :
    									$indexint = new indexint($field[$j]);
    									if ($indexint->comment) $field[$j] = $indexint->name." - ".$indexint->comment;
    									else $field[$j] = $indexint->name ;
    									if ($thesaurus_classement_mode_pmb != 0) {
    										$field[$j]="[".$indexint->name_pclass."] ".$field[$j];
    									}
    								break;
    								case "titre_uniforme" :
    									$tu = new titre_uniforme($field[$j]);
    									$field[$j] = $tu->name;
    									break;
    								case "notice" :
    									$requete = "select if(serie_name is not null,if(tnvol is not null,concat(serie_name,', ',tnvol,'. ',tit1),concat(serie_name,'. ',tit1)),tit1) AS tit from notices left join series on serie_id=tparent_id where notice_id='".$field[$j]."' ";
    									$res=pmb_mysql_query($requete);
    									if($res && pmb_mysql_num_rows($res)){
    										$field[$j] = pmb_mysql_result($res,0,0);
    									}
    									break;
    							}
    						}
    					}
    					$field_aff= $field;
    					break;
    				default:
    					$field_aff=$field;
    					break;		
    			}
    		} elseif ($s[0]=="s") {
    			//appel de la fonction make_human_query de la classe du champ special
    			//Recherche du type
    			$type=$this->specialfields[$s[1]]["TYPE"];
    			for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$field_aff=$specialclass->make_human_query();
						$field_aff[0]=html_entity_decode(strip_tags($field_aff[0]),ENT_QUOTES,$charset);
						break;
					}
    			}
    		}
    		
    		//Ajout des variables si necessaire
    		reset($fieldvar);
    		$fieldvar_aff=array();
    		while (list($var_name,$var_value)=each($fieldvar)) {
    			//Recherche de la variable par son nom
    			$vvar=$this->fixedfields[$s[1]]["VAR"];
    			for ($j=0; $j<count($vvar); $j++) {
    				if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {
    					
    					//Calcul de la visibilite
    					$varname=$vvar[$j]["NAME"];
   		 				$visibility=1;
   		 				$vis=$vvar[$j]["OPTIONS"]["VAR"][0];
   		 				if ($vis["NAME"]) {
   		 					$vis_name=$vis["NAME"];
   		 					global $$vis_name;
   		 					if ($vis["VISIBILITY"]=="no") $visibility=0;
   		 					for ($k=0; $k<count($vis["VALUE"]); $k++) {
   		 						if ($vis["VALUE"][$k]["value"]==$$vis_name) {
   		 							if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
   		 							if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
   		 							break;
   		 						}
   		 					}
   		 				}
    					
    					$var_list_aff=array();
     					$flag_aff = false;
    					
    					if ($visibility) {		
    						switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
    							case "query_list":
    								$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
       								$r_list=pmb_mysql_query($query_list);
    								while ($line=pmb_mysql_fetch_array($r_list)) {
    									$as=array_search($line[0],$var_value);
    									if (($as!==false)&&($as!==NULL)) {
    										$var_list_aff[]=$line[1];
    									}
    								}
    								if($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["ALLCHOICE"] == "yes" && count($var_list_aff) == 0){
    									$var_list_aff[]=$msg[substr($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"],4,strlen($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"])-4)];
    								}
    								$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff);
     								$flag_aff=true;
    								break;
    							case "checkbox":
    								$value = $var_value[0];
    								$label_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["COMMENTS"][0]["LABEL"];
    								for($indice=0;$indice<count($label_list);$indice++){
    									if($value == $label_list[$indice]["VALUE"]){
    										$libelle = $label_list[$indice]["value"];
    										break;
    									}
    								}
    								
    								$fieldvar_aff[]=$libelle;
    								$flag_aff=true;
    								break;
    						}
    						if($flag_aff) $fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
    					}
    				}
    			}
    		}
    		
    		switch ($operator_multi) {
    			case "and":
    				$op_list=$msg["search_and"];
    				break;
    			case "or":
    				$op_list=$msg["search_or"];
    				break;
    			default:
    				$op_list=$msg["search_or"];
    				break;
    		}
    		$texte=implode(" ".$op_list." ",$field_aff);
    		//$texte=implode(" ".$msg["search_or"]." ",$field_aff);
    		if (count($fieldvar_aff)) $texte.=" [".implode(" ; ",$fieldvar_aff)."]";

    		$inter="inter_".$i."_".$search[$i];
    		switch ($$inter) {
    			case "and":
    				$inter_op=$msg["search_and"];
    				break;
    			case "or":
    				$inter_op=$msg["search_or"];
    				break;
    			case "ex":
    				$inter_op=$msg["search_exept"];
    				break;
    			default:
    				$inter_op="";
    				break;
    		}
    		if ($inter_op) $inter_op="<strong>".htmlentities($inter_op,ENT_QUOTES,$charset)."</strong>";
    		$r.=$inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".htmlentities($texte,ENT_QUOTES,$charset).")</i> ";
    	}
    	return $r;
    }

	function push() {
		global $search;
		global $pile_search;
		$pile_search[]=$this->serialize_search();
		for ($i=0; $i<count($search); $i++) {
			$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		$$op="";
    		$$field_="";
    		$$inter="";
    		$$fieldvar="";
		}
		$search="";
	}
	
	function pull() {
		global $pile_search;
		$this->unserialize_search($pile_search[count($pile_search)-1]);
		$t=array();
		for ($i=0; $i<count($pile_search)-1; $i++) {
			$t[$i]=$pile_search[$i];
		}
		$pile_search=$t;
	}
	
	function get_unimarc_fields() {
		$r=array();
		foreach($this->fixedfields as $id=>$values) {
			if ($values["UNIMARCFIELD"]) {
				$r[$values["UNIMARCFIELD"]]["TITLE"][]=$values["TITLE"];
				foreach($values["QUERIES_INDEX"] as $op=>$top) {
					$r[$values["UNIMARCFIELD"]]["OPERATORS"][$op]=$this->operators[$op];
				}
			}
		}
		return $r;
	}
	
	function show_results_fichier($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
    	global $dbh;
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page,$dest;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $affich_tris_result_liste;
    	global $pmb_allow_external_search;
    	global $debug;
		global $gestion_acces_active, $gestion_acces_user_notice,$PMBuserid, $pmb_allow_external_search;
		global $link_bulletin;

 				
		$start_page=$nb_per_page_search*$page;
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}
    	
    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global $$op;
     		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ((string)$field[0]=="") {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$sf,$i,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[$$op])) {
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}
    	
    	$table=$this->make_search();

		if ($acces==true && $gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	    	$this->filter_searchtable_from_accessrights($table, $PMBuserid);
		}
    	
		$requete="select count(1) from $table";
		$nb_results=pmb_mysql_result(pmb_mysql_query($requete),0,0);
    	
		
    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		error_message_history("", $this->error_message, 1);
    		exit();
    	}

    	if (!$dest && $hidden_form){
    		print $this->make_hidden_search_form($url,"search_form","",false);
    		print "<input type='hidden' name='dest' value='' />\n";
    		print "</form>\n";
    	}
    		
    	
    	if($dest != "TABLEAU"){
	    	$human_requete = $this->make_human_query();
	    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
			if ($debug) print "<br />".$this->serialize_search();
			if ($nb_results) {
				print " => ".$nb_results." ".$msg["fiche_found"]."<br />\n";
			} else print "<br />".$msg["1915"]." ";
    	}
		
    	
		$requete="select $table.* from ".$table.",fiche where fiche.id_fiche=$table.id_fiche";
		if(!$dest){
			$requete .= " limit ".$start_page.",".$nb_per_page_search;
		}
		
    	$resultat=pmb_mysql_query($requete,$dbh);
    	
    	if(pmb_mysql_num_rows($resultat)){
	    	$result_fic=array();
			$fic = new fiche();
	    	while ($r=pmb_mysql_fetch_object($resultat)) {
	    		$result_fic[$r->id_fiche] = $fic->get_values($r->id_fiche,1);
	    	}
	    	if($result_fic){
	    		if($dest == "TABLEAUHTML"){
					print "<div class='row'>".$fic->display_results_tableau($result_fic,"",0,true)."</div>";
				}elseif($dest == "TABLEAU"){
					$fic->print_results_tableau($result_fic);
				}else{
	    			print "<div class='row'>".$fic->display_results_tableau($result_fic)."</div>";
	    		}
	    	}
    	}
    	if($this->limited_search)
    		$limit_script = "&limited_search=1";
    	else $limit_script="";

		if(!$dest){
			print "<div class='row'><input type='button' class='bouton' onClick=\"document.search_form.dest.value=''; document.search_form.action='$url_to_search_form$limit_script'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
			print "<font size='4'>&nbsp;&nbsp;&nbsp;&nbsp;</font>
			<input type='image' src='./images/tableur.gif' border='0' onClick=\"document.search_form.dest.value='TABLEAU'; document.search_form.submit(); \" alt='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' />
			<font size='4'>&nbsp;&nbsp;&nbsp;&nbsp;</font>
			<input type='image' src='./images/tableur_html.gif' border='0' onClick=\"document.search_form.dest.value='TABLEAUHTML'; document.search_form.submit(); \" alt='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' /></div>";
		}
    	//Gestion de la pagination
    	if ($nb_results && !$dest) {
	  	  	$n_max_page=ceil($nb_results/$nb_per_page_search);
	   	 	
	   	 	if (!$page) $page_en_cours=0 ;
				else $page_en_cours=$page ;
		
	   	 	// affichage du lien precedent si necessaire
   		 	if ($page>0) {
   		 		$nav_bar .= "<a href='#' onClick='document.search_form.dest.value=\"\"; document.search_form.page.value-=1; ";
   		 		if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
   		 		$nav_bar .= "document.search_form.submit(); return false;'>";
   	 			$nav_bar .= "<img src='./images/left.gif' border='0'  title='".$msg[48]."' alt='[".$msg[48]."]' hspace='3' align='middle'/>";
    			$nav_bar .= "</a>";
    		}
        	
			$deb = $page_en_cours - 10 ;
			if ($deb<0) $deb=0;
			for($i = $deb; ($i < $n_max_page) && ($i<$page_en_cours+10); $i++) {
				if($i==$page_en_cours) $nav_bar .= "<strong>".($i+1)."</strong>";
					else {
						$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=".($i)."; ";
    					if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
		    			$nav_bar .= "document.search_form.dest.value=''; document.search_form.submit(); return false;\">";
    					$nav_bar .= ($i+1);
    					$nav_bar .= "</a>";
						}
				if($i<$n_max_page) $nav_bar .= " "; 
				}
        	
			if(($page+1)<$n_max_page) {
    			$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=parseInt(document.search_form.page.value)+parseInt(1); ";
    			if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
    			$nav_bar .= "document.search_form.dest.value=''; document.search_form.submit(); return false;\">";
    			$nav_bar .= "<img src='./images/right.gif' border='0' title='".$msg[49]."' alt='[".$msg[49]."]' hspace='3' align='middle'>";
    			$nav_bar .= "</a>";
        		} else 	$nav_bar .= "";
			$nav_bar = "<div align='center'>$nav_bar</div>";
			echo $nav_bar ;
    	}  	
    }
	
function destroy_global_env(){
    	global $search;
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		unset($GLOBALS[$op]);
    		unset($GLOBALS[$field_]);
    		unset($GLOBALS[$inter]);
    		unset($GLOBALS[$fieldvar]);
    	}
    	 $search = array();
	}
	
	function reduct_search() {
		global $search;
		$tt=array();
		$it=0;
		for ($i=0; $i<count($search); $i++) {
  		  	$op="op_".$i."_".$search[$i];
   	 		global $$op;
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$field=$$field_;
    		if (((string)$field[0]!="")||($this->op_empty[$$op])) {
    			$tt[$it]=$i;
    			$it++;
    		}
		}
		
		//Décalage des critères
    	//1) copie des critères valides
    	for ($i=0; $i<count($tt); $i++) {
    		$it=$tt[$i];
    		$op="op_".$it."_".$search[$it];
    		$field_="field_".$it."_".$search[$it];
    		$fieldvar="fieldvar_".$it."_".$search[$it];
    		$inter="inter_".$it."_".$search[$it];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		$fieldt[$i]["op"]=$$op;
    		$fieldt[$i]["field"]=$$field_;
    		$fieldt[$i]["fieldvar"]=$$fieldvar;
    		$fieldt[$i]["inter"]=$$inter;
    		$fieldt[$i]["search"]=$search[$it];
    	}
    	//On nettoie et on reconstruit
    	$this->destroy_global_env();
    	$search=array();
    	for ($i=0; $i<count($tt); $i++) {
    		$search[$i]=$fieldt[$i]["search"];
    		$op="op_".$i."_".$search[$i];
    		$field_="field_".$i."_".$search[$i];
    		$fieldvar_="fieldvar_".$i."_".$search[$i];
    		$inter="inter_".$i."_".$search[$i];
    		global $$op;
    		global $$field_;
    		global $$inter;
    		global $$fieldvar;
    		$$op=$fieldt[$i]["op"];
    		$$field_=$fieldt[$i]["field"];
    		$$fieldvar=$fieldt[$i]["fieldvar"];
    		$$inter=$fieldt[$i]["inter"];
    	}
	}
	
	function get_ajax_params(){
		global $field_form;
		global $charset,$include_path;
		
		$elem = explode('_',$field_form);
		if($elem[count($elem)-2] == "s"){
			$field_id = $elem[count($elem)-1];
			//appel de la fonction get_input_box de la classe du champ special
    		$type=$this->specialfields[$field_id]["TYPE"];
    		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
				if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
					$sf=$this->specialfields[$field_id];
					if ($this->full_path && file_exists($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php"))
						require_once($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					else
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$n,$sf,$this);
					$specialclass->get_ajax_params();	
					break;
				}
    		}
		}
	}
	
	function show_search_history($idcaddie=0, $object_type="NOTI", $lien_origine="./catalog.php?", $action_click = "add_item") {
    	global $msg;
    	global $charset;
		
    	$r = "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=$idcaddie' method='post'>
    	<input type='hidden' id='item' name='item' >
    	<input type='hidden' id='search[0]' name='search[0]' value='s_1'>
    	<input type='hidden' id='field_0_s_1[0]' name='field_0_s_1[0]'>
    	<hr />
    	<div class='row'>".$msg["caddie_select_pointe_search_history"]."</div>";
		
    	//parcours de l'historique des recherches
    	$bool = false;
    	if (count($_SESSION["session_history"])) {
			$r .="<div id='$i"."Child' style='margin-bottom:6px;width:94%'>
				<table class='table-no-border'>
				!!contenu!!
				</table>
			</div>
			";

    		$style_odd="class='odd' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='odd'\" ";
    		$style_even="class='even' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\" ";

    		$liste="";
    	    for ($i=count($_SESSION["session_history"])-1; $i>=0; $i--) {
    			if ($_SESSION["session_history"][$i][$object_type]) {
    				$bool = true;
    				$temp=html_entity_decode(strip_tags(($i+1).") ".$_SESSION["session_history"][$i]["QUERY"]["HUMAN_QUERY"]),ENT_QUOTES,$charset);
    				
    				if(($pair=1-$pair)) $style=$style_odd;
    				else $style=$style_even;
    				$liste.="<tr $style><td><a href='#' onclick='javascript:document.getElementById(\"item\").value=".$i.";document.getElementById(\"field_0_s_1[0]\").value=".$i.";document.forms[\"print_options\"].submit();' />$temp</a>
    				<input type='hidden' id='human_query_history_".$i."' name='human_query_history_".$i."' value='".serialize($_SESSION["session_history"][$i]["QUERY"]["HUMAN_QUERY"])."' ></td></tr>";
    			}
    		}
    		$r=str_replace("!!contenu!!",$liste, $r);	
    	}
    	if (!$bool) {
    		$r .= "<b>".$msg["histo_empty"]."</b>";
   		}
    	return $r;
    }
    
    //suppression des champs de recherche marqués FORBIDDEN pour recherche externe
    function remove_forbidden_fields() {
    	global $search;
    	$old_search=array();
    	$old_search['search']=$search;
    	for ($i=0; $i<count($search); $i++) {
    
    		$inter="inter_".$i."_".$search[$i];
    		global $$inter;
    		$old_search[$inter]=$$inter;
    
    		$op="op_".$i."_".$search[$i];
    		global $$op;
    		$old_search[$op]=$$op;
    
    		$field_="field_".$i."_".$search[$i];
    		global $$field_;
    		$old_search[$field]=$$field;
    
    		$fieldvar="fieldvar_".$i."_".$search[$i];
    		global $$fieldvar;
    		$old_search[$fieldvar]=$$fieldvar;
    
    	}
    	$saved_search=array();
		if(count($search)){
	    	foreach($search as $k=>$s) {
	    		if ($s[0]=="f") {
	    			if ($this->fixedfields[substr($s,2)] && ($this->fixedfields[substr($s,2)]['UNIMARCFIELD']!='FORBIDDEN')) {
	    				$saved_search[$k]=$s;
	    			}
	    		} elseif(array_key_exists($s[0],$this->pp)){
	    			//Pas de recherche affiliée dans des champs personnalisés.
	    		} elseif ($s[0]=="s") {
	    			if ($this->specialfields[substr($s,2)] && ($this->specialfields[substr($s,2)]['UNIMARCFIELD']!='FORBIDDEN')) {
	    				$saved_search[$k]=$s;
	    			}
	    		}elseif (substr($s,0,9)=="authperso") {
	    			$saved_search[$k]=$s;
	    		}
	    	}
		}
    
    	$new_search=array();
    	$i=0;
    	foreach($saved_search as $k=>$v) {
    		$new_search['search'][$i]=$v;
    
    		$old_inter="inter_".$k."_".$v;
    		$new_inter="inter_".$i."_".$v;
    		global $$old_inter;
    		$new_search[$new_inter]=$$old_inter;
    
    		$old_op="op_".$k."_".$v;
    		$new_op="op_".$i."_".$v;
    		global $$old_op;
    		$new_search[$new_op]=$$old_op;
    
    		$old_field="field_".$k."_".$v;
    		$new_field="field_".$i."_".$v;
    		global $$old_field;
    		$new_search[$new_field]=$$old_field;
    
    		$old_fieldvar="fieldvar_".$k."_".$v;
    		$new_fieldvar="fieldvar_".$i."_".$v;
    		global $$old_fieldvar;
    		$new_search[$new_fieldvar]=$$old_fieldvar;
    
    		$i++;
    	}
    	$this->destroy_global_env();
    	foreach($new_search as $k=>$va) {
    		global $$k;
    		$$k=$va;
    	}
    }
}
?>