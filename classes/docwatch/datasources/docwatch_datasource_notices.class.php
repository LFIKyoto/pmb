<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_notices.class.php,v 1.4 2015-03-06 16:27:11 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/datasources/docwatch_datasource.class.php");
require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");
require_once($class_path."/docwatch/docwatch_item.class.php");
require_once($class_path."/notice.class.php");

/**
 * class docwatch_datasource_notices
 * 
 */
class docwatch_datasource_notices extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access private
	 */
	private $selector;
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
	
	/**
	 * Génération de la structure de données representant les items de type notice
	 * @return array
	 */
	
	protected function get_items_datas($items){
		global $pmb_opac_url, $pmb_keyword_sep;
		global $opac_show_book_pics, $opac_book_pics_url;
		$records = array();
		if(count($items)){
			foreach($items as $item) {
				$notice = new notice($item);
				$record = array();
				$record["num_notice"] = $notice->id;
				$record["title"] = $notice->tit1;
				$record["summary"] = $notice->n_resume;
				$record["content"] = $notice->n_resume;
				$record["url"] = $pmb_opac_url."index.php?lvl=notice_display&id=".$notice->id;
				if ($notice->code || $notice->thumbnail_url) {
					if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $notice->thumbnail_url)) {
						$code_chiffre = pmb_preg_replace('/-|\.| /', '', $notice->code);
						$url_image = $opac_book_pics_url ;
						$url_image = $pmb_opac_url."getimage.php?url_image=".urlencode($url_image)."&amp;noticecode=!!noticecode!!&amp;vigurl=".urlencode($notice->thumbnail_url) ;
						if ($notice->thumbnail_url) {
							$logo_url=$notice->thumbnail_url;
						} else {
							$logo_url = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
						}
					} else{
						$logo_url = "";
					}
				}
				$record["logo_url"] = $logo_url;
				$record["publication_date"] = $notice->date_parution;
				$record["descriptors"] = $notice->categories;
				$record["tags"] = ($notice->index_l ? explode($pmb_keyword_sep, $notice->index_l) : "");
				$records[] = $record;
			}
		}
		return $records;
	}

	public function filter_datas($datas, $user=0){
		return $this->filter_notices($datas, $user);
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
			"docwatch_selector_notices_caddie" => $msg['dsi_docwatch_selector_notices_caddie']
		);
	}


} // end of docwatch_datasource_notices

