<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette.class.php,v 1.39.4.2 2015-10-06 15:36:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ("$class_path/search.class.php") ; 
require_once ("$class_path/equation.class.php") ; 
require_once ($include_path."/mail.inc.php") ;
require_once ($include_path."/export_notices.inc.php");
require_once($class_path."/notice_tpl_gen.class.php");
if($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once ("$class_path/acces.class.php") ; 
}
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/bannette_facettes.class.php");
require_once($class_path."/bannette_tpl.class.php");

// définition de la classe de gestion des 'bannettes'
class bannette {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	var $id_bannette=0;	
	var $num_classement=1; 
	var $nom_classement=""; 
	var	$nom_bannette="";
	var	$comment_gestion="";
	var	$comment_public="";
	var	$entete_mail="";
	var	$bannette_tpl_num=0;
	var	$piedpage_mail="";
	var	$notice_tpl="";
	var	$date_last_remplissage="";
	var	$date_last_envoi="";
	var	$aff_date_last_remplissage="";
	var	$aff_date_last_envoi="";
	var $date_last_envoi_sql="";
	var	$proprio_bannette=0;
	var	$bannette_auto=0;
	var	$periodicite=0;
	var	$diffusion_email=0;
	var $nb_notices_diff=0;
	var	$categorie_lecteurs=0;
	var	$groupe_lecteurs=0;
	var	$update_type="C";
	var	$nb_notices=0;
	var	$nb_abonnes=0;
	var	$alert_diff=0;
	var $texte_export ;
	var $texte_diffuse ;
	var $num_panier ;
	var $limite_type; // D ou  I : Days ou Items
	var $limite_nombre; // Nombre limite, = soit durée de vie d'une notice dans la bannette ou bien nombre maxi de notices dans le panier
	var $liste_id_notice = array();
	var $export_contenu = "";
	var $typeexport = "pmbxml2marciso";
	var $prefixe_fichier = "prefix_";
	var $param_export = array();
	var	$group_pperso=0;
	var $display_notice_in_every_group=1;
	var $archive_number=0;
	var $group_type = 0;
	var	$statut_not_account=0;
	var $field_type='';
	var $field_id=0;
	var $group_pperso_order=array();
	var $document_generate=0;
	var $document_notice_tpl=0;
	var $document_insert_docnum=0;
	var $document_group=0;
	var $document_add_summary=0;
	var $aff_document="";
	var $bannette_opac_accueil=0;
	var $document_diffuse=""; //contenu html du document généré

	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	function bannette($id=0) {
		if ($id) {
			// on cherche à atteindre une notice existante
			$this->id_bannette = $id;
			$this->getData();
		} else {
			// la notice n'existe pas
			$this->id_bannette = 0;
			$this->getData();
		}
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos
	// ---------------------------------------------------------------
	function getData() {
		global $dbh;
		global $msg;
		$this->p_perso=new parametres_perso("notices");
		if (!$this->id_bannette) {
			// pas d'identifiant. on retourne un tableau vide
		 	$this->id_bannette=0;
		 	$this->num_classement = 1 ;
		 	$this->nom_classement = "" ;
			$this->nom_bannette="";
			$this->comment_gestion="";
			$this->comment_public="";
			$this->entete_mail="";
			$this->bannette_tpl_num=0;
			$this->piedpage_mail="";
			$this->notice_tpl="";
			$this->date_last_remplissage="";
			$this->date_last_envoi=today();
			$this->aff_date_last_remplissage="";
			$this->aff_date_last_envoi=formatdate($this->date_last_envoi);
			$this->date_last_envoi_sql=today();
			$this->proprio_bannette=0;
			$this->bannette_auto=0;
			$this->periodicite=0;
			$this->diffusion_email=0;
			$this->nb_notices_diff=0;
			$this->categorie_lecteurs="";
			$this->groupe_lecteurs="";
			$this->update_type="C";
			$this->nb_notices = 0 ;
			$this->nb_abonnes = 0 ;
			$this->alert_diff = 0 ;
			$this->num_panier = 0 ;
			$this->limite_type = '' ;
			$this->limite_nombre = 0 ;
			$this->typeexport = '';
			$this->group_pperso = 0;
			$this->group_type = 0;
			$this->display_notice_in_every_group=1;
			$this->statut_not_account = 0;
			$this->archive_number = 0;
			$this->document_generate=0;
			$this->document_notice_tpl=0;
			$this->document_insert_docnum=0;
			$this->document_group=0;
			$this->document_add_summary=0;
			$this->descriptor_num=0;
			$this->prefixe_fichier = "prefix_";
			$this->bannette_opac_accueil = 0;
		} else {
			$requete = "SELECT id_bannette, num_classement, nom_bannette,comment_gestion,comment_public,statut_not_account, ";
			$requete .= "date_last_remplissage, date_format(date_last_remplissage, '".$msg["format_date_heure"]."') as aff_date_last_remplissage, ";
			$requete .= "date_last_envoi,date_last_envoi as date_last_envoi_sql, date_format(date_last_envoi, '".$msg["format_date_heure"]."') as aff_date_last_envoi, ";
			$requete .= "proprio_bannette,bannette_auto,periodicite,diffusion_email, nb_notices_diff, categorie_lecteurs, groupe_lecteurs, update_type, entete_mail, bannette_tpl_num, piedpage_mail, notice_tpl, num_panier, ";
			$requete .= "limite_type, limite_nombre, typeexport, prefixe_fichier, param_export, group_type, group_pperso, display_notice_in_every_group, archive_number, ";
			$requete .= "document_generate, document_notice_tpl, document_insert_docnum, document_group, document_add_summary, bannette_opac_accueil ";
			$requete .= "FROM bannettes WHERE id_bannette='".$this->id_bannette."' " ;
			$result = pmb_mysql_query($requete, $dbh) or die ($requete."<br /> in bannette.class.php : ".pmb_mysql_error());
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
			 	$this->id_bannette			= $temp->id_bannette ;
			 	$this->num_classement 		= $temp->num_classement ;
				$this->nom_bannette			= $temp->nom_bannette ;
				$this->comment_gestion		= $temp->comment_gestion ;	
				$this->comment_public		= $temp->comment_public ;
				$this->bannette_tpl_num			= $temp->bannette_tpl_num ;
				$this->entete_mail			= $temp->entete_mail ;
				$this->piedpage_mail		= $temp->piedpage_mail ;
				$this->notice_tpl			= $temp->notice_tpl ;
				$this->date_last_remplissage= $temp->date_last_remplissage ;
				$this->date_last_envoi		= $temp->date_last_envoi ;	
				$this->aff_date_last_remplissage	= $temp->aff_date_last_remplissage ;
				$this->aff_date_last_envoi	= $temp->aff_date_last_envoi ;	
				$this->date_last_envoi_sql	= $temp->date_last_envoi_sql;
				$this->proprio_bannette		= $temp->proprio_bannette ;	
				$this->bannette_auto		= $temp->bannette_auto ;
				$this->periodicite			= $temp->periodicite ;
				$this->diffusion_email		= $temp->diffusion_email ;	
				$this->nb_notices_diff 		= $temp->nb_notices_diff;
				$this->categorie_lecteurs	= $temp->categorie_lecteurs ;
				$this->groupe_lecteurs		= $temp->groupe_lecteurs ;
				$this->update_type			= $temp->update_type ;
				$this->num_panier			= $temp->num_panier ;
				$this->limite_type 			= $temp->limite_type ;
				$this->limite_nombre 		= $temp->limite_nombre ;
				$this->typeexport 			= $temp->typeexport ;
				$this->prefixe_fichier 		= $temp->prefixe_fichier ;
				$this->group_pperso 		= $temp->group_pperso ;
				$this->group_type 			= $temp->group_type;
				$this->display_notice_in_every_group=$temp->display_notice_in_every_group;
				$this->statut_not_account 	= $temp->statut_not_account ;
				$this->archive_number 		= $temp->archive_number ;
				$this->document_generate 	= $temp->document_generate ;
				$this->document_notice_tpl	= $temp->document_notice_tpl;
				$this->document_insert_docnum= $temp->document_insert_docnum ;
				$this->document_group 		= $temp->document_group ;
				$this->document_add_summary = $temp->document_add_summary ;
				$this->descriptor_num		= $temp->ban_descriptor_num ;
				$this->bannette_opac_accueil= $temp->bannette_opac_accueil ;
				
				$this->param_export			= unserialize($temp->param_export) ;
				$this->compte_elements();
				$requete = "SELECt nom_classement FROM classements WHERE id_classement='".$this->num_classement."'" ;
				$resultclass = pmb_mysql_query($requete, $dbh) or die ($requete."<br /> in bannette.class.php : ".pmb_mysql_error());
			 	if ($temp = pmb_mysql_fetch_object($resultclass)) $this->nom_classement = $temp->nom_classement ;
			 	else $this->nom_classement = "" ;
			} else {
				// pas de bannette avec cette clé
			 	$this->id_bannette=0;
			 	$this->num_classement = 1 ;
			 	$this->nom_classement = "" ;
				$this->nom_bannette="";
				$this->comment_gestion="";
				$this->comment_public="";
				$this->bannette_tpl_num=0;
				$this->entete_mail="";
				$this->piedpage_mail="";
				$this->notice_tpl="";
				$this->date_last_remplissage="";
				$this->date_last_envoi="";
				$this->date_last_envoi_sql="";
				$this->aff_date_last_remplissage="";
				$this->aff_date_last_envoi="";
				$this->proprio_bannette=0;
				$this->bannette_auto=0;
				$this->periodicite=0;
				$this->diffusion_email=0;
				$this->nb_notices_diff=0;
				$this->categorie_lecteurs="";
				$this->groupe_lecteurs="";
				$this->update_type="C";
				$this->nb_notices = 0 ;
				$this->nb_abonnes = 0 ;
				$this->num_panier = 0 ;
				$this->limite_type = '' ;
				$this->limite_nombre = 0 ;
				$this->typeexport = '' ;
				$this->prefixe_fichier = "prefix_";
				$this->group_pperso = 0;
				$this->group_type = 0;
				$this->display_notice_in_every_group=1;
				$this->statut_not_account = 0;
				$this->archive_number=0;
				$this->document_generate=0;
				$this->document_notice_tpl=0;
				$this->document_insert_docnum=0;
				$this->document_group=0;
				$this->document_add_summary=0;
				$this->descriptor_num=0;
				$this->bannette_opac_accueil=0;
			}
		}
	}

