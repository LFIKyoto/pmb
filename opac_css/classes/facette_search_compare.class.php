<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_search_compare.class.php,v 1.10.2.5 2015-11-02 11:26:10 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/facette_search_compare.tpl.php");

class facette_search_compare {
	
	public $facette_compare=array();
	public $facette_groupby=array();
	public $result=array();
	public $headers=array();
	
	public $notice_tpl;
	public $notice_nb;
	public $max_display=1000;
	public $first_collumn_size=15;
	
	/**
	 * 
	 * @param integer $compare_notice_template le paramètre $pmb_compare_notice_template 
	 * @param integer $compare_notice_nb le paramètre $pmb_compare_notice_nb
	 */
	public function __construct($compare_notice_template,$compare_notice_nb) {
		$this->notice_tpl=$compare_notice_template;
		$this->notice_nb=$compare_notice_nb;
		
		$this->facette_compare=$_SESSION['check_facette_compare'];
		$this->facette_groupby=$_SESSION['check_facette_groupby'];
	}
	
	/**
	 * 
	 * Génére un nom de table temporaire
	 * 
	 * @param string $prefix
	 * @return string
	 */
	public static function gen_teporary_table_name($prefix='compare_table'){
		return $prefix.md5(microtime(true));
	}
	
	/**
	 * On lance la comparaison à partir d'un résultat de recherche
	 * Rempli la variables result
	 * 
	 * @param object searcher $searcher
	 * @return true si succès message d'erreur sinon
	 */
	public function compare($searcher){
		global $dbh,$msg,$charset,$lang;
		
		self::session_facette_compare($this);
		
		if(sizeof($this->facette_compare)){
			//on insert les notices du searcher en table memoire
			$tmp_table_1=self::gen_teporary_table_name();
			$listeNotices = "0";
			if($searcher->get_nb_results()){
				$listeNotices = $searcher->get_result();
			}
			$query = "CREATE TEMPORARY TABLE $tmp_table_1 engine=memory SELECT notice_id FROM notices WHERE notice_id IN (".$listeNotices.")";
			pmb_mysql_query($query,$dbh);
			$query = "ALTER TABLE $tmp_table_1 engine=memory ADD INDEX notice_id_index BTREE (notice_id)";
			pmb_mysql_query($query,$dbh);
			
			//pour toutes les facettes choisies en comparaison
			$pos=0;
			foreach($this->facette_compare as $key=>$facette_compare){
				//on remonte les ID de notices, dans la liste des ID de notices déjà prente dans le recherche et qui ont une correspondance avec la valeur de la facette
				$query="SELECT DISTINCT(notice_id) FROM notices_fields_global_index
				JOIN $tmp_table_1 ON notices_fields_global_index.id_notice=$tmp_table_1.notice_id
				WHERE notices_fields_global_index.code_champ='$facette_compare[2]' 
				AND notices_fields_global_index.code_ss_champ='$facette_compare[3]' 
				AND notices_fields_global_index.value='".addslashes($facette_compare[1])."'";
				$result=pmb_mysql_query($query,$dbh);
				
				$tmpArray=array();
				while($line=pmb_mysql_fetch_object($result)){
					$tmpArray[]=$line->notice_id;
				}
				//on construit les entete du tableau
				if(!in_array($facette_compare[1], $this->headers)){
					$pos++;
					$this->headers[$pos]=$facette_compare[1];
				}
				
				if(sizeof($tmpArray)){
					//et on organise par critère de regroupement.
					if(sizeof($this->facette_groupby)){
						foreach($this->facette_groupby as $key_groupby=>$facette_groupby){
							// je regroupe sous les valeurs de la facette choisie pour le regroupement
							$query = "
							SELECT value,id_notice FROM notices_fields_global_index 
							WHERE id_notice IN (".implode(",", $tmpArray).")
							AND notices_fields_global_index.code_champ='$facette_groupby[1]' 
							AND notices_fields_global_index.code_ss_champ='$facette_groupby[2]'
							AND (notices_fields_global_index.lang='$lang' OR notices_fields_global_index.lang='')
							";
							$result=pmb_mysql_query($query,$dbh);
							
							$tmpArray=array_flip($tmpArray);
							
							while($line=pmb_mysql_fetch_object($result)){
								
								if($this->result[$line->value][$pos]['count']){
									$this->result[$line->value][$pos]['count']++;
								}else{
									$this->result[$line->value][$pos]['count']=1;
								}
								
								if($this->result[$line->value][$pos]['notices_ids']){
									$this->result[$line->value][$pos]['notices_ids'].=",";
								}
								
								$this->result[$line->value][$pos]['notices_ids'].=$line->id_notice;
								
								$this->result[$line->value][$pos]['value']=$facette_compare[1];
								$this->result[$line->value][$pos]['key']=$key;
								$this->result[$line->value][$pos]['groupby_key']=$key_groupby;
								$this->result[$line->value][$pos]['pos']=$pos;
								unset($tmpArray[$line->id_notice]);
							}
							
							if(sizeof($tmpArray)){
								$tmpArray=array_flip($tmpArray);
								//pas de valeur de regroupement, je regroupe sous le message facettes_not_grouped
								$this->result[$msg['facettes_not_grouped']][$pos]['count']=sizeof($tmpArray);
								$this->result[$msg['facettes_not_grouped']][$pos]['value']=$facette_compare[1];
								$this->result[$msg['facettes_not_grouped']][$pos]['pos']=$pos;
								$this->result[$msg['facettes_not_grouped']][$pos]['key']=$key;
								$this->result[$msg['facettes_not_grouped']][$pos]['groupby_key']=$key_groupby;
								$this->result[$msg['facettes_not_grouped']][$pos]['notices_ids']=implode(",", $tmpArray);
							}
						}
						
					}else{
						//pas de valeur de regroupement, je regroupe sous le message facettes_not_grouped
						$this->result[$msg['facettes_not_grouped']][$pos]['count']=sizeof($tmpArray);
						$this->result[$msg['facettes_not_grouped']][$pos]['value']=$facette_compare[1];
						$this->result[$msg['facettes_not_grouped']][$pos]['pos']=$pos;
						$this->result[$msg['facettes_not_grouped']][$pos]['key']=$key;
						$this->result[$msg['facettes_not_grouped']][$pos]['groupby_key']=$key_groupby;
						$this->result[$msg['facettes_not_grouped']][$pos]['notices_ids']=implode(",", $tmpArray);
					}
				}
				
				//le tri du résultat
				if(sizeof($this->result)){
					self::sort_compare($this->result);
				}
			}
			
			//Si trop de résultat, la génération du tableau html sera trop longue = on coupe.
			if(sizeof($this->result)*sizeof($this->facette_compare) > $this->max_display){
				return 'facette_compare_too_more_result';
			}
			
			return true;
		}else{
			//pas de résultat
			return 'facette_compare_no_result';
		}
	}
	
