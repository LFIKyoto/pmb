<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_ascodocpsy.inc.php,v 1.6 2015-04-03 11:16:23 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// DEBUT paramétrage propre à la base de données d'importation :
require_once($class_path."/serials.class.php");
//require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");
$link_generate=0;//L'import des liens n'est pas compatible avec cette fonction
$isbn_dedoublonnage=0;//Pas de dédoublonage sur l'isbn
function recup_noticeunimarc_suite($notice) {
	global $info_461,$info_463		;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906;
	global $info_907,$info_908,$info_909,$info_910;
	global $info_606_a;
	global $bl,$hl;

	$info_461="";
	$info_463="";
	$info_900="";
	$info_901="";
	$info_902="";
	$info_903="";
	$info_904="";
	$info_905="";
	$info_906="";
	$info_907="";
	$info_908="";
	$info_909="";
	$info_910="";
	
	$record = new iso2709_record($notice, AUTO_UPDATE);
	
	$bl=$record->inner_guide['bl'];
	$hl=$record->inner_guide['hl'];	

	$info_461=$record->get_subfield("461","t","v");
	$info_463=$record->get_subfield("463","t","v");
	
	$info_606_a=$record->get_subfield_array_array("606","a");
	$info_900=$record->get_subfield_array_array("900","a");
	$info_901=$record->get_subfield_array_array("901","a");
	$info_902=$record->get_subfield_array_array("902","a");
	$info_903=$record->get_subfield_array_array("903","a");
	$info_904=$record->get_subfield("904","a");
	$info_905=$record->get_subfield_array_array("905","a");
	$info_906=$record->get_subfield_array_array("906","a");
	$info_907=$record->get_subfield_array_array("907","a");
	$info_908=$record->get_subfield("908","a");
	$info_909=$record->get_subfield("909","a");
	$info_910=$record->get_subfield("910","a");
	
} // fin recup_noticeunimarc_suite
	
