<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id:

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$tpl_form_compare="
	<form class='form-$current_module' name='form_compare' method='post' action='./admin.php?categ=opac&sub=facette_search_opac&section=comparateur&action=save'>
		<div class='row'>
			<label for='notice_tpl'>".htmlentities($msg['notice_tpl_label'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!sel_notice_tpl!!
		</div>
		<br />
		<div class='row'>
			<label for='notice_nb'>".htmlentities($msg['notice_nb_label'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' value='!!notice_nb!!' name='notice_nb'/>
		</div>
		<br />
		<div class='left'>
		<input class='bouton' type='button' value='".htmlentities($msg['76'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=comparateur&action=display'\"/>
			<input class='bouton' type='submit' value='".htmlentities($msg['77'],ENT_QUOTES,$charset)."'/>
		</div>
	</form>
";

$tpl_display_compare="
	<div class='row'>
		".htmlentities($msg['notice_tpl_label'],ENT_QUOTES,$charset)." : !!notice_tpl_libelle!!
	</div>
	<br />
	<div class='row'>
		".htmlentities($msg['notice_nb_label'],ENT_QUOTES,$charset)." : !!notice_nb_libelle!!
	</div>
	<br />
	<div class='left'>
		<input class='bouton' type='button' value='".htmlentities($msg['62'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=comparateur&action=modify'\"/>
	</div>
</form>
";