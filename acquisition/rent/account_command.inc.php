<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account_command.inc.php,v 1.2 2016-03-01 12:01:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/rent/rent_request.class.php');

if (!$id) {print "<script> self.close(); </script>" ; die;}

$rent_request=new rent_request($id);
$rent_request->gen_command();
