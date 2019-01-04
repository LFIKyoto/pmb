<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lieux.tpl.php,v 1.8 2017-11-07 15:34:41 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$container='
<h1>'.$msg["sauv_lieux_titre"].'</h1>
<table class="nobrd"><tr>
	<td style="vertical-align:top">!!lieux_tree!!</td>
	<td>
		!!lieux_form!!
	</td>
</tr>
</table>';

?>