	// ---------------------------------------------------------------
	//		vider() : vider le contenu de la bannette 
	// ---------------------------------------------------------------
	function vider() {
		global $dbh;
		global $msg;
		
		if (!$this->id_bannette) return $msg[dsi_ban_no_access]; // impossible d'accéder à cette bannette
	
		$requete = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette'";
		$res = pmb_mysql_query($requete, $dbh);
		
		$this->compte_elements() ;
		
		return $requete ;
	}

	// ---------------------------------------------------------------
	//		remplir() : remplir la bannette à partir des équations 
	// ---------------------------------------------------------------
	function remplir() {
		global $dbh;
		global $msg;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		if (!$this->id_bannette) return $msg[dsi_ban_no_access]; // impossible d'accéder à cette bannette
		
		// récupérer les équations associées à la bannette
		$equations = $this->get_equations() ;
		$res_affichage = "<ul>" ;
		if ($this->update_type=="C") $colonne_update_create="create_date";
			else $colonne_update_create="update_date";
		for ($i=0 ; $i < sizeof($equations) ; $i++) {
			// pour chaque équation ajouter les notices trouvées au contenu de la bannette
			$equ = new equation ($equations[$i]) ;
			$search = new search() ;
			$search->unserialize_search($equ->requete) ;
			$table = $search->make_search() ;
			$temp_requete = "insert into bannette_contenu (num_bannette, num_notice) (select ".$this->id_bannette." , notices.notice_id from $table , notices, notice_statut where notices.$colonne_update_create>='".$this->date_last_envoi."' and $table.notice_id=notices.notice_id and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0) or (notice_visible_opac_abon=1 and notice_visible_opac=1)) limit 300) " ;
			$res = @pmb_mysql_query($temp_requete, $dbh);
			$res_affichage .= "<li>".$equ->human_query."</li>" ;
		    $temp_requete = "drop table $table " ;
			$res = @pmb_mysql_query($temp_requete, $dbh);
		}
		$res_affichage .= "</ul>" ;
		$this->compte_elements() ;
		$temp_requete = "update bannettes set date_last_remplissage=sysdate() where id_bannette='".$this->id_bannette."' " ;
		$res = @pmb_mysql_query($temp_requete, $dbh);
	
		//purge pour les bannettes privees des notices ne devant pas etre diffusees 
		if ($this->proprio_bannette && $gestion_acces_active==1 && $gestion_acces_empr_notice==1){
			$ac = new acces();
			$dom_2 = $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($this->proprio_bannette,'4=0','num_notice');
			
			$q="delete from bannette_contenu using bannette_contenu $acces_j WHERE num_bannette='$this->id_bannette' ";
			pmb_mysql_query($q,$dbh);
		}
	
		return $res_affichage ;
	}

