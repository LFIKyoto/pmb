<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.class.php,v 1.31 2015-07-16 09:37:06 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/serialcirc.inc.php");
require_once($include_path."/templates/serialcirc.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/resa.class.php");
require_once($class_path."/serialcirc_print_fields.class.php");
@ini_set('zend.ze1_compatibility_mode',0);
require_once($include_path."/h2o/h2o.php");
require_once("$class_path/aut_link.class.php");

class serialcirc {
	var $info_expl=array();		
	var $classement=array();
	var $info_copy=array();
	var $info_resa=array();
	var $info_circ=array();
	var $id_location=0;
	
	function serialcirc($id_location) {
		global $pmb_lecteurs_localises,$deflt_docs_location;
		$this->id_location=0;
		if($pmb_lecteurs_localises){
			$id_location+=0;
			if(!$id_location)$id_location=$deflt_docs_location;
			$this->id_location=$id_location;
		}	
		$this->fetch_data();
	}
	
	function fetch_data() {
		$this->info_expl=array();
		$this->info_circ=array();
		$this->classement['alert']=array();
		$this->classement['to_be_circ']=array();
		$this->classement['in_circ']=array();
		$this->classement['retard']=array();
		$this->classement['reproduction_ask']=array();
		$this->classement['is_in_resa_ask']=array();		
		if($this->id_location) $restrict=" and expl_location=".$this->id_location;
		$req="select * from serialcirc_expl,exemplaires,bulletins where expl_id=num_serialcirc_expl_id and expl_bulletin=bulletin_id $restrict";
		$resultat=pmb_mysql_query($req);	
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info_expl[$r->expl_id]['expl_cb']= $r->expl_cb;	
				$this->info_expl[$r->expl_id]['expl_id']= $r->expl_id;				
				$this->info_expl[$r->expl_id]['expl_statut']= $r->expl_statut;
				$this->info_expl[$r->expl_id]['expl_location']= $r->expl_location;
				
				
				$rqtSite = "SELECT location_libelle FROM docs_location WHERE idlocation=".$r->expl_location;
				$resSite = pmb_mysql_result(pmb_mysql_query($rqtSite),0);
				$this->info_expl[$r->expl_id]['expl_location_name']= $resSite;
				
				$this->info_expl[$r->expl_id]['expl_cote']= $r->expl_cote;
				
				$this->info_expl[$r->expl_id]['bulletine_date']= $r->serialcirc_expl_bulletine_date;
				$this->info_expl[$r->expl_id]['num_diff']= $r->num_serialcirc_expl_serialcirc_diff;	
				$this->info_expl[$r->expl_id]['expl_abt_num']= $r->expl_abt_num;							
				
				$this->info_expl[$r->expl_id]['numero']= $r->bulletin_numero;
				$this->info_expl[$r->expl_id]['mention_date']= $r->mention_date;
				$this->info_expl[$r->expl_id]['bulletin_notice']= $r->bulletin_notice;
				$this->info_expl[$r->expl_id]['bulletin_id']= $r->bulletin_id;
				$this->info_expl[$r->expl_id]['num_notice']= $r->num_notice;	
				$this->info_expl[$r->expl_id]['expl_link']="./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=".$r->bulletin_id."&expl_id=".$r->expl_id;			
				$this->info_expl[$r->expl_id]['bull_link']="./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$r->bulletin_id;
				$this->info_expl[$r->expl_id]['serial_link']="./catalog.php?categ=serials&sub=view&serial_id=".$r->bulletin_notice;
				$this->info_expl[$r->expl_id]['abt_link']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r->expl_abt_num;
				$this->info_expl[$r->expl_id]['cirdiff_link']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r->expl_abt_num;
				$this->info_expl[$r->expl_id]['view_link']='./circ.php?categ=visu_ex&form_cb_expl='.$r->expl_cb;			
					
				$req_serial="select * from notices  where notice_id=".$r->bulletin_notice."";
				$res_serial=pmb_mysql_query($req_serial);	
				if ($r_serial=pmb_mysql_fetch_object($res_serial)){	
					$this->info_expl[$r->expl_id]['serial_title']=$r_serial->tit1;					
				}				
				
				$this->info_expl[$r->expl_id]['num_serialcirc']= $r->num_serialcirc_expl_serialcirc;
				$this->info_expl[$r->expl_id]['serialcirc_diff'] = new serialcirc_diff($r->num_serialcirc_expl_serialcirc);
				$this->info_expl[$r->expl_id]['state_circ']= $r->serialcirc_expl_state_circ;
			//	$this->info_expl[$r->expl_id]['diff']= $r->num_serialcirc_expl_serialcirc_diff;
				$this->info_expl[$r->expl_id]['current_empr']= $r->num_serialcirc_expl_current_empr;				
				$this->info_expl[$r->expl_id]['start_date']= $r->serialcirc_expl_start_date;
			
				$this->fetch_info_circ($r->expl_id);
				
				if($this->is_in_alert($r->expl_id)) $this->classement['alert'][]=$r->expl_id;
				else if($this->is_in_to_be_circ($r->expl_id)) $this->classement['to_be_circ'][]=$r->expl_id;
				if($this->is_in_circ($r->expl_id)) $this->classement['in_circ'][]=$r->expl_id;
				if($this->is_in_late($r->expl_id)) $this->classement['retard'][]=$r->expl_id;
				if($this->is_in_reproduction_ask($r->expl_id)) $this->classement['reproduction_ask'][]=$r->expl_id;
				if($this->is_in_resa_ask($r->expl_id)) $this->classement['is_in_resa_ask'][]=$r->expl_id;
				
				$this->info_expl[$r->expl_id]['circ']=array();
				$req_circ="select * from serialcirc_circ where num_serialcirc_circ_expl =".$r->expl_id;
				$resultat_circ=pmb_mysql_query($req_circ);	
				if (pmb_mysql_num_rows($resultat_circ)) {
					while($r_circ=pmb_mysql_fetch_object($resultat_circ)){	
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['subscription']=$r_circ->serialcirc_circ_subscription;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['ret_asked ']=$r_circ->serialcirc_circ_ret_asked ;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['trans_asked ']=$r_circ->serialcirc_circ_trans_asked ;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['trans_doc_asked']=$r_circ->serialcirc_circ_trans_doc_asked;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['expected_date']=$r_circ->serialcirc_circ_expected_date;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['pointed_date']=$r_circ->serialcirc_circ_pointed_date;
						if($this->info_expl[$r->expl_id]['serialcirc_diff']->virtual_circ && !$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['subscription']==0){
							$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['no_subscription']=1;
						}
						else {
							$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['no_subscription']=0;
						}	
					}
				}
			}
		}
		$this->fetch_data_copy() ;	
		$this->fetch_data_resa() ;
		// printr($this->info_expl);
		// print"<pre>";print_r($this->info_expl);print_r($this->classement);print"</pre>";
	}
	
	function fetch_data_copy(){
		global $opac_url_base;
		$this->info_copy=array();		
		$req="select * from serialcirc_copy ,bulletins, notices where num_serialcirc_copy_bulletin=bulletin_id and bulletin_notice=notice_id order by serialcirc_copy_date ";
		$resultat=pmb_mysql_query($req);
		$i=0;
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){				
				$this->info_copy[$i]['id']=$r->id_serialcirc_copy;
				$this->info_copy[$i]['id_empr']=$r->num_serialcirc_copy_empr;
				$this->info_copy[$i]['empr']=$this->empr_info($this->info_copy[$i]['id_empr']);
				$this->info_copy[$i]['id_bulletin']=$r->num_serialcirc_copy_bulletin;
				$this->info_copy[$i]['perio']=$r->tit1;
				$this->info_copy[$i]['numero']= $r->bulletin_numero;
				$this->info_copy[$i]['mention_date']= $r->mention_date;
				$this->info_copy[$i]['analysis']=$r->serialcirc_copy_analysis;
				$this->info_copy[$i]['date']=$r->serialcirc_copy_date;
				$this->info_copy[$i]['state']=$r->serialcirc_copy_state;
				$this->info_copy[$i]['comment']=$r->serialcirc_copy_comment;	
				$this->info_copy[$i]['serial_link']="./catalog.php?categ=serials&sub=view&serial_id=".$r->notice_id;	
				$this->info_copy[$i]['bull_link']="./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$r->bulletin_id;
				$this->info_copy[$i]['opac_link']=$opac_url_base."index.php?lvl=bulletin_display&id=".$r->bulletin_id;
				$this->index_info_copy[$r->id_serialcirc_copy]=	$this->info_copy[$i];					
				$i++;
			}
		}	
	}
	
	function fetch_data_resa(){
		
		$this->info_resa=array();		
		$req="select * from serialcirc_circ, exemplaires, bulletins, notices where serialcirc_circ_hold_asked=1 and num_serialcirc_circ_expl=expl_id and expl_bulletin=bulletin_id and bulletin_notice=notice_id order by num_serialcirc_circ_expl,serialcirc_circ_order ";
		$resultat=pmb_mysql_query($req);
		$i=0;
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){				
				$this->info_resa[$i]['id']=$r->id_serialcirc_circ;
				$this->info_resa[$i]['id_empr']=$r->num_serialcirc_circ_empr;
				$this->info_resa[$i]['id_expl']=$r->num_serialcirc_circ_expl;
				$this->info_resa[$i]['empr']=$this->empr_info($this->info_resa[$i]['id_empr']);
				$this->info_resa[$i]['id_bulletin']=$r->bulletin_id;
				$this->info_resa[$i]['perio']=$r->tit1;
				$this->info_resa[$i]['numero']= $r->bulletin_numero;
				$this->info_resa[$i]['mention_date']= $r->mention_date;						
				$i++;	
			}
		}
		
	}	
	function fetch_info_circ($id_expl){
		$this->info_circ[$id_expl]=array();		
		$req="select *,DATEDIFF(serialcirc_circ_expected_date,CURDATE())as late_diff from serialcirc_circ where num_serialcirc_circ_expl=$id_expl order by serialcirc_circ_order ";
		$resultat=pmb_mysql_query($req);
		
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){				
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['id']=$r->id_serialcirc_circ;			
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_diff']=$r->num_serialcirc_circ_diff;	
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_expl']=$r->num_serialcirc_circ_expl;			
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_empr']=$r->num_serialcirc_circ_empr;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_serialcirc']=$r->num_serialcirc_circ_serialcirc;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['order']=$r->serialcirc_circ_order;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['subscription']=$r->serialcirc_circ_subscription;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['hold_asked']=$r->serialcirc_circ_hold_asked;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['ret_asked']=$r->serialcirc_circ_ret_asked;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['trans_asked']=$r->serialcirc_circ_trans_asked;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['doc_asked']=$r->serialcirc_circ_trans_doc_asked;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['expected_date']=$r->serialcirc_circ_expected_date;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['pointed_date']=$r->serialcirc_circ_pointed_date;		
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['late_diff']=$r->late_diff;
				if($this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['late_diff'] <0 && !$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['pointed_date']){
					$this->info_expl[$id_expl]['is_late']=1;
				}else{
					$this->info_expl[$id_expl]['is_late']=0;
				}				
				
			}			
		}
	//	$this->get_next_diff_id($id_expl);	
		return $this->info_circ[$id_expl];
	}	
		
	function empr_info($id){
		global $dbh;
		$info=array();
		$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail from empr where id_empr=".$id;
		$res_empr=pmb_mysql_query($req);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {			
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom; 
			$info['prenom'] = $empr->empr_prenom;  
			$info['mail'] = $empr->empr_mail;  		
			$info['id_empr']=$id;	
			$info['view_link']='./circ.php?categ=pret&form_cb='.$empr->empr_cb;
			$info['empr_libelle']=$info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
		}
		return $info;
	}		
	
	function expl_info($id){
		global $dbh;
		$info=array();
		$req="select * from exemplaires, bulletins, notices where expl_id=$id and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
		$resultat=pmb_mysql_query($req);
				
		if($r=pmb_mysql_fetch_object($resultat)){				
			$info['id']=$id;				
			$info['cb']=$r->expl_cb;			
			$info['id_bulletin']=$r->num_serialcirc_copy_bulletin;
			$info['perio']=$r->tit1;
			$info['numero']= $r->bulletin_numero;
			$info['mention_date']= $r->mention_date;				
			$info['view_link']='./circ.php?categ=visu_ex&form_cb_expl='.$r->expl_cb;		
		}		
		return $info;
	}	
	
	function is_in_alert($expl_id){
		if($this->info_expl[$expl_id]['serialcirc_diff']->virtual_circ){
			if( $this->info_expl[$expl_id]['start_date']=="0000-00-00")$start_date=$this->info_expl[$expl_id]['bulletine_date'];
			else $start_date=$this->info_expl[$expl_id]['start_date'];
			$req="select DATEDIFF(DATE_ADD('".$start_date."',	INTERVAL ".$this->info_expl[$expl_id]['serialcirc_diff']->duration_before_send." DAY),CURDATE())";
			
			$result=pmb_mysql_query($req);
			if($row = pmb_mysql_fetch_row($result)) {
				if($row[0]>0){	
					return true;
				}
			}
		}
		return false;
	}	
	
	function is_alerted($expl_id){
		//if($this->is_in_alert($expl_id)) return false;
		if($this->info_expl[$expl_id]['start_date']!="0000-00-00")	return true;

		return false;
	}
		
	function is_in_to_be_circ($expl_id){	
		if($this->is_in_alert($expl_id) && $this->is_alerted($expl_id))	 return false;
		if(!$this->info_expl[$expl_id]['state_circ'] && !$this->info_expl[$expl_id]['num_diff']){
			return true;
		}	

		return false;
	}
	
	function is_in_circ($expl_id){
		if($this->info_expl[$expl_id]['num_diff']){
			return true;			
		}
		return false;
	}

	function empr_is_subscribe($empr_id, $expl_id){		
		if( !$this->info_expl[$expl_id]['circ'][$empr_group['num_empr'] ]){
			return true;			
		} elseif( !$this->info_expl[$expl_id]['circ'][$empr_group['num_empr'] ]['no_subscription']){
			return true;
		}
		return false;
	}	

	function is_in_late($expl_id){
		if(!$this->info_expl[$expl_id]['serialcirc_diff']->checked) return false;	
		if( $this->info_expl[$expl_id]['start_date']=="0000-00-00") return false;	
		return $this->info_expl[$expl_id]['is_late'];
	}
	
	function is_in_reproduction_ask($expl_id){
		if(!$this->info_expl[$expl_id]['state_circ'] && !$this->info_expl[$expl_id]['num_diff']){
			return true;
		}					
	}
	
	function is_in_resa_ask($expl_id){
		
	}

	function delete_diffusion($expl_id){
		global $dbh;
		
		$status=1;
		if (!$this->info_expl[$expl_id]) return 0;
		// Traitement des résa
		$req="select num_serialcirc_circ_empr from serialcirc_circ where serialcirc_circ_hold_asked=2 and num_serialcirc_circ_expl=$expl_id
		order by serialcirc_circ_order";
		$res=pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)) {
				$resa=new reservation($r->num_serialcirc_circ_empr,0,$this->info_expl[$expl_id]['bulletin_id']);
				$resa->add();
			}
		}
		$req="delete from serialcirc_expl where num_serialcirc_expl_id =$expl_id";
		pmb_mysql_query($req);		
		$req="delete from serialcirc_circ where num_serialcirc_circ_expl =$expl_id";
		pmb_mysql_query($req);
		
		// on change le statut si demandé
		if($this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ_after){
			$req="update exemplaires set expl_statut=".$this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ_after." where expl_id=".$expl_id;
			pmb_mysql_query($req);	
		}
		
		// traitement résa 
		$query = "select count(1) from resa where resa_idbulletin=".$this->info_expl[$expl_id]['bulletin_id'];
		$result = @pmb_mysql_query($query, $dbh);
		if(@pmb_mysql_result($result, 0, 0)) {
			$status=2;// mail de résa sera envoyé à l'affectation dans résa à traiter
		}
		return $status;
	}

	static function delete_expl($expl_id){
		$req="delete from serialcirc_expl where num_serialcirc_expl_id =$expl_id";
		pmb_mysql_query($req);		
		$req="delete from serialcirc_circ where num_serialcirc_circ_expl =$expl_id";
		pmb_mysql_query($req);
	}
	
	function copy_accept($copy_id){	
		global $serialcirc_copy_accepted_mail,$msg,$biblio_name, $biblio_email,$PMBuseremailbcc;
		
		if(!$this->index_info_copy[$copy_id]) return false;			
		$copy=$this->index_info_copy[$copy_id];		
		$texte_mail=$serialcirc_copy_accepted_mail;
		$texte_mail=str_replace("!!issue!!", $copy['perio']."-".$copy['numero'], $texte_mail);			
		mailpmb($copy['empr']["prenom"]." ".$copy['empr']["nom"], $copy['empr']["mail"],	$msg["serialcirc_circ_title"],	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
		
		$req="update serialcirc_copy set serialcirc_copy_state=1  where id_serialcirc_copy=$copy_id ";
		pmb_mysql_query($req);					
		return true;
	}		
	
	function copy_isdone($bul_id){	
		global $serialcirc_copy_isdone_mail,$msg,$biblio_name, $biblio_email,$PMBuseremailbcc;
		$req="select * from serialcirc_copy where num_serialcirc_copy_bulletin=$bul_id ";
		$resultat=pmb_mysql_query($req);		
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				// envoit des mails
				if($copy=$this->index_info_copy[$r->id_serialcirc_copy]){		
					$texte_mail=$serialcirc_copy_isdone_mail;
					$texte_mail=str_replace("!!issue!!", $copy['perio']."-".$copy['numero'], $texte_mail);		
					$texte_mail=str_replace("!!see!!", "<a href='".$copy['opac_link']."'>".$copy['numero']."</a>", $texte_mail);		
					mailpmb($copy['empr']["prenom"]." ".$copy['empr']["nom"], $copy['empr']["mail"], $msg["serialcirc_circ_title"],	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
				}				
			}
			// on efface 
			$req="delete from serialcirc_copy where num_serialcirc_copy_bulletin=$bul_id ";
			$resultat=pmb_mysql_query($req);
		}		
	}

	function copy_none($copy_id){
		global $serialcirc_copy_no_mail,$msg,$biblio_name, $biblio_email,$PMBuseremailbcc;		
			
		if(!$this->index_info_copy[$copy_id]) return false;			
		$req="delete from serialcirc_copy where id_serialcirc_copy=$copy_id";
		pmb_mysql_query($req);		
		$copy=$this->index_info_copy[$copy_id];		
		$texte_mail=$serialcirc_copy_no_mail;
		$texte_mail=str_replace("!!issue!!", $copy['perio']."-".$copy['numero'], $texte_mail);			
		mailpmb($copy['empr']["prenom"]." ".$copy['empr']["nom"], $copy['empr']["mail"], $msg["serialcirc_circ_title"],	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
				
		return true;
	}	
	
	function ask_send_mail($expl_id,$empr_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		$expl_info=$this->expl_info($expl_id);
		$empr_info=$this->empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);			
		mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);	
		return true;
	}
	
	function resa_accept($expl_id,$empr_id){		
	
		$req="select * from bulletins, exemplaires where bulletin_id=expl_bulletin and expl_id=$expl_id";
		$res=pmb_mysql_query($req);
		if ($r=pmb_mysql_fetch_object($res)) {		
//			$resa=new reservation($empr_id,0,$r->bulletin_id);
//			$resa->add();
			$req="update serialcirc_circ set serialcirc_circ_hold_asked=2 where
			num_serialcirc_circ_expl=$expl_id and num_serialcirc_circ_empr=$empr_id";
			$res=pmb_mysql_query($req);			
		}
		// mail de résa sera envoyé à l'affectation dans résa à traité			
		return true;
	}	
	
	function resa_none($expl_id,$empr_id){
		global $serialcirc_resa_no_mail,$msg;			
		$req="update serialcirc_circ set serialcirc_circ_hold_asked=0 where
		num_serialcirc_circ_expl=$expl_id and num_serialcirc_circ_empr=$empr_id";
		$res=pmb_mysql_query($req);
			
		// mail 
		$this->ask_send_mail($expl_id,$empr_id,$msg["serialcirc_circ_title"],$serialcirc_resa_no_mail);		
		return true;
	}	
	function get_all_next_empr_id($expl_id){
	
	}
	function get_next_diff_id($expl_id){
		$found=0;	
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $id_diff => $diffusion){
			// pas en circ on retourne le premier
			if(!$this->info_expl[$expl_id]['num_diff']) return $id_diff;			
			if($id_diff==$this->info_expl[$expl_id]['num_diff'])$found=1; 
			elseif( $found ){
				return $id_diff;
			}
		}
		// le dernier l'a consulté; pas de suivant;
		return 0;		
	}
	

	// l'exemplaire revient à la bib
	function return_expl($expl_id){
		global $msg;
		
		if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_rotative){
			// delete et changement de statut éventuel
			$status=$this->delete_diffusion($expl_id);
		}else{// SERIALCIRC_TYPE_star
			// envoi au empr suivant
			if($next_diff_id=$this->get_next_diff_id($expl_id)){	
				$req="UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc_diff=".$next_diff_id.",
				serialcirc_expl_state_circ=1,
				serialcirc_expl_ret_asked=0,
				serialcirc_expl_trans_asked=0,
				serialcirc_expl_trans_doc_asked=0,
				num_serialcirc_expl_current_empr=0
				where num_serialcirc_expl_id= $expl_id";
				
				pmb_mysql_query($req);
				$status=2;
			}else{
				// C'est terminé!
				$status=$this->delete_diffusion($expl_id);
			}	
		}
		switch($status){
			case "2":
				$info=$msg["circ_retour_ranger_resa"];
				break;
			default://On ne mets pas de message différent si l'exemplaire a déjà été retourné
				$info=$msg["serialcirc_info_retour"];
				break;
		}
		return $info;
	}
		
	function print_diffusion($expl_id,$start_diff_id){	
		$tpl=$this->build_print_diffusion($expl_id,$start_diff_id);		
		global $class_path;
		require_once($class_path.'/html2pdf/html2pdf.class.php');	
	    $html2pdf = new HTML2PDF('P','A4','fr');
	    $html2pdf->WriteHTML($tpl);
	    $html2pdf->Output('diffusion.pdf');		
	}
	
	function print_sel_diffusion($list){
		foreach($list as $circ){		
			$expl_id=$circ['expl_id'];
			$start_diff_id=$circ['start_diff_id'];

			$tpl.=$this->build_print_diffusion($expl_id,$start_diff_id);
		}	
		global $class_path;
		require_once($class_path.'/html2pdf/html2pdf.class.php');	
	    $html2pdf = new HTML2PDF('P','A4','fr');
	    $html2pdf->WriteHTML($tpl);
	    $html2pdf->Output('diffusion.pdf');				
	}
	
	function build_print_diffusion($expl_id,$start_diff_id){
		global $serialcirc_circ_pdf_diffusion,$charset,$serialcirc_circ_pdf_diffusion_destinataire;
		global $msg;
		if(!$start_diff_id){
			foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
				$start_diff_id=$diff_id;
				break;
			}	
		}
		if (!$this->info_expl[$expl_id]) return false;
		$req="UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc_diff=".$start_diff_id.",
		serialcirc_expl_state_circ=1,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		$req="select date_format(CURDATE(), '".$msg["format_date"]."') as print_date";	
		$result = pmb_mysql_query($req);
		$obj = pmb_mysql_fetch_object($result);
		$print_date=$obj->print_date;
		
		$tpl = $serialcirc_circ_pdf_diffusion;
		$tpl=str_replace("!!expl_cb!!", htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset), $tpl);			
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset), $tpl);	
		$tpl=str_replace("!!periodique!!", htmlentities($this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);	
		$tpl=str_replace("!!numero!!", htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset), $tpl);		
		$tpl=str_replace("!!print_date!!", htmlentities($print_date,ENT_QUOTES,$charset), $tpl);	
	//	$tpl=str_replace("!!abonnement!!", htmlentities($this->info_expl[$expl_id]['serialcirc_diff']->abt_name,ENT_QUOTES,$charset), $tpl);	
		
		if($start_diff_id) $found=0;else $found=1;
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			
			if($start_diff_id && !$found){				
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				$diff_list[]=$diff_id;
			
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){					
					$name=$diffusion["empr_name"];
					
					foreach($diffusion['group'] as $empr_group){
						$empr_list[$empr_group["num_empr"]]=$diff_id;
						if($empr_group["duration"])
							$empr_days[$empr_group["num_empr"]]=$empr_group["duration"];
						else
							$empr_days[$empr_group["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
							
						if($diffusion['type_diff']==1 && !$empr_group["responsable"]){	
							// groupe marguerite: on n'imprimera pas ce lecteur sauf le responsable
							//$empr_no_display[$empr_group["num_empr"]]=1;
						}	
					}
				}else  {
					$name=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"];
					$empr_list[$diffusion["num_empr"]]=$diff_id;
					if($diffusion["duration"])	$empr_days[$diffusion["num_empr"]]=$diffusion["duration"]; // durée de consultation particulière
					else $empr_days[$diffusion["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
				}	
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_star){
					// on n'imprime que le suivant dans la liste
					break;
				}				
			}
			$last_empr=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]];
		}		
		$this->gen_circ($empr_list,$empr_days, $expl_id);
		
		$gen_tpl= new serialcirc_print_fields($this->info_expl[$expl_id]['num_serialcirc']);
		$header_list=$gen_tpl->get_header_list();	
		$nb_col=count($header_list);
		$width_col=(int) (100/$nb_col);
		
		foreach($header_list as $titre){
			$th.="<th style='width: $width_col%; text-align: left'>".htmlentities($titre,ENT_QUOTES,$charset)."</th>";
		}
		$tpl=str_replace("!!th!!", $th, $tpl);
		$tr_list="";
		foreach($empr_list as $empr_id=>$diff_id){
			if($empr_no_display[$empr_id]) continue;
			$data['empr_id']=$empr_id;
			$data_fields=$gen_tpl->get_line($data);		
			$td_list="";
			foreach($data_fields as $field){
				$td_list.="<td style='width: $width_col%; text-align: left'>".htmlentities($field,ENT_QUOTES,$charset)."</td>";	
			}
			$tr_list.="<tr>".$td_list."</tr>";	
		}
		$tpl=str_replace("!!table_contens!!", $tr_list, $tpl);	

		
		
		if($gen_tpl->piedpage){	
			$data=array();
			$data['expl']=$this->info_expl[$expl_id];
			$data['last_empr']=$last_empr;
//		 	printr($data['expl']);
			$tpl.=H2o::parseString($gen_tpl->piedpage)->render($data);
		}
		
		if($this->info_expl[$expl_id]['serialcirc_diff']->no_ret_circ){
			//pas de retour sur site, suppression de la circulation.
			$this->delete_diffusion($expl_id);
		}
		if($charset!="utf-8"){
			$tpl=utf8_encode($tpl);
		}
		return $tpl;
	}	
	
	function gen_circ($empr_list, $empr_days,$expl_id){
		$order=0;
		$nb_days=0;	
		if($this->info_expl[$expl_id]['serialcirc_diff']->virtual_circ){
			foreach($empr_list as $empr_id=>$diff_id){
				
				$req=" update serialcirc_circ SET 

				serialcirc_circ_expected_date=DATE_ADD(CURDATE(),INTERVAL $nb_days DAY)
				where 
				num_serialcirc_circ_diff=".$diff_id ." and
				num_serialcirc_circ_expl=".$expl_id ." and
				num_serialcirc_circ_empr=". $empr_id." and 
				serialcirc_circ_subscription=1 ";
				pmb_mysql_query($req);
				$order++;
				$nb_days+=$empr_days[$empr_id];
			}			
		}else{
			$req=" delete from serialcirc_circ where num_serialcirc_circ_expl=".$expl_id  ;
			pmb_mysql_query($req);	
			
			foreach($empr_list as $empr_id=>$diff_id){
				
				$req=" insert into serialcirc_circ SET 
				num_serialcirc_circ_diff=".$diff_id .",
				num_serialcirc_circ_expl=".$expl_id .",
				num_serialcirc_circ_empr=". $empr_id.",
				serialcirc_circ_subscription=1,
				serialcirc_circ_order=". $order.",
				serialcirc_circ_expected_date=DATE_ADD(CURDATE(),INTERVAL $nb_days DAY),
				num_serialcirc_circ_serialcirc=".$this->info_expl[$expl_id]['num_serialcirc'];
				pmb_mysql_query($req);
				$order++;
				$nb_days+=$empr_days[$empr_id];
			}
		}		
		// on change le statut si demandé
		if($this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ){
			$req="update exemplaires set expl_statut=".$this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ." where expl_id=".$expl_id;
			pmb_mysql_query($req);	
		}	
	}
	
	function send_mail($expl_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		if (!$this->info_expl[$expl_id]) return false;
		// Si pas encore recu par l'emprunteur on ne fait rien... 
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		$empr_info=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $this->info_expl[$expl_id]["serial_title"]." - ".$this->info_expl[$expl_id]['numero'], $texte_mail);			
		return mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
	}
	
	function send_alert($expl_id){
		global $serialcirc_send_alert_mail;
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		
		$req=" delete from serialcirc_circ where num_serialcirc_circ_expl=".$expl_id  ;
		pmb_mysql_query($req);	
		
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){

			$diff_list[]=$diff_id;
		
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){					
				foreach($diffusion['group'] as $empr_group){
					$empr_list[$empr_group["num_empr"]]=$diff_id;
				}
			}else  {
				$empr_list[$diffusion["num_empr"]]=$diff_id;
			}		
		}

		$req="UPDATE serialcirc_expl SET 
		serialcirc_expl_state_circ=0,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		$order=0;	
		foreach($empr_list as $empr_id=>$diff_id){

			$req=" insert into serialcirc_circ SET 
			num_serialcirc_circ_diff=".$diff_id .",
			num_serialcirc_circ_expl=".$expl_id .",
			num_serialcirc_circ_empr=". $empr_id.",
			serialcirc_circ_subscription=0,
			serialcirc_circ_order=". $order.",
			num_serialcirc_circ_serialcirc=".$this->info_expl[$expl_id]['num_serialcirc'];
			pmb_mysql_query($req);
			$order++;
			
			// envoit mail alert
			$texte_mail=$serialcirc_send_alert_mail;		
			$expl_info=$this->expl_info($expl_id);
			$empr_info=$this->empr_info($empr_id);
			$texte_mail=str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);			
			mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);	
			
			
		}	

	}	
	
	function call_expl($expl_id){
		global $mail_, $serialcirc_call_mail,$msg;
		
		$req="UPDATE serialcirc_expl SET 
		serialcirc_expl_ret_asked=1
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET 
		serialcirc_circ_ret_asked = serialcirc_circ_ret_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);

		$objet=$msg["serialcirc_circ_title"];
		$texte_mail=$serialcirc_call_mail;
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}
	
	function call_insist($expl_id){
		global $mail_ ,$serialcirc_call_mail,$msg,$serialcirc_transmission_mail;

		$req="UPDATE serialcirc_expl SET 
		serialcirc_expl_trans_doc_asked=1
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET 
		serialcirc_circ_trans_doc_asked = serialcirc_circ_trans_doc_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);
				
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail=$serialcirc_transmission_mail;
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}
	
	function do_trans($expl_id){
		global $serialcirc_transmission_mail,$serialcirc_call_mail,$msg;
		$req="UPDATE serialcirc_expl SET 
		serialcirc_expl_trans_doc_asked=2
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET 
		serialcirc_circ_trans_doc_asked = serialcirc_circ_trans_doc_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);				
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail=$serialcirc_transmission_mail;
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}

	function build_diff_sel($expl_id){		
		global $charset;
		$tpl="
			<select name='!!zone!!_group_circ_select_$expl_id' id='!!zone!!_group_circ_select_$expl_id' >
				!!diff_select!!
			</select>"
		;
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_empr && $this->info_expl[$expl_id]['serialcirc_diff']->virtual_circ ){
				if( !$this->info_circ[$expl_id][$diffusion["num_empr"]]['subscription'])	continue;			
			}
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group )$name=$diffusion["empr_name"];
			else  $name=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"];
			if($this->info_expl[$expl_id]['num_diff'] == $diffusion['id']) $checked=" selected='selected' ";
			else $checked="";
			$list.="<option value='".$diffusion['id']."' $checked >".htmlentities($name, ENT_QUOTES, $charset)."</option>";
		}
		$tpl=str_replace("!!diff_select!!", $list, $tpl);	
