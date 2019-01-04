<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.tpl.php,v 1.17 2019-01-02 14:19:42 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_tpl['form_row'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row">	
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
		!!form_row_content_comment!! !!form_row_content_tooltip!!
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!onto_inside_row!!
	!!onto_row_inputs!!
</div>
';

$ontology_tpl['form_row_content_input_add']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\'">'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_add_merge_property']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_merge_property" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\'">'.$msg['ontology_p_add_button'].'</button>
<script type="text/javascript">
	var !!onto_row_id!!_template = "!!merge_properties_template!!";
</script>
';

$ontology_tpl['form_row_content_input_add_resource_selector']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_resource_selector" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_del']='
<button type="button" data-dojo-type="dijit/form/Button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">'.$msg['ontology_p_del_button'].'</button>
';

// Info-bulle
$ontology_tpl['form_row_content_tooltip'] = '
<i id="!!onto_row_id!!_tooltip" class="contribution_tooltip fa fa-info-circle"  style="cursor:help;" aria-hidden="true"></i>
<div data-dojo-type="dijit/Tooltip" data-dojo-props="connectId:\'!!onto_row_id!!_tooltip\',position:[\'above\']">
	!!form_row_content_tooltip_content!!
</div>
';

// Champ obligatoire
$ontology_tpl['form_row_content_mandatory_sign'] = '
<span class="contribution_mandatory_fields" title="'.$msg['onto_contribution_mandatory_field'].'">*</span>';

/*
 * Text
 */
$ontology_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" data-dojo-type="apps/pmb/PMBTextArea"  data-dojo-props="!!onto_input_props!!">!!onto_row_content_text_value!!</textarea>
';


/*
 * Small text
 */
$ontology_tpl['form_row_content_small_text']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" data-dojo-type="dijit/form/TextBox" data-dojo-props="!!onto_input_props!!"/>
';

/*
 * Small text card
 */
$ontology_tpl['form_row_card'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row">
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
		!!form_row_content_comment!! !!form_row_content_tooltip!!
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!" data-dojo-type="dijit/form/TextBox"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!" data-dojo-type="dijit/form/TextBox"/>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_small_text_card']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"  data-dojo-type="dijit/form/TextBox" data-dojo-props="!!onto_input_props!!"/>
';

$ontology_tpl['form_row_content_input_add_card']='
<input class="bouton_small" id="!!onto_row_id!!_add_card" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_card(\'!!onto_row_id!!\',!!onto_row_max_card!!);ajax_parse_dom();">
';

/*
 * Ressource selector
 */
$ontology_tpl['form_row_content_resource_selector']='
<input type="text" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list" data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" data-dojo-props="name:\'!!onto_row_id!![!!onto_row_order!!][display_label]\', id:\'!!onto_row_id!!_!!onto_row_order!!_display_label\', completion:\'!!onto_completion!!\', autexclude:\'!!onto_current_element!!\', param1:\'!!onto_equation_query!!\', param2:\'!!onto_area_id!!\', value:\'!!form_row_content_resource_selector_display_label!!\', valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\', templateNodeId:\'!!onto_row_id!!_!!onto_row_order!!_resource_template\'" !!onto_disabled!! autocomplete="off"/>
<datalist id="!!onto_row_id!!_!!onto_row_order!!_display_label_list">
</datalist>
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
';

/**
 * Linked forms
 */
//$ontology_tpl['form_row_content_linked_form']='
//<div data-dojo-type="dijit/form/DropDownButton">
//		<div data-dojo-type="dijit/TooltipDialog">
//			!!linked_forms!!
//		</div>
//</div>
//';

//$ontology_tpl['form_row_content_linked_form_button']='
//<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" data-form_url="!!url_linked_form!!" id="!!onto_row_id!!_!!linked_form_id!!_sel" data-form_title="!!linked_form_title!!" >!!linked_form_title!!</button>
//';

$ontology_tpl['form_row_content_linked_form']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" data-form_url="!!url_linked_form!!" id="!!onto_row_id!!_sel" data-form_title="!!linked_form_title!!">'.$msg['ontology_p_sel_button'].'</button>
';

$ontology_tpl['form_row_content_input_remove']='
<input type="button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton" value="'.$msg['ontology_p_del_button'].'"/>
';

/*
 * checkbox
 */
$ontology_tpl['form_row_content_checkbox']='
<input type="checkbox" class="saisie-80em" !!onto_row_content_checkbox_checked!! value="1" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" data-dojo-type="dijit/form/CheckBox" data-dojo-props="!!onto_input_props!!"/>
';

/*
 * date & dojo widget en g�n�ral (supp & add) 
*/
$ontology_tpl['form_row_content_date']='
<input type="text" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]" value="!!onto_date!!" data-dojo-type="dijit/form/DateTextBox" data-dojo-props="!!onto_input_props!!"/>';

$ontology_tpl['form_row_content_widget_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_dojo_element(\'!!onto_row_id!!\',0);">
';

/**
 * Bouton suppression widget dojo
 */
$ontology_tpl['form_row_content_widget_del']='
<button type="button" data-dojo-type="dijit/form/Button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_dojo_element(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">'.$msg['ontology_p_del_button'].'</button>
';

/** 
 * Repr�sentation d'un entier 
 */
$ontology_tpl['form_row_content_integer']='
<input type="text" class="saisie-80em" value="!!onto_row_content_integer_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" data-dojo-type="dijit/form/NumberTextBox" data-dojo-props="!!onto_input_props!!"/>
';

/**
 * Repr�sentation d'un marclist
 */
$ontology_tpl['form_row_content_marclist']='
<select
	name="!!onto_row_id!![!!onto_row_order!!][value]" 
	id="!!onto_row_id!!_!!onto_row_order!!_value" 
	data-dojo-type="dijit/form/Select" 
	data-dojo-props="options : [!!onto_row_content_marclist_options!!], !!onto_disabled!!"
/>';

/*
 * Liste
 */
$ontology_tpl['form_row_content_list']='
<select 
	name="!!onto_row_id!![!!onto_row_order!!][value][]"
	id="!!onto_row_id!!_!!onto_row_order!!_value"
	!!onto_disabled!!"
>
	!!onto_row_content_list_options!!
</select>
';

/*
 * Liste mutliple
 */
$ontology_tpl['form_row_content_list_multi']='
<select 
	name="!!onto_row_id!![!!onto_row_order!!][value][]" 
	id="!!onto_row_id!!_!!onto_row_order!!_value"
	multiple="yes" 
	!!onto_disabled!!
>
	!!onto_row_content_list_options!!
</select>
';

/*
 * Hidden field
 */
$ontology_tpl['form_row_hidden'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!" />
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

//pour le s�lecteur de ressource, on a besoin du champ display_label
$ontology_tpl['form_row_content_resource_selector_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	<input type="hidden" value="!!onto_row_content_hidden_display_label!!" name="!!onto_row_id!![!!onto_row_order!!][display_label]" id="!!onto_row_id!!_!!onto_row_order!!_display_label"/>
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

//Pour le champ cach� de type liste, on a besoin d'un tableau de values 
$ontology_tpl['form_row_content_list_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!form_row_content_list_item_hidden!!
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
	<input type="hidden" value="!!form_row_content_list_hidden_values!!" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
</div>
';

$ontology_tpl['form_row_content_list_item_hidden']='
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value][]"/>
';

/*
 *	File 
 */
$ontology_tpl['form_row_content_file']='
!!onto_contribution_last_file!!
<input type="file"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][default_value]" id="!!onto_row_id!!_!!onto_row_order!!_default_value"/>
<script type="text/javascript">
		var form = document.forms["!!onto_form_name!!"];
		if (form.getAttribute("enctype") != "multipart/form-data") {
			form.setAttribute("enctype","multipart/form-data");
		}
</script> 
';
/**
 * last file
 */
$ontology_tpl['form_row_content_last_file']='
<label>'.$msg["onto_contribution_last_file"].' : <em>!!onto_row_content_file_value!!</em></label>
<br/>
';

/*
* Merge properties
*/
$ontology_tpl['form_row_merge_properties'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row">
		<label class="etiquette">!!onto_row_label!!</label>
		<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	</div>
	!!onto_rows!!
</div>
';


/**
 * champ cach� pour le type
 */
$ontology_tpl['form_row_content_type'] = '
	<input type="hidden" value="!!onto_row_content_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Responsability selector
 */
$ontology_tpl['form_row_content_responsability_selector']='
<input type="text" value="!!form_row_content_responsability_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_responsability_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_responsability_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * linked record selector
 */
$ontology_tpl['form_row_content_linked_record_selector']='
<input type="text" value="!!form_row_content_linked_record_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
!!onto_row_content_linked_record_selector!!
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_linked_record_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_record_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * linked authority selector
 */
$ontology_tpl['form_row_content_linked_authority_selector']='
<input type="text" value="!!form_row_content_linked_authority_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_linked_authority_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_authority_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * resource template
 */
$ontology_tpl['form_row_content_resource_template'] = '
<div id="!!onto_row_id!!_!!onto_row_order!!_resource_template"></div>
';