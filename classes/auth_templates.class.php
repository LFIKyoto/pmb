<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: auth_templates.class.php,v 1.1 2015-05-18 07:45:15 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class auth_templates {
	
	public static function show_form(){
		global $auth_template_form, $msg, $opac_authorities_templates_folder;
		$dirs = array_filter(glob('./opac_css/includes/templates/authorities/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($opac_authorities_templates_folder) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".(basename($dir) == "common" ? basename($dir)." (".$msg['proc_options_default_value'].")" : basename($dir))."</option>";
			}
		}
		//return basename($opac_authorities_templates_folder);
 		return str_replace('!!options!!', $tpl, $auth_template_form);
	}
	
	public static function save_form(){
		global $auth_tpl_folder_choice, $opac_authorities_templates_folder;
		if(isset($auth_tpl_folder_choice) && '' !== $auth_tpl_folder_choice){
			$auth_tpl_folder_choice = addslashes($auth_tpl_folder_choice);
			$current_folder = "./includes/templates/authorities/";
			//Update directement le parametre sur le nom
			$requete = "update parametres set ";
			$requete .= "valeur_param='$current_folder$auth_tpl_folder_choice', ";
			$requete .= "comment_param='Repertoire des templates utilisés pour l\'affichage des autorités en OPAC' ";
			$requete .= "where type_param='opac' ";
			$requete .= "and sstype_param='authorities_templates_folder'";
			$res = @pmb_mysql_query($requete, $dbh);
			if($res){
				$opac_authorities_templates_folder = $current_folder.$auth_tpl_folder_choice;
				return true;
			}else{
				return false;
			}	
		}else{
			return false;
		}
		
	}
}