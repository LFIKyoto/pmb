<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.16 2015-06-18 13:55:50 jpermanne Exp $

// définition du minimum nécessaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ("$base_path/includes/init.inc.php");  
require_once ("./edition_func.inc.php");  
require_once ("$class_path/caddie.class.php");

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new caddie($idcaddie);
if (!$myCart->idcaddie) die();
// création de la page
switch($dest) {
	case "TABLEAU":
		require_once ("$class_path/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("$class_path/writeexcel/class.writeexcel_worksheet.inc.php");

		$fname = tempnam("./temp", $fichier_temp_nom.".xls");
		if (substr(phpversion(), 0, 3) <= "5.2") {
			eval ("
				\$workbook = new writeexcel_workbookbig(\$fname);
				\$worksheet = &\$workbook->addworksheet();
			");
		} else {
			eval ("
				\$workbook = new writeexcel_workbookbig(\$fname);
				\$worksheet = \$workbook->addworksheet();
			");
		}

		$worksheet->write_string(0,0,$msg["caddie_numero"].$idcaddie);
		$worksheet->write_string(0,1,$myCart->type);
		$worksheet->write_string(0,2,$myCart->name);
		$worksheet->write_string(0,3,$myCart->comment);
		break;
	case "TABLEAUHTML":
		header("Content-Type: application/download\n");
		header("Content-Disposition: atachement; filename=\"tableau.xls\"");
		print "<html><head>" .
	 	'<meta http-equiv=Content-Type content="text/html; charset='.$charset.'" />'.
		"</head><body>";
		break;
	case "EXPORT_NOTI":
		$fname = "bibliographie.doc";		
		break;		
	default:
        header ("Content-Type: text/html; charset=$charset");
		print $std_header;
		break;
	}
	
$contents=afftab_cart_objects ($idcaddie, $elt_flag , $elt_no_flag, $notice_tpl ) ;

switch($dest) {
	case "TABLEAU":
		$workbook->close();
		header("Content-Type: application/x-msexcel; name=\""."Caddie_".$myCart->type."_".$idcaddie.".xls"."\"");
		header("Content-Disposition: inline; filename=\""."Caddie_".$myCart->type."_".$idcaddie.".xls"."\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		break;
	case "EXPORT_NOTI":		
		header('Content-Disposition: attachment; filename='.$fname);
		header('Content-type: application/msword'); 
		header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	    header("Pragma: public");
		echo $contents;					
	break;
	case "TABLEAUHTML":
	default:
		if ($etat_table) echo "\n</table>";
		break;
	}
	
pmb_mysql_close($dbh);
