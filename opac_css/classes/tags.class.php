<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// © 2006 mental works / www.mental-works.com contact@mental-works.com
// 	repris et corrigé par PMB Services 
// +-------------------------------------------------+
// $Id: tags.class.php,v 1.15 2015-04-03 11:16:17 jpermanne Exp $

// définition de la classe d'affichage des 'tags'

class tags {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------


	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	function tags() {
	}

	function listeAlphabetique(){
		//renvoie la liste des tags existants
		global $dbh;
		global $pmb_keyword_sep;
		
		$requete = "select index_l from notices where index_l is not null and index_l!=''";
		$arr=array();
		$r = pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($r)){
			while ($loc = pmb_mysql_fetch_object($r)) {
				$liste = explode($pmb_keyword_sep,$loc->index_l);
				for ($i=0;$i<count($liste);$i++){
					$index=trim($liste[$i]);
					if ($index) $arr[strtolower($index)]++;
				}
			}
		}
		global $opac_allow_tags_search_min_occ ;
		if ($opac_allow_tags_search_min_occ>1) {
			$arr_purge=array();
			foreach ($arr as $key => $value) {
				if ($value>=$opac_allow_tags_search_min_occ) $arr_purge[$key]=$value ;
			}
			$arr=$arr_purge;
		}
		ksort($arr);
		$count=0;
		$max=0;
		//les seuils permettent de séparer les valeurs en 4 groupes pour afficher les tags dans 4 tailles différentes en fct de leur fréquence 
		foreach ($arr as $key => $value){
			$count++;
			$somme+=$value;
			if ($max<$value) $max=$value;
		}
		$seuil2 = array_sum($arr)/count($arr);//moyenne des valeurs
		$seuil1 = $seuil2/2;
		$seuil3 = $seuil2+($max-$seuil2)/2;//mi chemin en la valeur max et la moyenne

		$lettre="a";
		$reponse="";
		foreach ($arr as $key => $value) {
			if ($key{0}!=$lettre) {
				$lettre=$key{0};
				if($reponse) 
					$reponse.="<br /><br />";
			} else if($reponse) $reponse.=", ";
			if ($value<$seuil1) $reponse.="<a href='./index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF1'>$key</a> ";
				elseif ($value<$seuil2) $reponse.="<a href='./index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF2'>$key</a> ";
					elseif ($value<$seuil3) $reponse.="<a href='./index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF3'>$key</a> ";
						else $reponse.="<a href='./index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF4'>$key</a> ";
		}
		return $reponse;
	}
	
	
	function bold($str,$needle) {
		//cherche si un des mots de $needle existe dans $str et le met en gras
		$str_propre=strtolower(convert_diacrit($str));
		$mot=strtolower(convert_diacrit($needle));
		if (!(($pos=strpos($str_propre,$mot))===false))  {
			$size= strlen("<span class='tagQuery'>") + strlen($needle)+$pos ;
			$str=substr_replace($str, "<span class='tagQuery'>", $pos, 0);
			$str=substr_replace($str, "</span>", $size,0);
		}
		return $str;
	}

	function chercheTag($user_query){
		global $dbh;
		global $msg;
		global $pmb_keyword_sep ;
		$user_query=trim($user_query); 
		$requete = "select index_l from notices where index_l like '%$user_query%'";
		$user_query=stripslashes($user_query);
		$arr=array();
		$r = pmb_mysql_query($requete,$dbh);
		
		while ($loc = pmb_mysql_fetch_object($r)) {
			$liste = explode($pmb_keyword_sep,$loc->index_l);
			for ($i=0;$i<count($liste);$i++){
				$index=trim($liste[$i]);
				if ($index) $arr[$index]++;
			}
		}	
		ksort($arr);
		//les seuils permettent de séparer les valeurs en 4 groupes pour afficher les tags dans 4 tailles différentes en fct de leur fréquence 
		$count=0;
		$max=0;
		foreach ($arr as $key => $value){
			$texte=$this->bold($key,$user_query);
			if (!(strpos($texte,"</span>")===false)) {
				$count++;
				$somme+=$value;
				if ($max<$value) $max=$value;
			}
		}
		if(!$count){
			$count=1;
		}
		$seuil2 = $somme/$count;//moyenne des valeurs
		$seuil1 = $seuil2/2;
		$seuil3 = $seuil2+($max-$seuil2)/2;//mi chemin en la valeur max et la moyenne
		
		$reponse="";
		foreach ($arr as $key => $value){
			$texte=$this->bold($key,$user_query);
			
			if (!(strpos($texte,"</span>")===false)) {
				if ($reponse) $reponse.=", ";
				if ($value<$seuil1) $reponse.="<a href='index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF1'>$texte</a> ";
					elseif ($value<$seuil2) $reponse.="<a href='index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF2'>$texte</a> ";
						elseif ($value<$seuil3) $reponse.="<a href='index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF3'>$texte</a> ";
							else $reponse.="<a href='index.php?lvl=more_results&mode=keyword&user_query=".urlencode($key)."&tags=ok' class='TagF4'>$texte</a> ";
			}
		}
		if (count($arr)==0) $reponse=$msg["no_result"];
		return $reponse;
	}

}
?>