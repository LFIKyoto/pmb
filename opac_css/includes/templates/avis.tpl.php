<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.tpl.php,v 1.8.4.4 2015-11-01 14:32:58 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

$avis_tpl_header = "<div id='titre-popup'>".$msg["notice_title_avis"]."</div>";

$avis_tpl_form_script = "
	<script type='text/javascript' src='./includes/javascript/bbcode.js'></script>
";

if($opac_avis_note_display_mode==2)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<select id='avis_note' name='avis_note'>
				<option value='0'>".$msg["avis_detail_note_0"]."</option>
				<option value='1'>".$msg["avis_detail_note_1"]."</option>
				<option value='2'>".$msg["avis_detail_note_2"]."</option>
				<option value='3' selected='selected'>".$msg["avis_detail_note_3"]."</option>
				<option value='4'>".$msg["avis_detail_note_4"]."</option>
				<option value='5'>".$msg["avis_detail_note_5"]."</option>
			</select>
		</div>
	";
else if($opac_avis_note_display_mode==4)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_text'>".$msg["avis_note_1"]."</span>
					<span class='echelle_avis_stars'>
						<span class='echelle_avis_star'>
							<input type='radio' name='avis_note' id='note_1' value='1' /><label for='note_1'></label>
							<input type='radio' name='avis_note' id='note_2' value='2' /><label for='note_2'></label>
							<input type='radio' name='avis_note' id='note_3' value='3' checked /><label for='note_3'></label>
							<input type='radio' name='avis_note' id='note_4' value='4' /><label for='note_4'></label>
							<input type='radio' name='avis_note' id='note_5' value='5' /><label for='note_5'></label>
						</span>
					</span>
				<span class='echelle_avis_text'>".$msg["avis_note_5"]."</span>
			</span>
		</div>
	";
else if($opac_avis_note_display_mode==5)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_stars'>
					<span class='echelle_avis_star'>
						<input type='radio' name='avis_note' id='note_1' value='1' title='".$msg["avis_detail_note_1"]."' onClick=\"avis_checked();\" /><label for='note_1'></label>
						<input type='radio' name='avis_note' id='note_2' value='2' title='".$msg["avis_detail_note_2"]."' onClick=\"avis_checked();\" /><label for='note_2'></label>
						<input type='radio' name='avis_note' id='note_3' value='3' title='".$msg["avis_detail_note_3"]."' onClick=\"avis_checked();\" checked /><label for='note_3'></label>
						<input type='radio' name='avis_note' id='note_4' value='4' title='".$msg["avis_detail_note_4"]."' onClick=\"avis_checked();\" /><label for='note_4'></label>
						<input type='radio' name='avis_note' id='note_5' value='5' title='".$msg["avis_detail_note_5"]."' onClick=\"avis_checked();\" /><label for='note_5'></label>
					</span>
				</span>
				&nbsp;&nbsp;<span id='avis_detail_note'>".$msg["avis_detail_note_3"]."</span>
			</span>
		</div>

		<script type='text/javascript'>
			function avis_checked() {
				var avis_checked = document.getElementsByName('avis_note');
				for(var i=0; i < avis_checked.length; i++) {
					if(avis_checked[i].checked) {
						document.getElementById('avis_detail_note').innerHTML = (avis_checked[i].title);
					}
				}
			}
		</script>
	";
else if($opac_avis_note_display_mode!=0)
		$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				$msg[avis_note_1]
				<input type='radio' name='avis_note' id='note_1' value='1' />
				<input type='radio' name='avis_note' id='note_2' value='2' />
				<input type='radio' name='avis_note' id='note_3' value='3' checked />
				<input type='radio' name='avis_note' id='note_4' value='4' />
				<input type='radio' name='avis_note' id='note_5' value='5' />
				$msg[avis_note_5]
			</span>
		</div>
	";
else
	$avis_detail_note_msg="
		<input type='hidden' name='avis_note' value='3'>
	";

