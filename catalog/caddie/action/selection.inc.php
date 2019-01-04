<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selection.inc.php,v 1.24 2017-06-29 13:08:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/caddie_procs.class.php");

caddie_controller::proceed_selection($idcaddie, 'action', 'selection');