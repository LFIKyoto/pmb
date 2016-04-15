<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_cart.inc.php,v 1.28.2.1 2015-11-04 10:09:06 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// ********************************************************************************
// affichage des paniers existants
function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1,$post_param_serialized="") {
	global $msg;
	global $PMBuserid;
	global $charset;
	global $myCart;
	global $sub,$quoi;
	global $action;
	global $baselink;
	
	if ($lien_edition) $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='$lien_origine&action=edit_cart&idemprcaddie=!!idemprcaddie!!';\" />";
		else $lien_edition_panier_cst = "";
	 if($sub!='gestion' && $sub!='action') {
		print "<form name='print_options' action='$lien_origine&action=$action_click&item=$item' method='post'>";
	}
	$liste = empr_caddie::get_cart_list($restriction_panier);
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	print "<hr />";
	if ($lien_creation) {
		print "<div class='row'>";
		if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart&item=$item'; this.form.submit();\" />";
		else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&item=$item'\" />";
		print "</div><br>";
	}
	if(sizeof($liste)) {
		print pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='./images/expand_all.gif' id='expandall' border='0'></a>
				<a href='javascript:collapseAll()'><img src='./images/collapse_all.gif' id='collapseall' border='0'></a>$titre</div>");
		print confirmation_delete("$lien_origine&action=del_cart&item=$item&idemprcaddie=");
		print "<script type='text/javascript'>
			function add_to_cart(form) {
        		var inputs = form.getElementsByTagName('input');
        		var count=0;
        		for(i=0;i<inputs.length;i++){
					if(inputs[i].type=='checkbox' && inputs[i].checked==true)
        				count ++;
				}
				if(count == 0){
					alert(\"$msg[no_emprcart_selected]\");
					return false;
				}
				return true;
   			}
   		</script>";
		if($sub=="gestion" && $quoi=="panier"){
			print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		}
		$parity=0;
		while (list($cle, $valeur) = each($liste)) {
			$rqt_autorisation=explode(" ",$valeur['autorisations']);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
				$classementRow = $valeur['empr_caddie_classement'];
				if(!trim($classementRow)){
					$classementRow=classementGen::getDefaultLibelle();
				}
				$link = "$lien_origine&action=$action_click&idemprcaddie=".$valeur['idemprcaddie']."&item=$item";
				
				if (($parity=1-$parity)) $pair_impair = "even"; else $pair_impair = "odd";
	
				$lien_edition_panier = str_replace('!!idemprcaddie!!', $valeur['idemprcaddie'], $lien_edition_panier_cst);
		        $aff_lien = $lien_edition_panier;
		        $myCart = new empr_caddie(0);
		        $myCart->nb_item=$valeur['nb_item'];
		        $myCart->nb_item_pointe=$valeur['nb_item_pointe'];
		        $myCart->type='EMPR';
		        $print_cart[$classementRow]["titre"]=stripslashes($classementRow);
		        
		        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
				if($item) {
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<tr class='$pair_impair' $tr_javascript ><td class='classement60'>");
		            if($action != "transfert" && $action != "del_cart" && $action!="save_cart") {
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<input type='checkbox' id='id_".$valeur['idemprcaddie']."' name='caddie[".$valeur['idemprcaddie']."]' value='".$valeur['idemprcaddie']."'>&nbsp;");
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='#' onClick='javascript:document.getElementById(\"id_".$valeur['idemprcaddie']."\").checked=true; document.forms[\"print_options\"].submit();' /><strong>".$valeur['name']."</strong>");
		            } else {		            
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='$link' /><strong>".$valeur['name']."</strong>");
		            }	
	                if ($valeur['comment']) $print_cart[$classementRow]["cart_list"].=  pmb_bidi("<br /><small>(".$valeur['comment'].")</small>");
	            	$print_cart[$classementRow]["cart_list"].=  pmb_bidi("</td>
	            		".aff_cart_nb_items_reduit($myCart)."
	            		<td class='classement20'>$aff_lien</td>
						</tr>");						
				} else {		        
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<tr class='$pair_impair' $tr_javascript >");
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<td class='classement60'>");
		            if($sub!='gestion' && $sub!='action'  && $action!="save_cart") {
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<input type='checkbox' id='id_".$valeur['idemprcaddie']."' name='caddie[".$valeur['idemprcaddie']."]' value='".$valeur['idemprcaddie']."'>&nbsp;");		            	
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='#' onClick='javascript:document.getElementById(\"id_".$valeur['idemprcaddie']."\").checked=true; document.forms[\"print_options\"].submit();' /><strong>".$valeur['name']."</strong>");
		            } else {
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='$link' /><strong>".$valeur['name']."</strong>");
		            }
		            if ($valeur['comment']){
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<br /><small>(".$valeur['comment'].")</small>");
		            }
		            $print_cart[$classementRow]["cart_list"].=pmb_bidi("</a></td>");
		            $print_cart[$classementRow]["cart_list"].=pmb_bidi(aff_cart_nb_items_reduit($myCart));
		            if($sub=="gestion" && $quoi=="panier"){
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement15'>".$aff_lien."&nbsp;".empr_caddie::show_actions($valeur['idemprcaddie'])."</td>");
		            	$classementGen = new classementGen('empr_caddie', $valeur['idemprcaddie']);
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement5'>".$classementGen->show_selector($baselink,$PMBuserid)."</td>");
		            }else{
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement20'>$aff_lien</td>");
		            }
					$print_cart[$classementRow]["cart_list"].=pmb_bidi("</tr>");
				}		
			}
		}
		//on trie
		ksort($print_cart);
		//on remplace les clés à cause des accents
		$print_cart=array_values($print_cart);
		foreach($print_cart as $key => $type) {
			print gen_plus($key,$type["titre"],"<table class='classementGen_tableau'>".$type["cart_list"]."</table>",1);
		}
		
	} else {
		print $msg[398];
	}
	
	 if($sub!='gestion' && $sub!='action'&& $action != "del_cart") {
		$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton' onclick=\"return add_to_cart(this.form);\"/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
	}	
	if ($lien_creation) {
		print "<div class='row'><hr />";
			if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart&item=$item'; this.form.submit();\" />";
			else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&item=$item'\" />";
		print "</div>"; 
	} else {
		print "<div class='row'><hr />
			$boutons_select
			</div>"; 		
	}
	if ($post_param_serialized != "") {
		print unserialize($post_param_serialized);
	}			
	 if($sub!='gestion')  print"</form>";
	

}

// ********************************************************************************
function aff_empr_cart_titre ($myCart) {
	global $msg;
	if ($myCart->comment) $aff_tit_panier = $myCart->name." - ".$myCart->comment;
		else $aff_tit_panier = $myCart->name;
	return "<div class='titre-panier'><h3><a href='./circ.php?categ=caddie&sub=gestion&quoi=panier&action=&idemprcaddie=".$myCart->idemprcaddie."'>$aff_tit_panier</a></h3></div>";
	}

// ********************************************************************************
function aff_empr_cart_nb_items ($myCart) {
	global $msg;
	return "<div id='cart_".$myCart->idemprcaddie."_nb_items' name='cart_".$myCart->idemprcaddie."_nb_items'>
			<div class='row'>
			<div class='colonne3'>
				$msg[caddie_contient]
				</div>
			<div class='colonne3' align='center'>
				$msg[caddie_contient_total]
				</div>
			<div class='colonne_suite' align='center'>
				$msg[caddie_contient_nb_pointe]
				</div>
			</div>
		<div class='row'>
			<div class='colonne3' align='right'>
				$msg[caddie_contient_total]
				</div>
			<div class='colonne3' align='center'>
				<b>$myCart->nb_item</b>
				</div>
			<div class='colonne_suite' align='center'>
				<b>$myCart->nb_item_pointe</b>
				</div>
			</div>
		</div>
		<br />";
	}

// ****************************** aff_empr_cart_objects
function aff_empr_cart_objects ($idemprcaddie=0, $url_base="./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=0", $no_del=false,$rec_history=0, $no_point=false ) {
	global $msg, $begin_result_liste;
	global $dbh;
	global $nbr_lignes, $page, $nb_per_page_search ;
	global $url_base_suppr_empr_cart ;
	
	$url_base_suppr_empr_cart = $url_base ;
	
	// nombre de références par pages
	if ($nb_per_page_search != "") 
		$nb_per_page = $nb_per_page_search ;
	else $nb_per_page = 10;
	
	// on récupére le nombre de lignes
	if(!$nbr_lignes) {
		$requete = "SELECT count(1) FROM empr_caddie_content where empr_caddie_id='".$idemprcaddie."' ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
	}
	
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;
	
	//Calcul des variables pour la suppression d'items
	$modulo = $nbr_lignes%$nb_per_page;
	if($modulo == 1){
		$page_suppr = (!$page ? 1 : $page-1);
	} else {
		$page_suppr = $page;
	}	
	$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);	
	
		
	if($nbr_lignes) {
		// on lance la vraie requête
		$myCart = new empr_caddie($idemprcaddie);
		$from = " empr_caddie_content left join empr on id_empr = object_id ";
		$order_by = " empr_nom, empr_prenom " ;
		$requete = "SELECT object_id, flag FROM $from where empr_caddie_id='".$idemprcaddie."' order by $order_by"; 
		$requete.= " LIMIT $debut,$nb_per_page ";
			
		
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		// l'affichage du résultat est fait après le else
	} else {
		print $msg[399];
		return;
	}
	
	$liste=array();
	$result = @pmb_mysql_query($requete, $dbh);
	
	if(pmb_mysql_num_rows($result)) {
		while ($temp = pmb_mysql_fetch_object($result)) 
			$liste[] = array('object_id' => $temp->object_id, 'flag' => $temp->flag ) ;  
	}
	
	if(!sizeof($liste) || !is_array($liste)) {
		print $msg[399];
		return;
	} else {
		print "
		<script>
			var ajax_pointage=new http_request();
			var num_caddie=0;
			var num_item=0;
			var action='';
			function add_pointage_item(idcaddie,id_item) {
				num_caddie=idcaddie;
				num_item=id_item;
				action='add_item';
				var url = './ajax.php?module=catalog&categ=pointage_add&sub=pointage&moyen=manu&action=add_item&typecaddie=empr&idcaddie='+idcaddie+'&id_item='+id_item;
		 		ajax_pointage.request(url,0,'',1,pointage_callback,0,0);
			}
		
			function del_pointage_item(idcaddie,id_item) {
				num_caddie=idcaddie;
				num_item=id_item;
				action='del_item';
				var url = './ajax.php?module=catalog&categ=pointage_del&sub=pointage&moyen=manu&action=del_item&typecaddie=empr&idcaddie='+idcaddie+'&id_item='+id_item;
				ajax_pointage.request(url,0,'',1,pointage_callback,0,0);
			}
			function pointage_callback(response) {
				data = eval('('+response+')');
				switch (action) {
					case 'add_item':
						if (data.res_pointage == 1) {
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/depointer.png';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_depointer']."';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','del_pointage_item('+num_caddie+','+num_item+')');
						} else {
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/pointer.png';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_pointer']."';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','add_pointage_item('+num_caddie+','+num_item+')');
						}
						break;
					case 'del_item':
						if (data.res_pointage == 1) {
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/pointer.png';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_pointer']."';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','add_pointage_item('+num_caddie+','+num_item+')');
						} else {
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/depointer.png';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_depointer']."';
							document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','del_pointage_item('+num_caddie+','+num_item+')');
						}
						break;
				}
				var div = document.createElement('div');
				div.setAttribute('id','cart_'+data.idcaddie+'_nb_items');
				div.innerHTML = data.aff_cart_nb_items;
				document.getElementById('cart_'+data.idcaddie+'_nb_items').parentNode.replaceChild(div,document.getElementById('cart_'+data.idcaddie+'_nb_items'));
			}
		</script>";
		print $begin_result_liste;
		print empr_caddie::show_actions($idemprcaddie);
		while(list($cle, $object) = each($liste)) {
			// affichage de la liste des emprunteurs 
			$requete = "SELECT * FROM empr WHERE id_empr=$object[object_id] LIMIT 1";
			$fetch = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($fetch)) {
				$empr = pmb_mysql_fetch_object($fetch);
				// emprunteur
				$link = './circ.php?categ=pret&form_cb='.rawurlencode($empr->empr_cb);
				if (!$no_point) {
					if ($object[flag]) $marque_flag ="<img src='images/depointer.png' id='caddie_".$idemprcaddie."_item_".$empr->id_empr."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$idemprcaddie.",".$empr->id_empr.");' style='cursor: pointer'/>" ;
					else $marque_flag ="<img src='images/pointer.png' id='caddie_".$idemprcaddie."_item_".$empr->id_empr."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$idemprcaddie.",".$empr->id_empr.");' style='cursor: pointer'/>" ;
				} else {
					if ($object[flag]) $marque_flag ="<img src='images/tick.gif'/>" ;
					else $marque_flag ="" ;
				}
				if (!$no_del) $lien_suppr_cart = "<a href='$url_base&action=del_item&item=$empr->id_empr&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='./images/basket_empty_20x20.gif' alt='basket' title=\"".$msg[caddie_icone_suppr_elt]."\" /></a> $marque_flag";
					else $lien_suppr_cart = $marque_flag ;
				$empr = new emprunteur($empr->id_empr, "", FALSE, 3);
				$empr->fiche_consultation = str_replace('!!image_suppr_caddie_empr!!'    , $lien_suppr_cart    , $empr->fiche_consultation);
				$empr->fiche_consultation = str_replace('!!lien_vers_empr!!'    , $link    , $empr->fiche_consultation);
				print $empr->fiche_consultation; 
			}
		} // fin de liste
	
	}
	print "<br />".$nav_bar ;
	return;
}

