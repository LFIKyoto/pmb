<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: class_dispo.inc.php,v 1.3 2013-03-25 10:32:40 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if($quoi == ""){
	//si $quoi non défini on prend le premier de la liste...
	foreach($class_param->classMimetypes as $class => $mimetypes){
		$quoi =$class;
		break;
	}
}

$submenu="
<table width:auto;>
	<tr>
		<td style='width:20%' valign='top'>
			<div class='vmenu'>";
foreach($class_param->classMimetypes as $class => $mimetypes){
	$link ="categ=visionneuse&sub=class&quoi=$class";
	$submenu.="
				<span".ongletSelect($link).">
					<a title='$class' href='./admin.php?categ=visionneuse&sub=class&quoi=$class&vue=desc'>
						$class
					</a>
				</span>";
}
$submenu.="
			</div>
		</td>
		<td style='width:80%'>";
		
$submenu.="		
			<div class='hmenu'>
				<span".($vue == "desc" ? " class=\"selected\"":"").">
					<a title='desc' href='./admin.php?categ=visionneuse&sub=class&quoi=$quoi&vue=desc'>
						".htmlspecialchars($msg["planificateur_task_desc"],ENT_QUOTES,$charset)."
					</a>
				</span>
				<span".($vue == "param" ? " class=\"selected\"":"").">
					<a title='param' href='./admin.php?categ=visionneuse&sub=class&quoi=$quoi&vue=param'>
						".htmlspecialchars($msg["33"],ENT_QUOTES,$charset)."
					</a>
				</span>
			</div>";		
		switch ($vue){
			case "desc" :
				include('./admin/visionneuse/class_dispo/description.inc.php');
				break;
			case "param" :
				include('./admin/visionneuse/class_dispo/param.inc.php');
				break;
			default :
				include('./admin/visionneuse/class_dispo/description.inc.php');
				break;				
		}
$submenu.="
		</td>
	</tr>
</table>
";


print $submenu;
?>