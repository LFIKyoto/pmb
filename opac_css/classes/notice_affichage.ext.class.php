<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage.ext.class.php,v 1.237.2.6 2015-11-04 15:48:55 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if( $opac_notice_affichage_class && file_exists($class_path."/notice_affichage/".$opac_notice_affichage_class.".class.php")){
	require_once($class_path."/notice_affichage/".$opac_notice_affichage_class.".class.php");
}

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

require_once($include_path."/templates/collstate.tpl.php");

 // Use this class if you want to show responsability functions before authors. 
 // This class defines a new fetch_auteurs function that overwrites the one included in the parent class notice_affichage
 // using this function you can load the author functions from the litteral_function.xml file if this exists in the lang directory.
 // Marco Vaninetti

class notice_affichage_custom_it extends notice_affichage {
   
	function fetch_auteurs() {
		global $fonction_auteur0;
		global $dbh ;
		global $include_path;
		global $lang, $tdoc, $langue_doc;
		$this->responsabilites  = array() ;
		$auteurs = array() ;
		
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;
		
		// if literal_function.xml exists we use this instead of function.xml
	
		$ISBDv2=0;
	
		if (is_file("$include_path/marc_tables/$lang/literal_function.xml")) $ISBDv2=1;
	
		if (!count($tdoc)) $tdoc = new marc_list('doctype');
		if (!count($fonction_auteur0)) {
			if ($ISBDv2)
				$fonction_auteur0 = new marc_list('literal_function');
			else
				$fonction_auteur0 = new marc_list('function');
			$fonction_auteur0 = $fonction_auteur0->table;
		}
		if (!count($langue_doc)) {
			$langue_doc = new marc_list('lang');
			$langue_doc = $langue_doc->table;
		}
		
		$rqt = "SELECT author_id, responsability_fonction, responsability_type, author_name, author_rejete, author_type, author_date, author_see, author_web ";
		$rqt.= "FROM responsability, authors ";
		$rqt.= "WHERE responsability_notice='".$this->notice_id."' AND responsability_author=author_id ";
		$rqt.= "ORDER BY responsability_type, responsability_ordre, responsability_fonction " ;
		$res_sql = pmb_mysql_query($rqt, $dbh);
		while (($notice=pmb_mysql_fetch_object($res_sql))) {
			$responsabilites[] = $notice->responsability_type ;
			if ($notice->author_rejete) $auteur_isbd = $notice->author_rejete." ".$notice->author_name ;
				else  $auteur_isbd = $notice->author_name ;
			// on s'arrête là pour auteur_titre = "Prénom NOM" uniquement
			$auteur_titre = $auteur_isbd ;
			// on complète auteur_isbd pour l'affichage complet
			if ($notice->author_date) $auteur_isbd .= " (".$notice->author_date.")" ;
			// URL de l'auteur
			if ($notice->author_web) $auteur_web_link = " <a href='$notice->author_web' target='_blank'><img src='./images/globe.gif' border='0'/></a>";
				else $auteur_web_link = "" ;
			if (!$this->to_print) $auteur_isbd .= $auteur_web_link ;
			$auteur_isbd = inslink($auteur_isbd, str_replace("!!id!!", $notice->author_id, $this->lien_rech_auteur)) ;
				
			if ($notice->responsability_fonction) $fonction_aut=$fonction_auteur0[$notice->responsability_fonction] ;
			else {
				$fonction_aut="";
				$notice->responsability_fonction="0";
			}
			
			$auteurs[] = array( 
					'id' => $notice->author_id,
					'fonction' => $notice->responsability_fonction,
					'responsability' => $notice->responsability_type,
					'name' => $notice->author_name,
					'rejete' => $notice->author_rejete,
					'date' => $notice->author_date,
					'type' => $notice->author_type,
					'fonction_aff' => $fonction_aut,
					'auteur_isbd' => $auteur_isbd,
					'auteur_titre' => $auteur_titre
					) ;
		}
			
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;
		
		// $this->auteurs_principaux 
		// on ne prend que le auteur_titre = "Prénom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
				$aut1_libelle[]= $auteur_1["auteur_titre"];
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
		}
		$flag1=0;
		// $this->auteurs_tous
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			if($auteur_0["fonction"]!="0" and $auteur_0["fonction"]!= 70)  $mention_resp_lib= $auteur_0["fonction_aff"]." ".$mention_resp_lib;
			$first_mention=$auteur_0["fonction_aff"];
			$mention_resp[] = $mention_resp_lib ;
		}
		$i=0;
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		
		while ($i < count($as) ) {
			$j=count($as)-$i-1;
			$indice = $as[$j] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$flag= $auteur_1["fonction"];
			$fonct_aff=$auteur_1["fonction_aff"];
			$mention_resp_lib = "";
			$k=0;
			$sep="";
			while ($flag==$auteur_1["fonction"]) {
				$mention_resp_lib =$auteur_1["auteur_isbd"].$sep.$mention_resp_lib;
				if ($k==0) $sep= " e ";
				else $sep=",";
				$k++;
				$indice = $as[$j-$k] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			}
			$i=$i+$k;
					
			if($fonct_aff==$first_mention) {
				if ($k==1)$mention_resp_lib=$mention_resp[0]." e ".$mention_resp_lib;
				else $mention_resp_lib=$mention_resp[0].", ".$mention_resp_lib;
				$flag1++;
			} else if($fonct_aff !="") $mention_resp_lib=$fonct_aff." ".$mention_resp_lib;
			$mention_resp1[] = $mention_resp_lib ;
		}
		$mention_resp1 =array_reverse($mention_resp1);
		
		if($flag1==1) $mention_resp=$mention_resp1;
		else 	$mention_resp= array_merge($mention_resp,$mention_resp1);
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		$i=0;
		while ($i < count($as) ) {
			$j=count($as)-$i-1;
			$indice = $as[$j] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$flag= $auteur_2["fonction"];
			$fonct_aff=$auteur_2["fonction_aff"];
			$mention_resp_lib = "";
			$k=0;
			$sep="";
			while ($flag==$auteur_2["fonction"]) {
				$mention_resp_lib =$auteur_2["auteur_isbd"].$sep.$mention_resp_lib;
				if ($k==0) $sep= " e ";
				else $sep=",";
				$k++;
					$indice = $as[$j-$k] ;
					$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			}
			$i=$i+$k;
			$mention_resp_lib =$fonct_aff." ".$mention_resp_lib;
			$mention_resp2[] =$mention_resp_lib ;
		
		}
		$mention_resp2 =array_reverse($mention_resp2);
		$mention_resp= array_merge($mention_resp,$mention_resp2);
		$libelle_mention_resp = implode (" ; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
		else $this->auteurs_tous ="" ;
	} // end fetch_auteurs
	
} // end class notice_affichage_custom_it


class notice_affichage_custom_bretagne extends notice_affichage {
		
	function do_public($short=0,$ex=1) {
	
		global $dbh;
		global $msg;
		global $charset;
		global $opac_url_base, $opac_permalink;
	
		$this->fetch_categories() ;
		$this->notice_public="<table>";
		
		// ******* afin de pouvoir concaténer en td /td sous-collection et collection le cas échéant
		global $colspanbretagne;
		if ($this->notice->subcoll_id || ($this->notice->year && $this->notice->ed1_id)) $colspanbretagne = " colspan='3' ";
		else $colspanbretagne = "";
	
		// Notices parentes
		$this->notice_public.=$this->parents;
	
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td $colspanbretagne>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol)
				$this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td $colspanbretagne><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= ": ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td $colspanbretagne>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td $colspanbretagne>".$this->notice->tit3."</td></tr>" ;
		
		if ($this->auteurs_tous) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td $colspanbretagne>".$this->auteurs_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td $colspanbretagne>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year) {
			$annee = ", ".$this->notice->year;
			$colspanbretagneediteur="";
		} else $colspanbretagneediteur=$colspanbretagne;
	
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td $colspanbretagneediteur>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur)). $annee."</td></tr>" ;
			$annee = "" ;
		}
	
		// *** collection  et sous-collection
		// ******* concaténer en td /td sous-collection et collection
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>";
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection));
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td $colspanbretagne>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		}
	
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		if ($annee) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td $colspanbretagne>".$this->notice->year."</td></tr>" ;
	
		// Titres uniformes
		if(($tu_liste=$this->notice->tu->get_print_type(2,$opac_url_base."/index.php?lvl=titre_uniforme_see&id=" ))) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td><td $colspanbretagne>".$tu_liste."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td $colspanbretagne>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td $colspanbretagne>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill)
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td $colspanbretagne>".$this->notice->ill."</td></tr>";
		if ($this->notice->size)
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td $colspanbretagne>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp)
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td $colspanbretagne>".$this->notice->accomp."</td></tr>";
			
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td $colspanbretagne>".$zoneNote."</td></tr>";
	
		// Permalink avec Id
		if ($opac_permalink) $ret.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td $colspanbretagne><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td $colspanbretagne>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td $colspanbretagne>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		$this->notice_public.=$this->genere_in_perio();
		if (!$short) $this->notice_public .= $this->aff_suite() ;
		$this->notice_public.="</table>\n";
		
		//Notices liées
		// ajoutées en dehors de l'onglet PUBLIC ailleurs
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	} // end do_public

	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
	
		global $tdoc;
	
		// afin d'éviter de recalculer un truc déjà calculé...
		// MODIF ER 21 oct 2008 : pour la Bretagne, on recalcule forcément à cause du colspanbretagne
		// if ($this->affichage_suite) return $this->affichage_suite ;
		
		// ******* afin de pouvoir concaténer en td /td sous-collection et collection le cas échéant, 
		//     récupérer  $colspanbretagne calculé par do_public
		global $colspanbretagne;
	
		// serials : si article
		//$ret .= $this->genere_in_perio () ;
		
		//Espace
		$ret.="\n\t<tr class='tr_spacer'><td class='td_spacer'>&nbsp;</td><td $colspanbretagne></td></tr>";
		
		// résumé
		if($this->notice->n_resume)
	 		$ret .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td $colspanbretagne>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) 
	 		$ret .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td $colspanbretagne>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) 
			$ret_index .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td $colspanbretagne>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if ($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = $msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if ($mots_cles)
			$ret_index.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td $colspanbretagne>".$mots_cles."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td $colspanbretagne>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
			}
	
		if ($ret_index) {
			$ret.=$ret_index;
			// espace
			// $ret.="<tr class='tr_spacer'><td class='td_spacer'>&nbsp;</td><td $colspanbretagne></td></tr>";
		}
		
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"] && $p["NAME"]=="type_nature") {
					$perso_aff .="\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".substr($msg['typdocdisplay_start'],0,4)." :</span></td><td $colspanbretagne>".$tdoc->table[$this->notice->typdoc]."&nbsp; ;";
					$perso_aff .=" &nbsp;".$p["AFF"]."</td></tr>";
				} elseif($p['OPAC_SHOW'] && $p["AFF"]) {
					$perso_aff .="\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td $colspanbretagne>".$p["AFF"]."</td></tr>";
				}
			}
		}
		if ($perso_aff) {
			$ret .= $perso_aff ;
		}
		
		if ($this->notice->lien) {
			//$ret.="\n\t<tr class='tr_spacer'><td class='td_spacer'>&nbsp;</td><td $colspanbretagne></td></tr>";
			$ret.="\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td $colspanbretagne>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}		
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td $colspanbretagne>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td $colspanbretagne><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "\n\t<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td $colspanbretagne><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		return $ret ;
	}
	
	// fonction de génération de ,la mention in titre du pério + numéro
	function genere_in_perio () {
		global $charset ;
		// serials : si article
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			$bulletin = $this->parent_title;
			$notice_mere = inslink("<span class='perio_title'>".$this->parent_title."</span>", str_replace("!!id!!", $this->parent_id, $this->lien_rech_perio));
			if($this->parent_numero) $numero = $this->parent_numero." " ;
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			if ($this->parent_date) $date_affichee = " (".$this->parent_date.")";
			elseif ($this->parent_date_date) $date_affichee .= " [".formatdate($this->parent_date_date)."]";
			else $date_affichee="" ;
			$bulletin = inslink("<span class='bull_title'>".$numero.$date_affichee."</span>", str_replace("!!id!!", $this->bulletin_id, $this->lien_rech_bulletin));
			$this->bulletin_numero=$numero;
			$this->bulletin_date=$date_affichee;
			$mention_parent = "<b>in</b> $notice_mere > $bulletin ";
			$retour .= "<br />$mention_parent";
			$pagination = htmlentities($this->notice->npages,ENT_QUOTES, $charset);
			//if ($pagination) $retour .= ".&nbsp;-&nbsp;$pagination";
		}
		return $retour ;
	} // fin genere_in_perio ()
	
} // end class notice_affichage_custom_bretagne


class notice_affichage_custom_alstom extends notice_affichage {


	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite) return $this->affichage_suite ;
		
		// serials : si article
		$ret .= $this->genere_in_perio () ;
		
		//Espace
		$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// résumé
		if($this->notice->n_resume)
	 		$ret .= "<tr><td align='right' class='bg-grey'><b>".$msg['n_resume_start']."</b></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) 
	 		$ret .= "<tr><td align='right' class='bg-grey' style='color:#000000' ><b>".$msg['n_contenu_start']."</b></td><td  style='color:#000000'>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// Catégories
		if($this->categories_toutes) 
			$ret .= "<tr><td align='right' class='bg-grey'><b>".$msg['categories_start']."</b></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret .= "<tr><td align='right' class='bg-grey'><b>".$msg['concepts_start']."</b></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles)
			$ret .= "<tr><td align='right' class='bg-grey'><b>".$msg['motscle_start']."</b></td><td>".$mots_cles."</td></tr>";
		
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret .= "<tr><td align='right' class='bg-grey'><b>".$msg['indexint_start']."</b></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
			}
		
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'>".$p["TITRE"]."</td><td>".$p["AFF"]."</td></tr>";
				}
			}
		if ($perso_aff) {
			//Espace
			$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret .= $perso_aff ;
			}
		
		if ($this->notice->lien) {
			$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'
			style='color:#3B793B'><b>".$msg["lien_start"]."</b></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
				} else {
					$ret.="<a style='color:#3B793B' href=\"".$this->notice->lien."\" target=\"top\">".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a></td></tr>";
					}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><b>".$msg["eformat_start"]."</b></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><b>".$msg["notice_permalink"]."</b></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><b>".$msg["notice_permalink"]."</b></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		return $ret ;
	} 
	
}

class notice_affichage_mw extends notice_affichage {
	//affichage alterné de 2 styles différents dans les lignes du tableau des notices

	var $x="";		//gestion de l'alternance des lignes colorées dans le tableau HTML

	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_url_base;
		
		$this->fetch_categories() ;
		$this->notice_public = "<table>";

		// constitution de la mention de titre
		$x="";
		if ($this->notice->serie_name) {
			if ($x=="2") $x=""; 
				else $x="2";
			$this->notice_public.= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['tparent_start']."</b></td><td class='bg-grey$x'>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol)
			$this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		if ($x=="2") $x=""; 
			else $x="2";
		$this->notice_public.= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['title']." :</b></td>";
		$this->notice_public .= "<td class='bg-grey$x'>".$this->notice->tit1 ;

		if ($this->notice->tit4) $this->notice_public .= ": ".$this->notice->tit4 ;
		$this->notice_public.="</td></tr>";

		if ($this->notice->tit2) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['other_title_t2']." :</b></td><td class='bg-grey$x'>".$this->notice->tit2."</td></tr>" ;
		}
		if ($this->notice->tit3) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['other_title_t3']." :</b></td><td class='bg-grey$x'>".$this->notice->tit3."</td></tr>" ;
		}

		//type de document
		if ($tdoc->table[$this->notice->typdoc]) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['typdocdisplay_start']."</b></td><td class='bg-grey$x'>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		}

		//auteur
		if ($this->auteurs_tous) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['auteur_start']."</b></td><td class='bg-grey$x'>".$this->auteurs_tous."</td></tr>";
		}

		// mention d'édition
		if ($this->notice->mention_edition) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['mention_edition_start']."</b></td><td class='bg-grey$x'>".$this->notice->mention_edition."</td></tr>";
		}

		//Date de publication
		if ($this->notice->ed1_id) {
			if ($x=="2") {$x="";} else {$x="2";}
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['editeur_start']."</b></td><td class='bg-grey$x'>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur)) ;
			$this->notice_public.="</td></tr>";
			if ($this->notice->year){
				if ($x=="2") {$x="";} else {$x="2";}
				$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['year_start']."</b></td><td class='bg-grey$x'>".$this->notice->year."</td></tr>";
				$annee=true;
			}
		}


		// collection
		if($this->notice->subcoll_id) {
			if ($x=="2") $x=""; 
				else $x="2";
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['coll_start']."</b></td><td class='bg-grey$x'>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>" ;
			if ($x=="2") $x=""; 
				else $x="2";
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['subcoll_start']."</b></td><td class='bg-grey$x'>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection))."</td></tr>" ;
		}
		elseif ($this->notice->coll_id) {
			if ($x=="2") {$x="";} else {$x="2";}
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['coll_start']."</b></td><td class='bg-grey$x'>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
		}
		if ($this->notice->nocoll) $this->notice_public .= " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		$this->notice_public.="</td></tr>";

		// ajout $annee si pas vide. Est vide si déjà ajouté plus haut
		if (!$annee) $this->notice_public .= $annee ;
		
		// Titres uniformes
		if(($tu_liste=$this->notice->tu->get_print_type(2,$opac_url_base."/index.php?lvl=titre_uniforme_see&id=" ))) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$tu_liste."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages)
		if ($this->notice->niveau_biblio<>"a") {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'   class='bg-grey$x'><b>".$msg['npages_start']."</b></td><td class='bg-grey$x'>".$this->notice->npages."</td></tr>";
		}
		else {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['npages_start_perio']."</b></td><td class='bg-grey$x'>".$this->notice->npages."</td></tr>";
		}

		if ($this->notice->ill){
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['ill_start']."</b></td><td class='bg-grey$x'>".$this->notice->ill."</td></tr>";
		}
		if ($this->notice->size){
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['size_start']."</b></td><td class='bg-grey$x'>".$this->notice->size."</td></tr>";
		}
		if ($this->notice->accomp){
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['accomp_start']."</b></td><td class='bg-grey$x'>".$this->notice->accomp."</td></tr>";
		}

		// ISBN ou NO. commercial
		if ($this->notice->code){
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['code_start']."</b></td><td class='bg-grey$x'>".$this->notice->code."</td></tr>";
		}

		if ($this->notice->prix){
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['price_start']."</b></td><td class='bg-grey$x'>".$this->notice->prix."</td></tr>";
		}

		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['n_gen_start']."</b></td><td class='bg-grey$x'>".$zoneNote."</td></tr>";
		}

		// langues
		if (count($this->langues)) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['537']." :</b></td><td class='bg-grey$x'>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <b>".$msg['711']." :</b> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} else
		if (count($this->languesorg)) {
			if ($x=="2") {$x="";} else {$x="2";}
			$this->notice_public .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['711']." :</b></td><td class='bg-grey$x'>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
		}

		if (!$short) $this->notice_public .= $this->aff_suite() ; else $this->notice_public.=$this->genere_in_perio();
		$this->notice_public.="</table>\n";

		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;

		return;
	}

	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_permalink, $opac_url_base;
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite) return $this->affichage_suite ;

		// serials : si article
		$ret .= $this->genere_in_perio () ;

		//Espace
		$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";

		// résumé
		$x="" ;
		if($this->notice->n_resume){
			if ($x=="2") $x=""; 
				else $x="2";
			$ret .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['n_resume_start']."</b></td><td class='bg-grey$x'>".nl2br($this->notice->n_resume)."</td></tr>";
		}

		// note de contenu
		if($this->notice->n_contenu){
			if ($x=="2") {$x="";} else {$x="2";}
			$ret .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['n_contenu_start']."</b></td><td class='bg-grey$x'>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
		}

		// Catégories
		if($this->categories_toutes){
			if ($x=="2") {$x="";} else {$x="2";}
			$ret .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['categories_start']."</b></td><td class='bg-grey$x'>".$this->categories_toutes."</td></tr>";
		}
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			if ($x=="2") {$x="";} else {$x="2";}
			$ret .= "<tr><td align='right' class='bg-grey$x'><b>".$msg['concepts_start']."</b></td><td class='bg-grey$x'>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}

		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles){
			if ($x=="2") {$x="";} else {$x="2";}
			$ret .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['motscle_start']."</b></td><td class='bg-grey$x'>".$mots_cles."</td></tr>";
		}

		// indexation interne
		if($this->notice->indexint) {
			if ($x=="2") {$x="";} else {$x="2";}
			$indexint = new indexint($this->notice->indexint);
			$ret .= "<tr><td align='right'  class='bg-grey$x'><b>".$msg['indexint_start']."</b></td><td class='bg-grey$x'>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}

		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				if ($x=="2") {$x="";} else {$x="2";}
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey$x'>".$p["TITRE"]."</td><td class='bg-grey$x'>".$p["AFF"]."</td></tr>";
			}
		}
		if ($perso_aff) {
			//Espace
			$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret .= $perso_aff ;
		}

		if ($this->notice->lien) {
			if ($x=="2") {$x="";} else {$x="2";}
			$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right'  class='bg-grey$x'><b>".$msg["lien_start"]."</b></td><td class='bg-grey$x'>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				$ret.="<a href=\"".$this->notice->lien."\" target=\"top\">".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS'){
				if ($x=="2") {$x="";} else {$x="2";}
				$ret.="<tr><td align='right'  class='bg-grey$x'><b>".$msg["eformat_start"]."</b></td><td class='bg-grey$x'>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
			}
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right'  class='bg-grey$x'><b>".$msg["notice_permalink"]."</b></td><td class='bg-grey$x'><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right'  class='bg-grey$x'><b>".$msg["notice_permalink"]."</b></td><td class='bg-grey$x'><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		return $ret ;
	}

	// fonction d'affichage des exemplaires, résa et expl_num
	function aff_resa_expl() {
		global $opac_resa ;
		global $opac_max_resa ;
		global $opac_show_exemplaires ;
		global $msg;
		global $dbh;
		global $popup_resa ;
		global $opac_resa_popup ; // la résa se fait-elle par popup ?
		global $allow_book ;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_resa_expl) return $this->affichage_resa_expl ;
		
		if ( (is_null($this->dom) &&$opac_show_exemplaires && $this->visu_expl && (!$this->visu_expl_abon || ($this->visu_expl_abon && $_SESSION["user_code"]))) || ($this->rights & 8) ) {

			$resa_check=check_statut($this->notice_id,0) ;
			// vérification si exemplaire réservable
			if ($resa_check) {
				// déplacé dans le IF, si pas visible : pas de bouton résa
				$requete_resa = "SELECT count(1) FROM resa WHERE resa_idnotice='$this->notice_id'";
				$nb_resa_encours = pmb_mysql_result(pmb_mysql_query($requete_resa,$dbh), 0, 0) ;
				if ($nb_resa_encours) $message_nbresa = str_replace("!!nbresa!!", $nb_resa_encours, $msg["resa_nb_deja_resa"]) ;
				if (($this->notice->niveau_biblio=="m") && ($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
					$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
					if ($opac_max_resa==0 || $opac_max_resa>$nb_resa_encours) {
						if ($opac_resa_popup) $ret .= "<a href='#' onClick=\"w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" id=\"bt_resa\">".$msg["bulletin_display_place_resa"]."</a>" ;
							else $ret .= "<a href='./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&oresa=popup' id='bt_resa'>".$msg["bulletin_display_place_resa"]."</a>" ;
						$ret .= $message_nbresa ;
						} else $ret .= str_replace("!!nb_max_resa!!", $opac_max_resa, $msg["resa_nb_max_resa"]) ;
					$ret.= "<br />";
				} elseif ( ($this->notice->niveau_biblio=="m") && !($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
					// utilisateur pas connecté
					// préparation lien réservation sans être connecté
					$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
					if ($opac_resa_popup) $ret .= "<a href='#' onClick=\"w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" id=\"bt_resa\">".$msg["bulletin_display_place_resa"]."</a>" ;
						else $ret .= "<a href='./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&oresa=popup' id='bt_resa'>".$msg["bulletin_display_place_resa"]."</a>" ;
					$ret .= $message_nbresa ;
					$ret .= "<br />";
					}
				}
			$temp = $this->expl_list($this->notice->niveau_biblio,$this->notice->notice_id);
			$ret .= $temp ;
			$this->affichage_expl = $temp ;
		}

		if ($this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) {
			if (($explnum = show_explnum_per_notice($this->notice_id, 0, ''))) {
				$ret .= "<h3>$msg[explnum]</h3>".$explnum;
				$this->affichage_expl .= "<h3>$msg[explnum]</h3>".$explnum;
			}
		}
		$this->affichage_resa_expl = $ret ;
		return $ret ;
	}
	
	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_double($depliable=1, $premier='ISBD') {
		global $msg;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_avis_allow;
		global $opac_allow_add_tag;
		
		$basket="<img src='".$opac_url_base."mw/images/commun/cale.gif' border='0' width='1' height='8' /><br /><div style='float:left;'>";
		if ($this->cart_allowed) {
			$basket.="<a href='cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."' target='cart_info' title=\"".$msg[notice_title_basket]."\"><img src='".$opac_url_base."mw/images/commun/basket_small_20x20.png' border='0' align='absmiddle' alt=\"".$msg[notice_title_basket]."\" />".$msg[notice_bt_panier]."</a>";
		}
		 if (($opac_avis_allow && $opac_avis_allow != 2) || ($_SESSION["user_code"] && $opac_avis_allow == 2)) {//Avis
				$basket.="&nbsp;&nbsp;<a href='#' onclick=\"javascript:open('avis.php?todo=liste&noticeid=$this->notice_id','avis','width=520,height=290,scrollbars=yes,resizable=yes')\"><img src='".$opac_url_base."mw/images/commun/avis.gif' align='absmiddle' border='0' />".$msg[notice_bt_avis]."</a><br /><br />";
		}
		if (($opac_allow_add_tag==1)||(($opac_allow_add_tag==2)&&($_SESSION["user_code"]))){//add tags
				$basket.="&nbsp;&nbsp;<a href='#' onclick=\"javascript:open('addtags.php?noticeid=$this->notice_id','Ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes')\"><img src='".$opac_url_base."mw/images/commun/tag.gif'align='absmiddle' border='0' />".$msg[notice_bt_tag]."</a>";
		}
		if ((!$this->cart_allowed)&&($opac_avis_allow==0)) {
			$basket.="";
		}
		$basket.="</div><br /><br />";
		
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
			else $case_a_cocher = "" ;
	
		if ($this->notice->niveau_biblio=="s") 
				$icon="icon_per_16x16.gif";
			elseif ($this->notice->niveau_biblio=="a")
				$icon="icon_art_16x16.gif";
			else
				$icon="icon_".$this->notice->typdoc."_16x16.gif";	
	
		if ($depliable) {
			$template="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\" />";
			if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
			$template.="		
				<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
				</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">";
		} else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
							$case_a_cocher";
			if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
			$template.="
							<span class=\"notice-heada\">!!heada!!</span>";
		}
		$template.=$basket;
		$template.="<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD') $template.="
	    	<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' onclick=\"show_what('ISBD', '!!id!!'); return false;\">ISBD</a></li>
	    	<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">Public</a></li>
			</ul>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	  		else $template.="
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">Public</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' onclick=\"show_what('ISBD', '!!id!!'); return false;\">ISBD</a></li>
	    		</ul>
				<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  			<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
		
		
	 	$template.="</div>";
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$template = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='mw/index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!ISBD!!", $template);
			$template = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='mw/index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!PUBLIC!!", $template);
			} elseif ($this->notice->niveau_biblio =='a') { 
				$template = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template);
				$template = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template);
			}
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		$this->do_image($this->notice_isbd,$depliable);
		$this->result = str_replace('!!ISBD!!', $this->notice_isbd, $this->result);
		$this->do_image($this->notice_public,$depliable);
		$this->result = str_replace('!!PUBLIC!!', $this->notice_public, $this->result);
	}

	// génération de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg; 
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_avis_allow;
		global $opac_allow_add_tag;
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
			else $case_a_cocher = "" ;
		
		$basket="<img src='".$opac_url_base."mw/images/commun/cale.gif' border='0' width='1' height='8'><br /><div style='float:left;'>";
		if ($this->cart_allowed) {
			$basket.="<a href='cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."' target='cart_info' title=\"".$msg[notice_title_basket]."\"><img src='".$opac_url_base."mw/images/commun/basket_small_20x20.png' border='0' align='absmiddle' alt=\"".$msg[notice_title_basket]."\" />".$msg[notice_bt_panier]."</a>";
		}
		if ($opac_avis_allow){	//Avis
				$basket.="&nbsp;&nbsp;<a href='#' onclick=\"javascript:open('avis.php?todo=liste&noticeid=$this->notice_id','avis','width=520,height=290,scrollbars=yes,resizable=yes')\"><img src='".$opac_url_base."mw/images/commun/avis.gif' align='absmiddle' border='0'>".$msg[notice_bt_avis]."</a>";
		}
		if (($opac_allow_add_tag==1)||(($opac_allow_add_tag==2)&&($_SESSION["user_code"]))){//add tags
				$basket.="&nbsp;&nbsp;<a href='#' onclick=\"javascript:open('addtags.php?noticeid=$this->notice_id','Ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes')\"><img src='".$opac_url_base."mw/images/commun/tag.gif' align='absmiddle' border='0'>".$msg[notice_bt_tag]."</a>";
		}
		if ((!$this->cart_allowed)&&($opac_avis_allow==0)) {
			 	$basket.="";
		}
		$basket.="</div><br /><br />";
		
		if ($this->notice->niveau_biblio=="s") 
				$icon="icon_per_16x16.gif";
			elseif ($this->notice->niveau_biblio=="a")
				$icon="icon_art_16x16.gif";
			else
				$icon="icon_".$this->notice->typdoc."_16x16.gif";	
	
		if ($depliable) { 
			$template="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\" />";
			if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
			$template.="
	    		<span class=\"notice-heada\">!!heada!!</span><br />
	    		</div>			
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">".$basket."!!ISBD!!</div>";
		}
			else {
				$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    				$case_a_cocher";
				if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
				$template.="
	    				<span class=\"heada\">!!heada!!</span><br />
		    			</div>			
				\n<div id='el!!id!!Child' class='child' >".$basket."
				!!ISBD!!
				\n</div>";
		}
			
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$template = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='mw/index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!ISBD!!", $template);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template);
		}
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		
		if ($what=='ISBD') {
			$this->do_image($this->notice_isbd,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_isbd, $this->result);
		} else {
			$this->do_image($this->notice_public,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_public, $this->result);
		}
	}
}

