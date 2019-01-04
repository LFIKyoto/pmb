<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_list.inc.php,v 1.52 2018-12-14 08:24:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($form_cb)) $form_cb = '';
if(!isset($empr_location_id)) $empr_location_id = '';
if(!isset($empr_statut_edit)) $empr_statut_edit = '';
if(!isset($limite_page)) $limite_page = '';
if(!isset($page)) $page = 0;
if(!isset($numero_page)) $numero_page = '';
if(!isset($statut_action)) $statut_action = '';
if(!isset($empr_categ_filter)) $empr_categ_filter = '';
if(!isset($empr_codestat_filter)) $empr_codestat_filter = '';

include_once("$include_path/templates/empr.tpl.php");
require_once("./circ/empr/empr_func.inc.php");
require_once ("$class_path/emprunteur.class.php");

//Récupération des variables postées, on en aura besoin pour les liens
$page_url="./edit.php";

switch($dest) {
	case "TABLEAU":
		$worksheet = new spreadsheet();
		$worksheet->write_string(0,0,$titre_page);
		break;
	case "TABLEAUHTML":
		echo "<h1>".$titre_page."</h1>" ;  
		break;
	default:
		echo "<h1>".$titre_page."</h1>" ;
		break;
}

// nombre de références par pages
if ($nb_per_page_empr != "") 
	$nb_per_page = $nb_per_page_empr ;
else 
	$nb_per_page = 10;

// restriction localisation le cas échéant
$restrict_localisation = '';
if ($pmb_lecteurs_localises) {
	if ($empr_location_id=="") 
		$empr_location_id = $deflt2docs_location ;
	if ($empr_location_id!=0) 
		$restrict_localisation = " AND empr_location='$empr_location_id' ";
	else 
		$restrict_localisation = "";
}

// filtré par un statut sélectionné
$restrict_statut="";
if ($empr_statut_edit) {
	if ($empr_statut_edit!=0) 
		$restrict_statut = " AND empr_statut='$empr_statut_edit' ";
} 
$restrict_categ = '';
if($empr_categ_filter) {
	$restrict_categ = " AND empr_categ= '".$empr_categ_filter."' ";
}
$restrict_codestat = '';
if($empr_codestat_filter) {
	$restrict_codestat = " AND empr_codestat= '".$empr_codestat_filter."' ";
}
// on récupére le nombre de lignes 
if(!isset($nbr_lignes)) {
	$requete = "SELECT COUNT(1) FROM empr, empr_statut, empr_categ where empr.empr_categ=empr_categ.id_categ_empr"
			.$restrict_categ.$restrict_codestat.$restrict_localisation.$restrict_statut." and ".$restrict." and empr_statut=idstatut";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);
}

//Si aucune limite_page n'a été passée, valeur par défaut $nb_per_page
if (!$limite_page) 
	$limite_page = $nb_per_page;
else 
	$nb_per_page = $limite_page;

$nbpages= $nbr_lignes / $limite_page;
 
if(!$page) $page=1;

$debut =($page-1)*$nb_per_page;

