<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: integre_notices.inc.php,v 1.12 2015-06-23 11:12:48 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/notice_doublon.class.php");

//Recherche de la fonction auxiliaire d'integration
if ($z3950_import_modele) {
	if (file_exists($base_path."/catalog/z3950/".$z3950_import_modele)) {
		require_once($base_path."/catalog/z3950/".$z3950_import_modele);
	} else {
		error_message("", sprintf($msg["admin_error_file_import_modele_z3950"],$z3950_import_modele), 1, "./admin.php?categ=param");
		exit;
	}
} else require_once($base_path."/catalog/z3950/func_other.inc.php");


print "<form class='form-$current_module' name='back' method=\"post\" action=\"catalog.php?categ=search&mode=7&sub=launch\">
	<input type='hidden' name='serialized_search' value='".htmlentities(stripslashes($serialized_search),ENT_QUOTES,$charset)."'/>
	<input type='submit' name='ok' class='bouton' value='".$msg["connecteurs_back_to_list"]."' />&nbsp;
</form>
<script type='text/javascript'>
	function force_integer(ext_id){
		var ajax = new http_request();
		ajax.request('".$base_path."/ajax.php?module=catalog&categ=force_integer&item='+ext_id,true,'&serialized_search=".$sc->serialize_search()."&page=".$page."',true,integer_callback);
	}
	
	function integer_callback(response){
		data = eval('('+response+')');
		var div = document.createElement('div');
		div.setAttribute('id','notice_externe_'+data.id);
		div.innerHTML = data.html;
		document.getElementById('notice_externe_'+data.id).parentNode.replaceChild(div,document.getElementById('notice_externe_'+data.id));
	}
