<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relance.class.php,v 1.1.2.3 2019-11-27 15:21:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/amende.class.php");
require_once($class_path."/comptes.class.php");
require_once($class_path."/serie.class.php");
require_once ("$class_path/author.class.php");
require_once("$class_path/progress_bar.class.php");
require_once($class_path."/mail/reader/loans/mail_reader_loans_late_relance.class.php");

class relance {
	
    public static function get_action($id_empr,$niveau,$niveau_normal) {
        global $msg, $pmb_recouvrement_auto;
        $action="<input type='hidden' name='empr[]' value='".$id_empr."'>
	<select class='readers_relances_ui_action' name='action_".$id_empr."'>
	";
        $action.="<option value='-1'>".$msg["relance_do_nothing"]."</option>\n";
        
        //if ((($niveau==$niveau_normal)||(($niveau==3)&&($niveau_normal==4)))&&($niveau!=0)) {
        //	$action.="<option value='edit'>Editer la lettre</option>";
        //}
        if ($niveau>$niveau_normal) {
            $action.="<option value='$niveau_normal' ";
            $action.="selected";
            $action.=">".sprintf($msg["relance_back_level"],$niveau_normal)."</option>\n";
        }
        if ($niveau<$niveau_normal) {
            if ($niveau_normal==4) $nn=3; else $nn=$niveau_normal;
            if ($niveau==4) $nd=3; else $nd=$niveau+1;
            for ($i=$nd; $i<=$nn; $i++) {
                $action.="<option value='$i' ";
                if ($i==$nn) $action.="selected";
                $action.=">".sprintf($msg["relance_change_level"],$i)."</option>\n";
            }
            if ($niveau_normal==4) {
                $action.="<option value='4' ";
                if (($niveau==3) && ($pmb_recouvrement_auto)) $action.=" selected";
                $action.=">".$msg["relance_go_recouvr"]."</option>\n";
            }
        }
        $action.="</select>
	";
        return $action;
    }
    
