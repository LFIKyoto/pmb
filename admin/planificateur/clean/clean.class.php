<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean.class.php,v 1.9 2015-07-17 15:02:23 dgoron Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/tache.class.php");

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
define('CLEAN_INDEXINT'					, 1048576);
define('GEN_PHONETIQUE'					, 2097152);
define('INDEX_RDFSTORE'					, 4194304);
define('INDEX_SYNCHRORDFSTORE'			, 8388608);
define('INDEX_FAQ'						, 16777216);
define('INDEX_CMS'						, 33554432);
define('INDEX_CONCEPT'					, 67108864);
define('HASH_EMPR_PASSWORD'				, 134217728);
		
class clean extends tache {
	
	function clean($id_tache=0){
		global $base_path;
		
		parent::get_messages($base_path."/admin/planificateur/".get_class());
		$this->id_tache = $id_tache;
			
	}
	
	//formulaire spécifique au type de tâche
	function show_form ($param='') {
		global $msg, $charset, $acquisition_active, $pmb_indexation_docnum;
		global $pmb_gestion_financiere, $pmb_gestion_amende;
		global $pmb_synchro_rdf;
		global $faq_active, $cms_active;
		global $thesaurus_concepts_active;
		
		if ($param["clean"]) {
			foreach ($param["clean"] as $name=>$value) {
				$$name = $value;
			}
		}
			
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_clean"]."</label>
			</div>
			<div class='colonne_suite'>
				<div class='row'>
					<input type='checkbox' value='1' id='index_global' name='index_global' ".($index_global == "1" ? "checked" :"").">&nbsp;<label for='index_global' >".htmlentities($msg["nettoyage_index_global"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='2' id='index_notices' name='index_notices' ".($index_notices == "2" ? "checked" :"").">&nbsp;<label for='index_notices'>".htmlentities($msg["nettoyage_index_notices"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='4' id='clean_authors' name='clean_authors' ".($clean_authors == "4" ? "checked" :"").">&nbsp;<label for='clean_authors'>".htmlentities($msg["nettoyage_clean_authors"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='8' id='clean_editeurs' name='clean_editeurs' ".($clean_editeurs == "8" ? "checked" :"").">&nbsp;<label for='clean_editeurs'>".htmlentities($msg["nettoyage_clean_editeurs"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='16' id='clean_collections' name='clean_collections' ".($clean_collections == "16" ? "checked" :"").">&nbsp;<label for='clean_collections'>".htmlentities($msg["nettoyage_clean_collections"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='32' id='clean_subcollections' name='clean_subcollections' ".($clean_subcollections == "32" ? "checked" :"").">&nbsp;<label for='clean_subcollections'>".htmlentities($msg["nettoyage_clean_subcollections"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='64' id='clean_categories' name='clean_categories' ".($clean_categories == "64" ? "checked" :"").">&nbsp;<label for='clean_categories'>".htmlentities($msg["nettoyage_clean_categories"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='128' id='clean_series' name='clean_series' ".($clean_series == "128" ? "checked" :"").">&nbsp;<label for='clean_series'>".htmlentities($msg["nettoyage_clean_series"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='524288' id='clean_titres_uniformes' name='clean_titres_uniformes' ".($clean_titres_uniformes == "524288" ? "checked" :"").">&nbsp;<label for='clean_titres_uniformes'>".htmlentities($msg["nettoyage_clean_titres_uniformes"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='1048576' id='clean_indexint' name='clean_indexint' ".($clean_indexint == "1048576" ? "checked" :"").">&nbsp;<label for='clean_indexint'>".htmlentities($msg["nettoyage_clean_indexint"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='hidden' value='256' name='clean_relations' />
					<input type='checkbox' value='256' name='clean_relationschk' checked disabled='disabled'/>&nbsp;<label for='clean_relations'>".htmlentities($msg["nettoyage_clean_relations"], ENT_QUOTES, $charset)."</label>
					</div>
				<div class='row'>
					<input type='checkbox' value='512' id='clean_notices' name='clean_notices' ".($clean_notices == "512" ? "checked" :"").">&nbsp;<label for='clean_notices'>".htmlentities($msg["nettoyage_clean_expl"], ENT_QUOTES, $charset)."</label>
					</div>";		
			if ($acquisition_active) {
				$form_task .= "		
				<div class='row'>
					<input type='checkbox' value='1024' id='index_acquisitions' name='index_acquisitions' ".($index_acquisitions == "1024" ? "checked" :"").">&nbsp;<label for='index_acquisitions'>".htmlentities($msg["nettoyage_reindex_acq"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
				$form_task .= "	
					<div class='row'>
						<input type='checkbox' value='2048' id='gen_signature_notice' name='gen_signature_notice' ".($gen_signature_notice == "2048" ? "checked" :"").">&nbsp;<label for='gen_signature_notice'>".htmlentities($msg["gen_signature_notice"], ENT_QUOTES, $charset)."</label>
						</div>
					<div class='row'>
						<input type='checkbox' value='2097152' id='gen_phonetique' name='gen_phonetique' ".($gen_phonetique == "2097152" ? "checked" :"").">&nbsp;<label for='gen_phonetique'>".htmlentities($msg["gen_phonetique"], ENT_QUOTES, $charset)."</label>
					</div>
					<div class='row'>
						<input type='checkbox' value='4096' id='nettoyage_clean_tags' name='nettoyage_clean_tags' ".($nettoyage_clean_tags == "4096" ? "checked" :"").">&nbsp;<label for='nettoyage_clean_tags'>".htmlentities($msg["nettoyage_clean_tags"], ENT_QUOTES, $charset)."</label>
						</div>
					<div class='row'>
						<input type='checkbox' value='8192' id='clean_categories_path' name='clean_categories_path' ".($clean_categories_path == "8192" ? "checked" :"").">&nbsp;<label for='clean_categories_path'>".htmlentities($msg["clean_categories_path"], ENT_QUOTES, $charset)."</label>
						</div>
					<div class='row'>
						<input type='checkbox' value='16384' id='gen_date_publication_article' name='gen_date_publication_article' ".($gen_date_publication_article == "16384" ? "checked" :"").">&nbsp;<label for='gen_date_publication_article'>".htmlentities($msg["gen_date_publication_article"], ENT_QUOTES, $charset)."</label>
						</div>
					<div class='row'>
						<input type='checkbox' value='32768' id='gen_date_tri' name='gen_date_tri' ".($gen_date_tri == "32768" ? "checked" :"").">&nbsp;<label for='gen_date_tri'>".htmlentities($msg["gen_date_tri"], ENT_QUOTES, $charset)."</label>
						</div>";
			if($pmb_indexation_docnum){
				$form_task .= "	
				<div class='row'>
					<input type='checkbox' value='65536' id='reindex_docnum' name='reindex_docnum' ".($reindex_docnum == "65536" ? "checked" :"").">&nbsp;<label for='reindex_docnum'>".htmlentities($msg["docnum_reindexer"], ENT_QUOTES, $charset)."</label>
				</div>";
			}

			$form_task .= "<div class='row'>
						<input type='checkbox' value='131072' id='clean_opac_search_cache' name='clean_opac_search_cache' ".($clean_opac_search_cache == "131072" ? "checked" :"").">&nbsp;<label for='clean_opac_search_cache'>".htmlentities($msg["clean_opac_search_cache"], ENT_QUOTES, $charset)."</label>
					</div>";
			if($pmb_gestion_financiere && $pmb_gestion_amende){
				$form_task .= "
					<div class='row'>
						<input type='checkbox' value='262144' id='clean_cache_amende' name='clean_cache_amende' ".($clean_cache_amende == "262144" ? "checked" :"").">&nbsp;<label for='clean_cache_amende'>".htmlentities($msg["clean_cache_amende"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
			$form_task .= "
					<div class='row'>
						<input type='checkbox' value='4194304' id='index_rdfstore' name='index_rdfstore' ".($index_rdfstore == "4194304" ? "checked" :"").">&nbsp;<label for='index_rdfstore'>".htmlentities($msg["nettoyage_rdfstore_reindex"], ENT_QUOTES, $charset)."</label>
					</div>";
			if($pmb_synchro_rdf){
				$form_task .= "
					<div class='row'>
						<input type='checkbox' value='8388608' id='index_synchrordfstore' name='index_synchrordfstore' ".($index_synchrordfstore == "8388608" ? "checked" :"").">&nbsp;<label for='index_synchrordfstore'>".htmlentities($msg["nettoyage_synchrordfstore_reindex"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
			if($faq_active){
				$form_task .= "
					<div class='row'>
						<input type='checkbox' value='16777216' id='index_faq' name='index_faq' ".($index_faq == "16777216" ? "checked" :"").">&nbsp;<label for='index_faq'>".htmlentities($msg["nettoyage_faq_reindex"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
			if($cms_active){
				$form_task .= "
					<div class='row'>
						<input type='checkbox' value='33554432' id='index_cms' name='index_cms' ".($index_cms == "33554432" ? "checked" :"").">&nbsp;<label for='index_cms'>".htmlentities($msg["nettoyage_cms_reindex"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
			if($thesaurus_concepts_active==1){
				$form_task .= "
					<div class='row'>
						<input type='checkbox' value='67108864' id='index_concept' name='index_concept' ".($index_concept == "67108864" ? "checked" :"").">&nbsp;<label for='index_concept'>".htmlentities($msg["nettoyage_concept_reindex"], ENT_QUOTES, $charset)."</label>
					</div>";
			}
			$form_task .= "
					<div class='row'>
						<input type='checkbox' value='134217728' id='hash_empr_password' name='hash_empr_password' ".($hash_empr_password == "134217728" ? "checked" :"").">&nbsp;<label for='hash_empr_password'>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</label>
					</div>
				</div>
			</div>";	
								
		return $form_task;
	}

	function task_execution() {
		global $dbh, $msg, $charset, $PMBusername;
		global $acquisition_active,$pmb_indexation_docnum;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			//progression
			$p_value = (int) 100/count($parameters["clean"]);
			$this->report[] = "<tr><th>".$this->msg["planificateur_clean"]."</th></tr>";
			$result="";
			foreach ($parameters["clean"] as $clean) {
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if ($this->statut == RUNNING) {
					switch ($clean) {
						case INDEX_GLOBAL:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_index_global"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_indexGlobal')) {
								$result .= $this->proxy->pmbesClean_indexGlobal();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"indexGlobal","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_NOTICES:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_index_notices"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_indexNotices')) {
								$result .= $this->proxy->pmbesClean_indexNotices();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"indexNotices","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_AUTHORS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_authors"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanAuthors')) {
								$result .= $this->proxy->pmbesClean_cleanAuthors();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanAuthors","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_PUBLISHERS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_editeurs"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanPublishers')) {
								$result .= $this->proxy->pmbesClean_cleanPublishers();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanPublishers","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_COLLECTIONS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_collections"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanCollections')) {
								$result .= $this->proxy->pmbesClean_cleanCollections();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanCollections","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_SUBCOLLECTIONS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_subcollections"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanSubcollections')) {
								$result .= $this->proxy->pmbesClean_cleanSubcollections();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanSubcollections","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_CATEGORIES:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_categories"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanCategories')) {
								$result .= $this->proxy->pmbesClean_cleanCategories();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanCategories","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_SERIES:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_series"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanSeries')) {
								$result .= $this->proxy->pmbesClean_cleanSeries();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanSeries","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_TITRES_UNIFORMES:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_titres_uniformes"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanTitresUniformes')) {
								$result .= $this->proxy->pmbesClean_cleanTitresUniformes();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanTitresUniformes","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_INDEXINT:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_indexint"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanIndexint')) {
								$result .= $this->proxy->pmbesClean_cleanIndexint();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanIndexint","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_RELATIONS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_relations"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanRelations')) {
								$result .= $this->proxy->pmbesClean_cleanRelations();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanRelations","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_NOTICES:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_expl"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanNotices')) {
								$result .= $this->proxy->pmbesClean_cleanNotices();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanNotices","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_ACQUISITIONS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_reindex_acq"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if ($acquisition_active) {
								if (method_exists($this->proxy, 'pmbesClean_indexAcquisitions')) {
									$result .= $this->proxy->pmbesClean_indexAcquisitions();
									$percent += $p_value;
									$this->update_progression($percent);	
								} else {
									$result .= "<p>".sprintf($msg["planificateur_function_rights"],"indexAcquisitions","pmbesClean",$PMBusername)."</p>";
								}
							} else {
								$result .= "<p>".$this->msg["clean_acquisition"]."</p>";
							}
							$result .= "</td></tr>";
							break;
						case GEN_SIGNATURE_NOTICE:
							$result .= "<tr><th>".htmlentities($msg["gen_signature_notice"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_genSignatureNotice')) {
								$result .= $this->proxy->pmbesClean_genSignatureNotice();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"genSignatureNotice","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case GEN_PHONETIQUE:
							$result .= "<tr><th>".htmlentities($msg["gen_phonetique"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_genPhonetique')) {
								$result .= $this->proxy->pmbesClean_genPhonetique();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"genPhonetique","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case NETTOYAGE_CLEAN_TAGS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_clean_tags"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_nettoyageCleanTags')) {
								$result .= $this->proxy->pmbesClean_nettoyageCleanTags();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"nettoyageCleanTags","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_CATEGORIES_PATH:
							$result .= "<tr><th>".htmlentities($msg["clean_categories_path"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanCategoriesPath')) {
								$result .= $this->proxy->pmbesClean_cleanCategoriesPath();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanCategoriesPath","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case GEN_DATE_PUBLICATION_ARTICLE:
							$result .= "<tr><th>".htmlentities($msg["gen_date_publication_article"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_genDatePublicationArticle')) {
								$result .= $this->proxy->pmbesClean_genDatePublicationArticle();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"genDatePublicationArticle","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case GEN_DATE_TRI:
							$result .= "<tr><th>".htmlentities($msg["gen_date_tri"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_genDateTri')) {
								$result .= $this->proxy->pmbesClean_genDateTri();
								$percent += $p_value;
								$this->update_progression($percent);	
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"genDateTri","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_DOCNUM:
							$result .= "<tr><th>".htmlentities($msg["docnum_reindexer"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if ($pmb_indexation_docnum) {
								if (method_exists($this->proxy, 'pmbesClean_indexDocnum')) {
									$result .= $this->proxy->pmbesClean_indexDocnum();
									$percent += $p_value;
									$this->update_progression($percent);	
								} else {
									$result .= "<p>".sprintf($msg["planificateur_function_rights"],"indexDocnum","pmbesClean",$PMBusername)."</p>";
								}
							} else {
								$result .= "<p>".$this->msg["clean_indexation_docnum"]."</p>";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_OPAC_SEARCH_CACHE:
							$result .= "<tr><th>".htmlentities($msg["cleaning_opac_search_cache"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							$query = "truncate table search_cache";
							if(pmb_mysql_query($query,$dbh)){
								$query = "optimize table search_cache";
								if(pmb_mysql_query($query,$dbh)){
									$result.= "OK";
								}else{
									$result.= "OK";
								}
								$percent += $p_value;
								$this->update_progression($percent);
							}else{
								$result.= "KO";
							}
							$result .= "</td></tr>";
							break;
						case CLEAN_CACHE_AMENDE:
							$result .= "<tr><th>".htmlentities($msg["cleaning_cache_amende"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							$query = "truncate table cache_amendes";
							if(pmb_mysql_query($query,$dbh)){
								$query = "optimize table cache_amendes";
								if(pmb_mysql_query($query,$dbh)){
									$result.= "OK";
								}else{
									$result.= "OK";
								}
								$percent += $p_value;
								$this->update_progression($percent);
							}else{
								$result.= "KO";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_RDFSTORE:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_rdfstore_reindexation"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanRdfStore')) {
								$result .= $this->proxy->pmbesClean_cleanRdfStore();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanRdfStore","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_SYNCHRORDFSTORE:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_synchrordfstore_reindexation"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanSynchroRdfStore')) {
								$result .= $this->proxy->pmbesClean_cleanSynchroRdfStore();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanSynchroRdfStore","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_FAQ:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_reindex_faq"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanFAQ')) {
								$result .= $this->proxy->pmbesClean_cleanFAQ();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanFAQ","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_CMS:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_reindex_cms"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanCMS')) {
								$result .= $this->proxy->pmbesClean_cleanCMS();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanCMS","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case INDEX_CONCEPT:
							$result .= "<tr><th>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_cleanConcept')) {
								$result .= $this->proxy->pmbesClean_cleanConcept();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"cleanConcept","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
						case HASH_EMPR_PASSWORD:
							$result .= "<tr><th>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</th></tr>";
							$result .= "<tr><td>";
							if (method_exists($this->proxy, 'pmbesClean_hashEmprPassword')) {
								$result .= $this->proxy->pmbesClean_hashEmprPassword();
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$result .= "<p>".sprintf($msg["planificateur_function_rights"],"hashEmprPassword","pmbesClean",$PMBusername)."</p>";
							}
							$result .= "</td></tr>";
							break;
					}
				}
			}
			$this->report[] = $result;
		} else {
			$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
		}
		
	}
	
	function traite_commande($cmd,$message) {
		
		switch ($cmd) {
			case RESUME :
				$this->send_command(WAITING);
				break;
			case SUSPEND :
				$this->suspend_clean();
				break;
			case STOP :
				$this->finalize();
				die();
				break;
			case FAIL :
				$this->finalize();
				die();
				break;
		}
	}
		    
	function make_serialized_task_params() {
    	global $index_global, $index_notices, $clean_authors, $clean_editeurs;
    	global $clean_collections, $clean_subcollections, $clean_categories;
    	global $clean_series, $clean_titres_uniformes, $clean_indexint;
    	global $clean_relations, $clean_notices, $index_acquisitions;
    	global $gen_signature_notice, $gen_phonetique, $nettoyage_clean_tags, $clean_categories_path;
    	global $gen_date_publication_article, $gen_date_tri, $reindex_docnum;
    	global $clean_opac_search_cache, $clean_cache_amende;
    	global $index_rdfstore, $index_synchrordfstore;
    	global $index_faq, $index_cms, $index_concept, $hash_empr_password;

		$t = parent::make_serialized_task_params();
		
		$t_clean = array();
		//Ordre d'exécution
		if($clean_notices) $t_clean["clean_notices"] = $clean_notices;
		if($clean_subcollections) $t_clean["clean_subcollections"] = $clean_subcollections;
		if($clean_collections) $t_clean["clean_collections"] = $clean_collections;
		if($clean_editeurs) $t_clean["clean_editeurs"] = $clean_editeurs;
		if($clean_authors) $t_clean["clean_authors"] = $clean_authors;
		if($clean_categories) $t_clean["clean_categories"] = $clean_categories;
		if($clean_series) $t_clean["clean_series"] = $clean_series;
		if($clean_titres_uniformes) $t_clean["clean_titres_uniformes"] = $clean_titres_uniformes;
		if($clean_indexint) $t_clean["clean_indexint"] = $clean_indexint;
		if($clean_relations) $t_clean["clean_relations"] = $clean_relations;
		if($index_acquisitions) $t_clean["index_acquisitions"] = $index_acquisitions;
		if($gen_signature_notice) $t_clean["gen_signature_notice"] = $gen_signature_notice;
		if($gen_phonetique) $t_clean["gen_phonetique"] = $gen_phonetique;
		if($nettoyage_clean_tags) $t_clean["nettoyage_clean_tags"] = $nettoyage_clean_tags;
		if($clean_categories_path) $t_clean["clean_categories_path"] = $clean_categories_path;
		if($gen_date_publication_article) $t_clean["gen_date_publication_article"] = $gen_date_publication_article;
		if($gen_date_tri) $t_clean["gen_date_tri"] = $gen_date_tri;
		if($index_notices) $t_clean["index_notices"] = $index_notices;
		if ($index_global) $t_clean["index_global"] = $index_global;
		if($reindex_docnum) $t_clean["reindex_docnum"] = $reindex_docnum;
		if($clean_opac_search_cache) $t_clean["clean_opac_search_cache"] = $clean_opac_search_cache;
		if($clean_cache_amende) $t_clean["clean_cache_amende"] = $clean_cache_amende;
		if($index_rdfstore) $t_clean["index_rdfstore"] = $index_rdfstore;
		if($index_synchrordfstore) $t_clean["index_synchrordfstore"] = $index_synchrordfstore;
		if($index_faq) $t_clean["index_faq"] = $index_faq;
		if($index_cms) $t_clean["index_cms"] = $index_cms;
		if($index_concept) $t_clean["index_concept"] = $index_concept;
		if($hash_empr_password) $t_clean["hash_empr_password"] = $hash_empr_password;
		
		$t["clean"] = $t_clean;

    	return serialize($t);
	}

	function unserialize_task_params() {
    	$params = $this->get_task_params();
		
		return $params;
    }
    
	function suspend_clean() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->listen_commande(array(&$this,"traite_commande"));
		}
	}
}


