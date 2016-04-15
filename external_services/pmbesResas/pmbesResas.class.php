<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesResas.class.php,v 1.3 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/bannette.class.php");

class pmbesResas extends external_services_api_class {
	var $error=false;		//Y-a-t-il eu une erreur
	var $error_message="";	//Message correspondant à l'erreur
	
	function restore_general_config() {
		
	}
	
	function form_general_config() {
		return false;
	}
	
	function save_general_config() {
		
	}
		
	function list_empr_resas($empr_id) {
		global $dbh;
		global $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$empr_id += 0;
			if (!$empr_id)
				throw new Exception("Missing parameter: empr_id");
		
			$requete  = "SELECT id_resa FROM resa WHERE (resa_idempr='$empr_id')"; 
				
			$res = pmb_mysql_query($requete, $dbh);
			if ($res)
				while($row = pmb_mysql_fetch_assoc($res)) {
					$result[] = $row["id_resa"];
				}
		
			return $result;
		} else {
			return array();
		}
	}
	
	function get_empr_information($idempr) {
		global $pmb_lecteurs_localises, $deflt_docs_location;
		global $dbh;
		global $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$empr_id += 0;
			if (!$idempr)
				throw new Exception("Missing parameter: idempr");
				
			$sql = "SELECT id_empr, empr_cb, empr_nom, empr_prenom FROM empr WHERE id_empr = ".$idempr;
			$res = pmb_mysql_query($sql);
			if (!$res)
				throw new Exception("Not found: idempr = ".$idempr);
			$row = pmb_mysql_fetch_assoc($res);
	
			$result = $row;
			
			return $result;
		} else {
			return array();
		}			
	}
	
	function get_empr_information_and_resas($empr_id) {
		return array(
			"information" => $this->get_empr_information($empr_id),
			"resas_ids" => $this->list_empr_resas($empr_id)
		);
	}
	
	/* appuie sur la function resa_list de resa_func.inc.php */
	/* montrerquoi => validees,invalidees,valid_noconf 
	 * condition => encours,depassee */