	// ---------------------------------------------------------------
	//		construit_diff() :   
	// ---------------------------------------------------------------
	function construit_diff($texte="") {
		global $charset;
		$contenu = $this->construit_contenu_HTML() ;
		$contenu_total = $this->construit_contenu_HTML(0) ;
		$titre = $this->construit_liens_HTML($texte) ;
		$this->texte_diffuse = "<html><head><META HTTP-EQUIV='CONTENT-TYPE' CONTENT='text/html; charset=".$charset."'></head><body>". $titre;
		if ($this->diffusion_email) $this->texte_diffuse .= $contenu ;
		$this->texte_diffuse .= "</body></html>";
		$this->texte_export = "<html><head><META HTTP-EQUIV='CONTENT-TYPE' CONTENT='text/html; charset=".$charset."'></head><body>".$titre . $contenu_total . "</body></html>";
	}	


	// ---------------------------------------------------------------
	//		diffuser() : diffuser le contenu de la bannette  
	// ---------------------------------------------------------------
	function diffuser($texte="") {
		global $dbh;
		global $msg, $charset, $base_path;
		
		global $PMBusernom, $opac_biblio_email , $opac_biblio_name ;
		global $PMBuserprenom;
		global $PMBuseremail;
		global $id_empr ;
		
		// paramétrage OPAC: choix du nom de la bibliothèque comme expéditeur
		$requete = "select location_libelle, email from empr, docs_location where empr_location=idlocation and id_empr='$id_empr' ";
		$res = pmb_mysql_query($requete, $dbh);
		$loc=pmb_mysql_fetch_object($res) ;
		
		$PMBusernom = $loc->location_libelle ;
		$PMBuserprenom = "" ;
		$PMBuseremail = $loc->email ;
		$this->construit_diff($texte);
		$texte_base = $this->texte_diffuse ;
		
		$res_envoi = false;
		if($this->nb_notices > 0){
			if ($this->export_contenu) {
				$fic_params = $base_path."/admin/convert/imports/".$this->typeexport."/params.xml";
				$temppar = file_get_contents($fic_params);
				$params = _parser_text_no_function_($temppar,"PARAMS");
				if ($params["OUTPUT"][0]["SUFFIX"]) $ext=$params["OUTPUT"][0]["SUFFIX"];
				else $ext="fic";
				$pieces_jointes[0][nomfichier] = $this->prefixe_fichier.today().".".$ext ;
				$pieces_jointes[0][contenu] = $this->export_contenu ;
			}
			$nb_dest=0;
			$nb_echec=0;
			$nb_no_email=0;
			
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=".$charset."\n";
			
			$requete = "select id_empr, empr_mail, empr_nom, empr_prenom, empr_login, empr_password from empr, bannette_abon ";
			$requete .= "where num_bannette='".$this->id_bannette."' and empr_date_expiration>=sysdate() and num_empr=id_empr ";
			$requete .= "order by empr_nom, empr_prenom ";
			$res = pmb_mysql_query($requete, $dbh);
			while($empr=pmb_mysql_fetch_object($res)) {
				$emaildest = $empr->empr_mail;
				$texte = $texte_base ; 
				if ($emaildest) // $res_envoi=@mail("$emaildest",$this->comment_public,$texte." ","From: ".$PMBuserprenom." ".$PMBusernom." <".$PMBuseremail.">\r\n".$headers);
					$res_envoi=@mailpmb($empr->empr_prenom." ".$empr->empr_nom, $emaildest,$this->comment_public,$texte,$PMBuserprenom." ".$PMBusernom, $PMBuseremail, $headers, "", "", 0, $pieces_jointes);
			}
			/* A commenter pour tests */ 
			$temp_requete = "update bannettes set date_last_envoi=sysdate() where id_bannette='".$this->id_bannette."' " ;
			$res = pmb_mysql_query($temp_requete, $dbh);
		} 
		return $res_envoi ;
	}

