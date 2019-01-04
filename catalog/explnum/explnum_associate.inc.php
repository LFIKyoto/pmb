<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate.inc.php,v 1.1 2014-01-10 15:46:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/explnum.class.php';
require_once $class_path.'/explnum_associate.class.php';
require_once $include_path.'/templates/explnum_associate.tpl.php';

global $explnum_id;

$explnum_associate = new explnum_associate(new explnum($explnum_id));
$explnum_associate->getPlayer($explnum_associate_tpl);

$explnum_associate->getAjaxCall($explnum_associate_tpl);

$explnum_associate->getReturnLink($explnum_associate_tpl);

print $explnum_associate_tpl;

?>