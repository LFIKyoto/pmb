<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_campaigns_ui.tpl.php,v 1.3 2018-04-27 12:36:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_campaigns_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["campaigns_types"]."</label>
		</div>
		<div class='row'>
			!!types_selector!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["campaigns_labels"]."</label>
		</div>
		<div class='row'>
			!!labels_selector!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<label class='etiquette'>".$msg["campaigns_descriptors"]."</label>
		<br />
		!!descriptors_selector!!
	</div>
	<div class='colonne3'>
		<label class='etiquette'>".$msg["campaigns_tags"]."</label>
		<br />
		!!tags_selector!!
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['campaigns_dates']."</label>
		</div>
		<div class='row'>
			<input type='text' name='!!objects_type!!_date_start' id='!!objects_type!!_date_start' value='!!date_start!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
			 - <input type='text' name='!!objects_type!!_date_end' id='!!objects_type!!_date_end' value='!!date_end!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
		</div>
	</div>
</div>
";