<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk.tpl.php,v 1.4 2014-01-07 15:44:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$cashdesk_form="

<script type='text/javascript'>
<!--
	function test_form(form) {
		
		if((form.f_name.value.replace(/^\s+|\s+$/g,'').length == 0) ) {
			alert(\"".$msg["cashdesk_form_name_no"]."\");
			return false;
		}
		return true;
	}

-->
</script>
<form class='form-$current_module' name='caisse' method='post' action='!!action!!' >
<h3>!!titre!!</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='f_name'>".$msg["cashdesk_form_name"]."</label>
		<div class='row'>
			<input type='text' class='saisie-50em' id=\"f_name\" value='!!name!!' name='f_name'  />				
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='f_ex_section'>".$msg["cashdesk_form_affectation"]."</label>
		<div class='row'>
			!!location_section!!
		</div>
	</div>
	<div class='row'>
		<label class='etiquette' for='form_type'>".$msg["cashdesk_autorisations_transaction"]."</label>
		<input type='button' class='bouton_small' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_transactypes\").value,1);' align='middle'>
		<input type='button' class='bouton_small' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_transactypes\").value,0);' align='middle'>
	</div>
	<div class='row'>
		!!transactypes!!
	</div>
	<div class='row'>
		<label class='etiquette' for='form_type'>".$msg["cashdesk_autorisations"]."</label>
		<input type='button' class='bouton_small' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);' align='middle'>
		<input type='button' class='bouton_small' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);' align='middle'>
	</div>
	<div class='row'>
		!!autorisations_users!!
	</div>	
	<div class='row'>
		<input type='checkbox' !!cashbox_checked!! class='checkbox' id=\"f_cashbox\" value='1' name='f_cashbox'  />	<label class='etiquette' for='f_cashbox'>".$msg["cashdesk_cashbox"]."</label>			
	</div>
		
	<div class='row'></div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value=' $msg[76] ' onClick=\"history.go(-1);\" />
		<input type='submit' class='bouton' value=' ".$msg["cashdesk_form_save"]." ' onclick=\"return test_form(this.form)\" />
		</div>
	<div class='right'>
		!!supprimer!!
		</div>
	</div>
<div class='row'></div>
</form>		
<script>document.forms['caisse'].elements['f_name'].focus();</script>
";




$cashdesk_list_form="
<table>
	<tr>
		<th>".$msg["cashdesk_list_libelle"]."</th>		
	</tr>
	!!cashdesk_list!!
</table>
<input class='bouton' type='button' value=\" ".$msg["cashdesk_list_add"]." \" onClick=\"document.location='./admin.php?categ=finance&sub=cashdesk&action=edit'\" />
";
$cashdesk_list_form_summarize_table="
	<table class='sortable'>
		<tr>
			<th>".$msg["cashdesk_edition_name"]."</th>
			<th>".$msg["cashdesk_edition_transac_name"]."</th>
			<th>".$msg["cashdesk_edition_transac_unit_price"]."</th>
			<th>".$msg["cashdesk_edition_transac_montant"]."</th>
			<th>".$msg["cashdesk_edition_transac_realisee_no"]." (!!realisee_no!!)</th>
			<th>".$msg["cashdesk_edition_transac_realisee"]." (!!realisee!!)</th>
			<th>".$msg["cashdesk_edition_transac_encaissement_no"]." (!!encaissement_no!!)</th>
			<th>".$msg["cashdesk_edition_transac_encaissement"]." (!!encaissement!!)</th>
		</tr>
		!!cashdesk_list!!
	</table>
";
$cashdesk_list_form_summarize="
<script type='text/javascript'>
	function cashdesk_filter() {
	
	}
</script>
<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='./edit.php?categ=empr&sub=cashdesk' method=post>
	<input type='hidden' name='dest' value='' />
	<div class='left'>
		".$msg["cashdesk_edition_filter"]."!!cashdesk_filter!!&nbsp;
		".$msg["cashdesk_edition_transaction_filter"]."!!transaction_filter!!
		".$msg["cashdesk_edition_start_date"]."
		<input type='hidden' value='!!start_date!!' name='start_date'>
		<input type='text' placeholder='JJ/MM/AAAA' value='!!field_start_date!!' name='field_start_date'>
		<input class='bouton_small' type='button' onclick=\"openPopUp('./select.php?what=calendrier&caller=form-$current_module-list&date_caller=&param1=start_date&param2=field_start_date&auto_submit=NO&date_anterieure=YES&format_return=IN', 'start_date', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\" value='Calendrier' name='start_date_lib_bouton'>

		".$msg["cashdesk_edition_stop_date"]."
		<input type='hidden' value='!!stop_date!!' name='stop_date'>
		<input type='text' placeholder='JJ/MM/AAAA' value='!!field_stop_date!!' name='field_stop_date'>
		<input class='bouton_small' type='button' onclick=\"openPopUp('./select.php?what=calendrier&caller=form-$current_module-list&date_caller=&param1=stop_date&param2=field_stop_date&auto_submit=NO&date_anterieure=YES&format_return=IN', 'stop_date', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\" value='Calendrier' name='stop_date_lib_bouton'>
		
		<input type='submit' class='bouton' value='".$msg["actualiser"]."' onclick=\"document.forms['form-$current_module-list'].dest.value ='';\"/>
	</div>
	<div class='right'>
		<img  src='./images/tableur.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export tableau EXCEL'/>&nbsp;&nbsp;
		<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
	</div><div class='row'></div>
</form>		 	
<script type='text/javascript'>
	function survol(obj){
		obj.style.cursor = 'pointer';
	}
	function start_export(type){
		document.forms['form-$current_module-list'].dest.value = type;
		document.forms['form-$current_module-list'].submit();
		
	}	
</script>
<script type='text/javascript' src='./javascript/sorttable.js'></script>
$cashdesk_list_form_summarize_table
";
