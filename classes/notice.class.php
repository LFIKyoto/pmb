<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.class.php,v 1.201.2.4 2015-12-04 14:25:09 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


// classe de gestion des notices
if ( ! defined( 'NOTICE_CLASS' ) ) {
  define( 'NOTICE_CLASS', 1 );
  
  if (!defined( 'TYPE_RECORD' )) define('TYPE_RECORD',11);
  	
	require_once("$class_path/author.class.php");
	require_once("$class_path/marc_table.class.php");
	require_once("$class_path/category.class.php");
	require_once("$class_path/serie.class.php");
	require_once("$class_path/indexint.class.php");
//	require_once("$class_path/tu_notice.class.php");
	require_once($class_path."/parametres_perso.class.php");
	require_once($class_path."/audit.class.php");
	include_once($include_path."/notice_authors.inc.php");
	include_once($include_path."/notice_categories.inc.php");
	require_once($class_path."/thesaurus.class.php");
	require_once($class_path."/noeuds.class.php");
	require_once($include_path."/parser.inc.php");
	require_once($include_path."/rss_func.inc.php");	
	require_once("$class_path/acces.class.php");
	require_once($class_path."/marc_table.class.php");
	require_once($include_path."/misc.inc.php");	
	
	require_once($class_path."/double_metaphone.class.php");
	require_once($class_path."/stemming.class.php");
	require_once($class_path."/aut_pperso.class.php");
	require_once($class_path."/synchro_rdf.class.php");
	require_once($class_path."/index_concept.class.php");
	require_once($class_path."/authperso_notice.class.php");
	require_once($class_path."/map/map_edition_controler.class.php");
	require_once($class_path."/map_info.class.php");	
	require_once($class_path."/nomenclature/nomenclature_record_ui.class.php");
	
	require_once($class_path."/tu_notice.class.php");
	require_once($class_path."/titre_uniforme.class.php");
	
	class notice {
	
		// proprietes
		var $libelle_form = '';
		var $id = 0;
		var $duplicate_from_id = 0;
		var $tit1 = '';			// titre propre
		var $tit2 = '';			// titre propre 2
		var $tit3 = '';			// titre parallele
		var $tit4 = '';			// complement du titre
		var $tparent_id = '';		// id du titre parent
		var $tparent = '';		// libelle du titre parent
		var $tnvol = '';		// numero de partie
		var $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  // les auteurs
		var $ed1_id = '';		// id editeur 1
		var $ed1 ='';			// libelle editeur 1
		var $coll_id = '';		// id collection
		var $coll = '';			// libelle collection
		var $subcoll_id = '';		// id sous collection
		var $subcoll = '';		// libelle sous collection
		var $year = '';			// annee de publication
		var $nocoll = '';		// no. dans la collection
		var $mention_edition = '';	// mention d'edition (1ere, deuxieme...)
		var $ed2_id = '';		// id editeur 2
		var $ed2 ='';			// libelle editeur 2
		var $code = '';			// ISBN, code barre commercial ou no. commercial
		var $npages = '';		// importance materielle (nombre de pages, d'elements...)
		var $ill = '';			// mention d'illustration
		var $size = '';			// format
		var $prix = '';			// prix du document
		var $accomp = '';		// materiel d'accompagnement
		var $n_gen = '';		// note generale
		var $n_contenu = '';		// note de contenu
		var $n_resume = '';		// resume/extrait
		var $categories =	array();// les categories
		var $indexint = 0;		// indexation interne
		var $index_l = '';		// indexation libre
		var $langues = array();
		var $languesorg = array();
		var $lien = '';			// URL de la ressource electronique associee
		var $eformat = '';		// format de la ressource electronique associee
		var $ok = 1;
		var $type_doc = '';
		var $biblio_level = 'm';	// niveau bibliographique
		var $hierar_level = '0';	// niveau hierarchique
		var $action = './catalog.php?categ=update&id=';
		var $link_annul = './catalog.php';
		var $statut = 0 ; // statut 
		var $commentaire_gestion = '' ; // commentaire de gestion 
		var $thumbnail_url = '' ;
		var $notice_link=array();
		var $date_parution;
		var $is_new=0; // nouveauté
		var $date_is_new="0000-00-00 00:00:00"; // date nouveauté
		var $create_date="0000-00-00 00:00:00"; // date création
		var $update_date="0000-00-00 00:00:00"; // date modification
		
		// methodes

		// constructeur
		function notice($id=0, $cb='') {
			global $dbh;
			global $msg;
			global $include_path, $class_path ;
			global $deflt_notice_is_new;
			
			//On nettoie la variable de liens entre notices
			$this->notice_link=array();
			
			if($id) {
				$fonction = new marc_list('function');

				$this->id = $id;
				$this->libelle_form = $msg[278];  // libelle du form : modification d'une notice

				$requete = "SELECT * FROM notices WHERE notice_id='$id' LIMIT 1 ";
				$result = @pmb_mysql_query($requete, $dbh);

				if($result) {
					$notice = pmb_mysql_fetch_object($result);
		
					$this->type_doc = $notice->typdoc;				// type du document
					$this->tit1		= $notice->tit1;				// titre propre
					$this->tit2		= $notice->tit2;				// titre propre 2
					$this->tit3		= $notice->tit3;				// titre parallele
					$this->tit4		= $notice->tit4;				// complement du titre
					$this->tparent_id	= $notice->tparent_id;				// id du titre parent

					// libelle du titre parent
					if($this->tparent_id) {
						$serie = new serie($this->tparent_id);
						$this->tparent = $serie->name;
					} else {
						$this->tparent 		= '';
					}

					$this->tnvol		= $notice->tnvol;				// numero de partie

					$this->responsabilites = get_notice_authors($this->id) ;
					$this->subcoll_id 	= $notice->subcoll_id;				// id sous collection
					$this->coll_id 		= $notice->coll_id;				// id collection
					$this->ed1_id		= $notice->ed1_id	;			// id editeur 1

					require_once("$class_path/editor.class.php");

					if($this->subcoll_id) {
						require_once("$class_path/subcollection.class.php");
						require_once("$class_path/collection.class.php");
						$collection = new subcollection($this->subcoll_id);
						$this->subcoll = $collection->name;
					}
		
					if($this->coll_id) {
						require_once("$class_path/collection.class.php");
						$collection = new collection($this->coll_id);
						$this->coll = $collection->name;
					}
		
					if($this->ed1_id) {
						$editeur = new editeur($this->ed1_id);
						$this->ed1 = $editeur->display;
					}
		
					$this->year 		= $notice->year;				// annee de publication
					$this->nocoll		= $notice->nocoll;				// no. dans la collection
					$this->mention_edition		= $notice->mention_edition;	// mention d'edition (1ere, deuxieme...)
					$this->ed2_id		= $notice->ed2_id;				// id editeur 2
		
					if($this->ed2_id) {		// libelle editeur 2
						$editeur = new editeur($this->ed2_id);
						$this->ed2 = $editeur->display;
					}
		
					$this->code		= $notice->code;				// ISBN, code barre commercial ou no. commercial
		
					$this->npages		= $notice->npages;				// importance materielle (nombre de pages, d'elements...)
					$this->ill		= $notice->ill;					// mention d'illustration
					$this->size		= $notice->size;				// format
					$this->prix		= $notice->prix;				// Prix du document
					$this->accomp		= $notice->accomp;				// materiel d'accompagnement
		
					$this->n_gen		= $notice->n_gen;				// note generale
					$this->n_contenu	= $notice->n_contenu;				// note de contenu
					$this->n_resume		= $notice->n_resume;				// resume/extrait
		
					$this->categories = get_notice_categories($this->id) ;
		
					$this->indexint		= $notice->indexint;				// indexation interne
					$this->index_l		= $notice->index_l;				// indexation libre
		
					$this->langues	= get_notice_langues($this->id, 0) ;	// langues de la publication
					$this->languesorg	= get_notice_langues($this->id, 1) ; // langues originales
		
					$this->lien	= $notice->lien;				// URL de la ressource electronique associee
					$this->eformat	= $notice->eformat;				// format de la ressource electronique associee
					$this->biblio_level = $notice->niveau_biblio;   	    	// niveau bibliographique
					$this->hierar_level = $notice->niveau_hierar;       		// niveau hierarchique
					$this->statut = $notice->statut;
					$this->date_parution = notice::get_date_parution($notice->year);					
					$this->indexation_lang = $notice->indexation_lang;
					
					$this->is_new = $notice->notice_is_new;
					$this->date_is_new = $notice->notice_date_is_new;
					
					//liens vers autres notices
					$requete="SELECT * FROM notices_relations WHERE num_notice=".$this->id." OR linked_notice=".$this->id." order by rank";
					$result_rel=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($result_rel)) {
						$i=0;
						while (($r_rel=pmb_mysql_fetch_object($result_rel))) {
							if($r_rel->linked_notice==$this->id){
								//notice en cours est notice fille
								$this->notice_link['down'][$i]['relation_direction']='down';
								$this->notice_link['down'][$i]['id_notice']=$r_rel->num_notice;
								$this->notice_link['down'][$i]['title_notice']=$this->get_notice_title($r_rel->num_notice);
								$this->notice_link['down'][$i]['rank']=$r_rel->rank;
								$this->notice_link['down'][$i]['relation_type']=$r_rel->relation_type;
								
							}elseif($r_rel->num_notice==$this->id){
								//notice en cours est notice mere
								$this->notice_link['up'][$i]['relation_direction']='up';
								$this->notice_link['up'][$i]['id_notice']=$r_rel->linked_notice;
								$this->notice_link['up'][$i]['title_notice']=$this->get_notice_title($r_rel->linked_notice);
								$this->notice_link['up'][$i]['rank']=$r_rel->rank;
								$this->notice_link['up'][$i]['relation_type']=$r_rel->relation_type;
							}
							$i++;
						}
					}
					 
					$this->commentaire_gestion = $notice->commentaire_gestion;
					$this->thumbnail_url = $notice->thumbnail_url; 

					$this->create_date = $notice->create_date;
					$this->update_date = $notice->update_date;						
				} else {
					require_once("$include_path/user_error.inc.php");
					error_message("", $msg[280], 1, "./catalog.php");
					$this->ok = 0;
				}
				return;
			} else {
		    	// initialisation des valeurs (vides)
				$this->libelle_form = $msg[270];  // libelle du form : creation d'une notice
				$this->id = 0;
				$this->code = $cb;
				// initialisation avec les parametres du user :
				global $value_deflt_lang, $value_deflt_relation ;
				if ($value_deflt_lang) {
					$lang = new marc_list('lang');
					$this->langues[] = array( 
						'lang_code' => $value_deflt_lang,
						'langue' => $lang->table[$value_deflt_lang]
						) ;
				}
				global $deflt_notice_statut ;
				if ($deflt_notice_statut) $this->statut = $deflt_notice_statut;
					else $this->statut = 1;
				
				global $xmlta_doctype ;
				$this->type_doc = $xmlta_doctype ;
				
				global $notice_parent;
				//relation montante ou descendante
				global $notice_parent_direction;
				if ($notice_parent) {
					
					if(!$notice_parent_direction){
						// Si pas de sens, on force a relation descendante
						$notice_parent_direction='down';
					}
					$this->notice_link[$notice_parent_direction][0]['relation_direction']=$notice_parent_direction;
					$this->notice_link[$notice_parent_direction][0]['id_notice']=$notice_parent;
					$this->notice_link[$notice_parent_direction][0]['title_notice']=$this->get_notice_title($notice_parent);
					$this->notice_link[$notice_parent_direction][0]['rank']=1;
					//Recherche d'un type plausible
					$requete="SELECT relation_type FROM notices_relations WHERE num_notice='$notice_parent' ORDER BY rank DESC LIMIT 1";
					$resultat=pmb_mysql_query($requete);
					if (@pmb_mysql_num_rows($resultat)) {
						$this->notice_link[$notice_parent_direction][0]['relation_type']=pmb_mysql_result($resultat,0,0);
					}elseif(preg_match('/'.$notice_parent_direction.'$/',$value_deflt_relation)){
						$this->notice_link[$notice_parent_direction][0]['relation_type']=$value_deflt_relation ;
					}else{
						/*
						 * Comment forcer le sens ?
						 * peut-etre l'inverse de la valeur par defaut si celle-ci ne va pas dans le bon sens ...
						 */
					}
				}
				$this->is_new = $deflt_notice_is_new;
				// penser au test d'existence de la notice sur code-barre
				return;
			}
		}
				
		// Donne l'id de la notice par son isbn 
		static function get_notice_id_from_cb($code) {

			if(!$code) return 0;
			$isbn = traite_code_isbn($code);
			
			if(isISBN10($isbn)) {
				$isbn13 = formatISBN($isbn,13);
				$isbn10 = $isbn;
			} elseif (isISBN13($isbn)) {
				$isbn10 = formatISBN($isbn,10);
				$isbn13 = $isbn;				
			} else {
				// ce n'est pas un code au format isbn
				$isbn10=$code;
			}
					
			$requete = "SELECT notice_id FROM notices WHERE ( code='$isbn10' or code='$isbn13') and code !='' LIMIT 1 ";						
			if(($result = pmb_mysql_query($requete))) {
				if (pmb_mysql_num_rows($result)) {
					$notice = pmb_mysql_fetch_object($result);
					return($notice->notice_id);
				}	
			}
			return 0;
		}
		
		//Récupération d'un titre de notice
		function get_notice_title($notice_id) {
			$requete="select serie_name, tnvol, tit1, code from notices left join series on serie_id=tparent_id where notice_id=".$notice_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				return ($r->serie_name?$r->serie_name." ":"").($r->tnvol?$r->tnvol." ":"").$r->tit1.($r->code?" (".$r->code.")":"");
			}
			return '';
		}
		
		//Récupérer une date au format AAAA-MM-JJ
		static function get_date_parution($annee) {
			return detectFormatDate($annee);
		}
		
		// affichage du form associe
		function show_form() {
			
			global $msg;
			global $charset;
			global $lang;
			global $include_path, $class_path;
			global $current_module ;
			global $pmb_type_audit,$select_categ_prop, $z3950_accessible ;
			global $value_deflt_fonction, $value_deflt_relation;
			global $thesaurus_mode_pmb ;
			global $PMBuserid, $pmb_form_editables,$thesaurus_classement_mode_pmb;
			global $xmlta_indexation_lang;
			global $thesaurus_concepts_active;			
			global $pmb_map_activate;
			global $pmb_notices_show_dates;
			global $thesaurus_categories_affichage_ordre;
			
			include("$include_path/templates/catal_form.tpl.php");
			$fonction = new marc_list('function');
			
			// mise a jour de l'action en fonction de l'id
			$this->action .= $this->id;
		
			// mise a jour de l'en-tete du formulaire
			if ($this->notice_mere[0]) $this->libelle_form.=" ".$msg["catalog_notice_fille_lib"]." ".substr($this->notice_mere[0],0,100).(count($this->notice_mere)>1?", ...":"");
			$form_notice = str_replace('!!libelle_form!!', $this->libelle_form, $form_notice);
	
			// mise a jour des flags de niveau hierarchique
			$form_notice = str_replace('!!b_level!!', $this->biblio_level, $form_notice);
			$form_notice = str_replace('!!h_level!!', $this->hierar_level, $form_notice);
		
			// mise a jour de l'onglet 0
			$ptab[0] = str_replace('!!tit1!!',				htmlentities($this->tit1,ENT_QUOTES, $charset)			, $ptab[0]);
			$ptab[0] = str_replace('!!tit2!!',				htmlentities($this->tit2,ENT_QUOTES, $charset)			, $ptab[0]);
			$ptab[0] = str_replace('!!tit3!!',				htmlentities($this->tit3,ENT_QUOTES, $charset)			, $ptab[0]);
			$ptab[0] = str_replace('!!tit4!!',				htmlentities($this->tit4,ENT_QUOTES, $charset)			, $ptab[0]);
			$ptab[0] = str_replace('!!tparent_id!!',		$this->tparent_id										, $ptab[0]);
			$ptab[0] = str_replace('!!tparent!!',			htmlentities($this->tparent,ENT_QUOTES, $charset)		, $ptab[0]);
			$ptab[0] = str_replace('!!tnvol!!',				htmlentities($this->tnvol,ENT_QUOTES, $charset)			, $ptab[0]);
		
			$form_notice = str_replace('!!tab0!!', $ptab[0], $form_notice);
		
			// mise a jour de l'onglet 1
			// constitution de la mention de responsabilite
			//$this->responsabilites
			
			$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $this->responsabilites["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
			}
			if ($value_deflt_fonction && $auteur_0["id"]==0) $auteur_0["fonction"] = $value_deflt_fonction ;
		
			$ptab[1] = str_replace('!!aut0_id!!',			$auteur_0["id"], $ptab[1]);
			$ptab[1] = str_replace('!!aut0!!',				htmlentities($auteur->isbd_entry,ENT_QUOTES, $charset), $ptab[1]);
			$ptab[1] = str_replace('!!f0_code!!',			$auteur_0["fonction"], $ptab[1]);
			$ptab[1] = str_replace('!!f0!!',				$fonction->table[$auteur_0["fonction"]], $ptab[1]);
		
		
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			$max_aut1 = (count($as)) ;
			if ($max_aut1==0) $max_aut1=1;
			for ($i = 0 ; $i < $max_aut1 ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_1["id"]);
				if ($value_deflt_fonction && $auteur_1["id"]==0 && $i==0) $auteur_1["fonction"] = $value_deflt_fonction ;
				
				$ptab_aut_autres = str_replace('!!iaut!!', $i, $ptab[11]) ;
					
				$ptab_aut_autres = str_replace('!!aut1_id!!',			$auteur_1["id"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut1!!',				htmlentities($auteur->isbd_entry,ENT_QUOTES, $charset), $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f1_code!!',			$auteur_1["fonction"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f1!!',				$fonction->table[$auteur_1["fonction"]], $ptab_aut_autres);
				$autres_auteurs .= $ptab_aut_autres ;
			}
			$ptab[1] = str_replace('!!max_aut1!!', $max_aut1, $ptab[1]);
			
			$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
			$max_aut2 = (count($as)) ;
			if ($max_aut2==0) $max_aut2=1;
			for ($i = 0 ; $i < $max_aut2 ; $i++) {
				$indice = $as[$i] ;
				$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_2["id"]);
				if ($value_deflt_fonction && $auteur_2["id"]==0 && $i==0) $auteur_2["fonction"] = $value_deflt_fonction ;
				
				$ptab_aut_autres = str_replace('!!iaut!!', $i, $ptab[12]) ;
					
				$ptab_aut_autres = str_replace('!!aut2_id!!',			$auteur_2["id"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut2!!',				htmlentities($auteur->isbd_entry,ENT_QUOTES, $charset), $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f2_code!!',			$auteur_2["fonction"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f2!!',				$fonction->table[$auteur_2["fonction"]], $ptab_aut_autres);
				$auteurs_secondaires .= $ptab_aut_autres ;
			}
			$ptab[1] = str_replace('!!max_aut2!!', $max_aut2, $ptab[1]);
			
			$ptab[1] = str_replace('!!autres_auteurs!!', $autres_auteurs, $ptab[1]);
			$ptab[1] = str_replace('!!auteurs_secondaires!!', $auteurs_secondaires, $ptab[1]);
			$form_notice = str_replace('!!tab1!!', $ptab[1], $form_notice);
		
			// mise a jour de l'onglet 2
			$ptab[2] = str_replace('!!ed1_id!!',			$this->ed1_id			, $ptab[2]);
			$ptab[2] = str_replace('!!ed1!!',				htmlentities($this->ed1,ENT_QUOTES, $charset)				, $ptab[2]);
			$ptab[2] = str_replace('!!coll_id!!',			$this->coll_id			, $ptab[2]);
			$ptab[2] = str_replace('!!coll!!',				htmlentities($this->coll,ENT_QUOTES, $charset)				, $ptab[2]);
			$ptab[2] = str_replace('!!subcoll_id!!',			$this->subcoll_id		, $ptab[2]);
			$ptab[2] = str_replace('!!subcoll!!',			htmlentities($this->subcoll,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!year!!',				$this->year				, $ptab[2]);
			$ptab[2] = str_replace('!!nocoll!!',			htmlentities($this->nocoll,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!mention_edition!!',			htmlentities($this->mention_edition,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!ed2_id!!',			$this->ed2_id			, $ptab[2]);
			$ptab[2] = str_replace('!!ed2!!',				htmlentities($this->ed2,ENT_QUOTES, $charset)				, $ptab[2]);
		
			$form_notice = str_replace('!!tab2!!', $ptab[2], $form_notice);
		
			// mise a jour de l'onglet 3
			$ptab[3] = str_replace('!!cb!!',				$this->code				, $ptab[3]);
			$ptab[3] = str_replace('!!notice_id!!',			$this->id				, $ptab[3]);
		
			$form_notice = str_replace('!!tab3!!', $ptab[3], $form_notice);
			
			// Gestion des titres uniformes 
			global $pmb_use_uniform_title;
			if ($pmb_use_uniform_title) {
				if($this->duplicate_from_id) $tu=new tu_notice($this->duplicate_from_id);
				else $tu=new tu_notice($this->id);
				$ptab[230] = str_replace("!!titres_uniformes!!", $tu->get_form("notice"), $ptab[230]);
				$form_notice = str_replace('!!tab230!!', $ptab[230], $form_notice);
			}				

			// mise a jour de l'onglet 4
			$ptab[4] = str_replace('!!npages!!',	htmlentities($this->npages	,ENT_QUOTES, $charset)	, $ptab[4]);
			$ptab[4] = str_replace('!!ill!!',		htmlentities($this->ill		,ENT_QUOTES, $charset)	, $ptab[4]);
			$ptab[4] = str_replace('!!size!!',		htmlentities($this->size	,ENT_QUOTES, $charset)	, $ptab[4]);
			$ptab[4] = str_replace('!!prix!!',		htmlentities($this->prix	,ENT_QUOTES, $charset)	, $ptab[4]);
			$ptab[4] = str_replace('!!accomp!!',	htmlentities($this->accomp	,ENT_QUOTES, $charset)	, $ptab[4]);
		
			$form_notice = str_replace('!!tab4!!', $ptab[4], $form_notice);
		
			// mise a jour de l'onglet 5
			$ptab[5] = str_replace('!!n_gen!!',		htmlentities($this->n_gen	,ENT_QUOTES, $charset)	, $ptab[5]);
			$ptab[5] = str_replace('!!n_contenu!!',	htmlentities($this->n_contenu	,ENT_QUOTES, $charset)	, $ptab[5]);
			$ptab[5] = str_replace('!!n_resume!!',	htmlentities($this->n_resume	,ENT_QUOTES, $charset)	, $ptab[5]);
		
			$form_notice = str_replace('!!tab5!!', $ptab[5], $form_notice);
		
			// mise a jour de l'onglet 6
			// categories
			//tri ?
			if(($thesaurus_categories_affichage_ordre==0) && count($this->categories)){
				$tmp=array();
				foreach ( $this->categories as $key=>$value ) {
					$tmp[$key]=strip_tags($value['categ_libelle']);
				}
				$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$this->categories[$key];//On reprend les bons couples
				}
				$this->categories=array_values($tmp);
			}
			if (sizeof($this->categories)==0) $max_categ = 1 ;
				else $max_categ = sizeof($this->categories) ; 
			$tab_categ_order="";
			for ($i = 0 ; $i < $max_categ ; $i++) {
				$categ_id = $this->categories[$i]["categ_id"] ;
				$categ = new category($categ_id);
				
				if ($i==0) $ptab_categ = str_replace('!!icateg!!', $i, $ptab[60]) ;
					else $ptab_categ = str_replace('!!icateg!!', $i, $ptab[601]) ;					
									
				$ptab_categ = str_replace('!!categ_id!!',			$categ_id, $ptab_categ);
				if ( sizeof($this->categories)==0 ) { 
					$ptab_categ = str_replace('!!categ_libelle!!', '', $ptab_categ);		
				} else {
					if ($thesaurus_mode_pmb) $nom_thesaurus='['.$categ->thes->getLibelle().'] ' ;
						else $nom_thesaurus='' ;
					$ptab_categ = str_replace('!!categ_libelle!!',	htmlentities($nom_thesaurus.$categ->catalog_form,ENT_QUOTES, $charset), $ptab_categ);
					
					if($tab_categ_order!="")$tab_categ_order.=",";
					$tab_categ_order.=$i;
				}
				$categ_repetables .= $ptab_categ ;
			}
			$ptab[6] = str_replace('!!max_categ!!', $max_categ, $ptab[6]);
			$ptab[6] = str_replace('!!categories_repetables!!', $categ_repetables, $ptab[6]);
			$ptab[6] = str_replace('!!tab_categ_order!!', $tab_categ_order, $ptab[6]);
		
			// indexation interne
			$ptab[6] = str_replace('!!indexint_id!!', $this->indexint, $ptab[6]);
			if ($this->indexint){
				$indexint = new indexint($this->indexint);
				if ($indexint->comment) $disp_indexint= $indexint->name." - ".$indexint->comment ;
				else $disp_indexint= $indexint->name ;
				if ($thesaurus_classement_mode_pmb) { // plusieurs classements/indexations decimales autorises en parametrage
					if ($indexint->name_pclass) $disp_indexint="[".$indexint->name_pclass."] ".$disp_indexint;
				}
				$ptab[6] = str_replace('!!indexint!!', htmlentities($disp_indexint,ENT_QUOTES, $charset), $ptab[6]);
				$ptab[6] = str_replace('!!num_pclass!!', $indexint->id_pclass, $ptab[6]);
			} else {
				$ptab[6] = str_replace('!!indexint!!', '', $ptab[6]);
				$ptab[6] = str_replace('!!num_pclass!!', '', $ptab[6]);
			}
		
			// indexation libre
			$ptab[6] = str_replace('!!f_indexation!!', htmlentities($this->index_l,ENT_QUOTES, $charset), $ptab[6]);
			global $pmb_keyword_sep ;
			
			//if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";
			$sep="'$pmb_keyword_sep'";
			if (!$pmb_keyword_sep) $sep="' '";
			if(ord($pmb_keyword_sep)==0xa || ord($pmb_keyword_sep)==0xd) $sep=$msg['catalogue_saut_de_ligne'];
			$ptab[6] = str_replace("!!sep!!",htmlentities($sep,ENT_QUOTES, $charset),$ptab[6]);
			
			// Indexation concept
			if($thesaurus_concepts_active == 1){
				$index_concept = new index_concept($this->id, TYPE_NOTICE);
				$ptab[6] = str_replace('!!index_concept_form!!', $index_concept->get_form("notice"), $ptab[6]);
			}else{
				$ptab[6] = str_replace('!!index_concept_form!!', "", $ptab[6]);
			}
			$form_notice = str_replace('!!tab6!!', $ptab[6], $form_notice);
		
			// mise a jour de l'onglet 7 : langues
			// langues repetables
			if (sizeof($this->langues)==0) $max_lang = 1 ;
				else $max_lang = sizeof($this->langues) ; 
			for ($i = 0 ; $i < $max_lang ; $i++) {
				if ($i) $ptab_lang = str_replace('!!ilang!!', $i, $ptab[701]) ;
					else $ptab_lang = str_replace('!!ilang!!', $i, $ptab[70]) ;
				if ( sizeof($this->langues)==0 ) { 
					$ptab_lang = str_replace('!!lang_code!!', '', $ptab_lang);
					$ptab_lang = str_replace('!!lang!!', '', $ptab_lang);		
				} else {
					$ptab_lang = str_replace('!!lang_code!!', $this->langues[$i]["lang_code"], $ptab_lang);
					$ptab_lang = str_replace('!!lang!!',htmlentities($this->langues[$i]["langue"],ENT_QUOTES, $charset), $ptab_lang);
				}
				$lang_repetables .= $ptab_lang ;
			}
			$ptab[7] = str_replace('!!max_lang!!', $max_lang, $ptab[7]);
			$ptab[7] = str_replace('!!langues_repetables!!', $lang_repetables, $ptab[7]);
		
			// langues originales repetables
			if (sizeof($this->languesorg)==0) $max_langorg = 1 ;
				else $max_langorg = sizeof($this->languesorg) ; 
			for ($i = 0 ; $i < $max_langorg ; $i++) {
				if ($i) $ptab_lang = str_replace('!!ilangorg!!', $i, $ptab[711]) ;
					else $ptab_lang = str_replace('!!ilangorg!!', $i, $ptab[71]) ;
				if ( sizeof($this->languesorg)==0 ) { 
					$ptab_lang = str_replace('!!langorg_code!!', '', $ptab_lang);
					$ptab_lang = str_replace('!!langorg!!', '', $ptab_lang);		
				} else {
					$ptab_lang = str_replace('!!langorg_code!!', $this->languesorg[$i]["lang_code"], $ptab_lang);
					$ptab_lang = str_replace('!!langorg!!',htmlentities($this->languesorg[$i]["langue"],ENT_QUOTES, $charset), $ptab_lang);
				}
				$langorg_repetables .= $ptab_lang ;
			}
			$ptab[7] = str_replace('!!max_langorg!!', $max_langorg, $ptab[7]);
			$ptab[7] = str_replace('!!languesorg_repetables!!', $langorg_repetables, $ptab[7]);
		
			$form_notice = str_replace('!!tab7!!', $ptab[7], $form_notice);
		
			// mise a jour de l'onglet 8
			global $pmb_curl_timeout;
			$ptab[8] = str_replace('!!lien!!',			htmlentities($this->lien	,ENT_QUOTES, $charset)	, $ptab[8]);
			$ptab[8] = str_replace('!!eformat!!',		htmlentities($this->eformat	,ENT_QUOTES, $charset)	, $ptab[8]);
			$ptab[8] = str_replace('!!pmb_curl_timeout!!',		$pmb_curl_timeout	, $ptab[8]);
		
			$form_notice = str_replace('!!tab8!!', $ptab[8], $form_notice);
		
			//Mise a jour de l'onglet 9
			$p_perso=new parametres_perso("notices");
			
			if (!$p_perso->no_special_fields) {
				// si on duplique, construire le formulaire avec les donnees de la notice d'origine
				if ($this->duplicate_from_id) $perso_=$p_perso->show_editable_fields($this->duplicate_from_id);
					else $perso_=$p_perso->show_editable_fields($this->id);
				$perso="";
				for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
					$p=$perso_["FIELDS"][$i];
					$perso.="<div id='move_".$p["NAME"]."' movable='yes' title=\"".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."\">
								<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."</label></div>
								<div class='row'>".$p["AFF"]."</div>
							 </div>";
				}
				$perso.=$perso_["CHECK_SCRIPTS"];
				$ptab[9]=str_replace("!!champs_perso!!",$perso,$ptab[9]);
			} else 
				$ptab[9]="\n<script>function check_form() { return true; }</script>\n";
			
			$form_notice = str_replace('!!tab9!!', $ptab[9], $form_notice);
			
			//Liens vers d'autres notices
			$string_relations="";
			$n_rel=0;
			
			foreach($this->notice_link as $direction=>$relations){
				foreach($relations as $relation){
					//Selection du template
					if ($n_rel==0){
						$pattern_rel=$ptab[130];
					}else{
						$pattern_rel=$ptab[131];
					}
			
					//Construction du textbox
					$pattern_rel=str_replace("!!notice_relations_id!!",$relation['id_notice'],$pattern_rel);
					$pattern_rel=str_replace("!!notice_relations_libelle!!",htmlentities($relation['title_notice'],ENT_QUOTES,$charset),$pattern_rel);
					$pattern_rel=str_replace("!!notice_relations_rank!!",$relation['rank'],$pattern_rel);
					$pattern_rel=str_replace("!!n_rel!!",$n_rel,$pattern_rel);
			
					//Construction du combobox de type de lien
					$pattern_rel=str_replace("!!f_notice_type_relations_name!!","f_rel_type_$n_rel",$pattern_rel);
					//Recuperation des types de relation
					$liste_type_relation_up=new marc_list("relationtypeup");
					$liste_type_relation_down=new marc_list("relationtypedown");
					$liste_type_relation_both=array();
					$corresp_relation_up_down=array();
					foreach($liste_type_relation_up->table as $key_up=>$val_up){
						foreach($liste_type_relation_down->table as $key_down=>$val_down){
							if($val_up==$val_down){
								$liste_type_relation_both['down'][$key_down]=$val_down;
								$liste_type_relation_both['up'][$key_up]=$val_up;
								$corresp_relation_up_down[$key_up]=$key_down;
								unset($liste_type_relation_down->table[$key_down]);
								unset($liste_type_relation_up->table[$key_up]);
							}
						}
					}
					
					$opts='';
					if ($this->id) {
						foreach($liste_type_relation_up->table as $key=>$val){
							if(preg_match('/^'.$key.'/', $relation['relation_type']) && $direction=='up'){
								$opts.='<option  style="color:#000000" value="'.$key.'-up" selected="selected" >'.$val.'</option>';
							}else{
								$opts.='<option  style="color:#000000" value="'.$key.'-up">'.$val.'</option>';
							}
						}	
					} else {
						foreach($liste_type_relation_up->table as $key=>$val){
							if($key.'-up'==$value_deflt_relation){
								$opts.='<option  style="color:#000000" value="'.$key.'-up" selected="selected" >'.$val.'</option>';
							}else{
								$opts.='<option  style="color:#000000" value="'.$key.'-up">'.$val.'</option>';
							}
						}	
					}
					$pattern_rel=str_replace("!!f_notice_type_relations_up!!",$opts,$pattern_rel);
					$opts='';
					if ($this->id) {
						foreach($liste_type_relation_down->table as $key=>$val){
							if(preg_match('/^'.$key.'/', $relation['relation_type'])  && $direction=='down'){
								$opts.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
							}else{
								$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
							}
						}	
					} else {
						foreach($liste_type_relation_down->table as $key=>$val){
							if($key.'-down'==$value_deflt_relation){
								$opts.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
							}else{
								$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
							}
						}
					}
					$pattern_rel=str_replace("!!f_notice_type_relations_down!!",$opts,$pattern_rel);
					$opts='';

					if(array_key_exists($relation['relation_type'], $liste_type_relation_both['up']) || array_key_exists($relation['relation_type'], $liste_type_relation_both['down'])){
						$opts.='<option  style="color:#000000" value="'.$relation['relation_type'].'-'.$direction.'" selected="selected" >'.$liste_type_relation_both[$direction][$relation['relation_type']].'</option>';
						if($direction=="up"){
							$notDirection="down";
						}else{
							$notDirection="up";
							$corresp_relation_up_down=array_flip($corresp_relation_up_down);
						}
						$notRelationType=$corresp_relation_up_down[$relation['relation_type']];
						unset($liste_type_relation_both[$direction][$relation['relation_type']]);
						unset($liste_type_relation_both[$notDirection][$notRelationType]);
					}
					if ($this->id) {
						foreach($liste_type_relation_both['down'] as $key=>$val){
							$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
						}
					} else {
						foreach($liste_type_relation_both['down'] as $key=>$val){
							if($key.'-down'==$value_deflt_relation){
								$opts.='<option  style="color:#000000" value="'.$key.'-down" selected="selected">'.$val.'</option>';
							} else {
								$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
							}
						}
					}
					
					$pattern_rel=str_replace("!!f_notice_type_relations_both!!",$opts,$pattern_rel);
					
					$string_relations.=$pattern_rel;
			
					$n_rel++;
				}
			}
			if (!$n_rel) {
				$pattern_rel=$ptab[130];
				$pattern_rel=str_replace("!!notice_relations_id!!","",$pattern_rel);
				$pattern_rel=str_replace("!!notice_relations_libelle!!","",$pattern_rel);
				$pattern_rel=str_replace("!!notice_relations_rank!!","0",$pattern_rel);
				$pattern_rel=str_replace("!!n_rel!!",$n_rel,$pattern_rel);
				$pattern_rel=str_replace("!!f_notice_type_relations_name!!","f_rel_type_0",$pattern_rel);
				//Recuperation des types de relation
				$liste_type_relation_up=new marc_list("relationtypeup");
				$liste_type_relation_down=new marc_list("relationtypedown");
				$liste_type_relation_both=array();
				
				foreach($liste_type_relation_up->table as $key_up=>$val_up){
					foreach($liste_type_relation_down->table as $key_down=>$val_down){
						if($val_up==$val_down){
							$liste_type_relation_both[$key_down]=$val_down;
							unset($liste_type_relation_down->table[$key_down]);
							unset($liste_type_relation_up->table[$key_up]);
						}
					}
				}
				
				$opts='';
				foreach($liste_type_relation_up->table as $key=>$val){
					if($key.'-up'==$value_deflt_relation){
						$opts.='<option  style="color:#000000" value="'.$key.'-up" selected="selected" >'.$val.'</option>';
					}else{
						$opts.='<option  style="color:#000000" value="'.$key.'-up">'.$val.'</option>';
					}
				}
				$pattern_rel=str_replace("!!f_notice_type_relations_up!!",$opts,$pattern_rel);
				$opts='';
				foreach($liste_type_relation_down->table as $key=>$val){
					if($key.'-down'==$value_deflt_relation){
						$opts.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
					}else{
						$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
					}
				}
				$pattern_rel=str_replace("!!f_notice_type_relations_down!!",$opts,$pattern_rel);
				$opts='';
				foreach($liste_type_relation_both as $key=>$val){
					if($key.'-down'==$value_deflt_relation){
						$opts.='<option  style="color:#000000" value="'.$key.'-down" selected="selected" >'.$val.'</option>';
					}else{
						$opts.='<option  style="color:#000000" value="'.$key.'-down">'.$val.'</option>';
					}
				}
				$pattern_rel=str_replace("!!f_notice_type_relations_both!!",$opts,$pattern_rel);
				
				$string_relations.=$pattern_rel;
			
				$n_rel++;
			}
				
			//Nombre de relations
			$ptab[13]=str_replace("!!max_rel!!",$n_rel,$ptab[13]);
				
			//Liens multiples
			$ptab[13]=str_replace("!!notice_relations!!",$string_relations,$ptab[13]);
			
			$form_notice = str_replace('!!tab11!!', $ptab[13],$form_notice);
		
			// champs de gestion
			$select_statut = gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "form_notice_statut", "", $this->statut, "", "","","",0) ;
			$ptab[10] = str_replace('!!notice_statut!!', $select_statut, $ptab[10]);
			
			if($this->is_new){
				$ptab[10] = str_replace('!!checked_yes!!', "checked", $ptab[10]);
				$ptab[10] = str_replace('!!checked_no!!', "", $ptab[10]);		
			}else{
				$ptab[10] = str_replace('!!checked_no!!', "checked", $ptab[10]);	
				$ptab[10] = str_replace('!!checked_yes!!', "", $ptab[10]);				
			}
			
			$ptab[10] = str_replace('!!commentaire_gestion!!',htmlentities($this->commentaire_gestion,ENT_QUOTES, $charset), $ptab[10]);
			$ptab[10] = str_replace('!!thumbnail_url!!',htmlentities($this->thumbnail_url,ENT_QUOTES, $charset), $ptab[10]);
			
			global $pmb_notice_img_folder_id;
			$message_folder="";
			if($pmb_notice_img_folder_id){
				$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
				$res = pmb_mysql_query($req);
				if(pmb_mysql_num_rows($res)){
					$rep=pmb_mysql_fetch_object($res);
					if(!is_dir($rep->repertoire_path)){
						$notice_img_folder_error=1;
					}
				}else $notice_img_folder_error=1;
				if($notice_img_folder_error){
					if (SESSrights & ADMINISTRATION_AUTH){
						$requete = "select * from parametres where gestion=0 and type_param='pmb' and sstype_param='notice_img_folder_id' ";
						$res = pmb_mysql_query($requete);
						$i=0;
						if($param=pmb_mysql_fetch_object($res)) {
							$message_folder=" <a class='erreur' href='./admin.php?categ=param&action=modif&id_param=".$param->id_param."' >".$msg['notice_img_folder_admin_no_access']."</a> ";
						}
					}else{
						$message_folder=$msg['notice_img_folder_no_access'];
					}
				}
			}
			$ptab[10] = str_replace('!!message_folder!!',$message_folder, $ptab[10]);
			
			if ($this->id && $pmb_notices_show_dates) {
				$create_date = new DateTime($this->create_date);
				$update_date = new DateTime($this->update_date);
				$dates_notices = "<br>
					<label for='notice_date_crea' class='etiquette'>".$msg["noti_crea_date"]."</label>&nbsp;".$create_date->format('d/m/Y H:i:s')."
			    	<br>
			    	 <label for='notice_date_mod' class='etiquette'>".$msg["noti_mod_date"]."</label>&nbsp;".$update_date->format('d/m/Y H:i:s');
				$ptab[10] = str_replace('!!dates_notice!!',$dates_notices, $ptab[10]);
			} else {
				$ptab[10] = str_replace('!!dates_notice!!',"", $ptab[10]);
			}
				
			//affichage des formulaires des droits d'acces
			$rights_form = $this->get_rights_form();
			$ptab[10] = str_replace('<!-- rights_form -->', $rights_form, $ptab[10]);
			
			// langue de la notice
			global $lang;
			$user_lang=$this->indexation_lang;
			if(!$user_lang)$user_lang=$xmlta_indexation_lang;			
		//	if(!$user_lang) $user_lang="fr_FR";
			$langues = new XMLlist("$include_path/messages/languages.xml");
			$langues->analyser();
			$clang = $langues->table;
				
			$combo = "<select name='indexation_lang' id='indexation_lang' class='saisie-20em' >";
			if(!$user_lang) $combo .= "<option value='' selected>--</option>";
			else $combo .= "<option value='' >--</option>";
			while(list($cle, $value) = each($clang)) {
				// arabe seulement si on est en utf-8
				if (($charset != 'utf-8' and $user_lang != 'ar') or ($charset == 'utf-8')) {
					if(strcmp($cle, $user_lang) != 0) $combo .= "<option value='$cle'>$value ($cle)</option>";
					else $combo .= "<option value='$cle' selected>$value ($cle)</option>";
				}
			}
			$combo .= "</select>";
			$ptab[10] = str_replace('!!indexation_lang!!',$combo, $ptab[10]);
			$form_notice = str_replace('!!indexation_lang_sel!!', $user_lang, $form_notice);
			
			$form_notice = str_replace('!!tab10!!', $ptab[10], $form_notice);				
			
			// autorité personnalisées			
			$authperso = new authperso_notice($this->id);
			$authperso_tpl=$authperso->get_form();
			$form_notice = str_replace('!!authperso!!', $authperso_tpl, $form_notice);		
			
			$form_notice = str_replace('!!tab10!!', $ptab[10], $form_notice);
			
			// map	
			if($pmb_map_activate){
				if($this->duplicate_from_id) $map_edition=new map_edition_controler(TYPE_RECORD,$this->duplicate_from_id);
				else $map_edition=new map_edition_controler(TYPE_RECORD,$this->id);
				$map_form=$map_edition->get_form();
				if($this->duplicate_from_id) $map_info=new map_info($this->duplicate_from_id);
				else $map_info=new map_info($this->id);
				$map_form_info=$map_info->get_form();
				$map_notice_form=$ptab[14];			
				$map_notice_form = str_replace('!!notice_map!!', $map_form.$map_form_info, $map_notice_form);
				$form_notice = str_replace('!!tab14!!', $map_notice_form, $form_notice);
			}else{				
				$form_notice = str_replace('!!tab14!!', "", $form_notice);
			}
			
			// Nomenclature
			if($pmb_nomenclature_activate){								
				$nomenclature= new nomenclature_record_ui($this->id);				
				$nomenclature_notice_form=$ptab[15];
				$nomenclature_notice_form = str_replace('!!nomenclature_form!!', $nomenclature->get_form(), $nomenclature_notice_form);
				$form_notice = str_replace('!!tab15!!', $nomenclature_notice_form, $form_notice);
			}else{
				$form_notice = str_replace('!!tab15!!', "", $form_notice);
			}
							
			// definition de la page cible du form
			$form_notice = str_replace('!!action!!', $this->action, $form_notice);
		
			// ajout des selecteurs
			$select_doc = new marc_select('doctype', 'typdoc', $this->type_doc, "get_pos(); expandAll(); ajax_parse_dom(); if (inedit) move_parse_dom(relative); else initIt();");
			$form_notice = str_replace('!!doc_type!!', $select_doc->display, $form_notice);
		
			$form_notice = str_replace('!!notice_id_no_replace!!', $this->id, $form_notice);
		
			// Ajout des localisations pour edition
			$select_loc="";
			if ($PMBuserid==1) {
				$req_loc="select idlocation,location_libelle from docs_location";
				$res_loc=pmb_mysql_query($req_loc);
				if (pmb_mysql_num_rows($res_loc)>1) {	
					$select_loc="<select name='grille_location' id='grille_location' style='display:none' onChange=\"get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt();\">\n";
					$select_loc.="<option value='0'>".$msg['all_location']."</option>\n";
					while (($r=pmb_mysql_fetch_object($res_loc))) {
						$select_loc.="<option value='".$r->idlocation."'>".$r->location_libelle."</option>\n";
					}
					$select_loc.="</select>\n";
				}
			}	
			$form_notice=str_replace("!!location!!",$select_loc,$form_notice);
		
			// affichage du lien pour suppression et du lien d'annulation
			if ($this->id) {
				$link_supp = "
				<script type=\"text/javascript\">
					function confirm_delete() {
						result = confirm(\"{$msg[confirm_suppr_notice]}\");
			       		if(result) {
			       			unload_off();
			           		document.location = './catalog.php?categ=delete&id=".$this->id."'
						} 
					}
				</script>
				<input type='button' class='bouton' value=\"{$msg[63]}\" onClick=\"confirm_delete();\" />";
				$link_annul = "<input type='button' class='bouton' value=\"{$msg[76]}\" onClick=\"unload_off();history.go(-1);\" />";
				$link_remplace =  "<input type='button' class='bouton' value='$msg[158]' onclick='unload_off();document.location=\"./catalog.php?categ=remplace&id=".$this->id."\"' />";
				$link_duplicate =  "<input type='button' class='bouton' value='$msg[notice_duplicate_bouton]' onclick='unload_off();document.location=\"./catalog.php?categ=duplicate&id=".$this->id."\"' />";
				if ($z3950_accessible) $link_z3950 = "<input type='button' class='bouton' value='$msg[notice_z3950_update_bouton]' onclick='unload_off();document.location=\"./catalog.php?categ=z3950&id_notice=".$this->id."&isbn=".$this->code."\"' />";
					else $link_z3950="";
				if ($pmb_type_audit) $link_audit =  "<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=1&object_id=$this->id', 'audit_popup', 700, 500, -2, -2, '$select_categ_prop')\" title='$msg[audit_button]' value='$msg[audit_button]' />";
					else $link_audit = "" ;
			} else {
				$link_supp = "";
				$link_remplace = "";
				$link_duplicate = "" ;
				$link_z3950 = "" ;
				$link_audit = "" ;
// 				if ($this->notice_mere_id || $this->duplicate_from_id) $link_annul = "<input type='button' class='bouton' value=\"{$msg[76]}\" onClick=\"unload_off();history.go(-1);\" />";
				if ($this->notice_link['up'][0]['id_notice'] || $this->duplicate_from_id) $link_annul = "<input type='button' class='bouton' value=\"{$msg[76]}\" onClick=\"unload_off();history.go(-1);\" />"; 
				else $link_annul = "<input type='button' class='bouton' value=\"{$msg[76]}\" onClick=\"unload_off();document.location='".$this->link_annul."';\" />";
			}
			$form_notice = str_replace('!!link_supp!!', $link_supp, $form_notice);
			$form_notice = str_replace('!!link_annul!!', $link_annul, $form_notice);
			$form_notice = str_replace('!!link_remplace!!', $link_remplace, $form_notice);
			$form_notice = str_replace('!!link_duplicate!!', $link_duplicate, $form_notice);
			$form_notice = str_replace('!!link_z3950!!', $link_z3950, $form_notice);
			$form_notice = str_replace('!!link_audit!!', $link_audit, $form_notice);
			return $form_notice;
		}
		
		
		//creation formulaire droits d'acces pour notices
		function get_rights_form() {
			
			global $dbh,$msg,$charset;
			global $gestion_acces_active,$gestion_acces_user_notice, $gestion_acces_empr_notice;
			global $gestion_acces_user_notice_def, $gestion_acces_empr_notice_def;
			global $PMBuserid;
			
			if ($gestion_acces_active!=1) return '';
			$ac = new acces();
			
			$form = '';
			$c_form = "<label class='etiquette'><!-- domain_name --></label>
						<div class='row'>
				    	<div class='colonne3'>".htmlentities($msg['dom_cur_prf'],ENT_QUOTES,$charset)."</div>
				    	<div class='colonne_suite'><!-- prf_rad --></div>
				    	</div>
				    	<div class='row'>
				    	<div class='colonne3'>".htmlentities($msg['dom_cur_rights'],ENT_QUOTES,$charset)."</div>
					    <div class='colonne_suite'><!-- r_rad --></div>
					    <div class='row'><!-- rights_tab --></div>
					    </div>";
	
			if($gestion_acces_user_notice==1) {
				
				$r_form=$c_form;
				$dom_1 = $ac->setDomain(1);	
				$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_1->getComment('long_name'), ENT_QUOTES, $charset) ,$r_form);
				if($this->id) {
	
					//profil ressource
					$def_prf=$dom_1->getComment('res_prf_def_lib');
					$res_prf=$dom_1->getResourceProfile($this->id);
					$q=$dom_1->loadUsedResourceProfiles();
					
					//recuperation droits utilisateur
					$user_rights = $dom_1->getRights($PMBuserid,$this->id,3);
					
					if($user_rights & 2) {
						$p_sel = gen_liste($q,'prf_id','prf_name', 'res_prf[1]', '', $res_prf, '0', $def_prf , '0', $def_prf);
						$p_rad = "<input type='radio' name='prf_rad[1]' value='R' ";
						if ($gestion_acces_user_notice_def!='1') $p_rad.= "checked='checked' ";
						$p_rad.= ">".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</input><input type='radio' name='prf_rad[1]' value='C' ";
						if ($gestion_acces_user_notice_def=='1') $p_rad.= "checked='checked' ";
						$p_rad.= ">".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)." $p_sel</input>";
						$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
					} else {
						$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_1->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
					}

					
					//droits/profils utilisateurs
					if($user_rights & 1) {
						$r_rad = "<input type='radio' name='r_rad[1]' value='R' ";
						if ($gestion_acces_user_notice_def!='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</input><input type='radio' name='r_rad[1]' value='C' ";
						if ($gestion_acces_user_notice_def=='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)."</input>";
						$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
					}
								
					
					//recuperation profils utilisateurs
					$t_u=array();
					$t_u[0]= $dom_1->getComment('user_prf_def_lib');	//niveau par defaut
					$qu=$dom_1->loadUsedUserProfiles();
					$ru=pmb_mysql_query($qu, $dbh);
					if (pmb_mysql_num_rows($ru)) {
						while(($row=pmb_mysql_fetch_object($ru))) {
					        $t_u[$row->prf_id]= $row->prf_name;
						}
					}
	
					//recuperation des controles dependants de l'utilisateur 	
					$t_ctl=$dom_1->getControls(0);
					
					//recuperation des droits 
					$t_rights = $dom_1->getResourceRights($this->id);
									
					if (count($t_u)) {
		
						$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
						foreach($t_u as $k=>$v) {
							$h_tab.= "<th class='dom_col'>".htmlentities($v, ENT_QUOTES, $charset)."</th>";			
						}
						$h_tab.="</tr><!-- rights_tab --></table></div>";
						
						$c_tab = '<tr>';
						foreach($t_u as $k=>$v) {
								
							$c_tab.= "<td><table style='border:1px solid;' ><!-- rows --></table></td>";
							$t_rows = "";
									
							foreach($t_ctl as $k2=>$v2) {
															
								$t_rows.="
									<tr>
										<td style='width:25px;' ><input type='checkbox' name='chk_rights[1][".$k."][".$k2."]' value='1' ";
								if ($t_rights[$k][$res_prf] & (pow(2,$k2-1))) {
									$t_rows.= "checked='checked' ";
								}
								if(($user_rights & 1)==0) $t_rows.="disabled='disabled' "; 
								$t_rows.= "/></td>
										<td>".htmlentities($v2, ENT_QUOTES, $charset)."</td>
									</tr>";
							}						
							$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
						}
						$c_tab.= "</tr>";
						
					}
					$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);
					$r_form=str_replace('<!-- rights_tab -->', $h_tab, $r_form);
					
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
					$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
				}
				$form.= $r_form;
				
			}
	
			if($gestion_acces_empr_notice==1) {
				
				$r_form=$c_form;
				$dom_2 = $ac->setDomain(2);	
				$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_2->getComment('long_name'), ENT_QUOTES, $charset) ,$r_form);
				if($this->id) {
					
					//profil ressource
					$def_prf=$dom_2->getComment('res_prf_def_lib');
					$res_prf=$dom_2->getResourceProfile($this->id);
					$q=$dom_2->loadUsedResourceProfiles();
					
					//Recuperation droits generiques utilisateur
					$user_rights = $dom_2->getDomainRights(0,$res_prf);
					
					if($user_rights & 2) {
						$p_sel = gen_liste($q,'prf_id','prf_name', 'res_prf[2]', '', $res_prf, '0', $def_prf , '0', $def_prf);
						$p_rad = "<input type='radio' name='prf_rad[2]' value='R' ";
						if ($gestion_acces_empr_notice_def!='1') $p_rad.= "checked='checked' ";
						$p_rad.= ">".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</input><input type='radio' name='prf_rad[2]' value='C' ";
						if ($gestion_acces_empr_notice_def=='1') $p_rad.= "checked='checked' ";
						$p_rad.= ">".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)." $p_sel</input>";
						$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
					} else {
						$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_2->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
					}
										
					//droits/profils utilisateurs
					if($user_rights & 1) {
						$r_rad = "<input type='radio' name='r_rad[2]' value='R' ";
						if ($gestion_acces_empr_notice_def!='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</input><input type='radio' name='r_rad[2]' value='C' ";
						if ($gestion_acces_empr_notice_def=='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)."</input>";
						$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
					}
							
					//recuperation profils utilisateurs
					$t_u=array();
					$t_u[0]= $dom_2->getComment('user_prf_def_lib');	//niveau par defaut
					$qu=$dom_2->loadUsedUserProfiles();
					$ru=pmb_mysql_query($qu, $dbh);
					if (pmb_mysql_num_rows($ru)) {
						while(($row=pmb_mysql_fetch_object($ru))) {
					        $t_u[$row->prf_id]= $row->prf_name;
						}
					}
				
					//recuperation des controles dependants de l'utilisateur
					$t_ctl=$dom_2->getControls(0);
		
					//recuperation des droits 
					$t_rights = $dom_2->getResourceRights($this->id);
									
					if (count($t_u)) {
		
						$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
						foreach($t_u as $k=>$v) {
							$h_tab.= "<th class='dom_col'>".htmlentities($v, ENT_QUOTES, $charset)."</th>";			
						}
						$h_tab.="</tr><!-- rights_tab --></table></div>";
						
						$c_tab = '<tr>';
						foreach($t_u as $k=>$v) {
								
							$c_tab.= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
							$t_rows = "";
									
							foreach($t_ctl as $k2=>$v2) {
															
								$t_rows.="
									<tr>
										<td style='width:25px;' ><input type='checkbox' name='chk_rights[2][".$k."][".$k2."]' value='1' ";
								if ($t_rights[$k][$res_prf] & (pow(2,$k2-1))) {
									$t_rows.= "checked='checked' ";
								}
								if(($user_rights & 1)==0) $t_rows.="disabled='disabled' "; 
								$t_rows.="/></td>
										<td>".htmlentities($v2, ENT_QUOTES, $charset)."</td>
									</tr>";
							}						
							$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
						}
						$c_tab.= "</tr>";
						
					}
					$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);;
					$r_form=str_replace('<!-- rights_tab -->', $h_tab, $r_form);
					
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
					$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
				}
				$form.= $r_form;
				
			}
			return $form;
		}

		
		// ---------------------------------------------------------------
		//		replace_form : affichage du formulaire de remplacement
		// ---------------------------------------------------------------
		function replace_form() {
			global $notice_replace;
			global $msg;
			global $include_path;
			global $deflt_notice_replace_keep_categories;
			global $notice_replace_categories, $notice_replace_category;
			global $thesaurus_mode_pmb;
		
			// a completer
			if(!$this->id) {
				require_once("$include_path/user_error.inc.php");
				error_message($msg[161], $msg[162], 1, './catalog.php');
				return false;
			}
		
			$notice_replace=str_replace('!!old_notice_libelle!!', $this->tit1." - ".$this->code, $notice_replace);
			$notice_replace=str_replace('!!id!!', $this->id, $notice_replace);
			if ($deflt_notice_replace_keep_categories && sizeof($this->categories)) {
				// categories
				$categories_to_replace = "";
				for ($i = 0 ; $i < sizeof($this->categories) ; $i++) {
					$categ_id = $this->categories[$i]["categ_id"] ;
					$categ = new category($categ_id);
					$ptab_categ = str_replace('!!icateg!!', $i, $notice_replace_category) ;
					$ptab_categ = str_replace('!!categ_id!!', $categ_id, $ptab_categ);
					if ($thesaurus_mode_pmb) $nom_thesaurus='['.$categ->thes->getLibelle().'] ' ;
					else $nom_thesaurus='' ;
					$ptab_categ = str_replace('!!categ_libelle!!',	htmlentities($nom_thesaurus.$categ->catalog_form,ENT_QUOTES, $charset), $ptab_categ);
					$categories_to_replace .= $ptab_categ ;
				}
				$notice_replace_categories=str_replace('!!notice_replace_category!!', $categories_to_replace, $notice_replace_categories);
				$notice_replace_categories=str_replace('!!nb_categ!!', sizeof($this->categories), $notice_replace_categories);
				
				$notice_replace=str_replace('!!notice_replace_categories!!', $notice_replace_categories, $notice_replace);
			} else {
				$notice_replace=str_replace('!!notice_replace_categories!!', "", $notice_replace);
			}
			print $notice_replace;
			return true;
		}
		
		// ---------------------------------------------------------------
		//		replace($by) : remplacement de la notice
		// ---------------------------------------------------------------
		function replace($by,$supp_notice=true) {
			global $msg;
			global $dbh;
			global $keep_categories;
			
			if($this->id == $by) {
				return $msg[223];
			}
			if (($this->id == $by) || (!$this->id)) {
				return $msg[223];
			}
		
			$by_notice= new notice($by);
			if ($this->biblio_level != $by_notice->biblio_level || $this->hierar_level != $by_notice->hierar_level) {
				return $msg[catal_rep_not_err1];
			}
			
			// traitement des catégories (si conservation cochée)
			if ($keep_categories) {
				update_notice_categories_from_form($by);
			}
			
			// remplacement dans les exemplaires numériques
			$requete = "UPDATE explnum SET explnum_notice='$by' WHERE explnum_notice='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			// remplacement dans les exemplaires
			$requete = "UPDATE exemplaires SET expl_notice='$by' WHERE expl_notice='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			// remplacement dans les depouillements
			$requete = "UPDATE analysis SET analysis_notice='$by' WHERE analysis_notice='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			// remplacement dans les bulletins
			$requete = "UPDATE bulletins SET bulletin_notice='$by' WHERE bulletin_notice='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			// remplacement dans les notices filles
			/*$requete = "UPDATE notices_relations SET num_notice='$by' WHERE num_notice='$this->id' ";
			@pmb_mysql_query($requete, $dbh);
			$requete = "UPDATE notices_relations SET linked_notice='$by' WHERE linked_notice='$this->id' ";
			@pmb_mysql_query($requete, $dbh);*/
			
			// remplacement dans les resas
			$requete = "UPDATE resa SET resa_idnotice='$by' WHERE resa_idnotice='$this->id' ";
			pmb_mysql_query($requete, $dbh);
			
			$req="UPDATE notices_authperso SET notice_authperso_notice_num='$by' where notice_authperso_notice_num='$this->id' ";
			pmb_mysql_query($req, $dbh);
			
			//Suppression de la notice
			if($supp_notice){
				notice::del_notice($this->id);
			}
			return FALSE;
		}
		
		static function del_notice ($id) {

			global $dbh,$class_path,$pmb_synchro_rdf,$pmb_notice_img_folder_id;
			
			//Suppression de la vignette de la notice si il y en a une d'uploadée
			if($pmb_notice_img_folder_id){
				$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
				$res = pmb_mysql_query($req,$dbh);
				if(pmb_mysql_num_rows($res)){
					$rep=pmb_mysql_fetch_object($res);
					$img=$rep->repertoire_path."img_".$id;
					@unlink($img);
				}
			}
			
			//synchro_rdf (à laisser en premier : a besoin des éléments de la notice pour retirer du graphe rdf)
			if($pmb_synchro_rdf){				
				$synchro_rdf = new synchro_rdf();
				$synchro_rdf->delRdf($id,0);
			}
			
			$p_perso=new parametres_perso("notices");
			$p_perso->delete_values($id);
			
			$requete = "DELETE FROM notices_categories WHERE notcateg_notice='$id'" ;
			@pmb_mysql_query($requete, $dbh);
		
			$requete = "DELETE FROM notices_langues WHERE num_notice='$id'" ;
			@pmb_mysql_query($requete, $dbh);
			
			$requete = "DELETE FROM notices WHERE notice_id='$id'" ;
			@pmb_mysql_query($requete, $dbh);
			audit::delete_audit (AUDIT_NOTICE, $id) ;
			
			// Effacement de l'occurence de la notice ds la table notices_global_index :
			$requete = "DELETE FROM notices_global_index WHERE num_notice=".$id;
			@pmb_mysql_query($requete, $dbh);
			
			// Effacement des occurences de la notice ds la table notices_mots_global_index :
			$requete = "DELETE FROM notices_mots_global_index WHERE id_notice=".$id;
			@pmb_mysql_query($requete, $dbh);
			
			// Effacement des occurences de la notice ds la table notices_fields_global_index :
			$requete = "DELETE FROM notices_fields_global_index WHERE id_notice=".$id;
			@pmb_mysql_query($requete, $dbh);
			
			$requete = "delete from notices_relations where num_notice='$id' OR linked_notice='$id' ";
			@pmb_mysql_query($requete, $dbh);
					
			// elimination des docs numeriques
			$req_explNum = "select explnum_id from explnum where explnum_notice=".$id." ";
			$result_explNum = @pmb_mysql_query($req_explNum, $dbh);
			while(($explNum = pmb_mysql_fetch_object($result_explNum))) {
				$myExplNum = new explnum($explNum->explnum_id);
				$myExplNum->delete();		
			}
			
			$requete = "DELETE FROM responsability WHERE responsability_notice='$id'" ;
			@pmb_mysql_query($requete, $dbh);
				
			$requete = "DELETE FROM bannette_contenu WHERE num_notice='$id'" ;
			@pmb_mysql_query($requete, $dbh);
				
			$requete = "delete from caddie_content using caddie, caddie_content where caddie_id=idcaddie and type='NOTI' and object_id='".$id."' ";
			@pmb_mysql_query($requete, $dbh);
			
			$requete = "delete from analysis where analysis_notice='".$id."' ";
			@pmb_mysql_query($requete, $dbh);

			$requete = "update bulletins set num_notice=0 where num_notice='".$id."' ";
			@pmb_mysql_query($requete, $dbh);	
			
			//Suppression de la reference a la notice dans la table suggestions
			$requete = "UPDATE suggestions set num_notice = 0 where num_notice=".$id;
			@pmb_mysql_query($requete, $dbh);	
			
			//Suppression de la reference a la notice dans la table lignes_actes
			$requete = "UPDATE lignes_actes set num_produit=0, type_ligne=0 where num_produit='".$id."' and type_ligne in ('1','5') ";
			@pmb_mysql_query($requete, $dbh);	
				
			//suppression des droits d'acces user_notice
			$requete = "delete from acces_res_1 where res_num=".$id;
			@pmb_mysql_query($requete, $dbh);	
			
			// suppression des tags
			$rqt_del = "delete from tags where num_notice=".$id;
			@pmb_mysql_query($rqt_del, $dbh);
			
			//suppression des avis
			$requete = "delete from avis where num_notice=".$id;
			@pmb_mysql_query($requete, $dbh);
			
			//suppression des droits d'acces empr_notice
			$requete = "delete from acces_res_2 where res_num=".$id;
			@pmb_mysql_query($requete, $dbh);	
						
			// Supression des liens avec les titres uniformes
			$requete = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice='$id'" ;			
			@pmb_mysql_query($requete, $dbh);	
			
			//Suppression dans les listes de lecture partagées
			$requete = "SELECT id_liste, notices_associees from opac_liste_lecture" ;			
			$res=pmb_mysql_query($requete, $dbh);
			$id_tab=array();
			while(($notices=pmb_mysql_fetch_object($res))){
				$id_tab = explode(',',$notices->notices_associees);
				for($i=0;$i<sizeof($id_tab);$i++){
					if($id_tab[$i] == $id){
						unset($id_tab[$i]);
					}
				}
				$requete = "UPDATE opac_liste_lecture set notices_associees='".addslashes(implode(',',$id_tab))."' where id_liste='".$notices->id_liste."'";
				pmb_mysql_query($requete,$dbh);
			}
			
			// Suppression des résas 
			$requete = "DELETE FROM resa WHERE resa_idnotice=".$id;
			pmb_mysql_query($requete, $dbh);
			
			// Suppression des transferts_demande			
			$requete = "DELETE FROM transferts_demande using transferts_demande, transferts WHERE num_transfert=id_transfert and num_notice=".$id;
			pmb_mysql_query($requete, $dbh);
			// Suppression des transferts
			$requete = "DELETE FROM transferts WHERE num_notice=".$id;
			pmb_mysql_query($requete, $dbh);
			
			//si intégré depuis une source externe, on supprime aussi la référence
			$query="delete from notices_externes where num_notice=".$id;
			@pmb_mysql_query($query, $dbh);
			
			$req="delete from notices_authperso where notice_authperso_notice_num=".$id;
			pmb_mysql_query($req, $dbh);
			
			//Suppression des emprises liées à la notice
			$req = "select map_emprise_id from map_emprises where map_emprise_type=11 and map_emprise_obj_num=".$id;
			$result = pmb_mysql_query($req, $dbh);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$query="delete from map_emprises where map_emprise_obj_num=".$id." and map_emprise_type=11";
				pmb_mysql_query($query, $dbh);
				$req_areas="delete from map_hold_areas where type_obj=11 and id_obj=".$row->map_emprise_id;
				pmb_mysql_query($req_areas,$dbh);
			}		
			$query = "update docwatch_items set item_num_notice=0 where item_num_notice = ".$id;
			pmb_mysql_query($query, $dbh);
			
			// Nettoyage indexation concepts
			$index_concept = new index_concept($id, TYPE_NOTICE);
			$index_concept->delete();
		}
		
		
		//sauvegarde un ensemble de notices dans un entrepot agnostique a partir d'un tableau d'ids de notices
		static function save_to_agnostic_warehouse($notice_ids=array(),$source_id=0,$keep_expl=0) {
			global $base_path,$class_path,$include_path;
			
			if (is_array($notice_ids) && count($notice_ids) && $source_id*1) {
				
				$export_params=array(
					'genere_lien'	=>1,
					'notice_mere'	=>1,
					'notice_fille'	=>1,
					'mere'			=>0,
					'fille'			=>0,
					'bull_link'		=>1,
					'perio_link'	=>1,
					'art_link'		=>0,
					'bulletinage'	=>0,
					'notice_perio'	=>0,
					'notice_art'	=>0
				);

				require_once($base_path.'/admin/convert/export.class.php');
				require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
				$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
				$source_params = $conn->get_source_params($source_id);
				$export_params['docnum']=1;
				$export_params['docnum_rep']=$source_params['REP_UPLOAD'];
				$notice_ids=array_unique($notice_ids);
				$e=new export($notice_ids);
				$records=array();
				do{
					$nn = $e->get_next_notice('',array(),array(),$keep_expl,$export_params);
					if ($e->notice) $records[] = $e->xml_array;
				} while($nn);
				$conn->rec_records_from_xml_array($records,$source_id);
			}
		}	

		
		// Donne les id des notices liés a une notice		
		static function get_list_child($notice_id,$liste=array()){
			$tab=array();
			$liste[]=$notice_id;
			$requete="select num_notice as notice_id from notices_relations where linked_notice=".$notice_id." order by rank";						
			$res_child=@pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res_child)) {
				while (($child=pmb_mysql_fetch_object($res_child))) {
					if(!in_array($child->notice_id,$liste)) {
						$liste[]=$child->notice_id;
						$tab_tmp=notice::get_list_child($child->notice_id,$liste);					
						$tab=array_merge($tab,$tab_tmp);	
					}else {
						// cas de rebouclage d'une fille sur une mère: donc on sort.  
						$tab[]=$notice_id;
						return	$tab;				
					}
				}	
				pmb_mysql_free_result($res_child);
			}	
			$tab[]=$notice_id;
			return	$tab;
		}	
		
		static function majNotices_clean_tags($notice=0,$with_reindex=true) {
			global $dbh;

			$requete = "select index_l ,notice_id from notices where index_l is not null and index_l!='' ";
			if($notice) {				
				$requete.= " and notice_id = $notice ";
			}			
			$res = pmb_mysql_query($requete, $dbh);
			if($res && pmb_mysql_num_rows($res)){
				while (($r = pmb_mysql_fetch_object($res))) {
					$val=clean_tags($r->index_l);
					$requete = "update notices set index_l='".addslashes($val)."' where notice_id=".$r->notice_id;
					pmb_mysql_query($requete, $dbh);
					if($with_reindex && ($val != $r->index_l)){//On réindexe la notice si le nettoyage à réalisé des changements
						notice::majNoticesTotal($r->notice_id);
					}
				}
			}
		}	
						
		// Fonction statique pour la creation / maj d'un n-uplet dans la table "notices_global_index" lors de la creation ou mise a jour d'une notice.
		static function majNoticesGlobalIndex($notice, $NoIndex = 1) {
			global $dbh;
			
			pmb_mysql_query("delete from notices_global_index where num_notice = ".$notice." AND no_index = ".$NoIndex,$dbh);
			$titres = pmb_mysql_query("select index_serie, tnvol, index_wew, index_sew, index_l, index_matieres, n_gen, n_contenu, n_resume, index_n_gen, index_n_contenu, index_n_resume, eformat, niveau_biblio from notices where notice_id = ".$notice, $dbh);
		   	$mesNotices = pmb_mysql_fetch_assoc($titres);
			$tit = $mesNotices['index_wew'];
			$indTit = $mesNotices['index_sew'];
			$indMat = $mesNotices['index_matieres'];
			$indL = $mesNotices['index_l'];
			$indResume = $mesNotices['index_n_resume'];
			$indGen = $mesNotices['index_n_gen'];
			$indContenu = $mesNotices['index_n_contenu'];
			$resume = $mesNotices['n_resume'];
			$gen = $mesNotices['n_gen'];
			$contenu = $mesNotices['n_contenu'];
			$indSerie = $mesNotices['index_serie'];
			$tvol = $mesNotices['tnvol'];
			$eformatlien = $mesNotices['eformat'];
		   	$infos_global=' '.$tvol.' '.$tit.' '.$resume.' '.$gen.' '.$contenu.' '.$indL.' ';
		   	$infos_global_index=' '.$indSerie.' '.$indTit.' '.$indResume.' '.$indGen.' '.$indContenu.' '.$indMat.' ';
			
		   	// Authors : 
		   	$auteurs = pmb_mysql_query("select author_id, author_type, author_name, author_rejete, author_date, author_lieu,author_ville,author_pays,author_numero,author_subdivision, index_author from authors, responsability WHERE responsability_author = author_id AND responsability_notice = $notice", $dbh);
		   	$numA = pmb_mysql_num_rows($auteurs);		   	
		   	$aut_pperso= new aut_pperso("author");
		   	for($j=0;$j < $numA; $j++) {
		   		$mesAuteurs = pmb_mysql_fetch_assoc($auteurs);
		   		$infos_global.= 
		   			$mesAuteurs['author_name'].' '.
			   		$mesAuteurs['author_rejete'].' '.
			   		$mesAuteurs['author_lieu'].' '.
			   		$mesAuteurs['author_ville'].' '.
			   		$mesAuteurs['author_pays'].' '.
			   		$mesAuteurs['author_numero'].' '.
			   		$mesAuteurs['author_subdivision'].' ';
			   	if($mesAuteurs['author_type'] == "72") $infos_global.= ' '.$mesAuteurs['author_date'].' ';
			   	$infos_global_index.=strip_empty_chars(
			   		$mesAuteurs['author_name'].' '.
			   		$mesAuteurs['author_rejete'].' '.
			   		$mesAuteurs['author_lieu'].' '.
			   		$mesAuteurs['author_ville'].' '.
			   		$mesAuteurs['author_pays'].' '.
			   		$mesAuteurs['author_numero'].' '.
			   		$mesAuteurs['author_subdivision']).' ';
			   	if($mesAuteurs['author_type'] == "72") $infos_global_index.= strip_empty_chars($mesAuteurs['author_date']." ");
			   	
			   	$mots_perso=$aut_pperso->get_fields_recherche($mesAuteurs['author_id']);
			   	if($mots_perso) {
			   		$infos_global.= $mots_perso.' ';
			   		$infos_global_index.= strip_empty_words($mots_perso).' ';
			   	}
		   	}
		   	pmb_mysql_free_result($auteurs);
		   	
		   	// Nom du periodique 
			//cas d'un article
		   	if($mesNotices['niveau_biblio'] == 'a'){
			   	$temp = pmb_mysql_query("select bulletin_notice, bulletin_titre, index_titre, index_wew, index_sew from analysis, bulletins, notices  WHERE analysis_notice=".$notice." and analysis_bulletin = bulletin_id and bulletin_notice=notice_id", $dbh);
			   	$numP = pmb_mysql_num_rows($temp);
			   	if ($numP) {
					// La notice appartient a un periodique, on selectionne le titre de periodique :
			   		$mesTemp = pmb_mysql_fetch_assoc($temp);
				  	$infos_global.= $mesTemp['index_wew'].' '.$mesTemp['bulletin_titre'].' '.$mesTemp['index_titre'].' ';
				  	$infos_global_index.=strip_empty_words($mesTemp['index_wew'].' '.$mesTemp['bulletin_titre'].' '.$mesTemp['index_titre']).' ';		   		
			   	}
			   	pmb_mysql_free_result($temp);
			   //cas d'un bulletin
		   	}else if ($mesNotices['niveau_biblio'] == 'b'){
		   		$temp = pmb_mysql_query("select serial.index_wew from notices join bulletins on bulletins.num_notice = notices.notice_id join notices as serial on serial.notice_id = bulletins.bulletin_notice where notices.notice_id = ".$notice);
		   		$numP = pmb_mysql_num_rows($temp);
			   	if ($numP) {
					// La notice appartient a un periodique, on selectionne le titre de periodique :
			   		$mesTemp = pmb_mysql_fetch_assoc($temp);
				  	$infos_global.= $mesTemp['index_wew'].' ';
				  	$infos_global_index.=strip_empty_words($mesTemp['index_wew']);	   		
			   	}
			   	pmb_mysql_free_result($temp);
		   	}
		   	
		   	
		   	// Categories : 
		   	$aut_pperso= new aut_pperso("categ");
		   	$noeud = pmb_mysql_query("select notices_categories.num_noeud as categ_id, libelle_categorie from notices_categories,categories where notcateg_notice = ".$notice." and notices_categories.num_noeud=categories.num_noeud order by ordre_categorie", $dbh);
		   	$numNoeuds = pmb_mysql_num_rows($noeud);
		   	// Pour chaque noeud trouve on cherche les noeuds parents et les noeuds fils :
		   	for($j=0;$j < $numNoeuds; $j++) {
		   		// On met a jour la table notices_global_index avec le noeud trouve:
			 	$mesNoeuds = pmb_mysql_fetch_assoc($noeud);
			   	$infos_global.= $mesNoeuds['libelle_categorie'].' ';
			 	$infos_global_index.= strip_empty_words($mesNoeuds['libelle_categorie']).' ';
			 	
			 	$mots_perso=$aut_pperso->get_fields_recherche($mesNoeuds['categ_id']);
			 	if($mots_perso) {
			 		$infos_global.= $mots_perso.' ';
			 		$infos_global_index.= strip_empty_words($mots_perso).' ';
			 	}
		   	}
		   	
		   	// Sous-collection : 
		   	$aut_pperso= new aut_pperso("subcollection");
		   	$subColls = pmb_mysql_query("select subcoll_id, sub_coll_name, index_sub_coll from notices, sub_collections WHERE subcoll_id = sub_coll_id AND notice_id = ".$notice, $dbh);
		   	$numSC = pmb_mysql_num_rows($subColls);
		   	for($j=0;$j < $numSC; $j++) {
		   		$mesSubColl = pmb_mysql_fetch_assoc($subColls);
		   		$infos_global.=$mesSubColl['index_sub_coll'].' '.$mesSubColl['sub_coll_name'].' ';
		   		$infos_global_index.=strip_empty_words($mesSubColl['index_sub_coll'].' '.$mesSubColl['sub_coll_name']).' ';
		   		
		   		$mots_perso=$aut_pperso->get_fields_recherche($mesSubColl['subcoll_id']);
			 	if($mots_perso) {
			 		$infos_global.= $mots_perso.' ';
			 		$infos_global_index.= strip_empty_words($mots_perso).' ';
			 	}	   		
		   	}
		   	pmb_mysql_free_result($subColls);
		   	
		   	// Indexation numerique : 
		   	$aut_pperso= new aut_pperso("indexint");
		   	$indexNums = pmb_mysql_query("select indexint_id, indexint_name, indexint_comment from notices, indexint WHERE indexint = indexint_id AND notice_id = ".$notice, $dbh);
		   	$numIN = pmb_mysql_num_rows($indexNums);
		   	for($j=0;$j < $numIN; $j++) {
		   		$mesindexNums = pmb_mysql_fetch_assoc($indexNums);
		   		$infos_global.=$mesindexNums['indexint_name'].' '.$mesindexNums['indexint_comment'].' ';
		   		$infos_global_index.=strip_empty_words($mesindexNums['indexint_name'].' '.$mesindexNums['indexint_comment']).' ';
		   		
		   		$mots_perso=$aut_pperso->get_fields_recherche($mesindexNums['indexint_id']);
			 	if($mots_perso) {
			 		$infos_global.= $mots_perso.' ';
			 		$infos_global_index.= strip_empty_words($mots_perso).' ';
			 	}	   		
		   	}
		   	pmb_mysql_free_result($indexNums);
		   	
		   	// Collection : 
		   	$aut_pperso= new aut_pperso("collection");
		   	$Colls = pmb_mysql_query("select coll_id, collection_name ,collection_issn from notices, collections WHERE coll_id = collection_id AND notice_id = ".$notice, $dbh);
		   	$numCo = pmb_mysql_num_rows($Colls);
		   	for($j=0;$j < $numCo; $j++) {
		   		$mesColl = pmb_mysql_fetch_assoc($Colls);
		   		$infos_global.= $mesColl['collection_name'].' '.$mesColl['collection_issn'].' ';
		   		$infos_global_index.=strip_empty_words($mesColl['collection_name']).' '.strip_empty_words($mesColl['collection_issn']).' ';
		   		
		   		$mots_perso=$aut_pperso->get_fields_recherche($mesColl['coll_id']);
			 	if($mots_perso) {
			 		$infos_global.= $mots_perso.' ';
			 		$infos_global_index.= strip_empty_words($mots_perso).' ';
			 	}	   		
		   	}
		   	pmb_mysql_free_result($Colls);
		   			   	
		   	// Editeurs : 
		   	$aut_pperso= new aut_pperso("publisher");
		   	$editeurs = pmb_mysql_query("select ed_id, ed_name from notices, publishers WHERE (ed1_id = ed_id OR ed2_id = ed_id) AND notice_id = ".$notice, $dbh);
		   	$numE = pmb_mysql_num_rows($editeurs);
		   	for($j=0;$j < $numE; $j++) {
		   		$mesEditeurs = pmb_mysql_fetch_assoc($editeurs);		   		
		   		$infos_global.= $mesEditeurs['ed_name'].' ';
		   		$infos_global_index.=strip_empty_chars($mesEditeurs['ed_name']).' ';
		   		
		   		$mots_perso=$aut_pperso->get_fields_recherche($mesEditeurs['ed_id']);
			 	if($mots_perso) {
			 		$infos_global.= $mots_perso.' ';
			 		$infos_global_index.= strip_empty_words($mots_perso).' ';
			 	}	   		
		   	}
		   	pmb_mysql_free_result($editeurs);
		  
			pmb_mysql_free_result($titres);

			// Titres Uniformes : 
		   	$aut_pperso= new aut_pperso("tu");
		   	$tu = pmb_mysql_query("select tu_id, ntu_titre, tu_name, tu_tonalite, tu_sujet, tu_lieu, tu_contexte from notices_titres_uniformes,titres_uniformes WHERE tu_id=ntu_num_tu and ntu_num_notice=".$notice, $dbh);
		   	if(pmb_mysql_error()=="" && pmb_mysql_num_rows($tu)){
		   		$numtu = pmb_mysql_num_rows($tu);
		   		for($j=0;$j < $numtu; $j++) {
		   			$mesTu = pmb_mysql_fetch_assoc($tu);
		   			$infos_global.=$mesTu['ntu_titre'].' '.$mesTu['tu_name'].' '.$mesTu['tu_tonalite'].' '.$mesTu['tu_sujet'].' '.$mesTu['tu_lieu'].' '.$mesTu['tu_contexte'].' ';
		   			$infos_global_index.=strip_empty_words($mesTu['ntu_titre'].' '.$mesTu['tu_name'].' '.$mesTu['tu_tonalite'].' '.$mesTu['tu_sujet'].' '.$mesTu['tu_lieu'].' '.$mesTu['tu_contexte']).' ';
		   			 
		   			$mots_perso=$aut_pperso->get_fields_recherche($mesTu['tu_id']);
		   			if($mots_perso) {
		   				$infos_global.= $mots_perso.' ';
		   				$infos_global_index.= strip_empty_words($mots_perso).' ';
		   			}
		   		}
		   		pmb_mysql_free_result($tu);
		   	}		   	
		   	
			// indexer les cotes des etat des collections : 
			$p_perso=new parametres_perso("collstate");	
		   	$coll = pmb_mysql_query("select collstate_id, collstate_cote from collections_state WHERE id_serial=".$notice, $dbh);
		   	$numcoll = pmb_mysql_num_rows($coll);
		   	for($j=0;$j < $numcoll; $j++) {
		   		$mescoll = pmb_mysql_fetch_assoc($coll);		   		
		   		$infos_global.=$mescoll['collstate_cote'].' ';
		   		$infos_global_index.=strip_empty_words($mescoll['collstate_cote']).' ';	
		   		// champ perso cherchable		   	
				$mots_perso=$p_perso->get_fields_recherche($mescoll['collstate_id']);
				if($mots_perso) {
					$infos_global.= $mots_perso.' ';
					$infos_global_index.= strip_empty_words($mots_perso).' ';	
				}		   			   		
		   	}
		   	pmb_mysql_free_result($coll);	
	
		   	// Nomenclature		   	
		   	global $pmb_nomenclature_activate;
		   	if($pmb_nomenclature_activate){
		   		$mots=nomenclature_record_ui::get_index($notice);
		   		$infos_global.= $mots.' ';
		   		$infos_global_index.= strip_empty_words($mots).' ';
		   	}
		   	
		    // champ perso cherchable
		   	$p_perso=new parametres_perso("notices");	
			$mots_perso=$p_perso->get_fields_recherche($notice);
			if($mots_perso) {
				$infos_global.= $mots_perso.' ';
				$infos_global_index.= strip_empty_words($mots_perso).' ';	
			}
			
			// champs des authperso
			$auth_perso=new authperso_notice($notice);
			$mots_authperso=$auth_perso->get_fields_search();
			if($mots_authperso) {
				$infos_global.= $mots_authperso.' ';
				$infos_global_index.= strip_empty_words($mots_authperso).' ';	
			}
			
			// flux RSS éventuellement
			$eformat=array();
			$eformat = explode(' ', $eformatlien) ;
			if ($eformat[0]=='RSS' && $eformat[3]=='1') {
				$flux=strip_tags(affiche_rss($notice)) ;
				$infos_global_index.= strip_empty_words($flux).' ';
			}
			pmb_mysql_query("insert into notices_global_index SET num_notice=".$notice.",no_index =".$NoIndex.", infos_global='".addslashes($infos_global)."', index_infos_global='".addslashes($infos_global_index)."'" , $dbh);
		}
		
		
		// Fonction statique pour la creation / maj d'un n-uplet dans la table "notices_mots_global_index" lors de la creation ou mise a jour d'une notice.
		static function majNoticesMotsGlobalIndex($notice, $datatype='all') {
			global $include_path;
			global $dbh, $champ_base;
			global $lang;
			global $indexation_lang;
			
			//recuperation du fichier xml de configuration
			if(!count($champ_base)) {
				$file = $include_path."/indexation/notices/champs_base_subst.xml";
				if(!file_exists($file)){
					$file = $include_path."/indexation/notices/champs_base.xml";
				}
				$fp=fopen($file,"r");
	    		if ($fp) {
					$xml=fread($fp,filesize($file));
				}
				fclose($fp);
				$champ_base=_parser_text_no_function_($xml,"INDEXATION");
			}
			$tableau=$champ_base;
			
			//analyse des donnees des tables
			$temp_not=array();
			$temp_not['t'][0][0]=$tableau['REFERENCE'][0][value] ;
			$temp_ext=array();
			$temp_marc=array();
			$champ_trouve=false;
			$tab_code_champ = array();
			$tab_languages=array();
			$tab_keep_empty = array();
			$tab_pp=array();
			$tab_authperso=array();
			$authperso_code_champ_start=0;
			$isbd_ask_list=array();
			for ($i=0;$i<count($tableau['FIELD']);$i++) { //pour chacun des champs decrits
				
				//recuperation de la liste des informations a mettre a jour
				if ( $datatype=='all' || ($datatype==$tableau['FIELD'][$i]['DATATYPE']) ) {
					//conservation des mots vides
					if($tableau['FIELD'][$i]['KEEPEMPTYWORD'] == "yes"){
						$tab_keep_empty[]=$tableau['FIELD'][$i]['ID'];
					}
					//champ perso
					if($tableau['FIELD'][$i]['DATATYPE'] == "custom_field"){
						$tab_pp[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i]['TABLE'][0]['value'];
					//autorité perso	
					}elseif($tableau['FIELD'][$i]['DATATYPE'] == "authperso"){
						$tab_authperso[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i]['TABLE'][0]['value'];	
						$authperso_code_champ_start=$tableau['FIELD'][$i]['ID'];
						$authpersos = new authperso_notice($notice);
					}else if ($tableau['FIELD'][$i]['EXTERNAL']=="yes") {
						//champ externe à la table notice
						//Stockage de la structure pour un accès plus facile
						$temp_ext[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i];
					} else {
						//champ de la table notice
						$temp_not['f'][0][$tableau['FIELD'][$i]['ID']]= $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
						$tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
							'champ' => $tableau['FIELD'][$i]['ID'],
							'ss_champ' => 0,
							'pond' => $tableau['FIELD'][$i]['POND'],
							'no_words' => ($tableau['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false),
							'internal' => 1,
							'use_global_separator' => $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR']
						);
						if($tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE']){
							$tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype']=$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
							$temp_not['f'][0][$tableau['FIELD'][$i]['ID']."_marc"]=$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']." as "."subst_for_marc_".$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
						}
					}
					if($tableau['FIELD'][$i]['ISBD']){ // isbd autorités						
						$isbd_ask_list[$tableau['FIELD'][$i]['ID']]= array(
							'champ' => $tableau['FIELD'][$i]['ID'],
							'ss_champ' => $tableau['FIELD'][$i]['ISBD'][0]['ID'],
							'pond' => $tableau['FIELD'][$i]['ISBD'][0]['POND'],
							'class_name' => $tableau['FIELD'][$i]['ISBD'][0]['CLASS_NAME']
						);
					}
					$champ_trouve=true;
				}
			}
			if ($champ_trouve) {

				$tab_req=array();
				
				//Recherche des champs directs
				if($datatype=='all') {
					$tab_req[0]["rqt"]= "select ".implode(',',$temp_not['f'][0])." from ".$temp_not['t'][0][0];
					$tab_req[0]["rqt"].=" where ".$tableau['REFERENCEKEY'][0][value]."='".$notice."'";
					$tab_req[0]["table"]=$temp_not['t'][0][0];
				}
				foreach($temp_ext as $k=>$v) {
					$isbd_tab_req=array();
					$no_word_field=false;
					//Construction de la requete
					//Champs pour le select
					$select=array();
					//on harmonise les fichiers XML décrivant des requetes...
					for ($i = 0; $i<count($v["TABLE"]); $i++) {
						$table = $v['TABLE'][$i];	
						$select=array();
						if(count($table['TABLEFIELD'])){
							$use_word=true;
						}else{
							$use_word=false;
						}
						if($table['IDKEY'][0]){
 							$select[]=$table['NAME'].".".$table['IDKEY'][0]['value']." as subst_for_autorite_".$table['IDKEY'][0]['value'];
						}
						for ($j=0;$j<count($table['TABLEFIELD']);$j++) {
							$select[]=($table['ALIAS'] ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["value"];
							if($table['LANGUAGE']){
								$select[]=$table['LANGUAGE'][0]['value'];
								$tab_languages[$k]=$table['LANGUAGE'][0]['value'];
							}
							$field_name = $table['TABLEFIELD'][$j]["value"];
							if(strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")!== false){//Pour le cas où l'on a besoin de nommer un champ et d'utiliser un alias
								$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")+4);
							}elseif(strpos($table['TABLEFIELD'][$j]["value"],".")!== false){
								$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos($table['TABLEFIELD'][$j]["value"],".")+1);
							}
							$field_name=trim($field_name);
							$tab_code_champ[$v['ID']][$field_name] = array(
								'champ' => $v['ID'],
								'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
								'pond' => $table['TABLEFIELD'][$j]['POND'],
								'no_words' => ($v['DATATYPE'] == "marclist" ? true : false),
								'autorite' =>  $table['IDKEY'][0]['value']
							);
							if($table['TABLEFIELD'][$j]['MARCTYPE']){
								$tab_code_champ[$v['ID']][$table['TABLEFIELD'][$j]["value"]]['marctype']=$table['TABLEFIELD'][$j]['MARCTYPE'];
								$select[]=$table['NAME'].".".$table['TABLEFIELD'][$j]["value"]." as subst_for_marc_".$table['TABLEFIELD'][$j]['MARCTYPE'];
							}
						}
						$query="select ".implode(",",$select)." from notices";		
						$jointure="";						
						for( $j=0 ; $j<count($table['LINK']) ; $j++){
							
							$link = $table['LINK'][$j];							 
							 
							if($link["TABLE"][0]['ALIAS']){
								$alias = $link["TABLE"][0]['ALIAS'];
							}else{
								$alias = $link["TABLE"][0]['value'];
							}
							switch ($link["TYPE"]) {
								case "n0" :
									if ($link["TABLEKEY"][0]['value']) {
										$jointure .= " LEFT JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
										if($link["EXTERNALTABLE"][0]['value']){
											$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
										}else{
											$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
										}
										$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
									} else {
										$jointure .= " LEFT JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
										$jointure .= " ON " . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
										$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
									}
									break;
								case "n1" :
									if ($link["TABLEKEY"][0]['value']) {
										$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
										if($link["EXTERNALTABLE"][0]['value']){
											$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
										}else{
											$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
										}
										$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
									} else {
										$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
										$jointure .= " ON " . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
										$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
									}
									break;
								case "1n" :
									$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
									$jointure .= " ON (" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
									$jointure .= "=" . $tableau['REFERENCE'][0]['value'] . "." . $link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
									
									
									break;
								case "nn" :
									$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
									$jointure .= " ON (" . $tableau['REFERENCE'][0]['value'] . "." .  $tableau['REFERENCEKEY'][0]['value'];
									$jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
									if ($link["TABLEKEY"][0]['value']) {
										$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
										$jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
										$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] ." ".$link["LINKRESTRICT"][0]['value']. ") ";
									} else {
										$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
										$jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
										$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value'].") ";
									}
									break;
							}
						}
						$where=" where ".$temp_not['t'][0][0].".".$tableau['REFERENCEKEY'][0][value]."=".$notice;
						if($table['FILTER']){
							foreach ( $table['FILTER'] as $filter ) {
       							if($tmp=trim($filter["value"])){
       								$where.=" AND (".$tmp.")";
       							}
							}
						}
						if($table['LANGUAGE']){
							$tab_req_lang[$k]= "select ".$table['LANGUAGE'][0]['value']." from ";
						}
						$query.=$jointure.$where;
						if($table['LANGUAGE']){
							$tab_req_lang[$k].=$jointure.$where;
						}
						if($use_word){
							$tab_req[$k]["new_rqt"]['rqt'][]=$query;	
						}
						if($isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités							
							$id_aut=$table['NAME'].".".$table["TABLEKEY"][0]['value'];							
							$req="select $id_aut as id_aut_for_isbd from notices".$jointure.$where;	
							$isbd_tab_req[]=$req;															
						}
						
					}
					if($use_word){
						$tab_req[$k]["rqt"] = implode(" union ",$tab_req[$k]["new_rqt"]['rqt']);
					}
					if($isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités						
						$req=implode(" union ",$isbd_tab_req);
						$isbd_ask_list[$k]['req']=  $req;
					}
				}
				
				//qu'est-ce qu'on efface?
				if($datatype=='all') {
					$req_del="delete from notices_mots_global_index where id_notice='".$notice."' ";
					pmb_mysql_query($req_del,$dbh);
					//la table pour les recherche exacte
					$req_del="delete from notices_fields_global_index where id_notice='".$notice."' ";
					pmb_mysql_query($req_del,$dbh);					
				}else{
					foreach ( $tab_code_champ as $subfields ) {
						foreach($subfields as $subfield){
							$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ='".$subfield['champ']."'";
							pmb_mysql_query($req_del,$dbh);
							//la table pour les recherche exacte
							$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ='".$subfield['champ']."'";
							pmb_mysql_query($req_del,$dbh);	
							break;
						}
					}
					
					//Les champs perso
					if(count($tab_pp)){
						foreach ( $tab_pp as $id ) {
	       					$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ=100 and code_ss_champ='".$id."' ";
	       					pmb_mysql_query($req_del,$dbh);
							//la table pour les recherche exacte
							$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ=100 and code_ss_champ='".$id."' ";
							pmb_mysql_query($req_del,$dbh);	
						}
					}
					//Les autorités perso
					if(count($tab_authperso)){
						$authperso_fields=$authpersos->get_index_fields_to_delete();
						foreach ( $authperso_fields as $code_champ ) {
							$code_champ+=$authperso_code_champ_start;
	       					$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ=$code_champ ";
	       					pmb_mysql_query($req_del,$dbh);
							//la table pour les recherche exacte
							$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ=$code_champ ";
							pmb_mysql_query($req_del,$dbh);	
						}
					}
				}
				
				//qu'est-ce qu'on met a jour ?
				$tab_insert=array();	
				$tab_field_insert=array();
				foreach($tab_req as $k=>$v) {	
 					$r=pmb_mysql_query($v["rqt"],$dbh);

					$tab_mots=array();
					$tab_fields=array();
					if (pmb_mysql_num_rows($r)) {
						while(($tab_row=pmb_mysql_fetch_array($r,MYSQL_ASSOC))) {
							$langage="";
							if(isset($tab_row[$tab_languages[$k]])){
								$langage = $tab_row[$tab_languages[$k]];
								unset($tab_row[$tab_languages[$k]]);
							}
							foreach($tab_row as $nom_champ => $liste_mots) {
								if(substr($nom_champ,0,10)=='subst_for_'){
									continue;
								}
								if($tab_code_champ[$k][$nom_champ]['internal']){
									$langage=$indexation_lang;									
								}															
								if($tab_code_champ[$k][$nom_champ]['marctype']){
									//on veut toutes les langues, pas seulement celle de l'interface...
									$saved_lang = $lang;
									$code = $liste_mots;
									$dir = opendir($include_path."/marc_tables");
									while($dir_lang = readdir($dir)){
										if($dir_lang!= "." && $dir_lang!=".." && $dir_lang!="CVS" && $dir_lang!=".svn" && is_dir($include_path."/marc_tables/".$dir_lang)){
											$lang = $dir_lang;
											$marclist = new marc_list($tab_code_champ[$k][$nom_champ]['marctype']);
											$liste_mots = $marclist->table[$code];
											$tab_fields[$nom_champ][] = array(
												'value' => trim($liste_mots),
												'lang' => $lang,
												'autorite' => $tab_row["subst_for_marc_".$tab_code_champ[$k][$nom_champ]['marctype']]
											);
										}
									}
									$lang = $saved_lang;
									$liste_mots = "";
								}
								if($liste_mots!='') {
									$tab_tmp=array();
									$liste_mots = strip_tags($liste_mots);
									if(!in_array($k,$tab_keep_empty)){
										$tab_tmp=explode(' ',strip_empty_words($liste_mots));
									}else{
										$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
									}
									//	if($lang!="") $tab_tmp[]=$lang;
									//la table pour les recherche exacte
									if(!$tab_fields[$nom_champ]) $tab_fields[$nom_champ]=array();
									if(!$tab_code_champ[$k][$nom_champ]['use_global_separator']){
										$tab_fields[$nom_champ][] = array(
												'value' =>trim($liste_mots),
												'lang' => $langage,
												'autorite' => $tab_row["subst_for_autorite_".$tab_code_champ[$k][$nom_champ]['autorite']]
										);
									} else {
										$var_global_sep = $tab_code_champ[$k][$nom_champ]['use_global_separator'];
										global $$var_global_sep;
										$tab_liste_mots = explode($$var_global_sep,$liste_mots);
										if(count($tab_liste_mots)){
											foreach ($tab_liste_mots as $mot) {
												$tab_fields[$nom_champ][] = array(
														'value' =>trim($mot),
														'lang' => $langage,
														'autorite' => $tab_row["subst_for_autorite_".$tab_code_champ[$k][$nom_champ]['autorite']]
												);
											}
										}
									}
									if(!$tab_code_champ[$k][$nom_champ]['no_words']){
										foreach($tab_tmp as $mot) {
											if(trim($mot)){
												$langageKey = $langage;
												if (!trim($langageKey)) {
													$langageKey = "empty";
												}
												$tab_mots[$nom_champ][$langageKey][]=$mot;
											}
										}
									}
								}
							}
						}
					}
					
					foreach ($tab_mots as $nom_champ=>$tab) {
						$memo_ss_champ="";
						$order_fields=1;
						$pos=1;
						foreach ( $tab as $langage => $mots ) {
							if ($langage == "empty") {
								$langage = "";
							}
							foreach ($mots as $mot) {
								//on cherche le mot dans la table de mot...
								$num_word = 0;
								$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
								$result = pmb_mysql_query($query);
								if(pmb_mysql_num_rows($result)){
									$num_word = pmb_mysql_result($result,0,0);
								}else{
									$dmeta = new DoubleMetaPhone($mot);
									$stemming = new stemming($mot);
									$element_to_update = "";
									if($dmeta->primary || $dmeta->secondary){
										$element_to_update.="
										double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
									}
									if($element_to_update) $element_to_update.=",";
									$element_to_update.="stem = '".$stemming->stem."'";
									
									$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
									pmb_mysql_query($query);
									$num_word = pmb_mysql_insert_id();
								}
							
								if($num_word != 0){
									$tab_insert[]="(".$notice.",".$tab_code_champ[$k][$nom_champ]['champ'].",".$tab_code_champ[$k][$nom_champ]['ss_champ'].",".$num_word.",".$tab_code_champ[$k][$nom_champ]['pond'].",$order_fields,$pos)";
									$pos++;
									if($tab_code_champ[$k][$nom_champ]['ss_champ']!= $memo_ss_champ) $order_fields++;
									$memo_ss_champ=$tab_code_champ[$k][$nom_champ]['ss_champ'];
								}
							}
						}
						
					}
					//la table pour les recherche exacte
					foreach ($tab_fields as $nom_champ=>$tab) {
						foreach($tab as $order => $values){
       						//$tab_field_insert[]="(".$notice.",".$tab_code_champ[$v["table"]][$nom_champ][0].",".$tab_code_champ[$v["table"]][$nom_champ][1].",".$order.",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$tab_code_champ[$v["table"]][$nom_champ][2].")";
       						$tab_field_insert[]="(".$notice.",".$tab_code_champ[$k][$nom_champ]['champ'].",".$tab_code_champ[$k][$nom_champ]['ss_champ'].",".($order+1).",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$tab_code_champ[$k][$nom_champ]['pond'].",'".addslashes($values['autorite'])."')";
						}
					}
				}
				//Les champs perso
				if(count($tab_pp)){
					foreach ( $tab_pp as $code_champ => $table ) {
       					$p_perso=new parametres_perso($table);
       					//on doit retrouver l'id des eléments...
       					switch($table){
       						case "expl" :
       							$rqt = "select expl_id from notices join exemplaires on expl_notice = notice_id and expl_notice!=0 where notice_id = $notice union select expl_id from notices join bulletins on num_notice = notice_id join exemplaires on expl_bulletin = bulletin_id and expl_bulletin != 0 where notice_id = $notice";
       							$res = pmb_mysql_query($rqt);
       							if(pmb_mysql_num_rows($res)) {
       								$ids = array();
       								while($row= pmb_mysql_fetch_object($res)){
										$ids[] =$row->expl_id;
       								}
       							}
       							break;
       						case "collstate" :
       							break;
       						default :
       							$ids = array($notice);
       					}
       					if(count($ids)){
       						for($i=0 ; $i<count($ids) ; $i++) {
	      						$data=$p_perso->get_fields_recherche_mot_array($ids[$i]);
	      						
		       					$j=0;
		       					$order_fields=1;
	       						foreach ( $data as $code_ss_champ => $value ) {
	       							$tab_mots=array();
	       							foreach($value as $val) {
	       								$tab_tmp=explode(' ',strip_empty_words($val));
		       							//la table pour les recherche exacte
		       							$tab_field_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$j.",'".addslashes(trim($val))."','',".$p_perso->get_pond($code_ss_champ).",0)";
	    	   							$j++;
										foreach($tab_tmp as $mot) {
											if(trim($mot)){
												$tab_mots[$mot]= "";
											}
										}
	       							}
									$pos=1;
									foreach ( $tab_mots as $mot => $langage ) {
										$num_word = 0;
										//on cherche le mot dans la table de mot...
										$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
										$result = pmb_mysql_query($query);
										if(pmb_mysql_num_rows($result)){
											$num_word = pmb_mysql_result($result,0,0);
										}else{
											$dmeta = new DoubleMetaPhone($mot);
											$stemming = new stemming($mot);
											$element_to_update = "";
											if($dmeta->primary || $dmeta->secondary){
												$element_to_update.="
												double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
											}
											if($element_to_update) $element_to_update.=",";
											$element_to_update.="stem = '".$stemming->stem."'";
											
											$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
											pmb_mysql_query($query);
											$num_word = pmb_mysql_insert_id();
										}
										if($num_word != 0){
											$tab_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$num_word.",".$p_perso->get_pond($code_ss_champ).",$order_fields,$pos)";
											$pos++;
										}
									}
									$order_fields++;
								}
       						}
       					}
					}
				}
				//Les autorités perso
				if(count($tab_authperso)){
					$order_fields=1;
					$index_fields=$authpersos->get_index_fields($notice);
					foreach ( $index_fields as $code_champ => $auth ) {
						$code_champ+=$authperso_code_champ_start;
						$tab_mots=array();
						foreach ($auth['ss_champ'] as $ss_field){
							foreach ($ss_field as $code_ss_champ =>$val){
								$tab_field_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$j.",'".addslashes(trim($val))."','',".$auth['pond'].",0)";
								
								$tab_tmp=explode(' ',strip_empty_words($val));
								foreach($tab_tmp as $mot) {
									if(trim($mot)){
										$tab_mots[$mot]= "";
									}
								}							
								$pos=1;
								foreach ( $tab_mots as $mot => $langage ) {
									$num_word = 0;
									//on cherche le mot dans la table de mot...
									$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
									$result = pmb_mysql_query($query);
									if(pmb_mysql_num_rows($result)){
										$num_word = pmb_mysql_result($result,0,0);
									}else{
										$dmeta = new DoubleMetaPhone($mot);
										$stemming = new stemming($mot);
										$element_to_update = "";
										if($dmeta->primary || $dmeta->secondary){
											$element_to_update.="
											double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
										}
										if($element_to_update) $element_to_update.=",";
										$element_to_update.="stem = '".$stemming->stem."'";
										
										$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
										pmb_mysql_query($query);
										$num_word = pmb_mysql_insert_id();
									}
									if($num_word != 0){
										$tab_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$num_word.",".$auth['pond'].",$order_fields,$pos)";
										$pos++;
									}
								}
								$order_fields++;
							}	
						}
					}					
				}
				
				if(count($isbd_ask_list)){
					// Les isbd d'autorités					
					foreach($isbd_ask_list as $infos){
						$isbd_s=array(); // cumul des isbd						
						
						$res = pmb_mysql_query($infos["req"]) or die($infos["req"]);
						if(pmb_mysql_num_rows($res)) {	
						
							switch ($infos["class_name"]){
								case 'author':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new auteur($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}								
								break;
								case 'editeur':																	
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new editeur($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}															
								break;
								case 'indexint':																	
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new indexint($row->id_aut_for_isbd);
										$isbd_s[]=$aut->display;
									}															
								break;								
								case 'collection':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new collection($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
								break;								
								case 'subcollection':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new subcollection($row->id_aut_for_isbd);
										$isbd_s[]=$aut->isbd_entry;
									}
								break;								
								case 'serie':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new serie($row->id_aut_for_isbd);
										$isbd_s[]=$aut->name;
									}
								break;							
								case 'categories':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new categories($row->id_aut_for_isbd,$lang);
										$isbd_s[]=$aut->libelle_categorie;
									}
								break;						
								case 'titre_uniforme':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new titre_uniforme($row->id_aut_for_isbd);
										$isbd_s[]=$aut->libelle;
									}
								break;
							}
						}
						$order_fields=1;
						for($i=0 ; $i<count($isbd_s) ; $i++) {
							$tab_mots=array();
							$tab_field_insert[]="(".$notice.",".$infos["champ"].",".$infos["ss_champ"].",".$order_fields.",'".addslashes(trim($isbd_s[$i]))."','',".$infos["pond"].",0)";
							
							$tab_tmp=explode(' ',strip_empty_words($isbd_s[$i]));
							foreach($tab_tmp as $mot) {
								if(trim($mot)){
									$tab_mots[$mot]= "";
								}
							}	
							$pos=1;					
							foreach ( $tab_mots as $mot => $langage ) {
								$num_word=0;
								//on cherche le mot dans la table de mot...
								$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
								$result = pmb_mysql_query($query);
								if(pmb_mysql_num_rows($result)){
									$num_word = pmb_mysql_result($result,0,0);
								}else{
									$dmeta = new DoubleMetaPhone($mot);
									$stemming = new stemming($mot);
									$element_to_update = "";
									if($dmeta->primary || $dmeta->secondary){
										$element_to_update.="
										double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
									}
									if($element_to_update) $element_to_update.=",";
									$element_to_update.="stem = '".$stemming->stem."'";
							
									$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
									pmb_mysql_query($query);
									$num_word = pmb_mysql_insert_id();
								}
								if($num_word != 0){
									$tab_insert[]="(".$notice.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$order_fields,$pos)";
									$pos++;
								}
							}
							$order_fields++;
						}	
					}
				}
				
				if(count($tab_insert)){
					$req_insert="insert ignore into notices_mots_global_index(id_notice,code_champ,code_ss_champ,num_word,pond,position, field_position) values ".implode(',',$tab_insert);
					pmb_mysql_query($req_insert,$dbh);
				}
				if(count($tab_field_insert)){
					//la table pour les recherche exacte
					$req_insert="insert ignore into notices_fields_global_index(id_notice,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert);
					pmb_mysql_query($req_insert,$dbh);
				}
				
			}
		}
		
		//Fonction statique pour la maj des champs index de la notice
		static function majNotices($notice){
			global $pmb_keyword_sep;
			if($notice){
				$query = pmb_mysql_query("SELECT notice_id,tparent_id,tit1,tit2,tit3,tit4,index_l, n_gen, n_contenu, n_resume, tnvol, indexation_lang FROM notices WHERE notice_id='".$notice."'");
				if(pmb_mysql_num_rows($query)) {
					//Nettoyage des mots clès
					notice::majNotices_clean_tags($notice,false);
					$row = pmb_mysql_fetch_object($query);										
					// titre de série
					if ($row->tparent_id) {
						$tserie = new serie($row->tparent_id);
						$ind_serie = ' '.strip_empty_words($tserie->name).' ';
					} else {
						$ind_serie = '';
					}  
					$ind_wew = $ind_serie." ".$row->tnvol." ".$row->tit1." ".$row->tit2." ".$row->tit3." ".$row->tit4 ;
					$ind_sew = strip_empty_words($ind_wew) ;
					$row->index_l ? $ind_matieres = ' '.strip_empty_words(str_replace($pmb_keyword_sep," ",$row->index_l)).' ' : $ind_matieres = '';
					$row->n_gen ? $ind_n_gen = ' '.strip_empty_words($row->n_gen).' ' : $ind_n_gen = '';
					$row->n_contenu ? $ind_n_contenu = ' '.strip_empty_words($row->n_contenu).' ' : $ind_n_contenu = '';
					$row->n_resume ? $ind_n_resume = ' '.strip_empty_words($row->n_resume).' ' : $ind_n_resume = '';
					
					
					$req_update = "UPDATE notices";
					$req_update .= " SET index_wew='".addslashes($ind_wew)."'";
					$req_update .= ", index_sew=' ".addslashes($ind_sew)." '";
					$req_update .= ", index_serie='".addslashes($ind_serie)."'";
					$req_update .= ", index_n_gen='".addslashes($ind_n_gen)."'";
					$req_update .= ", index_n_contenu='".addslashes($ind_n_contenu)."'";
					$req_update .= ", index_n_resume='".addslashes($ind_n_resume)."'";
					$req_update .= ", index_matieres='".addslashes($ind_matieres)."'";
					$req_update .= " WHERE notice_id=$row->notice_id ";
					$update = pmb_mysql_query($req_update);

					pmb_mysql_free_result($query);			
					
				}
			}		
		}
		
		static function indexation_prepare($notice){
			global $lang,$include_path;
			global $pmb_indexation_lang;
			global $empty_word;
			global $indexation_lang;
			
			$info=array();
			$info['flag_lang_change']=0;
			if(!$notice) return;
			$query = pmb_mysql_query("SELECT indexation_lang FROM notices WHERE notice_id='".$notice."'");
			if(pmb_mysql_num_rows($query)) {
				$row = pmb_mysql_fetch_object($query);
				$indexation_lang=$row->indexation_lang;
				pmb_mysql_free_result($query);
				
				if($indexation_lang && $indexation_lang!= $lang){
					$info['save_pmb_indexation_lang']=$pmb_indexation_lang;
					$info['save_lang']=$lang;
					$info['flag_lang_change']=1;
					
					$pmb_indexation_lang=$indexation_lang;
					$lang=$indexation_lang;
					$empty_word=array();
					include("$include_path/marc_tables/".$lang."/empty_words");
				}else{
					//$indexation_lang=$lang;
				}
			}
		}
		
		static function indexation_restaure($info){
			global $lang;
			global $pmb_indexation_lang;
			global $empty_word;
					
			if($info['flag_lang_change']){
				// restauration de l'environemment
				$pmb_indexation_lang=$info['save_pmb_indexation_lang'];
				$lang=$info['save_lang'];
				$empty_word=array();
				include("$include_path/marc_tables/$lang/empty_words");
			}
			$pmb_indexation_lang="";
			$flag_lang_change=0;
		}
		
		//Met à jour toutes les informations liées une notice
		static function majNoticesTotal($notice){	
			$info=notice::indexation_prepare($notice);
			notice::majNotices($notice);
			notice::majNoticesGlobalIndex($notice);
			notice::majNoticesMotsGlobalIndex($notice);
			notice::indexation_restaure($info);
		}
		
		static function getAutomaticTu($notice) {
			global $dbh,$charset,$opac_enrichment_bnf_sparql;
			
			if (!$opac_enrichment_bnf_sparql) return 0;
			
			$requete="select code, responsability_author from notices left join responsability on (responsability_notice=$notice and responsability_type=0)
			left join notices_titres_uniformes on notice_id=ntu_num_notice where notice_id=$notice and ntu_num_notice is null";
			$resultat=pmb_mysql_query($requete,$dbh);
			if (pmb_mysql_num_rows($resultat,$dbh)) {
				$code=pmb_mysql_result($resultat,0,0,$dbh);
				$id_author=pmb_mysql_result($resultat,0,1,$dbh);
			} else $code="";
			$id_tu=0;
			if (isISBN($code)) {
				$uri=titre_uniforme::get_data_bnf_uri($code);
				if ($uri) {
					//Recherche du titre uniforme déjà existant ?
					$requete="select tu_id from titres_uniformes where tu_databnf_uri='".addslashes($uri)."'";
					$resultat=pmb_mysql_query($requete,$dbh);
					if (pmb_mysql_num_rows($resultat,$dbh)) 
						$id_tu=pmb_mysql_result($resultat,0,0,$dbh);
					else {
						//Interrogation de data.bnf pour obtenir les infos !
						$configbnf = array(
								'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
						);
						$storebnf = ARC2::getRemoteStore($configbnf);
						
						$sparql = "
						PREFIX dc: <http://purl.org/dc/terms/>
										
						SELECT ?title ?date ?description WHERE {
						  <".$uri."> dc:title ?title.
						  OPTIONAL { <".$uri."> dc:date ?date. }
						  OPTIONAL { <".$uri."> dc:description ?description. }
						}";
						$rows = $storebnf->query($sparql, 'rows');
						// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
						$err = $storebnf->getErrors();
						if (!$err) {
							$value=array(
									"name"=>encoding_normalize::charset_normalize($rows[0]['title'],"utf-8"),
									"num_author"=>$id_author,
									"date"=>encoding_normalize::charset_normalize($rows[0]['date'],"utf-8"),
									"comment"=>encoding_normalize::charset_normalize($rows[0]['description'],"utf-8"),
									"databnf_uri"=>$uri
							);
							$tu=new titre_uniforme();
							$id_tu=$tu->import($value);
						}
					}
				}
			}
			if ($id_tu) {
				$titres_uniformes=array(array("num_tu"=>$id_tu));
				$ntu=new tu_notice($notice);
				$ntu->update($titres_uniformes);
			}
			return $id_tu;
		}
	}
} # fin de declaration

