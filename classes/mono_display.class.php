<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mono_display.class.php,v 1.255.2.5 2015-11-14 12:44:15 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/emprunteur.class.php");
require_once("$class_path/transfert.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($include_path."/isbn.inc.php");
require_once($include_path."/resa_func.inc.php");
require_once("$class_path/tu_notice.class.php");
require_once("$class_path/sur_location.class.php");
require_once("$class_path/notice_tpl_gen.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");
require_once($class_path."/nomenclature/nomenclature_record_ui.class.php");

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

// propriétés pour le selecteur de panier
$selector_prop = "toolbar=no, dependent=yes, resizable=yes, scrollbars=yes";
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!id!!&unq=!!unique!!', 'cart', 600, 700, -2, -2, '$selector_prop')\"";


// définition de la classe d'affichage des monographies en liste
class mono_display {
	var $notice_id 	= 0;	// id de la notice à afficher
	var $isbn 		= 0;	// isbn ou code EAN de la notice à afficher
	var $notice;			// objet notice (tel que fetché dans la table 'notices'
	var $langues = array();
	var $languesorg = array();
	var $action		= '';	// URL à associer au header
	var $header		= '';	// chaine accueillant le chapeau de notice (peut-être cliquable)
	var $tit_serie	= '';	// titre de série si applicable
	var $tit1		= '';	// valeur du titre 1
	var $result		= '';	// affichage final
	var $level		= 1;	// niveau d'affichage
	var $isbd		= '';	// isbd de la notice en fonction du level défini
	var $simple_isbd = "";	// isbd de la notice en fonction du level défini, sans l'image
	var $expl		= 0;	// flag indiquant si on affiche les infos d'exemplaire
	var $nb_expl	= 0;	//nombre d'exemplaires
	var $link_expl	= '';	// lien associé à un exemplaire
	var $responsabilites = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	var $categories = array();// les categories
	var $show_resa	= 0;	// flag indiquant si on affiche les infos de resa
	var $show_planning	= 0;	// flag indiquant si on affiche les infos de prévision
	var $p_perso;
	var $print_mode=0;		// 0 affichage normal
							// 1 affichage impression sans liens
							// 2 affichage impression avec liens sur documents numeriques
							// 4 affichage email : sans lien sauf url associée
	var $show_explnum=1;
	var $show_statut=0;
	var $aff_statut='' ; 	// carré de couleur pour signaler le statut de la notice
	var $tit_serie_lien_gestion ;
	var $childs=array(); 	//Filles de la notice
	var $anti_loop="";
	var $drag=""; 			//Notice draggable ?
	var $no_link;
	var $show_opac_hidden_fields=true;
	var $ajax_mode=0;
	var $show_map=1;

// constructeur------------------------------------------------------------
function mono_display(	$id,							// $id = id de la notice à afficher
						$level=1, 						// $level :
														//	0 : juste le header (titre  / auteur principal avec le lien si applicable)
														//	1 : ISBD seul, pas de note, bouton modif, expl, explnum et résas
														// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
						$action='', 					// $action	 = URL associée au header
						$expl=1, 						// $expl -> affiche ou non les exemplaires associés
						$expl_link='', 					// $expl_link -> lien associé à l'exemplaire avec !!expl_id!!, !!notice_id!! et !!expl_cb!! à mettre à jour
						$lien_suppr_cart="", 			// $lien_suppr_cart -> lien de suppression de la notice d'un caddie
						$explnum_link='',
						$show_resa=0,   				// $show_resa = affichage des resa ou pas
						$print=0, 						// $print = 0 affichage normal
														//			1 affichage impression sans liens
														//			2 affichage impression avec liens sur documents numeriques
														//			4 affichage email : sans lien sauf url associée
						$show_explnum=1,
						$show_statut=0,
						$anti_loop='',
						$draggable=0,
						$no_link=false,
						$show_opac_hidden_fields=true,
						$ajax_mode=0,
						$show_planning=0, 				// $show_planning = affichage des prévisions ou pas
						$show_map=1                     // $show_map = affichage de la map
						) {

	global $pmb_recherche_ajax_mode;
	global $categ;
	global $id_empr;

	$this->show_map=$show_map;

	if($pmb_recherche_ajax_mode){
		$this->ajax_mode=$ajax_mode;
		if($this->ajax_mode) {
			if (is_object($id)){
				$param['id']=$id->notice_id;
			} else {
				$param['id']=$id;
			}
			$param['function_to_call']="mono_display";
			//if($level)$param['level']=$level;	// à 6
			if($action)$param['action']=$action;
			if($expl)$param['expl']=$expl;
			if($expl_link)$param['expl_link']=$expl_link;
//			if($lien_suppr_cart)$param['lien_suppr_cart']=$lien_suppr_cart;
			if($explnum_link)$param['explnum_link']=$explnum_link;
			//if($show_resa)$param['show_resa']=$show_resa;
			if($print)$param['print']=$print;
			//if($show_explnum)$param['show_explnum']=$show_explnum;
			//if($show_statut)$param['show_statut']=$show_statut;
			//if($anti_loop)$param['anti_loop']=$anti_loop;
			//if($draggable)$param['draggable']=$draggable;
			if($no_link)$param['no_link']=$no_link;
			if($categ)$param['categ']=$categ;
			if($id_empr)$param['id_empr']=$id_empr;
			//if($show_opac_hidden_fields)$param['show_opac_hidden_fields']=$show_opac_hidden_fields;
			$this->mono_display_cmd=serialize($param);
		}
	}

	if(!$id)
		return;
	else {
		if (is_object($id)){
			$this->notice_id = $id->notice_id;
			$this->notice = $id;
			$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
			$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales
			$this->isbn = $id->code ;
			//Récupération titre de série
			if($id->tparent_id) {
				$parent = new serie($id->tparent_id);
				$this->tit_serie = $parent->name;
				$this->tit_serie_lien_gestion = $parent->isbd_entry_lien_gestion;
			}
		} else {
			$this->notice_id = $id;
			$this->mono_display_fetch_data();
		}
		if(!$this->ajax_mode || !$level) {
			$this->childs=array();
			$requete="select num_notice as notice_id,relation_type from notices_relations,notices where linked_notice=".$this->notice_id." and num_notice=notice_id order by relation_type, rank,create_date";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					$this->childs[$r->relation_type][]=$r->notice_id;
				}
			}
		}
	}
	global $memo_p_perso_notice;
	if(!$this->ajax_mode || !$level) {
		if(!$memo_p_perso_notice) {
			$memo_p_perso_notice=new parametres_perso("notices");
		}
		$this->p_perso=$memo_p_perso_notice;
	}
	$this->level = $level;
	$this->expl  = $expl;
	$this->show_resa  = $show_resa;

	$this->link_expl = $expl_link;
	$this->link_explnum = $explnum_link;
	$this->lien_suppr_cart = $lien_suppr_cart;
	// mise à jour des liens
	$this->action = $action;
	$this->drag=$draggable;

	$this->print_mode=$print;
	$this->show_explnum=$show_explnum;
	$this->show_statut=$show_statut;
	$this->no_link=$no_link;

	$this->anti_loop=$anti_loop;

	//affichage ou pas des champs persos OPAC masqués
	$this->show_opac_hidden_fields=$show_opac_hidden_fields;

	$this->action = str_replace('!!id!!', $this->notice_id, $this->action);

	$this->responsabilites = get_notice_authors($this->notice_id) ;

	// mise à jour des catégories
	if(!$this->ajax_mode || !$level) $this->categories = get_notice_categories($this->notice_id) ;

	$this->show_planning  = $show_planning;
	$this->do_header();
	switch($level) {
		case 0:
			// là, c'est le niveau 0 : juste le header
			$this->result = $this->header;
			break;
		default:
			global $pmb_map_activate;
			$this->map=new stdClass();
			$this->map_info=new stdClass();
			if($pmb_map_activate){
				$ids[]=$this->notice_id;
				$this->map=new map_objects_controler(TYPE_RECORD,$ids);
				$this->map_info=new map_info($this->notice_id);
			}
			// niveau 1 et plus : header + isbd à générer
			$this->init_javascript();
			if(!$this->ajax_mode) $this->do_isbd();
			$this->finalize();
			break;
		}
	return;

	}


// finalisation du résultat (écriture de l'isbd)
function finalize() {
	$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
}

