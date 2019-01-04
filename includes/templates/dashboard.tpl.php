<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.tpl.php,v 1.1 2014-01-07 10:16:16 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$dashboard_menu = "";

$dashboard_layout = "
<div id='conteneur' class='dashboard'>
<script type='text/javascript'>dojo.require('dojox.layout.ContentPane');</script>
$dashboard_menu
";

$dashboard_layout_end = '
</div>
';