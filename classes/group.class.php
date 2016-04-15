<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: group.class.php,v 1.14 2015-04-03 11:16:20 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des groupes emprunteurs

class group {
	var $id=0;
	var $libelle = '';
	var $id_resp = 0;
	var $libelle_resp = '';
	var $cb_resp = '';
	var $mail_resp = '';
	var $members;
	var $nb_members = 0;
	var $lettre_rappel = 0 ;
	var $mail_rappel = 0 ;
	var $lettre_rappel_show_nomgroup = 0 ;

	// constructeur
	function group($id=0) {
		// si id; récupération des données du groupe
		if($id) {
			$this->id = $id;
			$this->members = array();
			$this->get_data();
			}
		// si id = 0; création d'une instance vide
		return;
	}

	// récupération des données du groupe
	function get_data() {
		global $dbh;
		$requete = "SELECT * FROM groupe";
		$requete .= " WHERE id_groupe='".$this->id."' ";
		$res = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_object($res);
			$this->libelle = $row->libelle_groupe;
			$this->lettre_rappel=$row->lettre_rappel;
			$this->mail_rappel=$row->mail_rappel;
			$this->lettre_rappel_show_nomgroup=$row->lettre_rappel_show_nomgroup;
			// récupération id et libelle du responsable
			if($row->resp_groupe) {
			  	$this->id_resp = $row->resp_groupe;
			  	$requete = "SELECT empr_nom, empr_prenom, empr_cb, empr_mail FROM empr";
			  	$requete .= " WHERE id_empr=".$this->id_resp." LIMIT 1";
			  	$res = pmb_mysql_query($requete, $dbh);
			  	if(pmb_mysql_num_rows($res)) {
			  		$row = pmb_mysql_fetch_object($res);
			  		$this->libelle_resp = $row->empr_nom;
			  		if($row->empr_prenom) $this->libelle_resp .= ', '.$row->empr_prenom;
			  		$this->libelle_resp .= ' ('.$row->empr_cb.')';
			  		$this->cb_resp = $row->empr_cb;
			  		$this->mail_resp = $row->empr_mail;
		  		}
		  	}
			$this->get_members();
		}
		return;
	}

	// génération du form de group
	function form() {
		global $group_form;
		global $msg;
	 	global $charset;
		if($this->id) $titre = $msg[912]; // modification
			else $titre = $msg[910]; // création
		$group_form = str_replace('!!titre!!', $titre, $group_form);
		if ($this->lettre_rappel) $group_form = str_replace('!!lettre_rappel!!', "checked", $group_form);
		else $group_form = str_replace('!!lettre_rappel!!', "", $group_form);
		if ($this->mail_rappel) $group_form = str_replace('!!mail_rappel!!', "checked", $group_form);
		else $group_form = str_replace('!!mail_rappel!!', "", $group_form);
		if ($this->lettre_rappel_show_nomgroup) $group_form = str_replace('!!lettre_rappel_show_nomgroup!!', "checked", $group_form);
		else $group_form = str_replace('!!lettre_rappel_show_nomgroup!!', "", $group_form);
	 	$group_form = str_replace('!!group_name!!', htmlentities($this->libelle,ENT_QUOTES, $charset), $group_form);
		$group_form = str_replace('!!nom_resp!!', $this->libelle_resp, $group_form);
		$group_form = str_replace('!!groupID!!', $this->id, $group_form);
		$group_form = str_replace('!!respID!!', $this->id_resp, $group_form);
		if($this->id) {
		 	$link_annul = './circ.php?categ=groups&action=showgroup&groupID='.$this->id;
		 	$link_suppr = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\" />";
		} else {
	 		$link_annul = './circ.php?categ=groups';
	 		$link_suppr = "";
	 	}
		$group_form = str_replace('!!link_annul!!', $link_annul, $group_form);
		$group_form = str_replace('<!-- bouton_suppression -->', $link_suppr, $group_form);
		return $group_form;
	}
      
	// affectation de nouvelles valeurs
	function set($group_name, $respID=0, $lettre_rappel=0, $mail_rappel=0, $lettre_rappel_show_nomgroup=0) {
		if ($group_name) $this->libelle = $group_name;
		$this->id_resp = $respID;
		$this->lettre_rappel=$lettre_rappel;
		$this->mail_rappel=$mail_rappel;
		$this->lettre_rappel_show_nomgroup=$lettre_rappel_show_nomgroup;
		return;
	}

	// récupération des membres du groupe (feed : array members)
	function get_members() {
		if(!$this->id) return;
		global $dbh;
	
		$requete = "select EMPR.id_empr AS id, EMPR.empr_nom AS nom , EMPR.empr_prenom AS prenom, EMPR.empr_cb AS cb, EMPR.empr_categ AS id_categ, EMPR.type_abt AS id_abt";
		$requete .= " FROM empr EMPR, empr_groupe MEMBERS";
		$requete .= " WHERE MEMBERS.empr_id=EMPR.id_empr";
		$requete .= " AND MEMBERS.groupe_id=".$this->id;
		$requete .= " ORDER BY EMPR.empr_nom, EMPR.empr_prenom";
		$result = pmb_mysql_query($requete, $dbh);
		$this->nb_members = pmb_mysql_num_rows($result);
		if($this->nb_members) {
		 	while($mb = pmb_mysql_fetch_object($result)) {
		 		$this->members[] = array( 'nom' => $mb->nom,
							'prenom' => $mb->prenom,
							'cb' => $mb->cb,
							'id' => $mb->id,
		 					'id_categ' => $mb->id_categ,
		 					'id_abt' => $mb->id_abt);
			}
		}
		$this->nb_members = sizeof($this->members);
		return;
	}

	// ajout d'un membre
	function add_member($member) {
		global $dbh;
		if(!$member) return 0;
		
		// checke si ce membre n'est pas déjà dans le groupe
		$requete = "SELECT count(1) FROM empr_groupe";
		$requete .= " WHERE empr_id=$member AND groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_result($res, 0, 0)) return $member;
		
		// OK. insertion 'pour de vrai'
		$requete = "INSERT INTO empr_groupe";
		$requete .= " SET empr_id='$member', groupe_id='".$this->id."'";
		$res = pmb_mysql_query($requete, $dbh);
		if($res) return $member;
			else return 0;
	}
      
	// suppression du groupe
	function delete() {
		global $dbh;
		$requete = "DELETE FROM groupe WHERE id_groupe=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		$nb = pmb_mysql_affected_rows($dbh);
		$requete = "DELETE FROM empr_groupe WHERE groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		return $nb;
	}

	// suppression d'un membre
	function del_member($member) {
		global $dbh;
		if(!$member) return 0;
		$requete = "DELETE FROM empr_groupe";
		$requete .= " WHERE empr_id=$member AND groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		return $res;
	}

	// mise à jour dans la table
	function update() {
		global $dbh;
		global $msg;
		
		if($this->id) {
			// mise à jour
			$requete = "UPDATE groupe";
			$requete .= " SET libelle_groupe='".$this->libelle."'";
			$requete .= ", resp_groupe='".$this->id_resp."'";
			$requete .= ", lettre_rappel='".$this->lettre_rappel."'";
			$requete .= ", mail_rappel='".$this->mail_rappel."'";
			$requete .= ", lettre_rappel_show_nomgroup='".$this->lettre_rappel_show_nomgroup."'";
			$requete .= " WHERE id_groupe=".$this->id." LIMIT 1";
			$res = pmb_mysql_query($requete, $dbh);
		} else {
			// on voit si ça n'existe pas
			if($this->exists($this->libelle)) return $this->id;
			
			// création
			$requete = "INSERT INTO groupe SET id_groupe=''";
			$requete .= ", libelle_groupe='".$this->libelle."'";
			$requete .= ", resp_groupe='".$this->id_resp."'";
			$requete .= ", lettre_rappel='".$this->lettre_rappel."'";
			$requete .= ", mail_rappel='".$this->mail_rappel."'";
			$requete .= ", lettre_rappel_show_nomgroup='".$this->lettre_rappel_show_nomgroup."'";
			$result = pmb_mysql_query($requete, $dbh);
			$this->id = pmb_mysql_insert_id();
		}
		return $this->id;
	}

	function exists($name) {
		global $dbh;
		if(!$name) return;
		$requete = "SELECT count(1) FROM groupe";
		$requete .= " WHERE libelle_groupe='$name'";
		$result = pmb_mysql_query($requete, $dbh);
		return pmb_mysql_result($result, 0, 0);
	}
	
	// prolongation d'adhésion des membres en fin d'abonnement ou en abonnement dépassé
	function update_members() {
		global $dbh;
		global $msg;
	
		if($this->id) {
			if($this->nb_members) {
				while(list($cle, $membre) = each($this->members)) {
					$date_prolong = "form_expiration_".$membre['id'];
					global $$date_prolong;
					if ($$date_prolong != "") {
						//Ne pas débiter l'abonnement deux fois..
						$requete = "SELECT empr_date_expiration FROM empr WHERE id_empr=".$membre['id'];
						$resultat = pmb_mysql_query($requete,$dbh);
						if ($resultat) {
							if (str_replace("-","",pmb_mysql_result($resultat,0,0)) != str_replace("-","",$$date_prolong)) {
								// mise à jour
								$requete = "UPDATE empr";
								$requete .= " SET empr_date_expiration='".$$date_prolong."'";
								$requete .= " WHERE id_empr=".$membre['id']." LIMIT 1";
								@pmb_mysql_query($requete, $dbh);
								if(!pmb_mysql_errno($dbh)) {
									global $debit;
									if ($debit) {
										if ($debit==2) $rec_caution=true; else $rec_caution=false;
										emprunteur::rec_abonnement($membre['id'],$membre['id_abt'],$membre['id_categ'],$rec_caution);
									}
								} else {
									error_message($msg[540], "erreur modification emprunteur", 1, './circ.php?categ=groups&action=showgroup&groupID=".$this->id."');
								}
							}
						}
					}
				}
			}
		}
	}

}
