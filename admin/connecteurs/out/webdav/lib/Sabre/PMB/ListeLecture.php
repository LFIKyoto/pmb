<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ListeLecture.php,v 1.1 2016-03-31 08:55:44 dgoron Exp $
namespace Sabre\PMB;

class ListeLecture extends Collection {
	protected $liste_lecture;

	function __construct($name,$config) {
		parent::__construct($config);
		$this->type = "liste_lecture";
		$code = $this->get_code_from_name($name);
		$id = substr($code,1);
		if($id){
			$this->liste_lecture = new \liste_lecture($id);
		}
	}
	
	function getName() {
		return $this->format_name($this->liste_lecture->nom_liste." (L".$this->liste_lecture->id_liste.")");
	}
	
	function getNotices(){
		$this->notices = array();
		if($this->liste_lecture->id_liste){
			//notice
			$query = "select notices_associees from opac_liste_lecture where id_liste = ".$this->liste_lecture->id_liste;
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$notices = explode(',', $row->notices_associees);
				if(is_array($notices) && count($notices)) {
					$query = "select notice_id from notices where notice_id in (".implode(',', $notices).")";
					$this->filterNotices($query);
				}
			}
		}
		return $this->notices;
	}
}