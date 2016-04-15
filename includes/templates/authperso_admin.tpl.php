<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_admin.tpl.php,v 1.2 2014-09-02 15:14:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$authperso_list_tpl="	
<h1>".htmlentities($msg["admin_authperso"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_authperso_name"], ENT_QUOTES, $charset)."			
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_notice_onglet"], ENT_QUOTES, $charset)."				
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_opac_search_simple_list_title"], ENT_QUOTES, $charset)."					
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_opac_search_multi_list_title"], ENT_QUOTES, $charset)."				
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_gestion_search_simple_list_title"], ENT_QUOTES, $charset)."		
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_gestion_search_multi_list_title"], ENT_QUOTES, $charset)."				
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
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!notice_onglet!!
	</td> 
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!simple_search!!
	</td> 
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!multi_search!!
	</td>  
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!gestion_simple_search!!
	</td> 
	<td valign='top' style=\"cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!gestion_multi_search!!
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
			<label class='etiquette' for='notice_onglet'>".$msg['admin_authperso_notice_onglet']."</label>
		</div>
		<div class='row'>
			!!notice_onglet_list!! <a href='./admin.php?categ=notices&sub=onglet'>".$msg['admin_authperso_notice_onglet_see']."</a>
		</div>						
		<div class='row'>
			<label class='etiquette' for='isbd_script'>".$msg['admin_authperso_form_isbd_script']."</label>!!fields_options!!
			<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('isbd_script')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' >
		</div>
		<div class='row'>
			<textarea type='text' name='isbd_script' id='isbd_script' class='saisie-50em' rows='4' cols='50' >!!isbd_script!!</textarea>
		</div>							
		<div class='row'>
			<label class='etiquette' for='view_script'>".$msg['admin_authperso_form_view_script']."</label>!!fields_options_view!!
			<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options_view'), document.getElementById('view_script')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' >
		</div>
		<div class='row'>
			<textarea type='text' name='view_script' id='view_script' class='saisie-50em' rows='4' cols='50' >!!view_script!!</textarea>
		</div>		
		<div class='row'>
			<label class='etiquette' >".$msg['admin_authperso_opac_search']."</label>
		</div>
		<div class='row'>
			!!search_simple!!		
			<input id='search_multi' type='checkbox' value='1' name='search_multi' !!search_multi!!> ".$msg['admin_authperso_opac_search_multi_critere']."	
		<div class='row'>
			<label class='etiquette' >".$msg['admin_authperso_gestion_search']."</label>
		</div>
		<div class='row'>
			!!search_simple_gestion!!
			<input id='gestion_search_multi_gestion' type='checkbox' value='1' name='gestion_search_multi' !!search_multi_gestion!!> ".$msg['admin_authperso_gestion_search_multi']."
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