<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tri.inc.php,v 1.4 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($quoifaire){	
	case 'up_order' :
		update_order();	
	break;	
	case 'up_order_avis' :
		update_order_avis();	
	break;
}

function update_order_avis(){	
	global $dbh, $tablo_avis;

	$liste_avis = explode(",",$tablo_avis);
	for($i=0;$i<count($liste_avis);$i++){
		$rqt = "update avis set avis_rank='".$i."' where id_avis='".$liste_avis[$i]."' ";
		pmb_mysql_query($rqt,$dbh);
	}
}

function update_order(){
	
	global $dbh,$idpere, $type_rel, $tablo_fille;
	
	$liste_fille = explode(",",$tablo_fille);
	for($i=0;$i<count($liste_fille);$i++){
		$req = "update notices_relations set rank='".$i."' where num_notice='".$liste_fille[$i]."' and linked_notice='".$idpere."' and relation_type='".$type_rel."'";
		pmb_mysql_query($req,$dbh);
	}

}
?>