	// ---------------------------------------------------------------
	//		get_equations() : construire un tableau des équations associées  
	// ---------------------------------------------------------------
	function get_equations() {
		global $dbh;
		global $msg;
		
		if (!$this->id_bannette) return $msg[dsi_ban_no_access]; // impossible d'accéder à cette bannette
	
		$requete = "select num_equation from bannette_equation, equations WHERE num_bannette='$this->id_bannette' and id_equation=num_equation ";
		$res = pmb_mysql_query($requete, $dbh);
		while($equ=pmb_mysql_fetch_object($res)) {
			$tab_equ[] = $equ->num_equation ;
		}
		return $tab_equ ;
	}

	// ---------------------------------------------------------------
	//		construit_contenu_HTML() : Préparation du contenu du mail ou du bulletin
	// ---------------------------------------------------------------
	function construit_contenu_HTML ($use_limit=1) {
		global $dbh;
		global $msg;
		global $opac_url_base ;
		global $liens_opac ;
		
		$url_base_opac = $opac_url_base."index.php?database=".DATA_BASE."&lvl=notice_display&id=";
		
		if ($this->nb_notices_diff && $use_limit) $limitation = " LIMIT $this->nb_notices_diff " ;
		$requete = "select num_notice from bannette_contenu, notices where num_bannette='".$this->id_bannette."' and notice_id=num_notice order by index_serie, tnvol, index_sew $limitation ";
		$resultat = pmb_mysql_query($requete, $dbh);
	
		// paramétrage :
		$environement["short"] = 6 ;
		$environement["ex"] = 0 ;
		$environement["exnum"] = 0 ;
		
		if (($this->nb_notices_diff >= $this->nb_notices) || (!$this->nb_notices_diff)) $nb_envoyees = $this->nb_notices ;
			else $nb_envoyees = $this->nb_notices_diff ;
		$resultat_aff .= "<hr />";
		$resultat_aff .= sprintf($msg["dsi_diff_n_notices"],$nb_envoyees, $this->nb_notices);
		$resultat_aff .= "<hr />";
		while ($r=pmb_mysql_fetch_object($resultat)) {
			// afin de ne pas afficher les liens pour réservation de doc dans le mail :
			global $opac_resa ;
			$opac_resa = 0 ;
			$depliable = 0 ;
			$notice = new notice_affichage($r->num_notice, $liens_opac) ;
			$notice->do_header();
			$notice->do_isbd();
			$notice->genere_simple($depliable, 'ISBD') ;
			$resultat_aff .= "<a href='".$url_base_opac.$r->num_notice."'><b>".$notice->notice_header."</b></a><br /><br />\r\n";
			$resultat_aff .= $notice->notice_isbd;
			$resultat_aff .= "<hr />\r\n";
		}
		if ($this->typeexport) {
			$this->export_contenu=cree_export_notices($this->liste_id_notice, start_export::get_id_by_path($this->typeexport), 1) ;
		}
	
		return $resultat_aff ;
	}

