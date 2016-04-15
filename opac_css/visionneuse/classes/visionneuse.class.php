<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.class.php,v 1.15 2015-04-03 11:16:28 jpermanne Exp $

require_once($visionneuse_path."/api/params.interface.php");
require_once($visionneuse_path."/classes/docNum.class.php");
require_once($visionneuse_path."/includes/templates/visionneuse.tpl.php");
require_once($visionneuse_path."/classes/messages.class.php");

class visionneuse {
	var $visionneuse_path = "";
	var $classParam;				//classe de paramétrage de la visionneuse
	var $docToRender;				
	var $mimetypeClass;
	var $message;					//messages localisés

    function visionneuse($driver,$visionneuse_path,$lvl="visionneuse",$lang="fr_FR",$tab_params=array()){

    	$this->visionneuse_path = $visionneuse_path;
	  	//on instancie la bonne classe
    	$this->classParam = new $driver($tab_params,$this->visionneuse_path);
    	//on instancie également les messages localisés...
    	$this->message = new message($this->visionneuse_path."/includes/message/$lang.xml");
    	switch ($lvl){
    		case "visionneuse" :
    			$this->display();
    			$this->classParam->cleanCache();
    			break;
    		case "afficheur" :
     			$this->classParam->getDocById($tab_params["explnum"]);
				$this->renderDoc();
				break;
    		case "ajax" :
    			$this->exec($tab_params['method']);
    			break;
    	}
    }
    
    function display(){
		global $visionneuse;
		global $charset,$opac_url_base;

		if($this->classParam->getNbDocs()>0){
			//on commence par remettre les champs cachés du formulaire...
			$hiddenFields = "";
			foreach($this->classParam->params as $key => $value){
				//sauf les paramètres qui n'ont pas été postés, mais créés à la main ou modifiés plus tard en javascript... 
				if ($key != "position" && $key != "start"){
				$hiddenFields .="
				<input type='hidden' name='$key' id='$key' value='".htmlentities(stripslashes($value),ENT_QUOTES,$charset)."' />";
				}
			}
			$visionneuse = str_replace("!!hiddenFields!!",$hiddenFields,$visionneuse);
	
			//et c'est parti
			//on s'occupe en premier du conteneur du document
			$visionneuse = str_replace("!!height!!",$this->classParam->getParam("maxY"),$visionneuse);
			//on insère le contenu propre au document;
			$docNum = new docNum($this->classParam->getCurrentDoc(),$this->classParam);
			$this->do_stat_opac($docNum->id);
 			if($this->classParam->is_downloadable($docNum->id)) {
 				$link= "<div id='visio_current_download'><a href='!!expnum_download!!' target='_blank'>!!expnum_download_lib!!</a></div>";
 				$url_download_explnum =$this->classParam->getDocumentUrl($docNum->id);
 				$link = str_replace("!!expnum_download!!",$url_download_explnum,$link);
 				$link = str_replace("!!expnum_download_lib!!",htmlentities($this->message->table['download_doc'],ENT_QUOTES,$charset),$link);
 			}else{
 				$link ="";
 			}
 			$visionneuse = str_replace("!!download!!",$link,$visionneuse);
			$docToDisplay = $docNum->fetchDisplay();
			foreach($docToDisplay as $key => $value){
				//le cas ou le document n'est pas autorisé!
				if($key == "doc" && $value == false){
					$visionneuse = str_replace("!!$key!!","<br/><br/><br/><h4>".htmlentities($this->message->table['forbidden_resources'],ENT_QUOTES,$charset)."</h4><br/><br/>".$this->classParam->forbidden_callback(),$visionneuse);
				}else if($key != "post"){
					$visionneuse = str_replace("!!$key!!",$value,$visionneuse);
				}
			}
			//maintenant le kit de survie du navigateur
			$visionneuse = str_replace("!!position!!",$this->classParam->current,$visionneuse);
			if($this->classParam->getNbDocs()==1){
				$visionneuse = str_replace("!!previous_style!!","none;",$visionneuse);
				$visionneuse = str_replace("!!next_style!!","none;",$visionneuse);			
	    	}elseif($this->classParam->current ==0){
				$visionneuse = str_replace("!!previous_style!!","none;",$visionneuse);
				$visionneuse = str_replace("!!next_style!!","block-inline;",$visionneuse);
			}elseif($this->classParam->current == sizeof($this->classParam->listeDocs)-1){
				$visionneuse = str_replace("!!previous_style!!","block-inline;",$visionneuse);
				$visionneuse = str_replace("!!next_style!!","none;",$visionneuse);		
			}else{
				$visionneuse = str_replace("!!previous_style!!","block-inline;",$visionneuse);
				$visionneuse = str_replace("!!next_style!!","block-inline;",$visionneuse);					
			}
			$visionneuse = str_replace("!!max_pos!!",$this->classParam->getNbDocs()-1,$visionneuse);		
			$visionneuse = str_replace("!!current_position!!",($this->classParam->current+1)." / ".$this->classParam->getNbDocs(),$visionneuse);
		
			//on localise les messages
			$visionneuse = str_replace("!!close!!",$this->message->table['close'],$visionneuse);
			$visionneuse = str_replace("!!fullscreen!!",$this->message->table['fullscreen'],$visionneuse);
			$visionneuse = str_replace("!!normal!!",$this->message->table['normal'],$visionneuse);

			//tout est bon, on affiche le tout...
			print $visionneuse;
    	}else{
    		print htmlentities($this->message->table['nothing_to_display'],ENT_QUOTES,$charset);
    	}
    }		
    
