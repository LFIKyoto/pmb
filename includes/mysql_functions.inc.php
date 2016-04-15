<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_functions.inc.php,v 1.1 2015-04-03 11:16:21 jpermanne Exp $

function pmb_mysql_affected_rows($link_identifier = null){
	global $dbh;

	if ($link_identifier == null) {
		$link_identifier = $dbh;
	}
	$res = mysql_affected_rows($link_identifier);

	return $res;
}

function pmb_mysql_close($link_identifier = null){
	global $dbh;

	if ($link_identifier == null) {
		$link_identifier = $dbh;
	}
	$res = mysql_close($link_identifier);

	return $res;
}

function pmb_mysql_connect($server = null, $username = null, $password = null, $new_link = false, $client_flags = 0){
	if ($server == null) {
		$res = mysql_connect();
	} elseif ($username == null) {
		$res = mysql_connect($server);
	} elseif ($password == null) {
		$res = mysql_connect($server, $username);
	} else {
		$res = mysql_connect($server, $username, $password, $new_link, $client_flags);
	}

	return $res;
}

function pmb_mysql_data_seek($result , $row_number){
	$res = mysql_data_seek($result , $row_number);

	return $res;
}

function pmb_mysql_errno($link_identifier = null){
	global $dbh;

	if ($link_identifier == null) {
		$link_identifier = $dbh;
	}
	$res = mysql_errno($link_identifier);

	return $res;
}

function pmb_mysql_error($link_identifier = null){
	global $dbh;

	if ($link_identifier == null) {
		$link_identifier = $dbh;
	}
	$res = mysql_error($link_identifier);

	return $res;
}

function pmb_mysql_escape_string($unescaped_string){
	$res = mysql_escape_string($unescaped_string);

	return $res;
}

function pmb_mysql_fetch_array($result, $result_type = MYSQL_BOTH){
	$res = mysql_fetch_array($result, $result_type);

	return $res;
}

function pmb_mysql_fetch_assoc($result){
	$res = mysql_fetch_assoc($result);

	return $res;
}

function pmb_mysql_fetch_field($result, $field_offset = 0){
	$res = mysql_fetch_field($result, $field_offset);

	return $res;
}

function pmb_mysql_fetch_object($result, $class_name = "", $params = array()){
	if (!$class_name) {
		$res = mysql_fetch_object($result);
	} elseif (!count($params)) {
		$res = mysql_fetch_object($result, $class_name);
	} else {
		$res = mysql_fetch_object($result, $class_name, $params);
	}

	return $res;
}

function pmb_mysql_fetch_row($result){
	$res = mysql_fetch_row($result);

	return $res;
}

function pmb_mysql_field_flags($result, $field_offset){
	$res = mysql_field_flags($result, $field_offset);

	return $res;
}

function pmb_mysql_field_len($result, $field_offset){
	$res = mysql_field_len($result, $field_offset);

	return $res;
}

function pmb_mysql_field_name($result, $field_offset){
	$res = mysql_field_name($result, $field_offset);

	return $res;
}

function pmb_mysql_field_table($result, $field_offset){
	$res = mysql_field_table($result, $field_offset);

	return $res;
}

function pmb_mysql_field_type($result, $field_offset){
	$res = mysql_field_type($result, $field_offset);

	return $res;
}

function pmb_mysql_free_result($result){
	$res = mysql_free_result($result);

	return $res;
}

function pmb_mysql_get_client_info(){
	$res = mysql_get_client_info();

	return $res;
}

function pmb_mysql_get_host_info($link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_get_host_info($link_identifier);

	return $res;
}

function pmb_mysql_get_proto_info($link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_get_proto_info($link_identifier);

	return $res;
}

function pmb_mysql_get_server_info($link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_get_server_info($link_identifier);

	return $res;
}

function pmb_mysql_insert_id($link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_insert_id($link_identifier);

	return $res;
}

function pmb_mysql_list_tables($database, $link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_list_tables($database, $link_identifier);

	return $res;
}

function pmb_mysql_num_fields($result){
	$res = mysql_num_fields($result);

	return $res;
}

function pmb_mysql_num_rows($result){
	$res = mysql_num_rows($result);

	return $res;
}

function pmb_mysql_query($query, $link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_query($query, $link_identifier);

	return $res;
}

function pmb_mysql_real_escape_string($unescaped_string, $link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_real_escape_string($unescaped_string, $link_identifier);

	return $res;
}

function pmb_mysql_result($result, $row, $field = 0){
	$res = mysql_result($result, $row, $field);

	return $res;
}

function pmb_mysql_select_db($database_name, $link_identifier = null){
	global $dbh;

	if ($link_identifier == null) {
		$link_identifier = $dbh;
	}
	if ($link_identifier == null) {
		$res = mysql_select_db($database_name);
	} else {
		$res = mysql_select_db($database_name, $link_identifier);
	}

	return $res;
}

function pmb_mysql_stat($link_identifier = null){
	global $dbh;

	if($link_identifier == null){
		$link_identifier = $dbh;
	}
	$res = mysql_stat($link_identifier);

	return $res;
}

function pmb_mysql_tablename($result, $i){
	$res = mysql_tablename($result, $i);

	return $res;
}