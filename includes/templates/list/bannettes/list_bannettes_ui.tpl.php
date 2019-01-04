<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_ui.tpl.php,v 1.1 2018-12-27 10:32:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_bannettes_ui_search_content_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------

$list_bannettes_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette' for='!!objects_type!!_name'>".$msg['dsi_ban_search_nom']."</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' id='!!objects_type!!_name' type='text' name='!!objects_type!!_name' value=\"!!name!!\" title='$msg[3000]' />
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette' for='form_classement'>".$msg['dsi_classement']."</label>
		</div>
		<div class='row'>
			!!classement!!
		</div>
	</div>
</div>
";