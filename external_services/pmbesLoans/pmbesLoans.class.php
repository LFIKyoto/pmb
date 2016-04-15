<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesLoans.class.php,v 1.3 2015-04-03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($include_path."/templates/relance.tpl.php");
require_once($include_path."/relance_func.inc.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class pmbesLoans extends external_services_api_class {
	var $error=false;		//Y-a-t-il eu une erreur
	var $error_message="";	//Message correspondant à l'erreur
	
	function restore_general_config() {
		
	}
	
	function form_general_config() {
		return false;
	}
	
	function save_general_config() {
		
	}
	
	//ex: "empr","empr_list","b,n,c,g","b,n,c,g".$localisation.",cs","n,g"
	// correspondance : ./includes/filter_list/empr/empr_list.xml
	// les 2 premiers params doivent-ils plutôt être forcées ??
	function filterLoansReaders($filter_name,$filter_source="",$display,$filter,$sort,$parameters) {
		global $empr_sort_rows, $empr_show_rows, $empr_filter_rows,$pmb_lecteurs_localises;

		if (SESSrights & CIRCULATION_AUTH) {
			if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
				if ($pmb_lecteurs_localises) $localisation=",l";
				else $localisation="";
				$filter=new filter_list($filter_name,$filter_source,$display,$filter.$localisation,$sort);
	
				$t_filters = explode(",",$filter->filtercolumns);
				foreach ($t_filters as $f) {
					$filters_selectors="f".$filter->fixedfields[$f]["ID"];
					if ($parameters[$filters_selectors]) {
						$tableau=array();
						foreach ($parameters[$filters_selectors] as $categ) {
							$tableau[$categ] = $categ;
						}
						global $$filters_selectors;
						$$filters_selectors = $tableau;
					}
				}
				$t_sort = explode(",",$filter->sortablecolumns);
				for ($j=0;$j<=count($t_sort)-1;$j++) {
	    			$sort_selector="sort_list_".$j;
	    			if ($parameters[$sort_selector]) {
						global $$sort_selector;
	    				$$sort_selector = $parameters[$sort_selector];    				
					}
	    		}
				$filter->activate_filters();
				$requete = $filter->query;						
			}
	
			$resultat=pmb_mysql_query($requete);
							
			$result = array();
			while ($row=pmb_mysql_fetch_assoc($resultat)) {
				$result = array(
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"categ_libelle" => utf8_normalize($row["libelle"]),
					"group_name" => utf8_normalize($row["group_name"]),
				);
			}
			return $result;
		} else {
			return array();
		}
	}
	
	/*Dépend du paramétrage PMB
	 * Retourne un chiffre >= 1 si des relances n'ont pas été envoyées par mail*/
	function relanceLoansReaders($t_empr) {

		if (SESSrights & CIRCULATION_AUTH) {
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			$requete.=" and id_empr in (".implode(",",$t_empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<now() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
			$resultat=pmb_mysql_query($requete);
			$not_all_mail=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_all_mail+=print_relance($r->id_empr);		
				}
			}
			return $not_all_mail;
		} else {
			return 0;
		}
	}
	
	function exportCSV($t_empr) {
		
		if (SESSrights & CIRCULATION_AUTH) {
			$req="TRUNCATE TABLE cache_amendes";
			pmb_mysql_query($req);
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			if (!isset($t_empr)) $t_empr[] = "0";
			$requete.=" and id_empr in (".implode(",",$t_empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<now() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
	
			$resultat=pmb_mysql_query($requete);
			$not_all_mail=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_all_mail+=print_relance($r->id_empr);		
				}
			}
			
			$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
			$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr";
			$res=pmb_mysql_query($req);
			while ($r=pmb_mysql_fetch_object($res)) {
				$relance_liste.=get_relance($r->id_empr);
			}
	
			//modification du template importé
			//possiblité de l'appeler sans le mot global
			//(juste pour noté qu'elle n'est pas valorisée ici)
			global $export_relance_tpl;
			$export_relance_tpl = str_replace("!!relance_liste!!",$relance_liste,$export_relance_tpl);
			
			return $export_relance_tpl;
		} else {
			return 0;
		}
	}
	
	//pour valider une action ...
	function commitActionEmpr($id_empr, $cb, $last_level_commit,$next_level) {
		
	}

	function listLoansReaders($loan_type=0, $f_loc=0,$f_categ=0,$f_group=0,$f_codestat=0,$sort_by=0,$limite_mysql='',$limite_page='') {
		global $dbh, $msg, $pmb_lecteurs_localises;
		
		if (SESSrights & CIRCULATION_AUTH) {
//			$empr = new emprunteur($empr_id);
		
			if ($loan_type) {
				switch ($loan_type) {
					case LIST_LOAN_LATE:
						break;
					case LIST_LOAN_CURRENT:
						break;
				}
			}
					
			$results = array();
			
			//REQUETE SQL
			$sql = "SELECT date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$sql .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$sql .= " IF(pret_retour>=CURDATE(),0,1) as retard, " ;
			$sql .= " id_empr, empr_nom, empr_prenom, empr_mail, id_empr, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
			$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
			$sql .= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
			$sql .= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
			$sql .= "        docs_type , pret, empr, empr_groupe ";
			$sql .= "WHERE ";
			if ($pmb_lecteurs_localises) {
				if ($f_loc) 
					$sql.= "empr_location in (".trim($f_loc,",").") AND "; 
			}
			if ($f_categ) {
				$sql .= "empr_categ in (".trim($f_categ,",").") AND ";
			}
			if ($f_group) {
				$sql .= "id_empr=empr_id and groupe_id in (".trim($f_group,",").") AND ";
			}
			if ($f_codestat) {
				$sql .= "empr_codestat in (".trim($f_codestat,",").") AND ";
			}
			$order = "";
			if ($sort_by) {
				$t_sort_by = explode(",",$sort_by);
				foreach ($t_sort_by as $v_sort_by) {
					if ($v_sort_by == "n") {
						$order .= "empr_nom, empr_prenom,";
					}
					if ($v_sort_by == "g") {
						$order .= "groupe_id,";
					}
				}
			}
		
			$sql.= "expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr ";
			if ($order != '') {
				$sql .= "order by ".trim($order,",");			
			}
			if ($limite_mysql && $limite_page) {
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
			}
								
			$res = pmb_mysql_query($sql, $dbh);
			if (!$res) {
				return false;
	//			throw new Exception("Not found: Error");	
			}

			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"aff_pret_date" => utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => utf8_normalize($row["aff_pret_retour"]),
					"retard" => utf8_normalize($row["retard"]),
					"id_empr" => $row["id_empr"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"empr_mail" => utf8_normalize($row["empr_mail"]),
					"empr_cb" => $row["empr_cb"],
					"expl_cote" => utf8_normalize($row["expl_cote"]),
					"expl_cb" => utf8_normalize($row["expl_cb"]),
					"expl_notice" => utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => utf8_normalize($row["expl_bulletin"]),
					"idnot" => utf8_normalize($row["idnot"]),
					"tit" => utf8_normalize($row["tit"]),
				);
				$results[] = $result;
			}
		
			return $results;
		} else {
			return array();
		}
	}
	