function import_new_notice_suite() {
	global $dbh ;
	global $notice_id ;
	
	global $info_461, $info_463 ;
	global $info_606_a;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906;
	global $info_907,$info_908,$info_909,$info_910,$info_911;
	
	global $bl,$hl;

	//cas d'un article
	if ($bl == "a" && $hl == "2"){
		$bulletin = array(
			'num' => (clean_string($info_461[0]["v"]) ? 'vol '.clean_string($info_461[0]["v"]).' ' : '').'n°'.clean_string($info_463[0]["v"])
		);
		$perio = array(
			'titre' => $info_461[0]['t'],
			'volume' => $info_461[0]['v']
		);
		notice_to_article($perio,$bulletin);
	} elseif($bl == "s" && $hl == "1"){
		update_notice("s", "1");
	}
	
	//Branche MOTCLE du Thésaurus DOC
	do_thesaurus_ascodocpsy(3, "MOTCLE", $info_606_a);

	//Branche CANDES du Thésaurus DOC
	if (count($info_900)) {
		do_thesaurus_ascodocpsy(3, "CANDES", $info_900);
	}

	//Branche THEME du Thésaurus DOC
	if (count($info_901)) {
		do_thesaurus_ascodocpsy(3, "THEME", $info_901);
	}

	//Branche NOMP du Thésaurus DOC
	if (count($info_902)) {
		do_thesaurus_ascodocpsy(3, "NOMP", $info_902);
	}	

	//Producteur de la fiche
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_prodfich'");
	if (count($info_903) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		$max=@pmb_mysql_result($resultat,0,0);
		$n=$max+1;
		for ($i=0; $i<count($info_903); $i++) {
			for ($j=0; $j<count($info_903[$i]); $j++) {
				$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_903[$i][$j])."' and notices_custom_champ=".$cp_id;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					$value=pmb_mysql_result($resultat,0,0);
				} else {
					$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_903[$i][$j])."')";
					pmb_mysql_query($requete);
					$value=$n;
					$n++;
				}
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
				pmb_mysql_query($requete);
			}
		}
	}

	//DIPSPE
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_dipspe'");
	if ($info_904[0] && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_904[0])."')";
		pmb_mysql_query($requete);
	}

	//Annexe
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_annexe'");
	if (count($info_905) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		for ($i=0; $i<count($info_905); $i++) {
			for ($j=0; $j<count($info_905[$i]); $j++) {
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_905[$i][$j])."')";
				pmb_mysql_query($requete);
			}
		}
	}

	//Lien annexe
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_lienanne'");
	if (count($info_906) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		for ($i=0; $i<count($info_906); $i++) {
			for ($j=0; $j<count($info_906[$i]); $j++) {
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_906[$i][$j])."')";
				pmb_mysql_query($requete);
			}
		}
	}

	//Localisation
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_loc'");
	if (count($info_907) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		$max=@pmb_mysql_result($resultat,0,0);
		$n=$max+1;
		for ($i=0; $i<count($info_907); $i++) {
			for ($j=0; $j<count($info_907[$i]); $j++) {
				$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_907[$i][$j])."' and notices_custom_champ=".$cp_id;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					$value=pmb_mysql_result($resultat,0,0);
				} else {
					$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_907[$i][$j])."')";
					pmb_mysql_query($requete);
					$value=$n;
					$n++;
				}
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
				pmb_mysql_query($requete);
			}
		}
	}

	//Nature du texte
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_nattext'");
	if (count($info_908[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_908[0])."')";
		pmb_mysql_query($requete);
	}
	
	//Date du texte
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_datetext'");
	if (count($info_909[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_date) values($cp_id,$notice_id,'".$info_909[0]."')";
		pmb_mysql_query($requete);
	}
	
	//Numéro du texte officiel
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_numtexof'");
	if (count($info_910[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_910[0])."')";
		pmb_mysql_query($requete);
	}

} // fin import_new_notice_suite

//descripteurs
function do_thesaurus_ascodocpsy($id_thesaurus, $nom_categ, $branch_values=array(), $lang='fr_FR', $ordre_categ=0) {
	global $notice_id;
	
	$res=pmb_mysql_query("select id_noeud from noeuds where autorite='TOP' and num_thesaurus='".$id_thesaurus."'");
	if($res && pmb_mysql_num_rows($res)){
		$parent_thes = pmb_mysql_result($res,0,0);
	}else{
		return;
	}
	$rqt = "select id_noeud from noeuds join categories on id_noeud=num_noeud and libelle_categorie='".$nom_categ."' and num_parent='".$parent_thes."'";
	$res = pmb_mysql_query($rqt);
	if ($res && pmb_mysql_num_rows($res)) {
		$num_parent = pmb_mysql_result($res,0,0);
		foreach ($branch_values as $terms){
			foreach($terms as $term){
				$categ_id = categories::searchLibelle(addslashes($term),$id_thesaurus,$lang,$num_parent);
				if($categ_id){
					//le terme existe
					$noeud = new noeuds($categ_id);
					if($noeud->num_renvoi_voir){
						$categ_to_index = $noeud->num_renvoi_voir;
					}else{
						$categ_to_index = $categ_id;
					}
				}else{
					//le terme est à créé
					$n = new noeuds();
					$n->num_thesaurus = $id_thesaurus;
					$n->num_parent = $num_parent;
					$n->save();
					$c = new categories($n->id_noeud, $lang);
					$c->libelle_categorie = $term;
					$c->index_categorie = ' '.strip_empty_words($term).' ';
					$c->save();
					
					$categ_to_index = $n->id_noeud;
				}
				$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) VALUES($notice_id,$categ_to_index,$ordre_categ)";
				pmb_mysql_query($requete);
				$ordre_categ++;	
			}
		}
	}
}

function update_notice($bl,$hl){
	global $notice_id;
	$update =" update notices set niveau_biblio = '$bl', niveau_hierar ='$hl' where notice_id = $notice_id";
	pmb_mysql_query($update);
}

function notice_to_article($perio_info,$bull_info){
	global $notice_id;
	$bull_id = genere_bulletin($perio_info,$bull_info);
	update_notice("a","2");
	$insert = "insert into analysis set analysis_bulletin = $bull_id, analysis_notice = $notice_id";
	pmb_mysql_query($insert);
	
}

function genere_perio($perio_info){
	$search = "select notice_id from notices where tit1 LIKE '".addslashes($perio_info['titre'])."' and niveau_biblio = 's' and niveau_hierar = '1'";
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		//il existe pas, faut le créer
		$chapeau=new serial();
		$info=array();
		$info['tit1']=addslashes($perio_info['titre']);
		$info['niveau_biblio']='s';
		$info['niveau_hierar']='1';
		$info['typdoc']='p';
				
		$chapeau->update($info);
		$perio_id=$chapeau->serial_id;
	}else $perio_id = pmb_mysql_result($res,0,0);
	return $perio_id;
}

function genere_bulletin($perio_info,$bull_info,$isbull=true){
	global $bl,$hl,$notice_id;
	//on récup et/ou génère le pério
	$perio_id = genere_perio($perio_info);

	$search = "select bulletin_id from bulletins where bulletin_numero LIKE '".addslashes($bull_info['num'])."' and bulletin_notice = $perio_id";
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		$bulletin=new bulletinage("",$perio_id);
		$info=array();
		$info['bul_titre']='';
		$info['bul_no']=addslashes($bull_info['num']);
		$bull_id=$bulletin->update($info);
	}else {
		$bull_id = pmb_mysql_result($res,0,0);
		//on regarde si une notice n'existe pas déjà pour ce bulletin
		/*$req = "select num_notice from bulletins where bulletin_id = $bull_id and num_notice != 0";
		$res = pmb_mysql_query($req);
		//si oui on retire l'enregistrement en cours, et on continue sur la notice existante...
		if(pmb_mysql_num_rows($res)>0) {
			notice::del_notice($notice_id);
			$notice_id = pmb_mysql_result($res,0,0);
		}*/
	}
	return $bull_id;
}