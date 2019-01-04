<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// � 2006 mental works / www.mental-works.com contact@mental-works.com
// 	compl�tement repris et corrig� par PMB Services 
// +-------------------------------------------------+
// $Id: addtags.php,v 1.30 2018-04-19 15:21:03 tsamson Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers n�cessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if (!$opac_allow_add_tag) die();

require_once($base_path.'/includes/templates/common.tpl.php');

// classe de gestion des cat�gories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// classe de gestion des r�servations
require_once($base_path.'/classes/resa.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/includes/notice_affichage.inc.php");
require_once($base_path."/includes/bulletin_affichage.inc.php");

require_once($base_path."/includes/connexion_empr.inc.php");

// autenticazione LDAP - by MaxMan
require_once($base_path."/includes/ldap_auth.inc.php");

// RSS
require_once($base_path."/includes/includes_rss.inc.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");
// pour fonction de v�rification de connexion
require_once($base_path.'/includes/empr_func.inc.php');

if ($opac_allow_add_tag==0) die("");
// par d�faut, on suppose que le droit donn� par le statut est Ok
$allow_avis = 1 ;
$allow_tag = 1 ;

if ($opac_allow_add_tag==1) {
	//ajout possible sans authentification
	$log_ok = 1;
} else {
	//V�rification de la session
	$empty_pwd=true;
	$ext_auth=false;
	// si param�trage authentification particuli�re et pour la re-authentification ntlm
	if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');
	$log_ok=connexion_empr();
}

print $popup_header;
if ($opac_allow_add_tag==2 && !$allow_tag) die($popup_footer);

print "<div id='titre-popup'>".$msg['notice_title_tag']."</div>";

// Le lecteur a ajout� un mot-cl�
if (($ChpTag) && ($log_ok)) {
	$sql="select index_l from notices notice_id='".$noticeid."'";	
	$r = pmb_mysql_query($sql, $dbh);
	$row = pmb_mysql_fetch_assoc($r);
	$tags = explode(';', $row['index_l']);    
	$tags = array_map('trim', $tags);
	if (in_array(trim($ChpTag), $tags)) {
	    echo "<br /><br />".$msg['addtag_exist'];
	} else {
		$sql="insert into tags (libelle, num_notice,user_code,dateajout) values ('$ChpTag',$noticeid,'". $_SESSION["user_code"] ."',CURRENT_TIMESTAMP())";
		if (pmb_mysql_query($sql, $dbh)) {
		echo "<div align='center'><br /><br />".$msg['addtag_enregistre']."<br /><br /><a href='#' onclick='window.close()'>".$msg['addtag_fermer']."</a></div>";
			} else {
		echo "<div align='center'><br /><br />".$msg['addtag_pb_enr']."<br /><br /><a href='#' onclick='window.close()'>".$msg['addtag_fermer']."</a></div>";
				}
	}
} else {
	$requete = "select index_l from notices where index_l is not null and index_l!=''";

	$r = pmb_mysql_query($requete, $dbh);
	if (pmb_mysql_num_rows($r)){
		while ($loc = pmb_mysql_fetch_object($r)) {
			$liste = explode($pmb_keyword_sep,$loc->index_l);
			for ($i=0;$i<count($liste);$i++){
				$index=trim($liste[$i]);
				if ($index) $arrTag[strtolower($index)]++;
				}
			}
		}
	ksort($arrTag);
	$lettre="";
	foreach ($arrTag as $key => $value) {
		if ($key{0}!=$lettre){
			$lettre=$key{0};
			$select.="<optgroup  class='erreur' label='$lettre'>";
			}
		$select.="<option style='color:#000000' value='$key'>".$key ."</option>";
		}
	echo "<form id='f' name='f' method='post' action='".$opac_url_base."addtags.php'>
			<input type='hidden' name='noticeid' value='$noticeid' />
			$msg[addtag_choisissez]<br />
			<select name='select' style='width:200px' onchange='document.f.ChpTag.value=this.value;'>
			$select
			</select><br /><br />
			$msg[addtag_nouveau]<br />
			<input type='text' name='ChpTag' style='width:200px'/>
		    <input type='submit' class='bouton' name='submit' value='".$msg['addtag_bt_ajouter']."' />
			</form>";
}

if (!$log_ok && $opac_allow_add_tag==2) {
	$lvl='tags';
	print do_formulaire_connexion();
	//print $erreur_session ;
	}

	
//Enregistrement du log
global $pmb_logs_activate;
if($pmb_logs_activate){	
	global $log;
	$log->add_log('num_session',session_id());
	$log->save();
}	
print $popup_footer;

/* Fermeture de la connexion */
pmb_mysql_close($dbh);
		