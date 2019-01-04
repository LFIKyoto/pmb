<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles_categories.class.php,v 1.9 2018-12-06 09:34:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_articles_categories extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_env_var",
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"publication_date",
			"id_article",
			"article_title",
			"article_order",
			"pert"
		);
	}
	
	protected function get_query_base() {
		$selector = $this->get_selected_selector();
		if ($selector) {
			$query = "select distinct id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date, notices_categories.num_noeud from cms_articles join cms_articles_descriptors on id_article=num_article join notices_categories on cms_articles_descriptors.num_noeud=notices_categories.num_noeud and notcateg_notice = '".($selector->get_value()*1)."'";
			return $query;
		}
		return false;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = $this->get_sorted_datas('id_article', 'num_noeud');
		if($return) {
			$return = $this->filter_datas("articles",$return);
			if ($this->parameters["nb_max_elements"] > 0) $return = array_slice($return, 0, $this->parameters["nb_max_elements"]);
		}
		return $return;
	}
}