<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended.inc.php,v 1.87.2.1 2015-10-16 10:55:52 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur titre
// inclusion classe pour affichage notices (level 1)
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/classes/notice.class.php');
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($base_path.'/classes/facette_search.class.php');
require_once($base_path.'/classes/suggest.class.php');
require_once($class_path."/map/map_search_controler.class.php");

$es=new search();

$sr_form='';

global $facette_test;

$allow_search_affiliate_and_external=true;
if($opac_allow_affiliate_search || $opac_allow_external_search){
	$es_uni=new search("search_fields_unimarc");
	if(count($_SESSION['facette']) || $facette_test || $es_uni->has_forbidden_fields()){
		$allow_search_affiliate_and_external=false;
	}
}

if($opac_allow_affiliate_search && $allow_search_affiliate_and_external){
	$sr_form.= $search_result_extended_affiliate_lvl2_head;
} else {
	$sr_form.= "	<div id=\"resultatrech\"><h3>".$msg['resultat_recherche']."</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	//gestion du tri
	if (isset($_GET["sort"])) {	
		$_SESSION["last_sortnotices"]=$_GET["sort"];
	}
	if ($count>$opac_nb_max_tri) {
		$_SESSION["last_sortnotices"]="";
	}
	
	if($facette_test==1){
		global $search;
		global $name;
		global $value;
		global $champ;
		global $ss_champ;
		global $check_facette;
		
		//si rien en multi-sélection, il n'y a qu'une seule facette de cliquée
		//on l'ajoute au tableau pour avoir un traitement unique après
		if(!isset($check_facette) || !count($check_facette)){
			$check_facette = array();
			//on encode en utf-8 pour être compatible avec json_encode
			if ($charset!='utf-8') {
				$check_facette[] =  json_encode(array(utf8_encode($name),utf8_encode($value),$champ,$ss_champ));
			} else {
				$check_facette[] = json_encode(array($name,$value,$champ,$ss_champ));
			}
		}else{
			//le tableau est addslashé automatiquement
			foreach($check_facette as $k=>$v){
				$check_facette[$k]=stripslashes($v);
			}
		}

		facettes::checked_facette_search($check_facette);
		
		$_SESSION["lq_facette"]=$_SESSION["facette"];
		$_SESSION["lq_facette_search"]["lq_search"]=search::serialize_search();
	}else if( $from_see == 1 && $filtre_compare == "compare"){
		//from_see est un élément posté dans un formulaire d'une page d'autorité 
		//il flage l'origine qui nécessite une reconstruction de l'environnement de la multi-critère pour faire les filtres multiples ou le comparateur
		facettes::make_facette_search_env();
	}
	$lib_recherche=$es->make_human_query();
	
	$searcher = new searcher_extended();
	if($opac_visionneuse_allow){
		$nbexplnum_to_photo = $searcher->get_nb_explnums();	
	}
	$count = $searcher->get_nb_results();
	$l_typdoc= implode(",",$searcher->get_typdocs());// La variable global $l_typdoc est utilisée pour la photothèque
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['extended'] = $count;
	}
	
	if($count){
		if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!==""){
			$notices = $searcher->get_sorted_result($_SESSION["last_sortnotices"],$debut,$opac_search_results_per_page);	
		}else{
			$notices = $searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);	
		}
		if (count($notices)) {
			$_SESSION['tab_result_current_page'] = implode(",", $notices);
		} else {
			$_SESSION['tab_result_current_page'] = "";
		}
	}
	$sr_form.= pmb_bidi("<h3><span>$count $msg[titles_found] ".$lib_recherche."</span></h3>");
	
	// pour la DSI
	if ($opac_allow_bannette_priv && $allow_dsi_priv && ($_SESSION['abon_cree_bannette_priv']==1 || $opac_allow_bannette_priv==2)) {
		$sr_form.= "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.form_values.action='./empr.php?lvl=bannette_creer'; document.form_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
	if(!$opac_allow_affiliate_search || !$allow_search_affiliate_and_external) {
		$sr_form.= "</div>";
	}
	$sr_form.= "<div id=\"resultatrech_liste\">";
	
	if ($count) {
		if ($opac_notices_depliable) {
			if($filtre_compare=='compare'){
				$sr_form.=facette_search_compare::get_begin_result_list();
			}else{
				$sr_form.= 	$begin_result_liste;
			}
		}
		
		//impression
		$sr_form.= "<span class='print_search_result'>".$link_to_print_search_result."</span>";
		
		//gestion du tri
		if ($count<=$opac_nb_max_tri) {
			$pos=strpos($_SERVER['REQUEST_URI'],"?");
			$pos1=strpos($_SERVER['REQUEST_URI'],"get");
			if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
			else $pos1=$pos1-3;
			$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
			$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
			$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste); 
			$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
			$sr_form.= $affich_tris_result_liste;
			
			if ($_SESSION["last_sortnotices"]!="") {
				$sort=new sort('notices','session');
				$sr_form.= " ".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span>";
			} elseif ($opac_default_sort_display) {
				$sort=new sort('notices','session');
				$sr_form.= " ".$msg['tri_par']." ".$sort->descriptionTriParId("default")."<span class=\"espaceResultSearch\">&nbsp;</span>";
			}
		} else {
			$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;</span>";
		}
		//fin gestion du tri
		
		$sr_form.= $add_cart_link;
		
		if($opac_visionneuse_allow && $nbexplnum_to_photo){
			$search_to_post = $es->serialize_search();
			$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
	
			$sr_form.= "
		<script type='text/javascript'>
			function sendToVisionneuse(explnum_id){
				if (typeof(explnum_id)!= 'undefined') {
					var explnum =document.createElement('input');
					explnum.setAttribute('type','hidden');
					explnum.setAttribute('name','explnum_id');
					explnum.setAttribute('value',explnum_id);
					document.form_values.appendChild(explnum);
				}
				var mode = document.createElement('input');
				mode.setAttribute('type','hidden');
				mode.setAttribute('name','mode');
				mode.setAttribute('value','extended');
				var input = document.createElement('input');
				input.setAttribute('id','search');
				input.setAttribute('name','search');
				input.setAttribute('type','hidden');
				input.setAttribute('value',\"".addslashes($search_to_post)."\");
				oldAction=document.form_values.action;
				document.form_values.appendChild(input);
				document.form_values.appendChild(mode);
			
		
				document.form_values.action='visionneuse.php';
				document.form_values.target='visionneuse';
				document.form_values.submit();
			}
		</script>";
			
		}
	}

	//affinage
	//enregistrement de l'endroit actuel dans la session
	if ($_SESSION["last_query"]) {	$n=$_SESSION["last_query"]; } else { $n=$_SESSION["nb_queries"]; }
	
	if(count($_SESSION['facette'])==0){
		$_SESSION["notice_view".$n]["search_mod"]="extended";
		$_SESSION["notice_view".$n]["search_page"]=$page;
	}
	
	
	//affichage
	$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&get_query=$n' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
	
	//fin affinage
	// url courte
	$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"short_url\"><a target='_blank' href='$base_path/s.php?action=gen' title='".$msg["short_url_generate"]."'>".$msg["short_url_generate"]."</a></span>";
	
	//Etendre
	if ($opac_allow_external_search  && $allow_search_affiliate_and_external) {
		$sr_form.= 	"<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=multi' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
	}
	//fin etendre
	
	if ($opac_show_suggest) {
		$bt_sugg = "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";
		if ($opac_resa_popup) $bt_sugg .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
		else $bt_sugg .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";			
		$bt_sugg.= " title='".$msg["empr_bt_make_sugg"]."' >".$msg["empr_bt_make_sugg"]."</a></span>";
		$sr_form.= $bt_sugg;
	}
	
	//on suis le flag filtre/compare
	facettes::session_filtre_compare();
	$sr_form.= "<blockquote>";
	if($filtre_compare=='compare'){
		//on valide la variable session qui comprend les critères de comparaisons
		facette_search_compare::session_facette_compare();
		//affichage comparateur
		//les parametres nécéssaires
		global $pmb_compare_notice_template;
		global $pmb_compare_notice_nb;
		
		$facette_compare= new facette_search_compare($pmb_compare_notice_template,$pmb_compare_notice_nb);
		$compare=$facette_compare->compare($searcher);
		if($compare===true){
			$sr_form.=  $facette_compare->display_compare();
		}else{
			$sr_form.=  $msg[$compare];
		}
	}else{
		//si demande de réinitialisation
		if($reinit_compare==1){
			facette_search_compare::session_facette_compare(null,$reinit_compare);
		}
	
		$sr_form.= $searcher->get_current_search_map();
		
		
		$sr_form.= aff_notice(-1);
		$nb=0;
		$recherche_ajax_mode=0;
		for ($i =0 ; $i<count($notices);$i++){
			if($i>4)$recherche_ajax_mode=1;
			$sr_form.= pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
		}
		$sr_form.= aff_notice(-2);
	}
	$sr_form.= "</blockquote></div></div>";
	
	// constitution des liens
	$nbepages = ceil($count/$opac_search_results_per_page);
	$sr_form.= "<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
	
	if(!$opac_allow_affiliate_search){
		$url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.submit()";
		$action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.submit()";
	}else{
		$url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"./index.php?lvl=more_results&mode=extended&tab=catalog\"; document.form_values.submit()";
		$action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"./index.php?lvl=more_results&mode=extended&tab=catalog\"; document.form_values.submit()";
	}
	
	//Si pas de résultats, affichage des suggestions
	if(!$count && $opac_simple_search_suggestions){
		$tableSuggest="";
		if ($opac_autolevel2==2) {
			$actionSuggest = $base_path."/index.php?lvl=more_results&autolevel1=1";
		} else {
			$actionSuggest = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
		}
		
		$termes = "";

		//on va chercher le premier champ
    	$s=explode("_",$search[0]);
    	$field_="field_0_".$search[0];
    	global $$field_;
    	$field=$$field_;

    	$termes=str_replace('*','',stripslashes($_SESSION["user_query".$field[0]]));
		if (trim($termes)){
			$suggestion = new suggest($termes);
			$tmpArray = array();
			$tmpArray = $suggestion->listUniqueSimilars();
			
			if(count($tmpArray)){
				$tableSuggest.="<table><tbody>";
				foreach($tmpArray as $word){
					$tableSuggest.="<tr>
						<td>
							<a href='".$actionSuggest."&user_query=".rawurlencode($word)."'>
								<span class='facette_libelle'>".$word."</span>
							</a>
						</td>
					</tr>";
				}
				$tableSuggest.="</tbody></table>";
				
				$sr_form.= "<h3>".$msg['facette_suggest']."</h3>".$tableSuggest;
			}
		}
	}
	
	if($filtre_compare=='compare'){
		$sr_form.="<div id='navbar'><hr></div>";
	}elseif($count){
		$sr_form.="<div id='navbar'><hr />\n<center>".printnavbar($page, $nbepages, $url_page,$action)."</center></div>";
	}
	
	if(!$opac_allow_affiliate_search  || !$allow_search_affiliate_and_external) {
		$sr_form.= "	</div>";
	}
	$sr_form = str_replace('<!-- search_result_extended_affiliate_lvl2_head_link -->',$search_result_extended_affiliate_lvl2_head_wo_link,$sr_form);
	
} else {
	
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$query = $es->serialize_search();
		$as=new affiliate_search_extended($query);
		$as->getResults();
		$sr_form.= $as->results;
	}
	$sr_form.= "
	</div>
	<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
	
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['extended_affiliate'] = $as->getTotalNbResults();
	}

	$es->unserialize_search($query);
	
}
print $sr_form;


function extended_get_current_search_map($mode_search=0){
	global $opac_map_activate;
	global $opac_map_max_holds;
	global $dbh;
	global $javascript_path;
	global $opac_map_size_search_result;
	global $page;
	global $aut_id;
	$map = "";
	if($opac_map_activate){
		$map_hold = null;

		$current_search=$_SESSION["nb_queries"];

		if($current_search<=0) $current_search = 0;
		$map_search_controler = new map_search_controler($map_hold, $current_search, $opac_map_max_holds,true);
		$map_search_controler->set_mode($current_search);

		$size=explode("*",$opac_map_size_search_result);
		if(count($size)!=2)$map_size="width:800px; height:480px;";
		$map_size= "width:".$size[0]."px; height:".$size[1]."px;";
			
		$map_search_controler->ajax = true;
		$map = "
		<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$map_search_controler->get_json_informations()."'></div>
		";
			
	}
	return $map;
}
		