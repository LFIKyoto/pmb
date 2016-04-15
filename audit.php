<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audit.php,v 1.9 2015-06-02 15:17:03 jpermanne Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "\$msg[audit_titre]";

require_once ("$base_path/includes/init.inc.php");  

switch($pmb_type_audit) {
	case '1':
		$audit = new audit($type_obj, $object_id) ;
		$audit->get_all();
		if(count($audit->all_audit) == 1){
			$all[0] =  $audit->get_creation() ;
		} else {
			$all[0] =  $audit->get_creation() ;
			$all[1] =  $audit->get_last() ;
		}		
		break;
	case '2':
		$audit = new audit($type_obj, $object_id) ;
		$audit->get_all() ;
		$all = $audit->all_audit ;
		break;
	default:
	case '0':
		echo "<script> self.close(); </script>" ;
		break;
	}

$audit_list = "<script type='text/javascript' src='./javascript/sorttable.js'></script>
<table class='sortable' ><tr><th>".$msg['audit_col_userid']."</th><th>".$msg['audit_col_username']."</th><th>".$msg['audit_col_type_action']."</th><th>".$msg['audit_col_date_heure']."</th><th>".$msg['audit_col_nom']."</th><th>".$msg['audit_comment']."</th></tr>";
while(list($cle, $valeur) = each($all)) {
	//user_id, user_name, type_modif, quand, concat(prenom, ' ', nom) as prenom_nom
	$info=json_decode($valeur->info);
	$info_display="";
	if(is_object($info)){
		if($info->comment)$info_display.=$info->comment."<br>";
		if(count($info->fields)){
			foreach($info->fields as $fieldname => $values){
				if(is_object($values)){
					$info_display.=$fieldname." : ".$values->old." => ".$values->new."<br>";
				}
			}
		}
	}else $info_display=$valeur->info;
	
	$audit_list .= "
		<tr>
			<td>$valeur->user_id</td>
			<td>$valeur->user_name</td>
			<td>".$msg['audit_type'.$valeur->type_modif]."</td>
			<td>$valeur->aff_quand</td>
			<td>$valeur->prenom_nom</td>
			<td>".$info_display."</td>
			</tr>";
		}
$audit_list .= "</table>";

echo $audit_list ;

if ($type_obj == 1 || $type_obj == 3) { //Audit notices/notices de bulletin
	if ($type_obj == 1) {
		$requete = "SELECT * FROM notices WHERE notice_id='$object_id' LIMIT 1 ";
	} else {
		$requete = "SELECT * FROM notices, bulletins WHERE num_notice = notice_id AND bulletin_id='$object_id' LIMIT 1 ";
	}
	$result = pmb_mysql_query($requete, $dbh);
	if(pmb_mysql_num_rows($result)) {
		$notice = pmb_mysql_fetch_object($result);
		$create_date = new DateTime($notice->create_date);
		$update_date = new DateTime($notice->update_date);
		echo "<br>";
		echo htmlentities($msg["noti_crea_date"],ENT_QUOTES, $charset)." ".$create_date->format('d/m/Y H:i:s')."<br>";
		echo htmlentities($msg["noti_mod_date"],ENT_QUOTES, $charset)." ".$update_date->format('d/m/Y H:i:s')."<br>";
	}
}

pmb_mysql_close($dbh);
