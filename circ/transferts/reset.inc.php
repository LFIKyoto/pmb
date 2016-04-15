<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reset.inc.php,v 1.10 2015-04-16 11:39:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


require_once ("$include_path/expl_info.inc.php");


// Titre de la fenêtre
echo window_title($database_window_title.$msg[transferts_circ_menu_reset].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();
$form=do_cb_expl($msg[transferts_circ_menu_titre]." > ".$msg[transferts_circ_menu_reset],
					$msg[661], $msg[transferts_circ_reset_exemplaire], "./circ.php?categ=trans&sub=".$sub, 0);

if(!$f_ex_location)$f_ex_location=$deflt_docs_location;
if(!$f_ex_statut)$f_ex_statut=$deflt_docs_statut;
$form_suite="
	<div class='row'>
		<label class='f_ex_location' for='form_cb_expl'>$msg[298]</label>
	</div>
	<div class='row'>
		".gen_liste ("select distinct idlocation, location_libelle from docs_location order by location_libelle", "idlocation", "location_libelle", 'f_ex_location', "calcule_section(this);", $f_ex_location, "", "","","",0)."
	</div>
	<script type='text/javascript'>
	function calcule_section(selectBox) {
		for (i=0; i<selectBox.options.length; i++) {
			id=selectBox.options[i].value;
		    list=document.getElementById(\"docloc_section\"+id);
		    list.style.display=\"none\";
		}
	
		id=selectBox.options[selectBox.selectedIndex].value;
		list=document.getElementById(\"docloc_section\"+id);
		list.style.display=\"block\";
	}
	</script>
	<div class='row'>
		<label class='etiquette' for='f_ex_section'>$msg[295]</label>
	</div>
	<div class='row'>";
$expl = new exemplaire();
$form_suite.=$expl->do_selector();
$form_suite.="	</div>
	<div class='row'>
		<label class='etiquette' for='f_ex_statut'>$msg[297]</label>
	</div>
	<div class='row'>
		".do_selector('docs_statut', 'f_ex_statut',$f_ex_statut)."
	</div>";

$form = str_replace('<!-- !!suite!! -->',
		$form_suite,
		$form);
print $form;
//si cb
if ($form_cb_expl != "") {
	$formlocid="f_ex_section".$f_ex_location ;
	$expl_section=$$formlocid ;
	$query = "select * from exemplaires where expl_cb='".$form_cb_expl."' ";	
	$result = pmb_mysql_query($query, $dbh);
	$expl_info = pmb_mysql_fetch_object($result);
	if($expl_info->expl_id) {
		// Reset des transferts en cours
		$rqt = "UPDATE transferts,transferts_demande, exemplaires set etat_transfert=1, etat_demande=7							
				WHERE id_transfert=num_transfert and num_expl=expl_id  and etat_transfert=0 AND expl_cb='".$form_cb_expl."' " ;
		pmb_mysql_query( $rqt );
		
		//on met à jour la localisation de expl avec celle de l'utilisateur
		$rqt = "UPDATE exemplaires 
				SET expl_location=".$f_ex_location.", transfert_location_origine =".$f_ex_location.",  
				expl_statut=".$f_ex_statut.", transfert_statut_origine =".$f_ex_statut.",  
				expl_section=".$expl_section.", transfert_section_origine =".$expl_section." 
				WHERE expl_cb='".$form_cb_expl."' " ;
		pmb_mysql_query( $rqt );
				
		$rqt = "DELETE FROM transferts_source WHERE trans_source_numexpl=".$expl_info->expl_id ;
		pmb_mysql_query( $rqt );		
		$rqt = "insert transferts_source SET trans_source_numloc=".$f_ex_location." , trans_source_numexpl=".$expl_info->expl_id;
		pmb_mysql_query( $rqt );
		
		// le reset est fait
		$aff=str_replace("!!cb_expl!!", $form_cb_expl,$transferts_reset_OK);
		echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		
		$stuff = get_expl_info($expl_info->expl_id);
		$stuff = check_pret($stuff);
		print print_info($stuff,1,1,0);
	}else{
		// cb inconnu
		print "<strong>".$form_cb_expl." : ".$msg[367]."</strong>";
	}	
} 




?>