<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl.tpl.php,v 1.2 2015-03-20 09:06:14 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// Affichage de la liste des templates de listes de circulation

$serialcirc_tpl_liste = "
<table width='100%'>
	<tbody>
		<tr>
			<th width='3%'>".$msg["serialcirc_tpl_id"]."</th>
			<th>".$msg["serialcirc_tpl_name"]."</th>
			<th>".$msg["serialcirc_tpl_description"]."</th>
		</tr>
		!!serialcirc_tpl_liste!!
	</tbody>
</table>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' value='".$msg["serialcirc_tpl_ajouter"]."' onclick=\"document.location='!!link_ajouter!!'\" type='button'>
	</div>
</div>
";

$serialcirc_tpl_liste_ligne = "
<tr <tr class='!!pair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair!!'\" style=\"cursor: pointer;\" >
	<td onmousedown=\"document.location='!!link_edit!!';\" align='right'><b>!!id!!</b></td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!name!!</td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!comment!!</td>
</tr>
";

$serialcirc_tpl_form = "
<script type='text/javascript' src='./javascript/serialcirc_tpl_diff.js'></script>
<script type='text/javascript' src='./javascript/circdiff_tpl_drop.js'></script>
<script type='text/javascript'>

	function test_form(form) {
		if(form.name.value.length == 0)	{
			alert('".$msg["serialcirc_tpl_nom_erreur"]."');
			return false;
		}	
		return true;
	}
	
	function confirm_delete() {
	    result = confirm(\"${msg[confirm_suppr]}\");
	    if(result) {
	        document.location='!!action_delete!!';
		} else
			document.forms['serialcirc_tpl_form'].elements['name'].focus();
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
	/*	
		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE.execCommand('mceInsertContent',false,selvars);
		}
	*/	
		var start = dest.selectionStart;		   
		var start_text = dest.value.substring(0, start);
		var end_text = dest.value.substring(start);
		dest.value = start_text+selvars+end_text;
	}
	
</script>
<script type='text/javascript' src='./javascript/tabform.js'></script>
<form class='form-$current_module' id='serialcirc_tpl_form' name='serialcirc_tpl_form' method='post' action='!!action!!' onSubmit=\"return false\" >
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["serialcirc_tpl_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
		</div>		
		<!-- 	Commentaire -->
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg["serialcirc_tpl_description"]."</label>
		</div>
		<div class='row'>
			<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
		</div>
		<!-- 	Format de la liste de circulation -->
		<div class='row'>
			<label class='etiquette'>".$msg["serialcirc_tpl_format"]."</label>
		</div>
		<div class='row'>
			!!format_serialcirc!!
		</div>			
		<div class='row'>	
			<label class='etiquette' for='piedpage'>".$msg['serialcirc_diff_option_form_fiche_pied_page']."</label>!!fields_options!!
			<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('piedpage')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' >			
		</div>
		<div class='row'>
			<textarea type='text' name='piedpage' id='piedpage' class='saisie-50em' rows='4' cols='50' >!!pied_page!!</textarea>
		</div>	
	</div>
	<!--	boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"history.go(-1);\" />
			<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
			</div>
		<div class='right'>
			!!delete!!
			</div>
		</div>
	<div class='row'></div>
	<input type='hidden' id='id_tpl' name='id_tpl' value='!!id!!' />
	<input type='hidden' id='order_tpl' name='order_tpl' value='!!order_tpl!!' />
</form>
<script type='text/javascript'>
	document.forms['serialcirc_tpl_form'].elements['name'].focus();	
</script>	
";