/*		
		// on liste les empr réel et ceux du group
		$name_list="";
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
				$group_name=$diffusion["empr_name"];
				foreach($diffusion['group'] as $empr_group){
					$name.="<a href='".$empr_group['view_link']."'>".htmlentities("[".$group_name."]".$empr_group['empr'],ENT_QUOTES,$charset)."</a><br />";
				}		
			} else{				
				$name="<a href='".$empr_group['view_link']."'>".htmlentities($this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
			}
			if($this->info_expl[$expl_id]['num_diff'] == $diffusion['id'])	 {
				$name="<span class='erreur'>". $name	."</span>";
			}
			$name_list.=$name;	
		}	
		$tpl=str_replace("!!empr_list!!", $name_list, $tpl);	
*/		
		return $tpl;
	}
	
	function build_empr_list($expl_id){		
		global $charset;
		// on liste les empr réel et ceux du group
		$name_list="";
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
				$group_name=$diffusion["empr_name"];
				$name="";
				foreach($diffusion['group'] as $empr_group){
					$name.= "<a href='".$empr_group['empr']['view_link']."'>".htmlentities("[".$group_name."]".$empr_group['empr']["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
				}		
			} else{				
				$name="<a href='".$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]['view_link']."'>". htmlentities($this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
			}				
			if($this->info_expl[$expl_id]['num_diff'] == $diffusion['id'])	 {
				$name="<span class='erreur'>". $name	."</span>";
			}
			$name_list.=$name;			
		}			
		return $name_list;
	}
		
	function build_expl_form($expl_id,$tpl,$zone=''){		
		global $charset;
		$tpl=str_replace("!!expl_id!!", $expl_id, $tpl);
		$tpl=str_replace("!!bull_id!!", $this->info_expl[$expl_id]['bulletin_id'], $tpl);	
		$tpl=str_replace("!!expl_cb!!", "<a href='".$this->info_expl[$expl_id]['expl_link']."'>".htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset)."</a>", $tpl);			
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset)."</a>", $tpl);	
		$tpl=str_replace("!!periodique!!","<a href='".$this->info_expl[$expl_id]['serial_link']."'>". htmlentities( $this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);	
		$tpl=str_replace("!!numero!!","<a href='".$this->info_expl[$expl_id]['bull_link']."'>". htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset)."</a>", $tpl);	
		$tpl=str_replace("!!abonnement!!",  "<a href='".$this->info_expl[$expl_id]['cirdiff_link']."'>".htmlentities( $this->info_expl[$expl_id]['serialcirc_diff']->abt_name,ENT_QUOTES,$charset)."</a>", $tpl);	
		$tpl=str_replace("!!destinataire!!",$this->build_diff_sel($expl_id), $tpl);
		$tpl=str_replace("!!empr_list!!", $this->build_empr_list($expl_id), $tpl);
		$tpl=str_replace("!!zone!!", $zone, $tpl);													
		return $tpl;		
	}
	
	function gen_circ_form($cb="") {
		global $charset, $serialcirc_circ_form,$serialcirc_circ_liste;
		global $serialcirc_circ_liste_alerter,$serialcirc_circ_liste_alerter_tr,$serialcirc_circ_liste_is_alerted_tr;
		global $serialcirc_circ_liste_circuler,$serialcirc_circ_liste_circuler_tr;
		global $serialcirc_circ_liste_circulation,$serialcirc_circ_liste_circulation_rotative_tr,$serialcirc_circ_liste_circulation_star_tr;
		global $serialcirc_circ_liste_retard,$serialcirc_circ_liste_retard_rotative_tr,$serialcirc_circ_liste_retard_star_tr;
		global $serialcirc_copy,$serialcirc_copy_tr,$serialcirc_copy_ok_tr;
		global $serialcirc_circ_liste_reservation,$serialcirc_circ_liste_reservation_tr;
		global $deflt_docs_location, $msg,$pmb_lecteurs_localises;
			
		$circ_form=$serialcirc_circ_form;
		$circ_form = str_replace("!!message!!", "", $circ_form);		
		$circ_form.=$serialcirc_circ_liste;
		
		// select "localisation"
		if($pmb_lecteurs_localises)$circ_form = str_replace("!!localisation!!", gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_id', "document.forms['form_pointage'].submit();", $this->id_location, "", "","","",0),	$circ_form);
		else $circ_form=str_replace("!!localisation!!","",$circ_form);
		
		$liste_alerter=$tr_list="";
		if($nb_liste_alerter=count($this->classement['alert'])){
			$liste_alerter=$serialcirc_circ_liste_alerter;
			foreach($this->classement['alert'] as $expl_id){
				if($this->is_alerted($expl_id))$tr=$serialcirc_circ_liste_is_alerted_tr;
				else $tr=$serialcirc_circ_liste_alerter_tr;
				
				$tr_list.=$this->build_expl_form($expl_id,$tr,"alert");
			}
			$liste_alerter=str_replace("!!liste_alerter!!", $tr_list, $liste_alerter);
		}

		$liste_circuler=$tr_list="";
		if($nb_liste_circuler=count($this->classement['to_be_circ'])){
			$liste_circuler=$serialcirc_circ_liste_circuler;
			foreach($this->classement['to_be_circ'] as $expl_id){
				$tr=$serialcirc_circ_liste_circuler_tr;
				
				$tr_list.=$this->build_expl_form($expl_id,$tr,"to_be_circ");
			}
			$liste_circuler=str_replace("!!liste_circuler!!",$tr_list , $liste_circuler);		
		}	
		$liste_circulation=$tr_list="";
		if($nb_liste_circulation=count($this->classement['in_circ'])){
			$liste_circulation=$serialcirc_circ_liste_circulation;
			foreach($this->classement['in_circ'] as $expl_id){
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_rotative){
					$tr=$serialcirc_circ_liste_circulation_rotative_tr;
				}else{					
					$tr=$serialcirc_circ_liste_circulation_star_tr;
				}	
				if ($this->info_expl[$expl_id]['serialcirc_diff']->checked) {
					$tr=str_replace("!!bt_rappel_perio!!","<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_call_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_call_expl('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;",$tr);
					$tr=str_replace("!!bt_exige_transmission!!","<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_go_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_do_trans('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;",$tr);
				} else {
					$tr=str_replace("!!bt_rappel_perio!!","",$tr);
					$tr=str_replace("!!bt_exige_transmission!!","",$tr);
				}
				$tr_list.=$this->build_expl_form($expl_id,$tr,"in_circ");
			}
			$liste_circulation=str_replace("!!liste_circulation!!", $tr_list, $liste_circulation);		
		}		
		
		$liste_retard=$tr_list="";
		if($nb_liste_retard=count($this->classement['retard'])){
			$liste_retard=$serialcirc_circ_liste_retard;
			foreach($this->classement['retard'] as $expl_id){
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_rotative){
					$tr=$serialcirc_circ_liste_retard_rotative_tr;
				}else{					
					$tr=$serialcirc_circ_liste_retard_star_tr;
				}							
				$tr_list.=$this->build_expl_form($expl_id,$tr,"in_late");
			}
			$liste_retard=str_replace("!!liste_retard!!", $tr_list, $liste_retard);		
		}
		
		$liste_reproduction=$tr_list="";
		if($nb_liste_reproduction=count($this->info_copy)){
			$liste_reproduction=$serialcirc_copy;
			foreach($this->info_copy as $copy){
				if($copy['state'] == 1){
					$tr=$serialcirc_copy_ok_tr;
				}else{					
					$tr=$serialcirc_copy_tr;
				}						
				$tr=str_replace("!!date!!",htmlentities(format_date ($copy['date']),ENT_QUOTES,$charset),$tr);			
				$tr=str_replace("!!periodique!!","<a href='".$copy['serial_link']."'>".htmlentities($copy['perio'],ENT_QUOTES,$charset)."</a>",$tr);
				$tr=str_replace("!!numero!!","<a href='".$copy['bull_link']."'>".htmlentities($copy['numero'],ENT_QUOTES,$charset)." ".$copy['mention_date']."</a>",$tr);
				$tr=str_replace("!!empr_name!!","<a href='".$copy['empr']['view_link']."'>".htmlentities($copy['empr']['empr_libelle'],ENT_QUOTES,$charset)."</a>",$tr);
				$tr=str_replace("!!empr_message!!",htmlentities($copy['comment'],ENT_QUOTES,$charset),$tr);
				$tpl=str_replace("!!zone!!", "copy", $tpl);									
				
				$tr=str_replace("!!id_copy!!",$copy['id'],$tr);
				$tr_list.=$tr;
			}
			$liste_reproduction=str_replace("!!liste_reproduction!!", $tr_list, $liste_reproduction);	
		}		

		$liste_resa=$tr_list="";
		if($nb_liste_resa=count($this->info_resa)){
			$liste_resa=$serialcirc_circ_liste_reservation;
			foreach($this->info_resa as $resa){				
				$tr=$serialcirc_circ_liste_reservation_tr;
				$tr=str_replace("!!periodique!!",htmlentities($resa['perio'],ENT_QUOTES,$charset),$tr);
				$tr=str_replace("!!numero!!",htmlentities($resa['numero'],ENT_QUOTES,$charset)." ".$resa['mention_date'],$tr);
				$tr=str_replace("!!empr_name!!","<a href='".$resa['empr']['view_link']."'>".htmlentities($resa['empr']['empr_libelle'],ENT_QUOTES,$charset)."</a>",$tr);
				
				$tpl=str_replace("!!zone!!", "resa", $tpl);									
				$tr=str_replace("!!empr_id!!",$resa['id_empr'],$tr);
				$tr=str_replace("!!expl_id!!",$resa['id_expl'],$tr);
				$tr=str_replace("!!id_serialcirc_circ!!",$resa['id'],$tr);
				$tr_list.=$tr;
			}
			$liste_resa=str_replace("!!liste_resa!!", $tr_list, $liste_resa);	
		}				
		$liste_resa=str_replace("!!liste_resa!!", $liste_resa, $liste_resa);		
		
		if($nb_liste_alerter) $circ_form = str_replace("!!liste_alerter!!" , gen_plus("liste_alerter",$msg["serialcirc_circ_list_bull_alerter"]." ($nb_liste_alerter)",$liste_alerter),	$circ_form);
		else $circ_form = str_replace("!!liste_alerter!!" ,"" ,	$circ_form);
		if($nb_liste_circuler) $circ_form = str_replace("!!liste_circuler!!" ,  gen_plus("liste_circuler",$msg["serialcirc_circ_list_bull_circuler"]." ($nb_liste_circuler)",$liste_circuler),	$circ_form);
		else $circ_form = str_replace("!!liste_circuler!!" , "", $circ_form);
		if($nb_liste_circulation)$circ_form = str_replace("!!liste_circulation!!" ,  gen_plus("liste_circulation",$msg["serialcirc_circ_list_bull_circulation"]." ($nb_liste_circulation)",$liste_circulation),	$circ_form);		
		else $circ_form = str_replace("!!liste_circulation!!", "", $circ_form);		
		if($nb_liste_retard) $circ_form = str_replace("!!liste_retard!!" ,  gen_plus("liste_retard",$msg["serialcirc_circ_list_bull_retards"]." ($nb_liste_retard)",$liste_retard),	$circ_form);
		else $circ_form = str_replace("!!liste_retard!!",  "", $circ_form);
		if($nb_liste_reproduction)$circ_form = str_replace("!!liste_reproduction!!" ,  gen_plus("liste_reproduction",$msg["serialcirc_circ_list_bull_reproduction"]." ($nb_liste_reproduction)",$liste_reproduction),	$circ_form);
		else $circ_form = str_replace("!!liste_reproduction!!", "", $circ_form);
		if($nb_liste_resa)$circ_form = str_replace("!!liste_resa!!" ,  gen_plus("liste_resa",$msg["serialcirc_circ_list_bull_reservation"]." ($nb_liste_resa)",$liste_resa),	$circ_form);
		else $circ_form = str_replace("!!liste_resa!!", "",	$circ_form);
		
		return $circ_form;
	}

		
	
	function gen_pointage_form($point_expl_id) {
		global $charset, $serialcirc_pointage_form,$serialcirc_circ_liste;
		global $serialcirc_circ_liste_alerter,$serialcirc_circ_liste_alerter_tr,$serialcirc_circ_liste_is_alerted_tr;
		global $serialcirc_circ_liste_circuler,$serialcirc_circ_liste_circuler_tr;
		global $serialcirc_circ_liste_circulation,$serialcirc_circ_liste_circulation_rotative_tr,$serialcirc_circ_liste_circulation_star_tr;
		global $serialcirc_circ_liste_retard,$serialcirc_circ_liste_retard_rotative_tr,$serialcirc_circ_liste_retard_star_tr;
		global $serialcirc_copy,$serialcirc_copy_tr,$serialcirc_copy_ok_tr;
		global $serialcirc_circ_liste_reservation,$serialcirc_circ_liste_reservation_tr;
		global $deflt_docs_location, $msg,$pmb_lecteurs_localises;
			
		$circ_form=$serialcirc_pointage_form;
	
		$liste_alerter=$tr_list="";
		foreach($this->classement['alert'] as $expl_id){
			if($expl_id==$point_expl_id){
				$nb_liste_alerter=1;
				$liste_alerter=$serialcirc_circ_liste_alerter;
				if($this->is_alerted($expl_id))$tr=$serialcirc_circ_liste_is_alerted_tr;
				else $tr=$serialcirc_circ_liste_alerter_tr;
					
				$tr_list.=$this->build_expl_form($expl_id,$tr,"alert");				
				$liste_alerter=str_replace("!!liste_alerter!!", $tr_list, $liste_alerter);
				break;
			}
		}
		$liste_circuler=$tr_list="";
		foreach($this->classement['to_be_circ'] as $expl_id){			
			if($expl_id==$point_expl_id){				
				$nb_liste_circuler=1;
				$liste_circuler=$serialcirc_circ_liste_circuler;
				foreach($this->classement['to_be_circ'] as $expl_id){
					$tr=$serialcirc_circ_liste_circuler_tr;
					
					$tr_list.=$this->build_expl_form($expl_id,$tr,"to_be_circ");
				}
				$liste_circuler=str_replace("!!liste_circuler!!",$tr_list , $liste_circuler);
				break;	
			}	
		}	
		
		$liste_circulation=$tr_list="";
		foreach($this->classement['in_circ'] as $expl_id){			
			if($expl_id==$point_expl_id){				
				$nb_liste_circulation=1;
				$liste_circulation=$serialcirc_circ_liste_circulation;
			
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_rotative){
					$tr=$serialcirc_circ_liste_circulation_rotative_tr;
				}else{					
					$tr=$serialcirc_circ_liste_circulation_star_tr;
				}
				if ($this->info_expl[$expl_id]['serialcirc_diff']->checked) {
					$tr=str_replace("!!bt_rappel_perio!!","<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_call_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_call_expl('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;",$tr);
					$tr=str_replace("!!bt_exige_transmission!!","<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_go_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_do_trans('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;",$tr);
				} else {
					$tr=str_replace("!!bt_rappel_perio!!","",$tr);
					$tr=str_replace("!!bt_exige_transmission!!","",$tr);
				}						
				$tr_list.=$this->build_expl_form($expl_id,$tr,"in_circ");			
				$liste_circulation=str_replace("!!liste_circulation!!", $tr_list, $liste_circulation);		
				
				$liste_alerter="";
				$nb_liste_alerter=0;
				break;
			}
		}		
		
		$liste_retard=$tr_list="";
		foreach($this->classement['retard'] as $expl_id){		
			if($expl_id==$point_expl_id){	
				$nb_liste_retard=1;
				$liste_retard=$serialcirc_circ_liste_retard;			
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_rotative){
					$tr=$serialcirc_circ_liste_retard_rotative_tr;
				}else{					
					$tr=$serialcirc_circ_liste_retard_star_tr;
				}							
				$tr_list.=$this->build_expl_form($expl_id,$tr,"in_late");				
				$liste_retard=str_replace("!!liste_retard!!", $tr_list, $liste_retard);		
				break;
			}					
		}
				
		
		if($nb_liste_alerter) $circ_form = str_replace("!!liste_alerter!!" ,"<span class='notice-heada'>".$msg["serialcirc_circ_list_bull_alerter"]."</span><br>".$liste_alerter,	$circ_form);
		else $circ_form = str_replace("!!liste_alerter!!" ,"" ,	$circ_form);
		if($nb_liste_circuler) $circ_form = str_replace("!!liste_circuler!!" ,"<span class='notice-heada'>".$msg["serialcirc_circ_list_bull_circuler"]."</span><br>".$liste_circuler,	$circ_form);
		else $circ_form = str_replace("!!liste_circuler!!" , "", $circ_form);
		if($nb_liste_circulation)$circ_form = str_replace("!!liste_circulation!!","<span class='notice-heada'>".$msg["serialcirc_circ_list_bull_circulation"]."</span><br>".$liste_circulation,	$circ_form);		
		else $circ_form = str_replace("!!liste_circulation!!", "", $circ_form);		
		if($nb_liste_retard) $circ_form = str_replace("!!liste_retard!!" , "<span class='notice-heada'>".$msg["serialcirc_circ_list_bull_retards"]."</span><br>".$liste_retard,	$circ_form);
		else $circ_form = str_replace("!!liste_retard!!",  "", $circ_form);
		
		return $circ_form;
	}
	
			
	function gen_circ_cb($cb) {
		global $serialcirc_circ_cb_notfound, $serialcirc_circ_cb_info;
		$info="";
		$req="select * from serialcirc_expl,exemplaires where expl_cb='$cb' and expl_id=num_serialcirc_expl_id ";
		$resultat=pmb_mysql_query($req);
		if ($resultat) {
			if (!pmb_mysql_num_rows($resultat)) {
				$this->info_cb['cb']='';
				return str_replace("!!cb!!", $cb, $serialcirc_circ_cb_notfound);
			}
			$r=pmb_mysql_fetch_object($resultat);
			$info.=$this->gen_pointage_form($r->expl_id);
		}
		return $info;
	}		

	
} //serialcirc class end

