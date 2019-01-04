<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_cart.tpl.php,v 1.2 2017-10-19 14:42:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

require_once("$include_path/templates/export_param.tpl.php");

global $authorities_cart_choix_quoi;

// templates pour la gestion des paniers

$authorities_cart_choix_quoi = "
<hr />
<form class='form-$current_module' name='maj_proc' method='post' action='!!action!!' >
	<h3>!!titre_form!!</h3>
	<!--	Contenu du form	-->
	<div class='form-contenu'>
		<div class='row'>
			<input type='checkbox' name='elt_flag' value='1' !!elt_flag_checked!!>$msg[caddie_item_marque]
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='checkbox' name='elt_no_flag' value='1' !!elt_no_flag_checked!!>$msg[caddie_item_NonMarque]
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\"!!action_cancel!!\"' />&nbsp;
		<input type='submit' class='bouton' value='!!bouton_valider!!' !!onclick_valider!!/>&nbsp;
	</div>
</form>
";

