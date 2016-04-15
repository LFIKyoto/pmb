<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: enter_sur_location.inc.php,v 1.3 2015-04-03 11:16:16 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_nb_localisations_per_line) $opac_nb_localisations_per_line=6;
print "<div id=\"location\">";
print "<h3><span>".$msg["l_browse_title"]."</span></h3>";
print "<div id='location-container'>";

if($opac_view_filter_class){
	$requete="select distinct surloc_id, surloc_libelle, surloc_pic, surloc_css_style from sur_location,docs_location  where surloc_visible_opac=1
	 and surloc_id=surloc_num and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]). ") order by surloc_libelle";
}
else
	$requete="select surloc_id, surloc_libelle, surloc_pic, surloc_css_style from sur_location where surloc_visible_opac=1 order by surloc_libelle ";
	
$resultat=pmb_mysql_query($requete);
if (pmb_mysql_num_rows($resultat)>1) {
	print "<table align='center' width='100%'>";
	$npl=0;
	while ($r=pmb_mysql_fetch_object($resultat)) {
		if ($npl==0) print "<tr>";
		if ($r->surloc_pic) $image_src = $r->surloc_pic ;
			else  $image_src = "images/bibli-small.png" ;
		print "<td align='center'>
				<a href='./index.php?lvl=section_see&surloc=".$r->surloc_id.($r->surloc_css_style?"&opac_css=".$r->surloc_css_style:"")."'><img src='$image_src' border='0' alt='".$r->surloc_libelle."' title='".$r->surloc_libelle."'/></a>
				<br /><a href='./index.php?lvl=section_see&surloc=".$r->surloc_id.($r->surloc_css_style?"&opac_css=".$r->surloc_css_style:"")."'><b>".$r->surloc_libelle."</b></a></td>";
		$npl++;
		if ($npl==$opac_nb_localisations_per_line) {
			print "</tr>";
			$npl=0;
		}
	}
	if ($npl!=0) {
		while ($npl<$opac_nb_localisations_per_line) {
			print "<td></td>";
			$npl++;
		}
		print "</tr>";
	}
	print "</table>";
} else {		
	if($opac_view_filter_class){
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 
		  and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]).")  order by location_libelle ";
	}
	else	
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 order by location_libelle ";

	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)>1) {
		print "<table align='center' width='100%'>";
		$npl=0;
		while ($r=pmb_mysql_fetch_object($resultat)) {
			if ($npl==0) print "<tr>";
			if ($r->location_pic) $image_src = $r->location_pic ;
				else  $image_src = "images/bibli-small.png" ;
			print "<td align='center'>
					<a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><img src='$image_src' border='0' alt='".$r->location_libelle."' title='".$r->location_libelle."'/></a>
					<br /><a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><b>".$r->location_libelle."</b></a></td>";
			$npl++;
			if ($npl==$opac_nb_localisations_per_line) {
				print "</tr>";
				$npl=0;
			}
		}
		if ($npl!=0) {
			while ($npl<$opac_nb_localisations_per_line) {
				print "<td></td>";
				$npl++;
			}
			print "</tr>";
		}
		print "</table>";
	} else{
		if (pmb_mysql_num_rows($resultat)) {
			$location=pmb_mysql_result($resultat,0,0);
			$requete="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=$location and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
			$resultat=pmb_mysql_query($requete);
			print "<table align='center' width='100%'>";
			$npl=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				if ($npl==0) print "<tr>";
				if ($r->section_pic) $image_src = $r->section_pic ;
					else  $image_src = "images/rayonnage-small.png" ;
				print "<td align='center'>
						<a href='./index.php?lvl=section_see&location=".$location."&id=".$r->idsection."'><img src='$image_src' border='0' alt='".$r->section_libelle."' title='".$r->section_libelle."'/></a>
						<br /><a href='./index.php?lvl=section_see&location=".$location."&id=".$r->idsection."'><b>".$r->section_libelle."</b></a></td>";
				$npl++;
				if ($npl==$opac_nb_localisations_per_line) {
					print "</tr>";
					$npl=0;
				}
			}
			if ($npl!=0) {
				while ($npl<$opac_nb_localisations_per_line) {
					print "<td></td>";
					$npl++;
				}
				print "</tr>";
			}
			print "</table>";
		}		
	}
}
print "</div>";
print "</div>";
?>