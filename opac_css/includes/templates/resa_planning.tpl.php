<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.tpl.php,v 1.6 2015-06-24 15:36:20 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// template pour le formulaire de pose de résa

$form_resa_dates = "
<script type='text/javascript'>
	function test_form(form) {
		var t_sel=form.getElementsByTagName('select');
		var resa_qty = 0;
		for(var i=0;i<t_sel.length;i++) {
			resa_qty = resa_qty + t_sel[i].value*1;
		}
		if(resa_qty==0 || isNaN(resa_qty)) {
			alert(\"".$msg['resa_planning_alert_qty']."\");
			return false;
		}
		if(form.resa_deb.value >= form.resa_fin.value){
			alert(\"".$msg['resa_planning_alert_date']."\");
			return false;
	    }
		return true;
	}
</script>
<h3>".$msg['resa_date_planning']."</h3>
<form action='./do_resa.php' method='post' name='dates_resa'>
	<div>
		<label>".$msg['resa_planning_date_debut']."</label>
		&nbsp;
		<input type='hidden' name='resa_deb' value='".date('Y-m-d')."' />
		<input type='button' name='resa_deb_bt' id='resa_deb_bt' value='".date($msg['date_format'])."' onclick=\"window.open('./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb&param2=resa_deb_bt&auto_submit=NO&date_anterieure=NO', 'resa_deb', 'width=250,height=300,toolbar=no,dependent=yes,resizable=yes')\" />
		<img src='./images/calendar.jpg' 															   onclick=\"window.open('./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb&param2=resa_deb_bt&auto_submit=NO&date_anterieure=NO', 'resa_deb', 'width=250,height=300,toolbar=no,dependent=yes,resizable=yes')\" />
		&nbsp;
		<label>".$msg['resa_planning_date_fin']."</label>
		&nbsp;
		<input type='hidden' name='resa_fin' value='".date('Y-m-d')."' />
		<input type='button' name='resa_fin_bt' id='resa_fin_bt' value='".date($msg['date_format'])."' onclick=\"window.open('./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin&param2=resa_fin_bt&auto_submit=NO&date_anterieure=NO', 'resa_fin', 'width=250,height=300,toolbar=no,dependent=yes,resizable=yes')\" />
		<img src='./images/calendar.jpg' 															   onclick=\"window.open('./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin&param2=resa_fin_bt&auto_submit=NO&date_anterieure=NO', 'resa_fin', 'width=250,height=300,toolbar=no,dependent=yes,resizable=yes')\" />
		&nbsp;
		<input type='hidden' name='id_notice' value='$id_notice' />
		<input type='hidden' name='id_bulletin' value='$id_bulletin' />
		<input type='hidden' name='lvl' value='resa_planning' />
		<input type='hidden' name='connectmode' value='popup' />
	</div>
	<div>
		!!resa_loc_retrait!!
	</div>
	<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton' onClick='return test_form(this.form);' />
</form>";

$form_resa_ok = "
<br />
<span class='alerte'>".$msg['added_resa']."<br />".
$msg['resa_date_debut']."!!date_deb!!&nbsp;".$msg['resa_date_fin']."!!date_fin!!</span>";
		