<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_small_text.class.php,v 1.1 2017-01-06 16:10:51 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_datatype_small_text
 * Les m�thodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont �ventuellement � red�finir pour le type de donn�es
 */
class onto_common_datatype_small_text extends onto_common_datatype {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
		if (is_string($this->value) && (strlen($this->value) < 256)) return true;
		return false;
	}
} // end of onto_common_datatype_small_text
