<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.tpl.php,v 1.5 2015-04-16 09:02:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");


$form_dialog_note="
<script src='./javascript/tablist.js' type='text/javascript'></script>

<script type='text/javascript'>
function confirm_delete()
{
	phrase = \"{$msg[demandes_note_confirm_suppr]}\";
	result = confirm(phrase);
	if(result){
		return true;
	}
	return false;
}
</script>

<form action=\"./empr.php?tab=request&lvl=list_dmde#fin\" method=\"post\" name=\"modif_notes\"> 
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='sub' id='sub' />
	<input type='hidden' name='idaction' id='idaction' value='!!idaction!!'/>
	<input type='hidden' name='iddemande' id='iddemande' value='!!iddemande!!'/>
	<input type='hidden' name='redirectto' id='redirectto' value='!!redirectto!!'/>
	<input type='hidden' name='idnote' id='idnote'/>
	<div id='dialog_wrapper'>
		!!dialog!!	
	</div>
	<textarea name='contenu_note'></textarea>
	<div>		
		<input type='checkbox' name='ck_vue' id='ck_vue' value='1' checked/>
		<label for='ck_vue' class='etiquette'>".$msg['demandes_note_vue']."</label>
	</div>	
	<input type='button' class='bouton' value='".$msg['demandes_note_add']."' onclick=\"this.form.sub.value='add_note';document.forms['modif_notes'].submit();\"/>
</form>
";

$form_table_note ="
<script src='./javascript/tablist.js' type='text/javascript'></script>
<script type='text/javascript'>
function confirm_delete()
{
	phrase = \"{$msg[demandes_note_confirm_suppr]}\";
	result = confirm(phrase);
	if(result){
		return true;
	}
	return false;
}
</script>
<form action=\"./demandes.php?categ=notes\" method=\"post\" name=\"modif_notes\" onsubmit=\"if(document.forms['modif_notes'].act.value == 'suppr_note') return confirm_delete();\"> 
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='act' id='act' />
	<input type='hidden' name='idaction' id='idaction' value='!!idaction!!'/>
	<input type='hidden' name='idnote' id='idnote'/>
	<div class='form-contenu'>
		!!liste_notes!!
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='".$msg['demandes_note_add']."' onclick='this.form.act.value=\"add_note\"'/>
	</div>
</form>
";

$form_modif_note="

<h2>!!path!!</h2>
<form class='form-".$current_module."' id='modif_note' name='modif_note' method='post' action=\"./demandes.php?categ=notes#fin\">
	
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='idnote' name='idnote' value='!!idnote!!'/>
	<input type='hidden' id='iduser' name='iduser' value='!!iduser!!'/>
	<input type='hidden' id='typeuser' name='typeuser' value='!!typeuser!!'/>
	<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette'>".$msg['demandes_note_date']." : </label>
			<input type='hidden' id='date_note' name='date_note' value='!!date_note!!' />
			<input type='button' class='bouton' id='date_note_btn' name='date_note_btn' value='!!date_note_btn!!' onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_note&date_caller=!!date_note!!&param1=date_note&param2=date_note_btn&auto_submit=NO&date_anterieure=YES', 'date_note', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"/>
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['demandes_note_contenu']."</label>
		</div>
		<div class='row'>
			<textarea id='contenu_note' style='width:99%' name='contenu_note'  rows='15' wrap='virtual'>!!contenu!!</textarea>
		</div>
		<div class='row'>
			<input type='checkbox' name='ck_vue' id='ck_vue' value='1' !!ck_vue!!/>
			<label for='ck_vue' class='etiquette'>".$msg['demandes_note_vue']."</label>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"!!cancel_action!!\" />
			<input type='submit' class='bouton' value='$msg[77]' onClick='this.form.act.value=\"save_note\" ; return test_form(this.form); ' />
		</div>
	</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>
function test_form(form) {	

	if((form.contenu_note.value.length == 0)){
		alert(\"$msg[demandes_note_create_ko]\");
		return false;
    } 
    
	return true;
		
}
</script>
";

?>