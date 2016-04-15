<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_articles_by_type.class.php,v 1.2 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_articles_by_type extends cms_module_common_filter{

	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_article_type_from"
		);
	}

	public function get_filter_by_selectors(){
		return array(
			"cms_module_common_selector_article_type"
		);
	}
	
	public function filter($datas){
		$filtered_datas= $filter = array();

		$selector_by = $this->get_selected_selector("by");
		$field_by = $selector_by->get_value();
		if(count($field_by)){
			$query = "select id_article from cms_articles where article_num_type in (".implode(",",$field_by).")";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$filter[] = $row->id_article;
				}
				foreach($datas as $article){
					if(in_array($article,$filter)){
						$filtered_datas[]=$article;
					}
				}
			}
		}
		return $filtered_datas;
	}
}