    function renderDoc(){
     	$docNum = new docNum($this->classParam->getCurrentDoc(),$this->classParam);
    	$docNum->render();
    }

    function exec($method){
     	$docNum = new docNum($this->classParam->getCurrentDoc(),$this->classParam);
    	$docNum->exec($method);
    }
    
    function do_stat_opac($id_explnum){
    	global $pmb_logs_activate;
    	if($pmb_logs_activate){
    		global $infos_explnum,$log;
			$rqt_explnum = "SELECT explnum_id, explnum_notice, explnum_bulletin, IF(location_libelle IS null, '', location_libelle) AS location_libelle, explnum_nom, explnum_mimetype, explnum_url, explnum_extfichier, IF(explnum_nomfichier IS null, '', explnum_nomfichier) AS nomfichier, explnum_path, IF(rep.repertoire_nom IS null, '', rep.repertoire_nom) AS nomrepertoire
				from explnum ex_n
				LEFT JOIN explnum_location ex_l ON ex_n.explnum_id= ex_l.num_explnum
				LEFT JOIN docs_location dl ON ex_l.num_location= dl.idlocation
				LEFT JOIN upload_repertoire rep ON ex_n.explnum_repertoire= rep.repertoire_id
				where explnum_id='".$id_explnum."'";
			$res_explnum=pmb_mysql_query($rqt_explnum);
			while(($explnum = pmb_mysql_fetch_array($res_explnum,MYSQL_ASSOC))){
				$infos_explnum[]=$explnum;
			}
			
			$rqt= " select empr_prof,empr_cp, empr_ville as ville, empr_year, empr_sexe, empr_login,  empr_date_adhesion, empr_date_expiration, count(pret_idexpl) as nbprets, count(resa.id_resa) as nbresa, code.libelle as codestat, es.statut_libelle as statut, categ.libelle as categ, gr.libelle_groupe as groupe,dl.location_libelle as location 
					from empr e
					left join empr_codestat code on code.idcode=e.empr_codestat
					left join empr_statut es on e.empr_statut=es.idstatut
					left join empr_categ categ on categ.id_categ_empr=e.empr_categ
					left join empr_groupe eg on eg.empr_id=e.id_empr
					left join groupe gr on eg.groupe_id=gr.id_groupe
					left join docs_location dl on e.empr_location=dl.idlocation
					left join resa on e.id_empr=resa_idempr
					left join pret on e.id_empr=pret_idempr
					where e.empr_login='".addslashes($_SESSION['user_code'])."'
					group by resa_idempr, pret_idempr";	
			$res=pmb_mysql_query($rqt);
			if($res){
				$empr_carac = pmb_mysql_fetch_array($res);
				$log->add_log('empr',$empr_carac);
			}
		
			$log->add_log('num_session',session_id());
			$log->add_log('explnum',$infos_explnum);
			
			//Accessibilité des documents numériques aux abonnés en opac
			$id_notice_droit=0;
			if($infos_explnum[0]["explnum_notice"]){
				$id_notice_droit=$infos_explnum[0]["explnum_notice"];
			}else{
				$requete="SELECT bulletin_notice, num_notice FROM bulletins WHERE bulletin_id='".$infos_explnum[0]["explnum_bulletin"]."'";
				$res=pmb_mysql_query($requete);
				if($res && pmb_mysql_num_rows($res)){
					if($id_noti_bull=pmb_mysql_result($res,0,1)){
						$id_notice_droit=$id_noti_bull;
					}else{
						$id_notice_droit=pmb_mysql_result($res,0,0);
					}
				}
			}
			if($id_notice_droit){
				$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='".$id_notice_droit."' AND statut=id_notice_statut ";
				$result=pmb_mysql_query($req_restriction_abo);
				$expl_num=pmb_mysql_fetch_array($result,MYSQL_ASSOC);
				$infos_restriction_abo = array();
				foreach ($expl_num as $key=>$value) {
					$infos_restriction_abo[$key] = $value;
				}
				$log->add_log('restriction_abo',$infos_restriction_abo);
			}

			$log->save();
    	}
    }
}
?>