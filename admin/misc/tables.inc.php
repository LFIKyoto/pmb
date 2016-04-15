<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tables.inc.php,v 1.11 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// on récupére la liste des tables

$result = pmb_mysql_query("SHOW TABLES FROM ".DATA_BASE);
$i = 0;

while($i < pmb_mysql_num_rows($result)) {
	$table[$i] = pmb_mysql_tablename($result, $i);

	$desc[$i] = "<table >";
	$desc[$i] .= "<tr><th><strong>Field</strong></th><th><strong>Type</strong></th><th><strong>Null</strong></th><th><strong>Key</strong></th><th><strong>Default</strong></th><th><strong>Extra</strong></th></tr>";

	$requete = "DESCRIBE $table[$i]";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr = pmb_mysql_num_rows($res);

	if($nbr) {
		$odd_even=1;
		for($j=0;$j<$nbr;$j++) {
			$row=pmb_mysql_fetch_row($res);
			if ($odd_even==0) {
				$pair_impair = "odd";
				$odd_even=1;
			} else if ($odd_even==1) {
					$pair_impair = "even";
					$odd_even=0;
			}
			
			$desc[$i] .=  "<tr class='$pair_impair'>";
			for($h=0;$h < 6;$h++) {
				if(empty($row[$h])) $row[$h] = "&nbsp;";
				$desc[$i] .= "<td class='strip'>$row[$h]</td>";
			}
			$desc[$i] .= "</tr>";
		}
	}

	$desc[$i] .= "</table>";
	$i++;
}



// création du script
?>
<script type="text/javascript">

	function show_table(table,cle)
	{
	var content = new Array();
<?php

while(list($cle,$valeur)=each($desc)) {
	print "content[$cle] = \"$valeur\";\n";
}

?>
		if(document.getElementById(table).innerHTML.length == 0) {
			document.getElementById(table).innerHTML = content[cle];
		} else {
			document.getElementById(table).innerHTML = "";
		}
	}
</script>

<?php

// affichage du résultat
while(list($cle,$valeur)=each($table)) {
	print "<a href=\"javascript:show_table('$valeur',$cle)\">$valeur</a><br /><span id='$valeur'></span>\n";
	}

print "<p><small>$msg[717]</small></p>";
