<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2015-11-25 14:18:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/searcher_tabs.class.php');

//onglets de recherche autorites
$searcher_tabs = new searcher_tabs('authorities');
$searcher_tabs->proceed($mode, $action);