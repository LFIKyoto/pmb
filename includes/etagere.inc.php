<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.inc.php,v 1.15.2.1 2015-10-10 10:04:43 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage des etagères existantes
function aff_etagere($action, $bouton_ajout=1) {
global $msg;
global $PMBuserid;
global $charset, $opac_url_base;

$liste = etagere::get_etagere_list();
if(sizeof($liste)) {
	if($action=="edit_etagere"){
		print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		print "<div class='hmenu'>
					<span><a href='catalog.php?categ=etagere&sub=classementGen'>".$msg["classementGen_list_libelle"]."</a></span>
				</div><hr>";
		if ($bouton_ajout) {
			print "<div class='row'>
				<input class='bouton' type='button' value=' ".$msg["etagere_new_etagere"]." ' onClick=\"document.location='./catalog.php?categ=etagere&sub=gestion&action=new_etagere'\" />
				</div><br>";
		}
	}
	print pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='./images/expand_all.gif' id='expandall' border='0'></a>
			<a href='javascript:collapseAll()'><img src='./images/collapse_all.gif' id='collapseall' border='0'></a></div>");
	$parity=1;
	$arrayRows=array();
	while(list($cle, $valeur) = each($liste)) {
		$rqt_autorisation=explode(" ",$valeur['autorisations']);
		if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
			$classementRow = $valeur['etagere_classement'];
			if(!trim($classementRow)){
				$classementRow=classementGen::getDefaultLibelle();
			}
			$baselink = "./catalog.php?categ=etagere";
			$link = $baselink."&sub=$action&action=edit_etagere&idetagere=".$valeur['idetagere'];
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;

        	$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
        	$td_javascript_click=" onmousedown=\"document.location='$link';\" ";

        	$rowPrint=pmb_bidi("<tr class='$pair_impair' $tr_javascript >");
        	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'><strong>".$valeur['name']."</strong>".($valeur['comment']?" (".$valeur['comment'].")":"")."</td>");
           	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'>".$valeur['nb_paniers']."</td>");
           	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'>".($valeur['validite']?$msg['etagere_visible_date_all']:$msg['etagere_visible_date_du']." ".$valeur['validite_date_deb_f']." ".$msg['etagere_visible_date_fin']." ".$valeur['validite_date_fin_f'])."</td>");
           	$rowPrint.=pmb_bidi("<td>".($valeur['visible_accueil']?"X":"")."<br /><a href='".$opac_url_base."index.php?lvl=etagere_see&id=".$valeur['idetagere']."' target=_blank>".$opac_url_base."index.php?lvl=etagere_see&id=".$valeur['idetagere']."</a></td>");
           	if($action=="edit_etagere"){
           		$classementGen = new classementGen('etagere', $valeur['idetagere']);
           		$rowPrint.=pmb_bidi("<td>".$classementGen->show_selector($baselink,$PMBuserid)."</td>");
           	}
			$rowPrint.=pmb_bidi("</tr>");

           	$arrayRows[$classementRow]["title"]=stripslashes($classementRow);
           	$arrayRows[$classementRow]["etagere_list"].=$rowPrint;
		}
	}
	//on trie
	ksort($arrayRows);
	//on remplace les clés à cause des accents
	$arrayRows=array_values($arrayRows);
	foreach($arrayRows as $key => $type) {
		if($action=="edit_etagere"){
			print gen_plus($key,$type["title"],"<table class='classementGen_tableau'><tr><th class='classement40'>".$msg['etagere_name']."</th><th class='classement10'>".$msg["etagere_cart_count"]."</th><th class='classement10'>".$msg['etagere_visible_date']."</th><th class='classement35'>".$msg['etagere_visible_accueil']."</th><th class='classement5'>&nbsp;</th></tr>".$type["etagere_list"]."</table>",1);
		}else{
			print gen_plus($key,$type["title"],"<table class='classementGen_tableau'><tr><th class='classement40'>".$msg['etagere_name']."</th><th class='classement10'>".$msg["etagere_cart_count"]."</th><th class='classement10'>".$msg['etagere_visible_date']."</th><th class='classement40'>".$msg['etagere_visible_accueil']."</th></tr>".$type["etagere_list"]."</table>",1);
		}
	}

} else {
	print $msg['etagere_no_etagere'];
}
if ($bouton_ajout) print "<div class='row'>
	<input class='bouton' type='button' value=' ".$msg["etagere_new_etagere"]." ' onClick=\"document.location='./catalog.php?categ=etagere&sub=gestion&action=new_etagere'\" />
	</div>";

}

// affichage des autorisations sur les etageres
function aff_form_autorisations_etagere ($param_autorisations="1", $creation_etagere="1") {
global $dbh;
global $msg;
global $PMBuserid;

$requete_users = "SELECT userid, username FROM users order by username ";
$res_users = pmb_mysql_query($requete_users, $dbh);
$all_users=array();
while (list($all_userid,$all_username)=pmb_mysql_fetch_row($res_users)) {
	$all_users[]=array($all_userid,$all_username);
}
if ($creation_etagere) $param_autorisations.=" ".$PMBuserid ;

$autorisations_donnees=explode(" ",$param_autorisations);
for ($i=0 ; $i<count($all_users) ; $i++) {
	if (array_search ($all_users[$i][0], $autorisations_donnees)!==FALSE) $autorisation[$i][0]=1;
	else $autorisation[$i][0]=0;
	$autorisation[$i][1]= $all_users[$i][0];
	$autorisation[$i][2]= $all_users[$i][1];
}
$autorisations_users="";
$id_check_list='';
while (list($row_number, $row_data) = each($autorisation)) {
	$id_check="auto_".$row_data[1];
	if($id_check_list)$id_check_list.='|';
	$id_check_list.=$id_check;
	if ($row_data[1]==1) $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='etagere_autorisations[]' value='".$row_data[1]."' id='$id_check' checked class='checkbox' readonly /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
	elseif ($row_data[0]) $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='etagere_autorisations[]' value='".$row_data[1]."' id='$id_check' checked class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
	else $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='etagere_autorisations[]' value='".$row_data[1]."' id='$id_check' class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
}
$autorisations_users.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='$id_check_list' >";
return $autorisations_users;
}

function verif_droit_etagere($id) {
	global $msg;
	global $PMBuserid;
	global $dbh ;

	if ($id) {
		$requete = "SELECT autorisations FROM etagere WHERE idetagere='$id' ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$rqt_autorisation=explode(" ",$temp->autorisations);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return $id ;
				else return 0 ;
			} else return 0;
		} else return 0 ;
	}
