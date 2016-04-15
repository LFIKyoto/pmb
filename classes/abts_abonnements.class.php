<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_abonnements.class.php,v 1.41 2015-04-13 14:44:41 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path,$lang;
require_once($include_path."/templates/abts_abonnements.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/abts_func.inc.php");
require_once($include_path."/misc.inc.php");
require_once($class_path."/abts_pointage.class.php");
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/serialcirc.class.php");

class abts_abonnement {
	var $abt_id; //Numéro du modèle
	var $abt_name; //Nom du modèle
	var $base_modele_name;//
	var $base_modele_id;//
	var $num_notice; //numéro de la notice liée
	var $duree_abonnement; //Durée de l'abonnement
	var $date_debut; //Date de début de validité du modèle
	var $date_fin; //Date de fin de validité du modèle
	var $fournisseur;// id du fournisseur
	var $destinataire;
	var $error; //Erreur
	var $error_message; //Message d'erreur
	var $abt_numeric=0;
	
	function abts_abonnement($abt_id="") {
		if ($abt_id) {
			$requete="select * from abts_abts where abt_id=".$abt_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$this->abt_id=$r->abt_id;
				$this->abt_name=$r->abt_name;
				$this->num_notice=$r->num_notice;
				$this->base_modele_name=$r->base_modele_name;
				$this->base_modele_id=$r->base_modele_id;
				$this->num_notice=$r->num_notice; //numéro de la notice liée
				$this->duree_abonnement=$r->duree_abonnement; //Durée de l'abonnement
				$this->date_debut=$r->date_debut; //Date de début de validité du modèle
				$this->date_fin=$r->date_fin; //Date de fin de validité du modèle
				$this->fournisseur=$r->fournisseur;// id du fournisseur
				$this->destinataire=$r->destinataire;
				
				$this->cote=$r->cote;
				$this->typdoc_id=$r->typdoc_id;
				$this->exemp_auto=$r->exemp_auto;
				$this->location_id=$r->location_id;
				$this->section_id=$r->section_id;
				$this->lender_id=$r->lender_id;
				$this->statut_id=$r->statut_id;
				$this->codestat_id=$r->codestat_id;
				$this->type_antivol=$r->type_antivol;		
				$this->abt_numeric=$r->abt_numeric;							
			} else {
				$this->error=true;
				$this->error_message="Le modèle demandé n'existe pas";
			}
		}
	}
	
	function getData() {
		if ($this->abt_id) {
			$requete="select * from abts_abts where abt_id=".$this->abt_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$this->abt_id=$r->abt_id;
				$this->abt_name=$r->abt_name;
				$this->num_notice=$r->num_notice;
				$this->base_modele_name=$r->base_modele_name;
				$this->base_modele_id=$r->base_modele_id;
				$this->num_notice=$r->num_notice; //numéro de la notice liée
				$this->duree_abonnement=$r->duree_abonnement; //Durée de l'abonnement
				$this->date_debut=$r->date_debut; //Date de début de validité du modèle
				$this->date_fin=$r->date_fin; //Date de fin de validité du modèle
				$this->fournisseur=$r->fournisseur;// id du fournisseur
				$this->destinataire=$r->destinataire;
				$this->cote=$r->cote;
				$this->typdoc_id=$r->typdoc_id;
				$this->exemp_auto=$r->exemp_auto;
				$this->location_id=$r->location_id;
				$this->section_id=$r->section_id;
				$this->lender_id=$r->lender_id;
				$this->statut_id=$r->statut_id;
				$this->codestat_id=$r->codestat_id;
				$this->type_antivol=$r->type_antivol;	
				$this->abt_numeric=$r->abt_numeric;		
			} else {
				$this->error=true;
				$this->error_message="Le modèle demandé n'existe pas";
			}
		}				
	}
	
	function set_perio($num_notice) {
		$this->num_notice=0;
		$requete="select niveau_biblio from notices where notice_id=".$num_notice;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			if (pmb_mysql_result($resultat,0,0)=="s")
				$this->num_notice=$num_notice;
		} else {
			$this->error=true;
			$this->error_message="La notice liée n'existe pas ou n'est pas un périodique";
		}
	}
	
	function show_abonnement() {
		global $abonnement_view,$serial_id;
		global $dbh,$msg;
		global $abonnement_serialcirc_empr_list_empr, $abonnement_serialcirc_empr_list_group, $abonnement_serialcirc_empr_list_group_elt;
		$perio=new serial_display($this->num_notice,1);
		$r=$abonnement_view;
		$r=str_replace("!!view_id_abonnement!!","catalog.php?categ=serials&sub=abon&serial_id=$serial_id&abt_id=$this->abt_id",$r);
		$r=str_replace("!!id_abonnement!!",$this->abt_id,$r);
		$r=str_replace("!!abonnement_header!!",$this->abt_name,$r);
		$modele=0;
		$modele_list="";
		$requete="select modele_id from abts_abts_modeles where abt_id='$this->abt_id'";			
		$resultat=pmb_mysql_query($requete, $dbh);
		while ($r_a=pmb_mysql_fetch_object($resultat)) {
			$modele_id=$r_a->modele_id;
			$modele_name=pmb_sql_value("select modele_name from abts_modeles where modele_id='$modele_id'");
			$num_periodicite=pmb_sql_value("select num_periodicite from abts_modeles where modele_id='$modele_id'");
			$periodicite=pmb_sql_value("SELECT libelle from abts_periodicites where periodicite_id='".$num_periodicite."'");
			if ($modele_list) $modele_list.=","; 
			$modele_list.=" $modele_name"; 
			if($periodicite) $modele_list.=" ($periodicite)"; 
		}			
		$r=str_replace("!!modele_lie!!",$modele_list,$r);
		$r=str_replace("!!duree_abonnement!!",$this->duree_abonnement,$r);
		$r=str_replace("!!date_debut!!",format_date($this->date_debut),$r);
		$r=str_replace("!!date_fin!!",format_date($this->date_fin),$r);								
		$r=str_replace("!!nombre_de_series!!",pmb_sql_value("select sum(nombre) from abts_grille_abt where num_abt='$this->abt_id' and type ='1'"),$r);
		$r=str_replace("!!nombre_de_horsseries!!",pmb_sql_value("select sum(nombre) from abts_grille_abt where num_abt='$this->abt_id' and type ='2'"),$r);

		if($this->fournisseur) $fournisseur_name=$msg["abonnements_fournisseur"].": ".pmb_sql_value("SELECT raison_sociale from entites where id_entite = '".$this->fournisseur."' ");
		$r=str_replace("!!fournisseur!!",$fournisseur_name,$r);		
		
		$aff_destinataire="";
		if($this->destinataire){
			$aff_destinataire="<tr>
				<td colspan='2'>".$this->destinataire."</td>
			</tr>";
		}
		$r=str_replace("!!commentaire!!",$aff_destinataire,$r);					
		
		//Liste des destinataires
		$serialcirc_diff=new serialcirc_diff(0,$this->abt_id);
		$tpl_empr_list = "";
		foreach($serialcirc_diff->diffusion as $diff){
			if($diff['empr_type']==SERIALCIRC_EMPR_TYPE_empr){
 				$tpl_empr=$abonnement_serialcirc_empr_list_empr;
 				$name_elt=$serialcirc_diff->empr_info[ $diff['empr']['id_empr']]['empr_libelle'];
			}else{
				$name_elt=$diff['empr_name'];
				$group_list_list="";
				if(count($diff['group'])){
 					$tpl_empr=$abonnement_serialcirc_empr_list_group;
					foreach($diff['group'] as $empr){
						$group_list=$abonnement_serialcirc_empr_list_group_elt;
						$resp="";
						if($empr['responsable']){
							$resp=$msg["serialcirc_group_responsable"];
						}
						$group_list=str_replace('!!empr_libelle!!',$empr['empr']['empr_libelle'].$resp, $group_list);
						$group_list_list.=$group_list;
					}
					$tpl_empr=str_replace('!!empr_list!!', $group_list_list, $tpl_empr);
				}else {
					$tpl_empr=$abonnement_serialcirc_empr_list_empr;
				}
			}
			$tpl_empr=str_replace('!!id_diff!!', $diff['id'], $tpl_empr);
			$tpl_empr=str_replace('!!empr_view_link!!', $diff['empr']['view_link'], $tpl_empr);
			$tpl_empr=str_replace('!!empr_name!!', $name_elt, $tpl_empr);
			$tpl_empr_list.=$tpl_empr;
		}
		$aff_empr_list="";
		if($tpl_empr_list){
			$aff_empr_list="
			<tr>
				<td colspan='2'>
					<h3>".$msg["serialcirc_diff_empr_list_title"]."</h3>
					$tpl_empr_list 
				</td>
			</tr>";
		}
		
		$r=str_replace("!!serial_id!!", $serial_id, $r);
		$r=str_replace("!!serialcirc_empr_list!!", $aff_empr_list, $r);
		$r=str_replace("!!serialcirc_export_list_bt!!", "<input type='button' class='bouton' value='".$msg["serialcirc_export_list"]."' 
				onClick=\"document.location='./edit.php?dest=TABLEAU&categ=serialcirc_diff&sub=export_empr&&num_abt=".$this->abt_id."'\"/>&nbsp;", $r);
		
		return $r;
	}
	
	function show_form() {
		global $creation_abonnement_form;
		global $serial_header;
		global $msg;
		global $charset;
		global $tpl_del_bouton,$tpl_copy_bouton,$serial_id,$edition_abonnement_form,$pmb_antivol;
		global $dbh;
		global $pmb_abt_label_perio;
				
		if (!$this->abt_id) {
			$r=$serial_header.$creation_abonnement_form;
			$r=str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_abonnements_add_title"], $r);
			$r=str_replace('!!libelle_form!!', $msg["abts_abonnements_add_title"], $r);
			if($pmb_abt_label_perio){
				$serial = new serial($serial_id);
				$r=str_replace('!!abt_name!!', $serial->tit1, $r);
			}

			//Checkbox des modèles à associer à l'abonnement
			$resultat=pmb_mysql_query("select modele_id,modele_name from abts_modeles where num_notice='$serial_id'");	
			$liste_modele="<table>";
			//Confection du javascript pour tester au moins une sélection de modèle
			$test_liste_modele="if(";	
			$cpt=0;
			while ($rp=pmb_mysql_fetch_object($resultat)) {		
				if(	$cpt++ >0)	$test_liste_modele.=" || ";
				$liste_modele.="<tr><td><input type='checkbox' value='$rp->modele_id' name='modele[$rp->modele_id]' id='modele[$rp->modele_id]'/>$rp->modele_name</td></tr>";
				$test_liste_modele.=" (document.getElementById('modele[".$rp->modele_id."]').checked==true) ";
				
			}
			$test_liste_modele.=")
			{
				return true;
			}else {
				alert(\"$msg[abonnements_err_msg_select_model]\");				
				return false;
			}";
			$liste_modele.="</table>";
			$r=str_replace("!!liste_modele!!",$liste_modele,$r);
			$r=str_replace("!!test_liste_modele!!",$test_liste_modele,$r);
			
			$copy_bouton=$del_bouton="";
			$r=str_replace("!!abonnement_form1!!","",$r);		
			$bouton_prolonge='';
			$bouton_raz='';
		} else {
			$this->getData();
			$r=$serial_header.$edition_abonnement_form;
			$r=str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_abonnements_modify_title"], $r);
			$r=str_replace('!!libelle_form!!', $msg["abts_abonnements_modify_title"], $r);
			$bouton_prolonge="<input type=\"submit\" class='bouton' value='".$msg["abonnement_prolonger_abonnement"]."' onClick=\"document.getElementById('act').value='prolonge';if(test_form(this.form)==true) this.form.submit();else return false;\"/>";
			$bouton_raz="<input type=\"submit\" class='bouton' value='".$msg["abonnement_raz_grille"]."' onClick=\"if(confirm('".$msg['confirm_raz_grille']."')){document.getElementById('act').value='raz';if(test_form(this.form)==true) this.form.submit();else return false;} else return false;\"/>";
			//Durée d'abonnement
			if (!$this->duree_abonnement)	$this->duree_abonnement=12;
			$r=str_replace("!!duree_abonnement!!",$this->duree_abonnement,$r);
			
			//Date de début
			if (!$this->date_debut || $this->date_debut == "0000-00-00") $date_debut=date("Ymd",time()); else $date_debut=$this->date_debut;
			
			$r=str_replace("!!date_debut!!",str_replace("-","",$date_debut),$r);
			$r=str_replace("!!date_debut_lib!!",formatdate($date_debut),$r);
			
			//Date de fin
			if (!$this->date_fin || $this->date_fin == "0000-00-00") $date_fin=pmb_sql_value("SELECT DATE_ADD('$date_debut', INTERVAL 1 YEAR)"); else $date_fin=$this->date_fin;
		
			$r=str_replace("!!date_fin!!",str_replace("-","",$date_fin),$r);
			$r=str_replace("!!date_fin_lib!!",format_date($date_fin),$r);
			
			//Fournisseur
			$r=str_replace('!!lib_fou!!', htmlentities(pmb_sql_value("SELECT raison_sociale from entites where id_entite = '".$this->fournisseur."' "),ENT_QUOTES,$charset), $r);
			$r=str_replace('!!id_fou!!', $this->fournisseur, $r);
			
			//Destinataire:
			$r=str_replace('!!destinataire!!', $this->destinataire, $r);
			
			//Cote:
			$r=str_replace('!!cote!!', htmlentities($this->cote,ENT_QUOTES,$charset), $r);
			
			// select "type document"
			$r = str_replace('!!type_doc!!',
						do_selector('docs_type', 'typdoc_id', $this->typdoc_id),
						$r);
																								
			$r = str_replace('!!exemplarisation_automatique!!',			
			"<input type='checkbox' value='1' ".($this->exemp_auto ?"checked":"yes")." name='exemp_auto' id='exemp_auto'/>",			
						$r);
			$r = str_replace('!!abt_numeric_checked!!',	($this->abt_numeric ?"checked":"yes"),			
						$r);
						
			// select "localisation"
			$r = str_replace('!!localisation!!',
						gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_id', "calcule_section(this);", $this->location_id, "", "","","",0),
						$r);
			
			// select "section"
			$r = str_replace('!!section!!',
						$this->do_selector(),
						$r);
		
				// select "owner"
			$r = str_replace('!!owner!!',
						do_selector('lenders', 'lender_id', $this->lender_id),
						$r);			
			
			// select "statut"
			$r = str_replace('!!statut!!',
						do_selector('docs_statut', 'statut_id', $this->statut_id),
						$r);
							
			// select "code statistique"
			$r = str_replace('!!codestat!!',
						do_selector('docs_codestat', 'codestat_id', $this->codestat_id),
						$r);
		
			$selector="";
			if($pmb_antivol>0) {// select "type_antivol"
				$selector = "<select name='type_antivol' id='type_antivol'>";			
				$selector .= "<option value='0'";
				if($this->type_antivol ==0)$selector .= ' SELECTED';
				$selector .= '>';
				$selector .= $msg["type_antivol_aucun"].'</option>';
				$selector .= "<option value='1'";
				if($this->type_antivol ==1)$selector .= ' SELECTED';
				$selector .= '>';
				$selector .= $msg["type_antivol_magnetique"].'</option>';
				$selector .= "<option value='2'";
				if($this->type_antivol ==2)$selector .= ' SELECTED';
				$selector .= '>';
				$selector .= $msg["type_antivol_autre"].'</option>';			                                        
				$selector .= '</select>'; 
			}
			  			        
			$r = str_replace('!!type_antivol!!',
						$selector,
						$r);
					
			//Liste des formulaire de modèles (dépliables +,-)
			$modele_list="";
			$requete="select modele_id,num,vol,tome,delais,critique, num_statut_general from abts_abts_modeles where abt_id='$this->abt_id'";			
			$resultat=pmb_mysql_query($requete, $dbh);
			if (!$resultat) die($requete."<br /><br />".pmb_mysql_error());
			while ($r_a=pmb_mysql_fetch_object($resultat)) {
				$modele_id=$r_a->modele_id;
				$num=$r_a->num;
				$vol=$r_a->vol;
				$tome=$r_a->tome;
				$delais=$r_a->delais;
				$critique=$r_a->critique;
				$modele_name=pmb_sql_value("select modele_name from abts_modeles where modele_id='$modele_id'");
				$num_periodicite=pmb_sql_value("select num_periodicite from abts_modeles where modele_id='$modele_id'");
				$periodicite=pmb_sql_value("select libelle from abts_periodicites where periodicite_id ='".$num_periodicite."'");
				$num_statut=$r_a->num_statut_general;
				if($periodicite) $modele_name.=" ($periodicite)"; 	
				if(!$num_statut)$num_statut=$this->statut_id;
				$modele_list.=$this->gen_tpl_abt_modele($modele_id,$modele_name,$num,$vol,$tome,$delais,$critique,$num_statut);	
			}		
			$r=str_replace("!!modele_list!!",$modele_list,$r);

			// calendrier de réception s'il y a des enregistrement présents dans la grille
			$r.="<script type=\"text/javascript\" src='./javascript/select.js'></script>
				<script type=\"text/javascript\" src='./javascript/ajax.js'></script>";
			
			if (pmb_sql_value("select sum(nombre) from abts_grille_abt where num_abt='$this->abt_id'"))
			{				
						
$calend.= <<<ENDOFTEXT
				<script type="text/javascript">
				function ad_date(obj,e) {
					if(!e) e=window.event;			
					var tgt = e.target || e.srcElement; // IE doesn't use .target
					var strid = tgt.id;
					var type = tgt.tagName;		
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();			
					var id_obj=document.getElementById(obj);
					var pos=findPos(id_obj);					
					var url="./catalog/serials/abonnement/abonnement_parution_edition.php?abonnement_id=!!abonnement_id!!&date_parution="+obj+"&type_serie=1&numero=";				
					var notice_view=document.createElement("iframe");		
					notice_view.setAttribute('id','frame_abts');
					notice_view.setAttribute('name','periodique');
					notice_view.src=url; 			
					var att=document.getElementById("att");	
					notice_view.style.visibility="hidden";
					notice_view.style.display="block";
					notice_view=att.appendChild(notice_view);			
					w=notice_view.clientWidth;
					h=notice_view.clientHeight;
					notice_view.style.left=pos[0]+"px";
					notice_view.style.top=pos[1]+"px";
					notice_view.style.visibility="visible";			
				}
				</script>	
ENDOFTEXT;
				$calend=str_replace("!!serial_id!!",$serial_id,$calend);	
				$calend=str_replace("!!abonnement_id!!",$this->abt_id,$calend);					
				$base_url="./catalog.php?categ=serials&sub=abonnement&serial_id="."$serial_id&abonnement_id=$this->abonnement_id";
				$base_url_mois='';	
	
				$calend.= "<div id='calendrier_tab' style='width:99%'>" ;
				$date = $this->date_debut;
				$calend.= "<A name='ancre_calendrier'></A>"; 
					
				$year=pmb_sql_value("SELECT YEAR('$date')");
				$cur_year=$year;
				//debut expand
				$calend.="
				<div class='row'>&nbsp;</div>
				<div id='abts_year_$year' class='notice-parent'>
					<img src='./images/minus.gif' class='img_plus' name='imEx' id='abts_year_$year"."Img' title='".addslashes($msg['plus_detail'])."' border='0' onClick=\"expandBase('abts_year_$year', true); return false;\" hspace='3'>
					<span class='notice-heada'>
						$year
		    		</span>
				</div>
				<div id='abts_year_$year"."Child' startOpen='Yes' class='notice-child' style='margin-bottom:6px;width:94%'>
				";	
							
				$i=pmb_sql_value("SELECT MONTH('$date')");	
				if($i==2 || $i==5 || $i==8 || $i==11) {
						$calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>&nbsp;";
						$calend.= "</div>\n";					
				}
				if($i==3 || $i==6 || $i==9 || $i==12) {
						$calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>&nbsp;";
						$calend.= "</div>\n";
						$calend.= "<div class='colonne3' style='padding-left: 3px'>&nbsp;";	
						$calend.= "</div>\n";		
				}	
				do{
					$year=pmb_sql_value("SELECT YEAR('$date')");	
					if($year!=$cur_year){
						$calend.= "
						</div>
						";
						$calend.="
						<div class='row'></div>
						<div id='abts_year_$year' class='notice-parent'>
							<img src='./images/plus.gif' class='img_plus' name='imEx' id='abts_year_$year"."Img' title='".addslashes($msg['plus_detail'])."' border='0' onClick=\"expandBase('abts_year_$year', true); return false;\" hspace='3'>
							<span class='notice-heada'>
								$year
				    		</span>
						</div>
						<div id='abts_year_$year"."Child' class='notice-child' style='margin-bottom:6px;display:none;width:94%'>
						";	
						$cur_year=$year;
					}								
					$i=pmb_sql_value("SELECT MONTH('$date')");	
					
					if ($i==1 || $i==4 || $i==7 || $i==10 ) $calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>";
					else 
						$calend.= "<div class='colonne3' style='padding-left: 3px'>";
					$calend.= pmb_bidi(calendar_gestion(str_replace("-","",$date), 0, $base_url, $base_url_mois,0,0,$this->abt_id));
					$calend.= "</div>\n";
					if ($i==3 || $i==6 || $i==9 || $i==12 ) $calend.="</div>\n";
					
					$date=pmb_sql_value("SELECT DATE_ADD('$date', INTERVAL 1 MONTH)");
					$diff=pmb_sql_value("SELECT DATEDIFF('$date_fin','$date')");					
				}
				while($diff>=0);	
				//fin expand
				$calend.= "	</div>";								
				$calend.= "</div>\n";
				$calend.="<script type='text/javascript'>parent.location.href='#ancre_calendrier';</script>";				
				$r.=$calend;	
			}		
			$js= <<<ENDOFTEXT
			<script type="text/javascript">
			function duplique(obj,e) {
				if(!e) e=window.event;
				var tgt = e.target || e.srcElement; // IE doesn't use .target
				var strid = tgt.id;
				var type = tgt.tagName;
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				var id_obj=document.getElementById(obj);
				var pos=findPos(id_obj);
				var url="./catalog/serials/abonnement/abonnement_duplique.php?abonnement_id=!!abonnement_id!!&serial_id=!!serial_id!!";
				var notice_view=document.createElement("iframe");
				notice_view.setAttribute('id','frame_abts');
				notice_view.setAttribute('name','periodique');
				notice_view.src=url;
				var att=document.getElementById("att");
				notice_view.style.visibility="hidden";
				notice_view.style.display="block";
				notice_view=att.appendChild(notice_view);
				w=notice_view.clientWidth;
				h=notice_view.clientHeight;
				posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
				posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
				notice_view.style.left=posx+"px";
				notice_view.style.top=posy+"px";
				notice_view.style.visibility="visible";
			}
					
			function kill_frame_periodique() {
				var notice_view=document.getElementById("frame_abts");
				notice_view.parentNode.removeChild(notice_view);	
			}
			</script>
ENDOFTEXT;
			$js=str_replace("!!serial_id!!",$serial_id,$js);	
			$js=str_replace("!!abonnement_id!!",$this->abt_id,$js);
			$r.=$js;
			$r=str_replace("!!test_liste_modele!!","",$r);			
		}	
		$r=str_replace("!!action!!","./catalog.php?categ=serials&sub=abon&serial_id="."$serial_id"."&abt_id="."$this->abt_id",$r);	
		$r=str_replace('!!bouton_prolonge!!', $bouton_prolonge, $r);
		$r=str_replace('!!bouton_raz!!', $bouton_raz, $r);
		
		$r=str_replace("!!serial_id!!",$serial_id,$r);
		
		//Remplacement des valeurs
		$r=str_replace("!!abt_id!!",htmlentities($this->abt_id,ENT_QUOTES,$charset),$r);
		$r=str_replace("!!abt_name!!",htmlentities($this->abt_name,ENT_QUOTES,$charset),$r);
		
		//Notice mère
		$perio=new serial_display($this->num_notice,1);
		$r=str_replace("!!num_notice_libelle!!",$perio->header,$r);
		$r=str_replace("!!num_notice!!",$this->num_notice,$r);
		return $r;
	}
	
	// ----------------------------------------------------------------------------
	//	fonction do_selector qui génère des combo_box avec tout ce qu'il faut
	// ----------------------------------------------------------------------------
	function do_selector() {	
		global $dbh;
	 	global $charset;		
		global $deflt_docs_section;
		global $deflt_docs_location;
	
		if (!$this->section_id) $this->section_id=$deflt_docs_section ;
		if (!$this->location_id) $this->location_id=$deflt_docs_location;
	
		$rqtloc = "SELECT idlocation FROM docs_location order by location_libelle";
		$resloc = pmb_mysql_query($rqtloc, $dbh);
		while ($loc=pmb_mysql_fetch_object($resloc)) {
			$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
			$result = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_num_rows($result);
			if ($nbr_lignes) {			
				if ($loc->idlocation==$this->location_id) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
					else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
				$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>\r\n";
				while($line = pmb_mysql_fetch_row($result)) {
					$selector .= "<option value='$line[0]'";
					$line[0] == $this->section_id ? $selector .= ' SELECTED>' : $selector .= '>';
		 			$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>\r\n';
					}                                         
				$selector .= '</select></div>';
				}                 
			}
		return $selector;                         
	}                                                 
 
	function gen_tpl_abt_modele($id,$titre,$num,$vol,$tome,$delais,$delais_critique,$change_statut_id){
		global $dbh;
		global $msg;
		
		$requete="select * from abts_modeles where modele_id='$id'";
		$resultat=pmb_mysql_query($requete, $dbh);
		if ($r_a=pmb_mysql_fetch_object($resultat)) {
			$tom_actif=$r_a->tom_actif;	
			$vol_actif=$r_a->vol_actif;	
			$num_depart=$r_a->num_depart;
			$vol_depart=$r_a->vol_depart;
			$tom_depart=$r_a->tom_depart;	
		}	
		if(!$num)	$num=$num_depart;
		if(!$vol)	$vol=$vol_depart;
		if(!$tome)	$tome=$tom_depart;			
		$contenu= "
		<div class='row'>
			<label for='num_periodicite' class='etiquette'>".$msg["abonnements_periodique_numero_depart"]."</label>
		</div>	
		<div class='row'>
			<input type='text' size='4' name='num[$id]' id='num[$id]' value='$num'/>		
		</div>
		";
		if($vol_actif)$contenu.= "		
		<div class='colonne2'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_volume_numero_depart"]."</label>
			</div>	
			<div class='row'>
				<input type='text' size='4' name='vol[$id]' id='vol[$id]' value='$vol'/>	
			</div>
		</div>
		";
		if($tom_actif)$contenu.= "
		<div class='colonne_suite'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_tome_numero_depart"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='tome[$id]' id='tome' value='$tome'/>
			</div>
		</div>
		";		
		$contenu.= "
		<div class='row'></div>
		<div class='colonne2'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_delais_avant_retard"]."</label>
			</div>	
			<div class='row'>
				<input type='text' size='4' name='delais[$id]' id='delais[$id]' value='$delais'/>
			</div>
		</div>
		<div class='colonne_suite'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_delais_critique"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='delais_critique[$id]' id='delais_critique[$id]' value='$delais_critique'/>
			</div>
		</div>
		<div class='row'></div>
		";
		
		// select !!change_statut!!	
		$statut_form=str_replace('!!statut_check!!',
			"<input type='checkbox' checked value='1' name='change_statut_check[".$id."]' id='change_statut[".$id."]_check' onclick=\"gere_statut('change_statut[".$id."]');\"/>",
			$msg['catalog_change_statut_form']);

		$statut_form=str_replace('!!statut_list!!',
			do_selector('docs_statut', "change_statut[".$id."]", $change_statut_id),
			$statut_form);
				
		$contenu.= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			$statut_form
		</div>
		";						

		return gen_plus_form($id,$titre,$contenu);
	}
	
	function gen_date($garder=0){
		global $dbh;
		global $msg;
		global $include_path;
		
		if($this->abt_id) {
			if (!$garder) {
				$dummy = "delete FROM abts_grille_abt WHERE num_abt='$this->abt_id' and state='0'";
				pmb_mysql_query($dummy, $dbh);
			}
			
			$date=$date_debut = construitdateheuremysql($this->date_debut);	
			$date_fin = construitdateheuremysql($this->date_fin);		
					
			//Pour tous les modèles utilisé dans l'abonnement, on recopie les grilles modèles dans la grille abonnement  					
			$requete="select modele_id from abts_abts_modeles where abt_id='$this->abt_id'";				
			$resultat_a=pmb_mysql_query($requete, $dbh);
			while ($r_a=pmb_mysql_fetch_object($resultat_a)) {
				$modele_id=$r_a->modele_id;
				
				$requete="select * from abts_grille_modele where num_modele='$modele_id'";
				$resultat=pmb_mysql_query($requete);
				while ($r_g=pmb_mysql_fetch_object($resultat)) {
					
					//Ne garder les bulletins compris entre les dates de début et fin d'abonnement
					if( ( pmb_sql_value("SELECT DATEDIFF('$date_fin','$r_g->date_parution')")>= 0 ) &&
						( pmb_sql_value("SELECT DATEDIFF('$date_debut','$r_g->date_parution')")<= 0 ) ) {
						for($i=1;$i<=$r_g->nombre_recu;$i++){
							$requete = "INSERT INTO abts_grille_abt SET num_abt='$this->abt_id', 
								date_parution ='$r_g->date_parution', 
								modele_id='$modele_id', 
								type = '$r_g->type_serie',
								numero='$r_g->numero', 
								nombre='1', 
								ordre='$i' ";
							pmb_mysql_query($requete, $dbh);
						}
					}
				}
			}	
		}	
	}
	
	function update() {
		global $dbh;
		global $msg;
		global $include_path;
		global $act,$modele,$num,$vol,$tome,$delais,$delais_critique,$change_statut,$change_statut_check;
		
		if(!$this->abt_name)	return false;	
		// nettoyage des valeurs en entrée
		$this->abt_name = clean_string($this->abt_name); 
		// construction de la requête
		$requete = "SET abt_name='".addslashes($this->abt_name)."', ";
		$requete .= "num_notice='$this->num_notice', ";
		$requete .= "duree_abonnement='$this->duree_abonnement', ";
		$requete .= "date_debut='$this->date_debut', ";
		$requete .= "date_fin='$this->date_fin', ";
		$requete .= "fournisseur='$this->fournisseur', ";
		$requete .= "destinataire='".addslashes($this->destinataire)."', ";		
		$requete .= "cote='".addslashes($this->cote)."', ";	
		$requete .= "typdoc_id='$this->typdoc_id', ";
		$requete .= "exemp_auto='$this->exemp_auto', ";
		$requete .= "location_id='$this->location_id', ";
		$requete .= "section_id='$this->section_id', ";
		$requete .= "lender_id='$this->lender_id', ";
		$requete .= "statut_id='$this->statut_id', ";
		$requete .= "codestat_id='$this->codestat_id', ";
		$requete .= "type_antivol='$this->type_antivol', ";	
		$requete .= "abt_numeric='$this->abt_numeric' ";	
			
		if($this->abt_id) {
			// Update: s'assurer que le nom d'abonnement n'existe pas déjà
			$dummy = "SELECT * FROM abts_abts WHERE abt_name='".addslashes($this->abt_name)."' and num_notice='$this->num_notice' and abt_id!=$this->abt_id";
			$check = pmb_mysql_query($dummy, $dbh);
			if(pmb_mysql_num_rows($check)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_erreur_creation_doublon_abonnement"]." ($this->abt_name).");
				return FALSE;
			}

			// update
			$requete = 'UPDATE abts_abts '.$requete;
			$requete .= ' WHERE abt_id='.$this->abt_id.' LIMIT 1;';
			
			if(pmb_mysql_query($requete, $dbh) ) {	
				if($act=="gen") $this->gen_date();
				$requete="select modele_id from abts_modeles where num_notice='$this->num_notice'";
				$resultat=pmb_mysql_query($requete, $dbh);			
				while ($r=pmb_mysql_fetch_object($resultat)) {
					$modele_id=$r->modele_id;
					if($change_statut_check[$modele_id])$num_statut=$change_statut[$modele_id];
					else $num_statut=$this->statut_id;
					$requete = "UPDATE abts_abts_modeles SET num='$num[$modele_id]', vol='$vol[$modele_id]', tome='$tome[$modele_id]', delais='$delais[$modele_id]', critique='$delais_critique[$modele_id]'
					, num_statut_general='$num_statut' WHERE modele_id='$modele_id'and abt_id='$this->abt_id'";
					pmb_mysql_query($requete, $dbh);						
				}								
				return TRUE;
			}
			else {
				echo pmb_mysql_error();
				require_once("$include_path/user_error.inc.php");
				warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
				return FALSE;
			}
		} 
		else {				
			// Création: s'assurer que le modèle n'existe pas déjà
			$dummy = "SELECT * FROM abts_abts WHERE abt_name='".addslashes($this->abt_name)."' and num_notice='$this->num_notice'";
			$check = pmb_mysql_query($dummy, $dbh);
			if(pmb_mysql_num_rows($check)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_erreur_creation_doublon_abonnement"]." ($this->abt_name).");
				return FALSE;
			}
			$requete = 'INSERT INTO abts_abts '.$requete.';';
			if(pmb_mysql_query($requete, $dbh)) {
				$this->abt_id=pmb_mysql_insert_id();		
				$requete="select modele_id,num_periodicite from abts_modeles where num_notice='$this->num_notice'";
				$resultat=pmb_mysql_query($requete, $dbh);		
				while ($r=pmb_mysql_fetch_object($resultat)) {
					$modele_id=$r->modele_id;	
					$num_periodicite=$r->num_periodicite;		
					if(isset($modele[$modele_id])){
						$requete="select retard_periodicite,seuil_periodicite from abts_periodicites where periodicite_id ='".$num_periodicite."'";
						$r_delais=pmb_mysql_query($requete, $dbh);		
						if ($r_d=pmb_mysql_fetch_object($r_delais)) {
							$periodicite=$r_d->libelle;									
							$delais=$r_d->seuil_periodicite;	
							$critique=$r_d->retard_periodicite;
						}
						if($change_statut_check[$modele_id])$num_statut=$change_statut[$modele_id];
						else $num_statut=$this->statut_id;
						$requete = "INSERT INTO abts_abts_modeles SET modele_id='$modele_id', abt_id='$this->abt_id', delais='$delais', critique='$critique', num_statut_general='$num_statut' ";
						pmb_mysql_query($requete, $dbh);	
					}			
				}
				if($act=="gen") $this->gen_date();
				return TRUE;	
			} 
			else {
				echo pmb_mysql_error();
				require_once("$include_path/user_error.inc.php");
				warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
				return FALSE;
			}
		}
	}
	
	function delete(){
		global $dbh;
		global $msg;
		global $include_path;
		
		// l'abonnement a encore au moins un expl en circulation
		if(serialcirc_diff::expl_in_circ($this->abt_id)){			
			return $msg['serialcirc_error_delete_abt'];
		}
		$dummy = "delete FROM abts_abts WHERE abt_id='$this->abt_id' ";
		$check = pmb_mysql_query($dummy, $dbh);	
							
		$dummy = "delete FROM abts_grille_abt WHERE num_abt='$this->abt_id' ";
		$check = pmb_mysql_query($dummy, $dbh);	
		
		$dummy = "delete FROM abts_abts_modeles WHERE abt_id='$this->abt_id' ";		
		$check = pmb_mysql_query($dummy, $dbh);	
					
		abts_pointage::delete_retard($this->abt_id);
		
		serialcirc_diff::delete($this->abt_id);
		return "";
	}
		
	
	function proceed() {
		global $act;
		global $serial_id,$msg,$num_notice,$num_periodicite,$duree_abonnement,$date_debut,$date_fin,$days,$day_month,$week_month,$week_year,$month_year,$date_parution;		
		global $abt_name,$duree_abonnement,$date_debut,$date_fin,$id_fou,$destinataire;
		global $dbh,$abt_id;
		global $cote,$typdoc_id,$exemp_auto,$location_id,$lender_id,$statut_id,$codestat_id,$type_antivol,$abt_numeric;
		global $deflt_docs_section;
		global $deflt_docs_location,$nb_duplication;
		
		$formlocid="f_ex_section".$location_id ;
		global $$formlocid;
		$section_id=$$formlocid ;
		
		if (!$section_id) $section_id=$deflt_docs_section ;
		if (!$location_id) $location_id=$deflt_docs_location;

		switch ($act) {
			case 'update':								
				// mise à jour modèle
				$this->abt_name= stripslashes($abt_name);
				$this->num_notice= $num_notice;
				$this->duree_abonnement = $duree_abonnement;
				$this->date_debut= $date_debut;
				$this->date_fin= $date_fin;
				$this->fournisseur = $id_fou;
				$this->destinataire = stripslashes($destinataire);			
				$this->cote=stripslashes($cote);
				$this->typdoc_id=$typdoc_id;
				$this->exemp_auto=$exemp_auto;
				$this->location_id=$location_id;
				$this->section_id=$section_id;
				$this->lender_id=$lender_id;
				$this->statut_id=$statut_id;
				$this->codestat_id=$codestat_id;
				$this->type_antivol=$type_antivol;
				$this->abt_numeric=$abt_numeric;
											
				$this->update();										
				print $this->show_form();		
			break;
			case 'gen':								
				// mise à jour modèle
				$this->abt_name= stripslashes($abt_name);
				$this->num_notice= $num_notice;
				$this->duree_abonnement = $duree_abonnement;
				$this->date_debut= $date_debut;
				$this->date_fin= $date_fin;
				$this->fournisseur = $id_fou;
				$this->destinataire = stripslashes($destinataire);
				$this->cote=stripslashes($cote);
				$this->typdoc_id=$typdoc_id;
				$this->exemp_auto=$exemp_auto;
				$this->location_id=$location_id;
				$this->section_id=$section_id;
				$this->lender_id=$lender_id;
				$this->statut_id=$statut_id;
				$this->codestat_id=$codestat_id;
				$this->type_antivol=$type_antivol;
				$this->abt_numeric=$abt_numeric;
													
				$this->update();										
				print $this->show_form();		
			break;	
			case 'prolonge':								
				// mise à jour modèle
				$this->abt_name= stripslashes($abt_name);
				$this->num_notice= $num_notice;
				$this->duree_abonnement = $duree_abonnement;							
				$this->date_debut= $date_fin;				
				$this->date_fin= pmb_sql_value("SELECT DATE_ADD('$date_fin',INTERVAL $duree_abonnement month)");
				$this->fournisseur = $id_fou;
				$this->destinataire = stripslashes($destinataire);
				$this->cote=stripslashes($cote);
				$this->typdoc_id=$typdoc_id;
				$this->exemp_auto=$exemp_auto;
				$this->location_id=$location_id;
				$this->section_id=$section_id;
				$this->lender_id=$lender_id;
				$this->statut_id=$statut_id;
				$this->codestat_id=$codestat_id;
				$this->type_antivol=$type_antivol;
				$this->abt_numeric=$abt_numeric;
				$this->gen_date(1);
				$this->date_debut= $date_debut;
				$this->update();				
				print $this->show_form();		
			break;			
			case 'copy':
				
				$this->getData();
				$abt_id=$this->abt_id;
				$this->abt_name.="_1";
				$this->abt_name=addslashes($this->abt_name);
				$this->destinataire=addslashes($this->destinataire);
				for($i=0;$i<$nb_duplication;$i++){
					//Création nouvel abonnement
					$this->abt_id='';
					do {
						$this->abt_name++;
						$requete = "SELECT abt_name FROM abts_abts WHERE abt_name='$this->abt_name' and num_notice='$this->num_notice'";						
						$resultat=pmb_mysql_query($requete, $dbh);		
					}	
					while (pmb_mysql_fetch_object($resultat));	
					$this->update();
					//recopie des modeles associés
					$requete = "select * from abts_abts_modeles where abt_id='$abt_id'";
					$resultat=pmb_mysql_query($requete);
					while ($r_m=pmb_mysql_fetch_object($resultat)) {	
						$requete = "INSERT INTO abts_abts_modeles SET modele_id='$r_m->modele_id', abt_id='$this->abt_id',num='$r_m->num' ,vol='$r_m->vol',tome='$r_m->tome',delais='$r_m->delais', critique='$r_m->critique',num_statut_general='$r_m->num_statut_general'";
						pmb_mysql_query($requete, $dbh);	
					}
					//recopie des infos du calendrier
					$requete = "select * from abts_grille_abt where num_abt='$abt_id'";
					$resultat=pmb_mysql_query($requete);
					while ($r_g=pmb_mysql_fetch_object($resultat)) {			
						$requete = "INSERT INTO abts_grille_abt SET num_abt='$this->abt_id', 
							date_parution ='$r_g->date_parution', 
							modele_id='$r_g->modele_id', 
							type = '$r_g->type',
							numero='$r_g->numero', 
							nombre='$r_g->nombre', 
							ordre='$r_g->ordre' ";		
						pmb_mysql_query($requete, $dbh);
					}		
				}							
				print "<div class='row'><div class='msg-perio'>".$msg[maj_encours]."</div></div>";
				$id_form = md5(microtime());
				$retour = "./catalog.php?categ=serials&sub=view&serial_id=$serial_id&view=abon";
				print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
					<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
					</form>
					<script type=\"text/javascript\">document.dummy.submit();</script>
					</div>";		
			break;
			case 'raz':																					
				if($this->abt_id) {
					$dummy = "delete FROM abts_grille_abt WHERE num_abt='".$this->abt_id."'";
					pmb_mysql_query($dummy, $dbh);
				}										
				print $this->show_form();		
				break;
			case 'del':				
				if($msg_error=$this->delete())	{
					$retour = "./circ.php?categ=serialcirc";
					error_message('', $msg_error, 1, $retour);						
				}else{
					print "<div class='row'><div class='msg-perio'>".$msg[maj_encours]."</div></div>";
					$id_form = md5(microtime());
					$retour = "./catalog.php?categ=serials&sub=view&serial_id=$serial_id&view=abon";
					print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
						<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
						</form>
						<script type=\"text/javascript\">document.dummy.submit();</script>
						</div>";	
				}						
			break;
			default:
				print $this->show_form();
				break;
		}
	}
}

