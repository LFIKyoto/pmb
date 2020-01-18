<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relance.inc.php,v 1.102.2.2 2019-10-25 14:52:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path;
global $empr, $act, $progress_bar;
global $pmb_lecteurs_localises, $pmb_utiliser_calendrier;
global $empr_sort_rows, $empr_show_rows, $empr_filter_rows;
global $deflt2docs_location, $relance_solo;

require_once($include_path."/mail.inc.php") ;
require_once ($include_path."/mailing.inc.php");
require_once ("$include_path/notice_authors.inc.php");  

//Gestion des relances
require_once($class_path."/relance.class.php");

function send_mail($id_empr, $relance) {
    mail_reader_loans_late_relance::set_niveau_relance($relance);
    $mail_reader_loans_late_relance = new mail_reader_loans_late_relance();
    $mail_reader_loans_late_relance->send_mail($id_empr);
    return true;
}

function print_relance($id_empr,$mail=true) {
	global $mailretard_priorite_email, $mailretard_priorite_email_2, $mailretard_priorite_email_3;
	global $dbh, $msg, $pmb_gestion_financiere, $pmb_gestion_amende;
	global $mail_sended;
	
	$mail_sended=0;
	$not_mail=0;
	if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
		$req="delete from cache_amendes where id_empr=".$id_empr;
		pmb_mysql_query($req);
		$amende=new amende($id_empr);
		$level=$amende->get_max_level();
		$niveau_min=$level["level_min"];
		$id_expl=$level["level_min_id_expl"];
		$total_amende = $amende->get_total_amendes();
	}
	
	//Si mail de rappel affecté au groupe, on envoi au responsable
	$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=$id_empr and resp_groupe and mail_rappel limit 1";
	$res=pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res) > 0) {
		$requete="select empr_mail from empr where id_empr='".pmb_mysql_result($res,0,1)."'";
		$res=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($res)) {
			list($empr_mail)=pmb_mysql_fetch_row($res);
		}
	} else {
		$requete="select empr_mail from empr where id_empr=$id_empr";
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			list($empr_mail)=pmb_mysql_fetch_row($resultat);
		}
	}
	
	if ($niveau_min) {
		//Si c'est un mail
		//JP 05/06/2017 : je passe par un flag car l'imbrication de conditions se complique...
		$flag_print=false;
		if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($empr_mail))&&( ($niveau_min<3)||($mailretard_priorite_email_3) )&&($mail)) {
			$flag_print=true;
			if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
				//On force en lettre
				$flag_print=false;
			}
		}
		
		if ($flag_print) {
			if (send_mail($id_empr,$niveau_min)) {
				$requete="update pret set printed=1 where pret_idexpl=".$id_expl;
				pmb_mysql_query($requete,$dbh);		
				$mail_sended=1;			
			}
		} else {
			$requete="update pret set printed=2 where pret_idexpl=".$id_expl;
			pmb_mysql_query($requete,$dbh);
			$not_mail=1;
		}
	}
	$req="delete from cache_amendes where id_empr=".$id_empr;
	pmb_mysql_query($req);
	//On loggue les infos de la lettre
	$niveau_courant = $niveau_min;
	
	if($niveau_courant){
		
		$niveau_suppose = $level["level_normal"];
		$cpt_id=comptes::get_compte_id_from_empr($id_empr,2);
		$cpt=new comptes($cpt_id);
		$solde=$cpt->update_solde();
		$frais_relance=$cpt->summarize_transactions("","",0,$realisee=-1);
		if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
	
		$req="insert into log_retard (niveau_reel,niveau_suppose,amende_totale,frais,idempr,log_printed,log_mail) values('".$niveau_courant."','".$niveau_suppose."','".$total_amende."','".$frais_relance."','".$id_empr."', '".$not_mail."', '".$mail_sended."')";
		pmb_mysql_query($req,$dbh);		
		$id_log_ret = pmb_mysql_insert_id();

		$reqexpl = "select pret_idexpl as expl from pret where pret_retour<	CURDATE() and pret_idempr=$id_empr";
		$resexple=pmb_mysql_query($reqexpl,$dbh);
		while(($liste = pmb_mysql_fetch_object($resexple))){			
			$dates_resa_sql = " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour " ;
			$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, pret_idempr, expl_id, expl_cb,expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date!='', concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql.", " ;
			$requete.= " notices_m.tparent_id, notices_m.tnvol " ; 
			$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
			$requete.= " WHERE expl_id='".$liste->expl."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
			$res_det_expl = pmb_mysql_query($requete) ;
			$expl = pmb_mysql_fetch_object($res_det_expl);
			if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {		
				$amd = $amende->get_amende($liste->expl);
			}
			$req_ins="insert into log_expl_retard (titre,expl_id,expl_cb,date_pret,date_retour,amende,num_log_retard) values('".addslashes($expl->tit)."','".$expl->expl_id."','".$expl->expl_cb."','".$expl->pret_date."','".$expl->pret_retour."','".$amd["valeur"]."','".$id_log_ret."')";
			pmb_mysql_query($req_ins,$dbh);	
		}
	}			
	return $not_mail;
}


