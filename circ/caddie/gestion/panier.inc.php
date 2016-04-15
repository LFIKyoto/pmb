<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: panier.inc.php,v 1.4 2015-06-10 07:22:17 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($action) {
	case 'new_cart':
		$empr_cart_form = str_replace('!!autorisations_users!!', aff_form_autorisations("",1), $empr_cart_form);
		$empr_cart_form = str_replace('!!formulaire_action!!', "./circ.php?categ=caddie&sub=gestion&quoi=panier&action=valid_new_cart&item=$item", $empr_cart_form);
		$empr_cart_form = str_replace('!!formulaire_annuler!!', "./circ.php?categ=caddie&sub=gestion&quoi=panier&action=&item=$item", $empr_cart_form);
		$classementGen = new classementGen('empr_caddie', '0');
		$empr_cart_form = str_replace("!!object_type!!",$classementGen->object_type,$empr_cart_form);
		$empr_cart_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$empr_cart_form);
		print $empr_cart_form ;
		break;
	case 'edit_cart':
		$myCart= new empr_caddie($idemprcaddie);
		$empr_cart_edit_form = str_replace('!!formulaire_action!!', "./circ.php?categ=caddie&sub=gestion&quoi=panier&action=save_cart&item=$item&idemprcaddie=$idemprcaddie", $empr_cart_edit_form);
		$empr_cart_edit_form = str_replace('!!formulaire_annuler!!', "./circ.php?categ=caddie&sub=gestion&quoi=panier&action=&item=$item", $empr_cart_edit_form);
		$empr_cart_edit_form = str_replace('!!name!!', htmlentities($myCart->name,ENT_QUOTES, $charset), $empr_cart_edit_form);
		$empr_cart_edit_form = str_replace('!!comment!!', htmlentities($myCart->comment,ENT_QUOTES, $charset), $empr_cart_edit_form);
		$empr_cart_edit_form = str_replace('!!autorisations_users!!', aff_form_autorisations($myCart->autorisations,0), $empr_cart_edit_form);
		//Liaisons pour le panier
		$info_liaisons="";
		$message_delete_warning = "";
		foreach ($myCart->liaisons as $type => $values){
			if(count($values)){
				$info_liaisons.="<br>";
				switch ($type){
					case "mailing":
						$info_liaisons.="<div class='row'>
                                           <label for='' class='etiquette'>".$msg["planificateur_task"]."</label>
                                       </div>
                                       <div class='row'>";
						if (SESSrights & ADMINISTRATION_AUTH) {
							$link="<a href='./admin.php?categ=planificateur&sub=manager&act=task&type_task_id=!!id_bis!!&planificateur_id=!!id!!'>!!name!!</a>";
						} else {
							$link="!!name!!";
						}
						break;
					default://On ne doit pas passer par là
						$info_liaisons="";
						break 2;//On sort aussi du foreach
				}
				foreach ($values as $infos){
					$info_liaisons.=str_replace(array("!!id!!","!!id_bis!!","!!name!!"),array($infos["id"],$infos["id_bis"],htmlentities($infos["lib"], ENT_QUOTES, $charset)), $link);
				}
				$info_liaisons.="</div>";
			}
		}
		if($info_liaisons){
			$liaison_tpl=str_replace("<!-- info_liaisons -->",$info_liaisons,$liaison_tpl);
			$empr_cart_edit_form = str_replace('<!-- liaisons -->', $liaison_tpl, $empr_cart_edit_form);
			$message_delete_warning = $msg["caddie_used_in_warning"];
			foreach ($myCart->liaisons as $type => $values){
				if(count($values)){
					switch ($type){
						case "mailing":
							$message_delete_warning .= "\\n- ".$msg["planificateur_task"];
							break;
						default://On ne doit pas passer par là
							$info_liaisons="";
							break 2;//On sort aussi du foreach
					}
				}
			}
			$message_delete_warning .= "\\n";
			$empr_cart_edit_form = str_replace("!!javascript_delete!!","javascript:alert('".$message_delete_warning."\\n".$msg["empr_caddie_used_cant_delete"]."')",$empr_cart_edit_form);
		} else {
			$empr_cart_edit_form = str_replace("!!javascript_delete!!","javascript:confirmation_delete(!!idemprcaddie!!,'!!name_suppr!!')",$empr_cart_edit_form);
			print confirmation_delete("./circ.php?categ=caddie&action=del_cart&idemprcaddie=");
		}
		$empr_cart_edit_form = str_replace('!!idemprcaddie!!', $idemprcaddie, $empr_cart_edit_form);
		$empr_cart_edit_form = str_replace('!!name_suppr!!', htmlentities(addslashes($myCart->name),ENT_QUOTES, $charset), $empr_cart_edit_form);
		$classementGen = new classementGen('empr_caddie', $idemprcaddie);
		$empr_cart_edit_form = str_replace("!!object_type!!",$classementGen->object_type,$empr_cart_edit_form);
		$empr_cart_edit_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$empr_cart_edit_form);
		print $empr_cart_edit_form ;
		break;
	case 'del_cart':
		$myCart= new empr_caddie($idemprcaddie);
		$myCart->delete();
		aff_paniers_empr($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
		break;
	case 'save_cart':
		$myCart= new empr_caddie($idemprcaddie);
		if (is_array($cart_autorisations)) $autorisations=implode(" ",$cart_autorisations);
				else $autorisations="1";
		$myCart->autorisations = $autorisations;
		$myCart->name = $cart_name;
		$myCart->comment = $cart_comment;
		$myCart->classementGen = $classementGen_empr_caddie;
		if($form_actif) $myCart->save_cart();
		aff_paniers_empr($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
		break;
	case 'del_item':
		$myCart= new empr_caddie($idemprcaddie);
		$myCart->del_item($item);
		print aff_empr_cart_titre ($myCart);
		print aff_empr_cart_nb_items ($myCart) ;
		aff_empr_cart_objects ($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=$idemprcaddie" );
		break;
	case 'valid_new_cart':
		$myCart = new empr_caddie(0);
		$myCart->name = $cart_name;
		$myCart->comment = $cart_comment;
		if (is_array($cart_autorisations)) $autorisations=implode(" ",$cart_autorisations);
				else $autorisations="";
		$myCart->autorisations = $autorisations;
		$myCart->classementGen = $classementGen_empr_caddie;
		if($form_actif) $myCart->create_cart();
		aff_paniers_empr($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
		break;
	default:
		if($idemprcaddie) {
			$myCart = new empr_caddie($idemprcaddie);
			print aff_empr_cart_titre ($myCart);
			print aff_empr_cart_nb_items ($myCart);
			aff_empr_cart_objects ($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=$idemprcaddie" );
			} else aff_paniers_empr($idemprcaddie, "./circ.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
	}
