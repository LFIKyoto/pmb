<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.5.14.16 2019-11-27 09:02:53 dgoron Exp $

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
    case 0 :
        // DG - Tri sur les flux RSS
        $rqt = "ALTER TABLE rss_flux ADD id_tri_rss_flux INT NOT NULL DEFAULT 0, ADD INDEX i_id_tri_rss_flux (id_tri_rss_flux)" ;
        echo traite_rqt($rqt,"alter table rss_flux add field id_tri_rss_flux");
    case 1 :
        // DG - Modification du commentaire du parametre gestion de monopole de pret
        $rqt = "update parametres set comment_param = 'Gestion de monopole de prêt\n 0: Non\n x: [message bloquant] Nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)\n 1,x: [message non bloquant] Nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)' where type_param='pmb' and sstype_param = 'loan_trust_management'";
        echo traite_rqt($rqt,"update parametres pmb_loan_trust_management set comment");
        
        //DG - Paramètre pour la personnalisation en PHP des relances d'acquisitions
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_print' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_print','','Quel script utiliser pour personnaliser l\'impression des relances ?','pdfrel',0)" ;
            echo traite_rqt($rqt,"insert acquisition_pdfrel_print into parametres") ;
        }
        
        // DG - Pré-remplissage du message en fonction de l'objet
        $rqt = "ALTER TABLE contact_form_objects ADD object_message text not null" ;
        echo traite_rqt($rqt,"alter table contact_form_objects add field object_message");
    case 2 :
        // DG - Modification du commentaire du parametre gestion de pret court
        $rqt = "update parametres set comment_param = 'Gestion des prêts courts\n 0: Non\n 1: Oui\n Attention, faire le retour des prêts courts avant de désactiver le module' where type_param='pmb' and sstype_param = 'short_loan_management'";
        echo traite_rqt($rqt,"update parametres pmb_short_loan_management set comment");
    case 3 :
        //DG - Paramètre pour ordonner la liste des exemplaires
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_order' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','expl_order','','Ordre d\'affichage des exemplaires, dans l\'ordre donné, séparé par des virgules : location_libelle,section_libelle,expl_cote,tdoc_libelle','',0)" ;
            echo traite_rqt($rqt,"insert pmb_expl_order into parametres") ;
        }
    case 4 :
        //DG - maj Colonnes exemplaires affichées en OPAC - ajout en commentaire des champs personnalisés
        $rqt = "update parametres set comment_param='Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle, #n : id des champs personnalisés' where type_param= 'opac' and sstype_param='expl_data' ";
        echo traite_rqt($rqt,"update opac_expl_data into parametres");
    case 5 :
        //DG - Paramètre pour localiser ou non l'indexation des éléments
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_location' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'indexation_location', '1', 'Localisation de l\'indexation activée.\n 0: Non\n 1: Oui', '',0) ";
            echo traite_rqt($rqt, "insert pmb_indexation_location into parameters");
        }        
    case 6 :
        // Ajout du pnb_flag dans la Table exemplaire
        $rqt = "ALTER TABLE exemplaires ADD expl_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table exemplaires add field expl_pnb_flag");
        // Ajout du pnb_flag dans la Table pret
        $rqt = "ALTER TABLE pret ADD pret_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table pret add field pret_pnb_flag");
        // Ajout du pnb_flag dans la Table resa
        $rqt = "ALTER TABLE resa ADD resa_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table resa add field resa_pnb_flag");
        // Ajout du pnb_flag dans la Table resa_archive
        $rqt = "ALTER TABLE resa_archive ADD resarc_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table resa_archive add field resarc_pnb_flag");
    case 7 :
        // TS & GN - Paramètre de tri par défaut des notices externes
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_external' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_external', 'd_num_6', '0', 'Tri par défaut des recherches externes à l\'OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche') ";
            echo traite_rqt($rqt,"INSERT opac_default_sort_external INTO parametres") ;
        }
        
        // TS & GN - Paramètre de définition du sélecteur de tri des notices externes
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_external_list' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_external_list', '1 d_num_6|Trier par pertinence', '0', 'Notices externes :\nAfficher la liste déroulante de sélection d\'un tri ?\n 0 : Non\n 1 : Oui\nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)', 'd_aff_recherche') ";
            echo traite_rqt($rqt,"INSERT opac_default_sort_external_list INTO parametres") ;
        }
        
    case 8 :
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
        
    case 9 :
        // NG - PNB : Ajout d'un paramètre caché pour affecter un code statistique à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_codestat_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_codestat_id', '0', 'Affectation d\'un code statistique à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_codestat_id into parameters");
        }
        // NG - PNB : Ajout d'un paramètre caché pour affecter un statut à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_statut_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_statut_id', '0', 'Affectation d\'un statut à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_statut_id into parameters");
        }
        // NG - PNB : Ajout d'un paramètre caché pour affecter un typedoc à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_typedoc_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_typedoc_id', '0', 'Affectation d\'un typedoc à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_typedoc_id into parameters");
        }
    case 10 :
        // DG - Ordre de tri (croissant / décroissant) des résultats de facettes dans les bannettes
        $rqt = "ALTER TABLE bannette_facettes ADD ban_facette_order_sort int(1) NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table bannette_facettes add ban_facette_order_sort");
        
        // DG - Type de données de tri des résultats de facettes dans les bannettes
        $rqt = "ALTER TABLE bannette_facettes ADD ban_facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha'";
        echo traite_rqt($rqt,"alter table bannette_facettes add ban_facette_datatype_sort");
        
        // DG - Ajout d'un commentaire de gestion sur le groupe
        $rqt = "ALTER TABLE groupe ADD comment_gestion TEXT NOT NULL DEFAULT ''";
        echo traite_rqt($rqt,"alter table groupe add comment_gestion");
        
        // DG - Ajout d'un commentaire OPAC sur le groupe
        $rqt = "ALTER TABLE groupe ADD comment_opac TEXT NOT NULL DEFAULT ''";
        echo traite_rqt($rqt,"alter table groupe add comment_opac");
    case 11 :
        //DG - Modification du champ pour un varchar afin d'accueillir les templates Django
        $rqt = "ALTER TABLE rss_flux MODIFY tpl_rss_flux VARCHAR(255) DEFAULT '0'";
        echo traite_rqt($rqt,"ALTER TABLE rss_flux MODIFY tpl_rss_flux VARCHAR(255)");
    case 12 :
        //DG - Ajout du champ pour personnaliser l'affichage du titre des éléments du flux RSS
        $rqt = "ALTER TABLE rss_flux ADD tpl_title_rss_flux VARCHAR(255) DEFAULT '0' AFTER export_court_flux";
        echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD tpl_title_rss_flux VARCHAR(255)");
    case 13 :
        // DB : Parametre d'augmentation par defaut pour les achats
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='increase_rate_percent' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'acquisition', 'increase_rate_percent', '2.00', 'Pourcentage d\'augmentation par défaut.', '',0) ";
            echo traite_rqt($rqt, "insert acquisition_increase_rate_percent=2.00 into parameters");
        }
    case 14 :
        //BT & QV : Paramètre gérant la regexep du contrôle du mot de passe empr
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='password_regexp' ")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (0, 'opac', 'websubscribe_password_regexp', '', 'Permet de choisir la regexp afin de contrôler le mot de passe emprunteur. Il ne faut pas mettre les délimiteurs.\n\nIl faut modifier le message empr_form_bad_security en conséquence afin de renseigner l\'utilisateur sur la présence du contrôle', 'f_modules', '0')";
            echo traite_rqt($rqt, "insert password_regexp = '' into parametres ");
        }
    case 15 :
        // DG : Ajout dans les préférences utilisateur du statut par défaut en création de document numérique sur une demande de numérisation
        $rqt = "ALTER TABLE users ADD deflt_scan_request_explnum_status INT(1) UNSIGNED NOT NULL DEFAULT 0 " ;
        echo traite_rqt($rqt,"ALTER users ADD deflt_scan_request_explnum_status");
        
        $rqt = "show fields from groupe";
        $res = pmb_mysql_query($rqt);
        $exists = 0;
        if(pmb_mysql_num_rows($res)){
            while($row = pmb_mysql_fetch_object($res)){
                if($row->Field == "lettre_resa" || $row->Field == "mail_resa" || $row->Field == "lettre_resa_show_nomgroup"){
                    $exists++;
                }
            }
        }
        // Il manque au moins un champ sur les 3.
        if($exists < 3){
            //DG - Lettre de réservation au référent
            $rqt = "ALTER TABLE groupe ADD lettre_resa INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_resa default 0");
            
            //DG - Mail de réservation au référent
            $rqt = "ALTER TABLE groupe ADD mail_resa INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD mail_resa default 0");
            
            //DG - Impression du nom du groupe sur la lettre de réservation
            $rqt = "ALTER TABLE groupe ADD lettre_resa_show_nomgroup INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_resa_show_nomgroup default 0");
            
            //DG - Mise à jour des informations en suivant le paramétrage existant
            $rqt = "update groupe set lettre_resa=lettre_rappel ";
            echo traite_rqt($rqt,"update groupe set lettre_resa=lettre_rappel");
            $rqt = "update groupe set mail_resa=mail_rappel ";
            echo traite_rqt($rqt,"update groupe set mail_resa=mail_rappel");
            $rqt = "update groupe set lettre_resa_show_nomgroup=lettre_rappel_show_nomgroup ";
            echo traite_rqt($rqt,"update groupe set lettre_resa_show_nomgroup=lettre_rappel_show_nomgroup");
        }
        
        //DG - Evolutions du paramètre pour les notifications sur les réservations OPAC
        $rqt = "update parametres set comment_param='Mode de notification par email des nouvelles réservations aux utilisateurs ? \n0 : Recevoir toutes les notifications \n1 : Notification des utilisateurs du site de gestion du lecteur \n2 : Notification des utilisateurs associés à la localisation par défaut en création d\'exemplaire \n3 : Notification des utilisateurs du site de gestion et de la localisation d\'exemplaire' where type_param= 'pmb' and sstype_param='resa_alert_localized' ";
        echo traite_rqt($rqt,"update pmb_resa_alert_localized into parametres");
}












/******************** JUSQU'ICI **************************************************/
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";