<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: websubscribe.tpl.php,v 1.10 2015-06-02 13:24:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

$subs_form_create="
<div id='subs_form'><h3><span>".$msg['subs_titre_form']."</span></h3>
<script type='text/javascript'>
<!--
function test_inscription(form) {
	if ((form.f_nom.value.length==0) || (form.f_prenom.value.length==0) || (form.f_email.value.length==0) || (form.f_login.value.length==0) || (form.f_password.value.length==0) || (form.f_passwordv.value.length==0) || (form.f_verifcode.value.length<5)) {
		alert(\"".$msg[subs_form_obligatoire]."\");
		return false;
	} else if (form.f_password.value!=form.f_passwordv.value) {
		alert(\"".$msg[subs_form_bad_passwords]."\");
		return false;
	} else return true;
   }
-->
</script>
<form name='subs_form' method='post' action='./subscribe.php?subsact=inscrire'>
	<table class='websubscribe_form'>
		<tr class='websubscribe_tr_nom'><td align=right width=20%><h4><span>".$msg['subs_f_nom']."</span></h4></td><td width=25%><input type='text' class='subsform' name='f_nom' tabindex='1' value='!!f_nom!!' /></td>
			<td rowspan=5 width=8%>&nbsp;</td><td rowspan=6 align=center>".$msg['subs_txt_codeverif']."<br /><img src='$base_path/includes/imageverifcode.inc.php'><br /><h4><span>".$msg['subs_f_verifcode']."</span></h4><br /><input type='text' class='subsform' name='f_verifcode' value='' /></td>
		</tr>
		<tr class='websubscribe_tr_prenom'><td align=right><h4><span>".$msg['subs_f_prenom']."</span></h4></td><td><input type='text' class='subsform' name='f_prenom'  tabindex='3' value='!!f_prenom!!' /></td>
		</tr>
		<tr class='websubscribe_tr_email'><td align=right><h4><span>".$msg['subs_f_email']."</span></h4></td><td><input type='text' class='subsform' name='f_email'  tabindex='4' value='!!f_email!!' /></td>
		</tr>
		<tr class='websubscribe_tr_login'><td align=right><h4><span>".$msg['subs_f_login']."</span></h4></td><td><input type='text' class='subsform' name='f_login' tabindex='5' value='!!f_login!!' /></td>
		</tr>
		<tr class='websubscribe_tr_password'><td align=right><h4><span>".$msg['subs_f_password']."</span></h4></td><td><input type='password' class='subsform' name='f_password' tabindex='6' value='!!f_password!!' /></td>
		</tr>
		<tr class='websubscribe_tr_passwordv'><td align=right><h4><span>".$msg['subs_f_passwordv']."</span></h4></td><td><input type='password' class='subsform' name='f_passwordv' tabindex='7' value='!!f_passwordv!!' /></td>
		</tr>
		<tr class='websubscribe_tr_bouton'><td align=right>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align=center><input type=button class='bouton' value=\"".$msg['subs_bouton_form']."\" onClick=\"if (test_inscription(this.form)) this.form.submit();\" /></td>
		</tr>
		<tr class='websubscribe_tr_adr1'><td align=right><span>".$msg['subs_f_adr1']."</span></td><td><input type='text' class='subsform' name='f_adr1' tabindex='10' value='!!f_adr1!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_adr2'><td align=right><span>".$msg['subs_f_adr2']."</span></td><td><input type='text' class='subsform' name='f_adr2' tabindex='11' value='!!f_adr2!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_cp'><td align=right><span>".$msg['subs_f_cp']." ".$msg['subs_f_ville']."</span></td><td><input type='text' class='subsform' name='f_cp' tabindex='12' value='!!f_cp!!' style='width:50px;'/> <input type='text' class='subsform' name='f_ville'  tabindex='13' value='!!f_ville!!' style='width:136px;'/></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_pays'><td align=right><span>".$msg['subs_f_pays']."</span></td><td><input type='text' class='subsform' name='f_pays' tabindex='14' value='!!f_pays!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_tel1'><td align=right><span>".$msg['subs_f_tel1']."</span></td><td><input type='text' class='subsform' name='f_tel1' tabindex='15' value='!!f_tel1!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_msg'><td align=right><span>".$msg['subs_f_msg']."</span></td><td><input type='text' class='subsform' name='f_msg' tabindex='16' value='!!f_msg!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		".(($opac_websubscribe_show_location && $pmb_lecteurs_localises) ? "<tr><td align=right>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_loc'><td align=right><span>".$msg['subs_f_loc']."</span></td><td>!!f_loc!!</td><td>&nbsp;</td><td></td>
		</tr>" : "")."
	</table>
	!!others_informations!!
	</form>	
</div>
";


$form_access_compte="<form action='empr.php' method='post' name='myform'>
				<input type='hidden' name='login' value=\"!!login!!\" />
				<input type='hidden' name='encrypted_password' value='!!encrypted_password!!' />
				<input type='submit' name='ok' value=\"".$msg[subs_bouton_acces_compte]."\" class='bouton'>
			</form>";
