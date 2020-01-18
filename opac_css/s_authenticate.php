<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: s_authenticate.php,v 1.1.6.2 2019-10-25 09:53:32 dbellamy Exp $
$base_path=".";

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if($opac_search_other_function){
    require_once($include_path."/".$opac_search_other_function);
}

if(!isset($autoloader) || !is_object($autoloader)){
    require_once($class_path.'/autoloader.class.php');
    $autoload = new autoloader();
}
require_once("$class_path/shorturl/shorturls.class.php");

if(isset($h)){
    try {
        shorturls::proceed($h);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