// génération du template javascript---------------------------------------
function init_javascript() {
	global $msg, $base_path, $pmb_recherche_ajax_mode;
	// propriétés pour le selecteur de panier
	$selector_prop = "toolbar=no, dependent=yes, width=500, height=400, resizable=yes, scrollbars=yes";
	$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=NOTI&item=!!notice_id!!', 'cart', 600, 700, -2, -2, '$selector_prop')\"";
	$cart_over_out = "onMouseOver=\"show_div_access_carts(event,!!notice_id!!);\" onMouseOut=\"set_flag_info_div(false);\"";
	$current=$_SESSION["CURRENT"];
	if ($current!==false) {
		$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('".$base_path."/print.php?current_print=$current&notice_id=!!notice_id!!&action_print=print_prepare','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); w.focus(); return false;\"><img src='".$base_path."/images/print.gif' border='0' align='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
	}
	if($pmb_recherche_ajax_mode && $this->ajax_mode){
		$javascript_template ="
		$attributs_drag
		<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			<img src=\"".$base_path."/images/plus.gif\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" param='".rawurlencode($this->mono_display_cmd)."' title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); return false;\" hspace=\"3\">
			<span class=\"notice-heada\">!!heada!!</span>
			<br />
		</div>
		<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
		</div>";
		if($this->is_child)
			 $javascript_template .= "</div>";
	} else{
		$javascript_template ="
		$attributs_drag
		<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			<img src=\"".$base_path."/images/plus.gif\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\">
			<span class=\"notice-heada\">!!heada!!</span>
			<br />
		</div>
		<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">";
		if(SESSrights & CATALOGAGE_AUTH){
			$javascript_template.="<img src='".$base_path."/images/basket_small_20x20.gif' align='middle' alt='basket' title=\"${msg[400]}\" $cart_click $cart_over_out>".$print_action;
		}else{
			$javascript_template.=$print_action;
		}


		$javascript_template .=" !!ISBD!!
			</div>";
		if($this->is_child)
			$javascript_template .= "</div>";
	}
	$this->result = str_replace('!!id!!', $this->notice_id.($this->anti_loop?"_p".implode("_",$this->anti_loop):""), $javascript_template);
	$this->result = str_replace('!!notice_id!!', $this->notice_id, $this->result);
	$this->result = str_replace('!!heada!!', $this->lien_suppr_cart.$this->header, $this->result);
}