class notice_affichage_categ_regroup extends notice_affichage {
	
	// récupération des categories ------------------------------------------------------------------
	function fetch_categories() {
		global $opac_categories_affichage_ordre;
		$this->categories = get_notice_categories($this->notice_id) ;
		// catégories
		$categ_repetables=array() ;
		$max_categ = sizeof($this->categories) ; 
		for ($i = 0 ; $i < $max_categ ; $i++) {
			$categ_id = $this->categories[$i]["categ_id"] ;
			$categ = new category($categ_id);
			$categ_repetables[$categ->thes->libelle_thesaurus][$categ_id]["libelle"] = $categ->libelle;
			$categ_repetables[$categ->thes->libelle_thesaurus][$categ_id]["commentaire_public"] = $categ->commentaire_public;
			//$categ_repetables[$categ_id] = $categ->catalog_form;
		}
		$categ_final_table=array();    
        while (list($key,$val)=each($categ_repetables)) {
        	$categ_final_table[$key]=$key;
			if ($opac_categories_affichage_ordre!="1")
				asort($val) ;
        	reset($val);
        	$categ_r=array();
            while (list($categ_id,$libelle)=each($val)) {
            	// Si il y a présence d'un commentaire affichage du layer					
				$result_com = categorie::zoom_categ($categ_id, $libelle["commentaire_public"]);
            	$categ_r[$categ_id] = inslink($libelle["libelle"],  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']).$result_com['zoom'];
            }
            $categ_final_table[$key].="<br />&nbsp;".implode(", ",$categ_r);
        }
		$this->categories_toutes = implode("<br />",$categ_final_table) ;
	}
}

class notice_affichage_epires extends notice_affichage {
	
	// récupération des categories ------------------------------------------------------------------
	function fetch_categories() {
		$this->categories = get_notice_categories($this->notice_id) ;
		// catégories
		$categ_repetables=array() ;
		$max_categ = sizeof($this->categories) ; 
		for ($i = 0 ; $i < $max_categ ; $i++) {
			$categ_id = $this->categories[$i]["categ_id"] ;
			$categ = new category($categ_id);
			$categ_repetables[$categ->path_table[0]["libelle"]][$categ_id]["libelle"] = $categ->libelle;
			$categ_repetables[$categ->path_table[0]["libelle"]][$categ_id]["commentaire_public"] = $categ->commentaire_public;
		}
		$categ_final_table=array();
		while (list($key,$val)=each($categ_repetables)) {
			$categ_final_table[$key]=$key;
			asort($val) ;
			reset($val);
			$categ_r=array();
			while (list($categ_id,$libelle)=each($val)) {
				// Si il y a présence d'un commentaire affichage du layer					
				$result_com = categorie::zoom_categ($categ_id, $libelle["commentaire_public"]);
				$categ_r[$categ_id] = inslink($libelle["libelle"],  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']).$result_com['zoom'];
			}
			$categ_final_table[$key].="<br />&nbsp;".implode(", ",$categ_r);
		}
		$this->categories_toutes = implode("<br />",$categ_final_table) ;
		}
	}
	
class notice_affichage_id extends notice_affichage {
	
	
	function aff_suite() {	
		global $msg;
		global $charset;
		
		if ($this->affichage_suite) return $this->affichage_suite ;
		
		$ret=parent::aff_suite();
		$ret.= "<tr><td align='right' class='bg-grey'><b>".$msg["notice_id_start"]."</b></td><td>".htmlentities($this->notice_id,ENT_QUOTES, $charset)."</td></tr>";
		$this->affichage_suite=$ret;
		return $ret ;
	}
}

// Demande CNL affichage de trouver le livre près de chez vous http://www.placedeslibraires.fr/detaillivre.php?gencod= isbn
class notice_affichage_placedeslibraires extends notice_affichage {
	
	
	function aff_suite() {	
		global $msg;
		global $charset;
		
		if ($this->affichage_suite) return $this->affichage_suite ;
		$link="<a href='http://www.placedeslibraires.fr/detaillivre.php?gencod=".htmlentities(str_replace("-","",$this->notice->code),ENT_QUOTES, $charset)."'><i>".$msg["notice_trouver_le_livre"]."</i></a>";
		$ret=parent::aff_suite();
		$ret.= "<tr><td align='right' class='bg-grey'><b>".$msg["notice_librairie"]."</b></td><td>".$link."</td></tr>";
		$this->affichage_suite=$ret;
		return $ret ;
	}
}

// Demande Livr'Jeunes Nantes
class notice_affichage_livrjeunes extends notice_affichage {

	function genere_double($depliable=1, $premier='ISBD') {
		$this->genere_simple($depliable, 'PUBLIC');
	}
	
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg; 
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $icon_doc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $opac_avis_display_mode;
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag.="&nbsp;&nbsp;<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>&nbsp;&nbsp;";
		
		// LivrJeunes : avis supprimés d'ici + what=PUBLIC
		$what='PUBLIC';
				 
		if ($basket) $basket="<div>".$basket.$img_tag."</div>";
	
		$icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		
		if ($depliable) { 
			$template="
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\" />";
			if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
			$template.="
	    		<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span><br />
	    		</div>			
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">".$basket."!!ISBD!!\n
				!!SUITE!!
				</div>";
		} else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    				$case_a_cocher";
			if ($icon) $template.="
					<img src=\"".$opac_url_base."images/$icon\" />";
			$template.="
	    				<span class=\"heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span><br />
		    			</div>			
				\n<div id='el!!id!!Child' class='child' >".$basket."
				!!ISBD!!
				!!SUITE!!
				</div>";
		}
	  	if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
				
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$template = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!ISBD!!", $template);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
		}
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		
		if ($what=='ISBD') {
			$this->do_image($this->notice_isbd,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_isbd, $this->result);
		} else {
			$this->do_image($this->notice_public,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_public, $this->result);
		} 
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
		
		$this->aff_resa_expl();
		global $action;
		if ($action=="print") {
			$this->notice_public .= $this->affichage_resa_expl ;
		}
	}
	
	function aff_resa_expl() {
		global $opac_resa ;
		global $opac_max_resa ;
		global $opac_show_exemplaires ;
		global $msg;
		global $dbh;
		global $popup_resa ;
		global $opac_resa_popup ; // la résa se fait-elle par popup ?
		global $opac_resa_planning; // la résa est elle planifiée
		global $allow_book;

		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_resa_expl) return $this->affichage_resa_expl ;
/*		
		if (($avis_en_bas = $this->avis_detail())) {
			$ret = $avis_en_bas;
		}
*/		
		if ( (is_null($this->dom) && $opac_show_exemplaires && $this->visu_expl && (!$this->visu_expl_abon || ($this->visu_expl_abon && $_SESSION["user_code"]))) || ($this->rights & 8) ) {
			$temp = $this->expl_list($this->notice->niveau_biblio,$this->notice->notice_id, $this->bulletin_id);
			$ret .= $temp ;
			$this->affichage_expl = $ret ; 
		}
		if ($this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) 	
			if ($this->notice->niveau_biblio=="b" && ($explnum = show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= "<h3>$msg[explnum]</h3>".$explnum;
				$this->affichage_expl .= "<h3>$msg[explnum]</h3>".$explnum;
			} elseif (($explnum = show_explnum_per_notice($this->notice_id,0, ''))) {
				$ret .= "<h3>$msg[explnum]</h3>".$explnum;
				$this->affichage_expl .= "<h3>$msg[explnum]</h3>".$explnum;
			} 
		if (($autres_lectures = $this->autres_lectures($this->notice_id,$this->bulletin_id))) {
			$ret .= $autres_lectures;
		}
		$this->affichage_resa_expl = $ret ;
		return $ret ;
	} 
/*	
// fontion qui génère le bloc H3 + table des avis détaillés
function avis_detail () {
	global $dbh, $msg;
	global $action; // pour gérer l'affichage des avis en impression de panier
	
	$sql_avis = "select note, commentaire, sujet from avis where num_notice='$this->notice_id' and valide=1 order by note desc, id_avis desc";
	$r_avis = pmb_mysql_query($sql_avis, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$sql_avis."<br />");	
	
	$sql_avisnb = "select note, count(id_avis) as nb_by_note from avis where num_notice='$this->notice_id' and valide=1 group by note ";
	$r_avisnb = pmb_mysql_query($sql_avisnb, $dbh) or die ("<br />".pmb_mysql_error()."<br />".$sql_avisnb."<br />");
	while ($datanb=pmb_mysql_fetch_object($r_avisnb)) 
		$rowspan[$datanb->note]=$datanb->nb_by_note ;
		
	if (pmb_mysql_num_rows($r_avis)) {
		// comptage des avis par note afin de mettre les bons rowspan
		$odd_even=1;
		$note_conserve=-1;
		$ret="";
		while (($data=pmb_mysql_fetch_object($r_avis))) { 
			// on affiche les résultats 
			if ($note_conserve!=$data->note) {
				if ($odd_even==0) {
					$pair_impair="odd";
					$odd_even=1;
				} else if ($odd_even==1) {
					$pair_impair="even";
					$odd_even=0;
				}
				$categ_avis=$msg['avis_detail_note_'.$data->note];
				$note_conserve=$data->note;
				$tr_javascript=" class='$pair_impair' ";
				$ret .= "<tr $tr_javascript>";
				$ret .= "<td class='avis_detail_note_".$data->note."' width='15%' rowspan='".$rowspan[$data->note]."'>".$categ_avis."</td>";
			} else {
				$categ_avis='&nbsp;';
				$ret .= "<tr $tr_javascript>";
			}
			    
			$ret .= "<td class='avis_detail_commentaire_".$data->note."'>".$data->commentaire."
					<br />
					<span class='avis_detail_signature'>".$data->sujet."</span></td>";    		
			$ret .= "</tr>\n";
			
		}

		if ($action=="print") {
			$ret = "<h3 class='avis_detail'>".$msg['avis_detail']."
				</h3>
				<table style='width:100%;'>".$ret."</table>";
		} else {
			$ret = "<h3 class='avis_detail'>".$msg['avis_detail']."
					<span class='lien_ajout_avis'> : 
						<a href='#' onclick=\"open('avis.php?todo=add&noticeid=$this->notice_id','avis','width=600,height=290,scrollbars=yes,resizable=yes'); return false;\">".str_replace("!!nb_avis!!",$this->avis_qte,$msg['avis_detail_nb_ajt'])."</a>
					</span></h3>
					<table style='width:100%;'>".$ret."</table>";
		}
	} else {
		if ($action=="print") {
			$ret = "<h3 class='avis_detail'>".$msg['avis_detail_aucun_ajt']."
				</h3>";
		} else {
			$ret="<h3 class='avis_detail'>".$msg['avis_detail']."
					<span class='lien_ajout_avis'>
						<a href='#' onclick=\"open('avis.php?todo=add&noticeid=$this->notice_id','avis','width=600,height=290,scrollbars=yes,resizable=yes'); return false;\">".$msg['avis_detail_aucun_ajt']."</a>
					</span></h3>" ;
		}
	}
	return $ret;
}
*/	
	

}


// abiodoc >> generation des liens vers la boutique openstudio
class notice_affichage_abiodoc extends notice_affichage {
	
	var $explnum_shoplink="./../boutique/produit.php?ref=";
	var $abiodoc_app_val="";
	var $abiodoc_app_lib="";
	
	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_double($depliable=1, $premier='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $opac_notice_enrichment;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg;// l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;	   // pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		$this->result ="";
		if(!$this->notice_id) return;	

		$this->get_abiodoc_app();
		
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;
		$this->notice_childs = $this->genere_notice_childs();
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."images/basket_small_20x20.png\" border=\"0\" alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) )
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";	
		
		 //Avis
		if (($opac_avis_display_mode==0) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);	
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
	
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) {
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
			$case_a_cocher
			<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\" />";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
				$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);
		    	$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
			}
			$template.="
					<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
					</div>
					<br />
					</div>
					<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}elseif ($depliable == 2) {
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
			$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); $script_simili_search $script_expl_voisin_search return false;\">
			<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" hspace=\"3\" />";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
				$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
				$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);
				$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
			}
			$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		</div>
				<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		} else {
			$template="
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$this->notice_id."');
				}else{
					waitingAddthisLoaded('el".$this->notice_id."');
				}
			</script>
			<div id='el!!id!!Parent' class='parent'>$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
			$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
	 	$template.="!!CONTENU!!
					!!SUITE!!</div>";
	
	 	if($this->notice->niveau_biblio != "b"){
	 		$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
	 	}else {
	 		$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
	 	}
	 	
	 	if($opac_show_social_network){
	 		if($this->notice_header_without_html == ""){
	 			$this->do_header_without_html();
	 		}
	 		$template_in.="
			<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style '
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
			</div>";
	 	}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_tag</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD'){ 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	    		<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    		<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<!-- onglets_perso_list -->
	    		$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	    	$template_in.="<!-- onglets_perso_content -->";
	    } elseif($premier=="autre") { 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$onglet_perso=new notice_onglets();
			$template_in.=$onglet_perso->build_onglets($this->notice_id,$li_tags);
			
	    }else{ 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
				<!-- onglets_perso_list -->
		    	$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  		<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
	    	$template_in.="<!-- onglets_perso_content -->";
	    }
		
		
	    if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {	
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}	
			//si visionneuse active...	
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
	
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}else
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
 		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);		
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}
		
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result); 		
		$this->result = str_replace('!!SUITE!!', "", $this->result);
	} // fin genere_double($depliable=1, $premier='ISBD')
	
	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg; // l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		global $opac_visionneuse_allow;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if(!$this->notice_id) return;
		
		$this->get_abiodoc_app();
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed){
		if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";
		
		 //Avis
		if (($opac_avis_display_mode==0)&&(($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);	
		 
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) { 
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
				$case_a_cocher
	    		<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\"/>";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		</div>
				<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";			
		}elseif($depliable == 2){ 
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true);  $script_simili_search $script_expl_voisin_search return false;\">
	    		<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		</div>
				<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";						
		}else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    		$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}			
			$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";
					
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}	
	
		if($opac_show_social_network){	
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}	
			$template_in.="
		<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
		</div>";	
		}			
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		if($basket || $img_tag || $img_avis || $img_sugg || $opac_notice_enrichment){
			$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
			if ($basket) $template_in.="<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			if($opac_notice_enrichment){
				if($what =='ISBD') $template_in.="<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>";
				else $template_in.="<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>";
			}
			$template_in.="
	  			$li_tags
			<!-- onglets_perso_list -->
		</ul>
		<div class='row'></div>";	
		}
		
		if($what =='ISBD') $template_in.="		    	
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		else $template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>"
	  			; 	
		$template_in.="
			<!-- onglets_perso_content -->";
	  	if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
	  			
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
		
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
				
	} // fin genere_simple($depliable=1, $what='ISBD')
	
	function genere_ajax($aj_type_aff,$header_only_origine=0){
		global $msg;
		global $opac_url_base;
		global $icon_doc,$biblio_doc,$tdoc;
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_notices_depliable;
		global $opac_allow_simili_search;
		global $opac_draggable;
	
		$this->get_abiodoc_app();
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
	
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
	
		$this->genere_ajax_param($aj_type_aff,$header_only_origine);
	
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
		}else{
			$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
	
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		$script_simili_search = $this->get_simili_script();
			
		if($opac_notices_depliable == 2){
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
			$case_a_cocher<span class=\"notices_depliables\" param='".rawurlencode($this->notice_affichage_cmd)."'  onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param'));  $script_simili_search return false;\">
			<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
	    	}
			$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
				</div>
				<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";
		}else{
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\"><div class=\"notice-parent_abiodoc_col1\" >
			$case_a_cocher
			<img class='img_plus".$this->abiodoc_app_val."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" param='".rawurlencode($this->notice_affichage_cmd)."' onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); $script_simili_search return false;\" hspace=\"3\"/>";
			$template.="<span class='abiodoc_app".$this->abiodoc_app_val."'>".htmlentities($this->abiodoc_app_lib,ENT_QUOTES,$charset)."</span></div><div class=\"notice-parent_abiodoc_col2\" >";
			if ($icon) {
	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
	    	}
	    	$template.="
	    		<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		</div>
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
				</div>";
		}
		
		$template.="<a href=\"".$this->permalink."\" style=\"display:none;\">Permalink</a>
		$simili_search_script_all
		";
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$opac_notices_depliable);
	
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
		$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
	
	} // fin genere_ajax()
	
	function get_abiodoc_app() {
		global $dbh;
		$q="select v.notices_custom_integer,l.notices_custom_list_lib from notices_custom_values v join notices_custom_lists l on v.notices_custom_champ=l.notices_custom_champ and v.notices_custom_integer=l.notices_custom_list_value where v.notices_custom_champ='27' and v.notices_custom_origine='".$this->notice_id."'  limit 1 ";
		$r=pmb_mysql_query($q, $dbh);
		$result=array();
		if (pmb_mysql_num_rows($r)) {
			$this->abiodoc_app_val= pmb_mysql_result($r,0,0);
			$this->abiodoc_app_lib= pmb_mysql_result($r,0,1);
		}
	}
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {
		
		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_show_links_invisible_docnums;
		global $opac_url_base;
		global $charset;
		
		$this->notice_header="";
		if(!$this->notice_id) return;
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
		//Champs personnalisés à ajouter au réduit 
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
				$perso_voulu_aff=trim($perso_voulu_aff);
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;
		}

		$this->notice_header .= $aff_bullperio_title;
		
		//$this->notice_header_without_html = $this->notice_header;	
		//$this->notice_header = "<span class='header_title'>".$this->notice_header."</span>";	
				
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		
		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;
		
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		//$this->notice_header_without_html .= $notice_header_suite ;
		//$this->notice_header .= $notice_header_suite."</span>"  ;	
		//Un  span de trop ?
		$this->notice_header .= $notice_header_suite;

		if ($this->notice->niveau_biblio =='m' || $this->notice->niveau_biblio =='s') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $this->notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $this->notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressources électroniques			
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" />";
			$this->notice_header_doclink .= "</a></span>";
		} 
		$join_acces_explnum = "";
		if (!$opac_show_links_invisible_docnums) {
			if (!is_null($this->dom_3)) {
				$join_acces_explnum = $this->dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
			} else {
				$join_acces_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)":"").")";
			}
		}
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype, explnum_statut FROM explnum $join_acces_explnum JOIN bulletins ON bulletins.bulletin_id = explnum.explnum_bulletin WHERE bulletins.num_notice = ".$this->notice_id." order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype, explnum_statut FROM explnum $join_acces_explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);
		
		if ($opac_show_links_invisible_docnums || (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				
				//vérification de la visibilité si non connecté
				if(!$_SESSION['id_empr_session'] && $opac_show_links_invisible_docnums){
					$visible = false;
					if (!is_null($this->dom_3)) {
						$right = $this->dom_3->getRights(0,$explnumrow->explnum_id,16);
						if($right == 16){
							$visible = true;
						}
					}else{
						$sql = "select explnum_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac= 1 and explnum_visible_opac_abon = 0 and explnum_id = ".$explnumrow->explnum_id;
						if(pmb_mysql_num_rows(pmb_mysql_query($sql))){
							$visible = true;
						}
					}
				}
				
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}
				$this->notice_header_doclink .= "&nbsp;<span>";
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($explnumrow->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
						function sendToVisionneuse_".$explnumrow->explnum_id."(){
								open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt' >";
						
				}else{
					if ($explnumrow->explnum_statut==1) {	//document payant
						$this->notice_header_doclink .= "<a href=\"".$this->explnum_shoplink.$explnumrow->explnum_id."\" target=\"__LINK__\">";
						$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/icon_k_16x16.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
					} else {								//document gratuit
						$this->notice_header_doclink .= "<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
						$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
					}
				}
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle, ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle, ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" />";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;
		
		
		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
		
	} // fin do_header()

			
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		global $opac_show_links_invisible_docnums;
		global $msg;
		$ret='';
		if ($opac_show_links_invisible_docnums || (is_null($this->docnum) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)) {
			if ($this->notice->niveau_biblio=="b" && ($explnum = $this->show_explnum_per_notice(0, $this->bulletin_id))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			} elseif (($explnum = $this->show_explnum_per_notice($this->notice_id,0))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			}
		}		 
		return $ret;
	} // fin aff_explnum ()
	
		
	// fonction retournant les infos d'exemplaires numeriques pour une notice ou un bulletin donne
	function show_explnum_per_notice($no_notice, $no_bulletin) {
		
		// params :
		// $link_expl= lien associe a l'exemplaire avec !!explnum_id!! a mettre à jour
		global $dbh;
		global $charset;
		global $opac_url_base ;
		
		if (!$no_notice && !$no_bulletin) return "";
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype() ;
		
		// récupération du nombre d'exemplaires
		$requete = "SELECT * FROM explnum WHERE ";
		if ($no_notice && !$no_bulletin) {
			$requete .= "explnum_notice='$no_notice' ";
		} elseif (!$no_notice && $no_bulletin) {
			$requete .= "explnum_bulletin='$no_bulletin' ";
		} elseif ($no_notice && $no_bulletin) {
			$requete .= "explnum_bulletin='$no_bulletin' or explnum_notice='$no_notice' ";
		}
		$requete .= " order by explnum_mimetype, explnum_id ";
		$res = pmb_mysql_query($requete, $dbh);
		$nb_ex = pmb_mysql_num_rows($res);
		
		if($nb_ex) {
			// on récupère les données des exemplaires
			$i = 1 ;
			while (($expl = pmb_mysql_fetch_object($res))) {
				if ($i==1) {
					$ligne="<tr><td class='docnum' width='33%'>!!1!!</td><td class='docnum' width='33%'>!!2!!</td><td class='docnum' width='33%'>!!3!!</td></tr>" ;
				}
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset);
				
				if ($expl->explnum_vignette) {
					$obj="<img src='".$opac_url_base."vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0' />";
				} else {// trouver l'icone correspondant au mime_type
					$obj="<img src='".$opac_url_base."images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0' />";
				}		
				$expl_liste_obj = "<center>";
				
				if ($expl->explnum_statut==1) {	//document payant
					$expl_liste_obj .= "<a href='".$this->explnum_shoplink.$expl->explnum_id."' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
				} else {						//document gratuit
					$expl_liste_obj .= "<a href='".$opac_url_base."doc_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
				}
				
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) {
					$explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
				} elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) {
					$explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
				} else {
					$explmime_nom = $expl->explnum_mimetype ;
				}
				
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				$expl_liste_obj .= "</center>";
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
				}
			}
			if (!$ligne_finale) $ligne_finale = $ligne ;
				elseif ($i!=1) $ligne_finale .= $ligne ;
			$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
			$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
			
		} else return "";
		$entry .= "<table class='docnum'>$ligne_finale</table>";
		return $entry;
	
	}
	
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		global $opac_map_activate;
	
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
	
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
	
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
	
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
	
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
	
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
	
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
	
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		//if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		$ret .= $perso_aff ;
	
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856' type=\"external_url_notice\">".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856' type=\"external_url_notice\">".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				//$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()
		

}

// pour affichage du descriptif du produit dans la boutique
class notice_affichage_abiodoc_boutique extends notice_affichage {
	
	var $explnum_shoplink="./../boutique/produit.php?ref=";
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {
		
		global $charset;
		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg ;
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalises ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'editeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
		//Champs personnalises a ajouter au reduit 
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
		
		//Si c'est un périodique, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2)  {
			 $aff_perio_title="<i>in ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		$this->notice_header .= $aff_bullperio_title;
		$this->notice_header_without_html = $this->notice_header;	
		$this->notice_header = "<span class='header_title'>".$this->notice_header."</span>";	

		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;		
		
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		$this->notice_header_without_html .= $notice_header_suite ;
		$this->notice_header .= $notice_header_suite."</span>"  ;	
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
		
	}

	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} 
	
			
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		
		return "";
	}
		
	function aff_resa_expl () {
		
		return "";
	}
	
	function show_explnum_per_notice($no_notice, $no_bulletin) {
		return "";
	
	}

}

// prao >> authentification sur kportal pour les resas
class notice_affichage_prao extends notice_affichage {
	
	function aff_resa_expl() {

		global $opac_resa_popup;
		
		parent::aff_resa_expl();
		$this->affichage_resa_expl=str_replace("do_resa.php?", "do_resa_prao.php?",$this->affichage_resa_expl);
		$ret=$this->affichage_resa_expl;
		return $ret; 
	}

	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		
		$this->notice_public= $this->genere_in_perio ();
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_public.=$this->parents;
			
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
	
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);			
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);			
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		
		// collection
		if(($this->notice->typdoc != "d" /*Article*/ ) && ($this->notice->typdoc != "a" /*Ouvrage*/ ) && ($this->notice->typdoc != "k" /*Guide*/ ) && ($this->notice->typdoc != "j" /*Etude*/ ) && ($this->notice->typdoc != "s" /*Rapport*/ )){
			if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
			else $affnocoll = "";
			if($this->notice->subcoll_id) {
				$subcollection = new subcollection($this->notice->subcoll_id);
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
				$this->notice_public .=$affnocoll."</td></tr>";
			} elseif ($this->notice->coll_id) {
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
				$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
			}
		}
		
		
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		//Présentation
		if ($this->notice->ill && ($this->notice->typdoc != "a" /*Ouvrage*/ ) && ($this->notice->typdoc != "x" /*Outil pédagogique*/ ) && ($this->notice->typdoc != "e" /*Compte-rendu*/ )) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if ($this->notice->size && ($this->notice->typdoc != "a" /*Ouvrage*/ ) && ($this->notice->typdoc != "x" /*Outil pédagogique*/ ) && ($this->notice->typdoc != "e" /*Compte-rendu*/ ) && ($this->notice->typdoc != "b" /*Actes*/ ) && ($this->notice->typdoc != "v" /*Travaux universitaires*/ ) && ($this->notice->typdoc != "s" /*Rapport*/ ) && ($this->notice->typdoc != "m" /*document multimédia*/ )) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if ($this->notice->prix && ($this->notice->typdoc != "a" /*Ouvrage*/ ) && ($this->notice->typdoc != "x" /*Outil pédagogique*/ )) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		/*if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}*/
		
		if (!$short) $this->notice_public .= $this->aff_suite() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	} // fin do_public($short=0,$ex=1)

	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		$ret .= $perso_aff ;
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				//$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			//if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()
	
}

// MBA
class notice_affichage_mba extends notice_affichage {
	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		
		$this->notice_public="";
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_public.=$this->parents;
	
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
	