    public static function do_action($id_empr) {
        global $pmb_gestion_amende, $lang, $include_path;
        global $finance_recouvrement_lecteur_statut;
        $action="action_".$id_empr;
        global ${$action},$msg,$finance_statut_perdu;
        $act=${$action};
        
        //R�cup�ration du solde du compte
        $frais_relance=0;
        $id_compte=comptes::get_compte_id_from_empr($id_empr,2);
        if ($id_compte) {
            $cpte=new comptes($id_compte);
            $frais_relance=$cpte->summarize_transactions("","",0,$realisee=-1);
            if (($frais_relance)&&($frais_relance<0)) {
                $frais_relance=-$frais_relance;
            } else $frais_relance=0;
        }
        
        //Si action diff�rent de -1, alors changement
        $quatre=false;
        if ($act!=-1) {
            //R�cup�ration de la liste des pr�ts
            $amende=new amende($id_empr);
            // on efface le cache pour qu'il soit remis � jour au prochain acc�s
            $req="delete from cache_amendes where id_empr=$id_empr ";
            pmb_mysql_query($req);
            
            $montant_total=0;
            for ($j=0; $j<count($amende->t_id_expl); $j++) {
                $params=$amende->t_id_expl[$j];
                //Si c'est juste un changement de niveau
                if ($act<4) {
                    //Si il y a attente de changement d'�tat
                    if ($params["amende"]["niveau_relance"]<$params["amende"]["niveau"]) {
                        //Si le niveau attendu est sup�rieur ou �gal au niveau demand�
                        if ($params["amende"]["niveau"]>=$act) {
                            //On passe au niveau demand�
                            $niveau=$act;
                        } else {
                            //Sinon on passe au niveau pr�vu
                            $niveau=$params["amende"]["niveau"];
                        }
                        //Enregistrement du changement de niveau
                        $requete="update pret set niveau_relance=$niveau, date_relance=now(), printed=0 where pret_idempr=$id_empr and pret_idexpl=".$params["id_expl"];
                        
                        pmb_mysql_query($requete);
                    }
                    //Si le niveau suppos� est inf�rieur au dernier niveau valid� (ex : prolongations,...), on peut revenir..
                    if ($params["amende"]["niveau"]<$params["amende"]["niveau_relance"]) {
                        if ($params["amende"]["niveau_relance"]>=$act) {
                            //On passe au niveau demand�
                            $niveau=$act;
                        } else {
                            //Sinon on passe au niveau pr�vu
                            $niveau=$params["amende"]["niveau"];
                        }
                        //Enregistrement du changement de niveau
                        $requete="update pret set niveau_relance=$niveau, date_relance=now(), printed=0 where pret_idempr=$id_empr and pret_idexpl=".$params["id_expl"];
                        
                        pmb_mysql_query($requete);
                    }
                } else {
                    //Sinon, c'est plus grave, on passe en recouvrement !!
                    $quatre=true;
                    //Si niveau pr�vu = 4
                    if ($params["amende"]["niveau"]==4) {
                        //Passage des ouvrages en statut perdu
                        $requete="update exemplaires set expl_statut=$finance_statut_perdu where expl_id=".$params["id_expl"];
                        pmb_mysql_query($requete);
                        //D�bit du compte lecteur + tarif des relances
                        $debit=$amende->get_amende($params["id_expl"]);
                        $debit=$debit["valeur"];
                        $id_compte=comptes::get_compte_id_from_empr($id_empr,2);
                        if ($id_compte) { //&&($debit)
                            $compte=new comptes($id_compte);
                            //Enregistrement transaction
                            $id_transaction=$compte->record_transaction("",$debit,-1,sprintf($msg["relance_recouvr_transaction"],$params["id_expl"]),0);
                            //Validation
                            $compte->validate_transaction($id_transaction);
                            $montant_total+=$debit;
                            
                            $requete="select pret_date from pret where pret_idexpl=".$params["id_expl"];
                            $resultat=pmb_mysql_query($requete);
                            $r=pmb_mysql_fetch_object($resultat);
                            $req_pret_date= ", date_pret='".$r->pret_date."' ";
                            
                            $requete="select  log.date_log as date_log, log.niveau_reel as niv
						from log_expl_retard as expl,log_retard as log
						where expl.num_log_retard=log.id_log and  log.idempr=$id_empr and expl.expl_id=".$params["id_expl"] ." order by log.date_log limit 3";
                            
                            $res=pmb_mysql_query($requete);
                            $req_date_relance="";
                            $i=1;
                            while($log = pmb_mysql_fetch_object($res)){
                                $req_date_relance.= ", date_relance".$i++."='".$log->date_log."' ";
                            }
                            
                            $requete="insert into recouvrements set empr_id=$id_empr, id_expl=".$params["id_expl"].", date_rec= now(), libelle='',recouvr_type=0, montant='$debit' $req_pret_date $req_date_relance";
                            pmb_mysql_query($requete);
                            
                            // Essayer de retrouver le prix de l'exemplaire
                            $requete="select expl_prix, prix from exemplaires, notices where (notice_id=expl_notice or notice_id=expl_bulletin) and expl_id =".$params["id_expl"];
                            $resultat=pmb_mysql_query($requete);
                            $prix=0;
                            if ($r = pmb_mysql_fetch_object($resultat)) {
                                $tmp_expl_prix = (int) str_replace(',', '.', $r->expl_prix);
                                $tmp_prix = (int) str_replace(',', '.', $r->prix);
                                if (!$prix = $tmp_expl_prix) {
                                    $prix = $tmp_prix;
                                }
                            }
                            $requete="insert into recouvrements set empr_id=$id_empr, id_expl=".$params["id_expl"].", date_rec=now(), libelle='', recouvr_type=1, montant='$prix' $req_pret_date $req_date_relance";
                            pmb_mysql_query($requete);
                            
                            // on modifie le status du lecteur si demand�
                            if($finance_recouvrement_lecteur_statut){
                                $requete="update empr set empr_statut=$finance_recouvrement_lecteur_statut where id_empr=$id_empr";
                                pmb_mysql_query($requete);
                            }
                        }
                        
                        //Supression du pret
                        $requete="delete from pret where pret_idexpl=".$params["id_expl"];
                        pmb_mysql_query($requete);
                        $requete="update exemplaires set expl_note=concat(expl_note,' ','".$msg["relance_non_rendu_expl"]."'),expl_lastempr='".$id_empr."' where expl_id=".$params["id_expl"];
                        pmb_mysql_query($requete);
                        $requete="update empr set empr_msg=trim(concat(empr_msg,' ','".addslashes($msg["relance_recouvrement"])."')) where id_empr=".$id_empr;
                        pmb_mysql_query($requete);
                    }
                }
                
                //Ajout solde du compte amendes
                if ($quatre) {
                    if ($frais_relance) {
                        $requete="insert into recouvrements (empr_id,id_expl,date_rec,libelle,montant) values($id_empr,0,now(),'".$msg["relance_frais_relance"]."',".$frais_relance.")";
                        pmb_mysql_query($requete);
                        $montant_total+=$frais_relance;
                    }
                    
                    //Passage en perte pour la biblioth�que
                    //D�bit sur le compte 0
                    //if ($montant_total) {
                    //	$requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,date_effective,montant,sens,realisee,commentaire,encaissement) values(0,$PMBuserid,'".$PMBusername."','".$_SERVER["REMOTE_ADDR"]."', now(), now(), now(), ".($montant_total*1).", -1, 1,'Recouvrement lecteur : ".$params["id_expl"]."',0)";
                    //	pmb_mysql_query($requete);
                    //}
                }
            }
            //Traitement des frais
            $niveau_min=$act;
            $the_frais = 0;
            if ($pmb_gestion_amende == 1) {
                $frais="finance_relance_".$niveau_min;
                global ${$frais};
                $the_frais = ${$frais};
            }
            else {
                $quota_name = "";
                switch ($niveau_min) {
                    case 1:
                        $quota_name="AMENDERELANCE_FRAISPREMIERERELANCE";
                        break;
                    case 2:
                        $quota_name="AMENDERELANCE_FRAISDEUXIEMERELANCE";
                        break;
                    case 3:
                        $quota_name="AMENDERELANCE_FRAISTROISIEMERELANCE";
                        break;
                    default:
                        break;
                }
                $qt = new quota($quota_name, "$include_path/quotas/own/$lang/finances.xml");
                $struct["READER"] = $id_empr;
                $the_frais = $qt -> get_quota_value($struct);
                if ($the_frais == -1) $the_frais = 0;
            }
            
            if($the_frais){
                if ($id_compte) {
                    $compte=new comptes($id_compte);
                    //Enregistrement transaction
                    $cpte->record_transaction("",$the_frais,-1,sprintf($msg["relance_frais_relance_level"],$niveau_min));
                }
            }
        }
    }
    