$avis_tpl_form = "
	$avis_tpl_form_script
	<center>".$msg["avis_explications"]."</center><br />
	<form id='add_avis' name='add_avis' method='post' action='avis.php?todo=save'>
	$avis_detail_note_msg
		       <input type='hidden' name='noticeid' value='".$noticeid."'>
		       <input type='hidden' name='login' value='".$login."'>

				<div class='row'><label>".$msg["avis_sujet"]."</label><br />
					<input type='text' name='sujet' size='50'/>
					</div>
				<div style='padding-top: 4px;'>
					<input value='".$msg["bbcode_button_label_b"]."' name='B' onclick=\"insert_text('commentaire','[b]','[/b]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_i"]."' name='I' onclick=\"insert_text('commentaire','[i]','[/i]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_u"]."' name='U' onclick=\"insert_text('commentaire','[u]','[/u]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_http"]."' name='Url' onclick=\"insert_text('commentaire','[url]','[/url]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_img"]."' name='Img' onclick=\"insert_text('commentaire','[img]','[/img]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_code"]."' name='Code' onclick=\"insert_text('commentaire','[code]','[/code]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_quote"]."' name='Quote' onclick=\"insert_text('commentaire','[quote]','[/quote]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_red"]."' name='Red' onclick=\"insert_text('commentaire','[red]','[/red]')\" type='button' class='bouton'>
					<input value='".$msg["bbcode_button_label_list"]."' name='List' onclick=\"insert_text('commentaire','[li]','[/li]')\" type='button' class='bouton'>
				</div>
				<div class='row'><label>".$msg["avis_avis"]."</label><br />
					<textarea id='commentaire' name='commentaire' cols='50' rows='4'></textarea>
					</div>

		      <div class='row'>
		        <input type='submit' class='bouton' name='Submit' value='".$msg["avis_bt_envoyer"]."'>
		        <input type='button' class='bouton' value='".$msg["avis_bt_retour"]."' onclick='javascript:document.location.href=\"avis.php?todo=liste&noticeid=".$noticeid."\"; return false;'>
		      </div>
		</form>";

$avis_tpl_post_add= "
	<div align='center'><br /><br />".$msg["avis_msg_validation"]."
	<br /><br /><a href='#' onclick='window.close()'>".$msg["avis_fermer"]."</a>";

$avis_tpl_post_add_pb="<div align='center'><br /><br />".$msg["avis_msg_pb"];

$avis_tpl_form1_script="
	<script type='text/javascript' src='./includes/javascript/bbcode.js'></script>
	<script type='text/javascript'>
	<!--
		function show_add_avis(notice_id) {
			var div_add_avis=document.getElementById('add_avis_'+notice_id);
			if(div_add_avis.style.display  == 'block'){
				div_add_avis.style.display  = 'none';
			}else{
				div_add_avis.style.display  = 'block';
			}
		}

		function send_avis(notice_id) {
			var note=3;
			var boutons_note = document.getElementsByName('avis_note_'+notice_id);
			if(boutons_note.length == 1) {
				boutons_note = document.getElementById('avis_note_'+notice_id);
				if(boutons_note){
					var selIndex = boutons_note.selectedIndex;
					note = boutons_note.options[selIndex].value;
				}
			} else {
				for (var i=0; i < boutons_note.length; i++) {
					if (boutons_note[i].checked) {
						note=i + 1;
					}
				}
			}
			var sujet=document.getElementById('edit_sujet_'+notice_id).value;
			var commentaire=document.getElementById('edit_commentaire_'+notice_id).value;
			if(	sujet  || commentaire){
				var url= './ajax.php?module=ajax&categ=avis&sub=add&id_empr=$id_empr';
				url+='&note='+note;
				url+='&notice_id='+notice_id;

				// On initialise la classe:
				var req = new http_request();
				// Execution de la requette
				req.request(url, true, 'sujet='+encodeURIComponent(sujet)+'&commentaire='+encodeURIComponent(commentaire));

				document.getElementById('add_avis_'+notice_id).innerHTML = '<label>".$msg["avis_validation_en_cours"]."</label>';
			}
			return 1;
		}
	-->
	</script>
";

if($opac_avis_note_display_mode==2)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<select id='avis_note_!!notice_id!!' name='avis_note_!!notice_id!!'>
				<option value='0'>".$msg["avis_detail_note_0"]."</option>
				<option value='1'>".$msg["avis_detail_note_1"]."</option>
				<option value='2'>".$msg["avis_detail_note_2"]."</option>
				<option value='3' selected='selected'>".$msg["avis_detail_note_3"]."</option>
				<option value='4'>".$msg["avis_detail_note_4"]."</option>
				<option value='5'>".$msg["avis_detail_note_5"]."</option>
			</select>
		</div>
	";
else if($opac_avis_note_display_mode==4)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_text'>".$msg["avis_note_1"]."</span>
					<span class='echelle_avis_stars'>
						<span class='echelle_avis_star'>
							<input type='radio' name='avis_note_!!notice_id!!' id='note_1_!!notice_id!!' value='1' /><label for='note_1_!!notice_id!!'></label>
							<input type='radio' name='avis_note_!!notice_id!!' id='note_2_!!notice_id!!' value='2' /><label for='note_2_!!notice_id!!'></label>
							<input type='radio' name='avis_note_!!notice_id!!' id='note_3_!!notice_id!!' value='3' checked /><label for='note_3_!!notice_id!!'></label>
							<input type='radio' name='avis_note_!!notice_id!!' id='note_4_!!notice_id!!' value='4' /><label for='note_4_!!notice_id!!'></label>
							<input type='radio' name='avis_note_!!notice_id!!' id='note_5_!!notice_id!!' value='5' /><label for='note_5_!!notice_id!!'></label>
						</span>
					</span>
				<span class='echelle_avis_text'>".$msg["avis_note_5"]."</span>
			</span>
		</div>
	";
