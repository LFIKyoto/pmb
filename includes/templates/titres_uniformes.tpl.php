<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes.tpl.php,v 1.12 2015-06-19 07:31:05 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$selector_prop = "toolbar=no, dependent=yes,resizable=yes, scrollbars=yes";


$titre_uniforme_form = jscript_unload_question()."
<script type='text/javascript'>

function test_form(form) {
	if(form.name.value.length == 0)	{
		alert(\"$msg[213]\");
		return false;
	}
	unload_off();	
	return true;
}

function confirm_delete() {
    result = confirm(\"${msg[confirm_suppr]}\");
    if(result) {
        unload_off();
        document.location='./autorites.php?categ=titres_uniformes&sub=delete&id=!!id!!&user_input=!!user_input_url!!&page=!!page!!&nbr_lignes=!!nbr_lignes!!';
	} else
        document.forms['saisie_titre_uniforme'].elements['titre_uniforme'].focus();
}
function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}

</script>

<script src='javascript/ajax.js'></script>
<form class='form-$current_module' id='saisie_titre_uniforme' name='saisie_titre_uniforme' method='post' action='!!action!!' onSubmit=\"return false\" >
<h3>!!libelle!!</h3>
<div class='form-contenu'>

<!--	nom	-->
<div class='row'>
	<label class='etiquette' for='form_nom'>".$msg["aut_titre_uniforme_form_nom"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='form_nom' name='name' value=\"!!nom!!\" />
</div>

!!authors!!

<!--	Forme de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_form'>".$msg["aut_oeuvre_form_forme"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30em' id='form_form' name='form' value='!!form!!'>
</div>	

<!--	Forme de l'oeuvre liste controlée -->		
<div class='row'>
	<label class='etiquette' for='form_form'>".$msg["aut_oeuvre_form_forme_list"]."</label>
</div>
<div class='row'>
	!!form_selector!!
</div>
			<!--	Date de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_dates'>".$msg["aut_oeuvre_form_date"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30em' id='form_dates' name='date' value='!!date!!'>
</div>
			
<!--	Lieu d'origine de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_place'>".$msg["aut_oeuvre_form_lieu"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30em' id='form_place' name='place' value='!!place!!'>
</div>
			
<!--	Sujet de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_subject'>".$msg["aut_oeuvre_form_sujet"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='form_subject' name='subject' cols='62' rows='4' wrap='virtual'>!!subject!!</textarea>
</div>

<!--	Complétude visée de l'oeuvre	-->
<div class='colonne2'>
	<label class='etiquette' for='form_completude'>".$msg["aut_oeuvre_form_completude"]."</label>
</div>
<div class='row'>
	<select id='form_intended_termination' name='intended_termination' class='saisie-20em'>
		<option value='0' !!intended_termination_0!!>--</option>\n
		<option value='1' !!intended_termination_1!!>Oeuvre finie</option>\n
		<option value='2' !!intended_termination_2!!>Oeuvre infinie</option>\n
	</select>	
</div>
			
<!--	Public visé de l'oeuvre	-->
<div class='colonne_suite'>
	<label class='etiquette' for='form_intended_audience'>".$msg["aut_oeuvre_form_public"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30em' id='form_intended_audience' name='intended_audience' value='!!intended_audience!!'>
</div>

<!--	Histoire de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_history'>".$msg["aut_oeuvre_form_histoire"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='form_history' name='history' cols='62' rows='4' wrap='virtual'>!!history!!</textarea>
</div>
		
<!--	Contexte de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_context'>".$msg["aut_oeuvre_form_contexte"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='form_context' name='context' cols='62' rows='4' wrap='virtual'>!!context!!</textarea>
</div>			

<!--	Distribution instrumentale et vocale (pour la musique)	-->
<!--	Référence numérique (pour la musique)	-->

<!--	Tonalité (Saisie Libre)	-->
<div class='row'>
	<label class='etiquette' for='form_tonalite'>".$msg["aut_titre_uniforme_form_tonalite"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='form_tonalite' name='tonalite' value='!!tonalite!!'>
</div>
			
<!--	Tonalité (Liste controlée)	-->
<div class='row'>
	<label class='etiquette' for='form_tonalite'>".$msg["aut_titre_uniforme_form_tonalite_list"]."</label>
</div>
<div class='row'>
	!!tonalite_selector!!
</div>
	
<!--	Coordonnées (oeuvre cartographique)	-->
<div class='row'>
	<label class='etiquette' for='form_coordinates'>".$msg["aut_oeuvre_form_coordonnees"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='form_coordinates' name='coordinates' value='!!coordinates!!'>
</div>
			
<!--	Equinoxe (oeuvre cartographique)	-->
<div class='row'>
	<label class='etiquette' for='form_equinox'>".$msg["aut_oeuvre_form_equinoxe"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='form_equinox' name='equinox' value='!!equinox!!'>
</div>

<!-- Subdivision de forme -->
					
<!--	Autres caractéristiques distinctives de l'oeuvre	-->
<div class='row'>
	<label class='etiquette' for='form_carac'>".$msg["aut_oeuvre_form_caracteristique"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='form_carac' name='characteristic' cols='62' rows='4' wrap='virtual'>!!characteristic!!</textarea>
</div>
			
<!-- 	Commentaire -->
<div class='row'>
	<label class='etiquette' for='comment'>".$msg["aut_titre_uniforme_commentaire"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
</div>
!!concept_form!!
!!aut_pperso!!
<div class='row'>
	<label class='etiquette' for='tu_import_denied'>".$msg['authority_import_denied']."</label> &nbsp;
	<input type='checkbox' id='tu_import_denied' name='tu_import_denied' value='1' !!tu_import_denied!!/>
</div>
<!-- aut_link -->
</div>
<!--	boutons	-->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='./autorites.php?categ=titres_uniformes&sub=reach&user_input=!!user_input_url!!&page=!!page!!&nbr_lignes=!!nbr_lignes!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
		</div>
	<div class='right'>
		!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['saisie_titre_uniforme'].elements['name'].focus();
</script>
";

$tu_authors_tpl="
<!--	Auteurs de l'oeuvre	-->
<div id='el1Child_2b_first' class='colonne2'>
	<div class='row'>
		<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut0_id!!iaut!!' id='f_aut0!!iaut!!' name='f_aut0!!iaut!!' value=\"!!aut0!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1=f_aut0_id!!iaut!!&param2=f_aut0!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut0!!iaut!!.value), 'select_author2', 500, 400, -2, -2, '$select1_prop')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut0!!iaut!!.value=''; this.form.f_aut0_id!!iaut!!.value='0'; \" />
		<input type='hidden' name='f_aut0_id!!iaut!!' id='f_aut0_id!!iaut!!' value=\"!!aut0_id!!\" />
	</div>
</div>
<!--    Fonction    -->
<div  id='el1Child_2b_others' class='colonne_suite'>
	<div class='row'>
		<input type='text' class='saisie-15emr' id='f_f0!!iaut!!' name='f_f0!!iaut!!' completion='fonction' autfield='f_f0_code!!iaut!!' value=\"!!f0!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=function&caller=saisie_titre_uniforme&p1=f_f0_code!!iaut!!&p2=f_f0!!iaut!!', 'select_func2', 500, 400, -2, -2, '$select2_prop')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f0!!iaut!!.value=''; this.form.f_f0_code!!iaut!!.value='0'; \" />
		<input type='hidden' name='f_f0_code!!iaut!!' id='f_f0_code!!iaut!!' value=\"!!f0_code!!\" />
		<input type='button' class='bouton' value='+' onClick=\"add_aut(0);\"/>
	</div>
</div>
";


$aut_fonctions= new marc_list('function');
$tu_authors_all_tpl = "
<script>
    function fonction_selecteur_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), 'select_author2', 500, 400, -2, -2, '$select1_prop');
    }
    function fonction_selecteur_auteur_change(field) {
    	// id champ text = 'f_aut'+n+suffixe
    	// id champ hidden = 'f_aut'+n+'_id'+suffixe; 
    	// select.php?what=auteur&caller=saisie_titre_uniforme&param1=f_aut0_id&param2=f_aut0&deb_rech='+t
        name=field.getAttribute('id');
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), 'select_author2', 500, 400, -2, -2, '$select1_prop');
    }
    function fonction_raz_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
    function fonction_selecteur_fonction() {
        name=this.getAttribute('id').substring(4);
        name_code = name.substr(0,4)+'_code'+name.substr(4);
        openPopUp('./select.php?what=function&caller=saisie_titre_uniforme&param1='+name_code+'&param2='+name+'&dyn=1', 'select_fonction2', 500, 400, -2, -2, '$select1_prop');
    }
    function fonction_raz_fonction() {
        name=this.getAttribute('id').substring(4);
        name_code = name.substr(0,4)+'_code'+name.substr(4);
        document.getElementById(name_code).value=0;
        document.getElementById(name).value='';
    }

