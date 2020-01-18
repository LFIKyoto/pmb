<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret.inc.php,v 1.157 2019-08-21 14:40:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($confirm_pret)) $confirm_pret = '';
if(!isset($id_notice)) $id_notice = 0; else $id_notice += 0;
if(!isset($id_bulletin)) $id_bulletin = 0; else $id_bulletin += 0;
if(!isset($id_expl)){
	$id_expl = 0; 
}elseif(!is_array($id_expl)){
	$id_expl += 0;
}
if(!isset($serialcirc_action)) $serialcirc_action = '';
if(!isset($cb_doc)) $cb_doc = ''; else $cb_doc = trim($cb_doc);

require_once ("$class_path/emprunteur.class.php");
require_once ("$class_path/serial_display.class.php");
require_once ("$class_path/quotas.class.php");
require_once ("$class_path/comptes.class.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/expl.class.php");
require_once("$class_path/transfert.class.php");
require_once($class_path."/ajax_pret.class.php");
require_once("$class_path/groupexpl.class.php");
require_once("$class_path/resa_planning.class.php");
require_once("$class_path/pret_parametres_perso.class.php");
require_once($class_path.'/event/events/event_loan.class.php');
require_once($class_path.'/audit.class.php');
require_once($class_path.'/expl.class.php');
require_once($class_path.'/pret.class.php');
require_once("$base_path/circ/pret_func.inc.php");

if(!isset($confirm)) $confirm = '';
if(!isset($expl_todo)) $expl_todo = '';
if(!isset($quota)) $quota = '';
if(!isset($pret_arc)) $pret_arc='';

$affichage = "";
$warning_text='';
$dispo_text='';
$is_doc_group = 0;
$information_text = '';
$resarc_id = '';
if (!isset($form_cb)) $form_cb = '';

$erreur_affichage = "<table border='0' cellpadding='1' style='width:100%' height='40'><tr><td style='width:30px'>&nbsp;<span></span></td>
		<td style='width:100%'>&nbsp;</td></tr></table>";
