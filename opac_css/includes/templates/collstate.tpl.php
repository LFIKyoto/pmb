<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.tpl.php,v 1.6 2014-08-29 10:23:00 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$collstate_list_header = "
<table class='exemplaires' cellpadding='2' width='100%'>
	<tbody>
";

$collstate_list_footer ="
	</tbody>
</table>";

$tpl_collstate_liste[0]="
<table class='exemplaires' cellpadding='2' width='100%'>
	<tbody>
		<tr>
			<th>".$msg["collstate_form_emplacement"]."</th>		
			<th>".$msg["collstate_form_cote"]."</th>
			<th>".$msg["collstate_form_support"]."</th>
			<th>".$msg["collstate_form_statut"]."</th>			
			<th>".$msg["collstate_form_origine"]."</th>		
			<th>".$msg["collstate_form_collections"]."</th>
			<th>".$msg["collstate_form_archive"]."</th>
			<th>".$msg["collstate_form_lacune"]."</th>		
		</tr>
		!!collstate_liste!!	
	</tbody>	
</table>
";

$tpl_collstate_liste_line[0]="
<tr class='!!pair_impair!!' !!tr_surbrillance!! >
	<!-- surloc -->
	<td !!tr_javascript!! >!!emplacement_libelle!!</td>
	<td !!tr_javascript!! >!!cote!!</td>
	<td !!tr_javascript!! >!!type_libelle!!</td>
	<td !!tr_javascript!! >!!statut_libelle!!</td>	
	<td !!tr_javascript!! >!!origine!!</td>
	<td !!tr_javascript!! >!!state_collections!!</td>
	<td !!tr_javascript!! >!!archive!!</td>
	<td !!tr_javascript!! >!!lacune!!</td>
</tr>";

$tpl_collstate_liste[1]="
<table class='exemplaires' cellpadding='2' width='100%'>
	<tbody>
		<tr>
			<!-- surloc -->
			<th>".$msg["collstate_form_localisation"]."</th>		
			<th>".$msg["collstate_form_emplacement"]."</th>		
			<th>".$msg["collstate_form_cote"]."</th>
			<th>".$msg["collstate_form_support"]."</th>
			<th>".$msg["collstate_form_statut"]."</th>		
			<th>".$msg["collstate_form_origine"]."</th>		
			<th>".$msg["collstate_form_collections"]."</th>
			<th>".$msg["collstate_form_archive"]."</th>
			<th>".$msg["collstate_form_lacune"]."</th>		
		</tr>
		!!collstate_liste!!
	</tbody>	
</table>
";

$tpl_collstate_surloc_liste = "<th>".$msg["collstate_form_surloc"]."</th>";

$tpl_collstate_liste_line[1]="
<tr class='!!pair_impair!!' !!tr_surbrillance!! >
	<!-- surloc -->
	<td !!tr_javascript!! >!!localisation!!</td>
	<td !!tr_javascript!! >!!emplacement_libelle!!</td>
	<td !!tr_javascript!! >!!cote!!</td>
	<td !!tr_javascript!! >!!type_libelle!!</td>	
	<td !!tr_javascript!! >!!statut_libelle!!</td>
	<td !!tr_javascript!! >!!origine!!</td>
	<td !!tr_javascript!! >!!state_collections!!</td>
	<td !!tr_javascript!! >!!archive!!</td>
	<td !!tr_javascript!! >!!lacune!!</td>
</tr>";

$tpl_collstate_surloc_liste_line = "<td !!tr_javascript!! >!!surloc!!</td>";
