<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage_ctles.class.php,v 1.11.2.4 2015-11-14 12:45:58 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/notice_affichage.class.php");

class notice_affichage_ctles extends notice_affichage {

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
		/* début modif */
		//$this->notice_childs = $this->genere_notice_childs();
		/* fin modif */
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
		if ($this->affichage_resa_expl || $this->affichage_avis_detail || $this->affichage_simili_search_head) $this->result = str_replace('!!SUITE!!', $this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_simili_search_head, $this->result);
		else $this->result = str_replace('!!SUITE!!', '', $this->result);

	} // fin genere_simple($depliable=1, $what='ISBD')

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

		/* début modif */
		// Notices parentes
		//$this->notice_public.=$this->parents;
		/* fin modif */

		$this->notice_public .= "<table>";
	//Titre
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}

		$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		//if($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "&nbsp;[".$tdoc->table[$this->notice->typdoc]."]";
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit3) $this->notice_public .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->mention_edition)  $this->notice_public .= "&nbsp;-&nbsp;".$this->notice->mention_edition ;
		$this->notice_public.="</span></td></tr>";

	//Préparation des champs personnalisés
	if (!$this->p_perso->no_special_fields) {
		if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
	}

	if(!$this->memo_perso_){
		$this->memo_perso_["FIELDS"]=array();
	}

	//PPN
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "ppn001")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".str_replace("Sudoc : ","",$p["AFF"])."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	//ISSN
		// ISBN ou NO. commercial
		$issn=$this->notice->code;
		$mes_pp= new parametres_perso("notices");
		$mes_pp->get_values($this->notice_id);
		$values = $mes_pp->values;
		foreach ( $values as $field_id => $vals ) {
			if($mes_pp->t_fields[$field_id]["NAME"] == "cp_issn_autres") {
				foreach ( $vals as $value ) {
					if($issn)$issn.=". ";
					$issn.=$mes_pp->get_formatted_output(array($value),$field_id);//Val
				}
			}
		}
		if ($issn) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".htmlentities($issn,ENT_QUOTES, $charset)."</td></tr>";

	//Auteurs
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";

	//Editeurs
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "cp_editeurs")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}
		//if ($this->notice->tit2) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		//if ($this->notice->tit3) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;

		//if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";

		// mention d'édition
		//if ($this->notice->mention_edition) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";

	// Années de publication
		if ($this->notice->year)
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".($charset != "utf-8"?"Années de publication":utf8_encode("Années de publication"))." :</span></td><td>".$this->notice->year."</td></tr>" ;

	// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		/*$this->notice_public .= $annee ;
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}
		}*/
		// Autre editeur
		/*if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}*/
	// Numérotation
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "numerotation207")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Pays de publication
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "paysdepublication102")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}


	// Langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
		}

	// Périodicité
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "periodicite110")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Note générale
		if ($this->notice->n_gen) $zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		if ($zoneNote) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";

	// Périodicité
		if (!$this->p_perso->no_special_fields) {
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p["AFF"] && ($p["NAME"] == "annexes320")){
					$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
					unset($this->memo_perso_["FIELDS"][$i]);
					break;
				}
			}
		}

	// Catégories
		if ($this->categories_toutes) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>Sujets :</span></td><td>".$this->categories_toutes."</td></tr>";

	// Titre clé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrecle530")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre abrégé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreabrege531")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) parallèle(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreparallele510")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) de couverture
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titredecouverture512")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) courant(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrecourant515")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}
	// Titre(s) historique(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrehistorique520")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Autres titres
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreautres517")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre développé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titredeveloppe532")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	//Lien notices
		if($this->parents){
			$this->notice_public.=$this->parents;
		}
		$this->genere_notice_childs();
		if($this->notice_childs){
			$this->notice_public.=$this->notice_childs;
		}

	// Origine de la notice
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "originenotice")){
				$this->notice_public.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

		// collection
		/*if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
		}*/

		// Titres uniformes
		/*if($this->notice->tu_print_type_2) {
			$this->notice_public.=
			"<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}*/
		// zone de la collation
		/*if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}*/
		//if ($this->notice->ill) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		//if ($this->notice->size) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		//if ($this->notice->accomp) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";


		//if ($this->notice->prix) $this->notice_public .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";



		if (!$short) $this->notice_public .= $this->aff_suite() ;
		else $this->notice_public.=$this->genere_in_perio();


		$this->notice_public.="</table>\n";

		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();

		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;

		return;
	} // fin do_public($short=0,$ex=1)

	// génération du header----------------------------------------------------
	function do_header($id_tpl=0) {

		global $opac_notice_reduit_format ;
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_url_base;
		global $charset;
		global $tdoc;

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



		//Titre
		//if($tdoc->table[$this->notice->typdoc]) $this->notice_header .= "&nbsp;[".$tdoc->table[$this->notice->typdoc]."]";
		if ($this->notice->tit4) $this->notice_header .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit3) $this->notice_header .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->mention_edition)  $this->notice_header .= "&nbsp;-&nbsp;".$this->notice->mention_edition ;

		$this->notice_header .= $aff_bullperio_title;

		//$this->notice_header_without_html = $this->notice_header;

		$this->notice_header = "<span !!zoteroNotice!! class='header_title'>".$this->notice_header."</span>";
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$this->notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$this->notice_header);
		}else $this->notice_header =str_replace("!!zoteroNotice!!","",$this->notice_header);

		$this->notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$this->notice_header;

		$notice_header_suite = "";
		//if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		//if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= " / ".$this->auteurs_principaux;
		//if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		//if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		//if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
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

	function aff_suite() {
		global $msg;
		global $charset,$opac_categories_affichage_ordre,$lang,$pmb_keyword_sep;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;

		// afin d'éviter de recalculer un truc déjà calculé...
		if ($this->affichage_suite_flag) return $this->affichage_suite ;

		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";

		// toutes indexations
		$ret_index = "";

		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente
		//if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		//else $libelle_key = 	$msg['motscle_start'];

		// indexation libre
		//$mots_cles = $this->do_mots_cle() ;
		//if($mots_cles) $ret_index.= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";

		// indexation interne
		/*if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}*/
		//if ($ret_index) {
		//	$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		//}

		// résumé
		//if($this->notice->n_resume) $ret .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";

		// note de contenu
		//if($this->notice->n_contenu) $ret .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";

		//Champs personalisés
		$perso_aff = "" ;
		/*if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]){
					if($p["NAME"] == "ppn001"){
						$perso_aff .="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".str_replace("Sudoc : ","",$p["AFF"])."</td></tr>";
					}else{
						$perso_aff .="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
					}
				}
			}
		}*/
		$ret .= $perso_aff ;

		/*if ($this->notice->lien) {
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
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
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}*/
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
			}else {
				$ret.= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}
		}

	//PCP
		$ret_index = "";
		$requete = "select * from (
			select libelle_thesaurus, c0.libelle_categorie as categ_libelle, c0.comment_public, n0.id_noeud , n0.num_parent, langue_defaut,id_thesaurus, if(c0.langue = '".$lang."',2, if(c0.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM noeuds as n0, categories as c0,thesaurus,notices_categories
			where notices_categories.num_noeud=n0.id_noeud and n0.id_noeud = c0.num_noeud and n0.num_thesaurus=id_thesaurus and
			notices_categories.notcateg_notice=".$this->notice_id." AND id_thesaurus='2' order by id_thesaurus, n0.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($opac_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";
		$result_categ=@pmb_mysql_query($requete);
		if ($result_categ && pmb_mysql_num_rows($result_categ)) {
			$ret_index .= "<tr><td align='left' class='bg-grey'><span class='etiq_champ'>PCP :</span></td><td>";
			$first=true;
			while(($res_categ = pmb_mysql_fetch_object($result_categ))) {
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$comment_public=$res_categ->comment_public ;
				// Si il y a présence d'un commentaire affichage du layer
				$result_com = categorie::zoom_categ($categ_id, $comment_public);
				$libelle_aff_complet = inslink($libelle_categ,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
				$libelle_aff_complet .= $result_com['zoom'];
				if(!$first)$ret_index .=" ".$pmb_keyword_sep." ";
				$first=false;
				$ret_index .=$libelle_aff_complet;
			}
			$ret_index .= "</td></tr>";
		}

		if ($ret_index) {
			$ret.=$ret_index;
		}

		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()

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
			/*if (pmb_mysql_num_rows($result_linked)==1) {
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
			} else {*/
				if (!$r_type[$r_rel->relation_type]) {
					$r_type[$r_rel->relation_type]=1;
					if ($ul_opened) $this->parents.="</td></tr>";
					else {
						//$this->parents.="<br />";
						$ul_opened=true;
					}
					$this->parents.="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$relation_listup->table[$r_rel->relation_type]." :</span></td><td>\n";
					//$this->parents.="<ul class='notice_rel'>\n";
				}
				//$this->parents.="<li>";
				if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$r_rel->linked_notice,$this->lien_rech_notice)."&seule=1'>";
				//$this->parents.=$parent_notice->notice_header;
				$this->parents.=$memo_notice[$r_rel->linked_notice]["header_without_doclink"];
				if ($this->lien_rech_notice) $this->parents.="</a>";
				$this->parents.="</br>\n";
			/*}*/
		}
		//if (pmb_mysql_num_rows($result_linked)>1)
				$this->parents.="</td></tr>\n";
	return ;
	} // fin do_parents()

	function genere_notice_childs() {
		global $msg, $opac_notice_affichage_class ;
		global $memo_notice;
		global $relation_typedown;

		/* début modif */
		//Je ne veux que les liens vers les notices liées, pas de notices dépliables.
		$this->seule=0;
		/* fin modif */
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
				/* début modif */
				$affichage="<tr><td align='left' class='bg-grey'><span class='etiq_champ'>".$relation_typedown->table[$rel_type]." :</span></td>\n";
				if ($this->seule) {
				} else $affichage.="<td>";
				/* fin modif */
				$bool=false;
				for ($i=0; (($i<count($child_notices))&&(($i<100)||  ($this->seule))); $i++) {
					if (!$this->antiloop[$child_notices[$i]]) {
						//if(!$this->seule && $memo_notice[$child_notices[$i]]["niveau_biblio"]!='b' && $memo_notice[$child_notices[$i]]["header"]) {
						if(!$this->seule && $memo_notice[$child_notices[$i]]["niveau_biblio"]!='b' && $memo_notice[$child_notices[$i]]["header_without_doclink"]) {
							//$affichage.="<li><a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$memo_notice[$child_notices[$i]]["header"]."</a></li>";
							$affichage.="<a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$memo_notice[$child_notices[$i]]["header_without_doclink"]."</a><br/>";
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
									/* début modif */
									$affichage.="<a href='".str_replace("!!id!!",$child_notices[$i],$this->lien_rech_notice)."'>".$child_notice->notice_header."</a><br/>";
									/* fin modif */
								}
								$bool=true;
							}
						}
					}
				}
				if ($bool) $aff_childs.=$affichage;
				if ($bool && (count($child_notices)>100) && (!$this->seule)) {
					$aff_childs.="<br />";
					if ($this->lien_rech_notice) $aff_childs.="<a href='".str_replace("!!id!!",$this->notice_id,$this->lien_rech_notice)."&seule=1'>";
					$aff_childs.=sprintf($msg["see_all_childs"],20,count($child_notices),count($child_notices)-20);
					if ($this->lien_rech_notice) $aff_childs.="</a>";
				}
				/* début modif */
				if ($this->seule) {
				} else $aff_childs.="</td>\n</tr>\n";
				/* fin modif */
			}
			$this->notice_childs=$aff_childs."<br />";
		} else $this->notice_childs = "" ;
		return $this->notice_childs ;
	}

	function affichage_etat_collections() {
		global $msg;
		global $pmb_etat_collections_localise;
		if ($this->notice->niveau_biblio!='s' && $this->notice->niveau_hierar!=1) return "";
		if($pmb_etat_collections_localise) {
			$this->coll_state_list("",0,0,0,1);
		} else {
			$this->coll_state_list("",0,0,0,0);
		}
		if($this->coll_state_list_nbr) {
			$affichage.= "<h3><span id='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$this->coll_state_list_liste;
		}

		return $affichage;
	} // fin affichage_etat_collections()

	//Récupérer de l'affichage complet
	function coll_state_list($base_url,$filtre,$debut=0,$page=0, $type=0) {
		global $dbh, $msg,$nb_per_page_a_search, $tpl_collstate_surloc_liste, $tpl_collstate_surloc_liste_line;
		global $opac_sur_location_activate, $opac_view_filter_class;
		global $opac_collstate_order, $opac_url_base;
		global $empr_location;
		global $include_path;
		global $script_coll_modif_ctles_is_include;

		$tpl_collstate_liste="
		<table class='exemplaires' cellpadding='2' width='100%'>
		<tbody>
		<tr>
		<th>Biblioth&egrave;que</th>
		<!--<th>Emplacement</th>-->
		<th>P&ocircle de conservation</th>
		<th>Cote</th>
		<th>Etat de collections</th>
		<th>Lacunes</th>
		<th>Fonds sp&eacute;cifique</th>
		<th>Notes</th>
		</tr>
		!!collstate_liste!!
		</tbody>
		</table>
		";
		if(!$script_coll_modif_ctles_is_include){
			$script_coll_modif_ctles_is_include=true;
			$tpl_collstate_liste="
<script type=\"text/javascript\">
function coll_modif_update(id,sql_field,texte){
	// récupération du form d'édition de la collection
	var action = new http_request();
	var url = \"./ajax.php?module=ajax&categ=extend&id=\"+id+\"&quoifaire=coll_save&texte=\"+texte+\"&sql_field=\"+sql_field;
	action.request(url);
}
</script>".$tpl_collstate_liste;
		}

		$tpl_collstate_liste_line="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
		<td !!tr_javascript!! >!!localisation!!</td>
		<!--<td !!tr_javascript!! >!!emplacement_libelle!!</td>-->
		<td !!tr_javascript!! >!!statut_libelle!!</td>
		<td !!tr_javascript!! >!!cote!!</td>
		<td !!tr_javascript!! >!!state_collections!!</td>
		<td !!tr_javascript!! >!!lacune!!</td>
		<td !!tr_javascript!! >!!cp_stat_fonds!!</td>
		<td !!tr_javascript!! >!!note!!</td>
		</tr>";

		$location=$filtre->location;
		if($opac_view_filter_class){
			$req="SELECT  collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id, docs_location
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and location_id=idlocation and idlocation in(". implode(",",$opac_view_filter_class->params["nav_collections"]).")
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		} else {
			$req="SELECT collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT  JOIN docs_location ON location_id = idlocation
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		}
		$myQuery = pmb_mysql_query($req, $dbh);

		if(($this->coll_state_list_nbr = pmb_mysql_num_rows($myQuery))) {

			$parity=1;
			while(($coll = pmb_mysql_fetch_object($myQuery))) {
				$my_collstate=new collstate($coll->collstate_id);
				if ($parity++ % 2) $pair_impair = "even"; else $pair_impair = "odd";
				$tr_javascript="  ";
				$tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";

				$line = str_replace('!!tr_javascript!!',$tr_javascript , $tpl_collstate_liste_line);
				$line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
				$line = str_replace('!!pair_impair!!',$pair_impair , $line);
				if ($opac_sur_location_activate) {
					$line = str_replace('!!surloc!!', $my_collstate->surloc_libelle, $line);
				}
				if ($my_collstate->num_infopage) {
					if ($my_collstate->surloc_id != "0") $param_surloc="&surloc=".$my_collstate->surloc_id;
					else $param_surloc="";
					$collstate_location = "<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$my_collstate->num_infopage."&location=".$my_collstate->location_id.$param_surloc."\" alt=\"".$msg['location_more_info']."\" title=\"".$msg['location_more_info']."\">".$my_collstate->location_libelle."</a>";
				} else
					$collstate_location = $my_collstate->location_libelle;
				$line = str_replace('!!localisation!!', $collstate_location, $line);
				$line = str_replace('!!cote!!', $my_collstate->cote, $line);
				$line = str_replace('!!type_libelle!!', $my_collstate->type_libelle, $line);
				$line = str_replace('!!emplacement_libelle!!', $my_collstate->emplacement_libelle, $line);
				$line = str_replace('!!origine!!', $my_collstate->origine, $line);
				if($empr_location==$my_collstate->location_id){
					// modif des notes
					$tpl_note_modif="
					<a onclick=\"document.getElementById('note_modif_".$coll->collstate_id."').style.display='block'; return false;\"  href=\"#\">
					<img align='absmiddle' border='0' alt='Editer' title='Editer' src='./images/tag.png'>
					</a>
					<div id='note_modif_".$coll->collstate_id."' style='display:none'>
					<textarea id='note_modif_text_".$coll->collstate_id."' class='saisie-80em'' wrap='virtual' rows='4' cols='40' name='note_modif_text_".$coll->collstate_id."'>".$my_collstate->note."</textarea><br />
					<input class='bouton' type='button' onclick=\"document.getElementById('note_modif_".$coll->collstate_id."').style.display='none';\" value='Annuler'>
					<input class='bouton' type='button' onclick=\"coll_modif_update(".$coll->collstate_id.",'collstate_note', document.getElementById('note_modif_text_".$coll->collstate_id."').value);
					document.getElementById('note_modif_".$coll->collstate_id."').style.display='none';
					document.getElementById('note_contens".$coll->collstate_id."').innerHTML=document.getElementById('note_modif_text_".$coll->collstate_id."').value;\" value='Enregistrer'>
					</div>
					";
					// modif du statut de la collection
					$on_change="coll_modif_update(".$coll->collstate_id.",'collstate_statut',document.getElementById('collstate_statut_".$coll->collstate_id."').value )";
					$select =  gen_liste("select archstatut_id, archstatut_gestion_libelle from arch_statut order by 2", "archstatut_id", "archstatut_gestion_libelle", "collstate_statut_".$coll->collstate_id, $on_change, $my_collstate->statut, "", "","","",0) ;
					$line = str_replace('!!statut_libelle!!',$select, $line);
				}else {
					$tpl_coll_modif="";
					$tpl_lacune_modif="";
					$tpl_note_modif="";
					$line = str_replace('!!statut_libelle!!', $my_collstate->statut_opac_libelle, $line);
				}

				$line = str_replace('!!state_collections!!',"<div id='coll_contens_".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->state_collections)."</div>".$tpl_coll_modif, $line);
				$line = str_replace('!!archive!!',$my_collstate->archive, $line);
				$line = str_replace('!!lacune!!', "<div id='lacune_contens_".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->lacune)."</div>".$tpl_lacune_modif, $line);
				//cp_stat_fonds
				$requete="SELECT collstate_custom_small_text FROM collstate_custom_values JOIN collstate_custom ON collstate_custom_champ=idchamp WHERE collstate_custom_origine='".$coll->collstate_id."' AND name='cp_stat_fonds'";
				$rescp=pmb_mysql_query($requete);
				if($rescp && pmb_mysql_num_rows($rescp)){
					$line = str_replace('!!cp_stat_fonds!!', "<div id='cp_stat_fonds".$coll->collstate_id."'>".str_replace("\n","<br />",pmb_mysql_result($rescp,0,0))."</div>", $line);
				}else{
					$line = str_replace('!!cp_stat_fonds!!', "<div id='cp_stat_fonds".$coll->collstate_id."'></div>", $line);
				}
				$line = str_replace('!!note!!', "<div id='note_contens".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->note)."</div>".$tpl_note_modif, $line);
				$liste.=$line;
			}
			$liste = str_replace('!!collstate_liste!!',$liste , $tpl_collstate_liste);
			$liste = str_replace('!!base_url!!', $base_url, $liste);
			$liste = str_replace('!!location!!', $location, $liste);
		} else {
			$liste= $msg["collstate_no_collstate"];
		}
		$this->coll_state_list_liste=$liste;

	}

	// récupération des categories ------------------------------------------------------------------
	function fetch_categories() {
		global $opac_thesaurus, $opac_categories_categ_in_line, $pmb_keyword_sep, $opac_categories_affichage_ordre;
		global $dbh;
		global $lang,$opac_categories_show_only_last;
		global $categories_memo,$libelle_thesaurus_memo;
		global $categories_top;

		$categ_repetables = array() ;
		if(!count($categories_top)) {
			$q = "select num_thesaurus,id_noeud from noeuds where num_parent in(select id_noeud from noeuds where autorite='TOP') ";
			$r = pmb_mysql_query($q, $dbh);
			while(($res = pmb_mysql_fetch_object($r))) {
				$categories_top[]=$res->id_noeud;
			}
		}
		$requete = "select * from (
			select libelle_thesaurus, c0.libelle_categorie as categ_libelle, c0.comment_public, n0.id_noeud , n0.num_parent, langue_defaut,id_thesaurus, if(c0.langue = '".$lang."',2, if(c0.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM noeuds as n0, categories as c0,thesaurus,notices_categories
			where notices_categories.num_noeud=n0.id_noeud and n0.id_noeud = c0.num_noeud and n0.num_thesaurus=id_thesaurus and
			notices_categories.notcateg_notice=".$this->notice_id." AND id_thesaurus!='2' order by id_thesaurus, n0.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($opac_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";

		$result_categ=@pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result_categ)) {
			while(($res_categ = pmb_mysql_fetch_object($result_categ))) {
				$libelle_thesaurus=$res_categ->libelle_thesaurus;
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$comment_public=$res_categ->comment_public ;
				$num_parent=$res_categ->num_parent ;
				$langue_defaut=$res_categ->langue_defaut ;
				$categ_head=0;
				if(in_array($categ_id,$categories_top))$categ_head=1;

				if ($opac_categories_show_only_last || $categ_head) {
					if ($opac_thesaurus) $catalog_form="[".$libelle_thesaurus."] ".$libelle_categ;
					// Si il y a présence d'un commentaire affichage du layer
					$result_com = categorie::zoom_categ($categ_id, $comment_public);
					$libelle_aff_complet = inslink($libelle_categ,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
					$libelle_aff_complet .= $result_com['zoom'];

					if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus][] =$libelle_aff_complet;
					else $categ_repetables['MONOTHESAURUS'][] =$libelle_aff_complet ;

				} else {
					if(!$categories_memo[$categ_id]) {
						$anti_recurse[$categ_id]=1;
						$path_table='';
						$requete = "
						select id_noeud as categ_id,
						num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,
						num_renvoi_voir as categ_see,
						note_application as categ_comment,
						if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
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
						// pour libellé complet mais sans le nom du thésaurus
						$libelle_aff_complet = $catalog_form ;

						if ($opac_thesaurus) $catalog_form="[".$libelle_thesaurus."] ".$catalog_form;

						//$categ = new category($categ_id);
						// Si il y a présence d'un commentaire affichage du layer
						$result_com = categorie::zoom_categ($categ_id, $comment_public);
						$libelle_aff_complet = inslink($libelle_aff_complet,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
						$libelle_aff_complet .= $result_com['zoom'];
						if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus][] =$libelle_aff_complet;
						else $categ_repetables['MONOTHESAURUS'][] =$libelle_aff_complet ;

						$categories_memo[$categ_id]=$libelle_aff_complet;
						$libelle_thesaurus_memo[$categ_id]=$libelle_thesaurus;

					} else {
						if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus_memo[$categ_id]][] =$categories_memo[$categ_id];
						else $categ_repetables['MONOTHESAURUS'][] =$categories_memo[$categ_id] ;
					}
				}
			}
		}

		while (list($nom_thesaurus, $val_lib)=each($categ_repetables)) {
			//c'est un tri par libellé qui est demandé
			if ($opac_categories_affichage_ordre==0){
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
			if ($opac_thesaurus) {
				if (!$opac_categories_categ_in_line) {
					$categ_repetables_aff = "[".$nom_thesaurus."] ".implode("<br />[".$nom_thesaurus."] ",$val_lib) ;
				}else {
					$categ_repetables_aff = "<b>".$nom_thesaurus."</b><br />".implode(" $pmb_keyword_sep ",$val_lib) ;
				}
			} elseif (!$opac_categories_categ_in_line) {
				$categ_repetables_aff = implode("<br />",$val_lib) ;
			} else {
				$categ_repetables_aff = implode(" $pmb_keyword_sep ",$val_lib) ;
			}
			if($categ_repetables_aff) $tmpcateg_aff .= "$categ_repetables_aff<br />";
		}
		$this->categories_toutes = $tmpcateg_aff;
	} // fin fetch_categories()

}// fin class notice_affichage_ctles
?>