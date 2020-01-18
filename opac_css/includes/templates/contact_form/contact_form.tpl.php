<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.tpl.php,v 1.8.6.4 2019-11-21 09:56:16 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $contact_form_form_tpl, $base_path, $msg, $charset, $contact_form_recipients_tpl, $contact_form_attachments_field_tpl;

$contact_form_form_tpl="
<script type='text/javascript'>
	function contact_form_send(form) {
		require([
			'dojo/request/xhr',
			'dojo/dom',
			'dojo/dom-form'
		], function(xhr, dom, domForm){
            xhr.post('./ajax.php?module=ajax&categ=contact_form&sub=form&action=send_attachments',{
                    data: new FormData(dom.byId('contact_form')),
                    headers: {
                        'Content-Type': false
                    }
                }
			).then(function(response){
                var response = JSON.parse(response);
                if(!parseInt(response.has_errors)) {
                    xhr.post('./ajax.php?module=ajax&categ=contact_form&sub=form&action=send_mail',{
        					handleAs: 'json',
        					data: {form_fields : domForm.toJson(contact_form), attachments: JSON.stringify(response.attachments)}
        				}
        			).then(function(response){
        				if(response) {
        					if(response.sended) {
        						var h3_node = document.createElement('h3');
        						h3_node.setAttribute('class', 'contact_form_title');
        						h3_node.innerHTML = \"!!title!!\";
        						var response_node = document.createElement('p');
        						response_node.setAttribute('class', 'contact_form_response');
        						response_node.innerHTML = response.messages.join('<br />');
        						document.getElementById('contact_form_content').innerHTML = '';
        						document.getElementById('contact_form_content').appendChild(h3_node);
        						document.getElementById('contact_form_content').appendChild(response_node);
        					} else {
        						document.getElementById('contact_form_message').innerHTML = response.messages.join('<br />');
                                captcha_image_audioObj.refresh(); 
                                document.getElementById('contact_form_verifcode').value = '';
                                document.getElementById('captcha_image').src = './includes/securimage/securimage_show.php?' + Math.random();
        					}
        				}
        			})
                } else {
                    document.getElementById('contact_form_message').innerHTML = response.errors_messages.join('<br />');
                    captcha_image_audioObj.refresh();
                    document.getElementById('contact_form_verifcode').value = '';
                    document.getElementById('captcha_image').src = './includes/securimage/securimage_show.php?' + Math.random();
                }
            })
		});
	}
    function contact_form_object_change(id) {
		require([
			'dojo/request/xhr',
			'dojo/dom',
            'dojo/dom-attr',
            'dojo/dom-style'
		], function(xhr, dom, domAttr, domStyle){
			xhr.post('./ajax.php?module=ajax&categ=contact_form&sub=object&action=change',{
					handleAs: 'json',
					data: {id : id}
				}
			).then(function(response){
				if(response) {
                    domAttr.remove('contact_form_object_free_entry_block', 'style');
                    if(parseInt(response.id)) {
                        domStyle.set(dom.byId('contact_form_object_free_entry_block'), 'display', 'none');
                        domAttr.set(dom.byId('contact_form_object_free_entry'), 'required', 'false');

                    } else {
                        domStyle.set(dom.byId('contact_form_object_free_entry_block'), 'display', 'block');
                        domAttr.set(dom.byId('contact_form_object_free_entry'), 'required', 'true');
                    }
					dom.byId('contact_form_text').value = response.message;
				}
			})
		});
	}
