<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_objects.class.php,v 1.3.10.2 2019-10-30 08:15:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_object.class.php");

class contact_form_objects {
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Identifiant de l'objet s�lectionn�
	 * @var integer
	 */
	protected $selected;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->fetch_data();
	}
	
	/**
	 * Donn�es
	 */
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_object from contact_form_objects order by object_label';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new contact_form_object($row->id_object);
			}
		}
	}
	
	/**
	 * S�lecteur d'objets de mail
	 */
	public function gen_selector($email_object_free_entry=0) {
	    global $msg, $charset;
	    global $contact_form_objects_id;

		$selector = "<select name='contact_form_objects' data-dojo-type='dijit/form/Select' onchange='contact_form_object_change(this.value);' ";
		if (isset($contact_form_objects_id)) {
			$selector .= " data-dojo-props='value:".$contact_form_objects_id."'";
		}
		$selector .= " maxHeight='80'>";
		foreach ($this->objects as $object) {
		    $selector .= "<option value='".$object->get_id()."'>".$object->get_translated_label()."</option>";
		}
		if($email_object_free_entry) {
		    $selector .= "<option value='0'>".htmlentities($msg['contact_form_object_other'], ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public function get_objects() {
		return $this->objects;
	}
	
	public function get_selected() {
	    if(!isset($this->selected)) {
	        if(count($this->objects)) {
	            $this->selected = $this->objects[0]->get_id();
	        } else {
	            $this->selected = 0;
	        }
	    }
	    return $this->selected;
	}
	
	public function get_selected_object() {
	    foreach ($this->objects as $object) {
	        if($object->get_id() == $this->get_selected()) {
	            return $object;
	        }
	    }
	    return false;
	}
}