//	function listResas($idnotice=0, $idbulletin=0, $idempr=0, $order="", $condition = "", $montrerquoi='',$f_loc='') {
//		global $deflt_docs_location, $pmb_lecteurs_localises;
//		
//		/* Mis à la place du paramètre where */
//		if ($condition="en_cours") {
//			$cl_where = "(resa_date_fin >= CURDATE() or resa_date_fin='0000-00-00')";	
//		} else if ($condition="depassee"){
//			$cl_where = "resa_date_fin < CURDATE() and resa_date_fin<>'0000-00-00' ";
//		} 
//		
//		if (!$montrerquoi) $montrerquoi='all' ;
//		if (!$order) $order="notices_m.index_sew, resa_idnotice, resa_idbulletin, resa_date" ;
//	
//		if ($pmb_lecteurs_localises && !$idempr){
//			if ($f_loc=="")	$f_loc = $deflt_docs_location;
//			if ($f_loc)	$sql_expl_loc= " and expl_location='".$f_loc."' ";
//		}
//
//		//partie transfert ??
////		$sql_loc_resa_from=", resa_loc ";
//		//retrait de la resa sur lieu lecteur
//		$sql_suite .= " AND empr_location='".$f_loc."' ";
//		
//		$sql="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_cb, resa_confirmee, resa_idempr, empr_nom, empr_prenom, empr_cb, location_libelle, resa_loc_retrait, ";
//		$sql.=" trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
//		$sql.=" ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc, ";
//		$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
//		$sql.=" FROM (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) ";
//		$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
//		$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
//		$sql.=" empr, docs_location $sql_loc_resa_from ";
//		$sql.=" WHERE resa_idempr = id_empr AND idlocation = empr_location ";
//		$sql.=$sql_suite;
//		
//		if ($idempr) 
//			$sql.=" AND id_empr='$idempr'";
//			
//		if ($montrerquoi=='validees')
//			$sql .= " AND resa_cb<>''";
//			
//		if ($montrerquoi=='invalidees')
//			$sql .= " AND resa_cb=''";
//		
//		if ($montrerquoi=='valid_noconf') {
//			$sql .= " AND resa_cb!=''";
//			$sql .= " AND resa_confirmee=0";
//		}
//		if ($cl_where) 
//			$sql.=" AND ".$cl_where ;
//	
//		if ($idnotice || $idbulletin) {
//			$sql="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_cb, resa_confirmee, resa_idempr, empr_nom, empr_prenom, empr_cb, location_libelle, resa_loc_retrait, ";
//			$sql.=" trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
//			$sql.=" ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc, ";
//			$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
//			$sql.=" FROM (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) ";
//			$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
//			$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
//			$sql.=" empr, docs_location  ";
//			$sql.=" WHERE resa_idempr = id_empr AND idlocation = empr_location  AND resa_idnotice = '$idnotice' AND resa_idbulletin='$idbulletin' ";
//			$sql.=" ORDER BY  resa_date" ;
//			$f_loc=0;
//		}	
//
//		$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error()); 	
//		
//		while ($data = pmb_mysql_fetch_assoc($req)) {
//			$result[] = array (
//				"resa_idnotice" => $data['resa_idnotice'],
//				"resa_idbulletin" => $data['resa_idbulletin'],
//				"resa_date" => $data['resa_date'],
//				"resa_date_debut" => $data['resa_date_debut'],
//				"resa_date_fin" => $data['resa_date_fin'],
//				"resa_cb" => $data['resa_cb'],
//				"resa_confirmee" => $data['resa_confirmee'],
//				"resa_idempr" => $data['resa_idempr'],
//				"empr_nom" => $data['empr_nom'],
//				"empr_prenom" => $data['empr_prenom'],
//				"empr_cb" => $data['empr_cb'],
//				"location_libelle" => $data['location_libelle'],
//				"resa_loc_retrait" => $data['resa_loc_retrait'],
//				"tit" => $data['tit'],
//				"id_resa" => $data["id_resa"],
//				"typdoc" => $data['typdoc'],
//				"perime" => $data['perime'],
//				"aff_resa_date_debut" => $data['aff_resa_date_debut'],
//				"aff_resa_date_fin" => $data['aff_resa_date_fin'],
//				"aff_resa_date" => $data['aff_resa_date'],
//				"rank" => $rank,
//				"situation" => $situation,
//			);
//		}
		
