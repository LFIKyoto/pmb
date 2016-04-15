<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_v5.inc.php,v 1.583.2.5 2015-11-13 09:51:43 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

settype ($action,"string");

pmb_mysql_query("set names latin1 ", $dbh);

switch ($action) {
	case "lancement":
		switch ($version_pmb_bdd) {
			case "v4.94":
			case "v4.95":
			case "v4.96":
			case "v4.97":
				$maj_a_faire = "v5.00";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.00":
				$maj_a_faire = "v5.01";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.01":
				$maj_a_faire = "v5.02";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.02":
				$maj_a_faire = "v5.03";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.03":
			case "v5.04":
				$maj_a_faire = "v5.05";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.05":
				$maj_a_faire = "v5.06";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.06":
				$maj_a_faire = "v5.07";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.07":
				$maj_a_faire = "v5.08";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.08":
				$maj_a_faire = "v5.09";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.09":
				$maj_a_faire = "v5.10";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.10":
				$maj_a_faire = "v5.11";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.11":
				$maj_a_faire = "v5.12";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.12":
				$maj_a_faire = "v5.13";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.13":
				$maj_a_faire = "v5.14";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.14":
				$maj_a_faire = "v5.15";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.15":
				$maj_a_faire = "v5.16";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.16":
				$maj_a_faire = "v5.17";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.17":
				$maj_a_faire = "v5.18";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.18":
				$maj_a_faire = "v5.19";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.19":
				echo "<strong><font color='#FF0000'>".$msg[1805].$version_pmb_bdd." !</font></strong><br />";
				break;

			default:
				echo "<strong><font color='#FF0000'>".$msg[1806].$version_pmb_bdd." !</font></strong><br />";
				break;
			}
		break;

	case "v5.00":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='opac_view_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activé \n 1 : activé', '', '0')";
			echo traite_rqt($rqt,"insert pmb_opac_view_activate='0' into parametres ");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='opac_view_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activé \n 1 : activé', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_opac_view_activate='0' into parametres ");
		}

		//Gestion des vues Opac
		$rqt = "CREATE TABLE if not exists opac_views (
			opac_view_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			opac_view_name VARCHAR( 255 ) NOT NULL default '',
			opac_view_query TEXT NOT NULL,
			opac_view_human_query TEXT NOT NULL,
			opac_view_param TEXT NOT NULL,
			opac_view_visible INT( 1 ) UNSIGNED NOT NULL default 0,
			opac_view_comment TEXT NOT NULL)";
		echo traite_rqt($rqt,"CREATE TABLE opac_views ") ;

		//Gestion des filtres de module ( pour vues Opac )
		$rqt = "CREATE TABLE if not exists opac_filters (
			opac_filter_view_num INT UNSIGNED NOT NULL default 0 ,
			opac_filter_path VARCHAR( 20 ) NOT NULL default '',
			opac_filter_param TEXT NOT NULL,
			PRIMARY KEY(opac_filter_view_num,opac_filter_path))";
		echo traite_rqt($rqt,"CREATE TABLE opac_filters ") ;

		//Gestion générique des subst de parametre ( pour vues Opac )
		$rqt = "CREATE TABLE if not exists param_subst (
			subst_module_param VARCHAR( 20 ) NOT NULL default '',
			subst_module_num INT( 2 ) UNSIGNED NOT NULL default 0,
			subst_type_param VARCHAR( 20 ) NOT NULL default '',
			subst_sstype_param VARCHAR( 255 ) NOT NULL default '',
			subst_valeur_param TEXT NOT NULL,
			subst_comment_param longtext NOT NULL,
			PRIMARY KEY(subst_module_param, subst_module_num, subst_type_param, subst_sstype_param))";
		echo traite_rqt($rqt,"CREATE TABLE param_subst ") ;

		$rqt = "CREATE TABLE if not exists opac_views_empr (
			emprview_view_num INT UNSIGNED NOT NULL default 0 ,
			emprview_empr_num INT UNSIGNED NOT NULL default 0 ,
		    emprview_default INT UNSIGNED NOT NULL default 0 ,
			PRIMARY KEY(emprview_view_num,emprview_empr_num))";
		echo traite_rqt($rqt,"CREATE TABLE opac_views_empr ") ;

		// Gestion des sur-localisations
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='sur_location_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'pmb', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', '', '0')";
			echo traite_rqt($rqt,"insert pmb_sur_location_activate='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='sur_location_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_sur_location_activate='0' into parametres ");
		}

		$rqt = "CREATE TABLE if not exists sur_location (
			surloc_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			surloc_libelle VARCHAR( 255 ) NOT NULL default '',
			surloc_pic VARCHAR( 255 ) NOT NULL default '',
			surloc_visible_opac tinyint( 1 ) UNSIGNED NOT NULL default 1,
			surloc_name VARCHAR( 255 ) NOT NULL default '',
			surloc_adr1 VARCHAR( 255 ) NOT NULL default '',
			surloc_adr2 VARCHAR( 255 ) NOT NULL default '',
			surloc_cp VARCHAR( 15 ) NOT NULL default '',
			surloc_town VARCHAR( 100 ) NOT NULL default '',
			surloc_state VARCHAR( 100 ) NOT NULL default '',
			surloc_country VARCHAR( 100 ) NOT NULL default '',
			surloc_phone VARCHAR( 100 ) NOT NULL default '',
			surloc_email VARCHAR( 100 ) NOT NULL default '',
			surloc_website VARCHAR( 100 ) NOT NULL default '',
			surloc_logo VARCHAR( 100 ) NOT NULL default '',
			surloc_comment TEXT NOT NULL,
			surloc_num_infopage INT( 6 ) UNSIGNED NOT NULL default 0,
			surloc_css_style VARCHAR( 100 ) NOT NULL default '')";
		echo traite_rqt($rqt,"CREATE TABLE sur_location ") ;

		$rqt = "ALTER TABLE docs_location ADD surloc_num INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table docs_location add surloc_num");

		$rqt = "ALTER TABLE docs_location ADD surloc_used tinyint( 1 ) NOT NULL default 0";
		echo traite_rqt($rqt,"alter table docs_location add surloc_used");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='opac_view_class' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'pmb', 'opac_view_class', '', 'Nom de la classe substituant la class opac_view pour la personnalisation de la gestion des vues Opac','')";
			echo traite_rqt($rqt,"insert pmb_opac_view_class='' into parametres");
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.01");
		break;

	case "v5.01":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		// Favicon, reporté de la 4.94 - ER
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='faviconurl' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'faviconurl', '', 'URL du favicon, si vide favicon=celui de PMB','a_general')";
			echo traite_rqt($rqt,"insert opac_faviconurl='' into parametres");
		}

		//on précise si une source est interrogée directement en ajax dans l'OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_affiliate_search INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table connectors_sources add opac_affiliate_search");

		// Activation des recherches affiliées dans les sources externes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_affiliate_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_affiliate_search', '0', 'Activer les recherches affiliées en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_affiliate_search='0' into parametres ");
		}

		$rqt = "ALTER TABLE users CHANGE explr_invisible explr_invisible TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_invisible explr_invisible TEXT NULL");
		$rqt = "ALTER TABLE users CHANGE explr_visible_mod explr_visible_mod TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_mod explr_visible_mod TEXT NULL");
		$rqt = "ALTER TABLE users CHANGE explr_visible_unmod explr_visible_unmod TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_unmod explr_visible_unmod TEXT NULL");

		//ajout table statuts de lignes d'actes
		$rqt = "CREATE TABLE lignes_actes_statuts (
			id_statut INT(3) NOT NULL AUTO_INCREMENT,
			libelle TEXT NOT NULL,
			relance INT(3) NOT NULL DEFAULT 0,
			PRIMARY KEY (id_statut)
			)  ";
		echo traite_rqt($rqt,"create table lignes_actes_statuts");

		$rqt = "CREATE TABLE lignes_actes_relances (
			num_ligne INT UNSIGNED NOT NULL ,
			date_relance DATE NOT NULL default '0000-00-00',
			type_ligne int(3) unsigned NOT NULL DEFAULT 0,
			num_acte int(8) unsigned NOT NULL DEFAULT 0,
			lig_ref int(15) unsigned NOT NULL DEFAULT 0,
			num_acquisition int(12) unsigned NOT NULL DEFAULT 0,
			num_rubrique int(8) unsigned NOT NULL DEFAULT 0,
			num_produit int(8) unsigned NOT NULL DEFAULT 0,
			num_type int(8) unsigned NOT NULL DEFAULT 0,
			libelle text NOT NULL,
			code varchar(255) NOT NULL DEFAULT '',
			prix float(8,2) NOT NULL DEFAULT 0,
			tva float(8,2) unsigned NOT NULL DEFAULT 0,
			nb int(5) unsigned NOT NULL DEFAULT 1,
			date_ech date NOT NULL DEFAULT '0000-00-00',
			date_cre date NOT NULL DEFAULT '0000-00-00',
			statut int(3) unsigned NOT NULL DEFAULT 1,
			remise float(8,2) NOT NULL DEFAULT 0,
			index_ligne text NOT NULL,
			ligne_ordre smallint(2) unsigned NOT NULL DEFAULT 0,
			debit_tva smallint(2) unsigned NOT NULL DEFAULT 0,
			commentaires_gestion text NOT NULL,
			commentaires_opac text NOT NULL,
			PRIMARY KEY (num_ligne, date_relance)
			) ";
		echo traite_rqt($rqt,"create table lignes_actes_relances");

		//ajout d'un statut de lignes d'actes par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from lignes_actes_statuts where id_statut='1' "))==0) {
			$rqt = "INSERT INTO lignes_actes_statuts (id_statut,libelle,relance) VALUES (1 ,'Traitement normal', '1') ";
			echo traite_rqt($rqt,"insert default lignes_actes_statuts");
		}

		//raz des statuts de lignes d'actes
		$rqt = "UPDATE lignes_actes set statut='1' ";
		echo traite_rqt($rqt,"alter lignes_actes raz statut");

		//ajout d'un statut de ligne d'acte par défaut par utilisateur pour les devis
		$rqt = "ALTER TABLE users ADD deflt3lgstatdev int(3) not null default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default lg state dev");

		//ajout d'un statut de ligne d'acte par défaut par utilisateur pour les commandes
		$rqt = "ALTER TABLE users ADD deflt3lgstatcde int(3) not null default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default lg state cde");

		//ajout d'un commentaire de gestion pour les lignes d'actes
		$rqt = "ALTER TABLE lignes_actes ADD commentaires_gestion TEXT NOT NULL";
		echo traite_rqt($rqt,"alter table lignes_actes add commentaires_gestion");

		//ajout d'un commentaire OPAC pour les lignes d'actes
		$rqt = "ALTER TABLE lignes_actes ADD commentaires_opac TEXT NOT NULL";
		echo traite_rqt($rqt,"alter table lignes_actes add commentaires_opac");

		//ajout d'un nom (pour les commandes)
		$rqt = "ALTER TABLE actes ADD nom_acte VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter table actes add nom_acte");

		//Paramètres de mise en page des relances d'acquisitions
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_format_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_format_page','210x297','Largeur x Hauteur de la page en mm','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_format_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_orient_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_orient_page','P','Orientation de la page: P=Portrait, L=Paysage','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_orient_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_marges_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_marges_page','10,20,10,10','Marges de page en mm : Haut,Bas,Droite,Gauche','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_marges_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_logo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_logo','10,10,20,20','Position du logo: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_logo into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_raison' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_raison','35,10,100,10,16','Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_raison into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_date' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_date','170,10,0,6,8','Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_date into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_adr_rel' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_adr_rel','10,35,60,5,10','Position Adresse de relance: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_adr_rel into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_adr_fou' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_adr_fou','100,55,100,6,14','Position Adresse fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_adr_fou into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_num_cli' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_num_cli','10,80,0,10,16','Position numéro de client: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_num_cli into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_num' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_num','10,0,10,16','Position numéro de commande/devis: Distance par rapport au bord gauche de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_num into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_size' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_size','10','Taille de la police texte','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_size into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_titre' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_titre','10,90,100,10,16','Position titre: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_titre into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_before' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_before','','Texte avant le tableau de relances','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_before into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_after' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_after','','Texte après le tableau de relances','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_after into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_tab_rel' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_tab_rel','5,10','Tableau de relances: Hauteur ligne,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_tab_rel into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_footer' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_footer','15,8','Position bas de page: Distance par rapport au bas de page, Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_footer into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_sign' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_sign','10,60,5,10','Position signature: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_sign into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_sign' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_sign','Le responsable de la bibliothèque.','Texte signature','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_sign into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_by_mail','1','Effectuer les relances par mail :\n 0 : non \n 1 : oui','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint un état des commandes en cours.\r\n\r\nMerci de nous préciser par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_mail into parametres") ;
		}

		//ajout bulletinage avec document numérique
		$rqt = "ALTER TABLE abts_abts ADD abt_numeric int(1) not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_numeric ");

		//ajout dans les bannettes la possibilité de ne pas tenir compte du statut des notices
		$rqt = "ALTER TABLE bannettes ADD statut_not_account INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add statut_not_account");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_perio_browser' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','show_perio_browser','0','Affichage du navigateur de périodiques en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_perio_browser into parametres") ;
		}

		// Gestion des relances des périodiques
		$rqt = "CREATE TABLE perio_relance (
			rel_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			rel_abt_num int(10) unsigned NOT NULL DEFAULT 0,
			rel_date_parution date NOT NULL default '0000-00-00',
			rel_libelle_numero varchar(255) default NULL,
			rel_comment_gestion TEXT NOT NULL,
			rel_comment_opac TEXT NOT NULL ,
			rel_nb int unsigned NOT NULL DEFAULT 0,
			rel_date date NOT NULL default '0000-00-00',
			PRIMARY KEY  (rel_id) ) ";
		echo traite_rqt($rqt,"create table perio_relance ");

		//relances d'acquisitions en pdf/rtf
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pdfrtf' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pdfrtf','0','Envoi des relances en :\n 0 : pdf\n 1 : rtf','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pdfrtf into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_onglet_perio_a2z' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','show_onglet_perio_a2z','0','Activer l\'onglet du navigateur de périodiques en OPAC.\n 0 : Non.\n 1 : Oui.','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_show_onglet_perio_a2z into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_note_display_mode into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_display_mode','0','Mode d\'affichage des avis de notices.\n 0 : Visible en lien à coté de l\'onglet Public/ISBD de la notice.\n 1 : Visible dans la notice.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_display_mode into parametres") ;
		}

		$rqt = "ALTER TABLE avis ADD avis_rank INT UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_rank") ;

		//Module Gestionnaire de tâches
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='planificateur_allow' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'planificateur_allow', '0', 'Planificateur activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_planificateur_allow=0 into parameters");
		}

		$rqt = "CREATE TABLE taches_type (
				id_type_tache int(11) unsigned NOT NULL,
				parameters text NOT NULL,
				timeout int(11) NOT NULL default '5',
				histo_day int(11) NOT NULL default '7',
				histo_number int(11) NOT NULL default '3',
				PRIMARY KEY  (id_type_tache)
				)";
		echo traite_rqt($rqt, "CREATE TABLE taches_type ");

		// Création des tables nécessaires au gestionnaire de tâches
		$rqt="CREATE TABLE taches (
			id_tache int(11) unsigned auto_increment,
			num_planificateur int(11),
			start_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			end_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			status varchar(128),
			msg_statut blob,
			commande int(8) NOT NULL default 0,
			next_state int(8) NOT NULL default 0,
			msg_commande blob,
			indicat_progress int(3),
			rapport text,
			id_process int(8),
			primary key (id_tache));";
		echo traite_rqt($rqt,"CREATE TABLE taches ");

		$rqt="CREATE TABLE planificateur (
			id_planificateur int(11) unsigned auto_increment,
			num_type_tache int(11) NOT NULL,
			libelle_tache VARCHAR(255) NOT NULL,
			desc_tache VARCHAR(255),
			num_user int(11) NOT NULL,
			param text,
			statut tinyint(1) unsigned DEFAULT 0,
			rep_upload int(8),
			path_upload text,
			perio_heure varchar(28),
			perio_minute varchar(28) DEFAULT '01',
			perio_jour varchar(128),
			perio_mois varchar(128),
			calc_next_heure_deb varchar(28),
			calc_next_date_deb date,
			primary key (id_planificateur))";
		echo traite_rqt($rqt,"CREATE TABLE planificateur ");

		$rqt="CREATE TABLE taches_docnum (
			id_tache_docnum int(11) unsigned auto_increment,
			tache_docnum_nomfichier varchar(255) NOT NULL,
			tache_docnum_mimetype VARCHAR(255) NOT NULL,
			tache_docnum_data mediumblob NOT NULL,
			tache_docnum_extfichier varchar(20),
			tache_docnum_repertoire int(8),
			tache_docnum_path text NOT NULL,
			num_tache int(11) NOT NULL,
			primary key (id_tache_docnum))";
		echo traite_rqt($rqt,"CREATE TABLE taches_docnum ");

		//modification de la longueur du champ numero de la table actes
		$rqt = "ALTER TABLE actes MODIFY numero varchar(255) NOT NULL default '' ";
		echo traite_rqt($rqt,"alter table actes modify numero");

		//ajout d'un statut par défaut en réception pour les suggestions
		$rqt = "ALTER TABLE users ADD deflt3receptsugstat int(3) not null default 32 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default recept sug state");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_obj_mail','Etat des en-cours','Objet du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_obj_mail into parametres") ;
		}

		//ajout de paramètres pour l'envoi de commandes par mail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_by_mail','1','Effectuer les envois de commandes par mail :\n 0 : non \n 1 : oui','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_obj_mail','Commande','Objet du mail','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_obj_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une commande à traiter.\r\n\r\nMerci de nous confirmer par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_text_mail into parametres") ;
		}

		//ajout de paramètres pour l'envoi de devis par mail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_by_mail','1','Effectuer les envois de demandes de devis par mail :\n 0 : non \n 1 : oui','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfdev_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_obj_mail','Demande de devis','Objet du mail','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfdev_obj_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une demande de devis.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcdev_text_mail into parametres") ;
		}

		// masquer la possibilité d'uploader les docnum en base
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='docnum_in_database_allow' "))==0){
			if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) $upd_param_docnum_in_database_allow = 1;
			else $upd_param_docnum_in_database_allow=0;
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'docnum_in_database_allow', '$upd_param_docnum_in_database_allow', 'Autoriser le stockage de document numérique en base ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_docnum_in_database_allow=$upd_param_docnum_in_database_allow into parameters <br><b>SET this parameter to 1 to (re)allow file storage in database !</b>");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_ajax_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'recherche_ajax_mode', '1', 'Affichage accéléré des résultats de recherche: header uniquement, la suite est chargée lors du click sur le \"+\".\n 0: Inactif\n 1: Actif (par lot)\n 2: Actif (par notice)', 'c_recherche', '0')" ;
			echo traite_rqt($rqt,"insert opac_recherche_ajax_mode=1 into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.','',0)" ;
			echo traite_rqt($rqt,"insert pmb_avis_note_display_mode into parametres") ;
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.02");
		break;

	case "v5.02":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//Module CMS
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active', '0', 'Module \'Portail\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert cms_active=0 into parameters");
		}

		//langue d'indexation par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_lang' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'indexation_lang', '', 'Choix de la langue d\'indexation par défaut. (ex : fr_FR,en_UK,...,ar), si vide c\'est la langue de l\'interface du catalogueur qui est utilisée.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_indexation_lang into parameters");
		}

		//ajout du champ permettant la pré-selection du connecteur en OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_selected int(3) unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD opac_selected");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_show_location' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'websubscribe_show_location', '0', 'Afficher la possibilité pour le lecteur de choisir sa localisation lors de son inscription en ligne.\n 0: Non\n 1: Oui', 'f_modules', '0')" ;
			echo traite_rqt($rqt,"insert opac_websubscribe_show_location=0 into parametres") ;
		}

		// CMS PMB
		//rubriques
		$rqt="create table if not exists cms_sections(
			id_section int unsigned not null auto_increment primary key,
			section_title varchar(255) not null default '',
			section_resume text not null,
			section_logo mediumblob not null,
			section_publication_state varchar(50) not null,
			section_start_date datetime,
			section_end_date datetime,
			section_num_parent int not null default 0,
			index i_cms_section_title(section_title),
			index i_cms_section_publication_state(section_publication_state),
			index i_cms_section_num_parent(section_num_parent)
			)";
		echo traite_rqt($rqt, "create table cms_sections");

		$rqt = "create table if not exists cms_sections_descriptors(
			num_section int not null default 0,
			num_noeud int not null default 0,
			section_descriptor_order int not null default 0,
			primary key (num_section,num_noeud)
			)";
		echo traite_rqt($rqt, "create table cms_sections_descriptors");

		$rqt="create table if not exists cms_articles(
			id_article int unsigned not null auto_increment primary key,
			article_title varchar(255) not null default '',
			article_resume text not null,
			article_contenu text not null,
			article_logo mediumblob not null,
			article_publication_state varchar(50) not null default '',
			article_start_date datetime,
			article_end_date datetime,
			num_section int not null default 0,
			index i_cms_article_title(article_title),
			index i_cms_article_publication_state(article_publication_state),
			index i_cms_article_num_parent(num_section)
			)";
		echo traite_rqt($rqt, "create table cms_articles");

		$rqt = "create table if not exists cms_articles_descriptors(
			num_article int not null default 0,
			num_noeud int not null default 0,
			article_descriptor_order int not null default 0,
			primary key (num_article,num_noeud)
			)";
		echo traite_rqt($rqt, "create table cms_articles_descriptors");


		$rqt = "create table if not exists cms_editorial_publications_states(
			id_publication_state int unsigned not null auto_increment primary key,
			editorial_publication_state_label varchar(255) not null default '',
			editorial_publication_state_opac_show int(1) not null default 0,
			editorial_publication_state_auth_opac_show int(1) not null default 0
			)";
		echo traite_rqt($rqt, "create table cms_editorial_publications_states");

		$rqt="create table if not exists cms_build (
			id_build int unsigned not null auto_increment primary key,
			build_obj varchar(255) not null default '',
			build_parent varchar(255) not null default '',
			build_child_after varchar(255) not null default '',
			build_css text not null
			)";
		echo traite_rqt($rqt, "create table cms_build");

		//paramétrage de la pondération des champs persos...
		// dans le notices
		$rqt = "alter table notices_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table notices_custom add pond");
		//dans les exemplaires
		$rqt = "alter table expl_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table expl_custom add pond ");
		//dans les états des collections
		$rqt = "alter table collstate_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table collstate_custom add pond");
		//dans les lecteurs, pour rester homogène...
		$rqt = "alter table empr_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table empr_custom add pond");

		//tri sur les états des collections en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'collstate_order', 'archempla_libelle,collstate_cote','Ordre d\'affichage des états des collections, dans l\'ordre donné, séparé par des virgules : archempla_libelle,collstate_cote','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_collstate_order=archempla_libelle,collstate_cote into parametres");
		}

		//la pondération dans les fiches ne sert à rien mais pour rester homogène avec les autres champs persos...
		$rqt = "alter table gestfic0_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table gestfic0_custom add pond");

		//AR new search !
		@set_time_limit(0);
		flush();
		$rqt = "truncate table notices_mots_global_index";
		echo traite_rqt($rqt,"truncate table notices_mots_global_index");

		//Changement du type de code_champ dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index change code_champ code_champ int(3) not null default 0";
		echo traite_rqt($rqt,"alter table notices_mots_global_index change code_champ");

		//ajout de code_ss_champ dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add code_ss_champ int(3) not null default 0 after code_champ";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add code_ss_champ");

		//ajout de pond dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add pond int(4) not null default 100";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add pond");

		//ajout de position dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add position int not null default 1";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add position");

		//ajout de lang dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add lang varchar(10) not null default ''";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add lang");

		//changement de clé primaire
		$rqt = "alter table notices_mots_global_index drop primary key, add primary key(id_notice,code_champ,code_ss_champ,mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index change primary key(id_notice,code_champ,code_ss_champ,mot");

		//index
		$rqt = "alter table notices_mots_global_index drop index i_mot";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_mot");
		$rqt = "alter table notices_mots_global_index add index i_mot(mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_mot");

		$rqt = "alter table notices_mots_global_index drop index i_id_mot";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_id_mot");
		$rqt = "alter table notices_mots_global_index add index i_id_mot(id_notice,mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_mot");

		//une nouvelle table pour les recherches exactes...
		$rqt="create table if not exists notices_fields_global_index (
			id_notice mediumint(8) not null default 0,
			code_champ int(3) not null default 0,
			code_ss_champ int(3) not null default 0,
			ordre int(4) not null default 0,
			value text not null,
			pond int(4) not null default 100,
			lang varchar(10) not null default '',
			primary key(id_notice,code_champ,code_ss_champ,ordre),
			index i_value(value(300)),
			index i_id_value(id_notice,value(300))
			)";
		echo traite_rqt($rqt, "create table notices_fields_global_index");

		$rqt = "create table if not exists search_cache (
			object_id varchar(255) not null default '',
			delete_on_date datetime not null default '0000-00-00 00:00:00',
			value mediumblob not null,
	 		PRIMARY KEY (object_id)
			)";
		echo traite_rqt($rqt, "create table search_cache");

		// ajout d'un paramètre de tri par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','default_sort','d_num_6,c_text_28','Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))','d_aff_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_default_sort into parametres") ;
		}
		flush();
		//AR /new search !

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//Précision affichage amendes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='fine_precision' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion) VALUES (0, 'pmb', 'fine_precision', '2', 'Nombre de décimales pour l\'affichage des amendes',1)";
			echo traite_rqt($rqt,"insert fine_precision=2 into parametres");
		}

		//Rafraichissement des vues opac
		$rqt = "alter table opac_views add opac_view_last_gen datetime default null";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_last_gen");
		$rqt = "alter table opac_views add opac_view_ttl int not null default 86400";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_ttl");

		// paramétrage du cache en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_cache_duration' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','search_cache_duration','600','Durée de validité (en secondes) du cache des recherches OPAC','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_cache_duration into parametres") ;
		}

		// ajout d'un paramètre utilisateur de statut par défaut en import (report de l'alter V4, modif tardive en 3.4)
		$rqt = "alter table users add deflt_integration_notice_statut int(6) not null default 1 after deflt_notice_statut";
		echo traite_rqt($rqt,"alter table users add deflt_integration_notice_statut");

		// Info de réindexation
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.03");
		break;

	case "v5.03":
	case "v5.04":
	case "v5.05":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//Type de document par défaut en création de périodique
		$rqt = "ALTER TABLE users ADD xmlta_doctype_serial varchar(2) NOT NULL DEFAULT '' after xmlta_doctype";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_serial after xmlta_doctype");

		//Type de document par défaut en création de bulletin
		$rqt = "ALTER TABLE users ADD xmlta_doctype_bulletin varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_bulletin after xmlta_doctype_serial");

		//Type de document par défaut en création d'article
		$rqt = "ALTER TABLE users ADD xmlta_doctype_analysis varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_analysis after xmlta_doctype_bulletin");

		// Mise à jour des valeurs en fonction du type de document par défaut en création de notice, si la valeur est vide !
		if ($res = pmb_mysql_query("select userid, xmlta_doctype,xmlta_doctype_serial,xmlta_doctype_bulletin,xmlta_doctype_analysis from users")){
			while ( $row = pmb_mysql_fetch_object($res)) {
				if ($row->xmlta_doctype_serial == '') pmb_mysql_query("update users set xmlta_doctype_serial='".$row->xmlta_doctype."' where userid=".$row->userid);
				if ($row->xmlta_doctype_bulletin == '') pmb_mysql_query("update users set xmlta_doctype_bulletin='".$row->xmlta_doctype."' where userid=".$row->userid);
				if ($row->xmlta_doctype_analysis == '') pmb_mysql_query("update users set xmlta_doctype_analysis='".$row->xmlta_doctype."' where userid=".$row->userid);
			}
		}

		// Ajout affichage a2z par localisation
		$rqt = "alter table docs_location add show_a2z int(1) unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE docs_location ADD show_a2z");

		// demande GM : index sur
		$rqt = "alter table pret drop index i_pret_arc_id";
		echo traite_rqt($rqt,"alter table pret drop index i_pret_arc_id");
		$rqt = "alter table pret add index i_pret_arc_id(pret_arc_id)";
		echo traite_rqt($rqt,"alter table pret add index i_pret_arc_id");

		$rqt = "CREATE TABLE if not exists facettes (
				id_facette int unsigned auto_increment,
				facette_name varchar(255) not null default '',
				facette_critere int(5) not null default 0,
				facette_ss_critere int(5) not null default 0,
				facette_nb_result int(2) not null default 0,
				facette_visible tinyint(1) not null default 0,
				facette_type_sort int(1) not null default 0,
				facette_order_sort int(1) not null default 0,
				primary key (id_facette))";
		echo traite_rqt($rqt,"CREATE TABLE facettes");

		// début circulation périodiques
		//ajout du champ expl_abt_num permettant de lier l'exemplaire a un abonnement de pério
		$rqt = "ALTER TABLE exemplaires ADD expl_abt_num int unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD expl_abt_num");

		$rqt="create table if not exists serialcirc (
			id_serialcirc int unsigned not null auto_increment primary key,
			num_serialcirc_abt int unsigned not null default 0,
			serialcirc_type int unsigned not null default 0,
			serialcirc_virtual int unsigned not null default 0,
			serialcirc_duration int unsigned not null default 0,
			serialcirc_checked int unsigned not null default 0,
			serialcirc_retard_mode int unsigned not null default 0,
			serialcirc_allow_resa int unsigned not null default 0,
			serialcirc_allow_copy int unsigned not null default 0,
			serialcirc_allow_send_ask int unsigned not null default 0,
			serialcirc_allow_subscription int unsigned not null default 0,
			serialcirc_duration_before_send int unsigned not null default 0,
			serialcirc_expl_statut_circ int unsigned not null default 0,
			serialcirc_expl_statut_circ_after int unsigned not null default 0,
			serialcirc_state int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc");

		$rqt="create table if not exists serialcirc_diff (
			id_serialcirc_diff int unsigned not null auto_increment primary key,
			num_serialcirc_diff_serialcirc int unsigned not null default 0,
			serialcirc_diff_empr_type int unsigned not null default 0,
			serialcirc_diff_type_diff int unsigned not null default 0,
			num_serialcirc_diff_empr int unsigned not null default 0,
			serialcirc_diff_group_name varchar(255) not null default '',
			serialcirc_diff_duration int unsigned not null default 0,
			serialcirc_diff_order int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc_diff");

		$rqt="create table if not exists serialcirc_group (
			id_serialcirc_group int unsigned not null auto_increment primary key,
			num_serialcirc_group_diff int unsigned not null default 0,
			num_serialcirc_group_empr int unsigned not null default 0,
			serialcirc_group_responsable int unsigned not null default 0,
			serialcirc_group_order int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc_group");

		$rqt="create table if not exists serialcirc_expl (
			id_serialcirc_expl int unsigned not null auto_increment primary key,
			num_serialcirc_expl_id int unsigned not null default 0,
			num_serialcirc_expl_serialcirc int unsigned not null default 0,
			serialcirc_expl_bulletine_date date NOT NULL default '0000-00-00',
			serialcirc_expl_state_circ int unsigned not null default 0,
			num_serialcirc_expl_serialcirc_diff int unsigned not null default 0,
			serialcirc_expl_ret_asked int unsigned not null default 0,
			serialcirc_expl_trans_asked int unsigned not null default 0,
			serialcirc_expl_trans_doc_asked int unsigned not null default 0,
			num_serialcirc_expl_current_empr int unsigned not null default 0,
			serialcirc_expl_start_date date NOT NULL default '0000-00-00'
		)";
		echo traite_rqt($rqt, "create table serialcirc_expl");

		$rqt="create table if not exists serialcirc_circ (
			id_serialcirc_circ int unsigned not null auto_increment primary key,
			num_serialcirc_circ_diff int unsigned not null default 0,
			num_serialcirc_circ_expl int unsigned not null default 0,
			num_serialcirc_circ_empr int unsigned not null default 0,
			num_serialcirc_circ_serialcirc int unsigned not null default 0,
            serialcirc_circ_order int unsigned not null default 0,
            serialcirc_circ_subscription int unsigned not null default 0,
            serialcirc_circ_ret_asked int unsigned not null default 0,
            serialcirc_circ_trans_asked int unsigned not null default 0,
            serialcirc_circ_trans_doc_asked int unsigned not null default 0,
			serialcirc_circ_expected_date datetime,
			serialcirc_circ_pointed_date datetime
		)";
		//,			primary key(id_serialcirc_circ, num_serialcirc_circ_diff,num_serialcirc_circ_expl,num_serialcirc_circ_empr,num_serialcirc_circ_serialcirc)
		echo traite_rqt($rqt,"create table serialcirc_circ");

		//path_pmb planificateur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='path_php' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'path_php', '', 'Chemin absolu de l\'interpréteur PHP, local ou distant', '',0) ";
			echo traite_rqt($rqt, "insert pmb_path_php into parameters");
		}

		//modification taille du champ expl_comment de la table exemplaires
		$rqt = "ALTER TABLE exemplaires MODIFY expl_comment TEXT ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_comment");

		//tri sur les documents numériques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='explnum_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id','Ordre d\'affichage des documents numériques, dans l\'ordre donné, séparé par des virgules : explnum_mimetype, explnum_nom, explnum_id','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_explnum_order=explnum_mimetype, explnum_nom, explnum_id into parametres");
		}

		//modification taille du champ resa_idempr de la table resa
		$rqt = "ALTER TABLE resa MODIFY resa_idempr int(10) unsigned NOT NULL default 0";
		echo traite_rqt($rqt,"ALTER TABLE resa MODIFY resa_idempr");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.06");
		break;

	case "v5.06":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		@set_time_limit(0);
		//ajout d'un flag pour la résa en circulation
		$rqt = "alter table serialcirc_circ add serialcirc_circ_hold_asked int not null default 0 after serialcirc_circ_subscription";
		echo traite_rqt($rqt,"alter table serialcirc_circ add serialcirc_circ_hold_asked");

		//table de gestion des demandes de reproduction
		$rqt="create table if not exists serialcirc_copy (
			id_serialcirc_copy int not null auto_increment primary key,
			num_serialcirc_copy_empr int not null default 0,
			num_serialcirc_copy_bulletin int not null default 0,
			serialcirc_copy_analysis text,
			serialcirc_copy_date date not null default '0000-00-00',
			serialcirc_copy_state int not null default 0,
			serialcirc_copy_comment text not null
			)";
		echo traite_rqt($rqt,"create table serialcirc_copy");

		$rqt="create table if not exists serialcirc_ask (
			id_serialcirc_ask int unsigned not null auto_increment primary key,
			num_serialcirc_ask_perio int unsigned not null default 0,
			num_serialcirc_ask_serialcirc int unsigned not null default 0,
			num_serialcirc_ask_empr int unsigned not null default 0,
			serialcirc_ask_type int unsigned not null default 0,
			serialcirc_ask_statut int unsigned not null default 0,
			serialcirc_ask_date date NOT NULL default '0000-00-00',
			serialcirc_ask_comment text not null
			)";
		echo traite_rqt($rqt,"create table serialcirc_ask");

		// Création table facettes foireuse en développement
		$rqt = "ALTER TABLE facettes add facette_type_sort int(1) not null default 0 AFTER facette_visible";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_type_sort ");
		$rqt = "ALTER TABLE facettes add facette_order_sort int(1) not null default 0 AFTER facette_type_sort";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_order_sort ");

		// comptabilisation de l'amende : à partir de la date de retour, à partir du délai de grâce
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='amende_comptabilisation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'amende_comptabilisation', '0','Date à laquelle le début de l\'amende sera comptabilisée \r\n 0 : à partir de la date de retour \r\n 1 : à partir du délai de grâce','')";
			echo traite_rqt($rqt,"insert pmb_amende_comptabilisation=0 into parametres");
		}

		// prêt en retard : compter le jour de la date de retour ou la date de relance comme un retard ?
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_calcul_retard_date_debut_incluse' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'pret_calcul_retard_date_debut_incluse', '0','Compter le jour de retour ou de relance comme un jour de retard pour le calcul de l\'amende ? \r\n 0 : Non \r\n  1 : Oui','')";
			echo traite_rqt($rqt,"insert pmb_pret_calcul_retard_date_debut_incluse=0 into parametres");
		}

		//modification taille du champ comment_gestion de la table bannettes
		$rqt = "ALTER TABLE bannettes MODIFY comment_gestion text NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes MODIFY comment_gestion");

		//modification taille du champ comment_public de la table bannettes
		$rqt = "ALTER TABLE bannettes MODIFY comment_public text NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes MODIFY comment_public");

		//AR
		//Exclusion de champs dans la recherche tous les champs en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exclude_fields' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'exclude_fields', '','Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','c_recherche')";
			echo traite_rqt($rqt,"insert opac_exclude_fields into parametres");
		}

		//ajout dates log dans table des vues
		$rqt = "ALTER TABLE statopac_vues ADD date_debut_log DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				ADD date_fin_log DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ";
		echo traite_rqt($rqt,"ALTER TABLE statopac_vues add log dates");

		//Ajout champ serialcirc_tpl pour l'impression de la fiche de circulation
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_tpl TEXT NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_tpl ");

		//AR
		//Onglet Abonnement du compte emprunteur visible ou non...
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='serialcirc_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'serialcirc_active', 0,'Activer la circulation des pédioques dans l\'OPAC \r\n 0: Non \r\n 1: Oui','f_modules')";
			echo traite_rqt($rqt,"insert opac_serialcirc_active into parametres");
		}

		//AR
		//Ajout d'un droit sur le statut pour la circulation des périos
		$rqt = "alter table empr_statut add allow_serialcirc int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table empr_statut add allow_serialcirc");

		// création $pmb_bdd_subversion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bdd_subversion' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'bdd_subversion', '0', 'Sous-version de la base de données')";
			echo traite_rqt($rqt,"insert pmb_bdd_subversion=0 into parametres");
		}

		//AR - Ajout d'un paramètre pour définir la classe d'import des autorités...
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='import_modele_authorities' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'import_modele_authorities', 'notice_authority_import','Quelle classe d\'import utiliser pour les notices d\'autorités ?','')";
			echo traite_rqt($rqt,"insert pmb_import_modele_authorities into parametres");
		}

		//AR - pris dans le tapis entre 2 versions...
		//création de la table origin_authorities
		$rqt = "create table if not exists origin_authorities (
			id_origin_authorities int(10) unsigned NOT NULL AUTO_INCREMENT,
			origin_authorities_name varchar(255) NOT NULL DEFAULT '',
			origin_authorities_country varchar(10) NOT NULL DEFAULT '',
			origin_authorities_diffusible int(10) unsigned NOT NULL DEFAULT 0,
			primary key (id_origin_authorities)
			)";
		echo traite_rqt($rqt,"create table origin_authorities");
		//AR - ajout de valeurs par défault...
		$rqt = "insert into origin_authorities
				(id_origin_authorities,origin_authorities_name,origin_authorities_country,origin_authorities_diffusible)
			values
				(1,'Catalogue Interne','FR',1),
				(2,'BnF','FR',1)";
		echo traite_rqt($rqt,"insert default values into origin_authorities");

		//AR - création de la table authorities_source
		$rqt = "create table if not exists authorities_sources (
			id_authority_source int(10) unsigned NOT NULL AUTO_INCREMENT,
			num_authority int(10) unsigned NOT NULL DEFAULT 0,
			authority_number varchar(50) NOT NULL DEFAULT '',
			authority_type varchar(20) NOT NULL DEFAULT '',
			num_origin_authority int(10) unsigned NOT NULL DEFAULT 0,
			authority_favorite int(10) unsigned NOT NULL DEFAULT 0,
			import_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			primary key (id_authority_source) )";
		echo traite_rqt($rqt,"create table authorities_sources");

		//AR - création de la table notices_authorities_sources
		$rqt ="create table if not exists notices_authorities_sources (
			num_authority_source int(10) unsigned NOT NULL DEFAULT 0,
			num_notice int(10) unsigned NOT NULL DEFAULT 0,
			primary key (num_authority_source,num_notice)
			)";
		echo traite_rqt($rqt,"create table notices_authorities_sources");

		//AR - modification du champ aut_link_type
		$rqt = "alter table aut_link change aut_link_type aut_link_type varchar(2) not null default ''";
		echo traite_rqt($rqt,"alter table aut_link change aut_link_type varchar");

		//MB - Modification de l'explication du paramètre d'affichage des dates d'exemplaire
		$rqt="UPDATE parametres SET comment_param='Afficher les dates des exemplaires ? \n 0 : Aucune date.\n 1 : Date de création et modification.\n 2 : Date de dépôt et retour (BDP).\n 3 : Date de création, modification, dépôt et retour.' WHERE type_param='pmb' AND sstype_param='expl_show_dates'";
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG
		// localisation des prévisions
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='location_resa_planning' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'pmb', 'location_resa_planning', '0', '0', 'Utiliser la gestion de la prévision localisée?\n 0: Non\n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT location_resa_planning INTO parametres") ;
		}

		//Localisation par défaut sur la visualisation des états des collections
		$rqt = "ALTER TABLE users ADD deflt_collstate_location int(6) UNSIGNED DEFAULT 0 after deflt_docs_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_collstate_location after deflt_docs_location");

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement\n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//maj valeurs possibles pour empr_show_rows
		$rqt = "update parametres set comment_param='Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés \n 1: icône panier' where type_param= 'empr' and sstype_param='show_rows' ";
		echo traite_rqt($rqt,"update empr_show_rows into parametres");

		//maj valeurs possibles pour empr_sort_rows
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//maj commentaire sms_msg_retard
		$rqt = "update parametres set comment_param='Texte du sms envoyé lors d\'un retard' where type_param= 'empr' and sstype_param='sms_msg_retard' ";
		echo traite_rqt($rqt,"update empr_sms_msg_retard into parametres");

		//maj commentaire afficher_numero_lecteur_lettres
		$rqt = "update parametres set comment_param='Afficher le numéro et le mail du lecteur sous l\'adresse dans les différentes lettres' where type_param= 'pmb' and sstype_param='afficher_numero_lecteur_lettres' ";
		echo traite_rqt($rqt,"update pmb_afficher_numero_lecteur_lettres into parametres");

		//DB
		//modification du paramètre empr_sms_activation
		$rqt = "select valeur_param from parametres where type_param= 'empr' and sstype_param='sms_activation' ";
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			$old_value = pmb_mysql_result($res,0,0);
			if ($old_value==1) {
				$new_value='1,1,1,1';
				$rqt = "update parametres set valeur_param='".$new_value."', comment_param='Activation de l\'envoi de sms. : relance 1,relance 2,relance 3,resa\n\n 0: Inactif\n 1: Actif' where type_param= 'empr' and sstype_param='sms_activation' ";
				echo traite_rqt($rqt,"update sms_activation");
			} elseif ($old_value==0) {
				$new_value='0,0,0,0';
				$rqt = "update parametres set valeur_param='".$new_value."', comment_param='Activation de l\'envoi de sms. : relance 1,relance 2,relance 3,resa\n\n 0: Inactif\n 1: Actif' where type_param= 'empr' and sstype_param='sms_activation' ";
				echo traite_rqt($rqt,"update empr_sms_activation");
			}
		}

		//Ajout de la durée de consultation pour la circulation des périos
		$rqt = "alter table abts_periodicites add consultation_duration int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table abts_periodicites add consultation_duration");

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		// suppr index inutile
		$rqt = "alter table notices_fields_global_index drop index i_id_value";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop index i_id_value");

		//Modification du commentaire du paramètre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		// Ajout du module Havest: Moissonneur de notice
        $rqt="create table if not exists harvest_profil (
            id_harvest_profil int unsigned not null auto_increment primary key,
            harvest_profil_name varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table harvest");

        $rqt="create table if not exists harvest_field (
            id_harvest_field int unsigned not null auto_increment primary key,
            num_harvest_profil int unsigned not null default 0,
            harvest_field_xml_id int unsigned not null default 0,
            harvest_field_first_flag int unsigned not null default 0,
            harvest_field_order int unsigned not null default 0
       		)";
        echo traite_rqt($rqt,"create table harvest_field");

        $rqt="create table if not exists harvest_src (
            id_harvest_src int unsigned not null auto_increment primary key,
            num_harvest_field int unsigned not null default 0,
            num_source int unsigned not null default 0,
            harvest_src_unimacfield varchar(255) not null default '',
            harvest_src_unimacsubfield varchar(255) not null default '',
            harvest_src_pmb_unimacfield varchar(255) not null default '',
            harvest_src_pmb_unimacsubfield varchar(255) not null default '',
            harvest_src_prec_flag int unsigned not null default 0,
            harvest_src_order int unsigned not null default 0
        	)";
        echo traite_rqt($rqt,"create table harvest_src");

        $rqt="create table if not exists harvest_profil_import (
            id_harvest_profil_import int unsigned not null auto_increment primary key,
            harvest_profil_import_name varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table harvest_profil_import");

        $rqt="create table if not exists harvest_profil_import_field (
            num_harvest_profil_import int unsigned not null default 0,
            harvest_profil_import_field_xml_id int unsigned not null default 0,
            harvest_profil_import_field_flag int unsigned not null default 0,
            harvest_profil_import_field_order int unsigned not null default 0,
            PRIMARY KEY (num_harvest_profil_import, harvest_profil_import_field_xml_id)
        	)";
        echo traite_rqt($rqt,"create table harvest_profil_import_field");

       	$rqt = "CREATE TABLE if not exists harvest_search_field (
			num_harvest_profil int unsigned not null default 0,
			num_source int unsigned not null default 0,
			num_field int unsigned not null default 0,
			num_ss_field int unsigned not null default 0 ,
            PRIMARY KEY (num_harvest_profil, num_source)
			)";
		echo traite_rqt($rqt,"CREATE TABLE harvest_search_field");

		//AR - Ajout d'un paramètre de blocage d'import dans les autorités
		$rqt = "alter table noeuds add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table noeuds add authority_import_denied");
		$rqt = "alter table authors add author_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table authors add author_import_denied");
		$rqt = "alter table titres_uniformes add tu_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table titres_uniformes add tu_import_denied");
		$rqt = "alter table sub_collections add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table sub_collections add authority_import_denied");
		$rqt = "alter table collections add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table collections add authority_import_denied");

		//AR - Modification d'un paramètre pour définir la classe d'import des autorités...
		$rqt = "update parametres set valeur_param = 'authority_import' where type_param= 'pmb' and sstype_param = 'import_modele_authorities'";
		echo traite_rqt($rqt,"update parametres set pmb_import_modele_authorities = 'authority_import'");

		//Ajout d'un index sur le champ ref dans les tables entrepots
		//Récupération de la liste des sources
		$sql_liste_sources = "SELECT source_id FROM connectors_sources ";
		$res_liste_sources = pmb_mysql_query($sql_liste_sources, $dbh) or die(pmb_mysql_error());

		//Pour chaque source
		while ($row=pmb_mysql_fetch_row($res_liste_sources)) {
			$sql_alter_table = "alter table entrepot_source_".$row[0]." drop index i_ref ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." drop index i_ref");
			$sql_alter_table = "alter table entrepot_source_".$row[0]." add index i_ref (ref) ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." add index i_ref");
		}

		//Ajout d'un parametre permettant de préciser si l'on informe par email de l'évolution des demandes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_demandes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'demandes', 'email_demandes', '1',
					'Information par email de l\'évolution des demandes.\n 0 : Non\n 1 : Oui',
					'',0) ";
			echo traite_rqt($rqt, "insert demandes_email_demandes into parameters");
		}


		//AR - Ajout d'un paramètre utilisateur (choix d'un thésaurus par défaut en import d'autorités
		$rqt = "alter table users add deflt_import_thesaurus int not null default 1 after deflt_thesaurus";
		echo traite_rqt($rqt,"alter table users add deflt_import_thesaurus'");

		//AR - On lui met un bonne valeur par défaut...
		$rqt = "update users set deflt_import_thesaurus = ".$thesaurus_defaut;
		echo traite_rqt($rqt,"update users set deflt_import_thesaurus");

		//AR - Ajout d'une colonne sur la table connectors_sources pour définir les types d'enrichissements autorisés dans une source
		$rqt = "alter table connectors_sources add type_enrichment_allowed text not null";
		echo traite_rqt($rqt,"alter table connectors_sources add type_enrichment_allowed");

		// ER - index notices.statut
		$rqt = "ALTER TABLE notices DROP INDEX i_not_statut " ;
		echo traite_rqt($rqt,"ALTER TABLE notices DROP INDEX i_not_statut ") ;
		$rqt = "ALTER TABLE notices ADD INDEX i_not_statut (statut)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_not_statut (statut)") ;


		// Création cms
		$rqt="create table if not exists cms_cadres (
            id_cadre int unsigned not null auto_increment primary key,
            cadre_hash varchar(255) not null default '',
            cadre_name varchar(255) not null default '',
            cadre_styles text not null,
            cadre_dom_parent varchar(255) not null default '',
            cadre_dom_after varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_cadres");

		$rqt="create table if not exists cms_cadre_content (
            id_cadre_content int unsigned not null auto_increment primary key,
            cadre_content_hash varchar(255) not null default '',
            cadre_content_type varchar(255) not null default '',
            cadre_content_num_cadre int(10) unsigned not null default 0,
            cadre_content_data text not null,
            cadre_content_num_cadre_content int unsigned not null default 0
        	)";
        echo traite_rqt($rqt,"create table cms_cadre_content");

		$rqt="create table if not exists cms_pages (
            id_page int unsigned not null auto_increment primary key,
            page_hash varchar(255) not null default '',
            page_name varchar(255) not null default '',
            page_description text not null
       		)";
        echo traite_rqt($rqt,"create table cms_pages");

		$rqt="create table if not exists cms_vars (
            id_var int unsigned not null auto_increment primary key,
            var_num_page int unsigned not null default 0,
            var_name varchar(255) not null default '',
            var_comment varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_vars");

		$rqt="create table if not exists cms_pages_env (
            page_env_num_page int unsigned not null auto_increment primary key,
            page_env_name varchar(255) not null default '',
            page_env_id_selector varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_pages_env");


		$rqt="create table if not exists cms_hash (
            hash varchar(255) not null default '' primary key
        	)";
        echo traite_rqt($rqt,"create table cms_hash ");

		//DB - parametre gestion de pret court
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='short_loan_management' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'short_loan_management', '0', 'Gestion des prêts courts\n 0: Non\n 1: Oui')";
			echo traite_rqt($rqt,"insert pmb_short_loan_management=0 into parametres");
		}
		//DB - ajout colonne duree pret court dans la table docs_type
		$rqt="ALTER TABLE docs_type ADD short_loan_duration INT(6) UNSIGNED NOT NULL DEFAULT 1 ";
		echo traite_rqt($rqt,"alter table docs_type add short_loan_duration");

		//DB - correction origine notices
		$rqt = "update notices set origine_catalogage='1', update_date=update_date where origine_catalogage='0' ";
		echo traite_rqt($rqt,"alter table notices correct origine_catalogage");

		//DB - ajout flag pret court dans table pret
		$rqt = "ALTER TABLE pret ADD short_loan_flag INT(1) NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table pret add short_loan_flag");

		//DB - ajout flag pret court dans table pret_archive
		$rqt = "ALTER TABLE pret_archive ADD arc_short_loan_flag INT(1) NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table pret_archive add arc_short_loan_flag");

		//DB - parametre gestion de monopole de pret
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='loan_trust_management' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'loan_trust_management', '0', 'Gestion de monopole de prêt\n 0: Non\n x: nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)')";
			echo traite_rqt($rqt,"insert pmb_loan_trust_management=0 into parametres");
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.07");
		break;

	case "v5.07":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// ER : pour le gars au pull rouge
		$rqt = "ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ");
		$rqt = "ALTER TABLE exemplaires MODIFY expl_cb varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cb varchar(255) ");

		//AR - Ajout d'un champ dans cms_cadres
		$rqt = "alter table cms_cadres add cadre_object varchar(255) not null default '' after cadre_hash";
		echo traite_rqt($rqt,"alter table cms_cadre add cadre_object");

		//JP - Ajout tri en opac pour champs persos de notice
		$rqt = "ALTER TABLE collstate_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE empr_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE expl_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE gestfic0_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE notices_custom ADD opac_sort INT NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD opac_sort INT NOT NULL DEFAULT 1");

		//JP : Ajout d'un paramètre permettant de choisir une navigation abécédaire ou non en navigation dans les périodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_abc_search' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_abc_search', '0',
					'Recherche abécédaire dans le navigateur de périodiques en OPAC.\n0 : Non.\n1 : Oui.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_abc_search 0 into parameters");
		}

		//JP : Ajout d'un paramètre permettant de choisir le nombre maximum de notices par onglet en navigation dans les périodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_max_per_onglet' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_max_per_onglet', '10',
					'Recherche dans le navigateur de périodiques en OPAC : nombre maximum de notices par onglet.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_max_per_onglet 10 into parameters");
		}

		//DG - Mail de rappel au référent
		$rqt = "ALTER TABLE groupe ADD mail_rappel INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE groupe ADD mail_rappel default 0");

		//DG - Modification du commentaire du paramètre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		//DG - Alerter l'utilisateur par mail des nouvelles demandes en OPAC ?
		$rqt = "ALTER TABLE users ADD user_alert_demandesmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_resamail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_demandesmail default 0");

		$rqt = "ALTER TABLE cms_cadre_content ADD cadre_content_object  VARCHAR(  255 ) NOT NULL DEFAULT '' AFTER cadre_content_type";
		echo traite_rqt($rqt,"ALTER TABLE cms_cadre_content ADD cadre_content_object");

		$rqt = "ALTER TABLE cms_build ADD build_page int(11) NOT NULL DEFAULT 0 AFTER build_obj";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_page");

		//DG - Ordre des langues pour les notices
		$rqt = "ALTER TABLE notices_langues ADD ordre_langue smallint(2) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE notices_langues ADD ordre_langue") ;

		//DB - grilles emprunteurs
		$rqt = "create table empr_grilles (
				empr_grille_categ int(5) not null default 0,
				empr_grille_location int(5) not null default 0,
				empr_grille_format longtext,
				primary key  (empr_grille_categ,empr_grille_location))";
		echo traite_rqt($rqt,"create table empr_grilles") ;

		//DB - parametres de gestion d'accès aux programmes externes pour l'indexation des documents numeriques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum_ext' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'indexation_docnum_ext', '',
					'Paramètres de gestion d\'accès aux programmes externes pour l\'indexation des documents numériques :\n\n Chaque paramètre est défini par un  couple : \"nom=valeur\"\n Les paramètres sont séparés par un \"point-virgule\".\n\n\n Exemples d\'utilisation de \"pyodconverter\", \"jodconverter\" et \"pdftotext\" :\n\npyodconverter_cmd=/opt/openoffice.org3/program/python /opt/ooo_converter/DocumentConverter.py %1s %2s;\njodconverter_cmd=/usr/bin/java -jar /opt/ooo_converter/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar %1s %2s;\njodconverter_url=http://localhost:8080/converter/converted/%1s;\npdftotext_cmd=/usr/bin/pdftotext -enc UTF-8 %1s -;',
					'',0) ";
			echo traite_rqt($rqt, "insert indexation_docnum_ext into parameters");
		}

		//Onglet perso en affichage de notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_onglets' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'notices_format_onglets', '','Liste des id de template de notice pour ajouter des onglets personnalisés en affichage de notice\nExemple: 1,3','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_notices_format_onglets into parametres");
		}

		//DG - Ajout de la localisation de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_empr_location INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_statut ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_empr_location default 0");

 		//DG - Ajout du type d'abonnement de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_type_abt INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_location ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_type_abt default 0");

		//DG - Libellé OPAC des statuts d'exemplaires
		$rqt = "ALTER TABLE docs_statut ADD statut_libelle_opac VARCHAR(255) DEFAULT '' after statut_libelle";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut add statut_libelle_opac default ''");

		//DG - Visibilité OPAC des statuts d'exemplaires
 		$rqt = "ALTER TABLE docs_statut ADD statut_visible_opac TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut ADD statut_visible_opac") ;

		//DB - parametres d'alerte avant affichage des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert', '', 'Message d\'alerte à l\'ouverture des documents numériques.', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert into parameters");
		}

		$rqt = "ALTER TABLE cms_build ADD build_fixed int(11) NOT NULL DEFAULT 0 AFTER id_build";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_fixed");

		$rqt = "ALTER TABLE cms_build ADD build_child_before varchar(255) not null default '' AFTER build_parent";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_child_before");

		//AR - création d'une boite noire pour les modules du portail
		$rqt="create table if not exists cms_managed_modules (
			managed_module_name varchar(255) not null default '',
			managed_module_box text not null,
			primary key (managed_module_name))";
		echo traite_rqt($rqt, "create table if not exists cms_managed_modules");


		$rqt = "alter table cms_cadres add cadre_fixed int(11) not null default 0 after cadre_name";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_fixed");


		//DG - Fixer l'âge minimum d'accès à la catégorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_min INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_min default 0");

		//DG - Fixer l'âge maximum d'accès à la catégorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_max INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_max default 0");

		// Liste des cms
		$rqt="create table if not exists cms (
            id_cms int unsigned not null auto_increment primary key,
            cms_name varchar(255) not null default '',
            cms_comment text not null
        )";
        echo traite_rqt($rqt,"create table cms");

 		// évolutions des cms
		$rqt="create table if not exists cms_version (
            id_version int unsigned not null auto_increment primary key,
            version_cms_num int unsigned not null default 0 ,
            version_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            version_comment text not null,
            version_public int unsigned not null default 0,
            version_user int unsigned not null default 0
        )";
        echo traite_rqt($rqt,"create table cms_version");

		$rqt = "alter table cms_build add build_version_num int not null default 0 after id_build";
		echo traite_rqt($rqt,"alter table cms_build add build_version_num");

		//id du cms à utiliser en Opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cms' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'cms', 0,'id du CMS utilisé en OPAC','a_general')";
			echo traite_rqt($rqt,"insert opac_cms into parametres");
		}

		//DG - Colonnes exemplaires affichées en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_data' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'expl_data', 'expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle', 'Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle #n : id des champs personnalisés \r\n expl_cb est obligatoire et sera ajouté si absent','')";
			echo traite_rqt($rqt,"insert pmb_expl_data=expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,codestat_libelle,lender_libelle,tdoc_libelle into parametres");
		}

		//DB - parametre gestion de monopole de pret
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_display_location_without_expl' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'expl_display_location_without_expl', '0', 'Affichage de la liste des localisations sans exemplaire\n 0: Non\n 1: oui')";
			echo traite_rqt($rqt,"insert pmb_expl_display_location_without_expl=0 into parametres");
		}

		// Voir les prets de son groupe de lecteur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_group_checkout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'show_group_checkout', '0', 'Le responsable du groupe de lecteur voit les prêts de son groupe\n 0: Non\n 1: oui','a_general')";
			echo traite_rqt($rqt,"insert opac_show_group_checkout=0 into parametres");
		}

		// Archivage DSI
		$rqt="create table if not exists dsi_archive (
           	num_banette_arc int unsigned not null default 0,
            num_notice_arc int unsigned not null default 0,
            date_diff_arc date not null default '0000-00-00',
            primary key (num_banette_arc,num_notice_arc,date_diff_arc)
        )";
		echo traite_rqt($rqt,"create table dsi_archive");

		//Nombre d'archive à mémoriser en dsi
		$rqt = "ALTER TABLE bannettes ADD archive_number INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add archive_number");

		//AR - Erreur dans le type de colonne
		$rqt = "ALTER TABLE cms_pages MODIFY page_hash varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ");

		//AR - L'authentification Digest impose une valeur en clair...
		$rqt= "alter table users add user_digest varchar(255) not null default '' after pwd";
		echo traite_rqt($rqt,"alter table users add user_digest");

		//Ajout de deux paramètres pour la navigation par facette
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facette_in_bandeau_2' "))==0){
			$rqt = "insert into parametres values(0,'opac','facette_in_bandeau_2',0,'La navigation par facettes apparait dans le bandeau ou dans le bandeau 2\n0 : dans le bandeau\n1 : Dans le bandeau 2','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_facette_in_bandeau_2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='autolevel2' "))==0){
			$rqt = "insert into parametres values(0,'opac','autolevel2',0,'0 : mode normal de recherche\n1 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_autolevel2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='first_page_params' "))==0){
			$rqt = "insert into parametres values(0,'opac','first_page_params','','Structure Json récapitulant les paramètres à initialiser pour la page d\\'accueil :\nExemple : \n{\n\"lvl\":\"cmspage\",\n\"pageid\":2\n}','b_aff_general',0)";
			echo traite_rqt($rqt,"insert opac_first_page_params='' into parametres");
		}

		$rqt = "ALTER TABLE cms_build ADD build_type varchar(255) not null default 'cadre' AFTER build_version_num";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_type");

		//Création d'un div class raw
		$rqt = "ALTER TABLE cms_build ADD build_div INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table cms_build add build_div");

		// Ajout tpl de notice pour générer le header
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.08");
		break;

	case "v5.08":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800");

		//AR - paramètre activant les liens vers les documents numériques non visibles
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_links_invisible_docnums' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'show_links_invisible_docnums', '0',
			'Afficher les liens vers les documents numériques non visible en mode non connecté. (Ne fonctionne pas avec les droits d\'accès).\n 0 : Non.\n1 : Oui.',
			'e_aff_notice',0) ";
			echo traite_rqt($rqt, "insert opac_show_links_invisible_docnums into parameters");
		}

		// Générer un document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_generate INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_generate");

		// Template de notice en génération de document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_notice_tpl INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_notice_tpl");

		// Générer un document avec les doc num (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_insert_docnum INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_insert_docnum");

		// Grouper les documents (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_group INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_group");

		// Ajouter un sommaire (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_add_summary INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_add_summary");

		//DG - Index
		$rqt = "alter table explnum drop index explnum_repertoire";
		echo traite_rqt($rqt,"alter table explnum drop index explnum_repertoire");
		$rqt = "alter table explnum add index explnum_repertoire(explnum_repertoire)";
		echo traite_rqt($rqt,"alter table explnum add index explnum_repertoire");

		// Ajout du module template de mail
        $rqt="create table if not exists mailtpl (
            id_mailtpl int unsigned not null auto_increment primary key,
            mailtpl_name varchar(255) not null default '',
            mailtpl_objet varchar(255) not null default '',
            mailtpl_tpl text not null,
            mailtpl_users varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table mailtpl");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_folder' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'img_folder', '',	'Répertoire de stockage des images', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_folder into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_url' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'img_url', '',	'URL d\'accès du répertoire des images (pmb_img_folder)', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_url into parameters");
		}
		// Ajout de la possibilité de joindre les images dans le mail ( pmb_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='pmb' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre pmb_mail_html_format");

		// Ajout de la possibilité de joindre les images dans le mail ( opac_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='opac' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre opac_mail_html_format");

		//AR - Ajout d'une colonne pour marquer un set comme étant en cours de rafraississement
		$rqt = "alter table connectors_out_sets add being_refreshed int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table connectors_out_sets add bien_refreshed");

		//DG - Infobulle lors du survol des vignettes (gestion)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'pmb', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url','')";
			echo traite_rqt($rqt,"insert pmb_book_pics_msg='' into parametres");
		}

		//DG - Infobulle lors du survol des vignettes (opac)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_book_pics_msg='' into parametres");
		}

		//AR - Utilisation des quotas pour la définition des vues disponibles pour un emprunteur
		$rqt = "create table if not exists quotas_opac_views (
			quota_type int(10) unsigned not null default 0,
			constraint_type varchar(255) not null default '',
			elements int(10) unsigned not null default 0,
			value text not null,
			primary key(quota_type,constraint_type,elements)
		)";
		echo traite_rqt($rqt,"create table quotas_opac_views");

		//AR - table de mots
		$rqt = "create table if not exists words (
			id_word int unsigned not null auto_increment primary key,
			word varchar(255) not null default '',
			lang varchar(10) not null default '',
			unique i_word_lang (word,lang)
		)";
		echo traite_rqt($rqt,"create table words");

		$rqt = "show fields from notices_mots_global_index";
		$res = pmb_mysql_query($rqt);
		$exists = false;
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				if($row->Field == "num_word"){
					$exists = true;
					break;
				}
			}
		}
		if(!$exists){
			//la méthode du chef reste la meilleure
			set_time_limit(0);

			if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
				$rqt = "truncate table notices_fields_global_index";
				echo traite_rqt($rqt,"truncate table notices_fields_global_index");

				$rqt = "truncate table notices_mots_global_index";
				echo traite_rqt($rqt,"truncate table notices_mots_global_index");

				// Info de réindexation
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}

			//on ajoute un index bien pratique...
			$rqt ="alter table notices_mots_global_index add index mot_lang(mot,lang)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add index");

			//remplissage de la table mots
			$rqt ="insert ignore into words (word,lang) select distinct mot,lang from notices_mots_global_index";
			echo traite_rqt($rqt,"insert into words");

			//on utilise une table tampon
			$rqt ="create table transition select id_notice,code_champ,code_ss_champ,mot,id_word from notices_mots_global_index join words on (mot=word and notices_mots_global_index.lang=words.lang);";
			echo traite_rqt($rqt,"create table transition");
			//on y ajoute les index qui vont bien
			$rqt ="alter table transition add primary key (id_notice,code_champ,code_ss_champ,mot)";
			echo traite_rqt($rqt,"alter table transition add primary key");

			//on ajout la clé étrangère num_word dans notices_mots_global_index
			$rqt ="alter table notices_mots_global_index add num_word int(10) unsigned not null default 0 after mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add num_word");
			//on l'affecte
			$rqt ="update notices_mots_global_index as a0 join transition as a1 on (a0.id_notice=a1.id_notice and a0.code_champ=a1.code_champ and a0.code_ss_champ=a1.code_ss_champ and a0.mot=a1.mot) set num_word=id_word";
			echo traite_rqt($rqt,"update notices_mots_global_index set num_word=id_word");

			//on peut se passer de certains index et mettre les nouveaux
			$rqt ="drop index i_mot on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index i_mot on notices_mots_global_index");
			$rqt ="drop index i_id_mot on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index i_id_mot on notices_mots_global_index");
			$rqt ="alter table notices_mots_global_index add index i_id_mot(num_word,id_notice)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_mot");
			$rqt ="alter table notices_mots_global_index drop primary key";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop primary key");
			$rqt ="alter table notices_mots_global_index add primary key (id_notice,code_champ,code_ss_champ,num_word,position)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add primary key");

			//on supprime l'index pratique
			$rqt ="drop index mot_lang on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index mot_lang on notices_mots_global_index");

			//certains champs n'ont plus d'utilité dans notices_mots_global_index
			$rqt ="alter table notices_mots_global_index drop mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop mot");
			$rqt ="alter table notices_mots_global_index drop nbr_mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop nbr_mot");
			$rqt ="alter table notices_mots_global_index drop lang";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop lang");

			//on supprime l'index pratique
			//on supprime la table de transition
			$rqt ="drop table transition";
			echo traite_rqt($rqt,"drop table transition");
		}

		//AR - modification du paramètre de gestion des vues
		$rqt = "update parametres set comment_param = 'Activer les vues OPAC :\n 0 : non activé\n 1 : activé avec gestion classique\n 2 : activé avec gestion avancée' where type_param = 'pmb' and sstype_param = 'opac_view_activate'";
		echo traite_rqt($rqt,"update parametres pmb_opac_view_activate");

		//DB - modification du paramètre utiliser_calendrier
		$rqt = "update parametres set comment_param = 'Utiliser le calendrier des jours d\'ouverture ?\n 0 : non\n 1 : oui, pour le calcul des dates de retour et des retards\n 2 : oui, pour le calcul des dates de retour uniquement' where type_param = 'pmb' and sstype_param = 'utiliser_calendrier'";
		echo traite_rqt($rqt,"update parametres pmb_utiliser_calendrier");

		//NG - Ajout dans les statuts d'exemplaire la possibilité de rendre réservable ou non
		$rqt = "ALTER TABLE docs_statut ADD statut_allow_resa INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table docs_statut add statut_allow_resa");
		$rqt = "UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1 ";
		echo traite_rqt($rqt,"UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1");

		// Ajout CMS actif par défaut en Opac
		$rqt = "alter table cms add cms_opac_default int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms add cms_opac_default");

		$rqt = "create table if not exists cms_editorial_types (
			id_editorial_type int unsigned not null auto_increment primary key,
			editorial_type_element varchar(20) not null default '',
			editorial_type_label varchar(255) not null default '',
			editorial_type_comment text not null
		)";
		echo traite_rqt($rqt,"create table cms_editorial_types");

		//AR - on ajoute le type de contenu sur les tables cms_articles et cms_sections
		$rqt = "alter table cms_articles add article_num_type int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms_articles add article_num_type");
		$rqt = "alter table cms_sections add section_num_type int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms_sections add section_num_type");

		//AR - Un type de contenu c'est quoi? c'est une définition de grille de champs perso
		$rqt = "create table if not exists cms_editorial_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table cms_editorial_custom ");

		$rqt = "create table if not exists cms_editorial_custom_lists (
			cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_list_value varchar(255) default NULL,
			cms_editorial_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (cms_editorial_custom_champ),
			KEY editorial_champ_list_value (cms_editorial_custom_champ,cms_editorial_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_lists ");

		$rqt = "create table if not exists cms_editorial_custom_values (
			cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_origine int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_small_text varchar(255) default NULL,
			cms_editorial_custom_text text,
			cms_editorial_custom_integer int(11) default NULL,
			cms_editorial_custom_date date default NULL,
			cms_editorial_custom_float float default NULL,
			KEY editorial_custom_champ (cms_editorial_custom_champ),
			KEY editorial_custom_origine (cms_editorial_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_values ");

		//NG - Ajout de l'url permetant de retouver la page Opac contenant le cadre
		$rqt = "alter table cms_cadres add cadre_url text not null ";
		echo traite_rqt($rqt,"alter table cms_cadre add cadre_url");

		//MB - Ajout d'une colonne pour les noeuds utilisables ou non en indexation
		$rqt = "ALTER TABLE noeuds ADD not_use_in_indexation INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table noeuds add not_use_in_indexation");

		//MB - Modification du commentaire du paramètre show_categ_browser
		$rqt = "UPDATE parametres SET comment_param = 'Affichage des catégories en page d\'accueil OPAC:\n0: Non\n1: Oui\n1 3,1: Oui, avec thésaurus id 3 puis 1 (préciser les thésaurus à afficher et l\'ordre)' where type_param = 'opac' and sstype_param = 'show_categ_browser'";
		echo traite_rqt($rqt,"update parametres show_categ_browser");

		//MB - Remplacement du code de lien d'autorité 2 par z car c'est le même libellé et z est normé
		$rqt = "UPDATE aut_link SET aut_link_type = 'z' where aut_link_type = '2' ";
		echo traite_rqt($rqt,"update aut_link");

		//AR indexons correctement le contenu éditorial
		$rqt = "create table if not exists cms_editorial_words_global_index(
			num_obj int unsigned not null default 0,
			type varchar(20) not null default '',
			code_champ int not null default 0,
			code_ss_champ int not null default 0,
			num_word int not null default 0,
			pond int not null default 100,
			position int not null default 1,
			primary key (num_obj,type,code_champ,code_ss_champ,num_word,position)

		)";
		echo traite_rqt($rqt,"create table cms_editorial_words_global_index ");

		$rqt = "create table if not exists cms_editorial_fields_global_index(
			num_obj int unsigned not null default 0,
			type varchar(20) not null default '',
			code_champ int(3) not null default 0,
			code_ss_champ int(3) not null default 0,
			ordre int(4) not null default 0,
			value text not null,
			pond int(4) not null default 100,
			lang varchar(10) not null default '',
			primary key(num_obj,type,code_champ,code_ss_champ,ordre),
			index i_value(value(300))
		)";
		echo traite_rqt($rqt,"create table cms_editorial_fields_global_index ");

		//DB - parametre d'alerte avant affichage des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert_doctype' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert_doctype', '', 'Liste des types de documents pour lesquels une alerte est générée (séparés par une virgule).', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert_doctype into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_memo_url int not null default 0 after cadre_url";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_memo_url");

		//DB - entrepot d'archivage à la suppression des notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='archive_warehouse' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'archive_warehouse', '0', 'Identifiant de l\'entrepôt d\'archivage à la suppression des notices.', '',0) ";
			echo traite_rqt($rqt, "insert archive_warehouse into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_classement  varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_classement");

		//NG - Imprimante ticket
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'printer_name', '', 'Nom de l\'imprimante de ticket de prêt, utilisant l\'applet jzebra. Le nom de l\'imprimante doit correspondre à la class développée spécifiquement pour la piloter.\nExemple: Nommer l\'imprimante \'metapace\' pour utiliser le driver classes/printer/metapace.class.php', '',0) ";
			echo traite_rqt($rqt, "insert pmb_printer_name into parameters");
		}

		//DG - Localisation par défaut sur la visualisation des réservations
		$rqt = "ALTER TABLE users ADD deflt_resas_location int(6) UNSIGNED DEFAULT 0 after deflt_collstate_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_resas_location after deflt_collstate_location");

		//DG - parametre localisation des groupes de lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='groupes_localises' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'empr', 'groupes_localises', '0', 'Groupes de lecteurs localisés par rapport au responsable \n0: Non \n1: oui')";
			echo traite_rqt($rqt,"insert empr_groupes_localises=0 into parametres");
		}

		// Activation des recherches similaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_simili_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_simili_search', '0', 'Activer les recherches similaires en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_simili_search='0' into parametres ");
		}

		//ajout d'une date de création pour les articles et les rubriques
		$rqt ="alter table cms_articles add article_creation_date date";
		echo traite_rqt($rqt,"alter table cms_articles add article_creation_date date");
		$rqt ="alter table cms_sections add section_creation_date date";
		echo traite_rqt($rqt,"alter table cms_sections add section_creation_date date");

		//index d'on se lève tous pour la bannette de Camille
		$rqt = "alter table bannette_abon drop index i_num_empr";
		echo traite_rqt($rqt,"alter table bannette_abon drop index i_num_empr");
		$rqt = "alter table bannette_abon add index i_num_empr(num_empr)";
		echo traite_rqt($rqt,"alter table bannette_abon add index i_num_empr(num_empr)");

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_plus' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_plus', 'plus.gif', 'Image à utiliser devant un titre de notice pliée', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_plus into parametres ");
		}

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_moins' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_moins', 'minus.gif', 'Image à utiliser devant un titre de notice dépliée', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_moins into parametres ");
		}

		//MB - Modification du commentaire du paramètre notices_depliable
		$rqt = "UPDATE parametres SET comment_param = 'Affichage dépliable des notices en résultat de recherche:\n0: Non dépliable\n1: Dépliable en cliquant que sur l\'icone\n2: Déplibable en cliquant sur toute la ligne du titre' where type_param = 'opac' and sstype_param = 'notices_depliable'";
		echo traite_rqt($rqt,"update parametres notices_depliable");

		// Ajout du regroupement d'exemplaires pour le prêt
		$rqt = "create table if not exists groupexpl (
			id_groupexpl int(10) unsigned NOT NULL auto_increment,
			groupexpl_resp_expl_num int(10) unsigned NOT NULL default 0,
			groupexpl_name varchar(255) NOT NULL default '',
			groupexpl_comment varchar(255) NOT NULL default '',
			groupexpl_location int(10) unsigned NOT NULL default 0,
			groupexpl_statut_resp int(10) unsigned NOT NULL default 0,
			groupexpl_statut_others int(10) unsigned NOT NULL default 0,
			PRIMARY KEY (id_groupexpl)) ";
		echo traite_rqt($rqt,"create table groupexpl ");

		// Ajout du regroupement d'exemplaires pour le prêt
		$rqt = "create table if not exists groupexpl_expl (
			groupexpl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_expl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_checked int unsigned NOT NULL  default 0,
			PRIMARY KEY (groupexpl_num, groupexpl_expl_num)) ";
		echo traite_rqt($rqt,"create table groupexpl_expl ");

		// Activation du prêt d'exemplaires groupés
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_groupement' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'pret_groupement', '0', 'Activer le prêt d\'exemplaires regroupés en un seul lot. La gestion des groupes se gére en Circulation / Groupe d\'exemplaires :\n 0 : non \n 1 : oui', '', '0')";
			echo traite_rqt($rqt,"insert pmb_pret_groupement='0' into parametres ");
		}

		//AR - refonte éditions...
		$rqt = "create table if not exists editions_states (
			id_editions_state int unsigned not null auto_increment primary key,
			editions_state_name varchar(255) not null default '',
			editions_state_num_classement int not null default 0,
			editions_state_used_datasource varchar(50) not null default '',
			editions_state_comment text not null,
			editions_state_fieldslist text not null,
			editions_state_fieldsparams text not null
		)";
		echo traite_rqt($rqt,"create table if not exists editions_states");

		// cms: Classement des pages
		$rqt = "alter table cms_pages add page_classement  varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table cms_pages add page_classement");

		// Transfert: regroupement des départs
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='regroupement_depart' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'regroupement_depart', '0', '1', 'Active le regroupement des départs\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT transferts_regroupement_depart INTO parametres") ;
		}

		//index Camille (comment ça encore ?)
		$rqt = "alter table coordonnees drop index i_num_entite";
		echo traite_rqt($rqt,"alter table coordonnees drop index i_num_entite");
		$rqt = "alter table coordonnees add index i_num_entite (num_entite)";
		echo traite_rqt($rqt,"alter table coordonnees add index i_num_entite (num_entite)");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.09");
		break;

	case "v5.09":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800");

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}

		//AR - On revoit une clé primaire
		$rqt ="alter table notices_fields_global_index drop primary key";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop primary key");
		$rqt ="alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)";
		echo traite_rqt($rqt,"alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)");

		//AR - ajout du partitionnement de manière systématique
		$rqt="show table status where name='notices_mots_global_index' or name='notices_fields_global_index'";
		$result = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->Create_options != "partitioned"){
					$rqt="alter table ".$row->Name." partition by key(code_champ,code_ss_champ) partitions 50";
					echo traite_rqt($rqt,"alter table ".$row->Name." partition by key");
				}
			}
		}

		// RFID: ajout de la gestion de l'antivol par afi
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='rfid_afi_security_codes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'pmb', 'rfid_afi_security_codes', '', '0', 'Gestion de l\'antivol par le registre AFI.\nLa première valeur est celle de l\'antivol actif, la deuxième est celle de l\antivol inactif.\nExemple: 07,C2  ') ";
			echo traite_rqt($rqt,"INSERT pmb_rfid_afi_security_codes INTO parametres") ;
		}

		// CMS: ajout de l'url de construction de l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='url_base_cms_build' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'pmb', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
			echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;
		}

		//AR - on stocke le double metaphone de chaque mot !
		$rqt = "alter table words add double_metaphone varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table words add double_metaphone");
		$rqt = "alter table words add stem varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table words add stem");
		//AR - Suggestions de mots dans la saisie en recherche simple
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='simple_search_suggestions' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','simple_search_suggestions','0','Activer la suggestion de mots en recherche simple via la complétion\n0 : Désactiver\n1 : Activer\n\nNB : Cette fonction nécessite l\'installation de l\'extension levenshtein dans MySQL','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_simple_search_suggestions into parametres") ;
		}

		//AR - Suggestions de mots dans la saisie en recherche simple
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='stemming_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','stemming_active','0','Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer\n','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_stemming_active into parametres") ;
		}

		$rqt = "delete from parametres where sstype_param like 'url_base_cms_build%' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
		VALUES (0, 'cms', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
		echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;

		//DG - Modification de la taille du champ content_infopage de la table infopages
		$rqt = "ALTER TABLE infopages MODIFY content_infopage longblob NOT NULL default ''";
		echo traite_rqt($rqt,"alter table infopages modify content_infopage");

		//DG - Modification du commentaire du paramètre pmb_blocage_delai
		$rqt = "UPDATE parametres SET comment_param = 'Délai à partir duquel le retard est pris en compte pour le blocage' where type_param = 'pmb' and sstype_param = 'blocage_delai'";
		echo traite_rqt($rqt,"update parametres pmb_blocage_delai");

		$rqt = "delete from parametres where sstype_param like 'url_base_cms_build%' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
		VALUES (0, 'cms', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
		echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;


		//index Camille (c'est que le début d'accord d'accord ?)
		$rqt = "alter table resa drop index i_idbulletin";
		echo traite_rqt($rqt,"alter table resa drop index i_idbulletin");
		$rqt = "alter table resa add index i_idbulletin (resa_idbulletin)";
		echo traite_rqt($rqt,"alter table resa add index i_idbulletin (resa_idbulletin)");

		$rqt = "alter table resa drop index i_idnotice";
		echo traite_rqt($rqt,"alter table resa drop index i_idnotice");
		$rqt = "alter table resa add index i_idnotice (resa_idnotice)";
		echo traite_rqt($rqt,"alter table resa add index i_idnotice (resa_idnotice)");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.10");
		break;

	case "v5.10":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//AR - ajout de type de contenu générique pour les articles et rubriques...
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='article_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'article_generic', editorial_type_label ='CP pour Article'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'article_generic'") ;
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'section_generic', editorial_type_label ='CP pour Rubrique'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'section_generic'") ;
		}

		//DG - Ajout du champ index_libelle dans la table frais
		$rqt = "ALTER TABLE frais ADD index_libelle TEXT";
		echo traite_rqt($rqt,"alter table frais add index_libelle");

		//DG - Paramètres pour les lettres de retard par groupe
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1before_list_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1before_list' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1before_list_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1before_list_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1after_list_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1after_list' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1after_list_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1after_list_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1fdp_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1fdp' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1fdp_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1fdp_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1madame_monsieur_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1madame_monsieur' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1madame_monsieur_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1madame_monsieur_group into parametres");
		}

		//DG - Impression du nom du groupe sur la lettre de rappel
		$rqt = "ALTER TABLE groupe ADD lettre_rappel_show_nomgroup INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_rappel_show_nomgroup default 0");
		$rqt = "update groupe set lettre_rappel_show_nomgroup=lettre_rappel ";
		echo traite_rqt($rqt,"update groupe set lettre_rappel_show_nomgroup=lettre_rappel");

		//AR - Ajout des extensions de formulaire pour les types de contenus
		$rqt = "alter table cms_editorial_types add editorial_type_extension text not null";
		echo traite_rqt($rqt,"alter table cms_editorial_types add editorial_type_extension");

		//AR - Ajout de la table de stockages des infos des extension
		$rqt = "create table cms_modules_extensions_datas (
			id_extension_datas int(10) not null auto_increment primary key,
			extension_datas_module varchar(255) not null default '',
			extension_datas_type varchar(255) not null default '',
			extension_datas_type_element varchar(255) not null default '',
			extension_datas_num_element int(10) not null default 0,
			extension_datas_datas blob
		)";
		echo traite_rqt($rqt,"create table cms_modules_extensions_datas");

		//NG - Ordre des facettes
		$rqt = "alter table facettes add facette_order int not null default 1";
		echo traite_rqt($rqt,"alter table facettes add facette_order");
		//NG - limit_plus des facettes
		$rqt = "alter table facettes add facette_limit_plus int not null default 0";
		echo traite_rqt($rqt,"alter table facettes add facette_limit_plus");

		//MB - Modification de l'identifiant 28 en 1 pour le trie car il est présent en double dans sort.xml
		$rqt = "update parametres set valeur_param=REPLACE(valeur_param, '_28', '_1') WHERE type_param='opac' AND sstype_param='default_sort' AND valeur_param REGEXP '_28[^0-9]|_28$'";
		echo traite_rqt($rqt,"update param opac_default_sort");

		//NG pb de placement de main_hors_footer et footer
		$rqt = "update cms_build set build_parent='main' where build_obj='main_header' or build_obj='main_hors_footer' or build_obj='footer' ";
		echo traite_rqt($rqt,"update cms_build set build_parent");

		//NG pb de placement des zones du contener
		$rqt = "update cms_build set build_child_before='', build_child_after='intro' where build_obj='main' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='main'");
		$rqt = "update cms_build set build_child_before='main', build_child_after='bandeau' where build_obj='intro' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='intro'");
		$rqt = "update cms_build set build_child_before='intro', build_child_after='bandeau_2' where build_obj='bandeau' ";
		echo traite_rqt($rqt,"update cms_build  where build_obj='bandeau'");
		$rqt = "update cms_build set build_child_before='bandeau', build_child_after='' where build_obj='bandeau_2' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='bandeau_2' ");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.11");
		break;

	case "v5.11":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//NG Ajout param opac_show_bandeau_2
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_bandeau_2' "))==0){
			$rqt = "select valeur_param from parametres where type_param= 'opac' and sstype_param='show_bandeaugauche' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'show_bandeau_2', '".addslashes($value_param)."', 'Affichage du bandeau_2 ? \n 0 : Non\n 1 : Oui', 'f_modules', 0) " ;
			echo traite_rqt($rqt,"insert opac_show_bandeau_2=opac_show_bandeaugauche into parametres");
		}

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		//NG ajout de field_position dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add field_position int not null default 1";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add field_position");

		//abacarisse en attente
		if (pmb_mysql_num_rows(pmb_mysql_query("select id_param from parametres where type_param= 'opac' and sstype_param='param_social_network' "))==0){
			//Ajout du paramètre de configuration de l'api addThis
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param ,comment_param ,section_param ,gestion) VALUES ('opac', 'param_social_network',
			'{
			\"token\":\"ra-4d9b1e202c30dea1\",
			\"version\":\"300\",
			\"buttons\":[
			{
			\"attributes\":{
			\"class\":\"addthis_button_facebook_like\",
			\"fb:like:layout\":\"button_count\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_tweet\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_counter addthis_button_compact\"
			}
			}
			],
			\"toolBoxParams\":{
			\"class\":\"addthis_toolbox addthis_default_style\"
			},
			\"addthis_share\":{

			},
			\"addthis_config\":{
			\"data_track_clickback\":\"true\",
			\"ui_click\":\"true\"
			}
			}
			', 'Tableau de paramètrage de l\'API de gestion des interconnexions aux réseaux sociaux.
			Au format JSON.
			Exemple :
			{
			\"token\":\"ra-4d9b1e202c30dea1\",
			\"version\":\"300\",
			\"buttons\":[
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_1\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_2\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_3\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_4\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_compact\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_counter addthis_bubble_style\"
			}
			}
			],
			\"toolBoxParams\":{
			\"class\":\"addthis_toolbox addthis_default_style addthis_32x32_style\"
			},
			\"addthis_share\":{

			},
			\"addthis_config\":{
			\"data_track_addressbar\":true
			}
			}', 'e_aff_notice', '0'
			)";
			echo traite_rqt($rqt,"insert opac_param_social_network into parametres");
		}

		// DG
		//ajout du champ groupe_lecteurs dans la table bannettes
		$rqt = "ALTER TABLE bannettes ADD groupe_lecteurs INT(8) UNSIGNED NOT NULL default 0";
		echo traite_rqt($rqt,"alter table bannettes add groupe_lecteurs");

		// JP
		$rqt = "update parametres set comment_param='Tri par défaut des recherches OPAC. Deux possibilités :\n- un seul tri par défaut de la forme c_num_6\n- plusieurs tris par défaut de la forme c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri si plusieurs' WHERE type_param='opac' AND sstype_param='default_sort'";
		echo traite_rqt($rqt,"update comment for param opac_default_sort");

		// Transfert: statut non pretable pour les expl en demande de transfert
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='pret_demande_statut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'transferts', 'pret_demande_statut', '0', '1', 'Appliquer ce statut avant la validation') ";
			echo traite_rqt($rqt,"INSERT transferts_pret_demande_statut INTO parametres") ;
		}

		// descriptors in DSI
		$rqt = "create table if not exists bannettes_descriptors(
			num_bannette int not null default 0,
			num_noeud int not null default 0,
			bannette_descriptor_order int not null default 0,
			primary key (num_bannette,num_noeud)
		)";
		echo traite_rqt($rqt,"create table bannettes_descriptors") ;

		//ajout du champ bannette_mail dans bannette_abon
		$rqt = "ALTER TABLE bannette_abon ADD bannette_mail varchar(255) not null default '' ";
		echo traite_rqt($rqt,"alter table bannette_abon add bannette_mail");

		//AR - on a vu un cas ou ca se passe mal dans la 5.10, par précaution, on répète!
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='article_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'article_generic', editorial_type_label ='CP pour Article'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'article_generic'") ;
		}
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='section_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'section_generic', editorial_type_label ='CP pour Rubrique'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'section_generic'") ;
		}

		//DG - Augmentation de la taille du champ mention_date de la table bulletins
		$rqt = "ALTER TABLE bulletins MODIFY mention_date varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table bulletins modify mention_date");

		//DG - parametre pour l'affichage des notices de bulletins dans la navigation a2z
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_show_bulletin_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'perio_a2z_show_bulletin_notice', '0', 'Affichage de la notice de bulletin dans le navigateur de périodiques', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_show_bulletin_notice=0 into parametres");
		}

		//DG - ajout d'un commentaire de gestion pour les suggestions
		$rqt = "ALTER TABLE suggestions ADD commentaires_gestion TEXT AFTER commentaires";
		echo traite_rqt($rqt,"alter table suggestions add commentaires_gestion");

		//NG - Champs perso author
		$rqt = "create table if not exists author_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table author_custom ");

		$rqt = "create table if not exists author_custom_lists (
			author_custom_champ int(10) unsigned NOT NULL default 0,
			author_custom_list_value varchar(255) default NULL,
			author_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (author_custom_champ),
			KEY editorial_champ_list_value (author_custom_champ,author_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists author_custom_lists ");

		$rqt = "create table if not exists author_custom_values (
			author_custom_champ int(10) unsigned NOT NULL default 0,
			author_custom_origine int(10) unsigned NOT NULL default 0,
			author_custom_small_text varchar(255) default NULL,
			author_custom_text text,
			author_custom_integer int(11) default NULL,
			author_custom_date date default NULL,
			author_custom_float float default NULL,
			KEY editorial_custom_champ (author_custom_champ),
			KEY editorial_custom_origine (author_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists author_custom_values ");

		//NG - Champs perso categ
		$rqt = "create table if not exists categ_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table categ_custom ");

		$rqt = "create table if not exists categ_custom_lists (
			categ_custom_champ int(10) unsigned NOT NULL default 0,
			categ_custom_list_value varchar(255) default NULL,
			categ_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (categ_custom_champ),
			KEY editorial_champ_list_value (categ_custom_champ,categ_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists categ_custom_lists ");

		$rqt = "create table if not exists categ_custom_values (
			categ_custom_champ int(10) unsigned NOT NULL default 0,
			categ_custom_origine int(10) unsigned NOT NULL default 0,
			categ_custom_small_text varchar(255) default NULL,
			categ_custom_text text,
			categ_custom_integer int(11) default NULL,
			categ_custom_date date default NULL,
			categ_custom_float float default NULL,
			KEY editorial_custom_champ (categ_custom_champ),
			KEY editorial_custom_origine (categ_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists categ_custom_values ");

		//NG - Champs perso publisher
		$rqt = "create table if not exists publisher_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table publisher_custom ");

		$rqt = "create table if not exists publisher_custom_lists (
			publisher_custom_champ int(10) unsigned NOT NULL default 0,
			publisher_custom_list_value varchar(255) default NULL,
			publisher_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (publisher_custom_champ),
			KEY editorial_champ_list_value (publisher_custom_champ,publisher_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists publisher_custom_lists ");

		$rqt = "create table if not exists publisher_custom_values (
			publisher_custom_champ int(10) unsigned NOT NULL default 0,
			publisher_custom_origine int(10) unsigned NOT NULL default 0,
			publisher_custom_small_text varchar(255) default NULL,
			publisher_custom_text text,
			publisher_custom_integer int(11) default NULL,
			publisher_custom_date date default NULL,
			publisher_custom_float float default NULL,
			KEY editorial_custom_champ (publisher_custom_champ),
			KEY editorial_custom_origine (publisher_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists publisher_custom_values ");

		//NG - Champs perso collection
		$rqt = "create table if not exists collection_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table collection_custom ");

		$rqt = "create table if not exists collection_custom_lists (
			collection_custom_champ int(10) unsigned NOT NULL default 0,
			collection_custom_list_value varchar(255) default NULL,
			collection_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (collection_custom_champ),
			KEY editorial_champ_list_value (collection_custom_champ,collection_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists collection_custom_lists ");

		$rqt = "create table if not exists collection_custom_values (
			collection_custom_champ int(10) unsigned NOT NULL default 0,
			collection_custom_origine int(10) unsigned NOT NULL default 0,
			collection_custom_small_text varchar(255) default NULL,
			collection_custom_text text,
			collection_custom_integer int(11) default NULL,
			collection_custom_date date default NULL,
			collection_custom_float float default NULL,
			KEY editorial_custom_champ (collection_custom_champ),
			KEY editorial_custom_origine (collection_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists collection_custom_values ");

		//NG - Champs perso subcollection
		$rqt = "create table if not exists subcollection_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table subcollection_custom ");

		$rqt = "create table if not exists subcollection_custom_lists (
			subcollection_custom_champ int(10) unsigned NOT NULL default 0,
			subcollection_custom_list_value varchar(255) default NULL,
			subcollection_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (subcollection_custom_champ),
			KEY editorial_champ_list_value (subcollection_custom_champ,subcollection_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists subcollection_custom_lists ");

		$rqt = "create table if not exists subcollection_custom_values (
			subcollection_custom_champ int(10) unsigned NOT NULL default 0,
			subcollection_custom_origine int(10) unsigned NOT NULL default 0,
			subcollection_custom_small_text varchar(255) default NULL,
			subcollection_custom_text text,
			subcollection_custom_integer int(11) default NULL,
			subcollection_custom_date date default NULL,
			subcollection_custom_float float default NULL,
			KEY editorial_custom_champ (subcollection_custom_champ),
			KEY editorial_custom_origine (subcollection_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists subcollection_custom_values ");

		//NG - Champs perso serie
		$rqt = "create table if not exists serie_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table serie_custom ");

		$rqt = "create table if not exists serie_custom_lists (
			serie_custom_champ int(10) unsigned NOT NULL default 0,
			serie_custom_list_value varchar(255) default NULL,
			serie_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (serie_custom_champ),
			KEY editorial_champ_list_value (serie_custom_champ,serie_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists serie_custom_lists ");

		$rqt = "create table if not exists serie_custom_values (
			serie_custom_champ int(10) unsigned NOT NULL default 0,
			serie_custom_origine int(10) unsigned NOT NULL default 0,
			serie_custom_small_text varchar(255) default NULL,
			serie_custom_text text,
			serie_custom_integer int(11) default NULL,
			serie_custom_date date default NULL,
			serie_custom_float float default NULL,
			KEY editorial_custom_champ (serie_custom_champ),
			KEY editorial_custom_origine (serie_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists serie_custom_values ");

		//NG - Champs perso tu
		$rqt = "create table if not exists tu_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table tu_custom ");

		$rqt = "create table if not exists tu_custom_lists (
			tu_custom_champ int(10) unsigned NOT NULL default 0,
			tu_custom_list_value varchar(255) default NULL,
			tu_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (tu_custom_champ),
			KEY editorial_champ_list_value (tu_custom_champ,tu_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists tu_custom_lists ");

		$rqt = "create table if not exists tu_custom_values (
			tu_custom_champ int(10) unsigned NOT NULL default 0,
			tu_custom_origine int(10) unsigned NOT NULL default 0,
			tu_custom_small_text varchar(255) default NULL,
			tu_custom_text text,
			tu_custom_integer int(11) default NULL,
			tu_custom_date date default NULL,
			tu_custom_float float default NULL,
			KEY editorial_custom_champ (tu_custom_champ),
			KEY editorial_custom_origine (tu_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists tu_custom_values ");

		//NG - Champs perso indexint
		$rqt = "create table if not exists indexint_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
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
		echo traite_rqt($rqt,"create table indexint_custom ");

		$rqt = "create table if not exists indexint_custom_lists (
			indexint_custom_champ int(10) unsigned NOT NULL default 0,
			indexint_custom_list_value varchar(255) default NULL,
			indexint_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (indexint_custom_champ),
			KEY editorial_champ_list_value (indexint_custom_champ,indexint_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists indexint_custom_lists ");

		$rqt = "create table if not exists indexint_custom_values (
			indexint_custom_champ int(10) unsigned NOT NULL default 0,
			indexint_custom_origine int(10) unsigned NOT NULL default 0,
			indexint_custom_small_text varchar(255) default NULL,
			indexint_custom_text text,
			indexint_custom_integer int(11) default NULL,
			indexint_custom_date date default NULL,
			indexint_custom_float float default NULL,
			KEY editorial_custom_champ (indexint_custom_champ),
			KEY editorial_custom_origine (indexint_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists indexint_custom_values ");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.12");
		break;

	case "v5.12":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//DG - parametre pour forcer l'exécution des procédures
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='procs_force_execution' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
		VALUES (0, 'pmb', 'procs_force_execution', '0', 'Permettre le forçage de l\'exécution des procédures', '',0) ";
			echo traite_rqt($rqt, "insert pmb_procs_force_execution=0 into parametres");
			$rqt = "update users set rights=rights+131072 where rights<131072 and userid=1 ";
			echo traite_rqt($rqt, "update users add editions forcing rights where super user ");
		}

		//NG - ajout facette en dsi
		$rqt = "ALTER TABLE bannettes ADD group_type int unsigned NOT NULL default 0 AFTER notice_tpl";
		echo traite_rqt($rqt,"alter table bannettes add group_type");

		$rqt = "CREATE TABLE if not exists bannette_facettes (
			num_ban_facette int unsigned NOT NULL default 0,
			ban_facette_critere int(5) not null default 0,
			ban_facette_ss_critere int(5) not null default 0,
			ban_facette_order int(1) not null default 0,
			KEY bannette_facettes_key (num_ban_facette,ban_facette_critere,ban_facette_ss_critere)) " ;
		echo traite_rqt($rqt,"CREATE TABLE bannette_facettes");

		//DB - L'authentification Digest impose une valeur, ce qui n'est pas le cas avec une authentification externe
		$rqt= "alter table empr add empr_digest varchar(255) not null default '' after empr_password";
		echo traite_rqt($rqt,"alter table empr add empr_digest");

		//AB
		$rqt = "UPDATE users SET value_deflt_relation=CONCAT(value_deflt_relation,'-up') WHERE value_deflt_relation!='' AND value_deflt_relation NOT LIKE '%-%'";
		echo traite_rqt($rqt, 'UPDATE users SET value_deflt_relation=CONCAT(value_deflt_relation,"-up")');

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.13");
		break;

	case "v5.13":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//AB parametre OPAC pour activer ou non le drag and drop si notice_depliable != 2
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='draggable' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'draggable', '1', 'Permet d\'activer le glisser déposer dans le panier pour l\'affichage des notices à l\'OPAC', 'e_aff_notice',0) ";
			echo traite_rqt($rqt, "insert opac_draggable=1 into parametres");
		}

		//DG - Modification de la longueur du champ description de la table opac_liste_lecture
		$rqt = "ALTER TABLE opac_liste_lecture MODIFY description TEXT ";
		echo traite_rqt($rqt,"alter table opac_liste_lecture modify description");

		//DB - Ajout d'un champ timestamp dans la table acces_user_2
		@pmb_mysql_query("describe acces_usr_2",$dbh);
		if (!pmb_mysql_error($dbh)) {
			$rqt = "ALTER IGNORE TABLE acces_usr_2 ADD updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ";
			echo traite_rqt($rqt,"alter table acces_usr_2 add field updated");
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.14");
		break;

	case "v5.14":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";

		// +-------------------------------------------------+
		// MB - Indexer la colonne num_renvoi_voir de la table noeuds
		$rqt = "ALTER TABLE noeuds DROP INDEX i_num_renvoi_voir";
		echo traite_rqt($rqt,"ALTER TABLE noeuds DROP INDEX i_num_renvoi_voir");
		$rqt = "ALTER TABLE noeuds ADD INDEX i_num_renvoi_voir (num_renvoi_voir)";
		echo traite_rqt($rqt,"ALTER TABLE noeuds ADD INDEX i_num_renvoi_voir (num_renvoi_voir)");

		$rqt="update parametres set comment_param='Liste des id de template de notice pour ajouter des onglets personnalisés en affichage de notice\nExemple: 1,3,ISBD,PUBLIC\nLe paramètre notices_format doit être à 0 pour placer ISBD et PUBLIC' where type_param='opac' and sstype_param='notices_format_onglets' ";
		echo traite_rqt($rqt,"update opac notices_format_onglets comments in parametres") ;

		$rqt = "update parametres set comment_param='0 : mode normal de recherche\n1 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche \n2 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche sans faire de recherche intermédaire'  where type_param='opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update opac_autolevel comments in parametres");


		//Création des tables pour le portfolio
		$rqt = "create table cms_collections (
			id_collection int unsigned not null auto_increment primary key,
			collection_title varchar(255) not null default '',
			collection_description text not null,
			collection_num_parent int not null default 0,
			collection_num_storage int not null default 0,
			index i_cms_collection_title(collection_title)
		)";
		echo traite_rqt($rqt,"create table cms_collections") ;
		$rqt = "create table cms_documents (
			id_document int unsigned not null auto_increment primary key,
			document_title varchar(255) not null default '',
			document_description text not null,
			document_filename varchar(255) not null default '',
			document_mimetype varchar(100) not null default '',
			document_filesize int not null default 0,
			document_vignette mediumblob not null default '',
			document_url text not null,
			document_path varchar(255) not null default '',
			document_create_date date not null default '0000-00-00',
			document_num_storage int not null default 0,
			document_type_object varchar(255) not null default '',
			document_num_object int not null default 0,
			index i_cms_document_title(document_title)
		)";
		echo traite_rqt($rqt,"create table cms_documents") ;
		$rqt = "create table storages (
			id_storage int unsigned not null auto_increment primary key,
			storage_name varchar(255) not null default '',
			storage_class varchar(255) not null default '',
			storage_params text not null,
			index i_storage_class(storage_class)
		)";
		echo traite_rqt($rqt,"create table storages") ;
		$rqt = "create table cms_documents_links (
			document_link_type_object varchar(255) not null default '',
			document_link_num_object int not null default 0,
			document_link_num_document int not null default 0,
			primary key(document_link_type_object,document_link_num_object,document_link_num_document)
		)";
		echo traite_rqt($rqt,"create table cms_documents_links") ;

		// FT - Ajout des paramètres pour forcer les tags meta pour les moteurs de recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_description' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_description','','Contenu du meta tag description pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_description");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_keywords' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_keywords','','Contenu du meta tag keywords pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_keywords");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_author' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_author','','Contenu du meta tag author pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_author");
		}

		//DG - autoriser le code HTML dans les cotes exemplaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='html_allow_expl_cote' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'html_allow_expl_cote', '0', 'Autoriser le code HTML dans les cotes exemplaires ? \n 0 : non \n 1', '',0) ";
			echo traite_rqt($rqt, "insert pmb_html_allow_expl_cote=0 into parametres");
		}

		//maj valeurs possibles pour empr_sort_rows
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n b: code-barres \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//DB - création table index pour le magasin rdf
		$rqt = "create table rdfstore_index (
					num_triple int(10) unsigned not null default 0,
					subject_uri text not null ,
					predicat_uri text not null ,
					num_object int(10) unsigned not null default 0 primary key,
					object_val text not null ,
					object_index text not null ,
					object_lang char(5) not null default ''
		) default charset=utf8 ";
		echo traite_rqt($rqt,"create table rdfstore_index");

		// MB - Création d'une table de cache pour les cadres du portail pour accélérer l'affichage
		$rqt = "DROP TABLE IF EXISTS cms_cache_cadres";
		echo traite_rqt($rqt,"DROP TABLE IF EXISTS cms_cache_cadres");
		$rqt = "CREATE TABLE  cms_cache_cadres (
			cache_cadre_hash VARCHAR( 32 ) NOT NULL,
			cache_cadre_type_content VARCHAR(30) NOT NULL,
			cache_cadre_create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			cache_cadre_content MEDIUMTEXT NOT NULL,
			PRIMARY KEY (  cache_cadre_hash, cache_cadre_type_content )
		);";
		echo traite_rqt($rqt,"CREATE TABLE  cms_cache_cadres");

		$rqt = "ALTER TABLE rdfstore_index ADD subject_type TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  subject_uri";
		echo traite_rqt($rqt,"alter table rdfstore_index add subject_type");

		// Info de réindexation
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base > Réindexer le magasin RDF</a></b> ") ;

		// AP - Ajout de l'ordre dans les rubriques et les articles
		$rqt = "ALTER TABLE cms_sections ADD section_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_sections add section_order");

		$rqt = "ALTER TABLE cms_articles ADD article_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_articles add article_order");

		//DG - CSS add on en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='default_style_addon' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles déjà incluses ?\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}', '',0) ";
			echo traite_rqt($rqt, "insert pmb_default_style_addon into parametres");
		}

		// NG - circulation sans retour
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_no_ret INT UNSIGNED not null default 0";
		echo traite_rqt($rqt,"alter table serialcirc add serialcirc_no_ret");

		// NG - personnalisation d'impression de la liste de circulation des périodiques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_subst' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','serialcirc_subst','','Nom du fichier permettant de personnaliser l\'impression de la liste de circulation des périodiques','',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres pmb_serialcirc_subst");
		}

		//MB - Augmenter la taille du libellé de groupe
		$rqt = "ALTER TABLE groupe CHANGE libelle_groupe libelle_groupe VARCHAR(255) NOT NULL";
		echo traite_rqt($rqt,"alter table groupe");

		//AR - Ajout d'un type de cache pour un cadre
		$rqt = "alter table cms_cadres add cadre_modcache varchar(255) not null default 'get_post_view'";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_modcache");

		//DG - Type de relation par défaut en création de périodique
		$rqt = "ALTER TABLE users ADD value_deflt_relation_serial VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_serial after value_deflt_relation");

		//DG - Type de relation par défaut en création de bulletin
		$rqt = "ALTER TABLE users ADD value_deflt_relation_bulletin VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_bulletin after value_deflt_relation_serial");

		//DG - Type de relation par défaut en création d'article
		$rqt = "ALTER TABLE users ADD value_deflt_relation_analysis VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_analysis after value_deflt_relation_bulletin");

		//DG - Mise à jour des valeurs en fonction du type de relation par défaut en création de notice, si la valeur est vide !
		if ($res = pmb_mysql_query("select userid, value_deflt_relation,value_deflt_relation_serial,value_deflt_relation_bulletin,value_deflt_relation_analysis from users")){
			while ( $row = pmb_mysql_fetch_object($res)) {
				if ($row->value_deflt_relation_serial == '') pmb_mysql_query("update users set value_deflt_relation_serial='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_bulletin == '') pmb_mysql_query("update users set value_deflt_relation_bulletin='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_analysis == '') pmb_mysql_query("update users set value_deflt_relation_analysis='".$row->value_deflt_relation."' where userid=".$row->userid);
			}
		}

		//DG - Activer le prêt court par défaut
		$rqt = "ALTER TABLE users ADD deflt_short_loan_activate INT(1) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_short_loan_activate");

		//DG - Alerter l'utilisateur par mail des nouvelles inscriptions en OPAC ?
		$rqt = "ALTER TABLE users ADD user_alert_subscribemail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_demandesmail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_subscribemail default 0");

		//DB - Modification commentaire autolevel
		$rqt = "update parametres set comment_param='0 : mode normal de recherche.\n1 : Affiche le résultat de la recherche tous les champs après calcul du niveau 1 de recherche.\n2 : Affiche directement le résultat de la recherche tous les champs sans passer par le calcul du niveau 1 de recherche.' where type_param= 'opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update parameter comment for opac_autolevel2");

		//AR - Ajout du paramètres pour la durée de validité du cache des cadres du potail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='cache_ttl' "))==0){
			$rqt = "insert into parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'cms', 'cache_ttl', '1800', 'durée de vie du cache des cadres du portail (en secondes)', '',0) ";
			echo traite_rqt($rqt, "insert cms_caches_ttl into parametres");
		}

		//DG - Périodicité : Jour du mois
		$rqt = "ALTER TABLE planificateur ADD perio_jour_mois VARCHAR( 128 ) DEFAULT '*' AFTER perio_minute";
		echo traite_rqt($rqt,"ALTER TABLE planificateur ADD perio_jour_mois DEFAULT * after perio_minute");

		//DG - Replanifier la tâche en cas d'échec
		$rqt = "alter table taches_type add restart_on_failure int(1) UNSIGNED DEFAULT 0 NOT NULL";
		echo traite_rqt($rqt,"alter table taches_type add restart_on_failure");

		//DG - Alerte mail en cas d'échec de la tâche
		$rqt = "alter table taches_type add alert_mail_on_failure VARCHAR(255) DEFAULT ''";
		echo traite_rqt($rqt,"alter table taches_type add alert_mail_on_failure");

		//DG - Préremplissage de la vignette des dépouillements
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serial_thumbnail_url_article' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'serial_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice mère en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert pmb_serial_thumbnail_url_article=0 into parametres");
		}

		//DG - Délai en millisecondes entre les mails envoyés lors d'un envoi groupé
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_delay' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','mail_delay','0','Temps d\'attente en millisecondes entre chaque mail envoyé lors d\'un envoi groupé. \n 0 : Pas d\'attente', '',0)" ;
			echo traite_rqt($rqt,"insert pmb_mail_delay=0 into parametres") ;
		}

		//DG - Timeout cURL sur la vérifications des liens
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='curl_timeout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','curl_timeout','5','Timeout cURL (en secondes) pour la vérification des liens', '',1)" ;
			echo traite_rqt($rqt,"insert pmb_curl_timeout=0 into parametres") ;
		}

		//DG - Autoriser la prolongation groupée pour tous les membres
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='allow_prolong_members_group' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'empr', 'allow_prolong_members_group', '0', 'Autoriser la prolongation groupée des adhésions des membres d\'un groupe ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert empr_allow_prolong_members_group=0 into parametres");
		}


		//DB - ajout d'un index stem+lang sur la table words
		$rqt = "alter table words add index i_stem_lang(stem, lang)";
		echo traite_rqt($rqt, "alter table words add index i_stem_lang");

		//NG - Autoindex
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_notice_fields' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_notice_fields', '', 'Liste des champs de notice à utiliser pour l\'indexation automatique, séparés par une virgule.\nLes noms des champs sont les identifiants des champs listés dans le fichier XML pmb/notice/notice.xml\nExemple: tit1,n_resume', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_notice_fields='' into parametres");
		}

		//NG - Autoindex: surchage du parametrage de la recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_search_param' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_search_param', '', 'Surchage des paramètres de recherche de l\'indexation automatique.\n\nSyntaxe: param=valeur;\n\nListe des paramètres:\nautoindex_max_up_distance,\nautoindex_max_down_distance,\nautoindex_stem_ratio,\nautoindex_see_also_ratio,\nautoindex_max_down_ratio,\nautoindex_max_up_ratio,\nautoindex_deep_ratio,\nautoindex_distance_ratio,\nmax_relevant_words,\nmax_relevant_terms', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_search_param='' into parametres");
		}

		//DG - Choix par défaut pour la prolongation des lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='abonnement_default_debit' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'empr', 'abonnement_default_debit', '0', 'Choix par défaut pour la prolongation des lecteurs. \n 0 : Ne pas débiter l\'abonnement \n 1 : Débiter l\'abonnement sans la caution \n 2 : Débiter l\'abonnement et la caution') " ;
			echo traite_rqt($rqt,"insert empr_abonnement_default_debit = 0 into parametres");
		}

		//NG - Ajout indexation_lang dans la table notices
		$rqt = "ALTER TABLE notices ADD indexation_lang VARCHAR( 20 ) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE notices ADD indexation_lang VARCHAR( 20 ) NOT NULL DEFAULT '' ");

		$rqt = "alter table users add xmlta_indexation_lang varchar(10) NOT NULL DEFAULT '' after deflt_integration_notice_statut";
		echo traite_rqt($rqt,"alter table users add xmlta_indexation_lang");

		//NG - Ajout ico_notice
		$rqt = "ALTER TABLE connectors_sources ADD ico_notice VARCHAR( 255 ) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD ico_notice VARCHAR( 255 ) NOT NULL DEFAULT '' ");

		//NG - liste des sources externes d'enrichissements à intégrer dans le a2z
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'perio_a2z_enrichissements', '0', 'Affichage de sources externes d\'enrichissement dans le navigateur de périodiques.\nListe des couples (séparé par une virgule) Id de connecteur, Id de source externe d\'enrichissement, séparé par un point virgule\nExemple:\n6,4;6,5', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_enrichissements=0 into parametres");
		}

		//DG - Modification taille du champ empr_msg de la table empr
		$rqt = "ALTER TABLE empr MODIFY empr_msg TEXT null " ;
		echo traite_rqt($rqt,"alter table empr modify empr_msg");

		//DG - Identifiant du template de notice par défaut en impression de panier
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_template_default' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'print_template_default', '0', 'En impression de panier, identifiant du template de notice utilisé par défaut. Si vide ou à 0, le template classique est utilisé', 'a_general', 0)";
			echo traite_rqt($rqt,"insert opac_print_template_default='0' into parametres");
		}

		//DG - Paramètre pour afficher le permalink de la notice dans le detail de la notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_permalink' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'show_permalink', '0', 'Afficher le lien permanent de l\'OPAC en gestion ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_show_permalink=0 into parameters");
		}

		//AB - Ajout du champ pour choix d'un template d'export pour les flux RSS
		$rqt = "ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0 ");

		//DG - Parametre pour afficher ou non l'emprunteur précédent dans la fiche exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_show_lastempr' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'expl_show_lastempr', '1', 'Afficher l\'emprunteur précédent sur la fiche exemplaire ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_expl_show_lastempr=1 into parameters");
		}

		// NG - Gestion de caisses
		$rqt = "CREATE TABLE cashdesk (
			cashdesk_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			cashdesk_name VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_autorisations VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_transactypes VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_cashbox INT UNSIGNED NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk");

		$rqt = "CREATE TABLE cashdesk_locations (
			cashdesk_loc_cashdesk_num  INT UNSIGNED NOT NULL default 0,
			cashdesk_loc_num  INT UNSIGNED NOT NULL default 0,
			PRIMARY KEY(cashdesk_loc_cashdesk_num,cashdesk_loc_num)
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk_locations");

		$rqt = "CREATE TABLE cashdesk_sections (
			cashdesk_section_cashdesk_num  INT UNSIGNED NOT NULL default 0,
			cashdesk_section_num  INT UNSIGNED NOT NULL default 0,
			PRIMARY KEY(cashdesk_section_cashdesk_num,cashdesk_section_num)
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk_sections");

		// NG - Gestion de type de transactions
		$rqt = "CREATE TABLE  transactype (
			transactype_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			transactype_name VARCHAR(255) NOT NULL DEFAULT '',
			transactype_quick_allowed INT UNSIGNED NOT NULL default 0,
			transactype_unit_price FLOAT NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE transactype");

		// NG - Mémorisation du payement des transactions
		$rqt = "CREATE TABLE transacash (
			transacash_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			transacash_empr_num INT UNSIGNED NOT NULL default 0,
			transacash_desk_num INT UNSIGNED NOT NULL default 0,
			transacash_user_num INT UNSIGNED NOT NULL default 0,
			transacash_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			transacash_sold FLOAT NOT NULL default 0,
			transacash_collected FLOAT NOT NULL default 0,
			transacash_rendering FLOAT NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE transacash");

		// NG - Activer la gestion de caisses en gestion financière
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='gestion_financiere_caisses' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'gestion_financiere_caisses', '0', 'Activer la gestion de caisses en gestion financière? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_gestion_financiere_caisses=0 into parameters");
		}

		$rqt = "ALTER TABLE transactions ADD transactype_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD transactype_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "ALTER TABLE transactions ADD cashdesk_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD cashdesk_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "ALTER TABLE transactions ADD transacash_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD transacash_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "alter table users add deflt_cashdesk int NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table users add deflt_cashdesk");

		$rqt= "alter table sessions add notifications text";
		echo traite_rqt($rqt,"alter table sessions add notifications");

		// AP - Ajout du paramètre de segmentation des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='diarization_docnum' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','diarization_docnum',0,'Activer la segmentation des documents numériques vidéo ou audio 0 : non activée 1 : activée','',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres diarization_docnum");
		}

		// AP - Ajout de la table explnum_speakers
		$rqt = "CREATE TABLE explnum_speakers (
			explnum_speaker_id int unsigned not null auto_increment primary key,
			explnum_speaker_explnum_num int unsigned not null default 0,
			explnum_speaker_speaker_num varchar(10) not null default '',
			explnum_speaker_gender varchar(1) default '',
			explnum_speaker_author int unsigned not null default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_speakers");
		$rqt = "alter table explnum_speakers drop index i_ensk_explnum_num";
		echo traite_rqt($rqt,"alter table explnum_speakers drop index i_ensk_explnum_num");
		$rqt = "alter table explnum_speakers add index i_ensk_explnum_num(explnum_speaker_explnum_num)";
		echo traite_rqt($rqt,"alter table explnum_speakers add index i_ensk_explnum_num");
		$rqt = "alter table explnum_speakers drop index i_ensk_author";
		echo traite_rqt($rqt,"alter table explnum_speakers drop index i_ensk_author");
		$rqt = "alter table explnum_speakers add index i_ensk_author(explnum_speaker_author)";
		echo traite_rqt($rqt,"alter table explnum_speakers add index i_ensk_author");


		// AP - Ajout de la table explnum_segments
		$rqt = "CREATE TABLE  explnum_segments (
			explnum_segment_id int unsigned not null auto_increment primary key,
			explnum_segment_explnum_num int unsigned not null default 0,
			explnum_segment_speaker_num int unsigned not null default 0,
			explnum_segment_start double not null default 0,
			explnum_segment_duration double not null default 0,
			explnum_segment_end double not null default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_segments");
		$rqt = "alter table explnum_segments drop index i_ensg_explnum_num";
		echo traite_rqt($rqt,"alter table explnum_segments drop index i_ensg_explnum_num");
		$rqt = "alter table explnum_segments add index i_ensg_explnum_num(explnum_segment_explnum_num)";
		echo traite_rqt($rqt,"alter table explnum_segments add index i_ensg_explnum_num");
		$rqt = "alter table explnum_segments drop index i_ensg_speaker";
		echo traite_rqt($rqt,"alter table explnum_segments drop index i_ensg_speaker");
		$rqt = "alter table explnum_segments add index i_ensg_speaker(explnum_segment_speaker_num)";
		echo traite_rqt($rqt,"alter table explnum_segments add index i_ensg_speaker");

		//DG - Modification de l'emplacement du paramètre bannette_notices_template dans la zone DSI
		$rqt = "update parametres set type_param='dsi',section_param='' where type_param='opac' and sstype_param='bannette_notices_template' ";
		echo traite_rqt($rqt,"update parametres set bannette_notices_template");

		//DG - Retour à la précédente forme de tri
		$rqt = "update parametres set comment_param='Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))' WHERE type_param='opac' AND sstype_param='default_sort'";
		echo traite_rqt($rqt,"update comment for param opac_default_sort");

		//DG - Mode d'application d'un tri - Liste de tris pré-enregistrés
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_list' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_list', '0 d_num_6,c_text_28;d_text_7', 'Afficher la liste déroulante de sélection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_list = 0 d_num_6,c_text_28;d_text_7 into parametres");
	 	}

	 	//DG - Afficher le libellé du tri appliqué par défaut en résultat de recherche
	 	if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_display' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_display', '0', 'Afficher le libellé du tri appliqué par défaut en résultat de recherche ? \n 0 : Non \n 1 : Oui','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_display = 0 into parametres");
	 	}

		// NG - Affichage des bannettes privées en page d'accueil de l'Opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_bannettes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','show_bannettes','0','Affichage des bannettes en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_bannettes into parametres") ;
		}

		// AB - Affichage des facettes en AJAX
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facettes_ajax' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','facettes_ajax','1','Charger les facettes en ajax\n0 : non\n1 : oui','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_facettes_ajax into parametres") ;
		}

		// DB - Modification index sur table notices_mots_global_index
		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800", $dbh);
		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		$rqt = 'alter table notices_mots_global_index drop primary key';
		echo traite_rqt($rqt, 'alter table notices_mots_global_index drop primary key');
		$rqt = 'alter table notices_mots_global_index add primary key (id_notice, code_champ, num_word, position, code_ss_champ)';
		echo traite_rqt($rqt, 'alter table notices_mots_global_index add primary key');

		//AB
		$rqt = "ALTER TABLE cms_build drop INDEX cms_build_index";
		echo traite_rqt($rqt,"alter cms_build drop index cms_build_index ");
		$rqt = "ALTER TABLE cms_build ADD INDEX cms_build_index (build_version_num , build_obj)";
		echo traite_rqt($rqt,"alter cms_build add index cms_build_index ON build_version_num , build_obj");

		// AR - Paramètres pour ne pas prendre en compte les mots vides en tous les champs à l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_all_keep_empty_words' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','search_all_keep_empty_words','1','Conserver les mots vides pour les autorités dans la recherche tous les champs\n0 : non\n1 : oui','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_all_keep_empty_words into parametres") ;
		}

		// NG - Paramètre pour activer le piège en prêt si l'emprunteur a déjà emprunté l'exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_already_loaned' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'pmb','pret_already_loaned','0','Activer le piège en prêt si le document a déjà été emprunté par le lecteur. Nécessite l\'activation de l\'archivage des prêts\n0 : non\n1 : oui','',0)" ;
			echo traite_rqt($rqt,"insert pmb_pret_already_loaned into parametres") ;
		}

		//DB - Ajout d'index
		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800", $dbh);

		$rqt = "alter table abts_abts drop index i_date_fin";
		echo traite_rqt($rqt,"alter table abts_abts drop index i_date_fin");
		$rqt = "alter table abts_abts add index i_date_fin (date_fin)";
		echo traite_rqt($rqt,"alter table abts_abts add index i_date_fin");

		$rqt = "alter table cms_editorial_types drop index i_editorial_type_element";
		echo traite_rqt($rqt,"alter table cms_editorial_types drop index i_editorial_type_element");
		$rqt = "alter table cms_editorial_types add index i_editorial_type_element (editorial_type_element)";
		echo traite_rqt($rqt,"alter table cms_editorial_types add index i_editorial_type_element");

		$rqt = "alter table cms_editorial_custom drop index i_num_type";
		echo traite_rqt($rqt,"alter table cms_editorial_custom drop index i_num_type");
		$rqt = "alter table cms_editorial_custom add index i_num_type (num_type)";
		echo traite_rqt($rqt,"alter table cms_editorial_custom add index i_num_type");

		$rqt = "alter table cms_build drop index i_build_parent_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_parent_build_version_num");
		$rqt = "alter table cms_build add index i_build_parent_build_version_num (build_parent,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_parent_build_version_num");

		$rqt = "alter table cms_build drop index i_build_type_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_type_build_version_num");
		$rqt = "alter table cms_build add index i_build_parent_build_version_num (build_type,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_type_build_version_num");

		$rqt = "alter table cms_build drop index i_build_obj_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_obj_build_version_num");
		$rqt = "alter table cms_build add index i_build_obj_build_version_num (build_obj,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_obj_build_version_num");

		$rqt = "alter table notices_fields_global_index drop index i_code_champ_code_ss_champ";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop index i_code_champ_code_ss_champ");
		$rqt = "alter table notices_fields_global_index add index i_code_champ_code_ss_champ (code_champ,code_ss_champ)";
		echo traite_rqt($rqt,"alter table notices_fields_global_index add index i_code_champ_code_ss_champ");

		$rqt = "alter table notices_mots_global_index drop index i_code_champ_code_ss_champ_num_word";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_code_champ_code_ss_champ_num_word");
		$rqt = "alter table notices_mots_global_index add index i_code_champ_code_ss_champ_num_word (code_champ,code_ss_champ,num_word)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_code_champ_code_ss_champ_num_word");

		// Activation des recherches exemplaires voisins
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_voisin_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_voisin_search', '0', 'Activer la recherche des exemplaires dont la cote est proche:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_voisin_search='0' into parametres ");
		}

		// MHo - Paramètre pour indiquer le nombre de notices similaires à afficher à l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='nb_notices_similaires' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'nb_notices_similaires', '6', 'Nombre de notices similaires affichées lors du dépliage d\'une notice.\nValeur max = 6.','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_nb_notices_similaires='6' into parametres");
		}
		// MHo - Paramètre pour rendre indépendant l'affichage réduit des notices similaires par rapport aux notices pliées
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notice_reduit_format_similaire' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'notice_reduit_format_similaire', '1', 'Format d\'affichage des réduits de notices similaires :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_notice_reduit_format_similaire='0' into parametres");
		}

		//AR - Paramètres d'écretage des résultats de recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_noise_limit_type' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_noise_limit_type='0' into parametres ");
		}

		//AR - Prise en compte de la fréquence d'apparition d'un mot dans le fonds pour le calcul de pertinence
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_relevant_with_frequency' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_relevant_with_frequency='0' into parametres ");
		}

		//DG - Calcul de la prolongation d'adhésion à partir de la date de fin d'adhésion ou la date du jour
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='prolong_calc_date_adhes_depassee' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'empr', 'prolong_calc_date_adhes_depassee', '0', 'Si la date d\'adhésion est dépassée, le calcul de la prolongation se fait à partir de :\n 0 : la date de fin d\'adhésion\n 1 : la date du jour','',0) " ;
			echo traite_rqt($rqt,"insert empr_prolong_calc_date_adhes_depassee = 0 into parametres");
		}

		//DG - Modification du commentaire du paramètre pmb_notice_reduit_format pour les améliorations
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice' where type_param='pmb' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre pmb_notice_reduit_format");

		//DG - Périodicité d'envoi par défaut en création de bannette privée (en jours)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='bannette_priv_periodicite' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'bannette_priv_periodicite', '15', 'Périodicité d\'envoi par défaut en création de bannette privée (en jours)','l_dsi',0) " ;
			echo traite_rqt($rqt,"insert opac_bannette_priv_periodicite = 15 into parametres");
		}

		//DG - Modification du commentaire opac_notices_format
		$rqt = "update parametres set comment_param='Format d\'affichage des notices en résultat de recherche \n 0 : Utiliser le paramètre notices_format_onglets \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 5 : ISBD et Public avec ISBD en premier \n 8 : Réduit (titre+auteurs) seul' where type_param='opac' and sstype_param='notices_format'" ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_notices_format") ;


		//DB - Modifications et ajout de commentaires pour les paramètres décrivant l'autoindexation
		$rqt = "UPDATE parametres SET valeur_param=replace(valeur_param,',',';'), comment_param = 'Liste des champs de notice à utiliser pour l\'indexation automatique.\n\n";
		$rqt.= "Syntaxe: nom_champ=poids_indexation;\n\n";
		$rqt.= "Les noms des champs sont ceux précisés dans le fichier XML \"pmb/includes/notice/notice.xml\"\n";
		$rqt.= "Le poids de l\'indexation est une valeur de 0.00 à 1. (Si rien n\'est précisé, le poids est de 1)\n\n";
		$rqt.= "Exemple :\n\n";
		$rqt.= "tit1=1.00;n_resume=0.5;' ";
		$rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_notice_fields' ";
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_notice_fields") ;

		$rqt = "UPDATE parametres SET comment_param = 'Surchage des paramètres de recherche de l\'indexation automatique.\n";
		$rqt.= "Syntaxe: param=valeur;\n\n";
		$rqt.= "Listes des parametres:\n\n";
		$rqt.= "max_relevant_words = 20 (nombre maximum de mots et de lemmes de la notice à prendre en compte pour le calcul)\n\n";
		$rqt.= "autoindex_deep_ratio = 0.05 (ratio sur la profondeur du terme dans le thésaurus)\n";
		$rqt.= "autoindex_stem_ratio = 0.80 (ratio de pondération des lemmes / aux mots)\n\n";
		$rqt.= "autoindex_max_up_distance = 2 (distance maximum de recherche dans les termes génériques du thésaurus)\n";
		$rqt.= "autoindex_max_up_ratio = 0.01 (pondération sur les termes génériques)\n\n";
		$rqt.= "autoindex_max_down_distance = 2 (distance maximum de recherche dans les termes spécifiques du thésaurus)\n";
		$rqt.= "autoindex_max_down_ratio = 0.01 (pondération sur les termes spécifiques)\n\n";
		$rqt.= "autoindex_see_also_ratio = 0.01 (surpondération sur les termes voir aussi du thésaurus)\n\n";
		$rqt.= "autoindex_distance_type = 1 (calcul de distance de 1 à 4)\n";
		$rqt.= "autoindex_distance_ratio = 0.50 (ratio de pondération sur la distance entre les mots trouvés et les termes d\'une expression du thésaurus)\n\n";
		$rqt.= "max_relevant_terms = 10 (nombre maximum de termes retournés)' ";
		$rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_search_param' ";
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_search_param") ;

		// MHo - Ajout des attributs de l'oeuvre dans la table des titres uniformes
		$rqt = "ALTER TABLE titres_uniformes ADD tu_num_author BIGINT(11) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_num_author");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_forme VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_forme");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_date VARCHAR(50) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_date");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_date_date DATE NOT NULL DEFAULT '0000-00-00' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_date_date");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_sujet VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_sujet");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_lieu VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_lieu");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_histoire TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_histoire");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_caracteristique TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_caracteristique");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_public VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_public");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_contexte TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_contexte");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_coordonnees VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_coordonnees");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_equinoxe VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_equinoxe");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_completude INT(2) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_completude");

		// AR - Retrait du paramètres juste commité : Activation des recherches exemplaires voisins
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='allow_voisin_search'";
		echo traite_rqt($rqt,"delete from parametres opac_allow_voisin_search");

		// AR - Modification du paramètre opac_allow_simili
		$rqt="update parametres set comment_param = 'Activer les recherches similaires sur une notice :\n0 : Non\n1 : Activer la recherche \"Dans le même rayon\" et \"Peut-être aimerez-vous\"\n2 : Activer seulement la recherche \"Dans le même rayon\"\n3 : Activer seulement la recherche \"Peut-être aimerez-vous\"', section_param = 'e_aff_notice' where type_param='opac' and sstype_param='allow_simili_search'";
		echo traite_rqt($rqt,"update parametres set opac_allow_simili_search");

		// NG - Affichage des bannettes en page d'accueil de l'Opac	selon la banette
		$rqt = "ALTER TABLE bannettes ADD bannette_opac_accueil INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add bannette_opac_accueil");

		// AR - DSI abonné en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_subscribed_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_subscribed_bannettes',0,'Affichage des bannettes auxquelles le lecteur est abonné en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_subscribed_bannettes=0 into parametres") ;
		}

		// AR - DSI publique sélectionné en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_public_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_public_bannettes',0,'Affichage des bannettes sélectionnées en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert show_public_bannettes=0 into parametres") ;
		}

		// AR - Retrait du paramètre perio_a2z_enrichissements, on ne l'a jamais utilisé car on a finalement ramené le paramétrage par un connecteur
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements'";
		echo traite_rqt($rqt,"delete from parametres opac_perio_a2z_enrichissements");

		//DG - Paramètre non utilisé
		$rqt = "delete from parametres where sstype_param='confirm_resa' and type_param='opac' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG - Paramètre non utilisé
		$rqt = "delete from parametres where sstype_param='authors_aut_rec_per_page' and type_param='opac' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		echo form_relance ("v5.15");
		break;

		case "v5.15":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// AB - Paramètre de modification du workflow d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='init_workflow' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES('demandes','init_workflow',0,'Initialisation du workflow de la demande.\n 0 : Validation avant tout\n 1 : Validation avant tout et attribution au validateur\n 2 : Attribution avant tout','',0)";
				echo traite_rqt($rqt,"insert demandes_init_workflow=0 into parametres") ;
			}

			// MHo - Paramètre pour automatiser ou non la création de notice lors de l'enregistrement d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='notice_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'notice_auto', '0', 'Création automatique de la notice de demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_notice_auto='0' into parametres");
			}

			// MHo - Paramètre pour la création par défaut d'une action lors de la validation d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='default_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'default_action', '1', 'Création par défaut d\'une action lors de la validation de la demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_default_action='1' into parametres");
			}

			// MHo - Ajout d'une colonne "origine" de l'utilisateur dans la table audit : 0 = gestion, 1 = opac
			$rqt = "ALTER TABLE audit ADD type_user INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter audit add type_user");

			// AR - Ajout d'une colonne pour stocker les actions autorisées par type de demande
			$rqt = "alter table demandes_type add allowed_actions text not null";
			echo traite_rqt($rqt,"alter table demandes_type add allowed_actions");

			//DG - Optimisation
			$rqt = "show fields from notices_fields_global_index";
			$res = pmb_mysql_query($rqt);
			$exists = false;
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					if($row->Field == "authority_num"){
						$exists = true;
						break;
					}
				}
			}
			if(!$exists){
				if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
					$rqt = "truncate table notices_fields_global_index";
					echo traite_rqt($rqt,"truncate table notices_fields_global_index");

					// Info de réindexation
					$rqt = " select 1 " ;
					echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
				}

				// JP - Synchronisation RDF
				$rqt = "ALTER TABLE notices_fields_global_index ADD authority_num VARCHAR(50) NOT NULL DEFAULT '0'";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add authority_num");
			}

			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='synchro_rdf' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'synchro_rdf', '0', 'Activer la synchronisation rdf\n 0 : non \n 1 : oui (l\'activation de ce paramètre nécessite une ré-indexation)','',0) " ;
				echo traite_rqt($rqt,"insert pmb_synchro_rdf = 0 into parametres");
			}

			// AB Modification de la valeur par défaut du parametre init_workflow
			$rqt="UPDATE parametres SET valeur_param='1' WHERE type_param='demandes' AND sstype_param='init_workflow'";
			echo traite_rqt($rqt,"update parametres set demandes_init_workflow=1");
			// AB Changement du type de champ pour date_note
			$rqt = "ALTER TABLE demandes_notes CHANGE date_note date_note DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"alter demandes_notes CHANGE date_note");
			// MHo - Ajout d'une colonne "notes_read_gestion" pour indiquer si une note a été lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add note_read_gestion");
			// MHo - Ajout d'une colonne "actions_read_gestion" pour indiquer si une action a été lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_actions ADD actions_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_actions add actions_read_gestion");
			// MHo - Ajout d'une colonne "dmde_read_gestion" pour indiquer si une demande contient des éléments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_gestion");

			// MHo - Ajout d'une colonne "reponse_finale" contenant la réponse finale qui sera intégrée à la faq
			$rqt = "ALTER TABLE demandes ADD reponse_finale TEXT NULL";
			echo traite_rqt($rqt,"alter demandes add reponse_finale");

			// DG - Le super user doit avoir accès à tous les établissements
			$rqt = "UPDATE entites SET autorisations=CONCAT(' 1', autorisations) WHERE type_entite='1' AND autorisations NOT LIKE '% 1 %'";
			echo traite_rqt($rqt, 'UPDATE entites SET autorisations=CONCAT(" 1",autorisations) for super user');

			// AR - Module FAQ - Paramètre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'faq' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'faq', 'active', '0', 'Module \'FAQ\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
				echo traite_rqt($rqt, "insert faq_active=0 into parameters");
			}

			// AR - Création de la table des types pour la FAQ
			$rqt = " CREATE TABLE faq_types(
				id_type int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_type varchar(255) NOT NULL default '',
	        	PRIMARY KEY  (id_type) )";
			echo traite_rqt($rqt,"CREATE TABLE faq_types") ;

			// AR - Création de la table des thèmes pour la FAQ
			$rqt = " CREATE TABLE faq_themes(
				id_theme int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_theme varchar(255) NOT NULL default '',
	    	    PRIMARY KEY  (id_theme))";
			echo traite_rqt($rqt,"CREATE TABLE faq_themes") ;

			// AR - Création de la table pour la FAQ
			$rqt = "create table faq_questions(
				id_faq_question int(10) unsigned not null auto_increment primary key,
				faq_question_num_type int(10) unsigned not null default 0,
				faq_question_num_theme int(10) unsigned not null default 0,
				faq_question_num_demande int(10) unsigned not null default 0,
				faq_question_question text not null,
				faq_question_question_userdate varchar(255) not null default '',
				faq_question_question_date datetime not null default '0000-00-00 00:00:00',
				faq_question_answer text not null,
				faq_question_answer_userdate varchar(255) not null default '',
				faq_question_answer_date datetime not null default '0000-00-00 00:00:00')";
			echo traite_rqt($rqt,"create table faq_questions");

			// AR - Création de la table de descripteurs pour la FAQ
			$rqt = "create table faq_questions_categories(
				num_faq_question int(10) unsigned not null default 0,
				num_categ int(10) unsigned not null default 0,
				index i_faq_categ(num_faq_question,num_categ))";
			echo traite_rqt($rqt,"create table faq_categories");

			// AR - Ajout de l'ordre dans la table de descripteurs pour la FAQ
			$rqt = "alter table faq_questions_categories add categ_order int(10) unsigned not null default 0";
			echo traite_rqt($rqt,"alter table faq_questions_categories add categ_order");

			// AR - Ajout d'un statut pour les questions de la FAQ (statut de publication 0/1)
			$rqt = "alter table faq_questions add faq_question_statut int(10) unsigned not null default 0";
			echo traite_rqt($rqt,"alter table faq_questions add faq_question_statut");

			// AR indexons correctement la FAQ - Table de mots
			$rqt = "create table if not exists faq_questions_words_global_index(
				id_faq_question int unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_faq_question,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_faq_question),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table faq_questions_words_global_index");

			// AR indexons correctement la FAQ - Table de champs
			$rqt = "create table if not exists faq_questions_fields_global_index(
				id_faq_question int unsigned not null default 0,
				code_champ int(3) unsigned not null default 0,
				code_ss_champ int(3) unsigned not null default 0,
				ordre int(4) unsigned not null default 0,
				value text not null,
				pond int(4) unsigned not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default 0,
				primary key(id_faq_question,code_champ,code_ss_champ,lang,ordre),
				index i_value(value(300)),
				index i_code_champ_code_ss_champ(code_champ,code_ss_champ))";
			echo traite_rqt($rqt,"create table faq_questions_fields_global_index ");

			// MHo - Renommage de la colonne "action_read" en "action_read_opac" : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_actions CHANGE actions_read actions_read_opac INT not null default 0";
			echo traite_rqt($rqt,"alter demandes_actions change actions_read actions_read_opac");

			// MHo - Ajout d'une colonne "dmde_read_opac" pour alerter à l'opac en cas d'éléments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_opac");

			// MHo - Ajout d'une colonne "notes_read_opac" pour alerter à l'opac en cas de nouveauté : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add notes_read_opac");

			// DB -Ajout d'une fonction spécifique pour génération de code-barres lecteurs
			$rqt = "update parametres set comment_param='Numéro de carte de lecteur automatique ?\n 0: Non (si utilisation de cartes pré-imprimées)\n";
			$rqt.= " 1: Oui, entièrement numérique\n 2,a,b,c: Oui, avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n";
			$rqt.= " 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/circ/empr' ";
			$rqt.= " where type_param='pmb' and sstype_param='num_carte_auto'";
			echo traite_rqt($rqt,"update parametre pmb_num_carte_auto ");

			// AB On augmente la taille des champs pour le num demandeur ....
			$rqt = "ALTER TABLE demandes CHANGE num_demandeur num_demandeur INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes change num_demandeur");
			$rqt = "ALTER TABLE demandes_actions CHANGE actions_num_user actions_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_actions change actions_num_user");
			$rqt = "ALTER TABLE demandes_notes CHANGE notes_num_user notes_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_notes change notes_num_user");

			//DB - Génération code-barres pour les inscritions Web
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_num_carte_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'opac', 'websubscribe_num_carte_auto', '', 'Numéro de carte de lecteur automatique ?\n 2,a,b,c: Oui avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/opac_css/circ/empr', 'f_modules', '0')" ;
				echo traite_rqt($rqt,"insert opac_websubscribe_num_carte_auto into parametres") ;
			}

			// AB
			$rqt = "CREATE TABLE IF NOT EXISTS onto_uri (
					uri_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
					uri VARCHAR(255) NOT NULL UNIQUE DEFAULT '' )";
			echo traite_rqt($rqt,"create table onto_uri") ;

			//DB - Génération de cartes lecteurs sur imprimante ticket
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_handler' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_handler', '', 'Gestionnaire d\'impression :\n\n 1 = script \"print_cb.php\"\n 2 = applet jzebra\n 3 = requête ajax','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_handler into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_name' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_name', '', 'Nom de l\'imprimante.','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_options into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_url' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_url', '', 'Adresse de l\'imprimante.','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_url into parametres");
			}

			// NG - Vignette de la notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_img_folder_id' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'pmb', 'notice_img_folder_id', '0', 'Identifiant du répertoire d\'upload des vignettes de notices', '', '0')" ;
				echo traite_rqt($rqt,"insert pmb_notice_img_folder_id into parametres") ;
			}

			//AR - On ajoute une colonne pour l'inscription en ligne à l'OPAC (pour conserver ce que l'on faisait)
			$rqt = "alter table empr add empr_subscription_action text";
			echo traite_rqt($rqt,"alter table empr add empr_subscription_action");

			//AR - Modification du paramètre opac_websubscribe_show
			$rqt = "update parametres set comment_param = 'Afficher la possibilité de s\'inscrire en ligne ?\n0: Non\n1: Oui\n2: Oui + proposition s\'incription sur les réservations/abonnements' where type_param='opac' and sstype_param = 'websubscribe_show'";
			echo traite_rqt($rqt,"update parametres opac_websubscribe_show");

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_template' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_template',0,'Choix du template d\'affichage des notices en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_template into parametres");
			}

			//AB comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_nb' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_nb',5,'Nombre de notices à afficher et à raffraichir en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_nb into parametres");
			}

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compare_notice_active' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','compare_notice_active',1,'Activer le comparateur de notices','c_recherche',0)";
				echo traite_rqt($rqt,"insert opac_compare_notice_active into parametres");
			}
			// NG - Transfert: mémorisation de la loc d'origine des exemplaires en transfert
			$rqt = "CREATE TABLE if not exists transferts_source (
				trans_source_numexpl INT UNSIGNED NOT NULL default 0 ,
				trans_source_numloc INT UNSIGNED NOT NULL default 0 ,
				PRIMARY KEY(trans_source_numexpl))";
			echo traite_rqt($rqt,"CREATE TABLE transferts_source ") ;

			// NG - Ajout dans les archives de prêt les localisations du pret et de la loc d'origine de l'exemplaire
			$rqt = "alter table pret_archive add arc_expl_location_retour INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_retour");
			$rqt = "alter table pret_archive add arc_expl_location_origine INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_origine");

			//DG - Augmentation de la taille du champ pour les équations
			$rqt = "ALTER TABLE equations MODIFY nom_equation TEXT NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE equations MODIFY nom_equation TEXT");

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.16");
			break;

		case "v5.16":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			// AR indexons correctement SKOS - Table de mots
			$rqt = "create table if not exists skos_words_global_index(
				id_item int unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_item,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_item),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table skos_words_global_index");

			// AR indexons correctement  SKOS - Table de champs
			$rqt = "create table if not exists skos_fields_global_index(
				id_item int unsigned not null default 0,
				code_champ int(3) unsigned not null default 0,
				code_ss_champ int(3) unsigned not null default 0,
				ordre int(4) unsigned not null default 0,
				value text not null,
				pond int(4) unsigned not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default 0,
				primary key(id_item,code_champ,code_ss_champ,lang,ordre),
				index i_value(value(300)),
				index i_code_champ_code_ss_champ(code_champ,code_ss_champ))";
			echo traite_rqt($rqt,"create table skos_fields_global_index ");

			//AB table de construction d'une vedette composée
			$rqt = "CREATE TABLE IF NOT EXISTS vedette_object (
						object_type int(3) unsigned NOT NULL DEFAULT 0,
						object_id int(11) unsigned NOT NULL DEFAULT 0,
						num_vedette int(11) unsigned NOT NULL DEFAULT 0,
						subdivision varchar(50) NOT NULL default '',
						position int(3) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (object_type, object_id, num_vedette, subdivision, position),
						INDEX i_vedette_object_object (object_type,object_id),
						INDEX i_vedette_object_vedette (num_vedette)
					) ";
			echo traite_rqt($rqt,"CREATE TABLE vedette_object") ;

			//AB table des identifiants de vedettes
			$rqt = "CREATE TABLE IF NOT EXISTS vedette (
						id_vedette int(11) unsigned NOT NULL AUTO_INCREMENT,
						label varchar(255) NOT NULL default '',
						PRIMARY KEY (id_vedette)
					) ";
			echo traite_rqt($rqt,"CREATE TABLE vedette") ;

			//AP ajout de la table index_concept
			$rqt = "CREATE TABLE IF NOT EXISTS index_concept (
					num_object INT UNSIGNED NOT NULL ,
					type_object INT UNSIGNED NOT NULL ,
					num_concept INT UNSIGNED NOT NULL ,
					order_concept INT UNSIGNED NOT NULL default 0 ,
					PRIMARY KEY(num_object, type_object, num_concept))";
			echo traite_rqt($rqt,"create table index_concept");

			//AP création de la table de lien entre vedettes et autorités
			$rqt = "CREATE TABLE if not exists vedette_link (
				num_vedette INT UNSIGNED NOT NULL ,
				num_object INT UNSIGNED NOT NULL ,
				type_object INT UNSIGNED NOT NULL ,
				PRIMARY KEY (num_vedette, num_object, type_object))";
			echo traite_rqt($rqt,"create table vedette_link");

			// AP script de vérification de saisie des autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='autorites_verif_js' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
						VALUES ( 'pmb', 'autorites_verif_js', '', 'Script de vérification de saisie des autorités','', 0)";
				echo traite_rqt($rqt,"insert autorites_verif_js into parametres");
			}

			//AB paramètre pour masquer/afficher la reservation par panier
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='resa_cart' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','resa_cart',1,'Paramètre pour masquer/afficher la reservation par panier\n0 : Non \n1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_resa_cart into parametres");
			}

			// AR - Report du paramètre activant le stemming en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_stemming_active' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_stemming_active',0,'Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_stemming_active=0 into parametres") ;
			}

			// AR - Report du paramètre excluant des champ dans la recherche tous les champs en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_exclude_fields' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_exclude_fields','','Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_exclude_fields into parametres") ;
			}

			//AR - Report du paramètre d'écretage des résultats de recherche en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_noise_limit_type' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_search_noise_limit_type='0' into parametres ");
			}

			//AR - Report de la prise en compte de la fréquence d'apparition d'un mot dans le fonds pour le calcul de pertinence en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_relevant_with_frequency' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_relevant_with_frequency='0' into parametres ");
			}

			//AR - Report du paramètre gérant la troncature À droite automatique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_term_troncat_search' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'allow_term_troncat_search', '0', 'Troncature à droite automatique :\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_allow_term_troncat_search='0' into parametres ");
			}

			//AR - Report du paramètre gérant la durée du cache des recherches
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_cache_duration' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_cache_duration', '0', 'Durée de validité (en secondes) du cache des recherches' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_cache_duration='0' into parametres ");
			}

			//DG - En impression de panier, imprimer les exemplaires est coché par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='print_expl_default' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coché par défaut \n 0 : Non \n 1 : Oui','',0) " ;
				echo traite_rqt($rqt,"insert pmb_print_expl_default = 0 into parametres");
			}


			//AR - Activation des concepts ou non
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_active' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_active', '0', 'Active ou non l\'utilisation des concepts:\n0 : Non\n1 : Oui', 'concepts', '0')";
				echo traite_rqt($rqt,"insert thesaurus_concepts_active='0' into parametres ");
			}

			//AP - Paramétrage de l'ordre d'affichage des concepts d'une notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_affichage_ordre' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_affichage_ordre into parametres ");
			}

			//AP - Paramétrage du mode d'affichage des concepts d'une notice (en ligne ou pas)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_concept_in_line' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_concept_in_line', '0', 'Affichage des catégories en ligne.\n 0 : Non.\n 1 : Oui.', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_concept_in_line into parametres ");
			}

			//AB Checkbox pour réafficher les notices dans chaque groupement ou pas
			$rqt = "ALTER TABLE bannettes ADD display_notice_in_every_group INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER group_pperso";
			echo traite_rqt($rqt,"alter table bannettes add display_notice_in_every_group");

			//NG - Autorités personalisées
			$rqt = "create table if not exists authperso (
				id_authperso int(10) unsigned NOT NULL auto_increment,
				authperso_name varchar(255) NOT NULL default '',
				authperso_notice_onglet_num  int unsigned not null default 0,
				authperso_isbd_script text not null,
				authperso_view_script text not null,
				authperso_opac_search int unsigned not null default 0,
				authperso_opac_multi_search int unsigned not null default 0,
				authperso_gestion_search int unsigned not null default 0,
				authperso_gestion_multi_search int unsigned not null default 0,
				authperso_comment text not null,
				PRIMARY KEY  (id_authperso)) ";
			echo traite_rqt($rqt,"create table authperso ");

			//NG - Champs perso des autorités personalisées
			$rqt = "create table if not exists authperso_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				custom_prefixe varchar(255) NOT NULL default '',
				num_type int unsigned not null default 0,
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
			echo traite_rqt($rqt,"create table authperso_custom ");

			$rqt = "create table if not exists authperso_custom_lists (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_list_value varchar(255) default NULL,
				authperso_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY editorial_custom_champ (authperso_custom_champ),
				KEY editorial_champ_list_value (authperso_custom_champ,authperso_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table if not exists authperso_custom_lists ");

			$rqt = "create table if not exists authperso_custom_values (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_origine int(10) unsigned NOT NULL default 0,
				authperso_custom_small_text varchar(255) default NULL,
				authperso_custom_text text,
				authperso_custom_integer int(11) default NULL,
				authperso_custom_date date default NULL,
				authperso_custom_float float default NULL,
				KEY editorial_custom_champ (authperso_custom_champ),
				KEY editorial_custom_origine (authperso_custom_origine)) " ;
			echo traite_rqt($rqt,"create table if not exists authperso_custom_values ");

			$rqt = "create table if not exists authperso_authorities (
				id_authperso_authority int(10) unsigned NOT NULL auto_increment,
				authperso_authority_authperso_num int(10) unsigned NOT NULL default 0 ,
				authperso_infos_global text not null,
				authperso_index_infos_global text not null,
				PRIMARY KEY  (id_authperso_authority))  " ;
			echo traite_rqt($rqt,"create table if not exists authperso_authorities ");

			$rqt = "create table if not exists notices_authperso (
				notice_authperso_notice_num int(10) unsigned NOT NULL default 0,
				notice_authperso_authority_num int(10) unsigned NOT NULL default 0,
				notice_authperso_order int(10) unsigned NOT NULL default 0,
				PRIMARY KEY  (notice_authperso_notice_num,notice_authperso_authority_num))  " ;
			echo traite_rqt($rqt,"create table if not exists notices_authperso ");

			// NG : Onglet personalisé de notice
			$rqt = "create table if not exists notice_onglet (
				id_onglet int(10) unsigned NOT NULL auto_increment,
				onglet_name varchar(255) default NULL,
				PRIMARY KEY  (id_onglet)) ";
			echo traite_rqt($rqt,"create table if not exists notice_onglet ");

			//DG - Personnalisation des colonnes pour l'affichage des états des collections
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_data' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/collstate.class.php\".','e_aff_notice',0)";
				echo traite_rqt($rqt,"insert opac_collstate_data = 0 into parametres");
			}

			//AB ajout d'un schema SKOS par défaut
			$rqt = "ALTER TABLE users ADD deflt_concept_scheme INT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_thesaurus";
			echo traite_rqt($rqt,"alter table users add deflt_concept_scheme");

			//AB paramètre caché pour conservation de la date de dernière modification de l'ontologie
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='ontology_filemtime' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('thesaurus','ontology_filemtime',0,'Paramètre caché pour conservation de la date de dernière modification de l\'ontologie','ontologie',1)";
				echo traite_rqt($rqt,"insert thesaurus_ontology_filemtime into parametres");
			}

			// NG - Ajout du champ resa_arc_trans pour associer un transfert à une archive résa
			$rqt = "ALTER TABLE transferts_demande ADD resa_arc_trans int(8) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter table transferts_demande add resa_arc_trans ");

			// NG - Ajout champ info dans les audits
			$rqt = "ALTER TABLE audit ADD info text NOT NULL";
			echo traite_rqt($rqt,"alter table audit add info ");

			// AP modification du paramètre de schema SKOS par défaut
			$rqt = "ALTER TABLE users CHANGE deflt_concept_scheme deflt_concept_scheme INT(3) NOT NULL DEFAULT -1";
			echo traite_rqt($rqt,"alter table users change deflt_concept_scheme");

			//DG - Statuts sur les documents numériques
			$rqt = "create table if not exists explnum_statut (
				id_explnum_statut smallint(5) unsigned not null auto_increment,
				gestion_libelle varchar(255) not NULL default '',
				opac_libelle varchar(255)  not NULL default '',
				class_html VARCHAR( 255 )  not NULL default '',
				explnum_visible_opac tinyint(1) NOT NULL default 1,
				explnum_visible_opac_abon tinyint(1) NOT NULL default 0,
				explnum_consult_opac tinyint(1) NOT NULL default 1,
				explnum_consult_opac_abon tinyint(1) NOT NULL default 0,
				explnum_download_opac tinyint(1) NOT NULL default 1,
				explnum_download_opac_abon tinyint(1) NOT NULL default 0,
				primary key(id_explnum_statut))";
			echo traite_rqt($rqt,"create table explnum_statut ");

			//DG - Statut "Sans statut particulier" ajouté par défaut
			$rqt = "insert into explnum_statut SET id_explnum_statut=1, gestion_libelle='Sans statut particulier',opac_libelle='', explnum_visible_opac='1' ";
			echo traite_rqt($rqt,"insert minimum into explnum_statut");

			//DG - Ajout d'un champ statut sur les documents numériques
			$rqt = "ALTER TABLE explnum ADD explnum_docnum_statut smallint(5) UNSIGNED NOT NULL DEFAULT 1 ";
			echo traite_rqt($rqt,"alter table explnum add explnum_docnum_statut ");

			//DG - Statut de document numérique par défaut en création de document numérique
			$rqt = "ALTER TABLE users ADD deflt_explnum_statut INT(6) UNSIGNED DEFAULT 1 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_explnum_statut ");

			//AR - paramétrages des droits d'accès sur les documents numériques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum' "))==0){
			$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum',0,'Gestion des droits d\'accès des emprunteurs aux documents numériques\n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum_def' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum_def',0,'Valeur par défaut en modification de document numérique pour les droits d\'accès emprunteurs - documents numériques\n0 : Recalculer.\n1 : Choisir.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum_def into parametres");
			}

			// NG - Ajout param transferts_retour_action_resa
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_action_resa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
				VALUES (0, 'transferts', 'retour_action_resa', '1', '1', 'Génére un transfert pour répondre à une réservation lors du retour de l\'exemplaire\n 0: Non\n 1: Oui') ";
				echo traite_rqt($rqt,"INSERT transferts_retour_action_resa INTO parametres") ;
			}

			//DG - Logs OPAC - Exclusion possible des robots et de certaines adresses IP
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='logs_exclude_robots' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'logs_exclude_robots', '1', 'Exclure les robots dans les logs OPAC ?\n 0: Non\n 1: Oui. \nFaire suivre d\'une virgule pour éventuellement exclure les logs OPAC provenant de certaines adresses IP, elles-mêmes séparées par des virgules (ex : 1,127.0.0.1,192.168.0.1).','',0)";
				echo traite_rqt($rqt,"insert pmb_logs_exclude_robots = 1 into parametres");
			}

			// NG - Auteurs répétables dans les titres uniformes
			$rqt = "CREATE TABLE if not exists responsability_tu (
					responsability_tu_author_num int unsigned NOT NULL default 0,
					responsability_tu_num int unsigned NOT NULL default 0,
					responsability_tu_fonction char(4) NOT NULL default '',
					responsability_tu_type int unsigned NOT NULL default 0,
					responsability_tu_ordre smallint(2) unsigned NOT NULL default 0,
					PRIMARY KEY  (responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction),
					KEY responsability_tu_author (responsability_tu_author_num),
					KEY responsability_tu_num (responsability_tu_num) )";
			echo traite_rqt($rqt,"CREATE TABLE responsability_tu ");
			// NG - migration de l'auteur de titre uniforme dans la table responsability_tu
			if ($res = pmb_mysql_query("select tu_num_author, tu_id from titres_uniformes where tu_num_author>0")){
				while ( $row = pmb_mysql_fetch_object($res)) {
					$rqt = "INSERT INTO responsability_tu set responsability_tu_author_num=".$row->tu_num_author.", responsability_tu_num= ".$row->tu_id."  ";
					pmb_mysql_query($rqt, $dbh);
				}
			}

			//NG - ajout pied de page dans la fiche de circulation
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_piedpage text NOT NULL AFTER serialcirc_tpl";
			echo traite_rqt($rqt,"alter table serialcirc add serialcirc_piedpage");

			//DG - Templates de listes de circulation
			$rqt = "CREATE TABLE serialcirc_tpl (
	 				serialcirctpl_id int(10) unsigned NOT NULL auto_increment,
	  				serialcirctpl_name varchar(255) NOT NULL DEFAULT '',
					serialcirctpl_comment varchar(255) NOT NULL DEFAULT '',
					serialcirctpl_tpl text NOT NULL,
	  				PRIMARY KEY  (serialcirctpl_id))";
			echo traite_rqt($rqt,"CREATE TABLE serialcirc_tpl") ;
			$rqt = "insert into serialcirc_tpl SET serialcirctpl_id=1, serialcirctpl_name='Template PMB', serialcirctpl_comment='', serialcirctpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
			echo traite_rqt($rqt,"insert minimum into serialcirc_tpl");

			//DG - Circulation des périodiques : Tri sur les destinataires
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_sort_diff text NOT NULL";
			echo traite_rqt($rqt,"alter table serialcirc add serialcirc_sort_diff");

			//NG - Templates de bannettes
			$rqt = "CREATE TABLE bannette_tpl (
					bannettetpl_id int(10) unsigned NOT NULL auto_increment,
					bannettetpl_name varchar(255) NOT NULL DEFAULT '',
					bannettetpl_comment varchar(255) NOT NULL DEFAULT '',
					bannettetpl_tpl text NOT NULL,
					PRIMARY KEY  (bannettetpl_id))";
			echo traite_rqt($rqt,"CREATE TABLE bannette_tpl") ;
			$rqt = "insert into bannette_tpl SET bannettetpl_id=1, bannettetpl_name='Template PMB', bannettetpl_comment='', bannettetpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
			echo traite_rqt($rqt,"insert minimum into bannette_tpl");

			//NG - Templates de bannettes
			$rqt = "ALTER TABLE bannettes ADD bannette_tpl_num INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER bannettes ADD bannette_tpl_num ");


			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.17");
			break;

		case "v5.17":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			// NG - Ajout paramètre pour activer la géolocalisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_activate', '0', 'Activation de la géolocalisation:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_map_activate into parametres");
			}

			//MB + CB - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas déjà (lié aux améliorations pour les transferts)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, transfert_statut_origine=expl_statut, update_date=update_date WHERE transfert_location_origine=0 AND transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine transfert_statut_origine");
			//NG - géolocalisation
			$rqt = "ALTER TABLE notices ADD map_echelle_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_echelle_num ");

			$rqt = "ALTER TABLE notices ADD map_projection_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_projection_num ");

			$rqt = "ALTER TABLE notices ADD map_ref_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_ref_num ");

			$rqt = "ALTER TABLE notices ADD map_equinoxe varchar(255) NOT NULL DEFAULT ''" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_equinoxe ");



			//NG - géolocalisation: Memo des emprises
			$rqt = "CREATE TABLE if not exists map_emprises (
					map_emprise_id int(10) unsigned NOT NULL auto_increment,
					map_emprise_type int(10) unsigned NOT NULL default 0,
					map_emprise_obj_num int(10) unsigned NOT NULL default 0,
					map_emprise_data GEOMETRY NOT NULL,
					map_emprise_order int(10) unsigned NOT NULL default 0,
					PRIMARY KEY  (map_emprise_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_emprises") ;

			//NG - géolocalisation: Echelles
			$rqt = "CREATE TABLE if not exists map_echelles (
					map_echelle_id int(10) unsigned NOT NULL auto_increment,
					map_echelle_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_echelle_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_echelles") ;

			//NG - géolocalisation: Système de projection du document
			$rqt = "CREATE TABLE if not exists map_projections (
					map_projection_id int(10) unsigned NOT NULL auto_increment,
					map_projection_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_projection_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_projections") ;

			//NG - géolocalisation: Systeme de référence de coord de la carte
			$rqt = "CREATE TABLE if not exists map_refs (
					map_ref_id int(10) unsigned NOT NULL auto_increment,
					map_ref_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_ref_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_refs") ;

			// AR - Ajout paramètre pour limiter le nombre d'emprises sur une carte !
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhaité par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_max_holds into parametres");
			}

			// AR - Les paramètres de cartes sont rangés ensemble !
			$rqt = "update parametres set section_param= 'map' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_max_holds");

			// AR - Définition de la couleur d'une emprise de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_record_color into parametres");
			}

			// AR - Définition de la couleur d'une emprise d'autorité
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_authority_color into parametres");
			}


			// AR - Définition du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_type into parametres");
			}
			// AR - Définition des paramètres du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_params into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en saisie de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_edition into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en résultat de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_result into parametres");
			}
			// NG - Ajout paramètre de la taille de la carte en visualisation de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_notice_view into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en édition de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_edition', '800*480', 'Taille de la carte en édition de notice','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_notice_edition into parametres");
			}

			// NG - cms: Ajout des vues opac
			$rqt = "ALTER TABLE cms ADD cms_opac_view_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER cms ADD cms_opac_view_num ");

			//DG - Modification taille du champ article_resume de la table cms_articles
			$rqt ="alter table cms_articles MODIFY article_resume MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_articles modify article_resume mediumtext");

			//DG - Modification taille du champ article_contenu de la table cms_articles
			$rqt ="alter table cms_articles MODIFY article_contenu MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_articles modify article_contenu mediumtext");

			//DG - Modification taille du champ section_resume de la table cms_sections
			$rqt ="alter table cms_sections MODIFY section_resume MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_sections modify section_resume mediumtext");

			//MB - Définition de la taille maximum des vignettes des notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_img_pics_max_size' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'notice_img_pics_max_size', '150', 'Taille maximale des vignettes uploadées dans les notices, en largeur ou en hauteur')";
				echo traite_rqt($rqt,"insert pmb_notice_img_pics_max_size='150' into parametres");
			}

			//DG - Vues OPAC dans les facettes
			$rqt = "show fields from facettes";
			$res = pmb_mysql_query($rqt);
			$exists = false;
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					if($row->Field == "facette_opac_views_num"){
						$exists = true;
						break;
					}
				}
			}
			if(!$exists){
				$rqt = "ALTER TABLE facettes ADD facette_opac_views_num text NOT NULL";
				echo traite_rqt($rqt,"alter table facettes add facette_opac_views_num");

				$req = "select id_facette, facette_opac_views_num from facettes";
				$res = pmb_mysql_query($req,$dbh);
				if ($res) {
					$facettes = array();
					while($row = pmb_mysql_fetch_object($res)) {
						$facettes[] = $row->id_facette;
					}
					if (count($facettes)) {
						$req = "select opac_view_id, opac_view_name from opac_views";
						$myQuery = pmb_mysql_query($req, $dbh);
						if ($myQuery) {
							$views = array();
							while ($row = pmb_mysql_fetch_object($myQuery)) {
								$v = array();
								$v["id"] = $row->opac_view_id;
								$v["name"] = $row->opac_view_name;
								$views[] = $v;
							}
							$param["selected"] = $facettes;
							$param=addslashes(serialize($param));
							foreach ($views as $view) {
								//Dans le cas où une modification a été faite avant le passage de la MAJ..
								$req = "delete from opac_filters where opac_filter_view_num=".$view["id"]." and opac_filter_path='facettes'";
								$res = pmb_mysql_query($req,$dbh);
								//Insertion..
								$rqt="insert into opac_filters set opac_filter_view_num=".$view["id"].",opac_filter_path='facettes', opac_filter_param='$param' ";
								echo traite_rqt($rqt,"insert authorization facettes into opac_filters view ".$view["name"]);
							}
						}
					}
				}
			}

			// NG - Ajout paramètre pour activer la géolocalisation en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_activate', '0', 'Activation de la géolocalisation:\n 0 : non \n 1 : oui','a_general', 0)";
				echo traite_rqt($rqt,"insert opac_map_activate into parametres");
			}

			//DB - commande psexec (planificateur sous windows)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='psexec_cmd' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'psexec_cmd', 'psexec -d', 'Paramètres de lancement de psexec (planificateur sous windows)\r\n\nAjouter l\'option -accepteula sur les versions les plus récentes. ', '',0) ";
				echo traite_rqt($rqt, "insert pmb_psexec_cmd into parameters");
			}

			// AR - Ajout paramètre pour activer l'éditeur Dojo
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='editorial_dojo_editor' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'editorial_dojo_editor', '1', 'Activation de l\'éditeur DoJo dans le contenu éditorial:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_editorial_dojo_editor into parametres");
			}

			// DG - Module "Surcharge de méta-données" : Groupes de méta-données par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from cms_managed_modules where managed_module_name= 'cms_module_metadatas' "))==0){
				$struct = array();
				$struct["metadatas1"] = array(
						'prefix' => "og",
						'name' => "Open Graph Protocol",
						'items' => array(
								'title' => array(
										'label' => "titre",
										'desc' => "Titre",
										'default_template' => "{{title}}"
								),
								'type' => array(
										'label' => "type",
										'desc' => "Type",
										'default_template' => "{{type}}"
								),
								'image' => array(
										'label' => "logo",
										'desc' => "Lien vers le logo",
										'default_template' => "{{logo_url}}"
								),
								'url' => array(
										'label' => "lien",
										'desc' => "Lien",
										'default_template' => "{{link}}"
								),
								'description' => array(
										'label' => "description",
										'desc' => "Résumé",
										'default_template' => "{{resume}}"
								),
								'locale' => array(
										'label' => "locale",
										'desc' => "Langue",
										'default_template' => ""
								),
								'site_name' => array(
										'label' => "site_name",
										'desc' => "Nom du site",
										'default_template' => ""
								),
						),
						'separator' => ":",
						'group_template' => "<meta property='{{key_metadata}}' content='{{value_metadata}}' />"
				);

				$struct["metadatas2"] = array(
						'prefix' => "twitter",
						'name' => "Twitter Cards",
						'items' => array(
								'title' => array(
										'label' => "titre",
										'desc' => "Titre",
										'default_template' => "{{title}}"
								),
								'card' => array(
										'label' => "card",
										'desc' => "Résumé",
										'default_template' => ""
								),
								'description' => array(
										'label' => "description",
										'desc' => "Description",
										'default_template' => "{{resume}}"
								),
								'image' => array(
										'label' => "logo",
										'desc' => "Lien vers le logo",
										'default_template' => "{{logo_url}}"
								),
								'site' => array(
										'label' => "site",
										'desc' => "Site",
										'default_template' => ""
								),
						),
						'separator' => ":",
						'group_template' => "<meta name='{{key_metadata}}' content='{{value_metadata}}' />"
				);
				$managed_datas = array();
				$managed_datas["module"]["metadatas"] = $struct;
				$managed_datas=addslashes(serialize($managed_datas));
				$rqt = "INSERT INTO cms_managed_modules ( managed_module_name, managed_module_box)
				VALUES ('cms_module_metadatas', '$managed_datas')";
				echo traite_rqt($rqt,"insert cms_module_metadatas into cms_managed_modules");
			}

			//DB Ajout vignette etageres (SDN)
			$rqt = "ALTER TABLE etagere ADD thumbnail_url MEDIUMBLOB NOT NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD thumbnail_url ");

			// AR - Ajout paramètre pour limiter le nombre d'emprises sur une carte à l'OPAC!
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhaité par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_max_holds into parametres");
			}

			// AR - Les paramètres de cartes sont rangés ensemble !
			$rqt = "update parametres set section_param= 'map', comment_param='Activation du géoréférencement' where type_param like 'opac' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update opac_map_activate");

			// AR - Changement de nom !
			$rqt = "update parametres set comment_param='Activation du géoréférencement' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_activate");

			// AR - Définition de la couleur d'une emprise de notice à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_record_color into parametres");
			}

			// AR - Définition de la couleur d'une emprise d'autorité à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_authority_color into parametres");
			}

			// AR - Ajout paramètre de la taille de la carte en saisie de recherche à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_edition into parametres");
			}

			// AR - Ajout paramètre de la taille de la carte en résultat de recherche à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_result into parametres");
			}
			// AR - Ajout paramètre de la taille de la carte en visualisation de notice à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_notice_view into parametres");
			}

			// AR - Définition du fond de carte à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_type into parametres");
			}
			// AR - Définition des paramètres du fond de carte à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_params into parametres");
			}

			// JP - Suggestions - Utilisateur : pouvoir être alerté en cas de nouvelle suggestion à l'OPAC
			$rqt = "ALTER TABLE users ADD user_alert_suggmail int(1) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter table users add user_alert_suggmail");

			// JP - Acquisitions - Sélection rubrique budgétaire en commande : pouvoir toutes les afficher
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='budget_show_all' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'acquisition', 'budget_show_all', '0', 'Sélection d\'une rubrique budgétaire en commande : toutes les afficher ?\n 0: Non (par pagination)\n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert budget_show_all = 0 into parametres");
			}


			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.18");
			break;

		case "v5.18":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//MB - Ajout index sur le nom des fichiers numériques pour accélérer la recherche
			$add_index=true;
			$req="SHOW INDEX FROM explnum";
			$res=pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)){
					if($ligne->Column_name == "explnum_nomfichier"){
						$add_index=false;
						break;
					}
				}
			}
			if($add_index){
				@set_time_limit(0);
				pmb_mysql_query("set wait_timeout=28800", $dbh);
				$rqt = "alter table explnum add index i_explnum_nomfichier(explnum_nomfichier(30))";
				echo traite_rqt($rqt,"alter table explnum add index i_explnum_nomfichier");
			}

			//JP - Ajout deux index sur les liens entre actes pour accélérer la recherche
			$rqt = "alter table liens_actes drop index i_num_acte";
			echo traite_rqt($rqt,"alter table liens_actes drop index i_num_acte");
			$rqt = "alter table liens_actes add index i_num_acte(num_acte)";
			echo traite_rqt($rqt,"alter table liens_actes add index i_num_acte");

			$rqt = "alter table liens_actes drop index i_num_acte_lie";
			echo traite_rqt($rqt,"alter table liens_actes drop index i_num_acte_lie");
			$rqt = "alter table liens_actes add index i_num_acte_lie(num_acte_lie)";
			echo traite_rqt($rqt,"alter table liens_actes add index i_num_acte_lie");

			//JP - Modification taille du champ mailtpl_tpl de la table mailtpl
			$rqt ="alter table mailtpl MODIFY mailtpl_tpl MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table mailtpl modify mailtpl_tpl mediumtext");

			//JP - Nettoyage des catégories sans libellé
			$rqt ="DELETE FROM categories WHERE libelle_categorie=''";
			echo traite_rqt($rqt,"Delete categories sans libellé");

			// JP - Abonnements - nom du périodique par défaut en création d'abonnement
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='abt_label_perio' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
							VALUES (0, 'pmb', 'abt_label_perio', '0', 'Création d\'un abonnement : reprendre le nom du périodique ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert pmb_abt_label_perio = 0 into parametres");
			}

			// JP - Acquisitions - afficher le nom de l'abonnement dans les lignes de la commande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='show_abt_in_cmde' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'acquisition', 'show_abt_in_cmde', '0', 'Afficher l\'abonnement dans les lignes de la commande ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert acquisition_show_abt_in_cmde = 0 into parametres");
			}

			// NG - Nomenclature: Familles
			$rqt = "CREATE TABLE if not exists nomenclature_families (
					id_family int unsigned NOT NULL auto_increment,
					family_name varchar(255) NOT NULL DEFAULT '',
					family_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_family))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_families");

			// NG - Nomenclature: pupitres
			$rqt = "CREATE TABLE if not exists nomenclature_musicstands (
					id_musicstand int unsigned NOT NULL auto_increment,
					musicstand_name varchar(255) NOT NULL DEFAULT '',
					musicstand_famille_num int unsigned NOT NULL DEFAULT 0,
					musicstand_division int unsigned NOT NULL DEFAULT 0,
					musicstand_order int unsigned NOT NULL DEFAULT 0,
					musicstand_workshop int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_musicstand))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_musicstands");

			// NG - Nomenclature: instruments
			$rqt = "CREATE TABLE if not exists nomenclature_instruments (
					id_instrument int unsigned NOT NULL auto_increment,
					instrument_code varchar(255) NOT NULL DEFAULT '',
					instrument_name varchar(255) NOT NULL DEFAULT '',
					instrument_musicstand_num int unsigned NOT NULL DEFAULT 0,
					instrument_standard int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_instruments");

			//NG - Nomenclature: Formations
			$rqt = "CREATE TABLE if not exists nomenclature_formations (
					id_formation int unsigned NOT NULL auto_increment,
					formation_name varchar(255) NOT NULL DEFAULT '',
					formation_nature int unsigned NOT NULL DEFAULT 0,
					formation_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_formation))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_formations");

			// NG - Nomenclature: Types
			$rqt = "CREATE TABLE if not exists nomenclature_types (
					id_type int unsigned NOT NULL auto_increment,
					type_name varchar(255) NOT NULL DEFAULT '',
					type_formation_num int unsigned NOT NULL DEFAULT 0,
					type_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_type))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_types");

			// NG - Nomenclature: voix
			$rqt = "CREATE TABLE if not exists nomenclature_voices (
				id_voice int unsigned NOT NULL auto_increment,
				voice_code varchar(255) NOT NULL DEFAULT '',
				voice_name varchar(255) NOT NULL DEFAULT '',
				voice_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_voice))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_voices");

			// NG - Nomenclature: Formations dans les notices
			$rqt = "CREATE TABLE if not exists nomenclature_notices_nomenclatures (
				id_notice_nomenclature int unsigned NOT NULL auto_increment,
				notice_nomenclature_num_notice int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_num_formation int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_num_type int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_label varchar(255) NOT NULL DEFAULT '',
				notice_nomenclature_abbreviation TEXT NOT NULL ,
				notice_nomenclature_notes TEXT NOT NULL,
				notice_nomenclature_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_notice_nomenclature))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_notices_nomenclatures");

			// NG - Nomenclature: Ateliers des formations de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_workshops (
				id_workshop int unsigned NOT NULL auto_increment,
				workshop_label varchar(255) NOT NULL DEFAULT '',
				workshop_num_nomenclature int unsigned NOT NULL DEFAULT 0,
				workshop_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_workshop))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_workshops");

			// NG - Nomenclature: Instruments des ateliers de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_workshops_instruments (
				id_workshop_instrument int unsigned NOT NULL auto_increment,
				workshop_instrument_num_workshop int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_number int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_workshop_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_workshops_instruments");

			// NG - Nomenclature: Instruments non standards de la formation de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_exotic_instruments (
				id_exotic_instrument int unsigned NOT NULL auto_increment,
				exotic_instrument_num_nomenclature int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_number int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_exotic_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_exotic_instruments");


			// NG - Nomenclature: Instruments non standards autres de la formation de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_exotic_other_instruments (
				id_exotic_other_instrument int unsigned NOT NULL auto_increment,
				exotic_other_instrument_num_exotic_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_other_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_other_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_exotic_other_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_exotic_other_instruments");

			// NG - Nomenclature: notices filles
			$rqt = "CREATE TABLE if not exists nomenclature_children_records (
				child_record_num_record int unsigned NOT NULL DEFAULT 0,
				child_record_num_formation int unsigned NOT NULL DEFAULT 0,
				child_record_num_type int unsigned NOT NULL DEFAULT 0,
				child_record_num_musicstand int unsigned NOT NULL DEFAULT 0,
				child_record_num_instrument int unsigned NOT NULL DEFAULT 0,
				child_record_effective int unsigned NOT NULL DEFAULT 0,
				child_record_order int unsigned NOT NULL DEFAULT 0,
				child_record_other varchar(255) NOT NULL DEFAULT '',
				child_record_num_voice int unsigned NOT NULL DEFAULT 0,
				child_record_num_workshop int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (child_record_num_record))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_children_records");

			// NG - Ajout paramètre pour identifier le type de relation entre une notice de nomenclature et ses notices filles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_record_children_link' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_record_children_link', '', 'Type de relation entre une notice de nomenclature et ses notices filles.','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_record_children_link");
			}

			// NG - Ajout paramètre pour activer les nomenclatures
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_activate', '0', 'Activation des nomenclatures:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_activate into parametres");
			}

			//MHo - Augmentation de la taille du champ pour les titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL");

			// MB - Affichage liste des bulletins - Modification explication du paramètre
			$rqt = "UPDATE parametres SET comment_param='Fonction d\'affichage de la liste des bulletins d\'un périodique\nValeurs possibles:\naffichage_liste_bulletins_normale (Si paramètre vide)\naffichage_liste_bulletins_tableau\naffichage_liste_bulletins_depliable' WHERE type_param= 'opac' and sstype_param='fonction_affichage_liste_bull'";
			echo traite_rqt($rqt,"UPDATE parametres opac_fonction_affichage_liste_bull");

			// VT & DG - Création des tables de veilles
			$rqt="create table if not exists docwatch_watches(
				id_watch int unsigned not null auto_increment primary key,
				watch_title varchar(255) not null default '',
				watch_owner int unsigned not null default 0,
				watch_allowed_users varchar(255) not null default '',
				watch_num_category int unsigned not null default 0,
				watch_last_date datetime,
				watch_ttl int unsigned not null default 0,
				index i_docwatch_watch_title(watch_title)
				)";
			echo traite_rqt($rqt, "create table docwatch_watches");

			$rqt="create table if not exists docwatch_datasources(
				id_datasource int unsigned not null auto_increment primary key,
				datasource_type varchar(255) not null default '',
				datasource_title varchar(255) not null default '',
				datasource_ttl int unsigned not null default 0,
				datasource_last_date datetime,
				datasource_parameters mediumtext not null,
				datasource_num_category int unsigned not null default 0,
				datasource_default_interesting int unsigned not null default 0,
				datasource_num_watch int unsigned not null default 0,
				index i_docwatch_datasource_title(datasource_title)
				)";
			echo traite_rqt($rqt, "create table docwatch_datasources");

			$rqt="create table if not exists docwatch_selectors (
				id_selector int unsigned not null auto_increment primary key,
				selector_type varchar(255) not null default '',
				selector_num_datasource int unsigned not null default 0,
				selector_parameters mediumtext not null
				)";
			echo traite_rqt($rqt, "create table docwatch_selectors");

			$rqt="create table if not exists docwatch_items(
				id_item int unsigned not null auto_increment primary key,
				item_type varchar(255) not null default '',
				item_title varchar(255) not null default '',
				item_summary mediumtext not null,
				item_content mediumtext not null,
				item_added_date datetime,
				item_publication_date datetime,
				item_hash varchar(255) not null default '',
				item_url varchar(255) not null default '',
				item_logo_url varchar(255) not null default '',
				item_status int unsigned not null default 0,
				item_interesting int unsigned not null default 0,
				item_num_article int unsigned not null default 0,
				item_num_section int unsigned not null default 0,
				item_num_notice int unsigned not null default 0,
				item_num_datasource int unsigned not null default 0,
				item_num_watch int unsigned not null default 0,
				index i_docwatch_item_type(item_type),
				index i_docwatch_item_title(item_title),
				index i_docwatch_item_num_article(item_num_article),
				index i_docwatch_item_num_section(item_num_section),
				index i_docwatch_item_num_notice(item_num_notice),
				index i_docwatch_item_num_watch(item_num_watch)
				)";
			echo traite_rqt($rqt, "create table docwatch_items");

			$rqt="create table if not exists docwatch_items_descriptors(
				num_item int unsigned not null default 0,
				num_noeud int unsigned not null default 0,
				primary key (num_item, num_noeud)
				)";
			echo traite_rqt($rqt, "create table docwatch_items_descriptors");

			$rqt="create table if not exists docwatch_categories(
				id_category int unsigned not null auto_increment primary key,
				category_title varchar(255) not null default '',
				category_num_parent int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table docwatch_categories");

			$rqt="create table if not exists docwatch_items_tags(
				num_item int unsigned not null default 0,
				num_tag int unsigned not null default 0,
				primary key (num_item, num_tag)
				)";
			echo traite_rqt($rqt, "create table docwatch_items_tags");

			$rqt="create table if not exists docwatch_tags(
				id_tag int unsigned not null auto_increment primary key,
				tag_title varchar(255) not null default ''
				)";
			echo traite_rqt($rqt, "create table docwatch_tags");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_desc text NOT NULL" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_desc ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_logo_url varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_logo_url ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_type char(2) not null default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_status ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_parent int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_parent");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_content_type int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_content_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_publication_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_content_publication_status ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_parent int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_parent");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_content_type int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_content_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_publication_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_content_publication_status ");

			// NG - Demandes: Ajout d'un paramètre permettant de saisir un email générique pour la gestion des demanades
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_generic' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'demandes', 'email_generic', '',
				'Information par un email générique de l\'évolution des demandes.\n 1,adrmail@mail.fr : Envoi une copie uniquement pour toutes les nouvelles demandes\n 2,adrmail@mail.fr : Envoi une copie uniquement des mails envoyés aux personnes affectées\n 3,adrmail@mail.fr : Envoi une copie dans les 2 cas précédents\n ',
				'',0) ";
				echo traite_rqt($rqt, "insert demandes_email_generic into parameters");
			}

			// NG - Demandes: Ajout d'un paramètre permettant d'afficher le format simplifié en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_affichage_simplifie' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_affichage_simplifie', '0',
				'Active le format simplifié des demandes en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_affichage_simplifie into parameters");
			}

			// NG - Demandes: Ajout d'un paramètre permettant d'interdire l'ajout d'une action en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_no_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_no_action', '0',
				'Interdire l\'ajout d\'une action en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_no_action into parameters");
			}

			// NG - Demandes: lien entre la note générant la réponse finale d'une demande
			$rqt = "ALTER TABLE demandes ADD demande_note_num int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes ADD demande_note_num ");

			//JP - Modification de la longueur du champ email de la table coordonnees
			$rqt = "ALTER TABLE coordonnees MODIFY email varchar(255) NOT NULL default '' ";
			echo traite_rqt($rqt,"alter table coordonnees modify email");

			// DG - Veilles : Option pour nettoyer le contenu HTML des nouveaux éléments
			$rqt = "ALTER TABLE docwatch_datasources ADD datasource_clean_html int unsigned not null default 1 after datasource_default_interesting" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_clean_html ");

			// VT - Ajout paramètre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_min' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_min into parametres");
			}

			// VT - Ajout paramètre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_max' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_max into parametres");
			}

			// VT - Ajout paramètre pour definir le rapport de distance entre deux points pour qu'ils soit aggrégés ensembles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_distance' "))==0){
					$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_distance into parametres");
			}

			// VT - Creation table de correspondance contenant les aires des différentes emprises de la base
			$rqt="create table if not exists map_hold_areas as (select map_emprise_id as id_obj, map_emprise_type as type_obj, Area(map_emprise_data) as area, Area(envelope(map_emprise_data)) as bbox_area, AsText(Centroid(envelope(map_emprise_data))) as center from map_emprises)";
			echo traite_rqt($rqt, "create table map_hold_areas");

			//VT - Verification de l'existance de la clé primaire (création si non-existante)
			if (pmb_mysql_num_rows(pmb_mysql_query("show keys from map_hold_areas where column_name = 'id_obj' "))==0){
				$rqt="alter table map_hold_areas add primary key(id_obj)";
				echo traite_rqt($rqt, "alter table map_hold_areas add primary key");
			}

			//NG - ajout pied de page dans template de la fiche de circulation
			$rqt = "ALTER TABLE serialcirc_tpl ADD serialcirctpl_piedpage text NOT NULL ";
			echo traite_rqt($rqt,"alter table serialcirc_tpl add serialcirctpl_piedpage");

			// AP - Ajout de la recherche dans les concepts
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_concept' "))==0) {
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'modules_search_concept', '0', 'Recherche dans les concepts : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut', 'c_recherche', 0) ";
				echo traite_rqt($rqt, "insert opac_modules_search_concept into parameters");
			}

			// VT - Ajout paramètre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_min' "))==0) {
				$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ('opac', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0) ";
				echo traite_rqt($rqt, "insert opac_map_hold_ratio_min into parametres");
			}

			// VT - Ajout paramètre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_max' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_ratio_max into parametres");
			}

			// VT - Ajout paramètre pour definir le rapport de distance entre deux points pour qu'ils soit aggrégés ensembles (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_distance' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_distance into parametres");
			}

			// VT - Ajout d'un index sur la colonne map_emprise_obj_num de la table map_emprises
			$rqt="alter table map_emprises add index i_map_emprise_obj_num(map_emprise_obj_num)";
			echo traite_rqt($rqt, "alter table map_emprises add index i_map_emprise_obj_num");

			// JP - Ajout champ de classement sur étagères et paniers
			$rqt = "ALTER TABLE caddie ADD caddie_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE caddie ADD caddie_classement ");

			$rqt = "ALTER TABLE empr_caddie ADD empr_caddie_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie ADD empr_caddie_classement ");

			$rqt = "ALTER TABLE etagere ADD etagere_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD etagere_classement ");

			// MB - LDAP gestion de l'encodage lors de l'import
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'ldap' and sstype_param='encoding_utf8' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'ldap', 'encoding_utf8', '0', 'Les informations du LDAP sont en utf-8 ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert ldap_encoding_utf8 = 0 into parametres");
			}

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.19");
			break;

		case "v5.19":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//DG - Code Javascript d'analyse d'audience
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='script_analytics' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'script_analytics', '', 'Code Javascript d\'analyse d\'audience (Par exemple pour Google Analytics, XiTi,..).','a_general',0)";
				echo traite_rqt($rqt,"insert opac_script_analytics into parametres");
			}

			//DG - Accessibilité OPAC : Paramètre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='accessibility' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'accessibility', '1', 'Accessibilité activée.\n 0 : Non.\n 1 : Oui.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_accessibility = 1 into parametres");
			}

			//JP - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas déjà (ajout sur la requête en v5.17)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, update_date=update_date  WHERE transfert_location_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine");

			$rqt = "UPDATE exemplaires SET transfert_statut_origine=expl_statut, update_date=update_date  WHERE transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_statut_origine");

			// NG - Ajout paramètre indiquant la durée en jours de conservation des notices en tant que nouveauté
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='newrecord_timeshift' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'newrecord_timeshift', '0', 'Nombre de jours de conservation des notices en tant que nouveauté.','', 0)";
				echo traite_rqt($rqt,"insert pmb_newrecord_timeshift");
			}

			// Création shorturls
			$rqt="create table if not exists shorturls (
				id_shorturl int unsigned not null auto_increment primary key,
				shorturl_hash varchar(255) not null default '',
				shorturl_last_access datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				shorturl_context text not null,
				shorturl_type varchar(255) not null default '',
				shorturl_action varchar(255) not null default ''
			)";
			echo traite_rqt($rqt,"create table shorturls");

			// NG - Nouveautés
			$rqt = "ALTER TABLE notices ADD notice_is_new int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_is_new ");

			$rqt = "ALTER TABLE notices ADD notice_date_is_new  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_date_is_new ");

			// VT - Modif du paramètre map_max_holds en gestion (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'pmb' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises' where type_param like 'pmb' and sstype_param like 'map_max_holds'";
				echo traite_rqt($rqt, "update parametres map_max_holds gestion");
			}

			// VT - Modif du paramètre map_max_holds en opac (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'opac' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises' where type_param like 'opac' and sstype_param like 'map_max_holds'";
				echo traite_rqt($rqt, "update parametres map_max_holds opac");
			}

			// DB - Modification de la table resa_planning (ajout de previsions sur bulletins)
			$rqt = "alter table resa_planning add resa_idbulletin int(8) unsigned default '0' not null after resa_idnotice";
			echo traite_rqt($rqt,"alter resa_planning add resa_idbulletin ");

			//JP - Section origine pour les transferts
			$rqt = "ALTER TABLE exemplaires ADD transfert_section_origine SMALLINT(5) NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD transfert_section_origine ");

			$rqt = "UPDATE exemplaires SET transfert_section_origine=expl_section, update_date=update_date WHERE transfert_section_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_section_origine");

			//AP Modification du commentaire d'opac_notices_format : Ajout des templates django
			$rqt = "update parametres set comment_param='Format d\'affichage des notices en résultat de recherche\n 0 : Utiliser le paramètre notices_format_onglets\n 1 : ISBD seul\n 2 : Public seul \n4 : ISBD et Public\n 5 : ISBD et Public avec ISBD en premier \n8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param= 'opac' and sstype_param='notices_format' ";
			echo traite_rqt($rqt,"update opac_notices_format into parametres");

			// AP - Ajout paramètre indiquant le nom du répertoire des templates django à utiliser en affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_django_directory' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'notices_format_django_directory', '', 'Nom du répertoire de templates django à utiliser en affichage de notice.\nLaisser vide pour utiliser le common.','e_aff_notice', 0)";
				echo traite_rqt($rqt,"insert notices_format_django_directory into parametres");
			}

			//MB: Ajouter une PK aux tables de vue
			$res=pmb_mysql_query("SHOW TABLES LIKE 'opac_view_notices_%'");
			if($res && pmb_mysql_num_rows($res)){
				while ($r=pmb_mysql_fetch_array($res)){
					$rqt = "ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice" ;
					echo traite_rqt($rqt,"ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice ");

					$rqt = "ALTER TABLE ".$r[0]." DROP PRIMARY KEY";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." DROP PRIMARY KEY");

					$rqt = "ALTER TABLE ".$r[0]." ADD PRIMARY KEY (opac_view_num_notice)";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." ADD PRIMARY KEY");
				}
			}

			//DG - Paramètre OPAC : Autoriser le téléchargement des documents numériques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_download_docnums' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'allow_download_docnums', '1', 'Autoriser le téléchargement des documents numériques.\n 0 : Non.\n 1 : Individuellement (un par un).\n 2 : Archive ZIP.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_allow_download_docnums = 1 into parametres");
			}

			//AB - Le nom du fichier de paramétrage du selecteur d'affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_display_modes' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'notices_display_modes', '', 'Nom du fichier xml de paramétrage du choix du mode d\'affichage des notices à l\'OPAC.\nPar défaut : display_modes_exemple.xml dans /opac_css/includes/records/','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_notices_display_modes='' into parametres");
			}

			//DG - Lien pour en savoir plus sur l'utilisation des cookies et des traceurs
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='url_more_about_cookies' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'url_more_about_cookies', '', 'Lien pour en savoir plus sur l\'utilisation des cookies et des traceurs','a_general',0)";
				echo traite_rqt($rqt,"insert opac_url_more_about_cookies into parametres");
			}

			//DG - MAJ du template de bannettes par défaut (identifiant 1)
			$rqt = "UPDATE bannette_tpl SET bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n{% for sommaire in sommaires %}\r\n<a href=\"#[{{sommaire.level}}]\">\r\n{{sommaire.level}} - {{sommaire.title}}\r\n</a>\r\n<br />\r\n{% endfor %}\r\n</div>\r\n<hr>\r\n{% for sommaire in sommaires %}\r\n<a name=\"[{{sommaire.level}}]\" />\r\n<h1>{{sommaire.level}} - {{sommaire.title}}</h1>\r\n{% for record in sommaire.records %}\r\n{{record.render}}\r\n<hr>\r\n{% endfor %}\r\n<br />\r\n{% endfor %}\r\n{{info.footer}}'
					WHERE bannettetpl_id=1";
			echo traite_rqt($rqt,"ALTER minimum into bannette_tpl");

			// DB - Modification de la table resa_planning (prévisions localisées)
			$rqt = "alter table resa_planning add resa_loc_retrait int(5) unsigned not null default 0 ";
			echo traite_rqt($rqt,"alter resa_planning add resa_loc_retrait ");

			// JP - Ajout champ demande abonnement sur périodique
			$rqt = "ALTER TABLE notices ADD opac_serialcirc_demande TINYINT UNSIGNED NOT NULL DEFAULT 1";
			echo traite_rqt($rqt,"ALTER TABLE notices ADD opac_serialcirc_demande") ;

			// JP - Ajout champ de classement sur infopages
			$rqt = "ALTER TABLE infopages ADD infopage_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE infopages ADD infopage_classement ");

			// JP - Ajout autorisations sur recherches prédéfinies gestion
			$rqt = "ALTER TABLE search_perso ADD autorisations MEDIUMTEXT NULL DEFAULT NULL ";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD autorisations") ;

			$rqt = "UPDATE search_perso SET autorisations=num_user ";
			echo traite_rqt($rqt,"UPDATE autorisations INTO search_perso");

			//VT - Paramètre OPAC : Definition du chemin des templates d'autorités en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='authorities_templates_folder' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'authorities_templates_folder', './includes/templates/authorities/common', 'Repertoire des templates utilisés pour l\'affichage des autorités en OPAC','',1)";
				echo traite_rqt($rqt,"insert opac_authorities_templates_folder = ./includes/templates/authorities/common into parametres");
			}

			// JP - template par défaut pour les bannettes privées
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_notices_template' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'dsi', 'private_bannette_notices_template', '0', 'Id du template de notice utilisé par défaut en diffusion de bannettes privées. Si vide ou à 0, le template classique est utilisé.', '', 0)";
				echo traite_rqt($rqt, "insert private_bannette_notices_template into parameters");
			}

			// JP - ajout index manquants sur tables de champs persos
			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_st");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_st(author_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_st");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_t");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_t(author_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_t");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_i");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_i(author_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_i");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_d");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_d(author_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_d");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_f");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_f(author_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_f");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_st");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_st(authperso_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_st");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_t");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_t(authperso_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_t");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_i");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_i(authperso_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_i");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_d");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_d(authperso_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_d");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_f");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_f(authperso_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_f");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_st(categ_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_t(categ_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_i(categ_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_d(categ_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_f(categ_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_st(cms_editorial_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_t(cms_editorial_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_i(cms_editorial_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_d(cms_editorial_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_f(cms_editorial_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_st(collection_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_t(collection_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_i(collection_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_d(collection_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_f(collection_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_st");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_st(gestfic0_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_st");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_t");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_t(gestfic0_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_t");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_i");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_i(gestfic0_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_i");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_d");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_d(gestfic0_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_d");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_f");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_f(gestfic0_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_f");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_st");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_st(indexint_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_st");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_t");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_t(indexint_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_t");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_i");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_i(indexint_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_i");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_d");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_d(indexint_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_d");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_f");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_f(indexint_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_f");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_st");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_st(publisher_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_st");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_t");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_t(publisher_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_t");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_i");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_i(publisher_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_i");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_d");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_d(publisher_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_d");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_f");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_f(publisher_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_f");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_st");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_st(serie_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_st");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_t");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_t(serie_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_t");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_i");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_i(serie_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_i");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_d");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_d(serie_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_d");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_f");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_f(serie_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_f");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_st");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_st(subcollection_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_st");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_t");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_t(subcollection_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_t");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_i");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_i(subcollection_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_i");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_d");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_d(subcollection_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_d");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_f");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_f(subcollection_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_f");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_st");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_st(tu_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_st");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_t");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_t(tu_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_t");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_i");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_i(tu_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_i");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_d");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_d(tu_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_d");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_f");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_f(tu_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_f");


			//AR - Paramètre Portail : Activer la mise en cache des images
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_image_cache' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_image_cache', '0', 'Activer la mise en cache des vignettes du contenu éditorial.\n 0: non \n 1:Oui \nAttention, si l\'OPAC ne se trouve pas sur le même serveur que la gestion, la purge du cache ne peut pas se faire automatiquement','',0)";
				echo traite_rqt($rqt,"insert cms_active_image_cache into parametres");
			}

			// MHo - Correction des messages des parametres sur l'ordre d'affichage et le mode d'affichage des concepts d'une notice (remplacement de "categorie" par "concept")
			$rqt="UPDATE parametres SET comment_param='Paramétrage de l\'ordre d\'affichage des concepts d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1'
				WHERE type_param='thesaurus' AND sstype_param='concepts_affichage_ordre' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_affichage_ordre into parametres ");

			$rqt="UPDATE parametres SET comment_param='Affichage des concepts en ligne.\n 0 : Non.\n 1 : Oui.'
				WHERE type_param='thesaurus' AND sstype_param='concepts_concept_in_line' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_concept_in_line into parametres ");

			//DG - Flag pour savoir si le mot de passe est déjà encrypté
			$rqt= "alter table empr add empr_password_is_encrypted int(1) not null default 0 after empr_password";
			echo traite_rqt($rqt,"alter table empr add empr_password_is_encrypted");

			//DG - Phrase pour le hashage des mots de passe emprunteurs (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_password_salt' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'empr_password_salt', '', 'Phrase pour le hashage des mots de passe emprunteurs','a_general',1)";
				echo traite_rqt($rqt,"insert opac_empr_password_salt into parametres");
			}

			//DG - Info d'encodage des mots de passe lecteurs pour la connexion à l'Opac
			$res=pmb_mysql_query("SELECT count(*) FROM empr");
			if($res && pmb_mysql_result($res,0,0)){
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ ENCODER LES MOTS DE PASSE LECTEURS (APRES ETAPES DE MISE A JOUR) / YOU MUST ENCODE PASSWORD READERS (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}

			// JP - Parametre affichage des dates de creation et modification notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notices_show_dates' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'notices_show_dates', '0', 'Afficher les dates des notices ? \n 0 : Aucune date.\n 1 : Date de création et modification.', '',0) ";
				echo traite_rqt($rqt, "insert expl_show_dates=0 into parameters");
			}

			// AR - Paramètre pour activer la compression des CSS
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compress_css' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'compress_css', '0', 'Activer la compilation et la compression des feuilles de styles.\n0: Non\n1: Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_compress_css into parametres");
			}

			//VT - Ajout d'un champ tonalité marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_tonalite_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_tonalite_marclist");

			//VT - Ajout d'un champ forme marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_forme_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_forme_marclist");

			// DB - Modification de la table resa_planning (quantité prévisions)
			$rqt = "alter table resa_planning add resa_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_qty");
			$rqt = "alter table resa_planning add resa_remaining_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_remaining_qty");
			// DB - Modification de la table resa (lien vers prévisions)
			$rqt = "alter table resa add resa_planning_id_resa int(8) unsigned not null default 0";
			echo traite_rqt($rqt,"alter resa add resa_planning_id_resa");

			// DB - Delai d'alerte pour le transfert des previsions en reservations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_planning_toresa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'resa_planning_toresa', '10', 'Délai d\'alerte pour le transfert des prévisions en réservations (en jours). ' ,'',0)";
				echo traite_rqt($rqt,"insert resa_planning_toresa into parametres");
			}
			
			//JP - Nettoyage vues en erreur suite ajout index unique
			$res=mysql_query("SHOW TABLES LIKE 'opac_view_notices_%'");
			if($res && mysql_num_rows($res)){
				while ($r=mysql_fetch_array($res)){
					$rqt = "TRUNCATE TABLE ".$r[0] ;
					echo traite_rqt($rqt,"TRUNCATE TABLE ".$r[0]);
			
					$rqt = "ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice" ;
					echo traite_rqt($rqt,"ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice ");
			
					$rqt = "ALTER TABLE ".$r[0]." DROP PRIMARY KEY";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." DROP PRIMARY KEY");
			
					$rqt = "ALTER TABLE ".$r[0]." ADD PRIMARY KEY (opac_view_num_notice)";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." ADD PRIMARY KEY");
				}
			
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=opac&sub=opac_view&section=list' target=_blank>VOUS DEVEZ RECALCULER LES VUES OPAC (APRES ETAPES DE MISE A JOUR) / YOU MUST RECALCULATE OPAC VIEWS (STEPS AFTER UPDATE) : Admin > Vues Opac > Générer les recherches</a></b> ") ;
			}
			
			//JP - nettoyage table authorities_sources
			$rqt = "DELETE FROM authorities_sources WHERE num_authority=0";
			echo traite_rqt($rqt,"DELETE FROM authorities_sources num_authority vide");
			
			//JP - accès rapide pour les paniers de notices
			$rqt = "ALTER TABLE caddie ADD acces_rapide INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE caddie ADD acces_rapide");
			
			//JP - modification index notices_mots_global_index
			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");
				
			$rqt ="alter table notices_mots_global_index drop primary key";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop primary key");
			$rqt ="alter table notices_mots_global_index add primary key (id_notice,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add primary key");
			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
				
			//DG - Proposer la conservation de catégories en remplacement de notice
			$rqt= "alter table users add deflt_notice_replace_keep_categories int(1) not null default 0";
			echo traite_rqt($rqt,"alter table users add deflt_notice_replace_keep_categories");
			
			//DG - Champs perso pret
			$rqt = "create table if not exists pret_custom (
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
				filters INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table pret_custom ");
			
			$rqt = "create table if not exists pret_custom_lists (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_list_value varchar(255) default NULL,
				pret_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY i_pret_custom_champ (pret_custom_champ),
				KEY i_pret_champ_list_value (pret_custom_champ,pret_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table if not exists pret_custom_lists ");
			
			$rqt = "create table if not exists pret_custom_values (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_origine int(10) unsigned NOT NULL default 0,
				pret_custom_small_text varchar(255) default NULL,
				pret_custom_text text,
				pret_custom_integer int(11) default NULL,
				pret_custom_date date default NULL,
				pret_custom_float float default NULL,
				KEY i_pret_custom_champ (pret_custom_champ),
				KEY i_pret_custom_origine (pret_custom_origine)) " ;
			echo traite_rqt($rqt,"create table if not exists pret_custom_values ");
				
			//DG - maj valeurs possibles pour empr_sort_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sort_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n b: code-barres \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts' where type_param= 'empr' and sstype_param='sort_rows' ";
				echo traite_rqt($rqt,"update empr_sort_rows into parametres");
			}
				
			//DG - maj valeurs possibles pour empr_filter_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_rows' and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts' where type_param= 'empr' and sstype_param='filter_rows' ";
				echo traite_rqt($rqt,"update empr_filter_rows into parametres");
			}
				
			//DG - maj valeurs possibles pour empr_show_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='show_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n 1: icône panier' where type_param= 'empr' and sstype_param='show_rows' ";
				echo traite_rqt($rqt,"update empr_show_rows into parametres");
			}
				
			// AP - Création d'une table pour la gestion de la suppression des enregistrements OAI
			$rqt = "CREATE TABLE if not exists connectors_out_oai_deleted_records (
					num_set int(11) unsigned NOT NULL DEFAULT 0,
					num_notice int(11) unsigned NOT NULL DEFAULT 0,
					deletion_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (num_set, num_notice))";
			echo traite_rqt($rqt,"CREATE TABLE connectors_out_oai_deleted_records") ;
			
			// AP - Ajout du stockage de la grammaire d'une vedette
			$rqt = "ALTER TABLE vedette ADD grammar varchar(255) NOT NULL default 'rameau'" ;
			echo traite_rqt($rqt,"ALTER TABLE vedette ADD grammar");
			
			//JP - recalcul des isbn à cause du nouveau fomatage
			require_once($include_path."/isbn.inc.php");
			$res=pmb_mysql_query("SELECT notice_id, code FROM notices WHERE code<>'' AND niveau_biblio='m' AND code LIKE '97%'");
			if($res && pmb_mysql_num_rows($res)){
				while ($row=pmb_mysql_fetch_object($res)) {
					$code = $row->code;
					$new_code = formatISBN($code);
					if ($code!= $new_code){
						pmb_mysql_query("UPDATE notices SET code='".addslashes($new_code)."', update_date=update_date WHERE notice_id=".$row->notice_id);
					}
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"update notices code / ISBN check and clean") ;
				
			
			//JP - mise à jour des dates de validation des commandes
			$rqt="UPDATE actes SET date_valid=date_acte WHERE statut>1 AND date_valid='0000-00-00'";
			echo traite_rqt($rqt,"update actes date_validation ");
			
			$rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' " ;
			echo traite_rqt($rqt,"update pmb_bdd_subversion=0 into parametres");
			$pmb_bdd_subversion=0;
			
			if ($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion) {
				// Info de déconnexion pour passer le add-on
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/logout.php' target=_blank>VOUS DEVEZ VOUS DECONNECTER ET VOUS RECONNECTER POUR TERMINER LA MISE A JOUR  / YOU MUST DISCONNECT AND RECONNECT YOU TO COMPLETE UPDATE</a></b> ") ;
			}
			
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			break;

	default:
		include("$include_path/messages/help/$lang/alter.txt");
		break;
	}

/* A mettre en 5.20


*
*
*
*/