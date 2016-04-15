<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_rubriques.tpl.php,v 1.7 2015-01-23 09:42:50 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur adresses

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
// nombre de références par pages
if(!isset($nb_per_page)){
	$nb_per_page = 10;
}

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label class='etiquette'>".htmlentities($msg['acquisition_sel_rub'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'></div>
";

//-------------------------------------------
//	$sel_search : search
//-------------------------------------------
$sel_search="<div class='row'>
	<form class='form-$current_module' id='form_query' name='form_query' method='post' action='!!action_url!!' onSubmit='return test_form(this)'>
		<div class='row' >
			<input type='text' id='elt_query' name='elt_query' value='!!elt_query!!' class='saisie-30em'/>
			<input type='button' class='bouton_small' value='X' onclick=\"document.forms['form_query'].elt_query.value=''; return false;\"/>
			<input type='submit' class='bouton_small' value='$msg[142]' />
		</div>
	</form>
</div>
<script type='text/javascript'>

	function test_form(form) {
		if (form.elt_query.value.length == 0) {
			form.elt_query.value='*';
			return true;
		}
		return true;
	}
	document.forms['form_query'].elements['elt_query'].focus();
</script>";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, rub, lib_rub)
{
	window.opener.document.forms[f_caller].elements['$param1'].value = rub;
	window.opener.document.forms[f_caller].elements['$param2'].value = reverse_html_entities(lib_rub);
	window.close();
}
-->
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
