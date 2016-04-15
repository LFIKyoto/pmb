<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author.class.php,v 1.96.2.2 2015-11-18 09:08:24 mbertin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");
	
	// définition de la classe de gestion des 'auteurs'
if (! defined('AUTEUR_CLASS')) {
	define('AUTEUR_CLASS', 1);
	
	require_once ($class_path ."/notice.class.php");
	require_once ("$class_path/aut_link.class.php");
	require_once ("$class_path/aut_pperso.class.php");
	require_once ("$class_path/audit.class.php");
	require_once ($class_path ."/synchro_rdf.class.php");
	require_once ($class_path ."/index_concept.class.php");
	require_once ($class_path ."/vedette/vedette_composee.class.php");
	require_once ($include_path ."/misc.inc.php");
	require_once ($include_path ."/isbn.inc.php");
	class auteur {
		
		// ---------------------------------------------------------------
		// propriétés de la classe
		// ---------------------------------------------------------------
		var $id; // MySQL id in table 'authors'
		var $type; // author type (70 or 71)
		var $name; // author name
		var $rejete; // author name (rejected element)
		var $date; // dates
		var $author_web; // web de l'auteur
		var $author_web_link; // lien web de l'auteur
		var $see; // 'see' author MySQL id
		var $see_libelle; // printable form of 'see' author (in fact 'display' of retained form)
		var $display; // usable form for displaying ( _name_, _rejete_ (_date1_-_date2_) )
		var $isbd_entry; // isbd like version ( _rejete_ _name_ (_date1_-_date2_))
		var $isbd_entry_lien_gestion; // lien sur le nom vers la gestion
		var $lieu; // lieu du congrès
		var $ville; // ville du congrès
		var $pays; // pays du congrès
		var $subdivision; // subdivision
		var $numero; // numero de congrès
		var $author_comment; // Commentaire, peut contenir du HTML
		var $duplicate_from_id = 0;
		var $import_denied = 0; // booléen pour interdire les modification depuis un import d'autorités
		var $info_bulle ="";
		                        
		// ---------------------------------------------------------------
		                        // auteur($id) : constructeur
		                        // ---------------------------------------------------------------
		function auteur($id = 0, $recursif = 0) {
			// echo "AUTHOR.CLASS $id<br />" ;
			if ($id) {
				// on cherche à atteindre une notice existante
				$this->recursif = $recursif;
				$this->id = $id;
				$this->getData();
			} else {
				// la notice n'existe pas
				$this->id = 0;
				$this->getData();
			}
		}
		
		// ---------------------------------------------------------------
		// getData() : récupération infos auteur
		// ---------------------------------------------------------------
		function getData() {
			global $dbh, $msg;
			if (! $this->id) {
				// pas d'identifiant.
				$this->id = 0;
				$this->type = '';
				$this->name = '';
				$this->rejete = '';
				$this->date = '';
				$this->author_web = '';
				$this->see = '';
				$this->see_libelle = '';
				$this->display = '';
				$this->isbd_entry = '';
				$this->author_comment = '';
				$this->subdivision = '';
				$this->lieu = '';
				$this->ville = '';
				$this->pays = '';
				$this->numero = '';
				$this->import_denied = 0;
			} else {
				$requete = "SELECT * FROM authors WHERE author_id=$this->id LIMIT 1 ";
				$result = @pmb_mysql_query($requete, $dbh);
				if (pmb_mysql_num_rows($result)) {
					$temp = pmb_mysql_fetch_object($result);
					pmb_mysql_free_result($result);
					$this->id = $temp->author_id;
					$this->type = $temp->author_type;
					$this->name = $temp->author_name;
					$this->rejete = $temp->author_rejete;
					$this->date = $temp->author_date;
					$this->author_web = $temp->author_web;
					$this->see = $temp->author_see;
					$this->author_comment = $temp->author_comment;
					// Ajout pour les congrès
					$this->subdivision = $temp->author_subdivision;
					$this->lieu = $temp->author_lieu;
					$this->ville = $temp->author_ville;
					$this->pays = $temp->author_pays;
					$this->numero = $temp->author_numero;
					$this->import_denied = $temp->author_import_denied;
					if ($this->type ==71) {
						// C'est une collectivité
						$this->isbd_entry = $temp->author_name;
						$this->display = $temp->author_name;
						
						if ($temp->author_subdivision) {
							$this->isbd_entry .= ". " .$temp->author_subdivision;
							$this->display .= ". " .$temp->author_subdivision;
						}
						
						if ($temp->author_rejete) {
							$this->isbd_entry .= ", " .$temp->author_rejete;
							$this->display .= ", " .$temp->author_rejete;
							// $this->info_bulle=$temp->author_rejete;
						}
						$liste_field = $liste_lieu = array();
						
						if ($temp->author_numero) {
							$liste_field[] = $temp->author_numero;
						}
						if ($temp->author_date) {
							$liste_field[] = $temp->author_date;
						}
						if ($temp->author_lieu) {
							$liste_lieu[] = $temp->author_lieu;
						}
						if ($temp->author_ville) {
							$liste_lieu[] = $temp->author_ville;
						}
						if ($temp->author_pays) {
							$liste_lieu[] = $temp->author_pays;
						}
						if (count($liste_lieu))
							$liste_field[] = implode(", ", $liste_lieu);
						if (count($liste_field)) {
							$liste_field = implode("; ", $liste_field);
							$this->isbd_entry .= ' (' .$liste_field .')';
							$this->display .= ' (' .$liste_field .')';
						}
					} elseif ($this->type ==72) {
						// C'est un congrès
						$libelle = $msg["congres_libelle"] .": ";
						if ($temp->author_rejete) {
							$this->isbd_entry = $temp->author_name .", " .$temp->author_rejete;
							$this->display = $libelle .$temp->author_name .", " .$temp->author_rejete;
						} else {
							$this->isbd_entry = $temp->author_name;
							$this->display = $libelle .$temp->author_name;
						}
						$liste_field = $liste_lieu = array();
						if ($temp->author_subdivision) {
							$liste_field[] = $temp->author_subdivision;
						}
						if ($temp->author_numero) {
							$liste_field[] = $temp->author_numero;
						}
						if ($temp->author_date) {
							$liste_field[] = $temp->author_date;
						}
						if ($temp->author_lieu) {
							$liste_lieu[] = $temp->author_lieu;
						}
						if ($temp->author_ville) {
							$liste_lieu[] = $temp->author_ville;
						}
						if ($temp->author_pays) {
							$liste_lieu[] = $temp->author_pays;
						}
						if (count($liste_lieu))
							$liste_field[] = implode(", ", $liste_lieu);
						if (count($liste_field)) {
							$liste_field = implode("; ", $liste_field);
							$this->isbd_entry .= ' (' .$liste_field .')';
							$this->display .= ' (' .$liste_field .')';
						}
					} else {
						// auteur physique
						if ($temp->author_rejete) {
							$this->isbd_entry = "$temp->author_name, $temp->author_rejete";
							$this->display = "$temp->author_name, $temp->author_rejete";
						} else {
							$this->isbd_entry = $temp->author_name;
							$this->display = $temp->author_name;
						}
						if ($temp->author_date) {
							$this->isbd_entry .= ' (' .$temp->author_date .')';
						}
					}
					// Ajoute un lien sur la fiche auteur si l'utilisateur à accès aux autorités
					if (SESSrights &AUTORITES_AUTH)
						$this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=auteurs&sub=author_form&id=" .$this->id ."' class='lien_gestion' title='" .$this->info_bulle ."'>" .$this->display ."</a>";
					else
						$this->isbd_entry_lien_gestion = $this->display;
					
					if ($temp->author_web)
						$this->author_web_link = " <a href='$temp->author_web' target=_blank><img src='./images/globe.gif' border=0 /></a>";
					else
						$this->author_web_link = "";
					
					if ($temp->author_see &&! $this->recursif) {
						$see = new auteur($temp->author_see, 1);
						$this->see_libelle = $see->display;
					} else {
						$this->see_libelle = '';
					}
				} else {
					// pas d'auteur avec cette clé
					$this->id = 0;
					$this->type = '';
					$this->name = '';
					$this->rejete = '';
					$this->date = '';
					$this->author_web = '';
					$this->see = '';
					$this->see_libelle = '';
					$this->display = '';
					$this->isbd_entry = '';
					$this->author_web_link = "";
					$this->author_comment = '';
					$this->subdivision = '';
					$this->lieu = '';
					$this->ville = '';
					$this->pays = '';
					$this->numero = '';
					$this->import_denied = 0;
				}
			}
		}
		
		// ---------------------------------------------------------------
		// show_form : affichage du formulaire de saisie
		// ---------------------------------------------------------------
		function show_form($type_autorite = 70) {
			global $msg;
			global $author_form;
			global $dbh;
			global $charset;
			global $pmb_type_audit;
			global $thesaurus_concepts_active;
			
			$liste_renvoyes = "";
			if ($this->id) {
				$action = "./autorites.php?categ=auteurs&sub=update&id=$this->id";
				$libelle = $msg[199];
				$button_remplace = "<input type='button' class='bouton' value='$msg[158]' ";
				$button_remplace .= "onclick='unload_off();document.location=\"./autorites.php?categ=auteurs&sub=replace&id=$this->id\"'>";
				
				$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' ";
				$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=0&etat=aut_search&aut_id=$this->id\"'>";
				
				$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
				$button_delete .= "onClick=\"confirm_delete();\">";
				
				$requete = "SELECT * FROM authors WHERE ";
				$requete .= "author_see = '$this->id' ";
				$requete .= "ORDER BY author_name, author_rejete ";
				$res = @pmb_mysql_query($requete, $dbh);
				$nbr_lignes = pmb_mysql_num_rows($res);
				if ($nbr_lignes) {
					$liste_renvoyes = "<br /><div class='row'><h3>$msg[aut_list_renv_titre]</h3><table>";
					$parity = 1;
					while ( ($author_renvoyes = pmb_mysql_fetch_object($res)) ) {
						$author_renvoyes->author_name = $author_renvoyes->author_name;
						$author_renvoyes->author_rejete = $author_renvoyes->author_rejete;
						if ($author_renvoyes->author_rejete)
							$author_entry = $author_renvoyes->author_name .',&nbsp;' .$author_renvoyes->author_rejete;
						else
							$author_entry = $author_renvoyes->author_name;
						if ($author_renvoyes->author_date)
							$author_entry .= "&nbsp;($author_renvoyes->author_date)";
						$link_auteur = "./autorites.php?categ=auteurs&sub=author_form&id=$author_renvoyes->author_id";
						if ($parity %2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$parity += 1;
						$tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='$link_auteur';\" ";
						$liste_renvoyes .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
									<td valign='top'>
								$author_entry
								</td>
							</tr>";
					} // fin while
					$liste_renvoyes .= "</table></div>";
				}
			} else {
				$action = './autorites.php?categ=auteurs&sub=update&id=';
				$libelle = $msg[207];
				$button_remplace = '';
				$button_delete = '';
			}
			
			// Si on est en modif ou non
			if (! $this->id) {
				$this->type = $type_autorite;
				$author_form = str_replace('!!dupliquer!!', "", $author_form);
			}
			
			// mise à jour de la zone type
			switch ($this->type) {
				case 71 :
					$sel_coll = " SELECTED";
					// Si on est en modif ou non
					if ($this->id) {
						$libelle = $msg["aut_modifier_coll"];
						$bouton_dupliquer = "<input type='button' id='dupli_btn' value='" .$msg["aut_duplicate"] ."' class='bouton' onClick='unload_off();document.location=\"./autorites.php?categ=auteurs&sub=duplicate&type_autorite=" .$this->type ."&id=" .$this->id ."\"'/>";
						$author_form = str_replace('!!dupliquer!!', $bouton_dupliquer, $author_form);
					} else
						$libelle = $msg["aut_ajout_collectivite"];
					$completion_name = "collectivite_name";
					break;
				case 72 :
					// Si on est en modif ou non
					if ($this->id) {
						$libelle = $msg["aut_modifier_congres"];
						$bouton_dupliquer = "<input type='button' id='dupli_btn' value='" .$msg["aut_duplicate"] ."' class='bouton' onClick='unload_off();document.location=\"./autorites.php?categ=auteurs&sub=duplicate&type_autorite=" .$this->type ."&id=" .$this->id ."\"'/>";
						$author_form = str_replace('!!dupliquer!!', $bouton_dupliquer, $author_form);
					} else
						$libelle = $msg["aut_ajout_congres"];
					$sel_congres = " SELECTED";
					$completion_name = "congres_name";
					break;
				default :
					$author_form = str_replace('!!display!!', "display:none", $author_form);
					$author_form = str_replace('!!dupliquer!!', "", $author_form);
					$sel_pp = " SELECTED";
					$completion_name = " ";
					break;
			}
			if ($this->import_denied ==1) {
				$import_denied_checked = "checked='checked'";
			} else {
				$import_denied_checked = "";
			}
			if ($pmb_type_audit &&$this->id)
				$bouton_audit = "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=" .AUDIT_AUTHOR ."&object_id=" .$this->id ."', 'audit_popup', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\" title=\"" .$msg['audit_button'] ."\" value=\"" .$msg['audit_button'] ."\" />&nbsp;";
			
			$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
			$author_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_auteur'), $author_form);
			
			$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
			$author_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_auteur'), $author_form);
			
			$aut_pperso = new aut_pperso("author", $this->id);
			$author_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $author_form);
			
			$author_form = str_replace('!!id!!', $this->id, $author_form);
			$author_form = str_replace('!!action!!', $action, $author_form);
			$author_form = str_replace('!!libelle!!', $libelle, $author_form);
			$author_form = str_replace('!!author_nom!!', htmlentities($this->name, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!author_rejete!!', htmlentities($this->rejete, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!voir_id!!', $this->see, $author_form);
			$author_form = str_replace('!!voir_libelle!!', htmlentities($this->see_libelle, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!date!!', htmlentities($this->date, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!lieu!!', htmlentities($this->lieu, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!ville!!', htmlentities($this->ville, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!pays!!', htmlentities($this->pays, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!subdivision!!', htmlentities($this->subdivision, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!numero!!', htmlentities($this->numero, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!author_web!!', htmlentities($this->author_web, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!sel_pp!!', $sel_pp, $author_form);
			$author_form = str_replace('!!sel_coll!!', $sel_coll, $author_form);
			$author_form = str_replace('!!sel_congres!!', $sel_congres, $author_form);
			$author_form = str_replace('!!remplace!!', $button_remplace, $author_form);
			$author_form = str_replace('!!voir_notices!!', $button_voir, $author_form);
			$author_form = str_replace('!!delete!!', $button_delete, $author_form);
			$author_form = str_replace('!!liste_des_renvoyes_vers!!', $liste_renvoyes, $author_form);
			$author_form = str_replace('!!completion_name!!', $completion_name, $author_form);
			$author_form = str_replace('!!type_autorite!!', $this->type, $author_form);
			// pour retour à la bonne page en gestion d'autorités
			// &user_input=".rawurlencode(stripslashes($user_input))."&nbr_lignes=$nbr_lignes&page=$page
			global $user_input, $nbr_lignes, $page;
			$author_form = str_replace('!!user_input_url!!', rawurlencode(stripslashes($user_input)), $author_form);
			$author_form = str_replace('!!user_input!!', htmlentities($user_input, ENT_QUOTES, $charset), $author_form);
			$author_form = str_replace('!!nbr_lignes!!', "", $author_form);
			$author_form = str_replace('!!page!!', $page, $author_form);
			$author_form = str_replace('!!author_comment!!', $this->author_comment, $author_form);
			$author_form = str_replace('!!author_import_denied!!', $import_denied_checked, $author_form);
			$author_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $author_form);
			$author_form = str_replace('!!audit_bt!!', $bouton_audit, $author_form);
			if ($thesaurus_concepts_active ==1) {
				$index_concept = new index_concept($this->id, TYPE_AUTHOR);
				$author_form = str_replace('!!concept_form!!', $index_concept->get_form('saisie_auteur'), $author_form);
			} else {
				$author_form = str_replace('!!concept_form!!', "", $author_form);
			}
			print $author_form;
		}
		
		// ---------------------------------------------------------------
		// replace_form : affichage du formulaire de remplacement
		// ---------------------------------------------------------------
		function replace_form() {
			global $author_replace;
			global $msg;
			global $include_path;
			
			// a compléter
			
			if (! $this->id ||! $this->name) {
				require_once ("$include_path/user_error.inc.php");
				error_message($msg[161], $msg[162], 1, './autorites.php?categ=auteurs&sub=&id=');
				return false;
			}
			
			$author_replace = str_replace('!!old_author_libelle!!', $this->display, $author_replace);
			$author_replace = str_replace('!!id!!', $this->id, $author_replace);
			print $author_replace;
			return true;
		}
		
		// ---------------------------------------------------------------
		// delete() : suppression de l'auteur
		// ---------------------------------------------------------------
		function delete() {
			global $dbh;
			global $msg;
			
			if (! $this->id) // impossible d'accéder à cette notice auteur
				return $msg[403];
				
				// effacement dans les notices
				// récupération du nombre de notices affectées
			$requete = "SELECT count(1) FROM responsability WHERE ";
			$requete .= "responsability_author='$this->id' ";
			
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if ($nbr_lignes) {
				// Cet auteur est utilisé dans des notices, impossible de le supprimer
				return '<strong>' .$this->display ."</strong><br />${msg[402]}";
			}
			// effacement dans les titres uniformes
			// récupération du nombre de titres affectées
			$requete = "SELECT count(1) FROM responsability_tu WHERE ";
			$requete .= "responsability_tu_author_num='$this->id' ";
			
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if ($nbr_lignes) {
				// Cet auteur est utilisé dans des tirres uniformes, impossible de le supprimer
				return '<strong>' .$this->display ."</strong><br />${msg[tu_dont_del_author]}";
			}
			
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, "author");
			if (count($attached_vedettes)) {
				// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
				return '<strong>' .$this->display ."</strong><br />" .$msg["vedette_dont_del_autority"];
			}
			
			// liens entre autorités
			$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
			$aut_link->delete();
			$aut_pperso = new aut_pperso("author", $this->id);
			$aut_pperso->delete();
			
			// nettoyage indexation concepts
			$index_concept = new index_concept($this->id, TYPE_AUTHOR);
			$index_concept->delete();
			
			// suppression dans la table de stockage des numéros d'autorités...
			auteur::delete_autority_sources($this->id);
			
			// on supprime automatiquement les formes rejetes
			$query = "select author_id from authors where author_see = " .$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ( $row = pmb_mysql_fetch_object($result) ) {
					// on regarde si cette forme est utilisée...
					$query2 = "select count(responsability_author) from responsability where responsability_author =" .$row->author_id;
					$result2 = pmb_mysql_query($query2);
					$query3 = "select count(responsability_tu_author_num) from responsability_tu where responsability_tu_author_num =" .$row->author_id;
					$result3 = pmb_mysql_query($query3);
					$rejete = new auteur($row->author_id);
					// elle est utilisée donc on nettoie juste la référence
					if (pmb_mysql_num_rows($result2) ||pmb_mysql_num_rows($result3)) {
						pmb_mysql_query("update authors set author_see= 0  where author_id = " .$row->author_id);
					} else {
						// sinon, on supprime...
						$rejete->delete();
					}
				}
			}
			audit::delete_audit(AUDIT_AUTHOR, $this->id);
			// effacement dans l'entrepot rdf
			auteur::delete_enrichment($id);
			// effacement dans la table des auteurs
			$requete = "DELETE FROM authors WHERE author_id='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			return false;
		}
		
		// ---------------------------------------------------------------
		// delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
		// ---------------------------------------------------------------
		static function delete_autority_sources($idaut = 0) {
			$tabl_id = array();
			if (! $idaut) {
				$requete = "SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN authors ON num_authority=author_id  WHERE authority_type = 'author' AND author_id IS NULL";
				$res = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($res)) {
					while ( $ligne = pmb_mysql_fetch_object($res) ) {
						$tabl_id[] = $ligne->num_authority;
					}
				}
			} else {
				$tabl_id[] = $idaut;
			}
			foreach ( $tabl_id as $value ) {
				// suppression dans la table de stockage des numéros d'autorités...
				$query = "select id_authority_source from authorities_sources where num_authority = " .$value ." and authority_type = 'author'";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ( $ligne = pmb_mysql_fetch_object($result) ) {
						$query = "delete from notices_authorities_sources where num_authority_source = " .$ligne->id_authority_source;
						pmb_mysql_query($query);
					}
				}
				$query = "delete from authorities_sources where num_authority = " .$value ." and authority_type = 'author'";
				pmb_mysql_query($query);
			}
		}
		
		// ---------------------------------------------------------------
		// replace($by) : remplacement de l'auteur
		// ---------------------------------------------------------------
		function replace($by, $link_save = 0) {
			global $msg;
			global $dbh;
			global $pmb_synchro_rdf;
			
			if (($this->id ==$by) ||(! $this->id)) {
				return $msg[223];
			}
			$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
			// "Conserver les liens entre autorités" est demandé
			if ($link_save) {
				// liens entre autorités
				$aut_link->add_link_to(AUT_TABLE_AUTHORS, $by);
				// Voir aussi
				if ($this->see) {
					$requete = "UPDATE authors SET author_see='" .$this->see ."'  WHERE author_id='$by' ";
					@pmb_mysql_query($requete, $dbh);
				}
			}
			$aut_link->delete();
			
			// remplacement dans les responsabilités
			$requete = "UPDATE responsability SET responsability_author='$by' WHERE responsability_author='$this->id' ";
			@pmb_mysql_query($requete, $dbh);
			
			// effacement dans les responsabilités
			$requete = "DELETE FROM responsability WHERE responsability_author='$this->id' ";
			@pmb_mysql_query($requete, $dbh);
			
			// remplacement dans les titres uniformes
			$requete = "UPDATE responsability_tu SET responsability_tu_author_num='$by' WHERE responsability_tu_author_num='$this->id' ";
			@pmb_mysql_query($requete, $dbh);
			$requete = "DELETE FROM responsability_tu WHERE responsability_tu_author_num='$this->id' ";
			@pmb_mysql_query($requete, $dbh);
			
			// effacement dans la table des auteurs
			$requete = "DELETE FROM authors WHERE author_id='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			// nettoyage d'autorities_sources
			$query = "select * from authorities_sources where num_authority = " .$this->id ." and authority_type = 'author'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ( $row = pmb_mysql_fetch_object($result) ) {
					if ($row->authority_favorite ==1) {
						// on suprime les références si l'autorité a été importée...
						$query = "delete from notices_authorities_sources where num_authority_source = " .$row->id_authority_source;
						pmb_mysql_result($query);
						$query = "delete from authorities_sources where id_authority_source = " .$row->id_authority_source;
						pmb_mysql_result($query);
					} else {
						// on fait suivre le reste
						$query = "update authorities_sources set num_authority = " .$by ." where num_authority_source = " .$row->id_authority_source;
						pmb_mysql_query($query);
					}
				}
			}
			audit::delete_audit(AUDIT_AUTHOR, $this->id);
			
			auteur::update_index($by);
			
			// mise à jour de l'oeuvre rdf
			if ($pmb_synchro_rdf) {
				$synchro_rdf = new synchro_rdf();
				$synchro_rdf->replaceAuthority($this->id, $by, 'auteur');
			}
			
			return FALSE;
		}
		
		// ---------------------------------------------------------------
		// update($value) : mise à jour de l'auteur
		// ---------------------------------------------------------------
		function update($value, $force = false) {
			global $dbh;
			global $msg, $charset;
			global $include_path;
			global $pmb_synchro_rdf;
			global $thesaurus_concepts_active;
			global $opac_enrichment_bnf_sparql;
			
			if (! $value['name'])
				return false;
				
				// nettoyage des chaînes en entrée
			$value['name'] = clean_string($value['name']);
			$value['rejete'] = clean_string($value['rejete']);
			$value['date'] = clean_string($value['date']);
			$value['lieu'] = clean_string($value['lieu']);
			$value['ville'] = clean_string($value['ville']);
			$value['pays'] = clean_string($value['pays']);
			$value['subdivision'] = clean_string($value['subdivision']);
			$value['numero'] = clean_string($value['numero']);
			
			if (! $force) {
				if ($this->id) {
					// s'assurer que l'auteur n'existe pas déjà
					switch ($value['type']) {
						case 71 : // Collectivité
							$and_dedoublonnage = " and author_subdivision ='" .$value['subdivision'] ."' and author_lieu='" .$value['lieu'] ."' and author_ville = '" .$value['ville'] ."' and author_pays = '" .$value['pays'] ."' and author_numero ='" .$value['numero'] ."' ";
							break;
						case 72 : // Congrès
							$and_dedoublonnage = " and author_subdivision ='" .$value['subdivision'] ."' and author_lieu='" .$value['lieu'] ."' and author_ville = '" .$value['ville'] ."' and author_pays = '" .$value['pays'] ."' and author_numero ='" .$value['numero'] ."' ";
							break;
						default :
							$and_dedoublonnage = '';
							break;
					}
					$dummy = "SELECT * FROM authors WHERE author_type='" .$value['type'] ."' AND author_name='" .$value['name'] ."'";
					$dummy .= " AND author_rejete='" .$value['rejete'] ."' ";
					$dummy .= "AND author_date='" .$value[date] ."' and author_id!='" .$this->id ."' $and_dedoublonnage ";
					
					$check = pmb_mysql_query($dummy, $dbh);
					if (pmb_mysql_num_rows($check)) {
						$auteur_exists = new auteur(pmb_mysql_result($check, 0, "author_id"));
						require_once ("$include_path/user_error.inc.php");
						warning($msg[200], htmlentities($msg[220] ." -> " .$auteur_exists->display, ENT_QUOTES, $charset));
						return FALSE;
					}
				} else {
					// s'assurer que l'auteur n'existe pas déjà
					if ($id_auteur_exists = auteur::check_if_exists($value)) {
						$auteur_exists = new auteur($id_auteur_exists);
						require_once ("$include_path/user_error.inc.php");
						warning($msg[200], htmlentities($msg[220] ." -> " .$auteur_exists->display, ENT_QUOTES, $charset));
						return FALSE;
					}
				}
				
				// s'assurer que la forme_retenue ne pointe pas dans les deux sens
				if ($this->id) {
					$dummy = "SELECT * FROM authors WHERE author_id='" .$value[voir_id] ."' and  author_see='" .$this->id ."'";
					$check = pmb_mysql_query($dummy, $dbh);
					if (pmb_mysql_num_rows($check)) {
						require_once ("$include_path/user_error.inc.php");
						warning($msg[200], htmlentities($msg['author_forme_retenue_error'] ." -> " .$this->display, ENT_QUOTES, $charset));
						return FALSE;
					}
				}
			}
			$requete = "SET author_type='$value[type]', ";
			$requete .= "author_name='$value[name]', ";
			$requete .= "author_rejete='$value[rejete]', ";
			$requete .= "author_date='$value[date]', ";
			$requete .= "author_lieu='" .$value["lieu"] ."', ";
			$requete .= "author_ville='" .$value["ville"] ."', ";
			$requete .= "author_pays='" .$value["pays"] ."', ";
			$requete .= "author_subdivision='" .$value["subdivision"] ."', ";
			$requete .= "author_numero='" .$value["numero"] ."', ";
			$requete .= "author_web='$value[author_web]', ";
			$requete .= "author_see='$value[voir_id]', ";
			$requete .= "author_comment='$value[author_comment]', ";
			$word_to_index = $value["name"] ." " .$value["rejete"] ." " .$value["lieu"] ." " .$value["ville"] ." " .$value["pays"] ." " .$value["numero"] ." " .$value["subdivision"];
			if ($value['type'] ==72)
				$word_to_index .= " " .$value["date"];
			$requete .= "index_author=' " .strip_empty_chars($word_to_index) ." ',";
			$requete .= "author_import_denied= " .($value['import_denied'] ? 1 : 0);
			if ($this->id) {
				
				audit::insert_modif(AUDIT_AUTHOR, $this->id);
				
				// update
				// on checke s'il n'y a pas un renvoi circulaire
				if ($this->id ==$value['voir_id']) {
					require_once ("$include_path/user_error.inc.php");
					warning($msg[199], htmlentities($msg[222] ." -> " .$this->display, ENT_QUOTES, $charset));
					return FALSE;
				}
				
				$requete = 'UPDATE authors ' .$requete;
				$requete .= ' WHERE author_id=' .$this->id .' ;';
				if (pmb_mysql_query($requete, $dbh)) {
					// liens entre autorités
					$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
					$aut_link->save_form();
					$aut_pperso = new aut_pperso("author", $this->id);
					$aut_pperso->save_form();
					auteur::update_index($this->id);
					
					// mise à jour de l'auteur dans la base rdf
					if ($pmb_synchro_rdf) {
						$synchro_rdf = new synchro_rdf();
						$synchro_rdf->updateAuthority($this->id, 'auteur');
					}
					
					// ////////////////////////modif de l'update///////////////////////////////
					if($opac_enrichment_bnf_sparql){
						$query = "select 1 from authors where (author_enrichment_last_update < now()-interval '0' day) and author_id=$this->id";
						$result = pmb_mysql_query($query, $dbh);
						if ($result && pmb_mysql_num_rows($result)) {
							auteur::author_enrichment($this->id);
						}
					}
					
					// ////////////////////////////////////////////////////////////////////////
				} else {
					require_once ("$include_path/user_error.inc.php");
					warning($msg[199], htmlentities($msg[208] ." -> " .$this->display, ENT_QUOTES, $charset));
					return FALSE;
				}
			} else {
				// creation
				$requete = 'INSERT INTO authors ' .$requete .' ';
				if (pmb_mysql_query($requete, $dbh)) {
					$this->id = pmb_mysql_insert_id();
					// liens entre autorités
					$aut_link = new aut_link(AUT_TABLE_AUTHORS, $this->id);
					$aut_link->save_form();
					$aut_pperso = new aut_pperso("author", $this->id);
					$aut_pperso->save_form();
					
					audit::insert_creation(AUDIT_AUTHOR, $this->id);
					
					// ajout des enrichissements si activés
					if ($opac_enrichment_bnf_sparql) {
						auteur::author_enrichment($this->id);
					}
					
				} else {
					require_once ("$include_path/user_error.inc.php");
					warning($msg[200], htmlentities($msg[221] ." -> " .$requete, ENT_QUOTES, $charset));
					return FALSE;
				}
			}
			// Indexation concepts
			if ($thesaurus_concepts_active ==1) {
				$index_concept = new index_concept($this->id, TYPE_AUTHOR);
				$index_concept->save();
			}
			
			// Mise à jour des vedettes composées contenant cette autorité
			vedette_composee::update_vedettes_built_with_element($this->id, "author");
			
			return TRUE;
		}
		
		// ---------------------------------------------------------------
		// import() : import d'un auteur
		// ---------------------------------------------------------------
		// fonction d'import de notice auteur (membre de la classe 'author');
		function import($data) {
			
			// cette méthode prend en entrée un tableau constitué des informations éditeurs suivantes :
			// $data['type'] type de l'autorité (70 , 71 ou 72)
			// $data['name'] élément d'entrée de l'autorité
			// $data['rejete'] élément rejeté
			// $data['date'] dates de l'autorité
			// $data['lieu'] lieu du congrès 210$e
			// $data['ville'] ville du congrès
			// $data['pays'] pays du congrès
			// $data['subdivision'] 210$b
			// $data['numero'] numero du congrès 210$d
			// $data['voir_id'] id de la forme retenue (sans objet pour l'import de notices)
			// $data['author_comment'] commentaire
			// $data['authority_number'] Numéro d'autortité
			
			// TODO gestion du dédoublonnage !
			global $dbh;
			global $opac_enrichment_bnf_sparql;
			
			// check sur le type de la variable passée en paramètre
			if (! sizeof($data) ||! is_array($data)) {
				// si ce n'est pas un tableau ou un tableau vide, on retourne 0
				return 0;
			}
			// check sur les éléments du tableau (data['name'] ou data['rejete'] est requis).
			$long_maxi_name = pmb_mysql_field_len(pmb_mysql_query("SELECT author_name FROM authors limit 1"), 0);
			$long_maxi_rejete = pmb_mysql_field_len(pmb_mysql_query("SELECT author_rejete FROM authors limit 1"), 0);
			
			$data['name'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))), 0, $long_maxi_name));
			$data['rejete'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['rejete']))), 0, $long_maxi_rejete));
			
			if (! $data['name'] &&! $data['rejete']) {
				return 0;
			}
			
			// check sur le type d'autorité
			if (! $data['type'] ==70 &&! $data['type'] ==71 &&! $data['type'] ==72) {
				return 0;
			}
			
			// tentative de récupérer l'id associée dans la base (implique que l'autorité existe)
			
			// préparation de la requête
			$key0 = $data['type'];
			$key1 = addslashes($data['name']);
			$key2 = addslashes($data['rejete']);
			$key3 = addslashes($data['date']);
			$key4 = addslashes($data['subdivision']);
			$key5 = addslashes($data['lieu']);
			$key6 = addslashes($data['ville']);
			$key7 = addslashes($data['pays']);
			$key8 = addslashes($data['numero']);
			
			$data['lieu'] = addslashes($data['lieu']);
			$data['ville'] = addslashes($data['ville']);
			$data['pays'] = addslashes($data['pays']);
			$data['subdivision'] = addslashes($data['subdivision']);
			$data['numero'] = addslashes($data['numero']);
			$data['author_comment'] = addslashes($data['author_comment']);
			$data['author_web'] = addslashes($data['author_web']);
			
			$query = "SELECT author_id FROM authors WHERE author_type='${key0}' AND author_name='${key1}' AND author_rejete='${key2}' AND author_date='${key3}'";
			if ($data["type"] >70) {
				$query .= " and author_subdivision='${key4}' and author_lieu='${key5}' and author_ville='${key6}' and author_pays='${key7}' and author_numero='${key8}'";
			}
			$query .= " LIMIT 1";
			$result = @pmb_mysql_query($query, $dbh);
			if (! $result)
				die("can't SELECT in database");
				// résultat
				
			// récupération du résultat de la recherche
			$aut = pmb_mysql_fetch_object($result);
			// du résultat et récupération éventuelle de l'id
			if ($aut->author_id)
				return $aut->author_id;
				
				// id non-récupérée, il faut créer l'auteur
			$query = "INSERT INTO authors SET author_type='$key0', ";
			$query .= "author_name='$key1', ";
			$query .= "author_rejete='$key2', ";
			$query .= "author_date='$key3', ";
			$query .= "author_lieu='" .$data['lieu'] ."', ";
			$query .= "author_ville='" .$data['ville'] ."', ";
			$query .= "author_pays='" .$data['pays'] ."', ";
			$query .= "author_subdivision='" .$data['subdivision'] ."', ";
			$query .= "author_numero='" .$data['numero'] ."', ";
			$query .= "author_web='" .$data['author_web'] ."', ";
			$query .= "author_comment='" .$data['author_comment'] ."', ";
			$word_to_index = $key1 ." " .$key2 ." " .$data['lieu'] ." " .$data['ville'] ." " .$data['pays'] ." " .$data['numero'] ." " .$data["subdivision"];
			if ($key0 =="72")
				$word_to_index .= " " .$key3;
			$query .= "index_author=' " .strip_empty_chars($word_to_index) ." ' ";
			
			$result = @pmb_mysql_query($query, $dbh);
			if (! $result)
				die("can't INSERT into table authors :<br /><b>$query</b> ");
			
			$id = pmb_mysql_insert_id($dbh);
			audit::insert_creation(AUDIT_AUTHOR, $id);
			
			return $id;
		}
		
		// ---------------------------------------------------------------
		// search_form() : affichage du form de recherche
		// ---------------------------------------------------------------
		static function search_form($type_autorite = 7) {
			global $user_query;
			global $msg;
			global $user_input, $charset;
			
			$sel_tout = ($type_autorite ==7) ? 'selected' : " ";
			$sel_pp = ($type_autorite ==70) ? 'selected' : " ";
			$sel_coll = ($type_autorite ==71) ? 'selected' : " ";
			$sel_congres = ($type_autorite ==72) ? 'selected' : " ";
			
			$libelleBtn = $msg[207];
			if ($type_autorite ==7 ||$type_autorite ==70)
				$libelleBtn = $msg[207];
			elseif ($type_autorite ==71)
				$libelleBtn = $msg["aut_ajout_collectivite"];
			elseif ($type_autorite ==72)
				$libelleBtn = $msg["aut_ajout_congres"];
			
			$libelleRech = $msg[133];
			if ($type_autorite ==7 ||$type_autorite ==70)
				$libelleRech = $msg[133];
			elseif ($type_autorite ==71)
				$libelleRech = $msg[204];
			elseif ($type_autorite ==72)
				$libelleRech = $msg["congres_libelle"];
			
			$url = "\"document.location = './autorites.php?categ=auteurs&sub=reach&id=&type_autorite='+this.value\"";
			$sel_autorite_auteur .= "<select class='saisie-30em' id='id_autorite' name='type_autorite' onchange=$url>";
			$sel_autorite_auteur .= "<option value ='7' $sel_tout>" .$msg["autorites_auteurs_all"] ."</option>";
			$sel_autorite_auteur .= "<option value='70'$sel_pp>$msg[203]</option>";
			$sel_autorite_auteur .= "<option value='71'$sel_coll>$msg[204]</option>";
			$sel_autorite_auteur .= "<option value='72'$sel_congres>" .$msg["congres_libelle"] ."</option>";
			$sel_autorite_auteur .= "</select>";
			
			$user_query = str_replace("<!-- sel_autorites -->", $sel_autorite_auteur, $user_query);
			
			$user_query = str_replace('!!user_query_title!!', $msg[357] ." : " .$libelleRech, $user_query);
			$user_query = str_replace('!!action!!', './autorites.php?categ=auteurs&sub=reach&id=', $user_query);
			$user_query = str_replace('!!add_auth_msg!!', $libelleBtn, $user_query);
			$user_query = str_replace('!!add_auth_act!!', './autorites.php?categ=auteurs&sub=author_form&type_autorite=' .$type_autorite, $user_query);
			$user_query = str_replace('<!-- lien_derniers -->', "<a href='./autorites.php?categ=auteurs&sub=author_last'>$msg[1310]</a>", $user_query);
			$user_query = str_replace("!!user_input!!", htmlentities(stripslashes($user_input), ENT_QUOTES, $charset), $user_query);
			
			print pmb_bidi($user_query);
		}
		// ---------------------------------------------------------------
		// update_index($id) : maj des n-uplets la table notice_global_index en rapport avec cet author
		// ---------------------------------------------------------------
		static function update_index($id) {
			global $dbh;
			// On cherche tous les n-uplet de la table notice correspondant à cet auteur.
			$found = pmb_mysql_query("select distinct responsability_notice from responsability where responsability_author='" .$id ."'", $dbh);
			// Pour chaque n-uplet trouvés on met a jour la table notice_global_index avec l'auteur modifié :
			while ( ($mesNotices = pmb_mysql_fetch_object($found)) ) {
				$notice_id = $mesNotices->responsability_notice;
				notice::majNoticesGlobalIndex($notice_id);
				notice::majNoticesMotsGlobalIndex($notice_id, 'author');
			}
			// On met à jour les titres uniformes correspondant à cet auteur
			$found = pmb_mysql_query("select distinct responsability_tu_num from responsability_tu where responsability_tu_author_num='" .$id ."'", $dbh);
			// Pour chaque n-uplet trouvés on met a jour l'index du titre uniforme avec l'auteur modifié :
			while ( ($mesTu = pmb_mysql_fetch_object($found)) ) {
				titre_uniforme::update_index_tu($mesTu->responsability_tu_num);
				titre_uniforme::update_index($mesTu->responsability_tu_num);
			}
		}
		static function get_informations_from_unimarc($fields, $zone, $type, $field = "") {
			$data = array();
			// zone 200
			if ($zone =="2") {
				switch ($type) {
					case 70 :
						if (! $field)
							$field = $zone ."00";
						$data['type'] = 70;
						$data['name'] = $fields[$field][0]['a'][0];
						$data['rejete'] = $fields[$field][0]['b'][0];
						$data['date'] = $fields[$field][0]['f'][0];
						$data['subdivision'] = "";
						$data['lieu'] = "";
						$data['ville'] = "";
						$data['pays'] = "";
						$data['numero'] = "";
						break;
					case 71 :
						if (! $field)
							$field = $zone ."10";
						if (substr($fields[$field][0]['IND'], 0, 1) ==1) {
							$data['type'] = 72;
						} else {
							$data['type'] = 71;
						}
						$data['name'] = $fields[$field][0]['a'][0] .((count($fields[$field][0]['c']) !=0) ? " (" .implode(", ", $fields[$field][0]['c']) .")" : "");
						$data['rejete'] = $fields[$field][0]['g'][0];
						$data['date'] = $fields[$field][0]['f'][0];
						if (count($fields[$field][0]['b'])) {
							$data['subdivision'] = implode(". ", $fields[$field][0]['b']);
						} else {
							$data['subdivision'] = "";
						}
						$data['lieu'] = $fields[$field][0]['e'][0];
						$data['ville'] = "";
						$data['pays'] = "";
						$data['numero'] = $fields[$field][0]['d'][0];
						break;
				}
				$data['author_comment'] = "";
				for($i = 0; $i <count($fields['300']); $i ++) {
					for($j = 0; $j <count($fields['300'][$i]['a']); $j ++) {
						if ($data['author_comment'] !="")
							$data['author_comment'] .= "\n";
						$data['author_comment'] .= $fields['300'][$i]['a'][$j];
					}
				}
				$data['author_web'] = $fields['856'][0]['u'][0];
			} else {
				// zone 400 / 500 / 700
				$data['authority_number'] = $fields['3'][0];
				switch ($type) {
					case 70 :
						$data['type'] = 70;
						$data['name'] = $fields['a'][0];
						$data['rejete'] = $fields['b'][0];
						$data['date'] = $fields['f'][0];
						$data['subdivision'] = "";
						$data['lieu'] = "";
						$data['ville'] = "";
						$data['pays'] = "";
						$data['numero'] = "";
						break;
					case 71 :
						if (substr($fields['IND'], 0, 1) ==1) {
							$data['type'] = 72;
						} else {
							$data['type'] = 71;
						}
						$data['name'] = $fields['a'][0] .((count($fields['c']) !=0) ? " (" .implode(", ", $fields['c']) .")" : "");
						$data['rejete'] = $fields['g'][0];
						$data['date'] = $fields['f'][0];
						if (count($fields['b'])) {
							$data['subdivision'] = implode(". ", $fields['b']);
						} else {
							$data['subdivision'] = "";
						}
						$data['lieu'] = $fields['e'][0];
						$data['ville'] = "";
						$data['pays'] = "";
						$data['numero'] = $fields['d'][0];
						break;
				}
			}
			$data['type_authority'] = "author";
			return $data;
		}
		static function check_if_exists($data) {
			global $dbh;
			if (! sizeof($data) ||! is_array($data)) {
				// si ce n'est pas un tableau ou un tableau vide, on retourne 0
				return 0;
			}
			// check sur les éléments du tableau (data['name'] ou data['rejete'] est requis).
			$long_maxi_name = pmb_mysql_field_len(pmb_mysql_query("SELECT author_name FROM authors limit 1"), 0);
			$long_maxi_rejete = pmb_mysql_field_len(pmb_mysql_query("SELECT author_rejete FROM authors limit 1"), 0);
			
			$data['name'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))), 0, $long_maxi_name));
			$data['rejete'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['rejete']))), 0, $long_maxi_rejete));
			
			if (! $data['name'] &&! $data['rejete'])
				return 0;
				
				// check sur le type d'autorité
			if (! $data['type'] ==70 &&! $data['type'] ==71 &&! $data['type'] ==72)
				return 0;
				
				// tentative de récupérer l'id associée dans la base (implique que l'autorité existe)
				
			// préparation de la requête
			$key0 = $data['type'];
			$key1 = addslashes($data['name']);
			$key2 = addslashes($data['rejete']);
			$key3 = addslashes($data['date']);
			$key4 = addslashes($data['subdivision']);
			$key5 = addslashes($data['lieu']);
			$key6 = addslashes($data['ville']);
			$key7 = addslashes($data['pays']);
			$key8 = addslashes($data['numero']);
			
			$data['lieu'] = addslashes($data['lieu']);
			$data['ville'] = addslashes($data['ville']);
			$data['pays'] = addslashes($data['pays']);
			$data['subdivision'] = addslashes($data['subdivision']);
			$data['numero'] = addslashes($data['numero']);
			$data['author_comment'] = addslashes($data['author_comment']);
			$data['web'] = addslashes($data['web']);
			
			$query = "SELECT author_id FROM authors WHERE author_type='${key0}' AND author_name='${key1}' AND author_rejete='${key2}' AND author_date='${key3}'";
			if ($data["type"] >70) {
				$query .= " and author_subdivision='${key4}' and author_lieu='${key5}' and author_ville='${key6}' and author_pays='${key7}' and author_numero='${key8}'";
			}
			$query .= " LIMIT 1";
			$result = pmb_mysql_query($query, $dbh);
			if (! $result)
				die("can't SELECT in database");
				// résultat
				
			// récupération du résultat de la recherche
			$aut = pmb_mysql_fetch_object($result);
			// du résultat et récupération éventuelle de l'id
			if ($aut->author_id)
				return $aut->author_id;
			else
				return 0;
		}
		function get_id_bnf($id) {
			// autre moyen de récuperer authority_number?
			global $dbh;
			// ---------------------------------------------------------------
			// verification de l'id bnf dans la base
			// ---------------------------------------------------------------
			
			$id_bnf = "";
			$query = "SELECT authority_number from authorities_sources WHERE num_authority='$id' ";
			$result = @pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				$id_bnf = pmb_mysql_result($result, 0, 0);
			}
			return $id_bnf;
		}
		static function delete_enrichment($id) {
			// to Do
		}
		static function author_enrichment($id) {
			global $opac_enrichment_bnf_sparql;
			global $lang;
			global $charset;
			
			if ($opac_enrichment_bnf_sparql) {
				
				// definition des endpoints databnf et dbpedia
				$configbnf = array(
						'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
				);
				$storebnf = ARC2::getRemoteStore($configbnf);
				$configdbp = array(
						'remote_store_endpoint' => 'http://dbpedia.org/sparql'
				);
				$storedbp = ARC2::getRemoteStore($configdbp);
				// verifier la date de author_enrichment_last_update => if(self)
				$aut_id_bnf = self::get_id_bnf($id);
				
				// si l'auteur est dans la base on récupère son uri bnf...
				if ($aut_id_bnf !="") {
					
					$sparql = "
						PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
						PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
						PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
						SELECT distinct ?author WHERE {
						?author rdf:type skos:Concept .
						?author bnf-onto:FRBNF $aut_id_bnf
					}";
					
					$rows = $storebnf->query($sparql, 'rows');
					// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
					$err = $storebnf->getErrors();
					if ($err) {
						return;
					}
				}
				
				// definition de l'uri bnf
				if ($rows[0]["author"]) {
					$uri_bnf = $rows[0]["author"];
					$enrichment['links']['uri_bnf'] = $uri_bnf;
					// ... ainsi que son uri dbpedia si elle existe
					$sparql = "
						PREFIX rdagroup2elements: <http://rdvocab.info/ElementsGr2/>
						PREFIX owl:<http://www.w3.org/2002/07/owl#>
						PREFIX foaf: <http://xmlns.com/foaf/0.1/>
						SELECT  ?dbpedia WHERE{
						<$uri_bnf> foaf:focus ?author.
						OPTIONAL {?author owl:sameAs ?dbpedia.
							FILTER regex(str(?dbpedia), 'http://dbpedia', 'i')}.
					}";
					try {
						$rows = $storebnf->query($sparql, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					
					if ($rows[0]["dbpedia"]) {
						$sub_dbp_uri = substr($rows[0]["dbpedia"], 28);
						$uri_dbpedia = "http://dbpedia.org/resource/" .rawurlencode($sub_dbp_uri);
						$enrichment['links']['uri_dbpedia'] = $uri_dbpedia;
					}
				}
				
				// debut de la requete d'enrichissement
				if ($uri_bnf !="") {
					// recuperation des infos biographiques bnf
					$sparql = "
						PREFIX foaf: <http://xmlns.com/foaf/0.1/>
						PREFIX rdagroup2elements: <http://rdvocab.info/ElementsGr2/>
						PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
						PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
						PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
						SELECT * WHERE {
							<$uri_bnf> foaf:focus ?person .
							<$uri_bnf> skos:prefLabel ?isbd .
							?person foaf:page ?page .
							OPTIONAL {
								?person rdagroup2elements:biographicalInformation ?biography
							}.
							OPTIONAL {
								?person rdagroup2elements:dateOfBirth ?birthdate.
								?birthdate rdfs:label ?birth.
							}
							OPTIONAL {?person bnf-onto:firstYear ?birthfirst.}
							OPTIONAL {?person rdagroup2elements:placeOfBirth ?birthplace .}
							OPTIONAL {
								?person rdagroup2elements:dateOfDeath ?deathdate .
								?deathdate rdfs:label ?death.
							}
							OPTIONAL {?person rdagroup2elements:placeOfDeath ?deathplace .}
						}";
					try {
						$rows = $storebnf->query($sparql, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					if ($rows[0]['birth'])
						$birthdate = $rows[0]['birth'];
					else {
						if ($rows[0]['birthfirst'])
							$birthdate = $rows[0]['birthfirst'];
						else
							$birthdate = "";
					}
					
					$enrichment['bio'] = array(
							'isbd' => $rows[0]['isbd'],
							'biography_bnf' => $rows[0]['biography'],
							'birthdate' => $birthdate,
							'birthplace' => $rows[0]['birthplace'],
							'deathdate' => $rows[0]['death'],
							'deathplace' => $rows[0]['deathplace']
					);
					// fin bio bnf
					
					// vignettes bnf
					$sparql = "
							PREFIX foaf: <http://xmlns.com/foaf/0.1/>
							PREFIX dc: <http://purl.org/dc/elements/1.1/>
							PREFIX dcterm: <http://purl.org/dc/terms/>
							SELECT * WHERE {
								<$uri_bnf> foaf:focus ?person .
								?person foaf:depiction ?url .
							}";
					try {
						$rows = $storebnf->query($sparql, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					
					foreach ( $rows as $row ) {
						$depictions[] = $row['url'];
					}
					$enrichment['depictions']['depictions_bnf'] = $depictions;
					
					// biblio bnf
					$sparql = "
							PREFIX foaf: <http://xmlns.com/foaf/0.1/>
							PREFIX dcterms: <http://purl.org/dc/terms/>
							PREFIX rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
							SELECT ?work ?date ?dates ?work_concept  ?title MIN(?minUrl) AS ?url MIN(?minGallica) AS ?gallica WHERE {
								<$uri_bnf> foaf:focus ?person .
								?work dcterms:creator ?person .
								OPTIONAL { ?work dcterms:date ?date } .
								OPTIONAL { ?work <http://rdvocab.info/Elements/dateOfWork> ?dates } .
								?work_concept foaf:focus ?work .
								?work dcterms:title ?title .
								OPTIONAL{?work foaf:depiction ?minUrl .}
								OPTIONAL{
									?manifestation rdarelationships:workManifested ?work .
									?manifestation rdarelationships:electronicReproduction ?minGallica .
								}
							}  order by ?dates";
					
					try {
						$rows = $storebnf->query($sparql, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					if ($rows[0]['work']) {
						foreach ( $rows as $row ) {
							
							$tab_isbn = array();
							$sparql ="
								PREFIX rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
								PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
								SELECT distinct ?isbn WHERE {
									?manifestation rdarelationships:workManifested <".$row['work'].">.
									?manifestation bnf-onto:isbn ?isbn
								}order by ?isbn";
							try {
								$isbns = $storebnf->query ( $sparql, 'rows' );
							} catch ( Exception $e ) {
								$isbns = array ();
							}		
							foreach ($isbns as $isbn){
								$isbn['isbn']=formatISBN($isbn['isbn']);
								$tab_isbn[] = "'".$isbn['isbn']."'";
							}
							$aut_works[] = array(
									'title' => $row['title'],
									'uri_work' => $row['work'],
									'date' => $row['date'],
									'work_concept' => $row['work_concept'],
									'url' => $row['url'],
									'gallica' => $row['gallica'],
									'tab_isbn' => $tab_isbn
							);
						
						}
						$enrichment['biblio'] = $aut_works;
					}
				}
				
				// si uri dbpedia on recherche la bio dbpedia et l'image
				if ($uri_dbpedia !="") {
					$langue = substr($lang, 0, 2);
					$sparqldbp = "
						PREFIX dbpedia-owl:<http://dbpedia.org/ontology/>
						SELECT  ?comment ?image WHERE{
							<$uri_dbpedia> dbpedia-owl:abstract ?comment FILTER langMatches( lang(?comment), '" .$langue ."' ).
							OPTIONAL {<$uri_dbpedia> dbpedia-owl:thumbnail ?image} .
						}";
					try {
						$rows = $storedbp->query($sparqldbp, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					

					$enrichment['bio']['biography_dbpedia'] = encoding_normalize::clean_cp1252($rows[0]['comment'], "utf-8");
					if ($rows[0]['image'])
						$enrichment['depictions']['depiction_dbpedia'] = $rows[0]['image'];
						
						// recherche du mouvement litteraire ...
					$sparqldbp = "
						PREFIX dbpedia-owl:<http://dbpedia.org/ontology/>
						PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
						SELECT ?movement ?mov WHERE{
							<$uri_dbpedia> dbpedia-owl:movement ?mov.
							?mov rdfs:label ?movement
								FILTER langMatches( lang(?movement), '$langue').
						}";
					try {
						$rows = $storedbp->query($sparqldbp, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					
					foreach ( $rows as $row ) {
						$movement = array();
						$list_aut = array();
						$movement['title'] = $row['movement'];
						
						$sparqldbp = "
							PREFIX dbpedia-owl:<http://dbpedia.org/ontology/>
							PREFIX foaf: <http://xmlns.com/foaf/0.1/>
							SELECT distinct ?auts WHERE{
								?auts ?p <" .$row['mov'] .">.
									FILTER( ?p = dbpedia-owl:genre || ?p = dbpedia-owl:movement)
								?auts rdf:type foaf:Person
							}";
						try {
							$rows = $storedbp->query($sparqldbp, 'rows');
						} catch ( Exception $e ) {
							$rows = array();
						}
						
						foreach ( $rows as $row ) {
							if ($row['auts'] !=$uri_dbpedia) {
								$list_aut[] = rawurldecode($row['auts']);
							}
						}
						$list_aut = array_unique($list_aut);
						foreach ( array_chunk($list_aut, 10) as $chunk ) {
							
							$sparql = "
									PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
									PREFIX foaf: <http://xmlns.com/foaf/0.1/>
									PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
									PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
									SELECT ?numaut ?name WHERE{
										?aut rdf:type foaf:Person .
							    		?author foaf:focus ?aut.
							    		?author skos:exactMatch ?uri_dbpedia.
											FILTER (?uri_dbpedia = <" .implode("> || ?uri_dbpedia = <", $chunk) .">) 
										?author bnf-onto:FRBNF ?numaut.
										?aut foaf:name ?name.
									}";
							
							try {
								$rows = $storebnf->query($sparql, 'rows');
							} catch ( Exception $e ) {
								$rows = array();
							}
							
							foreach ( $rows as $row ) {
								
								$aauthor = Array(
										"id_bnf" => $row['numaut'],
										"name" => $row['name']
								);
								
								$movement['authors'][] = $aauthor;
							}
						}
						$enrichment['movement'][] = $movement;
					}
					// ... et du genre
					$sparqldbp = "
						PREFIX dbpedia-owl:<http://dbpedia.org/ontology/>
						PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
						SELECT ?genre ?mov WHERE{
							<$uri_dbpedia> dbpedia-owl:genre ?mov.
							?mov rdfs:label ?genre
								FILTER langMatches( lang(?genre), '$langue').
						}";
					try {
						$rows = $storedbp->query($sparqldbp, 'rows');
					} catch ( Exception $e ) {
						$rows = array();
					}
					
					foreach ( $rows as $row ) {
						$genre = array();
						$list_aut = array();
						$genre['title'] = $row['genre'];
						
						$sparqldbp = "
							PREFIX dbpedia-owl:<http://dbpedia.org/ontology/>
							PREFIX foaf: <http://xmlns.com/foaf/0.1/>
							SELECT distinct ?auts WHERE{
								?auts ?p <" .$row['mov'] .">.
									FILTER( ?p = dbpedia-owl:genre || ?p = dbpedia-owl:genre)
								?auts rdf:type foaf:Person
							}";
						try {
							$rows = $storedbp->query($sparqldbp, 'rows');
						} catch ( Exception $e ) {
							$rows = array();
						}
						
						foreach ( $rows as $row ) {
							if ($row['auts'] !=$uri_dbpedia) {
								$list_aut[] = rawurldecode($row['auts']);
							}
						}
						$list_aut = array_unique($list_aut);
						foreach ( array_chunk($list_aut, 10) as $chunk ) {
							
							$sparql = "
									PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
									PREFIX foaf: <http://xmlns.com/foaf/0.1/>
									PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
									PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
									SELECT ?numaut ?name WHERE{
										?aut rdf:type foaf:Person .
							    		?author foaf:focus ?aut.
							    		?author skos:exactMatch ?uri_dbpedia.
											FILTER (?uri_dbpedia = <" .implode("> || ?uri_dbpedia = <", $chunk) .">) 
										?author bnf-onto:FRBNF ?numaut.
										?aut foaf:name ?name.
									}";
							
							try {
								$rows = $storebnf->query($sparql, 'rows');
							} catch ( Exception $e ) {
								$rows = array();
							}
							
							foreach ( $rows as $row ) {
								
								$aauthor = Array(
										"id_bnf" => $row['numaut'],
										"name" => $row['name']
								);
								
								$genre['authors'][] = $aauthor;
							}
						}
						$enrichment['genre'][] = $genre;
					}
				}
				
				if ($charset !='utf-8'){
					$enrichment = pmb_utf8_array_decode($enrichment);
					
					
				}
				$enrichments = serialize($enrichment);
				$enrichments = addslashes($enrichments);
				
				$query = "UPDATE authors SET author_enrichment = '" .$enrichments ."', author_enrichment_last_update = NOW() WHERE author_id='" .$id ."'";
				$result = @pmb_mysql_query($query, $dbh);
				// update
			}
		}
	} // class auteur
}

