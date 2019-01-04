<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: remote_procs.inc.php,v 1.12 2016-11-18 13:16:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/caddie_procs.class.php");

caddie_procs::proceed_remote();
?>