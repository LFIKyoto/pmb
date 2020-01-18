<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_scd_lyon3.inc.php,v 1.16 2019-08-01 13:16:34 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path; //N�cessaire pour certaines inclusions
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
global $thesaurus_defaut;


// Attention, dans le multithesaurus, on ins�re les cat�gories
// dans le thesaurus par defaut
		$thes = new thesaurus($thesaurus_defaut);
		$rac = $thes->num_noeud_racine;
    

// DEBUT param�trage propre � la base de donn�es d'importation :
// r�cup�ration du 606 : r�cup en cat�gories en essayant de classer :
//	les sujets sous le terme "Recherche par terme" 
	$id_rech_theme = categories::searchLibelle('Recherche par terme', $thes->id_thesaurus, 'fr_FR');
    if (!$id_rech_theme) $id_rech_theme = create_categ($rac, 'Recherche par terme', strip_empty_words('Recherche par terme', 'fr_FR'));
		
//	les pr�cisions g�ographiques sous le terme "Recherche g�ographique" 
	$id_rech_geo = categories::searchLibelle('Recherche g�ographique', $thes->id_thesaurus, 'fr_FR');
    if (!$id_rech_geo) 	$id_rech_geo = create_categ($rac, 'Recherche g�ographique', strip_empty_words('Recherche g�ographique', 'fr_FR'));

//	les pr�cisions de p�riode sous le terme "Recherche chronologique" 
	$id_rech_chrono = categories::searchLibelle('Recherche chronologique', $thes->id_thesaurus, 'fr_FR');
    if (!$id_rech_chrono) $id_rech_chrono = create_categ($rac, 'Recherche chronologique', strip_empty_words('Recherche chronologique', 'fr_FR'));

// FIN param�trage 

function recup_noticeunimarc_suite($notice) {
} // fin recup_noticeunimarc_suite = fin r�cup�ration des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $dbh ;
	global $notice_id ;
	
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z ;
	global $id_rech_theme, $id_rech_geo, $id_rech_chrono ; 
	global $thesaurus_defaut;
	global $thes;
	
	// les champs $606 sont stock�s dans les cat�gories
	//	$a >> en sous cat�gories de $id_rech_theme
	// 		$j en compl�ment de $a
	//		$x en sous cat�gories de $a
	// $y >> en sous cat�gories de $id_rech_geo
	// $z >> en sous cat�gories de $id_rech_chrono
	// TRAITEMENT :
	// pour $a=0 � size_of $info_606_a
	//	pour $j=0 � size_of $info_606_j[$a]
	//		concat�ner $libelle_j .= $info_606_j[$a][$j]
	//	$libelle_final = $info_606_a[0]." ** ".$libelle_j
	//	Rechercher si l'enregistrement existe d�j� dans categories = 
	//	$categid = categories::searchLibelle(addslashes($libelle_final), $thesaurus_defaut, 'fr_FR', $id_rech_theme)

	//	Cr�er si besoin et r�cup�rer l'id $categid_a
	//	$categid_parent =  $categid_a
	//	pour $x=0 � size_of $info_606_x[$a]
	//		Rechercher si l'enregistrement existe d�j� dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_x[$a][$x]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Cr�er si besoin et r�cup�rer l'id $categid_parent
	//
	//	$categid_parent =  $id_rech_geo
	//	pour $y=0 � size_of $info_606_y[$a]
	//		Rechercher si l'enregistrement existe d�j� dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_y[$a][$y]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Cr�er si besoin et r�cup�rer l'id $categid_parent
	//
	//	$categid_parent =  $id_rech_chrono
	//	pour $y=0 � size_of $info_606_z[$a]
	//		Rechercher si l'enregistrement existe d�j� dans categories = 
	//	$categid = categories::searchLibelle(addslashes($info_606_z[$a][$y]]), $thesaurus_defaut, 'fr_FR', $categ_parent)

	//		Cr�er si besoin et r�cup�rer l'id $categid_parent
	//
	for ($a=0; $a<count($info_606_a); $a++) {
		for ($j=0; $j<count($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= $info_606_j[$a][$j] ;
				else $libelle_j .= " ** ".$info_606_j[$a][$j] ;
		}
		if (!$libelle_j) $libelle_final = $info_606_a[$a][0] ;
			else $libelle_final = $info_606_a[$a][0]." ** ".$libelle_j ;
		if (!$libelle_final) break ; 
		$res_a = categories::searchLibelle(addslashes($libelle_final), $thesaurus_defaut, 'fr_FR', $id_rech_theme);
		if ($res_a) {
			$categid_a = $res_a;
		} else {
			$categid_a = create_categ($id_rech_theme, $libelle_final, strip_empty_words($libelle_final, 'fr_FR'));
		}
		// r�cup des sous-categ en cascade sous $a
		$categ_parent =  $categid_a ;
		for ($x=0 ; $x < count($info_606_x[$a]) ; $x++) {
			$res_x = categories::searchLibelle(addslashes($info_606_x[$a][$x]), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if ($res_x) {
				$categ_parent = $res_x;
			} else {
				$categ_parent = create_categ($categ_parent, $info_606_x[$a][$x], strip_empty_words($info_606_x[$a][$x], 'fr_FR'));
			}
		} // fin r�cup des $x en cascade sous l'id de la cat�gorie 606$a
		
		if ($categ_parent != $id_rech_theme) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;

			$res_ajout = @pmb_mysql_query($rqt_ajout, $dbh);
		}
				
		// r�cup des categ g�o � loger sous la categ g�o principale
		$categ_parent =  $id_rech_geo ;
		for ($y=0 ; $y < count($info_606_y[$a]) ; $y++) {
			$res_y = categories::searchLibelle(addslashes($info_606_y[$a][$y]), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if($res_y) {
				$categ_parent = $res_y;
			} else {
				$categ_parent = create_categ($categ_parent, $info_606_y[$a][$y], strip_empty_words($info_606_y[$a][$y], 'fr_FR'));
			}
		} // fin r�cup des $y en cascade sous l'id de la cat�gorie principale th�me g�o
		
		if ($categ_parent != $id_rech_geo) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;
			$res_ajout = @pmb_mysql_query($rqt_ajout, $dbh);
		}
		
		// r�cup des categ chrono � loger sous la categ chrono principale
		$categ_parent =  $id_rech_chrono ;
		for ($z=0 ; $z < count($info_606_z[$a]) ; $z++) {
			$res_z = categories::searchLibelle(addslashes($info_606_z[$a][$z]), $thesaurus_defaut, 'fr_FR', $categ_parent);
			if ($res_z) {
				$categ_parent = $res_z;
			} else {
				$categ_parent = create_categ($categ_parent, $info_606_z[$a][$z], strip_empty_words($info_606_z[$a][$z], 'fr_FR'));
			}
		} // fin r�cup des $z en cascade sous l'id de la cat�gorie principale th�me chrono
		
		if ($categ_parent != $id_rech_chrono) {
			// insertion dans la table notices_categories
			$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ_parent."' " ;

			$res_ajout = @pmb_mysql_query($rqt_ajout, $dbh);
		}
	}
	
} // fin import_new_notice_suite