		//Complément du titre parallèle dans le Champ personalisé sstitre_parallele
		$sstitre_parallele="";
		$sstitre_parallele1="";
		if (!$this->p_perso->no_special_fields) {
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) {
					if($p["NAME"] == "sstitre_parallele") {
						$sstitre_parallele=$p["AFF"];						
					}	
					if($p["NAME"] == "titre_parallele") {
						$sstitre_parallele1=str_replace("/","<br>",$p["AFF"]);
					}					
				}
			}
		}
		if($sstitre_parallele)$sstitre_parallele=" : ".$sstitre_parallele;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>Titre parallèle :</span></td><td>".$this->notice->tit3.$sstitre_parallele."</td></tr>" ;
		
		if ($sstitre_parallele1) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>Autres titres parallèles :</span></td><td>".$sstitre_parallele1."</td></tr>" ;
				
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;

		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		
		// collection  
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>" ;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		}
			
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		
		// zone de la collation
		$collation=$this->notice->npages;
		if($collation && $this->notice->ill)$collation.=" : ";
		$collation.=$this->notice->ill;
		if($collation && $this->notice->size)$collation.=", ";
		$collation.=$this->notice->size;
		
		if($collation) $this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_collation']."</span></td><td>".$collation."</td></tr>";
		
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
		
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if ($this->notice->prix) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_public .= $this->aff_suite() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	}	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// Permalink avec Id
		if ($opac_permalink) $ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
		
		//Champs personalisés
		$perso_aff = $perso_aff_suite = $titre = $loc = $etablissement = $date = $lieu_ed = "" ;
		if (!$this->p_perso->no_special_fields) {
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);		
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) {
					if($p["NAME"] == "t_d_f_titre")$titre=$p["AFF"];	
					elseif($p["NAME"] == "t_d_f_lieu_etabl")$lieu_ed=$p["AFF"];					
					elseif($p["NAME"] == "t_d_f_date")$date=$p["AFF"];	
					elseif($p["NAME"] == "sstitre_parallele");//rien, il est affiché après le titre paralelle
					elseif($p["NAME"] == "titre_parallele");//rien, les autres titres parralleles sont affichés après le titre paralelle
					else $perso_aff_suite.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$p["TITRE"]."</span></td><td>".$p["AFF"]."</td></tr>";
				}
			}
		}
		
		if($titre){
			$perso_aff= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_titre_de_forme']."</span></td><td>".$titre."</td></tr>" ;
			$lieu=explode("/",$lieu_ed);
			if(count($lieu)){
				for($l=0;$l<count($lieu);$l++){
					$perso_aff.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'></span></td><td>".$lieu[$l]."</td></tr>" ;
				}
			}
			
			if($date){
				$perso_aff.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'></span></td><td>".$date."</td></tr>" ;
			}
		}
		$ret .=$perso_aff.$perso_aff_suite;
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} 	

}

/*
 * Classe d'affichage pour Philip Morris
 */
class notice_affichage_pmi extends notice_affichage {

	var $collectivite_tous = "";
	var $customs = array();
	
	/*
	 * Affichage public
	 */
	function do_public($short=0,$ex=1){
		global $dbh;
			global $msg;
			global $tdoc;
			global $charset;
			global $memo_notice;
			
			$this->notice_public="";
			if(!$this->notice_id) return;
			
			// Chargement des champs persos
			if(!$this->customs) $this->customs = $this->load_custom_fields();
	
			// Notices parentes
			$this->notice_public.=$this->parents;
		
			$this->notice_public .= "<table>";
			// constitution de la mention de titre
					
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
			
			if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
			$this->notice_public.="</span></td></tr>";
			
			if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
			if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
			
			//Responsabilités
				
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			if ($this->collectivite_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['collectivites_search'].":</span></td><td>".$this->collectivite_tous."</td></tr>";
			
			// zone de l'éditeur 
			if ($this->notice->year)
				$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
	
			// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
			$this->notice_public .= $annee ;
			
			//Subtype
			if($this->customs["SUBTYPE"]) $this->notice_public .= $this->customs["SUBTYPE"] ;
			
			if (!$short) $this->notice_public .= $this->aff_suite_public(); 
			else $this->notice_public.=$this->genere_in_perio();
		
			$this->notice_public.="</table>\n";
			
			//etat des collections
			if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
			
			// exemplaires, résas et compagnie
			if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
		
			return;	
	}
	
	
	// fonction d'affichage de la suite PUBLIC 
	function aff_suite_public() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search,  $opac_url_base;
		
		$ret .= $this->genere_in_perio () ;
				
		/** toutes indexations **/
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";

		if ($ret_index) 
			$ret.=$ret_index;
			
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
		
		$this->affichage_suite = $ret ;
		return $ret ;
	} 
	
	// fonction d'affichage de la suite PUBLIC 
	function aff_suite_isbd() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		$ret .= $this->genere_in_perio () ;
				
		/** toutes indexations **/
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";

		if ($ret_index) 
			$ret.=$ret_index;
			
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
		
		// ISBN ou NO. commercial
		if ($this->notice->code) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
			
		//Persos
		if($this->customs["DISCO"]) $ret .= $this->customs["DISCO"];
		if($this->customs["PUBMED"]) $ret .= $this->customs["PUBMED"];
		if($this->customs["DOI"]) $ret .= $this->customs["DOI"];
		
		// Permalink avec Id
		if ($opac_permalink) $ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
	
		$this->affichage_suite = $ret ;
		return $ret ;
	} 
	
	/*
	 * Chargement des champs persos
	 */
	function load_custom_fields(){
		
		$custom_fields = array();
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]){
					$value = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";					
					if ($p["NAME"] == "pmi_doi_identifier"){
						$custom_fields["DOI"] = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td><a href='http://dx.doi.org/".$p["AFF"]."' target='_BLANK'>http://dx.doi.org/".$p["AFF"]."</a></td></tr>";
					}
					if ($p["NAME"] == "subtype"){
						$custom_fields["SUBTYPE"] = $value;
					}
					if ($p["NAME"] == "publishedOR"){
						$custom_fields["REPOS"] = $value;
					}	
					if ($p["NAME"] == "pmi_published_by_pmi"){
						$custom_fields["PMI_PUBLISHED"] = $value;
					}
					if ($p["NAME"] == "r_object_id"){
						$custom_fields["DISCO"] = $value;
					}
					if ($p["NAME"] == "pmi_xref_dbase_id"){
						$custom_fields["PUBMED"] = $value;
					}
				}    
			}
		}
		
		return $custom_fields;
	}
	
	/*
	 * Récuperation des autorites
	 */
	function fetch_auteurs() {
		global $fonction_auteur;
		global $dbh ;
		global $opac_url_base ;
	
		$this->responsabilites  = array() ;
		$auteurs = array() ;
		
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;
		
		$rqt = "SELECT author_id, responsability_fonction, responsability_type, author_type,author_name, author_rejete, author_type, author_date, author_see, author_web ";
		$rqt.= "FROM responsability, authors ";
		$rqt.= "WHERE responsability_notice='".$this->notice_id."' AND responsability_author=author_id ";
		$rqt.= "ORDER BY responsability_type, responsability_ordre " ;
		$res_sql = pmb_mysql_query($rqt, $dbh);
		while (($notice=pmb_mysql_fetch_object($res_sql))) {
			$responsabilites[] = $notice->responsability_type ;
			$info_bulle="";
			if($notice->author_type==72 || $notice->author_type==71) {			
				$congres=new auteur($notice->author_id);
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
			// URL de l'auteur
			if ($notice->author_web) $auteur_web_link = " <a href='$notice->author_web' target='_blank'><img src='".$opac_url_base."images/globe.gif' border='0'/></a>";
			else $auteur_web_link = "" ;
			if (!$this->to_print) $auteur_isbd .= $auteur_web_link ;
			$auteur_isbd = inslink($auteur_isbd, str_replace("!!id!!", $notice->author_id, $this->lien_rech_auteur),$info_bulle) ;
			if ($notice->responsability_fonction) $auteur_isbd .= ", ".$fonction_auteur[$notice->responsability_fonction] ;
			$auteurs[] = array( 
					'id' => $notice->author_id,
					'fonction' => $notice->responsability_fonction,
					'responsability' => $notice->responsability_type,
					'name' => $notice->author_name,
					'rejete' => $notice->author_rejete,
					'date' => $notice->author_date,
					'type' => $notice->author_type,
					'fonction_aff' => $fonction_auteur[$notice->responsability_fonction],
					'auteur_isbd' => $auteur_isbd,
					'auteur_titre' => $auteur_titre
					) ;
		}
		if (!$responsabilites) $responsabilites = array();
		if (!$auteurs) $auteurs = array();
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;
		
		// $this->auteurs_principaux 
		// on ne prend que le auteur_titre = "Prénom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			$aut1_libelle = array();
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;			
				if($auteur_1["type"]==72 || $auteur_1["type"]==72) {			
					$congres=new auteur($auteur_1["id"]);
					$aut1_libelle[]=$congres->display;
				} else {
					$aut1_libelle[]= $auteur_1["auteur_titre"];
				}	
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
		}
	
		// $this->auteurs_tous
		$mention_resp = array() ;
		$collectivite_resp = array();
		$congres_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			if($this->responsabilites["auteurs"][$as]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else if($this->responsabilites["auteurs"][$as]["type"]==71){
				$collectivite_resp[] = $mention_resp_lib;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else if($this->responsabilites["auteurs"][$indice]["type"]==71){
				$collectivite_resp[] = $mention_resp_lib;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_2["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else if($this->responsabilites["auteurs"][$indice]["type"]==71){
				$collectivite_resp[] = $mention_resp_lib;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}		
		}
		
		
		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
		else $this->auteurs_tous ="" ;
		
		$libelle_collectivite_resp = implode ("; ",$collectivite_resp) ;
		if ($libelle_collectivite_resp) $this->collectivite_tous = $libelle_collectivite_resp ;
		else $this->collectivite_tous ="" ;
		
		$libelle_congres_resp = implode ("; ",$congres_resp) ;
		if ($libelle_congres_resp) $this->congres_tous = $libelle_congres_resp ;
		else $this->congres_tous ="" ;
		
	}
	
	/*
	 * Affichage ISBD
	 */
	function do_isbd($short=0,$ex=1) {
		
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
		global $memo_notice;
		
		$this->notice_isbd="";
		if(!$this->notice_id) return;
		
		// Chargement des champs persos
		if(!$this->customs) $this->customs = $this->load_custom_fields();
		
		// Notices parentes
		$this->notice_isbd.=$this->parents;
	
		$this->notice_isbd .= "<table>";
		// constitution de la mention de titre
				
		$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_isbd .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_isbd .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_isbd.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		//Auteurs	
		if ($this->auteurs_tous) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		if ($this->collectivite_tous) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['collectivites_search'].":</span></td><td>".$this->collectivite_tous."</td></tr>";
		
		
		//PMI-AUTHORED
		$this->notice_isbd .= $this->customs["PMI_PUBLISHED"];
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
	
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_isbd .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}					
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_isbd .= $annee ;
		
		//Open Repository
		$this->notice_isbd .= $this->customs["REPOS"];
		
		//Subtype
		$this->notice_isbd .= $this->customs["SUBTYPE"];
		
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill) $this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
			
		// langues
		if (count($this->langues)) {
			$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_isbd .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_isbd.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_isbd .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_isbd .= $this->aff_suite_isbd(); 
		else $this->notice_isbd.=$this->genere_in_perio();
	
		$this->notice_isbd.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_isbd.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;	
	
	}	
	
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		
		global $msg;
		$ret='';
		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)){
			if ($this->notice->niveau_biblio=="b" && ($explnum = $this->show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			} elseif (($explnum = $this->show_explnum_per_notice($this->notice_id,0, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			}
		}		 
		return $ret;
	}
	
	// fonction retournant les infos d'exemplaires numériques pour une notice ou un bulletin donné
	function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='') {
		
		// params :
		// $link_expl= lien associé à l'exemplaire avec !!explnum_id!! à mettre à jour
		global $dbh;
		global $charset;
		global $opac_url_base ;
		
		if (!$no_notice && !$no_bulletin) return "";
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype() ;
	
		// récupération du nombre d'exemplaires
		$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, explnum_vignette, explnum_nomfichier, explnum_extfichier 
		FROM explnum left join explnum_location on (num_explnum=explnum_id) WHERE ";
		if ($no_notice && !$no_bulletin) $requete .= "explnum_notice='$no_notice' ";
		elseif (!$no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' ";
		elseif ($no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' or explnum_notice='$no_notice' ";
		$requete .= " and (num_location='".$_SESSION['empr_location']."' or num_location is null) order by explnum_mimetype, explnum_id ";
		$res = pmb_mysql_query($requete, $dbh);
		$nb_ex = pmb_mysql_num_rows($res);
		
		if($nb_ex) {
			// on récupère les données des exemplaires
			$i = 1 ;
			global $search_terms;
			
			while (($expl = pmb_mysql_fetch_object($res))) {
				if ($i==1) $ligne="<tr><td class='docnum' width='33%'>!!1!!</td><td class='docnum' width='33%'>!!2!!</td><td class='docnum' width='33%'>!!3!!</td></tr>" ;
				if ($link_expl) {
					$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
					$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
					$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
					} 
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."/vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0' />";
					else // trouver l'icone correspondant au mime_type
						$obj="<img src='".$opac_url_base."/images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0' />";		
				$expl_liste_obj = "<center>";
				
				$words_to_find="";
				if(($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
				}
				$expl_liste_obj .= "<a href='".$opac_url_base."/doc_num.php?explnum_id=$expl->explnum_id$words_to_find' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
				
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
					elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
						else $explmime_nom = $expl->explnum_mimetype ;
				
				
				if ($tlink) {
					$expl_liste_obj .= "<a href='$tlink'>";
					$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
					} else {
						$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
						}
				$expl_liste_obj .= "</center>";
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
					}
				}
			if (!$ligne_finale) $ligne_finale = $ligne ;
				elseif ($i!=1) $ligne_finale .= $ligne ;
			$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
			$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
			
			} else return "";
		$entry .= "<table class='docnum'>$ligne_finale</table>";
		return $entry;
	
	}
	
// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg ;
		global $memo_notice;
		
		$this->notice_header="";
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
		//Champs personalisés à ajouter au réduit 
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2)  {
			 $aff_perio_title="<i>in ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		$this->notice_header .= $aff_bullperio_title;
		$this->notice_header_without_html = $this->notice_header;	
		$this->notice_header = "<span class='header_title'>".$this->notice_header."</span>";	
				
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		$this->notice_header_without_html .= $notice_header_suite ;
		$this->notice_header .= $notice_header_suite."</span>"  ;	
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
		
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressource électroniques
			$this->notice_header .= "&nbsp;<span><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header .= " alt=\"";
			$this->notice_header .= $info_bulle;
			$this->notice_header .= "\" title=\"";
			$this->notice_header .= $info_bulle;
			$this->notice_header .= "\" />";
			$this->notice_header .= "</a></span>";
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins join explnum_location on (num_explnum=explnum_id and num_location='".$_SESSION['empr_location']."' ) WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum join explnum_location on (num_explnum=explnum_id and num_location='".$_SESSION['empr_location']."' ) WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header .= "&nbsp;<span>";		
				$this->notice_header .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				$this->notice_header .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header .= " alt=\"";
				$this->notice_header .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header .= "\" title=\"";
				$this->notice_header .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header .= "\" />";
				$this->notice_header .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\" />";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;		
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
	}
	
}

/*
 * Classe d'affichage OPAC pour supagro
 */
class notice_affichage_supagro extends notice_affichage {
	
	
	// génération de l'isbd----------------------------------------------------
	function do_isbd($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
		global $memo_notice;
		
		$this->notice_isbd="";
		if(!$this->notice_id) return;
		
		// Notices parentes
		$this->notice_isbd.=$this->parents;
		
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$serie_temp .= inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
			if($this->notice->tnvol) $serie_temp .= ',&nbsp;'.$this->notice->tnvol;
		}
		if ($serie_temp) $this->notice_isbd .= $serie_temp.".&nbsp;".$this->notice->tit1 ;
		else $this->notice_isbd .= $this->notice->tit1;
	
		
		if ($this->notice->tit3) $this->notice_isbd .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->tit4) $this->notice_isbd .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit2) $this->notice_isbd .= "&nbsp;; ".$this->notice->tit2 ;
		if($tdoc->table[$this->notice->typdoc]) $this->notice_isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		
		
		if ($this->auteurs_tous) $this->notice_isbd .= " / ".$this->auteurs_tous;
		if ($this->congres_tous) $this->notice_isbd .= " / ".$this->congres_tous;
		
		// mention d'édition
		if($this->notice->mention_edition) $this->notice_isbd .= " &nbsp;. -&nbsp; ".$this->notice->mention_edition;
		
		// zone de collection et éditeur
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->publisher, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection));
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->parent, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection));
		} elseif ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$editeurs .= inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
		}
		
		if($this->notice->ed2_id) {
			$editeur = new publisher($this->notice->ed2_id);
			$editeurs ? $editeurs .= '&nbsp;: '.inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur)) : $editeurs = inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur));
		}
	
		if($this->notice->year) $editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
		elseif ($this->notice->niveau_biblio == 'm' && $this->notice->niveau_hierar == 0) 
				$editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
	
		if($editeurs) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$editeurs";
		
		// zone de la collation
		if($this->notice->npages) $collation = $this->notice->npages;
		if($this->notice->ill) $collation .= '&nbsp;: '.$this->notice->ill;
		if($this->notice->size) $collation .= '&nbsp;; '.$this->notice->size;
		if($this->notice->accomp) $collation .= '&nbsp;+ '.$this->notice->accomp;
		if($collation) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$collation";
		if($collections) {
			if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
			$this->notice_isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}
	
		$this->notice_isbd .= '.';
			
		// ISBN ou NO. commercial
		if($this->notice->code) {
			if(isISBN($this->notice->code)) $zoneISBN = '<b>ISBN</b>&nbsp;: ';
			else $zoneISBN .= '<b>'.$msg["issn"].'</b>&nbsp;: ';
			$zoneISBN .= $this->notice->code;
		}
		if($this->notice->prix) {
			if($this->notice->code) $zoneISBN .= '&nbsp;: '.$this->notice->prix;
			else { 
				if ($zoneISBN) $zoneISBN .= '&nbsp; '.$this->notice->prix;
				else $zoneISBN = $this->notice->prix;
			}
		}
		if($zoneISBN) $this->notice_isbd .= "<br />".$zoneISBN;
		
		// note générale
		if($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if($zoneNote) $this->notice_isbd .= "<br />".$zoneNote;
				
	
		// langues
		if(count($this->langues)) {
			$langues = "<span class='etiq_champ'>${msg[537]}</span>&nbsp;: ".$this->construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <span class='etiq_champ'>${msg[711]}</span>&nbsp;: ".$this->construit_liste_langues($this->languesorg);
		}
		if ($langues) $this->notice_isbd .= "<br />".$langues."<br />" ;
		
		//Champs personalisés
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $this->notice_isbd .="<span class='etiq_champ'>".$p["TITRE"]."</span>".$p["AFF"]."<br />";
			}
		}
				
		if (!$short) {
			$this->notice_isbd .="<table>";
			$this->notice_isbd .= $this->aff_suite() ;
			$this->notice_isbd .="</table>";
		} else {
			$this->notice_isbd.=$this->genere_in_perio();
		}
	
		//etat des collections
		if ($this->notice->niveau_biblio=='s'&&$this->notice->niveau_hierar==1) $this->notice_isbd.=$this->affichage_etat_collections();	
	
		//Notices liées
		// ajoutées en dehors de l'onglet PUBLIC ailleurs
		
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	}	
	
	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		
		$this->notice_public="";
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_public.=$this->parents;
	
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $this->notice_public .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$p["TITRE"]."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		
		
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
	
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		
		// collection  
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>" ;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		}
		
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if ($this->notice->size) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if ($this->notice->prix) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_public .= $this->aff_suite() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	}	
		
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		
		$ret .= $this->genere_in_perio () ;		
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// Permalink avec Id
		if ($opac_permalink) $ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
	
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		
		$this->affichage_suite = $ret ;
		return $ret ;
	} 
}
	
/*
 * Classe d'affichage pour le CRIPS  
 */
class notice_affichage_crips extends notice_affichage {	
	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	var $customs = array();

	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_double($depliable=1, $premier='ISBD') {
		global $msg;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $opac_notice_enrichment;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg;// l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;	   // pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;

		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		$this->result ="";
		if(!$this->notice_id) return;	
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;
		$this->notice_childs = $this->genere_notice_childs();
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."images/basket_small_20x20.png\" border=\"0\" alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) )
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";	
		
		 //Avis
		if (($opac_avis_display_mode==0) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);	
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
	
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";
		}
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) {
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_".$this->notice->typdoc."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\" />";
//			if ($icon) {
//    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
//    			//$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
//    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
    	}elseif ($depliable == 2) {
    		$template="$simili_search_script_all
    			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
    			$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\">
    			<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" hspace=\"3\" />";
//    		if ($icon) {
//    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);
//    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
//    		}
    		$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		} else {
			$template="
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$this->notice_id."');
				}else{
					waitingAddthisLoaded('el".$this->notice_id."');
			}
			</script>
			<div id=\"el!!id!!Parent\" class=\"parent\">$case_a_cocher";
//			if ($icon) {
//    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
//    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
//    		}
    		$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
	 	$template.="!!CONTENU!!
					!!SUITE!!</div>";
	
	 	if($this->notice->niveau_biblio != "b"){
	 		$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
	 	}else {
	 		$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
	 	}
	 	
		if($opac_show_social_network){		
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}
			$template_in.="
			<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
			</div>";	
		}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD') { 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	    		<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    		<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<!-- onglets_perso_list -->
	    		$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	    	$template_in.="<!-- onglets_perso_content -->";
	    } elseif($premier=="autre") { 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$onglet_perso=new notice_onglets();
			$template_in.=$onglet_perso->build_onglets($this->notice_id,$li_tags);
			
	    }else {
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
				<!-- onglets_perso_list -->
		    	$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  		<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
	    	$template_in.="<!-- onglets_perso_content -->";
	    }
		
		
		if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
	
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}else
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);


		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result); 		
		$this->result = str_replace('!!SUITE!!', "", $this->result);
	} // fin genere_double($depliable=1, $premier='ISBD')
	
	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg; 
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg; // l'utilisateur a-t-il le droit de faire une suggestion
		global $opac_avis_display_mode;
		global $opac_allow_simili_search;
		global $opac_draggable;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if(!$this->notice_id) return;
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";
		
		 //Avis
		if (($opac_avis_display_mode==0)&&(($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);
		 
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";
		}
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) { 
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_".$this->notice->typdoc."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			//$template.="<img  src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";
    	}elseif($depliable == 2){
    		$template="$simili_search_script_all
    			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
    			$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true);  $script_simili_search return false;\">
    			<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
    		if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);
    			//$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";
		} else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    		$case_a_cocher";
//			if ($icon) {
//    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
//    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
//    		}			
    		$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";
		
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
		
		if($opac_show_social_network){	
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}	
			$template_in.="
		<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
		</div>";	
		}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		if($basket || $img_tag || $img_avis || $img_sugg || $opac_notice_enrichment){
			$template_in.="
				<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
			if ($basket) $template_in.="<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			if($opac_notice_enrichment){
				if($what =='ISBD') $template_in.="<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>";
				else $template_in.="<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>";
			}
			$template_in.="
	  			$li_tags
			<!-- onglets_perso_list -->
			</ul>
			<div class='row'></div>";
		} 

		if($what =='ISBD') $template_in.="		    	
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		else $template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>"
	  			; 	
		$template_in.="
			<!-- onglets_perso_content -->";
		if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
	  	
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
		
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
				
	} // fin genere_simple($depliable=1, $what='ISBD')
	
	function genere_ajax($aj_type_aff,$header_only_origine=0){
		global $msg; 
		global $opac_url_base;
		global $icon_doc,$biblio_doc,$tdoc;
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_notices_depliable;		
		global $opac_allow_simili_search;
		global $opac_draggable;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
				
		$this->genere_ajax_param($aj_type_aff,$header_only_origine);
		
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
		}else{
			$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
		
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		$script_simili_search = $this->get_simili_script();
			
		if($opac_notices_depliable == 2){
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" param='".rawurlencode($this->notice_affichage_cmd)."'  onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param'));  $script_simili_search return false;\">
		    	<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
//			if ($icon) {
//	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
//	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
//	    	}
	    	$template.="		
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
		    	<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";
		}else{
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
		    	<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" param='".rawurlencode($this->notice_affichage_cmd)."' onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); $script_simili_search return false;\" hspace=\"3\"/>";
//			if ($icon) {
//	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
//	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
//	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
//	    	}
	    	$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
		    	<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";	    	
		}
		
	    
		$template.="<a href=\"".$this->permalink."\" style=\"display:none;\">Permalink</a>
			$simili_search_script_all
		";
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$opac_notices_depliable);	
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
				
	} // fin genere_ajax()
	
// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg ;
		global $memo_notice;

		$this->notice_header="";
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
		//Champs personalisés à ajouter au réduit 
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;	
		}

		$this->notice_header .= $aff_bullperio_title;
//		$this->notice_header_without_html = $this->notice_header;
		$this->notice_header = "<span class='header_title'>".$this->notice_header."</span>";
		
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
//		$this->notice_header_without_html .= $notice_header_suite ;
//		$this->notice_header .= $notice_header_suite."</span>"  ;
		//Un  span de trop ?	
		$this->notice_header .= $notice_header_suite;	
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressource électroniques
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."styles/crips/images/oeil.png\" border=\"0\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" />";
			$this->notice_header_doclink .= "</a></span>";
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( !$this->notice->lien && (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			while($explnumrow = pmb_mysql_fetch_object($explnums)){
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";
				$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				$this->notice_header_doclink .= "<img src=\"./styles/crips/images/oeil.png\" border=\"0\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			}			
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;		
		
		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
	}
	
	function do_image(&$entree,$depliable) {
		global $charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg;
		global $opac_url_base ;
		global $msg;
				
		if ($this->notice->code || $this->notice->thumbnail_url) {
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $this->notice->thumbnail_url)) {
				$code_chiffre = pmb_preg_replace('/-|\.| /', '', $this->notice->code);
				$url_image = $opac_book_pics_url ;
				$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($this->notice->thumbnail_url) ;
				
				$hauteur_vig = "";	
				if(strpos($this->notice->thumbnail_url,"AFFICHES_VIGNETTES") !== false){
					$hauteur_vig = "";				
				} else $hauteur_vig= " height=\"150px\" ";
				if ($depliable) $image = "<img src='".$opac_url_base."images/vide.png' hspace='4' vspace='2' $hauteur_vig isbn='".$code_chiffre."' url_image='".$url_image."' border='1px solid #ccccff' vigurl=\"".$this->notice->thumbnail_url."\"  alt='".$msg["opac_notice_vignette_alt"]."'/>";
				else {
					if ($this->notice->thumbnail_url) {
						$url_image_ok=$this->notice->thumbnail_url;
						$title_image_ok="";
					} else {
						$url_image_ok = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
						$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
					}
					if(!trim($title_image_ok)){
						$title_image_ok = htmlentities($this->notice->tit1, ENT_QUOTES, $charset);
					}
					$image = "<img src='".$url_image_ok."' title=\"".$title_image_ok."\" $hauteur_vig align='right' hspace='4' vspace='2'  alt='".$msg["opac_notice_vignette_alt"]."'/>";
				}
			} else $image="" ;
			if ($image) {
				$entree = "<table width='100%'><tr><td>$image</td></tr><tr><td>$entree</td></tr></table>" ;
			} else {
				$entree = "<table width='100%'><tr><td>$entree</td></tr></table>" ;
			}
				
		} else {
			$entree = "<table width='100%'><tr><td>$entree</td></tr></table>" ;
		}
	}
	
	// génération de l'isbd----------------------------------------------------
	function do_isbd($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
		global $memo_notice;
		
		$this->notice_isbd="";
		if(!$this->notice_id) return;
		
		// Chargement des champs persos
		if(!$this->customs) $this->customs = $this->load_custom_fields();
		
		// Notices parentes
		$this->notice_isbd.=$this->parents;
		
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$serie_temp .= inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
			if($this->notice->tnvol) $serie_temp .= ',&nbsp;'.$this->notice->tnvol;
		}
		if ($serie_temp) $this->notice_isbd .= $serie_temp.".&nbsp;".$this->notice->tit1 ;
		else $this->notice_isbd .= $this->notice->tit1;
	
		$this->notice_isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		if ($this->notice->tit3) $this->notice_isbd .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->tit4) $this->notice_isbd .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit2) $this->notice_isbd .= "&nbsp;; ".$this->notice->tit2 ;
		
		if ($this->auteurs_tous) $this->notice_isbd .= " / ".$this->auteurs_tous;
		if ($this->congres_tous) $this->notice_isbd .= " / ".$this->congres_tous;
		
		// mention d'édition
		if($this->notice->mention_edition) $this->notice_isbd .= " &nbsp;. -&nbsp; ".$this->notice->mention_edition;
		
		// zone de collection et éditeur
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->publisher, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection));
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->parent, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection));
		} elseif ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$editeurs .= inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
		}
		
		if($this->notice->ed2_id) {
			$editeur = new publisher($this->notice->ed2_id);
			$editeurs ? $editeurs .= '&nbsp;: '.inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur)) : $editeurs = inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur));
		}
	
		if($this->notice->year) $editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
		elseif ($this->notice->niveau_biblio == 'm' && $this->notice->niveau_hierar == 0) 
				$editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
	
		if($editeurs) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$editeurs";
		
		// zone de la collation
		if($this->notice->npages) $collation = $this->notice->npages;
		if($this->notice->ill) $collation .= '&nbsp;: '.$this->notice->ill;
		if($this->notice->size) $collation .= '&nbsp;; '.$this->notice->size;
		if($this->notice->accomp) $collation .= '&nbsp;+ '.$this->notice->accomp;
		if($collation) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$collation";
		if($collections) {
			if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
			$this->notice_isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}
	
		$this->notice_isbd .= '.';
			
		// ISBN ou NO. commercial
		if($this->notice->code) {
			if(isISBN($this->notice->code)) $zoneISBN = '<b>ISBN</b>&nbsp;: ';
			else $zoneISBN .= '<b>'.$msg["issn"].'</b>&nbsp;: ';
			$zoneISBN .= $this->notice->code;
		}
		if($this->notice->prix) {
			if($this->notice->code) $zoneISBN .= '&nbsp;: '.$this->notice->prix;
			else { 
				if ($zoneISBN) $zoneISBN .= '&nbsp; '.$this->notice->prix;
				else $zoneISBN = $this->notice->prix;
			}
		}
		if($zoneISBN) $this->notice_isbd .= "<br />".$zoneISBN;
		
		// note générale
		if($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if($zoneNote) $this->notice_isbd .= "<br />".$zoneNote;
				
	
		// langues
		if(count($this->langues)) {
			$langues = "<span class='etiq_champ'>${msg[537]}</span>&nbsp;: ".$this->construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <span class='etiq_champ'>${msg[711]}</span>&nbsp;: ".$this->construit_liste_langues($this->languesorg);
		}
		if ($langues) $this->notice_isbd .= "<br />".$langues ;
		
		if (!$short) {
			$this->notice_isbd .="<table>";
			$this->notice_isbd .= $this->aff_suite_isbd() ;
			$this->notice_isbd .="</table>";
		} else {
			$this->notice_isbd.=$this->genere_in_perio();
		}
	
		//etat des collections
		if ($this->notice->niveau_biblio=='s'&&$this->notice->niveau_hierar==1) $this->notice_isbd.=$this->affichage_etat_collections();	
	
		//Notices liées
		// ajoutées en dehors de l'onglet PUBLIC ailleurs
		
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	}	
	
	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		
		$this->notice_public="";
		if(!$this->notice_id) return;
		
		// Chargement des champs persos
		if(!$this->customs) $this->customs = $this->load_custom_fields();
		
		// Notices parentes
		$this->notice_public.=$this->parents;
	
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ' >".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		//Nature du document
		if($this->customs["NATURE"]) $this->notice_public .= $this->customs["NATURE"];
		
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
		
		//Date de publication
		if($this->customs["PUBLICATION"]) $this->notice_public .= $this->customs["PUBLICATION"];
			
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		
		// collection  
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
		}
		
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if ($this->notice->size) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if ($this->notice->prix) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_public .= $this->aff_suite_public() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	}	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite_public() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		
		//Thématique
		if($this->customs["THEMATIQUE"]) $ret_index .= $this->customs["THEMATIQUE"];
		
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
		
		//Public cible
		if($this->customs["CIBLE"]) $ret_index .= $this->customs["CIBLE"];
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// Permalink avec Id
		if ($opac_permalink) $ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";

		//Signataire
		if($this->customs["SIGNATAIRE"]) $ret .= $this->customs["SIGNATAIRE"];
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		
		//Champs personalisés visibles restants
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) {
					if (($p["NAME"] != "thematiques") && ($p["NAME"] != "date_de_publication")
						&& ($p["NAME"] != "public_cible") && ($p["NAME"] != "ancien_type_doc")
						&& ($p["NAME"] != "signataire")) {
						$perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";	
					}
				}
			}
		}
		$ret .= $perso_aff ;
		
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} 
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite_isbd() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		if($this->customs["NATURE"]) $ret.= $this->customs["NATURE"];
		if($this->customs["PUBLICATION"]) $ret .= $this->customs["PUBLICATION"];
		
		// toutes indexations
		$ret_index = "";
		
		//Thématique
		if($this->customs["THEMATIQUE"]) $ret_index .= $this->customs["THEMATIQUE"];
		
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
		
		//Public cible
		if($this->customs["CIBLE"]) $ret_index .= $this->customs["CIBLE"];
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		// Permalink avec Id
		if ($opac_permalink) $ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";

		//Signataire
		if($this->customs["SIGNATAIRE"]) $ret .= $this->customs["SIGNATAIRE"];
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		
		//Champs personalisés visibles restants
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) {
					if (($p["NAME"] != "thematiques") && ($p["NAME"] != "date_de_publication")
						&& ($p["NAME"] != "public_cible") && ($p["NAME"] != "ancien_type_doc")
						&& ($p["NAME"] != "signataire")) {
						$perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";	
					}
				}
			}
		}
		$ret .= $perso_aff ;

		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} 
	
	/*
	 * Chargement des champs persos
	 */
	function load_custom_fields(){
		
		$custom_fields = array();
		if (!$this->p_perso->no_special_fields) {
			$this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]){
					$value = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";					
					if ($p["NAME"] == "thematiques"){
						$custom_fields["THEMATIQUE"] = $value;
					}
					if ($p["NAME"] == "date_de_publication"){
						$custom_fields["PUBLICATION"] = $value;
					}	
					if ($p["NAME"] == "public_cible"){
						$custom_fields["CIBLE"] = $value;
					}
					if ($p["NAME"] == "ancien_type_doc"){
						$custom_fields["NATURE"] = $value;
					}
					if ($p["NAME"] == "signataire"){
						$custom_fields["SIGNATAIRE"] = $value;
					}
				}    
			}
		}
		
		return $custom_fields;
	}
}