class abts_abonnements {
	
	var $abonnements = array(); //Tableau des IDs des modèles
	
    function abts_abonnements($id_perio,$localisation=0) {
    	if($localisation > 0) $where_localisation=" and location_id = $localisation ";
    	$requete="select abt_id from abts_abts where num_notice=$id_perio $where_localisation order by abt_name";   	
    	$resultat=pmb_mysql_query($requete);
    	while ($r=pmb_mysql_fetch_object($resultat)) {
    		$abonnement=new abts_abonnement($r->abt_id);
    		if (!$abonnement->error) $this->abonnements[]=$abonnement;
    	}
    }
    
    function show_list() {
    	global $abonnement_list,$msg,$serial_id;
    	$r=$abonnement_list;
    	$abonnements="";
    	if (count($this->abonnements)) {
    		for ($i=0; $i<count($this->abonnements); $i++) {
    			$abonnements.=$this->abonnements[$i]->show_abonnement();
    		}
    	} else $abonnements=$msg["abts_abonnements_no_abonnement"];
    	
    	$resultat=pmb_mysql_query("select modele_id,modele_name from abts_modeles where num_notice='$serial_id'");	
		$cpt=0;
		while ($rp=pmb_mysql_fetch_object($resultat)) {		
			$cpt++;
		}	
		if($cpt)		
			$r=str_replace("!!abts_abonnements_add_button!!","<input type='button' class='bouton' value='".$msg["abts_abonnements_add_button"]."' onClick='document.location=\"catalog.php?categ=serials&sub=abon&serial_id=$serial_id\"'/>",$r);
		else $r=str_replace("!!abts_abonnements_add_button!!",$msg["abts_modeles_no_modele"],$r);
    	
    	return str_replace("!!abonnement_list!!",$abonnements,$r);
    }
}
/*
function calc_selection($val,$size)
{
	$ret='';
	for ($i=0; $i<$size; $i++) {
		if(!isset($val[$i+1])) $ret .='1'; else $ret .='0';
	}		
	return $ret;
}	

function sql_value($rqt)
{
	if($result=pmb_mysql_query($rqt))
		if($row = pmb_mysql_fetch_row($result))	return $row[0];
	return '';
}*/

function gen_plus_form($id,$titre,$contenu)
{
	return "	
	<div class='row'></div>
	<div id='$id' class='notice-parent'>
		<img src='./images/plus.gif' class='img_plus' name='imEx' id='$id"."Img' title='".addslashes($msg['plus_detail'])."' border='0' onClick=\"expandBase('$id', true); return false;\" hspace='3'>
		<span class='notice-heada'>
			$titre
		</span>
	</div>
	<div id='$id"."Child' class='notice-child' startOpen='Yes' style='margin-bottom:6px;display:none;width:94%'>
		$contenu
	</div>
	";
}

?>