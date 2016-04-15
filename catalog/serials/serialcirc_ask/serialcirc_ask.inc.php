<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.inc.php,v 1.2 2011-11-24 16:30:07 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc_ask.class.php");

switch($sub){		
	case 'circ_ask':		
		switch($action){	
			case 'accept':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->accept();
				}				
			break;		
			case 'refus':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->refus();
				}				
			break;		
			case 'delete':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->delete();
				}				
			break;				
		}			
		$asklist=new serialcirc_asklist($location_id,$type_filter,$statut_filter);
		print $asklist->get_form_list();
	break;		
	default :			
		$asklist=new serialcirc_asklist($location_id,$type_filter,$statut_filter);
		print $asklist->get_form_list();
	break;		
	
}



