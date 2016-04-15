<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs_exp_imp.inc.php,v 1.9 2015-04-03 11:16:21 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function procs_create($type_proc, $retour, $retour_erreur) {
	
	global $dbh, $msg, $current_module, $charset ;
	global $PMBuserid;
	
	$table[EMPRCADDIE]="empr_caddie_procs";
	$table[CADDIE]="caddie_procs";
	$table[PROCS]="procs";
	
	print "<div class=\"row\">
		<h1>".$msg['procs_title_form_import']."</h1>";
	
	$erreur=0;
	$userfile_name = $_FILES['f_fichier']['name'];
	$userfile_temp = $_FILES['f_fichier']['tmp_name'];
	$userfile_moved = basename($userfile_temp);
	
	$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
	
	// création
	if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {
		$fic=1;
	}
	
	if (!$fic) {
		$erreur=$erreur+10;
	}
		
	if ($fic) {
		$fp = fopen('./temp/'.$userfile_moved , "r" );
		$contenu = fread ($fp, filesize('./temp/'.$userfile_moved));
		if (!$fp || $contenu=="") $erreur=$erreur+100; ;
		fclose ($fp) ;
	}
	
	//import avec encodage taggé
	if(strpos($contenu,'#charset=iso-8859-1')!==false && $charset=='utf-8'){
		//mise à jour de l'encodage du contenu
		$contenu = utf8_encode($contenu);
		//mise à jour de l'entête des paramètres
		$contenu = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $contenu) ;
	}elseif(strpos($contenu,'#charset=utf-8')!==false && $charset=='iso-8859-1'){
		//mise à jour de l'encodage du contenu
		$contenu = utf8_decode($contenu);
		//mise à jour de l'entête des paramètres
		$contenu = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $contenu) ;
	}

	if ($userfile_name) {
		unlink('./temp/'.$userfile_moved);
	}
	
	$pos = strpos($contenu,'INSERT INTO '.$table[$type_proc].' set ');
	if (($pos === false) || ($pos>0)) {
		$erreur=$erreur+1000; ;
	}
	
	if (!$erreur) {
		// ajouter les droits pour celui qui importe
		if ($PMBuserid!=1) $contenu = str_replace("autorisations='1'", "autorisations='1 ".$PMBuserid."'", $contenu) ;
		
		pmb_mysql_query($contenu, $dbh) ;
		if (pmb_mysql_error()) {
			echo pmb_mysql_error()."<br /><br />".htmlentities($contenu,ENT_QUOTES, $charset)."<br /><br />" ;
			die ();
		}
		
		$new_proc_id = pmb_mysql_insert_id();
		$retour = str_replace("!!id!!",$new_proc_id,$retour);
		print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" >
				<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
				</form>";
		print "<script type=\"text/javascript\">document.dummy.submit();</script>";
	
	} else {
		print "<h1>".$msg['procs_import_invalide']."</h1>
				<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour_erreur\" >
					Error code = $erreur
				<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
				</form>";
	}
	print "</div>";
}
?>