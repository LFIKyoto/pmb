<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.inc.php,v 1.2 2014-02-06 15:30:20 arenou Exp $

require_once($class_path."/storages/storages.class.php");

switch($sub){
	case "upload" :
		$storage = storages::get_storage_class($id);
		if($storage){
			$success = $storage->upload_process();
		}
		if($success){
			switch($type){
				case 'collection' :
					require_once($class_path."/cms/cms_collections.class.php");
					$collection = new cms_collection($id_collection);
					print $collection->add_document($storage->get_uploaded_fileinfos(),true,$from);
					break;
			}
		}
		break;
	default :
		switch($action){
			case "get_params_form" :
				$storages = new storages();
				print $storages->get_params_form($class_name,$id);
				break;
		}
		break;
}