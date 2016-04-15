<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_watches.class.php,v 1.5 2015-04-03 11:16:21 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/docwatch_root.class.php");
require_once($class_path."/docwatch/docwatch_watch.class.php");

/**
 * class docwatch_watches
 * 
 */
class docwatch_watches extends docwatch_root{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	public $id;
	public $type="category";
	public $title;
	public $num_parent;
	public $children = array();
	public $watches = array();
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$this->fetch_datas();
	} // end of member function __construct
	
	
	/**
	 * Fetch datas
	 * 
	 */
	public function fetch_datas(){
		global $dbh;
		
		if ($this->id) {
			$query = "select category_title, category_num_parent from docwatch_categories where id_category=".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if ($row = pmb_mysql_fetch_object($result)) {
				$this->title = $row->category_title;
				$this->num_parent = $row->category_num_parent;
			}
		} else {
			$this->title = "Racine";
			$this->num_parent = -1;
		}
		$query = "select id_watch from docwatch_watches where watch_num_category=".$this->id;
		$result = pmb_mysql_query($query,$dbh);
		while($row = pmb_mysql_fetch_object($result)) {
			$docwatch_watch = new docwatch_watch($row->id_watch);
			//Gestion des droits utilisateurs (on affiche uniquement les veilles paramétrées pour le current user)
			if(in_array(SESSuserid,$docwatch_watch->get_allowed_users())){
				$this->watches[] = $docwatch_watch->get_informations();
			}
		}
		$query = "select id_category from docwatch_categories where category_num_parent=".$this->id;
		$result = pmb_mysql_query($query,$dbh);
		while($row = pmb_mysql_fetch_object($result)) {
			$this->children[] = new docwatch_watches($row->id_category);
		}
	}
	

} // end of docwatch_watches