	/**
	 * La fonction d'affichage du comparateur de notices
	 * 
	 * @return string affichage en mode comparateur
	 */
	public function display_compare(){
		global $base_path,$charset,$msg;
		global $facette_search_compare_wrapper;
		global $facette_search_compare_header;
		global $facette_search_compare_line;
		global $facette_search_compare_element;
		global $facette_search_compare_hidden_line;
		global $facette_search_compare_hidden_element;
		global $opac_notice_affichage_class;
		
		//script
		$compare_wrapper_script="";
		$facette_search_compare_wrapper=str_replace("!!compare_wrapper_script!!", $compare_wrapper_script, $facette_search_compare_wrapper);
		
		$body="";
		$header="";
		
		if(sizeof($this->result)){
			//Les entetes
			foreach($this->headers as $pos=>$compareHeader){
				$header.=$facette_search_compare_header;
				$header=str_replace("!!compare_hearder_libelle!!", $compareHeader, $header);
			}
			
			//les tailles CSS
			$facette_search_compare_wrapper=str_replace("!!first_collumn_size!!", $this->first_collumn_size, $facette_search_compare_wrapper);
			$cullumn_size=round((100-$this->first_collumn_size)/(sizeof($this->headers)));
			$facette_search_compare_wrapper=str_replace("!!cullumn_size!!", $cullumn_size, $facette_search_compare_wrapper);
			
			//les lignes
			$even_odd='even';
			foreach($this->result as $groupedby=>$comparedElements){
				
				//une ligne
				$line=$facette_search_compare_line;
				$line=str_replace("!!even_odd!!",$even_odd , $line);
				if($even_odd=='even'){
					$even_odd='odd';
				}else{
					$even_odd='even';
				}
				$line=str_replace("!!groupedby_libelle!!",$groupedby , $line);
				$line=str_replace("!!compare_line_onclick!!",'toggle_hidden_line(this,"compare_hidden_line_'.$groupedby.'")' , $line);
				//et la ligne cachée
				$hidden_line=$facette_search_compare_hidden_line;
				$hidden_line=str_replace("!!compare_hidden_line_id!!", 'compare_hidden_line_'.$groupedby, $hidden_line);
				
				//chacun des elements et elements cachés d'une ligne
				$elements="";
				$hidden_elements="";
				for($i=1;$i<sizeof($this->headers)+1;$i++){
					//un element d'une ligne
					$element=$facette_search_compare_element;
					$hidden_element=$facette_search_compare_hidden_element;
					
					if($comparedElements[$i]['notices_ids']){
						$element=str_replace("!!compare_element_libelle!!", $comparedElements[$i]['count'], $element);
						
						$notices=self::call_notice_display($comparedElements[$i]['notices_ids'],$this->notice_nb,$this->notice_tpl);
						
						$hidden_element=str_replace("!!compare_hidden_element_libelle!!", $notices, $hidden_element);
					}else{
						$element=str_replace("!!compare_element_libelle!!", '', $element);
						
						$hidden_element=str_replace("!!compare_hidden_element_libelle!!", '', $hidden_element);
					}
					
					//on renseinge le boutton "..." si besoin
					if($comparedElements[$i]['notices_ids']){
						$hidden_element=str_replace("!!compare_hidden_line_see_more!!", self::get_compare_see_more($comparedElements[$i]['notices_ids']), $hidden_element);
					}else{
						$hidden_element=str_replace("!!compare_hidden_line_see_more!!", '', $hidden_element);
					}
					
					$elements.=$element;
					$hidden_elements.=$hidden_element;
				}
				
				$hidden_line=str_replace("!!compare_hidden_line_elements!!", $hidden_elements, $hidden_line);
				$line=str_replace("!!compare_line_elements!!", $elements, $line);
				
				//on ajoute la ligne visible et invisible
				$body.=$line.$hidden_line;
			}
		}
		
		//et on injecte le HTML
		$facette_search_compare_wrapper=str_replace("!!compare_header!!", $header, $facette_search_compare_wrapper);
		$facette_search_compare_wrapper=str_replace("!!compare_body!!", $body, $facette_search_compare_wrapper);
		
		return $facette_search_compare_wrapper;
	}
	
