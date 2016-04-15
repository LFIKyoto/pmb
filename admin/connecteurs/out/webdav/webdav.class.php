<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav.class.php,v 1.17.2.1 2015-10-13 08:01:50 ngantier Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path,$javascript_path;
require_once($class_path."/connecteurs_out.class.php");
require_once($class_path."/connecteurs_out_sets.class.php");
require_once($include_path."/misc.inc.php");
require_once($include_path."/isbn.inc.php");
//on inclut les dépendances...
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/explnum.class.php");
require_once("$class_path/acces.class.php");
require_once("$class_path/notice.class.php");
require_once("$class_path/notice_doublon.class.php");
require_once($class_path."/epubData.class.php");

require_once("$base_path/admin/connecteurs/out/webdav/lib/Sabre/autoload.php");//On charge de façon automatique tous les fichiers dont on a besoin

// on teste si des répertoires de stockages sont paramétrés
if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) {
	$pmb_docnum_in_directory_allow = 0;
} else {
	$pmb_docnum_in_directory_allow=1;
}

function debug($elem,$new_file=true){
	global $base_path;
	global $source_id;
	if(is_string($elem)){
		if(!$new_file){
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",$elem,FILE_APPEND);
		}else{
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",$elem);
		}
	}else{
	if(!$new_file){
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",print_r($elem,true),FILE_APPEND);
		}else{
			file_put_contents($base_path."/temp/debug_webdav_$source_id.txt",print_r($elem,true));
		}		
	}
}

function sortChildren($a,$b){
	return strcmp($a->getName(), $b->getName());
}


class webdav extends connecteur_out {
	
	function get_config_form() {
		//Rien
		return '';
	}
	
	function update_config_from_form() {
		return;
	}
	
	function instantiate_source_class($source_id) {
		return new webdav_source($this, $source_id, $this->msg);
	}
	
	function process($source_id, $pmb_user_id) {
		global $class_path;
		global $webdav_current_user_id,$webdav_current_user_name;
		global $pmb_url_base;
		
		$source_object = $this->instantiate_source_class($source_id);
		$webdav_current_user_id=0;
		$webdav_current_user_name = "Anonymous";
		$rootDir = new Sabre\PMB\Tree($source_object->config);
		$server = new Sabre\DAV\Server($rootDir);

		if($source_object->config['allow_web']){
			$web = new Sabre\PMB\BrowserPlugin();
			$server->addPlugin($web);
		}
		
		if($source_object->config['authentication'] != "anonymous"){		
			$auth = new Sabre\PMB\Auth($source_object->config['authentication']);
			$authPlugin = new Sabre\DAV\Auth\Plugin($auth,md5($pmb_url_base));
			// Adding the plugin to the server
			$server->addPlugin($authPlugin);
		}
		
		// We're required to set the base uri, it is recommended to put your webdav server on a root of a domain
		$server->setBaseUri($source_object->config['base_uri']);
		// And off we go!
	
		$server->exec();
	}
}

class webdav_source extends connecteur_out_source {
	var $onglets = array();
	
	function webdav_source($connector, $id, $msg) {
		
		parent::connecteur_out_source($connector, $id, $msg);
		$this->included_sets = isset($this->config["included_sets"]) ? $this->config["included_sets"] : array();
	}
	
	function get_config_form() {
		global $charset, $msg, $dbh;
		global $thesaurus_default;
		global $base_path;
		
		if(!$this->config['used_thesaurus']){
			$this->config['used_thesaurus'] = $thesaurus_default;
		}
		if(!$this->config['base_uri']){
			$this->config['base_uri'] = "/";
		}
		if(!$this->config['tree']){
			$this->config['tree'] = array();
		}
		if(!$this->config['restricted_empr_write_permission']){
			$this->config['restricted_empr_write_permission'] = array();
		}
		if(!$this->config['restricted_user_write_permission']){
			$this->config['restricted_user_write_permission'] = array();
		}
		if(!$this->config['metasMapper_class']){
			$this->config['metasMapper_class'] = "";
		}
		if(!$this->config['upload_rep']){
			global $PMBuserid;
			$query = "select deflt_upload_repertoire from users where userid = ".$PMBuserid;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->config['upload_rep'] = pmb_mysql_result($result,0,0);
			}else{
				$this->config['upload_rep'] = 0;
			}
		}
		
