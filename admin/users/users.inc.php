<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.inc.php,v 1.7 2019-08-01 13:52:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$admin_layout = str_replace('!!menu_sous_rub!!', htmlentities($msg[26],ENT_QUOTES, $charset), $admin_layout);
print $admin_layout;
		

require_once('./admin/users/users_func.inc.php');
require_once($class_path.'/user.class.php');
require_once ($class_path . "/event/events/event_user.class.php");

print $admin_user_javascript;

switch($action) {
	case 'pwd':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_pwd");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_pwd.inc.php");
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_pwd");
        $event->set_user_id($id);
        $evt_handler->send($event);
		break;
	case 'modif':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_modif");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_modif.inc.php");
		break;
	case 'update':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_update");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_update.inc.php");
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_update");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		break;
	case 'add':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_add");
        $event->set_user_id($id);
        if( isset($synchro_step) ) {
            $event->set_synchro_step($synchro_step);
        }
        $evt_handler->send($event);
        
		echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);
		$user = new user();
		print $user->get_user_form();
		echo form_focus('userform', 'form_login');
		break;
	case 'del':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_del");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_del.inc.php");
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_del");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		break;
	case 'duplicate':
        
        //Evenement publié
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_duplicate");
        $event->set_user_id($id);
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_duplicate.inc.php");
		break;
	default:
		echo window_title($database_window_title.$msg[25].$msg[1003].$msg[1001]);
		show_users();
		break;
}
