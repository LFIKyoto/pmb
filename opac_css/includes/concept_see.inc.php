<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_see.inc.php,v 1.3.2.1 2015-10-16 12:25:16 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/autoloader.class.php");
if(!is_object($autoloader)){
	$autoloader = new autoloader();
}

$controler = new skos_page_concept($id);
$controler->proceed();

rec_last_authorities();

//FACETTES
//gestion des facette si active
require_once($base_path.'/classes/facette_search.class.php');
$records = "";
if(count($controler->get_indexed_notices())){
	$records= implode(",",$controler->get_indexed_notices());

	if(!$opac_facettes_ajax){
		$str .= facettes::make_facette($records);
	}else{
		$_SESSION['tab_result']=$records;
		$str .=facettes::get_facette_wrapper();
		$str .="<div id='facette_wrapper'><img src='./images/patience.gif'/></div>";
	
		$str .="
			<script type='text/javascript'>
				var req = new http_request();
				req.request(\"./ajax.php?module=ajax&categ=facette&sub=call_facettes\",false,null,true,function(data){
					document.getElementById('facette_wrapper').innerHTML=data;
				});
			</script>";
	}
	//Formulaire "FACTICE" pour l'application du comparateur et du filetre multiple...
	$str.= '
<form name="form_values" style="display:none;" method="post" action="?lvl=more_results&mode=extended">
	<input type="hidden" name="from_see" value="1" />
	'.facette_search_compare::form_write_facette_compare().'
</form>';
}