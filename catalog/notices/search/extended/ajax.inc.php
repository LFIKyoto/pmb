<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.inc.php,v 1.7 2018-10-12 13:13:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

if(!isset($search_xml_file)) $search_xml_file = '';
if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';

$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
$sc->proceed_ajax();

