<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.php,v 1.3 2019-07-05 13:25:14 btafforeau Exp $

namespace Sabre\PMB\ScanRequest;

use Sabre\PMB;

class Tree extends PMB\Tree {
	
	public function getRootNode(){
		$this->rootNode = new RootNode($this->config);
	}
	
	protected function get_restricted_objects_query() {
		return "select id_scan_request as object_id from scan_requests";
	}
}