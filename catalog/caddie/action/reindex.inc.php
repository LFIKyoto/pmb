<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex.inc.php,v 1.3.4.1 2015-12-02 11:13:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("./classes/notice_tpl_gen.class.php");
require_once("./classes/progress_bar.class.php");
if($idcaddie) {
	$myCart= new caddie($idcaddie);
	print pmb_bidi(aff_cart_titre ($myCart));
	switch ($action) {
		case 'choix_quoi':
			print pmb_bidi(aff_cart_nb_items ($myCart)) ;
			print aff_choix_quoi ("./catalog.php?categ=caddie&sub=action&quelle=reindex&action=suite&idcaddie=$idcaddie", "./catalog.php?categ=caddie&sub=action&quelle=reindex&action=&idcaddie=0", $msg["caddie_choix_reindex"], $msg["caddie_bouton_reindex"],"");
			break;
		case 'suite':
			@set_time_limit(0);
			$nb_elements_flag=$nb_elements_no_flag=0;
			$liste_0=$liste_1= array();
			if ($elt_flag) {
				$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
				$nb_elements_flag=count($liste_0);
			}	
			if ($elt_no_flag) {
				$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
				$nb_elements_no_flag=count($liste_1);
			}	
			$liste= array_merge($liste_0,$liste_1);
			$nb_elements_total=count($liste);
			
			
			if($nb_elements_total){
				$pb=new progress_bar($msg[caddie_situation_reindex_encours],$nb_elements_total,5);
				if ($myCart->type=='NOTI'){
					while(list($cle, $object) = each($liste)) {
						// Mise à jour de tous les index de la notice
				    	notice::majNoticesTotal($object);
				    	$pb->progress();
					}
				}elseif($myCart->type=='BULL'){
					while(list($cle, $object) = each($liste)) {
						$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$object."'";
						$res=pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($res)){
							$element=pmb_mysql_fetch_object($res);
							if(trim($element->bulletin_titre)){
								$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$object."'";
								pmb_mysql_query($requete);
							}
							if($element->num_notice){
								notice::majNoticesTotal($element->num_notice);
							}
	
						}
						$pb->progress();
					}
				}elseif($myCart->type=='EXPL'){
					while(list($cle, $object) = each($liste)) {
						$requete="SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id='".$object."' ";
						$res=pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($res)){
							$row=pmb_mysql_fetch_object($res);
							if($row->expl_notice){
								notice::majNoticesTotal($row->expl_notice);
							}else{
								$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$row->expl_bulletin."'";
								$res2=pmb_mysql_query($requete);
								if(pmb_mysql_num_rows($res2)){
									$element=pmb_mysql_fetch_object($res2);
									if(trim($element->bulletin_titre)){
										$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$row->expl_bulletin."'";
										pmb_mysql_query($requete);
									}
									if($element->num_notice){
										notice::majNoticesTotal($element->num_notice);
									}
								}
							}
						}
						$pb->progress();
					}
				}
				$pb->hide();
			}
			
			print "<br /><h3>$msg[caddie_situation_reindex]</h3>";
			print sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag)."<br />";
			print sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag)."<br />";
			print "<b>".sprintf($msg["caddie_action_total_processed"],$nb_elements_total)."</b><br /><br />";
			print aff_cart_nb_items ($myCart) ;
			echo "<input type='button' class='bouton' value='".$msg["caddie_menu_action_suppr_panier"]."' onclick='document.location=&quot;./catalog.php?categ=caddie&amp;sub=action&amp;quelle=supprpanier&amp;action=choix_quoi&amp;object_type=NOTI&amp;idcaddie=".$idcaddie."&amp;item=0&amp;elt_flag=".$elt_flag."&amp;elt_no_flag=".$elt_no_flag."&quot;' />";
		default:
			break;
		}

} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=action&quelle=reindex", "choix_quoi", $msg["caddie_action_reindex"], "", 0, 0, 0);
