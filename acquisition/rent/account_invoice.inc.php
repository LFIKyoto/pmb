<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account_invoice.inc.php,v 1.1 2016-02-17 09:13:46 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/rent/rent_invoice.class.php');

if (!$id) {print "<script> self.close(); </script>" ; die;}

$invoice=new rent_invoice($id);
$invoice->gen_invoice();