    public static function filter_niveau($liste_ids,$champ,$selected = array(),$sort=false) {
        global $all_level,$late_ids;
        $ret="";
        $t=array();
        $v=array();
        
        //Recherche des lecteurs en retard
        if (!$late_ids) {
            $requete="select distinct pret_idempr from pret where pret_retour<	CURDATE() and pret_idempr in (".implode(",",$liste_ids).")";
            $res_id=pmb_mysql_query($requete);
            if (($res_id)&&(pmb_mysql_num_rows($res_id))) {
                while ($r=pmb_mysql_fetch_object($res_id)) {
                    $late_ids[$r->pret_idempr]=1;
                }
            } else $late_ids=array();
        }
        
        for ($i=0;$i<=count($liste_ids)-1;$i++) {
            if (isset($late_ids[$liste_ids[$i]])) {
                $amende=new amende($liste_ids[$i]);
                $level=$amende->get_max_level();
                $t[$liste_ids[$i]]=$level[$champ];
                $v[$liste_ids[$i]]=$level;
            }
        }
        if ($all_level) {
            $liste_ids=array_keys($all_level);
            //afin de g�rer les filtres combin�s..
            if (($selected)&&(is_array($selected))) {
                if (!(count($selected) == 1 && ($selected[0] == -1))) {
                    $all_level=array(0);
                }
            }
        }
        
        for ($i=0;$i<=count($liste_ids)-1;$i++) {
            if (($selected)&&(is_array($selected)) && !empty($v[$liste_ids[$i]])) {
                $as=array_search($v[$liste_ids[$i]][$champ],$selected);
                if (($as!==FALSE)&&($as!==NULL)) $all_level[$liste_ids[$i]]=$v[$liste_ids[$i]];
            }
        }
        if ($sort==true) sort($t[$champ],SORT_NUMERIC);
        $result=array_unique($t);
        sort($result,SORT_NUMERIC);
        for ($i=0;$i<=count($result)-1;$i++) {
            if ($result[$i]!=0) {
                $ret.="<option value='".$result[$i]."'";
                if (($selected)&&(is_array($selected))) {
                    $as=array_search($result[$i],$selected);
                    if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                }
                $ret.=">".$result[$i]."</option>";
            }
        }
        return $ret;
    }
    
    public static function filter_loc_expl($liste_ids, $champ, $selected = array(), $sort = false){
        global $dbh,$charset;
        
        global $all_level;
        $ret="";
        
        if((($selected)&&(is_array($selected))) && !(count($selected) == 1 && ($selected[0] == -1))){
            if(is_array($all_level) && count($all_level)){//Je repart des bons lecteurs
                $liste_ids=array_keys($all_level);
            }
            
            $requete="SELECT pret_idempr FROM pret JOIN exemplaires ON pret_idexpl=expl_id JOIN docs_location ON expl_location=idlocation WHERE location_libelle IN ('".implode("','",$selected)."') ";
            if(count($liste_ids)){
                $requete.="AND pret_idempr IN (".implode(",",$liste_ids).")";
            }
            $requete.=" GROUP BY pret_idempr";
            $res=pmb_mysql_query($requete,$dbh);
            if($res && pmb_mysql_num_rows($res)){
                $new_all_level=array(0);
                while ($empr=pmb_mysql_fetch_object($res)) {
                    if($all_level[$empr->pret_idempr]){
                        $new_all_level[$empr->pret_idempr]=$all_level[$empr->pret_idempr];
                    }else{
                        $amende=new amende($empr->pret_idempr);
                        $new_all_level[$empr->pret_idempr]=$amende->get_max_level();
                    }
                    
                }
                $all_level=$new_all_level;
            }else{
                $all_level=array(0);
            }
        }
        
        if(!is_array($selected)){
            $selected=array();
        }
        $requete="SELECT idlocation, location_libelle FROM docs_location GROUP BY location_libelle ORDER BY location_libelle";
        $res=pmb_mysql_query($requete,$dbh);
        if($res && pmb_mysql_num_rows($res)){
            while ($ligne=pmb_mysql_fetch_object($res)) {
                $ret.="<option value='".htmlentities($ligne->location_libelle,ENT_QUOTES,$charset)."'";
                if(in_array($ligne->location_libelle,$selected)) $ret.=" selected";
                $ret.=">".htmlentities($ligne->location_libelle,ENT_QUOTES,$charset)."</option>";
            }
        }
        return $ret;
    }
	
} // fin de d�claration de la classe relance