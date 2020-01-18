<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_pnb.class.php,v 1.1.2.3 2019-11-14 17:23:06 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . "/shorturl/shorturl_type.class.php");

class shorturl_type_pnb extends shorturl_type
{

    protected function returnCallback()
    {
        //Appellé que depuis l'OPAC
    }

    public function generate_hash($action, $context = array())
    {
        if (method_exists(self::class, $action)) {
            $hash = self::create_hash('pnb', $action, $context);
        }
        return $hash;
    }
}