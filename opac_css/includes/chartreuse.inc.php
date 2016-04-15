<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chartreuse.inc.php,v 1.6 2015-04-03 11:16:16 jpermanne Exp $

function search_other_function_filters() {
	global $chu_dom,$chu_tdp,$charset;

	//Domaine
	//$chu_dom_js="var chu_dom_code= new Array();\n var chu_dom_libelle = new Array();\n";
	$requete="select notices_custom_list_value,notices_custom_list_lib from notices_custom_lists where notices_custom_champ='11' order by ordre, notices_custom_list_lib";
	$resultat=pmb_mysql_query($requete);

	$r="<select name='chu_dom'>" ;
	$r.="<option value='' ";
	if($chu_dom=="") $r.="selected=\"selected\" ";
	$r.=">".htmlentities("< Toute rubrique >",ENT_QUOTES,$charset)."</option>";
	if (pmb_mysql_num_rows($resultat)) {
		$incr=0;
		while (($app = pmb_mysql_fetch_object($resultat))) {
			$selected="";
			if ($app->notices_custom_list_value==$chu_dom) {
				$selected="selected=\"selected\"";
			} else {
				$selected='';
			}
			$r.= "<option value='".$app->notices_custom_list_value."' $selected>".htmlentities($app->notices_custom_list_lib,ENT_QUOTES,$charset)."</option>";
			//$chu_dom_js.="chu_dom_code[$incr] = \"".$app->notices_custom_list_value."\";\n";
			//$chu_dom_js.="chu_dom_libelle[$incr] = \"".$app->notices_custom_list_lib."\";\n";
			$incr++;
		}
	}
	$r.="</select>\n";
	
	//Type de document professionnel
	$chu_tdp_js="var chu_tdp_code= new Array();\n var chu_tdp_libelle = new Array();\n";
	
	$requete="select notices_custom_list_value,notices_custom_list_lib from notices_custom_lists where notices_custom_champ='12' order by ordre, notices_custom_list_lib";
	$resultat=pmb_mysql_query($requete);

	$r.="<select name='chu_tdp'>" ;
	$r.="<option value='' ";
	if($chu_tdp=="") $r.="selected=\"selected\" ";
	$r.=">".htmlentities("< Tout type de document >",ENT_QUOTES,$charset)."</option>";
	if (pmb_mysql_num_rows($resultat)) {
		$incr=0;
		while (($app = pmb_mysql_fetch_object($resultat))) {
			$selected="";
			if ($app->notices_custom_list_value==$chu_tdp) {
				$selected="selected=\"selected\"";
			} else {
				$selected='';
			}
			$r.= "<option value='".$app->notices_custom_list_value."' $selected>".htmlentities($app->notices_custom_list_lib,ENT_QUOTES,$charset)."</option>";
			$chu_tdp_js.="chu_tdp_code[$incr] = \"".$app->notices_custom_list_value."\";\n";
			$chu_tdp_js.="chu_tdp_libelle[$incr] = \"".$app->notices_custom_list_lib."\";\n";
			$incr++;
		}
	}
	$r.="</select>";
	
	$chu_js="<script type=\"text/javascript\">"/*.$chu_dom_js*/.$chu_tdp_js."
	document.search_input.chu_dom.onchange=par_chu_dom;
	par_chu_dom();
	document.search_input.chu_tdp.onchange=par_chu_tdp;
	
	function par_chu_dom(){
		//console.log('chu_dom : '+document.search_input.chu_dom.options[document.search_input.chu_dom.selectedIndex].value);
		switch(document.search_input.chu_dom.options[document.search_input.chu_dom.selectedIndex].value){
			case 'AFF'://AFFAIRE GENERALE
				var visible = new Array('PLA','COM','RAP','EVA','LET','DIR','NOI','NOT','DES','DEG','CDI');
				trouve_select(visible);
				break;
			case 'COM'://COMMUNICATION
				var visible = new Array('AFF','PAQ','PHO','VID','ART');
				trouve_select(visible);
				break;
			case 'GES'://GESTION DOCUMENTAIRE
				var visible = new Array('PRO','PRT','FIC','FOR');
				trouve_select(visible);
				break;
			case 'PRO'://PROGRAMME QUALITE
				var visible = new Array('PLA','RAP','EVA','LET','POL','NOI','COM');
				trouve_select(visible);
				break;
			case 'VEI'://VEILLE REGLEMENTAIRE
				var visible = new Array('TEX');
				trouve_select(visible);
				break;
			default :
				var visible = new Array();
				trouve_select(visible);
				break;
		}
	}
	
	function par_chu_tdp(){
		//console.log('chu_dom : '+document.search_input.chu_tdp.options[document.search_input.chu_tdp.selectedIndex].value);
		/*switch(document.search_input.chu_tdp.options[document.search_input.chu_tdp.selectedIndex].value){
			case 'COM'://COMPTE RENDU
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'EVA'://EVALUATION
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'FOR'://FORMULAIRE
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'LET'://LETTRE D'INFORMATION
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'NOI'://NOTE D'INFORMATION
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'NOT'://NOTE DE SERVICE
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'PLA'://PLAN D'ACTION
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'PRO'://PROCEDURE
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'RAP'://RAPPORT
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;
			case 'SUP'://SUPPORT DE COMMUNICATION
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;	
			case 'TEX'://TEXTE OFFICIEL
				var visible = new Array('COM','EVA','FOR','LET','NOI','NOT','PLA','PRO','RAP','SUP','TEX');
				trouve_select(visible);
				break;	
			default :
				var visible = new Array();
				trouve_select(visible);
				break;
		}*/
	}
		
	function trouve_select(visible){
		var chu_tdp=document.search_input.chu_tdp;
		var mon_select=false;
		var index_select = chu_tdp.options[chu_tdp.selectedIndex].value;
		for (var i=0; i<chu_tdp.options.length;i++){
			chu_tdp.options[i]=null;
			i--;
		}
		chu_tdp.options[0] = new Option('< Tout type de document >','0');
		var incr=1;
		for (var i=0; i<chu_tdp_code.length;i++){
			if(visible.length){
				for(var j=0; j<visible.length;j++){
					if(chu_tdp_code[i] == visible[j]){
						chu_tdp.options[incr] = new Option(chu_tdp_libelle[i],chu_tdp_code[i]);
						if(index_select == chu_tdp_code[i]){
							chu_tdp.options[incr].selected=true;
							mon_select=true;
						}
						incr++;
					}
				}
			}else{
				chu_tdp.options[incr] = new Option(chu_tdp_libelle[i],chu_tdp_code[i]);
				if(index_select == chu_tdp_code[i]){
					chu_tdp.options[incr].selected=true;
					mon_select=true;
				}
				incr++;
			}
		}
		if(mon_select == false){
			chu_tdp.options[0].selected=true;
		}
	}
	
	</script>";
	
	return $r.$chu_js;
}

