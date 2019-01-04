<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: last_records.tpl.php,v 1.14 2018-01-25 10:13:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $last_records_header;
global $last_records_footer;

// template for PMB OPAC

$last_records_header = "
	<div id='last_entries'>
		<h3><span>$msg[last_entries]</span></h3>
		<div id='last_entries-container'>$msg[last_records_intro]<br />";

$last_records_footer ="			
		</div>
	</div>";