function add_aut(n) {
	template = document.getElementById('addaut'+n);
	aut=document.createElement('div');
	aut.className='row';
	
	// auteur
	colonne=document.createElement('div');
	colonne.className='colonne2';
	row=document.createElement('div');
	row.className='row';
	suffixe = eval('document.saisie_titre_uniforme.max_aut'+n+'.value')
	nom_id = 'f_aut'+n+suffixe
	f_aut0 = document.createElement('input');
	f_aut0.setAttribute('name',nom_id);
	f_aut0.setAttribute('id',nom_id);
	f_aut0.setAttribute('type','text');
	f_aut0.className='saisie-30emr';
	f_aut0.setAttribute('value','');
	f_aut0.setAttribute('completion','authors');
	f_aut0.setAttribute('autfield','f_aut'+n+'_id'+suffixe);
	
	sel_f_aut0 = document.createElement('input');
	sel_f_aut0.setAttribute('id','sel_f_aut'+n+suffixe);
	sel_f_aut0.setAttribute('type','button');
	sel_f_aut0.className='bouton';
	sel_f_aut0.setAttribute('readonly','');
	sel_f_aut0.setAttribute('value','$msg[parcourir]');
	sel_f_aut0.onclick=fonction_selecteur_auteur;
	
	del_f_aut0 = document.createElement('input');
	del_f_aut0.setAttribute('id','del_f_aut'+n+suffixe);
	del_f_aut0.onclick=fonction_raz_auteur;
	del_f_aut0.setAttribute('type','button');
	del_f_aut0.className='bouton';
	del_f_aut0.setAttribute('readonly','');
	del_f_aut0.setAttribute('value','$msg[raz]');
	
	f_aut0_id = document.createElement('input');
	f_aut0_id.name='f_aut'+n+'_id'+suffixe;
	f_aut0_id.setAttribute('type','hidden');
	f_aut0_id.setAttribute('id','f_aut'+n+'_id'+suffixe);
	f_aut0_id.setAttribute('value','');
	
	//f_aut0_content.appendChild(f_aut0);
	row.appendChild(f_aut0);
	space=document.createTextNode(' ');
	row.appendChild(space);
	row.appendChild(sel_f_aut0);
	space=document.createTextNode(' ');
	row.appendChild(space);
	row.appendChild(del_f_aut0);
	row.appendChild(f_aut0_id);
	colonne.appendChild(row);
	aut.appendChild(colonne);
			
	// fonction	
	colonne=document.createElement('div');
	colonne.className='colonne_suite';
	row=document.createElement('div');
	row.className='row';
	suffixe = eval('document.saisie_titre_uniforme.max_aut'+n+'.value');
	nom_id = 'f_f'+n+suffixe;
	f_f0 = document.createElement('input');
	f_f0.setAttribute('name',nom_id);
	f_f0.setAttribute('id',nom_id);
	f_f0.setAttribute('type','text');
	f_f0.className='saisie-15emr';
	f_f0.setAttribute('value','".$aut_fonctions->table[$value_deflt_fonction]."');
	f_f0.setAttribute('completion','fonction');
	f_f0.setAttribute('autfield','f_f'+n+'_code'+suffixe);
	
	sel_f_f0 = document.createElement('input');
	sel_f_f0.setAttribute('id','sel_f_f'+n+suffixe);
	sel_f_f0.setAttribute('type','button');
	sel_f_f0.className='bouton';
	sel_f_f0.setAttribute('readonly','');
	sel_f_f0.setAttribute('value','$msg[parcourir]');
	sel_f_f0.onclick=fonction_selecteur_fonction;
	
	del_f_f0 = document.createElement('input');
	del_f_f0.setAttribute('id','del_f_f'+n+suffixe);
	del_f_f0.onclick=fonction_raz_fonction;
	del_f_f0.setAttribute('type','button');
	del_f_f0.className='bouton';
	del_f_f0.setAttribute('readonly','readonly');
	del_f_f0.setAttribute('value','$msg[raz]');
			
	f_f0_code = document.createElement('input');
	f_f0_code.name='f_f'+n+'_code'+suffixe;
	f_f0_code.setAttribute('type','hidden');
	f_f0_code.setAttribute('id','f_f'+n+'_code'+suffixe);
	f_f0_code.setAttribute('value','$value_deflt_fonction');
	
	row.appendChild(f_f0);
	space=document.createTextNode(' ');
	row.appendChild(space);
	row.appendChild(sel_f_f0);
	space=document.createTextNode(' ');
	row.appendChild(space);
	row.appendChild(del_f_f0);
	row.appendChild(f_f0_code);
	colonne.appendChild(row);
	
	aut.appendChild(colonne);
	template.appendChild(aut);
	eval('document.saisie_titre_uniforme.max_aut'+n+'.value=suffixe*1+1*1');
	ajax_pack_element(f_aut0);
	ajax_pack_element(f_f0);
}

