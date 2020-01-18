<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_func.class.php,v 1.2 2019-08-20 09:18:41 btafforeau Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;

require_once($base_path."/selectors/classes/selector_marc_list.class.php");
require_once($class_path."/marc_table.class.php");

class selector_func extends selector_marc_list {
	
	public function __construct($user_input = '') {
		parent::__construct($user_input);
	}
	
	protected function get_marc_list_instance() {
		global $s_func;
		if (empty($s_func)) {
			$s_func = new marc_list('function');
		}
		return $s_func;
	}
}
?>