<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesCommons.class.php,v 1.5 2019-07-05 13:37:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesCommons extends external_services_api_class{

	public function get_unimarc_labels() {
		global $lang,$base_path;

		$fields=array();
		$t=_parser_text_no_function_(file_get_contents($base_path."/external_services/pmbesCommons/codes.xml"),"UNILABELS");
		for ($i=0; $i<count($t["FIELD"]); $i++) {
			$f=$t["FIELD"][$i];
			$c=$f["CODE"];
			if ($f["LABEL"]) $fields[$c]["label"]=utf8_normalize($f["LABEL"]);
			if (count($f["SUBFIELD"])) {
				for ($j=0; $j<count($f["SUBFIELD"]); $j++) {
					$sf=$f["SUBFIELD"][$j];
					$fields[$c]["subfields"][$sf["CODE"]][]=utf8_normalize($sf["LABEL"]);
				}
			}
		}
		return serialize($fields);
	}
}


?>