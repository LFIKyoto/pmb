<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_display.class.php,v 1.9.2.8 2015-11-13 09:11:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/record_datas.class.php");
require_once($class_path."/parametres_perso.class.php");
include_once($include_path."/templates/demandes.tpl.php");
require_once($class_path."/demandes.class.php");

/**
 * Classe d'affichage d'une notice
 * @author apetithomme
 *
 */
class record_display {

	/**
	 * Tableau d'instances de record_datas
	 * @var record_datas
	 */
	static private $records_datas = array();

	/**
	 * Retourne une instance de record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function get_record_datas($notice_id) {
		if (!isset(self::$records_datas[$notice_id])) {
			self::$records_datas[$notice_id] = new record_datas($notice_id);
		}
		return self::$records_datas[$notice_id];
	}

	static public function lookup($name, $object) {
		$return = null;
		// Si on le nom commence par record. on va chercher les méthodes
		if (substr($name, 0, 8) == ":record.") {
			$attributes = explode('.', $name);
			$notice_id = $object->getVariable('notice_id');

			// On va chercher dans record_display
			$return = static::look_for_attribute_in_class("record_display", $attributes[1], array($notice_id));

			if (!$return) {
				// On va chercher dans record_datas
				$record_datas = static::get_record_datas($notice_id);
				$return = static::look_for_attribute_in_class($record_datas, $attributes[1]);
			}

			// On regarde les attributs enfants recherchés
			if ($return && count($attributes) > 2) {
				for ($i = 2; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = $return[$attributes[$i]];
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		} else {
			$attributes = explode('.', $name);
			// On regarde si on a directement une instance d'objet, dans le cas des boucles for
			if (is_object($obj = $object->getVariable(substr($attributes[0], 1))) && (count($attributes) > 1)) {
				$return = $obj;
				for ($i = 1; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = $return[$attributes[$i]];
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		}
		return $return;
	}

	static protected function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
		if (is_object($class) && isset($class->{$attribute})) {
			return $class->{$attribute};
		} else if (method_exists($class, $attribute)) {
			return call_user_func_array(array($class, $attribute), $parameters);
		} else if (method_exists($class, "get_".$attribute)) {
			return call_user_func_array(array($class, "get_".$attribute), $parameters);
		} else if (method_exists($class, "is_".$attribute)) {
			return call_user_func_array(array($class, "is_".$attribute), $parameters);
		}
		return null;
	}

	static private function render($notice_id, $tpl) {
		$h2o = new H2o($tpl);
		$h2o->addLookup("record_display::lookup");
		$h2o->set(array('notice_id' => $notice_id));

		return $h2o->render();
	}

	/**
	 * Génère le span nécessaire à Zotéro
	 * @param int $id_notice Identifiant de la notice
	 * @return string
	 */
	static public function get_display_coins_span($notice_id){
		// Attention!! Fait pour Zotero qui ne traite pas toute la norme ocoins
		global $charset,$opac_url_base;

		$record_datas = static::get_record_datas($notice_id);

		if($charset!="utf-8") $f="utf8_encode";
		// http://generator.ocoins.info/?sitePage=info/book.html&
		// http://ocoins.info/cobg.html
		$coins_span="<span class='Z3988' title='ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3A";

		switch ($record_datas->get_niveau_biblio()){
			case 's':// periodique
				/*
				$coins_span.="book";
				$coins_span.="&amp;rft.genre=book";
				$coins_span.="&amp;rft.btitle=".rawurlencode($f($this->notice->tit1));
				$coins_span.="&amp;rft.title=".rawurlencode($f($this->notice->tit1));
				if($this->notice->code)	$coins_span.="&amp;rft.issn=".rawurlencode($f($this->notice->code));
				if($this->notice->npages) $coins_span.="&amp;rft.epage=".rawurlencode($f($this->notice->npages));
				if($this->notice->year) $coins_span.="&amp;rft.date=".rawurlencode($f($this->notice->year));
				*/
			break;
			case 'a': // article
				$parent = $record_datas->get_bul_info();
				$coins_span.="journal";
				$coins_span.="&amp;rft.genre=article";
				$coins_span.="&amp;rft.atitle=".rawurlencode($f?$f($record_datas->get_tit1()):$record_datas->get_tit1());
				$coins_span.="&amp;rft.jtitle=".rawurlencode($f?$f($parent['title']):$parent['title']);
				if ($parent['numero']) $coins_span.="&amp;rft.volume=".rawurlencode($f?$f($parent['numero']):$parent['numero']);
				
				if($parent['date']){
					$coins_span.="&amp;rft.date=".rawurlencode($f?$f($parent['date']):$parent['date']);
				}elseif($parent['date_date']){
					$coins_span.="&amp;rft.date=".rawurlencode($f?$f($parent['date_date']):$parent['date_date']);
				}
				if ($record_datas->get_code())	$coins_span.="&amp;rft.issn=".rawurlencode($f?$f($record_datas->get_code()):$record_datas->get_code());
				if ($record_datas->get_npages()) $coins_span.="&amp;rft.epage=".rawurlencode($f?$f($record_datas->get_npages()):$record_datas->get_npages());
			break;
			case 'b': //Bulletin
				/*
				$coins_span.="book";
				$coins_span.="&amp;rft.genre=issue"; // issue
				$coins_span.="&amp;rft.btitle=".rawurlencode($f($this->notice->tit1." / ".$this->parent_title));
				if($this->notice->code)	$coins_span.="&amp;rft.isbn=".rawurlencode($f($this->notice->code));
				if($this->notice->npages) $coins_span.="&amp;rft.epage=".rawurlencode($f($this->notice->npages));
				if($this->bulletin_date) $coins_span.="&amp;rft.date=".rawurlencode($f($this->bulletin_date));
				*/
			break;
			case 'm':// livre
			default:
				$coins_span.="book";
				$coins_span.="&amp;rft.genre=book";
				$coins_span.="&amp;rft.btitle=".rawurlencode($f?$f($record_datas->get_tit1()):$record_datas->get_tit1());
				
				$title="";
				$serie = $record_datas->get_serie();
				if($serie['name']) {
					$title .= $serie['name'];
					if($record_datas->get_tnvol()) $title .= ', '.$record_datas->get_tnvol();
					$title .= '. ';
				}
				$title .= $record_datas->get_tit1();
				
				$coins_span.="&amp;rft.title=".rawurlencode($f?$f($title):$title);
				if ($record_datas->get_code())	$coins_span.="&amp;rft.isbn=".rawurlencode($f?$f($record_datas->get_code()):$record_datas->get_code());
				if ($record_datas->get_npages()) $coins_span.="&amp;rft.tpages=".rawurlencode($f?$f($record_datas->get_npages()):$record_datas->get_npages());
				if ($record_datas->get_year()) $coins_span.="&amp;rft.date=".rawurlencode($f?$f($record_datas->get_year()):$record_datas->get_year());
			break;
		}

		if($record_datas->get_niveau_biblio() != "b"){
			$coins_span.="&rft_id=".rawurlencode($f?$f($record_datas->get_lien()):$record_datas->get_lien());
		}
		
		$collection = $record_datas->get_collection();
		$subcollection = $record_datas->get_subcollection();
		if($subcollection) {
			$coins_span.="&amp;rft.series=".rawurlencode($f?$f($subcollection->name):$subcollection->name);
		} elseif ($collection) {
			$coins_span.="&amp;rft.series=".rawurlencode($f?$f($collection->name):$collection->name);
		}
		
		$publishers = $record_datas->get_publishers();
		if (count($publishers)) {
			foreach($publishers as $publisher){
				$coins_span.="&amp;rft.pub=".rawurlencode($f?$f($publisher->name):$publisher->name);
				if($publisher->ville)$coins_span.="&amp;rft.place=".rawurlencode($f?$f($publisher->ville):$publisher->ville);
			}
		}
		
		if($record_datas->get_mention_edition()){
			$coins_span.="&rft.edition=".rawurlencode($f?$f($record_datas->get_mention_edition()):$record_datas->get_mention_edition());
		}
		
		$responsabilites = $record_datas->get_responsabilites();
		if (count($responsabilites["auteurs"])) {
			foreach($responsabilites["auteurs"] as $responsabilite){
				if($responsabilite['name']) $coins_span.="&amp;rft.aulast=".rawurlencode($f?$f($responsabilite['name']):$responsabilite['name']);
				if($responsabilite['rejete']) $coins_span.="&amp;rft.aufirst=".rawurlencode($f?$f($responsabilite['rejete']):$responsabilite['rejete']);
			}
		}
		$coins_span.="'></span>";
		return 	$coins_span;
	}

	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_list($notice_id) {
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		global $opac_sur_location_activate;

		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;

		$record_datas = static::get_record_datas($notice_id);

		$type = $record_datas->get_niveau_biblio();
		$id = $record_datas->get_id();
		$bull = $record_datas->get_bul_info();
		$bull_id = $bull['bulletin_id'];

		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;

		$expls_datas = $record_datas->get_expls_datas();

		$expl_list_header_deb="";
		foreach ($expls_datas['colonnesarray'] as $colonne) {
			$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
		}
		$expl_list_header_deb.="<th>".$msg['statut']."</th>";
		$expl_liste="";

		foreach ($expls_datas['expls'] as $expl) {
			$expl_liste .= "<tr>";
			$colencours="";

			foreach ($expls_datas['colonnesarray'] as $colonne) {
				$colencours = $expl[$colonne];
				if (($colonne == "location_libelle") && $expl['num_infopage']) {
					if ($expl['surloc_id'] != "0") $param_surloc="&surloc=".$expl['surloc_id'];
					else $param_surloc="";
					$expl_liste .="<td class='".$colonne."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</a></td>";
				} else
					$expl_liste .="<td class='".$colonne."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
			}

			$situation = "";
			if ($expl['statut_libelle_opac'] != "") $situation .= $expl['statut_libelle_opac']."<br />";
			if ($expl['flag_resa']) {
				$situation .= "<strong>".$msg['expl_reserve']."</strong>";
			} else {
				if ($expl['pret_flag']) {
					if($expl['pret_retour']) { // exemplaire sorti
						global $opac_show_empr ;
						if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
							$situation .= $msg['entete_show_empr'].htmlentities(" ".$expl['empr_prenom']." ".$expl['empr_nom'],ENT_QUOTES, $charset)."<br />";
						}
						$situation .= "<strong>".$msg['out_until']." ".formatdate($expl['pret_retour'])."</strong>";
						// ****** Affichage de l'emprunteur
					} else { // pas sorti
						$situation .= "<strong>".$msg['available']."</strong>";
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
						$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>";
					} else {
						$situation .= "<strong>".$msg['exclu']."</strong>";
					}
				}
			} // fin if else $flag_resa
			$expl_liste .= "<td class='expl_situation'>".$situation." </td>";

			//Champs personalisés
			$perso_aff = "" ;
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
				for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
					$p=$perso_["FIELDS"][$i];
					if ($p['OPAC_SHOW'] ) {
						if(!$header_found_p_perso) {
							$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
							$nb_perso_aff++;
						}
						if( $p["AFF"])	{
							$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
						}
						else $perso_aff.="<td class='p_perso'>&nbsp;</td>";
					}
				}
			}
			$header_found_p_perso=1;
			$expl_liste.=$perso_aff;

			$expl_liste .="</tr>";
			$expl_liste_all.=$expl_liste;

			if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
				if($expl['expl_location']==$_SESSION["empr_location"]) {
					$expl_liste_loc.=$expl_liste;
				} else $nb_expl_autre_loc++;
			}
			$expl_liste="";

		} // fin while
		$expl_list_header_deb="<tr>".$expl_list_header_deb;
		//S'il y a des titres de champs perso dans les exemplaires
		if($header_perso_aff) {
			$expl_list_header_deb.=$header_perso_aff;
		}
		$expl_list_header_deb.="</tr>";

		if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
			// affichage avec onglet selon la localisation
			if(!$expl_liste_loc) $expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
			$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
			$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
			$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
			$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
		} else {
		// affichage de la liste d'exemplaires calculée ci-dessus
			if (!$expl_liste_all && $opac_show_empty_items_block==1) {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
			} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
				$expl_liste_all = "";
			} else {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
			}
		}
		return $expl_liste_all;

	} // fin function get_display_expl_list

	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_responsive_list($notice_id) {
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		global $opac_sur_location_activate;

		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;

		$record_datas = static::get_record_datas($notice_id);

		$type = $record_datas->get_niveau_biblio();
		$id = $record_datas->get_id();
		$bull = $record_datas->get_bul_info();
		$bull_id = $bull['bulletin_id'];

		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;

		$expls_datas = $record_datas->get_expls_datas();

		$expl_list_header_deb="<tr class='thead'>";
		foreach ($expls_datas['colonnesarray'] as $colonne) {
			$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
		}
		$expl_list_header_deb.="<th class='expl_header_statut'>".$msg['statut']."</th>";
		$expl_liste="";

		foreach ($expls_datas['expls'] as $expl) {
			$expl_liste .= "<tr class='item_expl !!class_statut!!'>";
			$colencours="";

			foreach ($expls_datas['colonnesarray'] as $colonne) {
				$colencours = $expl[$colonne];
				if (($colonne == "location_libelle") && $expl['num_infopage']) {
					if ($expl['surloc_id'] != "0") $param_surloc="&surloc=".$expl['surloc_id'];
					else $param_surloc="";
					$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</a></td>";
				} else
					$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
			}

			$situation = "";
			if ($expl['statut_libelle_opac'] != "") $situation .= $expl['statut_libelle_opac']."<br />";
			if ($expl['flag_resa']) {
				$situation .= "<strong>".$msg['expl_reserve']."</strong>";
				$class_statut = "expl_reserve";
			} else {
				if ($expl['pret_flag']) {
					if($expl['pret_retour']) { // exemplaire sorti
						global $opac_show_empr ;
						if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
							$situation .= $msg['entete_show_empr'].htmlentities(" ".$expl['empr_prenom']." ".$expl['empr_nom'],ENT_QUOTES, $charset)."<br />";
						}
						$situation .= "<strong>".$msg['out_until']." ".formatdate($expl['pret_retour'])."</strong>";
						$class_statut = "expl_out";
						// ****** Affichage de l'emprunteur
					} else { // pas sorti
						$situation .= "<strong>".$msg['available']."</strong>";
						$class_statut = "expl_available";
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
						$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>";
						$class_statut = "expl_transfert";
					} else {
						$situation .= "<strong>".$msg['exclu']."</strong>";
						$class_statut = "expl_unavailable";
					}
				}
			} // fin if else $flag_resa
			$expl_liste .= "<td class='".$msg['statut']."'>".$situation." </td>";
			$expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);

			//Champs personalisés
			$perso_aff = "" ;
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
				for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
					$p=$perso_["FIELDS"][$i];
					if ($p['OPAC_SHOW'] ) {
						if(!$header_found_p_perso) {
							$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
							$nb_perso_aff++;
						}
						if( $p["AFF"])	{
							$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
						}
						else $perso_aff.="<td class='p_perso'>&nbsp;</td>";
					}
				}
			}
			$header_found_p_perso=1;
			$expl_liste.=$perso_aff;

			$expl_liste .="</tr>";
			$expl_liste_all.=$expl_liste;

			if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
				if($expl['expl_location']==$_SESSION["empr_location"]) {
					$expl_liste_loc.=$expl_liste;
				} else $nb_expl_autre_loc++;
			}
			$expl_liste="";

		} // fin while
		//S'il y a des titres de champs perso dans les exemplaires
		if($header_perso_aff) {
		$expl_list_header_deb.=$header_perso_aff;
		}

		if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
		// affichage avec onglet selon la localisation
			if(!$expl_liste_loc) $expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
			$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
			$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
			$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
			$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
		} else {
		// affichage de la liste d'exemplaires calculée ci-dessus
			if (!$expl_liste_all && $opac_show_empty_items_block==1) {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
			} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
			$expl_liste_all = "";
			} else {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
			}
		}
		return $expl_liste_all;

	} // fin function get_display_expl_responsive_list

	/**
	 * Fontion qui génère le bloc H3 + table des autres lectures
	 * @param number $notice_id Identifiant de la notice
	 * @param number $bulletin_id Identifiant du bulletin
	 * @return string
	 */
	static public function get_display_other_readings($notice_id) {
		global $dbh, $msg;
		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;

		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = $bull['bulletin_id'];

		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return "";

		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		// Ajout ici de la liste des notices lues par les lecteurs de cette notice
		$rqt_autres_lectures = "SELECT oal.arc_expl_notice, oal.arc_expl_bulletin, count(*) AS total_prets,
					trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '%d/%m/%Y'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id
				FROM ((((pret_archive AS oal JOIN
					(SELECT distinct arc_id_empr FROM pret_archive nbec where (nbec.arc_expl_notice='".$notice_id."' AND nbec.arc_expl_bulletin='".$bulletin_id."') AND nbec.arc_id_empr !=0) as nbec
					ON (oal.arc_id_empr=nbec.arc_id_empr and oal.arc_id_empr!=0 and nbec.arc_id_empr!=0))
					LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id )
					LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id)
					LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				WHERE $restrict_date $pas_notice $pas_bulletin oal.arc_id_empr !=0
				GROUP BY oal.arc_expl_notice, oal.arc_expl_bulletin
				HAVING total_prets>=$opac_autres_lectures_nb_mini_emprunts
				ORDER BY $opac_autres_lectures_tri
				";

		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures) or die ("<br />".pmb_mysql_error()."<br />".$rqt_autres_lectures."<br />");
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			$odd_even=1;
			$inotvisible=0;
			$ret="";

			//droits d'acces emprunteur/notice
			$acces_j='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}

			if($acces_j) {
				$statut_j='';
				$statut_r='';
			} else {
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}

			while (($data=pmb_mysql_fetch_array($res_autres_lectures))) { // $inotvisible<=$opac_autres_lectures_nb_maxi
				$requete = "SELECT  1  ";
				$requete .= " FROM notices $acces_j $statut_j  WHERE notice_id='".$data[not_id]."' $statut_r ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if (pmb_mysql_num_rows($myQuery) && $inotvisible<=$opac_autres_lectures_nb_maxi) { // pmb_mysql_num_rows($myQuery)
					$inotvisible++;
					$titre = $data['tit'];
					// **********
					$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
					$responsab = get_notice_authors($data['not_id']) ;
					$as = array_search ("0", $responsab["responsabilites"]) ;
					if ($as!== FALSE && $as!== NULL) {
						$auteur_0 = $responsab["auteurs"][$as] ;
						$auteur = new auteur($auteur_0["id"]);
						$mention_resp = $auteur->isbd_entry;
					} else {
						$aut1_libelle = array();
						$as = array_keys ($responsab["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsab["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->isbd_entry;
						}
						$mention_resp = implode (", ",$aut1_libelle) ;
					}
					$mention_resp ? $auteur = $mention_resp : $auteur="";

					// on affiche les résultats
					if ($odd_even==0) {
						$pair_impair="odd";
						$odd_even=1;
					} else if ($odd_even==1) {
						$pair_impair="even";
						$odd_even=0;
					}
					if ($data['arc_expl_notice']) $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=notice_display&id=".$data['not_id']."&seule=1';\" style='cursor: pointer' ";
						else $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=bulletin_display&id=".$data['arc_expl_bulletin']."';\" style='cursor: pointer' ";
					$ret .= "<tr $tr_javascript>";
					$ret .= "<td>".$titre."</td>";
					$ret .= "<td>".$auteur."</td>";
					$ret .= "</tr>\n";
				}
			}
			if ($ret) $ret = "<h3 class='autres_lectures'>".$msg['autres_lectures']."</h3><table style='width:100%;'>".$ret."</table>";
		} else $ret="";

	return $ret;
	} // fin autres_lectures ($notice_id=0,$bulletin_id=0)

	/**
	 * Ajoute l'image
	 * @param unknown $notice_id Identifiant de la notice
	 * @param unknown $entree Contenu avant l'ajout
	 * @param unknown $depliable
	 */
	static public function do_image($notice_id, &$entree,$depliable) {
		global $charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg;
		global $opac_url_base ;
		global $msg;

		$record_datas = static::get_record_datas($notice_id);

		if ($record_datas->get_code() || $record_datas->get_thumbnail_url()) {
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $record_datas->get_thumbnail_url())) {
				$code_chiffre = pmb_preg_replace('/-|\.| /', '', $record_datas->get_code());
				$url_image = $opac_book_pics_url ;
				$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($record_datas->get_thumbnail_url()) ;
				$title_image_ok = "";
				if(!$record_datas->get_thumbnail_url()) $title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
				if(!trim($title_image_ok)){
					$title_image_ok = htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset);
				}
				if ($depliable) $image = "<img class='vignetteimg' src='".$opac_url_base."images/vide.png' title=\"".$title_image_ok."\" align='right' hspace='4' vspace='2' isbn='".$code_chiffre."' url_image='".$url_image."' vigurl=\"".$record_datas->get_thumbnail_url()."\"  alt='".$msg["opac_notice_vignette_alt"]."'/>";
				else {
					if ($record_datas->get_thumbnail_url()) {
						$url_image_ok=$record_datas->get_thumbnail_url();
					} else {
						$url_image_ok = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					}
					$image = "<img class='vignetteimg' src='".$url_image_ok."' title=\"".$title_image_ok."\" align='right' hspace='4' vspace='2' alt='".$msg["opac_notice_vignette_alt"]."' />";
				}
			} else $image="" ;
			if ($image) {
				$entree = "<table width='100%'><tr><td valign='top'>$entree</td><td valign='top' align='right'>$image</td></tr></table>" ;
			} else {
				$entree = "<table width='100%'><tr><td>$entree</td></tr></table>" ;
			}

		} else {
			$entree = "<table width='100%'><tr><td>$entree</td></tr></table>" ;
		}
	}
	 /**
	  * Retourne le script des notices similaires
	  * @return string
	  */
	static public function get_display_simili_script($notice_id) {
		global $opac_allow_simili_search;

		switch ($opac_allow_simili_search) {
			case "0" :
				$script_simili_search = "";
				break;
			case "1" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				$script_simili_search.= "show_expl_voisin_search('".$notice_id."');";
				break;
			case "2" :
				$script_simili_search = "show_expl_voisin_search('".$notice_id."');";
				break;
			case "3" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				break;
		}
		return $script_simili_search;
	}

	/**
	 * Renvoie les états de collections
	 * @param int $notice_id Identifiant de la notice
	 * @return mixed
	 */
	static public function get_display_collstate($notice_id) {
		global $msg;
		global $pmb_etat_collections_localise;

		$record_datas = static::get_record_datas($notice_id);

		$collstate = $record_datas->get_collstate();

		if($pmb_etat_collections_localise) {
			$collstate->get_display_list("",0,0,0,1);
		} else {
			$collstate->get_display_list("",0,0,0,0);
		}
		if($collstate->nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}
		return $affichage;
	}

	static protected function get_lang_list($tableau) {
		$langues = "";
		for ($i = 0 ; $i < sizeof($tableau) ; $i++) {
			if ($langues) $langues.=" ";
			$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
		}
		return $langues;
	}

	/**
	 * Fonction d'affichage des avis
	 * @param int $notice_id Identifiant de la notice
	 */
	static public function get_display_avis($notice_id) {
		global $msg;

		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();

		$nombre_avis = "";
		//Affichage des Etoiles et nombre d'avis
		if ($avis['qte'] > 0) {
			$nombre_avis = "<a href='#' title=\"".$msg['notice_title_avis']."\" onclick=\"w=window.open('avis.php?todo=liste&noticeid=".$record_datas->get_id()."','avis','width=600,height=290,scrollbars=yes,resizable=yes'); w.focus(); return false;\">".$avis['qte']."&nbsp;".$msg['notice_bt_avis']."</a>";
			$etoiles_moyenne = static::get_stars($avis['moyenne']);
			$img_tag .= $nombre_avis."<a href='#' title=\"".$msg['notice_title_avis']."\" onclick=\"w=window.open('avis.php?todo=liste&noticeid=".$record_datas->get_id()."','avis','width=600,height=290,scrollbars=yes,resizable=yes'); w.focus(); return false;\">".$etoiles_moyenne."</a>";
		} else {
			$nombre_avis = "<a href='#' title=\"".$msg['notice_title_avis']."\" onclick=\"w=window.open('avis.php?todo=liste&noticeid=".$record_datas->get_id()."','avis','width=600,height=290,scrollbars=yes,resizable=yes'); w.focus(); return false;\">".$msg['avis_aucun']."</a>";
			$img_tag .= $nombre_avis;
		}
		return $img_tag;
	}

	static public function get_display_avis_detail($notice_id) {
		global $dbh, $msg;
		global $action; // pour gérer l'affichage des avis en impression de panier
		global $allow_avis_ajout;
		global $avis_tpl_form1;
		global $opac_avis_note_display_mode,$charset;
		global $opac_avis_allow;

		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();

		$avis_tpl_form=$avis_tpl_form1;
		$avis_tpl_form=str_replace("!!notice_id!!",$record_datas->get_id(),$avis_tpl_form);
		$add_avis_onclick="show_add_avis(".$record_datas->get_id().");";
		if (isset($avis['avis']) && count($avis['avis'])) {
			$pair_impair="odd";
			$ret="";
			foreach ($avis['avis'] as $data) {
				// on affiche les résultats
				if ($pair_impair=="odd") $pair_impair="even"; else 	$pair_impair="odd";
				$ret .= "<tr  class='$pair_impair' >";

				if($opac_avis_note_display_mode){
					if($opac_avis_note_display_mode!=1){
						$categ_avis=$msg['avis_detail_note_'.$data['note']];
					}
					if($opac_avis_note_display_mode!=2){
						$etoiles = static::get_stars($data['note']);
					}
					if($opac_avis_note_display_mode==3 || $opac_avis_note_display_mode==5)$aff=$etoiles."<br />".$categ_avis;
					else if($opac_avis_note_display_mode==4)$aff=$etoiles;
					else $aff=$etoiles.$categ_avis;
					$ret .= "<td class='avis_detail_note_".$data['note']."'  >".$aff."</td>";
				}
				$ret .= "
					<td class='avis_detail_commentaire_".$data['note']."'>".do_bbcode($data['commentaire'])."
						<br />
						<span class='avis_detail_signature'>".htmlentities($data['sujet'],ENT_QUOTES,$charset)."</span>
					</td>
				</tr>\n";
			}
			if($opac_avis_note_display_mode!=2 && $opac_avis_note_display_mode) $etoiles_moyenne = static::get_stars($avis['moyenne']);

			if ($action=="print" || ($opac_avis_allow==1 && !$_SESSION["user_code"] )) {
				$ret = "<h3 class='avis_detail'>".$msg['avis_detail']." :
					".str_replace("!!nb_avis!!",$avis['qte'],$msg['avis_detail_nb_auth_ajt'])."
					</h3>
					<table style='width:100%;'>".$ret."</table>";
			} else {
				$ret = "<h3 class='avis_detail'>".$msg['avis_detail']." $etoiles_moyenne
						<span class='lien_ajout_avis'> :
							<a href='#' onclick=\"$add_avis_onclick return false;\">".str_replace("!!nb_avis!!",$avis['qte'],$msg['avis_detail_nb_ajt'])."</a>
						</span></h3>
						$avis_tpl_form
						<table style='width:100%;'>".$ret."</table>";
			}
		} else {
			if ($action=="print" || ($opac_avis_allow==1 && !$_SESSION["user_code"] )) {
				$ret = "<h3 class='avis_detail'>".$msg['avis_detail_aucun_auth_ajt']."
					</h3>";
			} else {
				$ret="<h3 class='avis_detail'>".$msg['avis_detail']."
						<span class='lien_ajout_avis'>
							<a href='#' onclick=\"$add_avis_onclick return false;\">".$msg['avis_detail_aucun_ajt']."</a>

						</span></h3>
						$avis_tpl_form" ;
			}
		}
		return $ret;
	}

	static public function get_display_avis_only_stars($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();

		return "<a href='#' title=\"".$msg['notice_title_avis']."\" onclick=\"w=window.open('avis.php?todo=liste&noticeid=".$record_datas->get_id()."','avis','width=600,height=290,scrollbars=yes,resizable=yes'); w.focus(); return false;\">".static::get_stars($avis['moyenne'])."</a>";
	}
	/**
	 * Retourne l'affichage des étoiles
	 * @param float $moyenne
	 */
	static protected function get_stars($moyenne) {
		$etoiles_moyenne="";

		if (!$moyenne) {
			for ($i = 0; $i < 5; $i++) {
				$etoiles_moyenne .= "<img class='img_star_avis' border=0 src='".get_url_icon('star_unlight.png')."' align='absmiddle' />";
			}
		} else {
			$cpt_star = 0;
			for ($i = 1; $i <= $moyenne; $i++) {
				$etoiles_moyenne.="<img class='img_star_avis' border=0 src='".get_url_icon('star.png')."' align='absmiddle' />";
				$cpt_star++;
			}
			if (substr($moyenne,2,2) > 75) {
				$etoiles_moyenne.="<img class='img_star_avis' border=0 src='".get_url_icon('star.png')."' align='absmiddle' />";
				$cpt_star++;
			} elseif (substr($moyenne,2,2) > 25) {
				$etoiles_moyenne .= "<img class='img_star_avis' border=0 src='".get_url_icon('star-semibright.png')."' align='absmiddle' />";
				$cpt_star++;
			}
			for ($cpt_star;$cpt_star < 5 ; $cpt_star++) {
				$etoiles_moyenne .= "<img class='img_star_avis' border=0 src='".get_url_icon('star_unlight.png')."' align='absmiddle' />";
			}
		}
		return $etoiles_moyenne;
	}

	/**
	 * Fonction d'affichage des suggestions
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_suggestion($notice_id){
		global $msg;
		$do_suggest="<a href='#' onclick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup&id_notice=".$notice_id."','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\">".$msg['suggest_notice_opac']."</a>";
		return $do_suggest;
	}

	/**
	 * Retourne l'affichage étendu d'une notice
	 * @param unknown $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_extended($notice_id, $django_directory = "") {
		global $include_path;

		$record_datas = static::get_record_datas($notice_id);

		$template = static::get_template("record_extended_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice dans un résultat de recherche
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_in_result($notice_id, $django_directory = "") {
		global $include_path;
		global $opac_notices_format_django_directory;

		$record_datas = static::get_record_datas($notice_id);

		$template = static::get_template("record_in_result_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne le bon template
	 * @param string $template_name Nom du template : record_extended ou record_in_result
	 * @param string $niveau_biblio Niveau bibliographique
	 * @param string $typdoc Type de document
	 * @param string $django_directory Répertoire Django à utiliser (paramètre opac_notices_format_django_directory par défaut)
	 * @return string Nom du template à appeler
	 */
	static public function get_template($template_name, $niveau_biblio, $typdoc, $django_directory = "") {
		global $include_path;
		global $opac_notices_format_django_directory;

		if (!$django_directory) $django_directory = $opac_notices_format_django_directory;

		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/common/".$template_name."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/common/".$template_name."_".$niveau_biblio.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name.".tpl.html";
		}
		return $include_path."/templates/record/common/".$template_name.".tpl.html";
	}

	static public function get_liens_opac() {
		global $liens_opac;

		return $liens_opac;
	}

	/**
	 * Retourne l'affichage des documents numériques
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques
	 */
	static public function get_display_explnums($notice_id) {
		global $include_path;
		require_once($include_path."/explnum.inc.php");

		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = $bull['bulletin_id'];

		if ($record_datas->get_niveau_biblio() == "b" && ($explnums = show_explnum_per_notice(0, $bulletin_id, ''))) {
			return $explnums;
		}
		if ($explnums = show_explnum_per_notice($notice_id, 0, '')) {
			return $explnums;
		}
		return "";
	}

	static public function get_display_size($notice_id) {
		$record_datas = static::get_record_datas($notice_id);

		$size = array();
		if ($record_datas->get_npages()) $size[] = $record_datas->get_npages();
		if ($record_datas->get_ill()) $size[] = $record_datas->get_ill();
		if ($record_datas->get_size()) $size[] = $record_datas->get_size();

		return implode(" / ", $size);
	}

	static public function get_display_demand($notice_id) {
		global $msg, $include_path, $form_modif_demande, $form_linked_record, $demandes_active, $opac_demandes_allow_from_record;

		if ($demandes_active && $opac_demandes_allow_from_record && $_SESSION['id_empr_session']) {
			$record_datas = static::get_record_datas($notice_id);
			$demande = new demandes();
			$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$demande->theme_demande);
			$types = new demandes_types('demandes_type','id_type','libelle_type',$demande->type_demande);

			$f_modif_demande = $form_modif_demande;
			$f_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$f_modif_demande);
			$f_modif_demande = str_replace('!!sujet!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!progression!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!empr_txt!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!idempr!!',$_SESSION['id_empr_session'],$f_modif_demande);
			$f_modif_demande = str_replace('!!iduser!!',"",$f_modif_demande);
			$f_modif_demande = str_replace('!!titre!!','',$f_modif_demande);

			$etat=$demande->getStateValue();
			$f_modif_demande = str_replace('!!idetat!!',$etat['id'],$f_modif_demande);
			$f_modif_demande = str_replace('!!value_etat!!',$etat['comment'],$f_modif_demande);
			$f_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector(),$f_modif_demande);
			$f_modif_demande = str_replace('!!select_type!!',$types->getListSelector(),$f_modif_demande);

			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$date_dmde = "<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!'
					onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_dmde&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'date_debut', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"/>";
			$f_modif_demande = str_replace('!!date_demande!!',$date_dmde,$f_modif_demande);

			$f_modif_demande = str_replace('!!date_fin_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_fin!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue_btn!!',$date,$f_modif_demande);

			$f_modif_demande = str_replace('!!iddemande!!', '', $f_modif_demande);

			$f_modif_demande = str_replace('!!form_linked_record!!', $form_linked_record, $f_modif_demande);
			$f_modif_demande = str_replace('!!linked_record!!', $record_datas->get_tit1(), $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_id!!", $notice_id, $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_link!!", $record_datas->get_permalink(), $f_modif_demande);

			$act_cancel = "demandDialog_".$notice_id.".hide();";
			$act_form = "./empr.php?tab=request&lvl=list_dmde&sub=save_demande";

			$f_modif_demande = str_replace('!!form_action!!',$act_form,$f_modif_demande);
			$f_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$f_modif_demande);

			// Requires et début de formulaire
			$html = "
					<script type='text/javascript'>
						require(['dojo/parser', 'dijit/Dialog']);
						document.body.setAttribute('class', 'tundra');
					</script>
					<div data-dojo-type='dijit/Dialog' data-dojo-id='demandDialog_".$notice_id."' title='".$msg['do_demande_on_document']."' style='display:none;width:75%;'>
						".$f_modif_demande."
					</div>
					<a href='#' onClick='demandDialog_".$notice_id.".show();return false;'>
						".$msg['do_demande_on_record']."
					</a>";

			return $html;
		}
		return "";
	}
	
	/**
	 * Retourne le rendu html des documents numériques du bulletin parent de la notice d'article
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques du bulletin parent
	 */
	static public function get_display_bull_for_art_expl_num($notice_id) {
		
		$record_datas = static::get_record_datas($notice_id);
		$bul_infos = $record_datas->get_bul_info();
		
		$paramaff["mine_type"]=1;
		$retour = show_explnum_per_notice(0, $bul_infos['bulletin_id'],"",$paramaff);

		return $retour;
	}
}