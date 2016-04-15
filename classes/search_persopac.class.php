<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_persopac.class.php,v 1.22 2015-04-03 11:16:20 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des recherches personnalisées

// inclusions principales
require_once("$include_path/templates/search_persopac.tpl.php");
require_once("$class_path/search.class.php");
require_once("$class_path/translation.class.php");
require_once("$class_path/XMLlist.class.php");
class search_persopac {
	var $name="";
	var $shortname="";
	var $query="";
	var $human="";
	var $directlink="";
	var $limitsearch="";
	var $empr_categ_restrict = array();	

	// constructeur
	function search_persopac($id=0) {
		global $search_persopac_link;
		// si id, allez chercher les infos dans la base
		if($id) {
			$this->id = $id;
			$this->fetch_data();
		}	
		if(!$search_persopac_link)$search_persopac_link=$this->get_link();
		return $this->id;
	}
    
	// récupération des infos en base
	function fetch_data() {
		global $dbh;
		
		$myQuery = pmb_mysql_query("SELECT * FROM search_persopac WHERE search_id='".$this->id."' LIMIT 1", $dbh);
		$myreq= pmb_mysql_fetch_object($myQuery);
		
		$this->name=$myreq->search_name;
		$this->shortname=$myreq->search_shortname;
		$this->query=$myreq->search_query;
		$this->human=$myreq->search_human;
		$this->directlink=$myreq->search_directlink;
		$this->limitsearch=$myreq->search_limitsearch;
		$this->empr_categ_restrict = array();
		
		$req  = "select id_categ_empr from search_persopac_empr_categ where id_search_persopac = ".$this->id;
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			while ($obj = pmb_mysql_fetch_object($res)){
				$this->empr_categ_restrict[]=$obj->id_categ_empr;
			}
		}
	}

	function get_link() {
		global $dbh;	
		$myQuery = pmb_mysql_query("SELECT * FROM search_persopac order by search_name ", $dbh);
		$this->search_persopac_list=array();
		$link="";
		if(pmb_mysql_num_rows($myQuery)){
			$i=0;
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->search_persopac_list[$i]= new stdClass();
				$this->search_persopac_list[$i]->id=$r->search_id;
				$this->search_persopac_list[$i]->name=$r->search_name;
				$this->search_persopac_list[$i]->shortname=$r->search_shortname;
				$this->search_persopac_list[$i]->query=$r->search_query;
				$this->search_persopac_list[$i]->human=$r->search_human;
				$this->search_persopac_list[$i]->directlink=$r->search_directlink;	
				$this->search_persopac_list[$i]->limitsearch=$r->search_limitsearch;							
				$i++;			
			}	
		}
		return true;
	}

	// fonction de mise à jour ou de création 
	function update($value) {	
		global $dbh,$msg,$search_persopac_link;
		$fields="";
		foreach($value as $key => $val) {
			if($key != "search_empr_restrict"){
				if($fields) $fields.=","; 
				$fields.=" $key='$val' ";	
			}
		}	
		if($this->id) {
			// modif
			$no_erreur=pmb_mysql_query("UPDATE search_persopac SET $fields WHERE search_id=".$this->id, $dbh);	
			if(!$no_erreur) {
				error_message($msg["search_persopac_form_edit"], $msg["search_persopac_form_add_error"],1);	
				exit;
			}
			
		} else {
			// create
			$no_erreur=pmb_mysql_query("INSERT INTO search_persopac SET $fields ", $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
			if(!$no_erreur) {
				error_message($msg["search_persopac_form_add"], $msg["search_persopac_form_add_error"],1);
				exit;
			}
		}	
		//on s'occupe maintenant de la restriction par caégories de lecteur
		$req = "delete from search_persopac_empr_categ where id_search_persopac = ".$this->id;
		pmb_mysql_query($req);
		if(count($value->search_empr_restrict)>0){
			foreach($value->search_empr_restrict as $id_categ_empr){
				$req= "insert into search_persopac_empr_categ set id_search_persopac=".$this->id.", id_categ_empr=".$id_categ_empr;
				pmb_mysql_query($req);
			}
		}
		
		// rafraischissement des données
		$this->fetch_data();
		$search_persopac_link=$this->get_link();
		return $this->id;
	}

	function update_from_form() {
		global $name,$shortname,$query,$human,$directlink,$limitsearch,$thesaurus_liste_trad,$empr_restrict;
		$value = new stdClass();
		$value->search_name=$name;
		$value->search_shortname=$shortname;
		$value->search_query=$query;
		$value->search_human=$human;
		$value->search_directlink=$directlink;
		$value->search_limitsearch=$limitsearch;
		$value->search_empr_restrict=$empr_restrict;
		
		$this->update($value); 	
		$trans= new translation($this->id,"search_persopac","search_name",$thesaurus_liste_trad);	
		$trans->update("name");
		$trans= new translation($this->id,"search_persopac","search_shortname",$thesaurus_liste_trad);	
		$trans->update("shortname");	
	}

	// fonction générant le form de saisie 
	function do_form() {
		global $msg,$tpl_search_persopac_form,$charset,$base_path;	
		global $thesaurus_liste_trad;
		global $id_search_persopac;
		// titre formulaire
		$my_search=new search(false,"search_fields_opac","$base_path/temp/");
		if($this->id) {
			$libelle=$msg["search_persopac_form_edit"];
			$link_delete="<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\" />";
			$button_modif_requete = "<input type='button' class='bouton' value=\"".$msg["search_perso_modif_requete"]."\" onClick=\"document.modif_requete_form_".$this->id.".submit();\">";
			
			//Mémorisation de recherche prédéfinie en édition
	 		if ($id_search_persopac) {
	 			$this->query=$my_search->serialize_search();
	 			$this->human = $my_search->make_human_query();
	 			$my_search->unserialize_search($this->query);
	 		} else {
				$my_search->unserialize_search($this->query);
				$this->query=$my_search->serialize_search();
				$this->human = $my_search->make_human_query();
	 		}
	 		$form_modif_requete = $this->make_hidden_search_form();
		} else {
			$libelle=$msg["search_persopac_form_add"];
			$link_delete="";
			$button_modif_requete = "";
			$form_modif_requete = "";
	
	 		$this->query=$my_search->serialize_search();
	 		$this->human = $my_search->make_human_query();
		}
		// Champ éditable
		$tpl_search_persopac_form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		
		$trans= new translation($this->id,"search_persopac","search_name",$thesaurus_liste_trad);
		$field_name=$trans->get_form($msg["search_persopac_form_name"],"form_nom","name",$this->name,"saisie-80em");	
		$tpl_search_persopac_form = str_replace('!!name!!', $field_name, $tpl_search_persopac_form);
	
		$trans= new translation($this->id,"search_persopac","search_shortname",$thesaurus_liste_trad);
		$field_name=$trans->get_form($msg["search_persopac_form_shortname"],"shortname","shortname",$this->shortname,"saisie-80em");		
		$tpl_search_persopac_form = str_replace('!!shortname!!', $field_name, $tpl_search_persopac_form);
		$checked='';
		if($this->directlink) $checked= " checked='checked' ";
		$tpl_search_persopac_form = str_replace('!!directlink!!', $checked, $tpl_search_persopac_form);
		$checked='';
		if($this->limitsearch) $checked= " checked='checked' ";
		$tpl_search_persopac_form = str_replace('!!limitsearch!!', $checked, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!query!!', htmlentities($this->query,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!human!!', htmlentities($this->human,ENT_QUOTES,$charset), $tpl_search_persopac_form);
		
		$action="./admin.php?categ=opac&sub=search_persopac&section=liste&action=collstate_update&serial_id=".$this->serial_id."&id=".$this->id;
		$tpl_search_persopac_form = str_replace('!!action!!', $action, $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!delete!!', $link_delete, $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!libelle!!',htmlentities($libelle,ENT_QUOTES,$charset) , $tpl_search_persopac_form);
		
		$link_annul = "onClick=\"unload_off();history.go(-1);\"";
		$tpl_search_persopac_form = str_replace('!!annul!!', $link_annul, $tpl_search_persopac_form);
		
		//restriction aux catégories de lecteur
		$requete = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)>0){
			$categ = "
			<label for='empr_restrict'>".$msg['search_perso_form_user_restrict']."</label><br />
			<select id='empr_restrict' name='empr_restrict[]' multiple>";
			while($obj = pmb_mysql_fetch_object($res)){
				$categ.="
				<option value='".$obj->id_categ_empr."' ".(in_array($obj->id_categ_empr,$this->empr_categ_restrict) ? "selected=selected" : "") .">".$obj->libelle."</option>";
			}
			$categ.="
			</select>";
		}else $categ = "";
		$tpl_search_persopac_form = str_replace('!!categorie!!', $categ, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!requete!!', htmlentities($this->query,ENT_QUOTES, $charset), $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!requete_human!!', $this->human, $tpl_search_persopac_form);
		
		$tpl_search_persopac_form = str_replace('!!bouton_modif_requete!!', $button_modif_requete,  $tpl_search_persopac_form);
		$tpl_search_persopac_form = str_replace('!!form_modif_requete!!', $form_modif_requete,  $tpl_search_persopac_form);
		
		return $tpl_search_persopac_form;	
	}


	function do_list() {
		global $tpl_search_persopac_liste_tableau,$tpl_search_persopac_liste_tableau_ligne;	
			
		// liste des lien de recherche directe
		$liste="";
		// pour toute les recherche de l'utilisateur
		for($i=0;$i<count($this->search_persopac_list);$i++) {
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
	/*		
			//composer le formulaire de la recherche
			$my_search=new search();
			$my_search->unserialize_search($this->search_persopac_list[$i]->query);
			$forms_search.= $my_search->make_hidden_search_form("./catalog.php?categ=search&mode=6","search_form".$this->search_persopac_list[$i]->id);
	*/		
			
	        $td_javascript=" ";
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
	
	        $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_search_persopac_liste_tableau_ligne);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	
			$line =str_replace('!!id!!', $this->search_persopac_list[$i]->id, $line);
			$line = str_replace('!!name!!', $this->search_persopac_list[$i]->name, $line);
			$line = str_replace('!!human!!', $this->search_persopac_list[$i]->human, $line);		
			$line = str_replace('!!shortname!!', $this->search_persopac_list[$i]->shortname, $line);
			if($this->search_persopac_list[$i]->directlink)
				$directlink="<img src='./images/tick.gif' border='0'  hspace='0' align='middle'  class='bouton-nav' value='=' />";
			else $directlink="";
			$line = str_replace('!!directlink!!', $directlink, $line);
			
			$liste.=$line;
		}
		$tpl_search_persopac_liste_tableau = str_replace('!!lignes_tableau!!',$liste , $tpl_search_persopac_liste_tableau);
		return $forms_search.$tpl_search_persopac_liste_tableau;	
	}

	function delete() {
		global $dbh,$search_persopac_link;
		
		if($this->id) {
			pmb_mysql_query("DELETE from search_persopac WHERE search_id='".$this->id."' ", $dbh);
			pmb_mysql_query("delete from search_persopac_empr_categ where id_search_persopac = ".$this->id);
		}
		$search_persopac_link=$this->get_link();	
	}

	function add_search(){
		global $include_path,$pmb_opac_url;
		global $lang,$msg,$base_path;
	
		$save_msg=$msg;
		// Recherche du fichier lang de l'opac
		$url=$pmb_opac_url."includes/messages/$lang.xml";
		$fichier_xml=$base_path."/temp/opac_lang.xml";
		
		$this->curl_load_file($url,$fichier_xml);	
		$messages = new XMLlist("$base_path/temp/opac_lang.xml", 0);
		$messages->analyser();
		$msg = $messages->table;
		
		$url=$pmb_opac_url."includes/search_queries/search_fields.xml";
		$fichier_xml="$base_path/temp/search_fields_opac.xml";
		
		$this->curl_load_file($url,$fichier_xml);
		$my_search=new search(false,"search_fields_opac","$base_path/temp/");
		$form= $my_search->show_form("./admin.php?categ=opac&sub=search_persopac&section=liste&action=build",
			"","","./admin.php?categ=opac&sub=search_persopac&section=liste&action=form".($this->id ? "&id=".$this->id : ""));
		print $form;	
		$msg=$save_msg;
	}

	function continu_search(){
		
		$my_search=new search(false,"search_fields_opac");
		$form= $my_search->show_form("./admin.php?categ=opac&sub=search_persopac&section=liste&action=build",
			"","","./admin.php?categ=opac&sub=search_persopac&section=liste&action=form");
		print $form;
	}

	function curl_load_file($url, $filename) {
		global $opac_curl_available, $msg ;
		if (!$opac_curl_available) die("PHP Curl must be available");
		//Calcul du subst
		$url_subst=str_replace(".xml","_subst.xml",$url);
	    $curl = curl_init();
	    curl_setopt ($curl, CURLOPT_URL, $url_subst);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    $filename_subst=str_replace(".xml","_subst.xml",$filename);
	 	$fp = fopen($filename_subst, "w+");    
		curl_setopt($curl, CURLOPT_FILE, $fp);
		
		if(curl_exec ($curl)) {
			fclose($fp);
			if (curl_getinfo($curl,CURLINFO_HTTP_CODE)=="404") {
				unset($fp);
				@unlink($filename_subst);
			}
			curl_setopt ($curl, CURLOPT_URL, $url);
			$fp = fopen($filename, "w+"); 
			curl_setopt($curl, CURLOPT_FILE, $fp);
		   	if(!curl_exec ($curl)) die($msg["search_perso_error_param_opac_url"]);
		} else die($msg["search_perso_error_param_opac_url"]);
	    curl_close ($curl);
	    fclose($fp);
	}

	// pour maj de requete de recherche prédéfinie
	function make_hidden_search_form($url="") {
		global $search;
		global $charset;
	 	
		$url = "./admin.php?categ=opac&sub=search_persopac&section=liste&action=add" ;
	
		$r="<form name='modif_requete_form_$this->id' action='$url' style='display:none' method='post'>";
	
		for ($i=0; $i<count($search); $i++) {
			$inter="inter_".$i."_".$search[$i];
			global $$inter;
			$op="op_".$i."_".$search[$i];
			global $$op;
			$field_="field_".$i."_".$search[$i];
			global $$field_;
			$field=$$field_;
			//Récupération des variables auxiliaires
			$fieldvar_="fieldvar_".$i."_".$search[$i];
			global $$fieldvar_;
			$fieldvar=$$fieldvar_;
			if (!is_array($fieldvar)) $fieldvar=array();
	
			$r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$inter."' value='".htmlentities($$inter,ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$op."' value='".htmlentities($$op,ENT_QUOTES,$charset)."'/>";
			for ($j=0; $j<count($field); $j++) {
				$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
			}
			reset($fieldvar);
			while (list($var_name,$var_value)=each($fieldvar)) {
				for ($j=0; $j<count($var_value); $j++) {
					$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
				}
			}
		}
	 	$r.="<input type='hidden' name='id_search_persopac' value='$this->id'/>";
		$r.="</form>";
		return $r;
	}

} // fin définition classe
