<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_ui.tpl.php,v 1.10 2015-06-12 09:36:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['scheme_list_selector']='
	<select !!scheme_list_selector_onchange!! name="!!scheme_list_selector_name!!" id="!!scheme_list_selector_id!!" class="saisie-30em">
		!!scheme_list_selector_options!!
	</select>
';

$ontology_tpl['scheme_list_selector_option']='
	<option !!scheme_list_selector_options_selected!! value="!!scheme_list_selector_options_value!!">!!scheme_list_selector_options_label!!</option>
';



$ontology_tpl['skos_concept_list']='
<div class="row">
	<script type="javascript" src="./javascript/sorttable.js"></script>
	<table class="sorttable">
		<tr>
			<th>!!list_header!!</th>
			<th>!!list_header_utilisation!!</th>
		</tr>
		!!list_content!!
	</table>
	!!list_pagination!!
</div>
';

$ontology_tpl['skos_concept_list_line_doc']='
<tr>
	<td>
		<img hspace="3" border="0" src="'.$base_path.'/images/doc.gif">
		<a href="!!list_line_href!!">!!list_line_libelle!!</a>
	</td>
	<td onmousedown="document.location=\'!!list_line_nb_utilisations_href!!\'">
		!!list_line_nb_utilisations!!
	</td>
</tr>
';

$ontology_tpl['skos_concept_list_line_folder']='
<tr>
	<td>
		<a href="!!list_line_folder_href!!"><img hspace="3" border="0" src="'.$base_path.'/images/folderclosed.gif"></a>
		<a href="!!list_line_href!!">!!list_line_libelle!!</a>
	</td>
	<td onmousedown="document.location=\'!!list_line_nb_utilisations_href!!\'">
		!!list_line_nb_utilisations!!
	</td>
</tr>
';

$ontology_tpl['skos_concept_search_form']='
<form action="!!skos_concept_search_form_action!!" method="post" name="search" class="form-autorites">
	<h3>'.$msg['357'].' : !!skos_concept_search_form_title!!</h3>
	<div class="form-contenu">
		<div class="row">
			<div class="colonne">
				!!skos_concept_search_form_selector!!
				&nbsp;
				<input id="id_user_input" type="text" value="!!skos_concept_search_form_user_input!!" name="user_input" class="saisie-50em">
			</div>
		</div>
		<div class="row">
			<input type="checkbox" id="only_top_concepts" name="only_top_concepts" !!only_top_concepts_onchange!! !!only_top_concepts_checked!!>
			<label for="only_top_concepts">'.$msg['onto_skos_concept_only_top_concepts'].'</label>
		</div>
		<div class="row">
			<input type="submit" onclick="return test_form(this.form)" value="Rechercher" class="bouton">
			<input type="button" class="bouton" onclick="!!skos_concept_search_form_concept_onclick!!" value="!!skos_concept_search_form_concept_value!!"/>
			<input type="button" class="bouton" onclick="!!skos_concept_search_form_composed_onclick!!" value="'.$msg['onto_add_composed_concept'].'"/>
		</div>
	</div>
</form>
<script type="text/javascript">
	document.forms["search"].elements["user_input"].focus();
</script>
<div class="row"></div>
<div class="row">
	<a href="!!skos_concept_search_form_href!!"><img hspace="3" border="0" align="middle" src="./images/top.gif"></a>
	!!skos_concept_search_form_breadcrumb!!
	<hr>
</div>
';

$ontology_tpl['skos_concept_list_selector_line_folder']="
<tr>
	<td>
		<a href='!!folder_href!!'><img hspace='3' border='0' src='".$base_path."/images/folderclosed.gif'></a>
		<a href='#' onclick=\"set_parent('!!caller!!', '!!element!!', '!!uri!!', '!!item!!', '!!range!!', '!!callback!!')\" title=\"!!infobulle_libelle!!\">!!item_libelle!!</a>
	</td>
</tr>";
$ontology_tpl['skos_concept_list_selector_line_doc']="
<tr>
	<td>
		<img hspace='3' border='0' src='".$base_path."/images/doc.gif'>
		<a href='#' onclick=\"set_parent('!!caller!!', '!!element!!', '!!uri!!', '!!item!!', '!!range!!', '!!callback!!')\" title=\"!!infobulle_libelle!!\">!!item_libelle!!</a>
	</td>
</tr>";


$ontology_tpl['skos_concept_selector_search_form']="
<form name='search_form' method='post' action='!!base_url!!'>
	!!skos_concept_search_form_selector!!
<div class='row'>
	<input type='checkbox' id='only_top_concepts' name='only_top_concepts' !!only_top_concepts_onchange!! !!only_top_concepts_checked!!>
	<label for='only_top_concepts'>".$msg['onto_skos_concept_only_top_concepts']."</label>
</div>
	<input id='id_deb_rech' type='text' name='deb_rech' value=\"!!deb_rech!!\">
	&nbsp;
	<input type='submit' class='bouton_small' value='$msg[142]' />&nbsp;<input type='button' class='bouton' value='!!add_button_label!!' onclick='!!add_button_onclick!!'/>
</form>
<script type='text/javascript'>
	if(document.forms['search_form'].elements['deb_rech']){
		document.forms['search_form'].elements['deb_rech'].focus()
	}
	
</script>
<div class='row'></div>
	<div class='row'>
		<a href='!!skos_concept_search_form_href!!'><img hspace='3' border='0' align='middle' src='./images/top.gif'></a>
		!!skos_concept_selector_breadcrumb!!
	</div>
</div>	
<hr />";