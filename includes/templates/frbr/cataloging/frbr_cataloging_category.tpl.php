<?php

$frbr_cataloging_category_form_tpl = '
<form data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>	
	<div class="form-contenu">
		<input type="hidden" name="id" id="id" value=""/>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_category_parent']).'</label>
		</div>
		<div class="row">
			<select  id="category_parent" name="category_parent" data-dojo-type="dijit/form/Select" style="width:auto"></select>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_label']).'</label>
		</div>	
		<div class="row">		
			<input type="text" id="category_title" name="category_title" required="true" data-dojo-type="dijit/form/ValidationTextBox"/>
		</div>
		<div class="row"></div>
	</div>
	<div class="row">	
		<div class="left">
			<button data-dojo-type="dijit/form/Button" id="category_button_save" type="submit">'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_save']).'</button>
		</div>
		<div class="right">
			<button data-dojo-type="dijit/form/Button" id="category_button_delete" type="button">'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_delete']).'</button>
		</div>
	</div>	
	<div class="row"></div>		
</form>
';