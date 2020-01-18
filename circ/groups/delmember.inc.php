<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delmember.inc.php,v 1.5 2019-07-12 10:25:27 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$group = new group($groupID);
$res = $group->del_member($memberID);

if($res) {
    include('./circ/groups/show_group.inc.php');
} else {
	error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
}

?>