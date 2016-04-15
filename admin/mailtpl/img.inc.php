<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: img.inc.php,v 1.1 2012-07-05 14:33:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/files_gestion.class.php");

$img=new files_gestion($pmb_img_folder,$pmb_img_url);	

switch($action) {
	case 'upload':
		$img->upload();		
	break;	
	case 'delete':
		$img->delete($filename);
	break;
	default:
	break;
}

print $img->get_list("admin.php?categ=mailtpl&sub=img");
print $img->get_error();