function search_other_function_clause() {

	//doit retourner une requete de selection d'identifiants de notices
	global $chu_dom,$chu_tdp;
	$r='';
	if($chu_dom && $chu_tdp){
		$r.= "select distinct dom.notices_custom_origine as notice_id from notices_custom_values dom JOIN notices_custom_values tdp ON dom.notices_custom_origine=tdp.notices_custom_origine where tdp.notices_custom_champ='12' and tdp.notices_custom_small_text = '".$chu_tdp."' and dom.notices_custom_champ='11' and dom.notices_custom_small_text = '".$chu_dom."' ";
	}elseif($chu_dom) {
		$r.= "select distinct notices_custom_origine as notice_id from notices_custom_values where notices_custom_champ='11' and notices_custom_small_text = '".$chu_dom."' ";
	}elseif($chu_tdp){
		$r.= "select distinct notices_custom_origine as notice_id from notices_custom_values where notices_custom_champ='12' and notices_custom_small_text = '".$chu_tdp."' ";
	}
	return $r;
}

function search_other_function_has_values() {
	global $chu_dom,$chu_tdp;
	if ($chu_dom || $chu_tdp) return true; 
	else return false;
}

function search_other_function_get_values(){
	global $chu_dom,$chu_tdp;
	return $chu_dom."---".$chu_tdp;
}

function search_other_function_rec_history($n) {
	global $chu_dom,$chu_tdp;
	$_SESSION["chu_dom".$n]=$chu_dom;
	$_SESSION["chu_tdp".$n]=$chu_tdp;
}

function search_other_function_get_history($n) {
	global $chu_dom,$chu_tdp;
	$chu_dom=$_SESSION["chu_dom".$n];
	$chu_tdp=$_SESSION["chu_tdp".$n];
}

function search_other_function_human_query($n) {
	global $chu_dom,$chu_tdp;
	$r1=$r2="";
	
	$chu_dom=$_SESSION["chu_dom".$n];
	
	$chu_tdp=$_SESSION["chu_tdp".$n];
	
	if ($chu_dom) {
		$app="";
		$requete="select notices_custom_list_lib from notices_custom_lists where notices_custom_champ='11' and notices_custom_list_value='".$chu_dom."' limit 1 ";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$res=pmb_mysql_fetch_object($resultat);
			$app=$res->notices_custom_list_lib;
		}
		if ($app) $r1="rubrique: ".$app;
	}
	
	if ($chu_tdp) {
		$app="";
		$requete="select notices_custom_list_lib from notices_custom_lists where notices_custom_champ='12' and notices_custom_list_value='".$chu_tdp."' limit 1 ";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$res=pmb_mysql_fetch_object($resultat);
			$app=$res->notices_custom_list_lib;
		}
		if ($app) $r2="type de document: ".$app;
	}
	
	
	if($r1 && $r2){
		return $r1.", ".$r2;
	}elseif($r1){
		return $r1;
	}else{
		return $r2;
	}
	
}


function search_other_function_post_values() {
	global $chu_dom,$chu_tdp;
	return "<input type=\"hidden\" name=\"chu_dom\" value=\"$chu_dom\">\n<input type=\"hidden\" name=\"chu_tdp\" value=\"$chu_tdp\">\n";
}


?>