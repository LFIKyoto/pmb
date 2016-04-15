<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_item.tpl.php,v 1.1 2014-10-08 14:13:19 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl;
$ontology_tpl['form_body'] = '
<script type="text/javascript" src="./javascript/ajax.js"></script>
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites" onSubmit="return false;">
	<input type="hidden" name="item_uri" value="!!uri!!"/>	
	<h3>!!onto_form_title!!</h3>
	<div id="form-contenu">
		<div class="row">&nbsp;</div>
		!!onto_form_content!!
		<div class="row">&nbsp;</div>
		<!-- aut_link -->
	</div>
	<div class="row">&nbsp;</div>
	<div class="left">
		!!onto_form_history!!
		&nbsp;
		!!onto_form_submit!!
	</div>
	<div class="right">
		!!onto_form_delete!!
	</div>
	<div class="row"></div>
</form>
!!onto_form_scripts!!
';