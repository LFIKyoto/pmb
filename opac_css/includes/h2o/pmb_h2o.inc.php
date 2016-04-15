<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_h2o.inc.php,v 1.5.2.1 2015-09-30 16:27:20 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($include_path."/h2o/h2o.php");
require_once($include_path."/misc.inc.php");
require_once($include_path."/divers.inc.php");

class pmb_StringFilters extends FilterCollection {
	
	public static function limitstring($string, $max = 50, $ends = "[...]"){
		if(pmb_strlen($string)> $max){
			$string = pmb_substr($string,0,($max - pmb_strlen($ends))).$ends;
		}
		return $string;
	}
	
	public static function printf($string, $arg1, $arg2= "", $arg3= "", $arg4= "", $arg5= "", $arg6= "", $arg7= "", $arg8= "", $arg9= ""){
		return sprintf($string,$arg1,$arg2,$arg3,$arg4,$arg5,$arg6,$arg7,$arg8,$arg9);
	}
	
	public static function replace($string, $search, $replace) {
		return str_replace($search, $replace, $string);
	}
}

class pmb_DateFilters extends FilterCollection {
	
	public static function year($date){
		$cleandate = detectFormatDate($date);
		if($cleandate != "0000-00-00"){
			return date("Y",strtotime($cleandate));
		}
		return $date;
	}
	
	public static function month($date){
		$cleandate = detectFormatDate($date);
		if($cleandate != "0000-00-00"){
			return date("m",strtotime($cleandate));
		}
		return $date;
	}
	
	public static function monthletter($date){
		global $msg;
		$cleandate = detectFormatDate($date);
		if($cleandate != "0000-00-00"){
			return ucfirst($msg[strtolower(date("F",strtotime($cleandate)))]);
		}
		return $date;
	}
	
	public static function shortmonthletter($date){
		global $msg;
		$cleandate = detectFormatDate($date);
		if($cleandate != "0000-00-00"){
			return ucfirst($msg['short_'.strtolower(date("F",strtotime($cleandate)))]);
		}
		return $date;
	}
	
	public static function day($date){
		$cleandate = detectFormatDate($date);
		if($cleandate != "0000-00-00"){
			return date("d",strtotime($cleandate));
		}
		return $date;
	}
}

class Sqlvalue_Tag extends H2o_Node{
	private $struct_name;
	
	
	function __construct($argstring, $parser, $position){
		$this->struct_name = $argstring;
		$this->pmb_query = $parser->parse('endsqlvalue');
	}
	
	function render($context,$stream){
		global $dbh;
		
		$query_stream = new StreamWriter;
		$this->pmb_query->render($context, $query_stream);
		$query = $query_stream->close();
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$struct =array();
			while ($row = pmb_mysql_fetch_assoc($result)){
				$struct[]=$row;
			}
			$context->set($this->struct_name,$struct);
		}else{
			$context->set($this->struct_name,0);
		}
	}
}

class Sparqlvalue_Tag extends H2o_Node{
	private $struct_name;
	private $endpoint;

	function __construct($argstring, $parser, $position){
		$params = explode(" ",$argstring);
		$this->struct_name = $params[0];
		$this->endpoint = $params[1];
		$this->sparql_query = $parser->parse('endsparqlvalue');
	}

	function render($context,$stream){
		global $dbh;
		global $class_path;

		$query_stream = new StreamWriter;
		$this->sparql_query->render($context, $query_stream);
		$query = $query_stream->close();
		
		require_once ("$class_path/rdf/arc2/ARC2.php");
		$config = array(
			'remote_store_endpoint' => $this->endpoint,
			'remote_store_timeout' => 10
		);
		$store = ARC2::getRemoteStore($config);
		$context->set($this->struct_name,$store->query($query,'rows'));
	}
}

class Tplnotice_Tag extends H2o_Node{
	private $id_tpl;

	function __construct($argstring, $parser, $position){
		$this->id_tpl = $argstring;
		$this->pmb_notice = $parser->parse('endtplnotice');
	}

	function render($context,$stream){
		global $dbh;
		global $class_path;
		$query_stream = new StreamWriter;
		$this->pmb_notice->render($context, $query_stream);
		$notice_id = $query_stream->close();
		$notice_id = $notice_id+0;
		$query = "select count(notice_id) from notices where notice_id=".$notice_id;
		$result = mysql_query($query,$dbh);
		if($result && mysql_result($result, 0)){
			require_once ("$class_path/notice_tpl_gen.class.php");
			$struct = array();
			$tpl=new notice_tpl_gen($this->id_tpl);
			$this->content=$tpl->build_notice($notice_id);
			$stream->write($this->content);
		}
	}
}

function messagesLookup($name, $context) {
	global $msg;
	$value = null;
	$code = str_replace(":msg.","",$name);
	if ($code != $name && isset($msg[$code])) {
		$value = $msg[$code]; 
	}
	return $value; 
}

function globalLookup($name, $context) {
	$global = str_replace(":global.", "", $name);
	if ($global != $name) {
		global $$global;
		
		if (isset($$global)) {
			return $$global;
		}
	}
	return null;
}

h2o::addTag(array("sqlvalue"));
h2o::addTag(array("sparqlvalue"));
h2o::addTag(array("tplnotice"));
h2o::addFilter(array('pmb_StringFilters'));
h2o::addFilter(array('pmb_DateFilters'));

H2o::addLookup("messagesLookup");
H2o::addLookup("globalLookup");