<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.class.php,v 1.18.4.1 2015-12-02 12:52:32 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'

if ( ! defined( 'ETAGERE_CLASS' ) ) {
  define( 'ETAGERE_CLASS', 1 );
  
require_once($class_path."/sort.class.php");


class etagere {
// propriétés
var $idetagere ;
var $name = ''			;	// nom de référence
var $comment = ""		;	// description du contenu du panier
var $validite = 1		;	// validite de l'étagère permanente ?
var $validite_date_deb = ''	;	// 	si non permanente date de début
var $validite_date_fin = ''	;	// 	                  date de fin
var $validite_date_deb_f = ''	;	// 	si non permanente date de début formatée
var $validite_date_fin_f = ''	;	// 	                  date de fin formatée
var $visible_accueil = 1	;	// visible en page d'accueil ?
var $autorisations = ""		;	// autorisations accordées sur ce panier
var $classementGen = ""		;	// classement

// constructeur
function etagere($etagere_id=0) {
	if($etagere_id) {
		// on cherche à atteindre une etagere existant
		$this->idetagere = $etagere_id;
		$this->getData();
	} else {
		// l'etagere n'existe pas
		$this->idetagere = 0;
		$this->getData();
	}
}

// récupération infos etagere
function getData() {
	global $dbh;
	global $msg ;
	if(!$this->idetagere) {
		// pas d'identifiant.
		$this->name	= '';
		$this->comment	= '';
		$this->autorisations	= "";
		$this->validite = "";
		$this->validite_date_deb = "";
		$this->validite_date_fin = "";
		$this->validite_date_deb_f = "";
		$this->validite_date_fin_f = "";
		$this->visible_accueil = "";
		$this->id_tri = 0;
		$this->thumbnail_url = '';
		$this->classementGen = '';
	} else {
		$requete = "SELECT idetagere, name, comment, validite, ";
		$requete .= "validite_date_deb, date_format(validite_date_deb, '".$msg["format_date"]."') as validite_date_deb_f,  ";
		$requete .= "validite_date_fin, date_format(validite_date_fin, '".$msg["format_date"]."') as validite_date_fin_f,  ";
		$requete .= "visible_accueil, autorisations, id_tri, thumbnail_url, etagere_classement FROM etagere WHERE idetagere='$this->idetagere' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			pmb_mysql_free_result($result);
			$this->idetagere = $temp->idetagere;
			$this->name = $temp->name;
			$this->comment = $temp->comment;
			$this->validite = $temp->validite;
			$this->validite_date_deb = $temp->validite_date_deb;
			$this->validite_date_deb_f = $temp->validite_date_deb_f;
			$this->validite_date_fin = $temp->validite_date_fin;
			$this->validite_date_fin_f = $temp->validite_date_fin_f;
			$this->visible_accueil = $temp->visible_accueil;
			$this->autorisations = $temp->autorisations;
			$this->id_tri = $temp->id_tri;
			$this->thumbnail_url = $temp->thumbnail_url;
			$this->classementGen = $temp->etagere_classsement;
		} else {
			// pas de caddie avec cet id
			$this->idetagere = 0;
			$this->name = "";
			$this->comment = "";
			$this->validite = "";
			$this->validite_date_deb = "";
			$this->validite_date_fin = "";
			$this->validite_date_deb_f = "";
			$this->validite_date_fin_f = "";
			$this->visible_accueil = "";
			$this->autorisations = "";
			$this->id_tri = '';
			$this->thumbnail_url = '';
			$this->classementGen = '';
		}
	}
}

// liste des étagères disponibles
static function get_etagere_list() {
	global $dbh;
	global $msg ;
	$etagere_list=array();
	$requete = "SELECT idetagere, name, comment, validite, ";
	$requete .= "validite_date_deb, date_format(validite_date_deb, '".$msg["format_date"]."') as validite_date_deb_f,  ";
	$requete .= "validite_date_fin, date_format(validite_date_fin, '".$msg["format_date"]."') as validite_date_fin_f,  ";
	$requete .= "visible_accueil, autorisations, etagere_classement FROM etagere order by name ";
	$result = @pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($result)) {
		while ($temp = pmb_mysql_fetch_object($result)) {
				$sql = "SELECT COUNT(*) FROM etagere_caddie WHERE etagere_id = ".$temp->idetagere;
				$res = pmb_mysql_query($sql, $dbh);
				$nbr_paniers = pmb_mysql_result($res, 0, 0);
								
				$etagere_list[] = array( 
					'idetagere' => $temp->idetagere,
					'name' => $temp->name,
					'type' => $temp->type,
					'comment' => $temp->comment,
					'validite' => $temp->validite,
					'validite_date_deb' => $temp->validite_date_deb,
					'validite_date_fin' => $temp->validite_date_fin,
					'validite_date_deb_f' => $temp->validite_date_deb_f,
					'validite_date_fin_f' => $temp->validite_date_fin_f,
					'visible_accueil' => $temp->visible_accueil,
					'autorisations' => $temp->autorisations,
					'etagere_classement' => $temp->etagere_classement,
					'nb_paniers' => $nbr_paniers
					);
			}
		} 
	return $etagere_list;
}

