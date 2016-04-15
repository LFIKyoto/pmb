<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_list.class.php,v 1.1 2012-06-05 15:22:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_list extends cms_module_common_datasource{
	protected $sortable=false;
	protected $limitable=false;
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array();
	}
	
	public function get_form(){
		$form = parent::get_form();
		if ($this->sortable) {
			$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_datasource_list_sort_by'])."</label>
					</div>
					<div class='colonne-suite'>";
						$form.=$this->gen_select_sort_by();
			$form.="
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order'])."</label>
					</div>
					<div class='colonne-suite'>";
						$form.=$this->gen_select_sort_order();
			$form.="
					</div>
				</div>";
		}
		if ($this->limitable) {
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_datasource_list_limit'>".$this->format_text($this->msg['cms_module_common_datasource_list_limit'])."</label> 
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_datasource_list_limit' value='".$this->parameters['nb_max_elements']."'/>
					</div>
				</div>";
		}
		return $form;
	}
	
	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $cms_module_common_datasource_list_sort_by, $cms_module_common_datasource_list_sort_order;
		global $cms_module_common_datasource_list_limit;
		
		if ($this->sortable) {
			$this->parameters['sort_by'] = $cms_module_common_datasource_list_sort_by;
			$this->parameters['sort_order'] = $cms_module_common_datasource_list_sort_order;
		}
		if ($this->limitable) {
			$this->parameters['nb_max_elements'] = $cms_module_common_datasource_list_limit+0;
		}		
		return parent::save_form();
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array();
	}
	
	protected function gen_select_sort_by(){
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters = array();
		}
		$criterias = $this->get_sort_criterias();
		$select = "<select name='cms_module_common_datasource_list_sort_by' >";
						foreach ($criterias as $criteria) {
							$select .= "<option value='".$criteria."' ".($this->parameters['sort_by'] == $criteria ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_datasource_list_sort_by_'.$criteria])."</option>"; 
						}
						$select .= "</select>";
		return $select;
	}
	
	protected function gen_select_sort_order(){
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters = array();
		}

		$select = "
					<select name='cms_module_common_datasource_list_sort_order' >
						<option value='desc' ".($this->parameters['sort_order'] == 'desc' ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order_desc'])."</option>
						<option value='asc' ".($this->parameters['sort_order'] == 'asc' ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order_asc'])."</option>
					</select>
					";
		return $select;
	}
}