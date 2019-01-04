<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chat.inc.php,v 1.1 2018-10-03 12:45:49 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/chat/chat.class.php");

$chat = new chat();
//print encoding_normalize::utf8_normalize($chat->proceed());
print $chat->proceed();