// Confirm pret rfid mode1
if($confirm_pret && $id_empr){
	$expl = new do_pret();
	if(is_array($id_expl)) {
		foreach($id_expl as $id) {
			if($id)$status= $expl->confirm_pret($id_empr, $id, $short_loan, 'gestion_rfid');
		}
	} else {
	    if($id_expl)$status = $expl->confirm_pret($id_empr, $id_expl,$short_loan, 'gestion_rfid');
	}
	$erreur_affichage = pret::get_display_info('', $msg[384]);
	$erreur_affichage .= pret::get_display_custom_fields($id_empr,$id_expl);
	$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);

}else if (($sub == "pret_annulation") && ($id_expl)) {
	// r�cup�rer la stat ins�r�e pour la supprimer !
	$query = "select pret_arc_id from pret ";
	$query.= "where pret_idexpl = '".$id_expl."' ";
	$result = pmb_mysql_query($query);
	$stat_id = pmb_mysql_fetch_object($result) ;
	
	/**
	 * Publication d'un �venement � l'annulation du pr�t (avant suppression dans pret_archive)
	 */
	$evt_handler = events_handler::get_instance();
	$event = new event_loan("loan", "cancel_loan");
	$event->set_id_loan($stat_id->pret_arc_id);
	$evt_handler->send($event);
	
	$result = pmb_mysql_query("delete from pret_archive where arc_id='".$stat_id->pret_arc_id."' ");
	audit::delete_audit (AUDIT_PRET, $stat_id->pret_arc_id) ;

	// supprimer les valeurs de champs personnalis�s
	$p_perso=new pret_parametres_perso("pret");
	$p_perso->delete_values($stat_id->pret_arc_id);
	
	// supprimer le pr�t annul�
	$query = "delete from pret ";
	$query.= "where pret_idexpl = '".$id_expl."' ";
	$result = pmb_mysql_query($query);
	$erreur_affichage = pret::get_display_info('', str_replace('!!cb_expl!!', $cb_doc, $msg[607]));
	$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
} else {
	//Si il y a un emprunteur
	if ($id_empr) {
		// V�rification id, on dispose d'un id pour l'emprunteur, donc on est en situation de pr�t
		if (emprunteur::exists($id_empr)) {
			$empr_temp = new emprunteur($id_empr, '', FALSE, 0);
			$empr_date_depassee = $empr_temp -> adhesion_depassee();
			//Si adh�sion d�pass�e
			if (!($pmb_pret_adhesion_depassee == 0 && $empr_date_depassee)) {
				//Si un exemplaire ou un code barres a �t� fourni
				if ($cb_doc || $id_expl) {
					if ($id_expl = exemplaire::get_expl_id_from_cb($cb_doc)) {

						// Gestion Antivol
						print pret::get_display_antivol($id_expl);

						//V�rification de la validit� du document
						$statut = check_document($id_expl, $id_empr);
						// check_document remonte $statut->notice_id et $statut->bulletin_id
						if ($statut->notice_id) {
							$notice_temp = new mono_display($statut->notice_id, 0);
							$titre_prete = $notice_temp->header;
						} elseif ($statut->bulletin_id) {
							$bulletin_temp = new bulletinage_display($statut->bulletin_id);
							$titre_prete = $bulletin_temp->display ;
						} else $titre_prete = "";
						$titre_prete="<b>".$titre_prete."<br />".$cb_doc."</b> $statut->tdoc_libelle $statut->location_libelle $statut->section_libelle <b>$statut->expl_cote</b>";

						if(exemplaire::is_digital($id_expl) || exemplaire::is_digital($cb_doc)){
							$erreur_affichage = pret::get_display_error($titre_prete, $msg["circ_pret_digital_expl"], 1);
						    print emprunteur::get_display_card($id_empr, $erreur_affichage);
						    print alert_sound_script();
						    exit();
						}
						
						//Y-a-t-il un quota ?
						if (!$expl_todo && $deflt_docs_location) {
							$sql = "SELECT expl_retloc FROM exemplaires where expl_retloc='".$deflt_docs_location."' and  expl_id='".$id_expl."' ";
							$req = pmb_mysql_query($sql);
							$nb = pmb_mysql_num_rows($req) ;
							if($nb)	{
								$erreur_affichage = pret::get_display_error($titre_prete, $msg["circ_pret_piege_expl_todo"], 1, 1, "&cb_doc=$cb_doc&expl_todo=1&confirm=$confirm");
								print emprunteur::get_display_card($id_empr, $erreur_affichage);
								print alert_sound_script();
								exit();
							}
						}

						//Y-a-t-il un quota ? On y passe uniquement si l'exemplaire n'est pas d�j� en pr�t
						if ((!isset($quota) || !$quota) && !($statut -> flag && ($statut -> flag & ALREADY_LOANED || $statut -> flag & ALREADY_BORROWED))) {
							$qt=check_quota($id_empr, $id_expl);
							//Si quota viol�
							if (!empty($qt)) {
								$erreur_affichage = "<hr />
								<div class='row'>
									<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>
									<div class='colonne-suite'>$titre_prete : <span class='erreur'>".$qt["MESSAGE"]."</span><br />";
								$alert_sound_list[]="critique";
								$erreur_affichage.= "<input type='button' class='bouton' value='${msg[76]}' onClick=\"document.location='./circ.php?categ=pret&id_empr=$id_empr'\" />";
								if ($qt["FORCE"]==1) {
									$quota = 1;
									$erreur_affichage.= "&nbsp;<input type='button' class='bouton' value='${msg[389]}' onClick=\"document.location='./circ.php?categ=pret&id_empr=$id_empr&cb_doc=$cb_doc&quota=$quota'\" />";
								}
								$erreur_affichage.= "</div></div><br />";
								$empr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1);
								$affichage = $empr -> fiche;
								print pmb_bidi($affichage);
								print alert_sound_script();
								exit();
							} // fin if (!empty($qt))
						} // fin if !$quota

						// Le lecteur a d�j� emprunt� ce document ?
						if (!$pret_arc && $pmb_pret_already_loaned) {
							$rqt_arch = "select arc_id from pret_archive WHERE arc_id_empr = '".$id_empr."' AND arc_expl_id = ".$id_expl." ";
							$pretarc_res=pmb_mysql_query($rqt_arch);
							if(pmb_mysql_num_rows($pretarc_res)){
								$pret_arc=1;
								$res_pret_arc = pmb_mysql_fetch_object($pretarc_res);
								$resarc_id = $res_pret_arc->resarc_id;
								$erreur_affichage = pret::get_display_error($titre_prete, $msg['pret_already_loaned_arch'], 1, 1, "&cb_doc=$cb_doc&quota=$quota&pret_arc=1");
								print emprunteur::get_display_card($id_empr, $erreur_affichage);
								print alert_sound_script();
								exit();
							}
						}
                        
						if ($statut -> flag && ((($statut -> flag & HAS_NOTE) || ($statut -> flag & IS_GROUP) || ($statut -> flag & NON_PRETABLE) || ($statut -> flag & HAS_RESA_FALSE)) || ($statut -> flag & HAS_RESA_PLANNED_FALSE) || ($statut -> flag & IS_TRUSTED)) && !($statut -> flag & ALREADY_LOANED) && !($statut -> flag & ALREADY_BORROWED) ) {
							if (!$confirm) {
								// mettre ici les routines confirmation
								if($is_doc_group){
									$information_text.= $groupexpl->get_confirm_form($cb_doc);
									if($groupexpl->is_doc_header($cb_doc))	$serious = FALSE;
									else $serious = TRUE;
								}
								// l'exemplaire a une note
								if ($statut -> flag & HAS_NOTE) {
									// l'exemplaire a une note attach�e
									$warning_text.= "$msg[377] : <span class='message_important'>".nl2br($statut -> note)."</span>&nbsp;";
									$serious = FALSE;
								}
								if ($statut -> flag & NON_PRETABLE) {
									// l'exemplaire a le statut non-pr�table
									if ($warning_text) $warning_text.= "<br />".$msg[382]." (".$statut->statut.")";
										else $warning_text.= $msg[382]." (".$statut->statut.")";
									$serious = TRUE;
									// Si transfert activ�, on v�rifie le pr�t est forcable ou non
									if($pmb_transferts_actif) {
										$transfert = new transfert();
										$statut_trans=$transfert->check_pret($cb_doc);

										if($statut_trans==1) {
											//non forcable
											$erreur_affichage = pret::get_display_error($titre_prete, $transfert->check_pret_error_message, 1);
											print emprunteur::get_display_card($id_empr, $erreur_affichage);
											print alert_sound_script();
											exit();
										} elseif($statut_trans==2)	{
											// for�able
											$warning_text.= "<br />".$transfert->check_pret_error_message;
										}
									}
								}
								if ($statut -> flag & HAS_RESA_FALSE) {
									// le document est r�serv� pour un autre lecteur
									if ($warning_text) $warning_text.= "<br />".$msg[383]." : <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reservataire_empr_cb)."'>".$reservataire_nom_prenom."</a>";
										else $warning_text.= $msg[383]." : <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reservataire_empr_cb)."'>".$reservataire_nom_prenom."</a>";
									$serious = TRUE;
								}
								if ($statut -> flag & HAS_RESA_PLANNED_FALSE) {
									// le document � des pr�visions
									if ($warning_text) $warning_text.= "<br />";
									$warning_text.= "<img src='".get_url_icon('plus.gif')."' class='img_plus'
										onClick=\"
										var elt=document.getElementById('erreur-child');
										var vis=elt.style.display;
										if (vis=='block'){
											elt.style.display='none';
											this.src='".get_url_icon('plus.gif')."';
										} else {
											elt.style.display='block';
											this.src='".get_url_icon('minus.gif')."';
										}
										\" /> ".htmlentities($msg['resa_planning_encours'], ENT_QUOTES, $charset)." <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reservataire_empr_cb)."'>".$reservataire_nom_prenom."</a><br />";

									//Affichage des pr�visions sur le document courant
									$q = "SELECT id_resa, resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_validee, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin, ";
									$q.= "resa_idempr, concat(empr_prenom, ' ',empr_nom) as resa_nom, if(resa_idempr!='".$id_empr."', 0, 1) as resa_same ";
									$q.= "FROM resa_planning left join empr on resa_idempr=id_empr ";
									$q.= "where resa_idnotice=$statut->notice_id and resa_idbulletin=$statut->bulletin_id ";
									// En fonction de la localisation de l'exemplaire courant si les pr�visions sont localis�es
									if ($pmb_location_resa_planning) {
										$q.= "and resa_loc_retrait in (0,".$statut->expl_location.") ";
									}
									$r = pmb_mysql_query($q);
									if (pmb_mysql_num_rows($r)) {
										$warning_text.= "<div id='erreur-child' class='erreur-child'>";
										while ($resa = pmb_mysql_fetch_array($r)) {
											$id_resa = $resa['id_resa'];
											$resa_idempr = $resa['resa_idempr'];
											$resa_idnotice = $resa['resa_idnotice'];
											$resa_idbulletin = $resa['resa_idbulletin'];
											$resa_date = $resa['resa_date'];
											$resa_date_debut = $resa['resa_date_debut'];
											$resa_date_fin = $resa['resa_date_fin'];
											$resa_validee = $resa['resa_validee'];
											$resa_nom = $resa['resa_nom'];
											$resa_same = $resa['resa_same'];
											if ($resa_idempr==$id_empr) {
												$warning_text.= "<b>".htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;</b>";
											} else {
												$warning_text.= htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;";
											}
											$warning_text.= " &gt;&gt; <b>".$msg['resa_planning_date_debut']."</b> ".formatdate($resa_date_debut)."&nbsp;<b>".$msg['resa_planning_date_fin']."</b> ".formatdate($resa_date_fin)."&nbsp;" ;
											if (!$resa['perimee']) {
												if ($resa['resa_validee'])  $warning_text.= " ".$msg['resa_validee'] ;
													else $warning_text.= " ".$msg['resa_attente_validation']." " ;
											} else  {
												$warning_text.= " ".$msg['resa_overtime']." " ;
											}
											$warning_text.= "<br />" ;
										} //while
										$warning_text.= "</div>";
									} // if (pmb_mysql_num_rows($r))
									$serious = TRUE;
								} //if ($statut -> flag & HAS_RESA_PLANNED_FALSE)
								if ($statut->flag & IS_TRUSTED ) {
									// le document est monopolis�
								    $warning_text.= '<br />' . get_loan_trust_message($statut->notice_id, $statut->bulletin_id);
									$serious = TRUE;
								}
								$erreur_affichage = "<hr />
									<div class='row' >
									$information_text
									</div>
									<div class='row' >
									<div class='colonne10' ><img src='".get_url_icon('quest.png')."' /></div>
									<div class='colonne-suite'>$titre_prete : <span class='erreur' >$warning_text</span><br />";

								$alert_sound_list[]="question";
								$erreur_affichage.= "<input type='button' class='bouton' value='${msg[76]}' onClick=\"document.location='./circ.php?categ=pret&id_empr=$id_empr".(($pmb_short_loan_management==1) ? "&short_loan=$short_loan" : "")."'\" />";
								$confirm = $statut -> flag ;
								$erreur_affichage.= "&nbsp;<input type='button' class='bouton' value='${msg[389]}' onClick=\"document.location='./circ.php?categ=pret&id_empr=$id_empr&cb_doc=$cb_doc&expl_todo=$expl_todo&confirm=$confirm&quota=$quota&pret_arc=$pret_arc".(($pmb_short_loan_management==1) ? "&short_loan=$short_loan" : "")."'\" />";
								$erreur_affichage.= "&nbsp;<input class='bouton' type='button' value=\"".$msg[375]."\" onClick=\"document.location='circ.php?categ=visu_ex&form_cb_expl=".$cb_doc."';\" />";
								$erreur_affichage.= "</div></div><br />";
								$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
							} else { // else if !confirm
								// il y a eu confirmation du pr�t
								if ($statut -> flag == $confirm) {
									// ajout du pr�t
									// si transfert activ�, faire le n��essaire en cas de for�age
									if($pmb_transferts_actif) {
										$transfert = new transfert();
										$statut_trans=$transfert->check_pret($cb_doc,1);
									}
									if ($statut -> flag & HAS_RESA_GOOD) {
										// archivage resa
										$rqt_arch = "UPDATE resa_archive, resa SET resarc_pretee = 1 WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
										pmb_mysql_query($rqt_arch);
										$rqt_arch = "select resarc_id from resa_archive, resa WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
										$resarc_res=pmb_mysql_query($rqt_arch);
										$resarc = pmb_mysql_fetch_object($resarc_res);
										$resarc_id = $resarc->resarc_id;

										// suppression de la resa pour ce lecteur
										del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
									}
									if ($statut -> flag & HAS_RESA_FALSE) {
										// d�valider la resa correspondante
										if ($statut->resa_cb == $statut->expl_cb) {
											// la r�sa prioritaire avait d�j� un CB identique : il suffit de la d�valider
											$rqt_invalide_resa = "update resa set resa_date_debut='0000-00-00', resa_date_fin='0000-00-00', resa_cb='' where id_resa = '".$statut->id_resa."' " ;
											$truc_vide = pmb_mysql_query($rqt_invalide_resa) ;
										} // sinon rien � faire, la r�sa �tait valid�e avec autre chose, elle le reste
										// archivage resa
										$rqt_arch = "UPDATE resa_archive, resa SET resarc_pretee = 2 WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
										pmb_mysql_query($rqt_arch);
										$rqt_arch = "select resarc_id from resa_archive, resa WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
										$resarc_res=pmb_mysql_query($rqt_arch);
										$resarc = pmb_mysql_fetch_object($resarc_res);
										$resarc_id = $resarc->resarc_id;
										del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
									}
									del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
									add_pret($id_empr, $id_expl, $cb_doc, $resarc_id, $short_loan);
									$information_loan_trust_management_not_blocking = '';
									if (isset($loan_trust_management_not_blocking) && $loan_trust_management_not_blocking) {
									    $information_loan_trust_management_not_blocking = "<span class='erreur'>" . get_loan_trust_message($statut->notice_id, $statut->bulletin_id) . '</span><br/>';
									}	
									$information_group = "";
									if($pmb_pret_groupement){
										if($id_group=groupexpls::get_group_expl($cb_doc)){
											// ce document appartient � un groupe
											$is_doc_group=1;
											$groupexpl=new groupexpl($id_group);
											$information_text.= $groupexpl->get_confirm_form($cb_doc);
											$information_group= $groupexpl->get_confirm_form($cb_doc);
											//	$statut->flag+=IS_GROUP; client ne veut pas de comfirmation
										}
									}
									$erreur_affichage = $information_loan_trust_management_not_blocking . $information_group."<hr />
										<div class='row'>
										<div class='colonne10'><img src='".get_url_icon('info.png')."' /></div>
										<div class='colonne-suite'>".$titre_prete." : <span class='erreur'>".$msg[384]."</span><br />
										";
									$erreur_affichage .= pret::get_display_custom_fields($id_empr,$id_expl);
									$alert_sound_list[]="information";
									$erreur_affichage.= "<input type='button' class='bouton' value='${msg[76]}' onClick=\"document.location='circ.php?categ=pret&sub=pret_annulation&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=".$cb_doc."&short_loan=".$short_loan."'\" />";
									$erreur_affichage.= "&nbsp;<input type='button' class='bouton' value='${msg[1300]}' onclick=\"openPopUp('./pdf.php?pdfdoc=ticket_pret&cb_doc=$cb_doc&id_empr=$id_empr', 'print_PDF')\" />";
									$erreur_affichage.= "</div></div>";
									if ($statut->expl_comment) $erreur_affichage.= "<div class='expl_comment'>".$statut->expl_comment."</div>";

									$empr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1);
									$affichage = $empr -> fiche;

									// prise en compte du param d'envoi de ticket de pr�t �lectronique
									if ($empr_electronic_loan_ticket && $param_popup_ticket) {
										electronic_ticket($id_empr, $cb_doc);
									}

									// prise en compte du param popup_ticket
									if ($param_popup_ticket == 1) {
										if(!$pmb_printer_ticket_url) {
											print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=ticket_pret&cb_doc=$cb_doc&id_empr=$id_empr', 'print_PDF');</script>";
										} else {
											$affichage.="<script type='text/javascript'>print_ticket('./ajax.php?module=circ&categ=print_pret&sub=one&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=$cb_doc');</script>";
										}
									}
								} else {
									$erreur_affichage = pret::get_display_info($titre_prete, $msg[384]);
									$erreur_affichage .= pret::get_display_custom_fields($id_empr,$id_expl);

									$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
								} // fin else if ($statut -> flag == $confirm)
							} // fin if else !confirm
						} else {
							if ($statut -> flag & ALREADY_LOANED || $statut -> flag & ALREADY_BORROWED) {
								if ($statut -> flag & ALREADY_LOANED) {
									$erreur_affichage = pret::get_display_error($titre_prete, $msg[386]);
								}
								if ($statut -> flag & ALREADY_BORROWED) {
									// Proposer de faire le retour et de refaire le pr�t 
									$pret_already_borrowed_action='';
									if($pmb_pret_already_borrowed) {
										$pret_already_borrowed_action="
											&nbsp;<input type='button' class='bouton' value='".$msg['pret_do_retour']."' onClick=\"document.location='./circ.php?categ=retour&id_empr_to_do_pret=$id_empr&form_cb_expl=$cb_doc'\" />";
									}
									$erreur_affichage = "<hr />
									<div class='row'>
									<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>
									<div class='colonne-suite'>$titre_prete : <span class='erreur'>$msg[387]</span></div>
									<input class='bouton' type='button' value=\"".$msg[375]."\" onClick=\"document.location='circ.php?categ=visu_ex&form_cb_expl=$cb_doc';\" />
									".$pret_already_borrowed_action."									
									</div><br />";
									$alert_sound_list[]="critique";
								}
								$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
							} else {
								if ($statut -> flag && ($statut -> flag & HAS_RESA_GOOD)) {
									// archivage resa
									$rqt_arch = "UPDATE resa_archive, resa SET resarc_pretee = 1 WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
									pmb_mysql_query($rqt_arch);
									$rqt_arch = "select resarc_id from resa_archive, resa WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";
									$resarc_res=pmb_mysql_query($rqt_arch);
									$resarc = pmb_mysql_fetch_object($resarc_res);
									$resarc_id = $resarc->resarc_id;
									// suppression de la resa pour ce lecteur
									del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
								} else {
									$resarc_id = 0;
								}
								// ajout du pr�t
								del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
								add_pret($id_empr, $id_expl, $cb_doc,$resarc_id,$short_loan);
								// mise � jour de l'affichage								
								$information_loan_trust_management_not_blocking = '';
								if (isset($loan_trust_management_not_blocking) && $loan_trust_management_not_blocking) {
								    $information_loan_trust_management_not_blocking = "<span class='erreur'>" . get_loan_trust_message($statut->notice_id, $statut->bulletin_id) . '</span><br/>';
								}								
								$information_group = "";
								if($pmb_pret_groupement){
									if($id_group=groupexpls::get_group_expl($cb_doc)){
										// ce document appartient � un groupe
										$is_doc_group=1;
										$groupexpl=new groupexpl($id_group);
										$information_text.= $groupexpl->get_confirm_form($cb_doc);
										$information_group= $groupexpl->get_confirm_form($cb_doc);
										//	$statut->flag+=IS_GROUP; client ne veut pas de comfirmation
									}
								}
								// ajout du bouton d'annulation violente
								$erreur_affichage = $information_loan_trust_management_not_blocking . $information_group."<hr />
									<div class='row'>
									<div class='colonne10'><img src='".get_url_icon('info.png')."' /></div>
									<div class='colonne-suite'>$titre_prete : <span class='erreur'>".$msg[384]."</span><br />
									";
								$erreur_affichage .= pret::get_display_custom_fields($id_empr,$id_expl);
								if($pmb_play_pret_sound)$alert_sound_list[]="information";
								$erreur_affichage.= "<input type='button' class='bouton' value='${msg[76]}' onClick=\"document.location='circ.php?categ=pret&sub=pret_annulation&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=".$cb_doc."&short_loan=".$short_loan."'\" />";

								if($pmb_printer_ticket_url) $erreur_affichage.="&nbsp;<a href='#' onclick=\"print_ticket('./ajax.php?module=circ&categ=print_pret&sub=one&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=$cb_doc'); return false;\"><img src='".get_url_icon('print.gif')."' alt='Imprimer...' title='Imprimer...' class='align_middle' border='0'></a>";
								else $erreur_affichage.= "&nbsp;<input type='button' class='bouton' value='".$msg["imprimer"]."' onclick=\"openPopUp('./pdf.php?pdfdoc=ticket_pret&cb_doc=$cb_doc&id_empr=$id_empr', 'print_PDF')\" /><a href='#' onclick=\"printer_jzebra_print_ticket('./ajax.php?module=circ&categ=zebra_print_pret&sub=one&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=$cb_doc'); return false;\"><img src='".get_url_icon('print.gif')."' alt='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' class='align_middle' border='0'></a>";
								$erreur_affichage.= "</div></div>";
								if ($statut->expl_comment) $erreur_affichage.= "<div class='expl_comment'>".$statut->expl_comment."</div>";

								$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
								// prise en compte du param d'envoi de ticket de pr�t �lectronique
								if ($empr_electronic_loan_ticket && $param_popup_ticket) {
									electronic_ticket($id_empr, $cb_doc);
								}

								// prise en compte du param popup_ticket
								if ($param_popup_ticket == 1) {
									if(!$pmb_printer_ticket_url) {
										if ($pmb_printer_name) {
											$affichage.= "<script type='text/javascript'>printer_jzebra_print_ticket('./ajax.php?module=circ&categ=zebra_print_pret&sub=one&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=$cb_doc');</script>";
										} else {
											print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=ticket_pret&cb_doc=$cb_doc&id_empr=$id_empr', 'print_PDF');</script>";
										}
									} else {
										$affichage.= "<script type='text/javascript'>print_ticket('./ajax.php?module=circ&categ=print_pret&sub=one&id_empr=".$id_empr."&id_expl=".$id_expl."&cb_doc=$cb_doc');</script>";
									}
								}
							} // fin else if ($statut -> flag & ALREADY_LOANED || $statut -> flag & ALREADY_BORROWED) {
						} // fin de quoi ???
					} else { // pas d'exemplaire avec ce code-barre
						$erreur_affichage = pret::get_display_error("<b>".$cb_doc."</b>", $msg[367]);
						// on a un code-barres, est-ce un cb empr ?
						$query_empr = "select id_empr, empr_cb from empr where empr_cb='".$cb_doc."' ";
						$result_empr = pmb_mysql_query($query_empr);
						if(pmb_mysql_num_rows($result_empr)) {
							$erreur_affichage.="<script type=\"text/javascript\">document.location='./circ.php?categ=pret&form_cb=$cb_doc'</script>";
						}
						$alert_sound_list[]="critique";
						$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
					}
				} else { // aucun $id_expl ni de $cd_doc
					$erreur_affichage = pret::get_display_error();
					$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
				}
			} else { // date adh�sion d�pass�e et ici on bloque !!!
				$erreur_affichage = pret::get_display_error('', $msg['pret_impossible_adhesion']);
				$affichage = emprunteur::get_display_card($id_empr, $erreur_affichage);
			}
		} else {
			// afficher 'lecteur inconnu'
			$erreur_affichage = pret::get_display_error('', $msg[388]);
			print $erreur_affichage;
		}
	} else { // pas d'idempr
		$query = "select id_empr as id from empr where empr_cb='$form_cb' ";
		$result = pmb_mysql_query($query);
		$id = @ pmb_mysql_result($result, '0', 'id');
		if (($id) && ($form_cb)) {
			$erreur_affichage = pret::get_display_error();
			if ($id_notice || $id_bulletin) {
				if ($type_resa) {
					echo "<script type='text/javascript'> parent.location.href='./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin'; </script>";
				} else {
					echo "<script type='text/javascript'> parent.location.href='./circ.php?categ=resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin'; </script>";
				}
			} else {
				if ($serialcirc_action == "delete") {
					$serialcirc_empr = new serialcirc_empr($id);
					$msgs = $serialcirc_empr->unsbuscribe($serialcirc);
					if(is_array($msgs['errors']) && count($msgs['errors'])){
						$affichage.= return_error_message($msg['540'], implode("<br />",$msgs['errors']));
						$affichage.=" <div class='row'>&nbsp;</div>";
					}
				} else if (($serialcirc_action == "tr") || ($serialcirc_action == "dup")) {
					$serialcirc_empr = new serialcirc_empr($id);
					$msgs = $serialcirc_empr->forward($serialcirc, $serialcirc_new_empr, ($serialcirc_action == 'dup'));
					if(is_array($msgs['messages']) && count($msgs['messages'])){
						$affichage.="
						<table>
							<tr>
								<td><img src='".get_url_icon('idea.gif')."' class='align_left'></td>
								<td><p><strong>". implode("<br>",$msgs['messages'])."</strong></p></td>
							</tr>
						</table>";
						$affichage.=" <div class='row'>&nbsp;</div>";
					}if(is_array($msgs['errors']) && count($msgs['errors'])){
						$affichage.= return_error_message($msg['540'], implode("<br>",$msgs['errors']));
						$affichage.=" <div class='row'>&nbsp;</div>";
					}
				}
				$affichage.= emprunteur::get_display_card($id, $erreur_affichage);
			}
		} else {
			include ('./circ/empr/empr_list.inc.php');
		}
	} /* fin if else ajout� par ER pour fonction annulation */
}
//Comme dans $affichage on met la fiche de l'emprunteur ($affichage = $empr -> fiche) � aucun moment !!voir_sugg!! ne peut �tre encore pr�sent
if(SESSrights & ACQUISITION_AUTH){
	global $nb_per_page;
	$ori = ($id_empr ? $id_empr : $id);
	$req = "select count(id_suggestion) as nb, sugg_location from suggestions, suggestions_origine where num_suggestion=id_suggestion and origine='".$ori."' and type_origine='1'  ";
	$res=pmb_mysql_query($req);
	$btn_sug = "";
	if($res && pmb_mysql_num_rows($res)){
		$sug = pmb_mysql_fetch_object($res);
		if($sug->nb){
			$btn_sug = "<input type='button' class='bouton' id='see_sug' name='see_sug' value='".$msg['acquisition_lecteur_see_sugg']."' onclick=\"document.location='./acquisition.php?categ=sug&action=list&user_id[]=".$ori."&user_statut[]=1&sugg_location_id=".$sug->sugg_location."' \" />";
		}
	}
	$affichage = str_replace('!!voir_sugg!!',$btn_sug,$affichage);
}else{
	$affichage = str_replace('!!voir_sugg!!',"",$affichage);
}
//print $erreur_affichage ;
print pmb_bidi($affichage);