//*********************************************************************************
function aff_empr_choix_quoi($action="", $action_cancel="", $titre_form="", $bouton_valider="",$onclick="") {
	
	global $empr_cart_choix_quoi;
	global $elt_flag,$elt_no_flag;
	
	$empr_cart_choix_quoi = str_replace('!!action!!', $action, $empr_cart_choix_quoi);
	$empr_cart_choix_quoi = str_replace('!!action_cancel!!', $action_cancel, $empr_cart_choix_quoi);
	$empr_cart_choix_quoi = str_replace('!!titre_form!!', $titre_form, $empr_cart_choix_quoi);
	$empr_cart_choix_quoi = str_replace('!!bouton_valider!!', $bouton_valider, $empr_cart_choix_quoi);
	if ($onclick!="") $empr_cart_choix_quoi = str_replace('!!onclick_valider!!','onClick="'.$onclick.'"',$empr_cart_choix_quoi); 
		else $empr_cart_choix_quoi = str_replace('!!onclick_valider!!','',$empr_cart_choix_quoi);
	if ($elt_flag) {
		$empr_cart_choix_quoi = str_replace('!!elt_flag_checked!!', 'checked=\'checked\'', $empr_cart_choix_quoi);
	} else {
		$empr_cart_choix_quoi = str_replace('!!elt_flag_checked!!', '', $empr_cart_choix_quoi);
	}
	if ($elt_no_flag) {
		$empr_cart_choix_quoi = str_replace('!!elt_no_flag_checked!!', 'checked=\'checked\'', $empr_cart_choix_quoi);
	} else {
		$empr_cart_choix_quoi = str_replace('!!elt_no_flag_checked!!', '', $empr_cart_choix_quoi);
	}
	return $empr_cart_choix_quoi;
	}

// ********************************************************************************
function verif_droit_proc_empr_caddie($id) {
	global $msg;
	global $PMBuserid;
	global $dbh;
	
	if ($id) {
		$requete = "SELECT autorisations FROM empr_caddie_procs WHERE idproc='$id' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$rqt_autorisation=explode(" ",$temp->autorisations);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return 1 ;
				else return 0 ;
			} else return 0;
		} else return 0 ;
	}

// ********************************************************************************
function verif_droit_empr_caddie($id) {
	global $msg;
	global $PMBuserid;
	global $dbh ;
	
	if ($id) {
		$requete = "SELECT autorisations FROM empr_caddie WHERE idemprcaddie='$id' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$rqt_autorisation=explode(" ",$temp->autorisations);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return $id ;
				else return 0 ;
			} else return 0;
		} else return 0 ;
	}