// génération de l'isbd----------------------------------------------------
function do_isbd() {
	global $dbh, $base_path;
	global $langue_doc;
	global $msg;
	global $tdoc;
	global $fonction_auteur;
	global $charset;
	global $thesaurus_mode_pmb, $thesaurus_categories_categ_in_line, $pmb_keyword_sep, $thesaurus_categories_affichage_ordre;
	global $load_tablist_js;
	global $lang;
	global $categories_memo,$libelle_thesaurus_memo;
	global $categories_top,$use_opac_url_base,$opac_url_base,$thesaurus_categories_show_only_last;
	global $categ;
	global $id_empr;
	global $pmb_show_notice_id,$pmb_opac_url,$pmb_show_permalink;
	global $sort_children;
	global $pmb_resa_planning;
	global $thesaurus_concepts_active;
	global $pmb_map_activate;
	global $pmb_nomenclature_activate;

	// constitution de la mention de titre
	if($this->tit_serie) {
		if ($this->print_mode) $this->isbd = $this->tit_serie;
			else $this->isbd = $this->tit_serie_lien_gestion;
		if($this->notice->tnvol)
			$this->isbd .= ',&nbsp;'.$this->notice->tnvol;
	}
	$this->isbd ? $this->isbd .= '.&nbsp;'.$this->notice->tit1 : $this->isbd = $this->notice->tit1;

	$tit2 = $this->notice->tit2;
	$tit3 = $this->notice->tit3;
	$tit4 = $this->notice->tit4;
	if($tit3) $this->isbd .= "&nbsp;= $tit3";
	if($tit4) $this->isbd .= "&nbsp;: $tit4";
	if($tit2) $this->isbd .= "&nbsp;; $tit2";
	$this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';

	$mention_resp = array() ;

	// constitution de la mention de responsabilité
	//$this->responsabilites
	$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
	if ($as!== FALSE && $as!== NULL) {
		$auteur_0 = $this->responsabilites["auteurs"][$as] ;
		$auteur = new auteur($auteur_0["id"]);
		if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry;
		else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
		if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
		if ($auteur_0["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
		$mention_resp[] = $mention_resp_lib ;
	}

	$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
	for ($i = 0 ; $i < count($as) ; $i++) {
		$indice = $as[$i] ;
		$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
		$auteur = new auteur($auteur_1["id"]);
		if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry;
		else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
		if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
		if ($auteur_1["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
		$mention_resp[] = $mention_resp_lib ;
	}

	$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
	for ($i = 0 ; $i < count($as) ; $i++) {
		$indice = $as[$i] ;
		$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
		$auteur = new auteur($auteur_2["id"]);
		if ($this->print_mode) $mention_resp_lib = $auteur->isbd_entry;
		else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
		if (!$this->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
		if ($auteur_2["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
		$mention_resp[] = $mention_resp_lib ;
	}

	$libelle_mention_resp = implode ("; ",$mention_resp) ;
	if($libelle_mention_resp) $this->isbd .= "&nbsp;/ $libelle_mention_resp" ;

	// mention d'édition
	if($this->notice->mention_edition) $this->isbd .= ".&nbsp;-&nbsp;".$this->notice->mention_edition;

	if($pmb_map_activate){
		if($mapisbd=$this->map_info->get_isbd())	$this->isbd .=$mapisbd;
	}

	// zone de l'adresse
	// on récupère la collection au passage, si besoin est
	if($this->notice->subcoll_id) {
		$collection = new subcollection($this->notice->subcoll_id);
		$ed_obj = new editeur($collection->editeur) ;
		if ($this->print_mode) {
			$editeurs .= $ed_obj->isbd_entry;
			$collections = $collection->isbd_entry;
		} else {
			$editeurs .= $ed_obj->isbd_entry_lien_gestion;
			$collections = $collection->isbd_entry_lien_gestion;
		}
	} elseif ($this->notice->coll_id) {
		$collection = new collection($this->notice->coll_id);
		$ed_obj = new editeur($collection->parent) ;
		if ($this->print_mode) {
			$editeurs .= $ed_obj->isbd_entry;
			$collections = $collection->isbd_entry;
		} else {
			$editeurs .= $ed_obj->isbd_entry_lien_gestion;
			$collections = $collection->isbd_entry_lien_gestion;
		}
	} elseif ($this->notice->ed1_id) {
		$editeur = new editeur($this->notice->ed1_id);
		if ($this->print_mode) $editeurs .= $editeur->isbd_entry;
		else $editeurs .= $editeur->isbd_entry_lien_gestion;
	}

	if($this->notice->ed2_id) {
		$editeur = new editeur($this->notice->ed2_id);
		if ($this->print_mode) $ed_isbd=$editeur->isbd_entry;
		else $ed_isbd=$editeur->isbd_entry_lien_gestion;
		$editeurs ? $editeurs .= '&nbsp;; '.$ed_isbd : $editeurs = $ed_isbd;
		}

	if($this->notice->year) $editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
	elseif ($this->notice->niveau_biblio!='b') $editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";


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


	if($collections) {
		if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
		$this->isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}
	if(substr(trim($this->isbd), -1) != "."){
		$this->isbd .= '.';
	}

	// note générale
	if($this->notice->n_gen)
		$zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset)).' ';

	// ISBN ou NO. commercial
	if($this->notice->code) {
		if(isISBN($this->notice->code)) {
			if ($zoneNote) {
				$zoneNote .= '.&nbsp;-&nbsp;ISBN ';
			} else {
				$zoneNote = 'ISBN ';
			}
		} else {
			if($zoneNote) $zoneNote .= '.&nbsp;-&nbsp;';
		}
		$zoneNote .= $this->notice->code;
	}

	if($this->notice->prix) {
		if($this->notice->code) {$zoneNote .= '&nbsp;: '.$this->notice->prix;}
		else {
			if ($zoneNote) 	{ $zoneNote .= '&nbsp; '.$this->notice->prix;}
			else	{ $zoneNote = $this->notice->prix;}
		}
	}

	if($zoneNote) $this->isbd .= "<br /><br />$zoneNote.";

	//In
	//Recherche des notices parentes
	if (!$this->no_link) {
		$requete="select linked_notice, relation_type, rank, l.niveau_biblio as lnb, l.niveau_hierar as lnh from notices_relations, notices as l where num_notice=".$this->notice_id." and linked_notice=l.notice_id order by relation_type,rank";
		$result_linked=pmb_mysql_query($requete) or die(pmb_mysql_error());
		//Si il y en a, on prépare l'affichage
		if (pmb_mysql_num_rows($result_linked)) {
			global $relation_listup ;
			if (!$relation_listup) $relation_listup=new marc_list("relationtypeup");
		}
		$r_type=array();
		$ul_opened=false;
		$r_type_local="";
		//Pour toutes les notices liées

		while ($r_rel=pmb_mysql_fetch_object($result_linked)) {
			//Pour avoir le lien par défaut
			if (!$this->print_mode && (SESSrights & CATALOGAGE_AUTH)) $link_parent=$base_path.'/catalog.php?categ=isbd&id=!!id!!'; else $link_parent="";

			if ($r_rel->lnb=='s' && $r_rel->lnh=='1') {
				// c'est une notice chapeau
				global $link_serial,$link_analysis, $link_bulletin, $link_explnum_serial ;
				$link_serial_sub = $base_path."/catalog.php?categ=serials&sub=view&serial_id=".$r_rel->linked_notice;

				// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
				$serial = new serial_display($r_rel->linked_notice, 0, $link_serial_sub, $link_analysis, $link_bulletin, "", "", 0, $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, 1, true);
				$aff = $serial->header;
			}
			else if ($r_rel->lnb=='a' && $r_rel->lnh=='2') {
				// c'est un dépouillement de bulletin
				global $link_serial, $link_analysis, $link_bulletin, $link_explnum_serial ;
				if(!$link_analysis){
					$link_analysis=$base_path."/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!";
				}
				$serial = new serial_display($r_rel->linked_notice, 0, $link_serial, $link_analysis, $link_bulletin, "", "", 0, $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, 1, true);
				$aff = $serial->result;
			}
			else {
				if($link_parent && $r_rel->lnb=='b' && $r_rel->lnh=='2'){
					$requete="SELECT bulletin_id FROM bulletins WHERE num_notice='".$r_rel->linked_notice."'";
					$res=pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($res)){
						$link_parent=$base_path."/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".pmb_mysql_result($res,0,0);
					}
				}
				// dans les autres cas
				$parent_notice=new mono_display($r_rel->linked_notice,0,$link_parent, 1, '', "", '', 0, $this->print_mode, $this->show_explnum, $this->show_statut, '', 1, true, $this->show_opac_hidden_fields, 0);
				$aff = $parent_notice->header ;
				$this->nb_expl+=$parent_notice->nb_expl;
			}
			//$parent_notice=new mono_display($r_rel->linked_notice,0,$link_parent);
			//Présentation différente si il y en a un ou plusieurs
			if (pmb_mysql_num_rows($result_linked)==1) {
				$this->isbd.="<br /><b>".$relation_listup->table[$r_rel->relation_type]."</b> ".$aff."<br />";
			} else {
				if ($r_rel->relation_type!=$r_type_local) {
					$r_type_local=$r_rel->relation_type;
					if ($ul_opened) {
						$this->isbd.="</ul>";
						$this->isbd.="\n<b>".$relation_listup->table[$r_rel->relation_type]."</b>";
						$this->isbd.="\n<ul class='notice_rel'>\n";
						$ul_opened=true;
					} else {
						$this->isbd.="\n<br />";
						$this->isbd.="\n<b>".$relation_listup->table[$r_rel->relation_type]."</b>";
						$this->isbd.="\n<ul class='notice_rel'>\n";
						$ul_opened=true;
					}
				}
				$this->isbd.="\n<li>".$aff."</li>\n";
			}
		}
		if ($ul_opened) $this->isbd.="\n</ul>\n";
	}

	if($pmb_show_notice_id || $pmb_show_permalink) $this->isbd .= "<br />";
	if($pmb_show_notice_id){
		$prefixe = explode(",",$pmb_show_notice_id);
		$this->isbd .= "<b>".$msg['notice_id_libelle']."&nbsp;</b>".($prefixe[1] ? $prefixe[1] : '').$this->notice_id."<br />";
	}
	// Permalink OPAC
	if ($pmb_show_permalink) {
		$this->isbd .= "<b>".$msg["notice_permalink_opac"]."&nbsp;</b><a href='".$pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id."' target=\"__LINK__\">".$pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id."</a><br />";
	}
	// niveau 1
	if($this->level == 1) {
		if(!$this->print_mode) $this->isbd .= "<!-- !!bouton_modif!! -->";
		if ($this->expl) {
			$this->isbd .= "<br /><b>${msg[285]}</b> (".$this->nb_expl.")";
			$this->isbd .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl);
		}
		if ($this->show_explnum) {
			$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0,$this->link_explnum);
			if ($explnum_assoc) $this->isbd .= "<b>$msg[explnum_docs_associes]</b>".$explnum_assoc;
		}
		if($this->show_resa) {
			$aff_resa=resa_list ($this->notice_id, 0, 0) ;
			if ($aff_resa) $this->isbd .= "<b>$msg[resas]</b>".$aff_resa;
		}
		if($this->show_planning && $pmb_resa_planning) {
			$aff_resa_planning=planning_list($this->notice_id,0,0) ;
			if ($aff_resa_planning)	$this->isbd .= "<b>$msg[resas_planning]</b>".$aff_resa_planning;
		}
		$this->simple_isbd=$this->isbd;
		$this->do_image($this->isbd) ;
		return;
	}

	// map
	if($pmb_map_activate && $this->show_map){
		$this->isbd.=$this->map->get_map();
	}

	if($pmb_nomenclature_activate){
		$nomenclature= new nomenclature_record_ui($this->notice_id);
		$this->isbd.=$nomenclature->get_isbd();
	}
	// résumé
	if($this->notice->n_resume)
		// $this->isbd .= "<br /><b>${msg[267]}</b>&nbsp;: ".nl2br(htmlentities($this->notice->n_resume,ENT_QUOTES, $charset));
		$this->isbd .= "<br /><b>${msg[267]}</b>&nbsp;: ".nl2br($this->notice->n_resume);

	// note de contenu
	if($this->notice->n_contenu)
		// $this->isbd .= "<br /><b>${msg[266]}</b>&nbsp;: ".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset));
		$this->isbd .= "<br /><b>${msg[266]}</b>&nbsp;: ".nl2br($this->notice->n_contenu);

	// catégories
	$categ_repetables = array() ;
	if(!count($categories_top)) {
		$q = "select num_thesaurus,id_noeud from noeuds where num_parent in(select id_noeud from noeuds where autorite='TOP') ";
		$r = pmb_mysql_query($q, $dbh);
		while($res = pmb_mysql_fetch_object($r)) {
			$categories_top[]=$res->id_noeud;
		}
	}
	$requete = "select * from (
		select libelle_thesaurus, if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, noeuds.id_noeud , noeuds.num_parent, langue_defaut,id_thesaurus, if(catdef.langue = '".$lang."',2, if(catdef.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
		FROM ((noeuds
		join thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus
		left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = thesaurus.langue_defaut
		left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."'))
		,notices_categories
		where notices_categories.num_noeud=noeuds.id_noeud and
		notices_categories.notcateg_notice=".$this->notice_id."	order by id_thesaurus, noeuds.id_noeud, p desc
		) as list_categ group by id_noeud";
	if ($thesaurus_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";

	$result_categ=@pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result_categ)) {
		while($res_categ = pmb_mysql_fetch_object($result_categ)) {
			$libelle_thesaurus=$res_categ->libelle_thesaurus;
			$categ_id=$res_categ->id_noeud 	;
			$libelle_categ=$res_categ->categ_libelle ;
			$num_parent=$res_categ->num_parent ;
			$langue_defaut=$res_categ->langue_defaut ;
			$categ_head=0;
			if(in_array($categ_id,$categories_top)) $categ_head=1;

			if ($thesaurus_categories_show_only_last || $categ_head) {
				if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
				else $url_base_lien_aut=$base_path."/autorites.php?categ=categories&sub=categ_form&id=";
				if ( (SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$libelle_categ."</a>";
				else $libelle_aff_complet =$libelle_categ;
				if ($thesaurus_mode_pmb) {
					$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
				} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

			} else {
				if(!$categories_memo[$categ_id]) {
					$anti_recurse[$categ_id]=1;
					$path_table='';
					$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle, num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
						FROM noeuds, categories where id_noeud ='".$num_parent."'
						AND noeuds.id_noeud = categories.num_noeud
						order by p desc limit 1";

					$result=@pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($result)) {
						$parent = pmb_mysql_fetch_object($result);
						$anti_recurse[$parent->categ_id]=1;
						$path_table[] = array(
									'id' => $parent->categ_id,
									'libelle' => $parent->categ_libelle);

						// on remonte les ascendants
						while (($parent->categ_parent)&&(!$anti_recurse[$parent->categ_parent])) {
							$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
								FROM noeuds, categories where id_noeud ='".$parent->categ_parent."'
								AND noeuds.id_noeud = categories.num_noeud
								order by p desc limit 1";
							$result=@pmb_mysql_query($requete);
							if (pmb_mysql_num_rows($result)) {
								$parent = pmb_mysql_fetch_object($result);
								$anti_recurse[$parent->categ_id]=1;
								$path_table[] = array(
											'id' => $parent->categ_id,
											'libelle' => $parent->categ_libelle);
							} else {
								break;
							}
						}
					$anti_recurse=array();
					} else $path_table=array();
					// ceci remet le tableau dans l'ordre général->particulier
					$path_table = array_reverse($path_table);
					if(sizeof($path_table)) {
						$temp_table='';
						while(list($xi, $l) = each($path_table)) {
							$temp_table[] = $l['libelle'];
						}
						$parent_libelle = join(':', $temp_table);
						$catalog_form = $parent_libelle.':'.$libelle_categ;
					} else {
						$catalog_form = $libelle_categ;
					}

					if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
					else $url_base_lien_aut=$base_path."/autorites.php?categ=categories&sub=categ_form&id=";
					if ((SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$catalog_form."</a>";
					else $libelle_aff_complet =$catalog_form;
					if ($thesaurus_mode_pmb) {
						$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
					} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

					$categories_memo[$categ_id]=$libelle_aff_complet;
					$libelle_thesaurus_memo[$categ_id]=$libelle_thesaurus;

				} else {
					if ($thesaurus_mode_pmb) $categ_repetables[$libelle_thesaurus_memo[$categ_id]][] =$categories_memo[$categ_id];
					else $categ_repetables['MONOTHESAURUS'][] =$categories_memo[$categ_id] ;
				}
			}
		}
	}
	while (list($nom_tesaurus, $val_lib)=each($categ_repetables)) {
		//c'est un tri par libellé qui est demandé
		if ($thesaurus_categories_affichage_ordre==0){
			$tmp=array();
			foreach ( $val_lib as $key => $value ) {
				$tmp[$key]=strip_tags($value);
			}
			$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
			$tmp=array_map("strtoupper",$tmp);//On met en majuscule
			asort($tmp);//Tri sur les valeurs en majuscule sans accent
			foreach ( $tmp as $key => $value ) {
				$tmp[$key]=$val_lib[$key];//On reprend les bons couples clé / libellé
			}
			$val_lib=$tmp;
		}

		if ($thesaurus_mode_pmb) {
			if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = "[".$nom_tesaurus."] ".implode("<br />[".$nom_tesaurus."] ",$val_lib) ;
			else $categ_repetables_aff = "<b>".$nom_tesaurus."</b><br />".implode(" $pmb_keyword_sep ",$val_lib) ;
		} else if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = implode("<br />",$val_lib) ;
		else $categ_repetables_aff = implode(" $pmb_keyword_sep ",$val_lib) ;

		if($categ_repetables_aff) $tmpcateg_aff .= "<br />$categ_repetables_aff";
	}
	if ($tmpcateg_aff) $this->isbd .= "<br />$tmpcateg_aff";

	// Concepts
	if ($thesaurus_concepts_active == 1) {
		$index_concept = new index_concept($this->notice_id, TYPE_NOTICE);
		$this->isbd .= $index_concept->get_isbd_display();
	}

	// langues
	if(count($this->langues)) {
		$langues = "<b>${msg[537]}</b>&nbsp;: ".construit_liste_langues($this->langues);
	}
	if(count($this->languesorg)) {
		$langues .= " <b>${msg[711]}</b>&nbsp;: ".construit_liste_langues($this->languesorg);
	}
	if($langues)
		$this->isbd .= "<br />$langues";

	// indexation libre
	if($this->notice->index_l)
		$this->isbd .= "<br /><b>${msg[324]}</b>&nbsp;: ".nl2br($this->notice->index_l);

	// indexation interne
	if($this->notice->indexint) {
		$indexint = new indexint($this->notice->indexint);
		if ($this->print_mode) $indexint_isbd=$indexint->display;
		else $indexint_isbd=$indexint->isbd_entry_lien_gestion;
		$this->isbd .= "<br /><b>${msg[indexint_catal_title]}</b>&nbsp;: ".$indexint_isbd;
	}

	$tu= new tu_notice($this->notice_id);
	if(($tu_liste=$tu->get_print_type(1))) {
		$this->isbd .= "<br />".$tu_liste;
	}

	$authperso = new authperso_notice($this->notice_id);
	$this->isbd .=$authperso->get_notice_display();

	//Champs personalisés
	$perso_aff = "" ;
	if (!$this->p_perso->no_special_fields) {
		$perso_=$this->p_perso->show_fields($this->notice_id);
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			$p=$perso_["FIELDS"][$i];
			// ajout de && ($p['OPAC_SHOW']||$this->show_opac_hidden_fields) afin de masquer les champs masqués de l'OPAC en diff de bannette.
			if ($p["AFF"] && ($p['OPAC_SHOW'] || $this->show_opac_hidden_fields)) $perso_aff .="<br />".$p["TITRE"]." ".nl2br($p["AFF"]);
		}
	}
	if ($perso_aff) $this->isbd.=$perso_aff ;

	//Notices liées
	if ((count($this->childs))&&(!$this->print_mode)&&(!$this->no_link)) {
		$link = $base_path.'/catalog.php?categ=isbd&id=!!id!!';
		$link_expl = $base_path.'/catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!';
		$link_explnum = $base_path.'/catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
		global $relation_typedown;
		if (!$relation_typedown) $relation_typedown=new marc_list("relationtypedown");
		reset($this->childs);
		if(!$load_tablist_js) $aff_childs="<script type='text/javascript' src='".$base_path."/javascript/tablist.js'></script>\n";
		$aff_childs.="<br />";
		$load_tablist_js=1;
		$anti_loop=$this->anti_loop;
		$anti_loop[]=$this->notice_id;
		$n_childs=0;
		while ((list($rel_type,$child_notices)=each($this->childs))&&($n_childs<100)) {
			$aff_childs.="<b>".$relation_typedown->table[$rel_type]."</b>";
			$aff_childs.="<blockquote>";
			if($pmb_notice_fille_format) $aff_childs.= "<ul class='notice_rel'>";
			for ($i=0; $i<count($child_notices); $i++) {
				$as=array_search($child_notices[$i],$anti_loop);
				if ($as===false) {
					global $pmb_notice_fille_format;
					if($pmb_notice_fille_format) $level_fille = 0;
					else $level_fille = 6;

					// il faut aller chercher le niveau biblio et niveau hierar de la notice liée
					$requete_nbnh="select l.niveau_biblio as lnb, l.niveau_hierar as lnh, rank from notices as l join notices_relations on num_notice=notice_id where notice_id='".$child_notices[$i]."' ";
					$r_rel=pmb_mysql_fetch_object(pmb_mysql_query($requete_nbnh));
					if($r_rel->rank != $i){
						$req = "update notices_relations set rank='$i' where num_notice='".$child_notices[$i]."' and relation_type='".$rel_type."' and linked_notice='".$anti_loop[count($serial->anti_loop)-1]."'";
						pmb_mysql_query($req,$dbh);
					}
					if ($r_rel->lnb=='s' && $r_rel->lnh=='1') {
						// c'est une notice de pério
						global $link_serial, $link_analysis, $link_bulletin, $link_explnum_serial  ;
						$link_serial_sub = $base_path."/catalog.php?categ=serials&sub=view&serial_id=".$child_notices[$i];
						$serial = new serial_display($child_notices[$i], $level_fille, $link_serial_sub, $link_analysis, $link_bulletin, "", $link_explnum_serial, 0, $this->print_mode, 1, 1 ,1,0,0,$anti_loop);

						if((count($serial->anti_loop) == 1) && $sort_children){
							//Drag pour tri des notices filles
							$id_elt =  $serial->notice_id.($serial->anti_loop?"_p".implode("_",$serial->anti_loop):"");
							$drag_fille = "<div id=\"drag_".$id_elt."\" handler=\"handle_".$id_elt."\" dragtype='daughter' draggable='yes' recepttype='daughter' recept='yes'
									dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".htmlentities($serial->tit1,ENT_QUOTES,$charset)."\" callback_before=\"is_expandable\"
									callback_after=\"\" downlight=\"noti_downlight\" highlight=\"noti_highlight\" fille='$child_notices[$i]' pere='".$anti_loop[count($serial->anti_loop)-1]."' order='$i' type_rel=\"$rel_type\" >";
							$drag_fille .= "<span id=\"handle_".$id_elt."\" style=\"float:left; padding-right : 7px\"><img src=\"".$base_path."/images/sort.png\" style='width:12px; vertical-align:middle' /></span>";
							$affichage_result = $serial->result;
						} else {
							$drag_fille ="";
							$affichage_result = ($pmb_notice_fille_format ? "<li>".$serial->result."</li>" : $serial->result);
						}
						$aff = $drag_fille.$affichage_result;
						if($drag_fille)
							$aff .= "</div>";
					}
					else if ($r_rel->lnb=='a' && $r_rel->lnh=='2') {
						// c'est un dépouillement de bulletin
						global $link_serial, $link_analysis, $link_bulletin, $link_explnum_analysis;
						$serial = new serial_display($child_notices[$i], $level_fille, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_analysis, 0, 0, 1, 1, 1, 0, 0, $anti_loop );

						if((count($serial->anti_loop) == 1) && $sort_children){
							//Drag pour tri des notices filles
							$id_elt =  $serial->notice_id.($serial->anti_loop?"_p".implode("_",$serial->anti_loop):"");
							$drag_fille = "<div id=\"drag_".$id_elt."\" handler=\"handle_".$id_elt."\" dragtype='daughter' draggable='yes' recepttype='daughter' recept='yes'
									dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".htmlentities($serial->tit1,ENT_QUOTES,$charset)."\" callback_before=\"is_expandable\"
									callback_after=\"\" downlight=\"noti_downlight\" highlight=\"noti_highlight\" fille='$child_notices[$i]' pere='".$anti_loop[count($serial->anti_loop)-1]."' order='$i' type_rel=\"$rel_type\">";
							$drag_fille .= "<span id=\"handle_".$id_elt."\" style=\"float:left; padding-right : 7px\"><img src=\"".$base_path."/images/sort.png\" style='width:12px; vertical-align:middle' /></span>";
							$affichage_result = $serial->result;
						} else {
							$drag_fille ="";
							$affichage_result = ($pmb_notice_fille_format ? "<li>".$serial->result."</li>" : $serial->result);
						}
						$aff = $drag_fille.$affichage_result;
						if($drag_fille)
							$aff .= "</div>";
					}
					else {
						$display = new mono_display($child_notices[$i], $level_fille, $link, 1, $link_expl, '', $link_explnum,1, 0, 1, 1,$anti_loop,$this->drag, false, true, 0, 1, 0);
						if((count($display->anti_loop) == 1) && $sort_children){
							//Drag pour tri des notices filles
							$id_elt =  $display->notice_id.($display->anti_loop?"_p".implode("_",$display->anti_loop):"");
							$drag_fille = "<div id=\"drag_".$id_elt."\" handler=\"handle_".$id_elt."\" dragtype='daughter' draggable='yes' recepttype='daughter' recept='yes'
									dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".htmlentities($display->tit1,ENT_QUOTES,$charset)."\" callback_before=\"is_expandable\"
									callback_after=\"\" downlight=\"noti_downlight\" highlight=\"noti_highlight\" fille='$child_notices[$i]' pere='".$anti_loop[count($display->anti_loop)-1]."' order='$i' type_rel=\"$rel_type\">";
							$drag_fille .= "<span id=\"handle_".$id_elt."\" style=\"float:left; padding-right : 7px\"><img src=\"".$base_path."/images/sort.png\" style='width:12px; vertical-align:middle' /></span>";
							$affichage_result = $display->result;
						} else {
							$drag_fille ="";
							$affichage_result=($pmb_notice_fille_format ? "<li>".$display->result."</li>" : $display->result);
						}
						$display->result=str_replace("<!-- !!bouton_modif!! -->"," ",$display->result);
						$aff = $drag_fille.$affichage_result;
						$this->nb_expl+=$display->nb_expl;
						if($drag_fille)
							$aff .= "</div>";
					}
					$aff_childs.=$aff;
				}
				$n_childs++;
			}
			$aff_childs.=($pmb_notice_fille_format ? "</ul>" : "")."</blockquote>";
		}
		$this->isbd.=$aff_childs;
	}

	if(!$this->print_mode && !$this->anti_loop) $this->isbd .= "<!-- !!bouton_modif!! -->";
	$this->do_image($this->isbd) ;
	if( !$this->anti_loop)	$this->isbd .= "<!-- !!avis_notice!! -->";
	if($this->expl) {
		if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) { // on est face à une notice de bulletin
			$requete="select bulletin_id from bulletins where num_notice=".$this->notice->notice_id;
			$result=@pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($result)) {
				$bull = pmb_mysql_fetch_object($result);
				$expl_aff = $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl,$bull->bulletin_id);
			}
		}else{
			$expl_aff = $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl);
		}
		if ($expl_aff) {
			$this->isbd .= "<br /><b>${msg[285]} </b>(".$this->nb_expl.")";
			$this->isbd .= $expl_aff;
		}
	}
	if ($this->show_explnum) {
		$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum);
		if ($explnum_assoc) $this->isbd .= "<b>$msg[explnum_docs_associes]</b> (".show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum,array(),true).")".$explnum_assoc;
	}

	//documents numériques en relation...
	$explnum_in_relation = show_explnum_in_relation($this->notice->notice_id, $this->link_explnum);
	if ($explnum_in_relation) $this->isbd .= "<b>".$msg["explnum_docs_in_relation"]."</b>".$explnum_in_relation;

	//reservations et previsions
	if ($this->show_resa || ($this->show_planning && $pmb_resa_planning)) {
		$rqt_nt="select count(*) from exemplaires, notices, docs_statut where exemplaires.expl_statut=docs_statut.idstatut and notices.notice_id=exemplaires.expl_notice and statut_allow_resa=1 and notices.notice_id=".$this->notice_id;
		$result = pmb_mysql_query($rqt_nt, $dbh) or die ($rqt_nt. " ".pmb_mysql_error());
		$nb_expl_reservables = pmb_mysql_result($result,0,0);

		if($this->show_resa) {
			$aff_resa=resa_list($this->notice_id, 0, 0) ;
			$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_from_catal&id_notice=".$this->notice_id."'; return(false) \"";
			if ($aff_resa){
				$this->isbd .= "<b>".$msg['resas']."</b><br />";
				if($nb_expl_reservables && !($categ=="resa") && !$id_empr) $this->isbd .= "<input type='button' class='bouton' value='".$msg['351']."' $ouvrir_reserv><br /><br />";
				$this->isbd .= $aff_resa."<br />";
			} else {
				if ($nb_expl_reservables && !($categ=="resa") && !$id_empr) $this->isbd .= "<b>".$msg['resas']."</b><br /><input type='button' class='bouton' value='".$msg['351']."' $ouvrir_reserv><br /><br />";
			}
		}
		if($this->show_planning && $pmb_resa_planning) {
			$aff_resa_planning=planning_list($this->notice_id,0,0);
			$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_planning_from_catal&id_notice=".$this->notice_id."'; return(false) \"";
			if ($aff_resa_planning){
				$this->isbd .= "<b>".$msg['resas_planning']."</b><br />";
				if($nb_expl_reservables && !($categ=="resa_planning") && !$id_empr) $this->isbd .= "<input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
				$this->isbd .= $aff_resa_planning."<br />";
			} else {
				if ($nb_expl_reservables && !($categ=="resa_planning") && !$id_empr) $this->isbd .= "<b>".$msg['resas_planning']."</b><br /><input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
			}
		}
	}
	return;
}

