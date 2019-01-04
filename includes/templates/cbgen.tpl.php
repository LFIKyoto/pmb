<?php

// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cbgen.tpl.php,v 1.6 2017-11-07 15:33:07 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;


//	----------------------------------

// $cb_gen_menu : menu page g�n�ration de codes-barres

$cbgen_menu = "
<div><table class=\"menu\" border=\"0\">
	<tr>
		<td>
	<tr>
		<td>
			<a href=\"./cbgen.php\">$msg[804]</a><br />
		</td>
	</tr>
</table></div>
";


//	----------------------------------

// $cbgen_layout : layout page g�n�ration de codes-barres

$cbgen_layout = "
<div id='contenu'><table class='document' border='0'>
	<tr>
	<td style='vertical-align:top'>
	<!-- side-bar -->
		<table border='0'>
			<tr>
				<td class='formtitle'>
					$msg[805]
				</td>
			</tr>
			<tr>
				<td>
					$cbgen_menu
				</td>
			</tr>
		</table>
	</td>
	<td style='vertical-align:top'>
";

//	----------------------------------

// $cb_layout_end : layout page g�n�ration de codes-barres (fin)

$cbgen_layout_end = '
	</td>
	</tr>
</table></div>
';


