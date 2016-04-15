<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.inc.php,v 1.1 2015-03-30 07:14:52 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/classementGen.class.php");

$classementGen = new classementGen($object_type,$object_id);

switch($action){
	case "update" :
		print $classementGen->saveLibelle($classement_libelle);
		break;
}