function get_loan_trust_message($notice_id, $bulletin_id) {
    global $pmb_loan_trust_management, $msg;
    
    if ($notice_id || $bulletin_id) {
        if ($notice_id) {
            $qd = "select count(*) from exemplaires join docs_statut on idstatut=expl_statut and pret_flag=1 where expl_notice=".$notice_id;
        } else if ($bulletin_id) {
            $qd = "select count(*) from exemplaires join docs_statut on idstatut=expl_statut and pret_flag=1 where expl_bulletin=".$bulletin_id;
        }
        $rd = pmb_mysql_query($qd);
        if (pmb_mysql_num_rows($rd)) {
            $nd = pmb_mysql_result($rd,0,0);
            $param = explode(',', $pmb_loan_trust_management);
            $loan_trust_management = $param[0];
            return sprintf("<br />".$msg['loan_trust_warning'],$loan_trust_management, $nd);
        }
    }
    return '';
}

// <------------- check_quota --------------->
//V�rifie les quotas de pr�t si activ�s
function check_quota($id_empr, $id_expl) {
	global $msg;
	global $pmb_quotas_avances, $pmb_short_loan_management, $short_loan;
	
	$error = array();
	if ($pmb_quotas_avances) {
		//Initialisation des quotas pour nombre de documents pr�tables
		if ($pmb_short_loan_management && $short_loan) {
			$qt = new quota("SHORT_LOAN_NMBR_QUOTA");
		} else {
			$qt = new quota("LEND_NMBR_QUOTA");
		}//Tableau de passage des param�tres
		$struct["READER"] = $id_empr;
		$struct["EXPL"] = $id_expl;
		$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
		$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
		//Test du quota pour l'exemplaire et l'emprunteur
		if ($qt -> check_quota($struct)) {
			//Si erreur, r�cup�ration du message et peut-on forcer ou non ?
			$error["MESSAGE"] = $qt -> error_message;
			$error["FORCE"] = $qt -> force;
		} else
			$error = array();
	}
	return $error;
}


