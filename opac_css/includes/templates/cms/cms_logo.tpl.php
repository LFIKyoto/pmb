<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_logo.tpl.php,v 1.1 2012-03-19 15:02:17 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$cms_logo_form_tpl ="
	<div class='row'>
		<div class='colonne'>
			<div class='row'>
				<label for='cms_editorial_form_logo'>".$msg['cms_editorial_form_logo']."</label>
			</div> 
			<div class='row'>
				<div id='cms_logo_vign'>
					<img id='cms_logo_vign_img' src='./cms_vign.php?type=!!type!!&id=!!id!!&mode=vign' class='cms_log_vign'/>
				</div>
				<div>
					<span>".$msg['cms_editorial_form_logo_new']."</span>&nbsp;
					!!field!!
				</div>
			</div>
		</div>
	</div>";

$cms_logo_form_exist_obj_tpl="
	<iframe src='./ajax.php?module=cms&categ=edit_logo&quoi=!!type!!&id=!!id!!' class='cms_logo_frame' >
	</iframe>";

$cms_logo_form_new_obj_tpl="
	<input type='file' name='cms_logo_file'/>
	<script type='text/javascript'>
		!!js!!
	</script>";

$cms_logo_field_tpl ="

<form method='post' name='cms_logo_form' id='cms_logo_form' action='' enctype='multipart/form-data'>
	<input type='file' name='cms_logo_file'/>
	<input type='submit' value='".$msg['cms_editorial_form_logo_add']."' />
</form>
<script type='text/javascript'>
	!!js!!
</script>";

$cms_new_logo_field_tpl ="
<form method='post' name='cms_logo_form' id='cms_logo_form' action='' enctype='multipart/form-data'>
	<input type='file' name='cms_logo_file'/>
	<input type='submit' value='".$msg['cms_editorial_form_logo_add']."' />
</form>
<script type='text/javascript'>
	!!js!!
</script>";