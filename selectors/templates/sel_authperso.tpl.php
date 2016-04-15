<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_authperso.tpl.php,v 1.2 2014-10-31 15:33:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur éditeur

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
// nombre de références par pages
if ($nb_per_page_p_select != "") 
	$nb_per_page = $nb_per_page_p_select ;
	else $nb_per_page = 10;

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_authperso' class='etiquette'>".$msg["authperso_sel_title"]."</label>
	</div>	
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
/* pour $dyn=3, renseigner les champs suivants: (passé dans l'url)
 *
* $max_field : nombre de champs existant
* $field_id : id de la clé
* $field_name_id : id  du champ text
* $add_field : nom de la fonction permettant de rajouter un champ
*
*/
if ($dyn==3) {
	$jscript ="
<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value){	
	
		var w=window;
		
		var n_auth=w.opener.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		//Vérification pas déjà sélectionnée
		for (var i=0; i<n_auth; i++) {
			if (w.opener.document.getElementById('$p1'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
	
		if (flag) {
			for (i=0; i<n_auth; i++) {
				if ((w.opener.document.getElementById('$p1'+i).value==0)||(w.opener.document.getElementById('$p1'+i).value=='')||(w.opener.document.getElementById('$p1'+i).value=='0')){
					break;
				}	
			}
			if (i==n_auth) w.opener.add_authperso('$p3');
			w.opener.document.getElementById('$p1'+i).value = id_value;
			w.opener.document.getElementById('$p2'+i).value = reverse_html_entities(libelle_value);
		}
	
	}
</script>";
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value)
	{	
		w=window;
		n_aut_link=w.opener.document.forms[f_caller].elements['max_aut_link'].value;
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		for (i=0; i<n_aut_link; i++) {
			if (w.opener.document.getElementById('f_aut_link_id'+i).value==id_value && w.opener.document.getElementById('f_aut_link_table'+i).value==$param1) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}	
		if (flag) {
			for (i=0; i<n_aut_link; i++) {
				if ((w.opener.document.getElementById('f_aut_link_id'+i).value==0)||(w.opener.document.getElementById('f_aut_link_id'+i).value=='')) break;
			}	
			if (i==n_aut_link) w.opener.add_aut_link();
			
			var selObj = w.opener.document.getElementById('f_aut_link_table_list');
			var selIndex=selObj.selectedIndex;
			w.opener.document.getElementById('f_aut_link_table'+i).value= selObj.options[selIndex].value;
			
			w.opener.document.getElementById('f_aut_link_id'+i).value = id_value;
			w.opener.document.getElementById('f_aut_link_libelle'+i).value = reverse_html_entities('['+selObj.options[selIndex].text+']'+libelle_value);		
		}	
	}
	-->
	</script>
	";
}else 
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback)
{
	window.opener.document.forms[f_caller].elements['$p1'].value = id_value;
	window.opener.document.forms[f_caller].elements['$p2'].value = reverse_html_entities(libelle_value);".
	($p3 ? "window.opener.document.forms[f_caller].elements['$p3'].value = '0';" : "").
	($p4 ? "window.opener.document.forms[f_caller].elements['$p4'].value = '';" : "").
	($p5 ? "window.opener.document.forms[f_caller].elements['$p5'].value = '0';" : "").
	($p6 ? "window.opener.document.forms[f_caller].elements['$p6'].value = '';" : "")."
	if(callback)
		window.opener[callback]('$infield');
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
<input type='text' name='f_user_input' value=\"!!deb_rech!!\" />&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' />&nbsp;
!!bouton_ajouter!!
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
";

// ------------------------------------------
// 	$authperso_form : form saisie
// ------------------------------------------
$authperso_form_all = "
<script type='text/javascript'>
<!--
	function test_form(form)
	{

		return true;
	}
-->
</script>
<form name='saisie_authperso' method='post' action=\"$base_url&action=update\">
<!-- ajouter une authperso -->
<h3>$msg[143]</h3>
<div class='form-contenu'>

<div class='row'>
	<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='$base_url';\">
	<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