// Pour localiser les relances : $deflt2docs_location, $pmb_lecteurs_localises, $empr_location_id ;
$loc_filter = "";
if ($pmb_lecteurs_localises) {
	$empr_location_id = $deflt2docs_location;
	$loc_filter = "and empr_location = '".$empr_location_id."' ";
}

//Traitement avant affichage
switch ($act) {
	case 'solo':
		$id_empr=$relance_solo;
		relance::do_action($id_empr);
		break;
	case 'solo_print':
		$id_empr=$relance_solo;
		print_relance($id_empr,false);
		break;
	case 'solo_mail':
		$id_empr=$relance_solo;
		print_relance($id_empr);
		break;
	case 'valid':
		for ($i=0; $i<count($empr); $i++) {
			$id_empr=$empr[$i];
			relance::do_action($id_empr);
		}
		break;
	case 'print':
		$not_all_mail = array();
		if ($empr) {
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			$requete.=" and id_empr in (".implode(",",$empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
			$resultat=pmb_mysql_query($requete);
			$not_mail = 0;
			$mail_sended = 0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_mail = print_relance($r->id_empr);
					if (($not_mail == 1) || (!$mail_sended) ||($mailretard_priorite_email==2 && $niveau_min < 3)) { //mail_sended <=> globale
						$not_all_mail[] = $r->id_empr;
					}
				}
			}
		}

		if (count($not_all_mail) > 0) {
			print "
			<form name='print_empr_ids' action='pdf.php?pdfdoc=lettre_retard' target='lettre' method='post'>
			";		
			for ($i=0; $i<count($not_all_mail); $i++) {
				print "<input type='hidden' name='empr_print[]' value='".$not_all_mail[$i]."'/>";
			}	
			print "	<script>openPopUp('','lettre');
				document.print_empr_ids.submit();
				</script>
			</form>
			";
		}
		//Fermeture de la fenêtre d'impression si tout est parti par mail
		break;
	case 'export':
	    $not_all_mail = array();
	    if ($empr) {
	        $req="TRUNCATE TABLE cache_amendes";
	        pmb_mysql_query($req);
	        $requete = "select id_empr from empr, pret, exemplaires where 1 ";
	        $requete.=" and id_empr in (".implode(",",$empr).") ";
	        //$requete.= $loc_filter;
	        $requete.= "and pret_retour< CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
	        $resultat=pmb_mysql_query($requete);
	        while ($r=pmb_mysql_fetch_object($resultat)) {
	            $amende=new amende($r->id_empr);
	            $level=$amende->get_max_level();
	            $niveau_min=$level["level_min"];
	            $printed=$level["printed"];
	            if ((!$printed)&&($niveau_min)) {
	                $not_mail = print_relance($r->id_empr);
	                if ($not_mail == 1) {
	                    $not_all_mail[] = $r->id_empr;
	                }
	            }
	        }
	    }
		
	    if (count($not_all_mail) > 0) {
	        print "
    		<form name='print_empr_ids' action='./circ/relance/relance_export.php';' target='lettre' method='post'>
    		";		
			for ($i=0; $i<count($not_all_mail); $i++) {
				print "<input type='hidden' name='empr_export[]' value='".$not_all_mail[$i]."'/>";
			}	
			print "<script>openPopUp('','lettre');
        			document.print_empr_ids.submit();
        			</script>
        		</form>";
	    }
		//Fermeture de la fenêtre d'impression si tout est parti par mail
		break;	
	case 'raz_printed':
		$req="TRUNCATE TABLE cache_amendes";
		pmb_mysql_query($req);
		$requete="update pret set printed=0 where printed!=0";
		if ($printed_cd) {
			$requete.=" and date_relance='".stripslashes($printed_cd)."'";
		}
		pmb_mysql_query($requete);
		break;
}