	/**
	 * On créé le tableau des éléments à comparer, au dessus du menu des facettes
	 * 
	 * @return string le tableau de selection des valeurs de comparaisons
	 */
	public function gen_table_compare() {
		global $base_path,$charset,$msg;
		
		$table_compare='';
		foreach($this->facette_compare as $key=>$facette_compare){
			if(!$facette_compare['available']){
				$balise_start="<del>";
				$balise_stop="</del>";
			}else{
				$balise_start="<p>";
				$balise_stop="</p>";
			}
	
			$table_compare.='<tr>';
			$table_compare.='<td style="width:90%;">';
			$table_compare.=$balise_start.htmlentities($facette_compare[0],ENT_QUOTES,$charset).' : '.htmlentities($facette_compare[1],ENT_QUOTES,$charset).$balise_stop;
			$table_compare.='<input id="compare_facette_'.$key.'" type="hidden" value="'.htmlentities($facette_compare['value'],ENT_QUOTES,$charset).'" name="check_facette_compare[]"/>';
			$table_compare.='</td>';
			$table_compare.='<td>';
			$table_compare.='<span class="facette_compare">';
			$table_compare.='<img  width="18px" height="18px" title="'.$msg['facette_compare_remove'].'" alt="'.$msg['facette_compare_remove'].'" class="facette_compare_close" onclick="remove_compare_facette(\''.htmlentities(addslashes($facette_compare['value']),ENT_QUOTES,$charset).'\');"  src="'.$base_path.'/images/cross.png"/>';
			$table_compare.='</span>';
			$table_compare.='</td>';
			$table_compare.='</tr>';
	
		}
		
		return $table_compare;
	}
	
