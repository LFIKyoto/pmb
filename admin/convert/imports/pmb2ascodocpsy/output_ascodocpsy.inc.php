<?php
function _get_header_($output_params) {
	
	$tab_r = array();
	switch ($output_params["DOCTYPE"]) {
		case "a" :
		case "h" :
			//doctype : ouvrage/congrès
			$tab_r = array("TYPE","AUT","TIT","CONGRTIT","CONGRNUM","CONGRLIE","CONGRDAT","EDIT","LIEU","COL","REED","PAGE","DATE","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","NOTES","ISBNISSN","PRODFICH","LOC");
			break;
		case "o" :
		case "r" :
			//doctype : thèse/mémoire
			$tab_r = array("TYPE","AUT","TIT","DIPSPE","EDIT","LIEU","PAGE","DATE","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","NOTES","PRODFICH","LOC");
			break;
		case "m" :
			//doctype : document multimédia
			$tab_r = array("TYPE","SUPPORT","AUT","TIT","EDIT","LIEU","DATE","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","NOTES","PRODFICH","LOC");
			break;
		case "s" :
			//doctype : article
			$tab_r = array("TYPE","AUT","TIT","REV","VOL","NUM","PDPF","DATE","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","NOTES","PRODFICH");
			break;
		case "p" :
			//doctype : périodique
			$tab_r = array("TYPE","REV","VIEPERIO","ETATCOL","ISBNISSN","NOTES","LIEN","PRODFICH");
			break;
		case "t" :
			//doctype : texte officiel
			$tab_r = array("TYPE","NATTEXT","DATETEXT","DATEPUB","TIT","REV","NUM","NUMTEXOF","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","ANNEXE","LIENANNE","DATESAIS","DATEVALI","PRODFICH");
			break;
		case "q" :
			//doctype : document en ligne (rapport)
			$tab_r = array("TYPE","AUT","TIT","EDIT","LIEU","COL","REED","PAGE","REV","VOL","NUM","DATE","MOTCLE","CANDES","THEME","NOMP","RESU","LIEN","NOTES","ISBNISSN","DATESAIS","PRODFICH");
			break;
		default :
			break;
	}
	$r = implode("\t", $tab_r);
	$r.= "\n";
	return $r;
}

function _get_footer_($output_params) {
	return "";
}

?>