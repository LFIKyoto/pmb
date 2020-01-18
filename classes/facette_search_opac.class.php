<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_search_opac.class.php,v 1.39.2.2 2019-11-05 14:05:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des facettes pour la recherche OPAC

require_once($include_path."/templates/facette_search_opac_tpl.php");

class facette_search_opac {
	
	public $type;
	
	/**
	 * Critères
	 * @var array
	 */
	public static $fields;
	
	/**
	 * Nom de la table
	 * @var string
	 */
	public static $table_name = 'facettes';
	
	public function __construct($type='notices', $is_external=false){
		$this->type = $type;
		if($is_external) {
			static::$table_name = 'facettes_external';
		}
		static::parse_xml_file($this->type);
	}
	
	protected static function get_xml_file($type='notices') {
		global $include_path;
		
		$file = '';
		switch ($type) {
			case 'authors':
			case 'categories':
			case 'publishers':
			case 'collections':
			case 'subcollections':
			case 'series':
			case 'titres_uniformes':
			case 'indexint':
			case 'authperso':
				$file = $include_path."/indexation/authorities/".$type."/champs_base_subst.xml";
				if(!file_exists($file)){
					$file = $include_path."/indexation/authorities/".$type."/champs_base.xml";
				}
				break;
			default:
				$file = $include_path."/indexation/".$type."/champs_base_subst.xml";
				if(!file_exists($file)){
					$file = $include_path."/indexation/".$type."/champs_base.xml";
				}
				break;
		}
		return $file;
	}
	
	//recuperation de champs_base.xml
	public static function parse_xml_file($type='notices') {
		if(!isset(self::$fields[$type])) {
			$file = static::get_xml_file($type);
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			self::$fields[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);
		}
	}
	