		$result = parent::get_config_form();
		
		//Included sets
		$result.= "
			<div class='row'>
				<label for='base_uri'>".htmlentities($this->msg['webdav_base_uri'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<input type='text' name='base_uri' value='".htmlentities($this->config['base_uri'],ENT_QUOTES,$charset)."'/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='base_uri'>".htmlentities($this->msg['webdav_allow_web'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".htmlentities($this->msg['webdav_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='allow_web' value='1' ".($this->config['allow_web'] == 1 ? "checked='checked'" : "")."/>&nbsp;
				".htmlentities($this->msg['webdav_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='allow_web' value='0' ".($this->config['allow_web'] == 0 ? "checked='checked'" : "")."/>
						</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='authentication'>".htmlentities($this->msg['webdav_authentication'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='authentication'>
					<option value='anonymous' ".($this->config['authentication'] == "anonymous" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_anonymous'],ENT_QUOTES,$charset)."</option>
					<option value='gestion' ".($this->config['authentication'] == "gestion" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_authenticate_gest'],ENT_QUOTES,$charset)."</option>
					<option value='opac' ".($this->config['authentication'] == "opac" ? "selected='selected'" : "").">".htmlentities($this->msg['webdav_authenticate_opac'],ENT_QUOTES,$charset)."</option>
				</select>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='write_permission'>".htmlentities($this->msg['webdav_write_permission'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".htmlentities($this->msg['webdav_yes'],ENT_QUOTES,$charset)."&nbsp;<input type='radio' name='write_permission' value='1' ".($this->config['write_permission'] == 1 ? "checked='checked'" : "")."/>&nbsp;
				".htmlentities($this->msg['webdav_no'],ENT_QUOTES,$charset)." &nbsp;<input type='radio' name='write_permission' value='0' ".($this->config['write_permission'] == 0 ? "checked='checked'" : "")."/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_write_permission'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>";
		//groupes d'utilisateurs
		$result.= "
				<div class='colonne2'>
					<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_user_write_permission'],ENT_QUOTES,$charset)."</label><br />";	
		$query = "SELECT grp_id, grp_name FROM users_groups ORDER BY grp_name ";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)>0){
			$result .= "
				<select id='restricted_user_write_permission' name='restricted_user_write_permission[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
					$result.="
					<option value='".$obj->grp_id."' ".(in_array($obj->grp_id,$this->config['restricted_user_write_permission']) ? "selected=selected" : "") .">".htmlentities($obj->grp_name,ENT_QUOTES,$charset)."</option>";
			}
			$result.=" or id_noeud in (select id_noeud from noeuds where num_parent=".$this->categ->id."))
					</select>";
		}
		$result.= "
				</div>";
			
		$result.= "
				<div class='colonne-suite'>
					<label for='restricted_write_permission'>".htmlentities($this->msg['webdav_restricted_empr_write_permission'],ENT_QUOTES,$charset)."</label><br />";	
		//catégories de lecteurs
		$requete = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)>0){
			$result .= "
				<select id='restricted_empr_write_permission' name='restricted_empr_write_permission[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
					$result.="
					<option value='".$obj->id_categ_empr."' ".(in_array($obj->id_categ_empr,$this->config['restricted_empr_write_permission']) ? "selected=selected" : "") .">".htmlentities($obj->libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result.="
					</select>";
		}
			$result.= "	
				</div>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='included_sets'>".htmlentities($this->msg['webdav_restricted_sets'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select MULTIPLE name='included_sets[]'>";
		$sets = new connector_out_sets();
		foreach ($sets->sets as &$aset) {
			$result.= "
					<option ".(in_array($aset->id, $this->included_sets) ? "selected" : "")." value='".$aset->id."'>".htmlentities($aset->caption ,ENT_QUOTES, $charset)."</option>";
		}
		$result.= "
				</select>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='used_thesaurus'>".htmlentities($this->msg['webdav_user_thesaurus'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='used_thesaurus'>";
		$liste_thesaurus = thesaurus::getThesaurusList();
		foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
			$result.= "
					<option value='".$id_thesaurus."' ".($id_thesaurus == $this->config['used_thesaurus'] ? "selected='selected'" : "").">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";	
		}
		$result.= "
				</select>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='only_with_notices'>".htmlentities($this->msg['webdav_only_with_notices'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".$this->msg['webdav_yes']."&nbsp;<input type='radio' value='1' name='only_with_notices' ".($this->config['only_with_notices'] ? "checked='checked'" : "")."/>
				".$this->msg['webdav_no']."&nbsp;<input type='radio' value='0' name='only_with_notices' ".($this->config['only_with_notices'] ? "" : "checked='checked'")."/> 
			</div>";
		$result.="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='tree'>".htmlentities($this->msg['webdav_tree'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='tree_elem' id='select_tree_elem' onchange='load_tree_elem(this.value)'>
					<option value='0'>".htmlentities($this->msg['webdav_select_tree_elem'],ENT_QUOTES,$charset)."</option>
					<option value='typdoc'>typdoc</option>
					<option value='statut'>statut</option>
					<option value='categorie'>categorie</option>
					<option value='indexint'>indexint</option>
				</select><br />
				<table id='tree'>";
		foreach($this->config['tree'] as $pos => $elem){
			$result.="
					<tr id='tree_elem_tr".$pos."'>
						<td recept='yes' recepttype='tree_elem' highlight='tree_elem_show_recept' downlight='tree_elem_hide_recept' id='tree_elem_td".$pos."' draggable='yes' callback_after='move_tree_elem' dragtype='tree_elem' dragicon='$base_path/images/icone_drag_notice.png' dragtext='".$elem."'>
							<input type='hidden' name='tree[]' value='".$elem."' />
							<img src='$base_path/images/sort.png' style='width:12px; vertical-align:middle'/>".$elem."</td>
						<td onclick='tree_elem_delete(\"tree_elem_tr".$pos."\");'><img src=\"$base_path/images/trash.png\" /></td>
					</tr>";
		}
		$result.="
				</table>
				<script type='text/javascript'>
					var nb_tree_elems = ".count($this->config['tree']).";
					function load_tree_elem(elem){
						if(elem){
							var tr = document.createElement('tr');
							document.getElementById('tree').appendChild(tr);
							tr.setAttribute('id','tree_elem_tr'+nb_tree_elems);
							var td = document.createElement('td');	
							td.setAttribute('recept','yes');
							td.setAttribute('recepttype','tree_elem');
							td.setAttribute('highlight','tree_elem_show_recept');
							td.setAttribute('downlight','tree_elem_hide_recept');
							td.setAttribute('id','tree_elem_td'+nb_tree_elems);
							td.setAttribute('draggable','yes');
							td.setAttribute('callback_after','move_tree_elem');
							td.setAttribute('dragtype','tree_elem');
							td.setAttribute('dragicon','$base_path/images/icone_drag_notice.png');
							td.setAttribute('dragtext',elem);
							td.innerHTML = '<input type=\"hidden\" name=\"tree[]\" value=\"'+elem+'\" /> <img src=\"$base_path/images/sort.png\" style=\"width:12px; vertical-align:middle\"/>'+elem;
							tr.appendChild(td);
							var td = document.createElement('td');	
							td.setAttribute('onclick','tree_elem_delete(\"tree_elem_tr'+nb_tree_elems+'\")');
							td.innerHTML = '<img src=\"$base_path/images/trash.png\" />';
							tr.appendChild(td);
							nb_tree_elems++;
							init_drag();
							document.getElementById('select_tree_elem').selectedIndex=0;
						}
					}
					
					function move_tree_elem(elem,evt,target){
					
						if(target != 'false' || target != 'null'){
							elem = elem.parentNode;
							target = document.getElementById(target).parentNode;
							parent = target.parentNode;
							parent.insertBefore(elem,target);
						}
					}
					
					function tree_elem_show_recept(obj){
						obj.style.background='#DDD';
					}
					
					function tree_elem_hide_recept(obj){
						obj.style.background='';
					} 
					
					function tree_elem_delete(id){
						document.getElementById(id).parentNode.removeChild(document.getElementById(id));
					}
				</script>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='default_statut'>".htmlentities($this->msg['webdav_metasMapper_class'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>		
				<input type='text' name='metasMapper_class' value='".htmlentities($this->config['metasMapper_class'],ENT_QUOTES,$charset)."'/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='default_statut'>".htmlentities($this->msg['webdav_default_statut'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>";
		$query = "select id_notice_statut, gestion_libelle from notice_statut order by gestion_libelle";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$result .="
				<select name='default_statut'>";
			while($row=pmb_mysql_fetch_object($res)){
				$result.="
					<option value='".$row->id_notice_statut."'".($row->id_notice_statut == $this->config['default_statut'] ? "selected='selected'" : "").">".htmlentities($row->gestion_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result.="
				</select>";
		}
		$result.="				
			</div>
			<div class='row'>&nbsp;</div>
			<script src=\"./javascript/select.js\" type='text/javascript'></script>
			<script src=\"./javascript/upload.js\" type='text/javascript'></script>";
				//Intégration de la gestion de l'interface de l'upload

		
		//statut docunum
		$result.="
			<div class='row'>
				<label for='default_docnum_statut'>".htmlentities($this->msg['webdav_default_docnum_statut'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>";
		$query = "select id_explnum_statut, gestion_libelle from explnum_statut order by gestion_libelle";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$result .="
				<select name='default_docnum_statut'>";
			while($row = pmb_mysql_fetch_object($res)){
				$result.="
					<option value='".$row->id_explnum_statut."'".($row->id_explnum_statut == $this->config['default_docnum_statut'] ? "selected='selected'" : "").">".htmlentities($row->gestion_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$result .="
				</select>";
		}
		$result.="
			</div>";		
		
		
		global $pmb_docnum_in_database_allow,$pmb_docnum_in_directory_allow;

				$result.= "<div class='row'>";
				
		if ($pmb_docnum_in_database_allow) {
			$result .= "<input type='radio' name='up_place' id='base' value='0' !!check_base!! /> <label for='base'>".$msg['upload_repertoire_sql']."</label>";
		}
		
		if ($pmb_docnum_in_directory_allow) {				
			$result .= "<input type='radio' name='up_place' id='upload' value='1' !!check_up!! /> <label for='upload'>".$msg['upload_repertoire_server']."</label>";
				$req="select repertoire_id, repertoire_nom from upload_repertoire order by repertoire_nom";
				$res = pmb_mysql_query($req);
				if(pmb_mysql_num_rows($res)){
					$result.=" 
						<select name='id_rep'>";
					while ($row = pmb_mysql_fetch_object($res)){
						$result.="
							<option value='".$row->repertoire_id."' ".($row->repertoire_id == $this->config['upload_rep'] ? "selected='selected'" : "").">".htmlentities($row->repertoire_nom,ENT_QUOTES,$charset)."</option>";
					}
					$result.=" 
						</select>";
				}
		}	
		
		if($pmb_docnum_in_directory_allow && $this->config['up_place']){
					$result = str_replace('!!check_base!!','', $result);
			$result = str_replace('!!check_up!!',"checked='checked'", $result);
		} else if($pmb_docnum_in_database_allow) {
			$result = str_replace('!!check_up!!','', $result);
			$result = str_replace('!!check_base!!',"checked='checked'", $result);
				}
		
		$result .= "</div>";
		
		return $result;
	}
	
	function update_config_from_form() {
		global $dbh;
		global $included_sets;
		global $used_thesaurus;	
		global $only_with_notices;
		global $tree;
		global $authentication;
		global $write_permission;
		global $restricted_empr_write_permission,$restricted_user_write_permission;
		global $default_statut;
		global $base_uri;
		global $id_rep;
		global $up_place;
		global $allow_web;
		global $default_docnum_statut;
		global $metasMapper_class;

		parent::update_config_from_form();
		$this->config['included_sets'] = $included_sets;
		$this->config['used_thesaurus'] = $used_thesaurus;
		$this->config['only_with_notices'] = $only_with_notices;
		$this->config['tree'] = $tree;
		$this->config['authentication']= $authentication;
		$this->config['write_permission']= $write_permission;
		$this->config['restricted_empr_write_permission'] = $restricted_empr_write_permission;
		$this->config['restricted_user_write_permission'] = $restricted_user_write_permission;
		$this->config['default_statut'] = $default_statut;
		$this->config['base_uri'] = $base_uri;
		$this->config['upload_rep'] = $id_rep;
		$this->config['up_place'] = $up_place;
		$this->config['allow_web'] = $allow_web;
		$this->config['default_docnum_statut'] = $default_docnum_statut;
		$this->config['metasMapper_class'] = $metasMapper_class;
		return;
	}
}

?>
