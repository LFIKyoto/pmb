<?php
include_once($class_path."/synchro_rdf.class.php");

function _get_header_($output_params) {
	return;
}

function _get_footer_($output_params) {
	
	$export=new synchro_rdf(session_id());
	$contenuRdf=$export->exportStoreXml();
	
	//Suppression des tables temporaires
	$res=pmb_mysql_query("SHOW TABLES LIKE '".session_id()."%'");
	while($row=pmb_mysql_fetch_array($res)){
		pmb_mysql_query("DROP TABLE ".$row[0]);
	}

	return $contenuRdf;
}

?>