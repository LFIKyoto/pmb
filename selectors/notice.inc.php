<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.inc.php,v 1.29 2015-04-03 11:16:20 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=notice&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display&bt_ajouter=$bt_ajouter&callback=$callback&infield=$infield&typdoc_query=$typdoc_query";
if($niveau_biblio){ 
	$filtre_notice=" and niveau_biblio='$niveau_biblio' ";
	$base_url="./select.php?what=notice&niveau_biblio=$niveau_biblio&modele_id=$modele_id&serial_id=$serial_id&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display&bt_ajouter=$bt_ajouter&callback=$callback&infield=$infield&typdoc_query=$typdoc_query";
}
// contenu popup sélection notice
require('./selectors/templates/sel_notice.tpl.php');
include_once('./includes/isbn.inc.php');
require_once("./classes/mono_display.class.php");

//droits d'acces lecture notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("./classes/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
} 


// affichage du header
print $sel_header;

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;	
}

function show_results ($dbh, $user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;
	global $niveau_biblio,$modele_id,$serial_id;
	global $acces_j;
	global $callback;
	global $typdoc_query;
		
	if($niveau_biblio){ 
		$filtre_notice=" and niveau_biblio='$niveau_biblio' ";
	}
	
	if($typdoc_query){
		$filtre_notice_type_doc=" and typdoc='$typdoc_query' ";
	}
	
	// on récupére le nombre de lignes qui vont bien
	if($user_input=="") {
		$requete_count = "SELECT COUNT(1) FROM notices ";
		$requete_count.= $acces_j;
		$requete_count.= "where notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc ";
	} else {
		$aq=new analyse_query(stripslashes($user_input));
		if ($aq->error) {
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			exit;
		}
		$members=$aq->get_query_members("notices","index_wew","index_sew","notice_id");
		$isbn_verif=traite_code_isbn(stripslashes($user_input));
		$suite_rqt="";
		if (isISBN($isbn_verif)) {
			if (strlen($isbn_verif)==13)
				$suite_rqt="  code like '".formatISBN($isbn_verif,13)."' or code like '".addslashes($isbn_verif)."' ";
			else $suite_rqt=" code like '".formatISBN($isbn_verif,10)."' or code like '".addslashes($isbn_verif)."' ";
			$requete_count = "select count(1) from notices ";
			$requete_count.= $acces_j;
			$requete_count.= "where ( ".$suite_rqt." ) ";
			$requete_count.= "and notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc ";
		} else {
			$requete_count = "select count(1) from notices ";
			$requete_count.= $acces_j;
			$requete_count.= "where (".$members["where"]." or code like '".addslashes($isbn_verif)."' ) ";
			$requete_count.= "and notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc ";
		}	
	}
	$res = pmb_mysql_query($requete_count, $dbh);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);

	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	if($nbr_lignes) {
		// on lance la vraie requête
		if($user_input=="") {
			$requete = "SELECT notice_id, tit1, serie_name, tnvol, code FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join series on serie_id=tparent_id ";
			$requete.= "where notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc ORDER BY index_sew, code LIMIT $debut,$nb_per_page ";
		} else {
			$isbn_verif=traite_code_isbn(stripslashes($user_input));
			$suite_rqt="";
			if (isISBN($isbn_verif)) {
				if (strlen($isbn_verif)==13)
					$suite_rqt="  code like '".formatISBN($isbn_verif,13)."' or code like '".addslashes($isbn_verif)."' ";
				else $suite_rqt=" code like '".formatISBN($isbn_verif,10)."' or code like '".addslashes($isbn_verif)."' ";			
				$requete = "select notice_id, tit1, serie_name, tnvol, code from notices ";
				$requete.= $acces_j;
				$requete.= "left join series on serie_id=tparent_id ";
				$requete.= "where (  ".$suite_rqt." ) ";
				$requete.= "and notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc group by notice_id limit $debut,$nb_per_page";
			} else {
				$requete = "select notice_id, tit1, serie_name, tnvol, code, ".$members["select"]." as pert from notices ";
				$requete.= $acces_j;
				$requete.= "left join series on serie_id=tparent_id where (".$members["where"]." or (code like '".addslashes($isbn_verif)."' )) ";
				$requete.= "and notice_id!='".$no_display."' $filtre_notice $filtre_notice_type_doc group by notice_id order by pert desc, index_sew, code limit $debut,$nb_per_page";
			}	
		}

		$res = @pmb_mysql_query($requete, $dbh);
		while(($notice=pmb_mysql_fetch_object($res))) {
			if($niveau_biblio){
				$location="./catalog.php?categ=serials&sub=modele&act=copy&modele_id=$modele_id&serial_id=$serial_id&new_serial_id=$notice->notice_id";
				$display = new mono_display($notice->notice_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
				print pmb_bidi("<div class='row'>
								<div class='left'>
									<a href='#' onclick=\"copier_modele('$location')\">".$display->result."</a>
									</div>
								<div class='right'>
									".htmlentities($notice->code,ENT_QUOTES,$charset)."
									</div>
								</div>");				
			}
			
			else{			
				$display = new mono_display($notice->notice_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
				print pmb_bidi("<div class='row'>
							<div class='left'>
								<a href='#' onclick=\"set_parent('$caller', '$notice->notice_id', '".htmlentities(addslashes(strip_tags($display->result)),ENT_QUOTES,$charset)." ".($notice->code ? "($notice->code)" : "")."','$callback')\">".$display->result."</a>
							</div>
							<div class='right'>
								".htmlentities($notice->code,ENT_QUOTES,$charset)."
							</div>
						</div>");
			}									
		}
		pmb_mysql_free_result($res);
			
		// constitution des liens
		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;
	}
	print "<div class='row'>&nbsp;<hr /></div><div align='center'>";
	$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
	$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
	print $nav_bar;
	print "</div>";
}
// affichage des membres de la page
$sel_search_form = str_replace("!!deb_rech!!", stripslashes($f_user_input), $sel_search_form);

// on commence par créer le champ de sélection de document
// récupération des types de documents utilisés.
$query = "SELECT count(typdoc), typdoc ";
$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
$result = @pmb_mysql_query($query, $dbh);
$toprint_typdocfield .= "  <option value=''>$msg[tous_types_docs]</option>\n";
$doctype = new marc_list('doctype');
while (($rt = pmb_mysql_fetch_row($result))) {
	$obj[$rt[1]]=1;
	$qte[$rt[1]]=$rt[0];
}
foreach ($doctype->table as $key=>$libelle){
	if ($obj[$key]==1){
		$toprint_typdocfield .= "  <option ";
		$toprint_typdocfield .= " value='$key'";
		if ($typdoc_query == $key) $toprint_typdocfield .=" selected='selected' ";
		$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
	}
}
$sel_search_form = str_replace("!!typdocfield!!", $toprint_typdocfield, $sel_search_form);

print $sel_search_form;
print $jscript;
show_results($dbh, $user_input, $nbr_lignes, $page);
print $sel_footer;
