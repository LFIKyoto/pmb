<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tache_rapport.tpl.php,v 1.2 2014-10-03 15:12:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

	// Default Params
	$param['font_face']		= 'Times New Roman, Verdana, Arial, Helvetica'; // Default font to use
	$param['font_size']		= 13; // Font size in px
	$param['bg_color']		= '#EEEEEE';
	$param['bg2color']		= '#DDDDDD';
	$param['today_bg_color']	= '#A0C0C0';
	$param['font_today_color']	= '#990000';
	$param['font_color']		= '#000000';
	$param['font_nav_bg_color']	= '#A9B4B3';
	$param['font_nav_color']	= '#FFFFFF';
	$param['font_header_color']	= '#FFFFFF';
	$param['border_color']	= '#3f6551';
	
$report_task = '<style type="text/css">
		<!--
		.cols_header { background-color : '.$param['bg_color'].'; width:40%; }
		.cols2header { background-color : '.$param['bg2color'].'; width:40%; }
		.cols_header2 { background-color : '.$param['bg_color'].'; width:60%; }
		.cols2header2 { background-color : '.$param['bg2color'].'; width:60%; }
		.rapportTop_!!id!! 	{  font-family: '.$param['font_face'].'; font-size: '.($param['font_size']+2).'px; font-style: normal;  }
		.rapportTache_!!id!! {  font-size: '.$param['font_size'].'px; border: 0px; overflow: auto; height:200px; }
		-->
		</style>';

//template report task
$report_task .= '
<div id="div_rapport_!!id!!" style="display:block; " class="rapportTop_!!id!!">
	<div class="right"><a href="#" onClick="parent.kill_frame(\'frame_notice_preview\');return false;"><img src="'.$base_path.'/images/close.gif" border="0" align="right"></a></div>
<br />
!!print_report!!
<b>'.$msg['planificateur_type'].' :</b>
<span class="header_title">!!type_tache_name!!</span>
<br />
<br />

<table>
	<tr>
		<td class="cols2header">!!planificateur_task_name!! :</td>
		<td class="cols2header2">!!libelle_task!!</td>
	</tr>
	<tr>
		<td class="cols_header">!!libelle_date_generation!! :</td>
		<td class="cols_header2">!!date_mysql!!</td>
	</tr>
	<tr>
		<td class="cols2header">!!libelle_date_derniere_exec!! :</td>
		<td class="cols2header2">!!date_dern_exec!!</td>
	</tr>
	<tr>
		<td class="cols_header">!!libelle_heure_derniere_exec!! :</td>
		<td class="cols_header2">!!heure_dern_exec!!</td>
	</tr>
	<tr>
		<td class="cols2header">!!libelle_date_fin_exec!! :</td>
		<td class="cols2header2">!!date_fin_exec!!</td>
	</tr>
	<tr>
		<td class="cols_header">!!libelle_heure_fin_exec!! :</td>
		<td class="cols_header2">!!heure_fin_exec!!</td>
	</tr>
	<tr>
		<td class="cols2header">!!libelle_statut_exec!! :</td>
		<td class="cols2header2">!!status!! (!!percent!!%)</td>
	</tr>
</table>
</div>
<div class="row">
	<div align="center"><label for="space"/>&nbsp;</label></div>
</div>
<div class="row">
	<div align="left" class="rapportExec">
		<table id="tache_report">
			<tr width="80%" align=center>
				<th>!!report_execution!!</th>
			</tr>
		</table>
	</div>
	<div align="left" class="rapportTache_!!id!!"  style="overflow:auto; height:200px;">
		!!rapport!!
	</div>
</div>
';

//template report task
$report_error= '
<div id="div_rapport_error" style="display:block; ">
	<br />
	<br />
	<div class="row">
		<div align="center"><h2>'.$msg["planificateur_report_error"].'</h2></div>
	</div>
</div>';