<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_v5.inc.php,v 1.1104.2.2 2019-11-12 14:11:51 dgoron Exp $

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
				$maj_a_faire = "v5.20";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.20":
				$maj_a_faire = "v5.21";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.21":
				$maj_a_faire = "v5.22";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.22":
				$maj_a_faire = "v5.23";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.23":
				$maj_a_faire = "v5.24";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.24":
				$maj_a_faire = "v5.25";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.25":
				$maj_a_faire = "v5.26";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.26":
				$maj_a_faire = "v5.27";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.27":
				$maj_a_faire = "v5.28";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.28":
				$maj_a_faire = "v5.29";
				echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
				echo form_relance ($maj_a_faire);
				break;
			case "v5.29":
			    $maj_a_faire = "v5.30";
			    echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
			    echo form_relance ($maj_a_faire);
			    break;
			case "v5.30":
			    $maj_a_faire = "v5.31";
			    echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
			    echo form_relance ($maj_a_faire);
			    break;
			case "v5.31":
			    $maj_a_faire = "v5.32";
			    echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
			    echo form_relance ($maj_a_faire);
			    break;
			case "v5.32":
			    $maj_a_faire = "v5.33";
			    echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
			    echo form_relance ($maj_a_faire);
			    break;
			case "v5.33":
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
					VALUES (NULL, 'pmb', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activ� \n 1 : activ�', '', '0')";
			echo traite_rqt($rqt,"insert pmb_opac_view_activate='0' into parametres ");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='opac_view_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activ� \n 1 : activ�', 'a_general', '0')";
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

		//Gestion g�n�rique des subst de parametre ( pour vues Opac )
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
			  		VALUES (NULL, 'pmb', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activ� \n 1 : activ�', '', '0')";
			echo traite_rqt($rqt,"insert pmb_sur_location_activate='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='sur_location_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activ� \n 1 : activ�', 'a_general', '0')";
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

		// Favicon, report� de la 4.94 - ER
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='faviconurl' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'faviconurl', '', 'URL du favicon, si vide favicon=celui de PMB','a_general')";
			echo traite_rqt($rqt,"insert opac_faviconurl='' into parametres");
		}

		//on pr�cise si une source est interrog�e directement en ajax dans l'OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_affiliate_search INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table connectors_sources add opac_affiliate_search");

		// Activation des recherches affili�es dans les sources externes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_affiliate_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_affiliate_search', '0', 'Activer les recherches affili�es en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
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

		//ajout d'un statut de lignes d'actes par d�faut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from lignes_actes_statuts where id_statut='1' "))==0) {
			$rqt = "INSERT INTO lignes_actes_statuts (id_statut,libelle,relance) VALUES (1 ,'Traitement normal', '1') ";
			echo traite_rqt($rqt,"insert default lignes_actes_statuts");
		}

		//raz des statuts de lignes d'actes
		$rqt = "UPDATE lignes_actes set statut='1' ";
		echo traite_rqt($rqt,"alter lignes_actes raz statut");

		//ajout d'un statut de ligne d'acte par d�faut par utilisateur pour les devis
		$rqt = "ALTER TABLE users ADD deflt3lgstatdev int(3) not null default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default lg state dev");

		//ajout d'un statut de ligne d'acte par d�faut par utilisateur pour les commandes
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

		//Param�tres de mise en page des relances d'acquisitions
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
					VALUES(0,'acquisition','pdfrel_pos_num_cli','10,80,0,10,16','Position num�ro de client: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_num_cli into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_num' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_num','10,0,10,16','Position num�ro de commande/devis: Distance par rapport au bord gauche de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
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
					VALUES(0,'acquisition','pdfrel_text_after','','Texte apr�s le tableau de relances','pdfrel',0)" ;
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
					VALUES(0,'acquisition','pdfrel_text_sign','Le responsable de la biblioth�que.','Texte signature','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_sign into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_by_mail','1','Effectuer les relances par mail :\n 0 : non \n 1 : oui','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint un �tat des commandes en cours.\r\n\r\nMerci de nous pr�ciser par retour vos d�lais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la biblioth�que.','Texte du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_mail into parametres") ;
		}

		//ajout bulletinage avec document num�rique
		$rqt = "ALTER TABLE abts_abts ADD abt_numeric int(1) not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_numeric ");

		//ajout dans les bannettes la possibilit� de ne pas tenir compte du statut des notices
		$rqt = "ALTER TABLE bannettes ADD statut_not_account INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add statut_not_account");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_perio_browser' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','show_perio_browser','0','Affichage du navigateur de p�riodiques en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_perio_browser into parametres") ;
		}

		// Gestion des relances des p�riodiques
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
					VALUES(0,'opac','show_onglet_perio_a2z','0','Activer l\'onglet du navigateur de p�riodiques en OPAC.\n 0 : Non.\n 1 : Oui.','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_show_onglet_perio_a2z into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'�toiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'�toiles.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_note_display_mode into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_display_mode','0','Mode d\'affichage des avis de notices.\n 0 : Visible en lien � cot� de l\'onglet Public/ISBD de la notice.\n 1 : Visible dans la notice.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_display_mode into parametres") ;
		}

		$rqt = "ALTER TABLE avis ADD avis_rank INT UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_rank") ;

		//Module Gestionnaire de t�ches
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='planificateur_allow' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'planificateur_allow', '0', 'Planificateur activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
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

		// Cr�ation des tables n�cessaires au gestionnaire de t�ches
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

		//ajout d'un statut par d�faut en r�ception pour les suggestions
		$rqt = "ALTER TABLE users ADD deflt3receptsugstat int(3) not null default 32 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default recept sug state");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_obj_mail','Etat des en-cours','Objet du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_obj_mail into parametres") ;
		}

		//ajout de param�tres pour l'envoi de commandes par mail
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
					VALUES(0,'acquisition','pdfcde_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une commande � traiter.\r\n\r\nMerci de nous confirmer par retour vos d�lais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la biblioth�que.','Texte du mail','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_text_mail into parametres") ;
		}

		//ajout de param�tres pour l'envoi de devis par mail
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
					VALUES(0,'acquisition','pdfdev_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une demande de devis.\r\n\r\nCordialement,\r\n\r\nLe responsable de la biblioth�que.','Texte du mail','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcdev_text_mail into parametres") ;
		}

		// masquer la possibilit� d'uploader les docnum en base
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='docnum_in_database_allow' "))==0){
			if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) $upd_param_docnum_in_database_allow = 1;
			else $upd_param_docnum_in_database_allow=0;
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'docnum_in_database_allow', '$upd_param_docnum_in_database_allow', 'Autoriser le stockage de document num�rique en base ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_docnum_in_database_allow=$upd_param_docnum_in_database_allow into parameters <br><b>SET this parameter to 1 to (re)allow file storage in database !</b>");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_ajax_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'recherche_ajax_mode', '1', 'Affichage acc�l�r� des r�sultats de recherche: header uniquement, la suite est charg�e lors du click sur le \"+\".\n 0: Inactif\n 1: Actif (par lot)\n 2: Actif (par notice)', 'c_recherche', '0')" ;
			echo traite_rqt($rqt,"insert opac_recherche_ajax_mode=1 into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'�toiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'�toiles.','',0)" ;
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
					VALUES (0, 'cms', 'active', '0', 'Module \'Portail\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert cms_active=0 into parameters");
		}

		//langue d'indexation par d�faut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_lang' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'indexation_lang', '', 'Choix de la langue d\'indexation par d�faut. (ex : fr_FR,en_UK,...,ar), si vide c\'est la langue de l\'interface du catalogueur qui est utilis�e.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_indexation_lang into parameters");
		}

		//ajout du champ permettant la pr�-selection du connecteur en OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_selected int(3) unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD opac_selected");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_show_location' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'websubscribe_show_location', '0', 'Afficher la possibilit� pour le lecteur de choisir sa localisation lors de son inscription en ligne.\n 0: Non\n 1: Oui', 'f_modules', '0')" ;
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

		//param�trage de la pond�ration des champs persos...
		// dans le notices
		$rqt = "alter table notices_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table notices_custom add pond");
		//dans les exemplaires
		$rqt = "alter table expl_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table expl_custom add pond ");
		//dans les �tats des collections
		$rqt = "alter table collstate_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table collstate_custom add pond");
		//dans les lecteurs, pour rester homog�ne...
		$rqt = "alter table empr_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table empr_custom add pond");

		//tri sur les �tats des collections en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'collstate_order', 'archempla_libelle,collstate_cote','Ordre d\'affichage des �tats des collections, dans l\'ordre donn�, s�par� par des virgules : archempla_libelle,collstate_cote','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_collstate_order=archempla_libelle,collstate_cote into parametres");
		}

		//la pond�ration dans les fiches ne sert � rien mais pour rester homog�ne avec les autres champs persos...
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

		//changement de cl� primaire
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

		// ajout d'un param�tre de tri par d�faut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','default_sort','d_num_6,c_text_28','Tri par d�faut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour d�croissant, puis num ou text pour num�rique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))','d_aff_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_default_sort into parametres") ;
		}
		flush();
		//AR /new search !

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: cat�gorie\n s: statut\n g: groupe\n y: ann�e de naissance\n cp: code postal\n cs : code statistique\n #n : id des champs personnalis�s' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//Pr�cision affichage amendes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='fine_precision' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion) VALUES (0, 'pmb', 'fine_precision', '2', 'Nombre de d�cimales pour l\'affichage des amendes',1)";
			echo traite_rqt($rqt,"insert fine_precision=2 into parametres");
		}

		//Rafraichissement des vues opac
		$rqt = "alter table opac_views add opac_view_last_gen datetime default null";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_last_gen");
		$rqt = "alter table opac_views add opac_view_ttl int not null default 86400";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_ttl");

		// param�trage du cache en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_cache_duration' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','search_cache_duration','600','Dur�e de validit� (en secondes) du cache des recherches OPAC','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_cache_duration into parametres") ;
		}

		// ajout d'un param�tre utilisateur de statut par d�faut en import (report de l'alter V4, modif tardive en 3.4)
		$rqt = "alter table users add deflt_integration_notice_statut int(6) not null default 1 after deflt_notice_statut";
		echo traite_rqt($rqt,"alter table users add deflt_integration_notice_statut");

		// Info de r�indexation
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

		//Type de document par d�faut en cr�ation de p�riodique
		$rqt = "ALTER TABLE users ADD xmlta_doctype_serial varchar(2) NOT NULL DEFAULT '' after xmlta_doctype";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_serial after xmlta_doctype");

		//Type de document par d�faut en cr�ation de bulletin
		$rqt = "ALTER TABLE users ADD xmlta_doctype_bulletin varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_bulletin after xmlta_doctype_serial");

		//Type de document par d�faut en cr�ation d'article
		$rqt = "ALTER TABLE users ADD xmlta_doctype_analysis varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_analysis after xmlta_doctype_bulletin");

		// Mise � jour des valeurs en fonction du type de document par d�faut en cr�ation de notice, si la valeur est vide !
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

		// d�but circulation p�riodiques
		//ajout du champ expl_abt_num permettant de lier l'exemplaire a un abonnement de p�rio
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
				VALUES (0, 'pmb', 'path_php', '', 'Chemin absolu de l\'interpr�teur PHP, local ou distant', '',0) ";
			echo traite_rqt($rqt, "insert pmb_path_php into parameters");
		}

		//modification taille du champ expl_comment de la table exemplaires
		$rqt = "ALTER TABLE exemplaires MODIFY expl_comment TEXT ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_comment");

		//tri sur les documents num�riques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='explnum_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id','Ordre d\'affichage des documents num�riques, dans l\'ordre donn�, s�par� par des virgules : explnum_mimetype, explnum_nom, explnum_id','e_aff_notice')";
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
		//ajout d'un flag pour la r�sa en circulation
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

		// Cr�ation table facettes foireuse en d�veloppement
		$rqt = "ALTER TABLE facettes add facette_type_sort int(1) not null default 0 AFTER facette_visible";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_type_sort ");
		$rqt = "ALTER TABLE facettes add facette_order_sort int(1) not null default 0 AFTER facette_type_sort";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_order_sort ");

		// comptabilisation de l'amende : � partir de la date de retour, � partir du d�lai de gr�ce
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='amende_comptabilisation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'amende_comptabilisation', '0','Date � laquelle le d�but de l\'amende sera comptabilis�e \r\n 0 : � partir de la date de retour \r\n 1 : � partir du d�lai de gr�ce','')";
			echo traite_rqt($rqt,"insert pmb_amende_comptabilisation=0 into parametres");
		}

		// pr�t en retard : compter le jour de la date de retour ou la date de relance comme un retard ?
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
				VALUES (0, 'opac', 'exclude_fields', '','Identifiants des champs � exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','c_recherche')";
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
				VALUES (0, 'opac', 'serialcirc_active', 0,'Activer la circulation des p�dioques dans l\'OPAC \r\n 0: Non \r\n 1: Oui','f_modules')";
			echo traite_rqt($rqt,"insert opac_serialcirc_active into parametres");
		}

		//AR
		//Ajout d'un droit sur le statut pour la circulation des p�rios
		$rqt = "alter table empr_statut add allow_serialcirc int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table empr_statut add allow_serialcirc");

		// cr�ation $pmb_bdd_subversion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bdd_subversion' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'bdd_subversion', '0', 'Sous-version de la base de donn�es')";
			echo traite_rqt($rqt,"insert pmb_bdd_subversion=0 into parametres");
		}

		//AR - Ajout d'un param�tre pour d�finir la classe d'import des autorit�s...
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='import_modele_authorities' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'import_modele_authorities', 'notice_authority_import','Quelle classe d\'import utiliser pour les notices d\'autorit�s ?','')";
			echo traite_rqt($rqt,"insert pmb_import_modele_authorities into parametres");
		}

		//AR - pris dans le tapis entre 2 versions...
		//cr�ation de la table origin_authorities
		$rqt = "create table if not exists origin_authorities (
			id_origin_authorities int(10) unsigned NOT NULL AUTO_INCREMENT,
			origin_authorities_name varchar(255) NOT NULL DEFAULT '',
			origin_authorities_country varchar(10) NOT NULL DEFAULT '',
			origin_authorities_diffusible int(10) unsigned NOT NULL DEFAULT 0,
			primary key (id_origin_authorities)
			)";
		echo traite_rqt($rqt,"create table origin_authorities");
		//AR - ajout de valeurs par d�fault...
		$rqt = "insert into origin_authorities
				(id_origin_authorities,origin_authorities_name,origin_authorities_country,origin_authorities_diffusible)
			values
				(1,'Catalogue Interne','FR',1),
				(2,'BnF','FR',1)";
		echo traite_rqt($rqt,"insert default values into origin_authorities");

		//AR - cr�ation de la table authorities_source
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

		//AR - cr�ation de la table notices_authorities_sources
		$rqt ="create table if not exists notices_authorities_sources (
			num_authority_source int(10) unsigned NOT NULL DEFAULT 0,
			num_notice int(10) unsigned NOT NULL DEFAULT 0,
			primary key (num_authority_source,num_notice)
			)";
		echo traite_rqt($rqt,"create table notices_authorities_sources");

		//AR - modification du champ aut_link_type
		$rqt = "alter table aut_link change aut_link_type aut_link_type varchar(2) not null default ''";
		echo traite_rqt($rqt,"alter table aut_link change aut_link_type varchar");

		//MB - Modification de l'explication du param�tre d'affichage des dates d'exemplaire
		$rqt="UPDATE parametres SET comment_param='Afficher les dates des exemplaires ? \n 0 : Aucune date.\n 1 : Date de cr�ation et modification.\n 2 : Date de d�p�t et retour (BDP).\n 3 : Date de cr�ation, modification, d�p�t et retour.' WHERE type_param='pmb' AND sstype_param='expl_show_dates'";
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG
		// localisation des pr�visions
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='location_resa_planning' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'pmb', 'location_resa_planning', '0', '0', 'Utiliser la gestion de la pr�vision localis�e?\n 0: Non\n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT location_resa_planning INTO parametres") ;
		}

		//Localisation par d�faut sur la visualisation des �tats des collections
		$rqt = "ALTER TABLE users ADD deflt_collstate_location int(6) UNSIGNED DEFAULT 0 after deflt_docs_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_collstate_location after deflt_docs_location");

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: cat�gorie\n s: statut\n g: groupe\n y: ann�e de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement\n #n : id des champs personnalis�s' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//maj valeurs possibles pour empr_show_rows
		$rqt = "update parametres set comment_param='Colonnes affich�es en liste de lecteurs, saisir les colonnes s�par�es par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+pr�nom \n a: adresse \n b: code-barre \n c: cat�gories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: ann�e de naissance \n ab: type d\'abonnement \n #n : id des champs personnalis�s \n 1: ic�ne panier' where type_param= 'empr' and sstype_param='show_rows' ";
		echo traite_rqt($rqt,"update empr_show_rows into parametres");

		//maj valeurs possibles pour empr_sort_rows
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+pr�nom \n c: cat�gories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: ann�e de naissance \n ab: type d\'abonnement \n #n : id des champs personnalis�s' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//maj commentaire sms_msg_retard
		$rqt = "update parametres set comment_param='Texte du sms envoy� lors d\'un retard' where type_param= 'empr' and sstype_param='sms_msg_retard' ";
		echo traite_rqt($rqt,"update empr_sms_msg_retard into parametres");

		//maj commentaire afficher_numero_lecteur_lettres
		$rqt = "update parametres set comment_param='Afficher le num�ro et le mail du lecteur sous l\'adresse dans les diff�rentes lettres' where type_param= 'pmb' and sstype_param='afficher_numero_lecteur_lettres' ";
		echo traite_rqt($rqt,"update pmb_afficher_numero_lecteur_lettres into parametres");

		//DB
		//modification du param�tre empr_sms_activation
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

		//Ajout de la dur�e de consultation pour la circulation des p�rios
		$rqt = "alter table abts_periodicites add consultation_duration int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table abts_periodicites add consultation_duration");

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			// Info de r�indexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		// suppr index inutile
		$rqt = "alter table notices_fields_global_index drop index i_id_value";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop index i_id_value");

		//Modification du commentaire du param�tre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des r�duits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date �dition\n 2 = titre+auteur principal+date �dition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+�dit+champs persos id 1 2 3\n T = tit1+tit4' where type_param='opac' and sstype_param='notice_reduit_format'";
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

		//AR - Ajout d'un param�tre de blocage d'import dans les autorit�s
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

		//AR - Modification d'un param�tre pour d�finir la classe d'import des autorit�s...
		$rqt = "update parametres set valeur_param = 'authority_import' where type_param= 'pmb' and sstype_param = 'import_modele_authorities'";
		echo traite_rqt($rqt,"update parametres set pmb_import_modele_authorities = 'authority_import'");

		//Ajout d'un index sur le champ ref dans les tables entrepots
		//R�cup�ration de la liste des sources
		$sql_liste_sources = "SELECT source_id FROM connectors_sources ";
		$res_liste_sources = pmb_mysql_query($sql_liste_sources, $dbh) or die(pmb_mysql_error());

		//Pour chaque source
		while ($row=pmb_mysql_fetch_row($res_liste_sources)) {
			$sql_alter_table = "alter table entrepot_source_".$row[0]." drop index i_ref ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." drop index i_ref");
			$sql_alter_table = "alter table entrepot_source_".$row[0]." add index i_ref (ref) ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." add index i_ref");
		}

		//Ajout d'un parametre permettant de pr�ciser si l'on informe par email de l'�volution des demandes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_demandes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'demandes', 'email_demandes', '1',
					'Information par email de l\'�volution des demandes.\n 0 : Non\n 1 : Oui',
					'',0) ";
			echo traite_rqt($rqt, "insert demandes_email_demandes into parameters");
		}


		//AR - Ajout d'un param�tre utilisateur (choix d'un th�saurus par d�faut en import d'autorit�s
		$rqt = "alter table users add deflt_import_thesaurus int not null default 1 after deflt_thesaurus";
		echo traite_rqt($rqt,"alter table users add deflt_import_thesaurus'");

		//AR - On lui met un bonne valeur par d�faut...
		$rqt = "update users set deflt_import_thesaurus = ".$thesaurus_defaut;
		echo traite_rqt($rqt,"update users set deflt_import_thesaurus");

		//AR - Ajout d'une colonne sur la table connectors_sources pour d�finir les types d'enrichissements autoris�s dans une source
		$rqt = "alter table connectors_sources add type_enrichment_allowed text not null";
		echo traite_rqt($rqt,"alter table connectors_sources add type_enrichment_allowed");

		// ER - index notices.statut
		$rqt = "ALTER TABLE notices DROP INDEX i_not_statut " ;
		echo traite_rqt($rqt,"ALTER TABLE notices DROP INDEX i_not_statut ") ;
		$rqt = "ALTER TABLE notices ADD INDEX i_not_statut (statut)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_not_statut (statut)") ;


		// Cr�ation cms
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
				VALUES (0, 'pmb', 'short_loan_management', '0', 'Gestion des pr�ts courts\n 0: Non\n 1: Oui')";
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
				VALUES (0, 'pmb', 'loan_trust_management', '0', 'Gestion de monopole de pr�t\n 0: Non\n x: nombre de jours entre 2 pr�ts d\'un exemplaire d\'une m�me notice (ou bulletin)')";
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

		//JP : Ajout d'un param�tre permettant de choisir une navigation ab�c�daire ou non en navigation dans les p�riodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_abc_search' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_abc_search', '0',
					'Recherche ab�c�daire dans le navigateur de p�riodiques en OPAC.\n0 : Non.\n1 : Oui.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_abc_search 0 into parameters");
		}

		//JP : Ajout d'un param�tre permettant de choisir le nombre maximum de notices par onglet en navigation dans les p�riodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_max_per_onglet' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_max_per_onglet', '10',
					'Recherche dans le navigateur de p�riodiques en OPAC : nombre maximum de notices par onglet.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_max_per_onglet 10 into parameters");
		}

		//DG - Mail de rappel au r�f�rent
		$rqt = "ALTER TABLE groupe ADD mail_rappel INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE groupe ADD mail_rappel default 0");

		//DG - Modification du commentaire du param�tre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des r�duits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date �dition\n 2 = titre+auteur principal+date �dition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+�dit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parall�le+auteur principal' where type_param='opac' and sstype_param='notice_reduit_format'";
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

		//DB - parametres de gestion d'acc�s aux programmes externes pour l'indexation des documents numeriques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum_ext' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'indexation_docnum_ext', '',
					'Param�tres de gestion d\'acc�s aux programmes externes pour l\'indexation des documents num�riques :\n\n Chaque param�tre est d�fini par un  couple : \"nom=valeur\"\n Les param�tres sont s�par�s par un \"point-virgule\".\n\n\n Exemples d\'utilisation de \"pyodconverter\", \"jodconverter\" et \"pdftotext\" :\n\npyodconverter_cmd=/opt/openoffice.org3/program/python /opt/ooo_converter/DocumentConverter.py %1s %2s;\njodconverter_cmd=/usr/bin/java -jar /opt/ooo_converter/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar %1s %2s;\njodconverter_url=http://localhost:8080/converter/converted/%1s;\npdftotext_cmd=/usr/bin/pdftotext -enc UTF-8 %1s -;',
					'',0) ";
			echo traite_rqt($rqt, "insert indexation_docnum_ext into parameters");
		}

		//Onglet perso en affichage de notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_onglets' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'notices_format_onglets', '','Liste des id de template de notice pour ajouter des onglets personnalis�s en affichage de notice\nExemple: 1,3','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_notices_format_onglets into parametres");
		}

		//DG - Ajout de la localisation de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_empr_location INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_statut ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_empr_location default 0");

 		//DG - Ajout du type d'abonnement de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_type_abt INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_location ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_type_abt default 0");

		//DG - Libell� OPAC des statuts d'exemplaires
		$rqt = "ALTER TABLE docs_statut ADD statut_libelle_opac VARCHAR(255) DEFAULT '' after statut_libelle";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut add statut_libelle_opac default ''");

		//DG - Visibilit� OPAC des statuts d'exemplaires
 		$rqt = "ALTER TABLE docs_statut ADD statut_visible_opac TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut ADD statut_visible_opac") ;

		//DB - parametres d'alerte avant affichage des documents num�riques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert', '', 'Message d\'alerte � l\'ouverture des documents num�riques.', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert into parameters");
		}

		$rqt = "ALTER TABLE cms_build ADD build_fixed int(11) NOT NULL DEFAULT 0 AFTER id_build";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_fixed");

		$rqt = "ALTER TABLE cms_build ADD build_child_before varchar(255) not null default '' AFTER build_parent";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_child_before");

		//AR - cr�ation d'une boite noire pour les modules du portail
		$rqt="create table if not exists cms_managed_modules (
			managed_module_name varchar(255) not null default '',
			managed_module_box text not null,
			primary key (managed_module_name))";
		echo traite_rqt($rqt, "create table if not exists cms_managed_modules");


		$rqt = "alter table cms_cadres add cadre_fixed int(11) not null default 0 after cadre_name";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_fixed");


		//DG - Fixer l'�ge minimum d'acc�s � la cat�gorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_min INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_min default 0");

		//DG - Fixer l'�ge maximum d'acc�s � la cat�gorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_max INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_max default 0");

		// Liste des cms
		$rqt="create table if not exists cms (
            id_cms int unsigned not null auto_increment primary key,
            cms_name varchar(255) not null default '',
            cms_comment text not null
        )";
        echo traite_rqt($rqt,"create table cms");

 		// �volutions des cms
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

		//id du cms � utiliser en Opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cms' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'cms', 0,'id du CMS utilis� en OPAC','a_general')";
			echo traite_rqt($rqt,"insert opac_cms into parametres");
		}

		//DG - Colonnes exemplaires affich�es en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_data' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'expl_data', 'expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle', 'Colonne des exemplaires, dans l\'ordre donn�, s�par� par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle #n : id des champs personnalis�s \r\n expl_cb est obligatoire et sera ajout� si absent','')";
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
				VALUES (0, 'opac', 'show_group_checkout', '0', 'Le responsable du groupe de lecteur voit les pr�ts de son groupe\n 0: Non\n 1: oui','a_general')";
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

		//Nombre d'archive � m�moriser en dsi
		$rqt = "ALTER TABLE bannettes ADD archive_number INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add archive_number");

		//AR - Erreur dans le type de colonne
		$rqt = "ALTER TABLE cms_pages MODIFY page_hash varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ");

		//AR - L'authentification Digest impose une valeur en clair...
		$rqt= "alter table users add user_digest varchar(255) not null default '' after pwd";
		echo traite_rqt($rqt,"alter table users add user_digest");

		//Ajout de deux param�tres pour la navigation par facette
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facette_in_bandeau_2' "))==0){
			$rqt = "insert into parametres values(0,'opac','facette_in_bandeau_2',0,'La navigation par facettes apparait dans le bandeau ou dans le bandeau 2\n0 : dans le bandeau\n1 : Dans le bandeau 2','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_facette_in_bandeau_2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='autolevel2' "))==0){
			$rqt = "insert into parametres values(0,'opac','autolevel2',0,'0 : mode normal de recherche\n1 : Affiche directement le r�sultat de la recherche tous les champs sans passer par la pr�sentation du niveau 1 de recherche','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_autolevel2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='first_page_params' "))==0){
			$rqt = "insert into parametres values(0,'opac','first_page_params','','Structure Json r�capitulant les param�tres � initialiser pour la page d\\'accueil :\nExemple : \n{\n\"lvl\":\"cmspage\",\n\"pageid\":2\n}','b_aff_general',0)";
			echo traite_rqt($rqt,"insert opac_first_page_params='' into parametres");
		}

		$rqt = "ALTER TABLE cms_build ADD build_type varchar(255) not null default 'cadre' AFTER build_version_num";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_type");

		//Cr�ation d'un div class raw
		$rqt = "ALTER TABLE cms_build ADD build_div INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table cms_build add build_div");

		// Ajout tpl de notice pour g�n�rer le header
		$rqt = "update parametres set comment_param = 'Format d\'affichage des r�duits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date �dition\n 2 = titre+auteur principal+date �dition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+�dit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parall�le+auteur principal\n H 1 = id d\'un template de notice' where type_param='opac' and sstype_param='notice_reduit_format'";
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

		//AR - param�tre activant les liens vers les documents num�riques non visibles
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_links_invisible_docnums' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'show_links_invisible_docnums', '0',
			'Afficher les liens vers les documents num�riques non visible en mode non connect�. (Ne fonctionne pas avec les droits d\'acc�s).\n 0 : Non.\n1 : Oui.',
			'e_aff_notice',0) ";
			echo traite_rqt($rqt, "insert opac_show_links_invisible_docnums into parameters");
		}

		// G�n�rer un document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_generate INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_generate");

		// Template de notice en g�n�ration de document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_notice_tpl INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_notice_tpl");

		// G�n�rer un document avec les doc num (dsi)
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
					VALUES (0, 'pmb', 'img_folder', '',	'R�pertoire de stockage des images', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_folder into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_url' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'img_url', '',	'URL d\'acc�s du r�pertoire des images (pmb_img_folder)', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_url into parameters");
		}
		// Ajout de la possibilit� de joindre les images dans le mail ( pmb_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails � partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='pmb' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre pmb_mail_html_format");

		// Ajout de la possibilit� de joindre les images dans le mail ( opac_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails � partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='opac' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre opac_mail_html_format");

		//AR - Ajout d'une colonne pour marquer un set comme �tant en cours de rafraississement
		$rqt = "alter table connectors_out_sets add being_refreshed int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table connectors_out_sets add bien_refreshed");

		//DG - Infobulle lors du survol des vignettes (gestion)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'pmb', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le param�tre book_pics_url','')";
			echo traite_rqt($rqt,"insert pmb_book_pics_msg='' into parametres");
		}

		//DG - Infobulle lors du survol des vignettes (opac)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le param�tre book_pics_url','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_book_pics_msg='' into parametres");
		}

		//AR - Utilisation des quotas pour la d�finition des vues disponibles pour un emprunteur
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
			//la m�thode du chef reste la meilleure
			set_time_limit(0);

			if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
				$rqt = "truncate table notices_fields_global_index";
				echo traite_rqt($rqt,"truncate table notices_fields_global_index");

				$rqt = "truncate table notices_mots_global_index";
				echo traite_rqt($rqt,"truncate table notices_mots_global_index");

				// Info de r�indexation
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

			//on ajout la cl� �trang�re num_word dans notices_mots_global_index
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

			//certains champs n'ont plus d'utilit� dans notices_mots_global_index
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

		//AR - modification du param�tre de gestion des vues
		$rqt = "update parametres set comment_param = 'Activer les vues OPAC :\n 0 : non activ�\n 1 : activ� avec gestion classique\n 2 : activ� avec gestion avanc�e' where type_param = 'pmb' and sstype_param = 'opac_view_activate'";
		echo traite_rqt($rqt,"update parametres pmb_opac_view_activate");

		//DB - modification du param�tre utiliser_calendrier
		$rqt = "update parametres set comment_param = 'Utiliser le calendrier des jours d\'ouverture ?\n 0 : non\n 1 : oui, pour le calcul des dates de retour et des retards\n 2 : oui, pour le calcul des dates de retour uniquement' where type_param = 'pmb' and sstype_param = 'utiliser_calendrier'";
		echo traite_rqt($rqt,"update parametres pmb_utiliser_calendrier");

		//NG - Ajout dans les statuts d'exemplaire la possibilit� de rendre r�servable ou non
		$rqt = "ALTER TABLE docs_statut ADD statut_allow_resa INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table docs_statut add statut_allow_resa");
		$rqt = "UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1 ";
		echo traite_rqt($rqt,"UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1");

		// Ajout CMS actif par d�faut en Opac
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

		//AR - Un type de contenu c'est quoi? c'est une d�finition de grille de champs perso
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

		//MB - Modification du commentaire du param�tre show_categ_browser
		$rqt = "UPDATE parametres SET comment_param = 'Affichage des cat�gories en page d\'accueil OPAC:\n0: Non\n1: Oui\n1 3,1: Oui, avec th�saurus id 3 puis 1 (pr�ciser les th�saurus � afficher et l\'ordre)' where type_param = 'opac' and sstype_param = 'show_categ_browser'";
		echo traite_rqt($rqt,"update parametres show_categ_browser");

		//MB - Remplacement du code de lien d'autorit� 2 par z car c'est le m�me libell� et z est norm�
		$rqt = "UPDATE aut_link SET aut_link_type = 'z' where aut_link_type = '2' ";
		echo traite_rqt($rqt,"update aut_link");

		//AR indexons correctement le contenu �ditorial
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

		//DB - parametre d'alerte avant affichage des documents num�riques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert_doctype' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert_doctype', '', 'Liste des types de documents pour lesquels une alerte est g�n�r�e (s�par�s par une virgule).', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert_doctype into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_memo_url int not null default 0 after cadre_url";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_memo_url");

		//DB - entrepot d'archivage � la suppression des notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='archive_warehouse' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'archive_warehouse', '0', 'Identifiant de l\'entrep�t d\'archivage � la suppression des notices.', '',0) ";
			echo traite_rqt($rqt, "insert archive_warehouse into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_classement  varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_classement");

		//NG - Imprimante ticket
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'printer_name', '', 'Nom de l\'imprimante de ticket de pr�t, utilisant l\'applet jzebra. Le nom de l\'imprimante doit correspondre � la class d�velopp�e sp�cifiquement pour la piloter.\nExemple: Nommer l\'imprimante \'metapace\' pour utiliser le driver classes/printer/metapace.class.php', '',0) ";
			echo traite_rqt($rqt, "insert pmb_printer_name into parameters");
		}

		//DG - Localisation par d�faut sur la visualisation des r�servations
		$rqt = "ALTER TABLE users ADD deflt_resas_location int(6) UNSIGNED DEFAULT 0 after deflt_collstate_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_resas_location after deflt_collstate_location");

		//DG - parametre localisation des groupes de lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='groupes_localises' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'empr', 'groupes_localises', '0', 'Groupes de lecteurs localis�s par rapport au responsable \n0: Non \n1: oui')";
			echo traite_rqt($rqt,"insert empr_groupes_localises=0 into parametres");
		}

		// Activation des recherches similaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_simili_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_simili_search', '0', 'Activer les recherches similaires en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_simili_search='0' into parametres ");
		}

		//ajout d'une date de cr�ation pour les articles et les rubriques
		$rqt ="alter table cms_articles add article_creation_date date";
		echo traite_rqt($rqt,"alter table cms_articles add article_creation_date date");
		$rqt ="alter table cms_sections add section_creation_date date";
		echo traite_rqt($rqt,"alter table cms_sections add section_creation_date date");

		//index d'on se l�ve tous pour la bannette de Camille
		$rqt = "alter table bannette_abon drop index i_num_empr";
		echo traite_rqt($rqt,"alter table bannette_abon drop index i_num_empr");
		$rqt = "alter table bannette_abon add index i_num_empr(num_empr)";
		echo traite_rqt($rqt,"alter table bannette_abon add index i_num_empr(num_empr)");

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_plus' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_plus', 'plus.gif', 'Image � utiliser devant un titre de notice pli�e', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_plus into parametres ");
		}

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_moins' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_moins', 'minus.gif', 'Image � utiliser devant un titre de notice d�pli�e', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_moins into parametres ");
		}

		//MB - Modification du commentaire du param�tre notices_depliable
		$rqt = "UPDATE parametres SET comment_param = 'Affichage d�pliable des notices en r�sultat de recherche:\n0: Non d�pliable\n1: D�pliable en cliquant que sur l\'icone\n2: D�plibable en cliquant sur toute la ligne du titre' where type_param = 'opac' and sstype_param = 'notices_depliable'";
		echo traite_rqt($rqt,"update parametres notices_depliable");

		// Ajout du regroupement d'exemplaires pour le pr�t
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

		// Ajout du regroupement d'exemplaires pour le pr�t
		$rqt = "create table if not exists groupexpl_expl (
			groupexpl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_expl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_checked int unsigned NOT NULL  default 0,
			PRIMARY KEY (groupexpl_num, groupexpl_expl_num)) ";
		echo traite_rqt($rqt,"create table groupexpl_expl ");

		// Activation du pr�t d'exemplaires group�s
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_groupement' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'pret_groupement', '0', 'Activer le pr�t d\'exemplaires regroup�s en un seul lot. La gestion des groupes se g�re en Circulation / Groupe d\'exemplaires :\n 0 : non \n 1 : oui', '', '0')";
			echo traite_rqt($rqt,"insert pmb_pret_groupement='0' into parametres ");
		}

		//AR - refonte �ditions...
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

		// Transfert: regroupement des d�parts
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='regroupement_depart' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'regroupement_depart', '0', '1', 'Active le regroupement des d�parts\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT transferts_regroupement_depart INTO parametres") ;
		}

		//index Camille (comment �a encore ?)
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

			// Info de r�indexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}

		//AR - On revoit une cl� primaire
		$rqt ="alter table notices_fields_global_index drop primary key";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop primary key");
		$rqt ="alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)";
		echo traite_rqt($rqt,"alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)");

		//AR - ajout du partitionnement de mani�re syst�matique
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
			VALUES (0, 'pmb', 'rfid_afi_security_codes', '', '0', 'Gestion de l\'antivol par le registre AFI.\nLa premi�re valeur est celle de l\'antivol actif, la deuxi�me est celle de l\antivol inactif.\nExemple: 07,C2  ') ";
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
					VALUES(0,'opac','simple_search_suggestions','0','Activer la suggestion de mots en recherche simple via la compl�tion\n0 : D�sactiver\n1 : Activer\n\nNB : Cette fonction n�cessite l\'installation de l\'extension levenshtein dans MySQL','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_simple_search_suggestions into parametres") ;
		}

		//AR - Suggestions de mots dans la saisie en recherche simple
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='stemming_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','stemming_active','0','Activer le stemming dans la recherche\n0 : D�sactiver\n1 : Activer\n','c_recherche',0)" ;
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

		//DG - Modification du commentaire du param�tre pmb_blocage_delai
		$rqt = "UPDATE parametres SET comment_param = 'D�lai � partir duquel le retard est pris en compte pour le blocage' where type_param = 'pmb' and sstype_param = 'blocage_delai'";
		echo traite_rqt($rqt,"update parametres pmb_blocage_delai");

		$rqt = "delete from parametres where sstype_param like 'url_base_cms_build%' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
		VALUES (0, 'cms', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
		echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;


		//index Camille (c'est que le d�but d'accord d'accord ?)
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

		//AR - ajout de type de contenu g�n�rique pour les articles et rubriques...
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='article_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'article_generic', editorial_type_label ='CP pour Article'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'article_generic'") ;
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'section_generic', editorial_type_label ='CP pour Rubrique'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'section_generic'") ;
		}

		//DG - Ajout du champ index_libelle dans la table frais
		$rqt = "ALTER TABLE frais ADD index_libelle TEXT";
		echo traite_rqt($rqt,"alter table frais add index_libelle");

		//DG - Param�tres pour les lettres de retard par groupe
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

		//MB - Modification de l'identifiant 28 en 1 pour le trie car il est pr�sent en double dans sort.xml
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

			// Info de r�indexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		//NG ajout de field_position dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add field_position int not null default 1";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add field_position");

		//abacarisse en attente
		if (pmb_mysql_num_rows(pmb_mysql_query("select id_param from parametres where type_param= 'opac' and sstype_param='param_social_network' "))==0){
			//Ajout du param�tre de configuration de l'api addThis
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
			', 'Tableau de param�trage de l\'API de gestion des interconnexions aux r�seaux sociaux.
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
		$rqt = "update parametres set comment_param='Tri par d�faut des recherches OPAC. Deux possibilit�s :\n- un seul tri par d�faut de la forme c_num_6\n- plusieurs tris par d�faut de la forme c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour d�croissant\nnum ou text pour num�rique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibell� du tri si plusieurs' WHERE type_param='opac' AND sstype_param='default_sort'";
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

		//AR - on a vu un cas ou ca se passe mal dans la 5.10, par pr�caution, on r�p�te!
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
			VALUES (0, 'opac', 'perio_a2z_show_bulletin_notice', '0', 'Affichage de la notice de bulletin dans le navigateur de p�riodiques', 'c_recherche',0) ";
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

		//DG - parametre pour forcer l'ex�cution des proc�dures
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='procs_force_execution' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
		VALUES (0, 'pmb', 'procs_force_execution', '0', 'Permettre le for�age de l\'ex�cution des proc�dures', '',0) ";
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
				VALUES (0, 'opac', 'draggable', '1', 'Permet d\'activer le glisser d�poser dans le panier pour l\'affichage des notices � l\'OPAC', 'e_aff_notice',0) ";
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

		$rqt="update parametres set comment_param='Liste des id de template de notice pour ajouter des onglets personnalis�s en affichage de notice\nExemple: 1,3,ISBD,PUBLIC\nLe param�tre notices_format doit �tre � 0 pour placer ISBD et PUBLIC' where type_param='opac' and sstype_param='notices_format_onglets' ";
		echo traite_rqt($rqt,"update opac notices_format_onglets comments in parametres") ;

		$rqt = "update parametres set comment_param='0 : mode normal de recherche\n1 : Affiche directement le r�sultat de la recherche tous les champs sans passer par la pr�sentation du niveau 1 de recherche \n2 : Affiche directement le r�sultat de la recherche tous les champs sans passer par la pr�sentation du niveau 1 de recherche sans faire de recherche interm�daire'  where type_param='opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update opac_autolevel comments in parametres");


		//Cr�ation des tables pour le portfolio
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

		// FT - Ajout des param�tres pour forcer les tags meta pour les moteurs de recherche
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
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+pr�nom \n b: code-barres \n c: cat�gories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: ann�e de naissance \n ab: type d\'abonnement \n #n : id des champs personnalis�s' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//DB - cr�ation table index pour le magasin rdf
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

		// MB - Cr�ation d'une table de cache pour les cadres du portail pour acc�l�rer l'affichage
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

		// Info de r�indexation
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base > R�indexer le magasin RDF</a></b> ") ;

		// AP - Ajout de l'ordre dans les rubriques et les articles
		$rqt = "ALTER TABLE cms_sections ADD section_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_sections add section_order");

		$rqt = "ALTER TABLE cms_articles ADD article_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_articles add article_order");

		//DG - CSS add on en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='default_style_addon' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles d�j� incluses ?\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}', '',0) ";
			echo traite_rqt($rqt, "insert pmb_default_style_addon into parametres");
		}

		// NG - circulation sans retour
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_no_ret INT UNSIGNED not null default 0";
		echo traite_rqt($rqt,"alter table serialcirc add serialcirc_no_ret");

		// NG - personnalisation d'impression de la liste de circulation des p�riodiques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_subst' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','serialcirc_subst','','Nom du fichier permettant de personnaliser l\'impression de la liste de circulation des p�riodiques','',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres pmb_serialcirc_subst");
		}

		//MB - Augmenter la taille du libell� de groupe
		$rqt = "ALTER TABLE groupe CHANGE libelle_groupe libelle_groupe VARCHAR(255) NOT NULL";
		echo traite_rqt($rqt,"alter table groupe");

		//AR - Ajout d'un type de cache pour un cadre
		$rqt = "alter table cms_cadres add cadre_modcache varchar(255) not null default 'get_post_view'";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_modcache");

		//DG - Type de relation par d�faut en cr�ation de p�riodique
		$rqt = "ALTER TABLE users ADD value_deflt_relation_serial VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_serial after value_deflt_relation");

		//DG - Type de relation par d�faut en cr�ation de bulletin
		$rqt = "ALTER TABLE users ADD value_deflt_relation_bulletin VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_bulletin after value_deflt_relation_serial");

		//DG - Type de relation par d�faut en cr�ation d'article
		$rqt = "ALTER TABLE users ADD value_deflt_relation_analysis VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_analysis after value_deflt_relation_bulletin");

		//DG - Mise � jour des valeurs en fonction du type de relation par d�faut en cr�ation de notice, si la valeur est vide !
		if ($res = pmb_mysql_query("select userid, value_deflt_relation,value_deflt_relation_serial,value_deflt_relation_bulletin,value_deflt_relation_analysis from users")){
			while ( $row = pmb_mysql_fetch_object($res)) {
				if ($row->value_deflt_relation_serial == '') pmb_mysql_query("update users set value_deflt_relation_serial='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_bulletin == '') pmb_mysql_query("update users set value_deflt_relation_bulletin='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_analysis == '') pmb_mysql_query("update users set value_deflt_relation_analysis='".$row->value_deflt_relation."' where userid=".$row->userid);
			}
		}

		//DG - Activer le pr�t court par d�faut
		$rqt = "ALTER TABLE users ADD deflt_short_loan_activate INT(1) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_short_loan_activate");

		//DG - Alerter l'utilisateur par mail des nouvelles inscriptions en OPAC ?
		$rqt = "ALTER TABLE users ADD user_alert_subscribemail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_demandesmail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_subscribemail default 0");

		//DB - Modification commentaire autolevel
		$rqt = "update parametres set comment_param='0 : mode normal de recherche.\n1 : Affiche le r�sultat de la recherche tous les champs apr�s calcul du niveau 1 de recherche.\n2 : Affiche directement le r�sultat de la recherche tous les champs sans passer par le calcul du niveau 1 de recherche.' where type_param= 'opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update parameter comment for opac_autolevel2");

		//AR - Ajout du param�tres pour la dur�e de validit� du cache des cadres du potail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='cache_ttl' "))==0){
			$rqt = "insert into parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'cms', 'cache_ttl', '1800', 'dur�e de vie du cache des cadres du portail (en secondes)', '',0) ";
			echo traite_rqt($rqt, "insert cms_caches_ttl into parametres");
		}

		//DG - P�riodicit� : Jour du mois
		$rqt = "ALTER TABLE planificateur ADD perio_jour_mois VARCHAR( 128 ) DEFAULT '*' AFTER perio_minute";
		echo traite_rqt($rqt,"ALTER TABLE planificateur ADD perio_jour_mois DEFAULT * after perio_minute");

		//DG - Replanifier la t�che en cas d'�chec
		$rqt = "alter table taches_type add restart_on_failure int(1) UNSIGNED DEFAULT 0 NOT NULL";
		echo traite_rqt($rqt,"alter table taches_type add restart_on_failure");

		//DG - Alerte mail en cas d'�chec de la t�che
		$rqt = "alter table taches_type add alert_mail_on_failure VARCHAR(255) DEFAULT ''";
		echo traite_rqt($rqt,"alter table taches_type add alert_mail_on_failure");

		//DG - Pr�remplissage de la vignette des d�pouillements
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serial_thumbnail_url_article' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'serial_thumbnail_url_article', '0', 'Pr�remplissage de l\'url de la vignette des d�pouillements avec l\'url de la vignette de la notice m�re en catalogage des p�riodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert pmb_serial_thumbnail_url_article=0 into parametres");
		}

		//DG - D�lai en millisecondes entre les mails envoy�s lors d'un envoi group�
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_delay' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','mail_delay','0','Temps d\'attente en millisecondes entre chaque mail envoy� lors d\'un envoi group�. \n 0 : Pas d\'attente', '',0)" ;
			echo traite_rqt($rqt,"insert pmb_mail_delay=0 into parametres") ;
		}

		//DG - Timeout cURL sur la v�rifications des liens
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='curl_timeout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','curl_timeout','5','Timeout cURL (en secondes) pour la v�rification des liens', '',1)" ;
			echo traite_rqt($rqt,"insert pmb_curl_timeout=0 into parametres") ;
		}

		//DG - Autoriser la prolongation group�e pour tous les membres
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='allow_prolong_members_group' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'empr', 'allow_prolong_members_group', '0', 'Autoriser la prolongation group�e des adh�sions des membres d\'un groupe ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert empr_allow_prolong_members_group=0 into parametres");
		}


		//DB - ajout d'un index stem+lang sur la table words
		$rqt = "alter table words add index i_stem_lang(stem, lang)";
		echo traite_rqt($rqt, "alter table words add index i_stem_lang");

		//NG - Autoindex
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_notice_fields' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_notice_fields', '', 'Liste des champs de notice � utiliser pour l\'indexation automatique, s�par�s par une virgule.\nLes noms des champs sont les identifiants des champs list�s dans le fichier XML pmb/notice/notice.xml\nExemple: tit1,n_resume', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_notice_fields='' into parametres");
		}

		//NG - Autoindex: surchage du parametrage de la recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_search_param' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_search_param', '', 'Surchage des param�tres de recherche de l\'indexation automatique.\n\nSyntaxe: param=valeur;\n\nListe des param�tres:\nautoindex_max_up_distance,\nautoindex_max_down_distance,\nautoindex_stem_ratio,\nautoindex_see_also_ratio,\nautoindex_max_down_ratio,\nautoindex_max_up_ratio,\nautoindex_deep_ratio,\nautoindex_distance_ratio,\nmax_relevant_words,\nmax_relevant_terms', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_search_param='' into parametres");
		}

		//DG - Choix par d�faut pour la prolongation des lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='abonnement_default_debit' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'empr', 'abonnement_default_debit', '0', 'Choix par d�faut pour la prolongation des lecteurs. \n 0 : Ne pas d�biter l\'abonnement \n 1 : D�biter l\'abonnement sans la caution \n 2 : D�biter l\'abonnement et la caution') " ;
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

		//NG - liste des sources externes d'enrichissements � int�grer dans le a2z
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'perio_a2z_enrichissements', '0', 'Affichage de sources externes d\'enrichissement dans le navigateur de p�riodiques.\nListe des couples (s�par� par une virgule) Id de connecteur, Id de source externe d\'enrichissement, s�par� par un point virgule\nExemple:\n6,4;6,5', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_enrichissements=0 into parametres");
		}

		//DG - Modification taille du champ empr_msg de la table empr
		$rqt = "ALTER TABLE empr MODIFY empr_msg TEXT null " ;
		echo traite_rqt($rqt,"alter table empr modify empr_msg");

		//DG - Identifiant du template de notice par d�faut en impression de panier
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_template_default' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'print_template_default', '0', 'En impression de panier, identifiant du template de notice utilis� par d�faut. Si vide ou � 0, le template classique est utilis�', 'a_general', 0)";
			echo traite_rqt($rqt,"insert opac_print_template_default='0' into parametres");
		}

		//DG - Param�tre pour afficher le permalink de la notice dans le detail de la notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_permalink' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'show_permalink', '0', 'Afficher le lien permanent de l\'OPAC en gestion ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_show_permalink=0 into parameters");
		}

		//AB - Ajout du champ pour choix d'un template d'export pour les flux RSS
		$rqt = "ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0 ");

		//DG - Parametre pour afficher ou non l'emprunteur pr�c�dent dans la fiche exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_show_lastempr' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'expl_show_lastempr', '1', 'Afficher l\'emprunteur pr�c�dent sur la fiche exemplaire ? \n 0 : Non.\n 1 : Oui.', '',0) ";
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

		// NG - M�morisation du payement des transactions
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

		// NG - Activer la gestion de caisses en gestion financi�re
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='gestion_financiere_caisses' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'gestion_financiere_caisses', '0', 'Activer la gestion de caisses en gestion financi�re? \n 0 : Non.\n 1 : Oui.', '',0) ";
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

		// AP - Ajout du param�tre de segmentation des documents num�riques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='diarization_docnum' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','diarization_docnum',0,'Activer la segmentation des documents num�riques vid�o ou audio 0 : non activ�e 1 : activ�e','',0)";
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

		//DG - Modification de l'emplacement du param�tre bannette_notices_template dans la zone DSI
		$rqt = "update parametres set type_param='dsi',section_param='' where type_param='opac' and sstype_param='bannette_notices_template' ";
		echo traite_rqt($rqt,"update parametres set bannette_notices_template");

		//DG - Retour � la pr�c�dente forme de tri
		$rqt = "update parametres set comment_param='Tri par d�faut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour d�croissant, puis num ou text pour num�rique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))' WHERE type_param='opac' AND sstype_param='default_sort'";
		echo traite_rqt($rqt,"update comment for param opac_default_sort");

		//DG - Mode d'application d'un tri - Liste de tris pr�-enregistr�s
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_list' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_list', '0 d_num_6,c_text_28;d_text_7', 'Afficher la liste d�roulante de s�lection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour d�croissant\nnum ou text pour num�rique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibell� du tri (optionnel)','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_list = 0 d_num_6,c_text_28;d_text_7 into parametres");
	 	}

	 	//DG - Afficher le libell� du tri appliqu� par d�faut en r�sultat de recherche
	 	if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_display' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_display', '0', 'Afficher le libell� du tri appliqu� par d�faut en r�sultat de recherche ? \n 0 : Non \n 1 : Oui','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_display = 0 into parametres");
	 	}

		// NG - Affichage des bannettes priv�es en page d'accueil de l'Opac
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

			// Info de r�indexation
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

		// AR - Param�tres pour ne pas prendre en compte les mots vides en tous les champs � l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_all_keep_empty_words' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','search_all_keep_empty_words','1','Conserver les mots vides pour les autorit�s dans la recherche tous les champs\n0 : non\n1 : oui','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_all_keep_empty_words into parametres") ;
		}

		// NG - Param�tre pour activer le pi�ge en pr�t si l'emprunteur a d�j� emprunt� l'exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_already_loaned' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'pmb','pret_already_loaned','0','Activer le pi�ge en pr�t si le document a d�j� �t� emprunt� par le lecteur. N�cessite l\'activation de l\'archivage des pr�ts\n0 : non\n1 : oui','',0)" ;
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

		// MHo - Param�tre pour indiquer le nombre de notices similaires � afficher � l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='nb_notices_similaires' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'nb_notices_similaires', '6', 'Nombre de notices similaires affich�es lors du d�pliage d\'une notice.\nValeur max = 6.','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_nb_notices_similaires='6' into parametres");
		}
		// MHo - Param�tre pour rendre ind�pendant l'affichage r�duit des notices similaires par rapport aux notices pli�es
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notice_reduit_format_similaire' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'notice_reduit_format_similaire', '1', 'Format d\'affichage des r�duits de notices similaires :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date �dition\n 2 = titre+auteur principal+date �dition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+�dit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parall�le+auteur principal\n H 1 = id d\'un template de notice','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_notice_reduit_format_similaire='0' into parametres");
		}

		//AR - Param�tres d'�cretage des r�sultats de recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_noise_limit_type' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_noise_limit_type', '0', 'Ecr�ter les r�sulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du r�sultat tout ce qui est en dessous de la moyenne - l\'�cart-type\n2,ratio : Retirer du r�sultat tout ce qui est en dessous de la moyenne - un ratio de l\'�cart-type (ex: 2,1.96)\n3,ratio : Retirer du r�sultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 �limine tout ce qui est inf�rieur � 25% de la plus forte pertinence)' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_noise_limit_type='0' into parametres ");
		}

		//AR - Prise en compte de la fr�quence d'apparition d'un mot dans le fonds pour le calcul de pertinence
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_relevant_with_frequency' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_relevant_with_frequency', '0', 'Utiliser la fr�quence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_relevant_with_frequency='0' into parametres ");
		}

		//DG - Calcul de la prolongation d'adh�sion � partir de la date de fin d'adh�sion ou la date du jour
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='prolong_calc_date_adhes_depassee' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'empr', 'prolong_calc_date_adhes_depassee', '0', 'Si la date d\'adh�sion est d�pass�e, le calcul de la prolongation se fait � partir de :\n 0 : la date de fin d\'adh�sion\n 1 : la date du jour','',0) " ;
			echo traite_rqt($rqt,"insert empr_prolong_calc_date_adhes_depassee = 0 into parametres");
		}

		//DG - Modification du commentaire du param�tre pmb_notice_reduit_format pour les am�liorations
		$rqt = "update parametres set comment_param = 'Format d\'affichage des r�duits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date �dition\n 2 = titre+auteur principal+date �dition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+�dit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parall�le+auteur principal\n H 1 = id d\'un template de notice' where type_param='pmb' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre pmb_notice_reduit_format");

		//DG - P�riodicit� d'envoi par d�faut en cr�ation de bannette priv�e (en jours)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='bannette_priv_periodicite' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'bannette_priv_periodicite', '15', 'P�riodicit� d\'envoi par d�faut en cr�ation de bannette priv�e (en jours)','l_dsi',0) " ;
			echo traite_rqt($rqt,"insert opac_bannette_priv_periodicite = 15 into parametres");
		}

		//DG - Modification du commentaire opac_notices_format
		$rqt = "update parametres set comment_param='Format d\'affichage des notices en r�sultat de recherche \n 0 : Utiliser le param�tre notices_format_onglets \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 5 : ISBD et Public avec ISBD en premier \n 8 : R�duit (titre+auteurs) seul' where type_param='opac' and sstype_param='notices_format'" ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_notices_format") ;


		//DB - Modifications et ajout de commentaires pour les param�tres d�crivant l'autoindexation
		$rqt = "UPDATE parametres SET valeur_param=replace(valeur_param,',',';'), comment_param = 'Liste des champs de notice � utiliser pour l\'indexation automatique.\n\n";
		$rqt.= "Syntaxe: nom_champ=poids_indexation;\n\n";
		$rqt.= "Les noms des champs sont ceux pr�cis�s dans le fichier XML \"pmb/includes/notice/notice.xml\"\n";
		$rqt.= "Le poids de l\'indexation est une valeur de 0.00 � 1. (Si rien n\'est pr�cis�, le poids est de 1)\n\n";
		$rqt.= "Exemple :\n\n";
		$rqt.= "tit1=1.00;n_resume=0.5;' ";
		$rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_notice_fields' ";
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_notice_fields") ;

		$rqt = "UPDATE parametres SET comment_param = 'Surchage des param�tres de recherche de l\'indexation automatique.\n";
		$rqt.= "Syntaxe: param=valeur;\n\n";
		$rqt.= "Listes des parametres:\n\n";
		$rqt.= "max_relevant_words = 20 (nombre maximum de mots et de lemmes de la notice � prendre en compte pour le calcul)\n\n";
		$rqt.= "autoindex_deep_ratio = 0.05 (ratio sur la profondeur du terme dans le th�saurus)\n";
		$rqt.= "autoindex_stem_ratio = 0.80 (ratio de pond�ration des lemmes / aux mots)\n\n";
		$rqt.= "autoindex_max_up_distance = 2 (distance maximum de recherche dans les termes g�n�riques du th�saurus)\n";
		$rqt.= "autoindex_max_up_ratio = 0.01 (pond�ration sur les termes g�n�riques)\n\n";
		$rqt.= "autoindex_max_down_distance = 2 (distance maximum de recherche dans les termes sp�cifiques du th�saurus)\n";
		$rqt.= "autoindex_max_down_ratio = 0.01 (pond�ration sur les termes sp�cifiques)\n\n";
		$rqt.= "autoindex_see_also_ratio = 0.01 (surpond�ration sur les termes voir aussi du th�saurus)\n\n";
		$rqt.= "autoindex_distance_type = 1 (calcul de distance de 1 � 4)\n";
		$rqt.= "autoindex_distance_ratio = 0.50 (ratio de pond�ration sur la distance entre les mots trouv�s et les termes d\'une expression du th�saurus)\n\n";
		$rqt.= "max_relevant_terms = 10 (nombre maximum de termes retourn�s)' ";
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

		// AR - Retrait du param�tres juste commit� : Activation des recherches exemplaires voisins
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='allow_voisin_search'";
		echo traite_rqt($rqt,"delete from parametres opac_allow_voisin_search");

		// AR - Modification du param�tre opac_allow_simili
		$rqt="update parametres set comment_param = 'Activer les recherches similaires sur une notice :\n0 : Non\n1 : Activer la recherche \"Dans le m�me rayon\" et \"Peut-�tre aimerez-vous\"\n2 : Activer seulement la recherche \"Dans le m�me rayon\"\n3 : Activer seulement la recherche \"Peut-�tre aimerez-vous\"', section_param = 'e_aff_notice' where type_param='opac' and sstype_param='allow_simili_search'";
		echo traite_rqt($rqt,"update parametres set opac_allow_simili_search");

		// NG - Affichage des bannettes en page d'accueil de l'Opac	selon la banette
		$rqt = "ALTER TABLE bannettes ADD bannette_opac_accueil INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add bannette_opac_accueil");

		// AR - DSI abonn� en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_subscribed_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_subscribed_bannettes',0,'Affichage des bannettes auxquelles le lecteur est abonn� en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_subscribed_bannettes=0 into parametres") ;
		}

		// AR - DSI publique s�lectionn� en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_public_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_public_bannettes',0,'Affichage des bannettes s�lectionn�es en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert show_public_bannettes=0 into parametres") ;
		}

		// AR - Retrait du param�tre perio_a2z_enrichissements, on ne l'a jamais utilis� car on a finalement ramen� le param�trage par un connecteur
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements'";
		echo traite_rqt($rqt,"delete from parametres opac_perio_a2z_enrichissements");

		//DG - Param�tre non utilis�
		$rqt = "delete from parametres where sstype_param='confirm_resa' and type_param='opac' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG - Param�tre non utilis�
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
			// AB - Param�tre de modification du workflow d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='init_workflow' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES('demandes','init_workflow',0,'Initialisation du workflow de la demande.\n 0 : Validation avant tout\n 1 : Validation avant tout et attribution au validateur\n 2 : Attribution avant tout','',0)";
				echo traite_rqt($rqt,"insert demandes_init_workflow=0 into parametres") ;
			}

			// MHo - Param�tre pour automatiser ou non la cr�ation de notice lors de l'enregistrement d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='notice_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'notice_auto', '0', 'Cr�ation automatique de la notice de demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_notice_auto='0' into parametres");
			}

			// MHo - Param�tre pour la cr�ation par d�faut d'une action lors de la validation d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='default_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'default_action', '1', 'Cr�ation par d�faut d\'une action lors de la validation de la demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_default_action='1' into parametres");
			}

			// MHo - Ajout d'une colonne "origine" de l'utilisateur dans la table audit : 0 = gestion, 1 = opac
			$rqt = "ALTER TABLE audit ADD type_user INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter audit add type_user");

			// AR - Ajout d'une colonne pour stocker les actions autoris�es par type de demande
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

					// Info de r�indexation
					$rqt = " select 1 " ;
					echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
				}

				// JP - Synchronisation RDF
				$rqt = "ALTER TABLE notices_fields_global_index ADD authority_num VARCHAR(50) NOT NULL DEFAULT '0'";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add authority_num");
			}

			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='synchro_rdf' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'synchro_rdf', '0', 'Activer la synchronisation rdf\n 0 : non \n 1 : oui (l\'activation de ce param�tre n�cessite une r�-indexation)','',0) " ;
				echo traite_rqt($rqt,"insert pmb_synchro_rdf = 0 into parametres");
			}

			// AB Modification de la valeur par d�faut du parametre init_workflow
			$rqt="UPDATE parametres SET valeur_param='1' WHERE type_param='demandes' AND sstype_param='init_workflow'";
			echo traite_rqt($rqt,"update parametres set demandes_init_workflow=1");
			// AB Changement du type de champ pour date_note
			$rqt = "ALTER TABLE demandes_notes CHANGE date_note date_note DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"alter demandes_notes CHANGE date_note");
			// MHo - Ajout d'une colonne "notes_read_gestion" pour indiquer si une note a �t� lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add note_read_gestion");
			// MHo - Ajout d'une colonne "actions_read_gestion" pour indiquer si une action a �t� lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_actions ADD actions_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_actions add actions_read_gestion");
			// MHo - Ajout d'une colonne "dmde_read_gestion" pour indiquer si une demande contient des �l�ments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_gestion");

			// MHo - Ajout d'une colonne "reponse_finale" contenant la r�ponse finale qui sera int�gr�e � la faq
			$rqt = "ALTER TABLE demandes ADD reponse_finale TEXT NULL";
			echo traite_rqt($rqt,"alter demandes add reponse_finale");

			// DG - Le super user doit avoir acc�s � tous les �tablissements
			$rqt = "UPDATE entites SET autorisations=CONCAT(' 1', autorisations) WHERE type_entite='1' AND autorisations NOT LIKE '% 1 %'";
			echo traite_rqt($rqt, 'UPDATE entites SET autorisations=CONCAT(" 1",autorisations) for super user');

			// AR - Module FAQ - Param�tre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'faq' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'faq', 'active', '0', 'Module \'FAQ\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
				echo traite_rqt($rqt, "insert faq_active=0 into parameters");
			}

			// AR - Cr�ation de la table des types pour la FAQ
			$rqt = " CREATE TABLE faq_types(
				id_type int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_type varchar(255) NOT NULL default '',
	        	PRIMARY KEY  (id_type) )";
			echo traite_rqt($rqt,"CREATE TABLE faq_types") ;

			// AR - Cr�ation de la table des th�mes pour la FAQ
			$rqt = " CREATE TABLE faq_themes(
				id_theme int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_theme varchar(255) NOT NULL default '',
	    	    PRIMARY KEY  (id_theme))";
			echo traite_rqt($rqt,"CREATE TABLE faq_themes") ;

			// AR - Cr�ation de la table pour la FAQ
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

			// AR - Cr�ation de la table de descripteurs pour la FAQ
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

			// MHo - Ajout d'une colonne "dmde_read_opac" pour alerter � l'opac en cas d'�l�ments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_opac");

			// MHo - Ajout d'une colonne "notes_read_opac" pour alerter � l'opac en cas de nouveaut� : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add notes_read_opac");

			// DB -Ajout d'une fonction sp�cifique pour g�n�ration de code-barres lecteurs
			$rqt = "update parametres set comment_param='Num�ro de carte de lecteur automatique ?\n 0: Non (si utilisation de cartes pr�-imprim�es)\n";
			$rqt.= " 1: Oui, enti�rement num�rique\n 2,a,b,c: Oui, avec pr�fixe: a=longueur du pr�fixe, b=nombre de chiffres de la partie num�rique, c=pr�fixe fix� (facultatif)\n";
			$rqt.= " 3,fonction: fonction de g�n�ration sp�cifique dans fichier nomm� de la m�me fa�on, � placer dans pmb/circ/empr' ";
			$rqt.= " where type_param='pmb' and sstype_param='num_carte_auto'";
			echo traite_rqt($rqt,"update parametre pmb_num_carte_auto ");

			// AB On augmente la taille des champs pour le num demandeur ....
			$rqt = "ALTER TABLE demandes CHANGE num_demandeur num_demandeur INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes change num_demandeur");
			$rqt = "ALTER TABLE demandes_actions CHANGE actions_num_user actions_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_actions change actions_num_user");
			$rqt = "ALTER TABLE demandes_notes CHANGE notes_num_user notes_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_notes change notes_num_user");

			//DB - G�n�ration code-barres pour les inscritions Web
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_num_carte_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'opac', 'websubscribe_num_carte_auto', '', 'Num�ro de carte de lecteur automatique ?\n 2,a,b,c: Oui avec pr�fixe: a=longueur du pr�fixe, b=nombre de chiffres de la partie num�rique, c=pr�fixe fix� (facultatif)\n 3,fonction: fonction de g�n�ration sp�cifique dans fichier nomm� de la m�me fa�on, � placer dans pmb/opac_css/circ/empr', 'f_modules', '0')" ;
				echo traite_rqt($rqt,"insert opac_websubscribe_num_carte_auto into parametres") ;
			}

			// AB
			$rqt = "CREATE TABLE IF NOT EXISTS onto_uri (
					uri_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
					uri VARCHAR(255) NOT NULL UNIQUE DEFAULT '' )";
			echo traite_rqt($rqt,"create table onto_uri") ;

			//DB - G�n�ration de cartes lecteurs sur imprimante ticket
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_handler' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_handler', '', 'Gestionnaire d\'impression :\n\n 1 = script \"print_cb.php\"\n 2 = applet jzebra\n 3 = requ�te ajax','',0)";
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
				$rqt.= "VALUES (NULL, 'pmb', 'notice_img_folder_id', '0', 'Identifiant du r�pertoire d\'upload des vignettes de notices', '', '0')" ;
				echo traite_rqt($rqt,"insert pmb_notice_img_folder_id into parametres") ;
			}

			//AR - On ajoute une colonne pour l'inscription en ligne � l'OPAC (pour conserver ce que l'on faisait)
			$rqt = "alter table empr add empr_subscription_action text";
			echo traite_rqt($rqt,"alter table empr add empr_subscription_action");

			//AR - Modification du param�tre opac_websubscribe_show
			$rqt = "update parametres set comment_param = 'Afficher la possibilit� de s\'inscrire en ligne ?\n0: Non\n1: Oui\n2: Oui + proposition s\'incription sur les r�servations/abonnements' where type_param='opac' and sstype_param = 'websubscribe_show'";
			echo traite_rqt($rqt,"update parametres opac_websubscribe_show");

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_template' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_template',0,'Choix du template d\'affichage des notices en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_template into parametres");
			}

			//AB comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_nb' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_nb',5,'Nombre de notices � afficher et � raffraichir en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_nb into parametres");
			}

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compare_notice_active' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','compare_notice_active',1,'Activer le comparateur de notices','c_recherche',0)";
				echo traite_rqt($rqt,"insert opac_compare_notice_active into parametres");
			}
			// NG - Transfert: m�morisation de la loc d'origine des exemplaires en transfert
			$rqt = "CREATE TABLE if not exists transferts_source (
				trans_source_numexpl INT UNSIGNED NOT NULL default 0 ,
				trans_source_numloc INT UNSIGNED NOT NULL default 0 ,
				PRIMARY KEY(trans_source_numexpl))";
			echo traite_rqt($rqt,"CREATE TABLE transferts_source ") ;

			// NG - Ajout dans les archives de pr�t les localisations du pret et de la loc d'origine de l'exemplaire
			$rqt = "alter table pret_archive add arc_expl_location_retour INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_retour");
			$rqt = "alter table pret_archive add arc_expl_location_origine INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_origine");

			//DG - Augmentation de la taille du champ pour les �quations
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

			//AB table de construction d'une vedette compos�e
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

			//AP cr�ation de la table de lien entre vedettes et autorit�s
			$rqt = "CREATE TABLE if not exists vedette_link (
				num_vedette INT UNSIGNED NOT NULL ,
				num_object INT UNSIGNED NOT NULL ,
				type_object INT UNSIGNED NOT NULL ,
				PRIMARY KEY (num_vedette, num_object, type_object))";
			echo traite_rqt($rqt,"create table vedette_link");

			// AP script de v�rification de saisie des autorit�s
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='autorites_verif_js' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
						VALUES ( 'pmb', 'autorites_verif_js', '', 'Script de v�rification de saisie des autorit�s','', 0)";
				echo traite_rqt($rqt,"insert autorites_verif_js into parametres");
			}

			//AB param�tre pour masquer/afficher la reservation par panier
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='resa_cart' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','resa_cart',1,'Param�tre pour masquer/afficher la reservation par panier\n0 : Non \n1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_resa_cart into parametres");
			}

			// AR - Report du param�tre activant le stemming en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_stemming_active' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_stemming_active',0,'Activer le stemming dans la recherche\n0 : D�sactiver\n1 : Activer','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_stemming_active=0 into parametres") ;
			}

			// AR - Report du param�tre excluant des champ dans la recherche tous les champs en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_exclude_fields' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_exclude_fields','','Identifiants des champs � exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_exclude_fields into parametres") ;
			}

			//AR - Report du param�tre d'�cretage des r�sultats de recherche en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_noise_limit_type' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_noise_limit_type', '0', 'Ecr�ter les r�sulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du r�sultat tout ce qui est en dessous de la moyenne - l\'�cart-type\n2,ratio : Retirer du r�sultat tout ce qui est en dessous de la moyenne - un ratio de l\'�cart-type (ex: 2,1.96)\n3,ratio : Retirer du r�sultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 �limine tout ce qui est inf�rieur � 25% de la plus forte pertinence)' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_search_noise_limit_type='0' into parametres ");
			}

			//AR - Report de la prise en compte de la fr�quence d'apparition d'un mot dans le fonds pour le calcul de pertinence en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_relevant_with_frequency' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_relevant_with_frequency', '0', 'Utiliser la fr�quence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_relevant_with_frequency='0' into parametres ");
			}

			//AR - Report du param�tre g�rant la troncature � droite automatique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_term_troncat_search' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'allow_term_troncat_search', '0', 'Troncature � droite automatique :\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_allow_term_troncat_search='0' into parametres ");
			}

			//AR - Report du param�tre g�rant la dur�e du cache des recherches
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_cache_duration' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_cache_duration', '0', 'Dur�e de validit� (en secondes) du cache des recherches' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_cache_duration='0' into parametres ");
			}

			//DG - En impression de panier, imprimer les exemplaires est coch� par d�faut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='print_expl_default' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coch� par d�faut \n 0 : Non \n 1 : Oui','',0) " ;
				echo traite_rqt($rqt,"insert pmb_print_expl_default = 0 into parametres");
			}


			//AR - Activation des concepts ou non
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_active' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_active', '0', 'Active ou non l\'utilisation des concepts:\n0 : Non\n1 : Oui', 'concepts', '0')";
				echo traite_rqt($rqt,"insert thesaurus_concepts_active='0' into parametres ");
			}

			//AP - Param�trage de l'ordre d'affichage des concepts d'une notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_affichage_ordre' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_affichage_ordre', '0', 'Param�trage de l\'ordre d\'affichage des cat�gories d\'une notice.\nPar ordre alphab�tique: 0(par d�faut)\nPar ordre de saisie: 1', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_affichage_ordre into parametres ");
			}

			//AP - Param�trage du mode d'affichage des concepts d'une notice (en ligne ou pas)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_concept_in_line' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_concept_in_line', '0', 'Affichage des cat�gories en ligne.\n 0 : Non.\n 1 : Oui.', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_concept_in_line into parametres ");
			}

			//AB Checkbox pour r�afficher les notices dans chaque groupement ou pas
			$rqt = "ALTER TABLE bannettes ADD display_notice_in_every_group INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER group_pperso";
			echo traite_rqt($rqt,"alter table bannettes add display_notice_in_every_group");

			//NG - Autorit�s personalis�es
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

			//NG - Champs perso des autorit�s personalis�es
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

			// NG : Onglet personalis� de notice
			$rqt = "create table if not exists notice_onglet (
				id_onglet int(10) unsigned NOT NULL auto_increment,
				onglet_name varchar(255) default NULL,
				PRIMARY KEY  (id_onglet)) ";
			echo traite_rqt($rqt,"create table if not exists notice_onglet ");

			//DG - Personnalisation des colonnes pour l'affichage des �tats des collections
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_data' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'collstate_data', '', 'Colonne des �tats des collections, dans l\'ordre donn�, s�par� par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note\nLes valeurs possibles sont les propri�t�s de la classe PHP \"pmb/opac_css/classes/collstate.class.php\".','e_aff_notice',0)";
				echo traite_rqt($rqt,"insert opac_collstate_data = 0 into parametres");
			}

			//AB ajout d'un schema SKOS par d�faut
			$rqt = "ALTER TABLE users ADD deflt_concept_scheme INT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_thesaurus";
			echo traite_rqt($rqt,"alter table users add deflt_concept_scheme");

			//AB param�tre cach� pour conservation de la date de derni�re modification de l'ontologie
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='ontology_filemtime' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('thesaurus','ontology_filemtime',0,'Param�tre cach� pour conservation de la date de derni�re modification de l\'ontologie','ontologie',1)";
				echo traite_rqt($rqt,"insert thesaurus_ontology_filemtime into parametres");
			}

			// NG - Ajout du champ resa_arc_trans pour associer un transfert � une archive r�sa
			$rqt = "ALTER TABLE transferts_demande ADD resa_arc_trans int(8) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter table transferts_demande add resa_arc_trans ");

			// NG - Ajout champ info dans les audits
			$rqt = "ALTER TABLE audit ADD info text NOT NULL";
			echo traite_rqt($rqt,"alter table audit add info ");

			// AP modification du param�tre de schema SKOS par d�faut
			$rqt = "ALTER TABLE users CHANGE deflt_concept_scheme deflt_concept_scheme INT(3) NOT NULL DEFAULT -1";
			echo traite_rqt($rqt,"alter table users change deflt_concept_scheme");

			//DG - Statuts sur les documents num�riques
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

			//DG - Statut "Sans statut particulier" ajout� par d�faut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from explnum_statut where id_explnum_statut=1"))==0){
				$rqt = "insert into explnum_statut SET id_explnum_statut=1, gestion_libelle='Sans statut particulier',opac_libelle='', explnum_visible_opac='1' ";
				echo traite_rqt($rqt,"insert minimum into explnum_statut");
			}

			//DG - Ajout d'un champ statut sur les documents num�riques
			$rqt = "ALTER TABLE explnum ADD explnum_docnum_statut smallint(5) UNSIGNED NOT NULL DEFAULT 1 ";
			echo traite_rqt($rqt,"alter table explnum add explnum_docnum_statut ");

			//DG - Statut de document num�rique par d�faut en cr�ation de document num�rique
			$rqt = "ALTER TABLE users ADD deflt_explnum_statut INT(6) UNSIGNED DEFAULT 1 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_explnum_statut ");

			//AR - param�trages des droits d'acc�s sur les documents num�riques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum' "))==0){
			$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum',0,'Gestion des droits d\'acc�s des emprunteurs aux documents num�riques\n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum_def' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum_def',0,'Valeur par d�faut en modification de document num�rique pour les droits d\'acc�s emprunteurs - documents num�riques\n0 : Recalculer.\n1 : Choisir.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum_def into parametres");
			}

			// NG - Ajout param transferts_retour_action_resa
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_action_resa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
				VALUES (0, 'transferts', 'retour_action_resa', '1', '1', 'G�n�re un transfert pour r�pondre � une r�servation lors du retour de l\'exemplaire\n 0: Non\n 1: Oui') ";
				echo traite_rqt($rqt,"INSERT transferts_retour_action_resa INTO parametres") ;
			}

			//DG - Logs OPAC - Exclusion possible des robots et de certaines adresses IP
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='logs_exclude_robots' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'logs_exclude_robots', '1', 'Exclure les robots dans les logs OPAC ?\n 0: Non\n 1: Oui. \nFaire suivre d\'une virgule pour �ventuellement exclure les logs OPAC provenant de certaines adresses IP, elles-m�mes s�par�es par des virgules (ex : 1,127.0.0.1,192.168.0.1).','',0)";
				echo traite_rqt($rqt,"insert pmb_logs_exclude_robots = 1 into parametres");
			}

			// NG - Auteurs r�p�tables dans les titres uniformes
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
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from serialcirc_tpl where serialcirctpl_id=1"))==0){
				$rqt = "insert into serialcirc_tpl SET serialcirctpl_id=1, serialcirctpl_name='Template PMB', serialcirctpl_comment='', serialcirctpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
				echo traite_rqt($rqt,"insert minimum into serialcirc_tpl");
			}

			//DG - Circulation des p�riodiques : Tri sur les destinataires
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
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from bannette_tpl where bannettetpl_id=1"))==0){
				$rqt = "insert into bannette_tpl SET bannettetpl_id=1, bannettetpl_name='Template PMB', bannettetpl_comment='', bannettetpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
				echo traite_rqt($rqt,"insert minimum into bannette_tpl");
			}

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

			// NG - Ajout param�tre pour activer la g�olocalisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_activate', '0', 'Activation de la g�olocalisation:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_map_activate into parametres");
			}

			//MB + CB - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas d�j� (li� aux am�liorations pour les transferts)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, transfert_statut_origine=expl_statut, update_date=update_date WHERE transfert_location_origine=0 AND transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine transfert_statut_origine");
			//NG - g�olocalisation
			$rqt = "ALTER TABLE notices ADD map_echelle_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_echelle_num ");

			$rqt = "ALTER TABLE notices ADD map_projection_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_projection_num ");

			$rqt = "ALTER TABLE notices ADD map_ref_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_ref_num ");

			$rqt = "ALTER TABLE notices ADD map_equinoxe varchar(255) NOT NULL DEFAULT ''" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_equinoxe ");



			//NG - g�olocalisation: Memo des emprises
			$rqt = "CREATE TABLE if not exists map_emprises (
					map_emprise_id int(10) unsigned NOT NULL auto_increment,
					map_emprise_type int(10) unsigned NOT NULL default 0,
					map_emprise_obj_num int(10) unsigned NOT NULL default 0,
					map_emprise_data GEOMETRY NOT NULL,
					map_emprise_order int(10) unsigned NOT NULL default 0,
					PRIMARY KEY  (map_emprise_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_emprises") ;

			//NG - g�olocalisation: Echelles
			$rqt = "CREATE TABLE if not exists map_echelles (
					map_echelle_id int(10) unsigned NOT NULL auto_increment,
					map_echelle_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_echelle_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_echelles") ;

			//NG - g�olocalisation: Syst�me de projection du document
			$rqt = "CREATE TABLE if not exists map_projections (
					map_projection_id int(10) unsigned NOT NULL auto_increment,
					map_projection_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_projection_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_projections") ;

			//NG - g�olocalisation: Systeme de r�f�rence de coord de la carte
			$rqt = "CREATE TABLE if not exists map_refs (
					map_ref_id int(10) unsigned NOT NULL auto_increment,
					map_ref_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_ref_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_refs") ;

			// AR - Ajout param�tre pour limiter le nombre d'emprises sur une carte !
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhait� par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_max_holds into parametres");
			}

			// AR - Les param�tres de cartes sont rang�s ensemble !
			$rqt = "update parametres set section_param= 'map' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_max_holds");

			// AR - D�finition de la couleur d'une emprise de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associ�es � des notices','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_record_color into parametres");
			}

			// AR - D�finition de la couleur d'une emprise d'autorit�
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associ�es � des autorit�s','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_authority_color into parametres");
			}


			// AR - D�finition du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_type', 'OSM', 'Fonds de carte � utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_type into parametres");
			}
			// AR - D�finition des param�tres du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_params', '', 'Structure JSON � passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_params into parametres");
			}

			// NG - Ajout param�tre de la taille de la carte en saisie de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_edition into parametres");
			}

			// NG - Ajout param�tre de la taille de la carte en r�sultat de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_result', '800*480', 'Taille de la carte en r�sultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_result into parametres");
			}
			// NG - Ajout param�tre de la taille de la carte en visualisation de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_notice_view into parametres");
			}

			// NG - Ajout param�tre de la taille de la carte en �dition de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_edition', '800*480', 'Taille de la carte en �dition de notice','map', 0)";
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

			//MB - D�finition de la taille maximum des vignettes des notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_img_pics_max_size' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'notice_img_pics_max_size', '150', 'Taille maximale des vignettes upload�es dans les notices, en largeur ou en hauteur')";
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
								//Dans le cas o� une modification a �t� faite avant le passage de la MAJ..
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

			// NG - Ajout param�tre pour activer la g�olocalisation en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_activate', '0', 'Activation de la g�olocalisation:\n 0 : non \n 1 : oui','a_general', 0)";
				echo traite_rqt($rqt,"insert opac_map_activate into parametres");
			}

			//DB - commande psexec (planificateur sous windows)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='psexec_cmd' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'psexec_cmd', 'psexec -d', 'Param�tres de lancement de psexec (planificateur sous windows)\r\n\nAjouter l\'option -accepteula sur les versions les plus r�centes. ', '',0) ";
				echo traite_rqt($rqt, "insert pmb_psexec_cmd into parameters");
			}

			// AR - Ajout param�tre pour activer l'�diteur Dojo
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='editorial_dojo_editor' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'editorial_dojo_editor', '1', 'Activation de l\'�diteur DoJo dans le contenu �ditorial:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_editorial_dojo_editor into parametres");
			}

			// DG - Module "Surcharge de m�ta-donn�es" : Groupes de m�ta-donn�es par d�faut
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
										'desc' => "R�sum�",
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
										'desc' => "R�sum�",
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

			// AR - Ajout param�tre pour limiter le nombre d'emprises sur une carte � l'OPAC!
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhait� par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_max_holds into parametres");
			}

			// AR - Les param�tres de cartes sont rang�s ensemble !
			$rqt = "update parametres set section_param= 'map', comment_param='Activation du g�or�f�rencement' where type_param like 'opac' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update opac_map_activate");

			// AR - Changement de nom !
			$rqt = "update parametres set comment_param='Activation du g�or�f�rencement' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_activate");

			// AR - D�finition de la couleur d'une emprise de notice � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associ�es � des notices','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_record_color into parametres");
			}

			// AR - D�finition de la couleur d'une emprise d'autorit� � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associ�es � des autorit�s','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_authority_color into parametres");
			}

			// AR - Ajout param�tre de la taille de la carte en saisie de recherche � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_edition into parametres");
			}

			// AR - Ajout param�tre de la taille de la carte en r�sultat de recherche � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_result', '800*480', 'Taille de la carte en r�sultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_result into parametres");
			}
			// AR - Ajout param�tre de la taille de la carte en visualisation de notice � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_notice_view into parametres");
			}

			// AR - D�finition du fond de carte � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_type', 'OSM', 'Fonds de carte � utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_type into parametres");
			}
			// AR - D�finition des param�tres du fond de carte � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_params', '', 'Structure JSON � passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_params into parametres");
			}

			// JP - Suggestions - Utilisateur : pouvoir �tre alert� en cas de nouvelle suggestion � l'OPAC
			$rqt = "ALTER TABLE users ADD user_alert_suggmail int(1) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter table users add user_alert_suggmail");

			// JP - Acquisitions - S�lection rubrique budg�taire en commande : pouvoir toutes les afficher
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='budget_show_all' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'acquisition', 'budget_show_all', '0', 'S�lection d\'une rubrique budg�taire en commande : toutes les afficher ?\n 0: Non (par pagination)\n 1: Oui.','',0)";
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

			//MB - Ajout index sur le nom des fichiers num�riques pour acc�l�rer la recherche
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

			//JP - Ajout deux index sur les liens entre actes pour acc�l�rer la recherche
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

			//JP - Nettoyage des cat�gories sans libell�
			$rqt ="DELETE FROM categories WHERE libelle_categorie=''";
			echo traite_rqt($rqt,"Delete categories sans libell�");

			// JP - Abonnements - nom du p�riodique par d�faut en cr�ation d'abonnement
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='abt_label_perio' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
							VALUES (0, 'pmb', 'abt_label_perio', '0', 'Cr�ation d\'un abonnement : reprendre le nom du p�riodique ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert pmb_abt_label_perio = 0 into parametres");
			}

			// JP - Acquisitions - afficher le nom de l'abonnement dans les lignes de la commande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='show_abt_in_cmde' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'acquisition', 'show_abt_in_cmde', '0', 'Afficher l\'abonnement dans les lignes de la commande ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert acquisition_show_abt_in_cmde = 0 into parametres");
			}

			// NG - Ajout table nomenclature_families: Nomenclatures / Familles
			// id_family Identifiant unique de la famille
			// family_name: Nom de la famille
			// family_order: ordre dans l'affichage
			$rqt = "CREATE TABLE if not exists nomenclature_families (
					id_family int unsigned NOT NULL auto_increment,
					family_name varchar(255) NOT NULL DEFAULT '',
					family_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_family))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_families");

			// NG - Ajout table nomenclature_musicstands: Nomenclatures / pupitres
			// id_musicstand: Identifiant unique du pupitre
			// musicstand_name: Nom du pupitre
			// musicstand_famille_num: Cl� �trang�re nomenclature_families.id_family
			// musicstand_division: ( divisible ? 0: non, 1: oui )
			// musicstand_workshop: ( associ� aux ateliers ? 0: non, 1: oui )
			// musicstand_order: ordre dans l'affichage
			$rqt = "CREATE TABLE if not exists nomenclature_musicstands (
					id_musicstand int unsigned NOT NULL auto_increment,
					musicstand_name varchar(255) NOT NULL DEFAULT '',
					musicstand_famille_num int unsigned NOT NULL DEFAULT 0,
					musicstand_division int unsigned NOT NULL DEFAULT 0,
					musicstand_order int unsigned NOT NULL DEFAULT 0,
					musicstand_workshop int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_musicstand))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_musicstands");

			// NG - Ajout table nomenclature_instruments: Nomenclatures: / instruments
			// id_instrument: Identifiant unique instrument
			// instrument_code: code instrument
			// instrument_name: nom instrument
			// instrument_musicstand_num: Cl� �trang�re nomenclature_musicstands.id_musicstand
			// instrument_standard: ( standard ? 0: non, 1: oui )
			$rqt = "CREATE TABLE if not exists nomenclature_instruments (
					id_instrument int unsigned NOT NULL auto_increment,
					instrument_code varchar(255) NOT NULL DEFAULT '',
					instrument_name varchar(255) NOT NULL DEFAULT '',
					instrument_musicstand_num int unsigned NOT NULL DEFAULT 0,
					instrument_standard int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_instruments");

			// NG - Ajout table nomenclature_formations: Nomenclatures / formations
			// id_formation: Identifiant unique formation
			// formation_name: Nom formation
			// formation_nature: ( nature ? 0: Instruments , 1: Voix )
			// formation_order: ordre dans l'affichage
			$rqt = "CREATE TABLE if not exists nomenclature_formations (
					id_formation int unsigned NOT NULL auto_increment,
					formation_name varchar(255) NOT NULL DEFAULT '',
					formation_nature int unsigned NOT NULL DEFAULT 0,
					formation_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_formation))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_formations");

			// NG - Ajout table nomenclature_types: Nomenclatures: / Types
			// id_type: Identifiant unique type
			// type_name: nom type
			// type_formation_num: Cl� �trang�re nomenclature_formations.id_formation
			// type_order: ordre dans l'affichage
			$rqt = "CREATE TABLE if not exists nomenclature_types (
					id_type int unsigned NOT NULL auto_increment,
					type_name varchar(255) NOT NULL DEFAULT '',
					type_formation_num int unsigned NOT NULL DEFAULT 0,
					type_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_type))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_types");

			// NG - Ajout table nomenclature_voices: Nomenclatures: / voix
			// id_voice: Identifiant unique instrument
			// voice_code: code voix
			// voice_name: nom voix
			// voice_order: ordre dans l'affichage
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

			// NG - Ajout param�tre pour identifier le type de relation entre une notice de nomenclature et ses notices filles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_record_children_link' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_record_children_link', '', 'Type de relation entre une notice de nomenclature et ses notices filles.','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_record_children_link");
			}

			// NG - Ajout param�tre pour activer les nomenclatures
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_activate', '0', 'Activation des nomenclatures:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_activate into parametres");
			}

			//MHo - Augmentation de la taille du champ pour les titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL");

			// MB - Affichage liste des bulletins - Modification explication du param�tre
			$rqt = "UPDATE parametres SET comment_param='Fonction d\'affichage de la liste des bulletins d\'un p�riodique\nValeurs possibles:\naffichage_liste_bulletins_normale (Si param�tre vide)\naffichage_liste_bulletins_tableau\naffichage_liste_bulletins_depliable' WHERE type_param= 'opac' and sstype_param='fonction_affichage_liste_bull'";
			echo traite_rqt($rqt,"UPDATE parametres opac_fonction_affichage_liste_bull");

			// VT & DG - Cr�ation des tables de veilles
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

			// NG - Demandes: Ajout d'un param�tre permettant de saisir un email g�n�rique pour la gestion des demanades
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_generic' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'demandes', 'email_generic', '',
				'Information par un email g�n�rique de l\'�volution des demandes.\n 1,adrmail@mail.fr : Envoi une copie uniquement pour toutes les nouvelles demandes\n 2,adrmail@mail.fr : Envoi une copie uniquement des mails envoy�s aux personnes affect�es\n 3,adrmail@mail.fr : Envoi une copie dans les 2 cas pr�c�dents\n ',
				'',0) ";
				echo traite_rqt($rqt, "insert demandes_email_generic into parameters");
			}

			// NG - Demandes: Ajout d'un param�tre permettant d'afficher le format simplifi� en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_affichage_simplifie' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_affichage_simplifie', '0',
				'Active le format simplifi� des demandes en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_affichage_simplifie into parameters");
			}

			// NG - Demandes: Ajout d'un param�tre permettant d'interdire l'ajout d'une action en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_no_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_no_action', '0',
				'Interdire l\'ajout d\'une action en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_no_action into parameters");
			}

			// NG - Demandes: lien entre la note g�n�rant la r�ponse finale d'une demande
			$rqt = "ALTER TABLE demandes ADD demande_note_num int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes ADD demande_note_num ");

			//JP - Modification de la longueur du champ email de la table coordonnees
			$rqt = "ALTER TABLE coordonnees MODIFY email varchar(255) NOT NULL default '' ";
			echo traite_rqt($rqt,"alter table coordonnees modify email");

			// DG - Veilles : Option pour nettoyer le contenu HTML des nouveaux �l�ments
			$rqt = "ALTER TABLE docwatch_datasources ADD datasource_clean_html int unsigned not null default 1 after datasource_default_interesting" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_clean_html ");

			// VT - Ajout param�tre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_min' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_min into parametres");
			}

			// VT - Ajout param�tre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_max' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_max into parametres");
			}

			// VT - Ajout param�tre pour definir le rapport de distance entre deux points pour qu'ils soit aggr�g�s ensembles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_distance' "))==0){
					$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agr�ger','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_distance into parametres");
			}

			// VT - Creation table de correspondance contenant les aires des diff�rentes emprises de la base
			$rqt="create table if not exists map_hold_areas as (select map_emprise_id as id_obj, map_emprise_type as type_obj, Area(map_emprise_data) as area, Area(envelope(map_emprise_data)) as bbox_area, AsText(Centroid(envelope(map_emprise_data))) as center from map_emprises)";
			echo traite_rqt($rqt, "create table map_hold_areas");

			//VT - Verification de l'existance de la cl� primaire (cr�ation si non-existante)
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
				VALUES (0, 'opac', 'modules_search_concept', '0', 'Recherche dans les concepts : \n 0 : interdite, \n 1 : autoris�e, \n 2 : autoris�e et valid�e par d�faut', 'c_recherche', 0) ";
				echo traite_rqt($rqt, "insert opac_modules_search_concept into parameters");
			}

			// VT - Ajout param�tre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_min' "))==0) {
				$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ('opac', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0) ";
				echo traite_rqt($rqt, "insert opac_map_hold_ratio_min into parametres");
			}

			// VT - Ajout param�tre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_max' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_ratio_max into parametres");
			}

			// VT - Ajout param�tre pour definir le rapport de distance entre deux points pour qu'ils soit aggr�g�s ensembles (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_distance' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agr�ger','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_distance into parametres");
			}

			// VT - Ajout d'un index sur la colonne map_emprise_obj_num de la table map_emprises
			$rqt="alter table map_emprises add index i_map_emprise_obj_num(map_emprise_obj_num)";
			echo traite_rqt($rqt, "alter table map_emprises add index i_map_emprise_obj_num");

			// JP - Ajout champ de classement sur �tag�res et paniers
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

			//DG - Accessibilit� OPAC : Param�tre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='accessibility' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'accessibility', '1', 'Accessibilit� activ�e.\n 0 : Non.\n 1 : Oui.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_accessibility = 1 into parametres");
			}

			//JP - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas d�j� (ajout sur la requ�te en v5.17)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, update_date=update_date WHERE transfert_location_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine");

			$rqt = "UPDATE exemplaires SET transfert_statut_origine=expl_statut, update_date=update_date WHERE transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_statut_origine");

			// NG - Ajout param�tre indiquant la dur�e en jours de conservation des notices en tant que nouveaut�
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='newrecord_timeshift' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'newrecord_timeshift', '0', 'Nombre de jours de conservation des notices en tant que nouveaut�.','', 0)";
				echo traite_rqt($rqt,"insert pmb_newrecord_timeshift");
			}

			// Cr�ation shorturls
			$rqt="create table if not exists shorturls (
				id_shorturl int unsigned not null auto_increment primary key,
				shorturl_hash varchar(255) not null default '',
				shorturl_last_access datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				shorturl_context text not null,
				shorturl_type varchar(255) not null default '',
				shorturl_action varchar(255) not null default ''
			)";
			echo traite_rqt($rqt,"create table shorturls");

			// NG - Nouveaut�s
			$rqt = "ALTER TABLE notices ADD notice_is_new int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_is_new ");

			$rqt = "ALTER TABLE notices ADD notice_date_is_new  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_date_is_new ");

			// VT - Modif du param�tre map_max_holds en gestion (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'pmb' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donn� s�par� par une virgule: Nombre limite d\'emprises affich�es, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affich�es\n\n1 => Clusterisation de toutes les emprises' where type_param like 'pmb' and sstype_param like 'map_max_holds'";
				echo traite_rqt($rqt, "update parametres map_max_holds gestion");
			}

			// VT - Modif du param�tre map_max_holds en opac (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'opac' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donn� s�par� par une virgule: Nombre limite d\'emprises affich�es, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affich�es\n\n1 => Clusterisation de toutes les emprises' where type_param like 'opac' and sstype_param like 'map_max_holds'";
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
			$rqt = "update parametres set comment_param='Format d\'affichage des notices en r�sultat de recherche\n 0 : Utiliser le param�tre notices_format_onglets\n 1 : ISBD seul\n 2 : Public seul \n4 : ISBD et Public\n 5 : ISBD et Public avec ISBD en premier \n8 : R�duit (titre+auteurs) seul\n 9 : Templates django (Sp�cifier le nom du r�pertoire dans le param�tre notices_format_django_directory)' where type_param= 'opac' and sstype_param='notices_format' ";
			echo traite_rqt($rqt,"update opac_notices_format into parametres");

			// AP - Ajout param�tre indiquant le nom du r�pertoire des templates django � utiliser en affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_django_directory' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'notices_format_django_directory', '', 'Nom du r�pertoire de templates django � utiliser en affichage de notice.\nLaisser vide pour utiliser le common.','e_aff_notice', 0)";
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

			//DG - Param�tre OPAC : Autoriser le t�l�chargement des documents num�riques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_download_docnums' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'allow_download_docnums', '1', 'Autoriser le t�l�chargement des documents num�riques.\n 0 : Non.\n 1 : Individuellement (un par un).\n 2 : Archive ZIP.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_allow_download_docnums = 1 into parametres");
			}

			//AB - Le nom du fichier de param�trage du selecteur d'affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_display_modes' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'notices_display_modes', '', 'Nom du fichier xml de param�trage du choix du mode d\'affichage des notices � l\'OPAC.\nPar d�faut : display_modes_exemple.xml dans /opac_css/includes/records/','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_notices_display_modes='' into parametres");
			}

			//DG - Lien pour en savoir plus sur l'utilisation des cookies et des traceurs
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='url_more_about_cookies' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'url_more_about_cookies', '', 'Lien pour en savoir plus sur l\'utilisation des cookies et des traceurs','a_general',0)";
				echo traite_rqt($rqt,"insert opac_url_more_about_cookies into parametres");
			}

			//DG - MAJ du template de bannettes par d�faut (identifiant 1)
			$rqt = "UPDATE bannette_tpl SET bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n{% for sommaire in sommaires %}\r\n<a href=\"#[{{sommaire.level}}]\">\r\n{{sommaire.level}} - {{sommaire.title}}\r\n</a>\r\n<br />\r\n{% endfor %}\r\n</div>\r\n<hr>\r\n{% for sommaire in sommaires %}\r\n<a name=\"[{{sommaire.level}}]\" />\r\n<h1>{{sommaire.level}} - {{sommaire.title}}</h1>\r\n{% for record in sommaire.records %}\r\n{{record.render}}\r\n<hr>\r\n{% endfor %}\r\n<br />\r\n{% endfor %}\r\n{{info.footer}}'
					WHERE bannettetpl_id=1";
			echo traite_rqt($rqt,"ALTER minimum into bannette_tpl");

			// DB - Modification de la table resa_planning (pr�visions localis�es)
			$rqt = "alter table resa_planning add resa_loc_retrait int(5) unsigned not null default 0 ";
			echo traite_rqt($rqt,"alter resa_planning add resa_loc_retrait ");

			// JP - Ajout champ demande abonnement sur p�riodique
			$rqt = "ALTER TABLE notices ADD opac_serialcirc_demande TINYINT UNSIGNED NOT NULL DEFAULT 1";
			echo traite_rqt($rqt,"ALTER TABLE notices ADD opac_serialcirc_demande") ;

			// JP - Ajout champ de classement sur infopages
			$rqt = "ALTER TABLE infopages ADD infopage_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE infopages ADD infopage_classement ");

			// JP - Ajout autorisations sur recherches pr�d�finies gestion
			$rqt = "ALTER TABLE search_perso ADD autorisations MEDIUMTEXT NULL DEFAULT NULL ";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD autorisations") ;

			$rqt = "UPDATE search_perso SET autorisations=num_user ";
			echo traite_rqt($rqt,"UPDATE autorisations INTO search_perso");

			//VT - Param�tre OPAC : Definition du chemin des templates d'autorit�s en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='authorities_templates_folder' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'authorities_templates_folder', './includes/templates/authorities/common', 'Repertoire des templates utilis�s pour l\'affichage des autorit�s en OPAC','',1)";
				echo traite_rqt($rqt,"insert opac_authorities_templates_folder = ./includes/templates/authorities/common into parametres");
			}

			// JP - template par d�faut pour les bannettes priv�es
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_notices_template' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'dsi', 'private_bannette_notices_template', '0', 'Id du template de notice utilis� par d�faut en diffusion de bannettes priv�es. Si vide ou � 0, le template classique est utilis�.', '', 0)";
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


			//AR - Param�tre Portail : Activer la mise en cache des images
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_image_cache' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_image_cache', '0', 'Activer la mise en cache des vignettes du contenu �ditorial.\n 0: non \n 1:Oui \nAttention, si l\'OPAC ne se trouve pas sur le m�me serveur que la gestion, la purge du cache ne peut pas se faire automatiquement','',0)";
				echo traite_rqt($rqt,"insert cms_active_image_cache into parametres");
			}

			// MHo - Correction des messages des parametres sur l'ordre d'affichage et le mode d'affichage des concepts d'une notice (remplacement de "categorie" par "concept")
			$rqt="UPDATE parametres SET comment_param='Param�trage de l\'ordre d\'affichage des concepts d\'une notice.\nPar ordre alphab�tique: 0(par d�faut)\nPar ordre de saisie: 1'
				WHERE type_param='thesaurus' AND sstype_param='concepts_affichage_ordre' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_affichage_ordre into parametres ");

			$rqt="UPDATE parametres SET comment_param='Affichage des concepts en ligne.\n 0 : Non.\n 1 : Oui.'
				WHERE type_param='thesaurus' AND sstype_param='concepts_concept_in_line' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_concept_in_line into parametres ");

			//DG - Flag pour savoir si le mot de passe est d�j� encrypt�
			$rqt= "alter table empr add empr_password_is_encrypted int(1) not null default 0 after empr_password";
			echo traite_rqt($rqt,"alter table empr add empr_password_is_encrypted");

			//DG - Phrase pour le hashage des mots de passe emprunteurs (param�tre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_password_salt' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'empr_password_salt', '', 'Phrase pour le hashage des mots de passe emprunteurs','a_general',1)";
				echo traite_rqt($rqt,"insert opac_empr_password_salt into parametres");
			}

			//DG - Info d'encodage des mots de passe lecteurs pour la connexion � l'Opac
			$res=pmb_mysql_query("SELECT count(*) FROM empr");
			if($res && pmb_mysql_result($res,0,0)){
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ ENCODER LES MOTS DE PASSE LECTEURS (APRES ETAPES DE MISE A JOUR) / YOU MUST ENCODE PASSWORD READERS (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}

			// JP - Parametre affichage des dates de creation et modification notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notices_show_dates' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'notices_show_dates', '0', 'Afficher les dates des notices ? \n 0 : Aucune date.\n 1 : Date de cr�ation et modification.', '',0) ";
				echo traite_rqt($rqt, "insert expl_show_dates=0 into parameters");
			}

			// AR - Param�tre pour activer la compression des CSS
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compress_css' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'compress_css', '0', 'Activer la compilation et la compression des feuilles de styles.\n0: Non\n1: Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_compress_css into parametres");
			}

			//VT - Ajout d'un champ tonalit� marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_tonalite_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_tonalite_marclist");

			//VT - Ajout d'un champ forme marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_forme_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_forme_marclist");

			// DB - Modification de la table resa_planning (quantit� pr�visions)
			$rqt = "alter table resa_planning add resa_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_qty");
			$rqt = "alter table resa_planning add resa_remaining_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_remaining_qty");
			// DB - Modification de la table resa (lien vers pr�visions)
			$rqt = "alter table resa add resa_planning_id_resa int(8) unsigned not null default 0";
			echo traite_rqt($rqt,"alter resa add resa_planning_id_resa");

			// DB - Delai d'alerte pour le transfert des previsions en reservations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_planning_toresa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'resa_planning_toresa', '10', 'D�lai d\'alerte pour le transfert des pr�visions en r�servations (en jours). ' ,'',0)";
				echo traite_rqt($rqt,"insert resa_planning_toresa into parametres");
			}

			//JP - Nettoyage vues en erreur suite ajout index unique
			$res=pmb_mysql_query("SHOW TABLES LIKE 'opac_view_notices_%'");
			if($res && pmb_mysql_num_rows($res)){
				while ($r=pmb_mysql_fetch_array($res)){
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
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=opac&sub=opac_view&section=list' target=_blank>VOUS DEVEZ RECALCULER LES VUES OPAC (APRES ETAPES DE MISE A JOUR) / YOU MUST RECALCULATE OPAC VIEWS (STEPS AFTER UPDATE) : Admin > Vues Opac > G�n�rer les recherches</a></b> ") ;
			}

			//JP - nettoyage table authorities_sources
			$rqt = "DELETE FROM authorities_sources WHERE num_authority=0";
			echo traite_rqt($rqt,"DELETE FROM authorities_sources num_authority vide");

			//JP - acc�s rapide pour les paniers de notices
			$rqt = "ALTER TABLE caddie ADD acces_rapide INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE caddie ADD acces_rapide");

			//JP - modification index notices_mots_global_index
			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			$rqt ="alter table notices_mots_global_index drop primary key";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop primary key");
			$rqt ="alter table notices_mots_global_index add primary key (id_notice,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add primary key");
			// Info de r�indexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

			//DG - Proposer la conservation de cat�gories en remplacement de notice
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
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sort_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+pr�nom \n b: code-barres \n c: cat�gories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: ann�e de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalis�s lecteurs \n #p[n] : [n] = id des champs personnalis�s pr�ts' where type_param= 'empr' and sstype_param='sort_rows' ";
				echo traite_rqt($rqt,"update empr_sort_rows into parametres");
			}

			//DG - maj valeurs possibles pour empr_filter_rows
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_rows' and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: cat�gorie\n s: statut\n g: groupe\n y: ann�e de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement \n #e[n] : [n] = id des champs personnalis�s lecteurs \n #p[n] : [n] = id des champs personnalis�s pr�ts' where type_param= 'empr' and sstype_param='filter_rows' ";
				echo traite_rqt($rqt,"update empr_filter_rows into parametres");
			}

			//DG - maj valeurs possibles pour empr_show_rows
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='show_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes affich�es en liste de lecteurs, saisir les colonnes s�par�es par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+pr�nom \n a: adresse \n b: code-barre \n c: cat�gories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: ann�e de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalis�s lecteurs \n 1: ic�ne panier' where type_param= 'empr' and sstype_param='show_rows' ";
				echo traite_rqt($rqt,"update empr_show_rows into parametres");
			}

			// AP - Cr�ation d'une table pour la gestion de la suppression des enregistrements OAI
			$rqt = "CREATE TABLE if not exists connectors_out_oai_deleted_records (
					num_set int(11) unsigned NOT NULL DEFAULT 0,
					num_notice int(11) unsigned NOT NULL DEFAULT 0,
					deletion_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (num_set, num_notice))";
			echo traite_rqt($rqt,"CREATE TABLE connectors_out_oai_deleted_records") ;

			// AP - Ajout du stockage de la grammaire d'une vedette
			$rqt = "ALTER TABLE vedette ADD grammar varchar(255) NOT NULL default 'rameau'" ;
			echo traite_rqt($rqt,"ALTER TABLE vedette ADD grammar");

			//JP - recalcul des isbn � cause du nouveau fomatage
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


			//JP - mise � jour des dates de validation des commandes
			$rqt="UPDATE actes SET date_valid=date_acte WHERE statut>1 AND date_valid='0000-00-00'";
			echo traite_rqt($rqt,"update actes date_validation ");

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.20");
			break;

		case "v5.20":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//DG - maj Colonnes exemplaires affich�es en gestion - ajout en commentaire du groupe d'exemplaires
			$rqt = "update parametres set comment_param='Colonne des exemplaires, dans l\'ordre donn�, s�par� par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle,groupexpl_name #n : id des champs personnalis�s \r\n expl_cb est obligatoire et sera ajout� si absent' where type_param= 'pmb' and sstype_param='expl_data' ";
			echo traite_rqt($rqt,"update pmb_expl_data into parametres");

			// AP - Ajout d'une colonne pour lier une notice � une demande
			$rqt = "ALTER TABLE demandes ADD num_linked_notice mediumint(8) UNSIGNED NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes ADD num_linked_notice");

			// AP - Ajout d'un parametre permettant d'autoriser le lecteur � faire une demande � partir d'une notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_allow_from_record' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'demandes_allow_from_record', '0',	'Autoriser les lecteurs � cr�er une demande � partir d\'une notice.\n 0 : Non\n 1 : Oui', 'a_general', 0) ";
				echo traite_rqt($rqt, "insert opac_demandes_allow_from_record into parameters");
			}


			//VT - Parametre d'activation de la g�n�ration des exemplaires fantomes dans la popup de transfert en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_expl_enable' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_expl_enable', '0', '1', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_expl_enable INTO parametres") ;
			}

			//VT - Parametre de statut par d�faut pour les exemplaires fantomes en transfert
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_statut_expl_transferts' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_statut_expl_transferts', '0', '1', 'id du statut dans lequel seront plac�s les exemplaires fantomes en cours de transit') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_statut_expl_transferts INTO parametres") ;
			}

			//VT - Parametre de choix du script par d�faut pour la g�n�ration des codes barres des exemplaires fantomes
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_expl_gen_script' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_expl_gen_script', 'gen_code\/gen_code_exemplaire.php', '1', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_expl_gen_script INTO parametres") ;
			}

			//VT - Ajout d'un champs expl_ref_num correspondant � l'id d'exemplaire dont le fantome est issu
			$rqt = "ALTER TABLE exemplaires ADD expl_ref_num INT(10) NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD expl_ref_num ");

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

			//AR - Gestion d'ontologies...
			$rqt = "create table if not exists ontologies (
				id_ontology int unsigned not null auto_increment,
				ontology_name varchar(255) not null default '',
				ontology_description text not null,
				ontology_creation_date datetime not null default '0000-00-00 00:00:00',
				primary key(id_ontology)
			)";
			echo traite_rqt($rqt,"create table if not exists ontologies");

			// NG - Circulation simplifi�e de p�riodique
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_simple int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_simple ");

			// NG - Script de construction d'�tiquette de circulation simplifi�e de p�riodique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_simple_print_script' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'serialcirc_simple_print_script', '', 'Script de construction d\'�tiquette de circulation simplifi�e de p�riodique' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_serialcirc_simple_print_script into parametres");
			}

			// DB - Modification de la table resarc (id resa_planning pour resa issue d'une pr�vision)
			$rqt = "alter table resa_archive add resarc_resa_planning_id_resa int(8) unsigned not null default 0";
			echo traite_rqt($rqt,"alter resa_archive add resarc_resa_planning_id_resa");

			// NG - Ajout de tu_oeuvre_nature et tu_oeuvre_type
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_nature VARCHAR(3) NOT NULL default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_nature ");
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_type VARCHAR(3) NOT NULL default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_type ");

			// NG - Ajout de la table tu_oeuvres_links
			$rqt = "create table if not exists tu_oeuvres_links (
				oeuvre_link_from int not null default 0,
				oeuvre_link_to int not null default 0,
				oeuvre_link_type VARCHAR(3) not null default '',
				oeuvre_link_expression int not null default 0,
				oeuvre_link_other_link int not null default 1,
				oeuvre_link_order int not null default 0
			)";
			echo traite_rqt($rqt,"create table if not exists tu_oeuvres_links");

			// AP - Nombre maximum de notices � afficher dans une liste sans pagination
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='max_results_on_a_page' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'max_results_on_a_page', '500', 'Nombre maximum de notices � afficher sur une page, utile notamment quand la navigation est d�sactiv�e' ,'d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert max_results_on_a_page into parametres");
			}

			//JP - taille de certains champs blob trop juste
			$rqt = "ALTER TABLE opac_sessions CHANGE session session MEDIUMBLOB NULL DEFAULT NULL";
			echo traite_rqt($rqt,"ALTER TABLE opac_sessions CHANGE session MEDIUMBLOB");
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

			// NG - Ajout tu_oeuvre_nature_nature dans titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_nature_nature VARCHAR(255) NOT NULL default 'original'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_nature_nature ");

			// NG - Ajout authperso_oeuvre_event dans authperso
			$rqt = "ALTER TABLE authperso ADD authperso_oeuvre_event int unsigned NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso ADD authperso_oeuvre_event ");

			// NG - Ajout de la table tu_oeuvres_events
			$rqt = "create table if not exists tu_oeuvres_events (
				oeuvre_event_tu_num int not null default 0,
				oeuvre_event_authperso_authority_num int not null default 0,
				oeuvre_event_order int not null default 0
			)";
			echo traite_rqt($rqt,"create table if not exists tu_oeuvres_events");

			// NG - Ajout comment dans les champs personalis�s
			$rqt = "ALTER TABLE notices_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD comment ");

			$rqt = "ALTER TABLE author_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom ADD comment ");

			$rqt = "ALTER TABLE authperso_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom ADD comment ");

			$rqt = "ALTER TABLE categ_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom ADD comment ");

			$rqt = "ALTER TABLE cms_editorial_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom ADD comment ");

			$rqt = "ALTER TABLE collection_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom ADD comment ");

			$rqt = "ALTER TABLE collstate_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD comment ");

			$rqt = "ALTER TABLE demandes_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom ADD comment ");

			$rqt = "ALTER TABLE empr_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD comment ");

			$rqt = "ALTER TABLE expl_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD comment ");

			$rqt = "ALTER TABLE gestfic0_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD comment ");

			$rqt = "ALTER TABLE indexint_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom ADD comment ");

			$rqt = "ALTER TABLE pret_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE pret_custom ADD comment ");

			$rqt = "ALTER TABLE publisher_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom ADD comment ");

			$rqt = "ALTER TABLE serie_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom ADD comment ");

			$rqt = "ALTER TABLE subcollection_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom ADD comment ");

			$rqt = "ALTER TABLE tu_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom ADD comment ");

			// AP - Activation de l'interface DOJO pour la multicrit�re en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='extended_search_dnd_interface' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'extended_search_dnd_interface', '1', 'Activer l\'interface drag\'n\'drop pour la recherche multicrit�re.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert extended_search_dnd_interface into parametres");
			}

			// AP - Activation de l'interface DOJO pour la multicrit�re en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='extended_search_dnd_interface' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'extended_search_dnd_interface', '0', 'Activer l\'interface drag\'n\'drop pour la recherche multicrit�re.\n0 : Non\n1 : Oui' ,'c_recherche', 0)";
				echo traite_rqt($rqt,"insert extended_search_dnd_interface into parametres");
			}

			//JP - bouton vider le cache portail
			$rqt = "ALTER TABLE cms_articles ADD article_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE cms_articles ADD article_update_timestamp");
			$rqt = "UPDATE cms_articles SET article_update_timestamp=article_creation_date";
			echo traite_rqt($rqt,"UPDATE cms_articles SET article_update_timestamp");
			$rqt = "ALTER TABLE cms_sections ADD section_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE cms_sections ADD section_update_timestamp");
			$rqt = "UPDATE cms_sections SET section_update_timestamp=section_creation_date";
			echo traite_rqt($rqt,"UPDATE cms_sections SET section_update_timestamp");

			//JP - choix notice nouveaut� oui/non par utilisateur en cr�ation de notice
			$rqt = "ALTER TABLE users ADD deflt_notice_is_new INT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_is_new");

			// JP - param�tre mail_adresse_from pour l'envoi de mails
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_adresse_from' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'mail_adresse_from', '', 'Adresse d\'exp�dition des emails. Ce param�tre permet de forcer le From des mails envoy�s par PMB. Le reply-to reste inchang� (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou param�tre opac_biblio_mail � d�faut).\nFormat : adresse_email;libell�\nExemple : pmb@sigb.net;PMB Services' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_mail_adresse_from into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='mail_adresse_from' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'mail_adresse_from', '', 'Adresse d\'exp�dition des emails. Ce param�tre permet de forcer le From des mails envoy�s par PMB. Le reply-to reste inchang� (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou param�tre opac_biblio_mail � d�faut).\nFormat : adresse_email;libell�\nExemple : pmb@sigb.net;PMB Services' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_mail_adresse_from into parametres");
			}

			// JP - blocage des prolongations autoris�es si relance sur le pr�t
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pret_prolongation_blocage' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'pret_prolongation_blocage', '0', 'Bloquer la prolongation s\'il y a un niveau de relance valid� sur le pr�t ?\n0 : Non 1 : Oui' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_pret_prolongation_blocage into parametres");
			}

			// VT & DG - Ajout de la table memorisant les grilles de saisie de formulaire
			// grid_generic_type : Type d'objet
			// grid_generic_filter : Signature (en cas de grilles multiples)
			// grid_generic_data : Format JSON de la grille
			$rqt = "create table if not exists grids_generic (
				grid_generic_type VARCHAR(32) not null default '',
				grid_generic_filter VARCHAR(255) not null default '',
				grid_generic_data mediumblob NOT NULL,
				PRIMARY KEY (grid_generic_type,grid_generic_filter)
				)";
			echo traite_rqt($rqt,"create table if not exists grids_generic");

			//DG - Grilles d'autorit�s �ditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_authorities_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_authorities_editables', '1', 'Grilles d\'autorit�s �ditables \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_authorities_editables into parametres");
			}

			//JP - Export tableur des pr�ts dans le compte emprunteur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_export_loans' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'empr_export_loans', '0', 'Afficher sur le compte emprunteur un bouton permettant d\'exporter les pr�ts dans un tableur ?\n0 : Non 1 : Oui' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_empr_export_loans into parametres");
			}

			//Alexandre - Ajout des modes d'affichage avec s�lection par �toiles
			$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'�toiles, choix de la note sous la forme d\'�toiles.\n 5 : Affichage de la note sous la forme textuelle et d\'�toiles, choix de la note sous la forme d\'�toiles.') WHERE type_param= 'pmb' AND sstype_param='avis_note_display_mode'";
			echo traite_rqt($rqt,"UPDATE pmb_avis_note_display_mode into parametres");
			$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'�toiles, choix de la note sous la forme d\'�toiles.\n 5 : Affichage de la note sous la forme textuelle et d\'�toiles, choix de la note sous la forme d\'�toiles.') WHERE type_param= 'opac' AND sstype_param='avis_note_display_mode'";
			echo traite_rqt($rqt,"UPDATE opac_avis_note_display_mode into parametres");

			//JP - param�tre utilisateur : localisation par d�faut en bulletinage
			// deflt_bulletinage_location : Identifiant de la localisation par d�faut en bulletinage
			$rqt = "ALTER TABLE users ADD deflt_bulletinage_location INT( 6 ) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_collstate_location";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_bulletinage_location");
			$rqt = "UPDATE users SET deflt_bulletinage_location=deflt_docs_location";
			echo traite_rqt($rqt,"UPDATE users SET deflt_bulletinage_location=deflt_docs_location");

			//JP - audit sur le contenu �ditorial
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

			//MB - last_sync_date : Date de la derni�re synchronisation du connecteur
			$rqt = "ALTER TABLE connectors_sources ADD last_sync_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD last_sync_date");

			// DG & AP - Cr�ation d'une table d'autorit�s pour des identifiants uniques quel que soit le type d'autorit�
			// id_authority : Identifiant unique de l'autorit�
			// num_object : Identifiant de l'autorit� dans sa table
			// type_object : Type de l'autorit�
			$rqt="create table if not exists authorities (
				id_authority int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				num_object mediumint(8) UNSIGNED NOT NULL default 0,
				type_object int unsigned not null default 0,
				index i_object(num_object, type_object)
				)";
			echo traite_rqt($rqt, "create table authorities");

			// DG & AP - Cr�ation de la table d'indexation d'autorit�s - Table des champs
			// id_authority : Identifiant de l'autorit� faisant r�f�rence � l'identifiant de la table authorities
			// type : Type d'autorit�
			// code_champ : Code champ
			// code_ss_champ : Code sous champ
			// ordre : Ordre
			// value : Valeur
			// pond : Pond�ration
			// lang : Langue
			// authority_num : Identifiant de l'autorit� li�e
			$rqt="create table if not exists authorities_fields_global_index (
				id_authority int unsigned not null default 0,
				type int(5) unsigned not null default 0,
				code_champ int(10) not null default 0,
				code_ss_champ int(3) not null default 0,
				ordre int(4) not null default 0,
				value text not null,
				pond int(4) not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default '',
				primary key(id_authority,code_champ,code_ss_champ,ordre),
				index i_value(value(300)),
				index i_id_value(id_authority,value(300))
				)";
			echo traite_rqt($rqt, "create table authorities_fields_global_index");

			// DG & AP - Cr�ation de la table d'indexation d'autorit�s - Table de mots
			// id_authority : Identifiant de l'autorit� faisant r�f�rence � l'identifiant de la table authorities
			// type : Type d'autorit�
			// code_champ : Code champ
			// code_ss_champ : Code sous champ
			// num_word : Identifiant du mot dans la table words
			// pond : Pond�ration
			// position : Position du champ
			// field_position : Position du mot dans le champ
			$rqt = "create table if not exists authorities_words_global_index(
				id_authority int unsigned not null default 0,
				type int(5) unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_authority,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_authority),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table authorities_words_global_index");

			// DG & AP - Ajout d'index sur la table aut_link
			$rqt = "ALTER TABLE aut_link drop index i_from";
			echo traite_rqt($rqt,"alter table aut_link drop index i_from");
			$rqt = "ALTER TABLE aut_link add index i_from (aut_link_from,aut_link_from_num) ";
			echo traite_rqt($rqt, "add index i_from to aut_link");

			// DG & AP - Ajout d'index sur la table aut_link
			$rqt = "ALTER TABLE aut_link drop index i_to";
			echo traite_rqt($rqt,"alter table aut_link drop index i_to");
			$rqt = "ALTER TABLE aut_link add index i_to (aut_link_to,aut_link_to_num) ";
			echo traite_rqt($rqt, "add index i_to to aut_link");

			// AR - Cr�ation d'un statut pour les autorit�s
			// id_authorities_statut : Identifiant du statut d'autorit�s
			// authorities_statut_label : Libell� du statut
			// authorities_statut_class_html : Distinction de couleur pour le statut
			// authorities_statut_available_for : Quelles sont les autorit�s autoris�es � utiliser ce statut ?
			$rqt = "create table if not exists authorities_statuts (
				id_authorities_statut int unsigned not null auto_increment primary key,
				authorities_statut_label varchar(255) not null default '',
				authorities_statut_class_html varchar(25) not null default '',
				authorities_statut_available_for text
				)";
			echo traite_rqt($rqt,"create table authorities_statuts");

			// NG - VT - Statut par d�faut pour les autorit�s
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from authorities_statuts where id_authorities_statut='1' "))==0) {
				$rqt = 'INSERT INTO authorities_statuts (id_authorities_statut,authorities_statut_label,authorities_statut_class_html,authorities_statut_available_for) VALUES (1 ,"Statut par d�faut", "statutnot1", "'.addslashes('a:9:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";i:5;s:1:"6";i:6;s:1:"8";i:7;s:1:"7";i:8;s:2:"10";}').'") ';
				echo traite_rqt($rqt,"insert default lignes_actes_statuts");
			}

			// DG - cr�ation du champ statut pour les autorit�s
			$rqt = "alter table authorities add num_statut int(2) unsigned not null default 1";
			echo traite_rqt($rqt,"alter table authorities add num_statut");

			//DG - Param�tre pour afficher ou non le bandeau d'acceptation des cookies
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent', '1', 'Afficher le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_cookies_consent into parametres");
			}

			//DG - Grille d'auteur pour les personnes physiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from grids_generic where grid_generic_type= 'auteurs' and grid_generic_filter='70' "))==0){
				$rqt = "INSERT INTO grids_generic (grid_generic_type, grid_generic_filter, grid_generic_data)
								VALUES ('auteurs', '70', '[{\"nodeId\":\"el0\",\"label\":\"Zone par d\\u00e9faut\",\"isExpandable\":false,\"showLabel\":false,\"visible\":true,\"elements\":[{\"nodeId\":\"el0Child_0\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_1_a\",\"visible\":true,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_1_b\",\"visible\":true,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_2\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_3\",\"visible\":false,\"className\":\"row\"},{\"nodeId\":\"el0Child_4_a\",\"visible\":false,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_4_b\",\"visible\":false,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_5_a\",\"visible\":false,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_5_b\",\"visible\":false,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_6\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_7\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_8\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el6Child_3\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_9\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el7Child_0\",\"visible\":true,\"className\":\"row\"}]}]')";
				echo traite_rqt($rqt,"insert minimum into grids_generic");
			}

			//DG - Info de r�indexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER LES AUTORITES (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX THE AUTHORITIES (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.21");
			break;

		case "v5.21":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//NG -  DSI: Ajout de bannette_aff_notice_number pour afficher ou pas le nombre de notices envoy�es dans le mail
			$rqt = "ALTER TABLE bannettes ADD bannette_aff_notice_number int unsigned NOT NULL default 1 " ;
			echo traite_rqt($rqt,"ALTER TABLE bannettes ADD bannette_aff_notice_number ");

			//JP - Personnalisation des colonnes pour l'affichage des �tats des collections en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='collstate_data' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'collstate_data', '', 'Colonne des �tats des collections, dans l\'ordre donn�, s�par� par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note,#n : id des champs personnalis�s\nLes valeurs possibles sont les propri�t�s de la classe PHP \"pmb/classes/collstate.class.php\".','',0)";
				echo traite_rqt($rqt,"insert pmb_collstate_data = '' into parametres");
			}

			//JP - champ historique de session trop petit
			$rqt = "ALTER TABLE admin_session CHANGE session session MEDIUMBLOB " ;
			echo traite_rqt($rqt,"ALTER TABLE admin_session CHANGE session MEDIUMBLOB ");

			// JP - Alertes localis�es pour les r�servations depuis l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_alert_localized' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'resa_alert_localized', '0', 'Si les lecteurs sont localis�s, restreindre les notifications par email des nouvelles r�servations aux utilisateurs selon le site de gestion des lecteurs par d�faut ? \n0 : Non 1 : Oui' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_resa_alert_localized into parametres");
			}

			// VT & AP - Modification de la table nomenclature_children_records : on passe � un varchar pour la gestion des effectifs ind�finis
			$rqt = "ALTER TABLE nomenclature_children_records CHANGE child_record_effective child_record_effective varchar(10) not null default '0'";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_children_records CHANGE child_record_effective varchar(10)");

			//AP & VT - Ajout d'un param�tre d�finissant le nombre d'�l�ments affich�s par onglet
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nb_elems_per_tab' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nb_elems_per_tab', '20', 'Nombre d\'�l�ments affich�s par page dans les onglets', '', '0')";
				echo traite_rqt($rqt,"insert nb_elems_per_tab='20' into parametres ");
			}

			//NG - Ajout identifiant unique � la table responsability_tu
			$query = "SHOW KEYS FROM responsability_tu WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_responsability_tu','responsability_tu_author_num','responsability_tu_num','responsability_tu_fonction');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 4) {
				$flag = true;
			}
			if($flag) {			
				$rqt = "alter table responsability_tu drop primary key";
				echo traite_rqt($rqt,"alter table responsability_tu drop primary key");
				$rqt = "ALTER TABLE responsability_tu ADD id_responsability_tu  int unsigned not null auto_increment primary key FIRST";
				echo traite_rqt($rqt,"alter table responsability_tu add id_responsability_tu");
				$rqt = "alter table responsability_tu drop primary key, add primary key (id_responsability_tu,responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction)";
				echo traite_rqt($rqt,"alter table responsability_tu add primary key (id_responsability_tu,responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction)");
			}
			
			//NG - Ajout identifiant unique � la table responsability
			$query = "SHOW KEYS FROM responsability WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_responsability','responsability_author','responsability_notice','responsability_fonction');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 4) {
				$flag = true;
			}
			if($flag) {
				$rqt = "alter table responsability drop primary key";
				echo traite_rqt($rqt,"alter table responsability drop primary key");
				$rqt = "ALTER TABLE responsability ADD id_responsability  int unsigned not null auto_increment primary key FIRST";
				echo traite_rqt($rqt,"alter table responsability add id_responsability");
				$rqt = "alter table responsability drop primary key, add primary key (id_responsability, responsability_author, responsability_notice, responsability_fonction)";
				echo traite_rqt($rqt,"alter table responsability add primary key (id_responsability, responsability_author, responsability_notice, responsability_fonction)");
			}
			
			//NG - Ajout d'un param�tre pour activer la qualification d'un lien d'auteur dans les notices et les titres uniformes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authors_qualification' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'authors_qualification', '0', 'Activer qualification d\'un lien d\'auteur dans les notices et les titres uniformes\n 0 : Non\n 1 : Oui', '', '0')";
				echo traite_rqt($rqt,"insert pmb_authors_qualification=0 into parametres ");
			}

			//DG - Entrep�t par d�faut en suppression de notices d'un panier
			$rqt = "ALTER TABLE users ADD deflt_agnostic_warehouse INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_agnostic_warehouse");


			// NG : ajout dans les pr�f�rences utilisateur du statut de publication d'article par d�faut en cr�ation d'article
			$rqt = "ALTER TABLE users ADD deflt_cms_article_statut INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_statut ");
			// NG : ajout dans les pr�f�rences utilisateur du type de contenu par d�faut en cr�ation d'article
			$rqt = "ALTER TABLE users ADD deflt_cms_article_type INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_type ");
			// NG : ajout dans les pr�f�rences utilisateur du type de contenu par d�faut en cr�ation de rubrique
			$rqt = "ALTER TABLE users ADD deflt_cms_section_type INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_section_type ");

			//NG - Ajout d'un param�tre d�finissant le nombre de bulletins � afficher dans le navigateur de bulletins
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='navigateur_bulletin_number' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'navigateur_bulletin_number', '3', 'Nombre de bulletins � afficher dans le navigateur de bulletins', 'e_aff_notice', '0')";
				echo traite_rqt($rqt,"insert opac_navigateur_bulletin_number=3 into parametres ");
			}

			//DG - Upload du logo pour les veilles
			$rqt = "ALTER TABLE docwatch_watches ADD watch_logo mediumblob not null after watch_desc";
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_logo ");

			//DG Ajout couleur sur le statut de publication du contenu �ditorial
			$rqt = "ALTER TABLE cms_editorial_publications_states ADD editorial_publication_state_class_html VARCHAR( 255 ) NOT NULL default '' " ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_publications_states ADD editorial_publication_state_class_html ");

			//VT - Param�tre permettant de d�finir le dossier des classes de mappage � utiliser pour le mappage entre autorit�s
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_mapping_folder' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'authority_mapping_folder', '', 'Dossier des classes de mappage � utiliser pour les autorit�s','',0)";
				echo traite_rqt($rqt,"insert pmb_authority_mapping_folder='' into parametres");
			}

			//DG - Nomenclatures : Atelier d�fini/non d�fini
			$rqt = "ALTER TABLE nomenclature_workshops ADD workshop_defined int unsigned not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_workshops ADD workshop_defined ");

			//DG - Nomenclatures : Notes par familles en �dition de notices
			$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_families_notes mediumtext not null after notice_nomenclature_notes";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_families_notes ");

			// AP - Ajout d'un droit de num�risation sur les notices
			// notice_scan_request_opac : Autorisation de demander une num�risation de la notice � l'OPAC
			$rqt = "ALTER TABLE notice_statut ADD notice_scan_request_opac tinyint(1) NOT NULL default 0";
			echo traite_rqt($rqt, "ALTER TABLE notice_statut ADD notice_scan_request_opac");
			// notice_scan_request_opac_abon : Autorisation uniquement pour les abonn�s de demander une num�risation de la notice � l'OPAC
			$rqt = "ALTER TABLE notice_statut ADD notice_scan_request_opac_abon tinyint(1) NOT NULL default 0";
			echo traite_rqt($rqt, "ALTER TABLE notice_statut ADD notice_scan_request_opac_abon");

			// AP - Activation de la demande de num�risation en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'scan_request_activate', '0', 'Activer la demande de num�risation.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert pmb_scan_request_activate=0 into parametres");
			}

			// AP - Activation de la demande de num�risation en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'scan_request_activate', '0', 'Activer la demande de num�risation.\n0 : Non\n1 : Oui' ,'f_modules', 0)";
				echo traite_rqt($rqt,"insert opac_scan_request_activate=0 into parametres");
			}

			// NG - Demande de num�risation: Ajout de l'interface de gestion de la liste des statuts.
			$rqt = "create table if not exists scan_request_status(
				id_scan_request_status int unsigned not null auto_increment primary key,
				scan_request_status_label varchar(255) not null default '',
				scan_request_status_opac_show int(1) not null default 0,
				scan_request_status_cancelable int(1) not null default 0,
				scan_request_status_infos_editable int(1) not null default 0,
				scan_request_status_class_html VARCHAR( 255 ) not NULL default ''
				)";
			echo traite_rqt($rqt, "create table scan_request_status");

			// NG - Demande de num�risation: Interface pour d�finir � partir d'un statut, les statuts suivants possibles (Workflow)
			$rqt = "create table if not exists scan_request_status_workflow(
				scan_request_status_workflow_from_num int unsigned not null default 0,
				scan_request_status_workflow_to_num int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_request_status_workflow");

			// NG - Demande de num�risation: Interface pour d�finir les priorit�s des demandes
			$rqt = "create table if not exists scan_request_priorities(
				id_scan_request_priority int unsigned not null auto_increment primary key,
				scan_request_priority_label varchar(255) not null default '',
				scan_request_priority_weight int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_request_priorities");

			// VT : Ajout dans les pr�f�rences utilisateur du statut par d�faut � la cr�ation d'une demande de num�risation
			$rqt = "ALTER TABLE users ADD deflt_scan_request_status INT(1) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_scan_request_status");

			// VT - NG - Table des demandes de num�risation
			// id_scan_request  : Identifiant de la demande de num�risation
			// scan_request_title : Libell� du titre de la demande
			// scan_request_desc : Description de la demande
			// scan_request_num_status : Cl� �trang�re (correspondance dans la table scan_request_status)
			// scan_request_num_priority : Cl� �trang�re (correspondance dans la table scan_request_priorities)
			// scan_request_create_date : Date de cr�ation de la demande (machine)
			// scan_request_update_date : Date de mise � jour de la demande (machine)
			// scan_request_date : Date de la demande (humain)
			// scan_request_wish_date : Date de traitement de la demande souhait� (humain)
			// scan_request_deadline_date : Date butoir de la demande (humain)
			// scan_request_comment : Commentaire de la demande
			// scan_request_elapsed_time : Temps pass� sur la demande
			// scan_request_num_dest_empr : ID du destinataire de la demande
			// scan_request_num_creator : Identifiant du cr�ateur de la demande  (User Gestion ou usager OPAC)
			// scan_request_type_creator : Type du cr�ateur (User Gestion ou usager OPAC)
			// scan_request_num_last_user : Dernier utilisateur � avoir travaill� sur la demande
			// scan_request_state : D�fini l'�tat d'une demande par rapport aux actions de l'usager destinataire (0 = demande normale, 1=modifi�e, 2=annul�e)
			$rqt = "create table if not exists scan_requests(
				id_scan_request int unsigned not null auto_increment primary key,
				scan_request_title varchar(255) not null default '',
				scan_request_desc text,
				scan_request_num_status int unsigned not null default 0,
				scan_request_num_priority int unsigned not null default 0,
				scan_request_create_date datetime,
				scan_request_update_date datetime,
				scan_request_date datetime,
				scan_request_wish_date datetime,
				scan_request_deadline_date datetime,
				scan_request_comment text,
				scan_request_elapsed_time int unsigned not null default 0,
				scan_request_num_dest_empr int unsigned not null default 0,
				scan_request_num_creator int unsigned not null default 0,
				scan_request_type_creator int unsigned not null default 0,
				scan_request_num_last_user int unsigned not null default 0,
				scan_request_state int unsigned not null default 0,
				scan_request_as_folder int(1) unsigned not null default 0,
				scan_request_folder_num_notice int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_requests");

			// VT - NG - Table des correspondance notices / demandes de num�risation
			// scan_request_linked_record_num_request  : Identifiant de la demande de num�risation
			// scan_request_linked_record_num_notice : Identifiant de la notice
			// scan_request_linked_record_num_bulletin : Identifiant du bulletin
			// scan_request_linked_record_comment : Commentaire li� � cette notice dans la demande 'num_request'
			// scan_request_linked_record_order : Ordre des �l�ments
			$rqt = "create table if not exists scan_request_linked_records(
				scan_request_linked_record_num_request int unsigned not null default 0,
				scan_request_linked_record_num_notice int unsigned not null default 0,
				scan_request_linked_record_num_bulletin int unsigned not null default 0,
				scan_request_linked_record_comment text,
				scan_request_linked_record_order int(3) unsigned not null default 0,
				PRIMARY KEY (scan_request_linked_record_num_request,scan_request_linked_record_num_notice,scan_request_linked_record_num_bulletin)
				)";
			echo traite_rqt($rqt, "create table scan_requests_linked_records");

			//AP & DG - Ajout d'un droit sur le statut pour les demandes de num�risation
			$rqt = "alter table empr_statut add allow_scan_request int unsigned not null default 0";
			echo traite_rqt($rqt,"alter table empr_statut add allow_scan_request");


			// AP & DG - Statut par d�faut en cr�ation de demande de num�risation � l'OPAC (param�tre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_create_status' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'scan_request_create_status', '1', 'Statut de cr�ation � l\'OPAC','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_create_status=1 into parametres");
			}

			// AP & DG - Statut par d�faut apr�s annulation � l'OPAC (param�tre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_cancel_status' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'scan_request_cancel_status', '1', 'Statut apr�s annulation � l\'OPAC','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_cancel_status=1 into parametres");
			}

			//DG - Statut "Sans statut particulier" ajout� par d�faut pour les demandes de num�risation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from scan_request_status where id_scan_request_status=1"))==0){
				$rqt = "insert into scan_request_status SET id_scan_request_status=1, scan_request_status_label='Sans statut particulier', scan_request_status_opac_show='1' ";
				echo traite_rqt($rqt,"insert minimum into scan_request_status");
			}

			// VT - Table des correspondance demandes de num�risation/document num�rique
			// scan_request_explnum_num_request  : Identifiant de la demande de num�risation
			// scan_request_explnum_num_notice : Identifiant de la notice
			// scan_request_explnum_num_bulletin : Identifiant du bulletin
			// scan_request_explnum_num_explnum : Identifiant du document num�rique
			$rqt = "create table if not exists scan_request_explnum(
				scan_request_explnum_num_request int unsigned not null default 0,
				scan_request_explnum_num_notice int unsigned not null default 0,
				scan_request_explnum_num_bulletin int unsigned not null default 0,
				scan_request_explnum_num_explnum int unsigned not null default 0,
				PRIMARY KEY (scan_request_explnum_num_request, scan_request_explnum_num_explnum)
				)";
			echo traite_rqt($rqt, "create table scan_requests_explnum");

			//NG - Ajout d'un param�tre renseignant le r�pertoire d'upload des documents num�riques li�s aux demandes de num�risation (param�tre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_explnum_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'scan_request_explnum_folder', '0', 'R�pertoire d\'upload des documents num�riques li�s aux demandes de num�risation', '', '1')";
					echo traite_rqt($rqt,"insert pmb_scan_request_explnum_folder=0 into parametres ");
				}

			// AP : Ajout dans les pr�f�rences utilisateur du type de notice par d�faut � la cr�ation d'une notice de dossier de demande de num�risation
			$rqt = "ALTER TABLE users ADD xmlta_doctype_scan_request_folder_record VARCHAR(2) NOT NULL DEFAULT 'a' " ;
			echo traite_rqt($rqt,"ALTER users ADD xmlta_doctype_scan_request_folder_record='a'");

			//DG - Param�tre pour activer ou non le s�lecteur d'acc�s rapide
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='quick_access' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'quick_access', '1', 'Activer le s�lecteur d\'acc�s rapide ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_quick_access into parametres");
			}

			// AP & VT - Ajout de la colonne concept dans la table scan_request (permet de sp�cifier un concept pour indexer les documents num�riques)
			$rqt = "ALTER TABLE scan_requests ADD scan_request_concept_uri VARCHAR(255) NOT NULL DEFAULT '' " ;
			echo traite_rqt($rqt,"ALTER scan_requests ADD scan_request_concept_uri");

			// AP & VT - Ajout de la colonne nb_scanned_pages dans la table scan_request (permet de renseigner le nombre de pages scann�es dans la demande de num�risation)
			$rqt = "ALTER TABLE scan_requests ADD scan_request_nb_scanned_pages INT unsigned NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER scan_requests ADD scan_request_nb_scanned_pages");

			// VT & AP - Modification de la table nomenclature_children_records : on ajoute une colonne pour stocker l'id de la nomenclature
			$rqt = "ALTER TABLE nomenclature_children_records ADD child_record_num_nomenclature int unsigned not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_children_records ADD child_record_num_nomenclature int unsigned not null");

			//MB - Champ prix trop petit
			$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER lignes_actes CHANGE prix");

			//MB - Champ montant trop petit
			$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER frais CHANGE montant");

			//MB - Champ montant_global trop petit
			$rqt = "ALTER TABLE budgets CHANGE montant_global montant_global DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER budgets CHANGE montant_global");

			//DB - script de v�rification de saisie d'une notice perso en integration
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='catalog_verif_js_integration' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'catalog_verif_js_integration', '', 'Script de v�rification de saisie de notice en int�gration','', 0)";
				echo traite_rqt($rqt,"insert pmb_catalog_verif_js_integration='' into parametres");
			}

			//NG & DG - Ajout de la table rent_pricing_systems
			// id_pricing_system : Identifiant du syst�me de tarification
			// pricing_system_label : Nom
			// pricing_system_desc : Description
			// pricing_system_percents : Liste des pourcentages associ�s
			// pricing_system_num_exercice : Exercice comptable associ�

			$rqt = "create table if not exists rent_pricing_systems(
				id_pricing_system int unsigned not null auto_increment primary key,
				pricing_system_label varchar(255) not null default '',
				pricing_system_desc text,
				pricing_system_percents text,
				pricing_system_num_exercice int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table rent_pricing_systems");

			//NG & DG - Ajout de la table rent_pricing_system_grids
			// id_pricing_system_grid : Identifiant incr�mentiel
			// pricing_system_grid_num_system : Syst�me de tarification associ�
			// pricing_system_grid_time_start : Minutage de d�part
			// pricing_system_grid_time_end : Minutage de fin
			// pricing_system_grid_price : Prix
			// pricing_system_grid_type : Type (1 : intervalle, 2 : Temps suppl, 3 : Non utilis�)
			$rqt = "create table if not exists rent_pricing_system_grids(
				id_pricing_system_grid int unsigned not null auto_increment primary key,
				pricing_system_grid_num_system int unsigned not null default 0,
				pricing_system_grid_time_start int unsigned not null default 0,
				pricing_system_grid_time_end int unsigned not null default 0,
				pricing_system_grid_price float(12,2) unsigned not null default 0,
				pricing_system_grid_type int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table rent_pricing_system_grids");

			//NG & DG - Ajout de la table rent_account_sections
			// account_type_num_exercice : Exercice comptable associ�
			// account_type_num_section : Rubrique budg�taire associ�e
			// account_type_marclist : Type de d�compte associ�
			$rqt = "create table if not exists rent_account_types_sections(
				account_type_num_exercice int unsigned not null default 0,
				account_type_num_section int unsigned not null default 0,
				account_type_marclist varchar(10) not null default '',
				PRIMARY KEY (account_type_num_section, account_type_marclist)
				)";
			echo traite_rqt($rqt, "create table rent_account_sections");

			//NG & DG - Ajout de la table rent_accounts
			// id_account : Identifiant du d�compte
			// account_num_user : Identifiant de l'utilisateur qui l'a cr��
			// account_num_exercice : Exercice comptable associ�
			// account_type : Type de la demande
			// account_desc : Description
			// account_date : Date de cr�ation
			// account_receipt_limit_date : Date limite de r�ception
			// account_receipt_effective_date : Date effective de r�ception
			// account_return_date : Date de retour
			// account_num_authority : Ex�cution associ�e
			// account_title : Titre
			// account_event_date : Date de l'�v�nement
			// account_event_formation : Formation
			// account_event_orchestra : Chef d'orchestre
			// account_event_place : Lieu de l'�v�nement
			// account_num_publisher : Editeur associ�
			// account_num_author : Compositeur associ�
			// account_num_pricing_system : Syst�me de tarification associ�
			// account_time : Minutage
			// account_percent : Pourcentage
			// account_price : Prix
			// account_web : Diffusion Web (Oui/Non)
			// account_web_percent : Pourcentage Web
			// account_web_price : Prix Web
			$rqt = "create table if not exists rent_accounts(
				id_account int unsigned not null auto_increment primary key,
				account_num_user int unsigned not null default 0,
				account_num_exercice int unsigned not null default 0,
				account_type varchar(3) not null default '',
				account_desc text,
				account_date datetime,
				account_receipt_limit_date datetime,
				account_receipt_effective_date datetime,
				account_return_date datetime,
				account_num_uniform_title int unsigned not null default 0,
				account_title varchar(255) not null default '',
				account_event_date varchar(255) not null default '',
				account_event_formation varchar(255) not null default '',
				account_event_orchestra varchar(255) not null default '',
				account_event_place varchar(255) not null default '',
				account_num_publisher int unsigned not null default 0,
				account_num_author int unsigned not null default 0,
				account_num_pricing_system int unsigned not null default 0,
				account_time int unsigned not null default 0,
				account_percent float(8,2) unsigned not null default 0,
				account_price float(12,2) unsigned not null default 0,
				account_web int(1) unsigned not null default 0,
				account_web_percent float(8,2) unsigned not null default 0,
				account_web_price float(12,2) unsigned not null default 0,
				account_comment text
				)";
			echo traite_rqt($rqt, "create table rent_accounts");

			//NG & DG - Ajout de la table rent_invoices
			// id_invoice : Identifiant de la facture
			// invoice_num_user : Identifiant de l'utilisateur qui l'a cr��e
			// invoice_date : Date de g�n�ration
			// invoice_status : Enum�ration (0 = en cours, 1 = valid�e)
			// invoice_valid_date : Date de validation
			$rqt = "create table if not exists rent_invoices(
				id_invoice int unsigned not null auto_increment primary key,
				invoice_num_user int unsigned not null default 0,
				invoice_date datetime,
				invoice_status int unsigned not null default 1,
				invoice_valid_date datetime,
				invoice_destination varchar(10) not null default ''
				)";
			echo traite_rqt($rqt, "create table rent_invoices");

			//NG & DG - Ajout de la table rent_accounts_invoices
			// account_invoice_num_account : Identifiant du d�compte associ�
			// account_invoice_num_invoice : Identifiant de la facture associ�e
			$rqt = "create table if not exists rent_accounts_invoices(
				account_invoice_num_account int unsigned not null default 0,
				account_invoice_num_invoice int unsigned not null default 0,
				PRIMARY KEY (account_invoice_num_account, account_invoice_num_invoice)
				)";
			echo traite_rqt($rqt, "create table rent_accounts_invoices");

			//NG
			$rqt = "ALTER TABLE rent_accounts ADD account_request_type varchar(3) not null default '' after account_num_exercice " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_request_type ");

			//DG - Statut sur les demandes de location (command� / non command�)
			$rqt = "ALTER TABLE rent_accounts ADD account_request_status int(1) unsigned not null default 1 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_request_status ");

			//NG
			$rqt = "ALTER TABLE rent_accounts ADD account_num_acte int unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_num_acte ");

			//NG
			$rqt = "ALTER TABLE rent_invoices ADD invoice_num_acte int unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_invoices ADD invoice_num_acte ");

			//NG - Ajout d'un lien fournisseur dans la table des editeurs
			$rqt = "ALTER TABLE publishers ADD ed_num_entite int unsigned NOT NULL default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE publishers ADD ed_num_entite ");

			// DG - Afficher la possibilit� pour le lecteur d'inscrire d'autres membres � ses listes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='shared_lists_add_empr' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'shared_lists_add_empr', '0', 'Afficher la possibilit� pour le lecteur d\'inscrire d\'autres membres � ses listes de lecture partag�es \n 0 : Non \n 1 : Oui', 'a_general', '0')";
				echo traite_rqt($rqt,"insert opac_shared_lists_add_empr='0' into parametres ");
			}

			//DG - Gestion des avis - Notion de commentaire priv�
			$rqt = "ALTER TABLE avis ADD avis_private int(1) unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_private ");

			//DG - Gestion des avis - Association d'une liste lecture
			$rqt = "ALTER TABLE avis ADD avis_num_liste_lecture int(8) unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_num_liste_lecture ");

			//DG - Listes de lecture - Saisie libre d'un tag
			$rqt = "ALTER TABLE opac_liste_lecture ADD tag varchar(255) not null default '' " ;
			echo traite_rqt($rqt,"ALTER TABLE opac_liste_lecture ADD tag ");

			// AP & NG - Ajout d'un param�tre URI du concept � associer aux partitions avant ex�cution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_before' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_before', '0', 'URI du concept � associer aux partitions avant ex�cution', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_before into parametres ");
			}

			// AP & NG - Ajout d'un param�tre URI du concept � associer aux partitions apr�s ex�cution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_after' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_after', '0', 'URI du concept � associer aux partitions apr�s ex�cution', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_after into parametres ");
			}

			// AP & NG - Ajout d'un param�tre URI du concept � associer aux partitions originales
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_blank' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_blank', '0', 'URI du concept � associer aux partitions originales', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_blank into parametres ");
			}

			// AP & NG - Passage du param�tre nomenclature_record_children_link en cach�
			$rqt = "UPDATE parametres SET gestion='1' where type_param= 'pmb' and sstype_param='nomenclature_record_children_link' ";
			echo traite_rqt($rqt,"update pmb_nomenclature_record_children_link");


			//AP & VT - Mise � jour du param�tre de d�finition de r�pertoire de template pour les affichages autorit�s en opac
			if(pmb_mysql_num_rows(pmb_mysql_query('select valeur_param from parametres where type_param= "opac" and sstype_param="authorities_templates_folder" and valeur_param like "%/%"'))){
				$rqt = 'UPDATE parametres set valeur_param = SUBSTRING_INDEX(valeur_param, "/", -1) where type_param= "opac" and sstype_param="authorities_templates_folder" ';
				echo traite_rqt($rqt,"UPDATE parametres opac_authorities_templates_folder");
			}

			//DG - Fournisseur associ� au d�compte de location
			$rqt = "ALTER TABLE rent_accounts ADD account_num_supplier int unsigned not null default 0 after account_num_publisher" ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_num_supplier ");

			//DG - Modification du champ account_event_date de la table rent_accounts
			$rqt = "ALTER TABLE rent_accounts MODIFY account_event_date datetime" ;
			echo traite_rqt($rqt,"alter table rent_accounts modify account_event_date");

			//DB Maj commentaires mail_methode
			$rqt = "update parametres set comment_param= 'M�thode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls) : en smtp, mettre O ou 1 pour l\'authentification... ' where sstype_param='mail_methode' ";
			echo traite_rqt($rqt,"update mail_methode comments");

			// DG & VT - Activation des actions rapides (dans le tableau de bord)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='dashboard_quick_params_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'dashboard_quick_params_activate', '1', 'Activer les actions rapides dans le tableau de bord.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert pmb_dashboard_quick_params_activate=1 into parametres");
			}

			//JP - Autoriser les montants n�gatifs dans les acquisitions
			$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
			echo traite_rqt($rqt,"ALTER TABLE frais CHANGE montant");
			$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
			echo traite_rqt($rqt,"ALTER TABLE lignes_actes CHANGE prix");

			//DG - acc�s rapide pour les paniers de lecteurs
			$rqt = "ALTER TABLE empr_caddie ADD acces_rapide INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie ADD acces_rapide");

			//JP - update bannette_aff_notice_number pour les bannettes priv�es
			$rqt = "UPDATE bannettes SET bannette_aff_notice_number = 1 WHERE proprio_bannette <> 0" ;
			echo traite_rqt($rqt,"UPDATE bannettes SET bannette_aff_notice_number ");
			echo traite_rqt($rqt,"UPDATE bannettes SET bannette_aff_notice_number ");

			//NG - M�morisation de l'utilisateur qui fait une demande de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_ask_user_num INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_ask_user_num");

			//AP - Ajout du nom de groupe dans la table des circulations en cours
			$rqt = "ALTER TABLE serialcirc_circ ADD serialcirc_circ_group_name varchar(255) NOT NULL DEFAULT ''";
			echo traite_rqt($rqt,"ALTER TABLE serialcirc_circ add serialcirc_circ_group_name default ''");

			//DG - Avis priv� par d�faut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_default_private' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'avis_default_private', '0', 'Avis priv� par d�faut ? \n 0 : Non \n 1 : Oui', 'a_general', 0) ";
				echo traite_rqt($rqt,"insert opac_avis_default_private into parametres") ;
			}

			//JP - Alerter l'utilisateur par mail des nouvelles demandes d'inscription aux listes de circulation
			$rqt = "ALTER TABLE users ADD user_alert_serialcircmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_subscribemail";
			echo traite_rqt($rqt,"ALTER TABLE users add user_alert_serialcircmail default 0");

			//JP - Enrichir les flux rss g�n�r�s par les veilles
			$rqt = "ALTER TABLE docwatch_watches ADD watch_rss_link VARCHAR(255) NOT NULL, ADD watch_rss_lang VARCHAR(255) NOT NULL, ADD watch_rss_copyright VARCHAR(255) NOT NULL, ADD watch_rss_editor VARCHAR(255) NOT NULL, ADD watch_rss_webmaster VARCHAR(255) NOT NULL, ADD watch_rss_image_title VARCHAR(255) NOT NULL, ADD watch_rss_image_website VARCHAR(255) NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches add watch_rss_link, watch_rss_lang, watch_rss_copyright, watch_rss_editor, watch_rss_webmaster, watch_rss_image_title, watch_rss_image_website");

			//JP - Possibilit� de nettoyer le contenu HTML dans un OAI entrant
			$rqt = "ALTER TABLE connectors_sources ADD clean_html INT(3) UNSIGNED NOT NULL DEFAULT '0'";
			echo traite_rqt($rqt,"ALTER TABLE connectors_sources add clean_html");

			//Alexandre - Pr�remplissage de la vignette des d�pouillements avec la vignette du bulletin
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bulletin_thumbnail_url_article' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'bulletin_thumbnail_url_article', '0', 'Pr�remplissage de l\'url de la vignette des d�pouillements avec l\'url de la vignette de la notice bulletin en catalogage des p�riodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
				echo traite_rqt($rqt, "insert pmb_bulletin_thumbnail_url_article=0 into parametres");
			}

			//DG - Grilles exemplaires �ditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_expl_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_expl_editables', '1', 'Grilles exemplaires �ditables ? \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_expl_editables into parametres");
			}

			//DG - Grilles exemplaires num�riques �ditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_explnum_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_explnum_editables', '1', 'Grilles exemplaires num�riques �ditables ? \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_explnum_editables into parametres");
			}

			//DG - Mis � jour de la table grilles_auth (pour ceux qui ont une version r�cente de PMB) pour la rendre g�n�rique
			if (pmb_mysql_query("select * from grilles_auth")){
				$rqt = "RENAME TABLE grilles_auth TO grids_generic";
				echo traite_rqt($rqt, "rename grilles_auth to grids_generic");

				// DG - Renommage de la colonne "grille_auth_type" en "grid_generic_type"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_type grid_generic_type VARCHAR(32) not null default ''";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_type grid_generic_type");

				// DG - Renommage de la colonne "grille_auth_filter" en "grid_generic_filter"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_filter grid_generic_filter VARCHAR(255) null default ''";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_filter grid_generic_filter");

				// DG - Renommage de la colonne "grille_auth_descr_format" en "grid_generic_descr_format"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_descr_format grid_generic_data mediumblob NOT NULL";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_descr_format grid_generic_data");
			}

			//NG - M�morisation de l'utilisateur qui fait l'envoi de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_send_user_num INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_send_user_num");

			// DG - Cr�ation de la table de stockage des objets pour le formulaire de contact opac
			// id_object : Identifiant de l'objet
			// object_label : Libell� de l'objet
			$rqt = "create table if not exists contact_form_objects(
				id_object int unsigned not null auto_increment primary key,
				object_label varchar(255) not null default '') ";
			echo traite_rqt($rqt,"create table contact_form_objects");

			//DG - Formulaire de contact - Param�trage g�n�ral
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contact_form_parameters' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contact_form_parameters','','Param�trage g�n�ral du formulaire de contact','',1)";
				echo traite_rqt($rqt,"insert pmb_contact_form_parameters into parametres");
			}

			//DG - Formulaire de contact - Param�trage des listes de destinataires
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contact_form_recipients_lists' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contact_form_recipients_lists','','Param�trage des listes de destinataires du formulaire de contact','',1)";
				echo traite_rqt($rqt,"insert pmb_contact_form_recipients_lists into parametres");
			}

			//DG - Param�tre pour afficher ou non le formulaire de contact
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='contact_form' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'contact_form', '0', 'Afficher le formulaire de contact ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_contact_form into parametres");
			}

			//JP - Param�tre inutilis� cach�
			$rqt = "update parametres set gestion=1 where type_param= 'opac' and sstype_param='categories_categ_sort_records' ";
			echo traite_rqt($rqt,"update categories_categ_sort_records hide into parametres");

			//JP - date de cr�ation et cr�ateur des paniers
			$rqt = "ALTER TABLE caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE caddie add creation_user_name, creation_date");
			$rqt = "ALTER TABLE empr_caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie add creation_user_name, creation_date");

			//NG - M�morisation de la date demand�e par l'utilisateur en demande de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_ask_date date NOT NULL default '0000-00-00'";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_ask_date");

			//DG - Ajout du type sur les recherches pr�d�finies gestion (Pour sp�cifier Notices / Autorit�s)
			$rqt = "ALTER TABLE search_perso ADD search_type varchar(255) not null default 'RECORDS' after search_id";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD search_type") ;

			//DG - Nomenclatures : Note pour les instruments non standard en �dition de notices
			$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_exotic_instruments_note text not null after notice_nomenclature_families_notes";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_exotic_instruments_note ");

			//TS - Ajout d'une classe CSS sur les cadres du portail
			$rqt = "ALTER TABLE cms_cadres ADD cadre_css_class VARCHAR(255) NOT NULL DEFAULT '' AFTER cadre_modcache";
			echo traite_rqt($rqt,"ALTER TABLE cms_cadres ADD cadre_css_class");
			//DG - Personnalisables de notices : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE notices_custom_values ADD notices_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD notices_custom_order");

			//DG - Personnalisables de auteurs : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE author_custom_values ADD author_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD author_custom_order");

			//DG - Personnalisables de cat�gories : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE categ_custom_values ADD categ_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD categ_custom_order");

			//DG - Personnalisables de �diteurs : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE publisher_custom_values ADD publisher_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD publisher_custom_order");

			//DG - Personnalisables de collections : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE collection_custom_values ADD collection_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD collection_custom_order");

			//DG - Personnalisables de sous-collections : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE subcollection_custom_values ADD subcollection_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD subcollection_custom_order");

			//DG - Personnalisables de titres de s�ries : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE serie_custom_values ADD serie_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD serie_custom_order");

			//DG - Personnalisables de titres uniformes : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE tu_custom_values ADD tu_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD tu_custom_order");

			//DG - Personnalisables d'indexations d�cimales : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE indexint_custom_values ADD indexint_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD indexint_custom_order");

			//DG - Personnalisables d'autorit�s perso : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE authperso_custom_values ADD authperso_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD authperso_custom_order");

			//DG - Personnalisables du contenu �ditorial : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD cms_editorial_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD cms_editorial_custom_order");

			//DG - Personnalisables d'�tats des collections : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE collstate_custom_values ADD collstate_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD collstate_custom_order");

			//DG - Personnalisables de demandes : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE demandes_custom_values ADD demandes_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom_values ADD demandes_custom_order");

			//DG - Personnalisables de lecteurs : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE empr_custom_values ADD empr_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD empr_custom_order");

			//DG - Personnalisables d'exemplaires : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE expl_custom_values ADD expl_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD expl_custom_order");

			//DG - Personnalisables de fiches : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE gestfic0_custom_values ADD gestfic0_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD gestfic0_custom_order");

			//DG - Personnalisables de pr�ts : Ordre pour les champs r�p�tables
			$rqt = "ALTER TABLE pret_custom_values ADD pret_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE pret_custom_values ADD pret_custom_order");


			//DG - Param�tre pour afficher ou non le lien de g�n�ration d'un flux RSS de la recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url', '1', 'Afficher le lien permettant de g�n�rer un flux RSS de la recherche ? \n0 : Non 1 : Oui','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_short_url=1 into parametres");
			}

			//AR - Les index, c'est pratique...
			$rqt = "alter table tu_oeuvres_events add index i_toe_oeuvre_event_tu_num(oeuvre_event_tu_num)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "alter table tu_oeuvres_events add index i_toe_oeuvre_event_authperso_authority_num(oeuvre_event_authperso_authority_num)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "alter table notices_titres_uniformes add index i_ntu_ntu_num_tu(ntu_num_tu)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE authorities ADD INDEX i_a_num_object_type_object(num_object,type_object)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE authorities ADD INDEX i_a_num_statut(num_statut)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE titres_uniformes ADD INDEX i_tu_tu_oeuvre_type(tu_oeuvre_type)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE titres_uniformes ADD INDEX i_tu_tu_oeuvre_nature(tu_oeuvre_nature)";
			echo traite_rqt($rqt,$rqt);

			// AP & DG - Cr�ation de la table de planches d'�tiquettes
			// id_sticks_sheet : Identifiant
			// sticks_sheet_label : Libell�
			// sticks_sheet_data : Structure JSON des donn�es de g�n�ration
			// sticks_sheet_order : Ordre
			$rqt = "create table if not exists sticks_sheets(
				id_sticks_sheet int unsigned not null auto_increment primary key,
				sticks_sheet_label varchar(255) not null default '',
				sticks_sheet_data text not null,
				sticks_sheet_order int(11) NOT NULL default 0) ";
			echo traite_rqt($rqt,"create table sticks_sheets");

			// AP & DG - Cr�ation de la table de seuils de commandes
			// id_threshold : Identifiant
			// threshold_label : Libell�
			// threshold_amount : Montant du seuil
			// threshold_amount_tax_included : Montant du seuil HT/TTC
			// threshold_footer : Pied de page Signature
			// threshold_num_entity : Etablissement associ�
			$rqt = "create table if not exists thresholds(
				id_threshold int unsigned not null auto_increment primary key,
				threshold_label varchar(255) not null default '',
				threshold_amount float NOT NULL DEFAULT 0,
				threshold_amount_tax_included int(1) NOT NULL DEFAULT 0,
				threshold_footer text not null,
				threshold_num_entity int(11) NOT NULL default 0) ";
			echo traite_rqt($rqt,"create table thresholds");

			// DG & AP - Ajout d'un param�tre pour activer la gestion avanc�es des �tats des collections
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='collstate_advanced' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'collstate_advanced', '0', 'Activer la gestion avanc�e des �tats des collections :\n 0 : non activ�e \n 1 : activ�e', '', '0')";
				echo traite_rqt($rqt,"insert collstate_advanced='0' into parametres ");
			}

			// DG & AP - Cr�ation de la table de liaison entre bulletins et �tats des collections
			// collstate_bulletins_num_collstate : Identifiant de la collection
			// collstate_bulletins_num_bulletin : Identifiant du bulletin
			// collstate_bulletins_order : Ordre
			$rqt = "create table if not exists collstate_bulletins(
				collstate_bulletins_num_collstate int(8) not null default 0,
				collstate_bulletins_num_bulletin int(8) not null default 0,
				collstate_bulletins_order int(8) NOT NULL default 0,
				primary key (collstate_bulletins_num_collstate, collstate_bulletins_num_bulletin)) ";
			echo traite_rqt($rqt,"create table collstate_bulletins");

			//DG - OPAC : Personnalisation de la pagination des listes
			if(pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='items_pagination_custom' "))==0){
				$rqt = "INSERT INTO parametres ( id_param , type_param , sstype_param , valeur_param , comment_param , section_param , gestion )
				VALUES (0 , 'opac', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs num�riques suppl�mentaires dans les listes pagin�es, s�par�es par des virgules : 25,50,100,200.', 'a_general', '0')";
				echo traite_rqt($rqt, "insert opac_items_pagination_custom=25,50,100,200 into parametres");
			}

			//DG - Gestion : Personnalisation de la pagination des listes
			if(pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='items_pagination_custom' "))==0){
				$rqt = "INSERT INTO parametres ( id_param , type_param , sstype_param , valeur_param , comment_param , section_param , gestion )
				VALUES (0 , 'pmb', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs num�riques suppl�mentaires dans les listes pagin�es, s�par�es par des virgules : 25,50,100,200.', '', '0')";
				echo traite_rqt($rqt, "insert pmb_items_pagination_custom=25,50,100,200 into parametres");
			}

			//DG - Attribution des droits de construction de portail au Super User uniquement
			$rqt = "update users set rights=rights+1048576 where rights<1048576 and userid=1";
			echo traite_rqt($rqt, "update users add rights cms_build only for Super User");

			//DG - Ajout de l'ordre sur les recherches pr�d�finies OPAC
			$rqt = "ALTER TABLE search_persopac ADD search_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE search_persopac ADD search_order") ;

			//JP - Message personnalis� sur la page de connexion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='login_message' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'login_message', '', 'Message � afficher sur la page de connexion','', 0)";
				echo traite_rqt($rqt,"insert pmb_login_message into parametres");
			}

			//JP - �viter les codes-barre identiques en cr�ation d'emprunteur
			$rqt = "create table if not exists empr_temp (cb varchar( 255 ) NOT NULL ,sess varchar( 12 ) NOT NULL ,UNIQUE (cb))";
			echo traite_rqt($rqt,"create table  if not exists empr_temp ") ;

			//NG - Ajout d'un param�tre utilisateur permettant d'activer la webcam
			$rqt = "alter table users add deflt_camera_empr int not null default 0";
			echo traite_rqt($rqt,"alter table users add deflt_camera_empr");

			//JP - changement du s�parateur des tris de l'opac pour pouvoir utiliser le parse HTML
			$rqt = "UPDATE parametres SET valeur_param=REPLACE(valeur_param,';','||'), comment_param='Afficher la liste d�roulante de s�lection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour d�croissant\nnum ou text pour num�rique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibell� du tri (optionnel)' WHERE type_param='opac' AND sstype_param='default_sort_list'";
			echo traite_rqt($rqt,"update value into parametres") ;

			//JP - Recherche �tendue aux oeuvres sur param�tre
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_search_into_linked_elements' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'allow_search_into_linked_elements', '2', 'Afficher la case � cocher permettant d\'�tendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, coch�e par d�faut\n 2 : Oui, non-coch�e par d�faut','search', 0)";
				echo traite_rqt($rqt,"insert pmb_allow_search_into_linked_elements into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_search_into_linked_elements' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'allow_search_into_linked_elements', '2', 'Afficher la case � cocher permettant d\'�tendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, coch�e par d�faut\n 2 : Oui, non-coch�e par d�faut','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_allow_search_into_linked_elements into parametres");
			}

			//DG - Typage des avis pour combiner les notices et les articles du contenu �ditorial..pour le moment..
			$rqt = "ALTER TABLE avis ADD type_object mediumint(8) NOT NULL AFTER num_notice";
			echo traite_rqt($rqt,"ALTER TABLE avis ADD type_object") ;
			//DG - On affecte les avis existants de type AVIS_RECORDS
			$rqt = "UPDATE avis set type_object = 1, dateajout = dateajout where type_object = 0";
			echo traite_rqt($rqt,"UPDATE type_object = 1 FOR records avis") ;

			//VT - Ajout de deux �l�ments dans la table cms_editorial_types (id d'une page et variable d'environnement) permettant de g�n�rer un permalink
			$rqt = "ALTER TABLE cms_editorial_types ADD editorial_type_permalink_num_page int(11) NOT NULL default 0, ADD editorial_type_permalink_var_name varchar(255) not null default ''";
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_types ADD editorial_type_permalink_num_page, ADD editorial_type_permalink_var_name") ;

			//VT - D�finition du mode de g�n�ration du flux rss de recherche (le param�tre opac_short_url doit �tre activ�)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url_mode' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url_mode', '0', 'Elements g�n�r�s dans le flux rss de la recherche: \n0 : Nouveaut�s \n1 : R�sultats de la recherche \nPour le mode 1, un nombre de r�sultats limite peut �tre ajout� apr�s le mode, il doit �tre pr�c�d� d\'une virgule\nExemple: 1,30\nSi aucune limite n\'est sp�cifi�e, c\'est le param�tre opac_search_results_per_page qui sera pris en compte','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_short_url_mode=0 into parametres");
			}

			//VT - Ajout d'un param�tre autorisant la r�servation d'une notice sans exemplaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_records_no_expl' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'resa_records_no_expl', '0', 'R�servation sur les notices \n 0 : Non \n 1 : Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_resa_records_no_expl=0 into parametres");
			}

			//VT - Cr�ation d'une table permettant de stocker les identifiants des emprunteurs ayant sugg�r� la commande
			$rqt = "create table if not exists lignes_actes_applicants(
				ligne_acte_num int not null default 0,
				empr_num int not null default 0,
				primary key (ligne_acte_num, empr_num)) ";
			echo traite_rqt($rqt,"create table lignes_actes_applicants");

			//NG - OPAC : Ajout du param�tre activant la d�connexion dans le menu d'acc�s rapide et d�sactivant le lien de d�connexion classique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='quick_access_logout' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'quick_access_logout', '0', 'Activer le menu de d�connexion dans le s�lecteur d\'acc�s rapide ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_quick_access_logout into parametres");
			}

			//NG - Ajout du libell� opac des classements de bannettes publiques
			$rqt = "ALTER TABLE classements ADD classement_opac_name varchar(255) not null default ''";
			echo traite_rqt($rqt,"ALTER TABLE classements ADD classement_opac_name") ;

			//NG - Ajout de l'ordre dans les classements de bannettes publiques
			$rqt = "ALTER TABLE classements ADD classement_order int NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE classements ADD classement_order") ;

			//NG - Attribution d'un ordre par d�fault pour le fonctionnement du tri des classement des classement de bannettes
			$rqt = "update classements set classement_order=id_classement where (type_classement='BAN' or type_classement='')";
			echo traite_rqt($rqt, "update classements set classement_order");

			//NG - Ajout du param�tre du r�pertoire et motif d'upload des photos des emprunteurs, dans le motif fourni, !!num_carte!! sera remplac� par le num�ro de carte du lecteur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='pics_folder' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'empr', 'pics_folder', '', 'R�pertoire et motif d\'upload des photos des emprunteurs, dans le motif fourni, !!num_carte!! sera remplac� par le num�ro de carte du lecteur. \n exemple : ./photos/lecteurs/!!num_carte!!.jpg \n ATTENTION : coh�rence avec empr_pics_url � v�rifier','',0)";
				echo traite_rqt($rqt,"insert empr_pics_folder into parametres");
			}

			//NG - Activer la recherche de notices d�j� pr�sentes en saisie d'une suggestion d'acquisition
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='suggestion_search_notice_doublon' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','suggestion_search_notice_doublon','0','Activer la recherche de notices d�j� pr�sentes en saisie d\'une suggestion d\'acquisition\n0 : D�sactiver\n1 : Activer\n2 : Activer avec le levenshtein\n    NB : Cette fonction n�cessite l\'installation de l\'extension levenshtein dans MySQL','c_recherche',0)" ;
				echo traite_rqt($rqt,"insert opac_suggestion_search_notice_doublon into parametres") ;
			}

			//TS & NG - Ajout de la notion de propri�taire pour les documents num�riques
			$rqt = "create table if not exists explnum_lenders(
				explnum_lender_num_explnum int not null default 0,
				explnum_lender_num_lender int not null default 0,
				primary key (explnum_lender_num_explnum, explnum_lender_num_lender)) ";
			echo traite_rqt($rqt,"create table explnum_lenders");

			// VT - Ajout d'un param�tre permettant de d'activer/d�sactiver l'affinage des recherches � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_allow_refinement' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'search_allow_refinement', '1', 'Afficher le lien \"Affiner la recherche\" en r�sultat de recherche','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_search_allow_refinement into parametres");
			}

			//DG - Modification du commentaire opac_etagere_notices_format
			$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les �tag�res de l\'�cran d\'accueil \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : R�duit (titre+auteurs) seul\n 9 : Templates django (Sp�cifier le nom du r�pertoire dans le param�tre notices_format_django_directory)' where type_param='opac' and sstype_param='etagere_notices_format'" ;
			echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_etagere_notices_format") ;

			//DG - Modification du commentaire opac_bannette_notices_format
			$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les bannettes \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : R�duit (titre+auteurs) seul\n 9 : Templates django (Sp�cifier le nom du r�pertoire dans le param�tre notices_format_django_directory)' where type_param='opac' and sstype_param='bannette_notices_format'" ;
			echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_bannette_notices_format") ;

			// NG - Cr�ation de la table m�morisant les groupes de lecteurs abonn�s � une bannette
			// empr_groupe_num_bannette : Identifiant de bannette
			// empr_groupe_num_groupe : Identifiant de groupe de lecteur
			$rqt = "create table if not exists bannette_empr_groupes(
				empr_groupe_num_bannette int unsigned not null default 0,
				empr_groupe_num_groupe int unsigned not null default 0,
				PRIMARY KEY (empr_groupe_num_bannette, empr_groupe_num_groupe) )";
			echo traite_rqt($rqt,"create table bannette_empr_groupes");

			// NG - Cr�ation de la table m�morisant les cat�gories de lecteurs abonn�s � une bannette
			// empr_categ_num_bannette : Identifiant de bannette
			// empr_categ_num_categ : Identifiant de categorie de lecteur
			$rqt = "create table if not exists bannette_empr_categs(
				empr_categ_num_bannette int unsigned not null default 0,
				empr_categ_num_categ int unsigned not null default 0,
				PRIMARY KEY (empr_categ_num_bannette, empr_categ_num_categ) )";
			echo traite_rqt($rqt,"create table bannette_empr_categs");

			$rqt = "insert into bannette_empr_groupes (empr_groupe_num_bannette, empr_groupe_num_groupe)
					select id_bannette, groupe_lecteurs from bannettes where groupe_lecteurs > 0";
			echo traite_rqt($rqt,"insert into bannette_empr_groupes");

			$rqt = "insert into bannette_empr_categs (empr_categ_num_bannette, empr_categ_num_categ)
					select id_bannette, categorie_lecteurs from bannettes where categorie_lecteurs > 0";
			echo traite_rqt($rqt,"insert into bannette_empr_categs");

			//Alexandre - Suppression du code pour les articles de p�riodiques
			$rqt = "UPDATE notices SET code='',update_date=update_date WHERE niveau_biblio='a'";
			echo traite_rqt($rqt,"UPDATE notices SET code for niveau_biblio=a");

			// TS & NG - D�finition de la couleur d'une emprise de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associ�es � des localisations','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_location_color into parametres");
			}

			// TS & NG - Ajout param�tre de la taille de la carte en �dition de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_location_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_location_edition', '800*480', 'Taille de la carte en �dition de localisation en pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_location_edition into parametres");
			}

			// TS & NG - Ajout param�tre de la taille de la carte en visualisation des localisations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_location_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisations en pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_location_view into parametres");
			}

			// TS & NG - D�finition de la couleur d'une emprise de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associ�es � des localisations en RVB, exemple : #D60F0F','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_location_color into parametres");
			}

			// TS & NG - Ajout param�tre de la taille de la carte en visualisation des localisations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisationsen pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_view into parametres");
			}

			//JP - Templates de bannette pour dsi priv�es
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_tpl' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'dsi', 'private_bannette_tpl', '0', 'Identifiant du template de bannette � appliquer sur les bannettes priv�es \nSi vide ou � 0, l\'ent�te et pied de page par d�faut seront utilis�s.','', 0)";
				echo traite_rqt($rqt,"insert dsi_private_bannette_tpl into parametres");
			}

			//VT - Ajout d'un param�tre qui active l'envoi de mail au premier r�servataire d'une notice en cas de prolongation du pr�t
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreresa' and sstype_param='resa_prolong_email' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pdflettreresa', 'resa_prolong_email', '0', 'Envoi d\'un mail au r�servataire de rang 1, dont r�servation valid�e, lorsque le pr�t en cours est prolong� \n Non : 0 \n Oui : id_template ','',0)";
				echo traite_rqt($rqt,"insert pmb_resa_prolong_email=0 into parametres");
			}

			// VT - Ajout d'un param�tre permettant de masquer les +/- dans les listes de r�sultats � l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_show_expand' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'recherche_show_expand', '1', 'Affichage des boutons de d�pliage de toutes les notices dans les listes de r�sultats � l\'OPAC \n0: Boutons non affich�s \n1: Boutons affich�s','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_recherche_show_expand into parametres");
			}

			//DG - Param�tre Portail : Activer l'onglet Toolkits
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_toolkits' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_toolkits', '0', 'Activer la possibilit� de g�rer des toolkits.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert cms_active_toolkits into parametres");
			}

			// DG - Cr�ation de la table pour les toolkits d'aide � la construction d'un portail
			// cms_toolkit_name : Nom du toolkit
			// cms_toolkit_active : Activ� Oui/Non
			// cms_toolkit_data : Donn�es
			// cms_toolkit_order : Ordre
			$rqt = "create table if not exists cms_toolkits(
				cms_toolkit_name varchar(255) not null default '' primary key,
				cms_toolkit_active int(1) NOT NULL DEFAULT 0,
				cms_toolkit_data text not null,
				cms_toolkit_order int(3) unsigned not null default 0)";
			echo traite_rqt($rqt,"create table cms_toolkits");

			//DG - Ajout de l'ordre sur les recherches pr�d�finies Gestion
			$rqt = "ALTER TABLE search_perso ADD search_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD search_order") ;

			//JP - Champs persos sur les documents num�riques
			$rqt = "create table if not exists explnum_custom (
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
				comment BLOB NOT NULL default '',
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table explnum_custom ");

			$rqt = "create table if not exists explnum_custom_lists (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_list_value varchar(255) default NULL,
				explnum_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY explnum_champ_list_value (explnum_custom_champ,explnum_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table explnum_custom_lists ");

			$rqt = "create table if not exists explnum_custom_values (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_origine int(10) unsigned NOT NULL default 0,
				explnum_custom_small_text varchar(255) default NULL,
				explnum_custom_text text,
				explnum_custom_integer int(11) default NULL,
				explnum_custom_date date default NULL,
				explnum_custom_float float default NULL,
				explnum_custom_order int(11) NOT NULL default 0,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY i_encv_st (explnum_custom_small_text),
				KEY i_encv_t (explnum_custom_text(255)),
				KEY i_encv_i (explnum_custom_integer),
				KEY i_encv_d (explnum_custom_date),
				KEY i_encv_f (explnum_custom_float),
				KEY explnum_custom_origine (explnum_custom_origine)) " ;
			echo traite_rqt($rqt,"create table explnum_custom_values ");

			//JP - param�tre utilisateur : paniers du catalogue d�pli�s ou non par d�faut
			$rqt = "ALTER TABLE users ADD deflt_catalog_expanded_caddies INT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_catalog_expanded_caddies");
			
			//JP - Ajout du champ de notice Droit d'usage
			$rqt = "CREATE TABLE if not exists notice_usage (id_usage INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT , usage_libelle VARCHAR( 255 ) NOT NULL default '', PRIMARY KEY ( id_usage ) , INDEX ( usage_libelle ) ) " ;
			echo traite_rqt($rqt,"CREATE TABLE notice_usage ");
			$rqt = "ALTER TABLE notices ADD num_notice_usage INT( 8 ) UNSIGNED DEFAULT 0 NOT NULL ";
			echo traite_rqt($rqt,"ALTER TABLE notices ADD num_notice_usage ");

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.22");
			break;
			
		case "v5.22":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
					
			// DG - Veilles : Option pour filtrer les nouveaux items avec une expression bool��nne au niveau de la source
			$rqt = "ALTER TABLE docwatch_datasources ADD datasource_boolean_expression varchar(255) not null default '' after datasource_clean_html" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_boolean_expression ");
				
			// DG - Veilles : Option pour filtrer les nouveaux items avec une expression bool��nne au niveau de la veille
			$rqt = "ALTER TABLE docwatch_watches ADD watch_boolean_expression varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_boolean_expression ");
				
			// DG - Items de veilles : Index sans les mots vides
			$rqt = "ALTER TABLE docwatch_items ADD item_index_sew mediumtext not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_sew ");
				
			// DG - Items de veilles : Index avec les mots vides
			$rqt = "ALTER TABLE docwatch_items ADD item_index_wew mediumtext not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_wew ");
				
			// DG - Cr�ation d'une table pour stocker le source des sites surveill�s
			// datasource_monitoring_website_num_datasource : Identifiant de la source
			// datasource_monitoring_website_upload_date : Date du t�l�chargement
			// datasource_monitoring_website_content : Contenu
			// datasource_monitoring_website_content_hash : Hash du contenu
			$rqt = "create table if not exists docwatch_datasource_monitoring_website(
				datasource_monitoring_website_num_datasource int(10) unsigned not null default 0 primary key,
				datasource_monitoring_website_upload_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				datasource_monitoring_website_content mediumtext not null,
				datasource_monitoring_website_content_hash varchar(255) not null default '')";
			echo traite_rqt($rqt,"create table docwatch_datasource_monitoring_website");
				
			// TS & AP - Cr�ation d'une table de liaison entre SESSID et token (pour les SSO)
			// sessions_tokens_SESSID : SESSID
			// sessions_tokens_token : Token
			// sessions_tokens_type : Information sur l'utilisation du token
			$rqt = "create table if not exists sessions_tokens(
				sessions_tokens_SESSID varchar(12) not null default '',
				sessions_tokens_token varchar(255) NOT NULL default '',
				sessions_tokens_type varchar(255) NOT NULL default '',
				primary key (sessions_tokens_SESSID, sessions_tokens_type),
				index i_st_sessions_tokens_type(sessions_tokens_type),
				index i_st_sessions_tokens_token(sessions_tokens_token))";
					echo traite_rqt($rqt,"create table sessions_tokens");
								
			// NG - Cr�ation de la table des facettes de notices externes
			$rqt = "CREATE TABLE if not exists facettes_external (
				id_facette int unsigned auto_increment,
				facette_name varchar(255) not null default '',
				facette_critere int(5) not null default 0,
				facette_ss_critere int(5) not null default 0,
				facette_nb_result int(2) not null default 0,
				facette_visible_gestion tinyint(1) not null default 0,
				facette_visible tinyint(1) not null default 0,
				facette_type_sort int(1) not null default 0,
				facette_order_sort int(1) not null default 0,
				facette_order int not null default 1,
				facette_limit_plus int not null default 0,
				facette_opac_views_num text NOT NULL,
				primary key (id_facette))";
			echo traite_rqt($rqt,"CREATE TABLE facettes_external");
		
			// NG - Ajout de la visibilit� des facettes en Gestion
			$rqt = "ALTER TABLE facettes add facette_visible_gestion tinyint(1) not null default 0 AFTER facette_nb_result";
			echo traite_rqt($rqt,"ALTER TABLE facettes add facette_visible_gestion ");

			//AP & TS - Ajout de la notion de fermeture des statuts de demandes de num�risation
			$rqt = "ALTER TABLE scan_request_status ADD scan_request_status_is_closed int(1) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE scan_request_status ADD scan_request_status_is_closed");

			// NG - Suppression de la table tu_oeuvres_others_links
			$rqt ="drop table if exists tu_oeuvres_others_links";
			echo traite_rqt($rqt,"drop table tu_oeuvres_others_links");

			// NG - Suppression de la table tu_oeuvres_expressions
			$rqt ="drop table if exists tu_oeuvres_expressions";
			echo traite_rqt($rqt,"drop table tu_oeuvres_expressions");

			// NG - Ajoute index scan_request_status_workflow
			$rqt ="alter table scan_request_status_workflow add primary key (scan_request_status_workflow_from_num, scan_request_status_workflow_to_num)";
			echo traite_rqt($rqt,"alter table scan_request_status_workflow add primary key");

			// NG - Ajoute index tu_oeuvres_links
			$rqt ="alter table tu_oeuvres_links add primary key (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link)";
			echo traite_rqt($rqt,"alter table tu_oeuvres_links add primary key");

			//DG - Parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_active' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_active',1,'Activer le comparateur de notices','',0)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_active into parametres");
			}

			// NG - Ajout d'un param�tre m�morisant le d�compte pr�f�r� � un type de demande
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'acquisition' and sstype_param='request_type_pref_account' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'acquisition', 'request_type_pref_account', '', '1', 'M�morise le d�compte pr�f�r� � un type de demande') ";
				echo traite_rqt($rqt,"INSERT acquisition_request_type_pref_account INTO parametres") ;
			}
											
			//Alexandre - Pr�remplissage du prix des exemplaires avec le prix indiqu� en notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='prefill_prix' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'prefill_prix', '0', 'Pr�remplissage du prix des exemplaires avec le prix indiqu� en notice ? \n 0 : Non \n 1 : Oui', '',0) ";
				echo traite_rqt($rqt, "insert pmb_prefill_prix=0 into parametres");
			}

			// NG - Modification des commentaires des param�tres de g�olocalisation
			$rqt = "UPDATE parametres SET comment_param = 'Activation de la g�olocalisation:\n 0 : Non \n 1 : Pour toutes les cartes \n 2 : Seulement pour les cartes de notices \n 3 : Seulement pour les cartes de localisation des exemplaires'
					WHERE type_param= 'opac' and sstype_param='map_activate' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_activate");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_notice_view' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_notice_view");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_search_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_search_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en r�sultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_search_result' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_search_result");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en �dition de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_notice_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_notice_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_notice_view' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_notice_view");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_search_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_search_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en r�sultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_search_result' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_search_result");
				
			// TS & NG - D�finition de la couleur d'une emprise de sur-localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_sur_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_holds_sur_location_color', '#D60F0F', 'Couleur des emprises associ�es � des sur-localisations','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_sur_location_color into parametres");
			}
				
			// TS & NG - Ajout param�tre de la taille de la carte des localisations dans les facettes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_facette' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_size_location_facette', '100%*200px', 'Taille de la carte des localisations dans les facettes. En pixels ou en pourcentage. Exemple : 100%*200px','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_facette into parametres");
			}
				
			// TS & NG - Ajout param�tre de la taille de la carte des localisations dans la page d'accueil de l'Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_home_page' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_size_location_home_page', '100%*200px', 'Taille de la carte des localisations dans la page d\'accueil de l\'Opac. En pixels ou en pourcentage. Exemple : 100%*480px','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_home_page");
			}

			// NG - Param�tre d'activation de la localisation d'une demande de num�risation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_location_activate' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','scan_request_location_activate',0,'Activer la localisation d\'une demande de num�risation','',0)";
				echo traite_rqt($rqt,"insert pmb_scan_request_location_activate into parametres");
			}

			// NG - Ajout de la localisation d'une demande de num�risation
			$rqt = "ALTER TABLE scan_requests ADD scan_request_num_location INT UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE scan_requests ADD scan_request_num_location");
				
			// JP - Pouvoir cacher les documents num�riques dans les options d'impression des paniers � l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_explnum' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'print_explnum', '1', 'Activer la possibilit� d\'imprimer les documents num�riques.\n 0: non \n 1: oui','h_cart',0)";
				echo traite_rqt($rqt,"insert opac_print_explnum into parametres");
			}

			// AR - Permettre de masquer les premi�res pages affich�es par d�faut dans les listes d'autorit�s (Onglet autorit�s et popup de s�lection)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_authorities_first_page' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'allow_authorities_first_page', '1', 'Active ou non l\'affichage par d�faut la premi�re page d\'une liste d\'autorit� lorsque l\'on arrive en s�lection dans un popup.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_allow_authorities_first_page into parametres");
			}
				
			// NG - Autoriser le pr�t d'un exemplaire d�j� pr�t�
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_already_borrowed' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'pret_already_borrowed', '0', 'Autoriser le pr�t d\'un exemplaire d�j� pr�t�.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_pret_already_borrowed into parametres");
			}
				
			// NG - Ajout pr�f�rences utilisateur en cr�attion d'une fiche lecteur
			$rqt = "ALTER TABLE users ADD deflt_empr_categ INT UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_empr_statut ";
			echo traite_rqt($rqt, "add deflt_empr_categ in table users");
			$rqt = "ALTER TABLE users ADD deflt_empr_codestat INT UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_empr_categ ";
			echo traite_rqt($rqt, "add deflt_empr_codestat in table users");
											

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
 			echo form_relance ("v5.23");
			break;
			
		case "v5.23":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			
			// DG/JP - ajout des param�tres d'export pour les notices li�es horizontales
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_horizontale' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_horizontale', '0', 'Lien vers les notices li�es horizontales', '', '1')";
				echo traite_rqt($rqt,"insert exportparam_export_horizontale='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_horizontale_link' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_notice_horizontale_link', '0', 'Exporter les notices li�es horizontales', '', '1')";
				echo traite_rqt($rqt,"insert exportparam_export_notice_horizontale_link='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_horizontale' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_horizontale', '0', 'Lien vers les notices li�es horizontales', '', '1')";
				echo traite_rqt($rqt,"insert opac_exp_export_horizontale='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_horizontale_link' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_notice_horizontale_link', '0', 'Exporter les notices li�es horizontales', '', '1')";
				echo traite_rqt($rqt,"insert opac_exp_export_notice_horizontale_link='0' into parametres ");
			}
			// DG/JP - mise � jour de la table notices_relations
			if (pmb_mysql_num_rows(pmb_mysql_query("show columns from notices_relations like 'id_notices_relations'"))==0) {
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}			
			// AR - Activer les ontologies g�n�riques et l'onglet s�mantique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'semantic' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
								VALUES (0, 'semantic', 'active', '0', 'Module \"S�mantique\" activ�.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert semantic_active into parametres");
			}

			// NG - Activer le focus sur le champ de recherche a l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='focus_user_query' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'focus_user_query', '1', 'Activer le focus sur le champ de recherche.\n 0: non \n 1:Oui','c_recherche',0)";
				echo traite_rqt($rqt,"insert opac_focus_user_query into parametres");
			}
			
			//NG - Parametre pour afficher dans tous les cas la source et la destination en �dition de transfert
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='edition_show_all_colls' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'edition_show_all_colls', '0', '1', 'Afficher dans tous les cas la source et la destination en �dition de transfert') ";
				echo traite_rqt($rqt,"INSERT transferts_edition_show_all_colls INTO parametres") ;
			}
			
			//JP - Ajout d'index sur la table cat�gories
			$rqt = "alter table categories drop index i_num_thesaurus";
			echo traite_rqt($rqt,"alter table categories drop index i_num_thesaurus");
			$rqt = "alter table categories add index i_num_thesaurus(num_thesaurus)";
			echo traite_rqt($rqt,"alter table categories add index i_num_thesaurus");

			///JP - Nombre de notices diffus�es pour dsi priv�es
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_nb_notices' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'dsi', 'private_bannette_nb_notices', '30', 'Nombre maximum par d�faut de notices diffus�es dans les bannettes priv�es.','', 0)";
				echo traite_rqt($rqt,"insert dsi_private_bannette_nb_notices into parametres");
			}
				
			// AP-VT - Param�tre d'activation des graphes c�t� gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_graph_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'entity_graph_activate', '0', 'Active ou non le graphe des entit�s PMB.\n 0: Non \n 1: Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_entity_graph_activate into parametres");
			}
				
			// AP-VT - D�finition du niveau de r�cursion affich� par d�faut dans le graphe
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_graph_recursion_lvl' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'entity_graph_recursion_lvl', '1', 'Valeur num�rique d�finissant le niveau de profondeur du graphe.','',0)";
				echo traite_rqt($rqt,"insert pmb_entity_graph_recursion_lvl into parametres");
			}
				
			// NG - VT - Cr�ation du param�tre d'activation des espaces de contribution en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_area_activate' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            	VALUES (NULL, 'pmb', 'contribution_area_activate', '0', 'Espace de contribution actif: \r\n0: Non\r\n1: Oui', '', '0')";
				echo traite_rqt($rqt,"insert pmb_contribution_area_activate=0 into parametres ");
			}
			
			// AP & TS - Cr�ation d'un param�tre permettant d'activer l'espace de contribution � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='contribution_area_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'contribution_area_activate', '0', 'Espace de contribution actif ? \n0 : Non\n1 : Oui\n','f_modules', 0)";
				echo traite_rqt($rqt,"insert opac_contribution_area_activate=0 into parametres");
			}
				
			// NG - VT Cr�ation de la table des espaces de contribution
			// id_area: identifiant de l'espace de contribution
			// area_title: nom de l'espace de contribution
			// area_comment : Commentaire associ� � l'espace de contribution
			// area_color : Couleur associ�e � l'espace de contribution
			// area_order : Ordre associ� � l'espace de contribution
			$rqt="create table if not exists contribution_area_areas(
				id_area int unsigned not null auto_increment primary key,
				area_title varchar(255) not null default '',
				area_comment text not null,
				area_color varchar(10) not null default '',
				area_order int(5) not null default 0
			)";
			echo traite_rqt($rqt, "create table contribution_area_areas");
		
			// AR - VT Cr�ation de la table des formulaires de contribution
			// id_form: Identifiant du formulaire
			// form_title: Nom du formulaire
			// form_type: type d'entit� que repr�sente le formulaire (notice, auteur, categ etc...)
			// form_status : statut du formulaire
			// form_parameters: structure JSON contenant le param�trage sp�cifique du formulaire (champs affich�s, label choisis etc..)
			$rqt="create table if not exists contribution_area_forms(
				id_form int unsigned not null auto_increment primary key,
				form_title varchar(255) not null default '',
				form_type varchar(255) not null default '',
				form_status int(3) unsigned NOT NULL DEFAULT 1,
				form_parameters blob not null
			)";
			echo traite_rqt($rqt, "create table contribution_area_forms");
			
			//AP & TS - param�trages des droits d'acc�s sur les espaces de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution' "))==0){
			$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_contribution',0,'Gestion des droits d\'acc�s des emprunteurs aux espaces de contribution\n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_contribution=0 into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_def' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_contribution_def',0,'Valeur par d�faut en modification d\'espaces de contribution pour les droits d\'acc�s emprunteurs - espaces de contribution \n0 : Recalculer.\n1 : Choisir.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_contribution_def=0 into parametres");
			}
		
			//AP & TS - Cr�ation de la table des status des espaces de contribution
			//contribution_area_status_id : identifiant du statut
			//contribution_area_status_gestion_libelle : libell� du statut en gestion
			//contribution_area_status_opac_libelle : libell� du statut en OPAC
			//contribution_area_status_class_html : classe HTML du statut
			//contribution_area_status_available_for : D�finit les types d'entit�s PMB pour lesquelles un statut est disponible
			$rqt="create table if not exists contribution_area_status(
			contribution_area_status_id int unsigned not null auto_increment primary key,
				contribution_area_status_gestion_libelle varchar(255) not null default '',
				contribution_area_status_opac_libelle varchar(255) not null default '',
				contribution_area_status_class_html varchar(255) not null default '',
				contribution_area_status_available_for text default null
			)";
			echo traite_rqt($rqt, "create table contribution_area_status");
				
			// AP & TS - Statut par d�faut pour la contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from contribution_area_status where contribution_area_status_id ='1' "))==0) {
				$rqt = 'INSERT INTO contribution_area_status (contribution_area_status_id, contribution_area_status_gestion_libelle, contribution_area_status_opac_libelle, contribution_area_status_class_html, contribution_area_status_available_for) VALUES (1,"Statut par d�faut", "Statut par d�faut", "statutnot1","'.addslashes('a:10:{i:0;s:6:"record";i:1;s:6:"author";i:2;s:8:"category";i:3;s:9:"publisher";i:4;s:10:"collection";i:5;s:14:"sub_collection";i:6;s:5:"serie";i:7;s:4:"work";i:8;s:8:"indexint";i:9;s:7:"concept";}').'")';
				echo traite_rqt($rqt,"insert default contribution_area_status");
			}
				
			//NG & TS - Cr�ation de la table des equations de recherches pour les s�lecteurs de ressource dans les formulaires de contribution
			//contribution_area_equation_id : identifiant de l'equation
			//contribution_area_equation_name : libell� de l'equation
			//contribution_area_equation_type : type de l'equation
			//contribution_area_equation_query : requete de recherche de l'equation
			//contribution_area_equation_human_query : requete comprehensible
			$rqt="create table if not exists contribution_area_equations(
			contribution_area_equation_id int unsigned not null auto_increment primary key,
				contribution_area_equation_name varchar(255) not null default '',
				contribution_area_equation_type varchar(255) not null default '',
				contribution_area_equation_query text not null,
				contribution_area_equation_human_query text not null
			)";
			echo traite_rqt($rqt, "create table contribution_area_equations");
		
			// AP - Activer les statistiques de fr�quentation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='empr' and sstype_param='visits_statistics_active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'empr', 'visits_statistics_active', '0', 'Activer les statistiques de fr�quentation.\n 0 : Non \n 1 : Oui','',0)";
				echo traite_rqt($rqt,"insert empr_visits_statistics_active=0 into parametres");
			}
		
			// AP - Cr�ation d'une table pour stocker les statistiques de fr�quentation
			// visits_statistics_date : Date
			// visits_statistics_location : Localisation
			// visits_statistics_type : Type de service
			// visits_statistics_value : Valeur
			$rqt = "CREATE TABLE if not exists visits_statistics (
			visits_statistics_date DATE NOT NULL default '0000-00-00',
			visits_statistics_location SMALLINT(5) UNSIGNED NOT NULL default 0,
					visits_statistics_type VARCHAR(255) NOT NULL default '',
					visits_statistics_value INT UNSIGNED NOT NULL default 0,
					primary key (visits_statistics_date, visits_statistics_location, visits_statistics_type)
			)";
			echo traite_rqt($rqt,"CREATE TABLE visits_statistics ") ;
			
			// AP - Ajout d'une colonne de signature dans la table de documents num�riques
						// Attention, chez certain client, cette op�ration peut prendre beaucoup de temps et mener � un timeout
			if (pmb_mysql_num_rows(pmb_mysql_query("show columns from explnum like 'explnum_signature'")) == 0){
			$info_message = "<font color=\"#FF0000\">ATTENTION</font> si vous avez beaucoup de documents num�riques, la prochaine op�ration peut prendre du temps et n�cessiter une nouvelle mise � jour de base !";
				echo "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($info_message) : $info_message)."</font></td><td></td></tr>";
			flush();
				ob_flush();
			
				$rqt = "ALTER TABLE explnum ADD explnum_signature varchar(255) not null default ''";
				echo traite_rqt($rqt,"alter table explnum add explnum_signature");
			}
			
			// TS - VT // Ajout d'un parametre cach� contenant le nom d'utilisateur du webservice associ� � la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_username' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_username','','Param�tre cach� contenant le nom d\'utilisateur du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_username='' into parametres");
			}
		
			// TS - VT // Ajout d'un parametre cach� contenant le mot de passe de l'utilisateur du ws associ� � la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_password' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_password','','Param�tre cach� contenant le mot de passe de l\'utilisateur du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_password='' into parametres");
			}
		
			// TS - VT // Ajout d'un parametre cach� contenant l'url du webservice associ� � la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_url' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_url','','Param�tre cach� contenant l\'url du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_url='' into parametres");
			}
		
			// AP - Param�tre de contr�le des doublons de documents num�riques
						if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='explnum_controle_doublons' "))==0){
						$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'explnum_controle_doublons', '0', 'Contr�le sur les doublons de documents num�rique.\n 0 : Aucun d�doublonnage\n 1 : D�doublonnage sur le contenu des documents num�riques','',0)";
				echo traite_rqt($rqt,"insert pmb_explnum_controle_doublons=0 into parametres");
			}
		
			// VT & DG - Param�tre de transfert de panier anonyme
						// 0 : Non
						// 1 : Sur demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='integrate_anonymous_cart' "))==0){
						$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'integrate_anonymous_cart', '1', 'Proposer le transfert des �l�ments du panier lors de l\'authentification.\n 0 : Non\n 1 : Sur demande','h_cart',0)";
				echo traite_rqt($rqt,"insert opac_integrate_anonymous_cart=1 into parametres");
			}
		
			// DG - VT Activer l'autocompletion lecteur sur l'impression de recherche � l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email_autocomplete' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email_autocomplete', '0', 'Autoriser la compl�tion de l\'adresse mail sur le formulaire d\'impression de recherche\n 0 : Non \n 1 : Seulement pour les lecteurs connect�s \n 2 : Pour tous les lecteurs', 'a_general', '0')";
				echo traite_rqt($rqt,"insert opac_print_email_autocomplete='0' into parametres ");
			}
				
			///JP - Affichage simplifi� du panier OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='simplified_cart' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'simplified_cart', '0', 'Affichage simplifi� du panier.\n 0 : non \n 1 : oui','h_cart', 0)";
				echo traite_rqt($rqt,"insert opac_simplified_cart=0 into parametres");
			}
			
			//JP - Prix de l'exemplaire dans le param�trage de l'abonnement
			$rqt = "ALTER TABLE abts_abts ADD prix varchar(255) NOT NULL DEFAULT ''";
			echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD prix");
			
			//JP - Envoi d'email dans les demandes de num�risation (param�tre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_send_mail_status' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'scan_request_send_mail_status', '', 'Envoi d\'email au destinataire sur passage de la demande � ces statuts','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_send_mail_status='' into parametres");
			}			
			
			//JP - Bloquer les pr�ts d�s qu'un lecteur est en retard
			$rqt = "update parametres set comment_param='D�lai � partir duquel le retard est pris en compte pour le blocage\n 0 : d�s qu\'un pr�t est en retard\n N : au bout de N jours de retard' where type_param= 'pmb' and sstype_param='blocage_delai' ";
			echo traite_rqt($rqt,"update blocage_delai into parametres");
			$rqt = "update parametres set comment_param='Nombre maximum de jours bloqu�s\n 0 : pas de limite\n N : maxi N\n -1 : blocage lev� d�s qu\'il n\'y a plus de retard' where type_param= 'pmb' and sstype_param='blocage_max' ";
			echo traite_rqt($rqt,"update blocage_max into parametres");
			
			//JP - Ajout d'index sur la table es_searchcache
			$rqt = "alter table es_searchcache drop index i_es_searchcache_date";
			echo traite_rqt($rqt,"alter table es_searchcache drop index i_es_searchcache_date");
			$rqt = "alter table es_searchcache add index i_es_searchcache_date(es_searchcache_date)";
			echo traite_rqt($rqt,"alter table es_searchcache add index i_es_searchcache_date");
				
			//JP - Lien vers la documentation des fonctions de parse HTML dans les param�tres
			$rqt = "update parametres set comment_param=CONCAT(comment_param,'\n<a href=\'".$base_path."/includes/interpreter/doc?group=inhtml\' target=\'_blank\'>Consulter la liste des fonctions disponibles</a>') where type_param= 'opac' and sstype_param='parse_html' ";
			echo traite_rqt($rqt,"update parse_html into parametres");
			
			//MB - Ajout d'index sur la table es_cache_blob
			$req="SHOW INDEX FROM es_cache_blob WHERE key_name LIKE 'i_es_cache_expirationdate'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "alter table es_cache_blob add index i_es_cache_expirationdate(es_cache_expirationdate)";
				echo traite_rqt($rqt,"alter table es_cache_blob add index i_es_cache_expirationdate");
			}
			
			//JP - filtres de relances personnalisables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_relance_rows' "))==0){
				$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param, section_param, gestion) VALUES ('empr','filter_relance_rows', 'g,cs', 'Crit�res de filtrage ajout�s aux crit�res existants pour les relances � faire, saisir les crit�res s�par�s par des virgules.\nLes crit�res disponibles correspondent � l\'attribut value du fichier substituable empr_list.xml', '','0')";
				echo traite_rqt($rqt,"insert empr_filter_relance_rows into parametres");
			}
				
			//TS & DG - Liste des pages FRBR
			// id_page : Identifiant de la page
			// page_name : Libell� de la page
			// page_comment : Description de la page
			// page_entity : Quelle page d'entit� ?
			// page_parameters : Param�tres
			// page_opac_views : Vues OPAC
			$rqt="create table if not exists frbr_pages (
	            id_page int unsigned not null auto_increment primary key,
	            page_name varchar(255) not null default '',
				page_comment text not null,
	            page_entity varchar(255) not null default '',
				page_parameters text not null,
			    page_opac_views varchar(255) not null default ''
			)";
			echo traite_rqt($rqt,"create table frbr_pages");
				
			//TS & DG - Liste des donn�es FRBR
			// id_datanode : Identifiant du noeud
			// datanode_name : Libell�
			// datanode_comment : Description
			// datanode_object : Classe PHP
			// datanode_num_page : Page associ�e
			// datanode_num_parent : Noeud de donn�es parent
			$rqt="create table if not exists frbr_datanodes (
	            id_datanode int unsigned not null auto_increment primary key,
	            datanode_name varchar(255) not null default '',
				datanode_comment text not null,
				datanode_object varchar(255) not null default '',
				datanode_num_page int unsigned not null default 0,
				datanode_num_parent int unsigned not null default 0
        	)";
			echo traite_rqt($rqt,"create table frbr_datanodes");
				
			//TS & DG - Liste des donn�es FRBR
			// id_datanode_content : Identifiant d'un �l�ment du noeud
			// datanode_content_type : Type (datasource, filter, etc..)
			// datanode_content_object : Classe PHP
			// datanode_content_num_datanode : Identifiant du noeud associ�
			// datanode_content_data : Donn�es
			$rqt="create table if not exists frbr_datanodes_content (
	            id_datanode_content int unsigned not null auto_increment primary key,
	            datanode_content_type varchar(255) not null default '',
				datanode_content_object varchar(255) not null default '',
				datanode_content_num_datanode int unsigned not null default 0,
				datanode_content_data text not null
        	)";
			echo traite_rqt($rqt,"create table frbr_datanodes_content");
		
			//TS & DG - Liste des cadres FRBR
			// id_cadre : Identifiant du cadre
			// cadre_name : Libell�
			// cadre_comment : Description
			// cadre_object : Classe PHP
			// cadre_css_class : Classe CSS
			// cadre_num_datanode : Noeud de donn�es associ�
			$rqt="create table if not exists frbr_cadres (
	            id_cadre int unsigned not null auto_increment primary key,
	            cadre_name varchar(255) not null default '',
				cadre_comment text not null,
				cadre_object varchar(255) not null default '',
				cadre_css_class VARCHAR(255) NOT NULL DEFAULT '',
				cadre_num_datanode int unsigned not null default 0
        	)";
			echo traite_rqt($rqt,"create table frbr_cadres");
							
			//TS & DG - Liste des �l�ments de cadres FRBR
			// id_cadre_content : Identifiant d'un �l�ment du cadre
			// cadre_content_type : Type (view, filter, etc..)
			// cadre_content_object : Classe PHP
			// cadre_content_num_cadre : Identifiant du cadre associ�
			// cadre_content_data : Donn�es
			$rqt="create table if not exists frbr_cadres_content (
			id_cadre_content int unsigned not null auto_increment primary key,
	            cadre_content_type varchar(255) not null default '',
				cadre_content_object varchar(255) not null default '',
				cadre_content_num_cadre int unsigned not null default 0,
				cadre_content_data text not null default ''
        	)";
			echo traite_rqt($rqt,"create table frbr_cadres_content");
			
			//DG - Gestion des entit�s pour le FRBR
			$rqt="create table if not exists frbr_managed_entities (
			managed_entity_name varchar(255) not null default '',
			managed_entity_box text not null,
			primary key (managed_entity_name))";
			echo traite_rqt($rqt, "create table if not exists frbr_managed_entities");
			
			//DG - Vignettes sur les autorit�s
			$rqt = "ALTER TABLE authorities ADD thumbnail_url MEDIUMBLOB NOT NULL" ;
			echo traite_rqt($rqt,"ALTER TABLE authorities ADD thumbnail_url ");
				
			//DG - R�pertoire d'upload pour les vignettes sur les autorit�s
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_img_folder_id' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'pmb', 'authority_img_folder_id', '0', 'Identifiant du r�pertoire d\'upload des vignettes d\'autorit�s', '', '0')" ;
				echo traite_rqt($rqt,"insert pmb_authority_img_folder_id into parametres") ;
			}
							
			//DG - D�finition de la taille maximum des vignettes sur les autorit�s
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_img_pics_max_size' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'authority_img_pics_max_size', '150', 'Taille maximale des vignettes upload�es dans les autorit�s, en largeur ou en hauteur')";
				echo traite_rqt($rqt,"insert pmb_authority_img_pics_max_size='150' into parametres");
			}
			
			//NG - Update fonction d'auteur 'danseur' du code 274 en 275, suite au changement de function.xml
			$rqt = "UPDATE responsability set responsability_fonction='275' where responsability_fonction='274' ";
			echo traite_rqt($rqt,"UPDATE responsability set responsability_fonction ");
			$rqt = "UPDATE responsability_tu set responsability_tu_fonction='275' where responsability_tu_fonction='274' ";
			echo traite_rqt($rqt,"UPDATE responsability_tu set responsability_tu_fonction ");
							
			//NG - Ajout du champ commentaire de gestion dans les �tag�res
			$rqt = "ALTER TABLE etagere ADD comment_gestion TEXT NOT NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD comment_gestion ");

			//DG - Ordre sur les pages FRBR
			$rqt = "alter table frbr_pages add page_order int not null default 1";
			echo traite_rqt($rqt,"alter table frbr_pages add page_order");
									
			//TS & DG - Liste des donn�es FRBR
			// id_page_content : Identifiant d'un �l�ment du noeud
			// page_content_type : Type (backbone, etc..)
			// page_content_object : Classe PHP
			// page_content_num_page : Identifiant du noeud associ�
			// page_content_data : Donn�es
			$rqt="create table if not exists frbr_pages_content (
				id_page_content int unsigned not null auto_increment primary key,
	            page_content_type varchar(255) not null default '',
				page_content_object varchar(255) not null default '',
				page_content_num_page int unsigned not null default 0,
				page_content_data text not null
        	)";
			echo traite_rqt($rqt,"create table frbr_pages_content");
		
			//TS & DG - Placement des cadres FRBR
			// place_num_page : Identifiant de la page associ�e
			// place_num_cadre : Identifiant du cadre associ�
			// place_cadre_type : Type de cadre (natif, dynamique)
			// place_visibility : Visibilit� du cadre
			// place_order : Ordre
			$rqt="create table if not exists frbr_place (
				place_num_page int unsigned not null default 0,
				place_num_cadre int unsigned not null default 0,
				place_cadre_type varchar(255) not null default '',
	            place_visibility int(1) not null default 0,
				place_order int(10) UNSIGNED NOT NULL default 0,
				PRIMARY KEY(place_num_page,place_num_cadre, place_cadre_type)
        	)";
			echo traite_rqt($rqt,"create table frbr_place");
			
			//TS - Num�ro de page sur les cadres (n�cessaire pour les  cadres li�s uniquement � la page)
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_num_page int(10) UNSIGNED NOT NULL default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_num_page");
		
			//DG - Paniers d'autorit�s
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie (
			      idcaddie int(8) unsigned NOT NULL AUTO_INCREMENT,
				  name varchar(255) NOT NULL DEFAULT '',
				  type varchar(20) NOT NULL DEFAULT '',
				  comment varchar(255) NOT NULL DEFAULT '',
				  autorisations mediumtext,
				  caddie_classement varchar(255) NOT NULL DEFAULT '',
				  acces_rapide int(11) NOT NULL DEFAULT '0',
				  creation_user_name varchar(255) NOT NULL DEFAULT '',
				  creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (idcaddie),
				  KEY caddie_type (type)) ";
			echo traite_rqt($rqt,"create table authorities_caddie");
		
			//DG - Contenu des paniers d'autorit�s
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie_content (
				caddie_id int(8) unsigned NOT NULL DEFAULT '0',
				object_id int(10) unsigned NOT NULL DEFAULT '0',
				flag varchar(10) DEFAULT NULL,
				PRIMARY KEY (caddie_id,object_id),
				KEY object_id (object_id)) ";
			echo traite_rqt($rqt,"create table authorities_caddie_content");
		
			//DG - Proc�dures des paniers d'autorit�s
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie_procs (
				idproc smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				type varchar(20) NOT NULL DEFAULT 'SELECT',
				name varchar(255) NOT NULL DEFAULT '',
				requete blob NOT NULL,
				comment tinytext NOT NULL,
				autorisations mediumtext,
				parameters text,
				PRIMARY KEY (idproc)) ";
			echo traite_rqt($rqt,"create table authorities_caddie_procs");
		
		
			//NG - Regrouper les fonctions d'auteur en affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_author_functions_grouping' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
							VALUES ('pmb','notice_author_functions_grouping',1,'Regrouper les fonctions d\'auteur en affichage de notice ? \n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert pmb_notice_author_functions_grouping=1 into parametres");
			}
		
			//AP - Champs persos sur les concepts
			$rqt = "create table if not exists skos_custom (
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
				comment BLOB NOT NULL default '',
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table skos_custom ");
			
			$rqt = "create table if not exists skos_custom_lists (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_list_value varchar(255) default NULL,
				skos_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY skos_custom_champ (skos_custom_champ),
				KEY skos_champ_list_value (skos_custom_champ,skos_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table skos_custom_lists ");
			
			$rqt = "create table if not exists skos_custom_values (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_origine int(10) unsigned NOT NULL default 0,
				skos_custom_small_text varchar(255) default NULL,
				skos_custom_text text,
				skos_custom_integer int(11) default NULL,
				skos_custom_date date default NULL,
				skos_custom_float float default NULL,
				skos_custom_order int(11) NOT NULL default 0,
				KEY skos_custom_champ (skos_custom_champ),
				KEY i_encv_st (skos_custom_small_text),
				KEY i_encv_t (skos_custom_text(255)),
				KEY i_encv_i (skos_custom_integer),
				KEY i_encv_d (skos_custom_date),
				KEY i_encv_f (skos_custom_float),
				KEY skos_custom_origine (skos_custom_origine)) " ;
			echo traite_rqt($rqt,"create table skos_custom_values ");
			
			//JP - cacher les amendes et frais de relance dans les lettres et mails de retard
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='hide_fine' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'mailretard','hide_fine','0','Masquer les amendes et frais de relance dans les lettres et mails de retard :\n 0 : Non\n 1 : Oui','',0)" ;
				echo traite_rqt($rqt,"insert mailretard_hide_fine into parametres") ;
			}
				
			//JP - param�tre pour forcer l'envoi de relance de niveau 2 par lettre si priorite_email = 1
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='priorite_email_2' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'mailretard','priorite_email_2','0','Forcer le deuxi�me niveau de relance par lettre si priorite_email = 1 :\n 0 : Non\n 1 : Oui','',0)" ;
				echo traite_rqt($rqt,"insert mailretard_priorite_email_2 into parametres") ;
			}
				
			//JP - Calculer le retard, l'amende et le blocage sur le calendrier d'ouverture de la localisation de l'utilisateur ou de l'exemplaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='utiliser_calendrier_location' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'pmb','utiliser_calendrier_location','0','Si le param�tre utiliser_calendrier est � 1, choix de la localisation pour calculer le retard, l\'amende et le blocage :\n 0 : calcul sur le calendrier d\'ouverture de la localisation de l\'utilisateur\n 1 : calcul sur le calendrier d\'ouverture de la localisation de l\'exemplaire','',0)" ;
				echo traite_rqt($rqt,"insert pmb_utiliser_calendrier_location into parametres") ;
			}

			// NG - Ajout d'un parametre cach� indiquant l'affichage ou non des sous-formulaires de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_opac_show_sub_form' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_opac_show_sub_form','','Param�tre cach� indiquant l\'affichage ou non des sous-formulaires de contribution','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_opac_show_sub_form='' into parametres");
			}
			
			// TS - cadre visible ou non dans le graphe
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_visible_in_graph tinyint(1) UNSIGNED NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_visible_in_graph");
				
			// TS - Chemin des jeux de donn�es parent du cadre
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_datanodes_path varchar(255) default NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_datanodes_path");
				
			// VT - Ajout de l'identifiant de la methode de stockage sur les ontologies (partie semantique)
			$rqt = "ALTER TABLE ontologies ADD ontology_storage_id INT NOT NULL default 0";
			echo traite_rqt($rqt,"alter table ontologies add ontology_storage_id");
				
			// AP / VT - Cr�ation d'une table permettant de stocker les informations li�es aux fichiers upload�s pour les ontologies
			$rqt = "CREATE TABLE IF NOT EXISTS onto_files (
						id_onto_file int(10) unsigned NOT NULL auto_increment primary key,
						onto_file_title varchar(255) NOT NULL DEFAULT '' ,
						onto_file_description text NOT NULL,
						onto_file_filename varchar(255) NOT NULL DEFAULT '',
						onto_file_mimetype varchar(100) NOT NULL DEFAULT '',
						onto_file_filesize int(11) NOT NULL DEFAULT '0',
						onto_file_vignette mediumblob NOT NULL,
						onto_file_url text NOT NULL,
						onto_file_path varchar(255)NOT NULL DEFAULT '',
						onto_file_create_date date NOT NULL DEFAULT '0000-00-00',
						onto_file_num_storage int(11) NOT NULL DEFAULT '0',
						onto_file_type_object varchar(255) NOT NULL DEFAULT '',
						onto_file_num_object int(11) NOT NULL DEFAULT '0',
						INDEX i_of_onto_file_title (onto_file_title)
					)";
			echo traite_rqt($rqt,"create table onto_files");
				
			// TS - ajout d'un index sur le code champ et code sous champ de la table authorities_fields_global_index
			$req="SHOW INDEX FROM authorities_fields_global_index WHERE key_name LIKE 'i_code_champ_code_ss_champ'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE authorities_fields_global_index ADD INDEX i_code_champ_code_ss_champ(code_champ,code_ss_champ)";
				echo traite_rqt($rqt,"ALTER TABLE authorities_fields_global_index ADD INDEX i_code_champ_code_ss_champ");
			}
				
			// TS - ajout d'un index sur le num_concept et type_object de la table index_concept
			$req="SHOW INDEX FROM index_concept WHERE key_name LIKE 'i_num_concept_type_object'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE index_concept ADD INDEX i_num_concept_type_object(num_concept,type_object)";
				echo traite_rqt($rqt,"ALTER TABLE index_concept ADD INDEX i_num_concept_type_object");
			}
				
			// TS - ajout d'un index sur le type_object et num_object de la table index_concept
			$req="SHOW INDEX FROM index_concept WHERE key_name LIKE 'i_type_object_num_object'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE index_concept ADD INDEX i_type_object_num_object(type_object,num_object)";
				echo traite_rqt($rqt,"ALTER TABLE index_concept ADD INDEX i_type_object_num_object");
			}
				
			// MB - Nouvelle table pour les param�tres de PMB � ne pas surtout pas mettre dans le cache_apc ni en global (param�tres modifi�s fr�quemment pour le fonctionnement de PMB)
			$rqt = "CREATE TABLE IF NOT EXISTS parametres_uncached (
					 id_param int(6) unsigned NOT NULL AUTO_INCREMENT,
					 type_param varchar(20) DEFAULT NULL,
					 sstype_param varchar(255) DEFAULT NULL,
					 valeur_param text,
					 comment_param longtext,
					 section_param varchar(255) NOT NULL DEFAULT '',
					 gestion int(1) NOT NULL DEFAULT '0',
					 PRIMARY KEY (id_param),
					 UNIQUE KEY typ_sstyp (type_param,sstype_param)
					)";
			echo traite_rqt($rqt,"create table parametres_uncached");
				
			// MB - Param�tre de verrou pour la gestion des stats
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres_uncached where type_param= 'internal' and sstype_param='emptylogstatopac' "))==0){
				$rqt = "INSERT INTO parametres_uncached (type_param, sstype_param, valeur_param, comment_param,gestion)
					VALUES ('internal', 'emptylogstatopac', '0', 'Param�tre interne, ne pas modifier\r\n =1 si vidage des logs en cours', 0)";
				echo traite_rqt($rqt,"insert internal_emptylogstatopac=0 into parametres_uncached");
			}
				
			// MB - Suppression de l'ancien param�tre d�plac� dans la nouvelle table
			if(isset($internal_emptylogstatopac)){
				$rqt = "delete from parametres where type_param= 'internal' and sstype_param='emptylogstatopac' " ;
				echo traite_rqt($rqt,"delete old parameter 'emptylogstatopac' from parametres");
			}
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.24");
			break;
				
		case "v5.24":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// MB - Param�tre pour d�finir le r�pertoire � utiliser pour le cache des images en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (NULL, 'pmb', 'img_cache_folder', '', 'R�pertoire de stockage du cache des images')";
				echo traite_rqt($rqt,"insert pmb_img_cache_folder='' into parametres ");
			}
				
			// MB - Param�tre pour d�finir l'URL du r�pertoire pour le cache des images en gestion et avoir des URLs en dur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_url' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (NULL, 'pmb', 'img_cache_url', '', 'URL d\'acc�s du r�pertoire du cache des images (img_cache_folder)')";
				echo traite_rqt($rqt,"insert pmb_img_cache_url='' into parametres ");
			}
				
			// MB - Param�tre pour d�finir le r�pertoire � utiliser pour le cache des images en opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'opac', 'img_cache_folder', '', 'R�pertoire de stockage du cache des images','a_general')";
				echo traite_rqt($rqt,"insert opac_img_cache_folder='' into parametres ");
			}
			// MB - Param�tre pour d�finir l'URL du r�pertoire pour le cache des images en opac et avoir des URLs en dur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_url' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'opac', 'img_cache_url', '', 'URL d\'acc�s du r�pertoire du cache des images (img_cache_folder)','a_general')";
				echo traite_rqt($rqt,"insert opac_img_cache_url='' into parametres ");
			}
			
			// DG - Index sur le champ oeuvre_link_from de la table tu_oeuvres_links
			$req="SHOW INDEX FROM tu_oeuvres_links WHERE key_name LIKE 'i_oeuvre_link_from'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_from(oeuvre_link_from)";
				echo traite_rqt($rqt,"ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_from");
			}
				
			// DG - Index sur le champ oeuvre_link_to de la table tu_oeuvres_links
			$req="SHOW INDEX FROM tu_oeuvres_links WHERE key_name LIKE 'i_oeuvre_link_to'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_to(oeuvre_link_to)";
				echo traite_rqt($rqt,"ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_to");
			}
			
			// DG - Index sur le champ oeuvre_link_to de la table nomenclature_notices_nomenclatures
			$req="SHOW INDEX FROM nomenclature_notices_nomenclatures WHERE key_name LIKE 'i_notice_nomenclature_num_notice'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD INDEX i_notice_nomenclature_num_notice(notice_nomenclature_num_notice)";
				echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD INDEX i_notice_nomenclature_num_notice");
			}
				
			// DG - Index sur le champ map_echelle_num de la table notices
			$req="SHOW INDEX FROM notices WHERE key_name LIKE 'i_map_echelle_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE notices ADD INDEX i_map_echelle_num(map_echelle_num)";
				echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_map_echelle_num");
			}
				
			// DG - Index sur le champ map_projection_num de la table notices
			$req="SHOW INDEX FROM notices WHERE key_name LIKE 'i_map_projection_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE notices ADD INDEX i_map_projection_num(map_projection_num)";
				echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_map_projection_num");
			}
				
			// DG - Index sur le champ authperso_authority_authperso_num de la table authperso_authorities
			$req="SHOW INDEX FROM authperso_authorities WHERE key_name LIKE 'i_authperso_authority_authperso_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE authperso_authorities ADD INDEX i_authperso_authority_authperso_num(authperso_authority_authperso_num)";
				echo traite_rqt($rqt,"ALTER TABLE authperso_authorities ADD INDEX i_authperso_authority_authperso_num");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.25");
			break;
			
		case "v5.25":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// NG - Ajout index sur num_word de la table notices_mots_global_index
			$req="SHOW INDEX FROM notices_mots_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_mots_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_num_word");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.26");
			break;
				
		case "v5.26":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// NG - Ajout index sur num_word de la table cms_editorial_words_global_index
			$req="SHOW INDEX FROM cms_editorial_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_editorial_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table cms_editorial_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table authorities_words_global_index
			$req="SHOW INDEX FROM authorities_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table skos_words_global_index
			$req="SHOW INDEX FROM skos_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table skos_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table skos_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table faq_questions_words_global_index
			$req="SHOW INDEX FROM faq_questions_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table faq_questions_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table faq_questions_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur responsability_author de la table responsability
			$req="SHOW INDEX FROM responsability WHERE key_name LIKE 'i_responsability_author'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table responsability add index i_responsability_author (responsability_author)";
				echo traite_rqt($rqt,"alter table responsability add index i_responsability_author");
			}
			
			// NG - Ajout index sur shorturl_hash de la table shorturls
			$req="SHOW INDEX FROM shorturls WHERE key_name LIKE 'i_shorturl_hash'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table shorturls add index i_shorturl_hash (shorturl_hash)";
				echo traite_rqt($rqt,"alter table shorturls add index i_shorturl_hash");
			}
			
			// NG - Ajout index sur empr_login de la table empr
			$req="SHOW INDEX FROM empr WHERE key_name LIKE 'i_empr_login'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table empr add index i_empr_login (empr_login)";
				echo traite_rqt($rqt,"alter table empr add index i_empr_login");
			}
			
			// NG - Ajout index sur ouvert, num_location, date_ouverture de la table ouvertures
			$req="SHOW INDEX FROM ouvertures WHERE key_name LIKE 'i_ouvert_num_location_date_ouverture'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table ouvertures add index i_ouvert_num_location_date_ouverture (ouvert, num_location, date_ouverture)";
				echo traite_rqt($rqt,"alter table ouvertures add index i_ouvert_num_location_date_ouverture");
			}
				
			// NG - Ajout index sur etat_transfert, origine de la table transferts
			$req="SHOW INDEX FROM transferts WHERE key_name LIKE 'i_etat_transfert_origine'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transferts add index i_etat_transfert_origine (etat_transfert, origine)";
				echo traite_rqt($rqt,"alter table transferts add index i_etat_transfert_origine");
			}
				
			// NG - Ajout index sur author_type de la table authors
			$req="SHOW INDEX FROM authors WHERE key_name LIKE 'i_author_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authors add index i_author_type (author_type)";
				echo traite_rqt($rqt,"alter table authors add index i_author_type");
			}
			
			// NG - Ajout index sur grammar de la table vedette
			$req="SHOW INDEX FROM vedette WHERE key_name LIKE 'i_grammar'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table vedette add index i_grammar (grammar)";
				echo traite_rqt($rqt,"alter table vedette add index i_grammar");
			}
				
			// NG - Suppression index doublon dans la table authorities
			$req="SHOW INDEX FROM authorities WHERE key_name LIKE 'i_object'";
			$res=pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				$req="ALTER TABLE authorities DROP INDEX i_object ";
				$res=pmb_mysql_query($req);
				echo traite_rqt($rqt,"alter table authorities drop index i_object");
			}
			
			// AP - Param�tre pour activer sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'active', '0', 'Sphinx activ�.\n 0 : Non\n 1 : Oui','')";
				echo traite_rqt($rqt,"insert sphinx_active = 0 into parametres ");
			}
				
			// AP - Param�tre de d�finition du chemin vers les index sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='indexes_path' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'indexes_path', '', 'Chemin vers le r�pertoire de stockage des index sphinx','')";
				echo traite_rqt($rqt,"insert sphinx_indexes_path = '' into parametres ");
			}
				
			// AP - Param�tre de connexion mysql pour sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='mysql_connect' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'mysql_connect', '127.0.0.1:9306,0', 'Param�tre de connexion mysql au serveur sphinx :\n hote:port,auth,user,pass : mettre 0 ou 1 pour l\'authentification.','')";
				echo traite_rqt($rqt,"insert sphinx_indexes_path = '127.0.0.1:9306,0' into parametres ");
			}
				
			// JP - Ajout index sur num_authority / authority_type de la table authorities_sources
			$req="SHOW INDEX FROM authorities_sources WHERE key_name LIKE 'i_num_authority_authority_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_sources add index i_num_authority_authority_type (num_authority, authority_type)";
				echo traite_rqt($rqt,"alter table authorities_sources add index i_num_authority_authority_type");
			}
				
			// JP - Ajout index sur cadre_memo_url de la table cms_cadres
			$req="SHOW INDEX FROM cms_cadres WHERE key_name LIKE 'i_cadre_memo_url'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadres add index i_cadre_memo_url (cadre_memo_url)";
				echo traite_rqt($rqt,"alter table cms_cadres add index i_cadre_memo_url");
			}
				
			// JP - Ajout index sur cadre_object de la table cms_cadres
			$req="SHOW INDEX FROM cms_cadres WHERE key_name LIKE 'i_cadre_object'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadres add index i_cadre_object (cadre_object)";
				echo traite_rqt($rqt,"alter table cms_cadres add index i_cadre_object");
			}
				
			// JP - Ajout index sur cadre_content_num_cadre de la table cms_cadre_content
			$req="SHOW INDEX FROM cms_cadre_content WHERE key_name LIKE 'i_cadre_content_num_cadre'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadre_content add index i_cadre_content_num_cadre (cadre_content_num_cadre)";
				echo traite_rqt($rqt,"alter table cms_cadre_content add index i_cadre_content_num_cadre");
			}
				
			// JP - Ajout index sur cadre_content_num_cadre_content / cadre_content_type cms_cadre_content de la table cms_cadre_content
			$req="SHOW INDEX FROM cms_cadre_content WHERE key_name LIKE 'i_cadre_content_num_cadre_content_cadre_content_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadre_content add index i_cadre_content_num_cadre_content_cadre_content_type (cadre_content_num_cadre_content, cadre_content_type)";
				echo traite_rqt($rqt,"alter table cms_cadre_content add index i_cadre_content_num_cadre_content_cadre_content_type");
			}
				
			// JP - Ajout index sur document_num_object / document_type_object de la table cms_documents
			$req="SHOW INDEX FROM cms_documents WHERE key_name LIKE 'i_document_num_object_document_type_object'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_documents add index i_document_num_object_document_type_object (document_num_object, document_type_object)";
				echo traite_rqt($rqt,"alter table cms_documents add index i_document_num_object_document_type_object");
			}
				
			// JP - Ajout index sur document_link_num_document de la table cms_documents_links
			$req="SHOW INDEX FROM cms_documents_links WHERE key_name LIKE 'i_document_link_num_document'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_documents_links add index i_document_link_num_document (document_link_num_document)";
				echo traite_rqt($rqt,"alter table cms_documents_links add index i_document_link_num_document");
			}
				
			// JP - Ajout index sur groupe / method / available de la table es_methods
			$req="SHOW INDEX FROM es_methods WHERE key_name LIKE 'i_groupe_method_available'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table es_methods add index i_groupe_method_available (groupe(50), method(50), available)";
				echo traite_rqt($rqt,"alter table es_methods add index i_groupe_method_available");
			}
				
			// JP - Ajout index sur facette_visible de la table facettes
			$req="SHOW INDEX FROM facettes WHERE key_name LIKE 'i_facette_visible'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table facettes add index i_facette_visible (facette_visible)";
				echo traite_rqt($rqt,"alter table facettes add index i_facette_visible");
			}
				
			// JP - Ajout index sur origine de la table import_marc
			$req="SHOW INDEX FROM import_marc WHERE key_name LIKE 'i_origine'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table import_marc add index i_origine (origine)";
				echo traite_rqt($rqt,"alter table import_marc add index i_origine");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.27");
			break;
			
		case "v5.27":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// JP - Ajout index sur id_notice de la table notices_mots_global_index
			$req="SHOW INDEX FROM notices_mots_global_index WHERE key_name LIKE 'i_id_notice'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_mots_global_index add index i_id_notice (id_notice)";
				echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_notice");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.28");
			break;
				
		case "v5.28":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// JP - Ajout index sur resa_idempr de la table resa
			$req="SHOW INDEX FROM resa WHERE key_name LIKE 'i_resa_idempr'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table resa add index i_resa_idempr (resa_idempr)";
				echo traite_rqt($rqt,"alter table resa add index i_resa_idempr");
			}
				
			// JP - Ajout index sur num_suggestion de la table suggestions_origine
			$req="SHOW INDEX FROM suggestions_origine WHERE key_name LIKE 'i_num_suggestion'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table suggestions_origine add index i_num_suggestion (num_suggestion)";
				echo traite_rqt($rqt,"alter table suggestions_origine add index i_num_suggestion");
			}
				
			// JP - Ajout index sur resa_trans de la table transferts_demande
			$req="SHOW INDEX FROM transferts_demande WHERE key_name LIKE 'i_resa_trans'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transferts_demande add index i_resa_trans (resa_trans)";
				echo traite_rqt($rqt,"alter table transferts_demande add index i_resa_trans");
			}
				
			// JP - Ajout index sur realisee de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_realisee'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_realisee (realisee)";
				echo traite_rqt($rqt,"alter table transactions add index i_realisee");
			}
				
			// JP - Ajout index sur compte_id de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_compte_id'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_compte_id (compte_id)";
				echo traite_rqt($rqt,"alter table transactions add index i_compte_id");
			}
			
			// DG - Ajout index sur id_notice de la table notices_fields_global_index
			$req="SHOW INDEX FROM notices_fields_global_index WHERE key_name LIKE 'i_id_notice'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_fields_global_index add index i_id_notice (id_notice)";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add index i_id_notice");
			}
			
			// DG - Ajout index sur id_authority de la table authorities_words_global_index
			$req="SHOW INDEX FROM authorities_words_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_words_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add index i_id_authority");
			}
				
			// DG - Ajout index sur id_authority de la table authorities_fields_global_index
			$req="SHOW INDEX FROM authorities_fields_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_fields_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_fields_global_index add index i_id_authority");
			}
				
						
			// VT & AP - Nouvelle table pour les r�gimes de licence de documents num�riques
					// id_explnum_licence : Identifiant
					// explnum_licence_label : Libell�
					// explnum_licence_uri : URI
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence (
						id_explnum_licence int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_label varchar(255) DEFAULT '',
						explnum_licence_uri varchar(255) DEFAULT ''
					)";
			echo traite_rqt($rqt,"create table explnum_licence");
		
			// VT & AP - Nouvelle table pour les profils de r�gimes de licence de documents num�riques
					// id_explnum_licence_profile : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du r�gime de licence
					// explnum_licence_profile_label : Libell�
					// explnum_licence_profile_uri : URI
					// explnum_licence_profile_logo_url : URL du logo
					// explnum_licence_profile_explanation : Texte explicatif
					// explnum_licence_profile_quotation_rights : Droit de citation du profil
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profiles (
						id_explnum_licence_profile int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_profile_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_label varchar(255) DEFAULT '',
						explnum_licence_profile_uri varchar(255) DEFAULT '',
						explnum_licence_profile_logo_url varchar(255) DEFAULT '',
						explnum_licence_profile_explanation text,
						explnum_licence_profile_quotation_rights text,
						INDEX i_elp_explnum_licence_num (explnum_licence_profile_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profiles");
		
			// VT & AP - Nouvelle table pour les droits de r�gimes de licence de documents num�riques
					// id_explnum_licence_right : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du r�gime de licence
					// explnum_licence_right_label : Libell�
					// explnum_licence_right_type : Type de droit (Autorisation / Interdiction)
					// explnum_licence_right_logo_url : URL du logo
					// explnum_licence_right_explanation : Texte explicatif
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_rights (
						id_explnum_licence_right int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_right_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_right_label varchar(255) DEFAULT '',
						explnum_licence_right_type int(2) DEFAULT 0,
						explnum_licence_right_logo_url varchar(255) DEFAULT '',
						explnum_licence_right_explanation text,
						INDEX i_elr_explnum_licence_num (explnum_licence_right_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_rights");
			
			// VT & AP - Nouvelle table pour les liens droits / profils
					// explnum_licence_right_num : Identifiant du droit
					// explnum_licence_profile_num : Identifiant du lien
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_rights (
						explnum_licence_profile_num INT not null DEFAULT 0,
						explnum_licence_right_num INT not null DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_num, explnum_licence_right_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_rights");

			// AP & VT - Nouvelle table associant un document num�rique � un r�gime de licence
			// explnum_licence_explnums_licence_num : Identifiant du r�gime de licence
			// explnum_licence_explnums_explnum_num : Identifiant du document num�rique
			$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_explnums (
						explnum_licence_profile_explnums_explnum_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_explnums_profile_num int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_explnums_explnum_num, explnum_licence_profile_explnums_profile_num),
						INDEX i_elpe_explnum_profile_num (explnum_licence_profile_explnums_profile_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_explnums");
			
			// DG - Modification de la cl� primaire de la table authorities_words_global_index
			$query = "SHOW KEYS FROM authorities_words_global_index WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_authority','code_champ','code_ss_champ','num_word','position','field_position');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 6) {
				$flag = true;
			}
			if($flag) {
				$rqt ="alter table authorities_words_global_index drop primary key";
				echo traite_rqt($rqt,"alter table authorities_words_global_index drop primary key");
				$rqt ="alter table authorities_words_global_index add primary key (id_authority,code_champ,code_ss_champ,num_word,position,field_position)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add primary key");
			}
			
			// NG - Zone d'affichage par d�faut de la carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par d�faut de la carte. Coordonn�es d\'un polygone ferm�, en degr�s d�cimaux','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_bounding_box into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par d�faut de la carte. Coordonn�es d\'un polygone ferm�, en degr�s d�cimaux','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_bounding_box into parametres");
			}
			
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.29");
			break;
			
		case "v5.29":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			//JP - Impression tickets de pr�t via raspberry pi
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))){
			    $rqt = "update parametres set comment_param=CONCAT(comment_param,'\n\nSi l\'imprimante est connect�e � un Raspberry Pi, indiquer l\'ip et le port\nExemple : raspberry@192.168.0.82:3000') where type_param='pmb' and sstype_param='printer_name' " ;
			    echo traite_rqt($rqt,"update parameters pmb_printer_name");
			}
			
			// JP - Rectification index sur index_author / author_type de la table authors
			$rqt = "alter table authors drop index i_index_author_author_type";
			echo traite_rqt($rqt,"alter table authors drop index i_index_author_author_type");
			$rqt = "alter table authors add index i_index_author_author_type (index_author (350), author_type)";
			echo traite_rqt($rqt,"alter table authors add index i_index_author_author_type");
			
			//JP - Ajout d'une colonne commentaire dans la table des recherches pr�d�finies
			if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM search_perso LIKE 'search_comment'"))){
			    $rqt = "alter table search_perso add search_comment text not null";
			    echo traite_rqt($rqt,"alter table search_perso add search_comment");
			}
			//JP - Export des informations de documents num�riques dans les notices en unimarc pmb xml
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='export_allow_expl' "))){
			    $rqt = "update parametres set comment_param='Exporter les exemplaires et les documents num�riques avec les notices :\n 0 : Aucun\n 1 : Uniquement les exemplaires\n 2 : Uniquement les documents num�riques\n 3 : Les exemplaires et les documents num�riques' where type_param='opac' and sstype_param='export_allow_expl' " ;
			    echo traite_rqt($rqt,"update parameters opac_export_allow_expl");
			}
			
			//JP - Param�tre g�rant l'ent�te de la fiche lecteur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='header_format' "))){
			    $rqt = "update parametres set comment_param='Champs qui seront affich�s dans l\'ent�te de la fiche emprunteur. S�parer les valeurs par des virgules. \nPour les champs personnalis�s, saisir les identifiants. Les autres valeurs possibles sont les propri�t�s de la classe PHP \"pmb/opac_css/classes/emprunteur.class.php\".' where type_param='empr' and sstype_param='header_format' " ;
			    echo traite_rqt($rqt,"update parameters empr_header_format");
			}
			
			//JP & MB - Ajout d'index sur la table cms_cache_cadres
			$req="SHOW INDEX FROM cms_cache_cadres WHERE key_name LIKE 'i_cache_cadre_create_date'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
			    $rqt = "alter table cms_cache_cadres add index i_cache_cadre_create_date(cache_cadre_create_date)";
			    echo traite_rqt($rqt,"alter table cache_cadre_create_date add index i_cache_cadre_create_date");
			}
			
			// AP & VT - Gestion des traductions
			$req="SHOW COLUMNS from translation like 'trans_small_text'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
			    // AP & VT - Modification du nom de la colonne trans_text sur la table translation
			    $rqt = "ALTER TABLE translation change trans_text trans_small_text varchar(255)";
			    echo traite_rqt($rqt,"ALTER TABLE translation rename trans_text to trans_small_text varchar(255)") ;
			    
			    // AP & VT - Modification de la table translation ajout d'une colonne trans_text
			    $rqt = "ALTER TABLE translation add trans_text text";
			    echo traite_rqt($rqt,"ALTER TABLE translation add trans_text text") ;
			}
			
			// VT & AP - Ajout d'un droit sur le statut de lecteur pour les contributions
			$rqt = "alter table empr_statut add allow_contribution int unsigned not null default 0";
			echo traite_rqt($rqt,"alter table empr_statut add allow_contribution");
			
			
			// AP & VT - Modification du nom du parametre empr_contribution en empr_contribution_area
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution' "))){
			    $rqt = "update parametres set sstype_param='empr_contribution_area' where type_param='gestion_acces' and sstype_param='empr_contribution' " ;
			    echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area' where sstype_param='empr_contribution'");
			}
			
			// AP & VT - Modification du nom du parametre empr_contribution_def en empr_contribution_area_def
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_def' "))){
			    $rqt = "update parametres set sstype_param='empr_contribution_area_def' where type_param='gestion_acces' and sstype_param='empr_contribution_def' " ;
			    echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area_def' where sstype_param='empr_contribution_def'");
			}
			
			// AP & VT - Ajout du parametre empr_contribution_scenario dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario', '0', 'Gestion des droits d\'acc�s des emprunteurs aux sc�narios de contribution\n0 : Non.\n1 : Oui.', '', 0)";
			    echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
			}
			
			// AP & VT - Ajout du parametre empr_contribution_scenario_def dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario_def' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario_def', '0', 'Valeur par d�faut en modification de scenario de contribution pour les droits d\'acc�s emprunteurs - sc�narios\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			    echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
			}
			
			// AP & VT - Ajout du parametre contribution_moderator_empr dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr', '0', 'Gestion des droits d\'acc�s des mod�rateurs sur les contributeurs\n0 : Non.\n1 : Oui.', '', 0)";
			    echo traite_rqt($rqt,"insert contribution_moderator_empr into parametres");
			}
			
			// AP & VT - Ajout du parametre contribution_moderator_empr_def dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr_def' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr_def', '0', 'Valeur par d�faut en modification d\'emprunteur pour les droits d\'acc�s mod�rateur - emprunteur\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			    echo traite_rqt($rqt,"insert contribution_moderator_empr_def into parametres");
			}
			
			// AP & VT - Suppression du statut sur les formulaires de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_forms LIKE 'form_status'"))){
			    $rqt = "ALTER TABLE contribution_area_forms drop column form_status";
			    echo traite_rqt($rqt,"ALTER TABLE contribution_area_forms drop column form_status");
			}
			
			// AP & VT - Ajout d'un statut sur les espaces de contributions
			if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_areas LIKE 'area_status'"))==0){
			    $rqt = "ALTER TABLE contribution_area_areas add column area_status int(10) unsigned not null default 1";
			    echo traite_rqt($rqt,"ALTER TABLE contribution_area_areas add column area_status");
			}
			
			// TS & VT - table des univers de recherche
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes (
						exploded_search_universes_id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						exploded_search_universes_label varchar(255) DEFAULT ''
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes");
			
			
			// TS & VT - Table associant un univers � des vues
			// exploded_search_universes_views_num_universe : Identifiant de l'univers
			// exploded_search_universes_views_num_view : Identifiant de la view
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes_views (
						exploded_search_universes_views_num_universe int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_universes_views_num_view int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_universes_views_num_universe, exploded_search_universes_views_num_view),
						INDEX i_esuv_num_view (exploded_search_universes_views_num_view)
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes_views");
			
			// TS & VT - Table associant un univers � des segments
			// exploded_search_universes_segments_num_universe : Identifiant de l'univers
			// exploded_search_universes_segments_num_segment : Identifiant du segment
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes_segments (
						exploded_search_universes_segments_num_universe int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_universes_segments_num_segment int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_universes_segments_num_universe, exploded_search_universes_segments_num_segment),
						INDEX i_esus_num_segment (exploded_search_universes_segments_num_segment)
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes_segments");
			
			// TS & VT - Segments de recherche
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_segments (
						exploded_search_segments_id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						exploded_search_segments_label varchar(255) DEFAULT '',
						exploded_search_segments_icon varchar(255) default '',
						exploded_search_segments_type varchar(255) not null default '',
						exploded_search_segments_description TEXT NOT null,
						exploded_search_segments_parameters TEXT NOT NULL
					)";
			echo traite_rqt($rqt,"create table exploded_search_segments");
			
			// TS & VT - Table associant un segment � des recherches pr�d�finies
			// exploded_search_segments_views_num_universe : Identifiant de l'univers
			// exploded_search_universes_views_num_view : Identifiant de la view
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_segments_predefined (
						exploded_search_segments_predefined_num_segment int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_segments_predefined_num_predefined int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_segments_predefined_num_segment, exploded_search_segments_predefined_num_predefined),
						INDEX i_essp_num_predefined (exploded_search_segments_predefined_num_predefined)
					)";
			echo traite_rqt($rqt,"create table exploded_search_segments_predefined");
			
			
			// TS & VT - Ajout d'une colonne dans la table search_persopac permettant de typer la recherche pr�d�finie
			if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM search_persopac LIKE 'search_type'"))){
			    $rqt = "ALTER TABLE search_persopac add column search_type varchar(255) not null default 'record'";
			    echo traite_rqt($rqt,"ALTER TABLE search_persopac add column search_type varchar(255) not null default 'record'");
			}

			// NG - Si concept actif, attribution des droits de modification des concepts CONCEPTS_AUTH,
			// � tous les utilisateurs ayant acces � THESAURUS_AUTH,
			// seulement si aucun utilisateur n'a ce droit sur les concepts
			if($thesaurus_concepts_active) {
				if (!pmb_mysql_num_rows(pmb_mysql_query("select 1 from users where rights>=4194304"))) {
					$rqt = "update users set rights=rights+4194304 where rights<4194304 and rights&2048";
					echo traite_rqt($rqt, "update users add rights CONCEPTS_AUTH");
				}
			}
			
			// NG - Ajout template pour les impressions de panier en OPAC
			$rqt="CREATE TABLE IF NOT EXISTS print_cart_tpl (
	            id_print_cart_tpl int unsigned not null auto_increment primary key,
	            print_cart_tpl_name varchar(255) not null default '',
	            print_cart_tpl_header text not null,
	            print_cart_tpl_footer text not null
       	        )";
			echo traite_rqt($rqt,"create table print_cart_tpl");
			
			// Ajout du param�tre indiquant le template � utiliser pour les impressions de panier en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_cart_header_footer' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'print_cart_header_footer', '', 'Identifiant du template � utiliser pour ins�rer un en-t�te et un pied de page en impression de panier. Les templates sont cr��s en Administration > Template de Mail > Template impression de panier.','h_cart', 0)";
			    echo traite_rqt($rqt,"insert opac_print_cart_header_footer into parametres");
			}

			// VT - Param�tre de d�finition du style dojo en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='dojo_gestion_style' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			     VALUES ( 'pmb', 'dojo_gestion_style', 'claro', 'Styles disponibles: tundra, claro, flat, nihilo, soria','', 0)";
			    echo traite_rqt($rqt,"insert pmb_dojo_gestion_style into parametres");
			}
			
			// DG - Mode d'affichage par d�faut de cr�ation d'entit�s dans les pop-up (s�lecteurs)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='popup_form_display_mode' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'popup_form_display_mode', '1', 'Mode d\'affichage par d�faut du formulaire de cr�ation dans une popup de s�lection.\n 1 : Simple \n 2 : Avanc�','',0)";
			    echo traite_rqt($rqt,"insert pmb_popup_form_display_mode into parametres");
			}
			
			// TS - VT - Afficher template m�me sans donn�e
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_display_empty_template tinyint(1) UNSIGNED NOT NULL default 1" ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_display_empty_template");
			
			// AP - VT Cr�ation d'une table pour la gestion d'une pile d'indexation
			// indexation_stack_entity_id : Identifiant de l'entit� � indexer
			// indexation_stack_entity_type : Type de l'entit� � indexer (cf init.inc.php)
			// indexation_stack_datatype : Datatype de l'indexation
			// indexation_stack_timestamp : Timestamp de la demande d'indexation
			$rqt = "CREATE TABLE IF NOT EXISTS indexation_stack (
        			indexation_stack_entity_id int(8) unsigned not null default 0,
        			indexation_stack_entity_type int(3) unsigned not null default 0,
        			indexation_stack_datatype varchar(255) not null default '',
        			indexation_stack_timestamp bigint not null default 0,
        			indexation_stack_parent_id int(8) unsigned not null default 0,
        			indexation_stack_parent_type int(3) unsigned not null default 0,
        			primary key (indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype)
        		)";
			echo traite_rqt($rqt,"create table indexation_stack");
			
			// AP - VT - Ajout d'un param�tre cach� permettant de d�finir si une indexation est en cours
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_in_progress' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'indexation_in_progress', '0', 'Param�tre cach� permettant de d�finir si une indexation est en cours', '', '1')" ;
			    echo traite_rqt($rqt,"insert hidden pmb_indexation_in_progress=0 into parametres") ;
			}
			
			// AP - VT - Ajout d'un param�tre cach� permettant de d�finir si une indexation est n�cessaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_needed' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'indexation_needed', '0', 'Param�tre cach� permettant de d�finir si une indexation est n�cessaire', '', '1')" ;
			    echo traite_rqt($rqt,"insert hidden pmb_indexation_needed=0 into parametres") ;
			}			
			
			// JP - Index incorrect sur faq_questions_words_global_index
			$rqt ="alter table faq_questions_words_global_index drop primary key";
			echo traite_rqt($rqt,"alter table faq_questions_words_global_index drop primary key");
			$rqt ="alter table faq_questions_words_global_index add primary key (id_faq_question,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table faq_questions_words_global_index add primary key");
			if ($faq_active) {
			    // Info de r�indexation
			    $rqt = " select 1 " ;
			    echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER LA FAQ / YOU MUST REINDEX THE FAQ : Admin > Outils > Nettoyage de base > R�indexer la faq</a></b> ") ;
			}
			
			// JP - choix des liens � conserver en remplacement de notice et en import
			$rqt = "ALTER TABLE users ADD deflt_notice_replace_links int(1) UNSIGNED DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_replace_links");
			
			// TS - Param�tre pour l'activation de l'autopostage dans les concepts
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'thesaurus','concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. \n 0 : Non, \n 1 : Oui', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage=0") ;
			}
			
			// TS - Param�tre pour le nombre de niveaux de recherche de l'autopostage dans les concepts g�n�riques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_generic_levels_nb' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_generic_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts g�n�riques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_generic_levels_nb=3") ;
			}
			
			// TS - Param�tre pour le nombre de niveaux de recherche de l'autopostage dans les concepts sp�cifiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_specific_levels_nb' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_specific_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts sp�cifiques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_specific_levels_nb=3") ;
			}
			
			// TS - Param�tre pour l'activation de l'autopostage dans les concepts � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='concepts_autopostage' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'opac','concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. Le nombre de niveaux vers g�n�riques et/ou sp�cifiques est d�fini par les param�tres de gestion . \n 0 : Non, \n 1 : Oui', 'c_recherche', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters opac_concepts_autopostage") ;
			}
			
			// JP & DG - Nettoyage des autorit�s
			$rqt = "DELETE FROM authorities WHERE num_object = 0";
			echo traite_rqt($rqt,"DELETE FROM authorities WITH num_object = 0");
			
			// JP & DG - Nettoyage des doublons autorit�s
			require_once($class_path."/indexation_authority.class.php");
			require_once($class_path."/authority.class.php");
			$rqt = "SELECT COUNT(*) AS nbr_doublon, num_object, type_object FROM authorities GROUP BY num_object, type_object HAVING COUNT(*) > 1";
			$result = pmb_mysql_query($rqt);
			if($result && pmb_mysql_num_rows($result)) {
			    while($row = pmb_mysql_fetch_object($result)) {
			        $query_authority = "select id_authority from authorities where num_object = ".$row->num_object." and type_object = ".$row->type_object." order by id_authority";
			        $result_authority = pmb_mysql_query($query_authority);
			        $id_authority = 0;
			        while($row_authority = pmb_mysql_fetch_object($result_authority)) {
			            if(!$id_authority) {
			                $id_authority = $row_authority->id_authority;
			            } else {
			                $query_caddie_content = "select caddie_id from authorities_caddie_content where object_id = ".$row_authority->id_authority;
			                $result_caddie_content = pmb_mysql_query($query_caddie_content);
			                while($row_caddie_content = pmb_mysql_fetch_object($result_caddie_content)) {
			                    if(pmb_mysql_result(pmb_mysql_query("select count(*) from authorities_caddie_content where object_id = ".$id_authority." and caddie_id = ".$row_caddie_content->caddie_id), 0, 0)) {
			                        $requete = "delete from authorities_caddie_content where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
			                        pmb_mysql_query($requete);
			                    } else {
			                        $requete = "update authorities_caddie_content set object_id = ".$id_authority." where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
			                        pmb_mysql_query($requete);
			                    }
			                }
			                
			                // nettoyage indexation
			                indexation_authority::delete_all_index($row_authority->id_authority, "authorities", "id_authority", $row->type_object);
			                
			                pmb_mysql_query("delete from authorities where id_authority=".$row_authority->id_authority);
			            }
			        }
			    }
			}
			
			// JP & DG - Passage de l'index en unique
			$rqt = "ALTER TABLE authorities DROP INDEX i_a_num_object_type_object,
			ADD UNIQUE KEY i_a_num_object_type_object(num_object,type_object)";
			echo traite_rqt($rqt,$rqt);
			
			// AP - Statistiques de fr�quentation en mode horaire
			$errors = '';
			if (pmb_mysql_num_rows(pmb_mysql_query('SHOW COLUMNS FROM visits_statistics LIKE "visits_statistics_value"'))) {
			    $errors.= pmb_mysql_error();
			    // Renommage de la table existante pour r�injection des donn�es
			    $rqt = 'RENAME TABLE visits_statistics TO visits_statistics_old';
			    echo traite_rqt($rqt, $rqt);
			    $errors.= pmb_mysql_error();
			    
			    // Cr�ation de la nouvelle table
			    // visits_statistics_id : ID
			    // visits_statistics_date : Date
			    // visits_statistics_location : Localisation
			    // visits_statistics_type : Type de service
			    $rqt = 'CREATE TABLE if not exists visits_statistics (
						visits_statistics_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						visits_statistics_date DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
						visits_statistics_location SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
						visits_statistics_type VARCHAR(255) NOT NULL DEFAULT "",
						INDEX i_vs_visits_statistics_date (visits_statistics_date),
						INDEX i_vs_visits_statistics_location_visits_statistics_type (visits_statistics_location, visits_statistics_type)
				        )';
			    echo traite_rqt($rqt, 'CREATE TABLE visits_statistics');
			    $errors.= pmb_mysql_error();
			    
			    // On va chercher les anciennes donn�es pour ensuite les ins�rer dans la nouvelle table
			    $rqt = 'SELECT visits_statistics_date, visits_statistics_location, visits_statistics_type, visits_statistics_value FROM visits_statistics_old ORDER BY visits_statistics_date';
			    $result = pmb_mysql_query($rqt);
			    $errors.= pmb_mysql_error();
			    if (pmb_mysql_num_rows($result)) {
			        $insert = array();
			        while ($row = pmb_mysql_fetch_assoc($result)) {
			            for ($i = 0; $i < $row['visits_statistics_value']; $i++) {
			                $insert[] = '(0, "'.$row['visits_statistics_date'].' 00:00:00", "'.$row['visits_statistics_location'].'", "'.$row['visits_statistics_type'].'")';
			            }
			        }
			        if (count($insert)) {
			            $rqt = 'INSERT INTO visits_statistics(visits_statistics_id, visits_statistics_date, visits_statistics_location, visits_statistics_type) VALUES '.implode(',', $insert);
			            echo traite_rqt($rqt, 'INSERT INTO visits_statistics') ;
			            $errors.= pmb_mysql_error();
			        }
			    }
			    
			    // Si tout va bien, on supprime l'ancienne table
			    if (!$errors) {
			        $rqt = 'DROP TABLE visits_statistics_old';
			        echo traite_rqt($rqt, $rqt.($empr_visits_statistics_active ? "<br/><b>Stat de fr�quentation modifi�es, vous devez mettre � jour vos �tats personnalisables.</b>": ""));
				} else if ($empr_visits_statistics_active) {
			        $rqt="select 1";
			        traite_rqt($rqt, "<b>Probl�me avec le traitement des modifications des tables de statistiques de fr�quentation, la nouvelle table est cr��e mais les archives n'y ont pas �t� ins�r�es, elles sont dans la table visits_statistics_old.</b>");
			    }
			}
			
			// NG - Ajout dans la table explnum: explnum_create_date, explnum_update_date, explnum_file_size
			$rqt = "ALTER TABLE explnum ADD explnum_create_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_create_date");
			$rqt = "ALTER TABLE explnum ADD explnum_update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_update_date");
			$rqt = "ALTER TABLE explnum ADD explnum_file_size int not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_file_size");
				
			//DG / VT - Ajout du droit sur le module FRBR
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'frbr' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'frbr', 'active', '0', 'Module \'FRBR\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert frbr_active=0 into parameters");
			}
			
			//DG / VT - Ajout du droit sur le module mod�lisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'modelling' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'modelling', 'active', '0', 'Module \'Mod�lisation\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert modelling_active=0 into parameters");
			}
			
			//DG - Options pour le debogage - Afficher les erreurs PHP en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='display_errors' "))==0){
			    $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui' , 'debug', '0')";
			    echo traite_rqt($rqt,"insert pmb_display_errors='0' into parametres ");
			}
			
			//DG - Options pour le debogage - Afficher les erreurs PHP en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='display_errors' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0)" ;
			    echo traite_rqt($rqt,"insert opac_display_errors=0 into parametres");
			}
			
			// DG / VT - Ajout du droit sur le module FRBR
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'frbr' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'frbr', 'active', '0', 'Module \'FRBR\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert frbr_active=0 into parameters");
			}
			
			// DG / VT - Ajout du droit sur le module mod�lisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'modelling' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'modelling', 'active', '0', 'Module \'Mod�lisation\' activ�.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert modelling_active=0 into parameters");
			}
			
			// DG - Options pour le debogage - Afficher les erreurs PHP en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='display_errors' "))==0){
			    $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui' , 'debug', '0')";
			    echo traite_rqt($rqt,"insert pmb_display_errors='0' into parametres ");
			}
			
			// DG - Options pour le debogage - Afficher les erreurs PHP en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='display_errors' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0)" ;
			    echo traite_rqt($rqt,"insert opac_display_errors=0 into parametres");
			}
			
			// PLM - Description pour recherche dans les concepts � l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_concept' "))){
			    $rqt = "UPDATE parametres
					SET comment_param='Recherche dans les concepts : \n 0 : interdite, \n 1 : autoris�e, \n 2 : autoris�e et valid�e par d�faut, \n -1 : �galement interdite en recherche multi-crit�res'
					WHERE type_param='opac' AND sstype_param='modules_search_concept'";
			    echo traite_rqt($rqt, "update parametres opac_modules_search_concept set comment_param = ... ");
			}
			
			// NG - Cr�ation de la table des status des abonnements de p�riodiques
			//   abts_status_id : identifiant du statut
			//   abts_status_gestion_libelle : libell� du statut en gestion
			//   abts_status_opac_libelle : libell� du statut en OPAC
			//   abts_status_class_html : classe HTML du statut
			//   abts_status_bulletinage_active : abonnement actif ou non dans le bulletinage
			$rqt="create table if not exists abts_status(
				abts_status_id int unsigned not null auto_increment primary key,
				abts_status_gestion_libelle varchar(255) not null default '',
				abts_status_opac_libelle varchar(255) not null default '',
				abts_status_class_html varchar(255) not null default '',
				abts_status_bulletinage_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
			     )";
			echo traite_rqt($rqt, "create table abts_status");
			
			// NG - Statut par d�faut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from abts_status where abts_status_id ='1' "))==0) {
			    $rqt = 'INSERT INTO abts_status (abts_status_id, abts_status_gestion_libelle, abts_status_opac_libelle, abts_status_class_html)
						VALUES (1, "Statut par d�faut", "Statut par d�faut", "statutnot1")';
			    echo traite_rqt($rqt,"insert default abts_status");
			}
			
			// NG - Ajout du statut dans les abonnements
			$rqt = "ALTER TABLE abts_abts ADD abt_status int(1) UNSIGNED NOT NULL DEFAULT 1 ";
			echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_status ");
			
			//AP & DG - Mise � jour des liens perdus entre les notices de bulletin et les p�riodiques
			require_once($class_path."/notice_relations.class.php");
			$query = "SELECT bulletins.num_notice, bulletins.bulletin_notice from bulletins left join notices_relations ON notices_relations.num_notice = bulletins.num_notice AND notices_relations.linked_notice = bulletins.bulletin_notice where bulletins.num_notice<>0 AND id_notices_relations IS NULL";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_fetch_object($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					notice_relations::insert($row->num_notice, $row->bulletin_notice, 'b', 1, 'up', false);
				}
				echo traite_rqt("SELECT 1","ALTER TABLE notices_relations UPDATE relations ");
			}

			// JP - Param�tre pour d�finir l'�tat par d�faut de la case � cocher "Abonnement actif" dans le navigateur de p�riodiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_default_active_subscription_filter' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES ( 'opac', 'perio_a2z_default_active_subscription_filter', '0', 'Filtre sur les abonnements actifs coch� par d�faut dans le navigateur de p�riodiques ?\n 0 : Non\n 1 : Oui', 'c_recherche', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters opac_perio_a2z_default_active_subscription_filter") ;
			}
			
			//JP - Liste des imprimantes ticket de pr�t
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_list' "))==0){
			    $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES ('pmb', 'printer_list', '', 'Liste des imprimantes de ticket de pr�t g�r�es par raspberry, s�par�es par un point-virgule. Indiquer un identifiant, un libell� et une IP de raspberry (facultative) alternative � celle du param�tre g�n�ral printer_name.\nExemple : 1_Imprimante pr�t;2_Autre imprimante(192.168.0.83:3000).','', 0)";
			    echo traite_rqt($rqt,"insert pmb_printer_list into parametres");
			}
			$rqt = "ALTER TABLE users ADD deflt_printer int(3) UNSIGNED DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_printer");
			
			// DG - Gestion des classements pour le catalogage FRBR
			$rqt="create table if not exists frbr_cataloging_categories(
				id_cataloging_category int unsigned not null auto_increment primary key,
				cataloging_category_title varchar(255) not null default '',
				cataloging_category_num_parent int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table frbr_cataloging_categories");
				
			// DG - Gestion des jeux de donn�es
			$rqt="create table if not exists frbr_cataloging_datanodes(
				id_cataloging_datanode int unsigned not null auto_increment primary key,
				cataloging_datanode_title varchar(255) not null default '',
				cataloging_datanode_comment TEXT not null,
				cataloging_datanode_owner int unsigned not null default 0,
				cataloging_datanode_allowed_users varchar(255) not null default '',
				cataloging_datanode_num_category int unsigned not null default 0,
				index i_cataloging_datanode_title(cataloging_datanode_title)
				)";
			echo traite_rqt($rqt, "create table frbr_cataloging_datanodes");
				
			// DG - Gestion de la liste des �l�ments s�lectionn�es pour le catalogage FRBR
			$rqt="create table if not exists frbr_cataloging_items (
    			num_cataloging_item int UNSIGNED NOT NULL,
    			type_cataloging_item varchar(255) not null default '',
    			cataloging_item_num_user int UNSIGNED NOT NULL default 0,
    			cataloging_item_added_date datetime,
    			cataloging_item_num_datanode int unsigned not null default 0,
    			index i_cataloging_item_num_datanode(cataloging_item_num_datanode),
    			primary key (num_cataloging_item, type_cataloging_item, cataloging_item_num_datanode))";
			echo traite_rqt($rqt, "create table if not exists frbr_cataloging_items");
				
			// DG - Gestion des campagnes de mails
			$rqt="create table if not exists campaigns (
    			id_campaign int UNSIGNED NOT NULL auto_increment primary key,
    			campaign_type varchar(255) not null default '',
    			campaign_label varchar(255) not null default '',
    			campaign_date datetime,
    			campaign_num_user int UNSIGNED NOT NULL default 0)
    			";
			echo traite_rqt($rqt, "create table if not exists campaigns");
				
			// DG - Gestion des descripteurs de campagnes de mails
			$rqt="create table if not exists campaigns_descriptors (
				num_campaign int unsigned not null default 0,
				num_noeud int unsigned not null default 0,
				campaign_descriptor_order int not null default 0,
				primary key (num_campaign, num_noeud)
				)";
			echo traite_rqt($rqt, "create table campaign_descriptors");
				
			// DG - Gestion des tags de campagnes de mails
			$rqt="create table if not exists campaigns_tags (
				num_campaign int unsigned not null default 0,
				num_tag int unsigned not null default 0,
				campaign_tag_order int not null default 0,
				primary key (num_campaign, num_tag)
				)";
			echo traite_rqt($rqt, "create table campaigns_tags");
				
			// DG - Gestion des destinataires li�s aux campagnes de mails
			$rqt = "create table if not exists campaigns_recipients(
				id_campaign_recipient int unsigned not null auto_increment primary key,
				campaign_recipient_hash varchar(255) not null default '',
				campaign_recipient_num_campaign int not null default 0,
				campaign_recipient_num_empr int not null default 0,
				campaign_recipient_empr_cp varchar(5) not null default '',
				campaign_recipient_empr_ville varchar(255) not null default '',
				campaign_recipient_empr_prof varchar(255) not null default '',
				campaign_recipient_empr_year int not null default 0,
				campaign_recipient_empr_categ smallint(5) unsigned default 0,
				campaign_recipient_empr_codestat smallint(5) unsigned default 0,
				campaign_recipient_empr_sexe tinyint(3) unsigned default 0,
				campaign_recipient_empr_statut bigint(20) unsigned default 0,
				campaign_recipient_empr_location int(6) unsigned default 0,
				index i_campaign_recipient_num_campaign(campaign_recipient_num_campaign)
			)";
			echo traite_rqt($rqt,"create table campaigns_recipients");
				
			// AP & DG - Gestion des logs li�s aux campagnes de mails
			$rqt = "create table if not exists campaigns_logs(
				campaign_log_num_campaign int not null default 0,
				campaign_log_num_recipient int not null default 0,
				campaign_log_hash varchar(255) not null default '',
				campaign_log_url varchar(255) not null default '',
				campaign_log_date datetime not null default '0000-00-00 00:00:00',
				index i_campaign_log_num_campaign(campaign_log_num_campaign),
				index i_campaign_log_num_recipient(campaign_log_num_recipient)
			)";
			echo traite_rqt($rqt,"create table campaigns_logs");
				
			// AP & DG - Consolidation des logs li�s aux campagnes de mails
			$rqt = "create table if not exists campaigns_stats(
				campaign_stat_num_campaign int not null default 0 primary key,
				campaign_stat_data text not null,
				campaign_stat_date datetime not null default '0000-00-00 00:00:00'
			)";
			echo traite_rqt($rqt,"create table campaigns_stats");
			
			// DG - Ajout dans les bannettes la possibilit� d'�tablir un suivi
			$rqt = "ALTER TABLE bannettes ADD associated_campaign INT( 1 ) UNSIGNED NOT NULL default 0 ";
			echo traite_rqt($rqt,"alter table bannettes add associated_campaign");
			
			// Ajout champ perso date floue
			$rqt = "create table if not exists notices_custom_dates (
				notices_custom_champ int(10) unsigned NOT NULL default 0,
				notices_custom_origine int(10) unsigned NOT NULL default 0,
				notices_custom_date_type int(11) default NULL,
				notices_custom_date_start date default NULL,
				notices_custom_date_end date default NULL,
				notices_custom_order int(11) unsigned NOT NULL default 0,
				KEY notices_custom_champ (notices_custom_champ),
				KEY notices_custom_origine (notices_custom_origine),
	    		primary key (notices_custom_champ, notices_custom_origine, notices_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists notices_custom_dates");
			
			$rqt = "create table if not exists author_custom_dates (
				author_custom_champ int(10) unsigned NOT NULL default 0,
				author_custom_origine int(10) unsigned NOT NULL default 0,
				author_custom_date_type int(11) default NULL,
				author_custom_date_start date default NULL,
				author_custom_date_end date default NULL,
				author_custom_order int(11) unsigned NOT NULL default 0,
				KEY author_custom_champ (author_custom_champ),
				KEY author_custom_origine (author_custom_origine),
	    		primary key (author_custom_champ, author_custom_origine, author_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists author_custom_dates");
			
			$rqt = "create table if not exists authperso_custom_dates (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_origine int(10) unsigned NOT NULL default 0,
				authperso_custom_date_type int(11) default NULL,
				authperso_custom_date_start date default NULL,
				authperso_custom_date_end date default NULL,
				authperso_custom_order int(11) unsigned NOT NULL default 0,
				KEY authperso_custom_champ (authperso_custom_champ),
				KEY authperso_custom_origine (authperso_custom_origine),
	    		primary key (authperso_custom_champ, authperso_custom_origine, authperso_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists authperso_custom_dates");
			
			$rqt = "create table if not exists categ_custom_dates (
				categ_custom_champ int(10) unsigned NOT NULL default 0,
				categ_custom_origine int(10) unsigned NOT NULL default 0,
				categ_custom_date_type int(11) default NULL,
				categ_custom_date_start date default NULL,
				categ_custom_date_end date default NULL,
				categ_custom_order int(11) unsigned NOT NULL default 0,
				KEY categ_custom_champ (categ_custom_champ),
				KEY categ_custom_origine (categ_custom_origine),
	    		primary key (categ_custom_champ, categ_custom_origine, categ_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists categ_custom_dates");
			
			$rqt = "create table if not exists cms_editorial_custom_dates (
				cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
				cms_editorial_custom_origine int(10) unsigned NOT NULL default 0,
				cms_editorial_custom_date_type int(11) default NULL,
				cms_editorial_custom_date_start date default NULL,
				cms_editorial_custom_date_end date default NULL,
				cms_editorial_custom_order int(11) unsigned NOT NULL default 0,
				KEY cms_editorial_custom_champ (cms_editorial_custom_champ),
				KEY cms_editorial_custom_origine (cms_editorial_custom_origine),
	    		primary key (cms_editorial_custom_champ, cms_editorial_custom_origine, cms_editorial_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_dates");
			
			$rqt = "create table if not exists collection_custom_dates (
				collection_custom_champ int(10) unsigned NOT NULL default 0,
				collection_custom_origine int(10) unsigned NOT NULL default 0,
				collection_custom_date_type int(11) default NULL,
				collection_custom_date_start date default NULL,
				collection_custom_date_end date default NULL,
				collection_custom_order int(11) unsigned NOT NULL default 0,
				KEY collection_custom_champ (collection_custom_champ),
				KEY collection_custom_origine (collection_custom_origine),
	    		primary key (collection_custom_champ, collection_custom_origine, collection_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists collection_custom_dates");
			
			$rqt = "create table if not exists collstate_custom_dates (
				collstate_custom_champ int(10) unsigned NOT NULL default 0,
				collstate_custom_origine int(10) unsigned NOT NULL default 0,
				collstate_custom_date_type int(11) default NULL,
				collstate_custom_date_start date default NULL,
				collstate_custom_date_end date default NULL,
				collstate_custom_order int(11) unsigned NOT NULL default 0,
				KEY collstate_custom_champ (collstate_custom_champ),
				KEY collstate_custom_origine (collstate_custom_origine),
	    		primary key (collstate_custom_champ, collstate_custom_origine, collstate_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists collstate_custom_dates");
			
			$rqt = "create table if not exists demandes_custom_dates (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_origine int(10) unsigned NOT NULL default 0,
				demandes_custom_date_type int(11) default NULL,
				demandes_custom_date_start date default NULL,
				demandes_custom_date_end date default NULL,
				demandes_custom_order int(11) unsigned NOT NULL default 0,
				KEY demandes_custom_champ (demandes_custom_champ),
				KEY demandes_custom_origine (demandes_custom_origine),
	    		primary key (demandes_custom_champ, demandes_custom_origine, demandes_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists demandes_custom_dates");
			
			$rqt = "create table if not exists empr_custom_dates (
				empr_custom_champ int(10) unsigned NOT NULL default 0,
				empr_custom_origine int(10) unsigned NOT NULL default 0,
				empr_custom_date_type int(11) default NULL,
				empr_custom_date_start date default NULL,
				empr_custom_date_end date default NULL,
				empr_custom_order int(11) unsigned NOT NULL default 0,
				KEY empr_custom_champ (empr_custom_champ),
				KEY empr_custom_origine (empr_custom_origine),
	    		primary key (empr_custom_champ, empr_custom_origine, empr_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists empr_custom_dates");
			
			$rqt = "create table if not exists explnum_custom_dates (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_origine int(10) unsigned NOT NULL default 0,
				explnum_custom_date_type int(11) default NULL,
				explnum_custom_date_start date default NULL,
				explnum_custom_date_end date default NULL,
				explnum_custom_order int(11) unsigned NOT NULL default 0,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY explnum_custom_origine (explnum_custom_origine),
	    		primary key (explnum_custom_champ, explnum_custom_origine, explnum_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists explnum_custom_dates");
			
			$rqt = "create table if not exists expl_custom_dates (
				expl_custom_champ int(10) unsigned NOT NULL default 0,
				expl_custom_origine int(10) unsigned NOT NULL default 0,
				expl_custom_date_type int(11) default NULL,
				expl_custom_date_start date default NULL,
				expl_custom_date_end date default NULL,
				expl_custom_order int(11) unsigned NOT NULL default 0,
				KEY expl_custom_champ (expl_custom_champ),
				KEY expl_custom_origine (expl_custom_origine),
	    		primary key (expl_custom_champ, expl_custom_origine, expl_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists expl_custom_dates");
			
			$rqt = "create table if not exists indexint_custom_dates (
				indexint_custom_champ int(10) unsigned NOT NULL default 0,
				indexint_custom_origine int(10) unsigned NOT NULL default 0,
				indexint_custom_date_type int(11) default NULL,
				indexint_custom_date_start date default NULL,
				indexint_custom_date_end date default NULL,
				indexint_custom_order int(11) unsigned NOT NULL default 0,
				KEY indexint_custom_champ (indexint_custom_champ),
				KEY indexint_custom_origine (indexint_custom_origine),
	    		primary key (indexint_custom_champ, indexint_custom_origine, indexint_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists indexint_custom_dates");
			
			$rqt = "create table if not exists pret_custom_dates (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_origine int(10) unsigned NOT NULL default 0,
				pret_custom_date_type int(11) default NULL,
				pret_custom_date_start date default NULL,
				pret_custom_date_end date default NULL,
				pret_custom_order int(11) unsigned NOT NULL default 0,
				KEY pret_custom_champ (pret_custom_champ),
				KEY pret_custom_origine (pret_custom_origine),
	    		primary key (pret_custom_champ, pret_custom_origine, pret_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists pret_custom_dates");
			
			$rqt = "create table if not exists publisher_custom_dates (
				publisher_custom_champ int(10) unsigned NOT NULL default 0,
				publisher_custom_origine int(10) unsigned NOT NULL default 0,
				publisher_custom_date_type int(11) default NULL,
				publisher_custom_date_start date default NULL,
				publisher_custom_date_end date default NULL,
				publisher_custom_order int(11) unsigned NOT NULL default 0,
				KEY publisher_custom_champ (publisher_custom_champ),
				KEY publisher_custom_origine (publisher_custom_origine),
	    		primary key (publisher_custom_champ, publisher_custom_origine, publisher_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists publisher_custom_dates");
			
			$rqt = "create table if not exists serie_custom_dates (
				serie_custom_champ int(10) unsigned NOT NULL default 0,
				serie_custom_origine int(10) unsigned NOT NULL default 0,
				serie_custom_date_type int(11) default NULL,
				serie_custom_date_start date default NULL,
				serie_custom_date_end date default NULL,
				serie_custom_order int(11) unsigned NOT NULL default 0,
				KEY serie_custom_champ (serie_custom_champ),
				KEY serie_custom_origine (serie_custom_origine),
	    		primary key (serie_custom_champ, serie_custom_origine, serie_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists serie_custom_dates");
			
			$rqt = "create table if not exists skos_custom_dates (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_origine int(10) unsigned NOT NULL default 0,
				skos_custom_date_type int(11) default NULL,
				skos_custom_date_start date default NULL,
				skos_custom_date_end date default NULL,
				skos_custom_order int(11) unsigned NOT NULL default 0,
				KEY skos_custom_champ (skos_custom_champ),
				KEY skos_custom_origine (skos_custom_origine),
	    		primary key (skos_custom_champ, skos_custom_origine, skos_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists skos_custom_dates");
			
			$rqt = "create table if not exists subcollection_custom_dates (
				subcollection_custom_champ int(10) unsigned NOT NULL default 0,
				subcollection_custom_origine int(10) unsigned NOT NULL default 0,
				subcollection_custom_date_type int(11) default NULL,
				subcollection_custom_date_start date default NULL,
				subcollection_custom_date_end date default NULL,
				subcollection_custom_order int(11) unsigned NOT NULL default 0,
				KEY subcollection_custom_champ (subcollection_custom_champ),
				KEY subcollection_custom_origine (subcollection_custom_origine),
	    		primary key (subcollection_custom_champ, subcollection_custom_origine, subcollection_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists subcollection_custom_dates");
			
			$rqt = "create table if not exists tu_custom_dates (
				tu_custom_champ int(10) unsigned NOT NULL default 0,
				tu_custom_origine int(10) unsigned NOT NULL default 0,
				tu_custom_date_type int(11) default NULL,
				tu_custom_date_start date default NULL,
				tu_custom_date_end date default NULL,
				tu_custom_order int(11) unsigned NOT NULL default 0,
				KEY tu_custom_champ (tu_custom_champ),
				KEY tu_custom_origine (tu_custom_origine),
	    		primary key (tu_custom_champ, tu_custom_origine, tu_custom_order)) ";
			echo traite_rqt($rqt,"create table if not exists tu_custom_dates");
			
			// DG - Table de d�finition/personnalisation des listes par utilisateur
			$rqt = "CREATE TABLE IF NOT EXISTS lists (
					id_list int unsigned not null auto_increment primary key,
        			list_num_user int(8) unsigned not null default 0,
					list_objects_type varchar(255) not null default '',
					list_label varchar(255) not null default '',
        			list_selected_columns text,
					list_filters text,
					list_applied_group text,
					list_applied_sort text,
					list_pager text,
					list_autorisations mediumtext,
					list_default_selected int(1) unsigned not null default 0,
					list_order int not null default 0
        		)";
			echo traite_rqt($rqt,"create table lists");
			
			// DG - File d'attente de mails
			$rqt = "CREATE TABLE IF NOT EXISTS mails_waiting (
					id_mail int unsigned not null auto_increment primary key,
					mail_waiting_to_name varchar(255) not null default '',
					mail_waiting_to_mail varchar(255) not null default '',
					mail_waiting_object varchar(255) not null default '',
					mail_waiting_content mediumtext not null,
					mail_waiting_from_name varchar(255) not null default '',
					mail_waiting_from_mail varchar(255) not null default '',
					mail_waiting_headers text not null,
					mail_waiting_copy_cc varchar(255) not null default '',
        			mail_waiting_copy_bcc varchar(255) not null default '',
					mail_waiting_do_nl2br int(1) unsigned not null default 0,
					mail_waiting_attachments text not null,
					mail_waiting_reply_name varchar(255) not null default '',
					mail_waiting_reply_mail varchar(255) not null default '',
					mail_waiting_date datetime not null default '0000-00-00 00:00:00'
        		)";
			echo traite_rqt($rqt,"create table mails_waiting");
			
			// AP - Ajout d'un index sur la signature des documents num�riques
			$rqt = "alter table explnum add index i_e_explnum_signature(explnum_signature)";
			echo traite_rqt($rqt,"alter table explnum add index i_e_explnum_signature");
			
			//DG - Ajout du classement associ� aux champs personalis�s
			$rqt = "ALTER TABLE notices_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE author_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE authperso_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE categ_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE cms_editorial_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE collection_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE collstate_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE demandes_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE empr_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE expl_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE explnum_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE explnum_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE gestfic0_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE indexint_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE pret_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE pret_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE publisher_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE serie_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE skos_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE skos_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE subcollection_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE tu_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom ADD custom_classement ");
			
			// DG - Ajout index sur resp_groupe de la table groupe
			$req="SHOW INDEX FROM groupe WHERE key_name LIKE 'i_resp_groupe'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table groupe add index i_resp_groupe (resp_groupe)";
				echo traite_rqt($rqt,"alter table groupe add index i_resp_groupe");
			}
			
			// DG - Ajout index sur num_empr de la table opac_liste_lecture
			$req="SHOW INDEX FROM opac_liste_lecture WHERE key_name LIKE 'i_num_empr'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table opac_liste_lecture add index i_num_empr (num_empr)";
				echo traite_rqt($rqt,"alter table opac_liste_lecture add index i_num_empr");
			}
			
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			echo form_relance ("v5.30");
			break;
			
		case "v5.30":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    // TS & VT & NG - Suppression des anciennes tables des recherches s�gment�es
		    $query = "drop table if exists exploded_search_universes";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes");
		    $query = "drop table if exists exploded_search_universes_views";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes_views");
		    $query = "drop table if exists exploded_search_universes_segments";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes_segments");
		    $query = "drop table if exists exploded_search_segments";
		    echo traite_rqt($query, "drop table if exists exploded_search_segments");
		    $query = "drop table if exists exploded_search_segments_predefined";
		    echo traite_rqt($query, "drop table if exists exploded_search_segments_predefined");
		    
		    // TS & VT & NG - Modification du param�tre OPAC des recherches s�gment�es
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exploded_search_activate' "))==1){
		        $query = "update parametres set sstype_param = 'search_universes_activate' where sstype_param='exploded_search_activate'";
		        echo traite_rqt($query, "update parametres set sstype_param = 'search_universes_activate' where sstype_param='exploded_search_activate'");
		    } elseif (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_universes_activate' ")) == 0) {
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            	VALUES (NULL, 'opac', 'search_universes_activate', '0', 'Univers de recherche activ�s : \r\n0: Non\r\n1: Oui', 'c_recherche', '0')";
		        echo traite_rqt($rqt,"insert opac_search_universes_activate=0 into parametres ");
		    }
		    
		    
		    // TS & VT & NG - table des univers de recherche
		    $rqt = "CREATE TABLE IF NOT EXISTS search_universes (
        			id_search_universe int unsigned not null auto_increment primary key,
        			search_universe_label varchar(255) not null default '',
			        search_universe_description varchar(255) not null default '',
			        search_universe_template_directory varchar(255) not null default '',
			        search_universe_opac_views varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table search_universes");
		    
		    // TS & VT & NG - table des segments de recherche
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments (
        			id_search_segment int unsigned not null auto_increment primary key,
        			search_segment_label varchar(255) not null default '',
    			    search_segment_description varchar(255) not null default '',
    			    search_segment_template_directory varchar(255) not null default '',
    			    search_segment_num_universe int not null default 0,
    			    search_segment_type int not null default 0,
    			    search_segment_order int not null default 0,
    			    search_segment_set text,
    			    search_segment_logo varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table search_segments");
		    
		    // TS & VT & NG - table de liaison entre les segments et les pr�d�finies
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments_search_perso (
        			num_search_segment int unsigned not null default 0,
			        num_search_perso int unsigned not null default 0,
			        search_segment_search_perso_opac int unsigned not null default 0,
			        search_segment_search_perso_order int not null default 0,
			        primary key (num_search_segment, num_search_perso)
        		)";
		    echo traite_rqt($rqt,"create table search_segments_search_perso");
		    
		    // TS & VT & NG - table de liaison entre les segments et les facettes
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments_facets (
        			num_search_segment int not null default 0,
			        num_facet int not null default 0 ,
			        search_segment_facet_order int not null default 0,
        			primary key (num_search_segment, num_facet)
        		)";
		    echo traite_rqt($rqt,"create table search_segments_facets");
		    
		    // DG - Type sur les facettes
		    $rqt = "alter table facettes add facette_type varchar(255) not null default 'notices' after id_facette ";
		    echo traite_rqt($rqt,"alter table facettes add facette_type");
		    
		    // NG - Changement des champs date en INT pour la gestion des dates avant JC
		    $rqt = " ALTER TABLE author_custom_dates CHANGE author_custom_date_start author_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table author_custom_dates change author_custom_date_start");
		    $rqt = " ALTER TABLE author_custom_dates CHANGE author_custom_date_end author_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table author_custom_dates change author_custom_date_end");
		    
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_start authperso_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_start");
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_end authperso_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_end");
		    
		    $rqt = " ALTER TABLE categ_custom_dates CHANGE categ_custom_date_start categ_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table categ_custom_dates change categ_custom_date_start");
		    $rqt = " ALTER TABLE categ_custom_dates CHANGE categ_custom_date_end categ_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table categ_custom_dates change categ_custom_date_end");
		    
		    $rqt = " ALTER TABLE cms_editorial_custom_dates CHANGE cms_editorial_custom_date_start cms_editorial_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table cms_editorial_custom_dates change cms_editorial_custom_date_start");
		    $rqt = " ALTER TABLE cms_editorial_custom_dates CHANGE cms_editorial_custom_date_end cms_editorial_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table cms_editorial_custom_dates change cms_editorial_custom_date_end");
		    
		    $rqt = " ALTER TABLE collection_custom_dates CHANGE collection_custom_date_start collection_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collection_custom_dates change collection_custom_date_start");
		    $rqt = " ALTER TABLE collection_custom_dates CHANGE collection_custom_date_end collection_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collection_custom_dates change collection_custom_date_end");
		    
		    $rqt = " ALTER TABLE collstate_custom_dates CHANGE collstate_custom_date_start collstate_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collstate_custom_dates change collstate_custom_date_start");
		    $rqt = " ALTER TABLE collstate_custom_dates CHANGE collstate_custom_date_end collstate_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collstate_custom_dates change collstate_custom_date_end");
		    
		    $rqt = " ALTER TABLE demandes_custom_dates CHANGE demandes_custom_date_start demandes_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table demandes_custom_dates change demandes_custom_date_start");
		    $rqt = " ALTER TABLE demandes_custom_dates CHANGE demandes_custom_date_end demandes_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table demandes_custom_dates change demandes_custom_date_end");
		    
		    $rqt = " ALTER TABLE empr_custom_dates CHANGE empr_custom_date_start empr_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table empr_custom_dates change empr_custom_date_start");
		    $rqt = " ALTER TABLE empr_custom_dates CHANGE empr_custom_date_end empr_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table empr_custom_dates change empr_custom_date_end");
		    
		    $rqt = " ALTER TABLE explnum_custom_dates CHANGE explnum_custom_date_start explnum_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table explnum_custom_dates change explnum_custom_date_start");
		    $rqt = " ALTER TABLE explnum_custom_dates CHANGE explnum_custom_date_end explnum_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table explnum_custom_dates change explnum_custom_date_end");
		    
		    $rqt = " ALTER TABLE expl_custom_dates CHANGE expl_custom_date_start expl_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table expl_custom_dates change expl_custom_date_start");
		    $rqt = " ALTER TABLE expl_custom_dates CHANGE expl_custom_date_end expl_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table expl_custom_dates change expl_custom_date_end");
		    
		    $rqt = " ALTER TABLE indexint_custom_dates CHANGE indexint_custom_date_start indexint_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table indexint_custom_dates change indexint_custom_date_start");
		    $rqt = " ALTER TABLE indexint_custom_dates CHANGE indexint_custom_date_end indexint_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table indexint_custom_dates change indexint_custom_date_end");
		    
		    $rqt = " ALTER TABLE notices_custom_dates CHANGE notices_custom_date_start notices_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table notices_custom_dates change notices_custom_date_start");
		    $rqt = " ALTER TABLE notices_custom_dates CHANGE notices_custom_date_end notices_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table notices_custom_dates change notices_custom_date_end");
		    
		    $rqt = " ALTER TABLE pret_custom_dates CHANGE pret_custom_date_start pret_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table pret_custom_dates change pret_custom_date_start");
		    $rqt = " ALTER TABLE pret_custom_dates CHANGE pret_custom_date_end pret_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table pret_custom_dates change pret_custom_date_end");
		    
		    $rqt = " ALTER TABLE publisher_custom_dates CHANGE publisher_custom_date_start publisher_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table publisher_custom_dates change publisher_custom_date_start");
		    $rqt = " ALTER TABLE publisher_custom_dates CHANGE publisher_custom_date_end publisher_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table publisher_custom_dates change publisher_custom_date_end");
		    
		    $rqt = " ALTER TABLE serie_custom_dates CHANGE serie_custom_date_start serie_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table serie_custom_dates change serie_custom_date_start");
		    $rqt = " ALTER TABLE serie_custom_dates CHANGE serie_custom_date_end serie_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table serie_custom_dates change serie_custom_date_end");
		    
		    $rqt = " ALTER TABLE skos_custom_dates CHANGE skos_custom_date_start skos_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table skos_custom_dates change skos_custom_date_start");
		    $rqt = " ALTER TABLE skos_custom_dates CHANGE skos_custom_date_end skos_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table skos_custom_dates change skos_custom_date_end");
		    
		    $rqt = " ALTER TABLE subcollection_custom_dates CHANGE subcollection_custom_date_start subcollection_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table subcollection_custom_dates change subcollection_custom_date_start");
		    $rqt = " ALTER TABLE subcollection_custom_dates CHANGE subcollection_custom_date_end subcollection_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table subcollection_custom_dates change subcollection_custom_date_end");
		    
		    $rqt = " ALTER TABLE tu_custom_dates CHANGE tu_custom_date_start tu_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table tu_custom_dates change tu_custom_date_start");
		    $rqt = " ALTER TABLE tu_custom_dates CHANGE tu_custom_date_end tu_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table tu_custom_dates change tu_custom_date_end");
		    
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_start authperso_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_start");
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_end authperso_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_end");
		    
		    // DG - Type sur les facettes externes
		    $rqt = "alter table facettes_external add facette_type varchar(255) not null default 'notices' after id_facette ";
		    echo traite_rqt($rqt,"alter table facettes_external add facette_type");
		    
		    // DG - Ajout d'index sur les tables de circulation de p�riodiques
		    $indexes = array(
		        'serialcirc' => array('num_serialcirc_abt'),
		        'serialcirc_ask' => array('num_serialcirc_ask_perio', 'num_serialcirc_ask_serialcirc', 'num_serialcirc_ask_empr', 'serialcirc_ask_type', 'serialcirc_ask_statut'),
		        'serialcirc_circ' => array('num_serialcirc_circ_diff', 'num_serialcirc_circ_expl', 'num_serialcirc_circ_empr', 'num_serialcirc_circ_serialcirc'),
		        'serialcirc_copy' => array('num_serialcirc_copy_empr', 'num_serialcirc_copy_bulletin'),
		        'serialcirc_diff' => array('num_serialcirc_diff_serialcirc', 'serialcirc_diff_empr_type', 'serialcirc_diff_type_diff', 'num_serialcirc_diff_empr'),
		        'serialcirc_expl' => array('num_serialcirc_expl_id', 'num_serialcirc_expl_serialcirc', 'num_serialcirc_expl_serialcirc_diff', 'num_serialcirc_expl_current_empr'),
		        'serialcirc_group' => array('num_serialcirc_group_diff', 'num_serialcirc_group_empr')
		    );
		    foreach ($indexes as $table_name => $fields) {
		        foreach($fields as $field_name) {
		            $req="SHOW INDEX FROM ".$table_name." WHERE key_name LIKE 'i_".$field_name."'";
		            $res=pmb_mysql_query($req);
		            if($res && (pmb_mysql_num_rows($res) == 0)){
		                $rqt = "ALTER TABLE ".$table_name." ADD INDEX i_".$field_name."(".$field_name.")";
		                echo traite_rqt($rqt,"ALTER TABLE ".$table_name." ADD INDEX i_".$field_name);
		            }
		        }
		    }
		    
		    //DG - MAJ du template de bannettes par d�faut (identifiant 1) - N'allons pas alt�rer ceux d�j� personnalis�s
		    $rqt = "UPDATE bannette_tpl SET bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n    <ul>\r\n        {% for sommaire in sommaires %}\r\n            {% if sommaire.level==1 %}\r\n                <li>\r\n                    <a href=\"#[{{loop.counter}}]\">{{sommaire.title}}</a>\r\n                </li>\r\n            {% endif %}\r\n        {% endfor %}
			    		\r\n    </ul>\r\n</div>\r\n{% for sommaire in sommaires %}\r\n    {% if sommaire.level==1 %}\r\n        <h2 class=\"dsi_rang_1\"><a name=\"[{{loop.counter}}]\"></a>{{sommaire.title}}</h2>\r\n    {% endif %}\r\n    {% if sommaire.level==2 %}\r\n        <h3 class=\"dsi_rang_2\">{{sommaire.title}}</h3>\r\n    {% endif %}\r\n    {% if sommaire.level==3 %}\r\n        <h4 class=\"dsi_rang_3\">{{sommaire.title}}</h4>
			    		\r\n    {% endif %}\r\n    {% for record in sommaire.records %}\r\n        {{record.render}}\r\n    {% endfor %}
			    		\r\n    <br />\r\n{% endfor %}\r\n{{info.footer}}'
					WHERE bannettetpl_id=1 AND bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n{% for sommaire in sommaires %}\r\n<a href=\"#[{{sommaire.level}}]\">\r\n{{sommaire.level}} - {{sommaire.title}}\r\n</a>\r\n<br />\r\n{% endfor %}\r\n</div>\r\n<hr>\r\n{% for sommaire in sommaires %}\r\n<a name=\"[{{sommaire.level}}]\" />\r\n<h1>{{sommaire.level}} - {{sommaire.title}}</h1>\r\n{% for record in sommaire.records %}\r\n{{record.render}}\r\n<hr>\r\n{% endfor %}\r\n<br />\r\n{% endfor %}\r\n{{info.footer}}'";
		    echo traite_rqt($rqt,"ALTER minimum into bannette_tpl");
		  
		    // VT, TS, NG - Lignes de commande PNB
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_orders (
		    		id_pnb_order int unsigned not null auto_increment primary key,
        			pnb_order_id_order varchar(255) not null default '',
        			pnb_order_line_id varchar(255) not null default '',
			        pnb_order_num_notice int unsigned not null default 0,
			        pnb_order_loan_max_duration int unsigned not null default 0,
			        pnb_order_nb_loans int unsigned not null default 0,
			        pnb_order_nb_simultaneous_loans int unsigned not null default 0,
			        pnb_order_nb_consult_in_situ int unsigned not null default 0,
			        pnb_order_nb_consult_ex_situ int unsigned not null default 0,
			        pnb_order_offer_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			        pnb_order_offer_date_end datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			        pnb_order_offer_duration int unsigned not null default 0
        		)";
		    echo traite_rqt($rqt,"create table pnb_orders");
		    
		    // VT, TS, NG - Liaisons entre les lignes de commande PNB et les exemplaires
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_orders_expl (
        			pnb_order_num int unsigned not null default 0,
			        pnb_order_expl_num int unsigned not null default 0,
        			primary key (pnb_order_num, pnb_order_expl_num)
        		)";
		    echo traite_rqt($rqt,"create table pnb_orders_expl");
		    
		    // NG - Param�tres de connection au PNB
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_login' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_login', '', 'Param�trage du Login de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_login into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_password', '', 'Param�trage du mot de passe de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_login' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_login', '', 'Param�trage du login du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_login into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_password', '', 'Param�trage du mot de passe du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_server' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_server', '', 'Param�trage de l\'url du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_server into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ws_user_name' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ws_user_name', '', 'Param�trage du nom de l\'utilisateur externe.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ws_user_name into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ws_user_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ws_user_password', '', 'Param�trage du mot de passe de l\'utilisateur externe.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ws_user_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_dilicom_url' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_dilicom_url', '', 'Param�trage de l\'url du webservice Dilicom.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_dilicom_url into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pnb_param_webservice_url' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'pnb_param_webservice_url', '', 'Param�trage de l\'url du webservice de gestion pour les pr�ts num�riques.', '', 1)" ;
		        echo traite_rqt($rqt,"insert opac_pnb_param_webservice_url into parametres");
		    }
		    
		    // AP & VT - Table de liaison des emprunteurs � un ou plusieurs p�riph�rique(s) de lecture
		    $rqt = "CREATE TABLE IF NOT EXISTS empr_devices (
        			empr_num int unsigned not null default 0,
			        device_id int unsigned not null default 0,
        			primary key (empr_num, device_id)
        		)";
		    echo traite_rqt($rqt,"create table empr_devices");

            // +-------------------------------------------------+
            echo "</table>";
            $rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
            $res = pmb_mysql_query($rqt, $dbh) ;
            echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
            echo form_relance ("v5.31");
            break;

		case "v5.31":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    //Attention op�ration susceptible d'�tre longue, peut �tre pr�voir une sous version de l'alter v5
		    // AP & VT - Ajout d'un flag dans la table notice permettant de dire si la notice est num�rique
		    $rqt = "alter table notices add is_numeric TINYINT(1) not null default 0 ";
		    echo traite_rqt($rqt,"alter table notices add is_numeric");
		    
		    //Attention op�ration susceptible d'�tre longue, peut �tre pr�voir une sous version de l'alter v5
		    // CC & VT - Ajout d'une colonne contenant le mot de passe du PNB dans la table emprunteur
		    $rqt = "alter table empr add empr_pnb_password varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add pnb_password");
		    
		    //Attention op�ration susceptible d'�tre longue, peut �tre pr�voir une sous version de l'alter v5
		    // CC & VT - Ajout d'une colonne contenant l'indice du mot de passe du PNB dans la table emprunteur
		    $rqt = "alter table empr add empr_pnb_password_hint varchar(100) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add pnb_password_hint");
		    
		    // NG - Quotas du PNB
		    $rqt = "create table if not exists quotas_pnb (
				quota_type int(10) unsigned not null default 0,
				constraint_type varchar(255) not null default '',
				elements int(10) unsigned not null default 0,
				value text not null,
				primary key(quota_type,constraint_type,elements)
			)";
		    echo traite_rqt($rqt,"create table quotas_pnb");
		    
		    
		    // CC - VT Ajout d'un parametre cach� contenant le compteur de pr�t num�rique
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_loan_counter' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','pnb_loan_counter','0','Param�tre cach� contenant le compteur de pr�t num�rique','',1)";
		        echo traite_rqt($rqt,"insert pmb_pnb_loan_counter='0' into parametres");
		    }
		    
		    // CC - VT Table contenant les pr�ts numeriques
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_loans (
		    		id_pnb_loan int unsigned not null auto_increment primary key,
        			pnb_loan_order_line_id varchar(255) not null default '',
			        pnb_loan_link varchar(255) not null default '',
			        pnb_loan_request_id varchar(255) not null default '',
			        pnb_loan_num_expl int unsigned not null default 0,
		    		pnb_loan_num_loaner int unsigned not null default 0
        		)";
		    echo traite_rqt($rqt,"create table pnb_loans");
		    
		    // CC - TS - Parametre pour filtrer les bannettes privees avec une equation de recherche
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_search_equation' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'dsi', 'private_bannette_search_equation', '0', 'Id de l\'�quation de recherche utilis�e par d�faut en compl�ment de l\'�quation priv�e en diffusion de bannettes priv�es.', '', 0)";
		        echo traite_rqt($rqt, "insert private_bannette_search_equation into parameters");
		    }
		    
		    // AP | VT - Ajout d'un param�tre pour l'activation de l'�dition des documents num�riques en popup
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='enable_explnum_edition_popup' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'enable_explnum_edition_popup', '0', 'Activation de l\'�dition des documents num�riques en popup ?\n0 : Non\n1 : Oui', '', 0)";
		        echo traite_rqt($rqt, "insert enable_explnum_edition_popup into parameters");
		    }
		    
		    // NG - Ajout d'un param�tre cach� pour m�moriser les informations des DRM du pnb
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_drm_parameters' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_drm_parameters', '0', 'M�morise les informations des DRM du PNB', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_drm_parameters into parameters");
		    }
		    
		    // VT - Ajout de l'information du DRM utilis� dans la table des pr�ts num�riques
		    $rqt = "alter table pnb_loans add pnb_loan_drm varchar(100) not null default '' ";
		    echo traite_rqt($rqt,"alter table pnb_loans add pnb_loan_drm");
		    
		    // NG - Ajout d'un param�tre cach� pour d�clencher l'alerte des commandes arrivant � expiration
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_alert_end_offers' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_alert_end_offers', '0', 'Nombre de jours entre le d�clanchement de l\'alerte et l\'expiration des commandes', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_alert_end_offers into parameters");
		    }
		    
		    // NG - Ajout d'un param�tre cach� pour d�clencher l'alerte des commandes arrivant � saturation de pr�ts
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_alert_staturation_offers' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_alert_staturation_offers', '0', 'Nombre d\'exemplaires restants avant le d�clanchement de l\'alerte pour les commandes arrivant � saturation de pr�t', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_alert_staturation_offers into parameters");
		    }
		    
		    // NG - Ajout flag de pret pnb dans la table pret_archive
		    $rqt = "ALTER TABLE pret_archive ADD arc_pnb_flag INT(1) NOT NULL DEFAULT 0 ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_pnb_flag");
		    
		    // NG - Ajout d'un param�tre cach� pour suprimer les pr�ts pnb arriv�s � expiration, une seule fois par jour
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_clean_loans_date' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_clean_loans_date', date(now()), 'Date du nettoyage des pr�ts PNB expir�s', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_clean_loans_date into parameters");
		    }
		    
		    // DG - Dur�e maximale de la session sans rafra�chissement (en secondes).
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='session_reactivate' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'session_reactivate', '', 'Dur�e maximale de la session sans rafra�chissement (en secondes). Si vide ou 0, valeur fix�e � 120 minutes', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_session_reactivate into parametres");
		    }
		    
		    // DG - Dur�e maximale de la session (en secondes).
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='session_maxtime' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'session_maxtime', '', 'Dur�e maximale de la session (en secondes). Si vide ou 0, valeur fix�e � 24 heures', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_session_maxtime into parametres");
		    }
		    
		    // DG - Tri sur les documents num�riques en Gestion
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='explnum_order' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id','Ordre d\'affichage des documents num�riques, dans l\'ordre donn�, s�par� par des virgules : explnum_mimetype, explnum_nom, explnum_id','')";
		        echo traite_rqt($rqt,"insert pmb_explnum_order=explnum_mimetype, explnum_nom, explnum_id into parametres");
		    }
		    
		    // DG - Cr�ation automatique d'une r�servation lors de la r�ception d'une ligne de commande li�e � une suggestion d'emprunteur
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='sugg_to_cde_resa_auto' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','sugg_to_cde_resa_auto','1','Cr�ation automatique d\'une r�servation lors de la r�ception d\'une ligne de commande li�e � une suggestion d\'emprunteur.\nLa r�servation sur les notices sans exemplaires (Param�tres g�n�raux > resa_records_no_expl) doit �tre activ�e pour cela.\n Non : 0 \n Oui : 1','',0)" ;
		        echo traite_rqt($rqt,"insert acquisition_sugg_to_cde_resa_auto into parametres") ;
		    }
		    
		    // DG - Ajout d'un index sur l'identifiant de template d'une bannette
		    $rqt = "alter table bannettes add index i_bannette_tpl_num(bannette_tpl_num)";
		    echo traite_rqt($rqt,"alter table bannettes add index i_bannette_tpl_num");
		    
		    // DG - Statut de notice par d�faut en cr�ation d'article
		    $rqt = "ALTER TABLE users ADD deflt_notice_statut_analysis INT(6) UNSIGNED DEFAULT 0 AFTER deflt_notice_statut";
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_statut_analysis");
		    
		    // DG - Modification de la taille du champ name de la table etagere
		    $rqt = "ALTER TABLE etagere MODIFY name varchar(255) NOT NULL default ''" ;
		    echo traite_rqt($rqt,"ALTER TABLE etagere MODIFY name to varchar(255)");
		    
		    // DG - Modification du champ section_publication_state de la table cms_sections
		    $rqt = "ALTER TABLE cms_sections MODIFY section_publication_state INT UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE cms_sections MODIFY section_publication_state to INTEGER");
		    
		    // DG - Modification du champ article_publication_state de la table cms_articles
		    $rqt = "ALTER TABLE cms_articles MODIFY article_publication_state INT UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE cms_articles MODIFY article_publication_state to INTEGER");
		    
		    // DG - Utilisation d'un type de produit pour les commandes
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='type_produit' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','type_produit','0','Utilisation d\'un type de produit pour les commandes.\n 0:optionnel\n 1:obligatoire','',0)" ;
		        echo traite_rqt($rqt,"insert acquisition_type_produit into parametres") ;
		    }
		    
		    // DG - Pr�f�rence utilisateur : Type de produit par d�faut
		    $rqt = "ALTER TABLE users ADD deflt3type_produit INT(8) UNSIGNED DEFAULT 0 AFTER deflt3rubrique";
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt3type_produit");
		    
		    // TS - Parametre pour la date des notices � utiliser pour calculer les nouveaut�s des bannettes
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='private_bannette_date_used_to_calc' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'opac', 'private_bannette_date_used_to_calc', '0', 'Date des notices � utiliser en diffusion de bannettes priv�es ? 0 : date de cr�ation, 1 : date de modification, 2 : S�lectionnable par l\'usager en OPAC', 'l_dsi', 0)";
		        echo traite_rqt($rqt, "insert opac_private_bannette_date_used_to_calc into parameters");
		    }
		    
		    // TS - Modification du nom du parametre dsi_private_bannette_date_used_to_calc en opac_private_bannette_date_used_to_calc
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'"))){
		        $rqt = "UPDATE parametres SET type_param = 'opac', section_param= 'l_dsi', comment_param='Date des notices � utiliser en diffusion de bannettes priv�es ? 0 : date de cr�ation, 1 : date de modification, 2 : S�lectionnable par l\'usager en OPAC' WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'" ;
		        echo traite_rqt($rqt,"UPDATE parametres SET type_param = 'opac' WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'");
		    }
		    
		    // AR - Cr�ation d'un param�tre pour la m�thode de calcul de la pertinence avec Sphinx
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='pert_calc_method' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'sphinx','pert_calc_method','(sum(lcs*user_weight)+top(exact_order*user_weight/min_hit_pos)+top(3*exact_hit*user_weight))*1000+bm25','M�thode de calcul de la pertinence pour Sphinx en mode expression (par d�faut dans PMB). \n La liste des facteurs disponibles : http://sphinxsearch.com/docs/current.html#expression-ranker \n Exemples des autre modes : \n SPH_RANK_PROXIMITY_BM25 = sum(lcs*user_weight)*1000+bm25 \n SPH_RANK_BM25 = bm25 \n SPH_RANK_WORDCOUNT = sum(hit_count*user_weight) \n SPH_RANK_PROXIMITY = sum(lcs*user_weight) \n SPH_RANK_MATCHANY = sum((word_count+(lcs-1)*max_lcs)*user_weight) \n SPH_RANK_FIELDMASK = field_mask \n SPH_RANK_SPH04 = sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25','',0)" ;
		        echo traite_rqt($rqt,"insert sphinx_pert_calc_method into parametres") ;
		    }
		    
		    // TS - Ajout de l'identifiant du segment � afficher par d�faut
		    $rqt = "ALTER TABLE search_universes ADD search_universe_default_segment INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"ALTER TABLE search_universes ADD search_universe_default_segment");
		    
		    // PLM - Modification description param�tre avis_show_writer
		    $rqt = "update parametres set comment_param = 'Afficher le r�dacteur de l\'avis \r\n 0 : non \r\n 1 : Pr�nom NOM \r\n 2 : login OPAC uniquement\r\n 3 : Pr�nom uniquement'
		      where type_param = 'opac' and sstype_param = 'avis_show_writer'";
		    echo traite_rqt($rqt, "update parametres set comment_param where type_param = 'opac' and sstype_param = 'avis_show_writer'");	   
		     
		    // DG - Modification du param�tre opac_websubscribe_num_carte_auto
		    $rqt = "update parametres set valeur_param = '1' where valeur_param = '' and type_param='opac' and sstype_param = 'websubscribe_num_carte_auto'";
		    echo traite_rqt($rqt,"update parametres opac_websubscribe_num_carte_auto set value");
		    $rqt = "update parametres set comment_param = 'Num�ro de carte de lecteur automatique ?\n 1: www + Identifiant du lecteur \n 2,a,b,c: a=longueur du pr�fixe, b=nombre de chiffres de la partie num�rique, c=pr�fixe fix� (facultatif)\n 3,fonction: fonction de g�n�ration sp�cifique dans fichier nomm� de la m�me fa�on, � placer dans pmb/opac_css/circ/empr' where type_param='opac' and sstype_param = 'websubscribe_num_carte_auto'";
		    echo traite_rqt($rqt,"update parametres opac_websubscribe_num_carte_auto set comment");
		    
		    // AP - Support par d�faut en cr�ation d'exemplaire de p�riodique
		    $rqt = "ALTER TABLE users ADD deflt_serials_docs_type INT( 6 ) UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_docs_type" ;
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_serials_docs_type");
		    
		    // VT - Table des entit�s verrouill�es
		    $rqt = "CREATE TABLE IF NOT EXISTS locked_entities (
		    		id_entity int unsigned not null,
        			type int unsigned not null default 0,
			        date datetime not null default '0000-00-00 00:00:00',
			        parent_id int unsigned not null default 0,
			        parent_type int unsigned not null default 0,
		    		user_id int unsigned not null default 0,
                    empr_id int unsigned not null default 0,
                    primary key(id_entity, type)
        		)";
		    echo traite_rqt($rqt,"create table locked_entities");
		    
		    // CC / VT - D�finition du temps de verrouillage d'une entit�
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_locked_time' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'entity_locked_time', '0', 'Temps de verrouillage des entit�s en minutes. \n 0: aucun verrouillage, \n ## : dur�e de blocage de l\'entit� apr�s enregistrement ou abandon de la modification. Conseill� 5. Permet d\'�viter les entit�s rest�es verrouill�es.', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_entity_locked_time into parametres");
		    }
		    
		    // VT - Ajout d'un parametre syst�me contenant le temps de rafraichissement de la date de dernier acc�s � une entit� (en minute)
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_locked_refresh_time' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','entity_locked_refresh_time','5','Param�tre syst�me contenant le temps de rafraichissement de la date de dernier acc�s � une entit� (en minute)', '', 0)";
		        echo traite_rqt($rqt,"insert pmb_entity_locked_refresh_time='1' into parametres");
		    }
		    
		    // DG - Afficher les exemplaires du bulletin sous l'article
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_exemplaires_analysis' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'pmb', 'show_exemplaires_analysis', '0', 'Afficher les exemplaires du bulletin sous l\'article affich� ? \n 0: Non \n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_show_exemplaires_analysis=0 into parametres");
		    }
		    
		    // DG - Ajout d'un champ  pour les statuts de documents num�riques pour outrepasser les droits � l'affichage de la vignette, si coch� la vignette reste accessible m�me si les droits du doc la verrouille.
		    $rqt = "ALTER TABLE explnum_statut ADD explnum_thumbnail_visible_opac_override tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ";
		    echo traite_rqt($rqt,"alter table explnum add explnum_thumbnail_visible_opac_override ");
		    
		    // DG - Param�tre d'activation/d�sactivation de connexion auto en DSI
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='connexion_auto' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'dsi', 'connexion_auto', '1', 'Connexion automatique de l\'usager � l\'OPAC � partir d\'un mail de la DSI activ�e ? \n 0: Non \n 1: Oui','')";
		        echo traite_rqt($rqt,"insert dsi_connexion_auto=1 into parametres");
		    }

		    // NG - chat, pr�f�rence utilisateur
		    $rqt = "ALTER TABLE users ADD param_chat_activate INT(1) NOT NULL default 0 AFTER param_rfid_activate" ;
		    echo traite_rqt($rqt,"ALTER TABLE users ADD param_chat_activate");
		    
		    // NG - chat, m�morisation des discussions
		    $rqt = "create table if not exists chat_messages (
	    		id_chat_message int unsigned not null auto_increment primary key,
				chat_message_from_user_type int unsigned not null default 0,
				chat_message_from_user_num int unsigned not null default 0,
				chat_message_to_user_type int unsigned not null default 0,
				chat_message_to_user_num int unsigned not null default 0,
				chat_message_text text not null,
				chat_message_file blob,
				chat_message_read int unsigned not null default 0,
				chat_message_date datetime DEFAULT CURRENT_TIMESTAMP,
				INDEX i_from_user_num (chat_message_from_user_num, chat_message_from_user_type)
			)";
		    echo traite_rqt($rqt,"create table chat_messages");
		    
		    // NG - chat, m�morisation des groupes de discussion
		    $rqt = "create table if not exists chat_groups (
	    		id_chat_group int unsigned not null auto_increment primary key,
				chat_group_name varchar(255) not null default '',
				chat_group_author_user_type int unsigned not null default 0,
				chat_group_author_user_num int unsigned not null default 0
			)";
		    echo traite_rqt($rqt,"create table chat_groups");
		    
		    // NG - chat, m�morisation des inscrits aux groupes
		    $rqt = "create table if not exists chat_users_groups (
	    		chat_user_group_num int unsigned not null default 0,
				chat_user_group_user_type int unsigned not null default 0,
				chat_user_group_user_num int unsigned not null default 0,
                chat_user_group_unread_messages_number int unsigned not null default 0,
			    primary key (chat_user_group_num, chat_user_group_user_type, chat_user_group_user_num)
			)";
		    echo traite_rqt($rqt,"create table chat_users_groups");

		    // +-------------------------------------------------+
		    echo "</table>";
		    $rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		    $res = pmb_mysql_query($rqt, $dbh) ;
		    echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		    echo form_relance ("v5.32");
		    break;
		    
		case "v5.32":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    // PLM & DG - DSI > Veilles : Langue d'indexation par d�faut en cr�ation de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_index_lang varchar(20) not null default '' after watch_record_default_status";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_index_lang");
		    
		    // PLM & DG - DSI > Veilles : Langue par d�faut en cr�ation de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_lang varchar(20) not null default '' after watch_record_default_index_lang";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_lang");
		    
		    // PLM & DG - DSI > Veilles : Nouveaut� par d�faut en cr�ation de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_is_new tinyint(1) unsigned not null default 0 after watch_record_default_lang";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_is_new");
		    
		    // AP / CC - Table des champs calcul�s de sc�narios
		    // id_computed_fields : Identifiant du champ calcul�
		    // computed_fields_area_num : Identifiant de l'espace de contribution
		    // computed_fields_field_num : Identifiant unique dans l'arbre DOJO du champ calcul�
		    // computed_fields_template : Template � utiliser pour renseigner le champ
		    $rqt = "CREATE TABLE IF NOT EXISTS contribution_area_computed_fields (
		    		id_computed_fields int unsigned not null auto_increment primary key,
					computed_fields_area_num int unsigned not null default 0,
					computed_fields_field_num varchar(255) not null default '',
					computed_fields_template text
        		)";
		    echo traite_rqt($rqt,"create table contribution_area_computed_fields");
		    
		    // AP / CC - Table des champs utilis�s dans les champs calcul�s de sc�narios
		    // id_computed_fields_used : Identifiant du champ � utiliser pour renseigner un champ calcul�
		    // computed_fields_used_origine_field_num: Cl� �trang�re, identifiant du champ calcul�
		    // computed_fields_used_label: Libell� du champ � utiliser pour renseigner un champ calcul�
		    // computed_fields_used_num: Identifiant unique dans l'arbre DOJO du champ � utiliser pour renseigner un champ calcul�
		    // computed_fields_used_alias: Alias du champ � utiliser pour renseigner un champ calcul�
		    $rqt = "CREATE TABLE IF NOT EXISTS contribution_area_computed_fields_used (
		    		id_computed_fields_used int unsigned not null auto_increment primary key,
					computed_fields_used_origine_field_num int unsigned not null default 0,
					computed_fields_used_label text,
					computed_fields_used_num varchar(255) not null default '',
					computed_fields_used_alias varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table contribution_area_computed_fields_used");
		    
		    // NG - Ajout de l'identifiant ISNI des auteurs
		    $rqt = "alter table authors add author_isni varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add author_isni");
		    
		    // VT - Ajout d'un param�tre permettant de renseigner une URL interne (valeur par d�faut mise � pmb_url_base)
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='url_internal' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
		          VALUES (0, 'pmb', 'url_internal', '".$pmb_url_base."', 'URL interne utilis�e quand le serveur doit s\'appeler lui m�me. Ne pas oublier le / final','')";
		        echo traite_rqt($rqt,"insert pmb_url_internal=pmb_url_base into parametres");
		    }
		    
		    // DG - Dur�e de validit� (en heures) du lien de connexion automatique
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='connexion_auto_duration' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','connexion_auto_duration','0','Dur�e de validit� (en heures) du lien de connexion automatique','l_dsi',0)" ;
		        echo traite_rqt($rqt,"insert opac_connexion_auto_duration into parametres") ;
		    }
		    
		    // DG - Ajout du champ permettant la pr�-selection du connecteur en gestion
		    $rqt = "ALTER TABLE connectors_sources ADD gestion_selected int(1) unsigned not null default 0 after opac_selected";
		    echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD gestion_selected");
		    
		    // AP - Changement des constantes utilis�es dans les vedettes compos�es
		    
		    // AP - Cr�ation du param�tre qui va permettre de faire le traitement qu'une fois
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param, gestion)
		    	 VALUES (0, 'pmb', 'vedette_objects_id_updated', '0', 'La mise � jour des constantes de vedette a-t-elle �t� faite ?\n 0: Non\n 1: En cours\n 2: Termin�e','','1')";
		        echo traite_rqt($rqt,"INSERT pmb_vedette_objects_id_updated=0 INTO parametres");
		    }
		    
		    if (pmb_mysql_result(pmb_mysql_query("SELECT valeur_param FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'"), 0, 0) == 0) {
		        // On passe le param�tre � 1
		        $rqt = "UPDATE parametres SET valeur_param = '1' WHERE type_param= 'pmb' AND sstype_param='vedette_objects_id_updated'";
		        echo traite_rqt($rqt,"UPDATE parametres SET valeur_param = '1' WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'");
		        
		        // Copie de la table vedette_object
		        $rqt = "CREATE TABLE vedette_object_copy AS SELECT * FROM vedette_object";
		        echo traite_rqt($rqt,"COPY TABLE vedette_object TO vedette_object_copy");
		        
		        // Concepts 9 => 17
		        $rqt = "UPDATE vedette_object SET object_type = '17' WHERE object_type = '9'";
		        echo traite_rqt($rqt,"(Concepts) UPDATE vedette_object SET object_type = '17' WHERE object_type = '9'");
		        
		        // Index. d�cimales 8 => 9
		        $rqt = "UPDATE vedette_object SET object_type = '9' WHERE object_type = '8'";
		        echo traite_rqt($rqt,"(Index. d�cimales) UPDATE vedette_object SET object_type = '9' WHERE object_type = '8'");
		        
		        // Titres uniformes 7 => 8
		        $rqt = "UPDATE vedette_object SET object_type = '8' WHERE object_type = '7'";
		        echo traite_rqt($rqt,"(Titres uniformes) UPDATE vedette_object SET object_type = '8' WHERE object_type = '7'");
		        
		        // S�ries 6 => 7
		        $rqt = "UPDATE vedette_object SET object_type = '7' WHERE object_type = '6'";
		        echo traite_rqt($rqt,"(S�ries) UPDATE vedette_object SET object_type = '7' WHERE object_type = '6'");
		        
		        // Sous-collections 5 => 6
		        $rqt = "UPDATE vedette_object SET object_type = '6' WHERE object_type = '5'";
		        echo traite_rqt($rqt,"(Sous-collections) UPDATE vedette_object SET object_type = '6' WHERE object_type = '5'");
		        
		        // Collections 4 => 5
		        $rqt = "UPDATE vedette_object SET object_type = '5' WHERE object_type = '4'";
		        echo traite_rqt($rqt,"(Collections) UPDATE vedette_object SET object_type = '5' WHERE object_type = '4'");
		        
		        // Editeurs 3 => 4
		        $rqt = "UPDATE vedette_object SET object_type = '4' WHERE object_type = '3'";
		        echo traite_rqt($rqt,"(Editeurs) UPDATE vedette_object SET object_type = '4' WHERE object_type = '3'");
		        
		        // Cat�gories 2 => 3
		        $rqt = "UPDATE vedette_object SET object_type = '3' WHERE object_type = '2'";
		        echo traite_rqt($rqt,"(Cat�gories) UPDATE vedette_object SET object_type = '3' WHERE object_type = '2'");
		        
		        // Auteurs 1 => 2
		        $rqt = "UPDATE vedette_object SET object_type = '2' WHERE object_type = '1'";
		        echo traite_rqt($rqt,"(Auteurs) UPDATE vedette_object SET object_type = '2' WHERE object_type = '1'");
		        
		        // Notices 10 => 1
		        $rqt = "UPDATE vedette_object SET object_type = '1' WHERE object_type = '10'";
		        echo traite_rqt($rqt,"(Notices) UPDATE vedette_object SET object_type = '1' WHERE object_type = '10'");
		        
		        // On passe le param�tre � 2
		        $rqt = "UPDATE parametres SET valeur_param = '2' WHERE type_param= 'pmb' AND sstype_param='vedette_objects_id_updated'";
		        echo traite_rqt($rqt,"UPDATE parametres SET valeur_param = '2' WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'");
		    }
		    
		    // AP - Suppression de la table copi�e
		    if (pmb_mysql_result(pmb_mysql_query("SELECT valeur_param FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'"), 0, 0) == 2) {
		        $rqt = "DROP TABLE IF EXISTS vedette_object_copy";
		        echo traite_rqt($rqt,"DROP TABLE vedette_object_copy");
		    }
		    
		    // DG - Type de donn�es de tri des r�sultats de facettes
		    $rqt = "ALTER TABLE facettes ADD facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha' after facette_order_sort";
		    echo traite_rqt($rqt,"alter table facettes add facette_datatype_sort ");
		    
		    // DG - Type de donn�es de tri des r�sultats de facettes externes
		    $rqt = "ALTER TABLE facettes_external ADD facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha' after facette_order_sort";
		    echo traite_rqt($rqt,"alter table facettes_external add facette_datatype_sort ");
		    
		    // NG - Prendre en compte les diacritiques dans le d�doublonnage des autorit�s auteurs et �diteurs
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='controle_doublons_diacrit' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'controle_doublons_diacrit', '0', 'Prendre en compte les diacritiques dans le d�doublonnage des autorit�s auteurs et �diteurs? \n 0 : Non \n 1 : Oui', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_controle_doublons_diacrit=0 into parametres");
		    }
		    
		    // NG - Param�tre d'activation de la recherche avanc�e dans les autorit�s en OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_extended_search_authorities' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES (0, 'opac', 'allow_extended_search_authorities', '0', 'Autorisation ou non de la recherche avanc�e dans les autorit�s\n 0 : Non \n 1 : Oui', 'c_recherche', '0')";
		        echo traite_rqt($rqt,"insert opac_allow_extended_search_authorities='0' into parametres");
		    }
		    
		    // DG - Grilles sur les articles et rubriques du contenu �ditorial �ditables
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='editorial_form_editables' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			     VALUES (0, 'cms', 'editorial_form_editables', '1', 'Grilles �ditables sur les articles et rubriques du contenu �ditorial ? \n 0 non \n 1 oui','',0)";
		        echo traite_rqt($rqt,"insert cms_editorial_form_editables into parametres");
		    }
		    
		    // TS - Ajout du commentaire et de la visibilit� OPAC sur l'indexation des entit�s
		    $rqt = "ALTER TABLE index_concept ADD comment text NOT NULL" ;
		    echo traite_rqt($rqt,"ALTER TABLE index_concept ADD comment ");
		    $rqt = "ALTER TABLE index_concept ADD comment_visible_opac tinyint( 1 ) UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE index_concept ADD comment_visible_opac ");
		    
		    // DG - Gestion des fichiers substituables
		    $rqt = "CREATE TABLE IF NOT EXISTS subst_files (
		    	id_subst_file int unsigned not null auto_increment primary key,
				subst_file_path varchar(255) not null default '',
				subst_file_filename varchar(255) not null default '',
				subst_file_data mediumtext not null
			)";
		    echo traite_rqt($rqt,"create table subst_files");
		    
		    // AP - Grammaires de vedette compos�e � utiliser par entit�
		    $rqt = "CREATE TABLE IF NOT EXISTS vedette_grammars_by_entity (
		    	entity_type int UNSIGNED NOT NULL default 0,
		    	grammar varchar(255) NOT NULL default '',
		    	PRIMARY KEY(entity_type, grammar)
			)";
		    echo traite_rqt($rqt,"create table vedette_grammars_by_entity");
		    
		    // NG - chat, m�morisation des discussions, recr�� car chat_message_date datetime DEFAULT CURRENT_TIMESTAMP ne passe pas avant MYSQL V5.6.5
		    $rqt = "create table if not exists chat_messages (
	    		id_chat_message int unsigned not null auto_increment primary key,
				chat_message_from_user_type int unsigned not null default 0,
				chat_message_from_user_num int unsigned not null default 0,
				chat_message_to_user_type int unsigned not null default 0,
				chat_message_to_user_num int unsigned not null default 0,
				chat_message_text text not null,
				chat_message_file blob,
				chat_message_read int unsigned not null default 0,
				chat_message_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				INDEX i_from_user_num (chat_message_from_user_num, chat_message_from_user_type)
			)";
		    echo traite_rqt($rqt,"create table chat_messages");
		    
		    // AP - Ajout d'une colonne dans vedette_object pour stocker le num�ro correspondant au champ disponible associ�
		    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM vedette_object LIKE 'num_available_field'"))==0) {
		        $rqt = "ALTER TABLE vedette_object ADD num_available_field int(11) NOT NULL default 0";
		        echo traite_rqt($rqt, "ALTER TABLE vedette_object ADD num_available_field");
		        
		        // AP - On le pr�remplit avec la valeur d'object_type
		        $rqt = "UPDATE vedette_object SET num_available_field = object_type";
		        echo traite_rqt($rqt, "UPDATE vedette_object SET num_available_field = object_type");
		    }
		    
		    // DG - Param�tre d'activation de la localisation d'une demande de num�risation � l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_location_activate' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
		    	VALUES ('opac','scan_request_location_activate','0','Activer la localisation d\'une demande de num�risation','f_modules',0)";
		        echo traite_rqt($rqt,"insert opac_scan_request_location_activate into parametres");
		    }
		    
		    // NG - M�morisation des modes de paiement
		    $rqt = "CREATE TABLE IF NOT EXISTS transaction_payment_methods (
    			transaction_payment_method_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			transaction_payment_method_name varchar(255) not null default ''
			)";
		    echo traite_rqt($rqt,"CREATE TABLE transaction_payment_methods");
		    
		    // NG - M�morisation du mode de paiement d'une transaction
		    $rqt = "ALTER TABLE transactions ADD transaction_payment_method_num int NOT NULL default 0";
		    echo traite_rqt($rqt,"alter table transactions add transaction_payment_method_num ");
		    
		    // DG - Ajout d'un param�tre utilisateur permettant d'activer par d�faut le bulletinage en OPAC en cr�ation de p�riodique
		    $rqt = "alter table users add deflt_opac_visible_bulletinage int not null default 1";
		    echo traite_rqt($rqt,"alter table users add deflt_opac_visible_bulletinage");
		    
		    
		    // NG - Export des emprises cartographiques des notices
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_map' "))==0){
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_map', '0', 'Exporter les emprises cartographiques des notices', '', '1')";
		        echo traite_rqt($rqt,"insert exportparam_export_map='0' into parametres ");
		    }
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_map' "))==0){
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_map', '0', 'Exporter les emprises cartographiques des notices', '', '1')";
		        echo traite_rqt($rqt,"insert opac_exp_export_map='0' into parametres ");
		    }
		    
		    // NG - Ajout d'un commentaire dans la table contribution_area_forms
		    if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_forms LIKE 'form_comment'"))){
		        $rqt = "alter table contribution_area_forms add form_comment text not null";
		        echo traite_rqt($rqt,"alter table contribution_area_forms add form_comment");
		    }
		    
		    // DG - Ajout de la personnalisation des filtres par utilisateur
		    $rqt = "ALTER TABLE lists ADD list_selected_filters text AFTER list_pager" ;
		    echo traite_rqt($rqt,"ALTER TABLE lists ADD list_selected_filters");
		    
		    // NG - Ajout du mode de pr�t (borne_rfid, bibloto, pret_opac, gestion_rfid, gestion_standard) dans la table pret_archive:
		    $rqt = "ALTER TABLE pret_archive ADD arc_pret_source_device varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_pret_source_device");
		    
		    // NG - Ajout du mode de retour de pr�t (borne_rfid, bibloto, pret_opac, gestion_rfid, gestion_standard) dans la table pret_archive:
		    $rqt = "ALTER TABLE pret_archive ADD arc_retour_source_device varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_retour_source_device");
		    
		    // DG - Ajout dans les bannettes la possibilit� de d�finir un exp�diteur
		    $rqt = "ALTER TABLE bannettes ADD bannette_num_sender INT( 5 ) UNSIGNED NOT NULL default 0 ";
		    echo traite_rqt($rqt,"alter table bannettes add bannette_num_sender");
		    
		    // DG - Paniers - Visible pour tous ?
		    $rqt = "ALTER TABLE caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE caddie add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE empr_caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE authorities_caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie add autorisations_all AFTER autorisations");
		    
		    // DG - Proc�dures - Visible pour tous ?
		    $rqt = "ALTER TABLE procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE caddie_procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE empr_caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie_procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE authorities_caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie_procs add autorisations_all AFTER autorisations");
		    
		    // DG - Paniers - Couleur associ�e au panier
		    $rqt = "ALTER TABLE caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE caddie add favorite_color");
		    $rqt = "ALTER TABLE empr_caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie add favorite_color");
		    $rqt = "ALTER TABLE authorities_caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie add favorite_color");
		    
		    // DG - maj Colonnes exemplaires affich�es en gestion - ajout du nombre de pr�ts
		    $rqt = "update parametres set comment_param='Colonnes des exemplaires, dans l\'ordre donn�, s�par� par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle,groupexpl_name,nb_prets #n : id des champs personnalis�s \r\n expl_cb est obligatoire et sera ajout� si absent' where type_param= 'pmb' and sstype_param='expl_data' ";
		    echo traite_rqt($rqt,"update pmb_expl_data into parametres");
		    
		    // AP & BT - Ajout d'une table pour la gestion des champs � afficher dans le formulaire de r�abonnement
		    // empr_renewal_form_field_code : Code du champ
		    // empr_renewal_form_field_display : Afficher le champ ? 0 ou 1
		    // empr_renewal_form_field_mandatory : Le champ est-il obligatoire ? 0 ou 1
		    // empr_renewal_form_field_alterable : Le champ est-il modifiable ? 0 ou 1
		    // empr_renewal_form_field_explanation : Texte explicatif
		    $rqt="CREATE TABLE IF NOT EXISTS empr_renewal_form_fields (
				empr_renewal_form_field_code VARCHAR(255) NOT NULL PRIMARY KEY,
				empr_renewal_form_field_display TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
				empr_renewal_form_field_mandatory TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				empr_renewal_form_field_alterable TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
				empr_renewal_form_field_explanation VARCHAR(255) NOT NULL DEFAULT ''
			)";
		    echo traite_rqt($rqt, "CREATE TABLE empr_renewal_form_fields");
		     
		    // DG - maj du commentaire sur la consultation et l'ajout d'un avis
		    $rqt = "update parametres set comment_param='Permet de consulter/ajouter un avis pour les notices \n 0 : non \n 1 : sans �tre identifi� : consultation possible, ajout impossible \n 2 : identification obligatoire pour consulter et ajouter \n 3 : consultation et ajout anonymes possibles' where type_param= 'opac' and sstype_param='avis_allow' ";
		    echo traite_rqt($rqt,"update opac_avis_allow into parametres");
		    
		    // DG - Param�tre pour utiliser la localisation des plans de classement
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'thesaurus' and sstype_param='classement_location' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'thesaurus', 'classement_location', '0', '0', 'Utiliser la gestion des plans de classement localis�s?\n 0: Non\n 1: Oui', 'classement') ";
		        echo traite_rqt($rqt,"INSERT thesaurus_classement_location INTO parametres") ;
		    }
		    
		    // DG - Localisations visibles par plan de classement
		    $rqt = "ALTER TABLE pclassement ADD locations varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table pclassement add locations ");
		    
		    // DG - Param�tre pour l'affichage des notices dans les flux RSS partag�s
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='short_url_rss_records_format' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'short_url_rss_records_format', '1', '0', 'Format d\'affichage des notices du flux RSS de recherche\n 1: D�faut\n H 1 = id d\'un template de notice', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_short_url_rss_records_format INTO parametres") ;
		    }
		    
		    // AP & CC - Ajout d'un param�tre pour activer le r�abonnement � l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'empr' and sstype_param='active_opac_renewal' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'empr', 'active_opac_renewal', '0', '0', 'Activer la prolongation d\'abonnement � l\'OPAC', '') ";
		        echo traite_rqt($rqt,"INSERT empr_active_opac_renewal INTO parametres") ;
		    }
		    
		    
		    // TS - Ajout d'un param�tre pour activer la suppression du compte � l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'empr' and sstype_param='opac_account_deleted_status' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'empr', 'opac_account_deleted_status', '0', '0', '0 : Bouton de suppression du compte lecteur en OPAC invisible\nn : Identifiant du statut pour la suppression du compte lecteur + bouton visible', '') ";
		        echo traite_rqt($rqt,"INSERT empr_opac_account_deleted_status INTO parametres") ;
		    }
		    
		    // AP - Sch�mas de concepts � utiliser par d�faut en cr�ation de vedettes compos�es depuis une entit�
		    $rqt = "CREATE TABLE IF NOT EXISTS vedette_schemes_by_entity (
		    	entity_type INT UNSIGNED NOT NULL DEFAULT 0,
		    	scheme INT NOT NULL DEFAULT 0,
		    	PRIMARY KEY(entity_type, scheme)
			)";
		    echo traite_rqt($rqt,"create table vedette_schemes_by_entity");
		    
		    
		    // NG & BT - Table de liaison entre les listes de lecture et les notices
		    $rqt = "CREATE TABLE IF NOT EXISTS opac_liste_lecture_notices (
		    	opac_liste_lecture_num INT UNSIGNED NOT NULL DEFAULT 0,
		    	opac_liste_lecture_notice_num INT UNSIGNED NOT NULL DEFAULT 0,
		    	opac_liste_lecture_create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    	PRIMARY KEY(opac_liste_lecture_num, opac_liste_lecture_notice_num)
			)";
		    echo traite_rqt($rqt,"create table opac_liste_lecture_notices");
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM opac_liste_lecture LIKE 'notices_associees'"))) {
		        $query = "select id_liste, notices_associees from opac_liste_lecture";
		        $result = pmb_mysql_query($query);
		        if (pmb_mysql_num_rows($result)) {
		            while ($row = pmb_mysql_fetch_object($result)) {
		                if ($row->notices_associees) {
		                    $notices_associees = explode(',', $row->notices_associees);
		                    foreach ($notices_associees as $num_notice) {
		                        $query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=" . $row->id_liste . ", opac_liste_lecture_notice_num=" . $num_notice;
		                        pmb_mysql_query($query);
		                    }
		                }
		            }
		        }
		        $query = "ALTER TABLE opac_liste_lecture DROP notices_associees";
		        pmb_mysql_query($query);
		    }
		    
		    // AP - Param�tre de tri par d�faut des notices dans les listes de lecture
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_reading' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_reading', 'd_text_35', '0', 'Tri par d�faut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour d�croissant, puis num ou text pour num�rique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_default_sort_reading INTO parametres") ;
		    }
		    
		    // AP - Param�tre de d�finition du s�lecteur de tri des notices dans les listes de lecture
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_reading_list' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_reading_list', '1 d_text_14|Trier par date', '0', 'Listes de lecture :\nAfficher la liste d�roulante de s�lection d\'un tri ?\n 0 : Non\n 1 : Oui\nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour d�croissant\nnum ou text pour num�rique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibell� du tri (optionnel)', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_default_sort_reading_list INTO parametres") ;
		    }
		    
		    // DG - [R�gression] Correction sur la date de cr�ation de l'exemplaire
		    $query = "select expl_id from exemplaires where create_date = '0000-00-00 00:00:00'";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)) {
		        while($row = pmb_mysql_fetch_object($result)) {
		            $rqt = "select quand from audit where type_modif = 1 and type_obj = 2 and object_id =".$row->expl_id;
		            $res = pmb_mysql_query($rqt);
		            if(pmb_mysql_num_rows($res) == 1) {
		                pmb_mysql_query("update exemplaires set create_date='".pmb_mysql_result($res, 0, 'quand')."' where expl_id = '".$row->expl_id."'");
		            }
		        }
		    }
		    
		    // BT & NG - Ajout des champs dates dans la table aut_link
		    $rqt = "ALTER TABLE aut_link ADD aut_link_string_start_date varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_string_start_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_string_end_date varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_string_end_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_start_date DATE NOT NULL default '0000-00-00'";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_start_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_end_date DATE NOT NULL default '0000-00-00'";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_end_date ");
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0){
		        $info_message = "<font color=\"#FF0000\">ATTENTION ! Il est n�cessaire de reg�n�rer les liens entre autorit�s, en nettoyage de base !</font>";
		        echo "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($info_message) : $info_message)."</font></td><td></td></tr>";
		    }
		    
		    // NG - Param�tre pour ne pas envoyer la DSI aux lecteurs dont la date de fin d'adh�sion est d�pass�e
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'dsi' and sstype_param='send_empr_date_expiration' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'dsi', 'send_empr_date_expiration', '1', '0', 'Envoyer la D.S.I aux lecteurs dont la date de fin d\'adh�sion est d�pass�e ?\n 0: Non\n 1: Oui', '') ";
		        echo traite_rqt($rqt,"INSERT dsi_send_empr_date_expiration INTO parametres") ;
		    }
		    
		    // NG - Param�tre pour activer l'autocompl�tion dans les liens entre autorit�s
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='aut_link_autocompletion' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'pmb', 'aut_link_autocompletion', '0', '0', 'Activer l\'autocompl�tion dans les liens entre autorit�s ?\n 0: Non\n 1: Oui', '') ";
		        echo traite_rqt($rqt,"INSERT pmb_aut_link_autocompletion INTO parametres") ;
		    }
		    
		    // DB - Ajout d'une cl� primaire sur la table sessions
		    $rqt = "alter table sessions add primary key(SESSID)";
		    echo traite_rqt($rqt,"alter table sessions add primary key");
		    // DB - modification de la taille du champ login dans la table sessions
		    $rqt = "ALTER TABLE sessions CHANGE login login VARCHAR(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table sessions increase login size to 255");
		    
		    // AR - Param�tre de pr�fixe des index sphinx
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='indexes_prefix' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'indexes_prefix', '', 'Prefixe pour le nommage des index sphinx','')";
		        echo traite_rqt($rqt,"insert sphinx_indexes_prefix = '' into parametres ");
		    }
		    
		    // DG - Attribut BNF Z3950 - ISMN
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from z_attr where attr_bib_id= '3' and attr_libelle='ismn' "))==0){
		        $rqt = "INSERT INTO z_attr (attr_bib_id, attr_libelle, attr_attr) VALUES ('3','ismn','9')";
		        echo traite_rqt($rqt,"insert ismn for BNF into z_attr");
		    }
		    
		    // DG - Attribut BNF Z3950 - EAN
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from z_attr where attr_bib_id= '3' and attr_libelle='ean' "))==0){
		        $rqt = "INSERT INTO z_attr (attr_bib_id, attr_libelle, attr_attr) VALUES ('3','ean','1214')";
		        echo traite_rqt($rqt,"insert ean for BNF into z_attr");
		    }

		    // +-------------------------------------------------+
		    echo "</table>";
		    $rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		    $res = pmb_mysql_query($rqt, $dbh) ;
		    echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
		    echo form_relance ("v5.33");
		    break;
		    
		case "v5.33":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    //DG - Ajout du champ pour inclure ou non le flux RSS dans les m�tadonn�es de l'OPAC
		    // Case � cocher pour le rendre accessible ou non par un agr�gateur de flux RSS
		    $rqt = "ALTER TABLE rss_flux ADD metadata_rss_flux INT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER descr_rss_flux";
		    echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD metadata_rss_flux INT(1) UNSIGNED NOT NULL DEFAULT 1 ");
		    
		    // NG - Param�tre pour activer le formulaire du changement de profil � l'OPAC. Si des donn�es sont pr�sentes dans empr_renewal_form_fields, le formulaire est actif par d�faut      
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='empr' and sstype_param='renewal_activate' "))==0){
		        $activate = 0;
		        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT * FROM empr_renewal_form_fields LIMIT 1"))) {
		            $activate = 1;
		        }
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'empr', 'renewal_activate', '" . $activate . "', '1', 'Activer le formulaire changement de profil � l\'OPAC\n 0: Non \n 1: Oui') ";
		        echo traite_rqt($rqt,"INSERT empr_renewal_activate INTO parametres") ;
		    }

		    // DG - Param�tre d'activation des demandes de location
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='rent_requests_activate' "))==0){
		        $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES ( 'acquisition', 'rent_requests_activate', '0', 'Activation des demandes de location:\n 0 : non \n 1 : oui','', 0)";
		        echo traite_rqt($rqt,"insert acquisition_rent_requests_activate into parametres");
		    }
		    
		    // TS - Modification de la taille du champ name de la table titres_uniformes
		    $rqt = "ALTER TABLE titres_uniformes MODIFY tu_name TEXT" ;
		    echo traite_rqt($rqt,"ALTER TABLE titres_uniformes MODIFY tu_name TO TEXT");
		    
		    // DG - Param�tre pour activer/d�sactiver la circulation des p�riodiques ?
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_active' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
			VALUES (0, 'pmb', 'serialcirc_active', '1','Activer la circulation des p�riodiques ? \r\n 0: Non \r\n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_serialcirc_active into parametres");
		    }
		    
		    // DG - Modification du champ date_relance de la table lignes_actes_relances
		    $rqt = "ALTER TABLE lignes_actes_relances MODIFY date_relance DATETIME DEFAULT '0000-00-00 00:00:00'" ;
		    echo traite_rqt($rqt,"alter table lignes_actes_relances modify date_relance");
		    
		    // DG - Vues OPAC dans les recherches pr�d�finies
		    $rqt = "show fields from search_persopac";
		    $res = pmb_mysql_query($rqt);
		    $exists = false;
		    if(pmb_mysql_num_rows($res)){
		        while($row = pmb_mysql_fetch_object($res)){
		            if($row->Field == "search_opac_views_num"){
		                $exists = true;
		                break;
		            }
		        }
		    }
		    if(!$exists){
		        $rqt = "ALTER TABLE search_persopac ADD search_opac_views_num text NOT NULL";
		        echo traite_rqt($rqt,"alter table search_persopac add search_opac_views_num");
		        
		        $req = "select search_id, search_opac_views_num from search_persopac";
		        $res = pmb_mysql_query($req);
		        if ($res) {
		            $search_persopac = array();
		            while($row = pmb_mysql_fetch_object($res)) {
		                $search_persopac[$row->search_id] = array();
		            }
		            if (count($search_persopac)) {
		                $req = "select opac_filter_view_num, opac_filter_param from opac_filters where opac_filter_path='search_perso'";
		                $myQuery = pmb_mysql_query($req);
		                if ($myQuery) {
		                    while ($row = pmb_mysql_fetch_object($myQuery)) {
		                        $param = unserialize($row->opac_filter_param);
		                        if(is_array($param['selected'])) {
		                            foreach ($param['selected'] as $selected) {
		                                $search_persopac[$selected]['opac_views_num'][] = $row->opac_filter_view_num;
		                            }
		                        }
		                    }
		                    foreach ($search_persopac as $id=>$search_p) {
		                        if(!isset($search_p['opac_views_num']) || !is_array($search_p['opac_views_num'])) {
		                            $search_p['opac_views_num'] = array();
		                        }
		                        $query = "update search_persopac set search_opac_views_num = '".implode(',', $search_p['opac_views_num'])."' where search_id = '".$id."'";
		                        pmb_mysql_query($query);
		                    }
		                }
		            }
		        }
		    }
		    
		    // DG - Ajout du champ abt_name_opac permettant de pr�ciser un libell� OPAC pour l'abonnement
		    $rqt = "ALTER TABLE abts_abts ADD abt_name_opac VARCHAR(255) NOT NULL DEFAULT '' AFTER abt_name";
		    echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_name_opac");
		    
		    // DG - Ajout dans les bannettes la possibilit� de choisir un r�pertoire de templates
		    $rqt = "ALTER TABLE bannettes ADD django_directory VARCHAR( 255 ) NOT NULL default '' AFTER notice_tpl ";
		    echo traite_rqt($rqt,"alter table bannettes add django_directory");
		    
		    // DG - Ajout dans les bannettes la possibilit� de choisir un r�pertoire de templates pour le produit documentaire
		    $rqt = "ALTER TABLE bannettes ADD document_django_directory VARCHAR( 255 ) NOT NULL default '' AFTER document_notice_tpl ";
		    echo traite_rqt($rqt,"alter table bannettes add document_django_directory");
		    
		    // DG - Ajout dans les bannettes la possibilit� de choisir entre un template de notices et un r�pertoire django
		    $rqt = "ALTER TABLE bannettes ADD notice_display_type INT( 1 ) UNSIGNED NOT NULL default 0 AFTER piedpage_mail ";
		    echo traite_rqt($rqt,"alter table bannettes add notice_display_type");
		    
		    // DG - Ajout dans les bannettes la possibilit� choisir entre un template de notices et un r�pertoire django pour le produit documentaire
		    $rqt = "ALTER TABLE bannettes ADD document_notice_display_type INT( 1 ) UNSIGNED NOT NULL default 0 AFTER document_generate ";
		    echo traite_rqt($rqt,"alter table bannettes add document_notice_display_type");
		    
		    // DG - Connecteurs sortants JSON-RPC = 5, Connecteurs sortants Bibloto = 10
		    //Jusqu'� pr�sent le commentaire �tait utilis�e pour crypter la connexion
		    //Transfert de la valeur du commentaire dans un champ d�di� � cela
		    $query = "select connectors_out_source_id, connectors_out_source_name, connectors_out_source_comment, connectors_out_source_config from connectors_out_sources where connectors_out_sources_connectornum IN(5,10)";
		    $result = pmb_mysql_query($query);
		    if ($result && pmb_mysql_num_rows($result)) {
		        while ($source = pmb_mysql_fetch_object($result)) {
		            $source_config = unserialize($source->connectors_out_source_config);
		            // !isset pour s'assurer que l'on n'est pas encore pass� ici
		            // afin de ne pas �craser la phrase de connexion � chaque passage de l'alter ou de l'add-on
		            if(!isset($source_config['auth_connexion_phrase'])) {
		                $source_config['auth_connexion_phrase'] = $source->connectors_out_source_comment;
		                $query = "update connectors_out_sources set connectors_out_source_config = '".addslashes(serialize($source_config))."' where connectors_out_source_id = ".$source->connectors_out_source_id;
		                pmb_mysql_query($query);
		                echo traite_rqt($rqt,"UPDATE connectors_out_sources ".$source->connectors_out_source_name);
		            }
		        }
		    }
		    
		    // DB - Ajout champ add_to_new_order dans table Frais annexes
		    $rqt = "ALTER TABLE frais ADD add_to_new_order INT(1) NOT NULL DEFAULT 0 AFTER index_libelle" ;
		    echo traite_rqt($rqt,"alter table frais add field add_to_new_order");
		    
		    // NG - Ajout param�tre permettant de bloquer ou pas le pr�t lorsqu'une r�servation est faites sans validation
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_resa_non_validee' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
			    VALUES (0, 'pmb', 'pret_resa_non_validee', '0',' Bloquer le pr�t lorsqu\'une r�servation sans validation est faite pour un autre lecteur ? \r\n 0: Non \r\n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_pret_resa_non_validee into parametres");
		    }
		    
		    // BT - Suppression des espaces dans le code des instruments
		    $res = pmb_mysql_query("SELECT id_instrument, instrument_code FROM nomenclature_instruments");
		    while ($row = pmb_mysql_fetch_object($res)) {
		        $code = str_replace(' ', '', $row->instrument_code);
		        pmb_mysql_query("UPDATE nomenclature_instruments SET instrument_code='$code' WHERE id_instrument=$row->id_instrument");
		    }
		    echo traite_rqt("SELECT 1", "UPDATE nomenclature_instruments SET instrument_code without spaces");
		    
		    // +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			$rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
			break;

	default:
		include("$include_path/messages/help/$lang/alter.txt");
		break;
	}

	