	// ---------------------------------------------------------------
	//		construit_liens_HTML()
	// ---------------------------------------------------------------
	function construit_liens_HTML($texte="") {
		global $dbh;
		global $opac_url_base, $opac_connexion_phrase ;
		global $msg;
		global $charset ;
		
		$url_base_opac = $opac_url_base."empr.php?lvl=bannette";
		$resultat_aff .= "<style type='text/css'>
			body { 	
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			color:#000000;
			background:#FFFFFF;
			}
			td {
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			color:#000000;
			}
			th {
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			font-weight:bold;
			color:#000000;
			background:#DDDDDD;
			text-align:left;
			}
			hr {
			border:none;
			border-bottom:1px solid #000000;
			}
			h3 {
			font-size: 12pt;
			color:#000000;
			}
			</style>";
		
		$req = "select empr_login from empr where id_empr=$this->proprio_bannette";
		$res = pmb_mysql_query($req,$dbh);
		$empr = pmb_mysql_fetch_object($res);
		$date_today = formatdate(today()) ;
		$date = time();
		$login = $empr->empr_login;
		$code=md5($opac_connexion_phrase.$login.$date);	
		$public  = "<a href='$url_base_opac&id_bannette=".$this->id_bannette."&code=$code&emprlogin=$login&date_conex=$date'>";
		$public .= htmlentities($this->comment_public,ENT_QUOTES, $charset) ;
		$public .= "</a>";
	
		$entete = str_replace ("!!public!!",$public,$this->entete_mail) ;
		$entete = str_replace ("!!equation!!",$texte,$entete) ;
		$entete = str_replace ("!!date!!",$date_today,$entete) ;
		
		return $entete ;
	}

