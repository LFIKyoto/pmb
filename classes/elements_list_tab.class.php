<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_list_tab.class.php,v 1.6 2017-12-08 10:19:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class elements_list_tab {
	
	/**
	 * Nom de l'onglet
	 * @var string
	 */
	private $name;
	
	/**
	 * Libell� de l'onglet
	 * @var string
	 */
	private $label; 
	
	/**
	 * Type de contenu de l'onglet ('authorities' ou 'records')
	 * @var string
	 */
	private $content_type;
	
	/**
	 * Indique si les �l�ments de l'onglet doivent �tre regroup�s
	 * @var array
	 */
	private $groups;
	
	/**
	 * Liste des ids des �l�ments de l'onglet
	 * @var array
	 */
	private $contents;
	
	/**
	 * Nombre de r�sultats total
	 * @var int
	 */
	private $nb_results;
	
	/**
	 * Indique si il s'agit d'un onglet mixte
	 * @var bool
	 */
	private $mixed;
	
	/**
	 * Filtres appliqu�s sur le contenu de l'onglet
	 * @var array
	 */
	private $filters = array();
	
	/**
	 * Tableau des �l�ments pour construire les requ�tes
	 * @var array
	 */
	private $query_elements = array();
	
	/**
	 * Callable � appeler pour g�n�rer l'onglet
	 * @var callable
	 */
	private $callable = array();
	
	/**
	 * Dans le cas d'un onglet d'un seul type d'autorit�, on sp�cifie ici le type de l'autorit�
	 * @var int
	 */
	private $content_authority_type;
	
	/**
	 * Nombre de r�sultats apr�s passage des filtres
	 * @var int
	 */
	private $nb_filtered_results = 0;
	
	/**
	 * Nombre de r�sultats potentiels avec les filtres
	 * @var int
	 */
	private $nb_prefiltered_results = 0;
	
	/**
	 * Constructeur d'un onglet de page autorit�
	 * @param string $name Nom de l'onglet
	 * @param string $label Label de l'onglet
	 * @param string $content_type Type du contenu d'un onglet
	 * @param int $nb_results Nombre de r�sultats contenu dans l'onglet
	 * @param array $contents Contenu de l'onglet
	 */
	public function __construct($name, $label, $content_type, $mixed = false) {
		$this->name = $name;
		$this->label = $label;
		$this->content_type = $content_type;
		$this->mixed = $mixed;
	}
	
	/**
	 * Retourne le nom de l'onglet
	 * @return string Nom de l'onglet
	 */
	public function get_name(){
		return $this->name;
	}
	
	/**
	 * Retourne le label de l'onglet
	 * @return string Label de l'onglet
	 */
	public function get_label(){
		return $this->label;
	}
	
	/**
	 * Retourne le type du contenu d'un onglet
	 * @return string Type du contenu d'un onglet
	 */
	public function get_content_type(){
		return $this->content_type;
	}

	/**
	 * Retourne le nombre de r�sultats contenu dans l'onglet
	 * @return int Nombre de r�sultats contenu dans l'onglet
	 */
	public function get_nb_results(){
		return $this->nb_results;
	}
	
	/**
	 * D�fini le nombre de r�sultats contenu dans l'onglet
	 * @param int $nb_results Nombre de r�sultats � affecter
	 */
	public function set_nb_results($nb_results){
		$this->nb_results = $nb_results*1;
	}
	
	/**
	 * Retourne le contenu de l'onglet
	 * @return array Tableau des r�sultats 
	 */
	public function get_contents(){
		return $this->contents;
	}
	
	/**
	 * Permet de d�finir le contenu de l'onglet
	 * @param array $contents Contenu � affecter
	 */
	public function set_contents($contents){
		$this->contents = $contents;
	}
	
	/**
	 * Permet de d�finir le groupement appliqu� au contenu de l'onglet
	 * @param array $groups Contenu � affecter
	 */
	public function set_groups($groups){
		$this->groups = $groups;
	}
	
	/**
	 * Retourne le tableau des groupes
	 * @return array Tableau des groupes
	 */
	public function get_groups(){
		return $this->groups;
	}	
	
	/**
	 * Indique si l'onglet est mixte ou non
	 * @return bool 
	 */
	public function is_mixed(){
		return $this->mixed;
	}
	
	/**
	 * Ajoute un groupement � appliquer au contenu de l'onglet
	 * @param string $groups_parent Type des groupes � affecter
	 * @param array $groups Contenu � affecter
	 */
	public function add_groups($groups_parent, $groups) {
		$this->groups[$groups_parent] = $groups;
	}
	
	public function set_filters($filters) {
		$this->filters = $filters;
	}
	
	public function get_filters() {
		return $this->filters;
	}
	
	public function get_filter_values($filter_name) {
		global $elements_list_filters, ${'elements_list_filters_'.$filter_name.'_post'};
		if (isset(${'elements_list_filters_'.$filter_name.'_post'})) {
			$_SESSION['elements_list_filters'][$filter_name] = $elements_list_filters[$filter_name];
		}
		if (isset($_SESSION['elements_list_filters'][$filter_name])) {
			return $_SESSION['elements_list_filters'][$filter_name];
		}
		return array();
	}
	
	/**
	 * Indique si des filtres sont coch�s
	 * @return boolean
	 */
	public function has_filters_values() {
		foreach ($this->filters as $filter) {
			// On v�rifie que les filtres pr�sents en session sont pr�sents dans les filtres de l'�l�ment sur lequel on est
			if (isset($this->groups[$filter['name']]['elements']) && is_array($this->groups[$filter['name']]['elements']) && count(array_intersect($this->get_filter_values($filter['name']), array_keys($this->groups[$filter['name']]['elements'])))) {
				return true;
			}
		}
		return false;
	}
	
	public function set_query_elements($query_elements = array()) {
		$this->query_elements = $query_elements;
	}
	
	public function get_query_elements() {
		return $this->query_elements;
	}
	
	public function set_callable($callable) {
		$this->callable = $callable;
	}
	
	public function get_callable() {
		return $this->callable;
	}
	
	public function set_content_authority_type($content_authority_type) {
		$this->content_authority_type = $content_authority_type;
	}
	
	public function get_content_authority_type() {
		return $this->content_authority_type;
	}
	
	/**
	 * D�finit le nombre de r�sultats apr�s passage des filtres
	 * @param int $nb_filtered_results Nombre de r�sultats apr�s passage des filtres
	 */
	public function set_nb_filtered_results($nb_filtered_results) {
		$this->nb_filtered_results = $nb_filtered_results*1;
	}
	
	/**
	 * Retourne le nombre de r�sultats apr�s passage des filtres
	 * @return int Nombre de r�sultats apr�s passage des filtres
	 */
	public function get_nb_filtered_results() {
		return $this->nb_filtered_results;
	}
	
	/**
	 * D�finit le nombre de r�sultats apr�s passage des filtres
	 * @param int $nb_filtered_results Nombre de r�sultats apr�s passage des filtres
	 */
	public function set_nb_prefiltered_results($nb_prefiltered_results) {
		$this->nb_prefiltered_results = $nb_prefiltered_results*1;
	}
	
	/**
	 * Retourne le nombre de r�sultats apr�s passage des filtres
	 * @return int Nombre de r�sultats apr�s passage des filtres
	 */
	public function get_nb_prefiltered_results() {
		return $this->nb_prefiltered_results;
	}
}