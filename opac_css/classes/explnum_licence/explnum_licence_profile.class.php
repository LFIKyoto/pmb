<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_profile.class.php,v 1.2 2017-07-27 10:09:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/explnum_licence/explnum_licence.class.php');
require_once($class_path.'/explnum_licence/explnum_licence_right.class.php');
require_once($class_path.'/translation.class.php');

/**
 * Classe de gestion des profils de régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence_profile {
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé du profil de régime de licence
	 * @var string
	 */
	protected $label;
	
	/**
	 * URI
	 * @var string
	 */
	protected $uri;
	
	/**
	 * Droits associés
	 * @var explnum_licence_right
	 */
	protected $rights;
	
	/**
	 * Identifiant du régime de licence
	 * @var int $explnum_licence_num
	 */
	protected $explnum_licence_num;
	
	/**
	 * URL du logo
	 * @var string
	 */
	protected $logo_url;
	
	/**
	 * Phrase d'explication
	 * @var string
	 */
	protected $explanation;
	
	/**
	 * Droits de citation
	 * @var string $quotation_rights
	 */
	protected $quotation_rights;
	
	public function __construct($id = 0) {
		$this->id = $id*1;
	}
	
	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select 
				explnum_licence_profiles.explnum_licence_profile_explnum_licence_num, 
				explnum_licence_profiles.explnum_licence_profile_label, 
				explnum_licence_profiles.explnum_licence_profile_uri, 
				explnum_licence_profiles.explnum_licence_profile_logo_url, 
				explnum_licence_profiles.explnum_licence_profile_explanation, 
				explnum_licence_profiles.explnum_licence_profile_quotation_rights 
				from explnum_licence_profiles
				where explnum_licence_profiles.id_explnum_licence_profile = '.$this->id;
		
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {			
			$this->explnum_licence_num = $row['explnum_licence_profile_explnum_licence_num'];
			$this->label = translation::get_text($this->id, 'explnum_licence_profiles', 'explnum_licence_profile_label', $row['explnum_licence_profile_label']);
			$this->uri = translation::get_text($this->id, 'explnum_licence_profiles', 'explnum_licence_profile_uri', $row['explnum_licence_profile_uri']);
			$this->logo_url = translation::get_text($this->id, 'explnum_licence_profiles', 'explnum_licence_profile_logo_url', $row['explnum_licence_profile_logo_url']);
			$this->explanation = translation::get_text($this->id, 'explnum_licence_profiles', 'explnum_licence_profile_explanation', $row['explnum_licence_profile_explanation']);
			$this->quotation_rights = translation::get_text($this->id, 'explnum_licence_profiles', 'explnum_licence_profile_quotation_rights', $row['explnum_licence_profile_quotation_rights']);
		}
	}
	
	public function set_explnum_licence_num($explnum_licence_num) {
		$this->explnum_licence_num = $explnum_licence_num*1;
		return $this;
	}
	
	public function is_used() {
		$result = pmb_mysql_query('select explnum_licence_profile_explnums_explnum_num from explnum_licence_profile_explnums where explnum_licence_profile_explnums_profile_num = '.$this->id.' limit 1');
		if (pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return explnum_licence_right
	 */
	public function get_rights(){
		if(!isset($this->rights)){
			$this->rights = array();
			$query = 'select explnum_licence_right_num
					from explnum_licence_profile_rights 
					where explnum_licence_profile_num = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					$this->rights[$row['explnum_licence_right_num']] = new explnum_licence_right($row['explnum_licence_right_num']);
				}
			}
		}
		return $this->rights;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		if (!isset($this->label)) {
			$this->fetch_data();
		}
		return $this->label;
	}
	
	public function get_logo_url() {
		if (!isset($this->logo_url)) {
			$this->fetch_data();
		}
		return $this->logo_url;
	}
	
	public function get_uri() {
		if (!isset($this->uri)) {
			$this->fetch_data();
		}
		return $this->uri;
	}
	
	public function get_explanation(){
		return $this->explanation;
	}
	
	public function get_quotation_rights(){
		return $this->quotation_rights;
	}
}