// génération du header----------------------------------------------------
function do_header() {

	global $msg, $dbh, $base_path;
	global $charset;
	global $pmb_notice_reduit_format;
	global $icon_doc;
	global $tdoc,$biblio_doc;
	global $use_opac_url_base, $opac_url_base, $use_dsi_diff_mode;
	global $no_aff_doc_num_image;
	$aut1_libelle = array() ;

	$type_reduit = substr($pmb_notice_reduit_format,0,1);

	//Icone type de Document
	$icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
	if ($icon) {
		$info_bulle_icon=$biblio_doc[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc];
		if ($use_opac_url_base)	$this->icondoc="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
		else $this->icondoc="<img src=\"".$base_path."/images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
	}

	//Icone nouveauté
	$icon = "icone_nouveautes.png";
	if($this->notice->notice_is_new){
		$info_bulle_icon_new=$msg["notice_is_new_gestion"];
		if ($use_opac_url_base)	$this->icon_is_new="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon_new\" title=\"$info_bulle_icon_new\" align='top' />";
		else $this->icon_is_new="<img src=\"".$base_path."/images/$icon\" alt=\"$info_bulle_icon_new\" title=\"$info_bulle_icon_new\" align='top' />";
	}

	if ($this->notice->statut) {
		$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
		$res_st = pmb_mysql_query($rqt_st, $dbh) or die ($rqt_st. " ".pmb_mysql_error()) ;
		$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
		if ($this->notice->statut>1) $txt = pmb_mysql_result($res_st, 0, 1) ;
		else $txt = "" ;
	} else {
		$class_html = " class='statutnot1' " ;
		$txt = "" ;
	}
	if ($this->notice->commentaire_gestion) {
		if ($txt) $txt .= ":\r\n".$this->notice->commentaire_gestion ;
		else $txt = $this->notice->commentaire_gestion ;
	}
	if ($txt) {
		$statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".$base_path."/images/spacer.gif' width='10' height='10' /></a></span></small>";
		$statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
	} else {
		$statut = "<small><span $class_html style='margin-right: 3px;'><img src='".$base_path."/images/spacer.gif' width='10' height='10' /></span></small>";
	}
	$this->aff_statut = $statut;

	if ($type_reduit=="H"){
		$id_tpl=substr($pmb_notice_reduit_format,2);
		if($id_tpl){
			$tpl = new notice_tpl_gen($id_tpl);
			$notice_tpl_header=$tpl->build_notice($this->notice_id);
			if($notice_tpl_header){
 				$this->header=$notice_tpl_header;
			}
		}
	}
	if ($type_reduit=="E" || $type_reduit=="P" ) {
		// peut-être veut-on des personnalisés ?
		$perso_voulus_temp = substr($pmb_notice_reduit_format,2) ;
		if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
	}

	if ($type_reduit=="E") {
		// zone de l'éditeur
		if ($this->notice->ed1_id) {
			$editeur = new editeur($this->notice->ed1_id);
			$editeur_reduit = $editeur->display ;
			if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
		} elseif ($this->notice->year) {
			// année mais pas d'éditeur et si pas un article
			if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
		}
	} else $editeur_reduit = "" ;

	if ($type_reduit=="E" || $type_reduit=="P" ) {
		if (!is_object($this->p_perso)) $this->p_perso = new parametres_perso("notices");
		//Champs personalisés à ajouter au réduit
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
				$perso_voulu_aff=trim($perso_voulu_aff);
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
	}

	if ($type_reduit!="H") {
		// récupération du titre de série
		if($this->tit_serie) {
			$this->header =$this->header_texte= $this->tit_serie;
			if($this->notice->tnvol) {
				$this->header .= ',&nbsp;'.$this->notice->tnvol;
				$this->header_texte .= ', '.$this->notice->tnvol;
			}
		} elseif($this->notice->tnvol){
			$this->header .= $this->notice->tnvol;
			$this->header_texte .= $this->notice->tnvol;
		}
		$this->tit1 = $this->notice->tit1;
		$this->header ? $this->header .= '.&nbsp;'.$this->tit1 : $this->header = $this->tit1;
		$this->header_texte ? $this->header_texte .= '. '.$this->tit1 : $this->header_texte = $this->tit1;
		$this->memo_titre=$this->header_texte;
		$this->memo_complement_titre=$this->notice->tit4;
		$this->memo_titre_parallele=$this->notice->tit3;
	}

	if ($type_reduit=='4') {
		if ($this->memo_titre_parallele != "") {
			$this->header .= "&nbsp;=&nbsp;".$this->memo_titre_parallele;
 			$this->header_texte .= ' = '.$this->memo_titre_parallele;
		}
	}

//	if ((floor($type_reduit/10) == 1)&&($this->memo_complement_titre)) {
//		$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
//		$this->header_texte.=" : ".$this->memo_complement_titre;
//	}

	if ($type_reduit=="T" && $this->memo_complement_titre) {
		$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
		$this->header_texte.=" : ".$this->memo_complement_titre;
	}

	if (($type_reduit!='3') && ($type_reduit!='H')) {
		//$this->responsabilites
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			if ($auteur->isbd_entry){
				$this->header .= ' / '. $auteur->isbd_entry;
				$this->header_texte .= ' / '. $auteur->isbd_entry;
			}
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_1["id"]);
				$aut1_libelle[]= $auteur->isbd_entry;
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) {
				$this->header .= ' / '. $auteurs_liste ;
				$this->header_texte .= ' / '. $auteurs_liste ;
			}
		}
	}

	if ($editeur_reduit) {
		$this->header .= ' / '. $editeur_reduit ;
		$this->header_texte .= ' / '. $editeur_reduit ;
	}
	if ($perso_voulu_aff) {
		$this->header .= ' / '. $perso_voulu_aff ;
		$this->header_texte .= ' / '. $perso_voulu_aff ;
	}

	switch ($type_reduit) {
		case "1":
			if ($this->notice->year != '') {
				$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				$this->header_texte.=' ('.$this->notice->year.')';
			}
			break;
		case "2":
			if ($this->notice->year != '') {
				$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				$this->header_texte.=' ('.$this->notice->year.')';
			}
			if ($this->notice->code != '') {
				$this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
				$this->header_texte.=' / '.$this->notice->code;
			}
			break;
		default :
			break;
	}

	if ($this->drag)
		$drag="<span onMouseOver='if(init_drag) init_drag();' id=\"NOTI_drag_".$this->notice_id.($this->anti_loop?"_p".$this->anti_loop[count($this->anti_loop)-1]:"")."\"  dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".$this->header."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".$base_path."/images/notice_drag.png\"/></span>";

	if($this->action) {
		$this->header = "<a href=\"".$this->action."\">".$this->header.'</a>';
	}
	if ($this->icon_is_new) $this->header = $this->header." ".$this->icon_is_new;
	if ($this->notice->niveau_biblio=='b') {
		$rqt="select tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date, bulletin_numero as num_bull from bulletins,notices where bulletins.num_notice='".$this->notice_id."' and notices.notice_id=bulletins.bulletin_notice";
		$execute_query=pmb_mysql_query($rqt);
		$row=pmb_mysql_fetch_object($execute_query);
		$this->header.=" <i>".(!$row->aff_date_date?sprintf($msg["bul_titre_perio"],$row->tit1):sprintf($msg["bul_titre_perio"],$row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]"))."</i>";
		$this->header_texte.=" ".(!$row->aff_date_date?sprintf($msg["bul_titre_perio"],$row->tit1):sprintf($msg["bul_titre_perio"],$row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]"));
		pmb_mysql_free_result($execute_query);
	}
	if ($this->drag) $this->header.=$drag;

	if($this->notice->lien) {
		// ajout du lien pour les ressource notice_parent_useds électroniques
		if (!$this->print_mode || $this->print_mode=='2' || $use_dsi_diff_mode) {
			$this->header .= "<a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			else $this->header .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->header .= " alt=\"";
			$this->header .= $this->notice->eformat;
			$this->header .= "\" title=\"";
			$this->header .= $this->notice->eformat;
			$this->header .= "\">";
			$this->header .='</a>';
		} elseif ($this->print_mode=='4') {
			$this->header .= '<br />';
			$this->header .= "<a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->header .= '<font size="-1">'.$this->notice->lien.'</font>';
			$this->header .='</a>';
		} else {
			$this->header .= "<br />";
			$this->header .= '<font size="-1">'.$this->notice->lien.'</font>';
		}
	}
	if(!$this->print_mode || $this->print_mode=='2' && !$no_aff_doc_num_image)	{
		if ($this->notice->niveau_biblio=='b')
			$sql_explnum = "SELECT explnum_id, explnum_nom FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		else
			$sql_explnum = "SELECT explnum_id, explnum_nom FROM explnum WHERE explnum_notice = ".$this->notice_id;

		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);
		if ($explnumscount == 1) {
			$explnumrow = pmb_mysql_fetch_object($explnums);
			if (!$use_opac_url_base) $this->header .= "<a href=\"".$base_path."/doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
			else $this->header .= "<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
			if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
			else $this->header .= "<img src=\"".$opac_url_base."images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->header .= " alt=\"";
			$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
			$this->header .= "\" title=\"";
			$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
			$this->header .= "\">";
			$this->header .='</a>';
		}
		else if ($explnumscount > 1) {
			if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe_rouge.png\" border=\"0\" align=\"middle\" alt=\"".$msg['info_docs_num_notice']."\" title=\"".$msg['info_docs_num_notice']."\" hspace=\"3\">";
			else $this->header .= "<img src=\"".$opac_url_base."images/globe_rouge.png\" border=\"0\" align=\"middle\" alt=\"".$msg['info_docs_num_notice']."\" title=\"".$msg['info_docs_num_notice']."\" hspace=\"3\">";
		}
	}
	if ($this->icondoc) $this->header = $this->icondoc." ".$this->header;
	if ($this->show_statut) $this->header = $this->aff_statut." ".$this->header ;
}

