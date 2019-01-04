<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expdocnum.inc.php,v 1.7 2017-06-29 13:08:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ("$include_path/explnum.inc.php");  

caddie_controller::proceed_expdocnum($idcaddie);
	
/*
 *     explnumid_idnotice_idbulletin_indicedocnum_nomdoc.extention

 

o� : 
	explnumid serait (sur 6 chiffres) l'id du document num�rique
    idnotice serait (sur 6 chiffres) l'id de la notice tel qu'il est export� dans l'export UNIMARC TXT
    idbulletin serait (sur 6 chiffres) l'id du bulletin (et dans ce cas idnotice serait l'id de la notice m�re du bulletin)

    indicedocnum serait un chiffre allant de 001 � 00n en fonction du ni�me document num�rique attach� � cette notice

    nomdoc: nom du document tel que d�fini lors de la cr�ation de l'attachement

    extension: telle que donn�e lors de la cr�ation si existante, sinon en fonction du mimetype

			
 * 
 */