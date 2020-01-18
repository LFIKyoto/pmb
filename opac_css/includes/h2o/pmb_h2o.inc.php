<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_h2o.inc.php,v 1.40.2.5 2019-11-20 14:07:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($include_path."/h2o/h2o.php");
require_once($include_path."/misc.inc.php");
require_once($include_path."/divers.inc.php");
require_once ($class_path."/cms/cms_module_root.class.php");
require_once ($class_path."/connecteurs.class.php");
require_once ($base_path."/admin/connecteurs/in/artevod/artevod.class.php"); //A voir pour ajouter tout les connecteurs pour les utiliser dans le lookup connectorsLookup;


class pmb_StringFilters extends FilterCollection {
	
	public static function limitstring($string, $max = 50, $ends = "[...]"){
		global $charset;
		$string = html_entity_decode ($string, ENT_NOQUOTES, $charset);
		
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

	// retourne le reste de $string à la position $start 
	public static function substr($string, $start) {
		if(!$string) return '';
		return substr($string, $start);
	}
	
	// retourne le nombre d'occurrence d'une chaine
	public static function substr_count($string, $needle) {
	    if(!$string) return '';
	    return substr_count($string,$needle);
	}
	
	// retourne le reste de $string après la premiere occurence de $needle
	public static function substring($string, $needle) {
		if(!$string) return '';
		if(!$needle) return $string;
		$str = strstr($string, $needle);
		if ($str) {
			return substr($str, strlen($needle));
		}
		return $string; 
	}
	
	// retourne le reste de $string jusqu'à la premiere occurence de $needle
	public static function substring_until($string, $needle) {
		if(!$string) return '';
		if(!$needle) return $string;
		$str = strpos($string, $needle);
		if ($str) {
			return substr($string, 0, $str);
		}
		return $string;
	}
	
	public static function addslashes($string) {
		return addslashes($string);
	}
	
	public static function empremium($url, $soc_code, $private_key) {
		global $empr_login;
		if (!$url || !$soc_code || !$private_key || !$empr_login) {
			return '';
		}
		return $url.'/'.$soc_code.'/'.$empr_login.'/'.md5(date('Ymd').$empr_login.$private_key);
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

class pmb_CoreFilters extends FilterCollection {

	public static function url_proxy($string, $from=''){
		global $opac_url_base;
		
		$url_proxy = $opac_url_base."pmb.php?url=".urlencode($string);
		if($from) {
			$url_proxy .= "&from=".$from;
		}
		$url_proxy .= "&hash=".md5($string.$from);
		return $url_proxy;
	}
}

class pmb_ArrayFilters extends FilterCollection {    
    public static function getItem($array, $indice){
        return $array[$indice];
    }
}

class pmb_OpacLinks extends FilterCollection {
        public static function work_link($id) {
            return "index.php?lvl=titre_uniforme_see&id=".rawurlencode($id);
        }
        
        public static function record_link($id) {
            return "index.php?lvl=notice_display&id=".rawurlencode($id);
        }
        
        public static function author_link($id) {
            return "index.php?lvl=author_see&id=".rawurlencode($id);
        }
}

class Sqlvalue_Tag extends H2o_Node{
	private $struct_name;
	
	
	public function __construct($argstring, $parser, $position){
		$this->struct_name = $argstring;
		$this->pmb_query = $parser->parse('endsqlvalue');
	}
	
	public function render($context,$stream){
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

	public function __construct($argstring, $parser, $position){
		$params = explode(" ",$argstring);
		$this->struct_name = $params[0];
		$this->endpoint = $params[1];
		$this->sparql_query = $parser->parse('endsparqlvalue');
	}

	public function render($context,$stream){
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

	public function __construct($argstring, $parser, $position){
		$this->id_tpl = $argstring;
		$this->pmb_notice = $parser->parse('endtplnotice');
	}

	public function render($context,$stream){
		global $class_path;
		$query_stream = new StreamWriter;
		$this->pmb_notice->render($context, $query_stream);
		$notice_id = $query_stream->close();
		$notice_id = (int) $notice_id;
		$query = "select count(notice_id) from notices where notice_id=".$notice_id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_result($result, 0)){
			require_once ("$class_path/notice_tpl_gen.class.php");
			$struct = array();
			$tpl=notice_tpl_gen::get_instance($this->id_tpl);
			$this->content=$tpl->build_notice($notice_id);
			$stream->write($this->content);
		}
	}
}

class Imgbase64_Tag extends H2o_Node{
    private $argument;
    public function __construct($argstring, $parser, $pos = 0) {
        $this->argument = $argstring;
    }
    
    public function render($context, $stream) {
        global $charset;
        $path = $this->argument;
        try{
            $stream->write("data:image/".pathinfo($path,PATHINFO_EXTENSION).";base64,".base64_encode(file_get_contents($path)));
        }catch(Exception $e){
        }
    }
}

class Etageresee_Tag extends H2o_Node{
    private $id_etagere;
    private $aff_notices_nb = 10;
    private $mode_aff_notice = AFF_ETA_NOTICES_BOTH;
    private $depliable = 1;
    private $link_to_etagere = 1;
    private $link = "./index.php?lvl=etagere_see&id=!!id!!";
    private $args = [];
    
    public function __construct($argstring, $parser, $position){
        $this->args = H2o_Parser::parseArguments($argstring);
        if(count($this->args) == 0 && count( $this->args) > 7 ){
            throw new TemplateSyntaxError('Etageresee demande des arguments');
        }
    }
    
    public function render($context,$stream){
        $speFolder = "";
        $this->id_etagere =  $context->resolve($this->args[0]);
        if (isset( $this->args[1])){
            $this->aff_notices_nb = $context->resolve($this->args[1]);
        }
        if (isset( $this->args[2])){
            $this->mode_aff_notice =  $context->resolve($this->args[2]);
            if(pmb_strlen($this->mode_aff_notice) > 1 && substr($this->mode_aff_notice, 0,1) == "9"){
                $tmp = explode(" ", $this->mode_aff_notice);
                $this->mode_aff_notice = 9;
                if($tmp[1]){
                    $speFolder = trim($tmp[1]);
                }
            }
        }else{
            global $opac_notices_format;
            $this->mode_aff_notice = $opac_notices_format;
        }
        if (isset( $this->args[3])){
            $this->depliable =  $context->resolve($this->args[3]);
        }
        if (isset( $this->args[4])){
            $this->link_to_etagere =  $context->resolve($this->args[4]);
        }
        if (isset( $this->args[5])){
            $this->link =  $context->resolve($this->args[5]);
        }
        
        if($speFolder){
            global $opac_notices_format_django_directory;
            $tmp = $opac_notices_format_django_directory;
            $opac_notices_format_django_directory = $speFolder;
            
        }
        $stream->write(contenu_etagere($this->id_etagere, $this->aff_notices_nb, $this->mode_aff_notice, $this->depliable, $this->link_to_etagere, $this->link));
        if($speFolder){
            $opac_notices_format_django_directory = $tmp;
        }
    }
}

class Recordsee_Tag extends H2o_Node{
    private $id_record;
    private $mode_aff_notice = AFF_ETA_NOTICES_BOTH;
    private $nocart = 0;
    private $gen_header = 1;
    private $depliable = 1;
    private $nodocnum = 0;
    private $enrichment=1;
    private $recherche_ajax_mode=0;
    private $show_map=1;
    private $args = [];
    
    public function __construct($argstring, $parser, $position){
        $this->args = H2o_Parser::parseArguments($argstring);
        if(count($this->args) == 0 && count( $this->args) > 9 ){
            throw new TemplateSyntaxError('Recordsee demande des arguments');
        }
    }
    
    public function render($context,$stream){
        $speFolder = "";
        $this->id_record =  $context->resolve($this->args[0]);
        if (isset( $this->args[1])){
            $this->mode_aff_notice =  $context->resolve($this->args[1]);
            if(pmb_strlen($this->mode_aff_notice) > 1 && substr($this->mode_aff_notice, 0,1) == "9"){
                $tmp = explode(" ", $this->mode_aff_notice);
                $this->mode_aff_notice = 9;
                if($tmp[1]){
                    $speFolder = trim($tmp[1]);
                }
            }
        }else{
            global $opac_notices_format;
            $this->mode_aff_notice = $opac_notices_format;
        }
        if (isset( $this->args[2])){
            $this->nocart = $context->resolve($this->args[2]);
        }
        if (isset( $this->args[3])){
            $this->gen_header = $context->resolve($this->args[3]);
        }
        if (isset( $this->args[4])){
            $this->depliable =  $context->resolve($this->args[4]);
        }
        if (isset( $this->args[5])){
            $this->nodocnum =  $context->resolve($this->args[5]);
        }
        if (isset( $this->args[6])){
            $this->enrichment =  $context->resolve($this->args[6]);
        }
        if (isset( $this->args[7])){
            $this->recherche_ajax_mode =  $context->resolve($this->args[7]);
        }
        if (isset( $this->args[8])){
            $this->show_map =  $context->resolve($this->args[8]);
        }
        if($speFolder){
            global $opac_notices_format_django_directory;
            $tmp = $opac_notices_format_django_directory;
            $opac_notices_format_django_directory = $speFolder;
            
        }
        $stream->write(aff_notice($this->id_record, $this->nocart, 0, $this->mode_aff_notice, $this->depliable, $this->nodocnum, $this->enrichment, $this->recherche_ajax_mode, $this->show_map, $speFolder));
        if($speFolder){
            $opac_notices_format_django_directory = $tmp;
        }
    }
}

function pmb_H2O_recurse_object($object,$property){
    if(is_object($object)){
        if ((isset($object->{$property}) || method_exists($object, '__get'))) {
            return $object->{$property};
        }
        if (method_exists($object, $property)) {
            return call_user_func_array(array($object, $property), array());
        }
        if (method_exists($object, "get_".$property)) {
            return call_user_func_array(array($object, "get_".$property), array());
        }
        if (method_exists($object, "get".ucfirst($property))) {
            return call_user_func_array(array($object, "get".ucfirst($property)), array());
        }
        if (method_exists($object, "is_".$property)) {
            return call_user_func_array(array($object, "is_".$property), array());
        }
        if (method_exists($object, "is".ucfirst($property))) {
            return call_user_func_array(array($object, "is".ucfirst($property)), array());
        }
    }
    return null;
}
function imgLookup($name, $context) {
	$value = null;
	$img = str_replace(":img.","",$name);
	if($img != $name) {
		$value = get_url_icon($img);
	}
	return $value;
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

function cmsLookup($name,$context){
	global $msg;
	$type = substr($name, strpos($name, ':')+1, strpos($name, '.')-1);
	$code = str_replace(":".$type.".","",$name);
	$obj = null;
	if($type == "article" || $type == "section"){
		$attributes = explode('.', $code);
		$id = array_shift($attributes);
		if($id && is_numeric($id)){
			$cms_class = 'cms_'.$type;
			$obj = new $cms_class($id);
			$obj = $obj->format_datas();
			for($i=0 ; $i<count($attributes) ; $i++){
				$attribute = $attributes[$i];
				if(is_array($obj)){
					$obj = $obj[$attribute];
				} else if(is_object($obj)){
					if (is_object($obj) && (isset($obj->{$attribute}) || method_exists($obj, '__get'))) {
						$obj = $obj->{$attribute};
					} else if (method_exists($obj, $attribute)) {
						$obj = call_user_func_array(array($obj, $attribute), array());
					} else if (method_exists($obj, "get_".$attribute)) {
						$obj = call_user_func_array(array($obj, "get_".$attribute), array());
					} else if (method_exists($obj, "is_".$attribute)) {
						$obj = call_user_func_array(array($obj, "is_".$attribute), array());
					} else {
						$obj = null;
					}
				} else{
					$obj = null;
					break;
				}
			}
		}
	}
	return $obj;
}

function globalLookup($name, $context) {
	$global = str_replace(":global.", "", $name);
	if ($global != $name) {
		global ${$global};
		
		if (isset(${$global})) {
			return ${$global};
		}
	}
	return null;
}

function recursive_lookup($name,$context) {
	$obj = null;
	$attributes = explode('.', $name);
	$first=true;
	// on fait une "récursion" sur chaque attribut
		for($i=0 ; $i<count($attributes) ; $i++){
			$attribute = $attributes[$i];
	    //le premier commence par ":"
	    if($i=== 0){
	        $attribute = substr($attributes[0], 1);
	    }
	    //Au premier coup, le premier attributt peut lui aussi être en "lazyload"
	    if($first){
	        //On regarde dans le contexte
    	    foreach ($context->scopes as $layers) {
       	    	if (isset($layers[$attribute])) {
    	    		$obj = $layers[$attribute];
    	    		$first = false;
    	    		break;
    	    	} 
    	        // Pour chaque élément poussé dans le contexte
        	    foreach ($layers as $layer){
        	        // On regarde si c'est dans un objet
        	        $obj = pmb_H2O_recurse_object($layer,$attribute);
        	        if($obj !== null){
        	            // On s'assure de ne pas repasser dans ce cas pour le reste de la "récursion"
        	            $first = false;
        	            break(2);
        	        }
        	    }
        	}
	    }else{
	        // La récupération d'un élement de tableau ne fonctionne que pour le premier "niveau", après c'est à vérifier à la main
			if(is_array($obj)){
	            if(isset($obj[$attribute])){
	                $obj = $obj[$attribute];
				} else {
					$obj = null;
				}
			} else{
	            $obj = pmb_H2O_recurse_object($obj,$attribute);
			}
		}
	    // Si obj est null à cet instant, on évite de continuer le traitement pour rien
	    if($obj === null){
	        return null;
		}
	}
	return $obj;
}


function session_varsLookup($name, $context) {
	global $id_empr,
	$empr_cb,
	$empr_nom,
	$empr_prenom,
	$empr_adr1,
	$empr_adr2,
	$empr_cp,
	$empr_ville,
	$empr_mail,
	$empr_tel1,
	$empr_tel2,
	$empr_prof,
	$empr_year,
	$empr_categ,
	$empr_codestat,
	$empr_sexe,
	$empr_login,
	$empr_ldap,
	$empr_location,
	$empr_date_adhesion,
	$empr_date_expiration,
	$empr_statut;
	
	$value = null;
	
	$datas['session_vars']['view'] = (isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
	$datas['session_vars']['id_empr'] = $_SESSION['id_empr_session'];
	$datas['session_vars']['empr_cb'] =$empr_cb;
	$datas['session_vars']['empr_nom'] = $empr_nom;
	$datas['session_vars']['empr_prenom'] = $empr_prenom;
	$datas['session_vars']['empr_adr1'] = $empr_adr1;
	$datas['session_vars']['empr_adr2'] = $empr_adr2;
	$datas['session_vars']['empr_cp'] = $empr_cp;
	$datas['session_vars']['empr_ville'] = $empr_ville;
	$datas['session_vars']['empr_mail'] = $empr_mail;
	$datas['session_vars']['empr_tel1'] = $empr_tel1;
	$datas['session_vars']['empr_tel2'] = $empr_tel2;
	$datas['session_vars']['empr_prof'] = $empr_prof;
	$datas['session_vars']['empr_year'] = $empr_year;
	$datas['session_vars']['empr_categ'] = $empr_categ;
	$datas['session_vars']['empr_codestat'] = $empr_codestat;
	$datas['session_vars']['empr_sexe'] = $empr_sexe;
	$datas['session_vars']['empr_login'] = $empr_login;
	$datas['session_vars']['empr_location'] = $empr_location;
	$datas['session_vars']['empr_date_adhesion'] = $empr_date_adhesion;
	$datas['session_vars']['empr_date_expiration'] = $empr_date_expiration;
	$datas['session_vars']['empr_statut'] = $empr_statut;
	
	$code = str_replace(":session_vars.","",$name);
	if ($code != $name && isset($datas['session_vars'][$code])) {
		$value = $datas['session_vars'][$code];
	}
	return $value;
}

function env_varsLookup($name, $context) {
	global $opac_url_base;

	$value = null;

	$datas['env_vars']['script'] = basename($_SERVER['SCRIPT_NAME']);
	$datas['env_vars']['request'] = basename($_SERVER['REQUEST_URI']);
	$datas['env_vars']['opac_url'] = $opac_url_base;
	$datas['env_vars']['browser'] = cms_module_root::get_browser();
	$datas['env_vars']['platform'] = cms_module_root::get_platform();
	$datas['env_vars']['server_addr'] = $_SERVER['SERVER_ADDR'];

	$code = str_replace(":env_vars.","",$name);
	if ($code != $name && isset($datas['env_vars'][$code])) {
		$value = $datas['env_vars'][$code];
	}
	return $value;
}

function connectorsLookup($name, $context) {
	global $base_path;
	$value = str_replace(":connectors.", "", $name);
	if ($value != $name) {
		$exploded_value = explode('.', $value);
		$connector_name = $exploded_value[0];
		$connectors = new connecteurs();
		$attribute = $exploded_value[1];
		$connectors_catalog = $connectors->catalog;
		$obj = null;
		foreach($connectors_catalog as $connector){
			if($connector['NAME'] == $connector_name){
				if (is_file($base_path."/admin/connecteurs/in/".$connector['PATH']."/".$connector_name.".class.php")){
					require_once($base_path."/admin/connecteurs/in/".$connector['PATH']."/".$connector_name.".class.php");
					$obj = new $connector_name($base_path."/admin/connecteurs/in/".$connector['PATH']);
					if(is_object($obj)){
						if (is_object($obj) && (isset($obj->{$attribute}) || method_exists($obj, '__get'))) {
							$obj = $obj->{$attribute};
						} else if (method_exists($obj, $attribute)) {
							$obj = call_user_func_array(array($obj, $attribute), array());
						} else if (method_exists($obj, "get_".$attribute)) {
							$obj = call_user_func_array(array($obj, "get_".$attribute), array());
						} else if (method_exists($obj, "is_".$attribute)) {
							$obj = call_user_func_array(array($obj, "is_".$attribute), array());
						} else {
							$obj = null;
						}
					}
				}
				break;
			}
		}
		return $obj;
	}
	return null;
}

class H2o_collection {
    protected static $h2o_collection;
    /**
     * 
     * @param string $file
     * @param array $options
     * @return H2o
     */
    public static function get_instance($file, $options = array()) {                
        if (!isset(static::$h2o_collection)) {
            static::$h2o_collection = array();
        }
        if (!isset(static::$h2o_collection[$file])) {
            static::$h2o_collection[$file] = array();
        }
        if (!isset(static::$h2o_collection[$file][serialize($options)])) {
            static::$h2o_collection[$file][serialize($options)] = new H2o($file, $options);
		} else {
			$e = new Exception();
			$trace = $e->getTrace();
			$loop = false;
			$count_trace = count($trace);
			for ($i = 2; $i < $count_trace; $i++) {
				if ($trace[$i]['function'] == $trace[1]['function']) {
				    $loop = true;
					if (isset($trace[$i]['argument']) && ($trace[$i]['argument'] != $trace[1]['argument'])) {
                        $loop = false;
					}
				}
			}
			if ($loop) {
				return new H2o($file, $options);
        	}
		}
        return static::$h2o_collection[$file][serialize($options)];
    }
    public static function addLookup($lookup) {
        if (is_callable($lookup)) {
            if (!in_array($lookup, H2o_Context::$lookupTable)) {
                H2o_Context::$lookupTable[] = $lookup;
            }
        } else {
            die('damm it');
        }
    }
}

h2o::addTag(array("sqlvalue"));
h2o::addTag(array("sparqlvalue"));
h2o::addTag(array("tplnotice"));
h2o::addTag(array("imgbase64"));
h2o::addTag(array("etageresee"));
h2o::addTag(array("recordsee"));

h2o::addFilter(array('pmb_StringFilters'));
h2o::addFilter(array('pmb_DateFilters'));
h2o::addFilter(array('pmb_CoreFilters'));
h2O::addFilter(array('pmb_OpacLinks'));
h2O::addFilter(array('pmb_ArrayFilters'));

H2o::addLookup("imgLookup");
H2o::addLookup("messagesLookup");
H2o::addLookup("globalLookup");
H2o::addLookup("cmsLookup");
H2o::addLookup("recursive_lookup");
H2o::addLookup("session_varsLookup");
H2o::addLookup("env_varsLookup");
H2o::addLookup("connectorsLookup");