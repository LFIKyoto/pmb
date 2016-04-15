<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.3 2015-04-03 11:16:28 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/* Paramètres 
 * 
 * $caller = 		nom du formulaire appelant
 * param1 = 		nom du champ contenant l'identifiant de l'indexation décimale dans le formulaire appelant
 * param2 = 		nom du champ contenant le libellé de l'indexation décimale dans le formulaire appelant
 * id_pclass = 		identifiant du plan de classement demandé
 * typdoc = 		type de document
 * deb_rech =		debut de la recherche (contenu du champ appelant)
 * f_user_input = 	champ de recherche
 *  
*/  

$id_pclass+=0;

$base_url = "./select.php?what=indexint&caller=$caller&param1=$param1&param2=$param2&typdoc=$typdoc&f_user_input=$f_user_input";

if ($thesaurus_classement_mode_pmb==1) {
	
	$q = "select id_pclass,name_pclass from pclassement where typedoc like '%$typdoc%' order by name_pclass";
	$r = pmb_mysql_query($q, $dbh);
	$n = pmb_mysql_num_rows($r);
	
	if ($n==0) {			//pas de plan de classement avec le type de document défini
		
		print "<script type='text/javascript' >window.close();</script>";
		
	} else if ($n==1) {		//1 seul plan de classement
	
		$id_pclass = pmb_mysql_result($r,0,0);
		$tpl_pclass = "<input type='hidden' name='id_pclass' id='id_pclass' value='".$id_pclass."' />";
		  
	} else {				//plusieurs plans de classements
		
		$tpl_pclass = "<select id='id_pclass' name='id_pclass' ";
		$tpl_pclass.= "onchange = \"document.location = '".$base_url."&id_pclass='+document.getElementById('id_pclass').value;\" >" ;
		while ($row = pmb_mysql_fetch_object($r)) {
			$tpl_pclass.= "<option value='$row->id_pclass' ";
			if ($id_pclass==$row->id_pclass) {
				$tpl_pclass.="selected='selected' ";
			}
			$tpl_pclass.= ">".htmlentities($row->name_pclass,ENT_QUOTES,$charset)."</option>";
		}
		$tpl_pclass.= "</select>";
	}
		
} else {
	
		$id_pclass=$thesaurus_classement_defaut;
		$tpl_pclass = "<input type='hidden' name='id_pclass' id='id_pclass' value='".$id_pclass."' />";
}


$base_url.= "&id_pclass=$id_pclass";

// contenu popup sélection plan de classement
require('./selectors/templates/sel_indexint.tpl.php');
$sel_search_form = str_replace('!!pclassement!!', $tpl_pclass, $sel_search_form);	

// affichage du header
print $sel_header;

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=='' && $user_input=='') {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;
}

// affichage des membres de la page
$sel_search_form = str_replace('!!deb_rech!!', htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);

if ((string)$exact=='') $exact=1;
if ($exact) {
	$sel_search_form = str_replace('!!check1!!', "checked='checked' ", $sel_search_form);
	$sel_search_form = str_replace('!!check0!!', '', $sel_search_form);
} else {
	$sel_search_form = str_replace('!!check1!!', '', $sel_search_form);
	$sel_search_form = str_replace('!!check0!!', "checked='checked' ", $sel_search_form);
}
print $sel_search_form;
print $jscript;
show_results($dbh, $user_input, $nbr_lignes, $page);


function show_results($dbh, $user_input, $nbr_lignes=0, $page=0, $id=0) {
	global $dbh,$msg,$charset;
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $no_display;
	global $exact;
	global$thesaurus_classement_mode_pmb;
	global $id_pclass,$typdoc;

	
	$pclass_and_req=" num_pclass='$id_pclass' and id_pclass = num_pclass ";
	
	// on récupère le nombre de lignes qui vont bien
	if (!$id) {
		if($user_input=='') {
			$requete = "SELECT COUNT(1) FROM indexint,pclassement where $pclass_and_req ";
		} else {
			if (!$exact) {
				$aq=new analyse_query(stripslashes($user_input));
				if ($aq->error) {
					print '<br /><div class="row">'.htmlentities($msg['no_result'],ENT_QUOTES,$charset).'</div>';
					exit;
				}
				$requete=$aq->get_query_count("indexint, pclassement","concat(indexint_name,' ',indexint_comment)","index_indexint","indexint_id","$pclass_and_req");
			} else {
				$requete="select count(distinct indexint_id) from indexint,pclassement where indexint_name like '".str_replace("*","%",$user_input)."' and $pclass_and_req";
			}
		}
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	} else $nbr_lignes=1;
	
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;
	if($nbr_lignes) {
		// on lance la vraie requête
		if (!$id) {
			if($user_input=="") {
				$requete = "SELECT * FROM indexint,pclassement where $pclass_and_req ";
				$requete .= "ORDER BY indexint_name LIMIT $debut,$nb_per_page ";
			} else {
				if (!$exact) {
					$members=$aq->get_query_members("indexint","concat(indexint_name,' ',indexint_comment)","index_indexint","indexint_id");
					$requete="select *,".$members["select"]." as pert from indexint,pclassement where ".$members["where"]." and $pclass_and_req group by indexint_id order by pert desc, index_indexint limit $debut,$nb_per_page";
				} else {
					$requete="select * from indexint,pclassement where indexint_name like '".str_replace("*","%",$user_input)."' and $pclass_and_req group by indexint_id order by indexint_name limit $debut,$nb_per_page";
				}
			}
		} else {
			$requete="select * from indexint,pclassement where indexint_id='".$id."' $pclass_and_req";	
		}
		$res = @pmb_mysql_query($requete, $dbh);
		while(($indexint=pmb_mysql_fetch_object($res))) {
			if ($indexint->indexint_comment) {
				$entry = $indexint->indexint_name." : ".$indexint->indexint_comment;	
				$entry_ret = $indexint->indexint_name." ".$indexint->indexint_comment;
			} else {
				$entry = $indexint->indexint_name ;
				$entry_ret = $entry;
			}
			print "<a href='#' onclick=\"set_parent('$caller', '$indexint->indexint_id', '".htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",$entry_ret))),ENT_QUOTES,$charset)."')\">
				$entry</a>";
			print "<br />";
		}
		pmb_mysql_free_result($res);

		// constitution des liens
		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;

		// affichage pagination
		print '<hr /><div align="center">';
		if($precedente > 0) {
			print "<a href='$base_url&page=$precedente&nbr_lignes=$nbr_lignes".$pclass_url."&user_input=".rawurlencode(stripslashes($user_input))."&exact=$exact'><img src='./images/left.gif' border='0' title='$msg[48]' alt='[$msg[48]]' hspace='3' align='middle' /></a>";
		}
		for($i = 1; $i <= $nbepages; $i++) {
			if($i==$page) print "<b>$i/$nbepages</b>";
		}

		if($suivante<=$nbepages) {
			print "<a href='$base_url&page=$suivante&nbr_lignes=$nbr_lignes".$pclass_url."&user_input=".rawurlencode(stripslashes($user_input))."&exact=$exact'><img src='./images/right.gif' border='0' title='$msg[49]' alt='[$msg[49]]' hspace='3' align='middle' /></a>";
		}
		print '</div>';
			
	}
}

print $sel_footer;