/*
 * Classe d'affichage OPAC pour le CEDIAS
 */
class notice_affichage_cedias extends notice_affichage {
	
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		global $msg,$dbh;
		$ret='';
		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)){
			if ($this->notice->niveau_biblio=="b" && ($explnum = $this->show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= "<a name='docnum'><h3>$msg[explnum]</h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3>$msg[explnum]</h3></a>".$explnum;
			} else {
				if(($explnum = $this->show_explnum_per_notice($this->notice_id,0, ''))){
					$ret .= "<a name='docnum'><h3>$msg[explnum]</h3></a>".$explnum;
					$this->affichage_expl .= "<a name='docnum'><h3>$msg[explnum]</h3></a>".$explnum;
				}				
				if($this->notice->niveau_biblio=="a" && $this->notice->niveau_hierar=="2"){
					//cas des dépouillements...
					$req = "select analysis_bulletin from analysis where analysis_notice='".$this->notice_id."'";
					$res = pmb_mysql_query($req,$dbh);
					if(pmb_mysql_num_rows($res)){
						$bulletin_id = pmb_mysql_result($res,0,0);
						$explnum_bull = $this->show_explnum_per_notice(0,$bulletin_id, '', $this->notice_id);
						if($explnum_bull){
							$ret .= "<a name='docnum'><h3>".$msg['explnum_bulletin']."</h3></a>".$explnum_bull;
							$this->affichage_expl .= "<a name='docnum'><h3>".$msg['explnum_bulletin']."</h3></a>".$explnum_bull;
						}
					}
				}
			}
		}		 
		return $ret;
	}
	
	function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='',$analysis_id=0) {
		// params :
		// $link_expl= lien associé à l'exemplaire avec !!explnum_id!! à mettre à jour
		global $dbh;
		global $msg;
		global $charset;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		
		if (!$no_notice && !$no_bulletin) return "";
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype() ;
		
		// récupération du nombre d'exemplaires
		$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, explnum_vignette, explnum_nomfichier, explnum_extfichier FROM explnum WHERE ";
		if ($no_notice && !$no_bulletin) $requete .= "explnum_notice='$no_notice' ";
		elseif (!$no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' ";
		elseif ($no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' or explnum_notice='$no_notice' ";
		$requete .= " order by explnum_mimetype, explnum_id ";
		$res = pmb_mysql_query($requete, $dbh);
		$nb_ex = pmb_mysql_num_rows($res);
		
		if($nb_ex) {
			// on récupère les données des exemplaires
			$i = 1 ;
			global $search_terms;
		
			//Champ perso note de docnum
			$perso_display="";
			if (!$this->p_perso->no_special_fields) {
				$perso_=$this->p_perso->show_fields($this->notice_id);
				for ($j=0; $j<count($perso_["FIELDS"]); $j++) {
					$p=$perso_["FIELDS"][$j];
					if (($p['NAME']=='note_docnum')) $perso_display = $p['AFF'];
				}
			}
			
			while (($expl = pmb_mysql_fetch_object($res))) {
				if ($i==1) $ligne="<tr><td class='docnum' width='33%'>!!1!!</td><td class='docnum' width='33%'>!!2!!</td><td class='docnum' width='33%'>!!3!!</td></tr>" ;
				if ($link_expl) {
					$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
					$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
					$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
					} 
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."/vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0'>";
					else // trouver l'icone correspondant au mime_type
						$obj="<img src='".$opac_url_base."/images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0'>";		
				$expl_liste_obj = "<center>";
				
				$words_to_find="";
				if(($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					if(sizeof($search_terms)>0)$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
				}

				if ($opac_visionneuse_allow){
					//ouverture directe
					$pagin = '';
					if($no_bulletin && $analysis_id){
						$query = "select npages,notices_custom_small_text from notices left join notices_custom_values on notice_id = notices_custom_origine and notices_custom_champ = 35 where notice_id = ".$analysis_id;
						$res_analysis = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($res_analysis)){
							$row_analysis = pmb_mysql_fetch_object($res_analysis);
							$pagin = ($row_analysis->notices_custom_small_text ? $row_analysis->notices_custom_small_text : preg_replace("/[^0-9]/","",$row_analysis->npages)) * 1;
						}
					}
					$link="<script type='text/javascript'>
					
							if(typeof(sendToVisionneuse) == 'undefined' ){
							  var sendToVisionneuse = function (params){
								if(typeof(params) == 'string' && params.indexOf('_')){
								  var infos = params.split('_');
								  document.getElementById('visionneuseIframe').src = 'visionneuse.php?explnum_id='+infos[0]+'&myPage='+infos[1];
 								}else{
								  document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(params) != 'undefined' ? 'explnum_id='+params : '');
								}
								//document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id".($pagin ? "+'&myPage=".$pagin."'" : "")." : '');
							  }
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,'".$expl->explnum_id."_".$pagin."');return false;\" alt='$alt' title='$alt'>".$obj."</a><br />";
					$expl_liste_obj .=$link;
				}else{
					$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id$words_to_find";
					$expl_liste_obj .= "<a href='$opac_url_base$suite_url_explnum' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
				}
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
					elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
						else $explmime_nom = $expl->explnum_mimetype ;				
				
				if ($tlink) {
					$expl_liste_obj .= "<a href='$tlink'>";
					$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				} else {
					$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				}
				
				$expl_liste_obj .= "<div class='explnum_type'>".$perso_display."</div>";
				$expl_liste_obj .= "</center>";
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
					}
				}
				if (!$ligne_finale) $ligne_finale = $ligne ;
					elseif ($i!=1) $ligne_finale .= $ligne ;
				$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
				$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
			
		} else return "";
		$entry .= "<table class='docnum'>$ligne_finale</table>";
		return $entry;
	}
	
	
	function do_header($id_tpl=0) {
	
		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_url_base;
		global $charset;
	
		$this->notice_header="";
		if(!$this->notice_id) return;
	
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
	
		if ($type_reduit=="E") {
			// zone de l'éditeur
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			} elseif ($this->notice->year) {
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
	
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
	
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			$aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
	
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
	
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
	
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
	
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;
		}
	
		$this->notice_header .= $aff_bullperio_title;
	
		//$this->notice_header_without_html = $this->notice_header;
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
	
		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;
	
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		//$this->notice_header_without_html .= $notice_header_suite ;
		//$this->notice_header .= $notice_header_suite."</span>";
		//Un  span de trop ?
		$this->notice_header .= $notice_header_suite;
	
		if ($this->notice->niveau_biblio =='m' || $this->notice->niveau_biblio =='s') {
			switch($type_reduit) {
				case '1':
				if ($this->notice->year != '') $this->notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
				break;
				case '2':
				if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				if ($this->notice->code != '') $this->notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
				break;
				default:
				break;
			}
		}
	
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressources électroniques
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" />";
			$this->notice_header_doclink .= "</a></span>";
		}
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype, explnum_extfichier FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else if ($this->notice->niveau_biblio == 'a'){
			$sql_explnum_analysis = "SELECT explnum_id, explnum_nom,explnum_notice, explnum_bulletin, explnum_nomfichier,explnum_url, explnum_mimetype, explnum_extfichier FROM explnum WHERE explnum_notice = ".$this->notice_id;
			$sql_explnum_bull = "SELECT explnum_id, explnum_nom,explnum_notice, explnum_bulletin, explnum_nomfichier,explnum_url, explnum_mimetype, explnum_extfichier FROM analysis join explnum on analysis_notice = ".$this->notice_id." and explnum_bulletin = analysis_bulletin";
			$sql_explnum = "select * from ($sql_explnum_analysis union $sql_explnum_bull) as uni order by explnum_id";
		}else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url, explnum_mimetype, explnum_extfichier FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}
				$this->notice_header_doclink .= "&nbsp;<span>";
				if ($opac_visionneuse_allow && $this->docnum_allowed){
					if($this->notice->niveau_biblio == "a" && $explnumrow->explnum_bulletin != 0){
						$this->p_perso->get_values($this->notice_id) ;
						if($this->p_perso->values[35] && $this->p_perso->values[35][0] != 0){
							$pagin = $this->p_perso->values[35][0];
						}else{
							$pagin = preg_replace("/[^0-9]/","",$this->notice->npages) * 1;
						}	
						$this->notice_header_doclink .="
						<script type='text/javascript'>
							
						if(typeof(sendToVisionneuse) == 'undefined' ){
							var sendToVisionneuse = function (params){
								if(typeof(params) == 'string' && params.indexOf('_')){
									var infos = params.split('_');
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?explnum_id='+infos[0]+'&myPage='+infos[1];
								}else{
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(params) != 'undefined' ? 'explnum_id='+params : '');
								}
								//document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id".($pagin ? "+'&myPage=".$pagin."'" : "")." : '');
							}
						}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,'".$explnumrow->explnum_id."_".$pagin."');return false;\" alt='$alt' title='$alt'>";	
					}else{
						$this->notice_header_doclink .="
						<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined' ){
							var sendToVisionneuse = function (params){
								if(typeof(params) == 'string' && params.indexOf('_')){
									var infos = params.split('_');
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?explnum_id='+infos[0]+'&myPage='+infos[1];
								}else{
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(params) != 'undefined' ? 'explnum_id='+params : '');
								}
								//document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id".($pagin ? "+'&myPage=".$pagin."'" : "")." : '');
							}
						}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					}
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				
				
				$this->notice_header_doclink .= "<img src=\"".$this->get_docnum_icon($explnumrow->mimetype, $explnumrow->explnum_extfichier)."\" width='16px' border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
	
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;


		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;

		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;

	} // fin do_header()
	
	function get_docnum_icon($mimetype,$ext){
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype();
		
		$icon = icone_mimetype($mimetype,$ext);
		if($icon == "unknown.gif"){
			$path = "./images/globe_orange.png";
		}else{
			$path = "./images/mimetype/".$icon;
		}
		return $path;
	}
}

class notice_affichage_commande_copie extends notice_affichage {
	var $send_order ="";
	
	function do_header($id_tpl=0) {	
		global $msg;
		global $charset;
		global $lang;
		global $opac_url_base,$lang;
		
		if ($this->notice_header) return $this->notice_header ;
		
		parent::do_header();
		
		//booléen pour les articles de 5 ans et plus
		$condition_art = false;
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar ==2){
			if (date("Y")-($this->parent_date*1) >=5 ){
				$condition_art = true;
			}
		}

		$condition_chap = false;
		if($this->notice->typdoc == "w"){
			//si une année est défini
			if ($this->notice->year != ""){
				//en début d'année (de janvier à juin) on prend année strictement < à 2ans
				if(date("m")*1 <=6){
					if(date("Y")-$this->notice->year > 2){
						$condition_chap = true;
					}
				//dans le 2ème semestre on prend année <= à 2ans...	
				}else{
					if(date("Y")-$this->notice->year >= 2){
						$condition_chap = true;
					}					
				}
			}
		}
		
		if($condition_chap|| $condition_art)
		$this->send_order.= "
		&nbsp;<img src='".$opac_url_base."images_bsf/commander_$lang.gif' onclick='document.send_order$this->notice_id.submit();'>
		<form method='post' name='send_order$this->notice_id' target='_blank' action='".$opac_url_base."index.php?lvl=extend&sub=send_order'>
			<input type='hidden' name='order_notice_id' id='order_notice_id' value='$this->notice_id' />
		</form>";
		
		
	}
	
	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_double($depliable=1, $premier='ISBD') {
		global $msg;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		$this->result ="";
		if(!$this->notice_id) return;	
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;
		$this->notice_childs = $this->genere_notice_childs();
		if ($this->cart_allowed) $basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."images/basket_small_20x20.png\" border=\"0\" alt=\"".$msg['notice_title_basket']."\"></a>"; 
		else $basket="";
		
		//add tags
		if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) )
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\"></a>";	
		
		 //Avis
		if (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))
			$img_avis= $this->affichage_avis($this->notice_id);	
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && $this->sugg_allowed ==1))
			$img_sugg= $this->affichage_suggestion($this->notice_id);
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
	
		$icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if ($depliable) {
			$template="
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onclick=\"expandBase('el!!id!!', true);return false;\" hspace=\"3\" />";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>!!send_order!!
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">";
		} else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
    		}
    		$template.="<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>!!send_order!!";
		}
	 	$template.="!!CONTENU!!
					!!SUITE!!</div>";
	
		//$template_in=$basket;
		$template_in.="<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD') $template_in.="
	    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>
	    	<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    	<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
	    	<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>
	    	<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>
	    	<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>
			</ul>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	  	else $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
		    	<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>
		    	<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>
		    	<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>
				</ul>
				<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  			<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
		if($this->notice->opac_visible_bulletinage) $voir_bulletins="&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
		$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>$voir_bulletins&nbsp;!!ISBD!!", $template_in);
		$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>$voir_bulletins&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
	
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		$this->result = str_replace('!!send_order!!', $this->send_order, $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		if ($this->affichage_resa_expl || $this->notice_childs) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl, $this->result); 		
		$this->result = str_replace('!!SUITE!!', "", $this->result);
	}
	
	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg; 
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		if(!$this->notice_id) return;
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed) $basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\"></a>"; 
		else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\"></a>";
		
		 //Avis
		if (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))
			$img_avis= $this->affichage_avis($this->notice_id);
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && $this->sugg_allowed ==1))
			$img_sugg= $this->affichage_suggestion($this->notice_id);	
		 
		$icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		
		if ($depliable) { 
			$template="
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>!!send_order!!
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
	    		";			
		} else {
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    		$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
    		}			
    		$template.="<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>!!send_order!!<br />";
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";
		
		if($basket || $img_tag || $img_avis || $img_sugg) $template_in.="<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>
						<li id='baskets!!id!!' class='onglet_basket'>$basket</li>
	  					<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>
	  					<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>
	  					<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>
					   </ul>	
		";
		if($what =='ISBD') $template_in.="		    	
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		else $template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>"
	  			; 	
		
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
		
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
		if ($this->send_order)
		$this->result = str_replace('!!send_order!!', $this->send_order, $this->result);
		else $this->result = str_replace('!!send_order!!', '', $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		if ($this->affichage_resa_expl || $this->notice_childs) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
				
	}
	
	function do_isbd_small($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
		global $memo_notice;
		
		$this->notice_isbd_small="";
		if(!$this->notice_id) return;
		//In
		//Recherche des notices parentes
		$requete="select linked_notice, relation_type, rank from notices_relations where num_notice=".$this->notice_id." order by relation_type,rank";
		$result_linked=pmb_mysql_query($requete,$dbh);
		
		//Si il y en a, on prépare l'affichage
		if (pmb_mysql_num_rows($result_linked)) {
			global $relation_listup ;
			if (!$relation_listup) $relation_listup=new marc_list("relationtypeup");
		}
		$r_type=array();
		$ul_opened=false;
		//Pour toutes les notices liées
		while (($r_rel=pmb_mysql_fetch_object($result_linked))) {
			if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";

			if($memo_notice[$r_rel->linked_notice]["header"]) {
				$parent_notice->notice_header=$memo_notice[$r_rel->linked_notice]["header"];
			} else {
				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,$this->cart,$this->to_print,1);
				$parent_notice->visu_expl = 0;
				$parent_notice->visu_explnum = 0;
				$parent_notice->do_header();
			}				
			//Présentation différente si il y en a un ou plusieurs
			if (pmb_mysql_num_rows($result_linked)==1) {
				$this->notice_isbd_small.="<br /><b>".$relation_listup->table[$r_rel->relation_type]."</b> ";
				if ($this->lien_rech_notice) $this->notice_isbd_small.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				$this->notice_isbd_small.=$parent_notice->notice_header;
				if ($this->lien_rech_notice) $this->notice_isbd_small.="</a>";
				$this->notice_isbd_small.="<br /><br />";
				// si une seule, peut-être est-ce une notice de bulletin, aller chercher $this>bulletin_id
				$rqbull="select bulletin_id from bulletins where num_notice=".$this->notice_id;
				$rqbullr=pmb_mysql_query($rqbull);
				$rqbulld=@pmb_mysql_fetch_object($rqbullr);
				if($rqbulld->bulletin_id)	$this->bulletin_id=$rqbulld->bulletin_id;
			} else {
				if (!$r_type[$r_rel->relation_type]) {
					$r_type[$r_rel->relation_type]=1;
					if ($ul_opened) $this->notice_isbd_small.="</ul>"; else { $this->notice_isbd_small.="<br />"; $ul_opened=true; }
					$this->notice_isbd_small.="<b>".$relation_listup->table[$r_rel->relation_type]."</b>";
					$this->notice_isbd_small.="<ul class='notice_rel'>\n";
				}
				$this->notice_isbd_small.="<li>";
				if ($this->lien_rech_notice) $this->notice_isbd_small.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				$this->notice_isbd_small.=$parent_notice->notice_header;
				if ($this->lien_rech_notice) $this->notice_isbd_small.="</a>";
				$this->notice_isbd_small.="</li>\n";
			}
			if (pmb_mysql_num_rows($result_linked)>1) $this->notice_isbd_small.="</ul>\n";
		}
		
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$serie_temp .= inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
			if($this->notice->tnvol) $serie_temp .= ',&nbsp;'.$this->notice->tnvol;
		}
		if ($serie_temp) $this->notice_isbd_small .= $serie_temp.".&nbsp;".$this->notice->tit1 ;
		else $this->notice_isbd_small .= $this->notice->tit1;
	
		$this->notice_isbd_small .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		if ($this->notice->tit3) $this->notice_isbd_small .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->tit4) $this->notice_isbd_small .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit2) $this->notice_isbd_small .= "&nbsp;; ".$this->notice->tit2 ;
		
		if ($this->auteurs_tous) $this->notice_isbd_small .= " / ".$this->auteurs_tous;
		if ($this->congres_tous) $this->notice_isbd_small .= " / ".$this->congres_tous;
		
		// mention d'édition
		if($this->notice->mention_edition) $this->notice_isbd_small .= " &nbsp;. -&nbsp; ".$this->notice->mention_edition;
		
		// zone de collection et éditeur
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->publisher, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection));
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->parent, $this->lien_rech_editeur));
			$collections = inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection));
		} elseif ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$editeurs .= inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
		}
		
		if($this->notice->ed2_id) {
			$editeur = new publisher($this->notice->ed2_id);
			$editeurs ? $editeurs .= '&nbsp;: '.inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur)) : $editeurs = inslink($editeur->isbd_entry,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur));
		}
	
		if($this->notice->year) $editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
		elseif ($this->notice->niveau_biblio == 'm' && $this->notice->niveau_hierar == 0) 
				$editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
	
		if($editeurs) $this->notice_isbd_small .= "&nbsp;.&nbsp;-&nbsp;$editeurs";
		
		// zone de la collation
		if($this->notice->npages) $collation = $this->notice->npages;
		if($this->notice->ill) $collation .= '&nbsp;: '.$this->notice->ill;
		if($this->notice->size) $collation .= '&nbsp;; '.$this->notice->size;
		if($this->notice->accomp) $collation .= '&nbsp;+ '.$this->notice->accomp;
		if($collation) $this->notice_isbd_small .= "&nbsp;.&nbsp;-&nbsp;$collation";
		if($collections) {
			if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
			$this->notice_isbd_small .= ".&nbsp;-&nbsp;($collections)".' ';
		}
	
		$this->notice_isbd_small .= '.';
			
		// ISBN ou NO. commercial
		if($this->notice->code) {
			if(isISBN($this->notice->code)) $zoneISBN = '<b>ISBN</b>&nbsp;: ';
			else $zoneISBN .= '<b>'.$msg["issn"].'</b>&nbsp;: ';
			$zoneISBN .= $this->notice->code;
		}
		if($this->notice->prix) {
			if($this->notice->code) $zoneISBN .= '&nbsp;: '.$this->notice->prix;
			else { 
				if ($zoneISBN) $zoneISBN .= '&nbsp; '.$this->notice->prix;
				else $zoneISBN = $this->notice->prix;
			}
		}
		if($zoneISBN) $this->notice_isbd_small .= "<br />".$zoneISBN;

		// langues
		if(count($this->langues)) {
			$langues = "<span class='etiq_champ'>${msg[537]}</span>&nbsp;: ".$this->construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <span class='etiq_champ'>${msg[711]}</span>&nbsp;: ".$this->construit_liste_langues($this->languesorg);
		}
		if ($langues) $this->notice_isbd_small .= "<br />".$langues ;
		
		if (!$short) {
			$this->notice_isbd_small .="<table>";
			$this->notice_isbd_small .= $this->aff_suite() ;
			$this->notice_isbd_small .="</table>";
		} else {
			$this->notice_isbd_small.=$this->genere_in_perio();
		}
	
		//etat des collections
		if ($this->notice->niveau_biblio=='s'&&$this->notice->niveau_hierar==1) $this->notice_isbd_small.=$this->affichage_etat_collections();	
	
		//Notices liées
		// ajoutées en dehors de l'onglet PUBLIC ailleurs
		
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	}	
}


/*
 * Classe d'affichage pour le RECI 
 */
class notice_affichage_reci extends notice_affichage {	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
			
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		if ($perso_aff) {
			//Espace
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret .= $perso_aff ;
		}
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} 
}	

class notice_affichage_ireps extends notice_affichage {
	