//		//on parcours la liste des réservations
//		while ($data = pmb_mysql_fetch_array($req)) {
//			$resa_idnotice = $data['resa_idnotice'];
//			$resa_idbulletin = $data['resa_idbulletin'];
//			$resa_idempr = $data['resa_idempr'] ;
//			$precedenteresa_idbulletin=0;
//			$precedenteresa_idnotice=0;
//		
//			if(!($idnotice || $idbulletin))
//			if($f_loc &&!$idempr && $data['resa_cb'] && $data['resa_confirmee']){
//				// Dans la liste des résa à traiter, on n'affiche pas la résa qui a été affecté par un autre site
//				$query = "SELECT expl_location FROM exemplaires WHERE expl_cb='".$data['resa_cb']."' ";
//				$res = @pmb_mysql_query($query, $dbh);
//				if(($data_expl = pmb_mysql_fetch_array($res))){
//					if($data_expl['expl_location']!=$f_loc) {
//						$no_aff=1;
//						continue;
//					}	
//				}
//			}
//			if($idempr)$f_loc=0;
//			$rank = recupere_rang($resa_idempr, $resa_idnotice, $resa_idbulletin,$f_loc) ;
//			$resa=new reservation($resa_idempr,$resa_idnotice, $resa_idbulletin);
//			$resa->get_resa_cb();
//			
//			if (($resa_idnotice != $precedenteresa_idnotice) || ($resa_idbulletin != $precedenteresa_idbulletin)) {
//				$precedenteresa_idnotice=$resa_idnotice;
//				$precedenteresa_idbulletin=$resa_idbulletin;
//						
//				// détermination de la date à afficher dans la case retour pour le rang 1
//				// disponible, réservé ou date de retour du premier exemplaire
//				
//				// on compte le nombre total d'exemplaires prêtables pour la notice
//				$query = "SELECT count(1) FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND pret_flag=1 $sql_expl_loc ";
//				if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//					elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;
//				$tresult = @pmb_mysql_query($query, $dbh);
//				$total_ex = pmb_mysql_result($tresult, 0, 0);
//				if($sql_expl_loc && !$total_ex) $no_aff=1;
//				// on compte le nombre d'exemplaires sortis
//				$query = "SELECT count(1) as qte FROM exemplaires , pret WHERE pret_idexpl=expl_id $sql_expl_loc ";
//				if ($resa_idnotice) $query .= " and expl_notice=".$resa_idnotice;
//					elseif ($resa_idbulletin) $query .= " and expl_bulletin=".$resa_idbulletin;
//	
//				$tresult = @pmb_mysql_query($query, $dbh);
//				$total_sortis = pmb_mysql_result($tresult, 0, 0);
//				
//				// on en déduit le nombre d'exemplaires disponibles
//				$total_dispo = $total_ex - $total_sortis;
//				
//				$lien_transfert = false;
//				
//				if($total_dispo>0) {
//					// un exemplaire est disponible pour le réservataire (affichage : disponible)
//					$situation = "<strong>$msg[359]</strong>";
//					if($data['resa_cb']&& $data['aff_resa_date_fin']) $situation = "<strong>".$msg['expl_reserve']."</strong>";
//					elseif($rank>$total_dispo)	$situation = "<strong>".$msg['expl_resa_already_reserved']."</strong>";
//					if ( ($pmb_transferts_actif=="1") && ($info_gestion==GESTION_INFO_GESTION) ) {
//						$dest_loc = resa_loc_retrait($data['id_resa']);
//					
//						if ($dest_loc!=0) {
//							$query = "SELECT count(1) FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND pret_flag=1";
//							$query .= " AND expl_location=".$dest_loc;
//							if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//								elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;
//							$tresult = pmb_mysql_query($query, $dbh);
//							$total_ex = pmb_mysql_result($tresult, 0);
//							
//							if ($total_ex==0) {
//								//on a pas d'exemplaires sur le site de retrait
//								//on regarde si on en ailleurs
//								$query = "SELECT count(1) FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND pret_flag=1";
//								$query .= " AND expl_location<>".$dest_loc;
//								if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//									elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;
//								$tresult = pmb_mysql_query($query, $dbh);
//								$total_ex = pmb_mysql_result($tresult, 0);
//								
//								if ($total_ex!=0) { 
//									//on en a au moins un ailleurs!
//									//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
//									$query = "SELECT count(1) FROM transferts WHERE etat_transfert=0 AND origine=4 AND origine_comp=".$data['id_resa'];
//									$tresult = pmb_mysql_query($query, $dbh);
//									$nb_trans = pmb_mysql_result($tresult, 0);
//									
//									if ($nb_trans!=0) {
//										//on a un transfert en cours
//										$situation = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
//									} else
//										$lien_transfert = true;
//								}
//							} //if ($total_ex==0)
//						} //if ($dest_loc!=0)
//					} //if ( ($pmb_transferts_actif=="1") && ($info_gestion==GESTION_INFO_GESTION) )
//				} else {
//					if($total_dispo) {
//						// un ou des exemplaires sont disponibles, mais pas pour ce réservataire (affichage : reservé)
//						$situation = $msg["resa_expl_reserve"];
//					} else {
//						// rien n'est disponible, on trouve la date du premier retour
//						$query = "SELECT date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour from pret p, exemplaires e ";
//						if ($resa_idnotice) $query .= " WHERE e.expl_notice=".$resa_idnotice;
//							elseif ($resa_idbulletin) $query .= " WHERE e.expl_bulletin=".$resa_idbulletin;
//						$query .= " AND e.expl_id=p.pret_idexpl";
//						$query .= " ORDER BY p.pret_retour LIMIT 1";
//						$tresult = pmb_mysql_query($query, $dbh);
//						if (pmb_mysql_num_rows($tresult)) {
//							$situation = pmb_mysql_result($tresult, 0, 0);
//						}else {
//							$situation = $msg["resa_no_expl"];
//						}
//						if ( ($pmb_transferts_actif=="1") && ($f_loc!=0) && $transferts_choix_lieu_opac!=3) {
//							//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
//							$query = "SELECT count(1) FROM transferts WHERE origine_comp=".$data['id_resa'];
//							$no_aff=0;
//							$tresult = pmb_mysql_query($query, $dbh);
//							$nb_trans = pmb_mysql_result($tresult, 0);
//							if ($nb_trans!=0) {
//								//on a un transfert en cours
//								$situation = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
//							} else {
//								$query = "SELECT count(1) FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND pret_flag=1";
//								$query .= " AND expl_location<>".$f_loc;
//								if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//									elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;
//								$tresult = pmb_mysql_query($query, $dbh);
//								$total_ex = pmb_mysql_result($tresult, 0);
//								
//								if ($total_ex!=0) { 
//									//on en a au moins un ailleurs!
//									// sont-il déjà prêtés ou réservé
//									$query = "SELECT count(1) FROM exemplaires, docs_statut
//									 WHERE expl_statut=idstatut AND pret_flag=1";
//									$query .= " AND expl_location<>".$f_loc;
//									if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//									elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;		
//									$query .= " and expl_id not in(select 	pret_idexpl from pret, exemplaires where pret_idexpl=expl_id ";
//									if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//									elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;		
//									$query .= ")";
//									$query .= " and expl_cb not in(select resa_cb from resa, exemplaires where resa_cb=expl_cb ";
//									if ($resa_idnotice)  $query .= " AND expl_notice=".$resa_idnotice;
//									elseif ($resa_idbulletin) $query .= " AND expl_bulletin=".$resa_idbulletin;		
//									$query .= ")";
//											
//									$tresult = pmb_mysql_query($query, $dbh);
//									$nb_trans = pmb_mysql_result($tresult, 0);
//									if (!$nb_trans) {
//										$situation = $msg["resa_no_expl"];
//									} else
//										$lien_transfert = true;
//								}	
//							}						
//						}	
//					}
//				}
//			} else 
//				$situation='';
//		}
//		return $result;
//	}

	function generatePdfResasReaders($tresas, $location_biblio=0) {
		global $dbh,$ourPDF, $fpdf,$deflt2docs_location;
		global $fdp, $after_list, $limite_after_list,$before_list, $madame_monsieur;
		global $nb_1ere_page,$nb_par_page,$taille_bloc_expl,$debut_expl_1er_page,$debut_expl_page;
		global $marge_page_gauche,$marge_page_droite,$largeur_page,$hauteur_page,$format_page;

		if (!$tresas) {
			return 0;
		}
		if (SESSrights & CIRCULATION_AUTH) {
			if (!$location_biblio) $location_biblio = $deflt2docs_location;
			$this->infos_biblio($location_biblio);
			
			// la formule de politesse du bas (le signataire)
			$var = "pdflettreresa_fdp";
			global $$var;
			eval ("\$fdp=\"".$$var."\";");
			
			// le texte après la liste des ouvrages en résa
			$var = "pdflettreresa_after_list";
			global $$var;
			eval ("\$after_list=\"".$$var."\";");
			
			// la position verticale limite du texte after_liste (si >, saut de page et impression)
			$var = "pdflettreresa_limite_after_list";
			global $$var;
			$limite_after_list = $$var;
					
			// le texte avant la liste des ouvrges en réservation
			$var = "pdflettreresa_before_list";
			global $$var;
			eval ("\$before_list=\"".$$var."\";");
			
			// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
			$var = "pdflettreresa_madame_monsieur";
			global $$var;
			eval ("\$madame_monsieur=\"".$$var."\";");
			
			// le nombre de blocs notices à imprimer sur la première page
			$var = "pdflettreresa_nb_1ere_page";
			global $$var;
			$nb_1ere_page = $$var;
			
			// le nombre de blocs notices à imprimer sur les pages suivantes
			$var = "pdflettreresa_nb_par_page";
			global $$var;
			$nb_par_page = $$var;
			
			// la taille d'un bloc notices 
			$var = "pdflettreresa_taille_bloc_expl";
			global $$var;
			$taille_bloc_expl = $$var;
			
			// la position verticale du premier bloc notice sur la première page
			$var = "pdflettreresa_debut_expl_1er_page";
			global $$var;
			$debut_expl_1er_page = $$var;
			
			// la position verticale du premier bloc notice sur les pages suivantes
			$var = "pdflettreresa_debut_expl_page";
			global $$var;
			$debut_expl_page = $$var;
			
			// la marge gauche des pages
			$var = "pdflettreresa_marge_page_gauche";
			global $$var;
			$marge_page_gauche = $$var;
			
			// la marge droite des pages
			$var = "pdflettreresa_marge_page_droite";
			global $$var;
			$marge_page_droite = $$var;
			
			// la largeur des pages
			$var = "pdflettreresa_largeur_page";
			global $$var;
			$largeur_page = $$var;
			
			// la hauteur des pages
			$var = "pdflettreresa_hauteur_page";
			global $$var;
			$hauteur_page = $$var;
			
			// le format des pages
			$var = "pdflettreresa_format_page";
			global $$var;
			$format_page = $$var;
		
			$taille_doc=array($largeur_page,$hauteur_page);
			
			$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
			$ourPDF->Open();
			
			foreach ($tresas as $idempr=>$resa) {
				if($idempr != $id_empr_tmp){
					$liste_ids_resa = implode(",", $resa);
					lettre_resa_par_lecteur($idempr,$liste_ids_resa) ;
					$id_empr_tmp=$idempr;	
				}
			}
			$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
			
			return $ourPDF;
		} else {
			return 0;
		}
	}
		
	function confirmResaReader($id_resa=0, $id_empr_concerne=0, $f_loc=0) {
		global $dbh;
		global $msg, $charset;
		global $PMBuserid, $PMBuseremailbcc ;
		global $pdflettreresa_priorite_email ;
		global $pdflettreresa_before_list , $pdflettreresa_madame_monsieur, $pdflettreresa_after_list, $pdflettreresa_fdp;
		global $biblio_name, $biblio_email ;
		global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone ; 
		global $pmb_transferts_actif,$transferts_choix_lieu_opac;
		global $empr_sms_activation;	
		global $empr_sms_msg_resa_dispo;  		
//		global $deflt2docs_location;
		
		if (SESSrights & CIRCULATION_AUTH) {
			if ($pdflettreresa_priorite_email==3) return ;	

			$this->infos_biblio();
//			$this->infos_biblio($deflt2docs_location);
		
			$query = "select distinct "; 	
			$query .= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";  
			$query .= "date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, ";
			$query .= "date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, ";
			$query .= "empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_sms, id_resa, ";
			$query .= "trim(concat(ifnull(notices_m.niveau_biblio,''), ifnull(notices_s.niveau_biblio,''))) as niveau_biblio, ";
			$query .= "trim(concat(ifnull(notices_m.notice_id,''), ifnull(notices_s.notice_id,''))) as id_notice ";
			$query .= "from (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), empr ";
			$query .= "where id_resa in (".$id_resa.") and resa_idempr=id_empr ";
			$query .= "and resa_confirmee=0";
			if ($id_empr_concerne) $query .= " and id_empr=$id_empr_concerne ";
		
			if ($f_loc) $query .= " and empr_location=$f_loc ";
			
			$result = pmb_mysql_query($query, $dbh);
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=".$charset."\n";
		
			$var = "pdflettreresa_fdp";
			eval ("\$pdflettreresa_fdp=\"".$$var."\";");
		
			// le texte après la liste des ouvrages en résa
			$var = "pdflettreresa_after_list";
			eval ("\$pdflettreresa_after_list=\"".$$var."\";");
				
			// le texte avant la liste des ouvrges en réservation
			$var = "pdflettreresa_before_list";
			eval ("\$pdflettreresa_before_list=\"".$$var."\";");
			
			// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
			$var = "pdflettreresa_madame_monsieur";
			eval ("\$pdflettreresa_madame_monsieur=\"".$$var."\";");
					
			$tab_resa = array();
			while ($empr=pmb_mysql_fetch_object($result)) {
				$id_empr = $empr->id_empr ;				
				$rqt_maj = "update resa set resa_confirmee=1 where id_resa in (".$id_resa.") AND resa_cb is not null and resa_cb!=''" ;
				if ($id_empr_concerne) $rqt_maj .= " and resa_idempr=$id_empr_concerne ";
				pmb_mysql_query($rqt_maj, $dbh);
				if (($pdflettreresa_priorite_email==1 || $pdflettreresa_priorite_email==2) && $empr->empr_mail) {
					$to = $empr->empr_prenom." ".$empr->empr_nom." <".$empr->empr_mail.">";
					$output_final = "<html><body>" ;
					$pdflettreresa_madame_monsieur=str_replace("!!empr_first_name!!", $empr->empr_prenom,$pdflettreresa_madame_monsieur);
					$output_final .= "$pdflettreresa_madame_monsieur <br />".$pdflettreresa_before_list ;
					if($empr->niveau_biblio == 'm' || $empr->niveau_biblio == 'b'){
						$affichage=new mono_display($empr->id_notice,0,'','','','','','','','','','','',true,'','');
						$output_final .= "<hr /><strong>".$affichage->header."</strong>";
					} elseif($empr->niveau_biblio == 's' || $empr->niveau_biblio == 'a'){
						$affichage_perio=new serial_display($empr->id_notice,0);
						$output_final .= "<hr /><strong>".$affichage_perio->header."</strong>";
					}
					$output_final .= "<br />";
					$output_final .= $msg['fpdf_valide']." ".$empr->aff_resa_date_debut." ".$msg['fpdf_valable']." ".$empr->aff_resa_date_fin ;
					$lieu_retrait="";
					if($pmb_transferts_actif && $transferts_choix_lieu_opac==3) {
						$rqt = "select resa_confirmee, resa_cb,resa_loc_retrait from resa where id_resa in (".$id_resa.")  and resa_cb is not null and resa_cb!='' ";
						$res = pmb_mysql_query($rqt, $dbh) ;
						if(($resa_lue = pmb_mysql_fetch_object($res))) {
							if ($resa_lue->resa_confirmee) {
								if ($resa_lue->resa_loc_retrait) {
									$loc_retait=$resa_lue->resa_loc_retrait;
								} else {
									$rqt = "select expl_location from exemplaires where expl_cb='".$resa_lue->resa_cb."' ";
									$res = pmb_mysql_query($rqt, $dbh) ;
									if(($res_expl = pmb_mysql_fetch_object($res))) {	
										$loc_retait=$res_expl->expl_location;						
									}
								}
								$rqt = "select location_libelle from docs_location where idlocation=".$loc_retait;
								$res = pmb_mysql_query($rqt, $dbh) ;
								if(($res_expl = pmb_mysql_fetch_object($res))) {	
									$lieu_retrait=str_replace("!!location!!",$res_expl->location_libelle,$msg["resa_lettre_lieu_retrait"]);						
								}		
							}
						}	
					}
					$output_final .= "<br />$lieu_retrait<br /><hr />$pdflettreresa_after_list <br />".$pdflettreresa_fdp ;
					$output_final .= "<br /><br />".mail_bloc_adresse() ;
					$output_final .= "</body></html> ";
					if(is_resa_confirme($empr->id_resa)) {
						$res_envoi=mailpmb($empr->empr_prenom." ".$empr->empr_nom, $empr->empr_mail,$msg["mail_obj_resa_validee"]." : ".$empr->empr_prenom." ".mb_strtoupper($empr->empr_nom,$charset)." (".$empr->empr_cb.")",$output_final,$biblio_name, $biblio_email, $headers, "", $PMBuseremailbcc, 1);	
					}	
					if (!$res_envoi || $pdflettreresa_priorite_email==2) {
						if(is_resa_confirme($empr->id_resa)) array_push($tab_resa,$empr->id_resa);
					}
				} elseif ($pdflettreresa_priorite_email!=3) {
					if(is_resa_confirme($empr->id_resa)) array_push($tab_resa,$empr->id_resa);
				}				
				if(is_resa_confirme($empr->id_resa) && $empr_sms_activation && $empr->empr_tel1 && $empr->empr_sms && $empr_sms_msg_resa_dispo){		
					$res_envoi_sms=send_sms(1, 0, $empr->empr_tel1,$empr_sms_msg_resa_dispo);
				}		
			} // end while
			$valeur_tab = implode(',',$tab_resa);		
			if($valeur_tab)  return $valeur_tab;
			else return "";
		} else {
			return;
		}
	}
	
