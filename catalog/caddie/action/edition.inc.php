<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edition.inc.php,v 1.13 2018-08-06 10:46:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("./classes/notice_tpl_gen.class.php");

if(empty($mode)) $mode = 'simple';
caddie_controller::proceed_edition($idcaddie, $mode);