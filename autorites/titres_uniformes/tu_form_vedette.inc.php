<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tu_form_vedette.inc.php,v 1.2 2015-12-23 10:20:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/vedette/vedette_ui.class.php");

$vedette_ui = new vedette_ui(new vedette_composee(0, 'tu_authors'));
$form= $vedette_ui->get_form($role_field, $index, 'saisie_titre_uniforme');
print pmb_utf8_array_encode($form);