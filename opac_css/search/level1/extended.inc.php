<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended.inc.php,v 1.27 2014-07-02 13:24:57 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/searcher.class.php");

$nb_result_extended=0;
$flag=false;
//Vérification des champs vides
//Y-a-t-il des champs ?
if (count($search)==0) {
	$search_error_message=$msg["extended_use_at_least_one"];
	$flag=true;
} else {
    //Vérification des champs vides
    for ($i=0; $i<count($search); $i++) {
    	$op="op_".$i."_".$search[$i];
    	global $$op;
    	$field_="field_".$i."_".$search[$i];
    	global $$field_;
    	$field=$$field_;
    	$s=explode("_",$search[$i]);
    	if ($s[0]=="f") {
    		$champ=$es->fixedfields[$s[1]]["TITLE"];
    	} elseif ($s[0]=="s") { 
    		$champ=$es->specialfields[$s[1]]["TITLE"];	
    	} else {
    		$champ=$es->pp->t_fields[$s[1]]["TITRE"];
    	}
    	if (((string)$field[0]=="") && (!$es->op_empty[$$op])) {
    		$search_error_message=sprintf($msg["extended_empty_field"],$champ);
    		$flag=true;
 			break;
    	}
    }
}
if (!$flag) {
	$searcher_extended = new searcher_extended();
	$searcher_extended->get_result();
	$nb_result_extended =  $searcher_extended->get_nb_results();

	//	Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['extended'] = $nb_result_extended;
	}
	
	if($opac_allow_affiliate_search && $allow_search_affiliate_and_external){
		$search_result_affiliate_extented =  str_replace("!!mode!!","extended",$search_extented_result_affiliate_lvl1);
		$search_result_affiliate_extented =  str_replace("!!search_type!!","notices",$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!label!!",$es->make_human_query(),$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!nb_result!!",$nb_result_extended,$search_result_affiliate_extented);
		if($nb_result_extended){
			$link = "<a href='#' onclick=\"document.search_form.action = './index.php?lvl=more_results&mode=extended&tab=catalog'; document.search_form.submit();return false;\">".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a>";
		}else $link = "";
		$search_result_affiliate_extented =  str_replace("!!link!!",$link,$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!style!!","",$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!user_query!!",rawurlencode((($charset == "utf-8")?$user_query:utf8_encode($es->serialize_search()))),$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!form_name!!","search_form",$search_result_affiliate_extented);
		$search_result_affiliate_extented =  str_replace("!!form!!","",$search_result_affiliate_extented);
		print $search_result_affiliate_extented;
	}else{	
		if ($nb_result_extended) {
			print pmb_bidi("<strong>".$es->make_human_query()."</strong> ".$nb_result_extended." $msg[results] ");
			print "<a href=\"#\" onclick=\"document.search_form.action='./index.php?lvl=more_results&mode=extended'; document.search_form.submit(); return false;\">$msg[suite]&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/></a><br /><br />";
		}
	}	
	
}

?>