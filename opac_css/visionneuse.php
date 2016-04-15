<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.php,v 1.32.4.1 2015-10-01 13:35:53 arenou Exp $
$base_path = ".";
$include_path ="$base_path/includes";
$class_path ="$base_path/classes";
$visionneuse_path="$base_path/visionneuse";
//y a plein de trucs à récup...
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path.'/includes/opac_config.inc.php');
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();
require_once($base_path."/includes/session.inc.php");
//vraiment plein...

require_once($include_path.'/misc.inc.php');

require_once($base_path.'/includes/start.inc.php');
require_once($base_path.'/includes/divers.inc.php');
require_once($include_path.'/templates/common.tpl.php');
require_once($base_path."/includes/includes_rss.inc.php");
require_once($class_path."/cms/cms_cache.class.php");
//c'est bon, on peut commencer...

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

require_once($visionneuse_path."/classes/visionneuse.class.php");

if($opac_parse_html || $cms_active){
	ob_start();
}

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	if($opac_view==-1){
		$_SESSION["opac_view"]="default_opac";
	}else if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view*1;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");

	$opac_view_class= new $pmb_opac_view_class($_SESSION["opac_view"],$_SESSION["id_empr_session"]);
	if($opac_view_class->id){
		$opac_view_class->set_parameters();
		$opac_view_filter_class=$opac_view_class->opac_filters;
		$_SESSION["opac_view"]=$opac_view_class->id;
		if(!$opac_view_class->opac_view_wo_query) {
			$_SESSION['opac_view_query']=1;
		}
	}else{
		$_SESSION["opac_view"]=0;
	}
	$css=$_SESSION["css"]=$opac_default_style;
}

$explnum_id+=0;

//Pour les epubs
if (isset($_SERVER["PATH_INFO"])) {
	$myPage='';
	$tmpEpub = explode("/",trim($_SERVER["PATH_INFO"],"/"));
	$lvl = 'afficheur';
	$driver = array_shift($tmpEpub);
	$explnum = array_shift($tmpEpub);
	$myPage = implode("/",$tmpEpub);
}else{
	if(!$myPage)
		$myPage='';
}

switch($driver){
	case "pmb_document" :
		require_once($visionneuse_path."/api/pmb/pmb_document.class.php");
		if($lvl == "" || $lvl == "visionneuse"){
			$lvl = "visionneuse";
			$short_header= str_replace("!!liens_rss!!","",$short_header);
			print $short_header;
		}
		if($lvl!="ajax"){
			$params = array(
					'lvl' => $lvl,
					'type' => $cms_type,
					'num_type' => $num_type,
					'id' => $id,
					'explnum' => $explnum,
					'explnum_id' => $explnum_id,
					'user_query' => $user_query,
					'position' => $position,
					"page" => $myPage
			);
		}else{
			$params = array(
					'lvl' => $lvl,
					'explnum_id' => $explnum_id,
					'start' => true,
					'action' => $action,
					'method' => $method,
			);
		}
		$visionneuse = new visionneuse($driver,$visionneuse_path,$lvl,$lang,$params);
		break;
	case "pmb" :
	default :
		require_once($visionneuse_path."/api/pmb/pmb.class.php");
		if($lvl == "" || $lvl == "visionneuse"){
			$lvl = "visionneuse";
			$short_header= str_replace("!!liens_rss!!","",$short_header);
			print $short_header;
			$opac_allow_simili_search=0;
			$opac_notice_enrichment=0;
			print "<script type='text/javascript' src='$include_path/javascript/tablist.js'></script>";
		}
		if (isset($_POST["position"])){
			$position = $_POST["position"];
			if ($lvl == "visionneuse"){
				$start = false;
			}else{
				$start = true;
			}
		}else{
			$position = 0;
			$start = true;
		}
		if($lvl == "afficheur" || $lvl == "visionneuse"){
			$params = array(
					"mode" => $mode,
					"user_query" => $user_query,
					"pert" => $pert,
					"join" => $join,
					"clause" => $clause,
					"clause_bull" => $clause_bull,
					"clause_bull_num_notice" => $clause_bull_num_notice,
					"tri" => $tri,
					"table" => $table,
					"user_code" => $_SESSION["user_code"],
					"idautorite" => $idautorite,
					"id" => $id,
					"idperio" => $idperio,
					"search" => $search,
					"bulletin" => $bulletin,
					"explnum_id" => $explnum_id,
					"position" => $position,
					"start" => $start,
					"lvl" => $lvl,
					"explnum" => $explnum,
					"page" => $myPage
			);
		}else{
			$params = array(
					'explnum_id' => $explnum_id,
					'start' => true,
					'action' => $action,
					'method' => $method,
						
			);
		}
		
		$visionneuse = new visionneuse("pmb",$visionneuse_path,$lvl,$lang,$params);
		break;
}

if($lvl == "" || $lvl == "visionneuse"){
	if($opac_visionneuse_alert) {
		$confirm_alert=false;
		if ($opac_visionneuse_alert_doctype) {
			$t_opac_visionneuse_alert_doctype=explode(',',$opac_visionneuse_alert_doctype);
			$q = 'select typdoc from explnum join notices on explnum_notice=notice_id and explnum_id='.$explnum_id.' ';
			$q.= 'union ';
			$q.= 'select typdoc from explnum join bulletins on explnum_bulletin=bulletin_id and explnum_id='.$explnum_id.' join notices on num_notice=notice_id ';
			$q.= 'union ';
			$q.= 'select typdoc from explnum join bulletins on explnum_bulletin=bulletin_id and explnum_id='.$explnum_id.' join notices on bulletin_notice=notice_id';
			$r = pmb_mysql_query($q,$dbh);
			if (pmb_mysql_num_rows($r)) {
				$typdoc = pmb_mysql_result($r,0,0);
				if (is_array($t_opac_visionneuse_alert_doctype) && in_array($typdoc,$t_opac_visionneuse_alert_doctype)) {
					$confirm_alert=true;
				}
			}
		}
		if ($confirm_alert) {
			print "<script type='text/javascript'>window.parent.open_alertbox('".addslashes(trim($opac_visionneuse_alert))."');</script>";
		}
	}
	print $short_footer;

}

if(($opac_parse_html)&&($lvl!="afficheur")&&($lvl!="ajax")){
	$htmltoparse= parseHTML(ob_get_contents());
	ob_end_clean();
	print $htmltoparse;
}
		

	/*if ($cms_active) {
		require_once($base_path."/classes/cms/cms_build.class.php");
		$cms=new cms_build();
		$htmltoparse = $cms->transform_html($htmltoparse);
	}*/


?>