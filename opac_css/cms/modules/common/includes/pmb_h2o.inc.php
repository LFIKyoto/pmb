<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_h2o.inc.php,v 1.9 2015-04-10 09:46:40 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//on a d�ployer H2O � plus grande �chelle cot� OPAC, les sp�cificit�s PMB sont remont�es plus haut que juste le portail...
require_once($include_path."/h2o/pmb_h2o.inc.php");