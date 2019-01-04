<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_records_bulletins_collstate_edition_ui.tpl.php,v 1.1 2018-12-28 13:15:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_bannettes_ui_search_content_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------

$list_records_bulletins_collstate_edition_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette' for='!!objects_type!!_user_query'>".$msg['1914']."</label>
		</div>
		<div class='row'>
			<input class='saisie-80em' id='!!objects_type!!_user_query' type='text' name='!!objects_type!!_user_query' value=\"!!user_query!!\" />
		</div>
	</div>
</div>
";