	// ---------------------------------------------------------------
	//		compte_elements() : méthode pour pouvoir recompter en dehors !
	// ---------------------------------------------------------------
	function compte_elements() {
		global $dbh ;
		
		$req_nb = "SELECT num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' " ;
		$res_nb = pmb_mysql_query($req_nb, $dbh) or die ($req_nb."<br /> in bannette.class.php : ".pmb_mysql_error());
		$this->nb_notices = pmb_mysql_num_rows($res_nb);
		//initialisation du tableau à chaque fois que cette fonction est appelée pour éviter un mauvais cumul
		$this->liste_id_notice = array();
		while ($res = pmb_mysql_fetch_object($res_nb)) {
			$this->liste_id_notice[]=$res->num_notice ;
		}
		
		$req_nb = "SELECT count(1) as nb_abonnes from bannette_abon WHERE num_bannette='".$this->id_bannette."' " ;
		$res_nb = pmb_mysql_query($req_nb, $dbh) or die ($req_nb."<br /> in bannette.class.php : ".pmb_mysql_error());
		$res = pmb_mysql_fetch_object($res_nb);
		$this->nb_abonnes = $res->nb_abonnes ;
		$requete = "SELECt if(date_last_remplissage>date_last_envoi,1,0) as alert_diff ";
		$requete .= "FROM bannettes WHERE id_bannette='".$this->id_bannette."' " ;
		$result = pmb_mysql_query($requete, $dbh) or die ($requete."<br /> in bannette.class.php : ".pmb_mysql_error());
		$temp = pmb_mysql_fetch_object($result);
		$this->alert_diff = $temp->alert_diff ; 

	}
	
	function pmb_ksort(&$table){
		$table_final=array();
		if ($this->field_type == 'list') {
			if (is_array($table)) {
				reset($table);
				$tmp=array();
				$requete = "select ordre, notices_custom_list_lib from notices_custom_lists";
				$requete .= " where notices_custom_champ=".$this->field_id;
				$res = pmb_mysql_query($requete);
				while ($row = pmb_mysql_fetch_object($res)) {
					$this->group_pperso_order[$row->notices_custom_list_lib] = $row->ordre;
				}
				uksort($table, array(&$this,"cmp_pperso"));
			}
		} else {
			if (is_array($table)) {
				reset($table);
				$tmp=array();
				foreach ($table as $key => $value ) {
					$tmp[]=strtoupper(convert_diacrit($key));
					$tmp_key[]=$key;
					$tmp_contens[]=$value;
				}
				asort($tmp);
				foreach ($tmp as $key=>$value ) {
					$table_final[$tmp_key[$key]]=$tmp_contens[$key];
				}
				$table=$table_final;
			}
		}
	}
	
	function cmp_pperso($a,$b) {
		if ($this->group_pperso_order[$a]>$this->group_pperso_order[$b]) return 1;
		if ($this->group_pperso_order[$a]<$this->group_pperso_order[$b]) return -1;
		return 0;
	
	}
	
