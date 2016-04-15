<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: douchette.inc.php,v 1.11 2015-07-08 14:16:53 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if($idcaddie) {
	$myCart = new caddie($idcaddie);
	print pmb_bidi(aff_cart_titre ($myCart));
	switch ($action) {
		case 'pointe_item':
			if($form_cb_expl) {
				$expl_ajout_ok = 1 ;
				$query = "select expl_id from exemplaires where expl_cb='$form_cb_expl'";
				$result = pmb_mysql_query($query, $dbh);
				if(!pmb_mysql_num_rows($result)) {
					// exemplaire inconnu
					$message_ajout_expl =  "<strong>$form_cb_expl&nbsp;: $msg[367]</strong>";
					$expl_ajout_ok = 0 ;
					$alert_sound_list[]="critique";
					} else {
						$expl_trouve = pmb_mysql_fetch_object($result);
						$item = $expl_trouve->expl_id;
						if($stuff = get_expl_info($item)) {
							$stuff = check_pret($stuff);
							} else {
								$message_ajout_expl = "<strong>$form_cb_expl&nbsp;: $msg[395]</strong>";
								$expl_ajout_ok = 0 ;
								$alert_sound_list[]="critique";
								}
						}
				}
			$res_ajout = $myCart->pointe_item($item,"EXPL", $form_cb_expl, "EXPL_CB" );
			print pmb_bidi(aff_cart_nb_items ($myCart)) ;
			
			// form de saisie cb exemplaire
			print get_cb_expl($msg["caddie_pointe_expl"], $msg[661], "./catalog.php?categ=caddie&sub=pointage&moyen=douchette&action=pointe_item&idcaddie=$idcaddie", 1);
			if ($expl_ajout_ok) {
				if ($res_ajout==CADDIE_ITEM_OK) {
					print "<hr /><div class='row'><span class='erreur'>".$msg["caddie_".$myCart->type."_pointe"]."</span></div><hr />";
					print $begin_result_expl_liste_unique;
					print pmb_bidi(print_info($stuff,0,1)); 
					}
				if ($res_ajout==CADDIE_ITEM_NULL) {
					print "<hr /><div class='row'><span class='erreur'>$msg[caddie_item_null]</span></div><hr />";
					$alert_sound_list[]="critique";
					}
				if ($res_ajout==CADDIE_ITEM_IMPOSSIBLE_BULLETIN) {
					print "<hr /><div class='row'><span class='erreur'>$msg[caddie_pointe_item_impossible_bulletin]</span></div><hr />";
					$alert_sound_list[]="critique";
					}	
				if ($res_ajout==CADDIE_ITEM_INEXISTANT) {
					print "<hr /><div class='row'><span class='erreur'>$form_cb_expl&nbsp;: $msg[caddie_pointe_inconnu_panier]</span></div><hr />";
					$alert_sound_list[]="critique";
					}	
				} else print "<hr /><div class='row'><span class='erreur'>$message_ajout_expl</span></div><hr />" ;
			
			break;
		default:
			print aff_cart_nb_items ($myCart) ;
			// form de saisie cb exemplaire
			print get_cb_expl($msg["caddie_pointe_expl"], $msg[661], "./catalog.php?categ=caddie&sub=pointage&moyen=douchette&action=pointe_item&idcaddie=$idcaddie", 1);
			break;
		}

	} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=douchette", "", $msg["caddie_select_pointe"], "", 0, 0, 0);