function create_categ($num_parent, $libelle, $index) {
	
	global $thes;
	$n = new noeuds();
	$n->num_thesaurus = $thes->id_thesaurus;
	$n->num_parent = $num_parent;
	$n->save();
	
	$c = new categories($n->id_noeud, 'fr_FR');
	$c->libelle_categorie = $libelle;
	$c->index_categorie = $index;
	$c->save();
	
	return $n->id_noeud;
}		

			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $msg, $dbh ;
	
	global $prix, $notice_id, $info_852, $tdoc_codage, $book_lender_id, 
		$sdoc_codage, $book_statut_id, $locdoc_codage, $statisdoc_codage,
		$cote_mandatory, $book_location_id ;
	
	// d�buggage
	/*	echo "<pre>" ;
	print_r($info_852) ;
	echo "</pre>" ;
	exit ;
	 */ 

	// lu en 010$d de la notice
	$price = $prix[0];
	// la zone 852 est r�p�table
	for ($nb_expl = 0; $nb_expl < count($info_852); $nb_expl++) {
		if ($info_852[$nb_expl]['b']=="IDC") {
			/* pr�paration du tableau � passer � la m�thode */
			/* RAZ expl */
			$expl = array();

			$expl['cb'] 	    = $info_852[$nb_expl]['p'];
			$expl['cote'] 	    = $info_852[$nb_expl]['h'];
			$expl['notice']     = $notice_id ;

			// $expl['section']    = $info_852[$nb_expl]['h']; � chercher dans docs_section
			$data_doc=array();
			
			$pos_section = strpos($info_852[$nb_expl]['h']," ") ;
			if ($pos_section) $section = substr($info_852[$nb_expl]['h'],0,$pos_section) ;
				else $section = "XXX" ;
			
			if ($section=="XXX") $data_doc['section_libelle'] = "SECTION INDETERMINEE";
				else $data_doc['section_libelle'] = "Libell� pour ".$section;
			$data_doc['sdoc_codage_import'] = $section;
			if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
				else $data_doc['sdoc_owner'] = 0 ;
			$expl['section'] = docs_section::import($data_doc);

			// $expl['typdoc'] 
			$data_doc=array();
			$data_doc['tdoc_libelle'] = "Type doc ind�termin�";
			$data_doc['duree_pret'] = 15 ; /* valeur par d�faut */
			$data_doc['tdoc_codage_import'] = "XXX" ;
			if ($tdoc_codage) $data_doc['tdoc_owner'] = $book_lender_id ;
				else $data_doc['tdoc_owner'] = 0 ;
			$expl['typdoc'] = docs_type::import($data_doc);

			$expl['statut'] = $book_statut_id;
	
			$expl['location'] = $book_location_id;
		
			// $expl['codestat']
			$data_doc=array();
			$data_doc['codestat_libelle'] = "AUCUN CODE STATISTIQUE";
			$data_doc['statisdoc_codage_import'] = "XXX" ;
			if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $book_lender_id ;
				else $data_doc['statisdoc_owner'] = 0 ;
			$expl['codestat'] = docs_codestat::import($data_doc);
		
			$expl['note']       = "" ;
			$expl['prix']       = $price;
			$expl['expl_owner'] = $book_lender_id ;
			$expl['cote_mandatory'] = $cote_mandatory ;
		
			$expl_id = exemplaire::import($expl);
			if ($expl_id == 0) {
				$nb_expl_ignores++;
				}
                      	
			}
		
		} // fin for
	} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction sp�cifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}