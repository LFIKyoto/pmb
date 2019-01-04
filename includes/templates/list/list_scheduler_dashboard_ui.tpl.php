<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scheduler_dashboard_ui.tpl.php,v 1.1 2018-11-08 13:01:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_scheduler_dashboard_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["scheduler_types"]."</label>
		</div>
		<div class='row'>
			!!types_selector!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["scheduler_labels"]."</label>
		</div>
		<div class='row'>
			!!labels_selector!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["scheduler_states"]."</label>
		</div>
		<div class='row'>
			!!states_selector!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['scheduler_dates']."</label>
		</div>
		<div class='row'>
			<input type='text' name='!!objects_type!!_date_start' id='!!objects_type!!_date_start' value='!!date_start!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
			 - <input type='text' name='!!objects_type!!_date_end' id='!!objects_type!!_date_end' value='!!date_end!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
		</div>
	</div>
</div>
";