	/**
	 * @return string le tableau de selection des valeurs de groupement
	 */
	public function gen_table_groupby(){
		global $base_path,$charset,$msg;
		
		$table_groupby='';
		
		foreach($this->facette_groupby as $key=>$facette_groupby){
			if(!$facette_groupby['available']){
				$balise_start="<del>";
				$balise_stop="</del>";
			}else{
				$balise_start="<p>";
				$balise_stop="</p>";
			}
			
			$table_groupby.='
				<tr>
					<td style="width:90%;">'.$balise_start.$facette_groupby[0].$balise_stop.'</td>
					<td><img height="18px" width="18px" title="'.$msg['facette_compare_remove'].'" alt="'.$msg['facette_compare_remove'].'" class="facette_compare_close" src="'.$base_path.'/images/cross.png" onclick="group_by(\''.htmlentities(addslashes($facette_groupby['value']),ENT_QUOTES,$charset).'\');valid_facettes_compare();"/></td>
				</tr>';
		}
		
		return $table_groupby;
	}
	
	/**
	 * si une des facette n'est pas déjà choisie pour comparer et n'est pas utilisé en recherche, on la rend active pour pouvoir etre utilisé en comparaison
	 * 
	 * @param string $id l'id de la facette concernée 
	 * @param bool $available 
	 */
	public function set_available_compare($id,$available=true){
		$this->facette_compare[$id]['available']=$available;
		$_SESSION['check_facette_compare'][$id]['available']=$available;
	}
	
	/**
	 * Si un groupe n'est pas déjà choisi et dont un élement au moins est disponible pour la recherche, on le rend actif pour pouvoir etre utilisé en groupement
	 * 
	 * @param integer $id l'id du groupe
	 * @param bool $available
	 */
	public function set_available_groupby($id,$available=true){
		$this->facette_groupby[$id]['available']=$available;
		$_SESSION['check_facette_groupby'][$id]['available']=$available;
	}
	
	public static function sort_compare(&$array){
		global $msg;
		
		$tmp=array();
		if(sizeof($array[$msg['facettes_not_grouped']])){
			$tmp=$array[$msg['facettes_not_grouped']];
			unset($array[$msg['facettes_not_grouped']]);
		}
		
		krsort($array);
		
		if(sizeof($tmp)){
			$array[$msg['facettes_not_grouped']]= $tmp;
		}
		
		return $array;
	}
	
	/**
	 * Classe permettant d'appeler l'affichage des notices
	 * Retire de la liste envoyée en référence les notices déjà affichées
	 *
	 * @param string $notices_ids la liste des notices, séparées par ,
	 * @param integer $notice_nb le nombre de notices à afficher par passe
	 * @param integer $notice_tpl l'identifiant du template d'affichage, si null, affiche le header de la classe d'affichage
	 */
	public static function call_notice_display(&$notices_ids,$notice_nb,$notice_tpl){
		global $base_path,$charset,$msg;
		global $liens_opac;
		global $opac_notice_affichage_class;
		global $opac_url_base;
		global $opac_notices_format;
		
		$notices_ids=explode(",",$notices_ids);
		
		$notices='';
		for($i_notice_nb=0;$i_notice_nb<$notice_nb;$i_notice_nb++) {
			if($notices_ids[$i_notice_nb]){
				$notices.='<li>';
								
				$current = new $opac_notice_affichage_class($notices_ids[$i_notice_nb],$liens_opac,1,1);
				$current->genere_ajax_param($opac_notices_format,0);
				
				//le panier
				if ($current->cart_allowed){
					if(isset($_SESSION["cart"]) && in_array($current->notice_id, $_SESSION["cart"])) {
						$notices.="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".get_url_icon('basket_exist.gif', 1)."\" align='absmiddle' border='0' alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
					} else {
						$title=$current->notice_header;
						if(!$title)$title=$current->notice->tit1;
						$notices.="<a href=\"cart_info.php?id=".$current->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src='".get_url_icon("basket_small_20x20.png", 1)."' align='absmiddle' border='0' alt=\"".$msg['notice_title_basket']."\" /></a>";
					}
				}else {
					$notices.="";
				}
				//le lien pour ouvrir la popup
				$notices.="<a class=\"Cmpr_Wrap_linkItem\" ".$current->notice_affichage_enrichment." onclick=\"open_notice_popup($notices_ids[$i_notice_nb],'".rawurlencode($current->notice_affichage_cmd)."',this.getAttribute('enrichment'))\">";
				
				//l'affichage
				if($notice_tpl){
					$noti_tpl=new notice_tpl_gen($notice_tpl);
					$notices.=$noti_tpl->build_notice($notices_ids[$i_notice_nb]);
				}else{
					$current->do_header();
					$notices.=$current->notice_header;
				}
				
				unset($notices_ids[$i_notice_nb]);
				$notices.='</a>';
				$notices.='</li>';
			}
		}
		
		if(sizeof($notices_ids)){
			$notices_ids=implode(',', $notices_ids);
		}
		return $notices;
	}
	