//	function generatePdfResaReader($id_empr, $f_loc) {
//		global $dbh,$ourPDF, $fpdf;
//		global $fdp, $after_list, $limite_after_list,$before_list, $madame_monsieur;
//		global $nb_1ere_page,$nb_par_page,$taille_bloc_expl,$debut_expl_1er_page,$debut_expl_page;
//		global $marge_page_gauche,$marge_page_droite,$largeur_page,$hauteur_page,$format_page;
//
//		$this->infos_biblio($f_loc);
//		
//		// la formule de politesse du bas (le signataire)
//		$var = "pdflettreresa_fdp";
//		global $$var;
//		eval ("\$fdp=\"".$$var."\";");
//		
//		// le texte après la liste des ouvrages en résa
//		$var = "pdflettreresa_after_list";
//		global $$var;
//		eval ("\$after_list=\"".$$var."\";");
//		
//		// la position verticale limite du texte after_liste (si >, saut de page et impression)
//		$var = "pdflettreresa_limite_after_list";
//		global $$var;
//		$limite_after_list = $$var;
//				
//		// le texte avant la liste des ouvrges en réservation
//		$var = "pdflettreresa_before_list";
//		global $$var;
//		eval ("\$before_list=\"".$$var."\";");
//		
//		// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
//		$var = "pdflettreresa_madame_monsieur";
//		global $$var;
//		eval ("\$madame_monsieur=\"".$$var."\";");
//		
//		// le nombre de blocs notices à imprimer sur la première page
//		$var = "pdflettreresa_nb_1ere_page";
//		global $$var;
//		$nb_1ere_page = $$var;
//		
//		// le nombre de blocs notices à imprimer sur les pages suivantes
//		$var = "pdflettreresa_nb_par_page";
//		global $$var;
//		$nb_par_page = $$var;
//		
//		// la taille d'un bloc notices 
//		$var = "pdflettreresa_taille_bloc_expl";
//		global $$var;
//		$taille_bloc_expl = $$var;
//		
//		// la position verticale du premier bloc notice sur la première page
//		$var = "pdflettreresa_debut_expl_1er_page";
//		global $$var;
//		$debut_expl_1er_page = $$var;
//		
//		// la position verticale du premier bloc notice sur les pages suivantes
//		$var = "pdflettreresa_debut_expl_page";
//		global $$var;
//		$debut_expl_page = $$var;
//		
//		// la marge gauche des pages
//		$var = "pdflettreresa_marge_page_gauche";
//		global $$var;
//		$marge_page_gauche = $$var;
//		
//		// la marge droite des pages
//		$var = "pdflettreresa_marge_page_droite";
//		global $$var;
//		$marge_page_droite = $$var;
//		
//		// la largeur des pages
//		$var = "pdflettreresa_largeur_page";
//		global $$var;
//		$largeur_page = $$var;
//		
//		// la hauteur des pages
//		$var = "pdflettreresa_hauteur_page";
//		global $$var;
//		$hauteur_page = $$var;
//		
//		// le format des pages
//		$var = "pdflettreresa_format_page";
//		global $$var;
//		$format_page = $$var;
//	
//		$taille_doc=array($largeur_page,$hauteur_page);
//		
//		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
//		$ourPDF->Open();
//		lettre_resa_par_lecteur($id_empr) ;
//
//		return $ourPDF;
//	}
	
	function infos_biblio($location_biblio=0) {
		global $dbh;
		global $pmb_lecteurs_localises;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;

		if ($pmb_lecteurs_localises) {
			if (!$location_biblio) {
				global $deflt2docs_location;
				$location_biblio = $deflt2docs_location;
			}
			$query = "select name, adr1,adr2,cp,town,state,country,phone,email,website,logo from docs_location where idlocation=".$location_biblio;
			$res = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($res) == 1) {
				$row = pmb_mysql_fetch_object($res);
				$biblio_name = $row->name;
				$biblio_adr1 = $row->adr1;
				$biblio_adr2 = $row->adr2;
				$biblio_cp = $row->cp;
				$biblio_town = $row->town;
				$biblio_state = $row->state;
				$biblio_country = $row->country;
				$biblio_phone = $row->phone;
				$biblio_email = $row->email;
				$biblio_website = $row->website;
				$biblio_logo = $row->logo;
			}	
		} else {
			/*** Informations provenant des paramètres généraux - on ne parle donc pas de multi-localisations **/
			// nom de la structure
			$var = "opac_biblio_name";
			global $$var;
			eval ("\$biblio_name=\"".$$var."\";");
		
			// logo de la structure
			$var = "opac_logo";
			global $$var;
			eval ("\$biblio_logo=\"".$$var."\";");
		
			// adresse principale
			$var = "opac_biblio_adr1";
			global $$var;
			eval ("\$biblio_adr1=\"".$$var."\";");
			
			// adresse secondaire
			$var = "opac_biblio_adr2";
			global $$var;
			eval ("\$biblio_adr2=\"".$$var."\";");
			
			// code postal
			$var = "opac_biblio_cp";
			global $$var;
			eval ("\$biblio_cp=\"".$$var."\";");
			
			// ville
			$var = "opac_biblio_town";
			global $$var;
			eval ("\$biblio_town=\"".$$var."\";");
			
			// Etat
			$var = "opac_biblio_state";
			global $$var;
			eval ("\$biblio_state=\"".$$var."\";");
			
			// pays
			$var = "opac_biblio_country";
			global $$var;
			eval ("\$biblio_country=\"".$$var."\";");
			
			// telephone
			$var = "opac_biblio_phone";
			global $$var;
			eval ("\$biblio_phone=\"".$$var."\";");
			
			// adresse mail
			$var = "opac_biblio_email";
			global $$var;
			eval ("\$biblio_email=\"".$$var."\";");
			
			//site web
			$var = "opac_biblio_website";
			global $$var;
			eval ("\$biblio_website=\"".$$var."\";");
		}
	}
}




?>