<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex.inc.php,v 1.32 2015-04-03 11:16:18 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/notice.class.php");
require_once("$class_path/stemming.class.php");
require_once("$class_path/double_metaphone.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
$jauge_size .= "px";

// initialisation de la borne de départ
if (!isset($start)) $start=0;

$v_state=urldecode($v_state);

// on commence par :
if (!isset($index_quoi)) $index_quoi='NOTICES';

switch ($index_quoi) {
	case 'NOTICES':
	
		if (!$count) {
			$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($notices, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT notice_id FROM notices LIMIT $start, $lot");
		if(pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			$state .= "px";
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge' width='100%'>";
			print "<img src='../../images/jauge.png' width='$state' height='16px'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				notice::majNotices($row->notice_id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"NOTICES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
				// mise à jour de l'affichage de la jauge
				print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
				print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
				print "<div align='center'>100%</div>";
				$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_notices"], ENT_QUOTES, $charset);
				print "
					<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
					<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
					<input type='hidden' name='spec' value=\"$spec\">
					<input type='hidden' name='start' value='0'>
					<input type='hidden' name='count' value='0'>
					<input type='hidden' name='index_quoi' value=\"AUTEURS\">
					</form>
					<script type=\"text/javascript\"><!-- 
						setTimeout(\"document.forms['current_state'].submit()\",1000); 
						-->
					</script>";	
		}
	
		break ;
	
	case 'AUTEURS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM authors", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT author_id as id,concat(author_name,' ',author_rejete,' ', author_lieu, ' ',author_ville,' ',author_pays,' ',author_numero,' ',author_subdivision) as auteur from authors LIMIT $start, $lot", $dbh);
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_chars($row->auteur); 
				$req_update = "UPDATE authors ";
				$req_update .= " SET index_author=' ${ind_elt} '";
				$req_update .= " WHERE author_id=$row->id ";
				$update = pmb_mysql_query($req_update, $dbh);
				}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"AUTEURS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authors"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"EDITEURS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'EDITEURS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM publishers", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT ed_id as id, ed_name as publisher, ed_ville, ed_pays from publishers LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_chars($row->publisher." ".$row->ed_ville." ".$row->ed_pays); 
				$req_update = "UPDATE publishers ";
				$req_update .= " SET index_publisher=' ${ind_elt} '";
				$req_update .= " WHERE ed_id=$row->id ";
				$update = pmb_mysql_query($req_update);
				}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"EDITEURS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_publishers"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"CATEGORIES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'CATEGORIES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM categories", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)."</h2>";
		
		$req = "select num_noeud, langue, libelle_categorie from categories limit $start, $lot ";
		$query = pmb_mysql_query($req, $dbh);
		 
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while($row = pmb_mysql_fetch_object($query)) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->libelle_categorie, $row->langue); 
				
				$req_update = "UPDATE categories ";
				$req_update.= "SET index_categorie=' ${ind_elt} '";
				$req_update.= "WHERE num_noeud='".$row->num_noeud."' and langue='".$row->langue."' ";
				$update = pmb_mysql_query($req_update);
				
				
				//ajout des mots des termes dans la table words pour l autoindexation
				$t_words = array();
				$i = 0;
				$t_row = explode(' ',$ind_elt);
				if( is_array($t_row) && count($t_row) ) {
					$t_row = array_unique($t_row);
					foreach($t_row as $w) {
						if($w) {
							$t_words[$i]['word'] = $w;
							$t_words[$i]['lang'] = $row->langue;
							$i++;
						}
					}
				}
				if(count($t_words)) {
					//calcul de stem et double_metaphone
					foreach ($t_words as $i=>$w) {
						$q1 = "select id_word from words where word='".addslashes($w['word'])."' and lang='".addslashes($w['lang'])."' limit 1";
						$r1 = pmb_mysql_query($q1, $dbh);
						if(pmb_mysql_num_rows($r1)) {
							//le mot existe
							$t_words[$i]['allready_exists']=1;
						} else {
							//le mot n'existe pas
							$dmeta = new DoubleMetaPhone($w['word']);
							if($dmeta->primary || $dmeta->secondary){
								$t_words[$i]['double_metaphone'] = $dmeta->primary." ".$dmeta->secondary;
							}
							if($w['lang']=='fr_FR') {
								$stemming = new stemming($w['word']);
								$t_words[$i]['stem']=$stemming->stem;
							} else {
								$t_words[$i]['stem']='';
							}
						}
					}
					foreach($t_words as $i=>$w) {
						if (!$w['allready_exists']) {
							$q2 = "insert ignore into words (word, lang, double_metaphone, stem) values ('".$w['word']."', '".$w['lang']."', '".$w['double_metaphone']."', '".$w['stem']."') ";
							pmb_mysql_query($q2,$dbh);
						}
					}
				}
				
				
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"CATEGORIES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_categories"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"COLLECTIONS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'COLLECTIONS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT collection_id as id, collection_name as collection, collection_issn from collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->collection); 
				if($tmp = $row->collection_issn){
					$ind_elt .= " ".strip_empty_words($tmp); 
				}
				
				$req_update = "UPDATE collections ";
				$req_update .= " SET index_coll=' ${ind_elt} '";
				$req_update .= " WHERE collection_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"COLLECTIONS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_collections"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"SOUSCOLLECTIONS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'SOUSCOLLECTIONS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM sub_collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT sub_coll_id as id, sub_coll_name as sub_collection, sub_coll_issn from sub_collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->sub_collection); 
				if($tmp = $row->sub_coll_issn){
					$ind_elt .= " ".strip_empty_words($tmp); 
				}
				$req_update = "UPDATE sub_collections ";
				$req_update .= " SET index_sub_coll=' ${ind_elt} '";
				$req_update .= " WHERE sub_coll_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"SOUSCOLLECTIONS\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sub_collections"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"SERIES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'SERIES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM series", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT serie_id as id, serie_name from series LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->serie_name); 
				
				$req_update = "UPDATE series ";
				$req_update .= " SET serie_index=' ${ind_elt} '";
				$req_update .= " WHERE serie_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"SERIES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_series"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"DEWEY\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'DEWEY':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM indexint", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT indexint_id as id, concat(indexint_name,' ',indexint_comment) as index_indexint from indexint LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->index_indexint); 
				
				$req_update = "UPDATE indexint ";
				$req_update .= " SET index_indexint=' ${ind_elt} '";
				$req_update .= " WHERE indexint_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"DEWEY\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_indexint"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"FRAIS_ANNEXES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;
	
	case 'FRAIS_ANNEXES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM frais", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 align='center'>".htmlentities($msg["nettoyage_reindex_frais_annexes"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT id_frais as id, libelle from frais LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
		
			// définition de l'état de la jauge
			$state = floor($start / ($count / $jauge_size));
			
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$jauge_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$state' height='16'></td></tr></table>";
			
			// calcul pourcentage avancement
			$percent = floor(($start/$count)*100);
			
			// affichage du % d'avancement et de l'état
			print "<div align='center'>$percent%</div>";
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->libelle); 
				
				$req_update = "UPDATE frais ";
				$req_update .= " SET index_libelle=' ${ind_elt} '";
				$req_update .= " WHERE id_frais=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"FRAIS_ANNEXES\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";
		} else {
			// mise à jour de l'affichage de la jauge
			print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>";
			print "<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>";
			print "<div align='center'>100%</div>";
			$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_frais_annexes"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_frais_annexes"], ENT_QUOTES, $charset);
			print "
				<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value='0'>
				<input type='hidden' name='count' value='0'>
				<input type='hidden' name='index_quoi' value=\"FINI\">
				</form>
				<script type=\"text/javascript\"><!-- 
					setTimeout(\"document.forms['current_state'].submit()\",1000); 
					-->
				</script>";	
		}
		break ;

	case 'FINI':
		$spec = $spec - INDEX_NOTICES;
		$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["nettoyage_reindex_fini"], ENT_QUOTES, $charset);
		print "
			<form class='form-$current_module' name='process_state' action='./clean.php?spec=$spec&start=0' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
			</form>
			<script type=\"text/javascript\"><!--
				setTimeout(\"document.forms['process_state'].submit()\",1000);
				-->
			</script>";
		break ;
}
