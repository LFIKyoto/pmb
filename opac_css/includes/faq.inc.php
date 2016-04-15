<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.inc.php,v 1.1 2014-04-02 12:29:07 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;

switch($lvl){
	case "faq" :
		require_once($class_path."/faq.class.php");
		$faq = new faq($faq_page,0,$faq_filters);
		print $faq->show();
		break;
	case "question" :
		
		break;
}