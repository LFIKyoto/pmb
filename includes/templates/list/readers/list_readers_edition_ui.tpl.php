<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_edition_ui.tpl.php,v 1.1 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_readers_ui_search_filters_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['editions_filter_empr_location']."</label>
		</div>
		<div class='row'>
			!!locations!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['statut_empr']."</label>
		</div>
		<div class='row'>
			!!status!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['editions_filter_empr_categ']."</label>
		</div>
		<div class='row'>
			!!categories!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['editions_filter_empr_codestat']."</label>
		</div>
		<div class='row'>
			!!codestat!!
		</div>
	</div>
</div>
";
