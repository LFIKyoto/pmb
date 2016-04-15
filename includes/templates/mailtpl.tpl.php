<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailtpl.tpl.php,v 1.5 2015-06-11 14:17:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$mailtpl_list_tpl="	
<h1>".htmlentities($msg["admin_mailtpl_title"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_mailtpl_name"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_empr_button' value='".htmlentities($msg["admin_mailtpl_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=mailtpl&sub=build&action=form'\" />	
";

$mailtpl_list_line_tpl="
<tr  class='!!odd_even!!' onmousedown=\"document.location='./admin.php?categ=mailtpl&sub=build&action=form&id_mailtpl=!!id!!';\"  style=\"cursor: pointer\" 
onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td valign='top'>				
		!!name!!
	</td> 	
	
</tr> 	
";

$mailtpl_form_selvars="
<select name='selvars_id' id='selvars_id'>
	<option value=!!empr_name!!>".$msg["selvars_empr_name"]."</option>
	<option value=!!empr_first_name!!>".$msg["selvars_empr_first_name"]."</option>
	<option value=!!empr_sexe!!>".$msg["selvars_empr_civilite"]."</option>
	<option value=!!empr_cb!!>".$msg["selvars_empr_cb"]."</option>
	<option value=!!empr_login!!>".$msg["selvars_empr_login"]."</option>
	<option value=!!empr_mail!!>".$msg["selvars_empr_mail"]."</option>
	<option value=!!empr_loans!!>".$msg["selvars_empr_loans"]."</option>
	<option value=!!empr_resas!!>".$msg["selvars_empr_resas"]."</option>
	<option value=!!empr_name_and_adress!!>".$msg["selvars_empr_name_and_adress"]."</option>
	<option value=!!empr_all_information!!>".$msg["selvars_empr_all_information"]."</option>
	<option value='".htmlentities("<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>",ENT_QUOTES, $charset)."'>".$msg["selvars_empr_auth_opac"]."</option>
</select>
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars(document.getElementById('selvars_id'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>

	function insert_vars(theselector,dest){	
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;

		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE.execCommand('mceInsertContent',false,selvars);
		}
	}
	
	
</script>
";

$mailtpl_form_sel_img="
!!select_file!!
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_sel_img_insert"]." \" onClick=\"insert_img(document.getElementById('select_file'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>
	function insert_img(theselector,dest){	
		var href='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				href=theselector.options[i].value ;
				break;
			}
		}
		if(!href) return ;
		
		var sel_img='<img src=\"'+href+'\">';
		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+sel_img+end_text;
		}else{
			tinyMCE.execCommand('InsertHTML',false,sel_img);
		}
	}

</script>
";
$mailtpl_form_tpl="	
	$pmb_javascript_office_editor
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_mailtpl_name_error"]."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='mailtpl' name='mailtpl'  method='post' action=\"admin.php?categ=mailtpl&sub=build\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id_mailtpl' id='id_mailtpl' value='!!id_mailtpl!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_mailtpl_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='f_objet_mail'>$msg[empr_mailing_form_obj_mail]</label>
			<div class='row'>
				<input type='text' class='saisie-80em' id='f_objet_mail'  name='f_objet_mail' value='!!objet!!' />
			</div>
		</div>
		<div class='row'>
			<label class='etiquette' for='f_message'>".$msg["admin_mailtpl_form_tpl"]."</label>
			<div class='row'>
				<textarea id='f_message' name='f_message' cols='100' rows='20'>!!tpl!!</textarea>
			</div>
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg["admin_mailtpl_form_selvars"]."</label>
			<div class='row'>
				!!selvars!!
			</div>
		</div>
		!!sel_img!!		
		<div class='row'>
			<input type='hidden' id='auto_id_list' name='auto_id_list' value='!!id_check_list!!' >
			<label class='etiquette' for='form_comment'>$msg[procs_autorisations]</label>
			<input type='button' class='bouton_small' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);' align='middle'>
			<input type='button' class='bouton_small' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);' align='middle'>
		</div>
		<div class='row'>
			!!autorisations_users!!
		</div>
		<div class='row'> 
		</div>
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_mailtpl_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_mailtpl_exit']."'  onclick=\"document.location='./admin.php?categ=mailtpl&sub=build'\"  />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
";
