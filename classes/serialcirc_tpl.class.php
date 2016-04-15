<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl.class.php,v 1.5 2015-04-03 14:18:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$include_path/templates/serialcirc_tpl.tpl.php");
require_once("$class_path/serialcirc_tpl_print_fields.class.php");

class serialcirc_tpl {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	var $id = 0;		// MySQL id in table 'serialcirc_tpl'
	var $name = "";		// nom du template
	var $comment = "";	// description du template
	var $tpl = ""; 		// Template
	var $duplicate_from_id = 0; 	
	var $piedpage = ""; // pied de page
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	function serialcirc_tpl($id=0) {			
		$this->id = $id+0;
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos 
	// ---------------------------------------------------------------
	function getData() {
		global $dbh,$msg;
		$this->name	="";
		$this->comment="";
		$this->tpl ="";
		$this->piedpage="";
		if($this->id) {
			$requete = "SELECT * FROM serialcirc_tpl WHERE serialcirctpl_id='".$this->id."' LIMIT 1 ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->name	= $temp->serialcirctpl_name;
				$this->comment	= $temp->serialcirctpl_comment;
 				$this->tpl = $temp->serialcirctpl_tpl;
 				$this->piedpage = $temp->serialcirctpl_piedpage;
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;								
			}
		}
	}
	
