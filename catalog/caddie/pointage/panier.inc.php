<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: panier.inc.php,v 1.2 2014-09-05 07:59:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if($idcaddie) {
	$myCart = new caddie($idcaddie);
	switch ($action) {
		case 'choix_quoi':
			if ($idcaddie_selected) {
				$myCart_selected = new caddie($idcaddie_selected);
				print pmb_bidi(aff_cart_titre ($myCart_selected));
				print aff_cart_nb_items ($myCart_selected) ;
				print aff_choix_quoi ("./catalog.php?categ=caddie&sub=pointage&moyen=panier&action=pointe_item&idcaddie=$idcaddie&idcaddie_selected=$idcaddie_selected",
					"./catalog.php?categ=caddie&sub=pointage&moyen=panier&action=&object_type=NOTI&idcaddie=$idcaddie&item=0", 
					$msg["caddie_choix_pointe_panier"], 
					$msg["caddie_item_pointer"], 
					"",false,$myCart->type);
			}
			print pmb_bidi(aff_cart_titre ($myCart));
			print aff_cart_nb_items ($myCart) ;
		break;
		case 'pointe_item':
			print pmb_bidi(aff_cart_titre ($myCart));
			print aff_cart_nb_items ($myCart) ;
			if ($idcaddie_selected) {
				$myCart_selected = new caddie($idcaddie_selected);
				$liste_0=$liste_1= array();
				if ($elt_flag) {
					$liste_0 = $myCart_selected->get_cart("FLAG", $elt_flag_inconnu) ;
				}	
				if ($elt_no_flag) {
					$liste_1= $myCart_selected->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
				}	
				$liste= array_merge($liste_0,$liste_1);
				if($liste) {
					while(list($cle, $object) = each($liste)) {
						$myCart->pointe_item($object,$myCart_selected->type);	
					}
				}	
			}
			print "<h3>".$msg["caddie_menu_pointage_apres_pointage"]."</h3>";
			print pmb_bidi(aff_cart_nb_items ($myCart)) ;
			aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "choix_quoi", $msg["caddie_select_pointe_panier"], "", 0, 0, 0, true,1);
			break;
		default:
			print pmb_bidi(aff_cart_titre ($myCart));
			print pmb_bidi(aff_cart_nb_items ($myCart));
			aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "choix_quoi", $msg["caddie_select_pointe_panier"], "", 0, 0, 0, true, 1);
			break;
	}

} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "", $msg["caddie_select_pointe"], "", 0, 0, 0);