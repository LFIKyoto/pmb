<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notices.inc.php,v 1.18 2015-04-24 14:20:58 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case "resa_a_traiter" :
		switch($dest) {
			case "TABLEAU":
				$fname = tempnam("./temp", "$fichier_temp_nom.xls");
				$workbook = new writeexcel_workbook($fname);
				$worksheet = &$workbook->addworksheet();
				$worksheet->write(0,0,$msg[350].": ".$msg['edit_resa_menu_a_traiter']);

				$worksheet->write(2,0,$msg[366]);
				$worksheet->write(2,1,$msg["empr_nom_prenom"]);
				$worksheet->write(2,2,$msg["edit_resa_empr_location"]);
				$worksheet->write(2,3,$msg[233]);
				$worksheet->write(2,4,$msg["edit_resa_expl_location"]);
				$worksheet->write(2,5,$msg[295]);
				$worksheet->write(2,6,$msg[296]);
				$worksheet->write(2,7,$msg[297]);
				$worksheet->write(2,8,$msg[294]);
				$worksheet->write(2,9,$msg[232]);
				if ($pmb_transferts_actif=="1") $worksheet->write(2,10,$msg["edit_resa_loc_retrait"]);

				$tableau_resa = resa_list_resa_a_traiter () ;
				$line = 0;
				for ($j=0; $j< count($tableau_resa); $j++) {
					$tableau_expl_dispo = array();
					if ($no_notice!=$tableau_resa[$j]['resa_idnotice'] || $no_bulletin!=$tableau_resa[$j]['resa_idbulletin']) {
						$no_notice=$tableau_resa[$j]['resa_idnotice'] ;
						$no_bulletin=$tableau_resa[$j]['resa_idbulletin'] ;
						$tableau_expl_dispo = expl_dispo ($no_notice, $no_bulletin) ;
					}
					if (count($tableau_expl_dispo)) {
						for ($i=0;$i<count($tableau_expl_dispo);$i++) {
							if (!$f_dispo_loc || ($tableau_expl_dispo[$i]['idlocation'] == $f_dispo_loc)) {
								$worksheet->write(($line+3),0,$tableau_resa[$j]['rank']);
								$worksheet->write(($line+3),1,$tableau_resa[$j]['resa_empr']);
								$worksheet->write(($line+3),2,$tableau_resa[$j]['resa_empr_loc_libelle']);
								$worksheet->write(($line+3),3,$tableau_resa[$j]['resa_tit']);
								$worksheet->write(($line+3),4,$tableau_expl_dispo[$i]['location']);
								$worksheet->write(($line+3),5,$tableau_expl_dispo[$i]['section']);
								$worksheet->write(($line+3),6,$tableau_expl_dispo[$i]['expl_cote']);
								$worksheet->write(($line+3),7,$tableau_expl_dispo[$i]['statut']);
								$worksheet->write(($line+3),8,$tableau_expl_dispo[$i]['support']);
								$worksheet->write(($line+3),9,$tableau_expl_dispo[$i]['expl_cb']);
								if ($pmb_transferts_actif=="1") $worksheet->write(($line+3),10,$tableau_resa[$j]['loc_retrait_libelle']);
								$line++;
							}
						}
					}
				}
				$workbook->close();
				$fh=fopen($fname, "rb");
				fpassthru($fh);
				unlink($fname);
				break;
			case "TABLEAUHTML":
				echo "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu_a_traiter']."</h1>" ;
				$aff_final .= "<tr>
								<th>".$msg[366]."</th>
								<th>".$msg["empr_nom_prenom"]."</th>
								<th>".$msg["edit_resa_empr_location"]."</th>
								<th>".$msg[233]."</th>
								<th>".$msg["edit_resa_expl_location"]."</th>
								<th>$msg[295]</th>
								<th>$msg[296]</th>
								<th>$msg[297]</th>
								<th>$msg[294]</th>
								<th>$msg[232]</th>";
				if ($pmb_transferts_actif=="1") $aff_final .= "<th>" . $msg["edit_resa_loc_retrait"] . "</th>";
				$aff_final .="</tr>";
				$tableau_resa = resa_list_resa_a_traiter () ;
				for ($j=0; $j< count($tableau_resa); $j++) {
					$tableau_expl_dispo = array();
					if ($no_notice!=$tableau_resa[$j]['resa_idnotice'] || $no_bulletin!=$tableau_resa[$j]['resa_idbulletin']) {
						$no_notice=$tableau_resa[$j]['resa_idnotice'] ;
						$no_bulletin=$tableau_resa[$j]['resa_idbulletin'] ;
						$tableau_expl_dispo = expl_dispo ($no_notice, $no_bulletin) ;
					}
					if (count($tableau_expl_dispo)) {
						for ($i=0;$i<count($tableau_expl_dispo);$i++) {
							if (!$f_dispo_loc || ($tableau_expl_dispo[$i]['idlocation'] == $f_dispo_loc)) {
								$aff_final .= "<tr>
								<td>".$tableau_resa[$j]['rank']."</td>
								<td>".$tableau_resa[$j]['resa_empr']."</td>
								<td>".$tableau_resa[$j]['resa_empr_loc_libelle']."</td>
								<td><b>".$tableau_resa[$j]['resa_tit']."</b></td>
								<td>".$tableau_expl_dispo[$i]['location']."</td>
								<td>".$tableau_expl_dispo[$i]['section']."</td>
								<td>".$tableau_expl_dispo[$i]['expl_cote']."</td>
								<td>".$tableau_expl_dispo[$i]['statut']."</td>
								<td>".$tableau_expl_dispo[$i]['support']."</td>
								<td>".$tableau_expl_dispo[$i]['expl_cb']."</td>";
								if ($pmb_transferts_actif=="1") $aff_final .= "<td>".$tableau_resa[$j]['loc_retrait_libelle']."</td>";
								$aff_final .= "</tr>";
							}
						}
					}
				}
				if ($aff_final) print "<table border='0'>$aff_final</table>";

				break;
			default:
				echo "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu_a_traiter']."</h1>";

				// affichage du résultat
				echo "
				<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page' method=post>
				<div class='left'>

				</div>
				<div class='right'>
				<img  src='./images/tableur.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export tableau EXCEL'/>&nbsp;&nbsp;
				<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
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
				if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
					echo $msg["edit_resa_expl_location_filter"]."&nbsp;";
					if ($f_loc=="")	$f_loc = $deflt_resas_location;
					echo gen_liste ("SELECT idlocation, location_libelle FROM docs_location order by location_libelle", "idlocation", "location_libelle", "f_loc", "document.forms['form-$current_module-list'].dest.value='';document.forms['form-$current_module-list'].submit();", $f_loc, -1,"",0, $msg["all_location"]);
					echo $msg["edit_resa_expl_available_filter"]."&nbsp;";
					echo gen_liste ("SELECT idlocation, location_libelle FROM docs_location order by location_libelle", "idlocation", "location_libelle", "f_dispo_loc", "document.forms['form-$current_module-list'].dest.value='';document.forms['form-$current_module-list'].submit();", $f_dispo_loc, -1,"",0, $msg["all_location"]);
				}

				echo "&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
				echo "
				<div class='row'></div></form><br />";

				$aff_final .= "<tr>
								<th>".$msg[366]."</th>
								<th>".$msg["empr_nom_prenom"]."</th>
								<th>".$msg["edit_resa_empr_location"]."</th>
								<th>".$msg[233]."</th>
								<th>".$msg["edit_resa_expl_location"]."</th>
								<th>$msg[295]</th>
								<th>$msg[296]</th>
								<th>$msg[297]</th>
								<th>$msg[294]</th>
								<th>$msg[232]</th>";
				if ($pmb_transferts_actif=="1") $aff_final .= "<th>" . $msg["edit_resa_loc_retrait"] . "</th>";
				$aff_final .= "</tr>";
				$tableau_resa = resa_list_resa_a_traiter () ;
				 //echo "<pre>" ; print_r($tableau_resa); echo "</pre>" ;
				for ($j=0; $j< count($tableau_resa); $j++) {
					$tableau_expl_dispo = array();
					if ($no_notice!=$tableau_resa[$j]['resa_idnotice'] || $no_bulletin!=$tableau_resa[$j]['resa_idbulletin']) {
						$no_notice=$tableau_resa[$j]['resa_idnotice'] ;
						$no_bulletin=$tableau_resa[$j]['resa_idbulletin'] ;
						$tableau_expl_dispo = expl_dispo ($no_notice, $no_bulletin) ;
						 //echo "<pre>" ; print_r($tableau_expl_dispo); echo "</pre>" ;
					}
					if (count($tableau_expl_dispo)) {
						for ($i=0;$i<count($tableau_expl_dispo);$i++) {
							if (!$f_dispo_loc || ($tableau_expl_dispo[$i]['idlocation'] == $f_dispo_loc)) {
								$aff_final .= "<tr>
								<td>".$tableau_resa[$j]['rank']."</td>
								<td>".$tableau_resa[$j]['resa_empr']."</td>
								<td>".$tableau_resa[$j]['resa_empr_loc_libelle']."</td>
								<td><b>".$tableau_resa[$j]['resa_tit']."</b></td>
								<td>".$tableau_expl_dispo[$i]['location']."</td>
								<td>".$tableau_expl_dispo[$i]['section']."</td>
								<td>".$tableau_expl_dispo[$i]['expl_cote']."</td>
								<td>".$tableau_expl_dispo[$i]['statut']."</td>
								<td>".$tableau_expl_dispo[$i]['support']."</td>
								<td>".$tableau_expl_dispo[$i]['expl_cb']."</td>";
								if ($pmb_transferts_actif=="1") $aff_final .= "<td>".$tableau_resa[$j]['loc_retrait_libelle']."</td>";
								$aff_final .= "</tr>";
							}
						}
					}
				}
				if ($aff_final) print pmb_bidi("\n\n<script src='./javascript/sorttable.js' type='text/javascript'></script><table border='0' class ='sortable'>$aff_final</table>\n\n") ;
				if (SESSrights & EDIT_AUTH) print pmb_bidi("<a href='./circ.php?categ=listeresa&sub=encours'>".$msg['lien_traiter_reservations']."<a>");
				break;
		}
		// echo "<pre>"; print_r($tableau); echo "</pre>";
		break;
	case "resa_planning" :
		echo "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_planning_menu']."</h1>";
		print planning_list (0, 0, 0, '','', EDIT_INFO_GESTION) ;
		break;
	case "resa" :
	default:
		echo "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu']."</h1>";
		print resa_list (0, 0, 0) ;
		break;
	}