	// génération du de l'affichage double avec onglets
	//	si $depliable=1 alors inclusion du parent / child
	function genere_double($depliable=1, $premier='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $opac_notice_enrichment;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg;// l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;	   // pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;

		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		$this->result ="";
		if(!$this->notice_id) return;	
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;
		$this->notice_childs = $this->genere_notice_childs();
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."/styles/ireps/images/panier.png\" border=\"0\" alt=\"".$msg['notice_title_basket']."\" /></a>";
			} 
		}else $basket="";
		
		//add tags
		if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) )
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";	
		
		 //Avis
		if (($opac_avis_display_mode==0) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);	
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);
		
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
	
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);
			}
		}
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) {
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\" />";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}elseif ($depliable == 2) {
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\">
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['expandable_notice']."\" border=\"0\" hspace=\"3\" />";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}else{
			$template="
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$this->notice_id."');
				}else{
					waitingAddthisLoaded('el".$this->notice_id."');
				}
			</script>
			<div id='el!!id!!Parent' class='parent'>$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
	 	$template.="!!CONTENU!!
					!!SUITE!!</div>";
	
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
		
		if($opac_show_social_network){		
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}
			$template_in.="
			<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
			</div>";	
		}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD'){ 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	    		<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    		<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
	    		<!-- onglets_perso_list -->
	    		$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	  		$template_in.="<!-- onglets_perso_content -->";
	    } elseif($premier=="autre") { 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$onglet_perso=new notice_onglets();
			$template_in.=$onglet_perso->build_onglets($this->notice_id,$li_tags);
			
	    } else { 
	    	if ($basket) $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
	    	$template_in.="
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
		    	<!-- onglets_perso_list -->
		    	$li_tags
			</ul>
			<div class='row'></div>
			<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  		<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
	  		$template_in.="<!-- onglets_perso_content -->";
	    }
		
	    if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
	    
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			if(!$flag_no_get_bulletin){
			  if($this->get_bulletins()){
				  if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
				  else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
			  }
			}
			//si visionneuse active...	
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
	
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}else
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}
		
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result); 		
		$this->result = str_replace('!!SUITE!!', "", $this->result);
	} // fin genere_double($depliable=1, $premier='ISBD')

	
	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg; // l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		global $opac_visionneuse_allow;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if(!$this->notice_id) return;
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."/styles/ireps/images/panier.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";
		
		 //Avis
		if (($opac_avis_display_mode==0)&&(($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);	
		 
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) { 
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";			
		}elseif($depliable == 2){ 
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true);  $script_simili_search return false;\">
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="		
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";						
		}else{
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
	    		$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";
		
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}

		if($opac_show_social_network){	
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}	
			$template_in.="
		<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
		</div>";	
		}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		if($basket || $img_tag || $img_avis || $img_avis || $opac_notice_enrichment){
			$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
			if ($basket) $template_in.="<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			if($opac_notice_enrichment){
				if($what =='ISBD') $template_in.="<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>";
				else $template_in.="<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>";
			}
			$template_in.="
	  			$li_tags
			<!-- onglets_perso_list -->
		</ul>
		<div class='row'></div>";	
		}	

		if($what =='ISBD') $template_in.="		    	
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		else $template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>"
	  			; 	
		$template_in.="
			<!-- onglets_perso_content -->";
		if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();

		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
		
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}	
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
				
	} // fin genere_simple($depliable=1, $what='ISBD')


	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg ;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_url_base;
		$this->notice_header="";
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
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
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;
		}

		$this->notice_header .= $aff_bullperio_title;
		
		//$this->notice_header_without_html = $this->notice_header;	
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		
		if ($this->notice->niveau_biblio =='m') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $notice_header_suite.=' / ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $notice_header_suite.=' / ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $notice_header_suite.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
 		//$this->notice_header_without_html .= $notice_header_suite ;
		//$this->notice_header .= $notice_header_suite."</span>";
		//Un  span de trop ?
		$this->notice_header .= $notice_header_suite;	
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressource électroniques
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\">";
			$this->notice_header_doclink .= "</a></span>";
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";		
				if ($opac_visionneuse_allow && $this->docnum_allowed){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				$this->notice_header_doclink .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}

		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;		
		
 		$this->notice_header_without_doclink=$this->notice_header;
 		$this->notice_header.=$this->notice_header_doclink;
		
 		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
 		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
	} // fin do_header()
	
	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		//$ret .= $this->genere_in_perio () ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		if ($perso_aff) {
			//Espace
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret .= $perso_aff ;
		}
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} // fin aff_suite()
	
	
	// fonction de génération du tableau des exemplaires
	function expl_list($type,$id,$bull_id=0,$build_ifempty=1) {	
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		global $opac_sur_location_activate,$opac_view_filter_class;
	
		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;
		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;
		
		if($opac_sur_location_activate){
			$opac_sur_location_select=", sur_location.*";
			$opac_sur_location_from=", sur_location";
			$opac_sur_location_where=" AND docs_location.surloc_num=sur_location.surloc_id";
		}
		if($opac_view_filter_class){
			$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
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
		
		// récupération du nombre d'exemplaires
		$res = pmb_mysql_query($requete, $dbh);
		if(!$build_ifempty && !pmb_mysql_num_rows($res))return"";
		$surloc_field="";
		if ($opac_sur_location_activate==1) $surloc_field="surloc_libelle,";
		if (!$opac_expl_data) $opac_expl_data="expl_cb,expl_cote,tdoc_libelle,".$surloc_field."location_libelle,section_libelle";
		$colonnesarray=explode(",",$opac_expl_data);
		
		$expl_list_header_deb="";
		for ($i=0; $i<count($colonnesarray); $i++) {
			eval ("\$colencours=\$msg['expl_header_".$colonnesarray[$i]."'];");
			$expl_list_header_deb.="<th class='expl_header_".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
		}
		$expl_list_header_deb.="<th>$msg[statut]</th>";
		$expl_liste="";
		$nb_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
		while(($expl = pmb_mysql_fetch_object($res))) {
		$compteur = $compteur+1;
			$expl_liste .= "<tr>";
			$colencours="";

			for ($i=0; $i<count($colonnesarray); $i++) {
				eval ("\$colencours=\$expl->".$colonnesarray[$i].";");
				if ($colonnesarray[$i]=="location_libelle" && $expl->num_infopage) {
					if ($expl->surloc_id != "0") $param_surloc="&surloc=".$expl->surloc_id;
					else $param_surloc="";
					$expl_liste.="<td class='".$colonnesarray[$i]."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl->num_infopage."&location=".$expl->expl_location.$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".htmlentities($colencours, ENT_QUOTES, $charset)."</a></td>";
				} else 
					$expl_liste.="<td class='".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</td>";
			}
	
			$requete_resa = "SELECT count(1) from resa where resa_cb='$expl->expl_cb' ";
			$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$requete_resa = "SELECT count(1) from resa_ranger where resa_cb='$expl->expl_cb' ";
			$flag_resa = $flag_resa + pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$situation = "";
			if ($expl->statut_libelle_opac != "") $situation .= $expl->statut_libelle_opac."<br />";
			if ($flag_resa) {
				$nb_resa--;
				$situation .= $msg['expl_reserve'];
			} else {
				if ($expl->pret_flag) {
					if($expl->pret_retour) { // exemplaire sorti
						global $opac_show_empr ;
						if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
							$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl->pret_idempr' ";
							$res_empr = pmb_mysql_query($rqt_empr, $dbh) ;
							$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
							$situation .= $msg[entete_show_empr].htmlentities(" $res_empr_obj->empr_prenom $res_empr_obj->empr_nom",ENT_QUOTES, $charset)."<br />";
						} 
						$situation .= $msg['out_until'].formatdate($expl->pret_retour);
						// ****** Affichage de l'emprunteur
					} else { // pas sorti
						$situation .= $msg['available'];
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if (($pmb_transferts_actif=="1")&&("".$expl->expl_statut.""==$transferts_statut_transferts)) {
						$situation .= $msg['reservation_lib_entransfert'];
					} else {
						$situation .= $msg['exclu'];
					}
				}
			} // fin if else $flag_resa 
			$expl_liste .= "<td class='expl_situation'>$situation </td>";
			
			//Champs personalisés
			$perso_aff = "" ;
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_=$memo_p_perso_expl->show_fields($expl->expl_id);
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
			if($expl->expl_location==$_SESSION["empr_location"]) {
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
		if(!$expl_liste_loc) $expl_liste_loc="<tr class=even><td colspan='".(count($colonnesarray)+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";	
		$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);	
		$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);	
		$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
		$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
	} else {
		// affichage de la liste d'exemplaires calculée ci-dessus
		if (!$expl_liste_all && $opac_show_empty_items_block==1) {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($colonnesarray)+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
		} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
			$expl_liste_all = ""; 
		} else {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
		}
	}
	return $expl_liste_all;
		
	} // fin function expl_list

	
}

class notice_affichage_invs extends notice_affichage {
	// récupération des auteurs ---------------------------------------------------------------------
	// retourne $this->auteurs_principaux = ce qu'on va afficher en titre du résultat
	// retourne $this->auteurs = ce qu'on va afficher dans l'isbd
	// retourne $this->appartenance = ce qu'on va afficher dans l'isbd
	function fetch_auteurs() {
		global $fonction_auteur;
		global $dbh ;
		global $opac_url_base ;
	
		$this->responsabilites  = array() ;
		$auteurs = array() ;
		
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;
		
		$rqt = "SELECT author_id, responsability_fonction, responsability_type, author_type,author_name, author_rejete, author_type, author_date, author_see, author_web ";
		$rqt.= "FROM responsability, authors ";
		$rqt.= "WHERE responsability_notice='".$this->notice_id."' AND responsability_author=author_id ";
		$rqt.= "ORDER BY responsability_type, responsability_ordre " ;
		$res_sql = pmb_mysql_query($rqt, $dbh);
		while (($notice=pmb_mysql_fetch_object($res_sql))) {
			$responsabilites[] = $notice->responsability_type ;
			$info_bulle="";
			if($notice->author_type==72 || $notice->author_type==71) {			
				$congres=new auteur($notice->author_id);
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
			// URL de l'auteur
			if ($notice->author_web) $auteur_web_link = " <a href='$notice->author_web' target='_blank'><img src='".$opac_url_base."images/globe.gif' border='0'/></a>";
			else $auteur_web_link = "" ;
			if (!$this->to_print) $auteur_isbd .= $auteur_web_link ;
			$auteur_isbd = inslink($auteur_isbd, str_replace("!!id!!", $notice->author_id, $this->lien_rech_auteur),$info_bulle) ;
			if ($notice->responsability_fonction) $auteur_isbd .= ", ".$fonction_auteur[$notice->responsability_fonction] ;
			$auteurs[] = array( 
					'id' => $notice->author_id,
					'fonction' => $notice->responsability_fonction,
					'responsability' => $notice->responsability_type,
					'name' => $notice->author_name,
					'rejete' => $notice->author_rejete,
					'date' => $notice->author_date,
					'type' => $notice->author_type,
					'fonction_aff' => $fonction_auteur[$notice->responsability_fonction],
					'auteur_isbd' => $auteur_isbd,
					'auteur_titre' => $auteur_titre
					) ;
		}
		if (!$responsabilites) $responsabilites = array();
		if (!$auteurs) $auteurs = array();
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;
		
		// $this->auteurs_principaux 
		// on ne prend que le auteur_titre = "Prénom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			$aut1_libelle = array();
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;			
				if($auteur_1["type"]==72 || $auteur_1["type"]==72) {			
					$congres=new auteur($auteur_1["id"]);
					$aut1_libelle[]=$congres->display;
				} else {
					$aut1_libelle[]= $auteur_1["auteur_titre"];
				}	
			}
			$auteurs_liste = implode (" ; ",$aut1_libelle) ;
			if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
		}
	
		// $this->auteurs_tous
		$mention_resp = array() ;
		$congres_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			if($this->responsabilites["auteurs"][$as]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$appartenance_mention_resp[] = $auteur_2["auteur_isbd"];

		}
		
		$libelle_mention_resp = implode (" ; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs = $libelle_mention_resp ;
		else $this->auteurs ="" ;
		
		$libelle_congres_resp = implode (" ; ",$congres_resp) ;
		if ($libelle_congres_resp) $this->congres_tous = $libelle_congres_resp ;
		else $this->congres_tous ="" ;
		
		$appartenance_libelle_mention_resp = implode (" ; ",$appartenance_mention_resp) ;
		if ($appartenance_libelle_mention_resp) $this->appartenance = $appartenance_libelle_mention_resp ;
		else $this->appartenance ="" ;
		
		
		$this->auteurs_tous = $this->auteurs.($this->auteurs && $this->appartenance ? " ; " : "").$this->appartenance;
		
	} // fin fetch_auteurs
	
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		
		$this->notice_public="";
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_public.=$this->parents;
			
		$this->notice_public .= "<table class='invs-notice'>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";
		
		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		if ($this->auteurs) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs."</td></tr>";
		if ($this->appartenance) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['appartenance_auteur_start']."</span></td><td>".$this->appartenance."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->year)
			$annee = "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
	
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}  
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		
		// collection  
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
		}
		
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if ($this->notice->size) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if ($this->notice->prix) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_public .= $this->aff_suite() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	} // fin do_public($short=0,$ex=1)
	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		$ret .= $this->genere_in_perio () ;
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) $perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		if ($perso_aff) {
			//Espace
		//	if($this->notice->n_resume) $ret.="<tr><td class='bg-grey'>&nbsp;</td><td>&nbsp;</td></tr>";
			$ret .= $perso_aff ;
		}
		
		if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
				$ret.="</td></tr>";
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret ;
	} // fin aff_suite()
	
}

class notice_affichage_ensosp extends notice_affichage {
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		
		global $msg;
		$ret='';

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)){
			if ($this->notice->niveau_biblio=="b" && ($explnum = $this->show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			} elseif (($explnum = $this->show_explnum_per_notice($this->notice_id,0, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			}
		}		 
		return $ret;
	} // fin aff_explnum ()
	
	// fonction retournant les infos d'exemplaires numériques pour une notice ou un bulletin donné
	function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='') {
		
		// params :
		// $link_expl= lien associé à l'exemplaire avec !!explnum_id!! à mettre à jour
		global $dbh;
		global $charset;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		
		if (!$no_notice && !$no_bulletin) return "";
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype() ;
		
		// récupération du nombre d'exemplaires
		$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_repertoire FROM explnum WHERE ";
		if ($no_notice && !$no_bulletin) $requete .= "(explnum_notice='$no_notice' and explnum_bulletin=0) ";
		elseif (!$no_notice && $no_bulletin) $requete .= "(explnum_bulletin='$no_bulletin' and explnum_notice) ";
		elseif ($no_notice && $no_bulletin) $requete .= "(explnum_bulletin='$no_bulletin' and explnum_notice=0) or (explnum_notice='$no_notice' and explnum_bulletin) ";
		$requete .= " order by explnum_mimetype, explnum_id ";
		$res = pmb_mysql_query($requete, $dbh);
		$nb_ex = pmb_mysql_num_rows($res);
		
		if ($nb_ex) {
			// on récupère les données des exemplaires
			$i = 1 ;
			global $search_terms;
			
			while (($expl = pmb_mysql_fetch_object($res))) {
				if ($i==1) $ligne="<tr><td class='docnum' width='33%'>!!1!!</td><td class='docnum' width='33%'>!!2!!</td><td class='docnum' width='33%'>!!3!!</td></tr>" ;
				if ($link_expl) {
					$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
					$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
					$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
					} 
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0'>";
					else // trouver l'icone correspondant au mime_type
						$obj="<img src='".$opac_url_base."images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0'>";		
				$expl_liste_obj = "<center>";
				
				$words_to_find="";
				if (($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
				}
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
					$link="
						<script type='text/javascript'>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;\" alt='$alt' title='$alt'>".$obj."</a><br />";
					$expl_liste_obj .=$link;
				} else {
					if($expl->explnum_repertoire != 0){
						switch($_SERVER['REMOTE_ADDR']){
							case "92.103.17.90":
							case "92.103.17.91":
							case "92.103.17.92":
							case "92.103.17.93":
							case "92.103.17.94":
							case "217.128.195.136":
							case "193.251.186.82":
							case "80.13.185.226":
							case "80.14.211.232":
							case "80.13.10.218":
							case "80.13.195.117":
								//accès interne
								$expl_liste_obj .= "<a href='http://ressources.ensosp.fr/".$expl->explnum_nomfichier."' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;					
								break;
							default :
								//accès externe
								$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
								$expl_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
								break;
						}
					}else{
						//accès externe
						$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
						$expl_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
					}
				}
	
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
				elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
				else $explmime_nom = $expl->explnum_mimetype ;
				
				
				if ($tlink) {
					$expl_liste_obj .= "<a href='$tlink'>";
					$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
					} else {
						$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
						}
				$expl_liste_obj .= "</center>";
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
					}
				}
			if (!$ligne_finale) $ligne_finale = $ligne ;
			elseif ($i!=1) $ligne_finale .= $ligne ;
			$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
			$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
			
			} else return "";
		$entry .= "<table class='docnum'>$ligne_finale</table>";
		return $entry;
	}
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg ;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_url_base;
		
		$this->notice_header="";
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
		//Champs personalisés à ajouter au réduit 
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
		
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		$this->notice_header .= $aff_bullperio_title;
		
		if ($this->notice->niveau_biblio =='m') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $this->notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $this->notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		
		$this->notice_header_without_html = $this->notice_header;	
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		$this->notice_header_without_html .= $notice_header_suite ;
		//$this->notice_header .= $notice_header_suite."</span>";
		//Un  span de trop ?	
		$this->notice_header .= $notice_header_suite;
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressource électroniques			
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\">";
			$this->notice_header_doclink .= "</a></span>";			
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_repertoire FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url, explnum_repertoire FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";		
				if ($opac_visionneuse_allow && $this->docnum_allowed){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					if($explnumrow->explnum_repertoire != 0){
						switch($_SERVER['REMOTE_ADDR']){
							case "92.103.17.90":
							case "92.103.17.91":
							case "92.103.17.92":
							case "92.103.17.93":
							case "92.103.17.94":
							case "217.128.195.136":
							case "193.251.186.82":
							case "80.13.185.226":
							case "80.14.211.232":
							case "80.13.10.218":
							case "80.13.195.117":
								//accès interne
								$this->notice_header_doclink .= "<a href='http://ressources.ensosp.fr/".$explnumrow->explnum_nomfichier."' target=\"__LINK__\">" ;						
								break;
							default :
								//accès externe
								$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">" ;
								break;
						}
					}else{
						//accès externe
						$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
						$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">" ;
					}
					
					$this->notice_header_doclink .= "";
				}
				$this->notice_header_doclink .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;		
		
		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
	} // fin do_header()
}

class notice_affichage_livrechange extends notice_affichage {
	function expl_list($type,$id,$bull_id=0,$build_ifempty=1) {	
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		global $opac_sur_location_activate,$opac_view_filter_class;

	$nb_expl_autre_loc=0;
	$nb_perso_aff=0;
		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;
		
		if($opac_sur_location_activate){
			$opac_sur_location_select=", sur_location.*";
			$opac_sur_location_from=", sur_location";
			$opac_sur_location_where=" AND docs_location.surloc_num=sur_location.surloc_id";
		}
		if($opac_view_filter_class){
			$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
		}
		// les exemplaires des monographies
		if ($type=="m") {
			$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* $opac_sur_location_select";
			$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders $opac_sur_location_from";
			$requete .= " WHERE expl_notice='$id' and expl_bulletin='$bull_id'";
			$requete .= " AND location_visible_opac=1 AND section_visible_opac=1";			
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
			$requete .= " AND location_visible_opac=1 AND section_visible_opac=1";			
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
			$requete .= " AND location_visible_opac=1 AND section_visible_opac=1";			
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
		
		// récupération du nombre d'exemplaires
		$res = pmb_mysql_query($requete, $dbh);
		if(!$build_ifempty && !pmb_mysql_num_rows($res))return"";
		$surloc_field="";
		if ($opac_sur_location_activate==1) $surloc_field="surloc_libelle,";
		if (!$opac_expl_data) $opac_expl_data="expl_cb,expl_cote,tdoc_libelle,".$surloc_field."location_libelle,section_libelle";
		$colonnesarray=explode(",",$opac_expl_data);
		
		$expl_list_header_deb="";
		for ($i=0; $i<count($colonnesarray); $i++) {
			eval ("\$colencours=\$msg[expl_header_".$colonnesarray[$i]."];");
			$expl_list_header_deb.="<th class='expl_header_".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
		}
		$expl_list_header_deb.="<th>$msg[statut]</th>";
		$expl_liste="";
		$nb_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
		while(($expl = pmb_mysql_fetch_object($res))) {
			$compteur = $compteur+1;
			$expl_liste .= "<tr>";
			$colencours="";

			for ($i=0; $i<count($colonnesarray); $i++) {
				eval ("\$colencours=\$expl->".$colonnesarray[$i].";");
				if ($colonnesarray[$i]=="location_libelle" && $expl->num_infopage) {
					if ($expl->surloc_id != "0") $param_surloc="&surloc=".$expl->surloc_id;
					else $param_surloc="";
					$expl_liste.="<td class='".$colonnesarray[$i]."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl->num_infopage."&location=".$expl->expl_location.$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".htmlentities($colencours, ENT_QUOTES, $charset)."</a></td>";
				}elseif($colonnesarray[$i]=="expl_cote" ){
					$expl_liste.="<td class='".$colonnesarray[$i]."'>".$colencours."</td>";
				}else{
					$expl_liste.="<td class='".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</td>";
				}
					
			}
	
			$requete_resa = "SELECT count(1) from resa where resa_cb='$expl->expl_cb' ";
			$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$requete_resa = "SELECT count(1) from resa_ranger where resa_cb='$expl->expl_cb' ";
			$flag_resa = $flag_resa + pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$situation = "";
			if ($flag_resa) {
				$nb_resa--;
				$situation = "<strong>$msg[expl_reserve]</strong>";
			} else {
				if ($expl->pret_flag) {
					if($expl->pret_retour) { // exemplaire sorti
						global $opac_show_empr ;
						if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
							$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl->pret_idempr' ";
							$res_empr = pmb_mysql_query($rqt_empr, $dbh) ;
							$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
							$situation = $msg[entete_show_empr].htmlentities(" $res_empr_obj->empr_prenom $res_empr_obj->empr_nom",ENT_QUOTES, $charset)."<br />";
						} 
						$situation .= "<strong>$msg[out_until] ".formatdate($expl->pret_retour).'</strong>';
						// ****** Affichage de l'emprunteur
					} else { // pas sorti
						$situation = "<strong>".$msg['available']."</strong>";
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if (($pmb_transferts_actif=="1")&&("".$expl->expl_statut.""==$transferts_statut_transferts))
						$situation = "<strong>".$msg['reservation_lib_entransfert']."</strong>";
					else
						$situation = "<strong>".$msg['exclu']."</strong>";
					// $situation = "<strong>".$expl->statut_libelle.'</strong>';
				}
			} // fin if else $flag_resa 
			$expl_liste .= "<td class='expl_situation'>$situation </td>";
			
			//Champs personalisés
			$perso_aff = "" ;
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_=$memo_p_perso_expl->show_fields($expl->expl_id);
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
			if($expl->expl_location==$_SESSION["empr_location"]) {
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
		if(!$expl_liste_loc) $expl_liste_loc="<tr class=even><td colspan='".(count($colonnesarray)+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";	
		$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);	
		$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);	
		$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
		$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
	} else {
		// affichage de la liste d'exemplaires calculée ci-dessus
		if (!$expl_liste_all && $opac_show_empty_items_block==1) {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($colonnesarray)+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
		} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
			$expl_liste_all = ""; 
		} else {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
		}
	}
	return $expl_liste_all;
		
	} // fin function expl_list
}

// Centre d'étude de l'emploi
class notice_affichage_cee extends notice_affichage {
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {
		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_url_base;
		global $opac_resa_popup;
		$this->notice_header="";		
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){			
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);		
				if($notice_tpl_header){						
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}	
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		$editeur_reduit = "" ;
		// zone de l'éditeur 
		if ($this->notice->ed1_id) {				
			$requete = "SELECT * FROM publishers WHERE ed_id='".$this->notice->ed1_id."' ";
			$result = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($result)) {
				$obj = pmb_mysql_fetch_object($result);	
				$editeur_reduit = $obj->ed_name ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			}	
		} elseif ($this->notice->year) { 
			// année mais pas d'éditeur et si pas un article
			if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = " (".$this->notice->year.")";
		}
	
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
		
		//Si c'est un depouillement, : Titre / Auteur principal in Collection Année cote"
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_numero.", ".$this->parent_title.", ".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]")."</i>";			 
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin: Titre du périodique, vol., n° (date) - Cote Disponibilité"
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."";
		} else $aff_bullperio_title="";

		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;	
		}
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2){
			$this->notice_header ="";
		}		
		
		$this->notice_header .= $aff_bullperio_title;
/*		if ($this->notice->niveau_biblio =='m') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $this->notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $this->notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
	*/	
		//$this->notice_header_without_html = $this->notice_header;	
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit && $this->notice->niveau_biblio != "m" && $this->notice->niveau_hierar!=0) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		
		$this->notice_header.= $notice_header_suite;
		
		//"titre+auteur+in+vol, n°, année, titre de périodique" Cote
		if($this->notice->niveau_biblio == "m" && $this->notice->niveau_hierar==0 && $this->notice->nocoll && $this->notice->year && $this->notice->coll_id){
			$requete = "SELECT * FROM collections WHERE collection_id=".$this->notice->coll_id." LIMIT 1 ";
			$result = @pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->notice_header.=" / ".$temp->collection_name.", ".$this->notice->nocoll.", ".$this->notice->year.", ";
			}
		}
		
		// champ perso de notice cp_cote (Cote)
		$notice_cote_cee= $this->p_perso->get_val_field($this->notice_id ,"cp_cote") ;	
		
		$notice_rel_cee="";
		$notice_expl_dispo_cee="";
		
        if($this->bulletin_id){
         	$expl_bulletin=$this->bulletin_id;
           	$expl_notice=0;
		}else{            	
         	$expl_notice=$this->notice_id;
           	$expl_bulletin=0;
		}
		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* ";
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders ";
		$requete .= " WHERE expl_notice='".$expl_notice."' and expl_bulletin='".$expl_bulletin."'";
		$requete .= " AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1";			
		$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
		$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
		$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " AND exemplaires.expl_typdoc=docs_type. idtyp_doc ";
		$requete .= " AND exemplaires.expl_codestat=docs_codestat.idcode ";
		$requete .= " AND exemplaires.expl_owner=lenders.idlender ";		 
		$res = pmb_mysql_query($requete);
		
		if(pmb_mysql_num_rows($res)){
			while($expl = pmb_mysql_fetch_object($res)){
				$requete_resa = "SELECT count(1) from resa where resa_cb='$expl->expl_cb' ";
				$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa),0,0);
				if ($flag_resa || !$expl->pret_flag || $expl->pret_retour) {
					// exemplaire non dispo
					if($expl->pret_retour){
						// en pret
						$expl_en_pret=1;
					}
				}else{
					//$notice_expl_dispo_cee=" ( disponible ) ";
					$expl_dispo=1;
					break;
				}
			}	
		
			if( !$expl_dispo && $expl_en_pret){
				$notice_expl_dispo_cee=" ( emprunté ) ";
				// pas de dispo et en pret: on propose le lien de réservation
				if($_SESSION["user_code"]){
					
					if ($opac_resa_popup) $notice_expl_dispo_cee .= "<a href='#' onClick=\"if(confirm('".$msg["confirm_resa"]."')){w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&id_bulletin=".$this->bulletin_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;}else return false;\" id=\"bt_resa\">".$msg["bulletin_display_place_resa"]."</a>" ;
					else $notice_expl_dispo_cee .= "<a href='./do_resa.php?lvl=resa&id_notice=".$this->notice_id."&id_bulletin=".$this->bulletin_id."&oresa=popup' onClick=\"return confirm('".$msg["confirm_resa"]."')\" id=\"bt_resa\">".$msg["bulletin_display_place_resa"]."</a>" ;		
				}
			}
		}
		if(($this->notice->typdoc=='o') || ($this->notice->typdoc=='v')){		
			if($this->parents_in){
				$notice_parent_in_cee=$this->parents_in;
			}
		}
		
		$this->notice_header .= $notice_rel_cee ." ". $notice_cote_cee . $notice_parent_in_cee;
		
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressources électroniques			
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\">";
			$this->notice_header_doclink .= "</a></span>";			
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";		
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				$this->notice_header_doclink .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;
		
		
		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;
		
		$this->notice_header_doclink.=$notice_expl_dispo_cee;
		
		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
	} // fin do_header()
		
	// Construction des parents-----------------------------------------------------
	function do_parents() {
		global $dbh;
		global $msg;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $relation_listup, $parents_to_childs ;
		
		// Pour ne pas afficher en parents les liens transférer dans les childs
		if (sizeof($parents_to_childs)>0) $clause = " AND relation_type not in ('".implode("','", $parents_to_childs)."') ";

		// gestion des droits d'affichage des parents
		if (is_null($this->dom_2)) {
			$acces_j='';
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		} else {
			$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			$statut_j = "";
			$statut_r = "";	
		}
		
		//Recherche des notices parentes
		$requete="select linked_notice, relation_type, rank from notices_relations join notices on notice_id=linked_notice $acces_j $statut_j 
				where num_notice=".$this->notice_id." $clause $statut_r
				order by relation_type,rank";
		$result_linked=pmb_mysql_query($requete,$dbh);
		//Si il y en a, on prépare l'affichage
		if (!pmb_mysql_num_rows($result_linked)) {
			$this->parents = "";
			return ;
		}

		$this->parents = "";
		
		$this->parents_in = "";
		if (!$relation_listup) $relation_listup=new marc_list("relationtypeup");
		
		$r_type=array();
		$ul_opened=false;
		//Pour toutes les notices liées
		while (($r_rel=pmb_mysql_fetch_object($result_linked))) {			
			if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
			
			
//			if($memo_notice[$r_rel->linked_notice]["header"]) {
//				$parent_notice->notice_header=$memo_notice[$r_rel->linked_notice]["header"];	
//			} else {
//				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,1,$this->to_print,1);
//				$parent_notice->visu_expl = 0 ;
//				$parent_notice->visu_explnum = 0 ;
//				$parent_notice->do_header();
//			}		
			
			if(!$memo_notice[$r_rel->linked_notice]["header_without_doclink"]) {
				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,1,$this->to_print,1);
				$parent_notice->visu_expl = 0 ;
				$parent_notice->visu_explnum = 0 ;
				$parent_notice->do_header();
			}		
			//Présentation différente si il y en a un ou plusieurs
			if (pmb_mysql_num_rows($result_linked)==1) {
				// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this>bulletin_id
				$this->parents.="<br /><b>".$relation_listup->table[$r_rel->relation_type]."</b> ";
				if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				//$this->parents.=$parent_notice->notice_header;
				$this->parents.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
				if ($this->lien_rech_notice) $this->parents.="</a>";
				$this->parents.="<br /><br />";
				// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this->bulletin_id
				$rqbull="select bulletin_id from bulletins where num_notice=".$this->notice_id;
				$rqbullr=pmb_mysql_query($rqbull);
				$rqbulld=@pmb_mysql_fetch_object($rqbullr);
				$this->bulletin_id=$rqbulld->bulletin_id; 
				
				if($relation_listup->table[$r_rel->relation_type]== "in"){
					$this->parents_in="<b>".$relation_listup->table[$r_rel->relation_type]."</b> ";
					if ($this->lien_rech_notice) $this->parents_in.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
					//$this->parents.=$parent_notice->notice_header;
					$this->parents_in.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
					if ($this->lien_rech_notice) $this->parents_in.="</a>";
				}				
			} else {
				if (!$r_type[$r_rel->relation_type]) {
					$r_type[$r_rel->relation_type]=1;
					if ($ul_opened) $this->parents.="</ul>"; 
					else { 
						$this->parents.="<br />"; 
						$ul_opened=true; 
					}
					$this->parents.="<b>".$relation_listup->table[$r_rel->relation_type]."</b>";
					$this->parents.="<ul class='notice_rel'>\n";
				}
				$this->parents.="<li>";
				if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				//$this->parents.=$parent_notice->notice_header;
				$this->parents.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
				if ($this->lien_rech_notice) $this->parents.="</a>";
				$this->parents.="</li>\n";
			}
		}
		if (pmb_mysql_num_rows($result_linked)>1) 
				$this->parents.="</ul>\n";
	return ;
	} // fin do_parents()
	function affichage_etat_collections() {
		global $msg;
		global $pmb_etat_collections_localise;
		global $tpl_collstate_liste,$tpl_collstate_liste_line;
		
		$tpl_collstate_liste[2]="
		<table class='exemplaires' cellpadding='2' width='100%'>
			<tbody>
				<tr>
					<th>".$msg["collstate_form_emplacement"]."</th>		
					<th>".$msg["collstate_form_cote"]."</th>
					<th>".$msg["collstate_form_collections"]."</th>
					<th>".$msg["collstate_form_archive"]."</th>
					<th>".$msg["collstate_form_lacune"]."</th>		
				</tr>
				!!collstate_liste!!	
			</tbody>	
		</table>
		";		
		$tpl_collstate_liste_line[2]="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
			<!-- surloc -->
			<td !!tr_javascript!! >!!emplacement_libelle!!</td>
			<td !!tr_javascript!! >!!cote!!</td>
			<td !!tr_javascript!! >!!state_collections!!</td>
			<td !!tr_javascript!! >!!archive!!</td>
			<td !!tr_javascript!! >!!lacune!!</td>
		</tr>";
			
		$collstate=new collstate(0,$this->notice_id);			
		$collstate->get_display_list("",0,0,0,2);
	
		if($collstate->nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}
		return $affichage;
	} // fin affichage_etat_collections()
	
}// fin class notice_affichage_cee



