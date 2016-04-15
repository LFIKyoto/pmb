<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_datas.class.php,v 1.18.2.6 2015-12-10 14:12:36 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/acces.class.php");
require_once($class_path."/map/map_objects_controler.class.php");
require_once($class_path."/map_info.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/tu_notice.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/collstate.class.php");
require_once($class_path."/enrichment.class.php");
require_once($class_path."/skos/skos_concepts_list.class.php");
require_once($class_path."/authorities_collection.class.php");

if (!count($tdoc)) $tdoc = new marc_list('doctype');

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

/**
 * Classe qui représente les données d'une notice
 * @author apetithomme
 *
*/
class record_datas {

	/**
	 * Identifiant de la notice
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var domain
	 */
	private $dom_2 = null;

	/**
	 *
	 * @var domain
	 */
	private $dom_3 = null;

	/**
	 * Droits d'accès emprunteur/notice
	 * @var int
	 */
	private $rights = 0;

	/**
	 * Objet notice fetché en base
	 * @var stdClass
	 */
	private $notice;

	/**
	 * Tableau des informations du parent dans le cas d'un article
	 * @var array
	 */
	private $parent;

	/**
	 * Carte associée
	 * @var map_objects_controler
	*/
	private $map = null;

	/**
	 * Info de la carte associée
	 * @var map_info
	 */
	private $map_info = null;

	/**
	 * Paramètres persos
	 * @var parametres_perso
	 */
	private $p_perso = null;

	/**
	 * Libellé du statut de la notice
	 * @var string
	 */
	private $statut_notice = "";

	/**
	 * Visibilité de la notice à tout le monde
	 * @var int
	 */
	private $visu_notice = 1;

	/**
	 * Visibilité de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_notice_abon = 0;

	/**
	 * Visibilité des exemplaires de la notice à tout le monde
	 * @var int
	 */
	private $visu_expl = 1;

	/**
	 * Visibilité des exemplaires de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_expl_abon = 0;

	/**
	 * Visibilité des exemplaires numériques de la notice à tout le monde
	 * @var int
	 */
	private $visu_explnum = 1;

	/**
	 * Visibilité des exemplaires numériques de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_explnum_abon = 0;

	/**
	 * Tableau des auteurs
	 * @var array
	 */
	private $responsabilites = array();

	/**
	 * Auteurs principaux
	 * @var string
	*/
	private $auteurs_principaux;
	
	/**
	 * Catégories
	 * @var categorie
	 */
	private $categories;
	
	/**
	 * Titre uniforme
	 * @var tu_notice
	 */
	private $titre_uniforme = null;
	
	/**
	 * Avis
	 * @var array
	 */
	private $avis = array();
	
	/**
	 * Langues
	 * @var array
	 */
	private $langues = array();
	
	/**
	 * Nombre de bulletins associés
	 * @var int
	 */
	private $nb_bulletins;
	
	/**
	 * Tableau des bulletins associés
	 * @var array
	 */
	private $bulletins = array();
	
	/**
	 * Nombre de documents numériques associés aux bulletins
	 * @var int
	 */
	private $nb_bulletins_docnums;
	
	/**
	 * Indique si le pério est ouvert à la recherche
	 * @var int
	 */
	private $open_to_search;
	
	/**
	 * Editeurs
	 * @var publisher
	 */
	private $publishers = array();
	
	/**
	 * Etat de collections
	 * @var collstate
	 */
	private $collstate;
	
	/**
	 * Autorisation des avis
	 * @var int
	 */
	private $avis_allowed;
	
	/**
	 * Autorisation des tags
	 * @var int
	 */
	private $tag_allowed;
	
	/**
	 * Autorisation des suggestions
	 * @var int
	 */
	private $sugg_allowed;
	
	/**
	 * Tableau des sources d'enrichissement actives pour cette notice
	 * @var array
	 */
	private $enrichment_sources;
	
	/**
	 * Icone du type de document
	 * @var string
	 */
	private $icon_doc;
	
	/**
	 * Libellé du niveau biblio
	 * @var string
	 */
	private $biblio_doc;
	
	/**
	 * Libellé du type de document
	 * @var string
	 */
	private $tdoc;
	
	/**
	 * Liste de concepts qui indexent la notice
	 * @var skos_concepts_list
	 */
	private $concepts_list = null;
	
	/**
	 * Tableau des mots clés
	 * @var array
	 */
	private $mots_cles;
	
	/**
	 * Indexation décimale
	 * @var indexint
	 */
	private $indexint = null;
	
	/**
	 * Collection
	 * @var collection
	 */
	private $collection = null;
	
	/**
	 * Sous-collection
	 * @var subcollection
	 */
	private $subcollection = null;
	
	/**
	 * Permalink
	 * @var string
	 */
	private $permalink;
	
	/**
	 * Tableau des ids des notices du même auteur
	 * @var array
	 */
	private $records_from_same_author;
	
	/**
	 * Tableau des ids des notices du même éditeur
	 * @var array
	 */
	private $records_from_same_publisher;
	
	/**
	 * Tableau des ids des notices de la même collection
	 * @var array
	 */
	private $records_from_same_collection;
	
	/**
	 * Tableau des ids des notices dans la même série
	 * @var array
	 */
	private $records_from_same_serie;
	
	/**
	 * Tableau des ids des notices avec la même indexation décimale
	 * @var array
	 */
	private $records_from_same_indexint;
	
	/**
	 * Tableau des ids de notices avec des catégories communes
	 * @var array
	 */
	private $records_from_same_categories;
	
	/**
	 * URL vers l'image de la notice
	 * @var string
	 */
	private $picture_url;
	
	/**
	 * Disponibilité
	 * @var array
	 */
	private $availability;
	
	/**
	 * Paramètres de réservation
	 * @var array
	 */
	private $resas_datas;
	
	/**
	 * Données d'exemplaires
	 * @var array
	 */
	private $expls_datas;
	
	/**
	 * Données de série
	 * @var array
	 */
	private $serie;
	
	/**
	 * Tableau des relations parentes
	 * @var array
	 */
	private $relations_up;
	
	/**
	 * Tableau des relations enfants
	 * @var array
	 */
	private $relations_down;
	
	/**
	 * Tableau des dépouillements
	 * @var array
	 */
	private $articles;
	
	/**
	 * Données de demandes
	 * @var array
	 */
	private $demands_datas;
	
	/**
	 * Panier autorisé selon paramètres PMB et utilisateur connecté
	 * @var boolean
	 */
	private $cart_allow;
	
	/**
	 * Informations de documents numériques associés
	 * @var array
	 */
	private $explnums_datas;
	
	public function __construct($id) {
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
		global $to_print;
		global $opac_avis_allow, $opac_allow_add_tag, $opac_show_suggest_notice;

		$this->id = $id*1;

		if (!$this->id) return;

		$this->fetch_visibilite();

		$this->fetch_data();
		
		if ($to_print) {
			$this->avis_allowed = 0;
			$this->tag_allowed = 0;
			$this->sugg_allowed = 0;
		} else {
			$this->avis_allowed = $opac_avis_allow;
			$this->tag_allowed = $opac_allow_add_tag;
			$this->sugg_allowed = $opac_show_suggest_notice;
		}
			
		$this->to_print = $to_print;
	}

