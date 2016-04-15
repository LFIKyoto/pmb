<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.tpl.php,v 1.9 2015-03-04 14:26:24 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_tpl['form_row'] = '
<div id="!!onto_row_id!!">
	<div class="row"><label class="etiquette" for="!!onto_row_id!!">!!onto_row_label!!</label></div>
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
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_add_ressource_selector']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_selector(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * Text
 */
$ontology_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">!!onto_row_content_text_value!!</textarea>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';


/*
 * Small text
 */
$ontology_tpl['form_row_content_small_text']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Small text card
 */
$ontology_tpl['form_row_card'] = '
<div id="!!onto_row_id!!">
	<div class="row"><label class="etiquette" for="!!onto_row_id!!">!!onto_row_label!!</label></div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!">
	<input type="hidden" id="!!onto_row_id!!_available_lang" value=\'!!tab_available_lang!!\'>
	<input type="hidden" id="!!onto_row_id!!_lang_label" value=\'!!tab_lang_label!!\'>
	<div class="row" id="!!onto_row_id!!_combobox_lang" >!!onto_row_combobox_lang!! !!input_add!!</div>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_small_text_card']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!label_lang!!
<input type="hidden" value="!!onto_row_content_small_text_lang!!" name="!!onto_row_id!![!!onto_row_order!!][lang]" id="!!onto_row_id!!_!!onto_row_order!!_lang" />
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_del_card']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del_card" onclick="onto_del_card(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

$ontology_tpl['form_row_content_input_add_card']='
<input class="bouton_small" id="!!onto_row_id!!_add_card" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_card(\'!!onto_row_id!!\',!!onto_row_max_card!!);ajax_parse_dom();">
';

/*
 * Ressource selector
 */
$ontology_tpl['form_row_content_resource_selector']='
<input type="text" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" 
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"   
	completion="onto" 
	autexclude="!!onto_current_element!!" 
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_sel']='
<input type="button" class="bouton_small" onclick="onto_open_selector(\'!!onto_row_id!!\',\'!!property_name!!\');" value="'.$msg['ontology_p_sel_button'].'" id="!!onto_row_id!!_sel" />
';

$ontology_tpl['form_row_content_input_remove']='
<input type="button" id="!!onto_row_id!!_!!onto_row_order!!_del" value="'.$msg['ontology_p_del_button'].'" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';
