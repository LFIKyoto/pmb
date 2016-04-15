<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.inc.php,v 1.17.4.2 2015-12-07 10:37:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$class_path/curl.class.php");
require_once ("$class_path/caddie.class.php");
require_once ("$class_path/progress_bar.class.php");
require_once ("$include_path/misc.inc.php");

session_write_close();

$admin_layout = str_replace('!!menu_sous_rub!!', $msg[chklnk_titre], $admin_layout);
print $admin_layout;

if (!$suite) {
	echo $admin_chklnk_form ;
} else {
	echo "<h1>".$msg['chklnk_verifencours']."</h1>" ;
	@set_time_limit(0) ;
	$curl = new Curl();
	$curl->timeout=($chkcurltimeout*1 ? $chkcurltimeout*1 : 5);
	$curl->limit=1000;//Limite à 1Ko
	pmb_mysql_query("set wait_timeout=3600");
	
	
	$req_notice = array();
	$req_vign = array();
	$req_explnum_noti = array();
	$req_explnum_bull = array();
	$req_cp = array();
	
	$requete_notice ="select notice_id, tit1, lien from notices !!JOIN!! where lien!='' and lien is not null";
	$requete_vign ="select notice_id, tit1, thumbnail_url from notices !!JOIN!! where thumbnail_url!='' and thumbnail_url is not null";
	$requete_explnum_noti = "select notice_id, tit1, explnum_url, explnum_id from notices !!JOIN!! join explnum on explnum_notice=notice_id and explnum_notice != 0 where explnum_mimetype = 'URL'"; 
	$requete_explnum_bull = "select bulletin_id, concat(notices.tit1,' ',bulletin_numero,' ',date_date) as tit, explnum_url, explnum_id, notices.notice_id from notices join bulletins on notices.notice_id=bulletin_notice !!JOIN!! join explnum on explnum_bulletin=bulletin_id and explnum_bulletin != 0 where explnum_mimetype = 'URL'";	
	$requete_cp = "select distinct notice_id, tit1 from notices join notices_custom_values on notice_id = notices_custom_origine join notices_custom on idchamp = notices_custom_champ !!JOIN!! where type in ('url','resolve')";
	
	
	//on s'occupe des restrictions
	if($chkrestrict){
		//pour les paniers de notice	
		if($idcaddienoti){
			$paniers_ids = implode(",",$idcaddienoti);
			//restriction aux notices des paniers
			$limit_noti = "join caddie_content as c1 on c1.caddie_id in ($paniers_ids) and notice_id = c1.object_id";
			//restriction aux bulletins des notices de bulletins des paniers
			$limit_noti_bull = "join notices as n1 on n1.niveau_biblio = 'b' and n1.niveau_hierar = '2' and num_notice = n1.notice_id join caddie_content as c2 on n1.notice_id = c2.object_id and c2.caddie_id in ($paniers_ids)";

			$req_notice[] =str_replace("!!JOIN!!",$limit_noti,$requete_notice);
			$req_vign[] =str_replace("!!JOIN!!",$limit_noti,$requete_vign);
			$req_explnum_noti[]= str_replace("!!JOIN!!",$limit_noti,$requete_explnum_noti);
			$req_explnum_bull[]=str_replace("!!JOIN!!",$limit_noti_bull,$requete_explnum_bull);
			$req_cp[] = str_replace("!!JOIN!!",$limit_noti,$requete_cp);
		}
		//pour les paniers de bulletins
		if($idcaddiebull){
			$paniers_ids = implode(",",$idcaddiebull);
			//restriction aux bulletins du paniers 
			$limit_bull = "join caddie_content as c3 on c3.caddie_id in ($paniers_ids) and bulletin_id = c3.object_id";
			//restriction aux notices de bulletins associées aux bulletins des paniers
			$limit_bull_noti = "join bulletins as b1 on b1.num_notice = notice_id join caddie_content as c4 on c4.caddie_id in ($paniers_ids) and c4.object_id = b1.bulletin_id";
				
			$req_notice[] =str_replace("!!JOIN!!",$limit_bull_noti,$requete_notice);
			$req_vign[] =str_replace("!!JOIN!!",$limit_bull_noti,$requete_vign);
			$req_explnum_noti[]= str_replace("!!JOIN!!",$limit_bull_noti,$requete_explnum_noti);
			$req_explnum_bull[]=str_replace("!!JOIN!!",$limit_bull,$requete_explnum_bull);
			$req_cp[] = str_replace("!!JOIN!!",$limit_noti,$requete_cp);
		}
		//pour les paniers d'exemplaires
		if($idcaddieexpl){
			$paniers_ids = implode(",",$idcaddieexpl);
			//restriction aux notices associées au exemplaires des paniers
			$limit_expl_noti = "join exemplaires as e1 on e1.expl_notice = notice_id and e1.expl_notice != 0 join caddie_content as c5 on c5.caddie_id in ($paniers_ids) and e1.expl_id = c5.object_id";
			//restrictions aux bulletin associés au exemplaires des paniers
			$limit_expl_bull = "join exemplaires as e2 on e2.expl_bulletin = bulletin_id join caddie_content as c6 on c6.caddie_id in ($paniers_ids) and e2.expl_id = c6.object_id";
			//restriction aux notices de bulletins associées aux bulletins dont les exemplaires sont dans le paniers
			$limit_expl_bull_noti ="join bulletins as b2 on b2.num_notice = notice_id join exemplaires as e3 on e3.expl_bulletin = b2.bulletin_id join caddie_content as c7 on c7.caddie_id in ($paniers_ids) and e3.expl_id = c7.object_id";
			
			$req_notice[] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_notice);
			$req_notice[] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_notice);	
			$req_vign[] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_vign);
			$req_vign[] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_vign);	
			$req_explnum_noti[]= str_replace("!!JOIN!!",$limit_expl_noti,$requete_explnum_noti);
			$req_explnum_bull[]=str_replace("!!JOIN!!",$limit_expl_bull,$requete_explnum_bull);
			$req_cp[] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_cp);
			$req_cp[] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_cp);	
		}
	}else{
		//si on a pas restreint par panier, 
		$req_notice[] =str_replace("!!JOIN!!","",$requete_notice);
		$req_vign[] =str_replace("!!JOIN!!","",$requete_vign);
		$req_explnum_noti[]= str_replace("!!JOIN!!","",$requete_explnum_noti);
		$req_explnum_bull[]=str_replace("!!JOIN!!","",$requete_explnum_bull);
		$req_cp[] = str_replace("!!JOIN!!","",$requete_cp);
	}

	$pb=new progress_bar();
	$pb->pas=10;
	
	if ($chknoti) {
		if ($ajtnoti) {
			$cad=new caddie($idcaddienot);
			$liencad="&nbsp;<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=$idcaddienot\">".$cad->name."</a>";
		} else $liencad="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifnoti']."</label>".$liencad."</div>
			<div class='row'>";
		$q =implode($req_notice," union ");
		$r = pmb_mysql_query($q) ;
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verif_notice']);
		flush();
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->lien);
				if (!$response) {
					echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->lien."\">".$o->lien."</a> <span class='erreur'>".$curl->error."</span></div>";
					if ($ajtnoti) $cad->add_item($o->notice_id,'NOTI');
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->lien."\">".$o->lien."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
					if ($ajtnoti) $cad->add_item($o->notice_id,'NOTI');
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}
	
	if ($chkvign) {
		if ($ajtvign) {
			$cad=new caddie($idcaddievign);
			$liencad="&nbsp;<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=$idcaddievign\">".$cad->name."</a>";
		} else $liencad="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifvign']."</label>".$liencad."</div>
			<div class='row'>";
		$q =implode($req_vign," union ");
		$r = pmb_mysql_query($q) ;
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verif_vign']);
		flush();
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$url=$o->thumbnail_url;
				if(preg_match('`^[a-zA-Z0-9_]+\.php`',$url)){
					$url=$pmb_url_base."/".$url;
				}
				$response = $curl->get($url);
				if (!$response) {
					echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->thumbnail_url."\">".$o->thumbnail_url."</a> <span class='erreur'>".$curl->error."</span></div>";
					if ($ajtvign) $cad->add_item($o->notice_id,'NOTI');
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->thumbnail_url."\">".$o->thumbnail_url."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
					if ($ajtvign) $cad->add_item($o->notice_id,'NOTI');
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}
	
	if($chkcp){
		if($ajtcp){
			$cad=new caddie($idcaddiecp);
			$liencad="&nbsp;<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=$idcaddiecp\">".$cad->name."</a>";
		} else $liencad="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifcp']."</label>".$liencad."</div>
			<div class='row'>";			
		$q =implode($req_cp," union ");
		$r = pmb_mysql_query($q) ;
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verif_cp']);
		flush();
		$pp = new parametres_perso("notices");
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$pp->get_values($o->notice_id);
				foreach($pp->values as $id_cp => $values){
					if($pp->t_fields[$id_cp]['TYPE'] == "url"){
						foreach($values as $value){
							$link = "";
							if(strpos($value,"|")!== false){
								$link = substr($value,0,strpos($value,"|"));
							}else $link = $value;
							$response = $curl->get($link);
							if (!$response) {
								echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
								if ($ajtcp) $cad->add_item($o->notice_id,'NOTI');
							} elseif ($response->headers['Status-Code']!='200') {
								if($response->headers['Status-Code']){
									$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
								}else{
									$tmp=$msg["curl_no_status_code"];
								}
								echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
								if ($ajtcp) $cad->add_item($o->notice_id,'NOTI');
							}
						}
					}else if ($pp->t_fields[$id_cp]['TYPE'] == "resolve"){
						$options=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$pp->t_fields[$id_cp]['OPTIONS'], "OPTIONS");
						foreach($values as $value){
							$link = "";
							$val = explode("|",$value);
							if(count($val)>1){
								$id =$val[0];
								foreach ($options[RESOLVE] as $res){
									if($res[ID] == $val[1]){
										$label = $res[LABEL];
										$url= $res[value];
										break;
									}
								}
								$link = str_replace("!!id!!",$id,$url);
								$response = $curl->get($link);
								if (!$response) {
									echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
									if ($ajtcp) $cad->add_item($o->notice_id,'NOTI');
								} elseif ($response->headers['Status-Code']!='200') {
									if($response->headers['Status-Code']){
										$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
									}else{
										$tmp=$msg["curl_no_status_code"];
									}
									echo "<div class='row'><a href=\"./catalog.php?categ=isbd&id=".$o->notice_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
									if ($ajtcp) $cad->add_item($o->notice_id,'NOTI');
								}
							}
						}
					}
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();		
	}	
	
	if ($chkenum) {
		$resl="";
		if ($ajtenum) {
			$cad=new caddie($idcaddielnk);
			$liencad="&nbsp;<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=$idcaddielnk\">".$cad->name."</a>";
		} else $liencad="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifenum']."</label>".$liencad."</div>
			<div class='row'>";

		$q = implode($req_explnum_noti," union ");
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_docnum']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->explnum_url);
				if (!$response) {
					echo "<div class='row'><a href=\"./catalog.php?categ=edit_explnum&id=".$o->notice_id."&explnum_id=".$o->explnum_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->explnum_url."\">".$o->explnum_url."</a> <span class='erreur'>".$curl->error."</span></div>";
					if ($ajtenum) $cad->add_item($o->notice_id,'NOTI');
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./catalog.php?categ=edit_explnum&id=".$o->notice_id."&explnum_id=".$o->explnum_id."\">".$o->tit1."</a>&nbsp;<a href=\"".$o->explnum_url."\">".$o->explnum_url."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
					if ($ajtenum) $cad->add_item($o->notice_id,'NOTI');
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}
	
	if ($chkbull) {
		$resl="";
		if ($ajtbull) {
			$cad=new caddie($idcaddiebul);
			$liencad="&nbsp;<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=$idcaddiebul\">".$cad->name."</a>";
		} else $liencad="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifbull']."</label>".$liencad."</div>
			<div class='row'>";

		$q = implode($req_explnum_bull," union ");
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_bull']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->explnum_url);
				if (!$response) {
					echo "<div class='row'><a href=\"./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=".$o->bulletin_id."&explnum_id=".$o->explnum_id."\">".$o->tit."</a>&nbsp;<a href=\"".$o->explnum_url."\">".$o->explnum_url."</a> <span class='erreur'>".$curl->error."</span></div>";
					if ($ajtbull) $cad->add_item($o->bulletin_id,'BULL');
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=".$o->bulletin_id."&explnum_id=".$o->explnum_id."\">".$o->tit."</a>&nbsp;<a href=\"".$o->explnum_url."\">".$o->explnum_url."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
					if ($ajtbull) $cad->add_item($o->bulletin_id,'BULL');
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}

	if ($chkautaut) {
		$resl="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifautaut']."</label></div>
			<div class='row'>";
		$q = "select author_id, concat(author_name,', ',author_rejete,' - ',author_date) as nom_auteur, author_web from authors where author_web!='' and author_web is not null order by index_author ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_auteur']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->author_web);
				if (!$response) {
					echo "<div class='row'><a href=\"./autorites.php?categ=auteurs&sub=author_form&id=".$o->author_id."\">".$o->nom_auteur."</a>&nbsp;<a href=\"".$o->author_web."\">".$o->author_web."</a> <span class='erreur'>".$curl->error."</span></div>";
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./autorites.php?categ=auteurs&sub=author_form&id=".$o->author_id."\">".$o->nom_auteur."</a>&nbsp;<a href=\"".$o->author_web."\">".$o->author_web."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}

	if ($chkautpub) {
		$resl="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifautpub']."</label></div>
			<div class='row'>";
		$q = "select ed_id, concat(ed_name,' - ',ed_ville,' - ',ed_pays) as nom_pub, ed_web from publishers where ed_web!='' and ed_web is not null order by index_publisher ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_editeur']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->ed_web);
				if (!$response) {
					echo "<div class='row'><a href=\"./autorites.php?categ=editeurs&sub=editeur_form&id=".$o->ed_id."\">".$o->nom_pub."</a>&nbsp;<a href=\"".$o->ed_web."\">".$o->ed_web."</a> <span class='erreur'>".$curl->error."</span></div>";
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./autorites.php?categ=editeurs&sub=editeur_form&id=".$o->ed_id."\">".$o->nom_pub."</a>&nbsp;<a href=\"".$o->ed_web."\">".$o->ed_web."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}

	if ($chkautcol) {
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifautcol']."</label></div>
			<div class='row'>";
		$q = "select collection_id, concat(collection_name,' - ',collection_issn) as nom_col, collection_web from collections where collection_web!='' and collection_web is not null order by index_coll ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_coll']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->collection_web);
				if (!$response) {
					echo "<div class='row'><a href=\"./autorites.php?categ=collections&sub=collection_form&id=".$o->collection_id."\">".$o->nom_col."</a>&nbsp;<a href=\"".$o->collection_web."\">".$o->collection_web."</a> <span class='erreur'>".$curl->error."</span></div>";
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./autorites.php?categ=collections&sub=collection_form&id=".$o->collection_id."\">".$o->nom_col."</a>&nbsp;<a href=\"".$o->collection_web."\">".$o->collection_web."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}

	if ($chkautsco) {
		$resl="";
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifautsco']."</label></div>
			<div class='row'>";
		$q = "select sub_coll_id, concat(sub_coll_name,' - ',sub_coll_issn) as nom_sco, subcollection_web from sub_collections where subcollection_web!='' and subcollection_web is not null order by index_sub_coll ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_ss_coll']);
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$response = $curl->get($o->subcollection_web);
				if (!$response) {
					echo "<div class='row'><a href=\"./autorites.php?categ=souscollections&sub=collection_form&id=".$o->sub_coll_id."\">".$o->nom_sco."</a>&nbsp;<a href=\"".$o->subcollection_web."\">".$o->subcollection_web."</a> <span class='erreur'>".$curl->error."</span></div>";
				} elseif ($response->headers['Status-Code']!='200') {
					if($response->headers['Status-Code']){
						$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
					}else{
						$tmp=$msg["curl_no_status_code"];
					}
					echo "<div class='row'><a href=\"./autorites.php?categ=souscollections&sub=collection_form&id=".$o->sub_coll_id."\">".$o->nom_sco."</a>&nbsp;<a href=\"".$o->subcollection_web."\">".$o->subcollection_web."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}
	
	if($chkeditorialcontentcp){
		echo "<div class='row'><hr /></div><div class='row'><label class='etiquette' >".$msg['chklnk_verifeditorialcontentcp']."</label></div>
			<div class='row'>";
		$q = "select distinct id_article, article_title from cms_articles join cms_editorial_custom_values on id_article = cms_editorial_custom_origine join cms_editorial_custom on idchamp = cms_editorial_custom_champ where type in ('url','resolve')";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_editorial_content_cp']);
		flush();
		$pp = new parametres_perso("cms_editorial");
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$pp->get_values($o->id_article);
				foreach($pp->values as $id_cp => $values){
					if($pp->t_fields[$id_cp]['TYPE'] == "url"){
						foreach($values as $value){
							$link = "";
							if(strpos($value,"|")!== false){
								$link = substr($value,0,strpos($value,"|"));
							}else $link = $value;
							$response = $curl->get($link);
							if (!$response) {
								echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->article_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
							} elseif ($response->headers['Status-Code']!='200') {
								if($response->headers['Status-Code']){
									$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
								}else{
									$tmp=$msg["curl_no_status_code"];
								}
								echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->article_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
							}
						}
					}else if ($pp->t_fields[$id_cp]['TYPE'] == "resolve"){
						$options=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$pp->t_fields[$id_cp]['OPTIONS'], "OPTIONS");
						foreach($values as $value){
							$link = "";
							$val = explode("|",$value);
							if(count($val)>1){
								$id =$val[0];
								foreach ($options[RESOLVE] as $res){
									if($res[ID] == $val[1]){
										$label = $res[LABEL];
										$url= $res[value];
										break;
									}
								}
								$link = str_replace("!!id!!",$id,$url);
								$response = $curl->get($link);
								if (!$response) {
									echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->article_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
								} elseif ($response->headers['Status-Code']!='200') {
									if($response->headers['Status-Code']){
										$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
									}else{
										$tmp=$msg["curl_no_status_code"];
									}
									echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->article_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
								}
							}
						}
					}
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div><div class='row'>";
		$q = "select distinct id_section, section_title from cms_sections join cms_editorial_custom_values on id_section = cms_editorial_custom_origine join cms_editorial_custom on idchamp = cms_editorial_custom_champ where type in ('url','resolve')";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
		if ($r) $rc=pmb_mysql_num_rows($r);
		else $rc=0;
		$pb->count=$rc;
		$pb->nb_progress_call=0;
		$pb->set_text($msg['chklnk_verifurl_editorial_content_cp']);
		flush();
		$pp = new parametres_perso("cms_editorial");
		if ($r) {
			while ($o=pmb_mysql_fetch_object($r)) {
				$pp->get_values($o->id_section);
				foreach($pp->values as $id_cp => $values){
					if($pp->t_fields[$id_cp]['TYPE'] == "url"){
						foreach($values as $value){
							$link = "";
							if(strpos($value,"|")!== false){
								$link = substr($value,0,strpos($value,"|"));
							}else $link = $value;
							$response = $curl->get($link);
							if (!$response) {
								echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->section_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
							} elseif ($response->headers['Status-Code']!='200') {
								if($response->headers['Status-Code']){
									$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
								}else{
									$tmp=$msg["curl_no_status_code"];
								}
								echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->section_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
							}
						}
					}else if ($pp->t_fields[$id_cp]['TYPE'] == "resolve"){
						$options=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$pp->t_fields[$id_cp]['OPTIONS'], "OPTIONS");
						foreach($values as $value){
							$link = "";
							$val = explode("|",$value);
							if(count($val)>1){
								$id =$val[0];
								foreach ($options[RESOLVE] as $res){
									if($res[ID] == $val[1]){
										$label = $res[LABEL];
										$url= $res[value];
										break;
									}
								}
								$link = str_replace("!!id!!",$id,$url);
								$response = $curl->get($link);
								if (!$response) {
									echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->section_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$curl->error."</span></div>";
								} elseif ($response->headers['Status-Code']!='200') {
									if($response->headers['Status-Code']){
										$tmp=$curl->reponsecurl[$response->headers['Status-Code']];
									}else{
										$tmp=$msg["curl_no_status_code"];
									}
									echo "<div class='row'><a href=\"./cms.php?categ=editorial&sub=list\">".$o->section_title."</a>&nbsp;<a href=\"".$link."\">".$link."</a> <span class='erreur'>".$response->headers['Status-Code']." -> ".$tmp."</span></div>";
								}
							}
						}
					}
				}
				$pb->progress();
				flush();
			}
		}
		echo "</div>";
		flush();
	}

	if ($curl->timeout != $pmb_curl_timeout) {
		$q = "update parametres set valeur_param='".$curl->timeout."' where type_param='pmb' and sstype_param='curl_timeout'";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br />".$q);
	}

	echo "<div class='row'><hr /></div><h1>".$msg['chklnk_fin']."</h1>";
}
