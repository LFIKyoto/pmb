<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: description.inc.php,v 1.2 2013-03-25 10:32:40 mbertin Exp $

$submenu.= "		
			".htmlspecialchars($class_param->descriptions[$quoi],ENT_QUOTES,$charset)."<br />
			<img src='$visionneuse_path/".$class_param->screenshoots[$quoi]."' title='$quoi' alt='$quoi' width='500px'/><br />
			mimetypes support&eacute;s :<br />
			<ul>";

foreach($class_param->classMimetypes[$quoi] as $mimetype){
$submenu.="
				<li>".htmlspecialchars($mimetype,ENT_QUOTES,$charset)."</li>
";	
}
$submenu.="				
			</ul>";
?>
