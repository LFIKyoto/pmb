<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: douchette.inc.php,v 1.15 2017-06-29 13:08:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

caddie_controller::proceed_barcode($idcaddie, 'collecte', 'add');
