<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_ui.class.php,v 1.5 2015-04-03 11:16:21 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/docwatch.tpl.php");
require_once($class_path."/cms/cms_editorial_types.class.php");
require_once($class_path."/cms/cms_editorial.class.php");
require_once($class_path."/marc_table.class.php");

/**
 * class docwatch_ui
 * 
 */

class docwatch_ui{

	/** Aggregations: */

	/** Compositions: */

	/** Fonctions: */
	
	public static function get_watch_form(){
		global $docwatch_watch_form_tpl, $msg;
		$marc_select = new marc_select("doctype", 'record_types');
		$cms_editorial_article = new cms_editorial_types('article');
		$cms_editorial_section = new cms_editorial_types('section');
		$cms_section = new cms_section();
		$cms_article = new cms_article();
		$cms_publication_state = new cms_editorial_publications_states();
		$status = $cms_publication_state->get_selector_options();
		
		$record_part = gen_plus("record_options",encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_options_record']), 
				'<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_record_default_type']).'</label>
				</div>
				<div class="row">'.str_replace('<select', '<select data-dojo-type="dijit/form/Select" style="width:auto"', $marc_select->display).'</div>
				<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_record_default_status']).'</label>
				</div>
				<div class="row">		
					<select  id="record_status" data-dojo-type="dijit/form/Select" style="width:auto" name="record_status">'.self::get_record_status().'</select>
				</div>');
		
		$article_part = gen_plus("article_options",encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_options_article']),
				'<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_article_default_content_type']).'</label>
				</div>
				<div class="row">
					<select  id="article_type" data-dojo-type="dijit/form/Select" style="width:auto" name="article_type">'.$cms_editorial_article->get_selector_options().'</select>
				</div>
				<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_article_default_publication_status']).'</label>
				</div>
				<div class="row">
					<select  id="article_status" data-dojo-type="dijit/form/Select" style="width:auto" name="article_status">'.$status.'</select>
				</div>
				<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_article_default_parent']).'</label>
				</div>
				<div class="row">
					<select  id="article_parent" data-dojo-type="dijit/form/Select" style="width:auto" name="article_parent">'.$cms_article->get_parent_selector().'</select>
				</div>');
		
		$section_part = gen_plus("section_options",encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_options_section']),
				'<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_section_default_content_type']).'</label>
				</div>
				<div class="row">
					<select  id="section_type" data-dojo-type="dijit/form/Select" style="width:auto" name="section_type">'.$cms_editorial_section->get_selector_options().'</select>
				</div>
				<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_section_default_publication_status']).'</label>
				</div>
				<div class="row">
					<select  id="section_status" data-dojo-type="dijit/form/Select" style="width:auto" name="section_status">'.$status.'</select>
				</div>
				<div class="row">
					<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_section_default_parent']).'</label>
				</div>
				<div class="row">
					<select  id="section_parent" data-dojo-type="dijit/form/Select" style="width:auto" name="section_parent">'.$cms_section->get_parent_selector().'</select>
				</div>');
		

		$form = $docwatch_watch_form_tpl;
		$form = str_replace('!!users_checkboxes!!', self::generate_users(), $form);
		$form = str_replace('!!options_record!!', $record_part, $form);
		$form = str_replace('!!options_article!!', $article_part,$form);
		$form = str_replace('!!options_section!!', $section_part, $form);

		return $form;
	}
	
	public static function get_category_form(){
		global $docwatch_category_form_tpl;
		$form = $docwatch_category_form_tpl;
		return $form;
	}
	
	public static function generate_users(){
		global $dbh,$charset;
		$counter = 1;
		$users_checkboxes = "
	<input type='hidden' name='owner' id='owner' value='".SESSuserid."'/>
	<table id='user_id_table'><tr>";
		$query = "select userid, username from users order by username";
		$result=pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($result)) {
			while($row=pmb_mysql_fetch_object($result)){
				$checked = '';
				if($row->userid == SESSuserid){
					$checked = 'checked=\'checked\' onclick=\'return false;\'';
				}
				$users_checkboxes.= "<td><input type='checkbox' ".$checked." id='user_id_".$row->userid."' class='checkbox' name='allowed_users[]' value='".$row->userid."'/>"."<label for='user_id_".$row->userid."'>".htmlentities($row->username,ENT_QUOTES,$charset)."</label></td>";
				if($counter%6 == 0){
					$users_checkboxes.= "</tr><tr>";
				}
				$counter++;
			}
		}
		$users_checkboxes.="</tr></table>";
		return $users_checkboxes;
	}
	
	public static function get_record_status(){
		global $dbh, $msg, $charset;
		// récupération des statuts de documents utilisés.
		$query = "SELECT count(statut), id_notice_statut, gestion_libelle ";
		$query .= "FROM notices, notice_statut where id_notice_statut=statut GROUP BY id_notice_statut order by gestion_libelle";
		$res = pmb_mysql_query($query, $dbh);
		$toprint_statutfield = "";
		while ($obj = @pmb_mysql_fetch_row($res)) {
			$toprint_statutfield .= "  <option value='$obj[1]'";
			if ($statut_query==$obj[1]) $toprint_statutfield.=" selected";
			$toprint_statutfield .=">".htmlentities($obj[2]."  (".$obj[0].")",ENT_QUOTES, $charset)."</OPTION>\n";
		}
		return $toprint_statutfield;
	}
} // end of docwatch_ui
