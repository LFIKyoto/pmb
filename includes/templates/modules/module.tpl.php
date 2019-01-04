<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module.tpl.php,v 1.3 2018-11-21 21:11:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//    ----------------------------------
// $admin_layout : layout page module
$module_layout = "
<!-- conteneur -->
<div id='conteneur'  class='$current_module'>
	!!left_menu!!
	<!-- contenu -->
	<div id='contenu'>
		!!menu_contextuel!!
";

$module_sub_tabs = "
<div class='hmenu'>
	!!sub_tabs!!
</div>
";

// $module_layout_end : layout page module (fin)
$module_layout_end = '
	</div>
<!-- /conteneur -->
</div>
';

?>