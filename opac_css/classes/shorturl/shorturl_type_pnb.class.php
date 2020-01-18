<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_pnb.class.php,v 1.1.2.2 2019-11-12 14:45:48 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . "/shorturl/shorturl_type.class.php");
require_once ($class_path . "/pnb/pnb.class.php");


class shorturl_type_pnb extends shorturl_type
{

    protected function returnCallback()
    {
        
        $context = unserialize($this->context);
        $pnb = new pnb();
        $result = $pnb->return_book($context['empr_id'],$context['expl_id']);
        $response = encoding_normalize::json_decode($result);
        if($response['status'] === '1'){
            print ('delete from shorturls where shorturl_hash = "'.$this->hash.'"');
            pmb_mysql_query('delete from shorturls where shorturl_hash = "'.$this->hash.'"');
        }
        print $result;
    }
    

    public function generate_hash($action, $context = array())
    {
        if (method_exists($this, $action)) {
            $hash = self::create_hash('pnb', $action, $context);
        }
        return $hash;
    }
}