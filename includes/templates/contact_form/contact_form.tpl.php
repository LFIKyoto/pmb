<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.tpl.php,v 1.2.6.1 2019-10-09 08:18:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $contact_form_object_form_tpl, $msg, $current_module;

$contact_form_object_form_tpl="
<script type='text/javascript'>

	function test_form(form){
		if((form.label.value.length == 0) )		{
			alert('".$msg["admin_opac_contact_form_object_form_label_error"]."');
			return false;
		}
		return true;
	}

</script>
<form class='form-".$current_module."' id='contact_form_object_form' name='contact_form_object_form'  method='post' action=\"./admin.php?categ=contact_form&sub=objects&action=save&id=!!id!!\" >
	<h3>!!title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='object_label'>".$msg['admin_opac_contact_form_object_label']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='object_label' id='object_label' data-translation-fieldname='object_label' value='!!label!!' />
		</div>
        <div class='row'>
			<label class='etiquette' for='f_message'>".$msg["admin_opac_contact_form_object_message"]."</label>
		</div>	
        <div class='row'>
			<textarea id='object_message' name='object_message' cols='100' rows='20'>!!message!!</textarea>
		</div>
		<div class='row'>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_opac_contact_form_object_form_cancel']."'  onclick=\"document.location='./admin.php?categ=contact_form&sub=objects'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_opac_contact_form_object_form_save']."' onclick=\"if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['contact_form_object_form'].elements['object_label'].focus();
</script>
";