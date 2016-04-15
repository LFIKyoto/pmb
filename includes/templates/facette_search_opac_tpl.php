<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$tpl_form_facette_opac_view = "
<div class='row'>
	<label for='opac_views'>".htmlentities($msg['admin_opac_facette_opac_views'],ENT_QUOTES,$charset)."</label></br>
</div>
<div class='row'>	
	!!list_opac_views!!
</div>";

$tpl_form_facette=
"
<script type='text/javascript'>
	function check_form_facette(){
		var nom = document.getElementById('label_facette').value;
		if(nom==''){
			alert('".$msg['label_alert_form_facette']."');
			return false;
		}
		else return true;
	}
	
	function confirm_delete() {
    	result = confirm('!!name_del_facette!!');
	    if(result) {
	        document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=delete&id=!!id!!';
		}
	}
	
</script>
!!script!!
<form class='form-$current_module' name='nelle_facette_form' method='post' action='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=save' onSubmit='return check_form_facette()'>
	<h3>!!libelle!!</h3>
	
	<div id='listes' class='form-contenu'>
			<div class='row'>
				<label for='label_facette'>".htmlentities($msg['intitule_facette'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input id='label_facette' type='text' name='label_facette' value='!!nameF!!'/>
			</div>
			<div class='row'>
				<label for='list_crit'>".htmlentities($msg['list_crit_form_facette'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				!!liste1!!
			</div>
			<div id='liste2' class='row'></div>
			<div class='row'>
				<label>".htmlentities($msg['crit_sort_facette'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input type='radio' id='type_sort' name='type_sort' value='0' !!defaut_check_type!!/><label for='type_sort'>".$msg['intit_gest_tri1']."</label>
				<input type='radio' id='type_sort2' name='type_sort' value='1' !!defaut_check_type2!!/><label for='type_sort2'>".$msg['intit_gest_tri2']."</label>
			</div>
			<div class='row'>
				<label>".htmlentities($msg['order_sort_facette'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input type='radio' id='order_sort' name='order_sort' value='0' !!defaut_check_order!!/><label for='order_sort'>".$msg['intit_gest_tri3']."</label>
				<input type='radio' id='order_sort2' name='order_sort' value='1' !!defaut_check_order2!!/><label for='order_sort2'>".$msg['intit_gest_tri4']."</label>
			</div>
			<div class='row'>
				<label for='list_nb'>".htmlentities($msg['list_nbMax_form_facette'],ENT_QUOTES,$charset)."</label></br>
			</div>
			<div class='row'>	
				<input type='text' size='5' id='list_nb' name='list_nb' value='!!val_nb!!'>
			</div>
			<div class='row'>
				<label for='limit_plus'>".htmlentities($msg['facette_limit_plus_form'],ENT_QUOTES,$charset)."</label></br>
			</div>
			<div class='row'>	
				<input type='text' size='5' id='list_nb' name='limit_plus' value='!!limit_plus!!'>
			</div>
			<div class='row'>
				<label for=visible>".htmlentities($msg['check_visible'],ENT_QUOTES,$charset)."</label>
				<input type='checkbox' id=visible name='visible' value='1' !!defaut_check!!>
			</div>
			".($pmb_opac_view_activate ? $tpl_form_facette_opac_view : "" )."
			</br />
			
			<input name='hidden_form' type='hidden' value='!!valHidden!!'/>
	</div>
	<div class='left'>
			<input class='bouton' type='submit' value='!!val_submit_form!!'/>
			<input class='bouton' type='button' value='".htmlentities($msg['submitStopFacette'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette'\"/>
	</div>
	<div class='right'>
			!!val_submit_suppr!!
	</div>
	<div class='row'></div>
</form>";

$tpl_vue_facettes=
"
<hr/>
<h3>".htmlentities($msg['title_tab_facette'],ENT_QUOTES,$charset)."</h3>
<div class='row'>
	<table>
		<tr>
			<th>".htmlentities($msg['facette_order'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['intitule_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['critP_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['ssCrit_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['nbRslt_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['sort_view_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['visible_facette'],ENT_QUOTES,$charset)."</th>
		</tr>
		!!lst_facette!!
	</table>
	<div class='row'>
		<input class='bouton' type='button' value='".htmlentities($msg['lib_nelle_facette_form'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=edit'\"/>	
		<input class='bouton' type='button' value='".htmlentities($msg['facette_order_bt'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=order'\"/>
	</div>
</div>
";