</script>
";
if (is_array($external_notice_to_integer) && count($external_notice_to_integer) ) {
	foreach($external_notice_to_integer as $external_notice){
		//Construction de la notice UNIMARC
		$infos=entrepot_to_unimarc($external_notice);
		$biblio_notice ="";
		if ($infos['notice']) {
			$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
			if($z->bibliographic_level == "a" && $z->hierarchic_level == "2"){
				$biblio_notice = "art";
			}
			if($pmb_notice_controle_doublons != 0){
				$sign = new notice_doublon(true,$infos['source_id']);
				$signature = $sign->gen_signature($external_notice);
				$requete="select signature, niveau_biblio ,niveau_hierar ,notice_id from notices where signature='$signature'";
				$result = pmb_mysql_query($requete);
				if ($dbls=pmb_mysql_num_rows($result)) {
					//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage 	
					require_once("$class_path/mono_display.class.php");
					print "
						<br />
						<div id='notice_externe_".$external_notice."'>
						<div class='erreur'>$msg[540]</div>
						<script type='text/javascript' src='./javascript/tablist.js'></script>
						<div class='row'>
							<div class='colonne10'>
								<img src='./images/error.gif' align='left' />
							</div>
							<div class='colonne80'>
								<strong>".$msg["gen_signature_erreur_similaire"]."</strong>
							</div>
						</div>
						<div class='row'>
							<input type='button' class='bouton' onclick='force_integer(".$external_notice.")' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." '/>
						</div>";
					if($dbls<$nb_per_page_search){
						$maxAffiche=$dbls;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
					}else{
						$maxAffiche=$nb_per_page_search;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
					}
					$enCours=1;
					while($enCours<=$maxAffiche){
						$r=pmb_mysql_fetch_object($result);
						if (($notice->niveau_biblio =='s' || $r->niveau_biblio =='a') && ($r->niveau_hierar== 1 || $r->niveau_hierar== 2)) {
							$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
							$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
							$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
							$link_explnum = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
							$serial = new serial_display($r->notice_id, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum, 0, 0,1, 1);
							$notice_display =  pmb_bidi($serial->result);
						} elseif ($r->niveau_biblio=='m' && $r->niveau_hierar== 0) { 
							$link = './catalog.php?categ=isbd&id=!!id!!';
							$link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!'; 
							$link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!'; 
							$display = new mono_display($r->notice_id, 6, $link, 1, $link_expl, '', $link_explnum,1, 0, 1, 1,"", 1, false, true);
							$notice_display = pmb_bidi($display->result);
				        } elseif ($r->niveau_biblio=='b' && $r->niveau_hierar==2) { // on est face a une notice de bulletin
				        	$requete_suite = "SELECT bulletin_id, bulletin_notice FROM bulletins where num_notice='".$r->notice_id."'";
				        	$result_suite = pmb_mysql_query($requete_suite, $dbh) or die("<br /><br />".pmb_mysql_error()."<br /><br />");
				        	$notice_suite = pmb_mysql_fetch_object($result_suite);
				        	$r->bulletin_id=$notice_suite->bulletin_id;
				        	$r->bulletin_notice=$notice_suite->bulletin_notice;
							$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='.$r->bulletin_id;
							$display = new mono_display($r->notice_id, 6, $link_bulletin, 1, $link_expl, '', $link_explnum,1, 0, 1, 1, "", 1);
							$notice_display = $display->result;
						}
	
						echo "
						<div class='row'>
						$notice_display
				 	    </div>
						<script type='text/javascript'>document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');</script>
						</div>";
						$enCours++;						
					}
					continue;
				}
			}
			$z->signature = $signature;
			if($infos['notice']) $z->notice = $infos['notice'];
			if($infos['source_id']) $z->source_id = $infos['source_id'];
			$z->var_to_post();
			$ret=$z->insert_in_database();
			$id_notice = $ret[1];
			$rqt = "select recid from external_count where rid = '$external_notice'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)) $recid = pmb_mysql_result($res,0,0);
			$req= "insert into notices_externes set num_notice = '".$id_notice."', recid = '".$recid."'";
			pmb_mysql_query($req);
			if ($ret[0]) {
				if($z->bull_id && $z->perio_id){
					$notice_display=new serial_display($ret[1],6);
				} else $notice_display=new mono_display($ret[1],6);
				$retour = "
				<script src='javascript/tablist.js'></script>
				<br /><div class='erreur'></div>
				<div class='row'>
					<div class='colonne10'>
						<img src='./images/tick.gif' align='left'>
					</div>
					<div class='colonne80'>
						<strong>".(isset($notice_id) ? $msg["notice_connecteur_remplaced_ok"] : $msg["z3950_integr_not_ok"])."</strong>
						".$notice_display->result."
					</div>
				</div>";
				if($z->bull_id && $z->perio_id)
					$url_view = "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$z->bull_id&art_to_show=$ret[1]";
				else $url_view = "./catalog.php?categ=isbd&id=".$ret[1];
				$retour .= "
					<div class='row'>
						<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
					</div>";
				print $retour;
			} else if ($ret[1]){
				if($z->bull_id && $z->perio_id){
					$notice_display=new serial_display($ret[1],6);
				} else $notice_display=new mono_display($ret[1],6);
				$retour = "
				<script src='javascript/tablist.js'></script>
				<br /><div class='erreur'>$msg[540]</div>
				<div class='row'>
					<div class='colonne10'>
						<img src='./images/tick.gif' align='left'>
					</div>
					<div class='colonne80'>
						<strong>".($msg["z3950_integr_not_existait"])."</strong><br /><br />
						".$notice_display->result."
					</div>
				</div>";
				if($z->bull_id && $z->perio_id)
					$url_view = "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$z->bull_id&art_to_show=$ret[1]";
				else $url_view = "./catalog.php?categ=isbd&id=".$ret[1];
				$retour .= "
				<div class='row'>
					<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
				</div>
				<script type='text/javascript'>
					document.forms['dummy'].elements['ok'].focus();
				</script>
				</div>
				";
				print $retour;
			}
			else {
				$retour = "<script src='javascript/tablist.js'></script>";
				$retour .= form_error_message($msg["connecteurs_cant_integrate_title"], ($ret[1]?$msg["z3950_integr_not_existait"]:$msg["z3950_integr_not_newrate"]), $msg["connecteurs_back_to_list"], "catalog.php?categ=search&mode=7&sub=launch",array("serialized_search"=>$sc->serialize_search()));
				print $retour;
			}		
			
		}
	}
}