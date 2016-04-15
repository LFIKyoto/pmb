<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesClean.class.php,v 1.10.2.1 2015-09-30 08:40:31 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once("$include_path/mysql_connect.inc.php");

/*
 ATTENTION: Si vous modifiez de fichier vous devez probablement modifier le fichier pmbesIndex.class.php
*/
class pmbesClean extends external_services_api_class {
	var $error=false;		//Y-a-t-il eu une erreur
	var $error_message="";	//Message correspondant à l'erreur
	
	function restore_general_config() {
		
	}
	
	function form_general_config() {
		return false;
	}
	
	function save_general_config() {
		
	}
	
	function indexGlobal() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_global"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			//remise a zero de la table au début
			pmb_mysql_query("delete from notices_global_index",$dbh);
			pmb_mysql_query("delete from notices_mots_global_index",$dbh);
				
			$query = pmb_mysql_query("select notice_id from notices order by notice_id",$dbh);
			if(pmb_mysql_num_rows($query)) {
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					// Mise à jour de la table "notices_global_index"
			    	notice::majNoticesGlobalIndex($mesNotices['notice_id']);
			    	// Mise à jour de la table "notices_mots_global_index"
			    	notice::majNoticesMotsGlobalIndex($mesNotices['notice_id']);             		   	
				}
				pmb_mysql_free_result($query);
			}
			
			$not = pmb_mysql_query("SELECT count(1) FROM notices_global_index", $dbh);
			$count = pmb_mysql_result($not, 0, 0);
			$result .= $count." ".htmlentities($msg["nettoyage_res_reindex_global"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;	
	}
	
	function indexNotices() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			//NOTICES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			$query = pmb_mysql_query("SELECT notice_id FROM notices", $dbh);
			if(pmb_mysql_num_rows($query)) {		
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					notice::majNotices($row->notice_id);
				}
				pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($notices, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_notices"], ENT_QUOTES, $charset);
				
			//AUTEURS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)."</h3>";
		
			$query = pmb_mysql_query("SELECT author_id as id,concat(author_name,' ',author_rejete,' ', author_lieu, ' ',author_ville,' ',author_pays,' ',author_numero,' ',author_subdivision) as auteur from authors LIMIT $start, $lot", $dbh);
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_chars($row->auteur); 
					$req_update = "UPDATE authors ";
					$req_update .= " SET index_author=' ${ind_elt} '";
					$req_update .= " WHERE author_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM authors", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authors"], ENT_QUOTES, $charset);
					
			//EDITEURS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)."</h3>";

			$query = pmb_mysql_query("SELECT ed_id as id, ed_name as publisher, ed_ville, ed_pays from publishers", $dbh);
			if (pmb_mysql_num_rows($query)) {			
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_chars($row->publisher." ".$row->ed_ville." ".$row->ed_pays); 
					$req_update = "UPDATE publishers ";
					$req_update .= " SET index_publisher=' ${ind_elt} '";
					$req_update .= " WHERE ed_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM publishers", $dbh);
			$count = pmb_mysql_result($elts, 0, 0); 
			$result .= "".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_publishers"], ENT_QUOTES, $charset);
				
			//CATEGORIES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)."</h3>";

			$req = "select num_noeud, langue, libelle_categorie from categories";
			$query = pmb_mysql_query($req, $dbh);
			if (pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->libelle_categorie, $row->langue); 
					
					$req_update = "UPDATE categories ";
					$req_update.= "SET index_categorie=' ${ind_elt} '";
					$req_update.= "WHERE num_noeud='".$row->num_noeud."' and langue='".$row->langue."' ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			} 
			$elts = pmb_mysql_query("SELECT count(1) FROM categories", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_categories"], ENT_QUOTES, $charset);
		
			//COLLECTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)."</h3>";
		
			$query = pmb_mysql_query("SELECT collection_id as id, collection_name as collection from collections", $dbh);
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->collection); 
					
					$req_update = "UPDATE collections ";
					$req_update .= " SET index_coll=' ${ind_elt} '";
					$req_update .= " WHERE collection_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0); 
			$result .= "".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_collections"], ENT_QUOTES, $charset);
					
			//SOUSCOLLECTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT sub_coll_id as id, sub_coll_name as sub_collection from sub_collections", $dbh);
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->sub_collection); 
					
					$req_update = "UPDATE sub_collections ";
					$req_update .= " SET index_sub_coll=' ${ind_elt} '";
					$req_update .= " WHERE sub_coll_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM sub_collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sub_collections"], ENT_QUOTES, $charset);
			
			//SERIES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT serie_id as id, serie_name from series LIMIT $start, $lot", $dbh);
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->serie_name); 
					
					$req_update = "UPDATE series ";
					$req_update .= " SET serie_index=' ${ind_elt} '";
					$req_update .= " WHERE serie_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM series", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_series"], ENT_QUOTES, $charset);

			//DEWEY
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT indexint_id as id, concat(indexint_name,' ',indexint_comment) as index_indexint from indexint LIMIT $start, $lot", $dbh);
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->index_indexint); 
					
					$req_update = "UPDATE indexint ";
					$req_update .= " SET index_indexint=' ${ind_elt} '";
					$req_update .= " WHERE indexint_id=$row->id ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			} 
			$elts = pmb_mysql_query("SELECT count(1) FROM indexint", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_indexint"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	function cleanAuthors() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			//1er passage
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_auteurs"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			$query = pmb_mysql_query("delete authors from authors left join responsability on responsability_author=author_id where responsability_author is null and author_see=0 ",$dbh);
			$affected += pmb_mysql_affected_rows();
			
			//2eme passage
			$result .= "<h3>".htmlentities($msg["nettoyage_renvoi_auteurs"], ENT_QUOTES, $charset)."</h3>";
	
			$query = pmb_mysql_query("update authors A1 left join authors A2 on A1.author_see=A2.author_id set A1.author_see=0 where A2.author_id is null",$dbh);
			$affected += pmb_mysql_affected_rows();
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_auteurs"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE authors');
			
			$affected = 0;
			//3eme passage
			$result .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 1</h3>";
	
			$query = pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null ",$dbh);
			$affected += pmb_mysql_affected_rows();
			
			//4eme passage
			$notices = pmb_mysql_query("SELECT count(1) FROM responsability where responsability_author<>0 ", $dbh);
			$count = pmb_mysql_result($notices, 0, 0) ;
	
			$result .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 2</h3>";
	
			$query = pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null ",$dbh);
			$affected += pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_responsabilites"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE authors',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanPublishers() {
		global $msg,$dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_editeurs"], ENT_QUOTES, $charset)."</h3>";
			
			// creation table tempo contenant les id des publishers utilisés
			$query = pmb_mysql_query("create temporary table tmppub as select distinct ed1_id as edid from notices  where ed1_id!=0 union select distinct ed2_id as edid from notices where ed2_id!=0",$dbh);
			$query = pmb_mysql_query("alter table tmppub add index (edid)",$dbh);
	
			// supp des pub non utilisés dans les collections, sous-collections et notices !
			$query = pmb_mysql_query("delete publishers from publishers left join tmppub on ed_id=edid left join sub_collections on ed_id=sub_coll_parent left join collections on ed_id=collection_parent where sub_coll_parent is null and collection_parent is null and edid is null ",$dbh);
			$affected = pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_editeurs"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE publishers',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
			return $result;
	}
	
	function cleanCollections() {
		global $msg,$dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_collections"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("delete collections from collections left join notices on collection_id=coll_id left join sub_collections on sub_coll_parent=collection_id where coll_id is null and sub_coll_parent is null ",$dbh);
			$affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_collections"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE collections',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanSubcollections() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_subcollections"], ENT_QUOTES, $charset)."</h3>";
					
			$query = pmb_mysql_query("delete sub_collections from sub_collections left join notices on sub_coll_id=subcoll_id where subcoll_id is null ",$dbh);
			$affected = pmb_mysql_affected_rows();
						
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_subcollections"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE sub_collections',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanCategories() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if ($deleted=="") $deleted=0 ;

		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_categories"], ENT_QUOTES, $charset)."</h3>";
			
			$list_thesaurus = thesaurus::getThesaurusList();
			foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$thes = new thesaurus($id_thesaurus);
				$noeud_rac =  $thes->num_noeud_racine;
				$r = noeuds::listChilds($noeud_rac, 0);
				while($row = pmb_mysql_fetch_object($r)){
					noeuds::process_categ($row->id_noeud);
				}
			}	
		
			//TODO non repris >> Utilité ???
			//	$delete = pmb_mysql_query("delete from categories where categ_libelle='#deleted#'");

			$result .= $deleted." ".htmlentities($msg["nettoyage_res_suppr_categories"], ENT_QUOTES, $charset);

			$optn = noeuds::optimize();
			$optc = categories::optimize();
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanSeries() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_series"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("delete series from series left join notices on tparent_id=serie_id where tparent_id is null",$dbh);
			$affected = pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("update notices left join series on tparent_id=serie_id set tparent_id=0 where serie_id is null",$dbh);
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_series"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE series',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanTitresUniformes() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_titres_uniformes"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT tu_id from titres_uniformes left join notices_titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu is null",$dbh);
			$affected=0;
			if($affected = pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$tu = new titre_uniforme($ligne->tu_id);
					$tu->delete();
				}
			}

			//Nettoyage des informations d'autorités pour les sous collections
			titre_uniforme::delete_autority_sources();

			$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join titres_uniformes on ntu_num_tu=tu_id where tu_id is null",$dbh);
			$affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_titres_uniformes"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE titres_uniformes',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanIndexint() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_indexint"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT indexint_id from indexint left join notices on indexint=indexint_id where notice_id is null",$dbh);
			$affected=0;
			if($affected = pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$tu = new indexint($ligne->indexint_id);
					$tu->delete();
				}
			}
			$query = pmb_mysql_query("update notices left join indexint ON indexint=indexint_id SET indexint=0 WHERE indexint_id is null",$dbh);
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_indexint"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE indexint',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanRelations() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			//relation 1
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_ban"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			$query = pmb_mysql_query("DELETE bannettes FROM bannettes LEFT JOIN empr ON proprio_bannette = id_empr WHERE id_empr IS NULL AND proprio_bannette !=0");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE equations FROM equations LEFT JOIN empr ON proprio_equation = id_empr WHERE id_empr IS NULL AND proprio_equation !=0 ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN bannettes ON num_bannette = id_bannette WHERE id_bannette IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN equations on num_equation=id_equation WHERE id_equation is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN empr on num_empr=id_empr WHERE id_empr is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN bannettes ON num_bannette=id_bannette WHERE id_bannette IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='NOTI') left join notices on object_id=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join notices on num_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join bannettes on num_bannette=id_bannette where id_bannette is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE avis FROM avis LEFT JOIN notices ON num_notice=notice_id WHERE notice_id IS NULL ");
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_ban"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE bannette_contenu');
	
			//relation 2
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_cat"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			$query = pmb_mysql_query("delete from notices_custom_values where notices_custom_champ not in (select idchamp from notices_custom)");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete from expl_custom_values where expl_custom_champ not in (select idchamp from expl_custom)");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE empr_custom_values FROM empr_custom_values LEFT JOIN empr ON id_empr=empr_custom_origine WHERE id_empr IS NULL ");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete from empr_custom_values where empr_custom_champ not in (select idchamp from empr_custom)");
			$affected = pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_cat"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices_categories');
	
			//relation 3
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("DELETE notices_custom_values FROM notices_custom_values LEFT JOIN notices ON notice_id=notices_custom_origine WHERE notice_id IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices from notices left join bulletins on num_notice=notice_id where num_notice is null and niveau_hierar='2' and niveau_biblio='b' ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join notices on ntu_num_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_categories from notices_categories left join notices on notcateg_notice=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null");
			$affected = pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices_categories');
	
			//relation 4
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_cat2"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("delete notices_global_index from notices_global_index left join notices on num_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_mots_global_index from notices_mots_global_index left join notices on id_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete audit from audit left join notices on object_id=notice_id where notice_id is null and type_obj=1");
			$affected += pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_cat2"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices_categories');
	
			//relation 5
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan2"], ENT_QUOTES, $charset)."</h3>";
	
			$affected = 0;
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='EXPL') left join exemplaires on object_id=expl_id where expl_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete explnum from explnum left join notices on notice_id=explnum_notice where notice_id is null and explnum_bulletin=0");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete explnum from explnum left join bulletins on bulletin_id=explnum_bulletin where bulletin_id is null and explnum_notice=0 ");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete acces_res_1 from acces_res_1 left join notices on res_num=notice_id where notice_id is null ");
			if($query) $affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete acces_res_2 from acces_res_2 left join notices on res_num=notice_id where notice_id is null ");
			if($query) $affected = pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan2"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices_categories');
	
			//relation 6
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_dep1"], ENT_QUOTES, $charset)."</h3>";
	
			$affected = 0;
			$query = pmb_mysql_query("delete analysis from analysis left join notices on analysis_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices from notices left join analysis on analysis_notice=notice_id where analysis_notice is null and niveau_hierar='2' and niveau_biblio='a'");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete analysis from analysis left join bulletins on analysis_bulletin=bulletin_id where bulletin_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bulletins from bulletins left join notices on bulletin_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_relations from notices_relations left join notices on num_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_relations from notices_relations left join notices on linked_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_dep1"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices');
	
			//relation 7
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan3"], ENT_QUOTES, $charset)."</h3>";
	
			$affected = 0;
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='BULL') left join bulletins on object_id=bulletin_id where bulletin_id is null");
			$affected = pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete notices_langues from notices_langues left join notices on num_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete abo_liste_lecture from abo_liste_lecture left join empr on num_empr=id_empr where id_empr is null");
			$affected_ll += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete abo_liste_lecture from abo_liste_lecture left join opac_liste_lecture on num_liste=id_liste where id_liste is null");
			$affected_ll += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete opac_liste_lecture from opac_liste_lecture left join empr on num_empr=id_empr where id_empr is null");
			$affected_ll += pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan3"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE caddie_content');
			
			//relation 8
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_blob"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;			
			for($i=1;$i<=2;$i++){
				if($i==1){
					$table='logopac';
				}else{
					$table='statopac';
				}
				$query = "SELECT column_type FROM information_schema.columns WHERE table_schema = '".DATA_BASE."' AND table_name = '".$table."' AND column_name = 'empr_expl'";
				$res = pmb_mysql_query($query);
				$row = pmb_mysql_fetch_object($res);
			
				if ($row->column_type == 'blob') {
					$query = pmb_mysql_query("ALTER TABLE ".$table." CHANGE empr_expl empr_expl MEDIUMBLOB NOT NULL");
					$affected += pmb_mysql_affected_rows();
				}
			}
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_blob"], ENT_QUOTES, $charset);
			
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanNotices() {
		global $msg,$dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {	
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_notices"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			// La routine ne nettoie pour l'instant que les monographies
			$query = pmb_mysql_query("delete notices  
				FROM notices left join exemplaires on expl_notice=notice_id  
					left join explnum on explnum_notice=notice_id 
					left join notices_relations NRN on NRN.num_notice=notice_id  
					left join notices_relations NRL on NRL.linked_notice=notice_id 
				WHERE niveau_biblio='m' AND niveau_hierar='0' and explnum_notice is null and expl_notice is null and NRN.num_notice is null and NRL.linked_notice is null");
			$affected = pmb_mysql_affected_rows();
			$result .= "".$affected." ".htmlentities($msg["nettoyage_res_suppr_notices"], ENT_QUOTES, $charset)."";
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function indexAcquisitions() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			//SUGGESTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)."</h3>";
		
			$query = pmb_mysql_query("SELECT id_suggestion, titre, editeur, auteur, code, commentaires FROM suggestions", $dbh);
			if(pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE suggestions ";
					$req_update.= "SET index_suggestion = ' ".strip_empty_words($row->titre)." ".strip_empty_words($row->editeur)." ".strip_empty_words($row->auteur)." ".$row->code." ".strip_empty_words($row->commentaires)." ' ";
					$req_update.= "WHERE id_suggestion = ".$row->id_suggestion." ";
					$update = pmb_mysql_query($req_update, $dbh);
				}
				pmb_mysql_free_result($query);
			}
			$actes = pmb_mysql_query("SELECT count(1) FROM suggestions", $dbh);
			$count = pmb_mysql_result($actes, 0, 0); 
			$result .= htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sug"], ENT_QUOTES, $charset);
					
			//ENTITES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)."</h3>";

			$query = pmb_mysql_query("SELECT id_entite, raison_sociale FROM entites", $dbh);
			if(pmb_mysql_num_rows($query)) {		
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE entites ";
					$req_update.= "SET index_entite = ' ".strip_empty_words($row->raison_sociale)." ' ";
					$req_update.= "WHERE id_entite = ".$row->id_entite." ";
					$update = pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$entites = pmb_mysql_query("SELECT count(1) FROM entites", $dbh);
			$count = pmb_mysql_result($entites, 0, 0); 
			$result .= htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_ent"], ENT_QUOTES, $charset);
				
			//ACTES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT actes.id_acte, actes.numero, entites.raison_sociale, actes.commentaires, actes.reference FROM actes, entites where num_fournisseur=id_entite LIMIT ".$start.", ".$lot." ");
			if(pmb_mysql_num_rows($query)) {		
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE actes ";
					$req_update.= "SET index_acte = ' ".$row->numero." ".strip_empty_words($row->raison_sociale)." ".strip_empty_words($row->commentaires)." ".strip_empty_words($row->reference)." ' ";
					$req_update.= "WHERE id_acte = ".$row->id_acte." ";
					$update = pmb_mysql_query($req_update, $dbh);
	
					//index lignes_actes
					$query_2 = pmb_mysql_query("SELECT id_ligne, code, libelle FROM lignes_actes where num_acte = '".$row->id_acte."' ");
					if (pmb_mysql_num_rows($query_2)){
						while ($row_2 = pmb_mysql_fetch_object($query_2)) {
							$req_update_2 = "UPDATE lignes_actes ";
							$req_update_2.= "SET index_ligne = ' ".strip_empty_words($row_2->libelle)." ' ";
							$req_update_2.= "WHERE id_ligne = ".$row_2->id_ligne." ";
							$update_2 = pmb_mysql_query($req_update_2, $dbh);
						}
						pmb_mysql_free_result($query_2);
					}			
				}	
				pmb_mysql_free_result($query);
			}
			$actes = pmb_mysql_query("SELECT count(1) FROM actes", $dbh);
			$count = pmb_mysql_result($actes, 0, 0);
			$result .= htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_act"], ENT_QUOTES, $charset);
					
			//FINI
			$result .= htmlentities($msg["nettoyage_reindex_acq_fini"],ENT_QUOTES,$charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
	}
	
	function genSignatureNotice() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["gen_signature_notice"], ENT_QUOTES, $charset)."</h3>";
			
			$sign= new notice_doublon();
							
			$query = pmb_mysql_query("SELECT notice_id FROM notices",$dbh);
			if(pmb_mysql_num_rows($query)) {					
			   	while ($row = pmb_mysql_fetch_row($query) )  { 		
				   		$val= $sign->gen_signature($row[0]);
					pmb_mysql_query("update notices set signature='$val' where notice_id=".$row[0], $dbh);		
			   	}
		   		pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($notices, 0, 0);
			$result .= $count." ".htmlentities($msg["gen_signature_notice_status_end"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	function genPhonetique() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["gen_phonetique"], ENT_QUOTES, $charset)."</h3>";

			$notices = pmb_mysql_query("SELECT count(1) FROM words", $dbh);
			$count = pmb_mysql_result($notices, 0, 0);
			if ($count) {
				while($row = pmb_mysql_fetch_object($query)){
					$dmeta = new DoubleMetaPhone($row->word);
					$stemming = new stemming($row->word);
					$element_to_update = "";
					if($dmeta->primary || $dmeta->secondary){
						$element_to_update.="
						double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
					}
					if($element_to_update) $element_to_update.=",";
					$element_to_update.="stem = '".$stemming->stem."'";
					
					if ($element_to_update){
						pmb_mysql_query("update words set ".$element_to_update." where id_word = '".$row->id_word."'",$dbh);
					}
				}
			}
			$result .= $count." ".htmlentities($msg["gen_phonetique_end"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE words',$dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	function nettoyageCleanTags() {
		global $msg, $dbh, $charset, $PMBusername;
			
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_tags"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT notice_id FROM notices", $dbh);
			if(pmb_mysql_num_rows($query)) {
			   	while ($row = pmb_mysql_fetch_row($query) )  { 		
					notice::majNotices_clean_tags($row[0]);
			   	}
			   	pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($notices, 0, 0); 
			$result .= $count." ".htmlentities($msg["nettoyage_clean_tags_status_end"], ENT_QUOTES, $charset);
			$opt = pmb_mysql_query('OPTIMIZE TABLE notices', $dbh);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	function cleanCategoriesPath() {
		global $msg,$charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			// Pour tous les thésaurus, on parcours les childs
			$list_thesaurus = thesaurus::getThesaurusList();
			
			foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$thes = new thesaurus($id_thesaurus);
				$noeud_rac =  $thes->num_noeud_racine;
				$r = noeuds::listChilds($noeud_rac, 0);
				while(($row = pmb_mysql_fetch_object($r))){		
					noeuds::process_categ_path($row->id_noeud);
				}
			}	
			if($thesaurus_auto_postage_search){
				categories::process_categ_index();
			}
			$result .= htmlentities($msg["clean_categories_path_end"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function genDatePublicationArticle() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["gen_date_publication_article"], ENT_QUOTES, $charset)."</h3>";
			
			$req="select date_date,analysis_notice from analysis,bulletins where analysis_bulletin=bulletin_id";	
			$res=pmb_mysql_query($req,$dbh);	
			if(pmb_mysql_num_rows($res))while (($row = pmb_mysql_fetch_object($res))) {
				$year=substr($row->date_date,0,4);
				if($year) {
					$req="UPDATE notices SET year='$year' where notice_id=".$row->analysis_notice;
					pmb_mysql_query($req,$dbh);
				}		
			} 
			$result .= htmlentities($msg["gen_date_publication_article_end"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function genDateTri() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["gen_date_tri_msg"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("select notice_id, year, niveau_biblio, niveau_hierar from notices order by notice_id",$dbh);
			if(pmb_mysql_num_rows($query)) {		
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					switch($mesNotices['niveau_biblio'].$mesNotices['niveau_hierar']){
						case 'a2': 
							//Si c'est un article, on récupère la date du bulletin associé
							$reqAnneeArticle = "SELECT date_date FROM bulletins, analysis WHERE analysis_bulletin=bulletin_id AND analysis_notice='".$mesNotices['notice_id']."'";
							$queryArt=pmb_mysql_query($reqAnneeArticle,$dbh);
							
							if(!pmb_mysql_num_rows($queryArt)) $dateArt = "";
							else $dateArt=pmb_mysql_result($queryArt,0,0);
										
							if($dateArt == '0000-00-00' || !isset($dateArt) || $dateArt == "") $annee_art_tmp = "";
								else $annee_art_tmp = substr($dateArt,0,4);
			
							//On met à jour, les notices avec la date de parution et l'année
							$reqMajArt = "UPDATE notices SET date_parution='".$dateArt."', year='".$annee_art_tmp."'
										WHERE notice_id='".$mesNotices['notice_id']."'";
					        pmb_mysql_query($reqMajArt, $dbh);
						    break;	
							
						case 'b2': 
							//Si c'est une notice de bulletin, on récupère la date pour connaitre l'année						
							$reqAnneeBulletin = "SELECT date_date FROM bulletins WHERE num_notice='".$mesNotices['notice_id']."'";
							$queryAnnee=pmb_mysql_query($reqAnneeBulletin,$dbh);
							
							if(!pmb_mysql_num_rows($queryAnnee)) $dateBulletin="";
							else $dateBulletin = pmb_mysql_result($queryAnnee,0,0);
							
							if($dateBulletin == '0000-00-00' || !isset($dateBulletin) || $dateBulletin == "") $annee_tmp = "";
							else $annee_tmp = substr($dateBulletin,0,4);
							
							//On met à jour date de parution et année
							$reqMajBull = "UPDATE notices SET date_parution='".$dateBulletin."', year='".$annee_tmp."'
									WHERE notice_id='".$mesNotices['notice_id']."'";
				    		pmb_mysql_query($reqMajBull, $dbh);
							
							break;
							
						default:
							// Mise à jour du champ date_parution des notices (monographie et pério)
							$date_parution = notice::get_date_parution($mesNotices['year']);
					    	$reqMaj = "UPDATE notices SET date_parution='".$date_parution."' WHERE notice_id='".$mesNotices['notice_id']."'";
					    	pmb_mysql_query($reqMaj, $dbh);
					    	break;
					}    	           		   	
				}
				pmb_mysql_free_result($query);
			} 
			$not = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($not, 0, 0);
			$result .= $count." ".htmlentities($msg['gen_date_tri_msg'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	function indexDocnum() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["docnum_reindexation"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			$requete = "select explnum_id as id from explnum order by id";
			$res_explnum = pmb_mysql_query($requete,$dbh);
			if(pmb_mysql_num_rows($res_explnum)) {												
				while(($explnum = pmb_mysql_fetch_object($res_explnum))){
					pmb_mysql_close($dbh);
					$dbh = connection_mysql();
					$index = new indexation_docnum($explnum->id);
					$index->indexer();
				}	
			}
			$explnum = pmb_mysql_query("SELECT count(1) FROM explnum", $dbh);
			$count = pmb_mysql_result($explnum, 0, 0);
			$result .= $count." ".htmlentities($msg['docnum_reindex_expl'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanRdfStore() {
		global $msg, $dbh, $charset, $PMBusername;
		global $class_path;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_rdfstore_reindexation"], ENT_QUOTES, $charset)."</h3>";

			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE rdfstore_index",$dbh);
			
			$query = "select t.t as num_triple, s.val as subject_uri, p.val as predicat_uri, o.id as num_object, o.val as object_val, l.val as object_lang 
				from rdfstore_triple t, rdfstore_s2val s, rdfstore_id2val p, rdfstore_o2val o, rdfstore_id2val l  
				where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3 and t.s=s.id and t.p=p.id and t.o=o.id 
				order by t.t";
			$rdfStore = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($rdfStore)) {
				$op = new ontology_parser("$class_path/rdf/skos_pmb.rdf");
				$sh = new skos_handler($op);
				while(($triple = pmb_mysql_fetch_object($rdfStore))){
					$type=$sh->op->from_ns($sh->get_object_type($triple->subject_uri));
					$q_ins = "insert ignore into rdfstore_index ";
					$q_ins.= "set num_triple='".$triple->num_triple."', ";
					$q_ins.= "subject_uri='".addslashes($triple->subject_uri)."', ";
					$q_ins.= "subject_type='".addslashes($type)."', ";
					$q_ins.= "predicat_uri='".addslashes($triple->predicat_uri)."', ";
					$q_ins.= "num_object='".$triple->num_object."', ";
					$q_ins.= "object_val ='".addslashes($triple->object_val)."', ";
					$q_ins.= "object_index=' ".strip_empty_chars($triple->object_val)." ', ";
					$q_ins.= "object_lang ='".addslashes($triple->object_lang)."' ";
					
					$r_ins = pmb_mysql_query($q_ins,$dbh);
				}
			}
			$rdfStore = pmb_mysql_query("select count(1) from rdfstore_triple where o_type=2", $dbh);
			$count = pmb_mysql_result($rdfStore, 0, 0);
			$result .= $count." ".htmlentities($msg['nettoyage_rdfstore_reindex_elt'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanSynchroRdfStore() {
		global $msg,$dbh, $charset, $PMBusername;
	
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_synchrordfstore_reindexation"], ENT_QUOTES, $charset)."</h3>";
			$synchro_rdf = new synchro_rdf();
			//remise a zero des tables de synchro rdf
			$synchro_rdf->truncateStore();
			//reindex
			$query = pmb_mysql_query("select notice_id from notices order by notice_id", $dbh);
			if(pmb_mysql_num_rows($query)) {
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					$synchro_rdf->addRdf($mesNotices['notice_id'],0);
					$notice=new notice($mesNotices['notice_id']);
					$niveauB=strtolower($notice->biblio_level);
					//Si c'est un article, il faut réindexer son bulletin
					if($niveauB=='a'){
						$bulletin=analysis::getBulletinIdFromAnalysisId($mesNotices['notice_id']);
						$synchro_rdf->addRdf(0,$bulletin);
					}
				}
				pmb_mysql_free_result($query);
			}
			$compte=0;
			$q ="SELECT *
			WHERE {
			   FILTER (!regex(?p, rdf:type,'i')) .
			   ?s ?p ?o
			}";
			$r = $synchro_rdf->store->query($q);
			if (is_array($r['result']['rows'])) {
				$compte=count($r['result']['rows']);
			}	
			$result .= $compte." ".htmlentities($msg["nettoyage_synchrordfstore_reindex_total"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanFAQ() {
		global $msg,$dbh, $charset, $PMBusername;
		global $include_path;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_faq"], ENT_QUOTES, $charset)."</h3>";
				
			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE faq_questions_words_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE faq_questions_words_global_index DISABLE KEYS",$dbh);
			pmb_mysql_query("TRUNCATE faq_questions_fields_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index DISABLE KEYS",$dbh);
			
			$query = "select id_faq_question from faq_questions order by id_faq_question";
			$faq_questions = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($faq_questions)) {
				$indexation = new indexation($include_path."/indexation/faq/question.xml", "faq_questions");
				while($row = pmb_mysql_fetch_object($faq_questions)) {
					$indexation->maj($row->id_faq_question);
				}
			}
			pmb_mysql_query("ALTER TABLE faq_questions_words_global_index ENABLE KEYS",$dbh);
			pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index ENABLE KEYS",$dbh);
			$faq = pmb_mysql_query("SELECT count(1) FROM faq_questions", $dbh);
			$count = pmb_mysql_result($faq, 0, 0);
			$result .= $count." ".htmlentities($msg['nettoyage_res_reindex_faq'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanCMS() {
		global $msg, $dbh, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_cms"], ENT_QUOTES, $charset)."</h3>";

			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE cms_editorial_words_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index DISABLE KEYS",$dbh);
			pmb_mysql_query("TRUNCATE cms_editorial_fields_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index DISABLE KEYS",$dbh);
			
			$query = "select id_article from cms_articles order by id_article";
			$articles = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($articles)) {
				while($row = pmb_mysql_fetch_object($articles)) {
					$article = new cms_article($row->id_article);
					$article->maj_indexation();
				}
			}
			$query = "select id_section from cms_sections order by id_section";
			$sections = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($sections)) {
				while($row = pmb_mysql_fetch_object($sections)) {
					$section = new cms_section($row->id_section);
					$section->maj_indexation();
				}
			}
			pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index ENABLE KEYS",$dbh);
			pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index ENABLE KEYS",$dbh);
			$articles = pmb_mysql_query("SELECT count(1) FROM cms_articles", $dbh);
			$count = pmb_mysql_result($articles, 0, 0);
			$sections = pmb_mysql_query("SELECT count(1) FROM cms_sections", $dbh);
			$count+= pmb_mysql_result($sections, 0, 0);
			$result .= $count." ".htmlentities($msg['nettoyage_res_reindex_cms'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function cleanConcept() {
		global $msg, $dbh, $charset, $base_path, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)."</h3>";
		
			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE skos_words_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE skos_words_global_index DISABLE KEYS",$dbh);
			
			pmb_mysql_query("TRUNCATE skos_fields_global_index",$dbh);
			pmb_mysql_query("ALTER TABLE skos_fields_global_index DISABLE KEYS",$dbh);
			
			$onto_store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'ontology',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0
			);
			$data_store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'rdfstore',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0
			);

			$tab_namespaces=array(
					"skos"	=> "http://www.w3.org/2004/02/skos/core#",
					"dc"	=> "http://purl.org/dc/elements/1.1",
					"dct"	=> "http://purl.org/dc/terms/",
					"owl"	=> "http://www.w3.org/2002/07/owl#",
					"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
					"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
					"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
					"pmb"	=> "http://www.pmbservices.fr/ontology#"
			);
			
			$onto_index = new onto_index();
			$onto_index->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
			$onto_index->init();
			
			//la requete de base...
			$query = "select * where {
				?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
				?item rdf:type ?type .
				filter(";
			$i=0;
			foreach($onto_index->infos as $uri => $infos){
				if($i) $query.=" || ";
				$query.= "?type=<".$uri.">";
				$i++;
			}
			$query.=")
			}";
			$query.= " order by asc(?label)";
			$onto_index->handler->data_query($query);
			if($onto_index->handler->data_num_rows()) {
				$results = $onto_index->handler->data_result();
				foreach($results as $row){
					$onto_index->maj(0,$row->item);
				}
			}
			$query = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index", $dbh);
			$count = pmb_mysql_result($query, 0, 0);
			
			pmb_mysql_query("ALTER TABLE skos_words_global_index ENABLE KEYS",$dbh);
			pmb_mysql_query("ALTER TABLE skos_fields_global_index ENABLE KEYS",$dbh);
			
			$concepts = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index", $dbh);
			$count = pmb_mysql_result($concepts, 0, 0);
			$result .= $count." ".htmlentities($msg['nettoyage_res_reindex_concept'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	function hashEmprPassword() {
		global $msg,$dbh, $charset, $PMBusername;
	
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</h3>";
			$rqt = "SHOW COLUMNS FROM empr LIKE 'empr_password_is_encrypted'";
			$res = pmb_mysql_query($rqt,$dbh);
			if(pmb_mysql_num_rows($res)) {
				$empr = pmb_mysql_query("SELECT count(1) FROM empr where empr_password_is_encrypted=0", $dbh);
				$count = pmb_mysql_result($empr, 0, 0);
					
				$query = pmb_mysql_query("SELECT id_empr, empr_password, empr_login FROM empr where empr_password_is_encrypted=0");
				if(pmb_mysql_num_rows($query)) {
					$requete = "CREATE TABLE if not exists empr_passwords (
					id_empr INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					empr_password VARCHAR( 255 ) NOT NULL default '')";
					pmb_mysql_query($requete, $dbh);
					$requete = "INSERT INTO empr_passwords SELECT id_empr, empr_password FROM empr where empr_password_is_encrypted=0";
					pmb_mysql_query($requete, $dbh);
				
					while ($row = pmb_mysql_fetch_object($query) )  {
						emprunteur::update_digest($row->empr_login,$row->empr_password);
						emprunteur::hash_password($row->empr_login,$row->empr_password);
					}
				}
				$result .= $count." ".htmlentities($msg['hash_empr_password_status_end'], ENT_QUOTES, $charset);
			} else {
				$result .= htmlentities($msg['pmb_v_db_pas_a_jour'], ENT_QUOTES, $charset);
			}
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	/*Fonction copiée du fichier ./admin/netbase/category.inc.php*/
	/*Ne doit être appelable*/
//	function process_categ($id_noeud) {
//		global $dbh;
//		
//		global $deleted;
//		global $lot;
//		
//		$res = noeuds::listChilds($id_noeud, 0);
//		$total = pmb_mysql_num_rows($res);
//		if ($total) {
//			while ($row = pmb_mysql_fetch_object($res)) {
//				// la categorie a des filles qu'on va traiter
//				$this->process_categ ($row->id_noeud);
//			}
//			
//			// après ménage de ses filles, reste-t-il des filles ?
//			$total_filles = noeuds::hasChild($id_noeud);
//			
//			// categ utilisée en renvoi voir ?
//			$total_see = noeuds::isTarget($id_noeud);
//			
//			// est-elle utilisée ?
//			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
//			
//			if(!$iuse && !$total_filles && !$total_see) {
//				$deleted++ ;
//				noeuds::delete($id_noeud);
//			}
//			
//		} else { // la catégorie n'a pas de fille on va la supprimer si possible
//				// regarder si categ utilisée
//				$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
//				if(!$iuse) {
//					$deleted++ ;
//					noeuds::delete($id_noeud);
//				}
//		}
//	}
	
}

?>