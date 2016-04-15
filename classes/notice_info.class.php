<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_info.class.php,v 1.50.2.5 2015-10-28 10:19:20 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// Récupération des info de notices
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once("$class_path/author.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($include_path."/interpreter/bbcode.inc.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");

if (!sizeof($tdoc)) $tdoc = new marc_list('doctype');

if (!count($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

if (!count($langue_doc)) {
	$langue_doc = new marc_list('lang');
	$langue_doc = $langue_doc->table;
	}
if (!count($icon_doc)) {
	$icon_doc = new marc_list('icondoc');
	$icon_doc = $icon_doc->table;
}
if(!count($biblio_doc)) {
	$biblio_doc = new marc_list('nivbiblio');
	$biblio_doc = $biblio_doc->table;
}

class notice_info {
	var $notice;	
	
	function notice_info($id,$environement=array()) {			
		$this->notice_id=$id;
		$this->environement=$environement;
		if(!$this->environement["short"]) $this->environement["short"] = 6;
		if(!$this->environement["ex"])	$this->environement["ex"] = 0;
		if(!$this->environement["exnum"]) $this->environement["exnum"] = 1;
		
		if(!$this->environement["link"]) $this->environement["link"] = "./catalog.php?categ=isbd&id=!!id!!" ;
		if(!$this->environement["link_analysis"]) $this->environement["link_analysis"] = "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!" ;
		if(!$this->environement["link_explnum"]) $this->environement["link_explnum"] = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!" ;
		if(!$this->environement["link_bulletin"]) $this->environement["link_bulletin"] = "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!" ;
		
		$this->fetch_data();
	}
	
	function fetch_analysis_info() {
		if (($this->niveau_biblio=="a")&&($this->niveau_hierar==2)) {
			$requete="select tit1,bulletin_numero,date_date,mention_date from analysis join bulletins on (analysis_bulletin=bulletin_id) join notices on (bulletin_notice=notice_id) where " .
					"analysis_notice=".$this->notice_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$this->serial_title=$r->tit1;
				$this->bulletin_numero=$r->bulletin_numero;
				$this->bulletin_mention_date=$r->mention_date;
				$this->bulletin_date_date=formatdate($r->date_date);
			}
		}
	}
	
	function fetch_data() {
		global $base_path,$charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg;
		global $opac_url_base,$use_opac_url_base;
		global $opac_sur_location_activate;
		global $fonction_auteur,$msg;
		global $tdoc,$icon_doc,$biblio_doc;
		global $pmb_map_activate;
		global $pmb_url_base;
		
		if (!$this->notice_id) return false;
		
		//Recuperation des infos de la notice
		$requete = "select * from notices where notice_id=".$this->notice_id;
		$resultat = pmb_mysql_query($requete);
		$res = pmb_mysql_fetch_object($resultat);
		$this->notice=$res;

		$this->memo_isbn = $this->notice->code ;
		$this->memo_typdoc = $tdoc->table[$this->notice->typdoc];
 		//Icone type de Document
 		$icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
 		if ($icon) {
 			$info_bulle_icon=$biblio_doc[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc];
 			if ($use_opac_url_base)	$this->memo_icondoc="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
 			else $this->memo_icondoc="<img src=\"".$pmb_url_base."images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
 		}
 		if($use_opac_url_base) {
 			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
 				$this->memo_iconcart="<span id='baskets".$this->notice_id."'><a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a></span>";
 			} else {
 				$title=$this->notice_header;
 				if(!$title)$title=$this->notice->tit1;
 				$this->memo_iconcart="<span id='baskets".$this->notice_id."'><a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."images/basket_small_20x20.gif\" border=\"0\" title=\"".$msg['notice_title_basket']."\" alt=\"".$msg['notice_title_basket']."\" /></a></span>";
 			}
 		} else {
 			$this->memo_iconcart = "<img src=\"".$pmb_url_base."images/basket_small_20x20.gif\" align='absmiddle' border='0' title='".$msg["400"]."' alt='".$msg["400"]."' />";
 		}
		$this->niveau_biblio=$this->notice->niveau_biblio;
		$this->niveau_hierar=$this->notice->niveau_hierar;
		
		//Recherche des infos du périodique
		$this->fetch_analysis_info();
		
		//Recherche des etats de collection
		$this->fetch_collstate();
		
		//Titres
		//Titre de serie et composition du titre
		$this->memo_series[]=array();
		if($res->tparent_id) {
			$requete = "select * from series where serie_id=".$res->tparent_id;
			$resultat = pmb_mysql_query($requete);
			if (($serie = pmb_mysql_fetch_object($resultat))) {
				$this->memo_series[]=$serie;
				$this->memo_titre=$serie->serie_name;
				$this->memo_titre_serie=$serie->serie_name;
				$this->isbd = $this->serie_name;
				if($this->notice->tnvol) {
					$this->memo_titre.= ', '.$res->tnvol;				
					$this->memo_titre_serie.= ', '.$res->tnvol;	
					$this->isbd .= ',&nbsp;'.$this->tnvol;
				}
			}
		} elseif($this->notice->tnvol){
			$this->memo_titre.= $res->tnvol;
		}
		
		$this->memo_titre ? $this->memo_titre .= '. '.$res->tit1 : $this->memo_titre = $res->tit1;	
		
		$this->isbd ? $this->isbd .= '.&nbsp;'.$this->notice->tit1 : $this->isbd = $this->notice->tit1;
		$tit2 = $this->notice->tit2;
		$tit3 = $this->notice->tit3;
		$tit4 = $this->notice->tit4;
		if($tit3) $this->isbd .= "&nbsp;= $tit3";
		if($tit4) $this->isbd .= "&nbsp;: $tit4";
		if($tit2) $this->isbd .= "&nbsp;; $tit2";
		$this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		
		
		$this->memo_notice_bulletin=new stdClass();
		$this->memo_bulletin=new stdClass();
		if ($res->niveau_biblio=='b') {
			$rqt="select tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date, bulletin_numero as num_bull,bulletin_notice from bulletins,notices where bulletins.num_notice='".$this->notice_id."' and notices.notice_id=bulletins.bulletin_notice";
			$execute_query=pmb_mysql_query($rqt);
			$row=pmb_mysql_fetch_object($execute_query);
			$this->memo_titre.=" ".(!$row->aff_date_date?sprintf($msg["bul_titre_perio"],$row->tit1):sprintf($msg["bul_titre_perio"],$row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]"));
			
			// recherche editeur de la notice de perio 
			$rqt_perio="select * from notices where notice_id=".$row->bulletin_notice;
			$execute_query_perio=pmb_mysql_query($rqt_perio);
			$row_perio=pmb_mysql_fetch_object($execute_query_perio);
			if (!$this->notice->ed1_id) {
				$this->notice->ed1_id=$row_perio->ed1_id;
			}
			//issn pour les notices de bulletin
			if (!$this->notice->code) {
				$this->memo_isbn=$row_perio->code;
			}
		}elseif ($res->niveau_biblio == 'a' && $res->niveau_hierar == 2) {	
			$requete = "SELECT b.* "; 
			$requete .= "from analysis a, notices b, bulletins c";
			$requete .= " WHERE a.analysis_notice=".$this->notice_id;
			$requete .= " AND c.bulletin_id=a.analysis_bulletin";
			$requete .= " AND c.bulletin_notice=b.notice_id";
			$requete .= " LIMIT 1";
			$myQuery = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($myQuery)) {		
				$row_perio = pmb_mysql_fetch_object($myQuery);
				if (!$this->notice->ed1_id) {			
					$this->notice->ed1_id=$row_perio->ed1_id;
				}
				//issn pour les notice de dépouillement
				if (!$this->notice->code) {
					$this->memo_isbn=$row_perio->code;
				}				
			}	

			//	info du bulletin de ce dépouillement			
			$req_bulletin = "SELECT  c.* from analysis a, bulletins c WHERE c.bulletin_id=a.analysis_bulletin AND analysis_notice=".$res->notice_id;
			$result_bull = pmb_mysql_query($req_bulletin);
			if(($bull=pmb_mysql_fetch_object($result_bull))){				
				$this->memo_bulletin=$bull;				
				$this->memo_notice_bulletin=$bull;
				$this->bulletin_mention_date=$bull->mention_date;
				$this->bulletin_date_date=formatdate($bull->date_date);
				$this->bulletin_numero=$bull->bulletin_numero;
			}
		}	
		$this->memo_complement_titre=$res->tit4;
		$this->memo_titre_parallele=$res->tit3;
		
		$this->memo_notice = $res;
		
		//mention d'édition
		$this->memo_mention_edition=$res->mention_edition;
	
		//Titre du pério pour les notices de bulletin		
		if($res->niveau_biblio == 'b' && $res->niveau_hierar == '2'){				
			$req_bulletin = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero, tit1 as titre from bulletins, notices WHERE bulletin_notice=notice_id AND num_notice=".$res->notice_id;
			$result_bull = pmb_mysql_query($req_bulletin);
			while(($bull=pmb_mysql_fetch_object($result_bull))){
				$this->memo_notice_bulletin=$bull;
				$this->memo_bulletin=$bull;
				$this->serial_title=$bull->titre;
				$this->bulletin_mention_date=$bull->mention_date;
				$this->bulletin_date_date=formatdate($bull->date_date);
				$this->bulletin_numero=$bull->bulletin_numero;
				$this->bulletin_id = $bull->bulletin_id;
			}				
		}

		//Langage
		$this->memo_lang	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
		$this->memo_lang_or	= get_notice_langues($this->notice_id, 1) ; // langues originales
			
				
		//Auteurs
		$this->authors = array();
		//Recherche des auteurs;
		$this->responsabilites = get_notice_authors($this->notice_id);	
		$mention_resp = $mention_resp_1 = $mention_resp_2 = array() ;	
		$isbd_entry_1 = $isbd_entry_2 = array() ;		
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			$auteur->fonction = $fonction_auteur[$auteur_0["fonction"]];
			$this->authors[]=$auteur;
			if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry; 
			else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
			if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
			if ($auteur_0["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			$mention_resp[] = $mention_resp_lib;
			$this->memo_auteur_principal=$auteur->isbd_entry;
		}		
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$auteur->fonction = $fonction_auteur[$auteur_1["fonction"]];
			$this->authors[]=$auteur;
			if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry; 
			else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
			if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
			if ($auteur_1["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			$mention_resp[] = $mention_resp_lib;
			$mention_resp_1[] = $mention_resp_lib;
			$isbd_entry_1[]= $auteur->isbd_entry;
		}	
		$this->memo_mention_resp_1 = implode ("; ",$mention_resp_1);
		$this->memo_auteur_autre_tab = $isbd_entry_1;
		$this->memo_auteur_autre = implode ("; ",$isbd_entry_1);
			
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_2["id"]);
			$auteur->fonction = $fonction_auteur[$auteur_2["fonction"]];
			$this->authors[]=$auteur;
			if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry; 
			else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
			if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
			if ($auteur_2["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			$mention_resp[] = $mention_resp_lib;
			$mention_resp_2[]= $mention_resp_lib;
			$isbd_entry_2[]= $auteur->isbd_entry;
		}	
		$this->memo_mention_resp_2 = implode ("; ",$mention_resp_2);
		$this->memo_auteur_secondaire_tab = $isbd_entry_2;		
		$this->memo_auteur_secondaire = implode ("; ",$isbd_entry_2);
				
		$this->memo_libelle_mention_resp = implode ("; ",$mention_resp);		
		if($this->memo_libelle_mention_resp) $this->isbd .= "&nbsp;/ $this->memo_libelle_mention_resp" ;

		// on récupère la collection au passage, si besoin est
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$info=$this->get_info_editeur($collection->editeur);			
			$this->memo_collection=$collection->isbd_entry;
			$this->memo_ed1=$info["isbd_entry"];
			$this->memo_ed1_name=$info["name"];
			$this->memo_ed1_place=$info["place"];
			$editeurs=$info["isbd_entry"];				
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$info=$this->get_info_editeur($collection->parent);			
			$this->memo_collection=$collection->isbd_entry;
			$this->memo_ed1=$info["isbd_entry"];
			$this->memo_ed1_name=$info["name"];
			$this->memo_ed1_place=$info["place"];
			$editeurs=$info["isbd_entry"];		
		} elseif ($this->notice->ed1_id) {
			$info=$this->get_info_editeur($this->notice->ed1_id);
			$this->memo_ed1=$info["isbd_entry"];	
			$this->memo_ed1_name=$info["name"];
			$this->memo_ed1_place=$info["place"];
			$editeurs=$info["isbd_entry"];		
		}		
		if($this->notice->ed2_id) {
			$info=$this->get_info_editeur($this->notice->ed2_id);	
			$this->memo_ed2=$info["isbd_entry"];
			$this->memo_ed2_name=$info["name"];
			$this->memo_ed2_place=$info["place"];
			$editeurs ? $editeurs .= '&nbsp;; '.$info["isbd_entry"] : $editeurs = $info["isbd_entry"];
		}
	
		if($this->notice->year) {
			$editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
		} elseif ($this->notice->niveau_biblio!='b') $editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
		$this->memo_year=$this->notice->year;
	
		if ($editeurs) $this->isbd .= ".&nbsp;-&nbsp;$editeurs";		
		
		// zone de la collation (ne concerne que a2)
		if($this->notice->npages)
			$collation = $this->notice->npages;
		if($this->notice->ill)
			$collation .= ': '.$this->notice->ill;
		if($this->notice->size)
			$collation .= '; '.$this->notice->size;
		if($this->notice->accomp)
			$collation .= '+ '.$this->notice->accomp;
			
		if($collation)
			$this->isbd .= ".&nbsp;-&nbsp;$collation";
		$this->memo_collation=$collation;

		// map
		$this->memo_map_isbd="";			
		$this->memo_map_id = 0;
		$this->memo_map_echelle = "";
		$this->memo_map_projection = "";
		$this->memo_map_ref = "";
		$this->memo_map_equinoxe = "";
		$this->memo_map="";
		if($pmb_map_activate){
			$ids[]=$this->notice_id;
			$this->map=new map_objects_controler(TYPE_RECORD,$ids);
			$this->map_info=new map_info($this->notice_id);
			
			$this->memo_map_isbd=$this->map_info->get_isbd();
			if($this->memo_map_isbd)$this->isbd .=".&nbsp;-&nbsp;".$this->memo_map_isbd;
			
			$this->memo_map_id = $this->map_info->map['id'];
			$this->memo_map_echelle = $this->map_info->map['echelle'];
			$this->memo_map_projection = $this->map_info->map['projection'];
			$this->memo_map_ref = $this->map_info->map['ref'];
			$this->memo_map_equinoxe = $this->map_info->map['equinoxe'];
			$this->memo_map=$this->map->get_map();			
		}

		//Recherche du code dewey
		$requete = "select * from indexint where indexint_id=".$res -> indexint;
		$resultat = pmb_mysql_query($requete);
		if (($code_dewey=pmb_mysql_fetch_object($resultat))) {
			$this->memo_dewey=$code_dewey;
		}
		
		if($collections=$this->memo_collection) {
			if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
			$this->isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}
		if(substr(trim($this->isbd), -1) != "."){
			$this->isbd .= '.';
		}
		
		//Traitement des exemplaires
		$this->memo_exemplaires=array();
		$requete = "select expl_id, expl_cb, expl_cote, expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment, expl_section, section_libelle, "; 
		$requete.= "expl_owner, lender_libelle, expl_codestat, codestat_libelle, expl_date_retour, expl_date_depot, expl_note, pret_flag, expl_location, location_libelle, expl_prix ";
		if($opac_sur_location_activate) {
			$requete.= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
		}
		$requete.= "from exemplaires, docs_statut, docs_type, docs_section, docs_codestat, lenders, docs_location "; 
		if($opac_sur_location_activate) {
			$requete.= "left join sur_location on surloc_num=surloc_id ";
		}
		$requete.= "where expl_notice=".$res -> notice_id." and expl_statut=idstatut and expl_typdoc=idtyp_doc and expl_section=idsection and expl_owner=idlender and expl_codestat=idcode ";
		$requete.= "and expl_location=idlocation ";
		$requete.= "union ";
		$requete.= "select expl_id, expl_cb, expl_cote, expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment, expl_section, section_libelle, "; 
		$requete.= "expl_owner, lender_libelle, expl_codestat, codestat_libelle, expl_date_retour, expl_date_depot, expl_note, pret_flag, expl_location, location_libelle, expl_prix ";
		if($opac_sur_location_activate) {
			$requete.= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
		}
		$requete.= "from exemplaires, bulletins, docs_statut, docs_type, docs_section, docs_codestat, lenders, docs_location "; 
		if($opac_sur_location_activate) {
			$requete.= "left join sur_location on surloc_num=surloc_id ";
		}
		$requete.= "where bulletins.num_notice=".$res -> notice_id." and expl_bulletin=bulletin_id and expl_statut=idstatut and expl_typdoc=idtyp_doc and expl_section=idsection and expl_owner=idlender and expl_codestat=idcode ";
		$requete.= "and expl_location=idlocation";
		$resultat = pmb_mysql_query($requete);		
		while (($ex = pmb_mysql_fetch_object($resultat))) {
			//Champs perso d'exemplaires			
			$parametres_perso=array();
			$mes_pp=new parametres_perso("expl");
			if (!$mes_pp->no_special_fields) {			
				$mes_pp->get_values($ex->expl_id);
				$values = $mes_pp->values;
				foreach ( $values as $field_id => $vals ) {
					$parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$mes_pp->t_fields[$field_id]["TITRE"];
					foreach ( $vals as $value ) {				
						$parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$mes_pp->get_formatted_output(array($value),$field_id);	
					}
				}							
			}
			$ex->parametres_perso=$parametres_perso;
			$this->memo_exemplaires[]=$ex;
		}
		
		//Descripteurs
		$requete="SELECT libelle_categorie FROM categories, notices_categories WHERE notcateg_notice=".$res->notice_id." and categories.num_noeud = notices_categories.num_noeud ORDER BY ordre_categorie";
		$resultat=pmb_mysql_query($requete);
		$this->memo_categories=array();
		while (($cat = pmb_mysql_fetch_object($resultat))) {
			$this->memo_categories[]=$cat;
		}
		
		$authperso = new authperso_notice($this->notice_id);
		$this->memo_authperso_all_isbd .=$authperso->get_notice_display();
		$this->memo_authperso_all_isbd_list=$authperso->get_notice_display_list();		
		foreach ($authperso->auth_info as $fields) {
			foreach ($fields["info_fields"] as $field) {
				if(is_array($field["values"]) && count($field["values"])) {
					$tvalues = array();
					foreach ($field["values"] as $values) {
						$tvalues[] = $values["format_value"];
					}
					$this->parametres_auth_perso[$field["name"]]["TITRE"][] = $field["label"];
					$this->parametres_auth_perso[$field["name"]]["VALUE"][] = $tvalues;
				}
			}
		}
		
		//Champs perso de notice traite par la table notice_custom
		$mes_pp= new parametres_perso("notices");
		$mes_pp->get_values($res->notice_id);
		$values = $mes_pp->values;
		$this->parametres_perso=array();
		foreach ( $values as $field_id => $vals ) {
			$this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$mes_pp->t_fields[$field_id]["TITRE"];
			foreach ( $vals as $value ) {
				$this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$mes_pp->get_formatted_output(array($value),$field_id);				
				$this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE_IN_DATABASE"][]=$value;				
			}
		}		

		//Notices liées, relations entre notices
		//les notices mères
		$requete="SELECT num_notice, linked_notice, relation_type, rank from notices_relations where num_notice=".$res->notice_id." order by num_notice, rank asc";
		$resultat=pmb_mysql_query($requete);
		$i=0;
		while(($notice_fille=pmb_mysql_fetch_object($resultat))) {						
			$this->memo_notice_mere[$i]=$notice_fille->linked_notice;		
			$this->memo_notice_mere_relation_type[$i]=$notice_fille->relation_type;
			$i++;
		}
	
		// les notices filles	
		$requete="SELECT num_notice, linked_notice, relation_type, rank from notices_relations where linked_notice=".$res->notice_id." order by num_notice, rank asc";
		$resultat=pmb_mysql_query($requete);
		$i=0;
		while(($notice_mere=pmb_mysql_fetch_object($resultat))) {						
			$this->memo_notice_fille[$i]=$notice_mere->num_notice;	
			$this->memo_notice_fille_relation_type[$i]=$notice_mere->relation_type;
			$i++;
		}
			
		// liens vers les périodiques pour les notices d'article
		$req_perio_link = "SELECT notice_id, tit1, code from bulletins,analysis,notices WHERE bulletin_notice=notice_id and bulletin_id=analysis_bulletin and analysis_notice=".$res->notice_id;
		$result_perio_link=pmb_mysql_query($req_perio_link);
		while(($notice_perio_link=pmb_mysql_fetch_object($result_perio_link))){
			$this->memo_notice_article[]=$notice_perio_link->notice_id;
		}		
	
		// bulletinage pour les notices de pério			
		$req_bulletinage = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero from bulletins, notices WHERE bulletin_notice = notice_id AND notice_id=".$res->notice_id;
		$result_bulletinage=pmb_mysql_query($req_bulletinage);					
		while(($notice_bulletinage=pmb_mysql_fetch_object($result_bulletinage))){
			$this->memo_bulletinage[]=$notice_bulletinage->bulletin_id;
		}					
				
		// liens vers les bulletins pour les notices d'article
		$req_bull_link = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero from bulletins, analysis WHERE bulletin_id=analysis_bulletin and analysis_notice=".$res->notice_id;
		$result_bull_link=pmb_mysql_query($req_bull_link);						
		while(($notice_bull_link=pmb_mysql_fetch_object($result_bull_link))){
			$this->memo_article_bulletinage[]=$notice_bull_link->bulletin_id;
		}			
					
		$paramaff["mine_type"]=1;
		$this->memo_explnum_assoc=show_explnum_per_notice($res->notice_id, 0,"",$paramaff);
		
		if ($this->notice->code || $this->notice->thumbnail_url) {
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $this->notice->thumbnail_url)) {
				$code_chiffre = pmb_preg_replace('/-|\.| /', '', $this->notice->code);
				$url_image = $opac_book_pics_url ;
				$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&amp;noticecode=!!noticecode!!&amp;vigurl=".urlencode($this->notice->thumbnail_url) ;
				
				if ($this->notice->thumbnail_url) {
					$url_image_ok=$this->notice->thumbnail_url;
					$title_image_ok="";
				} else {
					$url_image_ok = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
				}
				$this->memo_image = "<img class='vignetteimg' src='".$url_image_ok."' title=\"".$title_image_ok."\" align='right' hspace='4' vspace='2' style='max-width : 140px; max-height: 200px;' >";
				$this->memo_url_image=$url_image_ok;
				
			} else{
				$this->memo_image="" ;
				$this->memo_url_image="";
			}
		}	
		
		//calcul du permalink...
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}		
		
		//Traitement des avis
		$this->memo_avis=array();
		$requete="SELECT id_avis,note,sujet,commentaire,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate,empr_login,empr_nom, empr_prenom, valide
		from avis left join empr on id_empr=num_empr where num_notice='".$res->notice_id."' and valide=1 order by avis_rank, dateajout desc";
		$resultat = pmb_mysql_query($requete);
		if ($resultat) {
			while (($avis = pmb_mysql_fetch_object($resultat))) {
				$avis->note_textuelle = $msg['avis_detail_note_'.$avis->note];
				if($charset != "utf-8") $avis->commentaire=cp1252Toiso88591($avis->commentaire);
 				$avis->commentaire = do_bbcode($avis->commentaire);
				$this->memo_avis[]=$avis;
			}
		}
		
		//Titres uniformes
		$requete = "select * from notices_titres_uniformes where ntu_num_notice=".$res -> notice_id." order by ntu_ordre";
		$resultat = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			while(($tu=pmb_mysql_fetch_object($resultat))) {
				$tu_memo = new titre_uniforme($tu->ntu_num_tu);
				$tu_memo->parametres_perso=array();
				
				$mes_pp= new parametres_perso("tu");
				$mes_pp->get_values($tu->ntu_num_tu);
				$values = $mes_pp->values;
				foreach ( $values as $field_id => $vals ) {
					$tu_memo->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$mes_pp->t_fields[$field_id]["TITRE"];
					foreach ( $vals as $value ) {
						$tu_memo->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$mes_pp->get_formatted_output(array($value),$field_id);
					}
				}
				
				$this->memo_tu[]=$tu_memo;
			}
		}
		
		//statut
		$this->memo_statut['id_notice_statut'] = $res->statut;
		$this->memo_statut['gestion_statut_libelle'] = '';
		$this->memo_statut['opac_statut_libelle'] = '';
		if ($this->memo_statut['id_notice_statut']) {
			$requete="SELECT * FROM notice_statut WHERE id_notice_statut=".($this->memo_statut['id_notice_statut']*1);
			$resultat = pmb_mysql_query($requete);
			if ($resultat) {
				$statut=pmb_mysql_fetch_object($resultat);
				$this->memo_statut['gestion_statut_libelle'] = $statut->gestion_libelle;
				$this->memo_statut['opac_statut_libelle'] = $statut->opac_libelle;
			}
		}
		
		return true;
	}
	
	function get_info_editeur($id) {
		$info=array();
		if($id){
			$requete = "SELECT * FROM publishers WHERE ed_id=$id LIMIT 1 ";
			$result = @pmb_mysql_query($requete);
			if($result && pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$id		= $temp->ed_id;
				$name		= $temp->ed_name;
				$adr1		= $temp->ed_adr1;
				$adr2		= $temp->ed_adr2;
				$cp		= $temp->ed_cp;
				$ville	= $temp->ed_ville;
				$pays		= $temp->ed_pays;
				$web		= $temp->ed_web;
				$ed_comment= $temp->ed_comment	;

				// Determine le lieu de publication
				$l = '';
				if ($adr1)  $l = $adr1;
				if ($adr2)  $l = ($l=='') ? $adr2 : $l.', '.$adr2;
				if ($cp)    $l = ($l=='') ? $cp   : $l.', '.$cp;
				if ($pays)  $l = ($l=='') ? $pays : $l.', '.$pays;
				if ($ville) $l = ($l=='') ? $ville : $ville.' ('.$l.')';
				if ($l=='')       $l = '[S.l.]';
					
				// Determine le nom de l'editeur
				if ($name) $n = $name; else $n = '[S.n.]';
					
				// Constitue l'ISBD pour le coupe lieu/editeur
				if ($l == '[S.l.]' AND $n == '[S.n.]') $isbd_entry = '[S.l.&nbsp;: s.n.]';
				else $isbd_entry = $l.'&nbsp;: '.$n;
				$info['isbd_entry']=$isbd_entry;
				$info['name'] = $name;
				$info['place'] = $l;
			}
		}	
		return($info);
	}
	
	function fetch_notices_parents(){
		$this->notices_parents = array();
		for($i=0 ; $i<count($this->memo_notice_mere) ; $i++){
			$this->notices_parents[] = new notice_info($this->memo_notice_mere[$i]);
		}
	}

	function fetch_notices_childs(){
		$this->notices_childs = array();
		for($i=0 ; $i<count($this->memo_notice_fille) ; $i++){
			$this->notices_childs[] = new notice_info($this->memo_notice_fille[$i]);
		}		
	}
	
	function fetch_collstate() {

		if (($this->niveau_biblio=='s')&&($this->niveau_hierar==1)) {
			global $dbh;
			global $opac_sur_location_activate;
			
			//Traitement des exemplaires
			$this->memo_collstate=array();
			
			$q = "select collstate_id, id_serial, state_collections, collstate_origine, collstate_cote, collstate_archive, collstate_lacune, collstate_note, ";
			$q.= "idlocation, location_libelle, ";
			$q.= "archempla_id, archempla_libelle, ";
			$q.= "archtype_id, archtype_libelle, ";
			$q.= "archstatut_id, archstatut_opac_libelle ";
			if($opac_sur_location_activate) {
				$q.= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
			}
			$q.= "from collections_state ";
			$q.= "join docs_location on location_id=idlocation ";
			if($opac_sur_location_activate) {
				$q.= "left join sur_location on surloc_num=surloc_id ";
			}
			$q.= "join arch_emplacement on collstate_emplacement=archempla_id ";
			$q.= "join arch_type on collstate_type=archtype_id ";
			$q.= "join arch_statut on collstate_statut=archstatut_id ";
			$q.= "where id_serial = '".$this->notice_id."' ";
			//pour l'opac
			//$q.= "and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".($_SESSION["user_code"]?" or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)":"").")";		
			$r = pmb_mysql_query($q, $dbh);
			if ($r) {
				while (($cs = pmb_mysql_fetch_object($r))) {
					//Champs perso d'etats de collection		
					$parametres_perso=array();
					$pp=new parametres_perso("collstate");
					if (!$pp->no_special_fields) {			
						$pp->get_values($cs->expl_id);
						$values = $pp->values;
						foreach ( $values as $field_id => $vals ) {
							foreach ( $vals as $value ) {				
								$parametres_perso[$pp->t_fields[$field_id]["NAME"]]["TITRE"]=$pp->t_fields[$field_id]["TITRE"];
								$parametres_perso[$pp->t_fields[$field_id]["NAME"]]["VALUE"]=$pp->get_formatted_output(array($value),$field_id);	
							}
						}							
					}
					$cs->parametres_perso=$parametres_perso;
					$this->memo_collstate[]=$cs;
				}
			}
		}
	}
}
?>
