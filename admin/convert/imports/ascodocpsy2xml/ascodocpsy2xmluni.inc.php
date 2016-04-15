<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ascodocpsy2xmluni.inc.php,v 1.1 2013-01-23 15:24:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/marc_table.class.php");

function convert_ascodocpsy($notice, $s, $islast, $isfirst, $param_path) {
	global $cols;
	global $ty;
	global $authors_function;
	global $base_path,$origine;
	global $tab_functions;
	global $charset;

	if (!$tab_functions) $tab_functions=new marc_list('function');
	
	if (!$cols) {
		//On lit les intitulés dans le fichier temporaire
		$fcols=fopen("$base_path/temp/".$origine."_cols.txt","r");
		if ($fcols) {
			$cols=fread($fcols,filesize("$base_path/temp/".$origine."_cols.txt"));
			fclose($fcols);
			$cols=unserialize($cols);
		}
	}
	
	if (!$ty) {
		$ty=array("Livre"=>"a","Congrès"=>"h","Mémoire"=>"r",
				"Thèse"=>"o","Rapport"=>"q","Texte officiel"=>"t",
				"Périodique"=>"p","Article"=>"s","Document multimédia"=>"m");
	}
	
	$fields=explode("\t",$notice);
	for ($i=0; $i<count($fields); $i++) {
		$ntable[$cols[$i]]=$fields[$i];
	}

	if ((!$ntable["TIT"]) && (!$ntable["REV"] && $ntable["TYPE"] == "Périodique")) {
		$data=""; 
		$error="Titre vide<br />".$notice;
	} elseif (array_key_exists("REV", $ntable) && ($ntable["REV"] == "") && ($ntable["TYPE"] != "Rapport")) {
		$data=""; 
		$error="Titre de revue vide<br />".$notice;
	} elseif (!$ntable["TYPE"]) {
		$data=""; 
		$error="Aucun type de document<br />".$notice;
	} else {
		$error="";
		$data="<notice>\n";
		
		//Entête
		$data.="  <rs>n</rs>\n";
		if ($ty[$ntable["TYPE"]]) $dt=$ty[$ntable["TYPE"]]; else $dt="a";
		
		switch ($dt) {
			case "p":
				$bl = "s";
				$hl = "1";
				break;
			case "s":
			case "t":
				$bl = "a";
				$hl = "2";
				break;
			default :
				if(($dt == "q") && ($ntable["REV"])) {
					$bl = "a";
					$hl = "2";
				} else {
					$bl = "m";
					$hl = "*";
				}
		}
		$data.="  <dt>".$dt."</dt>\n";
		$data.="<bl>".$bl."</bl>\n";
		$data.="<hl>".$hl."</hl>\n<el>1</el>\n<ru>i</ru>\n";
		
//		//Support du document
//		if ($ntable["SUPPORT"]) {
//			
//		}
		
		//Traitement des titres
		if ($ntable["TIT"]) {
			$data.="  <f c='200' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["TIT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}

		//Titre de revue (périodique)
		if($ntable["REV"]){
			if ($ntable["TYPE"] == "Périodique") {
				$code = '200';
				$ss_code = 'a';
			} else {
				$code = '461';
				$ss_code = 't';
			}
			$data .= "	<f c='".$code."' ind='  '>\n";
			$data .= "		<s c='".$ss_code."'>".htmlspecialchars($ntable["REV"],ENT_QUOTES,$charset)."</s>\n";
			//Volume ou tome
			if ($ntable["VOL"] && ($code == "461")) {
				$data.="    	<s c='v'>".htmlspecialchars($ntable["VOL"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Date de publication du texte
		if ($ntable["DATEPUB"]) {
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["DATEPUB"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Traitement des Auteurs
		if ($ntable["AUT"] && ($ntable["AUT"] != "[s.n.]")) {
			if (!$authors_function) {
				$authors_function=array("Collab."=>"Collaborateur","Coord."=>"Coordinateur","Dir."=>"Directeur de publication","Ed."=>"Editeur scientifique",
				"Ill."=>"Illustrateur","Préf."=>"Préfacier","Trad."=>"Traducteur","Postf."=>"Postfacier");
			}
			$auteurs=explode("/",$ntable["AUT"]);
			for ($i=0; $i<count($auteurs); $i++) {
				//preg_match_all('~\b[[:upper:]]+\b~', trim($auteurs[$i]),$matches);
				$fonction = "";
				$func_author = "";
				if (substr($auteurs[$i], strlen($auteurs[$i])-1,strlen($auteurs[$i])) == ".") {
					$func_author = trim(substr($auteurs[$i], strrpos($auteurs[$i], " "),strlen($auteurs[$i])));
				}
				if (array_key_exists($func_author, $authors_function)) {
					$fonction = $authors_function[$func_author];
				}
				$entree=trim(str_replace($func_author, "", $auteurs[$i]));
				if ($entree) {
					if ($i == 0) $data.="  <f c='700' ind='  '>\n";
					else $data.="  <f c='701' ind='  '>\n";
					$data.="    <s c='a'>".htmlspecialchars($entree,ENT_QUOTES,$charset)."</s>\n";
//					if ($rejete) {
//						$data.="    <s c='b'>".htmlspecialchars($rejete,ENT_QUOTES,$charset)."</s>\n";
//					}
					$as=array_search($fonction,$tab_functions->table);
					if (($as!==false)&&($as!==null)) $fonction=$as; else $fonction="070";
					$data.="    <s c='4'>".$fonction."</s>\n";
					$data.="  </f>\n";
				}
			}
		}
		
		//Numéro - infos bulletin
		if (($ntable["NUM"]) && ($ntable["NUM"] != "[s.n.]")) {
			//infos bulletin
			$data .= "<f c='463' ind='  '>";
			$data.="	<s c='v'>".htmlspecialchars($ntable["NUM"],ENT_QUOTES,$charset)."</s>";
			$data.="</f>\n";
		}

		//Date de vie et de mort du périodique
		if (($ntable["VIEPERIO"]) && ($ntable["VIEPERIO"] != "[s.d.]")) {
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["VIEPERIO"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
//		//Etat des collections des centres
//		if ($ntable["ETATCOL"]) {
//			
//		}
		
		//Congrès
		if (($ntable["CONGRTIT"]) || ($ntable["CONGRNUM"]) || ($ntable["CONGRLIE"]) || ($ntable["CONGRDAT"])) {
			$data.="  <f c='712' ind='1 '>\n";
			//Intitulé du congrès
			if ($ntable["CONGRTIT"]) {
				$data.="    <s c='a'>".htmlspecialchars($ntable["CONGRTIT"],ENT_QUOTES,$charset)."</s>\n";
			}
			//Numéro du congrès
			if ($ntable["CONGRNUM"]) {
				$data.="    <s c='d'>".htmlspecialchars($ntable["CONGRNUM"],ENT_QUOTES,$charset)."</s>\n";
			}	
			//Lieu du congrès
			if ($ntable["CONGRLIE"]) {
				$data.="    <s c='e'>".htmlspecialchars($ntable["CONGRLIE"],ENT_QUOTES,$charset)."</s>\n";
			}
			//Date du congrès
			if ($ntable["CONGRDAT"]) {
				$data.="    <s c='f'>".htmlspecialchars($ntable["CONGRDAT"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Editeurs
		if (($ntable["EDIT"]) && ($ntable["EDIT"] != "[s.n.]")) {
			$editeurs = explode("/", $ntable["EDIT"]);
			$data.="  <f c='210' ind='  '>\n";
			for ($i=0; $i<count($editeurs); $i++) {
				$data.="    <s c='c'>".htmlspecialchars($editeurs[$i],ENT_QUOTES,$charset)."</s>\n";				
			}
			if (($ntable["LIEU"]) && ($ntable["LIEU"] != "[s.l.]")) {
				$lieux = explode("/", $ntable["LIEU"]);
				for ($i=0; $i<count($lieux); $i++) {
					$data.="    <s c='a'>".htmlspecialchars($lieux[$i],ENT_QUOTES,$charset)."</s>\n";				
				}
			}
			if ($ntable["DATE"]) {
				$data.="    <s c='d'>".htmlspecialchars($ntable["DATE"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		} elseif ($ntable["DATE"]) {
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["DATE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Réédition
		if ($ntable["REED"]) {
			$data.="  <f c='205' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["REED"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Collection
		if ($ntable["COL"]) {
			$pos_deb_subtitle=strpos($ntable["COL"],":");
			$pos_deb_num_col=strpos($ntable["COL"],";");
			$data.="  <f c='225' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["COL"],ENT_QUOTES,$charset)."</s>\n";
			if ($pos_deb_num_col) {
				$data.="    <s c='a'>".htmlspecialchars(substr($ntable["COL"],$pos_deb_num_col+1),ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Nombre de pages
		if (($ntable["PAGE"]) && ($ntable["PAGE"] != "[s.p.]")) {
			$data.="  <f c='215' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["PAGE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//PDPF
		if ($ntable["PDPF"]) {
			$data.="  <f c='215' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["PDPF"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Traitement des Mots-clés
		if ($ntable["MOTCLE"]) {
			$motcles = explode("/",$ntable["MOTCLE"]);
			for ($i=0; $i<count($motcles); $i++) {
				$data.="  <f c='606' ind='  '>\n";
				$data.="    <s c='a'>".htmlspecialchars($motcles[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}

		//Résumé
		if ($ntable["RESU"]) {
			$data.="  <f c='330' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["RESU"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Lien
		if ($ntable["LIEN"]) {
			$data.="  <f c='856' ind='  '>\n";
			$data.="    <s c='u'>".htmlspecialchars($ntable["LIEN"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Notes
		if ($ntable["NOTES"]) {
			$data.="  <f c='300' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NOTES"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//ISBNISSN
		if (($ntable["ISBNISSN"]) && ($ntable["ISBNISSN"] != "0000-0000")) {
			$isbnissn = explode("/",$ntable["ISBNISSN"]);
			$data.="  <f c='010' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($isbnissn[0],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Champs spéciaux
		//Candidat-descripteur
		if ($ntable["CANDES"]) {
			$candes = explode("/", $ntable["CANDES"]);
			for ($i=0; $i < count($candes); $i++) {
				$data.="  <f c='900'>\n";
				$data.="    <s c='a'>".htmlspecialchars($candes[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		//Thème
		if ($ntable["THEME"]) {
			$data.="  <f c='901'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["THEME"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		//Nom Propre
		if ($ntable["NOMP"]) {
			$nomp = explode("/", $ntable["NOMP"]);
			for ($i=0; $i < count($nomp); $i++) {
				$data.="  <f c='902'>\n";
				$data.="    <s c='a'>".htmlspecialchars($nomp[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		//Producteur de la fiche
		if ($ntable["PRODFICH"]) {
			$prodfich = explode("/", $ntable["PRODFICH"]);
			for ($i=0; $i < count($prodfich); $i++) {
				$data.="  <f c='903'>\n";
				$data.="    <s c='a'>".htmlspecialchars($prodfich[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		//DIPSPE
		if ($ntable["DIPSPE"]) {
			$data.="  <f c='904'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["DIPSPE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		//Annexe
		if ($ntable["ANNEXE"]) {
			$annexe = explode(" ; ", $ntable["ANNEXE"]);
			for ($i=0; $i < count($annexe); $i++) {
				$data.="  <f c='905'>\n";
				$data.="    <s c='a'>".htmlspecialchars($annexe[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";	
			}
		}
		//Lien annexe
		if ($ntable["LIENANNE"]) {
			$lienanne = explode(" ; ", $ntable["LIENANNE"]);
			for ($i=0; $i < count($lienanne); $i++) {
				$data.="  <f c='906'>\n";
				$data.="    <s c='a'>".htmlspecialchars($lienanne[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";				
			}
		}
		
		//Localisation
		if ($ntable["LOC"]) {
			$loc = explode("/", $ntable["LOC"]);
			for ($i=0; $i < count($loc); $i++) {
				$data.="  <f c='907'>\n";
				$data.="    <s c='a'>".htmlspecialchars($loc[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		
		//Nature du texte
		if ($ntable["NATTEXT"]) {
			$data.="  <f c='908'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NATTEXT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Date du texte
		if ($ntable["DATETEXT"]) {
			$data.="  <f c='909'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["DATETEXT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Numéro du texte officiel
		if ($ntable["NUMTEXOF"]) {
			$data.="  <f c='910'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NUMTEXOF"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
//		//Date de saisie
//		if ($ntable["DATESAIS"]) {
//			$data.="  <f c='912'>\n";
//			$data.="    <s c='a'>".htmlspecialchars($ntable["DATESAIS"],ENT_QUOTES,$charset)."</s>\n";
//			$data.="  </f>\n";
//		}
		
//		//Date de fin de validité
//		if ($ntable["DATEVALI"]) {
//			$data.="  <f c='910'>\n";
//			$data.="    <s c='a'>".htmlspecialchars($ntable["DATEVALI"],ENT_QUOTES,$charset)."</s>\n";
//			$data.="  </f>\n";
//		}
		
		$data.="</notice>\n";
	}
	
	if (!$error) $r['VALID'] = true; else $r['VALID']=false;
	$r['ERROR'] = $error;
	$r['DATA'] = $data;
	return $r;
}
?>