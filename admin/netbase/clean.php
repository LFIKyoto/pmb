<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean.php,v 1.32 2015-06-02 13:24:51 dgoron Exp $

$base_path="../..";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "";    
require_once ("$base_path/includes/init.inc.php");  

// les requis par clean.php ou ses sous modules
include_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
include_once("$class_path/serie.class.php");
include_once("./params.inc.php");
echo "<div id='contenu-frame'>";

// definitions
define('INDEX_GLOBAL'					, 1);
define('INDEX_NOTICES'					, 2);
define('CLEAN_AUTHORS'					, 4);
define('CLEAN_PUBLISHERS'				, 8);
define('CLEAN_COLLECTIONS'				, 16);
define('CLEAN_SUBCOLLECTIONS'			, 32);
define('CLEAN_CATEGORIES'				, 64);
define('CLEAN_SERIES'					, 128);
define('CLEAN_RELATIONS'				, 256);
define('CLEAN_NOTICES'					, 512);
define('INDEX_ACQUISITIONS'				, 1024);
define('GEN_SIGNATURE_NOTICE'			, 2048);
define('NETTOYAGE_CLEAN_TAGS'			, 4096);
define('CLEAN_CATEGORIES_PATH'			, 8192);
define('GEN_DATE_PUBLICATION_ARTICLE'	, 16384);
define('GEN_DATE_TRI'					, 32768);
define('INDEX_DOCNUM'					, 65536);
define('CLEAN_OPAC_SEARCH_CACHE'		, 131072);
define('CLEAN_CACHE_AMENDE'				, 262144);
define('CLEAN_TITRES_UNIFORMES'			, 524288);
define('CLEAN_INDEXINT'			        , 1048576);
define('GEN_PHONETIQUE'			        , 2097152);
define('INDEX_RDFSTORE'					, 4194304);
define('INDEX_SYNCHRORDFSTORE'			, 8388608);
define('INDEX_FAQ'						, 16777216);
define('INDEX_CMS'						, 33554432);
define('INDEX_CONCEPT'					, 67108864);
define('HASH_EMPR_PASSWORD'				, 134217728);
if(!$spec) {
	$spec += $index_global;
	$spec += $index_notices;
	$spec += $clean_authors;
	$spec += $clean_editeurs;
	$spec += $clean_collections;
	$spec += $clean_subcollections;
	$spec += $clean_categories;
	$spec += $clean_series;
	$spec += $clean_relations;
	$spec += $clean_notices;
	$spec += $index_acquisitions;
	$spec += $gen_signature_notice;
	$spec += $nettoyage_clean_tags;
	$spec += $clean_categories_path;
	$spec += $gen_date_publication_article;	
	$spec += $gen_date_tri;
	$spec += $reindex_docnum;
	$spec += $clean_opac_search_cache;
	$spec += $clean_cache_amende;
	$spec += $clean_titres_uniformes;
	$spec += $clean_indexint;
	$spec += $gen_phonetique;
	$spec += $index_rdfstore;
	$spec += $index_synchrordfstore;
	$spec += $index_faq;
	$spec += $index_cms;
	$spec += $index_concept;
	$spec += $hash_empr_password;
}
if($spec) {
	if($spec & CLEAN_NOTICES) {
		include('./clean_expl.inc.php');
	} elseif($spec & CLEAN_SUBCOLLECTIONS) {
		include('./subcollections.inc.php');
	} elseif($spec & CLEAN_COLLECTIONS) {
		include('./collections.inc.php');
	} elseif($spec & CLEAN_PUBLISHERS) {
		include('./publishers.inc.php');
	} elseif($spec & CLEAN_AUTHORS) {
		if(!$pass2)
			include('./aut_pass1.inc.php'); // 1ère passe : auteurs non utilisés
		elseif ($pass2==1)
			include('./aut_pass2.inc.php'); // 2nde passe : renvois vers auteur inexistant
			elseif ($pass2==2) include('./aut_pass3.inc.php'); // 3eme passe : nettoyage des responsabilités sans notices
			else include('./aut_pass4.inc.php'); // 4eme passe : nettoyage des responsabilités sans auteurs
	} elseif($spec & CLEAN_CATEGORIES) {
		include('./category.inc.php');;
	} elseif($spec & CLEAN_SERIES) {
		include('./series.inc.php');
	} elseif ($spec & CLEAN_TITRES_UNIFORMES) {
		include('./titres_uniformes.inc.php');
	} elseif ($spec & CLEAN_INDEXINT) {
		include('./indexint.inc.php');
	} elseif ($spec & CLEAN_RELATIONS) {
		if(!$pass2) $pass2=1;
		include('./relations'.$pass2.'.inc.php');
	} elseif ($spec & INDEX_ACQUISITIONS) {
		include('./acquisitions.inc.php');
	} elseif ($spec & GEN_SIGNATURE_NOTICE) {
		include('./gen_signature_notice.inc.php');
	} elseif ($spec & GEN_PHONETIQUE) {
		include('./gen_phonetique.inc.php');
	} elseif ($spec & NETTOYAGE_CLEAN_TAGS) {
		include('./nettoyage_clean_tags.inc.php');	
	} elseif ($spec & CLEAN_CATEGORIES_PATH) {
		include('./clean_categories_path.inc.php');	
	} elseif ($spec & GEN_DATE_PUBLICATION_ARTICLE) {
		include('./gen_date_publication_article.inc.php');	
	} elseif ($spec & GEN_DATE_TRI) {
		include('./gen_date_tri.inc.php');
	} elseif($spec & INDEX_NOTICES) {
		include('./reindex.inc.php');
	} elseif($spec & INDEX_GLOBAL) {
		include('./reindex_global.inc.php');
	} elseif ($spec & INDEX_DOCNUM) {
		include('./reindex_docnum.inc.php');
	} elseif ($spec & CLEAN_OPAC_SEARCH_CACHE) {
		include('./clean_opac_search_cache.inc.php');
	} elseif ($spec & CLEAN_CACHE_AMENDE) {
		include('./clean_cache_amende.inc.php');
	}  elseif ($spec & INDEX_RDFSTORE) {
		include('./reindex_rdfstore.inc.php');
	}  elseif ($spec & INDEX_SYNCHRORDFSTORE) {
		include('./reindex_synchrordfstore.inc.php');
	}  elseif ($spec & INDEX_FAQ){
		include('./reindex_faq.inc.php');
	}  elseif ($spec & INDEX_CMS){
		include('./reindex_cms.inc.php');
	}  elseif ($spec & INDEX_CONCEPT){
		include('./reindex_concept.inc.php');
	} elseif ($spec & HASH_EMPR_PASSWORD){
		include('./hash_empr_password.inc.php');
	}
} else {
	if($v_state) {
		print "<h2>".htmlentities($msg["nettoyage_termine"], ENT_QUOTES, $charset)."</h2>";
		print urldecode($v_state);
	} else
		include_once('./form.inc.php');
}

// fermeture du lien MySQL

pmb_mysql_close($dbh);
echo "</div>";
print '</body></html>';
