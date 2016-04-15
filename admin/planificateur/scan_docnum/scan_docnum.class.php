<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_docnum.class.php,v 1.5.2.1 2015-09-01 10:40:09 dbellamy Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");

class scan_docnum extends tache {
	
	function scan_docnum($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
				
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $dbh,$charset;
		global $deflt_upload_repertoire;
		
		//On créer le sélecteur pour choisir le repertoire d'upload 
		$query="SELECT * FROM upload_repertoire";
		$result=pmb_mysql_query($query,$dbh);
		
		$select="";
		if(pmb_mysql_num_rows($result)){
			$select.="<select name='upload_repertoire'>";
			$allready_selected=false;
			while($upload_rep=pmb_mysql_fetch_object($result)){
				if($param['upload_repertoire']==$upload_rep->repertoire_id && !$allready_selected){
					$select.="	<option selected='true' value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
					$allready_selected=true;
				}elseif($deflt_upload_repertoire==$upload_rep->repertoire_id && !$allready_selected){
					$select.="	<option selected='true' value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
					$allready_selected=true;
				}else{
					$select.="	<option value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
				}
			}
			$select.="</select>";
		}else{
			$select.=$this->msg['planificateur_scan_docnum_no_upload_repertoire'];
		}
		
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='upload_folder'>".$this->msg["planificateur_scan_docnum_upload_repertoire"]."</label>
			</div>
			<div class='colonne_suite'>
				$select	
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='upload_folder'>".$this->msg["planificateur_scan_docnum_upload_folder"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' id='upload_folder' name='upload_folder' value='".htmlentities($param['upload_folder'],ENT_QUOTES,$charset)."'/>
			</div>
		</div>";
		
		return $form_task;
	}
	
	/**
	 * Liste le contenu du repertoire $upload_folder
	 * Ne tiens compte que des fichiers
	 * 
	 * @param string $upload_folder
	 * @return array
	 */
	function list_docnum($upload_folder){
		$list=array();
		$tmp_list=scandir($upload_folder);
		
		foreach ($tmp_list as $item){
			if(!is_dir($upload_folder.$item) && file_exists($upload_folder.$item)&& preg_match('/^(a|b|n)([0-9]+)(\.|\-).+$/', $item)){
				$list[]=$item;
			}
		}
		return $list;
	}
	
	function task_execution() {
		global $charset, $msg, $PMBusername;
		
		$reussi=0;
		$error_count=0;
		$errors=array();
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			
			if (method_exists($this->proxy, "pmbesScanDocnum_get_doc_num")) {
				
				if ($parameters["upload_folder"] && $parameters["upload_repertoire"]) {
					//on liste les documents dans le fichier upload_folder	
					$list_docnum=$this->list_docnum($parameters["upload_folder"]);
					if(sizeof($list_docnum)){
						//il y en a
						$percent = 0;
						$p_value = (int) 100/count($list_docnum);
						
						$this->report[] = "<tr><th colspan=3>".$this->msg["planificateur_scan_docnum_doc_a_traiter"]."</th><th>".count($list_docnum)."</th></tr>";
						
						
						foreach ($list_docnum as $docnum){
							$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
				
							if($this->statut == WAITING) {
								$this->send_command(RUNNING);
							}
							if($this->statut == RUNNING) {
								$explnum=array();
								$explnum['explnum_nomfichier']=$docnum;
								$explnum['explnum_repertoire']=$parameters["upload_repertoire"];
								
								$match=array();
								if(preg_match('/^b([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_bulletin']=$match[1];
								}elseif(preg_match('/^a([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_notice']=$match[1];
								}elseif(preg_match('/^n([0-9]+)(\.|\-).+$/', $docnum, $match)){
									$explnum['explnum_notice']=$match[1];
								}
								
								$report = $this->proxy->pmbesScanDocnum_get_doc_num($explnum, $parameters["upload_folder"]);
								foreach ($report as $msg_type=>$values){
									switch ($msg_type){
										case 'error':
											foreach($values as $error_msg){
												if($errors[$error_msg]){
													$errors[$error_msg]++;
												}else{
													$errors[$error_msg]=1;
												}
												$error_count++;
											}
											break;
										case 'info':
											$reussi=$reussi+$values;
											break;
									}	
								}
								$percent+=$p_value;
								$this->update_progression($percent);
							}
						}
					}else {
						$this->update_progression(100);
						$this->report[] = "<tr><td colspan=4>".$this->msg["planificateur_scan_docnum_no_docnum"]."</td></tr>";
					}
				} else {
					$this->report[] = "<tr><td colspan=4>".$this->msg["planificateur_scan_docnum_bad_params"]."</td></tr>";
				}
			} else {
				$this->report[] = "<tr><td colspan=4>".sprintf($msg["planificateur_function_rights"],"get_doc_num","pmbesScanDocnum",$PMBusername)."</td></tr>";
			}
		} else {
			$this->report[] = "<tr><td colspan=4>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</td></tr>";
		}
		
		if($reussi){
			$this->report[] = "<tr><td colspan=3>".$this->msg['planificateur_scan_docnum_doc_traites']."</td><td>$reussi</td></tr>";
		}
		if($errors){
			$this->report[] = "<tr><th colspan=3>".$this->msg['planificateur_scan_docnum_doc_non_traites']."</th><th>$error_count</th></tr>";
			foreach($errors as $error_msg=>$error_nb){
				$this->report[] = "<tr><td colspan=3>$error_msg</td><td>$error_nb</td></tr>";
			}
		}
// 		$this->show_report($this->report);
	}
	
	function traite_commande($cmd,$message) {		
		switch ($cmd) {
			case RESUME:
				$this->send_command(WAITING);
				break;
			case SUSPEND:
				$this->suspend_scan_docnum();
				break;
			case STOP:
				$this->finalize();
				die();
				break;
			case FAIL:
				$this->finalize();
				die();
				break;
		}
	}
    
	function make_serialized_task_params() {
    	global $upload_folder,$upload_repertoire;

		$t = parent::make_serialized_task_params();
		
		if ($upload_folder) {
			$t["upload_folder"]=stripslashes($upload_folder);
		}
		
		if ($upload_repertoire){
			$t["upload_repertoire"]=stripslashes($upload_repertoire);
		}
		
    	return serialize($t);
	}
	
	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }

	function suspend_scan_docnum() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
}