<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_bannette_ui.tpl.php,v 1.1 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_lecteurs_localises;

$list_readers_bannette_ui_search_filters_form_tpl = "
!!bannette_lecteurs_saved!!
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['dsi_ban_form_categ_lect']."</label>
		</div>
		<div class='row'>
			!!categories!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['dsi_ban_form_groupe_lect']."</label>
		</div>
		<div class='row'>
			!!groups!!
		</div>
	</div>
	".($pmb_lecteurs_localises ? 
			"<div class='colonne3'>
				<div class='row'>		
					<label class='etiquette'>".$msg['21']." :</label>
				</div>
				<div class='row'>
					!!locations!!
				</div>
			</div>" : ""
		)."
</div>
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label for='!!objects_type!!_name'>".$msg['dsi_ban_abo_empr_nom']."</label>
		</div>
		<div class='row'>
			<input type='text' class='10em' name='!!objects_type!!_name' value=\"!!name!!\" onchange=\"this.form.submit();\" />
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label for='!!objects_type!!_has_mail'>".$msg['dsi_ban_abo_mail']."</label>
		</div>
		<div class='row'>
			<input type='radio' id='!!objects_type!!_has_mail_no' name='!!objects_type!!_has_mail' value='0' !!has_mail_unchecked!! onchange=\"this.form.submit();\" />
			<label for='!!objects_type!!_has_mail_no'>".$msg['39']."</label>
			<input type='radio' id='!!objects_type!!_has_mail_yes' name='!!objects_type!!_has_mail' value='1' !!has_mail_checked!! onchange=\"this.form.submit();\" />
			<label for='!!objects_type!!_has_mail_yes'>".$msg['40']."</label>
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label for='!!objects_type!!_has_affected'>".$msg['dsi_ban_lecteurs_affectes']."</label>
		</div>
		<div class='row'>
			<input type='radio' id='!!objects_type!!_has_affected_no' name='!!objects_type!!_has_affected' value='0' !!has_affected_unchecked!! onchange=\"this.form.submit();\" />
			<label for='!!objects_type!!_has_affected_no'>".$msg['39']."</label>		
			<input type='radio' id='!!objects_type!!_has_affected_yes' name='!!objects_type!!_has_affected' value='1' !!has_affected_checked!! onchange=\"this.form.submit();\" />
			<label for='!!objects_type!!_has_affected_yes'>".$msg['40']."</label>
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label for='!!objects_type!!_mail'>".$msg['email']."</label>
		</div>
		<div class='row'>
			<input type='text' class='30em' name='!!objects_type!!_mail' value=\"!!mail!!\" onchange=\"this.form.submit();\" />
		</div>
	</div>
</div>
";
