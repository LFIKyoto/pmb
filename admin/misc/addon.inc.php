<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.5.4.18 2015-12-10 15:20:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function traite_rqt($requete="", $message="") {

	global $dbh,$charset;
	$retour="";
	if($charset == "utf-8"){
		$requete=utf8_encode($requete);
	}
	$res = pmb_mysql_query($requete, $dbh) ;
	$erreur_no = pmb_mysql_errno();
	if (!$erreur_no) {
		$retour = "Successful";
	} else {
		switch ($erreur_no) {
			case "1060":
				$retour = "Field already exists, no problem.";
				break;
			case "1061":
				$retour = "Key already exists, no problem.";
				break;
			case "1091":
				$retour = "Object already deleted, no problem.";
				break;
			default:
				$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
				break;
			}
	}
	return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
}
echo "<table>";

/******************** AJOUTER ICI LES MODIFICATIONS *******************************/

switch ($pmb_bdd_subversion) {
	case '0' :
		// DB - Modification de la table resarc (id resa_planning pour resa issue d'une prévision)
		$rqt = "alter table resa_archive add resarc_resa_planning_id_resa int(8) unsigned not null default 0";
		echo traite_rqt($rqt,"alter resa_archive add resarc_resa_planning_id_resa");
	case '1' :
		//DG - Champs perso demandes
		$rqt = "create table if not exists demandes_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table if not exists demandes_custom ");

		$rqt = "create table if not exists demandes_custom_lists (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_list_value varchar(255) default NULL,
				demandes_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_champ_list_value (demandes_custom_champ,demandes_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists demandes_custom_lists ");

		$rqt = "create table if not exists demandes_custom_values (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_origine int(10) unsigned NOT NULL default 0,
				demandes_custom_small_text varchar(255) default NULL,
				demandes_custom_text text,
				demandes_custom_integer int(11) default NULL,
				demandes_custom_date date default NULL,
				demandes_custom_float float default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_custom_origine (demandes_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists demandes_custom_values ");

	case '2' :
		// NG - Circulation simplifiée de périodique
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_simple int unsigned not null default 0" ;
		echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_simple ");

		// NG - Script de construction d'étiquette de circulation simplifiée de périodique
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_simple_print_script' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'serialcirc_simple_print_script', '', 'Script de construction d\'étiquette de circulation simplifiée de périodique' ,'',0)";
			echo traite_rqt($rqt,"insert pmb_serialcirc_simple_print_script into parametres");
		}

	case '3' :
		// AP - Nombre maximum de notices à afficher dans une liste sans pagination
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='max_results_on_a_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'max_results_on_a_page', '500', 'Nombre maximum de notices à afficher sur une page, utile notamment quand la navigation est désactivée' ,'d_aff_recherche',0)";
			echo traite_rqt($rqt,"insert max_results_on_a_page into parametres");
		}
	case '4' :
		//JP - taille de certains champs blob trop juste
		$rqt = "ALTER TABLE opac_sessions CHANGE session session MEDIUMBLOB NULL DEFAULT NULL";
		echo traite_rqt($rqt,"ALTER TABLE opac_sessions CHANGE session MEDIUMBLOB");
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
	case '5' :
		//JP - bouton vider le cache portail
		$rqt = "ALTER TABLE cms_articles ADD article_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE cms_articles ADD article_update_timestamp");
		$rqt = "UPDATE cms_articles SET article_update_timestamp=article_creation_date";
		echo traite_rqt($rqt,"UPDATE cms_articles SET article_update_timestamp");
		$rqt = "ALTER TABLE cms_sections ADD section_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE cms_sections ADD section_update_timestamp");
		$rqt = "UPDATE cms_sections SET section_update_timestamp=section_creation_date";
		echo traite_rqt($rqt,"UPDATE cms_sections SET section_update_timestamp");
	case '6' :
		//JP - choix notice nouveauté oui/non par utilisateur en création de notice
		$rqt = "ALTER TABLE users ADD deflt_notice_is_new INT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_is_new");
	case '7' :
		// JP - paramètre mail_adresse_from pour l'envoi de mails
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_adresse_from' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'',0)";
			echo traite_rqt($rqt,"insert pmb_mail_adresse_from into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='mail_adresse_from' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_mail_adresse_from into parametres");
		}
	case '8' :
		// JP - blocage des prolongations autorisées si relance sur le prêt
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pret_prolongation_blocage' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'pret_prolongation_blocage', '0', 'Bloquer la prolongation s\'il y a un niveau de relance validé sur le prêt ?\n0 : Non 1 : Oui' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_pret_prolongation_blocage into parametres");
		}
	case '9' :
		// JP - Export tableur des prêts dans le compte emprunteur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_export_loans' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'empr_export_loans', '0', 'Afficher sur le compte emprunteur un bouton permettant d\'exporter les prêts dans un tableur ?\n0 : Non 1 : Oui' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_empr_export_loans into parametres");
		}
	case '10' :
		//Alexandre - Ajout des modes d'affichage avec sélection par étoiles
		$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'pmb' AND sstype_param='avis_note_display_mode'";
		echo traite_rqt($rqt,"UPDATE pmb_avis_note_display_mode into parametres");
		$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'opac' AND sstype_param='avis_note_display_mode'";
		echo traite_rqt($rqt,"UPDATE opac_avis_note_display_mode into parametres");
	case '11' :
		//JP - paramètre utilisateur : localisation par défaut en bulletinage
		// deflt_bulletinage_location : Identifiant de la localisation par défaut en bulletinage
		$rqt = "ALTER TABLE users ADD deflt_bulletinage_location INT( 6 ) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_collstate_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_bulletinage_location");
		$rqt = "UPDATE users SET deflt_bulletinage_location=deflt_docs_location";
		echo traite_rqt($rqt,"UPDATE users SET deflt_bulletinage_location=deflt_docs_location");
	case '12' :
		//MB - last_sync_date : Date de la dernière synchronisation du connecteur
		$rqt = "ALTER TABLE connectors_sources ADD last_sync_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD last_sync_date");
	case '13' :
		//JP - audit sur le contenu éditorial
		$res=pmb_mysql_query("SELECT id_section, section_creation_date, section_update_timestamp FROM cms_sections");
		if($res && pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)){
				$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=1, quand='".$r->section_creation_date." 00:00:00', info='' ";
				pmb_mysql_query($rqt);
				if ($r->section_update_timestamp != $r->section_creation_date.' 00:00:00') {
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=2, quand='".$r->section_update_timestamp."', info='' ";
					pmb_mysql_query($rqt);
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"INSERT editorial_sections INTO audit ");
		}
		$res=pmb_mysql_query("SELECT id_article, article_creation_date, article_update_timestamp FROM cms_articles");
		if($res && pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)){
				$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=1, quand='".$r->article_creation_date." 00:00:00', info='' ";
				pmb_mysql_query($rqt);
				if ($r->article_update_timestamp != $r->article_creation_date.' 00:00:00') {
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=2, quand='".$r->article_update_timestamp."', info='' ";
					pmb_mysql_query($rqt);
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"INSERT editorial_articles INTO audit ");
		}
	case '14' :
		//DG - Paramètre pour afficher ou non le bandeau d'acceptation des cookies
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent', '1', 'Afficher le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui','a_general',0)";
			echo traite_rqt($rqt,"insert opac_cookies_consent into parametres");
		}
	case '15' :
		//DG - Entrepôt par défaut en suppression de notices d'un panier
		$rqt = "ALTER TABLE users ADD deflt_agnostic_warehouse INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
		echo traite_rqt($rqt,"ALTER users ADD deflt_agnostic_warehouse");

}

/******************** JUSQU'ICI **************************************************/
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";