else if($opac_avis_note_display_mode==5)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_stars'>
					<span class='echelle_avis_star'>
						<input type='radio' name='avis_note_!!notice_id!!' id='note_1_!!notice_id!!' value='1' title='".$msg["avis_detail_note_1"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_1_!!notice_id!!'></label>
						<input type='radio' name='avis_note_!!notice_id!!' id='note_2_!!notice_id!!' value='2' title='".$msg["avis_detail_note_2"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_2_!!notice_id!!'></label>
						<input type='radio' name='avis_note_!!notice_id!!' id='note_3_!!notice_id!!' value='3' title='".$msg["avis_detail_note_3"]."' onClick=\"avis_checked(!!notice_id!!);\" checked /><label for='note_3_!!notice_id!!'></label>
						<input type='radio' name='avis_note_!!notice_id!!' id='note_4_!!notice_id!!' value='4' title='".$msg["avis_detail_note_4"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_4_!!notice_id!!'></label>
						<input type='radio' name='avis_note_!!notice_id!!' id='note_5_!!notice_id!!' value='5' title='".$msg["avis_detail_note_5"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_5_!!notice_id!!'></label>
					</span>
				</span>
				&nbsp;&nbsp;<span id='avis_detail_note_!!notice_id!!'>".$msg["avis_detail_note_3"]."</span>
			</span>
		</div>

		<script type='text/javascript'>
			function avis_checked(notice_id) {
				var avis_checked = document.getElementsByName('avis_note_'+notice_id);
				for(var i=0; i < avis_checked.length; i++) {
					if(avis_checked[i].checked) {
						document.getElementById('avis_detail_note_'+notice_id).innerHTML = (avis_checked[i].title);
					}
				}
			}
		</script>
	";
else if($opac_avis_note_display_mode!=0)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				$msg[avis_note_1]
				<input type='radio' name='avis_note_!!notice_id!!' id='note_1_!!notice_id!!' value='1' />
				<input type='radio' name='avis_note_!!notice_id!!' id='note_2_!!notice_id!!' value='2' />
				<input type='radio' name='avis_note_!!notice_id!!' id='note_3_!!notice_id!!' value='3' checked />
				<input type='radio' name='avis_note_!!notice_id!!' id='note_4_!!notice_id!!' value='4' />
				<input type='radio' name='avis_note_!!notice_id!!' id='note_5_!!notice_id!!' value='5' />
				$msg[avis_note_5]
			</span>
		</div>
	";
else
	$avis_detail_note_msg="
		<input type='hidden' name='avis_note_!!notice_id!!' value='3'>
	";

$avis_tpl_form1 = "
	$avis_tpl_form1_script
	<div id='add_avis_!!notice_id!!' style='display: none;'>
		$avis_detail_note_msg
		<div class='row'><label>".$msg["avis_sujet"]."</label><br />
			<input type='text' name='sujet' id='edit_sujet_!!notice_id!!' size='50'/>
		</div>
		<div class='row'><label>".$msg["avis_avis"]."</label><br />
			<input value='".$msg["bbcode_button_label_b"]."' name='B' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[b]','[/b]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_i"]."' name='I' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[i]','[/i]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_u"]."' name='U' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[u]','[/u]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_http"]."' name='Url' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[url]','[/url]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_img"]."' name='Img' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[img]','[/img]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_code"]."' name='Code' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[code]','[/code]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_quote"]."' name='Quote' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[quote]','[/quote]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_red"]."' name='Red' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[red]','[/red]')\" type='button' class='bouton'>
			<input value='".$msg["bbcode_button_label_list"]."' name='List' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[li]','[/li]')\" type='button' class='bouton'>
		</div>
		<div class='row'>
			<textarea name='commentaire' id='edit_commentaire_!!notice_id!!' cols='60' rows='4'></textarea>
		</div>
		<div class='row'>
			<input type='button' class='bouton' onclick=\" send_avis(!!notice_id!!);  return false; \" value='".$msg["avis_bt_envoyer"]."'>
		</div>
	</div>
";

// si paramétrage formulaire particulier
if (file_exists($base_path.'/includes/templates/avis_subst.tpl.php')) require_once($base_path.'/includes/templates/avis_subst.tpl.php');

