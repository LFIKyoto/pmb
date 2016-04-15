<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_indexint.tpl.php,v 1.2 2012-08-14 09:33:36 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur indexint

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
if ($nb_per_page_s_select != '') {
	$nb_per_page = $nb_per_page_s_select ;
} else {
	$nb_per_page = 10;	
}

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_indexint' class='etiquette'>".htmlentities($msg['indexint_select'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value) {
	try {
		window.opener.document.forms[f_caller].elements['$param1'].value = id_value;
	} catch(err) {}
	try {
		window.opener.document.forms[f_caller].elements['$param2'].value = reverse_html_entities(libelle_value);
	} catch(err) {}  
	window.close();
}
-->
</script>
";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
	!!pclassement!!
	<input type='text' name='f_user_input' value=\"!!deb_rech!!\" />&nbsp;
	<input type='submit' class='bouton_small' value='$msg[142]' />
	<br />
	<input type='radio' name='exact' id='exact1' value='1' !!check1!! />
	<label class='etiquette' for='exact1'>".htmlentities($msg['indexint_search_index'],ENT_QUOTES,$charset)."</label>&nbsp;
	<input type='radio' name='exact' id='exact0' value='0' !!check0!! />
	<label for='exact0' class='etiquette'>".htmlentities($msg['indexint_search_comment'],ENT_QUOTES,$charset)."</label>
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