class notice_affichage_biotope extends notice_affichage {
	
	// fonction d'affichage des exemplaires numeriques
	function aff_explnum () {
		
		global $opac_show_links_invisible_docnums;
		global $msg;
		$ret='';
		if ($opac_show_links_invisible_docnums || (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)){
			if ($this->notice->niveau_biblio=="b" && ($explnum = $this->show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			} elseif (($explnum = $this->show_explnum_per_notice($this->notice_id,0, ''))) {
				$ret .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
				$this->affichage_expl .= "<a name='docnum'><h3><span id='titre_explnum'>$msg[explnum]</span></h3></a>".$explnum;
			}
		}		 
		return $ret;
	} // fin aff_explnum ()
	
		
	// fonction retournant les infos d'exemplaires numériques pour une notice ou un bulletin donné
	function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='') {
		
		global $class_path;
		require_once($class_path."/auth_popup.class.php");
		
		// params :
		// $link_expl= lien associé à l'exemplaire avec !!explnum_id!! à mettre à jour
		global $dbh,$msg,$charset;
		global $opac_url_base ;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_explnum_order;
		global $opac_show_links_invisible_docnums;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		if (!$no_notice && !$no_bulletin) return "";
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		create_tableau_mimetype() ;
		
		// récupération du nombre d'exemplaires
		$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier FROM explnum WHERE ";
		if ($no_notice && !$no_bulletin) $requete .= "explnum_notice='$no_notice' ";
		elseif (!$no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' ";
		elseif ($no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' or explnum_notice='$no_notice' ";
		if ($opac_explnum_order) $requete .= " order by ".$opac_explnum_order;
		else $requete .= " order by explnum_mimetype, explnum_nom, explnum_id ";
		$res = pmb_mysql_query($requete, $dbh);
		$nb_ex = pmb_mysql_num_rows($res);
		
		$docnum_visible = true;
		$id_for_right = $no_notice;
		if($no_bulletin){
			$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$no_bulletin;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$infos = pmb_mysql_fetch_object($result);
				if($infos->num_notice){
					$id_for_right = $infos->num_notice;
				}else{
					$id_for_right = $infos->bulletin_notice;	
				}
			}
		}
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
		} else {
			$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
			$myQuery = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($myQuery)) {
				$statut_temp = pmb_mysql_fetch_object($myQuery);
				if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
				if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
			} else 	$docnum_visible=false;
		}
		
		
		//on peut appeller cette méthode sans avoir le droit de voir les documents...
		if(!$docnum_visible && $opac_show_links_invisible_docnums){
			$auth_popup = new auth_popup();
		}
	
		if ($nb_ex) {
			// on récupère les données des exemplaires
			$i = 1 ;
			global $search_terms;
			
			while (($expl = pmb_mysql_fetch_object($res))) {
				if ($i==1) $ligne="<tr><td class='docnum' width='33%'>!!1!!</td><td class='docnum' width='33%'>!!2!!</td><td class='docnum' width='33%'>!!3!!</td></tr>" ;
				if ($link_expl) {
					$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
					$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
					$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
					} 
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0'>";
					else // trouver l'icone correspondant au mime_type
						$obj="<img src='".$opac_url_base."images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0'>";		
				$expl_liste_obj = "<center>";
				
				$words_to_find="";
				if (($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					if (is_array($search_terms)) {
						$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
					} 
				}
				//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
				if(!$docnum_visible && !$_SESSION['user_code'] && $opac_show_links_invisible_docnums){
					if ($opac_visionneuse_allow)
						$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
					if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
						$link="
							<script type='text/javascript'>
								if(typeof(sendToVisionneuse) == 'undefined'){
									var sendToVisionneuse = function (explnum_id){
										document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
									}
								}
								function sendToVisionneuse_".$expl->explnum_id."(){
									open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");
								}
							</script>
							<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$expl->explnum_id."');\" alt='$alt' title='$alt'>".$obj."</a><br />";
						$expl_liste_obj .=$link;
					}else{
					$link="
							<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$expl->explnum_id)."')\" alt='$alt' title='$alt'>".$obj."</a><br />";
						$expl_liste_obj .=$link;
					}
				}else{
					if ($opac_visionneuse_allow)
						$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
					if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
						$link="
							<script type='text/javascript'>
								if(typeof(sendToVisionneuse) == 'undefined'){
									var sendToVisionneuse = function (explnum_id){
										document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
									}
								}
							</script>
							<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;\" alt='$alt' title='$alt'>".$obj."</a><br />";
						$expl_liste_obj .=$link;
						
						$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
						$expl_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' alt='$alt' title='$alt' target='_blank'>".htmlentities($msg['download'],ENT_QUOTES,$charset)."</a><br />" ;
						
					} else {
						
						$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
						$expl_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' alt='$alt' title='$alt' target='_blank'>".$obj."</a><br />" ;
						
					}
	
				}
	
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
				elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
				else $explmime_nom = $expl->explnum_mimetype ;
				
				
				if ($tlink) {
					$expl_liste_obj .= "<a href='$tlink'>";
					$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
					} else {
						$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
						}
				$expl_liste_obj .= "</center>";
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
					}
				}
			if (!$ligne_finale) $ligne_finale = $ligne ;
			elseif ($i!=1) $ligne_finale .= $ligne ;
			$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
			$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
			
			} else return "";
		$entry .= "<table class='docnum'>$ligne_finale</table>";
		return $entry;
	
	}
		
}// fin class notice_affichage_biotope

class notice_affichage_esc_rennes extends notice_affichage {
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_url_base;
		global $charset;
		
		$this->notice_header="";		
		if(!$this->notice_id) return;	
		
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($opac_notice_reduit_format,2);
			if($id_tpl){			
				$tpl = new notice_tpl_gen($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);		
				if($notice_tpl_header){						
					$this->notice_header=$notice_tpl_header;
					return;
				}
			}	
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
		
		if ($type_reduit=="E") {
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			} elseif ($this->notice->year) { 
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
		
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
		
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 $aff_perio_title="<i>".$msg[in_serial]." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}
		
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";

		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
		
		if ($this->notice_header) $this->notice_header .= ". ".$this->notice->tit1 ;
		else $this->notice_header = $this->notice->tit1;
		
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $this->notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;	
		}
		
		$this->notice_header .= $aff_bullperio_title;
		if ($this->notice->niveau_biblio =='m') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $this->notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $this->notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		
		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;
		
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		$affiche_auteurs="";
		if(count($this->responsabilites["auteurs"])){
			for ( $index = 0; (($index < 3) && ($index < count($this->responsabilites["auteurs"]))); $index++ ) {
				if($affiche_auteurs)$affiche_auteurs.=" ; ";
				$affiche_auteurs.=$this->responsabilites["auteurs"][$index]["auteur_titre"];
			}
		}
		if ($type_reduit!='3' && $affiche_auteurs) $notice_header_suite .= " / ".$affiche_auteurs;
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		//Un  span de trop ?	
		$this->notice_header .= $notice_header_suite;
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressources électroniques			
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<input class=\"bouton\" type=\"button\" value=\"";
			if($this->notice->typdoc=='z' || $this->notice->typdoc=='1') {
				$this->notice_header_doclink .= $msg["link_ebook"];
			} else {
				$this->notice_header_doclink .= $msg["link_not_ebook"];
			}
			$this->notice_header_doclink .= "\">";
			$this->notice_header_doclink .= "</a></span>";			
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";		
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				$this->notice_header_doclink .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;
				
		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;

	} // fin do_header()
	

	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		
		$this->notice_public= $this->genere_in_perio ();
		if(!$this->notice_id) return;

		// Notices parentes
		//$this->notice_public.=$this->parents;
			
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
		
		if ($this->notice->statut == 3){
			$myStatut = "";
			$requete = "SELECT opac_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$myQuery = pmb_mysql_query($requete, $dbh);
			if (pmb_mysql_num_rows($myQuery)) {
				$statut = pmb_mysql_fetch_object($myQuery);
				$myStatut = $statut->opac_libelle ; 
			}
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['notice_statut'].":</span></td><td>".$myStatut."</td></tr>" ;
		}
		
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
		
		//ESCR et cp_vol_per (variable temp)
		$perso_aff_cp_vol_per = "" ;
		if (!$this->p_perso->no_special_fields) {
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"] && $p["NAME"]=='cp_vol_per') $perso_aff_cp_vol_per .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				elseif ($p['OPAC_SHOW'] && $p["AFF"] && $p["NAME"]=='ESCR') $this->notice_public .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
			}
		}
		
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
		
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		$this->notice_public.="</span></td></tr>";

		if ($this->notice->tit2) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
		
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td class='author_content'>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
		
		//notice mère
		$this->notice_public .= $this->aff_parents();

		//cp_vol_per
		$this->notice_public .= $perso_aff_cp_vol_per;
//		$perso_aff = "" ;
//		if (!$this->p_perso->no_special_fields) {
//			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
//			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
//				$p=$this->memo_perso_["FIELDS"][$i];
//				if ($p['OPAC_SHOW'] && $p["AFF"] && $p["NAME"]=='cp_vol_per') $this->notice_public .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
//			}
//		}
		
		// mention d'édition
		if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
		
		// zone de l'éditeur 
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);			
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);			
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}		
		//Année édition
		if ($this->notice->year) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
		}
		
		// collection  
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
		}
		
		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		$this->notice_public .= $annee ;
	
		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->notice_public.= 
			"<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}	
		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}
		if ($this->notice->ill) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if ($this->notice->size) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			
		// ISBN ou NO. commercial
		if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		//if ($this->notice->prix) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
		}
		if (!$short) $this->notice_public .= $this->aff_suite() ; 
		else $this->notice_public.=$this->genere_in_perio();
	
		$this->notice_public.="</table>\n";
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	} // fin do_public($short=0,$ex=1)
	
	function affichage_etat_collections() {
		global $msg;
		global $pmb_etat_collections_localise;
		global $tpl_collstate_liste,$tpl_collstate_liste_line;
		
		$tpl_collstate_liste[2]="
		<table class='exemplaires' cellpadding='2' width='100%'>
			<tbody>
				<tr>
					<th>".$msg["collstate_form_emplacement"]."</th>		
					<th>".$msg["collstate_form_support"]."</th>
					<th>".$msg["collstate_form_statut"]."</th>				
					<th>".$msg["collstate_form_collections"]."</th>
					<th>".$msg["collstate_form_archive"]."</th>
					<th>".$msg["collstate_form_lacune"]."</th>		
				</tr>
				!!collstate_liste!!	
			</tbody>	
		</table>
		";
		
		$tpl_collstate_liste_line[2]="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
			<!-- surloc -->
			<td !!tr_javascript!! >!!emplacement_libelle!!</td>
			<td !!tr_javascript!! >!!type_libelle!!</td>
			<td !!tr_javascript!! >!!statut_libelle!!</td>	
			<td !!tr_javascript!! >!!state_collections!!</td>
			<td !!tr_javascript!! >!!archive!!</td>
			<td !!tr_javascript!! >!!lacune!!</td>
		</tr>";
		
		$tpl_collstate_liste[3]="
		<table class='exemplaires' cellpadding='2' width='100%'>
			<tbody>
				<tr>
					<!-- surloc -->
					<th>".$msg["collstate_form_localisation"]."</th>		
					<th>".$msg["collstate_form_emplacement"]."</th>		
					<th>".$msg["collstate_form_support"]."</th>
					<th>".$msg["collstate_form_statut"]."</th>		
					<th>".$msg["collstate_form_collections"]."</th>
					<th>".$msg["collstate_form_archive"]."</th>
					<th>".$msg["collstate_form_lacune"]."</th>		
				</tr>
				!!collstate_liste!!
			</tbody>	
		</table>
		";
		
		$tpl_collstate_surloc_liste = "<th>".$msg["collstate_form_surloc"]."</th>";
		
		$tpl_collstate_liste_line[3]="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
			<!-- surloc -->
			<td !!tr_javascript!! >!!localisation!!</td>
			<td !!tr_javascript!! >!!emplacement_libelle!!</td>
			<td !!tr_javascript!! >!!type_libelle!!</td>	
			<td !!tr_javascript!! >!!statut_libelle!!</td>
			<td !!tr_javascript!! >!!state_collections!!</td>
			<td !!tr_javascript!! >!!archive!!</td>
			<td !!tr_javascript!! >!!lacune!!</td>
		</tr>";

		$collstate=new collstate(0,$this->notice_id);
		if($pmb_etat_collections_localise) {
			$collstate->get_display_list("",0,0,0,3);
		} else { 	
			$collstate->get_display_list("",0,0,0,2);
		}
		if($collstate->nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}

		return $affichage;
	} // fin affichage_etat_collections()
	
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;
		
		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;
		
		// toutes indexations
		$ret_index = "";
		// Catégories
		if ($this->categories_toutes) $ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
				
		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
		}
				
		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente	
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];
				
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles) $ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";
			
		//calculs champs persos et affichage keywords
		$perso_aff = "" ;
		$libKeywords = "";
		$valKeywords = "";
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]){
					if ($p["NAME"] != "cp_mc_eng" && $p["NAME"] != "cp_vol_per" && $p["NAME"] != "ESCR") {
						$perso_aff .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
					} elseif($p["NAME"] == "cp_mc_eng") {
						$libKeywords = strip_tags($p["TITRE"]);
						$valKeywords = $p["AFF"];
					}
				}
			}
		}
		if(trim($libKeywords)){
			$ret_index .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$libKeywords."</span></td><td>".$valKeywords."</td></tr>";
		}
		
		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}
		if ($ret_index) {
			$ret.=$ret_index;
		}
		
		// résumé
		if($this->notice->n_resume) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";
	
		// note de contenu
		if($this->notice->n_contenu) $ret .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";
	
		//Champs personalisés (calculés précédemment)
		$ret .= $perso_aff ;
		
		if ($this->notice->lien) {
			$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				if (strlen($this->notice->lien)>80) {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
				} else {
					$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
				}
			}
			$ret.="</td></tr>";
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$ret.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()
	
	function aff_parents() {
		global $dbh;
		global $msg;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $relation_listup, $parents_to_childs ;
		
		// Pour ne pas afficher en parents les liens transférer dans les childs
		if (sizeof($parents_to_childs)>0) $clause = " AND relation_type not in ('".implode("','", $parents_to_childs)."') ";

		// gestion des droits d'affichage des parents
		if (is_null($this->dom_2)) {
			$acces_j='';
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		} else {
			$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			$statut_j = "";
			$statut_r = "";	
		}
		
		//Recherche des notices parentes
		$requete="select linked_notice, relation_type, rank from notices_relations join notices on notice_id=linked_notice $acces_j $statut_j 
				where num_notice=".$this->notice_id." $clause $statut_r
				order by relation_type,rank";
		$result_linked=pmb_mysql_query($requete,$dbh);
		//Si il y en a, on prépare l'affichage
		if (!pmb_mysql_num_rows($result_linked)) {
			return ;
		}

		$retour = "";
		
		if (!$relation_listup) $relation_listup=new marc_list("relationtypeup");
		$r_type=array();
		//Pour toutes les notices liées
		while (($r_rel=pmb_mysql_fetch_object($result_linked))) {			
			if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
			
			if(!$memo_notice[$r_rel->linked_notice]["header_without_doclink"]) {
				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,1,$this->to_print,1);
				$parent_notice->visu_expl = 0 ;
				$parent_notice->visu_explnum = 0 ;
				$parent_notice->do_header();
			}	
			if (!$r_type[$r_rel->relation_type]) {
				$r_type[$r_rel->relation_type]=1;
				$retour.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$relation_listup->table[$r_rel->relation_type]."</span></td>";
			}
			$retour .= "<td>";
			if ($this->lien_rech_notice) $retour.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
			$retour.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
			if ($this->lien_rech_notice) $retour.="</a>";
			$retour .="</td></tr>";
		}
	return $retour;
	} // fin aff_parents()
	
	// fonction de génération du tableau des exemplaires
	function expl_list($type,$id,$bull_id=0,$build_ifempty=1) {	
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		global $opac_sur_location_activate,$opac_view_filter_class;

	$nb_expl_autre_loc=0;
	$nb_perso_aff=0;
		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;
		
		if($opac_sur_location_activate){
			$opac_sur_location_select=", sur_location.*";
			$opac_sur_location_from=", sur_location";
			$opac_sur_location_where=" AND docs_location.surloc_num=sur_location.surloc_id";
		}
		if($opac_view_filter_class){
			$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
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
		
		// récupération du nombre d'exemplaires
		$res = pmb_mysql_query($requete, $dbh);
		if(!$build_ifempty && !pmb_mysql_num_rows($res))return"";
		$surloc_field="";
		if ($opac_sur_location_activate==1) $surloc_field="surloc_libelle,";
		if (!$opac_expl_data) $opac_expl_data="expl_cb,expl_cote,tdoc_libelle,".$surloc_field."location_libelle,section_libelle";
		$colonnesarray=explode(",",$opac_expl_data);
		
		$expl_list_header_deb="";
		for ($i=0; $i<count($colonnesarray); $i++) {
			eval ("\$colencours=\$msg[expl_header_".$colonnesarray[$i]."];");
			$expl_list_header_deb.="<th class='expl_header_".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
		}
		$expl_list_header_deb.="<th>$msg[statut]</th>";
		$expl_liste="";
		$nb_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
		while(($expl = pmb_mysql_fetch_object($res))) {
			$compteur = $compteur+1;
			$expl_liste .= "<tr>";
			$colencours="";

			//Gestion de la couleur de la cote selon le code stat
			$color='';
			for ($i=0; $i<count($colonnesarray); $i++) {
				eval ("\$tmpColencours=\$expl->".$colonnesarray[$i].";");
				if ($colonnesarray[$i]=="codestat_libelle"){
					if(strripos($tmpColencours, 'red label')){
						$color='red';
					}elseif(strripos($tmpColencours, 'green label')){
						$color='green';
					}
				}
			}
			for ($i=0; $i<count($colonnesarray); $i++) {
				eval ("\$colencours=\$expl->".$colonnesarray[$i].";");
				if ($colonnesarray[$i]=="location_libelle" && $expl->num_infopage) {
					if ($expl->surloc_id != "0") $param_surloc="&surloc=".$expl->surloc_id;
					else $param_surloc="";
					$expl_liste.="<td class='".$colonnesarray[$i]."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl->num_infopage."&location=".$expl->expl_location.$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".htmlentities($colencours, ENT_QUOTES, $charset)."</a></td>";
				} else{
					$expl_liste.="<td class='".$colonnesarray[$i]."'>";
					if(trim($color) && $colonnesarray[$i]=="expl_cote"){
						$expl_liste.="<span style='color:".$color."'>";
					}
					$expl_liste.=htmlentities($colencours,ENT_QUOTES, $charset);
					if(trim($color) && $colonnesarray[$i]=="expl_cote"){
						$expl_liste.="</span>";
					}
					$expl_liste.="</td>";
				}
			}
	
			$requete_resa = "SELECT count(1) from resa where resa_cb='$expl->expl_cb' ";
			$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$requete_resa = "SELECT count(1) from resa_ranger where resa_cb='$expl->expl_cb' ";
			$flag_resa = $flag_resa + pmb_mysql_result(pmb_mysql_query($requete_resa, $dbh),0,0);
			$situation = "";
			if ($expl->statut_libelle_opac != "") $situation .= $expl->statut_libelle_opac."<br />";
			if ($flag_resa) {
				$nb_resa--;
				$situation .= "<strong>$msg[expl_reserve]</strong>";
			} else {
				if ($expl->pret_flag) {
					if($expl->pret_retour) { // exemplaire sorti
						global $opac_show_empr ;
						if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
							$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl->pret_idempr' ";
							$res_empr = pmb_mysql_query($rqt_empr, $dbh) ;
							$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
							$situation .= $msg[entete_show_empr].htmlentities(" $res_empr_obj->empr_prenom $res_empr_obj->empr_nom",ENT_QUOTES, $charset)."<br />";
						} 
						$situation .= "<strong>$msg[out_until] ".formatdate($expl->pret_retour).'</strong>';
						// ****** Affichage de l'emprunteur
					} else { // pas sorti
						$situation .= "<strong>".$msg['available']."</strong>";
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if (($pmb_transferts_actif=="1")&&("".$expl->expl_statut.""==$transferts_statut_transferts)) {
						$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>"; 
					} else {
						$situation .= "<strong>".$msg['exclu']."</strong>";
					}
				}
			} // fin if else $flag_resa 
			$expl_liste .= "<td class='expl_situation'>$situation </td>";
			
			//Champs personalisés
			$perso_aff = "" ;
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_=$memo_p_perso_expl->show_fields($expl->expl_id);
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
			if($expl->expl_location==$_SESSION["empr_location"]) {
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
		if(!$expl_liste_loc) $expl_liste_loc="<tr class=even><td colspan='".(count($colonnesarray)+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";	
		$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);	
		$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);	
		$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
		$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
	} else {
		// affichage de la liste d'exemplaires calculée ci-dessus
		if (!$expl_liste_all && $opac_show_empty_items_block==1) {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($colonnesarray)+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
		} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
			$expl_liste_all = ""; 
		} else {
			$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
		}
	}
	return $expl_liste_all;
		
	} // fin function expl_list

}// fin class notice_affichage_esc_rennes

