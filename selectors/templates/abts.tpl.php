<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts.tpl.php,v 1.2 2014-10-15 12:42:25 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur titre de série

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
// nombre de références par pages
if ($nb_per_page_s_select != "") 
	$nb_per_page = $nb_per_page_s_select ;
	else $nb_per_page = 10;

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_abt' class='etiquette'>".$msg["abts_sel_title"]."</label>
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
if ($dyn==1) {
$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value,callback, flag_circlist_info)	{		
		if(callback)
			window.opener[callback](id_value,libelle_value,flag_circlist_info);
		window.close();
	}
	-->
	</script>
";
}
//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">&nbsp;
<input type='submit' class='bouton_small' value='".$msg["abts_sel_search_button"]."' />&nbsp;

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
