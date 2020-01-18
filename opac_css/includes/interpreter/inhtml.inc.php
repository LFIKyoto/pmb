<?php
// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.10.8.3 2019-11-15 09:50:18 dgoron Exp $

require_once ($include_path . "/misc.inc.php");

$func_format['if_logged']= 'aff_if_logged';
$func_format['if_logged_lang']= 'aff_if_logged_lang';
$func_format['message_lang']= 'aff_message_lang';
$func_format['if_param']= 'aff_if_param';
$func_format['eval_php']= 'aff_eval_php';
$func_format['perio_a2z']= 'aff_perio_a2z';
$func_format['if_session_param']= 'aff_if_session_param';
$func_format['hidden_global_var_form']= 'aff_hidden_global_var_form';



$var_format = array();

function aff_eval_php($param) {
	eval($param[0]);
	return $ret;
}

function aff_if_param($param) {
	//Nom de la variable a tester, valeur, si =, si <>
	$varname=$param[0];
	global ${$varname};
	if (${$varname}==$param[1]) $ret=$param[2]; else $ret=$param[3];
	return $ret;
}

function aff_if_session_param($param) {
	//Nom de la variable a tester, valeur, si =, si <>
	if ($_SESSION[$param[0]]==$param[1]) $ret=$param[2]; else $ret=$param[3];
	return $ret;
}

function aff_if_logged($param) {
	if ($_SESSION['id_empr_session']) {
		$ret = $param[0];
	}else {
		if($param[1]) $ret = $param[1];
		else $ret ="";
	}
	return $ret;
}

function aff_if_logged_lang($param) {
	global $lang;
	if ($lang==$param[2]) {
		if ($_SESSION['id_empr_session']) {
			$ret = $param[0];
		}else {
			if($param[1]) $ret = $param[1];
			else $ret ="";
		}
	} else $ret="";
	return $ret;
}

function aff_message_lang($param) {
	global $lang;
	if ($lang==$param[1])
	return $param[0]; else return "";
}

function aff_perio_a2z($param) {
	global $base_path;
	global $opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet;
	require_once($base_path."/classes/perio_a2z.class.php");		
	$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);		
	return $perio_a2z=$a2z->get_form();
}

function aff_hidden_global_var_form($param) {
    global $charset;
    
    if(empty($param[0])) {
        $param[0] = 'hidden_global_var_form';
    }
    $action = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/')+1);
    $hidden_form = "<form method=\"post\" name='".$param[0]."' action=\"".$action."\" >";
    if(count($_POST)) {
        foreach ($_POST as $name=>$value) {
            if(is_string($value)) {
                $hidden_form .= "<input type='hidden' name=\"".$name."\" value=\"".htmlentities($value, ENT_QUOTES, $charset)."\" />";
            }
        }
    }
    if(!empty($param[1])) {
        $hidden_form .= "<input type='hidden' name=\"".$param[1]."\" value='' />";
    }
    $hidden_form .= "</form>"; 
    return $hidden_form;
}

?>