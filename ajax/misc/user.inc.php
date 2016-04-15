<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user.inc.php,v 1.3 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($fname) {
	case 'get_group' :
		$q = 'select grp_num from users where userid = '.$PMBuserid.' limit 1';
		$r = pmb_mysql_query($q, $dbh);
		if (pmb_mysql_num_rows($r)) {
			$grp = pmb_mysql_result($r,0,0);
			ajax_http_send_response($grp);
		}
		break;
}