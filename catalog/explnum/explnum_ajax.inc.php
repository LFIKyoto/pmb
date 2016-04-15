<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_ajax.inc.php,v 1.7 2015-04-03 11:16:28 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/explnum_associate_svg.class.php');

switch($quoifaire){
	
	case 'exist_file':
		existing_file($id,$id_repertoire);	
		break;
	case 'get_associate_svg':
		get_associate_svg($explnum_id);
		break;
	case 'get_associate_js':
		get_associate_js($explnum_id);
		break;
	case 'update_associate_author':
		update_associate_author($speaker_id, $author_id);
		break;
	case 'update_associate_speaker':
		update_associate_speaker($segment_id, $speaker_id);
		break;
	case 'add_new_speaker':
		add_new_speaker($explnum_id);
		break;
	case 'delete_associate_speaker':
		delete_associate_speaker($speaker_id);
		break;
	case 'add_new_segment':
		add_new_segment($explnum_id, $speaker_id, $start, $end);
		break;
	case 'delete_segments':
		delete_segments($segments_ids);
		break;
	case 'update_segment_time':
		update_segment_time($segment_id, $start, $end);
		break;
}

function existing_file($id,$id_repertoire){
	
	global $dbh,$fichier;
	
	if(!$id){
		$rqt = "select repertoire_path, explnum_path, repertoire_utf8, explnum_nomfichier as nom, explnum_extfichier as ext from explnum join upload_repertoire on explnum_repertoire=repertoire_id  where explnum_repertoire='$id_repertoire' and explnum_nomfichier ='$fichier'";
		$res = pmb_mysql_query($rqt,$dbh);
		
		if(pmb_mysql_num_rows($res)){
			$expl = pmb_mysql_fetch_object($res);
			$path = str_replace('//','/',$expl->repertoire_path.$expl->explnum_path);
			if($expl->repertoire_utf8)
				$path = utf8_encode($path);
					
			if($expl->ext)
				$file = substr($expl->nom,0,strpos($expl->nom,"."));
			else $file = $expl->nom;
			$exist = false;
			$i=0;
			while(!$exist){
				$i++;
				$filename = ($i ? $file."_".$i : $file).($expl->ext ? ".".$expl->ext : "");
				if(!file_exists($path.$filename)){
					print $filename;
					$exist = true;
				}
			}
		} else print "0";
	} else print "0";
}

function get_associate_svg($explnum_id) {
	$explnum_associate_svg = new explnum_associate_svg($explnum_id);
	$svg = $explnum_associate_svg->getSvg(true);
	ajax_http_send_response($svg,"text/xml");
}

function get_associate_js($explnum_id) {
	$explnum_associate_svg = new explnum_associate_svg($explnum_id);
	$js = $explnum_associate_svg->getJs(true);
	ajax_http_send_response($js,"text/xml");
}

function update_associate_author($speaker_id, $author_id) {
	global $dbh;
	$query = 'update explnum_speakers set explnum_speaker_author = '.$author_id.' where explnum_speaker_id = '.$speaker_id;
	pmb_mysql_query($query, $dbh);
}

function update_associate_speaker($segment_id, $speaker_id) {
	global $dbh;
	$query = 'update explnum_segments set explnum_segment_speaker_num = '.$speaker_id.' where explnum_segment_id = '.$segment_id;
	pmb_mysql_query($query, $dbh);
}

function add_new_speaker($explnum_id) {
	global $dbh;
	$query = 'insert into explnum_speakers (explnum_speaker_explnum_num, explnum_speaker_speaker_num) values ('.$explnum_id.', "PMB")';
	pmb_mysql_query($query, $dbh);
}

function delete_associate_speaker($speaker_id) {
	global $dbh;
	$query = 'delete from explnum_speakers where explnum_speaker_id = '.$speaker_id;
	pmb_mysql_query($query, $dbh);
}

function add_new_segment($explnum_id, $speaker_id, $start, $end) {
	global $dbh;
	if (!$speaker_id) {
		$query = 'insert into explnum_speakers (explnum_speaker_explnum_num, explnum_speaker_speaker_num) values ('.$explnum_id.', "PMB")';
		pmb_mysql_query($query, $dbh);
		$speaker_id = pmb_mysql_insert_id();
	}
	$duration = $end - $start;
	$query = 'insert into explnum_segments (explnum_segment_explnum_num, explnum_segment_speaker_num, explnum_segment_start, explnum_segment_duration, explnum_segment_end) value ('.$explnum_id.', '.$speaker_id.', '.$start.', '.$duration.', '.$end.')';
	pmb_mysql_query($query, $dbh);
}

function delete_segments($segments_ids) {
	global $dbh;
	$query = 'delete from explnum_segments where explnum_segment_id in ('.$segments_ids.')';
	pmb_mysql_query($query, $dbh);
}

function update_segment_time($segment_id, $start, $end) {
	global $dbh;
	
	$query = 'update explnum_segments set ';
	
	if ($start) {
		$query .= 'explnum_segment_start = '.$start.', ';
	} else {
		$select = 'select explnum_segment_start from explnum_segments where explnum_segment_id = '.$segment_id;
		$result = pmb_mysql_query($select, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				$start = $row->explnum_segment_start;
			}
		}
	}
	
	if ($end) {
		$query .= 'explnum_segment_end = '.$end.', ';
	} else {
		$select = 'select explnum_segment_end from explnum_segments where explnum_segment_id = '.$segment_id;
		$result = pmb_mysql_query($select, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				$end = $row->explnum_segment_end;
			}
		}
	}
	
	$duration = $end - $start;
	
	$query .= 'explnum_segment_duration = '.$duration.' where explnum_segment_id = '.$segment_id;
	pmb_mysql_query($query, $dbh);
}

?>