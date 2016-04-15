<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.tpl.php,v 1.3 2015-05-31 18:43:12 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$faq_question_form ="
<form method='post' class='form-$current_module' name='faq_question_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='faq_question_type_label'>".$msg['faq_question_type_label']."</label><br/>
				!!type_selector!!
			</div>
			<div class='colonne3'>
				<label for='faq_question_theme_label'>".$msg['faq_question_theme_label']."</label><br/>
				!!theme_selector!!
			</div>
			<div class='colonne-suite'>
				<label for='faq_question_statut'>".$msg['faq_question_statut_label']."</label><br/>
				!!statut_selector!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='faq_question_question'>".$msg['faq_question_question']."</label>
		</div>
		<div class='row'>
			<textarea name='faq_question_question' rows='5' >!!question!!</textarea>
		</div>
		<div class='row'>
			<label for='faq_question_question_date'>".$msg['faq_question_question_date']."</label>
		</div>
		<div class='row'>
			<input type='text' name='faq_question_question_date' value='!!question_date!!' placeholder='".$msg['format_date_input_text_placeholder']."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='faq_question_answer'>".$msg['faq_question_answer']."</label>
		</div>
		<div class='row'>
			<textarea name='faq_question_answer' rows='5' >!!answer!!</textarea>
		</div>
		<div class='row'>
				<label for='faq_question_answer_date'>".$msg['faq_question_answer_date']."</label>
			</div>
		<div class='row'>
			<input type='text' name='faq_question_answer_date' value='!!answer_date!!' placeholder='".$msg['format_date_input_text_placeholder']."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
	    	<label for='faq_question_desc'>".$msg['faq_question_desc']."</label>
	    </div>
	    <div class='row'>
	    	!!faq_question_categs!!
	    	<div id='addcateg'/></div>
		</div>
		<div class='row'>&nbsp;</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='faq_question_id' value='!!id!!'/>
			<input type='hidden' name='faq_question_num_demande' value='!!num_demande!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='!!action!!'\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</form>
<script type='text/javascript' src='".$javascript_path."/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
	function add_categ() {
        template = document.getElementById('addcateg');
        categ=document.createElement('div');
        categ.className='row';

        suffixe = eval('document.faq_question_form.max_categ.value')
        nom_id = 'f_categ'+suffixe
        f_categ = document.createElement('input');
        f_categ.setAttribute('name',nom_id);
        f_categ.setAttribute('id',nom_id);
        f_categ.setAttribute('type','text');
        f_categ.className='saisie-80emr';
        f_categ.setAttribute('value','');
		f_categ.setAttribute('completion','categories_mul');
        f_categ.setAttribute('autfield','f_categ_id'+suffixe);

        del_f_categ = document.createElement('input');
        del_f_categ.setAttribute('id','del_f_categ'+suffixe);
        del_f_categ.onclick=fonction_raz_categ;
        del_f_categ.setAttribute('type','button');
        del_f_categ.className='bouton';
        del_f_categ.setAttribute('readonly','');
        del_f_categ.setAttribute('value','$msg[raz]');

        f_categ_id = document.createElement('input');
        f_categ_id.name='f_categ_id'+suffixe;
        f_categ_id.setAttribute('type','hidden');
        f_categ_id.setAttribute('id','f_categ_id'+suffixe);
        f_categ_id.setAttribute('value','');

        categ.appendChild(f_categ);
        space=document.createTextNode(' ');
        categ.appendChild(space);
        categ.appendChild(del_f_categ);
        categ.appendChild(f_categ_id);

        template.appendChild(categ);

        document.faq_question_form.max_categ.value=suffixe*1+1*1 ;
        ajax_pack_element(f_categ);
    }
    function fonction_selecteur_categ() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,7)+'_id'+name.substr(7);
        openPopUp('./select.php?what=categorie&caller=!!cms_editorial_form_name!!&p1='+name_id+'&p2='+name+'&dyn=1', 'select_categ', 700, 500, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes');
    }
    function fonction_raz_categ() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,7)+'_id'+name.substr(7);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
</script>";



$faq_question_first_desc = "
<div class='row'>
<input type='hidden' name='max_categ' value=\"!!max_categ!!\" />
<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=categorie&caller='+this.form.name+'&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=0&deb_rech=', 'select_categ', 700, 500, -2, -2, '$select_categ_prop')\" />
<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
<input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
</div>";
$faq_question_other_desc = "
<div class='row'>
<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";