// création d'une etagere vide
function create_etagere() {
	global $dbh;
	$requete = "insert into etagere set name='".$this->name."', comment='".$this->comment."', validite='".$his->validite."', validite_date_deb='".$this->validite_date_deb."', validite_date_fin='".$this->validite_date_fin."', visible_accueil='".$this->visible_accueil."', autorisations='".$this->autorisations."'";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->idetagere = pmb_mysql_insert_id($dbh);
}

// ajout d'un item panier
function add_panier($item=0) {
	global $dbh;
	if (!$item) return 0 ;
	$requete_compte = "select count(1) from etagere_caddie where etagere_id='".$this->idetagere."' and caddie_id='".$item."' ";
	$result_compte = @pmb_mysql_query($requete_compte, $dbh);
	$deja_item=pmb_mysql_result($result_compte, 0, 0);
	if (!$deja_item) {
		$requete = "insert into etagere_caddie set etagere_id='".$this->idetagere."', caddie_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		} else return 0;
	return 1 ;
	}

// suppression d'un item panier
function del_item($item=0) {
	global $dbh;
	$requete = "delete FROM etagere_caddie where etagere_id='".$this->idcaddie."' and caddie_id='".$item."' ";
	$result = @pmb_mysql_query($requete, $dbh);
}

// suppression d'une etagere
function delete() {

	global $dbh;
	$requete = "delete FROM etagere_caddie where etagere_id='".$this->idetagere."' ";
	$result = @pmb_mysql_query($requete, $dbh);
	$this->delete_vignette();
	$requete = "delete FROM etagere where idetagere='".$this->idetagere."' ";
	$result = @pmb_mysql_query($requete, $dbh);
		
}

function delete_vignette() {
	
	global $dbh,$pmb_notice_img_folder_id;
	
	//Suppression de la vignette d'etagere
	if($pmb_notice_img_folder_id){
		$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$rep=pmb_mysql_fetch_object($res);
			$img=$rep->repertoire_path."img_etag_".$this->idetagere;
			@unlink($img);
		}
	}
	
}

function create_vignette() {

	global $dbh,$pmb_notice_img_folder_id,$pmb_notice_img_pics_max_size,$opac_url_base;
	
	$thumbnail_url=$this->thumbnail_url;
	
	// vignette de l'etagere
	if($pmb_notice_img_folder_id && $_FILES['f_img_load']['name'] ){
		$poids_fichier_max=1024*1024;//Limite la taille de l'image à 1 Mo
	
		$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$rep=pmb_mysql_fetch_object($res);
			$filename_output=$rep->repertoire_path."img_etag_".$this->idetagere;
		}
		if (($fp=fopen($_FILES['f_img_load']['tmp_name'], "rb")) && $filename_output) {
			$image="";
			$size=0;
			$flag=true;
			while (!feof($fp)) {
				$image.=fread($fp,4096);
				$size=strlen($image);
				if ($size>$poids_fichier_max) {
					$flag=false;
					break;
				}
			}
			if ($flag) {
				if ($img=imagecreatefromstring($image)) {
					if(!($pmb_notice_img_pics_max_size*1)) $pmb_notice_img_pics_max_size=100;
					$redim=false;
					if (imagesx($img) >= imagesy($img)) {
						if(imagesx($img) <= $pmb_notice_img_pics_max_size){
							$largeur=imagesx($img);
							$hauteur=imagesy($img);
						}else{
							$redim=true;
							$largeur=$pmb_notice_img_pics_max_size;
							$hauteur = ($largeur*imagesy($img))/imagesx($img);
						}
					} else {
						if(imagesy($img) <= $pmb_notice_img_pics_max_size){
							$hauteur=imagesy($img);
							$largeur=imagesx($img);
						}else{
							$redim=true;
							$hauteur=$pmb_notice_img_pics_max_size;
							$largeur = ($hauteur*imagesx($img))/imagesy($img);
						}
					}
					if($redim){
						$dest = imagecreatetruecolor($largeur,$hauteur);
						imagecopyresampled($dest, $img, 0, 0, 0, 0, $largeur, $hauteur,imagesx($img),imagesy($img));
						imagepng($dest,$filename_output);
						imagedestroy($dest);
					}else{
						imagepng($img,$filename_output);
					}
					imagedestroy($img);
					$thumbnail_url=$opac_url_base."getimage.php?noticecode=&vigurl=&etagere_id=".$this->idetagere ;
	
				}
			}
		}
	}
	
	return $thumbnail_url;
}

