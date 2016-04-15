<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.inc.php,v 1.2 2014-04-02 14:06:22 jpermanne Exp $

//ATTENTION : c'est un export vraiment personnalisé, on ne tient pas compte du formulaire avec les générations de liens...

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

include_once($class_path."/synchro_rdf.class.php");

function _export_($id,$keep_expl=0,$params=array()) {
	
	$export=new synchro_rdf(session_id());

	$export->addRdf($id,0);
	
	return;
}