echo "<h1>".$msg["relance_menu"]."&nbsp;:&nbsp;".$msg["relance_to_do"]."&nbsp;<span id='nb_relance_to_do'>&nbsp;</span></h1>";

// Juste pour la progress bar , on execute ceci:
$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
$req.= $loc_filter;
$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr";
$res=pmb_mysql_query($req);

$nb=pmb_mysql_num_rows($res);
if($nb>2){	
	$progress_bar=new progress_bar($msg["relance_progress_bar"],$nb,3);		
}

// Calendrier activé : Est-il bien paramétré sur le site de gestion par défaut des lecteurs ?
if ($pmb_utiliser_calendrier) {
	$req_date_calendrier = "select count(num_location) as nb from ouvertures where date_ouverture >=curdate() and ouvert=1 and num_location=".$deflt2docs_location;
	$res_date_calendrier = pmb_mysql_query($req_date_calendrier);
	if ($res_date_calendrier) {
		if (!pmb_mysql_result($res_date_calendrier, 0, "nb")) {
			warning("", "<span class='erreur'>".$msg["calendrier_active_and_empty"]."</span>");
		}
	}
}

$requete ="select id_empr, empr_nom, empr_prenom, empr_cb, count(pret_idexpl) as empr_nb, empr_codestat, empr_mail, libelle from empr, pret, exemplaires, empr_categ where 1 ";
$requete.= $loc_filter;
$requete.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr order by empr_nom, empr_prenom";

