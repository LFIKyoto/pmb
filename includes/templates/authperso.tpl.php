<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.tpl.php,v 1.7 2014-11-04 13:19:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$authperso_list_tpl="	
<h1>".htmlentities($msg["admin_authperso"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_authperso_name"], ENT_QUOTES, $charset)."			
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_action"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_authperso_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form'\" />	
";

$authperso_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!name!!
	</td> 
	<td valign='top'>				
		<input type='button' class='bouton' value='".$msg['admin_authperso_edition']."'  onclick=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=!!id!!'\"  />
	</td> 		
	
</tr> 	
";

$authperso_form_tpl="		
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_authperso_name_error"]."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='authperso' name='authperso'  method='post' action=\"admin.php?categ=authorities&sub=authperso\" >

	<input type='hidden' name='auth_action' id='auth_action' />
	<input type='hidden' name='id_authperso' id='id_authperso' value='!!id_authperso!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_authperso_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>				
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg['admin_authperso_form_comment']."</label>
		</div>
		<div class='row'>
			<textarea type='text' name='comment' id='comment' class='saisie-50em' rows='4' cols='50' >!!comment!!</textarea>
		</div>
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_authperso_save']."' onclick=\"document.getElementById('auth_action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_authperso_exit']."'  onclick=\"document.location='./admin.php?categ=authorities&sub=authperso'\"  />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
";

$authperso_form_edition_tpl="		
<script type='text/javascript'>
	function test_form(form){
		
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='authperso_edition' name='authperso_edition'  method='post' action=\"admin.php?categ=authorities&sub=authperso\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id_authperso' id='id_authperso' value='!!id_authperso!!'/>
	<div class='form-contenu'>	
		<div class='row'>
			<label class='etiquette' for='authperso_tpl_form'>".$msg['admin_authperso_tpl_form']."</label>
		</div>
		<div class='row'>
			<table width=100%>		
				<tr>
					<th></th><th>".$msg["parperso_field_name"]."</th><th>".$msg["parperso_field_title"]."</th><th>".$msg["parperso_input_type"]."</th><th>".$msg["parperso_data_type"]."</th>
				</tr>	
				!!fields_list!!
				
			</table>
		</div>
	
	
		<div class='row'>
			<label class='etiquette' for='authperso_tpl_form'>".$msg['admin_authperso_tpl_form']."</label>
		</div>
		<div class='row'>
			<textarea type='text' name='authperso_tpl_form' id='authperso_tpl_form' class='saisie-50em' rows='5' cols='80' >!!authperso_tpl_form!!</textarea>
		</div>
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_authperso_save']."' onclick=\"document.getElementById('action').value='save_edition';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_authperso_exit']."'  onclick=\"document.location='./admin.php?categ=authorities&sub=authperso'\"  />
		</div>
		<div class='right'>
		</div>
	</div>
<div class='row'></div>
</form>		
";

$authperso_form_edition_field_tpl="
<tr class='$pair_impair' style='cursor: pointer' $tr_javascript>
	<td>
		<input type='button' class='bouton_small' value='-' onClick='document.location=\"".$base_url."&action=up&id=".$r->idchamp."\"'/></a>
		<input type='button' class='bouton_small' value='+' onClick='document.location=\"".$base_url."&action=down&id=".$r->idchamp."\"'/>
	</td>
	<td><b>!!name!!</b></td>
	<td>!!>titre!!</td>
	<td>!!type!!</td>
	<td>!!datatype!!</td>
</tr>
";

		
$authperso_form = jscript_unload_question()."
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.ed_nom.value.length == 0)
			{
				alert(\"$msg[144]\");
				return false;
			}
		unload_off();
		return true;
	}
function confirm_delete() {
        result = confirm(\"${msg[confirm_suppr]}\");
        if(result) {
        	unload_off();
            document.location='./autorites.php?categ=authperso&sub=delete&id_authperso=!!id_authperso!!&id=!!id!!&user_input=!!user_input_url!!&page=!!page!!&nbr_lignes=!!nbr_lignes!!';
		} else{
            //document.forms['saisie_editeur'].elements['ed_nom'].focus();
        }
    }
	function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
-->
</script>
<form class='form-$current_module' id='saisie_authperso' name='saisie_authperso' method='post' action='!!action!!' onSubmit=\"return false\" >
<h3>!!libelle!!</h3>
<div class='form-contenu'>
		!!list_field!!
		<!-- index_concept_form -->
		<!-- aut_link -->
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='./autorites.php?categ=authperso&sub=reach&id_authperso=!!id_authperso!!&id=!!id!!&user_input=!!user_input_url!!&page=!!page!!&nbr_lignes=!!nbr_lignes!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"unload_off(); this.form.submit();\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	//document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";

		
$authperso_form_select = jscript_unload_question()."
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.ed_nom.value.length == 0)
			{
				alert(\"$msg[144]\");
				return false;
			}
		unload_off();
		return true;
	}
function confirm_delete() {
        result = confirm(\"${msg[confirm_suppr]}\");
        if(result) {
        	unload_off();
            document.location='./autorites.php?categ=authperso&sub=delete&id_authperso=!!id_authperso!!&id=!!id!!&user_input=!!user_input_url!!&page=!!page!!&nbr_lignes=!!nbr_lignes!!';
		} else{
            //document.forms['saisie_editeur'].elements['ed_nom'].focus();
        }
    }
	function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
-->
</script>
<form class='form-$current_module' id='saisie_authperso' name='saisie_authperso' method='post' action='!!action!!' onSubmit=\"return false\" >
<h3>!!libelle!!</h3>
<div class='form-contenu'>
		!!list_field!!
		<!-- index_concept_form -->
		<!-- aut_link -->
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!retour!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"unload_off(); this.form.submit();\" />

	</div>
	<div class='right'>
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	//document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";


// $authperso_replace : form remplacement 
$authperso_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='authperso_replace' method='post' action='./autorites.php?categ=authperso&sub=replace&id=!!id!!' onSubmit=\"return false\" >
<h3>$msg[159] !!old_authperso_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[160]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' id='authperso_libelle' name='authperso_libelle' value=\"\" completion=\"authpersos\" autfield=\"by\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=authperso&caller=authperso_replace&param1=by&param2=authperso_libelle&no_display=!!id!!', 'select_ed', $selector_x_size, $selector_x_size, -2, -2, '$selector_prop'); }\" />

		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=authperso&caller=authperso_replace&authperso_id=!!id_authperso!!&p1=by&p2=authperso_libelle&no_display=!!id!!', 'select_ed', $selector_x_size, $selector_x_size, -2, -2, '$selector_prop')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.authperso_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' id='by' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox'  value='1'>".$msg["aut_replace_link_save"]."
	</div>	
	</div>
<div class='row'>
	<input type='button' class='bouton' value='$msg[76]' onClick=\"document.location='./autorites.php?categ=authperso&sub=authperso_form&id_authperso=!!id_authperso!!&id=!!id!!';\">
	<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
	</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['authperso_replace'].elements['authperso_libelle'].focus();
</script>
";		
