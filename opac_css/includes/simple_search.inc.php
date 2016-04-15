<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simple_search.inc.php,v 1.120 2015-05-15 12:55:21 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// recherche simple
require_once($base_path."/classes/marc_table.class.php");
require_once($base_path."/includes/javascript/form.inc.php");
require_once($base_path."/includes/empr.inc.php");
require_once($class_path."/search.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/search_persopac.class.php");
require_once($base_path."/classes/perio_a2z.class.php");
require($include_path."/templates/search.tpl.php");//Car si l'on a des vues activées les variables globales de ce fichier ne sont pas celles de la vue lors du premier chargement mais les valeurs par défaut. il faut donc forcer son rechargement
require_once($base_path."/classes/authperso.class.php");
require_once($class_path."/skos/skos_concept.class.php");

function simple_search_content($value='',$css) {
	global $dbh;
	global $msg;
	global $charset;
	global $lang;
	global $css;
	global $search_type;
	global $class_path;
	global $es;
	global $lvl;
	global $include_path;
	global $opac_allow_extended_search,$opac_allow_term_search,$opac_allow_external_search;
	global $typdoc;
	global $opac_search_other_function, $opac_search_show_typdoc;
	global $opac_thesaurus;
	global $id_thes;
	global $base_path;
	global $opac_allow_tags_search;
	global $opac_show_onglet_empr;
	global $external_env;
	global $user_query;
	global $source;
	global $opac_recherches_pliables;
	global $opac_show_help;
	global $onglet_persopac,$opac_allow_personal_search;
	global $search_form_perso,$search_form,$search_form_perso_limitsearch,$limitsearch;
	global $opac_show_onglet_help;
	global $search_in_perio;
	global $get_query;
	global $opac_show_onglet_perio_a2z,$opac_autolevel2;
	global $opac_simple_search_suggestions;
	global $opac_show_onglet_map, $opac_map_activate;
	global $opac_map_base_layer_params,$opac_map_size_search_edition,$opac_map_base_layer_type;
	global $map_emprises_query;
	
	include($include_path."/templates/simple_search.tpl.php");
	if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);
	
	// pour la DSI
	global $opac_allow_bannette_priv ; // bannettes privees autorisees ?
	global $bt_cree_bannette_priv ;
	if ($opac_allow_bannette_priv && ($bt_cree_bannette_priv || $_SESSION['abon_cree_bannette_priv']==1)) $_SESSION['abon_cree_bannette_priv'] = 1 ;
	else $_SESSION['abon_cree_bannette_priv'] = 0 ;
	
	global $script_test_form;

	switch ($opac_show_onglet_empr) {
		case 1:
			$empr_link_onglet="./index.php?search_type_asked=connect_empr";
			break;
		case 2:
			$empr_link_onglet="./empr.php";
			break;
	}
	$search_p= new search_persopac();
	$onglets_search_perso=$search_p->directlink_user;	
	$onglets_search_perso_form=$search_p->directlink_user_form;
	switch ($search_type) {
		case "simple_search":
			// les tests de formulaire
			$result = $script_test_form;	
			$tests = test_field("search_input", "query", "recherche");
			$result = str_replace("!!tests!!", $tests, $result);
	
			// les typ_doc
			if ($opac_search_show_typdoc) {
				$query = "SELECT typdoc FROM notices where typdoc!='' GROUP BY typdoc";
				$result2 = pmb_mysql_query($query, $dbh);
				$toprint_typdocfield = " <select name='typdoc'>";
				$toprint_typdocfield .= "  <option ";
				$toprint_typdocfield .=" value=''";
				if ($typdoc=='') $toprint_typdocfield .=" selected";
				$toprint_typdocfield .=">".$msg["simple_search_all_doc_type"]."</option>\n";
				$doctype = new marc_list('doctype');
				while (($rt = pmb_mysql_fetch_row($result2))) {
					$obj[$rt[0]]=1;
				}	
				foreach ($doctype->table as $key=>$libelle){
					if ($obj[$key]==1){
						$toprint_typdocfield .= "  <option ";
						$toprint_typdocfield .= " value='$key'";
						if ($typdoc == $key) $toprint_typdocfield .=" selected";
						$toprint_typdocfield .= ">".htmlentities($libelle,ENT_QUOTES, $charset)."</option>\n";
					}
				}
				$toprint_typdocfield .= "</select>";
			} else $toprint_typdocfield="";
				
			if ($opac_search_other_function) $toprint_typdocfield.=search_other_function_filters();
	
			$toprint_typdocfield.="<br />";
			
			// le contenu
			$result .= $search_input;

			// on met la valeur a jour
			$result = str_replace("!!user_query!!", htmlentities($value,ENT_QUOTES,$charset), $result);
			$result = str_replace("<!--!!typdoc_field!!-->", $toprint_typdocfield, $result);
			if ($opac_autolevel2) {
				$result=str_replace("!!action_simple_search!!","./index.php?lvl=more_results&autolevel1=1",$result);
			} else {
				$result=str_replace("!!action_simple_search!!","./index.php?lvl=search_result",$result);
			}
			
			if (!$opac_recherches_pliables) 
				$ou_chercher="<div id='simple_search_zone'>".do_ou_chercher()."</div>";
			elseif ($opac_recherches_pliables==1) 
				$ou_chercher="<div id='simple_search_zone'>".gen_plus_form("zsimples",$msg["rechercher_dans"],do_ou_chercher(),false)."</div>" ;
			elseif ($opac_recherches_pliables==2) 
				$ou_chercher="<div id='simple_search_zone'>".gen_plus_form("zsimples",$msg["rechercher_dans"],do_ou_chercher(),true)."</div>" ;
			elseif ($opac_recherches_pliables==3)
				// les options de recherches sont invisibles, pas dépliables. 
				$ou_chercher="\n".do_ou_chercher_hidden()."\n" ;
			
			$result = str_replace("<!--!!ou_chercher!!-->", $ou_chercher, $result);
			// map
			if($opac_map_activate){
				$layer_params = json_decode($opac_map_base_layer_params,true);
				$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
				if(count($layer_params)){
					if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
					if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
					if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
				}				
				$size=explode("*",$opac_map_size_search_edition);
				if(count($size)!=2)$map_size="width:800px; height:480px;";
				$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
				
				if(!$map_emprises_query)$map_emprises_query = array();
				$map_holds=array();
				foreach($map_emprises_query as $map_hold){
					$map_holds[] = array(
							"wkt" => $map_hold,
							"type"=> "search",
							"color"=> null,
							"objects"=> array()
					);
				}
				$r="<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"map_emprises_query\",searchHolds:".json_encode($map_holds,true)."'></div>";				
				$result = str_replace("!!map!!", $r,  $result);
			}	
			// on se place dans le bon champ
			// $result .= form_focus("search_input", "query");
			$others="";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
					else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
				}
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";			
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			$result=str_replace("!!others!!",$others,$result);
			$result.=$onglets_search_perso_form;
			break;
			
		//Recherche avancee
		case "extended_search":
			global $mode_aff;
			if ($mode_aff) {
				if ($mode_aff=="aff_module") {
					//ajout de la recherche dans l'historique 
					$_SESSION["nb_queries"]=$_SESSION["nb_queries"]+1;
					$n=$_SESSION["nb_queries"];
					$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
					switch ($_SESSION["last_module_search"]["search_mod"]) {
						case 'etagere_see':
							//appel de la fonction tableau_etagere du fichier etagere_func.inc.php
							$r1 = $msg["etagere_query"];
							$t=array();
							$t=tableau_etagere($_SESSION["last_module_search"]["search_id"]);
							$r=$r1." '".$t[0]["nometagere"]."'";
						break;
						case 'categ_see':
							// instanciation de la categorie
							$ourCateg = new categorie($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg["category"];
							$r=$r1." '".$ourCateg->libelle."'";
						break;
						case 'indexint_see':
							// instanciation de la classe indexation
							$r1= $msg["indexint_search"];
							$ourIndexint = new indexint($_SESSION["last_module_search"]["search_id"]);
							$r=$r1." '".$ourIndexint->name." ".$ourIndexint->comment."'";
							
						break;
						case 'section_see':
							$resultat=pmb_mysql_query("select location_libelle from docs_location where idlocation='".addslashes($_SESSION["last_module_search"]["search_location"])."'");
							$j=pmb_mysql_fetch_array($resultat);
							$localisation_=$j["location_libelle"];
							pmb_mysql_free_result($resultat);
							$resultat=pmb_mysql_query("select section_libelle from docs_section where idsection='".addslashes($_SESSION["last_module_search"]["search_id"])."'");
							$j=pmb_mysql_fetch_array($resultat);
							$section_=$j["section_libelle"];
							pmb_mysql_free_result($resultat);
							$r1 = $localisation_." => ".$msg["section"];
							$r=$r1." '".$section_."'";
						break;
						case "author_see" :
							$ourAuthor = new auteur($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['author'];
							$r = $r1." '".$ourAuthor->isbd_entry."'";
							break;
						case "coll_see" :
							$ourColl = new collection($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['coll_search'];
							$r = $r1." '".$ourColl->isbd_entry."'";
							break;
						case "subcoll_see" :
							$ourSubcoll = new subcollection($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['subcoll_search'];
							$r = $r1." '".$ourSubcoll->isbd_entry."'";
							break;
						case "titre_uniforme_see" :
							$ourTu = new titre_uniforme($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['titre_uniforme_search'];
							$ourTu->do_isbd();
							$r = $r1." '".$ourTu->tu_isbd."'";
							break;
						case "publisher_see" :
							$ourPub = new publisher($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['publisher_search'];
							$r = $r1." '".$ourPub->isbd_entry."'";
							break;
						case "serie_see" :
							$ourSerie = new serie($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['serie_query'];
							$r = $r1." '".$ourSerie->name."'";
							break;
						case "concept_see" :
							$ourConcept = new skos_concept($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg['skos_concept'];
							$r = $r1." '".$ourConcept->get_display_label()."'";
							break;
						case "authperso_see" :
							$ourAuth = new authperso_authority($_SESSION["last_module_search"]["search_id"]);
							$r1 = $ourAuth->info['authperso']['name'];
							$r = $r1." '".$ourAuth->info['isbd']."'";
							break;
					}
					$_SESSION["human_query".$n]=$r;
					$_SESSION["search_type".$n]="module";
				} else {
					if ($_SESSION["last_query"]) {
						$n=$_SESSION["last_query"];
						if ($_SESSION["lq_facette"]) $facette=true;
					} else {
						$n=$_SESSION["nb_queries"];	
					}	
				}	
	       		//générer les critères de la multi_critères
	       		//Attention ! si on est déjà dans une facette !
				if ($facette) 
					search::unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
				else {
		       		global $search;
		       		$search[0]="s_1";
		       		$op_="EQ";
		       								
					//operateur
	    			$op="op_0_".$search[0];
	    			global $$op;
	    			$$op=$op_;
	    		    		
	    			//contenu de la recherche
	    			$field="field_0_".$search[0];
	    			$field_=array();
	    			$field_[0]=$n;
	    			global $$field;
	    			$$field=$field_;
	    	    	
	    	    	//opérateur inter-champ
	    			$inter="inter_0_".$search[0];
	    			global $$inter;
	    			$$inter="";
	    		    		
	    			//variables auxiliaires
	    			$fieldvar_="fieldvar_0_".$search[0];
	    			global $$fieldvar_;
	    			$$fieldvar_="";
	    			$fieldvar=$$fieldvar_;
				}
			}
			
			if($search_in_perio){
				global $search;
				$search[0]="f_34";
				//opérateur
	    		$op="op_0_".$search[0];
	    		global $$op;
	    		$op_ ="EQ";
	    		$$op=$op_;	    		    			
	    		//contenu de la recherche
	    		$field="field_0_".$search[0];
	    		$field_=array();
	    		$field_[0]=$search_in_perio;
	    		global $$field;
	    		$$field=$field_;
	    		
	    		$search[1]="f_42";
	    		//opérateur
	    		$op="op_1_".$search[0];
	    		global $$op;
	    		$op_ ="BOOLEAN";
	    		$$op=$op_;	    		    				    		
			} else {
				if ($get_query) {
					if (($_SESSION["last_query"]==$get_query)&&($_SESSION["lq_facette_test"])) {
						search::unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
					} else get_history($get_query);
				}
			}
			
			$es=new search();
			if($onglet_persopac){				
				$search_form=$search_form_perso;
				global $search;
				if (!$search) {
					$search_p_direct= new search_persopac($onglet_persopac);
					$es->unserialize_search($search_p_direct->query);	
				}
			} 
			if($limitsearch){				
				$search_form=$search_form_perso_limitsearch;
			}
			if (($onglet_persopac)&&($lvl=="search_result")) $es->reduct_search();

			if($opac_autolevel2==2){
				$result=$es->show_form("./index.php?lvl=$lvl&search_type_asked=extended_search","./index.php?lvl=more_results&mode=extended");
			}else{
				$result=$es->show_form("./index.php?lvl=$lvl&search_type_asked=extended_search","./index.php?lvl=search_result&search_type_asked=extended_search");
			}
			
			$others="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;
			if ($opac_allow_term_search) $others2="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>\n";
			else $others2="" ;
			if ($opac_allow_tags_search) $others2.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others2.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others2.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others2.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
			if ($opac_allow_external_search) $others2.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			$result=str_replace("!!others!!",$others,$result);
			$result=str_replace("!!others2!!",$others2,$result);
			$result="<div id='search'>".$result."</div>";
			$result.=$onglets_search_perso_form;
			break;
		//Recherche avancee
		case "external_search":
			//Si c'est une multi-critere, on l'affiche telle quelle
			global $external_type; 
			if ($external_type) $_SESSION["ext_type"]=$external_type; 
			global $mode_aff;
			//Affinage
			if ($mode_aff) {
				if ($mode_aff=="aff_module") {
					//ajout de la recherche dans l'historique 
					$_SESSION["nb_queries"]=$_SESSION["nb_queries"]+1;
					$n=$_SESSION["nb_queries"];
					$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
					switch ($_SESSION["last_module_search"]["search_mod"]) {
						case 'etagere_see':
							//appel de la fonction tableau_etagere du fichier etagere_func.inc.php
							$r1 = $msg["etagere_query"];
							$t=array();
							$t=tableau_etagere($_SESSION["last_module_search"]["search_id"]);
							$r=$r1." '".$t[0]["nometagere"]."'";
						break;
						case 'categ_see':
							// instanciation de la catégorie
							$ourCateg = new categorie($_SESSION["last_module_search"]["search_id"]);
							$r1 = $msg["category"];
							$r=$r1." '".$ourCateg->libelle."'";
						break;
						case 'indexint_see':
							// instanciation de la classe indexation
							$r1= $msg["indexint_search"];
							$ourIndexint = new indexint($_SESSION["last_module_search"]["search_id"]);
							$r=$r1." '".$ourIndexint->name." ".$ourIndexint->comment."'";
							
						break;
						case 'section_see':
							$resultat=pmb_mysql_query("select location_libelle from docs_location where idlocation='".addslashes($_SESSION["last_module_search"]["search_location"])."'");
							$j=pmb_mysql_fetch_array($resultat);
							$localisation_=$j["location_libelle"];
							pmb_mysql_free_result($resultat);
							$resultat=pmb_mysql_query("select section_libelle from docs_section where idsection='".addslashes($_SESSION["last_module_search"]["search_id"])."'");
							$j=pmb_mysql_fetch_array($resultat);
							$section_=$j["section_libelle"];
							pmb_mysql_free_result($resultat);
							$r1 = $localisation_." => ".$msg["section"];
							$r=$r1." '".$section_."'";
						break;
					}
					$_SESSION["human_query".$n]=$r;
					$_SESSION["search_type".$n]="module";
				} else {
					if ($_SESSION["last_query"]) {
						$n=$_SESSION["last_query"];
					} else {
						$n=$_SESSION["nb_queries"];	
					}	
				}	
			}
			
			if ($_SESSION["ext_type"]=="multi") {
				global $search;
				
				if (!$search) {
					$search[0]="s_2";
					$op_0_s_2="EQ";
					$field_0_s_2=array();
				} else {
					//Recherche du champp source, s'il n'est pas present, on decale tout et on l'ajoute
					$flag_found=false;
					for ($i=0; $i<count($search); $i++) {
						if ($search[$i]=="s_2") { $flag_found=true; break; }
					}
					if (!$flag_found) {
						//Pas trouve, on decale tout !!
						for ($i=count($search)-1; $i>=0; $i--) {
							$search[$i+1]=$search[$i];
							decale("field_".$i."_".$search[$i],"field_".($i+1)."_".$search[$i]);
							decale("op_".$i."_".$search[$i],"op_".($i+1)."_".$search[$i]);
							decale("inter_".$i."_".$search[$i],"inter_".($i+1)."_".$search[$i]);
							decale("fieldvar_".$i."_".$search[$i],"fieldvar_".($i+1)."_".$search[$i]);
						}
						$search[0]="s_2";
						$op_0_s_2="EQ";
						$field_0_s_2=array();
					}
				}
				
				if ($mode_aff) {
					//générer les critères de la multi_critères
		       		$search[1]="s_1";
		       		$op_="EQ";
		       								
					//opérateur
	    			$op="op_1_".$search[1];
	    			global $$op;
	    			$$op=$op_;
	    		    		
	    			//contenu de la recherche
	    			$field="field_1_".$search[1];
	    			$field_=array();
	    			$field_[0]=$n;
	    			global $$field;
	    			$$field=$field_;
	    	    	
	    	    	//opérateur inter-champ
	    			$inter="inter_1_".$search[1];
	    			global $$inter;
	    			$$inter="and";
	    		    		
	    			//variables auxiliaires
	    			$fieldvar_="fieldvar_1_".$search[1];
	    			global $$fieldvar_;
	    			$$fieldvar_="";
	    			$fieldvar=$$fieldvar_;
				}
				$es=new search("search_fields_unimarc");
				$result=$es->show_form("./index.php?lvl=$lvl&search_type_asked=external_search","./index.php?lvl=search_result&search_type_asked=external_search");
			} else {
				global $mode_aff;
				//Si il y a une mode d'affichage demandé, on construit l'écran correspondant
				if ($mode_aff) {
					$f=get_field_text($n);
					$user_query=$f[0];
					$look=$f[1];
					global $$look;
					$$look=1;	
					global $look_FIRSTACCESS;
					$look_FIRSTACCESS=1;
				} else {
					if ($external_env) {
						$external_env=unserialize(stripslashes($external_env));
						foreach ($external_env as $varname=>$varvalue) {
							global $$varname;
							$$varname=$varvalue;
						}
					}
				}
				$result=$search_input;
				$result=str_replace("!!user_query!!",htmlentities(stripslashes($user_query),ENT_QUOTES,$charset),$result);
				$result = str_replace("<!--!!ou_chercher!!-->", do_ou_chercher(), $result);
				$result = str_replace("<!--!!sources!!-->", do_sources(), $result);
			}
			$others="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";		
			$others.=$onglets_search_perso;	
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>\n";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
			$others2="";
			$result=str_replace("!!others!!",$others,$result);
			$result=str_replace("!!others2!!",$others2,$result);
			$result="<div id='search'>".$result."</div>";
			$result.=$onglets_search_perso_form;
			break;
			
		//Recherche par termes
		case "term_search":
			global $search_term;
			global $term_click;
			global $page_search;
			global $opac_term_search_height;
			global $opac_show_help;
			
			if (!$opac_term_search_height) $height=300; 
			else $height=$opac_term_search_height;
			
			$search_form_term = "
			<div id='search'>
			<ul class='search_tabs'>!!others!!".
				($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
			</ul>
			<div id='search_crl'></div>
			<form class='form-$current_module' name='term_search_form' method='post' action='./index.php?lvl=$lvl&search_type_asked=term_search'>
				<div class='form-contenu'>
				<!-- sel_thesaurus -->
							<span class='libSearchTermes'>".$msg["term_search_search_for"]."</span><input type='text' class='saisie-50em' name='search_term' value='".htmlentities(stripslashes($search_term),ENT_QUOTES,$charset)."'>
					<!--	Bouton Rechercher -->
						<input type='submit' class='boutonrechercher' value='$msg[142]' onClick=\"this.form.page_search.value=''; this.form.term_click.value='';\"/>\n";
			if ($opac_show_help) $search_form_term .= "<input type='submit' class='bouton' value='$msg[search_help]' onClick='window.open(\"help.php?whatis=search_terms\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
			$search_form_term .= "<input type='hidden' name='term_click' value='".htmlentities(stripslashes($term_click),ENT_QUOTES,$charset)."'/>
				<input type='hidden' name='page_search' value='".$page_search."'/>
				</div>
			</form>
			<script type='text/javascript'>
				document.forms['term_search_form'].elements['search_term'].focus();
				</script>
			</div>
			";

			//recuperation du thesaurus session 
			if(!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			else thesaurus::setSessionThesaurusId($id_thes);
			
			//affichage du selectionneur de thesaurus et du lien vers les thesaurus
			$liste_thesaurus = thesaurus::getThesaurusList();
			$sel_thesaurus = '';
			$lien_thesaurus = '';
			
			if ($opac_thesaurus != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
				$sel_thesaurus = "<select class='saisie-30em' id='id_thes' name='id_thes' ";
				$sel_thesaurus.= "onchange = \"document.location = './index.php?lvl=index&search_type_asked=term_search&id_thes='+document.getElementById('id_thes').value; \">" ;
				foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
					$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
					if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
					$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES, $charset)."</option>";
				}
				$sel_thesaurus.= "<option value=-1 ";
				if ($id_thes == -1) $sel_thesaurus.= "selected ";
				$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
				$sel_thesaurus.= "</select>&nbsp;";
				$lien_thesaurus = "<a href='./autorites.php?categ=categories&sub=thes'>".$msg[thes_lien]."</a>";
			
			}	
			$search_form_term=str_replace("<!-- sel_thesaurus -->",$sel_thesaurus,$search_form_term);
			$search_form_term=str_replace("<!-- lien_thesaurus -->",$lien_thesaurus,$search_form_term);
			
			$result=$search_form_term;

			$others="";
			$others.="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			$others.="<li id='current'>".$msg["search_by_terms"]."</li>";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			$result=str_replace("!!others!!",$others,$result);
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			$result.="
			<a name='search_frame'/>
			<iframe style='border: solid 1px black;' name='term_search' class='frame_term_search' src='".$base_path."/term_browse.php?search_term=".rawurlencode(stripslashes($search_term))."&term_click=".rawurlencode(stripslashes($term_click))."&page_search=$page_search&id_thes=$id_thes' width='100%' height='".$height."'></iframe>
			<br /><br />";
			$result.=$onglets_search_perso_form;
		break;
		
		case "tags_search":
			// les tests de formulaire
			$result = $script_test_form;	
			$tests = test_field("search_input", "query", "recherche");
			$result = str_replace("!!tests!!", $tests, $result);
			
			if ($opac_search_other_function) $toprint_typdocfield.=search_other_function_filters();
	
			// le contenu
			$result .= $search_input;
			
			// on met la valeur a jour
			$result = str_replace("!!user_query!!", htmlentities($value,ENT_QUOTES,$charset), $result);
			$result = str_replace("<!--!!typdoc_field!!-->", "", $result);
			$result = str_replace("<!--!!ou_chercher!!-->","" , $result);

			// on se place dans le bon champ
			// $result .= form_focus("search_input", "query");
			$others="";
			$others="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
			if ($opac_allow_tags_search) $others.="<li id='current'>".$msg["tags_search"]."</li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			$result=str_replace("!!others!!",$others,$result);
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			// Ajout de la liste des tags
			if($user_query=="") {
				$result.= "<h3><span>$msg[search_result_for]<b>".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."</b></span></h3>";
				$tag = new tags();
				$result.=  $tag->listeAlphabetique();
			}	
			$result.=$onglets_search_perso_form;	
			break;

		// *****************
		// Pour affichage compte emprunteur en onglet	
		case "connect_empr":
			// les tests de formulaire
			$result = $search_input;
			$others="";
			$others="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;			
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if ($opac_show_onglet_empr) {
				if (!$_SESSION["user_code"]) $others.="<li id='current'>".$msg["onglet_empr_connect"]."</li>";
				else $others.="<li id='current'>".$msg["onglet_empr_compte"]."</li>";
			}
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			$result=str_replace("!!account_or_form_empr_connect!!",affichage_onglet_compte_empr(),$result);
			$result=str_replace("!!others!!",$others,$result);
			$result.=$onglets_search_perso_form;
			break;
		case "search_perso":
			// les tests de formulaire
			$result = $search_input;
			$others="";
			$others="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li id='current'>".$msg["search_perso_menu"]."</li>";
			$others.=$onglets_search_perso;			
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";			
			if ($opac_show_onglet_perio_a2z) $others.="<li><a href=\"./index.php?search_type_asked=perio_a2z\">".$msg["a2z_onglet"]."</a></li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}			
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			
			$search_p= new search_persopac();
			$result=str_replace("!!contenu!!",$search_p->do_list(),$result);
			$result=str_replace("!!others!!",$others,$result);
		break;
				
		case "perio_a2z":
			global $opac_perio_a2z_abc_search;
			global $opac_perio_a2z_max_per_onglet;
			$result=$search_input;
			$others.="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
			if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
			$others.=$onglets_search_perso;
			if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
			if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
			if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
			if ($opac_show_onglet_perio_a2z) $others.="<li id='current'>".$msg["a2z_onglet"]."</li>";
			if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
				if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
				else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
			if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
			if ($opac_show_onglet_map && $opac_map_activate) $others.="<li><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
			$result=str_replace("!!others!!",$others,$result);
			
			// affichage des _perio_a2z		
			$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);		
			$a2z_form=$a2z->get_form();			
			$a2z_form.=$onglets_search_perso_form;	
			$result=str_replace("!!contenu!!",$a2z_form,$result);
			break;
			
			case "map":
				$result = $search_input;
				$others.="<li><a href=\"./index.php?search_type_asked=simple_search\">".$msg["simple_search"]."</a></li>\n";
				if ($opac_allow_personal_search) $others.="<li><a href=\"./index.php?search_type_asked=search_perso\">".$msg["search_perso_menu"]."</a></li>";
				$others.=$onglets_search_perso;
				if ($opac_allow_extended_search) $others.="<li><a href=\"./index.php?search_type_asked=extended_search\">".$msg["extended_search"]."</a></li>";
				if ($opac_allow_term_search) $others.="<li><a href=\"./index.php?search_type_asked=term_search\">".$msg["term_search"]."</a></li>";
				if ($opac_allow_tags_search) $others.="<li><a href=\"./index.php?search_type_asked=tags_search\">".$msg["tags_search"]."</a></li>";
				if ($opac_show_onglet_perio_a2z) $others.="<li>".$msg["a2z_onglet"]."</li>";
				if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
					if (!$_SESSION["user_code"]) $others.="<li><a href=\"./index.php?search_type_asked=connect_empr\">".$msg["onglet_empr_connect"]."</a></li>";
					else $others.="<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
				}
				if ($opac_allow_external_search) $others.="<li><a href=\"./index.php?search_type_asked=external_search&external_type=simple\">".$msg["connecteurs_external_search"]."</a></li>";
				if ($opac_show_onglet_map && $opac_map_activate) $others.="<li id='current'><a href=\"./index.php?search_type_asked=map\">".$msg["search_by_map"]."</a></li>";
				$result=str_replace("!!others!!",$others,$result);
					
				// affichage page géolocalisation
				global $msg;
				global $dbh;
				global $charset,$lang;
				global $all_query,$typdoc_query, $statut_query, $docnum_query, $pmb_indexation_docnum_allfields, $pmb_indexation_docnum;
				global $categ_query,$thesaurus_auto_postage_search,$auto_postage_query;
				global $thesaurus_concepts_active,$concept_query;
				global $map_echelle_query,$map_projection_query,$map_ref_query,$map_equinoxe_query;
				global $opac_map_size_search_edition;
				global $opac_map_base_layer_type;
				global $opac_map_base_layer_params;
				global $map_emprises_query;
				
				// on commence par créer le champ de sélection de document
				// récupération des types de documents utilisés.
				
				$query = "SELECT count(typdoc), typdoc ";
				$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
				$res = @pmb_mysql_query($query, $dbh);
				$toprint_typdocfield .= "  <option value=''>$msg[tous_types_docs]</option>\n";
				$doctype = new marc_list('doctype');
				while (($rt = pmb_mysql_fetch_row($res))) {
					$obj[$rt[1]]=1;
					$qte[$rt[1]]=$rt[0];
				}
				foreach ($doctype->table as $key=>$libelle){
					if ($obj[$key]==1){
						$toprint_typdocfield .= "  <option ";
						$toprint_typdocfield .= " value='$key'";
						if ($typdoc == $key) $toprint_typdocfield .=" selected='selected' ";
						$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
					}
				}
				
				// récupération des statuts de documents utilisés.
				$query = "SELECT count(statut), id_notice_statut, gestion_libelle ";
				$query .= "FROM notices, notice_statut where id_notice_statut=statut GROUP BY id_notice_statut order by gestion_libelle";
				$res = pmb_mysql_query($query, $dbh);
				$toprint_statutfield .= "  <option value=''>$msg[tous_statuts_notice]</option>\n";
				while ($obj = @pmb_mysql_fetch_row($res)) {
					$toprint_statutfield .= "  <option value='$obj[1]'";
					if ($statut_query==$obj[1]) $toprint_statutfield.=" selected";
					$toprint_statutfield .=">".htmlentities($obj[2]."  (".$obj[0].")",ENT_QUOTES, $charset)."</OPTION>\n";
				}
				
				$search_form_map = str_replace("!!typdocfield!!", $toprint_typdocfield, $search_form_map);
				$search_form_map = str_replace("!!statutfield!!", $toprint_statutfield, $search_form_map);
				$search_form_map = str_replace("!!all_query!!", htmlentities(stripslashes($all_query),ENT_QUOTES, $charset),  $search_form_map);
				$search_form_map = str_replace("!!categ_query!!", htmlentities(stripslashes($categ_query),ENT_QUOTES, $charset),  $search_form_map);
				
				if($thesaurus_concepts_active){
					$search_form_map = str_replace("!!concept_query!!", htmlentities(stripslashes($concept_query),ENT_QUOTES, $charset),  $search_form_map);
				}
				// map
				$layer_params = json_decode($opac_map_base_layer_params,true);
				$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
				if(count($layer_params)){
					if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
					if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
					if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
				}
				
				$size=explode("*",$opac_map_size_search_edition);
				if(count($size)!=2)$map_size="width:800px; height:480px;";
				$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
				
				if(!$map_emprises_query)$map_emprises_query = array();
				$map_holds=array();
				foreach($map_emprises_query as $map_hold){
					$map_holds[] = array(
							"wkt" => $map_hold,
							"type"=> "search",
							"color"=> null,
							"objects"=> array()
					);
				}
				$r="<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"map_emprises_query\",searchHolds:".json_encode($map_holds,true)."'></div>";
				
				$search_form_map = str_replace("!!map!!", $r,  $search_form_map);
				
				//champs maps
				$requete = "SELECT map_echelle_id, map_echelle_name FROM map_echelles ORDER BY map_echelle_name ";
				$projections=gen_liste($requete,"map_echelle_id","map_echelle_name","map_echelle_query","",$map_echelle_query,0,"",0,$msg['map_echelle_vide']);
				$search_form_map=str_replace("!!map_echelle_list!!",$projections,$search_form_map);
				
				$requete = "SELECT map_projection_id, map_projection_name FROM map_projections ORDER BY map_projection_name ";
				$projections=gen_liste($requete,"map_projection_id","map_projection_name","map_projection_query","",$map_projection_query,0,"",0,$msg['map_projection_vide']);
				$search_form_map=str_replace("!!map_projection_list!!",$projections,$search_form_map);
				
				$requete = "SELECT map_ref_id, map_ref_name FROM map_refs ORDER BY map_ref_name ";
				$refs=gen_liste($requete,"map_ref_id","map_ref_name","map_ref_query","",$map_ref_query,0,"",0,$msg['map_ref_vide']);
				$search_form_map=str_replace("!!map_ref_list!!",$refs,$search_form_map);
				
				$search_form_map=str_replace("!!map_equinoxe_value!!",$map_equinoxe_query,$search_form_map);
				
				$checkbox="";
				if($thesaurus_auto_postage_search){
					$checkbox = "
					<div class='colonne'>
					<div class='row'>
					<input type='checkbox' !!auto_postage_checked!! id='auto_postage_query' name='auto_postage_query'/><label for='auto_postage_query'>".$msg["search_autopostage_check"]."</label>
					</div>
					</div>";
					$checkbox = str_replace("!!auto_postage_checked!!",   (($auto_postage_query) ? 'checked' : ''),  $checkbox);
				}
				$search_form_map = str_replace("!!auto_postage!!",   $checkbox,  $search_form_map);
				
				if($pmb_indexation_docnum){
					$checkbox = "<div class='colonne'>
					<div class='row'>
					<input type='checkbox' !!docnum_query_checked!! id='docnum_query' name='docnum_query'/><label for='docnum_query'>$msg[docnum_indexation]</label>
					</div>
					</div>";
					$checkbox = str_replace("!!docnum_query_checked!!",   (($pmb_indexation_docnum_allfields || $docnum_query) ? 'checked' : ''),  $checkbox);
					$search_form_map = str_replace("!!docnum_query!!",   $checkbox,  $search_form_map);
				} else $search_form_map = str_replace("!!docnum_query!!", '' ,  $search_form_map);
			//	$search_form_map = str_replace("!!base_url!!",     $this->base_url,$search_form_map);

				
				$result=str_replace("!!contenu!!",$search_form_map,$result);
				break;
						
	}
	return $result;
}

function do_ou_chercher () {
	global $look_TITLE,
	       $look_AUTHOR,
	       $look_PUBLISHER,
	       $look_TITRE_UNIFORME,
	       $look_COLLECTION,
	       $look_SUBCOLLECTION,
	       $look_CATEGORY,
	       $look_INDEXINT,
	       $look_KEYWORDS,
	       $look_ABSTRACT,
	       $look_ALL,
	       $look_DOCNUM,
	       $look_CONTENT,
		   $look_CONCEPT;

	global $look_FIRSTACCESS ; // si 0 alors premier Acces : la rech par defaut est cochee
	
	// pour mise en service de cette precision de recherche : commenter cette partie 
	/*
	$look_TITLE = "1" ;        
	$look_AUTHOR = "1" ;              
	$look_PUBLISHER = "1" ;           
	$look_COLLECTION = "1" ;          
	$look_SUBCOLLECTION = "1" ;       
	$look_CATEGORY = "1" ;            
	$look_INDEXINT = "1" ;            
	$look_KEYWORDS = "1" ;            
	$look_ABSTRACT = "1" ;            
	$look_CONTENT = "1" ;  
	return "";
	*/
	// pour mise en service de cette precision de recherche : commenter jusque la
	
	// on recupere les globales de ce qui est autorise en recherche dans le parametrage de l'OPAC
	global	$opac_modules_search_title,
		$opac_modules_search_author,
		$opac_modules_search_publisher,
		$opac_modules_search_titre_uniforme,
		$opac_modules_search_collection,
		$opac_modules_search_subcollection,
		$opac_modules_search_category,
		$opac_modules_search_indexint,
		$opac_modules_search_keywords,
		$opac_modules_search_abstract,
		$opac_modules_search_all,
		$opac_modules_search_docnum,
		$pmb_indexation_docnum,
		$opac_modules_search_concept,
		$opac_allow_tags_search;
		// $opac_modules_search_content; inutilise pour l'instant, le search_abstract cherche aussi dans les notes de contenu
	
	global $msg,$get_query;
	
	if (!$look_FIRSTACCESS && !$get_query ) {
		// premier acces :
		if ($opac_modules_search_title==2) $look_TITLE=1;
		if ($opac_modules_search_author==2) $look_AUTHOR=1 ;
		if ($opac_modules_search_publisher==2) $look_PUBLISHER = 1 ; 
		if ($opac_modules_search_titre_uniforme==2) $look_TITRE_UNIFORME = 1 ; 
		if ($opac_modules_search_collection==2) $look_COLLECTION = 1 ;	
		if ($opac_modules_search_subcollection==2) $look_SUBCOLLECTION = 1 ;
		if ($opac_modules_search_category==2) $look_CATEGORY = 1 ;
		if ($opac_modules_search_indexint==2) $look_INDEXINT = 1 ;
		if ($opac_modules_search_keywords==2) $look_KEYWORDS = 1 ;
		if ($opac_modules_search_abstract==2) $look_ABSTRACT = 1 ;
		if ($opac_modules_search_all==2) $look_ALL = 1 ;
		if ($opac_modules_search_docnum==2) $look_DOCNUM = 1;
		if ($opac_modules_search_concept==2) $look_CONCEPT = 1;
	}
	if ($look_TITLE) 		$checked_TITLE = "checked" ;        
	if ($look_AUTHOR)		$checked_AUTHOR = "checked" ;              
	if ($look_PUBLISHER)		$checked_PUBLISHER = "checked" ;   
	if ($look_TITRE_UNIFORME)		$checked_TITRE_UNIFORME = "checked" ;          
	if ($look_COLLECTION)		$checked_COLLECTION = "checked" ;          
	if ($look_SUBCOLLECTION)	$checked_SUBCOLLECTION = "checked" ;       
	if ($look_CATEGORY)		$checked_CATEGORY = "checked" ;            
	if ($look_INDEXINT)		$checked_INDEXINT = "checked" ;            
	if ($look_KEYWORDS)		$checked_KEYWORDS = "checked" ;            
	if ($look_ABSTRACT)		$checked_ABSTRACT = "checked" ;    
	if ($look_ALL)		$checked_ALL = "checked" ;
	if ($look_DOCNUM) $checked_DOCNUM = "checked";
	if ($look_CONCEPT) $checked_CONCEPT = "checked";

	$authpersos=new authpersos();
	$ou_chercher_authperso_tab=$authpersos->get_simple_seach_list_tpl();
	
	if (!($look_TITLE || $look_AUTHOR || $look_PUBLISHER || $look_TITRE_UNIFORME || $look_COLLECTION || $look_SUBCOLLECTION || $look_CATEGORY || $look_INDEXINT || $look_KEYWORDS || $look_ABSTRACT || $look_ALL || $look_DOCNUM || $look_CONCEPT || $authpersos->simple_seach_list_checked)) {
		$checked_TITLE = "checked" ;
		$look_TITLE = "1" ;
		$checked_AUTHOR = "checked" ;
		$look_AUTHOR = "1" ;
	}

	$ou_chercher_tab=array();
	if ($opac_modules_search_title>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_TITLE' id='look_TITLE' value='1' $checked_TITLE /><label for='look_TITLE'> $msg[titles] </label></span>";
	if ($opac_modules_search_author>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_AUTHOR' id='look_AUTHOR' value='1' $checked_AUTHOR /><label for='look_AUTHOR'> $msg[authors] </label></span>";
	if ($opac_modules_search_publisher>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_PUBLISHER' id='look_PUBLISHER' value='1' $checked_PUBLISHER /><label for='look_PUBLISHER'> $msg[publishers] </label></span>";
	if ($opac_modules_search_titre_uniforme>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_TITRE_UNIFORME' id='look_TITRE_UNIFORME' value='1' $checked_TITRE_UNIFORME/><label for='look_TITRE_UNIFORME'> ".$msg["titres_uniformes"]." </label></span>";
	if ($opac_modules_search_collection>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_COLLECTION' id='look_COLLECTION' value='1' $checked_COLLECTION /><label for='look_COLLECTION'> $msg[collections] </label></span>";
	if ($opac_modules_search_subcollection>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_SUBCOLLECTION' id='look_SUBCOLLECTION' value='1' $checked_SUBCOLLECTION /><label for='look_SUBCOLLECTION'> $msg[subcollections] </label></span>";
	if ($opac_modules_search_category>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_CATEGORY' id='look_CATEGORY' value='1' $checked_CATEGORY /><label for='look_CATEGORY'> $msg[categories] </label></span>";
	if ($opac_modules_search_indexint>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_INDEXINT' id='look_INDEXINT' value='1' $checked_INDEXINT /><label for='look_INDEXINT'> $msg[indexint] </label></span>";
	if ($opac_modules_search_keywords>0) {	
		$ou_chercher_skey = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_KEYWORDS' id='look_KEYWORDS' value='1' $checked_KEYWORDS /><label for='look_KEYWORDS'> ";
	 	if($opac_allow_tags_search)	$ou_chercher_skey .= $msg['tag'];
	 	else $ou_chercher_skey .= $msg['keywords'];
	 	$ou_chercher_skey .= "</label></span>";
	 	$ou_chercher_tab[] = $ou_chercher_skey ; 
	}
	if ($opac_modules_search_abstract>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_ABSTRACT' id='look_ABSTRACT' value='1' $checked_ABSTRACT /><label for='look_ABSTRACT'> $msg[abstract] </label></span>";
	if ($opac_modules_search_all>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_ALL' id='look_ALL' value='1' $checked_ALL /><label for='look_ALL'> ".$msg['tous']." </label></span>";
	if (($pmb_indexation_docnum && $opac_modules_search_docnum)>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_DOCNUM' id='look_DOCNUM' value='1' $checked_DOCNUM /><label for='look_DOCNUM'> ".$msg['docnum']." </label></span>";
	if ($opac_modules_search_concept>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_CONCEPT' id='look_CONCEPT' value='1' $checked_CONCEPT /><label for='look_CONCEPT'> ".$msg['skos_view_concepts_concepts']." </label></span>";
	
	$ou_chercher_tab=array_merge($ou_chercher_tab,$ou_chercher_authperso_tab);
	
	$ou_chercher = "<div class='row'>" ;
	for ($nbopac_smodules=0;$nbopac_smodules<count($ou_chercher_tab);$nbopac_smodules++) {
		if ((($nbopac_smodules+1)/3)==(($nbopac_smodules+1) % 3)) $ou_chercher .= "</div><div class='row'>" ;
		$ou_chercher .= $ou_chercher_tab[$nbopac_smodules];
	}
	
	$ou_chercher .= "</div><div style='clear: both;'><input type='hidden' name='look_FIRSTACCESS' value='1' /></div>" ;
	$ou_chercher = str_replace ("<div class='row'></div>", "", $ou_chercher ) ;
	return $ou_chercher;
}

function do_ou_chercher_hidden () {

	// on récupère les globales de ce qui est autorisé en recherche dans le paramétrage de l'OPAC
	global	$opac_modules_search_title,
		$opac_modules_search_author,
		$opac_modules_search_publisher,
		$opac_modules_search_titre_uniforme,
		$opac_modules_search_collection,
		$opac_modules_search_subcollection,
		$opac_modules_search_category,
		$opac_modules_search_indexint,
		$opac_modules_search_keywords,
		$opac_modules_search_abstract,
		$opac_modules_search_docnum,
		$opac_modules_search_all,
		$opac_modules_search_concept;
	
	$ou_chercher_hidden = '' ;
	if ($opac_modules_search_title>1) $ou_chercher_hidden .= "<input type='hidden' name='look_TITLE' id='look_TITLE' value='1' />";
	if ($opac_modules_search_author>1) $ou_chercher_hidden .= "<input type='hidden' name='look_AUTHOR' id='look_AUTHOR' value='1' />";
	if ($opac_modules_search_publisher>1) $ou_chercher_hidden .= "<input type='hidden' name='look_PUBLISHER' id='look_PUBLISHER' value='1' />";
	if ($opac_modules_search_titre_uniforme>1) $ou_chercher_hidden .= "<input type='hidden' name='look_TITRE_UNIFORME' id='look_TITRE_UNIFORME' value='1' />";
	if ($opac_modules_search_collection>1) $ou_chercher_hidden .= "<input type='hidden' name='look_COLLECTION' id='look_COLLECTION' value='1' />";
	if ($opac_modules_search_subcollection>1) $ou_chercher_hidden .= "<input type='hidden' name='look_SUBCOLLECTION' id='look_SUBCOLLECTION' value='1' />";
	if ($opac_modules_search_category>1) $ou_chercher_hidden .= "<input type='hidden' name='look_CATEGORY' id='look_CATEGORY' value='1' />";
	if ($opac_modules_search_indexint>1) $ou_chercher_hidden .= "<input type='hidden' name='look_INDEXINT' id='look_INDEXINT' value='1' />";
	if ($opac_modules_search_keywords>1) $ou_chercher_hidden .= "<input type='hidden' name='look_KEYWORDS' id='look_KEYWORDS' value='1' /> ";
	if ($opac_modules_search_abstract>1) $ou_chercher_hidden .= "<input type='hidden' name='look_ABSTRACT' id='look_ABSTRACT' value='1' />";
	if ($opac_modules_search_all>1) $ou_chercher_hidden .= "<input type='hidden' name='look_ALL' id='look_ALL' value='1' />";
	if ($opac_modules_search_docnum>1) $ou_chercher_hidden .= "<input type='hidden' name='look_DOCNUM' id='look_DOCNUM' value='1' />";
	if ($opac_modules_search_concept>1) $ou_chercher_hidden .= "<input type='hidden' name='look_CONCEPT' id='look_CONCEPT' value='1' />";
	
	$authpersos=new authpersos();
	$ou_chercher_hidden.=$authpersos->get_simple_seach_list_tpl_hiden();
	return $ou_chercher_hidden;
}

function get_field_text($n) {
	$typ_search=$_SESSION["notice_view".$n]["search_mod"];
	switch($_SESSION["notice_view".$n]["search_mod"]) {
		case 'title':
			$valeur_champ=$_SESSION["user_query".$n];
			$typ_search="look_TITLE";
			break;
		case 'all':
			$valeur_champ=$_SESSION["user_query".$n];
			$typ_search="look_ALL";
			break;
		case 'abstract':
			$valeur_champ=$_SESSION["user_query".$n];
			$typ_search="look_ABSTRACT";
			break;
		case 'keyword':
			$valeur_champ=$_SESSION["user_query".$n];
			$typ_search="look_KEYWORDS";
			break;
		case 'author_see':
			//Recherche de l'auteur
			$author_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select concat(author_name,', ',author_rejete) from authors where author_id='".addslashes($author_id)."'";
			$r_author=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_author)) {
				$valeur_champ=pmb_mysql_result($r_author,0,0);
			}
			$typ_search="look_AUTHOR";
		break;
		case 'categ_see':
			//Recherche de la categorie
			$categ_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select libelle_categorie from categories where num_noeud='".addslashes($categ_id)."'";
			$r_cat=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_cat)) {
				$valeur_champ=pmb_mysql_result($r_cat,0,0);
			}
			$typ_search="look_CATEGORY";
		break;		
		case 'indexint_see':	
			//Recherche de l'indexation
			$indexint_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select indexint_name from indexint where indexint_id='".addslashes($indexint_id)."'";
			$r_indexint=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_indexint)) {
				$valeur_champ=pmb_mysql_result($r_indexint,0,0);
			}
			$typ_search="look_INDEXINT";
		break;		
		case 'coll_see':	
			//Recherche de l'indexation
			$coll_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select collection_name from collections where collection_id='".addslashes($coll_id)."'";
			$r_coll=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_coll)) {
				$valeur_champ=pmb_mysql_result($r_coll,0,0);
			}
			$typ_search="look_COLLECTION";
		break;		
		case 'publisher_see':	
			//Recherche de l'editeur
			$publisher_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select ed_name from publishers where ed_id='".addslashes($publisher_id)."'";
			$r_pub=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_pub)) {
				$valeur_champ=pmb_mysql_result($r_pub,0,0);
			}
			$typ_search="look_PUBLISHER";
		break;		
		case 'titre_uniforme_see':	
			//Recherche de titre uniforme
			$tu_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select tu_name from titres_uniformes where ed_id='".addslashes($tu_id)."'";
			$r_tu=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_tu)) {
				$valeur_champ=pmb_mysql_result($r_tu,0,0);
			}
			$typ_search="look_TITRE_UNIFORME";
		break;				
		case 'subcoll_see':	
			//Recherche de l'editeur
			$subcoll_id=$_SESSION["notice_view".$n]["search_id"];
			$requete="select sub_coll_name from sub_collections where sub_coll_id='".addslashes($subcoll_id)."'";
			$r_subcoll=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_subcoll)) {
				$valeur_champ=pmb_mysql_result($r_subcoll,0,0);
			}
			$typ_search="look_SUBCOLLECTION";
		break;
		case 'authperso_see':
			$authpersos=new authpersos();
			$info=$authpersos->get_field_text($_SESSION["notice_view".$n]["search_id"]);
			$valeur_champ=$info['valeur_champ'];
			$typ_search=$info['typ_search'];
		break;
		case 'concept_see':
			$concept=new skos_concept($_SESSION["notice_view".$n]["search_id"]);
			$valeur_champ=$concept->get_display_label();
			$typ_search="look_CONCEPT";
		break;
			
	}
	return array($valeur_champ,$typ_search);
}

function do_sources() {
	global $charset,$source, $dbh, $msg;
	$r="";
	if (!$source) $source=array();
	//Recherche des sources
    $requete="SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_categ.opac_expanded, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed,connectors_sources.opac_selected, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) WHERE connectors_sources.opac_allowed=1 ORDER BY connectors_categ_sources.num_categ DESC, connectors_sources.name";
    $resultat=pmb_mysql_query($requete, $dbh);
    if ($source) $_SESSION["checked_sources"]=$source;
    if ($_SESSION["checked_sources"]&&(!$source)) $source=$_SESSION["checked_sources"];
    //gen_plus_form("zsources",$msg["connecteurs_source_label"],"<!--!!sources!!-->",true)
    $old_categ = 0;
    $count = 0;
    $paquets_de_sources = array();
    $paquets_de_source = array();
    while (($srce=pmb_mysql_fetch_object($resultat))) {
    	if ($old_categ !== $srce->num_categ) {
    		//$msg["connecteurs_source_label"]
    		if ($paquets_de_source) $paquets_de_sources[] = $paquets_de_source; 
    		$paquets_de_source = array();
    		$paquets_de_source["id"] = $srce->num_categ;
       		$paquets_de_source["name"] = $srce->categ_name ? $srce->categ_name : $msg["source_no_category"];
    		$paquets_de_source["opac_expanded"] = $srce->opac_expanded ? true : false;
       		
			// gen_plus_form("zsources".$count, $srce->categ_name ,"sdfsdfsdfsdf",true);
	   		$count++;
	   		$old_categ = $srce->num_categ;
    	}
   		$paquets_de_source["content"] .="<div style='width:30%; float:left'>
				<input type='checkbox' ".($_SESSION["source_".$srce->source_id."_cancel"]==2 ? 'DISABLED' : "")." name='source[]' value='".$srce->source_id."' id='source_".$srce->source_id."_".$count."' onclick='change_source_checkbox(source_".$srce->source_id."_".$count.", ".$srce->source_id.");'";
   		if (array_search($srce->source_id,$source)!==false) {
   			$paquets_de_source["content"] .= " checked";
   		} else if (!count($source) && $srce->opac_selected) {
   			$paquets_de_source["content"] .= " checked";
   		}
   		$paquets_de_source["content"] .= "/>".($_SESSION["source_".$srce->source_id."_cancel"]==2 ? "<s>" : "")."<label for='source_".$srce->source_id."_".$count."'><img src='images/".($srce->repository==1?"entrepot.png":"globe.gif")."'/>&nbsp;".htmlentities($srce->name.($srce->comment?" : ".$srce->comment:""),ENT_QUOTES,$charset).($_SESSION["source_".$srce->source_id."_cancel"]==2 ? "</s> <i>(".$msg["source_blocked"].")</i>" : "")."</label>
			</div><div class='row'></div>";
    }
	if ($paquets_de_source) $paquets_de_sources[] = $paquets_de_source; 
    foreach($paquets_de_sources as $paquets_de_source) {
    	$r .= gen_plus_form("zsources".$paquets_de_source["id"], $paquets_de_source["name"], $paquets_de_source["content"], $paquets_de_source["opac_expanded"])."\n\n";
    }
   	
   	return $r;
}

function decale($var,$var1) {
	global $$var;
	global $$var1;
	$$var1=$$var;
}