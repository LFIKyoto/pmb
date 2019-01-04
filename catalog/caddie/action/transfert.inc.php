<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transfert.inc.php,v 1.13 2017-07-05 09:34:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idcaddie_origine)) $idcaddie_origine = 0;
caddie_controller::proceed_transfert($idcaddie, $idcaddie_origine);