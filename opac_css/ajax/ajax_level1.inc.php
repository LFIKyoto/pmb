<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_level1.inc.php,v 1.4 2014-11-20 15:46:01 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


require_once($class_path."/level1_search.class.php");
require_once($class_path."/facette_search.class.php");
require_once ("$base_path/includes/error_report.inc.php");
//On empêche l'abyme...
if ($autolevel1) { 
	$autolevel1=0; 
	$mode="tous";
}

//Recherches du niveau 1
$level1=new level1_search();
$nbresults=$level1->make_search();

$n=$_SESSION["nb_queries"];
$_SESSION["level1".$n]=$_SESSION["level1"];
$_SESSION["lq_level1"]=$_SESSION["level1"];

//On génère le bloc !
$result=facettes::do_level1();
print $result;
?>
