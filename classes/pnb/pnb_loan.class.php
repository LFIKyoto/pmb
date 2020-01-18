<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_loan.class.php,v 1.6.6.7 2019-12-03 10:47:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/emprunteur.class.php");
require_once("$include_path/ajax.inc.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/quotas.class.php");
require_once("$class_path/comptes.class.php");
require_once("$class_path/mono_display.class.php");
require_once($include_path."/parser.inc.php");
require_once("$base_path/circ/pret_func.inc.php");
require_once($include_path."/expl_info.inc.php");
require_once($class_path."/pret_parametres_perso.class.php");
require_once($class_path.'/event/events/event_loan.class.php');
require_once($class_path."/ajax_retour_class.php");
require_once($class_path."/ajax_pret.class.php");
require_once($class_path."/expl.class.php");

/*
 Pour effectuer un pret:
 // Appel de la class pret:
 $pret = new do_pret();
 // Fonction qui effectue le pret temporaire si pas d'erreur 
$status_xml = $pret->check_pieges($cb_empr, $id_empr,$cb_doc, $id_expl,0);
// Fonction qu effectue le pret d�finitif
confirm_pret($id_empr, $id_expl); 
 
 
 Fonction check_pieges
 		Effectue le pret temporaire d'un document � un emprunteur
 input:	
 		$cb_empr Cb de l'emprunteur ou ''
 		$id_empr id de l'emprunteur ou 0
 		$cb_doc	Cb du document ou ''
 		$id_expl Id du document ou 0
 		$forcage: En cas de piege forcable, ce parametre permet de forcer le numero du pi�ge
 				retourn� dans le param�res forcage.
 				Mettre 0 par d�faut
 output:
 		dans un format xml:
 		status 
 				0 : pas d'erreur, le pret temporaire est effectu�
 				-1 Erreur non forcable. Voir message d'erreur (error_message)
 				1 Erreur forcable. voir le num�ro du pi�ge  (forcage) et message d'erreur (error_message)
 		forcage
 				Si status � 1, c'est le num�ro du pi�ge qui ne passe pas. Voir message d'erreur (error_message)
 				Pour effectuer le forcage de ce pi�ge, il faut rapeller la fonction check_pieges avec $forcage � cette valeur
 		error_message
 				Message de l'erreur 
 		id_empr
 		empr_cb
 		id_expl
 		cb_expl
 		expl_notice
 		libelle:
 				Titre du document
 		tdoc_libelle:
 				Support
 */


class pnb_loan extends do_pret {

	public function check_pieges($empr_cb, $id_empr,$cb_expl, $id_expl,$forcage,$short_loan=0) {
		$this->id_empr = $id_empr;
		$this->empr_cb = $empr_cb;
		$this->id_expl = $id_expl;
		$this->cb_expl = $cb_expl;
		$this->forcage = 0;
		$this->short_loan=$short_loan;
			
		//Ordre d'execution des fonctions
		for($i=0; $i<count($this->trap_order); $i++) {
			$id=$this->trap_order[$i];
			// S'il n'y a pas de forcage, on check tous les pi�ges
			if(($forcage < $i) || ($id==1) || ($id==2)  )	{
				// Le test est � faire
					
				$p=$this->trap_func[$id]["ARG"];
				// Construction du code de l'appel � la fonction
				$cmd = "\$this->status = \$this->" . $this->trap_func[$id]["NAME"] . "(";
				// ajout des param�tres � l'appel de la fonction
				for($j=0; $j<count($p); $j++) {
					$cmd.= "\$this->"."$p[$j] ";
					if (($j+1) < count($p) ) {
						$cmd.= ", ";
					}
				}
				// Fin du code de l'appel de la fonction
				$cmd.= ");";
				// Execution de la fonction de pi�ge
				$status=0;
				$exec_stat = eval ($cmd);
				if($this->status!=0) {
					$this->forcage =$i;
					break;
				}
			}
		}
		if($this->status==0) {
			//Effectuer le pret (temporaire si issu de RFID)
			$this->add_pret($this->id_empr, $this->id_expl, $this->cb_expl);
		}
		$array[0]=$this;
		$buf_xml = array2xml($array);
		return $buf_xml;
	}
	
	
	public function check_quotas($id_empr, $id_expl) {
		global $lang, $include_path;
		global $pmb_quotas_avances;	
		if ($pmb_quotas_avances) {
			//Initialisation des quotas pour nombre de documents pr�tables
			$qt = new quota("PNB_LOANS", $include_path.'/quotas/own/'.$lang.'/pnb.xml');
			//Tableau de passage des param�tres
			$struct = array();
			$struct["READER"] = $id_empr;
			$struct["EXPL"] = $id_expl;
			$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
			$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
			//Test du quota pour l'exemplaire et l'emprunteur
			if ($qt->check_quota($struct)) {
				//Si erreur, r�cup�ration du message et peut-on forcer ou non ?
				$this->error_message= $qt->error_message;
				if( $qt->force) {
					return 1;
				} 
				return -1;	
			}
		}
		$this->error_message="";
		return 0;
	}

