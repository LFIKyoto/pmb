<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+

// $Id: navigator.inc.php,v 1.39 2015-06-17 13:38:05 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/*DB commenté car n'affiche rien lors de l'appel à etageres_see si showet non défini
if ($lvl=="etagere_see") 
	$navig.="<td><a href=\"index.php?lvl=etageres_see\" class='etageres_see'><span>".$msg["etageres_see"]."</span></a></td>\n";
*/

//Création de la recherche équivalente à tous les champs si on est en autolevel
//Si le niveau 1 est shunté
if (($opac_autolevel2)&&($autolevel1)&&(!$get_last_query)&&($user_query)) {
	//On fait la recherche tous les champs
	$search_all_fields = new searcher_all_fields(stripslashes($user_query),$map_emprises_query);
	$nb_result = $search_all_fields->get_nb_results();
	if ($nb_result) {
		$count=$nb_result;
		$l_typdoc= implode(",",$search_all_fields->get_typdocs());	
		$mode="tous";
		//définition du formulaire
		$form_lvl1 = "
			<form name=\"search_tous\" action=\"./index.php?lvl=more_results\" method=\"post\">";
			if (function_exists("search_other_function_post_values")){
				$form_lvl1 .=search_other_function_post_values(); 
			}
			
			if(count($map_emprise_query)){
				foreach($map_emprises_query as $map_emprise_query){
					$form_lvl1 .= "
				<input type=\"hidden\" name=\"map_emprises_query[]\" value=\"$map_emprise_query\">";
				}
			}
		  	$form_lvl1 .= "
		  		<input type=\"hidden\" name=\"mode\" value=\"tous\">
		  		<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">
		  		<input type=\"hidden\" name=\"count\" value=\"".$nb_result."\">
		  		<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">
		  		<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">";
		  	if($opac_indexation_docnum_allfields) 
		  		$form_lvl1 .= "<input type=\"hidden\" name=\"join\" value=\"".htmlentities($join,ENT_QUOTES,$charset)."\">";
		  	$form_lvl1 .= "
			</form>";
		unset($_SESSION["level1"]);
		$_SESSION["level1"]["tous"]["form"]=$form_lvl1;
		$_SESSION["level1"]["tous"]["count"]=$nb_result;
		$_SESSION["search_type"]="simple_search";
		rec_history();
		$_SESSION["new_last_query"]=$_SESSION["nb_queries"];
	} else {
		$lvl="search_result";
		unset($autolevel1);
	}
}
elseif($lvl=='more_results' && $search_type=='extended_search' && $mode=='extended' && !$facette_test && $opac_autolevel2){
	$es->reduct_search();
	rec_history();
	$_SESSION["new_last_query"]=$_SESSION["nb_queries"];
}

if (($_SESSION["nb_queries"])&&($lvl!="search_result")){
	//On ne peut pas prendre la dernière recherche car si la dernière chose que l'on a fait c'est la navigation dans une étagère alors on obtient une page blanche
	//Cette dernière recherche n'est d'ailleurs pas cliquable dans l'historique des recherches (search_history.inc.php)
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
			$navig.="<td class='navig_actions_last_search' ><a href=\"index.php?lvl=search_result&get_query=".$i."\" class='actions_last_search'><span>".$msg["actions_last_search"]."</span></a></td>\n";
			break;
		}
	}
}
if (($lvl!="more_results")&&($_SESSION["last_query"]!="")) {
	if ($_SESSION["last_query"]==$_SESSION["nb_queries"]) 
		$search_name=" ".$msg["actions_last_page_last_search"]; 
	else {
		if ($_SESSION["lq_mode"]=="extended")
			$search_name=" ".$msg["actions_last_page_extended_search"]." ";
		else
			$search_name=" ".$msg["actions_last_page_simple_search"]." ";
		$search_name.=$msg['number'].$_SESSION["last_query"];
	}
	$navig.="<td class='navig_actions_last_page' ><a href=\"index.php?lvl=more_results&get_last_query=1\" class='actions_last_page'><span>".sprintf($msg["actions_last_page"],$_SESSION["lq_page"],$msg[$_SESSION["list_name_msg"]],$search_name);
	$navig.="</span></a></td>\n";
}
if (($_SESSION["nb_queries"])&&($lvl!="search_history")) 
	$navig.="<td class='navig_actions_history' ><a href=\"index.php?lvl=search_history\" class='actions_history'><span>".$msg["actions_history"]."</span></a></td>\n";
if (($lvl!="index")&&($lvl!="")) {
	if ($lvl!="section_see") {
		$item="";
		if ($opac_show_categ_browser) {
			$item=$msg["navig_categ"];
			$class="navig_categ";
		}
		if (($opac_show_dernieresnotices)&&(!$item)) {
			$item=$msg["navig_lastnotices"];
			$class="navig_lastnotices";
		}
		if (($opac_show_etageresaccueil)&&(!$item)) {
			$item=$msg["navig_etageres"];
			$class="navig_etageres";
		}
		if (($opac_show_marguerite_browser)&&(!$item)) {
			$item=$msg["navig_marguerite"];
			$class="navig_marguerite";
		}
		if (($opac_show_100cases_browser)&&(!$item)) {
			$item=$msg["navig_100cases"];
			$class="navig_categ";
		}
		if (!$item) {
			$item=$msg[avec_recherches]; 
			$class="avec_recherches"; 
		}
		
	} else {
		$item=$msg[avec_recherches]; 
		$class="avec_recherches"; 
	}
	$navig.="<td class='navig_actions_first_screen' ><a href=\"./index.php?lvl=index\" class='$class'><span>".sprintf($msg["actions_first_screen"],$item)."</span></a></td>\n";
	if($opac_navig_empr)  $navig.="<td class='navig_empr_bt_show_compte' ><a href=\"./empr.php\" class='$class'><span>".$msg["empr_bt_show_compte"]."</span></a></td>\n";	
}

if ($_SESSION["user_code"]){
	if($opac_show_onglet_empr==3)  $navig.="<td class='navig_empr_bt_show_compte' ><a href=\"./index.php?search_type_asked=connect_empr\" class='$class'><span>".$msg["empr_bt_show_compte"]."</span></a></td>\n";	
	elseif($opac_show_onglet_empr==4)  $navig.="<td class='navig_empr_bt_show_compte' ><a href=\"./empr.php\" class='$class'><span>".$msg["empr_bt_show_compte"]."</span></a></td>\n";	
}
if($opac_show_onglet_help && ((($lvl!="index") && ($lvl!="search_type_asked") && ($lvl!="search_result") && ($lvl!=""))||(stristr($_SERVER['REQUEST_URI'], "empr.php"))))
		$navig .= "<td class='navig_search_help' ><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\" ><span>".$msg["search_help"]."</span></a></td>\n";

if ($navig) {
	print "<div id='navigator'>\n";
	print "<strong>".$msg["actions_you_can"]."</strong>\n";
	print "<table width='100%'>";
	print "<tr>";
	print $navig;
	print("</tr>");
	print("</table>");
	print "</div><!-- fermeture de #navigator -->\n";
}else{
	print "<div id='navigator' class='empty'></div>";
}
if (((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"])))&&($lvl!="show_cart")) 
	print "<div id='resume_panier'><iframe recept='yes' recepttype='cart' frameborder='0' id='iframe_resume_panier' name='cart_info' allowtransparency='true' src='cart_info.php?' scrolling='no' scrollbar='0'></iframe></div>";
else
	print "<div id='resume_panier' class='empty'></div>";
?>