	// ---------------------------------------------------------------
	//		construit_contenu_HTML() : Préparation du contenu du mail ou du bulletin
	// ---------------------------------------------------------------
	function get_datas_content ($use_limit=1) {
		global $dbh;
		global $msg;
		global $opac_url_base, $use_opac_url_base ;
		global $opac_notice_affichage_class;
		global $liens_opac ;
		global $dsi_bannette_notices_order ;
	
		if ($this->nb_notices_diff && $use_limit) $limitation = " LIMIT $this->nb_notices_diff " ;
		$requete = "select num_notice from bannette_contenu, notices where num_bannette='".$this->id_bannette."' and notice_id=num_notice order by index_serie, tnvol, index_sew $limitation ";
		$resultat = pmb_mysql_query($requete, $dbh);
	
		if($this->notice_tpl){
			$noti_tpl=new notice_tpl_gen($this->notice_tpl);
		}
		$liste=array();
		$liste_group=array();
		$notice_group=array();
		if(pmb_mysql_num_rows($resultat)) {
			while (($temp = pmb_mysql_fetch_object($resultat))) {
				// Si un champ perso est donné comme critère de regroupement
				if($this->group_pperso && $this->group_type!=1) {
					$this->p_perso->get_values($temp->num_notice);
					$values = $this->p_perso->values;
					$trouve = false;
					foreach ( $values as $field_id => $vals ) {
						if ($this->group_pperso==$field_id) {

							foreach($vals as $cpVal){
								$notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array($cpVal),$field_id);
								if (!$cpVal) {
									$cpVal = "_no_value_";
								}
								$liste_group[$cpVal][] = $temp;
								$trouve = true;
							}

							$this->field_type = $this->p_perso->t_fields[$field_id]["TYPE"];
							$this->field_id = $field_id;
						}
					}
					if (!$trouve) {
						$liste_group["_no_value_"][] = $temp;
						$notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array(),$field_id);
					}
				}
				else $liste[] = $temp ;
			}
		}
	
		// groupement par facettes
		if (count($liste) && $this->group_type==1) {
			$notice_ids=array();
			foreach($liste as $r) $notice_ids[]=$r->num_notice;
			$facette = new bannette_facettes($this->id_bannette);
			$this->data_document['sommaires']=$facette->build_document_data($notice_ids,$this->document_notice_tpl);
			return;
		}
		if(count($liste_group)) {
			foreach($liste_group as $list_notice) {
				$req_list=array();
				foreach($list_notice as $r) {
					$req_list[]=$r->num_notice;
				}
				$requete = "select notice_id as num_notice from  notices where  notice_id in(".implode(",",$req_list).") order by $dsi_bannette_notices_order ";
				$res_tri = pmb_mysql_query($requete, $dbh) ;
				while (($r = pmb_mysql_fetch_object($res_tri))) {
					$liste[] = $r;
				}
			}
		}
		$tri_tpl=array();
		if ($liste) {
			$already_printed=array();
	
			foreach($liste as $r) {
					$tpl="";
					if($this->notice_tpl) {
						$tpl=$noti_tpl->build_notice($r->num_notice,$deflt2docs_location);
					}
					if(!$tpl) {
						if (!$opac_notice_affichage_class) $opac_notice_affichage_class="notice_affichage";
						$current = new $opac_notice_affichage_class($r->num_notice, $liens_opac);
						$current->do_isbd();
						$tpl.=$current->notice_isbd;
					}
					if($this->group_pperso) {
						if($notice_group[$r->num_notice]) {
							foreach($notice_group[$r->num_notice] as $id=>$cpDisplay){
					
								if($this->display_notice_in_every_group){
									$already_printed=array();
								}
					
								if(!$tri_tpl[$notice_group[$r->num_notice][$id]] || !in_array($tpl, $tri_tpl[$notice_group[$r->num_notice][$id]])){
									if(!in_array($r->num_notice, $already_printed)){
										$tri_tpl[$notice_group[$r->num_notice][$id]][]= $tpl;
										$already_printed[]=$r->num_notice;
									}
								}
							}
						}
					}else{
						$this->data_document['sommaires'][0]['records'][]['render']=$tpl;
					}
			}
		}
		
		// il faut trier les regroupements par ordre alphabétique
		if($this->group_pperso) {
			//ksort($tri_tpl);
			$this->pmb_ksort($tri_tpl);
			$index=0;
			foreach ($tri_tpl as $titre => $liste) {
				$index++;
				$this->data_document['sommaires'][$index]['level']=1;
				$this->data_document['sommaires'][$index]['title']=$titre;
				$nb=0;
				foreach ($liste as $val) {
					$this->data_document['sommaires'][$index]['records'][$nb]['render']=$val;
					$nb++;
				}
			}
		}
	}
	
} # fin de définition de la classe bannette
