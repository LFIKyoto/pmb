<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sms.class.php,v 1.5 2011-12-28 13:19:48 pmbs Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition des classes d'envoi de sms selon opérateur


class sms_factory {

	public static function make() {
		
		global $empr_sms_config;
		$param_list=array();
		$tab_params=explode(';',$empr_sms_config);	  
		if(is_array($tab_params)) {
			foreach($tab_params as $param){
				$p=explode('=',$param);	
				if(is_array($p)) $param_list[$p[0]]=$p[1];
			}
		}
		if (!$param_list['class_name']) return false;
		$obj = new $param_list['class_name']($param_list);
		return $obj;
	}
} 


class smstrend {
	
	private $login='';
	private $password='';
	private $tpoa='';
	
	function smstrend ($param_list) {		
		$this->login=$param_list["login"];
		$this->password=$param_list["password"];
		$this->tpoa=$param_list["tpoa"];
	}
	
	function send_sms($telephone, $message) {
		global $charset;
		$telephone=preg_replace("/.[^0-9]/","",$telephone); 
		$telephone=preg_replace("/^[\+|[^0-9]]/",'',$telephone);
		if ($telephone[0]=="0") $telephone="+33".substr($telephone,1); 
		else if ($telephone[0]!="+") return false;
		$fields=array(
			"login"=>$this->login,
			"password"=>$this->password,
			"mobile"=>$telephone,
			"messageQty"=>"GOLD",
			"messageType"=>"PLUS",
			"tpoa"=>$this->tpoa, //$object_message,
			"message"=>$message
		);
		if (strtoupper($charset)!="UTF-8") {			
			foreach ($fields as $key=>$val)$fields[$key]=utf8_encode($val);
		}
		foreach ($fields as $key=>$val) $post[]=$key."=".rawurlencode($val);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.smstrend.net/fra/sendMessageFromPost.oeg");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&",$post));
		$r=curl_exec($ch);
		curl_close($ch);
		
		if($r=="OK") return true;
		return false;
	}

}


class sms_rouenbs {
	
	private $ws;
	private $from='';
	
	function sms_rouenbs ($param_list) {
		$this->from=$param_list['from'];
		global $class_path;
		require_once($class_path.'/ws_rouenbs.class.php');
		$this->ws = new ws_rouenbs();
	}
	
	function send_sms($telephone, $message) {
		global $charset;
		$r=FALSE;
		$telephone=preg_replace("/.[^0-9]/",'',$telephone);
		$telephone=preg_replace("/^[\+|[^0-9]]/",'',$telephone);
		if (strtoupper($charset)!='UTF-8') {			
			$message = utf8_encode($message);
			$from = utf8_encode($from);
		}
		$r=$this->ws->SendSMS($message,$telephone,$from);
		return $r;
	}

} // fin de déclaration de la classe sms_pmb
  
