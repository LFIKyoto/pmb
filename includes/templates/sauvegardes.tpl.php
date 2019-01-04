<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sauvegardes.tpl.php,v 1.7 2017-11-07 15:35:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$container='
<h1>'.$msg["sauv_sauvegardes_titre"].'</h1>
<table class="nobrd"><tr>
<td style="vertical-align:top">!!sauvegardes_tree!!</td>
<td>
!!sauvegardes_form!!
</td>
</tr></table>';
?>