/*
 * 
 * 
 * 
 * 
 * script perso d'impression de cote de périodique bulletiné dans la jounée et de liste de diffusion de circulation de périodique
 *  sur feuille d'étiquette autocollante
 * $pmb_serialcirc_subst indique le fichier perso ou tout se qui suit doit être copier
 * 
 * 
 * 
 * 
 * 
 * 
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once("$class_path/fpdf.class.php");

class serialcirc_subst extends serialcirc {
	
	function gen_print_option($list){
		global $charset;
		global $std_header, $footer;
		
		
		$list_serialized=serialize($list);
		$sub="print_sel_diffusion";
		if($list[0]['type']=="cote")	$sub="print_sel_cote";
		
		$tpl="
		$std_header
		<h3>".htmlentities("Sélection de l'étiquette de départ", ENT_QUOTES, $charset)."</h3>	
		<form id='form_print_circ' name='form_print_circ' method='post' action='./ajax.php?module=circ&categ=periocirc&sub=$sub&print_action=1' >
			<input type='hidden' id='list' name='list' value='$list_serialized' />
			<table>
				<tr>
					<td></td>
					<td valign='top'>Colonne 1</td> 	
					<td valign='top'>Colonne 2</td> 				
				</tr>
				<tr>	
					<td>1</td>
					<td valign='top'><input type='radio' name='index_start'   value='1' checked='checked' /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='2' /></td> 
				</tr>
				<tr>	
					<td>2</td>
					<td valign='top'><input type='radio' name='index_start'   value='3'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='4' /></td> 
				</tr>
				<tr>	
					<td>3</td>
					<td valign='top'><input type='radio' name='index_start'   value='5'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='6' /></td> 
				</tr>
				<tr>	
					<td>4</td>
					<td valign='top'><input type='radio' name='index_start'   value='7'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='8' /></td> 
				</tr>
				<tr>	
					<td>5</td>
					<td valign='top'><input type='radio' name='index_start'   value='9'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='10' /></td> 
				</tr>
				<tr>	
					<td>6</td>
					<td valign='top'><input type='radio' name='index_start'   value='11'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='12' /></td> 
				</tr>
				<tr>	
					<td>7</td>
					<td valign='top'><input type='radio' name='index_start'   value='13'  /></td> 
					<td valign='top'><input type='radio' name='index_start'   value='14' /></td> 
				</tr>
			</table>
			<input type='submit' class='bouton' value='Imprimer'  />
		
		</form>	
		$footer
		";
		return $tpl;
	}
	
	function print_diffusion($expl_id,$start_diff_id){
		global $fpdf;
		global $print_action;
		
		$list=array();
		$list[0]['expl_id']=$expl_id;
		$list[0]['start_diff_id']=$start_diff_id;
		if(!$print_action){
			print $this->gen_print_option($list);
			return;
		}
		
	}
		
	function print_cote($expl_id=""){
		global $fpdf;
		global $print_action;
		
		$list=array();
		if(!$expl_id){			
			$req="select expl_id from  exemplaires  where expl_abt_num > 0 and create_date > concat(CURDATE(),' 00:00:00') and expl_id not in (
			select num_serialcirc_expl_id from serialcirc_expl) order by create_date";
			$i=0;
			$resultat=pmb_mysql_query($req);
			if (!pmb_mysql_num_rows($resultat)) return;
			while($r=pmb_mysql_fetch_object($resultat)){
				$list[$i]['expl_id']=$r->expl_id;
				$list[$i]['type']="cote";
				$i++;
			}
		}else{			
			$list[0]['expl_id']=$expl_id;
			$list[0]['type']="cote";
		}
		if(!$print_action){
			print $this->gen_print_option($list);
			return;
		}
	
	}
		
	function print_sel_diffusion($list){
		global $fpdf;
		global $print_action,$index_start;
		if(!$print_action){
			print $this->gen_print_option($list);
			return;
		}
		//printr($list);return ;
		if(!$index_start)$index_start=1;
		
		$this->count=0;
		$ourPDF = new $fpdf('P', 'mm', 'A4');
		$ourPDF->Open();
		$ourPDF->SetAutoPageBreak(0,0);


		
		for($i=0;$i<$index_start-1;$i++){
			
			$this->build($this->count++,$ourPDF,"");
		}
		foreach($list as $circ){
			$expl_id=$circ['expl_id'];
			$start_diff_id=$circ['start_diff_id'];
	
			$tpl.=$this->build_print_diffusion($expl_id,$start_diff_id,$ourPDF);
		}
		//print 		$tpl;exit;
		header("Content-Type: application/pdf");
		$ourPDF->OutPut();		
	}

	function build($num,$ourPDF,$data){
		global $pmb_pdf_font;
		
		if(!(($num)%14)) {
			$ourPDF->addPage();
			$ourPDF->SetLeftMargin(0);
			$ourPDF->SetTopMargin(0);
	
		}
		if(!$data) return;
		
		$num=$num-((int)($num/14)*14);
	
		$hauteur=42;
		if($num%2){	
			$x=110;	
		}else{
			$x=6;
		}
		$y=($hauteur * (int)(($num)/2))+10;
		
		//$info.=" num=$num; x=$x, y=$y " ;
		// titre perio
		$ourPDF->SetXY ($x,$y);
		$ourPDF->setFont($pmb_pdf_font, 'BI', 10);
		$ourPDF->multiCell(110, 3, $info.substr($data['titre'],0,52) , 0, 'L', 0);
		
		if($data['cb']){
			// Code barre
			$ourPDF->SetXY ($x,$y+4);
			$ourPDF->setFont($pmb_pdf_font, '', 10);
			$ourPDF->multiCell(110, 3, "Ex ".substr($data['cb'],0,20)."  Cote: ".substr($data['cote'],0,20) , 0, 'L', 0);		
		}
		// Numéro & Date
		$ourPDF->SetXY ($x,$y+7);
		$ourPDF->setFont($pmb_pdf_font, '', 8);
		$ourPDF->multiCell(110, 3, "".substr($data['numero_libelle'],0,20)."  Date n° : ".substr($data['date_libelle'],0,30) , 0, 'L', 0);
		
		// Date reception
		$ourPDF->SetXY ($x,$y+10);
		$ourPDF->setFont($pmb_pdf_font, '', 8);
		$ourPDF->multiCell(110, 3, "Reçu le : ".substr($data['date_reception'],0,20)."  ".substr($data['abt_name'],0,30) , 0, 'L', 0);
		$i=0;	
		// empr list
		if(is_array($data['empr'])){
			foreach($data['empr']as $empr){	
				if($i==4){
					$x=$x+30;
					$i=0;
				}				
				$ourPDF->SetXY ($x,$y+15+($i*3));
				$ourPDF->setFont($pmb_pdf_font, '', 8);
				$field=substr($empr['name'],0,5);
				if($empr['pperso'])$field.="-".substr($empr['pperso'],0,5);
				$ourPDF->multiCell(110, 3, $field , 0, 'L', 0);
				$i++;		
			}
		}
		if((!is_array($data['empr']) || !count($data['empr'])) && $data['abt_name1']){
			
			// aff abt
			$ourPDF->SetXY ($x,$y+15);
			$ourPDF->setFont($pmb_pdf_font, '', 10);
			$ourPDF->multiCell(110, 3, substr($data['abt_name1'],0,30) , 0, 'L', 0);
			$i=0;
		}
	}

	function print_sel_cote($list){
		global $fpdf,$msg;
		global $print_action,$index_start;
	
		if(!$print_action){
			print $this->gen_print_option($list);
			return;
		}
		//printr($list);return ;
		if(!$index_start)$index_start=1;
		
		for($i=0;$i<$index_start-1;$i++){
			$this->build($this->count++,$ourPDF,"");
		}
	
		$this->count=0;
		$ourPDF = new $fpdf('P', 'mm', 'A4');
		$ourPDF->Open();
		$ourPDF->SetAutoPageBreak(0,0);
	
		foreach($list as $expl){
			$expl_id=$expl['expl_id'];
		
			$req="select *, date_format(create_date, '".$msg["format_date"]."') as aff_create_date from exemplaires,bulletins where expl_bulletin=bulletin_id and expl_id='$expl_id' ";
			$resultat=pmb_mysql_query($req);
			if (!pmb_mysql_num_rows($resultat)) return;
			$r=pmb_mysql_fetch_object($resultat);
			$this->info_expl[$r->expl_id]['expl_cb']= $r->expl_cb;
			$this->info_expl[$r->expl_id]['expl_id']= $r->expl_id;
			$this->info_expl[$r->expl_id]['expl_statut']= $r->expl_statut;
			$this->info_expl[$r->expl_id]['expl_location']= $r->expl_location;
			$this->info_expl[$r->expl_id]['bulletine_date']= $r->serialcirc_expl_bulletine_date;
			$this->info_expl[$r->expl_id]['numero']= $r->bulletin_numero;
			$this->info_expl[$r->expl_id]['mention_date']= $r->mention_date;
			$this->info_expl[$r->expl_id]['bulletin_notice']= $r->bulletin_notice;
			$this->info_expl[$r->expl_id]['bulletin_id']= $r->bulletin_id;
			$this->info_expl[$r->expl_id]['num_notice']= $r->num_notice;
			$print_date=$r->aff_create_date;
		
			$req_serial="select * from notices  where notice_id=".$r->bulletin_notice."";
			$res_serial=pmb_mysql_query($req_serial);
			if ($r_serial=pmb_mysql_fetch_object($res_serial)){
				$this->info_expl[$r->expl_id]['serial_title']=$r_serial->tit1;
			}
			$data=array();
			$data['titre']=$this->info_expl[$expl_id]['serial_title'];
			$data['cb']=$this->info_expl[$expl_id]['expl_cb'];
			$data['cote']=$this->info_expl[$expl_id]['expl_cote'];
			$data['numero_libelle']=$this->info_expl[$expl_id]['numero'];
			$data['date_libelle']=$this->info_expl[$expl_id]['mention_date'];
			$data['date_reception']=$print_date;
		
		
			$req="select  abt_name from abts_abts,exemplaires where expl_abt_num=abt_id and expl_id=". $r->expl_id;
			$res_abt=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($res_abt)) {
				$r_abt=pmb_mysql_fetch_object($res_abt);
				$data['abt_name1']=$r_abt->abt_name;
			}else $data['abt_name1']="";
		
			$this->build($this->count++,$ourPDF,$data);
		}
		//print 		$tpl;exit;
		header("Content-Type: application/pdf");
		$ourPDF->OutPut();	
	
	}
	
	function build_print_diffusion($expl_id,$start_diff_id,$ourPDF){
		global $serialcirc_circ_pdf_diffusion,$charset,$serialcirc_circ_pdf_diffusion_destinataire;
		global $msg,$dbh;
		
		if(!$start_diff_id){
			foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
				$start_diff_id=$diff_id;
				break;
			}
		}
		if (!$this->info_expl[$expl_id]) return false;
		$req="UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc_diff=".$start_diff_id.",
		serialcirc_expl_state_circ=1,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
	
		$req="select date_format(CURDATE(), '".$msg["format_date"]."') as print_date";
		$result = pmb_mysql_query($req);
		$obj = pmb_mysql_fetch_object($result);
		$print_date=$obj->print_date;
	
		$tpl = $serialcirc_circ_pdf_diffusion;
		$tpl=str_replace("!!expl_cb!!", htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!periodique!!", htmlentities($this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!numero!!", htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!print_date!!", htmlentities($print_date,ENT_QUOTES,$charset), $tpl);
		//	$tpl=str_replace("!!abonnement!!", htmlentities($this->info_expl[$expl_id]['serialcirc_diff']->abt_name,ENT_QUOTES,$charset), $tpl);
	
		if($start_diff_id) $found=0;else $found=1;
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
				
			if($start_diff_id && !$found){
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				$diff_list[]=$diff_id;
					
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
					$name=$diffusion["empr_name"];
						
					foreach($diffusion['group'] as $empr_group){
						$empr_list[$empr_group["num_empr"]]=$diff_id;
						if($empr_group["duration"])
							$empr_days[$empr_group["num_empr"]]=$empr_group["duration"];
						else
							$empr_days[$empr_group["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
							
						if($diffusion['type_diff']==1 && !$empr_group["responsable"]){
							// groupe marguerite: on n'imprimera pas ce lecteur sauf le responsable
							//$empr_no_display[$empr_group["num_empr"]]=1;
						}
					}
				}else  {
					$name=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"];
					$empr_list[$diffusion["num_empr"]]=$diff_id;
					if($diffusion["duration"])	$empr_days[$diffusion["num_empr"]]=$diffusion["duration"]; // durée de consultation particulière
					else $empr_days[$diffusion["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
				}
				if($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_star){
					// on n'imprime que le suivant dans la liste
					break;
				}
			}
		}
		$this->gen_circ($empr_list,$empr_days, $expl_id);
	
		
		$data=array();
		$data['titre']=$this->info_expl[$expl_id]['serial_title'];
		if(!$this->info_expl[$expl_id]['serialcirc_diff']->no_ret_circ){//pas de retour sur site: on n'affiche pas cb et cote
			$data['cb']=$this->info_expl[$expl_id]['expl_cb'];
			$data['cote']=$this->info_expl[$expl_id]['expl_cote'];
		}	
		$data['numero_libelle']=$this->info_expl[$expl_id]['numero'];
		$data['date_libelle']=$this->info_expl[$expl_id]['mention_date'];
		$data['date_reception']=$print_date;
		$data['abt_name']=$this->info_expl[$expl_id]['serialcirc_diff']->abt_name;
		
		
		$i=0;			
		$p_perso=new parametres_perso("empr");
		foreach($empr_list as $empr_id=>$diff_id){
			$req="select * from empr where 	id_empr=$empr_id";
			$res = pmb_mysql_query($req, $dbh);
			if (pmb_mysql_num_rows($res)) {
				$row = pmb_mysql_fetch_object($res);		
				$pp_values=$p_perso->read_base_fields_perso_values("pp_??????",$empr_id);	
				$data['empr'][$i]['name']=$row->empr_nom;
				$data['empr'][$i]['pperso']=$pp_values[0];
				$i++;
			}
		}
		
		$this->build($this->count++,$ourPDF,$data);
		
		if($this->info_expl[$expl_id]['serialcirc_diff']->no_ret_circ){
			//pas de retour sur site, suppression de la circulation.
			$this->delete_diffusion($expl_id);
			if($empr_id){
				$req="update exemplaires set expl_lastempr=$empr_id where expl_id=$expl_id";	
				pmb_mysql_query($req, $dbh);
			}
		}
	}
		
} //class end
*/
 