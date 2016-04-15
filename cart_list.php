<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_list.php,v 1.7 2015-06-18 13:11:10 jpermanne Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;


$base_auth = "CATALOGAGE_AUTH";
require_once("includes/init.inc.php");
require_once("$class_path/caddie.class.php");

header("Content-Type: text/html; charset=$charset");

//si id_notice est présent, il s'agit de l'accès rapide aux paniers (div apparaissant sur mouseOver icone panier)
//sinon, il s'agit de la liste des paniers pour le drag and drop

if ($id_notice) {
	$list_noti=caddie::get_cart_list("NOTI",1);
	$list_expl = array();
	$list_bull = array();
} else {
	$list_noti=caddie::get_cart_list("NOTI",0);
	$list_expl=caddie::get_cart_list("EXPL",0);
	$list_bull=caddie::get_cart_list("BULL",0);
}

$is_cart=0;

if ($id_notice) {
	$link="<a href='#' id='close_cart_div' ><img border='0' align='middle' src='images/close.gif'/></a>";
} else {
	$link="<a href='#' id='close_cart_pannel' ><img border='0' align='middle' src='images/close.gif'/></a>";
}
print "<div><table width='100%'><tbody><tr><td align='left' width='90%'></td><td align='right'>$link</td></tr></tbody></table></div>";

if (count($list_noti)) {
	print "<h3>$msg[396]</h3><br />";
	for ($i=0; $i<count($list_noti); $i++) {
		if ($id_notice) {
			$pannel_cart_link = "javascript:notice_div_caddie(".$id_notice.",".$list_noti[$i]["idcaddie"].")";
		} else {
			$pannel_cart_link = "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=".$list_noti[$i]["idcaddie"]."&item=0";
		}
		print "<div id=\"NOTI_".$list_noti[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='images/basket_20x20.gif'/>&nbsp;<a href='".$pannel_cart_link."'>".htmlentities($list_noti[$i]["name"],ENT_QUOTES,$charset)."<span id=\"NOTI_nbitem_".$list_noti[$i]["idcaddie"]."\"> (".$list_noti[$i]["nb_item"].")</span></a></div>";
	}
	$is_cart++;
}
print "<br />";
if (count($list_expl)) {
	print "<h3>$msg[expl_carts]</h3><br />";
	for ($i=0; $i<count($list_expl); $i++) {
		if ($id_notice) {
			$pannel_cart_link = "javascript:notice_div_caddie(".$id_notice.",".$list_noti[$i]["idcaddie"].")";
		} else {
			$pannel_cart_link = "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=EXPL&idcaddie=".$list_expl[$i]["idcaddie"]."&item=0";
		}
		print "<div id=\"EXPL_".$list_expl[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='images/basket_20x20.gif'/>&nbsp;<a href='".$pannel_cart_link."'>".htmlentities($list_expl[$i]["name"],ENT_QUOTES,$charset)."<span id='EXPL_nbitem_".$list_expl[$i]["idcaddie"]."'> (".$list_expl[$i]["nb_item"].")</span></a></div>";
	}
	$is_cart++;
}
print "<br />";
if (count($list_bull)) {
	print "<h3>$msg[bull_carts]</h3><br />";
	for ($i=0; $i<count($list_bull); $i++) {
		if ($id_notice) {
			$pannel_cart_link = "javascript:notice_div_caddie(".$id_notice.",".$list_noti[$i]["idcaddie"].")";
		} else {
			$pannel_cart_link = "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=BULL&idcaddie=".$list_bull[$i]["idcaddie"]."&item=0";
		}
		print "<div id=\"BULL_".$list_bull[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='images/basket_20x20.gif'/>&nbsp;<a href='".$pannel_cart_link."'>".htmlentities($list_bull[$i]["name"],ENT_QUOTES,$charset)."<span id='BULL_nbitem_".$list_bull[$i]["idcaddie"]."'> (".$list_bull[$i]["nb_item"].")</span></a></div>";
	}
	$is_cart++;
}

if (!$is_cart){
	if ($id_notice) {
		print "<h3>".$msg["caddie_fast_access_no_selected"]."</h3>";
	} else {
		print "<h3>$msg[398]</h3>";
	}
}

/*
					'idcaddie' => $temp->idcaddie,
					'name' => $temp->name,
					'type' => $temp->type,
					'comment' => $temp->comment,
					'autorisations' => $temp->autorisations,
					'nb_item' => $nb_item,
					'nb_item_pointe' => $nb_item_pointe,
					'nb_item_base' => $nb_item_base,
					'nb_item_base_pointe' => $nb_item_base_pointe,
					'nb_item_blob' => $nb_item_blob,
					'nb_item_blob_pointe' => $nb_item_blob_pointe
				*/
?>
