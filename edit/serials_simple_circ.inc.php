<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_simple_circ.inc.php,v 1.1.4.2 2015-09-22 13:17:41 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//require_once("$include_path/templates/serials_simple_circ.tpl.php");

require_once("$class_path/simple_circ.class.php");

$simple_circ= new simple_circ($start_date,$end_date);
//$simple_circ= new simple_circ("2013-09-01","2013-11-03");
$data=$simple_circ->get_data();
//printr($data);
print $simple_circ->get_display();