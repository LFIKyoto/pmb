<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: calendar.class.php,v 1.15 2015-04-03 11:16:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class calendar {

    function calendar() {
    }
    
    static function get_open_days($dd,$md,$yd,$df,$mf,$yf) {
    	global $pmb_utiliser_calendrier;
    	global $deflt2docs_location ;
    	global $pmb_pret_calcul_retard_date_debut_incluse;
    	$ndays=0;    	
	   	if ($pmb_utiliser_calendrier==1) {
	   		if ($pmb_pret_calcul_retard_date_debut_incluse) {
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=$deflt2docs_location and date_ouverture>='".$yd."-".$md."-".$dd."' and date_ouverture<='".$yf."-".$mf."-".$df."'";
	   		} else {
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=$deflt2docs_location and date_ouverture>'".$yd."-".$md."-".$dd."' and date_ouverture<='".$yf."-".$mf."-".$df."'";
	   		}
	   		$resultat=pmb_mysql_query($requete);	   		
	   		if (pmb_mysql_result($resultat,0,0)) {
	   			$ndays=pmb_mysql_result($resultat,0,0);
	   		} else {
	   			//on regarde si un jour d'ouverture arrive prochainement..
	   			//si oui cela signifie que l'emprunteur n'est pas en retard et attend la prochaine ouverture
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=$deflt2docs_location and date_ouverture >'".$yf."-".$mf."-".$df."' limit 0,1";
	   			$result=pmb_mysql_query($requete);
	   			if (pmb_mysql_result($result,0,0)) {
	   				$ndays = 0;
	   			} else {
	   				if ($pmb_pret_calcul_retard_date_debut_incluse)
	   					$ndays=1+(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
	   				else
	   					$ndays=(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
	   			}
	   		}
    	} else {
    		if ($pmb_pret_calcul_retard_date_debut_incluse) {
   				$ndays=1+(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
    		} else {
    			$ndays=(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
    		}
    	}
    	return $ndays;
    }
    
    static function add_days($dd,$md,$yd,$days) {
    	global $pmb_utiliser_calendrier;
    	global $deflt2docs_location;
    	
    	if ($pmb_utiliser_calendrier) {    	
 		   	$requete="select min(date_ouverture) from ouvertures where ouvert=1 and num_location=$deflt2docs_location and date_ouverture>=adddate('".$yd."-".$md."-".$dd."', interval $days day)";
   		 	$resultat=pmb_mysql_query($requete) or die ($requete." ".pmb_mysql_error());;
   		 	if (!@pmb_mysql_num_rows($resultat)) {
   		 		$requete="select adddate('".$yd."-".$md."-".$dd."', interval $days day)";
    			$resultat=pmb_mysql_query($requete) or die ($requete." ".pmb_mysql_error());;
   		 	}
   		 	if($date=pmb_mysql_result($resultat,0,0)){
   		 		return $date;
	    	} 
    	}
    	$requete="select adddate('".$yd."-".$md."-".$dd."', interval $days day)";
    	$resultat=pmb_mysql_query($requete) or die ($requete." ".pmb_mysql_error());

    	$date=pmb_mysql_result($resultat,0,0);
    	return $date;	
    }
 
 	static function maketime($mysql_date) {
 		$t_date=explode("-",$mysql_date);
 		return mktime(0,0,0,$t_date[1],$t_date[2],$t_date[0]);
 	}
}
?>