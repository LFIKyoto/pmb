<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_ui.tpl.php,v 1.1 2018-04-24 12:49:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $list_custom_fields_ui_search_filters_form_tpl;

$list_custom_fields_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["parperso_input_type"]."</label>
		</div>
		<div class='row'>
			!!input_type!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["parperso_data_type"]."</label>
		</div>
		<div class='row'>
			!!data_type!!
		</div>
	</div>
</div>
";