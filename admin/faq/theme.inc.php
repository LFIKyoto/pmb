<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: theme.inc.php,v 1.2 2019-07-30 14:22:49 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/faq_themes.class.php");

if(!isset($act)) $act = '';
if(!isset($id_liste)) $id_liste = 0;

$faq_themes = new faq_themes("faq_themes","id_theme","libelle_theme",$id_liste);
$faq_themes->proceed($act);