	/**
	 * Passage en session des valeurs du comparateur
	 * Ou revalidation des variables de classe courrante à partir des variables de session
	 * 
	 * @param facette_search_compare $facette_search_compare
	 */
	public static function session_facette_compare($facette_search_compare=null,$reinit_compare=false){
		global $check_facette_compare;
		global $check_facette_groupby;
		global $charset;
		
		if($facette_search_compare){
			$facette_search_compare->facette_compare=$_SESSION['check_facette_compare'];
			$facette_search_compare->facette_groupby=$_SESSION['check_facette_groupby'];
		}elseif($reinit_compare){
			if($facette_search_compare){
				$facette_search_compare->facette_compare=array();
			}
			$_SESSION['check_facette_compare']=array();
		}else{
			if(sizeof($check_facette_compare)){
				$_SESSION['check_facette_compare']=array();
				foreach($check_facette_compare as $key=>$f_c){
					$f_c=stripslashes($f_c);
					$f_c_tab=pmb_utf8_array_decode(json_decode(utf8_encode($f_c)));
					if($f_c!=''){
						$_SESSION['check_facette_compare'][$f_c_tab[4]]=$f_c_tab;
						$_SESSION['check_facette_compare'][$f_c_tab[4]]['value']=$f_c;
					}else{
						unset($_SESSION['check_facette_compare'][$f_c_tab[4]]);
					}
				}
			}else{
				unset($_SESSION['check_facette_compare']);
			}
		
			if(sizeof($check_facette_groupby)){
				$_SESSION['check_facette_groupby']=array();
				foreach($check_facette_groupby as $key=>$f_gb){
					$f_gb=stripslashes($f_gb);
					if($charset!='utf-8'){
						$f_gb=utf8_encode($f_gb);
					}
					$f_gb_tab=pmb_utf8_array_decode(json_decode($f_gb));
					if($f_gb!=''){
						$_SESSION['check_facette_groupby'][$f_gb_tab[3]]=$f_gb_tab;
						$_SESSION['check_facette_groupby'][$f_gb_tab[3]]['value']=$f_gb;
					}else{
						unset($_SESSION['check_facette_groupby'][$f_gb_tab[3]]);
					}
				}
			}else{
				unset($_SESSION['check_facette_groupby']);
			}
		}
	}
	
	/**
	 *
	 * On renvoi un id de facette en fonction de ses éléments
	 *
	 * @param String $name
	 * @param String $libelle
	 * @param String $code_champ
	 * @param String $code_ss_champ
	 */
	public static function gen_compare_id($name,$libelle,$code_champ,$code_ss_champ){
		$id=$name."_".$libelle."_".$code_champ."_".$code_ss_champ;
		$id=convert_diacrit($id).md5($id);
		$id=str_replace("'","",$id);
		return $id;
	}
	
	/**
	 *
	 * On renvoi un id de groupement en fonction de ses éléments
	 *
	 * @param String $name
	 * @param String $code_champ
	 * @param String $code_ss_champ
	 */
	public static function gen_groupby_id($name,$code_champ,$code_ss_champ){
		$id=$name."_".$code_champ."_".$code_ss_champ;
		$id=convert_diacrit($id).md5($id);
		$id=str_replace("'","",$id);
		return $id;
	}
	
	/**
	 *
	 * 
	 *
	 * @param String $name
	 * @param String $code_champ
	 * @param String $code_ss_champ
	 * @param String $idGroupBy
	 */
	public static function gen_groupby($name,$code_champ,$code_ss_champ,$idGroupBy){
		return '["'.addslashes($name).'",'.$code_champ.','.$code_ss_champ.',"'.$idGroupBy.'"]';
	}
	
