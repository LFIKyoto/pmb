<?php

// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tables.tpl.php,v 1.7 2017-11-07 15:35:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$container='
<h1>'.$msg["sauv_tables_titre"].'</h1>
<table class="nobrd"><tr>
<td style="vertical-align:top; width:30%">!!tables_tree!!</td>
<td>
!!tables_form!!
</td>
</tr></table>';
?>