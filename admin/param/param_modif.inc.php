<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_modif.inc.php,v 1.9 2015-04-03 11:16:28 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$requete = "SELECT * FROM parametres WHERE id_param='$id_param' and gestion=0 ";
$res = pmb_mysql_query($requete, $dbh);
$nbr = pmb_mysql_num_rows($res);

$param_default="<tr><td colspan='2' align='left'><hr />".$param_user."<hr />".$deflt_user."</td></tr>";
if($nbr) {
	$params=pmb_mysql_fetch_object($res);
	param_form(	$params->id_param,
				$params->type_param,
				$params->sstype_param,
				$params->valeur_param,
				$params->comment_param	);
	}