class notice_affichage_esa extends notice_affichage{
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {
	
		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_url_base;
		global $charset;

		$this->notice_header="";
		if(!$this->notice_id){
			return;
		}
		//Affichage perso pour header notice esa abacarisse
		$notice_header_suite = "";

		if($this->notice->niveau_biblio=='a' && $this->notice->niveau_hierar=='2'){
			//Articles
			$this->notice_header=$this->notice->tit1;
			
			if($this->notice->tit4){
				$this->notice_header.=' : '.$this->notice->tit4;
			}
			
			$this->notice_header.=' ';
			
			if($this->auteurs_principaux){
				$this->notice_header.='/ '.$this->auteurs_principaux;
			}
			$this->notice_header.=' / ';
			
			
			$first=true;
			if($this->parent_title){
				if(!$first){
					$this->notice_header.=', ';
				}
				$this->notice_header.=$this->parent_title;
				$first=false;
			}
				
				
			if($this->notice->year){
				if(!$first){
					$this->notice_header.=', ';
				}
					
				$this->notice_header.=$this->notice->year;
				$first=false;
			}
		}else{
			if($this->notice->typdoc=='u'){
				//Mémoire
				$this->notice_header=$this->notice->tit1;
			
				if($this->notice->tit4){
					$this->notice_header.=' : '.$this->notice->tit4;
				}
			
				$this->notice_header.=' ';
			
				if($this->auteurs_principaux){
					$this->notice_header.='/ '.$this->auteurs_principaux;
				}
				$this->notice_header.=' / ';
			
			
				$first=true;
			
				if($this->notice->year){
					if(!$first){
						$this->notice_header.=', ';
					}
						
					$this->notice_header.=$this->notice->year;
					$first=false;
				}
			
				// COTE
				$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$this->notice_id.' LIMIT 1';
				$res = pmb_mysql_query($req);
				if(pmb_mysql_result($res,0,0)){
					$match=array();
					if(preg_match('/^[a-z]{1,3}-[\d]{1,4}-[\d]{1,3}/i', pmb_mysql_result($res,0,0),$match)){
						if(!$first){
							$this->notice_header.=', ';
						}
						$this->notice_header.=$match[0];
					}
				}
				unset($res);
				unset($req);
					
			}else{
				//Livres
				$this->notice_header=$this->notice->tit1;
			
				if($this->notice->tit4){
					$this->notice_header.=' : '.$this->notice->tit4;
				}
			
				$this->notice_header.=' ';
					
				if($this->auteurs_principaux){
					$this->notice_header.='/ '.$this->auteurs_principaux;
				}
				$this->notice_header.=' / ';
			
				$first=true;
			
				if($this->notice->ed1_id){
					if(!$first){
						$this->notice_header.=', ';
					}
						
					$editeur=new publisher($this->notice->ed1_id);
						
					$this->notice_header.=$editeur->name;
					$first=false;
				}
			
				if($this->notice->year){
					if(!$first){
						$this->notice_header.=', ';
					}
						
					$this->notice_header.=$this->notice->year;
					$first=false;
				}
					
				// COTE
				$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$this->notice_id.' LIMIT 1';
				$res = pmb_mysql_query($req);
				if(pmb_mysql_result($res,0,0)){
					$match=array();
					if(preg_match('/^[a-z]{1,3}-[\d]{1,4}-[\d]{1,3}/i', pmb_mysql_result($res,0,0),$match)){
						if(!$first){
							$this->notice_header.=', ';
						}
						$this->notice_header.=$match[0];
					}
				}
				unset($res);
				unset($req);
			}
		}
		
		$this->notice_header_without_html = $this->notice_header;
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else {
			$this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		}
	
		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;
	
		$this->notice_header .= $notice_header_suite;
		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat){
				$info_bulle=$msg["open_link_url_notice"];
			}else{
				$info_bulle=$this->notice->eformat;
			}
			// ajout du lien pour les ressources électroniques
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" />";
			$this->notice_header_doclink .= "</a></span>";
		}
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);
	
		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier){
						$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					}else{ 
						$info_bulle=$explnumrow->explnum_nom;
					}
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url){
						$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					}else{
						$info_bulle=$explnumrow->explnum_nom;
					}
				}
				$this->notice_header_doclink .= "&nbsp;<span>";
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
						var sendToVisionneuse = function (explnum_id){
						document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
					}
					}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				$this->notice_header_doclink .= "<img src=\"./images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"./images/globe_rouge.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
	
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;


		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;

		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
		
	} // fin do_header()
	
	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $opac_permalink;
		
		$this->notice_public= $this->genere_in_perio ();
		if(!$this->notice_id){
			return;
		}
	
		// Notices parentes
		$this->notice_public.=$this->parents;
			
		$this->notice_public .= "<table>";
		
		if($this->notice->niveau_biblio=='a' && $this->notice->niveau_hierar=='2'){
			//Articles
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			
			//titres et sous titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1.' ';
			if ($this->notice->tit4){
				$this->notice_public .= ": ".$this->notice->tit4.' ';
			}
			if($this->auteurs_principaux){
				$this->notice_public .='/ '.$this->auteurs_principaux;
			}
			$this->notice_public.="</span></td></tr>";
			
			//Auteurs
			if ($this->auteurs_tous){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			}
			if ($this->congres_tous){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			}
			
			// zone de la collation
			if($this->notice->npages){
				$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['pagination_start'].":</span></td><td>".$this->notice->npages."</td></tr>";
			}
			
			//DANS
			$dans='';
			if($this->parent_title){
				$dans.=inslink($this->parent_title,'./index.php?lvl=notice_display&id='.$this->parent_id).', ';
			}
			if($this->parent_numero){
				$dans.=$this->parent_numero.', ';
			}
			if($this->notice->year){
				$dans.=$this->notice->year;
			}
			if($dans){
				$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['dans_start']." :</span></td><td>".$dans."</td></tr>";
			}
				
			//categories
			if($this->categories_toutes){
				$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['sujets_start']." :</span></td><td>".$this->categories_toutes."</td></tr>";
			}
			
			//resumé
			if($this->notice->n_resume){
				$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['267']." :</span></td><td>".$this->notice->n_resume."</td></tr>";
			}
		}else{
			if($this->notice->typdoc=='u'){
				//Memoires
				//type de doc
				if ($tdoc->table[$this->notice->typdoc]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
				}
				
				//Collection
				if ($this->notice->coll_id) {
					$collection = new collection($this->notice->coll_id);
					$this->collections[]=$collection;
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>Formation :</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
					$this->notice_public .=" ".$collection->collection_web_link."</td></tr>";
				}
					
				//titres et sous titre
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
				$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1.' ';
				if ($this->notice->tit4){
					$this->notice_public .= ": ".$this->notice->tit4.' ';
				}
				if($this->auteurs_principaux){
					$this->notice_public .='/ '.$this->auteurs_principaux;
				}
				$this->notice_public.="</span></td></tr>";
			
				//Auteurs
				if ($this->auteurs_tous){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
				}
				if ($this->congres_tous){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
				}
			
				//année
				if($this->notice->year){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
				}
			
				//langues
				if(sizeof($this->langues)){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
					if (count($this->languesorg)){
						$this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
					}
					$this->notice_public.="</td></tr>";
				} elseif (sizeof($this->languesorg)) {
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
				}
				
				//note générale
				if($this->notice->n_gen){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['collstate_form_note']." :</span></td><td>".$this->notice->n_gen."</td></tr>";
				}
					
				$this->p_perso->get_values($this->notice_id) ;
				//Entreprise
				if($this->p_perso->values[10][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['entreprise_start']." :</span></td><td>".$this->p_perso->get_formatted_output($this->p_perso->values[10],10)."</td></tr>";
				}
					
				//LIEU
				$lieu='';
				if($this->p_perso->values[11][0]){
					$lieu.=$this->p_perso->get_formatted_output($this->p_perso->values[11],11);
				}
				if($lieu){
					$lieu.='&nbsp;';
				}
				if($this->p_perso->values[8][0]){
					$lieu.=$this->p_perso->get_formatted_output($this->p_perso->values[8],8);
				}
				if($lieu){
					$lieu.=',&nbsp;';
				}
				if($this->p_perso->values[9][0]){
					$lieu.=$this->p_perso->get_formatted_output($this->p_perso->values[9],9);
				}
					
				if($lieu){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['lieu_start']." :</span></td><td>".$lieu." </td></tr>";
				}
					
				//categories
				if($this->categories_toutes){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['sujets_start']." :</span></td><td>".$this->categories_toutes."</td></tr>";
				}
			
				//resumé
				if($this->notice->n_resume){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['267']." :</span></td><td>".$this->notice->n_resume."</td></tr>";
				}
				
			}elseif($this->notice->typdoc=='q'){
				//Périodiques
				$this->p_perso->get_values($this->notice_id) ;
				
				//type de doc
				if ($tdoc->table[$this->notice->typdoc]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
				}
					
				//titres et sous titre
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
				$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1.'</span></td></tr>';
				
				//Ancien titre
				if($this->p_perso->values[4][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['old_title_start']." :</span></td>";
					$this->notice_public .= "<td><span class='public_title'>".$this->p_perso->get_formatted_output($this->p_perso->values[4],4)."</span></td></tr>";
				}
				//Nouveau titre
				if($this->p_perso->values[5][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['new_title_start']." :</span></td>";
					$this->notice_public .= "<td><span class='public_title'>".$this->p_perso->get_formatted_output($this->p_perso->values[5],5)."</span></td></tr>";
				}
				
				//langues
				if(sizeof($this->langues)){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
					if (count($this->languesorg)){
						$this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
					}
					$this->notice_public.="</td></tr>";
				} elseif (sizeof($this->languesorg)) {
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
				}
				
				//Périodicité
				if($this->p_perso->values[13][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['periodicite_start']." :</span></td>";
					$this->notice_public .= "<td><span class='public_title'>".$this->p_perso->get_formatted_output($this->p_perso->values[13],13)."</span></td></tr>";
				}
				
				//Abonnement à la revue en ligne
				if($this->p_perso->values[17][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['abo_revue_ligne_start']." :</span></td>";
					$this->notice_public .= "<td><span class='public_title'>".$this->p_perso->get_formatted_output($this->p_perso->values[17],17)."</span></td></tr>";
				}
				
				//Commentaires
				if($this->p_perso->values[6][0]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['commentaires_start']." :</span></td>";
					$this->notice_public .= "<td><span class='public_title'>".$this->p_perso->get_formatted_output($this->p_perso->values[6],6)."</span></td></tr>";
				}
				
			}else{
				//Livres
				
				//type de doc
				if ($tdoc->table[$this->notice->typdoc]){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
				}
			
				//titres et sous titre
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
				$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1.' ';
				if ($this->notice->tit4){
					$this->notice_public .= ": ".$this->notice->tit4.' ';
				}
				if($this->auteurs_principaux){
					$this->notice_public .='/ '.$this->auteurs_principaux;
				}
				$this->notice_public.="</span></td></tr>";
			
				//Auteurs
				if ($this->auteurs_tous){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
				}
				if ($this->congres_tous){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
				}
			
				//Editeur 1
				if($this->notice->ed1_id){
					$editeur1 = new publisher($this->notice->ed1_id);
					$edition1='';
					$firstEdition1=true;
						
					if($editeur1->ville){
						if(!$firstEdition1){
							$edition1.=', ';
						}
						$edition1.=$editeur1->ville;
						$firstEdition1=false;
					}
					if($editeur1->name){
						if(!$firstEdition1){
							$edition1.=', ';
						}
						$edition1.=$editeur1->name;
						$firstEdition1=false;
					}
					if($this->notice->year){
						if(!$firstEdition1){
							$edition1.=', ';
						}
						$edition1.=$this->notice->year;
						$firstEdition1=false;
					}
				}
			
				//Editeur 2
				if($this->notice->ed2_id){
					$editeur2 = new publisher($this->notice->ed2_id);
					$edition2='';
					$firstEdition2=true;
						
					if($editeur2->ville){
						if(!$firstEdition2){
							$edition2.=', ';
						}
						$edition2.=$editeur2->ville;
						$firstEdition2=false;
					}
					if($editeur2->name){
						if(!$firstEdition2){
							$edition2.=', ';
						}
						$edition2.=$editeur2->name;
						$firstEdition2=false;
					}
					if($this->notice->year){
						if(!$firstEdition2){
							$edition2.=', ';
						}
						$edition2.=$this->notice->year;
						$firstEdition2=false;
					}
						
					if($edition2){
						$edition2=', '.$edition2;
					}
				}
				if($edition1 || $edition2){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeurs_start']."</span></td><td>".inslink($edition1,'./index.php?lvl=publisher_see&id='.$this->notice->ed1_id).inslink($edition2,'./index.php?lvl=publisher_see&id='.$this->notice->ed2_id)."</td></tr>" ;
				}
			
				// collection
				if ($this->notice->nocoll){
					$affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
				}else{
					$affnocoll = "";
				}
					
				if ($this->notice->coll_id) {
					$collection = new collection($this->notice->coll_id);
					$this->collections[]=$collection;
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
					$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
				}
			
				// zone de la collation
				$collation='';
				$firstCollation=true;
				if($this->notice->npages) {
					if(!$firstCollation){
						$collation.=', ';
					}
					$collation.=$this->notice->npages;
					$firstCollation=false;
				}
				if ($this->notice->ill){
					if(!$firstCollation){
						$collation.=', ';
					}
					$collation.=$this->notice->ill;
					$firstCollation=false;
				}
				if ($this->notice->size){
					if(!$firstCollation){
						$collation.=', ';
					}
					$collation.=$this->notice->size;
					$firstCollation=false;
				}
				if ($this->notice->accomp){
					if(!$firstCollation){
						$collation.=', ';
					}
					$collation.=$this->notice->accomp;
					$firstCollation=false;
				}
				if($collation){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['description_start']." :</span></td><td>".$collation."</td></tr>";
				}
			
				//langues
				if(sizeof($this->langues)){
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
					if (count($this->languesorg)){
						$this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
					}
					$this->notice_public.="</td></tr>";
				} elseif (sizeof($this->languesorg)) {
					$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
				}
			
				//indexint
				if($this->notice->indexint){
					$indexint=new indexint($this->notice->indexint);
					if($indexint->name){
						if($this->to_print){
							$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexation_decimale']." :</span></td><td>".$indexint->name;
						}else{
							$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexation_decimale']." :</span></td><td>".inslink($indexint->name,'./index.php?lvl=indexint_see&id='.$indexint->indexint_id);
						}
							
						if($indexint->comment){
							$this->notice_public.=" ".$indexint->comment;
						}
						$this->notice_public.="</td></tr>";
					}
				}
			
				//categories
				if($this->categories_toutes){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['sujets_start']." :</span></td><td>".$this->categories_toutes."</td></tr>";
				}
			
				//resumé
				if($this->notice->n_resume){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['267']." :</span></td><td>".$this->notice->n_resume."</td></tr>";
				}
				
				//note générale
				if($this->notice->n_gen){
					$this->notice_public.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['collstate_form_note']." :</span></td><td>".$this->notice->n_gen."</td></tr>";
				}
		
			}
		}
		
		// Permalink avec Id
		if ($opac_permalink && !$this->to_print) {
			if($this->notice->niveau_biblio != "b"){
				$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
			}else {
				$this->notice_public.= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}
		}
		
		$this->notice_public.="</table>\n";
	
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1){
			$this->notice_public.=$this->affichage_etat_collections();
		}
	
		// exemplaires, résas et compagnie
		if ($ex){
			$this->affichage_resa_expl = $this->aff_resa_expl() ;
		}
		return;
	} // fin do_public($short=0,$ex=1)
	
	function affichage_etat_collections() {
		global $pmb_etat_collections_localise;
		global $dbh, $msg,$nb_per_page_a_search,$tpl_collstate_liste,$tpl_collstate_liste_line, $tpl_collstate_surloc_liste, $tpl_collstate_surloc_liste_line;
		global $opac_sur_location_activate, $opac_view_filter_class;
		global $opac_collstate_order, $opac_url_base;
		
		$location="";
		if($opac_view_filter_class){
			$req="SELECT  collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id, docs_location
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and location_id=idlocation and idlocation in(". implode(",",$opac_view_filter_class->params["nav_collections"]).")
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($pmb_etat_collections_localise?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		} else {
			$req="SELECT collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT  JOIN docs_location ON location_id = idlocation
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($pmb_etat_collections_localise?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		}
		$myQuery = pmb_mysql_query($req, $dbh);
		
		if(($nbr = pmb_mysql_num_rows($myQuery))) {
			$tab="<table class='exemplaires' cellpadding='2' width='100%'><tbody>
					<tr>
					<th>Statut</th>		
					<th>Archive</th>'
					</tr>";
			$parity=1;
			
			while(($coll = pmb_mysql_fetch_object($myQuery))) {
				$my_collstate=new collstate($coll->collstate_id);
				if ($parity++ % 2) $pair_impair = "even"; else $pair_impair = "odd";
				$line="<tr onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\"  >";
				
				$line.= '<td>'.$my_collstate->statut_opac_libelle.'</td>';
				$line.= '<td>'.$my_collstate->archive.'</td></tr>';
				$liste.=$line;
			}
			
		} else {
			$liste= $msg["collstate_no_collstate"];
		}
		$tab.=$liste."</tbody></table>";
		
		if($nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$tab;
		}
		
		return $affichage;
	} // fin affichage_etat_collections()
}


class notice_affichage_cconstitutionnel extends notice_affichage {
	
	var $parents_in = "";			// la chaine des parents, utilisée pour do_parents en header 

	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	function genere_simple($depliable=1, $what='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $icon_doc,$biblio_doc,$tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_sugg; // l'utilisateur a-t-il le droit de faire une suggestion
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		global $opac_visionneuse_allow;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if(!$this->notice_id) return;
		
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]'/>&nbsp;";
		else $case_a_cocher = "" ;
		
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
			}
		}else $basket="";
		
		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"open('addtags.php?noticeid=$this->notice_id','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\"><img src='".$opac_url_base."images/tag.png' align='absmiddle' border='0' title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" /></a>";
		
		 //Avis
		if (($opac_avis_display_mode==0)&&(($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2)))
			$img_avis= $this->affichage_avis($this->notice_id);
		
		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion($this->notice_id);	
		 
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);	
			}
		}
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		
		$script_simili_search = $this->get_simili_script();
		
		if ($depliable == 1) { 
			$template="<br />".$simili_search_script_all."
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				<table><tr><td width='8%'>
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.= "&nbsp;".$basket;
    		$template.="</td><td width='82%'>		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></td><td>".$this->notice_header_doclink."</td></tr></table>
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";			
		}elseif($depliable == 2){ 
			$template="<br />".$simili_search_script_all."
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				<table><tr><td width='8%'>
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true);  $script_simili_search return false;\">
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}
    		$template.= "&nbsp;".$basket;
    		$template.="</td><td width='82%'>	
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span></td><td>".$this->notice_header_doclink."</td></tr></table>
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>
	    		";						
		}else{
			$template="<div id=\"el!!id!!Parent\" class=\"parent\">
				<table><tr><td width='8%'>
	    		$case_a_cocher";
			if ($icon) {
    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
    			$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
    		}			
    		$template.="</td><td width='82%'>
    			<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></td><td>".$this->notice_header_doclink."</td></tr></table>";
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";
					
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
	
		if($opac_show_social_network){	
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}	
			$template_in.="
		<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style ' 
			addthis:url='".$opac_url_base."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($this->notice_header_without_html) : $this->notice_header_without_html)))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($this->permalink) : $this->permalink))."'>
		</div>";	
		}
		if($img_tag) $li_tags="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		if($basket || $img_tag || $img_avis || $img_sugg || $opac_notice_enrichment){
			$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
			if ($basket) $template_in.="<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			if($opac_notice_enrichment){
				if($what =='ISBD') $template_in.="<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>";
				else $template_in.="<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>";
			}
			$template_in.="
	  			$li_tags
			<!-- onglets_perso_list -->
		</ul>
		<div class='row'></div>";	
		}
		
		if($what =='ISBD') $template_in.="		    	
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		else $template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>"
	  			; 	
		$template_in.="
			<!-- onglets_perso_content -->";
	  	if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2))) $this->affichage_avis_detail=$this->avis_detail();
	  			
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
				if($test=$this->get_bulletins_docnums()){
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') { 
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
		
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		
		switch($opac_allow_simili_search){
			case "1" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "2" :
				$this->affichage_simili_search_head="
			<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
			case "3" :
				$this->affichage_simili_search_head="
			<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
				if(!$depliable){
					$this->affichage_simili_search_head.="
			<script type='text/javascript'>
				".$script_simili_search."
			</script>";
				}
				break;
		}	
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);
				
	} // fin genere_simple($depliable=1, $what='ISBD')
	
	function genere_ajax($aj_type_aff,$header_only_origine=0){
		global $msg,$charset; 
		global $opac_url_base;
		global $icon_doc,$biblio_doc,$tdoc;
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_notices_depliable;		
		global $opac_allow_simili_search;
		global $opac_draggable;
		
		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}
		
		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.gif\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1; 
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".$opac_url_base."images/basket_small_20x20.png' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>"; 
			}
		}else $basket="";
	
		if ($this->no_header) $icon="";
		else $icon = $icon_doc[$this->notice->niveau_biblio.$this->notice->typdoc];
				
		$this->genere_ajax_param($aj_type_aff,$header_only_origine);
		
		if($this->notice->niveau_biblio != "b"){
			$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
		}else{
			$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}
		
		if($opac_allow_simili_search){	
			$simili_search_script_all="
				<script type='text/javascript'>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";			
		}		
		$script_simili_search = $this->get_simili_script();
			
		if($opac_notices_depliable == 2){
			$template="<br />".$simili_search_script_all."
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				<table><tr><td width='8%'>
				$case_a_cocher<span class=\"notices_depliables\" param='".rawurlencode($this->notice_affichage_cmd)."'  onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param'));  $script_simili_search return false;\">
		    	<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" hspace=\"3\"/>";
			if ($icon) {
	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
	    	}
	    	$template.= "&nbsp;".$basket;
	    	$template.="</td><td width='82%'>		
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span></td><td>".$this->notice_header_doclink."</td></tr></table>
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";
		}else{
			$template="<br />".$simili_search_script_all."
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				<table><tr><td width='8%'>
				$case_a_cocher
		    	<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg["expandable_notice"]."\" border=\"0\" param='".rawurlencode($this->notice_affichage_cmd)."' onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); $script_simili_search return false;\" hspace=\"3\"/>";
			if ($icon) {
	    		$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$this->notice->niveau_biblio],$msg["info_bulle_icon"]);
	    		$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$this->notice->typdoc],$info_bulle_icon);    			
	    		$template.="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon,ENT_QUOTES, $charset)."'/>";
	    	}
	    	$template.= "&nbsp;".$basket;
	    	$template.="</td><td width='82%'>		
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></td><td>".$this->notice_header_doclink."</td></tr></table>
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";	    	
		}
		
		$template.="<a href=\"".$this->permalink."\" style=\"display:none;\">Permalink</a>
			$simili_search_script_all
		";
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$opac_notices_depliable);	
		
		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);
				
	} // fin genere_ajax()
	
	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_url_base;
		global $charset;
		
		$this->notice_header="";	
		if(!$this->notice_id) return;	
		
		if(($this->notice->typdoc=='a') || ($this->notice->typdoc=='j') 
			|| ($this->notice->typdoc=='m') || ($this->notice->typdoc=='3') || ($this->notice->typdoc=='4') || ($this->notice->typdoc=='5')){
			/*
			 * Ouvrage - Thèse / Mémoire
			 * Multimédia - Photo - Vidéo - Audio
			 */
			if($this->notice->serie_name) {
				$this->notice_header = $this->notice->serie_name;
				if($this->notice->tnvol) $this->notice_header .= '. '.$this->notice->tnvol;
			} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
			
			if ($this->notice_header) $this->notice_header .= ", <b>".$this->notice->tit1."</b>";
			else $this->notice_header = "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->notice->tit4;
			$this->notice_header .= ".";
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$this->notice_header .= "<br />".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				//Année édition
				if ($this->notice->year) {
					$this->notice_header .= ", ".$this->notice->year;
				}
			}
			if ($this->notice->ed1_id) {
				if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages;
			} else {
				if($this->notice->npages) $this->notice_header .= "<br />".$this->notice->npages;
			}
			//Collection
			if ($this->notice->coll_id) {
				$collection = new collection($this->notice->coll_id);
				$this->notice_header .= " (".inslink($collection->name, str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
				if ($this->notice->nocoll) $this->notice_header .= ", ".$this->notice->nocoll;
				$this->notice_header .= ")";
			}
			//Cote
			$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$this->notice_id.' LIMIT 1';
			$res = pmb_mysql_query($req);
			$tmp_notice_header = "";
			if ($res) {
				if(pmb_mysql_num_rows($res)){
					$tmp_notice_header .= "<br />".$msg['cote_start']." <b>".pmb_mysql_result($res,0,0)."</b>";	
				}
			}
			if ($this->notice->statut == 3) {
				if ($tmp_notice_header != "") $tmp_notice_header .= " <font color='red'>".$this->statut_notice."</font>";
				else $tmp_notice_header .= "<br /><font color='red'>".$this->statut_notice."</font>";
			}
			$this->notice_header .= $tmp_notice_header;
		} elseif(($this->notice->typdoc=='f') || ($this->notice->typdoc=='i')) {
			/*
			 * Dossier
			 * Archive
			 */
			$this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= ".&nbsp;".$this->notice->tit4;
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				if ($this->auteurs_tous) {
					$this->notice_header .= ". ".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				} else {
					$this->notice_header .= "<br />".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				}
				//Année édition
				if ($this->notice->year) {
					$this->notice_header .= ", ".$this->notice->year;
				}
			}
			//Cote
			$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$this->notice_id.' LIMIT 1';
			$res = pmb_mysql_query($req);
			$tmp_notice_header = "";
			if ($res) {
				if(pmb_mysql_num_rows($res)){
					$tmp_notice_header .= "<br />".$msg['cote_start']." <b>".pmb_mysql_result($res,0,0)."</b>";	
				}
			}
			if ($this->notice->statut == 3) {
				if ($tmp_notice_header != "") $tmp_notice_header .= " <font color='red'>".$this->statut_notice."</font>";
				else $tmp_notice_header .= "<br /><font color='red'>".$this->statut_notice."</font>";
			}
			$this->notice_header .= $tmp_notice_header;
		} elseif(($this->notice->typdoc=='c') || (($this->notice->niveau_biblio == 's') && ($this->notice->typdoc=='e'))) {
			/*
			 * Revue
			 * Revue de presse
			 */
			$this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= " : ".$this->notice->tit4;
			if ($this->notice->code) $this->notice_header .= " (".$this->notice->code.")";
			
		} elseif(($this->notice->typdoc=='d') || (($this->notice->niveau_biblio == 'a') && ($this->notice->typdoc=='e'))) {
			/*
			 * Article de périodique
			 * Article de presse
			 */
			//Champs perso cp_cpt_tit
			$perso_aff_1 = "";
			$perso_aff_2 = "";
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_cpt_tit') {
							$perso_aff_1 .= $p["AFF"];
						} elseif ($p['NAME']=='cp_partie_revue') {
							$perso_aff_2 .= $p["AFF"];
						}
					}
				}
			}
			$this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->notice->tit4;
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($perso_aff_1 != "") $this->notice_header .= ". ".$perso_aff_1;
			if ($perso_aff_2 != "") $this->notice_header .= ". ".$perso_aff_2;
//			if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages."";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			//Si c'est un depouillement, ajout du titre de pério
			if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			 	 $this->notice_header .= "<br />".$this->parent_title.(($this->parent_date != "" || $this->parent_date != "s.d.")?", ".$this->parent_date:", ".$this->parent_aff_date_date).(($this->parent_numero && ($this->parent_numero != "s.n.")) ? ", ".$msg['number']." ".$this->parent_numero : "") ;
			}	
			if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages."";

		} elseif($this->notice->typdoc=='b'){
			/*
			 * Article d'ouvrage
			 */
			//Champs perso cp_cpt_tit
			$perso_aff_1 = "";
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_cpt_tit') {
							$perso_aff_1 .= $p["AFF"];
						}
					}
				}
			}
			$this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->notice->tit4;
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($perso_aff_1 != "") $this->notice_header .= ". ".$perso_aff_1;
//			if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages."";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			if ($this->header_only && ($this->parents_in == "")) $this->do_parents(); //mode ajax
			$this->notice_header .= $this->parents_in;
			if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages."";
			$this->notice_header .= $this->parents_in_cote;
		} elseif($this->notice->typdoc=='g'){
			/*
			 * Décision du CC
			 */
			//Champs perso cp_referent et cp_date_dec
			$perso_aff_1 = "";
			$perso_aff_2 = "";
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_referent') {
							$perso_aff_1 .= $p["AFF"];
						} else if ($p['NAME']=='cp_datedec') {
							$perso_aff_2 .= $p["AFF"];
						}
					}
				}
			}
			$this->notice_header .= $perso_aff_1;
			if ($perso_aff_2 != "") {
				if ($this->notice_header != "") $this->notice_header .= " - ".$perso_aff_2;
				else $this->notice_header .= $perso_aff_2; 
			}
			if ($this->notice_header != "") $this->notice_header .= "<br /><b>".$this->notice->tit1."</b>";
			else $this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= " [".$this->notice->tit4."]";
		} elseif($this->notice->typdoc=='l'){
			/*
			 * Ressource électronique
			 */
			$this->notice_header .= "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->notice->tit4;
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				if ($this->auteurs_tous) {
					$this->notice_header .= ". ".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				} else {
					$this->notice_header .= "<br />".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				}
			}
			//Année édition
			if ($this->notice->year) {
				$this->notice_header .= ", ".$this->notice->year;
			}
			//Importance matérielle
			if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages;
			$this->notice_header .= ".";
		} elseif($this->notice->typdoc=='6'){
			/*
			 * Bulletin
			 */
			if ($this->parent_bulletin_title) $this->notice_header .= "<b>".$this->parent_bulletin_title."</b><br />";
			$this->notice_header .= $this->parent_title;
			if ($this->parent_title_4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->parent_title_4;
			if ($this->parent_date) $this->notice_header .= ", ".$this->parent_date;
			if ($this->parent_numero) $this->notice_header .= ", ".$msg['number']." ".$this->parent_numero;
			if ($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages;
		} else {
			/*
			 * Default
			 */
			if($this->notice->serie_name) {
				$this->notice_header = $this->notice->serie_name;
				if($this->notice->tnvol) $this->notice_header .= '. '.$this->notice->tnvol;
			} elseif ($this->notice->tnvol) $this->notice_header .= $this->notice->tnvol;
			
			if ($this->notice_header) $this->notice_header .= ", <b>".$this->notice->tit1."</b>";
			else $this->notice_header = "<b>".$this->notice->tit1."</b>";
			if ($this->notice->tit4 != "") $this->notice_header .= "&nbsp;:&nbsp;".$this->notice->tit4;
			$this->notice_header .= ".";
			//permet de fermer la balise <a> parente pour que éviter le prolongement du lien sur la fonction d'auteur (notices liées)
			$this->notice_header .= "<a></a>";
			if ($this->auteurs_tous) $this->notice_header .= "<br />".$this->auteurs_tous;
			if ($this->congres_tous) $this->notice_header .= "<br />".$this->congres_tous;
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$this->notice_header .= "<br />".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
				//Année édition
				if ($this->notice->year) {
					$this->notice_header .= ", ".$this->notice->year;
				}
			}
			if ($this->notice->ed1_id) {
				if($this->notice->npages) $this->notice_header .= ", ".$this->notice->npages;
			} else {
				if($this->notice->npages) $this->notice_header .= "<br />".$this->notice->npages;
			}
			//Collection
			if ($this->notice->coll_id) {
				$collection = new collection($this->notice->coll_id);
				$this->notice_header .= " (".inslink($collection->name, str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
				if ($this->notice->nocoll) $this->notice_header .= ", ".$this->notice->nocoll;
				$this->notice_header .= ")";
			}
			//Cote
			$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$this->notice_id.' LIMIT 1';
			$res = pmb_mysql_query($req);
			$tmp_notice_header = "";
			if ($res) {
				if(pmb_mysql_num_rows($res)){
					$tmp_notice_header .= "<br />".$msg['cote_start']." <b>".pmb_mysql_result($res,0,0)."</b>";	
				}
			}
			if ($this->notice->statut == 3) {
				if ($tmp_notice_header != "") $tmp_notice_header .= " <font color='red'>".$this->statut_notice."</font>";
				else $tmp_notice_header .= "<br /><font color='red'>".$this->statut_notice."</font>";
			}
			$this->notice_header .= $tmp_notice_header;
		}
		
//		$this->notice_header .="<br />";
		//$this->notice_header_without_html = $this->notice_header;	
	
		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";	
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);
		
		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;
		
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
			else $info_bulle=$this->notice->eformat;
			// ajout du lien pour les ressources électroniques			
			$this->notice_header_doclink .= "&nbsp;<span class='notice_link'><a href=\"".$this->notice->lien."\" target=\"__LINK__\">";
			$this->notice_header_doclink .= "<img src=\"".$opac_url_base."images/lien-web_x16.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
			$this->notice_header_doclink .= " alt=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" title=\"";
			$this->notice_header_doclink .= $info_bulle;
			$this->notice_header_doclink .= "\" />";
			$this->notice_header_doclink .= "</a></span>";			
		} 
		if ($this->notice->niveau_biblio == 'b') {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
		} else {
			$sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = ".$this->notice_id." order by explnum_id";
		}
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ( (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}	
				$this->notice_header_doclink .= "&nbsp;<span>";		
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script type='text/javascript'>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
					</script>
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" alt='$alt' title='$alt'>";
					
				}else{
					$this->notice_header_doclink .= "<a href=\"./doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"__LINK__\">";
				}
				$this->notice_header_doclink .= "<img src=\"./images/fichier-attache.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "&nbsp;";
				$this->notice_header_doclink .= "<img src=\"./images/fichiers-attaches_x20.png\" alt=\"$info_bulle\" \" title=\"$info_bulle\" border=\"0\" align=\"middle\" hspace=\"3\">";
			}
		}
		
		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;
		
		
		$this->notice_header_without_doclink=$this->notice_header;
