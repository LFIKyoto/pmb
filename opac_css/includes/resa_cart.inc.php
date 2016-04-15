<?php


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/includes/resa_func.inc.php');
require_once($include_path."/mail.inc.php");
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/resa.class.php');

if($_SESSION['user_code']){
	global $resa_cart_display;
	$resa_cart_display='';
	
	//Récupération des notices
	switch($sub){
		case 'resa_cart' :
			$notices = $_SESSION['cart'];			
			break;
		case 'resa_cart_checked':		
			$notices = $notice;
			break;
		default:
			print "<script>document.location='".$base_path."/index.php';</script>";
			break;
	}
	
	$id_empr=$_SESSION['id_empr_session'];
	
	if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")) {
		if($idloc==""){
			//les transferts sont actifs, avec un choix du lieu de retrait et pas de choix encore fait
			//=> on affiche les localisations
			if($pmb_location_reservation) {
				$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1  and idlocation in (select resa_loc from resa_loc where resa_emprloc=$empr_location) ORDER BY location_libelle ";
				$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_notice IN (".implode(",",$notices).") and  expl_statut=idstatut
					and transfert_flag=1 and statut_allow_resa=1
					AND expl_bulletin='0' and expl_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location)";
			} else {
				$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1 ORDER BY location_libelle";
				$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_noticeIN (".implode(",",$notices).") and  expl_statut=idstatut
					and transfert_flag=1 and statut_allow_resa=1 AND expl_bulletin='0' ";
			}
	
			$loc_list=array();
			$flag_transferable=0;
			$res_loc_list = pmb_mysql_query($req_loc_list);
			if(pmb_mysql_num_rows($res_loc_list)){
				while ($r = pmb_mysql_fetch_object($res_loc_list)){
					$loc_list[]=$r->expl_location;
					// au moins un expl transférable
					$flag_transferable=1;
				}
			}
			$res = pmb_mysql_query($loc_req);$tmpHtml = "<form method='post' action='do_resa.php?lvl=".$lvl."&sub=".$sub."'>";
			$tmpHtml .= $msg["reservation_selection_localisation"]."<br /><select name='idloc'>";
				
			//on parcours la liste des localisations
			while ($value = pmb_mysql_fetch_array($res)) {
				if(!$flag_transferable){
					// il y en a un ici?
					$req= "select expl_id from exemplaires, docs_statut where expl_notice IN (".implode(",",$notices).") AND expl_bulletin='0' and expl_location = " . $value[0] . "
						and expl_statut=idstatut and statut_allow_resa=1 ";
					$res_expl = pmb_mysql_query($req);
					if(!pmb_mysql_num_rows($res_expl)){
						continue;
					}
				}
				if($value[0]==$empr_location) $selected=" selected='selected' ";
				else $selected="";
				$tmpHtml .= "<option value='" . $value[0] . "' $selected >" . $value[1] . "</option>";
			}
			$tmpHtml .= "</select><input type='hidden' name='listeNotices' value='".implode(",",$notices)."'><br /><br /><input type='submit' value='" . $msg["reservation_bt_choisir_localisation"] . "'></form>";
			echo $tmpHtml;
		}else{
			$notices=explode(",",$listeNotices);
			$resa_cart_display="<table><tr><th colspan=2>".$msg["empr_menu_resa"]." : </th></tr>";
			foreach($notices as $notice_id){
				$resa_cart_display.="<tr>";
				$bulletin_id=0;
				//On vérifi si notre notice n'est pas une notice de bulletin.
				$query='SELECT bulletin_id FROM bulletins WHERE num_notice='.$notice_id;
				$result = pmb_mysql_query($query, $dbh);
				if(pmb_mysql_num_rows($result)){
					while($line=pmb_mysql_fetch_array($result,MYSQL_ASSOC)){
						$bulletin_id=$line['bulletin_id'];
					}
				}
					
				$resa=new reservation($id_empr, $notice_id, $bulletin_id);
				if($resa->add($idloc)){
					$resa_cart_display.="<td>".$resa->notice."</td><td>".$resa->message."</td>";
				}else{
					$resa_cart_display.="<td>".$resa->notice."</td><td>".$resa->message."</td>";
				}
				$resa_cart_display.="</tr>";
			}
			$resa_cart_display.="</table>";
			
			if(!$opac_resa_popup){
				require_once $base_path.'/includes/show_cart.inc.php';
			}
		}
	}else{
	
		$resa_cart_display="<table><tr><th colspan=2>".$msg["empr_menu_resa"]." : </th></tr>";
		foreach($notices as $notice_id){
			$resa_cart_display.="<tr>";
			$bulletin_id=0;
			//On vérifi si notre notice n'est pas une notice de bulletin.
			$query='SELECT bulletin_id FROM bulletins WHERE num_notice='.$notice_id;
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_num_rows($result)){
				while($line=pmb_mysql_fetch_array($result,MYSQL_ASSOC)){
					$bulletin_id=$line['bulletin_id'];
				}
			}
			
			$resa=new reservation($id_empr, $notice_id, $bulletin_id);
			if($resa->add($_SESSION['empr_location'])){
				$resa_cart_display.="<td>".$resa->notice."</td><td>".$resa->message."</td>";
			}else{
				$resa_cart_display.="<td>".$resa->notice."</td><td>".$resa->message."</td>";
			}
			$resa_cart_display.="</tr>";
		}
		$resa_cart_display.="</table>";
		
		if(!$opac_resa_popup){
			require_once $base_path.'/includes/show_cart.inc.php';
		}	
	}
} else {
	print "<script>document.location='".$base_path."/index.php';</script>";
}