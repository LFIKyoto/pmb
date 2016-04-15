<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_commande.inc.php,v 1.1 2011-08-16 12:17:30 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour liste des relances de receptions
// reçoit : id_bibli, id_cde

require_once("$class_path/entites.class.php");
require_once("$base_path/acquisition/achats/commandes/lettre_commande.class.php");

if ($id_cde && $id_bibli){
	
	$lettre = lettreCommande_factory::make();
	$lettre->doLettre($id_bibli, $id_cde);
	$lettre->getLettre();
}