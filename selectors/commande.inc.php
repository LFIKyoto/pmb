<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: commande.inc.php,v 1.2 2017-10-19 14:04:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=commande&callback=".$callback."&id_cmd=".$id_cmd."&bt_ajouter=".$bt_ajouter;

if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande

require_once('./selectors/templates/sel_commande.tpl.php');
require_once($class_path."/entites.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/actes.class.php");

// affichage du header

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;	
}

if (!$id_bibli) {
	$id_bibli = $_SESSION["id_bibli"];
}
$id_bibli+=0;

switch($action){
	case 'add':
		print $sel_header_add;
		$commande_form = str_replace("!!sel_exercice!!", show_list_exercices($id_bibli), $commande_form);
		$commande_form = str_replace("!!id_bibli!!", $id_bibli, $commande_form);
		
		print $commande_form;
		break;
	case 'update':
		$acte = new actes();
		$acte->type_acte = 0; //type commande
		$acte->num_entite = $id_bibli;
		$acte->num_exercice = $id_exercice*1;
		$acte->statut = 1;
		$acte->num_fournisseur = $id_fou*1;
		$acte->numero = $num_cde;
		$acte->nom_acte = $nom_acte;
		$acte->save();
		
		// no break;
	default:
		print $sel_header;
		// affichage des membres de la page
		$sel_search_form = str_replace("!!deb_rech!!", stripslashes($f_user_input), $sel_search_form);
		
		if($bt_ajouter == "no"){
			$bouton_ajouter="";
		}else{
			$bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value='".$msg['acquisition_ajout_cde']."' />";
		}
		
		//Creation selecteur etablissement
		//Recherche des etablissements auxquels a acces l'utilisateur
		$q = entites::list_biblio(SESSuserid);
		$list_bib = pmb_mysql_query($q,$dbh);
		$nb_bib=pmb_mysql_num_rows($list_bib);
		
		$tab_bib=array();
		while ($row=pmb_mysql_fetch_object($list_bib)) {
			$tab_bib[0][]=$row->id_entite;
			$tab_bib[1][]=$row->raison_sociale;
		}
		
		$sel_bibli ="<select id='id_bibli' name='id_bibli' onchange=\"submit();\" >";
		
		
		
		foreach($tab_bib[0] as $k=>$v) {
			$sel_bibli.="<option value='".$v."' ";
			if($v==$id_bibli) {
				$sel_bibli.="selected='selected' ";
			}
			$sel_bibli.=">".htmlentities($tab_bib[1][$k], ENT_QUOTES, $charset)."</option>";
		}
		$sel_bibli.="</select>";
		$sel_search_form=str_replace('<!-- sel_bibli -->', $sel_bibli, $sel_search_form);
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		
		print $sel_search_form;
		print $jscript;
		
		show_results($dbh, $user_input, $nbr_lignes, $page);
		break;
}



function show_results ($dbh, $user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;	
	global $id_bibli;
	global $callback;	
	global $id_cmd;	
	
	$statut=0;
	// traitement de la saisie utilisateur
	
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;
	
	// comptage
	if(!$nbr_lignes) {
		if(!$user_input) {
			$nbr_lignes = entites::getNbActes($id_bibli, TYP_ACT_CDE, $statut);
		} else {
			$aq=new analyse_query(stripslashes($user_input),0,0,0,0);
			if ($aq->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				exit;
			}
			$nbr_lignes = entites::getNbActes($id_bibli, TYP_ACT_CDE, $statut, $aq, $user_input);	
		}
	} else {
		$aq=new analyse_query(stripslashes($user_input),0,0,0,0);
	}
	
	if ($nbr_lignes) {
		// liste
		if (!$sortBy) {
			$sortBy = '-date_acte';
		}
		if(!$user_input) {
			$res = entites::listActes($id_bibli, TYP_ACT_CDE, $statut, $debut, $nb_per_page, 0, '', $sortBy);
		} else {
			$res = entites::listActes($id_bibli, TYP_ACT_CDE, $statut, $debut, $nb_per_page, $aq, $user_input, $sortBy);
		}
		
		print "<table>
				<tr>
					<th>".htmlentities($msg['38'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_date_cde'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_date_ech'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_nom'], ENT_QUOTES, $charset)."</th>
				</tr>";
		while ($row=pmb_mysql_fetch_object($res)) {
			if ($id_cmd == $row->id_acte){
				$nbr_lignes--;
				continue;
			}
			print "
				<tr>
					<td>
						<a href='#' onclick=\"set_parent(".$row->id_acte.", '".htmlentities(addslashes($row->numero),ENT_QUOTES,$charset)."','".$callback."')\">".htmlentities($row->numero,ENT_QUOTES,$charset)."</a>
					</td>
					<td>". htmlentities($row->raison_sociale,ENT_QUOTES,$charset) ."</td>
					<td>".format_date($row->date_acte)."</td>
					<td>";
					if ($row->date_ech_calc != "00000000") { 
						print format_date($row->date_ech_calc);
					}
			print"	</td>	
					<td>".$row->nom_acte."</td>				
				</tr>";
		}
		print "</table>";	

		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;
	
		// affichage de la pagination		
		print "<div class='row'>&nbsp;<hr /></div><div class='center'>";
		$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		print $nav_bar;
		print "</div>";
	}
}

//Affiche les exercices actifs pour création commande
function show_list_exercices($id_bibli) {

	global $dbh;
	global $msg, $charset;
	global $current_module;

	$q =  entites::getCurrentExercices($id_bibli);
	$r = pmb_mysql_query($q, $dbh);
	$aff.= "<select name = 'id_exercice'>";
	while(($row=pmb_mysql_fetch_object($r))) {
		$aff .= "<option value = '". $row->id_exercice ."' >" . htmlentities($row->libelle, ENT_QUOTES, $charset) . "</option>";
	}
	$aff.="</select>";
	
	return $aff;
}



print $sel_footer;