// récupération des valeurs en table---------------------------------------
function mono_display_fetch_data() {
	global $dbh;

	$requete = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
	$myQuery = pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($myQuery)) {
		$this->notice = pmb_mysql_fetch_object($myQuery);
	}
	$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
	$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales

	//Récupération titre de série
	if($this->notice->tparent_id) {
		$parent = new serie($this->notice->tparent_id);
		$this->tit_serie = $parent->name;
		$this->tit_serie_lien_gestion = $parent->isbd_entry_lien_gestion;
	}

	$this->isbn = $this->notice->code ;
	return pmb_mysql_num_rows($myQuery);
}

// fonction retournant les infos d'exemplaires pour une notice donnée
function show_expl_per_notice($no_notice, $link_expl='',$expl_bulletin=0 ) {
	global $msg, $dbh, $base_path, $class_path;
	global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises, $transferts_gestion_transferts;
	global $pmb_expl_list_display_comments;
	global $pmb_sur_location_activate;
	global $pmb_url_base, $pmb_expl_data,$charset;
	global $pmb_expl_display_location_without_expl;
	global $pmb_html_allow_expl_cote;
	// params :
	// $no_notice= id de la notice
	// $link_expl= lien associé à l'exemplaire avec !!expl_id!! et !!expl_cb!! à mettre à jour

	if(!$no_notice && !$expl_bulletin) return;

	$explr_tab_invis=explode(",",$explr_invisible);
	$explr_tab_unmod=explode(",",$explr_visible_unmod);
	$explr_tab_modif=explode(",",$explr_visible_mod);

	// récupération du nombre total d'exemplaires
	if($expl_bulletin){
		$requete = "SELECT COUNT(1) FROM exemplaires WHERE expl_bulletin='$expl_bulletin' ";
	}else{
		$requete = "SELECT COUNT(1) FROM exemplaires WHERE expl_notice='$no_notice' ";
	}
	$res = pmb_mysql_query($requete, $dbh);
	$nb_ex = pmb_mysql_result($res, 0, 0);

	if($nb_ex) {
		// on récupère les données des exemplaires
		// visibilité des exemplaires:
		if ($pmb_droits_explr_localises && $explr_invisible) $where_expl_localises = "and expl_location not in ($explr_invisible)";
		else $where_expl_localises = "";

		//Liste des champs d'exemplaires
		if($pmb_sur_location_activate) $surloc_field="surloc_libelle,";
		if (!$pmb_expl_data) $pmb_expl_data="expl_cb,expl_cote,".$surloc_field."location_libelle,section_libelle,statut_libelle,tdoc_libelle";
		$colonnesarray=explode(",",$pmb_expl_data);
		if (!in_array("expl_cb", $colonnesarray)) array_unshift($colonnesarray, "expl_cb");
		$total_columns = count($colonnesarray);

		//Présence de champs personnalisés
		if (strstr($pmb_expl_data, "#")) {
			require_once($class_path."/parametres_perso.class.php");
			$cp=new parametres_perso("expl");
		}
		if($expl_bulletin){
			$where_expl_notice_expl_bulletin = " expl_bulletin='$expl_bulletin' ";
		}else{
			$where_expl_notice_expl_bulletin = " expl_notice='$no_notice' ";
		}
		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_codestat.*, lenders.*, tdoc_libelle, ";
		if(in_array("surloc_libelle", $colonnesarray)){
			$requete .= "sur_location.*, ";
		}
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		if(in_array("surloc_libelle", $colonnesarray)){
			$requete .= " left join sur_location on docs_location.surloc_num=sur_location.surloc_id ";
		}
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_codestat on exemplaires.expl_codestat=docs_codestat.idcode ";
		$requete .= " left join lenders on exemplaires.expl_owner=lenders.idlender ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " WHERE $where_expl_notice_expl_bulletin $where_expl_localises ";
		if(in_array("surloc_libelle", $colonnesarray)){
			$requete .= " order by surloc_libelle,location_libelle, section_libelle, expl_cote, expl_cb ";
		}else{
			$requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
		}
		$result = pmb_mysql_query($requete, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);

		$nbr_expl = pmb_mysql_num_rows($result);
		if ($nbr_expl) {
			while($expl = pmb_mysql_fetch_object($result)) {
				//visibilité des exemplaires
				if ($pmb_droits_explr_localises) {
					$as_invis = array_search($expl->idlocation,$explr_tab_invis);
					$as_unmod = array_search($expl->idlocation,$explr_tab_unmod);
					$as_modif = array_search($expl->idlocation,$explr_tab_modif);
				} else {
					$as_invis = false;
					$as_unmod = false;
					$as_modif = true;
				}
				if ($link_expl) {
					if($expl_bulletin){
						$tlink="./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=!!bull_id!!&expl_id=!!expl_id!!";
						$tlink = str_replace('!!bull_id!!', $expl_bulletin, $tlink);
						$tlink = str_replace('!!expl_id!!', $expl->expl_id, $tlink);
						$tlink = str_replace('!!expl_cb!!', rawurlencode($expl->expl_cb), $tlink);
					}else{
						$tlink = str_replace('!!expl_id!!', $expl->expl_id, $link_expl);
						$tlink = str_replace('!!expl_cb!!', rawurlencode($expl->expl_cb), $tlink);
						$tlink = str_replace('!!notice_id!!', $expl->expl_notice, $tlink);
					}

				}
				$expl_liste .= "<tr>";

				for ($i=0; $i<count($colonnesarray); $i++) {
					if (!(substr($colonnesarray[$i],0,1)=="#")) eval ("\$colencours=\$expl->".$colonnesarray[$i].";");

					if (($i == 0) && ($expl->expl_note || $expl->expl_comment) && $pmb_expl_list_display_comments) $expl_rowspan = "rowspan='2'";
					else $expl_rowspan = "";
					$aff_column = "";
					if (substr($colonnesarray[$i],0,1)=="#") {
						//champs personnalisés
						$id=substr($colonnesarray[$i],1);
						$cp->get_values($expl->expl_id);
						if (!$cp->no_special_fields) {
							$temp=$cp->get_formatted_output($cp->values[$id], $id);
							if (!$temp) $temp="&nbsp;";
							$aff_column.=$temp;
						}
					} else if ($colonnesarray[$i]=="expl_cb") {
						if (($tlink) && ($as_modif!== FALSE && $as_modif!== NULL) ) {
							$aff_column .= "<a href='$tlink'>".$colencours."</a>";
						} else $aff_column .= $colencours;
					} else if ($colonnesarray[$i]=="expl_cote") {
						if ($pmb_html_allow_expl_cote) {
							$aff_column.="<strong>".$colencours."</strong>";
						} else {
							$aff_column.="<strong>".htmlentities($colencours,ENT_QUOTES, $charset)."</strong>";
						}
					} else if ($colonnesarray[$i]=="statut_libelle") {
						if($expl->pret_retour) {
							// exemplaire sorti
							$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl->pret_idempr' ";
							$res_empr = pmb_mysql_query($rqt_empr, $dbh) ;
							$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
							$situation = "<strong>${msg[358]} ".$expl->aff_pret_retour."</strong>";
							global $empr_show_caddie, $selector_prop_ajout_caddie_empr;
							if ($empr_show_caddie && (SESSrights & CIRCULATION_AUTH)) {
								$img_ajout_empr_caddie="<img src='".$base_path."/images/basket_empr.gif' align='middle' alt='basket' title=\"${msg[400]}\" onClick=\"openPopUp('".$base_path."/cart.php?object_type=EMPR&item=".$expl->pret_idempr."', 'cart', 600, 700, -2, -2, '$selector_prop_ajout_caddie_empr')\">&nbsp;";
							} else $img_ajout_empr_caddie="";
							switch ($this->print_mode) {
								case '2':
									$situation .= "<br />$res_empr_obj->empr_prenom $res_empr_obj->empr_nom";
									break;
								default :
									$situation .= "<br />$img_ajout_empr_caddie<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode($res_empr_obj->empr_cb)."'>$res_empr_obj->empr_prenom $res_empr_obj->empr_nom</a>";
								break;
							}
						} else {
							// tester si réservé
							$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($expl->expl_cb)."' ", $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);
							$reserve = pmb_mysql_num_rows($result_resa);

							// tester à ranger
							$result_aranger = pmb_mysql_query(" select 1 from resa_ranger where resa_cb='".addslashes($expl->expl_cb)."' ", $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);
							$aranger = pmb_mysql_num_rows($result_aranger);

							if ($reserve) $situation = "<strong>".$msg['expl_reserve']."</strong>"; // exemplaire réservé
							elseif($expl->expl_retloc) $situation = $msg['resa_menu_a_traiter'];  // exemplaire à traiter
							elseif ($aranger) $situation = "<strong>".$msg['resa_menu_a_ranger']."</strong>"; // exemplaire à ranger
							elseif ($expl->pret_flag) $situation = "<strong>${msg[359]}</strong>"; // exemplaire disponible
							else $situation = "";
						}

						$aff_column .= htmlentities($colencours,ENT_QUOTES, $charset);
						if ($situation) $aff_column .= "<br />$situation";
					} else
						$aff_column = htmlentities($colencours,ENT_QUOTES, $charset);

					$expl_liste .= "<td $expl_rowspan>".$aff_column."</td>";
				}
				if ($this->print_mode)
					$expl_liste .= "<td>&nbsp;</td>";
				else {

					if(SESSrights & CATALOGAGE_AUTH){
						//le panier d'exemplaire
						$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=EXPL&item=".$expl->expl_id."', 'cart', 600, 700, -2, -2, 'toolbar=no, dependent=yes, width=500, height=400, resizable=yes, scrollbars=yes')\"";
						$cart_link = "<a href='#' $cart_click><img src='".$base_path."/images/basket_small_20x20.gif' align='center' alt='basket' title=\"${msg[400]}\"></a>";
						//l'icon pour le drag&drop de panier
						$drag_link = "<span onMouseOver='if(init_drag) init_drag();' id='EXPL_drag_" . $expl->expl_id . "'  dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".htmlentities ( $expl->expl_cb,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".$base_path."/images/notice_drag.png\"/></span>";
					}else{
						$cart_click = "";
						$cart_link = "";
						$drag_link = "";
					}

					//l'impression de la fiche exemplaire
					$fiche_click = "onClick=\"openPopUp('".$base_path."/pdf.php?pdfdoc=fiche_catalographique&expl_id=".$expl->expl_id."', 'Fiche', 500, 400, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\"";
					$fiche_link = "<a href='#' $fiche_click><img src='".$base_path."/images/print.gif' align='center' alt='".$msg ['print_fiche_catalographique']."' title='".$msg ['print_fiche_catalographique']."'></a>";

					global $pmb_transferts_actif;

					//si les transferts sont activés
					if ($pmb_transferts_actif) {
						//si l'exemplaire n'est pas transferable on a une image vide
						$transfer_link = "<img src='".$base_path."/images/spacer.gif' align='center' height=20 width=20>";

						$dispo_pour_transfert = transfert::est_transferable ( $expl->expl_id );
						if (SESSrights & TRANSFERTS_AUTH && $dispo_pour_transfert)
							//l'icon de demande de transfert
							$transfer_link = "<a href=\"#\" onClick=\"openPopUp('".$base_path."/catalog/transferts/transferts_popup.php?expl=".$expl->expl_id."', 'cart', 600, 450, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');\"><img src='".$base_path."/images/peb_in.png' align='center' border=0 alt=\"".$msg ["transferts_alt_libelle_icon"]."\" title=\"".$msg ["transferts_alt_libelle_icon"]."\"></a>";
					}

					//on met tout dans la colonne
					$expl_liste .= "<td>$fiche_link $cart_link $transfer_link $drag_link</td>";
				}
				$expl_liste .= "</tr>";
				if (($expl->expl_note || $expl->expl_comment) && $pmb_expl_list_display_comments) {
					$notcom=array();
					$expl_liste .= "<tr><td colspan='".$total_columns."'>";
					if ($expl->expl_note && ($pmb_expl_list_display_comments & 1)) $notcom[] .= "<span class='erreur'>$expl->expl_note</span>";
					if ($expl->expl_comment && ($pmb_expl_list_display_comments & 2)) $notcom[] .= "$expl->expl_comment";
					$expl_liste .= implode("<br />",$notcom);
					$expl_liste .= "</tr>";
				}

			} // fin while
		} // fin il y a des expl visibles

		if ($expl_liste) {
			$entry = "<table border='0' class='expl-list'><tr>";
			for ($i=0; $i<count($colonnesarray); $i++) {
				if (substr($colonnesarray[$i],0,1)=="#") {
					//champs personnalisés
					if (!$cp->no_special_fields) {
						$id=substr($colonnesarray[$i],1);
						$entry.="<th>".htmlentities($cp->t_fields[$id][TITRE],ENT_QUOTES,$charset)."</th>";
					}
				} else {
					eval ("\$colencours=\$msg[expl_header_".$colonnesarray[$i]."];");
					$entry.="<th>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
				}
			}
			$entry.="<th>&nbsp;</th></tr>$expl_liste</table>";
		} else $entry = "";

		if($pmb_expl_display_location_without_expl){
			if ($pmb_sur_location_activate) {
				$array_surloc = array();
				$requete = "SELECT * FROM sur_location ORDER BY surloc_libelle";
				$result = pmb_mysql_query($requete, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);
				$nb_surloc = pmb_mysql_num_rows($result);
				if ($nb_surloc) {
					while($surloc = pmb_mysql_fetch_object($result)) {
						$array_surloc[]=array("id"=>$surloc->surloc_id, "libelle"=>$surloc->surloc_libelle, "locations"=>array());
					}
				}
				if (count($array_surloc)) {
					foreach ($array_surloc as $key=>$surloc) {
						$requete = "SELECT idlocation, location_libelle from docs_location where surloc_num=".$surloc["id"]." AND
						idlocation not in (SELECT expl_location from exemplaires WHERE expl_notice=$no_notice) order by location_libelle";

						$result = pmb_mysql_query($requete, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);
						$nb_loc = pmb_mysql_num_rows($result);
						if ($nb_loc) {
							while($loc = pmb_mysql_fetch_object($result)) {
								$array_surloc[$key]["locations"][] = array("id"=>$loc->idlocation, "libelle"=>$loc->location_libelle);
							}
						} else {
							unset($array_surloc[$key]);
						}
					}
				}
				//Au moins une surloc à afficher
				if (count($array_surloc)) {
					$tr_surloc="";
					foreach ($array_surloc as $key => $surloc) {
						$tr_surloc.="<tr><td>";
						$tr_loc="";
						foreach ($surloc["locations"] as $keyloc => $loc) {
							$tr_loc.="<tr><td>".$loc["libelle"]."</td></tr>";
						}
						$tpl_surloc= "
							<table border='0' class='expl-list'>
								$tr_loc
							</table>";
						$tr_surloc.=gen_plus('surlocation_without_expl'.$key.'_'.$no_notice,$surloc["libelle"],$tpl_surloc,0);
						$tr_surloc.="</td></tr>";
					}
					$tpl = "
					<table border='0' class='expl-list'>
						$tr_surloc
					</table>";
					$entry.=gen_plus('location_without_expl'.$no_notice,$msg['expl_surlocation_without_expl'],$tpl,0);
				}
			} else {
				$requete = "SELECT location_libelle from docs_location where
				idlocation not in (SELECT expl_location from exemplaires WHERE expl_notice=$no_notice) order by location_libelle";

				$result = pmb_mysql_query($requete, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$requete);
				$nb_loc = pmb_mysql_num_rows($result);
				if ($nb_loc) {
					$items="";
					while($loc = pmb_mysql_fetch_object($result)) {
						$items.="<tr><td>".$loc->location_libelle."</td></tr>";
					}

					$tpl = "
					<table border='0' class='expl-list'>
						$items
					</table>";
					$tpl=gen_plus('location_without_expl'.$no_notice,$msg['expl_location_without_expl'],$tpl,0);
					$entry.=$tpl;
				}
			}
		}
		$this->nb_expl=$nbr_expl;
		return $entry;
	} else {
		return "";
	}
}


