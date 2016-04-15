<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-devis.inc.php,v 1.18 2012-02-13 16:14:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour devis
// reçoit : id_dev

require_once("$class_path/lettre_devis.class.php");

if ($id_dev && $id_bibli){
	
	$lettre = lettreDevis_factory::make();
	$lettre->doLettre($id_bibli, $id_dev);
	$lettre->getLettre();
}

?>