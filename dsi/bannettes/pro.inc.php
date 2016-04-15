<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pro.inc.php,v 1.48.2.2 2015-12-08 14:48:37 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

print "<h1>".$msg[dsi_ban_pro]."</h1>" ;
switch($suite) {
    case 'acces':
    	$bannette = new bannette($id_bannette) ;
    	print $bannette->show_form(); 
    	
		if ($pmb_javascript_office_editor) print $pmb_javascript_office_editor ;
		break;
    case 'add':
    	$bannette = new bannette(0) ;
    	print $bannette->show_form();
    	if ($pmb_javascript_office_editor) print $pmb_javascript_office_editor ;
        break;
    case 'delete':
    	$bannette = new bannette($id_bannette) ;
    	print $bannette->delete();  
        print get_bannette_pro ($msg[dsi_ban_search], $msg[dsi_ban_search_nom], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, 0, $id_classement)) ;
		break;
    case 'update':
    	$bannette = new bannette($id_bannette) ;
    	$anc_categorie_lecteurs=    $bannette->categorie_lecteurs ;
    	$anc_groupe_lecteurs=    	$bannette->groupe_lecteurs ;
    	$temp = new stdClass();
    	$temp->id_bannette=         $id_bannette;		
		$temp->num_classement=      $num_classement;	
		$temp->nom_bannette=        $nom_bannette;	
		$temp->comment_gestion=	    $comment_gestion;
		$temp->comment_public=      $comment_public;	
		$temp->entete_mail =	    $entete_mail;	
		$temp->piedpage_mail =	    $piedpage_mail;	
		$temp->notice_tpl =	    	$notice_tpl;
		$temp->proprio_bannette=	$proprio_bannette;	
		$temp->bannette_auto=       $bannette_auto;	
		$temp->periodicite=			$periodicite;	
		$temp->diffusion_email=		$diffusion_email;
		$temp->statut_not_account=	$statut_not_account;
		$temp->nb_notices_diff=		$nb_notices_diff;
		$temp->categorie_lecteurs=	$categorie_lecteurs;
		$temp->groupe_lecteurs=		$groupe_lecteurs;
		$temp->update_type=			$update_type;
		$temp->date_last_envoi=		$form_date_last_envoi;
    	$temp->num_panier=			$num_panier;	
    	$temp->limite_type=			$limite_type;	
    	$temp->limite_nombre=		$limite_nombre;	
    	$temp->typeexport=			$typeexport;
    	$temp->prefixe_fichier=		$prefixe_fichier; 
    	$temp->group_pperso=		$group_pperso; 
    	$temp->display_notice_in_every_group=		$display_notice_in_every_group;
    	$temp->archive_number=		$archive_number;     	
    	$temp->document_generate=	$document_generate; 
    	$temp->document_notice_tpl=	$document_notice_tpl; 
    	$temp->document_insert_docnum=$document_insert_docnum; 
    	$temp->document_group=		$document_group; 
    	$temp->document_add_summary=$document_add_summary;
    	$temp->bannette_opac_accueil=$bannette_opac_accueil;
    	$temp->bannette_tpl_num=	$bannette_tpl_num;
    	$temp->param_export=array("genere_lien" => $genere_lien,
    							  "mere"=>$mere,
    							  "fille"=>$fille,
    							  "notice_mere"=>$notice_mere, 
    							  "notice_fille"=>$notice_fille, 
    							  "art_link"=>$art_link, 
    							  "bull_link"=>$bull_link,
    							  "perio_link"=>$perio_link,
    							  "bulletinage"=>$bulletinage, 
    							  "notice_art"=>$notice_art, 
    							  "notice_perio"=>$notice_perio);
		if($form_actif) {
			$bannette->update($temp); 
			if(!$id_bannette){
				$id_bannette = $bannette->id_bannette;
			}
	    	if (($majautocateg && $bannette->id_bannette && ($categorie_lecteurs!=$anc_categorie_lecteurs))
	    		||($majautogroupe && $bannette->id_bannette && ($groupe_lecteurs!=$anc_groupe_lecteurs))) {

	    		$new_categorie_lecteurs = (($categorie_lecteurs!=$anc_categorie_lecteurs) ? $categorie_lecteurs : $anc_categorie_lecteurs);
				$new_groupe_lecteurs = (($groupe_lecteurs!=$anc_groupe_lecteurs) ? $groupe_lecteurs : $anc_groupe_lecteurs);
								
				if ($majautocateg) {
					// changement d'une catégorie d'affectation tout en étant vigilant sur le groupe affectée
					if ($anc_categorie_lecteurs) {
						$req_lec = "select id_empr from empr";
						if ($new_groupe_lecteurs) {
							$req_lec .= " left join empr_groupe on (empr.id_empr=empr_groupe.empr_id) where groupe_id<>'$new_groupe_lecteurs'";
							$req_lec .= " and empr_categ='$anc_categorie_lecteurs'";
						} else {
							$req_lec .= " where empr_categ='$anc_categorie_lecteurs'";
						}
						$res_lec=pmb_mysql_query($req_lec, $dbh) ;
		    			while ($lec=pmb_mysql_fetch_object($res_lec)) {
		    				pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id_bannette'", $dbh) ;
		    			}
					}
					if ($new_categorie_lecteurs || $new_groupe_lecteurs) {
						$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)";
						if ($new_categorie_lecteurs) {
							$req_lec .= " where empr_categ='$new_categorie_lecteurs'";
							if ($new_groupe_lecteurs) $req_lec .= " and groupe_id='$new_groupe_lecteurs'";
						} else {
							$req_lec .= " where groupe_id='$new_groupe_lecteurs'";
						}
						$res_lec=pmb_mysql_query($req_lec, $dbh) ;
			    		while ($lec=pmb_mysql_fetch_object($res_lec)) {
			    			pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id_bannette', '$lec->id_empr')", $dbh) ;
		    			}
					}
				}
				
				if ($majautogroupe) {
					// changement d'un groupe d'affectation tout en étant vigilant sur la catégorie affectée
		    		if ($anc_groupe_lecteurs) {
		    			$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)";
		    			$req_lec .= " where groupe_id='$anc_groupe_lecteurs'";
		    			if ($new_categorie_lecteurs) $req_lec .= " and empr_categ<>'$new_categorie_lecteurs'";
			    		$res_lec=pmb_mysql_query($req_lec, $dbh) ;
			    		while ($lec=pmb_mysql_fetch_object($res_lec)) {
			    			pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id_bannette'", $dbh) ;
		    			}
		    		}
		    		if ($new_groupe_lecteurs || $new_categorie_lecteurs) {
		    			$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)";
		    			if ($new_groupe_lecteurs) {
		    				$req_lec .= " where groupe_id='$new_groupe_lecteurs'";
		    				if ($new_categorie_lecteurs) $req_lec .= " and empr_categ='$new_categorie_lecteurs'";
		    			} else {
		    				$req_lec .= " where empr_categ='$new_categorie_lecteurs'";
		    			}	
			    		$res_lec=pmb_mysql_query($req_lec, $dbh) ;
			    		while ($lec=pmb_mysql_fetch_object($res_lec)) {
			    			pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id_bannette', '$lec->id_empr')", $dbh) ;
		    			}	
		    		}
				}
    		}
		}
    	print get_bannette_pro ($msg[dsi_ban_search], $msg[dsi_ban_search_nom], './dsi.php?categ=bannettes&sub=pro', stripslashes($nom_bannette));
			
		print pmb_bidi(dsi_list_bannettes_info(stripslashes($nom_bannette), $id_bannette, $id_classement)) ;
        break;
	case 'duplicate':
     	print "<h1>$msg[catal_duplicate_bannette]</h1>"; 
		// routine de copie
		$bannette = new bannette($id_bannette) ;
		$bannette->id_bannette=0 ;
		$bannette->date_last_remplissage="";
		$bannette->aff_date_last_remplissage="";
		$bannette->date_last_envoi="";
		$bannette->aff_date_last_envoi="";
		$bannette->id_bannette_origine = $id_bannette;
		print pmb_bidi($bannette->show_form()) ;
        break;
    case 'search':
		print get_bannette_pro ($msg[dsi_ban_search], $msg[dsi_ban_search_nom], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, $id_bannette, $id_classement)) ;
		break;
    case 'affect_equation':
    	if ($faire=="enregistrer") {
    		//Enregistrer les affectations
    		// selectionner les equations affichées
    		if ($id_classement>0) $equ = "select id_equation from equations where num_classement='$id_classement' and proprio_equation=0";
    		if ($id_classement==0) $equ = "select id_equation from equations where proprio_equation=0 ";
    		if ($id_classement==-1) $equ = "select id_equation from equations, bannette_equation where proprio_equation=0 and num_bannette='$id_bannette' and num_equation=id_equation";
    		$res = pmb_mysql_query($equ, $dbh) or die (pmb_mysql_error()." $equ ") ;
    		if (!$bannette_equation) $bannette_equation = array();
			while ($equa=pmb_mysql_fetch_object($res)) {
				pmb_mysql_query("delete from bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id_bannette' ", $dbh) ; 
				$as = array_search($equa->id_equation,$bannette_equation) ;
				if (($as!==false) && ($as!==null) ) pmb_mysql_query("insert into bannette_equation set num_equation='$equa->id_equation', num_bannette='$id_bannette'", $dbh) ; 
				}
    		}
    	$bannette = new bannette($id_bannette) ;
    	print bannette_equation ($bannette->nom_bannette, $id_bannette) ;
		break;
    case 'affect_lecteurs':
    	if ($faire=="enregistrer") {
    		//Enregistrer les affectations
    		
    		// selectionner la localisation affichée
    		if ($pmb_lecteurs_localises && (string)$empr_location_id!="0") {
				if ((string)$empr_location_id=="") $empr_location_id=$deflt2docs_location;
				$restrict_loc = " and empr_location=$empr_location_id ";
			} else $restrict_loc = "";
    		
    		// selectionner les catégories affichées
    		if ($lect_restrict) $lect_query = " and empr_nom like '".str_replace("*","%",$lect_restrict."*")."'  order by nom_prenom, empr_cb " ;
    			else $lect_query = " order by nom_prenom, empr_cb limit 20 ";
   		
    		if ($quoi == "groups") {
    			if ($id_groupe>0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr,empr_groupe where id_empr=empr_id and groupe_id='$id_groupe' $restrict_loc ".$lect_query;
    			if ($id_groupe==0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where 1 $restrict_loc ".$lect_query;
    			if ($id_groupe==-1) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr, bannette_abon where num_bannette='$id_bannette' and num_empr=id_empr $restrict_loc ".$lect_query;
    		} else {
    			if ($id_categorie>0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where empr_categ='$id_categorie' $restrict_loc ".$lect_query;
    			if ($id_categorie==0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where 1 $restrict_loc ".$lect_query;
    			if ($id_categorie==-1) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr, bannette_abon where num_bannette='$id_bannette' and num_empr=id_empr $restrict_loc ".$lect_query;
    		}
    		
    		$res = pmb_mysql_query($equ, $dbh) or die (pmb_mysql_error()." $equ ") ;
    		if (!$bannette_abon) $bannette_abon = array();
			while ($empr=pmb_mysql_fetch_object($res)) {
				pmb_mysql_query("delete from bannette_abon where num_empr='$empr->id_empr' and num_bannette='$id_bannette'", $dbh) ; 
				$as = array_search($empr->id_empr,$bannette_abon) ;
				$sel_mail="sel_mail_".$empr->id_empr;
				if (($as!==false) && ($as!==null) ) pmb_mysql_query("insert into bannette_abon set num_empr='$empr->id_empr', num_bannette='$id_bannette', 
						bannette_mail='".$$sel_mail."'", $dbh) ; 
			}
    	}
    	$bannette = new bannette($id_bannette) ;
    	print bannette_lecteur ($bannette->nom_bannette, $id_bannette) ;
		break;
    default:
		echo window_title($database_window_title.$msg[dsi_menu_title]);
		print get_bannette_pro ($msg[dsi_ban_search], $msg[dsi_ban_search_nom], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, $id_bannette, $id_classement)) ;
        break;
    }

