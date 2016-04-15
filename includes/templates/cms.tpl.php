<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.tpl.php,v 1.7 2014-01-07 10:16:16 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require("cms/cms.tpl.php");

$cms_menu = "
	<div id='menu'>
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_menu_build"],ENT_QUOTES,$charset)."</h3>
		<ul>
			<li><a href='./cms.php?categ=build&sub=block'>".htmlentities($msg["cms_menu_build_block"],ENT_QUOTES,$charset)."</a></li>
			<li><a href='./cms.php?categ=pages&sub=list'>".htmlentities($msg["cms_menu_pages"],ENT_QUOTES,$charset)."</a></li>
		</ul>
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_menu_editorial"],ENT_QUOTES,$charset)."</h3>
		<ul>
			<li><a href='./cms.php?categ=editorial&sub=list'>".htmlentities($msg["cms_menu_editorial_gest"],ENT_QUOTES,$charset)."</a></li>
			<li><a href='./cms.php?categ=section&sub=edit&id=new'>".htmlentities($msg["cms_new_section_form_title"],ENT_QUOTES,$charset)."</a></li>
			<li><a href='./cms.php?categ=article&sub=edit&id=new'>".htmlentities($msg["cms_new_article_form_title"],ENT_QUOTES,$charset)."</a></li>
			<li><a href='./cms.php?categ=collection&sub='>".htmlentities($msg["cms_collections_form_title"],ENT_QUOTES,$charset)."</a></li>
		</ul>
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_manage_module_menu"],ENT_QUOTES,$charset)."</h3>
		<ul>
			!!cms_managed_modules!!
		</ul>
	</div>
";

$cms_layout = "<div id='conteneur' class='$current_module'>
	$cms_menu
	<div id='contenu'>
	!!menu_contextuel!!
";

$cms_layout_end = "
		</div>
	</div>
";