	/**
	 * Charge les infos présentes en base de données
	 */
	private function fetch_data() {
		global $dbh;

		if(is_null($this->dom_2)) {
			$query = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, statut, thumbnail_url, opac_visible_bulletinage, notice_is_new, notice_date_is_new ";
			$query.= "FROM notices WHERE notice_id='".$this->id."' ";
		} else {
			$query = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, thumbnail_url, opac_visible_bulletinage, notice_is_new, notice_date_is_new ";
			$query.= "FROM notices ";
			$query.= "WHERE notice_id='".$this->id."'";
		}
		$result = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$this->notice = pmb_mysql_fetch_object($result);
		}
	}
	
	/**
	 * Retourne l'identifiant de la notice
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Retourne les infos de bulletinage
	 *
	 * @return array Informations de bulletinage si applicable, un tableau vide sinon<br />
	 * $this->parent = array('title', 'id', 'bulletin_id', 'numero', 'date', 'date_date', 'aff_date_date')
	 */
	public function get_bul_info() {
		if (!$this->parent) {
			global $dbh, $msg;
			
			$this->parent = array();
	
			$query = "";
			if ($this->notice->niveau_hierar == 2) {
				if ($this->notice->niveau_biblio == 'a') {
					// récupération des données du bulletin et de la notice apparentée
					$query = "SELECT b.tit1,b.notice_id,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
					$query .= "from analysis a, notices b, bulletins c";
					$query .= " WHERE a.analysis_notice=".$this->id;
					$query .= " AND c.bulletin_id=a.analysis_bulletin";
					$query .= " AND c.bulletin_notice=b.notice_id";
					$query .= " LIMIT 1";
				} elseif ($this->notice->niveau_biblio == 'b') {
					// récupération des données du bulletin et de la notice apparentée
					$query = "SELECT tit1,notice_id,b.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
					$query .= "from bulletins b, notices";
					$query .= " WHERE num_notice=$this->id ";
					$query .= " AND  bulletin_notice=notice_id ";
					$query .= " LIMIT 1";
				}
				if ($query) {
					$result = pmb_mysql_query($query, $dbh);
					if (pmb_mysql_num_rows($result)) {
						$parent = pmb_mysql_fetch_object($result);
						$this->parent['title'] = $parent->tit1;
						$this->parent['id'] = $parent->notice_id;
						$this->parent['bulletin_id'] = $parent->bulletin_id;
						$this->parent['numero'] = $parent->bulletin_numero;
						$this->parent['date'] = $parent->mention_date;
						$this->parent['date_date'] = $parent->date_date;
						$this->parent['aff_date_date'] = $parent->aff_date_date;
					}
				}
			}
		}
		return $this->parent;
	}

	/**
	 * Retourne le type de document
	 *
	 * @return string
	 */
	public function get_typdoc() {
		if (!$this->notice->typdoc) $this->notice->typdoc='a';
		return $this->notice->typdoc;
	}

	/**
	 * Retourne les données de la série si il y en a une
	 *
	 * @return array
	 */
	public function get_serie() {
		global $dbh;

		if (!isset($this->serie)) {
			$this->serie = array();
			if ($this->notice->tparent_id) {
				$query = "SELECT serie_name FROM series WHERE serie_id='".$this->notice->tparent_id."' ";
				$result = pmb_mysql_query($query, $dbh);
				if (pmb_mysql_num_rows($result)) {
					$serie = pmb_mysql_fetch_object($result);
					$this->serie = array(
							'id' => $this->notice->tparent_id,
							'name' => $serie->serie_name
					);
				}
			}
		}
		return $this->serie;
	}

	/**
	 * Charge les données de carthographie
	 */
	private function fetch_map() {
		global $opac_map_activate;

		$this->map=new stdClass();
		$this->map_info=new stdClass();
		if($opac_map_activate){
			$ids[]=$this->notice_id;
			$this->map=new map_objects_controler(TYPE_RECORD,$ids);
			$this->map_info=new map_info($this->notice_id);
		}
	}

	/**
	 * Retourne la carte associée
	 * @return map_objects_controler
	 */
	public function get_map() {
		if (!$this->map) {
			$this->fetch_map();
		}
		return $this->map;
	}

	/**
	 * Retourne les infos de la carte associée
	 * @return map_info
	 */
	public function get_map_info() {
		if (!$this->map_info) {
			$this->fetch_map();
		}
		return $this->map_info;
	}

	/**
	 * Retourne les paramètres persos
	 * @return array
	 */
	public function get_p_perso() {
		if (!$this->p_perso) {
			global $memo_p_perso_notices;
				
			$this->p_perso = array();
				
			if (!$memo_p_perso_notices) {
				$memo_p_perso_notices = new parametres_perso("notices");
			}
			$ppersos = $memo_p_perso_notices->show_fields($this->id);
			// Filtre ceux qui ne sont pas visibles à l'OPAC ou qui n'ont pas de valeur
			foreach ($ppersos['FIELDS'] as $pperso) {
				if ($pperso['OPAC_SHOW'] && $pperso['AFF']) {
					$this->p_perso[] = $pperso;
				}
			}
		}
		return $this->p_perso;
	}

	/**
	 * Gestion des droits d'accès emprunteur/notice
	 */
	private function fetch_visibilite() {
		global $dbh;
		global $hide_explnum;
		global $gestion_acces_active,$gestion_acces_empr_notice, $gestion_acces_empr_docnum;

		if (($gestion_acces_active == 1) && (($gestion_acces_empr_notice == 1) || ($gestion_acces_empr_docnum == 1))) {
			$ac = new acces();
		}
		
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_notice == 1)) {
			$this->dom_2= $ac->setDomain(2);
			if ($hide_explnum) {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id,4);
			} else {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id);
			}
		} else {
			$query = "SELECT opac_libelle, notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$statut_temp = pmb_mysql_fetch_object($result);

				$this->statut_notice =        $statut_temp->opac_libelle;
				$this->visu_notice =          $statut_temp->notice_visible_opac;
				$this->visu_notice_abon =     $statut_temp->notice_visible_opac_abon;
				$this->visu_expl =            $statut_temp->expl_visible_opac;
				$this->visu_expl_abon =       $statut_temp->expl_visible_opac_abon;
				$this->visu_explnum =         $statut_temp->explnum_visible_opac;
				$this->visu_explnum_abon =    $statut_temp->explnum_visible_opac_abon;
					
				if ($hide_explnum) {
					$this->visu_explnum=0;
					$this->visu_explnum_abon=0;
				}
			}
		}
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_docnum == 1)) {
			$this->dom_3 = $ac->setDomain(3);
		}
	}
	
	public function get_dom_2() {
		return $this->dom_2;
	}
	
	public function get_dom_3() {
		return $this->dom_3;
	}
	
	public function get_rights() {
		return $this->rights;
	}

	/**
	 * Retourne un tableau des auteurs
	 * @return array Tableaux des responsabilités = array(
	 'responsabilites' => array(),
	 'auteurs' => array()
	 );
	 */
	public function get_responsabilites() {
		global $fonction_auteur;
		global $dbh;

		if (!count($this->responsabilites)) {
			$this->responsabilites = array(
					'responsabilites' => array(),
					'auteurs' => array()
			);
				
			$query = "SELECT author_id, responsability_fonction, responsability_type, author_type,author_name, author_rejete, author_type, author_date, author_see, author_web ";
			$query.= "FROM responsability, authors ";
			$query.= "WHERE responsability_notice='".$this->id."' AND responsability_author=author_id ";
			$query.= "ORDER BY responsability_type, responsability_ordre " ;
			$result = pmb_mysql_query($query, $dbh);
			while ($notice = pmb_mysql_fetch_object($result)) {
				$this->responsabilites['responsabilites'][] = $notice->responsability_type ;
				$info_bulle="";
				if($notice->author_type==72 || $notice->author_type==71) {
					$congres = authorities_collection::get_authority('author', $notice->author_id);
					$auteur_isbd=$congres->isbd_entry;
					$auteur_titre=$congres->display;
					$info_bulle=" title='".$congres->info_bulle."' ";
				} else {
					if ($notice->author_rejete) $auteur_isbd = $notice->author_rejete." ".$notice->author_name ;
					else  $auteur_isbd = $notice->author_name ;
					// on s'arrête là pour auteur_titre = "Prénom NOM" uniquement
					$auteur_titre = $auteur_isbd ;
					// on complète auteur_isbd pour l'affichage complet
					if ($notice->author_date) $auteur_isbd .= " (".$notice->author_date.")" ;
				}
				$this->responsabilites['auteurs'][] = array(
						'id' => $notice->author_id,
						'fonction' => $notice->responsability_fonction,
						'responsability' => $notice->responsability_type,
						'name' => $notice->author_name,
						'rejete' => $notice->author_rejete,
						'date' => $notice->author_date,
						'type' => $notice->author_type,
						'fonction_aff' => $fonction_auteur[$notice->responsability_fonction],
						'auteur_isbd' => $auteur_isbd,
						'auteur_titre' => $auteur_titre,
						'info_bulle' => $info_bulle,
						'web' => $notice->author_web
				);
			}
		}
		return $this->responsabilites;
	}

	/**
	 * Retourne les auteurs principaux
	 * @return string auteur1 ; auteur2 ...
	 */
	public function get_auteurs_principaux() {
		if (!$this->auteurs_principaux) {
			$this->get_responsabilites();
			// on ne prend que le auteur_titre = "Prénom NOM"
			$as = array_search("0", $this->responsabilites["responsabilites"]);
			if (($as !== FALSE) && ($as !== NULL)) {
				$auteur_0 = $this->responsabilites["auteurs"][$as];
				$this->auteurs_principaux = $auteur_0["auteur_titre"];
			} else {
				$as = array_keys($this->responsabilites["responsabilites"], "1" );
				$aut1_libelle = array();
				for ($i = 0; $i < count($as); $i++) {
					$indice = $as[$i];
					$auteur_1 = $this->responsabilites["auteurs"][$indice];
					if($auteur_1["type"]==72 || $auteur_1["type"]==71) {
						$congres = authorities_collection::get_authority('author', $auteur_1["id"]);
						$aut1_libelle[]=$congres->display;
					} else {
						$aut1_libelle[]= $auteur_1["auteur_titre"];
					}
				}
				$auteurs_liste = implode(" ; ",$aut1_libelle);
				if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste;
			}
		}
		return $this->auteurs_principaux;
	}

	/**
	 * Retourne le libellé du statut de la notice
	 *
	 * @return string
	 */
	public function get_statut_notice() {
		return $this->statut_notice;
	}

	/**
	 * Retourne la visibilité de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_notice() {
		return $this->visu_notice;
	}

	/**
	 * Retourne la visibilité de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_notice_abon() {
		return $this->visu_notice_abon;
	}

	/**
	 * Retourne la visibilité des exemplaires de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_expl() {
		return $this->visu_expl;
	}

	/**
	 * Retourne la visibilité des exemplaires de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_expl_abon() {
		return $this->visu_expl_abon;
	}

	/**
	 * Retourne la visibilité des exemplaires numériques de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_explnum() {
		return $this->visu_explnum;
	}

	/**
	 * Retourne la visibilité des exemplaires numériques de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_explnum_abon() {
		return $this->visu_explnum_abon;
	}

	/**
	 * Retourne les catégories de la notice
	 * @return categorie Tableau des catégories
	 */
	public function get_categories() {
		if (!isset($this->categories)) {
			global $dbh, $opac_categories_affichage_ordre, $opac_categories_show_only_last;

			$this->categories = array();
			
			// Tableau qui va nous servir à trier alphabétiquement les catégories
			if (!$opac_categories_affichage_ordre) $sort_array = array();
			
			$query = "select distinct num_noeud from notices_categories where notcateg_notice = ".$this->id." order by ordre_vedette, ordre_categorie";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					/* @var $object categorie */
					$object = authorities_collection::get_authority('category', $row->num_noeud);
					$format_label = $object->libelle;
					
					// On ajoute les parents si nécessaire
					if (!$opac_categories_show_only_last) {
						$parent_id = $object->parent;
						while ($parent_id && ($parent_id != 1) && (!in_array($parent_id, array($object->thes->num_noeud_racine, $object->thes->num_noeud_nonclasses, $object->thes->num_noeud_orphelins)))) {
							$parent = authorities_collection::get_authority('category', $parent_id);
							$format_label = $parent->libelle.':'.$format_label;
							$parent_id = $parent->parent;
						}
					}
					$categorie = array(
							'object' => $object,
							'format_label' => $format_label
					);
					if (!$opac_categories_affichage_ordre) {
						$sort_array[$object->thes->id_thesaurus][] = strtoupper(convert_diacrit($format_label));
					}
					$this->categories[$object->thes->id_thesaurus][] = $categorie;
				}
				// On tri par ordre alphabétique
				if (!$opac_categories_affichage_ordre) {
					foreach ($this->categories as $thes_id => &$categories) {
						array_multisort($sort_array[$thes_id], $categories);
					}
				}
				// On tri par index de thésaurus
				ksort($this->categories);
			}
		}
		return $this->categories;
	}
	
	/**
	 * Retourne le titre uniforme
	 * @return tu_notice
	 */
	public function get_titre_uniforme() {
		if (!$this->titre_uniforme) {
			$this->titre_uniforme = new tu_notice($this->id);
		}
		return $this->titre_uniforme;
	}
	
	/**
	 * Retourne le tableau des langues de la notices
	 * @return array $this->langues = array('langues' => array(), 'languesorg' => array())
	 */
	public function get_langues() {
		if (!count($this->langues)) {
			global $dbh;
			global $marc_liste_langues;
			if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');
		
			$this->langues = array(
					'langues' => array(),
					'languesorg' => array()
			);
			$query = "select code_langue, type_langue from notices_langues where num_notice=".$this->notice_id." order by ordre_langue ";
			$result = pmb_mysql_query($query, $dbh);
			while (($notice=pmb_mysql_fetch_object($result))) {
				if ($notice->code_langue) {
					$langue[] = array(
						'lang_code' => $notice->code_langue,
						'langue' => $marc_liste_langues->table[$notice->code_langue]
					);
					if (!$notice->type_langue) {
						$this->langues['langues'] = $langue;
					} else {
						$this->langues['languesorg'] = $langue;
					}
				}
			}
		}
		return $this->langues;
	}
	
	/**
	 * Retourne un tableau avec le nombre d'avis et la moyenne
	 * @return array Tableau $this->avis = array('moyenne', 'qte', 'avis' => array('note', 'commentaire', 'sujet'), 'nb_by_note' => array('{note}' => {nb_avis})
	 */
	public function get_avis() {
		global $dbh;
		
		if (!count($this->avis)) {
			$query = "select avg(note) as m, count(id_avis) as qte from avis where valide = 1 and num_notice = ".$this->id." group by num_notice";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				if ($avis = pmb_mysql_fetch_object($result)) {
					$this->avis = array(
							'moyenne' => $avis->m,
							'qte' => $avis->qte
					);
					if ($avis->qte) {
						$this->avis['avis'] = array();
						
						$query = "select note, commentaire, sujet from avis where num_notice='$this->id' and valide=1 order by avis_rank, note desc, id_avis desc";
						$result = pmb_mysql_query($query, $dbh);
						if ($result && pmb_mysql_num_rows($result)) {
							while ($avis = pmb_mysql_fetch_object($result)) {
								$this->avis['avis'][] = array(
										'note' => $avis->note,
										'commentaire' => $avis->commentaire,
										'sujet' => $avis->sujet
								);
								if (!isset($this->avis['nb_by_note'][$avis->note])) {
									$this->avis['nb_by_note'][$avis->note] = 0;
								}
								$this->avis['nb_by_note'][$avis->note]++;
							}
						}
					}	
				}
			}
		}
		return $this->avis;
	}

	/**
	 * Retourne le nombre de bulletins associés
	 * @return int
	 */
	public function get_nb_bulletins(){
		if (!isset($this->nb_bulletins)) {
			global $dbh;
			
			$this->nb_bulletins = 0;
			
			if($this->notice->opac_visible_bulletinage){
				//Droits d'accès
				if (is_null($this->dom_2)) {
					$acces_j='';
					$statut_j=',notice_statut';
					$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
				} else {
					$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
					$statut_j = "";
					$statut_r = "";
				}
				
				//Bulletins sans notice
				$req="SELECT bulletin_id FROM bulletins WHERE bulletin_notice='".$this->id."' and num_notice=0";
				$res = pmb_mysql_query($req,$dbh);
				if($res){
					$this->nb_bulletins+=pmb_mysql_num_rows($res);
				}
				
				//Bulletins avec notice
				$req="SELECT bulletin_id FROM bulletins 
					JOIN notices ON notice_id=num_notice AND num_notice!=0 
					".$acces_j." ".$statut_j." 
					WHERE bulletin_notice='".$this->id."' 
					".$statut_r."";
				$res = pmb_mysql_query($req,$dbh);
				if($res){
					$this->nb_bulletins+=pmb_mysql_num_rows($res);
				}
			}
		}
		return $this->nb_bulletins;
	}

	/**
	 * Retourne le tableau des bulletins associés à la notice
	 * @return array $this->bulletins[] = array('id', 'numero', 'mention_date', 'date_date', 'bulletin_titre', 'num_notice')
	 */
	public function get_bulletins(){
		if (!count($this->bulletins) && $this->get_nb_bulletins()) {
			global $dbh;
			
			if($this->notice->opac_visible_bulletinage){
				//Droits d'accès
				if (is_null($this->dom_2)) {
					$acces_j='';
					$statut_j=',notice_statut';
					$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
				} else {
					$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
					$statut_j = "";
					$statut_r = "";
				}
				
				//Bulletins sans notice
				$req="SELECT * FROM bulletins WHERE bulletin_notice='".$this->id."' and num_notice=0";
				$res = pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while($r=pmb_mysql_fetch_object($res)){
						$this->bulletins[] = array(
								'id' => $r->bulletin_id,
								'numero' => $r->bulletin_numero,
								'mention_date' => $r->mention_date,
								'date_date' => $r->date_date,
								'bulletin_titre' => $r->bulletin_titre,
								'num_notice' => $r->num_notice
						);
					}
				}
				
				//Bulletins avec notice
				$req="SELECT bulletins.* FROM bulletins
				JOIN notices ON notice_id=num_notice AND num_notice!=0
				".$acces_j." ".$statut_j."
				WHERE bulletin_notice='".$this->id."'
				".$statut_r."";
				$res = pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while($r=pmb_mysql_fetch_object($res)){
						$this->bulletins[] = array(
								'id' => $r->bulletin_id,
								'numero' => $r->bulletin_numero,
								'mention_date' => $r->mention_date,
								'date_date' => $r->date_date,
								'bulletin_titre' => $r->bulletin_titre,
								'num_notice' => $r->num_notice
						);
					}
				}
			}
		}
		return $this->bulletins;
	}

	/**
	 * Retourne le nombre de documents numériques associés aux bulletins
	 * @return int
	 */
	public function get_nb_bulletins_docnums() {
		if (!isset($this->nb_bulletins_docnums)) {
			global $dbh;
			
			$this->nb_bulletins_docnums = 0;
	
			//La gestion des droits se fait dans la visionneuse
			$query = "SELECT count(explnum_id) FROM explnum
							JOIN bulletins ON explnum_bulletin=bulletin_id
							WHERE bulletin_notice='".$this->id."' ";
			$result = pmb_mysql_query($query, $dbh);
			if(!pmb_mysql_error() && pmb_mysql_num_rows($result)){
				$this->nb_bulletins_docnums =  pmb_mysql_result($result,0,0);
			}
		}
		return $this->nb_bulletins_docnums;
	}

	/**
	 * Un pério est ouvert à la recherche si il possède au moins un article ou une notice de bulletin
	 * @return int
	 */
	public function is_open_to_search(){
		if (!isset($this->open_to_search)) {
			global $dbh;
			
			$this->open_to_search = 0;
		
			//Droits d'accès
			if (is_null($this->dom_2)) {
				$acces_j='';
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			} else {
				$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
				$statut_j = "";
				$statut_r = "";
			}
			
			//Articles
			$req="SELECT bulletin_id FROM bulletins 
					JOIN analysis ON analysis_bulletin=bulletin_id 
					JOIN notices ON analysis_notice=notice_id 
					".$acces_j." ".$statut_j." 
					WHERE bulletin_notice='".$this->id."' 
					".$statut_r."";
			$res = pmb_mysql_query($req,$dbh);
			if($res){
				$this->open_to_search+=pmb_mysql_num_rows($res);
			}
		
			//Notices de bulletin
			$req="SELECT bulletin_id FROM bulletins 
					JOIN notices ON notice_id=num_notice AND num_notice!=0 
					".$acces_j." ".$statut_j." 
					WHERE bulletin_notice='".$this->id."' 
					".$statut_r."";
			$res = pmb_mysql_query($req,$dbh);
			if($res){
				$this->open_to_search+=pmb_mysql_num_rows($res);
			}
		}
		return $this->open_to_search;
	}
	
	/**
	 * Retourne $this->notice->niveau_biblio
	 */
	public function get_niveau_biblio() {
		return $this->notice->niveau_biblio;
	}
	
	/**
	 * Retourne $this->notice->tit1
	 */
	public function get_tit1() {
		return $this->notice->tit1;
	}
	
	/**
	 * Retourne $this->notice->tit2
	 */
	public function get_tit2() {
		return $this->notice->tit2;
	}
	
	/**
	 * Retourne $this->notice->tit3
	 */
	public function get_tit3() {
		return $this->notice->tit3;
	}
	
	/**
	 * Retourne $this->notice->tit4
	 */
	public function get_tit4() {
		return $this->notice->tit4;
	}
	
	/**
	 * Retourne $this->notice->code
	 */
	public function get_code() {
		return $this->notice->code;
	}
	
	/**
	 * Retourne $this->notice->npages
	 */
	public function get_npages() {
		return $this->notice->npages;
	}
	
	/**
	 * Retourne $this->notice->year
	 */
	public function get_year() {
		return $this->notice->year;
	}
	
	/**
	 * Retourne un tableau des éditeurs
	 * @return publisher Tableau des instances d'éditeurs
	 */
	public function get_publishers() {
		if(!count($this->publishers) && $this->notice->ed1_id){
			$publisher = authorities_collection::get_authority('publisher', $this->notice->ed1_id);
			$this->publishers[]=$publisher;
		
			if ($this->notice->ed2_id) {
				$publisher = authorities_collection::get_authority('publisher', $this->notice->ed2_id);
				$this->publishers[]=$publisher;
			}
		}
		return $this->publishers;
	}
	
	/**
	 * Retourne $this->notice->thumbnail_url
	 */
	public function get_thumbnail_url() {
		return $this->notice->thumbnail_url;
	}
	
	/**
	 * Retourne l'état de collection
	 * @return collstate
	 */
	public function get_collstate() {
		if (!$this->collstate) {
			$collstate=new collstate(0,$this->notice_id);
		}
		return $this->collstate;
	}

	/**
	 * Retourne l'autorisation des avis
	 * @return int
	 */
	public function get_avis_allowed() {
		return $this->avis_allowed;
	}

	/**
	 * Retourne l'autorisation des tags
	 * @return int
	 */
	public function get_tag_allowed() {
		return $this->tag_allowed;
	}

	/**
	 * Retourne l'autorisation des suggestions
	 * @return int
	 */
	public function get_sugg_allowed() {
		return $this->sugg_allowed;
	}
	
	public function get_enrichment_sources() {
		if (!isset($this->enrichment_sources)) {
			global $opac_notice_enrichment;
			
			$this->enrichment_sources = array();
			
			if($opac_notice_enrichment){
				$enrichment = new enrichment();
				if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
					$this->enrichment_sources = $enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc];
				}else if ($enrichment->active[$this->notice->niveau_biblio]){
					$this->enrichment_sources = $enrichment->active[$this->notice->niveau_biblio];
				}
			}
		}
		return $this->enrichment_sources;
	}
	
	/**
	 * Retourne l'icone du type de document
	 * @return string
	 */
	public function get_icon_doc() {
		if (!isset($this->icon_doc)) {
			global $icon_doc;
			$this->icon_doc = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		}
		return $this->icon_doc;
	}
	
	/**
	 * Retourne le libellé du niveau biblio
	 * @return string
	 */
	public function get_biblio_doc() {
		if (!$this->biblio_doc) {
			global $biblio_doc;
			$this->biblio_doc = $biblio_doc[$this->notice->niveau_biblio];
		}
		return $this->biblio_doc;
	}
	
	/**
	 * Retourne le libellé du type de document
	 * @return string
	 */
	public function get_tdoc() {
		if (!$this->tdoc) {
			global $tdoc;
			$this->tdoc = $tdoc->table[$this->get_typdoc()];
		}
		return $this->tdoc;
	}
	
	/**
	 * Retourne la liste des concepts qui indexent la notice
	 * @return skos_concepts_list
	 */
	public function get_concepts_list() {
		if (!$this->concepts_list) {
			$this->concepts_list = new skos_concepts_list();
			$concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->id);
		}
		return $this->concepts_list;
	}
	
	/**
	 * Retourne le tableau des mots clés
	 * @return array
	 */
	public function get_mots_cles() {
		if (!isset($this->mots_cles)) {
			global $pmb_keyword_sep;
			if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";
			
			if (!trim($this->notice->index_l)) return "";
			
			$tableau_mots = explode($pmb_keyword_sep,trim($this->notice->index_l)) ;
		}
		return $this->mots_cles;
	}
	
	/**
	 * Retourne l'indexation décimale
	 * @return indexint
	 */
	public function get_indexint() {
		if(!$this->indexint && $this->notice->indexint) {
			$this->indexint = authorities_collection::get_authority('indexint', $this->notice->indexint);
		}
		return $this->indexint;
	}
	
	/**
	 * Retourne le résumé
	 * @return string
	 */
	public function get_resume() {
		return $this->notice->n_resume;
	}
	
	/**
	 * Retourne le contenu
	 * @return string
	 */
	public function get_contenu() {
		return $this->notice->n_contenu;
	}
	
	/**
	 * Retourne $this->notice->lien
	 * @return string
	 */
	public function get_lien() {
		return $this->notice->lien;
	}
	
	/**
	 * Retourne $this->notice->eformat
	 * @return string
	 */
	public function get_eformat() {
		return $this->notice->eformat;
	}
	
	/**
	 * Retourne $this->notice->tnvol
	 * @return string
	 */
	public function get_tnvol() {
		return $this->notice->tnvol;
	}
	
	/**
	 * Retourne $this->notice->mention_edition
	 * @return string
	 */
	public function get_mention_edition() {
		return $this->notice->mention_edition;
	}
	
	/**
	 * Retourne $this->notice->nocoll
	 * @return string
	 */
	public function get_nocoll() {
		return $this->notice->nocoll;
	}
	
	/**
	 * Retourne la collection
	 * @return collection
	 */
	public function get_collection() {
		if (!$this->collection && $this->notice->coll_id) {
			$this->collection = authorities_collection::get_authority('collection', $this->notice->coll_id);
		}
		return $this->collection;
	}
	
	/**
	 * Retourne la sous-collection
	 * @return subcollection
	 */
	public function get_subcollection() {
		if (!$this->subcollection && $this->notice->subcoll_id) {
			$this->subcollection = authorities_collection::get_authority('subcollection', $this->notice->subcoll_id);
		}
		return $this->subcollection;
	}
	
	/**
	 * Retourne $this->notice->ill
	 * @return string
	 */
	public function get_ill() {
		return $this->notice->ill;
	}
	
	/**
	 * Retourne $this->notice->size
	 * @return string
	 */
	public function get_size() {
		return $this->notice->size;
	}
	
	/**
	 * Retourne $this->notice->accomp
	 * @return string
	 */
	public function get_accomp() {
		return $this->notice->accomp;
	}
	
	/**
	 * Retourne $this->notice->prix
	 * @return string
	 */
	public function get_prix() {
		return $this->notice->prix;
	}
	
	/**
	 * Retourne $this->notice->n_gen
	 * @return string
	 */
	public function get_n_gen() {
		return $this->notice->n_gen;
	}
	
	/**
	 * Retourne le permalink
	 * @return string
	 */
	public function get_permalink() {
		if (!$this->permalink) {
			global $opac_url_base;
			
			if($this->notice->niveau_biblio != "b"){
				$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->id;
			}else{
				$bull = $this->get_bul_info();
				$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$bull['bulletin_id'];
			}
		}
		return $this->permalink;
	}
	
	/**
	 * Retourne les données d'exemplaires
	 * @return array
	 */
	public function get_expls_datas() {
		if (!isset($this->expls_datas)) {
			global $opac_sur_location_activate;
			global $opac_view_filter_class;
			global $opac_expl_order;
			global $opac_expl_data;
			global $opac_show_exemplaires_analysis;
			global $dbh;
			
			$type = $this->get_niveau_biblio();
			$id = $this->get_id();
			$bull = $this->get_bul_info();
			$bull_id = $bull['bulletin_id']*1;
			
			$this->expls_datas = array();
			
			if($opac_sur_location_activate){
				$opac_sur_location_select=", sur_location.*";
				$opac_sur_location_from=", sur_location";
				$opac_sur_location_where=" AND docs_location.surloc_num=sur_location.surloc_id";
			}
			if($opac_view_filter_class){
				if(sizeof($opac_view_filter_class->params["nav_sections"])){
					$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
				}else{
					return "";
				}
			}
			// les exemplaires des monographies
			if ($type=="m") {
				$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* $opac_sur_location_select";
				$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders $opac_sur_location_from";
				$requete .= " WHERE expl_notice='$id' and expl_bulletin='$bull_id'";
				$requete .= " AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1";
				$requete .= $opac_sur_location_where;
				$requete .= $opac_view_filter_where;
				$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
				$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
				$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
				$requete .= " AND exemplaires.expl_typdoc=docs_type. idtyp_doc ";
				$requete .= " AND exemplaires.expl_codestat=docs_codestat.idcode ";
				$requete .= " AND exemplaires.expl_owner=lenders.idlender ";
				if ($opac_expl_order) $requete .= " ORDER BY $opac_expl_order ";
				$requete_resa = "SELECT count(1) from resa where resa_idnotice='$id' ";
			} // fin si "m"
			
			// les exemplaires des bulletins
			if ($type=="b") {
				$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* $opac_sur_location_select";
				$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders $opac_sur_location_from";
				$requete .= " WHERE expl_notice='0' and expl_bulletin='$bull_id'";
				$requete .= " AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1";
				$requete .= $opac_sur_location_where;
				$requete .= $opac_view_filter_where;
				$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
				$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
				$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
				$requete .= " AND exemplaires.expl_typdoc=docs_type. idtyp_doc ";
				$requete .= " AND exemplaires.expl_codestat=docs_codestat.idcode ";
				$requete .= " AND exemplaires.expl_owner=lenders.idlender ";
				if ($opac_expl_order) $requete .= " ORDER BY $opac_expl_order ";
				$requete_resa = "SELECT count(1) from resa where resa_idbulletin='$bull_id' ";
			} // fin si "b"
			
			// les exemplaires des bulletins des articles affichés
			// ERICROBERT : A faire ici !
			if ($type=="a" && $opac_show_exemplaires_analysis) {
				$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* $opac_sur_location_select";
				$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders $opac_sur_location_from";
				$requete .= " WHERE expl_notice='0' and expl_bulletin='$bull_id'";
				$requete .= " AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1";
				$requete .= $opac_sur_location_where;
				$requete .= $opac_view_filter_where;
				$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
				$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
				$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
				$requete .= " AND exemplaires.expl_typdoc=docs_type. idtyp_doc ";
				$requete .= " AND exemplaires.expl_codestat=docs_codestat.idcode ";
				$requete .= " AND exemplaires.expl_owner=lenders.idlender ";
				if ($opac_expl_order) $requete .= " ORDER BY $opac_expl_order ";
				$requete_resa = "SELECT count(1) from resa where resa_idbulletin='$bull_id' ";
			} // fin si "a"
	
			$result = pmb_mysql_query($requete, $dbh);
			
			$surloc_field="";
			if ($opac_sur_location_activate==1) $surloc_field="surloc_libelle,";
			if (!$opac_expl_data) $opac_expl_data="tdoc_libelle,".$surloc_field."location_libelle,section_libelle,expl_cote";
			$colonnesarray=explode(",",$opac_expl_data);
			
			$this->expls_datas['colonnesarray'] = $colonnesarray;
	
			if ($result && pmb_mysql_num_rows($result)) {
				
				while ($expl = pmb_mysql_fetch_object($result)) {
	
					$requete_resa = "SELECT count(1) from resa where resa_cb='".$expl->expl_cb."' ";
					$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
					$requete_resa = "SELECT count(1) from resa_ranger where resa_cb='".$expl->expl_cb."' ";
					$flag_resa = $flag_resa + pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
					
					$expl_datas = array(
							'num_infopage' => $expl->num_infopage,
							'surloc_id' => $expl->surloc_id,
							'expl_location' => $expl->expl_location,
							'expl_cb' => $expl->expl_cb,
							'statut_libelle_opac' => $expl->statut_libelle_opac,
							'pret_flag' => $expl->pret_flag,
							'pret_retour' => $expl->pret_retour,
							'pret_idempr' => $expl->pret_idempr,
							'expl_statut' => $expl->expl_statut,
							'expl_id' => $expl->expl_id,
							'expl_location' => $expl->expl_location,
							'flag_resa' => $flag_resa
					);
					
					foreach ($colonnesarray as $colonne) {
						$expl_datas[$colonne] = $expl->{$colonne};
					}
					
					if($expl->pret_retour) { // exemplaire sorti
						$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr FROM empr WHERE id_empr='".$expl->pret_idempr."' ";
						$res_empr = pmb_mysql_query($rqt_empr, $dbh);
						$res_empr_obj = pmb_mysql_fetch_object($res_empr);
						
						$expl_datas['empr_nom'] = $res_empr_obj->empr_nom;
						$expl_datas['empr_prenom'] = $res_empr_obj->empr_prenom;
					}
					
					$this->expls_datas['expls'][] = $expl_datas;
				}
			}
		}
		return $this->expls_datas;
	}
	
	/**
	 * Retourne la disponibilité
	 * @return array $this->availibility = array('availibility', 'next-return')
	 */
	public function get_availability() {
		if (!$this->availability) {
			$expls_datas = $this->get_expls_datas();
			$next_return = "";
			$availability = "unavailable";
			
			if (count($expls_datas['expls'])) {
				foreach ($expls_datas['expls'] as $expl) {
					if ($expl['pret_flag']) { // Pretable
						if ($expl['flag_resa'] && !$next_return) { // Réservé
							$availability = "reserved";
						} else if ($expl['pret_retour']) { // Sorti
							if (!$next_return || ($next_return > $expl['pret_retour'])) {
								$next_return = $expl['pret_retour'];
								$availability = "out";
							}
						} else {
							$availability = "available";
							break;
						}
					}
				}
			} else {
				// Pas d'exemplaires
				$availability = "none";
			}
			$this->availability = array(
					'availability' => $availability,
					'next_return' => formatdate($next_return)
			);
		}
		return $this->availability;
	}
	
	/**
	 * Retourne le tableau des ids des notices du même auteur
	 * @return array
	 */
	public function get_records_from_same_author() {
		if (!isset($this->records_from_same_author)) {
			global $dbh;
			
			$this->records_from_same_author = array();
			
			$this->get_responsabilites();
			$as = array_search("0", $this->responsabilites["responsabilites"]);
			if (($as !== FALSE) && ($as !== NULL)) {
				$authors_ids = $this->responsabilites["auteurs"][$as]['id'];
			} else {
				$as = array_keys($this->responsabilites["responsabilites"], "1");
				$authors_ids = "";
				for ($i = 0; $i < count($as); $i++) {
					$indice = $as[$i];
					if ($authors_ids) $authors_ids .= ",";
					$authors_ids .= $this->responsabilites["auteurs"][$indice]['id'];
				}
			}
			
			if ($authors_ids) {
				$query = "select distinct responsability_notice from responsability where responsability_author in (".$authors_ids.") and responsability_notice != ".$this->id." order by responsability_type, responsability_ordre";
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_author[] = $record->responsability_notice;
					}
				}
			}
		}
		return $this->records_from_same_author;
	}
	
	/**
	 * Retourne le tableau des ids des notices du même éditeur
	 * @return array
	 */
	public function get_records_from_same_publisher() {
		if (!isset($this->records_from_same_publisher)) {
			global $dbh;
			
			$this->records_from_same_publisher = array();
			
			if ($this->notice->ed1_id) {
				$query = "select distinct notice_id from notices where ed1_id = ".$this->notice->ed1_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_publisher[] = $record->notice_id;
					}
				}
			}
		}
		return $this->records_from_same_publisher;
	}
	
	/**
	 * Retourne le tableau des ids des notices de la même collection
	 * @return array
	 */
	public function get_records_from_same_collection() {
		if (!isset($this->records_from_same_collection)) {
			global $dbh;
			
			$this->records_from_same_collection = array();
			
			if ($this->notice->coll_id) {
				$query = "select distinct notice_id from notices where coll_id = ".$this->notice->coll_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_collection[] = $record->notice_id;
					}
				}
			}
		}
		return $this->records_from_same_collection;
	}

	/**
	 * Retourne le tableau des ids des notices de la même série
	 * @return array
	 */
	public function get_records_from_same_serie() {
		if (!isset($this->records_from_same_serie)) {
			global $dbh;
			
			$this->records_from_same_serie = array();
			
			if ($this->notice->tparent_id) {
				$query = "select distinct notice_id from notices where tparent_id = ".$this->notice->tparent_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_serie[] = $record->notice_id;
					}
				}
			}
		}
		return $this->records_from_same_serie;
	}
	
	/**
	 * Retourne le tableau des ids des notices avec la même indexation décimale
	 * @return array
	 */
	public function get_records_from_same_indexint() {
		if (!isset($this->records_from_same_indexint)) {
			global $dbh;
			
			$this->records_from_same_indexint = array();
			
			if ($this->notice->indexint) {
				$query = "select distinct notice_id from notices where indexint = ".$this->notice->indexint." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_indexint[] = $record->notice_id;
					}
				}
			}
		}
		return $this->records_from_same_indexint;
	}
	
	/**
	 * Retourne le tableau des ids de notices avec des catégories communes
	 * @return array
	 */
	public function get_records_from_same_categories() {
		if (!$this->records_from_same_categories) {
			global $dbh;
			
			$this->records_from_same_categories = array();
			
			$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories where num_noeud in (select num_noeud from notices_categories where notcateg_notice = ".$this->id.") group by notcateg_notice order by pert desc";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($record = pmb_mysql_fetch_object($result)) {
					$this->records_from_same_categories[] = $record->notcateg_notice;
				}
			}
		}
		return $this->records_from_same_categories;
	}
	
	/**
	 * Retourne l'URL calculée de l'image
	 * @return string
	 */
	public function get_picture_url() {

		if (!$this->picture_url && ($this->get_code() || $this->get_thumbnail_url())) {
			global $opac_show_book_pics, $opac_book_pics_url;
			global $opac_url_base;
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $this->get_thumbnail_url())) {
				if ($this->get_thumbnail_url()) {
					$this->picture_url = $this->get_thumbnail_url();
				} else {
					$code_chiffre = pmb_preg_replace('/-|\.| /', '', $this->get_code());
					$url_image = $opac_book_pics_url ;
					$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($this->get_thumbnail_url()) ;
					$this->picture_url = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
				}
			}
		}
		if (!$this->picture_url) $this->picture_url = get_url_icon("no_image.jpg");
		return $this->picture_url;
	}
	
	/**
	 * Retourne les informations de réservation
	 * @return array $this->resas_datas = array('nb_resas', 'href', 'onclick', 'flag_max_resa', 'flag_resa_visible')
	 */
	public function get_resas_datas() {
		if (!isset($this->resas_datas)) {
			global $dbh, $msg;
			global $opac_resa ;
			global $opac_max_resa ;
			global $opac_show_exemplaires ;
			global $popup_resa ;
			global $opac_resa_popup ; // la résa se fait-elle par popup ?
			global $opac_resa_planning; // la résa est elle planifiée
			global $allow_book;
			global $opac_show_exemplaires_analysis;
			
			$this->resas_datas = array(
					'nb_resas' => 0,
					'href' => "#",
					'onclick' => "",
					'flag_max_resa' => false,
					'flag_resa_visible' => true,
					'flag_resa_possible' => true
			);
			$bul_info = $this->get_bul_info();
			$bulletin_id = $bul_info['bulletin_id'];
			if ($bulletin_id) $requete_resa = "SELECT count(1) FROM resa WHERE resa_idbulletin='$bulletin_id' ";
			else $requete_resa = "SELECT count(1) FROM resa WHERE resa_idnotice='$this->id' ";
			$this->resas_datas['nb_resas'] = pmb_mysql_result(pmb_mysql_query($requete_resa,$dbh), 0, 0) ;
			
			if ((is_null($this->dom_2) && $opac_show_exemplaires && $this->is_visu_expl() && (!$this->is_visu_expl_abon() || ($this->is_visu_expl_abon() && $_SESSION["user_code"]))) || ($this->get_rights() & 8)) {
				if (!$opac_resa_planning) {
					if($bulletin_id) $resa_check=check_statut(0,$bulletin_id) ;
					else $resa_check=check_statut($this->id, 0) ;
					// vérification si exemplaire réservable
					if ($resa_check) {
						if (($this->get_niveau_biblio()=="m" || $this->get_niveau_biblio()=="b" || ($this->get_niveau_biblio()=="a" && $opac_show_exemplaires_analysis)) && ($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
							if ($opac_max_resa==0 || $opac_max_resa>$this->resas_datas['nb_resas']) {
								if ($opac_resa_popup) {
									$this->resas_datas['onclick'] = "if(confirm('".$msg["confirm_resa"]."')){w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;}else return false;";
								} else {
									$this->resas_datas['href'] = "./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup";
									$this->resas_datas['onclick'] = "return confirm('".$msg["confirm_resa"]."')";
								}
							} else $this->resas_datas['flag_max_resa'] = true;
						} elseif (($this->get_niveau_biblio()=="m" || $this->get_niveau_biblio()=="b" || ($this->get_niveau_biblio()=="a" && $opac_show_exemplaires_analysis)) && !($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
							// utilisateur pas connecté
							// préparation lien réservation sans être connecté
							if ($opac_resa_popup) {
								$this->resas_datas['onclick'] = "if(confirm('".$msg["confirm_resa"]."')){w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;}else return false;";
							} else {
								$this->resas_datas['href'] = "./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup";
								$this->resas_datas['onclick'] = "return confirm('".$msg["confirm_resa"]."')";
							}
						}
					} else {
						$this->resas_datas['flag_resa_possible'] = false;
					} // fin if resa_check
				} else {
					// planning de réservations
					$this->resas_datas['nb_resas'] = resa_planning::count_resa($this->id);
					if (($this->get_niveau_biblio()=="m") && ($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
						if ($opac_max_resa==0 || $opac_max_resa>$this->resas_datas['nb_resas']) {
							if ($opac_resa_popup) {
								$this->resas_datas['onclick'] = "w=window.open('./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;";
							} else {
								$this->resas_datas['href'] = "./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup";
							}
						} else $this->resas_datas['flag_max_resa'] = true;
					} elseif (($this->get_niveau_biblio()=="m") && !($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
						// utilisateur pas connecté
						// préparation lien réservation sans être connecté
						if ($opac_resa_popup) {
							$this->resas_datas['onclick'] = "w=window.open('./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;";
						} else {
							$this->resas_datas['href'] = "./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup";
						}
					}
				}
			} else {
				$this->resas_datas['flag_resa_visible'] = false;
			}
		}
		return $this->resas_datas;
	}
	
	/**
	 * Retourne vrai si nouveauté, false sinon
	 * @return boolean
	 */
	public function is_new() {
		if ($this->notice->notice_is_new) {
			return true;
		}
		return false;
	}

	/**
	 * Retourne le tableau des relations parentes
	 * @return array
	 */
	public function get_relations_up() {
		if (!isset($this->relations_up)) {
			global $relation_listup, $dbh;
			if (!$relation_listup) $relation_listup = new marc_list("relationtypeup");
			
			$this->relations_up = array();
			
			$query = "select linked_notice, relation_type from notices_relations where num_notice = ".$this->id." order by relation_type asc, rank asc";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($parent = pmb_mysql_fetch_object($result)) {
					if (!isset($this->relations_up[$parent->relation_type]['label'])) $this->relations_up[$parent->relation_type]['label'] = $relation_listup->table[$parent->relation_type];
					$this->relations_up[$parent->relation_type]['parents'][] = record_display::get_record_datas($parent->linked_notice);
				}
			}
		}
		return $this->relations_up;
	}
	
	/**
	 * Retourne le tableau des relations enfants
	 * @return array
	 */
	public function get_relations_down() {
		if (!isset($this->relations_down)) {
			global $relation_typedown, $dbh;
			if (!$relation_typedown) $relation_typedown = new marc_list("relationtypedown");
			
			$this->relations_down = array();
			
			$query = "select num_notice, relation_type from notices_relations where linked_notice = ".$this->id." order by relation_type asc, rank asc";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($parent = pmb_mysql_fetch_object($result)) {
					if (!isset($this->relations_down[$parent->relation_type]['label'])) $this->relations_down[$parent->relation_type]['label'] = $relation_typedown->table[$parent->relation_type];
					$this->relations_down[$parent->relation_type]['children'][] = record_display::get_record_datas($parent->num_notice);
				}
			}
		}
		return $this->relations_down;
	}
	
	/**
	 * Retourne les dépouillements
	 * @return string Tableau des affichage des articles
	 */
	public function get_articles() {
		if (!isset($this->articles)) {
			global $dbh;
			
			$this->articles = array();
			
			$bul_info = $this->get_bul_info();
			$bulletin_id = $bul_info['bulletin_id'];
			
			$query = "SELECT analysis_notice FROM analysis, notices, notice_statut WHERE analysis_bulletin=".$bulletin_id." AND notice_id = analysis_notice AND statut = id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").") order by analysis_notice";
			$result = @pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				while(($article = pmb_mysql_fetch_object($result))) {
					$this->articles[] = record_display::get_display_in_result($article->analysis_notice);
				}
			}
		}
		return $this->articles;
	}
	
	/**
	 * Retourne les données de demandes
	 * @return string Tableau des données ['themes' => ['id', 'label'], 'types' => ['id', 'label']]
	 */
	public function get_demands_datas() {
		if (!isset($this->demands_datas)) {
			global $dbh;
			
			$this->demands_datas = array(
					'themes' => array(),
					'types' => array()
			);
			
			// On va chercher les thèmes
			$query = "select id_theme, libelle_theme from demandes_theme";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($theme = pmb_mysql_fetch_object($result)) {
					$this->demands_datas['themes'][] = array(
							'id' => $theme->id_theme,
							'label' => $theme->libelle_theme
					);
				}
			}
			
			// On va chercher les types
			$query = "select id_type, libelle_type from demandes_type";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($theme = pmb_mysql_fetch_object($result)) {
					$this->demands_datas['types'][] = array(
							'id' => $theme->id_type,
							'label' => $theme->libelle_type
					);
				}
			}
		}
		return $this->demands_datas;
	}
	
	/**
	 * Retourne l'autorisation d'afficher le panier en fonction des paramètres opac et de la connexion de l'utilisateur
	 * @return boolean true si le panier est autoriser, false sinon
	 */
	public function is_cart_allow() {
		if (!isset($this->cart_allow)) {
			global $opac_cart_allow, $opac_cart_only_for_subscriber;
			
			$this->cart_allow = ($opac_cart_allow && (!$opac_cart_only_for_subscriber || ($opac_cart_only_for_subscriber && $_SESSION["user_code"])));
		}
		return $this->cart_allow;
	}
	
	/**
	 * Retourne les infos de documents numériques associés à la notice
	 * @return array
	 */
	public function get_explnums_datas() {
		if (!isset($this->explnums_datas)) {
			global $dbh;
			global $charset;
			global $opac_url_base;
			global $opac_visionneuse_allow;
			global $opac_photo_filtre_mimetype;
			global $opac_explnum_order;
			global $opac_show_links_invisible_docnums;
			global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
			
			$this->explnums_datas = array(
					'nb_explnums' => 0,
					'explnums' => array(),
					'visionneuse_script' => '
								<script type="text/javascript">
									if(typeof(sendToVisionneuse) == "undefined"){
										var sendToVisionneuse = function (explnum_id){
											document.getElementById("visionneuseIframe").src = "visionneuse.php?"+(typeof(explnum_id) != "undefined" ? "explnum_id="+explnum_id : "");
										}
									}
								</script>'
			);
		
			global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
			if (!count($_mimetypes_bymimetype_)) {
				create_tableau_mimetype();
			}
			
			$this->get_bul_info();
		
			// récupération du nombre d'exemplaires
			$query = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut FROM explnum WHERE ";
			if ($this->get_niveau_biblio() != 'b') $query .= "explnum_notice='".$this->id."' ";
			else $query .= "explnum_bulletin='".$this->parent['bulletin_id']."' or explnum_notice='".$this->id."' ";
			$query .= "union SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum, bulletins
			WHERE bulletin_id = explnum_bulletin
			AND bulletins.num_notice='".$this->id."'";
			if ($opac_explnum_order) $query .= " order by ".$opac_explnum_order;
			else $query .= " order by explnum_mimetype, explnum_nom, explnum_id ";
			$res = pmb_mysql_query($query, $dbh);
			$nb_explnums = pmb_mysql_num_rows($res);
		
			$docnum_visible = true;
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$docnum_visible = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id,16);
			} else {
				$query = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$this->id."' and id_notice_statut=statut ";
				$result = pmb_mysql_query($query, $dbh);
				if($result && pmb_mysql_num_rows($result)) {
					$statut_temp = pmb_mysql_fetch_object($result);
					if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
					if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
				} else 	$docnum_visible=false;
			}
		
			if ($nb_explnums && ($docnum_visible || $opac_show_links_invisible_docnums)) {
				// on récupère les données des exemplaires
				global $search_terms;
				while (($expl = pmb_mysql_fetch_object($res))) {
					// couleur de l'img en fonction du statut
					if ($expl->explnum_docnum_statut) {
						$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
						$Query_statut = pmb_mysql_query($rqt_st, $dbh)or die ($rqt_st. " ".pmb_mysql_error()) ;
						$r_statut = pmb_mysql_fetch_object($Query_statut);
						$explnum_class = 'docnum_'.$r_statut->class_html;
						if ($expl->explnum_docnum_statut>1) {
							$explnum_opac_label = $r_statut->opac_libelle;
						} else $explnum_opac_label = '';
					} else {
						$explnum_class = 'docnum_statutnot1';
						$explnum_opac_label = '';
					}
		
					$explnum_docnum_visible = true;
					$explnum_docnum_consult = true;
					if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
						$explnum_docnum_visible = $this->dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,16);
						$explnum_docnum_consult = $this->dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,4);
					} else {
						$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_consult_opac, explnum_consult_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$expl->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
						$myQuery = pmb_mysql_query($requete, $dbh);
						if(pmb_mysql_num_rows($myQuery)) {
							$statut_temp = pmb_mysql_fetch_object($myQuery);
							if(!$statut_temp->explnum_visible_opac)	{
								$explnum_docnum_visible=false;
							}
							if(!$statut_temp->explnum_consult_opac)	{
								$explnum_docnum_consult=false;
							}
							if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_visible=false;
							if($statut_temp->explnum_consult_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_consult=false;
						} else {
							$explnum_docnum_visible=false;
						}
					}
					if ($explnum_docnum_visible ||  $opac_show_links_invisible_docnums) {
						$this->explnums_datas['nb_explnums']++;
						$explnum_datas = array(
								'id' => $expl->explnum_id,
								'name' => $expl->explnum_nom,
								'mimetype' => $expl->explnum_mimetype,
								'url' => $expl->explnum_url,
								'filename' => $expl->explnum_nomfichier,
								'extension' => $expl->explnum_extfichier,
								'statut' => $expl->explnum_docnum_statut,
								'consultation' => $explnum_docnum_consult
						);
						
						if ($expl->explnum_vignette) {
							$explnum_datas['thumbnail_url'] = $opac_url_base.'vig_num.php?explnum_id='.$expl->explnum_id;
						} else {
							// trouver l'icone correspondant au mime_type
							$explnum_datas['thumbnail_url'] = get_url_icon('mimetype/'.icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier), 1);
						}
						$words_to_find="";
						if (($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
							if (is_array($search_terms)) {
								$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
							}
						}
						$explnum_datas['access_datas'] = array(
								'script' => '',
								'href' => '#',
								'onclick' => ''
						);
						//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
						if(!$explnum_docnum_visible && $opac_show_links_invisible_docnums && !$_SESSION['id_empr_session']){
							if ($opac_visionneuse_allow) {
								$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
							}
							if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
								$explnum_datas['access_datas']['script'] = "
								<script type='text/javascript'>
									function sendToVisionneuse_".$expl->explnum_id."(){
										open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");
									}
								</script>";
								$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$expl->explnum_id."');";
							}else{
								$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$expl->explnum_id)."')";
							}
						}else{
							if ($opac_visionneuse_allow)
								$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
							if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
								$explnum_datas['access_datas']['onclick'] = "open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;";
							} else {
								$explnum_datas['access_datas']['href'] = $opac_url_base.'doc_num.php?explnum_id='.$expl->explnum_id;
							}
						}
		
						if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
						elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
						else $explnum_datas['mimetype_label'] = $expl->explnum_mimetype ;
		
						$this->explnums_datas['explnums'][] = $explnum_datas;
					}
				}
			}
		}
		return $this->explnums_datas;
	}
}