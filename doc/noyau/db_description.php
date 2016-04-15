<?php 

include 'db_doc.php';
$doc=new db_doc();
$doc->parsage();
$t_table=$doc->get_table();
$t_relation=$doc->get_relation();
	
print "<style>
td {font-size:12px;}
</style>
	";


if(isset($_GET["table"])) {
	$table=$_GET["table"];
} else {
	$table='';
}
if(isset($_GET["table_old"])) {
	$table_old=$_GET["table_old"];
} else {
	$table_old='';
}

if ($table=="") 
	print "<center>S&eacute;lectionnez une table</center>";
	
else
{
	// En-tête du tableau
	print "<table width=100% border=1>";
	print "<tr><td colspan=9 align=center bgcolor=#CCCCCC><b>".$t_table[$table]['NAME']."</b></td></tr>\n";
	print "<tr><td colspan=9 align=center bgcolor=#EEEEEE><i>".$t_table[$table]['DESC']."</i></td></tr>\n";
	print "<tr><td align=center>Nom champ</td><td align=center>Description</td><td align=center>Type</td><td align=center>Sign&eacute;</td><td colspan=3 align=center>Infos. compl&eacute;mentaires</td><td align=center>R&eacute;f. &agrave; d'autres tables</td><td align=center>Valeur par d&eacute;faut</td></tr>\n";
	$colums=$t_table[$table]['ATTRS'];
	// Complétion du tableau
	foreach ($colums as $k=>$v)
	{
		$lien="";
		if(count($t_table[$v['REF']])) {
			foreach ($t_table[$v['REF']]['ATTRS']as $key=>$val)
			{
				if($val['KEY']=="Cl&eacute; primaire"){
					$lien=".".$t_table[$v['REF']]['ATTRS'][$key]['NAME'];
				}
			}
		}
		print "<tr>";
		print "<td><b>".$v['NAME']."</b></td>";
		print "<td><i>".$v['DESC']."</i></td>";
		print "<td>".$v['TYPE']."</td>";
		print "<td>".$v['SIGNE']."</td>";
		print "<td>".$v['KEY']."</td>";
		print "<td>".(($v[4])?$v[4]:'')."</td>";
		print "<td>".(($v[1])?$v[1]:'')."</td>";
		print "<td>"."<a href='db_description.php?table=".$v['REF']."&table_old=".$table."'onclick=\"parent.tables.location='index_table.php#".$v['REF']."'\">".$t_table[$v['REF']]['NAME']."</a>".$lien."</td>";
		print "<td>".$v['DEFVAL']."</td>";
		print "</tr>\n";
	}
	print "</table>";
		if ($table_old!="") print "<center><i><a href='db_description.php?table=".$table_old."' onclick=\"parent.tables.location='index_table.php#".$table_old."'\">Retour à la table ".$t_table[$table_old][NAME]."</a></i></center>";
	
}
?>