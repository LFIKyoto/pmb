<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_delete.inc.php,v 1.18 2015-06-10 07:14:04 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/category.class.php");
require_once ("$class_path/noeuds.class.php");
require_once ($class_path."/thesaurus.class.php");
require_once ($class_path."/synchro_rdf.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/index_concept.class.php");


if (noeuds::hasChild($id)) {

	error_message($msg[321], $msg[322], 1, "./autorites.php?categ=categories&id=$id&sub=categ_form&parent=$parent");
	exit();
	
} elseif (count(noeuds::listTargetsExceptOrphans($id))){
	
	error_message($msg[321], $msg[thes_suppr_impossible_renvoi_voir], 1, "./autorites.php?categ=categories&id=$id&sub=categ_form&parent=$parent");
	exit();

} elseif (noeuds::isProtected($id)) {
	
	error_message($msg[321], $msg[thes_suppr_impossible_protege], 1, "./autorites.php?categ=categories&id=$id&sub=categ_form&parent=$parent");
	exit();
	
} elseif (count(vedette_composee::get_vedettes_built_with_element($id, "category"))) {
	// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
	error_message($msg[321], $msg["vedette_dont_del_autority"], 1);
	exit();
	
} elseif (noeuds::isUsedInNotices($id)) {
	if ($forcage == 1) {
		$tab= unserialize( urldecode($ret_url) );
		foreach($tab->GET as $key => $val){
			$GLOBALS[$key] = $val;
		}	
		foreach($tab->POST as $key => $val){
			$GLOBALS[$key] = $val;
		}
		//On met à jour le graphe rdf avant de supprimer
		if($pmb_synchro_rdf){
			$arrayIdImpactes=array();
			$synchro_rdf=new synchro_rdf();
			$noeud=new noeuds($id);
			$thes=new thesaurus($noeud->num_thesaurus);
			//parent
			if($noeud->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$noeud->num_parent;
			}
			//renvoi_voir
			if($noeud->num_renvoi_voir){
				$arrayIdImpactes[]=$noeud->num_renvoi_voir;
			}
			//on supprime le rdf
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
				}
			}
			$synchro_rdf->delConcept($id);
		}
		// nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_CATEGORY);
		$index_concept->delete();
		
		$requete="DELETE FROM notices_categories WHERE num_noeud=".$id;
		pmb_mysql_query($requete, $dbh);
		noeuds::delete($id);
		//On remet à jour les noeuds impactes
		if($pmb_synchro_rdf){
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
		}
	}  else {
		$tab = new stdClass();			
		$requete="SELECT notcateg_notice FROM notices_categories WHERE num_noeud=".$id." ORDER BY ordre_categorie";
		$result_cat=pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($result_cat)) {
			//affichage de l'erreur, en passant tous les param postés (serialise) pour l'éventuel forcage 	
			$tab->POST = $_POST;
			$tab->GET = $_GET;
			$ret_url= urlencode(serialize($tab));
			require_once("$class_path/mono_display.class.php");
			require_once("$class_path/serial_display.class.php");
		   
			print "
				<br /><div class='erreur'>$msg[540]</div>
				<script type='text/javascript' src='./javascript/tablist.js'></script>
				<script>
					function confirm_delete() {
						phrase = \"{$msg[autorite_confirm_suppr_categ]}\";
						result = confirm(phrase);
						if(result) form.submit();
					}	
				</script>
				<div class='row'>
					<div class='colonne10'>
						<img src='./images/error.gif' align='left'>
					</div>
					<div class='colonne80'>
						<strong>".$msg["autorite_suppr_categ_titre"]."</strong>
					</div>
				</div>
				<div class='row'>
					<form class='form-$current_module' name='dummy'  method='post' action='./autorites.php?categ=categories&sub=delete&parent=$parent&id=$id'>					
						<input type='hidden' name='forcage' value='1'>
						<input type='hidden' name='ret_url' value='$ret_url'>
						<input type='button' name='ok' class='bouton' value=' $msg[89] ' onClick='history.go(-1);'>
						<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["autorite_suppr_categ_forcage_button"], ENT_QUOTES,$charset)." '  onClick=\"confirm_delete();return false;\">
					</form>				
				</div>";
			while (($r_cat=pmb_mysql_fetch_object($result_cat))) {
				$requete="select signature, niveau_biblio ,notice_id from notices where notice_id=".$r_cat->notcateg_notice." limit 20";
				$result=pmb_mysql_query($requete, $dbh);	
				if (($r=pmb_mysql_fetch_object($result))) {

					if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
						// notice de monographie
						$nt = new mono_display($r->notice_id);
					} else {
						// on a affaire à un périodique
						$nt = new serial_display($r->notice_id,1);
					}
					echo "
						<div class='row'>
						$nt->result
				 	    </div>";
				}	
				echo "<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";	
			}
			exit();
		}	
	}	
//	error_message($msg[321], $msg[categ_delete_used], 1, "./autorites.php?categ=categories&id=$id&sub=categ_form&parent=$parent");
//	exit();

} elseif (count(noeuds::listTargetsOrphansOnly($id)) && !isset($force_delete_target)) {
	
	box_confirm_message($msg[321], $msg[confirm_suppr_categ_rejete], "./autorites.php?categ=categories&sub=delete&parent=$parent&id=$id&force_delete_target=1", "./autorites.php?categ=categories&id=$id&sub=categ_form&parent=$parent", $msg[40], $msg[39]);
	exit();
	
} else {
	$array_to_delete = array();
	$id_list_orphans = noeuds::listTargetsOrphansOnly($id);

	if (count($id_list_orphans)) {
		foreach ($id_list_orphans as $id_orphan) {
			// on n'efface pas les termes orphelins avec terme spécifique
			// on n'efface pas les termes orphelins utilisées en indexation
			if (!noeuds::hasChild($id_orphan) && !noeuds::isUsedInNotices($id_orphan)) {
				$array_to_delete[] = $id_orphan;
			}			
		}
	}
	$array_to_delete[] = $id;

	foreach($array_to_delete as $id_to_delete){
		//On met à jour le graphe rdf avant de supprimer
		if($pmb_synchro_rdf){
			$arrayIdImpactes=array();
			$synchro_rdf=new synchro_rdf();
			$noeud=new noeuds($id_to_delete);
			$thes=new thesaurus($noeud->num_thesaurus);
			//parent
			if($noeud->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$noeud->num_parent;
			}
			//renvoi_voir
			if($noeud->num_renvoi_voir){
				$arrayIdImpactes[]=$noeud->num_renvoi_voir;
			}
			//on supprime le rdf
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
				}
			}
			$synchro_rdf->delConcept($id_to_delete);
		}
		// nettoyage indexation concepts
		$index_concept = new index_concept($id_to_delete, TYPE_CATEGORY);
		$index_concept->delete();
		
		noeuds::delete($id_to_delete);
		//On remet à jour les noeuds impactes
		if($pmb_synchro_rdf){
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			//On met à jour le thésaurus pour les topConcepts
			$synchro_rdf->updateAuthority($noeud->num_thesaurus,'thesaurus');
		}
	}
}

include('./autorites/subjects/default.inc.php');

?>
