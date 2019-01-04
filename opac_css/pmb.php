<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb.php,v 1.1 2018-03-01 16:49:42 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($class_path."/campaigns/campaigns_controller.class.php");

campaigns_controller::proceed($hash, $url, $id);