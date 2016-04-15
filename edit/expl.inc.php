<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.inc.php,v 1.46.2.2 2015-11-12 10:06:16 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$class_path/emprunteur.class.php");

//Récupération des variables postées, on en aura besoin pour les liens
$url_page="./edit.php";

switch($dest) {
	case "TABLEAU":
		$fname = tempnam("./temp", "$fichier_temp_nom.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		$worksheet->write(0,0,$titre_page);
		break;
	case "TABLEAUHTML":
		echo "<h1>".$titre_page."</h1>" ;  
		break;
	default:
		echo "<h1>".$titre_page."</h1>" ;
		break;
}

// Pour localiser les éditions : $deflt2docs_location, $pmb_lecteurs_localises, $empr_location_id ;

// Calcul du nombre de pages à afficher 
$sql = "SELECT count(1) ";
$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql.= "LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql.= "LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql.= "docs_type , pret, empr ";
$sql.= "WHERE ";
if ($pmb_lecteurs_localises) {
	if ($empr_location_id=="") 
		$empr_location_id = $deflt2docs_location ;
	if ($empr_location_id!=0) 
		$sql.= "empr_location='$empr_location_id' AND "; 
	if ($docs_location_id=="")
		$docs_location_id = 0 ;
	if ($docs_location_id!=0)
		$sql.= "expl_location='$docs_location_id' AND ";
}
$sql.= "expl_typdoc = idtyp_doc and pret_idexpl = expl_id and empr.id_empr = pret.pret_idempr ";
$sql.= $critere_requete;

$req_nombre_lignes_pret = pmb_mysql_query($sql, $dbh);

$nombre_lignes_pret = pmb_mysql_result($req_nombre_lignes_pret, 0, 0);

//Si aucune limite_page n'a été passée, valeur par défaut : 10
if ($nb_per_page=="") {
	$nb_per_page = 10;
}

if(!$page) $page=1;
$limite_mysql =($page-1)*$nb_per_page;

// Comptage retard/en cours
$sql_count = "SELECT IF(pret_retour>=CURDATE(),'ENCOURS','RETARDS') as retard, count(pret_idexpl) as combien ";
$sql_count.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql_count.= "LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql_count.= "LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql_count.= "pret, empr ";
$sql_count.= "WHERE ";
if ($pmb_lecteurs_localises) {
	if ($empr_location_id!=0) 
		$sql_count.= "empr_location='$empr_location_id' AND "; 
	if ($docs_location_id!=0) 
		$sql_count.= "expl_location='$docs_location_id' AND "; 
}
$sql_count.= "pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr ";
$sql_count.=(($pmb_short_loan_management==1 && strpos($sub,'short_loans')!==false)?"and short_loan_flag='1' ":' ');
$sql_count.= "group by retard ";
$req_count = pmb_mysql_query($sql_count) or die("Erreur SQL !<br />".$sql_count."<br />".pmb_mysql_error()); 
while ($data_count = pmb_mysql_fetch_object($req_count)) { 
	$nbtotal_prets[$data_count->retard]=$data_count->combien;
}
// construction du message ## prêts en retard sur un total de ##
$msg['n_retards_sur_total_de'] = str_replace ("!!nb_retards!!",$nbtotal_prets['RETARDS']*1,$msg['n_retards_sur_total_de']);
$msg['n_retards_sur_total_de'] = str_replace ("!!nb_total!!",($nbtotal_prets['RETARDS']+$nbtotal_prets[ENCOURS])*1,$msg['n_retards_sur_total_de']);

//REINITIALISATION DE LA REQUETE SQL
$sql = "SELECT date_format(pret_date, '".$msg['format_date']."') as aff_pret_date, ";
$sql .= "date_format(pret_retour, '".$msg['format_date']."') as aff_pret_retour, ";
$sql .= "IF(pret_retour>=CURDATE(),0,1) as retard, ";
$sql .= "id_empr, empr_nom, empr_prenom, empr_mail, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, tdoc_libelle, ";
$sql .= "short_loan_flag ";
$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql .= "LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql .= "LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql .= "docs_type, pret, empr ";
$sql .= "WHERE ";
if ($pmb_lecteurs_localises) {
	if ($empr_location_id!=0) 
		$sql.= "empr_location='$empr_location_id' AND "; 
	if ($docs_location_id!=0) 
		$sql.= "expl_location='$docs_location_id' AND "; 
}
$sql.= "expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr ";
$sql.= $critere_requete;

if ($nombre_lignes_pret > 0) {
	switch($dest) {
		case "TABLEAU":
			$res = @pmb_mysql_query($sql, $dbh);
			$nbr_champs = @pmb_mysql_num_fields($res);
			for($n=0; $n < $nbr_champs; $n++) {
				$worksheet->write(2,$n,pmb_mysql_field_name($res,$n));
			}
			for($i=0; $i < $nombre_lignes_pret; $i++) {
				$row = pmb_mysql_fetch_row($res);
				$j=0;
				foreach($row as $dummykey=>$col) {
					if(!$col) $col=" ";
					$worksheet->write(($i+3),$j,$col);
					$j++;
				}
			}
			$workbook->close();
			$fh=fopen($fname, "rb");
			fpassthru($fh);
			unlink($fname);
			break;
		case "TABLEAUHTML":
			$res = @pmb_mysql_query($sql, $dbh);
			$expl_list .= "<table>" ;
			$expl_list .= "<tr>
			 	<th width='10%'>$msg[4014]</th>
				<th width='10%'>$msg[4016]</th>
				<th width='15%'>$msg[294]</th>
			 	<th width='15%'>$msg[233]</th>
			 	<th width='15%'>$msg[234]</th>
			 	<th width='15%'>$msg[empr_nom_prenom]</th>
			 	<th width='10%'>$msg[circ_date_emprunt]</th>
			 	<th width='10%'>$msg[circ_date_retour]</th>
				</tr>";
			while(($data=pmb_mysql_fetch_array($res))) {
				$header_aut = "";
				$responsabilites = get_notice_authors($data['idnot']) ;
				$as = array_search ("0", $responsabilites["responsabilites"]) ;
				if ($as!== FALSE && $as!== NULL) {
					$auteur_0 = $responsabilites["auteurs"][$as] ;
					$auteur = new auteur($auteur_0["id"]);
					$header_aut .= $auteur->isbd_entry;
				} else {
					$aut1_libelle=array();
					$as = array_keys ($responsabilites["responsabilites"], "1" ) ;
					for ($i = 0 ; $i < count($as) ; $i++) {
						$indice = $as[$i] ;
						$auteur_1 = $responsabilites["auteurs"][$indice] ;
						$auteur = new auteur($auteur_1["id"]);
						$aut1_libelle[]= $auteur->isbd_entry;
					}
					
					$header_aut .= implode (", ",$aut1_libelle) ;
				}
	
				$header_aut ? $auteur=$header_aut : $auteur="";
				
				$expl_list .= "<tr>";
				$expl_list .= "	<td><strong>".$data["empr_cb"]."</strong></td>
						<td>".$data["expl_cote"]."</td>
						<td>".$data["tdoc_libelle"]."</td>
						<td>".$data["tit"]."</td>
						<td>".$auteur."</td>
						<td>".$data['empr_nom'].", ".$data["empr_prenom"]."</td>
						<td>".$data["aff_pret_date"]."</td>
						<td>".$data['aff_pret_retour']."</td>
					</tr>";
			}
			$expl_list .= "</table>" ;
			echo $expl_list ;
			break;
		default:
						
			echo $msg['n_retards_sur_total_de'];
			
			jscript_checkbox() ;
			// formulaire de restriction
			echo "
				<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$url_page?categ=$categ&sub=$sub&nb_per_page=$nb_per_page&page=$page' method='post'>
			 	<div class='left'>
					$msg[circ_afficher]
			 		<input type=text name=nb_per_page size=2 value=$nb_per_page class='petit' /> $msg[1905] ";
			if ($pmb_lecteurs_localises){
				echo " / ".$msg['editions_filter_empr_location']." : ".docs_location::gen_combo_box_empr($empr_location_id);
				echo $msg['editions_filter_docs_location']." : ".docs_location::gen_combo_box_docs($docs_location_id);
			}
			echo '</div>';
			echo "
				<input type='button' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value=''; this.form.submit();\">&nbsp;&nbsp;<input type='hidden' name='dest' value='' />
				<div class='right'>
					<img  src='./images/tableur.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export tableau EXCEL'/>&nbsp;&nbsp;
					<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
				</div>
				</form>
				<br />";
			echo "<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function start_export(type){
						document.forms['form-$current_module-list'].dest.value = type;
						document.forms['form-$current_module-list'].submit();
						
					}	
				</script>";
			$sql = $sql." LIMIT ".$limite_mysql.", ".$nb_per_page;
			$res = @pmb_mysql_query($sql, $dbh);
	
			$parity=1;
			$expl_list .= "<tr>
			 	<th>$msg[4014]</th>
				<th>$msg[4016]</th>
				<th>$msg[294]</th>
			 	<th>$msg[233]</th>
			 	<th>$msg[234]</th>
			 	<th>".$msg['empr_nom_prenom']."</th>
			 	<th>".$msg['circ_date_emprunt']."</th>
			 	<th>".$msg['circ_date_retour']."</th>
			 	<th colspan=2>$msg[369]</th>
				</tr>";
			
			$odd_even=0;
			while(($data=pmb_mysql_fetch_array($res))) {
				$empr_nom = $data['empr_nom'];
				$empr_prenom = $data['empr_prenom'];
				$empr_mail = $data['empr_mail'];
				$id_empr = $data['id_empr']; 
				$empr_cb = $data['empr_cb'];
				$aff_pret_date = $data['aff_pret_date'];
				$aff_pret_retour = $data['aff_pret_retour'];  
				$retard = $data['retard'];
				$cote_expl = $data['expl_cote'];  
				$id_expl =$data['expl_cb'];
				$titre = $data['tit'];
				$support = $data['tdoc_libelle'];
				$id_empr=$data['id_empr'];
				$short_loan_flag=$data['short_loan_flag'];
	
				$header_aut = "";
				$responsabilites = get_notice_authors($data['idnot']) ;
				$as = array_search ("0", $responsabilites["responsabilites"]) ;
				if ($as!== FALSE && $as!== NULL) {
					$auteur_0 = $responsabilites["auteurs"][$as] ;
					$auteur = new auteur($auteur_0["id"]);
					$header_aut .= $auteur->isbd_entry;
				} else {
					$aut1_libelle=array();
					$as = array_keys ($responsabilites["responsabilites"], "1" ) ;
					for ($i = 0 ; $i < count($as) ; $i++) {
						$indice = $as[$i] ;
						$auteur_1 = $responsabilites["auteurs"][$indice] ;
						$auteur = new auteur($auteur_1["id"]);
						$aut1_libelle[]= $auteur->isbd_entry;
					}
					
					$header_aut .= implode (", ",$aut1_libelle) ;
				}
	
				$header_aut ? $auteur=$header_aut : $auteur="";
				if($retard || ($sub=='encours') || (strpos($sub,'short_loans')!==false)) {	
					// on affiche les résultats
					if ($retard) $tit_color="color='RED'";				
					else $tit_color="";				
				
					if ($odd_even==0) {
						$pair_impair = "odd";
						$odd_even=1;
					} elseif ($odd_even==1) {
						$pair_impair = "even";
						$odd_even=0;
					}
		
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"";			
					$expl_list .= "<tr class='$pair_impair' $tr_javascript>";		
				
					if (SESSrights & CIRCULATION_AUTH) { 
						$expl_list .= "<td><a href=\"./circ.php?categ=visu_ex&form_cb_expl=".$id_expl."\">".$id_expl."</a></td>";
					} else {
						$expl_list .= "<td>".$id_expl."</td>";
					}
					$expl_list .= "<td>".$cote_expl."</td>";
					$expl_list .= "<td>".$support."</td>";
					
					if (SESSrights & CATALOGAGE_AUTH) {
						if ($data['expl_notice']) {
							$expl_list .= "<td><a href='./catalog.php?categ=isbd&id=".$data['expl_notice']."'><font $tit_color><b>".$titre."</b></font></a></td>"; // notice de monographie
						} elseif ($data['expl_bulletin']) { 
							$expl_list .= "<td><a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$data['expl_bulletin']."'><font $tit_color><b>".$titre."</b></font></a></td>"; // notice de bulletin
						} else {
							$expl_list .= "<td><font $tit_color><b>".$titre."</b></font></td>";
						}
					} else {
						$expl_list .= "<td><font $tit_color><b>".$titre."</b></font></td>";
					}
					$expl_list .= "<td><font $tit_color>".$auteur."</font></td>";    
					// **************** ajout icône ajout panier
					if ($empr_show_caddie) {
						$img_ajout_empr_caddie="<img src='./images/basket_empr.gif' align='middle' alt='basket' title=\"${msg[400]}\" onClick=\"openPopUp('./cart.php?object_type=EMPR&item=".$id_empr."', 'cart', 600, 700, -2, -2,'$selector_prop_ajout_caddie_empr')\">&nbsp;";
					}
					
					$expl_list .= "<td>$img_ajout_empr_caddie<a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($empr_cb)."\">".$empr_nom.", ".$empr_prenom."</a></td>"; 
					$expl_list .= "<td>".$aff_pret_date;
					$expl_list .= (($pmb_short_loan_management && $short_loan_flag)?"&nbsp;<img src='./images/chrono.png' alt='".$msg['short_loan']."' title='".$msg['short_loan']."'/>":'');
					$expl_list .= "</td>"; 
					$expl_list .= "<td><font $tit_color><b>".$aff_pret_retour."</b></font></td>";
				
					/* test de date de retour dépassée */
					if ($retard) {			
						$imprime_click = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_retard&cb_doc=$id_expl&id_empr=$id_empr', 'lettre', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes'); return(false) \"";
						$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_retard&cb_doc=$id_expl&id_empr=$id_empr', 'mail', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');} return(false) \"";
						$expl_list .= "\n<td align='center'><a href=\"#\" ".$imprime_click."><img src=\"./images/new.gif\" title=\"".$msg["lettre_retard"]."\" alt=\"".$msg[lettre_retard]."\" border=\"0\"></a></td>\n";
						if (($empr_mail)&&($biblio_email)) {
							$expl_list .= "<td><a href=\"#\" ".$mail_click."><img src=\"./images/mail.png\" title=\"".$msg[mail_retard]."\" alt=\"".$msg[mail_retard]."\" border=\"0\"></a></td>"; 
						} else {
							$expl_list .= "<td>&nbsp;</td>";
						}
					} else {
						$expl_list .= "<td>&nbsp;</td><td>&nbsp;</td>";
					}
					$expl_list .= "</tr>\n";
				}
			}
			print "<script type='text/javascript' src='$base_path/javascript/sorttable.js'></script>";
			print pmb_bidi("<table class='sortable' width='100%'>".$expl_list."</table>");

			// formulaire d'action tout imprimer, dispo uniquement si pas de relances pultiples
			if ($pmb_gestion_amende==0 || $pmb_gestion_financiere==0) {
				$bouton_imprime_tout ="" ;
				$restrict_localisation ="";
				if ($pmb_lecteurs_localises) {
					if ($empr_location_id!="") $restrict_localisation .= "&empr_location_id=$empr_location_id" ;
					if ($docs_location_id!="") $restrict_localisation .= "&docs_location_id=$docs_location_id" ;
				}
				switch($sub) {
					case "pargroupe" :
						$bouton_imprime_tout = "<input type='button' class='bouton' value=\"".$msg['lettres_relance_groupe']."\" onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_retard_groupe', 'lettre', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes'); return(false) \" >";
						break;
					case "retard" :
					case "retard_par_date" :
						$bouton_imprime_tout = "<input type='hidden' name='act' value=''><input type='button' class='bouton' value=\"".$msg['lettres_relance']."\" onclick=\"this.form.act.value='print'; this.form.submit();\" >";
						break;
					case "owner" :
						break;
					case "encours" :
					default :
						break;
				}
				if ($bouton_imprime_tout) echo "
					<br />
					<form class='form-$current_module' action='' method='post'>
					$bouton_imprime_tout
					</form>";
			}
			$nav_bar = aff_pagination ("$url_page?categ=$categ&sub=$sub&empr_location_id=$empr_location_id&docs_location_id=$docs_location_id", $nombre_lignes_pret, $nb_per_page, $page, 10, false, true) ;
			print $nav_bar;

			//impression/emails (on est dans le cas retards/retards par date)
			if ($act=="print") {
				$not_all_mail = array();
				if ($nombre_lignes_pret) {
					$resultat=pmb_mysql_query($sql);
					while ($r=pmb_mysql_fetch_object($resultat)) {
						$mail_sended = 0;
						if ((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($r->empr_mail)) {
							// l'objet du mail
							$var = "mailretard_".$relance."objet";
							eval ("\$objet=\"".$$var."\";");
							
							// la formule de politesse du bas (le signataire)
							$var = "mailretard_".$relance."fdp";
							eval ("\$fdp=\"".$$var."\";");
							
							// le texte après la liste des ouvrages en retard
							$var = "mailretard_".$relance."after_list";
							eval ("\$after_list=\"".$$var."\";");
							
							// le texte avant la liste des ouvrges en retard
							$var = "mailretard_".$relance."before_list";
							eval ("\$before_list=\"".$$var."\";");
							
							// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
							$var = "mailretard_".$relance."madame_monsieur";
							eval ("\$madame_monsieur=\"".$$var."\";");
							
							if($madame_monsieur) $texte_mail.=$madame_monsieur."\r\n\r\n";
							if($before_list) $texte_mail.=$before_list."\r\n\r\n";
							
							//Récupération des exemplaires
							$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$r->id_empr."' and pret_retour < curdate() and pret_idexpl=expl_id order by pret_date " ;
							$req = pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error());
							
							$i=0;
							while ($data = pmb_mysql_fetch_array($req)) {
							
								/* Récupération des infos exemplaires et prêt */
								$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
								$requete.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
								$requete.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
								$requete.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ;
								$requete.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
								$requete.= "WHERE expl_cb='".addslashes($data['expl_cb'])."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
							
								$req = pmb_mysql_query($requete);
								$expl = pmb_mysql_fetch_object($req);
							
								$responsabilites=array() ;
								$header_aut = "" ;
								$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
								$as = array_search ("0", $responsabilites["responsabilites"]) ;
								if ($as!== FALSE && $as!== NULL) {
									$auteur_0 = $responsabilites["auteurs"][$as] ;
									$auteur = new auteur($auteur_0["id"]);
									$header_aut .= $auteur->isbd_entry;
								} else {
									$aut1_libelle=array();
									$as = array_keys ($responsabilites["responsabilites"], "1" ) ;
									for ($i = 0 ; $i < count($as) ; $i++) {
										$indice = $as[$i] ;
										$auteur_1 = $responsabilites["auteurs"][$indice] ;
										$auteur = new auteur($auteur_1["id"]);
										$aut1_libelle[]= $auteur->isbd_entry;
									}
									$header_aut .= implode (", ",$aut1_libelle) ;
								}
								$header_aut ? $auteur=" / ".$header_aut : $auteur="";
							
								// récupération du titre de série
								$tit_serie="";
								if ($expl->tparent_id && $expl->m_id) {
									$parent = new serie($expl->tparent_id);
									$tit_serie = $parent->name;
									if($expl->tnvol)
										$tit_serie .= ', '.$expl->tnvol;
								}
								if($tit_serie) {
									$expl->tit = $tit_serie.'. '.$expl->tit;
								}
							
								$texte_mail.=$expl->tit.$auteur."\r\n";
								$texte_mail.="    -".$msg[fpdf_date_pret]." : ".$expl->aff_pret_date." ".$msg[fpdf_retour_prevu]." : ".$expl->aff_pret_retour."\r\n";
								$texte_mail.="    -".$expl->location_libelle.": ".$expl->section_libelle." (".$expl->expl_cb.")\r\n\r\n\r\n";
								$i++;
							}
							$texte_mail.="\r\n";
							if($after_list) $texte_mail.=$after_list."\r\n\r\n";
							if($fdp) $texte_mail.=$fdp."\r\n\r\n";
							$texte_mail.=mail_bloc_adresse() ;
							
							//Si mail de rappel affecté au responsable du groupe
							$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$r->id_empr." and resp_groupe and mail_rappel limit 1";
							$req=pmb_mysql_query($requete);
							/* Récupération du nom, prénom et mail du lecteur destinataire */
							if(pmb_mysql_num_rows($req) > 0) {
								$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr='".pmb_mysql_result($req, 0,1)."'";
								$result=pmb_mysql_query($requete);
								$coords_dest=pmb_mysql_fetch_object($result);
							} else {
								$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr=".$r->id_empr;
								$result=pmb_mysql_query($requete);
								$coords_dest=pmb_mysql_fetch_object($result);
							}
								
							/* Récupération du nom, prénom et mail du lecteur concerné */
							$requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_cb from empr where id_empr=".$r->id_empr;
							$req=pmb_mysql_query($requete);
							$coords=pmb_mysql_fetch_object($req);
							
							//remplacement nom et prenom
							$texte_mail=str_replace("!!empr_name!!", $coords->empr_nom,$texte_mail);
							$texte_mail=str_replace("!!empr_first_name!!", $coords->empr_prenom,$texte_mail);
							
							$headers .= "Content-type: text/plain; charset=".$charset."\n";
							
							$mail_sended=mailpmb($coords_dest->empr_prenom." ".$coords_dest->empr_nom, $coords_dest->empr_mail, $objet." : ".$coords->empr_prenom." ".mb_strtoupper($coords->empr_nom,$charset)." (".$coords->empr_cb.")",$texte_mail, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);
						}			
						if (!$mail_sended) {
							$not_all_mail[] = $r->id_empr;
						}
					}
				}
				if (count($not_all_mail) > 0) {
					print "
					<form name='print_empr_ids' action='pdf.php?pdfdoc=lettre_retard$restrict_localisation' target='lettre' method='post'>
					";		
					for ($i=0; $i<count($not_all_mail); $i++) {
						print "<input type='hidden' name='empr_print[]' value='".$not_all_mail[$i]."'/>";
					}	
					print "	<script>openPopUp('','lettre', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes');
						document.print_empr_ids.submit();
						</script>
					</form>
					";
				}
			}
			
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
			echo $msg['n_retards_sur_total_de'];
			
			// formulaire de restriction
			echo "
				<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$url_page?categ=$categ&sub=$sub&limite_page=$nb_per_page&page=$page' method='post'>
			 	<div class='left'>
					$msg[circ_afficher]
			 		<input type=text name=limite_page size=2 value=$nb_per_page class='petit' /> $msg[1905] ";
			if ($pmb_lecteurs_localises){
				echo " / ".$msg['editions_filter_empr_location']." : ".docs_location::gen_combo_box_empr($empr_location_id);
				echo $msg['editions_filter_docs_location']." : ".docs_location::gen_combo_box_docs($docs_location_id);
			}
			echo '</div>';
			echo "
				<input type='button' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value=''; this.form.submit();\">&nbsp;&nbsp;<input type='hidden' name='dest' value='' />
				</form>
				<br />";
			error_message($msg[46], str_replace('!!form_cb!!', $form_cb, $msg['edit_lect_aucun_trouve']), 1, './edit.php?categ=empr&sub='.$sub);
	}
}