<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: youtube_api.class.php,v 1.2 2012-02-17 09:17:37 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/curl.class.php");

class youtube_api {
	protected $url_api = "http://gdata.youtube.com/feeds/api/videos";
	public $format = "json";
	public $max_results = 10;
	
	public function search_videos($vars){
		if(!$vars['alt']){
			$vars['alt'] = $this->format;
		}
		if(!$vars['max-results']){
			$vars['max-results'] = $this->max_results;
		}
		if(!$vars['format']){
			$vars['format'] = 5;
		}
		
		$request ="";
		foreach($vars as $var => $val){
			if($request!="") $request.="&";
			$request.=$var."=".rawurlencode($val);
		}
		$search_url = $this->url_api."?".$request;
		$curl = new Curl();
		$json = $curl->get($search_url);
		return json_decode($json);
	}
	
}