// ajoute le pr�t en table
function add_pret($id_empr, $id_expl, $cb_doc,$resarc_id=0,$short_loan=0) {
	global $msg;
	global $pmb_quotas_avances, $pmb_utiliser_calendrier;
	global $pmb_gestion_financiere,$pmb_gestion_tarif_prets;
	global $include_path,$lang;
	global $deflt2docs_location ;
	global $pmb_pret_date_retour_adhesion_depassee;
	global $pmb_short_loan_management;
	global $pmb_transferts_actif;
	global $pmb_resa_planning;
	
	$resarc_id+=0;
	
	/* on pr�pare la date de d�but*/
	$pret_date = today();

	$duree_pret=0;
	// calcul de la duree du pret si la date de fin est definie par les previsions
	if($resarc_id && $pmb_resa_planning==1) {
		$q = 'select datediff(resarc_fin,"'.$pret_date.'") from resa_archive where resarc_id ='.$resarc_id.' and resarc_resa_planning_id_resa!=0 limit 1';
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$duree_pret = pmb_mysql_result($r,0,0);
		}
	}
	if(!$duree_pret) {	
		/* on cherche la dur�e du pr�t */
		if ($pmb_short_loan_management && $short_loan) {
			if($pmb_quotas_avances) {
				//Initialisation de la classe
				$qt=new quota("SHORT_LOAN_TIME_QUOTA");
				$struct["READER"]=$id_empr;
				$struct["EXPL"]=$id_expl;
				$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
				$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
				$duree_pret=$qt->get_quota_value($struct);
				if ($duree_pret==-1) $duree_pret=0;
			} else {
				$query = "SELECT short_loan_duration as duree_pret";
				$query.= " FROM exemplaires, docs_type";
				$query.= " WHERE expl_id='".$id_expl;
				$query.= "' and idtyp_doc=expl_typdoc LIMIT 1";
				$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires ".$query);
				$expl_properties = pmb_mysql_fetch_object($result);
				$duree_pret = $expl_properties -> duree_pret;
			}
		} else {
			if($pmb_quotas_avances) {
				//Initialisation de la classe
				$qt=new quota("LEND_TIME_QUOTA");
				$struct["READER"]=$id_empr;
				$struct["EXPL"]=$id_expl;
				$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
				$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
				$duree_pret=$qt->get_quota_value($struct);
				if ($duree_pret==-1) $duree_pret=0;
			} else {
					$query = "SELECT duree_pret";
					$query.= " FROM exemplaires, docs_type";
					$query.= " WHERE expl_id='".$id_expl;
					$query.= "' and idtyp_doc=expl_typdoc LIMIT 1";
					$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires ".$query);
					$expl_properties = pmb_mysql_fetch_object($result);
					$duree_pret = $expl_properties -> duree_pret;
			}
		}
	}
	// calculer la date de retour pr�vue, tenir compte de la date de fin d'adh�sion
	if (!$duree_pret) {
		$duree_pret='0' ;
	}
	if($pmb_pret_date_retour_adhesion_depassee) {
		$rqt_date = "select empr_date_expiration,if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),0,1) as pret_depasse_adhes, date_add('".$pret_date."', INTERVAL '$duree_pret' DAY) as date_retour from empr where id_empr='".$id_empr."'";
	} else {
		$rqt_date = "select empr_date_expiration,if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),0,1) as pret_depasse_adhes, if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),empr_date_expiration) as date_retour from empr where id_empr='".$id_empr."'";
	}
	$resultatdate = pmb_mysql_query($rqt_date) or die(pmb_mysql_error()."<br /><br />$rqt_date<br /><br />");
	$res = pmb_mysql_fetch_object($resultatdate) ;
	$date_retour = $res->date_retour ;
	$pret_depasse_adhes = $res->pret_depasse_adhes ;
	$empr_date_expiration= $res->empr_date_expiration;

	if ($pmb_utiliser_calendrier) {
		if (($pret_depasse_adhes==0) || $pmb_pret_date_retour_adhesion_depassee) {
			$rqt_date = "select date_ouverture from ouvertures where ouvert=1 and to_days(date_ouverture)>=to_days('$date_retour') and num_location=$deflt2docs_location order by date_ouverture ";
			$resultatdate=pmb_mysql_query($rqt_date);
			if(pmb_mysql_num_rows($resultatdate)) {
				$res = pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $date_retour=$res->date_ouverture ;
			}
		} else {
			$rqt_date = "select date_ouverture from ouvertures where date_ouverture>=sysdate() and ouvert=1 and to_days(date_ouverture)<=to_days('$date_retour') and num_location=$deflt2docs_location order by date_ouverture DESC";
			$resultatdate=pmb_mysql_query($rqt_date);
			if(pmb_mysql_num_rows($resultatdate)) {
				$res = pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $date_retour=$res->date_ouverture ;
			}
		}
		// Si la date_retour, calcul�e ci-dessus d'apr�s le calendrier, d�passe l'adh�sion, alors que c'est interdit,
		// la date de retour doit etre le dernier jour ouvert
		if(!$pmb_pret_date_retour_adhesion_depassee){
			$rqt_date = "SELECT DATEDIFF('$empr_date_expiration','$date_retour')as diff";
			$resultatdate=pmb_mysql_query($rqt_date);
			$res=@pmb_mysql_fetch_object($resultatdate) ;
			if ($res->diff<0) {
				$rqt_date = "select date_ouverture from ouvertures where date_ouverture>=sysdate() and ouvert=1 and to_days(date_ouverture)<=to_days('$empr_date_expiration') and num_location=$deflt2docs_location order by date_ouverture DESC";
				$resultatdate=pmb_mysql_query($rqt_date);
				$res=@pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $date_retour=$res->date_ouverture ;
			}
		}
	}

	// ins�rer le pr�t
	$query = "INSERT INTO pret SET ";
	$query.= "pret_idempr = '".$id_empr."', ";
	$query.= "pret_idexpl = '".$id_expl."', ";
	$query.= "pret_date   = sysdate(), ";
	$query.= "pret_retour = '$date_retour', ";
	$query.= "retour_initial = '$date_retour', ";
	$query.= "short_loan_flag = ".(($pmb_short_loan_management && $short_loan)?"'1'":"'0'");
	pmb_mysql_query($query) or die(pmb_mysql_error()."<br />can't INSERT into pret".$query);

	// ins�rer la trace en stat, r�cup�rer l'id et le mettre dans la table des pr�ts pour la maj ult�rieure
	$stat_avant_pret = pret_construit_infos_stat ($id_expl) ;
	$stat_avant_pret->source_device = 'gestion_standard';
	$stat_id = stat_stuff ($stat_avant_pret) ;
	$query = "update pret SET pret_arc_id='$stat_id' where ";
	$query.= "pret_idempr = '".$id_empr."' and ";
	$query.= "pret_idexpl = '".$id_expl."' ";
	pmb_mysql_query($query) or die("can't update pret for stats ".$query);
	audit::insert_creation (AUDIT_PRET, $stat_id) ;

	//enregistrer les champs perso pret
	$p_perso=new pret_parametres_perso("pret");
	$p_perso->rec_fields_perso($stat_id);
	
	if($resarc_id){
		$rqt_arch = "UPDATE resa_archive SET resarc_arcpretid = $stat_id WHERE resarc_id = '".$resarc_id."' ";
		pmb_mysql_query($rqt_arch);
	}
	$query = "update exemplaires SET ";
	$query.= "last_loan_date = sysdate() ";
	$query.= "where expl_id= '".$id_expl."' ";
	pmb_mysql_query($query) or die("can't update last_loan_date in exemplaires : ".$query);

	$query = "update exemplaires SET ";
	$query.= "expl_retloc=0 ";
	$query.= "where expl_id= '".$id_expl."' ";
	pmb_mysql_query($query) or die("can't update expl_retloc in exemplaires : ".$query);

	$query = "update empr SET ";
	$query.= "last_loan_date = sysdate() ";
	$query.= "where id_empr= '".$id_empr."' ";
	pmb_mysql_query($query) or die("can't update last_loan_date in empr : ".$query);

	$query = "delete from resa_ranger ";
	$query .= "where resa_cb='".$cb_doc."'";
	pmb_mysql_query($query) or die("can't delete cb_doc in resa_ranger : ".$query);


	//D�bit du compte lecteur si n�cessaire
	if (($pmb_gestion_financiere)&&($pmb_gestion_tarif_prets)) {
		$tarif_pret=0;
		switch ($pmb_gestion_tarif_prets) {
			case 1:
				//Gestion simple
				$query = "SELECT tarif_pret";
				$query.= " FROM exemplaires, docs_type";
				$query.= " WHERE expl_id='".$id_expl;
				$query.= "' and idtyp_doc=expl_typdoc LIMIT 1";

				$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires ".$query);
				$expl_tarif = pmb_mysql_fetch_object($result);
				$tarif_pret = $expl_tarif -> tarif_pret;

				break;
			case 2:
				//Gestion avanc�e
				$qt_tarif=new quota("COST_LEND_QUOTA","$include_path/quotas/own/$lang/finances.xml");
				$struct["READER"]=$id_empr;
				$struct["EXPL"]=$id_expl;
				$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
				$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
				$tarif_pret=$qt_tarif->get_quota_value($struct);
				break;
		}
		$tarif_pret=$tarif_pret*1;
		if ($tarif_pret) {
			$compte_id=comptes::get_compte_id_from_empr($id_empr,3);
			if ($compte_id) {
				$cpte=new comptes($compte_id);
				$explaire = new exemplaire('',$id_expl);

				if($explaire->id_notice == 0 && $explaire->id_bulletin){
					//C'est un exemplaire de bulletin
					$bulletin = new bulletinage_display($explaire->id_bulletin);
					$titre = strip_tags($bulletin->display);
				} elseif($explaire->id_notice) {
					$notice = new mono_display($explaire->id_notice);
					$titre = strip_tags($notice->header);
				}
				$libelle_expl = (strlen($titre)>15)?$explaire->cb." ".$titre:$explaire->cb." ".$titre;
				$cpte->record_transaction("",abs($tarif_pret),-1,sprintf($msg["finance_pret_expl"],$libelle_expl),0);
			}
		}
	}
	if ($pmb_transferts_actif){
		// si transferts valid� (en attente d'envoi), il faut restaurer le statut
		global $PMBuserid;
		$rqt = "SELECT id_transfert FROM transferts,transferts_demande
		where
		num_transfert=id_transfert and
		etat_demande=1 and num_expl =$id_expl and etat_transfert=0 and sens_transfert=0";
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)){
			$obj = pmb_mysql_fetch_object($res);
			$idTrans=$obj->id_transfert;
			//R�cup�ration des informations d'origine
			$rqt = "SELECT statut_origine, num_expl FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
			WHERE id_transfert=".$idTrans." AND sens_transfert=0";
			$res = pmb_mysql_query($rqt);
			$obj_data = pmb_mysql_fetch_object($res);
			//on met � jour
			$rqt = "UPDATE exemplaires SET expl_statut=".$obj_data->statut_origine." WHERE expl_id=".$obj_data->num_expl;
			pmb_mysql_query( $rqt );
		}
		// cloture les demandes de transfert pour r�sa, refus�e ou pas
		// afin de g�n�rer les transfert en automatique dans le circuit classique des r�sa
		$req=" update transferts,transferts_demande
		set etat_transfert=1 ,
		motif=CONCAT(motif,'. Cloture, car parti en pret (gestion $PMBuserid, $id_empr)')
		where
		num_transfert=id_transfert and
		(etat_demande=4 or etat_demande=0 or etat_demande=1)and
		etat_demande != 3 and etat_demande!=2 and etat_demande!=5 and
		num_expl =$id_expl and etat_transfert=0 and sens_transfert=0
		";
		pmb_mysql_query($req);
	}
	// invalidation des r�sas avec ce code-barre, au cas o�
	// $query = "update resa SET resa_cb='' where resa_cb='".$cb_doc."' ";
	// $result = @ pmb_mysql_query($query) or die("can't update resa ".$query);
	
	
	/**
	 * Publication d'un �venement � l'enregistrement du pr�t en base (pi�ges pass�s et pr�t valid� (quotas etc..) )
	 */
	$evt_handler = events_handler::get_instance();
	$event = new event_loan("loan", "add_loan");
	$event->set_id_loan($stat_id);
	$event->set_id_empr($id_empr);
	$evt_handler->send($event);

}

