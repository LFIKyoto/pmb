<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_search.class.php,v 1.2 2015-04-17 14:11:31 ngantier Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/search.class.php");
require_once("$class_path/newrecords_flux.class.php");

class shorturl_type_search {
	private $notices_list;
	
	public function generate_rss($context,$hash){
		global $opac_url_base,$dbh;
		
		$this->notices_list=array();
		$mc=unserialize($context);
		$search=new search($mc["search_type"]);		
		$search->unserialize_search(serialize($mc["serialized_search"]));
		$table = $search->make_search();		
		
		$q="select distinct notice_id from $table ";
		$res = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($res)){
			while ($row = pmb_mysql_fetch_object($res)){
				$this->notices_list[]= $row->notice_id;
			}
		}
		$flux = new newrecords_flux(0) ;
		$flux->setRecords($this->notices_list) ;
		$flux->setLink($opac_url_base."s.php?h=$hash") ;
		$flux->setDescription(strip_tags($mc["human_query"])) ;
		$flux->xmlfile() ;
		if(!$flux->envoi )return;
		@header('Content-type: text/xml');
		echo $flux->envoi ;
	}
	
	public function generate_pdf($context){

	}	
} // end of class