</script>
    <div id='authors_list' title='".htmlentities($msg["tu_authors_list"],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    auteurs    -->
	    <div class='row'>
	    	<div class='row'>
		        <label for='f_aut0' class='etiquette'>".$msg["tu_authors_list"]."</label>
		        <input type='hidden' name='max_aut0' value=\"!!max_aut0!!\" />
	        </div>
	        <div class='row' id='addaut0'>
		        !!authors_list!!
			</div>
		</div>
	</div>
";

// $titre_uniforme_replace : form remplacement titre_uniforme
$titre_uniforme_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='titre_uniforme_replace' method='post' action='./autorites.php?categ=titres_uniformes&sub=replace&id=!!id!!' onSubmit=\"return false\" >
<h3>$msg[159] !!old_titre_uniforme_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='titre_uniforme_libelle'>$msg[160]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' id='titre_uniforme_libelle' name='titre_uniforme_libelle' value=\"\" completion=\"titres_uniformess\" autfield=\"by\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=titre_uniforme&caller=titre_uniforme_replace&param1=by&param2=titre_uniforme_libelle&no_display=!!id!!', 'select_ed', $selector_x_size, $selector_x_size, -2, -2, '$selector_prop'); }\" />

		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=titre_uniforme_replace&param1=by&param2=titre_uniforme_libelle&no_display=!!id!!', 'select_ed', $selector_x_size, $selector_x_size, -2, -2, '$selector_prop')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.titre_uniforme_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' id='by' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox'  value='1'>".$msg["aut_replace_link_save"]."
	</div>	
	</div>
<div class='row'>
	<input type='button' class='bouton' value='$msg[76]' onClick=\"document.location='./autorites.php?categ=titres_uniformes&sub=titre_uniforme_form&id=!!id!!';\">
	<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
	</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['titre_uniforme_replace'].elements['titre_uniforme_libelle'].focus();
</script>
";
