<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie_procs.class.php,v 1.2 2019-07-05 13:25:14 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/caddie_procs.class.php");

// définition de la classe de gestion des procédures de paniers

class authorities_caddie_procs extends caddie_procs {
	
	public static $module = 'autorites';
	public static $table = 'authorities_caddie_procs';
}