// sauvegarde de l'etagere
function save_etagere() {
	
	global $dbh;

	$this->thumbnail_url = $this->create_vignette();
	if(!$this->thumbnail_url) {
		$this->delete_vignette();
	}
	$requete = "update etagere set name='".$this->name."', comment='".$this->comment."', validite='".$this->validite."', validite_date_deb='".$this->validite_date_deb."', validite_date_fin='".$this->validite_date_fin."', visible_accueil='".$this->visible_accueil."', autorisations='".$this->autorisations."',id_tri='".$this->tri."',thumbnail_url='".$this->thumbnail_url."',etagere_classement='".$this->classementGen."' where idetagere='".$this->idetagere."'";
	$result = @pmb_mysql_query($requete, $dbh);
	
}


// get_cart() : ouvre une étagère et récupère le contenu
function constitution($modif=1) {
	global $dbh;
	global $PMBuserid ;
	global $msg ;
	$ret .= "<table><tr><th style='text-align:right;'>".$msg['etagere_caddie_inclus']."</th><th>".$msg['caddie_name']."</th></tr>" ;
	$rqt_caddie = "SELECT idcaddie, name, comment FROM caddie where type='NOTI' order by name "; 
	$rescaddie = @pmb_mysql_query($rqt_caddie, $dbh);
	$parity=1;
	while ($caddie = pmb_mysql_fetch_object($rescaddie)) {
		if ($PMBuserid==1 || verif_droit_caddie($caddie->idcaddie)) {
			if ($parity % 2) {
				$pair_impair = "even";
				} else {
					$pair_impair = "odd";
					}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
                	$link_visu_caddie = "<a href='./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&idcaddie=".$caddie->idcaddie."' target='_blank'>" ;
			$ret .= "<tr class='$pair_impair' $tr_javascript>";

			$ret .= "<td style='text-align:right;'><input type=checkbox name=idcaddie[] value='".$caddie->idcaddie."' class='checkbox' " ;
			if ($this->caddie_inclus($caddie->idcaddie)) $ret .= " checked ";
			if (!$modif) $ret .= " disabled='disabled' ";
			$ret .= " />&nbsp;</td>";

			$ret .= "<td>".$link_visu_caddie.$caddie->name ;
			if ($caddie->comment) $ret .= " (".$caddie->comment.")" ;
			$ret .= "</a>";
			$ret .= "</td>";
			$ret .= "</tr>" ;
			}
		}
	$ret .= "</table>" ;
	return $ret;
	}

function caddie_inclus($caddie) {
	global $dbh;
	$rqt = "SELECT count(1) FROM etagere_caddie where etagere_id='".$this->idetagere."' and caddie_id='".$caddie."' "; 
	return pmb_mysql_result(pmb_mysql_query($rqt,$dbh), 0, 0) ;
	}
	
	
	static function validate_img_folder () {
	
		global $msg,$dbh,$pmb_notice_img_folder_id;
	
		$message_folder='';
		if($pmb_notice_img_folder_id){
			$folder_error=true;
			$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
			$res = pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($res)){
				$rep=pmb_mysql_fetch_object($res);
				if(is_dir($rep->repertoire_path)){
					$folder_error=false;
				}
			}
			if($folder_error){
				if (SESSrights & ADMINISTRATION_AUTH){
					$requete = "select * from parametres where gestion=0 and type_param='pmb' and sstype_param='notice_img_folder_id' ";
					$res = pmb_mysql_query($requete);
					$i=0;
					if($param=pmb_mysql_fetch_object($res)) {
						$message_folder=" <a class='erreur' href='./admin.php?categ=param&action=modif&id_param=".$param->id_param."' >".$msg['etagere_img_folder_admin_no_access']."</a> ";
					}
				}else{
					$message_folder=$msg['etagere_img_folder_no_access'];
				}
			}
		}
		return $message_folder;
	
	}	
	
} // fin de déclaration de la classe cart
  
} # fin de déclaration du fichier caddie.class