if($nbr_lignes) {
	if ($statut_action=="modify") {
		$requete="UPDATE empr, empr_categ set empr_statut='$empr_chang_statut_edit' where empr.empr_categ=empr_categ.id_categ_empr ".$restrict_localisation.$restrict_statut.$restrict_categ.$restrict_codestat." and ".$restrict;
		$restrict_statut = " AND empr_statut='$empr_chang_statut_edit' ";
		@pmb_mysql_query($requete);
	} 
	// on lance la vraie requête
	$requete = "SELECT empr.*, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration, statut_libelle, empr_categ.libelle as categ_libelle  FROM empr, empr_statut, empr_categ ";
	$restrict_empr = " WHERE empr.empr_categ=empr_categ.id_categ_empr ".$restrict_categ.$restrict_codestat;
	$restrict_requete = $restrict_empr.$restrict_localisation.$restrict_statut." and ".$restrict;
	$requete .= $restrict_requete;
	$requete .= " and empr_statut=idstatut ";
	if (!isset($sortby))
		$sortby = 'empr_nom';

	$requete .= " ORDER BY $sortby ";
	
	switch($dest) {
		case "TABLEAU":
			$res = @pmb_mysql_query($requete, $dbh);
			$nbr_champs = @pmb_mysql_num_fields($res);

			for($n=0; $n < $nbr_champs; $n++) {
				$worksheet->write_string(2,$n,pmb_mysql_field_name($res,$n));
			}
			for($i=0; $i < $nbr_lignes; $i++) {
				$row = pmb_mysql_fetch_row($res);
				$j=0;
				foreach($row as $dummykey=>$col) {
					if(!$col) $col=" ";
					$worksheet->write_string(($i+3),$j,$col);
					$j++;
				}
			}
			$worksheet->download('Liste.xls');
			break;
		case "TABLEAUHTML":
			$res = @pmb_mysql_query($requete, $dbh);
			$empr_list = "<table>" ;
			$empr_list .="<tr>
			 	<th>$msg[code_barre_empr]</th>
				<th>$msg[nom_prenom_empr]</th>
			 	<th>$msg[adresse_empr]</th>
			 	<th></th>
			 	<th>$msg[ville_empr]</th>
			 	<th>$msg[year_empr]</th>
			 	<th>$msg[readerlist_dateexpiration]</th>
			 	<th>$msg[statut_empr]</th>
				</tr>";
			while(($empr=pmb_mysql_fetch_object($res))) {
				$empr_list .= "<tr>";
				$empr_list .= "	<td>
							<strong>$empr->empr_cb</strong>
							</td>
						<td>
							$empr->empr_nom&nbsp;$empr->empr_prenom
							</td>
						<td>$empr->empr_adr1</td>
						<td>$empr->empr_adr2</td>
						<td>$empr->empr_ville</td>
						<td>$empr->empr_year</td>";
				$empr_list .= "<td>".$empr->aff_empr_date_expiration."</td>";
				$empr_list .= "<td>".$empr->statut_libelle."</td>";
				$empr_list .= "</tr>";
			}
			$empr_list .= "</table>" ;
			echo $empr_list ;
			break;
		default:
			$requete .= "LIMIT $debut,$nb_per_page ";
			$res = @pmb_mysql_query($requete, $dbh);
	
			$parity=1;
			$empr_list ="<tr>
			 	<th>$msg[code_barre_empr]</th>
				<th>$msg[nom_prenom_empr]</th>
			 	<th>$msg[adresse_empr]</th>
			 	<th>$msg[ville_empr]</th>
			 	<th>$msg[year_empr]</th>
			 	<th>$msg[readerlist_dateexpiration]</th>
			 	<th>$msg[statut_empr]</th>";
			switch ($sub) {
				case "encours" :
					$empr_list .="<th></th>";
					break;
				case "categ_change" :
					$empr_list .="<th>$msg[categ_empr]</th>
						<th>$msg[empr_categ_change_prochain]</th>";
					break;
				default :
					$empr_list .="<th colspan=2></th>";
					break;
			}
			$empr_list .="</tr>";
			while(($empr=pmb_mysql_fetch_object($res))) {
				if ($parity % 2) 
					$pair_impair = "even";
				else 
					$pair_impair = "odd";
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
				$script="onclick=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($empr->empr_cb)."';\"";
				$empr_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
				$empr_list .= "	<td $script>
							<strong>$empr->empr_cb</strong>
							</td>
						<td $script>
							$empr->empr_nom&nbsp;$empr->empr_prenom
							</td>
						<td $script>$empr->empr_adr1</td>
						<td $script>$empr->empr_ville</td>
						<td $script>$empr->empr_year</td>";
				$empr_list .= "<td $script>".$empr->aff_empr_date_expiration."</td>";
				$empr_list .= "<td $script>".$empr->statut_libelle."</td>";
				switch ($sub) {
					case "encours" :
						$empr_list .= "<td>"."</td>";
						break;
					case "categ_change" :
						$empr_list.="<td $script>".$empr->categ_libelle."</td>";
						$empr_list.="<td>";
						$today = getdate();
						$age_lecteur = $today["year"] - $empr->empr_year;
						// on construit le select catégorie
						$query = "SELECT id_categ_empr, libelle FROM empr_categ WHERE (".$age_lecteur." >= age_min or age_min=0)  and (".$age_lecteur." <= age_max or age_max=0) ORDER BY age_min ";
						$result = pmb_mysql_query($query, $dbh);
						$nbr_rows = pmb_mysql_num_rows($result);
						$empr_list.= "<input type='hidden' value='".$empr->id_empr."' name='empr[]' />";
						$empr_list .= "<select id='empr_chang_categ_edit_".$empr->id_empr."' name='empr_chang_categ_edit_".$empr->id_empr."' class='saisie-20em'>";
						$empr_list .="<option value='0' selected='selected' >".$msg["change_categ_do_nothing"]."</option>";
						for($i=0; $i < $nbr_rows; $i++) {
							$row = pmb_mysql_fetch_row($result);
							$empr_list.= "<option value='$row[0]'";
							if($i == 0) $empr_list .= " selected='selected'";
							$empr_list .= ">$row[1]</option>";
						}
						$empr_list.="</select></td>";
						break;
					default :
						$empr_list.="<td>";
						$action_relance_courrier = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_relance_adhesion&id_empr=".$empr->id_empr."', 'lettre'); return(false) \"";
						$empr_list .= "<a href=\"#\" ".$action_relance_courrier."><img src=\"./images/new.gif\" title=\"".$msg["param_pdflettreadhesion"]."\" alt=\"".$msg["param_pdflettreadhesion"]."\" border=\"0\"></a>";
						if ($empr->empr_mail) {
							$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_relance_adhesion&id_empr=".$empr->id_empr."', 'mail');} return(false) \"";
							$empr_list .= "&nbsp;<a href=\"#\" ".$mail_click."><img src=\"./images/mail.png\" title=\"".$msg["param_mailrelanceadhesion"]."\" alt=\"".$msg["param_mailrelanceadhesion"]."\" border=\"0\"></a>";
						}
						$empr_list.="</td>";
						$empr_list .= "<td>"."</td>";
						break;
				}
				$empr_list .= "</tr>";
				$parity += 1;
			}
			pmb_mysql_free_result($res);

			// constitution des liens
			$nbepages = ceil($nbr_lignes/$nb_per_page);
			$suivante = $page+1;
			$precedente = $page-1;
			// affichage du lien précédent si nécéssaire
			$nav_bar = '';
			if($precedente > 0)
				$nav_bar .= "<a href='".$base_path."/edit.php?categ=empr&sub=$sub&page=$precedente&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&limite_page=$limite_page&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&sortby=$sortby'><img src='./images/left.gif' style='border:0px' title='$msg[48]' alt='[$msg[48]]' hspace='3' class='align_middle'></a>";
			for($i = 1; $i <= $nbepages; $i++) {
				if($i==$page) 
					$nav_bar .= "<strong>page $i/$nbepages</strong>";
			}
			if($suivante<=$nbepages) 
				$nav_bar .= "<a href='".$base_path."/edit.php?categ=empr&sub=$sub&page=$suivante&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&limite_page=$limite_page&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&sortby=$sortby'><img src='./images/right.gif' style='border:0px' title='$msg[49]' alt='[$msg[49]]' hspace='3' class='align_middle'></a>";

			// affichage du résultat
			echo "
				<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page' method=post>
			 	<div class='row'>
				 	<div class='left'>
						$nav_bar  <input type=text name=limite_page value='$limite_page' class='saisie-5em'> $msg[1905] &nbsp;
					</div>
					<div class='right'>
						<img  src='./images/tableur.gif' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."'/>&nbsp;&nbsp;
						<img  src='./images/tableur_html.gif' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."'/>&nbsp;&nbsp;
					</div>
				</div>
				<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function start_export(type){
						document.forms['form-$current_module-list'].dest.value = type;
						document.forms['form-$current_module-list'].submit();
					}	
				</script>
			";
					
			if ($pmb_lecteurs_localises) echo docs_location::gen_combo_box_empr($empr_location_id);
			echo gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$empr_statut_edit,-1,"",0,$msg["all_statuts_empr"]);
			echo "&nbsp;".emprunteur::gen_combo_box_codestat($empr_codestat_filter);
			echo "&nbsp;".emprunteur::gen_combo_box_categ($empr_categ_filter);
				
			$sort_params = array('empr_nom' => $msg['readerlist_name'], 'empr_cb' => $msg['readerlist_code'], 'empr_ville' => $msg['readerlist_ville'], 'empr_date_expiration' => $msg['readerlist_dateexpiration']);
			echo "&nbsp;".$msg["sort_by"].":&nbsp;";
			echo '<select name="sortby">';
				foreach($sort_params as $id => $caption) {
					echo '<option '.($id == $sortby ? 'selected' : '').' value="'.$id.'">'.$caption.'</option>';
				}
			echo '</select>';
			echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
			
			if ($empr_show_caddie) $bt_add_panier="&nbsp;&nbsp;<input type='button' class='bouton_small' value='".$msg["add_empr_cart"]."' onClick=\"openPopUp('./cart.php?object_type=EMPR&action=add_empr_$sub&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter', 'cart'); return false;\">";
			else $bt_add_panier="";
			echo "
				<div class='row'></div></form><br />";
			print "<script type='text/javascript' src='$base_path/javascript/sorttable.js'></script>";
			
			switch ($sub) {
				case "categ_change" :
					print pmb_bidi("<form class='form-$current_module' id='form-$current_module-empr' name='form-$current_module-empr' action='".$base_path."/edit.php?categ=empr&sub=$sub&categ_action=change_categ_empr' method='post'><table  class='sortable' width='100%'>".$empr_list."</table></form>");
					break;
				default :
					print pmb_bidi("<table  class='sortable' width='100%'>".$empr_list."</table>");
					break;
			}
			
			$nav_bar = aff_pagination ($base_path."/edit.php?categ=empr&sub=$sub&form_cb=".rawurlencode($form_cb)."&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&sortby=$sortby", $nbr_lignes, $nb_per_page, $page, 10, false, true);
			print $nav_bar;
			
			echo "
				<br /><form class='form-$current_module' id='form-$current_module-action' name='form-$current_module-action' action='".$base_path."/edit.php?categ=empr&sub=$sub&page=$precedente&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&limite_page=$limite_page&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&statut_action=modify' method='post'>
				<div class='left'>";
			if ($sub=="categ_change") echo "<input type='button' class='bouton_small' value='".htmlentities($msg["save_change_categ"],ENT_QUOTES,$charset)."' onClick=\"if (confirm('".addslashes($msg["empr_categ_confirm_change"])."')) { document.forms['form-$current_module-empr'].submit(); }\" >&nbsp;";
			if ($sub=="limite" || $sub=="depasse") echo "<input type='button' class='bouton_small' value='".htmlentities($msg["print_all_relances"],ENT_QUOTES,$charset)."' onclick='document.location=\"./edit.php?categ=$categ&sub=$sub&action=print_all&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter\"'>&nbsp;";
			
			echo "$bt_add_panier
				</div>
				<div class='align_right'>
					".$msg["empr_chang_statut"]."&nbsp;
					".gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_chang_statut_edit","","",0,"",0,"")."  
					&nbsp;<input type='submit' class='bouton_small' value='".$msg['empr_chang_statut_button']."' />
				</div>
				</form>"; 
			break;
	} //switch($dest)

} else {
	// la requête n'a produit aucun résultat
	switch($dest) {
		case "TABLEAU":
			break;
		case "TABLEAUHTML":
			break;
		default:
			echo "
				<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page' method=post>
			 	<div class='left'>
					<input type=text name=limite_page value='$limite_page' class='saisie-5em'> $msg[1905] &nbsp;";
				
				if ($pmb_lecteurs_localises) echo docs_location::gen_combo_box_empr($empr_location_id);
				echo gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$empr_statut_edit,-1,"",0,$msg["all_statuts_empr"]);
				echo "&nbsp;".emprunteur::gen_combo_box_codestat($empr_codestat_filter);
				echo "&nbsp;".emprunteur::gen_combo_box_categ($empr_categ_filter);
				
				$sort_params = array('empr_nom' => $msg['readerlist_name'], 'empr_cb' => $msg['readerlist_code'], 'empr_ville' => $msg['readerlist_ville'], 'empr_date_expiration' => $msg['readerlist_dateexpiration']);
				echo "&nbsp;".$msg["sort_by"].":&nbsp;";
				echo '<select name="sortby">';
				foreach($sort_params as $id => $caption) {
					echo '<option '.($id == $sortby ? 'selected' : '').' value="'.$id.'">'.$caption.'</option>';
				}
				echo '</select>';
				echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
				
				
				echo "</div>
				<div class='right'>
					<img  src='./images/tableur.gif' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."'/>&nbsp;&nbsp;
					<img  src='./images/tableur_html.gif' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."'/>&nbsp;&nbsp;
				</div>
				<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function start_export(type){
						document.forms['form-$current_module-list'].dest.value = type;
						document.forms['form-$current_module-list'].submit();
					}	
				</script>
			";
					
			echo "
			<div class='row'>&nbsp;</div></form>";
			error_message($msg[46], str_replace('!!form_cb!!', $form_cb, $msg['edit_lect_aucun_trouve']), 1, './edit.php?categ=empr&sub='.$sub);
	}
}