	/**
	 * On conserve dans les formulaires de recherche les informations, pour les faire évoluer au cours de la session.
	 * 
	 * @return string le bloc dans le formulaire
	 */
	public static function form_write_facette_compare(){
		$form='';
		
		if(sizeof($_SESSION['check_facette_compare'])){
			foreach($_SESSION['check_facette_compare'] as $key=>$facette_compare){
				$form .= "<input type=\"hidden\" name=\"check_facette_compare[]\" value=\"".htmlentities($facette_compare['value'],ENT_QUOTES,$charset)."\">\n";
			}
		}
		
		if(sizeof($_SESSION['check_facette_groupby'])){
			foreach($_SESSION['check_facette_groupby'] as $key=>$facette_groupby){
				$form .= "<input type=\"hidden\" name=\"check_facette_groupby[]\" value=\"".htmlentities($facette_groupby['value'],ENT_QUOTES,$charset)."\">\n";
			}
		}
		
		$form .= "<input type='hidden' value='' id='filtre_compare_form_values' name='filtre_compare'>";
		return $form;
	}
	
	public static function get_compare_see_more($notices_ids){
		global $msg;
		return "<a onclick='compare_see_more(this,[".$notices_ids."]);'>".$msg["facette_plus_link"]."</a>";
	}
	
	/**
	 * @return string le bouton d'ajout du critère de groupage dans le tableau HTML des facettes
	 */
	public static function get_groupby_row($facette_compare,$groupBy,$idGroupBy){
		global $base_path,$msg;
		global $charset;
		$script="";
		if(sizeof($facette_compare->facette_groupby[$idGroupBy])){
			$script= "
				<th class='groupby_button' onclick=\"group_by('".htmlentities($groupBy,ENT_QUOTES,$charset)."');\"><img title='".$msg['facette_compare_groupby']."' class='facette_compare_grp' alt='".$msg['facette_compare_groupby']."' src='".get_url_icon('group_by.png')."'/></th>
				<input type='hidden' id='facette_groupby_".$idGroupBy."' name='check_facette_groupby[]' value='".htmlentities($groupBy,ENT_QUOTES,$charset)."'/>";
		}else{
			$script= "
				<th class='groupby_button' onclick=\"group_by('".htmlentities($groupBy,ENT_QUOTES,$charset)."');\"><img title='".$msg['facette_compare_groupby']."' class='facette_compare_grp' alt='".$msg['facette_compare_groupby']."' src='".get_url_icon('group_by_grey.png')."'/></th>
				<input type='hidden' id='facette_groupby_".$idGroupBy."' name='check_facette_groupby[]' value=''/>";
			}	
		return $script;
	}
	
	public static function get_begin_result_list(){
		global $base_path,$msg;
		return "<a href='javascript:expandAll_compare();'>
					<img class='img_plusplus' src='$base_path/images/expand_all.gif' border='0' id='expandall'>
				</a>
				&nbsp;
				<a href='javascript:collapseAll_compare()'>
					<img class='img_moinsmoins' src='$base_path/images/collapse_all.gif' border='0' id='collapseall'>
				</a>
		";
	}
	
