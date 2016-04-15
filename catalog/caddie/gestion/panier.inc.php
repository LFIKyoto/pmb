<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: panier.inc.php,v 1.18 2015-06-19 09:23:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($action) {
	case 'new_cart':
		$cart_form = str_replace('!!autorisations_users!!', aff_form_autorisations("",1), $cart_form);
		$cart_form = str_replace('!!formulaire_action!!', "./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=valid_new_cart&item=$item", $cart_form);
		$cart_form = str_replace('!!formulaire_annuler!!', "./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&item=$item", $cart_form);
		$select_cart="
		<select name='cart_type' onchange='show_hide_acces_rapide(this.value);'>
			<option value='NOTI' selected>$msg[caddie_de_NOTI]</option>
			<option value='EXPL'>$msg[caddie_de_EXPL]</option>
			<option value='BULL'>$msg[caddie_de_BULL]</option>
		</select>
		<input type='hidden' name='current_print' value='$current_print'/>";
	 	$cart_form=str_replace('!!cart_type_select!!', $select_cart, $cart_form);
	 	$classementGen = new classementGen('caddie', '0');
	 	$cart_form = str_replace("!!object_type!!",$classementGen->object_type,$cart_form);
	 	$cart_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$cart_form);
		print $cart_form ;
		break;
	case 'edit_cart':
		$myCart= new caddie($idcaddie);
		$cart_edit_form = str_replace('!!formulaire_action!!', "./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=save_cart&item=$item&idcaddie=$idcaddie", $cart_edit_form);
		$cart_edit_form = str_replace('!!formulaire_annuler!!', "./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&item=$item", $cart_edit_form);
		$cart_edit_form = str_replace('!!idcaddie!!', $idcaddie, $cart_edit_form);
		$cart_edit_form = str_replace('!!name!!', htmlentities($myCart->name,ENT_QUOTES, $charset), $cart_edit_form);
		$cart_edit_form = str_replace('!!name_suppr!!', htmlentities(addslashes($myCart->name),ENT_QUOTES, $charset), $cart_edit_form);
		$type = "caddie_de_".$myCart->type;
		$cart_edit_form = str_replace('!!cart_type!!', $msg[$type], $cart_edit_form);
		$cart_edit_form = str_replace('!!comment!!', htmlentities($myCart->comment,ENT_QUOTES, $charset), $cart_edit_form);
		$cart_edit_form = str_replace('!!autorisations_users!!', aff_form_autorisations($myCart->autorisations,0), $cart_edit_form);
		//Liaisons pour le panier
		$info_liaisons="";
		$message_delete_warning = "";
		foreach ($myCart->liaisons as $type => $values){
			if(count($values)){
				$info_liaisons.="<br>";
				switch ($type){
					case "etageres":
						$info_liaisons.="<div class='row'>
                                            <label for='' class='etiquette'>".$msg["etagere_menu"]."</label>
                                        </div>
                                        <div class='row'>";
						$link="<a href='./catalog.php?categ=etagere&sub=constitution&action=edit_etagere&idetagere=!!id!!'>!!name!!</a>";
						break;
					case "bannettes":
						$info_liaisons.="<div class='row'>
                                            <label for='' class='etiquette'>".$msg["dsi_menu_bannettes"]."</label>
                                        </div>
                                        <div class='row'>";
						if ($dsi_active && (SESSrights & DSI_AUTH)) {
							$link="<a href='./dsi.php?categ=bannettes&sub=pro&id_bannette=!!id!!&suite=acces'>!!name!!</a>";
						} else {
							$link="!!name!!";
						}
						break;
					case "rss_flux":
						$info_liaisons.="<div class='row'>
                                            <label for='' class='etiquette'>".$msg["dsi_menu_flux"]."</label>
                                        </div>
                                        <div class='row'>";
						if ($dsi_active && (SESSrights & DSI_AUTH)) {
							$link="<a href='./dsi.php?categ=fluxrss&id_rss_flux=!!id!!&suite=acces'>!!name!!</a>";
						} else {
							$link="!!name!!";
						}
						break;
					case "connectors":
						$info_liaisons.="<div class='row'>
                                           <label for='' class='etiquette'>".$msg["admin_connecteurs_sets"]."</label>
                                       </div>
                                       <div class='row'>";
						if (SESSrights & ADMINISTRATION_AUTH) {
							$link="<a href='./admin.php?categ=connecteurs&sub=out_sets&action=edit&id=!!id!!'>!!name!!</a>";
						} else {
							$link="!!name!!";
						}
						break;
					default://On ne doit pas passer par là
						$info_liaisons="";
						break 2;//On sort aussi du foreach
				}
				foreach ($values as $infos){
					$info_liaisons.=str_replace(array("!!id!!","!!name!!"),array($infos["id"],htmlentities($infos["lib"], ENT_QUOTES, $charset)), $link);
				}
				$info_liaisons.="</div>";
			}
		}
		if($info_liaisons){
			$liaison_tpl=str_replace("<!-- info_liaisons -->",$info_liaisons,$liaison_tpl);
			$cart_edit_form = str_replace('<!-- liaisons -->', $liaison_tpl, $cart_edit_form);
			$message_delete_warning = $msg["caddie_used_in_warning"];
			foreach ($myCart->liaisons as $type => $values){
				if(count($values)){
					switch ($type){
						case "etageres":
							$message_delete_warning .= "\\n- ".$msg["etagere_menu"];
							break;
						case "bannettes":
							$message_delete_warning .= "\\n- ".$msg["dsi_menu_bannettes"];
							break;
						case "rss_flux":
							$message_delete_warning .= "\\n- ".$msg["dsi_menu_flux"];
							break;
						case "connectors":
							$message_delete_warning .= "\\n- ".$msg["admin_connecteurs_sets"];
							break;
						default://On ne doit pas passer par là
							$info_liaisons="";
							break 2;//On sort aussi du foreach
					}
				}
			}
			$message_delete_warning .= "\\n";
		}		
		print confirmation_delete("./catalog.php?categ=caddie&action=del_cart&idcaddie=",$message_delete_warning);
		$classementGen = new classementGen('caddie', $idcaddie);
		$cart_edit_form = str_replace("!!object_type!!",$classementGen->object_type,$cart_edit_form);
		$cart_edit_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$cart_edit_form);
		//acces rapide
		if ($myCart->type=="NOTI") {
			$cart_edit_form = str_replace("!!acces_rapide!!","<label class='etiquette' for='form_type'>".$msg["caddie_fast_access"]."</label>&nbsp;<input type='checkbox' name='acces_rapide' ".($myCart->acces_rapide?"checked='checked'":"").">",$cart_edit_form);
		} else {
			$cart_edit_form = str_replace("!!acces_rapide!!","",$cart_edit_form);
		}
		print $cart_edit_form ;
		break;
	case 'del_cart':
		$myCart= new caddie($idcaddie);
		$myCart->delete();
		aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1,1);
		break;
	case 'save_cart':
		$myCart= new caddie($idcaddie);
		if (is_array($cart_autorisations)) $autorisations=implode(" ",$cart_autorisations);
				else $autorisations="1";
		$myCart->autorisations = $autorisations;
		$myCart->name = $cart_name;
		$myCart->comment = $cart_comment;
		$myCart->classementGen = $classementGen_caddie;
		$myCart->acces_rapide = (isset($acces_rapide)?1:0);
		if($form_actif) $myCart->save_cart();
		aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
		break;
	case 'del_item':
		$myCart= new caddie($idcaddie);
		if ($object_type=="EXPL_CB") $myCart->del_item_blob($item);
			else $myCart->del_item($item);
		print pmb_bidi(aff_cart_titre ($myCart));
		print aff_cart_nb_items ($myCart) ;
		aff_cart_objects ($idcaddie, "./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=$idcaddie" );
		break;
	case 'valid_new_cart':
		$myCart = new caddie(0);
		$myCart->name = $cart_name;
		$myCart->type = $cart_type;
		$myCart->comment = $cart_comment;
		if (is_array($cart_autorisations)) $autorisations=implode(" ",$cart_autorisations);
				else $autorisations="";
		$myCart->autorisations = $autorisations;
		$myCart->classementGen = $classementGen_caddie;
		$myCart->acces_rapide = (isset($acces_rapide)?1:0);
		if($form_actif) $myCart->create_cart();
		aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
		break;
	default:
		if($idcaddie) {
			$myCart = new caddie($idcaddie);
			print pmb_bidi(aff_cart_titre ($myCart));
			print pmb_bidi(aff_cart_nb_items ($myCart));
			aff_cart_objects ($idcaddie, "./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=$idcaddie" );
			} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
	}
