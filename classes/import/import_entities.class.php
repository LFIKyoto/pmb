<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_entities.class.php,v 1.1 2018-11-22 15:34:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class import_entities {
	
	public function __construct(){
		
	}
	
	public function proceed(){
		
	}
	
	public static function get_encoding_selector() {
		global $msg, $charset;
		global $encodage_fic_source;
		
		if($encodage_fic_source){
			$_SESSION["encodage_fic_source"]=$encodage_fic_source;
		}elseif(isset($_SESSION["encodage_fic_source"])){
			$encodage_fic_source=$_SESSION["encodage_fic_source"];
		}
		return "
	       	<select name='encodage_fic_source' id='encodage_fic_source'>
				<option value='' ".(!$encodage_fic_source ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_undefine"],ENT_QUOTES,$charset)."</option>
				<option value='iso5426' ".($encodage_fic_source == "iso5426" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso5426"],ENT_QUOTES,$charset)."</option>
				<option value='utf8' ".($encodage_fic_source == "utf8" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_utf8"],ENT_QUOTES,$charset)."</option>
				<option value='iso8859' ".($encodage_fic_source == "iso8859" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso8859"],ENT_QUOTES,$charset)."</option>
			</select>";
	}
	
	public static function is_custom_values_exists($prefix, $datatype, $idchamp, $entity_id, $value) {
		if ($value) {
			$requete="select count(".$prefix."_custom_origine) from ".$prefix."_custom_values where ".$prefix."_custom_".$datatype."='".addslashes($value)."' and ".$prefix."_custom_champ=".$idchamp." and ".$prefix."_custom_origine='".$entity_id."'";
			$resultat=pmb_mysql_query($requete);
			if (!pmb_mysql_result($resultat, 0, 0)) {
				$requete="insert into ".$prefix."_custom_values (".$prefix."_custom_champ,".$prefix."_custom_origine,".$prefix."_custom_".$datatype.") values(".$idchamp.",$entity_id,'".addslashes($value)."')";
				pmb_mysql_query($requete);
			}
		}
	}
}