	/**
	 * @return string les script js utile pour le comparateur
	 */
	public static function get_compare_wrapper(){
		global $base_path;
		global $msg;
		$script="
			function valid_facettes_compare(){
				
				var form = document.facettes_multi;
				if(form.elements.length>0){
					
					var form_values_compare_input_array=new Array();
					
					for(var i=0; i<form.elements.length;i++){
						
						if(form.elements[i].name=='check_facette[]' && form.elements[i].checked){
							//on transforme les case à coché en element du tableau des facettes	
							//on ajoute dans le tableau des facettes
							var value=form.elements[i].value;
							var jsonArray=JSON.parse(value);
							
							//On ajoute dans le formulaire de postage général
							var form_values_compare_input=document.createElement('input');
							form_values_compare_input.setAttribute('name','check_facette_compare[]');
							form_values_compare_input.setAttribute('type','hidden');
							form_values_compare_input.setAttribute('value',value);
							form_values_compare_input_array.push(form_values_compare_input);
						}
					}
					
					var post=false;
					var form_values=document.form_values;
					for(var i=0;i<form_values_compare_input_array.length;i++) {
						form_values.appendChild(form_values_compare_input_array[i]);
					}
					
					for(var i=0; i<form_values.elements.length;i++){
						if(form_values.elements[i].name=='check_facette_compare[]' && form_values.elements[i].value!=''){
							document.getElementById('filtre_compare_facette').value='compare';
							document.getElementById('filtre_compare_form_values').value='compare';
							form_values.submit();
							post=true;
						}
					}
					if(post=false){
						alert('".$msg['facette_compare_not_selected']."');
					}
				}else{
					alert('".$msg['facette_compare_not_selected']."');
				}
			}
			
			function valid_compare(){
				var form_values=document.form_values;
				var post=false;
				
				for(var i=0; i<form_values.elements.length;i++){
					if(form_values.elements[i].name=='check_facette_compare[]' && form_values.elements[i].value!=''){
						document.getElementById('filtre_compare_facette').value='compare';
						document.getElementById('filtre_compare_form_values').value='compare';
						form_values.submit();
						post=true;
					}
				}
				if(post=false){
					alert('".$msg['facette_compare_not_selected']."');
				}
			}
			
			function remove_compare_facette(value){
				
				var jsonArray = JSON.parse(value);
				
				//on supprime l'élement du tableau des facettes
				elem=document.getElementById('compare_facette_'+jsonArray[4]);
				elem.parentNode.removeChild(elem);
				
				//on supprime l'élément du formulaire général aussi
				var form_values=document.form_values;
				for(var i in form_values.elements){
					if(form_values.elements[i] && form_values.elements[i].value && form_values.elements[i].name=='check_facette_compare[]'){
						form_values_json_array=JSON.parse(form_values.elements[i].value);
						
						if(form_values_json_array[4]==jsonArray[4]){
							elem=form_values.elements[i];
							elem.parentNode.removeChild(elem);
						}
					}
				}
				var post=true;
				for(var i in form_values.elements){
					if(form_values.elements[i] && form_values.elements[i].value && form_values.elements[i].name=='check_facette_compare[]'){
						valid_facettes_compare();
						post=false;
					}
				}

				if(post){
					document.location.href='$base_path/index.php?lvl=more_results&get_last_query=1&reinit_compare=1';	
				}
			}
			
			function group_by(groupBy){
				
				var jsonArray=JSON.parse(groupBy);
			
				//on vide les elements group_by
				var group_by_elements=document.getElementsByName('check_facette_groupby[]');
				
				var nodes_to_remove;
				
				for(var i in group_by_elements){
					if(group_by_elements[i].nodeName=='INPUT'){
						if(group_by_elements[i].getAttribute('id')!='facette_groupby_'+jsonArray[3]){
							
							if(group_by_elements[i].parentNode.getAttribute('name')!='form_values' && group_by_elements[i].parentNode.getAttribute('name')!='cart_values'){
								
								var group_by_elements_img=group_by_elements[i].previousElementSibling.firstChild;
									
								group_by_elements_img.setAttribute('src','".get_url_icon('group_by_grey.png')."');
								group_by_elements[i].setAttribute('value','');
								
							}else{
								nodes_to_remove=[i=[group_by_elements[i],group_by_elements[i].parentNode]];
							}
						}
					}
				}
				
				if(nodes_to_remove && nodes_to_remove.length>0){
					for(var i in nodes_to_remove){
						nodes_to_remove[i][1].removeChild(nodes_to_remove[i][0]);
					}
				}
				
				element=document.getElementById('facette_groupby_'+jsonArray[3]);
				var img=element.previousElementSibling.firstChild;
				
				var table_groupby=document.getElementById('facette_groupby');
				
				if(element.getAttribute('value')==''){
					element.setAttribute('value',JSON.stringify(groupBy));
					
					//On ajoute dans le formulaire de postage général
					var form_values=document.form_values;
					var form_values_groupby_input=document.createElement('input');
					form_values_groupby_input.setAttribute('name','check_facette_groupby[]');
					form_values_groupby_input.setAttribute('type','hidden');
					form_values_groupby_input.setAttribute('value',groupBy);
					form_values.appendChild(form_values_groupby_input);
					
					
				}
				valid_facettes_compare();
			}
		";
		
		return $script;
	}
}