	// ---------------------------------------------------------------
	//		show_list : affichage de la liste des éléments
	// ---------------------------------------------------------------	
	function show_list($link="./edit.php") {	
		global $dbh, $charset,$msg;
		global $serialcirc_tpl_liste, $serialcirc_tpl_liste_ligne;
		
		$tableau = "";
		$requete = "SELECT * FROM serialcirc_tpl ORDER BY serialcirctpl_name ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$pair="odd";
			while(($temp = pmb_mysql_fetch_object($result))){	
				$id = $temp->serialcirctpl_id;			
				$name = $temp->serialcirctpl_name;
				$comment = $temp->serialcirctpl_comment;						
					
				if($pair=="even") $pair ="odd";	else $pair ="even";
				// contruction de la ligne
				$ligne=$serialcirc_tpl_liste_ligne;
				
				$ligne = str_replace("!!name!!",	htmlentities($name,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!comment!!",	htmlentities($comment,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!pair!!",	$pair, $ligne);					
				$ligne = str_replace("!!link_edit!!",	$link."?categ=tpl&sub=serialcirc&action=edit&id=$id", $ligne);	
				$ligne = str_replace("!!id!!",		$id, $ligne);	
				$tableau.=$ligne;			
			}				
		}
		$liste = str_replace("!!serialcirc_tpl_liste!!",$tableau, $serialcirc_tpl_liste);	
		$liste = str_replace("!!link_ajouter!!",	$link."?categ=tpl&sub=serialcirc&action=edit", $liste);	
		return $liste;
	}	
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	function show_form($link="./edit.php", $act="") {
	
		global $msg;
		global $serialcirc_tpl_form;
		global $charset;
		global $name, $comment;
		
		$form=$serialcirc_tpl_form;		
		$action = $link."?categ=tpl&sub=serialcirc&action=update&id=!!id!!";
		
		if($this->id) {
			$libelle = $msg["serialcirc_tpl_modifier"];			
			$button_delete = "<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\">";
			$action_delete = $link."?categ=tpl&sub=serialcirc&action=delete&id=!!id!!";
			$button_duplicate = "<input type='button' class='bouton' value='".$msg["edit_tpl_duplicate_button"]."' onClick=\"document.location='./edit.php?categ=tpl&sub=serialcirc&action=duplicate&id=".$this->id."';\" />";
		} else {			
			$libelle = $msg["serialcirc_tpl_ajouter"];
			$button_delete = "";
			$button_duplicate = "";
			$action_delete= "";
		}
		
		if ($this->duplicate_from_id) $fields =new serialcirc_tpl_print_fields($this->duplicate_from_id);
		else $fields =new serialcirc_tpl_print_fields($this->id);
		switch ($act) {
			case "add_field" :
				$this->name = $name;
				$this->comment = $comment;
				$this->piedpage = $piedpage;
				$fields->add_field();
				break;
			case "del_field" :
				$this->name = $name;
				$this->comment = $comment;
				$this->piedpage = $piedpage;
				$fields->del_field();
				break;
			default :
				break;
		}
		$select_field=$fields->get_select_form("select_field",0,"serialcirc_tpl_print_add_button();");
		$format_serialcirc = $select_field;
		
		$fields_options="<select id='fields_options' name='fields_options'>";
		$fields_options.="<option value='{{last_empr.nom}}'>Dernier lecteur: Nom</option>";
		$fields_options.="<option value='{{last_empr.prenom}}'>Dernier lecteur: Prénom</option>";
		$fields_options.="<option value='{{last_empr.empr_libelle}}'>Dernier lecteur: Libellé</option>";
		$fields_options.="<option value='{{last_empr.mail}}'>Dernier lecteur: Mail</option>";
		$fields_options.="<option value='{{last_empr.cb}}'>Dernier lecteur: Code-barre</option>";
		$fields_options.="<option value='{{expl.cb}}'>Bulletin: Code-barre</option>";
		$fields_options.="<option value='{{expl.numero}}'>Bulletin: Numéro</option>";
		$fields_options.="<option value='{{expl.bulletine_date}}'>Bulletin: date</option>";
		$fields_options.="<option value='{{expl.serial_title}}'>Bulletin: Nom du périodique</option>";
		$fields_options.="<option value='{{expl.expl_location_name}}'>Bulletin: Localisation</option>";
		$fields_options.="<option value='{{expl.expl_cote}}'>Bulletin: Cote</option>";
		$fields_options.="</select>";
		$form=str_replace('!!fields_options!!', $fields_options, $form);
		$form=str_replace('!!pied_page!!', $this->piedpage, $form);
		
		$form = str_replace("!!libelle!!",	$libelle, $form);
		$form = str_replace("!!name!!",		htmlentities($this->name,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!comment!!",	htmlentities($this->comment,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!format_serialcirc!!", $format_serialcirc, $form);

		$form = str_replace("!!action!!",	$action, $form);
		$form = str_replace("!!duplicate!!", $button_duplicate, $form);		
		$form = str_replace("!!delete!!",	$button_delete,	$form);
		$form = str_replace("!!action_delete!!",$action_delete,	$form);
		$form = str_replace("!!id!!",		$this->id, $form);
		$form = str_replace("!!order_tpl!!",		implode(",",array_keys($fields->circ_tpl)), $form);
		return $form;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	function delete() {
		global $dbh;
		global $msg;
		
		if(!$this->id)	return $msg[403]; 

		$total = 0;
		$total = pmb_mysql_result(pmb_mysql_query("select count(1) from serialcirc where serialcirc_tpl ='".$this->id."' ", $dbh), 0, 0);
		if ($total==0) {
			// effacement dans la table
			$requete = "DELETE FROM serialcirc_tpl WHERE serialcirctpl_id='".$this->id."' ";
			pmb_mysql_query($requete, $dbh);
		} else {
			error_message(	$msg["edit_tpl_serialcirc_delete"], $msg["edit_tpl_serialcirc_delete_forbidden"], 1, 'edit.php?categ=tpl&sub=serialcirc&action=');
		}
		return false;
	}
	
	
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour 
	// ---------------------------------------------------------------
	function update($value) {
	
		global $dbh;
		global $msg;
		global $include_path;
			
		// nettoyage des chaînes en entrée		
		$value['name'] = addslashes(clean_string($value['name']));
		$value['comment'] = addslashes($value['comment']);
		$value['piedpage'] = addslashes($value['piedpage']);
		
		if(!$value['name'])	return false;
		
		$requete  = "SET  ";
		$requete .= "serialcirctpl_name='".$value["name"]."', ";	
		$requete .= "serialcirctpl_comment='".$value["comment"]."', ";
		$requete .= "serialcirctpl_piedpage='".$value["piedpage"]."' ";
		
		if($this->id) {
			// update
			$requete = "UPDATE serialcirc_tpl $requete WHERE serialcirctpl_id=".$this->id." ";
			if(!pmb_mysql_query($requete, $dbh)) {		
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["serialcirc_tpl_modifier"], $msg["serialcirc_tpl_modifier_erreur"]);
				return false;
			} else {
				// on enregistre les champs
				$fields =new serialcirc_tpl_print_fields($this->id);
				$fields->save_form();
			}
		} else {
			// creation
			$requete = "INSERT INTO serialcirc_tpl ".$requete;
			if(pmb_mysql_query($requete, $dbh)) {
				$this->id=pmb_mysql_insert_id();
				// on enregistre les champs
				$fields =new serialcirc_tpl_print_fields($this->id);
				$fields->save_form();
			} else {
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["serialcirc_tpl_ajouter"], $msg["serialcirc_tpl_ajouter_erreur"]);
				return false;
			}
		} 
			
		return true;
	}
		
	function update_from_form() {
		global $name, $comment, $piedpage;
		
		$value['name']=stripslashes($name);
		$value['comment']=stripslashes($comment);
		$value['piedpage']=stripslashes($piedpage);
		
		$this->update($value); 		
	}
	
	static function gen_tpl_select($select_name="form_serialcirc_tpl", $selected_id=0, $onchange="") {		
		global $msg;
		
		$requete = "SELECT serialcirctpl_id, concat(serialcirctpl_name,'. ',serialcirctpl_comment) as nom  FROM serialcirc_tpl ORDER BY serialcirctpl_name ";
		return gen_liste ($requete, "serialcirctpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg["serialcirc_tpl_list_default"], 0,$msg["serialcirc_tpl_list_default"], 0) ;
	}

} // fin class 
