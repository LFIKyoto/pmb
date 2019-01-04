<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_commande.tpl.php,v 1.4 2017-10-13 10:21:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label class='etiquette'>".$msg['selector_commande']."</label>
	</div>
<div class='row'>
";

$sel_header_add = "
<div id='att'></div>
<div class='row'>
	<label class='etiquette'>".$msg['acquisition_cde_cre']."</label>
	</div>
<div class='row'>
";
//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(id_value, libelle_value, callback)
{
	//window.parent.document.forms[f_caller].elements['".$param1."'].value = id_value;
	//window.parent.document.forms[f_caller].elements['".$param2."'].value = reverse_html_entities(libelle_value);
	if (callback) {
		window.parent[callback](id_value);
	}
	closeCurrentEnv();
}
-->
</script>
";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
	<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
	&nbsp;
	<input type='submit' class='bouton_small' value='$msg[142]' />
	<!-- sel_bibli -->
	!!bouton_ajouter!!
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
<hr />
";

$commande_form = "
<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
	function test_form(form) {
		if(form.raison.value.length == 0) {
			alert(\"".$msg['acquisition_raison_soc_vide']."\");
			return false;
		}
		return true;
	}
</script>
<form name='saisie_commande' method='post' action=\"$base_url&action=update\">
	<h3>".$msg['acquisition_ajout_commande']."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!sel_exercice!!
		</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou2'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>			
			<input type='text' id='lib_fou' name='lib_fou' tabindex='1' value='' completion='fournisseurs' param1='!!id_bibli!!' autfield='id_fou' autocomplete='off' class='saisie-30emr' />
			<input type='button' class='bouton_small' style='width:20px;' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=saisie_commande&param1=id_fou&param2=lib_fou&param3=adr_fou&id_bibli=!!id_bibli!!&deb_rech='+".pmb_escape()."(this.form.lib_fou.value), 'selector'); \" />
			<input type='hidden' id='id_fou' name='id_fou' value='' />
		</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg['38'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>	
			<input type='text' class='saisie-10em' value='' name='num_cde' id='num_cde' />
		</div>
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg['acquisition_cde_nom'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>	
			<input type='text' class='saisie-50em' value='' name='nom_acte' id='nom_acte' />
		</div>	
	</div>
	<div class='row'>
		<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='$base_url';\">
		<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
