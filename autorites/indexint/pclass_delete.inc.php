<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pclass_delete.inc.php,v 1.5 2015-04-03 11:16:27 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// si tout est OK, on a les variables suivantes à exploiter :
// $id_pclass				identifiant de classement (0 si nouveau)
if($id_pclass == 1){
	// Interdire l'effacement de l'id 1
	error_form_message($msg["pclassement_suppr_impossible_protege"]);
	exit;
}	
$requete = "SELECT indexint_id FROM indexint WHERE num_pclass='".$id_pclass."' " ;
$result = pmb_mysql_query($requete, $dbh) or die ($requete."<br />".pmb_mysql_error());
if(pmb_mysql_num_rows($result)) {
	// Il y a des enregistrements. Interdire l'effacement.
	error_form_message($msg["pclassement_suppr_impossible"]);
	exit;
	
} else {
	// effacement
	$dummy = "delete FROM pclassement WHERE id_pclass='$id_pclass' ";
	pmb_mysql_query($dummy, $dbh);		
}
include('./autorites/indexint/pclass.inc.php');
