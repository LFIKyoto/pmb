<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_records_doctypes.class.php,v 1.1.2.1 2015-09-30 14:17:59 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_records_doctypes extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_doctypes"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			global $dbh;
			
			$this->value = array();
			$sub = $this->get_selected_sub_selector();
			$doctypes = $sub->get_value();
			if (is_array($doctypes) && count($doctypes)) {
				$query = "select notice_id from notices where typdoc in ('".implode("','", $doctypes)."')";
				$result = pmb_mysql_query($query, $dbh);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$this->value[] = $row->notice_id;
					}
				}
			}
		}
		return $this->value;
	}
}