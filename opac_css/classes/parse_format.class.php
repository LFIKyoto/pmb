<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parse_format.class.php,v 1.12 2014-10-28 11:04:51 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");


require_once ($include_path . "/misc.inc.php");

class parse_format {
	var $error; //Erreur
	var $error_message; //Message d'erreur
	var $environnement=array();

	function parse_format($filename='interpreter.inc.php',$in_relation=false) {
		global $include_path;
		global $func_format;
		global $var_format;
		global $class_path;
		require_once ( $include_path."/interpreter/$filename");	
		$this->func_format=$func_format;
		$this->var_format=$var_format;
		$this->var_return='';
		$this->in_relation = $in_relation;
	}

	
	function exec_function($function_name, $param_name, $param_number) {
		if($this->in_relation == false || ($this->in_relation== true && $function_name != "get_childs_in_tpl" && $function_name != "get_parents_in_tpl")){
			if(! $this->func_format[$function_name]) return " $function_name not found ";
			$ret = $this->func_format[$function_name]( $param_name, $this);
			return $ret;
		}else{
			return "";
		}
	}
	
	function exec($cmd, & $i) {
		$state = 0;
		$ret = "";
		$function_name='';
		$param_uneval='';
		for ($i; $i < strlen($cmd); $i++) {
			switch ($state) {
				case '0' : //state Normal
					switch ($cmd[$i]) {
						case '$' :
							$state = "get_param_name";
							$param_name = '';
							break;
						case '#' :
							$state = "get_token";
							break;
						default :
							break;
					}
					break;
				case 'get_token' : //get param name
					if(($cmd[$i -2] == '&') && ($cmd[$i -1] == '#') && preg_match('/[0-9]/',$cmd[$i])){//Il n'y a pas de fonction commençant par un chiffre, il s'agit d'une entité html
						return "#".$cmd[$i];
						break;
					}
					switch ($cmd[$i]) {
						case ' ' :
						case '"' :
						case "'" :
						case '<' :
						case '>' :
						case "\r" :
						case "\n" :
						case "\t" :
							return "#".$function_name.$cmd[$i];  //La liste de caractères précédente permet de déterminer qu'on doit sortir de l'interpretation
							break;
						case '{' :
							if ($cmd[$i -1] == '#') { // fin d'un parametre à ne pas évaluer par };
								$state='get_sub_param';
							}else{
								return	$function_name.'{';
							}
							break;
						case '(' :
							$param_number = 0;
							$param_name[$param_number] = '';
							$state = "get_function_param";
							if(($function_name == "SET") || ($function_name == "set")) $this->var_set=1;
							elseif(! $this->func_format[$function_name]) return "#".$function_name.$cmd[$i]; // ce n'est pas une fonction existante, on retourne la chaine
							break;
						default :
							$function_name .= $cmd[$i];
							break;
					}
					break;
				case 'get_sub_param' :
					switch ($cmd[$i]) {
						case '}' :
							if ($cmd[$i +1] == ';') { // fin d'un parametre à ne pas évaluer par };
								$i++;
								return $param_uneval;
							}
							break;
						default:
							$param_uneval.=$cmd[$i];
							$state='get_sub_param';
							break;
					}
					break;
				case 'get_param_name' : //get param name
					
					switch ($cmd[$i]) {
						case ' ' :
						case '"' :
						case "'" :
						case '<' :
						case '>' :
						case "\r" :
						case "\n" :
						case "\t" :
							return "$".$param_name.$cmd[$i];
							break;
						case ';' :
							if($this->var_set){
								$this->var_set_name=$param_name;
								$this->var_set=0;
							}	
							return ($this->var_format[$param_name]);
							break;
						case '=' :
							$this->var_return=$param_name;
							return ($param_name);
							break;	
						default :
							$param_name .= $cmd[$i];
						break;
					}
					break;
				case 'get_function_name' : //get param name
	
					switch ($cmd[$i]) {
						case ' ' :
						case '"' :
						case "'" :
						case '<' :
						case '>' :
						case "\r" :
						case "\n" :
						case "\t" :
						case ";" :
							return "#".$function_name.$cmd[$i];
							break;
						case '(' :
							$param_number = 0;
							$param_name[$param_number] = '';
							$state = "get_function_param";
							if(($function_name == "SET") || ($function_name == "set")) $this->var_set=1;
							break;
						default :
							$function_name .= $cmd[$i];
							break;
					}
					break;
				case 'get_function_param' : //get param name
					
					switch ($cmd[$i]) {
						case '$' :
							if($param_name[$param_number])$param_name[$param_number] .= $this->exec($cmd, $i);
							else $param_name[$param_number]= $this->exec($cmd, $i);
							break;
						case '#' :
							
							if($param_name[$param_number])$param_name[$param_number] .= $this->exec($cmd, $i);
							else $param_name[$param_number]= $this->exec($cmd, $i);
							break;
						case ')' :
							if ($cmd[$i +1] == ';') { // fin d'une fonction par );
								$i++;
								if(($function_name == "SET") || ($function_name == "set")) {
									$this->var_format[$this->var_set_name]=$param_name[1];
									eval( "global \$".$param_name[0].";\$".$param_name[0]."=\"".$param_name[1]."\";");
									$this->var_set=0;
									return '';
								}else 
									return ($this->exec_function($function_name, $param_name, $param_number));		

							} else {
								$param_name[$param_number] .= ')';
							}
							break;
						case ',' :
							$param_number++;
							$param_name[$param_number] = '';
							break;
						default :
							if( ($cmd[$i]=='\\') && ( ($i+1) < strlen($cmd)) )$i++;
							$param_name[$param_number] .= $cmd[$i];
							break;
					}
					break;
				default :
				break;
			}
		}
		return $ret;
	}
		
	function exec_cmd($no_escape=false) {
	
		$cmd=$this->cmd;
		
		$ret = "";
		for ($i = 0; $i < strlen($cmd); $i++) {
			switch ($cmd[$i]) {
				case '$' :
				case '#' :
					$return = $this->exec($cmd, $i);
					
					if(!$this->var_return){	
						//C'est le retour pour afficher
						$ret .=$return;
							
					}else{
						//C'est une affectation d'une variable
						$this->var_format[$this->var_return]=$this->exec($cmd, $i);
						$this->var_return='';
					}	
					if ($this->erreur == 1) {
						return -1;
					}
					break;
				default :
					if (!$no_escape) if( ($cmd[$i]=='\\') && ( ($i+1) < strlen($cmd)) )$i++;
					$ret .= $cmd[$i];
					break;
			}
		}
		return $ret;
	}

	
	function exec_cmd_conso() {

	$cmd=$this->cmd;
	
		//$ret = "";
		for ($i = 0; $i < strlen($cmd); $i++) {
			switch ($cmd[$i]) {
				case '$' :
				case '#' :
					$return = $this->exec($cmd, $i);
					
					if(!$this->var_return && !is_array($return) ){	
						//C'est le retour pour afficher
						$ret .=$return;
							
					} elseif(is_array($return)){
						$ret = $return;
					} else{
						//C'est une affectation d'une variable
						$this->var_format[$this->var_return]=$this->exec($cmd, $i);
						$this->var_return='';
					}	
					if ($this->erreur == 1) {
						return -1;
					}
					break;
				default :
					if( ($cmd[$i]=='\\') && ( ($i+1) < strlen($cmd)) )$i++;
					$ret .= $cmd[$i];
					break;
			}
		}
		return $ret;
	}
	
	
}	
	
?>