if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
	require_once($class_path."/filter_list.class.php");
	if ($pmb_lecteurs_localises) $localisation=",l";
	else $localisation="";
	$p_perso=new pret_parametres_perso("pret");
	$filter_p_perso = "";
	if(count($p_perso->t_fields)) {
		foreach ($p_perso->t_fields as $id=>$field) {
			if($field["FILTERS"]) {
					$filter_p_perso.= ",#p".$id;
			}
		}
	}
	$filter=new filter_list("empr","empr_list","b,n,c,cs,g","c".$localisation.",13,2,3".$filter_p_perso.($empr_filter_relance_rows?",".$empr_filter_relance_rows:""),"n,g");
	if ($pmb_lecteurs_localises) {
		$lo="f".$filter->fixedfields["l"]["ID"];
		global ${$lo};
		if (!${$lo}) {
			$tableau=array();
			$tableau[0]=$deflt2docs_location;
			${$lo}=$tableau;
		}
	}
	$filter->fixedcolumns="b,n,c";
	$filter->original_query=$requete;
	$filter->multiple=1;
	$t=array();
	$t["table"]="";
	$t["row_even"]="even";
	$t["row_odd"]="odd";
	$t["cols"][0]="";
	$filter->css=$t;
	$filter->select_original="table_filter_tempo.empr_nb,empr_mail";
	$filter->original_query="select id_empr,count(pret_idexpl) as empr_nb from empr,pret where pret_retour<CURDATE() and pret_idempr=id_empr group by empr.id_empr";
	$filter->from_original="";
	$filter->activate_filters();
	if (!$filter->error) {
		$aff_filters="<script type='text/javascript' src='./javascript/tablist.js'></script><form class='form-$current_module' id='form_filters' name='form_filters' method='post' action='".$PHP_SELF."?categ=relance&sub=todo'><h3>".$msg["filters_tris"]."</h3>";
		$aff_filters.="<div class='form-contenu'><div id=\"el1Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" name=\"imEx\" class=\"img_plus\" id=\"el1Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el1', true); return false;\">
   								<b>".$msg["filters"]."</b></div>
						<div id=\"el1Child\" style=\"margin-left:7px;display:none;\">";
		$aff_filters.=$filter->display_filters();
		$aff_filters.="</div><div class='row'></div><div id=\"el2Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" name=\"imEx\" class=\"img_plus\" id=\"el2Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el2', true); return false;\">
							<b>".$msg["tris_dispos"]."</b></div>
							<div id=\"el2Child\" style=\"margin-left:7px;display:none;\">";
		$aff_filters.=$filter->display_sort();
		$aff_filters.="</div></div><div class='row'></div><input type='submit' class='bouton' value='".$msg["empr_sort_filter_button"]."'></form>";
		$aff_filters.=$filter->make_human_filters();
		$aff_filters.="<script>
						function envoi() {
							var formulaire=document.form_filters;
							var j=0;
							for (i=0;i<formulaire.elements.length;i++) {
								var values=new Array();
								if (formulaire.elements[i].type=='select-multiple') {
									for (j=0; j<formulaire.elements[i].options.length; j++) {
										if (formulaire.elements[i].options[j].selected) {
											values[values.length]=formulaire.elements[i].options[j].value;
										}
									}
								} else values[0]=formulaire.elements[i].value;
								if (values.length) {
									for (j=0; j<values.length; j++) {
										var nouvelelement=document.createElement('input');
										nouvelelement.setAttribute('type','hidden');
										nouvelelement.setAttribute('name',formulaire.elements[i].name);
										nouvelelement.value=values[j];
										document.relance_action.appendChild(nouvelelement);	
									}
								}
							}
							document.relance_action.submit();
						}
					</script>";
		print $aff_filters;
		if ($all_level) {
			$pos=strpos($filter->query,"where");
			$requete=substr($filter->query,0,$pos+6);
			$requete.=$filter->params["REFERENCE"][0]['value'].".".$filter->params["REFERENCEKEY"][0]['value']." in (".implode(",",array_keys($all_level)).") and ";
			$requete.=substr($filter->query,$pos+6,strlen($filter->query)-($pos+6));
		} else $requete=$filter->query;
		$colonnes=$filter->display_columns();
		$script="envoi();";
	} else print $filter->error_message;
} else {
	$script="this.form.submit();";
	$colonnes="<th>".$msg["relance_code_empr"]."</th><th>".$msg["relance_name_empr"]."</th><th>".$msg["59"]."</th><th>".$msg["codestat_empr"]."</th><th>".$msg["groupe_empr"]."</th>";
}
echo "<form name='relance_action' action='./circ.php?categ=relance&sub=todo' method='post'>
<input type='hidden' name='relance_solo' value=''/>
<input type='hidden' name='act' value=''/>
<input type='hidden' name='printed_cd' value=''/>";

echo "<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<table style='width:100%' class='sortable'>";
echo "<tr>".$colonnes."<th>".$msg["relance_nb_retard"]."</th><th>".$msg["relance_dernier_niveau"]."</th><th>".$msg["relance_date_derniere"]."</th><th>".$msg["relance_imprime"]."</th><th>".$msg["relance_niveau_suppose"]."</th><th>".$msg["relance_action_prochaine"]."</th><th>&nbsp;</th></tr>";