// efface une r�sa pour un emprunteur donn� et r�affecte le cb �ventuellement
function del_resa($id_empr, $id_notice, $id_bulletin, $cb_encours_de_pret) {
	if (!$id_empr || (!$id_notice && !$id_bulletin))
		return FALSE;

	$id_notice += 0;
	$id_bulletin += 0;
	$rqt = "select resa_cb, id_resa, resa_planning_id_resa from resa where resa_idnotice='".$id_notice."' and resa_idbulletin='".$id_bulletin."'  and resa_idempr='".$id_empr."' ";
	$res = pmb_mysql_query($rqt);
	if(pmb_mysql_num_rows($res)) {
		$obj = pmb_mysql_fetch_object($res);
		$cb_recup = $obj->resa_cb;
		$id_resa = $obj->id_resa;
	
		// suppression resa
		$rqt = "delete from resa where id_resa='".$id_resa."' ";
		pmb_mysql_query($rqt);
	
		// suppression de la pr�vision associ�e � la resa
		resa_planning::delete($obj->resa_planning_id_resa);
		
		// r�affectation du doc �ventuellement
		if ($cb_recup != $cb_encours_de_pret) {
			// les cb sont diff�rents
			if (!verif_cb_utilise($cb_recup)) {
				// le cb qui �tait affect� � la r�sa qu'on vient de supprimer n'est pas utilis�
				// on va affecter le cb_r�cup�r� � une resa non valid�e
				$res_affectation = affecte_cb($cb_recup) ;
				if (!$res_affectation && $cb_recup) {
					// cb non r�affect�, il faut transf�rer les infos de la r�sa dans la table des docs � ranger
					$rqt = "insert into resa_ranger (resa_cb) values ('".$cb_recup."') ";
					pmb_mysql_query($rqt);
				}
			}
		}
	}
	// si on delete une resa � partir d'un pr�t, on invalide la r�sa qui �tait valid�e avec le cb, mais on ne change pas les dates, �a sera fait par affect_cb
	$rqt_invalide_resa = "update resa set resa_cb='' where resa_cb='".$cb_encours_de_pret."' " ;
	pmb_mysql_query($rqt_invalide_resa) ;
	
	// Au cas o� il reste des r�sa invalid�es par resa_cb, on leur colle les dates comme il faut...
	$rqt_invalide_resa = "update resa set resa_date_debut='0000-00-00', resa_date_fin='0000-00-00' where resa_cb='' " ;
	pmb_mysql_query($rqt_invalide_resa) ;
	return TRUE;
}