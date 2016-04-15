<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts.inc.php,v 1.6.4.2 2015-11-12 10:44:14 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$include_path/templates/transferts.tpl.php");
require_once ("$class_path/mono_display.class.php");
require_once ("$class_path/serial_display.class.php");

switch($dest) {
	case "TABLEAU":
		$fname = tempnam("./temp", "$fichier_temp_nom.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		$worksheet->write(0,0,$msg["transferts_edition_titre"]." : ".$msg["transferts_edition_".$sub]);
		break;
	case "TABLEAUHTML":
		//le titre de la page
		print "<h1>".$msg["transferts_edition_titre"]."&nbsp;:&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
		break;
	default:
		//le titre de la page
		print "<h1>".$msg["transferts_edition_titre"]."&nbsp;&gt;&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
		break;
}

// en fonction de l'etat du transfert
switch($sub) {
	case "validation":
		//initialisation du site d'origine
		if ($site_origine=="")
			$site_origine = $deflt_docs_location;
		
		//initialisation du site de destination
		if ($site_destination=="")
			$site_destination = 0;
	
		//la requete d'affichage
		$rqt = "SELECT ". 
					"num_notice as val_id_notice, num_bulletin as val_id_bulletin, ".
					"expl_cb as val_expl, expl_cote as val_cote, ". 
					"section_libelle as val_section , locd.location_libelle as val_dest, " .
					"loco.location_libelle as val_source, lender_libelle as val_expl_owner, motif as val_motif, " .
					"empr_cb as val_empr_cb, concat(empr_nom,' ',empr_prenom) as val_empr_nom_prenom " .
				"FROM transferts " .
					"INNER JOIN transferts_demande ON id_transfert=num_transfert " .
					"INNER JOIN exemplaires ON num_expl=expl_id " .
					"INNER JOIN docs_section ON expl_section=idsection " .
					"INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation " .
					"INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation " .
					"INNER JOIN lenders ON expl_owner=idlender " .
					"LEFT JOIN resa ON resa_trans=id_resa " .
					"LEFT JOIN empr ON resa_idempr=id_empr " .
		"WHERE etat_transfert=0 ". 
					"AND etat_demande=0 ";
		
		//filtre source si nécéssaire
		if ($site_origine!=0)
			$rqt .= " AND num_location_source="  .$site_origine;
		
		//filtre destination si nécéssaire
		if ($site_destination!=0)
			$rqt .= " AND num_location_dest=" . $site_destination;
		
		break;
		
	case "envoi":
		//initialisation du site d'origine
		if ($site_origine=="")
			$site_origine = $deflt_docs_location;
		
		//initialisation du site de destination
		if ($site_destination=="")
			$site_destination = 0;
	
		//la requete d'affichage
		$rqt = "SELECT ". 
					"num_notice as val_id_notice, num_bulletin as val_id_bulletin,  ".
					"expl_cb as val_expl, expl_cote as val_cote, ". 
					"section_libelle as val_section , locd.location_libelle as val_dest, " .
					"loco.location_libelle as val_source, lender_libelle as val_expl_owner, motif as val_motif, " .
					"empr_cb as val_empr_cb, concat(empr_nom,' ',empr_prenom) as val_empr_nom_prenom " .
				"FROM transferts " .
					"INNER JOIN transferts_demande ON id_transfert=num_transfert " .
					"INNER JOIN exemplaires ON num_expl=expl_id " .
					"INNER JOIN docs_section ON expl_section=idsection " .
					"INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation " .
					"INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation " .
					"INNER JOIN lenders ON expl_owner=idlender " .
					"LEFT JOIN resa ON resa_trans=id_resa " .
					"LEFT JOIN empr ON resa_idempr=id_empr " .
		"WHERE etat_transfert=0 ". 
					"AND etat_demande=1 ";

		//filtre source si nécéssaire
		if ($site_origine!=0)
			$rqt .= " AND num_location_source="  .$site_origine;
		
		//filtre destination si nécéssaire
		if ($site_destination!=0)
			$rqt .= " AND num_location_dest="  .$site_destination;
		
		break;
		
	case "retours":
		//initialisation du site d'origine
		if ($site_origine=="")
			$site_origine = $deflt_docs_location;
		
		//initialisation du site de destination
		if ($site_destination=="")
			$site_destination = 0;
	
		//la requete d'affichage
		$rqt = "SELECT ". 
					"num_notice as val_id_notice, num_bulletin as val_id_bulletin, ".
					"expl_cb as val_expl, expl_cote as val_cote, ". 
					"section_libelle as val_section , locd.location_libelle as val_dest, " .
					"loco.location_libelle as val_source, lender_libelle as val_expl_owner, motif as val_motif, " .
					"empr_cb as val_empr_cb, concat(empr_nom,' ',empr_prenom) as val_empr_nom_prenom " .
				"FROM transferts " .
					"INNER JOIN transferts_demande ON id_transfert=num_transfert " .
					"INNER JOIN exemplaires ON num_expl=expl_id " .
					"INNER JOIN docs_section ON expl_section=idsection " .
					"INNER JOIN docs_location locd ON num_location_source=locd.idlocation " .
					"INNER JOIN docs_location loco ON num_location_dest=loco.idlocation " .
					"INNER JOIN lenders ON expl_owner=idlender " .
					"LEFT JOIN resa ON resa_trans=id_resa " .
					"LEFT JOIN empr ON resa_idempr=id_empr " .
				"WHERE etat_transfert=0 ". 
					"AND type_transfert=1 ".
					"AND etat_demande=3 ";

		//filtre origine si nécéssaire
		if ($site_origine!=0)
			$rqt .= " AND num_location_dest=".$site_origine;
		
		//filtre destination si nécéssaire
		if ($site_destination!=0)
			$rqt .= " AND num_location_source=".$site_destination;
				
		//application du filtre sur la date de retour
		switch ($f_etat_date) {
			case "1":
				$rqt .= " AND (DATEDIFF(DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY),CURDATE())<=0";
				$rqt .= " AND DATEDIFF(date_retour,CURDATE())>=0)";
				break;
			case "2":
				$rqt .= " AND DATEDIFF(date_retour,CURDATE())<0";
				break;
		
		}
			
		break;
}

$rqt .=	" ORDER BY val_section, val_expl";

//echo $rqt;

$cols_supp = "";
// si la destination n'est pas précisé
if ($site_origine==0) {
	$cols_supp .= $transferts_edition_titre_source;
	$cols_supp_ligne .= $transferts_edition_ligne_source;
}

if ($site_destination==0) {
	$cols_supp .= $transferts_edition_titre_destination;
	$cols_supp_ligne .= $transferts_edition_ligne_destination;
}

$tabLigne = str_replace("!!colonnes_variables!!", $cols_supp_ligne, $transferts_edition_ligne);

//echo $rqt;
//execution de la requete
$req = pmb_mysql_query($rqt);

switch($dest) {
	case "TABLEAU":
		$nbr_champs = @pmb_mysql_num_fields($req);
		$nbr_lignes = @pmb_mysql_num_rows($req);
		
		$worksheet->write(2,0,$msg["transferts_edition_tableau_titre"]);
		$worksheet->write(2,1,$msg["transferts_edition_tableau_section"]);
		$worksheet->write(2,2,$msg["transferts_edition_tableau_cote"]);
		$worksheet->write(2,3,$msg["transferts_edition_tableau_expl"]);
		$worksheet->write(2,4,$msg["transferts_edition_tableau_empr"]);
		$worksheet->write(2,5,$msg["transferts_edition_tableau_expl_owner"]);
		if ($site_origine==0 && $site_destination==0) {
			$worksheet->write(2,6,$msg["transferts_edition_tableau_source"]);
			$worksheet->write(2,7,$msg["transferts_edition_tableau_destination"]);
			$worksheet->write(2,8,$msg["transferts_edition_tableau_motif"]);
		}elseif ($site_origine==0) {
			$worksheet->write(2,6,$msg["transferts_edition_tableau_source"]);
			$worksheet->write(2,7,$msg["transferts_edition_tableau_motif"]);
		}elseif ($site_destination==0) {
			$worksheet->write(2,6,$msg["transferts_edition_tableau_destination"]);
			$worksheet->write(2,7,$msg["transferts_edition_tableau_motif"]);
		}else{
			$worksheet->write(2,6,$msg["transferts_edition_tableau_motif"]);
		}

		$ligne=3;
		while ($value = pmb_mysql_fetch_array($req)) {
			$worksheet->write($ligne,0,strip_tags(aff_titre($value[0], $value[1])));
			$worksheet->write($ligne,1,$value[4]);
			$worksheet->write($ligne,2,$value[3]);
			$worksheet->write($ligne,3,$value[2]);
			$worksheet->write($ligne,4,$value[10]);
			$worksheet->write($ligne,5,$value[7]);
			if ($site_origine==0 && $site_destination==0) {
				$worksheet->write($ligne,6,$value[6]);
				$worksheet->write($ligne,7,$value[5]);
				$worksheet->write($ligne,8,$value[8]);
			}elseif ($site_origine==0) {
				$worksheet->write(2,6,$msg["transferts_edition_tableau_source"]);
				$worksheet->write($ligne,6,$value[6]);
				$worksheet->write($ligne,7,$value[8]);
			}elseif ($site_destination==0) {
				$worksheet->write(2,6,$msg["transferts_edition_tableau_destination"]);
				$worksheet->write($ligne,6,$value[5]);
				$worksheet->write($ligne,7,$value[8]);
			}else{
				$worksheet->write($ligne,6,$value[8]);
			}
			
			$ligne++;
		
		}
		$workbook->close();
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		break;
	case "TABLEAUHTML":
		//le nombre de colonnes dans la requete pour remplacer les champs dans le template
		$nbCols = pmb_mysql_num_fields($req);
		
		$transferts_list .= "<table>" ;
		$transferts_list .= "<tr>
				<th>".$msg["transferts_edition_tableau_titre"]."</th>
				<th>".$msg["transferts_edition_tableau_section"]."</th>
				<th>".$msg["transferts_edition_tableau_cote"]."</th>
				<th>".$msg["transferts_edition_tableau_expl"]."</th>
				<th>".$msg["transferts_edition_tableau_empr"]."</th>
				<th>".$msg["transferts_edition_tableau_expl_owner"]."</th>
				!!colonnes_variables!!
				<th>".$msg["transferts_edition_tableau_motif"]."</th>
			</tr>
			!!lignes_tableau!!
		</table>";

		$tmpAff = "";
		//on boucle sur la liste
		while ($value = pmb_mysql_fetch_array($req)) {
		
			//pour la coloration
			if ($nb % 2)
				$tmpLigne = str_replace("!!class_ligne!!", "odd", $tabLigne);
			else			
				$tmpLigne = str_replace("!!class_ligne!!", "even", $tabLigne);
			
			//on parcours toutes les colonnes de la requete
			for($i=0;$i<$nbCols;$i++) {
				$tmpLigne = str_replace("!!".pmb_mysql_field_name($req,$i)."!!",$value[$i],$tmpLigne);
			}
		
			//affichage du titre
			$tmpLigne = str_replace("!!val_titre!!", aff_titre($value[0], $value[1]), $tmpLigne);
			
			//on ajoute la ligne a la liste
			$tmpAff .= $tmpLigne;
			$nb++;
		
		} //fin while
		
		//on met les lignes du tableau dans le tableau
		$transferts_list = str_replace("!!lignes_tableau!!",$tmpAff,$transferts_list);
		
		//si on a des colonnes en plus
		$transferts_list = str_replace("!!colonnes_variables!!", $cols_supp, $transferts_list);

		//on affiche la page !
		echo $transferts_list;
		break;
	default:
		//le nombre de colonnes dans la requete pour remplacer les champs dans le template
		$nbCols = pmb_mysql_num_fields($req);
		
		$tmpAff = "";
		
		//on boucle sur la liste
		while ($value = pmb_mysql_fetch_array($req)) {
		
			//pour la coloration
			if ($nb % 2)
				$tmpLigne = str_replace("!!class_ligne!!", "odd", $tabLigne);
			else			
				$tmpLigne = str_replace("!!class_ligne!!", "even", $tabLigne);
			
			//on parcours toutes les colonnes de la requete
			for($i=0;$i<$nbCols;$i++) {
				$tmpLigne = str_replace("!!".pmb_mysql_field_name($req,$i)."!!",$value[$i],$tmpLigne);
			}
		
			//affichage du titre
			$tmpLigne = str_replace("!!val_titre!!", aff_titre($value[0], $value[1]), $tmpLigne);
			
			//on ajoute la ligne a la liste
			$tmpAff .= $tmpLigne;
			$nb++;
		
		} //fin while
		
		//on met les lignes du tableau dans le tableau
		$tmpAff = str_replace("!!lignes_tableau!!",$tmpAff,$transferts_edition_tableau);
		
		//si on a des colonnes en plus
		$tmpAff = str_replace("!!colonnes_variables!!", $cols_supp, $tmpAff);
		
		//la sub pour retomber sur ses pattes
		$tmpAff = str_replace("!!sub!!",$sub,$tmpAff);
		
		//les filtres
		//pour la liste des origines
		$filtres = str_replace("!!liste_sites_origine!!",creer_liste_localisations($site_origine),$transferts_edition_filtre_source);
		//pour la liste de destination
		$filtres .= str_replace("!!liste_sites_destination!!",creer_liste_localisations($site_destination),$transferts_edition_filtre_destination);
		
		if ($sub=="retours") {
			//le filtre de l'etat de la date
			$filtres .= str_replace("!!sel_" . $f_etat_date . "!!", "selected", $transferts_retour_filtre_etat);
		}
		
		//la sub pour retomber sur ses pattes
		$tmpAff = str_replace("!!filtres_edition!!",$filtres,$tmpAff);
		
		//on affiche la page !
		echo $tmpAff;
		break;
}

//***********************************************************************************************************

//renvoi le titre de l'exemplaire pour le tableau
function aff_titre($id_notice,$id_bulletin) {
	if ($id_notice!=0) {
		//c'est une notice
		$disp = new mono_display($id_notice);

	} else {
		//c'est un bulletin
		$disp = new bulletinage_display($id_bulletin);
	}
	
	return $disp->header;
}

//***********************************************************************************************************

//crée la liste des localisations en précisant une de sélectionner et si on rajoute une ligne tous
function creer_liste_localisations($loc_select,$tous = true) {
	global $msg;

	//la requete
	$rqt="SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle ";
	$req = pmb_mysql_query($rqt);
	
	
	//initialisation de la liste
	if ($tous) 
		$tmpListe = "<option value=0>".$msg["all_location"]."</option>";
	else
		$tmpListe = "";
	
	//on parcours
	while ($value = pmb_mysql_fetch_array($req)) {
		
		$tmpListe .= "<option value=".$value[0]; 
		
		if ($value[0]==$loc_select)
			$tmpListe .= " selected";
		
		$tmpListe .= ">".$value[1]."</option>";
		
	}
	
	return $tmpListe;

}

?>