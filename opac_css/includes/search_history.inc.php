<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_history.inc.php,v 1.19.2.2 2015-10-09 13:15:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/rec_history.inc.php");
if ($_SESSION["nb_queries"]) {
	print "<script>
		var history_all_checked = false;
		
		function check_uncheck_all_history() {
			if (history_all_checked) {
				setCheckboxes('cases_a_cocher', 'cases_suppr', false);
				history_all_checked = false;
				document.getElementById('show_history_checked_all').value = '".$msg["show_history_check_all"]."';
				document.getElementById('show_history_checked_all').title = '".$msg["show_history_check_all"]."';
			} else {
				setCheckboxes('cases_a_cocher', 'cases_suppr', true);
				history_all_checked = true;
				document.getElementById('show_history_checked_all').value = '".$msg["show_history_uncheck_all"]."';
				document.getElementById('show_history_checked_all').title = '".$msg["show_history_uncheck_all"]."';
			}
			return false;
		}
		
		function setCheckboxes(the_form, the_objet, do_check) {
			 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
			 if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
			 		elts[i].checked = do_check;
			 	} // end for
			 } else {
			 	elts.checked = do_check;
			 } 
			 return true;
		}
						
		function verifCheckboxes(the_form, the_objet) {
			var bool=false;
			var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			var elts_cnt  = (typeof(elts.length) != 'undefined')
	                  ? elts.length
	                  : 0;
	
			if (elts_cnt) {
					
				for (var i = 0; i < elts_cnt; i++) { 		
					if (elts[i].checked)
					{
						bool = true;
					}
				}
			} else {
					if (elts.checked)
					{
						bool = true;
					}
			}
			return bool;
		} 
	</script>";

	print "<div id='history_action'>";
	print "<input type='button' class='bouton' id='show_history_checked_all' value=\"".$msg["show_history_check_all"]."\" onClick=\"check_uncheck_all_history();\" />&nbsp;";
	print "<input type='button' class='bouton' value=\"".$msg["suppr_elts_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ document.cases_a_cocher.submit(); return false;}\" />&nbsp;";
	print "</div>";
}


print "<h3 class='title_history'><span>".$msg["history_title"]."</span></h3>";

print "<form name='cases_a_cocher' method='post' action='./index.php?lvl=search_history&raz_history=1'>";

if ($_SESSION["nb_queries"]!=0) {
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
			print "<input type=checkbox name='cases_suppr[]' value='$i'><b>$i)</b> ";
			print "<a href=\"./index.php?lvl=search_result&get_query=$i\">".get_human_query($i)."</a><br /><br />";
		}
	}
} else {
	print "<b>".$msg["histo_empty"]."</b>";	
}

print "</form>";
?>