	public function del_pret($id_expl) {
		// le lien MySQL
		global $dbh;
		global $msg;
		// r�cup�rer la stat ins�r�e pour la supprimer !
		$query = "select pret_arc_id ,pret_temp from pret where pret_idexpl = '" . $id_expl . "' ";
		$result = pmb_mysql_query($query, $dbh);
		$stat_id = pmb_mysql_fetch_object($result);
		if($stat_id->pret_temp ) {
			/**
			 * Publication d'un �venement � l'annulation du pr�t (avant suppression dans pret_archive)
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_loan("loan", "cancel_loan");
			$event->set_id_loan($stat_id->pret_arc_id);
			$evt_handler->send($event);
			
			$result = pmb_mysql_query("delete from pret_archive where arc_id='" . $stat_id->pret_arc_id . "' ", $dbh);
			audit::delete_audit (AUDIT_PRET, $stat_id->pret_arc_id) ;
		
			// supprimer les valeurs de champs personnalis�s
			$p_perso=new pret_parametres_perso("pret");
			$p_perso->delete_values($stat_id->pret_arc_id);
			
			// supprimer le pr�t annul�
			$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
			$result = pmb_mysql_query($query, $dbh);
			
		}	
		$array[0]=$this;
		$buf_xml = array2xml($array);				
		return $buf_xml;
	}
	
	public function add_pret($id_empr, $id_expl, $cb_expl) {
		// le lien MySQL
		global $dbh;
		global $msg;
		// ins�rer le pr�t sans stat et gestion financi�re
		$query = "INSERT INTO pret SET ";
		$query .= "pret_idempr = '" . $id_empr . "', ";
		$query .= "pret_idexpl = '" . $id_expl . "', ";
		$query .= "pret_date   = sysdate(), ";
		$query .= "pret_retour = 'today()', ";
		$query .= "retour_initial = 'today()', ";
		$query .= "pret_temp = '".$_SERVER['REMOTE_ADDR']."'";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't INSERT into pret" . $query);
		
		$query = "delete from resa_ranger ";
		$query .= "where resa_cb='".$cb_expl."'";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't delete cb_doc in resa_ranger : ".$query);
	}
	
	public function confirm_pret($id_empr, $id_expl, $date_retour = '', $source_device = '') {
	
		$ret = array();
		//supprimer le pret temporaire
		$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
		pmb_mysql_query($query);
						
		// ins�rer le pr�t 
		$query = "INSERT INTO pret SET ";
		$query .= "pret_idempr = '" . $id_empr . "', ";
		$query .= "pret_idexpl = '" . $id_expl . "', ";
		$query .= "pret_date   = sysdate(), ";
		$query .= "pret_pnb_flag = '1', ";
		$query .= "pret_retour = '$date_retour', ";
		$query .= "retour_initial = '$date_retour', ";
		$query .= "short_loan_flag = '0'";
		$result = pmb_mysql_query($query);	    
		
		// ins�rer la trace en stat, r�cup�rer l'id et le mettre dans la table des pr�ts pour la maj ult�rieure
		$stat_avant_pret = pret_construit_infos_stat($id_expl);
		$stat_avant_pret->pnb_flag = true;		
		$stat_avant_pret->source_device = $source_device;
		$stat_id = stat_stuff($stat_avant_pret);
		$query = "update pret SET pret_arc_id='$stat_id' where ";
		$query .= "pret_idempr = '" . $id_empr . "' and ";
		$query .= "pret_idexpl = '" . $id_expl . "' ";
		pmb_mysql_query($query);
	
		//enregistrer les champs perso pret
		$p_perso=new pret_parametres_perso("pret");
		$p_perso->rec_fields_perso($stat_id);
		
		$query = "update exemplaires SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where expl_id= '" . $id_expl . "' ";
		pmb_mysql_query($query);

		$query = "update empr SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where id_empr= '" . $id_empr . "' ";
        pmb_mysql_query($query);
	
		/**
		 * Publication d'un �venement � l'enregistrement du pr�t en base (pi�ges pass�s et pr�t valid� (quotas etc..) )
		 */
		$evt_handler = events_handler::get_instance();
		$event = new event_loan("loan", "add_numeric_loan");
		$event->set_id_loan($stat_id);
		$event->set_id_empr($id_empr);
		$evt_handler->send($event);
		
		$ret[0]['id_empr'] = $id_empr;
		$ret[0]['id_expl'] = $id_expl;
		$ret[0]['date_retour'] = $date_retour;
		$ret[0]['retour_initial'] = $date_retour;		
		$ret[0]['pret_arc_id'] = $stat_id;
		$ret[0]['statut']=1;
		return $ret;
	}
// Fin class		
}
