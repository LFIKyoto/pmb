<?php

$v_state=urldecode($v_state);
// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

print "<br /><br /><h2 align='center'>".htmlentities($msg["cleaning_opac_search_cache"], ENT_QUOTES, $charset)."</h2>";

$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["cleaning_opac_search_cache"], ENT_QUOTES, $charset)." : ";
$query = "truncate table search_cache";
if(pmb_mysql_query($query)){
	$query = "optimize table search_cache";
	if(pmb_mysql_query($query)){
		$v_state.= "OK";
	}else{
		$v_state.= "OK";
	}
}else{
	$v_state.= "KO";
}
$spec = $spec - CLEAN_OPAC_SEARCH_CACHE;

// mise à jour de l'affichage de la jauge
print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
  			<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
 			<div align='center'>100%</div>";
print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		<input type='hidden' name='pass2' value=\"2\">	
	</form>
	<script type=\"text/javascript\"><!--
		document.forms['process_state'].submit();
		-->
	</script>";