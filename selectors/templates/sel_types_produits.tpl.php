<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_types_produits.tpl.php,v 1.6 2013-12-11 16:10:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur adresses

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
// nombre de références par pages
$nb_per_page = $nb_per_page_select;

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label class='etiquette'>".htmlentities($msg['acquisition_sel_type'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript' src='./javascript/actes.js'></script>
<script type='text/javascript'>
<!--
function set_parent(f_caller, typ, lib_typ, rem, tva)
{
	window.opener.document.forms[f_caller].elements['$param1'].value = typ;
	window.opener.document.forms[f_caller].elements['$param2'].value = reverse_html_entities(lib_typ);
	window.opener.document.forms[f_caller].elements['$param3'].value = reverse_html_entities(rem);";
if ($acquisition_gestion_tva) {
	$jscript.= "window.opener.document.forms[f_caller].elements['$param4'].value = reverse_html_entities(tva);";
}
if ($acquisition_gestion_tva == 1) {
	$jscript.= "window.opener.document.getElementById('convert_ht_ttc_".$param5."').innerHTML=ht_to_ttc(window.opener.document.forms[f_caller].elements['prix[$param5]'].value,window.opener.document.forms[f_caller].elements['$param4'].value);";
} else if ($acquisition_gestion_tva == 2) {
 	$jscript.= "window.opener.document.getElementById('convert_ht_ttc_".$param5."').innerHTML=ttc_to_ht(window.opener.document.forms[f_caller].elements['prix[$param5]'].value,window.opener.document.forms[f_caller].elements['$param4'].value);";
}
$jscript.= "window.close();
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
