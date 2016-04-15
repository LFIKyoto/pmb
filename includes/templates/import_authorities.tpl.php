<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_authorities.tpl.php,v 1.8 2013-03-21 10:28:55 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//Gestion de l'encodage du fichier d'import
if(isset($encodage_fic_source)){
	$_SESSION["encodage_fic_source"]=$encodage_fic_source;
}elseif($_SESSION["encodage_fic_source"]){
	$encodage_fic_source=$_SESSION["encodage_fic_source"];
}

$authorites_import_form ="
	<iframe src='".$base_path."/autorites/import/iimport_authorities.php' name='iimport_authorities' style='width:100%;height:650px;border:none;'/>";

$authorites_import_form_content = "
	<form class='form-$current_module' enctype='multipart/form-data' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<h3>".htmlentities($msg['import_authorities'],ENT_QUOTES,$charset)."</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='authorities_type'>".htmlentities($msg['import_authorities_type'],ENT_QUOTES,$charset)."</label><br />
				<select name='authorities_type'>
					<option value='all'>".htmlentities($msg['import_authorities_type_all'],ENT_QUOTES,$charset)."</option>
					<option value='author'>".htmlentities($msg['import_authorities_type_author'],ENT_QUOTES,$charset)."</option>
					<option value='uniform_title'>".htmlentities($msg['import_authorities_type_uniform_title'],ENT_QUOTES,$charset)."</option>
					<option value='collection'>".htmlentities($msg['import_authorities_type_collection'],ENT_QUOTES,$charset)."</option>
					<option value='category'>".htmlentities($msg['import_authorities_type_category'],ENT_QUOTES,$charset)."</option>
					<option value='subcollection'>".htmlentities($msg['import_authorities_type_subcollection'],ENT_QUOTES,$charset)."</option>
				</select>
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
			<label class='etiquette' for='id_authority'>".htmlentities($msg['import_authorities_thesaurus'],ENT_QUOTES,$charset)."</label><br />
				!!thesaurus!!
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='type_link'>".htmlentities($msg['import_authorities_type_link_subcollection'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='type_link[subcollection]' value='1' />".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='type_link[subcollection]' value='0' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='create_link'>".htmlentities($msg['import_authorities_create_link'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='create_link' value='1' />".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='create_link' value='0' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div class='row'>
				<label class='etiquette' for='type_link'>".htmlentities($msg['import_authorities_type_link'],ENT_QUOTES,$charset)."</label><br />
				<input type='checkbox' name='type_link[rejected]' value='1' checked='checked'/>".htmlentities($msg['import_authorities_type_link_rejected'],ENT_QUOTES,$charset)."<br />
				<input type='checkbox' name='type_link[associated]' value='1' checked='checked'/>".htmlentities($msg['import_authorities_type_link_associated'],ENT_QUOTES,$charset)."
			</div> 
			<div class='row'>
				<label class='etiquette' for='create_link_spec'>".htmlentities($msg['import_authorities_create_link_spec'],ENT_QUOTES,$charset)."</label><br />
				<!--<input type='radio' name='create_link_spec' value='0' checked='checked'/>".htmlentities($msg[''],ENT_QUOTES,$charset)."<br />-->
				<input type='radio' name='create_link_spec' value='1' checked='checked'/>".htmlentities($msg['import_authorities_create_link_internal'],ENT_QUOTES,$charset)."<br />
				<input type='radio' name='create_link_spec' value='2' />".htmlentities($msg['import_authorities_create_link_all'],ENT_QUOTES,$charset)."<br />
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='force_update'>".htmlentities($msg['import_authorities_force_update'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='force_update' value='1' title='".htmlentities($msg['import_authorities_force_update_yes'],ENT_QUOTES,$charset)."'/>".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='force_update' value='0' title='".htmlentities($msg['import_authorities_force_update_no'],ENT_QUOTES,$charset)."' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='userfile'>".htmlentities($msg[501],ENT_QUOTES,$charset)."</label><br />
				<input type='file' size='60' class='saisie-80em' name='userfile'/>
			</div>
			<div clas='row'>&nbsp;</div>
			<div class='row'>
				<label class=\"etiquette\" for=\"encodage_fic_source\" id=\"text_desc_encodage_fic_source\" name=\"text_desc_encodage_fic_source\">".htmlentities($msg["admin_import_encodage_fic_source"],ENT_QUOTES,$charset)."</label></br>
				<select name=\"encodage_fic_source\" id=\"encodage_fic_source\">
					<option value=\"\" ".(!$encodage_fic_source ? " selected=\"selected\" ": "").">".htmlentities($msg["admin_import_encodage_fic_source_undefine"],ENT_QUOTES,$charset)."</option>
			<option value=\"iso5426\" ".(($encodage_fic_source == "iso5426") ? " selected=\"selected\" ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso5426"],ENT_QUOTES,$charset)."</option>
			<option value=\"utf8\" ".(($encodage_fic_source == "utf8") ? " selected=\"selected\" ": "").">".htmlentities($msg["admin_import_encodage_fic_source_utf8"],ENT_QUOTES,$charset)."</option>
			<option value=\"iso8859\" ".(($encodage_fic_source == "iso8859") ? " selected=\"selected\" ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso8859"],ENT_QUOTES,$charset)."</option>
				</select>
			</div>
		</div>
		<div class='row'>
			<input type='hidden' name='action' value='upload'/>
			<input type='submit' class='bouton' value='".htmlentities($msg[502],ENT_QUOTES,$charset)."'/> 
		</div>
	</form>";

$authorities_import_preload_form = "
	<form class='form-$current_module' name='afterupload' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<input name='action' type='hidden' value=\"load\" />
		<input type='hidden' name='file_submit' value='!!file_submit!!' />
		<input type='hidden' name='from_file' value='!!from_file!!' />
		<input type='hidden' name='create_link' value='!!create_link!!' />
		<input type='hidden' name='create_link_spec' value='!!create_link_spec!!' />
		<input type='hidden' name='force_update' value='!!force_update!!' />
 		<input type='hidden' name='reload' value='!!reload!!' />
 		<input type='hidden' name='authorities_type' value='!!authorities_type!!' />
 		<input type='hidden' name='type_link' value='!!type_link!!' />
 		<input type='hidden' name='id_thesaurus' value='!!id_thesaurus!!' />
	</form>
	<script>setTimeout(\"document.afterupload.submit()\",1000);</script>";


$authorities_import_afterupload_form = "
	<form class='form-$current_module' name='import' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<input name='action' type='hidden' value=\"import\" />
		<input type='hidden' name='file_submit' value='!!file_submit!!' />
		<input type='hidden' name='from_file' value='!!from_file!!' />
		<input type='hidden' name='create_link' value='!!create_link!!' />
		<input type='hidden' name='create_link_spec' value='!!create_link_spec!!' />
		<input type='hidden' name='force_update' value='!!force_update!!' />
		<input type='hidden' name='total' value='!!total!!' />
		<input type='hidden' name='nb_notices' value='!!nb_notices!!' />
		<input type='hidden' name='nb_notices_import' value='!!nb_notices_import!!' />
		<input type='hidden' name='nb_notices_rejetees' value='!!nb_notices_rejetees!!' />
		<input type='hidden' name='authorities_type' value='!!authorities_type!!' />
		<input type='hidden' name='type_link' value='!!type_link!!' />
		<input type='hidden' name='id_thesaurus' value='!!id_thesaurus!!' />
	</form>
	<script>setTimeout(\"document.import.submit()\",1000);</script>";