$resultat=pmb_mysql_query($requete);
$pair=false;
//Nombre de relances à faire
$nb_relances = 0;
$list_dates_sort = array();
$list_dates_relance = array();
while ($r=pmb_mysql_fetch_array($resultat)) {
	if (!$pair) $pair_impair = "odd"; else $pair_impair = "even";
	$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
	if ($all_level[$r["id_empr"]]) $level=$all_level[$r["id_empr"]];	
	else {
		$amende=new amende($r["id_empr"]);
		$level=$amende->get_max_level();
	}
	if (($level["level_normal"])||($level["level_min"])) {
		$pair=!$pair;
		print "<tr id='relance_empr_".$r["id_empr"]."' class='$pair_impair' $tr_javascript>";
		print "<td>".htmlentities($r["empr_cb"],ENT_QUOTES,$charset)."</td>";
		print "<td><a href='./circ.php?categ=pret&id_empr=".$r["id_empr"]."'>".htmlentities($r["empr_nom"]." ".$r["empr_prenom"],ENT_QUOTES,$charset)."</a></td>";
		print "<td>".htmlentities($r["libelle_categ"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["libelle_codestat"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["group_name"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["empr_nb"],ENT_QUOTES,$charset)."</td>";
		$niveau=$level["level"];
		$niveau_min=$level["level_min"];
		$niveau_normal=$level["level_normal"];
		$printed=$level["printed"];
		$date_relance=$level["level_min_date_relance"];
		$list_dates[$date_relance]=format_date($date_relance);
		if ($printed) {
			$list_dates_relance[$date_relance]=$list_dates[$date_relance];
			$dr=explode("-",$date_relance);
			$list_dates_sort[$date_relance]=mktime(0,0,0,$dr[1],$dr[2],$dr[0]);
		}
		//Tri des dates
		if (count($list_dates_sort)) {
			arsort($list_dates_sort);
		}
		print "<td>$niveau_min</td>";
		print "<td>".$list_dates[$date_relance]."</td>";
		print "<td>".($printed?"x":"")."</td>";
		print "<td>$niveau_normal</td>";
		print "<td>".relance::get_action($r["id_empr"],$niveau_min,$niveau_normal)."</td>";
		print "<td><input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$r["id_empr"]."'; this.form.act.value='solo'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>&nbsp;";
		
		//Si mail de rappel affecté au responsable du groupe
		$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$r["id_empr"]." and resp_groupe and mail_rappel limit 1";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res) > 0) {
			$requete="select id_empr, empr_mail from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
			$result=pmb_mysql_query($requete);
			$has_mail = (pmb_mysql_result($result, 0,1) ? 1 : 0);
		} else {
			$has_mail = ($r["empr_mail"] ? 1 : 0); 
		}

		if ($niveau_min) {
			print "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$r["id_empr"]."&niveau=".$niveau_min."','lettre'); this.form.act.value='solo_print'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>";
			$flag_mail=false;
			if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($has_mail))&&(($niveau_min<3)||($mailretard_priorite_email_3==1 && $niveau_min>=3))) {
				$flag_mail=true;
				if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
					//On force en lettre
					$flag_mail=false;
				}
			}			
			if ($flag_mail) {
				print "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$r["id_empr"]."'; this.form.act.value='solo_mail'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>";
			}
		}
		print "</td>";
		print "</tr>\n";
		$nb_relances++;
	}
}
echo "</table>";
print "<div class='right'>";
print "<input type='button' class='bouton' value='".$msg["relance_valid_all"]."' onClick=\"this.form.act.value='valid'; this.form.relance_solo.value=''; $script\"/>&nbsp;";
print "<input type='button' class='bouton' value='".$msg["relance_print_nonprinted"]."' onClick=\"this.form.act.value='print'; this.form.relance_solo.value=''; $script\"/>&nbsp;";
print "<input type='button' class='bouton' value='".$msg["relance_export"]."' onClick=\"this.form.act.value='export'; this.form.relance_solo.value=''; $script\"/>&nbsp;";

if (count($list_dates_relance)) {
	print "<input type='button' value='".addslashes($msg["print_relance_clear"])."' onClick=\"if (confirm('".sprintf(addslashes($msg["confirm_print_relance_clear"]),"'+this.form.clear_date.options[this.form.clear_date.selectedIndex].text+' ?'").")) { this.form.act.value='raz_printed'; this.form.printed_cd.value=this.form.clear_date.options[this.form.clear_date.selectedIndex].value; $script }\" class='bouton'/>&nbsp;<select name='clear_date'>";
	print "<option value=''>".$msg["print_relance_clear_all"]."</option>\n";
	foreach ($list_dates_sort as $val=>$stamp) {
		$lib=$list_dates_relance[$val];
		print "<option value='$val'>".$lib."</option>\n";
	}
	print "</select>";
}
print "</div></form>";
print "<script type='text/javascript'>document.getElementById('nb_relance_to_do').innerHTML='(".$nb_relances.")';</script>";
if($progress_bar)$progress_bar->hide();
?>