/**
 * Creation de l'image vignette associée
 *
 * @param  $entree
 */
function do_image(&$entree) {
	global $charset;
	global $pmb_book_pics_show ;
	global $pmb_book_pics_url ;
	global $pmb_book_pics_msg;
	// pour url OPAC en diff DSI
	global $prefix_url_image ;
	global $depliable ;

	if ($this->notice->code || $this->notice->thumbnail_url) {
		if ($pmb_book_pics_show=='1' && ($pmb_book_pics_url || $this->notice->thumbnail_url)) {
			$code_chiffre = pmb_preg_replace('/-|\.| /', '', $this->notice->code);
			$url_image = $pmb_book_pics_url ;
			$url_image = $prefix_url_image."getimage.php?url_image=".urlencode($url_image)."&amp;noticecode=!!noticecode!!&amp;vigurl=".urlencode($this->notice->thumbnail_url) ;
			if ($depliable) $image = "<img class='img_notice' id='PMBimagecover".$this->notice_id."' src='".$prefix_url_image."images/vide.png' align='right' hspace='4' vspace='2' isbn='".$code_chiffre."' url_image='".$url_image."' vigurl=\"".$this->notice->thumbnail_url."\">";
			else {
				if ($this->notice->thumbnail_url) {
					$url_image_ok=$this->notice->thumbnail_url;
					$title_image_ok="";
				} else {
					$url_image_ok = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					$title_image_ok = htmlentities($pmb_book_pics_msg, ENT_QUOTES, $charset) ;
				}
				$image = "<img class='img_notice' id='PMBimagecover".$this->notice_id."' src='".$url_image_ok."' title=\"".$title_image_ok."\" align='right' hspace='4' vspace='2'>";
			}
		} else $image="" ;
		if ($image) {
			$entree = "<table width='100%'><tr><td valign=top>$entree</td><td valign=top align=right>$image</td></tr></table>" ;
		} else {
			$entree = "<table width='100%'><tr><td valign=top>$entree</td></tr></table>" ;
		}

	} else {
		$entree = "<table width='100%'><tr><td valign=top>$entree</td></tr></table>" ;
	}
}

}