</script>
<div id='contact_form_content'>
	<h3 class='contact_form_title'>!!title!!</h3>
	<p class='contact_form_introduction'>".$msg['contact_form_introduction']."</p><br />
	<form id='contact_form' name='contact_form' method='post' action='' data-dojo-type='dijit/form/Form' enctype='multipart/form-data'>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne2'>
			</div>
			<div class='colonne2' id='contact_form_message'>
			</div>
		</div>
		!!recipients!!
		!!fields!!
		<div class='contact_form_objects'>
			<div class='colonne2'>
				<label>!!objects_label!!</label>
			</div>
			<div class='colonne2'>
				!!objects_selector!!
			</div>
		</div>
		<div class='contact_form_separator'>&nbsp;</div>
		<div id='contact_form_object_free_entry_block' class='contact_form_object_free_entry' style='display:none;'>
			<div class='colonne2'>
				<label for='contact_form_object_free_entry'>".htmlentities($msg['contact_form_object_free_entry'], ENT_QUOTES, $charset)."</label>
					".htmlentities($msg['contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset)."
			</div>
			<div class='colonne2'>
				<input type='text' id='contact_form_object_free_entry' name='contact_form_object_free_entry' class='saisie-50em' data-dojo-type='dijit/form/TextBox' required='false' />
			</div>
		</div>
        <div class='contact_form_text'>
			<div class='colonne2'>
				<label for='contact_form_text'>".htmlentities($msg['contact_form_text'], ENT_QUOTES, $charset)."</label>
					".htmlentities($msg['contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset)."
			</div>
			<div class='colonne2'>
				<textarea id='contact_form_text' name='contact_form_text' class='saisie-50em' rows='5' cols='35' data-dojo-type='dijit/form/Textarea' required='true'>!!message!!</textarea>
			</div>
		</div>
		<div class='contact_form_separator'>&nbsp;</div>
		<div class='contact_form_code'>
			<div class='colonne2'>
                <span class='contact_form_text_verifcode'>".$msg['subs_f_verifcode']."</span>
			</div>
			<div class='colonne2'>
				<span class='contact_form_text_verif'>".$msg['subs_txt_codeverif']."</span><br />                
                !!captcha!!
				<div class='contact_form_verifcode'>
					<input type='text' class='subsform' name='contact_form_verifcode' id='contact_form_verifcode' data-dojo-type='dijit/form/TextBox' value='' required='true' />
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='center'>
			<input type='submit' class='bouton' value=\"".$msg['contact_form_button_send']."\" onclick=\"if(dijit.byId('contact_form').validate()) { contact_form_send(); } return false;\" />
		</div>
	</div>
	</form>
</div>
";

$contact_form_recipients_tpl= "
<div class='contact_form_recipients'>
	<div class='colonne2'>
		<label>!!recipients_label!!</label>
	</div>
	<div class='colonne2'>
		!!recipients_selector!!
	</div>
</div>
<div class='contact_form_separator'>&nbsp;</div>";

$contact_form_attachments_field_tpl="
<div id='add_attachments'>
	<input type='hidden' id='nb_attachment' value='1'/>
	<div class='row' id='attachment_1'>
		<input type='file' id='contact_form_parameter_attachments_1' name='contact_form_parameter_attachments[]' class='saisie-80em' size='60'/><input class='bouton' type='button' value='X' onclick='document.getElementById(\"contact_form_parameter_attachments_1\").value=\"\"'/>
		<input class='bouton' type='button' value='+' onClick=\"add_contact_form_parameter_attachments();\"/>
	</div>
</div>
<script type='text/javascript'>
	function add_contact_form_parameter_attachments(){
		var nb_attachment=document.getElementById('nb_attachment').value;
		nb_attachment= (nb_attachment*1) + 1;
	    
		var template = document.getElementById('add_attachments');
	    
		var divattachment=document.createElement('div');
   		divattachment.className='row';
   		divattachment.setAttribute('id','attachment_'+nb_attachment);
   		template.appendChild(divattachment);
   		document.getElementById('nb_attachment').value=nb_attachment;
	    
   		var inputfile=document.createElement('input');
   		inputfile.setAttribute('type','file');
   		inputfile.setAttribute('name','contact_form_parameter_attachments[]');
   		inputfile.setAttribute('id','contact_form_parameter_attachments_'+nb_attachment);
   		inputfile.setAttribute('class','saisie-80em');
   		inputfile.setAttribute('size','60');
   		divattachment.appendChild(inputfile);
	    
   		var inputfile=document.createElement('input');
   		inputfile.setAttribute('type','button');
   		inputfile.setAttribute('value','X');
   		inputfile.setAttribute('onclick','del_contact_form_parameter_attachments('+nb_attachment+');');
   		inputfile.setAttribute('class','bouton');
   		divattachment.appendChild(inputfile);
	}
	    
	function del_contact_form_parameter_attachments(nb_attachment){
		var parent = document.getElementById('add_attachments');
		var child = document.getElementById('attachment_'+nb_attachment);
		parent.removeChild(child);
	    
		var nb_attachment=document.getElementById('nb_attachment').value;
		nb_attachment= (nb_attachment*1) - 1;
		document.getElementById('nb_attachment').value=nb_attachment;
	    
	}
</script>";