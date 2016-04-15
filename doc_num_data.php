<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Yves PRATTER                                                   |
// +-------------------------------------------------+
// $Id: doc_num_data.php,v 1.15 2015-05-06 13:28:43 dgoron Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
//$base_nocheck  = 1;
$base_nobody   = 1;
$base_nosession   = 1;


require_once ("$base_path/includes/init.inc.php");  
require_once ($class_path."/upload_folder.class.php"); 

//gestion des droits
require_once($class_path."/acces.class.php");

$resultat = pmb_mysql_query("SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, length(explnum_data) as taille,explnum_path, concat(repertoire_path,explnum_path,explnum_nomfichier) as path, repertoire_id, explnum_nomfichier, explnum_extfichier FROM explnum left join upload_repertoire on repertoire_id=explnum_repertoire WHERE explnum_id = '$explnum_id' ", $dbh);
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
	} 
	
$ligne = pmb_mysql_fetch_object($resultat);

$id_for_rigths = $ligne->explnum_notice;
if($ligne->explnum_bulletin != 0){
	//si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
	$res = pmb_mysql_query($req);
	if(pmb_mysql_num_rows($res)){
		$row = pmb_mysql_fetch_object($res);
		$id_for_rigths = $row->num_notice;
		if(!$id_for_rigths){
			$id_for_rigths = $row->bulletin_notice;
		}
	}
}

//droits d'acces utilisateur/notice
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$rights = $dom_1->getRights($PMBuserid,$id_for_rigths);
}

if( $rights & 4 || (is_null($dom_1))){
	if (($ligne->explnum_data)||($ligne->explnum_path)) {
		if ($ligne->explnum_path) {
			$up = new upload_folder($ligne->repertoire_id);
			$path = str_replace("//","/",$ligne->path);
			$path=$up->encoder_chaine($path);
			$fo = fopen($path,'rb');
			$ligne->explnum_data=fread($fo,filesize($path));
			$ligne->taille=filesize($path);
			fclose($fo);
		}
		
		$nomfichier="";
		if ($ligne->explnum_nomfichier) {
			$nomfichier=$ligne->explnum_nomfichier;
		}elseif($ligne->explnum_extfichier){
			if($ligne->explnum_nom){
				$nomfichier=$ligne->explnum_nom;
				if(!preg_match("/\.".$ligne->explnum_extfichier."$/",$nomfichier)){
					$nomfichier.=".".$ligne->explnum_extfichier;
				}
			}else{
				$nomfichier="pmb".$ligne->explnum_id.".".$ligne->explnum_extfichier;
			}
		}
		if ($force_download == 1) {
			if($nomfichier) header("Content-disposition: attachment; filename=$nomfichier");
			header("Content-Transfer-Encoding: application/octet-stream");
			header("Pragma: no-cache");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
			header("Expires: 0");
		} else {
			if ($nomfichier) header("Content-Disposition: inline; filename=".$nomfichier);
		}
		
		header("Content-Type: ".$ligne->explnum_mimetype);
		header("Content-Length: ".$ligne->taille);
		print $ligne->explnum_data;
		exit ;
	} else print "ERROR".pmb_mysql_error() ;
} else {
	print $msg["forbidden_docnum"];
}