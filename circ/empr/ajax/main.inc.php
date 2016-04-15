<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!isset($empr_grille_categ)) $empr_grille_categ='0' ;
if (!isset($empr_grille_location)) $empr_grille_location='0' ;
$empr_grille_categ+=0;
$empr_grille_location+=0;

//champs persos
$nb_cp=0;
$q_cp = "select name from empr_custom order by ordre ";
$r_cp = pmb_mysql_query($q_cp, $dbh);
if (!pmb_mysql_errno($dbh)) {
	$nb_cp = pmb_mysql_num_rows($r_cp);
}

$empr_grille_default="
<formpage relative='yes'>
  <etirable id='g0_r0' visible='yes' parent='g0' />
  <etirable id='g0_r1' visible='yes' parent='g0' />
  <etirable id='g0_r2' visible='yes' parent='g0' />
  <etirable id='g0_r3' visible='yes' parent='g0' />
  <etirable id='g1_r0' visible='yes' parent='g1' />
  <etirable id='g2_r0' visible='yes' parent='g2' />
  <etirable id='g3_r0' visible='yes' parent='g3' />
  <etirable id='g4_r0' visible='yes' parent='g4' />
  <etirable id='g4_r1' visible='yes' parent='g4' />
  <etirable id='g5_r0' visible='yes' parent='g5' />";
if ($nb_cp) {
  	$empr_grille_default.= "	
  <etirable id='g6_r0' visible='yes' parent='g6' />";
}
$empr_grille_default.= "  
  <etirable id='g7_r0' visible='yes' parent='g7' />
  <movable id='g0_r0_f0' visible='yes' parent='g0_r0' width='33%'/>
  <movable id='g0_r0_f1' visible='yes' parent='g0_r0' width='33%'/>
  <movable id='g0_r0_f2' visible='yes' parent='g0_r0'/>
  <movable id='g0_r1_f0' visible='yes' parent='g0_r1' width='50%'/>
  <movable id='g0_r1_f1' visible='yes' parent='g0_r1' width='10%'/>
  <movable id='g0_r1_f2' visible='yes' parent='g0_r1'/>
  <movable id='g0_r2_f0' visible='yes' parent='g0_r2' width='50%'/>
  <movable id='g0_r2_f1' visible='yes' parent='g0_r2'/>
  <movable id='g0_r3_f0' visible='yes' parent='g0_r3' width='25%'/>
  <movable id='g0_r3_f1' visible='yes' parent='g0_r3' width='25%'/>
  <movable id='g0_r3_f2' visible='yes' parent='g0_r3'/>
  <movable id='g1_r0_f0' visible='yes' parent='g1_r0' width='25%'/>
  <movable id='g1_r0_f1' visible='yes' parent='g1_r0' width='25%'/>
  <movable id='g1_r0_f2' visible='yes' parent='g1_r0'/>
  <movable id='g2_r0_f0' visible='yes' parent='g2_r0' width='25%'/>
  <movable id='g2_r0_f1' visible='yes' parent='g2_r0' width='25%'/>
  <movable id='g2_r0_f2' visible='yes' parent='g2_r0'/>
  <movable id='g2_r1_f0' visible='yes' parent='g2_r1'/>
  <movable id='g3_r0_f0' visible='yes' parent='g3_r0' width='25%'/>
  <movable id='g3_r0_f1' visible='yes' parent='g3_r0' width='25%'/>
  <movable id='g3_r0_f2' visible='yes' parent='g3_r0'/>
  <movable id='g4_r0_f0' visible='yes' parent='g4_r0' width='25%'/>
  <movable id='g4_r0_f1' visible='yes' parent='g4_r0' width='25%'/>
  <movable id='g4_r0_f2' visible='yes' parent='g4_r0'/>
  <movable id='g4_r1_f0' visible='yes' parent='g4_r1'/>
  <movable id='g5_r0_f0' visible='yes' parent='g5_r0'/>";
if ($nb_cp) {
	for($i=0;$i<$nb_cp;$i++) {
		$empr_grille_default.= "<movable id='g6_r0_f".$i."' visible='yes' parent='g6_r0'/>";
	}
}
$empr_grille_default.= "  
</formpage>";

switch ($sub) {
	
	case 'get_default_empr_grille' :
		ajax_http_send_response($empr_grille_default,"text/xml");
		break;
		
	case 'get_empr_grille' :
		//format pour la categorie et la localisation demandee ? 
		$q = "select empr_grille_format from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='".$empr_grille_location."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_error($dbh) && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour la categorie demandee et toutes les localisations ?
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='0' ";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_error($dbh) && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour la localisation demandee et toutes les categories ?
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='0' and empr_grille_location='".$empr_grille_location."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_error($dbh) && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour toutes les localisations et toutes les categories
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='0' and empr_grille_location='0' ";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_error($dbh) && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format par defaut
		ajax_http_send_response($empr_grille_default,"text/xml");
		break;
	
	case 'set_empr_grille' :
		$q = "delete from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='".$empr_grille_location."' ";
		pmb_mysql_query($q, $dbh);
		$q1 = "insert into empr_grilles set empr_grille_categ='".$empr_grille_categ."', empr_grille_location='".$empr_grille_location."', empr_grille_format='".$empr_grille_format."' ";
		pmb_mysql_query($q1, $dbh);
		break;
		
	default :
		break;
}		
