<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nav_sections.tpl.php,v 1.1 2011-04-20 06:28:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$tpl_liste_item_tableau = "
<table>
	<tr>
		<th>".$this->msg["selection_opac"]."</th>
		<th>".$msg[21]."</th>
	</tr>
	!!lignes_tableau!!
</table>
";

$tpl_liste_item_tableau_ligne = "
	<tr class='!!pair_impair!!' '!!tr_surbrillance!!' >
		<td><input value='1' name='nav_sections_selected_!!id!!' !!selected!! type='checkbox'></td>
		<td !!td_javascript!! >!!name!!</td>
	</tr>
";
?>
