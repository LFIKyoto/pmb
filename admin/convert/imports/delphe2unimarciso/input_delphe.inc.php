<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: input_delphe.inc.php,v 1.2 2015-04-03 11:16:29 jpermanne Exp $

function _get_n_notices_($fi,$file_in,$input_params,$origine) {
	global $base_path;
	
	$first=true;
	$stop=false;
	$content="";
	$index=array();
	$n=1;
	$i=0;
	//Lecture du fichier d'entrée
	while (!feof($fi)) {
		$notice=fgets($fi,4096);
		if ($i>0 && $notice) {
			$requete="INSERT INTO import_marc (no_notice, notice, origine) VALUES ($n,'".addslashes($notice)."','$origine')";
			pmb_mysql_query($requete);
			$n++;
			$t=array();
			$t["POS"]=$n;
			$t["LENGHT"]=1;
			$index[]=$t;
		}
		$i++;
	}
	return $index;
}
?>