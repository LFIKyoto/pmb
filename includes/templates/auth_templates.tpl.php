<?php
$auth_template_form="
<h1>".$msg['title_auth_template']."</h1>		
<h3>".$msg['sub_title_auth_template']."</h3>
<div class='form-contenu'>
		<div class='row'>
			<form action='./admin.php?categ=authorities&sub=templates&action=save' method='POST'>
				<select name='auth_tpl_folder_choice'>!!options!!</select>
				<input type='submit' class='bouton' value='".$msg['cms_editorial_form_logo_add']."'/>
			</form>
		</div>
	</div>
";