//		$this->notice_header.=$this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;
		
		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
		$memo_notice[$this->notice_id]["typdoc"] = $this->notice->typdoc;
		
		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;

	} // fin do_header()
	

	// génération de l'affichage public----------------------------------------
	function do_public($short=0,$ex=1) {
		global $dbh;
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $opac_url_base,$opac_permalink;
		
		$this->notice_public= $this->genere_in_perio ();
		if(!$this->notice_id) return;

		// Notices parentes
		//$this->notice_public.=$this->parents;
			
		$this->notice_public .= "<table>";
		
		if(($this->notice->typdoc=='a') || ($this->notice->typdoc=='j') 
			|| ($this->notice->typdoc=='m') || ($this->notice->typdoc=='3') || ($this->notice->typdoc=='4') || ($this->notice->typdoc=='5')){
			/*
			 * Ouvrage - Thèse / Mémoire
			 * Multimédia - Photo - Vidéo - Audio
			 */
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			// constitution de la mention de titre
			if ($this->notice->serie_name) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
				$this->notice_public .="</td></tr>";
			}
			//Partie
			if ($this->notice->tnvol) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tnvol_start']."</span></td><td>".$this->notice->tnvol;
				$this->notice_public .="</td></tr>";	
			}
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Titre parallèle
			if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			// mention d'édition
			if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année de publication
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			//Matériel d'accompagnement
			if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			//Collection et Sous-collection 
			if($this->notice->subcoll_id) {
				$subcollection = new subcollection($this->notice->subcoll_id);
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>" ;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
				if ($this->notice->nocoll) $this->notice_public .= ", ".$this->notice->nocoll."</td></tr>";
			} elseif ($this->notice->coll_id) {
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
				if ($this->notice->nocoll) $this->notice_public .= ", ".$this->notice->nocoll."</td></tr>";
			}
			//ISBN ou NO. commercial
			if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
			//Note générale
			if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
			if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
			//Langues
			if (count($this->langues)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
				if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
				$this->notice_public.="</td></tr>";
			} elseif (count($this->languesorg)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
			}
			//Catégories
			if ($this->categories_toutes) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";	
			//Indexation décimale
				if($this->notice->indexint) {
				$indexint = new indexint($this->notice->indexint);
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name.($indexint->comment ? " - ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset)) : ""),  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))."</td></tr>" ;
			}
			//URL associée
			if ($this->notice->lien) {
				$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
				if (substr($this->notice->eformat,0,3)=='RSS') {
					$ret .= affiche_rss($this->notice->notice_id) ;
				} else {
					if (strlen($this->notice->lien)>80) {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
					} else {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
					}
				}
				$ret.="</td></tr>";
				if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
			}
			
			if ($this->notice->typdoc=='j') {
				//Champs perso - Lieu de la soutenance
				$perso_aff_1 = "" ;
				if (!$this->p_perso->no_special_fields) {
					// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
					if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
					for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
						$p=$this->memo_perso_["FIELDS"][$i];
						if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
							if ($p['NAME']=='cp_lieusout') {
								$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
							}
						}
					}
				}
				$this->notice_public .= $perso_aff_1;
			}
			
		} elseif(($this->notice->typdoc=='f') || ($this->notice->typdoc=='i')) {
			/*
			 * Dossier
			 * Archive
			 */
			
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}			
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année édition
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Indexation décimale
				if($this->notice->indexint) {
				$indexint = new indexint($this->notice->indexint);
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name.($indexint->comment ? " - ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset)) : ""),  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))."</td></tr>" ;
			}
			
		} elseif(($this->notice->typdoc=='c') || (($this->notice->niveau_biblio == 's') && ($this->notice->typdoc=='e'))) {
			/*
			 * Revue juridique
			 * Revue de presse
			 */
			
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année de publication
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//ISSN
			if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['issn']."</span></td><td>".$this->notice->code."</td></tr>";
			//Note générale
			if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
			if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
			
			//Lien - En ligne
			if ($this->notice->lien) {
				$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
				if (substr($this->notice->eformat,0,3)=='RSS') {
					$ret .= affiche_rss($this->notice->notice_id) ;
				} else {
					if (strlen($this->notice->lien)>80) {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
					} else {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
					}
				}
				$ret.="</td></tr>";
				if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
			}
			
			//Champs personnalisés (en 2 parties)
			$perso_aff_1 = "" ;
			$perso_aff_2 = "" ;
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_abrev') {
							$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_titreabrev') {
							$perso_aff_2 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						}
					}
				}
			}
			//Abréviation
			$this->notice_public .= $perso_aff_1;
				
		} elseif(($this->notice->typdoc=='d') || (($this->notice->niveau_biblio == 'a') && ($this->notice->typdoc=='e'))) {
			/*
			 * Article de périodique
			 * Article de presse
			 */
			
			//Champs personnalisés (en 4 parties)
			$perso_aff_1 = "" ;
			$perso_aff_2 = "" ;
			$perso_aff_3 = "" ;
			$perso_aff_4 = "" ;
			$perso_aff_5 = "" ;
			$perso_aff_6 = "" ;
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_theme') {
							$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_cahier') {
							$perso_aff_2 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_recueil') {
							$perso_aff_3 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_revdoct') {
							$perso_aff_4 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_cpt_tit') {
							$perso_aff_5 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_partie_revue') {
							$perso_aff_6 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						}
					}
				}
			}
			
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Autres compléments de titre
			$this->notice_public .= $perso_aff_5;
			//Partie revue
			$this->notice_public .= $perso_aff_6;
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";			
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			//Année édition
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";

			//Thématique
			$this->notice_public .= $perso_aff_1;
			//Cahier
			$this->notice_public .= $perso_aff_2;
			//Recueil
			$this->notice_public .= $perso_aff_3;
			//Revue doctrine
			$this->notice_public .= $perso_aff_4;
			
		} elseif($this->notice->typdoc=='b'){
			/*
			 * Article d'ouvrage
			 */
			
			//Champs personnalisés
			$perso_aff_1 = "" ;
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_cpt_tit') {
							$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						}
					}
				}
			}
			
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Autres compléments de titre
			$this->notice_public .= $perso_aff_1;
			//Titre en langue étrangère
			if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			// mention d'édition
			if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année de publication
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			// langues
			if (count($this->langues)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
				if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
				$this->notice_public.="</td></tr>";
			} elseif (count($this->languesorg)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
			}
			//Catégories
			if ($this->categories_toutes) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
			//URL associée
			if ($this->notice->lien) {
				$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
				if (substr($this->notice->eformat,0,3)=='RSS') {
					$ret .= affiche_rss($this->notice->notice_id) ;
				} else {
					if (strlen($this->notice->lien)>80) {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
					} else {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
					}
				}
				$ret.="</td></tr>";
				if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
			}
			
		} elseif($this->notice->typdoc=='g'){
			/*
			 * Décision du CC
			 */
			
			//Champs personnalisés (en 4 parties)
			$perso_aff_1 = "" ;
			$perso_aff_2 = "" ;
			$perso_aff_3 = "" ;
			$perso_aff_4 = "" ;
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_referent') {
							$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_datedec') {
							$perso_aff_2 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_codedec') {
							$perso_aff_3 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						} else if ($p['NAME']=='cp_type_dec') {
							$perso_aff_4 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						}
					}
				}
			}
			
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Référent
			$this->notice_public .= $perso_aff_1;
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Date de décision
			$this->notice_public .= $perso_aff_2;
			//Code décision
			$this->notice_public .= $perso_aff_3;
			//Type décision
			$this->notice_public .= $perso_aff_4;
			
		} elseif($this->notice->typdoc=='l'){
			/*
			 * Ressource électronique
			 */
			
			//Champs personnalisés
			$perso_aff_1 = "" ;
			if (!$this->p_perso->no_special_fields) {
				// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
				if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
				for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
					$p=$this->memo_perso_["FIELDS"][$i];
					if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
						if ($p['NAME']=='cp_cpt_tit') {
							$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
						}
					}
				}
			}
			
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Autres compléments de titre
			$this->notice_public .= $perso_aff_1;
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année de publication
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			//Indexation décimale
				if($this->notice->indexint) {
				$indexint = new indexint($this->notice->indexint);
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name.($indexint->comment ? " - ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset)) : ""),  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))."</td></tr>" ;
			}
			//Catégories
			if ($this->categories_toutes) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";	
			
		} elseif($this->notice->typdoc=='6'){
			/*
			 * Bulletin
			 */

			//Titre du bulletin
			if($this->parent_bulletin_title) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td><td><span class='public_title'><b>".$this->parent_bulletin_title."</b></span></td></tr>";
			
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Mention de date
			if($this->parent_date) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['bull_mention_date']."</span></td><td>".$this->parent_date."</td></tr>";
			
			//Numéro du bulletin
			if($this->parent_numero) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['bull_numero_start']."</span></td><td>".$this->parent_numero."</td></tr>";
			
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			
		} else {
			/*
			 * Default
			 */
			//Titre
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
			$this->notice_public .= "<td><span class='public_title'><b>".$this->notice->tit1."</b>" ;
			$this->notice_public .= "</span></td></tr>";
			// constitution de la mention de titre
			if ($this->notice->serie_name) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
				$this->notice_public .="</td></tr>";
			}
			//Partie
			if ($this->notice->tnvol) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['tnvol_start']."</span></td><td>".$this->notice->tnvol;
				$this->notice_public .="</td></tr>";	
			}
			//Complément du titre
			if ($this->notice->tit4) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['240']." :</span></td>";
				$this->notice_public .= "<td>".$this->notice->tit4."</td></tr>";
			}
			//Titre parallèle
			if ($this->notice->tit3) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
			//type de doc
			if ($tdoc->table[$this->notice->typdoc]){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
			}
			//Auteurs
			if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
			//Congrès
			if ($this->congres_tous) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";
			// mention d'édition
			if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
			// zone de l'éditeur 
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);			
				$this->publishers[]=$editeur;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".($editeur->ville ? $editeur->ville." : " : "").inslink($editeur->name,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			}
			//Année de publication
			if ($this->notice->year) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->notice->year."</td></tr>" ;
			}
			//Pagination
			if($this->notice->npages) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			//Matériel d'accompagnement
			if ($this->notice->accomp) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
			//Collection et Sous-collection 
			if($this->notice->subcoll_id) {
				$subcollection = new subcollection($this->notice->subcoll_id);
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))."</td></tr>" ;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
				if ($this->notice->nocoll) $this->notice_public .= ", ".$this->notice->nocoll."</td></tr>";
			} elseif ($this->notice->coll_id) {
				$collection = new collection($this->notice->coll_id);
				$this->collections[]=$collection;
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
				if ($this->notice->nocoll) $this->notice_public .= ", ".$this->notice->nocoll."</td></tr>";
			}
			//ISBN ou NO. commercial
			if ($this->notice->code) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
			//Note générale
			if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
			if ($zoneNote) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
			//Langues
			if (count($this->langues)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
				if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
				$this->notice_public.="</td></tr>";
			} elseif (count($this->languesorg)) {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>"; 
			}
			//Catégories
			if ($this->categories_toutes) $this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";	
			//Indexation décimale
				if($this->notice->indexint) {
				$indexint = new indexint($this->notice->indexint);
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name.($indexint->comment ? " - ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset)) : ""),  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))."</td></tr>" ;
			}
			//URL associée
			if ($this->notice->lien) {
				$ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
				if (substr($this->notice->eformat,0,3)=='RSS') {
					$ret .= affiche_rss($this->notice->notice_id) ;
				} else {
					if (strlen($this->notice->lien)>80) {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
					} else {
						$ret.="<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856'>".htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
					}
				}
				$ret.="</td></tr>";
				if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
			}
			
			if ($this->notice->typdoc=='j') {
				//Champs perso - Lieu de la soutenance
				$perso_aff_1 = "" ;
				if (!$this->p_perso->no_special_fields) {
					// $this->memo_perso_ permet aux affichages personnalisés dans notice_affichage_ext de gagner du temps
					if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);			
					for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
						$p=$this->memo_perso_["FIELDS"][$i];
						if (/*$p['OPAC_SHOW'] && */$p["AFF"]) {
							if ($p['NAME']=='cp_lieusout') {
								$perso_aff_1 .="<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
							}
						}
					}
				}
				$this->notice_public .= $perso_aff_1;
			}
		}
		
		//statut de notice en commande
		if ($this->notice->statut == 3) {
			$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['statut_start']."</span></td>
				<td><font color='red'>".$this->statut_notice."</font></td></tr>";
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";	
			}else {
				$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}	
		}
		//Identifiant de la notice
		$this->notice_public .= "<tr><td align='right' class='bg-grey'><span class='etiq_champ'>".$msg['notice_id_start']."</span></td><td>".$this->notice_id."</td></tr>"; 

//		if (!$short) $this->notice_public .= $this->aff_suite() ; 
//		else $this->notice_public.=$this->genere_in_perio();
		
		$this->notice_public.="</table>\n";
		
		//notice mère
		$this->notice_public.=$this->parents;
		
		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();	
		
		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
	} // fin do_public($short=0,$ex=1)	
	
	function affichage_etat_collections() {
		global $msg;
		global $pmb_etat_collections_localise;
		global $tpl_collstate_liste,$tpl_collstate_liste_line;
		
		$tpl_collstate_liste[2]="
		<table class='exemplaires' cellpadding='2' width='100%'>
			<tbody>
				<tr>
					<th>".$msg["collstate_form_emplacement"]."</th>		
					<th>".$msg["collstate_form_support"]."</th>
					<th>".$msg["collstate_form_statut"]."</th>				
					<th>".$msg["collstate_form_collections"]."</th>
					<th>".$msg["collstate_form_lacune"]."</th>		
				</tr>
				!!collstate_liste!!	
			</tbody>	
		</table>
		";
		
		$tpl_collstate_liste_line[2]="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
			<!-- surloc -->
			<td !!tr_javascript!! >!!emplacement_libelle!!</td>
			<td !!tr_javascript!! >!!type_libelle!!</td>
			<td !!tr_javascript!! >!!statut_libelle!!</td>	
			<td !!tr_javascript!! >!!state_collections!!</td>
			<td !!tr_javascript!! >!!lacune!!</td>
		</tr>";
		
		$tpl_collstate_liste[3]="
		<table class='exemplaires' cellpadding='2' width='100%'>
			<tbody>
				<tr>
					<!-- surloc -->
					<th>".$msg["collstate_form_localisation"]."</th>		
					<th>".$msg["collstate_form_emplacement"]."</th>		
					<th>".$msg["collstate_form_support"]."</th>
					<th>".$msg["collstate_form_statut"]."</th>		
					<th>".$msg["collstate_form_collections"]."</th>
					<th>".$msg["collstate_form_lacune"]."</th>		
				</tr>
				!!collstate_liste!!
			</tbody>	
		</table>
		";
		
		$tpl_collstate_surloc_liste = "<th>".$msg["collstate_form_surloc"]."</th>";
		
		$tpl_collstate_liste_line[3]="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
			<!-- surloc -->
			<td !!tr_javascript!! >!!localisation!!</td>
			<td !!tr_javascript!! >!!emplacement_libelle!!</td>
			<td !!tr_javascript!! >!!type_libelle!!</td>	
			<td !!tr_javascript!! >!!statut_libelle!!</td>
			<td !!tr_javascript!! >!!state_collections!!</td>
			<td !!tr_javascript!! >!!lacune!!</td>
		</tr>";

		$collstate=new collstate(0,$this->notice_id);
		if($pmb_etat_collections_localise) {
			$collstate->get_display_list("",0,0,0,3);
		} else { 	
			$collstate->get_display_list("",0,0,0,2);
		}
		if($collstate->nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}

		return $affichage;
	} // fin affichage_etat_collections()
	
	function aff_suite() {
		//
	}
	
	// Construction des parents-----------------------------------------------------
	function do_parents() {
		global $dbh;
		global $msg;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $relation_listup, $parents_to_childs ;
		global $opac_url_base,$icon_doc,$biblio_doc,$tdoc;
		global $parent_notice;
		
		// Pour ne pas afficher en parents les liens transférer dans les childs
		if (sizeof($parents_to_childs)>0) $clause = " AND relation_type not in ('".implode("','", $parents_to_childs)."') ";

		// gestion des droits d'affichage des parents
		if (is_null($this->dom_2)) {
			$acces_j='';
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		} else {
			$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			$statut_j = "";
			$statut_r = "";	
		}
		
		//Recherche des notices parentes
		$requete="select linked_notice, relation_type, rank from notices_relations join notices on notice_id=linked_notice $acces_j $statut_j 
				where num_notice=".$this->notice_id." $clause $statut_r
				order by relation_type,rank";
		$result_linked=pmb_mysql_query($requete,$dbh);
		//Si il y en a, on prépare l'affichage
		if (!pmb_mysql_num_rows($result_linked)) {
			$this->parents = "";
			return ;
		}

		$this->parents = "";
		
		$this->parents_in = "";
		$this->parents_in_cote = "";
		if (!$relation_listup) $relation_listup=new marc_list("relationtypeup");
		
		$r_type=array();
		$ul_opened=false;
		//Pour toutes les notices liées
		while (($r_rel=pmb_mysql_fetch_object($result_linked))) {			
			if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
			

//			if($memo_notice[$r_rel->linked_notice]["header"]) {
//				$parent_notice->notice_header=$memo_notice[$r_rel->linked_notice]["header"];	
//			} else {
//				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,1,$this->to_print,1);
//				$parent_notice->visu_expl = 0 ;
//				$parent_notice->visu_explnum = 0 ;
//				$parent_notice->do_header();
//			}		
			
			if(!$memo_notice[$r_rel->linked_notice]["header_without_doclink"]) {
				$parent_notice=new $notice_affichage($r_rel->linked_notice,$this->liens,1,$this->to_print,1);
				$parent_notice->visu_expl = 0 ;
				$parent_notice->visu_explnum = 0 ;
				$parent_notice->do_header();
			}		
			
			if (pmb_mysql_num_rows($result_linked)==1) {
				// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this->bulletin_id
				$rqbull="select bulletin_id from bulletins where num_notice=".$this->notice_id;
				$rqbullr=pmb_mysql_query($rqbull);
				if ($rqbullr) {
					if (pmb_mysql_num_rows($rqbullr)) {
						$rqbulld=@pmb_mysql_fetch_object($rqbullr);
						$this->bulletin_id=$rqbulld->bulletin_id;
					}
				}
			}
			if (!$r_type[$r_rel->relation_type]) {
				$r_type[$r_rel->relation_type]=1;
				if ($ul_opened) $this->parents.="</ul>";
				else {
//					$this->parents.="<br />";
					$ul_opened=true;
				}
				$this->parents.="<br /><b>".$relation_listup->table[$r_rel->relation_type]."</b>";
				$this->parents.="<ul class='notice_rel'>\n";
			}
			$icon = $icon_doc[$parent_notice->notice->niveau_biblio.$parent_notice->notice->typdoc];
			if ($icon) {
				$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$parent_notice->notice->niveau_biblio],$msg["info_bulle_icon"]);
				$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$parent_notice->notice->typdoc],$info_bulle_icon);
				$html_icon ="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
			} else {
				$html_icon = "";
			}
			$this->parents.="<br /><table><tr><td width='3%'><li style='list-style-type: none;'>".$html_icon."</td><td width='87%'>";
			if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
			//$this->parents.=$parent_notice->notice_header;
			$this->parents.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
			if ($this->lien_rech_notice) $this->parents.="</a>";
			$this->parents.="</td></tr></table></li>\n";
			$this->parents.="</ul>\n";

			if($relation_listup->table[$r_rel->relation_type]== "in"){
				$this->parents_in.="<br /><b>".$relation_listup->table[$r_rel->relation_type]."</b> ";
				if ($this->lien_rech_notice) $this->parents_in.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				$this->parents_in.= "<b>".$parent_notice->notice->tit1."</b>";
				if ($this->lien_rech_notice) $this->parents_in.="</a>";
				//Année édition
				if ($parent_notice->notice->year) {
					$this->parents_in .= ", ".$parent_notice->notice->year;
				}
				//Cote
				$req='SELECT expl_cote FROM exemplaires WHERE expl_notice='.$r_rel->linked_notice.' LIMIT 1';
				$res = pmb_mysql_query($req);
				$tmp_notice_header = "";
				if ($res) {
					if(pmb_mysql_num_rows($res)){
						$this->parents_in_cote .= "<br />".$msg['cote_start']." <b>".pmb_mysql_result($res,0,0)."</b>";
					}
				}
// 				if ($this->lien_rech_notice) $this->parents_in.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				//$this->parents.=$parent_notice->notice_header;
// 				$this->parents_in.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
// 				if ($this->lien_rech_notice) $this->parents_in.="</a>";
			}
		}	
				
		return ;
	} // fin do_parents()
	
	function fetch_auteurs() {
		global $fonction_auteur;
		global $dbh ;
		global $opac_url_base ;
	
		$this->responsabilites  = array() ;
		$auteurs = array() ;
		
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;
		
		$rqt = "SELECT author_id, responsability_fonction, responsability_type, author_type,author_name, author_rejete, author_type, author_date, author_see ";
		$rqt.= "FROM responsability, authors ";
		$rqt.= "WHERE responsability_notice='".$this->notice_id."' AND responsability_author=author_id ";
		$rqt.= "ORDER BY responsability_type, responsability_ordre " ;
		$res_sql = pmb_mysql_query($rqt, $dbh);
		while (($notice=pmb_mysql_fetch_object($res_sql))) {
			$responsabilites[] = $notice->responsability_type ;
			$info_bulle="";
			if($notice->author_type==72 || $notice->author_type==71) {			
				$congres=new auteur($notice->author_id);
				$auteur_isbd=$congres->isbd_entry;
				$auteur_titre=$congres->display;			
				$info_bulle=" title='".$congres->info_bulle."' ";
			} else {
				if ($notice->author_rejete) $auteur_isbd = $notice->author_name.", ".$notice->author_rejete ;
				else  $auteur_isbd = $notice->author_name ;
				// on s'arrête là pour auteur_titre = "NOM, Prénom" uniquement
				$auteur_titre = $auteur_isbd ;
			}
			$auteur_isbd = inslink($auteur_isbd, str_replace("!!id!!", $notice->author_id, $this->lien_rech_auteur),$info_bulle) ;
			if ($notice->responsability_fonction && $notice->responsability_fonction != "070") $auteur_isbd = $fonction_auteur[$notice->responsability_fonction]." : ".$auteur_isbd ;
			$auteurs[] = array( 
					'id' => $notice->author_id,
					'fonction' => $notice->responsability_fonction,
					'responsability' => $notice->responsability_type,
					'name' => $notice->author_name,
					'rejete' => $notice->author_rejete,
					'date' => $notice->author_date,
					'type' => $notice->author_type,
					'fonction_aff' => $fonction_auteur[$notice->responsability_fonction],
					'auteur_isbd' => $auteur_isbd,
					'auteur_titre' => $auteur_titre
					) ;
		}
		if (!$responsabilites) $responsabilites = array();
		if (!$auteurs) $auteurs = array();
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;
		
		// $this->auteurs_principaux 
		// on ne prend que le auteur_titre = "Prénom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			$aut1_libelle = array();
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;			
				if($auteur_1["type"]==72 || $auteur_1["type"]==72) {			
					$congres=new auteur($auteur_1["id"]);
					$aut1_libelle[]=$congres->display;
				} else {
					$aut1_libelle[]= $auteur_1["auteur_titre"];
				}	
			}
			$auteurs_liste = implode (" ; ",$aut1_libelle) ;
			if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
		}
	
		// $this->auteurs_tous
		$mention_resp = array() ;
		$congres_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			if($this->responsabilites["auteurs"][$as]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}	
		}
		
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_2["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}		
		}
		
		$libelle_mention_resp = implode (" ; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
		else $this->auteurs_tous ="" ;
		
		$libelle_congres_resp = implode (" ; ",$congres_resp) ;
		if ($libelle_congres_resp) $this->congres_tous = $libelle_congres_resp ;
		else $this->congres_tous ="" ;
		
	} // fin fetch_auteurs

	function genere_notice_childs() {
		global $msg, $opac_url_base, $opac_notice_affichage_class ;
		global $memo_notice;
		global $icon_doc,$biblio_doc,$tdoc;
		global $relation_typedown;
		
		$onglet_perso=new notice_onglets();
		$this->antiloop[$this->notice_id]=true;
		//Notices liées
		if ($this->notice_childs) return $this->notice_childs;
		if ((count($this->childs))&&(!$this->to_print)) {
			if ($this->seule) $affichage="";
			else $affichage = "<a href='".str_replace("!!id!!",$this->notice_id,$this->lien_rech_notice)."&seule=1'>".$msg[voir_contenu_detail]."</a>";
			if (!$relation_typedown) $relation_typedown=new marc_list("relationtypedown");
			reset($this->childs);
			$affichage.="<br />";
			while (list($rel_type,$child_notices)=each($this->childs)) {
				$affichage="<br /><b>".$relation_typedown->table[$rel_type]."</b>";
				if ($this->seule) {
				} else $affichage.="<ul>";
				$bool=false;	
				for ($i=0; ($i<count($child_notices)); $i++) {
					if (!$this->antiloop[$child_notices[$i]]) {							
						//if(!$this->seule && $memo_notice[$child_notices[$i]]["niveau_biblio"]!='b' && $memo_notice[$child_notices[$i]]["header"]) {
						if(!$this->seule && $memo_notice[$child_notices[$i]]["niveau_biblio"]!='b' && $memo_notice[$child_notices[$i]]["header_without_doclink"]) {
							//$affichage.="<li><a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$memo_notice[$child_notices[$i]]["header"]."</a></li>";	
							$icon = $icon_doc[$memo_notice[$child_notices[$i]]["niveau_biblio"].$memo_notice[$child_notices[$i]]["typdoc"]];
							if ($icon) {
				    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$memo_notice[$child_notices[$i]]["niveau_biblio"]],$msg["info_bulle_icon"]);
				    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$memo_notice[$child_notices[$i]]["typdoc"]],$info_bulle_icon);    			
				    			$html_icon ="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
				    		} else {
				    			$html_icon = "";
				    		}
							$affichage.="<br /><table><tr><td width='3%'><li style='list-style-type: none;'>".$html_icon."</td><td width='87%'><a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$memo_notice[$child_notices[$i]]["header_without_doclink"]."</a></td><td>".$child_notice->notice_header_doclink."</td></tr></table></li>";						
							$bool=true;	
						} else if (!$memo_notice[$child_notices[$i]]["niveau_biblio"]) {
							if($this->seule) $header_only=0; else $header_only=1;
							if ($opac_notice_affichage_class) $child_notice=new $opac_notice_affichage_class($child_notices[$i],$this->liens,$this->cart_allowed,$this->to_print,$header_only);
							else $child_notice=new notice_affichage($child_notices[$i],$this->liens,$this->cart_allowed,$this->to_print,$header_only);
							if ($child_notice->notice->niveau_biblio!='b' || ($child_notice->notice->niveau_biblio=='b' && $this->notice->niveau_biblio != "s")) {
								$child_notice->antiloop=$this->antiloop;
								$child_notice->do_header();
								if ($this->seule) {
									$child_notice->do_isbd();
									$child_notice->do_public();
									if ($this->double_ou_simple == 2 ) $child_notice->genere_double(1, $this->premier) ;
									$child_notice->genere_simple(1, $this->premier);
																		
									$child_notice->result=$onglet_perso->insert_onglets($child_notices[$i],$child_notice->result);
									$affichage .= $child_notice->result ;
								} else {
									$child_notice->visu_expl = 0 ;
									$child_notice->visu_explnum = 0 ;
									$icon = $icon_doc[$child_notice->notice->niveau_biblio.$child_notice->notice->typdoc];
									if ($icon) {
						    			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc[$child_notice->notice->niveau_biblio],$msg["info_bulle_icon"]);
						    			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$child_notice->notice->typdoc],$info_bulle_icon);    			
						    			$html_icon ="<img src=\"".$opac_url_base."images/$icon\" alt='".$info_bulle_icon."' title='".$info_bulle_icon."'/>";
						    		} else {
						    			$html_icon = "";
						    		}
									$affichage.="<br /><table><tr><td width='3%'><li style='list-style-type: none;'>".$html_icon."</td><td width='87%'><a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$child_notice->notice_header."</a></td><td>".$child_notice->notice_header_doclink."</td></tr></table></li>";
								}
								$bool=true;	
							}							
						}
					}
				}
				if ($bool) $aff_childs.=$affichage;
				if ($this->seule) {
				} else $aff_childs.="</ul>";
			}
			$this->notice_childs=$aff_childs."<br />";
		} else $this->notice_childs = "" ;
		return $this->notice_childs ;
	}

	// récupération des info de bulletinage (si applicable)
	function get_bul_info() {
		global $dbh;
		global $msg;
		if ($this->notice->niveau_biblio == 'a') {
			// récupération des données du bulletin et de la notice apparentée
			$requete = "SELECT b.tit1,b.tit4,b.notice_id,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date "; 
			$requete .= "from analysis a, notices b, bulletins c";
			$requete .= " WHERE a.analysis_notice=".$this->notice_id;
			$requete .= " AND c.bulletin_id=a.analysis_bulletin";
			$requete .= " AND c.bulletin_notice=b.notice_id";
			$requete .= " LIMIT 1";
		} elseif ($this->notice->niveau_biblio == 'b') {
			// récupération des données du bulletin et de la notice apparentée
			$requete = "SELECT tit1,tit4,notice_id,b.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date "; 
			$requete .= "from bulletins b, notices";
			$requete .= " WHERE num_notice=$this->notice_id ";
			$requete .= " AND  bulletin_notice=notice_id ";
			$requete .= " LIMIT 1";
		}
		$myQuery = pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($myQuery)) {
			$parent = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $parent->tit1;
			$this->parent_title_4 = $parent->tit4;
			$this->parent_id = $parent->notice_id;
			$this->bulletin_id = $parent->bulletin_id;
			$this->parent_numero = $parent->bulletin_numero;
			$this->parent_date = $parent->mention_date;
			$this->parent_date_date = $parent->date_date;
			$this->parent_aff_date_date = $parent->aff_date_date;
			$this->parent_bulletin_title = $parent->bulletin_titre;
		}
	} // fin get_bul_info()

}// fin class notice_affichage_cconstitutionnel
?>