//	function listLoansReaders($loan_type, $empr_location_id,$limite_mysql='',$limite_page='') {
//		global $dbh, $msg, $pmb_lecteurs_localises;
//		
////		$empr = new emprunteur($empr_id);
//		
//		switch ($loan_type) {
//			case LIST_LOAN_LATE:
//				break;
//			case LIST_LOAN_CURRENT:
//				break;
//		}
//				
//		$results = array();
//		
//		//REINITIALISATION DE LA REQUETE SQL
//		$sql = "SELECT date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
//		$sql .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
//		$sql .= " IF(pret_retour>=CURDATE(),0,1) as retard, " ;
//		$sql .= " id_empr, empr_nom, empr_prenom, empr_mail, id_empr, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
//		$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
//		$sql .= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
//		$sql .= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
//		$sql .= "        docs_type , pret, empr ";
//		$sql .= "WHERE ";
//		if ($pmb_lecteurs_localises) {
//			if ($empr_location_id!=0) 
//				$sql.= "empr_location='$empr_location_id' AND "; 
//		}
//		$sql.= "expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr ";
//		$sql = $sql.$critere_requete;
//		if ($limite_mysql && $limite_page) {
//			$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
//		}
//		
//		$res = pmb_mysql_query($sql, $dbh);
//		if (!$res)
//			throw new Exception("Not found: Error");
//
//		while ($row = pmb_mysql_fetch_assoc($res)) {
//			$result = array(
//				"aff_pret_date" => utf8_normalize($row["aff_pret_date"]),
//				"aff_pret_retour" => utf8_normalize($row["aff_pret_retour"]),
//				"retard" => utf8_normalize($row["retard"]),
//				"id_empr" => $row["id_empr"],
//				"empr_nom" => utf8_normalize($row["empr_nom"]),
//				"empr_prenom" => utf8_normalize($row["empr_prenom"]),
//				"empr_mail" => utf8_normalize($row["empr_mail"]),
//				"empr_cb" => $row["empr_cb"],
//				"expl_cote" => utf8_normalize($row["expl_cote"]),
//				"expl_cb" => utf8_normalize($row["expl_cb"]),
//				"expl_notice" => utf8_normalize($row["expl_notice"]),
//				"expl_bulletin" => utf8_normalize($row["expl_bulletin"]),
//				"idnot" => utf8_normalize($row["idnot"]),
//				"tit" => utf8_normalize($row["tit"]),
//			);
//			$results[] = $result;
//		}
//	
//		return $results;
//	}
	
	function listLoansGroups($loan_type=0, $limite_mysql='', $limite_page='') {
		global $dbh, $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$results = array();
		
			$critere_requete = "";
			if ($loan_type) {
				switch ($loan_type) {
					case LIST_LOAN_LATE:
						$critere_requete .= "And pret_retour < curdate()";	
						break;
					case LIST_LOAN_CURRENT:
						$critere_requete .= "";
						break;
				}
			}
		
			//REQUETE SQL
			$sql = "SELECT id_groupe, libelle_groupe, resp_groupe, ";
			$sql .= "id_empr, empr_cb, empr_nom, empr_prenom, empr_mail, ";
			$sql .= "pret_idexpl, pret_date, pret_retour, ";
			$sql .= "expl_cote, expl_id, expl_cb, ";
			$sql .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$sql .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$sql .= " IF(pret_retour>=curdate(),0,1) as retard, " ; 
			$sql .= " expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
			$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
			$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
			$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), " ;
			$sql.= "        empr,pret,empr_groupe, groupe "; 
			$sql .= "WHERE pret.pret_idempr = empr.id_empr AND pret.pret_idexpl = exemplaires.expl_id AND empr_groupe.empr_id = empr.id_empr AND groupe.id_groupe = empr_groupe.groupe_id ";
			$sql .= $critere_requete; 
			if ($limite_mysql && $limite_page) {
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
			} 
			// on lance la requête (mysql_query)  
			$res = pmb_mysql_query($sql, $dbh);
	
			if (!$res)
				throw new Exception("Not found: Error");
			
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"id_groupe" => utf8_normalize($row["id_groupe"]),
					"libelle_groupe" => utf8_normalize($row["libelle_groupe"]),
					"resp_groupe" => utf8_normalize($row["resp_groupe"]),
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"empr_mail" => utf8_normalize($row["empr_mail"]),
					"pret_idexpl" => utf8_normalize($row["pret_idexpl"]),
					"pret_date" => utf8_normalize($row["pret_date"]),
					"pret_retour" => utf8_normalize($row["pret_retour"]),
					"expl_cote" => utf8_normalize($row["expl_cote"]),
					"expl_id" => utf8_normalize($row["expl_id"]),
					"expl_cb" => utf8_normalize($row["expl_cb"]),
					"aff_pret_date" => utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => utf8_normalize($row["aff_pret_retour"]),
					"retard" => utf8_normalize($row["retard"]),
					"expl_notice" => utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => utf8_normalize($row["expl_bulletin"]),
					"idnot" => utf8_normalize($row["idnot"]),
					"tit" => utf8_normalize($row["tit"]),
				);
				$results[] = $result;
			}
			return $results;
		} else {
			return array();
		}
	}

	function buildPdfLoansDelayReaders($t_empr, $f_loc=0, $niveau_relance=0) {
		global $ourPDF,$fpdf,$pdflettreretard_1largeur_page, $pdflettreretard_1hauteur_page,$pdflettreretard_1format_page;
		global $pmb_lecteurs_localises,$deflt2docs_location,$pdflettreretard_impression_tri;
		global $empr_sms_activation, $empr_sms_msg_retard;
		global $mailretard_priorite_email,$pmb_gestion_financiere,$pmb_gestion_amende;
		
		$largeur_page=$pdflettreretard_1largeur_page;
		$hauteur_page=$pdflettreretard_1hauteur_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$format_page=$pdflettreretard_1format_page;
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();

		$restrict_localisation="";
		if ($t_empr) {
			$restrict_localisation = " id_empr in (".implode(",",$t_empr).") and "; 
		} 
		if ($pmb_lecteurs_localises) {
			if (!$f_loc) $f_loc = $deflt2docs_location ;
		} else {
			$f_loc = $deflt2docs_location;
		}
		$this->infos_biblio($f_loc);

		// parametre listant les champs de la table empr pour effectuer le tri d'impression des lettres		
		if($pdflettreretard_impression_tri) $order_by= " ORDER BY $pdflettreretard_impression_tri";
		else $order_by= "";

		$rqt="select id_empr, concat(empr_nom,' ',empr_prenom) as  empr_name, empr_cb, empr_mail, empr_tel1, empr_sms, count(pret_idexpl) as empr_nb, $pdflettreretard_impression_tri from empr, pret, exemplaires where $restrict_localisation pret_retour<curdate() and pret_idempr=id_empr  and pret_idexpl=expl_id group by id_empr $order_by";							
		$req=pmb_mysql_query($rqt);
		while ($r = pmb_mysql_fetch_object($req)) {
			if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ($printed==2) $printed=0;
				pmb_mysql_query("update pret set printed=1 where printed=2 and pret_idempr=".$r->id_empr);
				$not_mail=true;
				if ((($mailretard_priorite_email==1)&&($r->empr_mail))&&($niveau_min<3)) $not_mail=false;
				if ((($print_all || !$printed)&&($niveau_min))&&($not_mail)) {
					$niveau_relance=$niveau_min;
					$this->get_texts($niveau_relance);
					lettre_retard_par_lecteur($r->id_empr) ;
					$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
				}
			} else {
				if (!$niveau_relance) $niveau_relance=1;
				$this->get_texts($niveau_relance);
				lettre_retard_par_lecteur($r->id_empr) ;
				$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
			}
			if($r->empr_tel1 && $r->empr_sms && $empr_sms_msg_retard){	
				$res_envoi_sms=send_sms(0, $niveau_relance, $r->empr_tel1,$empr_sms_msg_retard);
			}	
		} // fin while	
		return $ourPDF;	
	}
	
	
	function buildPdfLoansRunningGroup($id_groupe='') {
		global $dbh, $fpdf, $msg, $ourPDF;
		global $class_path, $include_path;
		global $pdflettreretard_1largeur_page, $pdflettreretard_1hauteur_page,$pdflettreretard_1format_page;
		global $pmb_pdf_font, $pmb_pdf_fontfixed,$pmb_hide_biblioinfo_letter;

		if (!$id_groupe)
			throw new Exception("Missing parameter : id_groupe");
		
		$this->get_texts(1);
		
		$largeur_page=$pdflettreretard_1largeur_page;
		$hauteur_page=$pdflettreretard_1hauteur_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$format_page=$pdflettreretard_1format_page;
		// inclusion de la classe de gestion des impressions PDF
		// Definition de la police si pas définie dans les paramètres
		if (!$pmb_pdf_font) $pmb_pdf_font = 'pmb'; 
		if (!$pmb_pdf_fontfixed) $pmb_pdf_fontfixed = 'pmbmono'; 
		define('FPDF_FONTPATH',"$class_path/font/");

		// Démarrage et configuration du pdf
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();
				
		$ourPDF->addPage();
		//$ourPDF->SetMargins(10,10,10);
		$ourPDF->SetLeftMargin(10);
		$ourPDF->SetTopMargin(10);
		
		// paramétrage spécifique à ce document :
		$offsety = 0;
		if(!$pmb_hide_biblioinfo_letter) biblio_info( 10, 10, 1) ;
		groupe_adresse($id_groupe, 150, 10+$offsety, $dbh,true);
		date_edition(10,15+$offsety);

		$ourPDF->SetXY(10,15+$offsety);
//		$ourPDF->setFont($pmb_pdf_font, 'BI', 14);
		$ourPDF->multiCell(190, 20, $msg["prets_en_cours"], 0, 'L', 0);
		$i=0;
		$nb_page=0;
		$indice_page=0;
		$nb_par_page = 21;
		$nb_1ere_page = 19;
		$taille_bloc_expl = 12 ;
		$debut_expl_1er_page=35+$offsety;
		$debut_expl_page=10;

		//requete par rapport à un groupe d'emprunteurs
		$rqt1 = "select empr_id from empr_groupe, empr, pret where groupe_id='".$id_groupe."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
		$req1 = pmb_mysql_query($rqt1);

		while ($data1=pmb_mysql_fetch_array($req1)) {
			$id_empr=$data1['empr_id'];	
			if ($nb_page==0 && $indice_page==$nb_1ere_page) {
				$ourPDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			} elseif (($nb_page>=1) && ((($indice_page-$nb_1ere_page) % $nb_par_page)==0)) { 
				$ourPDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			}
			if ($nb_page==0) $pos_page = $debut_expl_1er_page+$taille_bloc_expl*$indice_page;
			else $pos_page = $debut_expl_page+$taille_bloc_expl*$indice_page;

			lecteur_info($id_empr,10,$pos_page,$dbh, 1, 0);
			$indice_page++;

			//requete par rapport à un emprunteur
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;	
			$req = pmb_mysql_query($rqt);
			
			while ($data = pmb_mysql_fetch_array($req)) {
				if ($nb_page==0 && $indice_page==$nb_1ere_page) {
					$ourPDF->addPage();
					$nb_page++;
					$indice_page = 0 ;
				} elseif (($nb_page>=1) && ((($indice_page-$nb_1ere_page) % $nb_par_page)==0)) { 
					$ourPDF->addPage();
					$nb_page++;
					$indice_page = 0 ;
				}
				if ($nb_page==0) $pos_page = $debut_expl_1er_page+$taille_bloc_expl*$indice_page;
				else $pos_page = $debut_expl_page+$taille_bloc_expl*$indice_page;
				expl_info ($data['expl_cb'],10,$pos_page-5,$dbh, 1, 80);
				$indice_page++;
			}	
		}

		return $ourPDF;
	}
	
	function buildPdfLoansDelayGroup ($groupe_id) {
		global $fpdf,$ourPDF,$pdflettreretard_1largeur_page, $pdflettreretard_1hauteur_page,$pdflettreretard_1format_page;
			
		$this->get_texts(1);

		$largeur_page=$pdflettreretard_1largeur_page;
		$hauteur_page=$pdflettreretard_1hauteur_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$format_page=$pdflettreretard_1format_page;
		
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();

		lettre_retard_par_groupe($groupe_id);
				
		return $ourPDF;
	}
	
	function buildPdfLoansRunningReader($id_empr, $location_biblio) {
		global $dbh, $fpdf, $ourPDF, $msg,$pmb_lecteurs_localises;
		global $pmb_hide_biblioinfo_letter;
		global $biblio_name;
		
		//récupère les informations sur la structure
		$this->infos_biblio($location_biblio);
		
		// liste des prêts et réservations
		// prise en compte du param d'envoi de ticket de prêt électronique si l'utilisateur le veut !
//		if ($empr_electronic_loan_ticket && $param_popup_ticket) {
//			electronic_ticket($id_empr) ;
//			}
		
		// popup d'impression PDF pour fiche lecteur
		// reçoit : id_empr
		// Démarrage et configuration du pdf
		$ourPDF = new $fpdf('P', 'mm', 'A4');
		$ourPDF->Open();
		
		//requete par rapport à un emprunteur
		$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;	
		$req = pmb_mysql_query($rqt);
		$count = pmb_mysql_num_rows($req);
		
		$ourPDF->addPage();
		//$ourPDF->SetMargins(10,10,10);
		$ourPDF->SetLeftMargin(10);
		$ourPDF->SetTopMargin(10);
		// paramétrage spécifique à ce document :
		$offsety = 0;
			
		if(!$pmb_hide_biblioinfo_letter) biblio_info( 10, 10, 1) ;
		$offsety=(ceil($ourPDF->GetStringWidth($biblio_name)/90)-1)*10; //90=largeur de la cell, 10=hauteur d'une ligne
		lecteur_info($id_empr, 90, 10+$offsety, $dbh, 1,1);
		date_edition(10,15+$offsety);
				
		$ourPDF->SetXY (10,22+$offsety);
		$ourPDF->setFont($pmb_pdf_font, 'BI', 14);
		$ourPDF->multiCell(190, 20, $msg["prets_en_cours"]." (".($count).")", 0, 'L', 0);
		$i=0;
		$nb_page=0;
		$nb_par_page = 21;
		$nb_1ere_page = 19;
		$taille_bloc = 12 ;
		
		while ($data = pmb_mysql_fetch_array($req)) {
			if ($nb_page==0 && $i<$nb_1ere_page) {
				$pos_page = 35+$offsety+$taille_bloc*$i;
				}
			if (($nb_page==0 && $i==$nb_1ere_page) || ((($i-$nb_1ere_page) % $nb_par_page)==0)) {
				$ourPDF->addPage();
				$nb_page++;
				}
			if ($nb_page>=1) {
				$pos_page = 10+($taille_bloc*($i-$nb_1ere_page-($nb_page-1)*$nb_par_page));
				}
			expl_info ($data['expl_cb'],10,$pos_page,$dbh, 1, 80);
			$i++;
			}

		pmb_mysql_free_result($req);
		
		return $ourPDF;
	}
	
	
	function buildPdfLoansDelayReader($id_empr, $biblio_location=0, $niveau_relance=0) {
		global $ourPDF,$fpdf,$pdflettreretard_1largeur_page, $pdflettreretard_1hauteur_page,$pdflettreretard_1format_page;
		global $pmb_lecteurs_localises;

		//récupère les informations sur la structure
		$this->infos_biblio($biblio_location);

		if (!$niveau_relance) $niveau_relance=1;
		$this->get_texts($niveau_relance);

		$largeur_page=$pdflettreretard_1largeur_page;
		$hauteur_page=$pdflettreretard_1hauteur_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$format_page=$pdflettreretard_1format_page;
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();
		lettre_retard_par_lecteur($id_empr) ;

		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
		if($empr_sms_msg_retard) {
			$rqt="select concat(empr_nom,' ',empr_prenom) as  empr_name, empr_mail, empr_tel1, empr_sms from empr where id_empr='".$id_empr."' and empr_tel1!='' and empr_sms=1";							
			$req=pmb_mysql_query($rqt);
			if ($r = pmb_mysql_fetch_object($req)) {
				if ($r->empr_tel1 && $r->empr_sms) {
					$res_envoi_sms=send_sms(0, $niveau_relance, $r->empr_tel1,$empr_sms_msg_retard);
				}
			}
		}
				
		return $ourPDF;
	}
	
	/**
	 * 
	 * Envoi de mail auto
	 * @param $type_send READER=1,GROUP=2
	 * @param $ident
	 */
	function sendMailLoansRunning($type_send, $ident, $location_biblio) {
		global $dbh, $msg, $pmb_lecteurs_localises;
		global $mailretard_1fdp,$biblio_name,$biblio_email,$PMBuseremailbcc;
		
		$this->infos_biblio($location_biblio);
		
		if (!$relance) $relance=1;
		// l'objet du mail
		$objet = $msg["prets_en_cours"];
		
		//Date de l'édition
		$date_edition=$msg['fpdf_edite']." ".formatdate(date("Y-m-d",time()));
		
		// la formule de politesse du bas (le signataire)
		$formule = $mailretard_1fdp;
		
		$texte_mail=$objet."\r\n";
		$texte_mail.=$date_edition."\r\n\r\n";
		
//		if ($id_groupe) {
//			//requete par rapport à un groupe d'emprunteurs
//			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$id_groupe."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
//			$req1 = pmb_mysql_query($rqt1) or die($msg['err_sql'].'<br />'.$rqt1.'<br />'.pmb_mysql_error());
//		}
//		
//		if ($id_empr) {
//			//requete par rapport à un emprunteur
//			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where id_empr='".$id_empr."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
//			$req1 = pmb_mysql_query($rqt1) or die($msg['err_sql'].'<br />'.$rqt1.'<br />'.pmb_mysql_error());
//		}
		if ($ident) {
			if ($type_send == 1) {
				//requete par rapport à un emprunteur
				$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where id_empr='".$ident."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
				$req1 = pmb_mysql_query($rqt1);		
			} else if ($type_send == 2) {
				//requete par rapport à un groupe d'emprunteurs
				$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$ident."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
				$req1 = pmb_mysql_query($rqt1);
			}
		}
		while ($data1=pmb_mysql_fetch_array($req1)) {
			$id_empr=$data1['id_empr'];	
			$texte_mail.=$data1['empr_nom']." ".$data1['empr_prenom']."\r\n\r\n";
	
			//Récupération des exemplaires
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;
			$req = pmb_mysql_query($rqt); 
	
			$i=0;
			while ($data = pmb_mysql_fetch_array($req)) {
		
				/* Récupération des infos exemplaires et prêt */
				$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
				$requete.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
				$requete.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
				$requete.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ; 
				$requete.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
				$requete.= "WHERE expl_cb='".$data['expl_cb']."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
		
				$res = pmb_mysql_query($requete,$dbh);
				$expl = pmb_mysql_fetch_object($res);
		
				$responsabilites=array() ;
				$header_aut = "" ;
				$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
				$as = array_search ("0", $responsabilites["responsabilites"]) ;
				if ($as!== FALSE && $as!== NULL) {
					$auteur_0 = $responsabilites["auteurs"][$as] ;
					$auteur = new auteur($auteur_0["id"]);
					$header_aut .= $auteur->isbd_entry;
					} else {
						$aut1_libelle=array();
						$as = array_keys ($responsabilites["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsabilites["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->isbd_entry;
						}
				
						$header_aut .= implode (", ",$aut1_libelle) ;
					}
				$header_aut ? $auteur=" / ".$header_aut : $auteur="";
		
				// récupération du titre de série
				if ($expl->tparent_id && $expl->m_id) {
					$parent = new serie($expl->tparent_id);
					$tit_serie = $parent->name;
					if($expl->tnvol)
						$tit_serie .= ', '.$expl->tnvol;
					}
				if($tit_serie) {
					$expl->tit = $tit_serie.'. '.$expl->tit;
				}
	
				$texte_mail.=$expl->tit.$auteur."\r\n";
				$texte_mail.="    -".$msg[fpdf_date_pret]." : ".$expl->aff_pret_date." ".$msg[fpdf_retour_prevu]." : ".$expl->aff_pret_retour."\r\n";
				$texte_mail.="    -".$expl->location_libelle.": ".$expl->section_libelle." (".$expl->expl_cb.")\r\n\r\n";
				$i++;
			}
		}
		
		$texte_mail.=$formule."\r\n\r\n".mail_bloc_adresse();
		
		/* Récupération du nom, prénom et mail de l'utilisateur */
		$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr=$id_empr";
		$res=pmb_mysql_query($requete);
		$coords=pmb_mysql_fetch_object($res);
		$headers .= "Content-type: text/plain; charset=".$charset."\n";
		$res_envoi=mailpmb($coords->empr_prenom." ".$coords->empr_nom, $coords->empr_mail, $objet,$texte_mail, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);
	
		if ($res_envoi) return sprintf($msg["mail_retard_succeed"],$coords->empr_mail);
		else return sprintf($msg["mail_retard_failed"],$coords->empr_mail);
	
	}
	
	/**
	 * 
	 * Envoi de mail auto
	 * @param $type_send READER=1,GROUP=2
	 * @param $ident
	 */
	function sendMailLoansDelay($type_send, $ident) {
		/*Quasi-identique à sendMailLoansRunning */
		
		return "";
	}
	
	function get_texts($relance) {
		global $fdp, $after_list,$before_recouvrement,$after_recouvrement,$limite_after_list, $before_list;
		global $madame_monsieur, $nb_1ere_page, $nb_par_page, $taille_bloc_expl, $debut_expl_1er_page,$debut_expl_page;
		global $marge_page_gauche, $marge_page_droite, $largeur_page, $hauteur_page,$format_page;
		global $biblio_name, $biblio_email,$biblio_phone;
//		global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_country,$biblio_website;
//		global $biblio_logo, $txt_biblio_info,$biblio_state ;
		global $pmb_lecteurs_localises;

		$var = "pdflettreretard_".$relance."fdp";
		global $$var;
		eval ("\$fdp=\"".$$var."\";");
	
		// le texte après la liste des ouvrages en retard
		$var = "pdflettreretard_".$relance."after_list";
		global $$var;
		eval ("\$after_list=\"".$$var."\";");
		
		// Le texte avant la liste des ouvrages qui passeront en recouvrement
		$var = "pdflettreretard_".$relance."before_recouvrement";
		global $$var;
		eval ("\$before_recouvrement=\"".$$var."\";");
		
		// Le texte après la liste des ouvrages qui passeront en recouvrement
		$var = "pdflettreretard_".$relance."after_recouvrement";
		global $$var;
		eval ("\$after_recouvrement=\"".$$var."\";");
			
		
		// la position verticale limite du texte after_liste (si >, saut de page et impression)
		$var = "pdflettreretard_".$relance."limite_after_list";
		global $$var;
		$limite_after_list = $$var;
				
		// le texte avant la liste des ouvrges en retard
		$var = "pdflettreretard_".$relance."before_list";
		global $$var;
		eval ("\$before_list=\"".$$var."\";");
		
		// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
		$var = "pdflettreretard_".$relance."madame_monsieur";
		global $$var;
		eval ("\$madame_monsieur=\"".$$var."\";");
		
		// le nombre de blocs expl à imprimer sur la première page
		$var = "pdflettreretard_".$relance."nb_1ere_page";
		global $$var;
		$nb_1ere_page = $$var;
		
		// le nombre de blocs expl à imprimer sur les pages suivantes
		$var = "pdflettreretard_".$relance."nb_par_page";
		global $$var;
		$nb_par_page = $$var;
		
		// la taille d'un bloc expl en retard affiché
		$var = "pdflettreretard_".$relance."taille_bloc_expl";
		global $$var;
		$taille_bloc_expl = $$var;
		
		// la position verticale du premier bloc expl sur la première page
		$var = "pdflettreretard_".$relance."debut_expl_1er_page";
		global $$var;
		$debut_expl_1er_page = $$var;
		
		// la position verticale du premier bloc expl sur les pages suivantes
		$var = "pdflettreretard_".$relance."debut_expl_page";
		global $$var;
		$debut_expl_page = $$var;
		
		// la marge gauche des pages
		$var = "pdflettreretard_".$relance."marge_page_gauche";
		global $$var;
		$marge_page_gauche = $$var;
		
		// la marge droite des pages
		$var = "pdflettreretard_".$relance."marge_page_droite";
		global $$var;
		$marge_page_droite = $$var;
		
		// la largeur des pages
		$var = "pdflettreretard_1largeur_page";
		global $$var;
		$largeur_page = $$var;
		
		// la hauteur des pages
		$var = "pdflettreretard_1hauteur_page";
		global $$var;
		$hauteur_page = $$var;
		
		// le format des pages
		$var = "pdflettreretard_1format_page";
		global $$var;
		$format_page = $$var;
	}
	
	function infos_biblio($location_biblio=0) {
		global $dbh,$pmb_lecteurs_localises;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;
		
		if ($pmb_lecteurs_localises) {
			if (!$location_biblio) {
				global $deflt2docs_location;
				$location_biblio = $deflt2docs_location;
			}
			$query = "select name, adr1,adr2,cp,town,state,country,phone,email,website,logo from docs_location where idlocation=".$location_biblio;
			$res = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($res) == 1) {
				$row = pmb_mysql_fetch_object($res);
				$biblio_name = $row->name;
				$biblio_adr1 = $row->adr1;
				$biblio_adr2 = $row->adr2;
				$biblio_cp = $row->cp;
				$biblio_town = $row->town;
				$biblio_state = $row->state;
				$biblio_country = $row->country;
				$biblio_phone = $row->phone;
				$biblio_email = $row->email;
				$biblio_website = $row->website;
				$biblio_logo = $row->logo;
			}	
		} else {
			/*** Informations provenant des paramètres généraux - on ne parle donc pas de multi-localisations **/
			// nom de la structure
			$var = "opac_biblio_name";
			global $$var;
			eval ("\$biblio_name=\"".$$var."\";");
		
			// logo de la structure
			$var = "opac_logo";
			global $$var;
			eval ("\$biblio_logo=\"".$$var."\";");
		
			// adresse principale
			$var = "opac_biblio_adr1";
			global $$var;
			eval ("\$biblio_adr1=\"".$$var."\";");
			
			// adresse secondaire
			$var = "opac_biblio_adr2";
			global $$var;
			eval ("\$biblio_adr2=\"".$$var."\";");
			
			// code postal
			$var = "opac_biblio_cp";
			global $$var;
			eval ("\$biblio_cp=\"".$$var."\";");
			
			// ville
			$var = "opac_biblio_town";
			global $$var;
			eval ("\$biblio_town=\"".$$var."\";");
			
//			// Etat
			$var = "opac_biblio_state";
			global $$var;
			eval ("\$biblio_state=\"".$$var."\";");
			
			// pays
			$var = "opac_biblio_country";
			global $$var;
			eval ("\$biblio_country=\"".$$var."\";");
			
			// telephone
			$var = "opac_biblio_phone";
			global $$var;
			eval ("\$biblio_phone=\"".$$var."\";");
			
			// adresse mail
			$var = "opac_biblio_email";
			global $$var;
			eval ("\$biblio_email=\"".$$var."\";");
			
			//site web
			$var = "opac_biblio_website";
			global $$var;
			eval ("\$biblio_website=\"".$$var."\";");
		}
	}
}

?>