<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fb.php,v 1.11 2013-09-09 07:28:06 apetithomme Exp $
$base_path=".";
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
require_once($base_path.'/includes/opac_db_param.inc.php');

$args = explode("&", $url);
$url_location = $args[0]; 
for($i=1; $i<count($args);$i++) {
	$key_value = explode("=",$args[$i]);
	$$key_value[0] = $key_value[1];
}

print "
<html xmlns='http://www.w3.org/1999/xhtm'
      xmlns:og='http://ogp.me/ns#'
      xmlns:fb='http://www.facebook.com/2008/fbml' charset='".$charset."'>
	<head>
		<meta name='title' content='".htmlentities(stripslashes($title),ENT_QUOTES,$charset)."' />
		<meta name='description' content='".htmlentities(stripslashes($desc),ENT_QUOTES,$charset)."' />
		<title>".htmlentities(stripslashes($title),ENT_QUOTES,$charset)."</title>
		
		<script type='text/javascript'>
			document.location='".htmlentities($url_location,ENT_QUOTES,$charset).($id ? "&id=$id" : "")."'
		</script>
	</head>
</html>";
?>