	//creation de la liste des criteres principaux
	public function create_list_fields($crit=0, $ss_crit=0){
		global $msg, $charset;
	
		$fields_sorted = $this->fields_sort();
		$select ="<select id='list_crit' name='list_crit' onchange='load_subfields(0)'>";
		foreach ($fields_sorted as $id => $value) {
			if($id == $this->get_authperso_start()) {
				$query = "select id_authperso, authperso_name from authperso order by authperso_name";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while($row = pmb_mysql_fetch_object($result)) {
						$select.="<option value=".($this->get_authperso_start() + $row->id_authperso)." ".($this->get_authperso_start() + $row->id_authperso == $crit ? "selected='selected'": "").">".htmlentities($row->authperso_name, ENT_QUOTES, $charset)."</option>";
					}
				}
			} else {
				$select.="<option value=".$id." ".($id==$crit ? "selected='selected'": "").">".htmlentities($value, ENT_QUOTES, $charset)."</option>";
			}
		}
		$select.="</select></br>";
		if($crit) $select .= "<script>load_subfields(".$ss_crit.")</script>";
		return $select;
	}
	
	//liste liee => sous champs
	public function create_list_subfields($id,$id_ss_champs=0,$suffixe_id=0,$no_label=0){
		global $msg,$charset;
		
		$array_subfields = $this->array_subfields($id);
		
		$select_ss_champs="";
		if($suffixe_id){
			$name_ss_champs="list_ss_champs_".$suffixe_id;
		}else{
			$name_ss_champs="list_ss_champs";
		}
		if((count($array_subfields)>1)){
			if(!$no_label) {
				$select_ss_champs .= "<label>".$msg["facette_filtre_secondaire"]."</label></br>";
			}
			$select_ss_champs.="<select id='$name_ss_champs' name='$name_ss_champs'>";
			foreach($array_subfields as $j=>$val2){
				if($id_ss_champs == $j) {
					$select_ss_champs.="<option value=".$j." selected='selected'>".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
				} else {
					$select_ss_champs.="<option value=".$j.">".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
				}
			}
			$select_ss_champs.="</select></br>";
		}elseif(count($array_subfields)==1){
			if($id > $this->get_authperso_start()) {
				$select_ss_champs .= "<input type='hidden' name='$name_ss_champs' value='".array_keys($array_subfields)[0]."'/>";
			} else {
				$select_ss_champs .= "<input type='hidden' name='$name_ss_champs' value='".array_keys($array_subfields)[0]."'/>";
			}
			if($id > 99) {
				//je repasse la clé à 0 pour y accéder
				$array_subfields = array_values($array_subfields);
				$select_ss_champs .= htmlentities($array_subfields[0],ENT_QUOTES,$charset);
			}
		}
		return $select_ss_champs;
	}
		
	//Liste des facettes créées
	public function get_display_list(){
		global $msg, $charset;
		global $tpl_vue_facettes;
		global $sub;
		global $type;
		
		$lst="";
		$fields = $this->fields_sort();
		$query = "SELECT * FROM ".static::$table_name." where facette_type LIKE '".$this->type."%' order by facette_order, facette_name";
		$result = pmb_mysql_query($query);
		$i = 0;
		while($row = pmb_mysql_fetch_object($result)){
			$array_subfields = $this->array_subfields($row->facette_critere);
			
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
			$td_javascript="
				onMouseDown=\"document.location='".static::format_url("&type=".$row->facette_type."&action=edit&id=".$row->id_facette)."'\"
        		onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" 
        		style=\"cursor : pointer\" ";			
			$lst .= "
				<tr class='".$pair_impair."'>
					<td>
						<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::format_url("&type=".$this->type."&action=down&id=".$row->id_facette)."'\" style='cursor:pointer;'/></a>
						<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::format_url("&type=".$this->type."&action=up&id=".$row->id_facette)."'\" style='cursor:pointer;'/>
					</td>
					<td ".$td_javascript.">".htmlentities($row->facette_name,ENT_QUOTES,$charset)."</td>
                    <td ".$td_javascript.">";
			         if($row->facette_critere > $this->get_authperso_start() && $this->type != "authperso") {					    
					    $authperso_query = "select authperso_name from authperso where id_authperso =".($row->facette_critere - $this->get_authperso_start());
					    $authperso_result = pmb_mysql_query($authperso_query);
					    if (pmb_mysql_num_rows($authperso_result)) {
					        $authperso_row = pmb_mysql_fetch_object($authperso_result);
					        $lst .= $authperso_row->authperso_name;
					    }
					} elseif ($this->type == "authperso")  {
					    $authperso =  explode("_",$row->facette_type);
					    $authperso_id = 0;
					    if (!empty($authperso[1]) && intval($authperso[1])) {
					        $authperso_id = $authperso[1];
					    }
					    $authperso_query = "select authperso_name from authperso where id_authperso =".$authperso_id;
					    $authperso_result = pmb_mysql_query($authperso_query);
					    if (pmb_mysql_num_rows($authperso_result)) {
					        $authperso_row = pmb_mysql_fetch_object($authperso_result);
					        $lst .= $authperso_row->authperso_name;
					    }
					    
					} else {
					    $lst .= htmlentities($fields[$row->facette_critere],ENT_QUOTES,$charset);
					}					
					$lst .= "
                    </td>
                    <td ".$td_javascript.">".(count($array_subfields)>1 ? htmlentities($array_subfields[$row->facette_ss_critere],ENT_QUOTES,$charset) : $msg["admin_opac_facette_ss_critere"])."</td>
					<td ".$td_javascript.">".($row->facette_nb_result ? $row->facette_nb_result : htmlentities($msg["admin_opac_facette_illimite"],ENT_QUOTES,$charset))."</td>
					<td ".$td_javascript.">".($row->facette_type_sort ? $msg['intit_gest_tri2'] : $msg['intit_gest_tri1'])." ".($row->facette_order_sort ? $msg['intit_gest_tri4'] : $msg['intit_gest_tri3'])."</td>
					<td ".$td_javascript." class='center'>".($row->facette_visible_gestion ? 'X' : '')."</td>
					<td ".$td_javascript." class='center'>".($row->facette_visible ? 'X' : '')."</td>
				</tr>
			";
			$tpl_vue_facettes = str_replace('!!id!!',$row->id_facette, $tpl_vue_facettes);
			$i++;
		}
		$tpl_vue_facettes = str_replace('!!lst_facette!!', $lst, $tpl_vue_facettes);
		$tpl_vue_facettes = str_replace('!!sub!!', $sub, $tpl_vue_facettes);
		$tpl_vue_facettes = str_replace('!!type!!', $type, $tpl_vue_facettes);
		
		return $tpl_vue_facettes;
	}
	
	public static function facette_up($id, $type='notices'){
		$requete="select facette_order from ".static::$table_name." where id_facette=$id";
		$resultat=pmb_mysql_query($requete);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(facette_order) as ordre from ".static::$table_name." where facette_type LIKE '".$type."%' and facette_order<$ordre";
		$resultat=pmb_mysql_query($requete);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max) {
			$requete="select id_facette from ".static::$table_name." where facette_type LIKE '".$type."%' and facette_order=$ordre_max limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_facette_max=pmb_mysql_result($resultat,0,0);
			$requete="update ".static::$table_name." set facette_order='".$ordre_max."' where id_facette=$id";
			pmb_mysql_query($requete);
			$requete="update ".static::$table_name." set facette_order='".$ordre."' where id_facette=".$id_facette_max;
			pmb_mysql_query($requete);
		}		
	}
	
	public static function facette_down($id, $type='notices'){
		$requete="select facette_order from ".static::$table_name." where id_facette=$id";
		$resultat=pmb_mysql_query($requete);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(facette_order) as ordre from ".static::$table_name." where facette_type LIKE '".$type."%' and facette_order>$ordre";
		$resultat=pmb_mysql_query($requete);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_facette from ".static::$table_name." where facette_type LIKE '".$type."%' and facette_order=$ordre_min limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_facette_min=pmb_mysql_result($resultat,0,0);
			$requete="update ".static::$table_name." set facette_order='".$ordre_min."' where id_facette=$id";
			pmb_mysql_query($requete);
			$requete="update ".static::$table_name." set facette_order='".$ordre."' where id_facette=".$id_facette_min;
			pmb_mysql_query($requete);
		}		
	}
	
	public static function facette_order_by_name($type='notices'){
		$query = "SELECT id_facette  FROM ".static::$table_name." WHERE facette_type LIKE '".$type."%' order by facette_name";
		$result = pmb_mysql_query($query);	
		$i=1;
		while($row = pmb_mysql_fetch_object($result)){
			pmb_mysql_query("UPDATE ".static::$table_name." SET facette_order='".$i."' where id_facette=".$row->id_facette);
			$i++;
		}
	}
		
	public function fields_sort(){
		global $msg;
		
		$array_sort = array();
		for($i=0;$i<count(self::$fields[$this->type]['FIELD']);$i++){
		    $prev_tmp = '';
		    if(isset(self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['NAME'])){
		        $prev_tmp = (isset($msg[self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['NAME']]) ? $msg[self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['NAME']] : self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['NAME']);
		    }
		    if(isset($msg[self::$fields[$this->type]['FIELD'][$i]['NAME']]) && $tmp = $msg[self::$fields[$this->type]['FIELD'][$i]['NAME']]){
				$lib = $tmp;
			}else{
				$lib = self::$fields[$this->type]['FIELD'][$i]['NAME'];
			}
			$array_sort[self::$fields[$this->type]['FIELD'][$i]['ID']+0] = $lib.($prev_tmp ? ' - '.$prev_tmp : '');
		}
		asort($array_sort);
		return $array_sort;
		
	}
	
	public function array_subfields($id){
		global $msg;
		
		$array_subfields = array();
		
		if($id == $this->get_custom_fields_id()) {
			$result = pmb_mysql_query("select idchamp, titre from ".$this->get_custom_fields_table()."_custom order by titre asc");
			while($row=pmb_mysql_fetch_object($result)){
				$array_subfields[$row->idchamp] = $row->titre;
			}
		} elseif($id == $this->get_custom_expl_fields_id()) {
		    $result = pmb_mysql_query("select idchamp, titre from expl_custom order by titre asc");
		    while($row=pmb_mysql_fetch_object($result)){
		        $array_subfields[$row->idchamp] = $row->titre;
		    }
		} elseif($id == $this->get_custom_explnum_fields_id()) {
		    $result = pmb_mysql_query("select idchamp, titre from explnum_custom order by titre asc");
		    while($row=pmb_mysql_fetch_object($result)){
		        $array_subfields[$row->idchamp] = $row->titre;
		    }
		} elseif($id > $this->get_authperso_start()) {
			$array_subfields[0] = $msg['facette_isbd'];
			$result = pmb_mysql_query("select idchamp,titre from authperso_custom where num_type='".($id-$this->get_authperso_start())."' order by titre asc");
			while($row=pmb_mysql_fetch_object($result)){
				$array_subfields[$row->idchamp] = $row->titre;
			}
		} else {
		    $array_subfields = $this->get_subfields_from_xml($id);
		}
		return $array_subfields;
	}
	
	protected function get_prefix_id() {
		switch ($this->type) {
			case 'notices':
				return 0;
			case 'authors':
				return 1;
			case 'categories':
				return 2;
			case 'publishers':
				return 3;
			case 'collections':
				return 4;
			case 'subcollections':
				return 5;
			case 'series':
				return 6;
			case 'titres_uniformes':
				return 7;
			case 'indexint':
				return 8;
			case 'authperso':
				break;
		}
	}
	
	public function get_custom_fields_id() {
		if($this->get_prefix_id()) {
			return $this->get_prefix_id().'100';
		} else {
			return 100;
		}
	}
	
	public function get_custom_expl_fields_id() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'200';
	    } else {
	        return 200;
	    }
	}
	
	public function get_custom_explnum_fields_id() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'300';
	    } else {
	        return 300;
	    }
	}
	
	public function get_authperso_start() {
		if($this->get_prefix_id()) {
			return $this->get_prefix_id().'500';
		} else {
			return 1000;
		}
	}
	
	protected function get_custom_fields_table() {
		switch ($this->type) {
			case 'notices':
				return 'notices';
			case 'authors':
				return 'author';
			case 'categories':
				return 'categ';
			case 'publishers':
				return 'publisher';
			case 'collections':
				return 'collection';
			case 'subcollections':
				return 'subcollection';
			case 'series':
				return 'serie';
			case 'titres_uniformes':
				return 'tu';
			case 'indexint':
				return 'indexint';
			case 'authperso':
				return 'authperso';
		}
	}
	
	public static function format_url($url) {
		global $base_path;
		global $sub;
		
		return $base_path."/admin.php?categ=opac&sub=".$sub.$url;
	}
	
	protected function get_subfields_from_xml($id) {
	    global $msg;
	    $array = array();
	    $callable = array();
	    $isbd = array();
	    $array_subfields = [];
	    for($i = 0; $i < count(self::$fields[$this->type]['FIELD']); $i++) {
	        if(self::$fields[$this->type]['FIELD'][$i]['ID']==$id) {
	            if(isset(self::$fields[$this->type]['FIELD'][$i]['ISBD'])) {
	                $isbd=self::$fields[$this->type]['FIELD'][$i]['ISBD'];
	            }
	            if(isset(self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'])) {
	                $array = self::$fields[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'];
	            }
	            if(isset(self::$fields[$this->type]['FIELD'][$i]['CALLABLE'])) {
	                $callable=self::$fields[$this->type]['FIELD'][$i]['CALLABLE'];
	            }
	            break;
	        }
	    }
	    for($i=0;$i<count($array);$i++){
	        if (isset($array[$i]['NAME'])) {
	            $array_subfields[$array[$i]['ID']+0] = (isset($msg[$array[$i]['NAME']]) ? $msg[$array[$i]['NAME']] : $array[$i]['NAME']);
	        }
	    }
	    for($i=0;$i<count($callable);$i++){
	        if (isset($callable[$i]['NAME'])) $array_subfields[$callable[$i]['ID']+0] = $msg[$callable[$i]['NAME']];
	    }
	    if(count($isbd)){
	        $array_subfields[$isbd[0]['ID']+0]=$msg['facette_isbd'];
	    }
	    return $array_subfields;
	}
}

