<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expdocnum.inc.php,v 1.8 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $idcaddie;

require_once ("$include_path/explnum.inc.php");  

caddie_controller::proceed_expdocnum($idcaddie);
	
/*
 *     explnumid_idnotice_idbulletin_indicedocnum_nomdoc.extention

 

où : 
	explnumid serait (sur 6 chiffres) l'id du document numérique
    idnotice serait (sur 6 chiffres) l'id de la notice tel qu'il est exporté dans l'export UNIMARC TXT
    idbulletin serait (sur 6 chiffres) l'id du bulletin (et dans ce cas idnotice serait l'id de la notice mère du bulletin)

    indicedocnum serait un chiffre allant de 001 à 00n en fonction du nième document numérique attaché à cette notice

    nomdoc: nom du document tel que défini lors de la création de l'attachement

    extension: telle que donnée lors de la création si existante, sinon en fonction du mimetype

			
 * 
 */