<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: form.inc.php,v 1.35 2015-06-26 13:22:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

print "
<script type='text/javascript'>
<!--
function check_clean_form(form) {
	var flag=0;
	if(form.index_global.checked) flag += 1;
	if(form.index_notices.checked) flag += 2;
	if(form.clean_authors.checked) flag += 4;
	if(form.clean_editeurs.checked) flag += 8;
	if(form.clean_collections.checked) flag += 16;
	if(form.clean_subcollections.checked) flag += 32;
	if(form.clean_categories.checked) flag += 64;
	if(form.clean_series.checked) flag += 128;
	// if(form.clean_relations.checked) clean_relations est forcé ! 
	flag += 256;
	if(form.clean_notices.checked) flag += 512;
	if(form.index_acquisitions) {
		if(form.index_acquisitions.checked) flag += 1024;
	}
	if(form.gen_signature_notice.checked) flag += 2048;
	if(form.nettoyage_clean_tags.checked) flag += 4096;
	if(form.clean_categories_path.checked) flag += 8192;
	if(form.gen_date_publication_article.checked) flag += 16384;
	if(form.gen_date_tri.checked) flag += 32768;
	if(form.reindex_docnum) {
		if(form.reindex_docnum.checked) flag += 65536;
	}
	if(form.clean_opac_search_cache) {
		if(form.clean_opac_search_cache.checked) flag += 131072;
	}
	if(form.clean_cache_amende) {
		if(form.clean_cache_amende.checked) flag += 262144;
	}
	if(form.clean_titres_uniformes.checked) flag += 524288;
	if(flag == 0) {
		alert(\"".$msg["nettoyage_alert"]."\");
		return(false);
	}
	if(form.clean_categories.checked) {
		if (confirm(\"".$msg["nettoyage_alert_categ"]."\")) return true
		else return(false);
	}
	if(form.clean_notices.checked) {
		if (confirm(\"".$msg["nettoyage_alert_expl"]."\")) return true
		else return(false);
	}

	return true;
}
-->
</script>
<form class='form-$current_module' name='form_netbase' action='./clean.php' method='post'>
<h3>".htmlentities($msg["nettoyage_operations"], ENT_QUOTES, $charset)."</h3>
<!--	Contenu du form	-->
<div class='form-contenu'>
	<div class='row'>
		<input type='checkbox' value='1' name='index_global'>&nbsp;<label class='etiquette' >".htmlentities($msg["nettoyage_index_global"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='2' name='index_notices'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_index_notices"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='4' name='clean_authors'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_authors"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='8' name='clean_editeurs'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_editeurs"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='16' name='clean_collections'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_collections"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='32' name='clean_subcollections'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_subcollections"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='64' name='clean_categories'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_categories"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='128' name='clean_series'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_series"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='524288' name='clean_titres_uniformes'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_titres_uniformes"], ENT_QUOTES, $charset)."</label>
	</div>
	<div class='row'>
		<input type='checkbox' value='1048576' name='clean_indexint'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_indexint"], ENT_QUOTES, $charset)."</label>
	</div>
	<div class='row'>
		<input type='hidden' value='256' name='clean_relations' />
		<input type='checkbox' value='256' name='clean_relationschk' checked disabled='disabled'/>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_relations"], ENT_QUOTES, $charset)."</label>
		</div>
	<div class='row'>
		<input type='checkbox' value='512' name='clean_notices'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_expl"], ENT_QUOTES, $charset)."</label>
		</div>";		
if ($acquisition_active) {
	print "		
	<div class='row'>
		<input type='checkbox' value='1024' name='index_acquisitions'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_reindex_acq"], ENT_QUOTES, $charset)."</label>
		</div>";
}
	print "	
	<div class='row'>
		<input type='checkbox' value='2048' name='gen_signature_notice'>&nbsp;<label class='etiquette'>".htmlentities($msg["gen_signature_notice"], ENT_QUOTES, $charset)."</label>
		</div>";
	print "	
	<div class='row'>
		<input type='checkbox' value='2097152' name='gen_phonetique'>&nbsp;<label class='etiquette'>".htmlentities($msg["gen_phonetique"], ENT_QUOTES, $charset)."</label>
		</div>";
	print "	
	<div class='row'>
		<input type='checkbox' value='4096' name='nettoyage_clean_tags'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_clean_tags"], ENT_QUOTES, $charset)."</label>
		</div>";
	print "	
	<div class='row'>
		<input type='checkbox' value='8192' name='clean_categories_path'>&nbsp;<label class='etiquette'>".htmlentities($msg["clean_categories_path"], ENT_QUOTES, $charset)."</label>
		</div>";
	print "	
	<div class='row'>
		<input type='checkbox' value='16384' name='gen_date_publication_article'>&nbsp;<label class='etiquette'>".htmlentities($msg["gen_date_publication_article"], ENT_QUOTES, $charset)."</label>
		</div>";
	print "	
	<div class='row'>
		<input type='checkbox' value='32768' name='gen_date_tri'>&nbsp;<label class='etiquette'>".htmlentities($msg["gen_date_tri"], ENT_QUOTES, $charset)."</label>
		</div>";
if($pmb_indexation_docnum){
	print "	
	<div class='row'>
		<input type='checkbox' value='65536' name='reindex_docnum'>&nbsp;<label class='etiquette'>".htmlentities($msg["docnum_reindexer"], ENT_QUOTES, $charset)."</label>
	</div>";
}
	print "	
	<div class='row'>
		<input type='checkbox' value='131072' name='clean_opac_search_cache'>&nbsp;<label class='etiquette'>".htmlentities($msg["clean_opac_search_cache"], ENT_QUOTES, $charset)."</label>
	</div>";
if($pmb_gestion_financiere && $pmb_gestion_amende){
	print "	
	<div class='row'>
		<input type='checkbox' value='262144' name='clean_cache_amende'>&nbsp;<label class='etiquette'>".htmlentities($msg["clean_cache_amende"], ENT_QUOTES, $charset)."</label>
	</div>";
}	
	print "
	<div class='row'>
		<input type='checkbox' value='4194304' name='index_rdfstore'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_rdfstore_reindex"], ENT_QUOTES, $charset)."</label>
	</div>";
if($pmb_synchro_rdf){
		print "
	<div class='row'>
		<input type='checkbox' value='8388608' name='index_synchrordfstore'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_synchrordfstore_reindex"], ENT_QUOTES, $charset)."</label>
	</div>";
}
if($faq_active){
		print "
	<div class='row'>
		<input type='checkbox' value='16777216' name='index_faq'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_faq_reindex"], ENT_QUOTES, $charset)."</label>
	</div>";
}
if($cms_active){
		print "
	<div class='row'>
		<input type='checkbox' value='33554432' name='index_cms'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_cms_reindex"], ENT_QUOTES, $charset)."</label>
	</div>";
}
if($thesaurus_concepts_active==1){
		print "
	<div class='row'>
		<input type='checkbox' value='67108864' name='index_concept'>&nbsp;<label class='etiquette'>".htmlentities($msg["nettoyage_concept_reindex"], ENT_QUOTES, $charset)."</label>
	</div>";
}
print "
<div class='row'>
	<input type='checkbox' value='134217728' name='hash_empr_password'>&nbsp;<label class='etiquette'>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</label>
	</div>";
print "
	</div>
<input type='submit' value='$msg[502]' class='bouton' onClick=\"return check_clean_form(this.form)\">
</form>
";


?>
